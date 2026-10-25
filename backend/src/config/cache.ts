import NodeCache from 'node-cache';
import { CACHE } from './constants';

// Criação da instância do cache
const cache = new NodeCache({
  stdTTL: CACHE.TTL, // Tempo de vida padrão dos itens em segundos
  checkperiod: CACHE.TTL * 0.2, // Período de verificação de itens expirados
  maxKeys: CACHE.MAX_ITEMS // Número máximo de itens no cache
});

// Função para obter um item do cache
export function get<T>(key: string): T | undefined {
  return cache.get<T>(key);
}

// Função para definir um item no cache
export function set<T>(key: string, value: T, ttl?: number): boolean {
  return cache.set(key, value, ttl);
}

// Função para remover um item do cache
export function del(key: string): number {
  return cache.del(key);
}

// Função para limpar todo o cache
export function clear(): void {
  cache.flushAll();
}

// Função para obter as estatísticas do cache
export function getStats(): NodeCache.Stats {
  return cache.getStats();
}

// Função para verificar se uma chave existe no cache
export function has(key: string): boolean {
  return cache.has(key);
}

// Função para obter as chaves do cache
export function keys(): string[] {
  return cache.keys();
}

// Função para obter o número de itens no cache
export function size(): number {
  return cache.keys().length;
}

export { cache };