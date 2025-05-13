<?php
class Atend_participante_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar( $cd_indicador_tabela)
	{
		
		$qr_sql = "
		   SELECT cd_atend_participante,
				  TO_CHAR(dt_referencia,'YYYY') as ano_referencia,
				  TO_CHAR(dt_referencia,'MM/YYYY') as mes_ano_referencia,
				  dt_referencia,
				  TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao,
				  cd_usuario_inclusao,
				  TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao,
				  cd_usuario_exclusao,
				  cd_indicador_tabela,
				  fl_media,
        		  observacao,
				  nr_ceee,
				  nr_aes,
				  nr_cgtee,
				  nr_rge,
				  nr_crm,
				  nr_senge,
				  nr_sinpro,
				  nr_familia,
				  nr_inpel,
				  nr_foz,
				  nr_ceran,
				  nr_familia_municipio,
				  nr_ieabprev,
				  nr_total_f,
				  nr_meta
			 FROM indicador_plugin.atend_participante 
		    WHERE dt_exclusao IS NULL
		      AND (fl_media='S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		    ORDER BY dt_referencia ASC";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia,
				   nr_meta, 
				   cd_indicador_tabela
			  FROM indicador_plugin.atend_participante 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_atend_participante)
	{
		$qr_sql = " 
			SELECT cd_atend_participante,
				   TO_CHAR(dt_referencia,'YYYY') as ano_referencia,
				   TO_CHAR(dt_referencia,'MM/YYYY') as mes_referencia,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') as dt_referencia,
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao,
				   cd_usuario_inclusao,
				   TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao,
				   cd_usuario_exclusao,
				   cd_indicador_tabela,
				   fl_media,
        		   observacao,
				   nr_ceee ,
				   nr_aes ,
				   nr_cgtee ,
				   nr_rge ,
				   nr_crm ,
				   nr_senge ,
				   nr_sinpro ,
				   nr_familia ,
				   nr_inpel,
				   nr_foz,
				   nr_ceran,
				   nr_familia_municipio,
				   nr_ieabprev,
				   nr_total_f ,
				   nr_meta 
			  FROM indicador_plugin.atend_participante 
			 WHERE cd_atend_participante = ".intval($cd_atend_participante).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		if(intval($args['cd_atend_participante']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.atend_participante 
				( 
					dt_referencia, 
			        dt_inclusao, 
			        cd_usuario_inclusao, 
			        cd_indicador_tabela,
			        fl_media,
                    observacao,
			        nr_ceee, 
			        nr_aes, 
			        nr_cgtee, 
			        nr_rge, 
			        nr_crm, 
			        nr_senge, 
			        nr_sinpro, 
			        nr_familia, 
			        nr_inpel,
			        nr_foz,
				    nr_ceran,
					nr_familia_municipio,
					nr_ieabprev,
				    nr_total_f ,
			        nr_meta 
				)
			 	VALUES 
			 	( 
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").", 
			 		CURRENT_TIMESTAMP, 
			  		".intval($args['cd_usuario']).", 
			    	".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
            		".(trim($args['nr_ceee']) == "" ? "DEFAULT" : floatval($args['nr_ceee'])).",
            		".(trim($args['nr_aes']) == "" ? "DEFAULT" : floatval($args['nr_aes'])).",
            		".(trim($args['nr_cgtee']) == "" ? "DEFAULT" : floatval($args['nr_cgtee'])).",
            		".(trim($args['nr_rge']) == "" ? "DEFAULT" : floatval($args['nr_rge'])).",
            		".(trim($args['nr_crm']) == "" ? "DEFAULT" : floatval($args['nr_crm'])).",
					".(trim($args['nr_senge']) == "" ? "DEFAULT" : floatval($args['nr_senge'])).",
					".(trim($args['nr_sinpro']) == "" ? "DEFAULT" : floatval($args['nr_sinpro'])).",
					".(trim($args['nr_familia']) == "" ? "DEFAULT" : floatval($args['nr_familia'])).",
					".(trim($args['nr_inpel']) == "" ? "DEFAULT" : floatval($args['nr_inpel'])).",
					".(trim($args['nr_foz']) == "" ? "DEFAULT" : floatval($args['nr_foz'])).",
					".(trim($args['nr_ceran']) == "" ? "DEFAULT" : floatval($args['nr_ceran'])).",
					".(trim($args['nr_familia_municipio']) == "" ? "DEFAULT" : floatval($args['nr_familia_municipio'])).",
					".(trim($args['nr_ieabprev']) == "" ? "DEFAULT" : floatval($args['nr_ieabprev'])).",
					".(trim($args['nr_total_f']) == "" ? "DEFAULT" : floatval($args['nr_total_f'])).",
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta']))."
				)";
		}
		else
		{
			$qr_sql="
				UPDATE indicador_plugin.atend_participante 
				SET cd_atend_participante = ".intval($args['cd_atend_participante']).",
				    dt_referencia         = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				    cd_indicador_tabela   = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
				    fl_media              = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
	                observacao            = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
				    nr_ceee               = ".(trim($args['nr_ceee']) == "" ? "DEFAULT" : floatval($args['nr_ceee'])).",
				    nr_aes                = ".(trim($args['nr_aes']) == "" ? "DEFAULT" : floatval($args['nr_aes'])).",
				    nr_cgtee              = ".(trim($args['nr_cgtee']) == "" ? "DEFAULT" : floatval($args['nr_cgtee'])).",
				    nr_rge                = ".(trim($args['nr_rge']) == "" ? "DEFAULT" : floatval($args['nr_rge'])).",
				    nr_crm                = ".(trim($args['nr_crm']) == "" ? "DEFAULT" : floatval($args['nr_crm'])).",
				    nr_senge              = ".(trim($args['nr_senge']) == "" ? "DEFAULT" : floatval($args['nr_senge'])).",
				    nr_sinpro             = ".(trim($args['nr_sinpro']) == "" ? "DEFAULT" : floatval($args['nr_sinpro'])).",
				    nr_familia            = ".(trim($args['nr_familia']) == "" ? "DEFAULT" : floatval($args['nr_familia'])).",
				    nr_inpel              = ".(trim($args['nr_inpel']) == "" ? "DEFAULT" : floatval($args['nr_inpel'])).",
				    nr_foz				  = ".(trim($args['nr_foz']) == "" ? "DEFAULT" : floatval($args['nr_foz'])).",
					nr_ceran			  = ".(trim($args['nr_ceran']) == "" ? "DEFAULT" : floatval($args['nr_ceran'])).",
					nr_familia_municipio  = ".(trim($args['nr_familia_municipio']) == "" ? "DEFAULT" : floatval($args['nr_familia_municipio'])).",
					nr_ieabprev           = ".(trim($args['nr_ieabprev']) == "" ? "DEFAULT" : floatval($args['nr_ieabprev'])).",
					nr_total_f            = ".(trim($args['nr_total_f']) == "" ? "DEFAULT" : floatval($args['nr_total_f'])).",
	                nr_meta               = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta']))."
				 WHERE cd_atend_participante = ".intval($args['cd_atend_participante']).";";
		}

		$this->db->query($qr_sql);
	}

	function excluir($cd_atend_participante, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.atend_participante 
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			   	   cd_usuario_exclusao = ".intval($cd_usuario)." 
			 WHERE cd_atend_participante =".intval($cd_atend_participante).";"; 
		
		$this->db->query($qr_sql);
	}

	public function atualiza_fechar_periodo($args = array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.atend_participante 
				 (
					cd_usuario_inclusao,
					cd_indicador_tabela,
					dt_inclusao,
					dt_referencia,
					nr_total_f,
					nr_meta,
					fl_media,
					nr_ceee, 
			        nr_aes, 
			        nr_cgtee, 
			        nr_rge, 
			        nr_crm, 
			        nr_senge, 
			        nr_sinpro, 
			        nr_familia, 
			        nr_inpel,
			        nr_foz,
				    nr_ceran,
				    nr_familia_municipio,
				    nr_ieabprev
				  ) 
			 VALUES 
				  ( 
					".intval($args['cd_usuario']).",
					".(intval($args['cd_indicador_tabela']) == 0 ? 'DEFAULT' : intval($args['cd_indicador_tabela'])).",
					CURRENT_TIMESTAMP,
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					".(trim($args['nr_total_f']) == "" ? "DEFAULT" : floatval($args['nr_total_f'])).", 
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					'S',
					".(trim($args['nr_ceee']) == "" ? "DEFAULT" : floatval($args['nr_ceee'])).",
				    ".(trim($args['nr_aes']) == "" ? "DEFAULT" : floatval($args['nr_aes'])).",
				    ".(trim($args['nr_cgtee']) == "" ? "DEFAULT" : floatval($args['nr_cgtee'])).",
				    ".(trim($args['nr_rge']) == "" ? "DEFAULT" : floatval($args['nr_rge'])).",
				    ".(trim($args['nr_crm']) == "" ? "DEFAULT" : floatval($args['nr_crm'])).",
				    ".(trim($args['nr_senge']) == "" ? "DEFAULT" : floatval($args['nr_senge'])).",
				    ".(trim($args['nr_sinpro']) == "" ? "DEFAULT" : floatval($args['nr_sinpro'])).",
				    ".(trim($args['nr_familia']) == "" ? "DEFAULT" : floatval($args['nr_familia'])).",
				    ".(trim($args['nr_inpel']) == "" ? "DEFAULT" : floatval($args['nr_inpel'])).",
				    ".(trim($args['nr_foz']) == "" ? "DEFAULT" : floatval($args['nr_foz'])).",
					".(trim($args['nr_ceran']) == "" ? "DEFAULT" : floatval($args['nr_ceran'])).",
					".(trim($args['nr_familia_municipio']) == "" ? "DEFAULT" : floatval($args['nr_familia_municipio'])).",
					".(trim($args['nr_ieabprev']) == "" ? "DEFAULT" : floatval($args['nr_ieabprev']))."
				   );";

		$this->db->query($qr_sql);
	}
	
	public function fechar_periodo($args = array())
	{
		$qr_sql = "
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($args['cd_usuario'])." 
		     WHERE cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).";";

		$this->db->query($qr_sql);
	}
}
?>