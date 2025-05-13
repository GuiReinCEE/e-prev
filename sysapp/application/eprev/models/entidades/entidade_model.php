<?php
class entidade_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT e.cd_entidade,
			       e.ds_entidade,
				   e.cnpj,
				   e.dt_exclusao,
				   e.telefone1,
				   e.telefone2
			  FROM entidades.entidade e
			 WHERE 1 = 1 
			   ".(trim($args['ds_entidade']) != '' ? "AND UPPER(funcoes.remove_acento(e.ds_entidade)) LIKE (UPPER(funcoes.remove_acento('%".trim($args['ds_entidade'])."%'))) " : "" )."
			   ".(trim($args['cnpj']) != '' ? "AND e.cnpj = '".trim($args['cnpj'])."'" : "" )."
			   ".(trim($args['cd_recolhimento']) != '' ? "AND 0 < (SELECT COUNT(*)
			                                                         FROM entidades.entidade_recolhimento r
			                                                        WHERE r.dt_exclusao IS NULL
			                                                          AND r.cd_recolhimento = ".intval($args['cd_recolhimento'])."
			   														  AND r.cd_entidade     = e.cd_entidade)" : "" ).";";

		$result = $this->db->query($qr_sql);
	}	
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_entidade,
			       ds_entidade,
				   cnpj,
				   telefone1,
				   telefone2,
				   dt_exclusao
			  FROM entidades.entidade
			 WHERE cd_entidade = ".intval($args['cd_entidade']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_entidade']) == 0)
		{
			$cd_entidade = intval($this->db->get_new_id("entidades.entidade", "cd_entidade"));
			
			$qr_sql = "
				INSERT INTO entidades.entidade
				     (
					    cd_entidade,
						ds_entidade,
						cnpj,
						telefone1,
						telefone2,
						cd_usuario_inclusao,
						cd_usuario_alteracao
					 )
				VALUES
				     (
					    ".intval($cd_entidade).",
						".(trim($args['ds_entidade']) != '' ? "'".trim($args['ds_entidade'])."'" : "DEFAULT").",
						".(trim($args['cnpj']) != '' ? "'".trim($args['cnpj'])."'" : "DEFAULT").",
						".(trim($args['telefone1']) != '' ? "'".trim($args['telefone1'])."'" : "DEFAULT").",
						".(trim($args['telefone2']) != '' ? "'".trim($args['telefone2'])."'" : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$cd_entidade = intval($args['cd_entidade']);
			
			$qr_sql = "
				UPDATE entidades.entidade
				   SET ds_entidade          = ".(trim($args['ds_entidade']) != '' ? "'".trim($args['ds_entidade'])."'" : "DEFAULT").",
				       cnpj                 = ".(trim($args['cnpj']) != '' ? "'".trim($args['cnpj'])."'" : "DEFAULT").",
				       telefone1           = ".(trim($args['telefone1']) != '' ? "'".trim($args['telefone1'])."'" : "DEFAULT").",
				       telefone2           = ".(trim($args['telefone2']) != '' ? "'".trim($args['telefone2'])."'" : "DEFAULT").",
					   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_entidade = ".intval($args['cd_entidade']).";";
		}

		$this->db->query($qr_sql);
		
		return $cd_entidade;
	}
	
	function desativar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE entidades.entidade
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_entidade = ".intval($args['cd_entidade']).";";
			
		$this->db->query($qr_sql);
	}
	
	function ativar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE entidades.entidade
			   SET cd_usuario_exclusao = NULL,
				   dt_exclusao         = NULL
			 WHERE cd_entidade = ".intval($args['cd_entidade']).";";
			
		$this->db->query($qr_sql);
	}
	
	function monta_menu(&$result, $args=array())
	{
		$qr_sql = "
		    INSERT INTO entidades.menu_entidade
			    (
                   cd_menu, 
				   cd_entidade
			    )
			SELECT cd_menu, 
			       ".intval($args['cd_entidade'])."
              FROM entidades.menu
			 WHERE dt_exclusao IS NULL;";
			
		$this->db->query($qr_sql);
	}

	function listar_recolhimento_entidade(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_recolhimento,
			       ds_entidade_recolhimento,
			       cd_entidade_recolhimento
			  FROM entidades.entidade_recolhimento
			 WHERE dt_exclusao IS NULL
			   AND cd_entidade = ".intval($args["cd_entidade"]).";";

		$result = $this->db->query($qr_sql);
	}

	function salvar_recolhimento(&$result, $args=array())
	{
		if(intval($args['cd_entidade_recolhimento']) == 0)
		{
			$qr_sql = "
				INSERT INTO entidades.entidade_recolhimento
				     (
					    cd_entidade,
						ds_entidade_recolhimento,
						cd_recolhimento,
						cd_usuario_inclusao,
						cd_usuario_alteracao
					 )
				VALUES
				     (
					    ".intval($args["cd_entidade"]).",
						".(trim($args['ds_entidade_recolhimento']) != '' ? "'".trim($args['ds_entidade_recolhimento'])."'" : "DEFAULT").",
						".(trim($args['cd_recolhimento']) != '' ? intval($args['cd_recolhimento']) : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					 );";
		}
		else
		{			
			$qr_sql = "
				UPDATE entidades.entidade_recolhimento
				   SET ds_entidade_recolhimento = ".(trim($args['ds_entidade_recolhimento']) != '' ? "'".trim($args['ds_entidade_recolhimento'])."'" : "DEFAULT").",
				       cd_recolhimento          = ".(trim($args['cd_recolhimento']) != '' ? intval($args['cd_recolhimento']) : "DEFAULT").",
					   cd_usuario_alteracao     = ".intval($args['cd_usuario']).",
					   dt_alteracao             = CURRENT_TIMESTAMP
				 WHERE cd_entidade_recolhimento = ".intval($args['cd_entidade_recolhimento']).";";
		}

		$this->db->query($qr_sql);
	}

	function carrega_recolhimento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_recolhimento,
			       ds_entidade_recolhimento,
			       cd_entidade_recolhimento
			  FROM entidades.entidade_recolhimento
			 WHERE dt_exclusao IS NULL
			   AND cd_entidade_recolhimento = ".intval($args["cd_entidade_recolhimento"]).";";

		$result = $this->db->query($qr_sql);
	}

	function excluir_recolhimento(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE entidades.entidade_recolhimento
			   SET cd_usuario_exclusao     = ".intval($args['cd_usuario']).",
				   dt_exclusao             = CURRENT_TIMESTAMP
			 WHERE cd_entidade_recolhimento = ".intval($args['cd_entidade_recolhimento']).";";

		 $this->db->query($qr_sql);
	}
}
?>