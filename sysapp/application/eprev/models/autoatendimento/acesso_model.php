<?php
class Acesso_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT a.ds_uri,
				   TO_CHAR(a.dt_acesso, 'DD/MM/YYYY HH24:MI:SS') AS dt_acesso,
			       l.cd_registro_empregado,
				   l.cd_empresa,
				   l.seq_dependencia,
				   TO_CHAR(l.dt_login, 'DD/MM/YYYY HH24:MI:SS') AS dt_login,
				   funcoes.get_usuario_nome(l.cd_usuario) AS nome,
				   p.nome AS nome_participante
			  FROM autoatendimento.acesso a
			  JOIN autoatendimento.login l
			    ON l.cd_login = a.cd_login
			  JOIN public.participantes p
			    ON p.cd_empresa            = l.cd_empresa
			   AND p.cd_registro_empregado = l.cd_registro_empregado
			   AND p.seq_dependencia       = l.seq_dependencia
			 WHERE 1 = 1
			   ".(trim($args['cd_empresa']) != '' ? "AND l.cd_empresa = ".intval($args['cd_empresa']) : "")."			   
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND l.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
			   ".(trim($args['seq_dependencia']) != '' ? "AND l.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(((trim($args['dt_login_ini']) != '') AND (trim($args['dt_login_fim']) != '')) ? " AND DATE_TRUNC('day', l.dt_login) BETWEEN TO_DATE('".$args['dt_login_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_login_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_acesso_ini']) != '') AND (trim($args['dt_acesso_fim']) != '')) ? " AND DATE_TRUNC('day', a.dt_acesso) BETWEEN TO_DATE('".$args['dt_acesso_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_acesso_fim']."', 'DD/MM/YYYY')" : "").";";
			   
		return $this->db->query($qr_sql)->result_array();
	}
}