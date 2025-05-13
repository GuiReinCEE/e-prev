<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
   if (isset($_REQUEST['r'])) {
      $r = $_REQUEST['r'];
   }
   else {
      $r = $_POST['r'];
   }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
  <title>...:: Documentos anexos ::...</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script language="JavaScript">
  <!--
     function valida_form(f) {
	    if ((f.texto_link.value=="")) {
		   alert("Você deve informar o texto para o link");
		   return false;
		}
	    if ((f.arquivo.value=="")) {
		   alert("Você deve informar o texto para o link");
		   return false;
		}
		else {
		   return true;
		}
	 }
     function fnc_arq(a) {
//		alert(a.value);
//		alert(form2.arq.value);
		form2.arq.value = a.value;
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
<body onLoad="atualiza_pai();">
<font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
<?
   if ($cd_link != '') {
		$cd_link = $cd_link;
		$item = $i;				// $i = cd_item
		$div = $d;			// $d = divisão
		$sql = "select * from projetos.links_intra_div where cd_item=$item and div='$div' and cd_link = $cd_link";
		$rs = pg_exec($db, $sql);
		if (pg_numrows($rs) > 0) {
			$reg=pg_fetch_array($rs);
			?>
			<font color="#0046ad"><strong> Anexando documentos: </strong></font></font> 
			<form name="form1" method="post" action="grava_anexo_docs.php" enctype="multipart/form-data"  onSubmit="return valida_form(this)">
  <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
  <input type="hidden" name="MAX_FILE_SIZE" value="2048000">
  <input type="hidden" name="cd_item" value="<?echo $i ?>">
  <input type="hidden" name="cd_link" value="<?echo $cd_link ?>">
  <input type="hidden" name="div" value="<?echo $d ?>">
  </font> 
  <table>
				<tr> 			
				  <td bgcolor="#F4F4F4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">1. 
					Selecione o arquivo:</font><br>
        <input type="text" name="arq" id="arq" size="55" value="<?echo $reg['link'];?>"> 
      </td>
					</tr>
					<tr>
						
				  <td bgcolor="#F4F4F4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">2. 
					Informe o texto do link:</font> 
					<br>
        <input name="texto_link" type="text" id="texto_link" size="55" maxlength="55" value="<?echo $reg['texto_link'];?>"> 
      </td>
					</tr>
					<tr> 
						
				  <td bgcolor="#F4F4F4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">3. 
					Confirme a opera&ccedil;&atilde;o &gt;&gt;&gt; </font> 
					<input type="submit" name="Submit" value="Confirmar">
						</td>
					</tr>
			  </table>
			</form>
			<font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
			<?
		}
		else {
//	     $sql = "insert into projetos.cargos_comp_inst(cd_cargo, cd_comp_inst) values('$r', $comp_inst)";
//		 echo $sql;
		}
	}
	else {
		?>
		<font color="#0046ad"><strong> Anexando documentos: </strong></font></font> 
		  <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
		  </font> 
		  <table>
			<tr> 			
			  <td bgcolor="#F4F4F4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">1. 
				Selecione o arquivo:</font><br>
        <input type="file" name="arquivo" size="55" id="arquivo" value="Selecionar o arquivo a ser anexado" onBlur="fnc_arq(this)"></td>

				</tr>
		<form name="form2" method="post" action="grava_anexo_docs.php" enctype="multipart/form-data"  onSubmit="return valida_form(this)">
		  <input type="hidden" name="MAX_FILE_SIZE" value="2048000">
		  <input type="hidden" name="cd_item" value="<?echo $i ?>">
		  <input type="hidden" name="div" value="<?echo $d ?>">
				<tr>
					
			  <td bgcolor="#F4F4F4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">2. 
				Informe o texto do link:</font> 
				<input name="arq" type="hidden" id="arq" value="">
				<br>
					<input name="texto_link" type="text" id="texto_link" size="55" maxlength="55">
					</td>
				</tr>
				<tr> 
					
			  <td bgcolor="#F4F4F4"><font size="2" face="Verdana, Arial, Helvetica, sans-serif">3. 
				Confirme a opera&ccedil;&atilde;o &gt;&gt;&gt; </font> 
				<input type="submit" name="Submit" value="Confirmar">
					</td>
				</tr>
		</form>

		  </table>
		<font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
		<?
//		$rs = pg_exec($db, $sql);
	}	     
	pg_close($db);
?>
</font> 
</body>
</html>