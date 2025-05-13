<?php

class sumula_conselho_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
	public function get_usuarios($cd_divisao, $cd_usuario)
    {
		$qr_sql = "
		    SELECT codigo AS value,
				   nome AS text
			  FROM projetos.usuarios_controledi 
			 WHERE divisao = '".trim($cd_divisao)."'
			   AND tipo NOT IN ('X')
			 UNION
		    SELECT codigo AS value,
				   nome AS text
			  FROM projetos.usuarios_controledi
			 WHERE codigo = ".intval($cd_usuario)."
			 ORDER BY text;";
				  
		return $this->db->query($qr_sql)->result_array();
	}

    public function valida_numero_sumula($cd_sumula_conselho,$nr_sumula_conselho)
    {
      $qr_sql = "
         SELECT COUNT(*) AS valida
          FROM gestao.sumula_conselho
         WHERE dt_exclusao IS NULL
           AND nr_sumula_conselho = ".intval($nr_sumula_conselho)." 
           AND cd_sumula_conselho != ".intval($cd_sumula_conselho)." ;";

      return $this->db->query($qr_sql)->row_array();
    }

    public function valida_pauta($nr_sumula_conselho)
    {
        $qr_sql = "
            SElECT COUNT(*) tl_valida_pauta
              FROM gestao.pauta_sg
             WHERE fl_sumula = 'CD'
               AND dt_aprovacao IS NOT NULL 
               AND nr_ata = ".intval($nr_sumula_conselho).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_numero_cadastro()
    {
      $qr_sql = "
        SELECT nr_sumula_conselho + 1 AS nr_sumula
          FROM gestao.sumula_conselho
         WHERE dt_exclusao IS NULL
         ORDER BY nr_sumula_conselho DESC limit 1;";

      return $this->db->query($qr_sql)->row_array();
    }
	
    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT s.cd_sumula_conselho,
                   s.nr_sumula_conselho,
                   TO_CHAR(s.dt_sumula_conselho,'DD/MM/YYYY') AS dt_sumula_conselho,
                   TO_CHAR(s.dt_divulgacao,'DD/MM/YYYY') AS dt_divulgacao,
                   TO_CHAR(s.dt_publicacao_libera,'DD/MM/YYYY') AS dt_publicacao_libera,
                   TO_CHAR(s.dt_publicacao,'DD/MM/YYYY HH24:MI:SS') AS dt_publicacao,
				   funcoes.get_usuario_nome(s.cd_usuario_publicacao) AS usuario_publicacao,
                   s.arquivo,
                   s.arquivo_nome,
                   (SELECT COUNT(si.*)
                      FROM gestao.sumula_conselho_item si
                     WHERE si.dt_exclusao IS NULL
                       AND s.cd_sumula_conselho = si.cd_sumula_conselho) AS qt_itens,
                   (SELECT COUNT(si.*) 
                      FROM gestao.sumula_conselho_item_resposta sir
                      LEFT JOIN gestao.sumula_conselho_item si
                        ON sir.cd_sumula_conselho_item = si.cd_sumula_conselho_item
                     WHERE si.dt_exclusao IS NULL
                       AND sir.dt_exclusao IS NULL
                       AND s.cd_sumula_conselho = si.cd_sumula_conselho) AS qt_respondidos,
					(SELECT COUNT(si.*) 
                      FROM gestao.sumula_conselho_item_resposta sir
                      LEFT JOIN gestao.sumula_conselho_item si
                        ON sir.cd_sumula_conselho_item = si.cd_sumula_conselho_item
                     WHERE si.dt_exclusao IS NULL
                       AND sir.dt_exclusao IS NULL
                       AND s.cd_sumula_conselho = si.cd_sumula_conselho
                       AND sir.dt_inclusao::date > si.dt_limite::date) AS qt_respondidos_limite,
					(SELECT COUNT(sir.*)
				   	   FROM gestao.sumula_conselho_item_resposta sir
					   LEFT JOIN gestao.sumula_conselho_item si
					     ON sir.cd_sumula_conselho_item = si.cd_sumula_conselho_item
					  WHERE si.dt_exclusao IS NULL
					    AND sir.dt_exclusao IS NULL
					    AND s.cd_sumula_conselho = si.cd_sumula_conselho
					    AND sir.cd_resposta = 'AP') AS tl_acao_preventiva,
					(SELECT COUNT(sir.*)
				   	   FROM gestao.sumula_conselho_item_resposta sir
					   LEFT JOIN gestao.sumula_conselho_item si
					     ON sir.cd_sumula_conselho_item = si.cd_sumula_conselho_item
					  WHERE si.dt_exclusao IS NULL
					    AND sir.dt_exclusao IS NULL
					    AND s.cd_sumula_conselho = si.cd_sumula_conselho
					    AND sir.cd_resposta = 'NC') AS tl_nao_conformidade,
					(SELECT COUNT(sir.*)
				   	   FROM gestao.sumula_conselho_item_resposta sir
					   LEFT JOIN gestao.sumula_conselho_item si
					     ON sir.cd_sumula_conselho_item = si.cd_sumula_conselho_item
					  WHERE si.dt_exclusao IS NULL
					    AND sir.dt_exclusao IS NULL
					    AND s.cd_sumula_conselho = si.cd_sumula_conselho
					    AND sir.cd_resposta = 'SP') AS tl_sem_reflexo_plano_de_acao,
					(SELECT COUNT(sir.*)
				   	   FROM gestao.sumula_conselho_item_resposta sir
					   LEFT JOIN gestao.sumula_conselho_item si
					     ON sir.cd_sumula_conselho_item = si.cd_sumula_conselho_item
					  WHERE si.dt_exclusao IS NULL
					    AND sir.dt_exclusao IS NULL
					    AND s.cd_sumula_conselho = si.cd_sumula_conselho
					    AND sir.cd_resposta = 'SR') AS tl_sem_reflexo
              FROM gestao.sumula_conselho s
              LEFT JOIN gestao.sumula_conselho_item si
                ON si.cd_sumula_conselho = s.cd_sumula_conselho
             WHERE s.dt_exclusao IS NULL
               AND si.dt_exclusao IS NULL
               ".(trim($args['nr_sumula_conselho']) != '' ? "AND s.nr_sumula_conselho = ".intval($args['nr_sumula_conselho']) :'')."
               ".(trim($args['descricao']) != '' ? "AND UPPER(si.descricao) LIKE UPPER('%".trim($args["descricao"])."%')" : "")."
               ".(((trim($args['dt_ini']) != "") and  (trim($args['dt_fim']) != "")) ? " AND DATE_TRUNC('day', s.dt_sumula_conselho) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_div_ini']) != "") and  (trim($args['dt_div_fim']) != "")) ? " AND DATE_TRUNC('day', s.dt_divulgacao) BETWEEN TO_DATE('".$args['dt_div_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_div_fim']."', 'DD/MM/YYYY')" : "")."
               ".($args["fl_respondido"] == 'S' ? "AND ((SELECT COUNT(si.*)
                                                           FROM gestao.sumula_conselho_item si
                                                          WHERE si.dt_exclusao IS NULL
                                                            AND s.cd_sumula_conselho = si.cd_sumula_conselho)
                                                        -    
                                                       (SELECT COUNT(si.*) 
                                                          FROM gestao.sumula_conselho_item_resposta sir
                                                          LEFT JOIN gestao.sumula_conselho_item si
                                                            ON sir.cd_sumula_conselho_item = si.cd_sumula_conselho_item
                                                         WHERE si.dt_exclusao IS NULL
                                                           AND sir.dt_exclusao IS NULL
                                                           AND s.cd_sumula_conselho = si.cd_sumula_conselho))
                                                           = 0" : '')."
              ".(trim($args["fl_respondido"]) == 'N' ? "AND ((SELECT COUNT(si.*)
                                                                FROM gestao.sumula_conselho_item si
                                                               WHERE si.dt_exclusao IS NULL
                                                                 AND s.cd_sumula_conselho = si.cd_sumula_conselho)
                                                               -    
                                                             (SELECT COUNT(si.*) 
                                                                FROM gestao.sumula_conselho_item_resposta sir
                                                                LEFT JOIN gestao.sumula_conselho_item si
                                                                  ON sir.cd_sumula_conselho_item = si.cd_sumula_conselho_item
                                                               WHERE si.dt_exclusao IS NULL
                                                                 AND sir.dt_exclusao IS NULL
                                                                 AND s.cd_sumula_conselho = si.cd_sumula_conselho)) > 0" : '')."
			  ".(trim($args['cd_resposta']) != '' ? "AND (SELECT COUNT(sir.*)
			                                                FROM gestao.sumula_conselho_item_resposta sir
														    LEFT JOIN gestao.sumula_conselho_item si
														      ON sir.cd_sumula_conselho_item = si.cd_sumula_conselho_item
													       WHERE si.dt_exclusao IS NULL
														     AND sir.dt_exclusao IS NULL
															 AND s.cd_sumula_conselho = si.cd_sumula_conselho
															 AND sir.cd_resposta = '".trim($args['cd_resposta'])."') > 0" : "")."
			  ".(((trim($args['dt_resposta_ini']) != "") and  (trim($args['dt_resposta_fim']) != "")) ? "
			                                         AND (SELECT COUNT(sir.*)
			                                                FROM gestao.sumula_conselho_item_resposta sir
														    LEFT JOIN gestao.sumula_conselho_item si
														      ON sir.cd_sumula_conselho_item = si.cd_sumula_conselho_item
													       WHERE si.dt_exclusao IS NULL
														     AND sir.dt_exclusao IS NULL
															 AND s.cd_sumula_conselho = si.cd_sumula_conselho
															 AND DATE_TRUNC('day', sir.dt_inclusao) BETWEEN TO_DATE('".$args['dt_resposta_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_resposta_fim']."', 'DD/MM/YYYY')) > 0" : "")."
             GROUP BY s.cd_sumula_conselho
             ORDER BY s.dt_sumula_conselho DESC";

        $result = $this->db->query($qr_sql);
    }
	
	function total_enviados(&$result, $args=array())
    {
        $qr_sql = "
			 SELECT x.* FROM (
				 SELECT COUNT(i1.*) AS tl
					  FROM gestao.sumula_conselho_item i1
					 WHERE i1.dt_exclusao IS NULL
					   AND i1.dt_envio IS NULL
					   AND i1.cd_sumula_conselho = ". intval($args['cd_sumula_conselho'])."
					 UNION 
					SELECT COUNT(i2.*) AS tl
					  FROM gestao.sumula_conselho_item i2
					 WHERE i2.dt_exclusao IS NULL
					   AND i2.cd_sumula_conselho = ". intval($args['cd_sumula_conselho'])."
				) AS x;";

        $result = $this->db->query($qr_sql);
    }
	
	function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT s.cd_sumula_conselho,
			       MD5('CD'||s.cd_sumula_conselho::TEXT) AS cd_sumula_md5,
                   s.nr_sumula_conselho,
                   TO_CHAR(s.dt_sumula_conselho,'DD/MM/YYYY') AS dt_sumula_conselho,
                   TO_CHAR(s.dt_divulgacao,'DD/MM/YYYY') AS dt_divulgacao,
                   TO_CHAR(s.dt_publicacao_libera,'DD/MM/YYYY') AS dt_publicacao_libera,
                   TO_CHAR(s.dt_publicacao,'DD/MM/YYYY HH24:MI:SS') AS dt_publicacao,
				   funcoes.get_usuario_nome(s.cd_usuario_publicacao) AS usuario_publicacao,				   
                   s.arquivo,
                   s.arquivo_nome,
				   p.integracao_arq,
				   s.arquivo_ata,
				   s.arquivo_ata_nome,
                   TO_CHAR(s.dt_arquivo_ata,'DD/MM/YYYY HH24:MI:SS') AS dt_arquivo_ata,
				   funcoes.get_usuario_nome(s.cd_usuario_arquivo_ata) AS usuario_arquivo_ata
              FROM gestao.sumula_conselho s
			  LEFT JOIN gestao.pauta_sg p
			    ON p.nr_ata    = s.nr_sumula_conselho
			   AND p.fl_sumula = 'CD'
             WHERE cd_sumula_conselho = ". intval($args['cd_sumula_conselho']);

        $result = $this->db->query($qr_sql);
    }
	
	function salvar(&$result, $args=array())
    {
        if(intval($args['cd_sumula_conselho']) > 0)
        {
            $qr_sql = "
                UPDATE gestao.sumula_conselho
                   SET nr_sumula_conselho    = ".intval($args['nr_sumula_conselho']).",
                       dt_sumula_conselho    = ".(trim($args['dt_sumula_conselho']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_sumula_conselho']."','DD/MM/YYYY')").",
                       dt_divulgacao         = ".(trim($args['dt_divulgacao']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_divulgacao']."','DD/MM/YYYY')").",
                       arquivo_nome          = ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".$args['arquivo_nome']."'").",
                       arquivo               = ".(trim($args['arquivo']) == "" ? "DEFAULT" : "'".$args['arquivo']."'").",
                       cd_usuario_alteracao  = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
                       dt_alteracao          = CURRENT_TIMESTAMP 
                 WHERE cd_sumula_conselho = ".intval($args['cd_sumula_conselho']);
            
            $this->db->query($qr_sql);
                
        }
        else
        {
            $qr_sql = "
                INSERT INTO gestao.sumula_conselho
                     (
                       nr_sumula_conselho,
                       dt_sumula_conselho,
                       dt_divulgacao,
                       arquivo_nome,
                       arquivo,
                       cd_usuario_inclusao,
                       cd_usuario_alteracao
                     )
                VALUES
                     (
                       ".intval($args['nr_sumula_conselho']).",
                       ".(trim($args['dt_sumula_conselho']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_sumula_conselho']."','DD/MM/YYYY')").",
                       ".(trim($args['dt_divulgacao']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_divulgacao']."','DD/MM/YYYY')").",
                       ".(trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".$args['arquivo_nome']."'").",
                       ".(trim($args['arquivo']) == "" ? "DEFAULT" : "'".$args['arquivo']."'").",
                       ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
                       ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario']))."
                     )";

            $this->db->query($qr_sql);
            
            $qr_sql = "
                SELECT cd_sumula_conselho
                  FROM gestao.sumula_conselho
                 WHERE nr_sumula_conselho  = ".intval($args['nr_sumula_conselho'])."
                   AND cd_usuario_inclusao = ".intval($args['cd_usuario'])."
                 ORDER BY cd_sumula_conselho DESC 
                 LIMIT 1";
            
            $result = $this->db->query($qr_sql);
            
            $row = $result->row_array();
            
            $args['cd_sumula_conselho'] = $row['cd_sumula_conselho'];
        }
        
        return $args['cd_sumula_conselho'];
    }
	
	function salvarAta(&$result, $args=array())
    {
        if(intval($args['cd_sumula_conselho']) > 0)
        {
            $qr_sql = "
						UPDATE gestao.sumula_conselho
						   SET arquivo_ata_nome       = ".(trim($args['arquivo_ata_nome']) == "" ? "DEFAULT" : "'".$args['arquivo_ata_nome']."'").",
							   arquivo_ata            = ".(trim($args['arquivo_ata']) == "" ? "DEFAULT" : "'".$args['arquivo_ata']."'").",
							   dt_arquivo_ata         = CURRENT_TIMESTAMP,
							   cd_usuario_arquivo_ata = ".intval($args['cd_usuario']).",
                               cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
                               dt_alteracao           = CURRENT_TIMESTAMP 
						 WHERE cd_sumula_conselho = ".intval($args['cd_sumula_conselho'])."
				      ";
            
            $this->db->query($qr_sql);
		}
	}	
	
	function publicar(&$result, $args=array())
    {
        if(intval($args['cd_sumula_conselho']) > 0)
        {
            $qr_sql = "
						UPDATE gestao.sumula_conselho
						   SET dt_publicacao_libera  = ".(trim($args['dt_publicacao_libera']) == "" ? "NULL" : "TO_DATE('".$args['dt_publicacao_libera']."','DD/MM/YYYY')").",
							   dt_publicacao         = ".(trim($args['dt_publicacao_libera']) == "" ? "NULL" : "CURRENT_TIMESTAMP").",
							   cd_usuario_publicacao = ".(trim($args['dt_publicacao_libera']) == "" ? "NULL" : intval($args['cd_usuario'])).",
                               cd_usuario_alteracao  = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
                               dt_alteracao          = CURRENT_TIMESTAMP 
						 WHERE cd_sumula_conselho = ".intval($args['cd_sumula_conselho'])."
				      ";
            
            $this->db->query($qr_sql);
		}
	}
	
	function get_gerencia(&$result, $args=array())
    {
        $qr_sql = "
            SELECT codigo AS value,
                   codigo || ' - '|| nome AS text
              FROM projetos.divisoes
             WHERE tipo = 'DIV'
             ORDER BY text";

        $result = $this->db->query($qr_sql);
    }
	
	function gericias_cadastradas(&$result, $args=array())
    {
        $qr_sql = "
            SELECT d.codigo AS value,
                   d.codigo || ' - '|| d.nome AS text
              FROM projetos.divisoes d
			  JOIN gestao.sumula_conselho_item si
			    ON d.codigo = si.cd_gerencia
             WHERE d.tipo = 'DIV'
			   AND si.cd_sumula_conselho = ".intval($args['cd_sumula_conselho'])."
             ORDER BY text";

        $result = $this->db->query($qr_sql);
    }
	
	function lista_itens(&$result, $args=array())
    {
        $qr_sql = "
            SELECT st.cd_sumula_conselho,
                   st.cd_sumula_conselho_item,
                   st.nr_sumula_conselho_item,
                   st.descricao,
                   TO_CHAR(st.dt_envio,'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
                   TO_CHAR(st.dt_limite,'DD/MM/YYYY') AS dt_limite,
                   d.codigo || ' - '|| d.nome AS gerencia,
                   sir.cd_resposta,
                   sir.complemento,
                   TO_CHAR(sir.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_resposta,
                   uc.nome,
                   uc2.nome AS responsavel,
                   uc3.nome AS substituto,
				   dir.ds_diretoria,
				   sir.numero,
				   sir.ano,
				   CASE WHEN sir.cd_resposta = 'NC' THEN funcoes.nr_nc(sir.ano, sir.numero)
						WHEN sir.cd_resposta = 'AP' THEN funcoes.nr_ap(sir.ano, sir.numero)
						ELSE ''
				   END AS nr_ano_numero
              FROM gestao.sumula_conselho_item st
			  LEFT JOIN projetos.diretoria dir
			    ON dir.cd_diretoria = st.cd_diretoria
              LEFT JOIN projetos.divisoes d
                ON st.cd_gerencia = d.codigo
              LEFT JOIN gestao.sumula_conselho_item_resposta sir
                ON st.cd_sumula_conselho_item = sir.cd_sumula_conselho_item
              LEFT JOIN projetos.usuarios_controledi uc
                ON uc.codigo = sir.cd_usuario_inclusao
              LEFT JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = st.cd_responsavel
              LEFT JOIN projetos.usuarios_controledi uc3
                ON uc3.codigo = st.cd_substituto
             WHERE st.dt_exclusao IS NULL
               AND st.cd_sumula_conselho = ". intval($args['cd_sumula_conselho'])."
               ".(trim($args['cd_gerencia']) != '' ? " AND st.cd_gerencia = '".trim($args['cd_gerencia'])."'" : '')."
               ".(trim($args['cd_diretoria']) != '' ? " AND st.cd_diretoria = '".trim($args['cd_diretoria'])."'" : '')."
			   ".(trim($args['cd_resposta']) != '' ? " AND sir.cd_resposta = '".trim($args['cd_resposta'])."'" : '')."
               ".(trim($args['fl_recebido']) == 'S' ? ' AND sir.dt_inclusao IS NOT NULL' : '')."
               ".(trim($args['fl_recebido']) == 'S' ? ' AND sir.dt_inclusao IS NOT NULL' : '')."
               ".(trim($args['fl_recebido']) == 'N' ? ' AND sir.dt_inclusao IS NULL' : '');
        $result = $this->db->query($qr_sql);
    }
	
	function salvar_item(&$result, $args=array())
    {
        
        if(intval($args['cd_sumula_conselho_item']) == 0)
        {
            $qr_sql = "
                INSERT INTO gestao.sumula_conselho_item
                     (
                       cd_sumula_conselho,
                       cd_gerencia,
					   cd_diretoria,
                       nr_sumula_conselho_item,
                       descricao,
                       cd_usuario_inclusao,
                       cd_substituto,
                       cd_responsavel
                     )
                VALUES
                     (
                       ".intval($args['cd_sumula_conselho']).",
                       ".(trim($args['cd_gerencia']) == "" ? "DEFAULT" : "'".$args['cd_gerencia']."'").",
                       ".(trim($args['cd_diretoria']) == "" ? "DEFAULT" : "'".$args['cd_diretoria']."'").",
                       ".(trim($args['nr_sumula_conselho_item']) == "" ? "DEFAULT" : intval($args['nr_sumula_conselho_item']))." ,
                       ".(trim($args['descricao']) == "" ? "DEFAULT" : "'".$args['descricao']."'").",
                       ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
                       ".(trim($args['cd_usuario_substituto']) == "" ? "DEFAULT" : intval($args['cd_usuario_substituto'])).", 
                       ".(trim($args['cd_usuario_responsavel']) == "" ? "DEFAULT" : intval($args['cd_usuario_responsavel']))." 
                     )";

            $this->db->query($qr_sql);
        }
        else
        {
            $qr_sql = "
                UPDATE gestao.sumula_conselho_item
                   SET nr_sumula_conselho_item = ".(trim($args['nr_sumula_conselho_item']) == "" ? "DEFAULT" : intval($args['nr_sumula_conselho_item']))." ,
                       descricao               = ".(trim($args['descricao']) == "" ? "DEFAULT" : "'".$args['descricao']."'").",
                       cd_gerencia             = ".(trim($args['cd_gerencia']) == "" ? "DEFAULT" : "'".$args['cd_gerencia']."'").",
                       cd_diretoria            = ".(trim($args['cd_diretoria']) == "" ? "DEFAULT" : "'".$args['cd_diretoria']."'").",
                       cd_substituto           = ".(trim($args['cd_usuario_substituto']) == "" ? "DEFAULT" : intval($args['cd_usuario_substituto'])).", 
                       cd_responsavel          = ".(trim($args['cd_usuario_responsavel']) == "" ? "DEFAULT" : intval($args['cd_usuario_responsavel']))." 
                 WHERE cd_sumula_conselho_item = ".intval($args['cd_sumula_conselho_item'])."";
            
            $this->db->query($qr_sql);
        }
		
    }
	function carrega_sumula_item(&$result, $args=array())
    {
        $qr_sql = "
            SELECT si.cd_sumula_conselho_item,
                   si.nr_sumula_conselho_item,
                   si.descricao,
                   si.cd_gerencia,
                   si.cd_substituto,
                   si.cd_responsavel,
                   uc.divisao AS cd_divisao_responsavel,
                   uc2.divisao AS cd_divisao_substituto,
				   si.cd_diretoria
              FROM gestao.sumula_conselho_item  si
              LEFT JOIN projetos.usuarios_controledi uc
                ON uc.codigo = si.cd_responsavel
              LEFT JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = si.cd_substituto
             WHERE si.dt_exclusao IS NULL
               AND si.cd_sumula_conselho_item = ". intval($args['cd_sumula_conselho_item']);

        $result = $this->db->query($qr_sql);
    }
	
	function excluir_sumula_item(&$result, $args=array())
    {
        $qr_sql = "
                UPDATE gestao.sumula_conselho_item
                   SET dt_exclusao         = CURRENT_TIMESTAMP ,
                       cd_usuario_exclusao = ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario']))." 
                 WHERE cd_sumula_conselho_item = ".intval($args['cd_sumula_conselho_item'])."";
            
        $this->db->query($qr_sql);
    }
	
	function enviar_todos(&$result, $args=array())
    {
        $qr_sql = "
                UPDATE gestao.sumula_conselho_item
                   SET dt_envio  = CURRENT_TIMESTAMP ,
                       dt_limite = funcoes.dia_util( 'DEPOIS', CURRENT_DATE, 5 )     
                 WHERE cd_sumula_conselho = ".intval($args['cd_sumula_conselho'])."
                   AND dt_envio IS NULL";
            
        $this->db->query($qr_sql);
    }
	
	function enviar(&$result, $args=array())
    {
        $qr_sql = "
                UPDATE gestao.sumula_conselho_item
                   SET dt_envio  = CURRENT_TIMESTAMP ,
                       dt_limite = funcoes.dia_util( 'DEPOIS', CURRENT_DATE, 5 )     
                 WHERE cd_sumula_conselho_item = ".intval($args['cd_sumula_conselho_item'])."";
            
        $this->db->query($qr_sql);
    }
	
	function acompanhamento_item(&$result, $args=array())
	{
		$qr_sql = "
			SELECT sa.descricao,
			       TO_CHAR(sa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM gestao.sumula_conselho_acompanhamento sa
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = sa.cd_usuario_inclusao
			 WHERE sa.dt_exclusao IS NULL
			   AND sa.cd_sumula_conselho_item = ".intval($args['cd_sumula_conselho_item'])."
			 ORDER BY sa.dt_inclusao";
		$result = $this->db->query($qr_sql);
	}
	
	function acompanhamento_sem_item(&$result, $args=array())
	{
		$qr_sql = "
			SELECT sa.descricao,
			       TO_CHAR(sa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM gestao.sumula_conselho_acompanhamento sa
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = sa.cd_usuario_inclusao
			 WHERE sa.dt_exclusao IS NULL
			   AND sa.cd_sumula_conselho_item IS NULL
			   AND sa.cd_sumula_conselho = ".intval($args['cd_sumula_conselho'])."
			 ORDER BY sa.dt_inclusao";
		$result = $this->db->query($qr_sql);
	}
	
	function item_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_sumula_conselho_item,
                   nr_sumula_conselho_item,
                   descricao
			  FROM gestao.sumula_conselho_item
			 WHERE cd_sumula_conselho = ".intval($args['cd_sumula_conselho'])."
			   AND dt_exclusao IS NULL
			 ORDER BY nr_sumula_conselho_item ASC";
		$result = $this->db->query($qr_sql);
	}
	
	function lista_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT sa.cd_sumula_conselho_acompanhamento,
			       sa.descricao,
				   TO_CHAR(sa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   si.nr_sumula_conselho_item || ' - ' || si.descricao AS item,
				   uc.nome
			  FROM gestao.sumula_conselho_acompanhamento sa
			  LEFT JOIN gestao.sumula_conselho_item si
			    ON si.cd_sumula_conselho_item = sa.cd_sumula_conselho_item
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = sa.cd_usuario_inclusao
			 WHERE sa.dt_exclusao IS NULL
			   AND si.dt_exclusao IS NULL
			   AND sa.cd_sumula_conselho = ".intval($args['cd_sumula_conselho'])."
			 ORDER BY sa.dt_inclusao DESC";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO gestao.sumula_conselho_acompanhamento
			     (
				   cd_sumula_conselho,
				   cd_sumula_conselho_item,
				   descricao,
				   cd_usuario_inclusao
				 )
		    VALUES
			     (
				   ".intval($args['cd_sumula_conselho']).",
				   ".(trim($args['cd_sumula_conselho_item']) != '' ? intval($args['cd_sumula_conselho_item']) : "DEFAULT").",
				   '".trim($args['descricao'])."',
				   ".intval($args['cd_usuario'])."
				 )";
				 
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.sumula_conselho_acompanhamento
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_sumula_conselho_acompanhamento = ".intval($args['cd_sumula_conselho_acompanhamento'])."";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_sumula_item_resposta(&$result, $args=array())
    {
        $qr_sql = "
            SELECT si.cd_sumula_conselho_item,
                   si.nr_sumula_conselho_item,
                   si.descricao,
                   si.cd_gerencia,
				   si.cd_diretoria,
                   TO_CHAR(si.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   TO_CHAR(s.dt_sumula_conselho,'DD/MM/YYYY') AS dt_sumula_conselho,
                   TO_CHAR(si.dt_envio,'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
                   TO_CHAR(si.dt_limite,'DD/MM/YYYY') AS dt_limite,
                   TO_CHAR(s.dt_divulgacao,'DD/MM/YYYY') AS dt_divulgacao,
                   s.nr_sumula_conselho,
                   s.arquivo,
                   TO_CHAR(sir.dt_inclusao,'DD/MM/YYYY') AS dt_resposta,
                   sir.complemento,
                   sir.cd_sumula_conselho_item_resposta,
				   sir.cd_resposta,
                   s.arquivo_nome,
                   si.cd_responsavel,
                   si.cd_substituto,
                   funcoes.get_usuario_nome(si.cd_substituto) AS substituto,
                   funcoes.get_usuario_nome(si.cd_responsavel) AS nome_do_responsavel,
				   sir.numero,
				   sir.ano
              FROM gestao.sumula_conselho_item si
              JOIN gestao.sumula_conselho s
                ON s.cd_sumula_conselho = si.cd_sumula_conselho
              LEFT JOIN gestao.sumula_conselho_item_resposta sir
                ON si.cd_sumula_conselho_item = sir.cd_sumula_conselho_item
             WHERE si.cd_sumula_conselho_item = ". intval($args['cd_sumula_conselho_item']);

        $result = $this->db->query($qr_sql);
    }
	
	function salvar_resposta(&$result, $args=array())
    {
        $qr_sql = "
            INSERT INTO gestao.sumula_conselho_item_resposta
                 (
                    cd_sumula_conselho_item,
                    cd_resposta,
                    complemento,
                    cd_usuario_inclusao,
					numero,
					ano
                  )
             VALUES
                  (
                    ".intval($args['cd_sumula_conselho_item']).",
                    ".(trim($args['cd_resposta']) == "" ? "DEFAULT" : "'".$args['cd_resposta']."'").",
                    ".(trim($args['complemento']) == "" ? "DEFAULT" : "'".$args['complemento']."'").",
                    ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).", 
                    ".(trim($args['numero']) == "" ? "DEFAULT" : intval($args['numero'])).", 
                    ".(trim($args['ano']) == "" ? "DEFAULT" : intval($args['ano']))." 
                  )";
        
        $this->db->query($qr_sql);
    }
	
	function mudar_responsavel(&$result, $args=array())
    {
        $qr_sql = "
			UPDATE gestao.sumula_conselho_item
			   SET dt_envio             = CURRENT_TIMESTAMP,
			       cd_responsavel       = ".intval($args['cd_responsavel'])."
			 WHERE cd_sumula_conselho_item = ".intval($args['cd_sumula_conselho_item'])."";

        $this->db->query($qr_sql);
    }
	
	function carrega_minhas(&$result, $args=array())
    {
        $qr_sql = "
            SELECT funcoes.nr_sumula_item(s.nr_sumula_conselho, nr_sumula_conselho_item) AS nr_sumula_conselho_item,
			       si.cd_sumula_conselho_item,
                   si.descricao,
                   si.cd_gerencia,
                   si.cd_diretoria,
                   TO_CHAR(si.dt_inclusao,'DD/MM/YYYY') AS dt_inclusao,
                   TO_CHAR(si.dt_envio,'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
                   TO_CHAR(si.dt_limite,'DD/MM/YYYY') AS dt_limite,
                   TO_CHAR(s.dt_divulgacao,'DD/MM/YYYY') AS dt_divulgacao,
                   s.nr_sumula_conselho,
                   s.arquivo,
                   TO_CHAR(sir.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_resposta,
                   sir.complemento,
                   sir.cd_resposta,
                   s.arquivo_nome,
                   si.cd_responsavel,
                   si.cd_substituto,
                   uc.nome,
                   uc2.nome AS responsavel,
                   uc3.nome AS substituto
              FROM gestao.sumula_conselho_item si
              JOIN gestao.sumula_conselho s
                ON s.cd_sumula_conselho = si.cd_sumula_conselho
              LEFT JOIN gestao.sumula_conselho_item_resposta sir
                ON si.cd_sumula_conselho_item = sir.cd_sumula_conselho_item
              LEFT JOIN projetos.usuarios_controledi uc
                ON uc.codigo = sir.cd_usuario_inclusao
              LEFT JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = si.cd_responsavel
              LEFT JOIN projetos.usuarios_controledi uc3
                ON uc3.codigo = si.cd_substituto
             WHERE (si.cd_responsavel =".intval($args['cd_usuario'])." 
			    OR si.cd_substituto = ".intval($args['cd_usuario'])."
				OR si.cd_diretoria  = '".trim($args['diretoria'])."')
		       AND si.dt_exclusao IS NULL
               AND sir.dt_exclusao IS NULL			   
              ".(trim($args['fl_respondido']) == 'S' ? "AND sir.dt_inclusao IS NOT NULL" : '')."
              ".(trim($args['fl_respondido']) == 'N' ? "AND sir.dt_inclusao IS NULL" : '')."
              ".(((trim($args["dt_ini_envio"]) != "") and (trim($args["dt_fim_envio"]) != "")) ? " AND CAST(si.dt_envio AS DATE) BETWEEN TO_DATE('".$args["dt_ini_envio"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_fim_envio"]."','DD/MM/YYYY')" : "")."  
              ".(((trim($args["dt_ini_resp"]) != "") and (trim($args["dt_fim_resp"]) != "")) ? " AND CAST(sir.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args["dt_ini_resp"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_fim_resp"]."','DD/MM/YYYY')" : "")."
              ".(trim($args['nr_sumula_conselho']) != '' ? "AND s.nr_sumula_conselho = ".intval($args['nr_sumula_conselho']) : '')."
			ORDER BY s.nr_sumula_conselho DESC
			  ";
        $result = $this->db->query($qr_sql);
    }
	
	function email_gerentes(&$result, $args=array())
	{
		$texto = "Prezado(a)s Gerentes:

Segue a Súmula do Conselho Deliberativo ".$args['nr_sumula_conselho']." para conhecimento.

".base_url().'up/sumula_conselho/' . $args['arquivo'];
	
		$qr_sql = "
			INSERT INTO projetos.envia_emails 
			     (
					dt_envio, 
					de, 
					para, 
					cc, 
					cco, 
					assunto, 
					texto,
					cd_evento
				 )
		    VALUES 
				 ( 
					CURRENT_TIMESTAMP, 
					'Conselho Deliberativo',
					'gerentes@eletroceee.com.br',                         
					'', 
					'',
					'Súmula do Conselho Deliberativo ".$args['nr_sumula_conselho']."', 
					'".$texto."',
					125
				  );";
			
		$result = $this->db->query($qr_sql);
	}
    
    function get_usuario_diretor(&$result, $args=array())
    {
        $qr_sql = "
            SELECT funcoes.get_usuario_diretor(".intval($args['cd_responsavel']).") AS cd_usuario_diretor_resposanvel, 
                   funcoes.get_usuario_diretor(".intval($args['cd_substituto']).") AS cd_usuario_diretor_substituto;";
        
        $result = $this->db->query($qr_sql);
    }
	
	function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO gestao.sumula_conselho_item_anexo
			     (
					cd_sumula_conselho_item,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_sumula_conselho_item']).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario'])."
				 )";
				 
		$result = $this->db->query($qr_sql);
	}
	
	function listar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT a.cd_sumula_conselho_item_anexo,
				   a.arquivo,
				   a.arquivo_nome,
				   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM gestao.sumula_conselho_item_anexo a
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = a.cd_usuario_inclusao
			 WHERE a.cd_sumula_conselho_item = ". $args['cd_sumula_conselho_item']."
			   AND a.dt_exclusao IS NULL
			 ORDER BY a.dt_inclusao DESC";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.sumula_conselho_item_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_sumula_conselho_item_anexo = ".intval($args['cd_sumula_conselho_item_anexo']).";";
		$this->db->query($qr_sql);
	}

	function enviar_fundacao(&$result, $args=array())
    {
        $qr_sql = "
            INSERT INTO projetos.envia_emails 
                 (
                    dt_envio, 
                    de, 
                    para, 
                    cc, 
                    cco, 
                    assunto, 
                    texto,
                    cd_evento,
                    cd_usuario
                 )
            VALUES 
                 (
                    CURRENT_TIMESTAMP, 
                    'Súmula Conselho Fiscal',
                    'todos@eletroceee.com.br',                         
                    '', 
                    '',
                    '".$args['assunto']."', 
                    '".$args['texto']."',
                    125,
                    ".intval($args['cd_usuario'])."
                  );

            UPDATE gestao.sumula_conselho s
               SET dt_envio_todos = CURRENT_TIMESTAMP
             WHERE cd_sumula_conselho = ".intval($args['cd_sumula_conselho']).";";
                  
        $result = $this->db->query($qr_sql);
    }

    public function assunto_aprovado($cd_sumula_conselho)
    {
        $qr_sql = "
            SELECT pa.cd_pauta_sg_assunto,
                   p.nr_ano,
                   p.nr_ata,
                   TO_CHAR(dt_pauta_sg, 'DD-MM-YYYY') AS dt_pauta_sg,
                   pa.nr_item_sumula
              FROM gestao.pauta_sg_assunto pa
              JOIN gestao.pauta_sg p
                ON p.cd_pauta_sg = pa.cd_pauta_sg
             WHERE pa.dt_exclusao       IS NULL
               AND pa.dt_retirada_pauta IS NULL
               AND pa.fl_aprovado       = 'S'
               AND pa.cd_pauta_sg_assunto IN (
                    SELECT si.cd_pauta_sg_assunto
                      FROM gestao.sumula_conselho s
                      JOIN gestao.sumula_conselho_item si
                        ON si.cd_sumula_conselho = s.cd_sumula_conselho
                     WHERE s.cd_sumula_conselho   = ".intval($cd_sumula_conselho)."
                       AND s.dt_exclusao IS NULL
               );";

        return $this->db->query($qr_sql)->result_array();
    }

    public function assunto_aprovado_anexo($cd_pauta_sg_assunto)
    {
        $qr_sql = "
            SELECT arquivo, 
                   arquivo_nome
              FROM gestao.pauta_sg_assunto_anexo 
             WHERE dt_exclusao        IS NULL
               AND cd_pauta_sg_assunto = ".intval($cd_pauta_sg_assunto).";";

        return $this->db->query($qr_sql)->result_array();
    }
	
    public function getSumulaAssinatura(&$result, $args)
    {
        $qr_sql = "
					SELECT s.cd_sumula_conselho,
						   s.nr_sumula_conselho,
						   TO_CHAR(d.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   d.id_doc
					  FROM gestao.sumula_conselho s
					  JOIN gestao.pauta_sg p
						ON p.nr_ata    = s.nr_sumula_conselho
					   AND p.fl_sumula = 'CD'
					  JOIN clicksign.documento d
						ON d.cd_gestao_tipo      = 'SUMULA'
					   AND d.cd_gestao_colegiado = p.fl_sumula
					   AND d.nr_gestao_doc       = s.nr_sumula_conselho
					 WHERE s.cd_sumula_conselho = ".intval($args['cd_sumula_conselho'])."
                     ORDER BY d.dt_inclusao DESC					 
                  ";

        $result = $this->db->query($qr_sql);
    }

    public function getAtaAssinatura(&$result, $args)
    {
        $qr_sql = "
					SELECT s.cd_sumula_conselho,
						   s.nr_sumula_conselho,
						   TO_CHAR(d.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   d.id_doc
					  FROM gestao.sumula_conselho s
					  JOIN gestao.pauta_sg p
						ON p.nr_ata    = s.nr_sumula_conselho
					   AND p.fl_sumula = 'CD'
					  JOIN clicksign.documento d
						ON d.cd_gestao_tipo      = 'ATA'
					   AND d.cd_gestao_colegiado = p.fl_sumula
					   AND d.nr_gestao_doc       = s.nr_sumula_conselho
					 WHERE s.cd_sumula_conselho = ".intval($args['cd_sumula_conselho'])."		
					 ORDER BY d.dt_inclusao DESC
                  ";

        $result = $this->db->query($qr_sql);
    }	
}
?>