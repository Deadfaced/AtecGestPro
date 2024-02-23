@extends('master.main')

@section('content')
    <div class="container  w-100 fade-in">
        <h1>Criar Turma</h1>

        <form method="post" action="{{ route('course-classes.store') }}" id="createCourseClassForm" class=" mb-3">
            @csrf

            <div class="row mb-1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="description">Descrição:</label>
                        <input type="text" class="form-control" id="description" name="description">

                        @error('description')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="course_id">Curso:</label>
                        <select class="form-control" id="course_id" name="course_id" required>

                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->description }}</option>
                            @endforeach
                        </select>

                        @error('course_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>


            <div class="row">
                <h3 class="my-3">Atribuir alunos à turma</h3>
                <div class="d-flex justify-content-between w-100">
                    <div class="form-group mr-3 w-25 search-container">
                        <input type="text" id="search" class="form-control w-100" placeholder="Pesquisar Aluno">
                    </div>
                    <div class="form-group w-25">
                        <a href="{{ route('users.create') }}" class="btn btn-primary w-100">
                            <i class="fa-solid fa-pen mr-1" style="color: #ffffff;"></i>
                            Novo Aluno
                        </a>
                    </div>
                </div>
                <div class=" row scrollable w-100">
                    <table class="table" id="studentsTable">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>Nome</th>
                            <th>Username</th>
                            <th>Email</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td><input type="checkbox" name="selected_students[]" value="{{ $student->id }}"></td>
                                <td>{{ $student->name }}</td>
                                <td>{{ $student->username }}</td>
                                <td>{{ $student->email }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-3 ">
                <button type="submit" class="btn btn-primary mr-2 modalBtn"
                        data-message="Tem a certeza que pretende criar turma sem alunos?" name="noImport"
                        id="criarTurmaBtn">Criar Turma
                </button>
                <button id="confirmButton" class="btn btn-primary mr-2 " name="import">Criar Turma e importar alunos a partir de
                    Excel
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
            </div>


        </form>

        {{--confirmation modal--}}
        <div class="modal" tabindex="-1" role="dialog" id="confirmationModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmação</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="continueBtn">Continuar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        {{----}}
        <!-- The Import Students Modal -->
        <div class="modal" tabindex="-1" role="dialog" id="importStudentsModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Importar alunos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('import-excel.importStudents') }}" method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            <div class="form-group p-3">
                                <label for="file">Excel - Importar Alunos</label><br>
                                <label for="file" class="btn btn-primary">Selecionar ficheiro</label><br>
                                <input type="file" name="file" id="file" class="btn" style="display: none;">
                                <input type="hidden" >
                                @error('attachment')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                                <p>Certifique-se que o arquivo tem menos de 20MB</p>
                            </div>

                            <div class="modal-footer">
                                <button  type="submit" name="withStudents" class="btn btn-primary">Importar</button>
                                <a href="{{ route('course-classes.create') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>


                </div>

            </div>

        </div>
        {{----}}

    </div>

    <style>

        .scrollable {
            height: 17rem;
            overflow-y: scroll;
        }
    </style>

    <script>

        var description;
        var courseId;

        $('#confirmButton').click(function(e) {
            e.preventDefault();

            // Get the courseclass description and the course_id from the form
            description = $('#description').val();
            courseId = $('#course_id').val();


            // Open the importStudentsModal
            $('#importStudentsModal').modal('show');
        });

        // Handle the form submit event
        $('form[action="{{ route('import-excel.importStudents') }}"]').submit(function(e) {
            // Get the form data
            var formData = new FormData(this);
            formData.append('description', description);
            formData.append('course_id', courseId);

            // Submit the form with the new data
            $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    // Handle the success response
                    console.log(data);
                },
                error: function(data) {
                    // Handle the error response
                    console.log(data);
                }
            });

            // Prevent the form from being submitted normally
            e.preventDefault();
        });
    </script>

@endsection
@push('scripts')
    <script src="{{ asset('js/course-classes/create.js') }}"></script>
@endpush
