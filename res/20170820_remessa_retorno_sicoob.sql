ALTER TABLE `conta_filial` CHANGE `identificador` `identificador` VARCHAR( 20 ) NULL DEFAULT NULL COMMENT 'Identificador da empresa no banco';

INSERT INTO `conta_filial` (`idconta_filial`, `idfilial`, `idbanco`, `agencia_filial`, `agencia_dig_filial`, `conta_filial`, `conta_dig_filial`, `principal_filial`, `carteira`, `conta_cnpj`, `conta_cedente`, `identificador`, `prefixo_nosso_numero`) VALUES
(25, 2, 172, '5631', '6', '3878', '4', '0', '01', '02.807.080/0001-17', 'SOS Prestadora de Servicos Ltda', '11198-8', 0);

ALTER TABLE `arquivo_remessa` ADD `nosso_numero` INT( 11 ) NULL COMMENT 'Maior valor de nosso número gerado na remessa';

ALTER TABLE `movimento` ADD `nosso_numero` VARCHAR( 12 ) NULL COMMENT 'Nosso número associado ao boleto';