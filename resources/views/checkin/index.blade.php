<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gatekeeper Scanner - Check-in Peserta</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Html5-Qrcode Scanner CDN -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <!-- Axios untuk AJAX request -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen py-10 px-4 flex flex-col items-center">
    <div class="max-w-md w-full bg-gray-800 rounded-xl shadow-xl overflow-hidden p-6">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold">Gatekeeper Check-In</h2>
            <p class="text-sm text-gray-400">Arahkan kamera ke QR Code peserta atau masukkan UUID</p>
        </div>

        <!-- Kamera Scanner Box -->
        <div id="reader" class="w-full bg-black rounded-lg overflow-hidden mb-4"></div>

        <!-- Alert Hasil Scan -->
        <div id="alertBox" class="hidden p-4 rounded-lg text-sm mb-4 font-medium"></div>

        <!-- Input Manual UUID (Fallback jika kamera bermasalah) -->
        <form id="manualForm" class="mt-4">
            @csrf
            <label class="block text-xs text-gray-400 mb-1">Atau masukkan UUID / Scan Gun:</label>
            <div class="flex gap-2">
                <input type="text" id="uuidInput" placeholder="Paste UUID peserta..." class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" required>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg text-sm font-semibold transition">Submit</button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="/admin" class="text-xs text-blue-400 hover:underline">← Ke Panel Admin Filament</a>
        </div>
    </div>

    <script>
        const alertBox = document.getElementById('alertBox');

        // Fungsi untuk membunyikan Audio Beep menggunakan Web Audio API
        function playSound(type) {
            const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(audioCtx.destination);

            if (type === 'success') {
                // Suara Beep Tinggi (Sukses)
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(800, audioCtx.currentTime); // Frekuensi nada tinggi
                gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
                oscillator.start();
                oscillator.stop(audioCtx.currentTime + 0.15); // Durasi 150ms
            } else if (type === 'warning') {
                // Suara Beep Dua Kali Pendek (Sudah Pernah Hadir)
                oscillator.type = 'triangle';
                oscillator.frequency.setValueAtTime(400, audioCtx.currentTime);
                gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
                oscillator.start();
                oscillator.stop(audioCtx.currentTime + 0.1);
                
                setTimeout(() => {
                    const osc2 = audioCtx.createOscillator();
                    const gain2 = audioCtx.createGain();
                    osc2.connect(gain2);
                    gain2.connect(audioCtx.destination);
                    osc2.type = 'triangle';
                    osc2.frequency.setValueAtTime(400, audioCtx.currentTime);
                    gain2.gain.setValueAtTime(0.1, audioCtx.currentTime);
                    osc2.start();
                    osc2.stop(audioCtx.currentTime + 0.1);
                }, 150);
            } else {
                // Suara Beep Rendah/Kasar (Gagal / Error)
                oscillator.type = 'sawtooth';
                oscillator.frequency.setValueAtTime(150, audioCtx.currentTime); // Frekuensi rendah
                gainNode.gain.setValueAtTime(0.2, audioCtx.currentTime);
                oscillator.start();
                oscillator.stop(audioCtx.currentTime + 0.3); // Durasi 300ms
            }
        }

        function showNotification(type, message) {
            alertBox.classList.remove('hidden', 'bg-green-600', 'bg-yellow-600', 'bg-red-600');
            if (type === 'success') {
                alertBox.classList.add('bg-green-600');
                playSound('success');
            } else if (type === 'warning') {
                alertBox.classList.add('bg-yellow-600');
                playSound('warning');
            } else {
                alertBox.classList.add('bg-red-600');
                playSound('error');
            }
            alertBox.innerHTML = message;
        }

        function processCheckin(uuid) {
            axios.post('{{ route("checkin.store") }}', {
                uuid: uuid,
                _token: '{{ csrf_token() }}'
            })
            .then(response => {
                showNotification(response.data.status, response.data.message);
            })
            .catch(error => {
                const errMsg = error.response?.data?.message || 'Terjadi kesalahan pada sistem.';
                showNotification('error', errMsg);
            });
        }

        // Form Manual Submit
        document.getElementById('manualForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const uuid = document.getElementById('uuidInput').value.trim();
            if (uuid) {
                processCheckin(uuid);
                document.getElementById('uuidInput').value = '';
            }
        });

        // Inisialisasi Kamera QR Scanner
        function onScanSuccess(decodedText, decodedResult) {
            // Hentikan sementara scan agar tidak spam request
            html5QrcodeScanner.clear();
            processCheckin(decodedText);
            
            // Nyalakan kembali scanner setelah 3 detik
            setTimeout(() => {
                html5QrcodeScanner.render(onScanSuccess);
            }, 3000);
        }

        const html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { fps: 10, qrbox: 250 }, /* verbose=false */);
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>