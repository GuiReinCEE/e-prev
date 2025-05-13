<?php
class Inscritos_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		
		$qr_sql = "
			SELECT i.cd_registro_empregado,
		           i.nome,
		           TO_CHAR(i.dt_inscricao, 'DD/MM/YYYY') AS dt_inscricao,
		           TO_CHAR(i.dt_email_confirmado, 'DD/MM/YYYY') AS dt_email_confirmado,
		           TO_CHAR(i.dt_senge_confirmado, 'DD/MM/YYYY') AS dt_senge_confirmado,
		           TO_CHAR(i.dt_documentacao_confirmada, 'DD/MM/YYYY') AS dt_documentacao_confirmada
		      FROM expansao.inscritos i
		      LEFT JOIN participantes p
		        ON p.cd_empresa = i.cd_empresa
		       AND p.cd_registro_empregado = i.cd_registro_empregado
		       AND p.seq_dependencia = i.cd_sequencia
		     WHERE i.dt_exclusao IS NULL
			   ".(trim($args['situacao']) == 1 ? "AND i.dt_email_contribuicao IS NULL" : "")."
			   ".(trim($args['situacao']) == 2 ? "AND i.dt_email_contribuicao IS NOT NULL" : "")."
			   ".(trim($args['situacao']) == 3 ? "AND i.dt_inscricao IS NOT NULL 
												  AND i.dt_email_confirmado IS NOT NULL 
												  AND i.dt_senge_confirmado IS NOT NULL 
												  AND i.dt_documentacao_confirmada IS NOT NULL
												  AND p.cd_plano = 7" : "").";";
		
		$result = $this->db->query($qr_sql);
	}
	
	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT i.cd_registro_empregado, 
			       i.cd_empresa, 
				   i.cd_sequencia AS seq_dependencia,  
				   i.nome, 
				   funcoes.format_cpf(i.cpf::BIGINT) AS cpf,
				   i.rg, 
				   i.crea, 
				   i.emissor, 
				   TO_CHAR(i.dt_emissao, 'DD/MM/YYYY') AS dt_emissao, 
				   TO_CHAR(i.dt_adesao_instituidor, 'DD/MM/YYYY') AS dt_adesao_instituidor, 
				   TO_CHAR(i.dt_alteracao, 'DD/MM/YYYY') AS dt_alteracao, 
				   i.cd_instituicao, 
				   i.cd_agencia, 
				   i.conta_bco, 
				   i.sexo,  
				   TO_CHAR(i.dt_nascimento, 'DD/MM/YYYY') AS dt_nascimento, 
				   i.cd_estado_civil, 
				   i.cd_grau_instrucao, 
				   i.cd_registro_patroc, 
				   i.seq_registro_patroc, 
				   i.categoria,  
				   i.matricula_titular, 
				   i.nome_pai, 
				   i.nome_mae, 
				   i.ip_inscricao, 
				   i.usuario_alteracao, 
				   i.opt_irpf,
				   TO_CHAR(i.dt_documentacao_confirmada, 'DD/MM/YYYY HH24:MI:SS') AS dt_documentacao_confirmada,
				   i.nome_titular,
				   i.endereco, 
				   i.bairro, 
				   i.cidade, 
				   i.uf, 
				   i.sigla_pais,
				   i.cep, 
				   i.complemento_cep, 
				   i.ddd, 
				   i.telefone, 
				   i.ramal, 
				   i.ddd_cel, 
				   i.celular,
				   i.ddd_fax, 
				   i.fax, 
				   i.email,
				   c.nome_cidade,
				   f.razao_social_nome AS nome_banco,
				   i.cd_pacote,
				   g.descricao_grau_instrucao,
				   i.naturalidade,
				   i.nacionalidade,
				   i.cep || '-' || i.complemento_cep AS cep_complemento,
				   '(' || i.ddd || ') ' || i.telefone AS ddd_telefone,
				   i.cd_instituicao || ' - ' || f.razao_social_nome AS banco,
				   TO_CHAR(i.dt_inscricao, 'DD/MM/YYYY') AS dt_inscricao,
				   TO_CHAR(i.dt_senge_confirmado, 'DD/MM/YYYY') AS dt_senge_confirmado,
				   TO_CHAR(i.dt_email_confirmado, 'DD/MM/YYYY') AS dt_email_confirmado,
				   (SELECT COUNT(*)
                      FROM expansao.registros_documentos rc
                      WHERE rc.cd_registro_empregado = i.cd_registro_empregado 
					    AND rc.cd_empresa            = i.cd_empresa
						AND rc.cd_doc = 1) AS doc_1,
				   (SELECT COUNT(*)
                      FROM expansao.registros_documentos rc
                      WHERE rc.cd_registro_empregado = i.cd_registro_empregado 
					    AND rc.cd_empresa            = i.cd_empresa
						AND rc.cd_doc = 225) AS doc_225
			  FROM expansao.inscritos i
			  LEFT JOIN expansao.cidades c
			    ON c.cd_municipio_ibge = i.cidade::integer
			  LEFT JOIN instituicao_financeiras f
			    ON f.cd_instituicao = i.cd_instituicao
		      LEFT JOIN grau_instrucaos g 
			    ON g.cd_grau_de_instrucao = i.cd_grau_instrucao
			 WHERE i.cd_registro_empregado = ".intval($args['cd_registro_empregado'])." 
			   AND i.cd_empresa            = ".intval($args['cd_empresa'])." 
			   AND i.cd_sequencia          = ".intval($args['seq_dependencia']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function banco( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_instituicao AS value, 
			       razao_social_nome AS text
              FROM instituicao_financeiras
             WHERE cd_agencia = '0' 
	           AND status <> 'I'
             ORDER BY razao_social_nome;";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function agencia( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_agencia AS value, 
			       razao_social_nome AS text
		      FROM instituicao_financeiras
			 WHERE 1 = 1
			  ".(trim($args['cd_instituicao']) != '' ? "AND cd_instituicao = ".intval($args['cd_instituicao'])." AND status <> 'I'" : "")."
			 ORDER BY razao_social_nome";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function estado_civil( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_estado_civil AS value, 
			       descricao_estado_civil AS text
			  FROM estado_civils	
			 ORDER BY descricao_estado_civil";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function grau_instrucao( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_grau_de_instrucao AS value, 
			       descricao_grau_instrucao AS text
			  FROM grau_instrucaos	
			 ORDER BY descricao_grau_instrucao";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE expansao.inscritos
			   SET nome              = ".(trim($args['nome']) != '' ? str_escape($args['nome']) : "DEFAULT").",
			       cpf               = ".(trim($args['cpf']) != '' ? trim($args['cpf']) : "DEFAULT").",
				   rg                = ".(trim($args['rg']) != '' ? intval($args['rg']) : "DEFAULT").",
				   emissor           = ".(trim($args['emissor']) != '' ? str_escape($args['emissor']) : "DEFAULT").",
				   dt_emissao        = ".(trim($args['dt_emissao']) != '' ? "TO_DATE('".trim($args['dt_emissao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   crea              = ".(trim($args['crea']) != '' ? intval($args['crea']) : "DEFAULT").",
				   cd_instituicao    = ".(trim($args['cd_instituicao']) != '' ? intval($args['cd_instituicao']) : "DEFAULT").",
				   cd_agencia        = ".(trim($args['cd_agencia']) != '' ? str_escape($args['cd_agencia']) : "DEFAULT").",
				   conta_bco         = ".(trim($args['conta_bco']) != '' ? str_escape($args['conta_bco']) : "DEFAULT").",
				   sexo              = ".(trim($args['sexo']) != '' ? str_escape($args['sexo']) : "DEFAULT").",
				   dt_nascimento     = ".(trim($args['dt_nascimento']) != '' ? "TO_DATE('".trim($args['dt_nascimento'])."', 'DD/MM/YYYY')" : "DEFAULT").",
				   cd_estado_civil   = ".(trim($args['cd_estado_civil']) != '' ? intval($args['cd_estado_civil']) : "DEFAULT").",
				   cd_grau_instrucao = ".(trim($args['cd_grau_instrucao']) != '' ? intval($args['cd_grau_instrucao']) : "DEFAULT").",
				   nome_pai          = ".(trim($args['nome_pai']) != '' ? str_escape($args['nome_pai']) : "DEFAULT").",
				   nome_mae          = ".(trim($args['nome_mae']) != '' ? str_escape($args['nome_mae']) : "DEFAULT").",
				   opt_irpf          = ".(trim($args['opt_irpf']) != '' ? intval($args['opt_irpf']) : "DEFAULT")."
			 WHERE cd_empresa            = ".intval($args['cd_empresa'])."
			   AND cd_registro_empregado = ".intval($args['cd_registro_empregado']).";";
			
			$result = $this->db->query($qr_sql);
	}
	
	function estado( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT sigla AS value, 
			       nome AS text
			  FROM expansao.estados	
			 ORDER BY nome";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function cidade( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_municipio_ibge AS value, 
			       nome_cidade AS text
			  FROM expansao.cidades
             WHERE sigla_uf = '".trim($args['uf'])."'			  
			 ORDER BY nome_cidade";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function contato( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cd_email,
				   TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
				   de,
				   para,
				   cc,
				   assunto,
				   dt_email_enviado
			  FROM projetos.envia_emails
             WHERE para = '".trim($args['email'])."'			  
			 ORDER BY cd_email DESC";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_contato( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE expansao.inscritos
			   SET endereco        = ".(trim($args['endereco']) != '' ? str_escape($args['endereco']) : "DEFAULT").",
			       bairro          = ".(trim($args['bairro']) != '' ? str_escape($args['bairro']) : "DEFAULT").",
				   uf              = ".(trim($args['uf']) != '' ? str_escape($args['uf']) : "DEFAULT").",
				   cidade          = ".(trim($args['cidade']) != '' ? str_escape($args['cidade']) : "DEFAULT").",
				   cep             = ".(trim($args['cep']) != '' ? trim($args['cep']) : "DEFAULT").",
				   complemento_cep = ".(trim($args['complemento_cep']) != '' ? trim($args['complemento_cep']) : "DEFAULT").",
				   ddd             = ".(trim($args['ddd']) != '' ? trim($args['ddd']) : "DEFAULT").",
				   telefone        = ".(trim($args['telefone']) != '' ? trim($args['telefone']) : "DEFAULT").",
				   ddd_cel         = ".(trim($args['ddd_cel']) != '' ? trim($args['ddd_cel']) : "DEFAULT").",
				   celular         = ".(trim($args['celular']) != '' ? trim($args['celular']) : "DEFAULT").",
				   ddd_fax         = ".(trim($args['ddd_fax']) != '' ? trim($args['ddd_fax']) : "DEFAULT").",
				   fax             = ".(trim($args['fax']) != '' ? trim($args['fax']) : "DEFAULT").",
				   ramal           = ".(trim($args['ramal']) != '' ? trim($args['ramal']) : "DEFAULT").",
				   email           = ".(trim($args['email']) != '' ? str_escape($args['email']) : "DEFAULT")."
			 WHERE cd_empresa            = ".intval($args['cd_empresa'])."
		       AND cd_registro_empregado = ".intval($args['cd_registro_empregado']).";";
			
		$result = $this->db->query($qr_sql);
	}
	
	function senha_inscrito( &$result, $args=array() )
	{
		$qr_sql = "
			 SELECT COALESCE(c.codigo_345, i.codigo_345) AS codigo_345, 
					i.nome, 
					i.email, 
					i.cd_registro_empregado, 
					i.cpf, 
					i.rg 
			   FROM expansao.inscritos i
			   LEFT JOIN  participantes_ccin c 
				 ON c.cd_registro_empregado = i.cd_registro_empregado 
				AND c.cd_empresa = i.cd_empresa 
			  WHERE i.cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
			    AND i.cd_empresa            = ".intval($args['cd_empresa']).";";
			  
		$result = $this->db->query($qr_sql);
	}
	
	function envia_email( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO projetos.envia_emails 
			     (
					dt_envio,
					de,
					para,
					assunto,
					texto,
					cd_empresa,
					cd_registro_empregado
				 )
			VALUES
			     (
					CURRENT_TIMESTAMP,
					'".trim($args['de'])."',
					'".trim($args['para'])."',
					'".trim($args['assunto'])."',
					'".trim($args['texto'])."',
					".(trim($args['cd_empresa']) != '' ?  $args['cd_empresa'] : 'DEFAULT').",
					".(trim($args['cd_registro_empregado']) != '' ?  $args['cd_registro_empregado'] : 'DEFAULT')."
				 )";
			
			$result = $this->db->query($qr_sql);
	}
	
	function mensagem_pedido_inscricao( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT email
			  FROM projetos.eventos
			 WHERE cd_evento = 14";
		
		$result = $this->db->query($qr_sql);
	}
	
	function peculio( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT nome, 
			       percentual
			  FROM expansao.peculio 
			 WHERE cd_empresa            = ".intval($args['cd_empresa'])."
			   AND cd_registro_empregado = ".intval($args['cd_registro_empregado']).";";
		
		$result = $this->db->query($qr_sql);		
	}
	
	function conteudo( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT conteudo, 
			       titulo
			  FROM projetos.conteudo_site 
			 WHERE cd_site    = 1
			   AND cd_materia = ".intval($args['cd_materia']).";";

		$result = $this->db->query($qr_sql);		
	}
	
	function taxa_adm( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT descricao, 
			       preco 
	          FROM pacotes
	         WHERE cd_plano = 7 
		       AND cd_empresa = ".intval($args['cd_empresa'])." 
	           AND tipo_cobranca = '".trim($args['tipo_cobranca'])."' 
	           AND dt_inicio = (SELECT MAX(dt_inicio) 
			                      FROM pacotes p
								  JOIN expansao.inscritos i
								    ON p.cd_plano = i.cd_plano 
								   AND p.cd_empresa = i.cd_empresa  
							     WHERE p.cd_empresa = ".intval($args['cd_empresa'])." 
								   AND p.cd_plano = 7 
								   AND DATE_TRUNC('month', dt_inicio) = DATE_TRUNC('month', CURRENT_DATE)) ;";
		
		$result = $this->db->query($qr_sql);		
	}
	
	function conta_contribuicao( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT dcc.banco, 
				   dcc.agencia, 
				   dcc.conta, 
				   dcc.vlr_debito
			  FROM expansao.debito_conta_contribuicao dcc
			 WHERE dcc.cd_empresa            = ".intval($args['cd_empresa'])." 
			   AND dcc.cd_registro_empregado = ".intval($args['cd_registro_empregado'])." 
			   AND dcc.seq_dependencia       = 0
			   AND dcc.num_seq = (SELECT MIN(dcc1.num_seq)
									FROM expansao.debito_conta_contribuicao dcc1
								   WHERE dcc1.cd_empresa            = dcc.cd_empresa
									 AND dcc1.cd_registro_empregado = dcc.cd_registro_empregado
									 AND dcc1.seq_dependencia       = dcc.seq_dependencia);";
		
		$result = $this->db->query($qr_sql);		
	}
	
	function documento( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT 	cd_doc, 
			        nro_via, 
					obrigatorio,
					TO_CHAR(dt_entrega, 'DD/MM/YYYY') AS dt_entrega, 
					TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
					CASE WHEN cd_doc = 1 THEN 'Carteira de Identidade / CIC'
					     WHEN cd_doc = 225 THEN 'Pedido de Inscrição'
				         ELSE ''
					END AS documento
			   FROM expansao.registros_documentos
			  WHERE cd_registro_empregado = ".intval($args['cd_registro_empregado'])." 
			    AND cd_empresa            = ".intval($args['cd_empresa']).";";
			
		$result = $this->db->query($qr_sql);	
	}
	
	function salvar_documento( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO expansao.registros_documentos
			     (
				   cd_empresa, 
				   cd_registro_empregado,
				   seq_dependencia, 
				   cd_doc, 
				   nro_via, 
				   dt_entrega, 
				   obrigatorio, 
				   dt_inclusao, 
				   usuario
				 )
			VALUES
			     (
					".intval($args['cd_empresa']).",
					".intval($args['cd_registro_empregado']).",
					".intval($args['seq_dependencia']).",
					".intval($args['cd_doc']).",
					1,
					TO_DATE('".trim($args['dt_entrega'])."', 'DD/MM/YYYY'),
					'S',
					CURRENT_DATE,
					".intval($args['cd_usuario'])."
				 )";
	
		$result = $this->db->query($qr_sql);	
	}
	
	function anexo( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT 	di.cd_inscritos_anexo,
					di.arquivo,
					di.arquivo_nome,
					TO_CHAR(di.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
					uc.nome
			   FROM expansao.inscritos_anexo di
			   JOIN projetos.usuarios_controledi uc
			     ON uc.codigo = di.cd_usuario_inclusao
			  WHERE di.cd_registro_empregado = ".intval($args['cd_registro_empregado'])." 
			    AND di.cd_empresa            = ".intval($args['cd_empresa'])."
				AND di.dt_exclusao IS NULL;";
			
		$result = $this->db->query($qr_sql);	
	}
	
	function salvar_anexo( &$result, $args=array() )
	{
		$qr_sql = "
			INSERT INTO expansao.inscritos_anexo
			     (
                   cd_empresa, 
				   cd_registro_empregado, 
				   cd_sequencia, 
                   arquivo, 
				   arquivo_nome, 
				   cd_usuario_inclusao
				 )
            VALUES 
			     (
				   ".intval($args['cd_empresa']).",
				   ".intval($args['cd_registro_empregado']).",
				   ".intval($args['cd_sequencia']).",
				   '".trim($args['arquivo'])."',
				   '".trim($args['arquivo_nome'])."',
				   ".intval($args['cd_usuario'])."
				 );";
				 
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_anexo( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE expansao.inscritos_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_inscritos_anexo = ".intval($args['cd_inscritos_anexo']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function confirmar( &$result, $args=array() )
	{
		$qr_sql = "
			UPDATE expansao.inscritos 
			   SET dt_documentacao_confirmada = CURRENT_TIMESTAMP
			 WHERE cd_empresa            = ".intval($args['cd_empresa'])."
			   AND cd_registro_empregado = ".$args['cd_registro_empregado'].";";
			 
		$result = $this->db->query($qr_sql);
	}

}
?>