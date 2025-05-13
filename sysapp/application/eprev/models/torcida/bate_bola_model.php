<?php
class Bate_bola_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT b.cd_bate_bola
		, b.ds_bate_bola
		, TO_CHAR(b.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') as dt_inclusao
		, b.cd_usuario_inclusao
		, TO_CHAR(b.dt_libera,'DD/MM/YYYY HH24:MI:SS') as dt_libera
		, b.cd_usuario_libera
		, u.guerra as nome_usuario_libera
		FROM torcida.bate_bola b
		LEFT JOIN projetos.usuarios_controledi u
		ON b.cd_usuario_libera=u.codigo
		WHERE dt_exclusao IS NULL
		";

		// parse query ...

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT cd_bate_bola
		, ds_bate_bola
		, TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao
		, cd_usuario_inclusao
		, TO_CHAR(dt_libera,'DD/MM/YYYY') as dt_libera
		, cd_usuario_libera
		, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
		, cd_usuario_exclusao 
		FROM torcida.bate_bola ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_bate_bola={cd_bate_bola} ";
			esc( "{cd_bate_bola}", intval($cd), $sql );
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
		if(intval($args['cd_bate_bola'])==0)
		{
			$sql="INSERT INTO torcida.bate_bola (ds_bate_bola,dt_inclusao,cd_usuario_inclusao) 
			VALUES ('{ds_bate_bola}',current_timestamp,{cd_usuario_inclusao})
			";
		}
		else
		{
			$sql="
			UPDATE torcida.bate_bola SET 
			 cd_bate_bola = {cd_bate_bola} 
			, ds_bate_bola = '{ds_bate_bola}' 
			 WHERE 
			cd_bate_bola = {cd_bate_bola} 
			";
		}

		esc("{ds_bate_bola}", $args["ds_bate_bola"], $sql, "str", FALSE);
		esc("{cd_bate_bola}", $args["cd_bate_bola"], $sql, "int", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);

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
		UPDATE torcida.bate_bola 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_bate_bola::varchar)='{cd_bate_bola}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_bate_bola}', $id, $sql, 'str'); 
 
		$query=$this->db->query($sql); 

	}

	function liberar($cd,$cd_usuario_libera,&$msg=array())
	{
		if( $cd=='' ){ $msg[]='Parametro $cd obrigatório (usar MD5)!'; return false; }
		if( $cd_usuario_libera=='' ){ $msg[]='Parametro $cd_usuario_libera obrigatório!'; return false; }

		$sql="
		UPDATE torcida.bate_bola 
		SET dt_libera=current_timestamp, cd_usuario_libera={cd_usuario_libera} 
		WHERE md5(cd_bate_bola::varchar) = '{cd_bate_bola}' 
		";

		esc("{cd_bate_bola}", $cd, $sql, "str", FALSE);
		esc("{cd_usuario_libera}", $cd_usuario_libera, $sql, "int", FALSE);

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
	
	function bloquear($cd,&$msg=array())
	{
		if( $cd=='' ){ $msg[]='Parametro $cd obrigatório (usar MD5)!'; return false; }

		$sql="
		UPDATE torcida.bate_bola 
		SET dt_libera=null, cd_usuario_libera=null 
		WHERE md5(cd_bate_bola::varchar) = '{cd_bate_bola}' 
		";

		esc("{cd_bate_bola}", $cd, $sql, "str", FALSE);

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
}