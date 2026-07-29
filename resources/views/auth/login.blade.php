@php
    $errorBag = session('errors');
    $errorBag = $errorBag instanceof \Illuminate\Support\ViewErrorBag ? $errorBag : new \Illuminate\Support\ViewErrorBag;
@endphp

<!DOCTYPE html>
<html lang="id" data-theme="{{ config('theme.default') }}" data-theme-default="{{ config('theme.default') }}"
    data-theme-options="{{ implode(',', array_keys(config('theme.themes'))) }}"
    data-theme-storage-key="{{ config('theme.storage_key') }}" data-layout-path="dashboard-free" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Login - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#2f2838] font-sans antialiased text-white">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-8 sm:px-6">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_22%_76%,rgba(255,70,85,0.12),transparent_24%),radial-gradient(circle_at_82%_24%,rgba(255,70,85,0.10),transparent_18%)]"></div>

        <section class="relative z-10 w-full max-w-md rounded-2xl border border-white/10 bg-[#292331] p-6 shadow-2xl shadow-black/40 sm:p-9">
            <a href="{{ route('home') }}" class="mb-8 flex items-center gap-3">
                <span class="flex size-11 items-center justify-center rounded-xl bg-primary text-primary-content shadow-lg shadow-primary/30">
                    <span class="icon-[tabler--template] size-5"></span>
                </span>
                <div>
                    <p class="text-xl font-bold leading-tight text-white">{{ config('app.name') }}</p>
                    <p class="mt-0.5 text-xs font-medium tracking-wide text-[#c9c1d0]">Laravel application skeleton</p>
                </div>
            </a>

            <div class="mb-7">
                <h1 class="text-3xl font-bold tracking-tight text-white">Masuk ke akun</h1>
                <p class="mt-2 text-sm leading-6 text-[#c9c1d0]">Gunakan email atau username dan kata sandi Anda.</p>
            </div>

            @if (session()->has('alert'))
                <div role="status" aria-live="polite" class="mb-5 rounded-xl border border-success/40 bg-success/10 p-3.5">
                    <p class="text-sm font-bold text-white">{{ session('alert.title', 'Berhasil') }}</p>
                    <p class="mt-0.5 text-sm text-[#d9fbe5]">{{ session('alert.message') }}</p>
                </div>
            @endif

            <form action="{{ url('login') }}" method="POST" class="space-y-5" x-data="{ showPassword: false }">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-white" for="login">Email atau username <span class="text-primary">*</span></label>
                    <label class="relative mt-2 flex h-12 items-center rounded-lg border bg-[#352e3e] text-white transition focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/25 {{ $errorBag->has('login') ? 'border-error' : 'border-[#766d80]' }}">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-[#c7becf]">
                            <span class="icon-[tabler--user] size-5"></span>
                        </span>
                        <input id="login" type="text" name="login" value="{{ old('login') }}" class="h-full w-full bg-transparent pl-11 pr-4 text-base outline-none placeholder:text-[#aaa1b2]" placeholder="Email atau username" required autofocus>
                    </label>
                    @error('login')<p class="mt-1.5 text-xs font-medium text-error">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-white" for="password">Kata sandi <span class="text-primary">*</span></label>
                    <label class="relative mt-2 flex h-12 items-center rounded-lg border bg-[#352e3e] text-white transition focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/25 {{ $errorBag->has('password') ? 'border-error' : 'border-[#766d80]' }}">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-[#c7becf]">
                            <span class="icon-[tabler--lock] size-5"></span>
                        </span>
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" class="h-full w-full bg-transparent pl-11 pr-11 text-base outline-none placeholder:text-[#aaa1b2]" placeholder="Masukkan kata sandi" required>
                        <button type="button" class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-[#c7becf] transition hover:text-primary" @click="showPassword = ! showPassword" :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                            <span :class="showPassword ? 'icon-[tabler--eye-off]' : 'icon-[tabler--eye]'" class="size-5"></span>
                        </button>
                    </label>
                    @error('password')<p class="mt-1.5 text-xs font-medium text-error">{{ $message }}</p>@enderror
                </div>

                <label class="flex cursor-pointer items-center gap-3 text-sm font-medium text-[#e9e3ed]">
                    <input type="checkbox" name="remember" value="1" class="checkbox checkbox-primary checkbox-sm rounded-sm border-[#7d7487] bg-transparent" @checked(old('remember'))>
                    <span>Ingat saya di perangkat ini</span>
                </label>

                <button class="btn btn-lg btn-primary btn-gradient btn-block h-12 rounded-lg text-base font-bold shadow-lg shadow-primary/20" type="submit">Masuk</button>
            </form>
        </section>
    </main>
</body>
</html>
