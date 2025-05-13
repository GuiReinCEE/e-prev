<?php
class sms_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function getSMS(&$result, $args=array())
    {
        $qr_sql = "
					SELECT cd_sms,
                           nr_telefone,
					       ds_assunto, 
						   ds_conteudo
					  FROM sms.sms 
					 WHERE dt_exclusao IS NULL
					   AND dt_enviado IS NULL
					   AND COALESCE(dt_agenda, CURRENT_TIMESTAMP) <= CURRENT_TIMESTAMP
					   AND cd_sms = ".intval($args['cd_sms'])."
                  ";
		#echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }
	
	function setEnvioSMS(&$result, $args=array())
	{
		$qr_sql = "
					UPDATE sms.sms
					   SET ds_retorno = '".trim($args['ws_retorno'])."',
						   dt_enviado = CAST(timeofday() AS TIMESTAMP)
					 WHERE cd_sms = ".intval($args['cd_sms'])."		
		          ";
		$result = $this->db->query($qr_sql);		  
	}
	
    function smsEletroIncluir(&$result, $args=array())
    {
        $qr_sql = "
					SELECT id_sms 
					  FROM sms.sms_incluir(
						".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "NULL").",
						".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "NULL").",
						".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "NULL").",
						".(trim($args['para']) != '' ? intval($args['para']) : "NULL").", -- numero do celular com DDD
						".(trim($args['assunto']) != '' ? str_escape($args['assunto']) : "NULL").", -- assunto
						".(trim($args['conteudo']) != '' ? str_escape($args['conteudo']) : "NULL").", -- conteudo
						".(intval($args['cd_sms_tipo']) > 0 ? intval($args['cd_sms_tipo']) : "2").", --tipo do envio (sms.sms_tipo)
						".(trim($args['dt_agendado']) != '' ? "(TO_TIMESTAMP('".trim($args['dt_agendado'])."', 'DD/MM/YYYY HH24:MI'))::TIMESTAMP" : "NULL").", --data futura
						(SELECT COALESCE((SELECT funcoes.get_usuario(LOWER('".trim($args['usuario'])."'))::INTEGER),999999)) -- usuario que enviou (usar NULL quando executado por rotina)
					);
                  ";
		#echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }	
}
?>
