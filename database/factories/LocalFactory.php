<?php

namespace Database\Factories;

use App\Models\Local;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocalFactory extends Factory
{
    protected $model = Local::class;

    public function definition()
    {
        return [
            'nome' => $this->faker->company,
            'setor' => $this->faker->word,
            'responsavel' => $this->faker->name,
            'telefone' => $this->faker->phoneNumber,
            'email' => $this->faker->safeEmail,
            'descricao' => $this->faker->sentence,
            'ativo' => true,
        ];
    }
} 