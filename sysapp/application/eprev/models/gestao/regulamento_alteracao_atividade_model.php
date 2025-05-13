<?php
class Regulamento_alteracao_atividade_model extends Model
{
	public function get_usuario_responsavel($cd_regulamento_alteracao_atividade, $cd_usuario)
	{
		$qr_sql = "
			SELECT COUNT(*) AS responsavel
			  FROM gestao.regulamento_alteracao_atividade_gerencia raag
			  JOIN gestao.regulamento_alteracao_responsavel rar
			    ON rar.cd_gerencia = raag.cd_gerencia
			 WHERE raag.cd_regulamento_alteracao_atividade = ".intval($cd_regulamento_alteracao_atividade)."
			   AND raag.dt_exclusao 					   IS NULL
			   AND rar.dt_exclusao  					   IS NULL
			   AND rar.cd_usuario 						   = ".intval($cd_usuario).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_atividade_tipo()
	{
		$qr_sql = "
			SELECT cd_regulamento_alteracao_atividade_tipo AS value,
				   ds_regulamento_alteracao_atividade_tipo AS text
			  FROM gestao.regulamento_alteracao_atividade_tipo
			 WHERE dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_regulamento_alteracao_atividade, $cd_gerencia)
	{
		$qr_sql = "
			SELECT raag.cd_regulamento_alteracao_atividade,
			       raag.cd_regulamento_alteracao_atividade_gerencia,
			       raag.cd_regulamento_alteracao_atividade_tipo,
			       TO_CHAR(raag.dt_prevista, 'DD/MM/YYYY') AS dt_prevista,
			       TO_CHAR(raag.dt_implementacao, 'DD/MM/YYYY') AS dt_implementacao,
			       raub.cd_regulamento_alteracao_unidade_basica_pai,
			       raub.cd_regulamento_alteracao_unidade_basica,
			       raub.nr_ordem,
			       raub.ds_regulamento_alteracao_unidade_basica,
			       raet.ds_regulamento_alteracao_estrutura_tipo,
			       raat.ds_regulamento_alteracao_atividade_tipo,
			       (CASE WHEN raat.cd_regulamento_alteracao_atividade_tipo = 1
			              THEN 'label label-info'
			              WHEN raat.cd_regulamento_alteracao_atividade_tipo = 2
			              THEN 'label label-success'
			              ELSE 'label label-inverse'
			       END) AS ds_class_tipo
			  FROM gestao.regulamento_alteracao_atividade_gerencia raag
			  JOIN gestao.regulamento_alteracao_atividade raa
			    ON raa.cd_regulamento_alteracao_atividade = raag.cd_regulamento_alteracao_atividade
			  JOIN gestao.regulamento_alteracao_unidade_basica raub
			    ON raub.cd_regulamento_alteracao_unidade_basica = raa.cd_regulamento_alteracao_unidade_basica
			  JOIN gestao.regulamento_alteracao_estrutura_tipo raet
			    ON raet.cd_regulamento_alteracao_estrutura_tipo = raub.cd_regulamento_alteracao_estrutura_tipo
			  LEFT JOIN gestao.regulamento_alteracao_atividade_tipo raat
			    ON raat.cd_regulamento_alteracao_atividade_tipo = raag.cd_regulamento_alteracao_atividade_tipo
			 WHERE raag.cd_gerencia 		       		   = '".trim($cd_gerencia)."'
			   AND raag.cd_regulamento_alteracao_atividade = ".intval($cd_regulamento_alteracao_atividade)."
			   AND raag.dt_exclusao 		    		   IS NULL
			   AND raa.dt_exclusao 		       			   IS NULL
			   AND raub.dt_exclusao 		      		   IS NULL
			   AND raat.dt_exclusao 		       		   IS NULL;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega_unidade_basica($cd_regulamento_alteracao_unidade_basica)
	{
		$qr_sql = "
			SELECT raub.ds_regulamento_alteracao_unidade_basica,
				   'Art. ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º ' ELSE '. ' END) || raub.ds_regulamento_alteracao_unidade_basica AS ds_artigo,
				   raub.cd_regulamento_alteracao,
				   rt.ds_regulamento_tipo,
				   rt.ds_cnpb,
				   raet.ds_regulamento_alteracao_estrutura_tipo || ' ' || TO_CHAR(rae.nr_ordem, 'FMRN') || ' - ' || rae.ds_regulamento_alteracao_estrutura AS ds_estrutura,
			       raet.ds_class_label,
			       raet.ds_regulamento_alteracao_estrutura_tipo
			  FROM gestao.regulamento_alteracao_unidade_basica raub
			  JOIN gestao.regulamento_alteracao ra
			    ON ra.cd_regulamento_alteracao = raub.cd_regulamento_alteracao
			  JOIN gestao.regulamento_tipo rt
			    ON rt.cd_regulamento_tipo = ra.cd_regulamento_tipo
			  JOIN gestao.regulamento_alteracao_estrutura rae
			    ON rae.cd_regulamento_alteracao_estrutura = raub.cd_regulamento_alteracao_estrutura
			  JOIN gestao.regulamento_alteracao_estrutura_tipo raet
			    ON raet.cd_regulamento_alteracao_estrutura_tipo = rae.cd_regulamento_alteracao_estrutura_tipo
			 WHERE raub.dt_exclusao IS NULL
			   AND raub.cd_regulamento_alteracao_unidade_basica = ".intval($cd_regulamento_alteracao_unidade_basica).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($cd_regulamento_alteracao_atividade_gerencia, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_atividade_gerencia
			   SET cd_regulamento_alteracao_atividade_tipo = ".(intval($args['cd_regulamento_alteracao_atividade_tipo']) > 0 ? intval($args['cd_regulamento_alteracao_atividade_tipo']) : "DEFAULT").",
				   dt_prevista 							   = ".(trim($args['dt_prevista']) != '' ? "TO_DATE('".trim($args['dt_prevista'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   dt_implementacao 					   = ".(trim($args['dt_implementacao']) != '' ? "TO_DATE('".trim($args['dt_implementacao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   dt_implementa 					   	   = ".(trim($args['dt_implementacao']) != '' ? "CURRENT_TIMESTAMP" : "DEFAULT").",
				   cd_usuario_implementa 				   = ".(trim($args['dt_implementacao']) != '' ? intval($args['cd_usuario']) : "DEFAULT").",
				   cd_usuario_respondente 				   = ".intval($args['cd_usuario']).",
				   cd_usuario_alteracao 				   = ".intval($args['cd_usuario'])."
			 WHERE cd_regulamento_alteracao_atividade_gerencia = ".intval($cd_regulamento_alteracao_atividade_gerencia).";";

		$this->db->query($qr_sql);
	}

	public function listar_acompanhamento($cd_regulamento_alteracao_atividade_gerencia)
	{
		$qr_sql = "
			SELECT cd_regulamento_alteracao_atividade_acompanhamento,
				   ds_regulamento_alteracao_atividade_acompanhamento,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario,
				   cd_usuario_inclusao
			  FROM gestao.regulamento_alteracao_atividade_acompanhamento
			 WHERE dt_exclusao IS NULL
			   AND cd_regulamento_alteracao_atividade_gerencia = ".intval($cd_regulamento_alteracao_atividade_gerencia).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_acompanhamento($cd_regulamento_alteracao_atividade_acompanhamento)
	{
		$qr_sql = "
			SELECT cd_regulamento_alteracao_atividade_acompanhamento,
				   ds_regulamento_alteracao_atividade_acompanhamento
			  FROM gestao.regulamento_alteracao_atividade_acompanhamento
			 WHERE dt_exclusao IS NULL
			   AND cd_regulamento_alteracao_atividade_acompanhamento = ".intval($cd_regulamento_alteracao_atividade_acompanhamento).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_acompanhamento($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.regulamento_alteracao_atividade_acompanhamento
				(
					ds_regulamento_alteracao_atividade_acompanhamento,
					cd_regulamento_alteracao_atividade_gerencia,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".(trim($args['ds_regulamento_alteracao_atividade_acompanhamento']) != '' ? str_escape($args['ds_regulamento_alteracao_atividade_acompanhamento']) : "DEFAULT").",
					".(intval($args['cd_regulamento_alteracao_atividade_gerencia']) > 0 ? intval($args['cd_regulamento_alteracao_atividade_gerencia']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
					".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
				);";

		$this->db->query($qr_sql);
	}

	public function atualizar_acompanhamento($cd_regulamento_alteracao_atividade_acompanhamento, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.regulamento_alteracao_atividade_acompanhamento
			   SET ds_regulamento_alteracao_atividade_acompanhamento = ".(trim($args['ds_regulamento_alteracao_atividade_acompanhamento']) != '' ? str_escape($args['ds_regulamento_alteracao_atividade_acompanhamento']) : "DEFAULT").",
				   cd_usuario_alteracao 							 = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
				   dt_alteracao 									 = CURRENT_TIMESTAMP
			 WHERE cd_regulamento_alteracao_atividade_acompanhamento = ".intval($cd_regulamento_alteracao_atividade_acompanhamento).";";

		$this->db->query($qr_sql);
	}

	public function listar_minhas($cd_usuario, $args = array())
	{
		$qr_sql = "
			SELECT raa.cd_regulamento_alteracao_atividade,
					TO_CHAR(raa.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       rt.ds_regulamento_tipo,
			       'Art. ' || raub.nr_ordem || (CASE WHEN raub.nr_ordem <= 9 THEN 'º ' ELSE '. ' END) || raub.ds_regulamento_alteracao_unidade_basica AS ds_artigo,
			       raat.ds_regulamento_alteracao_atividade_tipo,
			       TO_CHAR(raag.dt_prevista, 'DD/MM/YYYY') AS dt_prevista,
			       TO_CHAR(raag.dt_implementacao, 'DD/MM/YYYY') AS dt_implementacao,
			       (SELECT raaa.ds_regulamento_alteracao_atividade_acompanhamento
			          FROM gestao.regulamento_alteracao_atividade_acompanhamento raaa
			         WHERE raaa.dt_exclusao IS NULL
			           AND raaa.cd_regulamento_alteracao_atividade_gerencia = raag.cd_regulamento_alteracao_atividade_gerencia
			         ORDER BY raaa.dt_inclusao DESC
			         LIMIT 1) AS ds_regulamento_alteracao_atividade_acompanhamento,
			       (CASE WHEN raat.cd_regulamento_alteracao_atividade_tipo = 1
			              THEN 'label label-info'
			              WHEN raat.cd_regulamento_alteracao_atividade_tipo = 2
			              THEN 'label label-success'
			              ELSE 'label label-inverse'
			       END) AS ds_class_tipo
			  FROM gestao.regulamento_alteracao_atividade raa
			  JOIN gestao.regulamento_alteracao_atividade_gerencia raag
			    ON raag.cd_regulamento_alteracao_atividade = raa.cd_regulamento_alteracao_atividade
			  JOIN gestao.regulamento_alteracao_responsavel rar
			    ON rar.cd_gerencia = raag.cd_gerencia
			  LEFT JOIN gestao.regulamento_alteracao_atividade_tipo raat
			    ON raat.cd_regulamento_alteracao_atividade_tipo = raag.cd_regulamento_alteracao_atividade_tipo
			  JOIN gestao.regulamento_alteracao_unidade_basica raub
			    ON raub.cd_regulamento_alteracao_unidade_basica = raa.cd_regulamento_alteracao_unidade_basica
			  JOIN gestao.regulamento_alteracao ra
			    ON ra.cd_regulamento_alteracao = raub.cd_regulamento_alteracao
			  JOIN gestao.regulamento_tipo rt
			    ON rt.cd_regulamento_tipo = ra.cd_regulamento_tipo
			 WHERE raat.dt_exclusao IS NULL
			   AND raa.dt_exclusao  IS NULL
			   AND raa.dt_envio     IS NOT NULL
			   AND raag.dt_exclusao IS NULL
			   AND raub.dt_exclusao IS NULL
			   AND ra.dt_exclusao   IS NULL
			   AND rt.dt_exclusao   IS NULL
			   AND rar.dt_exclusao  IS NULL
			   AND rar.cd_usuario   = ".intval($cd_usuario)."
			   ".(trim($args['fl_respondido']) == 'S' ? 'AND raag.cd_regulamento_alteracao_atividade_tipo IS NOT NULL' : '')."
			   ".(trim($args['fl_respondido']) == 'N' ? 'AND raag.cd_regulamento_alteracao_atividade_tipo IS NULL' : '')."
			   ".(intval($args['cd_regulamento_alteracao_atividade_tipo']) > 0 ? 'AND raag.cd_regulamento_alteracao_atividade_tipo = '.intval($args['cd_regulamento_alteracao_atividade_tipo']) : '')."
			   ".(trim($args['fl_implementado']) == 'S' ? 'AND raag.dt_implementacao IS NOT NULL' : '')."
			   ".(trim($args['fl_implementado']) == 'N' ? 'AND raag.dt_implementacao IS NULL' : '')."
			   ".(((trim($args['dt_prevista_ini']) != '') AND (trim($args['dt_prevista_fim']) != '')) ? "AND DATE_TRUNC('day', raag.dt_prevista) BETWEEN TO_DATE('".$args['dt_prevista_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prevista_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_implementa_ini']) != '') AND (trim($args['dt_implementa_fim']) != '')) ? "AND DATE_TRUNC('day', raag.dt_implementacao) BETWEEN TO_DATE('".$args['dt_implementa_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_implementa_fim']."', 'DD/MM/YYYY')" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}
	
 	public function get_tipo()
	{
		$qr_sql = "
			SELECT cd_regulamento_alteracao_atividade_tipo AS value,
				   ds_regulamento_alteracao_atividade_tipo AS text
			  FROM gestao.regulamento_alteracao_atividade_tipo
			 WHERE dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}
}