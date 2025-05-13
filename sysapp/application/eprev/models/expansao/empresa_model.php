<?php
class Empresa_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	public function lista_temp_os87320()
	{
		$qr_sql = "SELECT * FROM temporario.os87320 WHERE cd_empresa IS NULL;";
		
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function set_temp_os87320($cd_os87320, $cd_empresa)
	{
		$qr_sql = "
			UPDATE temporario.os87320 SET
				   cd_empresa = ".intval($cd_empresa)."
			 WHERE cd_os87320 = ".intval($cd_os87320).";";
		
		return $this->db->query($qr_sql);
	}
	
	function uf( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_uf AS value, 
			       a.cd_uf AS text 
			  FROM geografico.uf a 
			  JOIN expansao.empresa b 
			    ON a.cd_uf = b.uf 
			 ORDER BY a.ds_uf;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function grupos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_empresa_grupo AS value, 
			       a.ds_empresa_grupo AS text 
			  FROM expansao.empresa_grupo a 
			 WHERE a.dt_exclusao IS NULL 
			 ORDER BY a.ds_empresa_grupo;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function segmentos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_empresa_segmento AS value, 
			       a.ds_empresa_segmento AS text 
			  FROM expansao.empresa_segmento a 
			 WHERE a.dt_exclusao IS NULL 
			 ORDER BY a.ds_empresa_segmento;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function cidades( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT DISTINCT c.cidade 
		      FROM geografico.cidade c
			  ".(trim($args['fl_filtro']) == 'S' ? "JOIN expansao.empresa p ON c.cidade = p.cidade" : "")."
			  
		     WHERE c.uf = '".trim($args['uf'])."'
			 ORDER BY c.cidade";
			 
		$result = $this->db->query($qr_sql);
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT DISTINCT a.cd_empresa, 
						   a.ds_empresa,
						   a.nr_colaborador,
						   a.ds_empresa_patroc,
						   (SELECT TO_CHAR(ec.dt_contato,'DD/MM/YYYY') AS dt_contato  
							  FROM expansao.empresa_contato ec
							 WHERE ec.dt_exclusao IS NULL
							   AND ec.cd_empresa = a.cd_empresa
							 ORDER BY cd_empresa_contato DESC
							 LIMIT 1) AS dt_contato,
						   (SELECT COUNT(*)
							  FROM expansao.empresa_contato ec
							 WHERE ec.dt_exclusao IS NULL
							   AND ec.cd_empresa = a.cd_empresa ) AS tl_contato,
						   (SELECT COUNT(*)
							  FROM expansao.pessoa p
							 WHERE p.dt_exclusao IS NULL
							   AND p.cd_pessoa_empresa = a.cd_empresa ) AS tl_pessoa,
						   UPPER(a.cidade) AS cidade,
						   UPPER(a.uf) AS uf
					  FROM expansao.empresa a  
					  LEFT JOIN expansao.empresa_grupo_relaciona b 
						ON a.cd_empresa = b.cd_empresa
					  LEFT JOIN expansao.empresa_grupo c 
						ON b.cd_empresa_grupo = c.cd_empresa_grupo
					  LEFT JOIN expansao.empresa_segmento_relaciona d
						ON d.cd_empresa = a.cd_empresa
					  LEFT JOIN expansao.empresa_segmento e
						ON d.cd_empresa_segmento=e.cd_empresa_segmento
					  LEFT JOIN expansao.empresa_evento_relaciona evr
						ON evr.cd_empresa = a.cd_empresa
					  LEFT JOIN expansao.empresa_evento ev
						ON evr.cd_empresa_evento = ev.cd_empresa_evento
					 WHERE a.dt_exclusao IS NULL
					   AND b.dt_exclusao IS NULL
					   AND d.dt_exclusao IS NULL
					   AND evr.dt_exclusao IS NULL
					   ".(trim($args['ds_empresa']) != "" ? "AND funcoes.remove_acento(UPPER(a.ds_empresa)) LIKE funcoes.remove_acento(UPPER('%".utf8_decode(trim($args['ds_empresa']))."%'))" : "" )."
					   ".(trim($args['cidade']) != "" ? "AND UPPER(a.cidade) = UPPER('".trim($args['cidade'])."')" : "" )."
					   ".(trim($args['uf']) != "" ? "AND UPPER(a.uf) = UPPER('".trim($args['uf'])."')" : "" )."
					   
					   ".(((is_array($args['fl_nr_colaborador'])) and (count($args['fl_nr_colaborador']) > 0))
							? 
							"
								AND (CASE WHEN COALESCE(a.nr_colaborador,0) = 0 THEN 'Z'
								          WHEN COALESCE(a.nr_colaborador,0) BETWEEN 0001 AND 0249 THEN 'B'
								          WHEN COALESCE(a.nr_colaborador,0) BETWEEN 0250 AND 0499 THEN 'B'
								          WHEN COALESCE(a.nr_colaborador,0) BETWEEN 0500 AND 0749 THEN 'C'
								          WHEN COALESCE(a.nr_colaborador,0) BETWEEN 0750 AND 0999 THEN 'D'
								          WHEN COALESCE(a.nr_colaborador,0) BETWEEN 1000 AND 1499 THEN 'E'
								          WHEN COALESCE(a.nr_colaborador,0) BETWEEN 1500 AND 1999 THEN 'F'
								          WHEN COALESCE(a.nr_colaborador,0) BETWEEN 2000 AND 2499 THEN 'G'
								          WHEN COALESCE(a.nr_colaborador,0) BETWEEN 2500 AND 2999 THEN 'H'
										  WHEN COALESCE(a.nr_colaborador,0) >= 3000 THEN 'I'
									END) IN ('".implode("','",$args['fl_nr_colaborador'])."')
							" 
						: "")."
					   
					   ".(((is_array($args['grupos'])) and (count($args['grupos']) > 0)) ? "AND c.cd_empresa_grupo IN (".implode(",",$args['grupos']).")" : "" )."
					   ".(((is_array($args['segmentos'])) and (count($args['segmentos']) > 0)) ? "AND c.cd_empresa_grupo IN (".implode(",",$args['segmentos']).")" : "" )."
					   ".(((is_array($args['evento'])) and (count($args['evento']) > 0)) ? "AND ev.cd_empresa_evento IN (".implode(",",$args['evento']).")" : "" )."
					   ".(((is_array($args['origem'])) and (count($args['origem']) > 0)) ? "AND (SELECT COUNT(*)
																								   FROM expansao.empresa_contato ec
																								  WHERE ec.dt_exclusao IS NULL
																									AND ec.cd_empresa = a.cd_empresa			   
																									AND ec.cd_empresa_origem_contato IN (".implode(",",$args['origem']).")) > 0" : "" )."
					   ".(trim($args['fl_contato']) != "" ? "AND (SELECT COUNT(*)
																    FROM expansao.empresa_contato ec
															       WHERE ec.dt_exclusao IS NULL
																     AND ec.cd_empresa = a.cd_empresa) > 0" : "" )."																 
                  ";
		#echo "<PRE>$qr_sql</PRE>"; exit;
		$result = $this->db->query($qr_sql);
	}
	
	function grupos_empresa( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_empresa_grupo, 
				   a.ds_empresa_grupo
			  FROM expansao.empresa_grupo a 
			  JOIN expansao.empresa_grupo_relaciona b 
				ON a.cd_empresa_grupo = b.cd_empresa_grupo 
			 WHERE b.cd_empresa = ".intval($args['cd_empresa'])." 
			   AND a.dt_exclusao IS NULL
			   AND b.dt_exclusao IS NULL
			 ORDER BY a.ds_empresa_grupo;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function segmentos_empresa( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_empresa_segmento, 
				   a.ds_empresa_segmento 
			  FROM expansao.empresa_segmento a 
			  JOIN expansao.empresa_segmento_relaciona b 
				ON a.cd_empresa_segmento = b.cd_empresa_segmento 
			 WHERE b.cd_empresa = ".intval($args['cd_empresa'])." 
			   AND a.dt_exclusao IS NULL
			   AND b.dt_exclusao IS NULL			 
			 ORDER BY a.ds_empresa_segmento;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_empresa,
				   ds_empresa,
				   fax,
				   fax_ramal,
				   telefone,
				   telefone_ramal,
				   celular,
				   cep,
				   uf,
				   cidade,
				   logradouro,
				   numero,
				   complemento,
				   bairro,
				   site,
				   nr_colaborador
			  FROM expansao.empresa
		     WHERE cd_empresa = ".intval($args['cd_empresa']).";";
			 
		$result = $this->db->query($qr_sql);
	}

	function salvar( &$result, $args=array() )
	{
		if(intval($args["cd_empresa"]) == 0)
		{
			$cd_empresa = intval($this->db->get_new_id("expansao.empresa", "cd_empresa"));
		
			$qr_sql = "
				INSERT INTO expansao.empresa 
				     ( 
						cd_empresa,
						ds_empresa,
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
						nr_colaborador,
						dt_inclusao, 
						cd_usuario_inclusao
			         )   
			    VALUES 
				     (
						".intval($cd_empresa).",
						".(trim($args['ds_empresa']) != "" ? "'".$args['ds_empresa']."'" : "DEFAULT").",
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
						".(trim($args['nr_colaborador']) != "" ? intval($args['nr_colaborador']) : "DEFAULT").",
						CURRENT_TIMESTAMP,
						".intval($args['cd_usuario'])."
			          );";
		}
		else
		{
			$cd_empresa = $args['cd_empresa'];
			
			$qr_sql = "
				UPDATE expansao.empresa SET
				       ds_empresa     = ".(trim($args['ds_empresa']) != "" ? "'".$args['ds_empresa']."'" : "DEFAULT").",
				       uf             = ".(trim($args['uf']) != "" ? "'".$args['uf']."'" : "DEFAULT").",
				       cidade         = ".(trim($args['cidade']) != "" ? "'".$args['cidade']."'" : "DEFAULT").",
				       cep            = ".(trim($args['cep']) != "" ? "'".$args['cep']."'" : "DEFAULT").",
					   logradouro     = ".(trim($args['logradouro']) != "" ? "'".$args['logradouro']."'" : "DEFAULT").",
				       numero         = ".(trim($args['numero']) != "" ? intval($args['numero']) : "DEFAULT").",
				       complemento    = ".(trim($args['complemento']) != "" ? "'".$args['complemento']."'" : "DEFAULT").",
				       bairro         = ".(trim($args['bairro']) != "" ? "'".$args['bairro']."'" : "DEFAULT").",
				       telefone       = ".(trim($args['telefone']) != "" ? "'".$args['telefone']."'" : "DEFAULT").",
				       telefone_ramal = ".(trim($args['telefone_ramal']) != "" ? "'".$args['telefone_ramal']."'" : "DEFAULT").",
				       fax            = ".(trim($args['fax']) != "" ? "'".$args['fax']."'" : "DEFAULT").",
				       fax_ramal      = ".(trim($args['fax_ramal']) != "" ? "'".$args['fax_ramal']."'" : "DEFAULT").",
				       celular        = ".(trim($args['celular']) != "" ? "'".$args['celular']."'" : "DEFAULT").",
				       site           = ".(trim($args['site']) != "" ? "'".$args['site']."'" : "DEFAULT").",
					   nr_colaborador = ".(trim($args['nr_colaborador']) != "" ? intval($args['nr_colaborador']) : "DEFAULT")."
			     WHERE cd_empresa = ".intval($args['cd_empresa']).";";
		}

		$this->db->query($qr_sql);

		return $cd_empresa;
	}

	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE expansao.empresa
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_empresa = ".intval($args['cd_empresa']).";";
			 
		$this->db->query($qr_sql);
	}
	
	function listar_emails( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_empresa_email, 
			       ds_email  
			  FROM expansao.empresa_email 
			 WHERE dt_exclusao IS NULL 
			   AND cd_empresa = ".intval($args['cd_empresa'])." 
			 ORDER BY ds_email ASC;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_email( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO expansao.empresa_email 
			     (
			       cd_empresa,
			       ds_email,
			       dt_inclusao,
			       cd_usuario_inclusao 
		         ) 
		    VALUES 
		         ( 
			       ".intval($args['cd_empresa']).",
				   '".trim($args['ds_email'])."',
			       CURRENT_TIMESTAMP, 
				   ".intval($args['cd_usuario'])."
		         );";
		
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_email( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE expansao.empresa_email
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_empresa_email = ".intval($args['cd_empresa_email']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function listar_grupos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT b.cd_empresa_grupo_relaciona, 
			       a.ds_empresa_grupo 
			  FROM expansao.empresa_grupo a 
			  JOIN expansao.empresa_grupo_relaciona b 
			    ON a.cd_empresa_grupo = b.cd_empresa_grupo 
			 WHERE b.cd_empresa = ".intval($args['cd_empresa'])." 
			   AND a.dt_exclusao IS NULL
			   AND b.dt_exclusao IS NULL			   
			 ORDER BY a.ds_empresa_grupo ASC";
			
		$result = $this->db->query($qr_sql);	
	}
	
	function salvar_grupo( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO expansao.empresa_grupo_relaciona  
			     (
			       cd_empresa_grupo,
			       cd_empresa,
			       dt_inclusao,
			       cd_usuario_inclusao 
		         ) 
		    VALUES 
		         ( 
					".intval($args['cd_grupo']).",
					".intval($args['cd_empresa']).",
					CURRENT_TIMESTAMP,
					".intval($args['cd_usuario'])."
		         );";
		
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_grupo( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE expansao.empresa_grupo_relaciona
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_empresa_grupo_relaciona = ".intval($args['cd_empresa_grupo_relaciona']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function listar_segmentos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT b.cd_empresa_segmento_relaciona, 
			       a.ds_empresa_segmento 
			  FROM expansao.empresa_segmento a 
			  JOIN expansao.empresa_segmento_relaciona b 
			    ON a.cd_empresa_segmento = b.cd_empresa_segmento 
			 WHERE b.cd_empresa = ".intval($args['cd_empresa'])." 
			   AND a.dt_exclusao IS NULL
			   AND b.dt_exclusao IS NULL			   
			 ORDER BY a.ds_empresa_segmento ASC;";
			
		$result = $this->db->query($qr_sql);	
	}
	
	function salvar_segmento( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO expansao.empresa_segmento_relaciona 
				 (
					cd_empresa_segmento,
					cd_empresa,
					dt_inclusao, 
					cd_usuario_inclusao 
				 ) 
		    VALUES 
		         ( 
					".intval($args['cd_segmento']).",
					".intval($args['cd_empresa']).",
			        CURRENT_TIMESTAMP ,
					".intval($args['cd_usuario'])."
		         );";
		
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_segmento( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE expansao.empresa_segmento_relaciona
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_empresa_segmento_relaciona = ".intval($args['cd_empresa_segmento_relaciona']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function listar_contato( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_empresa_contato, 
			       TO_CHAR(a.dt_contato,'DD/MM/YYYY') AS dt_contato, 
				   a.ds_contato, 
				   u.nome AS nome_usuario,
				   o.ds_empresa_origem_contato,
				   atv.ds_empresa_contato_atividade,
				   a.cd_empresa
			  FROM expansao.empresa_contato a
		      JOIN projetos.usuarios_controledi u 
			    ON a.cd_usuario_inclusao = u.codigo
			  JOIN expansao.empresa_origem_contato o
			    ON o.cd_empresa_origem_contato = a.cd_empresa_origem_contato
		      JOIN expansao.empresa_contato_atividade atv
			    ON atv.cd_empresa_contato_atividade = a.cd_empresa_contato_atividade
		     WHERE a.dt_exclusao IS NULL
		       AND a.cd_empresa = ".intval($args['cd_empresa'])."
		     ORDER BY a.ds_contato;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_contato( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_empresa,
			       cd_empresa_contato,
				   TO_CHAR(dt_contato, 'DD/MM/YYYY') AS dt_contato,
				   cd_empresa_contato_atividade,
				   cd_empresa_origem_contato,
				   ds_contato
			  FROM expansao.empresa_contato
			 WHERE cd_empresa_contato = ".intval($args['cd_empresa_contato']).";";
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_contato( &$result, $args=array() )
	{
		if(intval($args['cd_empresa_contato']) == 0)
		{
			$qr_sql = "
				INSERT INTO expansao.empresa_contato 
					 (
					   cd_empresa,
					   cd_empresa_contato_atividade,
					   dt_contato,
					   ds_contato,
					   cd_empresa_origem_contato,	
					   cd_usuario_inclusao,
					   cd_usuario_alteracao
					 )
				VALUES
					 (
					   ".intval($args['cd_empresa']).",
					   ".(trim($args['cd_empresa_contato_atividade']) != ''? intval($args['cd_empresa_contato_atividade']) : "DEFAULT").",
					   ".(trim($args['dt_contato']) != ''? "TO_DATE('".trim($args['dt_contato'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					   ".(trim($args['ds_contato']) != ''? utf8_decode(str_escape($args['ds_contato'])) : "DEFAULT").",
					   ".(trim($args['cd_empresa_origem_contato']) != ''? intval($args['cd_empresa_origem_contato']) : "DEFAULT").",
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$qr_sql = "
				UPDATE expansao.empresa_contato
				   SET cd_empresa_contato_atividade = ".(trim($args['cd_empresa_contato_atividade']) != ''? intval($args['cd_empresa_contato_atividade']) : "DEFAULT").",
				       dt_contato                   = ".(trim($args['dt_contato']) != ''? "TO_DATE('".trim($args['dt_contato'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					   ds_contato                   = ".(trim($args['ds_contato']) != ''? utf8_decode(str_escape($args['ds_contato'])) : "DEFAULT").",
					   cd_empresa_origem_contato    = ".(trim($args['cd_empresa_origem_contato']) != ''? intval($args['cd_empresa_origem_contato']) : "DEFAULT").",
					   cd_usuario_alteracao         = ".intval($args['cd_usuario']).",
					   dt_alteracao                 = CURRENT_TIMESTAMP
				 WHERE cd_empresa_contato = ".intval($args['cd_empresa_contato']).";";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_contato( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE expansao.empresa_contato
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_empresa_contato = ".intval($args['cd_empresa_contato']).";";
	
		$result = $this->db->query($qr_sql);
	}
	
	function listar_pessoas( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT DISTINCT a.cd_pessoa, 
						   a.ds_pessoa, 
						   b.ds_empresa, 
						   c.ds_pessoa_departamento, 
						   d.ds_pessoa_cargo,
						   a.telefone AS telefone_1,
						   a.celular AS telefone_2
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
					   AND a.cd_pessoa_empresa = ".intval($args['cd_empresa'])."
					 ORDER BY a.ds_pessoa
			      ";
	
		$result = $this->db->query($qr_sql);
	}
	
	function listar_anexos(&$result, $args=array())
	{
		$qr_sql = "
			SELECT e.cd_empresa_anexo,
				   e.arquivo,
				   e.arquivo_nome,
				   TO_CHAR(e.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM expansao.empresa_anexo e
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = e.cd_usuario_inclusao
			 WHERE e.cd_empresa = ".intval($args['cd_empresa'])."
			   AND e.dt_exclusao IS NULL
			 ORDER BY e.dt_inclusao DESC";
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO expansao.empresa_anexo
			     (
					cd_empresa,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_empresa']).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario'])."
				 )";
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE expansao.empresa_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_empresa_anexo = ".intval($args['cd_empresa_anexo']).";";
		$this->db->query($qr_sql);
	}
	
	function salvar_contato_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO expansao.empresa_contato_anexo
			     (
					cd_empresa_contato,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_empresa_contato']).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario'])."
				 )";
				 
		$this->db->query($qr_sql);
	}
	
	function listar_contato_anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT e.cd_empresa_contato_anexo,
				   e.arquivo,
				   e.arquivo_nome,
				   TO_CHAR(e.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM expansao.empresa_contato_anexo e
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = e.cd_usuario_inclusao
			 WHERE e.cd_empresa_contato = ".intval($args['cd_empresa_contato'])."
			   AND e.dt_exclusao IS NULL
			 ORDER BY e.dt_inclusao DESC";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_contato_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE expansao.empresa_contato_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_empresa_contato_anexo = ".intval($args['cd_empresa_contato_anexo']).";";
			 
		$this->db->query($qr_sql);
	}
	
	function salvar_evento( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO expansao.empresa_evento_relaciona 
				 (
					cd_empresa_evento,
					cd_empresa,
					cd_usuario_inclusao 
				 ) 
		    VALUES 
		         ( 
					".intval($args['cd_empresa_evento']).",
					".intval($args['cd_empresa']).",
					".intval($args['cd_usuario'])."
		         );";
		
		$result = $this->db->query($qr_sql);
	}
	
	function listar_evento( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT b.cd_empresa_evento_relaciona, 
			       a.ds_empresa_evento 
			  FROM expansao.empresa_evento a 
			  JOIN expansao.empresa_evento_relaciona b 
			    ON a.cd_empresa_evento = b.cd_empresa_evento 
			 WHERE b.cd_empresa = ".intval($args['cd_empresa'])." 
			   AND a.dt_exclusao IS NULL
			   AND b.dt_exclusao IS NULL			   
			 ORDER BY a.ds_empresa_evento ASC;";
			
		$result = $this->db->query($qr_sql);	
	}
	
	function excluir_evento ( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE expansao.empresa_evento_relaciona
			   SET dt_exclusao         = CURRENT_TIMESTAMP, 
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_empresa_evento_relaciona = ".intval($args['cd_empresa_evento_relaciona']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function evento( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT a.cd_empresa_evento AS value, 
			       a.ds_empresa_evento AS text 
			  FROM expansao.empresa_evento a 
			 WHERE a.dt_exclusao IS NULL 
			 ORDER BY a.ds_empresa_evento;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function origem( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_empresa_origem_contato AS value,
				   ds_empresa_origem_contato AS text
			  FROM expansao.empresa_origem_contato
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_empresa_origem_contato;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function atividade( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_empresa_contato_atividade AS value, 
			       ds_empresa_contato_atividade AS text
              FROM expansao.empresa_contato_atividade
			 ORDER BY ds_empresa_contato_atividade;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function usuario_relatorio(&$result, $args=array())
	{
		$qr_sql = "
					SELECT DISTINCT u.codigo AS value,
						   u.nome AS text
					  FROM expansao.empresa_contato a
					  JOIN projetos.usuarios_controledi u 
						ON a.cd_usuario_inclusao = u.codigo
					  JOIN expansao.empresa_origem_contato o
						ON o.cd_empresa_origem_contato = a.cd_empresa_origem_contato
					  JOIN expansao.empresa_contato_atividade atv
						ON atv.cd_empresa_contato_atividade = a.cd_empresa_contato_atividade
					 WHERE a.dt_exclusao IS NULL
					 ORDER BY text;		
			      ";
			 
		$result = $this->db->query($qr_sql);
	}	
	
	function listar_relatorio( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT e.ds_empresa,
				   a.ds_empresa_contato_atividade,
				   TO_CHAR(c.dt_contato, 'DD/MM/YYYY') AS dt_contato,
				   c.ds_contato,
				   funcoes.get_usuario_nome(c.cd_usuario_inclusao) AS ds_usuario
			  FROM expansao.empresa_contato c
			  JOIN expansao.empresa e
				ON e.cd_empresa = c.cd_empresa
			  JOIN expansao.empresa_contato_atividade a
				ON a.cd_empresa_contato_atividade = c.cd_empresa_contato_atividade
			 WHERE c.dt_exclusao IS NULL
			   ".(trim($args['ds_empresa']) != "" ? "AND funcoes.remove_acento(UPPER(e.ds_empresa)) LIKE funcoes.remove_acento(UPPER('%".utf8_decode(trim($args['ds_empresa']))."%'))" : "" )."
			   ".(trim($args['cd_empresa_contato_atividade']) != '' ? "AND c.cd_empresa_contato_atividade = ".intval($args['cd_empresa_contato_atividade']) : '')."
			   ".(trim($args['cd_empresa']) != '' ? "AND c.cd_empresa = ".intval($args['cd_empresa']) : '')."
			   ".(trim($args['cd_usuario_relatorio']) != '' ? "AND c.cd_usuario_inclusao = ".intval($args['cd_usuario_relatorio']) : '')."
			   ".(((trim($args['dt_ini']) != "") and  (trim($args['dt_fim']) != "")) ? " AND DATE_TRUNC('day',c.dt_contato) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "").";";
		$result = $this->db->query($qr_sql);
	}
	
	function empresa_contato( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT e.cd_empresa AS value,
			       e.ds_empresa AS text
			  FROM expansao.empresa e
			 WHERE e.dt_exclusao IS NULL
			   AND 0 < (SELECT COUNT(*)
			              FROM expansao.empresa_contato c
						 WHERE c.dt_exclusao IS NULL
						   AND c.cd_empresa = e.cd_empresa)
	        ORDER BY e.ds_empresa;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function carrega_agenda( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_empresa_agenda,
				   ds_empresa_agenda,
				   local,
				   TO_CHAR(dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
				   TO_CHAR(dt_inicio, 'HH24:MI') AS hr_inicio,
				   TO_CHAR(dt_final, 'DD/MM/YYYY') AS dt_final,
				   TO_CHAR(dt_final, 'HH24:MI') AS hr_final,
				   cd_empresa,
				   ds_email_envia,
				   ds_email_encaminhar
			  FROM expansao.empresa_agenda 
			 WHERE cd_empresa_agenda = ".intval($args['cd_empresa_agenda']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function listar_agenda( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT ea.cd_empresa_agenda,
				   ea.ds_empresa_agenda,
				   ea.local,
				   TO_CHAR(ea.dt_inicio, 'DD/MM/YYYY HH24:MI') AS dt_inicio,
				   TO_CHAR(ea.dt_final, 'DD/MM/YYYY HH24:MI') AS dt_final,
				   uc.nome,
				   TO_CHAR(ea.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   ea.cd_empresa,
				   ea.ds_email_envia,
				   ea.ds_email_encaminhar,
				   ea.cd_usuario_inclusao,
				   (SELECT COUNT(*)
				      FROM expansao.empresa_agenda_anexo eae
				     WHERE eae.dt_exclusao IS NULL
				       AND eae.cd_empresa_agenda = ea.cd_empresa_agenda) AS tl_arquivo
			  FROM expansao.empresa_agenda ea
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = ea.cd_usuario_inclusao
			 WHERE ea.cd_empresa = ".intval($args['cd_empresa'])."
			   AND ea.dt_exclusao IS NULL;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_agenda( &$result, $args=array() )
	{
		if(intval($args['cd_empresa_agenda']) == 0)
		{
			$cd_empresa_agenda = intval($this->db->get_new_id("expansao.empresa_agenda ", "cd_empresa_agenda"));

			$qr_sql = "
						INSERT INTO expansao.empresa_agenda 
							 (
							   cd_empresa_agenda,
							   cd_empresa,
							   ds_empresa_agenda,
							   local,
							   ds_email_envia,
							   ds_email_encaminhar,
							   dt_inicio,
							   dt_final,	
							   cd_usuario_inclusao,
							   cd_usuario_alteracao,
							   cd_agenda
							 )
						VALUES
							 (
							   ".$cd_empresa_agenda.",
							   ".intval($args['cd_empresa']).",
							   ".(trim($args['ds_empresa_agenda']) != ''? str_escape($args['ds_empresa_agenda']) : "DEFAULT").",
							   ".(trim($args['local']) != ''? str_escape($args['local']) : "DEFAULT").",
							   ".(trim($args['ds_email_envia']) != ''? str_escape($args['ds_email_envia']) : "DEFAULT").",
							   ".(trim($args['ds_email_encaminhar']) != ''? str_escape($args['ds_email_encaminhar']) : "DEFAULT").",
							   ".(trim($args['dt_inicio']) != ''? "TO_TIMESTAMP('".trim($args['dt_inicio'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
							   ".(trim($args['dt_final']) != ''? "TO_TIMESTAMP('".trim($args['dt_final'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
							   ".intval($args['cd_usuario']).",
							   ".intval($args['cd_usuario']).",
							   (SELECT agendar 
								  FROM agenda.agendar(0,
													 ".intval($args['cd_usuario']).",
													 ".(trim($args['dt_inicio']) != ''? "TO_TIMESTAMP('".trim($args['dt_inicio'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
													 ".(trim($args['dt_final']) != ''? "TO_TIMESTAMP('".trim($args['dt_final'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
													 (SELECT 'Reunião ' || ds_empresa FROM expansao.empresa WHERE cd_empresa = ".intval($args['cd_empresa'])."),
													 ".(trim($args['local']) != ''? str_escape($args['local']) : "DEFAULT").",
													 ".(trim($args['ds_texto_agenda']) != ''? str_escape($args['ds_texto_agenda']) : "DEFAULT").",
													 'S',
													 15,
													 '".(trim($args['ds_email_encaminhar']) != ''? trim($args['ds_email_encaminhar']).";" : "")."' || funcoes.get_usuario(".intval($args['cd_usuario']).") || '@eletroceee.com.br;".trim($args['ds_email_envia'])."')
													 )
							 );
					  ";
		}
		else
		{
			$qr_sql = "
						UPDATE expansao.empresa_agenda
						   SET ds_empresa_agenda    = ".(trim($args['ds_empresa_agenda']) != ''? str_escape($args['ds_empresa_agenda']) : "DEFAULT").",
							   local                = ".(trim($args['local']) != ''? str_escape($args['local']) : "DEFAULT").",
							   ds_email_envia       = ".(trim($args['ds_email_envia']) != ''? str_escape($args['ds_email_envia']) : "DEFAULT").",
							   ds_email_encaminhar  = ".(trim($args['ds_email_encaminhar']) != ''? str_escape($args['ds_email_encaminhar']) : "DEFAULT").",
							   dt_inicio            = ".(trim($args['dt_inicio']) != ''? "TO_TIMESTAMP('".trim($args['dt_inicio'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
							   dt_final             = ".(trim($args['dt_final']) != ''? "TO_TIMESTAMP('".trim($args['dt_final'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
							   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
							   dt_alteracao         = CURRENT_TIMESTAMP
						 WHERE cd_empresa_agenda = ".intval($args['cd_empresa_agenda']).";
						
						SELECT agenda.agendar((SELECT cd_agenda FROM expansao.empresa_agenda WHERE cd_empresa_agenda = ".intval($args['cd_empresa_agenda'])."),
											 ".intval($args['cd_usuario']).",
											 ".(trim($args['dt_inicio']) != ''? "TO_TIMESTAMP('".trim($args['dt_inicio'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
											 ".(trim($args['dt_final']) != ''? "TO_TIMESTAMP('".trim($args['dt_final'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
											 (SELECT 'Reunião ' || ds_empresa FROM expansao.empresa WHERE cd_empresa = ".intval($args['cd_empresa'])."),
											 ".(trim($args['local']) != ''? str_escape($args['local']) : "DEFAULT").",
											 ".(trim($args['ds_texto_agenda']) != ''? str_escape($args['ds_texto_agenda']) : "DEFAULT").",
											 'S',
											 15,
											 '".(trim($args['ds_email_encaminhar']) != ''? trim($args['ds_email_encaminhar']).";" : "")."' || funcoes.get_usuario(".intval($args['cd_usuario']).") || '@eletroceee.com.br;".trim($args['ds_email_envia'])."'
											 );				 
				      ";

				      $cd_empresa_agenda = intval($args['cd_empresa_agenda']);
		}


		foreach ($args['arquivo'] as $key => $item) 
		{
			$qr_sql .= "
				INSERT INTO expansao.empresa_agenda_anexo
				     (
            			cd_empresa_agenda, 
            			arquivo, 
            			arquivo_nome, 
            			cd_usuario_inclusao, 
            			cd_usuario_alteracao
                     )
    			VALUES 
    			     (
    			     	".intval($cd_empresa_agenda).",
    			     	'".trim($item['arquivo'])."',
    			     	'".trim($item['arquivo_nome'])."',
    			     	".intval($args['cd_usuario']).",
    			     	".intval($args['cd_usuario'])."
    			     );";
		}

		$result = $this->db->query($qr_sql);
	}

	function get_arquivos($cd_empresa_agenda)
	{
		$qr_sql = "
            SELECT arquivo_nome,
                   arquivo
              FROM expansao.empresa_agenda_anexo
             WHERE dt_exclusao                      IS NULL
               AND cd_empresa_agenda = ".intval($cd_empresa_agenda).";";

        return $this->db->query($qr_sql)->result_array();
	}
	
	function excluir_agenda( &$result, $args=array() )
	{
		$qr_sql = "
					UPDATE expansao.empresa_agenda
					   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
						   dt_exclusao         = CURRENT_TIMESTAMP
					 WHERE cd_empresa_agenda = ".intval($args['cd_empresa_agenda']).";
					
					SELECT agenda.excluir((SELECT cd_agenda FROM expansao.empresa_agenda WHERE cd_empresa_agenda = ".intval($args['cd_empresa_agenda'])."),".intval($args['cd_usuario']).");
				  ";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function mapaCidade(&$result, $args=array())
	{
		$qr_sql = "
					SELECT b.nm_localid AS ds_cidade, 
						   b.longitude,
						   b.latitude
					  FROM geografico.br_localidades_2010_v1 b
					 WHERE b.uf         IN ('".str_replace(",","','",$args['ar_uf'])."')
					   AND b.nm_localid IN ('".str_replace(",","','",$args['ar_cidade'])."')
					   AND b.tipo       = 'URBANO' 
					   AND b.cd_nivel   = '1' 
					   AND b.nm_categor = 'CIDADE'
					 ORDER BY ds_cidade,
							  b.longitude,
							  b.latitude
				  ";
		#echo "<pre style='text-align:center;'>$qr_sql</pre>"; exit;

		$result = $this->db->query($qr_sql);
	}	
}
?>