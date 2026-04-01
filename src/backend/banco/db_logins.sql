-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 01/04/2026 às 00:00
-- Versão do servidor: 9.1.0
-- Versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `db_logins`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOME` varchar(100) NOT NULL,
  `EMAIL` varchar(100) NOT NULL,
  `SENHA` varchar(255) NOT NULL,
  `DATA_CADASTRO` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ADMIN` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `EMAIL` (`EMAIL`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`ID`, `NOME`, `EMAIL`, `SENHA`, `DATA_CADASTRO`, `ADMIN`) VALUES
(1, 'hugo sabugo', 'hugosabugo@gmail.com', '123456', '2026-03-04 00:49:16', 0),
(2, 'Mariana Imenez', 'marianaimenez@gmail.com', 'euamoofelipe', '2026-03-04 00:52:43', 0),
(3, 'Mariana Urbano', 'marianaurbano@gmail.com', '$2y$10$qIbWTR57VYjU2mH.RxP3POdKOODgLVZtNGoclwPv9xzDHlxMo3CWu', '2026-03-04 00:54:26', 0),
(4, 'Otavio Pagagnotti thimotheo', 'otavopagagnotti@gmail.com', '$2y$10$gu0pDHpZikVswZSn9EiqhekGFIgrum4V4BRhmQpdjAK/4eR1e7a.C', '2026-03-09 14:16:35', 0),
(5, 'Pedro Chargas', 'pedrochargas@gmail.com', '$2y$10$VyqloVq9vzM3Jbo2MBoqOubX61LKm9SzxL64sshJ4uaW/hAiV94Nm', '2026-03-09 14:29:06', 0),
(6, 'Pedro Beatriz', 'pedrobeatriz@gmail.com', '$2y$10$/c2/nO5RdZqmoD5wRIyHju.IiMi1o/HNb0ULMFwCzoJddRY8fwCCe', '2026-03-09 14:31:08', 0),
(7, 'Pedro Elena', 'pedroelena@gmail.com', '$2y$10$3IQBNJsZnUErKtz4vvSGWuW5l3S2YFR66ePzFhneJ2DfsbddBcZg6', '2026-03-09 14:32:59', 0),
(8, 'Pedro Alana', 'pedroalana@gmail.com', '$2y$10$YwqMltUS3CIXUsg3xYowsu.RJ4j6FK8KZEpXKOT9HWpW6H8DMYFQK', '2026-03-09 14:34:46', 0),
(9, 'Pedro Deriz', 'pedroderiz@gmail.com', '$2y$10$blbF02yDsfXJBl/05nZW8eN.U5oInNubqm5.m0G6nKtgoSH3KAu6O', '2026-03-09 14:36:46', 0),
(10, 'Otavio Tedesco', 'OtavioTedesco@gmail.com', '$2y$10$LYLgHkkcGGN3NOe4IL2uZ.3Vf1gvscivQzvaFi9ddOUBEiXeV2Ul6', '2026-03-09 14:38:54', 0),
(11, 'Otavio 00 Face', 'Otavio00face@gmail.com', '$2y$10$bfX5UTvf6QxjT4zZYeG6.OwpowVa2XvjM8gn7bDpd7I06QS5QYazy', '2026-03-09 14:40:20', 0),
(12, 'hugo sabugo', 'hugohenriquesabugo@gmail.com', '$2y$10$bwb//Zd2vZuC97s90cBCJO.o9MnVLvbOK8ZAOqunK3Coo.Tp4SDxW', '2026-03-18 00:35:49', 0),
(13, 'pedro ', 'pedro@gmail.com', '$2y$10$IzS955IFNXaS0HOGexLSRObbH/lJ0VfJSBr8ocEYKXQySOCArq1VK', '2026-03-23 14:09:54', 0),
(14, 'pedro ', 'pedro8@gmail.com', '$2y$10$nOqZMRUlw2ekNvFPdMzVdOut664ki.iFLTPDLgK0.XQ8GcFB4HYSG', '2026-03-23 14:11:58', 0),
(15, 'pedro ', 'pedro7@gmail.com', '$2y$10$luTdklCA5kep5y6GJcT53OSwIC6Ja5kYGSBvBCfKK6X02ZofvpdnO', '2026-03-23 14:32:39', 0),
(16, 'cuzao', 'cuzaoprimao@gmail.com', '$2y$10$kSPwdmkOaRd3dMg.mCsyxev9mgs8N3nnSq9xv7JM2VbdMOYdbXDQW', '2026-03-25 00:05:31', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
