<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    const POSITIONS = [
        'Lawyer',
        'Content manager',
        'Security',
        'Designer',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (self::POSITIONS as $position) {
            Position::factory()
                ->create([
                    'name' => $position,
                ]);
        }
    }
}
