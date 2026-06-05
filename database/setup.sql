-- =============================================================
-- JahuAqui — Script de instalação do banco de dados
-- Execute este arquivo para criar o banco do zero.
-- =============================================================

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS db_logins
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_logins;

-- -------------------------------------------------------------
-- Tabela: USUARIOS
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS USUARIOS (
    id            INT(11)      NOT NULL AUTO_INCREMENT,
    NOME          VARCHAR(255) NOT NULL,
    EMAIL         VARCHAR(255) NOT NULL,
    SENHA         VARCHAR(255) NOT NULL,
    data_cadastro TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    IS_ADMIN      TINYINT(1)            DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`EMAIL`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------
-- Tabela: SERVICOS
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS SERVICOS (
    ID            INT(11)      NOT NULL AUTO_INCREMENT,
    USUARIO_ID    INT(11)      NOT NULL,
    NOME          VARCHAR(100) NOT NULL,
    CATEGORIA     VARCHAR(50)  NOT NULL,
    DESCRICAO     VARCHAR(500) NOT NULL,
    TELEFONE      VARCHAR(20)  NOT NULL,
    FOTO          VARCHAR(500)          DEFAULT NULL,
    STATUS        ENUM('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
    CRIADO_EM     TIMESTAMP             DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ID`),
    CONSTRAINT fk_servico_usuario
        FOREIGN KEY (USUARIO_ID) REFERENCES USUARIOS(id)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
