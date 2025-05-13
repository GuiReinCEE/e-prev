<?php
class Formulario_inscricao_eleicao_model extends model
{
	function __construct()
	{
		parent::Model();

		CheckLogin();
	}

	public function listar($args = array())
    {
    	$qr_sql = "
	        SELECT cd_formulario_inscricao_eleicao,
	               ds_codigo,
                   (CASE WHEN dt_cancelamento IS NOT NULL
                         THEN 'Inscrição cancelada'
                         WHEN dt_aprovacao IS NOT NULL
                         THEN 'Inscrição Homologada'
                         WHEN dt_reprovacao IS NOT NULL
                         THEN 'Inscrição Impugnada'
                         ELSE 'Inscrição em andamento'
                   END) AS ds_status,
                   (CASE WHEN dt_cancelamento IS NOT NULL
                        THEN 'label label'
                        WHEN dt_aprovacao IS NOT NULL
                        THEN 'label label-success'
                        WHEN dt_reprovacao IS NOT NULL
                        THEN 'label label-important'
                        ELSE 'label label-info'
                   END) AS class_status,
	               tp_cargo,
                   (CASE WHEN tp_cargo = 'DE' 
                        THEN 'label label-success'
                        WHEN tp_cargo = 'CF' 
                        THEN 'label label-warning'
                        WHEN tp_cargo = 'CAP' 
                        THEN 'label'
                        ELSE 'label label-info'
                   END) AS class_cargo,
	               ds_nome,
	               ds_cpf,
	               ds_vinculacao,
	               ds_telefone_1,
	               ds_telefone_2,
	               ds_email_1, 
	               ds_email_2, 
	               TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
	               TO_CHAR(dt_cancelamento,'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
	               TO_CHAR(dt_aprovacao,'DD/MM/YYYY HH24:MI:SS') AS dt_aprovacao,
                   TO_CHAR(dt_reprovacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_reprovacao,
	               funcoes.get_usuario_nome(cd_usuario_aprovacao) AS ds_usuario_aprovacao,
    			   ds_nome_representante,
    			   ds_cpf_representante,
    			   ds_telefone_representante,
    			   ds_email_representante			   
	          FROM gestao.formulario_inscricao_eleicao
	         WHERE 1=1
                ".(trim($args['nr_ano']) != '' ? "AND nr_ano = ".intval($args['nr_ano']) : "")."
	            ".(trim($args['tp_cargo']) != '' ? "AND tp_cargo = '".trim($args['tp_cargo'])."'" : "")."
	            ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? " AND DATE_TRUNC('day', dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")." 
	            ".(trim($args['fl_cancelamento']) == 'S' ? "AND dt_cancelamento IS NOT NULL" : '')."
			   	".(trim($args['fl_cancelamento']) == 'N' ? "AND dt_cancelamento IS NULL" : '')."
			   	".(trim($args['fl_aprovacao']) == 'S' ? "AND dt_aprovacao IS NOT NULL" : '')."
			   	".(trim($args['fl_aprovacao']) == 'N' ? "AND dt_aprovacao IS NULL" : '')."
                ".(trim($args['fl_status']) == 'CA' ? "AND dt_cancelamento IS NOT NULL" : '')."
                ".(trim($args['fl_status']) == 'AP' ? "AND dt_aprovacao IS NOT NULL" : '')."
                ".(trim($args['fl_status']) == 'IN' ? "AND dt_reprovacao IS NOT NULL" : '')."
                ".(trim($args['fl_status']) == 'AN' ? "AND dt_cancelamento IS NULL AND dt_aprovacao IS NULL AND dt_reprovacao IS NULL" : '').";";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function listar_cadastro($cd_formulario_inscricao_eleicao)
    {
    	$qr_sql = "
           SELECT tp_cargo,
                  ds_patrocinador,
                  cd_formulario_inscricao_eleicao,
                  (CASE WHEN tp_cargo = 'DE' 
                        THEN 'label label-success'
                        WHEN tp_cargo = 'CF' 
                        THEN 'label label-warning'
                        WHEN tp_cargo = 'CAP' 
                        THEN 'label'
                        ELSE 'label label-info'
                  END) AS class_cargo,
                  TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                  TO_CHAR(dt_encaminha_pendencia,'DD/MM/YYYY HH24:MI:SS') AS dt_encaminha_pendencia,
           		  ds_codigo,
                  (CASE WHEN dt_cancelamento IS NOT NULL
                         THEN 'Inscrição cancelada'
                         WHEN dt_aprovacao IS NOT NULL
                         THEN 'Inscrição Homologada'
                         WHEN dt_reprovacao IS NOT NULL
                         THEN 'Inscrição Impugnada'
                         ELSE 'Inscrição em andamento'
                  END) AS ds_status,
                  (CASE WHEN dt_cancelamento IS NOT NULL
                        THEN 'label label '
                        WHEN dt_aprovacao IS NOT NULL
                        THEN 'label label-success'
                        WHEN dt_reprovacao IS NOT NULL
                        THEN 'label label-important'
                        ELSE 'label label-info'
                  END) AS class_status,
                  (CASE WHEN dt_cancelamento IS NOT NULL
                         THEN 'CA'
                         WHEN dt_aprovacao IS NOT NULL
                         THEN 'AP'
                         WHEN dt_reprovacao IS NOT NULL
                         THEN 'IN'
                         ELSE 'AN'
                  END) AS tp_status,
           		  ds_nome,
         		  ds_cpf,
         		  ds_vinculacao,
         		  ds_telefone_1,
         		  ds_telefone_2,
         		  ds_email_1,
         		  ds_email_2,
         		  fl_representante,
         		  (CASE WHEN fl_representante = 'N' THEN 'Não'	
	                      ELSE 'Sim'
	              END) AS ds_representante,
    			  ds_nome_representante,
    			  ds_cpf_representante,
    			  ds_telefone_representante,
    			  ds_email_representante,
    			  TO_CHAR(dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
                  TO_CHAR(dt_aprovacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_aprovacao,
    			  TO_CHAR(dt_reprovacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_reprovacao,
    			  funcoes.get_usuario_nome(cd_usuario_aprovacao) AS ds_usuario_aprovacao,
                  arquivo_identidade,
                  declaracao_atividade,
                  certidao_negativa,
                  comprovante_nivel_superior,
                  comprovante_residencia,
                  certidao_comite,
                  ds_qualificacao,
                  (SELECT COUNT(*)
                      FROM gestao.formulario_inscricao_eleicao_acompanhamento a
                     WHERE a.dt_exclusao                                    IS NULL
                       AND a.tp_formulario_inscricao_eleicao_acompanhamento = 'S'
                       AND a.cd_formulario_inscricao_eleicao                = f.cd_formulario_inscricao_eleicao
                       AND a.dt_encaminhado                                 IS NULL) AS qt_pendencia
    		 FROM gestao.formulario_inscricao_eleicao f
    	    WHERE cd_formulario_inscricao_eleicao = ".intval($cd_formulario_inscricao_eleicao).";";
           
        return $this->db->query($qr_sql)->row_array();
    }

    public function listar_acompanhamento($cd_formulario_inscricao_eleicao)
    {
        $qr_sql = "
           SELECT tp_formulario_inscricao_eleicao_acompanhamento,
                  ds_formulario_inscricao_eleicao_acompanhamento,
                  cd_formulario_inscricao_eleicao,
                  cd_formulario_inscricao_eleicao_acompanhamento,
                  fl_solicitacao,
                  (CASE WHEN fl_solicitacao = 'S'
                         THEN 'Atendeu'
                         WHEN fl_solicitacao = 'N'
                         THEN 'Não atendeu'
                         WHEN dt_encaminhado IS NOT NULL
                         THEN 'Aguardando Validação'
                         ElSE 'Pendente de Recurso'
                  END) AS ds_solicitacao,
                  (CASE WHEN fl_solicitacao = 'S'
                         THEN 'label label-success'
                         WHEN fl_solicitacao = 'N'
                         THEN 'label label-important'
                         WHEN dt_encaminhado IS NOT NULL
                         THEN 'label label-info'
                         ElSE 'label label-warning'
                  END) AS ds_class_solicitacao,
                  (CASE WHEN tp_formulario_inscricao_eleicao_acompanhamento = 'A' 
                        THEN 'Registro Interno'
                        WHEN tp_formulario_inscricao_eleicao_acompanhamento = 'S' 
                        THEN 'Pendência de Inscrição'
                        WHEN tp_formulario_inscricao_eleicao_acompanhamento = 'I' 
                        THEN 'Impugnação de Inscrição'
                        WHEN tp_formulario_inscricao_eleicao_acompanhamento = 'H' 
                        THEN 'Inscrição Homologada'
                        ELSE ''
                  END) AS ds_tp_acompanhamento,
                  TO_CHAR(dt_encaminhado,'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhado,
                  TO_CHAR(dt_solicitacao_atendida,'DD/MM/YYYY HH24:MI:SS') AS dt_solicitacao_atendida,
                  funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao,
                  TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                  (SELECT COUNT(*)
                     FROM gestao.formulario_inscricao_eleicao_acompanhamento_anexo an
                    WHERE an.dt_exclusao                                    IS NULL
                      AND an.cd_formulario_inscricao_eleicao_acompanhamento = a.cd_formulario_inscricao_eleicao_acompanhamento) AS qt_anexo
             FROM gestao.formulario_inscricao_eleicao_acompanhamento a
            WHERE dt_exclusao is null
              AND cd_formulario_inscricao_eleicao = ".intval($cd_formulario_inscricao_eleicao).";";
            
        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_acompanhamento($args = array())
    {
        $qr_sql = "
           INSERT INTO gestao.formulario_inscricao_eleicao_acompanhamento
                  (
                      cd_formulario_inscricao_eleicao,
                      tp_formulario_inscricao_eleicao_acompanhamento,
                      ds_formulario_inscricao_eleicao_acompanhamento,
                      cd_usuario_inclusao,
                      cd_usuario_alteracao
                  )
           VALUES     
                  (
                      ".intval($args['cd_formulario_inscricao_eleicao']).",
                      '".trim($args['tp_formulario_inscricao_eleicao_acompanhamento'])."',
                      ".str_escape($args['ds_formulario_inscricao_eleicao_acompanhamento']).",
                      ".intval($args['cd_usuario']).",
                      ".intval($args['cd_usuario'])."
                  );";

        if(trim($args['tp_formulario_inscricao_eleicao_acompanhamento']) == 'I')
        {
            $qr_sql .= "
                UPDATE gestao.formulario_inscricao_eleicao
                   SET cd_usuario_reprovacao = ".intval($args['cd_usuario']).",
                       dt_reprovacao         = CURRENT_TIMESTAMP,
                       cd_usuario_aprovacao  = NULL,
                       dt_aprovacao          = NULL 
                 WHERE cd_formulario_inscricao_eleicao = ".intval($args['cd_formulario_inscricao_eleicao']).";";
        }

        $this->db->query($qr_sql);
    }

    public function encaminhar_pendencia($cd_formulario_inscricao_eleicao, $cd_usuario)
    {
        $qr_sql = "
            UPDATE gestao.formulario_inscricao_eleicao
               SET cd_usuario_encaminha_pendencia = ".intval($cd_usuario).",
                   dt_encaminha_pendencia         = CURRENT_TIMESTAMP
             WHERE cd_formulario_inscricao_eleicao = ".intval($cd_formulario_inscricao_eleicao).";";

        $this->db->query($qr_sql);
    }

    public function aprovar_inscricao($cd_formulario_inscricao_eleicao, $cd_usuario)
    {
        $qr_sql = "
           INSERT INTO gestao.formulario_inscricao_eleicao_acompanhamento
                  (
                      cd_formulario_inscricao_eleicao,
                      tp_formulario_inscricao_eleicao_acompanhamento,
                      ds_formulario_inscricao_eleicao_acompanhamento,
                      cd_usuario_inclusao,
                      cd_usuario_alteracao
                  )
           VALUES     
                  (
                      ".intval($cd_formulario_inscricao_eleicao).",
                      'H',
                      (SELECT (CASE WHEN cd_usuario_reprovacao IS NULL 
                                    THEN 'Inscrição Homologada'
                                    ElSE 'Inscrição Homologada por Aceite dos Recursos'
                              END )
                         FROM gestao.formulario_inscricao_eleicao
                        WHERE cd_formulario_inscricao_eleicao = ".intval($cd_formulario_inscricao_eleicao)."),
                      ".intval($cd_usuario).",
                      ".intval($cd_usuario)."
                  );

            UPDATE gestao.formulario_inscricao_eleicao
               SET cd_usuario_aprovacao  = ".intval($cd_usuario).",
                   dt_aprovacao          = CURRENT_TIMESTAMP,
                   cd_usuario_reprovacao = NULL,
                   dt_reprovacao         = NULL
             WHERE cd_formulario_inscricao_eleicao = ".intval($cd_formulario_inscricao_eleicao).";";

        $this->db->query($qr_sql);
    }

    public function cancelar_inscricao($cd_formulario_inscricao_eleicao)
    {
        $qr_sql = "
            UPDATE gestao.formulario_inscricao_eleicao
               SET dt_cancelamento = CURRENT_TIMESTAMP
             WHERE cd_formulario_inscricao_eleicao = ".intval($cd_formulario_inscricao_eleicao).";";

        $this->db->query($qr_sql);
    }

    public function listar_arquivo($cd_formulario_inscricao_eleicao, $cd_formulario_inscricao_eleicao_acompanhamento)
    {
        $qr_sql = "
           SELECT fie.tp_cargo,
                  fie.ds_codigo,
                  fie.cd_formulario_inscricao_eleicao,
                  (CASE WHEN fie.tp_cargo = 'DE' 
                        THEN 'label label-success'
                        WHEN fie.tp_cargo = 'CF' 
                        THEN 'label label-warning'
                        WHEN fie.tp_cargo = 'CAP' 
                        THEN 'label'
                        ELSE 'label label-info'
                  END) AS class_cargo,
                  TO_CHAR(fie.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                  ds_codigo,
                  (CASE WHEN fie.dt_cancelamento IS NOT NULL
                         THEN 'Inscrição cancelada'
                         WHEN fie.dt_aprovacao IS NOT NULL
                         THEN 'Inscrição Homologada'
                         WHEN fie.dt_reprovacao IS NOT NULL
                         THEN 'Inscrição Impugnada'
                         ELSE 'Inscrição em andamento'
                  END) AS ds_status,
                  (CASE WHEN fie.dt_cancelamento IS NOT NULL
                        THEN 'label label '
                        WHEN fie.dt_aprovacao IS NOT NULL
                        THEN 'label label-success'
                        WHEN fie.dt_reprovacao IS NOT NULL
                        THEN 'label label-important'
                        ELSE 'label label-info'
                  END) AS class_status,
                  fie.ds_nome,
                  fie.ds_cpf,
                  TO_CHAR(fie.dt_cancelamento,'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
                  TO_CHAR(fie.dt_aprovacao,'DD/MM/YYYY HH24:MI:SS') AS dt_aprovacao,
                  TO_CHAR(fie.dt_reprovacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_reprovacao,
                  funcoes.get_usuario_nome(fie.cd_usuario_aprovacao) AS ds_usuario_aprovacao,
                  fiea.tp_formulario_inscricao_eleicao_acompanhamento,
                  fiea.ds_formulario_inscricao_eleicao_acompanhamento,
                  fiea.cd_formulario_inscricao_eleicao_acompanhamento,
                  fiea.fl_solicitacao,
                  (CASE WHEN fiea.fl_solicitacao = 'S'
                         THEN 'Atendeu'
                         WHEN fiea.fl_solicitacao = 'N'
                         THEN 'Não atendeu'
                         WHEN fiea.dt_encaminhado IS NOT NULL
                         THEN 'Aguardando Validação'
                         ElSE 'Pendente de Recurso'
                  END) AS ds_solicitacao,
                  (CASE WHEN fiea.fl_solicitacao = 'S'
                         THEN 'label label-success'
                         WHEN fiea.fl_solicitacao = 'N'
                         THEN 'label label-important'
                         WHEN fiea.dt_encaminhado IS NOT NULL
                         THEN 'label label-info'
                         ElSE 'label label-warning'
                  END) AS ds_class_solicitacao,
                  (CASE WHEN fiea.tp_formulario_inscricao_eleicao_acompanhamento = 'A' 
                        THEN 'Registro Interno'
                        WHEN fiea.tp_formulario_inscricao_eleicao_acompanhamento = 'S' 
                        THEN 'Pendência de Inscrição'
                        ELSE ''
                  END) AS ds_tp_acompanhamento,
                  TO_CHAR(fiea.dt_encaminhado,'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhado,
                  TO_CHAR(fiea.dt_solicitacao_atendida,'DD/MM/YYYY HH24:MI:SS') AS dt_solicitacao_atendida,
                  funcoes.get_usuario_nome(fiea.cd_usuario_inclusao) AS ds_usuario_inclusao_acompanhamento,
                  TO_CHAR(fiea.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao_acompanhamento
             FROM gestao.formulario_inscricao_eleicao fie
             JOIN gestao.formulario_inscricao_eleicao_acompanhamento fiea
               ON fie.cd_formulario_inscricao_eleicao = fiea.cd_formulario_inscricao_eleicao
            WHERE fiea.cd_formulario_inscricao_eleicao_acompanhamento = ".intval($cd_formulario_inscricao_eleicao_acompanhamento).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_atendeu($cd_formulario_inscricao_eleicao_acompanhamento, $cd_usuario)
    {
        $qr_sql =  "
            UPDATE gestao.formulario_inscricao_eleicao_acompanhamento
               SET fl_solicitacao                   = 'S',
                   dt_solicitacao_atendida          = CURRENT_TIMESTAMP,
                   cd_usuario_solicitacao_atendida  = ".intval($cd_usuario)."
             WHERE cd_formulario_inscricao_eleicao_acompanhamento = ".intval($cd_formulario_inscricao_eleicao_acompanhamento).";"; 

        $this->db->query($qr_sql);
    }

    public function salvar_nao_atendeu($cd_formulario_inscricao_eleicao_acompanhamento, $ds_formulario_inscricao_eleicao_acompanhamento, $cd_usuario)
    {
        $qr_sql =  "
            UPDATE gestao.formulario_inscricao_eleicao_acompanhamento
               SET fl_solicitacao                                 = 'N',
                   dt_solicitacao_atendida                        = CURRENT_TIMESTAMP,
                   cd_usuario_solicitacao_atendida                = ".intval($cd_usuario).",
                   dt_encaminhado                                 = DEFAULT,
                   ds_formulario_inscricao_eleicao_acompanhamento = ds_formulario_inscricao_eleicao_acompanhamento 
                    || '\n\n' 
                    || 'Solicitação não foi atendida.'
                    || '\n' 
                    || TO_CHAR(CURRENT_TIMESTAMP, 'DD/MM/YYYY HH24:MI:SS') 
                    || '\n' 
                    || ".str_escape($ds_formulario_inscricao_eleicao_acompanhamento)."
             WHERE cd_formulario_inscricao_eleicao_acompanhamento = ".intval($cd_formulario_inscricao_eleicao_acompanhamento).";"; 
   
        $this->db->query($qr_sql);
    }

    public function listar_acompanhamento_anexo($cd_formulario_inscricao_eleicao_acompanhamento)
    {
        $qr_sql = "
            SELECT cd_formulario_inscricao_eleicao_acompanhamento_anexo,
                   ds_formulario_inscricao_eleicao_acompanhamento_anexo,
                   arquivo,
                   convert_from(convert_to(arquivo_nome,'utf-8'),'latin-1') AS arquivo_nome,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   TO_CHAR(dt_exclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_exclusao 
              FROM gestao.formulario_inscricao_eleicao_acompanhamento_anexo
             WHERE cd_formulario_inscricao_eleicao_acompanhamento = ".intval($cd_formulario_inscricao_eleicao_acompanhamento)."
               AND dt_exclusao IS NULL;";
            
        return $this->db->query($qr_sql)->result_array();
    }

    public function get_ds_codigo($cd_formulario_inscricao_eleicao)
    {
       $qr_sql = "
          SELECT ds_codigo
            FROM gestao.formulario_inscricao_eleicao
           WHERE cd_formulario_inscricao_eleicao = ".intval($cd_formulario_inscricao_eleicao).";
       ";

       return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_anexos($args = array())
    {
        $qr_sql = "
           INSERT INTO gestao.formulario_inscricao_eleicao_anexo
                  (
                      cd_formulario_inscricao_eleicao,
                      arquivo,
                      arquivo_nome,
                      dt_inclusao,
                      cd_usuario_inclusao
                  )
           VALUES     
                  (
                      ".intval($args['cd_formulario_inscricao_eleicao']).",
                      '".trim($args['arquivo'])."',
                      '".trim($args['arquivo_nome'])."',
                      CURRENT_TIMESTAMP,
                      ".intval($args['cd_usuario'])."
                  );";


        $this->db->query($qr_sql);

    }

    public function listar_anexos($cd_formulario_inscricao_eleicao)
    {
        $qr_sql = "
            SELECT cd_formulario_inscricao_eleicao_anexo,
                   cd_formulario_inscricao_eleicao,
                   arquivo,
                   convert_from(convert_to(arquivo_nome,'utf-8'),'latin-1') AS arquivo_nome,
                   TO_CHAR(dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
              FROM gestao.formulario_inscricao_eleicao_anexo
             WHERE cd_formulario_inscricao_eleicao = ".intval($cd_formulario_inscricao_eleicao)."
               AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function excluir_anexo($cd_formulario_inscricao_eleicao_anexo,$cd_usuario)
    {
       $qr_sql =  "
            UPDATE gestao.formulario_inscricao_eleicao_anexo
               SET dt_exclusao           = CURRENT_TIMESTAMP,
                   cd_usuario_exclusao   = ".intval($cd_usuario)."
             WHERE cd_formulario_inscricao_eleicao_anexo = ".intval($cd_formulario_inscricao_eleicao_anexo).";"; 

        $this->db->query($qr_sql);
    }
}