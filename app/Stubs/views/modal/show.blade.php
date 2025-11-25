<div class="hidden" id="modalShow">
    <div class="bg-white p-6 rounded shadow w-full max-w-lg">
        <h3 class="text-xl mb-2">View {{ moduleSingular }}</h3>

        <div id="showInfo"></div>
    </div>
</div>

<script>
function openShowModal(id) {
    $.get("{{ url('{{ modulePlural }}/') }}/" + id, data => {
        let html = ''
        for(const [k,v] of Object.entries(data)){
            html += `
                <div class="mb-2">
                    <strong>${k}:</strong> ${v ?? '-'}
                </div>`
        }
        $('#showInfo').html(html)
        $('#modalShow').show()
    })
}
</script>
