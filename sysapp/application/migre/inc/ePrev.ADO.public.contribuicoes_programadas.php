<?php
class ADO_public_contribuicoes_programadas {

    // DAL
    private $db;
    private $dal;

    function ADO_public_contribuicoes_programadas( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    /**
     * Retorna o valor da seguinte query:
     * 
     *    SELECT valor
	 *      FROM contribuicoes_programadas cpr
	 *     WHERE cpr.cd_empresa = {cd_empresa} 
	 *       AND cpr.cd_registro_empregado = {cd_registro_empregado} 
	 *       AND cpr.seq_dependencia = {seq_dependencia}
	 *       AND cpr.dt_confirma_opcao IS NOT NULL
	 *       AND cpr.dt_confirma_canc IS null
     * 
     */
    public function get_valor( $cd_empresa, $cd_registro_empregado, $seq_dependencia )
    {
        $sRet = "";
        $this->dal->createQuery("

          SELECT valor
	        FROM contribuicoes_programadas cpr
	       WHERE cpr.cd_empresa = {cd_empresa} 
	         AND cpr.cd_registro_empregado = {cd_registro_empregado} 
	         AND cpr.seq_dependencia = {seq_dependencia}
	         AND cpr.dt_confirma_opcao IS NOT NULL
	         AND cpr.dt_confirma_canc IS null

        ");

        $this->dal->setAttribute( "{cd_empresa}", (int)$cd_empresa );
        $this->dal->setAttribute( "{cd_registro_empregado}", (int)$cd_registro_empregado );
        $this->dal->setAttribute( "{seq_dependencia}", (int)$seq_dependencia );

        $result = $this->dal->getResultset();

        if ($result) 
        {
			if ( $row = pg_fetch_array($result) )
            {
                $sRet = $row["valor"];
            }
		}

        if ($this->dal->haveError()) 
        {
            throw new Exception("Erro em ADO_public_contribuicoes_programadas.get_valor($cd_empresa, $cd_registro_empregado, $seq_dependencia) ao executar comando SQL de consulta. ".$this->dal->getMessage());
        }

        return $sRet;
    }
}

?>