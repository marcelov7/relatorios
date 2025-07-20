-- Script para verificar índices da coluna local
-- Execute estes comandos para identificar quais índices existem

-- 1. Verificar TODOS os índices da tabela motores
SHOW INDEX FROM motores;

-- 2. Verificar especificamente índices da coluna local
SHOW INDEX FROM motores WHERE Column_name = 'local';

-- 3. Verificar estrutura da coluna local
DESCRIBE motores;

-- 4. Se não houver índices na coluna local, você pode alterar diretamente:
-- ALTER TABLE motores MODIFY COLUMN local VARCHAR(255) NULL;

-- 5. Se houver índices, remova-os primeiro (substitua pelo nome real):
-- ALTER TABLE motores DROP INDEX nome_real_do_indice; 