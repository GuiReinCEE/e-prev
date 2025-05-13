<?php
class Eventos_institucionais_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function evento(&$result, $args=array())
	{
		$qr_sql = "
                    SELECT e.cd_evento, 
						   TO_CHAR(e.dt_inicio,'DD/MM/YYYY') AS dt_inicio, 
						   TO_CHAR(e.dt_inicio,'HH24:MI') AS hr_inicio,
						   e.cd_tipo, 
						   e.nome, 
						   e.tipo_divulgacao, 
						   e.cd_cidade, 
						   e.dt_alteracao, 
						   e.local_evento, 
						   e.dt_fim, 
						   e.email_lembrete, 
						   e.lembrete_1hora, 
						   e.lembrete_vespera, 
						   e.texto_lembrete, 
						   e.agenda, 
						   e.dt_marcacao_agenda, 
						   e.dt_exclusao, 
						   e.usu_exclusao,
						   e.qt_inscricao,
						   e.texto_encerramento,
						   TO_CHAR(e.dt_ini_inscricao,'DD/MM/YYYY') AS dt_ini_inscricao, 
						   TO_CHAR(e.dt_ini_inscricao,'HH24:MI') AS hr_ini_inscricao,	
						   TO_CHAR(e.dt_fim_inscricao,'DD/MM/YYYY') AS dt_fim_inscricao, 
						   TO_CHAR(e.dt_fim_inscricao,'HH24:MI') AS hr_fim_inscricao,
						   img_inscricao,
						   img_confirma,
						   img_encerra,
                           email_texto,
                           email_assunto,
                           fl_acompanhante,
                           fl_arquivo,
                           fl_observacao,
                           ds_observacao,
					       certificado_img_frente,
					       certificado_img_verso,
					       certificado_nome_pos_x,
					       certificado_nome_pos_y,
						   REPLACE(certificado_nome_cor,'#','') AS certificado_nome_cor,
					       certificado_nome_fonte,
					       certificado_nome_tamanho,
					       certificado_nome_alinha,						   
						   (SELECT COUNT(*) 
						      FROM projetos.eventos_institucionais_inscricao i 
							 WHERE i.cd_eventos_institucionais = e.cd_evento) AS qt_inscrito
					  FROM projetos.eventos_institucionais e  
					 WHERE e.cd_evento = ".intval($args["cd_evento"])."
		          ";
		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function listar( &$result, $args=array() )
	{
		if(!isset($args["cd_tipo"])){ $args["cd_tipo"]=''; }

		$qr_sql = " 
					SELECT e.cd_evento,
						   TO_CHAR(e.dt_inicio,'DD/MM/YYYY') AS dt_inicio,
						   e.nome,
						   c.nome_cidade,
						   e.local_evento,
						   (SELECT COUNT(*) 
							  FROM projetos.eventos_institucionais_inscricao i 
							 WHERE i.cd_eventos_institucionais = e.cd_evento
							   AND i.dt_exclusao IS NULL) AS qt_inscrito,
						   (SELECT COUNT(*) 
							  FROM projetos.eventos_institucionais_inscricao ip 
							 WHERE ip.cd_eventos_institucionais = e.cd_evento
							   AND ip.fl_presente = 'S'
							   AND ip.dt_exclusao IS NULL) AS qt_presente							   
					  FROM projetos.eventos_institucionais e
					  LEFT JOIN expansao.cidades c 
						ON e.cd_cidade = c.cd_municipio_ibge 
					   AND c.sigla_uf  = 'RS'
					  LEFT JOIN public.listas l 
						ON l.codigo = e.cd_tipo
					 WHERE e.dt_exclusao IS NULL
					   ".(trim($args["nome"]) != "" ? "AND UPPER(funcoes.remove_acento(e.nome)) LIKE UPPER(funcoes.remove_acento('%".str_replace(" ","%",trim($args["nome"]))."%'))" : "")."
					   ".(trim($args["cd_tipo"]) != "" ? "AND e.cd_tipo='".trim($args["cd_tipo"])."'" : "")."
		          ";
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_evento']) > 0)
		{
			#### UPDATE ####
			$qr_sql = " 
						UPDATE projetos.eventos_institucionais
						   SET nome             = ".(trim($args['nome']) == "" ? "DEFAULT" : "'".$args['nome']."'").",
							   dt_inicio        = ".(trim($args['dt_inicio']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['dt_inicio']." ".$args['hr_inicio']."','DD/MM/YYYY HH24:MI')").",
							   qt_inscricao     = ".(intval($args['qt_inscricao']) == 0 ? "DEFAULT" : $args['qt_inscricao']).",
							   dt_ini_inscricao = ".(trim($args['dt_ini_inscricao']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['dt_ini_inscricao']." ".$args['hr_ini_inscricao']."','DD/MM/YYYY HH24:MI')").",
							   dt_fim_inscricao = ".(trim($args['dt_fim_inscricao']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['dt_fim_inscricao']." ".$args['hr_fim_inscricao']."','DD/MM/YYYY HH24:MI')").",
							   cd_cidade        = ".(intval($args['cd_cidade']) == 0 ? "DEFAULT" : $args['cd_cidade']).",
							   local_evento     = ".(trim($args['local_evento']) == "" ? "DEFAULT" : "'".$args['local_evento']."'").",
                               email_texto      = ".(trim($args['email_texto']) == "" ? "DEFAULT" : "'".$args['email_texto']."'").",
                               texto_encerramento = ".(trim($args['texto_encerramento']) == "" ? "DEFAULT" : "'".$args['texto_encerramento']."'").",
                               email_assunto	= ".(trim($args['email_assunto']) == "" ? "DEFAULT" : "'".$args['email_assunto']."'").",					   
                               fl_acompanhante	= ".(trim($args['fl_acompanhante']) == "" ? "DEFAULT" : "'".$args['fl_acompanhante']."'").",					   
                               fl_arquivo	    = ".(trim($args['fl_arquivo']) == "" ? "DEFAULT" : "'".$args['fl_arquivo']."'").",						   
                               fl_observacao	= ".(trim($args['fl_observacao']) == "" ? "DEFAULT" : "'".$args['fl_observacao']."'").",						   
                               ds_observacao	= ".(trim($args['ds_observacao']) == "" ? "DEFAULT" : "'".$args['ds_observacao']."'").",			   
                               fl_participante	= ".(trim($args['fl_participante']) == "" ? "DEFAULT" : "'".$args['fl_participante']."'").",						   
                               ar_participante_tipo	= ".(((is_array($args['ar_participante_tipo'])) and (trim($args['fl_participante']) == "S")) ? "'".implode(",",$args['ar_participante_tipo'])."'" : "DEFAULT").",
                               participante_msg_valida	= ".(((trim($args['participante_msg_valida']) != "") and (trim($args['fl_participante']) == "S")) ? "'".trim($args['participante_msg_valida'])."'" : "DEFAULT")."						   
						 WHERE cd_evento = ".intval($args['cd_evento'])."			
					  ";		
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_evento']);	
		}
		else
		{
			#### INSERT ####
			$new_id = intval($this->db->get_new_id("projetos.eventos_institucionais", "cd_evento"));
			$qr_sql = " 
						INSERT INTO projetos.eventos_institucionais 
						     ( 
							   cd_evento,
							   nome,
							   dt_inicio,
							   qt_inscricao,
							   dt_ini_inscricao,
							   dt_fim_inscricao,
							   cd_cidade,
							   local_evento,
                               email_texto,
							   texto_encerramento,
                               email_assunto,
                               fl_acompanhante,
                               fl_arquivo,
                               fl_observacao,
                               ds_observacao,
							   cd_tipo,
							   fl_participante,
							   ar_participante_tipo,
							   participante_msg_valida,
							   cd_usuario_inclusao
			                 ) 
					    VALUES 
						     ( 
							   ".$new_id.",
							   ".(trim($args['nome']) == "" ? "DEFAULT" : "'".$args['nome']."'").",
							   ".(trim($args['dt_inicio']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['dt_inicio']." ".$args['hr_inicio']."','DD/MM/YYYY HH24:MI')").",
							   ".(intval($args['qt_inscricao']) == 0 ? "DEFAULT" : $args['qt_inscricao']).",
							   ".(trim($args['dt_ini_inscricao']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['dt_ini_inscricao']." ".$args['hr_ini_inscricao']."','DD/MM/YYYY HH24:MI')").",
							   ".(trim($args['dt_fim_inscricao']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['dt_fim_inscricao']." ".$args['hr_fim_inscricao']."','DD/MM/YYYY HH24:MI')").",
							   ".(intval($args['cd_cidade']) == 0 ? "DEFAULT" : $args['cd_cidade']).",
							   ".(trim($args['local_evento']) == "" ? "DEFAULT" : "'".$args['local_evento']."'").",
							   ".(trim($args['email_texto']) == "" ? "DEFAULT" : "'".$args['email_texto']."'").",
							   ".(trim($args['texto_encerramento']) == "" ? "DEFAULT" : "'".$args['texto_encerramento']."'").",
							   ".(trim($args['email_assunto']) == "" ? "DEFAULT" : "'".$args['email_assunto']."'").",
							   ".(trim($args['fl_acompanhante']) == "" ? "DEFAULT" : "'".$args['fl_acompanhante']."'").",
							   ".(trim($args['fl_arquivo']) == "" ? "DEFAULT" : "'".$args['fl_arquivo']."'").",
							   ".(trim($args['fl_observacao']) == "" ? "DEFAULT" : "'".$args['fl_observacao']."'").",
							   ".(trim($args['ds_observacao']) == "" ? "DEFAULT" : "'".$args['ds_observacao']."'").",
							   ".(trim($args['cd_tipo']) == "" ? "DEFAULT" : "'".$args['cd_tipo']."'").",
							   ".(trim($args['fl_participante']) == "" ? "DEFAULT" : "'".$args['fl_participante']."'").",
							   ".(((is_array($args['ar_participante_tipo'])) and (trim($args['fl_participante']) == "S")) ? "'".implode(",",$args['ar_participante_tipo'])."'" : "DEFAULT").",
							   ".(((trim($args['participante_msg_valida']) != "") and (trim($args['fl_participante']) == "S")) ? "'".trim($args['participante_msg_valida'])."'" : "DEFAULT").",
							   ".intval($args['cd_usuario'])."	
			                 );							
					  ";
			$this->db->query($qr_sql);	
			$retorno = $new_id;			
		}
		
		#echo "<pre>$qr_sql</pre>";
		#exit;
		
		return $retorno;
	}	
	
	function emailCertificadoEvento($args=array())
	{
		$qr_sql = "
                    SELECT i.cd_eventos_institucionais_inscricao
					  FROM projetos.eventos_institucionais_inscricao i 
					 WHERE i.cd_eventos_institucionais = ".intval($args["cd_evento"])."
					   AND i.dt_exclusao IS NULL
					   AND COALESCE(i.email,'') LIKE ('%@%')
					   AND i.fl_presente = 'S'
		          ";
		$ob_resul = $this->db->query($qr_sql);
		$ar_reg   = $ob_resul->result_array();
		
		foreach($ar_reg as $ar_item)
		{
			$this->emailCertificadoEventoIndividual(Array('cd_eventos_institucionais_inscricao' => $ar_item['cd_eventos_institucionais_inscricao']));
		}
	}

	function emailCertificadoEventoIndividual($args=array())
	{
		$qr_sql = "
                    SELECT UPPER(funcoes.remove_acento(i.nome)) AS nome,
					       i.email,
						   UPPER(e.nome) AS nome_evento,
                           i.cd_empresa,
			               i.cd_registro_empregado,
			               i.seq_dependencia,
						   funcoes.gera_link('https://www.fundacaoceee.com.br/evento_certificado.php?i=' || MD5(i.cd_eventos_institucionais_inscricao::TEXT), NULL::INTEGER, NULL::INTEGER, NULL::INTEGER) AS link_certificado
					  FROM projetos.eventos_institucionais_inscricao i 
					  JOIN projetos.eventos_institucionais e 
					    ON e.cd_evento = i.cd_eventos_institucionais
					 WHERE i.cd_eventos_institucionais_inscricao = ".intval($args["cd_eventos_institucionais_inscricao"])."
					   AND i.dt_exclusao IS NULL
					   AND COALESCE(i.email,'') LIKE ('%@%')
					   AND i.fl_presente = 'S'
		          ";
		$ob_resul = $this->db->query($qr_sql);
		$ar_reg   = $ob_resul->row_array();	
		if(count($ar_reg) > 0)
		{
			$email = "Prezado(a): ".$ar_reg['nome']."

Clique no link abaixo para imprimir o certificado do evento ".$ar_reg['nome_evento']."

".$ar_reg['link_certificado']."


Fundação CEEE - Previdência Privada
http://www.fundacaoceee.com.br
Siga-nos! http://twitter.com/fundacaoceee

**** ATENÇÃO ****
Este e-mail é somente para leitura.
Caso queira falar conosco clique no link abaixo:
https://www.fundacaoceee.com.br/fale_conosco.php";		
		
			$qr_sql = "
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
								'Fundação CEEE',
								'".$ar_reg['email']."',                        
								'', 
								'',
								'Certificado - ".$ar_reg['nome_evento']."', 
								'".$email."',
								".(trim($ar_reg['cd_empresa']) == "" ? 'DEFAULT' : intval($ar_reg['cd_empresa'])).",
								".(trim($ar_reg['cd_registro_empregado']) == "" ? 'DEFAULT' : intval($ar_reg['cd_registro_empregado'])).",
								".(trim($ar_reg['seq_dependencia']) == "" ? 'DEFAULT' : intval($ar_reg['seq_dependencia'])).",
								101
							 );		
					  ";
			$this->db->query($qr_sql);
			#echo $qr_sql;
		}
	}	
        
    function listar_cracha_barras(&$result, $args=array())
    {
        $qr_sql = "
            SELECT nome,
                   cd_eventos_institucionais_inscricao AS cd_barra,
                   TRIM(UPPER(funcoes.remove_acento(TRIM(empresa)))) AS empresa,
				   cpf,
				   tp_inscrito
              FROM projetos.eventos_institucionais_inscricao
             WHERE dt_exclusao IS NULL
               AND cd_eventos_institucionais = ".intval($args['cd_evento'])."
             ORDER BY nome;";

        $result = $this->db->query($qr_sql);
    }
}
?>