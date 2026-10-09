<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0; url={{ route('login') }}">
    <title>Redirecting...</title>
</head>
<body>
    <script>window.location.href = "{{ route('login') }}";</script>
    <p>Mengalihkan ke halaman login... <a href="{{ route('login') }}">Klik di sini</a> jika tidak dialihkan otomatis.</p>
</body>
</html>