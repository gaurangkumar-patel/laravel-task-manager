<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Task Manager')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <main class="page-shell">
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <span>Built by Gaurang Patel</span>
            <span class="footer-separator" aria-hidden="true">·</span>
            <a href="https://www.linkedin.com/in/gaurangpatel2326" target="_blank" rel="noopener noreferrer">LinkedIn</a>
            <span class="footer-separator" aria-hidden="true">·</span>
            <a href="https://github.com/gaurangkumar-patel" target="_blank" rel="noopener noreferrer">GitHub</a>
        </div>
    </footer>

    <script src="{{ asset('js/tasks.js') }}" defer></script>
</body>
</html>
