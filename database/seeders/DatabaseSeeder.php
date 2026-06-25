<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        \App\Models\Transaction::create([
            'id' => \Illuminate\Support\Str::uuid(), 
            'tanggal' => now()->subDays(2), 
            'nominal' => 50000, 
            'arus' => 'Out', 
            'merchant' => 'Starbucks', 
            'sumber' => 'BCA', 
            'kategori' => 'Uncategorized', 
            'status' => 'Needs Review'
        ]);

        \App\Models\Transaction::create([
            'id' => \Illuminate\Support\Str::uuid(), 
            'tanggal' => now()->subDays(1), 
            'nominal' => 25000, 
            'arus' => 'Out', 
            'merchant' => 'GrabRide', 
            'sumber' => 'BCA', 
            'kategori' => 'Uncategorized', 
            'status' => 'Needs Review'
        ]);

        \App\Models\Transaction::create([
            'id' => \Illuminate\Support\Str::uuid(), 
            'tanggal' => now()->subDays(1), 
            'nominal' => 15000, 
            'arus' => 'Out', 
            'merchant' => 'Indomaret', 
            'sumber' => 'Line Bank', 
            'kategori' => 'Uncategorized', 
            'status' => 'Needs Review'
        ]);

        \App\Models\Transaction::create([
            'id' => \Illuminate\Support\Str::uuid(), 
            'tanggal' => now(), 
            'nominal' => 5000000, 
            'arus' => 'In', 
            'merchant' => 'Gaji PT Makmur', 
            'sumber' => 'BCA', 
            'kategori' => 'Income', 
            'status' => 'Reviewed'
        ]);
    }
}
