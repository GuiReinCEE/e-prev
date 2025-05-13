<?php
class auto_atendimento_usuario_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT aau.cd_auto_atendimento_usuario, 
					       aau.cd_usuario,
                           uc.nome AS nome_usuario,
						   uc.divisao AS ger_usuario,
						   TO_CHAR(aau.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   aau.cd_usuario_inclusao, 
						   uci.nome AS nome_usuario_inclusao,
						   TO_CHAR(aau.dt_exclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao,
						   aau.cd_usuario_exclusao,
						   uce.nome AS nome_usuario_exclusao
                      FROM projetos.auto_atendimento_usuario aau
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = aau.cd_usuario
					  JOIN projetos.usuarios_controledi uci
					    ON uci.codigo = aau.cd_usuario_inclusao		
					  LEFT JOIN projetos.usuarios_controledi uce
					    ON uce.codigo = aau.cd_usuario_exclusao							
					 WHERE 1 = 1
					   ".($args['cd_situacao'] == "A" ? " AND aau.dt_exclusao IS NULL ": "")."
					   ".($args['cd_situacao'] == "E" ? " AND aau.dt_exclusao IS NOT NULL ": "")."
					 ORDER BY ger_usuario, 
					          nome_usuario
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; exit;
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_usuario']) > 0)
		{
			$qr_sql = "
						SELECT COUNT(*) AS fl_existe
						  FROM projetos.auto_atendimento_usuario
						 WHERE cd_usuario  = ".intval($args['cd_usuario'])."
						   AND dt_exclusao IS NULL
					  ";
			$result = $this->db->query($qr_sql);			
			$ar_usu = $result->row_array();					
			
			if(intval($ar_usu['fl_existe']) == 0)
			{
				$qr_sql = " 
							INSERT INTO projetos.auto_atendimento_usuario
							     (
                                   cd_usuario, 
								   cd_usuario_inclusao
								 )
                            VALUES 
							     (
								   ".intval($args['cd_usuario']).",
								   ".intval($args['cd_usuario_inclusao'])."
								 );		
						  ";			
				if($this->db->query($qr_sql))
				{
					return true;
				}
			}
		}
		
		return false;
	}	
	
	function excluir(&$result, $args=array())
	{
		$retorno = "";
		if(intval($args['cd_auto_atendimento_usuario']) > 0)
		{
			$qr_sql = " 
						UPDATE projetos.auto_atendimento_usuario
						   SET dt_exclusao         = CURRENT_TIMESTAMP,
						       cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao'])."
						 WHERE cd_auto_atendimento_usuario = ".intval($args['cd_auto_atendimento_usuario'])."
					  ";			
			if($this->db->query($qr_sql))
			{
				$qr_sql = "
							SELECT TO_CHAR(dt_exclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao
							  FROM projetos.auto_atendimento_usuario
							 WHERE cd_auto_atendimento_usuario = ".intval($args['cd_auto_atendimento_usuario'])."
						  ";
				$result = $this->db->query($qr_sql);			
				$ar_usu = $result->row_array();
				$retorno = $ar_usu['dt_exclusao'];	
			}
		}
		
		return $retorno;
	}	
	
	
	function acessoListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT aaul.cd_empresa,
						   aaul.cd_registro_empregado,
						   aaul.seq_dependencia,
						   aaul.sid,
						   TO_CHAR(lau.hora,'DD/MM/YYYY HH24:MI:SS') AS dt_acesso,
						   lau.pagina
					  FROM projetos.auto_atendimento_usuario_log aaul
					  LEFT JOIN public.log_acessos_usuario lau
						ON lau.sid = aaul.sid
					 WHERE aaul.cd_usuario = ".$args['cd_usuario']."
						".(((trim($args['dt_acesso_ini']) != "") and  (trim($args['dt_acesso_fim']) != "")) ? "AND CAST(lau.hora AS DATE) BETWEEN TO_DATE('".trim($args['dt_acesso_ini'])."','DD/MM/YYYY') AND TO_DATE('".trim($args['dt_acesso_fim'])."','DD/MM/YYYY')" : "")."
						".(trim($args['cd_empresa']) != ""  ? "AND aaul.cd_empresa = ".intval($args['cd_empresa']) : "")."
						".(intval($args['cd_registro_empregado']) > 0  ? "AND aaul.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
						".(trim($args['seq_dependencia']) != ""  ? "AND aaul.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
					 ORDER BY aaul.sid DESC, 
					          lau.hora ASC		          
				  ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; exit;
		$result = $this->db->query($qr_sql);
	}	
}
?>