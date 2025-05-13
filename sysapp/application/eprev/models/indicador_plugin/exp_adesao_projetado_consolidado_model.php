<?php
class exp_adesao_projetado_consolidado_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT i.cd_exp_adesao_projetado_consolidado,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   COALESCE(i.ds_obs_origem,'') || E'\n' || COALESCE(i.ds_observacao,'') AS ds_observacao,
				   i.nr_resultado,
				   i.nr_percentual_f,
				   i.nr_meta,
				   i.nr_meta_ano,
				   i.nr_ceeeprev_meta,
				   i.nr_ceeeprev_resultado ,
				   i.nr_ceeeprev_atingido,
				   i.nr_crmprev_meta,
				   i.nr_crmprev_resultado,
				   i.nr_crmprev_atingido,
				   i.nr_inpelprev_meta,
				   i.nr_inpelprev_resultado,
				   i.nr_inpelprev_atingido,
				   i.nr_senge_meta, 
				   i.nr_senge_resultado,
				   i.nr_senge_atingido,
				   i.nr_sinpro_meta,
				   i.nr_sinpro_resultado,
				   i.nr_sinpro_atingido,
				   i.nr_familia_meta,
				   i.nr_familia_resultado,
				   i.nr_familia_atingido,
				   i.nr_fozprev_meta,
				   i.nr_fozprev_resultado,
				   i.nr_fozprev_atingido,
				   i.nr_unico_meta, 
				   i.nr_unico_resultado,
				   i.nr_unico_atingido,
				   i.nr_ceranprev_meta,
				   i.nr_ceranprev_resultado,
				   i.nr_ceranprev_atingido,
				   i.nr_municipios_meta,
				   i.nr_municipios_resultado,
				   i.nr_municipios_atingido
		      FROM indicador_plugin.exp_adesao_projetado_consolidado i
		     WHERE i.dt_exclusao IS NULL
		       AND (i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		     ORDER BY i.dt_referencia ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carrega_referencia($nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n,
				   cd_indicador_tabela,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.exp_adesao_projetado_consolidado
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.exp_adesao_projetado_consolidado
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_exp_adesao_projetado_consolidado)
	{
		$qr_sql = "
            SELECT cd_exp_adesao_projetado_consolidado,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,                   
                   fl_media,
                   ds_observacao
		      FROM indicador_plugin.exp_adesao_projetado_consolidado 
			 WHERE cd_exp_adesao_projetado_consolidado = ".intval($cd_exp_adesao_projetado_consolidado).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args=array())
	{
		if(intval($args['cd_exp_adesao_projetado_consolidado']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.exp_adesao_projetado_consolidado 
				     (
						dt_referencia, 
					    cd_indicador_tabela, 
					    fl_media,
					    ds_observacao,
                        cd_usuario_inclusao,
					    cd_usuario_alteracao
			          ) 
			     VALUES 
				      ( 
						".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
						".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
						".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
                      );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.exp_adesao_projetado_consolidado
				   SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				       cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
					   fl_media             = ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
					   ds_observacao        = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
			     WHERE cd_exp_adesao_projetado_consolidado = ".intval($args['cd_exp_adesao_projetado_consolidado']).";";
		}

		$this->db->query($qr_sql);
	}
	
	public function excluir($cd_exp_adesao_projetado_consolidado, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.exp_adesao_projetado_consolidado
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_exp_adesao_projetado_consolidado = ".intval($cd_exp_adesao_projetado_consolidado).";"; 
			 
		$this->db->query($qr_sql);
	}

	public function fechamento($args = array())
	{
		if(intval($args['cd_exp_adesao_projetado_consolidado']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.exp_adesao_projetado_consolidado 
				     (
						dt_referencia, 
					    cd_indicador_tabela, 
					    fl_media,
					    nr_resultado,
				   		nr_meta,
				    	nr_percentual_f, 
                        ds_observacao,
                        ds_obs_origem,
                        nr_ceeeprev_meta,
					    nr_ceeeprev_resultado,
					    nr_ceeeprev_atingido,
					    nr_crmprev_meta,
					    nr_crmprev_resultado,
					    nr_crmprev_atingido,
					    nr_inpelprev_meta,
					    nr_inpelprev_resultado,
					    nr_inpelprev_atingido,
					    nr_senge_meta,
					    nr_senge_resultado,  
					    nr_senge_atingido,
					    nr_sinpro_meta,
					    nr_sinpro_resultado,
					    nr_sinpro_atingido,
					    nr_familia_meta,
					    nr_familia_resultado,
					    nr_familia_atingido,
					    nr_fozprev_meta,
					    nr_fozprev_resultado,
					    nr_fozprev_atingido,
					    nr_unico_meta,
					    nr_unico_resultado,
					    nr_unico_atingido,
					    nr_ceranprev_meta,
					    nr_ceranprev_resultado,
					    nr_ceranprev_atingido,
					    nr_municipios_meta,
						nr_municipios_resultado,
						nr_municipios_atingido,
                        cd_usuario_inclusao,
					    cd_usuario_alteracao
			          ) 
			     VALUES 
				      ( 
						".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
						".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
						".(trim($args['nr_resultado']) > 0 ? intval($args['nr_resultado']) : "DEFAULT").",
					    ".(trim($args['nr_meta']) > 0 ? intval($args['nr_meta']) : "DEFAULT").",					    
					    ".(trim($args['nr_percentual_f']) > 0 ? floatval($args['nr_percentual_f']) : "DEFAULT").",
						".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
						".(trim($args['ds_obs_origem']) != '' ? str_escape($args['ds_obs_origem']) : "DEFAULT").",
						".(trim($args['nr_ceeeprev_meta']) > 0 ? intval($args['nr_ceeeprev_meta']) : "DEFAULT").",
						".(trim($args['nr_ceeeprev_resultado']) > 0 ? intval($args['nr_ceeeprev_resultado']) : "DEFAULT").",
						".(trim($args['nr_ceeeprev_atingido']) > 0 ? floatval($args['nr_ceeeprev_atingido']) : "DEFAULT").",
						".(trim($args['nr_crmprev_meta']) > 0 ? intval($args['nr_crmprev_meta']) : "DEFAULT").",
						".(trim($args['nr_crmprev_resultado']) > 0 ? intval($args['nr_crmprev_resultado']) : "DEFAULT").",
						".(trim($args['nr_crmprev_atingido']) > 0 ? floatval($args['nr_crmprev_atingido']) : "DEFAULT").",
						".(trim($args['nr_inpelprev_meta']) > 0 ? intval($args['nr_inpelprev_meta']) : "DEFAULT").",
						".(trim($args['nr_inpelprev_resultado']) > 0 ? intval($args['nr_inpelprev_resultado']) : "DEFAULT").",
						".(trim($args['nr_inpelprev_atingido']) > 0 ? floatval($args['nr_inpelprev_atingido']) : "DEFAULT").",
						".(trim($args['nr_senge_meta']) > 0 ? intval($args['nr_senge_meta']) : "DEFAULT").",
						".(trim($args['nr_senge_resultado']) > 0 ? intval($args['nr_senge_resultado']) : "DEFAULT").",
						".(trim($args['nr_senge_atingido']) > 0 ? floatval($args['nr_senge_atingido']) : "DEFAULT").",
						".(trim($args['nr_sinpro_meta']) > 0 ? intval($args['nr_sinpro_meta']) : "DEFAULT").",
						".(trim($args['nr_sinpro_resultado']) > 0 ? intval($args['nr_sinpro_resultado']) : "DEFAULT").",
						".(trim($args['nr_sinpro_atingido']) > 0 ? floatval($args['nr_sinpro_atingido']) : "DEFAULT").",
						".(trim($args['nr_familia_meta']) > 0 ? intval($args['nr_familia_meta']) : "DEFAULT").",
						".(trim($args['nr_familia_resultado']) > 0 ? intval($args['nr_familia_resultado']) : "DEFAULT").",
						".(trim($args['nr_familia_atingido']) > 0 ? floatval($args['nr_familia_atingido']) : "DEFAULT").",
						".(trim($args['nr_fozprev_meta']) > 0 ? intval($args['nr_fozprev_meta']) : "DEFAULT").",
						".(trim($args['nr_fozprev_resultado']) > 0 ? intval($args['nr_fozprev_resultado']) : "DEFAULT").",
						".(trim($args['nr_fozprev_atingido']) > 0 ? floatval($args['nr_fozprev_atingido']) : "DEFAULT").",
						".(trim($args['nr_unico_meta']) > 0 ? intval($args['nr_unico_meta']) : "DEFAULT").",
						".(trim($args['nr_unico_resultado']) > 0 ? intval($args['nr_unico_resultado']) : "DEFAULT").",
						".(trim($args['nr_unico_atingido']) > 0 ? floatval($args['nr_unico_atingido']) : "DEFAULT").",
						".(trim($args['nr_ceranprev_meta']) > 0 ? intval($args['nr_ceranprev_meta']) : "DEFAULT").",
						".(trim($args['nr_ceranprev_resultado']) > 0 ? intval($args['nr_ceranprev_resultado']) : "DEFAULT").",
						".(trim($args['nr_ceranprev_atingido']) > 0 ? floatval($args['nr_ceranprev_atingido']) : "DEFAULT").",
						".(trim($args['nr_municipios_meta']) > 0 ? intval($args['nr_municipios_meta']) : "DEFAULT").",
						".(trim($args['nr_municipios_resultado']) > 0 ? intval($args['nr_municipios_resultado']) : "DEFAULT").",
						".(trim($args['nr_municipios_atingido']) > 0 ? floatval($args['nr_municipios_atingido']) : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
                      );";
		}

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