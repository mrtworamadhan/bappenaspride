<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\FonnteService;

class ParticipantController extends Controller
{
    // Menampilkan Form Registrasi untuk Event yang aktif
    public function create()
    {
        // Ambil event yang sedang aktif (misal event pertama yang is_active = true)
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return view('participant.no-event');
        }

        return view('participant.register', compact('event'));
    }

    // Menyimpan Data Registrasi & Tanda Tangan Digital
    public function store(Request $request, FonnteService $fonnteService)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                \Illuminate\Validation\Rule::unique('participants')->where(function ($query) use ($request) {
                    return $query->where('event_id', $request->event_id);
                }),
            ],
            'nip' => 'nullable|string|max:50',
            'position' => 'nullable|string|max:255',
            'work_unit' => 'nullable|string|max:255',
            'signature' => 'required',
        ], [
            'phone.unique' => 'Nomor HP / WhatsApp ini sudah terdaftar untuk acara tersebut.',
            'signature.required' => 'Tanda tangan digital wajib diisi.',
        ]);

        try {
            // Proses Tanda Tangan
            $signatureData = $request->input('signature');
            $imageParts = explode(';', $signatureData);
            $imageExpo = explode(',', $imageParts[1]);
            $decodedImage = base64_decode($imageExpo[1]);
            
            $filename = 'signatures/' . Str::uuid() . '.png';
            Storage::disk('public')->put($filename, $decodedImage);

            // Simpan Peserta
            $participant = Participant::create([
                'event_id' => $request->event_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'nip' => $request->nip,
                'position' => $request->position,
                'work_unit' => $request->work_unit,
                'signature' => $filename,
                'attendance_status' => 'pending',
            ]);

            // Buat Link Barcode Unik untuk dikirim via WA
            $barcodeUrl = route('participant.show', $participant->uuid);
            $eventName = $participant->event->name;

            // Susun Pesan WhatsApp
            $message = "Halo *{$participant->name}*,\n\n";
            $message .= "Terima kasih telah mendaftar pada acara *{$eventName}*.\n\n";
            $message .= "Pendaftaran Anda berhasil. Silakan akses tautan di bawah ini untuk melihat Kartu Barcode Akses Masuk Anda:\n";
            $message .= "{$barcodeUrl}\n\n";
            $message .= "_Mohon simpan atau screenshot halaman tersebut._\n\n";
            $message .= "Salam,\nPanitia {$eventName}";

            // Kirim WhatsApp via Fonnte
            $fonnteService->sendWhatsApp($participant->phone, $message);

            return redirect()->route('participant.show', $participant->uuid)
                             ->with('success', 'Registrasi berhasil dan notifikasi WhatsApp telah dikirim!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    // Menampilkan Halaman Barcode & Info Peserta
    public function show($uuid)
    {
        $participant = Participant::with('event')->where('uuid', $uuid)->firstOrFail();

        return view('participant.show', compact('participant'));
    }
}