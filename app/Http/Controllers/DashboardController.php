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
        ]);
    }
}
