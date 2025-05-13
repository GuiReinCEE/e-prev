<?php
class Usuario_acesso_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT ua.cd_usuario,	
				   TO_CHAR(ua.dt_exclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao,
				   TO_CHAR(ua.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(ua.cd_usuario) AS nome,
				   funcoes.get_usuario_nome(ua.cd_usuario_inclusao) AS cd_usuario_inclusao,
				   funcoes.get_usuario_nome(ua.cd_usuario_exclusao) AS cd_usuario_exclusao,
				   (SELECT uc.divisao
					  FROM projetos.usuarios_controledi uc
					 WHERE ua.cd_usuario = uc.codigo) AS gerencia
			  FROM autoatendimento.usuario_acesso ua					
			 WHERE 1 = 1
				   ".(trim($args['fl_situacao']) == 'E' ? 'AND ua.dt_exclusao IS NOT NULL' : '')."
                   ".(trim($args['fl_situacao']) == 'A' ? 'AND ua.dt_exclusao IS NULL' : '').";";
			   
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function salvar($args = array())
	{
		if(intval($args['cd_usuario']) > 0)
		{
			$qr_sql = "
				SELECT COUNT(*) AS fl_existe
				  FROM autoatendimento.usuario_acesso
				 WHERE cd_usuario  = ".intval($args['cd_usuario'])."
				   AND dt_exclusao IS NULL;";
				   
			$ar_usu = $this->db->query($qr_sql)->row_array();
			
			if(intval($ar_usu['fl_existe']) == 0)
			{
				$qr_sql = " 
					INSERT INTO autoatendimento.usuario_acesso
						 (
						   cd_usuario,
						   cd_usuario_inclusao,
						   cd_usuario_alteracao
						 )
					VALUES 
						 (
						   ".intval($args['cd_usuario']).",
						   ".intval($args['cd_usuario_inclusao']).",
						   ".intval($args['cd_usuario_inclusao'])."
						 );";
						
				$this->db->query($qr_sql);
			}
		}
	}	
	
	public function excluir($cd_usuario, $cd_usuario_exclusao)
	{
		$qr_sql = " 
			UPDATE autoatendimento.usuario_acesso
			   SET dt_exclusao          = CURRENT_TIMESTAMP, 
				   cd_usuario_exclusao  = ".intval($cd_usuario_exclusao).",
				   dt_alteracao         = CURRENT_TIMESTAMP, 
				   cd_usuario_alteracao = ".intval($cd_usuario_exclusao)."	   
			 WHERE cd_usuario = ".intval($cd_usuario).";"; 
			 
		$this->db->query($qr_sql); 
	}
	
	public function reativar($cd_usuario, $cd_usuario_alteracao)
	{
		$qr_sql = " 
			UPDATE autoatendimento.usuario_acesso
			   SET dt_exclusao          = NULL,
				   cd_usuario_exclusao  = NULL,
				   dt_alteracao         = CURRENT_TIMESTAMP, 
				   cd_usuario_alteracao = ".intval($cd_usuario_alteracao)."	
			 WHERE cd_usuario = ".intval($cd_usuario).";"; 
			 
		$this->db->query($qr_sql); 
	}
	
	public function acesso($cd_usuario)
	{
		$qr_sql = "
			SELECT nome || ' (' || divisao || ')' AS nome,
				   codigo
			  FROM projetos.usuarios_controledi
			 WHERE codigo = ".$cd_usuario.";";
				
		return $this->db->query($qr_sql)->row_array();
	}
	
	public function acesso_listar($args = array())
	{
		$qr_sql = "
			SELECT l.cd_login,
				   l.nr_ip,
				   l.cd_empresa,
				   l.cd_registro_empregado,
				   l.seq_dependencia,
				   funcoes.get_usuario_nome(l.cd_usuario) AS nome,
				   TO_CHAR(a.dt_acesso,'DD/MM/YYYY HH24:MI:SS') AS dt_acesso,
				   TO_CHAR(l.dt_login,'DD/MM/YYYY HH24:MI:SS') AS dt_login,
				   a.ds_uri
			  FROM autoatendimento.login l 
			  JOIN autoatendimento.acesso a 
				ON a.cd_login = l.cd_login 
			 WHERE 1=1
			   AND l.cd_usuario = ".$args['cd_usuario']." 
				   ".(((trim($args['dt_acesso_ini']) != "") AND (trim($args['dt_acesso_fim']) != "")) ? " AND DATE_TRUNC('day', a.dt_acesso) BETWEEN TO_DATE('".$args['dt_acesso_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_acesso_fim']."', 'DD/MM/YYYY')" : "")."
                   ".(((trim($args['dt_login_ini']) != "") AND (trim($args['dt_login_fim']) != "")) ? " AND DATE_TRUNC('day', l.dt_login) BETWEEN TO_DATE('".$args['dt_login_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_login_fim']."', 'DD/MM/YYYY')" : "")."
				   ".(trim($args['cd_empresa']) != ""  ? "AND l.cd_empresa = ".intval($args['cd_empresa']) : '')."
				   ".(intval($args['cd_registro_empregado']) > 0  ? "AND l.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : '')."
				   ".(trim($args['seq_dependencia']) != "" ? "AND l.seq_dependencia = ".intval($args['seq_dependencia']) : '').";";

		return $this->db->query($qr_sql)->result_array();
	}	
	
	public function get_usuarios($cd_gerencia)
    {
		$qr_sql = "
			SELECT codigo AS value,
				   nome AS text
			  FROM projetos.usuarios_controledi
			 WHERE divisao = '".trim($cd_gerencia)."'
			   AND tipo NOT IN ('X')
			 ORDER BY nome;";
				  
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function pdf($cd_usuario)
	{
		$qr_sql = "
			SELECT TO_CHAR(ua.dt_inclusao, 'DD') AS dia,
				   TO_CHAR(ua.dt_inclusao, 'MM') AS mes,
				   TO_CHAR(ua.dt_inclusao, 'YYYY') AS ano,
				   UPPER(funcoes.remove_acento(uc.nome)) AS nome,
				   uc.cd_patrocinadora AS cd_empresa,
				   uc.cd_registro_empregado
			  FROM autoatendimento.usuario_acesso ua
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = ua.cd_usuario
			 WHERE ua.cd_usuario = ".intval($cd_usuario).";";
			 
		return $this->db->query($qr_sql)->row_array();
	}
}
?>