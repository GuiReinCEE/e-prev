<?php
	$_APP_ID  = "e7a9e3f647dd33941430647118aaf2b7";

	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();

	include_once('auto_atendimento_monta_sessao.php');

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
			   'AUTORIZACAO_DESCONTO_ENTIDADES'
			 );";
	@pg_query($db,$qr_sql);

	$ds_arq   = "tpl/tpl_auto_atendimento_desconto_entidades.html";

	$ob_arq   = fopen($ds_arq, 'r');
	$conteudo = fread($ob_arq, filesize($ds_arq));
	fclose($ob_arq);	

	if(in_array(trim($_SESSION['TIPO_PARTI']), array("APOS","PENS","AUXD")))
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, APP_SRV."/srvautoatendimento/index.php/get_lista_autorizacoes_verbas");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,"id_app=".$_APP_ID."&re_cripto=".$_SESSION['RE_CRIPTO']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$_RETORNO = curl_exec($ch);
		curl_close ($ch);

		#print_r($_RETORNO); echo "<HR>";exit;
		
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
				
				if(count($_RETORNO['result']['autorizacoes_verbas']) == 0)
				{
					$tabela ='<h3 style="color:red; text-align:center">ESTA OPÇÃO É APENAS PARA ASSISITDOS</h3>';
				}
				else
				{
					$tabela = '
						<center>
							<h1 style="font-family: Calibri, Arial; font-size: 15pt;">
								Esta opção é para realizar a autorização para o desconto em folha de pagamento (SIM) ou para desautorizar o desconto em folha de pagamento (NÃO).
								<br/><br/>
								Informamos que as autorizações/desautorizações cadastradas entre os dias 1º a 15 do mês, o desconto/cancelamento ocorrerá dentro do próprio mês e as autorizações/desautorizações cadastradas entre os dias 16 a 30/31, o desconto/cancelamento dar-se-á no mês subsequente.
							</h1>

					<form method="post" action="auto_atendimento_desconto_entidades_grava.php">';	

					foreach ($_RETORNO['result']['autorizacoes_verbas'] as $key => $item) 
					{
						$tbody = '';

						foreach ($item['verbas'] as $key2 => $verba) 
						{
							$disabled = '';

							if(trim($item['fl_desautorizar']) == 'S' AND trim($verba['fl_opcao']) == 'S')
							{
								$disabled = 'disabled';
							}

							$alteracao = '<td></td>';
							if(trim($verba['dt_alteracao']) != '')
							{
								$alteracao = '<td style="font-size:9pt;">Última Alteração Realizada em '.$verba['dt_alteracao'].'</td>';
							}

							$tbody.= '
								<tr>
									<td width="40%" style="font-size:10pt;">
										<b style="color:black;" id="'.$verba['cod_recolhimento'].'_'.$verba['cd_verba'].'_lbl">'.utf8_decode($verba['ds_verba']).'</b>
									</td>
									<td>
										<input type="hidden" id="arr[]" name="arr[]" value="'.$verba['cod_recolhimento'].'_'.$verba['cd_verba'].'" />
										<input type="hidden" id="'.$verba['cd_verba'].'_verba" name="'.$verba['cd_verba'].'_verba" value="'.$verba['ds_verba'].'"/>
										<input type="hidden" id="'.$verba['cod_recolhimento'].'_'.$verba['cd_verba'].'_old" name="'.$verba['cod_recolhimento'].'_'.$verba['cd_verba'].'_old" value="'.$verba['fl_opcao'].'" />
										<input type="hidden" id="'.$verba['cod_recolhimento'].'_'.$verba['cd_verba'].'_alt" name="'.$verba['cod_recolhimento'].'_'.$verba['cd_verba'].'_alt" value="'.(trim($verba['fl_opcao'])).'" />
										<select id="'.$verba['cod_recolhimento'].'_'.$verba['cd_verba'].'" name="'.$verba['cod_recolhimento'].'_'.$verba['cd_verba'].'" '.$disabled.' onchange="altera_opcao('.$verba['cod_recolhimento'].','.$verba['cd_verba'].')">
											<option value="X" '.(trim($verba['fl_opcao']) == 'X' ? "selected" : "").'></option>
											<option value="N" '.(trim($verba['fl_opcao']) == 'N' ? "selected" : "").'>Não</option>
											<option value="S" '.(trim($verba['fl_opcao']) == 'S' ? "selected" : "").'>Sim</option>
										</select>
									</td>
									'.$alteracao.'
								</tr>';
						}

						$tabela .= '
							<table width="700" class="sort-table" align="center" cellspacing="2" cellpadding="2">
								<thead>
									<tr>
										<td colspan="3" style="background: rgb(50, 124, 170); color: white;">
											<input type="hidden" id="'.$verba['cod_recolhimento'].'_recolhimento" name="'.$verba['cod_recolhimento'].'_recolhimento" value="'.$item['ds_entidade'].'"/>
											<b>'.utf8_decode($item['ds_entidade']).'</b>
										</td>
									</tr>
								</thead>
								<tbody>	
									<tr>'.$tbody.'</tr>					
								</tbody>								
							</table>
							<br/>';	
					}

					$tabela .= '
						<div style="text-align:center;" class="nao_imprimir">
							<input type="button" value="Confirmar Alterações" class="botao" onclick="confirma_alteracores(form)">
						</div>

						</form>';

				}
			
				$conteudo = str_replace('{TABELA}', $tabela, $conteudo);
			}
			else
			{
				$conteudo = str_replace('{TABELA}', '', $conteudo);
			}
		}
		else
		{
			$conteudo = str_replace('{TABELA}', json_last_error(), $conteudo);
		}

	}
	else
	{
		$conteudo = str_replace('{TABELA}', '', $conteudo);
	}	

	$tpl->assign('conteudo',$conteudo);

	$tpl->printToScreen();
?>