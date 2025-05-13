<?php
class aniversario_model extends Model
{
	function __construct()
    {
        parent::Model();
    }
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT a.cd_aniversario,
						   'CAD' AS origem,
						   a.nome, 
						   a.area, 
						   TO_CHAR(a.dt_nascimento,'DD/MM/') || TO_CHAR(CURRENT_DATE,'YYYY') AS dt_nascimento
					  FROM projetos.aniversario a
					 WHERE a.dt_exclusao IS NULL
					 ".(trim($args['nome']) != "" ? "AND funcoes.remove_acento(UPPER(a.nome)) LIKE funcoes.remove_acento(UPPER('%".str_replace(" ","%", trim($args['nome']))."%')) " : "")."
					 ".(trim($args['area']) != "" ? "AND funcoes.remove_acento(UPPER(a.area)) = funcoes.remove_acento(UPPER('".trim($args['area'])."'))" : "")."
					 ".(trim($args['origem']) != "" ? "AND 'CAD' = '".trim($args['origem'])."'" : "")."
					 ".(trim($args['mes']) != "" ? "AND TO_CHAR(a.dt_nascimento,'MM') = '".trim($args['mes'])."'" : "")."
					 ".(trim($args['fl_data']) == "S" ? "AND a.dt_nascimento IS NOT NULL" : "")."
					 ".(trim($args['fl_data']) == "N" ? "AND a.dt_nascimento IS NULL" : "")."

					 UNION

					SELECT uc.codigo AS cd_aniversario,
						   'USU' AS origem,
						   uc.nome,
						   uc.divisao AS area,
						   TO_CHAR(uc.dt_nascimento,'DD/MM/') || TO_CHAR(CURRENT_DATE,'YYYY')AS dt_nascimento
					  FROM projetos.usuarios_controledi uc
					 WHERE uc.tipo NOT IN('X')
					   AND uc.divisao NOT IN('FC','SNG')
					 ".(trim($args['nome']) != "" ? "AND funcoes.remove_acento(UPPER(uc.nome)) LIKE funcoes.remove_acento(UPPER('%".str_replace(" ","%", trim($args['nome']))."%')) " : "")."
					 ".(trim($args['area']) != "" ? "AND funcoes.remove_acento(UPPER(uc.divisao)) = funcoes.remove_acento(UPPER('".trim($args['area'])."'))" : "")."
					 ".(trim($args['origem']) != "" ? "AND 'USU' = '".trim($args['origem'])."'" : "")."
					 ".(trim($args['mes']) != "" ? "AND TO_CHAR(uc.dt_nascimento,'MM') = '".trim($args['mes'])."'" : "")."
					 ".(trim($args['fl_data']) == "S" ? "AND uc.dt_nascimento IS NOT NULL" : "")."
					 ".(trim($args['fl_data']) == "N" ? "AND uc.dt_nascimento IS NULL" : "")."					   
					   
					 ORDER BY nome		
		          ";
		#echo "<PRE>".$qr_sql."</PRE>";
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		if(trim($args['origem'] == "CAD"))
		{
			$qr_sql = "
						SELECT a.cd_aniversario,
						       'CAD' AS origem,
							   a.nome, 
							   a.area, 
							   TO_CHAR(a.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
							   TO_CHAR(a.dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao
						  FROM projetos.aniversario a
						 WHERE a.cd_aniversario = ".intval($args['cd_aniversario'])."
					  ";
		}
		else if(trim($args['origem'] == "USU"))
		{
			$qr_sql = "
						SELECT uc.codigo AS cd_aniversario,
						       'USU' AS origem,
							   uc.nome,
							   uc.divisao AS area,
							   TO_CHAR(uc.dt_nascimento,'DD/MM/YYYY') AS dt_nascimento,
							   NULL AS dt_exclusao
						  FROM projetos.usuarios_controledi uc
						 WHERE uc.codigo = ".intval($args['cd_aniversario'])."
					  ";
		}
		
		$result = $this->db->query($qr_sql);
	}	
	
	function salvar(&$result, $args=array())
	{
		if(trim($args['origem'] == "CAD"))
		{
			if(intval($args['cd_aniversario']) == 0)
			{
				$cd_aniversario = intval($this->db->get_new_id("projetos.aniversario", "cd_aniversario"));
				
				$qr_sql = "
							INSERT INTO projetos.aniversario
							     (
									cd_aniversario,
									nome,
									area,
									dt_nascimento,
									cd_usuario_inclusao,
									cd_usuario_alteracao 
								  )
							 VALUES
							      (
									".$cd_aniversario.",
									".(trim($args['nome']) == "" ? "DEFAULT": "funcoes.remove_acento('".trim($args['nome'])."')").",
								    ".(trim($args['area']) == "" ? "DEFAULT": "funcoes.remove_acento('".trim($args['area'])."')").",
								    ".(trim($args['dt_nascimento']) == "" ? "DEFAULT": "TO_DATE('".$args['dt_nascimento']."','DD/MM/YYYY')").",
								    ".(intval($args['cd_usuario']) == 0 ? "DEFAULT": intval($args['cd_usuario'])).",
								    ".(intval($args['cd_usuario']) == 0 ? "DEFAULT": intval($args['cd_usuario']))."
								  )
						  ";				
			}
			else
			{
				$qr_sql = "
							UPDATE projetos.aniversario
							   SET nome                   = ".(trim($args['nome']) == "" ? "DEFAULT": "funcoes.remove_acento('".trim($args['nome'])."')").",
							       area                   = ".(trim($args['area']) == "" ? "DEFAULT": "funcoes.remove_acento('".trim($args['area'])."')").",
							       dt_nascimento          = ".(trim($args['dt_nascimento']) == "" ? "DEFAULT": "TO_DATE('".$args['dt_nascimento']."','DD/MM/YYYY')").",
								   cd_usuario_alteracao = ".(intval($args['cd_usuario']) == 0 ? "DEFAULT": intval($args['cd_usuario']))."
							 WHERE cd_aniversario = ".intval($args['cd_aniversario'])."
						  ";	
				$cd_aniversario = intval($args['cd_aniversario']);
			}
		}
		else if(trim($args['origem'] == "USU"))
		{
			$qr_sql = "
						UPDATE projetos.usuarios_controledi
						   SET dt_nascimento = TO_DATE('".$args['dt_nascimento']."','DD/MM/YYYY')
						 WHERE codigo = ".intval($args['cd_aniversario'])."
					  ";
			$cd_aniversario = intval($args['cd_aniversario']);
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_aniversario;
	}	
	
	function excluir(&$result, $args=array())
    {
		$qr_sql = "
					UPDATE projetos.aniversario
					   SET dt_exclusao          = CURRENT_TIMESTAMP,
					       cd_usuario_exclusao  = ".$args['cd_usuario'].",
					       cd_usuario_alteracao = ".$args['cd_usuario']."
					 WHERE cd_aniversario = ".intval($args['cd_aniversario'])."
			      ";
		$result = $this->db->query($qr_sql);
	}	
	
	function comboArea(&$result, $args=array())
    {
		$qr_sql = "
					SELECT a.area AS value, 
						   a.area AS text
					  FROM projetos.aniversario a
					 WHERE a.dt_exclusao IS NULL

					UNION

					SELECT uc.divisao AS value,
					       uc.divisao AS text
					  FROM projetos.usuarios_controledi uc
					 WHERE uc.tipo NOT IN('X')
					   AND uc.divisao NOT IN('FC','SNG')
					   
					ORDER BY text
			      ";
			
		$result = $this->db->query($qr_sql);
	}	
	
	function aniversariante(&$result, $args=array())
    {
		if(trim($args['origem']) == "CAD")
		{
			$qr_sql = "
						SELECT a.cd_aniversario,
							   a.nome, 
							   a.area, 
							   TO_CHAR(a.dt_nascimento,'DD/MM/') || TO_CHAR(CURRENT_DATE,'YYYY') AS dt_nascimento
						  FROM projetos.aniversario a
						 WHERE a.cd_aniversario = ".intval($args['cd_aniversario'])."
					  ";
		}
		else if(trim($args['origem']) == "USU")
		{
			$qr_sql = "
						SELECT uc.codigo AS cd_aniversario,
							   uc.nome,
							   uc.divisao AS area,
							   TO_CHAR(uc.dt_nascimento,'DD/MM/') || TO_CHAR(CURRENT_DATE,'YYYY') AS dt_nascimento
						  FROM projetos.usuarios_controledi uc
						 WHERE uc.codigo = ".intval($args['cd_aniversario'])."
					  ";
		}
		else
		{
			$qr_sql = "
						SELECT 1 WHERE 1 = 0
			          ";
		}
		
		#echo "<PRE>".$qr_sql."</PRE>"; exit;
		
		$result = $this->db->query($qr_sql);
	}

	function assuntoListar(&$result, $args=array())
    {
		$qr_sql = "
					SELECT aa.cd_aniversario_assunto, 
					       aa.assunto, 
						   TO_CHAR(aa.dt_alteracao,'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
						   uc.nome
                      FROM projetos.aniversario_assunto aa
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = aa.cd_usuario_alteracao
					 WHERE aa.dt_exclusao IS NULL
					  ".(trim($args['assunto']) != "" ? "AND funcoes.remove_acento(UPPER(aa.assunto)) LIKE funcoes.remove_acento(UPPER('%".str_replace(" ","%", trim($args['assunto']))."%')) " : "")."
					 ORDER BY aa.dt_alteracao DESC
			      ";
		#echo "<PRE>".$qr_sql."</PRE>";	
		$result = $this->db->query($qr_sql);
	}	
	
	function assuntoCarrega(&$result, $args=array())
	{
		$qr_sql = "
					SELECT aa.cd_aniversario_assunto, 
					       aa.assunto, 
						   TO_CHAR(aa.dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao
                      FROM projetos.aniversario_assunto aa
					 WHERE aa.cd_aniversario_assunto = ".intval($args['cd_aniversario_assunto'])."
			      ";		
		
		$result = $this->db->query($qr_sql);
	}	

	function assuntoSalvar(&$result, $args=array())
	{
		if(intval($args['cd_aniversario_assunto']) == 0)
		{
			$cd_aniversario_assunto = intval($this->db->get_new_id("projetos.aniversario_assunto", "cd_aniversario_assunto"));
			
			$qr_sql = "
						INSERT INTO projetos.aniversario_assunto
							 (
								cd_aniversario_assunto,
								assunto,
								cd_usuario_inclusao,
								cd_usuario_alteracao 
							  )
						 VALUES
							  (
								".$cd_aniversario_assunto.",
								".(trim($args['assunto']) == "" ? "DEFAULT": "'".trim($args['assunto'])."',")."
								".(intval($args['cd_usuario']) == 0 ? "DEFAULT": intval($args['cd_usuario'])).",
								".(intval($args['cd_usuario']) == 0 ? "DEFAULT": intval($args['cd_usuario']))."
							  )
					  ";				
		}
		else
		{
			$qr_sql = "
						UPDATE projetos.aniversario_assunto
						   SET assunto                = ".(trim($args['assunto']) == "" ? "DEFAULT": "'".trim($args['assunto'])."',")."
							   cd_usuario_alteracao = ".(intval($args['cd_usuario']) == 0 ? "DEFAULT": intval($args['cd_usuario']))."
						 WHERE cd_aniversario_assunto = ".intval($args['cd_aniversario_assunto'])."
					  ";	
			$cd_aniversario_assunto = intval($args['cd_aniversario_assunto']);
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_aniversario_assunto;
	}

	function assuntoExcluir(&$result, $args=array())
    {
		$qr_sql = "
					UPDATE projetos.aniversario_assunto
					   SET dt_exclusao          = CURRENT_TIMESTAMP,
					       cd_usuario_exclusao  = ".$args['cd_usuario'].",
					       cd_usuario_alteracao = ".$args['cd_usuario']."
					 WHERE cd_aniversario_assunto = ".intval($args['cd_aniversario_assunto'])."
			      ";
		$result = $this->db->query($qr_sql);
	}	
	
	function resumo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT TO_CHAR(x.dt_mes,'YYYY-MM') AS mes, 
				   COUNT(a.cd_aniversario) AS qt_pessoa, 
				   SUM((SELECT COUNT(*)
						  FROM projetos.aniversario_log al
						 WHERE al.cd_aniversario = a.cd_aniversario
						   AND CAST(al.dt_envio AS DATE) = a.dt_nascimento)) AS qt_envio,
				   SUM((SELECT COUNT(DISTINCT eet.ip)
						  FROM projetos.aniversario_log al1
						  JOIN projetos.envia_emails_tracker eet
						    ON eet.cd_email = al1.cd_email
						 WHERE al1.cd_aniversario = a.cd_aniversario
						   AND CAST(al1.dt_envio AS DATE) = a.dt_nascimento)) AS qt_pessoa_acesso,
				   SUM((SELECT COUNT(*)
					 	  FROM projetos.aniversario_log al1
				 		  JOIN projetos.envia_emails_tracker eet
			 			    ON eet.cd_email = al1.cd_email
						 WHERE al1.cd_aniversario = a.cd_aniversario
						   AND CAST(al1.dt_envio AS DATE) = a.dt_nascimento)) AS qt_acesso
			  FROM generate_series('".trim($args['ano'])."-01-01'::timestamp, '".trim($args['ano'])."-12-31', '1 month') x(dt_mes)
			  JOIN (SELECT a.cd_aniversario,
						   'CAD' AS origem,
						   a.nome, 
						   a.area, 
						   TO_DATE(TO_CHAR(a.dt_nascimento,'DD/MM/') || TO_CHAR(CURRENT_DATE,'YYYY'),'DD/MM/YYYY') AS dt_nascimento
					  FROM projetos.aniversario a
					 WHERE a.dt_exclusao IS NULL
					 UNION
					SELECT uc.codigo AS cd_aniversario,
						   'USU' AS origem,
						   uc.nome,
						   uc.divisao AS area,
						   TO_DATE(TO_CHAR(uc.dt_nascimento,'DD/MM/') || TO_CHAR(CURRENT_DATE,'YYYY'),'DD/MM/YYYY') AS dt_nascimento
					  FROM projetos.usuarios_controledi uc
					 WHERE uc.tipo NOT IN('X')
					   AND uc.divisao NOT IN('FC','SNG')) a
				ON TO_CHAR(a.dt_nascimento,'YYYY-MM') = TO_CHAR(x.dt_mes,'YYYY-MM')
			 GROUP BY mes;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function aniversariantesDoMes(&$result, $args=array())
    {
		$qr_sql = "
					SELECT a.cd_aniversario,
						   TO_CHAR(a.dt_nascimento,'DD/MM') AS dt_niver,
						   a.nome,
						   '' AS divisao,
						   'CAD' AS origem
					  FROM projetos.aniversario a
					 WHERE a.dt_exclusao   IS NULL
					   AND TO_CHAR(a.dt_nascimento,'MM') = TO_CHAR(".intval($args['nr_mes_referencia']).",'FM00')

					 UNION

					SELECT uc.codigo AS cd_aniversario,
						   TO_CHAR(uc.dt_nascimento,'DD/MM') AS dt_niver,
						   uc.nome,
						   uc.divisao,
						   'USU' AS origem
					  FROM projetos.usuarios_controledi uc
					 WHERE uc.tipo          NOT IN('X')
					   AND uc.divisao       NOT IN('FC','SNG')
					   AND TO_CHAR(uc.dt_nascimento,'MM') = TO_CHAR(".intval($args['nr_mes_referencia']).",'FM00')

					ORDER BY dt_niver, nome
			      ";
			
		$result = $this->db->query($qr_sql);
	}	
}
?>