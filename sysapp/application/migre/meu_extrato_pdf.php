<?
    require_once('inc/sessao.php');
	require_once('inc/nusoap.php');

	$SEQ = "0";
	$emp = "9";

	$ob_cliente_soap = new nusoap_soapclient('http://10.63.255.16:1111/server.php');

	$ar_parametro = array(
							'cd_plano'=>$_REQUEST['cd_plano'],
							'cd_emp'=>$emp,
							'cd_re'=>$_REQUEST['re'],
							'cd_seq'=>$SEQ,
							'nr_extrato'=>$_REQUEST['nr_extrato'],
							'nr_indexador'=>$_REQUEST['nr_indexador'],
							'tp_patrocinadora'=>$_REQUEST['tp_patrocinadora'],
							'dt_base_extrato'=>$_REQUEST['dt_base_extrato']
						 );
	$resultado = $ob_cliente_soap->call('extratoPDF',$ar_parametro);
	if ($ob_cliente_soap->fault)
	{
		echo "<PRE>ERRO:<BR>".$ob_cliente_soap->faultstring;
		exit;
	}
	else
	{
		if(base64_decode($resultado) != "ERRO")
		{
			header("Cache-Control: public, must-revalidate");
			header("Pragma: hack");
			header("Content-Type: application/pdf");
			header("Content-Transfer-Encoding: binary");		
			echo base64_decode($resultado);			
		}
	}
?>