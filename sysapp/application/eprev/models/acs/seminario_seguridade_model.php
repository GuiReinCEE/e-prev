<?php
class Seminario_seguridade_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, &$count, $args=array() )
	{
  
		$qr_sql = "
				SELECT cd_seminario_seguridade,
				       MD5(cd_seminario_seguridade::TEXT) AS cd_seminario_seguridade_md5,
				       TO_CHAR( dt_inscricao, 'DD/MM/YYYY HH24:MI' ) AS dt_inscricao,
				       nome,
				       email,
				       cargo,
				       empresa,
				       endereco,
				       numero,
				       complemento,
				       cep,
				       uf,
				       cidade,
				       ddd,
				       telefone,
				       ramal,
				       cd_empresa,
				       cd_registro_empregado,
				       seq_dependencia,
					   fl_presente
			      FROM acs.seminario_seguridade
			     WHERE dt_exclusao IS NULL
				   {NR_ANO_EDICAO}
			     ORDER BY dt_inscricao DESC
		       ";
		$qr_sql = str_replace("{NR_ANO_EDICAO}",(intval($args["nr_ano_edicao"]) > 0 ? " AND nr_ano_edicao = ".intval($args['nr_ano_edicao']) : ""),$qr_sql);			   				  			   
			   
			   
		$result = $this->db->query($qr_sql);
		$count = $result->num_rows();
	}

	function presente($ar_dado = Array())
	{
		
		// UPDATE ...
		$query = $this->db->query("
									UPDATE acs.seminario_seguridade
			                           SET fl_presente = ".(trim($ar_dado['fl_presente']) == "" ? "NULL" : "'".$ar_dado['fl_presente']."'")."
			                         WHERE MD5(cd_seminario_seguridade::TEXT) = '".$ar_dado['cd_inscricao']."';
		                          ");
		
		if($query)
		{
			return true;
		}
		else
		{
			$e[sizeof($e)] = 'Erro no UPDATE';
		}	
	}
	
	
	function certificado($ar_dado = Array())
	{
		$query = $this->db->query("
									SELECT rotinas.seminario_seguridade(cd_seminario_seguridade) 
									  FROM acs.seminario_seguridade
									 WHERE dt_exclusao IS NULL
									   AND fl_presente = 'S'
									   ".(trim($ar_dado['cd_inscricao']) != "" ? " AND MD5(cd_seminario_seguridade::TEXT) = '".$ar_dado['cd_inscricao']."'" : "" )."
									 ORDER BY dt_inscricao DESC 
		                          ");
		
		if($query)
		{
			return true;
		}
		else
		{
			$e[sizeof($e)] = 'Erro no ENVIO';
		}	
	}	
	
	function excluir($ar_dado = Array())
	{
		
		// UPDATE ...
		$query = $this->db->query("
									UPDATE acs.seminario_seguridade
			                           SET dt_exclusao = CURRENT_TIMESTAMP
			                         WHERE cd_seminario_seguridade = ".$ar_dado['cd_inscricao'].";
		                          ");
		
		if($query)
		{
			return true;
		}
		else
		{
			$e[sizeof($e)] = 'Erro no UPDATE';
		}	
	}	
}
?>