<?php
class Documento_integra_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_documento_integra_doc_tipo,
				   ds_documento_integra_doc_tipo, 
				   ds_caminho, 
				   cd_gerencia, 
			 	   cd_usuario_responsavel,
				   cd_usuario_responsavel_2,
				   funcoes.get_usuario_nome(cd_usuario_responsavel) AS ds_responsavel,
				   funcoes.get_usuario_nome(cd_usuario_responsavel_2) AS ds_responsavel_2,
				   TO_CHAR(dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
				   tp_periodicidade,
				   CASE WHEN tp_periodicidade = 'M' THEN 'Mensal'
				        ELSE ''
				   END AS ds_periodicidade
			  FROM gestao.documento_integra_doc_tipo
			 WHERE dt_exclusao IS NULL
			   ".(trim($args['cd_gerencia']) != '' ? "AND cd_gerencia = '".trim($args['cd_gerencia'])."'" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function minhas_listar($cd_responsavel, $args = array())
	{
		$qr_sql = "
			SELECT d.cd_documento_integra,
			       funcoes.get_usuario_nome(d.cd_usuario_alteracao) AS ds_usuario_inclusao,
			       TO_CHAR(d.dt_alteracao, 'DD/MM/YYYY HH24:MI') AS dt_alteracao,
			       TO_CHAR(d.dt_envio, 'DD/MM/YYYY HH24:MI') AS dt_envio,
			       TO_CHAR(d.dt_referencia, 'DD/MM/YYYY') AS dt_referencia_adicionado,
			       TO_CHAR(d.dt_referencia, 'YYYY') AS nr_ano,
			       TO_CHAR(d.dt_referencia, 'MM') AS nr_mes,
			       d.ds_caminho AS ds_caminho_completo,
			       t.cd_documento_integra_doc_tipo,
			       t.ds_documento_integra_doc_tipo, 
			       t.ds_caminho, 
			       t.cd_gerencia, 
			       t.cd_usuario_responsavel,
			       t.cd_usuario_responsavel_2,
			       funcoes.get_usuario_nome(t.cd_usuario_responsavel) AS ds_responsavel,
			       funcoes.get_usuario_nome(t.cd_usuario_responsavel_2) AS ds_responsavel_2,
			       funcoes.get_usuario_nome(d.cd_usuario_envio) AS ds_usuario_envio,
			       TO_CHAR(t.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
			       
			       t.tp_periodicidade,
			       CASE WHEN t.tp_periodicidade = 'M' THEN 'Mensal'
					    ELSE ''
			       END AS ds_periodicidade
		      FROM gestao.documento_integra d
		      JOIN gestao.documento_integra_doc_tipo t
                ON t.cd_documento_integra_doc_tipo = d.cd_documento_integra_doc_tipo
             WHERE t.dt_exclusao IS NULL
			   AND ".intval($cd_responsavel)." IN (cd_usuario_responsavel, cd_usuario_responsavel_2, 251, 468, 424, 474)
			   ".(trim($args['cd_documento_integra_doc_tipo']) != '' ? "AND t.cd_documento_integra_doc_tipo = ".intval($args['cd_documento_integra_doc_tipo']) : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_doc_tipo($cd_documento_integra_doc_tipo)
	{
		$qr_sql = "
			SELECT t.cd_documento_integra_doc_tipo,
				   t.ds_documento_integra_doc_tipo,
				   t.ds_caminho,
				   t.tp_periodicidade
			  FROM gestao.documento_integra_doc_tipo t
			 WHERE t.dt_exclusao IS NULL
			   AND t.cd_documento_integra_doc_tipo = ".intval($cd_documento_integra_doc_tipo).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_documento_integra)
	{
		$qr_sql = "
			SELECT d.cd_documento_integra,
			       d.cd_documento_integra_doc_tipo,
			       t.ds_documento_integra_doc_tipo,
			       TO_CHAR(d.dt_referencia, 'YYYY') AS nr_ano,
			       TO_CHAR(d.dt_referencia, 'MM') AS nr_mes,
			       TO_CHAR(d.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
			       TO_CHAR(d.dt_envio, 'DD/MM/YYYY') AS dt_envio,
                   funcoes.get_usuario_nome(d.cd_usuario_envio) AS ds_usuario_envio,
                   d.ds_caminho AS ds_caminho_completo,
				   t.tp_periodicidade,
				   d.ds_referencia
			  FROM gestao.documento_integra d
			  JOIN gestao.documento_integra_doc_tipo t
                ON t.cd_documento_integra_doc_tipo = d.cd_documento_integra_doc_tipo
             WHERE d.cd_documento_integra = ".intval($cd_documento_integra).";";

        return $this->db->query($qr_sql)->row_array();
	}

	public function minhas_salvar($args = array())
	{
		$cd_documento_integra = intval($this->db->get_new_id('gestao.documento_integra', 'cd_documento_integra'));
		
		$qr_sql = "
			INSERT INTO gestao.documento_integra
			     (
			        cd_documento_integra,
					cd_documento_integra_doc_tipo, 
					dt_referencia,
					ds_referencia,
					ds_caminho,
					cd_usuario_inclusao, 
					cd_usuario_alteracao
				  )
	         VALUES 
	              (
	              	".intval($cd_documento_integra).",
	              	".(trim($args['cd_documento_integra_doc_tipo']) != '' ? intval($args['cd_documento_integra_doc_tipo']) : "DEFAULT").",
	              	".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
	              	".(trim($args['ds_referencia']) != '' ? "funcoes.remove_acento('".trim($args['ds_referencia'])."')" : "DEFAULT").",
	              	".(trim($args['ds_caminho']) != '' ? "funcoes.remove_acento('".trim($args['ds_caminho'])."')" : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
	              );";

		$this->db->query($qr_sql);

		return $cd_documento_integra;
	}

	public function anexar_documento($cd_documento_integra, $args = array())
	{
		$qr_sql = "
            INSERT INTO gestao.documento_integra_anexo
                 (
                    cd_documento_integra,
                    arquivo, 
                    arquivo_nome, 
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_documento_integra).",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);
	}

	public function listar_anexar_documento($cd_documento_integra)
    {
        $qr_sql = "
            SELECT cd_documento_integra_anexo,
                   arquivo,
                   funcoes.remove_acento(arquivo_nome) AS arquivo_nome,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario
              FROM gestao.documento_integra_anexo
             WHERE cd_documento_integra = ".intval($cd_documento_integra)."
               AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function excluir_documento($cd_documento_integra_anexo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.documento_integra_anexo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_documento_integra_anexo = ".intval($cd_documento_integra_anexo).";";

        $this->db->query($qr_sql);
    }

    public function envia($cd_documento_integra, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.documento_integra
               SET cd_usuario_envio = ".intval($cd_usuario).",
                   dt_envio         = CURRENT_TIMESTAMP
             WHERE cd_documento_integra = ".intval($cd_documento_integra).";";

        $this->db->query($qr_sql);
    }
}