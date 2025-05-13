<?php
class atuarial_eap_consolidado_bd_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT i.cd_atuarial_eap_consolidado_bd,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
				   i.obs_origem,
				   i.nr_resultado,				   
				   i.nr_meta
		      FROM indicador_plugin.atuarial_eap_consolidado_bd i
		      WHERE dt_exclusao IS NULL
	           AND ((fl_media ='S' AND cd_indicador_tabela <= ".intval($cd_indicador_tabela).") OR cd_indicador_tabela = ".intval($cd_indicador_tabela).") 
			 ORDER BY dt_referencia ASC";
		
		return $this->db->query($qr_sql)->result_array();
	}


	public function get_atuarial_ceeeprev_migrados($dt_referencia_db, $fl_media)
	{
		$qr_sql = "
			SELECT nr_valor_1 AS vl_ceeeprev_patrimonio,
	               nr_valor_2  AS vl_ceeeprev_provisao,
	               nr_meta AS vl_ceeeprev_meta
			  FROM indicador_plugin.atuarial_ceeeprev_migrados
		     WHERE dt_exclusao   IS NULL
			   AND fl_media      = '".trim($fl_media)."'
			   AND dt_referencia = '".trim($dt_referencia_db)."';";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_controladoria_eabd($dt_referencia_db, $fl_media)
	{
		$qr_sql = "
			SELECT nr_valor_1 AS vl_ceee_patrimonio,
	               nr_valor_2  AS vl_ceee_provisao,
	               nr_meta AS vl_ceee_meta
			  FROM indicador_plugin.controladoria_eabd
		     WHERE dt_exclusao   IS NULL
			   AND fl_media      = '".trim($fl_media)."'
			   AND dt_referencia = '".trim($dt_referencia_db)."';";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_atuarial_aessul($dt_referencia_db, $fl_media)
	{
		$qr_sql = "
			SELECT nr_valor_1 AS vl_aessul_patrimonio,
	               nr_valor_2  AS vl_aessul_provisao,
	               nr_meta AS vl_aessul_meta
			  FROM indicador_plugin.atuarial_aessul
		     WHERE dt_exclusao   IS NULL
			   AND fl_media      = '".trim($fl_media)."'
			   AND dt_referencia = '".trim($dt_referencia_db)."';";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_atuarial_cgtee($dt_referencia_db, $fl_media)
	{
		$qr_sql = "
			SELECT nr_valor_1 AS vl_cgtee_patrimonio,
	               nr_valor_2  AS vl_cgtee_provisao,
	               nr_meta AS vl_cgtee_meta
			  FROM indicador_plugin.atuarial_cgtee
		     WHERE dt_exclusao   IS NULL
			   AND fl_media      = '".trim($fl_media)."'
			   AND dt_referencia = '".trim($dt_referencia_db)."';";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_atuarial_rge($dt_referencia_db, $fl_media)
	{
		$qr_sql = "
			SELECT nr_valor_1 AS vl_rge_patrimonio,
	               nr_valor_2  AS vl_rge_provisao,
	               nr_meta AS vl_rge_meta
			  FROM indicador_plugin.atuarial_rge
		     WHERE dt_exclusao   IS NULL
			   AND fl_media      = '".trim($fl_media)."'
			   AND dt_referencia = '".trim($dt_referencia_db)."';";

		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega_referencia($nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.atuarial_eap_consolidado_bd
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.atuarial_eap_consolidado_bd 
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function carrega($cd_atuarial_eap_consolidado_bd)
	{
		$qr_sql = "
            SELECT cd_atuarial_eap_consolidado_bd,
                   TO_CHAR(dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_resultado,
                   nr_meta,
                   observacao,
                   1 AS qt_ano
		      FROM indicador_plugin.atuarial_eap_consolidado_bd 
			 WHERE cd_atuarial_eap_consolidado_bd = ".intval($cd_atuarial_eap_consolidado_bd).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args=array())
	{
		if(intval($args['cd_atuarial_eap_consolidado_bd']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.atuarial_eap_consolidado_bd 
				     (
						dt_referencia, 
					    nr_meta, 
					    nr_resultado,

						vl_ceeeprev_patrimonio,
						vl_ceeeprev_provisao,
						vl_ceeeprev_meta,

						vl_ceee_patrimonio,
						vl_ceee_provisao,
						vl_ceee_meta,

						vl_aessul_patrimonio,
						vl_aessul_provisao,
						vl_aessul_meta,

						vl_cgtee_patrimonio,
						vl_cgtee_provisao,
						vl_cgtee_meta,

						vl_rge_patrimonio,
						vl_rge_provisao,
						vl_rge_meta,

						obs_origem,
					    cd_indicador_tabela, 
					    fl_media, 
					    observacao,
					    cd_usuario_inclusao,
					    cd_usuario_alteracao
			          ) 
			     VALUES 
				      ( 
						".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')" : "DEFAULT").",
					    ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
					    ".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").",

					    ".(trim($args['vl_ceeeprev_patrimonio']) != '' ? floatval($args['vl_ceeeprev_patrimonio']) : "DEFAULT").",
					    ".(trim($args['vl_ceeeprev_provisao']) != '' ? floatval($args['vl_ceeeprev_provisao']) : "DEFAULT").",
					    ".(trim($args['vl_ceeeprev_meta']) != '' ? floatval($args['vl_ceeeprev_meta']) : "DEFAULT").",

					    ".(trim($args['vl_ceee_patrimonio']) != '' ? floatval($args['vl_ceee_patrimonio']) : "DEFAULT").",
					    ".(trim($args['vl_ceee_provisao']) != '' ? floatval($args['vl_ceee_provisao']) : "DEFAULT").",
					    ".(trim($args['vl_ceee_meta']) != '' ? floatval($args['vl_ceee_meta']) : "DEFAULT").",

					    ".(trim($args['vl_aessul_patrimonio']) != '' ? floatval($args['vl_aessul_patrimonio']) : "DEFAULT").",
					    ".(trim($args['vl_aessul_provisao']) != '' ? floatval($args['vl_aessul_provisao']) : "DEFAULT").",
					    ".(trim($args['vl_aessul_meta']) != '' ? floatval($args['vl_aessul_meta']) : "DEFAULT").",

					    ".(trim($args['vl_cgtee_patrimonio']) != '' ? floatval($args['vl_cgtee_patrimonio']) : "DEFAULT").",
					    ".(trim($args['vl_cgtee_provisao']) != '' ? floatval($args['vl_cgtee_provisao']) : "DEFAULT").",
					    ".(trim($args['vl_cgtee_meta']) != '' ? floatval($args['vl_cgtee_meta']) : "DEFAULT").",

					    ".(trim($args['vl_rge_patrimonio']) != '' ? floatval($args['vl_rge_patrimonio']) : "DEFAULT").",
					    ".(trim($args['vl_rge_provisao']) != '' ? floatval($args['vl_rge_provisao']) : "DEFAULT").",
					    ".(trim($args['vl_rge_meta']) != '' ? floatval($args['vl_rge_meta']) : "DEFAULT").",

					    ".(trim($args['obs_origem']) != '' ? str_escape($args['obs_origem']) : "DEFAULT").",
					    ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
					    ".(trim($args['fl_media']) != '' ? "'".trim($args["fl_media"])."'" : "DEFAULT").",
					    ".(trim($args['observacao']) != '' ? "'".trim($args["observacao"])."'" : "DEFAULT").",
					    ".intval($args['cd_usuario']).",
					    ".intval($args['cd_usuario'])."
                      );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.atuarial_eap_consolidado_bd
				   SET dt_referencia          = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')" : "DEFAULT").",
				       nr_meta                = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
				       nr_resultado           = ".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").",

				       vl_ceeeprev_patrimonio = ".(trim($args['vl_ceeeprev_patrimonio']) != '' ? floatval($args['vl_ceeeprev_patrimonio']) : "DEFAULT").",
					   vl_ceeeprev_provisao   = ".(trim($args['vl_ceeeprev_provisao']) != '' ? floatval($args['vl_ceeeprev_provisao']) : "DEFAULT").",
					   vl_ceeeprev_meta		  = ".(trim($args['vl_ceeeprev_meta']) != '' ? floatval($args['vl_ceeeprev_meta']) : "DEFAULT").",

					   vl_ceee_patrimonio 	  = ".(trim($args['vl_ceee_patrimonio']) != '' ? floatval($args['vl_ceee_patrimonio']) : "DEFAULT").",
					   vl_ceee_provisao 	  = ".(trim($args['vl_ceee_provisao']) != '' ? floatval($args['vl_ceee_provisao']) : "DEFAULT").",
					   vl_ceee_meta 		  = ".(trim($args['vl_ceee_meta']) != '' ? floatval($args['vl_ceee_meta']) : "DEFAULT").",

					   vl_aessul_patrimonio   = ".(trim($args['vl_aessul_patrimonio']) != '' ? floatval($args['vl_aessul_patrimonio']) : "DEFAULT").",
					   vl_aessul_provisao 	  = ".(trim($args['vl_aessul_provisao']) != '' ? floatval($args['vl_aessul_provisao']) : "DEFAULT").",
					   vl_aessul_meta 		  = ".(trim($args['vl_aessul_meta']) != '' ? floatval($args['vl_aessul_meta']) : "DEFAULT").",

					   vl_cgtee_patrimonio 	  = ".(trim($args['vl_cgtee_patrimonio']) != '' ? floatval($args['vl_cgtee_patrimonio']) : "DEFAULT").",
					   vl_cgtee_provisao 	  = ".(trim($args['vl_cgtee_provisao']) != '' ? floatval($args['vl_cgtee_provisao']) : "DEFAULT").",
					   vl_cgtee_meta 		  = ".(trim($args['vl_cgtee_meta']) != '' ? floatval($args['vl_cgtee_meta']) : "DEFAULT").",

					   vl_rge_patrimonio 	  = ".(trim($args['vl_rge_patrimonio']) != '' ? floatval($args['vl_rge_patrimonio']) : "DEFAULT").",
					   vl_rge_provisao 		  = ".(trim($args['vl_rge_provisao']) != '' ? floatval($args['vl_rge_provisao']) : "DEFAULT").",
					   vl_rge_meta 			  = ".(trim($args['vl_rge_meta']) != '' ? floatval($args['vl_rge_meta']) : "DEFAULT").",

					   obs_origem 			  = ".(trim($args['obs_origem']) != '' ? str_escape($args['obs_origem']) : "DEFAULT").",
	                   cd_indicador_tabela    = ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
					   fl_media               = ".(trim($args['fl_media']) != '' ? "'".trim($args["fl_media"])."'" : "DEFAULT").",
					   observacao             = ".(trim($args['observacao']) != '' ? "'".trim($args["observacao"])."'" : "DEFAULT").",
					   cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
					   dt_alteracao           = CURRENT_TIMESTAMP
			     WHERE cd_atuarial_eap_consolidado_bd = ".intval($args['cd_atuarial_eap_consolidado_bd']).";";
		}

		$this->db->query($qr_sql);		
	}

	public function excluir($cd_atuarial_eap_consolidado_bd, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.atuarial_eap_consolidado_bd 
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario)."
			 WHERE cd_atuarial_eap_consolidado_bd = ".intval($cd_atuarial_eap_consolidado_bd).";"; 

		$this->db->query($qr_sql);
	}	
	
	public function fechar_periodo($cd_indicador_tabela,  $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($cd_usuario)."
			 WHERE cd_indicador_tabela = ".(intval($cd_indicador_tabela))."; ";
			 
		$this->db->query($qr_sql);
	}	
}
?>