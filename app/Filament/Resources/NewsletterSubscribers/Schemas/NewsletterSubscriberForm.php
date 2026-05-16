<?php

namespace App\Filament\Resources\NewsletterSubscribers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsletterSubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscriber')
                    ->description('Identitas subscriber dan sumber akuisisi.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('email')
                                    ->label('Alamat email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                                TextInput::make('name')
                                    ->label('Nama')
                                    ->maxLength(255),
                                Select::make('status')
                                    ->options([
                                        'pending' => 'Menunggu',
                                        'subscribed' => 'Aktif',
                                        'unsubscribed' => 'Berhenti',
                                    ])
                                    ->default('pending')
                                    ->required(),
                                TextInput::make('source')
                                    ->label('Sumber')
                                    ->maxLength(255),
                            ]),
                    ]),
                Section::make('Siklus hidup')
                    ->description('Jejak verifikasi, langganan, dan berhenti langganan.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                DateTimePicker::make('verified_at')
                                    ->label('Diverifikasi pada')
                                    ->seconds(false),
                                DateTimePicker::make('subscribed_at')
                                    ->label('Aktif sejak')
                                    ->seconds(false),
                                DateTimePicker::make('unsubscribed_at')
                                    ->label('Berhenti pada')
                                    ->seconds(false),
                            ]),
                    ]),
            ]);
    }
}
