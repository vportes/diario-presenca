<?php
namespace App\Http\Controllers;
use App\Http\Requests\StorePresenceRequest;
use App\Models\Presence;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresenceController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->isCoordinator()) {
            $presences = Presence::with('user','justification')->latest()->paginate(25);
        } else {
            $presences = $user->presences()->with('justification')->latest()->paginate(25);
        }
        return view('presence.index', compact('presences'));
    }
// ... existing code ...
    public function history(Request $request)
    {
        $user = $request->user();
        if ($user->isCoordinator()) {
            $presences = Presence::with('user','justification')->latest()->paginate(25);
        } else {
            $presences = $user->presences()->with('justification')->latest()->paginate(25);
        }
        return view('historico.index', compact('presences'));
    }
// ... existing code ...
    public function justify(Request $request)
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
// ... existing code ...
    public function create() { return view('presence.create'); }

    public function store(StorePresenceRequest $request)
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
