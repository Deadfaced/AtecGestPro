@extends('master.main')

@section('content')
    <div class="container p-5">
        <h1>Vestuário</h1>


        <h5>Nome Completo</h5>
        <div class="input-group mb-3" style="width: 60%;">
            <input type="text" class="form-control" id="userToAssignClothing" placeholder="" aria-label="Username"
                aria-describedby="basic-addon1" disabled="disabled">
            <div class="input-group-prepend">
                <!-- replace the materials.index for the route to user.edit or student.edit with the user id-->
                <button class="btn btn-warning" id="EditInput" type="button" onclick="window.location.href='{{ route('materials.index') }}'">Editar</button>
            </div>
        </div>


        <div class="mb-3">
            <div class="d-flex">
                <div style="width: 30%;">
                    <input type="text" id="search" class="form-control" placeholder="Pesquisar" >
                </div>

                <div class="ms-2">
                    <label for="filter">Filtrar por:</label>
                    <select class="form-select" id="filter">
                        <option value="all">Todos</option>
                        <option value="trainer">Formador</option>
                        <option value="trainee">Formando</option>
                        <option value="technical">Técnico </option>
                    </select>
                </div>

                <a href="{{ route('clothing.create') }}" class="btn btn-primary mb-3">Novo Vestuário</a>

            </div>
        </div>


        <form method="post">


            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">
                            <input type="checkbox" id="select-all">
                        </th>
                        <th scope="col">Nome</th>
                        <th scope="col">Género</th>
                        <th scope="col" style="text-align: center;">Tamanho</th>
                        <th scope="col" style="text-align: center;">Função</th>
                        <th scope="col" style="text-align: center;">Quantidade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clothing as $clothing)

                        <tr class="material-row" data-trainer="{{ $clothing->role == 2 ? 1 : 0 }}"
                            data-trainee="{{ $clothing->role == 3 ? 1 : 0 }}"
                            data-technical="{{ $clothing->role == 4 ? 1 : 0 }}">
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="{{ $clothing->id }}"
                                        id="flexCheckDefault">

                                </div>
                            </td>
                            <td>
                                <a
                                    href="{{ route('materials.show', $clothing->id) }}">{{ isset($clothing->name) ? $clothing->name : 'N.A.' }}</a>
                            </td>
                            <td>
                                @if (isset($clothing->gender))
                                    @if ($clothing->gender == 1)
                                        Masculino
                                    @elseif($clothing->gender == 0)
                                        Feminino
                                    @endif
                                @else
                                    N.A.
                                @endif
                            </td>
                            <td style="text-align: center;">{{ isset($clothing->size) ? $clothing->size : 'N.A.' }}</td>
                            <!-- usar if ou swit para substituir o numero do role pelo nome -->
                            <td style="text-align: center;">{{ isset($clothing->role) ? $clothing->role : 'N.A.' }}</td>
                            <td style="text-align: center;">{{ isset($clothing->quantity) ? $clothing->quantity : 'N.A.' }}
                            </td>
                            <td>
                                <a href="{{ route('clothing.edit', $clothing->id) }}"
                                    class="btn btn-warning btn-edit">Editar</a>
                                <form method="post" action="{{ route('clothing.destroy', $clothing->id) }}"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Tem certeza que deseja excluir?')"
                                        >Excluir</button>

                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>

        <h5>Observações </h5>
        <div class="input-group mb-3" style="width: 80%;">
            <textarea class="form-control" id="textarea" aria-label="With textarea"></textarea>
            <div class="input-group-prepend">
                <button class="btn btn-danger" type="button" onclick="location.reload()">Apagar</button>

                <!-- I intend to meet with Claudio to discuss how we are going to deal with this Save part -->

                <button class="btn btn-primary" type="button">Guardar</button>

                <!-- replace the clothing.index for the route back to turmas or wherever -->
                <button class="btn btn-primary" type="button" onclick="window.location.href='{{ route('clothing.index') }}'">Fechar</button>
            </div>
        </div>

    </div>


    <script>

        // array only  for testing, substitute later for the value from ClassCourse or CourseClass
        let users = [{
                id: 1,
                name: 'Coelho Cenoura',
                role: 3
            },
            {
                id: 2,
                name: 'Not Your Mother',
                role: 2
            },
            {
                id: 3,
                name: 'Pipa Pepino',
                role: 4
            }
        ];

        //Get the user name and show in the "Nome Completo"
        let inputElement = document.querySelector('#userToAssignClothing');
        inputElement.placeholder = users[2].name;



        document.addEventListener('DOMContentLoaded', function() {
            const selectAllCheckbox = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.form-check-input');
            const searchInput = document.getElementById('search');
            const filterDropdown = document.getElementById('filter');


            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    selectAllCheckbox.checked = checkboxes.length === document.querySelectorAll(
                        'input[name="selectedClothing[]"]:checked').length;
                });
            });

            searchInput.addEventListener('input', function() {
                const searchTerm = searchInput.value.toLowerCase();
                filterMaterials(searchTerm);
            });

            filterDropdown.addEventListener('change', function() {
                filterMaterials();
            });

            function filterMaterials(searchTerm = null) {
                checkboxes.forEach(checkbox => {
                    const materialRow = checkbox.closest('.material-row');
                    const isTrainer = materialRow.getAttribute('data-trainer') === '1';
                    const isTrainee = materialRow.getAttribute('data-trainee') === '1';
                    const isTechnical = materialRow.getAttribute('data-technical') === '1';


                    const filterValue = filterDropdown.value;

                    const matchesFilter = (
                        (filterValue === 'all') ||
                        (filterValue === 'trainer' && isTrainer) ||
                        (filterValue === 'trainee' && isTrainee) ||
                        (filterValue === 'technical' && isTechnical)

                    );

                    const matchesSearch = !searchTerm || (
                        materialRow.textContent.toLowerCase().includes(searchTerm) ||
                        materialRow.querySelector('a').textContent.toLowerCase().includes(searchTerm)
                    );

                    checkbox.closest('tr').style.display = matchesFilter && matchesSearch ? '' : 'none';
                });
            }
        });
    </script>
@endsection
