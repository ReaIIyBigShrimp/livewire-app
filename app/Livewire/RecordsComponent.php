<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Record;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;

class RecordsComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:records,email',
        'phone' => 'required|regex:/^07[0-9]{9}$/|unique:records,phone',
        'newName' => 'required|string|max:255',
        'newEmail' => 'required|email|unique:records,email',
        'newPhone' => 'required|regex:/^07[0-9]{9}$/|unique:records,phone',
    ];

    protected $messages = [
        'name.required' => 'Name is required',
        'email.required' => 'Email is required',
        'email.email' => 'Email must be a valid email address',
        'email.unique' => 'This email is already taken',
        'phone.required' => 'Phone number is required. It must start with "07"',
        'phone.regex' => 'Phone number must start with "07" and contain 11 digits',
        'phone.unique' => 'This phone number is already taken',
        'newName.required' => 'Name is required',
        'newEmail.required' => 'Email is required',
        'newEmail.email' => 'Email must be a valid email address',
        'newEmail.unique' => 'This email is already taken',
        'newPhone.required' => 'Phone number is required. It must start with "07"',
        'newPhone.regex' => 'Phone number must start with "07" and contain 11 digits',
        'newPhone.unique' => 'This phone number is already taken',
    ];

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

    public $touchedFields = [];
    public $isFormValid = false;
    public $isTouched = false;

    public $originalName = '';
    public $originalEmail = '';
    public $originalPhone = '';

    public function mount()
    {
        //$this->records = Record::paginate(10);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->isFormValid = empty($this->getErrorBag()->all()) && $this->hasChanged();
    }

    public function hasChanged()
    {
        return $this->name !== $this->originalName || $this->email !== $this->originalEmail || $this->phone !== $this->originalPhone;
        $this->isTouched = !empty(array_filter($this->touchedFields));
    }

    public function fieldTouched($field)
    {
        $this->touchedFields[$field] = true;
    }

    public function resetPhoneNumber()
    {
        $this->phone = $this->originalPhone;
    }

    public function edit($id)
    {
        $this->touchedFields = [];
        $this->isFormValid = false;
        $this->isTouched = false;
        $record = Record::findOrFail($id);
        $this->resetValidation();
        $this->record_id = $record->id;
        $this->name = $record->name;
        $this->email = $record->email;
        $this->phone = $record->phone;
        $this->mode = 'edit';

        $this->originalName = $record->name;
        $this->originalEmail = $record->email;
        $this->originalPhone = $record->phone;

        $this->dispatch('openModal');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email' . ($this->email !== $this->originalEmail ? '|unique:records,email,' . $this->record_id : ''),
            'phone' => 'required|regex:/^07[0-9]{9}$/' . ($this->phone !== $this->originalPhone ? '|unique:records,phone,' . $this->record_id : ''),
        ]);
        try {
            $record = Record::findOrFail($this->record_id);
            $record->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
            ]);
            $this->dispatch('closeModal');
            $this->records = Record::all();
            $this->dispatch('showToast', ['msg' => 'Record successfully updated.']);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                $errorInfo = $e->errorInfo;
                if (strpos($errorInfo[2], 'records_email_unique') !== false) {
                    $this->dispatch('showToast', ['msg' => 'Duplicate entry detected. Please use a unique email address.', 'type' => 'error']);
                } elseif (strpos($errorInfo[2], 'records_phone_unique') !== false) {
                    $this->dispatch('showToast', ['msg' => 'Duplicate entry detected. Please use a unique phone number.', 'type' => 'error']);
                } else {
                    $this->dispatch('showToast', ['msg' => 'There was an error updating the record.', 'type' => 'error']);
                }
            } else {
                Log::error('Error updating record: ' . $e->getMessage());
                $this->dispatch('showToast', ['msg' => 'There was an error updating the record.', 'type' => 'error']);
            }
        } catch (\Exception $e) {
            Log::error('Error updating record: ' . $e->getMessage());
            $this->dispatch('showToast', ['msg' => 'There was an error updating the record.', 'type' => 'error']);
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
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                $errorInfo = $e->errorInfo;
                if (strpos($errorInfo[2], 'records_email_unique') !== false) {
                    $this->dispatch('showToast', ['msg' => 'Duplicate entry detected. Please use a unique email address.', 'type' => 'error']);
                } elseif (strpos($errorInfo[2], 'records_phone_unique') !== false) {
                    $this->dispatch('showToast', ['msg' => 'Duplicate entry detected. Please use a unique phone number.', 'type' => 'error']);
                } else {
                    $this->dispatch('showToast', ['msg' => 'There was an error creating the record.', 'type' => 'error']);
                }
            } else {
                Log::error('Error creating record: ' . $e->getMessage());
                $this->dispatch('showToast', ['msg' => 'There was an error creating the record.', 'type' => 'error']);
            }
        } catch (\Exception $e) {
            Log::error('Error creating record: ' . $e->getMessage());
            $this->dispatch('showToast', ['msg' => 'There was an error creating the record.', 'type' => 'error']);
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
            Log::error('Error creating record: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $paginatedRecords = Record::paginate(10);
        return view('livewire.records-component', ['paginatedRecords' => $paginatedRecords]);
    }
}
