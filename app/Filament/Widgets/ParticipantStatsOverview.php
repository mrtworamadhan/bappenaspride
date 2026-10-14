<?php

namespace App\Filament\Widgets;

use App\Models\Participant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ParticipantStatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = '5s'; // Auto-refresh setiap 5 detik untuk monitoring real-time

    protected function getStats(): array
    {
        $totalRegistered = Participant::count();
        $totalPresent = Participant::where('attendance_status', 'present')->count();
        $totalPending = Participant::where('attendance_status', 'pending')->count();

        return [
            Stat::make('Total Peserta Terdaftar', $totalRegistered)
                ->description('Semua peserta yang sudah registrasi')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Total Peserta Hadir', $totalPresent)
                ->description('Sudah melakukan check-in')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Peserta Belum Hadir', $totalPending)
                ->description('Belum melakukan check-in')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}