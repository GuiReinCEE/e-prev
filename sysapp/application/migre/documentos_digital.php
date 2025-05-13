<?php
	require_once('inc/nusoap.php');
	$ob_cliente_soap = new nusoap_soapclient('http://10.63.255.16:1111/server.php');
	$ar_parametro = array('ds_arq'=>$_REQUEST['ds_arq']);
	
	$resultado = $ob_cliente_soap->call('converteImgParaPDF',$ar_parametro);
	if($ob_cliente_soap->fault)
	{
		echo "<PRE>ERRO:<BR>".$ob_cliente_soap->faultstring;
		exit;
	}
	else
	{
		if(base64_decode($resultado) != "ERRO")
		{
			header('Content-Type: application/pdf');
			header("Cache-Control: public, must-revalidate");
			header("Pragma: hack");
			header('Content-Disposition: inline; filename="doc.pdf"');
			header("Content-Transfer-Encoding: binary");
			echo base64_decode($resultado);
		}
		else
		{
			echo "<PRE><BR><B>Não foi possível gerar o documento.</B>";
		}
	}
?>