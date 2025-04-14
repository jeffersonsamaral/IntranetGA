// resources/views/components/recent-board-messages.blade.php
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Mural de Recados</h5>
        @can('board.view')
        <a href="{{ route('admin.board.index') }}" class="btn btn-sm btn-primary">
            Ver todos
        </a>
        @endcan
    </div>
    <div class="card-body">
        @php
            $recentMessages = \App\Models\BoardMessage::with('user')
                ->where('is_active', true)
                ->orderBy('is_pinned', 'desc')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        @endphp
        
        @forelse($recentMessages as $message)
            <div class="message-item {{ !$loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
                <h6 class="message-title">
                    @if($message->is_pinned)
                    <i class="fas fa-thumbtack text-primary"></i> 
                    @endif
                    {{ $message->title }}
                </h6>
                <div class="message-content mb-2">
                    {!! Str::limit(strip_tags($message->content), 100) !!}
                </div>
                <div class="message-meta d-flex justify-content-between">
                    <small class="text-muted">
                        Por {{ $message->user->name }} em {{ $message->created_at->format('d/m/Y') }}
                    </small>
                    @can('board.view')
                    <a href="{{ route('admin.board.show', $message) }}" class="btn btn-sm btn-link p-0">
                        Ler mais
                    </a>
                    @endcan
                </div>
            </div>
        @empty
            <p class="text-muted">Nenhum recado disponível.</p>
        @endforelse
    </div>
</div>