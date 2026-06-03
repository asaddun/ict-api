<x-layout>
    <style>
        .login-card {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            background: #fff;
        }
    </style>

    <div class="container">
        <div class="login-card">
            <form method="POST" action="{{ route('auth.login') }}">
                @csrf
                <div class="mb-3">
                    <input type="text" name="email" class="form-control" id="email" placeholder="Email" autofocus>
                </div>
                <div class="mb-3">
                    <div class="position-relative">
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Password">
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3" role="button"
                            onclick="togglePassword()">
                            <i class="far fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById("password");
            const toggleIcon = document.getElementById("toggleIcon");

            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            toggleIcon.classList.toggle("fa-eye");
            toggleIcon.classList.toggle("fa-eye-slash");
        }
    </script>
</x-layout>
