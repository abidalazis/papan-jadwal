<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Undangan;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReminderEmail;
use App\Models\SuratUndangan;
use Carbon\Carbon;

class SendReminderNotification extends Command
{
    protected $signature = 'reminder:undangan';
    protected $description = 'Kirim email pengingat H-1 untuk undangan yang akan berlangsung besok';

    public function handle()
    {
        $besok = Carbon::tomorrow()->toDateString();

        $undanganBesok = SuratUndangan::whereDate('tanggal_acara', $besok)->get();

        if ($undanganBesok->isEmpty()) {
            $this->info('Tidak ada undangan untuk besok.');
            return;
        }

        // Ganti dengan email kamu
        Mail::to('abidalazis1@gmail.com')->send(new ReminderEmail($undanganBesok));

        $this->info('Email pengingat berhasil dikirim!');
    }
}
