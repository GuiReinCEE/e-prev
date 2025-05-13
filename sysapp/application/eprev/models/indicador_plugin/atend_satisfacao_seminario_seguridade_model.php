<?php
class atend_satisfacao_seminario_seguridade_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT i.cd_atend_satisfacao_seminario_seguridade,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   i.cd_indicador_tabela,
				   i.nr_participante,
				   i.nr_satisfeito,
				   i.nr_avaliacao,
				   i.nr_satisfacao_percentual,
				   i.observacao,
				   CASE WHEN (SELECT MAX(i1.dt_referencia)
						        FROM indicador_plugin.atend_satisfacao_seminario_seguridade i1
					           WHERE i1.dt_exclusao IS NULL) = i.dt_referencia
						THEN 'S'
						ELSE 'N'
					END AS fl_editar
			  FROM indicador_plugin.atend_satisfacao_seminario_seguridade i
			 WHERE i.dt_exclusao IS NULL
			 ORDER BY i.dt_referencia ASC;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carregar(&$result, $args=array())
	{
		$qr_sql = " 
			SELECT cd_atend_satisfacao_seminario_seguridade,
		           TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
                   observacao,
		           nr_participante,
		           nr_satisfeito,
                   nr_avaliacao,
		           nr_satisfacao_percentual
		      FROM indicador_plugin.atend_satisfacao_seminario_seguridade 
			 WHERE dt_exclusao IS NULL
			   AND cd_atend_satisfacao_seminario_seguridade = ".intval($args['cd_atend_satisfacao_seminario_seguridade']).";";
		
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_atend_satisfacao_seminario_seguridade']) == 0)
		{
			$qr_sql="
				INSERT INTO indicador_plugin.atend_satisfacao_seminario_seguridade 
				     (
					   cd_indicador_tabela,
					   dt_referencia,
					   nr_participante,
					   nr_satisfeito,
                       nr_avaliacao,
					   observacao,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
				VALUES
				     (
					   ".intval($args['cd_indicador_tabela']).",
					   TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY'),
					   ".intval($args['nr_participante']).",
					   ".intval($args['nr_satisfeito']).",
					   ".intval($args['nr_avaliacao']).",
					   ".(trim($args['observacao']) != '' ? str_escape($args['observacao']): "DEFAULT").",
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.atend_satisfacao_seminario_seguridade
				   SET dt_referencia        =  TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY'),
					   nr_participante      = ".intval($args['nr_participante']).",
					   nr_satisfeito        = ".intval($args['nr_satisfeito']).",
                       nr_avaliacao         = ".intval($args['nr_avaliacao']).",
					   observacao           = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']): "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_atend_satisfacao_seminario_seguridade = ".intval($args['cd_atend_satisfacao_seminario_seguridade']).";";
		}

		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE indicador_plugin.atend_satisfacao_seminario_seguridade
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_atend_satisfacao_seminario_seguridade = ".intval($args['cd_atend_satisfacao_seminario_seguridade']).";";
			 
		$result = $this->db->query($qr_sql);
	}
}

?>