<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    // Download PDF Daftar Peserta Terdaftar
    public function downloadRegisteredPdf()
    {
        $participants = Participant::with('event')->get();
        
        $pdf = Pdf::loadView('reports.registered-pdf', compact('participants'));
        $pdf->setPaper('a4', 'landscape'); // Format lanskap agar tabel luas

        return $pdf->download('daftar-peserta-terdaftar-' . date('Y-m-d') . '.pdf');
    }

    // Download PDF Daftar Hadir (Lengkap dengan TTD Digital)
    public function downloadAttendancePdf()
    {
        ini_set('max_execution_time', 300); // 5 menit
        ini_set('memory_limit', '512M');
        
        $participants = Participant::with('event')
                        ->where('attendance_status', 'present')
                        ->get();
        
        $pdf = Pdf::loadView('reports.attendance-pdf', compact('participants'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('daftar-hadir-peserta-' . date('Y-m-d') . '.pdf');
    }
}