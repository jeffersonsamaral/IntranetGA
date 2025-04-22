@extends('layouts.app')

@section('title', 'Mural de Recados')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mural de Recados</h1>
        @can('board.create')
        <a href="{{ route('admin.board.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Novo Recado
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Título</th>
                            <th>Conteúdo</th>
                            <th>Autor</th>
                            <th>Data</th>
                            <th style="width: 140px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                        <tr>
                            <td class="text-center">
                                @if($message->is_pinned)
                                <i class="fas fa-thumbtack text-primary" title="Recado fixado"></i>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $message->title }}</strong>
                                @if($message->attachment)
                                <i class="fas fa-paperclip text-muted ms-1" title="Contém anexo"></i>
                                @endif
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 350px;">
                                    {{ strip_tags($message->content) }}
                                </div>
                            </td>
                            <td>{{ $message->user->name }}</td>
                            <td>
                                <div>{{ $message->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $message->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.board.show', $message) }}" class="btn btn-sm btn-info" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @can('board.pin')
                                    <form action="{{ route('admin.board.toggle-pin', $message) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $message->is_pinned ? 'btn-warning' : 'btn-outline-secondary' }}" title="{{ $message->is_pinned ? 'Desafixar' : 'Fixar' }}">
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
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="alert alert-info mb-0">
                                    Nenhum recado encontrado. 
                                    @can('board.create')
                                    Crie o primeiro recado clicando no botão "Novo Recado".
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $messages->links() }}
    </div>
    
    <div class="mt-3 text-muted small">
        <i class="fas fa-info-circle"></i> 
        <strong>Legenda:</strong>
        <span class="ms-2"><i class="fas fa-thumbtack text-primary"></i> Recado fixado</span>
        <span class="ms-3"><i class="fas fa-paperclip text-muted"></i> Contém anexo</span>
    </div>
</div>

<style>
    .table td {
        vertical-align: middle;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    @media (prefers-color-scheme: dark) {
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.02);
        }
    }
</style>
@endsection