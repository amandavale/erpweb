ALTER TABLE movimento DROP saldo_pendente;

ALTER TABLE `saldo_pendente` ADD `atualizacao` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP 

DROP TRIGGER IF EXISTS trg_add_movimento;
DELIMITER //
CREATE TRIGGER trg_add_movimento BEFORE INSERT ON movimento
FOR EACH ROW
BEGIN
	IF NEW.baixado = '1' THEN
		IF NEW.idcliente_origem THEN
			INSERT INTO saldo_pendente (idcliente, data) VALUES (NEW.idcliente_origem, NEW.data_baixa);
		END IF;
		IF NEW.idcliente_destino THEN
			INSERT INTO saldo_pendente (idcliente, data) VALUES (NEW.idcliente_destino, NEW.data_baixa);
		END IF;
	END IF;
END;
//
DELIMITER ;


DROP TRIGGER IF EXISTS trg_upd_movimento;
DELIMITER //
CREATE TRIGGER trg_upd_movimento BEFORE UPDATE ON movimento
FOR EACH ROW
BEGIN
	IF NEW.baixado = '1' THEN
		IF NEW.idcliente_origem THEN
			INSERT INTO saldo_pendente (idcliente, data) VALUES (NEW.idcliente_origem, NEW.data_baixa);
		END IF;
		IF NEW.idcliente_destino THEN
			INSERT INTO saldo_pendente (idcliente, data) VALUES (NEW.idcliente_destino, NEW.data_baixa);
		END IF;
	END IF;
END;
//
DELIMITER ;
