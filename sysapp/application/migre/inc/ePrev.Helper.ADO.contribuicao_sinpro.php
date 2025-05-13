<?php
class helper_ado_contribuicao_sinpro
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
     * @param $params Array com todos parametros necessбrios para formar a consulta
     *            $params['mes'] 
     *            $params['ano']
     * @return array(new entity_public_controle_geracao_cobranca()) Coleзгo do objeto entity_public_controle_geracao_cobranca
     */
    public function controle_geracao_cobranca__confirmacao__get( $params,$cd_empresa=8 )
    {
    	$this->dal->createQuery("

             SELECT   to_char(dt_confirmacao, 'DD/MM/YYYY') as dt_confirmacao
             		, usuario_confirmacao
	                , tot_internet_confirm
	                , tot_bdl_confirm
	                , tot_cheque_confirm
	                , tot_deposito_confirm
	                , tot_debito_cc_confirm
	                , vlr_cheque_confirm
	                , vlr_deposito_confirm
	                , vlr_debito_cc_confirm
	                , tot_folha_confirm
	                , vlr_folha_confirm
			   FROM public.controle_geracao_cobranca
			  WHERE cd_plano = {cd_plano}
			    AND cd_empresa = {cd_empresa}
			  	AND mes_competencia = {mes_competencia}
			    AND ano_competencia = {ano_competencia}
			    AND dt_confirmacao IS NOT NULL;

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", $cd_empresa );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();
    	// echo $this->dal->getMessage();

    	$ents = array();
    	while($row = pg_fetch_array($result))
    	{
    		$ent = new entity_public_controle_geracao_cobranca();
    		$ent->dt_confirmacao = $row["dt_confirmacao"];
    		$ent->usuario_confirmacao = $row["usuario_confirmacao"];
    		$ent->tot_internet_confirm = $row["tot_internet_confirm"];
    		$ent->tot_bdl_confirm = $row["tot_bdl_confirm"];
    		$ent->tot_cheque_confirm = $row["tot_cheque_confirm"];
    		$ent->tot_deposito_confirm = $row["tot_deposito_confirm"];
    		$ent->tot_debito_cc_confirm = $row["tot_debito_cc_confirm"];
    		$ent->vlr_cheque_confirm = $row["vlr_cheque_confirm"];
    		$ent->vlr_deposito_confirm = $row["vlr_deposito_confirm"];
    		$ent->vlr_debito_cc_confirm = $row["vlr_debito_cc_confirm"];
    		$ent->tot_folha_confirm = $row["tot_folha_confirm"];
    		$ent->vlr_folha_confirm = $row["vlr_folha_confirm"];

    		$ents[ sizeof($ents) ] = $ent;
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_sinpro.controle_geracao_cobranca__confirmacao__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ents;
    }

    /**
     * Criar array com resultados da query
     * 
     * @param $params Array com todos parametros necessбrios para formar a consulta
     *            $params['mes'] 
     *            $params['ano']
     * @return array(new entity_public_controle_geracao_cobranca()) Coleзгo do objeto entity_public_controle_geracao_cobranca
     */
    public function controle_geracao_cobranca__geracao__get( $params, $cd_empresa=8 )
    {
    	$this->dal->createQuery("

             SELECT   to_char(dt_geracao, 'DD/MM/YYYY') as dt_geracao
             		, usuario_geracao
	                , coalesce( tot_internet_gerado, 0 ) as tot_internet_gerado
					, coalesce( tot_bdl_gerado, 0 ) as tot_bdl_gerado
					, coalesce( tot_cheque_gerado, 0 ) as tot_cheque_gerado
					, coalesce( tot_deposito_gerado, 0 ) as tot_deposito_gerado
					, coalesce( tot_debito_cc_gerado, 0 ) as tot_debito_cc_gerado
					, coalesce( vlr_internet_gerado, 0 ) as vlr_internet_gerado
					, coalesce( vlr_bdl_gerado, 0 ) as vlr_bdl_gerado
					, coalesce( vlr_cheque_gerado, 0 ) as vlr_cheque_gerado
					, coalesce( vlr_deposito_gerado, 0 ) as vlr_deposito_gerado
					, coalesce( vlr_debito_cc_gerado, 0 ) as vlr_debito_cc_gerado
					, coalesce( tot_folha_gerado, 0 ) as tot_folha_gerado
					, coalesce( vlr_folha_gerado, 0 ) as vlr_folha_gerado
			   FROM public.controle_geracao_cobranca
			  WHERE cd_plano = {cd_plano}
			    AND cd_empresa = {cd_empresa}
			  	AND mes_competencia = {mes_competencia}
			    AND ano_competencia = {ano_competencia}
			    AND dt_geracao IS NOT NULL;

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", $cd_empresa );
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
    		$ent->tot_cheque_gerado = $row["tot_cheque_gerado"];
    		$ent->tot_deposito_gerado = $row["tot_deposito_gerado"];
    		$ent->tot_debito_cc_gerado = $row["tot_debito_cc_gerado"];
    		$ent->vlr_internet_gerado = $row["vlr_internet_gerado"];
    		$ent->vlr_bdl_gerado = $row["vlr_bdl_gerado"];
    		$ent->vlr_cheque_gerado = $row["vlr_cheque_gerado"];
    		$ent->vlr_deposito_gerado = $row["vlr_deposito_gerado"];
    		$ent->vlr_debito_cc_gerado = $row["vlr_debito_cc_gerado"];
    		$ent->tot_folha_gerado = $row["tot_folha_gerado"];
    		$ent->vlr_folha_gerado = $row["vlr_folha_gerado"];

    		$ents[ sizeof($ents) ] = $ent;
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_sinpro.controle_geracao_cobranca__geracao__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ents;
    }

    /**
     * Criar array com resultados da query
     * 
     * @param $params Array com todos parametros necessбrios para formar a consulta
     *            $params['mes'] 
     *            $params['ano']
     * @return array(new entity_public_controle_geracao_cobranca()) Coleзгo do objeto entity_public_controle_geracao_cobranca
     */
    public function controle_geracao_cobranca__envio__get( $params, $cd_empresa=8 )
    {
    	$this->dal->createQuery("

             SELECT   

             		  usuario_envio_bdl
             		, to_char(dt_envio_bdl, 'DD/MM/YYYY') AS dt_envio_bdl
	                , tot_bdl_enviado
	                , vlr_bdl_enviado

	                , usuario_envio_debito_cc
	                , to_char(dt_envio_debito_cc, 'DD/MM/YYYY') AS dt_envio_debito_cc
	                , tot_debito_cc_enviado
	                , vlr_debito_cc_enviado

			   FROM

			   		public.controle_geracao_cobranca

			  WHERE

			  		cd_plano = {cd_plano}
			    AND cd_empresa = {cd_empresa}
			  	AND mes_competencia = {mes_competencia}
			    AND ano_competencia = {ano_competencia}
			    AND dt_envio_bdl IS NOT NULL;

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", $cd_empresa );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();

    	$ents = array();
    	while($row = pg_fetch_array($result))
    	{
    		$ent = new entity_public_controle_geracao_cobranca();

    		$ent->usuario_envio_bdl = $row["usuario_envio_bdl"];
    		$ent->dt_envio_bdl = $row["dt_envio_bdl"];
    		$ent->tot_bdl_enviado = $row["tot_bdl_enviado"];
    		$ent->vlr_bdl_enviado = $row["vlr_bdl_enviado"];
    		
    		$ent->usuario_envio_debito_cc = $row["usuario_envio_debito_cc"];
    		$ent->tot_debito_cc_enviado = $row["tot_debito_cc_enviado"];
    		$ent->vlr_debito_cc_enviado = $row["vlr_debito_cc_enviado"];
    		$ent->dt_envio_debito_cc = $row["dt_envio_debito_cc"];

    		$ents[ sizeof($ents) ] = $ent;
    	}

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em helper_ado_contribuicao_sinpro.controle_geracao_cobranca__envio__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ents;
    }

	/**
     * Totais do primeiro pagamento BDL/BCO devolve um array com um Count e um calculo baseado em SUMs da query formada
     * pelas tabelas inscritos_internet, controle_geracao_cobranca, taxas e pacotes
     * 
     * A possibilidade da consulta filtrar o debito em conta surgiu em manutenзгo e й 
     * possнvel pela passagem de $params['forma_pagamento']='BCO'
     * 
     * @param array() $params array com filtros utilizados na query
     *                $param['mes']
     *                $param['ano']
     *                $param['forma_pagamento'] {'BDL', 'BCO'}
     * @param enum_public_patrocinadoras $cd_empresa  Padrгo enum_public_patrocinadoras::SINPRO 
	 *
     * @return array(contador, valor) Array com os valores retornados na query
     */
    public function totais_bdl__get($params, $cd_empresa=8)
    {
    	$this->dal->createQuery("

    		-- TOTAIS BDL
    	
			SELECT COUNT(*) AS contador, SUM(   COALESCE(cpr.valor,0) + COALESCE(riscos.valor_risco,0) + COALESCE(dcc.vlr_debito,0)   ) AS valor

			  FROM public.protocolos_participantes p

			LEFT JOIN (SELECT ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia, sum(ap.premio) as valor_risco
				FROM public.apolices_participantes ap
				JOIN public.apolices a
				ON a.cd_apolice = ap.cd_apolice
				WHERE ap.dt_exclusao           IS NULL
				group by ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia
				) as riscos ON riscos.cd_empresa = p.cd_empresa
			   AND riscos.cd_registro_empregado = p.cd_registro_empregado
			   AND riscos.seq_dependencia       = p.seq_dependencia
 
			  JOIN public.calendarios_planos cp
			    ON cp.cd_empresa = p.cd_empresa

			  JOIN public.controle_geracao_cobranca cgc
			    ON cgc.cd_empresa = cp.cd_empresa
			   AND cgc.cd_plano   = cp.cd_plano

			  LEFT JOIN public.contribuicoes_programadas cpr
			    ON cpr.cd_empresa            = p.cd_empresa
			   AND cpr.cd_registro_empregado = p.cd_registro_empregado
			   AND cpr.seq_dependencia       = p.seq_dependencia
			   AND cpr.dt_confirma_opcao     IS NOT NULL
			   AND cpr.dt_confirma_canc      IS NULL

			  LEFT JOIN public.debito_conta_contribuicao dcc
			    ON dcc.cd_empresa            = p.cd_empresa
			   AND dcc.cd_registro_empregado = p.cd_registro_empregado
			   AND dcc.seq_dependencia       = p.seq_dependencia
			   AND dcc.dt_confirma_opcao     IS NOT NULL
			   AND dcc.dt_confirma_canc      IS NULL

	     LEFT JOIN titulares_planos tp
	            ON tp.cd_empresa = p.cd_empresa
	           AND tp.cd_registro_empregado = p.cd_registro_empregado
	           AND tp.seq_dependencia = p.seq_dependencia
	           AND tp.dt_ingresso_plano = ( SELECT max(tp1.dt_ingresso_plano) as max 
											  FROM titulares_planos tp1 
											 WHERE tp1.cd_empresa=p.cd_empresa 
											   AND tp1.cd_registro_empregado=p.cd_registro_empregado 
											   AND tp1.seq_dependencia=p.seq_dependencia )

			 WHERE cp.cd_empresa       = {cd_empresa}
			   AND cp.cd_plano         = {cd_plano}
			   AND cp.dt_competencia   = '{ano_competencia}-{mes_competencia}-01'
			   AND cgc.mes_competencia = {mes_competencia}
			   AND cgc.ano_competencia = {ano_competencia}
			   AND cgc.dt_geracao      IS NOT NULL
			   AND p.dt_confirma       BETWEEN cp.dt_inicio AND cp.dt_fim
			   AND p.forma_pagamento   = '{forma_pagamento}'
			   AND 0 = CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", $cd_empresa );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	if(isset($params['forma_pagamento']))
    	{
    		$this->dal->setAttribute( "{forma_pagamento}", $params["forma_pagamento"] );
    	}
    	else
    	{
    		$this->dal->setAttribute( "{forma_pagamento}", "BDL" );
    	}

    	$result = $this->dal->getResultset();

    	if($row=pg_fetch_array($result)) 
    	{
    		if($row['valor']=='') $valor=0; else $valor=$row['valor'];
    		$ent = array( 'contador'=>$row['contador'], 'valor'=>$valor );
    	}

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em helper_ado_contribuicao_sinpro.totais_bdl__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ent;
    }

    /**
     * Totais do pagamento mensal de bdl devolve um array com 
     * um Count e um calculo baseado em SUMs da query formada
     * pelas tabelas public.bloqueto e public.participantes
     * 
     * @param $params array com filtros utilizados na query
     *                $param['mes']
     *                $param['ano']
     * 
     * @return array(contador, valor) Array com os valores retornados na query
     */
    public function totais_mensal_bdl__get($params, $cd_empresa=8)
    {
    	$this->dal->createQuery("

            -- MENSAL PAGAMENTO
			SELECT COUNT(DISTINCT p.CD_REGISTRO_EMPREGADO) as contador
			     , SUM(valor_lancamento) AS valor
			  FROM public.bloqueto b
			  JOIN participantes p 
			    ON b.cd_empresa            = p.cd_empresa 
			   AND b.cd_registro_empregado = p.cd_registro_empregado 
			   AND b.seq_dependencia       = p.seq_dependencia
			 WHERE b.mes_competencia           = {mes_competencia}
			   AND b.ano_competencia           = {ano_competencia}
			   AND b.data_retorno              IS NULL
			   AND p.cd_plano                 = {cd_plano}
			   AND p.cd_empresa               = {cd_empresa}
			   AND b.codigo_lancamento IN ({codigo_lancamento})

		");

    	// cуdigo de lanзamento de BDL caso nгo seja informado no parametro
    	$codigo_lancamento = "2450,2451,2452";
    	if( isset($params['codigo_lancamento']) )
    	{
    		if(trim($params['codigo_lancamento'])!="")
    		{
    			// cуdigos de lanзamento informados por parametro
    			$codigo_lancamento = $params['codigo_lancamento'];
    		}
    	}

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", $cd_empresa );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$this->dal->setAttribute( "{codigo_lancamento}", $codigo_lancamento );

    	$result = $this->dal->getResultset(true);

    	if($row=pg_fetch_array($result))
    	{
    		if($row['valor']=='') $valor=0; else $valor=$row['valor'];
    		$ent = array( 'contador'=>$row['contador'], 'valor'=>$valor );
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_sinpro.totais_bdl__get() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ent;
    }

    public function total_emails_enviar_primeiro( $params, $cd_empresa=8 )
	{
		$this->dal->createQuery("

			/* total_emails_enviar_primeiro */

	        SELECT COUNT(*) AS contador
			  FROM public.protocolos_participantes p
			  JOIN public.participantes part
			    ON part.cd_empresa = p.cd_empresa AND part.cd_registro_empregado = p.cd_registro_empregado AND part.seq_dependencia = p.seq_dependencia
			  JOIN public.calendarios_planos cp
			    ON cp.cd_empresa = p.cd_empresa
			  JOIN public.controle_geracao_cobranca cgc
			    ON cgc.cd_empresa = cp.cd_empresa
			   AND cgc.cd_plano   = cp.cd_plano
			  LEFT JOIN public.contribuicoes_programadas cpr
			    ON cpr.cd_empresa            = p.cd_empresa
			   AND cpr.cd_registro_empregado = p.cd_registro_empregado
			   AND cpr.seq_dependencia       = p.seq_dependencia
			   AND cpr.dt_confirma_opcao     IS NOT NULL
			   AND cpr.dt_confirma_canc      IS NULL
	     LEFT JOIN titulares_planos tp
	            ON tp.cd_empresa = p.cd_empresa
	           AND tp.cd_registro_empregado = p.cd_registro_empregado
	           AND tp.seq_dependencia = p.seq_dependencia
	           AND tp.dt_ingresso_plano = ( SELECT max(tp1.dt_ingresso_plano) as max 
											  FROM titulares_planos tp1 
											 WHERE tp1.cd_empresa=p.cd_empresa 
											   AND tp1.cd_registro_empregado=p.cd_registro_empregado 
											   AND tp1.seq_dependencia=p.seq_dependencia )
			   WHERE cp.cd_empresa     = {cd_empresa}
			   AND cp.cd_plano         = {cd_plano}
			   AND cp.dt_competencia   = '{ano_competencia}-{mes_competencia}-01'
			   AND cgc.mes_competencia = {mes_competencia}
			   AND cgc.ano_competencia = {ano_competencia}
			   AND cgc.dt_geracao      IS NOT NULL
			   AND p.dt_confirma       BETWEEN cp.dt_inicio AND cp.dt_fim
			   AND p.forma_pagamento   = '{forma_pagamento}'
			   AND COALESCE(email, email_profissional) IS NOT NULL
			   AND 0 = CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", $cd_empresa );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );
    	if( isset($params['forma_pagamento']) )
    	{
    		$forma_pagamento = $params['forma_pagamento'];
    	}
    	else
    	{
    		$forma_pagamento = 'BDL';
    	}
    	$this->dal->setAttribute( "{forma_pagamento}", $forma_pagamento );

    	$result = $this->dal->getResultset();

    	if($row=pg_fetch_array($result))
    	{
    		$ret = $row['contador'];
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_sinpro.total_emails_enviar_primeiro() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ret;
	}

    public function lista_sem_emails_primeiro( $params, $cd_empresa=8 )
	{
		$this->dal->createQuery("

	        SELECT part.cd_empresa, part.cd_registro_empregado, part.seq_dependencia, part.nome
			  FROM public.protocolos_participantes p
			  JOIN public.participantes part
			    ON part.cd_empresa = p.cd_empresa AND part.cd_registro_empregado = p.cd_registro_empregado AND part.seq_dependencia = p.seq_dependencia
			  JOIN public.calendarios_planos cp
			    ON cp.cd_empresa = p.cd_empresa
			  JOIN public.controle_geracao_cobranca cgc
			    ON cgc.cd_empresa = cp.cd_empresa
			   AND cgc.cd_plano   = cp.cd_plano
			  LEFT JOIN public.contribuicoes_programadas cpr
			    ON cpr.cd_empresa            = p.cd_empresa
			   AND cpr.cd_registro_empregado = p.cd_registro_empregado
			   AND cpr.seq_dependencia       = p.seq_dependencia
			   AND cpr.dt_confirma_opcao     IS NOT NULL
			   AND cpr.dt_confirma_canc      IS NULL
	     LEFT JOIN titulares_planos tp
	            ON tp.cd_empresa = p.cd_empresa
	           AND tp.cd_registro_empregado = p.cd_registro_empregado
	           AND tp.seq_dependencia = p.seq_dependencia
	           AND tp.dt_ingresso_plano = ( SELECT max(tp1.dt_ingresso_plano) as max 
											  FROM titulares_planos tp1 
											 WHERE tp1.cd_empresa=p.cd_empresa 
											   AND tp1.cd_registro_empregado=p.cd_registro_empregado 
											   AND tp1.seq_dependencia=p.seq_dependencia )
			   WHERE cp.cd_empresa     = {cd_empresa}
			   AND cp.cd_plano         = {cd_plano}
			   AND cp.dt_competencia   = '{ano_competencia}-{mes_competencia}-01'
			   AND cgc.mes_competencia = {mes_competencia}
			   AND cgc.ano_competencia = {ano_competencia}
			   AND cgc.dt_geracao      IS NOT NULL
			   AND p.dt_confirma       BETWEEN cp.dt_inicio AND cp.dt_fim
			   AND p.forma_pagamento   = '{forma_pagamento}'
			   AND COALESCE(email, email_profissional) IS NULL
			   AND 0 = CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", $cd_empresa );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );
    	if( isset($params['forma_pagamento']) )
    	{
    		$forma_pagamento = $params['forma_pagamento'];
    	}
    	else
    	{
    		$forma_pagamento = 'BDL';
    	}
    	$this->dal->setAttribute( "{forma_pagamento}", $forma_pagamento );

    	$result = $this->dal->getResultset();

    	$ret = array();
    	while($row=pg_fetch_array($result))
    	{
    		$ret[sizeof($ret)] = $row;
    	}

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em helper_ado_contribuicao_sinpro.lista_sem_emails_primeiro() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ret;
	}

	public function total_emails_enviar_mensal($params, $cd_empresa=8)
	{
		$this->dal->createQuery("

		/* total_emails_enviar_mensal */

			SELECT count(*) as contador
			  FROM participantes p 
			 WHERE 
			       p.cd_plano = {cd_plano} 
			   AND p.cd_empresa = {cd_empresa} 
			   AND coalesce(email, email_profissional) is not null
			   AND EXISTS 
				     (
				     SELECT * FROM public.bloqueto b 
				      WHERE b.ano_competencia = {ano_competencia} 
					AND b.mes_competencia = {mes_competencia}
					AND b.data_retorno IS NULL 
					AND b.cd_empresa = p.cd_empresa 
					AND b.cd_registro_empregado = p.cd_registro_empregado 
					AND b.seq_dependencia = p.seq_dependencia 
					AND p.cd_plano = b.cd_plano
					AND b.codigo_lancamento IN (2450,2451,2452)
				     )

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", $cd_empresa );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();

    	if($row=pg_fetch_array($result))
    	{
    		$ret = $row['contador'];
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_sinpro.total_emails_enviar_mensal() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $ret;
	}

	public function lista_sem_email_mensal($params,$cd_empresa=8)
	{
		$this->dal->createQuery("

			SELECT p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia, p.nome
			  FROM participantes p 
			 WHERE 
			       p.cd_plano = {cd_plano} 
			   AND p.cd_empresa = {cd_empresa} 
			   AND coalesce(email, email_profissional) IS NULL
			   AND EXISTS 
				     (
				     SELECT * FROM public.bloqueto b 
				      WHERE b.ano_competencia = {ano_competencia} 
						AND b.mes_competencia = {mes_competencia}
						AND b.data_retorno IS NULL 
						AND b.cd_empresa = p.cd_empresa 
						AND b.cd_registro_empregado = p.cd_registro_empregado 
						AND b.seq_dependencia = p.seq_dependencia 
						AND p.cd_plano = b.cd_plano
						AND b.codigo_lancamento IN (2450,2451,2452)
				     )

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", $cd_empresa );
    	$this->dal->setAttribute( "{mes_competencia}", $params["mes"] );
    	$this->dal->setAttribute( "{ano_competencia}", $params["ano"] );

    	$result = $this->dal->getResultset();

    	$ret = array();
    	while($row=pg_fetch_array($result))
    	{
    		$ret[sizeof($ret)] = $row;
    	}

        if ($this->dal->haveError())
        {
            throw new Exception( 'Erro em helper_ado_contribuicao_sinpro.lista_sem_email_mensal() ao executar comando SQL de consulta. ' . $this->dal->getMessage() );
        }

    	return $ret;
	}

    public function ja_realizou_primeiro_pagamento( $cd_empresa, $cd_registro_empregado, $seq_dependencia )
    {
    	$this->dal->createQuery("

           SELECT COUNT(*) as contador
		     FROM titulares_planos tp 
		    WHERE tp.cd_empresa = {cd_empresa}
              AND tp.cd_registro_empregado = {cd_registro_empregado}
              AND tp.seq_dependencia = {seq_dependencia}
              AND tp.dt_ingresso_plano = (
              			SELECT max(tp1.dt_ingresso_plano) as max 
						 FROM titulares_planos tp1 
						WHERE tp1.cd_empresa=tp.cd_empresa 
						  AND tp1.cd_registro_empregado=tp.cd_registro_empregado 
						  AND tp1.seq_dependencia=tp.seq_dependencia 
						  )
   		      AND tp.dt_ingresso_plano IS NOT NULL

		");

    	$this->dal->setAttribute( "{cd_empresa}", $cd_empresa );
    	$this->dal->setAttribute( "{cd_registro_empregado}", $cd_registro_empregado );
    	$this->dal->setAttribute( "{seq_dependencia}", $seq_dependencia );

    	$result = $this->dal->getResultset();

    	$bres = false;
    	if( $row = pg_fetch_array($result) )
    	{
    		$bres = ( $row['contador']>0 );
    	}

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em helper_ado_contribuicao_sinpro.ja_realizou_primeiro_pagamento() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $bres;
    }

    public function participante_com_debito_conta( $cd_empresa, $cd_registro_empregado, $seq_dependencia )
    {
    	$bres = false;
    	$this->dal->createQuery("

           SELECT COUNT(*) as contador
			 FROM public.debito_conta_contribuicao
			WHERE cd_empresa={cd_empresa}
			  AND cd_registro_empregado={cd_registro_empregado}
			  AND seq_dependencia={seq_dependencia}
			  AND dt_confirma_opcao IS NOT NULL
			  AND dt_confirma_canc IS NULL
			  AND forma_pagamento = 'BCO'

		");

    	$this->dal->setAttribute( "{cd_empresa}", $cd_empresa );
    	$this->dal->setAttribute( "{cd_registro_empregado}", $cd_registro_empregado );
    	$this->dal->setAttribute( "{seq_dependencia}", $seq_dependencia );

    	$result = $this->dal->getResultset();

    	if( $row = pg_fetch_array($result) )
    	{
    		$bres = ( intval($row['contador'])>0 );
    	}

        if ($this->dal->haveError())
        {
            throw new Exception('Erro em helper_ado_contribuicao_sinpro.participante_com_debito_conta() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return $bres;
    }

    /**
     * Confere se o participante й de primeiro pagamento ou nгo
     * 
     * @return bool True indica que й um participante de primeiro pagamento. False indica que nгo й participante de primeiro pagamento. 
     */
    public function participante_primeiro_pagamento( $RE, $SEQ )
    {
    	$ret = 0;
    	$this->dal->createQuery("

	        SELECT COUNT(*) AS contador
			  FROM public.protocolos_participantes p
			  JOIN public.participantes part
			    ON part.cd_empresa = p.cd_empresa AND part.cd_registro_empregado = p.cd_registro_empregado AND part.seq_dependencia = p.seq_dependencia
			  JOIN public.calendarios_planos cp
			    ON cp.cd_empresa = p.cd_empresa
			  JOIN public.controle_geracao_cobranca cgc
			    ON cgc.cd_empresa = cp.cd_empresa
			   AND cgc.cd_plano   = cp.cd_plano
	     LEFT JOIN public.contribuicoes_programadas cpr
			    ON cpr.cd_empresa            = p.cd_empresa
			   AND cpr.cd_registro_empregado = p.cd_registro_empregado
			   AND cpr.seq_dependencia       = p.seq_dependencia
			   AND cpr.dt_confirma_opcao     IS NOT NULL
			   AND cpr.dt_confirma_canc      IS NULL
	     LEFT JOIN titulares_planos tp
	            ON tp.cd_empresa = p.cd_empresa
	           AND tp.cd_registro_empregado = p.cd_registro_empregado
	           AND tp.seq_dependencia = p.seq_dependencia
	           AND tp.dt_ingresso_plano = ( SELECT max(tp1.dt_ingresso_plano) as max 
											  FROM titulares_planos tp1 
											 WHERE tp1.cd_empresa=p.cd_empresa 
											   AND tp1.cd_registro_empregado=p.cd_registro_empregado 
											   AND tp1.seq_dependencia=p.seq_dependencia )
			   WHERE cp.cd_empresa     = {cd_empresa}
			   AND cp.cd_plano         = {cd_plano}
			   AND cgc.mes_competencia = extract( month from dt_competencia )
			   AND cgc.ano_competencia = extract( year from dt_competencia )
			   AND part.cd_empresa = {cd_empresa}
			   AND part.cd_registro_empregado = {cd_registro_empregado}
			   AND part.seq_dependencia = {seq_dependencia}
			   AND cgc.dt_geracao      IS NOT NULL
			   AND cgc.dt_envio_internet IS NOT NULL
			   AND p.dt_confirma       BETWEEN cp.dt_inicio AND cp.dt_fim
			   AND p.forma_pagamento   = 'BDL'
			   AND 0 = CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END

		");

    	$this->dal->setAttribute( "{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
    	$this->dal->setAttribute( "{cd_empresa}", enum_public_patrocinadoras::SINPRO );
    	$this->dal->setAttribute( "{cd_registro_empregado}", $RE );
    	$this->dal->setAttribute( "{seq_dependencia}", $SEQ );

    	$result = $this->dal->getResultset();

    	if($row=pg_fetch_array($result))
    	{
    		$ret = $row['contador'];
    	}

        if ($this->dal->haveError()) {
            throw new Exception('Erro em helper_ado_contribuicao_sinpro.participante_primeiro_pagamento() ao executar comando SQL de consulta. '.$this->dal->getMessage());
        }

    	return ($ret>0);
    }
    
    /**
     * Retorna classe hashtable_collection com re e competencia criptografada com md5 usando funзгo definida na base
     */
    public function re_competencia_md5( $args )
    {
    	$this->dal->createQuery("

	        SELECT funcoes.cripto_re( {cd_empresa}, {cd_registro_empregado}, {seq_dependencia} ) as re_md5
	             , funcoes.cripto_mes_ano({mes_competencia}, {ano_competencia}) as comp_md5

		");

    	$this->dal->setAttribute( "{cd_empresa}", $args['EMP'] );
    	$this->dal->setAttribute( "{cd_registro_empregado}", $args['RE'] );
    	$this->dal->setAttribute( "{seq_dependencia}", $args['SEQ'] );
    	$this->dal->setAttribute( "{mes_competencia}", $args['MES'] );
    	$this->dal->setAttribute( "{ano_competencia}", $args['ANO'] );

    	$result = $this->dal->getResultset();

    	$collection = new hashtable_collection();
    	if($row=pg_fetch_array($result))
    	{
	    	$collection->add('re', $row['re_md5']);
	    	$collection->add('comp', $row['comp_md5']);
    	}

    	return $collection;
    }
}
?>