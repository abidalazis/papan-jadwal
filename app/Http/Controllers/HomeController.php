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
        // PERBAIKAN: Gunakan selectRaw untuk extract date dari datetime
        $kalenderUndangan = SuratUndangan::whereMonth('tanggal_acara', Carbon::now()->month)
            ->whereYear('tanggal_acara', Carbon::now()->year)
            ->with('penerimas')
            ->orderBy('tanggal_acara')
            ->get()
            ->map(function($undangan) {
                // Tambahkan property tanggal_only untuk memudahkan perbandingan
                $undangan->tanggal_only = Carbon::parse($undangan->tanggal_acara)->format('Y-m-d');
                return $undangan;
            });

        return view('welcome', compact('undanganBesok', 'kalenderUndangan'));
    }
}