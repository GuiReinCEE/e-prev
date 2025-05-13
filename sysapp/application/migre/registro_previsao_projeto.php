<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/nextval_sequence.php');


	if(count($_POST) > 0) 
	{
		#### ABRE TRANSACAO COM O BD ####
		pg_query($db,"BEGIN TRANSACTION");	
		
		if ($_POST['cd_previsao'] != '') 
		{
 			#### UPDATE ####
			$sql = "
			        UPDATE projetos.previsoes_projetos 
					   SET mes       = '".$_POST['mes']."', 
						   ano       = '".$_POST['ano']."', 							
						   descricao = '".$_POST['descricao']."', 
						   obs       = '".$_POST['obs']."'
  					  WHERE cd_acomp    = ".$_POST['cd_acomp']." 
						AND cd_previsao = ".$_POST['cd_previsao'].";
				   ";						
		}
		else 
		{
			#### INSERT ####
			$cd_previsao_novo = getNextval("projetos", "previsoes_projetos", "cd_previsao", $db); // PEGA NEXTVAL DA SEQUENCE DO CAMPO			
			if ($cd_previsao_novo > 0) // TESTA SE RETORNOU ALGUM VALOR
			{			
				switch($_POST['mes'])
				{
					case  "JANEIRO":   $nr_mes = "01"; break;
					case  "FEVEREIRO": $nr_mes = "02"; break;
					case  "MARÇO":     $nr_mes = "03"; break;
					case  "ABRIL":     $nr_mes = "04"; break;
					case  "MAIO":      $nr_mes = "05"; break;
					case  "JUNHO":     $nr_mes = "06"; break;
					case  "JULHO":     $nr_mes = "07"; break;
					case  "AGOSTO":    $nr_mes = "08"; break;
					case  "SETEMBRO":  $nr_mes = "09"; break;
					case  "OUTUBRO":   $nr_mes = "10"; break;
					case  "NOVEMBRO":  $nr_mes = "11"; break;
					case  "DEZEMBRO":  $nr_mes = "12"; break;
					                           
				}				
				$sql = "
				        INSERT INTO projetos.previsoes_projetos
		                     (
							   cd_previsao,
							   cd_acomp, 
							   dt_previsao, 
							   mes,
							   ano,
							   descricao, 
							   obs
							 ) 
						VALUES
						     (
						       ".$cd_previsao_novo.",
							   ".$_POST['cd_acomp'].", 
							   TO_DATE('01/".$nr_mes."/".$_POST['ano']."','DD/MM/YYYY'), 
							   '".$_POST['mes']."', 
							   '".$_POST['ano']."', 
							   '".$_POST['descricao']."', 
							   '".$_POST['obs']."'
							 );
					   ";

			}
			else
			{
				// ---> DESFAZ A TRANSACAO COM BD<--- //
				pg_query($db,"ROLLBACK TRANSACTION");
				pg_close($db);
				echo "Erro a tentar incluir esta previsão (SEQ)";	
				exit;
			}			
		}

		//echo "<PRE>".$sql;
		//exit;
		
		$ob_resul= @pg_query($db,$sql);
		if(!$ob_resul)
		{
			$ds_erro = "ERRO: ".str_replace("ERROR:","",pg_last_error($db));
			// ---> DESFAZ A TRANSACAO COM BD<--- //
			pg_query($db,"ROLLBACK TRANSACTION");
			pg_close($db);
			echo $ds_erro;
			exit;
		}
		else
		{
			// ---> COMITA DADOS NO BD <--- //
			pg_query($db,"COMMIT TRANSACTION"); 	
			if($cd_previsao_novo > 0)
			{
				$_REQUEST['cd_previsao'] = $cd_previsao_novo;
			}
			else
			{
				$_REQUEST['cd_previsao'] = $_POST['cd_previsao'];
			}
			
			$_REQUEST['cd_acomp'] = $_POST['cd_acomp'];
			
			echo "	<script>			
						opener.location.href = opener.location.href;
					</script>";				
		}		
	}	     

	if ($_REQUEST['cd_previsao'] != '') 
	{
		$sql = "SELECT UPPER(mes) AS mes, 
					   ano, 
					   descricao, 
					   obs
				  FROM projetos.previsoes_projetos 
				 WHERE cd_acomp    = ".$_REQUEST['cd_acomp']." 
				   AND cd_previsao = ".$_REQUEST['cd_previsao'];
		$rs = pg_query($db, $sql);
		$ar_select = pg_fetch_array($rs);
	}
	else
	{
		switch(date('m'))
		{
			case  1: $ar_select['mes'] = "JANEIRO"; break;
			case  2: $ar_select['mes'] = "FEVEREIRO"; break;
			case  3: $ar_select['mes'] = "MARÇO"; break;
			case  4: $ar_select['mes'] = "ABRIL"; break;
			case  5: $ar_select['mes'] = "MAIO"; break;
			case  6: $ar_select['mes'] = "JUNHO"; break;
			case  7: $ar_select['mes'] = "JULHO"; break;
			case  8: $ar_select['mes'] = "AGOSTO"; break;
			case  9: $ar_select['mes'] = "SETEMBRO"; break;
			case 10: $ar_select['mes'] = "OUTUBRO"; break;
			case 11: $ar_select['mes'] = "NOVEMBRO"; break;
			case 12: $ar_select['mes'] = "DEZEMBRO"; break;
			
		}
		$ar_select['ano'] = date('Y');
	}

?>
<html>
<head>
	<title>...:: Previsto para o próximo mês ::...</title>
	<style>
		*{
			font-size: 10pt;
			font-weight: normal;
			font-family: Verdana, Arial, 'MS Sans Serif';			
		}
		
		body{
			background: #D4D0C8;
		}
		
		fieldset {
			padding-left: 10px;
			padding-right: 10px;
		}
		
		legend{
			font-size: 14pt;
			font-weight: normal;
		}

		label{
			font-weight: bold;
		}
		
		input{
			background: #FFFFFF;
			width:100%;			
		}

		select{
			background: #FFFFFF;
			width:100%;			
		}
		
		optgroup {
			font-weight: bold;
		}
		
		textarea{
			background: #FFFFFF;
			height:100px; 
			width:100%;
		}
		
		span{
			font-size: 8pt;
		}
		
		.css_botao{
			text-align:right;
			width: 100%;
		}
	</style>
	<script src="inc/mascara.js"></script>
	<script language="JavaScript">
		function validForm() 
		{
			var ds_msg_erro = "";

			if (trimValue(document.getElementById('ano').value) == "") 
			{
			   ds_msg_erro += "\n- Informe o Ano";
			}
			
			if (trimValue(document.getElementById('descricao').value) == "") 
			{
			   ds_msg_erro += "\n- Informe a Previsão";
			}			
			
			if(trimValue(ds_msg_erro) != "")
			{
				alert("Os seguinte itens são necessários:\n" + ds_msg_erro)
			}
			else
			{		
				document.getElementById('formulario').submit();
			}			
		}
	</script>	
</head>
<body>
<form name="formulario" id="formulario" action="" method="post" enctype="multipart/form-data">
	<?
		echo "<input type='hidden' name='cd_acomp' value='".$_REQUEST['cd_acomp']."'>";
		echo "<input type='hidden' name='cd_previsao' value='".$_REQUEST['cd_previsao']."'>";
	?>
	
	<fieldset>
		<legend>Previsto para o próximo mês</legend>
		
		<div class="css_botao">
			<img src="img/salvar_p.gif"        onclick="validForm();" style="cursor:pointer;" border="0" title="Salvar registro de reunião">
			<img src="img/fechar_janela_p.gif" onClick="window.close();" style="cursor:pointer;" border="0" title="Fechar janela">											
		</div>
		
		<label for="mes">Mês/Ano:</label>
		<br>
		<select name="mes" style="width:150px;">
			<option value="JANEIRO"   <? if(strtoupper($ar_select['mes']) == "JANEIRO")  { echo "selected"; } ?>>JANEIRO</option>
			<option value="FEVEREIRO" <? if(strtoupper($ar_select['mes']) == "FEVEREIRO"){ echo "selected"; } ?>>FEVEREIRO</option>
			<option value="MARÇO"     <? if(strtoupper($ar_select['mes']) == "MARÇO")    { echo "selected"; } ?>>MARÇO</option>
			<option value="ABRIL"     <? if(strtoupper($ar_select['mes']) == "ABRIL")    { echo "selected"; } ?>>ABRIL</option>
			<option value="MAIO"      <? if(strtoupper($ar_select['mes']) == "MAIO")     { echo "selected"; } ?>>MAIO</option>
			<option value="JUNHO"     <? if(strtoupper($ar_select['mes']) == "JUNHO")    { echo "selected"; } ?>>JUNHO</option>
			<option value="JULHO"     <? if(strtoupper($ar_select['mes']) == "JULHO")    { echo "selected"; } ?>>JULHO</option>
			<option value="AGOSTO"    <? if(strtoupper($ar_select['mes']) == "AGOSTO")   { echo "selected"; } ?>>AGOSTO</option>
			<option value="SETEMBRO"  <? if(strtoupper($ar_select['mes']) == "SETEMBRO") { echo "selected"; } ?>>SETEMBRO</option>
			<option value="OUTUBRO"   <? if(strtoupper($ar_select['mes']) == "OUTUBRO")  { echo "selected"; } ?>>OUTUBRO</option>
			<option value="NOVEMBRO"  <? if(strtoupper($ar_select['mes']) == "NOVEMBRO") { echo "selected"; } ?>>NOVEMBRO</option>
			<option value="DEZEMBRO"  <? if(strtoupper($ar_select['mes']) == "DEZEMBRO") { echo "selected"; } ?>>DEZEMBRO</option>
		</select>
		/
		<input type="text" name="ano" id="ano" value="<? echo $ar_select['ano']; ?>" onblur="validaAno(this)" maxlength="4" style="width:60px;">
		<br>
		<br>	
		
		<label for="descricao">Previsão:</label>
		<br>
		<textarea name="descricao" id="descricao" wrap="physical"><? echo $ar_select['descricao']; ?></textarea>
		<br>
		<br>	
		
		<label for="obs">Observação:</label>
		<br>
		<textarea name="obs" id="obs" wrap="physical"><? echo $ar_select['obs']; ?></textarea>
		<br>
		<br>	
		

	</fieldset>
	<script>
		MaskInput(document.getElementById('ano'),  "9999");
	</script>
</form>
</body>
</html>
<?
/*
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
<html>
<head>
  <title>...:: Registro de planejamento de projeto ::...</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <script src="inc/pck_funcoes.js"></script>
  <script language="JavaScript">
  <!--
     function valida_form(f) {
		if ((f.descricao.value=="")) {
		   alert("Descrição deve ser informada!");
		   return false;
		}
		else if ((f.mes.value=="")) {
		   alert("Mês/ano devem ser informados!");
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
<body bgcolor="#DCDCCC" onLoad="atualiza_pai();">
<?
	if (isset($_POST['descricao'])) {
		$h = $_POST['h'];
		$grau = $_POST['grau'];
		if ($h != '') {
			$sql="select cd_previsao, to_char(dt_previsao,'dd/mm/yyyy'), descricao, mes, ano, obs from projetos.previsoes_projetos where cd_acomp=$r and cd_previsao = $h";
			$rs = pg_exec($db, $sql);
			if ($reg = pg_fetch_row($rs)) {
					$v_dt_previsao = trim($reg[1]);
					$v_descricao = trim($reg[2]);
					$v_mes = trim($reg[3]);
					$v_ano = trim($reg[4]);
					$v_obs = trim($reg[5]);
			}
			$sql = "update projetos.previsoes_projetos set descricao = '$descricao', mes = '$mes', ano = '$ano', obs = '$obs' where cd_acomp=$r and cd_previsao = $h";
			$rs = pg_exec($db, $sql);
		}
		else {
			$sql = "insert into projetos.previsoes_projetos(cd_acomp, dt_previsao, descricao, mes, ano, obs) ";
			$sql = $sql . " values('$r', current_timestamp, '$descricao', '$mes', '$ano', '$obs')";
			$rs = pg_exec($db, $sql);
		}
	}	     
	else {
		if ($h != '') {
			$sql="select cd_previsao, to_char(dt_previsao,'dd/mm/yyyy'), descricao, mes, ano, obs from projetos.previsoes_projetos where cd_acomp=$r and cd_previsao = $h";
			$rs = pg_exec($db, $sql);
			if ($reg = pg_fetch_row($rs)) {
				$v_dt_previsao = trim($reg[1]);
				$v_descricao = trim($reg[2]);
				$v_mes = trim($reg[3]);
				$v_ano = trim($reg[4]);
				$v_obs = trim($reg[5]);
			}
		}
	}
?>
<form name="frmHabRec" method="post" action="registro_previsao_projeto.php" onSubmit="return valida_form(this);">
  <?
	echo "<input type='hidden' name='r' value='$r'>";
	echo "<input type='hidden' name='h' value='$h'>";
  ?>
  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr align="right"> 
      <td colspan="3"> 
        <input name="image" type="image" src="img/btn_salvar.jpg" border="0"><img src="img/btn_retorna.jpg"  onClick="self.close();"></td>
    </tr>
    <tr align="center" bgcolor="#0046ad"> 
      <td colspan="3"><strong><font color="#FFFFFF" size="3" face="Verdana, Arial, Helvetica, sans-serif">Registro 
        de planejamento de projeto</font></strong> </td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Descri&ccedil;&atilde;o:</font></td>
      <td colspan="2"><font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
        <textarea name="descricao" cols="40" rows="3" id="textarea"><? if ($h != '') {echo $v_descricao;}?></textarea>
        </font></td>
    </tr>
    <tr> 
      <td><font size="2" face="Verdana, Arial, Helvetica, sans-serif">Mes/ano:</font></td>
      <td colspan="2"><input name="mes" type="text" id="mes" size="12" maxlength="12" value="<? if ($h != '') {echo $v_mes;}?>">
        / 
        <input name="ano" type="text" id="ano" size="4" maxlength="4" value="<? if ($h != '') {echo $v_ano;}?>"></td>
    </tr>
    <tr> 
      <td> <font size="2" face="Verdana, Arial, Helvetica, sans-serif">Observa&ccedil;&atilde;o:</font></td>
      <td colspan="2"><textarea name="obs" cols="40" rows="2" id="obs"><? if ($h != '') {echo $v_obs;}?></textarea></td>
    </tr>
  </table>
</form>
<?
   pg_close($db);
?>
</body>
</html>
*/
?>