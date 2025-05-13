<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   if (isset($_REQUEST['r'])) {
      $cd_empresa = $_REQUEST['r'];
   }
   else {
      $cd_empresa = $_POST['r'];
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
		   alert("Você deve escolher um 'Ramo empresarial'");
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
<body bgcolor="#CCCCCC" onLoad="atualiza_pai();">
<?
// -----------------------------------------------------------------------------
	if (isset($_POST['ramo_empresarial'])) {
		$h = $_POST['h'];
		$grau = $_POST['grau'];
		$sql = "update expansao.empresas_instituicoes set cd_ramo=$ramo_empresarial where cd_emp_inst='$r' ";
		$rs = pg_exec($db, $sql);
	}	     
// -----------------------------------------------------------------------------
	$sql = "select cd_ramo from expansao.empresas_instituicoes where cd_emp_inst = $cd_empresa ";
	$rs = pg_exec($db, $sql);
	$reg = pg_fetch_row($rs);
	$v_cd_ramo = $reg[0];
// -----------------------------------------------------------------------------
?>
<form name="frmHabRec" method="post" action="ramo_empresa.php" onSubmit="return valida_form(this);">
  <?
     echo "<input type='hidden' name='r' value='$r'>"
  ?>
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td width="52"><font color="#006633" size="5" face="Arial, Helvetica, sans-serif">Ramos 
        Empresariais</font></td>
    </tr>
    <tr> 
      <td>
        <select name="ramo_empresarial" size="15" id="ramo_empresarial" style="font-size: 9px">
          <?
// ------------------------------------------------------------------------
			$sql = "select cd_ramo, descricao from expansao.ramo_empresarial order by descricao";
			$rs = pg_exec($db, $sql);
			while ($reg = pg_fetch_row($rs)) {
				if ($v_cd_ramo == $reg[0]) {
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