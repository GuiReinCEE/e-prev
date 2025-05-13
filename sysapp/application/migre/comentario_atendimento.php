<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
// ------------------------------------------------------
   if (isset($_REQUEST['cd_atendimento'])) {
      $cd_atendimento = $_REQUEST['cd_atendimento'];
   }
   else {
      $cd_atendimento = $_REQUEST['cd_atendimento'];
   }
// ------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<link href="main.css" rel="stylesheet" type="text/css">
<head>
  <title>...:: Comentários deste atendimento ::...</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#C5F5C5">
  <table border="0" align="center" cellpadding="5" cellspacing="0">
    <tr bgcolor="#006633" height="33"> 
      <td class="cabecalho">Comentários do atendimento:</td>
    </tr>
    <tr bgcolor="#006633"> 
      <td class="cabecalho" height="33">
  <?
	echo $cd_atendimento;
  ?> 
	  </td>
    </tr>

    <tr> 
		<td class="texto1">
		<?
			$sql = "
					SELECT a.obs,
					       ae.texto_encaminhamento,
					       ao.texto_observacao
					  FROM projetos.atendimento a 
					  LEFT JOIN projetos.atendimento_encaminhamento ae
					    ON ae.cd_atendimento = a.cd_atendimento  
					  LEFT JOIN projetos.atendimento_observacao ao
					    ON ao.cd_atendimento = a.cd_atendimento  
					 WHERE a.cd_atendimento =".$cd_atendimento;
					 
			$ob_resul = pg_query($db, $sql);
			while ($ar_reg = pg_fetch_array($ob_resul)) 
			{
				if ($ar_reg['obs'] != '') 
				{
					echo str_replace(chr(10), '<br>', $ar_reg['obs'])."<hr>";
				}
				
				if ($ar_reg['texto_observacao'] != '') 
				{
					echo str_replace(chr(10), '<br>', $ar_reg['texto_observacao'])."<hr>";
				}

				if ($ar_reg['texto_encaminhamento'] != '') 
				{
					echo str_replace(chr(10), '<br>', $ar_reg['texto_encaminhamento'])."<hr>";
				}		
			}
		?> 
		</td>
    </tr>
  </table>

<?
   pg_close($db);
?>
</body>
</html>