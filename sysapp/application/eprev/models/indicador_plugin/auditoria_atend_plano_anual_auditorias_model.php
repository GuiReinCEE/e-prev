<?php
class Auditoria_atend_plano_anual_auditorias_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT i.cd_auditoria_atend_plano_anual_auditorias,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.ds_observacao,
				   i.nr_auditoria_prevista,
				   i.nr_auditoria_realizada,
				   i.nr_atendimento,
				   i.nr_meta
		      FROM indicador_plugin.auditoria_atend_plano_anual_auditorias i
		     WHERE i.dt_exclusao IS NULL
		       AND (i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		     ORDER BY i.dt_referencia ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
				   TO_CHAR(dt_referencia + '1 month'::interval, 'MM') AS ds_mes_referencia_n, 
                   TO_CHAR(dt_referencia + '1 month'::interval, 'YYYY') AS ds_ano_referencia_n,
				   nr_meta, 
				   cd_indicador_tabela ,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.auditoria_atend_plano_anual_auditorias
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.auditoria_atend_plano_anual_auditorias
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	

	public function carrega($cd_auditoria_atend_plano_anual_auditorias)
	{
		$qr_sql = "
            SELECT cd_auditoria_atend_plano_anual_auditorias,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(dt_referencia, 'MM') AS mes_referencia, 
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_auditoria_prevista,
                   nr_auditoria_realizada,
                   nr_meta,
                   ds_observacao
		      FROM indicador_plugin.auditoria_atend_plano_anual_auditorias 
			 WHERE cd_auditoria_atend_plano_anual_auditorias = ".intval($cd_auditoria_atend_plano_anual_auditorias).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.auditoria_atend_plano_anual_auditorias 
			     (
					dt_referencia, 
				    nr_auditoria_prevista, 
                    nr_auditoria_realizada,
                    nr_atendimento,
				    nr_meta, 
				    cd_indicador_tabela, 
				    fl_media, 
                    ds_observacao,
				    cd_usuario_inclusao,
				    cd_usuario_alteracao
		          ) 
		     VALUES 
			      ( 
					".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')" : "DEFAULT").",
				  	".(trim($args['nr_auditoria_prevista']) != '' ?  intval($args['nr_auditoria_prevista']) : "DEFAULT").",
				  	".(trim($args['nr_auditoria_realizada']) != '' ?  intval($args['nr_auditoria_realizada']) : "DEFAULT").",
				  	".(trim($args['nr_atendimento']) != '' ?  floatval($args['nr_atendimento']) : "DEFAULT").",
				  	".(trim($args['nr_meta']) != '' ?  floatval($args['nr_meta']) : "DEFAULT").",
				    ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				    ".(trim($args['fl_media']) != '' ? "'".trim($args['fl_media'])."'" : "DEFAULT").",
				    ".(trim($args['ds_observacao']) != '' ?  str_escape($args['ds_observacao']) : "DEFAULT").",
				    ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
                  );";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_auditoria_atend_plano_anual_auditorias, $args = array())
	{
		$qr_sql = "
			UPDATE indicador_plugin.auditoria_atend_plano_anual_auditorias
			   SET dt_referencia          = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')" : "DEFAULT").",
			       nr_auditoria_prevista  = ".(trim($args['nr_auditoria_prevista']) != '' ?  intval($args['nr_auditoria_prevista']) : "DEFAULT").",
                   nr_auditoria_realizada =	".(trim($args['nr_auditoria_realizada']) != '' ?  intval($args['nr_auditoria_realizada']) : "DEFAULT").",
                   nr_atendimento         = ".(trim($args['nr_atendimento']) != '' ?  floatval($args['nr_atendimento']) : "DEFAULT").",
                   nr_meta                = ".(trim($args['nr_meta']) != '' ?  floatval($args['nr_meta']) : "DEFAULT").",
				   cd_indicador_tabela    = ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
				   fl_media               = ".(trim($args['fl_media']) != '' ? "'".trim($args['fl_media'])."'" : "DEFAULT").",
				   ds_observacao             = ".(trim($args['ds_observacao']) != '' ?  str_escape($args['ds_observacao']) : "DEFAULT").",
				   cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
				   dt_alteracao           = CURRENT_TIMESTAMP
		     WHERE cd_auditoria_atend_plano_anual_auditorias = ".intval($cd_auditoria_atend_plano_anual_auditorias).";";

		$this->db->query($qr_sql);
	}

	public function excluir($cd_auditoria_atend_plano_anual_auditorias, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.auditoria_atend_plano_anual_auditorias
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_auditoria_atend_plano_anual_auditorias = ".intval($cd_auditoria_atend_plano_anual_auditorias).";"; 
			 
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