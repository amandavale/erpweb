<!--

	/*
	Processa as Tabs da Pagina
	indice: numero do tab, inteiro que vai de 0 até n
	prefixo_tab_pai:  string que indica o prefixo do tab pai
	*/

	function Processa_Tabs (indice, prefixo_tab_pai) {
		var cont_tab = 0;

		while ( document.getElementById(prefixo_tab_pai + cont_tab) != null ) {
			document.getElementById(prefixo_tab_pai + cont_tab).style.display = 'none';
			document.getElementById('a_' + prefixo_tab_pai + cont_tab).style.background = '#E8E8E8';
			cont_tab++;
		}	// while

		document.getElementById(prefixo_tab_pai + indice).style.display = 'block';
		document.getElementById('a_' + prefixo_tab_pai + indice).style.background = '#ffffff';

	}

	

//-->
