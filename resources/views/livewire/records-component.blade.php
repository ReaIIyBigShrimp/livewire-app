<div key="recordsComponent" class="container mx-auto">
    <h1 class="text-3xl font-bold text-gray-900">Records Table</h1>
    <div>
        @if (session()->has('error'))
            <div class="alert alert-danger"> {{ session('error') }} </div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if (!is_null($paginatedRecords) && $paginatedRecords->count())
                    @foreach ($paginatedRecords as $record)
                        <tr>
                            <td>{{ $record->id }}</td>
                            <td>{{ $record->name }}</td>
                            <td>{{ $record->email }}</td>
                            <td>{{ $record->phone }}</td>
                            <td>
                                <button wire:click="edit({{ $record->id }})" class="btn btn-primary">Edit</button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5">No records found.</td>
                    </tr>
                @endif

            </tbody>
        </table>

        <div class="mt-4">
            @if ($paginatedRecords)
                {{ $paginatedRecords->links() }}
            @endif

            <button wire:click="create()" class="btn btn-success">Add
                Record</button>

            <div wire:ignore.self class="modal fade" id="recordModal" tabindex="-1" role="dialog"
                aria-labelledby="Add Record" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            @if ($mode === 'create')
                                <h5 class="modal-title" id="exampleModalLabel">Add Record</h5>
                            @else
                                <h5 class="modal-title" id="exampleModalLabel">Edit Record</h5>
                                <div class="ms-3">
                                    <button wire:click="delete({{ $record_id }})" type="button"
                                        class="btn btn-danger">Delete</button>
                                </div>
                            @endif

                        </div>
                        <div class="modal-body">
                            @if ($mode === 'edit')
                                <form wire:submit.prevent="update">
                                    <div class="mb-3">
                                        <input type="text" wire:model.live="name" placeholder="Name"
                                            class="form-control" wire:change="fieldTouched('name')">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <input type="email" wire:model.live="email" placeholder="Email"
                                            class="form-control" wire:change="fieldTouched('email')">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <input type="text" wire:model.live="phone" placeholder="Phone"
                                            class="form-control" wire:change="fieldTouched('phone')">
                                    </div>

                                    <div>
                                        @error('phone')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary"
                                    :disabled="!$wire.isFormValid">Confirm edit</button>
                                </form>
                            @else
                                <form wire:submit.prevent="store">
                                    <div class="mb-3">
                                        <input type="text" wire:model.live="newName" placeholder="Name"
                                            class="form-control">
                                        @error('newName')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <input type="email" wire:model.live="newEmail" placeholder="Email"
                                            class="form-control">
                                        @error('newEmail')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <input type="text" wire:model.live="newPhone" placeholder="Phone"
                                            class="form-control">
                                        @error('newPhone')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary"
                                        @if ($errors->any()) disabled @endif>Confirm</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('openModal', event => {
                $('#recordModal').modal('show');
            });

            Livewire.on('closeModal', (event) => {
                // Logic to close the modal
                console.log(event);
                $('#recordModal').modal('hide');
            });

            Livewire.on('showToast',
                event => {
                    console.log(event[0]);
                    Toastify({
                        text: `${event[0].msg}`,
                        duration: 3000,
                        gravity: "bottom", // `top` or `bottom`
                        position: "left", // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        style: {
                            background: "#123456",
                        },
                        onClick: function() {
                            console.log('Test')
                        } // Callback after click
                    }).showToast();
                });
        });
    </script>
