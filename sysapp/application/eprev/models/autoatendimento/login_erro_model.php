<?php
class Login_erro_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT TO_CHAR(le.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   le.cd_registro_empregado,
				   le.cd_empresa,
				   le.seq_dependencia,
			       le.cpf,
				   le.ds_login_erro,
				   funcoes.get_usuario_nome(le.cd_usuario) AS nome,
				   p.nome AS nome_participante
			  FROM autoatendimento.login_erro le
			  LEFT JOIN public.participantes p
			    ON p.cd_empresa            = le.cd_empresa
			   AND p.cd_registro_empregado = le.cd_registro_empregado
			   AND p.seq_dependencia       = le.seq_dependencia
			 WHERE 1 = 1
			   ".(trim($args['cd_empresa']) != '' ? "AND le.cd_empresa = ".intval($args['cd_empresa']) : "")."			   
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND le.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
			   ".(trim($args['seq_dependencia']) != '' ? "AND le.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? " AND DATE_TRUNC('day', le.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "").";";
			   
		return $this->db->query($qr_sql)->result_array();
	}
}