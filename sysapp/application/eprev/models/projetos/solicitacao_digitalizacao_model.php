<?php

class Solicitacao_digitalizacao_model extends Model {

	function __construct()
    {
    	parent::Model();
    }

	public function get_usuarios($divisao)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($divisao)."')";

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar($args = array())
    {
        $qr_sql = "
            SELECT cd_solicitacao_digitalizacao,
                   cd_gerencia_responsavel,
                   TO_CHAR(dt_solicitacao_digitalizacao, 'DD/MM/YYYY') AS dt_solicitacao_digitalizacao,
                   ds_solicitacao_digitalizacao,
                   nr_solicitacao_digitalizacao,
                   funcoes.get_usuario_nome(cd_usuario_responsavel) AS cd_usuario_responsavel
              FROM projetos.solicitacao_digitalizacao 
             WHERE dt_exclusao IS NULL
               ".(trim($args['cd_gerencia_responsavel']) != '' ? "AND cd_gerencia_responsavel = ".str_escape($args['cd_gerencia_responsavel']) : '')."
               ".(intval($args['cd_usuario_responsavel']) > 0 ? "AND cd_usuario_responsavel = ".intval($args['cd_usuario_responsavel']) : '')."
               ".(((trim($args['dt_solicitacao_digitalizacao_ini']) != '') AND (trim($args['dt_solicitacao_digitalizacao_fim']) != '')) ? " AND DATE_TRUNC('day', dt_solicitacao_digitalizacao) BETWEEN TO_DATE('".$args['dt_solicitacao_digitalizacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_solicitacao_digitalizacao_fim']."', 'DD/MM/YYYY')" : '')." 
              ORDER BY dt_solicitacao_digitalizacao DESC;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_solicitacao_digitalizacao)
    {
        $qr_sql = "
            SELECT cd_solicitacao_digitalizacao,
                   cd_gerencia_responsavel,
                   cd_usuario_responsavel,   
                   ds_solicitacao_digitalizacao, 
                   nr_solicitacao_digitalizacao, 
                   TO_CHAR(dt_solicitacao_digitalizacao, 'DD/MM/YYYY') AS dt_solicitacao_digitalizacao
              FROM projetos.solicitacao_digitalizacao 
             WHERE dt_exclusao IS NULL
               AND cd_solicitacao_digitalizacao = ".intval($cd_solicitacao_digitalizacao).";";

        return $this->db->query($qr_sql)->row_array();
    }

    function salvar($args = array())
    {
      $cd_solicitacao_digitalizacao = intval($this->db->get_new_id('projetos.solicitacao_digitalizacao', 'cd_solicitacao_digitalizacao'));

      $qr_sql = "
        INSERT INTO projetos.solicitacao_digitalizacao
             (
                cd_solicitacao_digitalizacao, 
                cd_gerencia_responsavel, 
                cd_usuario_responsavel, 
                ds_solicitacao_digitalizacao, 
                nr_solicitacao_digitalizacao,
                dt_solicitacao_digitalizacao,
                cd_usuario_inclusao,
                cd_usuario_alteracao
             )
        VALUES 
             (
                ".intval($cd_solicitacao_digitalizacao).",
                ".str_escape($args['cd_gerencia_responsavel']).",
                ".str_escape($args['cd_usuario_responsavel']).",
                ".(trim($args['ds_solicitacao_digitalizacao']) != '' ? str_escape($args['ds_solicitacao_digitalizacao']): 'DEFAULT').",
                ".(trim($args['nr_solicitacao_digitalizacao']) != '' ? intval($args['nr_solicitacao_digitalizacao']) : 'DEFAULT').",
                ".(trim($args['dt_solicitacao_digitalizacao']) != '' ? "TO_DATE('".trim($args['dt_solicitacao_digitalizacao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                ".intval($args['cd_usuario']).",
                ".intval($args['cd_usuario'])."
             );";

        $this->db->query($qr_sql);
  
        return $cd_solicitacao_digitalizacao;
    }

    public function atualizar($cd_solicitacao_digitalizacao, $args = array())
    {
      $qr_sql = "
          UPDATE projetos.solicitacao_digitalizacao
             SET cd_gerencia_responsavel      = ".str_escape($args['cd_gerencia_responsavel']).",
                 cd_usuario_responsavel       = ".(trim($args['cd_usuario_responsavel']) != ''? intval($args["cd_usuario_responsavel"]) : 'DEFAULT').", 
                 ds_solicitacao_digitalizacao = ".(trim($args['ds_solicitacao_digitalizacao']) != '' ? str_escape($args['ds_solicitacao_digitalizacao']): 'DEFAULT').",
                 nr_solicitacao_digitalizacao = ".(trim($args['nr_solicitacao_digitalizacao']) != '' ? intval($args['nr_solicitacao_digitalizacao']) : 'DEFAULT').",
                 dt_solicitacao_digitalizacao = ".(trim($args['dt_solicitacao_digitalizacao']) != '' ? "TO_DATE('".trim($args['dt_solicitacao_digitalizacao'])."', 'DD/MM/YYYY')" : "DEFAULT").",              
                 cd_usuario_alteracao         = ".intval($args['cd_usuario']).",
                 dt_alteracao                 = CURRENT_TIMESTAMP
            WHERE cd_solicitacao_digitalizacao  = ".intval($cd_solicitacao_digitalizacao).";";

      $this->db->query($qr_sql);
    }

    public function relatorio_mes($nr_ano)
    {
      $qr_sql = "
          SELECT TO_CHAR(dt_solicitacao_digitalizacao, 'MM') AS mes,
                 SUM(nr_solicitacao_digitalizacao) AS soma
            FROM projetos.solicitacao_digitalizacao
           WHERE dt_exclusao IS NULL
             AND TO_CHAR(dt_solicitacao_digitalizacao, 'YYYY') = '".trim($nr_ano)."'
           GROUP BY TO_CHAR(dt_solicitacao_digitalizacao, 'MM') 
           ORDER BY TO_CHAR(dt_solicitacao_digitalizacao, 'MM') ASC;";
  
      return $this->db->query($qr_sql)->result_array();
    }

    public function relatorio_gerencia($nr_ano)
    {
      $qr_sql = "
          SELECT cd_gerencia_responsavel AS gerencia,
                 SUM(nr_solicitacao_digitalizacao) AS soma
            FROM projetos.solicitacao_digitalizacao 
           WHERE dt_exclusao IS NULL
             AND TO_CHAR(dt_solicitacao_digitalizacao, 'YYYY') = '".trim($nr_ano)."'
           GROUP BY cd_gerencia_responsavel
           ORDER BY cd_gerencia_responsavel ASC;";

      return $this->db->query($qr_sql)->result_array();
    }

    public function relatorio_usuario($nr_ano)
    {
      $qr_sql = "
            SELECT funcoes.get_usuario_nome(cd_usuario_responsavel) AS cd_usuario_responsavel,
                   SUM(nr_solicitacao_digitalizacao) AS soma
              FROM projetos.solicitacao_digitalizacao 
             WHERE dt_exclusao IS NULL
               AND TO_CHAR(dt_solicitacao_digitalizacao, 'YYYY') = '".trim($nr_ano)."'
             GROUP BY cd_usuario_responsavel
             ORDER BY cd_usuario_responsavel ASC;";

      return $this->db->query($qr_sql)->result_array();
    }
}
?>