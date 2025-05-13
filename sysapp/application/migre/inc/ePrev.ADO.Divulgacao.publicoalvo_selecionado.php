<?php
/**
 * Classe para acesso a dados de divulgacao.publicoalvo_selecionado 
 * 
 * @access public
 * @package ePrev
 * @subpackage ADO
 * @require ePrev.Util.Message.php
 * @require ePrev.DAL.DBConnection.php
 */
class ADO_divulgacao_publicoalvo_selecionado {

    // DAL
    private $db;
    private $dal;

    function ADO_divulgacao_publicoalvo_selecionado( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function fetchAll()
    {
        /*$this->dal->createQuery("

            SELECT cd_publicoalvo, cd_divulgacao, cd_filtro
              FROM divulgacao.publicoalvo
          ORDER BY cd_publicoalvo, cd_divulgacao, cd_filtro;

        ");
        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_divulgacao_publicoalvo_selecionado.fetchAll() ao executar comando SQL de consulta.');
        }

        return $result;*/
        return true;
    }
    
    public function insert( entity_divulgacao_publicoalvo_selecionado $entidade )
    {
        $bReturn = false;
        $entidade->setcd_publicoalvo_selecionado( getNextval("divulgacao", "publicoalvo_selecionado", "cd_publicoalvo_selecionado", $this->db) );
        
        $this->dal->createQuery("

            INSERT INTO divulgacao.publicoalvo_selecionado(
                        cd_publicoalvo_selecionado, nome, email, cd_divulgacao, cd_registro_empregado, 
                        cd_empresa, seq_dependencia, cd_publicoalvo)
                VALUES ({cd_publicoalvo_selecionado}, '{nome}', '{email}', {cd_divulgacao}, {cd_registro_empregado}, 
                        {cd_empresa}, {seq_dependencia}, {cd_publicoalvo});

        ");

        $this->dal->setAttribute( "{cd_publicoalvo_selecionado}", (int)$entidade->getcd_publicoalvo_selecionado() );
        $this->dal->setAttribute( "{nome}", $entidade->getnome() );
        $this->dal->setAttribute( "{email}", $entidade->getemail() );
        $this->dal->setAttribute( "{cd_divulgacao}", (int)$entidade->getcd_divulgacao() );
        $this->dal->setAttribute( "{cd_registro_empregado}", (int)$entidade->getcd_registro_empregado() );
        $this->dal->setAttribute( "{cd_empresa}", (int)$entidade->getcd_empresa() );
        $this->dal->setAttribute( "{seq_dependencia}", (int)$entidade->getseq_dependencia() );
        $this->dal->setAttribute( "{cd_publicoalvo}", (int)$entidade->getcd_publicoalvo() );
        
        $result = $this->dal->executeQuery();
        
        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_divulgacao_publicoalvo_selecionado.insert() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
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
              FROM divulgacao.publicoalvo_selecionado
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