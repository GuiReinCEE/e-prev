<?php

class Atendimento_familiares_falecidos_retorno_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function listar($cd_atendimento_familiares_falecidos)
    {
    	$qr_sql = "
        	SELECT r.cd_atendimento_familiares_falecidos_retorno,
        		   r.ds_atendimento_familiares_falecidos_retorno,
        	       r.cd_atendimento_familiares_falecidos,
        	       TO_CHAR(r.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
        	       funcoes.get_usuario_nome(r.cd_usuario_inclusao) AS nome
        	  FROM projetos.atendimento_familiares_falecidos_retorno r
        	 WHERE r.cd_atendimento_familiares_falecidos = ".intval($cd_atendimento_familiares_falecidos).";";

       	#echo '<pre>'.$qr_sql; exit;

       	return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_atendimento_familiares_falecidos_retorno)
    {
    	$qr_sql = "
    		SELECT cd_atendimento_familiares_falecidos_retorno,
    		       cd_atendimento_familiares_falecidos,
    		       ds_atendimento_familiares_falecidos_retorno
    		  FROM projetos.atendimento_familiares_falecidos_retorno
    		 WHERE cd_atendimento_familiares_falecidos_retorno = ".intval($cd_atendimento_familiares_falecidos_retorno).";";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
    	$qr_sql = "
    		INSERT INTO projetos.atendimento_familiares_falecidos_retorno
    		     (
                   cd_atendimento_familiares_falecidos, 
                   ds_atendimento_familiares_falecidos_retorno, 
                   cd_usuario_inclusao, 
                   cd_usuario_alteracao
                 )
			VALUES
			     (
			       ".intval($args['cd_atendimento_familiares_falecidos']).",
			       ".str_escape($args['ds_atendimento_familiares_falecidos_retorno']).",
			       ".intval($args['cd_usuario']).",
			       ".intval($args['cd_usuario'])."
			     )";

    	$this->db->query($qr_sql);
    }

    public function atualizar($cd_atendimento_familiares_falecidos_retorno, $args = array())
    {
    	$qr_sql = "
    		UPDATE projetos.atendimento_familiares_falecidos_retorno
    		   SET ds_atendimento_familiares_falecidos_retorno = ".str_escape($args['ds_atendimento_familiares_falecidos_retorno']).",
                   cd_usuario_alteracao                        = ".intval($args['cd_usuario']).",
                   dt_alteracao                                = CURRENT_TIMESTAMP
    		 WHERE cd_atendimento_familiares_falecidos_retorno = ".intval($cd_atendimento_familiares_falecidos_retorno).";";

    	$this->db->query($qr_sql);
    }
}