<?php

class reuniao_sg_permissao_model extends Model
{

    function __construct()
    {
        parent::Model();
    }
	
	function listar(&$result, $args=array())
    {
	
		$qr_sql = "
			SELECT rsp.cd_reuniao_sg_permissao,
                   uc.nome,
                   uc.divisao
              FROM projetos.reuniao_sg_permissao rsp
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = rsp.cd_usuario
             WHERE dt_exclusao IS NULL";
		
		$result = $this->db->query($qr_sql);
	}
	
	function cadastro(&$result, $args=array())
    {
		$qr_sql = "
			SELECT rsp.cd_reuniao_sg_permissao,
                   rsp.cd_usuario,
                   uc.divisao
              FROM projetos.reuniao_sg_permissao rsp
              JOIN projetos.usuarios_controledi uc
                ON uc.codigo = rsp.cd_usuario
             WHERE dt_exclusao IS NULL
			   AND rsp.cd_reuniao_sg_permissao = ". intval($args['cd_reuniao_sg_permissao']);
		
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{

		$qr_sql = "
			INSERT INTO projetos.reuniao_sg_permissao
		         (
					cd_usuario,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario_inclusao'])."
				 )";
		
		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.reuniao_sg_permissao
			   SET cd_usuario_exclusao = ". intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_reuniao_sg_permissao = ".intval($args['cd_reuniao_sg_permissao']);
			 
		$result = $this->db->query($qr_sql);
	}
	
}

?>