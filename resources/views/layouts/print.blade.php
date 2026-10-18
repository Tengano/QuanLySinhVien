<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Báo cáo')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, "DejaVu Sans", sans-serif; font-size: 12px; color: #111; margin: 24px; }
        h1 { font-size: 20px; margin: 0 0 4px; text-align: center; text-transform: uppercase; }
        h2 { font-size: 15px; margin: 18px 0 8px; }
        .muted { color: #555; }
        .header { text-align: center; margin-bottom: 18px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 24px; margin: 12px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #777; padding: 5px 6px; text-align: left; }
        th { background: #eee; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .text-right { text-align: right; }
        .summary { margin-top: 12px; font-weight: bold; }
        .actions { margin: 16px 0; text-align: right; }
        .actions button { padding: 8px 14px; cursor: pointer; }
        @media print {
            .actions { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button onclick="window.print()">In / Lưu PDF</button>
    </div>

    @yield('content')

    <p class="muted" style="margin-top: 24px; font-size: 10px;">In từ Hệ thống Quản lý Sinh viên — {{ now()->format('d/m/Y H:i') }}</p>
</body>
</html>
