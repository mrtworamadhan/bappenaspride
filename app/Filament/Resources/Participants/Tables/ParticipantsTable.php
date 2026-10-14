<?php

namespace App\Filament\Resources\Participants\Tables;

use App\Models\Participant;
use App\Services\FonnteService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf; // Jika nanti mau export PDF, atau kita buat CSV/Excel bawaan PHP
use Illuminate\Support\Facades\Response;

class ParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Peserta'),
                TextColumn::make('phone')
                    ->searchable()
                    ->label('No HP'),
                TextColumn::make('nip')
                    ->searchable()
                    ->label('NIP'),
                TextColumn::make('work_unit')
                    ->searchable()
                    ->label('Unit Kerja'),
                BadgeColumn::make('attendance_status')
                    ->colors([
                        'danger' => 'pending',
                        'success' => 'present',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Belum Hadir',
                        'present' => 'Hadir',
                        default => $state,
                    })
                    ->label('Status'),
                ImageColumn::make('signature')
                    ->disk('public')
                    ->label('TTD Digital'),
                TextColumn::make('attended_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Waktu Hadir'),
            ])
            ->filters([
                SelectFilter::make('attendance_status')
                    ->options([
                        'pending' => 'Belum Hadir',
                        'present' => 'Hadir',
                    ])
                    ->label('Filter Status'),
                SelectFilter::make('event_id')
                    ->relationship('event', 'name')
                    ->label('Filter Acara'),
            ])
            ->headerActions([
                Action::make('exportParticipantsPdf')
                    ->label('Download Daftar Terdaftar (PDF)')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn () => route('report.registered.pdf'))
                    ->openUrlInNewTab(),

                Action::make('exportAttendancePdf')
                    ->label('Download Daftar Hadir & TTD (PDF)')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn () => route('report.attendance.pdf'))
                    ->openUrlInNewTab(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('blastCertificate')
                        ->label('Blast E-Certificate WhatsApp')
                        ->icon('heroicon-o-paper-airplane')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $fonnte = new FonnteService();
                            $successCount = 0;

                            foreach ($records as $participant) {
                                // Hanya kirim ke peserta yang statusnya 'present' (hadir)
                                if ($participant->attendance_status === 'present') {
                                    $certUrl = route('certificate.download', $participant->uuid);
                                    $eventName = $participant->event->name ?? 'Acara';

                                    $message = "Halo *{$participant->name}*,\n\n";
                                    $message .= "Terima kasih telah berpartisipasi dalam acara *{$eventName}*.\n\n";
                                    $message .= "Sertifikat keikutsertaan Anda sudah dapat diunduh melalui tautan di bawah ini:\n";
                                    $message .= "{$certUrl}\n\n";
                                    $message .= "Sampai jumpa di acara berikutnya!\n";
                                    $message .= "Panitia {$eventName}";

                                    $fonnte->sendWhatsApp($participant->phone, $message);
                                    $successCount++;
                                }
                            }

                            Notification::make()
                                ->title("Berhasil mengirim {$successCount} E-Certificate via WhatsApp!")
                                ->success()
                                ->send();
                        })
                ]),
            ]);
    }
}
