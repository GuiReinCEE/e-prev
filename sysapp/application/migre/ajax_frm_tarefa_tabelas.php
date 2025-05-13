<?
	include_once('inc/sessao.php');
	include_once('inc/config.inc.php');
	include_once('inc/class.SocketAbstraction2.inc.php');
	
		if (strtoupper($_POST['cd_db']) == "POSTGRESQL")
		{ #POSTGRESQL
			if($_POST['ds_funcao'] == "TABELA")	
			{
				pgComboTabelas();
			} 
			elseif($_POST['ds_funcao'] == "CAMPO")	
			{
				$ar_tabela = explode(".",$_POST['cd_tabela']);
				pgComboCampos($ar_tabela);				
			}
			else
			{
				echo "
						<select style='width:440px; font-family: Verdana; font-size: 8 pt'>
							<option value=''>Selecione</option>
						</select>		
				     ";			
			}
		} 
		elseif (strtoupper($_POST['cd_db']) == "ORACLE")
		{ #ORACLE
			## RETORNA INPUTS PARA PREENCHIMENTO
			if($_POST['ds_funcao'] == "TABELA")	
			{
				oraComboTabelas();
			} 
			elseif($_POST['ds_funcao'] == "CAMPO")	
			{
				//echo "<input type='text' name='cd_campo' id='cd_campo' value='' size='70'>";
				$ar_tabela = "";
				if(trim($_POST['cd_tabela']) != "")
				{				
					$ar_tabela = explode(".",$_POST['cd_tabela']);
				}
				oraComboCampos($ar_tabela);					
			}
			else
			{
				echo "
						<select style='width:440px; font-family: Verdana; font-size: 8 pt'>
							<option value=''>Selecione</option>
						</select>		
				     ";			
			}			
		} 
		elseif (strtoupper($_POST['cd_db']) == "NOVO")
		{ #NOVA TABELA 
			## RETORNA INPUTS PARA PREENCHIMENTO
			if($_POST['ds_funcao'] == "TABELA")	
			{
				echo "<input type='text' name='cd_tabela' id='cd_tabela' value='' size='70'>";
			} 
			elseif($_POST['ds_funcao'] == "CAMPO")	
			{
				echo "<input type='text' name='cd_campo' id='cd_campo' value='' size='70'>";			
			}
			else
			{
				echo "<input type='text' value='' size='70'>";		
			}			
		}
		else 
		{
			echo "
					<select style='width:440px; font-family: Verdana; font-size: 8 pt'>
						<option value=''>Selecione</option>
					</select>		
			     ";		
		}
	
	#######  MONTA COMBO COM AS TABELAS POSTGRESQL #######
	function pgComboTabelas()
	{
		global $db;
		$qr_select = "
						SELECT n.nspname || '.' || c.relname as ds_tabela  
						  FROM pg_namespace n, 
						       pg_class c
						 WHERE n.oid     = c.relnamespace
						   AND c.relkind = 'r'
						   AND n.nspname not like 'pg\\_%'
						   AND n.nspname != 'information_schema'
						 ORDER BY UPPER(nspname), 
						          UPPER(relname)		
				     ";
		$ob_result = pg_exec($db, $qr_select);
		echo "<select name='cd_tabela' id='cd_tabela' onchange='getComboCampo(this.value);' style='width:440px; font-family: Verdana; font-size: 8 pt'>
				<option value=''>Selecione</option>
			 ";
		while ($ob_dado = pg_fetch_object($ob_result)) {
			echo "<option value='".$ob_dado->ds_tabela."'>".$ob_dado->ds_tabela."</option>";
		}
		echo "</select>";
	}

	#######  MONTA COMBO COM OS CAMPOS DA TABELA POSTGRESQL #######
	function pgComboCampos($ar_tabela)
	{
		global $db;
		$qr_select = "
						SELECT a.attname as ds_campo,
						       t.typname as ds_tipo
						  FROM pg_class as c, 
						       pg_attribute a, 
						       pg_type t,
						       pg_namespace n 
						 WHERE a.attnum > 0 
						   AND a.attrelid = c.oid 
						   AND n.nspname = '".$ar_tabela[0]."' --ESQUEMA
						   AND c.relname = '".$ar_tabela[1]."' --TABELA
						   AND a.atttypid = t.oid 
						   AND n.oid = c.relnamespace
						 ORDER BY a.attnum		
				     ";
		$ob_result = pg_exec($db, $qr_select);
		echo "<select name='cd_campo' id='cd_campo' style='width:440px; font-family: Verdana; font-size: 8 pt'>";
		if(pg_numrows($ob_result) > 0){
			while ($ob_dado = pg_fetch_object($ob_result)) {
				echo "<option value='".$ob_dado->ds_campo." (".$ob_dado->ds_tipo.")'>".$ob_dado->ds_campo." (".$ob_dado->ds_tipo.")</option>";
			}
		}
		else
		{
			echo "<option value=''>Selecione</option>";			
		}
		echo "</select>";
		
	}

	#######  MONTA COMBO COM AS TABELAS ORACLE #######
	function oraComboTabelas()
	{
		$LISTNER_IP    = SKT_IP;
		$LISTNER_PORTA = SKT_PORTA;

		$cn = new Socket();
		$cn->SetRemoteHost($LISTNER_IP);
		$cn->SetRemotePort($LISTNER_PORTA);
		$cn->SetBufferLength(262144); // 256KB
		
		if ($cn->Connect()) 
		{
			if ($cn->Error()) 
			{
				echo "ERRO LISTENER: ".$cn->GetErrStr();
				exit;
			}
			else
			{			
				$ret = $cn->Ask("lista_all_tables_php");
				$ar_tabelas = explode("|",str_replace(";","",$ret));
			}
		}
		else
		{
			echo "ERRO LISTENER: ".$LISTNER_IP." ".$LISTNER_PORTA;
			exit;
		}		
		
		$nr_end   = count($ar_tabelas);
		$nr_conta = 0;
		
		echo "<select name='cd_tabela' id='cd_tabela' onchange='getComboCampo(this.value);' style='width:440px; font-family: Verdana; font-size: 8 pt'>
				<option value=''>Selecione</option>
			 ";
		while ($nr_conta < $nr_end) 
		{
			
			echo "<option value='".str_replace(",",".",$ar_tabelas[$nr_conta])."'>".str_replace(",",".",$ar_tabelas[$nr_conta])."</option>";
			$nr_conta++;
		}
		echo "</select>";
	}	

	#######  MONTA COMBO COM OS CAMPOS DA TABELA ORACLE #######
	function oraComboCampos($ar_tabela)
	{
	    //$ar_tabela[0] -> ESQUEMA
		//$ar_tabela[1] -> TABELA
	   
		$LISTNER_IP    = SKT_IP;
		$LISTNER_PORTA = SKT_PORTA;
		
		if(trim($ar_tabela) == "")
		{
			echo "<select name='cd_campo' id='cd_campo' style='width:440px; font-family: Verdana; font-size: 8 pt'>
					 <option value=''>Selecione</option>
				  </select> 
				 ";
		}
		else
		{
			$cn = new Socket();
			$cn->SetRemoteHost($LISTNER_IP);
			$cn->SetRemotePort($LISTNER_PORTA);
			$cn->SetBufferLength(262144); // 256KB
			
			if ($cn->Connect()) 
			{
				if ($cn->Error()) 
				{
					echo "ERRO LISTENER: ".$cn->GetErrStr();
					exit;
				}
				else
				{
					$ret = $cn->Ask("get_colunas_tabela_oracle;".$ar_tabela[0].";".$ar_tabela[1]);
					$ar_campos = explode("|",str_replace(";","",$ret));
				}
			}
			else
			{
				echo "ERRO LISTENER: ".$LISTNER_IP." ".$LISTNER_PORTA;
				exit;
			}
			
			$nr_end   = count($ar_campos);
			$nr_conta = 0;
			
			echo "<select name='cd_campo' id='cd_campo' style='width:440px; font-family: Verdana; font-size: 8 pt'>";
			while ($nr_conta < $nr_end) 
			{
				echo "<option value='".str_replace(","," (",$ar_campos[$nr_conta]).")'>".str_replace(","," (",$ar_campos[$nr_conta]).")</option>";
				$nr_conta++;
			}
			echo "</select>";
		}
	}	
?>