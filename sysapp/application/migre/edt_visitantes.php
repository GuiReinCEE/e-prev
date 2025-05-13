<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_edt_visitantes.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	
	$tpl->assign('cd_visitante', $_REQUEST['cd_visitante']);
	
	if(trim($_REQUEST['cd_visitante']) == "")
	{
		echo "<script> window.close(); </script>";
	}
	else 
	{
		$qr_select = "
					SELECT cd_visitante,
					       nr_cracha,
						   cd_tipo_visita,
						   UPPER(ds_origem) AS ds_origem,
						   UPPER(ds_nome) AS ds_nome,
						   UPPER(ds_destino) AS ds_destino,
						   (CASE WHEN cd_registro_empregado IS NULL
						         THEN 'S'
							 	 ELSE 'N'
						   END) AS fl_edita,
						   TO_CHAR(dt_entrada,'DD/MM/YYYY') AS dt_entrada,
						   TO_CHAR(dt_entrada,'HH24:MI') AS hr_entrada,
						   TO_CHAR(dt_saida,'DD/MM/YYYY') AS dt_saida,
						   TO_CHAR(dt_saida,'HH24:MI') AS hr_saida,
						   (CASE WHEN dt_saida IS NULL
						         THEN 'N'
							 	 ELSE 'S'
						   END) AS fl_saida                         						   
					  FROM projetos.visitantes
					 WHERE cd_visitante = ".$_REQUEST['cd_visitante']."
				     ";
		$ob_result = pg_query($db, $qr_select);	
		$ar_reg = pg_fetch_array($ob_result);
		
		$tpl->assign('nr_cracha',  $ar_reg['nr_cracha']);
		$tpl->assign('ds_nome',    $ar_reg['ds_nome']);
		$tpl->assign('ds_origem',  $ar_reg['ds_origem']);
		$tpl->assign('cd_tipo',    $ar_reg['cd_tipo_visita']);
		$tpl->assign('ds_destino', $ar_reg['ds_destino']);
		
		$tpl->assign('dt_entrada', $ar_reg['dt_entrada']);
		$tpl->assign('hr_entrada', $ar_reg['hr_entrada']);
		$tpl->assign('dt_saida',   $ar_reg['dt_saida']);
		$tpl->assign('hr_saida',   $ar_reg['hr_saida']);		
		
		#### DEFINE PERMISSAO PARA LIMPAR DATA DE SAIDA ####
		$tpl->assign('fl_saida', $ar_reg['fl_saida']);

		
		#### DESABILITA EDITAR NOME DE PARTICIPANTE ####
		if($ar_reg['fl_edita'] == "N")
		{
			$tpl->assign('fl_nome', 'readonly');
		}
	}
	
	if(trim($_REQUEST['fl_gravado']) == "OK")
	{
		echo "<script> 
						//try { opener.buscaMovimento(); } catch(e){ }
						window.close();
			  </script>";
	}
	
	$tpl->printToScreen();
	pg_close($db);
?>