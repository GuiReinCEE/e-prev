<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	require_once('inc/ajaxobject.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$tpl = new TemplatePower('tpl/tpl_exame_ingresso.html');
	$tpl->prepare();
	
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');

	$tpl->assign('n', $n);
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	#### PERMISSOES ####
	$fl_editar = false;
	if(($_SESSION['D'] == "GAP") or ($_SESSION['D'] == "GI"))
	{
		$fl_editar = true;
	}

	#### FILTROS #####
	$tpl->assign('cd_empresa', $_REQUEST['cd_empresa']);
	$tpl->assign('cd_registro_empregado', $_REQUEST['cd_registro_empregado']);	
	$tpl->assign('seq_dependencia', $_REQUEST['seq_dependencia']);	

	$_REQUEST['dt_envio'] = "";
	if(($_REQUEST['dt_envio_ini'] != "") AND ($_REQUEST['dt_envio_fim'] != ""))
	{
		$_REQUEST['dt_envio'] = "TO_DATE('".$_REQUEST['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_envio_fim']."', 'DD/MM/YYYY')";
	}
	$tpl->assign('dt_envio_ini', $_REQUEST['dt_envio_ini']);
	$tpl->assign('dt_envio_fim', $_REQUEST['dt_envio_fim']);

	/*$_REQUEST['dt_retorno'] = "";
	if(($_REQUEST['dt_retorno_ini'] != "") AND ($_REQUEST['dt_retorno_fim'] != ""))
	{
		$_REQUEST['dt_retorno'] = "TO_DATE('".$_REQUEST['dt_retorno_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$_REQUEST['dt_retorno_fim']."', 'DD/MM/YYYY')";
	}
	$tpl->assign('dt_retorno_ini', $_REQUEST['dt_retorno_ini']);
	$tpl->assign('dt_retorno_fim', $_REQUEST['dt_retorno_fim']);*/

	switch($_REQUEST['fl_apto'])
	{
		case 'S' : $tpl->assign('fl_apto_sim', 'selected'); break;
		case 'N' : $tpl->assign('fl_apto_nao', 'selected'); break;
		default  : $tpl->assign('fl_apto', 'selected');
	}

	#### BUSCA LISTA ####
	$qr_sql = " 
				SELECT ei.cd_exame_ingresso, 
				       ei.cd_empresa, 
					   ei.cd_registro_empregado, 
					   ei.seq_dependencia, 
                       ei.ds_nome, 
					   TO_CHAR(ei.dt_envio, 'DD/MM/YYYY HH24:MI') AS dt_enviado,
					   TO_CHAR(ei.dt_retorno, 'DD/MM/YYYY HH24:MI') AS dt_retorno,
					   CASE WHEN COALESCE(ei.fl_apto,'') = 'S' THEN 'SIM'
					        WHEN COALESCE(ei.fl_apto,'') = 'N' THEN 'NÃO'
							ELSE ''
					   END AS fl_apto,
					   ei.ds_motivo,
					   (SELECT COUNT(*) 
					      FROM projetos.exame_ingresso_contato eic
						 WHERE eic.cd_exame_ingresso = ei.cd_exame_ingresso) AS qt_contato
                  FROM projetos.exame_ingresso ei
				 WHERE 1 = 1
				 AND dt_retorno IS NULL
				".($_REQUEST['cd_empresa']            == "" ? "" : "AND cd_empresa = ".$_REQUEST['cd_empresa'])."
				".($_REQUEST['cd_registro_empregado'] == "" ? "" : "AND cd_registro_empregado = ".$_REQUEST['cd_registro_empregado'])."
				".($_REQUEST['seq_dependencia']       == "" ? "" : "AND seq_dependencia = ".$_REQUEST['seq_dependencia'])."
				".($_REQUEST['ds_nome']               == "" ? "" : "AND UPPER(funcoes.remove_acento(ds_nome)) LIKE UPPER(funcoes.remove_acento('%".$_REQUEST['ds_nome']."%'))")."
				".($_REQUEST['dt_envio']              == "" ? "" : "AND DATE_TRUNC('day',dt_envio) BETWEEN ".$_REQUEST['dt_envio'])."
				".($_REQUEST['fl_apto']               == "" ? "" : "AND fl_apto = '".$_REQUEST['fl_apto']."'")."
				 ORDER BY ei.dt_envio DESC
	           ";
	$ob_resul = pg_query($db, $qr_sql);
	$tpl->assign('qt_inscrito', pg_num_rows($ob_resul));
	while ($ar_reg=pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('lista');
		$tpl->assign('cd_exame_ingresso',       $ar_reg['cd_exame_ingresso']);
		$tpl->assign('cd_empresa',            $ar_reg['cd_empresa']);
		$tpl->assign('cd_registro_empregado', $ar_reg['cd_registro_empregado']);
		$tpl->assign('seq_dependencia',       $ar_reg['seq_dependencia']);
		$tpl->assign('ds_nome',               $ar_reg['ds_nome']);

		if($fl_editar)
		{
			$tpl->assign('qt_contato',  '
			Total: '.$ar_reg['qt_contato'].'
			<BR>
			<input type="submit" value="Contatos" class="botao">
			');
		}
		else
		{
			$tpl->assign('dt_enviado',  $ar_reg['dt_enviado']);
		}

		if(($fl_editar) and ($ar_reg['dt_enviado'] == ""))
		{
			$tpl->assign('dt_enviado',  '<input type="button" value="Enviar" onclick="envia('.$ar_reg['cd_exame_ingresso'].');" class="botao">');
		}
		else
		{
			$tpl->assign('dt_enviado',  $ar_reg['dt_enviado']);
		}

		if(($fl_editar)and ($ar_reg['dt_enviado'] != "") and ($ar_reg['dt_retorno'] == ""))
		{
			$tpl->assign('dt_retorno',  '<input type="button" value="Retorno" onclick="retorno('.$ar_reg['cd_exame_ingresso'].');" class="botao">');
			$tpl->assign('fl_apto',    '<select id="fl_apto_'.$ar_reg['cd_exame_ingresso'].'" name="fl_apto_'.$ar_reg['cd_exame_ingresso'].'">
											<option value="">Selecione</option>
											<option value="S">SIM</option>
											<option value="N">NÃO</option>
										</select>');
			$tpl->assign('ds_motivo',  '<textarea id="ds_motivo_'.$ar_reg['cd_exame_ingresso'].'" name="ds_motivo_'.$ar_reg['cd_exame_ingresso'].'"></textarea>');
		}
		else
		{
			$tpl->assign('dt_retorno', $ar_reg['dt_retorno']);
			$tpl->assign('fl_apto',    $ar_reg['fl_apto']);
			$tpl->assign('ds_motivo',  $ar_reg['ds_motivo']);
		}

		$nr_conta++;
	}	

	$tpl->printToScreen();
	pg_close($db);
?>