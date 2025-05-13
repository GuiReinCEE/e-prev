<?php
class Protocolo_gc_investimentos_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
	
    public function get_usuarios($cd_divisao)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($cd_divisao)."')";

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar($args = array())
    {
        $qr_sql = "
            SELECT pgi.cd_protocolo_gc_investimentos,
                   pgi.documento,
                   TO_CHAR(pgi.dt_envio_gc, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_gc,
                   TO_CHAR(pgi.dt_recebido, 'DD/MM/YYYY HH24:MI:SS') AS dt_recebido,
                   TO_CHAR(pgi.dt_envio_sg, 'DD/MM/YYYY') AS dt_envio_sg,
                   TO_CHAR(pgi.dt_expedicao, 'DD/MM/YYYY') AS dt_expedicao,
                   TO_CHAR(pgi.dt_encerrar, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerrar,
				   TO_CHAR(pgi.dt_recusado, 'DD/MM/YYYY HH24:MI:SS') AS dt_recusado,
                   fl_retorno,
                   arquivo,
                   arquivo_nome
              FROM projetos.protocolo_gc_investimentos pgi
             WHERE dt_exclusao IS NULL
               ".(((trim($args['dt_envio_gc_ini']) != '') AND  (trim($args['dt_envio_gc_fim']) != "")) ? "AND DATE_TRUNC('day', pgi.dt_envio_gc) BETWEEN TO_DATE('".$args['dt_envio_gc_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_gc_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_recebido_ini']) != '') AND  (trim($args['dt_recebido_fim']) != "")) ? "AND DATE_TRUNC('day', pgi.dt_recebido) BETWEEN TO_DATE('".$args['dt_recebido_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_recebido_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_envio_sg_ini']) != '') AND  (trim($args['dt_envio_sg_fim']) != "")) ? "AND DATE_TRUNC('day', pgi.dt_envio_sg) BETWEEN TO_DATE('".$args['dt_envio_sg_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_sg_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_expedicao_ini']) != '') AND  (trim($args['dt_expedicao_fim']) != "")) ? "AND DATE_TRUNC('day', pgi.dt_expedicao) BETWEEN TO_DATE('".$args['dt_expedicao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_expedicao_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_encerrar_ini']) != '') AND  (trim($args['dt_encerrar_fim']) != "")) ? "AND DATE_TRUNC('day', pgi.dt_encerrar) BETWEEN TO_DATE('".$args['dt_encerrar_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encerrar_fim']."', 'DD/MM/YYYY')" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }	
	
    public function carrega($cd_protocolo_gc_investimentos)
    {
        $qr_sql = "
            SELECT gci.cd_protocolo_gc_investimentos,
                   gci.documento,
                   gci.observacao,
                   TO_CHAR(gci.dt_envio_gc, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_gc,
                   funcoes.get_usuario_nome(gci.cd_usuario_envio_gc) AS ds_usuario_envio_gc,
                   gci.cd_usuario_sg,
                   funcoes.get_usuario_nome(gci.cd_usuario_sg) AS ds_usuario_sg,
                   funcoes.get_usuario_area(gci.cd_usuario_sg) AS cd_gerencia_sg,
                   TO_CHAR(gci.dt_recebido, 'DD/MM/YYYY HH24:MI:SS') AS dt_recebido,
                   funcoes.get_usuario_nome(gci.cd_usuario_recebido) AS ds_usuario_recebido,
                   TO_CHAR(gci.dt_envio_sg, 'DD/MM/YYYY') AS dt_envio_sg,
                   TO_CHAR(gci.dt_expedicao, 'DD/MM/YYYY') AS dt_expedicao,
                   TO_CHAR(gci.dt_encerrar, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerrar,
                   funcoes.get_usuario_nome(gci.cd_usuario_encerrar) AS ds_usuario_encerrar,
                   TO_CHAR(gci.dt_recusado, 'DD/MM/YYYY HH24:MI:SS') AS dt_recusado,
                   funcoes.get_usuario_nome(gci.cd_usuario_recusado) AS ds_usuario_recusado,
				   gci.ds_justificativa,
                   fl_retorno,
                   arquivo,
                   arquivo_nome,
                   ds_doc_pendente
              FROM projetos.protocolo_gc_investimentos gci 
             WHERE gci.cd_protocolo_gc_investimentos = ".intval($cd_protocolo_gc_investimentos).";";

        return $this->db->query($qr_sql)->row_array();
    }
	
    public function salvar($args = array())
    {
        $cd_protocolo_gc_investimentos = intval($this->db->get_new_id(
            'projetos.protocolo_gc_investimentos', 
            'cd_protocolo_gc_investimentos'
        ));

        $qr_sql = "
            INSERT INTO projetos.protocolo_gc_investimentos
                 (
                    cd_protocolo_gc_investimentos,
                    documento,
                    observacao,
                    cd_usuario_sg,
                    fl_retorno,
                    arquivo,
                    arquivo_nome,
                    ds_doc_pendente,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES
                 (
                    ".intval($cd_protocolo_gc_investimentos).",
                    ".(trim($args['documento']) != '' ? str_escape($args['documento']) : "DEFAULT").",
                    ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
                    ".(trim($args['cd_usuario_sg']) != '' ? intval($args['cd_usuario_sg']) : "DEFAULT").",
                    ".(trim($args['fl_retorno']) != '' ? str_escape($args['fl_retorno']) : "DEFAULT").",
                    ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
                    ".(trim($args['ds_doc_pendente']) != '' ? str_escape($args['ds_doc_pendente']) : "DEFAULT").",
                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);

        return $cd_protocolo_gc_investimentos;
    }

    public function atualizar($cd_protocolo_gc_investimentos, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.protocolo_gc_investimentos
               SET documento            = ".(trim($args['documento']) != '' ? str_escape($args['documento']) : "DEFAULT").",
                   observacao           = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
                   cd_usuario_sg        = ".(trim($args['cd_usuario_sg']) != '' ? intval($args['cd_usuario_sg']) : "DEFAULT").",
                   dt_envio_sg          = ".(trim($args['dt_envio_sg']) != '' ? "TO_DATE('".$args['dt_envio_sg']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   dt_expedicao         = ".(trim($args['dt_expedicao']) != '' ? "TO_DATE('".$args['dt_expedicao']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   fl_retorno           = ".(trim($args['fl_retorno']) != '' ? str_escape($args['fl_retorno']) : "DEFAULT").",
                   arquivo              = ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
                   arquivo_nome         = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
                   ds_doc_pendente      = ".(trim($args['ds_doc_pendente']) != '' ? str_escape($args['ds_doc_pendente']) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_protocolo_gc_investimentos = ".intval($cd_protocolo_gc_investimentos).";";

        $this->db->query($qr_sql);
    }

    public function enviar_gc($cd_protocolo_gc_investimentos, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.protocolo_gc_investimentos
               SET cd_usuario_envio_gc = ".intval($cd_usuario).",
                   dt_envio_gc         = CURRENT_TIMESTAMP
             WHERE cd_protocolo_gc_investimentos = ".intval($cd_protocolo_gc_investimentos).";";

        $this->db->query($qr_sql);
    }
	
    public function recusar($cd_protocolo_gc_investimentos, $args = array())
    {
        $qr_sql = "                 
            UPDATE projetos.protocolo_gc_investimentos
               SET ds_justificativa    = ".(trim($args['ds_justificativa']) != '' ? str_escape($args['ds_justificativa']) : "DEFAULT").",
                   cd_usuario_recusado = ".intval($args['cd_usuario']).",
			       cd_usuario_encerrar = ".intval($args['cd_usuario']).",
                   dt_recusado         = CURRENT_TIMESTAMP,
				   dt_encerrar         = CURRENT_TIMESTAMP
             WHERE cd_protocolo_gc_investimentos = ".intval($cd_protocolo_gc_investimentos).";";

        $this->db->query($qr_sql);
    }

    public function receber($cd_protocolo_gc_investimentos, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.protocolo_gc_investimentos
               SET cd_usuario_recebido = ".intval($cd_usuario).",
                   dt_recebido         = CURRENT_TIMESTAMP
             WHERE cd_protocolo_gc_investimentos = ".intval($cd_protocolo_gc_investimentos).";";

        $this->db->query($qr_sql);
    }

    public function encerrar($cd_protocolo_gc_investimentos, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.protocolo_gc_investimentos
                SET cd_usuario_encerrar = ".intval($cd_usuario).",
                    dt_encerrar         = CURRENT_TIMESTAMP
              WHERE cd_protocolo_gc_investimentos = ".intval($cd_protocolo_gc_investimentos).";";

        $result = $this->db->query($qr_sql);
    }
    
    public function listar_acompanhamento($cd_protocolo_gc_investimentos)
    {
        $qr_sql = "
            SELECT pgia.cd_protocolo_gc_investimentos_acompanhamento, 
                   pgia.acompanhamento, 
                   TO_CHAR(pgia.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
                   funcoes.get_usuario_nome(pgia.cd_usuario_inclusao) AS ds_usuario
              FROM projetos.protocolo_gc_investimentos_acompanhamento pgia
             WHERE pgia.dt_exclusao IS NULL
               AND pgia.cd_protocolo_gc_investimentos = ".intval($cd_protocolo_gc_investimentos).";";
        
        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_acompanhamento($cd_protocolo_gc_investimentos, $args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.protocolo_gc_investimentos_acompanhamento
                 (
                   cd_protocolo_gc_investimentos,
                   acompanhamento,
                   cd_usuario_inclusao
                 )
            VALUES
                 (
                   ".intval($cd_protocolo_gc_investimentos).",
                   ".(trim($args['acompanhamento']) != '' ? str_escape($args['acompanhamento']) : "DEFAULT").",   
                   ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }
}
?>