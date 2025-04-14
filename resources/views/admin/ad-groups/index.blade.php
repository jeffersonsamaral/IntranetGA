{{-- resources/views/admin/ad-groups/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Gerenciamento de Grupos AD')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gerenciamento de Grupos AD</h1>
        <div>
            @can('ad-groups.sync')
            <a href="{{ route('admin.ad-groups.sync') }}" class="btn btn-primary">
                <i class="fas fa-sync"></i> Sincronizar Grupos
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>SID</th>
                            <th>Descrição</th>
                            <th>Roles</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($adGroups as $adGroup)
                        <tr>
                            <td>{{ $adGroup->name }}</td>
                            <td><code>{{ Str::limit($adGroup->sid, 15) }}</code></td>
                            <td>{{ Str::limit($adGroup->description, 50) ?? 'Sem descrição' }}</td>
                            <td>{{ $adGroup->roles->count() }}</td>
                            <td>
                                <a href="{{ route('admin.ad-groups.show', $adGroup) }}" class="btn btn-sm btn-info" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('ad-groups.map')
                                <a href="{{ route('admin.ad-groups.edit', $adGroup) }}" class="btn btn-sm btn-primary" title="Mapear">
                                    <i class="fas fa-map-marker-alt"></i>
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Nenhum grupo AD encontrado. Use o botão "Sincronizar Grupos" para importar grupos do Active Directory.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $adGroups->links() }}
        </div>
    </div>
</div>
@endsection