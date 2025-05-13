<?php
class Processos_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar($args = array())
    {
        $qr_sql = "
          SELECT DISTINCT pp.cod_responsavel AS cd_gerencia_responsavel,
                 d.nome AS ds_responsavel,
                 pp.procedimento,
                 pp.cd_processo,
                 (CASE WHEN pp.dt_ini_vigencia <= CURRENT_DATE AND COALESCE(pp.dt_fim_vigencia, CURRENT_DATE) >= CURRENT_DATE 
                       THEN 'S' 
                       ELSE 'N' 
                 END) AS fl_vigente,
                 (CASE WHEN pp.dt_ini_vigencia <= CURRENT_DATE AND COALESCE(pp.dt_fim_vigencia, CURRENT_DATE) >= CURRENT_DATE 
                       THEN 'Sim' 
                       ELSE 'Não' 
                 END) AS ds_vigente,
                 (CASE WHEN pp.dt_ini_vigencia <= CURRENT_DATE AND COALESCE(pp.dt_fim_vigencia, CURRENT_DATE) >= CURRENT_DATE 
                       THEN 'label label-info' 
                       ELSE 'label label-important' 
                 END) AS ds_class_vigente,
                 (CASE WHEN pp.fl_versao_it = 'S' 
                       THEN 'green'
                       ELSE 'black'
                 END) AS ds_color_versao,
                 (CASE WHEN pp.fl_versao_it = 'S' 
                       THEN 'label label-success'
                       ELSE 'label label-inverse'
                 END) AS ds_class_versao,
                 (CASE WHEN pp.fl_versao_it = 'S' 
                       THEN 'Sim'
                       ELSE 'Não'
                 END) AS ds_versao_it,
                 TO_CHAR(pp.dt_ini_vigencia,'DD/MM/YYYY') AS dt_ini_vigencia,
                 TO_CHAR(pp.dt_fim_vigencia,'DD/MM/YYYY') AS dt_fim_vigencia,
                 (SELECT TO_CHAR(pr.dt_revisao,'DD/MM/YYYY') || ' : ' || funcoes.get_usuario_nome(pr.cd_usuario_revisao)
                    FROM projetos.processos_revisao pr
                   WHERE pr.dt_exclusao IS NULL
                     AND pr.dt_revisao IS NOT NULL
                     AND pr.cd_processo = pp.cd_processo
                   ORDER BY dt_revisao DESC
                   LIMIT 1) AS ds_revisao,
			     (SELECT COUNT(*)
		           FROM indicador.indicador i
		          WHERE i.dt_exclusao IS NULL
		            AND i.cd_processo = pp.cd_processo) AS qt_indicador
            FROM projetos.processos pp
            LEFT JOIN projetos.divisoes d
              ON pp.cod_responsavel = d.codigo
           WHERE pp.dt_exclusao IS NULL
             ".(trim($args['fl_vigente']) != '' ? "AND '".trim($args['fl_vigente'])."' = (CASE WHEN pp.dt_ini_vigencia <= CURRENT_DATE AND COALESCE(pp.dt_fim_vigencia, CURRENT_DATE) >= CURRENT_DATE THEN 'S' ELSE 'N' END)" : "")."
             ".(trim($args['fl_versao_it']) == 'S' ? "AND pp.fl_versao_it = 'S'" : "")."
             ".(trim($args['fl_versao_it']) == 'N' ? "AND pp.fl_versao_it = 'N'" : "")."
             ".(trim($args['cd_gerencia_responsavel']) != '' ? "AND pp.cod_responsavel = ".str_escape($args['cd_gerencia_responsavel']) : "")."
           ORDER BY pp.procedimento;";
                  
        return $this->db->query($qr_sql)->result_array();
    }

    public function get_responsavel()
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_gerencias_vigente('DIV, ASS, COM, CON');";
             
        return $this->db->query($qr_sql)->result_array();
    }

    public function get_usuario_gerencia($cd_gerencia)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($cd_gerencia)."')
             ORDER BY nome;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_processo)
    {   
        $qr_sql = "
            SELECT cd_processo,
                   procedimento,
                   TO_CHAR(data, 'DD/MM/YYYY') AS data,
                   TO_CHAR(dt_ini_vigencia,'DD/MM/YYYY') AS dt_ini_vigencia,
                   TO_CHAR(dt_fim_vigencia,'DD/MM/YYYY') AS dt_fim_vigencia,                           
                   cod_responsavel,
                   envolvidos,
                   fl_versao_it
              FROM projetos.processos
             WHERE cd_processo = ".intval($cd_processo).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_gerencia_responsavel()
    {
        $qr_sql = "
            SELECT DISTINCT codigo AS value,
                   nome AS text
              FROM funcoes.get_gerencias_vigente('DIV, ASS, COM, CON') d
              JOIN projetos.processos pp
                ON pp.cod_responsavel = d.codigo
             WHERE pp.dt_exclusao IS NULL
               AND (pp.dt_fim_vigencia::date > CURRENT_DATE OR pp.dt_fim_vigencia IS NULL)
           ORDER BY text;";

          return $this->db->query($qr_sql)->result_array();
    }
    
    public function salvar($args = array())
    {   
        $cd_processo = $this->db->get_new_id('projetos.processos', 'cd_processo');

        $qr_sql = "
            INSERT INTO projetos.processos
                 (
                   cd_processo,
                   dt_ini_vigencia,
                   dt_fim_vigencia,
                   procedimento,
                   cod_responsavel,
                   envolvidos,
                   fl_versao_it,
                   cd_usuario_inclusao,
                   cd_usuario_alteracao
                 )
            VALUES
                 (
                   ".intval($cd_processo).",
                   TO_DATE('".$args['dt_ini_vigencia']."', 'DD/MM/YYYY'),
                   ".(trim($args['dt_fim_vigencia']) != '' ? "TO_DATE('".$args['dt_fim_vigencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   ".(trim($args['procedimento']) != '' ? "'".trim($args['procedimento'])."'" : "DEFAULT").",
                   ".(trim($args['cod_responsavel']) != '' ? "'".trim($args['cod_responsavel'])."'" : "DEFAULT").",
                   ".(trim($args['envolvidos']) != '' ? "'".trim($args['envolvidos'])."'" : "DEFAULT").",
                   ".(trim($args['fl_versao_it']) != '' ? "'".trim($args['fl_versao_it'])."'" : "DEFAULT").",
                   ".intval($args['cd_usuario']).",
                   ".intval($args['cd_usuario'])."
                 );";

        if(count($args['gerencia_envolvida']) > 0)
        {
            $qr_sql .= "
                INSERT INTO projetos.processo_envolvidos
                (
                    cd_processo, 
                    cd_gerencia, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                )
                SELECT ".intval($cd_processo).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['gerencia_envolvida'])."')) x;";
        }

        $this->db->query($qr_sql);

        return $cd_processo;
    }

    public function atualizar($cd_processo, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.processos
               SET dt_ini_vigencia      = TO_DATE('".$args['dt_ini_vigencia']."', 'DD/MM/YYYY'),
                   dt_fim_vigencia      = ".(trim($args['dt_fim_vigencia']) != '' ? "TO_DATE('".$args['dt_fim_vigencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   procedimento         = ".(trim($args['procedimento']) != '' ? "'".trim($args['procedimento'])."'" : "DEFAULT").",
                   cod_responsavel      = ".(trim($args['cod_responsavel']) != '' ? "'".trim($args['cod_responsavel'])."'" : "DEFAULT").",
                   envolvidos           = ".(trim($args['envolvidos']) != '' ? "'".trim($args['envolvidos'])."'" : "DEFAULT").",
                   fl_versao_it         = ".(trim($args['fl_versao_it']) != '' ? "'".trim($args['fl_versao_it'])."'" : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_processo = ".intval($cd_processo).";";

        if(count($args['gerencia_envolvida']) > 0)
        {
            $qr_sql .= "
                UPDATE projetos.processo_envolvidos
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_processo = ".intval($cd_processo)."
                   AND dt_exclusao IS NULL
                   AND cd_gerencia NOT IN ('".implode("','", $args['gerencia_envolvida'])."');
       
                INSERT INTO projetos.processo_envolvidos
                (
                    cd_processo, 
                    cd_gerencia, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                )
                SELECT ".intval($cd_processo).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES ('".implode("'),('", $args['gerencia_envolvida'])."')) x
                 WHERE x.column1 NOT IN (
                                        SELECT a.cd_gerencia
                                          FROM projetos.processo_envolvidos a
                                         WHERE a.cd_processo = ".intval($cd_processo)."
                                           AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.processo_envolvidos
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_processo = ".intval($cd_processo)."
                   AND dt_exclusao IS NULL;";
        }

        if(count($args['usuario_responsavel']) > 0)
        {
            $qr_sql .= "
                UPDATE projetos.processo_usuario_responsavel
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_processo = ".intval($cd_processo)."
                   AND dt_exclusao IS NULL
                   AND cd_usuario NOT IN (".implode(",", $args['usuario_responsavel']).");
       
                INSERT INTO projetos.processo_usuario_responsavel
                (
                    cd_processo, 
                    cd_usuario, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                )
                SELECT ".intval($cd_processo).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['usuario_responsavel']).")) x
                 WHERE x.column1 NOT IN (
                                        SELECT a.cd_usuario
                                          FROM projetos.processo_usuario_responsavel a
                                         WHERE a.cd_processo = ".intval($cd_processo)."
                                           AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE projetos.processo_usuario_responsavel
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_processo = ".intval($cd_processo)."
                   AND dt_exclusao IS NULL;";
        }

        $this->db->query($qr_sql);
    }

    public function get_gerencia_envolvida($cd_processo)
    {
        $qr_sql = "
            SELECT cd_gerencia
              FROM projetos.processo_envolvidos
             WHERE cd_processo = ".intval($cd_processo)."
               AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_usuario_responsavel($cd_processo)
    {
        $qr_sql = "
            SELECT pur.cd_usuario,
                   uc.nome AS ds_nome_usuario
              FROM projetos.processo_usuario_responsavel pur
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = pur.cd_usuario
             WHERE pur.cd_processo = ".intval($cd_processo)."
               AND pur.dt_exclusao IS NULL
               AND uc.tipo != 'X';";

        return $this->db->query($qr_sql)->result_array();
    }

    public function lista_indicador($cd_processo, $cd_tipo = '')
    {
        $qr_sql = "
            SELECT i.cd_indicador,
                   i.ds_indicador,
               	   i.cd_tipo,
                   ig.ds_indicador_grupo,
                   (SELECT lit.cd_indicador_tabela 
            	      FROM indicador.listar_indicador_tabela_aberta_de_indicador lit 
            	     WHERE lit.cd_indicador = i.cd_indicador 
            	     ORDER BY nr_ano_referencia ASC 
            	     LIMIT 1) AS cd_indicador_tabela
              FROM indicador.indicador i
              JOIN indicador.indicador_grupo ig 
                ON ig.cd_indicador_grupo = i.cd_indicador_grupo
             WHERE i.dt_exclusao IS NULL
               AND i.cd_processo = ".intval($cd_processo)."
               ".(trim($cd_tipo) != '' ? "AND i.cd_tipo = '".trim($cd_tipo)."'" : "")."
             ORDER BY COALESCE(i.nr_ordem,0), i.ds_indicador;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar_instrumento($cd_processo)
    {
        $qr_sql = "
            SELECT a.cd_processos_instrumento_trabalho_anexo,
                   a.arquivo,
                   a.arquivo_nome,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   a.codigo,
                   a.ds_processos_instrumento_trabalho_anexo
              FROM projetos.processos_instrumento_trabalho_anexo a
             WHERE a.cd_processo = ".intval($cd_processo)."
               AND a.dt_exclusao IS NULL
             ORDER BY a.codigo, 
                      a.ds_processos_instrumento_trabalho_anexo, 
                      a.dt_inclusao DESC";

        return $this->db->query($qr_sql)->result_array();
    }

    public function instrumento($cd_processos_instrumento_trabalho_anexo)
    {
        $qr_sql = "
            SELECT a.cd_processos_instrumento_trabalho_anexo,
                   a.arquivo,
                   a.arquivo_nome,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   a.codigo,
                   a.ds_processos_instrumento_trabalho_anexo
              FROM projetos.processos_instrumento_trabalho_anexo a
             WHERE a.cd_processos_instrumento_trabalho_anexo = ".intval($cd_processos_instrumento_trabalho_anexo).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_instrumento($args = array())
    {
        $cd_processos_instrumento_trabalho_anexo = $this->db->get_new_id('projetos.processos_instrumento_trabalho_anexo', 'cd_processos_instrumento_trabalho_anexo');

        $qr_sql = "
            INSERT INTO projetos.processos_instrumento_trabalho_anexo
                 (
                    cd_processos_instrumento_trabalho_anexo,
                    cd_processo,
                    arquivo,
                    arquivo_nome,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_processos_instrumento_trabalho_anexo).",
                    ".intval($args['cd_processo']).",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_processos_instrumento_trabalho_anexo;
    }

    public function atualizar_instrumento($cd_processos_instrumento_trabalho_anexo, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.processos_instrumento_trabalho_anexo
               SET codigo                                  = ".(trim($args['codigo']) != '' ? str_escape($args['codigo']) : "DEFAULT").",
                   ds_processos_instrumento_trabalho_anexo = ".(trim($args['ds_processos_instrumento_trabalho_anexo']) != '' ? str_escape($args['ds_processos_instrumento_trabalho_anexo']) : "DEFAULT").",
                   cd_usuario_alteracao                    = ".intval($args['cd_usuario']).",
                   dt_alteracao                            = CURRENT_TIMESTAMP
             WHERE cd_processos_instrumento_trabalho_anexo = ".intval($cd_processos_instrumento_trabalho_anexo).";";

        $this->db->query($qr_sql);
    }

    public function excluir_instrumento($cd_processos_instrumento_trabalho_anexo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.processos_instrumento_trabalho_anexo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_processos_instrumento_trabalho_anexo = ".intval($cd_processos_instrumento_trabalho_anexo).";";
           
        $this->db->query($qr_sql);
    }

    public function listar_fluxo($cd_processo)
    {
        $qr_sql = "
            SELECT a.cd_processos_fluxo_anexo,
                   a.arquivo,
                   a.arquivo_nome,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   a.codigo,
                   a.ds_processos_fluxo_anexo,
                   a.ds_link_interact
              FROM projetos.processos_fluxo_anexo a
             WHERE a.cd_processo = ".intval($cd_processo)."
               AND a.dt_exclusao IS NULL
             ORDER BY a.codigo, 
                      a.ds_processos_fluxo_anexo, 
                      a.dt_inclusao DESC";

        return $this->db->query($qr_sql)->result_array();
    }

    public function fluxo($cd_processos_fluxo_anexo)
    {
        $qr_sql = "
            SELECT a.cd_processos_fluxo_anexo,
                   a.arquivo,
                   a.arquivo_nome,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   a.codigo,
                   a.ds_processos_fluxo_anexo,
                   a.ds_link_interact
              FROM projetos.processos_fluxo_anexo a
             WHERE a.cd_processos_fluxo_anexo = ".intval($cd_processos_fluxo_anexo).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_fluxo($args = array())
    {
        $cd_processos_fluxo_anexo = $this->db->get_new_id('projetos.processos_fluxo_anexo', 'cd_processos_fluxo_anexo');

        $qr_sql = "
            INSERT INTO projetos.processos_fluxo_anexo
                 (
                    cd_processos_fluxo_anexo,
                    cd_processo,
                    codigo,
                    ds_processos_fluxo_anexo,
                    ds_link_interact,
                    arquivo,
                    arquivo_nome,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_processos_fluxo_anexo).",
                    ".intval($args['cd_processo']).",
                    ".(trim($args['codigo']) != '' ? str_escape($args['codigo']) : "DEFAULT").",
                    ".(trim($args['ds_processos_fluxo_anexo']) != '' ? str_escape($args['ds_processos_fluxo_anexo']) : "DEFAULT").",
                    '".trim($args['ds_link_interact'])."',
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);

        return $cd_processos_fluxo_anexo;
    }

    public function atualizar_fluxo($cd_processos_fluxo_anexo, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.processos_fluxo_anexo
               SET codigo                   = ".(trim($args['codigo']) != '' ? str_escape($args['codigo']) : "DEFAULT").",
                   ds_processos_fluxo_anexo = ".(trim($args['ds_processos_fluxo_anexo']) != '' ? str_escape($args['ds_processos_fluxo_anexo']) : "DEFAULT").",
                   ds_link_interact         = ".(trim($args['ds_link_interact']) != '' ? str_escape($args['ds_link_interact']) : "DEFAULT").",
                   arquivo                  = '".trim($args['arquivo'])."',
                   arquivo_nome             = '".trim($args['arquivo_nome'])."',
                   cd_usuario_alteracao     = ".intval($args['cd_usuario']).",
                   dt_alteracao             = CURRENT_TIMESTAMP
             WHERE cd_processos_fluxo_anexo = ".intval($cd_processos_fluxo_anexo).";";

        $this->db->query($qr_sql);
    }

    public function excluir_fluxo($cd_processos_fluxo_anexo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.processos_fluxo_anexo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_processos_fluxo_anexo = ".intval($cd_processos_fluxo_anexo).";";
           
        $this->db->query($qr_sql);
    }

    public function listar_pop($cd_processo)
    {
        $qr_sql = "
            SELECT a.cd_processos_pop_anexo,
                   a.arquivo,
                   a.arquivo_nome,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   a.codigo,
                   a.ds_processos_pop_anexo
              FROM projetos.processos_pop_anexo a
             WHERE a.cd_processo = ".intval($cd_processo)."
               AND a.dt_exclusao IS NULL
             ORDER BY a.codigo, 
                      a.ds_processos_pop_anexo, 
                      a.dt_inclusao DESC";

        return $this->db->query($qr_sql)->result_array();
    }

    public function pop($cd_processos_pop_anexo)
    {
        $qr_sql = "
            SELECT a.cd_processos_pop_anexo,
                   a.arquivo,
                   a.arquivo_nome,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   a.codigo,
                   a.ds_processos_pop_anexo
              FROM projetos.processos_pop_anexo a
             WHERE a.cd_processos_pop_anexo = ".intval($cd_processos_pop_anexo).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_pop($args = array())
    {
        $cd_processos_pop_anexo = $this->db->get_new_id('projetos.processos_pop_anexo', 'cd_processos_pop_anexo');

        $qr_sql = "
            INSERT INTO projetos.processos_pop_anexo
                 (
                    cd_processos_pop_anexo,
                    cd_processo,
                    arquivo,
                    arquivo_nome,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_processos_pop_anexo).",
                    ".intval($args['cd_processo']).",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);

        return $cd_processos_pop_anexo;
    }

    public function atualizar_pop($cd_processos_pop_anexo, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.processos_pop_anexo
               SET codigo                 = ".(trim($args['codigo']) != '' ? str_escape($args['codigo']) : "DEFAULT").",
                   ds_processos_pop_anexo = ".(trim($args['ds_processos_pop_anexo']) != '' ? str_escape($args['ds_processos_pop_anexo']) : "DEFAULT").",
                   cd_usuario_alteracao   = ".intval($args['cd_usuario']).",
                   dt_alteracao           = CURRENT_TIMESTAMP
             WHERE cd_processos_pop_anexo = ".intval($cd_processos_pop_anexo).";";

        $this->db->query($qr_sql);
    }

    public function excluir_pop($cd_processos_pop_anexo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.processos_pop_anexo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_processos_pop_anexo = ".intval($cd_processos_pop_anexo).";";
           
        $this->db->query($qr_sql);
    }

    public function listar_registro($cd_processo)
    {
        $qr_sql = "
            SELECT a.cd_processos_registro_anexo,
                   a.arquivo,
                   a.arquivo_nome,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   a.codigo,
                   a.ds_processos_registro_anexo
              FROM projetos.processos_registro_anexo a
             WHERE a.cd_processo = ".intval($cd_processo)."
               AND a.dt_exclusao IS NULL
             ORDER BY a.codigo, 
                      a.ds_processos_registro_anexo, 
                      a.dt_inclusao DESC";

        return $this->db->query($qr_sql)->result_array();
    }

    public function registro($cd_processos_registro_anexo)
    {
        $qr_sql = "
            SELECT a.cd_processos_registro_anexo,
                   a.arquivo,
                   a.arquivo_nome,
                   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS ds_usuario_inclusao,
                   a.codigo,
                   a.ds_processos_registro_anexo
              FROM projetos.processos_registro_anexo a
             WHERE a.cd_processos_registro_anexo = ".intval($cd_processos_registro_anexo).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_registro($args = array())
    {
        $cd_processos_registro_anexo = $this->db->get_new_id('projetos.processos_registro_anexo', 'cd_processos_registro_anexo');

        $qr_sql = "
            INSERT INTO projetos.processos_registro_anexo
                 (
                    cd_processos_registro_anexo,
                    cd_processo,
                    arquivo,
                    arquivo_nome,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_processos_registro_anexo).",
                    ".intval($args['cd_processo']).",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);

        return $cd_processos_registro_anexo;
    }

    public function atualizar_registro($cd_processos_registro_anexo, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.processos_registro_anexo
               SET codigo                      = ".(trim($args['codigo']) != '' ? str_escape($args['codigo']) : "DEFAULT").",
                   ds_processos_registro_anexo = ".(trim($args['ds_processos_registro_anexo']) != '' ? str_escape($args['ds_processos_registro_anexo']) : "DEFAULT").",
                   cd_usuario_alteracao        = ".intval($args['cd_usuario']).",
                   dt_alteracao                = CURRENT_TIMESTAMP
             WHERE cd_processos_registro_anexo = ".intval($cd_processos_registro_anexo).";";

        $this->db->query($qr_sql);
    }

    public function excluir_registro($cd_processos_registro_anexo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.processos_registro_anexo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_processos_registro_anexo = ".intval($cd_processos_registro_anexo).";";
           
        $this->db->query($qr_sql);
    }
}
?>