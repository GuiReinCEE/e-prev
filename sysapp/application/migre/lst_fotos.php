<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	header( 'location:'.base_url().'index.php/ecrm/multimidia/foto' );
	
	include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_lst_fotos.html');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);

   	if (($D <> 'GRI') and ($D <> 'GI')) 
	{
   		header('location: acesso_restrito.php?IMG=banner_filmes');
	}

	$tpl->newBlock('lista');
    $sql = " 
	         SELECT cd_fotos,
	                ds_titulo, 
	                TO_CHAR(dt_data, 'DD/MM/YYYY') AS dt_data_evento,
	                TO_CHAR(dt_cadastro, 'DD/MM/YYYY') AS dt_cadastro
	           FROM acs.fotos
	          ORDER BY dt_data DESC,
                       ds_titulo ASC
		    ";
	$ob_resul = pg_query($db, $sql);
	$cont = 0;
	while($ar_reg = pg_fetch_array($ob_resul)) 
	{
		$tpl->newBlock('lst_fotos');
		$cont = $cont + 1;
		if (($cont % 2) <> 0) 
		{
			$tpl->assign('cor_fundo', $v_cor_fundo1);
		}
		else 
		{
			$tpl->assign('cor_fundo', $v_cor_fundo2);
		}
		$tpl->assign('cd_fotos',    $ar_reg['cd_fotos']);
		$tpl->assign('ds_titulo',   $ar_reg['ds_titulo']);
		$tpl->assign('dt_data',     $ar_reg['dt_data_evento']);
		$tpl->assign('dt_cadastro', $ar_reg['dt_cadastro']);
  	}
	
	pg_close($db);
	$tpl->printToScreen();	
?>