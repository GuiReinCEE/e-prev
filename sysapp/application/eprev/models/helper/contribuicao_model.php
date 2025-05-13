<?php 
class contribuicao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	/**
	 * Verifica se a cobrança foi enviada para o mes/ano de referencia
	 * 
	 * @param $cd_plano
	 * @param $cd_empresa
	 * @param $mes
	 * @param $ano
	 * @return boolean
	 */
	function cobranca_enviada( $cd_plano, $cd_empresa, $mes, $ano )
	{
		$q = $this->db->query("
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
				cd_plano = ?
				AND cd_empresa = ?
				AND mes_competencia = ?
				AND ano_competencia = ?
				AND dt_envio_bdl IS NOT NULL;
		", array(  (int)$cd_plano, (int)$cd_empresa, (int)$mes, (int)$ano  ) );

		$r = $q->row_array();

		return ($r && $r['dt_envio_bdl']!='');
	}

	function confirmacao_inscricao( $cd_plano, $cd_empresa, $mes, $ano )
	{
		$q = $this->db->query("
			  SELECT  to_char(dt_confirmacao, 'DD/MM/YYYY') as dt_confirmacao
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
			  WHERE cd_plano = ?
			    AND cd_empresa = ?
			  	AND mes_competencia = ?
			    AND ano_competencia = ?
			    AND dt_confirmacao IS NOT NULL;
		", array(  (int)$cd_plano, (int)$cd_empresa, (int)$mes, (int)$ano  ));

		return $q->row_array();
	}

	function geracao_contribuicao( $cd_plano, $cd_empresa, $mes, $ano )
	{
		$q = $this->db->query("
		  SELECT  to_char(dt_geracao, 'DD/MM/YYYY') as dt_geracao
         		, usuario_geracao
                , tot_internet_gerado
                , tot_bdl_gerado
                , tot_cheque_gerado
                , tot_deposito_gerado
                , tot_debito_cc_gerado
                , vlr_internet_gerado
                , vlr_bdl_gerado
                , vlr_cheque_gerado
                , vlr_deposito_gerado
                , vlr_debito_cc_gerado
                , tot_folha_gerado
                , vlr_folha_gerado
		   FROM public.controle_geracao_cobranca
		  WHERE cd_plano = ?
		    AND cd_empresa = ?
		  	AND mes_competencia = ?
		    AND ano_competencia = ?
		    AND dt_geracao IS NOT NULL;
		", array(  (int)$cd_plano, (int)$cd_empresa, (int)$mes, (int)$ano  ));

		return $q->row_array();
	}

	function envio_contribuicao( $cd_plano, $cd_empresa, $mes, $ano )
	{
		$q = $this->db->query("
			SELECT usuario_envio_bdl
	            , to_char(dt_envio_bdl, 'DD/MM/YYYY') AS dt_envio_bdl
		        , tot_bdl_enviado
		        , vlr_bdl_enviado
	
		        , usuario_envio_debito_cc
		        , to_char(dt_envio_debito_cc, 'DD/MM/YYYY') AS dt_envio_debito_cc
		        , tot_debito_cc_enviado
		        , vlr_debito_cc_enviado
			FROM public.controle_geracao_cobranca
			WHERE cd_plano = ?
			    AND cd_empresa = ?
			  	AND mes_competencia = ?
			    AND ano_competencia = ?
			    AND dt_envio_bdl IS NOT NULL;
		", array(  (int)$cd_plano, (int)$cd_empresa, (int)$mes, (int)$ano  ));

		return $q->row_array();
	}

	/**
	 * Calcula quantos emails serão enviados
	 * 
	 * @param $apenas_com_email	TRUE Limita o calculo em apenas participantes com email, FALSE calcula com todos
	 * @param $cd_plano
	 * @param $cd_empresa
	 * @param $mes
	 * @param $ano
	 * @param $forma_pagamento	Equivale ao campo public.protocolos_participantes.forma_pagamento ('BDL', 'BCO', 'FOL')
	 * @return array()			Array de uma linha com resultado da query composto pelas colunas 'contador' e 'valor'
	 */
	function quantidade_enviar( $apenas_com_email, $cd_plano, $cd_empresa, $mes, $ano, $forma_pagamento )
	{
		$query_apenas_com_email = ($apenas_com_email)?"AND COALESCE(email, email_profissional) IS NOT NULL":"";
		$q = $this->db->query(
		"
			SELECT COUNT(*) AS contador,
			SUM(   COALESCE(cpr.valor,0) + COALESCE(riscos.valor_risco,0)   ) AS valor
			FROM public.protocolos_participantes p

			LEFT JOIN 
			(
				SELECT ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia, SUM(ap.premio) as valor_risco
				FROM public.apolices_participantes ap
				JOIN public.apolices a
				ON a.cd_apolice = ap.cd_apolice
				WHERE ap.dt_exclusao IS NULL
				GROUP BY ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia
			) AS riscos
			ON riscos.cd_empresa = p.cd_empresa
			AND riscos.cd_registro_empregado = p.cd_registro_empregado
			AND riscos.seq_dependencia = p.seq_dependencia

			JOIN public.calendarios_planos cp
			ON cp.cd_empresa = p.cd_empresa

			JOIN public.controle_geracao_cobranca cgc
			ON cgc.cd_empresa = cp.cd_empresa
			AND cgc.cd_plano = cp.cd_plano

			JOIN public.participantes participantes
			ON participantes.cd_empresa = p.cd_empresa
			AND participantes.cd_registro_empregado = p.cd_registro_empregado
			AND participantes.seq_dependencia = p.seq_dependencia

			LEFT JOIN public.contribuicoes_programadas cpr
			ON cpr.cd_empresa = p.cd_empresa
			AND cpr.cd_registro_empregado = p.cd_registro_empregado
			AND cpr.seq_dependencia       = p.seq_dependencia
			AND cpr.dt_confirma_opcao     IS NOT NULL
			AND cpr.dt_confirma_canc      IS NULL

			LEFT JOIN titulares_planos tp
			ON tp.cd_empresa = p.cd_empresa
			AND tp.cd_registro_empregado = p.cd_registro_empregado
			AND tp.seq_dependencia = p.seq_dependencia
			AND tp.dt_ingresso_plano = 
			( 
				SELECT max(tp1.dt_ingresso_plano) as max 
				FROM titulares_planos tp1 
				WHERE tp1.cd_empresa=p.cd_empresa 
				AND tp1.cd_registro_empregado=p.cd_registro_empregado 
				AND tp1.seq_dependencia=p.seq_dependencia 
			)

			WHERE cp.cd_empresa = ?
			AND cp.cd_plano = ?
			AND cp.dt_competencia = ?
			AND cgc.mes_competencia = ?
			AND cgc.ano_competencia = ?
			AND cgc.dt_geracao IS NOT NULL
			AND p.dt_confirma BETWEEN cp.dt_inicio AND cp.dt_fim
			AND p.forma_pagamento = ?
			AND 0 = CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END
			$query_apenas_com_email

		", array(  (int)$cd_empresa, (int)$cd_plano, (int)$ano.'-'.(int)$mes.'-01', (int)$mes, (int)$ano, $forma_pagamento  ));

		return $q->row_array();
	}

	function contribuicao_controle_contador( $nr_ano_competencia, $nr_mes_competencia, $cd_empresa, $cd_contribuicao_controle_tipo )
	{
		$sql = "
			SELECT COUNT(*) as quantos
			FROM projetos.contribuicao_controle
			WHERE 
				nr_ano_competencia = ?
				AND nr_mes_competencia = ?
				AND cd_empresa = ?
				{cd_contribuicao_controle_tipo}
		";

		$sep = ""; $ext="";
		foreach($cd_contribuicao_controle_tipo as $tipo)
		{
			$ext .= $sep . "'" . $this->db->escape_str($tipo) . "'";
			$sep = ", ";
		}
		$sql = str_replace( "{cd_contribuicao_controle_tipo}", " AND cd_contribuicao_controle_tipo IN (" . $ext . ") ", $sql );

		$result = $this->db->query($sql, array( intval($nr_ano_competencia), intval($nr_mes_competencia), intval($cd_empresa) ));

		$row = $result->row_array();

		return $row['quantos'];
	}

	function listar_participantes_sem_email( $ano, $mes, $empresa, $plano, $formas_de_pagamento=array('BDL', 'BCO') )
	{
		$sql = "
			SELECT COUNT(*) as q
			FROM projetos.contribuicao_controle
			WHERE 
				nr_mes_competencia = ?
				AND nr_ano_competencia = ?
				AND cd_empresa = ?
		";

		$result = $this->db->query($sql, array( intval($mes), intval($ano), intval($empresa) ));
		$row = $result->row_array();

		// Se já foi gerada cobrança para o mes, então 
		// lista os participante sem email usando a tabela de controle das
		// contribuições geradas para o mes
		if($row['q']>0)
		{
			$result = $this->db->query( "
				SELECT b.* 
				FROM projetos.contribuicao_controle a 
				JOIN public.participantes b
				ON a.cd_empresa=b.cd_empresa 
				AND a.cd_registro_empregado=b.cd_registro_empregado 
				AND a.seq_dependencia=b.seq_dependencia
				AND coalesce( b.email, b.email_profissional ) IS NULL
				AND a.cd_empresa=?
				AND a.nr_ano_competencia=?
				AND a.nr_mes_competencia=?
			", array( (int)$ano, (int)$mes, (int)$empresa ) );
		}
		// Se não foi gerada cobrança para o mes
		// lista os participantes que não possuem email usando a mesma
		// query que será usada para gerar a lista de cobrança
		else
		{
			$sep='';
			$fp = '';
			foreach($formas_de_pagamento as $item)
			{
				$fp .= $sep . $this->db->escape($item);
				$sep = ",";
			}

			$sql = "
				SELECT participantes.cd_registro_empregado, participantes.nome, p.forma_pagamento

				FROM public.protocolos_participantes p
	
				LEFT JOIN 
				(
					SELECT ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia, SUM(ap.premio) as valor_risco
					FROM public.apolices_participantes ap
					JOIN public.apolices a
					ON a.cd_apolice = ap.cd_apolice
					WHERE ap.dt_exclusao IS NULL
					GROUP BY ap.cd_empresa, ap.cd_registro_empregado, ap.seq_dependencia
				) AS riscos
				ON riscos.cd_empresa = p.cd_empresa
				AND riscos.cd_registro_empregado = p.cd_registro_empregado
				AND riscos.seq_dependencia = p.seq_dependencia
	
				JOIN public.calendarios_planos cp
				ON cp.cd_empresa = p.cd_empresa
	
				JOIN public.controle_geracao_cobranca cgc
				ON cgc.cd_empresa = cp.cd_empresa
				AND cgc.cd_plano = cp.cd_plano
	
				JOIN public.participantes participantes
				ON participantes.cd_empresa = p.cd_empresa
				AND participantes.cd_registro_empregado = p.cd_registro_empregado
				AND participantes.seq_dependencia = p.seq_dependencia
	
				LEFT JOIN public.contribuicoes_programadas cpr
				ON cpr.cd_empresa = p.cd_empresa
				AND cpr.cd_registro_empregado = p.cd_registro_empregado
				AND cpr.seq_dependencia       = p.seq_dependencia
				AND cpr.dt_confirma_opcao     IS NOT NULL
				AND cpr.dt_confirma_canc      IS NULL
	
				LEFT JOIN titulares_planos tp
				ON tp.cd_empresa = p.cd_empresa
				AND tp.cd_registro_empregado = p.cd_registro_empregado
				AND tp.seq_dependencia = p.seq_dependencia
				AND tp.dt_ingresso_plano = 
				( 
					SELECT max(tp1.dt_ingresso_plano) as max 
					FROM titulares_planos tp1 
					WHERE tp1.cd_empresa=p.cd_empresa 
					AND tp1.cd_registro_empregado=p.cd_registro_empregado 
					AND tp1.seq_dependencia=p.seq_dependencia 
				)

				WHERE cp.cd_empresa = ?
				AND cp.cd_plano = ?
				AND cp.dt_competencia = ?
				AND cgc.mes_competencia = ?
				AND cgc.ano_competencia = ?
				AND cgc.dt_geracao IS NOT NULL
				AND p.dt_confirma BETWEEN cp.dt_inicio AND cp.dt_fim
				AND 0 = CASE WHEN tp.dt_ingresso_plano IS NULL AND tp.dt_deslig_plano IS NULL THEN 0 ELSE 1 END
				AND p.forma_pagamento IN ( $fp )
				AND coalesce(email, email_profissional) IS NULL
			";

			$result = $this->db->query(
				$sql, 
				array( intval($empresa), intval($plano), (int)$ano.'-'.(int)$mes.'-01', $mes, $ano )
			);

			$result = $result->result_array();
			
			return $result;
		}
	}
}
?>