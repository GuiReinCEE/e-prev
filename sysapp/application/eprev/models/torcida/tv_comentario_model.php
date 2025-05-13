<?php
class Tv_comentario_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT tvc.cd_tv_comentario
		, tv.titulo as titulo_tv
		, tvc.nome
		, tvc.comentario
		, tvc.ip 
		, TO_CHAR(tvc.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') as dt_inclusao
		, TO_CHAR(tvc.dt_libera,'DD/MM/YYYY HH24:MI:SS') as dt_libera
		, ul.guerra AS nome_usuario_libera
		FROM torcida.tv_comentario tvc
		JOIN torcida.tv tv ON tv.cd_tv=tvc.cd_tv
		LEFT JOIN projetos.usuarios_controledi ul 
		ON ul.codigo=tvc.cd_usuario_libera
		WHERE ( tvc.cd_tv={cd_tv} OR {cd_tv}=0 )
		AND tvc.dt_exclusao IS NULL
		";

		// parse query ...
		esc( "{cd_tv}", $args["cd_tv"], $sql, "int" );


		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT cd_tv_comentario
		, cd_tv
		, nome
		, comentario
		, TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao
		, TO_CHAR(dt_libera,'DD/MM/YYYY') as dt_libera
		, cd_usuario_libera
		, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
		, cd_usuario_exclusao
		, ip 
		FROM torcida.tv_comentario ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_tv_comentario={cd_tv_comentario} ";
			esc( "{cd_tv_comentario}", intval($cd), $sql );
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
		if(intval($args['cd_tv_comentario'])==0)
		{
			$sql="
			INSERT INTO torcida.tv_comentario ( nome 
			, comentario 
			) VALUES ( '{nome}' 
			, '{comentario}' 
			)
			";
		}
		else
		{
			$sql="
			UPDATE torcida.tv_comentario SET 
			 cd_tv_comentario = {cd_tv_comentario} 
			, nome = '{nome}' 
			, comentario = '{comentario}' 
			 WHERE 
			cd_tv_comentario = {cd_tv_comentario} 
			";
		}

		esc("{nome}", $args["nome"], $sql, "str", FALSE);
		esc("{comentario}", $args["comentario"], $sql, "str", FALSE);
		esc("{cd_tv_comentario}", $args["cd_tv_comentario"], $sql, "int", FALSE);

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
		UPDATE torcida.tv_comentario 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_tv_comentario::varchar)='{cd_tv_comentario}' 
		";
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_tv_comentario}', $id, $sql, 'str'); 
 
		$query=$this->db->query($sql); 
	}

	function liberar($cd,$cd_usuario_libera,&$msg=array())
	{
		if( $cd=='' ){ $msg[]='Parametro $cd obrigatório (usar MD5)!'; return false; }
		if( $cd_usuario_libera=='' ){ $msg[]='Parametro $cd_usuario_libera obrigatório!'; return false; }

		$sql="
		UPDATE torcida.tv_comentario
		SET dt_libera=current_timestamp, cd_usuario_libera={cd_usuario_libera} 
		WHERE md5(cd_tv_comentario::varchar) = '{cd_tv_comentario}' 
		";

		esc("{cd_tv_comentario}", $cd, $sql, "str", FALSE);
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
		UPDATE torcida.tv_comentario
		SET dt_libera=null, cd_usuario_libera=null 
		WHERE md5(cd_tv_comentario::varchar) = '{cd_tv_comentario}' 
		";

		esc("{cd_tv_comentario}", $cd, $sql, "str", FALSE);

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
?>