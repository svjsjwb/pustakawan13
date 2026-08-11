<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>

    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="login-page">

        <div class="login-background"></div>

        <div class="login-panel">

            <img
                src="{{ asset('images/logo-tiga-serangkai.png') }}"
                alt="Tiga Serangkai"
                class="login-logo">

            <div class="login-card">

                <h1>WELCOME!!</h1>

                <p>Selamat menjelajah jendela dunia.</p>

                <form method="POST" action="{{ route('login.store') }}">

                    @csrf

                    <div class="form-group">
                        <label>Username</label>

                        <input
                            type="text"
                            name="email"
                            placeholder="Enter username or email address"
                            required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>

                        <div class="password-input">
                            <input
                                type="password"
                                name="password"
                                placeholder="Enter Password"
                                required>
                        </div>
                    </div>

                    <div class="forgot">
                        <a href="#">Lupa Password?</a>
                    </div>

                    <button type="submit">
                        LOGIN
                    </button>

                </form>

            </div>

        </div>

    </div>

</body>

</html>