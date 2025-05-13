<?php
/**
 * Classe para acesso a dados de tipo_documentos 
 * 
 * @access public
 * @package ePrev
 * @subpackage ADO
 * @require ePrev.Util.Message.php
 * @require ePrev.DAL.DBConnection.php
 */
class ADO_tipo_documentos {

    // DAL
    private $db;
    private $dal;

    function ADO_tipo_documentos( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function load( $cd_tipo_doc )
    {
        $sRet = "";
        $this->dal->createQuery("

            SELECT *
              FROM tipo_documentos
             WHERE cd_tipo_doc = {cd_tipo_doc};

        ");

        $this->dal->setAttribute( "{cd_tipo_doc}", (int)$cd_tipo_doc );
        $result = $this->dal->getResultset();

        if ( $result ) 
        {
			if ( $row = pg_fetch_array($result) )
            {
                $sRet = $row["nome_documento"];
            }
		}

        if ( $this->dal->haveError() ) 
        {
            throw new Exception( "Erro em ADO_tipo_doc.load_tipo_doc($cd_empresa, $cd_registro_empregado, $cd_seq_dependencia) ao executar comando SQL de consulta. " . $this->dal->getMessage() );
        }

        return $sRet;
    }
    
    public function fetchAll( $nome_documento_partial )
    {
        $this->dal->createQuery("

            SELECT    cd_tipo_doc
                    , nome_documento
              FROM tipo_documentos
             WHERE upper(nome_documento) LIKE upper('%{nome_documento}%')
          ORDER BY nome_documento

        ");
        $this->dal->setAttribute("{nome_documento}", $nome_documento_partial);
        $result = $this->dal->getResultset();

        if ($this->dal->haveError()) {
            throw new Exception('Erro em ADO_tipo_documentos.fetchAll() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

        return $result;
    }

}

?>