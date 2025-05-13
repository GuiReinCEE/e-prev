<?php
class agenda_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function incluir(&$result, $args=array())
    {
        $qr_sql = "
					SELECT agendar AS cd_agenda
					  FROM agenda.agendar(
											".intval($args['cd_agenda']).",
											(SELECT funcoes.get_usuario(LOWER('".trim($args['usuario'])."'))),
											TO_TIMESTAMP('".trim($args['dt_agenda_ini'])." ".trim($args['hr_ini'])."', 'DD/MM/YYYY HH24:MI'),
											TO_TIMESTAMP('".trim($args['dt_agenda_fim'])." ".trim($args['hr_fim'])."', 'DD/MM/YYYY HH24:MI'),
											'".trim($args['assunto'])."',
											'".trim($args['local'])."',
											'".trim($args['texto'])."',
											'".trim($args['fl_ocupado'])."',
											".intval($args['qt_min_lembrete']).",
											'".trim($args['participantes'])."'
										 );					
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }
	
    function excluir(&$result, $args=array())
    {
        $qr_sql = "
					SELECT excluir
					  FROM agenda.excluir(".intval($args['cd_agenda']).", (SELECT funcoes.get_usuario(LOWER('".trim($args['usuario'])."'))));
				
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }	

}
?>
