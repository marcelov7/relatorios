<?php

namespace App\Policies;

use App\Models\Relatorio;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RelatorioPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Qualquer usuário autenticado pode ver a lista de relatórios
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Relatorio $relatorio): bool
    {
        // Qualquer usuário autenticado pode ver relatórios individuais
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Qualquer usuário autenticado pode criar relatórios
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Relatorio $relatorio): bool
    {
        // Se o relatório está 100% concluído, ninguém pode editar (exceto admin)
        if ($relatorio->progresso >= 100) {
            return $user->isAdmin();
        }

        // Administrador sempre pode editar
        if ($user->isAdmin()) {
            return true;
        }

        // Autor pode editar apenas nas primeiras 24 horas
        if ($relatorio->autor_id === $user->id) {
            $created_at = $relatorio->created_at;
            $now = now();
            $hoursElapsed = $created_at->diffInHours($now);
            
            return $hoursElapsed <= 24;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Relatorio $relatorio): bool
    {
        // Administrador sempre pode excluir
        if ($user->isAdmin()) {
            return true;
        }

        // Autor pode excluir apenas nas primeiras 24 horas
        if ($relatorio->autor_id === $user->id) {
            $created_at = $relatorio->created_at;
            $now = now();
            $hoursElapsed = $created_at->diffInHours($now);
            
            return $hoursElapsed <= 24;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Relatorio $relatorio): bool
    {
        // Apenas administradores podem restaurar
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Relatorio $relatorio): bool
    {
        // Apenas administradores podem excluir permanentemente
        return $user->isAdmin();
    }

    /**
     * Verificar se o usuário pode editar baseado nas 24h (para frontend)
     */
    public function canEditWithinTimeLimit(User $user, Relatorio $relatorio): bool
    {
        // Se o relatório está 100% concluído, ninguém pode editar (exceto admin)
        if ($relatorio->progresso >= 100) {
            return $user->isAdmin();
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($relatorio->autor_id !== $user->id) {
            return false;
        }

        // Autor pode editar apenas nas primeiras 24 horas
        $created_at = $relatorio->created_at;
        $now = now();
        $hoursElapsed = $created_at->diffInHours($now);
        
        return $hoursElapsed <= 24;
    }

    /**
     * Verificar se o usuário pode excluir baseado nas 24h (para frontend)
     */
    public function canDeleteWithinTimeLimit(User $user, Relatorio $relatorio): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($relatorio->autor_id === $user->id) {
            $created_at = $relatorio->created_at;
            $now = now();
            $hoursElapsed = $created_at->diffInHours($now);
            
            return $hoursElapsed <= 24;
        }

        return false;
    }

    /**
     * Obter tempo restante para exclusão (em horas)
     */
    public function getTimeRemainingForDeletion(Relatorio $relatorio): int
    {
        $created_at = $relatorio->created_at;
        $now = now();
        $hoursElapsed = $created_at->diffInHours($now);
        
        return max(0, 24 - $hoursElapsed);
    }

    /**
     * Obter tempo restante para edição (em horas)
     */
    public function getTimeRemainingForEdit(Relatorio $relatorio): int
    {
        $created_at = $relatorio->created_at;
        $now = now();
        $hoursElapsed = $created_at->diffInHours($now);
        
        return max(0, 24 - $hoursElapsed);
    }

    /**
     * Verificar se o relatório está concluído (progresso 100%)
     */
    public function isRelatorioConcluido(Relatorio $relatorio): bool
    {
        return $relatorio->progresso >= 100;
    }
}
