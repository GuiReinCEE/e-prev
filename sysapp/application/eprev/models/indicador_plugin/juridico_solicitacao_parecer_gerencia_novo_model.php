<?php
class Juridico_solicitacao_parecer_gerencia_novo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function listar($cd_indicador_tabela)
	{


		$qr_sql = "
			SELECT i.cd_juridico_solicitacao_parecer_gerencia_novo,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
				   i.nr_meta,
				   i.nr_total,
				   i.nr_ai,
				   i.nr_grc,
				   i.nr_gj,
				   i.nr_gc,
				   i.nr_gti,
				   i.nr_gin,
				   i.nr_gfc,
				   i.nr_gcm,
				   i.nr_gp,
				   i.nr_de,
				   i.nr_cf,
				   i.nr_cd
			  FROM indicador_plugin.juridico_solicitacao_parecer_gerencia_novo i
			 WHERE i.dt_exclusao IS NULL
			   AND (i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY i.dt_referencia ASC;";

		return $this->db->query($qr_sql)->result_array();
	}	
	
	public function carrega_referencia()
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
				   nr_meta, 
				   cd_indicador_tabela 
			  FROM indicador_plugin.juridico_solicitacao_parecer_gerencia_novo
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}	

	public function carrega($cd_juridico_solicitacao_parecer_gerencia_novo)
	{
		$qr_sql = "
            SELECT cd_juridico_solicitacao_parecer_gerencia_novo,
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
				   cd_indicador_tabela,
				   fl_media,
				   observacao,
				   nr_meta,
				   nr_total,
				   nr_ai,
				   nr_grc,
				   nr_gj,
				   nr_gc,
				   nr_gti,
				   nr_gin,
				   nr_gfc,
				   nr_gcm,
				   nr_gp,
				   nr_de,
				   nr_cf,
				   nr_cd
			  FROM indicador_plugin.juridico_solicitacao_parecer_gerencia_novo
			 WHERE cd_juridico_solicitacao_parecer_gerencia_novo = ".intval($cd_juridico_solicitacao_parecer_gerencia_novo).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.juridico_solicitacao_parecer_gerencia_novo 
				(
					dt_referencia, 
					nr_ai,
				    nr_grc,
				    nr_gj,
				    nr_gc,
				    nr_gti,
				    nr_gin,
				    nr_gfc,
				    nr_gcm,
				    nr_gp,
				    nr_de,
				    nr_cf,
				    nr_cd,
				    nr_total,
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
					".(trim($args['nr_ai']) == "" ? "DEFAULT" : floatval($args['nr_ai'])).",
					".(trim($args['nr_grc']) == "" ? "DEFAULT" : floatval($args['nr_grc'])).",
					".(trim($args['nr_gj']) == "" ? "DEFAULT" : floatval($args['nr_gj'])).",
					".(trim($args['nr_gc']) == "" ? "DEFAULT" : floatval($args['nr_gc'])).",
					".(trim($args['nr_gti']) == "" ? "DEFAULT" : floatval($args['nr_gti'])).",
					".(trim($args['nr_gin']) == "" ? "DEFAULT" : floatval($args['nr_gin'])).",
					".(trim($args['nr_gfc']) == "" ? "DEFAULT" : floatval($args['nr_gfc'])).",
					".(trim($args['nr_gcm']) == "" ? "DEFAULT" : floatval($args['nr_gcm'])).",
					".(trim($args['nr_gp']) == "" ? "DEFAULT" : floatval($args['nr_gp'])).",
					".(trim($args['nr_de']) == "" ? "DEFAULT" : floatval($args['nr_de'])).",
					".(trim($args['nr_cf']) == "" ? "DEFAULT" : floatval($args['nr_cf'])).",
					".(trim($args['nr_cd']) == "" ? "DEFAULT" : floatval($args['nr_cd'])).",
					".(trim($args['nr_total']) == "" ? "DEFAULT" : floatval($args['nr_total'])).",
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				);";

		$this->db->query($qr_sql);
	}	

	public function atualizar($cd_juridico_solicitacao_parecer_gerencia_novo, $args = array())
	{
		$qr_sql = "
			UPDATE indicador_plugin.juridico_solicitacao_parecer_gerencia_novo
			   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
			       nr_ai 				= ".(trim($args['nr_ai']) == "" ? "DEFAULT" : floatval($args['nr_ai'])).",
				   nr_grc 				= ".(trim($args['nr_grc']) == "" ? "DEFAULT" : floatval($args['nr_grc'])).",
				   nr_gj 				= ".(trim($args['nr_gj']) == "" ? "DEFAULT" : floatval($args['nr_gj'])).",
				   nr_gc 				= ".(trim($args['nr_gc']) == "" ? "DEFAULT" : floatval($args['nr_gc'])).",
				   nr_gti 				= ".(trim($args['nr_gti']) == "" ? "DEFAULT" : floatval($args['nr_gti'])).",
				   nr_gin 				= ".(trim($args['nr_gin']) == "" ? "DEFAULT" : floatval($args['nr_gin'])).",
				   nr_gfc 				= ".(trim($args['nr_gfc']) == "" ? "DEFAULT" : floatval($args['nr_gfc'])).",
				   nr_gcm 				= ".(trim($args['nr_gcm']) == "" ? "DEFAULT" : floatval($args['nr_gcm'])).",
				   nr_gp 				= ".(trim($args['nr_gp']) == "" ? "DEFAULT" : floatval($args['nr_gp'])).",   
				   nr_de 				= ".(trim($args['nr_de']) == "" ? "DEFAULT" : floatval($args['nr_de'])).",
				   nr_cf 				= ".(trim($args['nr_cf']) == "" ? "DEFAULT" : floatval($args['nr_cf'])).",
				   nr_cd 				= ".(trim($args['nr_cd']) == "" ? "DEFAULT" : floatval($args['nr_cd'])).",
				   nr_total             = ".(trim($args['nr_total']) == "" ? "DEFAULT" : floatval($args['nr_total'])).",
				   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
				   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
				   fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
				   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
		     WHERE cd_juridico_solicitacao_parecer_gerencia_novo = ".intval($cd_juridico_solicitacao_parecer_gerencia_novo).";";

		$this->db->query($qr_sql);
	}

	public function excluir($cd_juridico_solicitacao_parecer_gerencia_novo, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.juridico_solicitacao_parecer_gerencia_novo
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_juridico_solicitacao_parecer_gerencia_novo = ".intval($cd_juridico_solicitacao_parecer_gerencia_novo).";"; 
			 
		$this->db->query($qr_sql);
	}
	
	public function fechar_periodo($cd_indicador_tabela, $cd_usuario)
	{
		$qr_sql = "
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".$cd_usuario." 
		     WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela).";";

		$this->db->query($qr_sql);
	}	
}
?>