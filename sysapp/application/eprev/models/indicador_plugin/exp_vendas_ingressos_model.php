<?php
class exp_vendas_ingressos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_exp_vendas_ingressos,
				   cd_indicador_tabela,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') AS mes_referencia,
				   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
				   dt_referencia,		   
				   ds_observacao,
				   nr_ingressos,
				   nr_vendas,
				   nr_resultado,
				   nr_meta,
				   fl_media
			  FROM indicador_plugin.exp_vendas_ingressos 
		     WHERE dt_exclusao IS NULL
		       AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		     ORDER BY dt_referencia ASC;";
	
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carrega_referencia()
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela
		  	  FROM indicador_plugin.exp_vendas_ingressos
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_exp_vendas_ingressos)
	{
		$qr_sql = "
            SELECT cd_exp_vendas_ingressos,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_ingressos,
                   nr_vendas,
                   nr_resultado,
                   nr_meta,
                   ds_observacao
		      FROM indicador_plugin.exp_vendas_ingressos 
			 WHERE cd_exp_vendas_ingressos = ".intval($cd_exp_vendas_ingressos).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args=array())
	{
		if(intval($args['cd_exp_vendas_ingressos']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.exp_vendas_ingressos 
				     (
				     	cd_indicador_tabela, 
						dt_referencia, 
					    nr_ingressos, 
                        nr_vendas,
                        nr_resultado, 
					    nr_meta, 		    
					    fl_media, 
                        ds_observacao,
					    cd_usuario_inclusao,
					    cd_usuario_alteracao
			          ) 
			     VALUES 
				      ( 
				        ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
						".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')" : "DEFAULT").",
					    ".(trim($args['nr_ingressos']) != '' ? floatval($args['nr_ingressos']) : "DEFAULT").",
					    ".(trim($args['nr_vendas']) != ''? floatval($args['nr_vendas']) : "DEFAULT").",
					    ".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").",
					    ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
					    ".(trim($args['fl_media']) != '' ? str_escape($args["fl_media"]) : "DEFAULT").",
					    ".(trim($args['ds_observacao']) != '' ? str_escape($args["ds_observacao"]): "DEFAULT").",
					    ".intval($args['cd_usuario']).",
					    ".intval($args['cd_usuario'])."
                      );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.exp_vendas_ingressos
				   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')" : "DEFAULT").",
				       nr_ingressos         = ".(trim($args['nr_ingressos']) != '' ? floatval($args['nr_ingressos']): "DEFAULT").",
					   nr_vendas         = ".(trim($args['nr_vendas']) != ''? floatval($args['nr_vendas']) : "DEFAULT").",
					   nr_resultado         = ".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").",
	                   nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
					   fl_media             = ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
					   ds_observacao        = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']): "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
			     WHERE cd_exp_vendas_ingressos = ".intval($args['cd_exp_vendas_ingressos']).";";
		}

		$this->db->query($qr_sql);
	}
	
	public function excluir($cd_exp_vendas_ingressos, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.exp_vendas_ingressos
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_exp_vendas_ingressos = ".intval($cd_exp_vendas_ingressos).";"; 
			 
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