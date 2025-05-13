<?php
class Acompanhamento_projetos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT ap.cd_acomp,
				   p.nome,
				   TO_CHAR(ap.dt_acomp, 'DD/MM/YYYY') AS dt_acomp,
				   TO_CHAR(ap.dt_encerramento, 'DD/MM/YYYY') AS dt_encerramento,
				   app.mes_ano, 
				   ap.cd_projeto
			  FROM projetos.acompanhamento_projetos ap
			  JOIN projetos.projetos p 
				ON ap.cd_projeto = p.codigo
			  LEFT JOIN (SELECT pp.cd_acomp, 
								TO_CHAR(pp.dt_previsao, 'YYYY/MM') AS mes_ano
						   FROM projetos.previsoes_projetos pp
						  WHERE pp.dt_exclusao  IS NULL 
							AND pp.dt_previsao = (SELECT MAX(pp1.dt_previsao)
													FROM projetos.previsoes_projetos pp1
												   WHERE pp1.dt_exclusao IS NULL
													 AND pp1.cd_acomp    = pp.cd_acomp)) app
				ON app.cd_acomp = ap.cd_acomp
			 WHERE 1 = 1
			   ".(((trim($args["dt_acompanhamento_ini"]) != "") and (trim($args["dt_acompanhamento_fim"]) != "")) ? " AND CAST(ap.dt_acomp AS DATE) BETWEEN TO_DATE('".$args["dt_acompanhamento_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_acompanhamento_fim"]."','DD/MM/YYYY')" : "")."
			   ".(((trim($args["dt_encerramento_ini"]) != "") and (trim($args["dt_encerramento_fim"]) != "")) ? " AND CAST(ap.dt_encerramento AS DATE) BETWEEN TO_DATE('".$args["dt_encerramento_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_encerramento_fim"]."','DD/MM/YYYY')" : "")."
			 ORDER BY dt_encerramento DESC, 
					  dt_acomp DESC;";
				  				  
		$result = $this->db->query($qr_sql);
	}
	
	function analista(&$result, $args=array())
	{
		$qr_sql = "
			SELECT a.cd_projeto, 
				   a.cd_acomp,
				   a.cd_analista,
				   b.guerra AS analista
			  FROM projetos.analista_projeto a
			  JOIN projetos.usuarios_controledi b 
				ON b.codigo     = a.cd_analista
			   AND a.cd_projeto = ".intval($args['cd_projeto'])." 
			   AND a.cd_acomp   = ".intval($args['cd_acomp'])."
			 ORDER BY analista;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function projeto(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   CAST(codigo AS TEXT) || ' - ' || nome AS text
			  FROM projetos.projetos 
			 WHERE dt_exclusao IS NULL
			   AND TRIM(COALESCE(nome,'')) <> ''
			 ORDER BY nome;";
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_projeto(&$result, $args=array())
	{
		$qr_sql = "
			SELECT p.nome
			  FROM projetos.projetos p
			  JOIN projetos.acompanhamento_projetos ap
			    ON ap.cd_projeto = p.codigo
			 WHERE ap.cd_acomp   = ".trim($args['cd_acomp']).";";
		$result = $this->db->query($qr_sql);
	}
	
	function responsavel(&$result, $args=array())
	{
		$qr_sql = "
			SELECT uc.codigo AS value, 
				   uc.nome AS text
			  FROM projetos.usuarios_controledi uc
			 WHERE (uc.tipo   <> 'X' AND 'GI' IN (uc.divisao,uc.divisao_ant)) 
				OR (SELECT COUNT(*)
					  FROM projetos.analista_projeto ap
					 WHERE ap.cd_analista = uc.codigo
					   AND ap.cd_projeto  = ".intval($args['cd_projeto'])."
					   AND ap.cd_acomp    = ".intval($args['cd_acomp']).") > 0
			ORDER BY text;";
		$result = $this->db->query($qr_sql);
	}	
	
	function cadastro(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ap.cd_projeto, 
				   ap.cd_acomp, 
				   ap.nome_acomp, 
				   TO_CHAR(ap.dt_acomp, 'DD/MM/YYYY') AS dt_acomp, 
				   ap.texto_acomp, 
				   ap.status_ar, 
				   ap.status_es, 
				   ap.status_au, 
				   ap.status_de, 
				   ap.status_me, 
				   ap.desc_ar, 
				   ap.desc_es, 
				   ap.desc_au, 
				   ap.desc_de, 
				   ap.desc_me, 
				   TO_CHAR(ap.dt_email, 'DD/MM/YYYY HH24:MI') AS dt_email, 
				   TO_CHAR(ap.dt_encerramento, 'DD/MM/YYYY HH24:MI') AS dt_encerramento, 
				   TO_CHAR(ap.dt_cancelamento, 'DD/MM/YYYY HH24:MI') AS dt_cancelamento,
				   p.nome AS ds_projeto
			  FROM projetos.acompanhamento_projetos ap
			  LEFT JOIN projetos.projetos p
				ON p.codigo = ap.cd_projeto
			 WHERE ap.cd_acomp = ".intval($args['cd_acomp']).";";
		$result = $this->db->query($qr_sql);
	}	
	
	function salvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_acomp']) > 0)
		{
			$qr_sql = " 
				UPDATE projetos.acompanhamento_projetos 
				   SET cd_projeto = ".(intval($args['cd_projeto']) == 0 ? "DEFAULT" : intval($args['cd_projeto'])).",
				       dt_alteracao = CURRENT_TIMESTAMP,
					   cd_usuario_alteracao = ".intval($args['cd_usuario'])."
				 WHERE cd_acomp   = ".intval($args['cd_acomp']).";";	
					  
			if(count($args['arr_analista']) > 0)
			{
				$qr_sql.= " 
					 DELETE 
					   FROM projetos.analista_projeto 
					  WHERE cd_projeto = ".intval($args['cd_projeto'])."
						AND cd_acomp   = ".intval($args['cd_acomp']).";";
				$nr_conta = 0;
				$nr_fim   = count($args['arr_analista']);
				
				while($nr_conta < $nr_fim)
				{
					$qr_sql.= " 
						INSERT INTO projetos.analista_projeto 
							 ( 
							   cd_projeto, 
							   cd_acomp,
							   cd_analista 
							 ) 
						VALUES 
							 ( 
							   ".intval($args['cd_projeto']).", 
							   ".intval($args['cd_acomp']).",
							   ".intval($args['arr_analista'][$nr_conta])." 
							 );";	
					$nr_conta++;
				}
			}
			
			$qr_roteiro = " 
				SELECT rpr.cd_reunioes_projetos_roteiro,
					   rpi.cd_reunioes_projetos_inicio
				  FROM projetos.reunioes_projetos_roteiro rpr
				  LEFT JOIN projetos.reunioes_projetos_inicio rpi
					ON rpr.cd_reunioes_projetos_roteiro = rpi.cd_reunioes_projetos_roteiro
				   AND rpi.cd_acomp                     = ".intval($args['cd_acomp'])."
				 ORDER BY rpr.nr_ordem;";
				 
			$ob_resul = $this->db->query($qr_roteiro);
			$arr_roteiro = $ob_resul->result_array();
			
			$qr_respostas = "";
			
			foreach($arr_roteiro as $ob_reg)
			{			
				if($ob_reg['cd_reunioes_projetos_inicio'] == "")
				{
					$qr_sql.= "
						INSERT INTO projetos.reunioes_projetos_inicio
							 (
							   cd_acomp,
							   cd_reunioes_projetos_roteiro, 
							   cd_usuario
							 )
						VALUES 
						     (
							   ".intval($args['cd_acomp']).",
							   ".$ob_reg['cd_reunioes_projetos_roteiro'].",
							   ".$args['cd_usuario']."
							 );";
				}
			}			

			$this->db->query($qr_sql);
			$retorno = intval($args['cd_acomp']);	
		}
		else
		{
			$new_id = intval($this->db->get_new_id("projetos.acompanhamento_projetos", "cd_acomp"));
			$qr_sql = " 
				INSERT INTO projetos.acompanhamento_projetos
					 (
					   cd_acomp, 
					   cd_projeto,
					   cd_usuario_inclusao
					 )
				VALUES 
					 (
					   ".$new_id.",
					   ".(intval($args['cd_projeto']) == 0 ? "DEFAULT" : intval($args['cd_projeto'])).",
					   ".intval($args['cd_usuario'])."
					 );";			
	  
			if(count($args['arr_analista']) > 0)
			{
				$qr_sql.= " 
					 DELETE 
					   FROM projetos.analista_projeto 
					  WHERE cd_projeto = ".intval($args['cd_projeto'])."
						AND cd_acomp   = ".$new_id.";";
						
				$nr_conta = 0;
				$nr_fim   = count($args['arr_analista']);
				
				while($nr_conta < $nr_fim)
				{
					$qr_sql.= " 
						INSERT INTO projetos.analista_projeto 
							 ( 
							   cd_projeto, 
							   cd_acomp,
							   cd_analista 
							 ) 
						VALUES 
							 ( 
							   ".intval($args['cd_projeto']).", 
							   ".$new_id.",
							   ".intval($args['arr_analista'][$nr_conta])." 
							 );
						   ";	
					$nr_conta++;
				}
			}
			
			$qr_roteiro = " 
				SELECT rpr.cd_reunioes_projetos_roteiro,
					   rpi.cd_reunioes_projetos_inicio
				  FROM projetos.reunioes_projetos_roteiro rpr
				  LEFT JOIN projetos.reunioes_projetos_inicio rpi
					ON rpr.cd_reunioes_projetos_roteiro = rpi.cd_reunioes_projetos_roteiro
				   AND rpi.cd_acomp                     = ".$new_id."
				 ORDER BY rpr.nr_ordem;";
				 
			$ob_resul = $this->db->query($qr_roteiro);
			$arr_roteiro = $ob_resul->result_array();
			
			$qr_respostas = "";
			
			foreach($arr_roteiro as $ob_reg)
			{			
				if($ob_reg['cd_reunioes_projetos_inicio'] == "")
				{
					$qr_sql.= "
						INSERT INTO projetos.reunioes_projetos_inicio
							 (
								cd_acomp,
								cd_reunioes_projetos_roteiro, 
								cd_usuario
							 )
						VALUES 
						     (
								".$new_id.",
								".$ob_reg['cd_reunioes_projetos_roteiro'].",
								".$args['cd_usuario']."
							 );";
				}
			}	  
	  
			$this->db->query($qr_sql);	
			$retorno = $new_id;			
		}
				
		return $retorno;
	}	
	
	function envia_email(&$result, $args=array())
	{
		$qr_sql = "
			SELECT acomp_projeto AS tl
			  FROM rotinas.acomp_projeto(".intval($args['cd_acomp']).");";
		
		$result = $this->db->query($qr_sql);
	}
	
	function encerra(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.acompanhamento_projetos 
			   SET dt_encerramento         = CURRENT_TIMESTAMP,
			       cd_usuario_encerramento = ".intval($args['cd_usuario'])."
			 WHERE cd_acomp = ".intval($args['cd_acomp'])." ;";
		
		$this->db->query($qr_sql);	
	}
	
	function cancela(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.acompanhamento_projetos 
			   SET dt_cancelamento         = CURRENT_TIMESTAMP,
			       cd_usuario_cancelamento = ".intval($args['cd_usuario'])."
			 WHERE cd_acomp = ".intval($args['cd_acomp'])." ;";
		
		$this->db->query($qr_sql);	
	}
	
	function reuniao(&$result, $args=array())
	{
		$qr_sql = "
			 SELECT rp.cd_reuniao, 
					rp.cd_acomp, 
					rp.descricao, 
					rp.assunto,
					rp.envolvidos, 
					rp.motivo, 
					TO_CHAR(rp.dt_reuniao, 'DD/MM/YYYY') AS dt_reuniao,
					rp.ds_arquivo,
					rp.ds_arquivo_fisico,
					TO_CHAR(rp.dt_email,'DD/MM/YYYY HH24:MI') AS dt_email,
					(SELECT COUNT(*) 
					   FROM projetos.reunioes_projeto_anexo a
					  WHERE a.dt_exclusao IS NULL
					    AND a.cd_reuniao = rp.cd_reuniao) AS tl_anexo
			   FROM projetos.reunioes_projetos rp
			  WHERE rp.dt_exclusao IS NULL
				AND rp.cd_acomp    = ".intval($args['cd_acomp'])."
			  ORDER BY rp.dt_reuniao DESC, 
					   rp.cd_reuniao;";
		$result = $this->db->query($qr_sql);
	}	
	
	function envolvido(&$result, $args=array())
	{
		$qr_sql = "
			 SELECT rp.cd_reuniao,
					rpe.cd_usuario,
					uc.guerra AS nome
			   FROM projetos.reunioes_projetos rp
			   JOIN projetos.reunioes_projetos_envolvidos rpe
				 ON rpe.cd_acomp   = rp.cd_acomp
				AND rpe.cd_reuniao = rp.cd_reuniao
			   JOIN projetos.usuarios_controledi uc
				 ON rpe.cd_usuario = uc.codigo
			  WHERE rp.dt_exclusao IS NULL
				AND rp.cd_acomp    = ".intval($args['cd_acomp'])."
			  ORDER BY rp.cd_reuniao,
					   uc.guerra;";
		$result = $this->db->query($qr_sql);
	}	
	
	function presentes(&$result, $args=array())
	{
		$qr_sql = "
			SELECT uc.codigo AS value,
				   uc.nome AS text,
				   CASE WHEN r.cd_usuario IS NOT NULL THEN 0
				        ELSE 1
				   END AS nr_ordem
			  FROM projetos.usuarios_controledi uc
			  LEFT JOIN projetos.reunioes_projetos_envolvidos r
			    ON r.cd_usuario = uc.codigo
			   AND r.cd_acomp   = ".intval($args['cd_acomp'])."
			   AND r.cd_reuniao = ".intval($args['cd_reuniao'])."
               AND r.dt_exclusao IS NULL			   
			 WHERE (uc.tipo    NOT IN ('X','T') OR r.cd_usuario = uc.codigo)
			   AND uc.divisao NOT IN ('SNG', 'LM2')
			 ORDER BY nr_ordem,
					  uc.nome";
					  
		$result = $this->db->query($qr_sql);
	}
	
	function cadastro_reuniao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT rp.cd_reuniao, 
				   TO_CHAR(rp.dt_reuniao,'DD/MM/YYYY') AS dt_reuniao, 
				   rp.descricao, 
				   rp.envolvidos,
				   rp.assunto,
				   rp.motivo,
				   rp.ds_arquivo,
				   rp.ds_arquivo_fisico
			  FROM projetos.reunioes_projetos rp
			 WHERE rp.cd_acomp   = ".intval($args['cd_acomp'])." 
			   AND rp.cd_reuniao = ".intval($args['cd_reuniao']).";";
			   					  
		$result = $this->db->query($qr_sql);
	}
	
	function presentes_reuniao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT e.cd_usuario,
			       nome
			  FROM projetos.reunioes_projetos_envolvidos e
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = e.cd_usuario
			 WHERE e.cd_acomp   = ".intval($args['cd_acomp'])." 
			   AND e.cd_reuniao = ".intval($args['cd_reuniao'])."
			   AND e.dt_exclusao IS NULL;";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_reuniao(&$result, $args=array())
	{
		if(intval($args['cd_reuniao']) == 0)
		{
			$cd_reuniao = intval($this->db->get_new_id("projetos.reunioes_projetos", "cd_reuniao"));
		
			$qr_sql = "
				INSERT INTO projetos.reunioes_projetos
					 (
						cd_reuniao,
						cd_acomp, 
						dt_reuniao, 
						descricao, 
						assunto,
						motivo,
						cd_usuario_inclusao,
						cd_usuario_alteracao
					 )
				VALUES
				     (
						".intval($cd_reuniao).",
						".intval($args['cd_acomp']).",
						TO_DATE('".$args['dt_reuniao']."', 'DD/MM/YYYY'),
						".(trim($args['descricao']) != '' ? "'".trim($args['descricao'])."'" : "DEFAULT").",
						".(trim($args['assunto']) != '' ? "'".trim($args['assunto'])."'" : "DEFAULT").",
						".(trim($args['motivo']) != '' ? "'".trim($args['motivo'])."'" : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					 );";
			
			
			foreach($args['cd_usuario_presente'] as $item)
			{
				$qr_sql .= "
					INSERT INTO projetos.reunioes_projetos_envolvidos
						 (
						   cd_acomp,
						   cd_reuniao,
						   cd_usuario
						 )
					VALUES
						 (
						   ".intval($args['cd_acomp']).",
						   ".intval($cd_reuniao).",
						   ".trim($item)."
						 );";
			}
			
		}
		else
		{
			$cd_reuniao = $args['cd_reuniao'];
			
			$qr_sql = "
				UPDATE projetos.reunioes_projetos
				   SET dt_reuniao           = TO_DATE('".$args['dt_reuniao']."', 'DD/MM/YYYY'),
				       descricao            = ".(trim($args['descricao']) != '' ? "'".trim($args['descricao'])."'" : "DEFAULT").",
					   assunto              = ".(trim($args['assunto']) != '' ? "'".trim($args['assunto'])."'" : "DEFAULT").",
					   motivo               = ".(trim($args['motivo']) != '' ? "'".trim($args['motivo'])."'" : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_reuniao = ".intval($args['cd_reuniao']).";";
				 
			$qr_sql .= "
				UPDATE projetos.reunioes_projetos_envolvidos
				   SET dt_exclusao = CURRENT_TIMESTAMP
				 WHERE cd_usuario NOT IN ('".implode("', '", $args['cd_usuario_presente'])."')
				   AND cd_reuniao = ".intval($args['cd_reuniao']).";";	
				   
			foreach($args['cd_usuario_presente'] as $item)
			{			
				$qr_sql .= "
					INSERT INTO projetos.reunioes_projetos_envolvidos
						 (
						   cd_acomp,
						   cd_reuniao,
						   cd_usuario
						 )
					SELECT ".intval($args['cd_acomp']).",
						   ".intval($cd_reuniao).",
						   '".trim($item)."'
					 WHERE 0 = 
					     (
					     SELECT COUNT(*)
					       FROM projetos.reunioes_projetos_envolvidos
					      WHERE cd_acomp   = ".intval($args['cd_acomp'])."
						    AND cd_reuniao =  ".intval($cd_reuniao)."
					        AND cd_usuario = '".trim($item)."'
					        AND dt_exclusao IS NULL
					     );";
			}	
		}
	
		$result = $this->db->query($qr_sql);
		
		return $cd_reuniao;
	}
	
	function salvar_reuniao_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.reunioes_projeto_anexo
			     (
					cd_reuniao,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_reuniao']).",
					".str_escape($args['arquivo']).",
					".str_escape($args['arquivo_nome']).",
					".intval($args['cd_usuario'])."
				 )";
			
		$result = $this->db->query($qr_sql);
	}
	
	function listar_reuniao_anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT a.cd_reuniao_anexo,
				   a.arquivo,
				   a.arquivo_nome,
				   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM projetos.reunioes_projeto_anexo a
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = a.cd_usuario_inclusao
			 WHERE a.cd_reuniao = ".intval($args['cd_reuniao'])."
			   AND a.dt_exclusao IS NULL
			 ORDER BY a.dt_inclusao DESC";
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_reuniao_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.reunioes_projeto_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_reuniao_anexo = ".intval($args['cd_reuniao_anexo']).";";
		$this->db->query($qr_sql);
	}
	
	function roteiro(&$result, $args=array())
	{
		$qr_sql = "
			SELECT rpr.nr_ordem,
				   rpr.cd_reunioes_projetos_roteiro,
				   rpr.ds_reunioes_projetos_roteiro
			  FROM projetos.reunioes_projetos_roteiro rpr
			 ORDER BY rpr.nr_ordem;";
		$result = $this->db->query($qr_sql);
	}
	
	function email_reuniao(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.reunioes_projetos
			   SET dt_email         = CURRENT_TIMESTAMP,
			       cd_usuario_email = ".intval($args['cd_usuario'])."
		     WHERE cd_acomp    = ".intval($args['cd_acomp'])."
			   AND cd_reuniao  = ".intval($args['cd_reuniao']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function status_etapa(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value, 
				   descricao AS text
			  FROM listas 
			 WHERE categoria = 'STPJ' 
			 ORDER BY categoria, 
					  codigo;";
		$result = $this->db->query($qr_sql);
	}	
	
	function registro_operacional(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ap.cd_acompanhamento_registro_operacional,
				   ap.ds_nome,
				   TO_CHAR(ap.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro, 
				   ap.cd_acomp,
				   uc.nome,
				   uc.guerra,
				   TO_CHAR(ap.dt_finalizado,'DD/MM/YYYY') AS dt_finalizado 
			  FROM projetos.acompanhamento_registro_operacional ap
			  JOIN projetos.usuarios_controledi uc	
                ON ap.cd_usuario  = uc.codigo			  
			 WHERE ap.cd_acomp    = ".intval($args['cd_acomp'])."
			   AND ap.dt_exclusao IS NULL
			 ORDER BY ap.dt_cadastro DESC;";
		$result = $this->db->query($qr_sql);
	}	
	
	function escopo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ae.cd_acompanhamento_escopos, 
				   TO_CHAR(ae.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro, 
				   ae.ds_objetivos,
				   ae.cd_acomp,
				   uc.nome,
				   uc.guerra
			  FROM projetos.acompanhamento_escopos ae
			  JOIN projetos.usuarios_controledi uc		
                ON ae.cd_usuario  = uc.codigo			  
			 WHERE ae.cd_acomp    = ".intval($args['cd_acomp'])."
			   AND ae.dt_exclusao IS NULL
			 ORDER BY ae.dt_cadastro DESC;";
			 
		$result = $this->db->query($qr_sql);
	}	
	
	function wbs(&$result, $args=array())
	{
		$qr_sql = "
			SELECT aw.cd_acompanhamento_wbs, 
				   aw.cd_acomp,
				   TO_CHAR(aw.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro,
				   aw.ds_arquivo,
				   aw.ds_arquivo_fisico,
				   uc.nome,
				   uc.guerra
			  FROM projetos.acompanhamento_wbs aw
			  JOIN projetos.usuarios_controledi uc	
                ON aw.cd_usuario = uc.codigo			  
			 WHERE aw.cd_acomp   = ".intval($args['cd_acomp'])."
			   AND aw.dt_exclusao IS NULL
			 ORDER BY aw.dt_cadastro DESC;";

		$result = $this->db->query($qr_sql);
	}
	
	function mudanca_escopo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ae.cd_acompanhamento_mudanca_escopo, 
				   nr_numero,
				   TO_CHAR(ae.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro, 
				   TO_CHAR(ae.dt_mudanca,'DD/MM/YYYY') AS dt_mudanca, 
				   ae.cd_acomp,
				   uc.nome AS ds_nome_solicitante,
				   uc.guerra AS ds_solicitante
			  FROM projetos.acompanhamento_mudanca_escopo ae,
				   projetos.usuarios_controledi uc				  
			 WHERE ae.cd_acomp       = ".intval($args['cd_acomp'])."
			   AND ae.cd_solicitante = uc.codigo
			   AND ae.dt_exclusao    IS NULL
			 ORDER BY ae.dt_cadastro DESC;";
		$result = $this->db->query($qr_sql);
	}
	
	function cadastro_registro_operacional( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT aro.cd_acompanhamento_registro_operacional,
				   aro.cd_acomp,
				   aro.ds_nome,
				   aro.ds_processo_faz,
                   aro.ds_processo_executado,
				   aro.ds_calculo,
				   aro.ds_responsaveis,
				   aro.ds_requesito,
				   aro.ds_necessario,
				   aro.ds_integridade,
				   aro.ds_resultado,
				   aro.ds_local,
				   aro.dt_finalizado,
				   uc.nome,
				   p.nome AS projeto,
				   aro.ds_processo_faz_complemento,
				   aro.ds_processo_executado_complemento,
				   aro.ds_calculo_complemento,
				   aro.ds_requesito_complemento,
				   aro.ds_necessario_complemento,
				   aro.ds_integridade_complemento,
				   aro.ds_resultado_complemento,
				   aro.cd_usuario
			  FROM projetos.acompanhamento_registro_operacional aro
			  JOIN projetos.acompanhamento_projetos ap
			    ON ap.cd_acomp = aro.cd_acomp
			  JOIN projetos.projetos p
			    ON ap.cd_projeto = p.codigo	 
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = aro.cd_usuario
			 WHERE aro.cd_acompanhamento_registro_operacional = ".intval($args['cd_acompanhamento_registro_operacional']).";";
 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_registro_operacional( &$result, $args=array() )
	{
		if(intval($args['cd_acompanhamento_registro_operacional']) == 0)
		{
			$cd_acompanhamento_registro_operacional = intval($this->db->get_new_id("projetos.acompanhamento_registro_operacional", "cd_acompanhamento_registro_operacional"));
		
			$qr_sql = "
				INSERT INTO projetos.acompanhamento_registro_operacional
					 (
					   cd_acompanhamento_registro_operacional,
					   cd_acomp,
					   ds_nome,
					   ds_processo_faz,
                       ds_processo_executado,
					   ds_calculo,
					   ds_responsaveis,
					   ds_requesito,
					   ds_necessario,
					   ds_integridade,
					   ds_resultado,
					   ds_local,
					   cd_usuario
					 )
				VALUES
				     (
					   ".$cd_acompanhamento_registro_operacional.",
					   ".($args['cd_acomp'] != '' ? intval($args['cd_acomp']) : "DEFAULT").",
					   ".($args['ds_nome'] != '' ? "'".trim($args['ds_nome'])."'" : "DEFAULT").",
					   ".($args['ds_processo_faz'] != '' ? "'".trim($args['ds_processo_faz'])."'" : "DEFAULT").",
					   ".($args['ds_processo_executado'] != '' ? "'".trim($args['ds_processo_executado'])."'" : "DEFAULT").",
					   ".($args['ds_calculo'] != '' ? "'".trim($args['ds_calculo'])."'" : "DEFAULT").",
					   ".($args['ds_responsaveis'] != '' ? "'".trim($args['ds_responsaveis'])."'" : "DEFAULT").",
					   ".($args['ds_requesito'] != '' ? "'".trim($args['ds_requesito'])."'" : "DEFAULT").",
					   ".($args['ds_necessario'] != '' ? "'".trim($args['ds_necessario'])."'" : "DEFAULT").",
					   ".($args['ds_integridade'] != '' ? "'".trim($args['ds_integridade'])."'" : "DEFAULT").",
					   ".($args['ds_resultado'] != '' ? "'".trim($args['ds_resultado'])."'" : "DEFAULT").",
					   ".($args['ds_local'] != '' ? "'".trim($args['ds_local'])."'" : "DEFAULT").",
					   ".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.acompanhamento_registro_operacional
				   SET cd_acomp                          = ".($args['cd_acomp'] != '' ? intval($args['cd_acomp']) : "DEFAULT").",
					   ds_nome                           = ".($args['ds_nome'] != '' ? "'".trim($args['ds_nome'])."'" : "DEFAULT").",
					   ds_processo_faz                   = ".($args['ds_processo_faz'] != '' ? "'".trim($args['ds_processo_faz'])."'" : "DEFAULT").",
                       ds_processo_executado             = ".($args['ds_processo_executado'] != '' ? "'".trim($args['ds_processo_executado'])."'" : "DEFAULT").",
					   ds_calculo                        = ".($args['ds_calculo'] != '' ? "'".trim($args['ds_calculo'])."'" : "DEFAULT").",
					   ds_responsaveis                   = ".($args['ds_responsaveis'] != '' ? "'".trim($args['ds_responsaveis'])."'" : "DEFAULT").",
					   ds_requesito                      = ".($args['ds_requesito'] != '' ? "'".trim($args['ds_requesito'])."'" : "DEFAULT").",
					   ds_necessario                     = ".($args['ds_necessario'] != '' ? "'".trim($args['ds_necessario'])."'" : "DEFAULT").",
					   ds_integridade                    = ".($args['ds_integridade'] != '' ? "'".trim($args['ds_integridade'])."'" : "DEFAULT").",
					   ds_resultado                      = ".($args['ds_resultado'] != '' ? "'".trim($args['ds_resultado'])."'" : "DEFAULT").",
					   ds_local                          = ".($args['ds_local'] != '' ? "'".trim($args['ds_local'])."'" : "DEFAULT").",
					   ds_processo_faz_complemento       = ".($args['ds_processo_faz_complemento'] != '' ? "'".trim($args['ds_processo_faz_complemento'])."'" : "DEFAULT").",
					   ds_processo_executado_complemento = ".($args['ds_processo_executado_complemento'] != '' ? "'".trim($args['ds_processo_executado_complemento'])."'" : "DEFAULT").",
					   ds_calculo_complemento            = ".($args['ds_calculo_complemento'] != '' ? "'".trim($args['ds_calculo_complemento'])."'" : "DEFAULT").",
					   ds_requesito_complemento          = ".($args['ds_requesito_complemento'] != '' ? "'".trim($args['ds_requesito_complemento'])."'" : "DEFAULT").",
					   ds_necessario_complemento         = ".($args['ds_necessario_complemento'] != '' ? "'".trim($args['ds_necessario_complemento'])."'" : "DEFAULT").",
					   ds_integridade_complemento        = ".($args['ds_integridade_complemento'] != '' ? "'".trim($args['ds_integridade_complemento'])."'" : "DEFAULT").",
					   ds_resultado_complemento          = ".($args['ds_resultado_complemento'] != '' ? "'".trim($args['ds_resultado_complemento'])."'" : "DEFAULT")."
				 WHERE cd_acompanhamento_registro_operacional = ".intval($args['cd_acompanhamento_registro_operacional']).";";
				 
			$cd_acompanhamento_registro_operacional = intval($args['cd_acompanhamento_registro_operacional']);
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_acompanhamento_registro_operacional;
	}
	
	function finalizar_registro_operacional(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.acompanhamento_registro_operacional
			   SET dt_finalizado         = CURRENT_TIMESTAMP,
			       cd_usuario_finalizado = ".intval($args['cd_usuario'])."
			 WHERE cd_acompanhamento_registro_operacional = ".intval($args['cd_acompanhamento_registro_operacional']).";" ;
	
		$result = $this->db->query($qr_sql);
	}
	
	function reiniciar_registro_operacional(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.acompanhamento_registro_operacional
			   SET dt_finalizado         = NULL,
			       cd_usuario_finalizado = NULL
			 WHERE cd_acompanhamento_registro_operacional = ".intval($args['cd_acompanhamento_registro_operacional']).";" ;
	
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_registro_operacional_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.acompanhamento_registro_operacional_anexo
			     (
					cd_acompanhamento_registro_operacional,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_acompanhamento_registro_operacional']).",
					".str_escape($args['arquivo']).",
					".str_escape($args['arquivo_nome']).",
					".intval($args['cd_usuario'])."
				 )";
			
		$result = $this->db->query($qr_sql);
	}
	
	function listar_registro_operacional_anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT a.cd_acompanhamento_registro_operacional_anexo,
				   a.arquivo,
				   a.arquivo_nome,
				   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM projetos.acompanhamento_registro_operacional_anexo a
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = a.cd_usuario_inclusao
			 WHERE a.cd_acompanhamento_registro_operacional = ".intval($args['cd_acompanhamento_registro_operacional'])."
			   AND a.dt_exclusao IS NULL
			 ORDER BY a.dt_inclusao DESC";
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_registro_operacional_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.acompanhamento_registro_operacional_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_acompanhamento_registro_operacional_anexo = ".intval($args['cd_acompanhamento_registro_operacional_anexo']).";";
		$this->db->query($qr_sql);
	}
	
	function permissao_analista(&$result, $args=array())
	{
		$qr_sql = "
			SELECT COUNT(cd_analista) AS fl_analista
			  FROM projetos.analista_projeto 
		     WHERE cd_projeto  = (SELECT cd_projeto
			                        FROM projetos.acompanhamento_projetos
				 				   WHERE cd_acomp = ".intval($args['cd_acomp']).")
								     AND cd_acomp    = ".intval($args['cd_acomp'])."
								     AND cd_analista = ".intval($args['cd_usuario']).";";
									 
		$result = $this->db->query($qr_sql);
	}
	
	function cadastro_escopo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT e.cd_acompanhamento_escopos,
			       e.ds_objetivos,
			       e.ds_regras,
				   e.ds_impacto,
				   e.ds_responsaveis,
				   e.ds_solucao,
				   e.ds_recurso,
				   e.ds_viabilidade,
				   e.ds_modelagem,
				   e.ds_produtos,
				   p.nome AS projeto
			  FROM projetos.acompanhamento_escopos e
			  JOIN projetos.acompanhamento_projetos ap
			    ON ap.cd_acomp = e.cd_acomp
			  JOIN projetos.projetos p
			    ON p.codigo = ap.cd_projeto
			 WHERE e.cd_acompanhamento_escopos = ".intval($args['cd_acompanhamento_escopos']).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function salvar_escopo(&$result, $args=array())
	{
		if(intval($args['cd_acompanhamento_escopos']) == 0)
		{
			$cd_acompanhamento_escopos = intval($this->db->get_new_id("projetos.acompanhamento_escopos", "cd_acompanhamento_escopos"));
			
			$qr_sql = "
				INSERT INTO projetos.acompanhamento_escopos
					 (
						cd_acompanhamento_escopos,
						cd_acomp,
						ds_objetivos,
						ds_regras,
						ds_impacto,
						ds_responsaveis,
						ds_solucao,
						ds_recurso,
						ds_viabilidade,
						ds_modelagem,
						ds_produtos,
						cd_usuario,
						cd_usuario_alteracao,
						dt_cadastro,
						dt_alteracao
					 ) 
				VALUES
					 (
						".$cd_acompanhamento_escopos.",
						".intval($args['cd_acomp']).",
						".(trim($args['ds_objetivos']) != '' ? str_escape($args['ds_objetivos']) : "DEFAULT").",
						".(trim($args['ds_regras']) != '' ? str_escape($args['ds_regras']) : "DEFAULT").",
						".(trim($args['ds_impacto']) != '' ? str_escape($args['ds_impacto']) : "DEFAULT").",
						".(trim($args['ds_responsaveis']) != '' ? str_escape($args['ds_responsaveis']) : "DEFAULT").",
						".(trim($args['ds_solucao']) != '' ? str_escape($args['ds_solucao']): "DEFAULT").",
						".(trim($args['ds_recurso']) != '' ? str_escape($args['ds_recurso']) : "DEFAULT").",
						".(trim($args['ds_viabilidade']) != '' ? str_escape($args['ds_viabilidade']) : "DEFAULT").",
						".(trim($args['ds_modelagem']) != '' ? str_escape($args['ds_modelagem']) : "DEFAULT").",
						".(trim($args['ds_produtos']) != '' ? str_escape($args['ds_produtos']) : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario']).",
						CURRENT_TIMESTAMP,
						CURRENT_TIMESTAMP
				     );";
		}
		else
		{
			$cd_acompanhamento_escopos = $args['cd_acompanhamento_escopos'];
			
			$qr_sql = "
				UPDATE projetos.acompanhamento_escopos 
				   SET ds_objetivos         = ".(trim($args['ds_objetivos']) != '' ? str_escape($args['ds_objetivos']) : "DEFAULT").",
					   ds_regras            = ".(trim($args['ds_regras']) != '' ? str_escape($args['ds_regras']) : "DEFAULT").",
					   ds_impacto           = ".(trim($args['ds_impacto']) != '' ? str_escape($args['ds_impacto']) : "DEFAULT").",
					   ds_responsaveis      = ".(trim($args['ds_responsaveis']) != '' ? str_escape($args['ds_responsaveis']) : "DEFAULT").",
					   ds_solucao           = ".(trim($args['ds_solucao']) != '' ? str_escape($args['ds_solucao']) : "DEFAULT").",
					   ds_recurso           = ".(trim($args['ds_recurso']) != '' ? str_escape($args['ds_recurso']) : "DEFAULT").",
					   ds_viabilidade       = ".(trim($args['ds_viabilidade']) != '' ? str_escape($args['ds_viabilidade']) : "DEFAULT").",
					   ds_modelagem         = ".(trim($args['ds_modelagem']) != '' ? str_escape($args['ds_modelagem']) : "DEFAULT").",
					   ds_produtos          = ".(trim($args['ds_produtos']) != '' ? str_escape($args['ds_produtos']) : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_acompanhamento_escopos = ".intval($args['cd_acompanhamento_escopos'])." 
				   AND cd_acomp                  = ".intval($args['cd_acomp']).";";
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_acompanhamento_escopos;
	}
	
	function salvar_wbs(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.acompanhamento_wbs
			     (
					cd_acomp,
					ds_arquivo,
					ds_arquivo_fisico,
					cd_usuario,
					cd_usuario_alteracao,
					dt_cadastro,
					dt_alteracao
				 )
		    VALUES
			     (
					".intval($args['cd_acomp']).",
					".str_escape($args['ds_arquivo']).",
					".str_escape($args['ds_arquivo_fisico']).",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario']).",
					CURRENT_TIMESTAMP,
					CURRENT_TIMESTAMP
				 )";
			
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_wbs(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.acompanhamento_wbs
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_acompanhamento_wbs = ".intval($args['cd_acompanhamento_wbs']).";";
		$this->db->query($qr_sql);
	}
	
	function cadastro_mudanca_escopo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT me.cd_acompanhamento_mudanca_escopo, 
				   me.nr_numero,
				   me.cd_solicitante,
				   me.cd_analista,
				   me.cd_etapa,
				   TO_CHAR(me.dt_mudanca,'DD/MM/YYYY') AS dt_mudanca,
				   TO_CHAR(me.dt_aprovacao,'DD/MM/YYYY') AS dt_aprovacao,
				   me.nr_dias,							   
				   me.ds_descricao,
				   me.ds_regras,
				   me.ds_impacto,
				   me.ds_responsaveis,
				   me.ds_solucao,
				   me.ds_recurso,
				   me.ds_viabilidade,
				   me.ds_modelagem,
				   me.ds_produtos,
				   p.nome AS projeto
			  FROM projetos.acompanhamento_mudanca_escopo me
			  JOIN projetos.acompanhamento_projetos ap
			    ON ap.cd_acomp = me.cd_acomp
			  JOIN projetos.projetos p
			    ON p.codigo = ap.cd_projeto
			 WHERE me.cd_acompanhamento_mudanca_escopo = ".intval($args['cd_acompanhamento_mudanca_escopo'])."; ";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_mudanca_escopo(&$result, $args=array())
	{
		if(intval($args['cd_acompanhamento_mudanca_escopo']) == 0)
		{
			$cd_acompanhamento_mudanca_escopo = intval($this->db->get_new_id("projetos.acompanhamento_mudanca_escopo", "cd_acompanhamento_mudanca_escopo"));
			
			$qr_sql = "
				INSERT INTO projetos.acompanhamento_mudanca_escopo
					 (
						cd_acompanhamento_mudanca_escopo,
						cd_acomp,
						nr_numero,
						cd_solicitante,
						cd_analista,
						cd_etapa,
						dt_mudanca,
						dt_aprovacao,
						nr_dias,
						ds_descricao,
						ds_regras,
						ds_impacto,
						ds_responsaveis,
						ds_solucao,
						ds_recurso,
						ds_viabilidade,
						ds_modelagem,
						ds_produtos,
						cd_usuario,
						cd_usuario_alteracao,
						dt_cadastro,
						dt_alteracao
					 ) 
				VALUES
					 (
						".$cd_acompanhamento_mudanca_escopo.",
						".intval($args['cd_acomp']).",
						".(trim($args['nr_numero']) != '' ? "'".intval($args['nr_numero'])."'" : "DEFAULT").", 
						".(trim($args['cd_solicitante']) != '' ? intval($args['cd_solicitante']) : "DEFAULT").",
						".(trim($args['cd_analista']) != '' ? intval($args['cd_analista']) : "DEFAULT").",
						".(trim($args['cd_etapa']) != '' ? "'".trim($args['cd_etapa'])."'" : "DEFAULT").", 
						".(trim($args['dt_mudanca']) != '' ? "TO_DATE('".$args['dt_mudanca']."','DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['dt_aprovacao']) != '' ? "TO_DATE('".$args['dt_aprovacao']."','DD/MM/YYYY')" : "DEFAULT").",
						".(trim($args['nr_dias']) != '' ? intval($args['nr_dias']) : "DEFAULT").",
						".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
						".(trim($args['ds_regras']) != '' ? str_escape($args['ds_regras']) : "DEFAULT").",
						".(trim($args['ds_impacto']) != '' ? str_escape($args['ds_impacto']) : "DEFAULT").",
						".(trim($args['ds_responsaveis']) != '' ? str_escape($args['ds_responsaveis']) : "DEFAULT").",
						".(trim($args['ds_solucao']) != '' ? str_escape($args['ds_solucao']): "DEFAULT").",
						".(trim($args['ds_recurso']) != '' ? str_escape($args['ds_recurso']) : "DEFAULT").",
						".(trim($args['ds_viabilidade']) != '' ? str_escape($args['ds_viabilidade']) : "DEFAULT").",
						".(trim($args['ds_modelagem']) != '' ? str_escape($args['ds_modelagem']) : "DEFAULT").",
						".(trim($args['ds_produtos']) != '' ? str_escape($args['ds_produtos']) : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario']).",
						CURRENT_TIMESTAMP,
						CURRENT_TIMESTAMP
				     );";
		}
		else
		{
			$cd_acompanhamento_mudanca_escopo = $args['cd_acompanhamento_mudanca_escopo'];
			
			$qr_sql = "
				UPDATE projetos.acompanhamento_mudanca_escopo 
				   SET nr_numero            = ".(trim($args['nr_numero']) != '' ? "'".intval($args['nr_numero'])."'" : "DEFAULT").", 
					   cd_solicitante       = ".(trim($args['cd_solicitante']) != '' ? intval($args['cd_solicitante']) : "DEFAULT").",
					   cd_analista          = ".(trim($args['cd_analista']) != '' ? intval($args['cd_analista']) : "DEFAULT").",
					   cd_etapa             = ".(trim($args['cd_etapa']) != '' ? "'".trim($args['cd_etapa'])."'" : "DEFAULT").", 
					   dt_mudanca           = ".(trim($args['dt_mudanca']) != '' ? "TO_DATE('".$args['dt_mudanca']."','DD/MM/YYYY')" : "DEFAULT").",
					   dt_aprovacao         = ".(trim($args['dt_aprovacao']) != '' ? "TO_DATE('".$args['dt_aprovacao']."','DD/MM/YYYY')" : "DEFAULT").",
					   nr_dias              = ".(trim($args['nr_dias']) != '' ? intval($args['nr_dias']) : "DEFAULT").",
				       ds_descricao         = ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
					   ds_regras            = ".(trim($args['ds_regras']) != '' ? str_escape($args['ds_regras']) : "DEFAULT").",
					   ds_impacto           = ".(trim($args['ds_impacto']) != '' ? str_escape($args['ds_impacto']) : "DEFAULT").",
					   ds_responsaveis      = ".(trim($args['ds_responsaveis']) != '' ? str_escape($args['ds_responsaveis']) : "DEFAULT").",
					   ds_solucao           = ".(trim($args['ds_solucao']) != '' ? str_escape($args['ds_solucao']) : "DEFAULT").",
					   ds_recurso           = ".(trim($args['ds_recurso']) != '' ? str_escape($args['ds_recurso']) : "DEFAULT").",
					   ds_viabilidade       = ".(trim($args['ds_viabilidade']) != '' ? str_escape($args['ds_viabilidade']) : "DEFAULT").",
					   ds_modelagem         = ".(trim($args['ds_modelagem']) != '' ? str_escape($args['ds_modelagem']) : "DEFAULT").",
					   ds_produtos          = ".(trim($args['ds_produtos']) != '' ? str_escape($args['ds_produtos']) : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_acompanhamento_mudanca_escopo = ".intval($args['cd_acompanhamento_mudanca_escopo'])." 
				   AND cd_acomp                         = ".intval($args['cd_acomp']).";";
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_acompanhamento_mudanca_escopo;
	}
	
	function salvar_etapa(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.acompanhamento_projetos
			   SET dt_alteracao         = CURRENT_TIMESTAMP,
				   status_ar            = ".(trim($args['status_ar']) == "" ? "DEFAULT" : "'".$args['status_ar']."'").",
				   status_es            = ".(trim($args['status_es']) == "" ? "DEFAULT" : "'".$args['status_es']."'").",
				   status_au            = ".(trim($args['status_au']) == "" ? "DEFAULT" : "'".$args['status_au']."'").",
				   status_de            = ".(trim($args['status_de']) == "" ? "DEFAULT" : "'".$args['status_de']."'").",
				   status_me            = ".(trim($args['status_me']) == "" ? "DEFAULT" : "'".$args['status_me']."'").",
				   desc_ar              = ".(trim($args['desc_ar']) == "" ? "DEFAULT" : "'".$args['desc_ar']."'").",
				   desc_es              = ".(trim($args['desc_es']) == "" ? "DEFAULT" : "'".$args['desc_es']."'").",
				   desc_au              = ".(trim($args['desc_au']) == "" ? "DEFAULT" : "'".$args['desc_au']."'").",
				   desc_de              = ".(trim($args['desc_de']) == "" ? "DEFAULT" : "'".$args['desc_de']."'").",
				   desc_me              = ".(trim($args['desc_me']) == "" ? "DEFAULT" : "'".$args['desc_me']."'").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario'])."
			 WHERE cd_acomp = ".intval($args['cd_acomp'])."
				  ";		
		$this->db->query($qr_sql);
		
		return intval($args['cd_acomp']);
	}
	
	function solicitante(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo AS value,
				   nome AS text
			  FROM projetos.usuarios_controledi 
			 WHERE divisao NOT IN ('SNG', 'LM2')
			   AND tipo    NOT IN ('X', 'T')
			 ORDER BY nome";
			
		$result = $this->db->query($qr_sql);
	}
	
	function previsao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT pp.cd_previsao, 
				   pp.cd_acomp, 
				   pp.descricao, 
				   pp.mes, 
				   pp.ano, 
				   pp.obs,
				   TO_CHAR(pp.dt_previsao, 'YYYY/MM') AS mes_ano,
				   TO_CHAR(pp.dt_previsao, 'DD/MM/YYYY') AS dt_previsao, 
				   pp.dt_previsao
			  FROM projetos.previsoes_projetos pp
			 WHERE pp.dt_exclusao IS NULL 
			   AND pp.cd_acomp    = ".intval($args['cd_acomp'])."
			 ORDER BY pp.dt_previsao DESC;";
		$result = $this->db->query($qr_sql);
	}
	
	function cadastro_previsao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT mes AS mes, 
				   ano, 
				   descricao, 
				   obs,
				   TO_CHAR(dt_previsao, 'DD/MM/YYYY') AS mes_ano,
				   cd_previsao
		      FROM projetos.previsoes_projetos 
			 WHERE cd_previsao = ".intval($args['cd_previsao']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function previsao_pdf(&$result, $args=array())
	{
		$qr_sql = "
			SELECT mes AS mes, 
				   ano, 
				   descricao, 
				   obs,
				   TO_CHAR(dt_previsao, 'DD/MM/YYYY') AS mes_ano,
				   cd_previsao
		      FROM projetos.previsoes_projetos 
			 WHERE dt_exclusao IS NULL
			   AND cd_acomp = ".intval($args['cd_acomp'])."
			   ".(intval($args['cd_previsao']) > 0 ? "AND cd_previsao = ".intval($args['cd_previsao']) : "").";";
		
		$result = $this->db->query($qr_sql);
	}
	
	function previsao_valida_mes(&$result, $args=array())
	{
		$qr_sql = "
			SELECT COUNT(*) AS tl
		      FROM projetos.previsoes_projetos 
			 WHERE dt_exclusao IS NULL
			   AND cd_previsao != ".intval($args['cd_previsao'])."
			   AND cd_acomp    = ".intval($args['cd_acomp'])."
			   AND ano         = '".$args['ano']."'
			   AND UPPER(mes)  = UPPER('".mes_format($args['mes'], 'mmmm')."');";
		
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_previsao(&$result, $args=array())
	{
	
		if(intval($args['cd_previsao']) == 0)
		{
			$cd_previsao = intval($this->db->get_new_id("projetos.previsoes_projetos", "cd_previsao"));
			
			$qr_sql = "
				INSERT INTO projetos.previsoes_projetos
					 (
					   cd_previsao,
					   cd_acomp, 
					   dt_previsao, 
					   mes,
					   ano,
					   descricao, 
					   obs,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 ) 
				VALUES
					 (
					   ".$cd_previsao.",
					   ".intval($args['cd_acomp']).", 
					   TO_DATE('01/".$args['mes']."/".$args['ano']."','DD/MM/YYYY'), 
					   '".$args['mes_extenso']."', 
					   '".$args['ano']."', 
					   ".(trim($args['descricao']) != '' ? "'".trim($args['descricao'])."'" : "DEFAULT").", 
					   ".(trim($args['obs']) != '' ? "'".trim($args['obs'])."'" : "DEFAULT").",
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$cd_previsao = intval($args['cd_previsao']);
			
			$qr_sql = "
				UPDATE projetos.previsoes_projetos 
				   SET dt_previsao          = TO_DATE('01/".$args['mes']."/".$args['ano']."','DD/MM/YYYY'), 
				       mes                  = '".$args['mes_extenso']."', 
					   ano                  = '".$args['ano']."', 					
					   descricao            = ".(trim($args['descricao']) != '' ? "'".trim($args['descricao'])."'" : "DEFAULT").", 
					   obs                  = ".(trim($args['obs']) != '' ? "'".trim($args['obs'])."'" : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_previsao = ".intval($args['cd_previsao']).";";
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_previsao;
	}
}
?>