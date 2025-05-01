<?php

namespace App\Livewire;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasUser;
use Tapp\FilamentTimezoneField\Forms\Components\TimezoneSelect;

class ProfileTimezoneComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;
    use HasUser;

    public ?array $data = [];

    protected static int $sort = 0;

    public function mount(): void
    {
        $user = $this->getUser();

        $this->form->fill(['timezone' => $user->timezone]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Update Timezone')
                    ->aside()
                    ->description('Set/Update your Timezone.')
                    ->schema([
                        TimezoneSelect::make('timezone')
                            ->searchable(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = $this->getUser();
        $user->update($data);

        Notification::make()
            ->title('Your profile information has been saved successfully.')
            ->success()
            ->send();

    }

    public function render(): View
    {
        return view('livewire.profile-timezone-component');
    }
}
