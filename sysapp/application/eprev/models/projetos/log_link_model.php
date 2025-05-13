<?php
class Log_link_model extends Model
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

		cd_log_link
		, nr_ip
		, ds_link_pagina
		, ds_link_quebrado
		, TO_CHAR(dt_erro,'DD/MM/YYYY HH24:MI:SS') as dt_erro
		
		FROM projetos.log_link 
		
		WHERE date_trunc('day', dt_erro) between TO_DATE('{inicio}', 'DD/MM/YYYY') and TO_DATE('{fim}', 'DD/MM/YYYY')
		";

		// parse query ...
		esc( "{inicio}", $args["dt_erro_inicio"], $sql );
		esc( "{fim}", $args["dt_erro_fim"], $sql );


		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT cd_log_link
, nr_ip
, ds_link_pagina
, ds_link_quebrado
, TO_CHAR(dt_erro,'DD/MM/YYYY HH24:MI:SS') as dt_erro

FROM projetos.log_link 

 ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_log_link={cd_log_link} ";
			esc( "{cd_log_link}", intval($cd), $sql );
			$query=$this->db->query($sql);
			$row=$query->row_array();
		}

		return $row;
	}

	function salvar($args,&$msg=array())
	{
		if(intval($args['cd_log_link'])==0)
		{
			$sql="
			INSERT INTO projetos.log_link ( nr_ip 
, ds_link_pagina 
, ds_link_quebrado 
, dt_erro 
) VALUES ( '{nr_ip}' 
, '{ds_link_pagina}' 
, '{ds_link_quebrado}' 
, '{dt_erro}' 
)

			";
		}
		else
		{
			$sql="
			UPDATE projetos.log_link SET 
 cd_log_link = {cd_log_link} 
, nr_ip = '{nr_ip}' 
, ds_link_pagina = '{ds_link_pagina}' 
, ds_link_quebrado = '{ds_link_quebrado}' 
, dt_erro = '{dt_erro}' 
 WHERE 
cd_log_link = {cd_log_link} 
			";
		}

		esc("{nr_ip}", $args["nr_ip"], $sql, "str", FALSE);
esc("{ds_link_pagina}", $args["ds_link_pagina"], $sql, "str", FALSE);
esc("{ds_link_quebrado}", $args["ds_link_quebrado"], $sql, "str", FALSE);
esc("{dt_erro}", $args["dt_erro"], $sql, "str", FALSE);
esc("{cd_log_link}", $args["cd_log_link"], $sql, "int", FALSE);


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
		// TODO: IMPLEMENTAR DEPOIS
	}
}
?>