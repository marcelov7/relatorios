<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Local;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_admin_esta_autenticado()
    {
        $user = \App\Models\User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);
        $response = $this->get('/locais');
        $response->assertStatus(200);
    }

    public function test_usuario_pode_criar_local()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $response = $this->post('/locais', [
            'nome' => 'Local Teste',
            'setor' => 'TI',
            'responsavel' => 'Fulano',
            'telefone' => '(11) 99999-9999',
            'email' => 'teste@empresa.com',
            'descricao' => 'Descrição teste',
            'ativo' => true,
        ]);

        $response->assertRedirect('/locais');
        $this->assertDatabaseHas('locais', ['nome' => 'Local Teste']);
    }

    public function test_usuario_pode_editar_local()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $local = Local::factory()->create(['nome' => 'Local Antigo']);
        $this->actingAs($user);

        $response = $this->put("/locais/{$local->id}", [
            'nome' => 'Local Editado',
            'setor' => $local->setor,
            'responsavel' => $local->responsavel,
            'telefone' => $local->telefone,
            'email' => $local->email,
            'descricao' => $local->descricao,
            'ativo' => $local->ativo,
        ]);

        $response->assertRedirect('/locais');
        $this->assertDatabaseHas('locais', ['nome' => 'Local Editado']);
    }

    public function test_usuario_pode_ativar_desativar_local()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $local = Local::factory()->create(['ativo' => true]);
        $this->actingAs($user);

        $this->put("/locais/{$local->id}", [
            'nome' => $local->nome,
            'setor' => $local->setor,
            'responsavel' => $local->responsavel,
            'telefone' => $local->telefone,
            'email' => $local->email,
            'descricao' => $local->descricao,
            'ativo' => false,
        ]);
        $this->assertDatabaseHas('locais', ['id' => $local->id, 'ativo' => false]);
    }

    public function test_usuario_pode_excluir_local()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $local = Local::factory()->create();
        $this->actingAs($user);

        $response = $this->delete("/locais/{$local->id}");
        $response->assertRedirect('/locais');
        $this->assertDatabaseMissing('locais', ['id' => $local->id]);
    }

    public function test_edicao_simples_local()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $local = Local::factory()->create(['nome' => 'Local Original']);
        $this->actingAs($user);

        $response = $this->put("/locais/{$local->id}", [
            'nome' => 'Local Modificado',
            'ativo' => true,
        ]);

        $response->assertRedirect('/locais');
        $this->assertDatabaseHas('locais', [
            'id' => $local->id,
            'nome' => 'Local Modificado'
        ]);
    }
} 