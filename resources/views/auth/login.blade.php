<!-- resources/views/auth/login.blade.php -->
@extends('layouts.auth')

@section('title', 'Login - IntranetGA')

@section('content')
<div class="login-card">
    <div class="login-header">
        <h1 class="login-title">IntranetGA</h1>
        <p class="text-white">Faça login para acessar o sistema</p>
    </div>
    <div class="login-body">
        @if($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf
            
            <div class="form-group">
                <label for="username" class="form-label">Usuário</label>
                <input type="text" id="username" name="username" class="form-control @error('username') form-control-error @enderror" value="{{ old('username') }}" required autofocus>
                @error('username')
                    <span class="form-error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Senha</label>
                <input type="password" id="password" name="password" class="form-control @error('password') form-control-error @enderror" required>
                @error('password')
                    <span class="form-error-text">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="login-remember">
                <div class="form-check">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="form-check-label">Lembrar-me</label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary login-submit">Entrar</button>
        </form>
    </div>
    <div class="login-footer">
        &copy; {{ date('Y') }} IntranetGA - Todos os direitos reservados
    </div>
</div>
@endsection