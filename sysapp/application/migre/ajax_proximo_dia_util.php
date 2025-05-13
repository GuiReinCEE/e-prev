<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	
	if(trim($_POST['dt_data']) == "")
	{
		$_POST['dt_data'] = date("d/m/Y");
	}

	$fl_dia_util = true;
	
	// Parametro tipo_manutencao, se preenchido e na base não tiver o valor definido 
	// deve imprimir um valor em branco na tela e não calcular datas!
	// campo valor1 define se quantidade de dias é util ou corrido
	if(trim($_POST['tipo_manutencao']) != "")
	{
		$qr_sql = "
					SELECT CASE WHEN divisao = 'GB' AND EXTRACT(DAY FROM CURRENT_DATE) BETWEEN 14 AND 21 --PERÍODO DA FOLHA
					            THEN '24/'||TO_CHAR(CURRENT_DATE,'MM/YYYY')
					            ELSE NULL
					       END AS dt_data_limite,
 				           valor AS qt_dias,
						   CASE WHEN COALESCE(valor1,0) = 0
						        THEN 'S'
								ELSE 'N'
						   END AS fl_dia_util
					  FROM public.listas 
					 WHERE codigo      = '".pg_escape_string($_POST['tipo_manutencao'])."' 
					   AND categoria   IN ('TPMN','TPAT')
					   AND dt_exclusao IS NULL;
		           ";
		$ob_resul = pg_query($db, $qr_sql);
		$ar_reg   = pg_fetch_array($ob_resul);
        
        
		if( intval($ar_reg['qt_dias'])>0 )
		{
			$_POST['qt_dias'] = intval($ar_reg['qt_dias']);
			
			if(trim($ar_reg['dt_data_limite']) != "")
			{
				$_POST['dt_data'] = $ar_reg['dt_data_limite'];
			}
			
			if(trim($ar_reg['fl_dia_util']) == "N")
			{
				$fl_dia_util = false;
			}
		}
		else
		{
			echo "";
			exit;
		}
	}

	// Chegando aqui, o parametro tipo_manutencao ou não foi preenchido, 
	// ou uma quantidade de dias foi encontrado para o tipo_manutencao, 
	// desta forma deve seguir e calcular uma data

	if(trim($_POST['qt_dias']) == "")
	{
		$_POST['qt_dias'] = 1;
	}

	if($fl_dia_util)
	{
		$qr_sql = "
					SELECT TO_CHAR(dia_util,'DD/MM/YYYY') AS dt_util 
					  FROM funcoes.dia_util('DEPOIS', TO_DATE('".$_POST['dt_data']."','DD/MM/YYYY'), ".$_POST['qt_dias'].")
				  ";
	}
	else
	{
		$qr_sql = "
                    SELECT TO_CHAR(funcoes.dia_util('DEPOIS',(TO_DATE('".$_POST['dt_data']."','DD/MM/YYYY') + ".$_POST['qt_dias']."), 1),'DD/MM/YYYY') AS dt_util					
				  ";	
	}
	
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg   = pg_fetch_array($ob_resul);
	echo $ar_reg['dt_util'];
?>