<?php
namespace App\Http\Controllers;
use App\Http\Requests\StoreJustificationRequest;
use App\Models\Justification;
use App\Models\Presence;
use App\Models\Attachment;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JustificationController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function create(Request $request, $presenceId = null)
    {
        $presence = null;
        if ($presenceId) $presence = Presence::find($presenceId);
        return view('justification.create', compact('presence'));
    }

    public function store(StoreJustificationRequest $request)
    {
        $user = $request->user();

        // if presence_id provided, use it; otherwise create an 'absent' presence placeholder
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

    public function review(Request $request, $id)
    {
        $user = $request->user();
        if (!$user->isCoordinator()) abort(403);
        $just = Justification::with('presence','user','attachments')->findOrFail($id);
        return view('justification.review', compact('just'));
    }

    public function decide(Request $request, $id)
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
