// resources/views/admin/board/show.blade.php
@extends('layouts.app')

@section('title', $board->title)

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $board->title }}</h1>
        <div>
            <a href="{{ route('admin.board.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            @can('board.edit')
            <a href="{{ route('admin.board.edit', $board) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card shadow-sm {{ $board->is_pinned ? 'border-primary' : '' }}">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                @if($board->is_pinned)
                <span class="badge bg-primary me-2">
                    <i class="fas fa-thumbtack"></i> Fixado
                </span>
                @endif
                
                @if(!$board->is_active)
                <span class="badge bg-danger me-2">Inativo</span>
                @endif
            </div>
            
            @can('board.pin')
            <form action="{{ route('admin.board.toggle-pin', $board) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm {{ $board->is_pinned ? 'btn-outline-primary' : 'btn-outline-secondary' }}">
                    <i class="fas fa-thumbtack"></i> {{ $board->is_pinned ? 'Desafixar' : 'Fixar' }}
                </button>
            </form>
            @endcan
        </div>
        
        <div class="card-body">
            <div class="board-content mb-4">
                {!! $board->content !!}
            </div>
            
            @if($board->attachment)
            <div class="attachment mb-4">
                <h6>Anexo:</h6>
                <a href="{{ Storage::url($board->attachment) }}" target="_blank" class="btn btn-outline-secondary">
                    <i class="fas fa-paperclip"></i> Baixar anexo
                </a>
            </div>
            @endif
            
            <div class="message-info">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-4">Autor:</dt>
                            <dd class="col-sm-8">{{ $board->user->name }}</dd>
                            
                            <dt class="col-sm-4">Criado em:</dt>
                            <dd class="col-sm-8">{{ $board->created_at->format('d/m/Y H:i') }}</dd>
                            
                            @if($board->created_at != $board->updated_at)
                            <dt class="col-sm-4">Atualizado em:</dt>
                            <dd class="col-sm-8">{{ $board->updated_at->format('d/m/Y H:i') }}</dd>
                            @endif
                            
                            <dt class="col-sm-4">Status:</dt>
                            <dd class="col-sm-8">
                                @if($board->is_active)
                                <span class="badge bg-success">Ativo</span>
                                @else
                                <span class="badge bg-danger">Inativo</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @can('board.delete')
    <div class="mt-4">
        <form action="{{ route('admin.board.destroy', $board) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este recado? Esta ação não pode ser desfeita.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash"></i> Excluir Recado
            </button>
        </form>
    </div>
    @endcan
</div>
@endsection