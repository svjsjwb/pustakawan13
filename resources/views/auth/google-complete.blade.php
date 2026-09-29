<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lengkapi Data Akun</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family:
                Inter,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;

            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            background:
                linear-gradient(135deg,
                    #eef8f8 0%,
                    #f7fbfb 50%,
                    #e9f4f4 100%);

            color: #17324d;

            padding: 30px 20px;
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
            width: 64px;
            height: 64px;

            margin: 0 auto 14px;

            border-radius: 18px;

            background: #278889;

            display: flex;
            align-items: center;
            justify-content: center;

            color: white;

            font-size: 23px;
            font-weight: 800;

            box-shadow:
                0 8px 22px rgba(39, 136, 137, 0.22);
        }

        .brand-name {
            font-size: 20px;
            font-weight: 800;

            letter-spacing: 0.3px;

            color: #17324d;
        }

        .brand-subtitle {
            margin-top: 4px;

            font-size: 12px;

            color: #718096;

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
                0 18px 50px rgba(24, 60, 75, 0.10);

            border: 1px solid rgba(39, 136, 137, 0.08);
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

            line-height: 1.6;

            color: #718096;
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
            font-size: 12px;

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

            margin-top: 22px;

            font-size: 11px;

            color: #8a9aaa;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 576px) {

            body {
                padding: 20px 15px;
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
                TS
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
                        <strong>
                            menunggu persetujuan Admin
                        </strong>.

                    </p>

                </div>


                {{-- BUTTON --}}

                <button type="submit" class="auth-button">
                    Lanjutkan Pendaftaran
                </button>

            </form>


        </div>


        {{-- FOOTER --}}

        <div class="auth-footer">
            © {{ date('Y') }} Perpustakaan Tiga Serangkai
        </div>


    </div>

</body>

</html>
