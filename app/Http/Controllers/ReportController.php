<?php
namespace App\Http\Controllers;

use App\Models\Presence;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Classe ReportController
 *
 * Gerencia a geração e exportação de relatórios para coordenadores.
 *
 * @package App\Http\Controllers
 */
class ReportController extends Controller
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
     * Exporta registros de presença para formato CSV.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $user = $request->user();
        if (!$user->isCoordinator()) abort(403);

        $from = $request->query('from');
        $to = $request->query('to');

        $query = Presence::with('user')->orderBy('occurred_at','desc');
        if ($from) $query->where('occurred_at','>=',$from);
        if ($to) $query->where('occurred_at','<=',$to);

        $filename = 'report_presences_'.date('Ymd_His').'.csv';

        $callback = function() use ($query) {
            $handle = fopen('php://output','w');
            fputcsv($handle, ['presence_id','user_id','name','occurred_at','status','note']);
            foreach ($query->cursor() as $p) {
                fputcsv($handle, [$p->id,$p->user_id,$p->user->name,$p->occurred_at,$p->status,$p->note]);
            }
            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, ['Content-Type'=>'text/csv']);
    }
}
