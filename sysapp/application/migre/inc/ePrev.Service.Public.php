<?php
include_once( "ePrev.DAL.DBConnection.php" );

/**
 * Classe para Serviços de Divulgação 
 * 
 * @access public
 * @package ePrev
 * @subpackage Service
 */
class service_public 
{
    private $db;

    function service_public( $_db ) 
    {
        $this->db = $_db;
    }

    function __destruct()
    {
        $this->db = null;
    }
    
    public function participantes_LoadByRE( $cd_empresa, $cd_registro_empregado, $cd_seq_dependencia )
    {
        $ado = new ADO_participantes( $this->db );
        
        try
        {
            $sNome = $ado->loadByRE( $cd_empresa, $cd_registro_empregado, $cd_seq_dependencia );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }
        
        return $sNome;
    }

    /**
     * Carrega a entidade passada por parametro com as informações da base
     * obrigatório preenchimento dos atributos: 
     *   - $entidade->set_cd_empresa()
     *   - $entidade->set_cd_registro_empregado()
     *   - $entidade->set_seq_dependencia()
     * @param entity_participantes $entidade Por referencia, chega ao método com a PK preenchida e recebe as demais informações da base 
     */
    public function participantes_Load( entity_participantes $entidade )
    {
        $ado = new ADO_participantes( $this->db );
        
        try
        {
            $sNome = $ado->load( $entidade );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            //echo( $e->getMessage() );
        }
    }
    
    public function tipo_documentos_Load( $cd_tipo_doc )
    {
        $ado = new ADO_tipo_documentos( $this->db );
        
        try
        {
            $sNome = $ado->load( $cd_tipo_doc );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }
        
        return $sNome;
    }
    
    public function tipo_documentos_FetchAll( $nome_documento_partial )
    {
        $ado = new ADO_tipo_documentos( $this->db );
        
        try
        {
            $sNome = $ado->fetchAll( $nome_documento_partial );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }
        
        return $sNome;
    }

}

?>