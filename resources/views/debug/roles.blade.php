@extends('layouts.app')

@section('title', 'Debug Roles')

@section('content')
<div class="container py-4">
    <h1>Roles Debug</h1>
    
    <div class="card">
        <div class="card-header">
            <h5>Todas as Roles</h5>
        </div>
        <div class="card-body">
            @if($roles->isEmpty())
                <div class="alert alert-warning">
                    Nenhuma role encontrada. Verifique se as migrações foram executadas.
                </div>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Slug</th>
                            <th>Descrição</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->slug }}</td>
                            <td>{{ $role->description }}</td>
                            <td>{{ $role->is_active ? 'Ativo' : 'Inativo' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h5>Informações de Debug</h5>
        </div>
        <div class="card-body">
            <h6>Tabelas do Banco de Dados</h6>
            <pre>{{ print_r(DB::select('SHOW TABLES'), true) }}</pre>
            
            @if(DB::select('SHOW TABLES LIKE "roles"'))
                <h6>Estrutura da Tabela 'roles'</h6>
                <pre>{{ print_r(DB::select('DESCRIBE roles'), true) }}</pre>
            @endif
            
            @if(DB::select('SHOW TABLES LIKE "user_role"'))
                <h6>Estrutura da Tabela 'user_role'</h6>
                <pre>{{ print_r(DB::select('DESCRIBE user_role'), true) }}</pre>
            @endif
            
            @if(DB::select('SHOW TABLES LIKE "permission_role"'))
                <h6>Estrutura da Tabela 'permission_role'</h6>
                <pre>{{ print_r(DB::select('DESCRIBE permission_role'), true) }}</pre>
            @endif
        </div>
    </div>
</div>
@endsection