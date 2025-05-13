<?php
/**
 * Classe para acesso a dados de divulgacao.publicoalvo 
 * 
 * @access public
 * @package ePrev
 * @subpackage ADO
 * @require ePrev.Util.Message.php
 * @require ePrev.DAL.DBConnection.php
 */
class ADO_divulgacao_publicoalvo {

    // DAL
    private $db;
    private $dal;

    function ADO_divulgacao_publicoalvo( $_db ) {
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

            SELECT cd_publicoalvo, cd_divulgacao, cd_filtro, colunas_resultado
              FROM divulgacao.publicoalvo
          ORDER BY cd_publicoalvo, cd_divulgacao, cd_filtro;

        ");
        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_divulgacao_publicoalvo.fetchAll() ao executar comando SQL de consulta.');
        }

        return $result;
    }
    
    public function fetchByDivulgacao( $_cd_divulgacao )
    {
        $this->dal->createQuery("

            SELECT cd_publicoalvo, cd_divulgacao, cd_filtro, colunas_resultado
              FROM divulgacao.publicoalvo
             WHERE cd_divulgacao = {cd_divulgacao}
          ORDER BY cd_publicoalvo, cd_divulgacao, cd_filtro;

        ");
        $this->dal->setAttribute("{cd_divulgacao}", (int)$_cd_divulgacao);
        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_divulgacao_publicoalvo.fetchByDivulgacao() ao executar comando SQL de consulta.');
        }

        return $result;
    }

    public function insert( entity_divulgacao_publicoalvo $entidade )
    {
        $bReturn = false;
        $entidade->setCd_publicoalvo( getNextval("divulgacao", "publicoalvo", "cd_publicoalvo", $this->db) );
        
        $this->dal->createQuery("

            INSERT INTO divulgacao.publicoalvo(
                        cd_publicoalvo, cd_divulgacao, cd_filtro, cd_grupo, colunas_resultado)
                VALUES ({cd_publicoalvo}, {cd_divulgacao}, {cd_filtro}, {cd_grupo}, '{colunas_resultado}');

        ");

        $this->dal->setAttribute( "{cd_publicoalvo}", (int)$entidade->getCd_publicoalvo() );
        $this->dal->setAttribute( "{cd_divulgacao}", (int)$entidade->getCd_divulgacao() );
        $this->dal->setAttribute( "{cd_filtro}", (int)$entidade->getCd_filtro() );
        $this->dal->setAttribute( "{cd_grupo}", (int)$entidade->getCd_grupo() );
        $this->dal->setAttribute( "{colunas_resultado}", $entidade->getColunas_resultado() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_divulgacao_publicoalvo.insert() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
    
    public function update( entity_divulgacao_publicoalvo $entidade )
    {
        $bReturn = false;
        
        $this->dal->createQuery("

            UPDATE divulgacao.publicoalvo
               SET cd_divulgacao={cd_divulgacao}, cd_filtro={cd_filtro}, cd_grupo={cd_grupo}
                 , colunas_resultado='{colunas_resultado}'
             WHERE cd_publicoalvo = {cd_publicoalvo};

        ");

        $this->dal->setAttribute( "{cd_divulgacao}", (int)$entidade->getCd_divulgacao() );
        $this->dal->setAttribute( "{cd_filtro}", (int)$entidade->getCd_filtro() );
        $this->dal->setAttribute( "{cd_grupo}", (int)$entidade->getCd_grupo() );
        $this->dal->setAttribute( "{colunas_resultado}", $entidade->getColunas_resultado() );
        $this->dal->setAttribute( "{cd_publicoalvo}", $entidade->getCd_publicoalvo() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_divulgacao_publicoalvo.update() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

    public function deleteByDivulgacao( $_cd_divulgacao )
    {
        $this->dal->createQuery("

            DELETE 
              FROM divulgacao.publicoalvo
             WHERE cd_divulgacao = {cd_divulgacao}

        ");
        $this->dal->setAttribute("{cd_divulgacao}", (int)$_cd_divulgacao);
        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_divulgacao_publicoalvo.deleteByDivulgacao() ao executar comando SQL de consulta. ' . $this->dal->getMessage() );
        }

        return $result;
    }

}
?>