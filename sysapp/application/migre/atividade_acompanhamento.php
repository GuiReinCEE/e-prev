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
//	  $sql = "select grau_conhecimento from projetos.acompanhamento_atividades where cd_atividade='$r' and cd_acomp=$h";
//	  echo $sql;
//	  $rs = pg_exec($db, $sql);
//	  $reg = pg_fetch_array($rs);
//	  $g = $reg['grau_conhecimento'];
//  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>...:: Acompanhamento de atividade ::...</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script language="JavaScript">
  <!--
     function valida_form(f) {
	    if ((f.comp_espec.value=="")) {
		   alert("Você deve escolher ao menos uma 'Competência Específica'");
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
<body bgcolor="#F0E8BA" onLoad="atualiza_pai();">
<?
	if (isset($_POST['txt_campo'])) {
		$h = $_POST['h'];
		$grau = $_POST['grau'];
		$sql = "select * from projetos.acompanhamento_atividades where cd_atividade='$r' ";
		$rs = pg_exec($db, $sql);
		$sql = "insert into projetos.acompanhamento_atividades(cd_atividade, texto_acomp, dt_acompanhamento) values('$r', '$txt_campo', now())";
		$rs = pg_exec($db, $sql);
	}	     
?>
<form name="frmHabRec" method="post" action="atividade_acompanhamento.php" onSubmit="return valida_form(this);">
  <?
     echo "<input type='hidden' name='r' value='$r'>"
  ?>
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td><strong><font color="#0046ad" size="3" face="Verdana, Arial, Helvetica, sans-serif">Acompanhamento 
        de Atividade</font></strong></td>
      <td><input type="image" src="img/btn_salvar.jpg" border="0"><img src="img/btn_retorna.jpg" border="0" onClick="self.close();"></td>
    </tr>
    <tr> 
      <td colspan="2"> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
        <textarea name="txt_campo" cols="50" rows="10" id="txt_campo"></textarea>
        </font></td>
    </tr>
  </table>
</form>
<?
   pg_close($db);
?>
</body>
</html>