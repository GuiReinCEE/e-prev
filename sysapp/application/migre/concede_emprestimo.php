<?php
	echo "DESATIVADO";
	EXIT;

   include_once('class.SocketAbstraction.inc.php');
   include_once('class.TemplatePower.inc.php');
   
   $LISTNER_IP    = '10.63.255.113';
//   $LISTNER_IP = '10.63.255.16';
   $LISTNER_PORTA = '3355';
   
   $tpl = new TemplatePower('tpl_efetiva_concessao_dap.html');
   $tpl->prepare();

/*
   $sessao         = $_POST['session_id'];
   $num_prestacoes = $_POST['num_prestacoes'];
*/
   $sessao         = $_GET['session_id'];
   $num_prestacoes = $_GET['num_prestacoes'];
   
   $cn = new Socket();
   $cn->SetRemoteHost($LISTNER_IP);
   $cn->SetRemotePort($LISTNER_PORTA);
   $cn->SetBufferLength(131072);
   $cn->SetConnectTimeOut(1);
   if ($cn->Connect()) {
      $ret = $cn->Ask("fnc_busca_inf_concessao;$sessao;$num_prestacoes");
      if ($cn->Error()) {
         echo "Ocorreu um erro de conexão com o webservice";
      } else {
         // Coloca os dados na tela
         $dom = domxml_xmltree('<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?>'.$ret);
         $campos = $dom->get_elements_by_tagname("fld");
         for ($i=0;$i<count($campos);$i++) {
            if ($campos[$i]->get_attribute('tp') == 'DAT') {
               $tpl->assign(strtolower($campos[$i]->get_attribute('id')), $campos[$i]->get_content());
            }
         }
			$tpl->assign('session_id', $sessao);
      }
   } else {
      echo "Ocorreu um erro de conexão com o webservice";
   }
   $tpl->printToScreen();   
?>
