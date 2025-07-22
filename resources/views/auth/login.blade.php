<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | {{ $configuration->hotel_name.' '.$configuration->app_title }}</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background-color: #ffffff;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            padding: 15px 30px;
        }
        
        .header h1 {
            font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
            font-size: 18px;
            font-weight: 500;
            color: #2A3F54;
        }
        
        .main-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 50px 20px 40px 20px;
        }
        
        .login-container {
            background-color: #ffffff;
            border: 1px solid #d0d7de;
            border-radius: 6px;
            padding: 40px 60px;
            width: 100%;
            max-width: 800px;
            min-width: 700px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .login-title {
            background-color: #f6f8fa;
            margin: -40px -60px 30px -60px;
            padding: 15px 60px;
            border-bottom: 1px solid #d0d7de;
            border-radius: 6px 6px 0 0;
            font-size: 20px;
            font-weight: 600;
            color: #24292f;
        }
        
        .form-group {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 20px;
            border: 1px solid #d0d7de;
            border-radius: 4px;
            font-size: 16px;
            background-color: #ffffff;
            transition: border-color 0.2s;
            min-height: 44px;
            padding-right: 50px;
        }
        
        .form-label {
            display: block;
            font-size: 16px;
            color: #24292f;
            font-weight: 500;
            min-width: 120px;
            text-align: left;
            margin-bottom: 0;
        }
        
        .input-wrapper {
            flex: 1;
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            font-size: 18px;
            user-select: none;
            transition: color 0.2s;
        }
        
        .password-toggle:hover {
            color: #333;
        }
        
        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            margin-left: 130px;
        }
        
        .checkbox-container input[type="checkbox"] {
            margin-right: 8px;
        }
        
        .checkbox-container label {
            font-size: 14px;
            color: #24292f;
        }
        
        .button-container {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-left: 130px;
        }
        
        .login-button {
            background-color: #0969da;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            min-height: 36px;
            min-width: 80px;
        }
        
        .login-button:hover {
            background-color: #0550ae;
        }
        
        .forgot-password {
            margin: 0;
        }
        
        .forgot-password a {
            color: #0969da;
            text-decoration: none;
            font-size: 14px;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 5px;
            margin-left: 0;
            padding-left: 130px;
        }
        
        /* Responsive styles */
        @media screen and (max-width: 1024px) {
            .login-container {
                min-width: 600px;
                padding: 30px 40px;
            }
            
            .login-title {
                margin: -30px -40px 25px -40px;
                padding: 15px 40px;
            }
        }
        
        @media screen and (max-width: 768px) {
            .login-container {
                min-width: 0;
                max-width: 100%;
                padding: 25px 30px;
            }
            
            .login-title {
                margin: -25px -30px 20px -30px;
                padding: 12px 30px;
            }
            
            .form-group {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .form-label {
                margin-bottom: 8px;
            }
            
            .checkbox-container,
            .button-container,
            .error-message {
                margin-left: 0;
                padding-left: 0;
            }
            
            .button-container {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .login-button {
                width: 100%;
            }
        }
        
        @media screen and (max-width: 480px) {
            .main-container {
                padding: 30px 15px;
            }
            
            .login-container {
                padding: 20px 15px;
                border-radius: 4px;
            }
            
            .login-title {
                margin: -20px -15px 15px -15px;
                padding: 10px 15px;
                font-size: 18px;
            }
            
            .header h1 {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Jalak CRM {{ $configuration->hotel_name }}</h1>
    </div>
    
    <div class="main-container">
        <div class="login-container">
            <div class="login-title">Login</div>
            
            <form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" class="form-input{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" required>
                    </div>
                </div>
                @if ($errors->has('email'))
                    <div class="error-message">{{ $errors->first('email') }}</div>
                @endif
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-input{{ $errors->has('password') ? ' is-invalid' : '' }}" required>
                        <i class="fas fa-eye password-toggle" id="togglePassword" onclick="togglePassword()"></i>
                    </div>
                </div>
                @if ($errors->has('password'))
                    <div class="error-message">{{ $errors->first('password') }}</div>
                @endif
                
                <div class="checkbox-container">
                    <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">{{ __('Remember Me') }}</label>
                </div>
                
                <div class="button-container">
                    <button type="submit" class="login-button">{{ __('Login') }}</button>
                    
                    <div class="forgot-password">
                        <a href="#">Forgot Your Password?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePassword');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>