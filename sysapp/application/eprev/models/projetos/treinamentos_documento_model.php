<?php
class Treinamentos_documento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_treinamento_colaborador, $args = array())
	{
		$qr_sql = "
			SELECT DISTINCT tc.nome,
			       funcoes.nr_treinamento_colaborador(tc.ano, tc.numero) AS numero,
			       tc.promotor,
			       tc.uf,
			       tc.cidade,
			       tc.cd_treinamento_colaborador,
			       TO_CHAR(tc.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
			       TO_CHAR(tc.dt_final, 'DD/MM/YYYY') AS dt_final,
				   (SELECT COUNT(*)
					  FROM projetos.treinamento_colaborador_documento tcd
					 WHERE tcd.cd_treinamento_colaborador = tc.cd_treinamento_colaborador
					   AND tcd.dt_exclusao IS NULL) AS qt_arquivo,
			       tc.cd_treinamento_colaborador_tipo,
			       tct.ds_treinamento_colaborador_tipo
			  FROM projetos.treinamento_colaborador tc
			  JOIN projetos.treinamento_colaborador_item tci
			    ON tci.ano    = tc.ano
			   AND tci.numero = tc.numero
			  LEFT JOIN projetos.treinamento_colaborador_tipo tct
			    ON tct.cd_treinamento_colaborador_tipo = tc.cd_treinamento_colaborador_tipo
			 WHERE (SELECT COUNT(*)
				      FROM projetos.treinamento_colaborador_documento tcd
				     WHERE tcd.cd_treinamento_colaborador = tc.cd_treinamento_colaborador
			           AND tcd.dt_exclusao IS NULL) > 0
			   AND tc.dt_exclusao IS NULL
			   ".(trim($args['nome']) != '' ? "AND UPPER(tc.nome) LIKE UPPER('%".trim($args['nome'])."%')" : "")."
			   ".(trim($args['numero']) != '' ? "AND tc.numero = ".intval($args['numero']) : "")."
			   ".(trim($args['ano']) != '' ? "AND tc.ano = ".intval($args['ano']) : "")."
			   ".(((trim($args['dt_inicio_ini']) != '') AND (trim($args['dt_inicio_fim']) != '')) ? " AND DATE_TRUNC('day', tc.dt_inicio) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "")."						 
               ".(((trim($args['dt_final_ini']) != '') AND (trim($args['dt_final_fim']) != '')) ? " AND DATE_TRUNC('day', tc.dt_final) BETWEEN TO_DATE('".$args['dt_final_ini']."', 'DD/MM/YYYY') AND TO_yDATE('".$args['dt_final_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['cd_treinamento_colaborador_tipo']) != '' ? "AND tc.cd_treinamento_colaborador_tipo = '".intval($args['cd_treinamento_colaborador_tipo'])."'" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_treinamento_colaborador)
	{
		$qr_sql = "
			SELECT tc.nome,
			       funcoes.nr_treinamento_colaborador(tc.ano, tc.numero) AS numero,
			       tc.promotor,
			       tc.uf,
			       tc.cidade,
			       tc.cd_treinamento_colaborador,
			       TO_CHAR(tc.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
			       TO_CHAR(tc.dt_final, 'DD/MM/YYYY') AS dt_final,
			       tc.cd_treinamento_colaborador_tipo,
			       tct.ds_treinamento_colaborador_tipo
			  FROM projetos.treinamento_colaborador tc
			  LEFT JOIN projetos.treinamento_colaborador_tipo tct
			    ON tct.cd_treinamento_colaborador_tipo = tc.cd_treinamento_colaborador_tipo
			 WHERE tc.cd_treinamento_colaborador = ".intval($cd_treinamento_colaborador)."
			   AND tc.dt_exclusao IS NULL";		

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

	public function listar_documento($cd_treinamento_colaborador)
	{
		$qr_sql = "
            SELECT cd_treinamento_colaborador_documento, 
                   cd_usuario_inclusao,
                   arquivo, 
                   arquivo_nome, 
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
              FROM projetos.treinamento_colaborador_documento 
             WHERE cd_treinamento_colaborador = ".intval($cd_treinamento_colaborador)."
               AND dt_exclusao IS NULL;";
 
        return $this->db->query($qr_sql)->result_array();
	}
}