/* =========================================================
   REPORT DETAIL MODAL
   Membuka detail grafik dari report card
========================================================= */

function openReportDetail(card) {

    const modal = document.getElementById('reportDetailModal');

    if (!modal) {
        console.error(
            'Element #reportDetailModal tidak ditemukan.'
        );

        return;
    }


    /*
     * Ambil data dari attribute <article>
     */

    const reportTitle =
        card.dataset.report || 'Laporan';


    const reportValue =
        card.dataset.value || '0';


    const reportValueLabel =
        card.dataset.valueLabel || 'data';


    const labels =
        card.dataset.labels
            ? card.dataset.labels.split('|')
            : [];


    const bars =
        card.dataset.bars
            ? card.dataset.bars
                .split('|')
                .map(Number)
            : [];


    /*
     * =====================================================
     * JUDUL LAPORAN
     * =====================================================
     */

    const titleElement =
        document.getElementById(
            'modalReportTitle'
        );


    if (titleElement) {

        titleElement.textContent =
            reportTitle;

    }


    /*
     * =====================================================
     * NILAI UTAMA
     * =====================================================
     */

    const valueElement =
        document.getElementById(
            'modalReportValue'
        );


    if (valueElement) {

        valueElement.textContent =
            reportValue;

    }


    /*
     * =====================================================
     * LABEL NILAI
     * =====================================================
     */

    const valueLabelElement =
        document.getElementById(
            'modalReportValueLabel'
        );


    if (valueLabelElement) {

        valueLabelElement.textContent =
            reportValueLabel;

    }


    /*
     * =====================================================
     * CONTAINER GRAFIK
     * =====================================================
     */

    const chartContainer =
        document.getElementById(
            'modalChartBars'
        );


    if (!chartContainer) {

        console.error(
            'Element #modalChartBars tidak ditemukan.'
        );

        return;

    }


    /*
     * Bersihkan grafik sebelumnya
     */

    chartContainer.innerHTML = '';


    /*
     * =====================================================
     * BUAT BAR GRAFIK
     * =====================================================
     */

    bars.forEach(
        function (bar, index) {

            /*
             * Column
             */

            const column =
                document.createElement(
                    'div'
                );


            column.className =
                'modal-chart-column';


            /*
             * Bar
             */

            const barElement =
                document.createElement(
                    'div'
                );


            barElement.className =
                'modal-chart-bar';


            /*
             * Pastikan tinggi tidak lebih dari 100%
             */

            const safeBar =
                Math.max(
                    0,
                    Math.min(
                        Number(bar) || 0,
                        100
                    )
                );


            barElement.style.height =
                safeBar + '%';


            /*
             * Bar terakhir aktif
             */

            if (
                index === bars.length - 1
            ) {

                column.classList.add(
                    'active'
                );

            }


            /*
             * Angka di atas bar
             */

            const number =
                document.createElement(
                    'span'
                );


            number.className =
                'modal-chart-number';


            number.textContent =
                bar;


            /*
             * Label bulan / kategori
             */

            const label =
                document.createElement(
                    'span'
                );


            label.className =
                'modal-chart-label';


            label.textContent =
                labels[index] || '';


            /*
             * Masukkan elemen ke column
             */

            column.appendChild(
                number
            );


            column.appendChild(
                barElement
            );


            column.appendChild(
                label
            );


            /*
             * Masukkan column ke grafik
             */

            chartContainer.appendChild(
                column
            );

        }
    );


    /*
     * =====================================================
     * TAMPILKAN MODAL
     * =====================================================
     */

    modal.classList.add(
        'active'
    );


    /*
     * Kunci scroll halaman belakang
     */

    document.body.classList.add(
        'report-modal-open'
    );

}


/* =========================================================
   CLOSE REPORT DETAIL
========================================================= */

function closeReportDetail(event) {

    /*
     * Kalau klik isi modal,
     * jangan tutup modal.
     */

    if (
        event &&
        event.target &&
        event.currentTarget &&
        event.target !== event.currentTarget
    ) {

        return;

    }


    const modal =
        document.getElementById(
            'reportDetailModal'
        );


    if (!modal) {

        return;

    }


    /*
     * Tutup modal
     */

    modal.classList.remove(
        'active'
    );


    /*
     * Aktifkan kembali scroll halaman
     */

    document.body.classList.remove(
        'report-modal-open'
    );

}


/* =========================================================
   ESC KEY
========================================================= */

document.addEventListener(
    'keydown',
    function (event) {

        if (
            event.key === 'Escape'
        ) {

            closeReportDetail();

        }

    }
);