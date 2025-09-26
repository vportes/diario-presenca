<?php
namespace App\Http\Controllers;
use App\Models\Observation;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ObservationController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function store(Request $request, $userId)
    {
        $actor = $request->user();
        if (!$actor->isCoordinator()) abort(403);
        $text = $request->input('text');
        $obs = Observation::create(['user_id' => $userId, 'created_by' => $actor->id, 'text' => $text]);
        AuditLog::create(['action'=>'observation.create','user_id'=>$actor->id,'meta'=>['observation_id'=>$obs->id,'target_user'=>$userId]]);
        return redirect()->back()->with('success','Observação registrada.');
    }
}
