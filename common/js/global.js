<!--


	/* Funções para abrir e fechar Lightbox
	 * 
	 * Obs: no html deve ter a div: <div id="fade" class="black_overlay"></div>
	 */
	function abrirLightbox(idconteudo){
		$(idconteudo).style.display='block';
		$('fade').style.display='block';
	}
	
	function fecharLightbox(idconteudo){	
		$(idconteudo).style.display='none';
		$('fade').style.display='none'
	}
	//------------------------------------------------------------------//

	//Marca ou desmarca checkbox de um formulários
	function SetAllCheckBoxes(FormName, FieldName, CheckValue){

		if(!document.forms[FormName])
			return;
		var objCheckBoxes = document.forms[FormName].elements[FieldName];
		if(!objCheckBoxes)
			return;
		var countCheckBoxes = objCheckBoxes.length;
		if(!countCheckBoxes)
			objCheckBoxes.checked = CheckValue;
		else
			// set the check value for all check boxes
			for(var i = 0; i < countCheckBoxes; i++)
				objCheckBoxes[i].checked = CheckValue;
	}


	
	 /* Insere em um campo a hota atual do sistema.
	    Formato: hh:mm:ss 							*/
	 function setNow(formField) {
		 today = new Date();
	
		 sec = today.getSeconds();
		 ssec = sec;
		 if (sec<10) ssec = "0"+sec;
	
		 min = today.getMinutes();
		 smin = min;
		 if (min<10) smin = "0"+min;
	
		 hour = today.getHours();
		 shour = hour;
		 if (hour<10) shour = "0"+hour;
	
		 document.getElementById(formField).value = shour + ":" + smin + ":" + ssec;
	 }


	//Retorna o número de caracteres de uma string
	function strlen(string){
	    var len;
	    len = string.length;
	    return len;
	}


	/**
	 *  Verifica se o conteudo de um campo Auto Complete mudou, colocando
	 *  seu status em vermelho, ou seja, indicando que o que foi digitado não é válido
	 *  nomeID = nome do campo que guarda o ID selecionado
 	 *  nomeID_Dependentes = string com os campos ID separados por # e que sao dependentes do campo nomeID
 	 */
	function VerificaMudancaCampo(nomeID, nomeID_Dependentes) {
		var nomeTemp = nomeID + '_NomeTemp';
		var nomeCampo = nomeID + '_Nome';
		var nomeFlag = nomeID + '_Flag';
		
		
		// se houve mudança do campo, coloca como não selecionado
		if (document.getElementById(nomeTemp).value != document.getElementById(nomeCampo).value ||
			(!document.getElementById(nomeCampo).value) ||
			(document.getElementById(nomeCampo).value == '')) {

			document.getElementById(nomeFlag).className = 'nao_selecionou';
			document.getElementById(nomeID).value = '';
			
			// percorre os campos dependentes colocando limpando-os
			if ((nomeID_Dependentes != '') && (nomeID_Dependentes != null)) {
				var array_dependentes = nomeID_Dependentes.split('#');
				var cont_dep;
				for (cont_dep=0; cont_dep<array_dependentes.length; cont_dep++) {
					var nomeCampoID = array_dependentes[cont_dep];
					var nomeTemp = nomeCampoID + '_NomeTemp';
					var nomeCampo = nomeCampoID + '_Nome';
					var nomeFlag = nomeCampoID + '_Flag';

					document.getElementById(nomeFlag).className = 'nao_selecionou';
					document.getElementById(nomeCampo).value = '';
					document.getElementById(nomeCampoID).value = '';
					document.getElementById(nomeTemp).value = '';
				}
			}

		}
		// se nao houve mudança e ja foi selecionado algum registro, coloca como selecionado
		else if (document.getElementById(nomeID).value != '') {
			document.getElementById(nomeFlag).className = 'selecionou';
		}
		// nenhum registro selecionado
		else {

			document.getElementById(nomeFlag).className = 'nao_selecionou';
		}

		return (true);
  }
  
  
  /* Verifica se preencheu a filial no login */
  function VerificaFilial() {
    if (document.getElementById("idfilial").value == "") {
			alert('Preencha o campo Filial !');
			return (false);
		}

    return (true);
  }
  

  function post(frm) {
    document.getElementById(frm + "_chk").value = 0;
    document.getElementById(frm).submit();
  }

  function mask(targ, type, rev) {

    var patt = new Array();

    patt['cep'] = '##.###-###';
    patt['cnpj'] = '##.###.###/####-##';
    patt['cpf'] = '###.###.###-##';
    patt['data'] = '##/##/####';
    patt['hora'] = '##:##:##';
    patt['tel'] = '####-####';
    patt['valor'] = '####-####';

    ele = document.getElementById(targ);
    val = ele.value;
    pos = ele.value.length - 1;

    msk = patt[type];

    if(rev) {
      pos = val.length - pos;
    }
    if(msk.charAt(pos) != '#' && val.charAt(pos) != msk.charAt(pos)) {
      ele.value = val.substring(0, pos) + msk.charAt(pos) + val.charAt(pos);
    }
  }

  
  function FormataValor(arg, casas){
  	
	if( casas == undefined ){
		casas = 2;
	}	

    var campo = document.getElementById(arg);
    var strVal = campo.value;
	 var SubStrVal = strVal.substring( (strVal.length-1), strVal.length)

//	if ( isNaN(strVal.substring( (strVal.length-1), strVal.length) ) ) {


	if ( isNaN(SubStrVal) && SubStrVal != '-') {

				strVal = strVal.substring(0, strVal.length-1);
	}
	else {

		//retira a vírgula
		strVal = strVal.substring(0, strVal.indexOf(",")) + strVal.substring(strVal.indexOf(",") + 1 , strVal.length);
		
		while(strVal.length < (casas+1))
		    strVal = "0" + strVal;

		if ((strVal.substring(0,1) == "0") && (strVal.length == 4))
			strVal = strVal.substring(1, strVal.length);

		if(strVal.length > casas)
			//strVal = parseInt(strVal.substring(0, strVal.length - casas)).toString() + "," + strVal.substring(strVal.length - casas, strVal.length );
			strVal = strVal.substring(0, strVal.length - casas).toString() + "," + strVal.substring(strVal.length - casas, strVal.length );


	}

		campo.value = strVal;

  }
  	

  function FormataInteiro(arg){
    var campo = document.getElementById(arg);
    var strVal;

    strVal = campo.value;

		if ( isNaN(strVal.substring( (strVal.length-1), strVal.length) ) ) {
				strVal = strVal.substring(0, strVal.length-1);
		}

		campo.value = strVal;

	}

  	
    


  function AbreJanela(url,nome,propriedades) {
    window.open(url,nome,propriedades);
		return true;
  }

    
  //Função para confirmar a exclusão dos registros
  function confDelete(frm, endereco, mensagem) {
    if(confirm(mensagem)) {
      document.getElementById(frm).action = endereco;
      document.getElementById(frm).submit();
      return(true);
    }
    else {
        return(false);
    }
  }

	//Função para confirmar a exclusão dos registros - MAIS SIMPLES - JOAQUIM
  function confDeleteSimples(mensagem) {
    if(confirm(mensagem)) {
      return(true);
    }
    else {
      return(false);
    }
  }
  
  //Função para confirmar a exclusão dos registros sem mensagem
  function confDeleteSemMensagem(frm, endereco) {
    document.getElementById(frm).action = endereco;
    document.getElementById(frm).submit();
    return(true);
  }


  //Função para marcar todos os checkboxes com base no chk_exc_all - JOAQUIM
	function checkAll(form){
		var state;  //gurada o estado do checkbox principal
		var cont = 0;   //contador
		var msg_cod;

		state = form.elements["chk_exc_all"].checked;

		while(form.elements["hid_msg_" + cont] != null){
			msg_cod = form.elements["hid_msg_" + cont].value;
			form.elements["chk_exc_" + msg_cod].checked = state;
			cont++;
		}
		return true;
	}
	
  //Função para marcar todos os checkboxes com base no chk_exc_all - JOAQUIM
	function checkAll_generic(form, radical){
		var state;  //gurada o estado do checkbox principal
		var cont = 0;   //contador
		var cod;

		state = form.elements["chk_exc_all"].checked;

		while(form.elements["hid_" + radical + "_" + cont] != null){
			cod = form.elements["hid_" + radical + "_" + cont].value;
			form.elements["chk_exc_" + cod].checked = state;
			cont++;
		}
		return true;
	}




//-->
