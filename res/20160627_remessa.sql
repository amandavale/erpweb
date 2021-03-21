INSERT INTO submodulo (idmodulo, nome_submodulo, descricao_submodulo, submodulo_final, ordem_submodulo, largura_menu_programa) VALUES (21, 'Remessa', 'Remessa', '1', 7, 160);

select @codigoSubmodulo:= (LAST_INSERT_ID());

INSERT INTO programa (idsubmodulo, nome_programa, descricao_programa, nome_arquivo, parametros_arquivo, define_adicionar, define_listar, define_excluir, define_editar, ordem_programa) VALUES (@codigoSubmodulo, 'Remessa', 'Remessa', 'remessa', '', '1', '1', '0', '0', 7);

UPDATE submodulo SET ordem_submodulo = 8 WHERE nome_submodulo = 'Retorno';

UPDATE programa SET ordem_programa = 8 WHERE nome_programa = 'Retorno';

CREATE TABLE arquivo_remessa (
         idarquivo_remessa INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY COMMENT 'Chave primária, ID do arquivo de remessa',
         nome_arquivo VARCHAR(30) COMMENT 'Nome do arquivo de remessa',
         conteudo TEXT COMMENT 'Conteúdo do arquivo de remessa',
         sigla_modo_recebimento VARCHAR(2) COMMENT 'Sigla do banco relacionado ao arquivo. É a mesma sigla da tabela modo_recebimento',
         timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora da criação do arquivo'
       );

ALTER TABLE movimento ADD COLUMN idarquivo_remessa INT(10) UNSIGNED COMMENT 'ID do arquivo de remessa onde o movimento foi incluído';

INSERT INTO parametros (nome_parametro, valor_parametro, descricao_parametro, observacoes) VALUES
('juros_boleto_avulso', '0.33', 'Valor em porcentagem dos juros do boleto', ''),
('multa_boleto_avulso', '2', 'Valor em porcentagem da multa do boleto', '');

UPDATE `soserpweb`.`parametros` SET `descricao_parametro` = 'ID do status inicial da OS' WHERE `parametros`.`idparametros` =7;
UPDATE `soserpweb`.`parametros` SET `descricao_parametro` = 'ID do status final da OS' WHERE `parametros`.`idparametros` =9;
UPDATE `soserpweb`.`parametros` SET `descricao_parametro` = 'Multa de boleto para condomínios' WHERE `parametros`.`idparametros` =11;

ALTER TABLE `conta_filial` ADD `identificador` INT( 10 ) NULL COMMENT 'Identificador da empresa no banco',
ADD `prefixo_nosso_numero` INT( 4 ) NULL COMMENT 'Prefixo do nosso número';

UPDATE conta_filial SET identificador = 687527, prefixo_nosso_numero = 14 WHERE idconta_filial =6;

UPDATE `soserpweb`.`parametros` SET `descricao_parametro` = 'Juros de boleto para condomínios' WHERE `parametros`.`idparametros` =10;