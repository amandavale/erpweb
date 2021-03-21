<?php

	/**
	 * Substitui em um texto todos os caracteres que possuem acento
	 * pelo correspondente sem acento
	 * @param String $texto
	 */
	function substituiCaracteresEspeciais($texto, $retorna_maiusculas = false){

		if($texto){
		
			$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ‗אבגדהוזחטיךכלםמןנסעףפץצר°שתû‎‎‏ÿ,';
			$b = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYBSaaaaaaaceeeeiiiidnooooooouuuyyby ';
			$texto = strtr($texto,$a,$b);
			
			if($retorna_maiusculas){
				$texto = strtoupper(($texto));
			}
		}
		else{
			$texto = '';
		}
			

		return $texto;
	}