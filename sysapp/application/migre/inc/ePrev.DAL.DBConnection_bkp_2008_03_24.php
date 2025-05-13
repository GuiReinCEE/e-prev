<?php
include_once( "ePrev.Util.Message.php" );
/*
 * Created on 23/11/2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

/**
 * Camada para acesso a dados com execuчуo de queries
 * 
 * @access public
 * @package ePrev
 * @subpackage DAL
 * @require ePrev.Util.Message.php
 */
class DBConnection extends Message {

	/**
	 * $db recebe conexуo ativa com banco de dados
     * 
     * @var pg_connect
     * @access private
	 */
	private $db;
	
	/**
	 * Query que serс formada para execuчуo
	 * 
	 * @var string
	 * @access private
	 */
	private $query = "";
	
	/**
	 * Variсvel que recebe quantidade de registros afetados 
	 * pela executaчуo de algum comando no banco de dados
	 * 
	 * @var int
	 * @access private
	 */
	private $affectedRowsCount = "";
    
    /**
     * Variсvel que recebe boolean indicando erro em operaчуo 
     * 
     * @var bool
     * @access private
     */
    private $error = false;
	
	/**
	 * TODO: Criar documentaчуo do mщtodo loadConnection()
	 */
	public function loadConnection( $_db ){
		$this->db = $_db;
		$this->addMessage("loadConnection", "");
	}
	
	/**
	 * Criaчуo da query que serс executada
	 * a query deve ser informada sendo parametrizada como exemplo abaixo
	 * 
	 * - $DBConnection->createQuery(   "  SELECT * 
	 *                                      FROM schema.table 
	 *                                     WHERE collumnA LIKE '%::Valor_1%'  "   );
	 * - $DBConnection->setAttribute(  "::Valor_1"  ,  "Carlos Moraes"  ); 
	 * - $resultset = $DBConnection->getResultset();
	 * 
	 * A dupla de dois pontos foi um exemplo hipotщtico, pode ser usada qualquer
	 * forma de identificaчуo desejada, contanto que no setAttribute o valor seja idъntico.
	 * 
	 * @param string $_query Query para ser executada a conexуo ativa
	 * @access public
	 */
	public function createQuery( $_query )
    {
		$this->query = $_query;
	}

	/**
	 * TODO: Criar documentaчуo do mщtodo setAttribute()
	 */
	public function setAttribute( $_param, $_value ){
        $this->query = str_replace($_param, addslashes($_value), $this->query);
	}
    
    /**
     * TODO: Criar documentaчуo do mщtodo setAttribute()
     */
    public function setWhere( $_value ){
        $this->query = str_replace("{WHERE}", $_value, $this->query);
    }
	
	/**
	 * Executa uma query criada em this.createQuery( string )
     * @return pg_result Resultado da execuчуo do comando criado em this.createQuery( string )
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
			// Faz roollback e lanчa o erro no relatѓrio
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
     * TODO: beginTransaction documentar esse mщtodo
     */
    public function beginTransaction()
    {
        pg_query($this->db, "BEGIN TRANSACTION");
    }
    
    /**
     * TODO: addQueryTransaction documentar esse mщtodo
     */
    public function addQueryTransaction()
    {
        //$this->addMessage("addQueryTransaction.sql", $this->query);
        $result = @pg_query($this->db, $this->query);
        if( !$result )
        {
            // Faz roollback e lanчa o erro no relatѓrio
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
     * TODO: commitTransaction documentar esse mщtodo
     */
    public function commitTransaction()
    {
        pg_query($this->db, "COMMIT TRANSACTION");
    }
    
    /**
     * TODO: rollbackTransaction documentar esse mщtodo
     */
    public function rollbackTransaction()
    {
        pg_query($this->db, "ROLLBACK TRANSACTION");
    }

	/**
	 * TODO: Criar documentaчуo para o mщtodo getResultset
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
	 * TODO: Criar documentaчуo do mщtodo getAffectedRowsCount()
	 */
	public function getAffectedRowsCount(){
		return $this->affectedRowsCount;
	}

	/**
	 * Destrutor serс usado para fechar conexѕes e dispor objetos que forem necessсrios
	 */
	function __destruct(){
		// TODO: Criar lѓgica do destrutor da classe
	}

    /**
     * TODO: Criar documentaчуo para mщtodo
     */
    public function ifBlankThen($compareValue, $returnValue) {
        if ($compareValue=="") {
			return $returnValue;
		} else {
            return $compareValue; 
        }
    }
    
    /**
     * Propriedade para indicar se houve erro em operaчѕes 
     * no mщtodo dessa classe que executa comandos SQL
     */
    public function haveError(){
        return $this->error;
    }

}

?>