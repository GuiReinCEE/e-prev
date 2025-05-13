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
  <title>...:: Compet&ecirc;ncias Institucionais ::...</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script language="JavaScript">
  <!--
     function valida_form(f) {
	    if ((f.comp_inst.value=="")) {
		   alert("Você deve escolher ao menos uma 'Competência Institucional'");
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
<body bgcolor="#DAE9F7" onLoad="atualiza_pai();">
<?
   if (isset($_POST['comp_inst'])) {
      $h = $_POST['h'];
	  $grau = $_POST['grau'];
      $sql = "select * from projetos.cargos_comp_inst where cd_cargo='$r' and cd_comp_inst=$comp_inst";
	  $rs = pg_exec($db, $sql);
	  if (pg_numrows($rs) > 0) {
//	     $sql = "update projetos.cargos_comp_inst set grau_conhecimento=$grau where cd_cargo='$r' and cd_comp_inst=$comp_inst";
	  }
	  else {
	     $sql = "insert into projetos.cargos_comp_inst(cd_cargo, cd_comp_inst) values('$r', $comp_inst)";
//		 echo $sql;
      }
	  $rs = pg_exec($db, $sql);
   }	     
?>
<form name="frmHabRec" method="post" action="cargo_inc_comp_inst.php" onSubmit="return valida_form(this);">
  <?
     echo "<input type='hidden' name='r' value='$r'>"
  ?>
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td><strong><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Competência 
        Institucional</font></strong></td>
    </tr>
    <tr> 
      <td> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
        <select name="comp_inst" size="10" id="comp_inst">
          <?
		   $sql="select cd_comp_inst, nome_comp_inst from projetos.comp_inst order by nome_comp_inst";
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
    </tr>
    <tr> 
      <td> <div align="center"> 
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