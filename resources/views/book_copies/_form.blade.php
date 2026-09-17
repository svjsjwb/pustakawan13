@if($errors->any())

<div class="alert alert-danger">

    <ul>

        @foreach($errors->all() as $error)

        <li>
            {{ $error }}
        </li>

        @endforeach

    </ul>

</div>

@endif


{{-- =========================================================
     BARCODE
========================================================= --}}

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


{{-- =========================================================
     STATUS
========================================================= --}}

<div class="form-group">

    <label for="status">
        Status
    </label>

    <select
        name="status"
        id="status"
        required>

        <option
            value="available"
            @selected(
            old( 'status' ,
            $copy->status ?? 'available'
            ) === 'available'
            )
            >
            Tersedia
        </option>

        <option
            value="reserved"
            @selected(
            old( 'status' ,
            $copy->status ?? ''
            ) === 'reserved'
            )
            >
            Direservasi
        </option>

        <option
            value="borrowed"
            @selected(
            old( 'status' ,
            $copy->status ?? ''
            ) === 'borrowed'
            )
            >
            Dipinjam
        </option>

        <option
            value="lost"
            @selected(
            old( 'status' ,
            $copy->status ?? ''
            ) === 'lost'
            )
            >
            Hilang
        </option>

        <option
            value="damaged"
            @selected(
            old( 'status' ,
            $copy->status ?? ''
            ) === 'damaged'
            )
            >
            Rusak
        </option>

        <option
            value="maintenance"
            @selected(
            old( 'status' ,
            $copy->status ?? ''
            ) === 'maintenance'
            )
            >
            Maintenance
        </option>

    </select>

</div>


{{-- =========================================================
     LOKASI BUKU
========================================================= --}}

<h3>
    Lokasi Buku
</h3>


{{-- =========================================================
     RAK
========================================================= --}}

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
                )
                >

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


{{-- =========================================================
     SECTION
========================================================= --}}

<div class="form-group">

    <label for="section">
        Section Rak
    </label>

    <select
        name="section"
        id="section"
        required>

        <option
            value="1"
            @selected(
            old( 'section' ,
            $copy->section ?? 1
            ) == 1
            )
            >
            Section 1 — A-01
        </option>

        <option
            value="2"
            @selected(
            old( 'section' ,
            $copy->section ?? 1
            ) == 2
            )
            >
            Section 2 — A-02
        </option>

    </select>

</div>


{{-- =========================================================
     MUKA RAK
========================================================= --}}

<div class="form-group">

    <label for="side">
        Muka Rak
    </label>

    <select
        name="side"
        id="side"
        required>

        <option
            value="front"
            @selected(
            old( 'side' ,
            $copy->side ?? 'front'
            ) === 'front'
            )
            >
            Depan
        </option>

        <option
            value="back"
            @selected(
            old( 'side' ,
            $copy->side ?? 'front'
            ) === 'back'
            )
            >
            Belakang
        </option>

    </select>

</div>


{{-- =========================================================
     BARIS
========================================================= --}}

<div class="form-group">

    <label for="row">
        Baris
    </label>

    <input
        type="number"
        id="row"
        name="row"
        min="1"
        max="3"
        value="{{ old('row', $copy->row ?? '') }}"
        placeholder="1 - 3">

</div>


{{-- =========================================================
     KOLOM
========================================================= --}}

<div class="form-group">

    <label for="column">
        Kolom
    </label>

    <input
        type="number"
        id="column"
        name="column"
        min="1"
        max="30"
        value="{{ old('column', $copy->column ?? '') }}"
        placeholder="1 - 30">

</div>