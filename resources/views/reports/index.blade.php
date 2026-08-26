@extends('layouts.app')

@section('title', 'Laporan')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/report.css') }}"
    >
@endpush


@section('content')

<section class="report-page">

    {{-- =========================================================
         HERO
    ========================================================== --}}

    <div class="report-hero">

        <div class="report-hero-content">

            <div class="report-label">
                <span class="label-dot"></span>
                Pusat Laporan
            </div>

            <h1>
                Laporan Perpustakaan
            </h1>

            <p>
                Pantau aktivitas perpustakaan melalui ringkasan
                peminjaman, koleksi, keterlambatan, dan anggota aktif.
            </p>

        </div>


        <div class="report-hero-action">

            <a
                href="{{ route('reports.create') }}"
                class="report-add-btn"
            >

                <span class="add-icon">
                    +
                </span>

                <span>
                    Tambah Laporan
                </span>

            </a>

        </div>

    </div>


    {{-- =========================================================
         SUCCESS
    ========================================================== --}}

    @if(session('success'))

        <div class="report-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- =========================================================
         SECTION TITLE
    ========================================================== --}}

    <div class="report-section-title">

        <div>

            <span>
                Ringkasan Aktivitas
            </span>

            <h2>
                Laporan Terbaru
            </h2>

        </div>


        {{-- =====================================================
             CALENDAR
        ====================================================== --}}

        <div class="report-calendar-wrapper">

            <button
                type="button"
                class="report-calendar-trigger"
                id="calendarTrigger"
            >

                <span class="calendar-trigger-icon">
                    📅
                </span>

                <span class="calendar-trigger-content">

                    <small>
                        Periode laporan
                    </small>

                    <strong id="calendarTriggerText">
                        {{ $periodLabel }}
                    </strong>

                </span>

                <span class="calendar-trigger-arrow">
                    ▾
                </span>

            </button>


            {{-- =================================================
                 CALENDAR POPUP
            ================================================== --}}

            <div
                class="report-calendar-popup"
                id="reportCalendarPopup"
            >

                <div class="calendar-popup-header">

                    <div>

                        <span>
                            Pilih Periode
                        </span>

                        <strong id="calendarSelectedLabel">
                            {{ $periodLabel }}
                        </strong>

                    </div>


                    <button
                        type="button"
                        class="calendar-close"
                        id="calendarClose"
                    >
                        ×
                    </button>

                </div>


                {{-- MODE --}}

                <div class="calendar-mode">

                    <button
                        type="button"
                        class="calendar-mode-btn
                        {{ $periodType === 'day'
                            ? 'active'
                            : '' }}"
                        data-mode="day"
                    >
                        Harian
                    </button>


                    <button
                        type="button"
                        class="calendar-mode-btn
                        {{ $periodType === 'week'
                            ? 'active'
                            : '' }}"
                        data-mode="week"
                    >
                        Mingguan
                    </button>


                    <button
                        type="button"
                        class="calendar-mode-btn
                        {{ $periodType === 'month'
                            ? 'active'
                            : '' }}"
                        data-mode="month"
                    >
                        Bulanan
                    </button>

                </div>


                {{-- NAVIGATION --}}

                <div class="calendar-navigation">

                    <button
                        type="button"
                        id="calendarPrev"
                    >
                        ‹
                    </button>


                    <strong id="calendarMonthTitle">
                        -
                    </strong>


                    <button
                        type="button"
                        id="calendarNext"
                    >
                        ›
                    </button>

                </div>


                {{-- WEEKDAY --}}

                <div class="calendar-weekdays">

                    <span>Sen</span>
                    <span>Sel</span>
                    <span>Rab</span>
                    <span>Kam</span>
                    <span>Jum</span>
                    <span>Sab</span>
                    <span>Min</span>

                </div>


                {{-- DAYS --}}

                <div
                    class="calendar-days"
                    id="calendarDays"
                ></div>


                {{-- INFO --}}

                <div class="calendar-info">

                    <span class="calendar-info-dot"></span>

                    <span id="calendarInfo">
                        Pilih tanggal
                    </span>

                </div>


                {{-- FORM --}}

                <form
                    method="GET"
                    action="{{ url('/laporan') }}"
                    id="calendarForm"
                >

                    <input
                        type="hidden"
                        name="range_type"
                        id="rangeTypeInput"
                        value="{{ $periodType }}"
                    >


                    <input
                        type="hidden"
                        name="selected_date"
                        id="selectedDateInput"
                        value="{{ $selectedDate }}"
                    >


                    <button
                        type="submit"
                        class="calendar-apply"
                    >
                        Terapkan Periode
                    </button>

                </form>

            </div>

        </div>

    </div>


    {{-- =========================================================
         REPORT GRID
    ========================================================== --}}

    <div class="report-grid">

        @forelse($reports as $report)

            @php

                $jenis =
                    $report['jenis'];


                /*
                 * =================================================
                 * PEMINJAMAN
                 * =================================================
                 */

                if (
                    str_contains(
                        $jenis,
                        'Peminjaman'
                    )
                ) {

                    $type =
                        'borrow';

                    $category =
                        'Sirkulasi';

                    $icon =
                        '📖';

                    $valNum =
                        $borrowedBooks ?? 0;

                    $badge =
                        number_format(
                            $valNum,
                            0,
                            ',',
                            '.'
                        )
                        . ' Dipinjam';

                    $badgeClass =
                        'positive';

                    $value =
                        number_format(
                            $valNum,
                            0,
                            ',',
                            '.'
                        );

                    $valueLabel =
                        'peminjaman';

                    $description =
                        'Rekap transaksi peminjaman dan pengembalian buku selama periode yang dipilih.';

                    $bars =
                        $borrowChartBars ?? [];

                    $labels =
                        $borrowChartLabels ?? [];

                }


                /*
                 * =================================================
                 * KETERLAMBATAN
                 * =================================================
                 */

                elseif (
                    str_contains(
                        $jenis,
                        'Keterlambatan'
                    )
                ) {

                    $type =
                        'late';

                    $category =
                        'Monitoring';

                    $icon =
                        '⏰';

                    $valNum =
                        $lateBorrowings ?? 0;

                    $badge =
                        number_format(
                            $valNum,
                            0,
                            ',',
                            '.'
                        )
                        . ' Kasus';

                    $badgeClass =
                        $valNum > 0
                            ? 'warning'
                            : 'neutral';

                    $value =
                        number_format(
                            $valNum,
                            0,
                            ',',
                            '.'
                        );

                    $valueLabel =
                        'kasus aktif';

                    $description =
                        'Daftar aktivitas buku yang terlambat dikembalikan pada periode yang dipilih.';

                    $bars =
                        $lateChartBars ?? [];

                    $labels =
                        $lateChartLabels ?? [];

                }


                /*
                 * =================================================
                 * KOLEKSI
                 * =================================================
                 */

                elseif (
                    str_contains(
                        $jenis,
                        'Koleksi'
                    )
                ) {

                    $type =
                        'collection';

                    $category =
                        'Koleksi';

                    $icon =
                        '📚';

                    $valNum =
                        $totalBooks ?? 0;

                    $badge =
                        number_format(
                            $valNum,
                            0,
                            ',',
                            '.'
                        )
                        . ' Buku';

                    $badgeClass =
                        'neutral';

                    $value =
                        number_format(
                            $valNum,
                            0,
                            ',',
                            '.'
                        );

                    $valueLabel =
                        'total koleksi';

                    $description =
                        'Rekap buku yang tercatat dalam periode laporan yang dipilih.';

                    $bars =
                        $collectionChartBars ?? [];

                    $labels =
                        $collectionChartLabels ?? [];

                }


                /*
                 * =================================================
                 * ANGGOTA
                 * =================================================
                 */

                else {

                    $type =
                        'member';

                    $category =
                        'Keanggotaan';

                    $icon =
                        '👥';

                    $valNum =
                        $activeMembers ?? 0;

                    $badge =
                        number_format(
                            $valNum,
                            0,
                            ',',
                            '.'
                        )
                        . ' Aktif';

                    $badgeClass =
                        'positive';

                    $value =
                        number_format(
                            $valNum,
                            0,
                            ',',
                            '.'
                        );

                    $valueLabel =
                        'anggota aktif';

                    $description =
                        'Rekap anggota aktif yang tercatat dalam periode laporan yang dipilih.';

                    $bars =
                        $memberChartBars ?? [];

                    $labels =
                        $memberChartLabels ?? [];

                }

            @endphp


            {{-- =================================================
                 CARD
            ================================================== --}}

            <article
                class="report-card"
                onclick="openReportDetail(this)"

                data-report="{{ $report['jenis'] }}"

                data-value="{{ $value }}"

                data-value-label="{{ $valueLabel }}"

                data-period="{{ $periodLabel }}"

                data-labels="{{ implode('|', $labels) }}"

                data-bars="{{ implode('|', $bars) }}"
            >


                {{-- CARD TOP --}}

                <div class="report-card-top">

                    <div
                        class="report-card-icon {{ $type }}"
                    >
                        {{ $icon }}
                    </div>


                    <div class="report-card-info">

                        <span
                            class="report-card-category"
                        >
                            {{ $category }}
                        </span>

                        <h3>
                            {{ $report['jenis'] }}
                        </h3>

                    </div>


                    <span
                        class="report-badge {{ $badgeClass }}"
                    >
                        {{ $badge }}
                    </span>

                </div>


                {{-- DESCRIPTION --}}

                <p class="report-description">
                    {{ $description }}
                </p>


                {{-- CHART --}}

                <div class="report-chart-wrapper">

                    <div class="chart-value">

                        <strong>
                            {{ $value }}
                        </strong>

                        <span>
                            {{ $valueLabel }}
                        </span>

                    </div>


                    <div class="report-chart">

                        <div class="chart-line"></div>
                        <div class="chart-line"></div>
                        <div class="chart-line"></div>


                        <div class="chart-bars">

                            @foreach(
                                $bars
                                as $index => $bar
                            )

                                <div
                                    class="chart-column
                                    {{ $index === count($bars) - 1
                                        ? 'active'
                                        : '' }}"
                                >

                                    <div
                                        class="chart-bar"
                                        style="
                                            height:
                                            {{ $bar }}%;
                                        "
                                    ></div>

                                    <span>
                                        {{ $labels[$index] ?? '' }}
                                    </span>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>


                {{-- FOOTER --}}

                <div
                    class="report-card-footer"
                    onclick="event.stopPropagation()"
                >

                    <span class="report-updated">

                        ●
                        Periode:
                        {{ $periodLabel }}

                    </span>


                    <div
                        class="report-actions"
                        onclick="event.stopPropagation()"
                    >

                        <a
                            href="{{ route(
                                'reports.edit',
                                $report['id']
                            ) }}"
                            class="report-detail-btn"
                        >
                            Edit
                        </a>


                        <select
                            class="report-format-select"
                            aria-label="Pilih format laporan"
                            onclick="event.stopPropagation()"
                        >

                            <option value="pdf">
                                PDF
                            </option>

                            <option value="excel">
                                Excel
                            </option>

                        </select>


                        <div
                            class="report-download"
                            onclick="event.stopPropagation()"
                        >

                            <button
                                type="button"
                                class="report-download-btn"
                                onclick="downloadReport(this)"
                            >
                                Unduh
                            </button>

                        </div>

                    </div>

                </div>

            </article>

        @empty

            <div class="report-empty">

                <strong>
                    Belum ada laporan
                </strong>

                <span>
                    Klik "Tambah Laporan" untuk membuat laporan baru.
                </span>

            </div>

        @endforelse

    </div>

</section>


{{-- =========================================================
     MODAL DETAIL
========================================================= --}}

<div
    id="reportDetailModal"
    class="report-modal"
    onclick="closeReportDetail(event)"
>

    <div
        class="report-modal-content"
        onclick="event.stopPropagation()"
    >

        <div class="report-modal-header">

            <div>

                <span class="report-modal-label">
                    DETAIL LAPORAN
                </span>

                <h2 id="modalReportTitle">
                    Laporan
                </h2>

            </div>


            <button
                type="button"
                class="report-modal-close"
                onclick="closeReportDetail()"
            >
                ×
            </button>

        </div>


        <div class="report-modal-summary">

            <div class="report-modal-value">

                <strong id="modalReportValue">
                    0
                </strong>

                <span id="modalReportValueLabel">
                    data
                </span>

            </div>


            <div
                class="report-modal-period"
                id="modalReportPeriod"
            >
                Periode:
                {{ $periodLabel }}
            </div>

        </div>


        <div class="report-detail-chart">

            <div class="detail-chart-lines">

                <span></span>
                <span></span>
                <span></span>
                <span></span>

            </div>


            <div
                id="detailChartBars"
                class="detail-chart-bars"
            ></div>

        </div>


        <div class="report-modal-footer">

            <span>
                Data laporan berdasarkan periode
                <strong>
                    {{ $periodLabel }}
                </strong>.
            </span>


            <button
                type="button"
                onclick="closeReportDetail()"
            >
                Tutup
            </button>

        </div>

    </div>

</div>


<script src="{{ asset('js/openReportDetail.js') }}"></script>


{{-- =========================================================
     CALENDAR JAVASCRIPT
========================================================= --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const popup =
            document.getElementById(
                'reportCalendarPopup'
            );

        const trigger =
            document.getElementById(
                'calendarTrigger'
            );

        const closeButton =
            document.getElementById(
                'calendarClose'
            );

        const daysContainer =
            document.getElementById(
                'calendarDays'
            );

        const monthTitle =
            document.getElementById(
                'calendarMonthTitle'
            );

        const selectedLabel =
            document.getElementById(
                'calendarSelectedLabel'
            );

        const triggerText =
            document.getElementById(
                'calendarTriggerText'
            );

        const info =
            document.getElementById(
                'calendarInfo'
            );

        const rangeTypeInput =
            document.getElementById(
                'rangeTypeInput'
            );

        const selectedDateInput =
            document.getElementById(
                'selectedDateInput'
            );

        const modeButtons =
            document.querySelectorAll(
                '.calendar-mode-btn'
            );

        const previousButton =
            document.getElementById(
                'calendarPrev'
            );

        const nextButton =
            document.getElementById(
                'calendarNext'
            );


        let currentMode =
            rangeTypeInput.value ||
            'month';


        let selectedDate =
            parseDate(
                selectedDateInput.value
            );


        let currentMonth =
            new Date(
                selectedDate.getFullYear(),
                selectedDate.getMonth(),
                1
            );


        /*
         * ========================================================
         * OPEN
         * ========================================================
         */

        trigger.addEventListener(
            'click',
            function (event) {

                event.stopPropagation();

                popup.classList.toggle(
                    'show'
                );

            }
        );


        /*
         * ========================================================
         * CLOSE
         * ========================================================
         */

        closeButton.addEventListener(
            'click',
            function () {

                popup.classList.remove(
                    'show'
                );

            }
        );


        document.addEventListener(
            'click',
            function (event) {

                if (
                    !popup.contains(
                        event.target
                    )
                    &&
                    !trigger.contains(
                        event.target
                    )
                ) {

                    popup.classList.remove(
                        'show'
                    );

                }

            }
        );


        /*
         * ========================================================
         * MODE
         * ========================================================
         */

        modeButtons.forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        currentMode =
                            this.dataset.mode;


                        rangeTypeInput.value =
                            currentMode;


                        modeButtons.forEach(
                            function (item) {

                                item.classList.remove(
                                    'active'
                                );

                            }
                        );


                        this.classList.add(
                            'active'
                        );


                        renderCalendar();

                    }
                );

            }
        );


        /*
         * ========================================================
         * PREVIOUS MONTH
         * ========================================================
         */

        previousButton.addEventListener(
            'click',
            function () {

                currentMonth.setMonth(
                    currentMonth.getMonth() - 1
                );

                renderCalendar();

            }
        );


        /*
         * ========================================================
         * NEXT MONTH
         * ========================================================
         */

        nextButton.addEventListener(
            'click',
            function () {

                currentMonth.setMonth(
                    currentMonth.getMonth() + 1
                );

                renderCalendar();

            }
        );


        /*
         * ========================================================
         * RENDER
         * ========================================================
         */

        function renderCalendar()
        {
            daysContainer.innerHTML = '';


            const year =
                currentMonth.getFullYear();

            const month =
                currentMonth.getMonth();


            const firstDay =
                new Date(
                    year,
                    month,
                    1
                );


            /*
             * Senin = 0
             * Minggu = 6
             */
            let startDay =
                firstDay.getDay() - 1;


            if (startDay < 0) {
                startDay = 6;
            }


            const daysInMonth =
                new Date(
                    year,
                    month + 1,
                    0
                ).getDate();


            const previousMonthDays =
                new Date(
                    year,
                    month,
                    0
                ).getDate();


            monthTitle.textContent =
                formatMonth(
                    currentMonth
                );


            /*
             * TANGGAL BULAN SEBELUMNYA
             */

            for (
                let i = startDay - 1;
                i >= 0;
                i--
            ) {

                const day =
                    previousMonthDays - i;


                const date =
                    new Date(
                        year,
                        month - 1,
                        day
                    );


                createDay(
                    date,
                    true
                );

            }


            /*
             * TANGGAL BULAN SEKARANG
             */

            for (
                let day = 1;
                day <= daysInMonth;
                day++
            ) {

                const date =
                    new Date(
                        year,
                        month,
                        day
                    );


                createDay(
                    date,
                    false
                );

            }


            /*
             * TANGGAL BULAN BERIKUTNYA
             */

            const totalCells =
                startDay +
                daysInMonth;


            const remaining =
                Math.ceil(
                    totalCells / 7
                ) * 7
                -
                totalCells;


            for (
                let day = 1;
                day <= remaining;
                day++
            ) {

                const date =
                    new Date(
                        year,
                        month + 1,
                        day
                    );


                createDay(
                    date,
                    true
                );

            }


            updateInformation();
        }


        /*
         * ========================================================
         * CREATE DAY
         * ========================================================
         */

        function createDay(
            date,
            otherMonth
        ) {

            const element =
                document.createElement(
                    'button'
                );


            element.type =
                'button';


            element.className =
                'calendar-day';


            element.textContent =
                date.getDate();


            if (otherMonth) {

                element.classList.add(
                    'other-month'
                );

            }


            /*
             * TODAY
             */

            if (
                isSameDate(
                    date,
                    new Date()
                )
            ) {

                element.classList.add(
                    'today'
                );

            }


            /*
             * HARIAN
             */

            if (
                currentMode === 'day'
                &&
                isSameDate(
                    date,
                    selectedDate
                )
            ) {

                element.classList.add(
                    'selected'
                );

            }


            /*
             * MINGGUAN
             */

            if (
                currentMode === 'week'
                &&
                isDateInSelectedWeek(
                    date
                )
            ) {

                element.classList.add(
                    'week-range'
                );


                if (
                    isSameDate(
                        date,
                        getWeekStart(
                            selectedDate
                        )
                    )
                ) {

                    element.classList.add(
                        'week-start'
                    );

                }


                if (
                    isSameDate(
                        date,
                        getWeekEnd(
                            selectedDate
                        )
                    )
                ) {

                    element.classList.add(
                        'week-end'
                    );

                }


                if (
                    isSameDate(
                        date,
                        selectedDate
                    )
                ) {

                    element.classList.add(
                        'week-selected'
                    );

                }

            }


            /*
             * BULANAN
             */

            if (
                currentMode === 'month'
                &&
                date.getMonth()
                    ===
                selectedDate.getMonth()
                &&
                date.getFullYear()
                    ===
                selectedDate.getFullYear()
            ) {

                element.classList.add(
                    'month-selected'
                );

            }


            /*
             * KLIK
             */

            element.addEventListener(
                'click',
                function () {

                    selectedDate =
                        new Date(
                            date.getFullYear(),
                            date.getMonth(),
                            date.getDate()
                        );


                    selectedDateInput.value =
                        formatDate(
                            selectedDate
                        );


                    currentMonth =
                        new Date(
                            selectedDate.getFullYear(),
                            selectedDate.getMonth(),
                            1
                        );


                    renderCalendar();

                }
            );


            daysContainer.appendChild(
                element
            );
        }


        /*
         * ========================================================
         * INFORMATION
         * ========================================================
         */

        function updateInformation()
        {
            let text = '';


            /*
             * HARIAN
             */

            if (
                currentMode === 'day'
            ) {

                text =
                    '1 hari • ' +
                    formatLongDate(
                        selectedDate
                    );

            }


            /*
             * MINGGUAN
             */

            else if (
                currentMode === 'week'
            ) {

                const start =
                    getWeekStart(
                        selectedDate
                    );


                const end =
                    getWeekEnd(
                        selectedDate
                    );


                text =
                    '1 minggu • ' +
                    formatShortDate(
                        start
                    )
                    +
                    ' - ' +
                    formatShortDate(
                        end
                    );

            }


            /*
             * BULANAN
             */

            else {

                const start =
                    new Date(
                        selectedDate.getFullYear(),
                        selectedDate.getMonth(),
                        1
                    );


                const end =
                    new Date(
                        selectedDate.getFullYear(),
                        selectedDate.getMonth() + 1,
                        0
                    );


                text =
                    '1 bulan • ' +
                    formatShortDate(
                        start
                    )
                    +
                    ' - ' +
                    formatShortDate(
                        end
                    );

            }


            info.textContent =
                text;


            selectedLabel.textContent =
                text;


            triggerText.textContent =
                text;
        }


        /*
         * ========================================================
         * WEEK START
         * ========================================================
         */

        function getWeekStart(
            date
        ) {

            const result =
                new Date(date);


            const day =
                result.getDay();


            const difference =
                day === 0
                    ? -6
                    : 1 - day;


            result.setDate(
                result.getDate()
                +
                difference
            );


            result.setHours(
                0,
                0,
                0,
                0
            );


            return result;
        }


        /*
         * ========================================================
         * WEEK END
         * ========================================================
         */

        function getWeekEnd(
            date
        ) {

            const start =
                getWeekStart(
                    date
                );


            const result =
                new Date(start);


            result.setDate(
                start.getDate()
                +
                6
            );


            result.setHours(
                23,
                59,
                59,
                999
            );


            return result;
        }


        /*
         * ========================================================
         * CHECK WEEK
         * ========================================================
         */

        function isDateInSelectedWeek(
            date
        ) {

            const start =
                getWeekStart(
                    selectedDate
                );


            const end =
                getWeekEnd(
                    selectedDate
                );


            return (
                date >= start
                &&
                date <= end
            );
        }


        /*
         * ========================================================
         * PARSE DATE
         * ========================================================
         */

        function parseDate(
            value
        ) {

            if (!value) {
                return new Date();
            }


            const parts =
                value.split('-');


            return new Date(
                Number(parts[0]),
                Number(parts[1]) - 1,
                Number(parts[2])
            );
        }


        /*
         * ========================================================
         * FORMAT DATE
         * ========================================================
         */

        function formatDate(
            date
        ) {

            const year =
                date.getFullYear();


            const month =
                String(
                    date.getMonth() + 1
                ).padStart(
                    2,
                    '0'
                );


            const day =
                String(
                    date.getDate()
                ).padStart(
                    2,
                    '0'
                );


            return (
                year +
                '-' +
                month +
                '-' +
                day
            );
        }


        /*
         * ========================================================
         * FORMAT SHORT
         * ========================================================
         */

        function formatShortDate(
            date
        ) {

            return date.toLocaleDateString(
                'id-ID',
                {
                    day: '2-digit',
                    month: 'short'
                }
            );
        }


        /*
         * ========================================================
         * FORMAT LONG
         * ========================================================
         */

        function formatLongDate(
            date
        ) {

            return date.toLocaleDateString(
                'id-ID',
                {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                }
            );
        }


        /*
         * ========================================================
         * FORMAT MONTH
         * ========================================================
         */

        function formatMonth(
            date
        ) {

            return date.toLocaleDateString(
                'id-ID',
                {
                    month: 'long',
                    year: 'numeric'
                }
            );
        }


        /*
         * ========================================================
         * SAME DATE
         * ========================================================
         */

        function isSameDate(
            first,
            second
        ) {

            return (
                first.getFullYear()
                    ===
                second.getFullYear()

                &&

                first.getMonth()
                    ===
                second.getMonth()

                &&

                first.getDate()
                    ===
                second.getDate()
            );
        }


        /*
         * ========================================================
         * INITIAL RENDER
         * ========================================================
         */

        renderCalendar();

    });

</script>


{{-- =========================================================
     PERIOD UPDATE MODAL
========================================================= --}}

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const modalPeriod =
            document.getElementById(
                'modalReportPeriod'
            );


        if (modalPeriod) {

            modalPeriod.textContent =
                'Periode: {{ $periodLabel }}';

        }

    }
);

</script>


@endsection