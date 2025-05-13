<?php
class Administrativo_acao_corretiva_fora_prazo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT i.cd_administrativo_acao_corretiva_fora_prazo,
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
                                FROM indicador_plugin.administrativo_acao_corretiva_fora_prazo i1
                               WHERE i1.dt_exclusao IS NULL
							     AND (i1.fl_media='S' OR i1.cd_indicador_tabela = ".intval($cd_indicador_tabela).")) = i.dt_referencia THEN 'S'
					    ELSE 'N'
				   END AS fl_editar
		      FROM indicador_plugin.administrativo_acao_corretiva_fora_prazo i
		     WHERE i.dt_exclusao IS NULL
		       AND(i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		     ORDER BY i.dt_referencia ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}
	
	function carrega_referencia()
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
				   TO_CHAR(dt_referencia + '1 month'::interval, 'MM') AS ds_mes_referencia_n, 
                   TO_CHAR(dt_referencia + '1 month'::interval, 'YYYY') AS ds_ano_referencia_n,
				   nr_meta, 
				   cd_indicador_tabela,
				   nr_valor_1,
                   nr_valor_2
			  FROM indicador_plugin.administrativo_acao_corretiva_fora_prazo
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	function carrega($cd_administrativo_acao_corretiva_fora_prazo)
	{
		$qr_sql = "
            SELECT cd_administrativo_acao_corretiva_fora_prazo,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(dt_referencia, 'MM') AS mes_referencia, 
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_percentual_f,
                   nr_meta,
                   observacao
		      FROM indicador_plugin.administrativo_acao_corretiva_fora_prazo 
			 WHERE cd_administrativo_acao_corretiva_fora_prazo = ".intval($cd_administrativo_acao_corretiva_fora_prazo).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.administrativo_acao_corretiva_fora_prazo 
			     (
					dt_referencia, 
				    nr_valor_1, 
                    nr_valor_2, 
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
				    ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
				    ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
				    ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
				    ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
				    ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
				    ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
				    ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
                  );";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_administrativo_acao_corretiva_fora_prazo, $args = array())
	{
		$qr_sql = "
			UPDATE indicador_plugin.administrativo_acao_corretiva_fora_prazo
			   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
			       nr_valor_1           = ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
				   nr_valor_2           = ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
                   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
				   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
				   fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
				   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
		     WHERE cd_administrativo_acao_corretiva_fora_prazo = ".intval($cd_administrativo_acao_corretiva_fora_prazo).";";

		$this->db->query($qr_sql);
	}
	
	function excluir($cd_administrativo_acao_corretiva_fora_prazo, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.administrativo_acao_corretiva_fora_prazo
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_administrativo_acao_corretiva_fora_prazo = ".intval($cd_administrativo_acao_corretiva_fora_prazo).";"; 
			 
		$this->db->query($qr_sql);
	}
	
	function fechar_periodo($cd_indicador_tabela, $cd_usuario)
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