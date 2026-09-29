<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Riwayat Skor</title>
<style>body{font-family:sans-serif}table{width:100%;border-collapse:collapse}th,td{border:1px solid #333;padding:6px;text-align:left}th{background:#2E7D32;color:#fff}</style>
</head><body>
<h2>{{ $judul }}</h2>
<table><thead><tr>@foreach ($headings as $h)<th>{{ $h }}</th>@endforeach</tr></thead>
<tbody>@foreach ($rows as $row)<tr>@foreach ($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>@endforeach</tbody></table>
</body></html>
