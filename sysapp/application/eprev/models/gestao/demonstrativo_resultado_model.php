<?php
class Demonstrativo_resultado_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function get_ano()
	{
		$qr_sql = "
			SELECT nr_ano AS value,
				   nr_ano AS text
			  FROM gestao.demonstrativo_resultado
			 WHERE dt_exclusao IS NULL
			 ORDER BY nr_ano DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_ano_demostrativo_fechado()
	{
		$qr_sql = "
			SELECT dr.nr_ano AS value,
				   dr.nr_ano AS text
			  FROM gestao.demonstrativo_resultado dr
			 WHERE dr.dt_exclusao IS NULL
			   AND (SELECT COUNT(*)
			          FROM gestao.demonstrativo_resultado_mes drm
			         WHERE drm.dt_exclusao IS NULL
			           AND drm.dt_fechamento IS NOT NULL
			           AND drm.cd_demonstrativo_resultado = dr.cd_demonstrativo_resultado) > 0
			 ORDER BY dr.nr_ano DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT dr.cd_demonstrativo_resultado,
				   dr.nr_ano				   
			  FROM gestao.demonstrativo_resultado dr
			 WHERE dr.dt_exclusao IS NULL
			   ".(intval($args['nr_ano']) > 0 ? "AND dr.nr_ano = ".intval($args['nr_ano']) : "").";";
			   
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_demonstrativo_resultado)
	{
		$qr_sql = "
			SELECT dr.cd_demonstrativo_resultado,
				   dr.nr_ano
			  FROM gestao.demonstrativo_resultado dr
			 WHERE dr.cd_demonstrativo_resultado = ".intval($cd_demonstrativo_resultado).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_ano_anterior()
	{
		$qr_sql = "
			SELECT cd_demonstrativo_resultado
			  FROM gestao.demonstrativo_resultado
			 WHERE dt_exclusao IS NULL
			 ORDER BY nr_ano DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}	

	public function salvar($args = array())
	{
		$cd_demonstrativo_resultado = intval($this->db->get_new_id('gestao.demonstrativo_resultado', 'cd_demonstrativo_resultado'));

		$qr_sql = "
			INSERT INTO gestao.demonstrativo_resultado
			     (
			       cd_demonstrativo_resultado,
			       nr_ano,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_demonstrativo_resultado).",
                    ".intval($args['nr_ano']).",
                    ".intval($args['cd_usuario']).",
			        ".intval($args['cd_usuario'])."
			     );";
				 
		$this->db->query($qr_sql);

		return $cd_demonstrativo_resultado;
	}

	public function atualizar($cd_demonstrativo_resultado, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado
               SET nr_ano				= ".intval($args['nr_ano']).",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_demonstrativo_resultado = ".intval($cd_demonstrativo_resultado).";";

        $this->db->query($qr_sql);  
	}

	public function get_referencia_pai($cd_demonstrativo_resultado_estrutura_referencia)
	{
		$qr_sql = "
			SELECT cd_demonstrativo_resultado_estrutura
			  FROM gestao.demonstrativo_resultado_estrutura
			 WHERE cd_demonstrativo_resultado_estrutura_referencia = ".intval($cd_demonstrativo_resultado_estrutura_referencia).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function atualizar_estrutura_pai($cd_demonstrativo_resultado_estrutura, $cd_usuario, $cd_demonstrativo_resultado_estrutura_pai)
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado_estrutura
               SET cd_demonstrativo_resultado_estrutura_pai = ".(trim($cd_demonstrativo_resultado_estrutura_pai) > 0 ? intval($cd_demonstrativo_resultado_estrutura_pai) : "DEFAULT").",
               	   cd_usuario_alteracao                     = ".intval($cd_usuario).",
                   dt_alteracao                             = CURRENT_TIMESTAMP
             WHERE cd_demonstrativo_resultado_estrutura = ".intval($cd_demonstrativo_resultado_estrutura).";";

	    $this->db->query($qr_sql);  
	}

	public function excluir($cd_demonstrativo_resultado, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_demonstrativo_resultado = ".intval($cd_demonstrativo_resultado).";";

        $this->db->query($qr_sql);  
	}

	public function get_gerencia()
	{
		$qr_sql = "
			SELECT codigo AS value,
			       nome AS text 
			  FROM funcoes.get_gerencias_vigente();";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_tipo()
	{
		$qr_sql = "
			SELECT cd_demonstrativo_resultado_estrutura_tipo AS value,
			       ds_demonstrativo_resultado_estrutura_tipo AS text
			  FROM gestao.demonstrativo_resultado_estrutura_tipo
			 WHERE dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_estrutura($cd_demonstrativo_resultado_estrutura)
	{
		$qr_sql = "
			SELECT dre.cd_demonstrativo_resultado_estrutura,
				   dre.cd_demonstrativo_resultado,
				   dre.cd_demonstrativo_resultado_estrutura_pai,
				   dre.ds_demonstrativo_resultado_estrutura,
				   TO_CHAR(dre.dt_desativado, 'DD/MM/YYYY HH24:MI:SS') AS dt_desativado,
				   funcoes.get_usuario_nome(dre.cd_usuario_desativado) AS ds_usuario_desativado,
				   dre.nr_ordem,
				   dre.cd_demonstrativo_resultado_estrutura_tipo,
				   dre.cd_gerencia,
				   dre.cd_usuario_responsavel,
				   dre.cd_usuario_substituto   
			  FROM gestao.demonstrativo_resultado_estrutura dre
			 WHERE dre.dt_exclusao IS NULL
			   AND dre.cd_demonstrativo_resultado_estrutura = ".intval($cd_demonstrativo_resultado_estrutura).";";
		   
		return $this->db->query($qr_sql)->row_array();
	}

	public function get_usuario($cd_divisao)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($cd_divisao)."')";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_estrutura($args = array())
	{
		$cd_demonstrativo_resultado_estrutura = intval($this->db->get_new_id('gestao.demonstrativo_resultado_estrutura', 'cd_demonstrativo_resultado_estrutura'));

		$qr_sql = "
			INSERT INTO gestao.demonstrativo_resultado_estrutura
			     (
			       	cd_demonstrativo_resultado_estrutura,
			       	cd_demonstrativo_resultado,
			       	cd_demonstrativo_resultado_estrutura_referencia,
					cd_demonstrativo_resultado_estrutura_pai,
					ds_demonstrativo_resultado_estrutura,
					nr_ordem,
					cd_demonstrativo_resultado_estrutura_tipo,
					cd_gerencia,
					cd_usuario_responsavel,
					cd_usuario_substituto,
					cd_usuario_desativado,
					dt_desativado,
					cd_usuario_inclusao,
			       	cd_usuario_alteracao

			     )
			VALUES
			     (
			     	".intval($cd_demonstrativo_resultado_estrutura).",
			     	".(trim($args['cd_demonstrativo_resultado']) != '' ? intval($args['cd_demonstrativo_resultado']) : "DEFAULT").",
			     	".(trim($args['cd_demonstrativo_resultado_estrutura_referencia']) != '' ? intval($args['cd_demonstrativo_resultado_estrutura_referencia']) : "DEFAULT").",
                	".(trim($args['cd_demonstrativo_resultado_estrutura_pai']) != '' ? intval($args['cd_demonstrativo_resultado_estrutura_pai']) : "DEFAULT").",
                	".(trim($args['ds_demonstrativo_resultado_estrutura']) != '' ? str_escape($args['ds_demonstrativo_resultado_estrutura']) : "DEFAULT").",
                	".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
                	".(trim($args['cd_demonstrativo_resultado_estrutura_tipo']) != '' ? intval($args['cd_demonstrativo_resultado_estrutura_tipo']) : "DEFAULT").",
                	".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
                	".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",              	
                	".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "DEFAULT").",              	
                	".(trim($args['cd_usuario_desativado']) != '' ? intval($args['cd_usuario_desativado']) : "DEFAULT").",              	
                	".(trim($args['dt_desativado']) != '' ? "'".trim($args['dt_desativado'])."'" : "DEFAULT").",              	
                	".intval($args['cd_usuario']).",
			        ".intval($args['cd_usuario'])."
			     );";
			 
		$this->db->query($qr_sql);

		return $cd_demonstrativo_resultado_estrutura;
	}

	public function atualizar_estrutura($cd_demonstrativo_resultado_estrutura, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado_estrutura
               SET cd_demonstrativo_resultado_estrutura_pai  = ".(trim($args['cd_demonstrativo_resultado_estrutura_pai']) != '' ? intval($args['cd_demonstrativo_resultado_estrutura_pai']) :"DEFAULT").",
               	   ds_demonstrativo_resultado_estrutura      = ".(trim($args['ds_demonstrativo_resultado_estrutura']) != '' ? str_escape($args['ds_demonstrativo_resultado_estrutura']) : "DEFAULT").",
				   nr_ordem                                  = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
			       cd_demonstrativo_resultado_estrutura_tipo = ".(trim($args['cd_demonstrativo_resultado_estrutura_tipo']) != '' ? intval($args['cd_demonstrativo_resultado_estrutura_tipo']) : "DEFAULT").",
				   cd_gerencia                               = ".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
				   cd_usuario_responsavel                    = ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
				   cd_usuario_substituto                     = ".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "DEFAULT").",
				   cd_usuario_alteracao                      = ".intval($args['cd_usuario']).",
                   dt_alteracao                              = CURRENT_TIMESTAMP
             WHERE cd_demonstrativo_resultado_estrutura = ".intval($cd_demonstrativo_resultado_estrutura).";";

        $this->db->query($qr_sql);  
	}

	public function listar_estrutura_pai($cd_demonstrativo_resultado)
	{
		$qr_sql = "
			SELECT dre.cd_demonstrativo_resultado_estrutura,
				   dre.cd_demonstrativo_resultado,
				   dre.cd_demonstrativo_resultado_estrutura_pai,
				   dre.ds_demonstrativo_resultado_estrutura,
				   dre.nr_ordem,
				   TO_CHAR(dre.dt_desativado, 'DD/MM/YYYY HH24:MI:SS') AS dt_desativado,
				   funcoes.get_usuario_nome(dre.cd_usuario_desativado) AS ds_usuario_desativado,
				   dre.cd_usuario_responsavel,
				   dre.cd_usuario_substituto,
				   dre.cd_gerencia,
				   dre.cd_demonstrativo_resultado_estrutura_tipo,
				   dret.ds_demonstrativo_resultado_estrutura_tipo,
				   d.nome AS ds_gerencia,
				   funcoes.get_usuario_nome(dre.cd_usuario_responsavel) AS ds_usuario_responsavel,
				   funcoes.get_usuario_nome(dre.cd_usuario_substituto) AS ds_usuario_substituto,
				   0 AS nivel,
				   'S' AS fl_pai   
			  FROM gestao.demonstrativo_resultado_estrutura dre
			  JOIN gestao.demonstrativo_resultado_estrutura_tipo dret
			    ON dret.cd_demonstrativo_resultado_estrutura_tipo = dre.cd_demonstrativo_resultado_estrutura_tipo
			  LEFT JOIN projetos.divisoes d
			    ON d.codigo = dre.cd_gerencia
			 WHERE dre.dt_exclusao IS NULL
			   AND dre.cd_demonstrativo_resultado_estrutura_pai IS NULL
			   AND dre.cd_demonstrativo_resultado = ".intval($cd_demonstrativo_resultado)."
			 ORDER BY dre.nr_ordem ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_estrutura($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_estrutura = 0)
	{
		$qr_sql = "
			SELECT dre.cd_demonstrativo_resultado_estrutura,
				   dre.cd_demonstrativo_resultado,
				   dre.cd_demonstrativo_resultado_estrutura_pai,
				   dre.ds_demonstrativo_resultado_estrutura,
				   dre.nr_ordem,
				   dre.cd_demonstrativo_resultado_estrutura_tipo,
				   TO_CHAR(dre.dt_desativado, 'DD/MM/YYYY HH24:MI:SS') AS dt_desativado,
				   funcoes.get_usuario_nome(dre.cd_usuario_desativado) AS ds_usuario_desativado,
				   dre.cd_usuario_responsavel,
				   dre.cd_usuario_substituto,
				   dre.cd_gerencia,				   
				   (SELECT dre1.cd_demonstrativo_resultado_estrutura_pai
					  FROM gestao.demonstrativo_resultado_estrutura dre1
					 WHERE dre.cd_demonstrativo_resultado_estrutura_referencia = dre1.cd_demonstrativo_resultado_estrutura
				   ) AS cd_referencia,
				   dret.ds_demonstrativo_resultado_estrutura_tipo,
				   d.nome AS ds_gerencia,
				   dre.dt_desativado,
				   dre.cd_usuario_desativado,
				   0 AS nivel,
				   funcoes.get_usuario_nome(dre.cd_usuario_responsavel) AS ds_usuario_responsavel,
				   funcoes.get_usuario_nome(dre.cd_usuario_substituto) AS ds_usuario_substituto,
				   (CASE WHEN (SELECT COUNT(*)
				                 FROM gestao.demonstrativo_resultado_estrutura dre1
				                WHERE dre1.dt_exclusao IS NULL
				                  AND dre1.cd_demonstrativo_resultado_estrutura_pai = dre.cd_demonstrativo_resultado_estrutura) > 0 
				         THEN 'S'
				         ELSE 'N'
				   END) AS fl_pai
			  FROM gestao.demonstrativo_resultado_estrutura dre
			  LEFT JOIN projetos.divisoes d
			    ON d.codigo = dre.cd_gerencia
			  JOIN gestao.demonstrativo_resultado_estrutura_tipo dret
			    ON dret.cd_demonstrativo_resultado_estrutura_tipo = dre.cd_demonstrativo_resultado_estrutura_tipo
			 WHERE dre.dt_exclusao IS NULL
			   AND dre.cd_demonstrativo_resultado = ".intval($cd_demonstrativo_resultado)."
			   ".(intval($cd_demonstrativo_resultado_estrutura) > 0 ? "AND dre.cd_demonstrativo_resultado_estrutura_pai = ".intval($cd_demonstrativo_resultado_estrutura) : '')."
			 ORDER BY dre.nr_ordem ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_estrutura_ordem($cd_demonstrativo_resultado_estrutura)
	{
		$qr_sql = "
			SELECT cd_demonstrativo_resultado_estrutura_pai,
                   nr_ordem
			  FROM gestao.demonstrativo_resultado_estrutura
			 WHERE cd_demonstrativo_resultado_estrutura = ".intval($cd_demonstrativo_resultado_estrutura).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function excluir_estrutura($cd_demonstrativo_resultado_estrutura, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado_estrutura
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_demonstrativo_resultado_estrutura = ".intval($cd_demonstrativo_resultado_estrutura).";";

        $this->db->query($qr_sql); 
	}

	public function desativar_estrutura($cd_demonstrativo_resultado_estrutura, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado_estrutura
               SET cd_usuario_desativado = ".intval($cd_usuario).",
                   dt_desativado         = CURRENT_TIMESTAMP
             WHERE cd_demonstrativo_resultado_estrutura = ".intval($cd_demonstrativo_resultado_estrutura).";";

        $this->db->query($qr_sql); 
	}

	public function ativar_estrutura($cd_demonstrativo_resultado_estrutura)
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado_estrutura
               SET cd_usuario_desativado = NULL,
                   dt_desativado         = NULL
             WHERE cd_demonstrativo_resultado_estrutura = ".intval($cd_demonstrativo_resultado_estrutura).";";

        $this->db->query($qr_sql); 
	}

	public function carrega_meses($cd_demonstrativo_resultado, $cd_mes)
	{
		$qr_sql = "
			SELECT drm.cd_demonstrativo_resultado_mes,
			       TO_CHAR(drm.dt_referencia, 'MM') AS cd_mes,
				   TO_CHAR(drm.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(drm.dt_fechamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_fechamento,
				   (SELECT COUNT(*)
				      FROM gestao.demonstrativo_resultado_estrutura_mes drem
				     WHERE drem.dt_exclusao IS NULL
				       AND drem.cd_demonstrativo_resultado_mes = drm.cd_demonstrativo_resultado_mes
				       AND drem.cd_demonstrativo_resultado_estrutura_tipo != 2) AS qt_item,
				   (SELECT COUNT(*)
				      FROM gestao.demonstrativo_resultado_estrutura_mes drem
				     WHERE drem.dt_exclusao IS NULL
				       AND drem.cd_demonstrativo_resultado_mes = drm.cd_demonstrativo_resultado_mes
				       AND drem.cd_demonstrativo_resultado_estrutura_tipo != 2
				       AND drem.dt_fechamento IS NOT NULL
				       AND (SELECT COUNT(*)
					          FROM gestao.demonstrativo_resultado_estrutura_mes_anexo drema
					         WHERE drema.dt_exclusao IS NULL
					           AND drema.cd_demonstrativo_resultado_estrutura_mes = drem.cd_demonstrativo_resultado_estrutura_mes) > 0
				   ) AS qt_anexo,
				   drm.arquivo,
				   drm.arquivo_nome
			  FROM gestao.demonstrativo_resultado_mes drm
			 WHERE drm.dt_exclusao IS NULL
			   AND drm.cd_demonstrativo_resultado   = ".intval($cd_demonstrativo_resultado)."
			   AND TO_CHAR(drm.dt_referencia, 'MM') = ".str_escape($cd_mes).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function carrega_resultado_mes($cd_demonstrativo_resultado_mes)
	{
		$qr_sql = "
			SELECT drm.cd_demonstrativo_resultado_mes,
			       TO_CHAR(drm.dt_referencia, 'MM') AS cd_mes,
			       TO_CHAR(drm.dt_referencia, 'YYYY') AS ano,
				   TO_CHAR(drm.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(drm.dt_fechamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_fechamento,
				   TO_CHAR(funcoes.dia_util('ANTES', (DATE_TRUNC('month', drm.dt_inclusao::date) + '2 month'::interval - '1 day'::interval)::date, 4), 'DD/MM/YYYY') AS dt_limite,
				   (SELECT COUNT(*)
				      FROM gestao.demonstrativo_resultado_estrutura_mes drem
				     WHERE drem.dt_exclusao IS NULL
				       AND drem.cd_demonstrativo_resultado_mes = drm.cd_demonstrativo_resultado_mes
				       AND drem.cd_demonstrativo_resultado_estrutura_tipo != 2) AS qt_item,
				   (SELECT COUNT(*)
				      FROM gestao.demonstrativo_resultado_estrutura_mes drem
				     WHERE drem.dt_exclusao IS NULL
				       AND drem.cd_demonstrativo_resultado_mes = drm.cd_demonstrativo_resultado_mes
				       AND drem.cd_demonstrativo_resultado_estrutura_tipo != 2
				       AND drem.dt_fechamento IS NOT NULL
				       AND (SELECT COUNT(*)
					          FROM gestao.demonstrativo_resultado_estrutura_mes_anexo drema
					         WHERE drema.dt_exclusao IS NULL
					           AND drema.cd_demonstrativo_resultado_estrutura_mes = drem.cd_demonstrativo_resultado_estrutura_mes) > 0
				   ) AS qt_anexo
			  FROM gestao.demonstrativo_resultado_mes drm
			 WHERE drm.dt_exclusao IS NULL
			   AND drm.cd_demonstrativo_resultado_mes = ".intval($cd_demonstrativo_resultado_mes).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_mes($args = array())
	{
		$cd_demonstrativo_resultado_mes = intval($this->db->get_new_id('gestao.demonstrativo_resultado_mes', 'cd_demonstrativo_resultado_mes'));

		$qr_sql = "
			INSERT INTO gestao.demonstrativo_resultado_mes
			     (
			       cd_demonstrativo_resultado_mes,
			       cd_demonstrativo_resultado,
			       dt_referencia,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_demonstrativo_resultado_mes).",
			     	".(trim($args['cd_demonstrativo_resultado']) != '' ? intval($args['cd_demonstrativo_resultado']) : "DEFAULT").",
                    ".(trim($args['dt_referencia']) != '' ? "TO_TIMESTAMP('".trim($args['dt_referencia'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
			        ".intval($args['cd_usuario'])."
			     );";
				 
		$this->db->query($qr_sql);

		return $cd_demonstrativo_resultado_mes;
	}

	public function salvar_estrutura_mes($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_mes, $cd_usuario)
	{
		$qr_sql = "
			INSERT INTO gestao.demonstrativo_resultado_estrutura_mes
			     (
			        cd_demonstrativo_resultado, 
            		cd_demonstrativo_resultado_estrutura, 
            		cd_demonstrativo_resultado_mes, 
            		ds_demonstrativo_resultado_estrutura, 
            		cd_demonstrativo_resultado_estrutura_pai, 
            		nr_ordem, 
            		cd_demonstrativo_resultado_estrutura_tipo, 
            		cd_gerencia, 
            		cd_usuario_responsavel, 
            		cd_usuario_substituto, 
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao
			     )
			SELECT cd_demonstrativo_resultado, 
            	   cd_demonstrativo_resultado_estrutura, 
            	   ".intval($cd_demonstrativo_resultado_mes).", 
            	   ds_demonstrativo_resultado_estrutura, 
            	   cd_demonstrativo_resultado_estrutura_pai, 
            	   nr_ordem, 
            	   cd_demonstrativo_resultado_estrutura_tipo, 
            	   cd_gerencia, 
            	   cd_usuario_responsavel, 
            	   cd_usuario_substituto, 
            	   ".$cd_usuario.", 
            	   ".$cd_usuario."
			  FROM gestao.demonstrativo_resultado_estrutura
			 WHERE dt_exclusao   IS NULL
			   AND dt_desativado IS NULL
			   AND cd_demonstrativo_resultado = ".intval($cd_demonstrativo_resultado).";";

		$this->db->query($qr_sql);	  
	}

	public function listar_responsavel($cd_demonstrativo_resultado_mes)
	{
		$qr_sql = "
			SELECT funcoes.get_usuario(drem.cd_usuario_responsavel) AS ds_usuario,
			       drem.cd_usuario_responsavel AS cd_usuario
			  FROM gestao.demonstrativo_resultado_estrutura_mes drem
			 WHERE drem.cd_demonstrativo_resultado_mes = ".intval($cd_demonstrativo_resultado_mes)."
			   AND drem.dt_exclusao IS NULL
			   AND drem.cd_demonstrativo_resultado_estrutura_tipo != 2
			 GROUP BY ds_usuario, 
			          drem.cd_usuario_responsavel;";
	
		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_substituto($cd_demonstrativo_resultado_mes)
	{
		$qr_sql = "
			SELECT funcoes.get_usuario(drem.cd_usuario_substituto) AS ds_usuario,
			       drem.cd_usuario_substituto AS cd_usuario
			  FROM gestao.demonstrativo_resultado_estrutura_mes drem
			 WHERE drem.cd_demonstrativo_resultado_mes = ".intval($cd_demonstrativo_resultado_mes)."
			   AND drem.dt_exclusao IS NULL
			   AND drem.cd_demonstrativo_resultado_estrutura_tipo != 2
			 GROUP BY ds_usuario, 
			          drem.cd_usuario_substituto;";
	
		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_estrutura_mes_usuario($cd_demonstrativo_resultado_mes, $cd_usuario, $fl_tipo = 'R')
	{
		$qr_sql = "
			SELECT drem.ds_demonstrativo_resultado_estrutura
			  FROM gestao.demonstrativo_resultado_estrutura_mes drem
			 WHERE drem.cd_demonstrativo_resultado_mes = ".intval($cd_demonstrativo_resultado_mes)."
			   AND drem.dt_exclusao IS NULL
			   AND drem.cd_demonstrativo_resultado_estrutura_tipo != 2
			   ".(trim($fl_tipo) == 'R' ? "AND drem.cd_usuario_responsavel =".intval($cd_usuario) : "")."
			   ".(trim($fl_tipo) == 'S' ? "AND drem.cd_usuario_substituto =".intval($cd_usuario) : "").";";
	
		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_estrutura_mes_pai($cd_demonstrativo_resultado_mes)
	{
		$qr_sql = "
			SELECT dre.cd_demonstrativo_resultado_estrutura_mes,
			       dre.cd_demonstrativo_resultado_estrutura,
				   dre.cd_demonstrativo_resultado,
				   dre.cd_demonstrativo_resultado_estrutura_pai,
				   dre.ds_demonstrativo_resultado_estrutura,
				   dre.nr_ordem,
				   dre.cd_usuario_responsavel,
				   dre.cd_usuario_substituto,
				   dre.cd_gerencia,
				   dre.cd_demonstrativo_resultado_estrutura_tipo,
				   dret.ds_demonstrativo_resultado_estrutura_tipo,
				   d.nome AS ds_gerencia,
				   funcoes.get_usuario_nome(dre.cd_usuario_responsavel) AS ds_usuario_responsavel,
				   funcoes.get_usuario_nome(dre.cd_usuario_substituto) AS ds_usuario_substituto,
				   0 AS nivel		   
			  FROM gestao.demonstrativo_resultado_estrutura_mes dre
			  JOIN gestao.demonstrativo_resultado_estrutura_tipo dret
			    ON dret.cd_demonstrativo_resultado_estrutura_tipo = dre.cd_demonstrativo_resultado_estrutura_tipo
			  LEFT JOIN projetos.divisoes d
			    ON d.codigo = dre.cd_gerencia
			 WHERE dre.dt_exclusao IS NULL
			   AND dre.cd_demonstrativo_resultado_estrutura_pai IS NULL
			   AND dre.cd_demonstrativo_resultado_mes = ".intval($cd_demonstrativo_resultado_mes)."
			 ORDER BY dre.nr_ordem ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_estrutura_mes($cd_demonstrativo_resultado, $cd_demonstrativo_resultado_mes, $cd_demonstrativo_resultado_estrutura = 0)
	{
		$qr_sql = "
			SELECT dre.cd_demonstrativo_resultado_estrutura_mes,
			       dre.cd_demonstrativo_resultado_estrutura,
				   dre.cd_demonstrativo_resultado,
				   dre.cd_demonstrativo_resultado_estrutura_pai,
				   dre.ds_demonstrativo_resultado_estrutura,
				   dre.nr_ordem,
				   dre.cd_demonstrativo_resultado_estrutura_tipo,
				   dre.cd_usuario_responsavel,
				   dre.cd_usuario_substituto,
				   dre.cd_gerencia,				   
				   dret.ds_demonstrativo_resultado_estrutura_tipo,
				   d.nome AS ds_gerencia,
				   0 AS nivel,
				   funcoes.get_usuario_nome(dre.cd_usuario_responsavel) AS ds_usuario_responsavel,
				   funcoes.get_usuario_nome(dre.cd_usuario_substituto) AS ds_usuario_substituto,
				   dre.arquivo,
				   dre.arquivo_nome,
				   TO_CHAR(dre.dt_fechamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_fechamento,
    	           funcoes.get_usuario_nome(dre.cd_usuario_fechamento) AS ds_usuario
			  FROM gestao.demonstrativo_resultado_estrutura_mes dre
			  LEFT JOIN projetos.divisoes d
			    ON d.codigo = dre.cd_gerencia
			  JOIN gestao.demonstrativo_resultado_estrutura_tipo dret
			    ON dret.cd_demonstrativo_resultado_estrutura_tipo = dre.cd_demonstrativo_resultado_estrutura_tipo
			 WHERE dre.dt_exclusao IS NULL
			   AND dre.cd_demonstrativo_resultado     = ".intval($cd_demonstrativo_resultado)."
			   AND dre.cd_demonstrativo_resultado_mes = ".intval($cd_demonstrativo_resultado_mes)."
			   ".(intval($cd_demonstrativo_resultado_estrutura) > 0 ? "AND dre.cd_demonstrativo_resultado_estrutura_pai = ".intval($cd_demonstrativo_resultado_estrutura) : '')."
			 ORDER BY dre.nr_ordem ASC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_estrutura_mes_ordem($cd_demonstrativo_resultado_estrutura, $cd_demonstrativo_resultado_mes)
	{
		$qr_sql = "
			SELECT cd_demonstrativo_resultado_estrutura_pai,
                   nr_ordem
			  FROM gestao.demonstrativo_resultado_estrutura_mes
			 WHERE cd_demonstrativo_resultado_estrutura = ".intval($cd_demonstrativo_resultado_estrutura)."
			   AND cd_demonstrativo_resultado_mes       = ".intval($cd_demonstrativo_resultado_mes).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}

	public function listar_minhas($cd_usuario, $args)
	{
		$qr_sql = "
			SELECT dr.cd_demonstrativo_resultado,
                   drm.cd_demonstrativo_resultado_mes,
                   TO_CHAR(drm.dt_referencia, 'YYYY') AS nr_ano,
                   TO_CHAR(drm.dt_referencia, 'MM') AS nr_mes,
                   TO_CHAR(drm.dt_fechamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_fechamento,
                   TO_CHAR(drm.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   TO_CHAR(funcoes.dia_util('ANTES', (DATE_TRUNC('month', drm.dt_inclusao::date) + '2 month'::interval - '1 day'::interval)::date, 4), 'DD/MM/YYYY') AS dt_limite,
                   (SELECT COUNT(*)
				      FROM gestao.demonstrativo_resultado_estrutura_mes drem
				     WHERE drem.dt_exclusao IS NULL
				       AND drem.cd_demonstrativo_resultado_mes = drm.cd_demonstrativo_resultado_mes
				       AND drem.cd_demonstrativo_resultado_estrutura_tipo != 2
				       AND (
				   			drem.cd_usuario_responsavel = ".intval($cd_usuario)."
				   			OR 
				   			drem.cd_usuario_substituto  = ".intval($cd_usuario)."
					       ) 
				   ) AS qt_item,
				   (SELECT COUNT(*)
				      FROM gestao.demonstrativo_resultado_estrutura_mes drem
				     WHERE drem.dt_exclusao IS NULL
				       AND drem.cd_demonstrativo_resultado_mes = drm.cd_demonstrativo_resultado_mes
				       AND drem.cd_demonstrativo_resultado_estrutura_tipo != 2
				       AND drem.dt_fechamento IS NOT NULL
				       AND (SELECT COUNT(*)
					          FROM gestao.demonstrativo_resultado_estrutura_mes_anexo drema
					         WHERE drema.dt_exclusao IS NULL
					           AND drema.cd_demonstrativo_resultado_estrutura_mes = drem.cd_demonstrativo_resultado_estrutura_mes) > 0
				       AND (
				   			drem.cd_usuario_responsavel = ".intval($cd_usuario)."
				   			OR 
				   			drem.cd_usuario_substituto  = ".intval($cd_usuario)."
					       ) 
				   ) AS qt_anexo
			  FROM gestao.demonstrativo_resultado_mes drm
			  JOIN gestao.demonstrativo_resultado dr
			    ON dr.cd_demonstrativo_resultado = drm.cd_demonstrativo_resultado
			 WHERE drm.dt_exclusao IS NULL
			   AND dr.dt_exclusao IS NULL
			   AND (SELECT COUNT(*)
			          FROM gestao.demonstrativo_resultado_estrutura_mes drem
			         WHERE drem.dt_exclusao IS NULL
			           AND drem.cd_demonstrativo_resultado_mes = drm.cd_demonstrativo_resultado_mes
			           AND drem.cd_demonstrativo_resultado_estrutura_tipo != 2
					   AND (
				   			drem.cd_usuario_responsavel = ".intval($cd_usuario)."
				   			OR 
				   			drem.cd_usuario_substituto  = ".intval($cd_usuario)."
					       ) ) > 0
			   ".(intval($args['nr_ano']) > 0 ? "AND TO_CHAR(drm.dt_referencia,'YYYY') = ".str_escape($args['nr_ano']) : '')."
               ".(intval($args['nr_mes']) > 0 ? "AND TO_CHAR(drm.dt_referencia,'MM') = ".str_escape($args['nr_mes']) : '')."
               ".(trim($args['fl_fechamento']) == 'S' ? "AND dt_fechamento IS NOT NULL" : '')."
               ".(trim($args['fl_fechamento']) == 'n' ? "AND dt_fechamento IS NULL" : '').";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_ordem_estrutura_anexo($cd_demonstrativo_resultado_estrutura_mes)
	{
		$qr_sql = "
			SELECT nr_ordem 
			  FROM gestao.demonstrativo_resultado_estrutura_mes_anexo
			 WHERE dt_exclusao IS NULL
			   AND cd_demonstrativo_resultado_estrutura_mes = ".intval($cd_demonstrativo_resultado_estrutura_mes)."
			 ORDER BY nr_ordem DESC
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_estrutura_anexo($args = array())
    {
        $cd_demonstrativo_resultado_estrutura_mes_anexo = intval($this->db->get_new_id('gestao.demonstrativo_resultado_estrutura_mes_anexo','cd_demonstrativo_resultado_estrutura_mes_anexo'));

		$qr_sql = "
			INSERT INTO gestao.demonstrativo_resultado_estrutura_mes_anexo
			     (
			       cd_demonstrativo_resultado_estrutura_mes_anexo,
			       cd_demonstrativo_resultado_estrutura_mes,
			       nr_ordem,
			       arquivo,
			       arquivo_nome,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_demonstrativo_resultado_estrutura_mes_anexo).",
			     	".(trim($args['cd_demonstrativo_resultado_estrutura_mes']) != '' ? intval($args['cd_demonstrativo_resultado_estrutura_mes']) : "DEFAULT").",
			     	".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem']) : "DEFAULT").",
			     	".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
			     	".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
			        ".intval($args['cd_usuario'])."
			     );";
			     
		$this->db->query($qr_sql);
	}

	public function listar_estrutura_anexo($cd_demonstrativo_resultado_estrutura_mes)
    {
    	$qr_sql = "
    	    SELECT cd_demonstrativo_resultado_estrutura_mes_anexo,
    	           cd_demonstrativo_resultado_estrutura_mes,
    	           arquivo, 
    	           arquivo_nome, 
    	           nr_ordem,
    	           TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
    	           funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario
			  FROM gestao.demonstrativo_resultado_estrutura_mes_anexo
			 WHERE cd_demonstrativo_resultado_estrutura_mes = ".intval($cd_demonstrativo_resultado_estrutura_mes)."
			   AND dt_exclusao IS NULL
    	     ORDER BY nr_ordem ASC;";

    	return $this->db->query($qr_sql)->result_array();	
    } 

    public function excluir_anexo($cd_demonstrativo_resultado_estrutura_mes_anexo, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado_estrutura_mes_anexo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_demonstrativo_resultado_estrutura_mes_anexo = ".intval($cd_demonstrativo_resultado_estrutura_mes_anexo).";";

        $this->db->query($qr_sql); 
	}

	public function alterar_ordem($cd_demonstrativo_resultado_estrutura_mes_anexo, $nr_ordem, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado_estrutura_mes_anexo
               SET nr_ordem             = ".intval($nr_ordem).",
                   cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_demonstrativo_resultado_estrutura_mes_anexo = ".intval($cd_demonstrativo_resultado_estrutura_mes_anexo).";";

        $this->db->query($qr_sql); 
	}

	public function fechar_estrutura_mes($cd_demonstrativo_resultado_estrutura_mes, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado_estrutura_mes
			   SET arquivo               = ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
			       arquivo_nome          = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
			       cd_usuario_fechamento = ".intval($args['cd_usuario']).", 
			       cd_usuario_alteracao  = ".intval($args['cd_usuario']).", 
			       dt_fechamento         = CURRENT_TIMESTAMP,
			       dt_alteracao          = CURRENT_TIMESTAMP
			 WHERE cd_demonstrativo_resultado_estrutura_mes = ".intval($cd_demonstrativo_resultado_estrutura_mes).";";

		$this->db->query($qr_sql);
	}

	public function abrir_item($cd_demonstrativo_resultado_estrutura_mes, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado_estrutura_mes
			   SET arquivo               = NULL,
			       arquivo_nome          = NULL,
			       cd_usuario_fechamento = NULL, 
			       cd_usuario_alteracao  = ".intval($cd_usuario).", 
			       dt_fechamento         = NULL,
			       dt_alteracao          = CURRENT_TIMESTAMP
			 WHERE cd_demonstrativo_resultado_estrutura_mes = ".intval($cd_demonstrativo_resultado_estrutura_mes).";";

		$this->db->query($qr_sql);
	}

	public function carrega_estrutura_mes($cd_demonstrativo_resultado_estrutura_mes)
	{
		$qr_sql = "
			SELECT cd_demonstrativo_resultado_estrutura_mes,
			       TO_CHAR(dt_fechamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_fechamento,
    	           funcoes.get_usuario_nome(cd_usuario_fechamento) AS ds_usuario,
    	           arquivo,
    	           arquivo_nome
    	      FROM gestao.demonstrativo_resultado_estrutura_mes 
    	     WHERE cd_demonstrativo_resultado_estrutura_mes = ".intval($cd_demonstrativo_resultado_estrutura_mes).";";

		return $this->db->query($qr_sql)->row_array();
	}
	
	public function fechar_resultado_mes($cd_demonstrativo_resultado_mes, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado_mes
               SET arquivo               = ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
			       arquivo_nome          = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
			       cd_usuario_fechamento = ".intval($args['cd_usuario']).", 
			       cd_usuario_alteracao  = ".intval($args['cd_usuario']).", 
                   dt_alteracao          = CURRENT_TIMESTAMP,
                   dt_fechamento         = CURRENT_TIMESTAMP
             WHERE cd_demonstrativo_resultado_mes = ".intval($cd_demonstrativo_resultado_mes).";";

	    $this->db->query($qr_sql);
	}

	public function reabrir_mes($cd_demonstrativo_resultado_mes, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_resultado_mes
               SET arquivo               = NULL,
			       arquivo_nome          = NULL,
			       cd_usuario_fechamento = NULL, 
			       cd_usuario_alteracao  = ".intval($cd_usuario).", 
                   dt_alteracao          = CURRENT_TIMESTAMP,
                   dt_fechamento         = NULL
             WHERE cd_demonstrativo_resultado_mes = ".intval($cd_demonstrativo_resultado_mes).";";

	    $this->db->query($qr_sql);
	}

	public function consulta_listar($args = array())
	{
		$qr_sql = "
			SELECT dr.cd_demonstrativo_resultado,
				   dr.nr_ano,
				   drm.arquivo,
				   drm.arquivo_nome,
				   TO_CHAR(drm.dt_referencia, 'MM') AS cd_mes
			  FROM gestao.demonstrativo_resultado_mes drm
			  JOIN gestao.demonstrativo_resultado dr
			    ON drm.cd_demonstrativo_resultado = dr.cd_demonstrativo_resultado
			 WHERE dr.dt_exclusao IS NULL
			   AND drm.dt_exclusao IS NULL
			   AND drm.dt_fechamento IS NOT NULL
			   ".(intval($args['nr_ano']) > 0 ? "AND dr.nr_ano = ".intval($args['nr_ano']) : "").";";
			   
		return $this->db->query($qr_sql)->result_array();
	}
}