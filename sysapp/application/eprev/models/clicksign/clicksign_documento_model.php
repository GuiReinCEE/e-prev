<?php
class clicksign_documento_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listarAdmin(&$result, $args=array())
    {
		$qr_sql = "
					SELECT d.cd_documento,
						   d.id_doc,
						   d.fl_status,
						   d.tp_evento,
						   d.cd_usuario,
						   d.cd_area,
						   d.fl_area_monitorar,
						   funcoes.get_usuario_nome(d.cd_usuario) AS nome,
						   TO_CHAR(d.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   TO_CHAR(d.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
						   CASE WHEN TRIM(COALESCE(d.id_doc,'')) = '' 
								THEN 'ERRO'
						        WHEN TRIM(COALESCE(d.json_doc,'')) = '' 
								THEN 'ERRO'
								ELSE convert_from(convert_to(((COALESCE(d.json_doc,''))::JSONB)->'document'->>'path', 'UTF8'), 'LATIN1')
						   END AS documento,
 						   CASE WHEN d.fl_status = 'RUNNING' THEN 'EM PROCESSO DE ASSINATURA'
						        WHEN d.fl_status = 'CLOSED' THEN 'FINALIZADO'
								WHEN d.fl_status = 'CANCELED' THEN 'CANCELADO'
								ELSE d.fl_status
						   END ds_status,
						   CASE WHEN d.fl_status = 'RUNNING' THEN 'label label-warning'
						        WHEN d.fl_status = 'CLOSED' THEN 'label label-success'
								WHEN d.fl_status = 'CANCELED' THEN 'label label-important'
								ELSE 'label'
						   END cor_status,
						   CASE WHEN d.fl_area_monitorar = 'S' THEN 'SIM'
								ELSE 'NAO'
						   END ds_area_monitorar,
						   CASE WHEN d.fl_area_monitorar = 'S' THEN 'label label-info'
								ELSE 'label'
						   END cor_area_monitorar,
					       d.cd_empresa,
					       d.cd_registro_empregado,
					       d.seq_dependencia,
					       d.cd_tipo_documento,
					       d.cd_protocolo_interno,
						   funcoes.nr_documento_recebido(dr.nr_ano, dr.nr_contador) AS nr_documento_recebido
					  FROM clicksign.documento d
					  LEFT JOIN projetos.documento_recebido dr
					    ON dr.cd_documento_recebido = d.cd_protocolo_interno
					 WHERE 1 = 1 
					 --and coalesce(d.cd_usuario,0) <= 0 
					 --and convert_from(convert_to(((d.json_doc)::JSONB)->'document'->>'path', 'UTF8'), 'LATIN1') not like '/VENDAS/%'
					   ".((trim($args['cd_usuario_documento_gerencia']) != "") ? "AND d.cd_area = '".trim($args['cd_usuario_documento_gerencia'])."'" : "")."
					   ".((intval($args['cd_usuario_documento']) > 0) ? "AND d.cd_usuario = ".intval($args['cd_usuario_documento']) : "")."
					   ".((trim($args['fl_status']) != "") ? "AND d.fl_status = '".trim($args['fl_status'])."'" : "")."
					   ".((trim($args['id_doc']) != "") ? "AND d.id_doc = '".trim($args['id_doc'])."'" : "")."
					   ".(((trim($args['dt_inclusao_fim']) != "") and  (trim($args['dt_inclusao_fim']) != "")) ? " AND CAST(d.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
					 ORDER BY d.cd_documento DESC;
                  ";		
		
		#echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }
	
    function listar(&$result, $args=array())
    {
		$qr_sql = "
					SELECT d.cd_documento,
						   d.id_doc,
						   d.fl_status,
						   d.tp_evento,
						   d.cd_usuario,
						   d.cd_area,
						   d.fl_area_monitorar,
						   funcoes.get_usuario_nome(d.cd_usuario) AS nome,
						   TO_CHAR(d.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   TO_CHAR(d.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
						   CASE WHEN TRIM(COALESCE(d.id_doc,'')) = '' 
								THEN 'ERRO'
						        WHEN TRIM(COALESCE(d.json_doc,'')) = '' 
								THEN 'ERRO'
								ELSE convert_from(convert_to(((COALESCE(d.json_doc,''))::JSONB)->'document'->>'path', 'UTF8'), 'LATIN1')
						   END AS documento,
 						   CASE WHEN d.fl_status = 'RUNNING' THEN 'EM PROCESSO DE ASSINATURA'
						        WHEN d.fl_status = 'CLOSED' THEN 'FINALIZADO'
								WHEN d.fl_status = 'CANCELED' THEN 'CANCELADO'
								ELSE d.fl_status
						   END ds_status,
						   CASE WHEN d.fl_status = 'RUNNING' THEN 'label label-warning'
						        WHEN d.fl_status = 'CLOSED' THEN 'label label-success'
								WHEN d.fl_status = 'CANCELED' THEN 'label label-important'
								ELSE 'label'
						   END cor_status,
						   CASE WHEN d.fl_area_monitorar = 'S' THEN 'SIM'
								ELSE 'NAO'
						   END ds_area_monitorar,
						   CASE WHEN d.fl_area_monitorar = 'S' THEN 'label label-info'
								ELSE 'label'
						   END cor_area_monitorar,
					       d.cd_empresa,
					       d.cd_registro_empregado,
					       d.seq_dependencia,
					       d.cd_tipo_documento,
					       d.cd_protocolo_interno,
						   funcoes.nr_documento_recebido(dr.nr_ano, dr.nr_contador) AS nr_documento_recebido
					  FROM clicksign.documento d
					  LEFT JOIN projetos.documento_recebido dr
					    ON dr.cd_documento_recebido = d.cd_protocolo_interno
					 WHERE
					    (
							(funcoes.get_usuario_gerente(".intval($args['cd_usuario_documento']).") = ".intval($args['cd_usuario_documento'])." AND d.cd_area = '".trim($args['cd_usuario_documento_gerencia'])."') --GERENTE
							OR
		

							(
								(
									".intval($args['cd_usuario_documento'])." IN (SELECT su.cd_usuario FROM funcoes.get_usuario_supervisor(".intval($args['cd_usuario_documento']).") su) 
									AND 
									COALESCE(funcoes.get_usuario_unidade(d.cd_usuario), d.cd_area) IN (SELECT funcoes.get_unidade_usuario_supervisor(".intval($args['cd_usuario_documento'])."))
							 	)
							 	OR
							 	(
							 		d.cd_gerencia_unidade IN (SELECT funcoes.get_unidade_usuario_supervisor(".intval($args['cd_usuario_documento'])."))
							 	)

							) --SUPERVISOR

							OR
							(
								d.cd_usuario = ".intval($args['cd_usuario_documento'])." 
								OR 
								(
									CASE WHEN COALESCE(funcoes.get_usuario_unidade(".intval($args['cd_usuario_documento'])."), '') <> '' AND d.cd_area NOT IN ('GNR')
										 THEN (d.fl_area_monitorar = 'S' 
										       AND d.cd_area = '".trim($args['cd_usuario_documento_gerencia'])."' 
										       AND funcoes.get_usuario_unidade(d.cd_usuario) = funcoes.get_usuario_unidade(".intval($args['cd_usuario_documento']).")) 
										 ELSE (d.fl_area_monitorar = 'S' AND d.cd_area = '".trim($args['cd_usuario_documento_gerencia'])."') 
									END
								)
								OR
								(
									d.fl_area_monitorar = 'S' AND d.cd_gerencia_unidade = funcoes.get_usuario_unidade(".intval($args['cd_usuario_documento']).")
								)
							) --USUARIO
					    )
					   
					   ".((trim($args['fl_status']) != "") ? "AND d.fl_status = '".trim($args['fl_status'])."'" : "")."
					   ".(((trim($args['dt_inclusao_fim']) != "") and  (trim($args['dt_inclusao_fim']) != "")) ? " AND CAST(d.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
					 ORDER BY d.cd_documento DESC;
                  ";		
		/*
							(".intval($args['cd_usuario_documento'])." IN (SELECT su.cd_usuario FROM funcoes.get_usuario_supervisor(".intval($args['cd_usuario_documento']).") su) AND d.cd_area = '".trim($args['cd_usuario_documento_gerencia'])."') --SUPERVISOR			   
		*/
		
		#echo "<PRE>$qr_sql</PRE>";exit;

        $result = $this->db->query($qr_sql);
    }	
	
	public function salvarProtocoloInterno($args = array())
    {
        $qr_sql = "
					UPDATE clicksign.documento
					   SET cd_protocolo_interno = ".(intval($args['cd_documento_recebido']) > 0 ? intval($args['cd_documento_recebido']) : "NULL")."
					 WHERE id_doc = '".trim($args['id_doc'])."'
				  ";
		
        $this->db->query($qr_sql);
		
		#echo $qr_sql; exit;
    }	
}
?>
