<?php
class log_postgresql_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT pg_logfile_lista AS arquivo
					  FROM funcoes.pg_logfile_lista()
			      ";
        $result = $this->db->query($qr_sql);
    }
	
	
    function listarLog(&$result, $args=array())
    {
        $qr_sql = "
					SELECT linha
					  FROM funcoes.pg_logfile('".$args['arquivo']."')
			      ";
        $result = $this->db->query($qr_sql);
    }	
}
?>