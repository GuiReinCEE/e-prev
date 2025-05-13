<?php
class Documento_participante_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					 SELECT d.cd_tipo_doc, 
					        d.cd_empresa,
							d.cd_registro_empregado,
							d.seq_dependencia,
							t.nome_documento, 
							TO_CHAR(d.dt_documento, 'DD/MM/YYYY') AS dt_documento,
							d.caminho_imagem
					   FROM public.documentos d, 
							public.tipo_documentos t
					  WHERE t.cd_tipo_doc           = d.cd_tipo_doc
						AND d.cd_empresa            = ".$args['cd_empresa']."
						AND d.cd_registro_empregado = ".$args['cd_registro_empregado']."
						AND d.seq_dependencia       = ".$args['seq_dependencia']."
						AND d.caminho_imagem        IS NOT NULL
						{PERIODO_DATA_DOCUMENTO}
						AND d.dt_documento IN (SELECT MAX(d1.dt_documento) 
													FROM documentos d1 
												   WHERE d1.cd_registro_empregado = d.cd_registro_empregado 
													 AND d1.cd_empresa            = d.cd_empresa 
													 AND d1.seq_dependencia       = d.seq_dependencia 
													 AND d1.cd_tipo_doc           = d.cd_tipo_doc)	
					  ORDER BY d.dt_documento DESC  		
		       ";

		if((trim($args["dt_documento_ini"]) != "") and (trim($args["dt_documento_fim"]) != ""))
		{
			$periodo = "AND DATE_TRUNC('day', d.dt_documento) BETWEEN TO_DATE('{dt_ini}','DD/MM/YYYY') AND TO_DATE('{dt_fim}','DD/MM/YYYY')";
			$periodo = str_replace("{dt_ini}", $args["dt_documento_ini"],$periodo);
			$periodo = str_replace("{dt_fim}", $args["dt_documento_fim"],$periodo);
			$qr_sql = str_replace("{PERIODO_DATA_DOCUMENTO}", $periodo, $qr_sql);
		}
		else
		{
			$qr_sql = str_replace("{PERIODO_DATA_DOCUMENTO}", "", $qr_sql);
		}

		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}
}
?>