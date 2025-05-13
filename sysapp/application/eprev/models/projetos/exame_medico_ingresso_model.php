<?php
class Exame_medico_ingresso_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function exameMedico(&$result, $args=array())
	{
		$qr_sql = "
					SELECT emi.cd_exame_medico_ingresso, 
					       COALESCE(p.nome,emi.nome) AS nome, 
						   emi.cd_empresa, 
						   emi.cd_registro_empregado, 
						   emi.seq_dependencia, 
						   emi.telefone, 
						   emi.celular, 
						   emi.telefone_comercial, 
						   COALESCE(emi.email,COALESCE(p.email,p.email_profissional)) AS email,
                           emi.pedido_inscricao_local, 
						   TO_CHAR(emi.dt_envio_exame,'DD/MM/YYYY HH24:MI') AS dt_envio_exame,						   
						   emi.cd_usuario_envio_exame, 
						   uce.nome AS ds_usuario_envio_exame,
						   TO_CHAR(emi.dt_recebido_exame,'DD/MM/YYYY HH24:MI') AS dt_recebido_exame,						   						   
						   emi.cd_usuario_recebido_exame, 
						   ucr.nome AS ds_usuario_recebido_exame,
						   TO_CHAR(emi.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   emi.cd_usuario_inclusao, 
						   uci.nome AS ds_usuario_inclusao,
						   TO_CHAR(emi.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao,
						   emi.cd_usuario_exclusao,
						   ucd.nome AS ds_usuario_exclusao
                      FROM projetos.exame_medico_ingresso emi
					  LEFT JOIN public.participantes p
					    ON p.cd_empresa            = emi.cd_empresa
					   AND p.cd_registro_empregado = emi.cd_registro_empregado
					   AND p.seq_dependencia       = emi.seq_dependencia
					  LEFT JOIN projetos.usuarios_controledi uce
					    ON uce.codigo = emi.cd_usuario_envio_exame
					  LEFT JOIN projetos.usuarios_controledi ucr
					    ON ucr.codigo = emi.cd_usuario_envio_exame	
					  LEFT JOIN projetos.usuarios_controledi uci
					    ON uci.codigo = emi.cd_usuario_inclusao	
					  LEFT JOIN projetos.usuarios_controledi ucd
					    ON ucd.codigo = emi.cd_usuario_exclusao						
					 WHERE emi.cd_exame_medico_ingresso = ".intval($args["cd_exame_medico_ingresso"])."
		          ";

		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function exameMedicoSalvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_exame_medico_ingresso']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE projetos.exame_medico_ingresso
						   SET nome                   = ".(trim($args['nome']) == "" ? "DEFAULT" : "'".$args['nome']."'").",
							   cd_empresa             = ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : $args['cd_empresa']).",
							   cd_registro_empregado  = ".(intval($args['cd_registro_empregado']) == 0 ? "DEFAULT" : $args['cd_registro_empregado']).",
							   seq_dependencia        = ".(trim($args['seq_dependencia']) == "" ? "DEFAULT" : $args['seq_dependencia']).",
							   telefone               = ".(trim($args['telefone']) == "" ? "DEFAULT" : "'".$args['telefone']."'").",
							   celular                = ".(trim($args['celular']) == "" ? "DEFAULT" : "'".$args['celular']."'").",
							   telefone_comercial     = ".(trim($args['telefone_comercial']) == "" ? "DEFAULT" : "'".$args['telefone_comercial']."'").",
							   email                  = ".(trim($args['email']) == "" ? "DEFAULT" : "'".$args['email']."'").",
							   pedido_inscricao_local = ".(trim($args['pedido_inscricao_local']) == "" ? "DEFAULT" : "'".$args['pedido_inscricao_local']."'")."
						 WHERE cd_exame_medico_ingresso = ".intval($args['cd_exame_medico_ingresso'])."
					  ";		
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_exame_medico_ingresso']);	
		}
		else
		{
			##INSERT
			$new_id = intval($this->db->get_new_id("projetos.exame_medico_ingresso", "cd_exame_medico_ingresso"));
			$qr_sql = " 
						INSERT INTO projetos.exame_medico_ingresso
						     (
							   cd_exame_medico_ingresso, 
							   nome, 
							   cd_empresa, 
							   cd_registro_empregado, 
							   seq_dependencia, 
							   telefone, 
							   celular, 
							   telefone_comercial, 
							   email, 
							   pedido_inscricao_local, 
							   cd_usuario_inclusao
							 )
                        VALUES 
						     (
							   ".$new_id.",
							   ".(trim($args['nome']) == "" ? "DEFAULT" : "'".$args['nome']."'").",
							   ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : $args['cd_empresa']).",
							   ".(intval($args['cd_registro_empregado']) == 0 ? "DEFAULT" : $args['cd_registro_empregado']).",
							   ".(trim($args['seq_dependencia']) == "" ? "DEFAULT" : $args['seq_dependencia']).",
							   ".(trim($args['telefone']) == "" ? "DEFAULT" : "'".$args['telefone']."'").",
							   ".(trim($args['celular']) == "" ? "DEFAULT" : "'".$args['celular']."'").",
							   ".(trim($args['telefone_comercial']) == "" ? "DEFAULT" : "'".$args['telefone_comercial']."'").",
							   ".(trim($args['email']) == "" ? "DEFAULT" : "'".$args['email']."'").",
							   ".(trim($args['pedido_inscricao_local']) == "" ? "DEFAULT" : "'".$args['pedido_inscricao_local']."'").",
							   ".(intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario'])."
							 );			
					  ";
			$this->db->query($qr_sql);	
			$retorno = $new_id;			
		}
		
		#echo "<pre>$qr_sql</pre>";
		#exit;
		
		return $retorno;
	}	
	
	function exameMedicoListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT emi.cd_exame_medico_ingresso, 
					       COALESCE(p.nome,emi.nome) AS nome, 
						   emi.cd_empresa, 
						   emi.cd_registro_empregado, 
						   emi.seq_dependencia, 
						   emi.telefone, 
						   emi.celular, 
						   emi.telefone_comercial, 
						   COALESCE(emi.email,COALESCE(p.email,p.email_profissional)) AS email,
                           emi.pedido_inscricao_local, 
						   TO_CHAR(emi.dt_envio_exame,'DD/MM/YYYY HH24:MI') AS dt_envio_exame,						   
						   emi.cd_usuario_envio_exame, 
						   uce.nome AS ds_usuario_envio_exame,
						   TO_CHAR(emi.dt_recebido_exame,'DD/MM/YYYY HH24:MI') AS dt_recebido_exame,						   						   
						   emi.cd_usuario_recebido_exame, 
						   ucr.nome AS ds_usuario_recebido_exame,
						   TO_CHAR(emi.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   emi.cd_usuario_inclusao, 
						   uci.nome AS ds_usuario_inclusao,
						   TO_CHAR(emi.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao,
						   emi.cd_usuario_exclusao,
						   ucd.nome AS ds_usuario_exclusao
                      FROM projetos.exame_medico_ingresso emi
					  LEFT JOIN public.participantes p
					    ON p.cd_empresa            = emi.cd_empresa
					   AND p.cd_registro_empregado = emi.cd_registro_empregado
					   AND p.seq_dependencia       = emi.seq_dependencia
					  LEFT JOIN projetos.usuarios_controledi uce
					    ON uce.codigo = emi.cd_usuario_envio_exame
					  LEFT JOIN projetos.usuarios_controledi ucr
					    ON ucr.codigo = emi.cd_usuario_envio_exame	
					  LEFT JOIN projetos.usuarios_controledi uci
					    ON uci.codigo = emi.cd_usuario_inclusao	
					  LEFT JOIN projetos.usuarios_controledi ucd
					    ON ucd.codigo = emi.cd_usuario_exclusao		
					 WHERE emi.dt_exclusao IS NULL
		          ";

		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function exameMedicoExcluir(&$result, $args=array())
	{
		if(intval($args['cd_exame_medico_ingresso']) > 0)
		{
			$qr_sql = " 
						UPDATE projetos.exame_medico_ingresso
						   SET dt_exclusao         = CURRENT_TIMESTAMP,
						       cd_usuario_exclusao = ".$args['cd_usuario']."
						 WHERE cd_exame_medico_ingresso = ".intval($args['cd_exame_medico_ingresso'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}	
	
	function exameMedicoEnviar(&$result, $args=array())
	{
		if(intval($args['cd_exame_medico_ingresso']) > 0)
		{
			$qr_sql = " 
						UPDATE projetos.exame_medico_ingresso
						   SET dt_envio_exame         = CURRENT_TIMESTAMP,
						       cd_usuario_envio_exame = ".$args['cd_usuario']."
						 WHERE cd_exame_medico_ingresso = ".intval($args['cd_exame_medico_ingresso']).";
						 
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
	   'Exame Medico Ingresso',
	   'rdornelles@eletroceee.com.br',     
	   'vdornelles@eletroceee.com.br;mpozzebon@eletroceee.com.br',
	   'coliveira@eletroceee.com.br',                      
	   'Agendar Exame Médico para Ingresso',
'Foi registrado solicitação de agendamento de Exame Médico para Ingresso.

Clique no link abaixo para visualizar as informações sobre a solicitação.

http://www.e-prev.com.br/cieprev/index.php/ecrm/exame_medico_ingresso/detalhe/".intval($args['cd_exame_medico_ingresso'])."


',
		(SELECT COALESCE(cd_empresa,9999)FROM projetos.exame_medico_ingresso WHERE cd_exame_medico_ingresso = ".intval($args['cd_exame_medico_ingresso'])."),
		(SELECT COALESCE(cd_registro_empregado,0) FROM projetos.exame_medico_ingresso WHERE cd_exame_medico_ingresso = ".intval($args['cd_exame_medico_ingresso'])."),
		(SELECT COALESCE(seq_dependencia,0) FROM projetos.exame_medico_ingresso WHERE cd_exame_medico_ingresso = ".intval($args['cd_exame_medico_ingresso'])."),
		75,
		'F'
		);							 
					  ";			
			$this->db->query($qr_sql);
		}
	}

	function exameMedicoReceber(&$result, $args=array())
	{
		if(intval($args['cd_exame_medico_ingresso']) > 0)
		{
			$qr_sql = " 
						UPDATE projetos.exame_medico_ingresso
						   SET dt_recebido_exame         = CURRENT_TIMESTAMP,
						       cd_usuario_recebido_exame = ".$args['cd_usuario']."
						 WHERE cd_exame_medico_ingresso = ".intval($args['cd_exame_medico_ingresso'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}	

	function acompanhamentoListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT emia.cd_exame_medico_ingresso_acompanhamento, 
					       emia.cd_exame_medico_ingresso, 
						   emia.acompanhamento,
						   TO_CHAR(emia.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   uc.nome AS ds_usuario_inclusao
					  FROM projetos.exame_medico_ingresso_acompanhamento emia
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = emia.cd_usuario_inclusao	
					 WHERE emia.cd_exame_medico_ingresso = ".intval($args['cd_exame_medico_ingresso'])."
					   AND emia.dt_exclusao IS NULL
		          ";

		#echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}

	function acompanhamentoSalvar(&$result, $args=array())
	{
		if(intval($args['cd_exame_medico_ingresso']) > 0)
		{
			$new_id = intval($this->db->get_new_id("projetos.exame_medico_ingresso_acompanhamento", "cd_exame_medico_ingresso_acompanhamento"));
			$qr_sql = " 
						INSERT INTO projetos.exame_medico_ingresso_acompanhamento
							 (
							   cd_exame_medico_ingresso_acompanhamento, 
							   cd_exame_medico_ingresso, 
							   acompanhamento, 
							   cd_usuario_inclusao
							 )
						VALUES 
							 (
							   ".$new_id.",
							   ".intval($args['cd_exame_medico_ingresso']).",
							   ".(trim($args['acompanhamento']) == "" ? "DEFAULT" : "'".$args['acompanhamento']."'").",
							   ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : $args['cd_usuario'])."
							 );			
					  ";
			$this->db->query($qr_sql);	
			#echo "<pre>$qr_sql</pre>";exit;
		}
		return true;
	}	
}
?>