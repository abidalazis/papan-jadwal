@component('mail::message')
# 📅 Pengingat Undangan untuk Besok

Halo Admin 👋  
Berikut daftar undangan yang akan berlangsung **besok ({{ now()->addDay()->format('d M Y') }})**:

@foreach ($undangan as $u)
---

**Judul:** {{ $u->judul }}  
**Tanggal:** {{ \Carbon\Carbon::parse($u->tanggal_acara)->format('d M Y') }}  
**Lokasi:** {{ $u->lokasi ?? '-' }}

@component('mail::button', ['url' => url('/admin/surat-undangans/' . $u->id)])
Lihat Detail di Sistem
@endcomponent

@endforeach

Terima kasih,  
**Sistem Papan Jadwal Surat**
@endcomponent
