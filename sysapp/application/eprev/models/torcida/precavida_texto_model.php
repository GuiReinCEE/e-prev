<?php
class Precavida_texto_model extends Model
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
		cd_precavida_texto
		, texto
		, TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') as dt_inclusao
		, TO_CHAR(dt_libera,'DD/MM/YYYY HH24:MI:SS') as dt_libera
		, ul.guerra as nome_usuario_libera
		FROM torcida.precavida_texto pt
		LEFT JOIN projetos.usuarios_controledi ul ON ul.codigo=pt.cd_usuario_libera
		WHERE pt.dt_exclusao IS NULL;
		";

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT cd_precavida_texto
		, texto
		FROM torcida.precavida_texto  ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_precavida_texto={cd_precavida_texto} ";
			esc( "{cd_precavida_texto}", intval($cd), $sql );
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
		if(intval($args['cd_precavida_texto'])==0)
		{
			$sql="INSERT INTO torcida.precavida_texto ( texto, dt_inclusao, cd_usuario_inclusao ) VALUES ( '{texto}', CURRENT_TIMESTAMP, {cd_usuario_inclusao} )";
		}
		else
		{
			$sql="UPDATE torcida.precavida_texto SET texto = '{texto}' WHERE cd_precavida_texto={cd_precavida_texto}";
		}

		esc("{texto}", $args["texto"], $sql, "str", FALSE);
		esc("{cd_precavida_texto}", $args["cd_precavida_texto"], $sql, "int", FALSE);
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
		UPDATE torcida.precavida_texto 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_precavida_texto::varchar)='{cd_precavida_texto}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_precavida_texto}', $id, $sql, 'str'); 
 
		$query=$this->db->query($sql); 
	}

	function liberar( $cd, $cd_usuario_libera, &$msg=array() )
	{
		if( $cd=='' ){ $msg[]='Parametro $cd obrigatório (usar MD5)!'; return false; }
		if( $cd_usuario_libera=='' ){ $msg[]='Parametro $cd_usuario_libera obrigatório!'; return false; }

		$sql="
		UPDATE torcida.precavida_texto
		SET dt_libera=current_timestamp, cd_usuario_libera={cd_usuario_libera} 
		WHERE md5(cd_precavida_texto::varchar) = '{cd_precavida_texto}' 
		";

		esc("{cd_precavida_texto}", $cd, $sql, "str", false);
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

		$sql="UPDATE torcida.precavida_texto
		SET dt_libera=null, cd_usuario_libera=null 
		WHERE md5(cd_precavida_texto::varchar) = '{cd_precavida_texto}'";

		esc("{cd_precavida_texto}", $cd, $sql, "str", false);

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
