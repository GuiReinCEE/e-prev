<?php
class Auditoria_atendimento_prazo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_indicador_tabela)
	{
		$qr_sql = "
			SELECT cd_auditoria_atendimento_prazo, 
			       TO_CHAR(dt_referencia,'MM/YYYY') AS mes_ano_referencia,
				   TO_CHAR(dt_referencia,'YYYY') AS ano_referencia, 
				   TO_CHAR(dt_referencia,'MM') AS mes_referencia, 
				   dt_referencia, 
				   cd_indicador_tabela, 
				   fl_media, 
				   nr_solicitacoes, 
				   nr_respondidos_prazo,
				   nr_respondidos, 
				   nr_meta, 
			       observacao
			  FROM indicador_plugin.auditoria_atendimento_prazo 
			 WHERE dt_exclusao IS NULL
	           AND (fl_media ='S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
			 ORDER BY dt_referencia ASC";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_referencia($cd_indicador_tabela, $nr_ano_referencia)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval,'DD/MM/YYYY') AS dt_referencia,
				   nr_meta, 
				   cd_indicador_tabela,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.auditoria_atendimento_prazo
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.auditoria_atendimento_prazo 
			 WHERE dt_exclusao IS NULL 
			   AND cd_indicador_tabela = ".intval($cd_indicador_tabela)."
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_auditoria_atendimento_prazo)
	{
		$qr_sql = " 
			SELECT cd_auditoria_atendimento_prazo,
			 	   TO_CHAR(dt_referencia,'YYYY') as ano_referencia, 
				   TO_CHAR(dt_referencia,'MM') as mes_referencia, 
				   TO_CHAR(dt_referencia,'DD/MM/YYYY') as dt_referencia, 
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao, 
				   cd_usuario_inclusao, 
				   TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao, 
				   cd_usuario_exclusao, 
				   cd_indicador_tabela, 
				   fl_media, 
			       observacao, 
				   nr_solicitacoes, 
				   nr_respondidos_prazo,
				   nr_respondidos,
				   nr_meta 
			  FROM indicador_plugin.auditoria_atendimento_prazo 
		 	 WHERE cd_auditoria_atendimento_prazo = ".intval($cd_auditoria_atendimento_prazo).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args)
	{
		if(intval($args['cd_auditoria_atendimento_prazo']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.auditoria_atendimento_prazo 
				( 
					dt_referencia,  
					dt_inclusao,
					dt_alteracao, 
					cd_usuario_inclusao,  
					cd_indicador_tabela, 
					fl_media, 
					nr_solicitacoes, 
				    nr_respondidos_prazo,
				    nr_respondidos,
					nr_meta,  
		            observacao,
		            cd_usuario_alteracao
		        ) 
		        VALUES 
		        ( 
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").", 
			 		CURRENT_TIMESTAMP,
			 		CURRENT_TIMESTAMP, 
			 		".intval($args['cd_usuario']).", 
					".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",  
					".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").", 
					".(trim($args['nr_solicitacoes']) == "" ? "DEFAULT" : floatval($args['nr_solicitacoes'])).", 
					".(trim($args['nr_respondidos_prazo']) == "" ? "DEFAULT" : floatval($args['nr_respondidos_prazo'])).", 
					".(trim($args['nr_respondidos']) == "" ? "DEFAULT" : floatval($args['nr_respondidos'])).", 
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).", 
            		".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
            		".intval($args['cd_usuario'])."
				);";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.auditoria_atendimento_prazo 
				SET cd_auditoria_atendimento_prazo = ".intval($args['cd_auditoria_atendimento_prazo']).",
					dt_referencia				   = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",  
					cd_indicador_tabela            = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",  
					fl_media                       = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").", 
					nr_solicitacoes                = ".(trim($args['nr_solicitacoes']) == "" ? "DEFAULT" : floatval($args['nr_solicitacoes'])).", 
					nr_respondidos_prazo           = ".(trim($args['nr_respondidos_prazo']) == "" ? "DEFAULT" : floatval($args['nr_respondidos_prazo'])).",
					nr_respondidos                 = ".(trim($args['nr_respondidos']) == "" ? "DEFAULT" : floatval($args['nr_respondidos'])).",
					nr_meta                        = ".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",  
	            	observacao                     = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'")."
			  WHERE cd_auditoria_atendimento_prazo = ".intval($args['cd_auditoria_atendimento_prazo']).";";
		}
			
		$this->db->query($qr_sql);
	}

	function excluir($cd_auditoria_atendimento_prazo, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE indicador_plugin.auditoria_atendimento_prazo 
			   SET dt_exclusao=current_timestamp, 
			   	   cd_usuario_exclusao=".intval($cd_usuario)." 
			 WHERE cd_auditoria_atendimento_prazo =".intval($cd_auditoria_atendimento_prazo).";"; 
		 
		$this->db->query($qr_sql);
	}

	public function atualiza_fechar_periodo($args = array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.auditoria_atendimento_prazo 
				 (
					dt_referencia, 
					dt_inclusao,
					dt_alteracao, 
					nr_solicitacoes, 
				    nr_respondidos_prazo,
				    nr_respondidos,
					nr_meta,
					cd_indicador_tabela,
					fl_media, 
					cd_usuario_inclusao,
					cd_usuario_alteracao
				  ) 
			 VALUES 
				  ( 
					".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
					CURRENT_TIMESTAMP,
					CURRENT_TIMESTAMP,
					".(trim($args['nr_solicitacoes']) == "" ? "DEFAULT" : floatval($args['nr_solicitacoes'])).", 
					".(trim($args['nr_respondidos_prazo']) == "" ? "DEFAULT" : floatval($args['nr_respondidos_prazo'])).",
					".(trim($args['nr_respondidos']) == "" ? "DEFAULT" : floatval($args['nr_respondidos'])).",
					".(trim($args['nr_meta']) == "" ? "DEFAULT" : floatval($args['nr_meta'])).",
					".(intval($args['cd_indicador_tabela']) == 0 ? 'DEFAULT' : intval($args['cd_indicador_tabela'])).",
					'S',
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
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