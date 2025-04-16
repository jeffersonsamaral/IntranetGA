{{-- resources/views/admin/roles/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Regra')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Regra: {{ $role->name }}</h1>
        <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $role->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $role->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Role ativa
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Permissões</label>
                    <div class="card">
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            @foreach($permissions as $permission)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="permission-{{ $permission->id }}" name="permissions[]" value="{{ $permission->id }}" 
                                    {{ is_array(old('permissions')) ? (in_array($permission->id, old('permissions')) ? 'checked' : '') : ($role->permissions->contains($permission->id) ? 'checked' : '') }}>
                                <label class="form-check-label" for="permission-{{ $permission->id }}">
                                    {{ $permission->name }} <span class="text-muted">({{ $permission->slug }})</span>
                                </label>
                                @if($permission->description)
                                <div class="form-text">{{ $permission->description }}</div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Adição da seção de Grupos AD -->
                <div class="mb-4">
                    <label class="form-label">Grupos AD Associados</label>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Os usuários que pertencerem a estes grupos do Active Directory receberão automaticamente esta Regra.
                    </div>
                    <div class="card">
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            @forelse($adGroups as $adGroup)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="adgroup-{{ $adGroup->id }}" name="ad_groups[]" value="{{ $adGroup->id }}" 
                                    {{ is_array(old('ad_groups')) ? (in_array($adGroup->id, old('ad_groups')) ? 'checked' : '') : ($role->adGroups->contains($adGroup->id) ? 'checked' : '') }}>
                                <label class="form-check-label" for="adgroup-{{ $adGroup->id }}">
                                    {{ $adGroup->name }}
                                </label>
                                @if($adGroup->description)
                                <div class="form-text">{{ $adGroup->description }}</div>
                                @endif
                            </div>
                            @empty
                            <p class="text-muted">Nenhum grupo AD disponível. Sincronize os grupos primeiro.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-outline-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection