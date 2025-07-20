-- Script SQL corrigido para o campo local
-- Resolve o erro: "Coluna BLOB 'local' usada na especificação de chave sem o comprimento da chave"

-- 1. Verificar índices na coluna local
SHOW INDEX FROM motores WHERE Column_name = 'local';

-- 2. Remover índices da coluna local (execute um por vez)
-- Substitua 'nome_do_indice' pelo nome real do índice
-- ALTER TABLE motores DROP INDEX nome_do_indice;

-- 3. Alterar o tipo da coluna para VARCHAR (não TEXT para evitar problemas)
ALTER TABLE motores MODIFY COLUMN local VARCHAR(255) NULL;

-- 4. Verificar se a alteração foi feita
DESCRIBE motores;

-- 5. Testar inserção
INSERT INTO motores (tag, equipamento, local, created_at, updated_at) 
VALUES ('TESTE_LOCAL', 'Equipamento Teste', 'Local Teste', NOW(), NOW());

-- 6. Verificar inserção
SELECT id, tag, local FROM motores WHERE tag = 'TESTE_LOCAL';

-- 7. Limpar teste
DELETE FROM motores WHERE tag = 'TESTE_LOCAL'; 