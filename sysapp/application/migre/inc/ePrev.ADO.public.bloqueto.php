<?php
class ADO_public_bloqueto
{
    // DAL
    private $db;
    private $dal;

    function ADO_public_bloqueto( $_db ) {
        $this->db = $_db;
        $this->dal = new DBConnection();
        $this->dal->loadConnection( $this->db );
    }

    function __destruct(){
        $this->dal = null;
    } 

    public function competencias_a_pagar__get( $cd_empresa, $cd_registro_empregado, $seq_dependencia )
    {
        $this->dal->createQuery("

             	SELECT DISTINCT(ano_competencia) AS ano_competencia, 
					   mes_competencia, 
					   dt_lancamento 
				  FROM bloqueto b1
				 WHERE b1.cd_empresa            = {cd_empresa} 
				   AND b1.cd_registro_empregado = {cd_registro_empregado} 
				   AND b1.seq_dependencia       = {seq_dependencia}
				   AND b1.status                IS NULL 
				   AND b1.data_retorno          IS NULL 
				   AND b1.codigo_lancamento IN (2450,2451,2452,2460,2461,2462,2480,2481,2482)
				   AND b1.dt_lancamento IN (
				   							SELECT MAX(b2.dt_lancamento) 
											  FROM bloqueto b2 
											 WHERE b2.seq_dependencia       = 0 
											   AND b2.cd_registro_empregado = b1.cd_registro_empregado 
											   AND b2.cd_empresa            = b1.cd_empresa 
											   AND b2.seq_dependencia       = b1.seq_dependencia
											   )
				  ORDER BY b1.ano_competencia ASC, 
						   b1.mes_competencia ASC, 
						   b1.dt_lancamento

        ");

        $this->dal->setAttribute( "{cd_empresa}", (int)$cd_empresa );
        $this->dal->setAttribute( "{cd_registro_empregado}", (int)$cd_registro_empregado );
        $this->dal->setAttribute( "{seq_dependencia}", (int)$seq_dependencia );

        $result = $this->dal->getResultset();
		
        $return = array();
		while( $row = pg_fetch_array($result) )
        {
        	$bloqueto = new entity_public_bloqueto();
        	$bloqueto->ano_competencia = $row['ano_competencia'];
        	$bloqueto->mes_competencia = $row['mes_competencia'];
        	$return[sizeof($return)] = $bloqueto;
        }

        if ($this->dal->haveError()) 
        {
            throw new Exception("Erro em ADO_public_bloqueto.competencias_a_pagar__get($EMP, $RE, $SEQ) ao executar comando SQL de consulta. ". $this->dal->getMessage() );
        }

        return $return;
    }

	/**
	 * Resgatar a data limite sem encargos
	 * ano e mes de competencia
	 * soma do valor_lancamento
	 * e caso a data atual seja superior a data limite sem encargos, retorna a soma de vlr_encargo
	 * para um determinado participante em um determinado mes/ano de competencia
	 * 
	 * @param array $args com os atributos cd_empresa, cd_registro_empregado, seq_dependencia, mes, ano
	 * por exemplo: 
	 * $args = array(
	 *		'cd_empresa'=>enum_public_patrocinadoras::SINPRO,
	 *		'cd_registro_empregado'=>86,
	 *		'seq_dependencia'=>0,
	 *      'codigo_lancamento'=>array(enum_public_codigos_cobrancas::CONTRIBUICAO_SINPRORS_PREV)
	 * )
	 * @return entity_public_bloqueto Objeto preenchido com consulta ao banco de dados com critérios que atendam a descrição desse método
	 */
    public function infos_para_emissao_pagamento__get($args)
    {
    	$dal = new DBConnection();
    	$dal = $this->dal;

    	$ent = new entity_public_bloqueto();

    	// montar filtros opcionais
    	$where = "";
    	//if($args['mes']!='') $where .= " AND mes_competencia = {mes_compentencia} ";
    	//if($args['ano']!='') $where .= " AND ano_competencia = {ano_competencia} ";
    	//if($args['comp_md5']!='') $where .= " AND funcoes.cripto_mes_ano(mes_competencia::numeric, ano_competencia::numeric) = '{comp_md5}' ";

    	$dal->createQuery("

		     SELECT TO_CHAR(dt_limite_sem_encargos, 'DD/MM/YYYY') AS dt_limite_sem_encargos,
					TO_CHAR(dt_vencimento, 'DD/MM/YYYY') as dt_vencimento,
		         	b.ano_competencia, 
		         	b.mes_competencia, 
		         	b.valor_lancamento,
		         	CASE WHEN CURRENT_DATE <= dt_limite_sem_encargos
			        THEN 0
			        ELSE vlr_encargo
					END AS vlr_encargo,
		         	b.descricao,
		         	b.codigo_lancamento
		       FROM bloqueto b
		      WHERE b.cd_empresa = {cd_empresa} 
		        AND b.cd_registro_empregado = {cd_registro_empregado}
		        AND b.seq_dependencia = {seq_dependencia}

		        AND b.status IS NULL 
		        /*AND b.codigo_lancamento = {codigo_lancamento}*/
		        AND b.codigo_lancamento IN ({codigo_lancamento})

		        -- MAIOR ANO/MES DE COMPETENCIA NÃO PAGA
				AND to_date(ano_competencia::varchar || '-' || mes_competencia::varchar || '-01', 'YYYY-MM-DD') = (
					SELECT MAX( TO_DATE(ano_competencia::varchar || '-' || mes_competencia::varchar || '-01', 'YYYY-MM-DD') )
					FROM public.bloqueto
					WHERE public.bloqueto.cd_empresa=b.cd_empresa
					AND public.bloqueto.cd_registro_empregado=b.cd_registro_empregado
					AND public.bloqueto.seq_dependencia=b.seq_dependencia
					AND public.bloqueto.status IS NULL 
					AND public.bloqueto.data_retorno IS NULL
					AND public.bloqueto.codigo_lancamento IN ( {codigo_lancamento} )
				)

		        AND b.dt_emissao IN (SELECT MAX(b1.dt_emissao) 
		                               FROM bloqueto b1
						               WHERE b1.cd_empresa            = b.cd_empresa
						                 AND b1.cd_registro_empregado = b.cd_registro_empregado
						              AND b1.seq_dependencia       = b.seq_dependencia
						              AND b1.ano_competencia       = b.ano_competencia 
						              AND b1.mes_competencia       = b.mes_competencia 
						               GROUP BY b1.ano_competencia, 
						                      b1.mes_competencia)

    	");
    	$dal->setAttribute("{cd_empresa}", (int)$args['cd_empresa']);
    	$dal->setAttribute("{cd_registro_empregado}", (int)$args['cd_registro_empregado']);
    	$dal->setAttribute("{seq_dependencia}", (int)$args['seq_dependencia']);
    	$dal->setAttribute("{comp_md5}", $args['comp_md5']);
    	$dal->setAttribute("{codigo_lancamento}", implode(',', $args['codigo_lancamento']));
    	
    	$result = $dal->getResultset();

    	if ($dal->haveError()) 
        {
            throw new Exception("Erro em ADO_public_bloqueto.infos_para_emissao_pagamento__get($args) ao executar comando SQL de consulta. ". $dal->getMessage() );
        }
        
        if($row=pg_fetch_array($result))
        {
        	$ent->dt_limite_sem_encargos = $row['dt_limite_sem_encargos'];
        	$ent->dt_vencimento = $row['dt_vencimento'];
        	$ent->ano_competencia = $row['ano_competencia'];
        	//echo $row['ano_competencia']; exit;
        	$ent->mes_competencia = $row['mes_competencia'];
        	$ent->valor_lancamento = $row['valor_lancamento'];
        	$ent->vlr_encargo = $row['vlr_encargo'];
        	$ent->descricao = $row['descricao'];
        	$ent->codigo_lancamento = $row['codigo_lancamento'];
        }
        
        return $ent;
    }
}
?>