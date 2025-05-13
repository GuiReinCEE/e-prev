<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();

	include_once('auto_atendimento_monta_sessao.php');

	$ds_arq   = "tpl/tpl_auto_atendimento_doc_encaminhado.html";

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
			   'DOC_ENCAMINHADO'
			 );";
	@pg_query($db,$qr_sql);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_doc_encaminhado_tipo");
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

	$tipo_doc = '';

	if($FL_RETORNO)
	{
		if(intval($_RETORNO['error']['status']) == 0)
		{
			foreach ($_RETORNO['result']['tipo'] as $key => $item) 
			{
				$selected = '';

				if($_GET['doc'] == $item['cd_doc_encaminhado_tipo_doc'])
				{
					$selected = 'selected=""';
				}

				$tipo_doc .= '<option value="'.$item['cd_doc_encaminhado_tipo_doc'].'" '.$selected.'>'.utf8_decode($item['ds_documento']).'</option>';
			}
		}
		else
		{
			#echo 'ERRO - [2]<br/>';
			#echo implode(' ', $_RETORNO['error']['mensagem']);
		}
	}
	else 
	{
		#echo 'ERRO [1]';
	}

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_doc_encaminhado");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&fl_encaminhado=N");
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

	$lista_doc_nao_encaminhado   = '';
	$display_doc_nao_encaminhado = 'display:none;';
	$fl_doc_nao_encaminhado      = 'N';

	if($FL_RETORNO)
	{
		if(intval($_RETORNO['error']['status']) == 0)
		{
			if(count($_RETORNO['result']['doc_encaminhado']) > 0)
			{
				$display_doc_nao_encaminhado = '';
			}

			foreach ($_RETORNO['result']['doc_encaminhado'] as $key => $item) 
			{
				$lista_doc_nao_encaminhado .= '
					<tr>
		                <td style="text-align:left;">
		                	<a href="http://app.eletroceee.com.br/srvautoatendimento/index.php/doc_encaminhado_arquivo/'.intval($item['cd_doc_encaminhado_arquivo']).'" target="_blank">
		                	'.utf8_decode($item['ds_documento_tipo']).'
		                	</a>
		                </td>
		                <td style="text-align:justify;">'.nl2br(utf8_decode($item['ds_observacao'])).'</td>
		                <td style="text-align:center;">
		                	'.(trim($item['dt_encaminhamento']) == '' 
		                		? '<a href="javascript:void(0);" onclick="excluir_documento('.$item['cd_doc_encaminhado_arquivo'].')">EXCLUIR DOCUMENTO</a>' : '').'
		                </td>
		            </tr>';
			}
		}
		else
		{
			#echo 'ERRO - [2]<br/>';
			#echo implode(' ', $_RETORNO['error']['mensagem']);
		}
	}
	else 
	{
		#echo 'ERRO [1]';
	}

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_doc_encaminhado");
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']."&fl_encaminhado=S");
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

	$lista_doc_encaminhado        = '';
	$display_doc_encaminhado      = 'display:none;';
	$display_aguardando_validacao = 'display:none;';

	if($FL_RETORNO)
	{
		if(intval($_RETORNO['error']['status']) == 0)
		{
			if(count($_RETORNO['result']['doc_encaminhado']) > 0)
			{
				$display_doc_encaminhado = '';
			}

			foreach ($_RETORNO['result']['doc_encaminhado'] as $key => $item) 
			{
				if(trim($item['fl_aguardando_validacao']) == 'S')
				{	
					$display_aguardando_validacao = '';
				}

				$lista_doc_encaminhado .= '
					<tr>
		                <td style="text-align:left;">'.utf8_decode($item['ds_documento_tipo']).'</td>
		                
		                <td style="text-align:center;">'.$item['dt_encaminhamento'].'</td>
		                <td style="text-align:justify;">'.nl2br(utf8_decode($item['ds_justificativa'])).'</td>
		                <td style="text-align:center;">
		                	<span style="color:'.$item['ds_color_status'].'; font-weight:bold;">'.utf8_decode($item['ds_status']).'</span></td>
		            </tr>';
			}
		}
		else
		{
			#echo 'ERRO - [2]<br/>';
			#echo implode(' ', $_RETORNO['error']['mensagem']);
		}
	}
	else 
	{
		#echo 'ERRO [1]';
	}

	$conteudo = str_replace('{TIPO_DOC}', $tipo_doc, $conteudo);

	$conteudo = str_replace('{LISTA_DOC_NAO_ENCAMINHADO}', $lista_doc_nao_encaminhado, $conteudo);
	$conteudo = str_replace('{DISPLAY_DOC_NAO_ENCAMINHADO}', $display_doc_nao_encaminhado, $conteudo);
	$conteudo = str_replace('{DISPLAY_AGUARDANDO_VALIDACAO}', $display_aguardando_validacao, $conteudo);

	$conteudo = str_replace('{LISTA_DOC_ENCAMINHADO}', $lista_doc_encaminhado, $conteudo);
	$conteudo = str_replace('{DISPLAY_DOC_ENCAMINHADO}', $display_doc_encaminhado, $conteudo);

	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();