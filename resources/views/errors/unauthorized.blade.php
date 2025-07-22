<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unauthorized | {{ config('app.name', 'CRM') }}</title>
    <!-- Bootstrap -->
    <link href="{{ asset('vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- Custom Theme Style -->
    <style>
        body {
            background: #f7f7f7;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #73879C;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            max-width: 500px;
            padding: 40px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
        }
        .error-icon {
            font-size: 72px;
            color: #E74C3C;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #34495E;
        }
        .lead {
            font-size: 18px;
            margin-bottom: 30px;
            color: #7F8C8D;
        }
        .btn {
            border-radius: 3px;
            font-weight: 600;
            padding: 10px 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #3498DB;
            border-color: #3498DB;
        }
        .btn-primary:hover {
            background-color: #2980B9;
            border-color: #2980B9;
        }
        .btn-default {
            background-color: #95A5A6;
            border-color: #95A5A6;
            color: white;
        }
        .btn-default:hover {
            background-color: #7F8C8D;
            border-color: #7F8C8D;
            color: white;
        }
        .btn-success {
            background-color: #2ECC71;
            border-color: #2ECC71;
        }
        .btn-success:hover {
            background-color: #27AE60;
            border-color: #27AE60;
        }
        .hotel-logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        @if(isset($configuration->hotel_logo) && !empty($configuration->hotel_logo))
            <img src="{{ asset('images/'.$configuration->hotel_logo) }}" alt="Hotel Logo" class="hotel-logo">
        @endif
        
        <div class="error-icon">
            <i class="fa fa-exclamation-triangle"></i>
        </div>
        
        <h1>Access Denied</h1>
        
        <p class="lead">You don't have permission to access this page.</p>
        
        <p>Please contact it.helpdesk@rcoid.com</p>
        
        <div class="actions" style="margin-top: 30px;">
            <a href="{{ url('/') }}" class="btn btn-primary btn-block">
                <i class="fa fa-home"></i> Go to Dashboard
            </a>
            
            <form method="POST" action="{{ route('logout') }}" style="margin-top: 10px;">
                @csrf
                <button type="submit" class="btn btn-default btn-block">
                    <i class="fa fa-sign-out"></i> Logout
                </button>
            </form>
            
            <a href="{{ route('login.form') }}" class="btn btn-success btn-block" style="margin-top: 10px;">
                <i class="fa fa-user"></i> Login as Different User
            </a>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('vendors/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>
</body>
</html>