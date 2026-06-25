<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\MerchantRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleSheetsSyncService
{
    /**
     * Sinkronisasi data dari Google Sheets API (JSON)
     *
     * @param string $url URL Web App API Google Sheets
     * @return int Jumlah data yang berhasil disinkronisasi
     */
    public function sync(string $url = null): int
    {
        // Jika tidak ada URL, kita bisa menggunakan URL mock/dummy
        // atau lempar exception
        if (!$url) {
            $url = config('services.google_sheets.api_url'); 
        }

        if (!$url) {
            throw new \Exception("URL API Google Sheets belum dikonfigurasi.");
        }

        // Ambil data JSON
        $response = Http::get($url);

        if (!$response->successful()) {
            throw new \Exception("Gagal mengambil data dari Google Sheets.");
        }

        $data = $response->json();
        
        // Asumsikan data berupa array di key 'data' atau array langsung
        $transactions = $data['data'] ?? $data;

        if (!is_array($transactions)) {
            throw new \Exception("Format data tidak valid.");
        }

        $count = 0;
        $rules = MerchantRule::all();

        foreach ($transactions as $item) {
            // Validasi data minimal
            if (empty($item['id']) || empty($item['merchant'])) {
                continue;
            }

            $merchant = $item['merchant'];
            $kategori = 'Uncategorized';
            $status = 'Needs Review';

            // 1. Blocker Logic untuk Ojek Online (Grab, Gojek, GoPay)
            $isBlocked = Str::contains(strtolower($merchant), ['grab', 'gojek', 'gopay']);

            if ($isBlocked) {
                $kategori = 'Uncategorized';
                $status = 'Needs Review';
            } else {
                // 2. Smart Categorization berdasarkan merchant_rules
                foreach ($rules as $rule) {
                    if (Str::contains(strtolower($merchant), strtolower($rule->keyword))) {
                        $kategori = $rule->kategori;
                        $status = 'Reviewed';
                        break;
                    }
                }
            }

            // Check if transaction was manually edited
            $existing = Transaction::where('id', $item['id'])->first();
            if ($existing && $existing->is_manually_edited) {
                continue; // Skip this item to preserve local edits
            }

            // 3. Simpan atau Update transaksi ke database
            Transaction::updateOrCreate(
                ['id' => $item['id']], // kondisi pencarian berdasarkan id unik Google Sheets
                [
                    'tanggal'  => date('Y-m-d H:i:s', strtotime($item['tanggal'] ?? now())),
                    'nominal'  => (int) ($item['nominal'] ?? 0),
                    'arus'     => $item['arus'] ?? 'Out',
                    'merchant' => $merchant,
                    'sumber'   => $item['sumber'] ?? 'Unknown',
                    'kategori' => $kategori,
                    'status'   => $status,
                ]
            );

            $count++;
        }

        return $count;
    }
}
