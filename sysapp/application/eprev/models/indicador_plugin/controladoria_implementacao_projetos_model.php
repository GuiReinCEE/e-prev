<?php
class Controladoria_implementacao_projetos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT i.cd_controladoria_implementacao_projetos,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.ds_observacao,
				   i.nr_tarefas,
				   i.nr_realizadas,
				   i.nr_resultado,
				   i.nr_meta,
				   CASE WHEN (SELECT MAX(i1.dt_referencia)
                                FROM indicador_plugin.controladoria_implementacao_projetos i1
                               WHERE i1.dt_exclusao IS NULL
							     AND (i1.fl_media='S' OR i1.cd_indicador_tabela = ".intval($cd_indicador_tabela).")) = i.dt_referencia THEN 'S'
					    ELSE 'N'
				   END AS fl_editar
		      FROM indicador_plugin.controladoria_implementacao_projetos i
		     WHERE i.dt_exclusao IS NULL
		       AND(i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($cd_indicador_tabela).")
		     ORDER BY i.dt_referencia ASC;";
		
		return $this->db->query($qr_sql)->result_array();
	}
	
	function carrega_referencia($nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
				   TO_CHAR(dt_referencia + '1 month'::interval, 'MM') AS ds_mes_referencia_n, 
                   TO_CHAR(dt_referencia + '1 month'::interval, 'YYYY') AS ds_ano_referencia_n,
				   nr_meta, 
				   cd_indicador_tabela,
				   nr_tarefas,
                   nr_realizadas,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.controladoria_implementacao_projetos
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.controladoria_implementacao_projetos
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	function carrega($cd_controladoria_implementacao_projetos)
	{
		$qr_sql = "
            SELECT cd_controladoria_implementacao_projetos,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   TO_CHAR(dt_referencia, 'MM') AS mes_referencia, 
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_tarefas,
                   nr_realizadas,
                   nr_resultado,
                   nr_meta,
                   ds_observacao
		      FROM indicador_plugin.controladoria_implementacao_projetos 
			 WHERE cd_controladoria_implementacao_projetos = ".intval($cd_controladoria_implementacao_projetos).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.controladoria_implementacao_projetos 
			     (
					dt_referencia, 
				    nr_tarefas, 
                    nr_realizadas, 
				    nr_meta, 
				    nr_resultado, 
				    cd_indicador_tabela, 
				    fl_media, 
                    ds_observacao,
				    cd_usuario_inclusao,
				    cd_usuario_alteracao
		          ) 
		     VALUES 
			      ( 
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				    ".(trim($args['nr_tarefas']) == "" ? "DEFAULT" : floatval($args['nr_tarefas'])).",
				    ".(trim($args['nr_realizadas']) == "" ? "DEFAULT" : floatval($args['nr_realizadas'])).",
				    ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
				    ".(trim($args['nr_resultado']) == "" ? "DEFAULT" : floatval($args['nr_resultado'])).",
				    ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
				    ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
				    ".(trim($args['ds_observacao']) == "" ? "DEFAULT" : "'".trim($args["ds_observacao"])."'").",
				    ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
                  );";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_controladoria_implementacao_projetos, $args = array())
	{
		$qr_sql = "
			UPDATE indicador_plugin.controladoria_implementacao_projetos
			   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
			       nr_tarefas           = ".(trim($args['nr_tarefas']) == "" ? "DEFAULT" : floatval($args['nr_tarefas'])).",
				   nr_realizadas        = ".(trim($args['nr_realizadas']) == "" ? "DEFAULT" : floatval($args['nr_realizadas'])).",
                   nr_meta              = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
                   nr_resultado         = ".(trim($args['nr_resultado']) == "" ? "DEFAULT" : floatval($args['nr_resultado'])).",
				   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
				   fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
				   ds_observacao        = ".(trim($args['ds_observacao']) == "" ? "DEFAULT" : "'".trim($args["ds_observacao"])."'").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
		     WHERE cd_controladoria_implementacao_projetos = ".intval($cd_controladoria_implementacao_projetos).";";

		$this->db->query($qr_sql);
	}
	
	function excluir($cd_controladoria_implementacao_projetos, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.controladoria_implementacao_projetos
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($cd_usuario)."
		     WHERE cd_controladoria_implementacao_projetos = ".intval($cd_controladoria_implementacao_projetos).";"; 
			 
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