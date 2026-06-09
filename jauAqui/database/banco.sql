-- =============================================================
-- JahuAqui — Dump do banco de dados (db_logins)
-- Gerado para compatibilidade com MariaDB/MySQL + XAMPP/Laragon
-- =============================================================

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Garante que o banco existe e o seleciona
CREATE DATABASE IF NOT EXISTS db_logins
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE db_logins;

-- -------------------------------------------------------------
-- Tabela: USUARIOS
-- -------------------------------------------------------------
DROP TABLE IF EXISTS `SERVICOS`;
DROP TABLE IF EXISTS `USUARIOS`;

CREATE TABLE `USUARIOS` (
  `id`            int(11)      NOT NULL AUTO_INCREMENT,
  `NOME`          varchar(255) NOT NULL,
  `EMAIL`         varchar(255) NOT NULL,
  `SENHA`         varchar(255) NOT NULL,
  `data_cadastro` timestamp    NOT NULL DEFAULT current_timestamp(),
  `IS_ADMIN`      tinyint(1)            DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`EMAIL`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuário admin de exemplo (senha: 123456)
LOCK TABLES `USUARIOS` WRITE;
/*!40000 ALTER TABLE `USUARIOS` DISABLE KEYS */;
INSERT INTO `USUARIOS` VALUES
  (1,'lucas','lucas@gmail.com','$2y$10$EmCnKVQgaIRXImKzCZh/HelYq7e8QkskzG/7jKR55hZXI9FcgJlu.','2026-05-16 21:51:29',1);
/*!40000 ALTER TABLE `USUARIOS` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------------------------------------
-- Tabela: SERVICOS
-- -------------------------------------------------------------
CREATE TABLE `SERVICOS` (
  `ID`          int(11)      NOT NULL AUTO_INCREMENT,
  `USUARIO_ID`  int(11)      NOT NULL,
  `NOME`        varchar(100) NOT NULL,
  `CATEGORIA`   varchar(50)  NOT NULL,
  `DESCRICAO`   varchar(500) NOT NULL,
  `TELEFONE`    varchar(20)  NOT NULL,
  `FOTO`        varchar(500)          DEFAULT NULL,
  `STATUS`      enum('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
  `CRIADO_EM`   timestamp             DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  CONSTRAINT `fk_servico_usuario`
    FOREIGN KEY (`USUARIO_ID`) REFERENCES `USUARIOS` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `SERVICOS` WRITE;
/*!40000 ALTER TABLE `SERVICOS` DISABLE KEYS */;
INSERT INTO `SERVICOS` VALUES
  (2,1,'Eletricista Residencial','Manutenção','Eletricista profissional em Jaú','(14) 99999-9999',NULL,'aprovado','2026-05-19 19:37:41');
/*!40000 ALTER TABLE `SERVICOS` ENABLE KEYS */;
UNLOCK TABLES;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
