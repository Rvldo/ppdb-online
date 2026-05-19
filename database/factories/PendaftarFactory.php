<?php

namespace Database\Factories;

use App\Models\Jurusan;
use App\Models\Pendaftar;
use Illuminate\Database\Eloquent\Factories\Factory;

class PendaftarFactory extends Factory
{
    protected $model = Pendaftar::class;

    public function definition(): array
    {
        $gender = fake()->randomElement(['L', 'P']);
        $firstName = $gender === 'L' ? fake()->firstNameMale() : fake()->firstNameFemale();
        $namaLengkap = $firstName . ' ' . fake()->lastName();

        return [
            'no_pendaftaran' => 'PPDB-' . date('Y') . '-' . str_pad((string) fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'nama_lengkap' => $namaLengkap,
            'nisn' => fake()->unique()->numerify('##########'),
            'nik' => fake()->numerify('################'),
            'jenis_kelamin' => $gender,
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-19 years', '-15 years')->format('Y-m-d'),
            'agama' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'alamat' => fake()->address(),
            'no_hp' => '08' . fake()->numerify('##########'),
            'email' => fake()->unique()->safeEmail(),
            'asal_sekolah' => 'SMP ' . fake()->randomElement(['Negeri 1', 'Negeri 2', 'Negeri 3', 'Muhammadiyah', 'Kristen Petra', 'Tunas Bangsa']) . ' ' . fake()->city(),
            'tahun_lulus' => (string) (date('Y') - 0),
            'nilai_rata_rata' => fake()->randomFloat(2, 70, 95),
            'nama_ayah' => 'Bp. ' . fake()->firstNameMale() . ' ' . fake()->lastName(),
            'nama_ibu' => 'Ibu ' . fake()->firstNameFemale() . ' ' . fake()->lastName(),
            'pekerjaan_ayah' => fake()->randomElement(['Wiraswasta', 'PNS', 'Karyawan Swasta', 'Petani', 'Buruh', 'TNI/Polri']),
            'pekerjaan_ibu' => fake()->randomElement(['Ibu Rumah Tangga', 'Wiraswasta', 'PNS', 'Karyawan Swasta', 'Guru']),
            'no_hp_ortu' => '08' . fake()->numerify('##########'),
            'jurusan_id' => Jurusan::inRandomOrder()->value('id') ?? Jurusan::factory(),
            'status' => fake()->randomElement(['pending', 'pending', 'pending', 'diterima', 'diterima', 'ditolak']),
            'catatan_admin' => null,
            'berkas_path' => null,
        ];
    }

    public function diterima(): self
    {
        return $this->state(fn () => ['status' => 'diterima']);
    }

    public function ditolak(): self
    {
        return $this->state(fn () => ['status' => 'ditolak', 'catatan_admin' => 'Berkas tidak lengkap.']);
    }
}
