<?php
class atend_avaliacao_exp_atendimento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public  function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT c1.cd_atend_avaliacao_exp_atendimento,
			       c1.cd_indicador_tabela,
				   TO_CHAR(c1.dt_referencia, 'YYYY') AS ano_referencia,
				   TO_CHAR(c1.dt_referencia, 'MM') AS mes_referencia,
				   TO_CHAR(c1.dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
				   c1.dt_referencia,
				   c1.nr_total,
				   c1.nr_avaliacao,
				   c1.nr_resultado,
				   c1.nr_meta,
				   c1.fl_media,
				   c1.ds_observacao
		      FROM indicador_plugin.atend_avaliacao_exp_atendimento c1
		     WHERE c1.dt_exclusao IS NULL
		       AND (c1.fl_media = 'S' OR c1.cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		     ORDER BY c1.dt_referencia ASC;";

		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carrega_referencia($nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
			       nr_meta,
				   cd_indicador_tabela,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.atend_avaliacao_exp_atendimento
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.atend_avaliacao_exp_atendimento
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_atend_avaliacao_exp_atendimento)
	{
		$qr_sql = "
            SELECT cd_atend_avaliacao_exp_atendimento,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_total,
                   nr_avaliacao,
				   nr_resultado,
				   nr_meta,
                   ds_observacao,
                   1 AS qt_ano
		      FROM indicador_plugin.atend_avaliacao_exp_atendimento 
			 WHERE cd_atend_avaliacao_exp_atendimento = ".intval($cd_atend_avaliacao_exp_atendimento).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		if(intval($args['cd_atend_avaliacao_exp_atendimento']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.atend_avaliacao_exp_atendimento 
				     (
				        cd_indicador_tabela, 
						dt_referencia, 
					    fl_media,
						nr_total,
					    nr_avaliacao,
					    nr_resultado,
					    nr_meta,
                        ds_observacao,
					    cd_usuario_inclusao,
					    cd_usuario_alteracao
			          ) 
			     VALUES 
				      ( 
				        ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
						".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					    ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
					    ".(trim($args['nr_total']) != '' ? floatval($args['nr_total']) : "DEFAULT").",
					    ".(trim($args['nr_avaliacao']) != '' ? floatval($args['nr_avaliacao']) : "DEFAULT").",
					    ".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").",
					    ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
					    ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					    ".intval($args['cd_usuario']).",
					    ".intval($args['cd_usuario'])."
                      );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.atend_avaliacao_exp_atendimento
				   SET dt_referencia          = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				       nr_total      = ".(trim($args['nr_total']) != '' ? floatval($args['nr_total']) : "DEFAULT").",
				       nr_avaliacao = ".(trim($args['nr_avaliacao']) != '' ? floatval($args['nr_avaliacao']) : "DEFAULT").",
				       nr_resultado = ".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").",
				       nr_meta                = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
                       ds_observacao          = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					   cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
					   dt_alteracao           = CURRENT_TIMESTAMP
			     WHERE cd_atend_avaliacao_exp_atendimento = ".intval($args['cd_atend_avaliacao_exp_atendimento']).";";
		}

		$this->db->query($qr_sql);
	}
	
	public function excluir($cd_atend_avaliacao_exp_atendimento, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.atend_avaliacao_exp_atendimento
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_atend_avaliacao_exp_atendimento = ".intval($cd_atend_avaliacao_exp_atendimento).";"; 
			 
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