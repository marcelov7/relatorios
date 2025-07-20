-- Comandos para remover os índices da coluna local
-- Execute estes comandos em sequência

-- 1. Remover o primeiro índice 'local'
ALTER TABLE motores DROP INDEX local;

-- 2. Remover o segundo índice 'local_2'
ALTER TABLE motores DROP INDEX local_2;

-- 3. Verificar se os índices foram removidos
SHOW INDEX FROM motores WHERE Column_name = 'local';

-- 4. Verificar estrutura da coluna local
DESCRIBE motores;

-- 5. Se ainda houver problemas, alterar o tipo novamente
-- ALTER TABLE motores MODIFY COLUMN local VARCHAR(255) NULL; 