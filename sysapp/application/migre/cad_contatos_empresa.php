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
<title>...:: Contato ::...</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<script src="inc/pck_funcoes.js"></script>
<script src="inc/mascara.js"></script>

<script language="JavaScript">
<!--
     function __onLoad()
     {
        atualiza_pai();
        reset_uploadfile();
     }
     
     function atualiza_pai() 
     {
	    <?if($_POST["__POST"]=="s"){?>
            opener.location.reload(true);
            window.close();
        <?}?>
	 }

    function valida_form(f)
    {
        f.__POST.value = "s";
        f.submit();
    }
     
    function anexoDel()
    {
        document.frmHabRec.path_file.value = '';
        reset_uploadfile();
    }
    
    function reset_uploadfile()
    {
        document.getElementById( 'div_com_arquivo' ).style.display = ( document.frmHabRec.path_file.value != '' )?'BLOCK':'NONE';
        document.getElementById( 'div_sem_arquivo' ).style.display = ( document.frmHabRec.path_file.value == '' )?'BLOCK':'NONE';
    }
-->
</script>
</head>
<body bgcolor="#F0E8BA" onLoad="__onLoad()">
<?
	if ($h != '') {
		if ($txt_campo == '') {
			$sql = " SELECT cd_empresa, texto_acomp, to_char(dt_contato, 'dd/mm/yyyy'), $Z, arquivo from expansao.contatos_empresa where cd_empresa=".$r." and cd_contato = ".$h;
			$rs = pg_query($db, $sql);
			if ($reg = pg_fetch_row($rs)) {
				$v_dt_previsao = trim($reg[2]);
				$v_descricao = trim($reg[1]);
                $v_arquivo = trim( $reg[4] );
			}	
		} else {
			$sql = " UPDATE expansao.contatos_empresa SET texto_acomp = '$txt_campo', dt_contato = TO_DATE('$dt_contato','DD/MM/YYYY'), arquivo = '".$path_file."' WHERE cd_empresa=".$r." AND cd_contato = ".$h;
			$rs = pg_query($db, $sql);
		}
	} else {
		if ( $txt_campo != '' ) {
			$sql = " INSERT INTO expansao.contatos_empresa( cd_empresa, texto_acomp, dt_contato, cd_responsavel, arquivo ) values ( '$r', '$txt_campo', TO_DATE('$dt_contato','DD/MM/YYYY'), $Z, '".$path_file."' ) ";
			$rs = pg_query( $db, $sql );
		}
	}	     
?>

<form name="frmHabRec" 
    method="post" 
    action="cad_contatos_empresa.php" 
    ><!-- enctype="multipart/form-data" -->
    
    <input type="hidden" name="__POST" value="" />

<?
echo "<input type='hidden' name='h' value='$h'>";
echo "<input type='hidden' name='r' value='$r'>";
?>

  <table border="0" align="center" cellpadding="0" cellspacing="0">
    <tr> 
      <td><strong><font color="#006633" size="3" face="Verdana, Arial, Helvetica, sans-serif">Contato</font></strong></td>
      <td align="right"><a href="javascript:void(0)" onclick="valida_form(document.frmHabRec);"><img src="img/btn_salvar.jpg" border="0" /></a><img src="img/btn_retorna.jpg" border="0" onClick="self.close();"></td>
    </tr>
    <tr> 
      <td colspan="2">
        <input name="dt_contato" type="text" id="dt_contato"  onKeyUp="mascara_data(this)" onBlur="verifica_data(this)"  value="<? if ($h != ''){echo $v_dt_previsao;}?>" size="12" maxlength="12">
        <font size="2" face="Verdana, Arial, Helvetica, sans-serif">&laquo;Informe a data do contato</font></td>
    </tr>
    <tr>
      <td colspan="2"> <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
        <textarea name="txt_campo" cols="50" rows="10" id="txt_campo"><? if ($h != '') {echo $v_descricao;}?></textarea>
        </font></td>
    </tr>

    <tr> 
        <td colspan="2">

            <div style="float:left; width:48%;">
                <label for="arquivo">Anexo:</label>
                <br>
                
                    <div id="div_com_arquivo" style="display:none">
                        <a href="file:///<?=$v_arquivo?>" class="links3" target="_blank"><img src="img/anexo_ver.gif" border="0" title="Visualizar anexo" style="cursor:pointer;"></a>
                        <img src="img/anexo_del.gif" border="0" title="Exclui anexo" style="cursor:pointer;" onclick="anexoDel()">
                        <?=$v_arquivo?>
                    </div>
                    
                    <div id="div_sem_arquivo" style="display:none">
                        <input type="hidden" name="path_file" id="ds_arquivo_local" value="<?=$v_arquivo?>">
                        <input type="file" name="arquivo" id="arquivo" onchange="document.getElementById('ds_arquivo_local').value=this.value;">
                    </div>
                
                    <?/*
                        if(trim($v_arquivo) != "")
                        {
                            echo "<a href='file:///".$v_arquivo."' class='links3' target='_blank'><img src='img/anexo_ver.gif' border='0' title='Visualizar anexo' style='cursor:pointer;'></a> ";
                            echo " <img src='img/anexo_del.gif' border='0' title='Exclui anexo' style='cursor:pointer;' onclick=\"anexoDel('".$v_arquivo."')\">";
                            echo " ".$v_arquivo;
                        }
                        else
                        {
                            echo '
                                    <input type="hidden" name="path_file" id="ds_arquivo_local" value="">
                                    <input type="file" name="arquivo" id="arquivo" onchange="document.getElementById(\'ds_arquivo_local\').value=this.value;">                   
                                 ';
                        }
                    */?>
                <br>        
            </div>
            <!--
            <font size="1" face="Verdana, Arial, Helvetica, sans-serif"> 
            <input name="path_file" type="hidden" value="" />
                <input type="file" name="arquivo" size="53" id="arquivo" value="Selecionar o arquivo a ser anexado" />
            </font>
            -->
        </td>
        </td>
    </tr>

  </table>
</form>
<?
   pg_close($db);
?>
<script>
    MaskInput(document.getElementById('dt_contato'), "99/99/9999");
</script>
</body>
</html>