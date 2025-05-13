<?php
class Administrativo_resul_superior_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_administrativo_resul_superior,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,
				   nr_valor_1,
				   nr_valor_2,
			       nr_valor_3,
			       nr_valor_4,
			       nr_valor_5,
			       nr_valor_6,
				   nr_percentual_f,
				   nr_meta,
				   fl_media,
				   observacao
			  FROM indicador_plugin.administrativo_resul_superior
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
			  FROM indicador_plugin.administrativo_resul_superior 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carregar($cd_administrativo_resul_superior)
	{
		$qr_sql = "
            SELECT cd_administrativo_resul_superior,
                   cd_indicador_tabela,
                   TO_CHAR(dt_referencia,'MM') AS mes_referencia,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   nr_valor_1,
                   nr_valor_2,
                   nr_valor_3,
                   nr_valor_4,
                   nr_valor_5,
                   nr_valor_6,
                   nr_percentual_f,
                   nr_meta,
                   fl_media,
                   observacao
		      FROM indicador_plugin.administrativo_resul_superior 
		     WHERE cd_administrativo_resul_superior = ".intval($cd_administrativo_resul_superior).";";
			
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.administrativo_resul_superior 
				(
					cd_indicador_tabela,
					dt_referencia, 
					nr_valor_1,
					nr_valor_2,
		            nr_valor_3,
		            nr_valor_4,
		            nr_valor_5,
		            nr_valor_6,
					nr_meta, 
					nr_percentual_f,
					fl_media,
		            observacao,
		            cd_usuario_inclusao,
					cd_usuario_alteracao
				) 
		   VALUES 
				( 
				   ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(trim($args['nr_valor_1']) != '' ? intval($args['nr_valor_1']) : "DEFAULT").",
				   ".(trim($args['nr_valor_2']) != '' ? intval($args['nr_valor_2']) : "DEFAULT").",
				   ".(trim($args['nr_valor_3']) != '' ? intval($args['nr_valor_3']) : "DEFAULT").",
				   ".(trim($args['nr_valor_4']) != '' ? intval($args['nr_valor_4']) : "DEFAULT").",
				   ".(trim($args['nr_valor_5']) != '' ? intval($args['nr_valor_5']) : "DEFAULT").",
				   ".(trim($args['nr_valor_6']) != '' ? intval($args['nr_valor_6']) : "DEFAULT").",
				   ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   ".(trim($args['nr_percentual_f']) != '' ? intval($args['nr_percentual_f']) : "DEFAULT").",
				   ".(trim($args['fl_media']) != '' ?  str_escape(trim($args["fl_media"])) : "DEFAULT").",
				   ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				)";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_administrativo_resul_superior, $args=array())
	{
		$qr_sql = "
			UPDATE indicador_plugin.administrativo_resul_superior
			   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   nr_valor_1           = ".(trim($args['nr_valor_1']) != '' ? intval($args['nr_valor_1']): "DEFAULT").",
				   nr_valor_2           = ".(trim($args['nr_valor_2']) != '' ? intval($args['nr_valor_2']): "DEFAULT").",
	               nr_valor_3           = ".(trim($args['nr_valor_3']) != ''? intval($args['nr_valor_3']) : "DEFAULT").",
	               nr_valor_4           = ".(trim($args['nr_valor_4']) != ''? floatval($args['nr_valor_4']) : "DEFAULT").",
	               nr_valor_5           = ".(trim($args['nr_valor_5']) != ''? floatval($args['nr_valor_5']) : "DEFAULT").",
	               nr_valor_6           = ".(trim($args['nr_valor_6']) != ''? floatval($args['nr_valor_6']) : "DEFAULT").",
				   nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				   nr_percentual_f      = ".(trim($args['nr_percentual_f']) != '' ? intval($args['nr_percentual_f']) : "DEFAULT").",
	               observacao           = ".(trim($args['observacao']) != '' ?  str_escape(trim($args["observacao"])) : "DEFAULT").",
	               cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_administrativo_resul_superior = ".intval($cd_administrativo_resul_superior).";";

		$this->db->query($qr_sql);
	}

	public function excluir($cd_administrativo_resul_superior, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.administrativo_resul_superior
		   	   SET dt_exclusao         = CURRENT_TIMESTAMP,
		           cd_usuario_exclusao = ".intval($cd_usuario)." 
		     WHERE cd_administrativo_resul_superior = ".intval($cd_administrativo_resul_superior).";";
		
		return $this->db->query($qr_sql); 
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