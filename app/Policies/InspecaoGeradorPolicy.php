<?php

namespace App\Policies;

use App\Models\InspecaoGerador;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class InspecaoGeradorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Usuários autenticados podem ver a lista
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, InspecaoGerador $inspecaoGerador): bool
    {
        return true; // Usuários autenticados podem ver inspeções
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Usuários autenticados podem criar inspeções
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, InspecaoGerador $inspecaoGerador): bool
    {
        // O autor da inspeção ou administradores podem editar
        return $inspecaoGerador->user_id === $user->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, InspecaoGerador $inspecaoGerador): bool
    {
        // O autor da inspeção ou administradores podem excluir
        return $inspecaoGerador->user_id === $user->id || $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, InspecaoGerador $inspecaoGerador): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, InspecaoGerador $inspecaoGerador): bool
    {
        return false;
    }
}
