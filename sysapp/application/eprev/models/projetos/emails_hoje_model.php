<?php
class Emails_hoje_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function get_evento()
	{
		$qr_sql = "
			SELECT DISTINCT e.cd_evento AS value,
				   e.nome AS text
			  FROM projetos.emails_hoje eh
			  JOIN projetos.eventos e
			    ON e.cd_evento = eh.cd_evento
			 ORDER BY e.cd_evento;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_divulgacao()
	{
		$qr_sql = "
			SELECT DISTINCT d.cd_divulgacao AS value,
				   d.assunto AS text
			  FROM projetos.emails_hoje eh
			  LEFT JOIN projetos.divulgacao d
			    ON d.cd_divulgacao = eh.cd_divulgacao
			 ORDER BY d.cd_divulgacao;";
			 
		return $this->db->query($qr_sql)->result_array();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT eh.cd_email, 
				   TO_CHAR(eh.dt_envio, 'dd/mm/yyyy HH24:MI') AS dt_email, 
				   TO_CHAR(eh.dt_email_enviado, 'dd/mm/yyyy HH24:MI') AS dt_envio, 
				   eh.assunto,
				   eh.para,
				   eh.cc,
				   eh.cco,
				   eh.cd_evento,
				   e.nome AS evento,
				   d.assunto AS divulgacao
			  FROM projetos.emails_hoje eh
			  LEFT JOIN projetos.eventos e
			    ON e.cd_evento = eh.cd_evento
			  LEFT JOIN projetos.divulgacao d
			    ON d.cd_divulgacao = eh.cd_divulgacao			  
			 WHERE 1 = 1
				   ".(trim($args['assunto']) != '' ? "AND UPPER(eh.assunto) LIKE UPPER('%".trim($args['assunto'])."%')" : "")."
                   ".(trim($args['fl_enviado']) == 'S' ? "AND eh.dt_email_enviado IS NOT NULL" : (trim($args['fl_enviado']) == 'N' ? "AND eh.dt_email_enviado IS NULL" : ""))."
                   ".(intval($args['cd_evento']) != '' ? "AND eh.cd_evento = ".intval($args['cd_evento']) : "")."
                   ".(intval($args['cd_divulgacao']) != '' ? "AND eh.cd_divulgacao = ".intval($args['cd_divulgacao']) : "").";";
			
		return $this->db->query($qr_sql)->result_array();
	}
}
?>