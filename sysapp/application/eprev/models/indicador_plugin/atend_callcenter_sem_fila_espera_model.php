<?php
class atend_callcenter_sem_fila_espera_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_atend_callcenter_sem_fila_espera,
						   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') AS ano_mes_referencia,
						   i.cd_indicador_tabela,
						   i.nr_ligacao_sem_fila,
						   i.nr_ligacao_atendida,
						   i.nr_ligacao_atendida_percentual,
						   i.nr_meta,
						   i.observacao,
						   i.fl_media,
						   CASE WHEN (SELECT MAX(i1.dt_referencia)
										FROM indicador_plugin.atend_callcenter_sem_fila_espera i1
									   WHERE (fl_media = 'S' OR i1.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")) = i.dt_referencia
								THEN 'S'
								ELSE 'N'
							END AS fl_editar
					  FROM indicador_plugin.atend_callcenter_sem_fila_espera i
					 WHERE i.dt_exclusao IS NULL
					   AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")			 
					 ORDER BY i.dt_referencia ASC;
			       ";
			 
		$result = $this->db->query($qr_sql);
		
		#echo "<PRE>$qr_sql</PRE>";
	}
	
	function referencia( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, 
						   dt_referencia,
						   nr_meta,
						   cd_indicador_tabela
					  FROM indicador_plugin.atend_callcenter_sem_fila_espera 
					 WHERE dt_exclusao IS NULL 
					 ORDER BY dt_referencia DESC 
					 LIMIT 1
			      ";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carregar(&$result, $args=array())
	{
		$qr_sql = " 
					SELECT cd_atend_callcenter_sem_fila_espera,
						   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
						   observacao,
						   nr_ligacao_sem_fila,
						   nr_ligacao_atendida,
						   nr_meta,
						   observacao
					  FROM indicador_plugin.atend_callcenter_sem_fila_espera 
					 WHERE dt_exclusao IS NULL
					   AND cd_atend_callcenter_sem_fila_espera = ".intval($args['cd_atend_callcenter_sem_fila_espera']).";
			      ";
		
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_atend_callcenter_sem_fila_espera']) == 0)
		{
			$qr_sql="
				INSERT INTO indicador_plugin.atend_callcenter_sem_fila_espera 
				     (
					   cd_indicador_tabela,
					   dt_referencia,
					   nr_ligacao_sem_fila,
					   nr_ligacao_atendida,
					   nr_meta,
					   observacao,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
				VALUES
				     (
					   ".intval($args['cd_indicador_tabela']).",
					   TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY'),
					   ".intval($args['nr_ligacao_sem_fila']).",
					   ".intval($args['nr_ligacao_atendida']).",
					   ".floatval($args['nr_meta']).",
					   ".(trim($args['observacao']) != '' ? str_escape($args['observacao']): "DEFAULT").",
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.atend_callcenter_sem_fila_espera
				   SET dt_referencia        =  TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY'),
					   nr_ligacao_sem_fila  = ".intval($args['nr_ligacao_sem_fila']).",
					   nr_ligacao_atendida  = ".intval($args['nr_ligacao_atendida']).",
					   nr_meta              = ".floatval($args['nr_meta']).",
					   observacao           = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']): "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_atend_callcenter_sem_fila_espera = ".intval($args['cd_atend_callcenter_sem_fila_espera']).";";
		}

		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE indicador_plugin.atend_callcenter_sem_fila_espera
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_atend_callcenter_sem_fila_espera = ".intval($args['cd_atend_callcenter_sem_fila_espera']).";";
			 
		$result = $this->db->query($qr_sql);
	}
}

?>