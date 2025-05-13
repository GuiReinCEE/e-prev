<?php
class Familia_previdencia_cadastro_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT c.cd_cadastro, 
						   MD5(CAST(c.cd_cadastro AS TEXT)) AS cd_cadastro_md5, 
						   c.nome, 
						   c.endereco, 
						   c.cep, 
						   c.cidade, 
						   c.uf, 
						   c.telefone, 
						   c.celular, 
						   c.email, 
						   c.fl_associado,
						   TO_CHAR((CASE WHEN (SELECT MAX(d.dt_alteracao)
												FROM familia_previdencia.dependente d
											   WHERE d.cd_cadastro = c.cd_cadastro
												 AND d.dt_exclusao IS NULL) > c.dt_alteracao
										THEN (SELECT MAX(d.dt_alteracao)
												FROM familia_previdencia.dependente d
											   WHERE d.cd_cadastro = c.cd_cadastro
												 AND d.dt_exclusao IS NULL)
										ELSE c.dt_alteracao
									END),'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
						   (CASE WHEN (SELECT MAX(d.dt_alteracao)
										FROM familia_previdencia.dependente d
									   WHERE d.cd_cadastro = c.cd_cadastro
										 AND d.dt_exclusao IS NULL) > c.dt_alteracao
								THEN (SELECT ud.usuario
										FROM familia_previdencia.dependente d
										JOIN familia_previdencia.usuario ud
										  ON ud.cd_usuario = d.cd_usuario_alteracao											
									   WHERE d.cd_cadastro = c.cd_cadastro
										 AND d.dt_exclusao IS NULL
									   ORDER BY d.dt_alteracao DESC
									   LIMIT 1)
								ELSE u.usuario
							END) AS ds_usuario_alteracao,
						   (SELECT COUNT(*)
							  FROM familia_previdencia.dependente d
							 WHERE d.cd_cadastro = c.cd_cadastro
							   AND d.dt_exclusao IS NULL) AS qt_familiar,						
						   c.cd_usuario_alteracao,
						   c.cd_cadastro_situacao,
						   cs.ds_cadastro_situacao
					  FROM familia_previdencia.cadastro c
					  JOIN familia_previdencia.usuario u
						ON u.cd_usuario = c.cd_usuario_alteracao
					  JOIN familia_previdencia.cadastro_situacao cs
						ON cs.cd_cadastro_situacao = c.cd_cadastro_situacao
					 WHERE c.dt_exclusao IS NULL
		          ";
		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
}
?>