<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Perpustakaan Tiga Serangkai')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user-layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user-theme.css') }}">

    {{-- Anti-flash dark mode initializer --}}
    <script>
        (function() {
            var theme = localStorage.getItem('lib_theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    @stack('styles')
</head>
<body>

<div class="app">
    @hasSection('navbar')
        @yield('navbar')
    @else
        @include('partials.user-navbar')
    @endif

    <main class="content ul-content @yield('content_class')">
        @if(session('success'))
        <div class="ul-flash ul-flash-success" id="ulFlashSuccess">
            ✓ {{ session('success') }}
            <button onclick="this.parentElement.remove()">×</button>
        </div>
        @endif
        @if(session('error'))
        <div class="ul-flash ul-flash-danger" id="ulFlashError">
            ⚠ {{ session('error') }}
            <button onclick="this.parentElement.remove()">×</button>
        </div>
        @endif

        @yield('content')
    </main>

    @include('partials.footer')
</div>

{{-- Toast container --}}
<div id="ulToastContainer"></div>

<script src="{{ asset('js/app.js') }}"></script>
<script>
// ── Auto-hide flash ───────────────────────────────────────
setTimeout(() => {
    document.getElementById('ulFlashSuccess')?.remove();
    document.getElementById('ulFlashError')?.remove();
}, 4000);

// ── Toast ─────────────────────────────────────────────────
window.showToast = function(message, type = 'info') {
    const container = document.getElementById('ulToastContainer');
    if (!container) return;
    const icons = {
        success: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        error:   '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>',
        info:    '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/>',
        warning: '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>',
    };
    const toast = document.createElement('div');
    toast.className = `ul-toast ul-toast-${type}`;
    toast.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">${icons[type]||icons.info}</svg><span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; toast.style.transform = 'translateX(20px)'; setTimeout(() => toast.remove(), 250); }, 3200);
};

// ── Favorite toggle ───────────────────────────────────────
window.toggleFavorite = function(bookId, btn) {
    fetch('/user/favorites/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
        body: JSON.stringify({ book_id: bookId }),
    })
    .then(r => r.json())
    .then(data => {
        if (btn) {
            btn.classList.toggle('favorited', data.favorited);
            const svg = btn.querySelector('svg');
            if (svg) svg.setAttribute('fill', data.favorited ? 'currentColor' : 'none');
        }
        showToast(data.message, data.favorited ? 'success' : 'info');
    })
    .catch(() => showToast('Gagal mengubah favorit', 'error'));
};
</script>

@stack('scripts')
</body>
</html>
