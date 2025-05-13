<?php
class juridico_pareceres_fora_prazo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT i.cd_juridico_pareceres_fora_prazo,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
				   i.nr_valor_1,
				   i.nr_valor_2,
				   i.nr_percentual_f,
				   i.nr_meta,
				   CASE WHEN (SELECT MAX(i1.dt_referencia)
                                FROM indicador_plugin.juridico_pareceres_fora_prazo i1
                               WHERE i1.dt_exclusao IS NULL
							     AND (i1.fl_media='S' OR i1.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")) = i.dt_referencia THEN 'S'
					    ELSE 'N'
				   END AS fl_editar,
				   ds_tabela
		      FROM indicador_plugin.juridico_pareceres_fora_prazo i
		     WHERE i.dt_exclusao IS NULL
		       AND(i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
		     ORDER BY i.dt_referencia ASC;";
		
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela,
				   nr_valor_1,
                   nr_valor_2
			  FROM indicador_plugin.juridico_pareceres_fora_prazo
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
            SELECT cd_juridico_pareceres_fora_prazo,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_percentual_f,
                   nr_meta,
                   observacao,
                   nr_pareceres_ai,
				   nr_pareceres_prazo_ai,
				   nr_pareceres_grc,
				   nr_pareceres_prazo_grc,
				   nr_pareceres_gj,
				   nr_pareceres_prazo_gj,
				   nr_pareceres_gc,
				   nr_pareceres_prazo_gc,
				   nr_pareceres_gti,
				   nr_pareceres_prazo_gti,
				   nr_pareceres_gin,
				   nr_pareceres_prazo_gin,
				   nr_pareceres_gfc,
				   nr_pareceres_prazo_gfc,
				   nr_pareceres_gcm,
				   nr_pareceres_prazo_gcm,
				   nr_pareceres_gp,
				   nr_pareceres_prazo_gp,
				   nr_pareceres_de,
				   nr_pareceres_prazo_de,
				   nr_pareceres_cf,
				   nr_pareceres_prazo_cf,
				   nr_pareceres_cd,
				   nr_pareceres_prazo_cd,
				   nr_pareceres_grsc,
				   nr_pareceres_prazo_grsc,
				   nr_pareceres_gn,
				   nr_pareceres_prazo_gn
		      FROM indicador_plugin.juridico_pareceres_fora_prazo 
			 WHERE cd_juridico_pareceres_fora_prazo = ".intval($args['cd_juridico_pareceres_fora_prazo']).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_juridico_pareceres_fora_prazo']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.juridico_pareceres_fora_prazo 
				     (
						dt_referencia, 
					    nr_valor_1, 
                        nr_valor_2, 
					    nr_meta, 
					    cd_indicador_tabela, 
					    fl_media, 
                        observacao,

						nr_pareceres_ai,
						nr_pareceres_prazo_ai,
						nr_pareceres_grc,
						nr_pareceres_prazo_grc,
						nr_pareceres_gj,
						nr_pareceres_prazo_gj,
						nr_pareceres_gc,
						nr_pareceres_prazo_gc,
						nr_pareceres_gti,
						nr_pareceres_prazo_gti,
						nr_pareceres_gin,
						nr_pareceres_prazo_gin,
						nr_pareceres_gfc,
						nr_pareceres_prazo_gfc,
						nr_pareceres_gcm,
						nr_pareceres_prazo_gcm,
						nr_pareceres_gp,
						nr_pareceres_prazo_gp,
						nr_pareceres_de,
						nr_pareceres_prazo_de,
						nr_pareceres_cf,
						nr_pareceres_prazo_cf,
						nr_pareceres_cd,
						nr_pareceres_prazo_cd,
				   		
				   		nr_pareceres_grsc,
					    nr_pareceres_prazo_grsc,
					    nr_pareceres_gn,
					    nr_pareceres_prazo_gn,

						ds_tabela,

					    cd_usuario_inclusao,
					    cd_usuario_alteracao
			          ) 
			     VALUES 
				      ( 
						".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					    ".(intval($args['nr_valor_1']) == 0 ? "DEFAULT" : intval($args['nr_valor_1'])).",
					    ".(intval($args['nr_valor_2']) == 0 ? "DEFAULT" : intval($args['nr_valor_2'])).",
					    ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					    ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					    ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					    ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",

					    ".(trim($args['nr_pareceres_ai']) == '' ? "DEFAULT" : intval($args['nr_pareceres_ai'])).",
					    ".(trim($args['nr_pareceres_prazo_ai']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_ai'])).",
					    ".(trim($args['nr_pareceres_grc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_grc'])).",
					    ".(trim($args['nr_pareceres_prazo_grc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_grc'])).",
					    ".(trim($args['nr_pareceres_gj']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gj'])).",
					    ".(trim($args['nr_pareceres_prazo_gj']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gj'])).",
					    ".(trim($args['nr_pareceres_gc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gc'])).",
					    ".(trim($args['nr_pareceres_prazo_gc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gc'])).",
					    ".(trim($args['nr_pareceres_gti']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gti'])).",
					    ".(trim($args['nr_pareceres_prazo_gti']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gti'])).",
					    ".(trim($args['nr_pareceres_gin']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gin'])).",
					    ".(trim($args['nr_pareceres_prazo_gin']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gin'])).",
					    ".(trim($args['nr_pareceres_gfc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gfc'])).",
					    ".(trim($args['nr_pareceres_prazo_gfc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gfc'])).",
					    ".(trim($args['nr_pareceres_gcm']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gcm'])).",
					    ".(trim($args['nr_pareceres_prazo_gcm']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gcm'])).",
					    ".(trim($args['nr_pareceres_gp']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gp'])).",
					    ".(trim($args['nr_pareceres_prazo_gp']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gp'])).",
					    ".(trim($args['nr_pareceres_de']) == '' ? "DEFAULT" : intval($args['nr_pareceres_de'])).",
					    ".(trim($args['nr_pareceres_prazo_de']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_de'])).",
					    ".(trim($args['nr_pareceres_cf']) == '' ? "DEFAULT" : intval($args['nr_pareceres_cf'])).",
					    ".(trim($args['nr_pareceres_prazo_cf']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_cf'])).",
					    ".(trim($args['nr_pareceres_cd']) == '' ? "DEFAULT" : intval($args['nr_pareceres_cd'])).",
					    ".(trim($args['nr_pareceres_prazo_cd']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_cd'])).",

					    ".(trim($args['nr_pareceres_grsc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_grsc'])).",
					    ".(trim($args['nr_pareceres_prazo_grsc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_grsc'])).",

					    ".(trim($args['nr_pareceres_gn']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gn'])).",
					    ".(trim($args['nr_pareceres_prazo_gn']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gn'])).",

					    ".(trim($args['ds_tabela']) == "" ? "DEFAULT" : "'".trim($args["ds_tabela"])."'").",

					    ".intval($args['cd_usuario']).",
					    ".intval($args['cd_usuario'])."
                      );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.juridico_pareceres_fora_prazo
				   SET dt_referencia           = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				       nr_valor_1              = ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
					   nr_valor_2              = ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
	                   nr_meta                 = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					   cd_indicador_tabela     = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   fl_media                = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					   observacao              = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",

						nr_pareceres_ai 	   = ".(trim($args['nr_pareceres_ai']) == '' ? "DEFAULT" : intval($args['nr_pareceres_ai'])).",
					    nr_pareceres_prazo_ai  = ".(trim($args['nr_pareceres_prazo_ai']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_ai'])).",
					    nr_pareceres_grc 	   = ".(trim($args['nr_pareceres_grc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_grc'])).",
					    nr_pareceres_prazo_grc = ".(trim($args['nr_pareceres_prazo_grc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_grc'])).",
					    nr_pareceres_gj 	   = ".(trim($args['nr_pareceres_gj']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gj'])).",
					    nr_pareceres_prazo_gj  = ".(trim($args['nr_pareceres_prazo_gj']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gj'])).",
					    nr_pareceres_gc 	   = ".(trim($args['nr_pareceres_gc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gc'])).",
					    nr_pareceres_prazo_gc  = ".(trim($args['nr_pareceres_prazo_gc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gc'])).",
					    nr_pareceres_gti 	   = ".(trim($args['nr_pareceres_gti']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gti'])).",
					    nr_pareceres_prazo_gti = ".(trim($args['nr_pareceres_prazo_gti']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gti'])).",
					    nr_pareceres_gin 	   = ".(trim($args['nr_pareceres_gin']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gin'])).",
					    nr_pareceres_prazo_gin = ".(trim($args['nr_pareceres_prazo_gin']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gin'])).",
					    nr_pareceres_gfc 	   = ".(trim($args['nr_pareceres_gfc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gfc'])).",
					    nr_pareceres_prazo_gfc = ".(trim($args['nr_pareceres_prazo_gfc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gfc'])).",
					    nr_pareceres_gcm 	   = ".(trim($args['nr_pareceres_gcm']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gcm'])).",
					    nr_pareceres_prazo_gcm = ".(trim($args['nr_pareceres_prazo_gcm']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gcm'])).",
					    nr_pareceres_gp 	   = ".(trim($args['nr_pareceres_gp']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gp'])).",
					    nr_pareceres_prazo_gp  = ".(trim($args['nr_pareceres_prazo_gp']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gp'])).",
					    nr_pareceres_de 	   = ".(trim($args['nr_pareceres_de']) == '' ? "DEFAULT" : intval($args['nr_pareceres_de'])).",
					    nr_pareceres_prazo_de  = ".(trim($args['nr_pareceres_prazo_de']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_de'])).",
					    nr_pareceres_cf 	   = ".(trim($args['nr_pareceres_cf']) == '' ? "DEFAULT" : intval($args['nr_pareceres_cf'])).",
					    nr_pareceres_prazo_cf  = ".(trim($args['nr_pareceres_prazo_cf']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_cf'])).",
					    nr_pareceres_cd 	   = ".(trim($args['nr_pareceres_cd']) == '' ? "DEFAULT" : intval($args['nr_pareceres_cd'])).",
					    nr_pareceres_prazo_cd  = ".(trim($args['nr_pareceres_prazo_cd']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_cd'])).",

					    nr_pareceres_grsc 	   = ".(trim($args['nr_pareceres_grsc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_grsc'])).",
					    nr_pareceres_prazo_grsc  = ".(trim($args['nr_pareceres_prazo_grsc']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_grsc'])).",

					    nr_pareceres_gn 	   = ".(trim($args['nr_pareceres_gn']) == '' ? "DEFAULT" : intval($args['nr_pareceres_gn'])).",
					    nr_pareceres_prazo_gn  = ".(trim($args['nr_pareceres_prazo_gn']) == '' ? "DEFAULT" : intval($args['nr_pareceres_prazo_gn'])).",

					    ds_tabela 			   = ".(trim($args['ds_tabela']) == "" ? "DEFAULT" : "'".trim($args["ds_tabela"])."'").",

					   cd_usuario_alteracao    = ".intval($args['cd_usuario']).",
					   dt_alteracao            = CURRENT_TIMESTAMP
			     WHERE cd_juridico_pareceres_fora_prazo = ".intval($args['cd_juridico_pareceres_fora_prazo']).";";
		}

		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.juridico_pareceres_fora_prazo
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_juridico_pareceres_fora_prazo = ".intval($args['cd_juridico_pareceres_fora_prazo']).";"; 
			 
		$result = $this->db->query($qr_sql);
	}
	
	function atualiza_fechar_periodo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.juridico_pareceres_fora_prazo 
				 (
					dt_referencia, 
					nr_valor_1, 
					nr_valor_2, 
					nr_meta, 
					cd_indicador_tabela, 
					fl_media, 
					cd_usuario_inclusao,
					cd_usuario_alteracao
				  ) 
			 VALUES 
				  ( 
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
				    ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					'S',
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				  );";

		$result = $this->db->query($qr_sql);
	}
	
	function fechar_periodo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".$args['cd_usuario']." 
		     WHERE cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).";";

		$result = $this->db->query($qr_sql);
	}
}
?>