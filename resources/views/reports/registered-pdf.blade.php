<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Peserta Terdaftar</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; color: #333; }
        h2 { text-align: center; margin-bottom: 5px; }
        p.subtitle { text-align: center; font-size: 9pt; color: #666; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; font-size: 10pt; }
        td { font-size: 9pt; }
    </style>
</head>
<body>
    <h2>REKAPITULASI PESERTA TERDAFTAR</h2>
    <p class="subtitle">Dicetak pada: {{ date('d-m-Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Lengkap</th>
                <th style="width: 15%;">No HP / WA</th>
                <th style="width: 15%;">NIP</th>
                <th style="width: 20%;">Jabatan</th>
                <th style="width: 20%;">Unit Kerja</th>
            </tr>
        </thead>
        <tbody>
            @foreach($participants as $index => $p)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->phone }}</td>
                <td>{{ $p->nip ?? '-' }}</td>
                <td>{{ $p->position ?? '-' }}</td>
                <td>{{ $p->work_unit ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>