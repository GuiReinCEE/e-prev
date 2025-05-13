<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();

	include_once('auto_atendimento_monta_sessao.php');
	
	$ds_arq   = "tpl/tpl_auto_atendimento_contato.html";

	$ob_arq   = fopen($ds_arq, 'r');
	$conteudo = fread($ob_arq, filesize($ds_arq));

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
			   'CONTATO'
			 );";
	@pg_query($db,$qr_sql);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_assunto_contato");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
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

	$assunto = '';

	if($FL_RETORNO)
	{
		if(intval($_RETORNO['error']['status']) == 0)
		{
			foreach ($_RETORNO['result']['assunto'] as $key => $item) 
			{
				$onclick = '';

				if(intval($item['cd_materia']) > 0)
				{
					$onclick = 'data-link="'.trim($item['ds_link']).'"';
				}

				$assunto .= '<option value="'.utf8_decode($item['ds_contato_assunto']).'" '.$onclick.'>'.utf8_decode($item['ds_contato_assunto']).'</option>';
			}
		}
	}

	$conteudo = str_replace('{ASSUNTO}', $assunto, $conteudo);

	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();

?>