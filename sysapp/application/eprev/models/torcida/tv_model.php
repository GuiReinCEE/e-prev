<?php
class Tv_model extends Model
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
		cd_tv
		, titulo
		, resumo
		, caminho
		, icone
		, TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') as dt_inclusao
		, cd_usuario_inclusao
		, TO_CHAR(dt_libera,'DD/MM/YYYY HH24:MI:SS') as dt_libera
		, cd_usuario_libera
		, uli.guerra as nome_usuario_libera
		FROM torcida.tv tv
		LEFT JOIN projetos.usuarios_controledi uli on uli.codigo=tv.cd_usuario_libera
		WHERE dt_exclusao IS NULL
		";

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT 
		cd_tv
		, titulo
		, resumo
		, caminho
		, icone
		, TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao
		, cd_usuario_inclusao
		, TO_CHAR(dt_libera,'DD/MM/YYYY') as dt_libera
		, cd_usuario_libera
		FROM torcida.tv  ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_tv={cd_tv} ";
			esc( "{cd_tv}", intval($cd), $sql );
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
		if(intval($args['cd_tv'])==0)
		{
			$sql="
			INSERT INTO torcida.tv 
			(
			titulo 
			, resumo 
			, caminho 
			, icone 
			, dt_inclusao
			, cd_usuario_inclusao
			) 
			VALUES 
			( 
			'{titulo}' 
			, '{resumo}' 
			, '{caminho}' 
			, '{icone}' 
			, current_timestamp 
			, {cd_usuario_inclusao} 
			)

			";
		}
		else
		{
			$sql="
			UPDATE torcida.tv 
			SET 
			  titulo = '{titulo}' 
			, resumo = '{resumo}' 
			, caminho = '{caminho}' 
			, icone = '{icone}'
			
			WHERE
			
			cd_tv = {cd_tv}
			";
		}

		esc("{titulo}", $args["titulo"], $sql, "str", FALSE);
		esc("{resumo}", $args["resumo"], $sql, "str", FALSE);
		esc("{caminho}", $args["caminho"], $sql, "str", FALSE);
		esc("{icone}", $args["icone"], $sql, "str", FALSE);
		esc("{cd_tv}", $args["cd_tv"], $sql, "int", FALSE);
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
		UPDATE torcida.tv 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_tv::varchar)='{cd_tv}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_tv}', $id, $sql, 'str'); 
 
		$query=$this->db->query($sql); 

	}

	function liberar($cd,$cd_usuario_libera,&$msg=array())
	{
		if( $cd=='' ){ $msg[]='Parametro $cd obrigatório (usar MD5)!'; return false; }
		if( $cd_usuario_libera=='' ){ $msg[]='Parametro $cd_usuario_libera obrigatório!'; return false; }

		$sql="
		UPDATE torcida.tv 
		SET dt_libera=current_timestamp, cd_usuario_libera={cd_usuario_libera} 
		WHERE md5(cd_tv::varchar) = '{cd_tv}' 
		";

		esc("{cd_tv}", $cd, $sql, "str", FALSE);
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
		UPDATE torcida.tv
		SET dt_libera=null, cd_usuario_libera=null 
		WHERE md5(cd_tv::varchar) = '{cd_tv}' 
		";

		esc("{cd_tv}", $cd, $sql, "str", FALSE);

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
