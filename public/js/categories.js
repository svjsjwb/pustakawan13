document.addEventListener('DOMContentLoaded', function () {

    const level1 = document.getElementById('category_level_1');
    const level2 = document.getElementById('category_level_2');
    const level3 = document.getElementById('category_level_3');

    if (!level1) return;

    const data = {

        "Pendidikan": {
            "SD": [
                "Kelas 1",
                "Kelas 2",
                "Kelas 3",
                "Kelas 4",
                "Kelas 5",
                "Kelas 6"
            ],
            "SMP": [
                "Kelas 7",
                "Kelas 8",
                "Kelas 9"
            ],
            "SMA": [
                "Kelas 10",
                "Kelas 11",
                "Kelas 12"
            ]
        },

        "Anak-anak": {
            "Dongeng": [
                "Fabel",
                "Legenda",
                "Cerita Rakyat"
            ],
            "Aktivitas": [
                "Mewarnai",
                "Puzzle",
                "Kerajinan"
            ]
        },

        "Remaja": {
            "Novel": [
                "Romance",
                "Fantasi",
                "Petualangan"
            ],
            "Pengembangan Diri": [
                "Motivasi",
                "Karier",
                "Public Speaking"
            ]
        },

        "Dewasa": {
            "Bisnis": [
                "Manajemen",
                "Marketing",
                "Keuangan"
            ],
            "Teknologi": [
                "Programming",
                "AI",
                "Data Science"
            ]
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