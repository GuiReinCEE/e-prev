<?php

class Reclamacao_analise_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT ra.cd_reclamacao_analise, 
			       funcoes.nr_reclamacao_analise(ra.nr_ano, ra.nr_numero) AS ano_numero,
				   rac.ds_reclamacao_analise_classifica,
				   TO_CHAR(ra.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   uc.nome AS responsavel,
				   TO_CHAR(ra.dt_limite, 'DD/MM/YYYY') AS dt_limite,
				   TO_CHAR(ra.dt_prorrogacao, 'DD/MM/YYYY') AS dt_prorrogacao,
				   TO_CHAR(ra.dt_retorno, 'DD/MM/YYYY HH24:MI:SS') AS dt_retorno,
				   (SELECT COUNT(*) 
				      FROM projetos.reclamacao_analise_item rai
					 WHERE rai.dt_exclusao IS NULL
					   AND rai.cd_reclamacao_analise = ra.cd_reclamacao_analise) AS quantidade,
				   CASE WHEN ra.dt_retorno IS NULL AND ra.dt_limite < CURRENT_TIMESTAMP THEN 'Sim'
				        ELSE 'Não'
				   END AS atrasado,
				   CASE WHEN ra.dt_retorno IS NULL AND ra.dt_limite < CURRENT_TIMESTAMP THEN 'label-important'
				        ELSE ''
				   END AS class_atrasado,
				   uc2.nome AS usuario_retorno
			  FROM projetos.reclamacao_analise ra
			  JOIN projetos.reclamacao_analise_classifica rac
				ON rac.cd_reclamacao_analise_classifica = ra.cd_reclamacao_analise_classifica
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = ra.cd_usuario_responsavel
			  LEFT JOIN projetos.usuarios_controledi uc2
				ON uc2.codigo = ra.cd_usuario_retorno
			 WHERE ra.dt_exclusao IS NULL
			   ".(trim($args['fl_atrasado']) == 'S' ? "AND ra.dt_retorno IS NULL AND ra.dt_limite < CURRENT_TIMESTAMP" : '')."	
			   ".(trim($args['fl_atrasado']) == 'N' ? "AND (ra.dt_retorno IS NOT NULL OR ra.dt_limite >= CURRENT_TIMESTAMP)" : '')."	
			   ".(trim($args['fl_retornado']) == 'S' ? "AND ra.dt_retorno IS NOT NULL" : '')."	
			   ".(trim($args['fl_retornado']) == 'N' ? "AND ra.dt_retorno IS NULL" : '')."	
			   ".(trim($args['nr_ano']) != '' ? "AND ra.nr_ano = ".intval($args['nr_ano']) : "")."
			   ".(trim($args['nr_numero']) != '' ? "AND ra.nr_numero = ".intval($args['nr_numero']) : "")."
			   ".(trim($args['cd_reclamacao_analise_classifica']) != '' ? "AND ra.cd_reclamacao_analise_classifica = ".intval($args['cd_reclamacao_analise_classifica']) : "")."
			   ".(trim($args['cd_usuario_responsavel']) != '' ? "AND ra.cd_usuario_responsavel = ".intval($args['cd_usuario_responsavel']) : "")."
			   ".(((trim($args['dt_envio_ini']) != "") and  (trim($args['dt_envio_fim']) != "")) ? " AND DATE_TRUNC('day', ra.dt_envio) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_limite_ini']) != "") and  (trim($args['dt_limite_fim']) != "")) ? " AND DATE_TRUNC('day', ra.dt_limite) BETWEEN TO_DATE('".$args['dt_limite_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_limite_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_prorrogacao_ini']) != "") and  (trim($args['dt_prorrogacao_fim']) != "")) ? " AND DATE_TRUNC('day', ra.dt_prorrogacao) BETWEEN TO_DATE('".$args['dt_prorrogacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_prorrogacao_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_retorno_ini']) != "") and  (trim($args['dt_retorno_fim']) != "")) ? " AND DATE_TRUNC('day', ra.dt_retorno) BETWEEN TO_DATE('".$args['dt_retorno_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_retorno_fim']."', 'DD/MM/YYYY')" : "").";";
	
        $result = $this->db->query($qr_sql);
    }
	
	function classificacao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_reclamacao_analise_classifica AS value,
			       ds_reclamacao_analise_classifica AS text
			  FROM projetos.reclamacao_analise_classifica
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_reclamacao_analise_classifica;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function responsavel(&$result, $args=array())
	{
		$qr_sql = "
			SELECT uc.codigo AS value,
			       uc.nome AS text
			 FROM projetos.usuarios_controledi uc
			 JOIN projetos.reclamacao_analise ra
			   ON ra.cd_usuario_responsavel = uc.codigo
			WHERE ra.dt_exclusao IS NULL
			GROUP BY uc.codigo
			ORDER BY uc.nome;";
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_reclamacao_analise']) == 0)
		{
			$cd_reclamacao_analise = intval($this->db->get_new_id("projetos.reclamacao_analise", "cd_reclamacao_analise"));
			
			$qr_sql = "
				INSERT INTO projetos.reclamacao_analise
				     (
						cd_reclamacao_analise, 
						cd_reclamacao_analise_classifica, 
                        cd_usuario_responsavel, 
						cd_usuario_substituto,  
                        dt_limite, 
						observacao,
						cd_usuario_inclusao, 
                        cd_usuario_alteracao
					 )
                VALUES 
				     (
						".intval($cd_reclamacao_analise).",
						".(trim($args['cd_reclamacao_analise_classifica']) != '' ? intval($args['cd_reclamacao_analise_classifica']) : "DEFAULT").",
						".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
						".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "DEFAULT").",
						".(trim($args['dt_limite']) != '' ? "TO_DATE('".$args['dt_limite']."', 'DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['observacao']) != '' ? "'".$args['observacao']."'" : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$cd_reclamacao_analise = intval($args['cd_reclamacao_analise']);
			
			$qr_sql = "
				UPDATE projetos.reclamacao_analise
				   SET cd_reclamacao_analise_classifica = ".(trim($args['cd_reclamacao_analise_classifica']) != '' ? intval($args['cd_reclamacao_analise_classifica']) : "DEFAULT").",
					   cd_usuario_responsavel           = ".(trim($args['cd_usuario_responsavel']) != '' ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
					   cd_usuario_substituto            = ".(trim($args['cd_usuario_substituto']) != '' ? intval($args['cd_usuario_substituto']) : "DEFAULT").",
					   dt_limite                        = ".(trim($args['dt_limite']) != '' ? "TO_DATE('".$args['dt_limite']."', 'DD/MM/YYYY')" : "DEFAULT").",
					   observacao                       = ".(trim($args['observacao']) != '' ? "'".$args['observacao']."'" : "DEFAULT").",
					   dt_prorrogacao                   = ".(trim($args['dt_prorrogacao']) != '' ? "TO_DATE('".$args['dt_prorrogacao']."', 'DD/MM/YYYY')" : "DEFAULT").",
					   cd_usuario_prorrogacao           = ".(trim($args['dt_prorrogacao']) != '' ? intval($args['cd_usuario']) : "DEFAULT").",
					   cd_usuario_alteracao             = ".intval($args['cd_usuario']).",
					   dt_alteracao                     = CURRENT_TIMESTAMP
				 WHERE cd_reclamacao_analise = ".intval($cd_reclamacao_analise).";";
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_reclamacao_analise;
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT funcoes.nr_reclamacao_analise(ra.nr_ano, ra.nr_numero) AS ano_numero,
			       ra.cd_reclamacao_analise,
			       ra.cd_reclamacao_analise_classifica,
				   ra.cd_usuario_responsavel,
				   ra.cd_usuario_substituto,
				   TO_CHAR(ra.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   TO_CHAR(ra.dt_limite, 'DD/MM/YYYY') AS dt_limite,
				   TO_CHAR(ra.dt_prorrogacao, 'DD/MM/YYYY') AS dt_prorrogacao,
				   ra.observacao,
				   uc1.nome AS responsavel,
				   uc1.divisao AS cd_usuario_responsavel_gerencia,
				   uc2.nome AS substituto,
				   uc2.divisao AS cd_usuario_substituto_gerencia,
				   rac.ds_reclamacao_analise_classifica,
				   TO_CHAR(ra.dt_retorno, 'DD/MM/YYYY HH24:MI:SS') AS dt_retorno,
				   uc3.nome AS usuario_retorno,
				   ra.ds_retorno
			  FROM projetos.reclamacao_analise ra
			  JOIN projetos.usuarios_controledi uc1
			    ON uc1.codigo = ra.cd_usuario_responsavel
			  JOIN projetos.usuarios_controledi uc2
			    ON uc2.codigo = ra.cd_usuario_substituto
			  LEFT JOIN projetos.usuarios_controledi uc3
				ON uc3.codigo = ra.cd_usuario_retorno
			  JOIN projetos.reclamacao_analise_classifica rac
				ON rac.cd_reclamacao_analise_classifica = ra.cd_reclamacao_analise_classifica
			 WHERE ra.cd_reclamacao_analise = ".intval($args['cd_reclamacao_analise']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function reclamacao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT funcoes.nr_reclamacao(r.ano, r.numero, r.tipo) AS cd_reclamacao,
				   funcoes.nr_reclamacao_analise(ran.nr_ano_nc, ran.nr_nc) AS nc_ano_numero,
                   funcoes.nr_ap(ran.nr_ano_sap, ran.nr_sap) AS ano_numero_sap,
                   rai.ds_retorno,
				   r.numero,
				   r.ano,
				   r.tipo,
				   r.cd_empresa, 
				   r.cd_registro_empregado, 
				   r.seq_dependencia, 
				   r.nome, 						   
				   TO_CHAR(r.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
				   TO_CHAR(r.dt_cancela,'DD/MM/YYYY HH24:MI') AS dt_cancela,
				   ucc.nome AS ds_usuario_cancela,
				   r.descricao,
				   TO_CHAR(ra.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_encaminhado,
				   uc.nome AS ds_usuario_reclamacao,
				   ra.cd_divisao,
				   uca.nome AS ds_usuario_responsavel,
				   TO_CHAR(ra.dt_prazo,'DD/MM/YYYY') AS dt_prazo,
				   TO_CHAR(ra.dt_prorrogacao,'DD/MM/YYYY') AS dt_prorrogacao,
				   TO_CHAR(ran.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_retorno,
				   ran.cd_reclamacao_retorno_classificacao,
				   rrc.ds_reclamacao_retorno_classificacao,
				   rrc.cor,
				   rai.cd_reclamacao_analise_item,
				   (SELECT ra4.dt_envio 
                      FROM projetos.reclamacao_analise ra4
                     WHERE ra4.cd_reclamacao_analise = ".intval($args['cd_reclamacao_analise']).") AS dt_envio
			  FROM projetos.reclamacao r
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = r.cd_usuario_inclusao
			  LEFT JOIN public.patrocinadoras patr
				ON patr.cd_empresa = r.cd_empresa
			  LEFT JOIN projetos.reclamacao_atendimento ra
				ON ra.numero = r.numero
			   AND ra.ano    = r.ano
			   AND ra.tipo   = r.tipo
			  LEFT JOIN projetos.usuarios_controledi uca
				ON uca.codigo = ra.cd_usuario_responsavel
			  LEFT JOIN projetos.usuarios_controledi ucc
				ON ucc.codigo = r.cd_usuario_cancela						
			  LEFT JOIN projetos.reclamacao_andamento ran
				ON ran.numero                  = r.numero
			   AND ran.ano                     = r.ano
			   AND ran.tipo                    = r.tipo
			   AND ran.tp_reclamacao_andamento = 'R' --RETORNO
			  LEFT JOIN projetos.reclamacao_retorno_classificacao rrc
			    ON rrc.cd_reclamacao_retorno_classificacao = ran.cd_reclamacao_retorno_classificacao
			  LEFT JOIN projetos.reclamacao_analise_item rai
			    ON r.numero = rai.numero
			   AND r.ano    = rai.ano
			   AND r.tipo   = rai.tipo
			   AND rai.dt_exclusao IS NULL
			 WHERE r.dt_exclusao IS NULL
			   AND (ran.cd_reclamacao_retorno_classificacao = 2 OR ran.dt_reclamacao_retorno IS NOT NULL) 
			   AND (r.numero, r.ano, r.tipo) NOT IN (SELECT rai.numero,
			                                                rai.ano,
															rai.tipo
													   FROM projetos.reclamacao_analise_item rai
													  WHERE rai.dt_exclusao IS NULL
													    AND rai.cd_reclamacao_analise <> ".intval($args['cd_reclamacao_analise']).")
			  AND ((SELECT ra2.dt_envio 
					  FROM projetos.reclamacao_analise ra2
                     WHERE ra2.cd_reclamacao_analise = ".intval($args['cd_reclamacao_analise']).") IS NOT NULL 

              AND (r.numero, r.ano, r.tipo) IN (SELECT rai1.numero,
													   rai1.ano,
													   rai1.tipo
											      FROM projetos.reclamacao_analise_item rai1
											      JOIN projetos.reclamacao_analise ra1
												    ON ra1.cd_reclamacao_analise = rai1.cd_reclamacao_analise
											     WHERE rai1.dt_exclusao IS NULL
												   AND rai1.cd_reclamacao_analise = ".intval($args['cd_reclamacao_analise']).") 
			 OR ((SELECT ra3.dt_envio 
                    FROM projetos.reclamacao_analise ra3
                   WHERE ra3.cd_reclamacao_analise = ".intval($args['cd_reclamacao_analise']).") IS NULL) 
			);";
		#echo '<pre>'.$qr_sql;												
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_reclamacao(&$result, $args=array())
	{

		$qr_sql = "
					UPDATE projetos.reclamacao_analise_item
					   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).", 
						   dt_exclusao         = CURRENT_TIMESTAMP
					 WHERE cd_reclamacao_analise = ".intval($args['cd_reclamacao_analise'])."
					   AND numero = ".intval($args['numero'])." 
					   AND ano    = ".intval($args['ano'])."
					   AND tipo   = '".trim($args['tipo'])."'
					   AND dt_exclusao IS NULL;			
		          ";
		
		if(trim($args['fl_marcado']) == "S")
		{
			$qr_sql.= "
						INSERT INTO projetos.reclamacao_analise_item
							(
								cd_reclamacao_analise, 
								numero, 
								ano, 
								tipo, 
								cd_usuario_inclusao
							)
					   VALUES 
							(
								".intval($args['cd_reclamacao_analise']).",
								".intval($args['numero']).",
								".intval($args['ano']).",
								'".trim($args['tipo'])."',
								".intval($args['cd_usuario'])."
							);			
			          ";
		}
		
		
		$result = $this->db->query($qr_sql);
	}
	
	function enviar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.reclamacao_analise
			   SET cd_usuario_envio             = ".intval($args['cd_usuario']).",
				   dt_envio                     = CURRENT_TIMESTAMP
			 WHERE cd_reclamacao_analise = ".intval($args['cd_reclamacao_analise']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function data_limite(&$result, $args=array())
	{
		$qr_sql = "SELECT TO_CHAR(funcoes.dia_util('DEPOIS', CURRENT_DATE, 5), 'DD/MM/YYYY') AS dt_limite";
		
		$result = $this->db->query($qr_sql);
	}
	
}

?>