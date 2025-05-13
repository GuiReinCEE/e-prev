<?php
	#### FUNCAO QUE RETORNA O PROXIMO VALOR DE UM CAMPO USANDO A SEQUENCIA ####
	// Retorna zero quando ocorrer algum erro 
	function getNextval($ds_esquema, $ds_tabela, $ds_campo, $ob_conexao)
	{
		/*
		$qr_sequence = "
						SELECT n.nspname || '.' || REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(d.adsrc,'nextval',''),')',''),'(',''),'''',''),'::text',''),'::regclass',''),n.nspname || '.','') as ds_sequence
						  FROM pg_class as c,
						       pg_attribute a,
						       pg_type t,
						       pg_namespace n,
						       pg_attrdef d
						 WHERE a.attnum > 0
						   AND a.attrelid = c.oid
						   AND a.atttypid = t.oid
						   AND n.oid = c.relnamespace
						   AND d.adnum  = a.attnum
						   AND d.adrelid = c.oid
						   AND d.adsrc like 'nextval%'
						   AND n.nspname = '".$ds_esquema."' 
						   AND c.relname = '".$ds_tabela."' 
						   AND a.attname = '".$ds_campo."' 
		               ";
		*/
		$qr_sequence = "SELECT pg_get_serial_sequence('".$ds_esquema." .".$ds_tabela."', '".$ds_campo."' ) AS ds_sequence;";
		
		$ob_resul = pg_query($ob_conexao, $qr_sequence);
		if($ob_resul)
		{		
			$ob_seq   = pg_fetch_object($ob_resul); 		
			
			if(trim($ob_seq->ds_sequence != ""))
			{
				pg_query($ob_conexao,"BEGIN TRANSACTION");
				$qr_nextval = "
							  SELECT nextval('".$ob_seq->ds_sequence."') AS nr_codigo;
							  ";
				$ob_resul = @pg_query($ob_conexao, $qr_nextval);
				if(!$ob_resul)
				{
					pg_query($ob_conexao,"ROLLBACK TRANSACTION");
					return 0;
				}			
				pg_query($ob_conexao,"COMMIT TRANSACTION"); 
				$ob_val   = pg_fetch_object($ob_resul);
				
				return $ob_val->nr_codigo;
			}
			else
			{
				return 0;
			}
		}
		else
		{
			return 0;
		}			
	}
?>