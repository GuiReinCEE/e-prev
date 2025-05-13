<?php
class Pre_venda_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function abrir($id)
	{
		$entity = array(
			'cd_pre_venda'=>'', 'cd_empresa'=>'', 'cd_registro_empregado'=>'', 'seq_dependencia'=>'',
	       	'nome'=>'', 'cpf'=>'', 'dt_inclusao'=>'', 'cd_usuario_inclusao'=>'', 'dt_exclusao'=>'', 'cd_usuario_exclusao'=>''
		);

		$id = (int)$id;

		$dados[0] = $id;
		$query = $this->db->query( "
			SELECT
				pv.cd_pre_venda
				, pv.cd_empresa
				, pv.cd_registro_empregado
				, pv.seq_dependencia
				, pv.nome
				, to_char(pv.dt_inclusao, 'DD/MM/YYYY') as dt_inclusao
				, pv.cd_usuario_inclusao
				, to_char(pv.dt_exclusao, 'DD/MM/YYYY') as dt_exclusao
				, pv.cd_usuario_exclusao
				, pv.cpf
				, (SELECT COUNT(pvc.*)
				     FROM projetos.pre_venda_contato pvc
					WHERE pvc.dt_envio_inscricao IS NOT NULL
					  AND pvc.dt_exclusao IS NULL
					  AND pvc.cd_pre_venda = pv.cd_pre_venda) AS qt_protocolo
		  	FROM projetos.pre_venda pv
		  	WHERE pv.cd_pre_venda=? AND pv.dt_exclusao IS NULL;
		", $dados );

		if($query)
		{
			$row = $query->row_array();
			
			if($row)
			{
				$entity=$row;
			}
		}

		return $entity;
	}

	function listar(&$result, $args=Array())
	{
		$qr_sql = "
					SELECT a.cd_pre_venda,
						   a.cd_empresa,
						   a.cd_registro_empregado,
						   a.seq_dependencia,
						   a.nome,
						   a.cpf,
						   TO_CHAR(b.dt_opcao_plano, 'DD/MM/YYYY') AS dt_opcao_plano,
						   TO_CHAR(b.dt_ingresso_plano, 'DD/MM/YYYY') AS dt_ingresso_plano,
						   TO_CHAR(c.dt_envio, 'DD/MM/YYYY') AS dt_envio,
						   TO_CHAR(c.dt_retorno, 'DD/MM/YYYY') AS dt_retorno,
						   c.fl_apto,
						   (SELECT TO_CHAR(MIN(dt_pre_venda_agenda), 'DD/MM/YYYY') AS dt_pre_venda_agenda 
							  FROM projetos.pre_venda_agenda 
							 WHERE projetos.pre_venda_agenda.cd_pre_venda = a.cd_pre_venda 
							   AND DATE_TRUNC('DAY', dt_pre_venda_agenda) >= CURRENT_DATE 
							   AND dt_pre_venda_agenda_enviado            IS NULL) AS dt_proximo_agendamento,
						   (SELECT TO_CHAR(MIN(b1.dt_pre_venda_contato),'DD/MM/YYYY')
										   FROM projetos.pre_venda_contato b1 
										  WHERE b1.cd_pre_venda = a.cd_pre_venda
											AND b1.dt_exclusao  IS NULL) AS dt_primeiro_contato							   
					  FROM projetos.pre_venda a
					  LEFT JOIN public.titulares_planos b 
						ON b.cd_empresa            = a.cd_empresa 
					   AND b.cd_registro_empregado = a.cd_registro_empregado 
					   AND b.seq_dependencia       = a.seq_dependencia 
					   AND b.dt_ingresso_plano     = (SELECT MAX(b1.dt_ingresso_plano)
					                                    FROM public.titulares_planos b1
													   WHERE b1.cd_empresa            = b.cd_empresa 
					                                     AND b1.cd_registro_empregado = b.cd_registro_empregado 
					                                     AND b1.seq_dependencia       = b.seq_dependencia) 
					  LEFT JOIN projetos.exame_ingresso c 
						ON c.cd_empresa            = a.cd_empresa 
					   AND c.cd_registro_empregado = a.cd_registro_empregado 
					   AND c.seq_dependencia       = a.seq_dependencia 
					   AND c.dt_envio              IS NOT NULL
					   AND c.dt_envio              = (SELECT MAX(c1.dt_envio)
														FROM projetos.exame_ingresso c1
													   WHERE c1.cd_empresa            = c.cd_empresa 
														 AND c1.cd_registro_empregado = c.cd_registro_empregado 
														 AND c1.seq_dependencia       = c.seq_dependencia 
														 AND c1.dt_envio              IS NOT NULL)
					 WHERE 1 = 1
					   AND a.dt_exclusao IS NULL
					   {CD_EMPRESA}
					   {CD_REGISTRO_EMPREGADO}
					   {SEQ_DEPENDENCIA}
					   {NOME}
					   {DT_OPCAO}
					   {DT_INGRESSO}
					   {CONTATO}
		              ";
		$qr_sql = str_replace("{CD_EMPRESA}",(trim($args["cd_empresa"]) != "" ? " AND a.cd_empresa = ".intval($args['cd_empresa']) : ""),$qr_sql);			   				  
		$qr_sql = str_replace("{CD_REGISTRO_EMPREGADO}",(intval($args["cd_registro_empregado"]) > 0 ? " AND a.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : ""),$qr_sql);			   				  
		$qr_sql = str_replace("{SEQ_DEPENDENCIA}",(trim($args["seq_dependencia"]) != "" ? " AND a.seq_dependencia = ".intval($args['seq_dependencia']) : ""),$qr_sql);			   				  
		$qr_sql = str_replace("{NOME}",(trim($args["nome"]) != "" ? " AND TRIM(UPPER(a.nome)) LIKE UPPER('%".trim($args['nome'])."%')" : ""),$qr_sql);
		$qr_sql = str_replace("{DT_OPCAO}",(((trim($args["dt_opcao_ini"]) != "") and (trim($args["dt_opcao_fim"]) != "")) ? " AND CAST(b.dt_opcao_plano AS DATE) BETWEEN TO_DATE('".$args["dt_opcao_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_opcao_fim"]."','DD/MM/YYYY')" : ""),$qr_sql);			   				  
		$qr_sql = str_replace("{DT_INGRESSO}",(((trim($args["dt_ingresso_ini"]) != "") and (trim($args["dt_ingresso_fim"]) != "")) ? " AND CAST(b.dt_ingresso_plano AS DATE) BETWEEN TO_DATE('".$args["dt_ingresso_fim"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_opcao_fim"]."','DD/MM/YYYY')" : ""),$qr_sql);			   				  
		
		if(
			((trim($args['dt_envio_ini']) != '') and (trim($args['dt_envio_fim']) != '')) or
			((trim($args['dt_contato_ini']) != '') and (trim($args['dt_contato_fim']) != '')) or
			(intval($args["cd_pre_venda_local"]) > 0) or
			(trim($args["fl_inscricao"]) != "") or
			(intval($args["cd_usuario_contato"]) > 0)
		  )
		{
			$qr_filtro = " 
							AND 1 = (SELECT 1
									   FROM projetos.pre_venda_contato pvc
									   LEFT JOIN projetos.pre_venda_motivo pvm 
										 ON pvm.cd_pre_venda_motivo = pvc.cd_pre_venda_motivo
									  WHERE pvc.cd_pre_venda = a.cd_pre_venda
									    AND pvc.dt_exclusao IS NULL
										".(((trim($args['dt_envio_ini']) != '') and (trim($args['dt_envio_fim']) != '')) ? "AND CAST(pvc.dt_envio_inscricao AS DATE) BETWEEN TO_DATE('".$args['dt_envio_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."','DD/MM/YYYY')" : "")."
									    ".(((trim($args['dt_contato_ini']) != '') and (trim($args['dt_contato_fim']) != '')) ? "AND CAST(pvc.dt_pre_venda_contato AS DATE) BETWEEN TO_DATE('".$args['dt_contato_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_contato_fim']."','DD/MM/YYYY')": "")."					   
										".((intval($args["cd_pre_venda_local"]) > 0) ?  "AND pvc.cd_pre_venda_local = ".intval($args["cd_pre_venda_local"]) : "")."
										".((trim($args["fl_inscricao"]) != "") ?  "AND pvc.dt_envio_inscricao IS ".(trim($args["fl_inscricao"]) == "S" ? "NOT" : "")." NULL" : "")."
										".((intval($args["cd_usuario_contato"]) > 0) ?  "AND pvc.cd_usuario_inclusao = ".intval($args["cd_usuario_contato"]) : "")."
									 LIMIT 1)
						 ";
			$qr_sql = str_replace("{CONTATO}",$qr_filtro,$qr_sql);
		}
		else
		{
			$qr_sql = str_replace("{CONTATO}","",$qr_sql);
		}
												 
															 
		#echo "<PRE style='text-align: left;'>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
		$result = $result->result_array();

		
		for($idx = 0; $idx < sizeof($result); $idx++)
		{
			$qr_sql = "				
						SELECT pvc.cd_pre_venda_contato, 
							   TO_CHAR(pvc.dt_pre_venda_contato, 'DD/MM/YYYY') AS dt_pre_venda_contato, 
							   TO_CHAR(pvc.dt_envio_inscricao, 'DD/MM/YYYY') AS dt_envio_inscricao,
							   pvc.cd_pre_venda_motivo,
							   pvc.observacao,
							   pvm.ds_pre_venda_motivo,
							   uc.guerra AS ds_usuario_inclusao
						  FROM projetos.pre_venda_contato pvc
						  JOIN projetos.usuarios_controledi uc
						    ON uc.codigo = pvc.cd_usuario_inclusao
						  LEFT JOIN projetos.pre_venda_motivo pvm 
							ON pvm.cd_pre_venda_motivo = pvc.cd_pre_venda_motivo
						 WHERE pvc.cd_pre_venda = ".intval($result[$idx]['cd_pre_venda'])." 
						   AND pvc.dt_exclusao  IS NULL
							".(((trim($args['dt_envio_ini']) != '') and (trim($args['dt_envio_fim']) != '')) ? "AND CAST(pvc.dt_envio_inscricao AS DATE) BETWEEN TO_DATE('".$args['dt_envio_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."','DD/MM/YYYY')" : "")."
							".(((trim($args['dt_contato_ini']) != '') and (trim($args['dt_contato_fim']) != '')) ? "AND CAST(pvc.dt_pre_venda_contato AS DATE) BETWEEN TO_DATE('".$args['dt_contato_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_contato_fim']."','DD/MM/YYYY')": "")."					   
							".((intval($args["cd_pre_venda_local"]) > 0) ?  "AND pvc.cd_pre_venda_local = ".intval($args["cd_pre_venda_local"]) : "")."
							".((trim($args["fl_inscricao"]) != "") ?  "AND pvc.dt_envio_inscricao IS ".(trim($args["fl_inscricao"]) == "S" ? "NOT" : "")." NULL" : "")."
							".((intval($args["cd_usuario_contato"]) > 0) ?  "AND pvc.cd_usuario_inclusao = ".intval($args["cd_usuario_contato"]) : "")."
						 ORDER BY pvc.dt_pre_venda_contato ASC
					  ";
			
			$query = $this->db->query($qr_sql);//, array()
			
			if($query)
			{
				$contatos = $query->result_array();
			}
			else
			{
				$contatos = array();
			}
			
			$result[$idx]['contatos'] = $contatos;
		}
		
	}
	
	function salvar(&$result, $dados=array())
	{
		if(intval($dados['cd_pre_venda']) > 0)
		{
			$qr_sql = " 
				UPDATE projetos.pre_venda
				   SET cd_empresa            = ".(trim($dados['cd_empresa']) != "" ? intval($dados['cd_empresa']) : "DEFAULT").",
					   cd_registro_empregado = ".(intval($dados['cd_registro_empregado']) > 0 ? intval($dados['cd_registro_empregado']) : "DEFAULT").",
					   seq_dependencia       = ".(trim($dados['seq_dependencia']) != "" ? intval($dados['seq_dependencia']) : "DEFAULT").",
					   nome                  = ".(trim($dados['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$dados['nome']."'))").",
					   cpf                   = ".(trim($dados['cpf']) == "" ? "DEFAULT" : "'".$dados['cpf']."'")."
				 WHERE cd_pre_venda = ".intval($dados['cd_pre_venda']).";";		
			$this->db->query($qr_sql);
			$retorno = intval($dados['cd_pre_venda']);	
		}
		else
		{
			$new_id = intval($this->db->get_new_id("projetos.pre_venda", "cd_pre_venda"));
			$qr_sql = "
				INSERT INTO projetos.pre_venda
					 (
					   cd_pre_venda,
					   cd_empresa,
					   cd_registro_empregado,
					   seq_dependencia,
					   nome,
					   cpf,
					   cd_usuario_inclusao
					 )
				VALUES
					 (
					   ".$new_id.",
					   ".(trim($dados['cd_empresa']) != "" ? intval($dados['cd_empresa']) : "DEFAULT").",
					   ".(intval($dados['cd_registro_empregado']) > 0 ? intval($dados['cd_registro_empregado']) : "DEFAULT").",
					   ".(trim($dados['seq_dependencia']) != "" ? intval($dados['seq_dependencia']) : "DEFAULT").",									 
					   ".(trim($dados['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$dados['nome']."'))").",
					   ".(trim($dados['cpf']) == "" ? "DEFAULT" : "'".$dados['cpf']."'").",
					   ".(intval($dados['cd_usuario']) == 0 ? "DEFAULT" : $dados['cd_usuario'])."
					 );	";
			$this->db->query($qr_sql);	
			$retorno = $new_id;				
		}
		
		return $retorno;
	}
	
	function local(&$result, $args=array())
	{
		$qr_sql = "
			SELECT pvl.cd_pre_venda_local AS value, 
				   pvl.ds_pre_venda_local AS text
			  FROM projetos.pre_venda_local pvl
			 WHERE pvl.dt_exclusao IS NULL
			 ORDER BY pvl.ds_pre_venda_local";
		$result = $this->db->query($qr_sql);
	}	

	function usuario_contato(&$result, $args=array())
	{
		$qr_sql = "
			SELECT DISTINCT uc.codigo AS value, 
				   uc.nome AS text
			  FROM projetos.pre_venda_contato pvc
			  JOIN projetos.usuarios_controledi uc
				ON uc.codigo = pvc.cd_usuario_inclusao
			 WHERE pvc.dt_exclusao IS NULL		
			 ORDER BY uc.nome";
		$result = $this->db->query($qr_sql);
	}		
	
	function evento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_evento AS value,
				   '['||TO_CHAR(dt_inicio,'DD/MM/YYYY')||'] - ' || nome AS text
			  FROM projetos.eventos_institucionais
			 WHERE dt_exclusao IS NULL
			 ORDER BY dt_inicio DESC";
			
		$result = $this->db->query($qr_sql);
	}
	
	function protocoloInternoListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT pv.cd_pre_venda,
					       pvc.cd_pre_venda_contato, 
					       pv.nome,
						   pv.cd_empresa,
						   pv.cd_registro_empregado,
						   pv.seq_dependencia,
						   TO_CHAR(pvc.dt_pre_venda_contato, 'DD/MM/YYYY HH24:MI' ) AS dt_pre_venda_contato, 
						   TO_CHAR(pvc.dt_envio_inscricao, 'DD/MM/YYYY') AS dt_envio_inscricao,
						   pvc.cd_pre_venda_motivo,
						   pvc.observacao,
						   pvm.ds_pre_venda_motivo,
						   pvl.ds_pre_venda_local,
						   uc.guerra AS ds_usuario_contato,
						   '['||TO_CHAR(ei.dt_inicio,'DD/MM/YYYY')||'] - ' || ei.nome AS ds_evento,
						   pvc.cd_documento_recebido,
						   TO_CHAR(dr.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_protocolo_envio,
						   funcoes.nr_documento_recebido(dr.nr_ano, dr.nr_contador) AS nr_documento_recebido
					  FROM projetos.pre_venda_contato pvc
					  JOIN projetos.pre_venda pv
					    ON pv.cd_pre_venda = pvc.cd_pre_venda
					  JOIN projetos.usuarios_controledi uc
						ON uc.codigo = pvc.cd_usuario_inclusao						  
					  LEFT JOIN projetos.pre_venda_motivo pvm 
						ON pvm.cd_pre_venda_motivo = pvc.cd_pre_venda_motivo
					  LEFT JOIN projetos.pre_venda_local pvl
						ON pvl.cd_pre_venda_local = pvc.cd_pre_venda_local
					  LEFT JOIN projetos.eventos_institucionais ei
						ON ei.cd_evento = pvc.cd_evento_institucional
					  LEFT JOIN projetos.documento_recebido dr
						ON dr.cd_documento_recebido = pvc.cd_documento_recebido						
					 WHERE pvc.dt_exclusao IS NULL
					   AND pvc.dt_envio_inscricao IS NOT NULL
					   ".(trim($args['cd_pre_venda_contato']) != "" ? "AND pvc.cd_pre_venda_contato IN (".trim($args["cd_pre_venda_contato"]).")" : "")."
					   ".(trim($args['cd_empresa']) != "" ? "AND pv.cd_empresa = ".intval($args["cd_empresa"]) : "")."
					   ".(trim($args['cd_registro_empregado']) != "" ? "AND pv.cd_registro_empregado = ".intval($args["cd_registro_empregado"]) : "")."
					   ".(trim($args['seq_dependencia']) != "" ? "AND pv.seq_dependencia = ".intval($args["seq_dependencia"]) : "")."
					   ".(trim($args['nome']) != "" ? "AND TRIM(UPPER(pv.nome)) LIKE UPPER('%".str_replace(" ","%",trim($args['nome']))."%')" : "")."
					   ".(((trim($args['dt_contato_ini']) != "") and  (trim($args['dt_contato_fim']) != "")) ? " AND DATE_TRUNC('day', pvc.dt_pre_venda_contato) BETWEEN TO_DATE('".$args['dt_contato_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_contato_fim']."', 'DD/MM/YYYY')" : "")."
					   ".(((trim($args['dt_protocolo_envio_ini']) != "") and  (trim($args['dt_protocolo_envio_fim']) != "")) ? " AND DATE_TRUNC('day', dr.dt_envio) BETWEEN TO_DATE('".$args['dt_protocolo_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_protocolo_envio_fim']."', 'DD/MM/YYYY')" : "")."
					   ".(trim($args['fl_protocolo']) == "S" ? "AND pvc.cd_documento_recebido IS NOT NULL" : "")."
					   ".(trim($args['fl_protocolo']) == "N" ? "AND pvc.cd_documento_recebido IS NULL" : "")."	
					   ".(trim($args['fl_protocolo_enviado']) == "S" ? "AND dr.dt_envio IS NOT NULL" : "")."
					   ".(trim($args['fl_protocolo_enviado']) == "N" ? "AND dr.dt_envio IS NULL" : "")."						   
		          ";
		#echo "<PRE style='text-align:left;'>".$qr_sql."</PRE>"; #exit;
		$result = $this->db->query($qr_sql);				  
	}
	
	function protocoloInternoSetProtocolo(&$result, $args=array())
	{
		if(trim($args['cd_pre_venda_contato']) != "")
		{
			$qr_sql = " 
						UPDATE projetos.pre_venda_contato
						   SET cd_documento_recebido = ".(trim($args['cd_documento_recebido']) != "" ? intval($args['cd_documento_recebido']) : "DEFAULT")."
						 WHERE cd_pre_venda_contato IN (".trim($args['cd_pre_venda_contato']).");
				      ";		
			$this->db->query($qr_sql);
		}
	}	
	
	function relatorioListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT mesano.mes, 
						   mesano.ano,
						   COALESCE((SELECT COUNT(DISTINCT DATE_TRUNC('day', pvc.dt_pre_venda_contato))
									   FROM projetos.pre_venda_contato pvc 
									   JOIN projetos.pre_venda_local pvl 
										 ON pvl.cd_pre_venda_local = pvc.cd_pre_venda_local
									   JOIN projetos.pre_venda pv 
										 ON pv.cd_pre_venda = pvc.cd_pre_venda
									  WHERE pvc.dt_exclusao IS NULL
										AND date_part('MONTH', pvc.dt_pre_venda_contato) = mesano.mes
										AND date_part('YEAR', pvc.dt_pre_venda_contato)  = mesano.ano
									  ".(trim($args['cd_empresa']) != "" ? "AND pv.cd_empresa = ".intval($args['cd_empresa']) : "")."
									  ".(trim($args['tp_empresa']) != "" ? "AND pv.cd_empresa IN (SELECT ps.cd_empresa FROM public.patrocinadoras ps WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."
									 GROUP BY date_part('MONTH', pvc.dt_pre_venda_contato), 
											  date_part('YEAR', pvc.dt_pre_venda_contato)
									 ORDER BY date_part('MONTH', pvc.dt_pre_venda_contato), 
											  date_part('YEAR', pvc.dt_pre_venda_contato)),0) AS quantos_locais,
						   COALESCE((SELECT COUNT(funcoes.cripto_re(pv.cd_empresa,pv.cd_registro_empregado,pv.seq_dependencia))
									   FROM projetos.pre_venda pv 
									   JOIN projetos.pre_venda_contato pvc 
										 ON pvc.cd_pre_venda = pv.cd_pre_venda
									  WHERE pv.dt_exclusao IS NULL
										AND pvc.dt_exclusao IS NULL
										AND pvc.dt_pre_venda_contato = (SELECT MIN(b1.dt_pre_venda_contato)
																		  FROM projetos.pre_venda_contato b1 
																		 WHERE b1.cd_pre_venda = pv.cd_pre_venda
																		   AND b1.dt_exclusao  IS NULL)
										AND date_part('MONTH', pvc.dt_pre_venda_contato) = mesano.mes
										AND date_part('YEAR', pvc.dt_pre_venda_contato)  = mesano.ano										
										".(trim($args['cd_empresa']) != "" ? "AND pv.cd_empresa = ".intval($args['cd_empresa']) : "")."
										".(trim($args['tp_empresa']) != "" ? "AND pv.cd_empresa IN (SELECT ps.cd_empresa 
																									  FROM public.patrocinadoras ps 
																									 WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."				 
									 GROUP BY date_part('MONTH', pvc.dt_pre_venda_contato), 
											  date_part('YEAR', pvc.dt_pre_venda_contato)
									 ORDER BY date_part('MONTH', pvc.dt_pre_venda_contato), 
											  date_part('YEAR', pvc.dt_pre_venda_contato)),0) AS quantos_participantes,
											   
						   COALESCE((SELECT COUNT(*)
									   FROM projetos.pre_venda_contato pvc 
									   JOIN projetos.pre_venda pv 
										 ON pv.cd_pre_venda = pvc.cd_pre_venda				  
									  WHERE pvc.dt_exclusao IS NULL
										AND date_part('MONTH', pvc.dt_pre_venda_contato) = mesano.mes
										AND date_part('YEAR', pvc.dt_pre_venda_contato)  = mesano.ano
										".(trim($args['cd_empresa']) != "" ? "AND pv.cd_empresa = ".intval($args['cd_empresa']) : "")."
										".(trim($args['tp_empresa']) != "" ? "AND pv.cd_empresa IN (SELECT ps.cd_empresa 
																									  FROM public.patrocinadoras ps 
																									 WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."				 
									  GROUP BY date_part('MONTH', pvc.dt_pre_venda_contato), 
											   date_part('YEAR', pvc.dt_pre_venda_contato)
									  ORDER BY date_part('MONTH', pvc.dt_pre_venda_contato), 
											   date_part('YEAR', pvc.dt_pre_venda_contato)),0) AS quantos_contatos,
						   COALESCE((SELECT COUNT(*)
									   FROM projetos.pre_venda_contato pvc
									   JOIN projetos.pre_venda pv 
										 ON pv.cd_pre_venda = pvc.cd_pre_venda					  
									  WHERE pvc.dt_envio_inscricao IS NOT NULL
										AND pvc.dt_exclusao        IS NULL
										AND date_part('MONTH', pvc.dt_pre_venda_contato) = mesano.mes
										AND date_part('YEAR', pvc.dt_pre_venda_contato)  = mesano.ano
										".(trim($args['cd_empresa']) != "" ? "AND pv.cd_empresa = ".intval($args['cd_empresa']) : "")."
										".(trim($args['tp_empresa']) != "" ? "AND pv.cd_empresa IN (SELECT ps.cd_empresa 
																									  FROM public.patrocinadoras ps 
																									 WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."
									  GROUP BY date_part('MONTH', pvc.dt_pre_venda_contato), 
											   date_part('YEAR', pvc.dt_pre_venda_contato)
									  ORDER BY date_part('MONTH', pvc.dt_pre_venda_contato), 
											   date_part('YEAR', pvc.dt_pre_venda_contato)),0) AS quantos_contatos_enviados,
						   COALESCE((SELECT COUNT(DISTINCT a.cd_registro_empregado)
									   FROM public.titulares_planos a 
									   JOIN public.titulares t
										 ON t.cd_empresa            = a.cd_empresa 
										AND t.cd_registro_empregado = a.cd_registro_empregado 
										AND t.seq_dependencia       = a.seq_dependencia 				    
									   JOIN projetos.pre_venda b 
										 ON b.cd_empresa            = a.cd_empresa 
										AND b.cd_registro_empregado = a.cd_registro_empregado 
										AND b.seq_dependencia       = a.seq_dependencia 
									   JOIN projetos.pre_venda_contato c 
										 ON b.cd_pre_venda          = c.cd_pre_venda
									  WHERE a.dt_ingresso_plano    IS NOT NULL
										AND b.dt_exclusao          IS NULL
										AND c.dt_exclusao          IS NULL
										AND date_part('MONTH', a.dt_ingresso_plano) = mesano.mes
										AND date_part('YEAR', a.dt_ingresso_plano)  = mesano.ano										
										AND DATE_TRUNC('month',a.dt_ingresso_plano) <> DATE_TRUNC('month', COALESCE(t.dt_desligamento_eletro, CURRENT_TIMESTAMP + '1 year'))
										".(trim($args['cd_empresa']) != "" ? "AND a.cd_empresa = ".intval($args['cd_empresa']) : "")."
										".(trim($args['tp_empresa']) != "" ? "AND a.cd_empresa IN (SELECT ps.cd_empresa FROM public.patrocinadoras ps WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."
									  GROUP BY date_part('MONTH', a.dt_ingresso_plano),
											   date_part('YEAR', a.dt_ingresso_plano)
									  ORDER BY date_part('MONTH', a.dt_ingresso_plano),
											   date_part('YEAR', a.dt_ingresso_plano)),0) AS quantos_contatos_ingresso,
						   COALESCE((SELECT COUNT(DISTINCT a.cd_registro_empregado)
									   FROM public.titulares t
									   JOIN public.titulares_planos a 
										 ON a.cd_empresa            = t.cd_empresa 
										AND a.cd_registro_empregado = t.cd_registro_empregado 
										AND a.seq_dependencia       = t.seq_dependencia 					  
									  WHERE a.dt_ingresso_plano    IS NOT NULL
										AND date_part('MONTH', a.dt_ingresso_plano) = mesano.mes
										AND date_part('YEAR', a.dt_ingresso_plano)  = mesano.ano
										AND DATE_TRUNC('month',a.dt_ingresso_plano) <> DATE_TRUNC('month', COALESCE(t.dt_desligamento_eletro, CURRENT_TIMESTAMP + '1 year'))
										".(trim($args['cd_empresa']) != "" ? "AND t.cd_empresa = ".intval($args['cd_empresa']) : "")."
										".(trim($args['tp_empresa']) != "" ? "AND t.cd_empresa IN (SELECT ps.cd_empresa FROM public.patrocinadoras ps WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."
									  GROUP BY date_part('MONTH', a.dt_ingresso_plano),
											   date_part('YEAR', a.dt_ingresso_plano)
									  ORDER BY date_part('MONTH', a.dt_ingresso_plano),
											   date_part('YEAR', a.dt_ingresso_plano)),0) AS quantos_ingresso_fceee,
											   
						   COALESCE((SELECT COUNT(DISTINCT t.cd_registro_empregado)
									   FROM public.titulares t
									   JOIN public.titulares_planos tp
										 ON tp.cd_empresa            = t.cd_empresa
										AND tp.cd_registro_empregado = t.cd_registro_empregado
										AND tp.seq_dependencia       = t.seq_dependencia
									  WHERE tp.dt_ingresso_plano  IS NOT NULL
										AND COALESCE(date_part('MONTH',t.dt_digita_ingresso), date_part('MONTH',tp.dt_ingresso_plano)) = mesano.mes
										AND COALESCE(date_part('YEAR',t.dt_digita_ingresso), date_part('YEAR',tp.dt_ingresso_plano)) = mesano.mes									
										AND DATE_TRUNC('month',tp.dt_ingresso_plano) <> DATE_TRUNC('month', COALESCE(t.dt_desligamento_eletro, CURRENT_TIMESTAMP + '1 year'))
										".(trim($args['cd_empresa']) != "" ? "AND t.cd_empresa = ".intval($args['cd_empresa']) : "")."
										".(trim($args['tp_empresa']) != "" ? "AND t.cd_empresa IN (SELECT ps.cd_empresa FROM public.patrocinadoras ps WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."
									  GROUP BY COALESCE(date_part('MONTH',t.dt_digita_ingresso), date_part('MONTH',tp.dt_ingresso_plano)), 
									           COALESCE(date_part('YEAR',t.dt_digita_ingresso), date_part('YEAR',tp.dt_ingresso_plano))
									  ORDER BY COALESCE(date_part('MONTH',t.dt_digita_ingresso), date_part('MONTH',tp.dt_ingresso_plano)), 
									           COALESCE(date_part('YEAR',t.dt_digita_ingresso), date_part('YEAR',tp.dt_ingresso_plano))),0) AS quantos_digita_ingresso_fceee
					 FROM (SELECT mes, 
								  ano
							 FROM generate_series(".intval($args['nr_ano']).", ".intval($args['nr_ano']).") AS ano, 
								  generate_series(1,12) AS mes
							WHERE 1 = 1
							".(intval($args['nr_mes']) > 0 ? "AND mes = ".intval($args['nr_mes']) : "")." 
							ORDER BY ano,
									 mes) AS mesano
					 ORDER BY mesano.ano, mesano.mes
			     ";
		#echo "<PRE style='text-align:left;'>".$qr_sql."</PRE>";
		$result = $this->db->query($qr_sql);
	}		
	
	
	function relatorioListarOLD(&$result, $args=array())
	{
		$qr_sql = "
			SELECT mesano.mes, 
			       mesano.ano,
				   COALESCE(locais.quantos,0) AS quantos_locais,
				   COALESCE(participantes.quantos,0) AS quantos_participantes,
				   COALESCE(contatos.quantos,0) AS quantos_contatos,
				   COALESCE(contatos_enviados.quantos,0) AS quantos_contatos_enviados,
				   COALESCE(contatos_ingresso.quantos,0) AS quantos_contatos_ingresso,
				   COALESCE(ingresso_fceee.quantos,0) AS quantos_ingresso_fceee,
				   COALESCE(digita_ingresso_fceee.quantos,0) AS quantos_digita_ingresso_fceee

			FROM 
			(
				SELECT TRIM(TO_CHAR(mes, '00')) AS mes, 
					   TRIM(TO_CHAR(ano, '0000')) AS ano
				  FROM generate_series(".intval($args['nr_ano']).", ".intval($args['nr_ano']).") AS ano, 
					   generate_series(1,12) AS mes
				 WHERE 1 = 1
				   ".(intval($args['nr_mes']) > 0 ? "AND mes = ".intval($args['nr_mes']) : "")." 
				 ORDER BY ano,
						  mes				   
			) as mesano

			LEFT JOIN 

			(
				SELECT TO_CHAR( DATE_TRUNC('day', pvc.dt_pre_venda_contato), 'MM' ) AS mes,
					   TO_CHAR( DATE_TRUNC('day', pvc.dt_pre_venda_contato), 'YYYY') AS ano,
					   COUNT(DISTINCT DATE_TRUNC('day', pvc.dt_pre_venda_contato)) AS quantos
				  FROM projetos.pre_venda_contato pvc 
				  JOIN projetos.pre_venda_local pvl 
					ON pvl.cd_pre_venda_local = pvc.cd_pre_venda_local
				  JOIN projetos.pre_venda pv 
					ON pv.cd_pre_venda = pvc.cd_pre_venda
				 WHERE pvc.dt_exclusao IS NULL
				 ".(trim($args['cd_empresa']) != "" ? "AND pv.cd_empresa = ".intval($args['cd_empresa']) : "")."
				 ".(trim($args['tp_empresa']) != "" ? "AND pv.cd_empresa IN (SELECT ps.cd_empresa FROM public.patrocinadoras ps WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."
				 GROUP BY mes, 
						  ano
				 ORDER BY ano,
						  mes					  
			) as locais
			ON mesano.mes=locais.mes AND mesano.ano=locais.ano

			LEFT JOIN 
			(
				SELECT TRIM(TO_CHAR( pvc.dt_pre_venda_contato, 'MM' )) AS mes, 
					   TRIM(TO_CHAR( pvc.dt_pre_venda_contato, 'YYYY' )) AS ano, 
					   COUNT(pv.cd_empresa::TEXT || pv.cd_registro_empregado::TEXT || pv.seq_dependencia::TEXT ) AS quantos
				  FROM projetos.pre_venda pv 
				  JOIN projetos.pre_venda_contato pvc 
					ON pvc.cd_pre_venda = pv.cd_pre_venda
				 WHERE pv.dt_exclusao IS NULL
				   AND pvc.dt_exclusao IS NULL
				   AND pvc.dt_pre_venda_contato = (SELECT MIN(b1.dt_pre_venda_contato)
												     FROM projetos.pre_venda_contato b1 
												    WHERE b1.cd_pre_venda = pv.cd_pre_venda
													  AND b1.dt_exclusao  IS NULL)
				 ".(trim($args['cd_empresa']) != "" ? "AND pv.cd_empresa = ".intval($args['cd_empresa']) : "")."
                 ".(trim($args['tp_empresa']) != "" ? "AND pv.cd_empresa IN (SELECT ps.cd_empresa FROM public.patrocinadoras ps WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."				 
				 GROUP BY mes, 
						  ano
				 ORDER BY ano,
						  mes					  
			) as participantes
			ON mesano.mes=participantes.mes AND mesano.ano=participantes.ano

			LEFT JOIN 
			(
				SELECT TRIM(TO_CHAR( pvc.dt_pre_venda_contato, 'MM' )) AS mes, 
					   TRIM(TO_CHAR( pvc.dt_pre_venda_contato, 'YYYY' )) AS ano, 
					   COUNT(*) AS quantos
				  FROM projetos.pre_venda_contato pvc 
				  JOIN projetos.pre_venda pv 
					ON pv.cd_pre_venda = pvc.cd_pre_venda				  
				 WHERE pvc.dt_exclusao IS NULL
				 ".(trim($args['cd_empresa']) != "" ? "AND pv.cd_empresa = ".intval($args['cd_empresa']) : "")."
                 ".(trim($args['tp_empresa']) != "" ? "AND pv.cd_empresa IN (SELECT ps.cd_empresa FROM public.patrocinadoras ps WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."				 
				 GROUP BY mes, 
						  ano 
				 ORDER BY ano,
						  mes					  
			) as contatos
			ON mesano.mes=contatos.mes AND mesano.ano=contatos.ano

			LEFT JOIN
			(
				SELECT TRIM(TO_CHAR( pvc.dt_pre_venda_contato, 'MM' )) AS mes, 
					   TRIM(TO_CHAR( pvc.dt_pre_venda_contato, 'YYYY')) AS ano, 
					   COUNT(*) AS quantos
				  FROM projetos.pre_venda_contato pvc
				  JOIN projetos.pre_venda pv 
					ON pv.cd_pre_venda = pvc.cd_pre_venda					  
				 WHERE pvc.dt_envio_inscricao IS NOT NULL
				   AND pvc.dt_exclusao        IS NULL
				   ".(trim($args['cd_empresa']) != "" ? "AND pv.cd_empresa = ".intval($args['cd_empresa']) : "")."
				   ".(trim($args['tp_empresa']) != "" ? "AND pv.cd_empresa IN (SELECT ps.cd_empresa FROM public.patrocinadoras ps WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."
				 GROUP BY mes, 
						  ano 
				 ORDER BY ano,
						  mes					  
			) as contatos_enviados
			ON mesano.mes=contatos_enviados.mes AND mesano.ano=contatos_enviados.ano

			LEFT JOIN 
			(
				SELECT TRIM(TO_CHAR( a.dt_ingresso_plano, 'MM' )) AS mes, 
					   TRIM(TO_CHAR( a.dt_ingresso_plano, 'YYYY' )) AS ano, 
					   COUNT(DISTINCT a.cd_registro_empregado) AS quantos
				  FROM public.titulares_planos a 
				  JOIN public.titulares t
					ON t.cd_empresa            = a.cd_empresa 
				   AND t.cd_registro_empregado = a.cd_registro_empregado 
				   AND t.seq_dependencia       = a.seq_dependencia 				    
				  JOIN projetos.pre_venda b 
					ON b.cd_empresa            = a.cd_empresa 
				   AND b.cd_registro_empregado = a.cd_registro_empregado 
				   AND b.seq_dependencia       = a.seq_dependencia 
				  JOIN projetos.pre_venda_contato c 
					ON b.cd_pre_venda          = c.cd_pre_venda
				 WHERE a.dt_ingresso_plano    IS NOT NULL
				   AND b.dt_exclusao          IS NULL
				   AND c.dt_exclusao          IS NULL
				   AND DATE_TRUNC('month',a.dt_ingresso_plano) <> DATE_TRUNC('month', COALESCE(t.dt_desligamento_eletro, CURRENT_TIMESTAMP + '1 year'))
				   ".(trim($args['cd_empresa']) != "" ? "AND a.cd_empresa = ".intval($args['cd_empresa']) : "")."
				   ".(trim($args['tp_empresa']) != "" ? "AND a.cd_empresa IN (SELECT ps.cd_empresa FROM public.patrocinadoras ps WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."
				 GROUP BY mes,
						  ano
				 ORDER BY ano,
						  mes					  
			) as contatos_ingresso
			ON mesano.mes=contatos_ingresso.mes AND mesano.ano=contatos_ingresso.ano

			LEFT JOIN 
			(
				SELECT TRIM(TO_CHAR( a.dt_ingresso_plano, 'MM' )) AS mes, 
					   TRIM(TO_CHAR( a.dt_ingresso_plano, 'YYYY' )) AS ano, 
					   COUNT(DISTINCT a.cd_registro_empregado) AS quantos
				  FROM public.titulares_planos a 
				  JOIN public.titulares t
					ON t.cd_empresa            = a.cd_empresa 
				   AND t.cd_registro_empregado = a.cd_registro_empregado 
				   AND t.seq_dependencia       = a.seq_dependencia 					  
				 WHERE a.dt_ingresso_plano    IS NOT NULL
				   AND DATE_TRUNC('month',a.dt_ingresso_plano) <> DATE_TRUNC('month', COALESCE(t.dt_desligamento_eletro, CURRENT_TIMESTAMP + '1 year'))
				   ".(trim($args['cd_empresa']) != "" ? "AND t.cd_empresa = ".intval($args['cd_empresa']) : "")."
				   ".(trim($args['tp_empresa']) != "" ? "AND t.cd_empresa IN (SELECT ps.cd_empresa FROM public.patrocinadoras ps WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."
				 GROUP BY mes,
						  ano
				 ORDER BY ano,
						  mes
			) as ingresso_fceee
			ON mesano.mes=ingresso_fceee.mes AND mesano.ano=ingresso_fceee.ano		
			
			
			LEFT JOIN 
			(
				SELECT TO_CHAR(COALESCE(t.dt_digita_ingresso,tp.dt_ingresso_plano),'MM')AS mes, 
					   TO_CHAR(COALESCE(t.dt_digita_ingresso,tp.dt_ingresso_plano),'YYYY') AS ano, 
					   COUNT(DISTINCT t.cd_registro_empregado)AS quantos
				  FROM public.titulares_planos tp
				  JOIN public.titulares t
					ON t.cd_empresa            = tp.cd_empresa
				   AND t.cd_registro_empregado = tp.cd_registro_empregado
				   AND t.seq_dependencia       = tp.seq_dependencia
				 WHERE tp.dt_ingresso_plano  IS NOT NULL
				   AND TO_CHAR(COALESCE(t.dt_digita_ingresso,tp.dt_ingresso_plano),'YYYY') = '".intval($args['nr_ano'])."'
				   AND DATE_TRUNC('month',tp.dt_ingresso_plano) <> DATE_TRUNC('month', COALESCE(t.dt_desligamento_eletro, CURRENT_TIMESTAMP + '1 year'))
				   ".(trim($args['cd_empresa']) != "" ? "AND t.cd_empresa = ".intval($args['cd_empresa']) : "")."
				   ".(trim($args['tp_empresa']) != "" ? "AND t.cd_empresa IN (SELECT ps.cd_empresa FROM public.patrocinadoras ps WHERE ps.tipo_cliente = '".trim($args['tp_empresa'])."')" : "")."
				 GROUP BY mes,ano
				 ORDER BY ano,mes
			) as digita_ingresso_fceee
			ON mesano.mes=digita_ingresso_fceee.mes AND mesano.ano=digita_ingresso_fceee.ano		
			
			ORDER BY mesano.ano, mesano.mes
			";
		echo "<PRE style='text-align:left;'>".$qr_sql."</PRE>";
		$result = $this->db->query($qr_sql);
	}	
}
?>