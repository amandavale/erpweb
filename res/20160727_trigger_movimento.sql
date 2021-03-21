
DROP TRIGGER IF EXISTS trg_add_movimento;
DELIMITER //
CREATE TRIGGER trg_add_movimento BEFORE INSERT ON movimento
FOR EACH ROW
BEGIN
	IF NEW.baixado = '1' THEN
		SET NEW.saldo_pendente = '1';
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
		SET NEW.saldo_pendente = '1';
	END IF;
END;
//
DELIMITER ;

