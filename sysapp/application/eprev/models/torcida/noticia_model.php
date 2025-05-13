<?php
class Noticia_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		if( ! isset($args['tp_noticia']) ){ $args['tp_noticia']=''; }
		// mount query
		$sql = "
		SELECT noticia.cd_noticia
		, noticia.ds_titulo
		, noticia.ds_noticia
		, noticia.ds_resumo
		, noticia.tp_noticia
		, TO_CHAR(noticia.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') as dt_inclusao
		, noticia.cd_usuario_inclusao
		, TO_CHAR(noticia.dt_libera,'DD/MM/YYYY HH24:MI:SS') as dt_libera
		, noticia.cd_usuario_libera
		, TO_CHAR(noticia.dt_exclusao,'DD/MM/YYYY') as dt_exclusao
		, noticia.cd_usuario_exclusao
		, ulib.guerra as nome_usuario_libera 
		FROM torcida.noticia noticia
		LEFT JOIN projetos.usuarios_controledi ulib ON ulib.codigo=noticia.cd_usuario_libera
		WHERE ( tp_noticia='{tp_noticia}' OR '{tp_noticia}'='' )
		AND dt_exclusao IS NULL
		";

		// parse query ...
		esc( '{tp_noticia}', $args['tp_noticia'], $sql );

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT cd_noticia
		, ds_titulo
		, ds_noticia
		, ds_resumo
		, tp_noticia
		, TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao
		, cd_usuario_inclusao
		, TO_CHAR(dt_libera,'DD/MM/YYYY') as dt_libera
		, cd_usuario_libera
		, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
		, cd_usuario_exclusao 
		FROM torcida.noticia  ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_noticia={cd_noticia} ";
			esc( "{cd_noticia}", intval($cd), $sql );
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
		if(intval($args['cd_noticia'])==0)
		{
			$sql="
			INSERT INTO torcida.noticia ( ds_titulo 
, ds_noticia 
, ds_resumo 
, tp_noticia 
, dt_inclusao
, cd_usuario_inclusao
) VALUES ( '{ds_titulo}' 
, '{ds_noticia}' 
, '{ds_resumo}' 
, '{tp_noticia}' 
, current_timestamp
, {cd_usuario_inclusao}
)

			";
		}
		else
		{
			$sql="
			UPDATE torcida.noticia SET 
  ds_titulo = '{ds_titulo}' 
, ds_noticia = '{ds_noticia}' 
, ds_resumo = '{ds_resumo}' 
, tp_noticia = '{tp_noticia}' 
 WHERE 
cd_noticia = {cd_noticia} 
			";
		}

		esc("{ds_titulo}", $args["ds_titulo"], $sql, "str", FALSE);
esc("{ds_noticia}", $args["ds_noticia"], $sql, "str", FALSE);
esc("{ds_resumo}", $args["ds_resumo"], $sql, "str", FALSE);
esc("{tp_noticia}", $args["tp_noticia"], $sql, "str", FALSE);
esc("{cd_noticia}", $args["cd_noticia"], $sql, "int", FALSE);
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
		UPDATE torcida.noticia 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_noticia::varchar)='{cd_noticia}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_noticia}', $id, $sql, 'str'); 
 
		$query=$this->db->query($sql); 

	}

	function liberar($cd,$cd_usuario_libera,&$msg=array())
	{
		if( $cd=='' ){ $msg[]='Parametro $cd obrigatório (usar MD5)!'; return false; }
		if( $cd_usuario_libera=='' ){ $msg[]='Parametro $cd_usuario_libera obrigatório!'; return false; }

		$sql="
		UPDATE torcida.noticia 
		SET dt_libera=current_timestamp, cd_usuario_libera={cd_usuario_libera} 
		WHERE md5(cd_noticia::varchar) = '{cd_noticia}' 
		";

		esc("{cd_noticia}", $cd, $sql, "str", FALSE);
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
		UPDATE torcida.noticia 
		SET dt_libera=null, cd_usuario_libera=null 
		WHERE md5(cd_noticia::varchar) = '{cd_noticia}' 
		";

		esc("{cd_noticia}", $cd, $sql, "str", FALSE);

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