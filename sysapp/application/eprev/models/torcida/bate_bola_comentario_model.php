<?php
class Bate_bola_comentario_model extends Model
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

		cd_bate_bola_comentario
		, b.ds_bate_bola as descricao_bate_bola
		, c.nome
		, c.comentario
		, c.ip 
		, TO_CHAR(c.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') as dt_inclusao
		, TO_CHAR(c.dt_libera,'DD/MM/YYYY HH24:MI:SS') as dt_libera
		, u.guerra as nome_usuario_libera

		FROM 

		torcida.bate_bola b
		JOIN torcida.bate_bola_comentario c ON c.cd_bate_bola=b.cd_bate_bola
		LEFT JOIN projetos.usuarios_controledi u ON c.cd_usuario_libera=u.codigo

		WHERE 

		c.dt_exclusao IS NULL
		AND (c.cd_bate_bola={cd_bate_bola} OR {cd_bate_bola}=0)
		";

		// parse query ...
		esc( "{cd_bate_bola}", $args["cd_bate_bola"], $sql, "int" );

		// return result ...
		$result = $this->db->query($sql);
	}

	function liberar($cd, $cd_usuario_libera, &$msg=array())
	{
		if( $cd=='' ){ $msg[]='Parametro $cd obrigatório (usar MD5)!'; return false; }
		if( $cd_usuario_libera=='' ){ $msg[]='Parametro $cd_usuario_libera obrigatório!'; return false; }

		$sql="
		UPDATE torcida.bate_bola_comentario
		SET dt_libera=current_timestamp, cd_usuario_libera={cd_usuario_libera} 
		WHERE md5(cd_bate_bola_comentario::varchar) = '{cd_bate_bola_comentario}' 
		";

		esc("{cd_bate_bola_comentario}", $cd, $sql, "str", false);
		esc("{cd_usuario_libera}", $cd_usuario_libera, $sql, "int", false);

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

	function bloquear($cd, &$msg=array())
	{
		if( $cd=='' )
		{
			$msg[]='Parametro $cd obrigatório (usar MD5)!';
			return false;
		}

		$sql="
		UPDATE torcida.bate_bola_comentario 
		SET dt_libera=null, cd_usuario_libera=null 
		WHERE md5(cd_bate_bola_comentario::varchar) = '{cd_bate_bola_comentario}' 
		";

		esc("{cd_bate_bola_comentario}", $cd, $sql, "str", false);

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

	function carregar($cd)
	{
		$sql=" SELECT cd_bate_bola_comentario
			, cd_bate_bola
			, nome
			, comentario
			, TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao
			, TO_CHAR(dt_libera,'DD/MM/YYYY') as dt_libera
			, cd_usuario_libera
			, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
			, cd_usuario_exclusao
			, ip 
			FROM torcida.bate_bola_comentario ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_bate_bola_comentario={cd_bate_bola_comentario} ";
			esc( "{cd_bate_bola_comentario}", intval($cd), $sql );
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
		if(intval($args['cd_bate_bola_comentario'])==0)
		{
			$sql="
			INSERT INTO torcida.bate_bola_comentario ( nome 
			, comentario 
			) VALUES ( '{nome}' 
			, '{comentario}' 
			)
			";
		}
		else
		{
			$sql="
			UPDATE torcida.bate_bola_comentario SET 
			 cd_bate_bola_comentario = {cd_bate_bola_comentario} 
			, nome = '{nome}' 
			, comentario = '{comentario}' 
			 WHERE 
			cd_bate_bola_comentario = {cd_bate_bola_comentario} 
			";
		}

		esc("{nome}", $args["nome"], $sql, "str", FALSE);
		esc("{comentario}", $args["comentario"], $sql, "str", FALSE);
		esc("{cd_bate_bola_comentario}", $args["cd_bate_bola_comentario"], $sql, "int", FALSE);

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
		UPDATE torcida.bate_bola_comentario 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_bate_bola_comentario::varchar)='{cd_bate_bola_comentario}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_bate_bola_comentario}', $id, $sql, 'str'); 
 
		$query=$this->db->query($sql); 
	}
}
?>