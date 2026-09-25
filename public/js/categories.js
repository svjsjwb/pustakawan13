document.addEventListener('DOMContentLoaded', function () {

    const level1 = document.getElementById('category_level_1');
    const level2 = document.getElementById('category_level_2');
    const level3 = document.getElementById('category_level_3');

    if (!level1) return;

    const data = {

        "Pendidikan": {
            "SD": [],
            "SMP": [],
            "SMA": []
        },

        "Anak-anak": {
            "Fiksi": [],
            "Non Fiksi": []
        },

        "Remaja": {
            "Fiksi": [],
            "Non Fiksi": []
        },

        "Dewasa": {
            "Fiksi": [],
            "Non Fiksi": []
        }

    };

    level1.addEventListener('change', function () {

        level2.innerHTML =
            '<option value="">Pilih Subkategori</option>';

        level3.innerHTML =
            '<option value="">Pilih Detail</option>';

        if (!data[this.value]) return;

        Object.keys(data[this.value]).forEach(item => {

            level2.innerHTML +=
                `<option value="${item}">${item}</option>`;

        });

    });

    level2.addEventListener('change', function () {

        level3.innerHTML =
            '<option value="">Pilih Detail</option>';

        const parent = level1.value;

        data[parent][this.value].forEach(item => {

            level3.innerHTML +=
                `<option value="${item}">${item}</option>`;

        });

    });

});