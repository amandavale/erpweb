
/**
 * Script que cont�m fun��es que permitem selecionar todos os campos de checkbox
 * em um formul�rio, ou retirar a sele��o de todos os campos 
 */

function selecionar_todos(){
	
   for (var i=0;i<document.for_retorno.elements.length;i++)
      if(document.for_retorno.elements[i].type == "checkbox")
         document.for_retorno.elements[i].checked=1;
}

function selecionar_nenhum(){
   for (var i=0;i<document.for_retorno.elements.length;i++)
      if(document.for_retorno.elements[i].type == "checkbox")
         document.for_retorno.elements[i].checked=0;
} 