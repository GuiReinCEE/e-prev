<?php

class formulario_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "SELECT f.cd_formulario,
                          f.arquivo_nome,
                          f.arquivo,
                          p.sigla,
                          TO_CHAR(f.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                          uc.nome
                     FROM extranet.formulario f
                     JOIN public.patrocinadoras p
                       ON f.cd_empresa = p.cd_empresa
                     JOIN projetos.usuarios_controledi uc
                       ON uc.codigo = f.cd_usuario_inclusao
                    WHERE f.dt_exclusao IS NULL
                    ".(trim($args['cd_empresa']) != '' ? " AND f.cd_empresa = ". intval($args['cd_empresa']) : '')."
                    ".(trim($args['cd_plano']) != '' ? " AND f.cd_plano = ". intval($args['cd_plano']) : '')."
                    ORDER BY cd_formulario";

        #echo "<pre>$qr_sql</pre>";
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
       $qr_sql = "INSERT INTO extranet.formulario
                         (
                            cd_empresa,
                            cd_plano,
                            arquivo_nome,
                            arquivo,
                            cd_usuario_inclusao
                         )
                  VALUES
                         (
                            " . (trim($args['cd_empresa']) == "" ? "DEFAULT" : intval($args['cd_empresa'])) . ",
                            " . (trim($args['cd_plano']) == "" ? "DEFAULT" : intval($args['cd_plano'])) . ",
                            " . (trim($args['arquivo_nome']) == "" ? "DEFAULT" : "'".trim($args['arquivo_nome'])."'") . ",
                            " . (trim($args['arquivo']) == "" ? "DEFAULT" : "'".trim($args['arquivo'])."'") . ",
                            " . (trim($args['cd_usuario_inclusao']) == "" ? "DEFAULT" : intval($args['cd_usuario_inclusao'])) . "
                         )";

        #echo "<pre>$qr_sql</pre>";
        $result = $this->db->query($qr_sql); 
    }
    
    function excluir(&$result, $args=array())
    {
        $qr_sql = "UPDATE extranet.formulario
                      SET cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao']).",
                          dt_exclusao = CURRENT_TIMESTAMP
                    WHERE cd_formulario = ".intval($args['cd_formulario']);
        $this->db->query($qr_sql); 
    }
}