<?php
class solucao_suporte_model extends Model
{
    function __construct()
	{
		parent::Model();
	}

	function categoria(&$result, $args=array())
    {
        $qr_sql = "
            SELECT codigo AS value, 
			       descricao AS text 
		      FROM public.listas 
			 WHERE categoria = 'SOLU' 
			 ORDER BY descricao";
			 
        $result = $this->db->query($qr_sql);
    }
	
	function listar( &$result, $args=array() )
	{				
		$qr_sql = "
			SELECT a.numero,
				   TO_CHAR(a.dt_cad, 'DD/MM/YYYY') AS dt_cad,
				   TO_CHAR(a.dt_fim_real, 'DD/MM/YYYY') AS dt_fim_real,
				   a.descricao AS atividade,
				   a.solucao,
				   c.descricao AS categoria,
				   b.ds_assunto AS assunto
			  FROM projetos.atividades a
			  JOIN projetos.atividade_solucao b 
			    ON a.numero = b.cd_atividade
			  JOIN public.listas c 
			    ON c.codigo=b.cd_categoria
			 WHERE area = 'GI'
			   AND UPPER(a.descricao) LIKE UPPER('%".trim(utf8_decode($args["descricao"]))."%')
			   AND UPPER(a.solucao) LIKE UPPER('%".trim(utf8_decode($args["solucao"]))."%')
			   AND UPPER(b.ds_assunto) LIKE UPPER('%".trim(utf8_decode($args["assunto"]))."%')
			   ".(trim($args['numero']) != '' ? " AND a.numero = ".intval($args['numero']): "")."
			   ".(trim($args['cd_categoria']) != '' ? " AND b.cd_categoria = '".trim($args['cd_categoria'])."'" : "")."
			   ".(((trim($args['dt_cadastro_ini']) != "") AND (trim($args['dt_cadastro_fim']) != "")) ? " AND DATE_TRUNC('day', a.dt_cad) BETWEEN TO_DATE('".$args['dt_cadastro_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_cadastro_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_conclusao_ini']) != "") AND (trim($args['dt_conclusao_fim']) != "")) ? " AND DATE_TRUNC('day', a.dt_fim_real) BETWEEN TO_DATE('".$args['dt_conclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_conclusao_fim']."', 'DD/MM/YYYY')" : "")."
	      	ORDER BY numero ASC;";

		$result = $this->db->query($qr_sql);
	}
}

?>