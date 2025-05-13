<?php
class Reuniao_sistema_gestao_tipo_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar($args = array(), $fl_reuniao = 'S')
    {
    	$qr_sql = "
    		SELECT cd_reuniao_sistema_gestao_tipo,
    			   ds_reuniao_sistema_gestao_tipo,
                   dt_inclusao 
			  FROM gestao.reuniao_sistema_gestao_tipo
			 WHERE dt_exclusao IS NULL
               AND fl_reuniao = '".trim($fl_reuniao)."'
			   ".(trim($args['ds_reuniao_sistema_gestao_tipo']) != '' ? "AND UPPER(funcoes.remove_acento(ds_reuniao_sistema_gestao_tipo)) LIKE UPPER(funcoes.remove_acento('%".trim($args['ds_reuniao_sistema_gestao_tipo'])."%'))" : "").";";
                  
		  return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_reuniao_sistema_gestao_tipo)
    {
       $qr_sql = "
          SELECT cd_reuniao_sistema_gestao_tipo,
                 ds_reuniao_sistema_gestao_tipo
            FROM gestao.reuniao_sistema_gestao_tipo 
           WHERE dt_exclusao IS NULL
             AND cd_reuniao_sistema_gestao_tipo = ".intval($cd_reuniao_sistema_gestao_tipo).";";
     
        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_reuniao_sistema_gestao_tipo = intval($this->db->get_new_id('gestao.reuniao_sistema_gestao_tipo', 'cd_reuniao_sistema_gestao_tipo'));

        $qr_sql = "
            INSERT INTO gestao.reuniao_sistema_gestao_tipo
                 (
                    cd_reuniao_sistema_gestao_tipo, 
                    ds_reuniao_sistema_gestao_tipo, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_reuniao_sistema_gestao_tipo).",
                    ".(trim($args['ds_reuniao_sistema_gestao_tipo']) != '' ? str_escape($args['ds_reuniao_sistema_gestao_tipo']) : 'DEFAULT').",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        if(count($args['processo_checked']) > 0)
        {
            $qr_sql .= "
                INSERT INTO gestao.reuniao_sistema_gestao_tipo_processo
                (
                    cd_reuniao_sistema_gestao_tipo, 
                    cd_processo, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                )
                SELECT ".intval($cd_reuniao_sistema_gestao_tipo).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['processo_checked']).")) x;";
        }

        $this->db->query($qr_sql);

        return $cd_reuniao_sistema_gestao_tipo;
    }

    public function atualizar($cd_reuniao_sistema_gestao_tipo, $args = array())
    {
        $qr_sql = "
            UPDATE gestao.reuniao_sistema_gestao_tipo
               SET ds_reuniao_sistema_gestao_tipo = ".(trim($args['ds_reuniao_sistema_gestao_tipo']) != '' ? str_escape($args['ds_reuniao_sistema_gestao_tipo']) : 'DEFAULT').", 
                    cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                    dt_alteracao         = CURRENT_TIMESTAMP                   
             WHERE cd_reuniao_sistema_gestao_tipo = ".intval($cd_reuniao_sistema_gestao_tipo).";";

        if(count($args['processo_checked']) > 0)
        {
            $qr_sql .= "
                UPDATE gestao.reuniao_sistema_gestao_tipo_processo
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_reuniao_sistema_gestao_tipo = ".intval($cd_reuniao_sistema_gestao_tipo)."
                   AND dt_exclusao IS NULL
                   AND cd_processo NOT IN (".implode(",", $args['processo_checked']).");
       
                INSERT INTO gestao.reuniao_sistema_gestao_tipo_processo
                (
                    cd_reuniao_sistema_gestao_tipo, 
                    cd_processo, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                )
                SELECT ".intval($cd_reuniao_sistema_gestao_tipo).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['processo_checked']).")) x
                 WHERE x.column1 NOT IN (
                                        SELECT a.cd_processo
                                          FROM gestao.reuniao_sistema_gestao_tipo_processo a
                                         WHERE a.cd_reuniao_sistema_gestao_tipo = ".intval($cd_reuniao_sistema_gestao_tipo)."
                                           AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql .= "
                UPDATE gestao.reuniao_sistema_gestao_tipo_processo
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_reuniao_sistema_gestao_tipo = ".intval($cd_reuniao_sistema_gestao_tipo)."
                   AND dt_exclusao IS NULL;";
        }
              
       $this->db->query($qr_sql);
    }

    public function get_processo()
    {
        $qr_sql = "
            SELECT cd_processo AS value,
                   procedimento AS text
              FROM projetos.processos
             WHERE dt_fim_vigencia is NULL
             ORDER BY text;";       

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_processo_checked($cd_reuniao_sistema_gestao_tipo)
    {
        $qr_sql = "
            SELECT a.cd_processo,
                   a.cd_reuniao_sistema_gestao_tipo_processo,
                   a.nr_ordem,
                   p.procedimento AS processo
              FROM gestao.reuniao_sistema_gestao_tipo_processo a
              JOIN projetos.processos p
                ON p.cd_processo = a.cd_processo
             WHERE a.cd_reuniao_sistema_gestao_tipo  = ".intval($cd_reuniao_sistema_gestao_tipo)."
               AND a.dt_exclusao IS NULL
             ORDER BY a.nr_ordem ASC, a.cd_processo;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_cadastro_ordem($cd_reuniao_sistema_gestao_tipo, $cd_usuario, $args = array())
    {
        $qr_sql = "
            UPDATE gestao.reuniao_sistema_gestao_tipo_processo
               SET nr_ordem             = ".intval($args['nr_ordem']).",
                   cd_usuario_alteracao = ".intval($cd_usuario).", 
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_reuniao_sistema_gestao_tipo = ".intval($cd_reuniao_sistema_gestao_tipo)."
               AND cd_processo                    = ".intval($args['cd_processo']).";";
           
        $this->db->query($qr_sql);
    }

    public function get_indicador_checked($cd_processo, $cd_reuniao_sistema_gestao_tipo)
    {
        $qr_sql = "
            SELECT i.cd_indicador,
                   (SELECT lit.cd_indicador_tabela 
                    FROM indicador.listar_indicador_tabela_aberta_de_indicador lit 
                   WHERE lit.cd_indicador = i.cd_indicador 
                   ORDER BY nr_ano_referencia ASC 
                   LIMIT 1) AS cd_indicador_tabela
              FROM indicador.indicador i
              JOIN gestao.reuniao_sistema_gestao_tipo_indicador ri
                ON ri.cd_indicador = i.cd_indicador
             WHERE i.dt_exclusao IS NULL
               AND ri.cd_reuniao_sistema_gestao_tipo = ".$cd_reuniao_sistema_gestao_tipo."
               AND i.cd_processo                = ".intval($cd_processo)."
               AND ri.dt_exclusao IS NULL
             ORDER BY COALESCE(i.nr_ordem,0), 
                   i.ds_indicador;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_indicador($cd_processo)
    {
        $qr_sql = "
            SELECT i.cd_indicador AS value,
                   i.ds_indicador As text
              FROM indicador.indicador i
             WHERE i.dt_exclusao IS NULL
               AND i.cd_processo = ".intval($cd_processo)."
             ORDER BY COALESCE(i.nr_ordem,0), 
                   i.ds_indicador;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_indicador($cd_indicador, $cd_reuniao_sistema_gestao_tipo_processo, $cd_usuario, $indicador_tabela)
    {
        $qr_sql = "
            INSERT INTO gestao.reuniao_sistema_gestao_tipo_indicador
                 (
                    cd_reuniao_sistema_gestao_tipo_processo, 
                    cd_indicador, 
                    parametro, 
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                    ".intval($cd_reuniao_sistema_gestao_tipo_processo).",
                    ".intval($cd_indicador).",
                    ".str_escape($indicador_tabela).",
                    ".intval($cd_usuario).",
                    ".intval($cd_usuario)."
                 );";

        $this->db->query($qr_sql);
    }

    public function atualizar_indicador($cd_reuniao_sistema_gestao_tipo, $args)
    {
        if(count($args['indicador_checked']) > 0)
        {
            $qr_sql = "
                UPDATE gestao.reuniao_sistema_gestao_tipo_indicador
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_reuniao_sistema_gestao_tipo = ".intval($cd_reuniao_sistema_gestao_tipo)."
                   AND dt_exclusao IS NULL
                   AND cd_indicador NOT IN (".implode(",", $args['indicador_checked']).");  

                INSERT INTO gestao.reuniao_sistema_gestao_tipo_indicador(cd_reuniao_sistema_gestao_tipo, cd_indicador, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_reuniao_sistema_gestao_tipo).", i.cd_indicador , ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM indicador.indicador i
                 WHERE i.dt_exclusao IS NULL
                   AND i.cd_indicador NOT IN (SELECT i2.cd_indicador 
                                                FROM gestao.reuniao_sistema_gestao_tipo_indicador i2
                                               WHERE i2.cd_reuniao_sistema_gestao_tipo = ".intval($cd_reuniao_sistema_gestao_tipo)."
                                                 AND i2.dt_exclusao IS NULL)
                   AND i.cd_indicador IN (".implode(",", $args['indicador_checked']).");";
        }
        else
        {
            $qr_sql = "
                UPDATE gestao.reuniao_sistema_gestao_tipo_indicador
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_reuniao_sistema_gestao_tipo = ".intval($cd_reuniao_sistema_gestao_tipo)."
                   AND dt_exclusao IS NULL;";
        }    

        $this->db->query($qr_sql);
    }

    public function salvar_tipo_pendencia($args = array())
    {
        $qr_sql = "
            INSERT INTO gestao.reuniao_sistema_gestao_tipo
                 (
                    ds_reuniao_sistema_gestao_tipo,
                    fl_reuniao,
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".(trim($args['ds_reuniao_sistema_gestao_tipo']) != '' ? str_escape($args['ds_reuniao_sistema_gestao_tipo']) : 'DEFAULT').",
                    'N',
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function atualizar_tipo_pendencia($cd_reuniao_sistema_gestao_tipo, $args = array())
    {
        $qr_sql = "
            UPDATE gestao.reuniao_sistema_gestao_tipo
               SET ds_reuniao_sistema_gestao_tipo = ".(trim($args['ds_reuniao_sistema_gestao_tipo']) != '' ? str_escape($args['ds_reuniao_sistema_gestao_tipo']) : 'DEFAULT').", 
                   cd_usuario_alteracao           = ".intval($args['cd_usuario']).",
                   dt_alteracao                   = CURRENT_TIMESTAMP                   
             WHERE cd_reuniao_sistema_gestao_tipo = ".intval($cd_reuniao_sistema_gestao_tipo).";";

        $this->db->query($qr_sql);
    }
}