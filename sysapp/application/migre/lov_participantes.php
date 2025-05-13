<?php
   include_once("inc/sessao.php");
   include_once("inc/conexao.php");
   include_once("inc/class.TemplatePower.inc.php");
   $tpl = new TemplatePower("tpl/tpl_lov_participantes.html");
   $tpl->prepare();
   
   $nome = $_REQUEST['nome'];

  $tpl->assign('ownerEmp', $_REQUEST['e']);
  $tpl->assign('ownerRe', $_REQUEST['r']);
  $tpl->assign('ownerSeq', $_REQUEST['s']);
  $tpl->assign('ownerNome', $_REQUEST['n']);
  $tpl->assign('nome', $_REQUEST['nome']);
  $tpl->assign('t', $_REQUEST['t']);
  
	if($nome!='')
	{
      $sql  = " select cd_empresa, cd_registro_empregado, seq_dependencia, nome ";
      $sql .= " from   participantes ";
      $sql .= " where  seq_dependencia = 0 ";
	  if ($_REQUEST['t'] != 'A') { // Assinatura
         $sql .= "   and cd_empresa = 9  ";
	  }
	  if (is_numeric(trim($nome))) {
         $sql .= "   and  cd_registro_empregado = $nome ";
	  } 
	  else {
         $sql .= "   and  nome like upper('$nome%') ";
	  }
	  $sql .= " order by nome ";

      $rs = pg_exec($db, $sql);
	  if (pg_num_rows($rs) < 1)
	  {
	     $tpl->newBlock("blk_mensagem");
		 $tpl->assign("mensagem", "Nenhum participante encontrado.");
      }
	  elseif (pg_num_rows($rs) > 200)
	  { 
	     $tpl->newBlock("blk_mensagem");
		 $tpl->assign("mensagem", "Sua pesquisa encontrou mais de 200 resultados. <br><br>Seja mais específico, informando uma parte maior do nome.");
	  }
	  else
	  {
         while ($reg=pg_fetch_array($rs))
         {
            $tpl->newBlock("blk_linha");
            $tpl->assign('emp', $reg['cd_empresa']);
            $tpl->assign('re', $reg['cd_registro_empregado']);
            $tpl->assign('seq', $reg['seq_dependencia']);
            $tpl->assign('nome', str_replace("'", "\'", $reg['nome']));
         }
	  }
   }

   //}
   $tpl->printToScreen();
   pg_close($db);
?>