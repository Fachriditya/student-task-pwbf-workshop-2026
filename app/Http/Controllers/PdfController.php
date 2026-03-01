<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    /**
     * Generate Sertifikat (Landscape A4)
     */
    public function certificate()
    {
        $data = [
            'name' => 'Muhammad Fachri Ditya',
            'event' => 'Workshop Menulis Kreatif',
            'date' => now()->format('d F Y'),
            'owner' => 'Andi Wijaya'
        ];

        $pdf = Pdf::loadView('pdf.certificate', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download('sertifikat.pdf');
    }

    /**
     * Generate Undangan (Portrait A4 + Header)
     */
    public function invitation()
    {
        $data = [
            'title' => 'UNDANGAN LAUNCHING BUKU',
            'content' => 'Dengan hormat, kami mengundang Anda untuk menghadiri acara launching buku terbaru kami di Toko Buku Cerdas.',
            'date' => now()->format('d F Y'),
        ];

        $pdf = Pdf::loadView('pdf.invitation', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->download('undangan.pdf');
    }
}