<?php
class helper_ado_contribuicao_senge
{
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

    /**
     * Criar array com resultados da query
     * 
     * @param $params Array com todos parametros necessários para formar a consulta
     *            $params['mes'] 
     *            $params['ano']
     * @return array(new entity_public_controle_geracao_cobranca()) Coleção do objeto entity_public_controle_geracao_cobranca
     */
    public function controle_geracao_cobranca__confirmacao__get( $params )
    {
    	$this->dal->createQuery("

             SELECT   to_char(dt_confirmacao, 'DD/MM/YYYY') as dt_confirmacao
             		, usuario_confirmacao
	                , tot_internet_confirm
	                , tot_bdl_confirm
	                , tot_arrec_confirm
	                , tot_folha_confirm
	                , vlr_folha_confirm
			   FROM public.controle_geracao_cobranca
			  WHERE cd_plano = {cd_plano}
			    AND cd_empresa = {cd_empresa}
			  	AND mes_competencia = {mes_competencia}
			    AND ano_competencia = {ano_competencia}
			    AND dt_confirmacao IS NOT NULL;

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SENGE_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", enum_public_patrocinadoras::SENGE );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();

    	$ents = array();
    	while($row = pg_fetch_array($result))
    	{
    		$ent = new entity_public_controle_geracao_cobranca();
    		$ent->dt_confirmacao = $row["dt_confirmacao"];
    		$ent->usuario_confirmacao = $row["usuario_confirmacao"];
    		$ent->tot_internet_confirm = $row["tot_internet_confirm"];
    		$ent->tot_bdl_confirm = $row["tot_bdl_confirm"];
    		$ent->tot_arrec_confirm = $row["tot_arrec_confirm"];
    		$ent->tot_folha_confirm = $row["tot_folha_confirm"];
    		$ent->vlr_folha_confirm = $row["vlr_folha_confirm"];

    		$ents[ sizeof($ents) ] = $ent;
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_senge.controle_geracao_cobranca__confirmacao__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ents;
    }

    /**
     * Criar array com resultados da query
     * 
     * @param $params Array com todos parametros necessários para formar a consulta
     *            $params['mes'] 
     *            $params['ano']
     * @return array(new entity_public_controle_geracao_cobranca()) Coleção do objeto entity_public_controle_geracao_cobranca
     */
    public function controle_geracao_cobranca__geracao__get( $params )
    {
    	$this->dal->createQuery("

             SELECT   to_char(dt_geracao, 'DD/MM/YYYY') as dt_geracao
             		, usuario_geracao
	                , tot_internet_gerado
	                , tot_bdl_gerado
	                , tot_arrec_gerado
	                , vlr_internet_gerado
	                , vlr_bdl_gerado
	                , vlr_arrec_gerado
	                , tot_folha_gerado
	                , vlr_folha_gerado
			   FROM public.controle_geracao_cobranca
			  WHERE cd_plano = {cd_plano}
			    AND cd_empresa = {cd_empresa}
			  	AND mes_competencia = {mes_competencia}
			    AND ano_competencia = {ano_competencia}
			    AND dt_geracao IS NOT NULL;

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SENGE_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", enum_public_patrocinadoras::SENGE );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();

    	$ents = array();
    	while($row = pg_fetch_array($result))
    	{
    		$ent = new entity_public_controle_geracao_cobranca();
    		$ent->dt_geracao = $row["dt_geracao"];
    		$ent->usuario_geracao = $row["usuario_geracao"];
    		$ent->tot_internet_gerado = $row["tot_internet_gerado"];
    		$ent->tot_bdl_gerado = $row["tot_bdl_gerado"];
    		$ent->tot_arrec_gerado = $row["tot_arrec_gerado"];
    		$ent->vlr_internet_gerado = $row["vlr_internet_gerado"];
    		$ent->vlr_bdl_gerado = $row["vlr_bdl_gerado"];
    		$ent->vlr_arrec_gerado = $row["vlr_arrec_gerado"];
    		$ent->tot_folha_gerado = $row["tot_folha_gerado"];
    		$ent->vlr_folha_gerado = $row["vlr_folha_gerado"];

    		$ents[ sizeof($ents) ] = $ent;
    	}

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em helper_ado_contribuicao_senge.controle_geracao_cobranca__geracao__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ents;
    }

    /**
     * Criar array com resultados da query
     * 
     * @param $params Array com todos parametros necessários para formar a consulta
     *            $params['mes'] 
     *            $params['ano']
     * @return array(new entity_public_controle_geracao_cobranca()) Coleção do objeto entity_public_controle_geracao_cobranca
     */
    public function controle_geracao_cobranca__internet__get( $params )
    {
    	$this->dal->createQuery("

             SELECT   to_char(dt_envio_internet, 'DD/MM/YYYY') AS dt_envio_internet
             		, usuario_envio_internet
	                , tot_internet_enviado
	                , tot_bdl_enviado
	                , tot_arrec_enviado
	                , vlr_internet_enviado
	                , vlr_bdl_enviado
	                , vlr_arrec_enviado
			   FROM public.controle_geracao_cobranca
			  WHERE cd_plano = {cd_plano}
			    AND cd_empresa = {cd_empresa}
			  	AND mes_competencia = {mes_competencia}
			    AND ano_competencia = {ano_competencia}
			    AND dt_envio_internet IS NOT NULL;

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SENGE_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", enum_public_patrocinadoras::SENGE );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();

    	$ents = array();
    	while($row = pg_fetch_array($result))
    	{
    		$ent = new entity_public_controle_geracao_cobranca();
    		$ent->dt_envio_internet = $row["dt_envio_internet"];
    		$ent->usuario_envio_internet = $row["usuario_envio_internet"];
    		$ent->tot_internet_enviado = $row["tot_internet_enviado"];
    		$ent->tot_bdl_enviado = $row["tot_bdl_enviado"];
    		$ent->tot_arrec_enviado = $row["tot_arrec_enviado"];
    		$ent->vlr_internet_enviado = $row["vlr_internet_enviado"];
    		$ent->vlr_bdl_enviado = $row["vlr_bdl_enviado"];
    		$ent->vlr_arrec_enviado = $row["vlr_arrec_enviado"];

    		$ents[ sizeof($ents) ] = $ent;
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_senge.controle_geracao_cobranca__internet__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ents;
    }
    
    /**
     * Totais do primeiro pagamento devolve um array com um Count e um calculo baseado em SUMs da query formada
     * pelas tabelas inscritos_internet, controle_geracao_cobranca, taxas e pacotes
     * 
     * @param $params array com filtros utilizados na query
     *                $param['mes']
     *                $param['ano']
     * 
     * @return array(qt_primeiro_pg, vl_primeiro_pg) Array com os valores retornados na query
     */
    public function totais_primeiro_pagamento__get($params)
    {
    	$this->dal->createQuery("

            SELECT COUNT(*) AS qt_primeiro_pg,
			       SUM(t.vlr_taxa) + SUM(preco) AS vl_primeiro_pg
			  FROM public.inscritos_internet ii,
			       public.controle_geracao_cobranca cgc,
			       public.taxas t,
			       public.pacotes p
			 WHERE ii.dt_envio_primeira_cobr   IS NULL
			   AND ii.dt_primeiro_pgto         IS NULL
			   AND ii.dt_geracao_primeira_cobr IS NOT NULL
			   AND ii.cd_pacote                = 1
			   AND ii.cd_plano                 = {cd_plano}
			   AND ii.cd_empresa               = {cd_empresa}
			   AND cgc.cd_plano                = ii.cd_plano
			   AND cgc.cd_empresa              = ii.cd_empresa
			   AND cgc.mes_competencia         = {mes_competencia}
			   AND cgc.ano_competencia         = {ano_competencia}
			   AND cgc.dt_geracao              IS NOT NULL
			   AND t.cd_indexador              = 42 
			   AND t.dt_taxa                   = DATE_TRUNC('month', CURRENT_DATE)
			   AND p.cd_pacote                 = ii.cd_pacote
			   AND p.cd_plano                  = ii.cd_plano
			   AND p.cd_empresa                = ii.cd_empresa
			   AND p.tipo_cobranca             = 'I'
			   AND p.dt_inicio                 = DATE_TRUNC('month', CURRENT_DATE)

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SENGE_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", enum_public_patrocinadoras::SENGE );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();

    	if($row=pg_fetch_array($result))
    	{
    		if($row['vl_primeiro_pg']=='') $valor=0; else $valor=$row['vl_primeiro_pg'];
    		$ent = array( 'contador'=>$row['qt_primeiro_pg'], 'valor'=>$valor );
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_senge.totais_primeiro_pagamento__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ent;
    }

    /**
     * Totais do pagamento mensal de internet devolve um array com um Count e um calculo baseado em SUMs da query formada
     * pelas tabelas inscritos_internet, controle_geracao_cobranca, taxas e pacotes
     * 
     * @param $params array com filtros utilizados na query
     *                $param['mes']
     *                $param['ano']
     * 
     * @return array(qt_primeiro_pg, vl_primeiro_pg) Array com os valores retornados na query
     */
    public function totais_mensal_internet__get($params)
    {
    	$this->dal->createQuery("
            -- MENSAL PAGAMENTO
			SELECT COUNT(DISTINCT ii.CD_REGISTRO_EMPREGADO) as contador
			     , SUM(valor_lancamento) AS valor
			  FROM public.bloqueto b
			  JOIN public.inscritos_internet ii
			       ON b.cd_empresa = ii.cd_empresa 
			          AND b.cd_registro_empregado = ii.cd_registro_empregado
			          AND b.seq_dependencia = ii.seq_dependencia
	     JOIN participantes p 
			       ON ii.cd_empresa = p.cd_empresa 
			          AND ii.cd_registro_empregado = p.cd_registro_empregado 
			          AND ii.seq_dependencia       = p.seq_dependencia
			 WHERE b.mes_competencia           = {mes_competencia}
			   AND b.ano_competencia           = {ano_competencia}
			   AND b.data_retorno              IS NULL
			   AND ii.cd_plano                 = {cd_plano}
			   AND ii.cd_empresa               = {cd_empresa}
			   AND ii.cd_pacote                = 1
			   --AND ii.dt_geracao_primeira_cobr IS NOT NULL   
			   AND ii.dt_primeiro_pgto         IS NOT NULL
		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SENGE_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", enum_public_patrocinadoras::SENGE );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();

    	if($row=pg_fetch_array($result))
    	{
			if($row['valor']=='') $valor=0; else $valor=$row['valor'];
    		$ent = array( 'contador'=>$row['contador'], 'valor'=>$valor );
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_senge.totais_mensal_internet__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ent;
    }

	/**
     * Totais do primeiro pagamento BDL devolve um array com um Count e um calculo baseado em SUMs da query formada
     * pelas tabelas inscritos_internet, controle_geracao_cobranca, taxas e pacotes
     * 
     * @param $params array com filtros utilizados na query
     *                $param['mes']
     *                $param['ano']
     * 
     * @return array(contador, valor) Array com os valores retornados na query
     */
    public function totais_bdl__get($params)
    {
    	$this->dal->createQuery("

            SELECT COUNT(*) AS qt_primeiro_pg,
			       SUM(t.vlr_taxa) + SUM(preco) AS vl_primeiro_pg_bdl
			  FROM public.inscritos_internet ii,
			       public.controle_geracao_cobranca cgc,
			       public.taxas t,
			       public.pacotes p
			 WHERE ii.dt_envio_primeira_cobr   IS NULL
			   AND ii.dt_primeiro_pgto         IS NULL
			   AND ii.dt_geracao_primeira_cobr IS NOT NULL
			   AND ii.cd_pacote                = 2
			   AND ii.id_bdl                   = 'S'			   
			   AND ii.cd_plano                 = {cd_plano}
			   AND ii.cd_empresa               = {cd_empresa}
			   AND cgc.cd_plano                = ii.cd_plano
			   AND cgc.cd_empresa              = ii.cd_empresa
			   AND cgc.mes_competencia         = {mes_competencia}
			   AND cgc.ano_competencia         = {ano_competencia}
			   AND cgc.dt_geracao              IS NOT NULL
			   AND t.cd_indexador              = 42 
			   AND t.dt_taxa                   = DATE_TRUNC('month', CURRENT_DATE)
			   AND p.cd_pacote                 = ii.cd_pacote
			   AND p.cd_plano                  = ii.cd_plano
			   AND p.cd_empresa                = ii.cd_empresa
			   AND p.tipo_cobranca             = 'I'
			   AND p.dt_inicio                 = DATE_TRUNC('month', CURRENT_DATE)

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SENGE_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", enum_public_patrocinadoras::SENGE );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();
    	// echo $this->dal->getMessage();

    	if($row=pg_fetch_array($result))
    	{
    		if($row['vl_primeiro_pg_bdl']=='') $valor=0; else $valor=$row['vl_primeiro_pg_bdl'];
    		$ent = array( 'contador'=>$row['qt_primeiro_pg'], 'valor'=>$valor );
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_senge.totais_bdl__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ent;
    }

    /**
     * Totais do pagamento mensal de bdl devolve um array com um Count e um calculo baseado em SUMs da query formada
     * pelas tabelas inscritos_internet, controle_geracao_cobranca, taxas e pacotes
     * 
     * @param $params array com filtros utilizados na query
     *                $param['mes']
     *                $param['ano']
     * 
     * @return array(contador, valor) Array com os valores retornados na query
     */
    public function totais_mensal_bdl__get($params)
    {
    	$this->dal->createQuery("

            -- MENSAL PAGAMENTO
			SELECT COUNT(DISTINCT ii.CD_REGISTRO_EMPREGADO) as contador
			     , SUM(valor_lancamento) AS valor
			  FROM public.bloqueto b
			  JOIN public.inscritos_internet ii
			    ON b.cd_empresa            = ii.cd_empresa 
			   AND b.cd_registro_empregado = ii.cd_registro_empregado
			   AND b.seq_dependencia       = ii.seq_dependencia
			  LEFT JOIN participantes p 
			    ON ii.cd_empresa            = p.cd_empresa 
			   AND ii.cd_registro_empregado = p.cd_registro_empregado 
			   AND ii.seq_dependencia       = p.seq_dependencia
			 WHERE b.mes_competencia           = {mes_competencia}
			   AND ii.id_bdl                   = 'S'
			   AND b.ano_competencia           = {ano_competencia}
			   AND b.data_retorno              IS NULL
			   AND ii.cd_plano                 = {cd_plano}
			   AND ii.cd_empresa               = {cd_empresa}
			   AND ii.cd_pacote                = 2
			   --AND ii.dt_geracao_primeira_cobr IS NOT NULL   
			   AND ii.dt_primeiro_pgto         IS NOT NULL

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SENGE_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", enum_public_patrocinadoras::SENGE );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();
    	// echo '<pre>' . $this->dal->getMessage() . '</pre>';

    	if($row=pg_fetch_array($result))
    	{
    		if($row['valor']=='') $valor=0; else $valor=$row['valor'];
    		$ent = array( 'contador'=>$row['contador'], 'valor'=>$valor );
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_senge.totais_bdl__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ent;
    }

	/**
     * Totais do primeiro pagamento de arrecadação devolve um array com um Count e um calculo baseado em SUMs da query formada
     * pelas tabelas inscritos_internet, controle_geracao_cobranca, taxas e pacotes
     * 
     * @param $params array com filtros utilizados na query
     *                $param['mes']
     *                $param['ano']
     * 
     * @return array(qt_primeiro_pg, vl_primeiro_pg) Array com os valores retornados na query
     */
    public function totais_arrecadacao__get($params)
    {
    	$this->dal->createQuery("

            SELECT COUNT(*) AS qt_primeiro_pg,
			       SUM(t.vlr_taxa) + SUM(preco) AS valor
			  FROM public.inscritos_internet ii,
			       public.controle_geracao_cobranca cgc,
			       public.taxas t,
			       public.pacotes p
			 WHERE ii.dt_envio_primeira_cobr   IS NULL
			   AND ii.dt_primeiro_pgto         IS NULL
			   AND ii.dt_geracao_primeira_cobr IS NOT NULL
			   AND ii.cd_pacote                = 2
			   AND ii.id_arrecadacao           = 'S'			   
			   AND ii.cd_plano                 = {cd_plano}
			   AND ii.cd_empresa               = {cd_empresa}
			   AND cgc.cd_plano                = ii.cd_plano
			   AND cgc.cd_empresa              = ii.cd_empresa
			   AND cgc.mes_competencia         = {mes_competencia}
			   AND cgc.ano_competencia         = {ano_competencia}
			   AND cgc.dt_geracao              IS NOT NULL
			   AND t.cd_indexador              = 42 
			   AND t.dt_taxa                   = DATE_TRUNC('month', CURRENT_DATE)
			   AND p.cd_pacote                 = ii.cd_pacote
			   AND p.cd_plano                  = ii.cd_plano
			   AND p.cd_empresa                = ii.cd_empresa
			   AND p.tipo_cobranca             = 'I'
			   AND p.dt_inicio                 = DATE_TRUNC('month', CURRENT_DATE)

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SENGE_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", enum_public_patrocinadoras::SENGE );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();
    	// echo $this->dal->getMessage();

    	if($row=pg_fetch_array($result))
    	{
    		if($row['valor']=='') $valor=0; else $valor=$row['valor'];
    		$ent = array( 'contador'=>$row['qt_primeiro_pg'], 'valor'=>$valor );
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_senge.totais_arrecadacao__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ent;
    }

    /**
     * Totais do pagamento mensal de arrecadação devolve um array com um Count e um calculo baseado em SUMs da query formada
     * pelas tabelas inscritos_internet, controle_geracao_cobranca, taxas e pacotes
     * 
     * @param $params array com filtros utilizados na query
     *                $param['mes']
     *                $param['ano']
     * 
     * @return array(qt_primeiro_pg, vl_primeiro_pg) Array com os valores retornados na query
     */
    public function totais_mensal_arrecadacao__get($params)
    {
    	$this->dal->createQuery("

            -- MENSAL PAGAMENTO
			SELECT COUNT(DISTINCT p.CD_REGISTRO_EMPREGADO) as contador
			     , SUM(b.valor_lancamento) AS valor
			  FROM public.bloqueto b
			  JOIN participantes p 
			    ON b.cd_empresa            = p.cd_empresa 
			   AND b.cd_registro_empregado = p.cd_registro_empregado 
			   AND b.seq_dependencia       = p.seq_dependencia
			  JOIN public.inscritos_internet ii
			    ON b.cd_empresa            = ii.cd_empresa 
			   AND b.cd_registro_empregado = ii.cd_registro_empregado
			   AND b.seq_dependencia       = ii.seq_dependencia
			 WHERE b.mes_competencia           = {mes_competencia}
			   AND b.ano_competencia           = {ano_competencia}
			   AND b.data_retorno              IS NULL
			   AND p.cd_plano                 = {cd_plano}
			   AND p.cd_empresa               = {cd_empresa}
			   AND ii.id_arrecadacao           = 'S'

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SENGE_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", enum_public_patrocinadoras::SENGE );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();

    	if($row=pg_fetch_array($result))
    	{
    		// if($row['valor']=='') $valor=0; else $valor=$row['valor'];
    		$ent = array( 'contador'=>$row['contador'], 'valor'=>0 );
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_senge.totais_arrecadacao__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ent;
    }

	public function total_emails_enviar_primeiro($params)
	{
		$this->dal->createQuery("

	        SELECT COUNT(*) as contador
			  FROM public.inscritos_internet ii,
			       public.controle_geracao_cobranca cgc,
			       public.taxas t,
			       public.pacotes p
			 WHERE ii.dt_envio_primeira_cobr   IS NULL
			   AND ii.dt_primeiro_pgto         IS NULL
			   AND ii.dt_geracao_primeira_cobr IS NOT NULL
			   AND ii.cd_pacote                = 1
			   AND ii.cd_plano                 = {cd_plano}
			   AND ii.cd_empresa               = {cd_empresa}
			   AND cgc.cd_plano                = ii.cd_plano
			   AND cgc.cd_empresa              = ii.cd_empresa
			   AND cgc.mes_competencia         = {mes_competencia}
			   AND cgc.ano_competencia         = {ano_competencia}
			   AND cgc.dt_geracao              IS NOT NULL
			   AND t.cd_indexador              = 42 
			   AND t.dt_taxa                   = DATE_TRUNC('month', CURRENT_DATE)
			   AND p.cd_pacote                 = ii.cd_pacote
			   AND p.cd_plano                  = ii.cd_plano
			   AND p.cd_empresa                = ii.cd_empresa
			   AND p.tipo_cobranca             = 'I'
			   AND p.dt_inicio                 = DATE_TRUNC('month', CURRENT_DATE)
			   AND ii.email IS NOT NULL AND ii.email <> ''

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SENGE_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", enum_public_patrocinadoras::SENGE );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();
    	// echo $this->dal->getMessage();

    	if($row=pg_fetch_array($result))
    	{
    		$ret = $row['contador'];
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_senge.total_emails_enviar_primeiro() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ret;
	}

	public function total_emails_enviar_mensal($params)
	{
		$this->dal->createQuery("

		    SELECT COUNT(DISTINCT COALESCE(b.nome, ii.nome)) as contador
			  FROM public.bloqueto b
			  JOIN public.inscritos_internet ii
			    ON b.cd_empresa            = ii.cd_empresa 
			   AND b.cd_registro_empregado = ii.cd_registro_empregado
			   AND b.seq_dependencia       = ii.seq_dependencia
			  LEFT JOIN participantes p 
			    ON ii.cd_empresa            = p.cd_empresa 
			   AND ii.cd_registro_empregado = p.cd_registro_empregado 
			   AND ii.seq_dependencia       = p.seq_dependencia
			 WHERE b.mes_competencia           = {mes_competencia}
			   AND b.ano_competencia           = {ano_competencia}
			   AND b.data_retorno              IS NULL
			   AND ii.cd_plano                 = {cd_plano}
			   AND ii.cd_empresa               = {cd_empresa}
			   AND ii.cd_pacote                = 1
			   --AND ii.dt_geracao_primeira_cobr IS NOT NULL   
			   AND ii.dt_primeiro_pgto         IS NOT NULL
			   AND ( COALESCE(p.email, ii.email) IS NOT NULL AND COALESCE(p.email, ii.email) <> '')

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SENGE_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", enum_public_patrocinadoras::SENGE );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();

    	if($row=pg_fetch_array($result))
    	{
    		$ret = $row['contador'];
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_senge.total_emails_enviar_mensal() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ret;
	}
}
?>