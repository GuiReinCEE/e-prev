<?php
class ADO_projetos_auto_atendimento_pagamento_impressao
{
    // DAL
    private $db;
    private $dal;

    function __construct( $_db )
    {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct()
    {
        $this->dal = null;
    } 

    public function get($params)
    {
        $relatorios = array();

        $this->dal->createQuery("

              SELECT cd_auto_atendimento_pagamento_impressao, cd_empresa, cd_registro_empregado, 
			         seq_dependencia, tp_documento, vl_valor, mes_competencia, ano_competencia, 
			         TO_CHAR(dt_vencimento, 'DD/MM/YYYY') AS dt_vencimento, 
			         TO_CHAR(dt_impressao, 'DD/MM/YYYY HH24:MI:SS') as dt_impressao, 
			         ip
                FROM projetos.auto_atendimento_pagamento_impressao
			   WHERE ip NOT LIKE '10.63.255.%'
			     AND cd_empresa = {cd_empresa}
				 AND (
					     (ano_competencia = {ano_competencia} AND mes_competencia = {mes_competencia} )
					  OR (EXTRACT( month from dt_vencimento-'1 month'::interval ) = {mes_competencia} AND  EXTRACT( year from dt_vencimento-'1 month'::interval ) = {ano_competencia})
				     )

        ");

        $this->dal->setAttribute( "{ano_competencia}", (int)$params['ano_competencia'] );
        $this->dal->setAttribute( "{mes_competencia}", (int)$params['mes_competencia'] );
        $this->dal->setAttribute( "{cd_empresa}", (int)$params['cd_empresa'] );

        $result = $this->dal->getResultset();
        // echo $this->dal->getMessage();

        if($result)
        {
            while( $row = pg_fetch_array($result) )
            {
                $relatorio = new entity_projetos_auto_atendimento_pagamento_impressao();
            	foreach( $relatorio as $key=>$value )
            	{
            		eval( '$relatorio->'.$key.' = $row['.$key.'];' );
            	}
				$relatorios[sizeof($relatorios)] = $relatorio;
            }
		}

        if( $this->dal->haveError() )
        {
            throw new Exception( 'Erro em ADO_projetos_auto_atendimento_pagamento_impressao.get() ao executar comando SQL de consulta. '.$this->dal->getMessage() );
        }

        pg_free_result($result);
        $result = null;
        $row = null;

        return $relatorios;
    }
}
?>