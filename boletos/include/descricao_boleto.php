<?php

function retornaDescricaoCaixa($descricao_caixa,$titulo){

    $descricao = '';

    if(is_array($descricao_caixa) && !empty($descricao_caixa)){

        while(key($descricao_caixa) !== null){

            $dados_caixa = current($descricao_caixa);

            $titulo_caixa = 'DEMONSTRATIVO - ' . $dados_caixa['nome_cliente'] . ' - ' . $titulo;

            $descricao .= '<table class=descricao_condominio> ' .

                            '<tr align=center> ' .
                                '<th colspan="2">' . $titulo_caixa . '</th>' .
                            '</tr>' .
                            '<tr align=center>' .
                                '<th colspan="2">DESPESAS</th>' .
                            '</tr>' ;
                
            $descricao .= retornaDescricao($dados_caixa) . '<br />';
                            
            next($descricao_caixa);
        }
    }

    return $descricao;

}


function retornaDescricaoCondominio($descricao_condominio, $titulo){

    $descricao = '<table class=descricao_condominio> ' .

                    '<tr align=center> ' .
                        '<th colspan="2">' . $titulo . '</th>' .
                    '</tr>' .
                    '<tr align=center>' .
                        '<th colspan="2">DESPESAS</th>' .
                    '</tr>' ;


    if(is_array($descricao_condominio) && !empty($descricao_condominio)){

        $descricao .= retornaDescricao($descricao_condominio);
    }
    else{
        $descricao .= '</table>';
    }

    return $descricao;
}

function retornaDescricao($descricao_condominio){

    $descricao = '';

	$contas = $descricao_condominio['debito']['contas'];

	foreach($contas as $indice => $despesa){
		
		$descricao .= '<tr>' .
                    	'<td>' . $despesa['descricao_movimento'] . '</td>' .
                    	'<td align="right">' . $despesa['valor_movimento'] . '</td>' .
                		'</tr>';
	}

    $descricao .= '<tr>' .
                	'<td ><b>TOTAL DAS DESPESAS</b></td>' .
                	'<td align="right"><b>' . $descricao_condominio['somatorio']['debito'] . '</b></td>' .
            		'</tr>' .					
        		'</table>' .
				'<br />' .
        		'<table class=descricao_condominio>' .
            		'<tr align=center>' .
                		'<th colspan="2">SALDOS</th>' .	
            		'</tr>' .
    		        '<tr>' . 
                		'<td>SALDO DO PER&Iacute;ODO ANTERIOR</td>' .
                		'<td  align="right">' . $descricao_condominio['somatorio']['saldo_anterior'] . '</td>' .
            		'</tr>' .
            		'<tr>' . 
                		'<td>RECEITAS DO PER&Iacute;ODO</td>' .
                		'<td  align="right">' . $descricao_condominio['somatorio']['credito'] . '</td>' .
            		'</tr>' .
            		'<tr>' .
                		'<td>DESPESAS DO PER&Iacute;ODO</td>' .
                		'<td  align="right">' . $descricao_condominio['somatorio']['debito'] . '</td>' .
            		'</tr>' .
            		'<tr>' .
                		'<td><b>SALDO DO PER&Iacute;ODO</b></td>' .
                		'<td  align="right"><b>' . $descricao_condominio['somatorio']['saldo_final'] . '</b></td>' .
            		'</tr>' .
    				'</table>';
    
    if(isset($descricao_condominio['em_aberto']['qtd']) && ($descricao_condominio['em_aberto']['qtd'] > 0)){

    	$descricao .= '<table class=descricao_condominio>' .
    			'<tr align=center>' .
    				'<th colspan="2">COND&Ocirc;MINOS EM ATRASO</th>' .
    			'</tr>' .
    			'<tr>' .
    				'<td>QUANTIDADE DE BOLETOS EM ABERTO:</td>' .
    				'<td><b>' . $descricao_condominio['em_aberto']['qtd'] . '</b></td>' .
    			'</tr>' .
    			'<tr>' .
	    			'<td>TOTAL DO VALOR EM ABERTO:</td>' .
    				'<td> <b>R$' . $descricao_condominio['em_aberto']['somatorio'] . '</b></td>' .
    			'</tr>' .
    			'</table>';
    }
    
    if(isset($descricao_condominio['mensagem']) && $descricao_condominio['mensagem']){
    
    	$descricao .= '<table class=descricao_condominio>' .
    			'<tr align=center>' .
	    			'<th colspan="2">OBSERVA&Ccedil;&Otilde;ES</th>' .
    			'</tr>' .
    			'<tr>' .
    				'<td>' . $descricao_condominio['mensagem'] . '</td>' .
    			'</tr>' .
    			'</table>';
    }
    

    
    return $descricao;
}
