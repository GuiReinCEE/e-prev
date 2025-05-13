<?php
	$token = "c1656f543fa6bc16aae79d1f128933f5";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$qr_sql = "
		INSERT INTO public.log_acessos_usuario 
			 (
			   sid,
			   hora,
			   pagina
			 ) 
		VALUES
			 (
			   ".$_SESSION['SID'].",
			   CURRENT_TIMESTAMP,
			   'EMPRESTIMO_IR'
			 );";
	@pg_query($db,$qr_sql);

	$data = '31/12/'.$_GET['ano_base_ir'];

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvweb/index.php/report_ir_emprestimo");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"token=".$token."&cd_empresa=".$_SESSION['EMP']."&cd_registro_empregado=".$_SESSION['RE']."&seq_dependencia=".$_SESSION['SEQ']."&cd_contrato=".$_GET['cd_contrato']."&dt_base_ir=".$data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$_RETORNO = curl_exec($ch);
	curl_close ($ch);

	$FL_RETORNO = TRUE;
	$_RETORNO = json_decode($_RETORNO, TRUE);

	if (!(json_last_error() === JSON_ERROR_NONE))
	{
		switch (json_last_error()) 
		{
			case JSON_ERROR_NONE:
				$FL_RETORNO = TRUE;
			break;
				default:
				$FL_RETORNO = FALSE;
			break;
		}
	}

	if($FL_RETORNO)
	{
		if(intval($_RETORNO['error']['status']) == 0)
		{
			header('Content-Type: application/pdf');
            header('Cache-Control: public, must-revalidate');
            header('Pragma: hack');
            header('Content-Disposition: inline; filename="doc.pdf"');
            header('Content-Transfer-Encoding: binary');        
            echo base64_decode($_RETORNO['result']);
		}
		else
		{
			echo 'ERRO [2]';
		}
	}
	else 
	{
		echo 'ERRO [1]';
	}