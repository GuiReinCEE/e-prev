<?php
class Info_backup_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT i.cd_info_backup,
				   TO_CHAR(i.dt_referencia,'YYYY') as ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') as mes_referencia,
				   i.dt_referencia,
				   i.nr_soma,
				   i.nr_processo,
				   i.nr_percentual,
				   i.nr_meta,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
				   i.fl_meta,
				   i.fl_direcao,
				   (SELECT i1.tp_analise
					  FROM indicador.indicador_tabela it
					  JOIN indicador.indicador i1
						ON i1.cd_indicador = it.cd_indicador
					 WHERE it.cd_indicador_tabela = i.cd_indicador_tabela) AS tp_analise						   
			  FROM indicador_plugin.info_backup i
			 WHERE i.dt_exclusao IS NULL
			   AND (i.fl_media='S' OR i.cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY i.dt_referencia ASC;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS mes_referencia, 
				   dt_referencia, 
				   nr_meta, 
				   cd_indicador_tabela
			  FROM indicador_plugin.info_backup 
			 WHERE dt_exclusao IS NULL
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_info_backup)
	{
		$qr_sql = " 
			SELECT cd_info_backup,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   cd_indicador_tabela,
				   nr_soma,
				   nr_processo,
				   nr_percentual,
				   nr_meta,
				   observacao
			  FROM indicador_plugin.info_backup
			 WHERE cd_info_backup = ".$cd_info_backup.";";
		
		return $this->db->query($qr_sql)->row_array();
	}	

	public function salvar($args)
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.info_backup 
				 ( 
				   dt_referencia, 
				   cd_indicador_tabela,
				   fl_media,
				   nr_soma, 
				   nr_processo, 
				   nr_percentual,
				   nr_meta,
				   observacao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 ) 
			VALUES 
				 ( 
   				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args['fl_media'])) : "DEFAULT").",
				   ".(trim($args['nr_soma']) != '' ? floatval($args['nr_soma']) : "DEFAULT").",
				   ".(trim($args['nr_processo']) != '' ? intval($args['nr_processo']) : "DEFAULT").",
				   ".(trim($args['nr_percentual']) != '' ? floatval($args['nr_percentual']) : "DEFAULT").",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args['observacao'])) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";
		
		$this->db->query($qr_sql);	
	}	
	
	public function atualizar($cd_info_backup, $args)
	{
		$qr_sql = "
			UPDATE indicador_plugin.info_backup 
			   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   fl_media             = ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
			       nr_soma              = ".(trim($args['nr_soma']) != '' ? floatval($args['nr_soma']) : "DEFAULT").",
			       nr_processo          = ".(trim($args['nr_processo']) != '' ? intval($args['nr_processo']) : "DEFAULT").",
			       nr_percentual        = ".(trim($args['nr_percentual']) != '' ? floatval($args['nr_percentual']) : "DEFAULT").",
				   nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   observacao           = ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_info_backup = ".$cd_info_backup.";";
	
		$this->db->query($qr_sql);	
	}
  
	public function excluir($cd_info_backup, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.info_backup 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_info_backup = ".intval($cd_info_backup).";"; 

		$this->db->query($qr_sql);
	}

	public function fechar_indicador($cd_indicador_tabela, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo 	     = CURRENT_TIMESTAMP,
				   cd_usuario_fechamento_periodo = ".intval($cd_usuario)."
			 WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela).";";

		$this->db->query($qr_sql);
	}	
}
?>