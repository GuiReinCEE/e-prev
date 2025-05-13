<?php
class Precavida_imagem_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		// mount query
		$sql = "
		SELECT cd_precavida_imagem
		, imagem
		, x1
		, y1
		, x2
		, y2
		, TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') as dt_inclusao
		, cd_usuario_inclusao
		, TO_CHAR(dt_libera,'DD/MM/YYYY HH24:MI:SS') as dt_libera
		, cd_usuario_libera
		, ul.guerra as nome_usuario_libera
		, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
		, cd_usuario_exclusao 
		FROM torcida.precavida_imagem pi
		LEFT JOIN projetos.usuarios_controledi ul ON ul.codigo=pi.cd_usuario_libera
		WHERE pi.dt_exclusao IS null
		";

		// parse query ...
		

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT cd_precavida_imagem
, imagem
, x1
, y1
, x2
, y2
, TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') as dt_inclusao
, cd_usuario_inclusao
, TO_CHAR(dt_libera,'DD/MM/YYYY HH24:MI:SS') as dt_libera
, cd_usuario_libera
, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
, cd_usuario_exclusao 
FROM torcida.precavida_imagem pi ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_precavida_imagem={cd_precavida_imagem} ";
			esc( "{cd_precavida_imagem}", intval($cd), $sql );
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
		if(intval($args['cd_precavida_imagem'])==0)
		{
			$sql="
			INSERT INTO torcida.precavida_imagem ( imagem 
			, x1 
			, y1 
			, x2 
			, y2 
			, dt_inclusao 
			, cd_usuario_inclusao 
			) VALUES ( '{imagem}' 
			, {x1} 
			, {y1} 
			, {x2} 
			, {y2} 
			, CURRENT_TIMESTAMP 
			, {cd_usuario_inclusao} 
			)
			";
		}
		else
		{
			$sql="
			UPDATE torcida.precavida_imagem SET 
			 cd_precavida_imagem = {cd_precavida_imagem} 
			, imagem = '{imagem}' 
			, x1 = {x1} 
			, y1 = {y1} 
			, x2 = {x2} 
			, y2 = {y2} 
			 WHERE 
			cd_precavida_imagem = {cd_precavida_imagem} 
			";
		}

		esc("{imagem}", $args["imagem"], $sql, "str", FALSE);
		esc("{x1}", $args["x1"], $sql, "int", FALSE);
		esc("{y1}", $args["y1"], $sql, "int", FALSE);
		esc("{x2}", $args["x2"], $sql, "int", FALSE);
		esc("{y2}", $args["y2"], $sql, "int", FALSE);
		esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
		esc("{cd_precavida_imagem}", $args["cd_precavida_imagem"], $sql, "int", FALSE);

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
		UPDATE torcida.precavida_imagem 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_precavida_imagem::varchar)='{cd_precavida_imagem}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_precavida_imagem}', $id, $sql, 'str'); 

		$query=$this->db->query($sql); 
	}

	function liberar( $cd, $cd_usuario_libera, &$msg=array() )
	{
		if( $cd=='' ){ $msg[]='Parametro $cd obrigatório (usar MD5)!'; return false; }
		if( $cd_usuario_libera=='' ){ $msg[]='Parametro $cd_usuario_libera obrigatório!'; return false; }

		$sql="
		UPDATE torcida.precavida_imagem
		SET dt_libera=current_timestamp, cd_usuario_libera={cd_usuario_libera} 
		WHERE md5(cd_precavida_imagem::varchar) = '{cd_precavida_imagem}' 
		";

		esc("{cd_precavida_imagem}", $cd, $sql, "str", false);
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
		if( $cd=='' ){ $msg[]='Parametro $cd obrigatório (usar MD5)!'; return false; }

		$sql="UPDATE torcida.precavida_imagem
		SET dt_libera=null, cd_usuario_libera=null 
		WHERE md5(cd_precavida_imagem::varchar) = '{cd_precavida_imagem}'";

		esc("{cd_precavida_imagem}", $cd, $sql, "str", false);

		try
		{
			$query=$this->db->query($sql);
			return true;
		}
		catch( Exception $e )
		{
			$msg[]=$e->getMessage();
			return false;
		}
	}
}
