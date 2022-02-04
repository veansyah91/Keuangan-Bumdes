<?php

namespace Database\Factories;

use App\Models\Income;
use Illuminate\Database\Eloquent\Factories\Factory;

class IncomeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */

    protected $model = Income::class;

    public function definition()
    {
        return [
            'keterangan' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),
            'jumlah' => $this->faker->numberBetween($min = 100000, $max = 10000000),
            'tanggal_masuk' => $this->faker->date($format = 'Y-m-d', $startDate = '1 years', $endDate = '5 years')
        ];
    }
}
