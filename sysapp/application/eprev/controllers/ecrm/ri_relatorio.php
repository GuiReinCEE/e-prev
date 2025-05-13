<?php
class ri_relatorio extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index()
    {
		CheckLogin();
		if(gerencia_in(array('GRI','GI','GA')))
		{
			$args = Array();	
			$data = Array();	
			$this->load->view('ecrm/ri_relatorio/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		
    }	
	
    function listar()
    {
        CheckLogin();
	
		if(gerencia_in(array('GRI','GI','GA')))
		{		
			$result = null;
			$data = Array();
			$args = Array();
			
			$args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE);
			$args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
			$args["cd_plano"]   = $this->input->post("cd_plano", TRUE);
			$args["nr_ano"]     = $this->input->post("nr_ano", TRUE);
			
			manter_filtros($args);
			
			$qr_acum = "
							SELECT i.cd_empresa,
								   i.cd_plano,
								   SUM(i.qt_ingresso) AS qt_ingresso,
								   SUM(i.qt_desligamento) AS qt_desligamento,
								   SUM(i.qt_digita_ingresso) AS qt_digita_ingresso,
								   SUM(i.qt_digita_desligamento) AS qt_digita_desligamento
							  FROM (
							SELECT tp.cd_empresa,
								   tp.cd_plano,
								   COUNT(DISTINCT funcoes.cripto_re(tp.cd_empresa, tp.cd_registro_empregado, tp.seq_dependencia)) AS qt_ingresso,
								   0 AS qt_desligamento,
								   0 AS qt_digita_ingresso,
								   0 AS qt_digita_desligamento
							  FROM public.titulares_planos tp 
							 WHERE tp.dt_ingresso_plano IS NOT NULL
								".(trim($args['cd_empresa']) != "" ?  "AND tp.cd_empresa = ".intval($args['cd_empresa']) : "")."
								".(trim($args['cd_plano']) != "" ?  "AND tp.cd_plano = ".intval($args['cd_plano']) : "")."
							   AND CAST(TO_CHAR(tp.dt_ingresso_plano,'YYYY') AS INTEGER) < ".intval($args['nr_ano'])."
							 GROUP BY tp.cd_empresa, tp.cd_plano

							UNION

							SELECT tp.cd_empresa,
								   tp.cd_plano,
								   0 AS qt_ingresso,
								   COUNT(DISTINCT funcoes.cripto_re(tp.cd_empresa, tp.cd_registro_empregado, tp.seq_dependencia)) AS qt_desligamento,
								   0 AS qt_digita_ingresso,
								   0 AS qt_digita_desligamento
							  FROM public.titulares_planos tp 
							 WHERE tp.dt_deslig_plano IS NOT NULL
								".(trim($args['cd_empresa']) != "" ?  "AND tp.cd_empresa = ".intval($args['cd_empresa']) : "")."
								".(trim($args['cd_plano']) != "" ?  "AND tp.cd_plano = ".intval($args['cd_plano']) : "")."							   
							   AND CAST(TO_CHAR(tp.dt_deslig_plano,'YYYY') AS INTEGER) < ".intval($args['nr_ano'])."
							 GROUP BY tp.cd_empresa, tp.cd_plano
							 
							UNION 
							 
							SELECT tp.cd_empresa,
								   tp.cd_plano,
								   0 AS qt_ingresso,
								   0 AS qt_desligamento,
								   COUNT(DISTINCT funcoes.cripto_re(tp.cd_empresa, tp.cd_registro_empregado, tp.seq_dependencia)) AS qt_digita_ingresso,
								   0 AS qt_digita_desligamento
							  FROM public.titulares_planos tp
							  JOIN public.titulares t
								ON t.cd_empresa            = tp.cd_empresa
							   AND t.cd_registro_empregado = tp.cd_registro_empregado
							   AND t.seq_dependencia       = tp.seq_dependencia
							 WHERE tp.dt_ingresso_plano  IS NOT NULL
								".(trim($args['cd_empresa']) != "" ?  "AND tp.cd_empresa = ".intval($args['cd_empresa']) : "")."
								".(trim($args['cd_plano']) != "" ?  "AND tp.cd_plano = ".intval($args['cd_plano']) : "")."							   
							   AND CAST(TO_CHAR(COALESCE(t.dt_digita_ingresso,tp.dt_ingresso_plano),'YYYY') AS INTEGER) < ".intval($args['nr_ano'])."
							 GROUP BY tp.cd_empresa, tp.cd_plano

							UNION

							SELECT tp.cd_empresa,
								   tp.cd_plano,
								   0 AS qt_ingresso,
								   0 AS qt_desligamento,
								   0 AS qt_digita_ingresso,
								   COUNT(DISTINCT funcoes.cripto_re(tp.cd_empresa, tp.cd_registro_empregado, tp.seq_dependencia)) AS qt_digita_desligamento
							  FROM public.titulares_planos tp
							  JOIN public.titulares t
								ON t.cd_empresa            = tp.cd_empresa
							   AND t.cd_registro_empregado = tp.cd_registro_empregado
							   AND t.seq_dependencia       = tp.seq_dependencia
							 WHERE tp.dt_ingresso_plano  IS NOT NULL
								".(trim($args['cd_empresa']) != "" ?  "AND tp.cd_empresa = ".intval($args['cd_empresa']) : "")."
								".(trim($args['cd_plano']) != "" ?  "AND tp.cd_plano = ".intval($args['cd_plano']) : "")."							   
							   AND CAST(TO_CHAR(COALESCE(t.dt_digita_desligamento,tp.dt_deslig_plano),'YYYY') AS INTEGER) < ".intval($args['nr_ano'])."
							 GROUP BY tp.cd_empresa, tp.cd_plano							 
							) i

							 GROUP BY i.cd_empresa, i.cd_plano			
			           ";
			$result = $this->db->query($qr_acum);
			$data['acumulado'] = $result->result_array();			
			
			$qr_sql = "
						SELECT ma.mes,
							   ma.ano,
							   SUM(COALESCE(di.qt_ingresso,0)) AS qt_ingresso,
							   SUM(COALESCE(dd.qt_desligamento,0)) AS qt_desligamento,
							   SUM(COALESCE(ddi.qt_digita_ingresso,0)) AS qt_digita_ingresso,
							   SUM(COALESCE(ddd.qt_digita_desligamento,0)) AS qt_digita_desligamento
						  FROM (SELECT pp.cd_empresa,
									   pp.cd_plano,
									   TRIM(TO_CHAR(mes, '00')) AS mes, 
							           TRIM(TO_CHAR(ano, '0000')) AS ano
							      FROM generate_series(".intval($args['nr_ano']).", ".intval($args['nr_ano']).") AS ano, 
								       generate_series(1,12) AS mes,
									   planos_patrocinadoras pp
								 WHERE pp.cd_plano > 0
									 ORDER BY pp.cd_empresa, pp.cd_plano, ano, mes) as ma
								
						  LEFT JOIN (SELECT tp.cd_empresa,
											tp.cd_plano,
											TRIM(TO_CHAR(tp.dt_ingresso_plano, 'MM' )) AS mes, 
											TRIM(TO_CHAR(tp.dt_ingresso_plano, 'YYYY' )) AS ano, 
											COUNT(DISTINCT funcoes.cripto_re(tp.cd_empresa, tp.cd_registro_empregado, tp.seq_dependencia)) AS qt_ingresso
									   FROM public.titulares_planos tp 
									   JOIN public.titulares t
										 ON t.cd_empresa            = tp.cd_empresa 
									    AND t.cd_registro_empregado = tp.cd_registro_empregado 
									    AND t.seq_dependencia       = tp.seq_dependencia 										   
									  WHERE tp.dt_ingresso_plano IS NOT NULL
									  AND DATE_TRUNC('month',tp.dt_ingresso_plano) <> DATE_TRUNC('month', COALESCE(t.dt_desligamento_eletro, CURRENT_TIMESTAMP + '1 year'))
									  
									  GROUP BY tp.cd_empresa, tp.cd_plano, ano, mes
									  ORDER BY tp.cd_empresa, tp.cd_plano, ano, mes) AS di
							 ON di.cd_empresa = ma.cd_empresa
							AND di.cd_plano   = ma.cd_plano
								AND di.mes        = ma.mes 
								AND di.ano        = ma.ano

						  LEFT JOIN (SELECT tp.cd_empresa,
											tp.cd_plano,
											TRIM(TO_CHAR(tp.dt_deslig_plano, 'MM' )) AS mes, 
											TRIM(TO_CHAR(tp.dt_deslig_plano, 'YYYY' )) AS ano, 
											COUNT(DISTINCT funcoes.cripto_re(tp.cd_empresa, tp.cd_registro_empregado, tp.seq_dependencia)) AS qt_desligamento
									   FROM public.titulares_planos tp 
									   JOIN public.titulares t
										 ON t.cd_empresa            = tp.cd_empresa 
									    AND t.cd_registro_empregado = tp.cd_registro_empregado 
									    AND t.seq_dependencia       = tp.seq_dependencia 									   
									  WHERE tp.dt_deslig_plano IS NOT NULL
									    AND DATE_TRUNC('month',tp.dt_ingresso_plano) <> DATE_TRUNC('month', COALESCE(t.dt_desligamento_eletro, CURRENT_TIMESTAMP + '1 year'))
									  GROUP BY tp.cd_empresa, tp.cd_plano, ano, mes
									  ORDER BY tp.cd_empresa, tp.cd_plano, ano, mes) AS dd
							 ON dd.cd_empresa = ma.cd_empresa
							AND dd.cd_plano   = ma.cd_plano
								AND dd.mes        = ma.mes 
								AND dd.ano        = ma.ano


						   LEFT JOIN (SELECT tp.cd_empresa,
										     tp.cd_plano,
											 TO_CHAR(COALESCE(t.dt_digita_ingresso,tp.dt_ingresso_plano),'MM')AS mes, 
											 TO_CHAR(COALESCE(t.dt_digita_ingresso,tp.dt_ingresso_plano),'YYYY') AS ano, 
											 COUNT(DISTINCT funcoes.cripto_re(tp.cd_empresa, tp.cd_registro_empregado, tp.seq_dependencia)) AS qt_digita_ingresso
										FROM public.titulares_planos tp
										JOIN public.titulares t
										  ON t.cd_empresa            = tp.cd_empresa
										 AND t.cd_registro_empregado = tp.cd_registro_empregado
										 AND t.seq_dependencia       = tp.seq_dependencia
									   WHERE tp.dt_ingresso_plano  IS NOT NULL
										 AND TO_CHAR(COALESCE(t.dt_digita_ingresso,tp.dt_ingresso_plano),'YYYY') = '".intval($args['nr_ano'])."'
										 AND DATE_TRUNC('month',tp.dt_ingresso_plano) <> DATE_TRUNC('month', COALESCE(t.dt_desligamento_eletro, CURRENT_TIMESTAMP + '1 year'))
									   GROUP BY tp.cd_empresa, tp.cd_plano, ano, mes
									   ORDER BY tp.cd_empresa, tp.cd_plano, ano, mes) AS ddi
							 ON ddi.cd_empresa = ma.cd_empresa
							AND ddi.cd_plano   = ma.cd_plano
								AND ddi.mes        = ma.mes 
								AND ddi.ano        = ma.ano

						   LEFT JOIN (SELECT tp.cd_empresa,
										     tp.cd_plano,
											 TO_CHAR(COALESCE(t.dt_digita_desligamento,tp.dt_deslig_plano),'MM')AS mes, 
											 TO_CHAR(COALESCE(t.dt_digita_desligamento,tp.dt_deslig_plano),'YYYY') AS ano, 
											 COUNT(DISTINCT funcoes.cripto_re(tp.cd_empresa, tp.cd_registro_empregado, tp.seq_dependencia)) AS qt_digita_desligamento
										FROM public.titulares_planos tp
										JOIN public.titulares t
										  ON t.cd_empresa            = tp.cd_empresa
										 AND t.cd_registro_empregado = tp.cd_registro_empregado
										 AND t.seq_dependencia       = tp.seq_dependencia
									   WHERE tp.dt_ingresso_plano  IS NOT NULL
										 AND TO_CHAR(COALESCE(t.dt_digita_desligamento,tp.dt_deslig_plano),'YYYY') = '".intval($args['nr_ano'])."'
										 AND DATE_TRUNC('month',tp.dt_ingresso_plano) <> DATE_TRUNC('month', COALESCE(t.dt_desligamento_eletro, CURRENT_TIMESTAMP + '1 year'))
									   GROUP BY tp.cd_empresa, tp.cd_plano, ano, mes
									   ORDER BY tp.cd_empresa, tp.cd_plano, ano, mes) AS ddd
							 ON ddd.cd_empresa = ma.cd_empresa
							AND ddd.cd_plano   = ma.cd_plano
								AND ddd.mes        = ma.mes 
								AND ddd.ano        = ma.ano
								
						  WHERE 1 = 1 
								".(trim($args['cd_empresa']) != "" ?  "AND ma.cd_empresa = ".intval($args['cd_empresa']) : "")."
								".(trim($args['cd_plano']) != "" ?  "AND ma.cd_plano = ".intval($args['cd_plano']) : "")."						  

						  GROUP BY ma.mes,
								   ma.ano	
								
								
						  ORDER BY ma.mes,
								   ma.ano			
			          ";
			
			#echo "<PRE style='text-align:left;'>".$qr_sql."</PRE>";
			
			$result = $this->db->query($qr_sql);
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/ri_relatorio/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }		
	}
