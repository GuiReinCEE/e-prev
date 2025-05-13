<?php
class Enquete_model extends Model
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

en.cd_enquete
, en.nome
/*, TO_CHAR(en.dt_inicio,'DD/MM/YYYY') as dt_inicio*/
, TO_CHAR(en.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') as dt_inclusao
, TO_CHAR(en.dt_libera,'DD/MM/YYYY HH24:MI:SS') as dt_libera
, ul.guerra as nome_usuario_libera

FROM torcida.enquete en

LEFT JOIN projetos.usuarios_controledi ul 
ON ul.codigo=en.cd_usuario_libera

WHERE en.dt_exclusao IS NULL
		";

		// parse query ...
		

		// return result ...
		$result = $this->db->query($sql);
	}

	function carregar($cd)
	{
		$sql = " SELECT cd_enquete
, nome
/*, TO_CHAR(dt_inicio,'DD/MM/YYYY') as dt_inicio*/
, TO_CHAR(dt_inclusao,'DD/MM/YYYY') as dt_inclusao
, cd_usuario_inclusao
, TO_CHAR(dt_libera,'DD/MM/YYYY') as dt_libera
, cd_usuario_libera
, TO_CHAR(dt_exclusao,'DD/MM/YYYY') as dt_exclusao
, cd_usuario_exclusao
FROM torcida.enquete ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE cd_enquete={cd_enquete} ";
			esc( "{cd_enquete}", intval($cd), $sql );
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
		if(intval($args['cd_enquete'])==0)
		{
			$sql="
			INSERT INTO torcida.enquete ( nome 
/*, dt_inicio*/ 
, dt_inclusao 
, cd_usuario_inclusao 
) VALUES ( '{nome}' 
/*, TO_DATE('{dt_inicio}', 'DD/MM/YYYY')*/ 
, CURRENT_TIMESTAMP 
, {cd_usuario_inclusao} 
)

			";
		}
		else
		{
			$sql="
			UPDATE torcida.enquete SET 
  nome = '{nome}' 
/*, dt_inicio = TO_DATE('{dt_inicio}', 'DD/MM/YYYY')*/ 
 WHERE 
cd_enquete = {cd_enquete} 
			";
		}

		esc("{nome}", $args["nome"], $sql, "str", FALSE);
//esc("{dt_inicio}", $args["dt_inicio"], $sql, "str", FALSE);
esc("{cd_usuario_inclusao}", $args["cd_usuario_inclusao"], $sql, "int", FALSE);
esc("{cd_enquete}", $args["cd_enquete"], $sql, "int", FALSE);


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
		UPDATE torcida.enquete 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao} 
		WHERE md5(cd_enquete::varchar)='{cd_enquete}' 
		"; 
		esc('{cd_usuario_exclusao}', usuario_id(), $sql, 'int'); 
		esc('{cd_enquete}', $id, $sql, 'str'); 
 
		$query=$this->db->query($sql); 

	}

	function liberar( $cd, $cd_usuario_libera, &$msg=array() )
	{
		if( $cd=='' ){ $msg[]='Parametro $cd obrigatório (usar MD5)!'; return false; }
		if( $cd_usuario_libera=='' ){ $msg[]='Parametro $cd_usuario_libera obrigatório!'; return false; }

		$sql="
		UPDATE torcida.enquete
		SET dt_libera=current_timestamp, cd_usuario_libera={cd_usuario_libera} 
		WHERE md5(cd_enquete::varchar) = '{cd_enquete}' 
		";

		esc("{cd_enquete}", $cd, $sql, "str", false);
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

		$sql="UPDATE torcida.enquete
		SET dt_libera=null, cd_usuario_libera=null 
		WHERE md5(cd_enquete::varchar) = '{cd_enquete}'";

		esc("{cd_enquete}", $cd, $sql, "str", false);

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

	function carregar_pergunta($cd_enquete, $cd_usuario)
	{
		$sql = " SELECT * FROM torcida.enquete_pergunta ep WHERE ep.dt_exclusao IS NULL AND cd_enquete = {cd_enquete} ";
		esc( '{cd_enquete}', $cd_enquete, $sql, 'int' );
		$query=$this->db->query($sql);
		$row = $query->row_array();
		
		if(!$row)
		{
			$sql="
			INSERT INTO torcida.enquete_pergunta(
            cd_enquete, ds_pergunta, ds_complemento, 
            nr_ordem, dt_inclusao, cd_usuario_inclusao)
    		VALUES ({cd_enquete}, '{ds_pergunta}', '{ds_complemento}', 
            {nr_ordem}, CURRENT_TIMESTAMP, {cd_usuario_inclusao})";
            esc( '{cd_enquete}', $cd_enquete, $sql, 'int' );
            esc( '{ds_pergunta}', '', $sql, 'str' );
            esc( '{ds_complemento}', '', $sql, 'str' );
            esc( '{nr_ordem}', 1, $sql, 'int' );
            esc( '{cd_usuario_inclusao}', $cd_usuario, $sql, 'int' );
			$query=$this->db->query($sql);

			$sql = " SELECT * FROM torcida.enquete_pergunta ep WHERE ep.dt_exclusao IS NULL AND cd_enquete = {cd_enquete} ";
			esc( '{cd_enquete}', $cd_enquete, $sql, 'int' );
			$query=$this->db->query($sql);
			$row = $query->row_array();
		}
		
		return $row;
	}
	
	function listar_pergunta_item($cd_enquete)
	{
		$sql = "
SELECT epi.* 
FROM torcida.enquete_pergunta_item epi 
JOIN torcida.enquete_pergunta ep ON ep.cd_enquete_pergunta=epi.cd_enquete_pergunta 
WHERE epi.dt_exclusao IS NULL 
AND ep.cd_enquete = {cd_enquete}
ORDER BY nr_ordem ASC, ds_item ASC
";
		esc( '{cd_enquete}', $cd_enquete, $sql, 'int' );
		$query = $this->db->query($sql);
		$rows = $query->result_array();

		return $rows;
	}
	
	function listar_pergunta_resposta($cd_enquete,$args=array())
	{
		$periodo='';
		$origem='';
		if(isset($args['inicio']) && $args['inicio']!='')
		{
			$periodo.=" AND date_trunc( 'day', epr.dt_inclusao ) >= to_date( '{dt_inclusao_inicio}', 'DD/MM/YYYY' ) ";
		}
		if(isset($args['fim']) && $args['fim']!='')
		{
			$periodo.=" AND date_trunc( 'day', epr.dt_inclusao ) <= to_date( '{dt_inclusao_fim}', 'DD/MM/YYYY' ) ";
		}
		if(isset($args['origem']) && $args['origem']!='')
		{
			if( $args['origem']=='interna' )
			{
				$origem=" AND ip like '10.63.%' ";
			}
			if( $args['origem']=='externa' )
			{
				$origem=" AND NOT ip like '10.63.%' ";
			}
		}

		$sql = "
			SELECT epi.nr_ordem,
			       epi.ds_item, 
		               COUNT(epr.cd_enquete_pergunta_item) AS qt_item
			  FROM torcida.enquete_pergunta_item epi
			  JOIN torcida.enquete_pergunta ep
			    ON ep.cd_enquete_pergunta = epi.cd_enquete_pergunta
			  LEFT JOIN torcida.enquete_pergunta_resposta epr
			    ON epr.cd_enquete_pergunta_item = epi.cd_enquete_pergunta_item
			 WHERE ep.cd_enquete = {cd_enquete}
				$periodo
				$origem
			 GROUP BY epi.nr_ordem, epi.ds_item
			 ORDER BY epi.nr_ordem ASC
		";

		esc( '{cd_enquete}', $cd_enquete, $sql, 'int' );
		esc( '{dt_inclusao_inicio}', $args['inicio'], $sql, 'str' );
		esc( '{dt_inclusao_fim}', $args['fim'], $sql, 'str' );
		$query=$this->db->query($sql);
		$rows = $query->result_array();

		return $rows;
	}
}
