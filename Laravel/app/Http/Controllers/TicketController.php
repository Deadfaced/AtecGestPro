<?php

namespace App\Http\Controllers;

use App\Ticket;
use App\User;
use App\TicketStatus;
use App\TicketPriority;
use App\TicketCategory;
use App\TicketUser;

use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('users','requester')->get();
        $users = User::all();
        return view('tickets.index', compact('tickets', 'users'));
    }

    public function create()
    {
        $statuses = TicketStatus::where('id', 1)->get();
        $priorities = TicketPriority::all();
        $categories = TicketCategory::all();
        $users = User::where('role_id', 1)->get();

        return view('tickets.create', compact('statuses', 'priorities', 'categories', 'users'));
    }
    protected function calculateDueByDate($priorityId)
    {
        switch ($priorityId) {
            case 1: // Baixa
                return now()->addWeeks(3);
            case 2: // Normal
                return now()->addWeeks(2);
            case 3: // Alta
                return now()->addWeeks(1);
            case 4: // Urgente
                return now()->addDay();
            case 5: // Crítico
                return now()->addHours(4);
            default:
                return now()->addWeeks(3);//Default fica como baixa prioridade
        }
    }
    public function store(Request $request)
    {
        $dueByDate = $this->calculateDueByDate($request->priority_id);

        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'technician_id' => 'required|exists:users,id',
            'attachment' => 'sometimes|file|max:20480',
            'priority_id' => 'required|exists:ticket_priorities,id',
            'category_id' => 'required|exists:ticket_categories,id',
        ]);
        if ($request->hasFile('attachment')) {
            $filename = $request->file('attachment')->store('attachments', 'public');
        }
        $ticket = new Ticket([
            'title' => $request->title,
            'description' => $request->description,
            'ticket_status_id' => 1,
            'ticket_priority_id' => $request->priority_id,
            'ticket_category_id' => $request->category_id,
            'dueByDate' => $dueByDate,
            'attachment' => $filename ?? null,
            'user_id' => $request->technician_id,
        ]);

        $ticket->save();

        return redirect()->route('tickets.index');
    }

    public function show(Ticket $ticket)
    {
        $ticket = Ticket::with(['users', 'requester', 'comments' => function($query) {
            $query->orderBy('created_at', 'desc');
        }, 'comments.user'])->find($ticket->id);

        $users = User::all();
        $statuses = TicketStatus::all();
        $priorities = TicketPriority::all();
        $categories = TicketCategory::all();
        $userTickets = Ticket::where('user_id', $ticket->user_id)->pluck('id');

        return view('tickets.show', compact('ticket', 'userTickets', 'users', 'statuses', 'priorities', 'categories'));
    }

    public function edit(Ticket $ticket)
    {
        $ticket = Ticket::with('users','requester')->find($ticket->id);
        $technicians = User::where('role_id', 1)->get();
        $requester = User::where('id', $ticket->user_id)->first();
        $statuses = TicketStatus::all();
        $priorities = TicketPriority::all();
        $categories = TicketCategory::all();
        $userTickets = Ticket::where('user_id', $ticket->user_id)->pluck('id');

        return view('tickets.edit', compact('ticket', 'technicians', 'requester', 'statuses', 'priorities', 'categories', 'userTickets'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        // $ticket2 = Ticket::with('users','requester','ticketPriority','ticketStatus','ticketCategory')->find($ticket->id);

        $this->validate($request, [
            'user_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'dueByDate' => 'required|date',
            'attachment' => 'sometimes|file|max:20480', // 20MB
            'ticket_status_id' => 'required|integer|exists:ticket_statuses,id',
            'ticket_priority_id' => 'required|integer|exists:ticket_priorities,id',
            'ticket_category_id' => 'required|integer|exists:ticket_categories,id',
        ]);

        if ($request->hasFile('attachment')) {
            $filename = $request->file('attachment')->store('attachments', 'public');
            $ticket->attachment = $filename;
        }

        $ticket->user_id = $request->user_id;
        $ticket->title = $request->title;
        $ticket->description = $request->description;
        $ticket->ticket_priority_id = $request->priority_id;
        $ticket->ticket_status_id = $request->status_id;
        $ticket->ticket_category_id = $request->category_id;

        $ticket->update($request->all());
        //$ticket->save();
        return redirect()->route('tickets.index')->with('success', 'Ticket atualizado com sucesso!');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('tickets.index');
    }

    public function showComment($id)
    {
        $ticket = Ticket::with(['comments' => function($query) {
            $query->orderBy('created_at', 'desc');
        },
        'comments.user'])->findOrFail($id);

        return view('tickets.show', compact('ticket'));
    }
}
