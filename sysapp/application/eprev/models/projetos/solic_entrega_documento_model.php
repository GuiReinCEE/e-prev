<?php
class Solic_entrega_documento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_usuario, $fl_usuario_admin = FALSE, $args = array())
	{
		$qr_sql = "
			SELECT p.cd_solic_entrega_documento,
			       TO_CHAR(p.data_ini, 'DD/MM/YYYY') AS data_ini,
			       TO_CHAR(p.data_ini, 'HH24:MI') AS hr_ini,
			       TO_CHAR(p.data_fim, 'HH24:MI') AS hr_limite,
				   TO_CHAR(p.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(p.dt_recebido, 'DD/MM/YYYY HH24:MI:SS') AS dt_recebido,
				   p.cd_usuario_inclusao,
                   (CASE WHEN p.fl_prioridade = 'U' THEN 'Urgente'
	                     WHEN p.fl_prioridade = 'M' THEN 'Moderada'
                         ELSE 'Baixa'
                   END) AS ds_prioridade,
                   (CASE WHEN p.fl_prioridade = 'U' THEN 'label label-important'
	                     WHEN p.fl_prioridade = 'M' THEN 'label label-warning'
                         ELSE 'label label-info'
                   END) AS ds_class_prioridade,
                   (CASE WHEN p.fl_destinatario = 'A' THEN 'Aguardar'
			             WHEN p.fl_destinatario = 'E' THEN 'Entrar em contato com'
                         ELSE 'Outro'
                   END) AS ds_destinatario,
                   (CASE WHEN p.ds_destinatario = 'S' THEN 'Sim'
                         ELSE 'Não'
                   END) AS fl_recebido,
			       pt.ds_solic_entrega_documento_tipo,
			       funcoes.get_usuario_nome(p.cd_usuario_recebido) AS ds_usuario_recebido,
			       funcoes.get_usuario_nome(p.cd_usuario_inclusao) AS ds_usuario_inclusao,
			       (SELECT (CASE WHEN fl_status = 'E'
		    			         THEN 'Em Atendimento'
		    			         WHEN fl_status = 'O'
		    			         THEN 'Concluído'
		    			         WHEN fl_status = 'C'
		    			         THEN 'Cancelado'
		    			         ELSE ''
		    			   END)
    		          FROM projetos.solic_entrega_documento_acompanhamento pa
    		         WHERE pa.dt_exclusao                IS NULL
    		           AND pa.cd_solic_entrega_documento = p.cd_solic_entrega_documento
    		           AND pa.fl_status IN ('C', 'O', 'E')
    		         ORDER BY pa.dt_inclusao DESC
    		         LIMIT 1) AS ds_status,
    		       (SELECT (CASE WHEN fl_status = 'E'
		    			         THEN 'label label-info'
		    			         WHEN fl_status = 'O'
		    			         THEN 'label label-success'
		    			         WHEN fl_status = 'C'
		    			         THEN 'label'
		    			         ELSE ''
		    			   END)
    		          FROM projetos.solic_entrega_documento_acompanhamento pa
    		         WHERE pa.dt_exclusao                IS NULL
    		           AND pa.cd_solic_entrega_documento = p.cd_solic_entrega_documento
    		           AND pa.fl_status IN ('C', 'O', 'E')
    		         ORDER BY pa.dt_inclusao DESC
    		         LIMIT 1) AS ds_class_status
			  FROM projetos.solic_entrega_documento p
			  JOIN projetos.solic_entrega_documento_tipo pt
			    ON p.cd_solic_entrega_documento_tipo = pt.cd_solic_entrega_documento_tipo
			 WHERE p.dt_exclusao IS NULL
			   ".(!$fl_usuario_admin ? "AND p.cd_usuario_inclusao = ".intval($cd_usuario) : "")."
               ".(trim($args['cd_solic_entrega_documento_tipo']) != '' ? "AND pt.cd_solic_entrega_documento_tipo = ".trim($args['cd_solic_entrega_documento_tipo']) : "")."    
			   ".(trim($args['fl_prioridade']) != '' ? "AND fl_prioridade = '".trim($args['fl_prioridade'])."'" : "")."
               ".(((trim($args['dt_ini']) != '') AND trim($args['dt_fim']) != '') ? "AND DATE_TRUNC('day', data_ini) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : '')."
               ".(((trim($args['dt_recebido_ini']) != '') AND trim($args['dt_recebido_fim']) != '') ? "AND DATE_TRUNC('day', dt_recebido) BETWEEN TO_DATE('".$args['dt_recebido_ini']."', 'DD/MM/YYYY HH24:MI') AND TO_DATE('".$args['dt_recebido_fim']."', 'DD/MM/YYYY HH24:MI')" : '')."
               ".(trim($args['fl_recebido']) == 'S' ? "AND dt_recebido IS NOT NULL" : "")."
               ".(trim($args['fl_recebido']) == 'N' ? "AND dt_recebido IS NULL" : "")."
               ".(trim($args['fl_status']) != '' ? "AND (SELECT fl_status
								    		           FROM projetos.solic_entrega_documento_acompanhamento pa
								    		          WHERE pa.dt_exclusao                IS NULL
								    		            AND pa.cd_solic_entrega_documento = p.cd_solic_entrega_documento
								    		            AND pa.fl_status IN ('C', 'O', 'E')
								    		          ORDER BY pa.dt_inclusao DESC
								    		          LIMIT 1) = '".trim($args['fl_status'])."'" : "")."
            ORDER BY data_ini DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_documento_tipo()
	{
		$qr_sql = "
			SELECT cd_solic_entrega_documento_tipo AS value,
                   ds_solic_entrega_documento_tipo AS text
			  FROM projetos.solic_entrega_documento_tipo
			 WHERE dt_exclusao IS NULL;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_solic_entrega_documento)
	{
		$qr_sql = "
			SELECT p.cd_solic_entrega_documento,
				   p.cd_solic_entrega_documento_tipo,
                   p.fl_prioridade,
                   p.fl_destinatario,
                   p.ds_destinatario,
                   p.ds_observacao,
                   p.ds_endereco,
                   p.ds_contato,
                   pt.ds_solic_entrega_documento_tipo,
			       TO_CHAR(data_ini, 'DD/MM/YYYY') AS data_ini,
			       TO_CHAR(data_ini, 'HH24:MI') AS hr_ini,
			       TO_CHAR(data_fim, 'HH24:MI') AS hr_limite,
			       TO_CHAR(dt_recebido, 'DD/MM/YYYY HH24:MI') AS dt_recebido,
			       TO_CHAR(p.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       funcoes.get_usuario_nome(p.cd_usuario_recebido) AS ds_usuario_recebido,
			       funcoes.get_usuario_nome(p.cd_usuario_inclusao) AS ds_usuario_inclusao,
			       uc.nome AS ds_solicitante,
			       '9/' || uc.cd_registro_empregado::text AS ds_re,
			       uc.divisao AS cd_gerencia,
			       p.dt_recebido,
			       p.cd_usuario_inclusao,
			       (CASE WHEN p.fl_prioridade = 'U' THEN 'Urgente'
	                     WHEN p.fl_prioridade = 'M' THEN 'Moderada'
                         ELSE 'Baixa'
                   END) AS ds_prioridade,
                   (CASE WHEN p.fl_prioridade = 'U' THEN 'label label-important'
	                     WHEN p.fl_prioridade = 'M' THEN 'label label-warning'
                         ELSE 'label label-info'
                   END) AS ds_class_prioridade,
                   (SELECT COUNT(*)
    		          FROM projetos.solic_entrega_documento_acompanhamento pa
    		         WHERE pa.dt_exclusao                IS NULL
    		           AND pa.cd_solic_entrega_documento = p.cd_solic_entrega_documento
    		           AND pa.fl_status IN ('C', 'O')) AS fl_finalizado,
    		       uc.usuario || '@eletroceee.com.br' AS ds_email_solicitante
			  FROM projetos.solic_entrega_documento p
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = p.cd_usuario_inclusao
			  JOIN projetos.solic_entrega_documento_tipo pt
			    ON pt.cd_solic_entrega_documento_tipo = p.cd_solic_entrega_documento_tipo
			 WHERE cd_solic_entrega_documento = ".intval($cd_solic_entrega_documento).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_solic_entrega_documento = intval($this->db->get_new_id('projetos.solic_entrega_documento', 'cd_solic_entrega_documento'));

		$qr_sql = "
			INSERT INTO projetos.solic_entrega_documento 
                 (
                   cd_solic_entrega_documento,
                   cd_solic_entrega_documento_tipo,
	               fl_prioridade,
			       fl_destinatario,
                   ds_destinatario,
                   ds_observacao,
                   ds_endereco,
                   ds_contato,
			       data_ini,
			       data_fim,
			       cd_usuario_inclusao,
               	   cd_usuario_alteracao                 
                 )
            VALUES 
                 (		
                   ".intval($cd_solic_entrega_documento).",		     	
                   ".(trim($args['cd_solic_entrega_documento_tipo']) != '' ? intval($args['cd_solic_entrega_documento_tipo']) : "DEFAULT").",
                   ".(trim($args['fl_prioridade']) != '' ? "'".trim($args['fl_prioridade'])."'" : "DEFAULT").",
                   ".(trim($args['fl_destinatario']) != '' ? "'".trim($args['fl_destinatario'])."'" : "DEFAULT").",
                   ".(trim($args['ds_destinatario']) != '' ? str_escape($args['ds_destinatario']) : "DEFAULT").",
                   ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                   ".(trim($args['ds_endereco']) != '' ? str_escape($args['ds_endereco']) : "DEFAULT").",
                   ".(trim($args['ds_contato']) != '' ? str_escape($args['ds_contato']) : "DEFAULT").",
			       ".(trim($args['data_ini']) != '' ? "TO_TIMESTAMP('".trim($args['data_ini'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",         
			       ".(trim($args['data_fim']) != '' ? "TO_TIMESTAMP('".trim($args['data_fim'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",       
                   ".intval($args['cd_usuario']).",
                   ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_solic_entrega_documento;
	}

	public function atualizar($cd_solic_entrega_documento, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.solic_entrega_documento 
			   SET cd_solic_entrega_documento_tipo  = ".(trim($args['cd_solic_entrega_documento_tipo']) != '' ? intval($args['cd_solic_entrega_documento_tipo']) : "DEFAULT").",
			       fl_prioridade                    = ".(trim($args['fl_prioridade']) != '' ? "'".trim($args['fl_prioridade'])."'" : "DEFAULT").",
			       fl_destinatario                  = ".(trim($args['fl_destinatario']) != '' ? "'".trim($args['fl_destinatario'])."'" : "DEFAULT").",
			       ds_destinatario                  = ".(trim($args['ds_destinatario']) != '' ? str_escape($args['ds_destinatario']) : "DEFAULT").",
			       ds_observacao                    = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
			       ds_endereco                      = ".(trim($args['ds_endereco']) != '' ? str_escape($args['ds_endereco']) : "DEFAULT").",
			       ds_contato                       = ".(trim($args['ds_contato']) != '' ? str_escape($args['ds_contato']) : "DEFAULT").",
			       data_ini                         = ".(trim($args['data_ini']) != '' ? "TO_TIMESTAMP('".trim($args['data_ini'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
			       data_fim                         = ".(trim($args['data_fim']) != '' ? "TO_TIMESTAMP('".trim($args['data_fim'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
			       cd_usuario_alteracao             = ".intval($args['cd_usuario']).", 
			       dt_alteracao                     = CURRENT_TIMESTAMP
			 WHERE cd_solic_entrega_documento = ".intval($cd_solic_entrega_documento).";";

		$this->db->query($qr_sql);
	}

	public function receber($cd_solic_entrega_documento, $cd_usuario)
	{
        $qr_sql = "
            UPDATE projetos.solic_entrega_documento
               SET cd_usuario_recebido = ".intval($cd_usuario).",
                   dt_recebido         = CURRENT_TIMESTAMP
             WHERE cd_solic_entrega_documento = ".intval($cd_solic_entrega_documento).";";

        $this->db->query($qr_sql);
    }

    public function listar_acompanhamento($cd_solic_entrega_documento)
    {
    	$qr_sql = "
    		SELECT cd_solic_entrega_documento_acompanhamento,
    			   (CASE WHEN fl_status = 'A'
    			         THEN 'Acompanhamento'
    			         WHEN fl_status = 'E'
    			         THEN 'Em Atendimento'
    			         WHEN fl_status = 'O'
    			         THEN 'Concluído'
    			         ELSE 'Cancelado'
    			   END) AS ds_status,
    			   (CASE WHEN fl_status = 'A'
    			         THEN 'label label-inverse'
    			         WHEN fl_status = 'E'
    			         THEN 'label label-info'
    			         WHEN fl_status = 'O'
    			         THEN 'label label-success'
    			         ELSE 'label'
    			   END) AS ds_class_status,
    		       fl_status,
    		       ds_descricao,
    		       TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
    		       funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
    		  FROM projetos.solic_entrega_documento_acompanhamento
    		 WHERE dt_exclusao                IS NULL
    		   AND cd_solic_entrega_documento = ".intval($cd_solic_entrega_documento).";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_acompanhamento($args)
    {
    	$qr_sql = "
    		INSERT INTO projetos.solic_entrega_documento_acompanhamento
    		     (
            		cd_solic_entrega_documento, 
            		ds_descricao, 
            		fl_status, 
            		cd_usuario_inclusao, 
            		cd_usuario_alteracao
                 )
    		VALUES 
    		     (
    		     	".intval($args['cd_solic_entrega_documento']).",
    		     	".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
    		     	".(trim($args['fl_status']) != '' ? "'".trim($args['fl_status'])."'" : 'DEFAULT').",
    		     	".intval($args['cd_usuario']).",
    		     	".intval($args['cd_usuario'])."
    		     );";

    	$this->db->query($qr_sql);
    }

    public function atualizar_atualizar($cd_solic_entrega_documento_acompanhamento, $args = array())
    {
    	$qr_sql = "
    		UPDATE projetos.solic_entrega_documento_acompanhamento
			   SET ds_descricao         = ".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",
			       fl_status            = ".(trim($args['fl_status']) != '' ? "'".trim($args['fl_status'])."'" : 'DEFAULT').",
			       cd_usuario_alteracao = ".intval($args['cd_usuario']).", 
			       dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_solic_entrega_documento_acompanhamento = ".intval($cd_solic_entrega_documento_acompanhamento).";";

		$this->db->query($qr_sql);
    }	

}