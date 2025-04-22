@extends('layouts.app')

@section('title', 'Visualizar Recado')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Visualizar Recado</h1>
        <a href="{{ route('admin.board.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card shadow-sm mb-4 {{ $board->is_pinned ? 'border-primary' : '' }}">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                @if($board->is_pinned)
                <i class="fas fa-thumbtack text-primary"></i> 
                @endif
                {{ $board->title }}
            </h5>
            <div>
                @can('board.pin')
                <form action="{{ route('admin.board.toggle-pin', $board) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $board->is_pinned ? 'btn-outline-primary' : 'btn-outline-secondary' }}" title="{{ $board->is_pinned ? 'Desafixar' : 'Fixar' }}">
                        <i class="fas fa-thumbtack"></i>
                    </button>
                </form>
                @endcan
                
                @can('board.edit')
                <a href="{{ route('admin.board.edit', $board) }}" class="btn btn-sm btn-primary" title="Editar">
                    <i class="fas fa-edit"></i>
                </a>
                @endcan
                
                @can('board.delete')
                <form action="{{ route('admin.board.destroy', $board) }}" method="POST" class="d-inline">
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
            <div class="board-content mb-4">
                {!! $board->content !!}
            </div>
            
            @if($board->attachment)
            <div class="attachment mb-3">
                <a href="{{ Storage::url($board->attachment) }}" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-paperclip"></i> Baixar Anexo
                </a>
            </div>
            @endif
            
            <div class="message-meta text-muted mt-4 pt-3 border-top">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Autor:</strong> {{ $board->user->name }}</p>
                        <p class="mb-1"><strong>Criado em:</strong> {{ $board->created_at->format('d/m/Y H:i') }}</p>
                        @if($board->created_at != $board->updated_at)
                        <p class="mb-1"><strong>Atualizado em:</strong> {{ $board->updated_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Status:</strong> 
                            @if($board->is_active)
                            <span class="badge bg-success">Ativo</span>
                            @else
                            <span class="badge bg-danger">Inativo</span>
                            @endif
                        </p>
                        <p class="mb-1"><strong>Fixado:</strong> 
                            @if($board->is_pinned)
                            <span class="badge bg-primary">Sim</span>
                            @else
                            <span class="badge bg-secondary">Não</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .board-content {
        min-height: 150px;
    }
    
    .board-content img {
        max-width: 100%;
        height: auto;
    }
</style>
@endsection