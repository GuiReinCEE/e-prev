<?php
class Login_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT l.cd_login,
				   TO_CHAR(l.dt_login, 'DD/MM/YYYY HH24:MI:SS') AS dt_login,
				   l.cd_registro_empregado,
				   l.cd_empresa,
				   l.seq_dependencia,
			       l.nr_ip,
				   l.ds_agent,
				   l.ds_platform,
				   funcoes.get_usuario_nome(l.cd_usuario) AS nome,
				   p.nome AS nome_participante
			  FROM autoatendimento.login l
			  JOIN public.participantes p
			    ON p.cd_empresa            = l.cd_empresa
			   AND p.cd_registro_empregado = l.cd_registro_empregado
			   AND p.seq_dependencia       = l.seq_dependencia
			 WHERE 1 = 1
			    ".(trim($args['cd_empresa']) != '' ? "AND l.cd_empresa = ".intval($args['cd_empresa']) : "")."			   
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND l.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
			   ".(trim($args['seq_dependencia']) != '' ? "AND l.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(((trim($args['dt_login_ini']) != '') AND (trim($args['dt_login_fim']) != '')) ? " AND DATE_TRUNC('day', l.dt_login) BETWEEN TO_DATE('".$args['dt_login_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_login_fim']."', 'DD/MM/YYYY')" : "").";";
			   
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function carregar($cd_login)
	{
		$qr_sql = "
			 SELECT l.cd_login,
					l.cd_registro_empregado,
				    l.cd_empresa,
				    l.seq_dependencia,
					TO_CHAR(l.dt_login, 'DD/MM/YYYY HH24:MI:SS') AS dt_login,
					funcoes.get_usuario_nome(l.cd_usuario) AS nome,
					p.nome AS nome_participante
			   FROM autoatendimento.login l
			   JOIN public.participantes p
			     ON p.cd_empresa            = l.cd_empresa
			    AND p.cd_registro_empregado = l.cd_registro_empregado
			    AND p.seq_dependencia       = l.seq_dependencia
			  WHERE cd_login = ".intval($cd_login).";";
			   
			return $this->db->query($qr_sql)->row_array();
	}
	
	public function acesso_listar($cd_login)
	{
		$qr_sql = "
			SELECT ds_uri,
				   TO_CHAR(dt_acesso, 'DD/MM/YYYY HH24:MI:SS') AS dt_acesso
			  FROM autoatendimento.acesso
			 WHERE cd_login = ".intval($cd_login).";";
			 
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function acesso_quebrado_listar($cd_login)
	{
		$qr_sql = "
			SELECT ds_uri,
				   TO_CHAR(dt_acesso_quebrado, 'DD/MM/YYYY HH24:MI:SS') AS dt_acesso_quebrado
			  FROM autoatendimento.acesso_quebrado
			 WHERE cd_login = ".intval($cd_login).";";
			 
		return $this->db->query($qr_sql)->result_array();
	}
}