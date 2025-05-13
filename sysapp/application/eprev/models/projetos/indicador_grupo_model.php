<?php
class Indicador_grupo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT 
cd_indicador_grupo
, ds_indicador_grupo
, ds_missao
FROM indicador.indicador_grupo
WHERE dt_exclusao IS NULL
		";

		// parse query ...
		

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT 
cd_indicador_grupo
, ds_indicador_grupo
, ds_missao
, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
, cd_usuario_exclusao 
FROM indicador.indicador_grupo  ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_indicador_grupo={cd_indicador_grupo} ";
			esc( "{cd_indicador_grupo}", intval($cd), $sql );
			$query=$this->db->query($sql);

			if($query->row_array())
			{
				$row=$query->row_array();
			}
		}

		return $row;
	}

	function salvar($args,&$msg=array())
	{
		if(intval($args['cd_indicador_grupo'])==0)
		{
			$sql=" INSERT INTO indicador.indicador_grupo ( ds_indicador_grupo , ds_missao ) VALUES ( '{ds_indicador_grupo}' , '{ds_missao}' ) ";
		}
		else
		{
			$sql="
				UPDATE indicador.indicador_grupo
				SET ds_indicador_grupo = '{ds_indicador_grupo}', ds_missao='{ds_missao}'
				WHERE cd_indicador_grupo = {cd_indicador_grupo}
			";
		}

		esc("{ds_indicador_grupo}", $args["ds_indicador_grupo"], $sql, "str", FALSE);
		esc("{ds_missao}", $args["ds_missao"], $sql, "str", FALSE);
		esc("{cd_indicador_grupo}", $args["cd_indicador_grupo"], $sql, "int", FALSE);

		try
		{
			$query = $this->db->query($sql);
			return true;
		}
		catch(Exception $e)
		{
			$msg[]=$e->getMessage();
			return false;
		}
	}

	function excluir($id)
	{
		$sql = " 
		UPDATE indicador.indicador_grupo 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_indicador_grupo::varchar)='{cd_indicador_grupo}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_indicador_grupo}', $id, $sql, 'str'); 

		$query=$this->db->query($sql); 

	}
}
?>