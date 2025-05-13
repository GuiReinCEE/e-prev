<?php
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	$tpl = new TemplatePower('tpl/tpl_cad_acompanhamento.html');
	$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
    
	$tpl->newBlock('cadastro');
	$tpl->assign('cod_nao_conf', $_REQUEST['cod_nao_conf']);
	
	if (isset($c))	
	{
		############################## BUSCA ACOMPANHAMENTO ############################
		$sql = "SELECT a.cd_acompanhamento, 
		               a.auditor,
					   (SELECT nome
					      FROM projetos.usuarios_controledi
						 WHERE codigo = a.auditor
					   ) AS nome_auditor,
					   TO_CHAR(data,'DD/MM/YYYY') AS data,   
					   a.situacao
		          FROM projetos.acompanhamento a
				 WHERE a.cd_acompanhamento = ".$c;
        $rs  = pg_query($db, $sql);
        $reg = pg_fetch_array($rs);
		$tpl->assign('data',$reg['data']);
		$tpl->assign('situacao',$reg['situacao']);
		$tpl->assign('auditor',$reg['auditor']);
		$tpl->assign('nome_auditor',$reg['nome_auditor']);
		$cd_acomp = $reg['cd_acompanhamento'];
		$i = 'A';
	}
	else
	{
		############################## DADOS PARA INSERIR ############################
		$tpl->assign('data', date('d/m/Y'));
		$tpl->assign('auditor',$_SESSION['Z']);
		
		$qr_select = "SELECT nome
			            FROM projetos.usuarios_controledi
				       WHERE codigo = ".$_SESSION['Z'];
		$ob_resul  = pg_query($db, $qr_select);
		$ob_reg    = pg_fetch_object($ob_resul);
		$tpl->assign('nome_auditor',$ob_reg->nome);		
	}
	
	$tpl->newBlock('codigo');
	
	if(trim($_POST['cod_nao_conf']) != "")
	{
		$tpl->assign('cod_nao_conf', $_POST['cod_nao_conf']);
	}
	else
	{
		$tpl->assign('cod_nao_conf', $_REQUEST['cod_nao_conf']);
	}	

	$tpl->assign('cod_processo', $_POST['cod_processo']);
	$tpl->assign('cod_acompanhamento', $cd_acomp);
	$tpl->assign('insere', $i);

	pg_close($db);
	$tpl->printToScreen();	
?>