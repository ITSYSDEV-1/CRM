<?php

namespace App\Console\Commands;

use App\Models\ProfileFolio;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Traits\LogsSystemCommand;  // Import trait logging

class UpdateStatusCommand extends Command
{
    use LogsSystemCommand;  // Gunakan trait logging

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updatestatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status folio menjadi "Out" untuk transaksi yang sudah lewat 3 hari dari tanggal check-out';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Mulai pencatatan waktu mulai eksekusi command
        $this->startLogging();

        try {
            $updatedTransactions = 0;
            $updatedProfiles = 0;

            // Ambil transaksi dengan dateco lebih dari 3 hari lalu dan status belum 'O'
            $transactions = ProfileFolio::where('dateco', '<', Carbon::now()->subDays(3)->format('Y-m-d'))
                ->where(function ($q) {
                    $q->where('foliostatus', '<>', 'O')->orWhereNull('foliostatus');
                })->get();

            foreach ($transactions as $transaction) {
                $profilesfolio = ProfileFolio::where('foliostatus', '<>', 'O')
                    ->where('folio', '=', $transaction->resv_id)
                    ->get();

                if ($profilesfolio) {
                    foreach ($profilesfolio as $prof) {
                        ProfileFolio::where('profileid', '=', $prof->profileid)->update(['foliostatus' => 'O']);
                        $updatedProfiles++;
                    }
                }

                $transaction->foliostatus = "O";
                $transaction->save();
                $updatedTransactions++;
            }

            // Tambahkan konteks log untuk jumlah data yang diupdate
            $this->addLogContext('updated_transactions', $updatedTransactions);
            $this->addLogContext('updated_profiles', $updatedProfiles);

        } catch (\Exception $e) {
            // Tandai log sebagai gagal dan simpan pesan error
            $this->markFailed($e->getMessage());
            $this->error('Error updating statuses: ' . $e->getMessage());
        }

        // Simpan log akhir eksekusi command
        $this->logCommandEnd('updatestatus', 'Update status folio ke "O" untuk transaksi lama');
    }
}
