{{-- resources/views/admin/ad-groups/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Mapear Grupo AD para Roles')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mapear Grupo AD para Roles: {{ $adGroup->name }}</h1>
        <a href="{{ route('admin.ad-groups.show', $adGroup) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.ad-groups.update', $adGroup) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Selecione as Roles para este Grupo AD</label>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Usuários que pertencem a este grupo AD receberão automaticamente todas as permissões das roles selecionadas.
                    </div>
                    
                    <div class="card">
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            @foreach($roles as $role)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}" 
                                    {{ is_array(old('roles')) ? (in_array($role->id, old('roles')) ? 'checked' : '') : ($adGroup->roles->contains($role->id) ? 'checked' : '') }}>
                                <label class="form-check-label" for="role-{{ $role->id }}">
                                    {{ $role->name }} <span class="text-muted">({{ $role->slug }})</span>
                                </label>
                                @if($role->description)
                                <div class="form-text">{{ $role->description }}</div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.ad-groups.show', $adGroup) }}" class="btn btn-outline-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Mapeamento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection