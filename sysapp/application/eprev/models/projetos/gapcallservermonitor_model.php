<?php
class gapcall_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
	
    public function testaCliente(&$result, $args=array())
    {
        $qr_sql = "
                    SELECT CASE WHEN dt_login_callcenter IS NOT NULL
                                THEN 'S'
                                ELSE 'N'
                           END AS fl_logado
                      FROM projetos.usuarios_controledi
                     WHERE nr_ip_callcenter = '".trim($args['ip_cliente'])."'
                  ";

        $result = $this->db->query($qr_sql);
    }

    public function iniciaConexao($args=array())
    {
        $qr_sql = "
                    UPDATE projetos.usuarios_controledi
                       SET dt_monitor_callcenter = CURRENT_TIMESTAMP
                     WHERE nr_ip_callcenter    = '".trim($args['ip_cliente'])."'
                       AND nr_ramal_callcenter = ".intval($args['nr_ramal'])."
                  ";

        $this->db->query($qr_sql);
    }

    public function encerrar($args=array())
    {
        $qr_sql = "
                    UPDATE projetos.usuarios_controledi
                       SET dt_monitor_callcenter = NULL
                     WHERE nr_ip_callcenter    = '".trim($args['ip_cliente'])."'
                       AND nr_ramal_callcenter = ".intval($args['nr_ramal'])."
                  ";

        $this->db->query($qr_sql);
    }	
}
?>