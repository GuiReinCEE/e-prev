<?php

class Atendimento_familiares_falecidos_acompanhamento_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function listar($cd_atendimento_familiares_falecidos)
    {
    	$qr_sql = "
        	SELECT a.cd_atendimento_familiares_falecidos_acompanhamento,
        		   a.ds_atendimento_familiares_falecidos_acompanhamento,
        	       a.cd_atendimento_familiares_falecidos,
        	       TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(a.cd_usuario_inclusao) AS nome
        	  FROM projetos.atendimento_familiares_falecidos_acompanhamento a
        	 WHERE a.cd_atendimento_familiares_falecidos = ".intval($cd_atendimento_familiares_falecidos).";";

       	#echo '<pre>'.$qr_sql; exit;

       	return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_atendimento_familiares_falecidos_acompanhamento)
    {
    	$qr_sql = "
    		SELECT cd_atendimento_familiares_falecidos_acompanhamento,
    		       cd_atendimento_familiares_falecidos,
    		       ds_atendimento_familiares_falecidos_acompanhamento
    		  FROM projetos.atendimento_familiares_falecidos_acompanhamento
    		 WHERE cd_atendimento_familiares_falecidos_acompanhamento = ".intval($cd_atendimento_familiares_falecidos_acompanhamento).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
    	$qr_sql = "
    		INSERT INTO projetos.atendimento_familiares_falecidos_acompanhamento
    		     (
                   cd_atendimento_familiares_falecidos, 
                   ds_atendimento_familiares_falecidos_acompanhamento, 
                   cd_usuario_inclusao, 
                   cd_usuario_alteracao
                 )
			VALUES
			     (
			       ".intval($args['cd_atendimento_familiares_falecidos']).",
			       ".str_escape($args['ds_atendimento_familiares_falecidos_acompanhamento']).",
			       ".intval($args['cd_usuario']).",
			       ".intval($args['cd_usuario'])."
			     )";

    	$this->db->query($qr_sql);
    }

    public function atualizar($cd_atendimento_familiares_falecidos_acompanhamento, $args = array())
    {
    	$qr_sql = "
    		UPDATE projetos.atendimento_familiares_falecidos_acompanhamento
    		   SET ds_atendimento_familiares_falecidos_acompanhamento = ".str_escape($args['ds_atendimento_familiares_falecidos_acompanhamento']).",
                   cd_usuario_alteracao                               = ".intval($args['cd_usuario']).",
                   dt_alteracao                                       = CURRENT_TIMESTAMP
    		 WHERE cd_atendimento_familiares_falecidos_acompanhamento = ".intval($cd_atendimento_familiares_falecidos_acompanhamento).";";

    	$this->db->query($qr_sql);
    }
}