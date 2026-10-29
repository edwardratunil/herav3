<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 dark:bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <title>Sign In - HERA v3 Philippine Education HR & Payroll</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans text-slate-800 dark:text-slate-100 antialiased flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 selection:bg-emerald-500 selection:text-white">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-slate-900 p-8 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800">
        <!-- Brand Header -->
        <div class="text-center">
            <div class="mx-auto w-12 h-12 rounded-2xl bg-emerald-600 flex items-center justify-center text-white font-black text-2xl shadow-md">
                H
            </div>
            <h2 class="mt-4 text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                HERA v3 Platform
            </h2>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Philippine Multi-Tenant HR, Workload & Payroll System
            </p>
        </div>

        <!-- Superadmin Credential Helper Pill -->
        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800/80 rounded-xl text-xs space-y-1">
            <div class="flex items-center gap-1.5 font-bold text-emerald-800 dark:text-emerald-300">
                <span>🔐</span>
                <span>Superadmin Credentials</span>
            </div>
            <div class="text-slate-600 dark:text-slate-300 font-mono text-[11px] flex justify-between">
                <span>Email: <strong class="text-emerald-700 dark:text-emerald-300">admin@hera.ph</strong></span>
                <span>Pass: <strong class="text-emerald-700 dark:text-emerald-300">Password123!</strong></span>
            </div>
        </div>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="p-3 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 rounded-xl text-xs text-rose-700 dark:text-rose-300">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Login Form -->
        <form class="mt-6 space-y-5" action="{{ route('login') }}" method="POST">
            @csrf

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                    Email Address
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    value="{{ old('email', 'admin@hera.ph') }}"
                    class="w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition"
                    placeholder="you@institution.edu.ph"
                >
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                    Password
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                    value="Password123!"
                    class="w-full min-h-[44px] px-3.5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/70 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-none transition"
                    placeholder="••••••••••••"
                >
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        name="remember"
                        checked
                        class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700 bg-slate-50 dark:bg-slate-800"
                    >
                    <span class="text-slate-600 dark:text-slate-400">Remember this device</span>
                </label>
            </div>

            <button
                type="submit"
                class="w-full min-h-[44px] flex justify-center items-center py-2.5 px-4 border border-transparent rounded-lg text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] transition shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500"
            >
                Sign In to Platform
            </button>
        </form>

        <div class="text-center pt-2 border-t border-slate-100 dark:border-slate-800">
            <span class="text-[11px] text-slate-400">
                Encrypted Session • RA 10173 Protected • cPanel Optimized
            </span>
        </div>
    </div>
</body>
</html>

