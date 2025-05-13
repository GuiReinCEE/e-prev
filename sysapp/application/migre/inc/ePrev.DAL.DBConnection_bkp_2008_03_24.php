<?php
include_once( "ePrev.Util.Message.php" );
/*
 * Created on 23/11/2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/**
 * Camada para acesso a dados com execu��o de queries
 * 
 * @access public
 * @package ePrev
 * @subpackage DAL
 * @require ePrev.Util.Message.php
 */
class DBConnection extends Message {

	/**
	 * $db recebe conex�o ativa com banco de dados
     * 
     * @var pg_connect
     * @access private
	 */
	private $db;
	
	/**
	 * Query que ser� formada para execu��o
	 * 
	 * @var string
	 * @access private
	 */
	private $query = "";
	
	/**
	 * Vari�vel que recebe quantidade de registros afetados 
	 * pela executa��o de algum comando no banco de dados
	 * 
	 * @var int
	 * @access private
	 */
	private $affectedRowsCount = "";
    
    /**
     * Vari�vel que recebe boolean indicando erro em opera��o 
     * 
     * @var bool
     * @access private
     */
    private $error = false;
	
	/**
	 * TODO: Criar documenta��o do m�todo loadConnection()
	 */
	public function loadConnection( $_db ){
		$this->db = $_db;
		$this->addMessage("loadConnection", "");
	}
	
	/**
	 * Cria��o da query que ser� executada
	 * a query deve ser informada sendo parametrizada como exemplo abaixo
	 * 
	 * - $DBConnection->createQuery(   "  SELECT * 
	 *                                      FROM schema.table 
	 *                                     WHERE collumnA LIKE '%::Valor_1%'  "   );
	 * - $DBConnection->setAttribute(  "::Valor_1"  ,  "Carlos Moraes"  ); 
	 * - $resultset = $DBConnection->getResultset();
	 * 
	 * A dupla de dois pontos foi um exemplo hipot�tico, pode ser usada qualquer
	 * forma de identifica��o desejada, contanto que no setAttribute o valor seja id�ntico.
	 * 
	 * @param string $_query Query para ser executada a conex�o ativa
	 * @access public
	 */
	public function createQuery( $_query )
    {
		$this->query = $_query;
	}

	/**
	 * TODO: Criar documenta��o do m�todo setAttribute()
	 */
	public function setAttribute( $_param, $_value ){
        $this->query = str_replace($_param, addslashes($_value), $this->query);
	}
    
    /**
     * TODO: Criar documenta��o do m�todo setAttribute()
     */
    public function setWhere( $_value ){
        $this->query = str_replace("{WHERE}", $_value, $this->query);
    }
	
	/**
	 * Executa uma query criada em this.createQuery( string )
     * @return pg_result Resultado da execu��o do comando criado em this.createQuery( string )
	 */
	public function executeQuery(){

		$affectedRows = 0;
		// Abre transacao com o bd
		pg_query($this->db, "BEGIN TRANSACTION"); 

		// Executa a query
		//$this->addMessage("executeQuery.sql", $this->query);
		$result = @pg_query($this->db, $this->query);

		// Testa o resultado
		if(!$result)
		{
			// Faz roollback e lan�a o erro no relat�rio
			$ds_erro = pg_last_error($this->db);
			if($ds_erro)
			{
				$ds_erro = str_replace( "ERROR:", "", $ds_erro );
				$this->addMessage( "executeQuery.Erro", $ds_erro );
                $this->error = true;
    			pg_query($this->db, "ROLLBACK TRANSACTION");
			}
            else
            {
    			pg_query($this->db, "ROLLBACK TRANSACTION");
            }
		}
		else
		{
			// Comita dados no bd
			pg_query($this->db, "COMMIT TRANSACTION");
			$this->addMessage( "executeQuery.Commit", "OK" );

			// Verifica quantidade de registros afetados
			$affectedRows = pg_affected_rows($result);
            $this->error = false;
		}

		$this->addMessage("executeQuery.affectedRows", $affectedRows);
		$this->affectedRowsCount = $affectedRows; 
		return $result;

	}

    /**
     * TODO: beginTransaction documentar esse m�todo
     */
    public function beginTransaction()
    {
        pg_query($this->db, "BEGIN TRANSACTION");
    }
    
    /**
     * TODO: addQueryTransaction documentar esse m�todo
     */
    public function addQueryTransaction()
    {
        //$this->addMessage("addQueryTransaction.sql", $this->query);
        $result = @pg_query($this->db, $this->query);
        if( !$result )
        {
            // Faz roollback e lan�a o erro no relat�rio
            $ds_erro = pg_last_error($this->db);
            if($ds_erro)
            {
                $ds_erro = str_replace( "ERROR:", "", $ds_erro );
                $this->addMessage( "addQueryTransaction.Erro", $ds_erro );
                $this->error = true;
            }
        }
        else
        {
            $this->addMessage( "addQueryTransaction", "OK" );
            $this->error = false;
        }
    }
    
    /**
     * TODO: commitTransaction documentar esse m�todo
     */
    public function commitTransaction()
    {
        pg_query($this->db, "COMMIT TRANSACTION");
    }
    
    /**
     * TODO: rollbackTransaction documentar esse m�todo
     */
    public function rollbackTransaction()
    {
        pg_query($this->db, "ROLLBACK TRANSACTION");
    }

	/**
	 * TODO: Criar documenta��o para o m�todo getResultset
	 */
	public function getResultset(){

		$affectedRows = 0;
		$result = @pg_query($this->db, $this->query);

		if (!$result)
		{
			$erro = pg_last_error($this->db);
			if ($erro)
			{
				$this->addMessage( "getResultset.Erro", $erro );
				//$this->addMessage( "getResultset.Erro.Query", $this->query );
                $this->error = true;
			}
			else
			{
				//$this->addMessage( "getResultset.NoResult.NoError.Query", $this->query );
	            $this->error = false;
    		}
		}
		else
		{
			//$this->addMessage( "getResultset.WithResult.Query", $this->query );
			$affectedRows = pg_num_rows($result);
            $this->error = false;
		}

		$this->affectedRowsCount = $affectedRows;
		return $result;
	}

	/**
	 * TODO: Criar documenta��o do m�todo getAffectedRowsCount()
	 */
	public function getAffectedRowsCount(){
		return $this->affectedRowsCount;
	}

	/**
	 * Destrutor ser� usado para fechar conex�es e dispor objetos que forem necess�rios
	 */
	function __destruct(){
		// TODO: Criar l�gica do destrutor da classe
	}

    /**
     * TODO: Criar documenta��o para m�todo
     */
    public function ifBlankThen($compareValue, $returnValue) {
        if ($compareValue=="") {
			return $returnValue;
		} else {
            return $compareValue; 
        }
    }
    
    /**
     * Propriedade para indicar se houve erro em opera��es 
     * no m�todo dessa classe que executa comandos SQL
     */
    public function haveError(){
        return $this->error;
    }

}

?>