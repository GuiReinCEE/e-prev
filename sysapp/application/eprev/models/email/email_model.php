<?php
class email_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function anexoListar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_email_anexo, 
			       arquivo_nome, 
				   arquivo 
			  FROM email.email_anexo 
			 WHERE dt_exclusao IS NULL
			   AND cd_email = ".intval($args['cd_email']).";";

		#echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }
	
    function emailIncluir($args=array())
    {
        $cd_email = intval($this->db->get_new_id('projetos.envia_emails', 'cd_email'));

		$qr_sql = "
					INSERT INTO projetos.envia_emails 
						 (
							cd_email,
							dt_schedule_email,
							de, 
							para, 
							cc, 
							cco, 
							assunto, 
							texto,
							cd_empresa,
							cd_registro_empregado,
							seq_dependencia,							
							cd_evento,
							formato,
							fl_comprova,
							tp_email,
							cd_usuario
						 )
					VALUES 
						 (
							".intval($cd_email).",
							".(trim($args['dt_agendado']) != '' ? "TO_TIMESTAMP('".trim($args['dt_agendado'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
							".(trim($args['de']) != '' ? str_escape($args['de']) : "DEFAULT").",
							".(trim($args['para']) != '' ? str_escape($args['para']) : "DEFAULT").",                    
							".(trim($args['cc']) != '' ? str_escape($args['cc']) : "DEFAULT").",                      
							".(trim($args['cco']) != '' ? str_escape($args['cco']) : "DEFAULT").",
							".(trim($args['assunto']) != '' ? str_escape($args['assunto']) : "DEFAULT").",
							".(trim($args['conteudo']) != '' ? str_escape($args['conteudo']) : "DEFAULT").",
							".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
							".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
							".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
							".(trim($args['cd_evento']) != '' ? intval($args['cd_evento']) : "DEFAULT").",
							".(trim($args['formato']) != '' ? str_escape($args['formato']) : "DEFAULT").",
							".(trim($args['fl_comprova']) == 'S' ? 'S' : "DEFAULT").",
							".(trim($args['tp_email']) != '' ? str_escape($args['tp_email']) : "F").",
							(CASE WHEN COALESCE(funcoes.get_usuario('".trim($args['usuario'])."'), 999999) > 0 
					             THEN COALESCE(funcoes.get_usuario('".trim($args['usuario'])."'), 999999)
					             ELSE 999999
					        END)
						  );";

		foreach($args['ar_anexo'] as $item)
		{
			$qr_sql .= "
				INSERT INTO email.email_anexo(cd_usuario_inclusao, cd_email, arquivo_nome, arquivo)
				VALUES 
				     (
						999999,
						".intval($cd_email).",
						'".trim($item['arquivo_nome'])."',
						'".trim($item['arquivo'])."'
					 );";
		}
				  
		#echo "<PRE>".$qr_sql."</PRE>";exit;
        $this->db->query($qr_sql);
		
		return $cd_email;
    }	
}
?>
