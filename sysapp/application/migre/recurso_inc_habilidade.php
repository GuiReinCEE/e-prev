<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   if (isset($_REQUEST['r'])) {
      $r = $_REQUEST['r'];
   }
   else {
      $r = $_POST['r'];
   }
   if (isset($_REQUEST['h']) and ($_REQUEST['h']<>'')) {
      $h = $_REQUEST['h'];
	  $sql = "select grau_conhecimento from projetos.habilidades_recursos where cod_recurso='$r' and cod_habilidade=$h";
//	  echo $sql;
	  $rs = pg_exec($db, $sql);
	  $reg = pg_fetch_array($rs);
	  $g = $reg['grau_conhecimento'];
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>Documento sem t&iacute;tulo</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script language="JavaScript">
  <!--
     function valida_form(f) {
	    if ((f.habilidade.value=="") || (f.grau.value=="")) {
		   alert("Você deve escolher uma 'Habilidade' e um 'Grau de conhecimento'");
		   return false;
		}
		else {
		   return true;
		}
	 }
  -->
  </script>
  <script language="JavaScript">
  <!--
     function atualiza_pai() {
	    opener.location.reload(true);
	 }
  -->
  </script>
</head>
<body bgcolor="#B8DEC7" onLoad="atualiza_pai();">
<?
   if (isset($_POST['habilidade'])) {
      $h = $_POST['h'];
	  $grau = $_POST['grau'];
      $sql = "select * from projetos.habilidades_recursos where cod_recurso='$r' and cod_habilidade=$habilidade";
      echo $sql;
	  $rs = pg_exec($db, $sql);
	  if (pg_numrows($rs) > 0) {
	     $sql = "update projetos.habilidades_recursos set grau_conhecimento=$grau where cod_recurso='$r' and cod_habilidade=$habilidade";
	  }
	  else {
	     $sql = "insert into projetos.habilidades_recursos(cod_recurso, cod_habilidade, grau_conhecimento) values('$r', $habilidade, $grau)";
//		 echo $sql;
      }
	  $rs = pg_exec($db, $sql);
   }	     
?>
<form name="frmHabRec" method="post" action="recurso_inc_habilidade.php" onSubmit="return valida_form(this);">
  <?
     echo "<input type='hidden' name='r' value='$r'>"
  ?>
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="52"><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">Habilidade</font></strong></td>
      <td width="108"><strong><font size="1" face="Verdana, Arial, Helvetica, sans-serif">Grau</font></strong></td>
    </tr>
    <tr> 
      <td> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
        <select name="habilidade" size="10" id="habilidade">
          <?
		   $sql="select codigo, descricao from projetos.habilidades order by descricao";
		   $rs = pg_exec($db, $sql);
		   while ($reg = pg_fetch_row($rs)) {
		      if ($h == $reg[0]) {
                 echo "<option value='".$reg[0]."' selected>".$reg[1]."</option>\r";
			  }
			  else {
                 echo "<option value='".$reg[0]."'>".$reg[1]."</option>\r";
		      }
		   }
		?>
        </select>
        </font></td>
      <td> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="radio" name="grau" value="5" <? echo ($g==5 ? " checked" : ""); ?>>
        S&ecirc;nior<br>
        <input type="radio" name="grau" value="4" <? echo ($g==4 ? " checked" : ""); ?>>
        Pleno <br>
        <input type="radio" name="grau" value="3" <? echo ($g==3 ? " checked" : ""); ?>>
        Junior<br>
        <input type="radio" name="grau" value="2" <? echo ($g==2 ? " checked" : ""); ?>>
        Trainee/Junior<br>
        <input type="radio" name="grau" value="1" <? echo ($g==1 ? " checked" : ""); ?>>
        Trainee </font></td>
    </tr>
    <tr> 
      <td colspan="2"> <div align="center"> 
          <input type="submit" name="Submit" value="Gravar">
          <input name="fechar" type="button" id="fechar" value="Fechar" onClick="self.close();">
        </div></td>
    </tr>
  </table>
</form>
<?
   pg_close($db);
?>
</body>
</html>