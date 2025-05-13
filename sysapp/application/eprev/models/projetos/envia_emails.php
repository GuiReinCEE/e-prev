<?php
class Envia_emails extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar_emprestimo( &$result, &$count, $args=array() )
	{
		// mount query
		$sql = "
		SELECT 
			ee.cd_email
			, TO_CHAR(ee.dt_envio, 'DD/MM/YYYY') AS dt_envio
			, ee.de
			, ee.para
			, ee.assunto
			, TO_CHAR(ee.dt_email_enviado, 'DD/MM/YYYY') AS dt_email_enviado
			, ee.cd_empresa
			, ee.cd_registro_empregado
			, ee.seq_dependencia
			, TO_CHAR(le.dt_email, 'DD/MM/YYYY HH24:MI') AS dt_retorno
			, le.ds_msg

		FROM projetos.envia_emails ee
		LEFT JOIN projetos.log_email le
		ON le.nr_msg::INTEGER = ee.cd_email::INTEGER

		WHERE ee.cd_evento=33
		AND date_trunc('day', ee.dt_envio) BETWEEN TO_DATE('{inicio}', 'DD/MM/YYYY') AND TO_DATE('{fim}', 'DD/MM/YYYY')
		{devolvido}
		";

		// parse query ...
		$devolvido="";
		if($args["devolvido"]=='S')
		{
			$devolvido = "AND le.dt_email IS NOT NULL";
		}
		esc( "{devolvido}", $devolvido, $sql );
		esc( "{inicio}", $args["dt_envio_inicio"], $sql );
		esc( "{fim}", $args["dt_envio_fim"], $sql );

		// return result ...
		$result = $this->db->query($sql);
	}

	function listar_senge( &$result, &$count, $args=array() )
	{
		// mount query
		$sql = "

		SELECT 
		ee.cd_email
		, TO_CHAR(ee.dt_envio, 'DD/MM/YYYY') AS dt_envio
		, ee.de
		, ee.para
		, ee.assunto
		, TO_CHAR(ee.dt_email_enviado, 'DD/MM/YYYY') AS dt_email_enviado
		, ee.cd_empresa
		, ee.cd_registro_empregado
		, ee.seq_dependencia
		, TO_CHAR(le.dt_email, 'DD/MM/YYYY HH24:MI') AS dt_retorno
		, le.ds_msg
		FROM projetos.envia_emails ee
		LEFT JOIN projetos.log_email le
		ON le.nr_msg::INTEGER = ee.cd_email::INTEGER
		WHERE 
		(
		   (ee.de = 'Senge Previdência') 
		   OR (ee.de like 'Senge Previdência%') 
		   OR (ee.de = 'Senge Previdencia')
		   OR (ee.cd_evento in(31,32,40))
		)
		AND
		(
		   date_trunc('day', ee.dt_envio)
		   BETWEEN TO_DATE('{inicio}', 'DD/MM/YYYY')
		   AND TO_DATE('{fim}', 'DD/MM/YYYY')
		)
		{devolvido}
		";

		// parse query ...
		$devolvido="";
		if($args["devolvido"]=='S')
		{
			$devolvido = "AND le.dt_email IS NOT NULL";
		}

		esc( "{devolvido}", $devolvido, $sql );
		esc( "{inicio}", $args["dt_envio_inicio"], $sql );
		esc( "{fim}", $args["dt_envio_fim"], $sql );

		// return result ...
		$result = $this->db->query($sql);
	}

	function lista_emails_sinprors( &$result, &$count, $args=array() )
	{
		$this->load->library('pagination');

		// COUNT
		$sql_count = "
			SELECT COUNT(*) as qtd
			  FROM projetos.envia_emails ee
	     LEFT JOIN projetos.log_email le
                ON CAST(le.nr_msg AS INTEGER) = ee.cd_email
			 WHERE cd_empresa = 8
			   {filtro_data} {filtro_assunto} {filtro_evento} {filtro_empregado}
			   AND le.dt_email IS NULL;
		";

		$sql_select = "
			SELECT cd_email
			     , TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio
			     , de
			     , para
			     , cc
			     , cco
			     , assunto
			     , texto
			     , TO_CHAR(dt_email_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_email_enviado
			     , TO_CHAR(dt_schedule_email, 'DD/MM/YYYY HH24:MI:SS') AS dt_schedule_email
			     , arquivo_anexo
			     , div_solicitante
			     , cd_divulgacao
			     , cd_plano
			     , cd_empresa
			     , cd_registro_empregado
			     , seq_dependencia
			     , tipo_mensagem
			     , cd_evento
			     , TO_CHAR(le.dt_email, 'DD/MM/YYYY HH24:MI') AS dt_retorno
			  FROM projetos.envia_emails ee
	     LEFT JOIN projetos.log_email le
                ON CAST(le.nr_msg AS INTEGER) = ee.cd_email
			 WHERE cd_empresa = 8
			   {filtro_data} {filtro_assunto} {filtro_evento} {filtro_empregado}
	      
	      ORDER BY cd_email ASC

			 LIMIT " . $this->pagination->per_page . " OFFSET " . $args["page"] . ";
		";

		if($args['inicio']!="" && $args['fim']!="")
		{
			$sql_count = str_replace( "{filtro_data}", " AND DATE_TRUNC('day', dt_envio) BETWEEN TO_DATE( {inicio}, 'DD/MM/YYYY' ) AND TO_DATE( {fim}, 'DD/MM/YYYY' ) ", $sql_count );
			$sql_count = str_replace( "{inicio}", $this->db->escape($args['inicio']), $sql_count );
			$sql_count = str_replace( "{fim}", $this->db->escape($args['fim']), $sql_count );
			
			$sql_select = str_replace( "{filtro_data}", " AND DATE_TRUNC('day', dt_envio) BETWEEN TO_DATE( {inicio}, 'DD/MM/YYYY' ) AND TO_DATE( {fim}, 'DD/MM/YYYY' ) ", $sql_select );
			$sql_select = str_replace( "{inicio}", $this->db->escape($args['inicio']), $sql_select );
			$sql_select = str_replace( "{fim}", $this->db->escape($args['fim']), $sql_select );
		}
		else
		{
			$sql_count = str_replace( "{filtro_data}", "  ", $sql_count );
			$sql_select = str_replace( "{filtro_data}", "  ", $sql_select );
		}

		if( $args["cd_registro_empregado"]!="" )
		{
			$sql_count = str_replace( "{filtro_empregado}", " AND cd_registro_empregado = " . intval($args['cd_registro_empregado']) . " ", $sql_count );
			$sql_select = str_replace( "{filtro_empregado}", " AND cd_registro_empregado = " . intval($args['cd_registro_empregado']) . " ", $sql_select );
		}
		else
		{
			$sql_count = str_replace( "{filtro_empregado}", "  ", $sql_count );
			$sql_select = str_replace( "{filtro_empregado}", "  ", $sql_select );
		}
		
		if( $args["assunto"]!="" )
		{
			$sql_count = str_replace( "{filtro_assunto}", " AND UPPER(assunto) like UPPER('%' || " . $this->db->escape($args['assunto']) . "|| '%')", $sql_count );
			$sql_select = str_replace( "{filtro_assunto}", " AND UPPER(assunto) like UPPER('%' || " . $this->db->escape($args['assunto']) . "|| '%')", $sql_select );
		}
		else
		{
			$sql_count = str_replace( "{filtro_assunto}", "  ", $sql_count );
			$sql_select = str_replace( "{filtro_assunto}", "  ", $sql_select );
		}

		if( $args["cd_evento"]!="" )
		{
			$sql_count = str_replace( "{filtro_evento}", " AND cd_evento = " . intval($args['cd_evento']) . " ", $sql_count );
			$sql_select = str_replace( "{filtro_evento}", " AND cd_evento = " . intval($args['cd_evento']) . " ", $sql_select );
		}
		else
		{
			$sql_count = str_replace( "{filtro_evento}", " AND cd_evento in (47,38,39) ", $sql_count );
			$sql_select = str_replace( "{filtro_evento}", " AND cd_evento in (47,38,39) ", $sql_select );
		}

		// ----------------------
		// RESULTADOS

		$query = $this->db->query($sql_count);
		$row = $query->row_array(0);
		$count = $row['qtd'];
		
		$this->setup_pagination($count, $this->config->item('base_url') . 'index.php/sinprors/email_enviado/index');

		// RESULTS
		$result = $this->db->query($sql_select);
	}
	
	function lista_emails_sintae( &$result, &$count, $args=array() )
	{
		$this->load->library('pagination');

		// COUNT
		$sql_count = "
			SELECT COUNT(*) as qtd
			  FROM projetos.envia_emails ee
	     LEFT JOIN projetos.log_email le
                ON CAST(le.nr_msg AS INTEGER) = ee.cd_email
			 WHERE cd_empresa = 10
			   {filtro_data} {filtro_assunto} {filtro_evento} {filtro_empregado}
			   AND le.dt_email IS NULL;
		";

		$sql_select = "
			SELECT cd_email
			     , TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio
			     , de
			     , para
			     , cc
			     , cco
			     , assunto
			     , texto
			     , TO_CHAR(dt_email_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_email_enviado
			     , TO_CHAR(dt_schedule_email, 'DD/MM/YYYY HH24:MI:SS') AS dt_schedule_email
			     , arquivo_anexo
			     , div_solicitante
			     , cd_divulgacao
			     , cd_plano
			     , cd_empresa
			     , cd_registro_empregado
			     , seq_dependencia
			     , tipo_mensagem
			     , cd_evento
			     , TO_CHAR(le.dt_email, 'DD/MM/YYYY HH24:MI') AS dt_retorno
			  FROM projetos.envia_emails ee
	     LEFT JOIN projetos.log_email le
                ON CAST(le.nr_msg AS INTEGER) = ee.cd_email
			 WHERE cd_empresa = 10
			   {filtro_data} {filtro_assunto} {filtro_evento} {filtro_empregado}
	      
	      ORDER BY cd_email ASC

			 LIMIT " . $this->pagination->per_page . " OFFSET " . $args["page"] . ";
		";

		if($args['inicio']!="" && $args['fim']!="")
		{
			$sql_count = str_replace( "{filtro_data}", " AND DATE_TRUNC('day', dt_envio) BETWEEN TO_DATE( {inicio}, 'DD/MM/YYYY' ) AND TO_DATE( {fim}, 'DD/MM/YYYY' ) ", $sql_count );
			$sql_count = str_replace( "{inicio}", $this->db->escape($args['inicio']), $sql_count );
			$sql_count = str_replace( "{fim}", $this->db->escape($args['fim']), $sql_count );
			
			$sql_select = str_replace( "{filtro_data}", " AND DATE_TRUNC('day', dt_envio) BETWEEN TO_DATE( {inicio}, 'DD/MM/YYYY' ) AND TO_DATE( {fim}, 'DD/MM/YYYY' ) ", $sql_select );
			$sql_select = str_replace( "{inicio}", $this->db->escape($args['inicio']), $sql_select );
			$sql_select = str_replace( "{fim}", $this->db->escape($args['fim']), $sql_select );
		}
		else
		{
			$sql_count = str_replace( "{filtro_data}", "  ", $sql_count );
			$sql_select = str_replace( "{filtro_data}", "  ", $sql_select );
		}

		if( $args["cd_registro_empregado"]!="" )
		{
			$sql_count = str_replace( "{filtro_empregado}", " AND cd_registro_empregado = " . intval($args['cd_registro_empregado']) . " ", $sql_count );
			$sql_select = str_replace( "{filtro_empregado}", " AND cd_registro_empregado = " . intval($args['cd_registro_empregado']) . " ", $sql_select );
		}
		else
		{
			$sql_count = str_replace( "{filtro_empregado}", "  ", $sql_count );
			$sql_select = str_replace( "{filtro_empregado}", "  ", $sql_select );
		}
		
		if( $args["assunto"]!="" )
		{
			$sql_count = str_replace( "{filtro_assunto}", " AND UPPER(assunto) like UPPER('%' || " . $this->db->escape($args['assunto']) . "|| '%')", $sql_count );
			$sql_select = str_replace( "{filtro_assunto}", " AND UPPER(assunto) like UPPER('%' || " . $this->db->escape($args['assunto']) . "|| '%')", $sql_select );
		}
		else
		{
			$sql_count = str_replace( "{filtro_assunto}", "  ", $sql_count );
			$sql_select = str_replace( "{filtro_assunto}", "  ", $sql_select );
		}

		if( $args["cd_evento"]!="" )
		{
			$sql_count = str_replace( "{filtro_evento}", " AND cd_evento = " . intval($args['cd_evento']) . " ", $sql_count );
			$sql_select = str_replace( "{filtro_evento}", " AND cd_evento = " . intval($args['cd_evento']) . " ", $sql_select );
		}
		else
		{
			$sql_count = str_replace( "{filtro_evento}", " AND cd_evento in (47,38,39) ", $sql_count );
			$sql_select = str_replace( "{filtro_evento}", " AND cd_evento in (47,38,39) ", $sql_select );
		}

		// ----------------------
		// RESULTADOS

		$query = $this->db->query($sql_count);
		$row = $query->row_array(0);
		$count = $row['qtd'];
		
		$this->setup_pagination($count, $this->config->item('base_url') . 'index.php/sinprors/email_enviado/index');

		// RESULTS
		$result = $this->db->query($sql_select);
	}

	private function setup_pagination($count, $page)
	{
		// Setup pagination
		$config['enable_query_strings'] = FALSE;
		$config['base_url'] = $page;
		$config['per_page'] = 10000;
		$config['total_rows'] = $count;
		$this->pagination->initialize($config);
	}
	/* PASSEI PARA Pos_venda_participante_model. LUCIANO RODRIGUEZ 05/09/2013
	function envia_email_pos_venda($dados,&$e=array())
	{
		#### ENVIO DE EMAIL DO POS VENDA ####
		if($dados['cd_empresa']=='')            $e[sizeof($e)] = 'cd_empresa não informado!';
		if($dados['cd_registro_empregado']=='') $e[sizeof($e)] = 'cd_registro_empregado não informado!';
		if($dados['seq_dependencia']=='')       $e[sizeof($e)] = 'seq_dependencia não informado!';	
	
		if(sizeof($e)==0)
		{
			$query = $this->db->query("
					SELECT rotinas.pos_venda_email(".$dados['cd_empresa'].", ".$dados['cd_registro_empregado'].", ".$dados['seq_dependencia'].", ".$this->session->userdata('codigo').")
				");
				
				if($query)
				{
					//return $this->db->insert_id("projetos.pre_venda", "cd_pre_venda");
					return true;
				}
				else
				{
					$e[sizeof($e)] = 'Erro no SELECT';
					return false;
				}						 
						 
		}
		else
		{
			return false;
		}
	}
	*/
	function busca_email(&$result, $args=array())
	{
		$qr_sql = "
					SELECT ee.cd_email, 
					       ee.cd_email_pai,
						   ee.de, 
						   ee.para, 
						   ee.cc, 
						   ee.cco, 
						   ee.assunto, 
						   ee.texto, 
						   ee.cd_empresa, 
						   ee.cd_registro_empregado, 
						   ee.seq_dependencia,
						   p.nome,
						   TO_CHAR(ee.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio, 
						   TO_CHAR(ee.dt_email_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_email_enviado, 
						   TO_CHAR(ee.dt_schedule_email, 'DD/MM/YYYY HH24:MI:SS') AS dt_schedule_email, 
						   ee.cd_divulgacao,
						   ee.cd_evento,
						   e.nome AS ds_evento,
						   ee.fl_retornou,
						   ee.tp_email,
						   ee.cd_usuario,
						   funcoes.get_usuario_nome(ee.cd_usuario) AS nome_usuario,
						   COALESCE(formato,'TEXT') AS formato,
						   ee.fl_comprova,
						   (SELECT COUNT(*) FROM email.email_anexo ea WHERE ea.dt_exclusao IS NULL AND ea.cd_email = ee.cd_email) AS qt_anexo,
						   (SELECT COUNT(*) FROM projetos.envia_emails ef WHERE ef.cd_email_pai = ee.cd_email) AS qt_email_filho
					  FROM projetos.envia_emails ee
					  LEFT JOIN public.participantes p
						ON p.cd_empresa            = ee.cd_empresa
					   AND p.cd_registro_empregado = ee.cd_registro_empregado
					   AND p.seq_dependencia       = ee.seq_dependencia	
					  LEFT JOIN projetos.eventos e
						ON e.cd_evento = ee.cd_evento
					 WHERE ee.cd_email = ".intval($args["cd_email"])."
		          ";
		#echo $qr_sql;#
		
		$result = $this->db->query($qr_sql);
	}	
	
	function listarAnexo(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_email_anexo, 
					       MD5(cd_email_anexo::TEXT) AS cd_email_anexo_md5,
						   arquivo_nome
					  FROM email.email_anexo
					 WHERE dt_exclusao IS NULL
					   AND cd_email = ".intval($args['cd_email'])."
					 ORDER BY arquivo_nome
		          ";
		#echo "<pre>$qr_sql</pre>"; EXIT;
		$result = $this->db->query($qr_sql);
	}

	function abrirAnexo(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_email_anexo, 
						   arquivo_nome,
						   arquivo
					  FROM email.email_anexo
					 WHERE dt_exclusao IS NULL
					   AND MD5(cd_email_anexo::TEXT) = '".trim($args['cd_email_anexo'])."'
		          ";
		#echo "<pre>$qr_sql</pre>"; EXIT;
		$result = $this->db->query($qr_sql);
	}	
	
	function reenviar_email(&$result, $args=array())
	{
		$new_id = intval($this->db->get_new_id("projetos.envia_emails", "cd_email"));
		#### INSERT ####
		$qr_sql = " 
					INSERT INTO projetos.envia_emails 
					     (
						   cd_email,
						   dt_envio, 
						   de, 
						   para, 
						   cc, 
						   cco, 
						   assunto, 
						   texto, 
						   formato,
						   fl_comprova,
						   cd_empresa, 
						   cd_registro_empregado, 
						   seq_dependencia,
						   cd_evento,
						   cd_divulgacao,
						   tp_email,
						   cd_email_pai,
						   cd_usuario
						 )
				    VALUES 
						 (
						   ".$new_id.",
						   CURRENT_TIMESTAMP,
						   ".(trim($args['de']) == "" ? "DEFAULT" : "'".$args['de']."'").",
						   ".(trim($args['para']) == "" ? "DEFAULT" : "'".$args['para']."'").",
						   ".(trim($args['cc']) == "" ? "DEFAULT" : "'".$args['cc']."'").",
						   ".(trim($args['cco']) == "" ? "DEFAULT" : "'".$args['cco']."'").",
						   ".(trim($args['assunto']) == "" ? "DEFAULT" : "'[Reenvio] ".$args['assunto']."'").",
						   ".(trim($args['texto']) == "" ? "DEFAULT" : str_escape($args['texto'])).",
						   ".(trim($args['formato']) == "" ? "DEFAULT" : "'".$args['formato']."'").",
						   ".(trim($args['fl_comprova']) == "" ? "DEFAULT" : "'".$args['fl_comprova']."'").",
						   ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : $args['cd_empresa']).",
						   ".(trim($args['cd_registro_empregado']) == "" ? "DEFAULT" : $args['cd_registro_empregado']).",
						   ".(trim($args['seq_dependencia']) == "" ? "DEFAULT" : $args['seq_dependencia']).",
						   ".(trim($args['cd_evento']) == "" ? "DEFAULT" : $args['cd_evento']).",
						   ".(trim($args['cd_divulgacao']) == "" ? "DEFAULT" : $args['cd_divulgacao']).",
						   ".(trim($args['tp_email']) == "" ? "DEFAULT" : "'".$args['tp_email']."'").",						   
						   ".(intval($args['cd_email']) == 0 ? "DEFAULT" : intval($args['cd_email'])).",						   
						   ".(intval($args['cd_usuario']) == 0 ? "DEFAULT" : intval($args['cd_usuario']))."						   
						 );
						 
					INSERT INTO email.email_anexo(cd_usuario_inclusao, cd_email, arquivo_nome, arquivo)
					SELECT ".(intval($args['cd_usuario']) == 0 ? "cd_usuario_inclusao" : intval($args['cd_usuario'])).", 
					       ".$new_id.", 
						   arquivo_nome, 
						   arquivo
					  FROM email.email_anexo
					 WHERE dt_exclusao IS NULL
					   AND cd_email = ".(intval($args['cd_email']) == 0 ? "-1" : intval($args['cd_email'])).";
			      ";			
		#echo "<PRE>$qr_sql</PRE>";exit;
		$this->db->query($qr_sql);	
		return $new_id;
	}
	
	function listaLink(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_email_link, 
					       cd_email, 
						   link, 
						   dt_inclusao
					  FROM projetos.envia_emails_link
					 WHERE cd_email = ".intval($args['cd_email'])."

		          ";
		#echo "<pre>$qr_sql</pre>"; EXIT;
		$result = $this->db->query($qr_sql);
	}	
	
	function listaLinkLog(&$result, $args=array())
	{
		$qr_sql = "
					SELECT TO_CHAR(ll.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_acesso,
						   ll.ip
					  FROM projetos.envia_emails ee
					  JOIN projetos.envia_emails_link eel
						ON eel.cd_email = ee.cd_email
					  JOIN projetos.link l
						ON l.cd_link               = REPLACE(REPLACE(REPLACE(eel.link,'http://www.fceee.com.br/?',''),'https://fceee.com.br/?',''),'http://fceee.com.br/?','')
					   AND l.cd_empresa            = ee.cd_empresa
					   AND l.cd_registro_empregado = ee.cd_registro_empregado
					   AND l.seq_dependencia       = ee.seq_dependencia
					  JOIN projetos.link_log ll
						ON ll.cd_link = l.cd_link
					 WHERE eel.cd_email_link = ".intval($args['cd_email_link'])."
					   AND ll.ip NOT LIKE ('10.63.%')
					   AND ll.ip NOT LIKE ('10.64.%')
					 ORDER BY ll.dt_inclusao DESC		
		          ";
		#echo "<pre>$qr_sql</pre>"; EXIT;
		$result = $this->db->query($qr_sql);
	}	
	
	
	function buscaLogEmailErro(&$result, $args=array())
	{
		$qr_sql = "
	
		          ";
		#echo "<pre>$qr_sql</pre>"; EXIT;
		$result = $this->db->query($qr_sql);
	}	
}
?>