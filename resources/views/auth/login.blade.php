<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - PT Arung Laut Nusantara</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-coral: #FF8B8B;
            --primary-coral-dark: #FF6B6B;
            --bg-cream: #FFF5F5;
            --bg-white: #FFFFFF;
            --text-dark: #2D3748;
            --text-gray: #718096;
            --text-light: #A0AEC0;
            --border-light: #F7FAFC;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 20px rgba(0, 0, 0, 0.06);
        }
        
        body {
            background: linear-gradient(135deg, #FFE5E5 0%, #FFD1D1 50%, #FFB8B8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated particles background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.2) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(255, 255, 255, 0.15) 0%, transparent 40%);
            pointer-events: none;
        }

        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }

        .particle:nth-child(1) {
            width: 4px;
            height: 4px;
            top: 10%;
            left: 15%;
            animation-delay: 0s;
            animation-duration: 12s;
        }

        .particle:nth-child(2) {
            width: 6px;
            height: 6px;
            top: 60%;
            left: 80%;
            animation-delay: 2s;
            animation-duration: 15s;
        }

        .particle:nth-child(3) {
            width: 3px;
            height: 3px;
            top: 40%;
            left: 10%;
            animation-delay: 4s;
            animation-duration: 18s;
        }

        .particle:nth-child(4) {
            width: 5px;
            height: 5px;
            top: 80%;
            left: 70%;
            animation-delay: 1s;
            animation-duration: 14s;
        }

        .particle:nth-child(5) {
            width: 4px;
            height: 4px;
            top: 20%;
            left: 85%;
            animation-delay: 3s;
            animation-duration: 16s;
        }

        .particle:nth-child(6) {
            width: 7px;
            height: 7px;
            top: 70%;
            left: 20%;
            animation-delay: 5s;
            animation-duration: 20s;
        }

        .particle.line {
            width: 40px;
            height: 2px;
            border-radius: 2px;
            background: rgba(255, 255, 255, 0.4);
            animation: floatRotate 20s infinite ease-in-out;
        }

        .particle.line:nth-child(7) {
            top: 15%;
            right: 10%;
            animation-delay: 0s;
        }

        .particle.line:nth-child(8) {
            top: 85%;
            left: 30%;
            animation-delay: 3s;
            width: 60px;
        }

        .particle.line:nth-child(9) {
            top: 50%;
            right: 25%;
            animation-delay: 6s;
            width: 30px;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            50% {
                transform: translate(50px, -80px);
                opacity: 0.8;
            }
            90% {
                opacity: 1;
            }
        }

        @keyframes floatRotate {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.6;
            }
            50% {
                transform: translate(-30px, -60px) rotate(180deg);
                opacity: 0.8;
            }
            90% {
                opacity: 0.6;
            }
        }
        
        .login-container {
            max-width: 1000px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            background: var(--bg-white);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.12);
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.96);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .login-left {
            padding: 60px 50px;
            background: var(--bg-white);
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 60px;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: var(--primary-coral);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .logo-icon::before,
        .logo-icon::after {
            content: '';
            position: absolute;
            background: white;
            border-radius: 50%;
        }

        .logo-icon::before {
            width: 8px;
            height: 8px;
            top: 8px;
            left: 8px;
        }

        .logo-icon::after {
            width: 6px;
            height: 6px;
            bottom: 8px;
            right: 8px;
        }

        .logo-icon i {
            color: white;
            font-size: 18px;
            z-index: 1;
        }

        .company-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .welcome-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
            letter-spacing: -0.03em;
        }

        .welcome-subtitle {
            font-size: 14px;
            color: var(--text-gray);
            margin-bottom: 40px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--border-light);
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            color: var(--text-dark);
            background: var(--bg-cream);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }

        .form-input::placeholder {
            color: var(--text-light);
        }

        .form-input:focus {
            background: white;
            border-color: var(--primary-coral);
            box-shadow: 0 0 0 4px rgba(255, 139, 139, 0.1);
            transform: translateY(-1px);
        }

        .form-input.is-invalid {
            border-color: #FC8181;
            background: #FFF5F5;
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: var(--primary-coral);
        }

        .password-toggle i {
            font-size: 16px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            border: 2px solid #E2E8F0;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s;
            accent-color: var(--primary-coral);
        }

        .form-check-input:checked {
            background-color: var(--primary-coral);
            border-color: var(--primary-coral);
        }

        .form-check-label {
            font-size: 14px;
            color: var(--text-gray);
            cursor: pointer;
            user-select: none;
        }

        .forgot-password {
            font-size: 14px;
            color: var(--text-gray);
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-password:hover {
            color: var(--primary-coral);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: var(--primary-coral);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 14px rgba(255, 139, 139, 0.3);
        }

        .btn-login:hover {
            background: var(--primary-coral-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(255, 139, 139, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }
        
        .login-right {
            background: linear-gradient(135deg, #FFB4B4 0%, #FF9A9A 50%, #FF8585 100%);
            padding: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .login-right::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 30% 40%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 70% 60%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            animation: drift 20s infinite ease-in-out;
        }

        @keyframes drift {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            50% {
                transform: translate(-20px, -20px) rotate(5deg);
            }
        }

        .illustration {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .illustration-circles {
            position: relative;
            width: 300px;
            height: 300px;
            margin: 0 auto;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            animation: pulse 3s infinite ease-in-out;
        }

        .circle-1 {
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            animation-delay: 0s;
        }

        .circle-2 {
            width: 70%;
            height: 70%;
            background: rgba(255, 255, 255, 0.25);
            top: 15%;
            left: 15%;
            animation-delay: 0.5s;
        }

        .circle-3 {
            width: 40%;
            height: 40%;
            background: rgba(255, 255, 255, 0.3);
            top: 30%;
            left: 30%;
            animation-delay: 1s;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        .decorative-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }

        .deco-line {
            position: absolute;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 2px;
            animation: floatLine 15s infinite ease-in-out;
        }

        .deco-line:nth-child(1) {
            width: 80px;
            height: 3px;
            top: 20%;
            right: 15%;
            animation-delay: 0s;
        }

        .deco-line:nth-child(2) {
            width: 60px;
            height: 3px;
            bottom: 30%;
            left: 10%;
            animation-delay: 2s;
        }

        .deco-line:nth-child(3) {
            width: 100px;
            height: 3px;
            top: 60%;
            right: 10%;
            animation-delay: 4s;
        }

        .deco-dot {
            position: absolute;
            width: 8px;
            height: 8px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: floatDot 12s infinite ease-in-out;
        }

        .deco-dot:nth-child(4) {
            top: 15%;
            left: 20%;
            animation-delay: 1s;
        }

        .deco-dot:nth-child(5) {
            bottom: 25%;
            right: 25%;
            animation-delay: 3s;
        }

        .deco-dot:nth-child(6) {
            top: 70%;
            left: 15%;
            width: 6px;
            height: 6px;
            animation-delay: 5s;
        }

        @keyframes floatLine {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
                opacity: 0.4;
            }
            50% {
                transform: translate(-15px, -25px) rotate(10deg);
                opacity: 0.7;
            }
        }

        @keyframes floatDot {
            0%, 100% {
                transform: translate(0, 0);
                opacity: 0.5;
            }
            50% {
                transform: translate(20px, -30px);
                opacity: 1;
            }
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            border: none;
            animation: slideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #C6F6D5;
            color: #22543D;
        }

        .alert-danger {
            background: #FED7D7;
            color: #742A2A;
        }

        .alert i {
            font-size: 16px;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            margin-left: auto;
            color: inherit;
            opacity: 0.5;
            transition: opacity 0.2s;
            padding: 0;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-close:hover {
            opacity: 1;
        }

        .invalid-feedback {
            display: block;
            color: #FC8181;
            font-size: 13px;
            margin-top: 6px;
        }

        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 450px;
            }

            .login-right {
                display: none;
            }

            .login-left {
                padding: 40px 30px;
            }

            .welcome-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Animated particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle line"></div>
        <div class="particle line"></div>
        <div class="particle line"></div>
    </div>

    <div class="login-container">
        <!-- Left Side - Login Form -->
        <div class="login-left">
            <div class="logo-section">
                <div class="logo-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <div class="company-name">PT ARUNG LAUT NUSANTARA</div>
            </div>

            <h1 class="welcome-title">Welcome to Employee Attendance</h1>
            <p class="welcome-subtitle">Please login to your account</p>

            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                    <button type="button" class="btn-close" onclick="this.parentElement.remove()">×</button>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           class="form-input @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           placeholder="example@gmail.com"
                           value="{{ old('email') }}"
                           required 
                           autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input type="password" 
                               class="form-input @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="••••••••"
                               required>
                        <button type="button" class="password-toggle" id="togglePassword">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-options">
                    <div class="checkbox-wrapper">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="remember" 
                               id="remember"
                               {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="btn-login">
                    Login
                </button>
            </form>
        </div>

        <!-- Right Side - Illustration -->
        <div class="login-right">
            <div class="decorative-elements">
                <div class="deco-line"></div>
                <div class="deco-line"></div>
                <div class="deco-line"></div>
                <div class="deco-dot"></div>
                <div class="deco-dot"></div>
                <div class="deco-dot"></div>
            </div>
            <div class="illustration">
                <div class="illustration-circles">
                    <div class="circle circle-1"></div>
                    <div class="circle circle-2"></div>
                    <div class="circle circle-3"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('bi-eye');
                eyeIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('bi-eye-slash');
                eyeIcon.classList.add('bi-eye');
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.3s, transform 0.3s';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);

        // Add subtle animation to form inputs
        const inputs = document.querySelectorAll('.form-input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'translateY(-2px)';
            });
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>