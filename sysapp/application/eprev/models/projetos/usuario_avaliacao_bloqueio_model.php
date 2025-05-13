<?php
class Usuario_avaliacao_bloqueio_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT uab.cd_usuario_avaliacao_bloqueio, u.nome, u.usuario, to_char(uab.dt_bloqueio,'DD/MM/YYYY HH24:MI') AS dt_bloqueio
FROM projetos.usuario_avaliacao_bloqueio uab
JOIN projetos.usuarios_controledi u on u.codigo=uab.cd_usuario
WHERE dt_exclusao IS NULL
		";

		// parse query ...
		

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT cd_usuario_avaliacao_bloqueio
, cd_usuario
, TO_CHAR(dt_bloqueio,'DD/MM/YYYY') as dt_bloqueio
, cd_usuario_inclusao
, TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao
, cd_usuario_exclusao
, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao 
FROM projetos.usuario_avaliacao_bloqueio  ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_usuario_avaliacao_bloqueio={cd_usuario_avaliacao_bloqueio} ";
			esc( "{cd_usuario_avaliacao_bloqueio}", intval($cd), $sql );
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
		if(intval($args['cd_usuario_avaliacao_bloqueio'])==0)
		{
			$sql="
			INSERT INTO projetos.usuario_avaliacao_bloqueio

( cd_usuario, dt_bloqueio, dt_inclusao, cd_usuario_inclusao)

VALUES

( {cd_usuario}, current_timestamp, current_timestamp, {cd_usuario_inclusao} )

			";
		}
		else
		{
			$sql="
			UPDATE projetos.usuario_avaliacao_bloqueio
SET cd_usuario_avaliacao_bloqueio = {cd_usuario_avaliacao_bloqueio} 
, cd_usuario = {cd_usuario} 
, dt_bloqueio = current_timestamp
WHERE cd_usuario_avaliacao_bloqueio = {cd_usuario_avaliacao_bloqueio}
			";
		}

		esc("{cd_usuario}", $args["cd_usuario"], $sql, "int", false);

esc("{cd_usuario_avaliacao_bloqueio}", $args["cd_usuario_avaliacao_bloqueio"], $sql, "int", false);

esc( "{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", false );

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
		UPDATE projetos.usuario_avaliacao_bloqueio 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_usuario_avaliacao_bloqueio::varchar)='{cd_usuario_avaliacao_bloqueio}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_usuario_avaliacao_bloqueio}', $id, $sql, 'str'); 
 
		$query=$this->db->query($sql); 

	}
}
?>