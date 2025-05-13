<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	if (isset($_POST['txt_campo'])) 
	{
		if ($cd_questao == 0) 
		{
			$sql = "
					UPDATE projetos.enquete_resultados 
			           SET descricao = '".$_POST['txt_campo']."'  
					 WHERE cd_enquete = ".$_REQUEST['cd_enquete']." 
					   AND ip         = '".$_REQUEST['cd_ip']."' 
					   AND questao    = 'Texto' 
				   ";
		} 
		else 
		{
			$sql = "
					UPDATE projetos.enquete_resultados 
			           SET descricao = '".$_POST['txt_campo']."' 
					 WHERE cd_enquete = ".$_REQUEST['cd_enquete']." 
					   AND ip         = '".$_REQUEST['cd_ip']."' 
					   AND questao    ='R_".$_REQUEST['cd_questao']."' 
				   ";			
		}
		$rs = pg_query($db, $sql);
		$resp = $txt_campo;
	}	     
	
	
	
	if ($_REQUEST['cd_questao'] == 0) 
	{
		$sql = "select descricao from projetos.enquete_resultados where cd_enquete=".$_REQUEST['cd_enquete']." and ip='".$_REQUEST['cd_ip']."' and questao ='Texto' ";
	} 
	else 
	{
		$sql = "select descricao from projetos.enquete_resultados where cd_enquete=".$_REQUEST['cd_enquete']." and ip='".$_REQUEST['cd_ip']."' and questao ='R_".$_REQUEST['cd_questao']."' ";
	}
	
	$rs = pg_query($db, $sql);
	$reg = pg_fetch_array($rs);
	$resp = $reg['descricao'];
?>
<html>
<head>
  <title>...:: Resposta dissertativa ::...</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script language="JavaScript">
  <!--
     function valida_form(f) {
	    if ((f.txt_campo.value=="")) {
		   alert("Descrição deve ser preenchida!");
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

<form name="frmHabRec" method="post" action="edita_resp_enquete.php" onSubmit="return valida_form(this);">
<?
	echo "<input type='hidden' name='cd_enquete' value='".$_REQUEST['cd_enquete']."'>";
	echo "<input type='hidden' name='cd_ip' value='".$_REQUEST['cd_ip']."'>";
	echo "<input type='hidden' name='cd_questao' value='".$_REQUEST['cd_questao']."'>";
?>
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td><strong><font color="#006633" size="3" face="Verdana, Arial, Helvetica, sans-serif">Resposta</font></strong></td>
      <td align="right">
	  <input type="image" src="img/btn_salvar.jpg" border="0">
	  <img src="img/btn_retorna.jpg" border="0" onClick="self.close();" style="cursor:pointer;">
	  </td>
    </tr>
    <tr> 
      <td colspan="2"> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
        <textarea name="txt_campo" cols="50" rows="10" id="txt_campo"><? echo $resp; ?></textarea>
        </font></td>
    </tr>
  </table>
</form>
<?
   pg_close($db);
?>
</body>
</html>