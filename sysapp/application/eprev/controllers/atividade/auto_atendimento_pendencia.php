<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();

	include_once('auto_atendimento_monta_sessao.php');

	$ds_arq   = "tpl/tpl_auto_atendimento_pendencia.html";

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
			   'PENDENCIA'
			 );";
	@pg_query($db,$qr_sql);

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_lista_pendencia");
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

	$lista = '';

	if($FL_RETORNO)
	{
		if(intval($_RETORNO['error']['status']) == 0)
		{
			if(count($_RETORNO['result']['pendencia']) > 0)
			{	
				foreach ($_RETORNO['result']['pendencia'] as $key => $item) 
				{
					$lista .= '
						<tr>
			                <td style="text-align:left;">'.utf8_decode($item['ds_pendencia']).'</td>
			                <td style="text-align:center;">'.$item['dt_inicio'].'</td>
			                <td style="text-align:center;">
			                	<label style="color:'.$item['ds_limite_color'].'; font-weight:bold;">'.$item['dt_limite'].'</label>
			                </td>
			                <td style="text-align:center;">
			                	<label style="color:'.$item['ds_status_color'].'; font-weight:bold;">'.$item['ds_status'].'</label>
			                </td>
			                <td style="text-align:center;">
			                	'.(trim($item['ds_url']) != '' ? '<a href="'.$item['ds_url'].'" target="_blank">[acessar]</a>' : '').'
			                </td>
			            </tr>';
				}
			}
			else
			{
				$lista .= '
						<tr>
			                <td style="text-align:center;" colspan="3">Nenhuma pendência cadastrada.</td>
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

	$conteudo = str_replace('{LISTA_PENDENCIA}', $lista, $conteudo);

	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();