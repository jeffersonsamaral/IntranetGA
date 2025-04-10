@extends('layouts.app')

@section('title', 'Criar Nova Permissão')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Criar Nova Permissão</h1>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.permissions.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Nome descritivo da permissão (ex: "Visualizar Usuários")</div>
                </div>

                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" required>
                    @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Identificador único usado no código (ex: "users.view")</div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Descrição clara do que esta permissão permite fazer</div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Criar Permissão</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Script para gerar automaticamente o slug a partir do nome
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        
        // Função para converter texto em slug
        const slugify = (text) => {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '.') // Substitui espaços por pontos
                .replace(/[^\w\-\.]+/g, '') // Remove caracteres não alfanuméricos
                .replace(/\-\-+/g, '.') // Substitui múltiplos hífens por ponto
                .replace(/^-+/, '') // Remove hífens do início
                .replace(/-+$/, ''); // Remove hífens do final
        };
        
        // Atualiza o slug quando o nome é alterado
        nameInput.addEventListener('input', function() {
            // Só atualiza se o usuário não tiver modificado manualmente o slug
            if (!slugInput.dataset.manuallyChanged) {
                slugInput.value = slugify(nameInput.value);
            }
        });
        
        // Marca quando o usuário edita manualmente o slug
        slugInput.addEventListener('input', function() {
            slugInput.dataset.manuallyChanged = 'true';
        });
    });
</script>
@endpush
@endsection