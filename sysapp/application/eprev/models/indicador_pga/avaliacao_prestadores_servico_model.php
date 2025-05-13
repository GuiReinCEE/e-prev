<?php
class Avaliacao_prestadores_servico_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_avaliacao_prestadores_servico,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   nr_media,
                   nr_meta,
				   fl_media,
				   ds_observacao
			  FROM indicador_pga.avaliacao_prestadores_servico
			 WHERE dt_exclusao IS NULL
			   AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n,
			       TO_CHAR(dt_referencia + '1 month'::interval,'MM') AS mes_referencia,
			       TO_CHAR(dt_referencia + '1 month'::interval,'YYYY') AS ano_referencia,
				   nr_meta, 
				   cd_indicador_tabela 
			  FROM indicador_pga.avaliacao_prestadores_servico 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_avaliacao_prestadores_servico,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   nr_media,
                   nr_meta,
				   fl_media,
				   ds_observacao
			  FROM indicador_pga.avaliacao_prestadores_servico
			 WHERE cd_avaliacao_prestadores_servico = ".intval($cd_indicador_tabela).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}	

	public function get_valores($ano, $mes)
	{
		$qr_sql = "
			SELECT nr_percentual_f AS nr_media,
                   nr_meta
			  FROM indicador_plugin.administrativo_contratacao_servico
			 WHERE dt_exclusao IS NULL
			   AND fl_media != 'S'
               AND TO_CHAR(dt_referencia, 'YYYY') = '".$ano."';";
			   
		return $this->db->query($qr_sql)->row_array();
	}	

	public function salvar($args = array())
	{
		if(intval($args['cd_avaliacao_prestadores_servico']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_pga.avaliacao_prestadores_servico
				     (
				       cd_indicador_tabela,
                       dt_referencia, 
                       nr_media,
					   nr_meta, 
					   fl_media, 
                       ds_observacao,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
                VALUES 
				     (
					   ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
					   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					   ".(trim($args['nr_media']) != '' ? floatval($args['nr_media']) : "DEFAULT").",
					   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
					   ".(trim($args['fl_media']) != '' ?  str_escape($args['fl_media']) : "DEFAULT").",
					   ".(trim($args['ds_observacao']) != '' ?  str_escape($args['ds_observacao']) : "DEFAULT").",
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_pga.avaliacao_prestadores_servico
				   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                       nr_media             = ".(trim($args['nr_media']) != '' ? floatval($args['nr_media']) : "DEFAULT").",
					   nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
					   ds_observacao        = ".(trim($args['ds_observacao']) != '' ?  str_escape($args['ds_observacao']) : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_avaliacao_prestadores_servico = ".intval($args['cd_avaliacao_prestadores_servico']).";";
		}
	
		$this->db->query($qr_sql);		
	}

	public function excluir($avaliacao_prestadores_servico, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_pga.avaliacao_prestadores_servico 
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			   	   cd_usuario_exclusao = ".intval($cd_usuario)." 
			 WHERE cd_avaliacao_prestadores_servico = ".intval($avaliacao_prestadores_servico).";"; 
		
		$this->db->query($qr_sql);
	}

	public function fechar_periodo($cd_indicador_tabela, $cd_usuario)
	{
		$qr_sql = "
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($cd_usuario)." 
		     WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela).";";

		$this->db->query($qr_sql);
	}


}