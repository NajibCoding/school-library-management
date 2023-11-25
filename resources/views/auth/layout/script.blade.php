<!-- jQuery -->
<script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets') }}/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets') }}/dist/js/adminlte.min.js"></script>

<!-- Loading Overlay Js  -->
<script src="{{ asset('assets') }}/loading_overlay/loadingoverlay.js"></script>

<script src="{{ asset('assets') }}/toastr/toastr.min.js"></script>
{!! Toastr::message() !!}
<script>
    $(document).ready(function() {
        $('.btn-submit').click(function() {
            // $(this).attr('disabled','disabled');
            $.LoadingOverlay("show");
        });
    });
</script>
