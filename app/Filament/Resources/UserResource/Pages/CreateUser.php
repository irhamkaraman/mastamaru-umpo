<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User created')
            ->body('The user has been created successfully.');
    }
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set email_verified_at to current time if not set
        if (empty($data['email_verified_at'])) {
            $data['email_verified_at'] = now();
        }
        
        return $data;
    }
}
