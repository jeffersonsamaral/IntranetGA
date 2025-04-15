
@extends('layouts.app')

@section('title', 'Mural de Recados')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mural de Recados</h1>
        @can('board.create')
        <a href="{{ route('admin.board.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Recado
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="board-messages">
        @forelse($messages as $message)
        <div class="card shadow-sm mb-4 {{ $message->is_pinned ? 'border-primary' : '' }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    @if($message->is_pinned)
                    <i class="fas fa-thumbtack text-primary"></i> 
                    @endif
                    {{ $message->title }}
                </h5>
                <div>
                    @can('board.pin')
                    <form action="{{ route('admin.board.toggle-pin', $message) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $message->is_pinned ? 'btn-outline-primary' : 'btn-outline-secondary' }}" title="{{ $message->is_pinned ? 'Desafixar' : 'Fixar' }}">
                            <i class="fas fa-thumbtack"></i>
                        </button>
                    </form>
                    @endcan
                    
                    @can('board.edit')
                    <a href="{{ route('admin.board.edit', $message) }}" class="btn btn-sm btn-primary" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                    @endcan
                    
                    @can('board.delete')
                    <form action="{{ route('admin.board.destroy', $message) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este recado?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    {!! $message->content !!}
                </div>
                
                @if($message->attachment)
                <div class="attachment mb-3">
                    <a href="{{ Storage::url($message->attachment) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-paperclip"></i> Anexo
                    </a>
                </div>
                @endif
                
                <div class="message-meta text-muted">
                    <small>
                        Por: {{ $message->user->name }} | 
                        Criado em: {{ $message->created_at->format('d/m/Y H:i') }}
                        @if($message->created_at != $message->updated_at)
                        | Atualizado em: {{ $message->updated_at->format('d/m/Y H:i') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
        @empty
        <div class="alert alert-info">
            Nenhum recado encontrado. Crie o primeiro recado clicando no botão "Novo Recado".
        </div>
        @endforelse
    </div>
    
    {{ $messages->links() }}
</div>
@endsection