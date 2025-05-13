<?php
class Seminario_economico_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT s.codigo AS cd_inscricao, 
						   MD5(CAST(s.codigo AS TEXT)) AS cd_inscricao_md5,
						   UPPER(funcoes.remove_acento(s.nome)) AS nome, 
						   s.cargo, 
						   funcoes.remove_acento(s.empresa) AS empresa, 
						   TO_CHAR(s.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao, 
						   TO_CHAR(s.dt_envio_certificado,'DD/MM/YYYY HH24:MI') AS dt_envio_certificado, 
						   s.fl_presente,
						   s.email,
						   se.dt_ano AS nr_ano,
						   se.certificado,
						   s.cd_empresa, 
						   s.cd_registro_empregado, 
						   s.seq_dependencia
					 FROM acs.seminario s
					 JOIN acs.seminario_edicao se
					   ON se.cd_seminario_edicao = s.cd_seminario_edicao
					WHERE s.dt_exclusao IS NULL
					  AND s.cd_seminario_edicao = ".intval($args['cd_seminario_edicao'])."
					  {DT_INCLUSAO}
					  {FL_PRESENTE}
					  {FL_EMAIL}
		          ";

		$qr_sql = str_replace("{DT_INCLUSAO}",(((trim($args["dt_inclusao_ini"]) != "") and (trim($args["dt_inclusao_fim"]) != "")) ? " AND CAST(s.data_cadastro AS DATE) BETWEEN TO_DATE('".$args["dt_inclusao_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_inclusao_fim"]."','DD/MM/YYYY')" : ""),$qr_sql);
		$qr_sql = str_replace("{FL_PRESENTE}",(trim($args["fl_presente"]) != "" ? " AND s.fl_presente = '".trim($args["fl_presente"])."' " : ""),$qr_sql);
		$qr_sql = str_replace("{FL_EMAIL}",(trim($args["fl_email"]) == "S" ? " AND s.email LIKE '%@%' " : (trim($args["fl_email"]) == "N" ? " AND COALESCE(s.email,'') NOT LIKE '%@%' " : "")),$qr_sql);
		#echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}

	function inscricao(&$result, $args=array())
	{
		$qr_sql = "
					SELECT s.codigo AS cd_inscricao, 
					       s.nome, 
						   s.nome_sem_acento,
						   s.cargo, 
						   s.empresa, 
						   s.endereco, 
						   s.numero, 
						   s.complemento, 						   
						   s.cidade, 
						   s.uf, 
						   s.cep, 
						   s.telefone_ddd, 
						   s.telefone, 
						   s.telefone_ramal, 
						   s.fax_ddd, 
						   s.fax, 
						   s.fax_ramal, 
						   s.celular_ddd, 
						   s.celular, 
						   s.email, 
						   s.autoriza_mailing, 
						   s.cd_empresa, 
						   s.cd_registro_empregado, 
						   s.seq_dependencia, 
						   s.dt_confirmacao, 
						   s.cd_seminario_edicao, 
						   s.cd_barra, 
						   s.cd_usuario_exclusao, 
						   s.fl_presente, 
						   TO_CHAR(s.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   TO_CHAR(s.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao,
						   TO_CHAR(s.dt_envio_certificado,'DD/MM/YYYY HH24:MI') AS dt_envio_certificado,
						   se.ds_seminario_edicao
					  FROM acs.seminario s
					 JOIN acs.seminario_edicao se
					   ON se.cd_seminario_edicao = s.cd_seminario_edicao					  
					 WHERE s.codigo = ".intval($args['cd_inscricao'])."
		          ";
		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_inscricao']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE acs.seminario
						   SET cd_empresa            = ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : intval($args['cd_empresa'])).",
							   cd_registro_empregado = ".(trim($args['cd_registro_empregado']) == "" ? "DEFAULT" : intval($args['cd_registro_empregado'])).",
							   seq_dependencia       = ".(trim($args['seq_dependencia']) == "" ? "DEFAULT" : intval($args['seq_dependencia'])).",
							   nome                  = ".(trim($args['nome']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['nome']."'))").",                 						   
							   email                 = ".(trim($args['email']) == "" ? "DEFAULT" : "'".$args['email']."'").",             						   
							   cargo                 = ".(trim($args['cargo']) == "" ? "DEFAULT" : "'".$args['cargo']."'").",
							   empresa               = ".(trim($args['empresa']) == "" ? "DEFAULT" : "'".$args['empresa']."'").",						   
							   cep                   = ".(trim($args['cep']) == "" ? "DEFAULT" : "'".$args['cep']."'").",                  						   
							   endereco              = ".(trim($args['endereco']) == "" ? "DEFAULT" : "'".$args['endereco']."'").",
							   numero                = ".(trim($args['numero']) == "" ? "DEFAULT" : "'".$args['numero']."'").",
							   complemento           = ".(trim($args['complemento']) == "" ? "DEFAULT" : "'".$args['complemento']."'").",
							   cidade                = ".(trim($args['cidade']) == "" ? "DEFAULT" : "UPPER(funcoes.remove_acento('".$args['cidade']."'))").",
							   uf                    = ".(trim($args['uf']) == "" ? "DEFAULT" : "UPPER('".$args['uf']."')").",
							   telefone_ddd          = ".(trim($args['telefone_ddd']) == "" ? "DEFAULT" : "'".$args['telefone_ddd']."'").",
							   telefone              = ".(trim($args['telefone']) == "" ? "DEFAULT" : "'".$args['telefone']."'").",
							   telefone_ramal        = ".(trim($args['telefone_ramal']) == "" ? "DEFAULT" : "'".$args['telefone_ramal']."'").",
							   celular_ddd           = ".(trim($args['celular_ddd']) == "" ? "DEFAULT" : "'".$args['celular_ddd']."'").",
							   celular               = ".(trim($args['celular']) == "" ? "DEFAULT" : "'".$args['celular']."'").",
							   fax_ddd               = ".(trim($args['fax_ddd']) == "" ? "DEFAULT" : "'".$args['fax_ddd']."'").",
							   fax                   = ".(trim($args['fax']) == "" ? "DEFAULT" : "'".$args['fax']."'").",
							   fax_ramal             = ".(trim($args['fax_ramal']) == "" ? "DEFAULT" : "'".$args['fax_ramal']."'").",
							   fl_presente           = ".(trim($args['fl_presente']) == "" ? "DEFAULT" : "'".$args['fl_presente']."'")."
						 WHERE codigo = ".intval($args['cd_inscricao'])."
					  ";		
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_inscricao']);	
		}
		else
		{
			##INSERT
		}
		
		#echo "<pre>$qr_sql</pre>";
		#exit;
		
		return $retorno;
	}	

	function excluir(&$result, $args=array())
	{
		if(intval($args['cd_inscricao']) > 0)
		{
			$qr_sql = " 
						UPDATE acs.seminario
						   SET dt_exclusao         = CURRENT_TIMESTAMP,
						       cd_usuario_exclusao = ".$args['cd_usuario']."
						 WHERE codigo = ".intval($args['cd_inscricao'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}	

	function certificado(&$result, $args=array())
	{
		$retorno = 0;
		if(intval($args['cd_seminario_edicao']) > 0)
		{
			$qr_sql = "
						SELECT s.codigo AS cd_inscricao,
						       s.nome,
						       s.email,
						       s.cd_empresa,
						       s.cd_registro_empregado,
						       s.seq_dependencia,
						       se.ds_seminario_edicao,
							   se.certificado,
							   funcoes.gera_link(REPLACE(se.certificado,'[CD_INSCRICAO]',MD5(CAST(s.codigo AS TEXT))),CAST(s.cd_empresa AS INTEGER), CAST(s.cd_registro_empregado AS INTEGER), CAST(s.seq_dependencia AS INTEGER)) AS link_certificado
						  FROM acs.seminario s
						  JOIN acs.seminario_edicao se
						    ON se.cd_seminario_edicao = s.cd_seminario_edicao
						 WHERE s.dt_exclusao IS NULL
						   AND s.fl_presente = 'S'
						   AND s.email       LIKE '%@%'
						   AND s.cd_seminario_edicao = ".intval($args['cd_seminario_edicao'])."
						   ".(intval($args['cd_inscricao']) > 0 ? "AND s.codigo = ".intval($args['cd_inscricao']) : "")."
					  ";
			$result = $this->db->query($qr_sql);
			$ar_certificado = $result->result_array();
			$nr_conta = 0;
			$qr_email = "";
			foreach($ar_certificado as $item )
			{
				$enter = chr(10);
				$msg = "";
				$msg.= "Prezado(a): ".$item['nome'].$enter.$enter;
				$msg.= "Agradecemos a sua participação no ".$item['ds_seminario_edicao'].".".$enter."Esperamos que as idéias apresentadas neste evento sejam úteis para o planejamento estratégico.".$enter.$enter;
				$msg.= "Aproveitamos a oportunidade para disponibilizar, no link abaixo, seu certificado de participação no Seminário.".$enter.$enter;
				$msg.= $item['link_certificado'].$enter.$enter;
				$msg.= "Atenciosamente, ".$enter.$enter;
				$msg.= "Fundação CEEE ". $enter;
				$msg.= "Gerência de Relações Institucionais". $enter;
				$msg.= "(51) 3027 3112". $enter;
				
				$qr_email.= "
							INSERT INTO projetos.envia_emails 
								 ( 
								   dt_envio, 
								   de, 
								   para, 
								   cc,	
								   cco, 
								   assunto, 
								   texto,
								   cd_empresa,   
								   cd_registro_empregado,
								   seq_dependencia,
								   cd_evento
								 ) 
							VALUES 
								 ( 
								   CURRENT_TIMESTAMP, 
								   '".$item['ds_seminario_edicao']."', 
								   '".$item['email']."', 
								   '', 
								   '', 
								   'Certificado do ".$item['ds_seminario_edicao']."', 
								   '".$msg."',
								   ".(trim($item['cd_empresa']) == "" ? "DEFAULT" : intval($item['cd_empresa'])).",
								   ".(trim($item['cd_registro_empregado']) == "" ? "DEFAULT" : intval($item['cd_registro_empregado'])).",
								   ".(trim($item['seq_dependencia']) == "" ? "DEFAULT" : intval($item['seq_dependencia'])).",
								   46
								 );
								 
							UPDATE acs.seminario
							   SET dt_envio_certificado = CURRENT_TIMESTAMP
							 WHERE codigo = ".intval($item['cd_inscricao']).";								 
				            ";
				$nr_conta++;
			}
			
			if(trim($qr_email) != "")
			{
				#echo "<PRE>$qr_email</PRE>";
				
				$this->db->query($qr_email);
				$retorno = $nr_conta;
			}
			
		}
		
		return $retorno;
	}
	
	function comboEdicao(&$result, $args=array())
	{
		$qr_sql = "
					SELECT se.cd_seminario_edicao AS value,
				           se.ds_seminario_edicao AS text
	                  FROM acs.seminario_edicao se
					 ORDER BY se.ds_seminario_edicao DESC
		          ";
		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}
}
?>