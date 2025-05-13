<?php
class Protocolo_digitalizacao_expedida_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($args = array(), $cd_usuario)
	{
		$qr_sql = "
			SELECT funcoes.nr_protocolo_digitalizacao(a.ano, a.contador) || ' - ' || a.tipo AS nr_protocolo,
				   a.cd_gerencia_origem,
				   b.cd_documento_protocolo_item,
				   TO_CHAR(b.dt_cadastro, 'DD/MM/YYYY HH24:MI:SS') AS dt_cadastro,
				   TO_CHAR(a.dt_envio, 'DD/MM/YYYY HH24:MI') AS dt_envio,
				   b.cd_empresa || '/' || b.cd_registro_empregado || '/' || b.seq_dependencia AS re,
				   c.nome AS nome_participante,
				   b.cd_tipo_doc,
				   d.nome_documento,
				   TO_CHAR(f.dt_gerado, 'DD/MM/YYYY HH24:MI') AS dt_gerado
			  FROM projetos.documento_protocolo a
			  JOIN projetos.documento_protocolo_item b
				ON a.cd_documento_protocolo = b.cd_documento_protocolo
			  JOIN public.participantes c
				ON b.cd_empresa = c.cd_empresa
			   AND b.cd_registro_empregado = c.cd_registro_empregado
			   AND b.seq_dependencia = c.seq_dependencia
			  LEFT JOIN public.tipo_documentos d
			  ON b.cd_tipo_doc = d.cd_tipo_doc
			  LEFT JOIN projetos.protocolo_digitalizacao_expedida_item e
				ON e.cd_documento_protocolo_item = b.cd_documento_protocolo_item
			  LEFT JOIN projetos.protocolo_digitalizacao_expedida f
			    ON e.cd_protocolo_digitalizacao_expedida = f.cd_protocolo_digitalizacao_expedida
			 WHERE a.dt_exclusao IS NULL
			   AND b.dt_exclusao IS NULL
			    ".(intval($args['ano']) > 0 ? "AND a.ano = ".intval($args['ano']) : "")."
                ".(intval($args['numero']) > 0 ? "AND a.contador = ".intval($args['numero']) : "")."
                ".(trim($args['tipo']) != "" ? "AND a.tipo = '".trim($args['tipo'])."'" : "")."
				".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? " AND CAST(a.dt_cadastro AS DATE) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
                ".(trim($args['fl_gerado']) == "N" ? "AND f.dt_gerado IS NULL " : "")."
				".(trim($args['fl_gerado']) == "S" ? "AND f.dt_gerado IS NOT NULL " : "")."
			  AND (a.cd_usuario_cadastro = ".(intval($cd_usuario))."
			   OR a.cd_usuario_envio = ".(intval($cd_usuario)).");";

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar($documento, $cd_usuario)
	{
		$cd_protocolo_digitalizacao_expedida = intval($this->db->get_new_id("projetos.protocolo_digitalizacao_expedida", "cd_protocolo_digitalizacao_expedida"));
		
		$qr_sql = "
                INSERT INTO projetos.protocolo_digitalizacao_expedida
                     (
                       cd_protocolo_digitalizacao_expedida,
					   cd_usuario_inclusao
                     )
                VALUES
                      (
                        ".intval($cd_protocolo_digitalizacao_expedida).",
                        ".trim($cd_usuario)."
					  );";
					  
		$this->db->query($qr_sql);

		$qr_sql = "
			INSERT INTO projetos.protocolo_digitalizacao_expedida_item
                 (
                   cd_protocolo_digitalizacao_expedida, 
                   cd_documento_protocolo_item, 
                   cd_usuario_inclusao
                 )
            SELECT ".intval($cd_protocolo_digitalizacao_expedida).", x.column1, ".intval($cd_usuario)."
			  FROM (VALUES (".implode("),(", $documento).")) x";

		$this->db->query($qr_sql);
		
		return $cd_protocolo_digitalizacao_expedida;
	}
	
	public function gerar_protocolo_expedido($args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.atendimento_protocolo
				(
					cd_empresa,
					cd_registro_empregado,
					seq_dependencia,
					nome,
					destino,
					tipo,
					identificacao,
					cd_atendimento_protocolo_tipo,
					cd_atendimento_protocolo_discriminacao,
					dt_inclusao,
					cd_usuario_inclusao,
					cd_gerencia_origem
				)
			SELECT b.cd_empresa,
			   	   b.cd_registro_empregado,
				   b.seq_dependencia,
				   c.nome,
				   c.endereco || ', ' || c.nr_endereco||COALESCE(c.complemento_endereco, '') || ', ' || c.bairro || ', ' || c.cidade || '-' || c.unidade_federativa,
				   (SELECT nome FROM projetos.atendimento_protocolo_tipo WHERE cd_atendimento_protocolo_tipo = ".intval($args['cd_atendimento_protocolo_tipo'])."),
				   ".str_escape($args['ds_identificacao']).",
				   ".intval($args['cd_atendimento_protocolo_tipo']).",
				   ".intval($args['cd_atendimento_protocolo_discriminacao']).",
				   CURRENT_TIMESTAMP,
				   ".intval($args['cd_usuario']).",
				   funcoes.get_usuario_area(b.cd_usuario_cadastro)
			  FROM projetos.documento_protocolo a
			  JOIN projetos.documento_protocolo_item b
			    ON a.cd_documento_protocolo = b.cd_documento_protocolo
			  LEFT JOIN public.participantes c
				ON b.cd_empresa            = c.cd_empresa 
			   AND b.cd_registro_empregado = c.cd_registro_empregado  
			   AND b.seq_dependencia       = c.seq_dependencia
			 WHERE a.dt_exclusao IS NULL
			   AND b.dt_exclusao IS NULL
			   AND b.cd_documento_protocolo_item IN (SELECT cd_documento_protocolo_item
					                                   FROM projetos.protocolo_digitalizacao_expedida_item
					                                  WHERE cd_protocolo_digitalizacao_expedida = ".intval($args['cd_protocolo_digitalizacao_expedida']).");

			UPDATE projetos.protocolo_digitalizacao_expedida
			   SET cd_usuario_gerado = ".intval($args['cd_usuario']).",
			       dt_gerado         = CURRENT_TIMESTAMP
			 WHERE cd_protocolo_digitalizacao_expedida = ".intval($args['cd_protocolo_digitalizacao_expedida']).";";
			 
		$this->db->query($qr_sql);
	}
	
	public function get_tipo()
    {
        $qr_sql = "
			SELECT cd_atendimento_protocolo_tipo AS value,
                   nome  AS text
			  FROM projetos.atendimento_protocolo_tipo
			 WHERE dt_exclusao IS NULL
		     ORDER BY nome;";
        
        return $this->db->query($qr_sql)->result_array();
    }

    public function get_discriminacao()
    {
        $qr_sql = "
			SELECT cd_atendimento_protocolo_discriminacao AS value,
                   nome AS text
			  FROM projetos.atendimento_protocolo_discriminacao
			 WHERE dt_exclusao IS NULL
		     ORDER BY nome;";

        return $this->db->query($qr_sql)->result_array();
    }
}