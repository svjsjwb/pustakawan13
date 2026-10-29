<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lengkapi Data Akun</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-user-select: none;
            user-select: none;
        }

        body {
            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            height: 100vh;
            height: 100dvh;
            min-height: 0;
            overflow: hidden;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(135deg,
                    #eef8f8 0%,
                    #f7fbfb 50%,
                    #e9f4f4 100%);

            color: #17324d;

            padding: clamp(8px, 2vh, 30px) 20px;
        }


        /* =====================================================
           AUTH WRAPPER
        ===================================================== */

        .auth-wrapper {
            width: 100%;
            max-width: 500px;
        }


        /* =====================================================
           BRAND
        ===================================================== */

        .brand {
            text-align: center;
            margin-bottom: 24px;
        }

        .brand-icon {
            width: 80px;
            height: 80px;

            margin: 0 auto 14px;

            border-radius: 20px;
            overflow: hidden;

            background: #278889;

            display: flex;
            align-items: center;
            justify-content: center;

            box-shadow:
                0 8px 22px rgba(39, 136, 137, 0.22);
        }

        .brand-logo {
            display: block;
            width: 64px;
            height: 64px;
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .brand-name {
            font-size: 20px;
            font-weight: 800;

            letter-spacing: 0.3px;

            color: #17324d;
        }

        .brand-subtitle {
            margin-top: 4px;

            font-size: 13px;
            font-weight: 500;

            color: #5f7182;

            letter-spacing: 0.5px;
        }


        /* =====================================================
           CARD
        ===================================================== */

        .auth-card {
            width: 100%;

            background: #ffffff;

            border-radius: 20px;

            padding: 34px;

            box-shadow:
                0 28px 70px rgba(23, 50, 77, 0.22),
                0 8px 20px rgba(39, 136, 137, 0.12);

            border: 1px solid rgba(39, 136, 137, 0.24);
        }


        /* =====================================================
           HEADER
        ===================================================== */

        .auth-header {
            margin-bottom: 28px;
        }

        .auth-header h1 {
            font-size: 28px;

            line-height: 1.25;

            color: #17324d;

            margin-bottom: 8px;
        }

        .auth-header p {
            font-size: 14px;
            font-weight: 500;

            line-height: 1.6;

            color: #5f7182;
        }


        /* =====================================================
           FORM
        ===================================================== */

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;

            margin-bottom: 8px;

            font-size: 14px;

            font-weight: 700;

            color: #304b63;
        }

        .required {
            color: #dc5555;
        }


        .form-control,
        .form-select {
            width: 100%;

            height: 48px;

            padding: 0 14px;

            border: 1px solid #d8e2e8;

            border-radius: 10px;

            background: #ffffff;

            color: #243b53;

            font-size: 14px;

            outline: none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background 0.2s ease;
        }


        .form-control:focus,
        .form-select:focus {
            border-color: #278889;

            box-shadow:
                0 0 0 3px rgba(39, 136, 137, 0.10);
        }


        .form-control[readonly] {
            background: #f5f8fa;

            color: #526779;

            cursor: not-allowed;
        }


        .form-select {
            cursor: pointer;

            appearance: auto;
        }

        .division-select-trigger {
            width: 100%;
            height: 48px;
            padding: 0 14px;
            border: 1px solid #d8e2e8;
            border-radius: 10px;
            background: #ffffff;
            color: #243b53;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font: inherit;
            font-size: 14px;
            text-align: left;
            cursor: pointer;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .division-select-trigger.is-placeholder {
            color: #718096;
            font-family: Inter, "Segoe UI Variable", system-ui, sans-serif;
            font-weight: 500;
            letter-spacing: 0.1px;
        }

        .division-select-trigger:hover,
        .division-select-trigger[aria-expanded="true"],
        .division-select-trigger:focus-visible {
            border-color: #278889;
            box-shadow: 0 0 0 3px rgba(39, 136, 137, 0.10);
            outline: none;
        }

        .division-select-trigger svg {
            width: 18px;
            height: 18px;
            flex: 0 0 auto;
            color: #718096;
            transition: transform 0.16s ease;
        }

        .division-select-trigger[aria-expanded="true"] svg {
            transform: rotate(180deg);
        }

        .form-select.division-native-select {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            padding: 0 !important;
            margin: -1px !important;
            overflow: hidden !important;
            clip: rect(0, 0, 0, 0) !important;
            clip-path: inset(50%) !important;
            white-space: nowrap !important;
            border: 0 !important;
        }

        .division-options {
            position: fixed;
            z-index: 10000;
            display: none;
            overflow-y: auto;
            overscroll-behavior: contain;
            padding: 6px;
            border: 1px solid #d8e2e8;
            border-radius: 12px;
            background: #ffffff;
            box-shadow: 0 14px 32px rgba(24, 60, 75, 0.16);
            opacity: 0;
            transform: translateY(-4px);
        }

        .division-options[hidden] {
            display: none !important;
        }

        .division-options.is-open {
            display: block;
            animation: division-options-in 140ms ease-out both;
        }

        @keyframes division-options-in {
            from { opacity: 0; transform: translateY(-4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .division-option {
            width: 100%;
            min-height: 40px;
            padding: 9px 11px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #304b63;
            display: flex;
            align-items: center;
            gap: 12px;
            font: inherit;
            font-size: 14px;
            text-align: left;
            cursor: pointer;
            transition: background 0.12s ease, color 0.12s ease;
        }

        .division-select-trigger,
        .division-option {
            -webkit-user-select: none;
            user-select: none;
        }

        .division-option:hover,
        .division-option:focus-visible {
            background: #eaf6f5;
            color: #217475;
            outline: none;
        }

        .division-option[aria-selected="true"] {
            background: #eef8f8;
            color: #217475;
            font-weight: 700;
        }

        @media (prefers-reduced-motion: reduce) {
            .division-select-trigger,
            .division-select-trigger svg,
            .division-option {
                transition: none;
            }

            .division-options.is-open {
                animation: none;
            }
        }


        /* =====================================================
           ERROR
        ===================================================== */

        .error-message {
            display: block;

            margin-top: 7px;

            font-size: 12px;

            color: #dc5555;
        }


        /* =====================================================
           INFO
        ===================================================== */

        .approval-info {
            display: flex;

            align-items: flex-start;

            gap: 10px;

            margin-top: 4px;
            margin-bottom: 24px;

            padding: 13px 14px;

            background: #eef8f8;

            border: 1px solid #d5eeee;

            border-radius: 10px;

            color: #41636b;
        }

        .approval-icon {
            flex-shrink: 0;

            width: 20px;
            height: 20px;

            border-radius: 50%;

            background: #278889;

            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 12px;
            font-weight: 700;
        }

        .approval-info p {
            font-size: 13px;
            font-weight: 500;

            line-height: 1.6;
        }

        .approval-info strong {
            color: #278889;
        }


        /* =====================================================
           BUTTON
        ===================================================== */

        .auth-button {
            width: 100%;

            height: 48px;

            border: none;

            border-radius: 10px;

            background: #278889;

            color: #ffffff;

            font-size: 14px;

            font-weight: 700;

            cursor: pointer;

            transition:
                background 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .auth-button:hover {
            background: #217475;

            box-shadow:
                0 7px 18px rgba(39, 136, 137, 0.20);

            transform: translateY(-1px);
        }

        .auth-button:active {
            transform: translateY(0);
        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .auth-footer {
            text-align: center;

            margin-top: 20px;
            position: relative;
            top: 7px;

            font-size: 12px;
            font-weight: 500;

            color: #718096;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 576px) {

            body {
                height: auto;
                min-height: 100vh;
                min-height: 100dvh;
                overflow-x: hidden;
                overflow-y: auto;
                padding: max(8px, env(safe-area-inset-top)) max(15px, env(safe-area-inset-right)) max(8px, env(safe-area-inset-bottom)) max(15px, env(safe-area-inset-left));
            }

            .auth-card {
                padding: 26px 22px;

                border-radius: 16px;
            }

            .auth-header h1 {
                font-size: 24px;
            }

            .brand-name {
                font-size: 18px;
            }

            .auth-footer {
                top: 3px;
            }

        }

        @media (max-height: 700px) {
            .brand {
                margin-bottom: 14px;
            }

            .brand-icon {
                width: 64px;
                height: 64px;
                margin-bottom: 8px;
            }

            .auth-card {
                padding: 22px;
            }

            .auth-header {
                margin-bottom: 16px;
            }

            .form-group {
                margin-bottom: 12px;
            }

            .approval-info {
                margin-bottom: 16px;
                padding: 11px 12px;
            }
        }
    </style>

</head>


<body>

    <div class="auth-wrapper">


        {{-- =================================================
             BRAND
        ================================================== --}}

        <div class="brand">

            <div class="brand-icon">
                <img class="brand-logo" src="{{ asset('images/logo-ts-white.png') }}" alt="Logo Tiga Serangkai" draggable="false">
            </div>

            <div class="brand-name">
                PERPUSTAKAAN TIGA SERANGKAI
            </div>

            <div class="brand-subtitle">
                SISTEM INFORMASI PERPUSTAKAAN
            </div>

        </div>


        {{-- =================================================
             CARD
        ================================================== --}}

        <div class="auth-card">


            {{-- HEADER --}}

            <div class="auth-header">

                <h1>
                    Lengkapi Data Akun
                </h1>

                <p>
                    Lengkapi data berikut untuk menyelesaikan
                    pendaftaran melalui Google.
                </p>

            </div>


            {{-- FORM --}}

            <form method="POST" action="{{ route('google.complete.store') }}">

                @csrf


                {{-- NAMA --}}

                <div class="form-group">

                    <label for="name">
                        Nama
                    </label>

                    <input type="text" id="name" class="form-control" value="{{ $googleRegistration['name'] }}"
                        readonly>

                </div>


                {{-- EMAIL --}}

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input type="email" id="email" class="form-control" value="{{ $googleRegistration['email'] }}"
                        readonly>

                </div>


                {{-- DIVISI --}}

                <div class="form-group">

                    <label for="division">

                        Divisi

                        <span class="required">
                            *
                        </span>

                    </label>

                    <select name="division" id="division" class="form-select" required>
                        <option value="">Pilih divisi</option>

                        <option value="Center Of Excellence">Center Of Excellence</option>
                        <option value="Digital Business">Digital Business</option>
                        <option value="E-Publishing">E-Publishing</option>
                        <option value="Finance">Finance</option>
                        <option value="General Trading">General Trading</option>
                        <option value="HR & GA">HR & GA</option>
                        <option value="HSE">HSE</option>
                        <option value="IQA">IQA</option>
                        <option value="IT">IT</option>
                        <option value="Marketing">Marketing</option>
                        <option value="MTIS Perpuskita dan Tisera">
                            MTIS Perpuskita dan Tisera
                        </option>
                        <option value="MTIS Planning and Development">
                            MTIS Planning and Development
                        </option>
                        <option value="People Development Center">
                            People Development Center
                        </option>
                        <option value="Production">Production</option>
                        <option value="School Book Sales">School Book Sales</option>
                        <option value="School Book Publishing">
                            School Book Publishing
                        </option>
                        <option value="SCM">SCM</option>
                        <option value="TAX">TAX</option>
                    </select>


                    @error('division')
                        <small class="error-message">
                            {{ $message }}
                        </small>
                    @enderror

                </div>


                {{-- INFO APPROVAL --}}

                <div class="approval-info">

                    <div class="approval-icon">
                        i
                    </div>

                    <p>

                        Setelah pendaftaran selesai,
                        akun Anda akan berstatus
                        <strong>menunggu persetujuan Admin.</strong>

                    </p>

                </div>


                {{-- BUTTON --}}

                <button type="submit" class="auth-button">
                    Lanjutkan Pendaftaran
                </button>

                <div class="auth-footer">
                    © {{ date('Y') }} Perpustakaan Tiga Serangkai
                </div>

            </form>


        </div>


    </div>

    <script>
        (function () {
            var select = document.getElementById('division');
            if (!select || !select.form) return;

            var label = document.querySelector('label[for="division"]');
            var trigger = document.createElement('button');
            var triggerText = document.createElement('span');
            var triggerIcon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            var iconPath = document.createElementNS('http://www.w3.org/2000/svg', 'polyline');
            var optionList = document.createElement('div');
            var optionButtons = [];
            var searchText = '';
            var searchTimer;

            trigger.type = 'button';
            trigger.id = 'division-trigger';
            trigger.className = 'division-select-trigger is-placeholder';
            trigger.setAttribute('role', 'combobox');
            trigger.setAttribute('aria-haspopup', 'listbox');
            trigger.setAttribute('aria-expanded', 'false');
            trigger.setAttribute('aria-controls', 'division-options');
            trigger.setAttribute('aria-required', 'true');
            trigger.appendChild(triggerText);

            triggerIcon.setAttribute('viewBox', '0 0 24 24');
            triggerIcon.setAttribute('fill', 'none');
            triggerIcon.setAttribute('stroke', 'currentColor');
            triggerIcon.setAttribute('stroke-width', '2');
            triggerIcon.setAttribute('stroke-linecap', 'round');
            triggerIcon.setAttribute('stroke-linejoin', 'round');
            triggerIcon.setAttribute('aria-hidden', 'true');
            iconPath.setAttribute('points', '6 9 12 15 18 9');
            triggerIcon.appendChild(iconPath);
            trigger.appendChild(triggerIcon);

            if (label) {
                label.id = 'division-label';
                label.htmlFor = trigger.id;
                trigger.setAttribute('aria-labelledby', label.id);
            }

            optionList.id = 'division-options';
            optionList.className = 'division-options';
            optionList.setAttribute('role', 'listbox');
            optionList.setAttribute('aria-label', 'Pilihan Divisi');
            optionList.hidden = true;

            Array.prototype.forEach.call(select.options, function (option) {
                if (!option.value) return;

                var optionButton = document.createElement('button');
                optionButton.type = 'button';
                optionButton.className = 'division-option';
                optionButton.setAttribute('role', 'option');
                optionButton.setAttribute('aria-selected', 'false');
                optionButton.tabIndex = -1;
                optionButton.textContent = option.textContent.trim();
                optionButton.addEventListener('click', function () {
                    select.value = option.value;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    syncSelection();
                    closeOptions(true);
                });
                optionButton.addEventListener('keydown', handleOptionKeydown);
                optionButtons.push(optionButton);
                optionList.appendChild(optionButton);
            });

            select.parentNode.insertBefore(trigger, select);
            select.classList.add('division-native-select');
            select.tabIndex = -1;
            select.setAttribute('aria-hidden', 'true');
            document.body.appendChild(optionList);

            function selectedIndex() {
                return optionButtons.findIndex(function (optionButton) {
                    return optionButton.getAttribute('aria-selected') === 'true';
                });
            }

            function syncSelection() {
                var selectedOption = select.options[select.selectedIndex];
                var hasValue = Boolean(selectedOption && selectedOption.value);
                triggerText.textContent = hasValue ? selectedOption.textContent.trim() : 'Pilih divisi';
                trigger.classList.toggle('is-placeholder', !hasValue);

                optionButtons.forEach(function (optionButton, index) {
                    var isSelected = select.options[index + 1].value === select.value && hasValue;
                    optionButton.setAttribute('aria-selected', String(isSelected));
                });
            }

            function positionOptions() {
                var rect = trigger.getBoundingClientRect();
                var viewportPadding = 12;
                var spaceBelow = window.innerHeight - rect.bottom - viewportPadding;
                var spaceAbove = rect.top - viewportPadding;
                var naturalHeight = Math.min(optionList.scrollHeight, 280);
                var openAbove = spaceBelow < naturalHeight && spaceAbove > spaceBelow;
                var availableSpace = openAbove ? spaceAbove : spaceBelow;
                var menuHeight = Math.max(0, Math.min(naturalHeight, availableSpace));
                var menuWidth = rect.width;
                var left = Math.max(viewportPadding, Math.min(rect.left, window.innerWidth - menuWidth - viewportPadding));

                optionList.style.width = menuWidth + 'px';
                optionList.style.maxHeight = menuHeight + 'px';
                optionList.style.left = left + 'px';
                optionList.style.top = (openAbove ? rect.top - menuHeight - 8 : rect.bottom + 8) + 'px';
            }

            function openOptions(focusIndex) {
                optionList.hidden = false;
                trigger.setAttribute('aria-expanded', 'true');
                optionList.classList.add('is-open');
                positionOptions();

                var targetIndex = selectedIndex();
                if (targetIndex < 0) targetIndex = focusIndex === 'last' ? optionButtons.length - 1 : 0;
                if (focusIndex === 'first') targetIndex = 0;
                if (focusIndex === 'last') targetIndex = optionButtons.length - 1;

                if (optionButtons[targetIndex]) {
                    optionButtons[targetIndex].focus();
                    optionButtons[targetIndex].scrollIntoView({ block: 'nearest' });
                }
            }

            function closeOptions(returnFocus) {
                optionList.classList.remove('is-open');
                optionList.hidden = true;
                trigger.setAttribute('aria-expanded', 'false');
                if (returnFocus) trigger.focus();
            }

            function handleOptionKeydown(event) {
                var currentIndex = optionButtons.indexOf(event.currentTarget);
                var nextIndex = currentIndex;

                if (event.key === 'ArrowDown') nextIndex = Math.min(currentIndex + 1, optionButtons.length - 1);
                else if (event.key === 'ArrowUp') nextIndex = Math.max(currentIndex - 1, 0);
                else if (event.key === 'Home') nextIndex = 0;
                else if (event.key === 'End') nextIndex = optionButtons.length - 1;
                else if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    optionButtons[currentIndex].click();
                    return;
                } else if (event.key === 'Escape') {
                    event.preventDefault();
                    closeOptions(true);
                    return;
                } else if (event.key === 'Tab') {
                    closeOptions(false);
                    return;
                } else if (event.key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
                    searchText += event.key.toLowerCase();
                    window.clearTimeout(searchTimer);
                    searchTimer = window.setTimeout(function () { searchText = ''; }, 600);
                    var matchIndex = optionButtons.findIndex(function (optionButton, index) {
                        return index > currentIndex && optionButton.textContent.toLowerCase().indexOf(searchText) === 0;
                    });
                    if (matchIndex < 0) {
                        matchIndex = optionButtons.findIndex(function (optionButton) {
                            return optionButton.textContent.toLowerCase().indexOf(searchText) === 0;
                        });
                    }
                    if (matchIndex >= 0) nextIndex = matchIndex;
                    else return;
                } else {
                    return;
                }

                event.preventDefault();
                if (optionButtons[nextIndex]) {
                    optionButtons[nextIndex].focus();
                    optionButtons[nextIndex].scrollIntoView({ block: 'nearest' });
                }
            }

            trigger.addEventListener('click', function () {
                if (trigger.getAttribute('aria-expanded') === 'true') closeOptions(false);
                else openOptions();
            });

            trigger.addEventListener('keydown', function (event) {
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp' || event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openOptions(event.key === 'ArrowUp' ? 'last' : undefined);
                }
            });

            select.addEventListener('invalid', function (event) {
                event.preventDefault();
                trigger.focus();
                openOptions();
            });

            select.addEventListener('change', syncSelection);
            document.addEventListener('click', function (event) {
                if (!trigger.contains(event.target) && !optionList.contains(event.target)) closeOptions(false);
            });
            window.addEventListener('resize', function () {
                if (!optionList.hidden) positionOptions();
            });
            window.addEventListener('scroll', function () {
                if (!optionList.hidden) positionOptions();
            }, true);

            syncSelection();
        })();
    </script>

</body>

</html>
