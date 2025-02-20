@extends('master.main')

@section('content')
    <div class="container w-100 fade-in">

        @if (session('error'))
            <div class="alert alert-danger contact-alert">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success contact-alert">
                {{ session('success') }}
            </div>
        @endif

        @foreach ($errors->get('new_contact_descriptions.*') as $error)
            <div class="alert alert-danger contact-alert">{{ $error[0] }}</div>
        @endforeach

        @foreach ($errors->get('new_contact_values.*') as $error)
            <div class="alert alert-danger contact-alert">{{ $error[0] }}</div>
        @endforeach

        <h1>Editar Empresa</h1>
        <form method="post" action="{{ route('partners.update', $partner->id) }}">

            @csrf
            @method('put')

            <div class="row">
                <div class="col-md-6">
                    <div class="my-3">
                        <label for="name" class="form-label">Empresa:</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ $partner->name }}">

                        @error('name')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição:</label>
                        <textarea class="form-control" id="description" name="description">{{ $partner->description }}</textarea>

                        @error('description')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Morada:</label>
                        <input type="text" class="form-control" id="address" name="address"
                            value="{{ $partner->address }}">

                        @error('address')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="contacts" class="form-label">Contactos:</label>
                            <button type="button" class="btn btn-primary" onclick="addContactFields()">Novo Contacto</button>
                        </div>
                        <div id="contacts-container">
                            @foreach ($partner->contactPartner as $index => $contact)
                                <div class="contact-group mb-3">
                                    <input type="hidden" name="existing_contact_ids[]" value="{{ $contact->id }}">
                                    <input type="text" class="form-control" name="existing_contact_descriptions[]"
                                        value="{{ old('existing_contact_descriptions.' . $index, $contact->description) }}"
                                        placeholder="Descrição">
                                    <input type="text" class="form-control" name="existing_contact_values[]"
                                        value="{{ old('existing_contact_values.' . $index, $contact->contact) }}"
                                        placeholder="Contacto">
                                    <a href="/partners/remove-contact/{{ $contact->id }}" class="btn"
                                        onclick="return confirm('Tem certeza de que deseja excluir este contacto?');">
                                        <svg xmlns="http://www.w3.org/2000/svg" height="16" width="14"
                                            viewBox="0 0 448 512">
                                            <path fill="#116fdc"
                                                d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0H284.2c12.1 0 23.2 6.8 28.6 17.7L320 32h96c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32h96l7.2-14.3zM32 128H416V448c0 35.3-28.7 64-64 64H96c-35.3 0-64-28.7-64-64V128zm96 64c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16z" />
                                        </svg>
                                    </a>


                                    @error("existing_contact_descriptions.$index")
                                        <div class="alert alert-danger contact-alert">{{ $message }}</div>
                                    @enderror

                                    @error("existing_contact_values.$index")
                                        <div class="alert alert-danger contact-alert">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach


                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" onclick="submitForm()">Atualizar Parceiro</button>
            <a href="{{ route('external.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function submitForm() {
            validateContacts();

            const firstContactGroup = document.querySelector('.contact-group');
            const firstDescriptionInput = firstContactGroup.querySelector('[name^="new_contact_descriptions"]');
            const firstValueInput = firstContactGroup.querySelector('[name^="new_contact_values"]');

            if (firstDescriptionInput.value.trim() === '' && firstValueInput.value.trim() === '') {
                firstContactGroup.remove();
            }

            document.querySelector('form').submit();
        }

        function validateContacts() {
            const contactGroups = document.querySelectorAll('.contact-group');

            for (let i = 0; i < contactGroups.length; i++) {
                const descriptionInput = contactGroups[i].querySelector(
                    '[name^="existing_contact_descriptions"], [name^="new_contact_descriptions"]');
                const valueInput = contactGroups[i].querySelector(
                    '[name^="existing_contact_values"], [name^="new_contact_values"]');

                if (descriptionInput.value.trim() === '' || valueInput.value.trim() === '') {
                    if (contactGroups.length > 1 || (contactGroups.length === 1 && descriptionInput.value.trim() !== '' &&
                            valueInput.value.trim() !== '')) {
                        contactGroups[i].remove();
                    }
                }
            }

            addNewContactGroupIfNeeded();
            updateRemoveButtonState();
        }

        document.addEventListener('DOMContentLoaded', function() {
            checkAndAddContactFields();
            updateRemoveButtonState();
        });

        function checkAndAddContactFields() {
            const contactsContainer = document.getElementById('contacts-container');
            const existingContacts = document.querySelectorAll('.contact-group');

            if (existingContacts.length === 0) {
                addContactFields();
            }
        }

        function addContactFields() {
            const contactGroups = document.querySelectorAll('.contact-group');
            const maxContacts = 3;

            if (contactGroups.length < maxContacts) {
                if (contactGroups.length === 0) {
                    addNewContactGroup();
                } else {
                    const lastContactGroup = contactGroups[contactGroups.length - 1];
                    const lastDescriptionInput = lastContactGroup.querySelector('[name^="new_contact_descriptions"]');
                    const lastValueInput = lastContactGroup.querySelector('[name^="new_contact_values"]');

                    if (lastDescriptionInput && lastValueInput && (lastDescriptionInput.value.trim() === '' ||
                            lastValueInput.value.trim() === '')) {
                        alert('Preencha todos os campos dos contactos anteriores!');
                    } else {
                        addNewContactGroup();
                    }
                }
            } else {
                alert('Número máximo de contactos atingido!');
            }
        }

        function addNewContactGroup() {
            const contactsContainer = document.getElementById('contacts-container');
            const newContactGroup = document.createElement('div');
            newContactGroup.classList.add('contact-group', 'mb-3');

            const inputId = document.createElement('input');
            inputId.type = 'hidden';
            inputId.name = 'new_contact_ids[]';
            inputId.value = '';

            const inputDescription = document.createElement('input');
            inputDescription.type = 'text';
            inputDescription.classList.add('form-control');
            inputDescription.name = 'new_contact_descriptions[]';
            inputDescription.placeholder = 'Descrição';

            const inputValue = document.createElement('input');
            inputValue.type = 'text';
            inputValue.classList.add('form-control');
            inputValue.name = 'new_contact_values[]';
            inputValue.placeholder = 'Contacto';

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.classList.add('btn');
            removeButton.innerHTML =
                '<svg xmlns="http://www.w3.org/2000/svg" height="16" width="14" viewBox="0 0 448 512"><path fill="#116fdc" d="M135.2 17.7C140.6 6.8 151.7 0 163.8 0H284.2c12.1 0 23.2 6.8 28.6 17.7L320 32h96c17.7 0 32 14.3 32 32s-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32h96l7.2-14.3zM32 128H416V448c0 35.3-28.7 64-64 64H96c-35.3 0-64-28.7-64-64V128zm96 64c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16zm96 0c-8.8 0-16 7.2-16 16V432c0 8.8 7.2 16 16 16s16-7.2 16-16V208c0-8.8-7.2-16-16-16z"/></svg>';
            removeButton.addEventListener('click', function() {
                newContactGroup.remove();
                addNewContactGroupIfNeeded();
                updateRemoveButtonState();
            });

            newContactGroup.appendChild(inputId);
            newContactGroup.appendChild(inputDescription);
            newContactGroup.appendChild(inputValue);
            newContactGroup.appendChild(removeButton);

            contactsContainer.appendChild(newContactGroup);
            updateRemoveButtonState();
        }

        function addNewContactGroupIfNeeded() {
            const contactsContainer = document.getElementById('contacts-container');
            const contactGroups = document.querySelectorAll('.contact-group');

            if (contactGroups.length === 0) {
                addNewContactGroup();
            }
        }

        function updateRemoveButtonState() {
            const contactGroups = document.querySelectorAll('.contact-group');

            contactGroups.forEach(function(contactGroup) {
                const descriptionInput = contactGroup.querySelector(
                    '[name^="existing_contact_descriptions"], [name^="new_contact_descriptions"]');
                const valueInput = contactGroup.querySelector(
                    '[name^="existing_contact_values"], [name^="new_contact_values"]');

                if (descriptionInput && valueInput) {
                    const isDescriptionEmpty = descriptionInput.value.trim() === '';
                    const isValueEmpty = valueInput.value.trim() === '';

                    const isGroupEmpty = isDescriptionEmpty && isValueEmpty;

                    const removeButton = contactGroup.querySelector('button');
                    if (removeButton) {
                        removeButton.disabled = isGroupEmpty && contactGroups.length === 1;
                    }
                }
            });
        }

        window.setTimeout(function() {
            $(".contact-alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 2500);
    </script>
@endsection
