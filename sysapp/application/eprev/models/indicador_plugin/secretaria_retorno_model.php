<?php
class Secretaria_retorno_model extends Model
{
	function __construct()
	{
		parent::Model();

	}
	
	public  function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_secretaria_retorno,
			       cd_indicador_tabela,
				   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   nr_valor_1,
				   nr_valor_2,
				   nr_sumula_disp_24h,
				   nr_percent_disp_24h,
				   nr_percentual_f,
				   nr_meta,
				   fl_media,
				   observacao
		      FROM indicador_plugin.secretaria_retorno
		     WHERE dt_exclusao IS NULL
		       AND(fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		     ORDER BY dt_referencia ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carrega_referencia()
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela 
			  FROM indicador_plugin.secretaria_retorno
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_secretaria_retorno)
	{
		$qr_sql = "
            SELECT cd_secretaria_retorno,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_percentual_f,
                   nr_sumula_disp_24h,
                   nr_percent_disp_24h,
				   nr_meta,
                   observacao
		      FROM indicador_plugin.secretaria_retorno 
			 WHERE cd_secretaria_retorno = ".intval($cd_secretaria_retorno).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		if(intval($args['cd_secretaria_retorno']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.secretaria_retorno 
				     (
				        cd_indicador_tabela, 
						dt_referencia, 
					    nr_valor_1, 
                        nr_valor_2, 
					    nr_meta, 
					    fl_media,
					    nr_sumula_disp_24h,
					    nr_percentual_f,
					    nr_percent_disp_24h,
                        observacao,
					    cd_usuario_inclusao
			          ) 
			     VALUES 
				      ( 
				        ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
						".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					    ".(trim($args['nr_valor_1']) != '' ? floatval($args['nr_valor_1']) : "DEFAULT").",
					    ".(trim($args['nr_valor_2']) != '' ? floatval($args['nr_valor_2']) : "DEFAULT" ).",
					    ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
					    ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
					    ".(trim($args['nr_sumula_disp_24h']) != '' ? floatval($args['nr_sumula_disp_24h']) : "DEFAULT").",
					    ".(trim($args['nr_percentual_f']) != '' ? floatval($args['nr_percentual_f']) : "DEFAULT").",
					    ".(trim($args['nr_percent_disp_24h']) != '' ? floatval($args['nr_percent_disp_24h']) : "DEFAULT").",
					    ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
					    ".intval($args['cd_usuario'])."
                      );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.secretaria_retorno
				   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				       nr_valor_1           = ".(trim($args['nr_valor_1']) != '' ? floatval($args['nr_valor_1']) : "DEFAULT").",
					   nr_valor_2           = ".(trim($args['nr_valor_2']) != '' ? floatval($args['nr_valor_2']) : "DEFAULT" ).",
	                   nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
					   nr_sumula_disp_24h   = ".(trim($args['nr_sumula_disp_24h']) != '' ? floatval($args['nr_sumula_disp_24h']) : "DEFAULT").",
					   nr_percentual_f      = ".(trim($args['nr_percentual_f']) != '' ? floatval($args['nr_percentual_f']) : "DEFAULT").",
					   nr_percent_disp_24h  = ".(trim($args['nr_percent_disp_24h']) != '' ? floatval($args['nr_percent_disp_24h']) : "DEFAULT").",
                       observacao           = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT")."
				WHERE cd_secretaria_retorno = ".intval($args['cd_secretaria_retorno']).";";
		}

		$this->db->query($qr_sql);
	}
	
	public function excluir($cd_secretaria_retorno, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.secretaria_retorno
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_secretaria_retorno = ".intval($cd_secretaria_retorno).";"; 
			 
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