@extends('layouts.app')

@section('title', 'Editar Permissão')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Permissão: {{ $permission->name }}</h1>
        <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $permission->name) }}" readonly>
                    <div class="form-text">O nome da permissão não pode ser alterado.</div>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $permission->slug) }}" readonly>
                    <div class="form-text">O slug da permissão não pode ser alterado.</div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $permission->description) }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Associar a Roles</label>
                    <div class="card shadow-sm">
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            @foreach($roles as $role)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}" {{ $permission->roles->contains($role->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role-{{ $role->id }}">
                                    {{ $role->name }} <span class="text-muted">({{ $role->slug }})</span>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.permissions.show', $permission) }}" class="btn btn-outline-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection