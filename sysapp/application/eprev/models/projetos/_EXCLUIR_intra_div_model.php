<?php
class Intra_div_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT id.cd_item,
					       id.div,
			               id.titulo,
			               TO_CHAR(id.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   (SELECT COUNT(*) 
						      FROM projetos.intra_div id1
							 WHERE id1.div                     = id.div
					           AND id1.dt_exclusao             IS NULL
					           AND COALESCE(id1.cd_item_pai,0) = id.cd_item) AS qt_subitem
		              FROM projetos.intra_div id
 		             WHERE id.div                     = '{div}'
					   AND id.dt_exclusao             IS NULL
					   AND COALESCE(id.cd_item_pai,0) = 0
					 ORDER BY id.titulo
		          ";

		esc('{div}',$args['div'],$qr_sql);
		$result = $this->db->query($qr_sql);
	}
	
	function listarSubitem( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT id.cd_item,
			               id.div,
			               id.titulo,
			               TO_CHAR(id.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao
		              FROM projetos.intra_div id
 		             WHERE id.div                     = '{div}'
					   AND id.dt_exclusao             IS NULL
					   AND COALESCE(id.cd_item_pai,0) = {cd_item_pai}
					 ORDER BY id.titulo
		          ";

		esc('{cd_item_pai}',$args['cd_item_pai'],$qr_sql);
		esc('{div}',$args['div'],$qr_sql);
		
		#echo "<PRE>".$qr_sql."</PRE>";
		
		$result = $this->db->query($qr_sql);
	}	
}
?>