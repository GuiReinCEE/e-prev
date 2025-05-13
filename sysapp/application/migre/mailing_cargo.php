<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   if (isset($_REQUEST['r'])) {
      $cd_mailing = $_REQUEST['r'];
   }
   else {
      $cd_mailing = $_POST['r'];
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
	    if (f.cargo.value=="") {
		   alert("Você deve escolher um cargo");
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
<body bgcolor="#DDDDDD" onLoad="atualiza_pai();">
<?
// -----------------------------------------------------------------------------
	if (isset($_POST['cd_cargo'])) {
		$h = $_POST['h'];
		$sql = "update expansao.mailing set cd_cargo=$cd_cargo where cd_mailing=$cd_mailing ";
		$rs = pg_exec($db, $sql);
	}	     
// -----------------------------------------------------------------------------
	$sql = "select cd_cargo from expansao.mailing where cd_mailing = $cd_mailing ";
	$rs = pg_exec($db, $sql);
	$reg = pg_fetch_row($rs);
	$v_cd_cargo = $reg[0];
// -----------------------------------------------------------------------------
?>
<form name="frmHabRec" method="post" action="mailing_cargo.php" onSubmit="return valida_form(this);">
  <?
     echo "<input type='hidden' name='r' value='$r'>"
  ?>
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="52"><font color="#006633" size="5" face="Arial, Helvetica, sans-serif">Cargos</font></td>
    </tr>
    <tr> 
      <td>
        <select name="cd_cargo" size="15" id="cd_cargo" style="font-size: 9px">
          <?
// ------------------------------------------------------------------------
			$sql = "select cd_cargo, descricao from expansao.cargos_mailing order by descricao";
			$rs = pg_exec($db, $sql);
			while ($reg = pg_fetch_row($rs)) {
				if ($v_cd_cargo == $reg[0]) {
					echo "<option value='".$reg[0]."' selected>".$reg[1]."</option>\r";
				}
				else {
					echo "<option value='".$reg[0]."'>".$reg[1]."</option>\r";
				}
			}
		?>
        </select>
        </td>
    </tr>
    <tr> 
      <td>
          <input type="submit" name="Submit" value="Gravar">
          <input name="fechar" type="button" id="fechar" value="Fechar" onClick="self.close();">
</td>
    </tr>
  </table>
</form>
<?
   pg_close($db);
?>
</body>
</html>