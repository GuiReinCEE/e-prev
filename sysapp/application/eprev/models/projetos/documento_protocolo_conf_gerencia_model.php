<?php
class Documento_protocolo_conf_gerencia_model extends Model
{
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_documento_protocolo_conf_gerencia,
				   cd_gerencia,
				   funcoes.get_usuario_nome(cd_usuario_responsavel) AS ds_usuario_responsavel,
			       fl_conferencia,
				   nr_amostragem
			  FROM projetos.documento_protocolo_conf_gerencia
			 WHERE dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_documento_protocolo_conf_gerencia)
	{
		$qr_sql = "
			SELECT cd_documento_protocolo_conf_gerencia,
				   cd_gerencia,
				   cd_usuario_responsavel,
			       fl_conferencia,
				   nr_amostragem
			  FROM projetos.documento_protocolo_conf_gerencia
			 WHERE dt_exclusao IS NULL
			   AND cd_documento_protocolo_conf_gerencia = ".intval($cd_documento_protocolo_conf_gerencia).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_usuarios($cd_gerencia)
	{
		$qr_sql = "
			SELECT codigo AS value,
			       nome AS text
			  FROM projetos.usuarios_controledi
			 WHERE divisao = '".trim($cd_gerencia)."'
			   AND tipo NOT IN('X');";

		return $this->db->query($qr_sql)->result_array();
	}

	public function atualizar($cd_documento_protocolo_conf_gerencia, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.documento_protocolo_conf_gerencia
			   SET cd_usuario_responsavel = ".(intval($args['cd_usuario_responsavel']) > 0 ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
			   	   fl_conferencia 		  = ".(trim($args['fl_conferencia']) != '' ? str_escape($args['fl_conferencia']) : "DEFAULT").",
			   	   nr_amostragem 		  = ".(trim($args['nr_amostragem']) != '' ? floatval($args['nr_amostragem']) : "DEFAULT").",
			   	   cd_usuario_alteracao   = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
			   	   dt_alteracao 		  = CURRENT_TIMESTAMP
			 WHERE cd_documento_protocolo_conf_gerencia = ".intval($cd_documento_protocolo_conf_gerencia).";";

		$this->db->query($qr_sql);
	}

	public function listar_relatorio($args = array())
	{
		$qr_sql = "
			SELECT m.cd_documento_protocolo_conf_gerencia_item_mes,
				   TO_CHAR(m.dt_referencia, 'YYYY-MM') AS dt_referencia, 
			       m.cd_gerencia, 
			       (SELECT COUNT(*)
			          FROM projetos.documento_protocolo_conf_gerencia_item i
			         WHERE i.dt_exclusao IS NULL
			           AND i.cd_documento_protocolo_conf_gerencia_item_mes = m.cd_documento_protocolo_conf_gerencia_item_mes) AS qt_conferencia,
			       (SELECT COUNT(*) 
			          FROM projetos.documento_protocolo_item dpi2
			          JOIN projetos.documento_protocolo dp2
			            ON dp2.cd_documento_protocolo = dpi2.cd_documento_protocolo
			         WHERE dpi2.dt_exclusao IS NULL
			           AND dp2.dt_exclusao IS NULL
			           AND dp2.cd_gerencia_origem = m.cd_gerencia
			           AND dpi2.dt_indexacao IS NOT NULL
			           AND extract(month from dp2.dt_envio) = extract(month from m.dt_referencia)
			           AND extract(year from dp2.dt_envio)  = extract(year from m.dt_referencia)) AS qt_indexados,
			        (SELECT COUNT(*)
			          FROM projetos.documento_protocolo_conf_gerencia_item i2
			         WHERE i2.dt_exclusao IS NULL
			           AND i2.cd_documento_protocolo_conf_gerencia_item_mes = m.cd_documento_protocolo_conf_gerencia_item_mes
				       AND i2.dt_conferencia IS NOT NULL) AS qt_conferido,
				   (SELECT COUNT(*)
			          FROM projetos.documento_protocolo_conf_gerencia_item i2
			          JOIN projetos.documento_protocolo_item dpi2
			            ON dpi2.cd_documento_protocolo_item = i2.cd_documento_protocolo_item
			          JOIN projetos.documento_protocolo dp2
			            ON dp2.cd_documento_protocolo = dpi2.cd_documento_protocolo
			         WHERE i2.dt_exclusao IS NULL
			           AND dpi2.dt_exclusao IS NULL
			           AND dp2.dt_exclusao IS NULL
			           AND i2.cd_documento_protocolo_conf_gerencia_item_mes = m.cd_documento_protocolo_conf_gerencia_item_mes
				       AND i2.dt_conferencia IS NULL) AS qt_conferencia_pendente,
				   (SELECT COUNT(*)
				      FROM projetos.documento_protocolo_conf_gerencia_item_acompanhamento a2
					 WHERE a2.dt_exclusao       IS NULL  
					   AND a2.fl_acompanhamento = 'N'
					   AND a2.cd_documento_protocolo_conf_gerencia_item IN (SELECT i2.cd_documento_protocolo_conf_gerencia_item
					                                                          FROM projetos.documento_protocolo_conf_gerencia_item i2
			                                                                 WHERE i2.dt_exclusao IS NULL
			                                                                   AND i2.cd_documento_protocolo_conf_gerencia_item_mes = m.cd_documento_protocolo_conf_gerencia_item_mes)) AS qt_acompanhamento,
			       (SELECT COUNT(*)
				      FROM projetos.documento_protocolo_conf_gerencia_item_acompanhamento a2
					 WHERE a2.dt_exclusao       IS NULL  
					   AND a2.fl_acompanhamento = 'S'
					   AND a2.tp_acompanhamento = 'S'
					   AND a2.cd_documento_protocolo_conf_gerencia_item IN (SELECT i2.cd_documento_protocolo_conf_gerencia_item
					                                                          FROM projetos.documento_protocolo_conf_gerencia_item i2
			                                                                 WHERE i2.dt_exclusao IS NULL
			                                                                   AND i2.cd_documento_protocolo_conf_gerencia_item_mes = m.cd_documento_protocolo_conf_gerencia_item_mes)) AS qt_ajuste,
			       (SELECT COUNT(*)
				      FROM projetos.documento_protocolo_conf_gerencia_item_acompanhamento a2
					 WHERE a2.dt_exclusao       IS NULL  
					   AND a2.fl_acompanhamento = 'S'
					   AND a2.tp_acompanhamento = 'A'
					   AND a2.cd_documento_protocolo_conf_gerencia_item IN (SELECT i2.cd_documento_protocolo_conf_gerencia_item
					                                                          FROM projetos.documento_protocolo_conf_gerencia_item i2
			                                                                 WHERE i2.dt_exclusao IS NULL
			                                                                   AND i2.cd_documento_protocolo_conf_gerencia_item_mes = m.cd_documento_protocolo_conf_gerencia_item_mes)) AS qt_ajustado,
				   
				    TO_CHAR((m.dt_inclusao + INTERVAL '1 month')::date, 'DD/MM/YYYY') AS dt_limite,
				    TO_CHAR(m.dt_referencia, 'DD/MM/YYYY') AS dt_inclusao
			  FROM projetos.documento_protocolo_conf_gerencia_item_mes m
			 WHERE m.dt_exclusao IS NULL
			 ".(intval($args['mes_referencia']) > 0 ? "AND extract(month from m.dt_referencia) = ".intval($args['mes_referencia']) : "")."
			 ".(intval($args['ano_referencia']) > 0 ? "AND extract(year from m.dt_referencia) = ".intval($args['ano_referencia']) : "")."
			 ".(trim($args['cd_gerencia']) != '' ? "AND m.cd_gerencia = '".trim($args['cd_gerencia'])."'" : "").";";

/*
(SELECT COUNT(*)
			          FROM projetos.documento_protocolo_conf_gerencia_item i2
			         WHERE i2.dt_exclusao IS NULL
			           AND i2.cd_documento_protocolo_conf_gerencia_item_mes = m.cd_documento_protocolo_conf_gerencia_item_mes
					   AND (SELECT COUNT(*)
					          FROM projetos.documento_protocolo_conf_gerencia_item_acompanhamento a2
					         WHERE a2.dt_exclusao IS NULL  
						       AND a2.cd_documento_protocolo_conf_gerencia_item = i2.cd_documento_protocolo_conf_gerencia_item) > 0) AS qt_ajuste,
*/
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_ano_relatorio()
	{
		$qr_sql = "
			SELECT extract(year from dt_referencia) AS value,
				   extract(year from dt_referencia) AS text
			  FROM projetos.documento_protocolo_conf_gerencia_item_mes
			 WHERE dt_exclusao IS NULL
			 GROUP BY dt_referencia;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_gerencia()
	{
		$qr_sql = "
			SELECT codigo AS value,
			       nome AS text
			  FROM funcoes.get_gerencias_vigente('DIV');";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_docs_relatorio($cd_documento_protocolo_conf_gerencia_item_mes, $args = array())
	{
		$qr_sql = "
			SELECT dpcgi.cd_documento_protocolo_conf_gerencia_item,
				   funcoes.nr_protocolo_digitalizacao(dp.ano, dp.contador) AS nr_protocolo,
			       TO_CHAR(dp.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       funcoes.get_usuario_nome(dp.cd_usuario_envio) AS ds_usuario_envio,
			       TO_CHAR(dp.dt_ok, 'DD/MM/YYYY HH24:MI:SS') AS dt_recebimento,
			       funcoes.get_usuario_nome(dp.cd_usuario_ok) AS ds_usuario_recebimento,
			       TO_CHAR(dpi.dt_indexacao, 'DD/MM/YYYY') AS dt_indexacao,
			       TO_CHAR(dpcgi.dt_conferencia, 'DD/MM/YYYY HH24:MI:SS') AS dt_conferencia,
			       funcoes.get_usuario_nome(dpcgi.cd_usuario_conferencia) AS ds_usuario_conferencia,
			       dpi.cd_empresa ||'/'||dpi.cd_registro_empregado||'/'||dpi.seq_dependencia AS nr_re,
			       dpi.cd_tipo_doc,
			       dpi.nr_id_contrato,
			       dpi.ds_processo,
			       dpi.ds_caminho_liquid AS ds_caminho,
			       dpi.nr_folha,
			       dpi.arquivo,
			       dpi.arquivo_nome,
			       dpi.observacao,
			       COALESCE(p.nome,'') AS ds_participante,
			       td.nome_documento AS ds_documento,
			       (CASE WHEN dpcgi.fl_status = 'P' THEN 'Pendente'
			             WHEN dpcgi.fl_status = 'A' THEN 'Ajustes'
			             ELSE 'Conferido'
			        END) AS ds_status,
			       (CASE WHEN dpcgi.fl_status = 'P' THEN 'label label-important'
			             WHEN dpcgi.fl_status = 'A' THEN 'label label-warning'
			             ELSE 'label label-success'
			        END) AS ds_label_status,
			        dpcgi.fl_status,
			        (SELECT COUNT(*)
			           FROM projetos.documento_protocolo_conf_gerencia_item_acompanhamento dpcgia
			          WHERE dpcgia.dt_exclusao IS NULL
			            AND dpcgia.cd_documento_protocolo_conf_gerencia_item = dpcgi.cd_documento_protocolo_conf_gerencia_item) AS qtd_acomp
			  FROM projetos.documento_protocolo_conf_gerencia_item dpcgi
			  JOIN projetos.documento_protocolo_conf_gerencia_item_mes dpcgim
			    ON dpcgim.cd_documento_protocolo_conf_gerencia_item_mes = dpcgi.cd_documento_protocolo_conf_gerencia_item_mes
			  JOIN projetos.documento_protocolo_item dpi
			    ON dpi.cd_documento_protocolo_item = dpcgi.cd_documento_protocolo_item
			  JOIN projetos.documento_protocolo_conf_gerencia dpcg
			  ON dpcg.cd_gerencia = dpcgim.cd_gerencia
			  JOIN projetos.documento_protocolo dp
			    ON dp.cd_documento_protocolo = dpi.cd_documento_protocolo
			  LEFT JOIN public.participantes p
			    ON p.cd_registro_empregado = dpi.cd_registro_empregado
			   AND p.cd_empresa = dpi.cd_empresa
			   AND p.seq_dependencia = dpi.seq_dependencia
			  LEFT JOIN public.tipo_documentos td 
			    ON td.cd_tipo_doc = dpi.cd_tipo_doc
			 WHERE dpcgi.dt_exclusao  IS NULL
			   AND dpi.dt_exclusao    IS NULL
			   AND dp.dt_exclusao     IS NULL
			   AND dpcgim.dt_exclusao IS NULL
			   AND dpcgim.cd_documento_protocolo_conf_gerencia_item_mes = ".intval($cd_documento_protocolo_conf_gerencia_item_mes)."
			   ".(trim($args['fl_status']) != '' ? 'AND dpcgi.fl_status = '.str_escape($args['fl_status']) : "").";";


		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_docs($cd_usuario, $args = array())
	{
		$qr_sql = "
			SELECT dpcgi.cd_documento_protocolo_conf_gerencia_item,
				   funcoes.nr_protocolo_digitalizacao(dp.ano, dp.contador) AS nr_protocolo,
			       TO_CHAR(dp.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       funcoes.get_usuario_nome(dp.cd_usuario_envio) AS ds_usuario_envio,
			       TO_CHAR(dp.dt_ok, 'DD/MM/YYYY HH24:MI:SS') AS dt_recebimento,
			       funcoes.get_usuario_nome(dp.cd_usuario_ok) AS ds_usuario_recebimento,
			       TO_CHAR(dpi.dt_indexacao, 'DD/MM/YYYY') AS dt_indexacao,
			       TO_CHAR(dpcgi.dt_conferencia, 'DD/MM/YYYY HH24:MI:SS') AS dt_conferencia,
			       dpi.cd_empresa ||'/'||dpi.cd_registro_empregado||'/'||dpi.seq_dependencia AS nr_re,
			       dpi.cd_tipo_doc,
			       dpi.ds_processo,
			       dpi.nr_id_contrato,
			       dpi.ds_caminho_liquid AS ds_caminho,
			       dpi.nr_folha,
			       dpi.arquivo,
			       dpi.arquivo_nome,
			       dpi.cd_documento_protocolo,
			       COALESCE(p.nome,'') AS ds_participante,
			       td.nome_documento AS ds_documento,
			       dpcgi.ds_ajuste,
			       dpcgi.fl_status,
			       dpi.observacao,
			       (SELECT COUNT(*)
			          FROM projetos.documento_protocolo_conf_gerencia_item_acompanhamento dpcgia
			         WHERE dpcgia.dt_exclusao IS NULL
			           AND dpcgia.cd_documento_protocolo_conf_gerencia_item = dpcgi.cd_documento_protocolo_conf_gerencia_item) AS qtd_acomp
			  FROM projetos.documento_protocolo_conf_gerencia_item dpcgi
			  JOIN projetos.documento_protocolo_conf_gerencia_item_mes dpcgim
			    ON dpcgim.cd_documento_protocolo_conf_gerencia_item_mes = dpcgi.cd_documento_protocolo_conf_gerencia_item_mes
			  JOIN projetos.documento_protocolo_item dpi
			    ON dpi.cd_documento_protocolo_item = dpcgi.cd_documento_protocolo_item
			  JOIN projetos.documento_protocolo_conf_gerencia dpcg
			    ON dpcg.cd_gerencia = dpcgim.cd_gerencia
			  JOIN projetos.documento_protocolo dp
			    ON dp.cd_documento_protocolo = dpi.cd_documento_protocolo
			  LEFT JOIN public.participantes p
			    ON p.cd_registro_empregado = dpi.cd_registro_empregado
			   AND p.cd_empresa = dpi.cd_empresa
			   AND p.seq_dependencia = dpi.seq_dependencia
			  LEFT JOIN public.tipo_documentos td 
			    ON td.cd_tipo_doc = dpi.cd_tipo_doc
			 WHERE dpcgi.dt_exclusao  IS NULL
			   AND dpi.dt_exclusao    IS NULL
			   AND dp.dt_exclusao     IS NULL
			   AND dpcgim.dt_exclusao IS NULL
			   AND (dpcg.cd_usuario_responsavel = ".intval($cd_usuario)." OR dp.cd_usuario_envio = ".intval($cd_usuario).")
			   ".(intval($args['mes_referencia']) > 0 ? "AND extract(month from dpcgim.dt_referencia) = ".intval($args['mes_referencia']) : "")."
			   ".(intval($args['ano_referencia']) > 0 ? "AND extract(year from dpcgim.dt_referencia) = ".intval($args['ano_referencia']) : "")."
			   ".(trim($args['fl_status']) != '' ? 'AND dpcgi.fl_status = '.str_escape($args['fl_status']) : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_doc($cd_documento_protocolo_conf_gerencia_item)
	{
		$qr_sql = "
			SELECT dpcgi.cd_documento_protocolo_conf_gerencia_item,
				   dpcgi.cd_documento_protocolo_conf_gerencia_item_mes,
				   funcoes.nr_protocolo_digitalizacao(dp.ano, dp.contador) AS nr_protocolo,
			       TO_CHAR(dp.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       funcoes.get_usuario_nome(dp.cd_usuario_envio) AS ds_usuario_envio,
			       TO_CHAR(dp.dt_ok, 'DD/MM/YYYY HH24:MI:SS') AS dt_recebimento,
			       funcoes.get_usuario_nome(dp.cd_usuario_ok) AS ds_usuario_recebimento,
			       dp.cd_usuario_envio,
			       TO_CHAR(dpi.dt_indexacao, 'DD/MM/YYYY') AS dt_indexacao,
			       TO_CHAR(dpcgi.dt_conferencia, 'DD/MM/YYYY HH24:MI:SS') AS dt_conferencia,
			       dpi.cd_empresa ||'/'||dpi.cd_registro_empregado||'/'||dpi.seq_dependencia AS nr_re,
			       dpi.cd_tipo_doc,
			       dpi.ds_processo,
			       dpi.ds_caminho_liquid AS ds_caminho,
			       dpi.nr_folha,
			       dpi.arquivo,
			       dpi.arquivo_nome,
			       COALESCE(p.nome,'') AS ds_participante,
			       td.nome_documento AS ds_documento,
			       dpcgi.ds_ajuste,
			       dpcgi.fl_status,
			       dpcg.cd_usuario_responsavel,
			       funcoes.get_usuario(dpcg.cd_usuario_responsavel) || '@eletroceee.com.br' AS ds_usuario_responsavel_email,
			       funcoes.get_usuario(dp.cd_usuario_envio) || '@eletroceee.com.br' AS ds_usuario_envio_email,
			       (CASE WHEN dpcgi.fl_status = 'P' THEN 'Pendente'
			             WHEN dpcgi.fl_status = 'A' THEN 'Ajustes'
			             ELSE 'Conferido'
			        END) AS ds_status,
			       (CASE WHEN dpcgi.fl_status = 'P' THEN 'label label-important'
			             WHEN dpcgi.fl_status = 'A' THEN 'label label-warning'
			             ELSE 'label label-success'
			        END) AS ds_label_status,
			       TO_CHAR((dpcgim.dt_inclusao + INTERVAL '1 month')::date, 'DD/MM/YYYY') AS dt_limite
			  FROM projetos.documento_protocolo_conf_gerencia_item dpcgi
			  JOIN projetos.documento_protocolo_conf_gerencia_item_mes dpcgim
			    ON dpcgim.cd_documento_protocolo_conf_gerencia_item_mes = dpcgi.cd_documento_protocolo_conf_gerencia_item_mes
			  JOIN projetos.documento_protocolo_item dpi
			    ON dpi.cd_documento_protocolo_item = dpcgi.cd_documento_protocolo_item
			  JOIN projetos.documento_protocolo_conf_gerencia dpcg
			    ON dpcg.cd_gerencia = dpcgim.cd_gerencia
			  JOIN projetos.documento_protocolo dp
			    ON dp.cd_documento_protocolo = dpi.cd_documento_protocolo
			  LEFT JOIN public.participantes p
			    ON p.cd_registro_empregado = dpi.cd_registro_empregado
			   AND p.cd_empresa = dpi.cd_empresa
			   AND p.seq_dependencia = dpi.seq_dependencia
			  LEFT JOIN public.tipo_documentos td 
			    ON td.cd_tipo_doc = dpi.cd_tipo_doc
			 WHERE dpcgi.dt_exclusao  IS NULL
			   AND dpi.dt_exclusao    IS NULL
			   AND dp.dt_exclusao     IS NULL
			   AND dpcgim.dt_exclusao IS NULL
			   AND dpcgi.cd_documento_protocolo_conf_gerencia_item = ".intval($cd_documento_protocolo_conf_gerencia_item).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_mes_ano_conferir($cd_divisao)
	{
		$qr_sql = "
			SELECT TO_CHAR(extract(month from dpcgim.dt_referencia)::integer, 'FM00') AS mes_referencia,
			       extract(year from dpcgim.dt_referencia)::integer AS ano_referencia
			  FROM projetos.documento_protocolo_conf_gerencia_item_mes dpcgim
			 WHERE dpcgim.dt_exclusao IS NULL
			   AND dpcgim.cd_gerencia = '".trim($cd_divisao)."'
			   AND (SELECT COUNT(*)
			          FROM projetos.documento_protocolo_conf_gerencia_item dpcgi
			         WHERE dpcgi.dt_exclusao IS NULL
			           AND dpcgi.dt_conferencia IS NULL
			           AND dpcgi.cd_documento_protocolo_conf_gerencia_item_mes = dpcgim.cd_documento_protocolo_conf_gerencia_item_mes) > 0
			 ORDER BY dpcgim.dt_referencia ASC
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_conferencia($cd_documento_protocolo_conf_gerencia_item, $fl_status, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.documento_protocolo_conf_gerencia_item
			   SET dt_conferencia 		  = CURRENT_TIMESTAMP,
			       cd_usuario_conferencia = ".intval($cd_usuario).",
			       fl_status 			  = ".(trim($fl_status) != '' ? str_escape($fl_status) : "DEFAULT").",
			       dt_alteracao 		  = CURRENT_TIMESTAMP,
			       cd_usuario_alteracao   = ".intval($cd_usuario).",
			       ds_ajuste 			  = DEFAULT
			 WHERE cd_documento_protocolo_conf_gerencia_item = ".intval($cd_documento_protocolo_conf_gerencia_item).";";

		$this->db->query($qr_sql);
	}

	public function salvar_ajuste($cd_documento_protocolo_conf_gerencia_item, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.documento_protocolo_conf_gerencia_item
			   SET ds_ajuste 			= ".(trim($args['ds_ajuste']) != '' ? str_escape($args['ds_ajuste']) : "DEFAULT").",
			   	   fl_status 			= ".(trim($args['fl_status']) != '' ? str_escape($args['fl_status']) : "DEFAULT").",
			   	   dt_alteracao 		= CURRENT_TIMESTAMP,
			   	   cd_usuario_alteracao = ".intval($args['cd_usuario'])."
			 WHERE cd_documento_protocolo_conf_gerencia_item = ".intval($cd_documento_protocolo_conf_gerencia_item).";";

		$this->db->query($qr_sql);
	}

	public function salvar_acompanhamento($args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.documento_protocolo_conf_gerencia_item_acompanhamento
				(
					cd_documento_protocolo_conf_gerencia_item,
					ds_acompanhamento,
					fl_acompanhamento,
					tp_acompanhamento,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				)
			VALUES
				(
					".(intval($args['cd_documento_protocolo_conf_gerencia_item']) > 0 ? intval($args['cd_documento_protocolo_conf_gerencia_item']) : "DEFAULT").",
					".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
					".(trim($args['fl_acompanhamento']) != '' ? str_escape($args['fl_acompanhamento']) : "DEFAULT").",
					".(trim($args['tp_acompanhamento']) != '' ? str_escape($args['tp_acompanhamento']) : "DEFAULT").",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				);";

		$this->db->query($qr_sql);
	}

	public function listar_acompanhamento($cd_documento_protocolo_conf_gerencia_item)
	{
		$qr_sql = "
			SELECT cd_documento_protocolo_conf_gerencia_item_acompanhamento,
				   ds_acompanhamento,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao,
				   cd_usuario_inclusao,
				   fl_acompanhamento
			  FROM projetos.documento_protocolo_conf_gerencia_item_acompanhamento
			 WHERE dt_exclusao IS NULL
			   AND cd_documento_protocolo_conf_gerencia_item = ".intval($cd_documento_protocolo_conf_gerencia_item).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_acompanhamento($cd_documento_protocolo_conf_gerencia_item_acompanhamento)
	{
		$qr_sql = "
			SELECT cd_usuario_inclusao,
				   fl_acompanhamento
			  FROM projetos.documento_protocolo_conf_gerencia_item_acompanhamento
			 WHERE dt_exclusao IS NULL
			   AND cd_documento_protocolo_conf_gerencia_item_acompanhamento = ".intval($cd_documento_protocolo_conf_gerencia_item_acompanhamento).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function excluir_acompanhamento($cd_documento_protocolo_conf_gerencia_item_acompanhamento, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.documento_protocolo_conf_gerencia_item_acompanhamento
			   SET cd_usuario_exclusao = ".(intval($cd_usuario) > 0 ? intval($cd_usuario) : "DEFAULT").",
			       dt_exclusao 		   = CURRENT_TIMESTAMP
			 WHERE cd_documento_protocolo_conf_gerencia_item_acompanhamento = ".intval($cd_documento_protocolo_conf_gerencia_item_acompanhamento).";";

		$this->db->query($qr_sql);
	}

	public function get_acompanhamento($cd_documento_protocolo_conf_gerencia_item)
	{
		$qr_sql = "
			SELECT cd_usuario_inclusao
			  FROM projetos.documento_protocolo_conf_gerencia_item_acompanhamento
			 WHERE dt_exclusao 								 IS NULL
			   AND fl_acompanhamento 						 = 'S'
			   AND cd_documento_protocolo_conf_gerencia_item = ".intval($cd_documento_protocolo_conf_gerencia_item)."
			 ORDER BY dt_inclusao DESC
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}
}