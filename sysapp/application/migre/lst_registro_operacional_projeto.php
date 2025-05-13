<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	header( 'location:'.base_url().'index.php/atividade/registro_operacional' );


	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_lst_registro_operacional_projeto.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	
	#### LISTA REGISTRO OPERACIONAL ####
	$qr_select = "
                    SELECT ap.cd_acomp,
                           p.nome AS ds_projeto,
						   ap.cd_acompanhamento_registro_operacional AS cd_operacional,
						   ap.ds_nome AS ds_registro,
						   TO_CHAR(ap.dt_finalizado,'DD/MM/YYYY') AS dt_finalizado
				      FROM projetos.acompanhamento_registro_operacional ap,
						   projetos.usuarios_controledi uc,
						   projetos.acompanhamento_projetos app,
						   projetos.projetos p
				     WHERE ap.cd_usuario  = uc.codigo
				       AND ap.dt_exclusao IS NULL
					   AND ap.cd_acomp    = app.cd_acomp
					   AND app.cd_projeto = p.codigo
					   AND ap.cd_usuario  = ".$_SESSION['Z']."
				     ORDER BY p.nome,
                              ap.ds_nome
				 ";
	$ob_result = pg_query($db, $qr_select);	
	$cd_acomp_atual = 0;
	$nr_conta  = 0;
	while ($ar_reg = pg_fetch_array($ob_result)) 
	{
		if(($nr_conta % 2) != 0)
		{
			$bg_color = '#F4F4F4';
		}
		else
		{
			$bg_color = '#FFFFFF';		
		}
		
		
		if($cd_acomp_atual != $ar_reg['cd_acomp'])
		{
			
			$tpl->newBlock('lst_projeto');
			$tpl->assign('ds_projeto', $ar_reg['ds_projeto']);	
			$cd_acomp_atual = $ar_reg['cd_acomp'];
		}

		$tpl->newBlock('lst_registro');
		$tpl->assign('bg_color',       $bg_color);
		$tpl->assign('ds_registro',    $ar_reg['ds_registro']);	
		$tpl->assign('dt_finalizado',  $ar_reg['dt_finalizado']);		
		$tpl->assign('cd_acomp',       $ar_reg['cd_acomp']);
		$tpl->assign('cd_operacional', $ar_reg['cd_operacional']);
		$nr_conta++;
	}
	
	$tpl->printToScreen();
	pg_close($db);
	
?>