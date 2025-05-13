<?php
class Meus_treinamentos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_registro_empregado, $args = array())
	{
		$qr_sql = "
			SELECT tci.cd_registro_empregado,
				   funcoes.nr_treinamento_colaborador(tc.ano, tc.numero) AS numero,
				   tc.ano,
				   tc.nome,
				   tc.promotor,
				   tc.uf,
				   tc.cidade,
				   tc.carga_horaria,
				   tc.cd_treinamento_colaborador,
				   tci.cd_treinamento_colaborador_item,
				   TO_CHAR(tc.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
			       TO_CHAR(tc.dt_final, 'DD/MM/YYYY') AS dt_final,
				   tc.cd_treinamento_colaborador_tipo,
				   tct.ds_treinamento_colaborador_tipo,
				   COALESCE(tci.fl_certificado, tc.fl_certificado) AS fl_certificado,
				   tci.arquivo,
				   tci.arquivo_nome,
				   tci.dt_certificado,
				   tci.ds_justificativa,
				   COALESCE(tc.fl_certificado, 'S') AS fl_certificado_treinamento
			  FROM projetos.treinamento_colaborador tc
			  JOIN projetos.treinamento_colaborador_item tci
			    ON tci.ano    = tc.ano
			   AND tci.numero = tc.numero
			  LEFT JOIN projetos.treinamento_colaborador_tipo tct
			    ON tct.cd_treinamento_colaborador_tipo = tc.cd_treinamento_colaborador_tipo
			 WHERE tc.dt_exclusao IS NULL
			   AND tci.dt_exclusao IS NULL
			   AND tci.cd_empresa            = 9
			   AND tci.seq_dependencia       = 0
			   AND tci.cd_registro_empregado = ".intval($cd_registro_empregado)."
			   ".(trim($args['fl_certificado']) == 'N' ? "AND tci.fl_certificado = 'N'" : "")."
			   ".(trim($args['fl_certificado']) == 'S' ? "AND tci.fl_certificado = 'S'" : "")."
			   ".(trim($args['fl_certificado']) == 'P' ? "AND COALESCE(tc.fl_certificado, 'S') != 'N' AND tci.fl_certificado IS NULL" : "")."
			   ".(trim($args['nome']) != '' ? "AND UPPER(tc.nome) LIKE UPPER('%".trim($args['nome'])."%')" : "")."
			   ".(trim($args['numero']) != '' ? "AND tc.numero = ".intval($args['numero']) : "")."
			   ".(trim($args['ano']) != '' ? "AND tc.ano = ".intval($args['ano']) : "")."
			   ".(((trim($args['dt_inicio_ini']) != '') AND (trim($args['dt_inicio_fim']) != '')) ? " AND DATE_TRUNC('day', tc.dt_inicio) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "")."						 
               ".(((trim($args['dt_final_ini']) != '') AND (trim($args['dt_final_fim']) != '')) ? " AND DATE_TRUNC('day', tc.dt_final) BETWEEN TO_DATE('".$args['dt_final_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_final_fim']."', 'DD/MM/YYYY')" : "")."						 
               ".(trim($args['cd_treinamento_colaborador_tipo']) != '' ? "AND tc.cd_treinamento_colaborador_tipo = '".intval($args['cd_treinamento_colaborador_tipo'])."'" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_treinamento_colaborador_item)
	{
		$qr_sql = "
			SELECT tc.cd_treinamento_colaborador,
			       funcoes.nr_treinamento_colaborador(tc.ano, tc.numero) AS numero,
			       COALESCE(tc.fl_certificado, tci.fl_certificado) AS fl_certificado,
			       tc.ano,
			       tc.nome,
			       tc.promotor,
			       TO_CHAR(tc.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
			       TO_CHAR(tc.dt_final, 'DD/MM/YYYY') AS dt_final,
			       tc.uf,
			       tci.arquivo,
				   tci.arquivo_nome,
				   tci.cd_registro_empregado,
			       tci.cd_treinamento_colaborador_item,
			       tc.cd_treinamento_colaborador_tipo,
			       tct.ds_treinamento_colaborador_tipo,
			       tci.ds_justificativa
	  		  FROM projetos.treinamento_colaborador_item tci
	  		  JOIN projetos.treinamento_colaborador tc
			    ON tci.ano    = tc.ano
			   AND tci.numero = tc.numero
			  LEFT JOIN projetos.treinamento_colaborador_tipo tct
			    ON tct.cd_treinamento_colaborador_tipo = tc.cd_treinamento_colaborador_tipo
	  		 WHERE tci.cd_treinamento_colaborador_item = ".intval($cd_treinamento_colaborador_item).";";
	  		 
		return $this->db->query($qr_sql)->row_array();
	}

	public function get_tipo()
	{
		$qr_sql = "
			SELECT cd_treinamento_colaborador_tipo AS value,
                   ds_treinamento_colaborador_tipo AS text
              FROM projetos.treinamento_colaborador_tipo
             WHERE dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_anexo($cd_treinamento_colaborador_item, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.treinamento_colaborador_item
			   SET fl_certificado         = ".(trim($args['fl_certificado']) != '' ? "'".trim($args['fl_certificado'])."'" : "DEFAULT").",
			       arquivo                = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
			       arquivo_nome           = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
			       ds_justificativa       = ".(trim($args['ds_justificativa']) != '' ? "'".trim($args['ds_justificativa'])."'" : "DEFAULT").",
			       cd_usuario_certificado = ".intval($args['cd_usuario']).",
			       dt_certificado         = CURRENT_TIMESTAMP
			 WHERE cd_treinamento_colaborador_item = ".intval($cd_treinamento_colaborador_item).";";

		$this->db->query($qr_sql);
	}

	public function listar_documento($cd_treinamento_colaborador_item)
	{
		$qr_sql = "
            SELECT cd_treinamento_colaborador_documento, 
                   cd_usuario_inclusao,
                   arquivo, 
                   arquivo_nome, 
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
              FROM projetos.treinamento_colaborador_documento 
             WHERE cd_treinamento_colaborador_item = ".intval($cd_treinamento_colaborador_item)."
               AND dt_exclusao                     IS NULL;";

        return $this->db->query($qr_sql)->result_array();
	}

    public function salvar_documento($cd_treinamento_colaborador_item, $args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.treinamento_colaborador_documento
                 (
                    cd_treinamento_colaborador_item,
                    cd_treinamento_colaborador,
                    arquivo, 
                    arquivo_nome, 
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_treinamento_colaborador_item).",
                    ".intval($args['cd_treinamento_colaborador']).",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);
    }

    public function excluir_documento($cd_treinamento_colaborador_documento, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.treinamento_colaborador_documento
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_treinamento_colaborador_documento = ".intval($cd_treinamento_colaborador_documento).";";

        $this->db->query($qr_sql);
    }

}