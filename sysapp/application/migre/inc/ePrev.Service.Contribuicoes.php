<?php
include_once("ePrev.DAL.DBConnection.php");
include_once("nextval_sequence.php");

include_once("ePrev.Enums.php");

include_once("ePrev.Entity.php");
include_once("ePrev.Helper.ADO.contribuicao_senge.php");
include_once("ePrev.Helper.ADO.contribuicao_sinpro.php");

class service_contribuicoes
{
    private $db;

    function __construct( $_db ) 
    {
        $this->db = $_db;
    }

    function __destruct()
    {
        $this->db = null;
    }

	//
    // SENGE PREVIDÊNCIA
    //
    
    /**
     * Criar array com resultados da query
     * 
     * @param $params Array com todos parametros necessários para formar a consulta
     *            $params['mes'] 
     *            $params['ano']
     * @return array(new entity_public_controle_geracao_cobranca()) Coleção do objeto entity_public_controle_geracao_cobranca
     *         OU
     *         retorna FALSE se ocorrer algum erro na consulta
     */
    public function contribuicao_senge__controle_geracao_cobranca__get( $tipo, $params )
    {
    	$return = false;
    	$helper = new helper_ado_contribuicao_senge( $this->db );
    	try
    	{
    		if( $tipo=="confirmacao" )
    		{
    			$return = $helper->controle_geracao_cobranca__confirmacao__get( $params );
    		}
    		elseif($tipo=="geracao")
    		{
    			$return = $helper->controle_geracao_cobranca__geracao__get( $params );
    		}
    		elseif($tipo=="internet")
    		{
    			$return = $helper->controle_geracao_cobranca__internet__get( $params );
    		}
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }

    /**
     * Totais do primeiro pagamento devolve um array com um Count e um calculo baseado em SUMs da query formada
     * pelas tabelas inscritos_internet, controle_geracao_cobranca, taxas e pacotes
     * 
     * @param $params array com filtros utilizados na query
     *                $param['mes']
     *                $param['ano']
     * 
     * @return array(contador, valor) Array com os valores retornados na query
     *         OU
     *         retorna FALSE se ocorrer algum erro na consulta
     */
    public function contribuicao_senge__totais__get( $tipo, $params )
    {
    	$return = false;
    	$helper = new helper_ado_contribuicao_senge( $this->db );
    	try
    	{
    		if($tipo=="primeiro_pagamento")
    		{
    			$return = $helper->totais_primeiro_pagamento__get( $params );
    		}
    		elseif($tipo=="bdl")
    		{
    			$return = $helper->totais_bdl__get( $params );
    		}
    		elseif($tipo=="arrecadacao")
    		{
    			$return = $helper->totais_arrecadacao__get( $params );
    		}
    		elseif($tipo=="mensal internet")
    		{
    			$return = $helper->totais_mensal_internet__get( $params );
    		}
    		elseif($tipo=="mensal bdl")
    		{
    			$return = $helper->totais_mensal_bdl__get( $params );
    		}
    		elseif($tipo=="mensal arrecadacao")
    		{
    			$return = $helper->totais_mensal_arrecadacao__get( $params );
    		}
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }
    
    /**
     * Quantidade de emails a enviar para primeiro pagamento
     * considerando apenas registros que contém email
     * 
     * @param $params Array com colunas 'mes' e 'ano'
     *        $params['mes'] : mes de competencia
     *        $params['ano'] : ano de competencia
     */
    public function contribuicao_senge__emails_enviar_primeiro__get( $params )
    {
    	$return = false;
    	$helper = new helper_ado_contribuicao_senge( $this->db );
    	try
    	{
    		$return = $helper->total_emails_enviar_primeiro( $params );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }

    public function contribuicao_senge__emails_enviar_mensal__get( $params )
    {
    	$return = false;
    	$helper = new helper_ado_contribuicao_senge( $this->db );
    	try
    	{
    		$return = $helper->total_emails_enviar_mensal( $params );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }

	//
    // SINPRORS PREVIDÊNCIA
    //
    
    /**
     * Criar array com resultados da query
     * 
     * @param $params Array com todos parametros necessários para formar a consulta
     *            $params['mes'] 
     *            $params['ano']
     * @return array(new entity_public_controle_geracao_cobranca()) Coleção do objeto entity_public_controle_geracao_cobranca
     *         OU
     *         retorna FALSE se ocorrer algum erro na consulta
     */
    public function contribuicao_sinpro__controle_geracao_cobranca__get( $tipo, $params, $cd_empresa=8 )
    {
    	$return = false;
    	$helper = new helper_ado_contribuicao_sinpro( $this->db );
    	try
    	{
    		if( $tipo=="confirmacao" )
    		{
    			$return = $helper->controle_geracao_cobranca__confirmacao__get( $params, $cd_empresa );
    		}
    		elseif($tipo=="geracao")
    		{
    			$return = $helper->controle_geracao_cobranca__geracao__get( $params, $cd_empresa );
    		}
    		elseif($tipo=="envio")
    		{
    			$return = $helper->controle_geracao_cobranca__envio__get( $params, $cd_empresa );
    		}
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }

    /**
     * Totais do primeiro pagamento devolve um array com um Count e um calculo baseado em SUMs da query formada
     * pelas tabelas inscritos_internet, controle_geracao_cobranca, taxas e pacotes
     * 
     * @param $params array com filtros utilizados na query
     *                $param['mes']
     *                $param['ano']
     * 
     * @return array(contador, valor) Array com os valores retornados na query
     *         OU
     *         retorna FALSE se ocorrer algum erro na consulta
     */
    public function contribuicao_sinpro__totais__get( $tipo, $params, $cd_empresa=8 )
    {
    	$return = false;
    	$helper = new helper_ado_contribuicao_sinpro( $this->db );
    	try
    	{
			if($tipo=="bdl")
    		{
    			$return = $helper->totais_bdl__get( $params, $cd_empresa );
    		}
			if($tipo=="mensal bdl")
    		{
    			$return = $helper->totais_mensal_bdl__get( $params, $cd_empresa );
    		}
    		
    		if($tipo=="bco")
    		{
    			$params['forma_pagamento'] = 'BCO';
    			$return = $helper->totais_bdl__get( $params, $cd_empresa );
    		}
    		if($tipo=="mensal bco")
    		{
    			$params['codigo_lancamento'] = "2060,2061,2062";
    			$return = $helper->totais_mensal_bdl__get( $params, $cd_empresa );
    		}
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }
    
    /**
     * Quantidade de emails a enviar para primeiro pagamento
     * considerando apenas registros que contém email
     * 
     * @param $params Array com colunas 'mes' e 'ano'
     *        $params['mes'] : mes de competencia
     *        $params['ano'] : ano de competencia
     */
    public function contribuicao_sinpro__emails_enviar_primeiro__get( $params, $cd_empresa=8 )
    {
    	$return = false;
    	$helper = new helper_ado_contribuicao_sinpro( $this->db );
    	try
    	{
    		$return = $helper->total_emails_enviar_primeiro( $params, $cd_empresa );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }
    
    /**
     * Lista de participantes que devem ser cobrados referente ao primeiro pagamento
     * e que não possuem email
     * 
     * @param $params Array com colunas 'mes' e 'ano'
     *        $params['mes'] : mes de competencia
     *        $params['ano'] : ano de competencia
     */
    public function contribuicao_sinpro__lista_sem_emails_primeiro( $params, $cd_empresa=8 )
    {
    	$return = false;
    	$helper = new helper_ado_contribuicao_sinpro( $this->db );
    	try
    	{
    		$return = $helper->lista_sem_emails_primeiro( $params, $cd_empresa );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }

    public function contribuicao_sinpro__emails_enviar_mensal__get( $params, $cd_empresa=8 )
    {
    	$helper = new helper_ado_contribuicao_sinpro( $this->db );
    	try
    	{
    		$return = $helper->total_emails_enviar_mensal( $params, $cd_empresa );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }

    public function contribuicao_sinpro__lista_sem_email_mensal( $params, $cd_empresa=8 )
    {
    	$helper = new helper_ado_contribuicao_sinpro( $this->db );
    	try
    	{
    		$return = $helper->lista_sem_email_mensal( $params, $cd_empresa );
    	}
    	catch( Exception $e )
    	{
    		$ado = null;
    		$return = false;
    	}

    	return $return;
    }
}
?>