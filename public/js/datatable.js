$(document).ready(function () {
    // Initialize DataTable
    $("#datatable").DataTable({
        dom: "Blfrtip",
        buttons: [
            {
                extend: "csvHtml5",
                text: "Export CSV",
                className: "btn btn-info",
            },
            {
                extend: "excelHtml5",
                text: "Export Excel",
                className: "btn btn-success",
            },
            {
                extend: "pdfHtml5",
                text: "Export PDF",
                className: "btn btn-danger",
            },
            {
                extend: "print",
                text: "Print",
                className: "btn btn-warning",
            },

        ],
        pagingType: "full_numbers",
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search..",
        },
        initComplete: function () {
            $(".dataTables_length select").css("width", "120px");
        },
        ordering: false
    });
});
