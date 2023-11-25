<div class="form-group col-6">
    <label class="col-form-label">Nilai Konfigurasi</label>
    <select class="form-control form-control-sm" name="value" id="value">
        <option value="">Pilih Nilai</option>
        @if (is_array($enum) && count($enum) > 0)
            @foreach ($enum as $row)
                <option value="{{ key($row) }}">{{ current($row) }}</option>
            @endforeach
        @endif
    </select>
    <small class="form-text text-danger" id="value_error"></small>
</div>
