<?php
class Caderno_cci_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function ultimo_dia_mes(&$result, $args=array())
	{
		$qr_sql = "SELECT TO_CHAR((('".$args["ano"]."-".$args["mes"]."-01'::DATE + '1 MONTH'::INTERVAL) - '1 day'::INTERVAL), 'DD') AS dia ;";
			 
		$result = $this->db->query($qr_sql);
	}	

	function atualiza_estrutura_exclusao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT gestao.caderno_cci_atualiza_estrutura(".intval($args["cd_caderno_cci"]).", ".intval($args["cd_usuario"]).");";

		$result = $this->db->query($qr_sql);
	}

	function rentabilidae_historica(&$result, $args=array())
	{		
		$qr_sql = "
			SELECT nr_ano,
			       nr_nominal,
			       nr_inpc,
			       nr_real
			  FROM gestao.caderno_cci_rentabilidade_historica
			 WHERE dt_exclusao IS NULL
			 ORDER BY nr_ano;";

		$result = $this->db->query($qr_sql);
	}

	/* CADASTRO ANO ----------------------------------------------------------------- */ 

	function listar(&$result, $args=array())
	{		
		$qr_sql = "
			SELECT cc.cd_caderno_cci,
			       cc.nr_ano,
			       (SELECT nr_mes
			          FROM gestao.caderno_cci_fechamento ccf
			         WHERE cc.cd_caderno_cci = ccf.cd_caderno_cci
			         ORDER BY ccf.nr_mes DESC  
			        LIMIT 1) AS nr_mes
			 FROM gestao.caderno_cci cc
			 WHERE dt_exclusao IS NULL
			   ".(trim($args["nr_ano"]) != "" ? "AND nr_ano = ".intval($args["nr_ano"]) : "")." ;";

		$result = $this->db->query($qr_sql);
	}

	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cc.cd_caderno_cci,
			       cc.nr_ano
			  FROM gestao.caderno_cci cc
			 WHERE cc.cd_caderno_cci = ".intval($args["cd_caderno_cci"]).";";
			 
		$result = $this->db->query($qr_sql);
	}	

	public function get_arquivo_importar($cd_caderno_cci, $nr_mes, $nr_ano)
	{
		$qr_sql = "
			SELECT cca.cd_caderno_cci_estrutura_arquivo,
			       cca.arquivo,
			       cca.arquivo_nome,
			       TO_CHAR(cca.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM gestao.caderno_cci_estrutura_arquivo cca
			 WHERE cca.cd_caderno_cci = ".intval($cd_caderno_cci)."
			   AND cca.nr_mes         = ".intval($nr_mes)."
			   AND cca.nr_ano         = ".intval($nr_ano)."
			 ORDER BY cca.dt_inclusao DESC  
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_estrutura_valor($cd_caderno_cci_estrutura, $dt_referencia)
	{
		$qr_sql = "
			SELECT cd_caderno_cci_estrutura_valor
			  FROM gestao.caderno_cci_estrutura_valor
			 WHERE cd_caderno_cci_estrutura = ".intval($cd_caderno_cci_estrutura)."
			   AND TO_CHAR(dt_referencia, 'DD/MM/YYYY') = '".trim($dt_referencia)."'
			   AND dt_exclusao IS NULL
			 ORDER BY dt_inclusao DESC
			 LIMIT 1";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_anexo_importar($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.caderno_cci_estrutura_arquivo
			     (
					cd_caderno_cci, 
					nr_ano, 
					nr_mes, 
					arquivo, 
					arquivo_nome, 
					cd_usuario_inclusao, 
					cd_usuario_alteracao
				 )
			VALUES 
			     (
			     	".(trim($args["cd_caderno_cci"]) != "" ? intval($args["cd_caderno_cci"]) : "DEFAULT").",
			     	".(trim($args["nr_ano"]) != "" ? intval($args["nr_ano"]) : "DEFAULT").",
			     	".(trim($args["nr_mes"]) != "" ? intval($args["nr_mes"]) : "DEFAULT").",
			     	".(trim($args["arquivo"]) != "" ? str_escape($args["arquivo"]) : "DEFAULT").",
			     	".(trim($args["arquivo_nome"]) != "" ? str_escape($args["arquivo_nome"]) : "DEFAULT").",
			     	".intval($args["cd_usuario"]).",
					".intval($args["cd_usuario"])."
			 	 );";

		$this->db->query($qr_sql);
	}

	public function atualizar_anexo_importar($cd_caderno_cci_estrutura_arquivo, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_estrutura_arquivo
	           SET nr_ano               = ".(trim($args["nr_ano"]) != "" ? intval($args["nr_ano"]) : "DEFAULT").",
	               nr_mes               = ".(trim($args["nr_mes"]) != "" ? intval($args["nr_mes"]) : "DEFAULT").",
	               arquivo              = ".(trim($args["arquivo"]) != "" ? str_escape($args["arquivo"]) : "DEFAULT").", 
	               arquivo_nome         = ".(trim($args["arquivo_nome"]) != "" ? str_escape($args["arquivo_nome"]) : "DEFAULT").", 
	               cd_usuario_alteracao = ".intval($args["cd_usuario"]).",  
	               dt_alteracao         = CURRENT_TIMESTAMP
	         WHERE cd_caderno_cci_estrutura_arquivo = ".intval($cd_caderno_cci_estrutura_arquivo).";";

		$this->db->query($qr_sql);
	}

	function ano_anterior(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci
			  FROM gestao.caderno_cci
			 WHERE dt_exclusao IS NULL
			   AND nr_ano = ".intval($args["nr_ano"]).";";
			 
		$result = $this->db->query($qr_sql);
	}	

	function salvar(&$result, $args=array())
	{
		if(intval($args["cd_caderno_cci"]) == 0)
		{
			$cd_caderno_cci = intval($this->db->get_new_id("gestao.caderno_cci", "cd_caderno_cci"));

			$qr_sql = "
				INSERT INTO gestao.caderno_cci 
				     (
				     	cd_caderno_cci,
					    nr_ano,
					    cd_caderno_cci_referencia,
						cd_usuario_inclusao,
						cd_usuario_alteracao
					 )
				VALUES
				     (
				     	".intval($cd_caderno_cci).",
					    ".(trim($args["nr_ano"]) != "" ? intval($args["nr_ano"]) : "DEFAULT").",
					    ".(((isset($args["cd_caderno_cci_referencia"])) AND (intval($args["cd_caderno_cci_referencia"]) > 0)) ? intval($args["cd_caderno_cci_referencia"]) : "DEFAULT").",
						".intval($args["cd_usuario"]).",
						".intval($args["cd_usuario"])."
					 );";
		}
		else
		{
			$cd_caderno_cci = $args["cd_caderno_cci"];

			$qr_sql = "
				UPDATE gestao.caderno_cci
				   SET cd_usuario_alteracao = ".intval($args["cd_usuario"]).",
				       dt_alteracao         = CURRENT_TIMESTAMP
			 	 WHERE cd_caderno_cci = ".intval($args["cd_caderno_cci"]).";";
		}
		
			 
		$result = $this->db->query($qr_sql);

		return $cd_caderno_cci;
	}

	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
		     WHERE cd_caderno_cci = ".intval($args["cd_caderno_cci"]).";";
			 
		$this->db->query($qr_sql);
	}

	/* CADASTRO ANO END -------------------------------------------------------------- */  

	/* CADASTRO PROJETADO ------------------------------------------------------------ */

	function projetado(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_projetado,
			       cd_caderno_cci, 
                   ds_caderno_cci_projetado,
                   nr_ordem,
                   nr_projetado,
                   cd_caderno_cci_projetado_referencia
              FROM gestao.caderno_cci_projetado
			 WHERE dt_exclusao IS NULL
			   AND cd_caderno_cci_projetado = ".intval($args["cd_caderno_cci_projetado"]).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function projetado_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_projetado
              FROM gestao.caderno_cci_projetado
			 WHERE cd_caderno_cci_projetado_referencia = ".intval($args["cd_caderno_cci_projetado_referencia"]).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function projetado_salvar(&$result, $args=array())
	{
		if(intval($args["cd_caderno_cci_projetado"]) == 0)
		{
			$qr_sql = "
				INSERT INTO gestao.caderno_cci_projetado
				     (
                       cd_caderno_cci, 
                       ds_caderno_cci_projetado,
                       nr_ordem,
                       nr_projetado,
                       cd_caderno_cci_projetado_referencia,
                       cd_referencia_integracao,
                       cd_usuario_inclusao, 
                       cd_usuario_alteracao
                     )
				VALUES
				     (
				     	".(trim($args["cd_caderno_cci"]) != "" ? intval($args["cd_caderno_cci"]) : "DEFAULT").",
				     	".(trim($args["ds_caderno_cci_projetado"]) != "" ? str_escape($args["ds_caderno_cci_projetado"]) : "DEFAULT").",
				     	".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
				     	".(trim($args["nr_projetado"]) != "" ? app_decimal_para_db($args["nr_projetado"]) : "DEFAULT").",
				     	".(((isset($args["cd_caderno_cci_projetado_referencia"])) AND (intval($args["cd_caderno_cci_projetado_referencia"]) > 0)) ? intval($args["cd_caderno_cci_projetado_referencia"]) : "DEFAULT").",
				     	".(((isset($args["cd_referencia_integracao"])) AND (trim($args["cd_referencia_integracao"]) != '')) ? str_escape($args["cd_referencia_integracao"]) : "DEFAULT").",
						".intval($args["cd_usuario"]).",
						".intval($args["cd_usuario"])."
					 );";
		}
		else
		{		
			$qr_sql = "
				UPDATE gestao.caderno_cci_projetado
				   SET ds_caderno_cci_projetado = ".(trim($args["ds_caderno_cci_projetado"]) != "" ? str_escape($args["ds_caderno_cci_projetado"]) : "DEFAULT").",
				       nr_ordem                 = ".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
				       nr_projetado             = ".(trim($args["nr_projetado"]) != "" ? app_decimal_para_db($args["nr_projetado"]) : "DEFAULT").",
					   cd_usuario_alteracao     = ".intval($args["cd_usuario"]).",
					   dt_alteracao             = CURRENT_TIMESTAMP
				 WHERE cd_caderno_cci_projetado = ".intval($args["cd_caderno_cci_projetado"]).";";
		}

		$this->db->query($qr_sql);
	}

	function projetado_listar(&$result, $args=array(), $ordem = "nr_ordem")
	{
		$qr_sql = "
			SELECT cd_caderno_cci_projetado,
			       cd_caderno_cci, 
                   ds_caderno_cci_projetado,
                   nr_ordem,
                   nr_projetado,
                   cd_referencia_integracao
              FROM gestao.caderno_cci_projetado
			 WHERE dt_exclusao IS NULL
			   AND cd_caderno_cci = ".intval($args["cd_caderno_cci"])."
			 ORDER BY ".$ordem.";";
			 
		$result = $this->db->query($qr_sql);
	}

	function projetado_excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_projetado
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
		     WHERE cd_caderno_cci_projetado = ".intval($args["cd_caderno_cci_projetado"]).";";
			 
		$this->db->query($qr_sql);
	}

	/* CADASTRO PROJETADO END -------------------------------------------------------- */ 

	/* CADASTRO ESTRUTURA ------------------------------------------------------------ */  

	function estrutura_calculo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_estrutura,
			       calculo
			  FROM gestao.caderno_cci_estrutura
			 WHERE cd_caderno_cci = ".intval($args["cd_caderno_cci"])."
			   AND calculo IS NOT NULL
			   AND dt_exclusao IS NULL;";

		$result = $this->db->query($qr_sql);
	}

	function estrutura_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_estrutura
			  FROM gestao.caderno_cci_estrutura
			 WHERE cd_caderno_cci_estrutura_referencia = ".intval($args["cd_caderno_cci_estrutura_referencia"]).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function estrutura_ordem(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_estrutura_pai,
                   nr_ordem
			  FROM gestao.caderno_cci_estrutura
			 WHERE cd_caderno_cci_estrutura = ".intval($args["cd_caderno_cci_estrutura"]).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function estrutura_filho(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_estrutura
			  FROM gestao.caderno_cci_estrutura
			 WHERE cd_caderno_cci_estrutura_pai = ".intval($args["cd_caderno_cci_estrutura"])."
			   AND dt_exclusao IS NULL;";
			 
		$result = $this->db->query($qr_sql);
	}

	function estrutura(&$result, $args=array())
	{
		$qr_sql = "
			SELECT c1.cd_caderno_cci_estrutura,
			       c1.cd_caderno_cci, 
                   c1.ds_caderno_cci_estrutura, 
        		   c1.nr_politica_max, 
        		   c1.nr_politica_min, 
        		   c1.nr_legal_max, 
        		   c1.nr_legal_min, 
        		   c1.nr_rentabilidade,
        		   c1.cd_caderno_cci_estrutura_pai, 
                   c1.fl_grupo, 
                   c1.fl_agrupar,
                   c1.nr_ordem,
                   c1.fl_campo_metro,
                   c1.fl_campo_quantidade,
                   c1.calculo,
                   c1.fl_fundo,
                   c1.fl_total,
                   c1.cd_caderno_cci_estrutura_referencia,
                   c1.fl_real,
                   c1.fl_nominal,
                   (SELECT COUNT(*)
                   	  FROM gestao.caderno_cci_estrutura c2
                   	 WHERE c2.dt_exclusao IS NULL
                   	   AND c2.cd_caderno_cci_estrutura_pai = c1.cd_caderno_cci_estrutura) AS total_filho,
                   0 AS nivel,
                   c1.seq_estrutura,
                   c1.nr_alocacao_estrategica
			  FROM gestao.caderno_cci_estrutura c1
			 WHERE c1.dt_exclusao IS NULL
			   AND c1.cd_caderno_cci_estrutura = ".intval($args["cd_caderno_cci_estrutura"]).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function estrutura_total(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_estrutura,
			       ds_caderno_cci_estrutura,
			       nr_ordem
			  FROM gestao.caderno_cci_estrutura
			 WHERE cd_caderno_cci = ".intval($args["cd_caderno_cci"])."
			   AND fl_total = 'S';";
			 
		$result = $this->db->query($qr_sql);
	}

	function estrutura_pai_principal(&$result, $args=array())
	{		
		$qr_sql = "
			SELECT c.cd_caderno_cci_estrutura,
			       c.cd_caderno_cci, 
                   c.ds_caderno_cci_estrutura, 
                   s.ds_estrutura AS ds_estrutura_oracle,
        		   c.nr_politica_max, 
        		   c.nr_politica_min,
        		   c.nr_legal_max, 
        		   c.nr_legal_min,
        		   c.nr_rentabilidade,
                   c.fl_grupo, 
                   c.fl_agrupar,
                   c.nr_ordem,
                   c.cd_caderno_cci_estrutura_pai,
                   c.fl_campo_metro,
                   c.fl_campo_quantidade,
                   c.fl_fundo,
                   c.calculo,
                   c.fl_total,
                   c.fl_real,
                   c.fl_nominal,
                   c.nr_alocacao_estrategica
			  FROM gestao.caderno_cci_estrutura c
			  LEFT JOIN st_estrutura_seg_cad_cci s
			    ON s.seq_estrutura = c.seq_estrutura
			 WHERE c.cd_caderno_cci = ".intval($args["cd_caderno_cci"])."
			   AND c.dt_exclusao IS NULL
			   AND c.cd_caderno_cci_estrutura_pai IS NULL
			   AND c.fl_grupo = 'S'
			   ".(((!isset($args["calculo"])) OR (trim($args["calculo"])) == "S") ? "AND c.calculo IS NULL" : "" )."
			 ORDER BY c.nr_ordem;";

		$result = $this->db->query($qr_sql);
	}

	function estrutura_pai(&$result, $args=array())
	{		
		$qr_sql = "
			SELECT cd_caderno_cci_estrutura,
			       ds_caderno_cci_estrutura,
			       nr_ordem,
			       cd_caderno_cci_estrutura_pai
			  FROM gestao.caderno_cci_estrutura
			 WHERE cd_caderno_cci = ".intval($args["cd_caderno_cci"])."
			   AND dt_exclusao IS NULL
			   AND cd_caderno_cci_estrutura != ".intval($args["cd_caderno_cci_estrutura"])."
			   AND cd_caderno_cci_estrutura_pai IS NULL
			   AND fl_grupo = 'S'
			 ORDER BY nr_ordem;";

		$result = $this->db->query($qr_sql);
	}

	function estrutura_salvar(&$result, $args=array())
	{
		if(intval($args["cd_caderno_cci_estrutura"]) == 0)
		{
			$cd_caderno_cci_estrutura = intval($this->db->get_new_id("gestao.caderno_cci_estrutura", "cd_caderno_cci_estrutura"));

			$qr_sql = "
				INSERT INTO gestao.caderno_cci_estrutura
				     (
				       cd_caderno_cci_estrutura,
                       cd_caderno_cci, 
                       ds_caderno_cci_estrutura, 
            		   nr_politica_max, 
            		   nr_politica_min,
            		   nr_legal_max, 
            		   nr_legal_min,
            		   nr_alocacao_estrategica,
            		   nr_rentabilidade,
            		   cd_caderno_cci_estrutura_pai, 
                       fl_grupo, 
                       fl_agrupar,
                       nr_ordem,
                       fl_campo_metro,
                       fl_campo_quantidade,
                       fl_fundo,
                       fl_total,
                       cd_caderno_cci_estrutura_referencia,
                       calculo,
                       fl_real,
                       fl_nominal,
                       seq_estrutura,
                       cd_referencia_integracao,
                       cd_usuario_inclusao, 
                       cd_usuario_alteracao
                     )
				VALUES
				     (
				     	".$cd_caderno_cci_estrutura.",
		                ".(trim($args["cd_caderno_cci"]) != "" ? intval($args["cd_caderno_cci"]) : "DEFAULT").",
		                ".(trim($args["ds_caderno_cci_estrutura"]) != "" ? str_escape($args["ds_caderno_cci_estrutura"]) : "DEFAULT").",
		                ".(trim($args["nr_politica_max"]) != "" ? floatval($args["nr_politica_max"]) : "DEFAULT").",
		                ".(trim($args["nr_politica_min"]) != "" ? floatval($args["nr_politica_min"]) : "DEFAULT").",
		                ".(trim($args["nr_legal_max"]) != "" ? floatval($args["nr_legal_max"]) : "DEFAULT").",
		                ".(trim($args["nr_legal_min"]) != "" ? floatval($args["nr_legal_min"]) : "DEFAULT").",
		                ".(trim($args["nr_alocacao_estrategica"]) != "" ? floatval($args["nr_alocacao_estrategica"]) : "DEFAULT").",
		                ".(trim($args["nr_rentabilidade"]) != "" ? floatval($args["nr_rentabilidade"]) : "DEFAULT").",
		                ".(trim($args["cd_caderno_cci_estrutura_pai"]) != "" ? intval($args["cd_caderno_cci_estrutura_pai"]) : "DEFAULT").",
		                ".(trim($args["fl_grupo"]) != "" ? str_escape($args["fl_grupo"]) : "DEFAULT").",
		                ".(trim($args["fl_agrupar"]) != "" ? str_escape($args["fl_agrupar"]) : "DEFAULT").",
		                ".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
		                ".(trim($args["fl_campo_metro"]) != "" ? str_escape($args["fl_campo_metro"]) : "DEFAULT").",
		                ".(trim($args["fl_campo_quantidade"]) != "" ? str_escape($args["fl_campo_quantidade"]) : "DEFAULT").",
		                ".(trim($args["fl_fundo"]) != "" ? str_escape($args["fl_fundo"]) : "DEFAULT").",
		                ".(trim($args["fl_total"]) != "" ? str_escape($args["fl_total"]) : "DEFAULT").",
		                ".(((isset($args["cd_caderno_cci_estrutura_referencia"])) AND (intval($args["cd_caderno_cci_estrutura_referencia"]) > 0)) ? intval($args["cd_caderno_cci_estrutura_referencia"]) : "DEFAULT").",
		                ".(((isset($args["calculo"])) AND (trim($args["calculo"]) != "")) ? "'".trim($args["calculo"])."'" : "DEFAULT").",
		                ".(((isset($args["fl_real"])) AND (trim($args["fl_real"]) != "")) ? str_escape($args["fl_real"]) : "DEFAULT").",
		                ".(((isset($args["fl_nominal"])) AND (trim($args["fl_nominal"]) != "")) ? str_escape($args["fl_nominal"]) : "DEFAULT").",
		                ".(trim($args["seq_estrutura"]) != "" ? intval($args["seq_estrutura"]) : "DEFAULT").",
		                ".(((isset($args["cd_referencia_integracao"])) AND (trim($args["cd_referencia_integracao"]) != '')) ? str_escape($args["cd_referencia_integracao"]) : "DEFAULT").",
		                ".intval($args["cd_usuario"]).",
		                ".intval($args["cd_usuario"])."
					 );";
		}
		else
		{		
			$cd_caderno_cci_estrutura = $args["cd_caderno_cci_estrutura"];

			$qr_sql = "
				UPDATE gestao.caderno_cci_estrutura
				   SET ds_caderno_cci_estrutura     = ".(trim($args["ds_caderno_cci_estrutura"]) != "" ? str_escape($args["ds_caderno_cci_estrutura"]) : "DEFAULT").",
	                   nr_politica_max              = ".(trim($args["nr_politica_max"]) != "" ? floatval($args["nr_politica_max"]) : "DEFAULT").",
	                   nr_politica_min              = ".(trim($args["nr_politica_min"]) != "" ? floatval($args["nr_politica_min"]) : "DEFAULT").",
	                   nr_legal_max                 = ".(trim($args["nr_legal_max"]) != "" ? floatval($args["nr_legal_max"]) : "DEFAULT").",
	                   nr_legal_min                 = ".(trim($args["nr_legal_min"]) != "" ? floatval($args["nr_legal_min"]) : "DEFAULT").",
	                   nr_alocacao_estrategica      = ".(trim($args["nr_alocacao_estrategica"]) != "" ? floatval($args["nr_alocacao_estrategica"]) : "DEFAULT").",
	                   nr_rentabilidade             = ".(trim($args["nr_rentabilidade"]) != "" ? floatval($args["nr_rentabilidade"]) : "DEFAULT").",
	                   cd_caderno_cci_estrutura_pai = ".(trim($args["cd_caderno_cci_estrutura_pai"]) != "" ? intval($args["cd_caderno_cci_estrutura_pai"]) : "DEFAULT").",
                       fl_grupo                     = ".(trim($args["fl_grupo"]) != "" ? str_escape($args["fl_grupo"]) : "DEFAULT").",
                       fl_agrupar                   = ".(trim($args["fl_agrupar"]) != "" ? str_escape($args["fl_agrupar"]) : "DEFAULT").",
                       nr_ordem                     = ".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
                       fl_campo_metro               = ".(trim($args["fl_campo_metro"]) != "" ? str_escape($args["fl_campo_metro"]) : "DEFAULT").",
                       fl_campo_quantidade          = ".(trim($args["fl_campo_quantidade"]) != "" ? str_escape($args["fl_campo_quantidade"]) : "DEFAULT").",
                       fl_fundo                     = ".(trim($args["fl_fundo"]) != "" ? str_escape($args["fl_fundo"]) : "DEFAULT").",
                       seq_estrutura                = ".(trim($args["seq_estrutura"]) != "" ? intval($args["seq_estrutura"]) : "DEFAULT").",
	             	   cd_usuario_alteracao         = ".intval($args["cd_usuario"]).",
	                   dt_alteracao                 = CURRENT_TIMESTAMP
	         WHERE cd_caderno_cci_estrutura = ".intval($args["cd_caderno_cci_estrutura"]).";";
		}
	
	    $this->db->query($qr_sql);

	    return $cd_caderno_cci_estrutura;
	}

	function estrutura_update_pai(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_estrutura
			   SET cd_caderno_cci_estrutura_pai = ".intval($args["cd_caderno_cci_estrutura_pai"])."
			 WHERE cd_caderno_cci_estrutura = ".intval($args["cd_caderno_cci_estrutura"]).";";

		$this->db->query($qr_sql);
	}

	function estrutura_update_calculo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_estrutura
			   SET calculo = '".trim($args["calculo"])."'
			 WHERE cd_caderno_cci_estrutura = ".intval($args["cd_caderno_cci_estrutura"]).";";

		$this->db->query($qr_sql);
	}

	function estrutura_listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT c.cd_caderno_cci_estrutura,
			       c.cd_caderno_cci,
			       c.ds_caderno_cci_estrutura,
			       s.ds_estrutura AS ds_estrutura_oracle,
        		   c.nr_politica_max, 
        		   c.nr_politica_min,
        		   c.nr_legal_max, 
        		   c.nr_legal_min,
        		   c.nr_rentabilidade,
        		   cpai.ds_caderno_cci_estrutura AS ds_caderno_cci_estrutura_pai, 
                   c.fl_grupo, 
                   c.fl_agrupar,
                   c.nr_ordem,
                   c.cd_caderno_cci_estrutura_pai,
                   c.fl_campo_metro,
                   c.fl_campo_quantidade,
                   c.fl_fundo,
                   c.calculo,
                   c.fl_total,
                   c.fl_real,
  				   c.fl_nominal,
  				   c.seq_estrutura,
  				   c.cd_referencia_integracao,
  				   c.nr_alocacao_estrategica,
                   (SELECT COUNT(*)
                   	  FROM gestao.caderno_cci_estrutura c2
                   	 WHERE c2.dt_exclusao IS NULL
                   	   AND c2.cd_caderno_cci_estrutura_pai = c.cd_caderno_cci_estrutura) AS total_filho
			  FROM gestao.caderno_cci_estrutura c
			  LEFT JOIN gestao.caderno_cci_estrutura cpai
			    ON cpai.cd_caderno_cci_estrutura = c.cd_caderno_cci_estrutura_pai
			  LEFT JOIN st_estrutura_seg_cad_cci s
			    ON s.seq_estrutura = c.seq_estrutura
			 WHERE c.dt_exclusao IS NULL
			   AND c.cd_caderno_cci = ".intval($args["cd_caderno_cci"])."
			   ".(isset($args["cd_caderno_cci_estrutura_pai"]) != "" ? "AND c.cd_caderno_cci_estrutura_pai = ".intval($args["cd_caderno_cci_estrutura_pai"]) : "")."
			   ".(((isset($args["fl_grupo"])) AND (trim($args["fl_grupo"]) != "")) != "" ? "AND c.fl_grupo = '".trim($args["fl_grupo"])."'" : "")."
			  ORDER BY c.nr_ordem;";
		$result = $this->db->query($qr_sql);
	}

	function estrutura_excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_estrutura
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
		     WHERE cd_caderno_cci_estrutura = ".intval($args["cd_caderno_cci_estrutura"]).";";
			 
		$this->db->query($qr_sql);
	}

	/* CADASTRO ESTRUTURA END -------------------------------------------------------- */

	/* CADASTRO ESTRUTURA VALOR ------------------------------------------------------ */ 

	function estrutura_proximo_mes(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'MM') AS mes
			  FROM gestao.caderno_cci_estrutura_valor
			 WHERE cd_caderno_cci_estrutura = ".intval($args["cd_caderno_cci_estrutura"])."
			   AND TO_CHAR(dt_referencia, 'YYYY') = '".intval($args["nr_ano"])."'
			   AND dt_exclusao IS NULL
			 ORDER BY dt_inclusao DESC;";
			 
		$result = $this->db->query($qr_sql);
	}

	function estrutura_valor_salvar(&$result, $args=array())
	{
		if(intval($args["cd_caderno_cci_estrutura_valor"]) == 0)
		{
			$cd_caderno_cci_estrutura_valor = intval($this->db->get_new_id("gestao.caderno_cci_estrutura_valor", "cd_caderno_cci_estrutura_valor"));

			$qr_sql = "
				INSERT INTO gestao.caderno_cci_estrutura_valor
				     (
				     	cd_caderno_cci_estrutura_valor,
            			cd_caderno_cci_estrutura, 
            			dt_referencia, 
           				nr_valor_atual, 
           				nr_fluxo, 
           				nr_rentabilidade,  
           				nr_realizado,
           				nr_metro,
           				nr_quantidade,
           				nr_valor_integralizar,
						nr_taxa_adm,
						nr_ano_vencimento,
						nr_participacao_fundo,
                        cd_usuario_inclusao, 
                        cd_usuario_alteracao
                     )
				VALUES
				     (
				     	".intval($cd_caderno_cci_estrutura_valor).",
				     	".(trim($args["cd_caderno_cci_estrutura"]) != "" ? intval($args["cd_caderno_cci_estrutura"]) : "DEFAULT").",
				     	".(trim($args["dt_referencia"]) != "" ? "TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY')" : "DEFAULT").",
				     	".(trim($args["nr_valor_atual"]) != "" ? app_decimal_para_db($args["nr_valor_atual"]) : "DEFAULT").",
				     	".(trim($args["nr_fluxo"]) != "" ? app_decimal_para_db($args["nr_fluxo"]) : "DEFAULT").",
				     	".(trim($args["nr_rentabilidade"]) != "" ? app_decimal_para_db($args["nr_rentabilidade"]) : "DEFAULT").",
				     	".(trim($args["nr_realizado"]) != "" ? app_decimal_para_db($args["nr_realizado"]) : "DEFAULT").",
				     	".(trim($args["nr_metro"]) != "" ? app_decimal_para_db($args["nr_metro"]) : "DEFAULT").",
				     	".(trim($args["nr_quantidade"]) != "" ? app_decimal_para_db($args["nr_quantidade"]) : "DEFAULT").",
				     	".(trim($args["nr_valor_integralizar"]) != "" ? app_decimal_para_db($args["nr_valor_integralizar"]) : "DEFAULT").",
				     	".(trim($args["nr_taxa_adm"]) != "" ? app_decimal_para_db($args["nr_taxa_adm"]) : "DEFAULT").",
				     	".(trim($args["nr_ano_vencimento"]) != "" ? intval($args["nr_ano_vencimento"]) : "DEFAULT").",
				     	".(trim($args["nr_participacao_fundo"]) != "" ? app_decimal_para_db($args["nr_participacao_fundo"]) : "DEFAULT").",
						".intval($args["cd_usuario"]).",
						".intval($args["cd_usuario"])."
					 );";
		}
		else
		{		
			$cd_caderno_cci_estrutura_valor = $args["cd_caderno_cci_estrutura_valor"];

			$qr_sql = "
				UPDATE gestao.caderno_cci_estrutura_valor
				   SET dt_referencia         = ".(trim($args["dt_referencia"]) != "" ? "TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY')" : "DEFAULT").",
           			   nr_valor_atual        = ".(trim($args["nr_valor_atual"]) != "" ? app_decimal_para_db($args["nr_valor_atual"]) : "DEFAULT").",
           			   nr_fluxo              = ".(trim($args["nr_fluxo"]) != "" ? app_decimal_para_db($args["nr_fluxo"]) : "DEFAULT").",
           			   nr_rentabilidade      = ".(trim($args["nr_rentabilidade"]) != "" ? app_decimal_para_db($args["nr_rentabilidade"]) : "DEFAULT").",
           			   nr_realizado          = ".(trim($args["nr_realizado"]) != "" ? app_decimal_para_db($args["nr_realizado"]) : "DEFAULT").",
           			   nr_metro              = ".(trim($args["nr_metro"]) != "" ? app_decimal_para_db($args["nr_metro"]) : "DEFAULT").",
           			   nr_quantidade         = ".(trim($args["nr_quantidade"]) != "" ? app_decimal_para_db($args["nr_quantidade"]) : "DEFAULT").",
           			   nr_valor_integralizar = ".(trim($args["nr_valor_integralizar"]) != "" ? app_decimal_para_db($args["nr_valor_integralizar"]) : "DEFAULT").",
					   nr_taxa_adm           = ".(trim($args["nr_taxa_adm"]) != "" ? app_decimal_para_db($args["nr_taxa_adm"]) : "DEFAULT").",
					   nr_ano_vencimento     = ".(trim($args["nr_ano_vencimento"]) != "" ? intval($args["nr_ano_vencimento"]) : "DEFAULT").",
					   nr_participacao_fundo = ".(trim($args["nr_participacao_fundo"]) != "" ? app_decimal_para_db($args["nr_participacao_fundo"]) : "DEFAULT").",
					   cd_usuario_alteracao  = ".intval($args["cd_usuario"]).",
					   dt_alteracao          = CURRENT_TIMESTAMP
				 WHERE cd_caderno_cci_estrutura_valor = ".intval($cd_caderno_cci_estrutura_valor).";";
		}
	
		$this->db->query($qr_sql);

		return $cd_caderno_cci_estrutura_valor;
	}

	function estrutura_valor_calcula_salvar(&$result, $args=array())
	{
		if(intval($args["cd_caderno_cci_estrutura_valor"]) == 0)
		{
			$cd_caderno_cci_estrutura_valor = intval($this->db->get_new_id("gestao.caderno_cci_estrutura_valor", "cd_caderno_cci_estrutura_valor"));

			$qr_sql = "
				INSERT INTO gestao.caderno_cci_estrutura_valor
		             ( 
		              cd_caderno_cci_estrutura_valor,
		              cd_caderno_cci_estrutura, 
		              dt_referencia, 
		              nr_quantidade,
		              nr_valor_atual, 
		              nr_realizado,
		              nr_metro,
		              cd_usuario_inclusao, 
		              cd_usuario_alteracao
		             )
		        VALUES 
		             (
		              ".intval($cd_caderno_cci_estrutura_valor).",
		              ".intval($args["cd_caderno_cci_estrutura"]).",
				      ".(trim($args["dt_referencia"]) != "" ? "TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY')" : "DEFAULT").",
				      ".(trim($args["nr_quantidade"]) != "" ? app_decimal_para_db($args["nr_quantidade"]) : "DEFAULT").",
		              (SELECT SUM(nr_valor_atual)
                         FROM gestao.caderno_cci_estrutura_valor 
                        WHERE cd_caderno_cci_estrutura IN (SELECT cd_caderno_cci_estrutura 
                                                             FROM gestao.caderno_cci_estrutura 
                                                            WHERE cd_caderno_cci_estrutura_pai = ".intval($args["cd_caderno_cci_estrutura"])."
                                                              AND dt_exclusao IS NULL
                                                              AND dt_referencia = TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'))),
		              (SELECT SUM(nr_realizado)
                         FROM gestao.caderno_cci_estrutura_valor 
                        WHERE cd_caderno_cci_estrutura IN (SELECT cd_caderno_cci_estrutura 
                                                             FROM gestao.caderno_cci_estrutura 
                                                            WHERE cd_caderno_cci_estrutura_pai = ".intval($args["cd_caderno_cci_estrutura"])."
                                                              AND dt_exclusao IS NULL
                                                              AND dt_referencia = TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'))),
                      (SELECT SUM(nr_metro)
                         FROM gestao.caderno_cci_estrutura_valor 
                        WHERE cd_caderno_cci_estrutura IN (SELECT cd_caderno_cci_estrutura 
                                                             FROM gestao.caderno_cci_estrutura 
                                                            WHERE cd_caderno_cci_estrutura_pai = ".intval($args["cd_caderno_cci_estrutura"])."
                                                              AND dt_exclusao IS NULL
                                                              AND dt_referencia = TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'))),
		              ".intval($args["cd_usuario"]).",
					  ".intval($args["cd_usuario"])."
		             );";
		}
		else
		{		
			$cd_caderno_cci_estrutura_valor = $args["cd_caderno_cci_estrutura_valor"];

			$qr_sql = "
				UPDATE gestao.caderno_cci_estrutura_valor
		           SET nr_quantidade =      ".(trim($args["nr_quantidade"]) != "" ? app_decimal_para_db($args["nr_quantidade"]) : "DEFAULT").",
		               nr_valor_atual       = (SELECT SUM(nr_valor_atual)
						                         FROM gestao.caderno_cci_estrutura_valor 
						                        WHERE cd_caderno_cci_estrutura IN (SELECT cd_caderno_cci_estrutura 
						                                                             FROM gestao.caderno_cci_estrutura 
						                                                            WHERE cd_caderno_cci_estrutura_pai = ".intval($args["cd_caderno_cci_estrutura"])."
						                                                              AND dt_exclusao IS NULL
						                                                              AND dt_referencia = TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'))),
		               nr_realizado         = (SELECT SUM(nr_realizado)
						                         FROM gestao.caderno_cci_estrutura_valor 
						                        WHERE cd_caderno_cci_estrutura IN (SELECT cd_caderno_cci_estrutura 
						                                                             FROM gestao.caderno_cci_estrutura 
						                                                            WHERE cd_caderno_cci_estrutura_pai = ".intval($args["cd_caderno_cci_estrutura"])."
						                                                              AND dt_exclusao IS NULL
						                                                              AND dt_referencia = TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'))),
                       nr_metro             = (SELECT SUM(nr_metro)
						                         FROM gestao.caderno_cci_estrutura_valor 
						                        WHERE cd_caderno_cci_estrutura IN (SELECT cd_caderno_cci_estrutura 
						                                                             FROM gestao.caderno_cci_estrutura 
						                                                            WHERE cd_caderno_cci_estrutura_pai = ".intval($args["cd_caderno_cci_estrutura"])."
						                                                              AND dt_exclusao IS NULL
						                                                              AND dt_referencia = TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY'))),
		               cd_usuario_alteracao = ".intval($args["cd_usuario"]).",
		               dt_alteracao         = CURRENT_DATE
		         WHERE cd_caderno_cci_estrutura_valor = ".intval($cd_caderno_cci_estrutura_valor).";";
		}
	
		$this->db->query($qr_sql);

		return $cd_caderno_cci_estrutura_valor;
	}

	function estrutura_valor_participacao_salvar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_estrutura_valor
			   SET nr_participacao       = (CASE WHEN (SELECT nr_valor_atual
			   	                                         FROM gestao.caderno_cci_estrutura_valor
			   	                                        WHERE cd_caderno_cci_estrutura_valor = ".intval($args["cd_caderno_cci_estrutura_valor_pai"]).") > 0
                                                 THEN  ((nr_valor_atual/(SELECT nr_valor_atual
			   	                                                           FROM gestao.caderno_cci_estrutura_valor
			   	                                                          WHERE cd_caderno_cci_estrutura_valor = ".intval($args["cd_caderno_cci_estrutura_valor_pai"])."))*100)
											ELSE 0
                                            END),
				   cd_usuario_alteracao  = ".intval($args["cd_usuario"]).",
				   dt_alteracao          = CURRENT_TIMESTAMP
			 WHERE cd_caderno_cci_estrutura_valor = ".intval($args["cd_caderno_cci_estrutura_valor"]).";";

		$this->db->query($qr_sql);
	}

	function estrutura_valor_rentabilidade_pai_salvar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_estrutura_valor
			   SET nr_rentabilidade      = (SELECT SUM((nr_rentabilidade/100)*nr_participacao)
			   	                              FROM gestao.caderno_cci_estrutura_valor
			   	                             WHERE cd_caderno_cci_estrutura_valor IN (".implode(", ", $args["caderno_cci_estrutura_filho"]).")),
				   cd_usuario_alteracao  = ".intval($args["cd_usuario"]).",
				   dt_alteracao          = CURRENT_TIMESTAMP
			 WHERE cd_caderno_cci_estrutura_valor = ".intval($args["cd_caderno_cci_estrutura_valor"]).";";

		$this->db->query($qr_sql);
	}

	function estrutura_valor(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_estrutura_valor,
			       TO_CHAR(dt_referencia, 'MM') AS mes,
			       nr_valor_atual, 
       			   nr_fluxo, 
       			   nr_rentabilidade,
       			   nr_realizado,
       			   nr_metro,
       			   nr_quantidade,
       			   nr_valor_integralizar,
				   nr_taxa_adm,
				   nr_ano_vencimento,
				   nr_participacao_fundo
       		  FROM gestao.caderno_cci_estrutura_valor v
       		 WHERE dt_exclusao IS NULL
       		   ".(((isset($args["mes"])) AND (trim($args["mes"]) != "")) ? "AND TO_CHAR(dt_referencia, 'MM') = TO_CHAR(".trim($args["mes"]).",'FM00')" : ""  )."
       		   AND cd_caderno_cci_estrutura = ".intval($args["cd_caderno_cci_estrutura"])."
       		 ORDER BY v.dt_referencia;";
     
		$result = $this->db->query($qr_sql);
	}

	function estrutura_valor_listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT v.cd_caderno_cci_estrutura_valor,
				   v.cd_caderno_cci_estrutura,
			       TO_CHAR(v.dt_referencia, 'MM') AS mes,
			       v.nr_valor_atual, 
       			   v.nr_fluxo, 
       			   v.nr_rentabilidade,
       			   v.nr_realizado,
       			   v.nr_valor_integralizar,
				   v.nr_taxa_adm,
				   v.nr_ano_vencimento,
				   v.nr_participacao_fundo,
				   v.nr_metro,
				   v.nr_quantidade,
				   CASE WHEN e.fl_agrupar = 'N' THEN v.nr_valor_integralizar
                        ELSE gestao.get_cci_caderno_nr_valor_integralizar_total(v.cd_caderno_cci_estrutura, v.dt_referencia)
                   END AS nr_valor_integralizar_total,
                   e.cd_caderno_cci_estrutura_pai,
                   e.fl_agrupar
       		  FROM gestao.caderno_cci_estrutura_valor v
       		  JOIN gestao.caderno_cci_estrutura e
       		    ON e.cd_caderno_cci_estrutura = v.cd_caderno_cci_estrutura
       		 WHERE v.dt_exclusao IS NULL
       		   AND v.cd_caderno_cci_estrutura = ".intval($args["cd_caderno_cci_estrutura"])."
       		   AND TO_CHAR(v.dt_referencia, 'MM') <= TO_CHAR(".trim($args["mes"]).",'FM00')
       		  ORDER BY v.dt_referencia;";

		$result = $this->db->query($qr_sql);
	}

	function estrutura_valor_excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_estrutura_valor
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
		     WHERE cd_caderno_cci_estrutura_valor = ".intval($args["cd_caderno_cci_estrutura_valor"]).";";
			 
		$this->db->query($qr_sql);
	}

	/* CADASTRO ESTRUTURA VALOR END -------------------------------------------------- */ 

	/* CADASTRO INDICE --------------------------------------------------------------- */

	function indice(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_indice,
			       cd_caderno_cci, 
                   ds_caderno_cci_indice,
                   nr_ordem,
                   cd_sgs,
                   fl_inpc,
                   cd_caderno_cci_indice_referencia
              FROM gestao.caderno_cci_indice
			 WHERE dt_exclusao IS NULL 
			   AND cd_caderno_cci_indice = ".intval($args["cd_caderno_cci_indice"]).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function indice_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_indice
			  FROM gestao.caderno_cci_indice
			 WHERE cd_caderno_cci_indice_referencia = ".intval($args["cd_caderno_cci_indice_referencia"]).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function indice_salvar(&$result, $args=array())
	{
		if(intval($args["cd_caderno_cci_indice"]) == 0)
		{
			$qr_sql = "
				INSERT INTO gestao.caderno_cci_indice
				     (
                       cd_caderno_cci, 
                       ds_caderno_cci_indice,
                       nr_ordem,
                       cd_caderno_cci_indice_referencia,
                       cd_sgs,
                       fl_inpc,
                       cd_referencia_integracao,
                       cd_usuario_inclusao, 
                       cd_usuario_alteracao
                     )
				VALUES
				     (
				     	".(trim($args["cd_caderno_cci"]) != "" ? intval($args["cd_caderno_cci"]) : "DEFAULT").",
				     	".(trim($args["ds_caderno_cci_indice"]) != "" ? str_escape($args["ds_caderno_cci_indice"]) : "DEFAULT").",
				     	".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
				     	".(((isset($args["cd_caderno_cci_indice_referencia"])) AND (intval($args["cd_caderno_cci_indice_referencia"]) > 0)) ? intval($args["cd_caderno_cci_indice_referencia"]) : "DEFAULT").",
				        ".(((isset($args["cd_sgs"])) AND (intval($args["cd_sgs"]) > 0)) ? intval($args["cd_sgs"]) : "DEFAULT").",
				        ".(((isset($args["fl_inpc"])) AND (trim($args["fl_inpc"]) != "")) ? str_escape($args["fl_inpc"]) : "DEFAULT").",
				        ".(((isset($args["cd_referencia_integracao"])) AND (trim($args["cd_referencia_integracao"]) != '')) ? str_escape($args["cd_referencia_integracao"]) : "DEFAULT").",
						".intval($args["cd_usuario"]).",
						".intval($args["cd_usuario"])."
					 );";
		}
		else
		{		
			$qr_sql = "
				UPDATE gestao.caderno_cci_indice
				   SET ds_caderno_cci_indice = ".(trim($args["ds_caderno_cci_indice"]) != "" ? str_escape($args["ds_caderno_cci_indice"]) : "DEFAULT").",
				       nr_ordem              = ".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
					   cd_usuario_alteracao  = ".intval($args["cd_usuario"]).",
					   dt_alteracao          = CURRENT_TIMESTAMP
				 WHERE cd_caderno_cci_indice = ".intval($args["cd_caderno_cci_indice"]).";";
		}
	
		$this->db->query($qr_sql);
	}

	function indice_listar(&$result, $args=array(), $ordem = "nr_ordem")
	{
		$qr_sql = "
			SELECT cd_caderno_cci_indice,
			       cd_caderno_cci, 
                   ds_caderno_cci_indice,
                   nr_ordem,
                   cd_sgs,
                   fl_inpc,
                   cd_referencia_integracao
              FROM gestao.caderno_cci_indice
			 WHERE dt_exclusao IS NULL
			   AND cd_caderno_cci = ".intval($args["cd_caderno_cci"])."
			 ORDER BY ".$ordem .";";

		$result = $this->db->query($qr_sql);
	}

	function indice_excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_indice
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
		     WHERE cd_caderno_cci_indice = ".intval($args["cd_caderno_cci_indice"]).";";
			 
		$this->db->query($qr_sql);
	}

	/* CADASTRO INDICE END ----------------------------------------------------------- */

	/* CADASTRO INDICE VALOR --------------------------------------------------------- */ 

	function indice_proximo_mes(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'MM') AS mes
			  FROM gestao.caderno_cci_indice_valor
			 WHERE cd_caderno_cci_indice = ".intval($args["cd_caderno_cci_indice"])."
			   AND TO_CHAR(dt_referencia, 'YYYY') = '".intval($args["nr_ano"])."'
			   AND dt_exclusao IS NULL
			 ORDER BY dt_inclusao DESC;";
			 
		$result = $this->db->query($qr_sql);
	}

	function indice_valor_salvar(&$result, $args=array())
	{
		if(intval($args["cd_caderno_cci_indice_valor"]) == 0)
		{
			$qr_sql = "
				INSERT INTO gestao.caderno_cci_indice_valor
				     (
            			cd_caderno_cci_indice, 
            			dt_referencia, 
           				nr_indice, 
                        cd_usuario_inclusao, 
                        cd_usuario_alteracao
                     )
				VALUES
				     (
				     	".(trim($args["cd_caderno_cci_indice"]) != "" ? intval($args["cd_caderno_cci_indice"]) : "DEFAULT").",
				     	".(trim($args["dt_referencia"]) != "" ? "TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY')" : "DEFAULT").",
				     	".(trim($args["nr_indice"]) != "" ? app_decimal_para_db($args["nr_indice"]) : "DEFAULT").",
						".intval($args["cd_usuario"]).",
						".intval($args["cd_usuario"])."
					 );";
		}
		else
		{		
			$qr_sql = "
				UPDATE gestao.caderno_cci_indice_valor
				   SET dt_referencia        = ".(trim($args["dt_referencia"]) != "" ? "TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY')" : "DEFAULT").",
           			   nr_indice            = ".(trim($args["nr_indice"]) != "" ? app_decimal_para_db($args["nr_indice"]) : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args["cd_usuario"]).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_caderno_cci_indice_valor = ".intval($args["cd_caderno_cci_indice_valor"]).";";
		}
	
		$this->db->query($qr_sql);
	}

	function indice_valor(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_indice_valor,
			       TO_CHAR(dt_referencia, 'MM') AS mes,
			       nr_indice
       		  FROM gestao.caderno_cci_indice_valor
       		 WHERE dt_exclusao IS NULL
       		   AND TO_CHAR(dt_referencia, 'MM') = '".trim($args["mes"])."'
       		   AND cd_caderno_cci_indice = ".intval($args["cd_caderno_cci_indice"]).";";

		$result = $this->db->query($qr_sql);
	}

	function indice_valor_listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_indice_valor,
				   cd_caderno_cci_indice,
			       TO_CHAR(dt_referencia, 'MM') AS mes,
			       nr_indice
       		  FROM gestao.caderno_cci_indice_valor
       		 WHERE dt_exclusao IS NULL
       		   AND cd_caderno_cci_indice = ".intval($args["cd_caderno_cci_indice"])."
       		   AND TO_CHAR(dt_referencia, 'MM') <= TO_CHAR(".trim($args["mes"]).",'FM00')
       		  ORDER BY dt_referencia;";

		$result = $this->db->query($qr_sql);
	}

	function indice_valor_excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_indice_valor
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
		     WHERE cd_caderno_cci_indice_valor = ".intval($args["cd_caderno_cci_indice_valor"]).";";
			 
		$this->db->query($qr_sql);
	}

	/* CADASTRO INDICE VALOR END ----------------------------------------------------- */ 

	/* CADASTRO BENCHMARK ------------------------------------------------------------ */ 

	function benchmark(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_benchmark,
			       cd_caderno_cci, 
                   ds_caderno_cci_benchmark,
                   nr_ordem,
                   cd_caderno_cci_benchmark_referencia
              FROM gestao.caderno_cci_benchmark
			 WHERE dt_exclusao IS NULL
			   AND cd_caderno_cci_benchmark = ".intval($args["cd_caderno_cci_benchmark"]).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function benchmark_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_benchmark
			  FROM gestao.caderno_cci_benchmark
			 WHERE cd_caderno_cci_benchmark_referencia = ".intval($args["cd_caderno_cci_benchmark_referencia"]).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function benchmark_salvar(&$result, $args=array())
	{
		if(intval($args["cd_caderno_cci_benchmark"]) == 0)
		{
			$qr_sql = "
				INSERT INTO gestao.caderno_cci_benchmark
				     (
                       cd_caderno_cci, 
                       ds_caderno_cci_benchmark,
                       nr_ordem,
                       cd_caderno_cci_benchmark_referencia,
                       cd_referencia_integracao,
                       cd_usuario_inclusao, 
                       cd_usuario_alteracao
                     )
				VALUES
				     (
				     	".(trim($args["cd_caderno_cci"]) != "" ? intval($args["cd_caderno_cci"]) : "DEFAULT").",
				     	".(trim($args["ds_caderno_cci_benchmark"]) != "" ? str_escape($args["ds_caderno_cci_benchmark"]) : "DEFAULT").",
				     	".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
				     	".(((isset($args["cd_caderno_cci_benchmark_referencia"])) AND (intval($args["cd_caderno_cci_benchmark_referencia"]) > 0)) ? intval($args["cd_caderno_cci_benchmark_referencia"]) : "DEFAULT").",
				     	".(((isset($args["cd_referencia_integracao"])) AND (trim($args["cd_referencia_integracao"]) != '')) ? str_escape($args["cd_referencia_integracao"]) : "DEFAULT").",
						".intval($args["cd_usuario"]).",
						".intval($args["cd_usuario"])."
					 );";
		}
		else
		{		
			$qr_sql = "
				UPDATE gestao.caderno_cci_benchmark
				   SET ds_caderno_cci_benchmark = ".(trim($args["ds_caderno_cci_benchmark"]) != "" ? str_escape($args["ds_caderno_cci_benchmark"]) : "DEFAULT").",
				       nr_ordem                 = ".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
					   cd_usuario_alteracao     = ".intval($args["cd_usuario"]).",
					   dt_alteracao             = CURRENT_TIMESTAMP
				 WHERE cd_caderno_cci_benchmark = ".intval($args["cd_caderno_cci_benchmark"]).";";
		}
	
		$this->db->query($qr_sql);
	}

	function benchmark_listar(&$result, $args=array(), $ordem = "nr_ordem")
	{
		$qr_sql = "
			SELECT cd_caderno_cci_benchmark,
			       cd_caderno_cci, 
                   ds_caderno_cci_benchmark,
                   nr_ordem,
                   cd_referencia_integracao
              FROM gestao.caderno_cci_benchmark
			 WHERE dt_exclusao IS NULL
			   AND cd_caderno_cci = ".intval($args["cd_caderno_cci"])."
			 ORDER BY ".$ordem.";";
			 
		$result = $this->db->query($qr_sql);
	}

	function benchmark_excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_benchmark
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
		     WHERE cd_caderno_cci_benchmark = ".intval($args["cd_caderno_cci_benchmark"]).";";
			 
		$this->db->query($qr_sql);
	}

	/* CADASTRO BENCHMARK END -------------------------------------------------------- */ 

	/* CADASTRO BENCHMARK VALOR ------------------------------------------------------ */

	function benchmark_proximo_mes(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'MM') AS mes
			  FROM gestao.caderno_cci_benchmark_valor
			 WHERE cd_caderno_cci_benchmark = ".intval($args["cd_caderno_cci_benchmark"])."
			   AND TO_CHAR(dt_referencia, 'YYYY') = '".intval($args["nr_ano"])."'
			   AND dt_exclusao IS NULL
			 ORDER BY dt_inclusao DESC;";
			 
		$result = $this->db->query($qr_sql);
	}

	function benchmark_valor_salvar(&$result, $args=array())
	{
		if(intval($args["cd_caderno_cci_benchmark_valor"]) == 0)
		{
			$qr_sql = "
				INSERT INTO gestao.caderno_cci_benchmark_valor
				     (
            			cd_caderno_cci_benchmark, 
            			dt_referencia, 
           				nr_benchmark, 
                        cd_usuario_inclusao, 
                        cd_usuario_alteracao
                     )
				VALUES
				     (
				     	".(trim($args["cd_caderno_cci_benchmark"]) != "" ? intval($args["cd_caderno_cci_benchmark"]) : "DEFAULT").",
				     	".(trim($args["dt_referencia"]) != "" ? "TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY')" : "DEFAULT").",
				     	".(trim($args["nr_benchmark"]) != "" ? app_decimal_para_db($args["nr_benchmark"]) : "DEFAULT").",
						".intval($args["cd_usuario"]).",
						".intval($args["cd_usuario"])."
					 );";
		}
		else
		{		
			$qr_sql = "
				UPDATE gestao.caderno_cci_benchmark_valor
				   SET dt_referencia        = ".(trim($args["dt_referencia"]) != "" ? "TO_DATE('".$args["dt_referencia"]."', 'DD/MM/YYYY')" : "DEFAULT").",
           			   nr_benchmark            = ".(trim($args["nr_benchmark"]) != "" ? app_decimal_para_db($args["nr_benchmark"]) : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args["cd_usuario"]).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_caderno_cci_benchmark_valor = ".intval($args["cd_caderno_cci_benchmark_valor"]).";";
		}
	
		$this->db->query($qr_sql);
	}

	function benchmark_valor(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_benchmark_valor,
			       TO_CHAR(dt_referencia, 'MM') AS mes,
			       nr_benchmark
       		  FROM gestao.caderno_cci_benchmark_valor
       		 WHERE dt_exclusao IS NULL
       		   AND TO_CHAR(dt_referencia, 'MM') = '".trim($args["mes"])."'
       		   AND cd_caderno_cci_benchmark = ".intval($args["cd_caderno_cci_benchmark"]).";";

		$result = $this->db->query($qr_sql);
	}

	function benchmark_valor_listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_benchmark_valor,
				   cd_caderno_cci_benchmark,
			       TO_CHAR(dt_referencia, 'MM') AS mes,
			       nr_benchmark
       		  FROM gestao.caderno_cci_benchmark_valor
       		 WHERE dt_exclusao IS NULL
       		   AND cd_caderno_cci_benchmark = ".intval($args["cd_caderno_cci_benchmark"])."
       		   AND TO_CHAR(dt_referencia, 'MM') <= TO_CHAR(".trim($args["mes"]).",'FM00')
       		 ORDER BY dt_referencia;";

		$result = $this->db->query($qr_sql);
	}

	function benchmark_valor_excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_benchmark_valor
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
		     WHERE cd_caderno_cci_benchmark_valor = ".intval($args["cd_caderno_cci_benchmark_valor"]).";";
			 
		$this->db->query($qr_sql);
	}

	/* CADASTRO BENCHMARK VALOR END -------------------------------------------------- */

	function grafico(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_grafico,
			       ds_caderno_cci_grafico,
			       parametro,
			       tp_grafico,
			       nr_ordem,
			       campo,
			       participacao,
			       participacao_m2,
			       nota_rodape,
			       fl_ano,
			       cor,
			       negrito,
			       ordem,
			       ds_html,
			       fl_mes,
			       linha,
			       tab
			  FROM gestao.caderno_cci_grafico
			 WHERE cd_caderno_cci_grafico = ".intval($args["cd_caderno_cci_grafico"]).";";

		$result = $this->db->query($qr_sql);
	}

	function grafico_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_grafico
			  FROM gestao.caderno_cci_grafico
			 WHERE cd_caderno_cci_grafico_referencia = ".intval($args["cd_caderno_cci_grafico_referencia"]).";";

		$result = $this->db->query($qr_sql);
	}

	function grafico_listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_grafico,
			       ds_caderno_cci_grafico,
			       parametro,
			       cd_caderno_cci,
			       CASE WHEN tp_grafico = 'L' THEN 'Linha'
			            WHEN tp_grafico = 'B' THEN 'Barra'
			            WHEN tp_grafico = 'T' THEN 'Tabela'
			            WHEN tp_grafico = 'P' THEN 'Pizza'
			            WHEN tp_grafico = 'E' THEN 'Texto'
			            WHEN tp_grafico = 'R' THEN 'Rentabilidade Histórica'
			            WHEN tp_grafico = 'A' THEN 'Barra Agrupada'
			       END AS tipo_grafico,
			       tp_grafico,
			       nr_ordem,
			       campo,
			       participacao,
			       participacao_m2,
			       nota_rodape,
			       fl_ano,
			       cor,
			       ordem,
			       negrito,
			       ds_html,
			       fl_mes,
			       linha,
			       tab
			  FROM gestao.caderno_cci_grafico
			 WHERE dt_exclusao IS NULL 
			   AND cd_caderno_cci = ".intval($args["cd_caderno_cci"])."
			 ORDER BY nr_ordem ASC;";

		$result = $this->db->query($qr_sql);
	}

	function grafico_salvar(&$result, $args=array())
	{
		if(intval($args["cd_caderno_cci_grafico"]) == 0)
		{
			$qr_sql = "
				INSERT INTO gestao.caderno_cci_grafico
				     (
                       cd_caderno_cci, 
                       ds_caderno_cci_grafico,
                       tp_grafico,
                       parametro,
                       nr_ordem,
                       nota_rodape,
                       fl_ano,
                       fl_mes,
                       cd_caderno_cci_grafico_referencia,
                       campo,
                       participacao,
                       participacao_m2,
                       cor,
                       ordem,
                       negrito,
                       ds_html,
                       linha,
                       tab,
                       cd_usuario_inclusao, 
                       cd_usuario_alteracao
                     )
				VALUES
				     (
				     	".(trim($args["cd_caderno_cci"]) != "" ? intval($args["cd_caderno_cci"]) : "DEFAULT").",
				     	".(trim($args["ds_caderno_cci_grafico"]) != "" ? str_escape($args["ds_caderno_cci_grafico"]) : "DEFAULT").",
				     	".(trim($args["tp_grafico"]) != "" ? str_escape($args["tp_grafico"]) : "DEFAULT").",
				     	".(trim($args["parametro"]) != "" ? str_escape($args["parametro"]) : "DEFAULT").",
				     	".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
				     	".(trim($args["nota_rodape"]) != "" ? str_escape($args["nota_rodape"]) : "DEFAULT").",
				     	".(trim($args["fl_ano"]) != "" ? str_escape($args["fl_ano"]) : "DEFAULT").",
				     	".(trim($args["fl_mes"]) != "" ? str_escape($args["fl_mes"]) : "DEFAULT").",
				     	".(((isset($args["cd_caderno_cci_grafico_referencia"])) AND (intval($args["cd_caderno_cci_grafico_referencia"]) > 0)) ? intval($args["cd_caderno_cci_grafico_referencia"]) : "DEFAULT").",
				        ".(((isset($args["campo"])) AND (trim($args["campo"]) != "")) ? str_escape($args["campo"]) : "DEFAULT").",
				        ".(((isset($args["participacao"])) AND (trim($args["participacao"]) != "")) ? str_escape($args["participacao"]) : "DEFAULT").",
				        ".(((isset($args["participacao_m2"])) AND (trim($args["participacao_m2"]) != "")) ? str_escape($args["participacao_m2"]) : "DEFAULT").",
				        ".(((isset($args["cor"])) AND (trim($args["cor"]) != "")) ? str_escape($args["cor"]) : "DEFAULT").",
				        ".(((isset($args["ordem"])) AND (trim($args["ordem"]) != "")) ? str_escape($args["ordem"]) : "DEFAULT").",
				        ".(((isset($args["negrito"])) AND (trim($args["negrito"]) != "")) ? str_escape($args["negrito"]) : "DEFAULT").",
				        ".(((isset($args["ds_html"])) AND (trim($args["ds_html"]) != "")) ? str_escape($args["ds_html"]) : "DEFAULT").",
				        ".(((isset($args["linha"])) AND (trim($args["linha"]) != "")) ? str_escape($args["linha"]) : "DEFAULT").",
				        ".(((isset($args["tab"])) AND (trim($args["tab"]) != "")) ? str_escape($args["tab"]) : "DEFAULT").",
						".intval($args["cd_usuario"]).",
						".intval($args["cd_usuario"])."
					 );";
		}
		else
		{		
			$qr_sql = "
				UPDATE gestao.caderno_cci_grafico
				   SET ds_caderno_cci_grafico = ".(trim($args["ds_caderno_cci_grafico"]) != "" ? str_escape($args["ds_caderno_cci_grafico"]) : "DEFAULT").",
				       parametro              = ".(trim($args["parametro"]) != "" ? str_escape($args["parametro"]) : "DEFAULT").",
				       tp_grafico             = ".(trim($args["tp_grafico"]) != "" ? str_escape($args["tp_grafico"]) : "DEFAULT").",
				       nr_ordem               = ".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
				       nota_rodape            = ".(trim($args["nota_rodape"]) != "" ? str_escape($args["nota_rodape"]) : "DEFAULT").",
				       fl_ano                 = ".(trim($args["fl_ano"]) != "" ? str_escape($args["fl_ano"]) : "DEFAULT").",
				       fl_mes                 = ".(trim($args["fl_mes"]) != "" ? str_escape($args["fl_mes"]) : "DEFAULT").",
					   cd_usuario_alteracao   = ".intval($args["cd_usuario"]).",
					   dt_alteracao           = CURRENT_TIMESTAMP
				 WHERE cd_caderno_cci_grafico = ".intval($args["cd_caderno_cci_grafico"]).";";
		}
	
		$this->db->query($qr_sql);
	}

	function grafico_salvar_ordem(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_grafico
			   SET nr_ordem               = ".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
				   cd_usuario_alteracao   = ".intval($args["cd_usuario"]).",
				   dt_alteracao           = CURRENT_TIMESTAMP
			 WHERE cd_caderno_cci_grafico = ".intval($args["cd_caderno_cci_grafico"]).";";

		$this->db->query($qr_sql);
	}

	function grafico_configurar_tabela_salvar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_grafico
			   SET campo                = ".(trim($args["campo"]) != "" ? str_escape($args["campo"]) : "DEFAULT").",
			       participacao         = ".(trim($args["participacao"]) != "" ? str_escape($args["participacao"]) : "DEFAULT").",
			       participacao_m2      = ".(trim($args["participacao_m2"]) != "" ? str_escape($args["participacao_m2"]) : "DEFAULT").",
			       ordem                = ".(trim($args["ordem"]) != "" ? str_escape($args["ordem"]) : "DEFAULT").",
			       negrito              = ".(trim($args["negrito"]) != "" ? str_escape($args["negrito"]) : "DEFAULT").",
			       linha                = ".(trim($args["linha"]) != "" ? str_escape($args["linha"]) : "DEFAULT").",
			       tab                  = ".(trim($args["tab"]) != "" ? str_escape($args["tab"]) : "DEFAULT").",
			       cd_usuario_alteracao = ".intval($args["cd_usuario"]).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_caderno_cci_grafico = ".intval($args["cd_caderno_cci_grafico"]).";";

		$this->db->query($qr_sql);
	}

	function grafico_configurar_salvar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_grafico
			   SET cor                  = ".(trim($args["cor"]) != "" ? str_escape($args["cor"]) : "DEFAULT").",
			       ordem                = ".(trim($args["ordem"]) != "" ? str_escape($args["ordem"]) : "DEFAULT").",
			       cd_usuario_alteracao = ".intval($args["cd_usuario"]).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_caderno_cci_grafico = ".intval($args["cd_caderno_cci_grafico"]).";";

		$this->db->query($qr_sql);
	}

	function grafico_configurar_texto_salvar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_grafico
			   SET ds_html              = ".(trim($args["ds_html"]) != "" ? str_escape($args["ds_html"]) : "DEFAULT").",
			       cd_usuario_alteracao = ".intval($args["cd_usuario"]).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_caderno_cci_grafico = ".intval($args["cd_caderno_cci_grafico"]).";";

		$this->db->query($qr_sql);
	}

	function grafico_update(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_grafico
			   SET parametro       = ".(trim($args["parametro"]) != "" ? str_escape($args["parametro"]) : "DEFAULT").",
			       participacao    = ".(trim($args["participacao"]) != "" ? str_escape($args["participacao"]) : "DEFAULT").",
			       participacao_m2 = ".(trim($args["participacao_m2"]) != "" ? str_escape($args["participacao_m2"]) : "DEFAULT").",
			       cor             = ".(trim($args["cor"]) != "" ? str_escape($args["cor"]) : "DEFAULT").",
			       ordem           = ".(trim($args["ordem"]) != "" ? str_escape($args["ordem"]) : "DEFAULT").",
			       negrito         = ".(trim($args["negrito"]) != "" ? str_escape($args["negrito"]) : "DEFAULT").",
			       linha           = ".(trim($args["linha"]) != "" ? str_escape($args["linha"]) : "DEFAULT").",
			       tab             = ".(trim($args["tab"]) != "" ? str_escape($args["tab"]) : "DEFAULT")."
			 WHERE cd_caderno_cci_grafico = ".intval($args["cd_caderno_cci_grafico"]).";";

		$this->db->query($qr_sql);
	}

	function grafico_excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_grafico
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
		     WHERE cd_caderno_cci_grafico = ".intval($args["cd_caderno_cci_grafico"]).";";
			 
		$this->db->query($qr_sql);
	}

	function get_valor_total(&$result, $args=array())
	{
		$qr_sql = "SELECT gestao.get_valor_total(".$args["cd_caderno_cci_estrutura"].", '".$args["dt_referencia"]."') AS nr_valor;";

		$result = $this->db->query($qr_sql);
	}

	function fechamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(f.dt_inclusao, 'DD/MM/YYYY') AS dt_inclusao,
			       uc.nome,
			       f.parametro
			  FROM gestao.caderno_cci_fechamento f
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = f.cd_usuario_inclusao
			 WHERE cd_caderno_cci = ".intval($args["cd_caderno_cci"])."
			   AND f.nr_mes       = ".intval($args["mes"]).";";

		$result = $this->db->query($qr_sql);
	}

	function fechamento_salvar(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO gestao.caderno_cci_fechamento
			     (
                   	cd_caderno_cci, 
                   	nr_mes, 
                   	parametro, 
                   	cd_usuario_inclusao
                 )
            VALUES 
                 (
                 	".(trim($args["cd_caderno_cci"]) != "" ? intval($args["cd_caderno_cci"]) : "DEFAULT").",
                 	".(trim($args["nr_mes"]) != "" ? intval($args["nr_mes"]) : "DEFAULT").",	
                 	".(trim($args["parametro"]) != "" ? str_escape($args["parametro"]) : "DEFAULT").",
				    ".intval($args["cd_usuario"])."
                 );";

		$result = $this->db->query($qr_sql);
	}

	function grafico_rotulo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_grafico,
				   cd_caderno_cci_grafico_rotulo,
                   ds_caderno_cci_grafico_rotulo,
                   nr_ordem,
                   cor
			  FROM gestao.caderno_cci_grafico_rotulo
			 WHERE cd_caderno_cci_grafico_rotulo = ".intval($args["cd_caderno_cci_grafico_rotulo"]).";";

		$result = $this->db->query($qr_sql);
	}

	function grafico_rotulo_referencia(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_grafico_rotulo
			  FROM gestao.caderno_cci_grafico_rotulo
			 WHERE cd_caderno_cci_grafico_rotulo_referencia = ".intval($args["cd_caderno_cci_grafico_rotulo_referencia"]).";";

		$result = $this->db->query($qr_sql);
	}

	function grafico_rotulo_listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_grafico,
				   cd_caderno_cci_grafico_rotulo,
                   ds_caderno_cci_grafico_rotulo,
                   nr_ordem,
                   cor
			  FROM gestao.caderno_cci_grafico_rotulo
			 WHERE cd_caderno_cci_grafico = ".intval($args["cd_caderno_cci_grafico"])."
			   AND dt_exclusao IS NULL
			 ORDER BY nr_ordem;";

		$result = $this->db->query($qr_sql);
	}

	function grafico_configura_rotulo_salvar(&$result, $args=array())
	{
		if(intval($args["cd_caderno_cci_grafico_rotulo"]) == 0)
		{
			$qr_sql = "
				INSERT INTO gestao.caderno_cci_grafico_rotulo
				     (
				       cd_caderno_cci_grafico,
                       ds_caderno_cci_grafico_rotulo,
                       nr_ordem,
                       cor,
                       cd_caderno_cci_grafico_rotulo_referencia,
                       cd_usuario_inclusao, 
                       cd_usuario_alteracao
                     )
				VALUES
				     (
				     	".(trim($args["cd_caderno_cci_grafico"]) != "" ? intval($args["cd_caderno_cci_grafico"]) : "DEFAULT").",
				     	".(trim($args["ds_caderno_cci_grafico_rotulo"]) != "" ? str_escape($args["ds_caderno_cci_grafico_rotulo"]) : "DEFAULT").",
				     	".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
				        ".(trim($args["cor"]) != "" ? str_escape($args["cor"]) : "DEFAULT").",
				        ".(((isset($args["cd_caderno_cci_grafico_rotulo_referencia"])) AND (intval($args["cd_caderno_cci_grafico_rotulo_referencia"]) > 0)) ? intval($args["cd_caderno_cci_grafico_rotulo_referencia"]) : "DEFAULT").",
						".intval($args["cd_usuario"]).",
						".intval($args["cd_usuario"])."
					 );";
		}
		else
		{		
			$qr_sql = "
				UPDATE gestao.caderno_cci_grafico_rotulo
				   SET ds_caderno_cci_grafico_rotulo = ".(trim($args["ds_caderno_cci_grafico_rotulo"]) != "" ? str_escape($args["ds_caderno_cci_grafico_rotulo"]) : "DEFAULT").",
				       nr_ordem                        = ".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
				       cor                             = ".(trim($args["cor"]) != "" ? str_escape($args["cor"]) : "DEFAULT").",
					   cd_usuario_alteracao            = ".intval($args["cd_usuario"]).",
					   dt_alteracao                    = CURRENT_TIMESTAMP
				 WHERE cd_caderno_cci_grafico_rotulo = ".intval($args["cd_caderno_cci_grafico_rotulo"]).";";
		}
	
		$this->db->query($qr_sql);
	}

	function grafico_configura_rotulo_excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_grafico_rotulo
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
		     WHERE cd_caderno_cci_grafico_rotulo = ".intval($args["cd_caderno_cci_grafico_rotulo"]).";";
			 
		$this->db->query($qr_sql);
	}

	function grafico_configura_agrupamento_salvar(&$result, $args=array())
	{
		if(intval($args["cd_caderno_cci_grafico_agrupamento"]) == 0)
		{
			$qr_sql = "
				INSERT INTO gestao.caderno_cci_grafico_agrupamento
				     (
				       cd_caderno_cci_grafico,
                       ds_caderno_cci_grafico_agrupamento,
                       nr_ordem,
                       agrupamento,
                       cd_caderno_cci_grafico_agrupamento_referencia,
                       cd_usuario_inclusao, 
                       cd_usuario_alteracao
                     )
				VALUES
				     (
				     	".(trim($args["cd_caderno_cci_grafico"]) != "" ? intval($args["cd_caderno_cci_grafico"]) : "DEFAULT").",
				     	".(trim($args["ds_caderno_cci_grafico_agrupamento"]) != "" ? str_escape($args["ds_caderno_cci_grafico_agrupamento"]) : "DEFAULT").",
				     	".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
				        ".(((isset($args["agrupamento"])) AND (trim($args["agrupamento"]) != "")) ? str_escape($args["agrupamento"]) : "DEFAULT").",
				        ".(((isset($args["cd_caderno_cci_grafico_agrupamento_referencia"])) AND (intval($args["cd_caderno_cci_grafico_agrupamento_referencia"]) > 0)) ? intval($args["cd_caderno_cci_grafico_agrupamento_referencia"]) : "DEFAULT").",
						".intval($args["cd_usuario"]).",
						".intval($args["cd_usuario"])."
					 );";
		}
		else
		{		
			$qr_sql = "
				UPDATE gestao.caderno_cci_grafico_agrupamento
				   SET ds_caderno_cci_grafico_agrupamento = ".(trim($args["ds_caderno_cci_grafico_agrupamento"]) != "" ? str_escape($args["ds_caderno_cci_grafico_agrupamento"]) : "DEFAULT").",
				       nr_ordem                           = ".(trim($args["nr_ordem"]) != "" ? intval($args["nr_ordem"]) : "DEFAULT").",
				       agrupamento                        = ".(((isset($args["agrupamento"])) AND (trim($args["agrupamento"]) != "")) ? str_escape($args["agrupamento"]) : "DEFAULT").",
					   cd_usuario_alteracao               = ".intval($args["cd_usuario"]).",
					   dt_alteracao                       = CURRENT_TIMESTAMP
				 WHERE cd_caderno_cci_grafico_agrupamento = ".intval($args["cd_caderno_cci_grafico_agrupamento"]).";";
		}
	
		$this->db->query($qr_sql);
	}

	function grafico_agrupamento_listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_grafico,
				   cd_caderno_cci_grafico_agrupamento,
                   ds_caderno_cci_grafico_agrupamento,
                   nr_ordem,
                   agrupamento
			  FROM gestao.caderno_cci_grafico_agrupamento
			 WHERE cd_caderno_cci_grafico = ".intval($args["cd_caderno_cci_grafico"])."
			   AND dt_exclusao IS NULL
			 ORDER BY nr_ordem;";

		$result = $this->db->query($qr_sql);
	}

	function grafico_agrupamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_caderno_cci_grafico,
				   cd_caderno_cci_grafico_agrupamento,
                   ds_caderno_cci_grafico_agrupamento,
                   nr_ordem,
                   agrupamento
			  FROM gestao.caderno_cci_grafico_agrupamento
			 WHERE cd_caderno_cci_grafico_agrupamento = ".intval($args["cd_caderno_cci_grafico_agrupamento"]).";";

		$result = $this->db->query($qr_sql);
	}

	function grafico_configura_agrupamento_excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.caderno_cci_grafico_agrupamento
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args["cd_usuario"])."
		     WHERE cd_caderno_cci_grafico_agrupamento = ".intval($args["cd_caderno_cci_grafico_agrupamento"]).";";
			 
		$this->db->query($qr_sql);
	}

	public function estrutura_oracle($cd_caderno_cci, $cd_caderno_cci_estrutura)
	{
		$qr_sql = "
			SELECT seq_estrutura AS value,
			       ds_estrutura AS text 
			  FROM st_segmento_cader_cci s
			  JOIN st_estrutura_seg_cad_cci e
			    ON e.seq_segmento = s.seq_segmento
			 WHERE seq_estrutura NOT IN (SELECT cce.seq_estrutura
			                               FROM gestao.caderno_cci_estrutura cce
			                              WHERE cce.dt_exclusao IS NULL
			                                AND cce.seq_estrutura IS NOT NULL
			                                AND cce.cd_caderno_cci = ".intval($cd_caderno_cci)."
			                                AND cce.cd_caderno_cci_estrutura != ".intval($cd_caderno_cci_estrutura).");";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_valor_oracle($seq_estrutura, $nr_mes, $nr_ano)
	{
		$qr_sql = "
			SELECT c.ds_campo, 
			       v.vl_cad_mes
			  FROM st_segmento_cader_cci s 
			  JOIN st_estrutura_seg_cad_cci e 
			    ON s.seq_segmento = e.seq_segmento
			  JOIN st_campos_estrutura_seg c
			    ON e.seq_estrutura = c.seq_estrutura
			  JOIN st_cad_cci_mes m 
			    ON m.seq_segmento = s.seq_segmento
			  JOIN st_vl_cad_cci v
			    ON v.seq_cad_mes = m.seq_cad_mes
			   AND v.seq_campo = c.seq_campo
			 WHERE m.dt_confirma IS NOT NULL
			   AND e.seq_estrutura            = ".intval($seq_estrutura)."
			   AND TO_CHAR(m.mes_ano, 'YYYY') = '".trim($nr_ano)."'
			   AND TO_CHAR(m.mes_ano, 'MM')   = '".trim($nr_mes)."';";

		return $this->db->query($qr_sql)->result_array();
	}
}