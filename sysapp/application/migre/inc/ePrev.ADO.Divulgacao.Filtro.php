<?php
/**
 * Classe para acesso a dados de divulgacao.filtro 
 * 
 * @access public
 * @package ePrev
 * @subpackage ADO
 * @require ePrev.Util.Message.php
 * @require ePrev.DAL.DBConnection.php
 */
class ADO_divulgacao_filtro {

    // DAL
    private $db;
    private $dal;

    function ADO_divulgacao_filtro( $_db ) {
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

            SELECT cd_filtro, cd_grupo, nome, ordem, dt_exclusao 
              FROM divulgacao.filtro 
             WHERE dt_exclusao IS NULL 
          ORDER BY ordem, nome;

        ");
        $result = $this->dal->getResultset();
        
        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_divulgacao_filtro.fetchAll() ao executar comando SQL de consulta.');
        }
        
        return $result;
    }
    
    public function fetchByGrupo( $_cd_grupo )
    {
        $this->dal->createQuery("

            SELECT cd_filtro, cd_grupo, nome, ordem, dt_exclusao 
              FROM divulgacao.filtro 
             WHERE dt_exclusao IS NULL
               AND cd_grupo = {cd_grupo} 
          ORDER BY ordem, nome;

        ");
        $this->dal->setAttribute( "{cd_grupo}", (int)$_cd_grupo );
        $result = $this->dal->getResultset();
        
        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_divulgacao_filtro.fetchAll() ao executar comando SQL de consulta.');
        }
        
        return $result;
    }

}

?>