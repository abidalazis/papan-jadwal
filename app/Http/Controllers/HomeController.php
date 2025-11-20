<?php

namespace App\Http\Controllers;

use App\Models\SuratUndangan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil undangan besok dengan relasi penerima
        $undanganBesok = SuratUndangan::whereDate('tanggal_acara', Carbon::tomorrow())
            ->with('penerimas')
            ->orderBy('tanggal_acara')
            ->get();

        // Ambil undangan 15 hari ke belakang dan 15 hari ke depan dari hari ini
        $startDate = Carbon::today()->subDays(15);
        $endDate = Carbon::today()->addDays(15);
        
        $kalenderUndangan = SuratUndangan::whereBetween('tanggal_acara', [$startDate, $endDate])
            ->with('penerimas')
            ->orderBy('tanggal_acara')
            ->get();

        return view('welcome', compact('undanganBesok', 'kalenderUndangan', 'startDate', 'endDate'));
    }
}