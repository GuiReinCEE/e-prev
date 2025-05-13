<?php
class Log_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, &$count, $args=array() )
	{
		// mount query
		$sql = "
		SELECT 

		tipo, local, '<div class=\'descricao_log\'>'||descricao||'</div>' as descricao, to_char(dt_cadastro, 'DD/MM/YYYY HH24:MI:SS') as dt_cadastro

		FROM

		projetos.log

		WHERE 

		(upper(tipo) = upper('{tipo}') OR '{tipo}'='')

		AND UPPER(local) LIKE upper('%{local}%')
		AND descricao LIKE '%{descricao}%'

		AND date_trunc('day', dt_cadastro) BETWEEN TO_DATE('{dt_cadastro_inicio}', 'DD/MM/YYYY') 
		AND TO_DATE('{dt_cadastro_fim} 23:59:59', 'DD/MM/YYYY HH24:MI:SS') 

		ORDER BY dt_cadastro DESC

		LIMIT {limite}
		";

		// parse query ...
		esc( "{tipo}", $args["tipo"], $sql );
		esc( "{local}", $args["local"], $sql );
		esc( "{descricao}", $args["descricao"], $sql );
		esc( "{dt_cadastro_inicio}", $args["data_inicio"], $sql );
		esc( "{dt_cadastro_fim}", $args["data_fim"], $sql );
		esc( "{limite}", $args["limite"], $sql );

		//echo "<pre>$sql</pre>";

		// return result ...
		$result=$this->db->query($sql);
	}

	function insert($tipo, $mensagem)
	{
		$dados = array( $tipo, $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'], $mensagem );
		$sql_log = "INSERT INTO projetos.log (tipo, local, descricao, dt_cadastro) VALUES (?, ?, ?, CURRENT_TIMESTAMP)";

		if( true === ($resultado=$this->db->query( $sql_log, $dados)) )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
