<?php
class Atuarial_aessul_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_atuarial_aessul,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
				   dt_referencia,
				   cd_indicador_tabela,
				   fl_media,
				   observacao,
				   nr_valor_1,
				   nr_valor_2,
				   nr_percentual_f,
				   nr_meta
			  FROM indicador_plugin.atuarial_aessul   
		     WHERE dt_exclusao IS NULL
	           AND (fl_media ='S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC";
		
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carrega_referencia($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela
			  FROM indicador_plugin.atuarial_aessul 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_atuarial_aessul)
	{
		$qr_sql = "
            SELECT cd_atuarial_aessul,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_percentual_f,
                   nr_meta,
                   observacao
		      FROM indicador_plugin.atuarial_aessul 
			 WHERE cd_atuarial_aessul = ".intval($cd_atuarial_aessul).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		if(intval($args['cd_atuarial_aessul']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.atuarial_aessul 
				     (
						dt_referencia, 
					    nr_valor_1, 
                        nr_valor_2, 
					    nr_meta, 
					    cd_indicador_tabela, 
					    fl_media, 
					    nr_percentual_f,
                        observacao,
					    cd_usuario_inclusao,
					    cd_usuario_alteracao
			          ) 
			     VALUES 
				      ( 
						".(trim($args['dt_referencia']) != "" ? "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')" : "DEFAULT").",
					    ".(trim($args['nr_valor_1']) != "" ? floatval($args['nr_valor_1']) : "DEFAULT").",
					    ".(trim($args['nr_valor_2']) != "" ? floatval($args['nr_valor_2']) : "DEFAULT").",
					    ".(trim($args['nr_meta']) != "" ? floatval($args['nr_meta']) : "DEFAULT").",
					    ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
					    ".(trim($args['fl_media']) != "" ? "'".trim($args["fl_media"])."'" : "DEFAULT").",
					    ".(trim($args['nr_percentual_f']) != "" ? floatval($args['nr_percentual_f']) : "DEFAULT").",
					    ".(trim($args['observacao']) != "" ? "'".trim($args["observacao"])."'" : "DEFAULT").",
					    ".intval($args['cd_usuario']).",
					    ".intval($args['cd_usuario'])."
                      );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.atuarial_aessul
				   SET dt_referencia        = ".(trim($args['dt_referencia']) != "" ? "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')" : "DEFAULT").",
				       nr_valor_1           = ".(trim($args['nr_valor_1']) != "" ? floatval($args['nr_valor_1']) : "DEFAULT").",
					   nr_valor_2           = ".(trim($args['nr_valor_2']) != "" ? floatval($args['nr_valor_2']) : "DEFAULT").",
	                   nr_meta              = ".(trim($args['nr_meta']) != "" ? floatval($args['nr_meta']) : "DEFAULT").",
	                   nr_percentual_f      = ".(trim($args['nr_percentual_f']) != "" ? floatval($args['nr_percentual_f']) : "DEFAULT").",
					   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
					   fl_media             = ".(trim($args['fl_media']) != "" ? "'".trim($args["fl_media"])."'" : "DEFAULT").",
					   observacao           = ".(trim($args['observacao']) != "" ? "'".trim($args["observacao"])."'" : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
			     WHERE cd_atuarial_aessul = ".intval($args['cd_atuarial_aessul']).";";
		}

		$this->db->query($qr_sql);
	}
	
	public function excluir($cd_atuarial_aessul, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.atuarial_aessul
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_atuarial_aessul = ".intval($cd_atuarial_aessul).";"; 
			 
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
?>