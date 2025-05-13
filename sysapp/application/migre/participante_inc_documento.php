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
//	  $sql = "select grau_conhecimento from projetos.cargos_comp_espec where cd_cargo='$r' and cd_comp_espec=$h";
//	  echo $sql;
//	  $rs = pg_exec($db, $sql);
//	  $reg = pg_fetch_array($rs);
//	  $g = $reg['grau_conhecimento'];
//  }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>...:: Documentos do participante ::...</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script src="inc/pck_funcoes.js"></script>
  <script language="JavaScript">
  <!--
     function valida_form(f) {
	    if ((f.opt_tipo_doc.value=="")) {
		   alert("Código do documento deve ser informado!");
		   return false;
		}
		else {
		    if ((f.num_vias.value=="")) {
			   alert("Número de vias do documento deve ser informado!");
			   return false;
			}
			else {
			    if ((f.dt_entrega.value=="")) {
				   alert("Data de entrega do documento deve ser informada!");
				   return false;
				 }
				else {
				   return true;
				}
			}
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
<body bgcolor="#F4F4F4" onLoad="atualiza_pai();">
<?
   if (isset($_POST['opt_tipo_doc'])) {
		$txt_dt_entrega = ( $dt_entrega == '' ? 'Null' : "'".convdata_br_iso($dt_entrega)."'");
		if ($e == '') { $e = 7; }
		if ($seq_dependencia == '') { $seq_dependencia = 0; }

	    $sql = "select count(*) as num_regs from expansao.registros_documentos where cd_empresa = $e and cd_registro_empregado = $r and cd_doc = $opt_tipo_doc ";
		$rs = pg_exec($db, $sql);
		$reg = pg_fetch_array($rs);
		$g = $reg['num_regs'];
		if ($g == 0) {
			$h = $_POST['h'];
			$grau = $_POST['grau'];
// ----------------------------------------- tabela intermediária para atualizar Oracle:
/*
	    	$sql2 = "insert into registros_documentos_ceeeprev_hist(cd_empresa, cd_registro_empregado, ";
			$sql2 = $sql2 . " seq_dependencia, cd_doc, nro_via, dt_entrega, obrigatorio, dt_inclusao, usuario) ";
			$sql2 = $sql2 . " values($e, $r, $seq_dependencia, $opt_tipo_doc, 1, $txt_dt_entrega, 'S', current_date, '$USUARIO')";
			$rs = pg_exec($db, $sql2);
*/			
// ----------------------------------------- tabela em que ficam guardados os documentos no Postgresql:
	    	$sql = "insert into expansao.registros_documentos(cd_empresa, cd_registro_empregado, ";
			$sql = $sql . " seq_dependencia, cd_doc, nro_via, dt_entrega, obrigatorio, dt_inclusao, usuario) ";
			$sql = $sql . " values($e, $r, $seq_dependencia, $opt_tipo_doc, 1, $txt_dt_entrega, 'S', current_date, '".$_SESSION['U']."')";
		}
		else {
	    	$sql = "update expansao.registros_documentos set dt_entrega = $txt_dt_entrega ";
			$sql = $sql . " where cd_empresa = $e and cd_registro_empregado = $r and cd_doc = $opt_tipo_doc ";
		}
		$rs = pg_exec($db, $sql);
	}	     
?>
<form name="frmHabRec" method="post" action="participante_inc_documento.php" onSubmit="return valida_form(this);">
  <?
     echo "<input type='hidden' name='r' value='$r'>"
  ?>
  <table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
    <tr align="center" bgcolor="#dae9f7"> 
      <td><font face="Verdana, Arial, Helvetica, sans-serif"><strong><font size="2">C&oacute;digo</font></strong></font></td>
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong>Data<br>
        Entrega</strong></font> </td>
    </tr>
    <tr bgcolor="#F4F4F4"> 
      <td width="60%"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input type="radio" name="opt_tipo_doc" value="1">
        Carteira de Identidade / CIC<br>
        <input type="radio" name="opt_tipo_doc" value="225">
        Pedido de Inscri&ccedil;&atilde;o</font> </td>
      <td width="40%"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <input name="dt_entrega" type="text" id="dt_entrega" size="12" onBlur="verifica_data(this)" onKeyUp="mascara_data(this)"  maxlength="10">
        dd/mm/yyyy</font></td>
    </tr>
    <tr bgcolor="#F4F4F4"> 
      <td colspan="2"> <div align="center"> 
          <input type="submit" name="Submit" value="Gravar">
          <input name="fechar" type="button" id="fechar" value="Fechar" onClick="self.close();">
        </div></td>
    </tr>
  </table>
</form>
<?
   pg_close($db);

function convdata_br_iso($dt) {
      // Pressupõe que a data esteja no formato DD/MM/AAAA
      // A melhor forma de gravar datas no PostgreSQL é utilizando 
      // uma string no formato DDDD-MM-AA. Esta função justamente 
      // adequa a data a este formato
      $d = substr($dt, 0, 2);
      $m = substr($dt, 3, 2);
      $a = substr($dt, 6, 4);
      return $a.'-'.$m.'-'.$d;
}
	

?>
</body>
</html>