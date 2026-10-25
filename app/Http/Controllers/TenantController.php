<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantController extends Controller
{
    /**
     * Lista todos os tenants (Super Admin).
     */
    public function index()
    {
        $tenants = Tenant::withCount(['users', 'relatorios', 'equipamentos'])->paginate(15);

        return Inertia::render('Tenants/Index', [
            'tenants' => $tenants,
        ]);
    }

    /**
     * Cria um novo tenant / empresa (Super Admin).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tenants,slug',
            'documento' => 'nullable|string|max:255',
            'ativo' => 'boolean',
            'admin_name' => 'nullable|string|max:255',
            'admin_email' => 'nullable|email|unique:users,email',
            'admin_password' => 'nullable|string|min:8',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['nome']);
        }

        $tenant = Tenant::create([
            'nome' => $validated['nome'],
            'slug' => $validated['slug'],
            'documento' => $validated['documento'] ?? null,
            'ativo' => $validated['ativo'] ?? true,
        ]);

        // Se foram fornecidos dados para criar o admin do tenant
        if (!empty($validated['admin_email']) && !empty($validated['admin_password'])) {
            User::withoutTenancy()->create([
                'tenant_id' => $tenant->id,
                'name' => $validated['admin_name'] ?? 'Admin ' . $tenant->nome,
                'email' => $validated['admin_email'],
                'password' => bcrypt($validated['admin_password']),
                'role' => 'admin',
                'ativo' => true,
            ]);
        }

        return redirect()->back()->with('message', 'Empresa/Tenant cadastrado com sucesso!');
    }

    /**
     * Atualiza dados de um tenant (Super Admin).
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,' . $tenant->id,
            'documento' => 'nullable|string|max:255',
            'ativo' => 'boolean',
        ]);

        $tenant->update($validated);

        return redirect()->back()->with('message', 'Empresa/Tenant atualizada com sucesso!');
    }

    /**
     * Alterna o contexto do tenant ativo na sessão.
     */
    public function switchTenant(Request $request)
    {
        $request->validate([
            'tenant_id' => 'required',
        ]);

        $tenantId = $request->tenant_id;

        if ($tenantId === 'all') {
            session(['tenant_id' => 'all']);
        } else {
            $tenant = Tenant::findOrFail($tenantId);
            session(['tenant_id' => $tenant->id]);
        }

        return redirect()->back()->with('message', 'Contexto de empresa alterado com sucesso!');
    }
}
