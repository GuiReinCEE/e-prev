<?php
class juridico_num_sol_ext_patr_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_juridico_num_sol_ext_patr,
						   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
						   i.dt_referencia,
						   i.cd_indicador_tabela,
						   i.fl_media,
						   i.observacao,
						   i.nr_perito,
						   i.nr_ceee,
						   i.nr_aes,
						   i.nr_rge,
						   i.nr_cgtee,
						   i.nr_crm,
						   i.nr_total,
						   i.nr_meta
					  FROM indicador_plugin.juridico_num_sol_ext_patr i
					 WHERE i.dt_exclusao IS NULL
					   AND(i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
					 ORDER BY i.dt_referencia ASC;
			      ";

		$result = $this->db->query($qr_sql);
	}		
	
	function carrega_referencia(&$result, $args=array())
	{
		$qr_sql = "
					SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
						   nr_meta, 
						   cd_indicador_tabela 
					  FROM indicador_plugin.juridico_num_sol_ext_patr
					 WHERE dt_exclusao IS NULL 
					 ORDER BY dt_referencia DESC 
					 LIMIT 1;
			      ";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_juridico_num_sol_ext_patr,
						   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
						   cd_indicador_tabela,
						   fl_media,
						   nr_perito,
						   nr_ceee,
						   nr_aes,
						   nr_rge,
						   nr_cgtee,
						   nr_crm,
						   nr_total,
						   nr_meta,
						   observacao
					  FROM indicador_plugin.juridico_num_sol_ext_patr 
					 WHERE cd_juridico_num_sol_ext_patr = ".intval($args['cd_juridico_num_sol_ext_patr']).";
			      ";
			 
		$result = $this->db->query($qr_sql);
	}	
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_juridico_num_sol_ext_patr']) == 0)
		{
			$qr_sql = "
						INSERT INTO indicador_plugin.juridico_num_sol_ext_patr 
							 (
								dt_referencia, 
							    nr_perito,
							    nr_ceee,
							    nr_aes,
							    nr_rge,
							    nr_cgtee,
							    nr_crm,
								nr_meta, 
								cd_indicador_tabela, 
								fl_media, 
								observacao,
								cd_usuario_inclusao,
								cd_usuario_alteracao
							  ) 
						 VALUES 
							  ( 
								".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
								".(trim($args['nr_perito']) == "" ? "DEFAULT" : floatval($args['nr_perito'])).",
								".(trim($args['nr_ceee']) == "" ? "DEFAULT" : floatval($args['nr_ceee'])).",
								".(trim($args['nr_aes']) == "" ? "DEFAULT" : floatval($args['nr_aes'])).",
								".(trim($args['nr_rge']) == "" ? "DEFAULT" : floatval($args['nr_rge'])).",
								".(trim($args['nr_cgtee']) == "" ? "DEFAULT" : floatval($args['nr_cgtee'])).",
								".(trim($args['nr_crm']) == "" ? "DEFAULT" : floatval($args['nr_crm'])).",
								".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
								".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
								".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
								".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
								".intval($args['cd_usuario']).",
								".intval($args['cd_usuario'])."
							  );
					  ";
		}
		else
		{
			$qr_sql = "
						UPDATE indicador_plugin.juridico_num_sol_ext_patr
						   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
							   nr_perito            = ".(trim($args['nr_perito']) == "" ? "DEFAULT" : floatval($args['nr_perito'])).",
							   nr_ceee              = ".(trim($args['nr_ceee']) == "" ? "DEFAULT" : floatval($args['nr_ceee'])).",
							   nr_aes               = ".(trim($args['nr_aes']) == "" ? "DEFAULT" : floatval($args['nr_aes'])).",
							   nr_rge               = ".(trim($args['nr_rge']) == "" ? "DEFAULT" : floatval($args['nr_rge'])).",
							   nr_cgtee             = ".(trim($args['nr_cgtee']) == "" ? "DEFAULT" : floatval($args['nr_cgtee'])).",
							   nr_crm               = ".(trim($args['nr_crm']) == "" ? "DEFAULT" : floatval($args['nr_crm'])).",
							   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
							   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
							   fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
							   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
							   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
							   dt_alteracao         = CURRENT_TIMESTAMP
						 WHERE cd_juridico_num_sol_ext_patr = ".intval($args['cd_juridico_num_sol_ext_patr']).";
				      ";
		}

		$result = $this->db->query($qr_sql);
	}		
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
					UPDATE indicador_plugin.juridico_num_sol_ext_patr
					   SET dt_exclusao         = CURRENT_TIMESTAMP, 
						   cd_usuario_exclusao = ".intval($args['cd_usuario'])."
					 WHERE cd_juridico_num_sol_ext_patr = ".intval($args['cd_juridico_num_sol_ext_patr']).";
			      "; 
			 
		$result = $this->db->query($qr_sql);
	}

	function atualiza_fechar_periodo(&$result, $args=array())
	{
		$qr_sql = " 
						INSERT INTO indicador_plugin.juridico_num_sol_ext_patr 
							 (
								dt_referencia, 
							    nr_perito,
							    nr_ceee,
							    nr_aes,
							    nr_rge,
							    nr_cgtee,
							    nr_crm,
								nr_meta, 
								cd_indicador_tabela, 
								fl_media, 
								observacao,
								cd_usuario_inclusao,
								cd_usuario_alteracao
							  ) 
						 VALUES 
							  ( 
								".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
								".(trim($args['nr_perito']) == "" ? "DEFAULT" : floatval($args['nr_perito'])).",
								".(trim($args['nr_ceee']) == "" ? "DEFAULT" : floatval($args['nr_ceee'])).",
								".(trim($args['nr_aes']) == "" ? "DEFAULT" : floatval($args['nr_aes'])).",
								".(trim($args['nr_rge']) == "" ? "DEFAULT" : floatval($args['nr_rge'])).",
								".(trim($args['nr_cgtee']) == "" ? "DEFAULT" : floatval($args['nr_cgtee'])).",
								".(trim($args['nr_crm']) == "" ? "DEFAULT" : floatval($args['nr_crm'])).",
								".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
								".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
								'S',
								".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
								".intval($args['cd_usuario']).",
								".intval($args['cd_usuario'])."
							  );
				 
				 "; 
		#echo "<PRE>$qr_sql</PRE>";
		$result = $this->db->query($qr_sql);
	}
	
	function fechar_periodo(&$result, $args=array())
	{
		$qr_sql = "
					UPDATE indicador.indicador_tabela 
					   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
						   cd_usuario_fechamento_periodo = ".$args['cd_usuario']." 
					 WHERE cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).";
			      ";

		$result = $this->db->query($qr_sql);
	}	
}
?>