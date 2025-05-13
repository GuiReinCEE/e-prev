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
class ADO_consultas {

    // DAL
    private $db;
    private $dal;

    function ADO_consultas( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function sinpro_tipo_pagamento_boleto__get_tipo( $re, $comp )
    {
        $sRet = "";
        $this->dal->createQuery("

            SELECT tp_pagamento
              FROM consultas.sinprors_tipo_pagamento_boleto 
             WHERE re='{re}' 
               AND competencia='{competencia}'
               AND tp_pagamento <> 'X'

        ");

        $this->dal->setAttribute( "{re}", $re );
        $this->dal->setAttribute( "{competencia}", $comp );

        $result = $this->dal->getResultset();

        if ( $result ) 
        {
			if ( $row = pg_fetch_array($result) )
            {
                $sRet = $row["tp_pagamento"];
            }
		}

        if ( $this->dal->haveError() ) 
        {
            throw new Exception( "Erro em ADO_consultas.sinpro_tipo_pagamento_boleto__get_tipo( $re, $comp ) ao executar comando SQL de consulta. " . $this->dal->getMessage() );
        }

        return $sRet;
    }

    /**
     * Responde um array montado por pg_fetch_array
     */
    public function sinpro_tipo_pagamento_boleto__get( $emp, $re, $seq )
    {
        $a_rows = array();
        $sRet = "";
        $this->dal->createQuery("

            SELECT *
              FROM consultas.sinprors_tipo_pagamento_boleto 
             WHERE cd_empresa = {cd_empresa}
               AND cd_registro_empregado = {cd_registro_empregado}
               AND seq_dependencia = {seq_dependencia}
               AND tp_pagamento IN ('A', 'M');

        ");

        $this->dal->setAttribute( "{cd_empresa}", (int)$emp );
        $this->dal->setAttribute( "{cd_registro_empregado}", (int)$re );
        $this->dal->setAttribute( "{seq_dependencia}", (int)$seq );

        $result = $this->dal->getResultset();

        while ( $row = pg_fetch_array($result) )
        {
            $a_row = array(   
                              'tp_pagamento'=>$row['tp_pagamento']
                            , 'mes_competencia'=>$row['mes_competencia']
                            , 'ano_competencia'=>$row['ano_competencia']
                            , 're'=>$row['re']
                            , 'competencia'=>$row['competencia'] 
                            );

            $a_rows[ sizeof($a_rows) ] = $a_row;
        }
        if ( $this->dal->haveError() ) 
        {
            throw new Exception( "Erro em ADO_consultas.sinpro_tipo_pagamento_boleto__get( $emp, $re, $seq ) ao executar comando SQL de consulta. " . $this->dal->getMessage() );
        }

        return $a_rows;
    }
}
?>