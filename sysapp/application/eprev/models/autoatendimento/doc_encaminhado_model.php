<?php
class Doc_encaminhado_model extends Model
{
	function __construct()
	{
		parent::model();
	}

	public function get_tipo_doc()
	{
		$qr_sql = "
		   SELECT det.cd_doc_encaminhado_tipo_doc AS value,
		   		  det.ds_doc_encaminhado_tipo_doc AS text
		     FROM autoatendimento.doc_encaminhado_tipo_doc det
		    WHERE det.dt_exclusao IS NULL
		    ORDER BY det.nr_ordem, det.ds_doc_encaminhado_tipo_doc;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function listar($args = array(), $cd_doc_encaminhado = 0)
	{
		$qr_sql = "
		   SELECT de.cd_doc_encaminhado,
		   		  dea.cd_empresa,  		  
				  dea.cd_registro_empregado,
				  dea.seq_dependencia,
				  p.nome,
				  funcoes.get_usuario_nome(de.cd_usuario_cancelamento) AS cd_usuario_cancelamento,
				  funcoes.get_usuario_nome(de.cd_usuario_confirmacao) AS cd_usuario_confirmacao,
				  TO_CHAR(de.dt_encaminhamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhamento,
				  TO_CHAR(de.dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
				  TO_CHAR(de.dt_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
				  TO_CHAR(de.dt_envio_participante, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_participante,
				  TO_CHAR(de.dt_andamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_andamento,
				  TO_CHAR(de.dt_validado, 'DD/MM/YYYY HH24:MI:SS') AS dt_validado,
				  de.ds_validado,
				  de.ds_andamento,
				  det.ds_doc_encaminhado_tipo_doc,
				  (SELECT TO_CHAR(deaa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') || ' ' || deaa.ds_descricao AS ds_acompanhamento
				     FROM autoatendimento.doc_encaminhado_acompanhamento deaa
				    WHERE deaa.dt_exclusao IS NULL
				      AND deaa.cd_doc_encaminhado = de.cd_doc_encaminhado
				    ORDER BY deaa.dt_inclusao DESC
				    LIMIT 1),
				  (CASE WHEN de.dt_confirmacao IS NOT NULL
                        THEN 'N'
                        WHEN de.dt_cancelamento IS NOT NULL
                        THEN 'N'
                        WHEN de.dt_validado IS NOT NULL
                        THEN 'N'
                        WHEN de.dt_andamento IS NOT NULL
                        THEN 'S'
                        ELSE 'N'
                   END) AS fl_andamento,
				  (CASE WHEN de.dt_confirmacao IS NOT NULL
                        THEN 'Confirmado'
                        WHEN de.dt_cancelamento IS NOT NULL
                        THEN 'Cancelado'
                        WHEN de.dt_validado IS NOT NULL
                        THEN 'Validado p/ Atendente'
                        WHEN de.dt_andamento IS NOT NULL
                        THEN 'Em andamento'
                        ELSE 'Aguardando Validação'
                   END) AS ds_status,
                   (CASE WHEN de.dt_confirmacao IS NOT NULL
                         THEN 'label label-success'
                         WHEN de.dt_cancelamento IS NOT NULL
                         THEN 'label label-important'
                         WHEN de.dt_validado IS NOT NULL
                         THEN 'label label-warning'
                         WHEN de.dt_andamento IS NOT NULL
                         THEN 'label label-info'
                         ELSE 'label label-info'
                   END) AS ds_class_status,
                   de.ds_justificativa,
				   COUNT(*) AS qt_documento
		     FROM autoatendimento.doc_encaminhado de
		     JOIN autoatendimento.doc_encaminhado_arquivo dea
		       ON de.cd_doc_encaminhado = dea.cd_doc_encaminhado
		     JOIN participantes p
               ON p.cd_empresa            = dea.cd_empresa
              AND p.cd_registro_empregado = dea.cd_registro_empregado
              AND p.seq_dependencia       = dea.seq_dependencia
             JOIN autoatendimento.doc_encaminhado_tipo_doc det
               ON det.cd_doc_encaminhado_tipo_doc = dea.cd_doc_encaminhado_tipo_doc
		    WHERE dea.dt_exclusao       IS NULL
		      AND de.dt_encaminhamento IS NOT NULL
		      ".(intval($cd_doc_encaminhado) > 0 ? "AND de.cd_doc_encaminhado != ".intval($cd_doc_encaminhado) : '')."
		      ".(trim($args['cd_empresa']) != '' ? "AND dea.cd_empresa = ".intval($args['cd_empresa']) : '')."			   
			  ".(trim($args['cd_registro_empregado']) != '' ? "AND dea.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : '')."			   
			  ".(trim($args['seq_dependencia']) != '' ? "AND dea.seq_dependencia = ".intval($args['seq_dependencia']) : '')."
		      ".(((trim($args['dt_encaminhamento_ini']) != '') AND (trim($args['dt_encaminhamento_fim']) != '')) ? "AND DATE_TRUNC('day', de.dt_encaminhamento) BETWEEN TO_DATE('".$args['dt_encaminhamento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encaminhamento_fim']."', 'DD/MM/YYYY')" : "")."
		      ".(trim($args['cd_doc_encaminhado_tipo_doc']) != '' ? 'AND det.cd_doc_encaminhado_tipo_doc = '.intval($args['cd_doc_encaminhado_tipo_doc']) : '')."
		      ".(trim($args['fl_cancelamento']) == 'S' ? 'AND de.dt_cancelamento IS NOT NULL' : '')."
		      ".(trim($args['fl_cancelamento']) == 'N' ? 'AND de.dt_cancelamento IS NULL' : '')."
		      ".(trim($args['fl_confirmacao']) == 'S' ? 'AND de.dt_confirmacao IS NOT NULL' : '')."
		      ".(trim($args['fl_confirmacao']) == 'N' ? 'AND de.dt_confirmacao IS NULL' : '')."
		      ".(trim($args['fl_envio_participante']) == 'S' ? 'AND de.dt_envio_participante IS NOT NULL' : '')."
		      ".(trim($args['fl_envio_participante']) == 'N' ? 'AND de.dt_envio_participante IS NULL' : '')."
		    GROUP BY de.cd_doc_encaminhado,
		             dea.cd_empresa,  		  
				     dea.cd_registro_empregado,
				     dea.seq_dependencia,
				     det.cd_doc_encaminhado_tipo_doc,
				     p.nome,
				     de.cd_usuario_cancelamento,
				     de.cd_usuario_confirmacao,
				     de.dt_encaminhamento,
				     de.dt_cancelamento,
				     de.dt_confirmacao,
				     de.dt_envio_participante,
				     de.dt_andamento,
				     det.ds_doc_encaminhado_tipo_doc
			ORDER BY de.dt_encaminhamento DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_doc_encaminhado)
	{
		$qr_sql = "
		   SELECT de.cd_doc_encaminhado,
		   		  dea.cd_empresa,  		  
				  dea.cd_registro_empregado,
				  dea.seq_dependencia,
				  p.nome,
				  de.ds_justificativa,
		   	      det.cd_doc_encaminhado_tipo_doc,
		   		  det.ds_doc_encaminhado_tipo_doc,
		   		  TO_CHAR(de.dt_envio_participante, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_participante,
		   		  TO_CHAR(de.dt_encaminhamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhamento,
		   		  TO_CHAR(de.dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
		   		  TO_CHAR(de.dt_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
		   		  TO_CHAR(de.dt_andamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_andamento,
				  TO_CHAR(de.dt_validado, 'DD/MM/YYYY HH24:MI:SS') AS dt_validado,
				  de.ds_validado,
				  de.ds_andamento,
		   		  (CASE WHEN de.dt_confirmacao IS NOT NULL
                        THEN 'Confirmado'
                        WHEN de.dt_cancelamento IS NOT NULL
                        THEN 'Cancelado'
                        WHEN de.dt_validado IS NOT NULL
                        THEN 'Validado p/ Atendente'
                        WHEN de.dt_andamento IS NOT NULL
                        THEN 'Em andamento'
                        ELSE 'Aguardando Validação'
                   END) AS ds_status,
                   (CASE WHEN de.dt_confirmacao IS NOT NULL
                         THEN 'label label-success'
                         WHEN de.dt_cancelamento IS NOT NULL
                         THEN 'label label-important'
                         WHEN de.dt_validado IS NOT NULL
                         THEN 'label label-warning'
                         WHEN de.dt_andamento IS NOT NULL
                         THEN 'label label-info'
                         ELSE 'label label-info'
                   END) AS ds_class_status
		     FROM autoatendimento.doc_encaminhado de
		     JOIN autoatendimento.doc_encaminhado_arquivo dea
		       ON de.cd_doc_encaminhado = dea.cd_doc_encaminhado
		     JOIN participantes p
               ON p.cd_empresa            = dea.cd_empresa
              AND p.cd_registro_empregado = dea.cd_registro_empregado
              AND p.seq_dependencia       = dea.seq_dependencia
		     JOIN autoatendimento.doc_encaminhado_tipo_doc det
		       ON dea.cd_doc_encaminhado_tipo_doc = det.cd_doc_encaminhado_tipo_doc
		    WHERE de.cd_doc_encaminhado = ".intval($cd_doc_encaminhado)."
		    GROUP BY de.cd_doc_encaminhado,
		   		     dea.cd_empresa,  		  
				     dea.cd_registro_empregado,
				     dea.seq_dependencia,
				     p.nome,
				     de.ds_justificativa,
		   	         det.cd_doc_encaminhado_tipo_doc,
		   		     det.ds_doc_encaminhado_tipo_doc,
		   		     de.dt_cancelamento,
		   		     de.dt_envio_participante,
		   		     de.dt_andamento,
		   		     de.dt_encaminhamento;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function listar_doc_encaminhado_arquivo($cd_doc_encaminhado)
	{
		$qr_sql = "
			SELECT dea.cd_doc_encaminhado_arquivo,
				   dea.ds_documento,
				   dea.cd_doc_encaminhado_tipo_doc,
				   det.ds_doc_encaminhado_tipo_doc,
				   COALESCE(dea.cd_tipo_doc, det.cd_tipo_doc) AS cd_tipo_doc,
				   dea.ds_observacao,
				   dea.cd_documento_recebido,
				   funcoes.nr_documento_recebido(dr.nr_ano, dr.nr_contador) AS nr_documento_recebido,
				   dea.id_liquid
			  FROM autoatendimento.doc_encaminhado_arquivo dea
			  JOIN autoatendimento.doc_encaminhado_tipo_doc det
		        ON dea.cd_doc_encaminhado_tipo_doc = det.cd_doc_encaminhado_tipo_doc
		      LEFT JOIN projetos.documento_recebido dr
		        ON dr.cd_documento_recebido = dea.cd_documento_recebido
			 WHERE dea.cd_doc_encaminhado = ".intval($cd_doc_encaminhado).";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function confirmar($cd_doc_encaminhado, $cd_usuario)
	{
		$qr_sql = "
		   UPDATE autoatendimento.doc_encaminhado
			  SET cd_usuario_confirmacao = ".intval($cd_usuario)." ,
			      dt_confirmacao         = CURRENT_TIMESTAMP
		    WHERE cd_doc_encaminhado = ".intval($cd_doc_encaminhado).";";

		$this->db->query($qr_sql);
	}

	public function andamento($cd_doc_encaminhado, $ds_descricao, $cd_usuario)
	{
		$qr_sql = "
		   UPDATE autoatendimento.doc_encaminhado
			  SET cd_usuario_andamento = ".intval($cd_usuario)." ,
			      dt_andamento         = CURRENT_TIMESTAMP,
			      ds_andamento         = ".(trim($ds_descricao) != '' ? str_escape($ds_descricao) : '')."
		    WHERE cd_doc_encaminhado = ".intval($cd_doc_encaminhado).";";

		$this->db->query($qr_sql);
	}

	public function cancelar($cd_doc_encaminhado, $args = array())
	{
		$qr_sql = "
		   UPDATE autoatendimento.doc_encaminhado
			  SET cd_usuario_cancelamento = ".intval($args['cd_usuario'])." ,
			      dt_cancelamento         = CURRENT_TIMESTAMP,
			      ds_justificativa        = ".(trim($args['ds_justificativa']) != '' ? str_escape($args['ds_justificativa']) : "")."		
		    WHERE cd_doc_encaminhado = ".intval($cd_doc_encaminhado).";";

		$this->db->query($qr_sql);
	}	

	public function enviar($cd_doc_encaminhado, $cd_usuario)
	{
		$qr_sql = "
		   UPDATE autoatendimento.doc_encaminhado
			  SET cd_usuario_envio_participante = ".intval($cd_usuario).",
			      dt_envio_participante         = CURRENT_TIMESTAMP
		    WHERE cd_doc_encaminhado = ".intval($cd_doc_encaminhado).";";

		$this->db->query($qr_sql);
	}

	public function participante_email($cd_doc_encaminhado)
	{
		$qr_sql = "
			SELECT de.cd_doc_encaminhado,
				   dea.cd_empresa, 
			       dea.cd_registro_empregado,  
			       dea.seq_dependencia,
			       p.email, 
       			   p.email_profissional,
       			   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
       			   de.ds_justificativa
			  FROM autoatendimento.doc_encaminhado de
			  JOIN autoatendimento.doc_encaminhado_arquivo dea
			    ON dea.cd_doc_encaminhado = de.cd_doc_encaminhado
			  JOIN public.participantes p
			    ON dea.cd_registro_empregado = p.cd_registro_empregado 
			   AND dea.seq_dependencia       = p.seq_dependencia 
			   AND dea.cd_empresa            = p.cd_empresa
			   AND COALESCE(p.email, COALESCE(p.email_profissional, '')) LIKE '%@%'
		     WHERE de.cd_doc_encaminhado = ".intval($cd_doc_encaminhado)."
		     GROUP BY de.cd_doc_encaminhado,
				   dea.cd_empresa, 
			       dea.cd_registro_empregado,  
			       dea.seq_dependencia,
			       p.email,
			       funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia),
       			   p.email_profissional,
       			   de.ds_justificativa;";

		return $this->db->query($qr_sql)->row_array(); 
	}

	public function tipo_solicitacao_protocolo_interno()
	{
		$qr_sql = "
			SELECT cd_documento_recebido_tipo_solic AS value,
	               ds_documento_recebido_tipo_solic AS text
			  FROM projetos.documento_recebido_tipo_solic 
		     WHERE dt_exclusao IS NULL
		     ORDER BY nr_ordem, ds_documento_recebido_tipo_solic;";

	    return $this->db->query($qr_sql)->result_array();
	}

	public function get_doc_encaminhado_arquivo($cd_doc_encaminhado_arquivo)
	{
		$qr_sql = "
			SELECT dea.cd_doc_encaminhado_arquivo,
				   dea.ds_documento
			  FROM autoatendimento.doc_encaminhado_arquivo dea
			 WHERE dea.cd_doc_encaminhado_arquivo = ".intval($cd_doc_encaminhado_arquivo).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function protocolo_interno($args = array())
	{
		$cd_documento_recebido = $this->db->get_new_id('projetos.documento_recebido', 'cd_documento_recebido');

        $qr_sql = "
			INSERT INTO projetos.documento_recebido 
			     (
					cd_documento_recebido,
					cd_documento_recebido_tipo,
					cd_documento_recebido_tipo_solic,
					cd_usuario_cadastro,
					dt_cadastro
		         ) 
		    VALUES 
		         (
					".intval($cd_documento_recebido).",
					".intval($args['cd_documento_recebido_tipo']).",
					".intval($args['cd_documento_recebido_tipo_solic']).",
					".intval($args['cd_usuario']).",
					CURRENT_TIMESTAMP
		         );";
	
		$result = $this->db->query($qr_sql);
		
       return $cd_documento_recebido;
	}

	public function protocolo_interno_documento($cd_doc_encaminhado_arquivo, $args = array())
	{
		$qr_sql = "
            INSERT INTO projetos.documento_recebido_item 
			     ( 
                    cd_documento_recebido,
                    cd_empresa,
                    cd_registro_empregado,
                    seq_dependencia,
                    nome,
                    cd_tipo_doc, 
                    arquivo,
                    arquivo_nome,
                    nr_folha,
                    nr_folha_pdf,
                    cd_usuario_cadastro,
                   	dt_cadastro
                 ) 
			VALUES 
			     ( 
                    ".intval($args['cd_documento_recebido']).",
					".intval($args['cd_empresa']).",
					".intval($args['cd_registro_empregado']).",
					".intval($args['seq_dependencia']).",
					".str_escape($args['nome']).",
					".intval($args['cd_tipo_doc']).",
					".str_escape($args['arquivo']).",
					".str_escape($args['arquivo_nome']).",
					".intval($args['nr_folha']).",
					".intval($args['nr_folha_pdf']).",
					".intval($args['cd_usuario']).",
					CURRENT_TIMESTAMP
                 );
        UPDATE autoatendimento.doc_encaminhado_arquivo
           SET cd_documento_recebido = ".intval($args['cd_documento_recebido']).",
               cd_tipo_doc           = ".intval($args['cd_tipo_doc'])."
         WHERE cd_doc_encaminhado_arquivo = ".intval($cd_doc_encaminhado_arquivo).";";

        $this->db->query($qr_sql);
	}

	public function get_documento_nome($cd_tipo_doc)
	{
		$qr_sql = "
			SELECT cd_tipo_doc,
			       nome_documento
			  FROM tipo_documentos
			 WHERE cd_tipo_doc = ".intval($cd_tipo_doc).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function liquid_documento($cd_doc_encaminhado_arquivo, $args = array())
	{
		$qr_sql = "
	        UPDATE autoatendimento.doc_encaminhado_arquivo
	           SET id_liquid   = ".intval($args['id_liquid']).",
	               cd_tipo_doc = ".intval($args['cd_tipo_doc'])."
	         WHERE cd_doc_encaminhado_arquivo = ".intval($cd_doc_encaminhado_arquivo).";";

        $this->db->query($qr_sql);
	}

	public function salvar_acompanhamento($cd_doc_encaminhado, $ds_descricao, $cd_usuario)
	{
		$qr_sql = "
			INSERT INTO autoatendimento.doc_encaminhado_acompanhamento
				 (
            		cd_doc_encaminhado, 
            		ds_descricao, 
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao
            	 )
    		VALUES 
    			 (
    			 	".intval($cd_doc_encaminhado).",
    			 	".(trim($ds_descricao) != '' ? str_escape($ds_descricao) : '').",
    			 	".intval($cd_usuario).",
    			 	".intval($cd_usuario)."
    			 );";

        $this->db->query($qr_sql);
	}

	public function listar_acompanhamento($cd_doc_encaminhado)
	{
		$qr_sql = "
			SELECT cd_doc_encaminhado_acompanhamento, 
            	   ds_descricao, 
            	   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
            	   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario
			  FROM autoatendimento.doc_encaminhado_acompanhamento
			 WHERE dt_exclusao IS NULL
			   AND cd_doc_encaminhado = ".intval($cd_doc_encaminhado)."
			 ORDER BY doc_encaminhado_acompanhamento.dt_inclusao DESC";

	    return $this->db->query($qr_sql)->result_array();
	}

	public function validado_pelo_atendente($cd_doc_encaminhado, $cd_usuario)
	{
		$qr_sql = "
			UPDATE autoatendimento.doc_encaminhado
			   SET dt_validado          = CURRENT_TIMESTAMP, 
			       cd_usuario_validado  = ".intval($cd_usuario)."
			 WHERE cd_doc_encaminhado = ".intval($cd_doc_encaminhado).";";

        $this->db->query($qr_sql);
	}
}
