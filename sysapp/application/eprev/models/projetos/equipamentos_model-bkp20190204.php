<?php
class Equipamentos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query

		$where_sistema_operacional = "";
		if( $args['sistema_operacional_categoria'] == 'Não Identificado' )
		{
			$where_sistema_operacional = " AND (eq.sistema_operacional_categoria IS NULL OR eq.sistema_operacional_categoria = 'Não Identificado') ";
		}
		else
		{
			$where_sistema_operacional = " AND (eq.sistema_operacional_categoria = '{SISTEMA_OPERACIONAL_CATEGORIA}' OR '' = '{SISTEMA_OPERACIONAL_CATEGORIA}') ";
		}

		$where_processador = "";
		if( $args['processador_categoria'] == 'Não Identificado' )
		{
			$where_processador = " AND (eq.processador_categoria IS NULL OR eq.processador_categoria = 'Não Identificado') ";
		}
		else
		{
			$where_processador = " AND (eq.processador_categoria = '{PROCESSADOR_CATEGORIA}' OR '' = '{PROCESSADOR_CATEGORIA}') ";
		}

		$where_login = "";
		if( $args['login_rede']=='S' )
		{
			$where_login = " AND uc.dt_hora_scanner_computador IS NOT NULL ";
		}
		elseif( $args['login_rede']=='N' )
		{
			$where_login = " AND uc.dt_hora_scanner_computador IS NULL ";
		}

		$sql = "
				SELECT bi.numero_patrimonio AS nr_patrimonio,
			           SUBSTR(bi.descricao,1,30) AS ds_equipamento,
					   TO_CHAR(bi.data_inic_utilizacao, 'DD/MM/YYYY') AS dt_equipamento,
					   TO_CHAR(bi.data_baixa, 'DD/MM/YYYY') AS dt_baixa_bi,
					   TO_CHAR(eq.dt_baixa, 'DD/MM/YYYY') AS dt_baixa,
			           l1.descricao AS ds_situacao, 
					   eq.sistema_operacional, 
					   eq.sistema_operacional_categoria,
					   
					   eq.memoria_ram_categoria,
			           pg_size_pretty(eq.memoria_ram::BIGINT) AS qt_memoria,
					   
			           eq.cod_divisao AS cd_divisao,
			           lf.descricao AS ds_sala,
			           TO_CHAR(eq.ultima_atualizacao, 'DD/MM/YYYY HH24:MI') AS dt_cpuscanner,
			           TO_CHAR(uc.dt_ult_login, 'DD/MM/YYYY HH24:MI') AS dt_eprev,
			           eq.nome_computador,
		               eq.processador_nome,
		               eq.processador_categoria,
			           TO_CHAR(eq.dt_instalacao_os,'DD/MM/YYYY HH24:MI') AS dt_instalacao_os,
			           eq.monitor_resolucao,
			           eq.versao_explorer,
			           eq.versao_firefox,
			           eq.versao_chrome,
			           eq.ip AS nr_ip,
			           l2.descricao AS ds_tipo,
			           uc.guerra AS ds_usuario,
			           TO_CHAR(uc.dt_hora_scanner_computador, 'DD/MM/YY HH24:MI') AS dt_rede,
					   -- CPU SCANNER
			           CASE WHEN (bi.tpbi_cod_tp_bem IN (9,10,26) OR eq.tipo_equipamento IN (1,8,11)) 
				            THEN CASE WHEN bi.tpbi_cod_tp_bem = 10 
							          THEN 'S'
					                  
									  WHEN eq.tipo_equipamento = 8 
									  THEN 'S'
					                  
									  WHEN (eq.ultima_atualizacao < (CURRENT_DATE - '1 months'::interval) OR (eq.ultima_atualizacao IS NULL)) 
					                  THEN CASE WHEN eq.codigo_patrimonio IS NULL 
						                        THEN 'N'
						                        
												WHEN eq.ultima_atualizacao IS NULL 
						                        THEN 'N'

						                        WHEN eq.ultima_atualizacao IS NOT NULL 
						                        THEN 'N'
					                       END 
					                  ELSE 'S'
				            END ELSE 'S'
 			           END AS fl_cpuscanner,
					   eq.versao_cpuscanner,
					   eq.sistema_operacional_tipo			   
			      FROM public.bens_imobilizados bi
			      LEFT JOIN public.locais_fisicos lf
			        ON lf.pes_codigo = bi.pes_codigo
			       AND lf.codigo     = bi.lfisi_cod				  
			      LEFT JOIN projetos.equipamentos eq
			        ON eq.codigo_patrimonio = bi.numero_patrimonio
			      LEFT JOIN public.listas l1
			        ON l1.codigo    = eq.situacao
			       AND l1.categoria	= 'SITU'
			      LEFT JOIN public.listas l2
			        ON l2.codigo::INTEGER = eq.tipo_equipamento
			       AND l2.categoria = 'EQUP'				   
			      LEFT JOIN projetos.usuarios_controledi uc
			        ON uc.codigo = eq.usuario
			     WHERE bi.tpbi_cod_tp_bem   IN (9,10,26)
				   AND bi.descricao        NOT LIKE ('%DISCO R%')
				   AND bi.descricao        NOT LIKE ('%WINCHESTER%') 
				   AND bi.descricao        NOT LIKE ('%PLACA HOT%') 
				   AND bi.descricao        NOT LIKE ('%MEMÓRIA SDRAM%')
				   AND bi.descricao        NOT LIKE ('%MULTIMETRO%')
				   AND bi.descricao        NOT LIKE ('%MONITO%')
				   AND bi.descricao        NOT LIKE ('%SWITCH%')
				   AND bi.descricao        NOT LIKE ('%IMPRESSORA%')
				   AND bi.descricao        NOT LIKE ('%BREAK%')
				   AND bi.descricao        NOT LIKE ('%SCANNER%')			
				   AND bi.descricao        NOT LIKE ('%ROTEADOR%')			
				   AND bi.descricao        NOT LIKE ('%FURADEIRA%')			
			       AND bi.data_baixa        IS NULL
			       AND (eq.tipo_equipamento = {TIPO_EQUIPAMENTO} OR {TIPO_EQUIPAMENTO} = 0)
			       AND (eq.cod_divisao      = '{COD_DIVISAO}' OR '{COD_DIVISAO}'='')
			       AND (lf.codigo           = {CD_SALA} OR {CD_SALA}=0)
			       AND (eq.situacao         = '{SITUACAO}' OR '{SITUACAO}'='')
			       AND (eq.memoria_ram_categoria = '{QT_MEMORIA}' OR '{QT_MEMORIA}'='')
			       AND (CASE WHEN (bi.tpbi_cod_tp_bem IN (9,10,26) OR eq.tipo_equipamento IN (1,8,11)) 
				             THEN CASE WHEN bi.tpbi_cod_tp_bem = 10 
							           THEN 'S'
					                   
								  	   WHEN eq.tipo_equipamento = 8 
									   THEN 'S'
					                  
									   WHEN (eq.ultima_atualizacao < (CURRENT_DATE - '1 months'::interval) OR (eq.ultima_atualizacao IS NULL)) 
					                   THEN CASE WHEN eq.codigo_patrimonio IS NULL 
						                         THEN 'N'
						                        
												 WHEN eq.ultima_atualizacao IS NULL 
						                         THEN 'N'

						                         WHEN eq.ultima_atualizacao IS NOT NULL 
						                         THEN 'N'
					                        END 
					                   ELSE 'S'
				             END ELSE 'S'
 			            END = '{CPUSCANNER}' OR '{CPUSCANNER}' = '')

			$where_sistema_operacional
			$where_processador
			$where_login
		";

		// parse query ...
		esc( "{TIPO_EQUIPAMENTO}", $args["tipo_equipamento"], $sql, "int" );
		esc( "{COD_DIVISAO}", $args["cod_divisao"], $sql );
		esc( "{CD_SALA}", $args["cd_sala"], $sql, "int" );
		esc( "{SITUACAO}", $args["situacao"], $sql );
		esc( "{QT_MEMORIA}", $args["qt_memoria"], $sql );
		esc( "{SISTEMA_OPERACIONAL_CATEGORIA}", $args["sistema_operacional_categoria"], $sql );
		esc( "{PROCESSADOR_CATEGORIA}", $args["processador_categoria"], $sql );
		esc( "{CPUSCANNER}", $args["cpuscanner"], $sql );

		# echo "<pre>$sql</pre>";

		$result = $this->db->query($sql);
	}
	
	
	function detalhe( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT e.codigo_patrimonio, 
						   e.usuario, 
						   e.ip, 
						   e.ip_numerico, 
						   e.tipo_equipamento, 
                           e.cod_divisao, 
						   TO_CHAR(e.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro,
						   convert_from(convert_to(e.programas_instalados, 'UTF8'), 'LATIN1') AS programas_instalados,
						   e.situacao, 
						   e.monitor, 
						   e.impressora, 
						   e.periferico, 
						   e.configuracao, 
						   e.identif_rede, 
						   e.memoria, 
						   e.processador, 
						   e.espaco_disco_total, 
						   pg_size_pretty(CAST(COALESCE(espaco_disco_total,0) AS BIGINT) * 1000) AS qt_espaco_total,
						   e.espaco_disco_livre, 
						   pg_size_pretty(CAST(COALESCE(espaco_disco_livre,0) AS BIGINT) * 1000) AS qt_espaco_livre,
						   e.espaco_disco_usado, 
						   pg_size_pretty(CAST(COALESCE(espaco_disco_usado,0) AS BIGINT) * 1000) AS qt_espaco_usado,
						   e.tipo_processador, 
                           e.nome_computador, 
	                       TO_CHAR(e.ultima_atualizacao,'DD/MM/YYYY HH24:MI') AS ultima_atualizacao, 
	                       e.tipo_equip_temp, 
                           e.atalhos, 
						   e.processador_nome, 
						   e.mac_address, 
						   e.drv_odbc, 
	                       TO_CHAR(e.dt_cpuscanner_verificado,'DD/MM/YYYY HH24:MI') AS dt_cpuscanner_verificado, 
                           ucv.nome AS ds_cpuscanner_verificado_usuario,
	                       e.cd_cpuscanner_verificado_usuario, 
	   	                   e.monitor_resolucao, 
						   e.versao_explorer, 
                           e.versao_firefox,
                           e.versao_chrome,
	                       e.versao_dotnet,
	                       TO_CHAR(e.dt_instalacao_os,'DD/MM/YYYY') AS dt_instalacao_os,
						   bi.descricao AS bi_descricao,
						   TO_CHAR(bi.data_inic_utilizacao, 'DD/MM/YYYY') AS dt_equipamento,
   						   e.memoria_ram, 
						   e.memoria_ram_categoria,
                           pg_size_pretty(e.memoria_ram::BIGINT) AS qt_memoria,
						   e.processador_nome,
						   e.processador_categoria,
						   e.sistema_operacional, 
						   e.sistema_operacional_categoria,
					       e.versao_cpuscanner,
					       e.versao_freepdf,
					       e.versao_java,
					       e.lista_unidade,
					       e.lista_dispositivo_som,
						   e.lista_compartilhamento,
						   e.ds_usuario,
						   TO_CHAR(bi.data_baixa, 'DD/MM/YYYY') AS dt_baixa_bi,
						   TO_CHAR(e.dt_baixa, 'DD/MM/YYYY') AS dt_baixa						   
					  FROM projetos.equipamentos e
					  LEFT JOIN projetos.usuarios_controledi ucv
					    ON ucv.codigo = e.cd_cpuscanner_verificado_usuario
					  LEFT JOIN bens_imobilizados bi 
					    ON bi.numero_patrimonio = e.codigo_patrimonio
					 WHERE e.codigo_patrimonio = ".$args['codigo_patrimonio']."
		       ";
		$result = $this->db->query($qr_sql);
	}	
	
	function meu_computador( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT e.codigo_patrimonio, 
						   e.usuario, 
						   e.ip, 
						   e.ip_numerico, 
						   e.tipo_equipamento, 
                           e.cod_divisao, 
						   TO_CHAR(e.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro,
						   e.programas_instalados, 
						   e.situacao, 
						   e.monitor, 
						   e.impressora, 
						   e.periferico, 
						   e.configuracao, 
						   e.identif_rede, 
						   e.memoria, 
						   e.processador, 
						   e.espaco_disco_total, 
						   pg_size_pretty(CAST(COALESCE(espaco_disco_total,0) AS BIGINT) * 1000) AS qt_espaco_total,
						   e.espaco_disco_livre, 
						   pg_size_pretty(CAST(COALESCE(espaco_disco_livre,0) AS BIGINT) * 1000) AS qt_espaco_livre,
						   e.espaco_disco_usado, 
						   pg_size_pretty(CAST(COALESCE(espaco_disco_usado,0) AS BIGINT) * 1000) AS qt_espaco_usado,
                           e.tipo_processador, 
                           e.nome_computador, 
	                       TO_CHAR(e.ultima_atualizacao,'DD/MM/YYYY HH24:MI') AS ultima_atualizacao, 
	                       e.tipo_equip_temp, 
                           e.atalhos, 
						   e.processador_nome, 
						   e.mac_address, 
						   e.drv_odbc, 
	                       TO_CHAR(e.dt_cpuscanner_verificado,'DD/MM/YYYY HH24:MI') AS dt_cpuscanner_verificado, 
                           ucv.nome AS ds_cpuscanner_verificado_usuario,
	                       e.cd_cpuscanner_verificado_usuario, 
	   	                   e.monitor_resolucao, 
						   e.versao_explorer, 
                           e.versao_firefox,
                           e.versao_chrome,
	                       e.versao_dotnet,
	                       TO_CHAR(e.dt_instalacao_os,'DD/MM/YYYY') AS dt_instalacao_os,
						   bi.descricao AS bi_descricao,
						   TO_CHAR(bi.data_inic_utilizacao, 'DD/MM/YYYY') AS dt_equipamento,
   						   e.memoria_ram, 
						   e.memoria_ram_categoria,
                           pg_size_pretty(e.memoria_ram::BIGINT) AS qt_memoria,
						   e.processador_nome,
						   e.processador_categoria,
						   e.sistema_operacional, 
						   e.sistema_operacional_categoria
					  FROM projetos.equipamentos e
					  LEFT JOIN projetos.usuarios_controledi ucv
					    ON ucv.codigo = e.cd_cpuscanner_verificado_usuario
					  LEFT JOIN bens_imobilizados bi 
					    ON bi.numero_patrimonio = e.codigo_patrimonio
					 WHERE e.usuario = ".intval($args['cd_usuario'])."
		       ";
		$result = $this->db->query($qr_sql);
	}		
	
	function salvar(&$result, $args=array())
	{
		if(!$this->existeEquipamento($args['codigo_patrimonio']))
		{
			#### INSERT ####
			$qr_sql = " 
						INSERT INTO projetos.equipamentos
						     (
							   codigo_patrimonio,
							   dt_cadastro,                   
						       cod_divisao,                   
						       usuario,                       
						       nome_computador,
						       ip,                            
						       tipo_equipamento,              
						       situacao,                     
						       sistema_operacional_categoria,
							   sistema_operacional,
						       processador_categoria,        
						       processador_nome,        
							   memoria_ram,                   
							   tipo_c,
							   espaco_total_c,                
							   espaco_livre_c,                
							   espaco_usado_c
							 )
						VALUES
							 (
						       ".$args['codigo_patrimonio'].",
							   ".(trim($args['dt_cadastro'])                   == "" ? "NULL" : "TO_DATE('".$args['dt_cadastro'] ."','DD/MM/YYYY')").",
							   ".(trim($args['cod_divisao'])                   == "" ? "NULL" : "'".$args['cod_divisao']."'").",
							   ".(trim($args['usuario'])                       == "" ? "NULL" : $args['usuario']).",
							   ".(trim($args['nome_computador'])               == "" ? "NULL" : "'".$args['nome_computador']."'").",
							   ".(trim($args['ip'])                            == "" ? "NULL" : "'".$args['ip']."'").",
							   ".(trim($args['tipo_equipamento'])              == "" ? "NULL" : "'".$args['tipo_equipamento']."'").",
							   ".(trim($args['situacao'])                      == "" ? "DEFAULT" : "'".$args['situacao']."'").",
							   ".(trim($args['sistema_operacional_categoria']) == "" ? "NULL" : "'".$args['sistema_operacional_categoria']."'").",
							   ".(trim($args['sistema_operacional_categoria']) == "" ? "NULL" : "'".$args['sistema_operacional_categoria']."'").",
							   ".(trim($args['processador_categoria'])         == "" ? "NULL" : "'".$args['processador_categoria']."'").",
							   ".(trim($args['processador_categoria'])         == "" ? "NULL" : "'".$args['processador_categoria']."'").",
							   ".(trim($args['memoria_ram'])                   == "" ? "DEFAULT" : $args['memoria_ram']).",
							   'RIG',
							   ".(trim($args['espaco_disco_total'])            == "" ? "DEFAULT" : $args['espaco_disco_total']).",
							   ".(trim($args['espaco_disco_livre'])            == "" ? "DEFAULT" : $args['espaco_disco_livre']).",
							   ".(trim($args['espaco_disco_usado'])            == "" ? "DEFAULT" : $args['espaco_disco_usado'])."
							 )
				   ";				
		}
		else
		{
			#### UPDATE ####
			$qr_sql = " 
						UPDATE projetos.equipamentos
						   SET dt_cadastro                   = ".(trim($args['dt_cadastro'])                   == "" ? "NULL" : "TO_DATE('".$args['dt_cadastro'] ."','DD/MM/YYYY')").",
						       cod_divisao                   = ".(trim($args['cod_divisao'])                   == "" ? "NULL" : "'".$args['cod_divisao']."'").",
						       usuario                       = ".(trim($args['usuario'])                       == "" ? "NULL" : $args['usuario']).",
						       nome_computador               = ".(trim($args['nome_computador'])               == "" ? "NULL" : "'".$args['nome_computador']."'").",
						       ip                            = ".(trim($args['ip'])                            == "" ? "NULL" : "'".$args['ip']."'").",
						       tipo_equipamento              = ".(trim($args['tipo_equipamento'])              == "" ? "NULL" : "'".$args['tipo_equipamento']."'").",
						       situacao                      = ".(trim($args['situacao'])                      == "" ? "DEFAULT" : "'".$args['situacao']."'").",
						       sistema_operacional_categoria = ".(trim($args['sistema_operacional_categoria']) == "" ? "NULL" : "'".$args['sistema_operacional_categoria']."'").",
							   sistema_operacional           = ".(trim($args['sistema_operacional_categoria']) == "" ? "NULL" : "'".$args['sistema_operacional_categoria']."'").",
						       processador_categoria         = ".(trim($args['processador_categoria'])         == "" ? "NULL" : "'".$args['processador_categoria']."'").",
						       processador_nome              = ".(trim($args['processador_categoria'])         == "" ? "NULL" : "'".$args['processador_categoria']."'").",
							   memoria_ram                   = ".(trim($args['memoria_ram'])                   == "" ? "DEFAULT" : $args['memoria_ram']).",
							   tipo_c                        = 'RIG',
							   espaco_total_c                = ".(trim($args['espaco_disco_total'])            == "" ? "DEFAULT" : $args['espaco_disco_total']).",
							   espaco_livre_c                = ".(trim($args['espaco_disco_livre'])            == "" ? "DEFAULT" : $args['espaco_disco_livre']).",
							   espaco_usado_c                = ".(trim($args['espaco_disco_usado'])            == "" ? "DEFAULT" : $args['espaco_disco_usado']).",
							   espaco_total_d                = DEFAULT,
							   espaco_total_e                = DEFAULT,
							   espaco_total_f                = DEFAULT,
							   espaco_total_g                = DEFAULT,
							   espaco_total_h                = DEFAULT,			   
							   espaco_livre_d                = DEFAULT,
							   espaco_livre_e                = DEFAULT,
							   espaco_livre_f                = DEFAULT,
							   espaco_livre_g                = DEFAULT,
							   espaco_livre_h                = DEFAULT,
							   espaco_usado_d                = DEFAULT,
							   espaco_usado_e                = DEFAULT,
							   espaco_usado_f                = DEFAULT,
							   espaco_usado_g                = DEFAULT,
							   espaco_usado_h                = DEFAULT	   
						 WHERE codigo_patrimonio = ".$args['codigo_patrimonio']."
				   ";			
		}
		
		#echo "<PRE>$qr_sql</PRE>";exit;
		$this->db->query($qr_sql);		
	}

	function existeEquipamento($codigo_patrimonio = 0)
	{
		$qr_sql = " 
					SELECT COUNT(*) AS fl_existe
   					  FROM projetos.equipamentos
					 WHERE codigo_patrimonio = ".$codigo_patrimonio."
			   ";
		$result = $this->db->query($qr_sql);
		$ar_reg = $result->row_array();	
		if($ar_reg['fl_existe'] == 0)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function setCPUScannerManual( &$result, $args=array() )
	{
		$qr_sql = " 
					UPDATE projetos.equipamentos
					   SET dt_cpuscanner_verificado = CURRENT_TIMESTAMP,
						   cd_cpuscanner_verificado_usuario = ".$args['cd_usuario']."
					 WHERE codigo_patrimonio = ".$args['codigo_patrimonio']."
			   ";
		$this->db->query($qr_sql);
	}
	
	function tipoEquipamento( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT codigo AS value,
					       descricao AS text 
				      FROM public.listas 
					 WHERE categoria = 'EQUP' 
					 ORDER BY descricao
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function gerenciaEquipamento( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT codigo AS value, 
					       nome AS text 
					  FROM projetos.divisoes 
					 ORDER BY nome
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function usuarioEquipamento( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT codigo AS value, 
					       nome AS text 
					  FROM projetos.usuarios_controledi 
					 ORDER BY nome
		          ";
		$result = $this->db->query($qr_sql);
	}	

	function situacaoEquipamento( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT codigo AS value, 
					       descricao AS text 
					  FROM public.listas 
					 WHERE categoria = 'SITU' 
					 ORDER BY descricao
		          ";
		$result = $this->db->query($qr_sql);
	}	

	function sisOperacionalEquipamento( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT DISTINCT sistema_operacional_categoria AS value,
					       sistema_operacional_categoria AS text
					 FROM projetos.equipamentos
					ORDER BY value
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function salaEquipamento(&$result, $args=array())
	{
		$qr_sql = "
					SELECT DISTINCT(lf.descricao) AS text, 
					       lf.codigo AS value 
					  FROM bens_imobilizados bi 
					  JOIN public.locais_fisicos lf 
					    ON lf.pes_codigo = bi.pes_codigo 
					   AND lf.codigo     = bi.lfisi_cod 
					 WHERE bi.tpbi_cod_tp_bem IN (8,9,10,11,12,13,26,29,40,52) 
					 ORDER BY text
		          ";
		$result = $this->db->query($qr_sql);
	}	

	function memoriaEquipamento( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT DISTINCT memoria_ram_categoria AS value,
					       memoria_ram_categoria AS text
					 FROM projetos.equipamentos
					ORDER BY value
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function processadorEquipamento(&$result, $args=array())
	{
		$qr_sql = "
					SELECT DISTINCT processador_categoria AS value,
					       processador_categoria AS text
					 FROM projetos.equipamentos
					ORDER BY value
		          ";
		$result = $this->db->query($qr_sql);
	}

	function resumoTipoEquipamento(&$result, $args=array())
	{
		$qr_sql = "
					SELECT l2.descricao AS tipo_equipamento,
						   l2.codigo,
						   COUNT(*)	AS quantidade	   
					  FROM public.bens_imobilizados bi
					  JOIN public.locais_fisicos lf
						ON lf.pes_codigo = bi.pes_codigo
					   AND lf.codigo     = bi.lfisi_cod				  
					  JOIN projetos.equipamentos eq
						ON eq.codigo_patrimonio = bi.numero_patrimonio
					  JOIN public.listas l1
						ON l1.codigo    = eq.situacao
					   AND l1.categoria	= 'SITU'
					  JOIN public.listas l2
						ON l2.codigo::int = eq.tipo_equipamento::int
					   AND l2.categoria	  = 'EQUP'				   
					 WHERE bi.tpbi_cod_tp_bem  IN (9,10,26)
					   AND bi.descricao        NOT LIKE ('%DISCO R%')
					   AND bi.descricao        NOT LIKE ('%WINCHESTER%') 
					   AND bi.descricao        NOT LIKE ('%PLACA HOT%') 
					   AND bi.descricao        NOT LIKE ('%MEMÓRIA SDRAM%')
					   AND bi.descricao        NOT LIKE ('%MULTIMETRO%')
					   AND bi.descricao        NOT LIKE ('%MONITO%')
					   AND bi.descricao        NOT LIKE ('%SWITCH%')
					   AND bi.descricao        NOT LIKE ('%IMPRESSORA%')
					   AND bi.descricao        NOT LIKE ('%BREAK%')
					   AND bi.descricao        NOT LIKE ('%SCANNER%')			
					   AND bi.descricao        NOT LIKE ('%ROTEADOR%')			
					   AND bi.descricao        NOT LIKE ('%FURADEIRA%')	
					   AND eq.situacao         = 'SIT1'				   
					   AND bi.data_baixa       IS NULL
					   AND eq.tipo_equipamento IN (1,8,11)
					   ".(trim($args['cd_divisao']) != "" ? "AND eq.cod_divisao = '".trim($args['cd_divisao'])."'" : "")."
					 GROUP BY l2.descricao,
						      l2.codigo 
					 ORDER BY l2.descricao
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function resumoSituacaoEquipamento(&$result, $args=array())
	{
		$qr_sql = "
					SELECT l1.descricao AS situacao_equipamento,
						   l1.codigo,
						   COUNT(*)	AS quantidade	   
					  FROM public.bens_imobilizados bi
					  JOIN public.locais_fisicos lf
						ON lf.pes_codigo = bi.pes_codigo
					   AND lf.codigo     = bi.lfisi_cod				  
					  JOIN projetos.equipamentos eq
						ON eq.codigo_patrimonio = bi.numero_patrimonio
					  JOIN public.listas l1
						ON l1.codigo    = eq.situacao
					   AND l1.categoria	= 'SITU'
					  JOIN public.listas l2
						ON l2.codigo::int = eq.tipo_equipamento::int
					   AND l2.categoria	  = 'EQUP'				   
					 WHERE bi.tpbi_cod_tp_bem  IN (9,10,26)
					   AND bi.descricao        NOT LIKE ('%DISCO R%')
					   AND bi.descricao        NOT LIKE ('%WINCHESTER%') 
					   AND bi.descricao        NOT LIKE ('%PLACA HOT%') 
					   AND bi.descricao        NOT LIKE ('%MEMÓRIA SDRAM%')
					   AND bi.descricao        NOT LIKE ('%MULTIMETRO%')
					   AND bi.descricao        NOT LIKE ('%MONITO%')
					   AND bi.descricao        NOT LIKE ('%SWITCH%')
					   AND bi.descricao        NOT LIKE ('%IMPRESSORA%')
					   AND bi.descricao        NOT LIKE ('%BREAK%')
					   AND bi.descricao        NOT LIKE ('%SCANNER%')			
					   AND bi.descricao        NOT LIKE ('%ROTEADOR%')			
					   AND bi.descricao        NOT LIKE ('%FURADEIRA%')					
					   AND bi.data_baixa       IS NULL
					   AND eq.tipo_equipamento IN (1,8,11)
					   ".(trim($args['cd_divisao']) != "" ? "AND eq.cod_divisao = '".trim($args['cd_divisao'])."'" : "")."
					 GROUP BY l1.descricao,
						      l1.codigo
					 ORDER BY l1.descricao
		          ";
		$result = $this->db->query($qr_sql);
	}		
	
	function resumoDiscoEquipamento(&$result, $args=array())
	{
		$qr_sql = "
					SELECT l2.descricao AS tipo_equipamento,
					       l2.codigo,
					       SUM(COALESCE(eq.espaco_disco_total,0)) AS espaco_disco_total,
					       SUM(COALESCE(eq.espaco_disco_livre,0)) AS espaco_disco_livre,
					       SUM(COALESCE(eq.espaco_disco_usado,0)) AS espaco_disco_usado
					  FROM public.bens_imobilizados bi
					  JOIN public.locais_fisicos lf
						ON lf.pes_codigo = bi.pes_codigo
					   AND lf.codigo     = bi.lfisi_cod				  
					  JOIN projetos.equipamentos eq
						ON eq.codigo_patrimonio = bi.numero_patrimonio
					  JOIN public.listas l1
						ON l1.codigo    = eq.situacao
					   AND l1.categoria	= 'SITU'
					  JOIN public.listas l2
						ON l2.codigo::int = eq.tipo_equipamento::int
					   AND l2.categoria	  = 'EQUP'				   
					 WHERE bi.tpbi_cod_tp_bem  IN (9,10,26)
					   AND bi.descricao        NOT LIKE ('%DISCO R%')
					   AND bi.descricao        NOT LIKE ('%WINCHESTER%') 
					   AND bi.descricao        NOT LIKE ('%PLACA HOT%') 
					   AND bi.descricao        NOT LIKE ('%MEMÓRIA SDRAM%')
					   AND bi.descricao        NOT LIKE ('%MULTIMETRO%')
					   AND bi.descricao        NOT LIKE ('%MONITO%')
					   AND bi.descricao        NOT LIKE ('%SWITCH%')
					   AND bi.descricao        NOT LIKE ('%IMPRESSORA%')
					   AND bi.descricao        NOT LIKE ('%BREAK%')
					   AND bi.descricao        NOT LIKE ('%SCANNER%')			
					   AND bi.descricao        NOT LIKE ('%ROTEADOR%')			
					   AND bi.descricao        NOT LIKE ('%FURADEIRA%')	
					   AND eq.situacao         = 'SIT1'				   
					   AND bi.data_baixa       IS NULL
					   AND eq.tipo_equipamento IN (1,8,11)
					   ".(trim($args['cd_divisao']) != "" ? "AND eq.cod_divisao = '".trim($args['cd_divisao'])."'" : "")."
					 GROUP BY l2.descricao,
						      l2.codigo 
					 ORDER BY l2.descricao
		          ";
		$result = $this->db->query($qr_sql);
	}		
	
	function resumoMemoriaEquipamento(&$result, $args=array())
	{
		$qr_sql = "
					SELECT eq.memoria_ram_categoria,
					       eq.memoria_ram_categoria_ordem,
						   COUNT(*)	AS quantidade	   
					  FROM public.bens_imobilizados bi
					  JOIN public.locais_fisicos lf
						ON lf.pes_codigo = bi.pes_codigo
					   AND lf.codigo     = bi.lfisi_cod				  
					  JOIN projetos.equipamentos eq
						ON eq.codigo_patrimonio = bi.numero_patrimonio
					  JOIN public.listas l1
						ON l1.codigo    = eq.situacao
					   AND l1.categoria = 'SITU'
					  JOIN public.listas l2
						ON l2.codigo::int = eq.tipo_equipamento::int
					   AND l2.categoria   = 'EQUP'				   
					 WHERE bi.tpbi_cod_tp_bem  IN (9,10,26)
					   AND bi.descricao        NOT LIKE ('%DISCO R%')
					   AND bi.descricao        NOT LIKE ('%WINCHESTER%') 
					   AND bi.descricao        NOT LIKE ('%PLACA HOT%') 
					   AND bi.descricao        NOT LIKE ('%MEMÓRIA SDRAM%')
					   AND bi.descricao        NOT LIKE ('%MULTIMETRO%')
					   AND bi.descricao        NOT LIKE ('%MONITO%')
					   AND bi.descricao        NOT LIKE ('%SWITCH%')
					   AND bi.descricao        NOT LIKE ('%IMPRESSORA%')
					   AND bi.descricao        NOT LIKE ('%BREAK%')
					   AND bi.descricao        NOT LIKE ('%SCANNER%')			
					   AND bi.descricao        NOT LIKE ('%ROTEADOR%')			
					   AND bi.descricao        NOT LIKE ('%FURADEIRA%')	
					   AND eq.situacao         = 'SIT1'				   
					   AND bi.data_baixa       IS NULL
					   AND eq.tipo_equipamento IN (1,11)
					   ".(trim($args['cd_divisao']) != "" ? "AND eq.cod_divisao = '".trim($args['cd_divisao'])."'" : "")."
					 GROUP BY eq.memoria_ram_categoria, eq.memoria_ram_categoria_ordem
					 ORDER BY eq.memoria_ram_categoria_ordem 
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function resumoProcessadorEquipamento( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT eq.processador_categoria,
						   COUNT(*)	AS quantidade	   
					  FROM public.bens_imobilizados bi
					  JOIN public.locais_fisicos lf
						ON lf.pes_codigo = bi.pes_codigo
					   AND lf.codigo     = bi.lfisi_cod				  
					  JOIN projetos.equipamentos eq
						ON eq.codigo_patrimonio = bi.numero_patrimonio
					  JOIN public.listas l1
						ON l1.codigo    = eq.situacao
					   AND l1.categoria = 'SITU'
					  JOIN public.listas l2
						ON l2.codigo::int = eq.tipo_equipamento::int
					   AND l2.categoria   = 'EQUP'				   
					 WHERE bi.tpbi_cod_tp_bem  IN (9,10,26)
					   AND bi.descricao        NOT LIKE ('%DISCO R%')
					   AND bi.descricao        NOT LIKE ('%WINCHESTER%') 
					   AND bi.descricao        NOT LIKE ('%PLACA HOT%') 
					   AND bi.descricao        NOT LIKE ('%MEMÓRIA SDRAM%')
					   AND bi.descricao        NOT LIKE ('%MULTIMETRO%')
					   AND bi.descricao        NOT LIKE ('%MONITO%')
					   AND bi.descricao        NOT LIKE ('%SWITCH%')
					   AND bi.descricao        NOT LIKE ('%IMPRESSORA%')
					   AND bi.descricao        NOT LIKE ('%BREAK%')
					   AND bi.descricao        NOT LIKE ('%SCANNER%')			
					   AND bi.descricao        NOT LIKE ('%ROTEADOR%')			
					   AND bi.descricao        NOT LIKE ('%FURADEIRA%')	
					   AND eq.situacao         = 'SIT1'				   
					   AND bi.data_baixa       IS NULL
					   AND eq.tipo_equipamento IN (1,11)
					   ".(trim($args['cd_divisao']) != "" ? "AND eq.cod_divisao = '".trim($args['cd_divisao'])."'" : "")."
					 GROUP BY eq.processador_categoria
					 ORDER BY quantidade 
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function resumoSistemaOperacionalEquipamento( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT eq.sistema_operacional_categoria,
						   COUNT(*)	AS quantidade	   
					  FROM public.bens_imobilizados bi
					  JOIN public.locais_fisicos lf
						ON lf.pes_codigo = bi.pes_codigo
					   AND lf.codigo     = bi.lfisi_cod				  
					  JOIN projetos.equipamentos eq
						ON eq.codigo_patrimonio = bi.numero_patrimonio
					  JOIN public.listas l1
						ON l1.codigo    = eq.situacao
					   AND l1.categoria = 'SITU'
					  JOIN public.listas l2
						ON l2.codigo::int = eq.tipo_equipamento::int
					   AND l2.categoria   = 'EQUP'				   
					 WHERE bi.tpbi_cod_tp_bem  IN (9,10,26)
					   AND bi.descricao        NOT LIKE ('%DISCO R%')
					   AND bi.descricao        NOT LIKE ('%WINCHESTER%') 
					   AND bi.descricao        NOT LIKE ('%PLACA HOT%') 
					   AND bi.descricao        NOT LIKE ('%MEMÓRIA SDRAM%')
					   AND bi.descricao        NOT LIKE ('%MULTIMETRO%')
					   AND bi.descricao        NOT LIKE ('%MONITO%')
					   AND bi.descricao        NOT LIKE ('%SWITCH%')
					   AND bi.descricao        NOT LIKE ('%IMPRESSORA%')
					   AND bi.descricao        NOT LIKE ('%BREAK%')
					   AND bi.descricao        NOT LIKE ('%SCANNER%')			
					   AND bi.descricao        NOT LIKE ('%ROTEADOR%')			
					   AND bi.descricao        NOT LIKE ('%FURADEIRA%')	
					   AND eq.situacao         = 'SIT1'				   
					   AND bi.data_baixa       IS NULL
					   AND eq.tipo_equipamento IN (1,11)
					   ".(trim($args['cd_divisao']) != "" ? "AND eq.cod_divisao = '".trim($args['cd_divisao'])."'" : "")."
					 GROUP BY eq.sistema_operacional_categoria
					 ORDER BY quantidade 
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
}
?>