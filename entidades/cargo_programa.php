<?php
	
	class cargo_programa {

		var $err;
		
		/**
    * construtor da classe
  	*/
		function cargo_programa(){
			// não faz nada
		}


			/**
		  método: Consulta_Programa
		  propósito: busca informações
		*/
		function Consulta_Programa_Cargo($idcargo){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											CPROG.*, CRG.nome_cargo
										FROM
											{$conf['db_name']}cargo_programa CPROG
											INNER JOIN {$conf['db_name']}cargo CRG ON CPROG.idcargo=CRG.idcargo 
										WHERE
											 CPROG.idcargo = $idcargo";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);
			
						
			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida
				
				$list_return = array();
				
				$cont=0;
				
				while($list = $db->fetch_array($get_q)){
					
					$list['index'] = $cont;
					$list_return[] = $list;
					
					$cont++;
				}
				
				return $list_return;		
								
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}
				
		}

		
		


		/**
		 * método: Monta_Programas
		 * propósito: montar a tabela de programas para o usuário preencher.
		 */
		
		function Monta_Tabela_Programa(){
			
      global $conf;
     	global $db;
      
     	      
				$get_sql = "	SELECT DISTINCT
												MDL.*, count(DISTINCT SMDL.idsubmodulo) as qtd_sub
											FROM
												{$conf['db_name']}modulo MDL
												INNER JOIN {$conf['db_name']}submodulo SMDL ON MDL.idmodulo = SMDL.idmodulo
												INNER JOIN {$conf['db_name']}programa PROG ON SMDL.idsubmodulo = PROG.idsubmodulo
				
											GROUP BY MDL.ordem_modulo 
											ORDER BY MDL.ordem_modulo ASC";

				$get_q = $db->query($get_sql);
				
				
				
				$cont=0;
				$cont3=0;
				$list_modulo = array();
					while($list = $db->fetch_array($get_q)){
						
						// nome da tabela criada
    		  	$nome_tabela = "tabela_modulo_" . $list['idmodulo'];
						$codigoModulo = $list['idmodulo'];	
						$list['index'] = $cont+1;
						$mod = "modulo";
						$ad = "adicionar";
						
						
						// tabela de fornecedor
						$list['tab_mod'] = utf8_encode("
						
						<table width='100%' cellpadding='5' id='$nome_tabela'>
							<tr onmouseover='this.style.background=\"#ddeeff\";' onmouseout='this.style.background=\"#ffffff\";'>
								
								<input type='hidden' name='modulo_$codigoModulo' id='modulo_$codigoModulo' value='$codigoModulo'/>
								<td class='tb_bord_baixo' align='left' width='55%'><div id=div_m_$codigoModulo onClick='xajax_Manipula_Div($codigoModulo,0,0);'>+&nbsp;{$list['nome_modulo']}</div></td>
								<td class='tb_bord_baixo' align='center' width='15%'><input class='radio' type='checkbox' name='permissao_adicionar_modulo_$codigoModulo' id='permissao_adicionar_modulo_$codigoModulo' onClick='xajax_Seleciona_Herdeiro(0,$codigoModulo,0)'/> </td>
								<td class='tb_bord_baixo' align='center' width='10%'>
								<input class='radio' type='checkbox' name='permissao_editar_modulo_$codigoModulo' id='permissao_editar_modulo_$codigoModulo' onClick='xajax_Seleciona_Herdeiro(0,$codigoModulo,1)'/> </td>
								<td class='tb_bord_baixo' align='center' width='10%'>
								<input class='radio' type='checkbox' name='permissao_excluir_modulo_$codigoModulo' id='permissao_excluir_modulo_$codigoModulo' onClick='xajax_Seleciona_Herdeiro(0,$codigoModulo,2)'/> </td>
								<td class='tb_bord_baixo' align='center' width='10%'>
								<input class='radio' type='checkbox' name='permissao_listar_modulo_$codigoModulo' id='permissao_listar_modulo_$codigoModulo' onClick='xajax_Seleciona_Herdeiro(0,$codigoModulo,3)'/> </td>
								</td>
							</tr>
						</table>
						<div  style='display:none' id='div_modulo_$codigoModulo' name='div_modulo_$codigoModulo'> 
					");
						$list_modulo[] = $list;
						
						$tabela_final= $tabela_final . $list['tab_mod'];
						
						$get_sql2 = "	SELECT DISTINCT
												SMDL.*, count(DISTINCT PROG.idprograma) as qtd_prog, PROG.*
											FROM
												{$conf['db_name']}submodulo SMDL 
												INNER JOIN {$conf['db_name']}programa PROG ON SMDL.idsubmodulo = PROG.idsubmodulo
											WHERE
												 SMDL.idmodulo = " . $list['idmodulo'] . " 
											
											GROUP BY SMDL.ordem_submodulo											 
											ORDER BY SMDL.ordem_submodulo ASC";
				
						$get_q2 = $db->query($get_sql2);
						
						$cont2=0;
						$list_submodulo = array();
							while($list2 = $db->fetch_array($get_q2)){
								
								$list2['index'] = $cont2+1;
								// nome da tabela criada
    		  			$nome_tabela = "tabela_submodulo_" . $list2['idsubmodulo'];
								$codigoSubmodulo = $list2['idsubmodulo'];
								$final = $list2['submodulo_final'];			
								$sub = "submodulo";
								$codigo_programa = $list2['idprograma'];			
								
									if ($list2['submodulo_final'] ==0)$list2['tab_sub'] = utf8_encode("
									
										<table width='100%' cellpadding='5' id='$nome_tabela'>
											<tr onmouseover='this.style.background=\"#ddeeff\";' onmouseout='this.style.background=\"#ffffff\";'>
												
												<input type='hidden' name='submodulo_$codigoSubmodulo' id='submodulo_$codigoSubmodulo' value='$codigoSubmodulo'/>
												<input type='hidden' name='submodulo_final_$codigoSubmodulo' id='submodulo_final_$codigoSubmodulo' value='$final'/>
												<input type='hidden' name='sub_programa_$codigoSubmodulo' id='sub_programa_$codigoSubmodulo' value='$codigo_programa'/>
												<td class='tb_bord_baixo' align='left' width='55%'><div id=div_s_$codigoSubmodulo onClick='xajax_Manipula_Div($codigoSubmodulo,1,0);'>&nbsp;&nbsp;&nbsp;&nbsp;+&nbsp;{$list2['nome_submodulo']}</div></td>
												<td class='tb_bord_baixo' align='center' width='15%'>
												<input class='radio' type='checkbox' name='permissao_adicionar_submodulo_$codigoSubmodulo' id='permissao_adicionar_submodulo_$codigoSubmodulo' onClick='xajax_Seleciona_Herdeiro(1,$codigoSubmodulo,0)' /> </td>
												<td class='tb_bord_baixo' align='center' width='10%'>
												<input class='radio' type='checkbox' name='permissao_editar_submodulo_$codigoSubmodulo' id='permissao_editar_submodulo_$codigoSubmodulo' onClick='xajax_Seleciona_Herdeiro(1,$codigoSubmodulo,1)'/> </td>
												<td class='tb_bord_baixo' align='center' width='10%'>
												<input class='radio' type='checkbox' name='permissao_excluir_submodulo_$codigoSubmodulo' id='permissao_excluir_submodulo_$codigoSubmodulo' onClick='xajax_Seleciona_Herdeiro(1,$codigoSubmodulo,2)'/> </td>
												<td class='tb_bord_baixo' align='center' width='10%'>
												<input class='radio' type='checkbox' name='permissao_listar_submodulo_$codigoSubmodulo' id='permissao_listar_submodulo_$codigoSubmodulo' onClick='xajax_Seleciona_Herdeiro(1,$codigoSubmodulo,3)'/> </td>
												</td>
											</tr>
										</table>
										<div  style='display:none' id='div_submodulo_$codigoSubmodulo' name='div_submodulo_$codigoSubmodulo'>  ");

								$list_submodulo = $list2;
							
								$tabela_final = $tabela_final . $list2['tab_sub'];
								
								$get_sql3 = "	SELECT DISTINCT
												PROG.*, count(DISTINCT PROG.idprograma) as qtd_prog
											FROM
												{$conf['db_name']}programa PROG
												
											WHERE
												 PROG.idsubmodulo = " . $list2['idsubmodulo'] . " 
											
											GROUP BY PROG.ordem_programa											 
											ORDER BY PROG.ordem_programa ASC";
				
								$get_q3 = $db->query($get_sql3);
								
								
								$list_programa = array();
									while($list3 = $db->fetch_array($get_q3)){
										
										$list3['index'] = $cont3+1;
										
										// nome da tabela criada
    		  					$nome_tabela = "tabela_programa_" . $list3['idprograma'];
										$codigoPrograma = $list3['idprograma'];
										
										
										
										if($final == 1) {$tipopai = "0"; $idpai = $codigoModulo;$qtd = $list['qtd_sub'];}
										else {$tipopai = "1"; $idpai = $codigoSubmodulo;$qtd = $list2['qtd_prog'];}
										
										if($list3['define_adicionar'] == 1) $definir_adicionar = "<input class='radio' type='checkbox' name='permissao_adicionar_programa_$codigoPrograma' id='permissao_adicionar_programa_$codigoPrograma' onClick='xajax_Verifica_Pai($idpai,$tipopai,$codigoPrograma,$qtd,0)'/> ";
										else $definir_adicionar = "&nbsp;&nbsp";
										
										if($list3['define_editar'] == 1) $definir_editar = "<input class='radio' type='checkbox' name='permissao_editar_programa_$codigoPrograma' id='permissao_editar_programa_$codigoPrograma' onClick='xajax_Verifica_Pai($idpai,$tipopai,$codigoPrograma,$qtd,1)'/> ";
										else $definir_editar = "&nbsp;&nbsp";
										
										if($list3['define_excluir'] == 1) $definir_excluir = "<input class='radio' type='checkbox' name='permissao_excluir_programa_$codigoPrograma' id='permissao_excluir_programa_$codigoPrograma' onClick='xajax_Verifica_Pai($idpai,$tipopai,$codigoPrograma,$qtd,2)'/>";
										else $definir_excluir = "&nbsp;&nbsp";
						
										if($list3['define_listar'] == 1) $definir_listar = "<input class='radio' type='checkbox' name='permissao_listar_programa_$codigoPrograma' id='permissao_listar_programa_$codigoPrograma' onClick='xajax_Verifica_Pai($idpai,$tipopai,$codigoPrograma,$qtd,3)'/> ";
										else $definir_listar = "&nbsp;&nbsp";
																		
										if ($list2['submodulo_final'] ==1)
                    {          
										$list3['tab_prog'] = utf8_encode("
										<table width='100%' cellpadding='5' id='$nome_tabela'>
											<tr onmouseover='this.style.background=\"#ddeeff\";' onmouseout='this.style.background=\"#ffffff\";'>
												
												<input type='hidden' name='programa_$cont3' id='programa_$cont3' value='$codigoPrograma'/>
												<td class='tb_bord_baixo' align='left' width='55%'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$list3['nome_programa']}</td>
												<td class='tb_bord_baixo' align='center' width='15%'>$definir_adicionar</td>
												<td class='tb_bord_baixo' align='center' width='10%'>$definir_editar</td>
												<td class='tb_bord_baixo' align='center' width='10%'>$definir_excluir</td>
												<td class='tb_bord_baixo' align='center' width='10%'>$definir_listar</td>
												</td>
											</tr>
										</table>");
										}
                    else
                    {
                    $list3['tab_prog'] = utf8_encode("
                    <table width='100%' cellpadding='5' id='$nome_tabela'>
                      <tr onmouseover='this.style.background=\"#ddeeff\";' onmouseout='this.style.background=\"#ffffff\";'>
                        
                        <input type='hidden' name='programa_$cont3' id='programa_$cont3' value='$codigoPrograma'/>
                        <td class='tb_bord_baixo' align='left' width='55%'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$list3['nome_programa']}</td>
                        <td class='tb_bord_baixo' align='center' width='15%'>$definir_adicionar</td>
                        <td class='tb_bord_baixo' align='center' width='10%'>$definir_editar</td>
                        <td class='tb_bord_baixo' align='center' width='10%'>$definir_excluir</td>
                        <td class='tb_bord_baixo' align='center' width='10%'>$definir_listar</td>
                        </td>
                      </tr>
                    </table>");
                    }
										
										$list_programa[] = $list3;
										
										$tabela_final = $tabela_final. $list3['tab_prog'];
																		
							$cont3++;
							}
							if($final != 1)$tabela_final = $tabela_final . "</div>";	
				
				$cont2++;	
				}
			$tabela_final = $tabela_final . "</div>";			
			$cont++;
			}
			
			return $tabela_final;
		}
		
		
		/**
		  método: Consulta_Programa
		  propósito: busca informações
		*/
		function Consulta_Programa($idcargo,$idprograma){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											CPROG.*, CRG.nome_cargo
										FROM
											{$conf['db_name']}cargo_programa CPROG
											INNER JOIN {$conf['db_name']}cargo CRG ON CPROG.idcargo=CRG.idcargo 
										WHERE
											 CPROG.idcargo = $idcargo";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);
			
						
			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				while($list = $db->fetch_array($get_q)){
					
					if($list['idprograma'] == $idprograma) return true;
				}
				
				return false;		
								
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}
				
		}

		

		/**
		  método: delete_programa
		  propósito: excluir registro de programa a partir apenas do cargo
		*/
		function delete_programa($idcargo){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			// conjunto de dependências geradas
			
			//---------------------
			

			// verifica se pode excluir
			if (1) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}cargo_programa
												WHERE
													 idcargo = $idcargo  ";
				$delete_q = $db->query($delete_sql);

				if($delete_q){
					return(1);
				}
				else{
					$this->err = $falha['excluir'];
					return(0);
				}
				
			}
			else {
				$this->err = "Este registro não pode ser excluído, pois existem registros relacionadas a ele.";
			}	

		}	
		
		
		


		/**
		  método: getById
		  propósito: busca informações
		*/
		function getById($idcargo,$idprograma){

			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$get_sql = "	SELECT
											CPROG.*, CRG.nome_cargo
										FROM
											{$conf['db_name']}cargo_programa CPROG
											INNER JOIN {$conf['db_name']}cargo CRG ON CPROG.idcargo=CRG.idcargo 
										WHERE
											 CPROG.idcargo = $idcargo AND  CPROG.idprograma = $idprograma ";

			//executa a query no banco de dados
			$get_q = $db->query($get_sql);

			//testa se a consulta foi bem sucedida
			if($get_q){ //foi bem sucedida

				$get = $db->fetch_array($get_q);
				
				
				
				
				
				//retorna o vetor associativo com os dados
				return $get;
			}
			else{ //deu erro no banco de dados
				$this->err = $falha['listar'];
				return(0);
			}
				
		}

		/**
		  método: make_list
		  propósito: faz a listagem
		*/
		
		function make_list( $pg, $rppg, $filtro = "", $ordem = "", $url = ""){
			
			if ($ordem == "") $ordem = " 	GROUP BY CRG.nome_cargo 
											ORDER BY CPROG.idcargo ASC";
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			$list_sql = "	SELECT 
											CPROG.*   , CRG.nome_cargo
										FROM
           						{$conf['db_name']}cargo_programa CPROG 
												 INNER JOIN {$conf['db_name']}cargo CRG ON CPROG.idcargo=CRG.idcargo 
																					
										$filtro
										$ordem";

			//manda fazer a paginação
			$list_q = $form->paginacao_completa($list_sql, $pg, $rppg, $url);

			if($list_q){
				
				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){
					
					//insere um índice na listagem
					$list['index'] = $cont+1 + ($pg*$rppg);
					
					
					
					
					
					
					
          $list_return[] = $list;
          
          $cont++;
				}
				
				return $list_return;
					
			}	
			else{
				$this->err = $falha['listar'];
				return(0);
			}
		}	
		

		/**
			método: set
		  propósito: inclui novo registro
		*/

		function set($info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			
			$set_sql = "  INSERT INTO
		                  {$conf['db_name']}cargo_programa
		                    (
		                    
												idcargo, 
												idprograma, 
												permissao_adicionar, 
												permissao_editar, 
												permissao_excluir, 
												permissao_listar  
												
												)
		                VALUES
		                    (
		                    
		                    " . $info['idcargo'] . ",  
												" . $info['idprograma'] . ",  
												'" . $info['permissao_adicionar'] . "',  
												'" . $info['permissao_editar'] . "',  
												'" . $info['permissao_excluir'] . "',  
												'" . $info['permissao_listar'] . "'   
												
												)";
			
			
			//executa a query e testa se a consulta foi "boa"
			if($db->query($set_sql)){
				//retorna o código inserido
				$codigo = $db->insert_id();
				
				
				
				return($codigo);
			}
			else{
				$this->err = $falha['inserir'];
				return(0);
			}
		}
		
		/**
		  método: update
		  propósito: atualiza os dados
		  
		  1) o vetor $info deve conter todos os campos tabela a serem atualizados
			2) a variável $id deve conter o código do usuário cujos dados serão atualizados
			3) campos literais deverão ter o prefixo lit e campos numéricos deverão ter o prefixo num
		*/
		function update($idcargo,$idprograma, $info){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			//inicializa a query
			$update_sql = "	UPDATE
												{$conf['db_name']}cargo_programa
											SET ";

   		//varre o formulário e monta a consulta;
			$cont_validos = 0;
			foreach($info as $campo => $valor){

				$tipo_campo = substr($campo, 0, 3);
				$nome_campo = substr($campo, 3, strlen($campo) - 3);
					
				if(($tipo_campo == "lit") || ($tipo_campo == "num")){
					
					$usu_validos["$campo"] = $valor;
					$cont_validos++;
					
				}
					
			}
			
			$cont = 0;
			foreach($usu_validos as $campo => $valor){
			
				$tipo_campo = substr($campo, 0, 3);
				$nome_campo = substr($campo, 3, strlen($campo) - 3);
				
				if($tipo_campo == "lit")
					$update_sql .= "$nome_campo = '$valor'";
				elseif($tipo_campo == "num")
					$update_sql .= "$nome_campo = $valor";
					
				$cont++;
				
				//testa se é o último
				if($cont != $cont_validos){
					$update_sql .= ", ";
				}
				
			}
			

			//completa o sql com a restrição
			$update_sql .= " WHERE  idcargo = $idcargo AND  idprograma = $idprograma ";
			
			
			//envia a query para o banco
			$update_q = $db->query($update_sql);
			
			if($update_q)
			  return(1);
			else
			  $this->err = $falha['alterar'];
		}	
		
		/**
		  método: delete
		  propósito: excluir registro
		*/
		function delete($idcargo,$idprograma){
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			
			// conjunto de dependências geradas
			
			//---------------------
			

			// verifica se pode excluir
			if (1) {

				

				$delete_sql = "	DELETE FROM
													{$conf['db_name']}cargo_programa
												WHERE
													 idcargo = $idcargo AND  idprograma = $idprograma ";
				$delete_q = $db->query($delete_sql);

				if($delete_q){
					return(1);
				}
				else{
					$this->err = $falha['excluir'];
					return(0);
				}
				
			}
			else {
				$this->err = "Este registro não pode ser excluído, pois existem registros relacionadas a ele.";
			}	

		}	

		
		/**
		  método: make_list
		  propósito: faz a listagem para colocar no select
		*/

		function make_list_select( $filtro = "", $ordem = " ORDER BY idcargo ASC") {
			
			// variáveis globais
			global $form;
			global $conf;
			global $db;
			global $falha;
			//---------------------
			

			$list_sql = "	SELECT
					 						*
										FROM
											{$conf['db_name']}cargo_programa
										$filtro
										$ordem";

			$list_q = $db->query($list_sql);
			if($list_q){

				//busca os registros no banco de dados e monta o vetor de retorno
				$list_return = array();
				$cont = 0;
				while($list = $db->fetch_array($list_q)){

					foreach($list as $campo => $value){
						$list_return["$campo"][$cont] = $value;
					}

          $cont++;
				}

				return $list_return;

			}
			else{
				$this->err = $falha['listar'];
				return(0);
			}
			
		}
		
		
		
		

	} // fim da classe
?>
