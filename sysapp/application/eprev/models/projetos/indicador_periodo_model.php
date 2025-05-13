<?php
class Indicador_periodo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT cd_indicador_periodo
, ds_periodo
, TO_CHAR(dt_inicio,'DD/MM/YYYY') as dt_inicio
, TO_CHAR(dt_fim,'DD/MM/YYYY') as dt_fim
FROM indicador.indicador_periodo 
		";

		// parse query ...
		

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT cd_indicador_periodo
, ds_periodo
, TO_CHAR(dt_inicio,'DD/MM/YYYY') as dt_inicio
, TO_CHAR(dt_fim,'DD/MM/YYYY') as dt_fim
, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
, cd_usuario_exclusao 
FROM indicador.indicador_periodo  ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_indicador_periodo={cd_indicador_periodo} ";
			esc( "{cd_indicador_periodo}", intval($cd), $sql );
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
		if(intval($args['cd_indicador_periodo'])==0)
		{
			$sql="
			INSERT INTO indicador.indicador_periodo ( ds_periodo 
, dt_inicio 
, dt_fim 
) VALUES ( '{ds_periodo}' 
, TO_DATE('{dt_inicio}', 'DD/MM/YYYY') 
, TO_DATE('{dt_fim}', 'DD/MM/YYYY') 
)

			";
		}
		else
		{
			$sql="
			UPDATE indicador.indicador_periodo SET 
 ds_periodo = '{ds_periodo}' 
, dt_inicio = TO_DATE('{dt_inicio}', 'DD/MM/YYYY') 
, dt_fim = TO_DATE('{dt_fim}', 'DD/MM/YYYY') 
 WHERE 
cd_indicador_periodo = {cd_indicador_periodo} 
			";
		}

		esc("{ds_periodo}", $args["ds_periodo"], $sql, "str", FALSE);
		esc("{dt_inicio}", $args["dt_inicio"], $sql, "str", FALSE);
		esc("{dt_fim}", $args["dt_fim"], $sql, "str", FALSE);
		esc("{cd_indicador_periodo}", $args["cd_indicador_periodo"], $sql, "int", FALSE);

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
		UPDATE indicador.indicador_periodo 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_indicador_periodo::varchar)='{cd_indicador_periodo}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_indicador_periodo}', $id, $sql, 'str'); 
 
		$query=$this->db->query($sql); 

	}
}
?>