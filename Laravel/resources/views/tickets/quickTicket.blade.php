<div class="quickTicket">
    <button class="closeTicket" onclick="closeTicket()">X</button>
    <h2 class="mb-3">Ticket rápido</h2>
    <p class="mb-3">Descreva brevemente o seu problema</p>
    <form method="post" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Título:</label>
            <input type="text" class="form-control" id="title" name="title" required>

            @error('title')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror

        </div>

        <div class="mb-3">
            <label for="priority" class="form-label">Prioridade:</label>
            <select class="form-control" id="priority" name="priority_id" required>
                @foreach ($priorities as $priority)
                    <option value="{{ $priority->id }}">{{ $priority->description }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Categoria:</label>
            <select class="form-control" id="category" name="category_id" required>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->description }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="">Anexo: <strong><span id="file-name"></span></strong></label><br>
            <label for="attachment" class="btn btn-primary">Selecionar ficheiro</label>
            <input type="file" class="btn" id="attachment" name="attachment" style="display: none;" accept=".jpeg, .jpg, .png, .gif, .svg, .bmp, .raw, .pdf, .doc, .docx, .xls, .xlsm, .xlsx">
            @error('attachment')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            <p>Certifique-se que o arquivo tem menos de 20MB</p>
        </div>


           <input type="hidden" class="form-control" id="technician" name="technician_id" value="1">

        <div class="mb-3">
            <label for="description" class="form-label">Descrição:</label>
            <textarea class="form-control" id="description" name="description" required></textarea>

            @error('description')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mb-3 w-100">Criar Ticket</button>
        <button onclick="closeTicket()" class="btn btn-secondary w-100">Cancelar</button>
    </form>
</div>


<style>
    .quickTicket{
        opacity: 0;
        visibility: hidden;
        width: 20%;
        height: auto;
        max-height: calc(100dvh - 12rem);
        position: fixed;
        right: 0;
        padding: 3rem 3rem 0 3rem;
        margin-right: 3rem;
        height: 100%;
        background-color: white;
        overflow: scroll;
    }

    .quickTicket::-webkit-scrollbar {
        display: none;
    }

    .quickTicket button.closeTicket{
        position: absolute;
        top: 0;
        right: 0;
        width: 3rem;
        height: 3rem;
        border: none;
        cursor: pointer;
        background-color: transparent;
    }
</style>

<script>
    function closeTicket() {
        let quickTicket = document.querySelector('.quickTicket');
        if (quickTicket.style.visibility === 'visible') {
            quickTicket.style.opacity = '0';
            quickTicket.style.visibility = 'hidden';
            quickTicket.style.transition = 'opacity 0.1s ease, visibility 0.1s ease';
            document.querySelector('.container').classList.remove('w-70');
        }
    }

    document.getElementById('attachment').addEventListener('change', function() {
            var filename = this.value.split('\\').pop();
            document.getElementById('file-name').textContent = filename;
        });


</script>
