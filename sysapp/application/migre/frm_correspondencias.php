<?php
	include_once("inc/sessao.php");
	include_once("inc/conexao.php");
	include_once("inc/class.TemplatePower.inc.php");
	$tpl = new TemplatePower("tpl/tpl_frm_correspondencias.html");
	$tpl->prepare();
	
	header( 'location:'.base_url().'index.php/cadastro/sg_correspondencia');

	if($erro=="permissao")
	{
		$tpl->assign('erro', "Somente usuários da SG têm permissão para alterar correspondências.");
	}

   	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
   	$tpl->assign('divsao', $D);

	if ( isset($_REQUEST['n']) and ($_REQUEST['n'] <> "") )
	{
		$tthis->load->model('projetos/Correspondencias_model');
		$reg=$tthis->Correspondencias_model->carregar(intval($_REQUEST['a']),intval($_REQUEST['n']));
	  	if (sizeof($reg)>0)
	  	{
		     $tpl->assign('operacao', 'A');
	         $tpl->assign('numero', $reg['numero']);
	         $tpl->assign('ano', $reg['ano']);
	         $tpl->assign('solicitante_emp', $reg['solicitante_emp']);
	         $tpl->assign('solicitante_re', $reg['solicitante_re']);
	         $tpl->assign('solicitante_seq', $reg['solicitante_seq']);
	         $tpl->assign('solicitante_nome', $reg['solicitante_nome']);
	         $tpl->assign('assinatura_emp', $reg['assinatura_emp']);
	         $tpl->assign('assinatura_re', $reg['assinatura_re']);
	         $tpl->assign('assinatura_seq', $reg['assinatura_seq']);
	         $tpl->assign('assinatura_nome', $reg['assinatura_nome']);
	         $tpl->assign('destinatario_nome', $reg['destinatario_nome']);
	         $tpl->assign('assunto', $reg['assunto']);
	         $tpl->assign('data', $reg['data']);
	         $divisao = $reg['divisao'];
		}
	}
	else
	{
		$tpl->assign('operacao', 'C');
		$tpl->newBlock("blk_primeira_execussao");
		$tpl->assign('primeira_execussao', "   <script language='JavaScript'>consulta();</script>   ");
   	}

   $sql = "SELECT codigo, nome FROM projetos.divisoes ORDER BY nome";
   $rs = pg_query($db, $sql);
   while ($reg = pg_fetch_array($rs))
   {
      $tpl->newBlock("blk_divisao");
      $tpl->assign('cod_divisao', $reg['codigo']);
      $tpl->assign('nome_divisao', $reg['nome']);
      if ($reg['codigo'] == $divisao)
      {
         $tpl->assign('sel_divisao', ' selected');
      }
   }

   $tpl->printToScreen();
?>