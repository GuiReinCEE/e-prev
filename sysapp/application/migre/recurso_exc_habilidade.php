<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Documento sem t&iacute;tulo</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
  <br>
  <div align="center">
  <font size="1" face="Verdana, Arial, Helvetica, sans-serif">
  <strong>Excluindo Habilidade...</strong>
  </font> 
  </div>
  <?
     $r = $_REQUEST['r'];
     $h = $_REQUEST['h'];
     $sql = "delete from projetos.habilidades_recursos where cod_recurso=$r and cod_habilidade=$h";
	 $rs = pg_exec($db, $sql);
	 pg_close($db);     
  ?>
</body>
</html>
