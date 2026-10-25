<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserApiController extends Controller
{
    /**
     * Listar usuários (apenas admins)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filtros
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('setor', 'like', "%{$search}%")
                  ->orWhere('cargo', 'like', "%{$search}%");
            });
        }

        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        if ($request->has('ativo') && $request->ativo !== '') {
            $query->where('ativo', $request->ativo);
        }

        if ($request->has('setor') && $request->setor) {
            $query->where('setor', 'like', "%{$request->setor}%");
        }

        // Incluir contagem de relatórios se solicitado
        if ($request->get('with_relatorios_count', false)) {
            $query->withCount('relatorios');
        }

        // Paginação
        $perPage = $request->get('per_page', 15);
        $users = $query->orderBy('name')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ]
            ]
        ]);
    }

    /**
     * Criar novo usuário (apenas admins)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'setor' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,operador,visualizador',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'setor' => $request->setor,
            'cargo' => $request->cargo,
            'telefone' => $request->telefone,
            'role' => $request->role,
            'ativo' => $request->get('ativo', true),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso',
            'data' => ['user' => $user]
        ], 201);
    }

    /**
     * Exibir usuário específico (apenas admins)
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::withCount('relatorios')->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['user' => $user]
        ]);
    }

    /**
     * Atualizar usuário (apenas admins)
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'setor' => 'required|string|max:255',
            'cargo' => 'required|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'role' => 'required|in:admin,operador,visualizador',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'setor' => $request->setor,
            'cargo' => $request->cargo,
            'telefone' => $request->telefone,
            'role' => $request->role,
            'ativo' => $request->get('ativo', $user->ativo),
        ];

        // Atualizar senha apenas se fornecida
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso',
            'data' => ['user' => $user]
        ]);
    }

    /**
     * Deletar usuário (apenas admins)
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        // Verificar se não é o último admin
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->where('ativo', true)->count();
            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível deletar o último administrador ativo'
                ], 422);
            }
        }

        // Verificar se tem relatórios associados
        if ($user->relatorios()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível deletar usuário com relatórios associados. Desative o usuário em vez de deletá-lo.'
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuário deletado com sucesso'
        ]);
    }
}