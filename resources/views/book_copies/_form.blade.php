@if($errors->any())

<div class="alert alert-danger">

    <ul>

        @foreach($errors->all() as $error)

        <li>{{ $error }}</li>

        @endforeach

    </ul>

</div>

@endif


<div class="form-group">

    <label for="barcode">
        Barcode
    </label>

    <input
        type="text"
        id="barcode"
        name="barcode"
        value="{{ old('barcode', $copy->barcode ?? '') }}"
        placeholder="BK-00001-001">

</div>


<div class="form-group">

    <label for="status">
        Status
    </label>

    <select
        name="status"
        id="status">

        <option
            value="available"
            @selected(old('status', $copy->status ?? 'available') === 'available')>
            Tersedia
        </option>

        <option
            value="reserved"
            @selected(old('status', $copy->status ?? '') === 'reserved')>
            Direservasi
        </option>

        <option
            value="borrowed"
            @selected(old('status', $copy->status ?? '') === 'borrowed')>
            Dipinjam
        </option>

        <option
            value="lost"
            @selected(old('status', $copy->status ?? '') === 'lost')>
            Hilang
        </option>

        <option
            value="damaged"
            @selected(old('status', $copy->status ?? '') === 'damaged')>
            Rusak
        </option>

        <option
            value="maintenance"
            @selected(old('status', $copy->status ?? '') === 'maintenance')>
            Maintenance
        </option>

    </select>

</div>


<h3>
    Lokasi Buku
</h3>


<div class="form-group">

    <label for="shelf_id">
        Rak
    </label>

    <select
        name="shelf_id"
        id="shelf_id">

        <option value="">
            -- Pilih Rak --
        </option>

        @foreach($floors as $floor)

        <optgroup label="{{ $floor->name }}">

            @foreach($floor->zones as $zone)

            @foreach($zone->shelves as $shelf)

            <option
                value="{{ $shelf->id }}"
                @selected(
                old( 'shelf_id' ,
                $copy->shelf_id ?? ''
                ) == $shelf->id
                )>

                {{ $zone->name }}
                —
                {{ $shelf->code }}

            </option>

            @endforeach

            @endforeach

        </optgroup>

        @endforeach

    </select>

</div>


<div class="form-group">

    <label for="row">
        Baris
    </label>

    <input
        type="number"
        id="row"
        name="row"
        min="1"
        value="{{ old('row', $copy->row ?? '') }}">

</div>


<div class="form-group">

    <label for="column">
        Kolom
    </label>

    <input
        type="number"
        id="column"
        name="column"
        min="1"
        value="{{ old('column', $copy->column ?? '') }}">

</div>