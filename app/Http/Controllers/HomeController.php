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

        // Ambil semua undangan bulan ini untuk kalender
        $kalenderUndangan = SuratUndangan::whereMonth('tanggal_acara', Carbon::now()->month)
            ->whereYear('tanggal_acara', Carbon::now()->year)
            ->orderBy('tanggal_acara')
            ->get();

        return view('welcome', compact('undanganBesok', 'kalenderUndangan'));
    }
}