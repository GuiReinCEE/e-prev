<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');

	if(count($_POST) > 0) 
	{
		$fl_arquivo = false;
		$uploadDir       = '/u/www/upload/';
		if(count($_FILES) > 0)
		{
			$ds_nome_arquivo = date("YmdHis");
			$ds_extensao     = strtolower(strrchr($_FILES['ds_arquivo']['name'], "."));			
			$uploadFile      = $uploadDir.$ds_nome_arquivo.$ds_extensao;			
			if(move_uploaded_file($_FILES['ds_arquivo']['tmp_name'], $uploadFile))
			{
				$fl_arquivo      = true;
			}
		}
		
		if ($_POST['h'] != '') 
		{
 			$sql = "UPDATE projetos.reunioes_projetos 
					   SET dt_reuniao  = TO_DATE('".$_POST['dt_reuniao']."','DD/MM/YYYY'), 
							descricao  = '".$_POST['descricao']."', 
							envolvidos = '".$_POST['envolvidos']."', 
							motivo     = '".$_POST['motivo']."'"; 
			if($fl_arquivo)
			{
				$sql.=" ,   ds_arquivo        = '".$_FILES['ds_arquivo']['name']."'";
				$sql.=" ,   ds_arquivo_fisico = '".$ds_nome_arquivo.$ds_extensao."'";
			}
			elseif(trim($_POST['ds_arquivo_fisico']) != '')
			{
				$sql.=" ,   ds_arquivo        = NULL";
				$sql.=" ,   ds_arquivo_fisico = NULL";
				@unlink($uploadDir.$_POST['ds_arquivo_fisico']);
			}
			
			$sql.="   WHERE cd_acomp   = ".$_POST['r']." 
						AND cd_reuniao = ".$_POST['h'];
			$rs = pg_query($db, $sql);
		}
		else 
		{
			$sql = "INSERT INTO projetos.reunioes_projetos
			                  (
							    cd_acomp, 
								dt_reuniao, 
								descricao, 
								envolvidos, 
								motivo";
			if($fl_arquivo)
			{
				$sql.="       , ds_arquivo
                              , ds_arquivo_fisico";
			}								
			$sql.= "
							  ) 
					     VALUES
						      (
							    '".$_POST['r']."', 
								TO_DATE('".$_POST['dt_reuniao']."','DD/MM/YYYY'), 
								'".$_POST['descricao']."', 
								'".$_POST['envolvidos']."', 
								'".$_POST['motivo']."'";
			if($fl_arquivo)
			{
				$sql.="       , '".$_FILES['ds_arquivo']['name']."'
				              , '".$ds_nome_arquivo.$ds_extensao."'";
			}								
			$sql.= "		  )";
			$rs = pg_query($db, $sql);
		}
		
		$sql = "SELECT cd_reuniao, 
					   TO_CHAR(dt_reuniao,'DD/MM/YYYY') AS dt_reuniao, 
					   descricao, 
					   envolvidos,
					   motivo,
					   ds_arquivo,
					   ds_arquivo_fisico
				  FROM projetos.reunioes_projetos 
			     WHERE cd_acomp   = ".$_POST['r']." 
				   AND cd_reuniao = ".$_POST['h'];
		$rs = pg_query($db, $sql);
		$reg = pg_fetch_array($rs);
		$v_data_reuniao   = trim($reg['dt_reuniao']);
		$v_descricao      = trim($reg['descricao']);
		$v_envolvidos     = trim($reg['envolvidos']);
		$v_motivo         = trim($reg['motivo']);
		$v_arquivo        = trim($reg['ds_arquivo']);
		$v_arquivo_fisico = trim($reg['ds_arquivo_fisico']);	
		
		$_REQUEST['r'] = $_POST['r'];	
		$_REQUEST['h'] = $_POST['h'];	
		
		echo "	<script>			
					opener.location.reload(true);
				</script>";
	}	     
	else 
	{
		if ($_REQUEST['h'] != '') 
		{
			$sql = "SELECT cd_reuniao, 
					       TO_CHAR(dt_reuniao,'DD/MM/YYYY') AS dt_reuniao, 
					       descricao, 
					       envolvidos,
					       motivo,
					       ds_arquivo,
					       ds_arquivo_fisico
				      FROM projetos.reunioes_projetos 
				     WHERE cd_acomp   = ".$_REQUEST['r']." 
				       AND cd_reuniao = ".$_REQUEST['h'];
			$rs = pg_query($db, $sql);
			$reg = pg_fetch_array($rs);
			$v_data_reuniao   = trim($reg['dt_reuniao']);
			$v_descricao      = trim($reg['descricao']);
			$v_envolvidos     = trim($reg['envolvidos']);
			$v_motivo         = trim($reg['motivo']);
			$v_arquivo        = trim($reg['ds_arquivo']);
			$v_arquivo_fisico = trim($reg['ds_arquivo_fisico']);
		}
	}	
?>
<html>
<head>
	<title>...:: Registro de reuniões de projeto ::...</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="main.css" rel="stylesheet" type="text/css">
	<script src="inc/pck_funcoes.js"></script>
	<script language="JavaScript">
		function valida_form(f) 
		{
			if ((f.dt_reuniao.value=="")) 
			{
			   alert("Data da reunião deve ser informada!");
			   return false;
			}
			else if ((f.descricao.value=="")) 
			{
			   alert("Descrição deve ser informada!");
			   return false;
			}
			else if ((f.descricao.value==""))
			{
			   alert("Envolvidos devem ser informados!");
			   return false;
			}
			else 
			{
			   return true;
			}
		}
		
		function anexoDel(ds_arquivo_fisico)
		{
			if(confirm('Deseja realmente excluir o anexo da reunião?'))
			{
				document.getElementById('ds_arquivo_fisico').value = ds_arquivo_fisico;
				document.getElementById('frmHabRec').submit();
			}
		}
	</script>
</head>
<body bgcolor="#D5F4FF">
<form name="frmHabRec" id="frmHabRec" action="registro_reunioes_projeto.php" method="post" enctype="multipart/form-data" onSubmit="return valida_form(this);">

	<?
		echo "<input type='hidden' name='r' value='".$_REQUEST['r']."'>";
		echo "<input type='hidden' name='h' value='".$_REQUEST['h']."'>";
		echo "<input type='hidden' name='ds_arquivo_fisico' id='ds_arquivo_fisico' value=''>";
	?>
	
  <table border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#0099CC">
    <tr> 
      <td colspan="2" height="33" class="cabecalho">Email</td>
      <td align="right"> <input name="image" type="image" src="img/btn_salvar.jpg" border="0" title="Salvar reunião"><img src="img/btn_retorna.jpg"  onClick="self.close();" style="cursor:pointer;" title="Fechar janela"> 
      </td>
    </tr>
    <tr bgcolor="#D5F4FF"> 
      <td> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Data Reunião: 
        </font> </td>
      <td colspan="2"> <input type="text" name="dt_reuniao" id="dt_reuniao" value="<? echo $v_data_reuniao; ?>" onBlur="verifica_data(this)" onKeyUp="mascara_data(this)"> 
      </td>
    </tr>
    <tr bgcolor="#D5F4FF"> 
      <td> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Descrição: 
        </font> </td>
      <td colspan="2"> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
        <textarea name="descricao" cols="40" rows="3" id="textarea"><? echo $v_descricao; ?></textarea>
        </font> </td>
    </tr>
    <tr bgcolor="#D5F4FF"> 
      <td> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Envolvidos: 
        </font> </td>
      <td colspan="2"> <textarea name="envolvidos" cols="40" rows="2" id="envolvidos"><? echo $v_envolvidos; ?></textarea> 
      </td>
    </tr>
    <tr bgcolor="#D5F4FF"> 
      <td> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Motivo 
        não ocorrência: </font> </td>
      <td colspan="2"> <textarea name="motivo" cols="40" rows="2" id="motivo"><? echo $v_motivo; ?></textarea> 
      </td>
    </tr>
    <tr bgcolor="#D5F4FF"> 
      <td> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> Anexo: 
        </font> </td>
      <td colspan="2" valign="middle"> <font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
        <?
				if(trim($v_arquivo) != "")
				{
					echo "<a href='../upload/".$v_arquivo_fisico."' class='links3' target='_blank' title='Clique para fazer download do anexo'><img src='img/btn_abrir.jpg' border='0'></a>
						  <img src='img/btn_exclusao.jpg' border='0' title='Exclui anexo' style='cursor:pointer;' onclick=\"anexoDel('".$v_arquivo_fisico."')\">   ".$v_arquivo;
				}
				else
				{
					echo "<input type='file' name='ds_arquivo' id='ds_arquivo' value=''>";
				}
			?>
        </font> </td>
    </tr>
  </table>
</form>
<?
   pg_close($db);
?>
</body>
</html>