<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   if (isset($_REQUEST['r'])) {
      $r = $_REQUEST['r'];
   }
   else {
      $r = $_POST['r'];
   }
//   if (isset($_REQUEST['h']) and ($_REQUEST['h']<>'')) {
//      $h = $_REQUEST['h'];
//	  $sql = "select grau_conhecimento from projetos.cargos_comp_inst where cd_cargo='$r' and cd_comp_inst=$h";
//	  echo $sql;
//	  $rs = pg_exec($db, $sql);
//	  $reg = pg_fetch_array($rs);
//	  $g = $reg['grau_conhecimento'];
//  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>...:: Previs&atilde;o Or&ccedil;ament&aacute;ria ::...</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script language="JavaScript">
  <!--
     function valida_form(f) {
	    if ((f.projeto.value=="")) {
		   alert("Você deve escolher ao menos um 'Projeto'");
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
<body bgcolor="#FFFFFF" onLoad="atualiza_pai();">
<?
   if (isset($_POST['projeto'])) {
      $h = $_POST['h'];
	  $grau = $_POST['grau'];
      $sql = "select * from projetos.ativ_projetos where cd_atividade='$r' and cd_projeto=$projeto";
	  $rs = pg_exec($db, $sql);
	  if (pg_numrows($rs) > 0) {
//	     $sql = "update projetos.ativ_projeto set grau_conhecimento=$grau where cd_ativ='$r' and cd_projeto=$projeto";
	  }
	  else {
	     $sql = "insert into projetos.ativ_projetos(cd_atividade, cd_projeto, cd_programa, num_dias) values('$r', $projeto, '$programa', $dias)";
//		 echo $sql;
      }
	  $rs = pg_exec($db, $sql);
   }	     
?>
<form name="frmativprojprog" method="post" action="atividade_orcamento.php" onSubmit="return valida_form(this);">
  <?
     echo "<input type='hidden' name='r' value='$r'>"
  ?>
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td align="center" bgcolor="#CCCCCC"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Projeto</font></strong></td>
      <td align="center" bgcolor="#CCCCCC"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">N&uacute;m 
        Dias</font></strong></td>
      <td align="center" bgcolor="#CCCCCC"><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Programa</font></strong></td>
      <td bgcolor="#CCCCCC">&nbsp;</td>
    </tr>
    <tr> 
      <td> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
        <select name="projeto" size="10" id="projeto">
          <?
		   $sql="select codigo, nome from projetos.projetos where dt_exclusao is null order by nome ";
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
      <td> <input name="dias" type="text" id="dias" value="0" size="5" maxlength="3"></td>
      <td><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
        <select name="programa" size="10" id="programa">
          <?
		   $sql="select codigo, descricao from listas where categoria = 'PRFC' order by descricao";
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
      <td>&nbsp;</td>
    </tr>
    <tr> 
      <td colspan="3" bgcolor="#CCCCCC"> 
        <div align="center"> 
          <input type="submit" name="Submit" value="Gravar">
          <input name="fechar" type="button" id="fechar" value="Fechar" onClick="self.close();">
        </div></td>
      <td>&nbsp;</td>
    </tr>
  </table>
</form>
<?
   pg_close($db);
?>
</body>
</html>