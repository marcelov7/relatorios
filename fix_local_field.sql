-- Script SQL para corrigir o campo local da tabela motores
-- Alterar de bigint (chave) para text (campo simples)

-- 1. Verificar índices na coluna local
SHOW INDEX FROM motores WHERE Column_name = 'local';

-- 2. Remover índices se existirem (substitua 'nome_do_indice' pelo nome real)
-- ALTER TABLE motores DROP INDEX nome_do_indice;

-- 3. Alterar o tipo da coluna para TEXT
ALTER TABLE motores MODIFY COLUMN local TEXT NULL;

-- 4. Verificar se a alteração foi feita
DESCRIBE motores;

-- 5. Testar inserção
INSERT INTO motores (tag, equipamento, local, created_at, updated_at) 
VALUES ('TESTE_LOCAL', 'Equipamento Teste', 'Local Teste', NOW(), NOW());

-- 6. Verificar inserção
SELECT id, tag, local FROM motores WHERE tag = 'TESTE_LOCAL';

-- 7. Limpar teste
DELETE FROM motores WHERE tag = 'TESTE_LOCAL'; 