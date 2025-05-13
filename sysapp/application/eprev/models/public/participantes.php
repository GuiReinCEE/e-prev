<?php
class Participantes extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar_ingresso( &$result, &$count, $args=array() )
	{
		$this->load->library('pagination');

		// COUNT
		$sql = "

			SELECT count(*) as qtd
			FROM 

				public.participantes p
				JOIN public.titulares t
				ON t.cd_empresa=p.cd_empresa
				AND t.cd_registro_empregado=p.cd_registro_empregado
				AND t.seq_dependencia=p.seq_dependencia

			WHERE p.cd_empresa={cd_empresa}
			AND t.dt_ingresso_eletro IS NOT NULL
			AND DATE_TRUNC( 'day', t.dt_ingresso_eletro ) = TO_DATE('01/{mes_ingresso}/{ano_ingresso}', 'DD/MM/YYYY')

		";

		$sql = str_replace( "{cd_empresa}", $this->db->escape( $args["cd_empresa"] ), $sql );
		$sql = str_replace( "{mes_ingresso}", $this->db->escape( $args["mes_ingresso"] ), $sql );
		$sql = str_replace( "{ano_ingresso}", $this->db->escape( $args["ano_ingresso"] ), $sql );

		$query = $this->db->query($sql);
		$row = $query->row_array(0);
		$count = $row['qtd'];

		$this->setup_pagination($count);

		// RESULTS
		$sql = "
			SELECT count(*) as qtd
			FROM 

				public.participantes p
				JOIN public.titulares t
				ON t.cd_empresa=p.cd_empresa
				AND t.cd_registro_empregado=p.cd_registro_empregado
				AND t.seq_dependencia=p.seq_dependencia

			WHERE p.cd_empresa={cd_empresa}
			AND t.dt_ingresso_eletro IS NOT NULL
			AND DATE_TRUNC( 'day', t.dt_ingresso_eletro ) = TO_DATE('01/{mes_ingresso}/{ano_ingresso}', 'DD/MM/YYYY')

			LIMIT " . $this->pagination->per_page . " OFFSET " . $args["page"] . "
		";

		$sql = str_replace( "{cd_empresa}", $args["cd_empresa"], $sql );
		$sql = str_replace( "{mes_ingresso}", $args["mes_ingresso"], $sql );
		$sql = str_replace( "{ano_ingresso}", $args["ano_ingresso"], $sql );

		$result = $this->db->query($sql);
	}

	function carregar_legal( &$result, $pk=array() )
	{
		$sql = "
			SELECT 

				atividades.numero
				, atividades.tipo
				, to_char(atividades.dt_cad, 'DD/MM/YYYY') as dt_cad
				, atividades.descricao
				, atividades.area
				, atividades.dt_inicio_prev
				, atividades.sistema
				, atividades.problema
				, atividades.solucao
				, atividades.dt_inicio_real
				, atividades.status_atual
				, atividades.complexidade
				, atividades.prioridade
				, atividades.negocio_fim
				, atividades.prejuizo
				, atividades.legislacao
				, atividades.situacao
				, atividades.dependencia
				, atividades.dias_realizados
				, atividades.cliente_externo
				, atividades.concorrencia
				, atividades.tarefa
				, atividades.tipo_solicitacao
				, atividades.numero_dias
				, atividades.dt_fim_prev
				, atividades.periodicidade
				, atividades.dt_fim_real
				, atividades.dt_deacordo
				, atividades.observacoes
				, atividades.divisao
				, atividades.origem
				, atividades.recurso
				, atividades.cod_atendente
				, atividades.cod_solicitante
				, atividades.dt_limite
				, atividades.dt_limite_testes
				, atividades.ok
				, atividades.complemento
				, atividades.num_dias_adicionados
				, atividades.titulo
				, atividades.cod_testador
				, atividades.cd_empresa
				, atividades.cd_registro_empregado
				, atividades.cd_sequencia
				, atividades.dt_retorno
				, atividades.pertinencia
				, atividades.cd_cenario
				, atividades.opt_grafica
				, atividades.opt_eletronica
				, atividades.opt_evento
				, atividades.opt_anuncio
				, atividades.opt_folder
				, atividades.opt_mala
				, atividades.opt_cartaz
				, atividades.opt_cartilha
				, atividades.opt_site
				, atividades.opt_outro
				, atividades.cores
				, atividades.formato
				, atividades.gramatura
				, atividades.quantia
				, atividades.custo
				, atividades.cc
				, atividades.pacs
				, atividades.patracs
				, atividades.nacs
				, atividades.cacs
				, atividades.lacs
				, atividades.dacs
				, atividades.forma
				, atividades.solicitante
				, atividades.cd_plano
				, atividades.dt_env_teste
				, atividades.dt_fim_real_nova
				, atividades.numero_at_origem
				, atividades.dt_implementacao_norma_legal
				, atividades.dt_prevista_implementacao_norma_legal
				, atividades.cd_recorrente
				, atividades.fl_teste_relevante
				, atividades.fl_encerrado_automatico
				, atividades.fl_teste_prorrogado
				, atividades.tp_envio
				, atividades.cd_atendimento

			FROM 

				projetos.atividades atividades 

			WHERE

				atividades.numero = ?
				;
		";

		$result = $this->db->query($sql, array('atividades.numero'=>$pk['numero']));
	}

	private function setup_pagination($count)
	{
		// Setup pagination
		$config['enable_query_strings'] = FALSE;
		$config['base_url'] = $this->config->item('base_url') . 'index.php/atividade/legal/index';
		$config['per_page'] = 30;
		$config['total_rows'] = $count;
		$this->pagination->initialize($config);
	}
}
?>