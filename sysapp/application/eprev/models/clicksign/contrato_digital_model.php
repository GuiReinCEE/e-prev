<?php
class contrato_digital_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT cd.cd_contrato_digital, 
						   cd.id_doc, 
						   cd.ip,
						   cd.cd_liquid, 
						   cd.dt_notificao_app,
						   cd.cd_empresa, 
						   cd.cd_registro_empregado, 
						   cd.seq_dependencia,
						   projetos.participante_nome(cd.cd_empresa,cd.cd_registro_empregado,cd.seq_dependencia) AS nome_participante,
						   TO_CHAR(cd.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   TO_CHAR(cd.dt_limite, 'DD/MM/YYYY HH24:MI:SS') AS dt_limite, 
						   TO_CHAR(cd.dt_concluido, 'DD/MM/YYYY HH24:MI:SS') AS dt_concluido, 
						   TO_CHAR(cd.dt_cancelado, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelado, 
						   TO_CHAR(cd.dt_finalizado, 'DD/MM/YYYY HH24:MI:SS') AS dt_finalizado, 
						   (CASE WHEN cd.dt_concluido IS NOT NULL THEN 'Concluído'
						         WHEN (cd.dt_cancelado IS NOT NULL OR cd.dt_finalizado IS NOT NULL) THEN 'Cancelado/Finalizado'
						         WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'P'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'Pendente Participante'
								 WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'T1'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'Pendente Testemunha 1'	
								 WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'T2'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'Pendente Testemunha 2'	
								 WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'V'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'Pendente Validador'											  
								 ELSE 'Não identificado'
						   END) AS situacao,
						   (CASE WHEN cd.dt_concluido IS NOT NULL THEN 'label label-success' --Concluído
						         WHEN (cd.dt_cancelado IS NOT NULL OR cd.dt_finalizado IS NOT NULL) THEN 'label' --Cancelado/Finalizado
						         WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'P'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'label label-warning' --Pendente Participante
								 WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'T1'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'label label-important' --Pendente Testemunha 1
								 WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'T2'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'label label-important' --Pendente Testemunha 2
								 WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'V'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'label label-important' --Pendente Validador											  
								 ELSE 'Não identificado'
						   END) AS situacao_label						   
					  FROM clicksign.contrato_digital cd
					 WHERE 1 = 1  
					
					".(trim($args['cpf']) != '' ? "AND (cd.cd_empresa,cd.cd_registro_empregado,cd.seq_dependencia) IN (SELECT x.cd_empresa,x.cd_registro_empregado,x.seq_dependencia FROM projetos.participante_cpf('".trim($args['cpf'])."',NULL) x) " : "")."
					
					".(trim($args['cd_empresa']) != '' ? "AND cd.cd_empresa = ".intval($args['cd_empresa']) : "")."
					".(intval($args['cd_registro_empregado']) > 0 ? "AND cd.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
					".(intval($args['seq_dependencia']) > 0 ? "AND cd.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
					
					".(trim($args['nome']) != '' ? "AND UPPER(projetos.participante_nome(cd.cd_empresa,cd.cd_registro_empregado,cd.seq_dependencia)) LIKE UPPER('%".trim($args['nome'])."%')" : "")."
					
					".(trim($args['fl_pendente']) == 'S' ? "AND cd.dt_concluido IS NULL AND cd.dt_cancelado IS NULL AND cd.dt_finalizado IS NULL" : "")."												
					".(trim($args['fl_pendente']) == 'N' ? "AND (cd.dt_concluido IS NOT NULL OR cd.dt_cancelado IS NOT NULL OR cd.dt_finalizado IS NOT NULL)" : "")."												
																	
					".(trim($args['fl_pendente_participante']) == 'S' ? 
					"
						AND cd.dt_concluido IS NULL AND cd.dt_cancelado IS NULL AND cd.dt_finalizado IS NULL
						AND (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'P'
										  AND cda1.dt_assinatura IS NULL) > 0
					
					" : "")."
					".(trim($args['fl_pendente_participante']) == 'N' ? 
					"
						AND cd.dt_concluido IS NULL AND cd.dt_cancelado IS NULL AND cd.dt_finalizado IS NULL
						AND (SELECT COUNT(*)
							   FROM clicksign.contrato_digital_assinatura cda1
							  WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
							    AND cda1.tp_assinatura = 'P'
							    AND cda1.dt_assinatura IS NOT NULL) > 0
					
					" : "")."	
					
					".(trim($args['fl_concluido']) == 'S' ? "AND cd.dt_concluido IS NOT NULL" : "")."
					".(trim($args['fl_concluido']) == 'N' ? "AND cd.dt_concluido IS NULL" : "")."
					".(trim($args['fl_encerrado']) == 'S' ? "AND (cd.dt_cancelado IS NOT NULL OR cd.dt_finalizado IS NOT NULL)" : "")."
					".(trim($args['fl_encerrado']) == 'N' ? "AND (cd.dt_cancelado IS NULL AND cd.dt_finalizado IS NULL)" : "")."					
					
					".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? "AND DATE_TRUNC('day', cd.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")." 
					".(((trim($args['dt_limite_ini']) != '') AND (trim($args['dt_limite_fim']) != '')) ? "AND DATE_TRUNC('day', cd.dt_limite) BETWEEN TO_DATE('".$args['dt_limite_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_limite_fim']."', 'DD/MM/YYYY')" : "")." 
					".(((trim($args['dt_concluido_ini']) != '') AND (trim($args['dt_concluido_fim']) != '')) ? "AND DATE_TRUNC('day', cd.dt_concluido) BETWEEN TO_DATE('".$args['dt_concluido_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_concluido_fim']."', 'DD/MM/YYYY')" : "")." 
					".(((trim($args['dt_cancelado_ini']) != '') AND (trim($args['dt_cancelado_fim']) != '')) ? "AND DATE_TRUNC('day', cd.dt_cancelado) BETWEEN TO_DATE('".$args['dt_cancelado_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_cancelado_fim']."', 'DD/MM/YYYY')" : "")." 
					".(((trim($args['dt_finalizado_ini']) != '') AND (trim($args['dt_finalizado_fim']) != '')) ? "AND DATE_TRUNC('day', cd.dt_finalizado) BETWEEN TO_DATE('".$args['dt_finalizado_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_finalizadoo_fim']."', 'DD/MM/YYYY')" : "")." 
					 
					 ORDER BY cd.dt_inclusao DESC
                  ";
				  
		#echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }
	
    function carrega($cd_contrato_digital = 0)
    {
        $qr_sql = "
					SELECT cd.cd_contrato_digital, 
						   cd.id_doc, 
						   cd.ip,
						   cd.cd_liquid, 
						   cd.dt_notificao_app,
						   cd.cd_empresa, 
						   cd.cd_registro_empregado, 
						   cd.seq_dependencia,
						   projetos.participante_nome(cd.cd_empresa,cd.cd_registro_empregado,cd.seq_dependencia) AS nome_participante,
						   TO_CHAR(cd.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   TO_CHAR(cd.dt_limite, 'DD/MM/YYYY HH24:MI:SS') AS dt_limite, 
						   TO_CHAR(cd.dt_concluido, 'DD/MM/YYYY HH24:MI:SS') AS dt_concluido, 
						   TO_CHAR(cd.dt_cancelado, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelado, 
						   TO_CHAR(cd.dt_finalizado, 'DD/MM/YYYY HH24:MI:SS') AS dt_finalizado, 
						   (CASE WHEN cd.dt_concluido IS NOT NULL THEN 'Concluído'
						         WHEN (cd.dt_cancelado IS NOT NULL OR cd.dt_finalizado IS NOT NULL) THEN 'Cancelado/Finalizado'
						         WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'P'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'Pendente Participante'
								 WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'T1'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'Pendente Testemunha 1'	
								 WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'T2'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'Pendente Testemunha 2'	
								 WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'V'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'Pendente Validador'											  
								 ELSE 'Não identificado'
						   END) AS situacao,
						   (CASE WHEN cd.dt_concluido IS NOT NULL THEN 'label label-success' --Concluído
						         WHEN (cd.dt_cancelado IS NOT NULL OR cd.dt_finalizado IS NOT NULL) THEN 'label' --Cancelado/Finalizado
						         WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'P'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'label label-warning' --Pendente Participante
								 WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'T1'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'label label-important' --Pendente Testemunha 1
								 WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'T2'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'label label-important' --Pendente Testemunha 2
								 WHEN (SELECT COUNT(*)
										 FROM clicksign.contrato_digital_assinatura cda1
									    WHERE cda1.cd_contrato_digital = cd.cd_contrato_digital
										  AND cda1.tp_assinatura = 'V'
										  AND cda1.dt_assinatura IS NULL) > 0 THEN 'label label-important' --Pendente Validador											  
								 ELSE 'Não identificado'
						   END) AS situacao_label						   
					  FROM clicksign.contrato_digital cd
					 WHERE cd.cd_contrato_digital = ".intval($cd_contrato_digital)."
                  ";
				  
		#echo "<PRE>$qr_sql</PRE>";exit;
        return $this->db->query($qr_sql)->row_array();
    }

    function listarAssinadores(&$result, $args=array())
    {
        $qr_sql = "
					SELECT cda.cd_contrato_digital_assinatura, 
					       cda.cd_contrato_digital, 
						   cda.id_assinador, 
						   cda.id_assinatura, 						   
						   cda.tp_assinatura,
						   (CASE WHEN cda.tp_assinatura = 'P'  THEN 'PARTICIPANTE'
						         WHEN cda.tp_assinatura = 'T1' THEN 'TESTEMUNHA 1'
								 WHEN cda.tp_assinatura = 'T2' THEN 'TESTEMUNHA 2'
								 WHEN cda.tp_assinatura = 'V'  THEN 'VALIDADOR'
								 ELSE 'Não identificado'
						   END) AS ds_tp_assinatura,
						   (CASE WHEN cda.tp_assinatura = 'P'  THEN 1
						         WHEN cda.tp_assinatura = 'T1' THEN 2
								 WHEN cda.tp_assinatura = 'T2' THEN 3
								 WHEN cda.tp_assinatura = 'V'  THEN 4
								 ELSE 999999
						   END) AS nr_ordem,						   
						   TO_CHAR(cda.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
						   TO_CHAR(cda.dt_assinatura, 'DD/MM/YYYY HH24:MI:SS') AS dt_assinatura,
						   (CASE WHEN cda.dt_assinatura IS NOT NULL THEN 'S' ELSE 'N' END) AS fl_assinatura,
						   cda.ds_url_assinatura
					  FROM clicksign.contrato_digital_assinatura cda
					 WHERE cda.cd_contrato_digital = ".intval($args['cd_contrato_digital'])."
					 ORDER BY nr_ordem
                  ";
				  
		#echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }	
}
?>
