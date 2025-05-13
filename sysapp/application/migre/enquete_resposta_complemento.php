<?
    include_once('inc/sessao.php');
    include_once('inc/conexao.php');



?>
<html>
<head>
	<title>...:: Complemento da resposta ::...</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<script type='text/javascript' src='inc/sort_table/sortabletable.js'></script>
    <script src="inc/jquery-1.3.2.min.js"></script>
<script src="inc/jeip.js"></script>
	<link type='text/css' rel='StyleSheet' href='inc/sort_table/sortabletable.css'>
    <link type='text/css' rel='StyleSheet' href='main.css'>
</head>
<body>
	<table class="sort-table" id="table-1" align="center" width="100%" cellspacing="2" cellpadding="2">
    	<thead>
		<tr>
			<td><b>Nº</b></td>
			<td><b>Resposta</b></td>
		</tr>
    	</thead>
	<tbody>
		<?
			#### COMPLEMENTO ####
			$qr_sql = "
						SELECT MD5(CAST(cd_enquete AS TEXT) || CAST(cd_agrupamento AS TEXT) || CAST(questao AS TEXT) || CAST(ip AS TEXT)) AS cd_resposta,
                               complemento
						  FROM projetos.enquete_resultados	
						 WHERE cd_enquete = ".$_REQUEST['cd_enquete']."
						   AND questao    = 'R_".$_REQUEST['cd_questao']."'
						   AND valor      = ".$_REQUEST['cd_resp']."
					   {WHERE} 
						   AND complemento IS NOT NULL	
						 ORDER BY dt_resposta  
					  ";
			$where = "";
			if ($_REQUEST['dt_ini'] != "") 
			{
				$where = "
				   AND DATE_TRUNC('day', dt_resposta) BETWEEN TO_DATE('".$_REQUEST['dt_ini']."', 'DD/MM/YYYY')
														  AND TO_DATE('".$_REQUEST['dt_fim']."', 'DD/MM/YYYY')
				";
			}
			$qr_sql = str_replace("{WHERE}", $where, $qr_sql);
			$ob_resul = pg_query($db, $qr_sql);			
			$nr_conta = 0;
			while ($ar_reg = pg_fetch_array($ob_resul)) 
			{
				$nr_conta++;
				echo '
						<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
							<td align="center">'.$nr_conta.'</td>
							<td>
                                <div id="'.$ar_reg['cd_resposta'].'" title="Clique para editar" style="padding-left: 5px; padding-right: 5px; width: 100%; line-height: 12pt;">'.$ar_reg['complemento'].'</div>	
                                <script>
                                        $("#'.$ar_reg['cd_resposta'].'").eip("enquete_resposta_complemento_grava.php", 
                                        { 
                                            form_type: "textarea",
                                            savebutton_text		: "Salvar",
                                            savebutton_class	: "botao",
                                            cancelbutton_text	: "Cancelar",
                                            cancelbutton_class	: "botao_disabled"						
                                        } );
                                </script>
                            </td>
						</tr>
				     ';
			}
		?>
	</tbody>
	</table>
	<script>
		var ob_resul = new SortableTable(document.getElementById("table-1"),["Number", "CaseInsensitiveString"]);
		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
				addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
			}
		};
		ob_resul.sort(0, false);
	</script>
		
</body>
</html>