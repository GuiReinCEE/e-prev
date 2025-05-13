<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	header('location:'.base_url().'index.php/ecrm/chave');

	include_once('inc/class.TemplatePower.inc.php');
	
    $tpl = new TemplatePower('tpl/tpl_lst_chaves.html');
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$qr_select = "
					SELECT c.cd_chave,
						   c.ds_chave,
						   c.cd_sala
					  FROM projetos.chaves c
				     ORDER BY c.cd_sala,
                              c.ds_chave
				 ";
	$ob_result = pg_query($db, $qr_select);	
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
		$nr_conta++;
		
		$tpl->newBlock('lst_chave');		
		$tpl->assign('bg_color',              $bg_color);
		$tpl->assign('cd_chave',  $ar_reg['cd_chave']);
		$tpl->assign('ds_chave',  $ar_reg['cd_sala']." - ".$ar_reg['ds_chave']);
	}
	
	$tpl->printToScreen();
	pg_close($db);
?>