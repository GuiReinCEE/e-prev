<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

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
			   'CONTRIBUICAO_MAIS_CEEEPREV'
			 );";
	@pg_query($db,$qr_sql);

	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();

	include_once('auto_atendimento_monta_sessao.php');

	function get_boletos_gerados_eventual() 
	{
		$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_boletos_gerados_eventual");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_RETORNO = curl_exec($ch);
		curl_close ($ch);

		return $_RETORNO;
	}

	function get_vencimento_eventual() 
	{
		$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_vencimento_eventual");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_RETORNO = curl_exec($ch);
		curl_close ($ch);

		return $_RETORNO;
	}

	function get_info_contribuicao() 
	{
		$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_info_contribuicao");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_RETORNO = curl_exec($ch);
		curl_close ($ch);

		return $_RETORNO;
	}

	function get_valores_pagamento_upceee() 
	{
		$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_valores_pagamento_upceee");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_RETORNO = curl_exec($ch);
		curl_close ($ch);

		return $_RETORNO;
	}

/*
	#TODA A FFP
	if($_SESSION['EMP'] != 9)
	{
		echo "em manutenção";
		exit;
	}
*/
	
	$ds_arq   = "tpl/tpl_auto_atendimento_contribuicao_mais_ceeeprev.html";

	$ob_arq   = fopen($ds_arq, 'r');
	$conteudo = fread($ob_arq, filesize($ds_arq));
	fclose($ob_arq);	

	$FL_RETORNO = TRUE;
	$_RETORNO = json_decode(get_info_contribuicao(), TRUE);
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

	$display_botao = 'style="display:none"';

	if($FL_RETORNO)
	{
		if(intval($_RETORNO['error']['status']) == 0)
		{
			$mensagem = $_RETORNO['result']['ds_mensagem'];

			$mensagem = implode("</br>", $mensagem);

			$conteudo = str_replace('{MENSAGEM}', utf8_decode($mensagem), $conteudo);

			if(trim($_RETORNO['result']['fl_contribuicao']) == 'S')
			{
				$display_botao = '';
			}
		}
	}

	$conteudo = str_replace('{DISPLAY_SOLICITACAO}', $display_botao, $conteudo);

	$FL_RETORNO = TRUE;
	$_RETORNO = json_decode(get_valores_pagamento_upceee(), TRUE);
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
			$valores = $_RETORNO['result']['valores'];

			$valores_option = '';

			if(count($valores) > 0)		
			{
				foreach ($valores as $key => $item) 
				{
					$valores_option .= '<option value="'.$item['qt_upceee'].'-'.$item['valor'].'">R$ '.$item['valor'].' - ('.$item['qt_upceee'].' upceee)</option>';
				}
			}

			$conteudo = str_replace('{VALOR_UPCEEE}', $valores_option, $conteudo);
		}
	}

	$FL_RETORNO = TRUE;
	$_RETORNO = json_decode(get_vencimento_eventual(), TRUE);
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
	/*
	echo "X".$FL_RETORNO;
	echo "<HR>";
	echo "<PRE>"; print_r($_RETORNO);  
	echo "<HR>";	
	exit;
	*/
	if($FL_RETORNO)
	{
		if(intval($_RETORNO['error']['status']) == 0)
		{
			$vencimento = $_RETORNO['result']['vencimento'];

			$vencimento_option = '';

			if(count($vencimento) > 0)		
			{
				foreach ($vencimento as $key => $item) 
				{
					$vencimento_option .= '<option value="'.$item.'">'.$item.'</option>';
				}
			}

			$conteudo = str_replace('{VENCIMENTO}', $vencimento_option, $conteudo);
		}
	}

	$FL_RETORNO = TRUE;
	$_RETORNO = json_decode(get_boletos_gerados_eventual(), TRUE);
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
	/*
	echo "X".$FL_RETORNO;
	echo "<HR>";
	echo "<PRE>"; print_r($_RETORNO);  
	echo "<HR>";	
	exit;
	*/
	if($FL_RETORNO)
	{
		if(intval($_RETORNO['error']['status']) == 0)
		{
			$boleto = $_RETORNO['result']['boleto'];

			$registro_boleto = '
				<tr><td colspan="4">Nenhum registro efetuado</td></tr>
			';

			if(count($boleto) > 0)		
			{
				$registro_boleto = '';
				foreach ($boleto as $key => $item) 
				{
					$color = "green";

					if(trim($item['ds_status']) == 'AGUARDANDO PAGTO') 
					{
						$color = "orange";
					}
					else if (trim($item['ds_status']) == 'VENCIDO') 
					{
						$color = "red";
					}
					else if (trim($item['ds_status']) == 'CANCELADO') 
					{
						$color = "red";
					}
	
					$link = $item['dt_solicitacao'];

					if(trim($item['fl_gera_boleto']) == 'S') 
					{
						$link = '<a href="auto_atendimento_contribuicao_ceeeprev_gerado.php?cd_log='.MD5($item['cd_log']).'">'.$item['dt_solicitacao'].'</a>';
					}

					$registro_boleto .= '
						<tr>
							<td style="text-align:center;">'.$link.'</td>
							<td style="text-align:center;">'.$item['dt_vencimento'].'</td>
							<td style="text-align:right;">'.$item['vl_solicitado'].'</td>
							<td style="text-align:center; color:'.trim($color).'; font-weight:bold;">'.utf8_decode($item['ds_status']).'</td>
						</tr>
					';
				}
			}
			

			$conteudo = str_replace('{REGISTRO_BOLETO}', $registro_boleto, $conteudo);
		}
	}


	$tpl->assign('conteudo',$conteudo);

	$tpl->printToScreen();

?>