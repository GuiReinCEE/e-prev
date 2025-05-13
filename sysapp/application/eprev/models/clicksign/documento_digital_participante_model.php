<?php
class documento_digital_participante_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT x.cd_empresa,
						   x.cd_registro_empregado,
						   x.seq_dependencia,
						   projetos.participante_nome(x.cd_empresa,x.cd_registro_empregado,x.seq_dependencia) AS nome,
						   x.id_doc,
						   x.ds_doc,
						   TO_CHAR(x.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   x.status,
						   CASE WHEN x.status = 'RUNNING' THEN 'Em processo de assinatura'
						        WHEN x.status = 'CLOSED' THEN 'Finalizado'
								WHEN x.status = 'CANCELED' THEN 'Cancelado'
								ELSE x.status
						   END ds_status,
						   CASE WHEN x.status = 'RUNNING' THEN 'label label-warning'
						        WHEN x.status = 'CLOSED' THEN 'label label-success'
								WHEN x.status = 'CANCELED' THEN 'label label-important'
								ELSE 'label'
						   END cor_status						   
					  FROM oracle.protocolos_assinatura_docs_listar(
							'".$args['fl_status']."', 
							".(trim($args['dt_inclusao_ini']) != '' ? "TO_DATE('".trim($args['dt_inclusao_ini'])."','DD/MM/YYYY')" : "NULL").", 
							".(trim($args['dt_inclusao_fim']) != '' ? "TO_DATE('".trim($args['dt_inclusao_fim'])."','DD/MM/YYYY')" : "NULL")." 
							) x
					ORDER BY x.dt_inclusao
                  ";
				  
		#echo "<PRE>$qr_sql</PRE>";exit;
        $result = $this->db->query($qr_sql);
    }
	
   
}
?>
