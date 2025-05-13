<?php
class Cenario_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

    public function listar($args = array())
	{
		$qr_sql = "
			SELECT ec.cd_edicao,
				   ec.tit_capa, 
				   TO_CHAR(ec.dt_edicao, 'DD/MM/YYYY HH24:MI:SS') AS dt_edicao,
				   TO_CHAR(ec.dt_envio_email, 'DD/MM/YYYY HH24:MI') AS dt_envio_email, 
				   ec.texto_capa
			  FROM projetos.edicao_cenario ec
			 WHERE ec.dt_exclusao IS NULL
			   ".(trim($args['nome']) != '' ? "AND UPPER(ec.tit_capa) LIKE UPPER('%".trim($args['nome'])."%')" : '')."
			   ".(trim($args['cd_edicao']) != '' ? "AND ec.cd_edicao = ".intval($args['cd_edicao']) : '')."
			   ".(((trim($args['dt_ini']) != '') AND (trim($args['dt_fim']) != '')) ? "AND DATE_TRUNC('day', ec.dt_edicao) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."

			  	".(trim($args['conteudo']) != '' ? "AND (
			  		SELECT COUNT(*)
		              FROM projetos.cenario c
		             WHERE c.dt_exclusao IS NULL
		               AND c.cd_edicao = ec.cd_edicao
		               AND UPPER(c.titulo) LIKE UPPER('%".trim($args['conteudo'])."%')) > 0" : '').";";
			   
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_pertinencia($cd_edicao = 0)
	{
		$qr_sql = "
			SELECT pertinencia AS value, 
			       ds_pertinencia AS text 
			  FROM projetos.cenario_pertinencia 
             WHERE 1 = 1 
               ".(intval($cd_edicao) == 0 ? "AND dt_exclusao IS NULL" : "AND (dt_exclusao IS NULL OR pertinencia = (SELECT pertinencia
                                                                                                                      FROM projetos.cenario
                                                                                                                     WHERE cd_cenario = ".intval($cd_edicao)."))").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_conteudo($cd_edicao)
	{
		$qr_sql = "
			SELECT c.cd_cenario, 
			       c.titulo, 
				   TO_CHAR(c.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(c.dt_exclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao, 
				   TO_CHAR(c.dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
				   funcoes.get_usuario_nome(c.cd_usuario_cancelamento) AS ds_usuario_cancelamento,
				   c.cd_edicao,
				   l.descricao AS ds_secao
              FROM projetos.cenario c
              LEFT JOIN listas l
                ON l.codigo = c.cd_secao
               AND categoria = 'SCEN'  
             WHERE c.dt_exclusao IS NULL
               AND c.cd_edicao = ".intval($cd_edicao).";";
			 
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carrega($cd_edicao)
	{
		$qr_sql = "
			SELECT ec.cd_edicao, 
				   TO_CHAR(ec.dt_edicao, 'DD/MM/YYYY HH24:MI') AS dt_edicao, 
				   TO_CHAR(ec.dt_exclusao, 'DD/MM/YYYY HH24:MI') AS dt_exclusao, 
				   ec.tit_capa, 
				   ec.texto_capa,
				   TO_CHAR(ec.dt_envio_email, 'DD/MM/YYYY HH24:MI') AS dt_envio_email, 
				   funcoes.get_usuario_nome(ec.cd_usuario_envio_email) AS ds_usuario_envio
			  FROM projetos.edicao_cenario ec
			 WHERE ec.cd_edicao = ".intval($cd_edicao).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}	

	public function carrega_anterior()
	{
		$qr_sql = "
			SELECT texto_capa
			  FROM projetos.edicao_cenario ec
			 WHERE ec.dt_exclusao IS NULL
			 ORDER BY dt_inclusao DESC
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
	}	

	public function salvar($args = array())
	{
		$cd_edicao = intval($this->db->get_new_id('projetos.edicao_cenario', 'cd_edicao'));

		$qr_sql = " 
			INSERT INTO projetos.edicao_cenario
				 (
				   cd_edicao,
				   tit_capa,
				   texto_capa,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
			VALUES
				 (
				   ".intval($cd_edicao).",
				   ".(trim($args['tit_capa']) != '' ? str_escape($args['tit_capa']) : "DEFAULT").",
				   ".(trim($args['texto_capa']) != '' ? str_escape($args['texto_capa']) : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 );";	

		$this->db->query($qr_sql);	

		return $cd_edicao;
	}

	public function atualizar($cd_edicao, $args = array())
	{
		$qr_sql = " 
			UPDATE projetos.edicao_cenario
			   SET tit_capa             = ".(trim($args['tit_capa']) != '' ? str_escape($args['tit_capa']) : "DEFAULT").",
				   texto_capa           = ".(trim($args['texto_capa']) != '' ? str_escape($args['texto_capa']) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_edicao = ".intval($cd_edicao).";";	

		$this->db->query($qr_sql);	
	}

	public function excluir($cd_edicao, $cd_usuario)
	{
		$qr_sql = " 
			UPDATE projetos.edicao_cenario
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_edicao = ".intval($cd_edicao).";";	
			
		$this->db->query($qr_sql);	
	}	
	
	public function get_secao()
	{
		$qr_sql = "
			SELECT codigo AS value,
			       descricao as text
		      FROM listas
			 WHERE categoria = 'SCEN'  
			 ORDER BY descricao";
			
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function get_divisao($cd_cenario = 0)
	{
		$qr_sql = "
			SELECT d.codigo AS value,
				   d.nome AS text
			  FROM projetos.divisoes d
			 WHERE d.tipo = 'DIV'
			   AND (d.codigo IN (SELECT DISTINCT uc.divisao
			                       FROM projetos.usuarios_controledi uc
			                      WHERE uc.tipo IN ('D','G','N','P','U'))
			    OR 0 < (SELECT COUNT(*)
			              FROM projetos.cenario_areas ca
			             WHERE ca.cd_cenario = ".intval($cd_cenario)."
			               AND ca.cd_divisao = d.codigo
			               AND ca.dt_exclusao IS NULL))
			 ORDER BY text;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega_conteudo($cd_cenario)
	{
		$qr_sql = "
			SELECT c.cd_cenario,
			       c.cd_edicao,
			       ec.tit_capa,
			       TO_CHAR(ec.dt_inclusao, 'YYYY') AS ds_ano_edicao,
			       TO_CHAR(ec.dt_inclusao, 'MM') AS ds_mes_edicao,
				   c.titulo,
				   c.referencia,
				   c.fonte,
				   c.cd_secao,
				   c.conteudo,
				   c.cd_cenario_referencia,
				   c.link1,
				   c.link2,
				   c.link3,
				   c.link4,
				   c.pertinencia,
				   TO_CHAR(c.dt_exclusao, 'DD/MM/YYYY') AS dt_exclusao,
				   TO_CHAR(ec.dt_envio_email, 'DD/MM/YYYY') AS dt_envio_email,
				   TO_CHAR(c.dt_legal, 'DD/MM/YYYY') AS dt_legal,
				   TO_CHAR(c.dt_prevista, 'DD/MM/YYYY') AS dt_prevista,
				   TO_CHAR(c.dt_implementacao, 'DD/MM/YYYY') AS dt_implementacao,
				   TO_CHAR(c.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(c.cd_usuario_inclusao) AS ds_usuario_inclusao,
				   TO_CHAR(c.dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
				   funcoes.get_usuario_nome(c.cd_usuario_cancelamento) AS ds_usuario_cancelamento,
				   (SELECT COUNT(*)
				      FROM projetos.cenario_gerencia cg
				     WHERE cg.cd_cenario         = c.cd_cenario
				       AND cg.dt_envio_atividade IS NULL) AS tl_area_enviar,
				   c.arquivo,
				   c.arquivo_nome,
				   ec.dt_envio_email_colegiado
			  FROM projetos.cenario c
			  JOIN projetos.edicao_cenario ec
			    ON ec.cd_edicao = c.cd_edicao
			 WHERE c.cd_cenario = ".intval($cd_cenario).";";
			   
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function cenario_gerencia($cd_cenario)
	{
		$qr_sql = "
		    SELECT cd_gerencia
			  FROM projetos.cenario_gerencia 	
			 WHERE cd_cenario  = ".intval($cd_cenario)."			   
			   AND dt_exclusao IS NULL;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_conteudo($args = array())
	{
		$cd_cenario = intval($this->db->get_new_id('projetos.cenario', 'cd_cenario'));

		$qr_sql = "
			INSERT INTO projetos.cenario
			     (
			       cd_cenario, 
			       cd_edicao, 
			       titulo, 
			       conteudo, 
			       link1,
			       link2, 
			       link3, 
	               link4, 
	               referencia, 
	               fonte,  
	               dt_legal, 
	               cd_cenario_referencia, 
	               pertinencia, 
	               cd_secao,
	               arquivo,
	               arquivo_nome,
	               cd_usuario_inclusao, 
	               cd_usuario_alteracao
			     )
			VALUES  
			     (
			       ".intval($cd_cenario).",
				   ".intval($args['cd_edicao']).",
				   ".(trim($args['titulo']) != '' ? str_escape($args['titulo']) : "DEFAULT").",
				   ".(trim($args['conteudo']) != '' ? str_escape($args['conteudo']) : "DEFAULT").",
				   ".(trim($args['link1']) != '' ? str_escape($args['link1']) : "DEFAULT").",
				   ".(trim($args['link2']) != '' ? str_escape($args['link2']) : "DEFAULT").",
				   ".(trim($args['link3']) != '' ? str_escape($args['link3']) : "DEFAULT").",
				   ".(trim($args['link4']) != '' ? str_escape($args['link4']) : "DEFAULT").",
				   ".(trim($args['referencia']) != '' ? str_escape($args['referencia']) : "DEFAULT").",
				   ".(trim($args['fonte']) != '' ? str_escape($args['fonte']) : "DEFAULT").",
				   ".(trim($args['dt_legal']) != '' ? "TO_DATE('".$args['dt_legal']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   ".(trim($args['cd_cenario_referencia']) != '' ? "'".intval($args['cd_cenario_referencia'])."'" : "DEFAULT").",
				   ".(trim($args['pertinencia']) != '' ? "'".intval($args['pertinencia'])."'" : "DEFAULT").",
				   ".(trim($args['cd_secao']) != '' ? "'".trim($args['cd_secao'])."'" : "DEFAULT").",
				   ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
				   ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
			 	 );";

		if(count($args['cenario_gerencia']) > 0)
        {
 			$qr_sql .= "
				INSERT INTO projetos.cenario_gerencia(cd_cenario, cd_gerencia, cd_usuario_inclusao, cd_usuario_alteracao)
				SELECT ".intval($cd_cenario).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
				  FROM (VALUES ('".implode("'),('", $args['cenario_gerencia'])."')) x;";
		}

        $this->db->query($qr_sql);

        return $cd_cenario;
	}

	public function atualizar_conteudo($cd_cenario, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.cenario
			   SET titulo                = ".(trim($args['titulo']) != '' ? str_escape($args['titulo']) : "DEFAULT").",
			       conteudo              = ".(trim($args['conteudo']) != '' ? str_escape($args['conteudo']) : "DEFAULT").",
			       link1                 = ".(trim($args['link1']) != '' ? str_escape($args['link1']) : "DEFAULT").",
			       link2                 = ".(trim($args['link2']) != '' ? str_escape($args['link2']) : "DEFAULT").",
			       link3                 = ".(trim($args['link3']) != '' ? str_escape($args['link3']) : "DEFAULT").",
			       link4                 = ".(trim($args['link4']) != '' ? str_escape($args['link4']) : "DEFAULT").",
			       referencia            = ".(trim($args['referencia']) != '' ? str_escape($args['referencia']) : "DEFAULT").",
			       fonte                 = ".(trim($args['fonte']) != '' ? str_escape($args['fonte']) : "DEFAULT").",
			       cd_cenario_referencia = ".(trim($args['cd_cenario_referencia']) != '' ? "'".intval($args['cd_cenario_referencia'])."'" : "DEFAULT").",
			       dt_legal              = ".(trim($args['dt_legal']) != '' ? "TO_DATE('".$args['dt_legal']."', 'DD/MM/YYYY')" : "DEFAULT").",
			       pertinencia           = ".(trim($args['pertinencia']) != '' ? "'".intval($args['pertinencia'])."'" : "DEFAULT").", 
			       cd_secao              = ".(trim($args['cd_secao']) != '' ? "'".trim($args['cd_secao'])."'" : "DEFAULT").",
			       arquivo               = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
			       arquivo_nome          = ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
			       cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
			       dt_alteracao          = CURRENT_TIMESTAMP
			 WHERE cd_cenario = ".intval($cd_cenario).";";

		if(count($args['cenario_gerencia']) > 0)
        {
			$qr_sql .= "
        		UPDATE projetos.cenario_gerencia
				   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
					   dt_exclusao         = CURRENT_TIMESTAMP
				 WHERE cd_cenario = ".intval($cd_cenario)."
				   AND dt_exclusao IS NULL
				   AND cd_gerencia NOT IN ('".implode("','", $args['cenario_gerencia'])."');
	   
				INSERT INTO projetos.cenario_gerencia(cd_cenario, cd_gerencia, cd_usuario_inclusao, cd_usuario_alteracao)
				SELECT ".intval($cd_cenario).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
				  FROM (VALUES ('".implode("'),('", $args['cenario_gerencia'])."')) x
				 WHERE x.column1 NOT IN (SELECT a.cd_gerencia
										   FROM projetos.cenario_gerencia a
										  WHERE a.cd_cenario = ".intval($cd_cenario)."
											AND a.dt_exclusao IS NULL);";
		}

		$this->db->query($qr_sql);
	}

	public function excluir_conteudo($cd_cenario, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.cenario
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_cenario = ".intval($cd_cenario).";";	
		
		$this->db->query($qr_sql);
	}

	public function cancelar_conteudo($cd_cenario, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.cenario
			   SET cd_usuario_cancelamento = ".intval($cd_usuario).",
			       dt_cancelamento         = CURRENT_TIMESTAMP
			 WHERE cd_cenario = ".intval($cd_cenario).";";	
		
		$this->db->query($qr_sql);
	}
	
	public function listar_anexo($cd_cenario)
	{
		$qr_sql = "
			SELECT a.cd_cenario_anexo,
				   a.arquivo,
				   a.arquivo_nome,
				   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS ds_usuario_inclusao
			  FROM projetos.cenario_anexo a
			 WHERE a.cd_cenario = ".intval($cd_cenario)."
			   AND a.dt_exclusao IS NULL
			 ORDER BY a.dt_inclusao DESC";

		return $this->db->query($qr_sql)->result_array();
	}
	
	public function salvar_anexo($cd_cenario, $args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.cenario_anexo
			     (
					cd_cenario,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($cd_cenario).",
					".str_escape($args['arquivo']).",
					".str_escape($args['arquivo_nome']).",
					".intval($args['cd_usuario'])."
				 )";

		$this->db->query($qr_sql);
	}
	
	public function excluir_anexo($cd_cenario_anexo, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.cenario_anexo
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_cenario_anexo = ".intval($cd_cenario_anexo).";";

		$this->db->query($qr_sql);
	}
	
	public function get_legislacao($cd_edicao)
	{
		$qr_sql = "
			SELECT titulo,
			       cd_cenario,
				   referencia
			  FROM projetos.cenario
			 WHERE cd_cenario NOT IN (SELECT cd_cenario FROM projetos.cenario WHERE dt_exclusao > '2000-01-01') 
			   AND cd_secao  = 'LGIN' 
			   AND cd_edicao = ".intval($cd_edicao)."
			 ORDER BY cd_cenario DESC;";
	
		return $this->db->query($qr_sql)->result_array();	
	}
	
	public function edicao_envia_email($cd_edicao, $cd_usuario)
	{
		$qr_sql = "
			SELECT rotinas.cenario_legal_enviar_email(".intval($cd_edicao).", ".intval($cd_usuario).");";
			 
		$this->db->query($qr_sql);
	}

	public function enviar_atividade($cd_cenario, $cd_edicao, $cd_usuario)
	{
		$qr_sql = "
			SELECT rotinas.cenario_legal_insere_atividade(".intval($cd_cenario).", ".intval($cd_edicao).", ".intval($cd_usuario).");";
			 
		$this->db->query($qr_sql);
	}
	
	public function get_ultima_edicao()
	{
		$qr_sql = "
			SELECT cd_edicao
			  FROM projetos.edicao_cenario
			 WHERE cd_edicao IN (SELECT MAX(cd_edicao) FROM projetos.edicao_cenario WHERE dt_exclusao IS NULL)";
			
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function get_ponto_vista($cd_edicao)
	{
		$qr_sql = "
			SELECT cd_cenario, 
			       titulo, 
				   conteudo
              FROM projetos.cenario
             WHERE cd_edicao = ".intval($cd_edicao)." 
			   AND cd_secao  = 'PVST';";
			   
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function get_ultimo_cenario($cd_edicao)
	{
		$qr_sql = "
			SELECT MAX(cd_cenario) AS cd_cenario
			  FROM projetos.cenario
			 WHERE cd_edicao  = ".intval($cd_edicao)."
			   AND cd_secao = 'LGIN'
			   AND dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_conteudo_legislacao($cd_cenario)
	{
		$qr_sql = "
			SELECT cd_cenario,
			       cd_edicao,
				   titulo,
				   referencia,
				   fonte,
				   cd_secao,
				   conteudo,
				   link1,
				   link2,
				   link3,
				   link4,
				   pertinencia,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MM') AS data_inc,
				   TO_CHAR(dt_exclusao, 'DD/MM/YYYY') AS dt_exclusao,
				   TO_CHAR(dt_legal, 'DD/MM/YYYY') AS dt_legal,
				   TO_CHAR(dt_prevista, 'DD/MM/YYYY') AS dt_prevista,
				   TO_CHAR(dt_implementacao, 'DD/MM/YYYY') AS dt_implementacao,
				   TO_CHAR(dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
				   funcoes.get_usuario_nome(cd_usuario_cancelamento) AS ds_usuario_cancelamento
			  FROM projetos.cenario
			 WHERE cd_secao = 'LGIN'
			   AND cd_cenario = ".intval($cd_cenario).";";
			   
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function get_agenda($cd_edicao)
	{
		$qr_sql = "
			SELECT cd_cenario, 
			       titulo, 
				   conteudo
			  FROM projetos.cenario
			 WHERE cd_edicao = ".intval($cd_edicao)."
			   AND cd_secao = 'AGEN'";
			
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function get_edicoes()
	{
		$qr_sql = "
			SELECT cd_edicao, 
			       tit_capa
			  FROM projetos.edicao_cenario
		     WHERE dt_edicao >= DATE_TRUNC('month', CURRENT_DATE - '1 year'::interval)
			 ORDER BY cd_edicao DESC";
			
		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_pesquisa_edicao($args = array())
	{
		$qr_sql ="
			SELECT ec.cd_edicao || ' - '|| ec.tit_capa AS n_titulo,
				   c.cd_cenario || ' - '|| c.titulo AS n_titulo_cenario,
				   TO_CHAR(c.dt_legal, 'DD/MM/YYYY') AS dt_legal,
				   TO_CHAR(c.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(c.dt_implementacao, 'DD/MM/YYYY') AS dt_implementacao,
				   c.cd_secao,
				   ec.cd_edicao,
				   c.cd_cenario 
			  FROM projetos.edicao_cenario ec
			  JOIN projetos.cenario c
			    ON c.cd_edicao = ec.cd_edicao
			 WHERE c.dt_exclusao IS NULL
			 ".(((trim($args['dt_legal_ini']) != "") AND (trim($args['dt_legal_fim']) != "")) ? " AND DATE_TRUNC('day', c.dt_legal) BETWEEN TO_DATE('".$args['dt_legal_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_legal_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(((trim($args['dt_implementacao_ini']) != "") AND (trim($args['dt_implementacao_fim']) != "")) ? " AND DATE_TRUNC('day', c.dt_implementacao) BETWEEN TO_DATE('".$args['dt_implementacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_implementacao_fim']."', 'DD/MM/YYYY')" : "")."
			 ".(trim($args["tit_capa"]) != "" ? "AND UPPER(funcoes.remove_acento(ec.tit_capa)) LIKE UPPER(funcoes.remove_acento('%".trim($args["tit_capa"])."%'))" : "")."
			 ".(trim($args["conteudo"]) != "" ? "AND UPPER(funcoes.remove_acento(c.conteudo)) LIKE UPPER(funcoes.remove_acento('%".trim($args["conteudo"])."%'))" : "")."
			 ".(trim($args["titulo"]) != "" ? "AND UPPER(funcoes.remove_acento(c.titulo)) LIKE UPPER(funcoes.remove_acento('%".trim($args["titulo"])."%'))" : "")."
			 ".(trim($args["referencia"]) != "" ? "AND UPPER(funcoes.remove_acento(c.referencia)) LIKE UPPER(funcoes.remove_acento('%".trim($args["referencia"])."%'))" : "")."
			 ORDER BY c.dt_inclusao DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar_lgin($cd_cenario)
	{
		$qr_sql ="
			SELECT c.cd_cenario || ' - '|| c.titulo AS text,
				   c.cd_cenario  AS value
			  FROM projetos.cenario c
			 WHERE c.dt_exclusao IS NULL
			   AND c.cd_secao = 'LGIN'
			   AND c.cd_cenario != ".intval($cd_cenario)."
			 ORDER BY c.cd_cenario DESC ;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function envia_email_colegiado($cd_edicao, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.edicao_cenario
			   SET cd_usuario_envio_email_colegiado = ".intval($cd_usuario_exclusao).",
			       dt_envio_email_colegiado         = CURRENT_TIMESTAMP
			 WHERE cd_edicao = ".intval($cd_edicao).";";

		$this->db->query($qr_sql);
	}

	public function consulta_normativo_listar($args = array())
	{
		$qr_sql = "
			SELECT c.cd_cenario, 
			       c.titulo, 
			       ec.tit_capa, 
				   TO_CHAR(c.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(c.dt_exclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao,
				   c.cd_edicao,
				   c.conteudo,
				   l.descricao AS ds_secao,
				   c.arquivo,
				   c.arquivo_nome
              FROM projetos.cenario c
              JOIN projetos.edicao_cenario ec
                ON ec.cd_edicao = c.cd_edicao
              LEFT JOIN listas l
                ON l.codigo = c.cd_secao
               AND categoria = 'SCEN'  
             WHERE c.dt_exclusao IS NULL
               AND c.dt_cancelamento IS NULL
               ".(trim($args['nome']) != '' ? "AND UPPER(ec.tit_capa) LIKE UPPER('%".trim($args['nome'])."%')" : '')."
			   ".(trim($args['cd_edicao']) != '' ? "AND ec.cd_edicao = ".intval($args['cd_edicao']) : '')."
			   ".(((trim($args['dt_ini']) != '') AND (trim($args['dt_fim']) != '')) ? "AND DATE_TRUNC('day', ec.dt_edicao) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['conteudo']) != '' ? "AND UPPER(c.titulo) LIKE UPPER('%".trim($args['conteudo'])."%')" : '')."
             ORDER BY c.dt_inclusao DESC;";

        return $this->db->query($qr_sql)->result_array();
	}
}
?>