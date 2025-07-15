<?php

namespace Database\Factories;

use App\Models\Local;
use App\Models\Equipamento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Relatorio>
 */
class RelatorioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['Aberta', 'Em Andamento', 'Concluída', 'Cancelada']);
        $setores = ['Produção', 'Manutenção', 'Qualidade', 'Logística', 'TI', 'Administrativo'];
        $atividades = [
            'Manutenção Preventiva',
            'Manutenção Corretiva',
            'Inspeção de Qualidade',
            'Calibração de Equipamento',
            'Troca de Peças',
            'Limpeza Técnica',
            'Verificação de Segurança',
            'Atualização de Software',
            'Backup de Dados',
            'Teste de Funcionamento'
        ];
        
        $local = Local::inRandomOrder()->first();
        
        return [
            'titulo' => $this->faker->sentence(4),
            'sector' => $this->faker->randomElement($setores),
            'activity' => $this->faker->randomElement($atividades),
            'user_id' => 1, // Assumindo que existe um usuário com ID 1
            'date_created' => $this->faker->dateTimeBetween('-60 days', 'now'),
            'local_id' => $local?->id,
            'detalhes' => $this->faker->paragraphs(3, true),
            'status' => $status,
            'progresso' => match($status) {
                'Aberta' => $this->faker->numberBetween(0, 25),
                'Em Andamento' => $this->faker->numberBetween(25, 90),
                'Concluída' => 100,
                'Cancelada' => $this->faker->numberBetween(0, 50),
            },
            'images' => [], // Array vazio para imagens
            'created_at' => $this->faker->dateTimeBetween('-60 days', 'now'),
        ];
    }
}
