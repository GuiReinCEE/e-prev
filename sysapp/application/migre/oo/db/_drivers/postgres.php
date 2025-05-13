<?php
class postgres extends DatabaseSuper implements i_database
{
	// Conexгo
	function connect()
	{
		$this->db = pg_connect('
			host='.$this->host.'
			port='.$this->port.'
			dbname='.$this->base.'
			user='.$this->user
		);
	}

	/**
	 * Criaзгo da query, setando os parametros
	 * 
	 * @param string $key
	 * 
	 * @param string $value
	 * 
	 * @param array $options Para tratamentos especiais em parametros
	 * 
	 * 		$options['is_date'] : indica que o $value se trata de data ptbr e deve ter o formato modificado para yyyy-mm-dd
	 * 		$options['use_null'] : indica que o $value se estiver em branco deve ser substituнdo por NULL e se preenchido deve ser devolvido entre apуstofres.
	 * 
	 */
	function setParameter($key, $value, $options=array())
	{
		if($options['is_date'])
		{
			if($value!='')
			{
				$a = explode("/", $value);
				$value = $a[2] . '-' . $a[1] . '-' . $a[0];
			}
		}

		if($options['use_null'])
		{
			if($value=="")
			{
				$this->sql = str_replace( $key, "null", $this->sql);
			}
			else
			{
				$this->sql = str_replace( $key, "'" . $this->escape($value) . "'", $this->sql );
			}
		}
		else
		{
			$this->sql = str_replace( $key, $this->escape($value), $this->sql );
		}
	}

	/**
	 * Executa uma instruзгo SQL sem retorno previsto
	 * 
	 * @return Resultado da execuзгo da consulta pelo metodo pg_query()
	 */
	function execute($log=true)
	{
		$affectedRows = 0;
		pg_query($this->db, "BEGIN TRANSACTION"); 
		
		//$this->addMessage("postgres.execute():sql", $this->sql);
		$result = @pg_query($this->db, $this->sql);

		if(!$result)
		{
			$ds_erro = pg_last_error($this->db);
			if($ds_erro)
			{
				$ds_erro = str_replace( "ERROR:", "", $ds_erro);
                $this->erro = true;
    			pg_query($this->db, "ROLLBACK TRANSACTION");
				
    			$this->addMessage("postgres.execute():sql", $this->sql, 'error');
				$this->addMessage( "postgres.execute():Erro", $ds_erro, 'error' );
    			$this->addMessage( "postgres.execute():", "ROLLBACK TRANSACTION", 'error' );
			}
            else
            {
    			pg_query($this->db, "ROLLBACK TRANSACTION");
            	
    			$this->addMessage("postgres.execute():sql", $this->sql);
            	$this->addMessage( "postgres.execute():", "Sem Resultado, Sem Erro" );
    			$this->addMessage( "postgres.execute():", "ROLLBACK TRANSACTION", 'error' );
            }
		}
		else
		{
			pg_query($this->db, "COMMIT TRANSACTION");
            $this->erro = false;
			
            $this->addMessage( "postgres.execute():sql", $this->sql );
			$this->addMessage( "postgres.execute():", "COMMIT TRANSACTION" );
			$this->addMessage( "postgres.execute():affectedRows", pg_affected_rows($result) );
		}

		if($log) $this->log( $this->erro, $this->getMessage() );
		return $result;
	}

	private function log( $erro, $q )
	{
		$local = $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
		$tipo = ($erro)?"ERRO":"EXECUTADO";
		@pg_query($this->db,"INSERT INTO projetos.log (tipo, local, descricao, dt_cadastro) 
			VALUES ('" . $tipo . "', '" . pg_escape_string($local) . "', '" . pg_escape_string($q) . "', CURRENT_TIMESTAMP);");
	}

	/**
	 * 
	 * @return array(pg_fetch_all($result)) Coleзгo com todo o resultado da query
	 */
	function get($log=false)
	{
		$result = @pg_query($this->sql);

		if (!$result)
		{
			$erro = pg_last_error($this->db);
			if ($erro)
			{
				$this->addMessage( "postgres.get():Erro", $erro, 'error' );
				$this->addMessage( "postgres.get():Erro.SQL", $this->sql, 'error' );
                $this->erro = true;
			}
			else
			{
				$this->addMessage( "postgres.get():NoResult.NoError.SQL", $this->sql );
	            $this->erro = false;
    		}
		}
		else
		{
			$this->addMessage( "postgres.get():WithResult.SQL", $this->sql );
			$this->addMessage( "postgres.get():Count", pg_num_rows($result) );
            $this->erro = false;
		}

		if( ! $this->erro )
		{
			if(pg_num_rows($result)>0)
			{
				$all = pg_fetch_all($result);
			}
			else
			{
				$all = array();
			}
		}
		else
		{
			$all = array();
		}
		
		if($log) $this->log( $this->erro, $this->getMessage() );
		return $all;
	}

	/**
	 * 
	 * @return array Array com colunas da query da primeira linha retornada
	 */
	function getFirst()
	{
		// TODO: Implements
	}

	/**
	 * 
	 * @return boolean
	 */
	function haveError()
	{
		return $this->erro;
	}

	/**
	 * 
	 */
	function escape($value)
	{
		return pg_escape_string($value);
	}
	
	/**
	 * FUNCAO QUE RETORNA O PROXIMO VALOR DE UM CAMPO USANDO A SEQUENCIA
	 * Retorna zero quando ocorrer algum erro
	 * 
	 * @param string $value Informaзгo concatenada "esquema.tabela.campo"
	 * por exemplo cenario.cenario_edicao.cd_cenario_edicao
	 */
	function newId($value)
	{
		$config = explode( ".", $value );

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
						   AND n.nspname = '".$config[0]."' 
						   AND c.relname = '".$config[1]."' 
						   AND a.attname = '".$config[2]."' 
		               ";
		$ob_resul = pg_query($this->db, $qr_sequence);
		if($ob_resul)
		{		
			$ob_seq   = pg_fetch_object($ob_resul); 		

			if(trim($ob_seq->ds_sequence != ""))
			{
				pg_query($this->db,"BEGIN TRANSACTION");
				$qr_nextval = "
							  SELECT nextval('".$ob_seq->ds_sequence."') AS nr_codigo;
							  ";
				$ob_resul = @pg_query($this->db, $qr_nextval);
				if(!$ob_resul)
				{
					pg_query($this->db,"ROLLBACK TRANSACTION");
					return 0;
				}			
				pg_query($this->db,"COMMIT TRANSACTION"); 
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
}
?>