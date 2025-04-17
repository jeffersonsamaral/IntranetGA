<div class="dashboard-card board-card mb-4">
    <div class="card-header">
        <h5 class="card-title">
            <i class="fas fa-clipboard-list text-primary me-2"></i> 
            Mural de Recados
        </h5>
        <div>
            @can('board.create')
            <a href="{{ route('admin.board.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Novo Recado
            </a>
            @endcan
            
            @can('board.view')
            <a href="{{ route('admin.board.index') }}" class="btn btn-sm btn-outline ms-1">
                <i class="fas fa-list me-1"></i> Ver Todos
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body p-0">
        <div class="board-preview">
            @php
                $pinnedMessages = \App\Models\BoardMessage::with('user')
                    ->where('is_active', true)
                    ->where('is_pinned', true)
                    ->orderBy('updated_at', 'desc')
                    ->take(2)
                    ->get();
                    
                $regularMessages = \App\Models\BoardMessage::with('user')
                    ->where('is_active', true)
                    ->where('is_pinned', false)
                    ->orderBy('created_at', 'desc')
                    ->take(5 - $pinnedMessages->count())
                    ->get();
                    
                $messages = $pinnedMessages->concat($regularMessages);
            @endphp
            
            @if($messages->isEmpty())
                <div class="p-4 text-center">
                    <p class="text-muted mb-0">Nenhum recado disponível.</p>
                    @can('board.create')
                    <p class="mt-2">
                        <a href="{{ route('admin.board.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus me-1"></i> Criar primeiro recado
                        </a>
                    </p>
                    @endcan
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($messages as $message)
                    <a href="{{ route('admin.board.show', $message) }}" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">
                                @if($message->is_pinned)
                                <i class="fas fa-thumbtack text-primary me-1"></i> 
                                @endif
                                {{ $message->title }}
                            </h6>
                            <small>{{ $message->created_at->diffForHumans() }}</small>
                        </div>
                        <p class="mb-1 text-truncate">{{ strip_tags($message->content) }}</p>
                        <small class="text-muted">
                            Por: {{ $message->user->name }}
                            @if($message->attachment)
                            <i class="fas fa-paperclip ms-2"></i>
                            @endif
                        </small>
                    </a>
                    @endforeach
                </div>
                
                @if(count($messages) > 0)
                <div class="text-center py-2 border-top">
                    @can('board.view')
                    <a href="{{ route('admin.board.index') }}" class="text-decoration-none text-primary">
                        Ver todos os recados <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                    @endcan
                </div>
                @endif
            @endif
        </div>
    </div>
</div>