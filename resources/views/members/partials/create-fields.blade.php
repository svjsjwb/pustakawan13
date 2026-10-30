<div class="member-create-form-section">
    <div class="member-create-section-title">
        <span>1</span>
        <h3>Informasi Pribadi</h3>
    </div>

    <div class="form-group">
        <label for="name">Nama <span>*</span></label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name') }}"
            placeholder="Masukkan nama anggota"
            autocomplete="name"
            required
        >
    </div>

    <div class="form-group">
        <label for="division">Divisi <span>*</span></label>
        <select id="division" name="division" required>
            <option value="">-- Pilih Divisi --</option>
            <option value="Center Of Excellence" {{ old('division') === 'Center Of Excellence' ? 'selected' : '' }}>Center Of Excellence</option>
            <option value="Digital Business" {{ old('division') === 'Digital Business' ? 'selected' : '' }}>Digital Business</option>
            <option value="E-Publishing" {{ old('division') === 'E-Publishing' ? 'selected' : '' }}>E-Publishing</option>
            <option value="Finance" {{ old('division') === 'Finance' ? 'selected' : '' }}>Finance</option>
            <option value="General Trading" {{ old('division') === 'General Trading' ? 'selected' : '' }}>General Trading</option>
            <option value="HR &amp; GA" {{ old('division') === 'HR & GA' ? 'selected' : '' }}>HR &amp; GA</option>
            <option value="HSE" {{ old('division') === 'HSE' ? 'selected' : '' }}>HSE</option>
            <option value="IQA" {{ old('division') === 'IQA' ? 'selected' : '' }}>IQA</option>
            <option value="IT" {{ old('division') === 'IT' ? 'selected' : '' }}>IT</option>
            <option value="Marketing" {{ old('division') === 'Marketing' ? 'selected' : '' }}>Marketing</option>
            <option value="MTIS Perpuskita dan Tisera" {{ old('division') === 'MTIS Perpuskita dan Tisera' ? 'selected' : '' }}>MTIS Perpuskita dan Tisera</option>
            <option value="MTIS Planning and Development" {{ old('division') === 'MTIS Planning and Development' ? 'selected' : '' }}>MTIS Planning and Development</option>
            <option value="People Development Center" {{ old('division') === 'People Development Center' ? 'selected' : '' }}>People Development Center</option>
            <option value="Production" {{ old('division') === 'Production' ? 'selected' : '' }}>Production</option>
            <option value="School Book Sales" {{ old('division') === 'School Book Sales' ? 'selected' : '' }}>School Book Sales</option>
            <option value="School Book Publishing" {{ old('division') === 'School Book Publishing' ? 'selected' : '' }}>School Book Publishing</option>
            <option value="SCM" {{ old('division') === 'SCM' ? 'selected' : '' }}>SCM</option>
            <option value="TAX" {{ old('division') === 'TAX' ? 'selected' : '' }}>TAX</option>
        </select>
    </div>
</div>

<div class="member-create-form-section">
    <div class="member-create-section-title">
        <span>2</span>
        <h3>Kontak</h3>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="Contoh: nama@email.com"
            autocomplete="email"
        >
    </div>

    <div class="form-group">
        <label for="phone">No. Telepon <span>*</span></label>
        <input
            type="text"
            id="phone"
            name="phone"
            value="{{ old('phone') }}"
            placeholder="Contoh: 081234567890"
            autocomplete="tel"
            required
        >
    </div>
</div>
