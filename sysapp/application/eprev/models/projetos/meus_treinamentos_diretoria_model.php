<?php
class Meus_treinamentos_diretoria_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_registro_empregado, $args = array())
	{
		$qr_sql = "
			SELECT tci.cd_registro_empregado,
				   funcoes.nr_treinamento_colaborador(tc.nr_ano, tc.nr_numero) AS numero,
				   tc.nr_ano,
				   tc.ds_nome,
				   tc.ds_promotor,
				   tc.ds_uf,
				   tc.ds_cidade,
				   tc.nr_carga_horaria,
				   tc.cd_treinamento_diretoria_conselhos,
				   tci.cd_treinamento_diretoria_conselhos_item,
				   TO_CHAR(tc.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
			       TO_CHAR(tc.dt_final, 'DD/MM/YYYY') AS dt_final,
				   tc.cd_treinamento_colaborador_tipo,
				   tct.ds_treinamento_colaborador_tipo,
				   tci.arquivo,
				   tci.arquivo_nome
			  FROM projetos.treinamento_diretoria_conselhos tc
			  JOIN projetos.treinamento_diretoria_conselhos_item tci
			    ON tci.cd_treinamento_diretoria_conselhos    = tc.cd_treinamento_diretoria_conselhos
			  LEFT JOIN projetos.treinamento_colaborador_tipo tct
			    ON tct.cd_treinamento_colaborador_tipo = tc.cd_treinamento_colaborador_tipo
			 WHERE tc.dt_exclusao IS NULL
			   AND tci.dt_exclusao IS NULL
			   AND tci.cd_empresa            = 9
			   AND tci.seq_dependencia       = 0
			   AND tci.cd_registro_empregado = ".intval($cd_registro_empregado)."
			   ".(trim($args['nome']) != '' ? "AND UPPER(tc.ds_nome) LIKE UPPER('%".trim($args['nome'])."%')" : "")."
			   ".(trim($args['numero']) != '' ? "AND tc.nr_numero = ".intval($args['numero']) : "")."
			   ".(trim($args['ano']) != '' ? "AND tc.nr_ano = ".intval($args['ano']) : "")."
			   ".(((trim($args['dt_inicio_ini']) != '') AND (trim($args['dt_inicio_fim']) != '')) ? " AND DATE_TRUNC('day', tc.dt_inicio) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "")."						 
               ".(((trim($args['dt_final_ini']) != '') AND (trim($args['dt_final_fim']) != '')) ? " AND DATE_TRUNC('day', tc.dt_final) BETWEEN TO_DATE('".$args['dt_final_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_final_fim']."', 'DD/MM/YYYY')" : "")."						 
               ".(trim($args['cd_treinamento_colaborador_tipo']) != '' ? "AND tc.cd_treinamento_colaborador_tipo = '".intval($args['cd_treinamento_colaborador_tipo'])."'" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_treinamento_diretoria_conselhos_item)
	{
		$qr_sql = "
			SELECT tc.cd_treinamento_diretoria_conselhos,
			       funcoes.nr_treinamento_colaborador(tc.nr_ano, tc.nr_numero) AS numero,
			       tc.nr_ano,
			       tc.ds_nome,
			       tc.ds_promotor,
			       TO_CHAR(tc.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
			       TO_CHAR(tc.dt_final, 'DD/MM/YYYY') AS dt_final,
			       tc.ds_uf,
			       tci.arquivo,
				   tci.arquivo_nome,
				   tci.cd_registro_empregado,
			       tci.cd_treinamento_diretoria_conselhos_item,
			       tc.cd_treinamento_colaborador_tipo,
			       tct.ds_treinamento_colaborador_tipo
	  		  FROM projetos.treinamento_diretoria_conselhos_item tci
	  		  JOIN projetos.treinamento_diretoria_conselhos tc
			    ON tci.cd_treinamento_diretoria_conselhos = tc.cd_treinamento_diretoria_conselhos
			  LEFT JOIN projetos.treinamento_colaborador_tipo tct
			    ON tct.cd_treinamento_colaborador_tipo = tc.cd_treinamento_colaborador_tipo
	  		 WHERE tci.cd_treinamento_diretoria_conselhos_item = ".intval($cd_treinamento_diretoria_conselhos_item).";";
	  		 
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

	public function salvar_anexo($cd_treinamento_diretoria_conselhos_item, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.treinamento_diretoria_conselhos_item
			   SET fl_certificado         = ".(trim($args['fl_certificado']) != '' ? "'".trim($args['fl_certificado'])."'" : "DEFAULT").",
			       arquivo                = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
			       arquivo_nome           = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
			       ds_justificativa       = ".(trim($args['ds_justificativa']) != '' ? "'".trim($args['ds_justificativa'])."'" : "DEFAULT").",
			       cd_usuario_certificado = ".intval($args['cd_usuario']).",
			       dt_certificado         = CURRENT_TIMESTAMP
			 WHERE cd_treinamento_diretoria_conselhos_item = ".intval($cd_treinamento_diretoria_conselhos_item).";";

		$this->db->query($qr_sql);
	}

	public function listar_documento($cd_treinamento_diretoria_conselhos_item)
	{
		$qr_sql = "
            SELECT cd_treinamento_diretoria_conselhos_documento, 
                   cd_usuario_inclusao,
                   arquivo, 
                   arquivo_nome, 
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
              FROM projetos.treinamento_diretoria_conselhos_documento 
             WHERE cd_treinamento_diretoria_conselhos_item = ".intval($cd_treinamento_diretoria_conselhos_item)."
               AND dt_exclusao                     IS NULL;";

        return $this->db->query($qr_sql)->result_array();
	}

    public function salvar_documento($cd_treinamento_diretoria_conselhos_item, $args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.treinamento_diretoria_conselhos_documento
                 (
                    cd_treinamento_diretoria_conselhos_item,
                    cd_treinamento_diretoria_conselhos,
                    arquivo, 
                    arquivo_nome, 
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_treinamento_diretoria_conselhos_item).",
                    ".intval($args['cd_treinamento_diretoria_conselhos']).",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);
    }

    public function excluir_documento($cd_treinamento_diretoria_conselhos_documento, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.treinamento_diretoria_conselhos_documento
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_treinamento_diretoria_conselhos_documento = ".intval($cd_treinamento_diretoria_conselhos_documento).";";

        $this->db->query($qr_sql);
    }

}