$(document).ready(function() {
    $('#report_result').DataTable({
    	"lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]],
    	"order": [],
    });

    $('#upcTable').DataTable({
    	// "lengthMenu": [[-1, 10, 25, 50], ["All", 10, 25, 50]],
    	"paging": false,
    	"ordering" : false
    });
} );