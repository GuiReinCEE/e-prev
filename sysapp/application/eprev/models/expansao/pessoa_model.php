<?php
class Pessoa_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function empresas( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_empresa AS value, 
			       substring(a.ds_empresa, 0, 200) AS text 
			  FROM expansao.empresa a 
			  LEFT JOIN expansao.pessoa b 
			    ON a.cd_empresa = b.cd_pessoa_empresa 
		     WHERE a.dt_exclusao IS NULL
			 ORDER BY a.ds_empresa";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function uf( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_uf AS value, 
			       a.cd_uf AS text 
			  FROM geografico.uf a 
			 ORDER BY a.ds_uf";
			 
		$result = $this->db->query($qr_sql);
	}

	function grupos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_empresa_grupo AS value, 
			       a.ds_empresa_grupo AS text 
			  FROM expansao.empresa_grupo a 
			 WHERE a.dt_exclusao IS NULL 
			 ORDER BY a.ds_empresa_grupo";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function segmentos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_empresa_segmento AS value, 
			       a.ds_empresa_segmento AS text 
			  FROM expansao.empresa_segmento a 
			 WHERE a.dt_exclusao IS NULL 
			 ORDER BY a.ds_empresa_segmento";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function departamentos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_pessoa_departamento AS value, 
			       ds_pessoa_departamento AS text 
			  FROM expansao.pessoa_departamento 
			 WHERE dt_exclusao IS NULL 
			 ORDER BY ds_pessoa_departamento";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function cargos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_pessoa_cargo AS value, 
			       ds_pessoa_cargo AS text 
			  FROM expansao.pessoa_cargo 
			 WHERE dt_exclusao IS NULL 
			 ORDER BY ds_pessoa_cargo";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function cidades( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT DISTINCT c.cidade 
		      FROM geografico.cidade c
			  ".(trim($args['fl_filtro']) == 'S' ? "JOIN expansao.pessoa p ON c.cidade = p.cidade" : "")."
			  
		     WHERE c.uf = '".trim($args['uf'])."'
			 ORDER BY c.cidade";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT DISTINCT a.cd_pessoa, 
			       a.ds_pessoa, 
				   b.ds_empresa, 
				   c.ds_pessoa_departamento, 
				   d.ds_pessoa_cargo
		      FROM expansao.pessoa a
			  LEFT JOIN expansao.empresa b 
			    ON a.cd_pessoa_empresa = b.cd_empresa
			  LEFT JOIN expansao.pessoa_departamento c
			    ON c.cd_pessoa_departamento = a.cd_pessoa_departamento
			  LEFT JOIN expansao.pessoa_cargo d
			    ON d.cd_pessoa_cargo = a.cd_pessoa_cargo
			  LEFT JOIN expansao.empresa_grupo_relaciona e
			    ON b.cd_empresa = e.cd_empresa
			  LEFT JOIN expansao.empresa_grupo f
			    ON e.cd_empresa_grupo = f.cd_empresa_grupo
			  LEFT JOIN expansao.empresa_segmento_relaciona g
			    ON b.cd_empresa = g.cd_empresa
			  LEFT JOIN expansao.empresa_segmento h
			    ON h.cd_empresa_segmento = g.cd_empresa_segmento
		     WHERE a.dt_exclusao IS NULL 
			   AND b.dt_exclusao IS NULL
			   ".(trim($args['ds_pessoa']) != "" ? "AND funcoes.remove_acento(UPPER(a.ds_pessoa)) LIKE funcoes.remove_acento(UPPER('%".utf8_decode(trim($args['ds_pessoa']))."%'))" : "" )."
			   ".(trim($args['cidade']) != "" ? "AND UPPER(a.cidade) = UPPER('".trim($args['cidade'])."')" : "" )."
			   ".(trim($args['uf']) != "" ? "AND UPPER(a.uf) = UPPER('".trim($args['uf'])."')" : "" )."
			   ".(trim($args['cd_pessoa_empresa']) != "" ? "AND a.cd_pessoa_empresa = ".intval($args['cd_pessoa_empresa']) : "" )."
			   ".(trim($args['grupos']) != "" ? "AND f.cd_empresa_grupo IN (".trim($args['grupos']).")" : "" )."
			   ".(trim($args['segmentos']) != "" ? "AND h.cd_empresa_segmento IN (".trim($args['segmentos']).")" : "" )."
			   ".(trim($args['cd_pessoa_departamento']) != "" ? "AND a.cd_pessoa_departamento = ".intval($args['cd_pessoa_departamento']) : "" )."
			   ".(trim($args['cd_pessoa_cargo']) != "" ? "AND a.cd_pessoa_cargo = ".intval($args['cd_pessoa_cargo']) : "" ).";";

		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args["cd_pessoa"]) == 0)
		{
			$cd_pessoa = intval($this->db->get_new_id("expansao.pessoa", "cd_pessoa"));
		
			$qr_sql = "
				INSERT INTO expansao.pessoa 
				     ( 
						cd_pessoa,
						ds_pessoa,
						cd_pessoa_empresa,
						cd_pessoa_departamento,
						cd_pessoa_cargo,
						uf,
						cidade, 
						cep,
						logradouro,
						numero,
						complemento,
						bairro,
						telefone, 
						telefone_ramal,
						fax,
						fax_ramal, 
						celular,
						site,
						dt_inclusao, 
						cd_usuario_inclusao, 
						cd_empresa,
						cd_registro_empregado,
						seq_dependencia
			         )   
			    VALUES 
				     (
						".intval($cd_pessoa).",
						".(trim($args['ds_pessoa']) != "" ? "'".$args['ds_pessoa']."'" : "DEFAULT").",
						".(trim($args['cd_pessoa_empresa']) != "" ? intval($args['cd_pessoa_empresa']) : "DEFAULT").",
						".(trim($args['cd_pessoa_departamento']) != "" ? intval($args['cd_pessoa_departamento']) : "DEFAULT").",
						".(trim($args['cd_pessoa_cargo']) != "" ? intval($args['cd_pessoa_cargo']) : "DEFAULT").",
						".(trim($args['uf']) != "" ? "'".$args['uf']."'" : "DEFAULT").",
						".(trim($args['cidade']) != "" ? "'".$args['cidade']."'" : "DEFAULT").",
						".(trim($args['cep']) != "" ? "'".$args['cep']."'" : "DEFAULT").",
						".(trim($args['logradouro']) != "" ? "'".$args['logradouro']."'" : "DEFAULT").",
						".(trim($args['numero']) != "" ? intval($args['numero']) : "DEFAULT").",
						".(trim($args['complemento']) != "" ? "'".$args['complemento']."'" : "DEFAULT").",
						".(trim($args['bairro']) != "" ? "'".$args['bairro']."'" : "DEFAULT").",
						".(trim($args['telefone']) != "" ? "'".$args['telefone']."'" : "DEFAULT").",
						".(trim($args['telefone_ramal']) != "" ? "'".$args['telefone_ramal']."'" : "DEFAULT").",
						".(trim($args['fax']) != "" ? "'".$args['fax']."'" : "DEFAULT").",
						".(trim($args['fax_ramal']) != "" ? "'".$args['fax_ramal']."'" : "DEFAULT").",
						".(trim($args['celular']) != "" ? "'".$args['celular']."'" : "DEFAULT").",
						".(trim($args['site']) != "" ? "'".$args['site']."'" : "DEFAULT").",
						CURRENT_TIMESTAMP,
						".intval($args['cd_usuario']).",
						".(trim($args['cd_empresa']) != "" ? intval($args['cd_empresa']) : "DEFAULT").",
						".(trim($args['cd_registro_empregado']) != "" ? intval($args['cd_registro_empregado']) : "DEFAULT").",
						".(trim($args['seq_dependencia']) != "" ? intval($args['seq_dependencia']) : "DEFAULT")."
			          );";
		}
		else
		{
			$cd_pessoa = $args['cd_pessoa'];
			
			$qr_sql = "
				UPDATE expansao.pessoa SET
				       cd_pessoa_empresa      = ".(trim($args['cd_pessoa_empresa']) != "" ? intval($args['cd_pessoa_empresa']) : "DEFAULT").",
				       cd_pessoa_departamento = ".(trim($args['cd_pessoa_departamento']) != "" ? intval($args['cd_pessoa_departamento']) : "DEFAULT").",
				       cd_pessoa_cargo        = ".(trim($args['cd_pessoa_cargo']) != "" ? intval($args['cd_pessoa_cargo']) : "DEFAULT").",
				       ds_pessoa              = ".(trim($args['ds_pessoa']) != "" ? "'".$args['ds_pessoa']."'" : "DEFAULT").",
				       uf                     = ".(trim($args['uf']) != "" ? "'".$args['uf']."'" : "DEFAULT").",
				       cidade                 = ".(trim($args['cidade']) != "" ? "'".$args['cidade']."'" : "DEFAULT").",
				       cep                    = ".(trim($args['cep']) != "" ? "'".$args['cep']."'" : "DEFAULT").",
					   logradouro             = ".(trim($args['logradouro']) != "" ? "'".$args['logradouro']."'" : "DEFAULT").",
				       numero                 = ".(trim($args['numero']) != "" ? intval($args['numero']) : "DEFAULT").",
				       complemento            = ".(trim($args['complemento']) != "" ? "'".$args['complemento']."'" : "DEFAULT").",
				       bairro                 = ".(trim($args['bairro']) != "" ? "'".$args['bairro']."'" : "DEFAULT").",
				       telefone               = ".(trim($args['telefone']) != "" ? "'".$args['telefone']."'" : "DEFAULT").",
				       telefone_ramal         = ".(trim($args['telefone_ramal']) != "" ? "'".$args['telefone_ramal']."'" : "DEFAULT").",
				       fax                    = ".(trim($args['fax']) != "" ? "'".$args['fax']."'" : "DEFAULT").",
				       fax_ramal              = ".(trim($args['fax_ramal']) != "" ? "'".$args['fax_ramal']."'" : "DEFAULT").",
				       celular                = ".(trim($args['celular']) != "" ? "'".$args['celular']."'" : "DEFAULT").",
				       site                   = ".(trim($args['site']) != "" ? "'".$args['site']."'" : "DEFAULT").",
				       cd_empresa             = ".(trim($args['cd_empresa']) != "" ? intval($args['cd_empresa']) : "DEFAULT").",
				       cd_registro_empregado  = ".(trim($args['cd_registro_empregado']) != "" ? intval($args['cd_registro_empregado']) : "DEFAULT").",
				       seq_dependencia        = ".(trim($args['seq_dependencia']) != "" ? intval($args['seq_dependencia']) : "DEFAULT")."
			     WHERE cd_pessoa = ".intval($args['cd_pessoa']).";";
		}

		$this->db->query($qr_sql);

		return $cd_pessoa;
	}
	
	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_pessoa,
				   ds_pessoa,
				   cd_pessoa_empresa,
				   cd_pessoa_departamento,
				   cd_pessoa_cargo,
				   uf,
				   cidade, 
				   cep,
				   logradouro,
				   numero,
				   complemento,
				   bairro,
				   telefone, 
				   telefone_ramal,
				   fax,
				   fax_ramal, 
				   celular,
				   site,
				   dt_inclusao, 
				   cd_usuario_inclusao, 
				   cd_empresa,
				   cd_registro_empregado,
				   seq_dependencia 
			  FROM expansao.pessoa
			 WHERE dt_exclusao IS NULL
			   AND cd_pessoa = ".intval($args['cd_pessoa']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_email( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO expansao.pessoa_email 
			     (
			       cd_pessoa,
			       ds_email,
			       dt_inclusao,
			       cd_usuario_inclusao 
		         ) 
		    VALUES 
		         ( 
			       ".intval($args['cd_pessoa']).",
				   '".trim($args['ds_email'])."',
			       CURRENT_TIMESTAMP, 
				   ".intval($args['cd_usuario'])."
		         );";
		
		$result = $this->db->query($qr_sql);
	}
	
	function listar_emails( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_pessoa_email, 
			       ds_email  
			  FROM expansao.pessoa_email 
			 WHERE dt_exclusao IS NULL 
			   AND cd_pessoa = ".intval($args['cd_pessoa'])." 
			 ORDER BY ds_email ASC;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_email( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE expansao.pessoa_email
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_pessoa_email = ".intval($args['cd_pessoa_email']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function excluir( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE expansao.pessoa
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_pessoa = ".intval($args['cd_pessoa']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function listar_contato( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_pessoa_contato, 
			       TO_CHAR(a.dt_contato,'DD/MM/YYYY') AS dt_contato, 
				   a.ds_contato, 
				   u.nome AS nome_usuario
			  FROM expansao.pessoa_contato a
		      JOIN projetos.usuarios_controledi u 
			    ON a.cd_usuario_inclusao = u.codigo
		     WHERE a.dt_exclusao IS NULL
		       AND a.cd_pessoa = ".intval($args['cd_pessoa'])."
		     ORDER BY a.ds_contato;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_contato( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO expansao.pessoa_contato 
			     (
			       cd_pessoa,
			       dt_contato,
			       ds_contato,
			       dt_inclusao,
			       cd_usuario_inclusao
		         )
		    VALUES
		         (
			       ".intval($args['cd_pessoa']).",
			       TO_DATE('".trim($args['dt_contato'])."', 'DD/MM/YYYY'),
				   ".str_escape($args['ds_contato']).",
				   CURRENT_TIMESTAMP,
				   ".intval($args['cd_usuario'])."
		         );";
			
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_contato( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE expansao.pessoa_contato
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_pessoa_contato = ".intval($args['cd_pessoa_contato']).";";
	
		$result = $this->db->query($qr_sql);
	}
}
?>