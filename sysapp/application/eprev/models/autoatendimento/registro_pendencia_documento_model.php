<?php
class registro_pendencia_documento_model extends Model
{
	function __construct()
	{
		parent::model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT rpd.cd_registro_pendencia_documento,
			       rpd.cd_empresa,  		  
				   rpd.cd_registro_empregado,
				   rpd.seq_dependencia,
				   p.nome,
				   funcoes.get_usuario_nome(rpd.cd_usuario_cancelamento) AS cd_usuario_cancelamento,
				   funcoes.get_usuario_nome(rpd.cd_usuario_confirmacao) AS cd_usuario_confirmacao,
				   TO_CHAR(rpd.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhamento,
				   TO_CHAR(rpd.dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
				   TO_CHAR(rpd.dt_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
				   TO_CHAR(rpd.dt_envio_participante, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_participante,
				   TO_CHAR(rpd.dt_andamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_andamento,
				   (CASE WHEN rpd.dt_confirmacao IS NOT NULL
                         THEN 'Confirmado'
                         WHEN rpd.dt_cancelamento IS NOT NULL
                         THEN 'Cancelado'
                         WHEN rpd.dt_andamento IS NOT NULL
                         THEN 'Em andamento'
                         ELSE 'Aguardando Validação'
                   END) AS ds_status,
                   (CASE WHEN rpd.dt_confirmacao IS NOT NULL
                         THEN 'label label-success'
                         WHEN rpd.dt_cancelamento IS NOT NULL
                         THEN 'label label-important'
                         WHEN rpd.dt_andamento IS NOT NULL
                         THEN 'label label-info'
                         ELSE 'label label-info'
                   END) AS ds_class_status,
                   rpd.ds_justificativa,
                   (SELECT COUNT(*)
                      FROM autoatendimento.registro_pendencia_documento_arquivo rpda
                     WHERE rpda.cd_registro_pendencia_documento = rpd.cd_registro_pendencia_documento) AS qt_documento
			  FROM autoatendimento.registro_pendencia_documento rpd
			  JOIN participantes p
                ON p.cd_empresa            = rpd.cd_empresa
               AND p.cd_registro_empregado = rpd.cd_registro_empregado
               AND p.seq_dependencia       = rpd.seq_dependencia
             WHERE 1 = 1
               ".(trim($args['cd_empresa']) != '' ? "AND rpd.cd_empresa = ".intval($args['cd_empresa']) : "")."			   
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND rpd.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
			   ".(trim($args['seq_dependencia']) != '' ? "AND rpd.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
		       ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? "AND DATE_TRUNC('day', rpd.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
		       ".(trim($args['fl_cancelamento']) == 'S' ? 'AND rpd.dt_cancelamento IS NOT NULL' : '')."
		       ".(trim($args['fl_cancelamento']) == 'N' ? 'AND rpd.dt_cancelamento IS NULL' : '')."
		       ".(trim($args['fl_confirmacao']) == 'S' ? 'AND rpd.dt_confirmacao IS NOT NULL' : '')."
		       ".(trim($args['fl_confirmacao']) == 'N' ? 'AND rpd.dt_confirmacao IS NULL' : '')."
		       ".(trim($args['fl_envio_participante']) == 'S' ? 'AND rpd.dt_envio_participante IS NOT NULL' : '')."
		       ".(trim($args['fl_envio_participante']) == 'N' ? 'AND rpd.dt_envio_participante IS NULL' : '')."";
 
		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_registro_pendencia_documento)
	{
		$qr_sql = "
			SELECT rpd.cd_registro_pendencia_documento,
			       rpd.cd_empresa,  		  
				   rpd.cd_registro_empregado,
				   rpd.seq_dependencia,
				   funcoes.cripto_re(rpd.cd_empresa, rpd.cd_registro_empregado, rpd.seq_dependencia) AS re_cripto,
				   p.nome,
				   funcoes.get_usuario_nome(rpd.cd_usuario_cancelamento) AS cd_usuario_cancelamento,
				   funcoes.get_usuario_nome(rpd.cd_usuario_confirmacao) AS cd_usuario_confirmacao,
				   TO_CHAR(rpd.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhamento,
				   TO_CHAR(rpd.dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
				   TO_CHAR(rpd.dt_confirmacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_confirmacao,
				   TO_CHAR(rpd.dt_envio_participante, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_participante,
				   TO_CHAR(rpd.dt_andamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_andamento,
				   (CASE WHEN rpd.dt_confirmacao IS NOT NULL
                         THEN 'Confirmado'
                         WHEN rpd.dt_cancelamento IS NOT NULL
                         THEN 'Cancelado'
                         WHEN rpd.dt_andamento IS NOT NULL
                         THEN 'Em andamento'
                         ELSE 'Aguardando Validação'
                   END) AS ds_status,
                   (CASE WHEN rpd.dt_confirmacao IS NOT NULL
                         THEN 'label label-success'
                         WHEN rpd.dt_cancelamento IS NOT NULL
                         THEN 'label label-important'
                         WHEN rpd.dt_andamento IS NOT NULL
                         THEN 'label label-info'
                         ELSE 'label label-info'
                   END) AS ds_class_status,
                   rpd.ds_justificativa,
                   rpd.id_pendencia
              FROM autoatendimento.registro_pendencia_documento rpd
			  JOIN participantes p
                ON p.cd_empresa            = rpd.cd_empresa
               AND p.cd_registro_empregado = rpd.cd_registro_empregado
               AND p.seq_dependencia       = rpd.seq_dependencia
             WHERE rpd.cd_registro_pendencia_documento = ".intval($cd_registro_pendencia_documento).";";
 
		return $this->db->query($qr_sql)->row_array();
	}

	public function listar_registro_pendencia_documento_arquivo($cd_registro_pendencia_documento)
	{
		$qr_sql = "
			SELECT rpda.cd_registro_pendencia_documento_arquivo,
				   rpda.ds_arquivo,
				   rpda.cd_tipo_doc,
				   rpda.cd_documento_recebido,
				   funcoes.nr_documento_recebido(dr.nr_ano, dr.nr_contador) AS nr_documento_recebido,
				   rpda.id_liquid
			  FROM autoatendimento.registro_pendencia_documento_arquivo rpda
			  LEFT JOIN projetos.documento_recebido dr
		        ON dr.cd_documento_recebido = rpda.cd_documento_recebido
			 WHERE rpda.cd_registro_pendencia_documento = ".intval($cd_registro_pendencia_documento).";";

		return $this->db->query($qr_sql)->result_array();
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

	public function confirmar($cd_registro_pendencia_documento, $cd_usuario)
	{
		$qr_sql = "
		   UPDATE autoatendimento.registro_pendencia_documento
			  SET cd_usuario_confirmacao = ".intval($cd_usuario)." ,
			      dt_confirmacao         = CURRENT_TIMESTAMP
		    WHERE cd_registro_pendencia_documento = ".intval($cd_registro_pendencia_documento).";";

		$this->db->query($qr_sql);
	}

	public function andamento($cd_registro_pendencia_documento, $cd_usuario)
	{
		$qr_sql = "
		   UPDATE autoatendimento.registro_pendencia_documento
			  SET cd_usuario_andamento = ".intval($cd_usuario)." ,
			      dt_andamento         = CURRENT_TIMESTAMP
		    WHERE cd_registro_pendencia_documento = ".intval($cd_registro_pendencia_documento).";";

		$this->db->query($qr_sql);
	}

	public function cancelar($cd_registro_pendencia_documento, $args = array())
	{
		$qr_sql = "
		   UPDATE autoatendimento.registro_pendencia_documento
			  SET cd_usuario_cancelamento = ".intval($args['cd_usuario'])." ,
			      dt_cancelamento         = CURRENT_TIMESTAMP,
			      ds_justificativa        = ".(trim($args['ds_justificativa']) != '' ? str_escape($args['ds_justificativa']) : "")."		
		    WHERE cd_registro_pendencia_documento = ".intval($cd_registro_pendencia_documento).";";

		$this->db->query($qr_sql);
	}

	public function enviar($cd_registro_pendencia_documento, $cd_usuario)
	{
		$qr_sql = "
		   UPDATE autoatendimento.registro_pendencia_documento
			  SET cd_usuario_envio_participante = ".intval($cd_usuario).",
			      dt_envio_participante         = CURRENT_TIMESTAMP
		    WHERE cd_registro_pendencia_documento = ".intval($cd_registro_pendencia_documento).";";

		$this->db->query($qr_sql);
	}

	public function participante_email($cd_registro_pendencia_documento)
	{
		$qr_sql = "
			SELECT rpd.cd_registro_pendencia_documento,
				   rpd.cd_empresa, 
			       rpd.cd_registro_empregado,  
			       rpd.seq_dependencia,
			       p.email, 
       			   p.email_profissional,
       			   funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) AS re_cripto,
       			   rpd.ds_justificativa
			  FROM autoatendimento.registro_pendencia_documento rpd
			  JOIN public.participantes p
			    ON rpd.cd_registro_empregado = p.cd_registro_empregado 
			   AND rpd.seq_dependencia       = p.seq_dependencia 
			   AND rpd.cd_empresa            = p.cd_empresa
			   AND COALESCE(p.email, COALESCE(p.email_profissional, '')) LIKE '%@%'
		     WHERE rpd.cd_registro_pendencia_documento = ".intval($cd_registro_pendencia_documento).";";

		return $this->db->query($qr_sql)->row_array(); 
	}

	public function get_registro_pendencia_documento_arquivo($cd_registro_pendencia_documento_arquivo)
	{
		$qr_sql = "
			SELECT dea.cd_registro_pendencia_documento_arquivo,
				   dea.ds_arquivo
			  FROM autoatendimento.registro_pendencia_documento_arquivo dea
			 WHERE dea.cd_registro_pendencia_documento_arquivo = ".intval($cd_registro_pendencia_documento_arquivo).";";

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

	public function protocolo_interno_documento($cd_registro_pendencia_documento_arquivo, $args = array())
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
        UPDATE autoatendimento.registro_pendencia_documento_arquivo
           SET cd_documento_recebido = ".intval($args['cd_documento_recebido']).",
               cd_tipo_doc           = ".intval($args['cd_tipo_doc'])."
         WHERE cd_registro_pendencia_documento_arquivo = ".intval($cd_registro_pendencia_documento_arquivo).";";

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

	public function liquid_documento($cd_registro_pendencia_documento_arquivo, $args = array())
	{
		$qr_sql = "
	        UPDATE autoatendimento.registro_pendencia_documento_arquivo
	           SET id_liquid   = ".intval($args['id_liquid']).",
	               cd_tipo_doc = ".intval($args['cd_tipo_doc'])."
	         WHERE cd_registro_pendencia_documento_arquivo = ".intval($cd_registro_pendencia_documento_arquivo).";";

        $this->db->query($qr_sql);
	}

}