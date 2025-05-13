<?php
class Dialogo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function comboEdicao( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT cd_dialogo AS value, 
						   ds_dialogo AS text
					  FROM acs.dialogo
					 ORDER BY text DESC
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function listar_inscricao(&$result, $args=array())
	{
		$qr_sql = "
					SELECT di.cd_dialogo_inscricao, 
					       di.cd_dialogo, 
						   TO_CHAR(di.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
						   di.nome, 
						   di.cargo, 
						   di.empresa, 
						   di.endereco, 
						   di.numero, 
						   di.complemento, 
						   di.bairro, 
						   di.cidade, 
						   di.uf, 
						   di.cep, 
						   di.telefone, 
						   di.telefone_ramal, 
						   di.fax, 
						   di.fax_ramal, 
						   di.telefone_ddd, 
						   di.fax_ddd, 
						   di.celular_ddd, 
						   di.celular, 
						   di.email,
						   CASE WHEN di.email like '%@%' THEN 'S' ELSE 'N' END AS fl_email,
						   di.cd_empresa, 
						   di.cd_registro_empregado, 
						   di.seq_dependencia, 
						   di.dt_exclusao, 
						   di.cd_usuario_exclusao, 
						   di.fl_presente,
						   MD5(di.cd_dialogo_inscricao::TEXT) AS cd_certificado,
						   TO_CHAR(di.dt_envio_certificado, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_certificado,
						   CASE WHEN di.fl_presente = 'S' 
						        THEN REPLACE(d.certificado,'{CD_CERTIFICADO_MD5}',MD5(di.cd_dialogo_inscricao::TEXT)) 
								ELSE ''
						   END AS link_certificado
					  FROM acs.dialogo_inscricao di
					  JOIN acs.dialogo d
					    ON d.cd_dialogo = di.cd_dialogo
					 WHERE di.dt_exclusao IS NULL
						".(intval($args['cd_dialogo']) > 0 ? "AND di.cd_dialogo = ".intval($args['cd_dialogo']) : "")."					       
						".(trim($args['fl_presente']) != "" ? "AND di.fl_presente = '".$args["fl_presente"]."'"  : "")."					       
		          ";
		#echo "<pre style='text-align:left;'>$qr_sql</pre>"; exit;				  
		$result = $this->db->query($qr_sql);
	}
	
	function inscricao(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_dialogo_inscricao, 
					       cd_dialogo, 
						   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
						   nome, 
						   cargo, 
						   empresa, 
						   endereco, 
						   numero, 
						   complemento, 
						   bairro, 
						   cidade, 
						   uf, 
						   cep, 
						   telefone, 
						   telefone_ramal, 
						   fax, 
						   fax_ramal, 
						   telefone_ddd, 
						   fax_ddd, 
						   celular_ddd, 
						   celular, 
						   email, 
						   cd_empresa, 
						   cd_registro_empregado, 
						   seq_dependencia, 
						   dt_exclusao, 
						   cd_usuario_exclusao, 
						   fl_presente,
						   MD5(cd_dialogo_inscricao::TEXT) AS cd_certificado
					  FROM acs.dialogo_inscricao
				     WHERE cd_dialogo_inscricao = ".intval($args['cd_dialogo_inscricao'])."
					   AND dt_exclusao IS NULL
		          ";
		#echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function inscricaoExcluir(&$result, $args=array())
	{
		if(intval($args['cd_dialogo_inscricao']) > 0)
		{
			$qr_sql = " 
						UPDATE acs.dialogo_inscricao
						   SET dt_exclusao = CURRENT_TIMESTAMP,
						       cd_usuario_exclusao  = ".$args['cd_usuario']."
						 WHERE cd_dialogo_inscricao = ".intval($args['cd_dialogo_inscricao'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}		
	
	function setPresente($args = Array())
	{
		$e=array();
		$query = $this->db->query("
									UPDATE acs.dialogo_inscricao
			                           SET fl_presente = ".(trim($args['fl_presente']) == "" ? "DEFAULT" : "'".$args['fl_presente']."'")."
			                         WHERE cd_dialogo_inscricao = ".intval($args['cd_dialogo_inscricao'])."
		                          ");
		
		if($query)
		{
			return true;
		}
		else
		{
			return false;
		}	
	}
	
	function enviaCertificado($args = Array())
	{
		$qr_sql = "
					SELECT di.cd_dialogo_inscricao, 
					       di.cd_dialogo, 
						   TO_CHAR(di.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
						   UPPER(funcoes.remove_acento(di.nome)) AS nome, 
						   di.cargo, 
						   di.empresa, 
						   di.endereco, 
						   di.numero, 
						   di.complemento, 
						   di.bairro, 
						   di.cidade, 
						   di.uf, 
						   di.cep, 
						   di.telefone, 
						   di.telefone_ramal, 
						   di.fax, 
						   di.fax_ramal, 
						   di.telefone_ddd, 
						   di.fax_ddd, 
						   di.celular_ddd, 
						   di.celular, 
						   di.email,
						   CASE WHEN di.email like '%@%' THEN 'S' ELSE 'N' END AS fl_email,
						   di.cd_empresa, 
						   di.cd_registro_empregado, 
						   di.seq_dependencia, 
						   di.dt_exclusao, 
						   di.cd_usuario_exclusao, 
						   di.fl_presente,
						   MD5(di.cd_dialogo_inscricao::TEXT) AS cd_certificado,
						   TO_CHAR(di.dt_envio_certificado, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_certificado,
						   CASE WHEN di.fl_presente = 'S' 
						        THEN funcoes.gera_link(REPLACE(d.certificado,'{CD_CERTIFICADO_MD5}',MD5(di.cd_dialogo_inscricao::TEXT)), di.cd_empresa, di.cd_registro_empregado, di.seq_dependencia)
								ELSE ''
						   END AS link_certificado,
						   d.cd_dialogo,
						   d.ds_dialogo
					  FROM acs.dialogo_inscricao di
					  JOIN acs.dialogo d
					    ON d.cd_dialogo = di.cd_dialogo
					 WHERE di.dt_exclusao IS NULL
					   AND MD5(di.cd_dialogo_inscricao::TEXT) = '".$args['cd_certificado']."'
		          ";
		$ob_resul = $this->db->query($qr_sql);		
		$ar_reg = $ob_resul->row_array();
		
		$e=array();
		$query = $this->db->query("
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
	   cd_evento,
	   tp_email
	 ) 
VALUES 
	 (
	   CURRENT_TIMESTAMP,
	   '".$ar_reg['ds_dialogo']."',
	   '".$ar_reg['email']."',     
	   '',
	   'coliveira@eletroceee.com.br',                      
	   'Certificado: ".$ar_reg['ds_dialogo']."',
'Prezado(a): ".$ar_reg['nome'].".

Agradecemos a sua participação no ".$ar_reg['ds_dialogo'].".

Aproveitamos a oportunidade para disponibilizar, no link abaixo, seu certificado de participação no Diálogo:

".$ar_reg['link_certificado']."

Atenciosamente, 

Fundação CEEE - Previdência Privada
www.fundacaoceee.com.br
0800 51 2596		

**** ATENÇÃO ****
Este e-mail é somente para leitura.
Caso queira falar conosco clique no link abaixo:
http://www.fundacaoceee.com.br/fale_conosco.php
',
	   ".(trim($ar_reg['cd_empresa']) == "" ? "DEFAULT" : intval($ar_reg['cd_empresa'])).",
	   ".(intval($ar_reg['cd_registro_empregado']) == 0 ? "DEFAULT" : intval($ar_reg['cd_registro_empregado'])).",
	   ".(trim($ar_reg['seq_dependencia']) == "" ? "DEFAULT" : intval($ar_reg['seq_dependencia'])).",
		73,
		'F'
		);	
		
UPDATE acs.dialogo_inscricao
   SET dt_envio_certificado = CURRENT_TIMESTAMP
 WHERE MD5(cd_dialogo_inscricao::TEXT) = '".$args['cd_certificado']."';
		                          ");
		
		if($query)
		{
			return true;
		}
		else
		{
			return false;
		}	
	}	
	
	/*	
	#### AJUSTAR PARA PARAMETROS ####
	function enviaCertificadoLista($args = Array())
	{
		$qr_sql = "
					SELECT cd_dialogo_inscricao, 
					       cd_dialogo, 
						   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
						   nome, 
						   cargo, 
						   empresa, 
						   endereco, 
						   numero, 
						   complemento, 
						   bairro, 
						   cidade, 
						   uf, 
						   cep, 
						   telefone, 
						   telefone_ramal, 
						   fax, 
						   fax_ramal, 
						   telefone_ddd, 
						   fax_ddd, 
						   celular_ddd, 
						   celular, 
						   email, 
						   cd_empresa, 
						   cd_registro_empregado, 
						   seq_dependencia, 
						   dt_exclusao, 
						   cd_usuario_exclusao, 
						   fl_presente,
						   MD5(di.cd_dialogo_inscricao::TEXT) AS cd_certificado
					  FROM acs.dialogo_inscricao di
					 WHERE di.fl_presente = 'S'
					   AND cd_dialogo = 1
					   AND email like('%@%')
		          ";
		$result = $this->db->query($qr_sql);
		$ar_reg = $result->result_array();

		$qr_sql = "";
		foreach( $ar_reg as $item )
		{		
		$qr_sql.= "
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
	   cd_evento,
	   tp_email
	 ) 
VALUES 
	 (
	   CURRENT_TIMESTAMP,
	   '2º Diálogo Institucional - Fundação CEEE',
	   '".$item['email']."',     
	   '',
	   'coliveira@eletroceee.com.br',                      
	   'Certificado',
							   
'Prezado(a): ".$item['nome']."

Agradecemos a sua participação no 2º Diálogo Institucional Fundação CEEE.

Esperamos que os assuntos debatidos neste evento sejam úteis para o esclarecimento de possíveis dúvidas sobre o contrato previdenciário.

Aproveitamos a oportunidade para disponibilizar, no link abaixo, seu certificado de participação no Diálogo:

' 
|| 
(SELECT funcoes.gera_link('https://www.fundacaoceee.com.br/dialogo_2/certificado.php?i=".$item['cd_certificado']."',
		".$item['cd_empresa'].",
		".$item['cd_registro_empregado'].",
		".$item['seq_dependencia'].")) 
|| 
'

Atenciosamente, 

Fundação CEEE - Previdência Privada
www.fundacaoceee.com.br
0800 51 2596		

**** ATENÇÃO ****
Este e-mail é somente para leitura.
Caso queira falar conosco clique no link abaixo:
http://www.fundacaoceee.com.br/fale_conosco.php
',
		".$item['cd_empresa'].",
		".$item['cd_registro_empregado'].",
		".$item['seq_dependencia'].",
		73,
		'F'
		);		
									UPDATE acs.dialogo_inscricao
			                           SET dt_envio_certificado = CURRENT_TIMESTAMP
			                         WHERE MD5(cd_dialogo_inscricao::TEXT) = '".$item['cd_certificado']."';
		                          ";		
		}
		
		

		$query = $this->db->query($qr_sql);
		if($query)
		{
			return true;
		}
		else
		{
			return false;
		}	
	}	
	*/
}
?>