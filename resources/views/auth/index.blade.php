@extends('layouts.auth')

@section('title', 'Login Pemandu')

@section('content')
<div class="mentor-login-shell w-full max-w-5xl z-10">
    <div class="mentor-login-brand">
        <div class="mentor-brand-logos">
            <span class="mentor-logo mentor-logo-masta"><img src="{{ asset('img/logo_mastamaru_2025.png') }}" alt="Logo MASTAMARU"></span>
            <span class="mentor-logo mentor-logo-campus"><img src="{{ asset('img/logo_Universitas-Muhammadiyah-Ponorogo-1.png') }}" alt="Logo Universitas Muhammadiyah Ponorogo"></span>
        </div>
        <p class="mentor-eyebrow">Area pemandu</p>
        <h1>Selamat datang kembali.</h1>
        <p>Masuk untuk mengelola presensi dan mendampingi peserta MASTAMARU.</p>
        <div class="mentor-trust-note"><span class="mentor-trust-dot"></span><span>Akses khusus pemandu terdaftar</span></div>
    </div>
    <div class="mentor-login-form-wrap">
    <!-- Logo/Header Section -->
    <div class="mentor-form-heading"><p class="mentor-eyebrow">Secure sign-in</p><h2>Login Pemandu</h2><p>Gunakan NIM dan kata sandi yang diberikan administrator.</p></div>

    <!-- Notifikasi dengan SweetAlert2 -->
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3b82f6',
                    timer: 3000,
                    timerProgressBar: true
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#3b82f6'
                });
            });
        </script>
    @endif

    <!-- Login Form -->
    <div class="glass-effect mentor-form-card rounded-2xl p-6 sm:p-8">
        <form class="space-y-6" action="{{ route('mentor.login.post') }}" method="POST">
            @csrf

            <!-- Student ID Field -->
            <div class="space-y-2">
                <label for="student_id" class="block text-sm font-semibold text-gray-700">
                    NIM Pemandu
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-4 0v2m0 0h4"></path>
                        </svg>
                    </div>
                    <input
                        id="student_id"
                        name="student_id"
                        type="text"
                        required
                         class="input-focus block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-300 focus:border-transparent bg-white/90 backdrop-blur-sm"
                        placeholder="Masukkan NIM Anda"
                        value="{{ old('student_id') }}"
                    >
                </div>
                @error('student_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
                <label for="password" class="block text-sm font-semibold text-gray-700">
                    Kata Sandi
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                         class="input-focus block w-full pl-10 pr-12 py-3 border border-slate-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-sky-300 focus:border-transparent bg-white/90 backdrop-blur-sm"
                        placeholder="Masukkan kata sandi"
                    >
                    <button
                        type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        onclick="togglePassword()"
                    >
                        <svg id="eye-open" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="eye-closed" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input
                        id="remember_me"
                        name="remember"
                        type="checkbox"
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded transition-colors"
                    >
                    <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                        Ingat saya
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button
                    type="submit"
                    class="btn-hover mentor-submit flex justify-center items-center gap-2 group w-full py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-slate-700 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-all duration-300 shadow-lg"
                >
                    <span class="font-bold">Masuk</span>
                </button>
            </div>
        </form>

        <!-- Additional Info -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Belum memiliki akses?
                <a href="https://wa.me/62895366427079" target="_blank" class="font-medium text-primary-600 hover:text-primary-500 transition-colors">
                    Hubungi Administrator
                </a>
            </p>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="text-center mentor-login-footer">
        <p class="text-slate-500 text-sm">
            © {{ config('app.name') }}.
        </p>
    </div>
</div>
</div>

<style>
    .mentor-login-shell { display:grid; grid-template-columns:minmax(0,1fr) minmax(360px,.85fr); gap:34px; align-items:center; }
    .mentor-login-brand { padding:18px 12px 18px 4px; color:var(--auth-ink); }
    .mentor-brand-logos { display:flex; align-items:center; gap:10px; margin-bottom:34px; }
    .mentor-logo { display:grid; place-items:center; background:rgba(255,255,255,.8); border:1px solid rgba(255,255,255,.9); box-shadow:0 12px 28px rgba(75,58,146,.10); }
    .mentor-logo img { width:100%; height:100%; object-fit:contain; }
    .mentor-logo-masta { width:64px; height:64px; padding:7px; border-radius:18px; }
    .mentor-logo-campus { width:52px; height:52px; padding:5px; border-radius:15px; }
    .mentor-eyebrow { margin:0 0 9px; color:#5a8aa6; font-size:11px; font-weight:800; letter-spacing:.18em; text-transform:uppercase; }
    .mentor-login-brand h1 { max-width:520px; margin:0; font-size:clamp(2rem,4vw,3.5rem); line-height:1.08; letter-spacing:-.05em; }
    .mentor-login-brand > p:not(.mentor-eyebrow) { max-width:440px; margin:18px 0 0; color:var(--auth-muted); font-size:16px; line-height:1.7; }
    .mentor-trust-note { display:flex; align-items:center; gap:9px; margin-top:28px; color:#748198; font-size:12px; font-weight:600; }
    .mentor-trust-dot { width:8px; height:8px; border-radius:999px; background:#9bd6c4; box-shadow:0 0 0 5px rgba(155,214,196,.16); }
    .mentor-login-form-wrap { min-width:0; }
    .mentor-form-heading { margin:0 0 18px; padding:0 4px; }
    .mentor-form-heading h2 { margin:0; color:var(--auth-ink); font-size:28px; letter-spacing:-.04em; }
    .mentor-form-heading p:last-child { margin:7px 0 0; color:var(--auth-muted); font-size:13px; line-height:1.5; }
    .mentor-form-card { background:rgba(255,255,255,.82); }
    .mentor-form-card label { color:#46536b; }
    .mentor-login-footer { margin-top:18px; }
    .mentor-submit-loading { display:inline-flex; align-items:center; justify-content:center; gap:.7rem; }
    .mentor-submit-spinner { width:1.05em; height:1.05em; border:2px solid currentColor; border-right-color:transparent; border-radius:999px; animation:mentor-spin 1.25s linear infinite; flex:0 0 auto; }
    @keyframes mentor-spin { to { transform:rotate(360deg); } }
    @media (max-width:900px) { .mentor-login-shell { grid-template-columns:minmax(0,1fr) minmax(320px,.9fr); gap:22px; } .mentor-login-brand h1{font-size:2.35rem}.mentor-brand-logos{margin-bottom:24px} }
    @media (max-width:700px) { .mentor-login-shell { display:block; max-width:500px; } .mentor-login-brand { padding:0 4px 24px; text-align:center; } .mentor-brand-logos{justify-content:center;margin-bottom:20px}.mentor-login-brand > p:not(.mentor-eyebrow){margin-left:auto;margin-right:auto;font-size:14px}.mentor-trust-note{justify-content:center;margin-top:18px}.mentor-login-brand h1{font-size:2rem}.mentor-form-heading{text-align:center}.mentor-login-footer{margin-bottom:4px} }
    @media (max-width:420px) { .mentor-login-brand h1{font-size:1.75rem}.mentor-login-brand > p:not(.mentor-eyebrow){font-size:13px}.mentor-form-card{padding:20px!important}.mentor-logo-masta{width:54px;height:54px}.mentor-logo-campus{width:44px;height:44px} }
</style>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    } else {
        passwordInput.type = 'password';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    }
}

// Add some interactive effects
document.addEventListener('DOMContentLoaded', function() {
    // Add focus effects to inputs
    const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('scale-105');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('scale-105');
        });
    });

    // Add loading state to submit button
    const form = document.querySelector('form');
    const submitBtn = document.querySelector('button[type="submit"]');

    form.addEventListener('submit', function() {
        submitBtn.innerHTML = '<span class="mentor-submit-loading"><span class="mentor-submit-spinner" aria-hidden="true"></span><span>Memproses...</span></span>';
        submitBtn.disabled = true;
    });
});
</script>
@endsection
