$(function () {
    $('.js-basic-example').DataTable({
        responsive: true
    });

    //Exportable table
    $('.js-exportable').DataTable({
        dom: 'Bfrtip',
	 pageLength:15,
        responsive: true,
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});