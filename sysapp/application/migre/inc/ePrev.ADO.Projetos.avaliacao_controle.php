<?php
class ADO_projetos_avaliacao_controle
{
    // DAL
    private $db;
    private $dal;

    function ADO_projetos_avaliacao_controle( $_db )
    {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct()
    {
        $this->dal = null;
    }

    public function get_dt_fechamento( $dt_periodo )
    {
        $sRet = "";
        $this->dal->createQuery("

            SELECT TO_CHAR( dt_fechamento, 'DD/MM/YYYY' ) AS dt_fechamento
              FROM projetos.avaliacao_controle
             WHERE dt_periodo='{dt_periodo}' 

        ");

        $this->dal->setAttribute( "{dt_periodo}", $dt_periodo );

        $result = $this->dal->getResultset();
        
        if ( $result ) 
        {
            if ( $row = pg_fetch_array($result) )
            {
                $sRet = $row["dt_fechamento"];
            }
        }

        if ( $this->dal->haveError() ) 
        {
            throw new Exception( "Erro em ADO_projetos_avaliacao_controle.get_dt_fechamento( $re, $comp ) ao executar comando SQL de consulta. " . $this->dal->getMessage() );
        }

        return $sRet;
    }

    /**
     * Verifica se o perнodo jб possui avaliaзгo aberta
     *
     * @return bool True se data corrente estб dentro do perнodo de abertura e fechamento. False se nгo existir registro pro perнodo ou data corrente fora do periodo.
     */
    public function is_open()
    {
        $this->dal->createQuery("

            SELECT count(*) AS contador
              FROM projetos.avaliacao_controle
             WHERE dt_periodo = EXTRACT( YEAR FROM CURRENT_DATE )
               AND CURRENT_TIMESTAMP BETWEEN dt_abertura AND dt_fechamento

        ");

        $result = $this->dal->getResultset();

        if ( $result ) 
        {
            if ( $row = pg_fetch_array($result) )
            {
                $return = ($row['contador']);
            }
            else
            {
            	$return = false;
            }
        }
        else
        {
        	$return = false;
        }

        if ( $this->dal->haveError() ) 
        {
            throw new Exception( "Erro em ADO_projetos_avaliacao_controle.is_open() ao executar comando SQL de consulta. " . $this->dal->getMessage() );
        }

        return $return;
    }

    /**
     * Consulta baseado em chave unica
     *
     * @return entity_projetos_avaliacao_controle() Preenchido com registro baseado em dt_periodo
     */
    public function load_by_year( entity_projetos_avaliacao_controle & $controle )
    {
        $this->dal->createQuery("

            SELECT cd_avaliacao_controle
            	 , cd_usuario_abertura
            	 , cd_usuario_fechamento
            	 , dt_periodo
                 , TO_CHAR(dt_abertura, 'DD/MM/YYYY HH24:MI:SS') as dt_abertura
                 , TO_CHAR(dt_fechamento, 'DD/MM/YYYY HH24:MI:SS') as dt_fechamento
              FROM projetos.avaliacao_controle
             WHERE dt_periodo = {dt_periodo}

        ");

        $this->dal->setAttribute( '{dt_periodo}', (int)$controle->dt_periodo );
        $result = $this->dal->getResultset();

        if ( $result )
        {
            if ( $row = pg_fetch_array($result) )
            {
               	$controle->cd_avaliacao_controle = $row['cd_avaliacao_controle']; 
               	$controle->cd_usuario_abertura = $row['cd_usuario_abertura']; 
               	$controle->cd_usuario_fechamento = $row['cd_usuario_fechamento']; 
               	$controle->dt_abertura = $row['dt_abertura']; 
               	$controle->dt_fechamento = $row['dt_fechamento']; 
               	$controle->dt_periodo = $row['dt_periodo']; 
            }
        }

        if ( $this->dal->haveError() ) 
        {
            throw new Exception( "Erro em ADO_projetos_avaliacao_controle.load_by_year( $dt_periodo ) ao executar comando SQL de consulta. " . $this->dal->getMessage() );
        }
    }
}
?>