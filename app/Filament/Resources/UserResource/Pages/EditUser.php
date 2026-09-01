<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('verify_email')
                ->label('Verify Email')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->action(function (User $record) {
                    $record->update(['email_verified_at' => now()]);
                    Notification::make()
                        ->success()
                        ->title('Email verified')
                        ->body('User email has been verified successfully.')
                        ->send();
                })
                ->visible(fn (User $record) => is_null($record->email_verified_at)),
            Actions\Action::make('unverify_email')
                ->label('Unverify Email')
                ->icon('heroicon-o-x-mark')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function (User $record) {
                    $record->update(['email_verified_at' => null]);
                    Notification::make()
                        ->warning()
                        ->title('Email unverified')
                        ->body('User email verification has been removed.')
                        ->send();
                })
                ->visible(fn (User $record) => ! is_null($record->email_verified_at)),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('User updated')
            ->body('The user has been updated successfully.');
    }
}
