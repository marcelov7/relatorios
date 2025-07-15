<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UserController extends Controller
{

    /**
     * Exibir lista de usuários
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtro de busca
        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->where(function ($q) use ($busca) {
                $q->where('name', 'like', "%{$busca}%")
                  ->orWhere('email', 'like', "%{$busca}%")
                  ->orWhere('setor', 'like', "%{$busca}%")
                  ->orWhere('cargo', 'like', "%{$busca}%");
            });
        }

        // Filtro por setor
        if ($request->filled('setor')) {
            $query->where('setor', $request->setor);
        }

        // Filtro por role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filtro por status
        if ($request->filled('ativo')) {
            $query->where('ativo', $request->ativo === '1');
        }

        $users = $query->orderBy('name')
                      ->paginate(12)
                      ->appends($request->all());

        // Obter setores únicos para filtro
        $setores = User::whereNotNull('setor')
                      ->distinct()
                      ->pluck('setor')
                      ->sort()
                      ->values();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'setores' => $setores,
            'filters' => $request->only(['busca', 'setor', 'role', 'ativo'])
        ]);
    }

    /**
     * Exibir formulário de criação
     */
    public function create()
    {
        return Inertia::render('Users/Create');
    }

    /**
     * Salvar novo usuário
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'setor' => 'nullable|string|max:255',
            'cargo' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'role' => 'required|in:user,admin',
            'ativo' => 'boolean'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'setor' => $request->setor,
            'cargo' => $request->cargo,
            'telefone' => $request->telefone,
            'role' => $request->role,
            'ativo' => $request->ativo ?? true
        ]);

        return redirect()->route('users.index')
                        ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Exibir detalhes do usuário
     */
    public function show(User $user)
    {
        $user->load(['relatorios' => function ($query) {
            $query->latest()->take(10);
        }]);

        return Inertia::render('Users/Show', [
            'user' => $user
        ]);
    }

    /**
     * Exibir formulário de edição
     */
    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user
        ]);
    }

    /**
     * Atualizar usuário
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'setor' => 'nullable|string|max:255',
            'cargo' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'role' => 'required|in:user,admin',
            'ativo' => 'boolean'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'setor' => $request->setor,
            'cargo' => $request->cargo,
            'telefone' => $request->telefone,
            'role' => $request->role,
            'ativo' => $request->ativo ?? true
        ];

        // Apenas atualizar senha se foi fornecida
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
                        ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Excluir usuário
     */
    public function destroy(User $user)
    {
        // Verificar se não é o próprio usuário logado
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                            ->with('error', 'Você não pode excluir sua própria conta.');
        }

        // Verificar se o usuário tem relatórios
        if ($user->relatorios()->count() > 0) {
            return redirect()->route('users.index')
                            ->with('error', 'Não é possível excluir usuário que possui relatórios vinculados.');
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'Usuário excluído com sucesso!');
    }
} 