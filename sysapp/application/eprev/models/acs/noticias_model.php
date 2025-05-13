<?php
class Noticias_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT n.codigo, 
				   TO_CHAR(n.data, 'DD/MM/YYYY HH24:MI:SS') AS data, 
				   n.titulo, 
				   n.editorial,
				   ne.ds_noticia_editorial
			  FROM acs.noticias n
			  JOIN acs.noticia_editorial ne
			    ON ne.id_noticia_editorial = n.editorial
			 WHERE n.dt_exclusao IS NULL
			   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND CAST(n.data AS DATE) BETWEEN TO_DATE('".$args["dt_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_fim"]."','DD/MM/YYYY') " : "")."
			   ".(trim($args['id_noticia_editorial']) != '' ? "AND n.editorial = '".trim($args["id_noticia_editorial"])."'" : "").";";
			   
		$result = $this->db->query($qr_sql);
	}

	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE acs.noticias 
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
				   cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE codigo = ".intval($args['codigo']).";";
		
		$result = $this->db->query($qr_sql);
	}
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT codigo, 
				   TO_CHAR(data, 'DD/MM/YYYY HH24:MI:SS') AS data, 
				   descricao,
				   titulo, 
				   editorial,
				   fl_formato,
				   TO_CHAR(data,'DD') AS dia,
				   TO_CHAR(data,'MM') AS mes,
				   TO_CHAR(data,'YYYY') AS ano			   
			  FROM acs.noticias 
			 WHERE dt_exclusao IS NULL
			   AND codigo = ".intval($args['codigo']).";";

		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
    {
        if(intval($args['cd_noticia']) > 0)
        {
            $cd_noticia = intval($args['cd_noticia']);
			
			$qr_sql = "
				UPDATE acs.noticias 
				   SET titulo               = ".(trim($args['titulo']) == "" ? "DEFAULT" : str_escape($args['titulo'])).",
					   descricao            = ".(trim($args['descricao']) == "" ? "DEFAULT" : str_escape($args['descricao'])).",
					   editorial            = ".(trim($args['id_noticia_editorial']) == "" ? "DEFAULT" : str_escape($args['id_noticia_editorial'])).",
					   fl_formato           = ".(trim($args['fl_formato']) == "" ? "DEFAULT" : str_escape($args['fl_formato'])).",
					   ordem                = ".(trim($args['ordem']) == "" ? "DEFAULT" : intval($args['ordem'])).",
					   dt_alteracao         = CURRENT_TIMESTAMP,
					   cd_usuario_alteracao = ".intval($args['cd_usuario'])."
				 WHERE codigo = ".intval($cd_noticia).";";
            
            $this->db->query($qr_sql);
        }
        else
        {
            $cd_noticia = intval($this->db->get_new_id("acs.noticias", "codigo"));
			
			$qr_sql = "
				INSERT INTO acs.noticias 
					 ( 
					   codigo,
					   data,
					   titulo, 
					   descricao, 
					   editorial,
					   fl_formato,
					   ordem,
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 ) 
				VALUES 
					 ( 
					   ".intval($cd_noticia).",
					   CURRENT_TIMESTAMP,
					   ".(trim($args['titulo']) == "" ? "DEFAULT" : str_escape($args['titulo'])).",
					   ".(trim($args['descricao']) == "" ? "DEFAULT" : str_escape($args['descricao'])).",
					   ".(trim($args['id_noticia_editorial']) == "" ? "DEFAULT" : str_escape($args['id_noticia_editorial'])).",
					   ".(trim($args['fl_formato']) == "" ? "DEFAULT" : str_escape($args['fl_formato'])).",
					   ".(trim($args['ordem']) == "" ? "DEFAULT" : intval($args['ordem'])).",
					   ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario'])).",
					   ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : intval($args['cd_usuario']))."
					 );";
        }
        
        $this->db->query($qr_sql);
		
		return intval($cd_noticia);
    }
	
	function listar_resumo(&$result, $args=array())
	{
		#### ATENCAO USADO NOS INDICADORES ####
		$qr_sql = "
			SELECT TO_CHAR(x.dia, 'YYYY/MM') AS ano_mes,
				   TO_CHAR(x.dia, 'YYYY_MM') AS ano_mes_percent,
				   COUNT(x.dia) AS qt_dia_mes,
				   SUM(CASE WHEN (SELECT COUNT(*)
									FROM acs.noticias n
								   WHERE n.dt_exclusao IS NULL
									 AND funcoes.timestamp_to_date(n.data) = funcoes.timestamp_to_date(x.dia)) = 0 THEN 1
							ELSE 0
					   END) AS qt_dia_sem
			  FROM (SELECT generate_series AS dia
					  FROM generate_series(TO_DATE('01/01/".trim($args['nr_ano'])."','DD/MM/YYYY'), TO_DATE('31/12/".trim($args['nr_ano'])."','DD/MM/YYYY'), '1 day')) x
			 WHERE funcoes.fnc_feriado(x.dia::date, 'EMP') = FALSE
			   AND extract(DOW FROM  x.dia) NOT IN (0,6)
			   
			 ".(intval($args["nr_ano"]) > 0 ? "AND TO_CHAR(x.dia,'YYYY') = '".intval($args["nr_ano"])."'" : "")."
			 ".(intval($args["nr_mes"]) > 0 ? "AND TO_CHAR(x.dia,'MM')   = '".trim($args["nr_mes"])."'" : "")."
			 
			 GROUP BY ano_mes, ano_mes_percent
			 ORDER BY ano_mes, ano_mes_percent;";

		$result = $this->db->query($qr_sql);
	}
	
	function editorial(&$result, $args=array())
	{
		$qr_sql = "
			SELECT id_noticia_editorial AS value,
				   ds_noticia_editorial AS text
			  FROM acs.noticia_editorial
			  ".($args['fl_exclusao'] ? "WHERE dt_exclusao IS NULL" : "")."
			 ORDER BY ds_noticia_editorial;";
			  
		$result = $this->db->query($qr_sql);
	}
}
?>