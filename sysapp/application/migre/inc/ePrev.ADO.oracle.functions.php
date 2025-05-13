<?php
class ADO_oracle_functions
{

    // DAL
    private $db;
    private $dal;

    function ADO_oracle_functions( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function get_custo_administrativo( $cd_empresa , $valor )
    {
        $sRet = "";
        $this->dal->createQuery("

            SELECT oracle.fnc_retorna_custo_adm_instit( {cd_empresa}, CURRENT_DATE, {valor} ) as custo

        ");

        $this->dal->setAttribute( "{cd_empresa}", (int)$cd_empresa );
        $this->dal->setAttribute( "{valor}", floatval($valor) );

        $result = $this->dal->getResultset();

        if ($result) 
        {
			if ( $row = pg_fetch_array($result) )
            {
                $sRet = $row['custo'];
            }
            else
            {
                $sRet = '0';
            }
		}
        else
        {
            $sRet = '0';
        }

        if ($this->dal->haveError()) 
        {
            throw new Exception("Erro em ADO_oracle_functions.get_custo_administrativo($cd_empresa , $valor) ao executar comando SQL de consulta. ".$this->dal->getMessage());
        }

        return $sRet;
    }
}

?>