<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreJustificationRequest;
use App\Models\Justification;
use App\Models\Presence;
use App\Models\Attachment;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

/**
 * Classe JustificationController
 *
 * Gerencia o fluxo de envio e avaliação de justificativas.
 * Alunos podem enviar justificativas de faltas com anexos.
 * Coordenadores podem revisar e aprovar/rejeitar justificativas.
 *
 * @package App\Http\Controllers
 */
class JustificationController extends Controller
{
    /**
     * Cria uma nova instância do controller.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe o formulário para criar uma nova justificativa.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|null $presenceId ID opcional da presença a justificar
     * @return \Illuminate\View\View
     */
    public function create(Request $request, $presenceId = null): View
    {
        $presence = null;
        if ($presenceId) $presence = Presence::find($presenceId);
        return view('justification.create', compact('presence'));
    }

    /**
     * Armazena uma justificativa recém-criada no banco de dados.
     *
     * @param \App\Http\Requests\StoreJustificationRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreJustificationRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Se presence_id fornecido, usa; caso contrário cria uma presença 'absent' placeholder
        $presence = null;
        if ($request->filled('presence_id')) {
            $presence = Presence::findOrFail($request->input('presence_id'));
        } else {
            $presence = Presence::create([
                'user_id' => $user->id,
                'occurred_at' => now(),
                'status' => 'absent',
                'note' => 'Registro criado via justificativa.'
            ]);
        }

        $just = Justification::create([
            'presence_id' => $presence->id,
            'user_id' => $user->id,
            'message' => $request->input('message'),
            'status' => 'submitted'
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $f) {
                $path = $f->store('attachments');
                $attach = new Attachment([
                    'filename' => $f->getClientOriginalName(),
                    'path' => $path,
                    'mime' => $f->getClientMimeType(),
                    'size' => $f->getSize()
                ]);
                $just->attachments()->save($attach);
            }
        }

        AuditLog::create([
            'action' => 'justification.submit',
            'user_id' => $user->id,
            'meta' => ['justification_id' => $just->id, 'presence_id' => $presence->id]
        ]);

        return redirect()->route('presence.index')->with('success','Justificativa enviada.');
    }

    /**
     * Exibe a interface de revisão de justificativa para coordenadores.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id ID da justificativa
     * @return \Illuminate\View\View
     */
    public function review(Request $request, $id): View
    {
        $user = $request->user();
        if (!$user->isCoordinator()) abort(403);
        $just = Justification::with('presence','user','attachments')->findOrFail($id);
        return view('justification.review', compact('just'));
    }

    /**
     * Processa a decisão de um coordenador sobre uma justificativa.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id ID da justificativa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function decide(Request $request, $id): RedirectResponse
    {
        $user = $request->user();
        if (!$user->isCoordinator()) abort(403);

        $just = Justification::findOrFail($id);
        $action = $request->input('action'); // approve|reject|needs_more
        $note = $request->input('review_note');
        $status = $action === 'approve' ? 'approved' : ($action === 'reject' ? 'rejected' : 'needs_more');

        $just->status = $status;
        $just->reviewed_by = $user->id;
        $just->review_note = $note;
        $just->save();

        if ($status === 'approved') {
            $p = $just->presence;
            $p->status = 'present';
            $p->save();
        }

        AuditLog::create([
            'action' => 'justification.decide',
            'user_id' => $user->id,
            'meta' => ['justification_id' => $just->id, 'decision' => $status]
        ]);

        return redirect()->route('justifications.review', $just->id)->with('success','Decisão registrada.');
    }
}
