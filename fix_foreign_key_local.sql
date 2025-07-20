-- Script para identificar e remover foreign key constraints da coluna local

-- 1. Verificar foreign keys da tabela motores
SELECT 
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'relatodb' 
AND TABLE_NAME = 'motores' 
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- 2. Verificar especificamente foreign keys da coluna local
SELECT 
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'relatodb' 
AND TABLE_NAME = 'motores' 
AND COLUMN_NAME = 'local'
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- 3. Verificar índices da coluna local
SHOW INDEX FROM motores WHERE Column_name = 'local';

-- 4. Comandos para remover foreign keys (execute um por vez)
-- Substitua 'nome_da_constraint' pelo nome real da foreign key
-- ALTER TABLE motores DROP FOREIGN KEY nome_da_constraint;

-- 5. Depois remover índices
-- ALTER TABLE motores DROP INDEX local_2;

-- 6. Finalmente alterar o tipo da coluna
-- ALTER TABLE motores MODIFY COLUMN local VARCHAR(255) NULL; 