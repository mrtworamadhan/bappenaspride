<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Peserta - {{ $participant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4 flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden p-6 text-center">
        <div class="mb-4">
            @if($participant->event->logo)
                <div class="flex justify-center mb-3">
                    <img src="{{ asset('storage/' . $participant->event->logo) }}" alt="Logo {{ $participant->event->name }}" class="h-14 object-contain">
                </div>
            @endif
            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $participant->attendance_status == 'present' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-850' }}">
                {{ $participant->attendance_status == 'present' ? 'Hadir' : 'Belum Hadir' }}
            </span>
            <h2 class="text-xl font-bold text-gray-800 mt-2">{{ $participant->event->name }}</h2>
            <p class="text-sm text-gray-500">Terima kasih telah mendaftar, silakan *screenshot* halaman ini.</p>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border my-4 text-left space-y-2 text-sm">
            <p><strong>Nama:</strong> {{ $participant->name }}</p>
            <p><strong>NIP:</strong> {{ $participant->nip ?? '-' }}</p>
            <p><strong>Jabatan:</strong> {{ $participant->position ?? '-' }}</p>
            <p><strong>Unit Kerja:</strong> {{ $participant->work_unit ?? '-' }}</p>
        </div>

        <!-- Barcode / QR Code menggunakan Simple Software IO QrCode -->
        <div class="my-6 flex justify-center">
            <div class="p-4 bg-white border rounded-xl shadow-sm">
                {!! QrCode::size(200)->generate($participant->uuid) !!}
            </div>
        </div>
        <p class="text-xs text-gray-500 mb-6">Tunjukkan QR Code ini kepada panitia/gatekeeper saat memasuki lokasi acara.</p>

        <a href="{{ route('participant.create') }}" class="text-sm text-blue-600 hover:underline">← Kembali ke Form Registrasi</a>
    </div>
</body>
</html>