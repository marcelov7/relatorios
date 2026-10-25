-- ==============================================================================
-- SCRIPT DE ATUALIZAÇÃO MULTI-TENANT PARA MYSQL / CLOUDPANEL / PHPMYADMIN
-- Data: 27/08/2026
-- Descrição: Cria a tabela de tenants, cria a tabela de inspecao_geradores se não
--            existir, adiciona a coluna tenant_id e vincula dados de forma segura.
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. CRIAR TABELA TENANTS (EMPRESAS)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `documento` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. POPULAR TENANTS INICIAIS
-- ------------------------------------------------------------------------------
INSERT INTO `tenants` (`id`, `nome`, `slug`, `documento`, `ativo`, `created_at`, `updated_at`)
VALUES
  (1, 'Empresa Principal', 'empresa-principal', '12.345.678/0001-90', 1, NOW(), NOW()),
  (2, 'Empresa Demo / Secundária', 'empresa-demo', '98.765.432/0001-10', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE `nome` = VALUES(`nome`), `updated_at` = NOW();

-- ------------------------------------------------------------------------------
-- 3. CRIAR TABELA INSPECAO_GERADORES (SE NÃO EXISTIR NO BANCO DE PRODUÇÃO)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inspecao_geradores` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `data_inspecao` datetime NOT NULL,
  `colaborador_id` bigint(20) UNSIGNED NOT NULL,
  `nivel_oleo_motor_parado` enum('MÍNIMO','NORMAL') NOT NULL,
  `nivel_agua_parado` enum('MÍNIMO','NORMAL') NOT NULL,
  `sync_gerador` decimal(5,2) DEFAULT NULL,
  `sync_rede` decimal(5,2) DEFAULT NULL,
  `temperatura_agua` decimal(5,2) DEFAULT NULL,
  `pressao_oleo` decimal(5,2) DEFAULT NULL,
  `frequencia` decimal(5,2) DEFAULT NULL,
  `tensao_a` decimal(6,2) DEFAULT NULL,
  `tensao_b` decimal(6,2) DEFAULT NULL,
  `tensao_c` decimal(6,2) DEFAULT NULL,
  `rpm_1800` int(11) DEFAULT NULL,
  `tensao_bateria_parado` decimal(5,2) DEFAULT NULL,
  `tensao_alternador_marcha` decimal(5,2) DEFAULT NULL,
  `nivel_combustivel` int(11) DEFAULT NULL,
  `iluminacao_sala_deficiente` tinyint(1) NOT NULL DEFAULT 0,
  `limpeza_sala_realizada` tinyint(1) NOT NULL DEFAULT 0,
  `situacao` enum('OK - PRONTO ( AUTO)','ANORMAL - PRECISA DE INSPEÇÃO') NOT NULL DEFAULT 'OK - PRONTO ( AUTO)',
  `observacoes` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `setor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `setor_text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inspecao_geradores_data_inspecao_index` (`data_inspecao`),
  KEY `inspecao_geradores_situacao_index` (`situacao`),
  KEY `inspecao_geradores_colaborador_id_index` (`colaborador_id`),
  KEY `inspecao_geradores_setor_id_index` (`setor_id`),
  CONSTRAINT `fk_inspecao_geradores_tenant_id` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. ADICIONAR COLUNA TENANT_ID E EXPANDIR ROLE NA TABELA USERS
-- ------------------------------------------------------------------------------
SET @dbname = DATABASE();

-- Ajustar a coluna role da tabela users para aceitar super_admin (VARCHAR ou ENUM expandido)
ALTER TABLE `users` MODIFY COLUMN `role` varchar(255) DEFAULT 'user';

-- Tabela: USERS
SET @tablename = 'users';
SET @columnname = 'tenant_id';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  "SELECT 1",
  "ALTER TABLE users ADD COLUMN tenant_id bigint(20) UNSIGNED NULL AFTER id, ADD CONSTRAINT fk_users_tenant_id FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL;"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tabela: RELATORIOS
SET @tablename = 'relatorios';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  "SELECT 1",
  "ALTER TABLE relatorios ADD COLUMN tenant_id bigint(20) UNSIGNED NULL AFTER id, ADD CONSTRAINT fk_relatorios_tenant_id FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL;"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tabela: EQUIPAMENTOS
SET @tablename = 'equipamentos';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  "SELECT 1",
  "ALTER TABLE equipamentos ADD COLUMN tenant_id bigint(20) UNSIGNED NULL AFTER id, ADD CONSTRAINT fk_equipamentos_tenant_id FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL;"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tabela: SETORES
SET @tablename = 'setores';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  "SELECT 1",
  "ALTER TABLE setores ADD COLUMN tenant_id bigint(20) UNSIGNED NULL AFTER id, ADD CONSTRAINT fk_setores_tenant_id FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL;"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tabela: MOTORES
SET @tablename = 'motores';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  "SELECT 1",
  "ALTER TABLE motores ADD COLUMN tenant_id bigint(20) UNSIGNED NULL AFTER id, ADD CONSTRAINT fk_motores_tenant_id FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL;"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tabela: INSPECAO_GERADORES (Adiciona tenant_id caso a tabela já existia sem a coluna)
SET @tablename = 'inspecao_geradores';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = @columnname) > 0,
  "SELECT 1",
  "ALTER TABLE inspecao_geradores ADD COLUMN tenant_id bigint(20) UNSIGNED NULL AFTER id, ADD CONSTRAINT fk_inspecao_geradores_tenant_id FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL;"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tabela: LOCAIS (Se existir no banco)
SET @tablename = 'locais';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE table_name = @tablename AND table_schema = @dbname) > 0 AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = 'tenant_id') = 0,
  "ALTER TABLE locais ADD COLUMN tenant_id bigint(20) UNSIGNED NULL AFTER id, ADD CONSTRAINT fk_locais_tenant_id FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL;",
  "SELECT 1"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tabela: EQUIPAMENTO_TESTS (Se existir no banco)
SET @tablename = 'equipamento_tests';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE table_name = @tablename AND table_schema = @dbname) > 0 AND
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = @tablename AND table_schema = @dbname AND column_name = 'tenant_id') = 0,
  "ALTER TABLE equipamento_tests ADD COLUMN tenant_id bigint(20) UNSIGNED NULL AFTER id, ADD CONSTRAINT fk_equipamento_tests_tenant_id FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE SET NULL;",
  "SELECT 1"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ------------------------------------------------------------------------------
-- 5. VINCULAR DADOS EXISTENTES AO TENANT PADRÃO (ID = 1)
-- ------------------------------------------------------------------------------
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'users' AND table_schema = @dbname) > 0,
  "UPDATE `users` SET `tenant_id` = 1 WHERE `tenant_id` IS NULL AND (`role` IS NULL OR `role` != 'super_admin');",
  "SELECT 1"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'relatorios' AND table_schema = @dbname) > 0,
  "UPDATE `relatorios` SET `tenant_id` = 1 WHERE `tenant_id` IS NULL;",
  "SELECT 1"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'equipamentos' AND table_schema = @dbname) > 0,
  "UPDATE `equipamentos` SET `tenant_id` = 1 WHERE `tenant_id` IS NULL;",
  "SELECT 1"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'setores' AND table_schema = @dbname) > 0,
  "UPDATE `setores` SET `tenant_id` = 1 WHERE `tenant_id` IS NULL;",
  "SELECT 1"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'motores' AND table_schema = @dbname) > 0,
  "UPDATE `motores` SET `tenant_id` = 1 WHERE `tenant_id` IS NULL;",
  "SELECT 1"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'inspecao_geradores' AND table_schema = @dbname) > 0,
  "UPDATE `inspecao_geradores` SET `tenant_id` = 1 WHERE `tenant_id` IS NULL;",
  "SELECT 1"
));
PREPARE stmt FROM @preparedStatement; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ------------------------------------------------------------------------------
-- 6. CRIAR USUÁRIO SUPER ADMIN GLOBAL (SE NÃO EXISTIR)
-- (Email: superadmin@sistema.com | Senha: admin123)
-- ------------------------------------------------------------------------------
INSERT INTO `users` (`name`, `email`, `password`, `setor`, `cargo`, `telefone`, `role`, `ativo`, `created_at`, `updated_at`)
SELECT 'Super Administrador', 'superadmin@sistema.com', '$2y$12$NqL1Xp0/zXoV0O.y4bCsmex4oA/7qf5k6V8P.5N3o4.1o0V3S5.W.', 'Gestão Global', 'Super Admin SaaS', '(11) 99999-0000', 'super_admin', 1, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT * FROM `users` WHERE `email` = 'superadmin@sistema.com');

SET FOREIGN_KEY_CHECKS = 1;

-- FIM DO SCRIPT
