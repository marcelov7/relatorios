-- =====================================================
-- Script para adicionar coluna 'setor' na tabela equipamento_tests
-- Execute este script no CloudPanel se a coluna não existir
-- =====================================================

-- Verificar se a coluna 'setor' existe na tabela equipamento_tests
SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'equipamento_tests'
    AND COLUMN_NAME = 'setor'
);

-- Adicionar a coluna se ela não existir
SET @sql = IF(@column_exists = 0,
    'ALTER TABLE equipamento_tests ADD COLUMN setor VARCHAR(255) NULL AFTER nome;',
    'SELECT "Coluna setor já existe na tabela equipamento_tests" AS message;'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar se a coluna foi criada
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'equipamento_tests'
AND COLUMN_NAME = 'setor';

-- Inserir dados de exemplo se a tabela estiver vazia
INSERT INTO equipamento_tests (tag, nome, setor, status, created_at, updated_at)
SELECT * FROM (
    SELECT 
        'EQPROD001' as tag,
        'Máquina de Corte' as nome,
        'Produção' as setor,
        'Operacional' as status,
        NOW() as created_at,
        NOW() as updated_at
) AS temp
WHERE NOT EXISTS (
    SELECT 1 FROM equipamento_tests WHERE tag = 'EQPROD001'
);

INSERT INTO equipamento_tests (tag, nome, setor, status, created_at, updated_at)
SELECT * FROM (
    SELECT 
        'EQMAN001' as tag,
        'Compressor de Ar' as nome,
        'Manutenção' as setor,
        'Operacional' as status,
        NOW() as created_at,
        NOW() as updated_at
) AS temp
WHERE NOT EXISTS (
    SELECT 1 FROM equipamento_tests WHERE tag = 'EQMAN001'
);

INSERT INTO equipamento_tests (tag, nome, setor, status, created_at, updated_at)
SELECT * FROM (
    SELECT 
        'EQQUAL001' as tag,
        'Microscópio Digital' as nome,
        'Qualidade' as setor,
        'Operacional' as status,
        NOW() as created_at,
        NOW() as updated_at
) AS temp
WHERE NOT EXISTS (
    SELECT 1 FROM equipamento_tests WHERE tag = 'EQQUAL001'
);

-- Mostrar setores únicos disponíveis
SELECT 
    setor,
    COUNT(*) as total_equipamentos
FROM equipamento_tests 
WHERE setor IS NOT NULL 
AND setor != ''
GROUP BY setor
ORDER BY setor;

-- Verificar estrutura final da tabela
DESCRIBE equipamento_tests; 