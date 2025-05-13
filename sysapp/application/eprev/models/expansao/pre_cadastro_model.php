<?php

class Pre_cadastro_model extends Model
{

    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT pc.cd_pre_cadastro,
                   TO_CHAR( pc.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
                   pc.ds_nome,
                   pc.ds_email,
                   pc.nr_telefone,
                   pc.nr_matricula,
                   pc.nr_cpf,
                   TO_CHAR(pc.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
                   pc.ds_duvida,
                   (SELECT pca.cd_enviado
                      FROM expansao.pre_cadastro_acompanhamento pca
                     WHERE pca.cd_pre_cadastro = pc.cd_pre_cadastro
                       AND pca.dt_exclusao IS NULL
                     ORDER BY pca.dt_inclusao DESC
                     LIMIT 1) AS cd_enviado,
                   (SELECT pca.observacao
                      FROM expansao.pre_cadastro_acompanhamento pca
                     WHERE pca.cd_pre_cadastro = pc.cd_pre_cadastro
                       AND pca.dt_exclusao IS NULL
                     ORDER BY pca.dt_inclusao DESC
                     LIMIT 1) AS observacao
              FROM expansao.pre_cadastro pc
             WHERE pc.tp_pre_cadastro='".$args["tp_pre_cadastro"]."'
              ".(((trim($args['dt_inclusao_inicial']) != "") and (trim($args['dt_inclusao_final']) != "")) ? " AND CAST(pc.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args['dt_inclusao_inicial']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_final']."', 'DD/MM/YYYY')" : "")."
              ".(((trim($args['dt_acompanhamento_inicial']) != "") and (trim($args['dt_acompanhamento_final']) != "")) ? " AND CAST(pca.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args['dt_acompanhamento_inicial']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_acompanhamento_final']."', 'DD/MM/YYYY')" : "")."
              ".(trim($args['cd_enviado']) != '' ? " AND 0 < (SELECT COUNT(*)
                                                                FROM expansao.pre_cadastro_acompanhamento pca
                                                               WHERE pca.cd_pre_cadastro = pc.cd_pre_cadastro
                                                                 AND pca.cd_enviado ='". trim($args['cd_enviado'])."'
                                                                 AND pca.dt_exclusao IS NULL)"
                                                                                    : '')."
              ".(trim($args['nr_cpf']) != '' ? " AND pc.nr_cpf ='". trim($args['nr_cpf'])."'" : '')."
              ".(trim($args['ds_nome']) != '' ? " AND UPPER(pc.ds_nome) LIKE UPPER('%". trim($args['ds_nome'])."%')" : '')."
             ";

        $result = $this->db->query($qr_sql);
    }
    
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_pre_cadastro,
                   ds_nome,
                   ds_email,
                   nr_telefone,
                   nr_matricula,
                   nr_cpf,
                   TO_CHAR(dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
                   ds_duvida
              FROM expansao.pre_cadastro
             WHERE cd_pre_cadastro=".intval($args["cd_pre_cadastro"]);
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        $retorno = 0;

        if (intval($args['cd_pre_cadastro']) > 0) {
            $qr_sql = "
                    UPDATE expansao.pre_cadastro
                       SET ds_nome              = " . (trim($args['ds_nome']) == "" ? "DEFAULT" : "'" . (trim($args['ds_nome'])) . "'") . ",
                           ds_email             = " . (trim($args['ds_email']) == "" ? "DEFAULT" : "'" . (trim($args['ds_email'])) . "'") . ",
                           nr_telefone          = " . (trim($args['nr_telefone']) == "" ? "DEFAULT" : "'" . (trim($args['nr_telefone'])) . "'") . ",
                           nr_matricula         = " . (trim($args['nr_matricula']) == "" ? "DEFAULT" :"'". $args['nr_matricula']. "'") . ",
                           nr_cpf               = " . (trim($args['nr_cpf']) == "" ? "DEFAULT" : "'". $args['nr_cpf']. "'") . ",
                           ds_duvida            = " . (trim($args['ds_duvida']) == "" ? "DEFAULT" : "'". trim($args['ds_duvida']). "'") . ",
                           dt_nascimento        = " . (trim($args['dt_nascimento']) == "" ? "DEFAULT" : "TO_DATE('".trim($args['dt_nascimento'])."', 'DD/MM/YYYY')") . "
                     WHERE cd_pre_cadastro = " . intval($args['cd_pre_cadastro']) . "
                ";

            $this->db->query($qr_sql);
            $retorno = $args['cd_pre_cadastro'];
        } 
        else
        {
            $new_id = intval($this->db->get_new_id("expansao.pre_cadastro", "cd_pre_cadastro"));
            $retorno = $new_id;
            $qr_sql = "
                INSERT INTO expansao.pre_cadastro
                       (
                         cd_pre_cadastro,
                         ds_nome,
                         ds_email,
                         nr_telefone,
                         nr_matricula,
                         nr_cpf,
                         ds_duvida,
                         dt_nascimento
                       )
                  VALUES
                       (
                         " . $new_id . ",
                         " . (trim($args['ds_nome']) == "" ? "DEFAULT" : "'" . (trim($args['ds_nome'])) . "'") . ",
                         " . (trim($args['ds_email']) == "" ? "DEFAULT" : "'" . (trim($args['ds_email'])) . "'") . ",
                         " . (trim($args['nr_telefone']) == "" ? "DEFAULT" : "'" . (trim($args['nr_telefone'])) . "'") . ",
                         " . (trim($args['nr_matricula']) == "" ? "DEFAULT" : "'" . $args['nr_matricula'] . "'") . ",
                         " . (trim($args['nr_cpf']) == "" ? "DEFAULT" : "'" .$args['nr_cpf'] . "'") . ",
                         " . (trim($args['ds_duvida']) == "" ? "DEFAULT" : "'". trim($args['ds_duvida']). "'") . ",
                         " . (trim($args['dt_nascimento']) == "" ? "DEFAULT" : "TO_DATE('".trim($args['dt_nascimento'])."', 'DD/MM/YYYY')") . "
                       )
                ";

            $this->db->query($qr_sql);
        }

        return $retorno;
    }
    
    function acompanhamento(&$result, $args=array())
    {
        $qr_sql = "
            SELECT pca.cd_pre_cadastro_acompanhamento,
                   TO_CHAR( pca.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
                   u.nome,
                   pca.cd_enviado,
                   pca.observacao
              FROM expansao.pre_cadastro_acompanhamento pca
              JOIN projetos.usuarios_controledi u
                ON u.codigo = pca.cd_usuario_inclusao
             WHERE pca.cd_pre_cadastro = ".intval($args['cd_pre_cadastro'])."
               AND pca.dt_exclusao IS NULL ";
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar_acompanhamento(&$result, $args=array())
    {
        $qr_sql = "
                INSERT INTO expansao.pre_cadastro_acompanhamento
                       (
                         cd_pre_cadastro,
                         cd_enviado,
                         observacao,
                         cd_usuario_inclusao
                       )
                  VALUES
                       (
                         " . intval($args['cd_pre_cadastro']) . ",
                         " . (trim($args['cd_enviado']) == "" ? "DEFAULT" : "'" . (trim($args['cd_enviado'])) . "'") . ",
                         " . (trim($args['observacao']) == "" ? "DEFAULT" : "'" . (trim($args['observacao'])) . "'") . ",
                         " . intval($args['cd_usuario']) . "
                       )
                ";
        
        $this->db->query($qr_sql);
    }
    

}

?>