<!-- Bootstrap 4 -->
{{-- <script src="{{asset('assets')}}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-fQybjgWLrvvRgtW6bFlB7jaZrFsaBXjsOMm/tB9LTS58ONXgqbR9W8oWht/amnpF" crossorigin="anonymous">
</script>




<!-- DataTables  & Plugins -->
<script src="{{ asset('assets') }}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{ asset('assets') }}/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

<!-- AdminLTE App -->
<script src="{{ asset('assets') }}/dist/js/adminlte.min.js"></script>

<script src="{{ asset('assets') }}/loading_overlay/loadingoverlay.min.js"></script>

<!-- Sweet Alert Js  -->
<script src="{{ asset('assets') }}/sweet-alert/sweetalert2.min.js"></script>

<!-- Select2 -->
<script src="{{ asset('assets') }}/plugins/select2/js/select2.full.min.js"></script>

<script src="{{ asset('assets') }}/plugins/moment/moment.min.js"></script>

<!-- date-range-picker -->
<script src="{{ asset('assets') }}/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('assets') }}/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="{{ asset('assets') }}/js/jquery.inputmask.min.js"> </script>
<script src="{{ asset('assets') }}/js/inputmask.binding.js"> </script>

<!-- Summernote -->
{{-- <script src="{{asset('assets')}}/plugins/summernote/summernote-bs4.min.js"></script> --}}

<script src="{{ asset('assets') }}/toastr/toastr.min.js"></script>
{!! Toastr::message() !!}

<!-- Kalau Theme Merahnya Sidebar Berubah -->
{{-- <script src="{{asset('assets')}}/dist/js/pages/dashboard.js"></script>
<script src="{{asset('assets')}}/dist/js/demo.js"></script> --}}

<!-- Page specific script -->
@yield('javascript')

<script>
    $(document).ready(function() {

        $("#formChangePassword").submit(function(event) {
            event.preventDefault();
            ajaxSubmitCP();
        });

        $(document).on('select2:open', (e) => {
            const selectId = e.target.id

            $(".select2-search__field[aria-controls='select2-" + selectId + "-results']").each(function(
                key,
                value,
            ) {
                value.focus();
            })
        })

    });

    $('.btn-submit').click(function() {
        // $(this).attr('disabled','disabled');
        $.LoadingOverlay("show");
    });
    $('.nomor').keyup(function() {
        this.value = formatRupiah(this.value);
    });

    $(".nomor").keydown(function(e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode == 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    // FILTER ONLY NUMBER INPUT
    $('.number').keyup(function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    // FILTER ONLY NUMBER INPUT
    $('.number-no-zero').keyup(function() {
        this.value = this.value.replace(/^[^1-9]|[^0-9]/g, '');
    });
    // FILTER ONLY NUMBER, MINUS, DOT
    $('.numberMinDot').keyup(function() {
        this.value = this.value.replace(/[^0-9\-.]/g, '');
    });

    //Initialize Select2 Elements
    $('.select2').select2()

    // $('body').on('focus', ".tanggal", function() {
    //   $(this).datepicker({
    //     dateFormat: 'dd/mm/yy',
    //     // viewMode: "year",
    //     // autoclose: true,
    //     // todayHighlight: true,
    //     // calendarWeeks: false,
    //     // startView: 0,
    //     // todayBtn: "linked"
    //   });
    //     });

    //   $('body').on('focus', ".waktu", function() {
    //   $(this).timepicker({
    //     // locale:'ID',
    //     timeFormat: 'HH:mm',
    //     dynamic: false,
    //     dropdown: true,
    //     scrollbar: true,
    //   });
    //     });

    // });


    var DateDiff = {

        inDays: function(d1, d2) {
            var t2 = d2.getTime();
            var t1 = d1.getTime();

            return Math.floor((t2 - t1) / (24 * 3600 * 1000));
        },

        inWeeks: function(d1, d2) {
            var t2 = d2.getTime();
            var t1 = d1.getTime();

            return parseInt((t2 - t1) / (24 * 3600 * 1000 * 7));
        },

        inMonths: function(d1, d2) {
            var d1Y = d1.getFullYear();
            var d2Y = d2.getFullYear();
            var d1M = d1.getMonth();
            var d2M = d2.getMonth();

            return (d2M + 12 * d2Y) - (d1M + 12 * d1Y);
        },

        inYears: function(d1, d2) {
            return d2.getFullYear() - d1.getFullYear();
        }
    }

    $('#reservationdate').datetimepicker({
        // format: 'L',
        format: 'DD/MM/YYYY',
    });
    //Timepicker
    $('#timepicker').datetimepicker({
        format: 'LT',
    })

    function formatRupiah(angka) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }


    function clearErrorValidation() {
        var formChangePassword = $('#formChangePassword').serializeArray();
        var fields = [];
        for (let i = 0; i < formChangePassword.length; i++) {
            const el = formChangePassword[i];
            fields = [...fields, el.name];
        }
        for (let j = 0; j < fields.length; j++) {
            $("#" + fields[j] + "_error").text("");
            $("#" + fields[j]).removeClass("is-invalid");
        }
    }

    function ubahPassword() {
        $('#ModalChangePassword form').trigger("reset")
        $("#modal_title_cp").text("Ganti Password")
        clearErrorValidation()
        $('#ModalChangePassword').modal('show')
    }


    // submit brand
    function ajaxSubmitCP() {
        clearErrorValidation();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{ url('admin/ajax_change_password') }}",
            type: 'POST',
            data: $('#formChangePassword').serialize(),
            beforeSend: function() {
                $.LoadingOverlay("show");
            },
            complete: function() {
                $.LoadingOverlay("hide");
            },
            success: function(json) {
                $.LoadingOverlay("hide");
                console.log(json);
                if (json.status == "success") {
                    swal({
                        type: 'success',
                        html: json.result,
                        allowOutsideClick: false,
                    }).then((result) => {
                        $('#ModalChangePassword').modal('hide')
                        location.reload();
                    }).catch(swal.noop);

                } else if (json.status == "error_val") {
                    $.each(json.error_message, function(key, val) {
                        $("#" + key + "_error").text(val[0]);
                        $("#" + key).addClass("is-invalid");
                    });
                    toastr["error"]('Validation Error!')
                } else {
                    toastr["error"](json.error_message)
                }

            },
            error: function(error) {
                $.LoadingOverlay("hide");
                console.log(error.responseJSON.message);
                swal({
                    type: 'error',
                    html: error.responseJSON.message,
                })
            }
        })
    }
    //
</script>
