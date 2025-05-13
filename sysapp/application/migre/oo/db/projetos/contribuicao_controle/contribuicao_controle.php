<?php
class contribuicao_controle
{
	/**
	 * contribuicao_controle::select_1($cd_empresa, $ano, $mes)
	 * 
	 * Conta os registros da tabela contribuicao_controle
	 * 
	 * @param int $ano Ano de competência em contribuicao_controle.nr_ano_competencia
	 * @param int $mes Mês de competência em contribuicao_controle.nr_mes_competencia
	 * @param int $cd_empresa Código da empresa em contribuicao_controle.cd_empresa
	 * @param array(enum_projetos_contribuicao_controle_tipo) $cd_contribuicao_controle_tipo Lista de tipos de contribuições
	 * 
	 * @return array($row) onde row é um array() com as colunas da seleção
	 */
	public static function quantos( $nr_ano_competencia, $nr_mes_competencia, $cd_empresa, $cd_contribuicao_controle_tipo )
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$sql = "

			-- contribuicao_controle::quantos();

			SELECT

				COUNT(*) as quantos

			FROM

				projetos.contribuicao_controle

			WHERE

				nr_ano_competencia = {nr_ano_competencia}
				AND nr_mes_competencia = {nr_mes_competencia}
				AND cd_empresa = {cd_empresa}
				{cd_contribuicao_controle_tipo};

		";

		$sep = "";
		foreach($cd_contribuicao_controle_tipo as $tipo)
		{
			$ext .= $sep . "'" . $db->escape($tipo) . "'";
			$sep = ", ";
		}
		$sql = str_replace( "{cd_contribuicao_controle_tipo}", " AND cd_contribuicao_controle_tipo IN (" . $ext . ") ", $sql );

		$db->setSQL( $sql );
		$db->setParameter("{nr_mes_competencia}", $nr_mes_competencia);
		$db->setParameter("{nr_ano_competencia}", $nr_ano_competencia);
		$db->setParameter("{cd_empresa}", $cd_empresa);

		$r = $db->get();

		return $r[0]['quantos'];
	}

	/**
	 * contribuicao_controle::select_1($cd_empresa, $ano, $mes)
	 * 
	 * Seleciona a tabela contribuicao_controle e alguns campos de tabelas relacionadas
	 * 
	 * @param string $tipo Pode ser "mensal" ou "primeiro" ou "atraso"
	 * @param int $cd_empresa Código da empresa em contribuicao_controle.cd_empresa
	 * @param int $ano Ano de competência em contribuicao_controle.nr_ano_competencia
	 * @param int $mes Mês de competência em contribuicao_controle.nr_mes_competencia
	 * 
	 * @return array($row) onde row é um array() com as colunas da seleção
	 */
	public static function select_1($tipo, $cd_empresa, $ano, $mes)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		if($tipo=="mensal")
		{
			$t[0] = enum_projetos_contribuicao_controle_tipo::PAGAMENTO_MENSAL_BDL;
			$t[1] = enum_projetos_contribuicao_controle_tipo::PAGAMENTO_MENSAL_DEBITO_CONTA_CORRENTE;
			$cd_contribuicao_controle_tipo = "'" . $t[0] . "', '" . $t[1] . "'" ;
		}
		if($tipo=="primeiro")
		{
			$t[0] = enum_projetos_contribuicao_controle_tipo::PRIMEIRO_PAGAMENTO_BDL;
			$t[1] = enum_projetos_contribuicao_controle_tipo::PRIMEIRO_PAGAMENTO_DEBITO_CONTA_CORRENTE;
			$cd_contribuicao_controle_tipo = "'" . $t[0] . "', '" . $t[1] . "'" ;
		}
		if($tipo=="atraso")
		{
			$t[0] = enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DEBITO_CONTA_CORRENTE;
			$t[1] = enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DESCONTO_FOLHA;
			$t[2] = enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_PRIMEIRO_PAGAMENTO;
			$cd_contribuicao_controle_tipo = "'" . $t[0] . "', '" . $t[1] . "', '" . $t[2] . "'" ;
		}

		$db->setSQL( "

			SELECT 

				cc.*
				, pa.nome
				, COALESCE(pa.email, pa.email_profissional) as email
				, cct.ds_contribuicao_controle_tipo

			FROM

				projetos.contribuicao_controle cc
				JOIN public.participantes pa 
				ON cc.cd_empresa=pa.cd_empresa AND cc.cd_registro_empregado=pa.cd_registro_empregado AND cc.seq_dependencia=pa.seq_dependencia

				JOIN projetos.contribuicao_controle_tipo cct
				ON cct.cd_contribuicao_controle_tipo=cc.cd_contribuicao_controle_tipo

			WHERE 

				cc.nr_ano_competencia = {nr_ano_competencia} 
				AND cc.nr_mes_competencia = {nr_mes_competencia}
				AND cc.cd_empresa = {cd_empresa}
				AND cc.cd_contribuicao_controle_tipo IN ($cd_contribuicao_controle_tipo);

		" );

		#$db->setParameter( "{cd_empresa}", enum_public_patrocinadoras::SINPRO );
		$db->setParameter( "{cd_empresa}", $cd_empresa );
		$db->setParameter( "{nr_ano_competencia}", $ano );
		$db->setParameter( "{nr_mes_competencia}", $mes );

		$r = $db->get( true );

		$collection = array();
		foreach( $r as $row )
		{
			$collection[sizeof($collection)] = $row;
		}

		return $collection;
	}

	/**
	 * Criar_cobranca_1ro_pagamento($mes, $ano)
	 * 
	 * Criar cobrança com base nas regras de negócio definidas
	 * para o projeto SINPRORS.
	 * 
	 * O Insert é realizado através de consulta envolvendo as tabelas:
	 * 
	 * - protocolos_participantes
	 * - calendarios_planos
	 * - controle_geracao_cobranca
	 * - contribuicoes_programadas
	 * - titulares_planos
	 * 
	 * @param int $mes Mês de competência
	 * @param int $ano Ano de competência
	 * 
	 * @return boolean True ou False para Sucesso ou Falha na transação
	 * 
	 */
	public static function criar_cobranca_1ro_pagamento_sinprors($mes, $ano, $cd_empresa=8)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			INSERT INTO projetos.contribuicao_controle(

				cd_empresa
	            , cd_registro_empregado
	            , seq_dependencia
	            , nr_ano_competencia
	            , nr_mes_competencia
	            , cd_contribuicao_controle_tipo
	            , dt_controle
	            , cd_usuario

			)

			SELECT

				part.cd_empresa
				, part.cd_registro_empregado
				, part.seq_dependencia
				, '{ano_competencia}' as ano_competencia
				, '{mes_competencia}' as mes_competencia
				, CASE WHEN p.forma_pagamento = 'BDL' THEN '1PBDL' WHEN p.forma_pagamento = 'BCO' THEN '1PDCC' END AS cd_contribuicao_controle_tipo 
				, current_timestamp as dt_controle
				, {cd_usuario} as cd_usuario

			FROM

				       public.protocolos_participantes p
				  JOIN public.participantes part
				    ON part.cd_empresa = p.cd_empresa 
				   AND part.cd_registro_empregado = p.cd_registro_empregado 
				   AND part.seq_dependencia = p.seq_dependencia
				    
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

			WHERE

				cp.cd_empresa     = {cd_empresa}
			    AND cp.cd_plano         = {cd_plano}
			    AND cp.dt_competencia   = '{ano_competencia}-{mes_competencia}-01'
			    AND cgc.mes_competencia = {mes_competencia}
			    AND cgc.ano_competencia = {ano_competencia}
			    AND cgc.dt_geracao      IS NOT NULL
			    AND p.dt_confirma       BETWEEN cp.dt_inicio AND cp.dt_fim
			    AND p.forma_pagamento   IN ('BDL', 'BCO')
			    AND COALESCE(email, email_profissional) IS NOT NULL
			    AND 0 = CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END

		" );
		
		$db->setParameter("{ano_competencia}", $ano);
		$db->setParameter("{mes_competencia}", $mes);
		$db->setParameter("{cd_empresa}", $cd_empresa );
		$db->setParameter("{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
		$db->setParameter("{cd_usuario}", $_SESSION['Z'] );
		
		$b = $db->execute(true);
		
		if( $db->haveError() )
		{
			throw new Exception( "Erro no método contribuicao_controle.criar_cobranca_1ro_pagamento_sinprors - { " . $db->getMessage() . ' } ' );
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Criar_cobranca_mensal($mes, $ano)
	 * 
	 * Criar cobrança com base nas regras de negócio definidas
	 * para o projeto SINPRORS.
	 * 
	 * O Insert é realizado através de consulta envolvendo as tabelas:
	 * 
	 * - participantes
	 * - bloqueto
	 * - cobrancas
	 * 
	 * @param int $mes Mês de competência
	 * @param int $ano Ano de competência
	 * 
	 * @return boolean True ou False para Sucesso ou Falha na transação
	 * 
	 */
	public static function criar_cobranca_mensal_sinprors($mes, $ano, $cd_empresa=8)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			INSERT INTO projetos.contribuicao_controle
			(
				cd_empresa
	            , cd_registro_empregado
	            , seq_dependencia
	            , nr_ano_competencia
	            , nr_mes_competencia
	            , cd_contribuicao_controle_tipo
	            , dt_controle
	            , cd_usuario
			)

			SELECT

				p.cd_empresa
				, p.cd_registro_empregado
				, p.seq_dependencia
				, {ano_competencia} as ano_competencia
				, {mes_competencia} as mes_competencia
				, 'PMBDL' AS cd_contribuicao_controle_tipo 
				, current_timestamp as dt_controle
				, {cd_usuario} as cd_usuario

			FROM

				participantes p 

			WHERE 

				p.cd_plano = {cd_plano} 
				AND p.cd_empresa = {cd_empresa} 
				AND coalesce(email, email_profissional) is not null
				AND EXISTS 
				(
					SELECT *
					FROM public.bloqueto b
					WHERE b.ano_competencia = {ano_competencia}
					AND b.mes_competencia = {mes_competencia}
					AND b.data_retorno IS NULL
					AND b.cd_empresa = p.cd_empresa
					AND b.cd_registro_empregado = p.cd_registro_empregado
					AND b.seq_dependencia = p.seq_dependencia
					AND p.cd_plano = b.cd_plano
					AND b.codigo_lancamento IN (2450)
				)

			UNION
			
			SELECT 

				par.cd_empresa
				, par.cd_registro_empregado
				, par.seq_dependencia
				, {ano_competencia} as ano_competencia
				, {mes_competencia} as mes_competencia
				, 'PMDCC' AS cd_contribuicao_controle_tipo 
				, current_timestamp as dt_controle
				, {cd_usuario} as cd_usuario

			FROM 

				public.cobrancas cob
				JOIN public.participantes par 
				ON cob.cd_empresa=par.cd_empresa and cob.cd_registro_empregado=par.cd_registro_empregado and cob.seq_dependencia=par.seq_dependencia

			WHERE

				mes_competencia = {mes_competencia}
      			AND ano_competencia = {ano_competencia}
				AND cob.cd_empresa={cd_empresa}
				AND cob.codigo_lancamento IN (2460)
				AND date_trunc('month', cob.dt_lancamento) = (cob.ano_competencia || '-' || cob.mes_competencia || '-01')::date
				AND COALESCE(par.email, par.email_profissional) IS NOT NULL

		" );
		
		$db->setParameter("{ano_competencia}", $ano);
		$db->setParameter("{mes_competencia}", $mes);
		$db->setParameter("{cd_empresa}", $cd_empresa );
		$db->setParameter("{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
		$db->setParameter("{cd_usuario}", $_SESSION['Z'] );
		
		$b = $db->execute();
		
		if( $db->haveError() )
		{
			throw new Exception( "Erro no método contribuicao_controle.criar_cobranca_mensal_sinprors - { " . $db->getMessage() . ' } ' );
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Criar_cobranca_atraso($mes, $ano)
	 * 
	 * Criar cobrança com base nas regras de negócio definidas
	 * para o projeto SINPRORS.
	 * 
	 * O Insert é realizado através de consulta envolvendo as tabelas:
	 * 
	 * - participantes
	 * - bloqueto
	 * 
	 * @param int $mes Mês de competência
	 * @param int $ano Ano de competência
	 * 
	 * @return boolean True ou False para Sucesso ou Falha na transação
	 * 
	 */
	public static function criar_cobranca_atraso_sinprors($mes, $ano, $cd_empresa=8)
	{
		$db = new postgres();
		$db = DBFactory::createObject();
		$db->setSQL( "

			INSERT INTO projetos.contribuicao_controle
			(
				cd_empresa
	            , cd_registro_empregado
	            , seq_dependencia
	            , nr_ano_competencia
	            , nr_mes_competencia
	            , cd_contribuicao_controle_tipo
	            , dt_controle
	            , cd_usuario
			)

			SELECT DISTINCT 

				part.cd_empresa
				, part.cd_registro_empregado
				, part.seq_dependencia
				, {ano_lancamento}
				, {mes_lancamento}
				, CASE 	WHEN bloq.codigo_lancamento in ({codigo_folha}) THEN '{tipo_folha}' 
						WHEN bloq.codigo_lancamento in ({codigo_bco}) THEN '{tipo_bco}' END AS cd_contribuicao_controle_tipo
				, CURRENT_TIMESTAMP
				, {cd_usuario}

			FROM 

				public.bloqueto as bloq

				JOIN public.participantes AS part 
				ON bloq.cd_empresa=part.cd_empresa 
				AND bloq.cd_registro_empregado=part.cd_registro_empregado 
				AND bloq.seq_dependencia=part.seq_dependencia

			WHERE

				bloq.status IS NULL 
				AND bloq.data_retorno IS NULL
				AND to_date( bloq.ano_competencia::varchar || '/' || bloq.mes_competencia::varchar || '/01' , 'YYYY/MM/DD' ) < to_date( '{ano_lancamento}/{mes_lancamento}/01' , 'YYYY/MM/DD' )
				AND bloq.cd_empresa = {cd_empresa}
				AND bloq.codigo_lancamento IN ( {codigo_lancamento} )
				AND date_trunc('month', bloq.dt_lancamento) = '{ano_lancamento}-{mes_lancamento}-01'
				AND COALESCE(part.email, part.email_profissional) IS NOT NULL

			UNION

			SELECT

				part.cd_empresa
				, part.cd_registro_empregado
				, part.seq_dependencia
				, {ano_lancamento}
				, {mes_lancamento}
				, 'COB1P' AS cd_contribuicao_controle_tipo
				, CURRENT_TIMESTAMP
				, {cd_usuario}

			FROM 

				public.protocolos_participantes p
				JOIN public.participantes part
				ON part.cd_empresa=p.cd_empresa
				AND part.cd_registro_empregado=p.cd_registro_empregado
				AND part.seq_dependencia=p.seq_dependencia

				LEFT JOIN 
				(
					SELECT ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia, sum(ap.premio) as valor_risco
					FROM public.apolices_participantes ap
					JOIN public.apolices a
					ON a.cd_apolice = ap.cd_apolice
					WHERE ap.dt_exclusao IS NULL
					group by ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia
				) AS riscos
				ON riscos.cd_empresa = p.cd_empresa 
				AND riscos.cd_registro_empregado = p.cd_registro_empregado 
				AND riscos.seq_dependencia = p.seq_dependencia

				JOIN public.calendarios_planos cp
				ON cp.cd_empresa = p.cd_empresa

				LEFT JOIN public.contribuicoes_programadas cpr
				ON cpr.cd_empresa = p.cd_empresa
				AND cpr.cd_registro_empregado = p.cd_registro_empregado
				AND cpr.seq_dependencia = p.seq_dependencia
				AND cpr.dt_confirma_opcao IS NOT NULL
				AND cpr.dt_confirma_canc IS NULL

				LEFT JOIN titulares_planos tp
				ON tp.cd_empresa = p.cd_empresa
				AND tp.cd_registro_empregado = p.cd_registro_empregado
				AND tp.seq_dependencia = p.seq_dependencia
				AND tp.dt_ingresso_plano =
				( 
					SELECT max(tp1.dt_ingresso_plano) AS max 
					FROM titulares_planos tp1 
					WHERE tp1.cd_empresa=p.cd_empresa 
					AND tp1.cd_registro_empregado=p.cd_registro_empregado 
					AND tp1.seq_dependencia=p.seq_dependencia
				)
				
				JOIN public.titulares pt
				ON pt.cd_empresa=p.cd_empresa 
				AND pt.cd_registro_empregado=p.cd_registro_empregado 
				AND pt.seq_dependencia=p.seq_dependencia

			WHERE 

				cp.cd_empresa = {cd_empresa}
				AND cp.cd_plano = {cd_plano}

				-- MES ANO DE COMPETENCIA DEVE SER INFERIOR AO MES/ANO INFORMADO
				-- DEVE RETORNAR 5 E 2 MESES POIS QUANDO ESSA QUERY É USADA PARA GERAR COBRANÇA,
				-- OS REGISTROS DO MES ANTERIOR NÃO ESTÃO EM ATRASO
				AND DATE_TRUNC( 'DAY', cp.dt_competencia) BETWEEN ('{ano_lancamento}-{mes_lancamento}-01'::date - '5 months'::interval) AND ('{ano_lancamento}-{mes_lancamento}-01'::date - '2 month'::interval )

				AND p.dt_confirma BETWEEN cp.dt_inicio AND cp.dt_fim

				AND (  
						p.forma_pagamento = 'BDL' 
						OR (p.forma_pagamento = 'BCO' AND p.nao_desconto_primcontrib='S') 
						OR (p.forma_pagamento = 'FOL' AND p.nao_desconto_primcontrib='S')  
					)
				
				AND 0 = CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END

				AND COALESCE( part.email, part.email_profissional ) IS NOT NULL

				AND pt.DT_CANCELA_INSCRICAO IS NULL
				;

		" );

		$fol    = enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_FOLHA;
		$fol_r1 = enum_public_codigos_cobrancas::RISCO_MORTE_SINPRORS_FOLHA;
		$fol_r2 = enum_public_codigos_cobrancas::RISCO_INVALIDEZ_SINPRORS_FOLHA;
		
		$bco    = enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_PREV_CC;
		$bco_r1 = enum_public_codigos_cobrancas::RISCO_MORTE_SINPRORS_CC;
		$bco_r2 = enum_public_codigos_cobrancas::RISCO_INVALIDEZ_SINPRORS_CC;
		
		$db->setParameter("{codigo_folha}", "$fol , $fol_r1 , $fol_r2");
		$db->setParameter("{codigo_bco}", "$bco , $bco_r1 , $bco_r2");
		$db->setParameter("{tipo_folha}", enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DESCONTO_FOLHA);
		$db->setParameter("{tipo_bco}", enum_projetos_contribuicao_controle_tipo::COBRANCA_ATRASADO_DEBITO_CONTA_CORRENTE);
		$db->setParameter("{codigo_lancamento}", "$fol,$fol_r1,$fol_r2,$bco,$bco_r1,$bco_r2");

		$db->setParameter("{ano_lancamento}", $ano);
		$db->setParameter("{mes_lancamento}", $mes);
		$db->setParameter("{cd_empresa}", $cd_empresa );
		$db->setParameter("{cd_plano}", enum_public_planos::SINPRORS_PREVIDENCIA );
		$db->setParameter("{cd_usuario}", $_SESSION['Z'] );

		$b = $db->execute();

		if( $db->haveError() )
		{
			throw new Exception( "Erro no método contribuicao_controle.criar_cobranca_atraso_sinprors - { " . $db->getMessage() . ' } ' );
			return false;
		}
		else
		{
			return true;
		}
	}

	public static function inserir($args)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$db->setSQL("
			INSERT INTO projetos.contribuicao_controle(
				cd_empresa
	            , cd_registro_empregado
	            , seq_dependencia
	            , nr_ano_competencia
	            , nr_mes_competencia
	            , cd_contribuicao_controle_tipo
	            , dt_controle
	            , cd_usuario
	            , fl_email_enviado
			) VALUES (
				{cd_empresa}
	            , {cd_registro_empregado}
	            , {seq_dependencia}
	            , {nr_ano_competencia}
	            , {nr_mes_competencia}
	            , '{cd_contribuicao_controle_tipo}'
	            , current_timestamp
	            , {cd_usuario}
				, '{fl_email_enviado}'
			)
		");

		$db->setParameter('{cd_empresa}', intval($args['cd_empresa']));
		$db->setParameter('{cd_registro_empregado}', intval($args['cd_registro_empregado']));
		$db->setParameter('{seq_dependencia}', intval($args['seq_dependencia']));
		$db->setParameter('{nr_ano_competencia}', intval($args['nr_ano_competencia']));
		$db->setParameter('{nr_mes_competencia}', intval($args['nr_mes_competencia']));
		$db->setParameter('{cd_contribuicao_controle_tipo}', $args['cd_contribuicao_controle_tipo']);
		$db->setParameter('{cd_usuario}', intval($args['cd_usuario']));
		$db->setParameter('{fl_email_enviado}', $args['fl_email_enviado']);

		$b = $db->execute(true);
		
		if( $db->haveError() )
		{
			throw new Exception( "Erro no método contribuicao_controle.inserir - { " . $db->getMessage() . ' } ' );
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Verifica se a cobrança já foi enviada para o Tipo, Mes e Ano de competência
	 * 
	 * @param array(enum_projetos_contribuicao_cobranca_tipo) $cd_contribucao_cobranca_tipo Usar enum_projetos_contribuicao_cobranca_tipo
	 * @param int $nr_mes_competencia
	 * @param int $nr_ano_competencia
	 * @param int $cd_empresa SINPRORS por padrao pois foi quem originou essa função
	 */
	public static function cobranca_ja_enviada($cd_contribuicao_controle_tipo, $nr_mes_competencia, $nr_ano_competencia,$cd_empresa=8)
	{
		$db = new postgres();
		$db = DBFactory::createObject();

		$sql = "

			SELECT

				DISTINCT fl_email_enviado

			FROM

				projetos.contribuicao_controle

			WHERE
			
				nr_mes_competencia={nr_mes_competencia}
				AND nr_ano_competencia={nr_ano_competencia}
				{cd_contribuicao_controle_tipo}
				AND cd_empresa={cd_empresa}
				;

		";
		$sep = "";
		foreach( $cd_contribuicao_controle_tipo as $tipo )
		{
			$ext .= $sep . "'" . $db->escape($tipo) . "'";
			$sep = ", ";
		}
		$sql = str_replace( "{cd_contribuicao_controle_tipo}", " AND cd_contribuicao_controle_tipo IN (" . $ext . ") ", $sql );

		$db->setSQL( $sql );
		$db->setParameter("{nr_mes_competencia}", $nr_mes_competencia);
		$db->setParameter("{nr_ano_competencia}", $nr_ano_competencia);
		$db->setParameter("{cd_empresa}", $cd_empresa);

		$r = $db->get();

		if(sizeof($r)==0)
		{
			$r = false;
		}
		elseif(sizeof($r)>1)
		{
			echo '<b>Ao verificar se essa cobrança já foi enviada, ocorreu uma inconsistência, favor entrar em contato com a equipe de informática.</b>';
			exit;
		}
		elseif(sizeof($r)==1)
		{
			$r = ($r[0]['fl_email_enviado']=="S");
		}

		return $r;
	}
}