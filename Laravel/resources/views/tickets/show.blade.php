@extends('master.main')

@section('content')
    <div class="container  w-100 fade-in">
        @if (session('success'))
            <div class="alert alert-success" id="success-alert">
                {{ session('success') }}
            </div>
        @endif
        <div class="row my-2">
            <div class="col-md-9">
                <h2>
                    Ticket #{{ $ticket->id }} - {{ $ticket->title }}
                    <a href="{{ route('tickets.edit', $ticket->id) }}">
                        <i class="fa-solid fa-pen-to-square fa-sm" style="color: #116fdc;"></i>
                    </a>
                </h2>
                <div class="mb-3">
                    <div class="table-responsive">
                        <span class="rounded-circle bg-primary text-white"
                            style="width: 30px; height: 30px; font-size: 13px; margin-right: 5px; display: inline-block; text-align: center; line-height: 30px; z-index: 1000;">
                            <strong>{{ $requester->initials }}</strong>
                        </span>
                        <b>{{ $requester->name }}</b>
                        - {{ $ticket->created_at }}
                        @if ($ticket->ticketStatus->description === 'Aberto')
                            @if ($openedSince === 0)
                                <span class="text-danger">(Aberto hoje)</span>
                            @elseif ($openedSince === 1)
                                <span class="text-danger">(Aberto há 1 dia)</span>
                            @else
                                <span class="text-danger">(Aberto há {{ $openedSince }} dias)</span>
                            @endif
                        @endif
                    </div>
                </div>
                <div class="mb-2">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="ticket-details-tab" data-toggle="tab" href="#ticket-details"
                                role="tab" aria-controls="ticket-details" aria-selected="true">Detalhes</a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="ticket-history-tab" data-toggle="tab" href="#ticket-history"
                                role="tab" aria-controls="ticket-history" aria-selected="false">Histórico</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="ticket-details" role="tabpanel"
                        aria-labelledby="ticket-details-tab">

                        <div class="card mb-3 bg-light">
                            <h5 class="card-header">Descrição</h5>
                            <div class="card-body">
                                <div class="table-responsive">
                                    {!! $ticket->description !!}
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            @if ($ticket->attachment == 'Sem Anexo' || $ticket->attachment == null || $ticket->attachment == '')
                                <p>Não existe anexo.</p>
                            @else
                                <label for="attachment" class="form-label">Anexo:</label>
                                <a href="{{ asset('storage/' . $ticket->attachment) }}" target="_blank">Abrir anexo</a>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label for="comments" class="form-label">Insira uma nota ou comentário:</label>
                            <form action="{{ route('comments.store') }}" method="post">
                                @csrf
                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                                <div class="mb-3">
                                    <textarea class="form-control" id="new-comment" name="comment" required></textarea>
                                    <div class="d-flex justify-content-between" style="gap: 10px;">
                                        <button type="submit" class="btn btn-primary mt-2 w-70">Enviar Comentário</button>
                                        <a type="button" href="{{ url()->previous() }}"
                                            class="btn btn-secondary mt-2 w-30">Voltar</a>
                                    </div>
                                </div>
                            </form>
                            <div class="mb-3">
                                <label for="comments" class="form-label">Comentários:</label>

                                @if ($ticket->comments->isNotEmpty())
                                    @foreach ($ticket->comments as $comment)
                                        <div class="card mb-2 bg-light mx-4">
                                            <div class="card-body d-flex justify-content-between">
                                                <div>
                                                    @if (optional($comment->user)->name)
                                                        <span class="card-text font-weight-bold">
                                                            {{ optional($comment->user)->name }}:
                                                        </span>
                                                        {{ $comment->description }}
                                                    @else
                                                        <span class="text-danger font-weight-bold card-text">
                                                            Utilizador desconhecido:
                                                        </span>
                                                        {{ $comment->description }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="card-text">
                                                        {{ $comment->created_at }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p>Não existem comentários para este ticket.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="ticket-history" role="tabpanel" aria-labelledby="ticket-history-tab">
                        <div class="my-2">
                            <div class="card ">
                                <div class="card-body ">
                                    @foreach ($ticketHistories as $history)
                                        <div class="card mb-3 shadow-lg">
                                            <div class="card-header d-flex justify-content-center align-items-center">
                                                <h5>{{ $history->action->description }}</h5>
                                            </div>
                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                <p class="card-title">{{ $history->created_at }}</p>
                                            </div>
                                            <div class="card-body d-flex justify-content-center align-items-center">
                                                <p>{!! nl2br(str_replace('.', ".\n", e(strip_tags($history->ticket_info)))) !!}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-3 sideInfo">
                <div class="mb-3">
                    <label for="status" class="form-label">Estado:</label>
                    <input type="text" class="form-control" value="{{ $ticket->ticketStatus->description ?? 'N/A' }}"
                        disabled>
                </div>
                <div class="mb-3">
                    <label for="technician" class="form-label">Técnico:</label>
                    <input type="text" class="form-control" value="{{ $technician->name ?? 'N/A' }}" disabled>
                </div>
                <div class="mb-3">
                    <label for="priority" class="form-label">Prioridade:</label>
                    <input type="text" class="form-control"
                        value="{{ $ticket->ticketPriority->description ?? 'N/A' }}" disabled>
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Categoria:</label>
                    <input type="text" class="form-control"
                        value="{{ $ticket->ticketCategory->description ?? 'N/A' }}" disabled>
                </div>
                <div class="mb-3">
                    <label>Data Limite:</label>
                    <input type="text" class="form-control" value="{{ $ticket->dueByDate ?? 'N/A' }}" disabled>
                </div>
                <div class="mb-5">
                    <label>Histórico do Utilizador:</label>
                    <div id="histTickets">
                        <ul>
                            @foreach ($userTickets as $userTicketId)
                                <li>
                                    <a href="{{ route('tickets.show', $userTicketId) }}">Ticket #{{ $userTicketId }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/tickets/show.js') }}"></script>
@endpush

