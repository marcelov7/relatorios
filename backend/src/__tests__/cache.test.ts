import { cache } from '../config/cache';
import { CACHE } from '../config/constants';

describe('Cache System', () => {
  beforeEach(() => {
    cache.clear();
  });

  describe('Basic Operations', () => {
    it('should set and get values', () => {
      cache.set('test-key', 'test-value');
      expect(cache.get('test-key')).toBe('test-value');
    });

    it('should handle different data types', () => {
      const testData = {
        string: 'test',
        number: 123,
        boolean: true,
        array: [1, 2, 3],
        object: { key: 'value' }
      };

      Object.entries(testData).forEach(([key, value]) => {
        cache.set(key, value);
        expect(cache.get(key)).toEqual(value);
      });
    });

    it('should return undefined for non-existent keys', () => {
      expect(cache.get('non-existent')).toBeUndefined();
    });

    it('should delete values', () => {
      cache.set('delete-test', 'value');
      expect(cache.get('delete-test')).toBe('value');
      
      cache.del('delete-test');
      expect(cache.get('delete-test')).toBeUndefined();
    });
  });

  describe('TTL Behavior', () => {
    it('should respect default TTL', async () => {
      cache.set('ttl-test', 'value');
      expect(cache.get('ttl-test')).toBe('value');

      // Esperar TTL expirar
      await new Promise(resolve => setTimeout(resolve, CACHE.TTL + 100));
      expect(cache.get('ttl-test')).toBeUndefined();
    });

    it('should handle custom TTL', async () => {
      const customTTL = 1000; // 1 segundo
      cache.set('custom-ttl', 'value', customTTL);
      
      // Verificar antes da expiração
      expect(cache.get('custom-ttl')).toBe('value');
      
      // Esperar TTL expirar
      await new Promise(resolve => setTimeout(resolve, customTTL + 100));
      expect(cache.get('custom-ttl')).toBeUndefined();
    });

    it('should update TTL on value update', async () => {
      cache.set('update-ttl', 'value1');
      await new Promise(resolve => setTimeout(resolve, CACHE.TTL / 2));
      
      cache.set('update-ttl', 'value2');
      await new Promise(resolve => setTimeout(resolve, CACHE.TTL / 2));
      
      expect(cache.get('update-ttl')).toBe('value2');
    });
  });

  describe('Cache Management', () => {
    it('should clear all values', () => {
      cache.set('key1', 'value1');
      cache.set('key2', 'value2');
      
      cache.clear();
      
      expect(cache.get('key1')).toBeUndefined();
      expect(cache.get('key2')).toBeUndefined();
    });

    it('should check key existence', () => {
      cache.set('exists-test', 'value');
      
      expect(cache.has('exists-test')).toBe(true);
      expect(cache.has('non-existent')).toBe(false);
    });

    it('should return all keys', () => {
      const testKeys = ['key1', 'key2', 'key3'];
      testKeys.forEach(key => cache.set(key, 'value'));
      
      const keys = cache.keys();
      expect(keys).toHaveLength(testKeys.length);
      testKeys.forEach(key => expect(keys).toContain(key));
    });

    it('should maintain size limits', () => {
      const maxItems = CACHE.MAX_ITEMS;
      
      // Adicionar mais itens que o limite
      for (let i = 0; i < maxItems + 10; i++) {
        cache.set(`key${i}`, `value${i}`);
      }
      
      expect(cache.size()).toBeLessThanOrEqual(maxItems);
    });
  });

  describe('Cache Statistics', () => {
    it('should track hits and misses', () => {
      cache.set('stats-test', 'value');
      
      // Hits
      cache.get('stats-test');
      cache.get('stats-test');
      
      // Misses
      cache.get('non-existent1');
      cache.get('non-existent2');
      
      const stats = cache.getStats();
      expect(stats.hits).toBe(2);
      expect(stats.misses).toBe(2);
    });

    it('should track key deletions', () => {
      cache.set('delete-stats', 'value');
      cache.del('delete-stats');
      cache.del('non-existent');
      
      const stats = cache.getStats();
      expect(stats.deletions).toBe(1);
    });

    it('should reset statistics', () => {
      cache.set('reset-test', 'value');
      cache.get('reset-test');
      cache.get('non-existent');
      
      cache.resetStats();
      
      const stats = cache.getStats();
      expect(stats.hits).toBe(0);
      expect(stats.misses).toBe(0);
      expect(stats.deletions).toBe(0);
    });
  });

  describe('Error Handling', () => {
    it('should handle invalid keys', () => {
      expect(() => cache.set('', 'value')).toThrow();
      expect(() => cache.set(null as any, 'value')).toThrow();
      expect(() => cache.set(undefined as any, 'value')).toThrow();
    });

    it('should handle invalid TTL values', () => {
      expect(() => cache.set('ttl-test', 'value', -1)).toThrow();
      expect(() => cache.set('ttl-test', 'value', 0)).toThrow();
      expect(() => cache.set('ttl-test', 'value', 'invalid' as any)).toThrow();
    });

    it('should handle concurrent operations', async () => {
      const operations = Array.from({ length: 100 }, (_, i) => {
        return new Promise<void>(resolve => {
          setTimeout(() => {
            cache.set(`key${i}`, `value${i}`);
            cache.get(`key${i}`);
            resolve();
          }, Math.random() * 100);
        });
      });

      await Promise.all(operations);
      expect(cache.size()).toBeLessThanOrEqual(CACHE.MAX_ITEMS);
    });
  });

  describe('Cache Performance', () => {
    it('should handle large objects', () => {
      const largeObject = {
        data: Array.from({ length: 1000 }, (_, i) => ({
          id: i,
          value: `test${i}`,
          nested: { more: 'data' }
        }))
      };

      cache.set('large-object', largeObject);
      expect(cache.get('large-object')).toEqual(largeObject);
    });

    it('should handle rapid operations', () => {
      const iterations = 1000;
      
      console.time('rapid-operations');
      for (let i = 0; i < iterations; i++) {
        cache.set(`key${i}`, `value${i}`);
        cache.get(`key${i}`);
      }
      console.timeEnd('rapid-operations');
      
      expect(cache.size()).toBeLessThanOrEqual(CACHE.MAX_ITEMS);
    });

    it('should maintain performance with many keys', () => {
      const keyCount = CACHE.MAX_ITEMS;
      
      console.time('many-keys');
      for (let i = 0; i < keyCount; i++) {
        cache.set(`key${i}`, `value${i}`);
      }
      
      for (let i = 0; i < keyCount; i++) {
        cache.get(`key${i}`);
      }
      console.timeEnd('many-keys');
      
      expect(cache.size()).toBe(keyCount);
    });
  });
});