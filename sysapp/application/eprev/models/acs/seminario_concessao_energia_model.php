<?php
class Seminario_concessao_energia_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, &$count, $args=array() )
	{
  
		$sql = "
				SELECT sce.cd_seminario_concessao_energia,
				       MD5(sce.cd_seminario_concessao_energia::TEXT) AS cd_seminario_concessao_energia_md5,
				       TO_CHAR(sce.dt_inscricao, 'DD/MM/YYYY HH24:MI') AS dt_inscricao,
				       sce.nome_sem_acento AS nome,
				       sce.email,
				       sce.cargo,
				       sce.empresa,
				       sce.endereco,
				       sce.numero,
				       sce.complemento,
				       sce.cep,
				       sce.uf,
				       sce.cidade,
				       sce.telefone_ddd,
				       sce.telefone,
				       sce.telefone_ramal,
				       sce.cd_empresa,
				       sce.cd_registro_empregado,
				       sce.seq_dependencia,
				       sce.fl_presente,
                       CASE WHEN ee1.nr_retorno > 1 
					        THEN 'S'
					        ELSE 'N'
				       END AS fl_retorno
			      FROM acs.seminario_concessao_energia sce
			      LEFT JOIN (SELECT TRIM(UPPER(ee.para)) AS email_para, COUNT(le.ds_msg) AS nr_retorno
			                   FROM projetos.envia_emails ee
                               LEFT JOIN projetos.log_email le
				                 ON le.nr_msg::INTEGER = ee.cd_email::INTEGER
				              WHERE ee.cd_evento = 60
                              GROUP BY email_para) ee1
				    ON TRIM(UPPER(ee1.email_para)) = TRIM(UPPER(sce.email))
			     WHERE sce.dt_exclusao IS NULL
			     ORDER BY sce.dt_inscricao DESC
		       ";
		$result = $this->db->query($sql);
		$count = $result->num_rows();
	}

	function presente($ar_dado = Array())
	{
		
		// UPDATE ...
		$query = $this->db->query("
									UPDATE acs.seminario_concessao_energia
			                           SET fl_presente = ".(trim($ar_dado['fl_presente']) == "" ? "NULL" : "'".$ar_dado['fl_presente']."'")."
			                         WHERE MD5(cd_seminario_concessao_energia::TEXT) = '".$ar_dado['cd_inscricao']."';
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
	
	function excluir($ar_dado = Array())
	{
		
		// UPDATE ...
		$query = $this->db->query("
									UPDATE acs.seminario_concessao_energia
			                           SET dt_exclusao = CURRENT_TIMESTAMP
			                         WHERE MD5(cd_seminario_concessao_energia::TEXT) = '".$ar_dado['cd_inscricao']."';
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