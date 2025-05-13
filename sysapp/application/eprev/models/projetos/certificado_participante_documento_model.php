<?php
class Certificado_participante_documento_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cpd.cd_certificado_participante_documento,
                   cpd.cd_documento,
                   td.nome_documento,
                   cpd.fl_verificar,
                   p.sigla,
                   TO_CHAR(cpd.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS')  AS dt_inclusao,
                   uc.nome
              FROM projetos.certificado_participante_documento  cpd
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = cpd.cd_usuario_inclusao 
              JOIN public.tipo_documentos td
                ON td.cd_tipo_doc = cpd.cd_documento
              JOIN public.patrocinadoras p
                ON p.cd_empresa = cpd.cd_empresa
             WHERE cpd.dt_exclusao IS NULL
               ".(trim($args['cd_empresa']) != '' ? "AND p.cd_empresa = ".trim($args['cd_empresa']) : "")."
             ORDER BY cpd.cd_documento";
        
        $result = $this->db->query($qr_sql);
    }
        
    function carrega(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_certificado_participante_documento,
                   cd_documento,
                   cd_empresa,
                   fl_verificar
              FROM projetos.certificado_participante_documento 
             WHERE dt_exclusao IS NULL
               AND cd_certificado_participante_documento = ". intval($args['cd_certificado_participante_documento']);
        
        $result = $this->db->query($qr_sql);
    }
    
    function get_patrocinadoras(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_empresa AS value,
                   sigla AS text
              FROM public.patrocinadoras 
             ORDER BY cd_empresa";
        
        $result = $this->db->query($qr_sql);
    }
    
    function salvar(&$result, $args=array())
    {
        if(intval($args['cd_certificado_participante_documento']) > 0)
        {
            $qr_sql = "
                UPDATE projetos.certificado_participante_documento 
                   SET cd_documento = ".intval($args['cd_documento']).",
                       cd_empresa = ".intval($args['cd_empresa']).",
                       fl_verificar = '".trim($args['fl_verificar'])."'
                 WHERE cd_certificado_participante_documento = ". invtal($args['cd_certificado_participante_documento']);
        }
        else
        {
            $qr_sql = "
                INSERT INTO projetos.certificado_participante_documento 
                     (
                       cd_documento,
                       cd_empresa,
                       fl_verificar,
                       cd_usuario_inclusao
                     )
                VALUES
                     (
                       ".intval($args['cd_documento']).",
                       ".intval($args['cd_empresa']).",
                       '".trim($args['fl_verificar'])."',
                       ".intval($args['cd_usuario'])."
                     )";
        }
        
        $this->db->query($qr_sql);
    }
    
    function excluir(&$result, $args=array())
    {
        $qr_sql = "
                UPDATE projetos.certificado_participante_documento 
                   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                       dt_exclusao = CURRENT_TIMESTAMP
                 WHERE cd_certificado_participante_documento = ". intval($args['cd_certificado_participante_documento']);
        
        $this->db->query($qr_sql);
    }
        
}
?>