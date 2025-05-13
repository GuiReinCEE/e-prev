<?php
class exp_adesao_projetado_familia_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT i.cd_exp_adesao_projetado_familia,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
				   TO_CHAR(i.dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
				   i.nr_valor_1,
				   i.nr_percentual_f,
				   i.nr_meta
		      FROM indicador_plugin.exp_adesao_projetado_familia i
		     WHERE i.dt_exclusao IS NULL
		       AND(i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		     ORDER BY i.dt_referencia ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carrega_referencia()
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n,
				   nr_valor_1, 
				   cd_indicador_tabela 
			  FROM indicador_plugin.exp_adesao_projetado_familia
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_exp_adesao_projetado_familia)
	{
		$qr_sql = "
            SELECT cd_exp_adesao_projetado_familia,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_percentual_f,
                   nr_meta,
                   observacao
		      FROM indicador_plugin.exp_adesao_projetado_familia 
			 WHERE cd_exp_adesao_projetado_familia = ".intval($cd_exp_adesao_projetado_familia).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args=array())
	{
		if(intval($args['cd_exp_adesao_projetado_familia']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.exp_adesao_projetado_familia 
				     (
						dt_referencia, 
					    nr_valor_1, 
                        nr_meta, 
					    cd_indicador_tabela, 
					    fl_media, 
                        observacao,
                        nr_percentual_f,
					    cd_usuario_inclusao,
					    cd_usuario_alteracao
			          ) 
			     VALUES 
				      ( 
						".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['nr_valor_1']) != '' ? floatval($args['nr_valor_1']) : "DEFAULT").",
						".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
						".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
						".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
						".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
						".(trim($args['nr_percentual_f']) != "" ? floatval($args['nr_percentual_f']) : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
                      );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.exp_adesao_projetado_familia
				   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				       nr_valor_1           = ".(trim($args['nr_valor_1']) != '' ? floatval($args['nr_valor_1']) : "DEFAULT").",
					   nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
					   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
					   fl_media             = ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
					   observacao           = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
					   nr_percentual_f      = ".(trim($args['nr_percentual_f']) != '' ? floatval($args['nr_percentual_f']) : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
			     WHERE cd_exp_adesao_projetado_familia = ".intval($args['cd_exp_adesao_projetado_familia']).";";
		}

		$this->db->query($qr_sql);
	}
	
	public function excluir($cd_exp_adesao_projetado_familia, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.exp_adesao_projetado_familia
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_exp_adesao_projetado_familia = ".intval($cd_exp_adesao_projetado_familia).";"; 
			 
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