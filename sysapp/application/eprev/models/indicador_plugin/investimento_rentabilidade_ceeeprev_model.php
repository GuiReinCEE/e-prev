<?php
class Investimento_rentabilidade_ceeeprev_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT i.cd_investimento_rentabilidade_ceeeprev,
				   TO_CHAR(i.dt_referencia,'YYYY') AS ano_referencia,
				   TO_CHAR(i.dt_referencia,'MM/YYYY') AS mes_referencia,
				   i.dt_referencia,
				   i.cd_indicador_tabela,
				   i.fl_media,
				   i.observacao,
				   i.nr_valor_1,
				   i.nr_valor_2,
				   i.nr_valor_3,
				   i.nr_valor_4,
				   i.nr_valor_5,
				   i.nr_valor_6,
				   CASE WHEN (SELECT MAX(i1.dt_referencia)
                                FROM indicador_plugin.investimento_rentabilidade_ceeeprev i1
                               WHERE i1.dt_exclusao IS NULL
							     AND (i1.fl_media='S' OR i1.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")) = i.dt_referencia THEN 'S'
					    ELSE 'N'
				   END AS fl_editar
		      FROM indicador_plugin.investimento_rentabilidade_ceeeprev i
		     WHERE i.dt_exclusao IS NULL
		       AND(i.fl_media = 'S' OR i.cd_indicador_tabela = ".intval($args['cd_indicador_tabela']).")
		     ORDER BY i.dt_referencia ASC;";
		
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia, 
				   cd_indicador_tabela,
				   nr_valor_1,
                   nr_valor_2,
                   nr_valor_3,
                   nr_valor_4,
                   nr_valor_5,
                   nr_valor_6
			  FROM indicador_plugin.investimento_rentabilidade_ceeeprev
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
            SELECT cd_investimento_rentabilidade_ceeeprev,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   nr_valor_1,
                   nr_valor_2,
                   nr_valor_3,
                   nr_valor_4,
                   nr_valor_5,
                   nr_valor_6,
                   observacao
		      FROM indicador_plugin.investimento_rentabilidade_ceeeprev 
			 WHERE cd_investimento_rentabilidade_ceeeprev = ".intval($args['cd_investimento_rentabilidade_ceeeprev']).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_investimento_rentabilidade_ceeeprev']) == 0)
		{
			$qr_sql = "
				INSERT INTO indicador_plugin.investimento_rentabilidade_ceeeprev 
				     (
						dt_referencia, 
					    nr_valor_1, 
                        nr_valor_2, 
                        nr_valor_3,
                        nr_valor_4,
                        nr_valor_5,
                   		nr_valor_6,
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
					    ".(trim($args['nr_valor_3']) == "" ? "DEFAULT" : floatval($args['nr_valor_3'])).",
					    ".(trim($args['nr_valor_4']) == "" ? "DEFAULT" : floatval($args['nr_valor_4'])).",
					    ".(trim($args['nr_valor_5']) == "" ? "DEFAULT" : floatval($args['nr_valor_5'])).",
					    ".(trim($args['nr_valor_6']) == "" ? "DEFAULT" : floatval($args['nr_valor_6'])).",
					    ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					    ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					    ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					    ".intval($args['cd_usuario']).",
					    ".intval($args['cd_usuario'])."
                      );";
		}
		else
		{
			$qr_sql = "
				UPDATE indicador_plugin.investimento_rentabilidade_ceeeprev
				   SET dt_referencia        = ".(trim($args['dt_referencia']) == "" ? "DEFAULT" : "TO_DATE('".trim($args["dt_referencia"])."', 'DD/MM/YYYY')").",
				       nr_valor_1           = ".(trim($args['nr_valor_1']) == "" ? "DEFAULT" : floatval($args['nr_valor_1'])).",
					   nr_valor_2           = ".(trim($args['nr_valor_2']) == "" ? "DEFAULT" : floatval($args['nr_valor_2'])).",
					   nr_valor_3           = ".(trim($args['nr_valor_3']) == "" ? "DEFAULT" : floatval($args['nr_valor_3'])).",
					   nr_valor_4           = ".(trim($args['nr_valor_4']) == "" ? "DEFAULT" : floatval($args['nr_valor_4'])).",
	                   nr_valor_5           = ".(trim($args['nr_valor_5']) == "" ? "DEFAULT" : floatval($args['nr_valor_5'])).",
	                   nr_valor_6           = ".(trim($args['nr_valor_6']) == "" ? "DEFAULT" : floatval($args['nr_valor_6'])).",
					   cd_indicador_tabela  = ".(intval($args['cd_indicador_tabela']) == 0 ? "DEFAULT" : intval($args['cd_indicador_tabela'])).",
					   fl_media             = ".(trim($args['fl_media']) == "" ? "''" : "'".trim($args["fl_media"])."'").",
					   observacao           = ".(trim($args['observacao']) == "" ? "DEFAULT" : "'".trim($args["observacao"])."'").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
			     WHERE cd_investimento_rentabilidade_ceeeprev = ".intval($args['cd_investimento_rentabilidade_ceeeprev']).";";
		}

		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = " 
			UPDATE indicador_plugin.investimento_rentabilidade_ceeeprev
		       SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_investimento_rentabilidade_ceeeprev = ".intval($args['cd_investimento_rentabilidade_ceeeprev']).";"; 
			 
		$result = $this->db->query($qr_sql);
	}
	
	function atualiza_fechar_periodo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO indicador_plugin.investimento_rentabilidade_ceeeprev 
				 (
					dt_referencia, 
					nr_valor_1, 
					nr_valor_2, 
					nr_valor_3, 
					nr_valor_4, 
					nr_valor_5,
               		nr_valor_6,
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
				    ".(trim($args['nr_valor_3']) == "" ? "DEFAULT" : floatval($args['nr_valor_3'])).",
				    ".(trim($args['nr_valor_4']) == "" ? "DEFAULT" : floatval($args['nr_valor_4'])).",
					".(trim($args['nr_valor_5']) == "" ? "DEFAULT" : floatval($args['nr_valor_5'])).",
				    ".(trim($args['nr_valor_6']) == "" ? "DEFAULT" : floatval($args['nr_valor_6'])).",
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