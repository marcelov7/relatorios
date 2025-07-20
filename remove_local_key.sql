-- Script para remover chaves/índices da coluna local
-- Execute estes comandos ANTES de alterar o tipo da coluna

-- 1. Verificar TODOS os índices da tabela motores
SHOW INDEX FROM motores;

-- 2. Verificar especificamente índices da coluna local
SHOW INDEX FROM motores WHERE Column_name = 'local';

-- 3. Remover índices da coluna local (execute um por vez)
-- Substitua 'nome_do_indice' pelo nome real do índice
-- ALTER TABLE motores DROP INDEX nome_do_indice;

-- Exemplos de comandos (descomente e ajuste conforme necessário):
-- ALTER TABLE motores DROP INDEX idx_local;
-- ALTER TABLE motores DROP INDEX local_index;
-- ALTER TABLE motores DROP INDEX motores_local_index;

-- 4. Verificar se os índices foram removidos
SHOW INDEX FROM motores WHERE Column_name = 'local';

-- 5. Depois que todos os índices forem removidos, altere o tipo:
-- ALTER TABLE motores MODIFY COLUMN local VARCHAR(255) NULL;

-- 6. Verificar se a alteração foi feita
DESCRIBE motores; 