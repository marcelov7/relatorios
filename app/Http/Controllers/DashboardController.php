<?php

namespace App\Http\Controllers;

use App\Models\Relatorio;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalRelatorios' => Relatorio::count(),
            'esteMes' => Relatorio::whereMonth('created_at', now()->month)
                                 ->whereYear('created_at', now()->year)
                                 ->count(),
            'emAndamento' => Relatorio::where('status', 'Em Andamento')->count(),
            'concluidos' => Relatorio::where('status', 'Concluída')->count(),
            'abertos' => Relatorio::where('status', 'Aberta')->count(),
        ];

        // Relatórios abertos que precisam de atenção
        $relatoriosAbertos = Relatorio::with(['autor', 'equipamentosTeste'])
            ->where('status', 'Aberta')
            ->orderBy('created_at', 'asc') // Mais antigos primeiro
            ->take(5)
            ->get()
            ->map(function ($relatorio) {
                // Calcular tempo desde a criação (usando data sem hora para evitar problemas de timezone)
                $dataAtual = now()->startOfDay();
                $dataCriacao = $relatorio->created_at->startOfDay();
                $diasAberto = $dataAtual->diffInDays($dataCriacao);
                $prioridade = $diasAberto > 7 ? 'alta' : ($diasAberto > 3 ? 'media' : 'baixa');
                
                // Formatar tempo de forma mais amigável
                if ($diasAberto == 0) {
                    $tempoAberto = 'Hoje';
                } elseif ($diasAberto == 1) {
                    $tempoAberto = 'Ontem';
                } elseif ($diasAberto < 7) {
                    $tempoAberto = "Há {$diasAberto} dias";
                } else {
                    $semanas = floor($diasAberto / 7);
                    $diasRestantes = $diasAberto % 7;
                    if ($diasRestantes == 0) {
                        $tempoAberto = $semanas == 1 ? "Há 1 semana" : "Há {$semanas} semanas";
                    } else {
                        $tempoAberto = "Há {$semanas} semana" . ($semanas > 1 ? 's' : '') . " e {$diasRestantes} dia" . ($diasRestantes > 1 ? 's' : '');
                    }
                }
                
                return [
                    'id' => $relatorio->id,
                    'titulo' => $relatorio->titulo,
                    'diasAberto' => $diasAberto,
                    'prioridade' => $prioridade,
                    'autor' => $relatorio->autor->name ?? 'Usuário não encontrado',
                    'setor' => $relatorio->equipamentosTeste->first()->setor ?? 'Sem setor',
                    'dataCriacao' => $relatorio->created_at->format('d/m/Y'),
                    'tempoAberto' => $tempoAberto,
                ];
            });

        // Relatórios em andamento que podem precisar de atenção
        $relatoriosEmAndamento = Relatorio::with(['autor', 'equipamentosTeste'])
            ->where('status', 'Em Andamento')
            ->where('progresso', '<', 100)
            ->orderBy('updated_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($relatorio) {
                // Calcular dias sem atualização (usando data sem hora para evitar problemas de timezone)
                $dataAtual = now()->startOfDay();
                $dataUltimaAtualizacao = $relatorio->updated_at->startOfDay();
                $diasSemAtualizacao = $dataAtual->diffInDays($dataUltimaAtualizacao);
                $precisaAtencao = $diasSemAtualizacao > 3;
                
                // Formatar tempo sem atualização de forma mais amigável
                if ($diasSemAtualizacao == 0) {
                    $tempoSemAtualizacao = 'Atualizado hoje';
                } elseif ($diasSemAtualizacao == 1) {
                    $tempoSemAtualizacao = 'Atualizado ontem';
                } elseif ($diasSemAtualizacao < 7) {
                    $tempoSemAtualizacao = "{$diasSemAtualizacao} dias sem atualização";
                } else {
                    $semanas = floor($diasSemAtualizacao / 7);
                    $diasRestantes = $diasSemAtualizacao % 7;
                    if ($diasRestantes == 0) {
                        $tempoSemAtualizacao = $semanas == 1 ? "1 semana sem atualização" : "{$semanas} semanas sem atualização";
                    } else {
                        $tempoSemAtualizacao = "{$semanas} semana" . ($semanas > 1 ? 's' : '') . " e {$diasRestantes} dia" . ($diasRestantes > 1 ? 's' : '') . " sem atualização";
                    }
                }
                
                return [
                    'id' => $relatorio->id,
                    'titulo' => $relatorio->titulo,
                    'progresso' => $relatorio->progresso,
                    'diasSemAtualizacao' => $diasSemAtualizacao,
                    'tempoSemAtualizacao' => $tempoSemAtualizacao,
                    'precisaAtencao' => $precisaAtencao,
                    'autor' => $relatorio->autor->name ?? 'Usuário não encontrado',
                    'setor' => $relatorio->equipamentosTeste->first()->setor ?? 'Sem setor',
                    'ultimaAtualizacao' => $relatorio->updated_at->format('d/m/Y'),
                ];
            });

        $relatoriosRecentes = Relatorio::with(['autor', 'local', 'setor', 'equipamentos', 'equipamentosTeste'])
                                     ->latest()
                                     ->take(5)
                                     ->get()
                                     ->map(function ($relatorio) {
                                         // Se não houver setor, monta string dos equipamentos de teste
                                         $equipamentosTesteArr = $relatorio->equipamentosTeste->map(function($equip) {
                                             return $equip->tag . ' - ' . $equip->nome . ' (' . ($equip->setor ?: 'Sem setor') . ')';
                                         })->implode('; ');
                                         return [
                                             'id' => $relatorio->id,
                                             'titulo' => $relatorio->titulo,
                                             'status' => $relatorio->status,
                                             'progresso' => $relatorio->progresso,
                                             'autor' => $relatorio->autor->name ?? 'Usuário não encontrado',
                                             'setor' => $relatorio->setor->nome ?? ($equipamentosTesteArr ?: 'Sem setor'),
                                             'tag' => optional($relatorio->equipamentos->first())->equipment_tag ?? ($relatorio->equipamentosTeste->first()->tag ?? 'Sem tag'),
                                             'data' => $relatorio->created_at->format('d/m/Y'),
                                             'equipamentosTeste' => $relatorio->equipamentosTeste->map(function($equip) {
                                                 return [
                                                     'tag' => $equip->tag,
                                                     'nome' => $equip->nome,
                                                     'setor' => $equip->setor,
                                                     'status' => $equip->status,
                                                 ];
                                             }),
                                         ];
                                     });

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'relatoriosRecentes' => $relatoriosRecentes,
            'relatoriosAbertos' => $relatoriosAbertos,
            'relatoriosEmAndamento' => $relatoriosEmAndamento,
        ]);
    }
}
