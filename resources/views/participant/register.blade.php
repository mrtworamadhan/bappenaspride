<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - {{ $event->name }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Signature Pad CDN -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen py-10 px-4">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-lg p-6">
        <div class="text-center mb-6">
            @if($event->logo)
                <div class="flex justify-center mb-3">
                    <img src="{{ asset('storage/' . $event->logo) }}" alt="Logo {{ $event->name }}" class="h-16 object-contain">
                </div>
            @endif
            <h2 class="text-2xl font-bold text-gray-800">{{ $event->name }}</h2>
            <p class="text-sm text-gray-500">Silakan isi formulir registrasi di bawah ini</p>
        </div>
        <!-- Notifikasi Error / Peringatan -->
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm text-sm" role="alert">
                <p class="font-bold">Mohon Perhatian:</p>
                <ul class="mt-1 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm text-sm" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <form action="{{ route('participant.store') }}" method="POST" id="registrationForm">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id }}">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap *</label>
                <input type="text" name="name" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan nama lengkap">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">No HP / WhatsApp (Aktif) *</label>
                <input type="text" name="phone" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: 628123456789">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">NIP</label>
                <input type="text" name="nip" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Nomor Induk Pegawai">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Jabatan</label>
                <input type="text" name="position" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Jabatan saat ini">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Unit Kerja</label>
                <input type="text" name="work_unit" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Unit kerja / Biro / Direktorat">
            </div>

            <!-- Tanda Tangan Digital Canvas -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Tanda Tangan Digital *</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-2 bg-gray-50 flex flex-col items-center">
                    <canvas id="signature-pad" class="w-full h-40 bg-white border rounded cursor-crosshair"></canvas>
                    <button type="button" id="clear" class="mt-2 text-xs text-red-600 hover:underline font-medium">Hapus Tanda Tangan</button>
                </div>
                <textarea name="signature" id="signatureInput" class="hidden"></textarea>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                Daftar Sekarang
            </button>
        </form>
    </div>

    <script>
        const canvas = document.querySelector("#signature-pad");
        const signaturePad = new SignaturePad(canvas);

        // Atur ukuran canvas agar responsif
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); // Bersihkan saat resize
        }
        window.addEventListener("resize", resizeCanvas);
        resizeCanvas();

        document.getElementById('clear').addEventListener('click', function () {
            signaturePad.clear();
        });

        document.getElementById('registrationForm').addEventListener('submit', function (e) {
            if (signaturePad.isEmpty()) {
                alert("Mohon isi tanda tangan digital Anda terlebih dahulu!");
                e.preventDefault();
            } else {
                const dataURL = signaturePad.toDataURL();
                document.getElementById('signatureInput').value = dataURL;
            }
        });
    </script>
</body>
</html>