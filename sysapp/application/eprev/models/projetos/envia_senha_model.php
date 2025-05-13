<?php
class envia_senha_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function get_tipo_senha(&$result, $args=array())
    {
        $qr_sql = "SELECT projetos.participante_tipo_senha(
                            ".intval($args['cd_empresa']).", 
                            ".intval($args['cd_registro_empregado']).",
                            ".intval($args['seq_dependencia']).")";
        
        $result = $this->db->query($qr_sql);
    }
    
    function envia_senha(&$result, $args=array())
    {
        $qr_sql = "SELECT projetos.participante_envia_senha(
                            ".intval($args['cd_empresa']).", 
                            ".intval($args['cd_registro_empregado']).",
                            ".intval($args['seq_dependencia']).")";
        
        $result = $this->db->query($qr_sql);
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
            SELECT ee.cd_email, 
                   ee.cd_empresa,
                   ee.cd_registro_empregado,
                   ee.seq_dependencia,
                   ee.para,
                   ee.cc,
                   ee.cco,
                   TO_CHAR(ee.dt_envio, 'dd/mm/yyyy HH24:MI') AS dt_email, 
                   TO_CHAR(ee.dt_email_enviado, 'dd/mm/yyyy HH24:MI') AS dt_envio, 
                   ee.assunto,
                   ee.fl_retornou AS fl_retorno,
                   p.nome
              FROM projetos.envia_emails ee	
              JOIN public.participantes p
                ON ee.cd_registro_empregado = p.cd_registro_empregado
               AND ee.cd_empresa            = p.cd_empresa
               AND ee.seq_dependencia       = p.seq_dependencia
             WHERE ee.cd_evento = 112
               ".(trim($args['cd_empresa']) != '' ? " AND ee.cd_empresa  = ".$args['cd_empresa'] : '' )."
               ".(trim($args['cd_registro_empregado']) != '' ? " AND ee.cd_registro_empregado  = ".$args['cd_registro_empregado'] : '' )."
               ".(trim($args['seq_dependencia']) != '' ? " AND ee.seq_dependencia  = ".$args['seq_dependencia'] : '' )."
               ".(((trim($args["dt_email_ini"]) != "") and (trim($args["dt_email_fim"]) != "")) ? "AND DATE_TRUNC('day', ee.dt_envio) BETWEEN TO_DATE('".$args["dt_email_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_email_fim"]."','DD/MM/YYYY')"  : '')."
               ".(((trim($args["dt_envio_ini"]) != "") and (trim($args["dt_envio_fim"]) != "")) ? "AND DATE_TRUNC('day', ee.dt_email_enviado) BETWEEN TO_DATE('".$args["dt_envio_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_envio_fim"]."','DD/MM/YYYY')"  : '')."
             ORDER BY COALESCE(ee.dt_email_enviado,ee.dt_envio) DESC, ee.assunto ASC 		
               ";

        $result = $this->db->query($qr_sql);
    }
    
}
?>