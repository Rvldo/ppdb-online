<?php

namespace Database\Seeders;

use App\Models\Pendaftar;
use Illuminate\Database\Seeder;

class DummyPendaftarSeeder extends Seeder
{
    public function run(): void
    {
        Pendaftar::factory(30)->create();
    }
}
