<?php
class ADO_public_pacotes {

    // DAL
    private $db;
    private $dal;

    function ADO_public_pacotes( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function get_valor_bdl( $cd_pacote, $cd_plano, $cd_empresa, $data_ref )
    {
        $sRet = "";
        $this->dal->createQuery("

             SELECT p.valor_bdl
               FROM pacotes p 
              WHERE p.cd_plano   = {cd_plano}
                AND p.cd_empresa = {cd_empresa}
                AND p.cd_pacote  = {cd_pacote}
                AND p.dt_inicio  = DATE_TRUNC('month', TO_DATE( '{data_ref}', 'DD/MM/YYYY' ) )

        ");

        $this->dal->setAttribute( "{cd_pacote}", (int)$cd_pacote );
        $this->dal->setAttribute( "{cd_plano}", (int)$cd_plano );
        $this->dal->setAttribute( "{cd_empresa}", (int)$cd_empresa );
        $this->dal->setAttribute( "{data_ref}", $data_ref );

        $result = $this->dal->getResultset();

        $return = '0';
        if ($result) 
        {
			if ( $row = pg_fetch_array($result) )
            {
                $return = $row['valor_bdl'];
            }
		}

        if ($this->dal->haveError()) 
        {
            throw new Exception("Erro em ADO_public_pacotes.get_valor_bdl($cd_pacote, $cd_plano, $cd_empresa) ao executar comando SQL de consulta. ". $this->dal->getMessage() );
        }

        return $return;
    }
}
?>