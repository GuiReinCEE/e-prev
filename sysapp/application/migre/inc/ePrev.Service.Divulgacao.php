<?php
include_once( "ePrev.DAL.DBConnection.php" );

/**
 * Classe para Serviços de Divulgação 
 * 
 * @access public
 * @package ePrev
 * @subpackage Service
 */
class service_divulgacao 
{
    private $db;

    function service_divulgacao( $_db ) 
    {
        $this->db = $_db;
    }

    function __destruct()
    {
        $this->db = null;
    }
    
    public function save( entity_divulgacao_divulgacao $entidade)
    {
        $ado = new ADO_divulgacao_divulgacao( $this->db );
        
        try
        {
            if ($entidade->getCd_divulgacao()=="0") 
            {
                $bResult = $ado->insert( $entidade );
                $ado = null;
			}
            else
            {
                $bResult = $ado->update( $entidade );
                $ado = null;
            }
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() ); 
        }
        
        return $bResult;
    }
    
    public function deletePublicoAlvoByDivulgacao( $_cd_divulgacao )
    {
        $ado = new ADO_divulgacao_publicoalvo( $this->db );
        
        try
        {
            $bResult = $ado->deleteByDivulgacao( $_cd_divulgacao );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() );
        }
        
        return $bResult;
    }
    public function savePublicoAlvo( entity_divulgacao_publicoalvo $entidade )
    {
        $ado = new ADO_divulgacao_publicoalvo( $this->db );
        
        try
        {
            if ($entidade->getCd_publicoalvo()=="" || $entidade->getCd_publicoalvo()=="0") 
            {
                $bResult = $ado->insert( $entidade );
			}
            else
            {
                $bResult = $ado->update( $entidade );
            }
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() );
        }
        
        return $bResult;
    }

    public function deletePublicoAlvoSelecionadoByDivulgacao( $_cd_divulgacao )
    {
        $ado = new ADO_divulgacao_publicoalvo_selecionado( $this->db );

        try
        {
            $bResult = $ado->deleteByDivulgacao( $_cd_divulgacao );
            $ado = null;
        }
        catch(Exception $e)
        {
            $ado = null;
            $bResult = false;
            echo( $e->getMessage() );
        }

        return $bResult;
    }

    public function insertPublicoAlvoSelecionado( $cd_divulgacao, $cd_grupo, $cd_filtro_in, $_publicoalvo )
    {
        $adoPublicoSelecionado = new ADO_divulgacao_publicoalvo_selecionado( $this->db );
        $adoG = new ADO_divulgacao_grupo( $this->db );
        $count = 0;

        $result = $adoG->fetchByPGFunction( $cd_grupo, $cd_filtro_in );
        if ($result) {
            $row2 = pg_fetch_row($result);
            $colunas = "";
            $virgula = "";
            for ($index = 0; $index < sizeof($row2); $index++) {
                $colunas .= $virgula.pg_field_name($result, $index);
                $virgula = ",";
            }
            
            $_publicoalvo->setColunas_resultado( $colunas );
		}
        while ($row = pg_fetch_array($result)) 
        {
            $selecionado = new entity_divulgacao_publicoalvo_selecionado();
            
            $selecionado->setcd_divulgacao( $cd_divulgacao );
            $selecionado->setnome($row["nome"]);
            $selecionado->setemail($row["email"]);
            $selecionado->setcd_publicoalvo( $_publicoalvo->getCd_publicoalvo() );
            
            $bResult = $adoPublicoSelecionado->insert( $selecionado );
            
            $count++;
        }
        $result = null;
    }
}

?>