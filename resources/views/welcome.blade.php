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
        
        /* Mobile responsiveness */
        @media (max-width: 640px) {
            .calendar-day {
                min-height: 70px;
                font-size: 0.75rem;
            }
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: white;
            margin: 1rem;
            padding: 0;
            border-radius: 1rem;
            max-width: 500px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    
    <!-- Header dengan tombol Login -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 sm:px-6 py-3 sm:py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <i class="fas fa-envelope-open-text text-2xl sm:text-3xl text-indigo-600"></i>
                    <h1 class="text-lg sm:text-2xl font-bold text-gray-800">Sistem Undangan</h1>
                </div>
                <a href="/admin/login" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 sm:px-6 py-2 sm:py-2.5 rounded-lg text-sm sm:text-base font-semibold transition shadow-md hover:shadow-lg flex items-center space-x-1 sm:space-x-2">
                    <i class="fas fa-sign-in-alt"></i>
                    <span class="hidden sm:inline">Login Admin</span>
                    <span class="sm:hidden">Login</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-8">
        
        <!-- Undangan Besok Section -->
        @if($undanganBesok->isNotEmpty())
        <div class="mb-6 sm:mb-10">
            <div class="flex items-center space-x-2 sm:space-x-3 mb-4 sm:mb-6">
                <i class="fas fa-calendar-day text-3xl sm:text-4xl text-red-500"></i>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">📅 Undangan Besok</h2>
                    <p class="text-sm sm:text-base text-gray-600">{{ \Carbon\Carbon::tomorrow()->isoFormat('dddd, D MMMM YYYY') }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                @foreach($undanganBesok as $undangan)
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-1">
                    <!-- Header Card -->
                    <div class="bg-gradient-to-r from-red-500 to-pink-500 p-4 sm:p-5 text-white">
                        <div class="flex justify-between items-start gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs sm:text-sm font-medium opacity-90 truncate">{{ $undangan->nomor_surat }}</p>
                                <h3 class="text-lg sm:text-xl font-bold mt-1 leading-tight line-clamp-2">{{ $undangan->judul }}</h3>
                                @if($undangan->penerimas->isNotEmpty())
                                <div class="mt-2 flex items-center space-x-1">
                                    <i class="fas fa-users text-xs opacity-75"></i>
                                    <span class="text-xs opacity-90">
                                        {{ $undangan->penerimas->take(3)->pluck('nama')->join(', ') }}
                                        @if($undangan->penerimas->count() > 3)
                                        <span class="font-semibold">+{{ $undangan->penerimas->count() - 3 }} lainnya</span>
                                        @endif
                                    </span>
                                </div>
                                @endif
                            </div>
                            <span class="event-badge bg-white text-red-600 px-2 sm:px-3 py-1 rounded-full text-xs font-bold whitespace-nowrap">BESOK</span>
                        </div>
                    </div>

                    <!-- Detail Card -->
                    <div class="p-4 sm:p-5">
                        <div class="space-y-2 sm:space-y-3 mb-4">
                            <div class="flex items-center space-x-2 sm:space-x-3 text-gray-700">
                                <i class="fas fa-clock text-indigo-600 w-4 sm:w-5"></i>
                                <span class="text-sm sm:text-base font-medium">{{ \Carbon\Carbon::parse($undangan->tanggal_acara)->format('H:i') }} WIB</span>
                            </div>
                            <div class="flex items-center space-x-2 sm:space-x-3 text-gray-700">
                                <i class="fas fa-map-marker-alt text-red-600 w-4 sm:w-5"></i>
                                <span class="text-sm sm:text-base font-medium line-clamp-1">{{ $undangan->lokasi }}</span>
                            </div>
                            <div class="flex items-center space-x-2 sm:space-x-3 text-gray-700">
                                <i class="fas fa-users text-green-600 w-4 sm:w-5"></i>
                                <span class="text-sm sm:text-base font-medium">{{ $undangan->penerimas->count() }} Penerima</span>
                            </div>
                        </div>

                        <!-- Daftar Penerima -->
                        <div class="border-t pt-3 sm:pt-4">
                            <p class="text-xs sm:text-sm font-semibold text-gray-700 mb-2 sm:mb-3 flex items-center">
                                <i class="fas fa-user-check mr-2 text-indigo-600"></i>
                                Penerima Undangan:
                            </p>
                            <div class="max-h-40 sm:max-h-48 overflow-y-auto space-y-2">
                                @foreach($undangan->penerimas as $penerima)
                                <div class="flex items-center justify-between bg-gray-50 p-2 sm:p-3 rounded-lg hover:bg-gray-100 transition">
                                    <div class="flex items-center space-x-2 sm:space-x-3 min-w-0 flex-1">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                            {{ substr($penerima->nama, 0, 1) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-semibold text-gray-800 text-xs sm:text-sm truncate">{{ $penerima->nama }}</p>
                                            <p class="text-xs text-gray-600 truncate">{{ $penerima->email ?? $penerima->nomor_hp }}</p>
                                        </div>
                                    </div>
                                    @if($penerima->pivot->status_kirim)
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap ml-2">
                                        <i class="fas fa-check-circle"></i> <span class="hidden sm:inline">Terkirim</span>
                                    </span>
                                    @else
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-semibold whitespace-nowrap ml-2">
                                        <i class="fas fa-clock"></i> <span class="hidden sm:inline">Pending</span>
                                    </span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Status Kirim -->
                        <div class="mt-3 sm:mt-4 pt-3 sm:pt-4 border-t">
                            @php
                                $total = $undangan->penerimas->count();
                                $terkirim = $undangan->penerimas->where('pivot.status_kirim', true)->count();
                                $percentage = $total > 0 ? ($terkirim / $total) * 100 : 0;
                            @endphp
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs sm:text-sm font-semibold text-gray-700">Status Pengiriman</span>
                                <span class="text-xs sm:text-sm font-bold text-indigo-600">{{ $terkirim }}/{{ $total }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 sm:h-2.5">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 sm:h-2.5 rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-lg p-8 sm:p-12 text-center mb-6 sm:mb-10">
            <i class="fas fa-calendar-check text-5xl sm:text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">Tidak Ada Undangan Besok</h3>
            <p class="text-sm sm:text-base text-gray-600">Tidak ada acara yang dijadwalkan untuk besok</p>
        </div>
        @endif

        <!-- Kalender 30 Hari (15 hari ke belakang + 15 hari ke depan) -->
        <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-8">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 sm:mb-8 gap-4">
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <i class="fas fa-calendar-alt text-3xl sm:text-4xl text-indigo-600"></i>
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-gray-800">Kalender Undangan</h2>
                        <p class="text-xs sm:text-sm text-gray-600">15 hari ke belakang - 15 hari ke depan</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600"></div>
                        <span class="text-xs sm:text-sm text-gray-700 font-medium">Ada Undangan</span>
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="grid grid-cols-7 gap-1 sm:gap-3">
                <!-- Header Hari -->
                @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $hari)
                <div class="text-center font-bold text-gray-700 py-2 sm:py-3 bg-gray-100 rounded-lg text-xs sm:text-base">
                    {{ $hari }}
                </div>
                @endforeach

                <!-- Tanggal -->
                @php
                    $currentDate = clone $startDate;
                    $today = \Carbon\Carbon::today();
                    $tomorrow = \Carbon\Carbon::tomorrow();
                    
                    // Tambahkan cell kosong di awal untuk alignment hari
                    $startDayOfWeek = $currentDate->dayOfWeek;
                    for($i = 0; $i < $startDayOfWeek; $i++) {
                        echo '<div class="calendar-day"></div>';
                    }
                    
                    // Loop 30 hari
                    while($currentDate <= $endDate) {
                        $eventsOnDay = $kalenderUndangan->filter(function($undangan) use ($currentDate) {
                            return \Carbon\Carbon::parse($undangan->tanggal_acara)->isSameDay($currentDate);
                        });
                        
                        $isToday = $currentDate->isSameDay($today);
                        $isTomorrow = $currentDate->isSameDay($tomorrow);
                        $isPast = $currentDate->isPast() && !$isToday;
                @endphp
                
                <div class="calendar-day border-2 rounded-lg sm:rounded-xl p-1 sm:p-3 {{ $eventsOnDay->isNotEmpty() ? 'has-event' : 'bg-white border-gray-200' }} {{ $isToday ? 'ring-2 sm:ring-4 ring-yellow-400' : '' }} {{ $isTomorrow ? 'ring-2 sm:ring-4 ring-red-400' : '' }} {{ $isPast ? 'opacity-60' : '' }} relative cursor-pointer">
                    <div class="text-center">
                        <p class="font-bold text-sm sm:text-lg {{ $eventsOnDay->isNotEmpty() ? 'text-white' : 'text-gray-800' }}">
                            {{ $currentDate->format('d') }}
                        </p>
                        <p class="text-xs {{ $eventsOnDay->isNotEmpty() ? 'text-white opacity-75' : 'text-gray-500' }} hidden sm:block">
                            {{ $currentDate->format('M') }}
                        </p>
                        @if($eventsOnDay->isNotEmpty())
                        <div class="mt-1 sm:mt-2 space-y-1">
                            @foreach($eventsOnDay->take(2) as $event)
                            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded px-1 sm:px-2 py-0.5 sm:py-1 text-xs font-semibold truncate" title="{{ $event->judul }}">
                                <span class="hidden sm:inline">{{ Str::limit($event->judul, 15) }}</span>
                                <span class="sm:hidden">•</span>
                            </div>
                            @endforeach
                            @if($eventsOnDay->count() > 2)
                            <p class="text-xs font-semibold text-white opacity-90">+{{ $eventsOnDay->count() - 2 }}</p>
                            @endif
                        </div>
                        @endif
                        
                        @if($isToday)
                        <span class="absolute top-0.5 right-0.5 sm:top-1 sm:right-1 bg-yellow-400 text-yellow-900 text-xs px-1 sm:px-2 py-0.5 rounded-full font-bold">
                            <span class="hidden sm:inline">Hari Ini</span>
                            <span class="sm:hidden">!</span>
                        </span>
                        @endif
                        
                        @if($isTomorrow)
                        <span class="absolute top-0.5 right-0.5 sm:top-1 sm:right-1 bg-red-500 text-white text-xs px-1 sm:px-2 py-0.5 rounded-full font-bold">
                            <span class="hidden sm:inline">Besok</span>
                            <span class="sm:hidden">!</span>
                        </span>
                        @endif
                    </div>
                </div>
                
                @php
                        $currentDate->addDay();
                        
                        // Add empty cells at the end if needed to complete the week
                        if($currentDate > $endDate && $currentDate->dayOfWeek != 0) {
                            $remainingDays = 7 - $currentDate->dayOfWeek;
                            for($i = 0; $i < $remainingDays; $i++) {
                                echo '<div class="calendar-day"></div>';
                            }
                        }
                    }
                @endphp
            </div>

            <!-- Legend -->
            <div class="mt-4 sm:mt-8 pt-4 sm:pt-6 border-t flex flex-wrap gap-3 sm:gap-4 justify-center">
                <div class="flex items-center space-x-2">
                    <div class="w-5 h-5 sm:w-6 sm:h-6 rounded border-2 sm:border-4 border-yellow-400"></div>
                    <span class="text-xs sm:text-sm text-gray-700">Hari Ini</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-5 h-5 sm:w-6 sm:h-6 rounded border-2 sm:border-4 border-red-400"></div>
                    <span class="text-xs sm:text-sm text-gray-700">Besok</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-5 h-5 sm:w-6 sm:h-6 rounded bg-gradient-to-br from-indigo-500 to-purple-600"></div>
                    <span class="text-xs sm:text-sm text-gray-700">Ada Undangan</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 sm:mt-10 pb-4 sm:pb-8">
            <p class="text-sm sm:text-base text-gray-600 px-4">
                <i class="fas fa-info-circle mr-2"></i>
                Untuk mengelola undangan, silakan <a href="/admin/login" class="text-indigo-600 font-semibold hover:underline">login sebagai admin</a>
            </p>
        </div>

    </div>

</body>
</html>