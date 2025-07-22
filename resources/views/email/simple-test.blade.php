
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Email Sederhana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Test Email Sederhana</div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ url('email123/send-simple-test') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="email">Email Tujuan</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <small class="form-text text-muted">Masukkan email yang ingin ditest</small>
                            </div>
                            <button type="submit" class="btn btn-primary">Kirim Test Email</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
