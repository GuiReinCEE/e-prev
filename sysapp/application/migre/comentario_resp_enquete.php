<?
   include_once('inc/sessao.php');
   include_once('inc/conexao.php');
// ------------------------------------------------------
   if (isset($_REQUEST['cd_enquete'])) {
      $cd_enquete = $_REQUEST['cd_enquete'];
   }
   else {
      $cd_enquete = $_POST['cd_enquete'];
   }
   if (isset($_REQUEST['cd_questao'])) {
      $cd_questao = $_REQUEST['cd_questao'];
   }
   else {
      $cd_questao = $_POST['cd_questao'];
   }
// ------------------------------------------------------
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<link href="main.css" rel="stylesheet" type="text/css">
<head>
  <title>...:: Comentários desta questão ::...</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body bgcolor="#C5F5C5">
  <table border="0" align="center" cellpadding="5" cellspacing="0">
    <tr bgcolor="#0046ad" height="33"> 
      <td class="cabecalho">Comentários da questão:</td>
    </tr>
    <tr bgcolor="#0046ad"> 
      <td class="cabecalho" height="33">

  <?

	$sql = "select texto from projetos.enquete_perguntas where cd_enquete=".$cd_enquete." and cd_pergunta=".$cd_questao;
	$rs = pg_query($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		echo $reg["texto"];
	}

  ?> 

	  </td>
    </tr>

    <tr> 
      <td class="texto1">
  <?
	$sql = "
            SELECT descricao 
              FROM projetos.enquete_resultados 
             WHERE cd_enquete = " . $cd_enquete . " 
               AND questao = 'R_" . $cd_questao . "'
        {ANDWHERE}
    ";
    $where = "";
    if ($_SESSION["filtro_data_inicio"]!="")
    {
        $where = "
                   AND DATE_TRUNC('day', dt_resposta) BETWEEN TO_DATE('" . $_SESSION["filtro_data_inicio"] . "', 'DD/MM/YYYY')
                                                          AND TO_DATE('" . $_SESSION["filtro_data_fim"] . "', 'DD/MM/YYYY')
        ";
    }
    $sql = str_replace( "{ANDWHERE}", $where, $sql );
	$rs = pg_query($db, $sql);
	while ($reg = pg_fetch_array($rs)) {
		if ($reg['descricao'] != '') {
			echo $reg['descricao']."<hr>";
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