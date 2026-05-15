<?php

namespace App\Filament\Resources\NewsletterSubscribers\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class NewsletterSubscriberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->schema([
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('name')
                            ->maxLength(255),
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'subscribed' => 'Subscribed',
                                'unsubscribed' => 'Unsubscribed',
                            ])
                            ->default('pending')
                            ->required(),
                        TextInput::make('source')
                            ->maxLength(255),
                    ])
                    ->columnSpanFull(),
                Grid::make(3)
                    ->schema([
                        DateTimePicker::make('verified_at')
                            ->seconds(false),
                        DateTimePicker::make('subscribed_at')
                            ->seconds(false),
                        DateTimePicker::make('unsubscribed_at')
                            ->seconds(false),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
