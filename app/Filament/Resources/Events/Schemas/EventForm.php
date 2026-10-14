<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Acara'),
                FileUpload::make('logo')
                    ->image()
                    ->disk('public')
                    ->directory('event-logos')
                    ->label('Logo Acara'),
                DatePicker::make('event_date')
                    ->label('Tanggal Acara'),
                TextInput::make('location')
                    ->maxLength(255)
                    ->label('Lokasi'),
                FileUpload::make('certificate_background')
                    ->image()
                    ->directory('certificate-backgrounds')
                    ->label('Background E-Certificate (Format Gambar)'),
                Toggle::make('is_active')
                    ->required()
                    ->default(true)
                    ->label('Status Aktif Acara'),
            ]);
    }
}
