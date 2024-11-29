<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Record;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Validate;

class RecordsComponent extends Component
{
    protected $rules = ['name' => 'required|string|max:255', 'email' => 'required|email', 'phone' => 'required|regex:/^07[0-9]{9}$/', 'newName' => 'required|string|max:255', 'newEmail' => 'required|email', 'newPhone' => 'required|regex:/^07[0-9]{9}$/',];
    protected $messages = ['name.required' => 'Name is required', 'email.required' => 'Email is required', 'email.email' => 'Email must be a valid email address', 'phone.required' => 'Phone number is required. It must start with "07"', 'phone.regex' => 'Phone number must start with "07" and contain 11 digits', 'newName.required' => 'Name is required', 'newEmail.required' => 'Email is required', 'newEmail.email' => 'Email must be a valid email address', 'newPhone.required' => 'Phone number is required. It must start with "07"', 'newPhone.regex' => 'Phone number must start with "07" and contain 11 digits',];

    public $name = '';
    public $email = '';
    public $phone = '';
    public $newName = '';
    public $newEmail = '';
    public $newPhone = '';
    public $records;
    public $selectedRecord;
    public $showModal = false;
    public $mode;
    public $record_id;

    public function mount()
    {
        $this->records = Record::all();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function edit($id)
    {
        $record = Record::findOrFail($id);
        $this->resetValidation();
        $this->record_id = $record->id;
        $this->name = $record->name;
        $this->email = $record->email;
        $this->phone = $record->phone;
        $this->mode = 'edit';
        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate();
        try {
            $this->validate([
                'name' => 'required',
                'email' => 'required|email',
                'phone' => 'required|regex:/^07[0-9]{9}$/',
            ]);
            $record = Record::findOrFail($this->record_id);
            $record->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);
            $this->dispatch('closeModal');
            $this->records = Record::all();
            $this->dispatch('showToast', ['msg' => 'Record successfully updated.']);
        } catch (\Exception $e) {
            Log::error('Error updating record: ' . $e->getMessage());
            session()->flash('error', 'There was an error updating the record.');
        }
    }

    public function create()
    {
        $this->resetValidation();
        $this->newName = '';
        $this->newEmail = '';
        $this->newPhone = '';
        $this->mode = 'create';
        $this->dispatch('openModal');
    }
    public function store()
    {
        $this->validate();
        try {
            $this->validate([
                'newName' => 'required|string|max:255',
                'newEmail' => 'required|email',
                'newPhone' => 'required|regex:/^07[0-9]{9}$/',
            ]);
            Record::create([
                'name' => $this->newName,
                'email' => $this->newEmail,
                'phone' => $this->newPhone,
            ]);

            $this->dispatch('closeModal');
            $this->records = Record::all();
            $this->dispatch('showToast', ['msg' => 'Record successfully added.']);
        } catch (\Exception $e) {
            Log::error('Error creating record: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $record = Record::findOrFail($id);
            $record->delete();
            $this->dispatch('closeModal');
            $this->records = Record::all();
            $this->dispatch('showToast', ['msg' => 'Record ' . $id . ' was deleted successfully.']);
        } catch (\Exception $e) {
            $this->dispatch('showToast', ['msg' => 'Record deletion failed.']);
        }
    }

    public function render()
    {
        return view('livewire.records-component');
    }
}
