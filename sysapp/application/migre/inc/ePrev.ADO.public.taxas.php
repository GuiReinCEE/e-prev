<?php
class ADO_public_taxas {

    // DAL
    private $db;
    private $dal;

    function ADO_public_taxas( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function get_taxa( $cd_indexador, $data_ref )
    {
        $sRet = "";
        $this->dal->createQuery("

            SELECT vlr_taxa 
              FROM taxas 
             WHERE cd_indexador = {cd_indexador} 
               AND dt_taxa = DATE_TRUNC('month', TO_DATE( '{data_ref}', 'DD/MM/YYYY' ) )

        ");

        $this->dal->setAttribute( "{cd_indexador}", (int)$cd_indexador );
        $this->dal->setAttribute( "{data_ref}", $data_ref );

        $result = $this->dal->getResultset();

        if ($result) 
        {
			if ( $row = pg_fetch_array($result) )
            {
                $sRet = $row["vlr_taxa"];
            }
		}

        if ($this->dal->haveError()) 
        {
            throw new Exception("Erro em ADO_public_taxas.get_taxa($cd_indexador) ao executar comando SQL de consulta. ".$this->dal->getMessage());
        }

        return $sRet;
    }
}

?>