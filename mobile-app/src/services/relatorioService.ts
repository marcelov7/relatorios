import { api } from './authService';
import { API_BASE_URL } from '../config/api';

// Interfaces 100% alinhadas ao banco de dados e modelos do Laravel

export interface SetorItem {
  id: number;
  nome: string;
  descricao?: string;
}

export interface EquipamentoItem {
  id: number;
  equipment_tag?: string;
  nome: string;
  tipo?: string;
  marca?: string;
  modelo?: string;
  status?: string;
  setor_id?: number;
  setor?: SetorItem;
}

export interface RelatorioImagemItem {
  id: number;
  nome_original?: string;
  url: string;
  thumb_url?: string;
  medium_url?: string;
}

export interface Relatorio {
  id: number;
  titulo: string;
  activity?: string;
  sector?: string;
  status: string;              // 'Aberta' | 'Em Andamento' | 'Concluída' | 'Cancelada'
  progresso?: number;          // 0 a 100
  detalhes?: string;
  date_created?: string;
  time_created?: string;
  created_at: string;
  updated_at?: string;
  nome_responsavel?: string;
  cargo_responsavel?: string;
  user_id?: number;
  autor_id?: number;
  setor_id?: number;
  user?: { id: number; name: string; email: string; setor?: string; cargo?: string };
  autor?: { id: number; name: string; email: string };
  setor?: SetorItem;
  equipamentos?: EquipamentoItem[];
  equipamentos_teste?: Array<{ id: number; tag?: string; nome: string; setor?: string }>;
  imagens?: Array<{ id: number; caminho_original?: string; caminho_thumb?: string; caminho_medium?: string; nome_original?: string }>;
  imagens_formatadas?: RelatorioImagemItem[];
}

export interface DashboardStats {
  total_relatorios: number;
  relatorios_abertos: number;
  relatorios_em_andamento: number;
  relatorios_resolvidos: number;
  meus_relatorios: number;
  relatorios_criticos: number;
}

// ---------------------------------------------------------------------------

/**
 * Busca estatísticas do dashboard em tempo real via GET /api/dashboard/stats.
 */
export async function fetchDashboardStats(): Promise<DashboardStats> {
  const { data } = await api.get<{ success: boolean; data: DashboardStats }>('/dashboard/stats');
  return data.data;
}

/**
 * Busca relatórios mais recentes em tempo real via GET /api/dashboard/recent.
 */
export async function fetchRecentRelatorios(limit = 6): Promise<Relatorio[]> {
  const { data } = await api.get<{ success: boolean; data: Relatorio[] }>('/dashboard/recent', {
    params: { limit },
  });
  return data.data;
}

/**
 * Lista relatórios com paginação e busca via GET /api/relatorios.
 */
export async function fetchRelatorios(params?: {
  page?: number;
  status?: string;
  search?: string;
  per_page?: number;
}): Promise<{ data: Relatorio[]; total: number; last_page: number }> {
  const { data } = await api.get<{
    success: boolean;
    data: { relatorios: Relatorio[]; pagination: { total: number; last_page: number } };
  }>('/relatorios', {
    params: { per_page: 15, ...params },
  });
  return {
    data: data.data.relatorios,
    total: data.data.pagination.total,
    last_page: data.data.pagination.last_page,
  };
}

/**
 * Busca um relatório com detalhes completos via GET /api/relatorios/:id.
 */
export async function fetchRelatorio(id: number): Promise<Relatorio> {
  const { data } = await api.get<{ success: boolean; data: Relatorio }>(`/relatorios/${id}`);
  return data.data;
}

/**
 * Cria um novo relatório com suporte a campos e upload multipart de fotos via POST /api/relatorios.
 */
export async function createRelatorio(formData: FormData): Promise<Relatorio> {
  const { data } = await api.post<{ success: boolean; message: string; data: Relatorio }>(
    '/relatorios',
    formData,
    {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    }
  );
  return data.data;
}

/**
 * Atualiza um relatório existente (dados, status, progresso e novas fotos) via POST/PUT.
 */
export async function updateRelatorio(id: number, formData: FormData): Promise<Relatorio> {
  const { data } = await api.post<{ success: boolean; message: string; data: Relatorio }>(
    `/relatorios/${id}`,
    formData,
    {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
      params: {
        _method: 'PUT',
      },
    }
  );
  return data.data;
}

/**
 * Exclui um relatório via DELETE /api/relatorios/:id.
 */
export async function deleteRelatorio(id: number): Promise<void> {
  await api.delete(`/relatorios/${id}`);
}

/**
 * Busca todos os setores cadastrados via GET /api/setores.
 */
export async function fetchSetores(): Promise<SetorItem[]> {
  const { data } = await api.get<{ success: boolean; data: { setores: SetorItem[] } }>('/setores');
  return data.data?.setores || [];
}

/**
 * Busca todos os equipamentos cadastrados via GET /api/equipamentos.
 */
export async function fetchEquipamentos(setorId?: number): Promise<EquipamentoItem[]> {
  const { data } = await api.get<{ success: boolean; data: { equipamentos: EquipamentoItem[] } }>(
    '/equipamentos',
    { params: { setor_id: setorId } }
  );
  return data.data?.equipamentos || [];
}

/**
 * Retorna a URL direta para download/visualização do PDF do relatório.
 */
export function getRelatorioPdfUrl(id: number): string {
  return `${API_BASE_URL}/relatorios/${id}/pdf/spatie`;
}
