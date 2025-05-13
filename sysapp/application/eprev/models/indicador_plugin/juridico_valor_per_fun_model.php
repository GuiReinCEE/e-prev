<?php
class juridico_valor_per_fun_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT i.cd_juridico_valor_per_fun,
						   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
						   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
						   i.dt_referencia,
						   i.cd_indicador_tabela,
						   i.fl_media,
						   i.observacao,
						   i.nr_laudo,
						   i.nr_liquidacao,
						   i.nr_total,
						   i.nr_manifestacao,
						   i.vl_perito,
						   i.vl_fundacao,
						   i.vl_reversao,
						   i.pr_reversao,
						   i.nr_meta
					  FROM indicador_plugin.juridico_valor_per_fun i
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
					  FROM indicador_plugin.juridico_valor_per_fun
					 WHERE dt_exclusao IS NULL 
					 ORDER BY dt_referencia DESC 
					 LIMIT 1;
			      ";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_juridico_valor_per_fun,
						   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
						   cd_indicador_tabela,
						   fl_media,
						   nr_laudo,
						   nr_liquidacao,
						   nr_total,
						   nr_manifestacao,
						   vl_perito,
						   vl_fundacao,
						   vl_reversao,
						   pr_reversao,
						   nr_meta,
						   observacao
					  FROM indicador_plugin.juridico_valor_per_fun 
					 WHERE cd_juridico_valor_per_fun = ".intval($args['cd_juridico_valor_per_fun']).";
			      ";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_juridico_valor_per_fun']) == 0)
		{
			$qr_sql = "
						INSERT INTO indicador_plugin.juridico_valor_per_fun 
							 (
								dt_referencia, 
								nr_laudo,
								nr_liquidacao,
								nr_manifestacao,
								vl_perito,
								vl_fundacao,
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
								".(trim($args['nr_laudo']) == "" ? "DEFAULT" : floatval($args['nr_laudo'])).",
								".(trim($args['nr_liquidacao']) == "" ? "DEFAULT" : floatval($args['nr_liquidacao'])).",
								".(trim($args['nr_manifestacao']) == "" ? "DEFAULT" : floatval($args['nr_manifestacao'])).",
								".(trim($args['vl_perito']) == "" ? "DEFAULT" : floatval($args['vl_perito'])).",
								".(trim($args['vl_fundacao']) == "" ? "DEFAULT" : floatval($args['vl_fundacao'])).",
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
						UPDATE indicador_plugin.juridico_valor_per_fun
						   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
							   nr_laudo             = ".(trim($args['nr_laudo']) == "" ? "DEFAULT" : floatval($args['nr_laudo'])).",
							   nr_liquidacao        = ".(trim($args['nr_liquidacao']) == "" ? "DEFAULT" : floatval($args['nr_liquidacao'])).",
							   nr_manifestacao      = ".(trim($args['nr_manifestacao']) == "" ? "DEFAULT" : floatval($args['nr_manifestacao'])).",
							   vl_perito            = ".(trim($args['vl_perito']) == "" ? "DEFAULT" : floatval($args['vl_perito'])).",
							   vl_fundacao          = ".(trim($args['vl_fundacao']) == "" ? "DEFAULT" : floatval($args['vl_fundacao'])).",
							   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
							   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
							   fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
							   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
							   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
							   dt_alteracao         = CURRENT_TIMESTAMP
						 WHERE cd_juridico_valor_per_fun = ".intval($args['cd_juridico_valor_per_fun']).";
				      ";
		}

		$result = $this->db->query($qr_sql);
	}	
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
					UPDATE indicador_plugin.juridico_valor_per_fun
					   SET dt_exclusao         = CURRENT_TIMESTAMP, 
						   cd_usuario_exclusao = ".intval($args['cd_usuario'])."
					 WHERE cd_juridico_valor_per_fun = ".intval($args['cd_juridico_valor_per_fun']).";
			      "; 
			 
		$result = $this->db->query($qr_sql);
	}

	function atualiza_fechar_periodo(&$result, $args=array())
	{
		$qr_sql = " 
					INSERT INTO indicador_plugin.juridico_valor_per_fun 
						 (
							dt_referencia, 
							nr_laudo,
							nr_liquidacao,
							nr_total,
							nr_manifestacao,
							vl_perito,
							vl_fundacao,
							vl_reversao,
							pr_reversao,
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
							".(trim($args['nr_laudo']) == "" ? "DEFAULT" : floatval($args['nr_laudo'])).",
							".(trim($args['nr_liquidacao']) == "" ? "DEFAULT" : floatval($args['nr_liquidacao'])).",
							".(trim($args['nr_total']) == "" ? "DEFAULT" : floatval($args['nr_total'])).",
							".(trim($args['nr_manifestacao']) == "" ? "DEFAULT" : floatval($args['nr_manifestacao'])).",
							".(trim($args['vl_perito']) == "" ? "DEFAULT" : floatval($args['vl_perito'])).",
							".(trim($args['vl_fundacao']) == "" ? "DEFAULT" : floatval($args['vl_fundacao'])).",
							".(trim($args['vl_reversao']) == "" ? "DEFAULT" : floatval($args['vl_reversao'])).",
							".(trim($args['pr_reversao']) == "" ? "DEFAULT" : floatval($args['pr_reversao'])).",
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