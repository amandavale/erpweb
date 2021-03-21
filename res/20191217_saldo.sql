ALTER TABLE `saldo_pendente` ADD `saldo_processado` ENUM( '0', '1' ) NOT NULL COMMENT 'indica se a data de saldo já foi processada';

ALTER TABLE `saldo_pendente` CHANGE `saldo_processado` `saldo_processado` ENUM( '0', '1' ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT '0' COMMENT 'indica se a data de saldo já foi processada';