<div class="form-group col-6">
    <label class="col-form-label">Nilai Konfigurasi</label>
    <div class="custom-file">
        <input type="file" name="value" class="form-control form-control-sm custom-file-input" id="value">
        <label class="form-control form-control-sm custom-file-label" for="customFile">Choose file</label>
    </div>
    <small class="form-text text-danger" id="value_error"></small>
</div>

@section('appendJS')
    <script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js" defer></script>
    <script>
        $(document).ready(function() {
            bsCustomFileInput.init()
        })
    </script>
@endsection
