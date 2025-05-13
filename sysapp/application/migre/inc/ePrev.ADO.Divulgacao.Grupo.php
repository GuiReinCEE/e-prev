<?php
/**
 * Classe para acesso a dados de divulgacao.grupo 
 * 
 * @access public
 * @package ePrev
 * @subpackage ADO
 * @require ePrev.Util.Message.php
 * @require ePrev.DAL.DBConnection.php
 */
class ADO_divulgacao_grupo {

    // DAL
    private $db;
    private $dal;

    function ADO_divulgacao_grupo( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function fetchAll()
    {
        $this->dal->createQuery("

            SELECT cd_grupo, nome, ordem, pg_function, dt_exclusao 
              FROM divulgacao.grupo 
             WHERE dt_exclusao IS NULL 
               AND pg_function IS NOT NULL
          ORDER BY ordem, nome;

        ");
        $result = $this->dal->getResultset();
        
        if ($this->dal->haveError()) {
			throw new Exception('Erro em ADO_divulgacao_grupo.fetchAll() ao executar comando SQL de consulta.');
		}
        
        return $result;
    }
    
    public function fetchByPGFunction( $_cd_grupo, $_cd_filtro_in ){
        
        $result = null;
          
        // Resgata do banco o grupo
        $this->dal->createQuery("
            SELECT pg_function 
              FROM divulgacao.grupo 
             WHERE cd_grupo = {cd_grupo};
        ");
        $this->dal->setAttribute("{cd_grupo}", (int)$_cd_grupo);
        $resultG = $this->dal->getResultset(); 
        
        if ($this->dal->haveError) {
            throw new Exception('Erro em ADO_divulgacao_grupo.fetchByPGFunction() ao executar comando SQL de consulta.#1');
        }
        
        // Resgata do banco os filtros
        $this->dal->createQuery("
            SELECT cd_filtro 
              FROM divulgacao.filtro
             WHERE cd_grupo = {cd_grupo}
          ORDER BY ordem ASC
        ");
        $this->dal->setAttribute("{cd_grupo}", (int)$_cd_grupo);
        $resultF = $this->dal->getResultset();
            
        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_divulgacao_grupo.fetchByPGFunction() ao executar comando SQL de consulta.#2');
        }
        
        // Monta string da funчуo PG que irс montar a query do 
        // resultado principal deste mщtodo
        
        // Resgata nome da funчуo
        if ($rowG = pg_fetch_array($resultG)) {
            $pg_function = "SELECT ".$rowG["pg_function"]."( {args} )";
		}

        $virgula = "";
        // Resgata filtros setando os que foram marcados (indicados no parametro $_cd_filtros_in)
        while ( $rowF = pg_fetch_array($resultF) ) {
			if (  strpos( ",".$_cd_filtro_in.",", ",".$rowF["cd_filtro"]."," )>-1 ) {
                $args .= $virgula . "1";
			} else {
                $args .= $virgula . "0";
            }
            $virgula = ", ";
		}
        $pg_function = str_replace( "{args}", $args, $pg_function );

        // Query do resultado principal
        $this->dal->createQuery( $pg_function );
        $resultQ = $this->dal->getResultset();

        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_divulgacao_grupo.fetchByPGFunction() ao executar comando SQL de consulta. #3');
        }

        if ($this->dal->getAffectedRowsCount()>0) {

            $rowQ = pg_fetch_row($resultQ);
            $sqlDef = $rowQ[0];

            $this->dal->createQuery( $sqlDef );
            $result = $this->dal->getResultset();

            if ( $this->dal->haveError() ) {
                echo($this->dal->getMessage());
                throw new Exception('Erro em ADO_divulgacao_grupo.fetchByPGFunction() ao executar comando SQL de consulta. #4');
            }

		}

        return $result;

    }

}

?>