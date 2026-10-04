<?php

namespace App\Features\SupportTickets\Present\Controllers;

use App\Features\SupportTickets\Data\Models\SupportTicket;
use App\Features\SupportTickets\Present\Resources\SupportTicketResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportTicketsController
{
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $query = SupportTicket::where('user_id', $user->id)->latest();

        return paginateRes($query->paginate(10), SupportTicketResource::class);
    }

    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);
        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'subject' => $data['subject'],
            'type' => $data['type'],
            'description' => $data['description'],
            'status' => 'open',
        ]);

        return successRes(new SupportTicketResource($ticket), 201);
    }

    public function show($id)
    {
        $user = Auth::guard('sanctum')->user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);

        return successRes(new SupportTicketResource($ticket));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::guard('sanctum')->user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);
        $data = $request->validate([
            'subject' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'status' => ['sometimes', 'in:open,closed'],
        ]);
        $ticket->update($data);

        return successRes(new SupportTicketResource($ticket));
    }

    public function destroy($id)
    {
        $user = Auth::guard('sanctum')->user();
        $ticket = SupportTicket::where('user_id', $user->id)->findOrFail($id);
        $ticket->delete();

        return successRes(null, 204);
    }
}
