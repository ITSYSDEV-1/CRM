<?php
use Illuminate\Support\Facades\Config;

Route::get('/ftp-test', function () {
    $ftp_server = config('ftp.host');
    $ftp_user   = config('ftp.username');
    $ftp_pass   = config('ftp.password');

    $conn_id = ftp_connect($ftp_server, 21, 10);
    if (!$conn_id) {
        return "❌ Gagal konek ke FTP server";
    }

    if (!ftp_login($conn_id, $ftp_user, $ftp_pass)) {
        return "❌ Gagal login ke FTP";
    }

    ftp_pasv($conn_id, true);
    ftp_close($conn_id);
    return "✅ Sukses konek dan login ke FTP server";
});

?>
