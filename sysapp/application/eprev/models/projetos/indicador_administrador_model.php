<?php
class Indicador_administrador_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = " 
					SELECT a.cd_indicador_administrador,
		                   u.nome,
						   u.divisao
		             FROM indicador.indicador_administrador a
		             JOIN projetos.usuarios_controledi u 
					   ON a.cd_usuario = u.codigo
		            WHERE ds_tipo     = 'RESPONSAVEL'
		              AND dt_exclusao IS NULL 
				  ";
		$result = $this->db->query($qr_sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT cd_indicador_administrador
		, cd_usuario
		, ds_tipo 
		, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
		, cd_usuario_exclusao
		FROM indicador.indicador_administrador ";

		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_indicador_administrador={cd_indicador_administrador} ";
			esc( "{cd_indicador_administrador}", intval($cd), $sql );
			$query=$this->db->query($sql);

			if($query->row_array())
			{
				$row=$query->row_array();
			}
		}

		return $row;
	}

	function salvar(&$result, $args=array())
	{		
		if(intval($args['cd_indicador_administrador']) == 0)
		{
            $cd_indicador_administrador = intval($this->db->get_new_id("indicador.indicador_administrador", "cd_indicador_administrador"));
            
			$qr_sql = "
						INSERT INTO indicador.indicador_administrador
							 (
							   cd_indicador_administrador,
							   cd_usuario
							 )
						VALUES 
							 (
							   ".intval($cd_indicador_administrador).",
							   ".intval($args['cd_usuario'])."
							 );
					  ";		
		}
		else
		{
            $cd_indicador_administrador = intval($args['cd_indicador_administrador']);
            
			$qr_sql = "
						UPDATE indicador.indicador_administrador_grupo
						   SET dt_exclusao = CURRENT_TIMESTAMP,
						       cd_usuario_exclusao = ".intval($args['cd_usuario_salvar'])."
						 WHERE cd_indicador_administrador = ".intval($cd_indicador_administrador)."
						   AND cd_indicador_grupo NOT IN (".implode(",",$args["ar_grupo"]).");

						INSERT INTO indicador.indicador_administrador_grupo(cd_indicador_administrador, cd_indicador_grupo, cd_usuario_inclusao)
						SELECT ".intval($cd_indicador_administrador)."::INTEGER, 
						       ig.cd_indicador_grupo, 
						       ".intval($args['cd_usuario_salvar'])."::INTEGER
						  FROM indicador.indicador_grupo ig
						  LEFT JOIN indicador.indicador_administrador_grupo iag
						    ON iag.cd_indicador_grupo = ig.cd_indicador_grupo
						   AND iag.dt_exclusao IS NULL
						   AND iag.cd_indicador_administrador = ".intval($cd_indicador_administrador)."
						 WHERE ig.dt_exclusao IS NULL
						   AND ig.cd_indicador_grupo IN (".implode(",",$args["ar_grupo"]).")
						   AND iag.cd_indicador_administrador_grupo IS NULL;					  
                      ";
		}
		
		#echo "<PRE>".$qr_sql."</PRE>"; exit;
		
        $result = $this->db->query($qr_sql);
        
        return $cd_indicador_administrador;
	}	

    function excluir(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE indicador.indicador_administrador
					   SET dt_exclusao         = CURRENT_TIMESTAMP,
						   cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao'])."
					 WHERE cd_indicador_administrador = ".intval($args['cd_indicador_administrador']).";
					 
					UPDATE indicador.indicador_administrador_grupo
					   SET dt_exclusao         = CURRENT_TIMESTAMP,
						   cd_usuario_exclusao = ".intval($args['cd_usuario_exclusao'])."
					 WHERE cd_indicador_administrador = ".intval($args['cd_indicador_administrador']).";			 
			      ";
        $result = $this->db->query($qr_sql);
    }	
	
	function indicador_grupo(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_indicador_grupo AS value,
                           ds_indicador_grupo AS text
                      FROM indicador.indicador_grupo
                     WHERE dt_exclusao IS NULL
		             ORDER BY text
			      ";
		$result = $this->db->query($qr_sql);
	}	
	
	function administrador_indicador_grupo(&$result, $args=array())
	{
		$qr_sql = "
					SELECT ig.cd_indicador_grupo,
					       ig.ds_indicador_grupo
                      FROM indicador.indicador_grupo ig
					  JOIN indicador.indicador_administrador_grupo iag
					    ON iag.cd_indicador_grupo = ig.cd_indicador_grupo
					   AND iag.dt_exclusao IS NULL
				      JOIN indicador.indicador_administrador ia
					    ON ia.cd_indicador_administrador = iag.cd_indicador_administrador
					   AND ia.dt_exclusao IS NULL
                     WHERE ig.dt_exclusao IS NULL
                       AND ia.cd_indicador_administrador = ".intval($args["cd_indicador_administrador"])."
			      ";
		$result = $this->db->query($qr_sql);
	}	
}
?>