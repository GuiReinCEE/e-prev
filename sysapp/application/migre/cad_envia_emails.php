<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   
   header( 'location:'.base_url().'index.php/ecrm/reenvio_email/index/'.$_REQUEST['c']);
   
   EXIT;   
   
   include_once('inc/class.TemplatePower.inc.php');
   
	$tpl = new TemplatePower('tpl/tpl_cad_emails.html');
	$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
   
	$tpl->prepare();
	$tpl->assign('n', $n);
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$tpl->newBlock('cadastro');
	if (isset($c))	{
		$sql =        " select cd_email, de, para, cc, cco, assunto, texto, cd_empresa, cd_registro_empregado, seq_dependencia, ";
		$sql = $sql . "        to_char(dt_envio, 'DD/MM/YYYY') as dt_envio, div_solicitante, ";
		$sql = $sql . "        to_char(dt_email_enviado, 'DD/MM/YYYY') as dt_email_enviado, cd_evento ";
		$sql = $sql . " from   projetos.envia_emails ";
		$sql = $sql . " where  cd_email   = $c ";
//		echo $sql;
		$rs = pg_exec($db, $sql);
		$reg=pg_fetch_array($rs);
		$tpl->assign('e', $e);	
		$tpl->assign('cd_email', $reg['cd_email']);
		$tpl->assign('de', $reg['de']);
		$tpl->assign('para', $reg['para']);
		$tpl->assign('cc', $reg['cc']);
		$tpl->assign('cco', $reg['cco']);
		$tpl->assign('conteudo', $reg['texto']);
		$tpl->assign('assunto', $reg['assunto']);
		$tpl->assign('dt_envio', $reg['dt_envio']);
		$tpl->assign('dt_email_enviado', $reg['dt_email_enviado']);
		$tpl->assign('empresa', $reg['cd_empresa']);
		$tpl->assign('red', $reg['cd_registro_empregado']);
		$tpl->assign('seq', $reg['seq_dependencia']);
		$tpl->assign('cd_evento', $reg['cd_evento']);
		$tpl->assign('gerencia', $reg['div_solicitante']);
	}
    $n = 'U';
	$tpl->assign('insere', $n);
	
	#### DETALHES DO RETORNO DO EMAIL ####
	$qr_select = "
					SELECT TO_CHAR(dt_email, 'DD/MM/YYYY HH24:MI') AS dt_retorno,
					       ds_msg
					  FROM projetos.log_email
					 WHERE nr_msg = '".$c."'
	             ";
	$ob_resul = pg_query($db, $qr_select);
	$fl_mostra = true;
	$nr_conta = 0;
	while ($ar_reg = pg_fetch_array($ob_resul)) 
	{
		if(($nr_conta % 2) != 0)
		{
			$bg_color = '#F4F4F4';
		}
		else
		{
			$bg_color = '#FFFFFF';		
		}
		
		if($fl_mostra)
		{
			$tpl->newBlock('det_email_retornado');
		}
		
		$tpl->newBlock('lst_email_retornado');
		$tpl->assign('bg_color', $bg_color);
		$tpl->assign('dt_retorno', $ar_reg['dt_retorno']);
		$tpl->assign('ds_msg', nl2br($ar_reg['ds_msg']));
		
		$nr_conta++;
	}				 
	
	
	
// ----------------------------------------------------------
	pg_close($db);
	$tpl->printToScreen();
// ----------------------------------------------------------
	function convdata_br_iso($dt) {
		// Pressupѕe que a data esteja no formato DD/MM/AAAA
		// A melhor forma de gravar datas no PostgreSQL щ utilizando 
		// uma string no formato DDDD-MM-AA. Esta funчуo justamente 
		// adequa a data a este formato
		$d = substr($dt, 0, 2);
		$m = substr($dt, 3, 2);
		$a = substr($dt, 6, 4);
		return $a.'-'.$m.'-'.$d;
	}
?>