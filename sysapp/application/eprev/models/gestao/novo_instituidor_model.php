<?php
class Novo_instituidor_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function get_usuarios($cd_divisao)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($cd_divisao)."')";

        return $this->db->query($qr_sql)->result_array();
    }

    public function set_ordem($cd_novo_instituidor_estrutura, $args = array())
    {
        $qr_sql = "
            UPDATE gestao.novo_instituidor_estrutura
               SET nr_novo_instituidor_estrutura = ".(trim($args['nr_novo_instituidor_estrutura']) != '' ? intval($args['nr_novo_instituidor_estrutura']) : 'DEFAULT').",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).", 
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura).";";

        $this->db->query($qr_sql);
    }

    public function get_atividades($cd_novo_instituidor_estrutura)
    {
        $qr_sql = "
            SELECT cd_novo_instituidor_estrutura AS value,
                   nr_novo_instituidor_estrutura|| ' - ' || ds_novo_instituidor_estrutura AS text
              FROM gestao.novo_instituidor_estrutura
             WHERE dt_exclusao IS NULL
               AND cd_novo_instituidor_estrutura != ".intval($cd_novo_instituidor_estrutura)."
             ORDER BY nr_novo_instituidor_estrutura ASC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_atividade_checked($cd_novo_instituidor_estrutura)
    {
        $qr_sql = "
            SELECT niep.cd_novo_instituidor_estrutura_dep,
                   nie.nr_novo_instituidor_estrutura || ' - ' || nie.ds_novo_instituidor_estrutura as  ds_atividades_dependentes 
              FROM gestao.novo_instituidor_estrutura_dependencia niep
              JOIN gestao.novo_instituidor_estrutura nie
                ON nie.cd_novo_instituidor_estrutura = niep.cd_novo_instituidor_estrutura_dep
             WHERE niep.dt_exclusao IS NULL 
               AND niep.cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar($args = array())
    {
    	$qr_sql = "
    	  	SELECT cd_novo_instituidor_estrutura,
    	  	       nr_novo_instituidor_estrutura,
    	  	       ds_novo_instituidor_estrutura,
    	  	       ds_atividade,
    	  	       cd_gerencia,
    	  	       funcoes.get_usuario_nome(cd_usuario_responsavel) AS ds_usuario_responsavel,
    	  	       funcoes.get_usuario_nome(cd_usuario_substituto) AS ds_usuario_substituto,
    	  	       nr_prazo,
                   TO_CHAR(dt_desativado, 'DD/MM/YYYY HH24:MI:SS') AS dt_desativado,
    	  	       ds_observacao
    	  	  FROM gestao.novo_instituidor_estrutura
    	  	 WHERE dt_exclusao IS NULL
    	  	   ".(trim($args['fl_desativado']) == 'S' ? "AND dt_desativado IS NOT NULL" : "")."
    	  	   ".(trim($args['fl_desativado']) == 'N' ? "AND dt_desativado IS NULL" : "").";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_proximo_numero()
    {
    	$qr_sql = "
    		SELECT (nr_novo_instituidor_estrutura + 1) AS nr_novo_instituidor_estrutura
    		  FROM gestao.novo_instituidor_estrutura
    		 WHERE dt_exclusao   IS NULL
    		   AND dt_desativado IS NULL
    		 ORDER BY nr_novo_instituidor_estrutura DESC
    		 LIMIT 1";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function carrega($cd_novo_instituidor_estrutura)
    {
    	$qr_sql = "
    	  	SELECT cd_novo_instituidor_estrutura,
    	  	       nr_novo_instituidor_estrutura,
    	  	       ds_novo_instituidor_estrutura,
    	  	       ds_atividade,
    	  	       cd_gerencia,
    	  	       cd_usuario_responsavel,
    	  	       cd_usuario_substituto,
    	  	       nr_prazo,
    	  	       ds_observacao,
    	  	       TO_CHAR(dt_desativado, 'DD/MM/YYYY HH24:MI:SS') AS dt_desativado,
    	  	       funcoes.get_usuario_nome(cd_usuario_desativado) AS ds_usuario_desativado
    	  	  FROM gestao.novo_instituidor_estrutura
    	  	 WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $cd_novo_instituidor_estrutura = intval($this->db->get_new_id(
            'gestao.novo_instituidor_estrutura', 
            'cd_novo_instituidor_estrutura'
        ));

        $qr_sql = "
            INSERT INTO gestao.novo_instituidor_estrutura
                 (
            		cd_novo_instituidor_estrutura, 
            		nr_novo_instituidor_estrutura, 
            		ds_novo_instituidor_estrutura, 
            		ds_atividade, 
            		cd_gerencia, 
            		cd_usuario_responsavel, 
            		cd_usuario_substituto, 
            		nr_prazo, 
            		ds_observacao, 
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao
                 )
    		VALUES 
                 (
                    ".intval($cd_novo_instituidor_estrutura).",
                    ".(trim($args['nr_novo_instituidor_estrutura']) != '' ? intval($args['nr_novo_instituidor_estrutura']) : "DEFAULT").",
                    ".(trim($args['ds_novo_instituidor_estrutura']) != '' ? str_escape($args['ds_novo_instituidor_estrutura']) : "DEFAULT").",
                    ".(trim($args['ds_atividade']) != '' ? str_escape($args['ds_atividade']) : "DEFAULT").",
                    ".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
                    ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
                    ".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "DEFAULT").",
                    ".(trim($args['nr_prazo']) != '' ? intval($args['nr_prazo']) : "DEFAULT").",
                    ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        if(count($args['atividade_checked']) > 0)
        {
            $qr_sql .= "
                INSERT INTO gestao.novo_instituidor_estrutura_dependencia(cd_novo_instituidor_estrutura, cd_novo_instituidor_estrutura_dep, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_novo_instituidor_estrutura).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['atividade_checked']).")) x;";
        }
       
        $this->db->query($qr_sql);

        return $cd_novo_instituidor_estrutura;
    }

    public function atualizar($cd_novo_instituidor_estrutura, $args = array())
    {
    	$qr_sql = "
            UPDATE gestao.novo_instituidor_estrutura
               SET nr_novo_instituidor_estrutura = ".(trim($args['nr_novo_instituidor_estrutura']) != '' ? intval($args['nr_novo_instituidor_estrutura']) : "DEFAULT").",
            	   ds_novo_instituidor_estrutura = ".(trim($args['ds_novo_instituidor_estrutura']) != '' ? str_escape($args['ds_novo_instituidor_estrutura']) : "DEFAULT").",
            	   ds_atividade                  = ".(trim($args['ds_atividade']) != '' ? str_escape($args['ds_atividade']) : "DEFAULT").",
            	   cd_gerencia                   = ".(trim($args['cd_gerencia']) != '' ? str_escape($args['cd_gerencia']) : "DEFAULT").",
            	   cd_usuario_responsavel        = ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
            	   cd_usuario_substituto         = ".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "DEFAULT").",
            	   nr_prazo                      = ".(trim($args['nr_prazo']) != '' ? intval($args['nr_prazo']) : "DEFAULT").",
            	   ds_observacao                 = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                   cd_usuario_alteracao          = ".intval($args['cd_usuario']).",
                   dt_alteracao                  = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura).";";

        if(count($args['atividade_checked']) > 0)
        {
             $qr_sql .= "
                UPDATE gestao.novo_instituidor_estrutura_dependencia
                   SET cd_usuario_exclusao                      = ".intval($args['cd_usuario']).",
                       dt_exclusao                              = CURRENT_TIMESTAMP
                 WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura)."
                   AND dt_exclusao IS NULL
                   AND cd_novo_instituidor_estrutura_dep NOT IN (".implode(",", $args['atividade_checked']).");
       
                INSERT INTO gestao.novo_instituidor_estrutura_dependencia(cd_novo_instituidor_estrutura, cd_novo_instituidor_estrutura_dep, cd_usuario_inclusao, cd_usuario_alteracao)
                SELECT ".intval($cd_novo_instituidor_estrutura).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
                  FROM (VALUES (".implode("),(", $args['atividade_checked']).")) x
                 WHERE x.column1 NOT IN (SELECT a.cd_novo_instituidor_estrutura_dep
                                           FROM gestao.novo_instituidor_estrutura_dependencia a
                                          WHERE a.cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura)."
                                            AND a.dt_exclusao IS NULL);";
        }
        else
        {
            $qr_sql = "
                UPDATE gestao.novo_instituidor_estrutura_dependencia
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao         = CURRENT_TIMESTAMP
                 WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura)."
                   AND dt_exclusao IS NULL;";
        }    

        $this->db->query($qr_sql);
    }

    public function ativar($cd_novo_instituidor_estrutura, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.novo_instituidor_estrutura
               SET cd_usuario_alteracao          = ".intval($cd_usuario).",
                   dt_alteracao                  = CURRENT_TIMESTAMP,
                   cd_usuario_inclusao           = ".intval($cd_usuario).",
                   dt_inclusao                   = CURRENT_TIMESTAMP,
                   cd_usuario_exclusao         = ".intval($cd_usuario).",
                   dt_exclusao                  = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura).";";

        $this->db->query($qr_sql);
    }

    public function desativar($cd_novo_instituidor_estrutura, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.novo_instituidor_estrutura
               SET cd_usuario_desativado        = ".intval($cd_usuario).",
                   dt_desativado                = CURRENT_TIMESTAMP
             WHERE cd_novo_instituidor_estrutura = ".intval($cd_novo_instituidor_estrutura).";";

        $this->db->query($qr_sql);
    }

}

