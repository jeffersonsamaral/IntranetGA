<!-- resources/views/auth/login.blade.php -->
@extends('layouts.auth')

@section('title', 'Login - IntranetGA')

@section('content')
<div class="auth-container">
    <div class="login-card">
        <div class="login-logo-container">
            <img src="{{ asset('images/logo.png') }}" class="login-logo" alt="Logo IntranetGA" onerror="this.src='{{ asset('images/default-logo.png') }}'; this.onerror='';">
        </div>
        
        <div class="login-body">
            <h1 class="login-title">Acesse a Intranet</h1>
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
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
            &copy; {{ date('Y') }} Grupo Amplitec - Todos os direitos reservados
        </div>
    </div>
</div>

<style>
/* Estilos específicos para a página de login */
.auth-container {
    background-color: #ffffff;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.login-card {
    width: 100%;
    max-width: 420px;
    border-radius: var(--border-radius-lg);
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    background-color: white;
}

.login-logo-container {
    padding: var(--spacing-6) var(--spacing-6) var(--spacing-2);
    text-align: center;
    background-color: white;
}

.login-logo {
    height: 80px;
    width: auto;
    margin: 0 auto;
    object-fit: contain;
}

.login-title {
    color: var(--color-primary);
    font-size: var(--font-size-xl);
    font-weight: var(--font-weight-semibold);
    margin: 0 0 var(--spacing-4);
    text-align: center;
}

.login-body {
    background-color: white;
    padding: var(--spacing-6);
    padding-top: 0;
}

.login-form {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-4);
}

.login-form .form-group {
    margin-bottom: var(--spacing-4);
}

.login-form .form-label {
    font-weight: var(--font-weight-medium);
    margin-bottom: var(--spacing-2);
    color: var(--color-text-light);
}

.login-form .form-control {
    padding: var(--spacing-3);
    border-radius: var(--border-radius-md);
    border: 1px solid #dce0e5;
    transition: all 0.2s ease;
}

.login-form .form-control:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.15);
}

.login-remember {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-4);
}

.login-submit {
    width: 100%;
    padding: var(--spacing-3) var(--spacing-4);
    font-weight: var(--font-weight-semibold);
    transition: all 0.2s ease;
}

.login-footer {
    padding: var(--spacing-4) var(--spacing-6);
    background-color: #f8f9fa;
    border-top: 1px solid #eee;
    font-size: var(--font-size-sm);
    color: #6c757d;
    text-align: center;
}

/* Responsividade */
@media (max-width: 576px) {
    .login-card {
        max-width: 100%;
        margin: 0 1rem;
    }
    
    .login-body {
        padding: var(--spacing-4);
    }
}
</style>
@endsection