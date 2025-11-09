<?php
namespace App\Http\Controllers;

use App\Models\Observation;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Classe ObservationController
 *
 * Gerencia a criação de observações por coordenadores sobre alunos.
 *
 * @package App\Http\Controllers
 */
class ObservationController extends Controller
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
     * Armazena uma observação recém-criada no banco de dados.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $userId ID do usuário sendo observado
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, $userId): RedirectResponse
    {
        $actor = $request->user();
        if (!$actor->isCoordinator()) abort(403);
        $text = $request->input('text');
        $obs = Observation::create(['user_id' => $userId, 'created_by' => $actor->id, 'text' => $text]);
        AuditLog::create(['action'=>'observation.create','user_id'=>$actor->id,'meta'=>['observation_id'=>$obs->id,'target_user'=>$userId]]);
        return redirect()->back()->with('success','Observação registrada.');
    }
}
