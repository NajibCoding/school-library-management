    // Datepicker    
    $(function () {
        $('#tgl_lahir').datepicker({
            autoclose: true,
            todayHighlight: true,
              format: "mm/dd/yyyy"
        })
    });
    // End Datepicker

    // Summernote
    $(function () {
        $('.summernote').summernote()
    });
    // End Summernote

	// Table Start
    $(document).ready( function () {
        $('#table_id').DataTable({
		    "lengthMenu": [ 3, 5, 10 ],
        	"language": {
        		"info": "_PAGE_ dari _PAGES_",
        		"infoEmpty": "Data tidak ditemukan.",
        		"zeroRecords": "Data tidak ditemukan.",
        		"lengthMenu": "Tampilkan _MENU_ data",
        		"search": "Cari _INPUT_",
			    "paginate": {
			      "previous": "&laquo;",
			      "next": "&raquo;"
			    }
        	}
        });
    } );    
    // Table End

    // upload image
    const realFileBtn = document.getElementById("foto");
    const customBtn = document.getElementById("custom-button");
    const customTxt = document.getElementById("custom-text");

    customBtn.addEventListener("click", function() {
      realFileBtn.click();
    });

    realFileBtn.addEventListener("change", function() {
      if (realFileBtn.value) {
        customTxt.innerHTML = realFileBtn.value.match(
          /[\/\\]([\w\d\s\.\-\(\)]+)$/
        )[1];
      } else {
        customTxt.innerHTML = "No file chosen, yet.";
      }
    });