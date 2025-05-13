<?php
   include_once("inc/sessao.php");
   include_once("inc/conexao.php");
   include_once("inc/class.TemplatePower.inc.php");
   
    header( 'location:'.base_url().'index.php/cadastro/sg_documento_recebido');
   
   $tpl = new TemplatePower("tpl/tpl_frm_docs_recebidos.html");
   $tpl->prepare();

   $PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
$tpl->assign('usuario', $N);
   $tpl->assign('divsao', $D);
   if ( isset($_REQUEST['n']) and ($_REQUEST['n'] <> "") ) {
      $sql  = " select doc.numero,  ";
	  $sql .= "        doc.ano, ";
	  $sql .= "        to_char(doc.datahora, 'DD/MM/YYYY HH24:MI') as datahora, ";
	  $sql .= "        remetente, ";
	  $sql .= "        doc.assunto, ";
	  $sql .= "        doc.destino_emp, ";
	  $sql .= "        doc.destino_re, ";
	  $sql .= "        doc.destino_seq, ";
	  $sql .= "        part.nome ";
	  $sql .= " from   projetos.docs_recebidos doc, ";
	  $sql .= "        participantes part ";
	  $sql .= " where  part.cd_empresa            = doc.destino_emp ";
	  $sql .= "   and  part.cd_registro_empregado = doc.destino_re ";
	  $sql .= "   and  part.seq_dependencia       = doc.destino_seq ";
	  $sql .= "   and  doc.ano=".$_REQUEST['a'];
	  $sql .= "   and  doc.numero=".$_REQUEST['n'];
	  $rs = pg_exec($db, $sql);
	  if ($reg = pg_fetch_array($rs)) {
	     $tpl->assign('operacao', 'A');
         $tpl->assign('numero', $reg['numero']);
         $tpl->assign('ano', $reg['ano']);
         $tpl->assign('datahora', $reg['datahora']);
         $tpl->assign('remetente', $reg['remetente']);
         $tpl->assign('assunto', $reg['assunto']);
         $tpl->assign('destino_emp', $reg['destino_emp']);
         $tpl->assign('destino_re', $reg['destino_re']);
         $tpl->assign('destino_seq', $reg['destino_seq']);
         $tpl->assign('destino_nome', $reg['nome']);
         $divisao = $reg['divisao'];
      }
   }
   else {
      $tpl->assign('operacao', 'C');
	  $tpl->newBlock("blk_primeira_execussao");
	  $tpl->assign('primeira_execussao', "   <script language='JavaScript'>consulta();</script>   ");
   }
   $sql = "select codigo, nome from projetos.divisoes order by nome";
   $rs = pg_exec($db, $sql);
   while ($reg = pg_fetch_array($rs)) {
      $tpl->newBlock("blk_divisao");
      $tpl->assign('cod_divisao', $reg['codigo']);
      $tpl->assign('nome_divisao', $reg['nome']);
      if ($reg['codigo'] == $divisao) {
         $tpl->assign('sel_divisao', ' selected');
      }
   }
   
   $tpl->assignGlobal('dia', date('d'));
   $tpl->assignGlobal('mes', date('m'));
   $tpl->assignGlobal('ano', date('Y'));

   $tpl->printToScreen();
   
?>