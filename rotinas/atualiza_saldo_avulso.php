<?php

	$diretorio_raiz = '/var/www/erpweb_backup';
        require_once("$diretorio_raiz/common/lib/conf.inc.php");
        require_once("$diretorio_raiz/common/lib/db.inc.php");
        require_once("$diretorio_raiz/common/lib/form.inc.php");

        require_once $diretorio_raiz . '/entidades/saldo.php';

        $data = $_GET['data'];
        $cliente = $_GET['cliente'];


        $form = new form();
        $db = new db();

        $saldo = new saldo();

error_reporting(E_ALL);


//      $saldo->atualizaSaldo('2017-06-27', 1616);
        $saldo->atualizaSaldo($data, $cliente);


