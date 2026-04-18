<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة تحكم مكتب الاستقدام')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Cairo', 'ui-sans-serif', 'system-ui', 'sans-serif'] } } }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        *, body { font-family: 'Cairo', sans-serif !important; }
        body { background: #f0f4f9; }

        /* ── Sidebar ──────────────────────────────── */
        .sidebar { background: linear-gradient(180deg, #0d1117 0%, #161b22 100%); width: 260px; }

        .nav-label {
            font-size: 10px; font-weight: 700; letter-spacing: .1em;
            text-transform: uppercase; color: #3d444d;
            padding: 4px 14px 6px; display: block;
        }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 14px; border-radius: 10px;
            color: #8b949e; font-size: 13.5px; font-weight: 500;
            transition: all .18s; text-decoration: none; margin-bottom: 2px;
        }
        .nav-item:hover  { background: rgba(255,255,255,.06); color: #e6edf3; }
        .nav-item.active { background: #10b981; color: #fff; font-weight: 600; box-shadow: 0 4px 15px rgba(16,185,129,.35); }
        .nav-item svg    { flex-shrink: 0; opacity: .75; }
        .nav-item.active svg { opacity: 1; }

        .nav-divider { border-top: 1px solid rgba(255,255,255,.06); margin: 10px 0; }

        /* ── Cards ────────────────────────────────── */
        .stat-card { transition: all .2s ease; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0,0,0,.1) !important; }

        /* ── Scrollbar ────────────────────────────── */
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .sidebar::-webkit-scrollbar-thumb { background: #21262d; }
    </style>
</head>
<body class="min-h-screen">
<div class="flex min-h-screen">

    {{-- ═══ MAIN CONTENT ═══ --}}
    <main class="flex-1 min-w-0 overflow-y-auto" style="margin-right:260px;">
        {{-- Top bar --}}
        <div class="bg-white border-b border-gray-100 px-6 py-3 flex items-center justify-between sticky top-0 z-30" style="box-shadow:0 1px 8px rgba(0,0,0,.05);">
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400">
                    {{ \Carbon\Carbon::now()->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
                </span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center">
                    <span class="text-white text-sm font-bold">{{ mb_substr(auth()->user()?->name ?? 'م', 0, 1) }}</span>
                </div>
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-semibold text-gray-800 leading-tight">{{ auth()->user()?->name ?? 'صاحب الشركة' }}</p>
                    <p class="text-xs text-gray-400">صاحب الشركة</p>
                </div>
            </div>
        </div>
        <div class="p-5">
            @yield('content')
        </div>
    </main>

    {{-- ═══ SIDEBAR (right side — RTL) ═══ --}}
    <aside class="sidebar fixed top-0 right-0 bottom-0 flex flex-col z-40 overflow-y-auto">

        {{-- Brand --}}
        <div class="p-5 flex items-center gap-3 justify-end" style="border-bottom:1px solid rgba(255,255,255,.06);">
            <div class="text-right">
                <h1 class="text-white font-bold text-base leading-tight">مكتب الاستقدام</h1>
                <p class="text-xs mt-0.5" style="color:#8b949e;">لوحة تحكم مبسطة وسهلة</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center flex-shrink-0" style="box-shadow:0 4px 12px rgba(16,185,129,.4);">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4">
            <span class="nav-label">الرئيسية</span>

            <a href="{{ route('owner.dashboard') }}" class="nav-item active">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                <span>الرئيسية</span>
            </a>

            <div class="nav-divider"></div>
            <span class="nav-label">إدارة الطلبات</span>

            <a href="{{ url('/admin/recruitment/recruitment-contracts') }}" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>الطلبات</span>
            </a>

            <a href="{{ url('/admin/h-r/employees') }}" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>الموارد البشرية</span>
            </a>

            <a href="{{ url('/admin/rental/rental-contracts') }}" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span>العقود</span>
            </a>

            <div class="nav-divider"></div>
            <span class="nav-label">المالية والشكاوى</span>

            <a href="{{ url('/admin/finance/branch-transactions') }}" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>المالية</span>
            </a>

            <a href="{{ url('/admin/complaints') }}" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span>الشكاوى</span>
            </a>

            <a href="{{ url('/admin/main-core/branches') }}" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>الفروع</span>
            </a>

            <div class="nav-divider"></div>
            <span class="nav-label">أخرى</span>

            <a href="{{ route('filament.admin.pages.dashboard') }}" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span>التقارير</span>
            </a>

            <a href="{{ url('/admin') }}" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>الإعدادات</span>
            </a>
        </nav>

        {{-- User footer --}}
        <div class="p-4 mx-3 mb-3 rounded-xl" style="background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.06);">
            <div class="flex items-center gap-3 justify-end">
                <div class="text-right min-w-0">
                    <p class="text-white text-sm font-semibold truncate">{{ auth()->user()?->name ?? 'صاحب الشركة' }}</p>
                    <p class="text-xs" style="color:#8b949e;">صاحب الشركة</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-emerald-500 flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-sm font-bold">{{ mb_substr(auth()->user()?->name ?? 'م', 0, 1) }}</span>
                </div>
            </div>
        </div>

    </aside>
</div>
</body>
</html>
