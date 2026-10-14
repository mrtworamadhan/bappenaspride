<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    // Generate dan Download E-Certificate untuk Peserta
    public function download($uuid)
    {
        $participant = Participant::with('event')->where('uuid', $uuid)->firstOrFail();

        // Pastikan peserta sudah hadir baru bisa ambil sertifikat
        if ($participant->attendance_status !== 'present') {
            return redirect()->route('participant.show', $uuid)
                             ->with('error', 'E-Certificate belum dapat diunduh karena status kehadiran Anda belum tercatat.');
        }

        $event = $participant->event;

        if (!$event->certificate_background || !Storage::disk('public')->exists($event->certificate_background)) {
            return back()->with('error', 'Background sertifikat belum diatur oleh panitia.');
        }

        // Path file background asli di storage
        $bgPath = Storage::disk('public')->path($event->certificate_background);
        
        // Deteksi ekstensi gambar (jpg/png)
        $info = getimagesize($bgPath);
        $mime = $info['mime'];

        if ($mime == 'image/jpeg' || $mime == 'image/jpg') {
            $image = imagecreatefromjpeg($bgPath);
        } elseif ($mime == 'image/png') {
            $image = imagecreatefrompng($bgPath);
        } else {
            return back()->with('error', 'Format background sertifikat tidak didukung.');
        }

        $width = imagesx($image);
        $height = imagesy($image);

        // Warna teks (Hitam pekat atau kustom, misal RGB: 30, 41, 59)
        $textColor = imagecolorallocate($image, 30, 41, 59);

        // Cari file font TTF (kita gunakan font bawaan sistem atau letakkan file .ttf di storage/fonts/font.ttf)
        // Jika tidak ada custom font, kita bisa pakai imagestring bawaan (kurang rapi) atau arahkan ke file ttf
        $fontPath = public_path('fonts/arial.ttf'); // Pastikan nanti sediakan file font atau pakai built-in
        
        $name = strtoupper($participant->name);

        if (file_exists($fontPath)) {
            // Menggunakan TrueType Font (TTF) agar teks bisa diatur ukurannya & diletakkan pas di tengah
            $fontSize = 45; // Ukuran font
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $name);
            $textWidth = abs($bbox[4] - $bbox[0]);
            
            // Posisi X di tengah (Center Align)
            $x = ($width - $textWidth) / 2;
            $y = ($height / 2) + 20; // Sesuaikan posisi vertikal (tengah agak ke bawah)

            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontPath, $name);
        } else {
            // Fallback jika file TTF belum ada (menggunakan font bawaan GD ukuran 5)
            // Letakkan teks di tengah perkiraan
            $x = ($width / 2) - (strlen($name) * 4);
            $y = $height / 2;
            imagestring($image, 5, $x, $y, $name, $textColor);
        }

        // Output gambar ke browser untuk di-download
        $fileName = 'E-Certificate-' . str_replace(' ', '-', $participant->name) . '.jpg';
        
        return response()->streamDownload(function () use ($image, $mime) {
            if ($mime == 'image/png') {
                imagepng($image);
            } else {
                imagejpeg($image, NULL, 90);
            }
            imagedestroy($image);
        }, $fileName, [
            'Content-Type' => $mime,
        ]);
    }
}