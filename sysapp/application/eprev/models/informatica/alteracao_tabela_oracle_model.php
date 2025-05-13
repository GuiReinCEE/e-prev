<?php
class Alteracao_tabela_oracle_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar()
    {
    	$qr_sql = "
    		SELECT ora.id_alteracao,
               ora.tabela,
               TO_CHAR(ora.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao
          FROM oracle.fnc_alteracao_tabela_oracle() ora
          JOIN projetos.tabelas_atualizar ta
            ON ta.tabela = ora.tabela
           AND ta.tipo_bd = 'O'
         WHERE (SELECT COUNT(*)
                  FROM informatica.alteracao_tabela_oracle ato
                 WHERE ato.cd_alteracao = ora.id_alteracao
                   AND ato.dt_alteracao = ora.dt_alteracao) = 0
         ORDER BY ora.id_alteracao ASC;";

       return $this->db->query($qr_sql)->result_array(); 
    }

    public function salvar($cd_alteracao, $cd_usuario)
    {
       $cd_alteracao_tabela_oracle = intval($this->db->get_new_id('informatica.alteracao_tabela_oracle', 'cd_alteracao_tabela_oracle'));

       $qr_sql = "
            INSERT INTO informatica.alteracao_tabela_oracle
                (
                    cd_alteracao_tabela_oracle,
                    cd_alteracao, 
                    dt_alteracao, 
                    dt_inclusao, 
                    cd_usuario_inclusao
                )
                VALUES
                (
                    ".intval($cd_alteracao_tabela_oracle).",
                    ".intval($cd_alteracao).",
                    (SELECT ora.dt_alteracao FROM oracle.fnc_alteracao_tabela_oracle() ora WHERE ora.id_alteracao = ".intval($cd_alteracao)."),
                    CURRENT_TIMESTAMP,
                    ".intval($cd_usuario)."
                );";

        $this->db->query($qr_sql);
    }

    public function set_descricao($cd_alteracao_tabela_oracle, $args = array())
    {
        $qr_sql = "
            UPDATE informatica.alteracao_tabela_oracle
               SET ds_descricao = ".str_escape($args['ds_descricao'])."
             WHERE cd_alteracao_tabela_oracle = ".intval($cd_alteracao_tabela_oracle).";";

        $this->db->query($qr_sql);
    }

    public function listar_confirmadas($args = array())
    {
        $qr_sql = "
            SELECT TO_CHAR(ato.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(ato.cd_usuario_inclusao) AS usuario_inclusao,
                   TO_CHAR(ato.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
                   ora.tabela,
                   ato.ds_descricao,
                   ato.cd_alteracao_tabela_oracle
              FROM informatica.alteracao_tabela_oracle ato
              JOIN oracle.fnc_alteracao_tabela_oracle() ora
                ON ora.id_alteracao = ato.cd_alteracao
              JOIN projetos.tabelas_atualizar ta
                ON ta.tabela = ora.tabela
               AND ta.tipo_bd = 'O'
            ".(((trim($args['dt_alteracao_ini']) != '') AND (trim($args['dt_alteracao_fim']) != '')) ? " WHERE DATE_TRUNC('day', ora.dt_alteracao) BETWEEN TO_DATE('".$args['dt_alteracao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_alteracao_fim']."', 'DD/MM/YYYY')" : "")."
              ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? " AND DATE_TRUNC('day', ato.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "").";";

        return $this->db->query($qr_sql)->result_array(); 
    }
}
?>