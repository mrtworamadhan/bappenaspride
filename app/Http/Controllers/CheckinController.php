<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Http\Request;

class CheckinController extends Controller
{
    // Menampilkan Halaman Scanner / Check-in untuk Panitia
    public function index()
    {
        $events = Event::where('is_active', true)->get();
        return view('checkin.index', compact('events'));
    }

    // Proses Check-in berdasarkan UUID Barcode peserta
    public function store(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string|exists:participants,uuid',
        ]);

        $participant = Participant::where('uuid', $request->uuid)->first();

        if (!$participant) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data peserta tidak ditemukan!'
            ], 404);
        }

        // Cek apakah sudah pernah hadir sebelumnya
        if ($participant->attendance_status === 'present') {
            return response()->json([
                'status' => 'warning',
                'message' => "Peserta atas nama *{$participant->name}* sudah melakukan check-in sebelumnya pada " . $participant->attended_at->format('d/m/Y H:i') . ".",
                'participant' => $participant
            ]);
        }

        // Ubah status jadi hadir
        $participant->update([
            'attendance_status' => 'present',
            'attended_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Check-in berhasil! Selamat datang, *{$participant->name}* ({$participant->work_unit}).",
            'participant' => $participant
        ]);
    }
}