// resources/views/admin/board/edit.blade.php
@extends('layouts.app')

@section('title', 'Editar Recado')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Editar Recado</h1>
        <a href="{{ route('admin.board.show', $board) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.board.update', $board) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $board->title) }}" required>
                    @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Conteúdo</label>
                    <textarea class="form-control richtext @error('content') is-invalid @enderror" id="content" name="content" rows="10">{{ old('content', $board->content) }}</textarea>
                    @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="attachment" class="form-label">Anexo</label>
                    @if($board->attachment)
                    <div class="mb-2">
                        <a href="{{ Storage::url($board->attachment) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-paperclip"></i> Ver anexo atual
                        </a>
                    </div>
                    @endif
                    <input type="file" class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment">
                    @error('attachment')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Tamanho máximo: 10MB. Deixe em branco para manter o anexo atual.
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $board->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Recado ativo
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_pinned" name="is_pinned" value="1" {{ old('is_pinned', $board->is_pinned) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_pinned">
                            Fixar recado
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.board.show', $board) }}" class="btn btn-outline-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '.richtext',
            height: 300,
            menubar: false,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount'
            ],
            toolbar: 'undo redo | formatselect | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
        });
    });
</script>
@endpush
@endsection