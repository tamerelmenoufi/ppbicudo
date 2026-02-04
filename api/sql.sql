-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 04/02/2026 às 20:49
-- Versão do servidor: 5.7.44
-- Versão do PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Banco de dados: `app`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `origens`
--

CREATE TABLE `origens` (
  `codigo` bigint(20) NOT NULL,
  `nome` char(100) NOT NULL,
  `imagem` varchar(50) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `deletado` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `relatorio`
--

CREATE TABLE `relatorio` (
  `codigo` bigint(20) NOT NULL,
  `planilha` bigint(20) NOT NULL,
  `origem` bigint(20) NOT NULL,
  `dataCriacao` datetime NOT NULL,
  `codigoPedido` char(30) NOT NULL,
  `pedidoOrigem` char(50) NOT NULL,
  `tituloItem` varchar(255) NOT NULL,
  `frete` char(30) NOT NULL,
  `ValorPedidoXquantidade` decimal(20,2) NOT NULL,
  `CustoEnvio` decimal(20,2) NOT NULL,
  `CustoEnvioSeller` decimal(20,2) NOT NULL,
  `TarifaGatwayPagamento` decimal(20,2) NOT NULL DEFAULT '0.00',
  `TarifaMarketplace` decimal(20,2) NOT NULL,
  `PrecoCusto` decimal(20,2) NOT NULL,
  `Porcentagem` decimal(20,2) NOT NULL,
  `Conta` char(50) DEFAULT NULL,
  `relatorio` bigint(20) NOT NULL DEFAULT '0',
  `observacoes` text,
  `deletado` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0 = ativo, 1 = deletado',
  `deletado_usuario` bigint(20) NOT NULL DEFAULT '0',
  `deletado_justificativa` text,
  `devolucao` enum('0','1') NOT NULL DEFAULT '0',
  `devolucao_data` date DEFAULT NULL,
  `devolucao_relatorio` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `relatorio_modelos`
--

CREATE TABLE `relatorio_modelos` (
  `codigo` bigint(20) NOT NULL,
  `data` date NOT NULL,
  `data2` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `origem` bigint(20) NOT NULL,
  `nome` char(100) NOT NULL,
  `registros` json NOT NULL,
  `devolucoes` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `origens`
--
ALTER TABLE `origens`
  ADD PRIMARY KEY (`codigo`);

--
-- Índices de tabela `relatorio`
--
ALTER TABLE `relatorio`
  ADD PRIMARY KEY (`codigo`),
  ADD UNIQUE KEY `codigoPedido` (`codigoPedido`);

--
-- Índices de tabela `relatorio_modelos`
--
ALTER TABLE `relatorio_modelos`
  ADD PRIMARY KEY (`codigo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `origens`
--
ALTER TABLE `origens`
  MODIFY `codigo` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `relatorio`
--
ALTER TABLE `relatorio`
  MODIFY `codigo` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `relatorio_modelos`
--
ALTER TABLE `relatorio_modelos`
  MODIFY `codigo` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;
