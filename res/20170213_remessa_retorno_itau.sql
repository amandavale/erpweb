ALTER TABLE `arquivo_remessa` ADD `sequencia` INT( 6 ) NOT NULL COMMENT 'Número sequencial do arquivo';

UPDATE arquivo_remessa SET sequencia = idarquivo_remessa;