@extends('layouts.app')

@section('content')
<style>
  .login-wrapper {
    min-height: calc(100vh - 72px);
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #1a3a6b 0%, #0d6efd 60%, #6ea8fe 100%);
    padding: 2rem 1rem;
  }

  .login-card {
    width: 100%;
    max-width: 440px;
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    border: none;
  }

  .login-card-header {
    background: linear-gradient(135deg, #1a3a6b, #0d6efd);
    padding: 2.5rem 2rem 2rem;
    text-align: center;
    color: #fff;
  }

  .login-card-header .login-icon {
    width: 72px;
    height: 72px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
  }

  .login-card-header .login-icon i {
    font-size: 2rem;
    color: #fff;
  }

  .login-card-header h4 {
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
    letter-spacing: 0.5px;
  }

  .login-card-header p {
    font-size: 0.875rem;
    opacity: 0.8;
    margin: 0;
  }

  .login-card-body {
    background: #fff;
    padding: 2.25rem 2rem 2rem;
  }

  .input-group-login {
    position: relative;
    margin-bottom: 1.25rem;
  }

  .input-group-login label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.4rem;
    letter-spacing: 0.4px;
    text-transform: uppercase;
  }

  .input-group-login .input-with-icon {
    position: relative;
  }

  .input-group-login .input-with-icon .field-icon {
    position: absolute;
    left: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    color: #adb5bd;
    font-size: 0.95rem;
    pointer-events: none;
    transition: color 0.2s;
  }

  .input-group-login .input-with-icon input {
    padding-left: 2.6rem;
    border-radius: 0.6rem;
    border: 1.5px solid #dee2e6;
    height: 46px;
    font-size: 0.95rem;
    transition: border-color 0.2s, box-shadow 0.2s;
    width: 100%;
  }

  .input-group-login .input-with-icon input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
    outline: none;
  }

  .input-group-login .input-with-icon input:focus + .field-icon,
  .input-group-login .input-with-icon input:focus ~ .field-icon {
    color: #0d6efd;
  }

  .input-group-login .input-with-icon input.is-invalid {
    border-color: #dc3545;
  }

  .input-group-login .input-with-icon .field-icon-inside {
    position: absolute;
    left: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    color: #adb5bd;
    font-size: 0.95rem;
    pointer-events: none;
  }

  .remember-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
  }

  .remember-row .form-check {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0;
  }

  .remember-row .form-check-input {
    margin: 0;
    accent-color: #0d6efd;
    width: 16px;
    height: 16px;
    cursor: pointer;
  }

  .remember-row .form-check-label {
    font-size: 0.875rem;
    color: #6c757d;
    cursor: pointer;
    margin: 0;
  }

  .btn-login {
    width: 100%;
    height: 48px;
    border-radius: 0.6rem;
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    background: linear-gradient(135deg, #1a3a6b, #0d6efd);
    border: none;
    color: #fff;
    transition: opacity 0.2s, transform 0.15s, box-shadow 0.2s;
    box-shadow: 0 4px 14px rgba(13, 110, 253, 0.35);
  }

  .btn-login:hover {
    opacity: 0.92;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(13, 110, 253, 0.45);
    color: #fff;
  }

  .btn-login:active {
    transform: translateY(0);
  }

  .forgot-link {
    display: block;
    text-align: center;
    margin-top: 1rem;
    font-size: 0.875rem;
    color: #6c757d;
    text-decoration: none;
  }

  .forgot-link:hover {
    color: #0d6efd;
    text-decoration: underline;
  }

  .login-footer {
    background: #f8f9fa;
    padding: 0.9rem 2rem;
    text-align: center;
    border-top: 1px solid #f0f0f0;
    font-size: 0.78rem;
    color: #adb5bd;
  }
</style>

<div class="login-wrapper">
  <div class="login-card">

    <div class="login-card-header">
      <div class="login-icon">
        <i class="fas fa-vote-yea"></i>
      </div>
      <h4>{{ __('Welcome Back') }}</h4>
      <p>{{ __('Sign in to your account to continue') }}</p>
    </div>

    <div class="login-card-body">
      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="input-group-login">
          <label for="email">{{ __('Email Address') }}</label>
          <div class="input-with-icon">
            <input
              id="email"
              type="email"
              class="form-control @error('email') is-invalid @enderror"
              name="email"
              value="{{ old('email') }}"
              required
              autocomplete="email"
              autofocus
              placeholder="you@example.com"
            >
            <i class="fas fa-envelope field-icon-inside"></i>
            @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
        </div>

        <div class="input-group-login">
          <label for="password">{{ __('Password') }}</label>
          <div class="input-with-icon">
            <input
              id="password"
              type="password"
              class="form-control @error('password') is-invalid @enderror"
              name="password"
              required
              autocomplete="current-password"
              placeholder="••••••••"
            >
            <i class="fas fa-lock field-icon-inside"></i>
            @error('password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
        </div>

        <div class="remember-row">
          <div class="form-check">
            <input
              class="form-check-input"
              type="checkbox"
              name="remember"
              id="remember"
              {{ old('remember') ? 'checked' : '' }}
            >
            <label class="form-check-label" for="remember">
              {{ __('Remember Me') }}
            </label>
          </div>

          @if (Route::has('password.request'))
            <a class="forgot-link" style="margin-top:0; display:inline;" href="{{ route('password.request') }}">
              {{ __('Forgot Password?') }}
            </a>
          @endif
        </div>

        <button type="submit" class="btn btn-login">
          <i class="fas fa-sign-in-alt me-2"></i> {{ __('Sign In') }}
        </button>

      </form>
    </div>

    <div class="login-footer">
      &copy; {{ date('Y') }} &nbsp;{{ config('app.name', 'Elections System') }}
    </div>

  </div>
</div>
@endsection
