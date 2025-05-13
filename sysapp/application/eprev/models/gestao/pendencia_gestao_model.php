<?php
class Pendencia_gestao_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function get_grupo_tipo_reuniao()
	{
		$qr_sql = "
			SELECT cd_reuniao_sistema_gestao_grupo AS value,
			       ds_reuniao_sistema_gestao_grupo AS text
			  FROM gestao.reuniao_sistema_gestao_grupo
			 WHERE dt_exclusao IS NULL
			 ORDER BY text;";		

		return $this->db->query($qr_sql)->result_array();
	}

    public function get_tipo_reuniao()
	{
		$qr_sql = "
			SELECT cd_reuniao_sistema_gestao_tipo AS value,
			       ds_reuniao_sistema_gestao_tipo AS text
			  FROM gestao.reuniao_sistema_gestao_tipo
			 WHERE dt_exclusao         IS NULL
			   AND fl_pendencia_gestao = 'S'
			 ORDER BY text;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_pendencia_gestao_superior()
	{
		$qr_sql = "
			SELECT pg.cd_superior AS value,
			       rp.nome AS text
			  FROM gestao.pendencia_gestao pg
			  JOIN gestao.responsavel_pendencia rp
			    ON rp.cd_responsavel = pg.cd_superior
			 GROUP BY pg.cd_superior, text
			 ORDER BY text;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_pendencia_gestao_usuario()
	{
		$qr_sql = "
			SELECT pg.cd_usuario_responsavel AS value,
				   funcoes.get_usuario_nome(pg.cd_usuario_responsavel) AS text
			  FROM gestao.pendencia_gestao pg
			 WHERE pg.dt_exclusao IS NULL
			   AND pg.cd_usuario_responsavel IS NOT NULL
			 GROUP BY pg.cd_usuario_responsavel, text
			 ORDER BY text ASC;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_usuarios($divisao)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($divisao)."')";

        return $this->db->query($qr_sql)->result_array();
    }

	public function get_pendencia_gestao_gerencia()
	{
		$qr_sql = "
			SELECT pgg.cd_gerencia AS value,
				   d.nome AS text
			  FROM gestao.pendencia_gestao_gerencia pgg
			  JOIN projetos.divisoes d
			    ON d.codigo = pgg.cd_gerencia
			 WHERE pgg.dt_exclusao IS NULL
			 GROUP BY pgg.cd_gerencia, text
			 ORDER BY text;";		

		return $this->db->query($qr_sql)->result_array();
	}

    public function listar($args = array(), $fl_comite_apuracao_resp  = false)
    {        
        $where_situacao = '';

        if(trim($args['tp_aberto']) == 'A')
        {
            $where_situacao .= " pg.dt_encerrada IS NULL AND pg.dt_implementado IS NULL AND CURRENT_DATE <= COALESCE(pg.dt_prazo_prorroga, pg.dt_prazo, CURRENT_DATE) AND pg.dt_inicio IS NULL"; 
        }

        if(trim($args['tp_atrasado']) == 'T')
        {
            if(trim($where_situacao) != '')
            {
                $where_situacao .= " OR";
            }

            $where_situacao .= " CURRENT_DATE > COALESCE(pg.dt_prazo_prorroga, pg.dt_prazo, (pg.dt_inclusao + INTERVAL '1 month')::date) AND pg.dt_encerrada IS NULL AND pg.dt_implementado IS NULL"; 
        }
		
        if(trim($args['tp_execuntado']) == 'X')
        {
            if(trim($where_situacao) != '')
            {
                $where_situacao .= " OR";
            }

            $where_situacao .= " (pg.dt_inicio IS NOT NULL AND pg.dt_implementado IS NULL  AND pg.dt_encerrada IS NULL AND CURRENT_DATE <= COALESCE(pg.dt_prazo_prorroga, pg.dt_prazo)) "; 
        }				

        if(trim($args['tp_implementado']) == 'I')
        {
            if(trim($where_situacao) != '')
            {
                $where_situacao .= " OR";
            }

            $where_situacao .= " pg.dt_encerrada IS NULL AND pg.dt_implementado IS NOT NULL"; 
        }

        if(trim($args['tp_encerrado']) == 'E')
        {
            if(trim($where_situacao) != '')
            {
                $where_situacao .= " OR";
            }

            $where_situacao .= " pg.dt_encerrada IS NOT NULL"; 
        }

        $qr_sql = "
			SELECT pg.cd_pendencia_gestao,
				   pg.cd_pendencia_gestao_reuniao,
				   rsgt.ds_reuniao_sistema_gestao_tipo,
				   pg.cd_superior,
				   pg.ds_item,
				   pg.cd_usuario_responsavel,
				   funcoes.get_usuario_area(pg.cd_usuario_responsavel) AS ds_gerencia_responsavel,
				   funcoes.get_usuario_nome(pg.cd_usuario_responsavel) AS ds_usuario_responsavel,
				   TO_CHAR(COALESCE(pg.dt_prazo_prorroga, pg.dt_prazo),'DD/MM/YYYY') AS dt_prazo,
				   TO_CHAR(pg.dt_reuniao,'DD/MM/YYYY') AS dt_reuniao,
				   pg.dt_encerrada,
				   g.nome AS ds_gerencia,
				   pg.dt_encerrada,
				   (SELECT COUNT(*)
				      FROM gestao.pendencia_gestao_anexo pga
				     WHERE pga.dt_exclusao IS NULL
				  	   AND pga.fl_cronograma = 'N'
					   AND pga.cd_pendencia_gestao = pg.cd_pendencia_gestao) AS qt_anexo,
				   (SELECT COUNT(*)
				      FROM gestao.pendencia_gestao_anexo pga
				     WHERE pga.dt_exclusao IS NULL
				  	   AND pga.fl_cronograma = 'S'
					   AND pga.cd_pendencia_gestao = pg.cd_pendencia_gestao) AS qt_cronograma,		
				   (SELECT pga.arquivo
				      FROM gestao.pendencia_gestao_anexo pga
				     WHERE pga.dt_exclusao IS NULL
				  	   AND pga.fl_cronograma = 'S'
					   AND pga.cd_pendencia_gestao = pg.cd_pendencia_gestao
					 ORDER BY pga.dt_inclusao DESC
					 LIMIT 1) AS arquivo_cronograma,					   
				   pg.arquivo,
				   pg.arquivo_nome,
				   (CASE WHEN pg.dt_encerrada IS NOT NULL
				         THEN 'Encerrado'
				         WHEN pg.dt_encerrada IS NULL AND pg.dt_implementado IS NULL AND CURRENT_DATE > COALESCE(pg.dt_prazo_prorroga, pg.dt_prazo, (pg.dt_inclusao + INTERVAL '1 month')::date)
				         THEN 'Atrasado'						 
				         WHEN pg.dt_inicio IS NOT NULL AND pg.dt_implementado IS NULL  AND pg.dt_encerrada IS NULL 
				         THEN 'Em Andamento'						 
				         WHEN pg.dt_implementado IS NOT NULL AND pg.dt_encerrada IS NULL 
				         THEN 'Implementado'
				         ELSE 'Aberto'
				   END) AS ds_status,
				   (CASE WHEN pg.dt_encerrada IS NOT NULL
				         THEN 'label'
				         WHEN pg.dt_encerrada IS NULL AND pg.dt_implementado IS NULL AND CURRENT_DATE > COALESCE(pg.dt_prazo_prorroga, pg.dt_prazo, (pg.dt_inclusao + INTERVAL '1 month')::date)
				         THEN 'label label-important'
				         WHEN pg.dt_inicio IS NOT NULL AND pg.dt_implementado IS NULL  AND pg.dt_encerrada IS NULL 
				         THEN 'label label-info'					 
				         WHEN pg.dt_implementado IS NOT NULL AND pg.dt_encerrada IS NULL 
				         THEN 'label label-success'
				         ELSE 'label label-warning'
				   END) AS ds_class_status,
				   (SELECT TO_CHAR(pga.dt_inclusao, 'DD/MM/YYYY') || ': ' || pga.ds_pendencia_gestao_acompanhamento 
		              FROM gestao.pendencia_gestao_acompanhamento pga
		             WHERE pga.cd_pendencia_gestao = pg.cd_pendencia_gestao
		               AND pga.dt_exclusao IS NULL
		             ORDER BY pga.dt_inclusao DESC
		             LIMIT 1) AS ds_acompanhamento
			  FROM gestao.pendencia_gestao pg
			  LEFT JOIN gestao.reuniao_sistema_gestao_tipo rsgt
			    ON rsgt.cd_reuniao_sistema_gestao_tipo = pg.cd_reuniao_sistema_gestao_tipo
			  LEFT JOIN gestao.responsavel_pendencia g
			    ON g.cd_responsavel = pg.cd_superior
			 WHERE pg.dt_exclusao IS NULL
			   ".(!$fl_comite_apuracao_resp ? "AND pg.cd_reuniao_sistema_gestao_tipo != 49" : "")."
			   ".(trim($args['fl_dashboard']) == "S" ? "AND COALESCE(rsgt.fl_dashboard,'N') = 'S'" : '')."
			   ".(intval($args['cd_pendencia_gestao']) > 0 ? "AND pg.cd_pendencia_gestao = ".intval($args['cd_pendencia_gestao']) : '')."
			   ".(intval($args['cd_reuniao_sistema_gestao_tipo']) > 0 ? "AND pg.cd_reuniao_sistema_gestao_tipo = ".intval($args['cd_reuniao_sistema_gestao_tipo']) : '')."
			   ".(intval($args['cd_usuario_responsavel']) > 0 ? "AND pg.cd_usuario_responsavel = ".intval($args['cd_usuario_responsavel']) : '')."
			   ".(((trim($args['dt_inicial']) != '') AND  (trim($args['dt_final']) != '')) ? " AND DATE_TRUNC('day', pg.dt_reuniao) BETWEEN TO_DATE('".$args['dt_inicial']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_final']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_prazo_inicial']) != "") AND  (trim($args['dt_prazo_final']) != "")) ? "AND DATE_TRUNC('day', pg.dt_prazo) BETWEEN TO_DATE('".$args['dt_prazo_inicial']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prazo_final']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['cd_superior']) != '' ? "AND pg.cd_superior = '".trim($args['cd_superior'])."'" : '')."
               ".(trim($where_situacao) != '' ? "AND (".$where_situacao.")" : "")."
			   ".(trim($args['cd_responsavel']) != '' ? "AND 0 < (SELECT COUNT(*)
														            FROM gestao.pendencia_gestao_gerencia pgg
														           WHERE pgg.dt_exclusao IS NULL
														             AND pgg.cd_pendencia_gestao = pg.cd_pendencia_gestao
														             AND pgg.cd_gerencia = '".trim($args['cd_responsavel'])."')" :'')."
			   ".(trim($args['cd_reuniao_sistema_gestao_grupo']) != '' ? "AND pg.cd_reuniao_sistema_gestao_tipo IN (SELECT cd_reuniao_sistema_gestao_tipo
																													  FROM gestao.reuniao_sistema_gestao_grupo_tipo
																													 WHERE dt_exclusao IS NULL
                                                                                                                       AND cd_reuniao_sistema_gestao_grupo = ".intval($args['cd_reuniao_sistema_gestao_grupo']).")" : '')."
																													   
            ORDER BY pg.cd_pendencia_gestao DESC
			";
        #echo "<PRE>$qr_sql</PRE>";exit;            
        return $this->db->query($qr_sql)->result_array();
    }

    public function get_gerencia_pendencia_gestao_gerencia($cd_pendencia_gestao)
    {
        $qr_sql = "
			SELECT pgg.cd_gerencia,
			       d.nome
			  FROM gestao.pendencia_gestao_gerencia pgg
			  JOIN projetos.divisoes d
			    ON d.codigo = pgg.cd_gerencia
			 WHERE pgg.cd_pendencia_gestao = ".intval($cd_pendencia_gestao)."
			   AND pgg.dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }
    
    public function get_responsavel_pendencia($gerencia = array())
	{
		$qr_sql = "
			SELECT cd_responsavel AS value,
				   nome AS text
			  FROM gestao.responsavel_pendencia
			  WHERE (
			 	    	dt_vigencia_fim > CURRENT_DATE 
			       		OR
			       		dt_vigencia_fim IS NULL
			       		".(count($gerencia) > 0 ? "OR cd_responsavel IN ('".implode("','", $gerencia)."')" : '')."
			       )
			 ORDER BY text;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_pendencia_gestao)
    {
        $qr_sql = "
			SELECT pg.cd_pendencia_gestao,
				   pg.cd_reuniao_sistema_gestao_tipo,
				   rsgt.ds_reuniao_sistema_gestao_tipo,
				   pg.cd_superior,
				   pg.ds_item,
				   pg.cd_usuario_responsavel,
				   pg.dt_implementado,
				   TO_CHAR(pg.dt_implementado, 'DD/MM/YYYY HH24:MI:SS') AS ds_implementado,
				   pg.cd_usuario_implementado,
				   funcoes.get_usuario_nome(pg.cd_usuario_implementado) AS ds_usuario_implementado,
				   pg.dt_inicio,
				   TO_CHAR(pg.dt_inicio, 'DD/MM/YYYY HH24:MI:SS') AS dt_executando,
				   pg.cd_usuario_inicio,
				   funcoes.get_usuario_nome(pg.cd_usuario_inicio) AS ds_usuario_executando,
				   TO_CHAR(pg.dt_reuniao, 'DD/MM/YYYY') AS dt_reuniao,
				   TO_CHAR(pg.dt_prazo, 'DD/MM/YYYY') AS dt_prazo,
				   TO_CHAR(pg.dt_prazo_prorroga, 'DD/MM/YYYY') AS dt_prazo_prorroga,
				   TO_CHAR(pg.dt_encerrada, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerrada,
				   funcoes.get_usuario_nome(pg.cd_usuario_encerrada) AS ds_usuario_encerramento,
				   funcoes.get_usuario_nome(pg.cd_usuario_responsavel) AS ds_usuario_responsavel,
				   pg.arquivo,
				   pg.arquivo_nome,
				   uc.divisao AS cd_gerencia,
				   (CASE WHEN pg.cd_usuario_responsavel > 0 THEN uc.divisao
				         WHEN (SELECT COUNT(*)
				                 FROM gestao.pendencia_gestao_gerencia pgg
				                WHERE pg.cd_pendencia_gestao = pgg.cd_pendencia_gestao
				                  AND pgg.dt_exclusao IS NULL) = 1 THEN  
																	   (SELECT pgg.cd_gerencia
																	      FROM gestao.pendencia_gestao_gerencia pgg
																	     WHERE pg.cd_pendencia_gestao = pgg.cd_pendencia_gestao
																	       AND pgg.dt_exclusao IS NULL
																	     LIMIT 1
																	   )
					ELSE '' END) AS cd_gerencia,
				   pg.cd_reuniao_sistema_gestao,
				   pg.cd_atividade,
				   pg.cd_cenario,
				   rsgt.fl_cronograma,
				   TO_CHAR(pg.dt_verificado_eficacia, 'DD/MM/YYYY') AS dt_verificado_eficacia,
				   TO_CHAR(pg.dt_verificacao_eficacia, 'DD/MM/YYYY HH24:MI:SS') AS dt_verificacao_eficacia,
				   funcoes.get_usuario_nome(pg.cd_usuario_verificacao_eficacia) AS ds_usuario_verificacao_eficacia
			  FROM gestao.pendencia_gestao pg
			  JOIN gestao.reuniao_sistema_gestao_tipo rsgt
			    ON rsgt.cd_reuniao_sistema_gestao_tipo = pg.cd_reuniao_sistema_gestao_tipo
			  LEFT JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = pg.cd_usuario_responsavel
			 WHERE pg.cd_pendencia_gestao = ".intval($cd_pendencia_gestao);

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_permissao_usuario_responsavel($cd_usuario, $gerencia_responsavel)
    {
        $qr_sql = "
			SELECT (CASE WHEN COUNT(*) > 0  THEN 'S'
				        ELSE 'N'
			       END) AS fl_permissao
		      FROM projetos.usuarios_controledi
		     WHERE codigo = ".intval($cd_usuario)."
		       AND tipo NOT IN('X', 'T', 'F')
		       AND (divisao IN ('".implode("','", $gerencia_responsavel)."') OR diretoria IN ('".implode("','", $gerencia_responsavel)."'))";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_reuniao_sistema_gestao($cd_reuniao_sistema_gestao_tipo, $dt_reuniao)
	{
		$qr_sql = "
			SELECT cd_reuniao_sistema_gestao
			  FROM gestao.reuniao_sistema_gestao
			 WHERE dt_exclusao IS NULL
			   AND cd_reuniao_sistema_gestao_tipo = ".intval($cd_reuniao_sistema_gestao_tipo)."
			   AND dt_reuniao_sistema_gestao      = TO_DATE('".$dt_reuniao."', 'DD/MM/YYYY')
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_reuniao_sistema_gestao_reuniao($cd_reuniao_sistema_gestao)
	{
		$qr_sql = "
			SELECT cd_reuniao_sistema_gestao_tipo,
				   TO_CHAR(dt_reuniao_sistema_gestao, 'DD/MM/YYYY') AS dt_reuniao
			  FROM gestao.reuniao_sistema_gestao
			 WHERE dt_exclusao               IS NULL
			   AND cd_reuniao_sistema_gestao = ".intval($cd_reuniao_sistema_gestao)."
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

    public function salvar($args = array())
    {
    	$cd_pendencia_gestao = $this->db->get_new_id('gestao.pendencia_gestao', 'cd_pendencia_gestao');

    	$qr_sql = "
            INSERT INTO gestao.pendencia_gestao
                 (
                 	cd_pendencia_gestao,
					cd_reuniao_sistema_gestao_tipo,
					cd_superior,
					dt_reuniao,
					dt_prazo,
					ds_item,
					arquivo,
					arquivo_nome,
					cd_reuniao_sistema_gestao,
					cd_usuario_responsavel,
					cd_usuario_inclusao,
					cd_usuario_alteracao
                 )
            VALUES
                 (
					".intval($cd_pendencia_gestao).",
					".intval($args['cd_reuniao_sistema_gestao_tipo']).",
					".str_escape($args['cd_superior']).",
					".(trim($args['dt_reuniao']) != '' ? "TO_DATE('".$args['dt_reuniao']."', 'DD/MM/YYYY')" : "DEFAULT").",
					".(trim($args['dt_prazo']) != '' ? "TO_DATE('".$args['dt_prazo']."', 'DD/MM/YYYY')" : "DEFAULT").",
					".(trim($args['ds_item']) != '' ? str_escape($args['ds_item']) : "DEFAULT").",
					".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
					".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
					".(intval($args['cd_reuniao_sistema_gestao']) > 0 ? intval($args['cd_reuniao_sistema_gestao']) : "DEFAULT").",
					".(intval($args['cd_usuario_responsavel']) > 0 ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
                 );";

        if(count($args['responsavel']) > 0)
        {
 			$qr_sql .= "
				INSERT INTO gestao.pendencia_gestao_gerencia(cd_pendencia_gestao, cd_gerencia, cd_usuario_inclusao)
				SELECT ".intval($cd_pendencia_gestao).", x.column1, ".intval($args['cd_usuario'])."
				  FROM (VALUES ('".implode("'),('", $args['responsavel'])."')) x;";
		}

        $this->db->query($qr_sql);

        return $cd_pendencia_gestao;
	}

	public function atualizar($cd_pendencia_gestao, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.pendencia_gestao
			   SET cd_reuniao_sistema_gestao_tipo = ".intval($args['cd_reuniao_sistema_gestao_tipo']).",
				   cd_superior                    = ".str_escape($args['cd_superior']).",
				   cd_usuario_responsavel        = ".(intval($args['cd_usuario_responsavel']) > 0 ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
				   ds_item                        = ".(trim($args['ds_item']) != '' ? str_escape($args['ds_item']) : "DEFAULT").",
				   dt_reuniao                     = ".(trim($args['dt_reuniao']) != '' ? "TO_DATE('".$args['dt_reuniao']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   dt_prazo                       = ".(trim($args['dt_prazo']) != '' ? "TO_DATE('".$args['dt_prazo']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   dt_prazo_prorroga              = ".(trim($args['dt_prazo_prorroga']) != '' ? "TO_DATE('".$args['dt_prazo_prorroga']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   cd_reuniao_sistema_gestao      = ".(intval($args['cd_reuniao_sistema_gestao']) > 0 ? intval($args['cd_reuniao_sistema_gestao']) : "DEFAULT").",
				   arquivo                        = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
				   arquivo_nome                   =	".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
				   cd_usuario_alteracao           = ".intval($args['cd_usuario']).",
				   dt_alteracao                   = CURRENT_TIMESTAMP
			 WHERE cd_pendencia_gestao = ".intval($cd_pendencia_gestao).";";

		if(count($args['responsavel']) > 0)
        {
			 $qr_sql .= "
        		UPDATE gestao.pendencia_gestao_gerencia
				   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
					   dt_exclusao         = CURRENT_TIMESTAMP
				 WHERE cd_pendencia_gestao = ".intval($cd_pendencia_gestao)."
				   AND dt_exclusao IS NULL
				   AND cd_gerencia NOT IN ('".implode("','", $args['responsavel'])."');
	   
				INSERT INTO gestao.pendencia_gestao_gerencia(cd_pendencia_gestao, cd_gerencia, cd_usuario_inclusao)
				SELECT ".intval($cd_pendencia_gestao).", x.column1, ".intval($args['cd_usuario'])."
				  FROM (VALUES ('".implode("'),('", $args['responsavel'])."')) x
				 WHERE x.column1 NOT IN (SELECT a.cd_gerencia
										   FROM gestao.pendencia_gestao_gerencia a
										  WHERE a.cd_pendencia_gestao = ".intval($cd_pendencia_gestao)."
											AND a.dt_exclusao IS NULL);";
		}
		else
		{
			$qr_sql .= "
        		UPDATE gestao.pendencia_gestao_gerencia
				   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
					   dt_exclusao         = CURRENT_TIMESTAMP
				 WHERE cd_pendencia_gestao = ".intval($cd_pendencia_gestao)."
				   AND dt_exclusao IS NULL;";
		}

		$this->db->query($qr_sql);
	}

	public function salvar_pendencia_gestao_prorrogacao($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.pendencia_gestao_prorrogacao
			     (
                	cd_pendencia_gestao, 
                	dt_pendencia_gestao_prorrogacao, 
                    cd_usuario_inclusao
                 )
            VALUES 
                 (
                 	".intval($args['cd_pendencia_gestao']).",
                 	".(trim($args['dt_pendencia_gestao_prorrogacao']) != '' ? "TO_DATE('".$args['dt_pendencia_gestao_prorrogacao']."', 'DD/MM/YYYY')" : "DEFAULT").",
                 	".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
	}

	public function get_historico_prorrogacao($cd_pendencia_gestao)
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_pendencia_gestao_prorrogacao, 'DD/MM/YYYY') AS dt_pendencia_gestao_prorrogacao,
			       TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario
			  FROM gestao.pendencia_gestao_prorrogacao
			 WHERE cd_pendencia_gestao = ".intval($cd_pendencia_gestao).";";

		return $this->db->query($qr_sql)->result_array();
	}
    
    public function encerrar($cd_pendencia_gestao, $cd_usuario)
    {
        $qr_sql = "
			UPDATE gestao.pendencia_gestao
			   SET dt_encerrada         = CURRENT_TIMESTAMP,
				   cd_usuario_encerrada = ".intval($cd_usuario)."
			 WHERE cd_pendencia_gestao = ".intval($cd_pendencia_gestao);

        $this->db->query($qr_sql);
    }

    public function lista_acompanhamento($cd_pendencia_gestao)
    {
        $qr_sql = "
			SELECT pga.ds_pendencia_gestao_acompanhamento,
			       pga.cd_usuario_inclusao,
			       (SELECT pg.dt_encerrada
			          FROM gestao.pendencia_gestao pg
			         WHERE pg.dt_exclusao IS NULL
			           AND pg.cd_pendencia_gestao = pga.cd_pendencia_gestao
			        ) AS dt_encerramento,
			        pga.cd_pendencia_gestao_acompanhamento,
				   funcoes.get_usuario_nome(pga.cd_usuario_inclusao) AS ds_usuario,
				   TO_CHAR(pga.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM gestao.pendencia_gestao_acompanhamento pga
			 WHERE pga.dt_exclusao IS NULL
			   AND pga.cd_pendencia_gestao = ".intval($cd_pendencia_gestao)."
			 ORDER BY pga.dt_inclusao DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salva_acompanhamento($args = array(), $fl_implementado = 'N', $fl_executando = 'N', $fl_verificado = 'N')
    {
        $qr_sql = "
			INSERT INTO gestao.pendencia_gestao_acompanhamento
				 (
				 	cd_pendencia_gestao,
					ds_pendencia_gestao_acompanhamento,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				 )
			VALUES
				 (
				 	".intval($args['cd_pendencia_gestao']).",
				 	".(trim($args['ds_pendencia_gestao_acompanhamento']) != '' ? str_escape($args['ds_pendencia_gestao_acompanhamento']) : "DEFAULT").",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				 );";
		
		if($fl_executando == 'S')
		{
			$qr_sql .= "
				UPDATE gestao.pendencia_gestao
				   SET cd_usuario_inicio = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
  					   dt_inicio         = CURRENT_TIMESTAMP
  				 WHERE cd_pendencia_gestao = ".intval($args['cd_pendencia_gestao']).";";
		}

		if($fl_implementado == 'S')
		{
			$qr_sql .= "
				UPDATE gestao.pendencia_gestao
				   SET cd_usuario_implementado = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
  					   dt_implementado         = CURRENT_TIMESTAMP
  				 WHERE cd_pendencia_gestao = ".intval($args['cd_pendencia_gestao']).";";
		}	

		if($fl_verificado == 'S')
		{
			$qr_sql .= "
				UPDATE gestao.pendencia_gestao
				   SET cd_usuario_verificacao_eficacia = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
				       dt_verificado_eficacia          = ".(trim($args['dt_verificado_eficacia']) != '' ? "TO_DATE('".$args['dt_verificado_eficacia']."', 'DD/MM/YYYY')" : "DEFAULT").",
  					   dt_verificacao_eficacia         = CURRENT_TIMESTAMP
  				 WHERE cd_pendencia_gestao = ".intval($args['cd_pendencia_gestao']).";";
		}	

        $this->db->query($qr_sql);
    }

    public function lista_cronograma($cd_pendencia_gestao)
    {
        $qr_sql = "
			SELECT cd_pendencia_gestao_anexo,
				   arquivo,
				   arquivo_nome,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario,
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM gestao.pendencia_gestao_anexo 
			 WHERE dt_exclusao IS NULL
			   AND cd_pendencia_gestao = ".intval($cd_pendencia_gestao)."
			   AND fl_cronograma = 'S'
		     ORDER BY dt_inclusao DESC;";
        
        return $this->db->query($qr_sql)->result_array();
    }	
	
    public function salvar_cronograma($args = array())
    {
        $qr_sql = "
			INSERT INTO gestao.pendencia_gestao_anexo
				 (
					fl_cronograma,
					cd_pendencia_gestao,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
			     	'S',
					".intval($args['cd_pendencia_gestao']).",
				 	".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
				 	".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
					".intval($args['cd_usuario'])."
				 );";
        
        $this->db->query($qr_sql);
    }
	
    public function lista_anexo($cd_pendencia_gestao)
    {
        $qr_sql = "
			SELECT cd_pendencia_gestao_anexo,
				   arquivo,
				   arquivo_nome,
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario,
				   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM gestao.pendencia_gestao_anexo 
			 WHERE dt_exclusao IS NULL
			   AND cd_pendencia_gestao = ".intval($cd_pendencia_gestao)."
			   AND fl_cronograma = 'N'
		     ORDER BY dt_inclusao DESC;";
        
        return $this->db->query($qr_sql)->result_array();
    }
    
    public function salvar_anexo($args = array())
    {
        $qr_sql = "
			INSERT INTO gestao.pendencia_gestao_anexo
				 (
					cd_pendencia_gestao,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
			     	".intval($args['cd_pendencia_gestao']).",
				 	".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
				 	".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
					".intval($args['cd_usuario'])."
				 );";
        
        $this->db->query($qr_sql);
    }
    
    public function excluir_anexo($cd_pendencia_gestao_anexo, $cd_usuario)
    {
        $qr_sql = "
			UPDATE gestao.pendencia_gestao_anexo
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_pendencia_gestao_anexo = ".intval($cd_pendencia_gestao_anexo);
        
        $this->db->query($qr_sql);
    }

    public function excluir_acompanhamento($cd_pendencia_gestao_acompanhamento, $cd_usuario)
    {
        $qr_sql = "
			UPDATE gestao.pendencia_gestao_acompanhamento
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_pendencia_gestao_acompanhamento = ".intval($cd_pendencia_gestao_acompanhamento);
        
        $this->db->query($qr_sql);
    }

    public function atualiza_implementacao_cenario_legal($cd_atividade, $cd_cenario)
	{
		$qr_sql = "
            UPDATE projetos.atividades 
               SET dt_implementacao_norma_legal = CURRENT_DATE
             WHERE numero = ".intval($cd_atividade).";";

        $this->db->query($qr_sql);

        $qr_sql = "
        	UPDATE projetos.cenario
               SET dt_implementacao = (SELECT MAX(dt_implementacao_norma_legal)
                                         FROM projetos.atividades
                                        WHERE cd_cenario = ".intval($cd_cenario).")
             WHERE cd_cenario = ".intval($cd_cenario).";";

        $this->db->query($qr_sql);
	}
}
?>