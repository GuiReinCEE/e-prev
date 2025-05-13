<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

    include_once("inc/sessao_auto_atendimento.php");
    require_once('inc/conexao.php');

    $ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/obter_extrato");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&tp_extrato=".$_GET['tp_extrato']."&nr_extrato=".$_GET['nr_extrato']."&dt_inicio=".$_GET['dt_inicio']."&data_base=".$_GET['data_base']."&cd_plano=".$_GET['cd_plano']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
    curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
	$_RETORNO = curl_exec($ch);
	curl_close ($ch);

	/*
	$ob_cliente_soap = new SoapClient('http://10.63.255.16:1111/server.php?wsdl');
	#echo "<PRE>"; var_dump($ob_cliente_soap->__getFunctions()); exit;
	
	$cd_plano=$_GET['cd_plano'];
	$cd_emp=$_SESSION['EMP'];
	$cd_re=$_SESSION['RE'];
	$cd_seq=$_SESSION['SEQ'];
	$nr_extrato=$_GET['nr_extrato'];
	$nr_indexador=$_GET['nr_indexador'];
	$tp_patrocinadora=$_GET['tp_patrocinadora'];
	$dt_base_extrato=$_GET['dt_base_extrato'];
	
	$resultado = $ob_cliente_soap->extratoPDF($cd_plano,$cd_emp,$cd_re,$cd_seq,$nr_extrato,$nr_indexador,$tp_patrocinadora,$dt_base_extrato);	
	*/
	header('Content-Type: application/pdf');
    header('Cache-Control: public, must-revalidate');
    header('Pragma: hack');
    header('Content-Disposition: inline; filename="doc.pdf"');
    header('Content-Transfer-Encoding: binary');			
	echo $_RETORNO;		
?>