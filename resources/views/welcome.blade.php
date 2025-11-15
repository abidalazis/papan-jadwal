<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Undangan - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .calendar-day {
            min-height: 80px;
            transition: all 0.3s;
        }
        .calendar-day:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .has-event {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .event-badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body class="bg-linear-to-r from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    
    <!-- Header dengan tombol Login -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-envelope-open-text text-3xl text-indigo-600"></i>
                    <h1 class="text-2xl font-bold text-gray-800">Sistem Undangan</h1>
                </div>
                <a href="/admin/login" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-semibold transition shadow-md hover:shadow-lg flex items-center space-x-2">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login Admin</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8">
        
        <!-- Undangan Besok Section -->
        @if($undanganBesok->isNotEmpty())
        <div class="mb-10">
            <div class="flex items-center space-x-3 mb-6">
                <i class="fas fa-calendar-day text-4xl text-red-500"></i>
                <div>
                    <h2 class="text-3xl font-bold text-gray-800">📅 Undangan Besok</h2>
                    <p class="text-gray-600">{{ \Carbon\Carbon::tomorrow()->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($undanganBesok as $undangan)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-1">
                    <!-- Header Card -->
                    <div class="bg-linear-to-r from-red-500 to-pink-500 p-5 text-black">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-sm font-medium opacity-90">{{ $undangan->nomor_surat }}</p>
                                <h3 class="text-xl font-bold mt-1 leading-tight">{{ $undangan->judul }}</h3>
                            </div>
                            <span class="event-badge bg-white text-red-600 px-3 py-1 rounded-full text-xs font-bold">BESOK</span>
                        </div>
                    </div>

                    <!-- Detail Card -->
                    <div class="p-5">
                        <div class="space-y-3 mb-4">
                            <div class="flex items-center space-x-3 text-gray-700">
                                <i class="fas fa-clock text-indigo-600 w-5"></i>
                                <span class="font-medium">{{ \Carbon\Carbon::parse($undangan->tanggal_acara)->format('H:i') }} WIB</span>
                            </div>
                            <div class="flex items-center space-x-3 text-gray-700">
                                <i class="fas fa-map-marker-alt text-red-600 w-5"></i>
                                <span class="font-medium">{{ $undangan->lokasi }}</span>
                            </div>
                            <div class="flex items-center space-x-3 text-gray-700">
                                <i class="fas fa-users text-green-600 w-5"></i>
                                <span class="font-medium">{{ $undangan->penerimas->count() }} Penerima</span>
                            </div>
                        </div>

                        <!-- Daftar Penerima -->
                        <div class="border-t pt-4">
                            <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <i class="fas fa-user-check mr-2 text-indigo-600"></i>
                                Penerima Undangan:
                            </p>
                            <div class="max-h-48 overflow-y-auto space-y-2">
                                @foreach($undangan->penerimas as $penerima)
                                <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full bg-linear-to-r from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold">
                                            {{ substr($penerima->nama, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">{{ $penerima->nama }}</p>
                                            <p class="text-xs text-gray-600">{{ $penerima->email ?? $penerima->no_telp }}</p>
                                        </div>
                                    </div>
                                    @if($penerima->pivot->status_kirim)
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold">
                                        <i class="fas fa-check-circle"></i> Terkirim
                                    </span>
                                    @else
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-semibold">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Status Kirim -->
                        <div class="mt-4 pt-4 border-t">
                            @php
                                $total = $undangan->penerimas->count();
                                $terkirim = $undangan->penerimas->where('pivot.status_kirim', true)->count();
                                $percentage = $total > 0 ? ($terkirim / $total) * 100 : 0;
                            @endphp
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-semibold text-gray-700">Status Pengiriman</span>
                                <span class="text-sm font-bold text-indigo-600">{{ $terkirim }}/{{ $total }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-linear-to-r from-green-400 to-green-600 h-2.5 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-lg p-12 text-center mb-10">
            <i class="fas fa-calendar-check text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Tidak Ada Undangan Besok</h3>
            <p class="text-gray-600">Tidak ada acara yang dijadwalkan untuk besok</p>
        </div>
        @endif

        <!-- Kalender Bulan Ini -->
        <!-- Kalender Bulan Ini -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-calendar-alt text-4xl text-indigo-600"></i>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800">Kalender Undangan</h2>
                        <p class="text-gray-600">{{ \Carbon\Carbon::now()->isoFormat('MMMM YYYY') }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-4 h-4 rounded-full bg-linear-to-br from-indigo-500 to-purple-600"></div>
                        <span class="text-sm text-gray-700 font-medium">Ada Undangan</span>
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-3">
                <!-- Header Hari -->
                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $hari)
                <div class="text-center font-bold text-gray-700 py-3 bg-gray-100 rounded-lg">
                    {{ $hari }}
                </div>
                @endforeach

                <!-- Tanggal -->
                @php
                    $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
                    $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
                    $startDay = $startOfMonth->dayOfWeek; // 0 = Minggu
                    $daysInMonth = $startOfMonth->daysInMonth;
                    
                    // Empty cells before first day
                    for($i = 0; $i < $startDay; $i++) {
                        echo '<div class="calendar-day"></div>';
                    }
                    
                    // Days with events
                    for($day = 1; $day <= $daysInMonth; $day++) {
                        $currentDate = \Carbon\Carbon::now()->startOfMonth()->addDays($day - 1);
                        $currentDateString = $currentDate->format('Y-m-d');
                        
                        // PERBAIKAN: Filter berdasarkan tanggal_only yang sudah kita buat di controller
                        $eventsOnDay = $kalenderUndangan->filter(function($undangan) use ($currentDateString) {
                            return $undangan->tanggal_only === $currentDateString;
                        });
                        
                        $isToday = $currentDate->isToday();
                        $isTomorrow = $currentDate->isTomorrow();
                @endphp
                
                <div class="calendar-day border-2 rounded-xl p-3 {{ $eventsOnDay->isNotEmpty() ? 'has-event' : 'bg-white border-gray-200' }} {{ $isToday ? 'ring-4 ring-yellow-400' : '' }} {{ $isTomorrow ? 'ring-4 ring-red-400' : '' }} relative cursor-pointer">
                    <div class="text-center">
                        <p class="font-bold text-lg {{ $eventsOnDay->isNotEmpty() ? 'text-white' : 'text-gray-800' }}">
                            {{ $day }}
                        </p>
                        @if($eventsOnDay->isNotEmpty())
                        <div class="mt-2 space-y-1">
                            @foreach($eventsOnDay->take(2) as $event)
                            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded px-2 py-1 text-xs font-semibold truncate" title="{{ $event->judul }}">
                                {{ Str::limit($event->judul, 15) }}
                            </div>
                            @endforeach
                            @if($eventsOnDay->count() > 2)
                            <p class="text-xs font-semibold text-white opacity-90">+{{ $eventsOnDay->count() - 2 }} lainnya</p>
                            @endif
                        </div>
                        @endif
                        
                        @if($isToday)
                        <span class="absolute top-1 right-1 bg-yellow-400 text-yellow-900 text-xs px-2 py-0.5 rounded-full font-bold">Hari Ini</span>
                        @endif
                        
                        @if($isTomorrow)
                        <span class="absolute top-1 right-1 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">Besok</span>
                        @endif
                    </div>
                </div>
                
                @php
                    }
                @endphp
            </div>

            <!-- Legend -->
            <div class="mt-8 pt-6 border-t flex flex-wrap gap-4 justify-center">
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 rounded border-4 border-yellow-400"></div>
                    <span class="text-sm text-gray-700">Hari Ini</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 rounded border-4 border-red-400"></div>
                    <span class="text-sm text-gray-700">Besok</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-6 h-6 rounded bg-linear-to-br from-indigo-500 to-purple-600"></div>
                    <span class="text-sm text-gray-700">Tanggal Ada Undangan</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-10 pb-8">
            <p class="text-gray-600">
                <i class="fas fa-info-circle mr-2"></i>
                Untuk mengelola undangan, silakan <a href="/admin/login" class="text-indigo-600 font-semibold hover:underline">login sebagai admin</a>
            </p>
        </div>

    </div>

</body>
</html>