<?php

class Reclamacao_responder_model extends Model
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
			   AND ra.dt_envio IS NOT NULL
			   AND (ra.cd_usuario_responsavel = ".intval($args['cd_usuario'])." OR ra.cd_usuario_substituto = ".intval($args['cd_usuario']).")
			   ".(trim($args['fl_atrasado']) == 'S' ? "AND ra.dt_retorno IS NULL AND ra.dt_limite < CURRENT_TIMESTAMP" : '')."	
			   ".(trim($args['fl_atrasado']) == 'N' ? "AND (ra.dt_retorno IS NOT NULL OR ra.dt_limite >= CURRENT_TIMESTAMP)" : '')."	
			   ".(trim($args['fl_retornado']) == 'S' ? "AND ra.dt_retorno IS NOT NULL" : '')."	
			   ".(trim($args['fl_retornado']) == 'N' ? "AND ra.dt_retorno IS NULL" : '')."	
			   ".(trim($args['nr_ano']) != '' ? "AND ra.nr_ano = ".intval($args['nr_ano']) : "")."
			   ".(trim($args['nr_numero']) != '' ? "AND ra.nr_numero = ".intval($args['nr_numero']) : "")."
			   ".(trim($args['cd_reclamacao_analise_classifica']) != '' ? "AND ra.cd_reclamacao_analise_classifica = ".intval($args['cd_reclamacao_analise_classifica']) : "")."
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
				   TO_CHAR(ra.dt_retorno, 'DD/MM/YYYY HH24:MI:SS') AS dt_retorno,
				   ra.ds_retorno,
				   rac.ds_reclamacao_analise_classifica,
				   TO_CHAR(ra.dt_retorno, 'DD/MM/YYYY HH24:MI:SS') AS dt_retorno,
				   uc3.nome AS usuario_retorno,
                   (SELECT COUNT(rai.*)
                      FROM projetos.reclamacao_analise_item rai
                     WHERE rai.dt_exclusao IS NULL
                       AND rai.cd_reclamacao_analise = ra.cd_reclamacao_analise) AS qt_itens,
                   (SELECT COUNT(rai.*)
                      FROM projetos.reclamacao_analise_item rai
                     WHERE rai.dt_exclusao IS NULL
                       AND TRIM(COALESCE(rai.ds_retorno, '')) <> '' 
                       AND rai.cd_reclamacao_analise = ra.cd_reclamacao_analise) AS qt_itens_respondidos
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
			SELECT rai.cd_reclamacao_analise_item,
                   funcoes.nr_reclamacao(r.ano, r.numero, r.tipo) AS cd_reclamacao,	
				   funcoes.nr_nc(rai.nr_ano_nc, rai.nr_nc) AS ano_numero_nc,
                   funcoes.nr_ap(rai.nr_ano_sap, rai.nr_sap) AS ano_numero_sap,
                   rai.nr_ano_sap,
                   rai.nr_sap,
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
				   rai.nr_nc,
				   rai.nr_ano_nc,
				   (SELECT ra4.dt_retorno 
                      FROM projetos.reclamacao_analise ra4
                     WHERE ra4.cd_reclamacao_analise = ".intval($args['cd_reclamacao_analise']).") AS dt_retorno_reclamacao,
                   TO_CHAR(rana.dt_retorno, 'DD/MM/YYYY HH24:MI:SS') AS dt_retorno_parecer_gerencia
			  FROM projetos.reclamacao_analise_item rai
              JOIN projetos.reclamacao_analise rana
                ON rana.cd_reclamacao_analise = rai.cd_reclamacao_analise
			  JOIN projetos.reclamacao r
			    ON r.numero = rai.numero
			   AND r.ano    = rai.ano
			   AND r.tipo   = rai.tipo
			  JOIN projetos.reclamacao_atendimento ra
				ON ra.numero = rai.numero
			   AND ra.ano    = rai.ano
			   AND ra.tipo   = rai.tipo
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = r.cd_usuario_inclusao
			  LEFT JOIN public.patrocinadoras patr
				ON patr.cd_empresa = r.cd_empresa
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
			 WHERE rai.cd_reclamacao_analise = ".intval($args['cd_reclamacao_analise'])."
			   AND rai.dt_exclusao IS NULL;";
										
		$result = $this->db->query($qr_sql);
	}
    
    function parecer(&$result, $args=array())
	{	   
		$qr_sql = "
			SELECT rai.cd_reclamacao_analise_item,
                   rai.cd_reclamacao_analise,
                   funcoes.nr_reclamacao(r.ano, r.numero, r.tipo) AS cd_reclamacao,	
				   funcoes.nr_nc(rai.nr_ano_nc, rai.nr_nc) AS ano_numero_nc,
                   funcoes.nr_ap(rai.nr_ano_sap, rai.nr_sap) AS ano_numero_sap,
                   rai.nr_ano_sap,
                   rai.nr_sap,
                   rai.ds_retorno,
				   r.numero,
				   r.ano,
				   r.tipo,
				   r.cd_empresa, 
				   r.cd_registro_empregado, 
				   r.seq_dependencia, 
				   r.nome, 	
                   rai.ds_retorno,
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
				   rai.nr_nc,
				   rai.nr_ano_nc,
				   (SELECT ra4.dt_retorno 
                      FROM projetos.reclamacao_analise ra4
                     WHERE ra4.cd_reclamacao_analise = rai.cd_reclamacao_analise) AS dt_retorno_reclamacao,
                   TO_CHAR(rana.dt_retorno, 'DD/MM/YYYY HH24:MI:SS') AS dt_retorno_parecer_gerencia,
                   CASE WHEN rai.nr_ano_sap IS NOT NULL AND rai.nr_sap IS NOT NULL THEN 'sap'
                        WHEN rai.nr_ano_nc IS NOT NULL AND rai.nr_nc IS NOT NULL THEN 'nc'
                        ELSE ''
                   END AS fl_acao
			  FROM projetos.reclamacao_analise_item rai
              JOIN projetos.reclamacao_analise rana
                ON rana.cd_reclamacao_analise = rai.cd_reclamacao_analise
			  JOIN projetos.reclamacao r
			    ON r.numero = rai.numero
			   AND r.ano    = rai.ano
			   AND r.tipo   = rai.tipo
			  JOIN projetos.reclamacao_atendimento ra
				ON ra.numero = rai.numero
			   AND ra.ano    = rai.ano
			   AND ra.tipo   = rai.tipo
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = r.cd_usuario_inclusao
			  LEFT JOIN public.patrocinadoras patr
				ON patr.cd_empresa = r.cd_empresa
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
			 WHERE rai.cd_reclamacao_analise_item = ".intval($args['cd_reclamacao_analise_item'])."
			   AND rai.dt_exclusao IS NULL;";
							
		$result = $this->db->query($qr_sql);
	}
	
	function verifica_nc(&$result, $args=array())
	{
		$qr_sql = "
			SELECT COUNT(*) AS tl
			  FROM projetos.nao_conformidade
			 WHERE nr_ano = ".intval($args['nr_ano_nc'])."
			   AND nr_nc  = ".intval($args['nr_nc']).";";
		
		$result = $this->db->query($qr_sql);
	}
    
    function verifica_sap(&$result, $args=array())
	{
		$qr_sql = "
			SELECT COUNT(*) AS tl
			  FROM projetos.acao_preventiva
			 WHERE nr_ano = ".intval($args['nr_ano_sap'])."
			   AND nr_ap  = ".intval($args['nr_sap']).";";
		
		$result = $this->db->query($qr_sql);
	}
   
    function salvar_retorno(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.reclamacao_analise_item
			   SET nr_ano_sap = ".(trim($args['nr_ano_sap']) != '' ? intval($args['nr_ano_sap']) : "DEFAULT").", 
			       nr_sap     = ".(trim($args['nr_sap']) != '' ? intval($args['nr_sap']) : "DEFAULT").",
                   ds_retorno = ".(trim($args['ds_retorno']) != '' ? str_escape($args['ds_retorno']) : "DEFAULT").",
                   nr_ano_nc  = ".(trim($args['nr_ano_nc']) != '' ? intval($args['nr_ano_nc']) : "DEFAULT").",
                   nr_nc      = ".(trim($args['nr_nc']) != '' ? intval($args['nr_nc']) : "DEFAULT")."
			 WHERE cd_reclamacao_analise_item = ".intval($args['cd_reclamacao_analise_item']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function retorno(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.reclamacao_analise
			   SET cd_usuario_retorno = ".intval($args['cd_usuario']).",
				   dt_retorno         = CURRENT_TIMESTAMP
			 WHERE cd_reclamacao_analise = ".intval($args['cd_reclamacao_analise']).";";
			 
		$result = $this->db->query($qr_sql);
	}
}

?>