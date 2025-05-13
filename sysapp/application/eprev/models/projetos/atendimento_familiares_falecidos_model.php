<?php

class Atendimento_familiares_falecidos_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function listar($args = array())
    {
    	$qr_sql = "
        	SELECT aff.cd_atendimento_familiares_falecidos,
        	       TO_CHAR(aff.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
        	       aff.cd_empresa,
        	       aff.cd_registro_empregado,
        	       aff.seq_dependencia,
        	       projetos.participante_nome(aff.cd_empresa, aff.cd_registro_empregado, aff.seq_dependencia) AS nome,
        	       aff.contato,
        	       aff.observacao,
        	       funcoes.get_usuario_nome(aff.cd_usuario_inclusao) AS usuario_inclusao,
        	       aff.cd_atendimento,
        	       TO_CHAR(aff.dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
        	       (SELECT TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') || ' : ' || a.ds_atendimento_familiares_falecidos_acompanhamento
        	       	  FROM projetos.atendimento_familiares_falecidos_acompanhamento a
        	       	 WHERE a.dt_exclusao IS NULL
        	       	   AND a.cd_atendimento_familiares_falecidos = aff.cd_atendimento_familiares_falecidos
        	       	 ORDER BY a.dt_inclusao DESC
        	       	 LIMIT 1) AS acompanhamento
        	  FROM projetos.atendimento_familiares_falecidos aff
        	 WHERE aff.dt_exclusao IS NULL
        	  ".(trim($args['cd_empresa']) != '' ? "AND aff.cd_empresa = ".intval($args['cd_empresa']) : "")." 
        	  ".(trim($args['cd_registro_empregado']) != '' ? "AND aff.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")." 
        	  ".(trim($args['seq_dependencia']) != '' ? "AND aff.seq_dependencia = ".intval($args['seq_dependencia']) : "")." 
        	  ".(trim($args['fl_encerrada']) == 'S' ? "AND aff.dt_encerramento IS NOT NULL" : "")." 
			  ".(trim($args['fl_encerrada']) == 'N' ? "AND aff.dt_encerramento IS NULL" : "")." 
        	  ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? "AND DATE_TRUNC('day', aff.dt_inclusao) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
        	  ".(((trim($args['dt_encerramento_ini']) != "") AND (trim($args['dt_encerramento_fim']) != "")) ? "AND DATE_TRUNC('day', aff.dt_encerramento) BETWEEN TO_DATE('".$args['dt_encerramento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encerramento_fim']."', 'DD/MM/YYYY')" : "").";";

       	#echo '<pre>'.$qr_sql; exit;

       	return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_atendimento_familiares_falecidos)
    {
    	$qr_sql = "
    		SELECT cd_atendimento_familiares_falecidos,
    		       cd_empresa,
    		       cd_registro_empregado,
    		       seq_dependencia,
    		       cd_atendimento,
    		       contato,
    		       observacao,
    		       TO_CHAR(dt_encerramento, 'DD/MM/YYYY HH24:MI:SS') AS dt_encerramento,
    		       projetos.participante_nome(cd_empresa, cd_registro_empregado, seq_dependencia) AS nome
    		  FROM projetos.atendimento_familiares_falecidos 
    		 WHERE cd_atendimento_familiares_falecidos = ".intval($cd_atendimento_familiares_falecidos).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
    	$qr_sql = "
    		INSERT INTO projetos.atendimento_familiares_falecidos
    		     (
                   cd_empresa, 
                   cd_registro_empregado, 
                   seq_dependencia, 
                   cd_atendimento, 
                   contato, 
                   observacao,
                   cd_usuario_inclusao, 
                   cd_usuario_alteracao
                 )
			VALUES
			     (
			       ".intval($args['cd_empresa']).",
			       ".intval($args['cd_registro_empregado']).",
			       ".intval($args['seq_dependencia']).",
			       ".(intval($args['cd_atendimento']) > 0 ? intval($args['cd_atendimento']) : "DEFAULT").",
			       ".(trim($args['contato']) != '' ? str_escape($args['contato']) : "DEFAULT").",
			       ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
			       ".intval($args['cd_usuario']).",
			       ".intval($args['cd_usuario'])."
			     )";

    	$this->db->query($qr_sql);
    }

    public function atualizar($cd_atendimento_familiares_falecidos, $args = array())
    {
    	$qr_sql = "
    		UPDATE projetos.atendimento_familiares_falecidos
    		   SET cd_empresa            = ".intval($args['cd_empresa']).",
                   cd_registro_empregado = ".intval($args['cd_registro_empregado']).", 
                   seq_dependencia       = ".intval($args['seq_dependencia']).",
                   cd_atendimento        = ".(intval($args['cd_atendimento']) > 0 ? intval($args['cd_atendimento']) : "DEFAULT").",
                   contato               = ".(trim($args['contato']) != '' ? str_escape($args['contato']) : "DEFAULT").",
                   observacao            = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
                   cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
                   dt_alteracao          = CURRENT_TIMESTAMP
    		 WHERE cd_atendimento_familiares_falecidos = ".intval($cd_atendimento_familiares_falecidos).";";

    	$this->db->query($qr_sql);
    }

    public function excluir($cd_atendimento_familiares_falecidos, $args  = array())
    {
    	$qr_sql = "
    		UPDATE projetos.atendimento_familiares_falecidos
    		   SET cd_usuario_exclusao  = ".intval($args['cd_usuario']).",
                   dt_exclusao          = CURRENT_TIMESTAMP
    		 WHERE cd_atendimento_familiares_falecidos = ".intval($cd_atendimento_familiares_falecidos).";";

    	$this->db->query($qr_sql);
    }

    public function encerrar($cd_atendimento_familiares_falecidos, $args  = array())
    {
    	$qr_sql = "
    		UPDATE projetos.atendimento_familiares_falecidos
    		   SET cd_usuario_encerramento  = ".intval($args['cd_usuario']).",
                   dt_encerramento          = CURRENT_TIMESTAMP
    		 WHERE cd_atendimento_familiares_falecidos = ".intval($cd_atendimento_familiares_falecidos).";";

    	$this->db->query($qr_sql);
    }
}