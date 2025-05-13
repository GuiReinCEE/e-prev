<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   include_once('inc/class.TemplatePower.inc.php');
   include_once('inc/class.SocketAbstraction.inc.php');
   
	$cn = new Socket();
	$cn->SetRemoteHost($LISTNER_IP);
	$cn->SetRemotePort($LISTNER_PORTA);
	header("location: simulacao_dap.php?e=$EMP&r=$RE&s=$SEQ");
?>