<?php

namespace App\Filament\Resources\Participants\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required()
                    ->label('Pilih Acara'),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Lengkap'),
                TextInput::make('phone')
                    ->required()
                    ->tel()
                    ->maxLength(20)
                    ->label('No HP / WhatsApp (cth: 628xxx)'),
                TextInput::make('nip')
                    ->maxLength(50)
                    ->label('NIP'),
                TextInput::make('position')
                    ->maxLength(255)
                    ->label('Jabatan'),
                TextInput::make('work_unit')
                    ->maxLength(255)
                    ->label('Unit Kerja'),
                Select::make('attendance_status')
                    ->options([
                        'pending' => 'Belum Hadir',
                        'present' => 'Hadir',
                    ])
                    ->required()
                    ->default('pending')
                    ->label('Status Kehadiran'),
            ]);
    }
}
