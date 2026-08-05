<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CreditPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-heart';
    
    protected static string $view = 'filament.pages.credit-page';
    
    protected static ?string $title = 'Tentang Sistem';
    
    protected static ?string $navigationLabel = 'Credit';
    
    protected static bool $shouldRegisterNavigation = false;
    
    public function getTitle(): string
    {
        return 'Tentang Sistem Presensi MASTAUMPO 2025';
    }
    
    public function getHeading(): string
    {
        return 'Tentang Sistem Presensi MASTAUMPO 2025';
    }
}