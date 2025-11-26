function initializeDataTable(pageUrl, columns) {
    const tableClass = '.data_table';

    if ($.fn.DataTable.isDataTable(tableClass)) {
        $(tableClass).DataTable().clear().destroy();
    }

    $(tableClass).DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        ajax: {
            url: pageUrl + "?loaddata=yes",
            type: "GET"
        },
        columns: columns
    });
}