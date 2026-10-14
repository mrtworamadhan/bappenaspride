<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir & Tanda Tangan</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        h2 { text-align: center; margin-bottom: 5px; }
        p.subtitle { text-align: center; font-size: 9pt; color: #666; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; vertical-align: middle; }
        th { background-color: #f2f2f2; font-size: 9.5pt; text-align: center; }
        td { font-size: 9pt; }
        .signature-img { width: 90px; height: 40px; object-fit: contain; }
    </style>
</head>
<body>
    <h2>DAFTAR HADIR ACARA</h2>
    <p class="subtitle">Rekapitulasi Peserta Hadir & Tanda Tangan Digital - {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama & NIP</th>
                <th style="width: 20%;">Unit Kerja</th>
                <th style="width: 18%;">Waktu Hadir</th>
                <th style="width: 15%;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $index => $p)
            <tr>
                <td align="center">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $p->name }}</strong><br>
                    <span style="font-size: 8pt; color: #555;">NIP: {{ $p->nip ?? '-' }}</span>
                </td>
                <td>{{ $p->work_unit ?? '-' }}</td>
                <td>{{ $p->attended_at ? $p->attended_at->format('d/m/Y H:i') : '-' }}</td>
                <td align="center">
                    @if($p->signature && file_exists(storage_path('app/public/' . $p->signature)))
                        <img src="{{ storage_path('app/public/' . $p->signature) }}" class="signature-img">
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>