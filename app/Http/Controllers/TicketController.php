<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

final class TicketController extends Controller
{
    public function index(Request $req)
    {
        $q = Ticket::query()->orderByDesc('last_activity_at');
        if ($s = $req->query('status'))
            $q->where('status', $s);
        if ($d = $req->query('device_id'))
            $q->where('device_id', $d);
        return response()->json($q->paginate(20));
    }

    public function show(string $id)
    {
        return response()->json(Ticket::findOrFail($id));
    }
}
