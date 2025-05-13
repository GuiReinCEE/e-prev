<?php
class Eventos_email_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_evento,
				   fl_tipo,
			       nome,
				   assunto,
				   para,
				   cc,
				   cco,
			       email
			  FROM projetos.eventos e
			 WHERE 1 = 1
			   ".(trim($args['fl_tipo']) != '' ? "AND e.fl_tipo = '".trim($args['fl_tipo'])."'" : "")."
			   ".(trim($args['nome']) != '' ? "AND UPPER(nome) LIKE UPPER('%".trim($args["nome"])."%')" : "")."
			   ".(trim($args['assunto']) != '' ? "AND UPPER(assunto) LIKE UPPER('%".trim($args["assunto"])."%')" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_evento)
	{
		$qr_sql = "
			SELECT cd_evento,
				   fl_tipo,
			       nome,
				   assunto,
				   para,
				   cc,
				   cco,
			       email
			  FROM projetos.eventos
			 WHERE cd_evento = ".intval($cd_evento).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_evento = intval($this->db->get_new_id('projetos.eventos', 'cd_evento'));

		$qr_sql = "
			INSERT INTO projetos.eventos
			     (
			       cd_evento,
				   fl_tipo,
			       nome,
				   assunto,
				   para,
				   cc,
				   cco,
				   email,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
			     )
			VALUES
			     (
			     	".intval($cd_evento).",
					".(trim($args['fl_tipo']) != '' ? str_escape($args['fl_tipo']) : "DEFAULT").",
                    ".(trim($args['nome']) != '' ? str_escape($args['nome']) : "DEFAULT").",
                    ".(trim($args['assunto']) != '' ? str_escape($args['assunto']) : "DEFAULT").",
                    ".(trim($args['para']) != '' ? str_escape($args['para']) : "DEFAULT").",
                    ".(trim($args['cc']) != '' ? str_escape($args['cc']) : "DEFAULT").",
                    ".(trim($args['cco']) != '' ? str_escape($args['cco']) : "DEFAULT").",
			        ".(trim($args['email']) != '' ? str_escape($args['email']) : "DEFAULT").",
			     	".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
			     );";
			     
		$this->db->query($qr_sql);

		return $cd_evento;
	}

	public function atualizar($cd_evento, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.eventos
               SET fl_tipo				= ".(trim($args['fl_tipo']) != '' ? str_escape($args['fl_tipo']) : "DEFAULT").",
				   nome    				= ".(trim($args['nome']) != '' ? str_escape($args['nome']) : "DEFAULT").",
				   assunto 				= ".(trim($args['assunto']) != '' ? str_escape($args['assunto']) : "DEFAULT").",
				   para    				= ".(trim($args['para']) != '' ? str_escape($args['para']) : "DEFAULT").",
				   cc      				= ".(trim($args['cc']) != '' ? str_escape($args['cc']) : "DEFAULT").",
				   cco     				= ".(trim($args['cco']) != '' ? str_escape($args['cco']) : "DEFAULT").",
                   email   				= ".(trim($args['email']) != '' ? str_escape($args['email']) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao 		= CURRENT_TIMESTAMP
             WHERE cd_evento = ".intval($cd_evento).";";    

        $this->db->query($qr_sql);  
	}
	
	public function envia_email_listar($cd_evento, $args = array())
	{
		$qr_sql = "
			SELECT cd_email,
				   de,
			       assunto,
				   TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   TO_CHAR(dt_email_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_email_enviado,
				   funcoes.get_usuario_nome(cd_usuario) AS nome,
				   para,
				   cc,
				   cco,
				   fl_retornou,
                   fl_visualizado
			  FROM projetos.envia_emails
			 WHERE cd_evento = ".intval($cd_evento)."
			   ".(((trim($args['dt_envio_ini']) != '') AND (trim($args['dt_envio_fim']) != '')) ? " AND DATE_TRUNC('day', dt_envio) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
	           ".(((trim($args['dt_email_enviado_ini']) != '') AND (trim($args['dt_email_enviado_fim']) != '')) ? " AND DATE_TRUNC('day', dt_email_enviado) BETWEEN TO_DATE('".$args['dt_email_enviado_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_email_enviado_fim']."', 'DD/MM/YYYY')" : "").";"; 
	 
		return $this->db->query($qr_sql)->result_array();
	}

	public function envia_email($cd_evento, $cd_usuario = 0, $args = array(), $anexo = array())
    {
    	$cd_email = intval($this->db->get_new_id('projetos.envia_emails', 'cd_email'));

      	$qr_sql = "
	        INSERT INTO projetos.envia_emails 
	            (
	            	cd_email,
					dt_envio, 
					de, 
					para, 
					cc, 
					cco, 
					assunto, 
					texto,
					formato,
					cd_evento,
					cd_divulgacao,
					cd_empresa,
					cd_registro_empregado,
					seq_dependencia,
					tp_email,
					cd_usuario
	             )
	        VALUES 
	             (
	             	".intval($cd_email).",
					CURRENT_TIMESTAMP, 
					".(trim($args['de']) != '' ? str_escape($args['de']) : "DEFAULT").",
					".(trim($args['para']) != '' ? "'".trim($args['para'])."'" : "DEFAULT").",                        
					".(trim($args['cc']) != '' ? "'".trim($args['cc'])."'" : "DEFAULT").",  
					".(trim($args['cco']) != '' ? "'".trim($args['cco'])."'" : "DEFAULT").",  
					".(trim($args['assunto']) != '' ? "'".trim($args['assunto'])."'" : "DEFAULT").",  
					".str_escape($args['texto']).",
					'HTML',
					".intval($cd_evento).",
					".(((isset($args['cd_divulgacao'])) AND (trim($args['cd_divulgacao']) != '')) ? intval($args['cd_divulgacao']) : "DEFAULT").", 
					".(((isset($args['cd_empresa'])) AND (trim($args['cd_empresa']) != '')) ? intval($args['cd_empresa']) : "DEFAULT").", 
					".(((isset($args['cd_registro_empregado'])) AND (trim($args['cd_registro_empregado']) != '')) ? intval($args['cd_registro_empregado']) : "DEFAULT").", 
					".(((isset($args['seq_dependencia'])) AND (trim($args['cd_divulgacao']) != '')) ? intval($args['seq_dependencia']) : "DEFAULT").", 
					".(((isset($args['tp_email'])) AND (trim($args['tp_email']) != '')) ? str_escape($args['tp_email']) : "DEFAULT").", 
					".(intval($cd_usuario) > 0 ? intval($cd_usuario) : "DEFAULT")."
	             );";

	    foreach($anexo as $item)
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

      $this->db->query($qr_sql);
    }
}