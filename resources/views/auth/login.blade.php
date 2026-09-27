<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ t('messages.sign_in') }} - {{ t('messages.app_name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:ital,wght@0,100..900;1,100..900&family=Noto+Sans+Khmer:wght@100..900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'Google Sans Khmer';
            src: local('Google Sans Khmer'), local('Google Sans'), local('Noto Sans Khmer');
        }
        *, html, body, input, button, select, textarea, span, p, h1, h2, h3, h4 {
            font-family: 'Google Sans', 'Google Sans Khmer', 'Noto Sans Khmer', 'Kantumruy Pro', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-8 border border-slate-100 relative">
        <!-- Language Switcher in Login Page with Country Flags -->
        <div class="flex justify-end mb-2">
            <div class="inline-flex items-center gap-1 rounded-2xl bg-slate-100 p-1 text-xs font-semibold">
                <a href="{{ route('locale.switch', 'km') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-all {{ app()->getLocale() === 'km' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <span class="w-4 h-3 rounded-xs overflow-hidden inline-flex items-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 480" class="w-full h-full object-cover">
                            <rect width="640" height="480" fill="#032ea1"/>
                            <rect width="640" height="240" y="120" fill="#e00025"/>
                            <g fill="#ffffff">
                                <path d="M320 160l-16 28h32zm-40 44h80v12h-80zm-16 16h112v10h-112zm-20 14h152v12h-152z"/>
                                <path d="M256 220l-10 24h20zm128 0l-10 24h20zm-64-50l-12 24h24z"/>
                            </g>
                        </svg>
                    </span>
                    <span>ភាសាខ្មែរ</span>
                </a>
                <a href="{{ route('locale.switch', 'en') }}"
                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-all {{ app()->getLocale() === 'en' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <span class="w-4 h-3 rounded-xs overflow-hidden inline-flex items-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 60 30" class="w-full h-full object-cover">
                            <clipPath id="uk-flag-login"><path d="M0 0v30h60V0z"/></clipPath>
                            <clipPath id="uk-flag-login-t"><path d="M30 15h30v15zm0 0v15H0zm0 0H0V0zm0 0V0h30z"/></clipPath>
                            <g clip-path="url(#uk-flag-login)">
                                <path d="M0 0v30h60V0z" fill="#012169"/>
                                <path d="M0 0l60 30m0-30L0 30" stroke="#ffffff" stroke-width="6"/>
                                <path d="M0 0l60 30m0-30L0 30" clip-path="url(#uk-flag-login-t)" stroke="#C8102E" stroke-width="4"/>
                                <path d="M30 0v30M0 15h60" stroke="#ffffff" stroke-width="10"/>
                                <path d="M30 0v30M0 15h60" stroke="#C8102E" stroke-width="6"/>
                            </g>
                        </svg>
                    </span>
                    <span>English</span>
                </a>
            </div>
        </div>

        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-emerald-600 rounded-2xl flex items-center justify-center text-white font-bold text-2xl mx-auto shadow-lg shadow-emerald-500/30 mb-3">
                IMS
            </div>
            <h2 class="text-2xl font-black text-slate-800 tracking-tight">{{ t('messages.welcome_back') }}</h2>
            <p class="text-xs text-slate-500 mt-1">{{ t('messages.sign_in_sub') }}</p>
        </div>

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-xl text-xs font-medium">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-xs font-medium">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ t('messages.email_address') }}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email', 'admin@inventory.com') }}" required autofocus
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:border-emerald-500 focus:outline-none transition">
                </div>
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ t('messages.password') }}</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </span>
                    <input type="password" name="password" value="password" required
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:border-emerald-500 focus:outline-none transition">
                </div>
                @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 mr-2">
                    {{ t('messages.remember_me') }}
                </label>
                <span class="text-emerald-600 font-semibold cursor-pointer">Default: password</span>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/30 text-sm transition duration-150">
                {{ t('messages.sign_in') }}
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs text-slate-400">
            Default credentials: <span class="font-mono text-slate-600 font-semibold">admin@inventory.com</span> / <span class="font-mono text-slate-600 font-semibold">password</span>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
