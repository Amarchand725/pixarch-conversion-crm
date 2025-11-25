<div class="hidden" id="modalEdit">
    <div class="bg-white p-6 rounded shadow w-full max-w-lg">
        <h3 class="text-xl mb-4">Edit {{ moduleSingular }}</h3>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            @include('{{ modulePlural }}._form')
            <button class="btn btn-primary mt-3">Update</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id) {
    $.get("{{ url('{{ modulePlural }}/') }}/" + id, data => {
        $('#editForm').attr('action', "{{ url('{{ modulePlural }}/') }}/" + id)

        for(const [k,v] of Object.entries(data)){
            $("[name='"+k+"']").val(v)
        }

        $('#modalEdit').show()
    })
}

$('#editForm').on('submit', function(e) {
    e.preventDefault()

    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: $(this).serialize(),
        success: () => {
            $('#modalEdit').hide()
            reloadTable()
        }
    })
})
</script>
