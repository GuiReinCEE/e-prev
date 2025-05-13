<?php
	header('Content-Type: application/pdf');
	header("Cache-Control: public, must-revalidate");
	header("Pragma: hack");
	header('Content-Disposition: inline; filename="doc.pdf"');
	header("Content-Transfer-Encoding: binary");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://".($_SERVER['SERVER_ADDR'] == "10.63.255.5" ? 'www.e-prev.com.br' : $_SERVER['SERVER_ADDR'])."/cieprev/index.php/ecrm/certificado_participante/certificadoRE/".$_REQUEST['emp']."/".$_REQUEST['re']."/".$_REQUEST['seq']);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_exec($ch);
    curl_close($ch);

	##include("../cieprev/sysapp/application/migre/cert_participantes.php");
	
	#header("Location: http://".($_SERVER['SERVER_ADDR'] == "10.63.255.5" ? 'www.e-prev.com.br' : $_SERVER['SERVER_ADDR'])."/cieprev/index.php/ecrm/certificado_participante/certificadoRE/".$_REQUEST['emp']."/".$_REQUEST['re']."/".$_REQUEST['seq'])
	
	//http://10.63.255.150/cieprev/index.php/ecrm/certificado_participante/certificadoRE/0/370592/0
	//http://www.fundacaoceee.com.br/certificado_participante.php?emp=19&re=116&seq=0
	/*$arq_cert = "http://".($_SERVER['SERVER_ADDR'] == "10.63.255.5" ? 'www.e-prev.com.br' : $_SERVER['SERVER_ADDR'])."/cieprev/index.php/ecrm/certificado_participante/certificadoRE/".$_REQUEST['emp']."/".$_REQUEST['re']."/".$_REQUEST['seq'];
	$ob_arq = fopen($arq_cert,'r');
	$conteudo = '';
	while(!feof($ob_arq))
	{
		$conteudo.= fread($ob_arq,1024);
	}
	fclose($ob_arq); 
	
	header('Content-Type: application/pdf');
	header("Cache-Control: public, must-revalidate");
	header("Pragma: hack");
	header('Content-Disposition: inline; filename="doc.pdf"');
	header("Content-Transfer-Encoding: binary");	
	echo $conteudo;
	exit;	*/
?>