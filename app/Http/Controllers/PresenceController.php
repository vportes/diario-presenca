<?php
namespace App\Http\Controllers;

use App\Http\Requests\StorePresenceRequest;
use App\Models\Presence;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

/**
 * Classe PresenceController
 *
 * Gerencia os registros de presença para alunos e coordenadores.
 * Alunos podem visualizar seu próprio histórico de presença e registrar novas presenças.
 * Coordenadores podem visualizar todas as presenças.
 *
 * @package App\Http\Controllers
 */
class PresenceController extends Controller
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
     * Exibe uma listagem de presenças.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        if ($user->isCoordinator()) {
            $presences = Presence::with('user','justification')->latest()->paginate(25);
        } else {
            $presences = $user->presences()->with('justification')->latest()->paginate(25);
        }
        return view('presence.index', compact('presences'));
    }

    /**
     * Exibe o histórico de presença para o usuário autenticado.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function history(Request $request): View
    {
        $user = $request->user();
        if ($user->isCoordinator()) {
            $presences = Presence::with('user','justification')->latest()->paginate(25);
        } else {
            $presences = $user->presences()->with('justification')->latest()->paginate(25);
        }
        return view('historico.index', compact('presences'));
    }

    /**
     * Inicia o processo de justificativa para uma data específica.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function justify(Request $request): RedirectResponse
    {
        $request->validate([
            'date' => ['required','date_format:Y-m-d'],
        ]);

        $user = $request->user();
        $date = Carbon::createFromFormat('Y-m-d', $request->input('date'))->startOfDay();

        // Buscar presença do dia; se não existir, criar como ausente
        $presence = $user->presences()
            ->whereDate('occurred_at', $date->toDateString())
            ->first();

        if (!$presence) {
            $presence = Presence::create([
                'user_id'     => $user->id,
                'occurred_at' => $date->copy()->setTime(13,0), // horário padrão de exibição
                'status'      => 'absent',
                'note'        => null,
            ]);

            AuditLog::create([
                'action'  => 'presence.auto_absent_for_justification',
                'user_id' => $user->id,
                'meta'    => ['presence_id' => $presence->id, 'date' => $date->toDateString()],
            ]);
        }

        return redirect()->route('justifications.create', ['presence' => $presence->id]);
    }

    /**
     * Exibe o formulário para criar uma nova presença.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('presence.create');
    }

    /**
     * Armazena uma presença recém-criada no banco de dados.
     *
     * @param \App\Http\Requests\StorePresenceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StorePresenceRequest $request): RedirectResponse
    {
        $user = $request->user();
        $occurred = $request->input('occurred_at') ? Carbon::parse($request->input('occurred_at')) : now();
        $status = $request->input('status') ?? 'present';

        $presence = Presence::create([
            'user_id' => $user->id,
            'occurred_at' => $occurred,
            'status' => $status,
            'note' => $request->input('note')
        ]);

        AuditLog::create([
            'action' => 'presence.create',
            'user_id' => $user->id,
            'meta' => ['presence_id' => $presence->id, 'status' => $status]
        ]);

        return redirect()->route('presence.index')->with('success','Presença registrada.');
    }
}
