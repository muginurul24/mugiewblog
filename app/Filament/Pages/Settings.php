<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

class Settings extends Page
{
    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?int $navigationSort = 1;

    protected static string|\UnitEnum|null $navigationGroup = 'Sistem';

    protected string $view = 'filament.pages.settings';

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->attributesToArray());
    }

    public function getTitle(): string|Htmlable
    {
        return 'Pengaturan';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'settings';
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->operation('edit')
            ->model(SiteSetting::current())
            ->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Umum')
                    ->description('Identitas yang tampil di layout publik dan metadata utama.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('Nama situs')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('site_url')
                                    ->label('URL situs')
                                    ->url()
                                    ->required()
                                    ->maxLength(255),
                            ]),
                        TextInput::make('tagline')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('site_description')
                            ->label('Deskripsi situs')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Section::make('Distribusi')
                    ->description('Kontrol endpoint publik dan modul pembaca.')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                FileUpload::make('default_og_image')
                                    ->label('Default OG image')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif'])
                                    ->disk('public')
                                    ->directory('settings/og')
                                    ->visibility('public')
                                    ->imageEditor()
                                    ->maxSize(5120)
                                    ->helperText('Upload gambar untuk preview sosial. Rasio 1200x630 direkomendasikan.'),
                                TextInput::make('contact_email')
                                    ->label('Email kontak')
                                    ->email()
                                    ->maxLength(255),
                                TextInput::make('articles_per_page')
                                    ->label('Artikel per halaman')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(6)
                                    ->maxValue(30)
                                    ->required(),
                            ]),
                        Grid::make(3)
                            ->schema([
                                Toggle::make('sitemap_enabled')
                                    ->label('Sitemap aktif')
                                    ->required(),
                                Toggle::make('rss_enabled')
                                    ->label('RSS aktif')
                                    ->required(),
                                Toggle::make('newsletter_enabled')
                                    ->label('Newsletter aktif')
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public function save(): void
    {
        SiteSetting::current()->update($this->form->getState());
        SiteSetting::forgetCurrent();

        Notification::make()
            ->success()
            ->title('Pengaturan tersimpan')
            ->send();

        $this->form->fill(SiteSetting::current()->attributesToArray());
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([
                    EmbeddedSchema::make('form'),
                ])
                    ->id('settings-form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Simpan pengaturan')
                                ->submit('save')
                                ->keyBindings(['mod+s']),
                        ])
                            ->alignment(Alignment::End),
                    ]),
            ]);
    }
}
