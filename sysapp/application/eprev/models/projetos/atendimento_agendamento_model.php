<?php
class Atendimento_agendamento_model extends Model
{
    function __construct()
    {
        parent::Model();

        CheckLogin();
    }
	
    public function listar($args = array())
    {
      	$qr_sql = "
         	SELECT cd_atendimento_agendamento,
         	  	   TO_CHAR(aa.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
         		   TO_CHAR(aa.dt_agenda,'DD/MM/YYYY HH24:MI') AS dt_agenda,
          		   aa.nome,
          		   aa.cpf,
          		   aa.cd_empresa,
          		   aa.cd_registro_empregado,
          		   aa.seq_dependencia,
          		   aat.ds_atendimento_agendamento_tipo || (CASE WHEN aa.ds_tipo IS NOT NULL
          		                                                THEN ' - ' || aa.ds_tipo
          		                                                ELSE ''
          		                                          END) AS ds_atendimento_agendamento_tipo,
          		   aa.email,
          		   aa.telefone_1,
          		   aa.telefone_2,
          		   funcoes.get_usuario_nome(aa.cd_usuario_cancelado) AS ds_usuario_cancelado,
          		   funcoes.get_usuario_nome(aa.cd_usuario_inclusao) AS ds_usuario_inclusao,
          		   TO_CHAR(aa.dt_cancelado,'DD/MM/YYYY HH24:MI:SS') AS dt_cancelado,
          		   ds_justificativa_cancelamento,
          		   aa.fl_atendimento AS fl_compareceu,
                   (CASE WHEN aa.fl_atendimento = 'N' THEN 'Não'	
                         ELSE 'Sim'
                   END) AS ds_compareceu,
                   aa.cd_atendimento, 
				   aa.tp_agendamento,
				   CASE WHEN aa.tp_agendamento = 'V' THEN 'VIRTUAL'
				        WHEN aa.tp_agendamento = 'P' THEN 'PRESENCIAL'
                        WHEN aa.tp_agendamento = 'T' THEN 'TELEFONICO'
						ELSE 'NAO INFORMADO'
				   END AS ds_tipo_agendamento
              FROM projetos.atendimento_agendamento aa
              JOIN projetos.atendimento_agendamento_tipo aat
                ON aa.cd_atendimento_agendamento_tipo = aat.cd_atendimento_agendamento_tipo
             WHERE aa.dt_exclusao IS NULL
          	   ".(((trim($args['dt_agenda_ini']) != '') AND (trim($args['dt_agenda_fim']) != '')) ? " AND DATE_TRUNC('day', dt_agenda) BETWEEN TO_DATE('".$args['dt_agenda_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_agenda_fim']."', 'DD/MM/YYYY')" : "")."  
		   	   ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? " AND DATE_TRUNC('day', aa.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."  
		   	   ".(((trim($args['dt_cancelamento_ini']) != '') AND (trim($args['dt_cancelamento_fim']) != '')) ? " AND DATE_TRUNC('day', dt_cancelado) BETWEEN TO_DATE('".$args['dt_cancelamento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_cancelamento_fim']."', 'DD/MM/YYYY')" : "")."  
		   	   ".(trim($args['cd_empresa']) != '' ? "AND cd_empresa = ".intval($args['cd_empresa']) : "")."
		   	   ".(trim($args['cd_registro_empregado']) != '' ? "AND cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
           	   ".(trim($args['seq_dependencia']) != '' ? "AND seq_dependencia = ".intval($args['seq_dependencia']) : "")."	           		
           	   ".(trim($args['fl_cancelado']) == 'S' ? "AND dt_cancelado IS NOT NULL" : '')."
		       ".(trim($args['fl_cancelado']) == 'N' ? "AND dt_cancelado IS NULL" : '')."
		   	   ".(trim($args['fl_compareceu']) == 'S' ? "AND fl_atendimento = 'S'" : '')."
		       ".(trim($args['fl_compareceu']) == 'N' ? "AND fl_atendimento = 'N'" : '')."
		   	   ".(trim($args['nome']) != '' ? " AND UPPER(aa.nome) like UPPER('%".trim($args['nome'])."%')" : "").";";

     	return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_agendamento($cd_atendimento_agendamento)
    {
    	$qr_sql = "
	         SELECT aa.cd_atendimento_agendamento,
	         		TO_CHAR(aa.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
	         		TO_CHAR(aa.dt_agenda,'DD/MM/YYYY HH24:MI:SS') AS dt_agenda,
	          		aa.nome,
	          		aa.cpf,
	          		aa.cd_empresa,
	          		aa.cd_registro_empregado,
	          		aa.seq_dependencia,
					aa.ds_link_zoom,
					aa.ds_senha_zoom,
					aa.cd_usuario_envio_email,
	          		aat.ds_atendimento_agendamento_tipo || (CASE WHEN aa.ds_tipo IS NOT NULL
	          		                                             THEN ' - ' || aa.ds_tipo
	          		                                             ELSE ''
	          		                                       END) AS ds_atendimento_agendamento_tipo,
	          		aa.email,
	          		aa.telefone_1,
	          		aa.telefone_2,
	          		TO_CHAR(aa.dt_cancelado,'DD/MM/YYYY HH24:MI:SS') AS dt_cancelado,
				   aa.tp_agendamento,
				   CASE WHEN aa.tp_agendamento = 'V' THEN 'VIRTUAL'
				        WHEN aa.tp_agendamento = 'P' THEN 'PRESENCIAL'
                        WHEN aa.tp_agendamento = 'T' THEN 'TELEFONICO'
						ELSE 'NAO INFORMADO'
				   END AS ds_tipo_agendamento					
	           FROM projetos.atendimento_agendamento aa
	           JOIN projetos.atendimento_agendamento_tipo aat
	             ON aa.cd_atendimento_agendamento_tipo = aat.cd_atendimento_agendamento_tipo
	          WHERE aa.dt_exclusao IS NULL
	          	AND aa.cd_atendimento_agendamento = ".intval($cd_atendimento_agendamento).";";

     	return $this->db->query($qr_sql)->row_array();
    }

    public function cancelar_agendamento($cd_atendimento_agendamento,$cd_usuario_exclusao,$ds_justificativa_cancelamento )
    {
    	$qr_sql = "
            UPDATE projetos.atendimento_agendamento
               SET cd_usuario_cancelado			 = ".intval($cd_usuario_exclusao).",
                   dt_cancelado         		 = CURRENT_TIMESTAMP,
                   ds_justificativa_cancelamento = '".trim($ds_justificativa_cancelamento)."'
             WHERE cd_atendimento_agendamento 	 = ".intval($cd_atendimento_agendamento).";"; 

        $this->db->query($qr_sql);
    }


    public function get_data_agenda()
    {
    	$qr_sql = "
    		SELECT dt_dia || ' - ' || dia_da_semana AS text,
         		   dt_dia AS value
              FROM autoatendimento.atendimento_agendamento_agenda();";

        return $this->db->query($qr_sql)->result_array();
    } 

    public function get_tipo()
    {
    	$qr_sql = "
    		SELECT aat.cd_atendimento_agendamento_tipo AS value,
				   aat.ds_atendimento_agendamento_tipo AS text,
                   aat.fl_especificar
			  FROM projetos.atendimento_agendamento_tipo aat
			 WHERE aat.dt_exclusao IS NULL
			 ORDER BY (CASE WHEN fl_especificar = 'S'
                            THEN 1
                            ELSE 0
                      END) ASC, aat.ds_atendimento_agendamento_tipo;";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function get_re_cripto($cd_empresa,$rcd_registro_empregado,$seq_dependencia)
    {
		$qr_sql = "
     	    SELECT cripto_re AS re_cripto 
     	      FROM funcoes.cripto_re(".intval($cd_empresa).",".intval($rcd_registro_empregado).",".intval($seq_dependencia).");";   

    	return $this->db->query($qr_sql)->row_array();
    }

    public function salvar_compareceu($args = array())
    {
		$qr_sql =  "
			UPDATE projetos.atendimento_agendamento
		   	   SET fl_atendimento		  	  = ".(trim($args['fl_compareceu']) != '' ?  "'".trim($args['fl_compareceu'])."'" : "DEFAULT")."
		     WHERE cd_atendimento_agendamento = ".intval($args['cd_atendimento_agendamento']).";"; 

        $this->db->query($qr_sql);
    }

    public function salvar_editar_agendamento($args = array())
    {
      $qr_sql = "
         UPDATE projetos.atendimento_agendamento
            SET ds_link_zoom                = ".(trim($args['ds_link_zoom']) != '' ?  "'".trim($args['ds_link_zoom'])."'" : "DEFAULT").",
                email                       = ".(trim($args['email']) != '' ?  "'".trim($args['email'])."'" : "DEFAULT").",
                ds_senha_zoom               = ".(trim($args['ds_senha_zoom']) != '' ?  "'".trim($args['ds_senha_zoom'])."'" : "DEFAULT")."
          WHERE cd_atendimento_agendamento  = ".intval($args['cd_atendimento_agendamento']).";"; 

        $this->db->query($qr_sql);
    }

    public function salvar_envio_email($args = array())
    {
      $qr_sql = "
         UPDATE projetos.atendimento_agendamento
            SET dt_envio_email              = CURRENT_TIMESTAMP,
                cd_usuario_envio_email      = ".intval($args['cd_usuario_envio_email'])."
          WHERE cd_atendimento_agendamento  = ".intval($args['cd_atendimento_agendamento']).";"; 

        $this->db->query($qr_sql);
    }
}

				