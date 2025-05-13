<?php
/**
 * Classe para acesso a dados de divulgacao.divulgacao 
 * 
 * @access public
 * @package ePrev
 * @subpackage ADO
 * @require ePrev.Util.Message.php
 * @require ePrev.DAL.DBConnection.php
 */
class ADO_divulgacao_divulgacao {

    // DAL
    private $db;
    private $dal;

    function ADO_divulgacao_divulgacao( $_db ) {
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

            SELECT cd_divulgacao, nome, cd_usuario, de, assunto, texto, modelo
              FROM divulgacao.divulgacao 
          ORDER BY nome;

        ");
        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_divulgacao_divulgacao.fetchAll() ao executar comando SQL de consulta.');
        }

        return $result;
    }

    public function insert( entity_divulgacao_divulgacao $entidade )
    {
        $bReturn = false;

        $entidade->setCd_divulgacao( getNextval("divulgacao", "divulgacao", "cd_divulgacao", $this->db) );

        $this->dal->createQuery("

            INSERT INTO divulgacao.divulgacao(
                        cd_divulgacao, nome, cd_usuario, de, assunto, texto, modelo_tipo, modelo_publico)
                VALUES ({cd_divulgacao}, '{nome}', {cd_usuario}, '{de}', '{assunto}', '{texto}', {modelo_tipo}, {modelo_publico});

        ");

        $this->dal->setAttribute( "{cd_divulgacao}", (int)$entidade->getCd_divulgacao() );
        $this->dal->setAttribute( "{nome}", $entidade->getNome() );
        $this->dal->setAttribute( "{cd_usuario}", (int)$entidade->getCd_usuario() );
        $this->dal->setAttribute( "{de}", $entidade->getDe() );
        $this->dal->setAttribute( "{assunto}", $entidade->getAssunto() );
        $this->dal->setAttribute( "{texto}", $entidade->getTexto() );
        $this->dal->setAttribute( "{modelo_tipo}", (int)$entidade->getModelo_tipo() );
        $this->dal->setAttribute( "{modelo_publico}", (int)$entidade->getModelo_publico() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_divulgacao_divulgacao.insert() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }
    
    public function update( entity_divulgacao_divulgacao $entidade )
    {
        $bReturn = false;

        $this->dal->createQuery("

                    UPDATE   divulgacao.divulgacao
                       SET   nome='{nome}'
                           , cd_usuario={cd_usuario}
                           , de='{de}'
                           , assunto='{assunto}'
                           , texto='{texto}'
                           , modelo_tipo={modelo_tipo}
                           , modelo_publico={modelo_publico}
                    WHERE    cd_divulgacao={cd_divulgacao};

        ");

        $this->dal->setAttribute( "{cd_divulgacao}", (int)$entidade->getCd_divulgacao() );
        $this->dal->setAttribute( "{nome}", $entidade->getNome() );
        $this->dal->setAttribute( "{cd_usuario}", (int)$entidade->getCd_usuario() );
        $this->dal->setAttribute( "{de}", $entidade->getDe() );
        $this->dal->setAttribute( "{assunto}", $entidade->getAssunto() );
        $this->dal->setAttribute( "{texto}", $entidade->getTexto() );
        $this->dal->setAttribute( "{modelo_tipo}", (int)$entidade->getModelo_tipo() );
        $this->dal->setAttribute( "{modelo_publico}", (int)$entidade->getModelo_publico() );

        $result = $this->dal->executeQuery();

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em ADO_divulgacao_divulgacao.update() ao executar comando SQL de consulta.' . $this->dal->getMessage() . '');
        }
        else
        {
            $bReturn = true;
        }
        return $bReturn;
    }

}

?>