<?php
class Familia_previdencia_inscricao_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT i.cd_cadastro,
				   i.nome,
				   i.cpf,
				   TO_CHAR(i.dt_inclusao_gap, 'DD/MM/YYYY') AS dt_inclusao_gap,
				   TO_CHAR(i.dt_ingresso_eletro, 'DD/MM/YYYY') AS dt_ingresso_eletro,
			       TO_CHAR(i.dt_inscricao, 'DD/MM/YYYY') AS dt_inscricao
			  FROM (SELECT c.cd_cadastro, 
						   c.nome,
						   c.cpf,
						   (SELECT MIN(CAST(dt_inclusao_jn AS DATE))
							  FROM familia_previdencia.cadastro_jn cj
							 WHERE cj.cd_cadastro = c.cd_cadastro
							   AND cj.fl_inscrito = 'S') AS dt_inscricao,          
						   t.dt_inclusao AS dt_inclusao_gap,
						   t.dt_ingresso_eletro AS dt_ingresso_eletro
					  FROM familia_previdencia.cadastro c
					  LEFT JOIN public.participantes p
						ON p.cd_empresa = 19
					   AND funcoes.format_cpf(p.cpf_mf::bigint) = c.cpf
					  LEFT JOIN public.titulares t
						ON t.cd_empresa            = p.cd_empresa
					   AND t.cd_registro_empregado = p.cd_registro_empregado
					   AND t.seq_dependencia       = p.seq_dependencia
					 WHERE c.dt_exclusao IS NULL
					   AND c.fl_inscrito = 'S'
					 UNION   
					SELECT d.cd_cadastro, 
						   d.nome,
						   d.cpf,
						   (SELECT MIN(CAST(dt_inclusao_jn AS DATE))
							  FROM familia_previdencia.dependente_jn cj
							 WHERE cj.cd_cadastro = d.cd_cadastro
							   AND cj.fl_inscrito = 'S') AS dt_inscricao,
						   t.dt_inclusao AS dt_inclusao_gap,
						   t.dt_ingresso_eletro AS dt_ingresso_eletro
					  FROM familia_previdencia.dependente d
					  JOIN familia_previdencia.cadastro c
						ON c.cd_cadastro = d.cd_cadastro
					  LEFT JOIN public.participantes p
						ON p.cd_empresa = 19
					   AND funcoes.format_cpf(p.cpf_mf::bigint) = d.cpf
					  LEFT JOIN public.titulares t
						ON t.cd_empresa            = p.cd_empresa
					   AND t.cd_registro_empregado = p.cd_registro_empregado
					   AND t.seq_dependencia       = p.seq_dependencia
					 WHERE d.dt_exclusao IS NULL
					   AND d.fl_inscrito = 'S') AS i 
			 WHERE 1 = 1	
			  ".(trim($args['fl_cadastro_gap']) == 'S' ? "AND i.dt_inclusao_gap IS NOT NULL" : '')."
			  ".(trim($args['fl_cadastro_gap']) == 'N' ? "AND i.dt_inclusao_gap IS NULL" : '')."
			  ".(trim($args['fl_participante']) == 'S' ? "AND i.dt_ingresso_eletro IS NOT NULL" : '')."
			  ".(trim($args['fl_participante']) == 'N' ? "AND i.dt_ingresso_eletro IS NULL" : '')."
			  ".(((trim($args['dt_inscricao_ini']) != "") AND (trim($args['dt_inscricao_fim']) != "")) ? "AND DATE_TRUNC('day', i.dt_inscricao) BETWEEN TO_DATE('".$args['dt_inscricao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inscricao_fim']."', 'DD/MM/YYYY')" : "")."		   
			  ".(((trim($args['dt_inclusao_gap_ini']) != "") AND (trim($args['dt_inclusao_gap_fim']) != "")) ? "AND DATE_TRUNC('day', i.dt_inclusao_gap) BETWEEN TO_DATE('".$args['dt_inclusao_gap_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_gap_fim']."', 'DD/MM/YYYY')" : "")."		   
			  ".(((trim($args['dt_ingresso_eletro_ini']) != "") AND (trim($args['dt_ingresso_eletro_fim']) != "")) ? "AND DATE_TRUNC('day', i.dt_ingresso_eletro) BETWEEN TO_DATE('".$args['dt_ingresso_eletro_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_ingresso_eletro_fim']."', 'DD/MM/YYYY')" : "")."		   
			 ORDER BY i.nome;";

		$result = $this->db->query($qr_sql);
	}	
	
}
?>