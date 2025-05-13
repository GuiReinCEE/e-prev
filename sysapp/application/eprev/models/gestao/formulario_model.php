<?php
class formulario_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function listar(&$result, $args=array())
	{		
		$qr_sql = "
			SELECT cd_formulario,
				   nr_formulario,
				   ds_formulario,
				   arquivo,
				   arquivo_nome,
				   UPPER(funcoes.remove_acento(ds_formulario)) AS ds_formulario_zip
			  FROM gestao.formulario
			 WHERE dt_exclusao IS NULL
			 ".(trim($args['nr_formulario']) != '' ? "AND nr_formulario = ".intval($args['nr_formulario']) : "")."
			 ".(trim($args['ds_formulario']) != '' ? "AND UPPER(funcoes.remove_acento(ds_formulario)) LIKE (UPPER(funcoes.remove_acento('%".trim($args['ds_formulario'])."%')))" : "").";";
		
		$result = $this->db->query($qr_sql);
	}

	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_formulario,
				   nr_formulario,
				   ds_formulario,
				   arquivo,
				   arquivo_nome,
				   fl_tipo
			  FROM gestao.formulario
			 WHERE cd_formulario = ".intval($args['cd_formulario']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_formulario']) == 0)
		{
			$qr_sql = "
				INSERT INTO gestao.formulario
				     (
					    nr_formulario,
					    ds_formulario,
					    arquivo,
					    arquivo_nome,
					    fl_tipo,
						cd_usuario_inclusao,
						cd_usuario_alteracao
					 )
				VALUES
				     (
					    ".(trim($args['nr_formulario']) != '' ? intval($args['nr_formulario']) : "DEFAULT").",
						".(trim($args['ds_formulario']) != '' ? str_escape($args['ds_formulario']) : "DEFAULT").",
						".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
						".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
						".(trim($args['fl_tipo']) != '' ? str_escape($args['fl_tipo']) : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					 );";
		}
		else
		{		
			$qr_sql = "
				UPDATE gestao.formulario
				   SET nr_formulario        = ".(trim($args['nr_formulario']) != '' ? intval($args['nr_formulario']) : "DEFAULT").",
				       ds_formulario        = ".(trim($args['ds_formulario']) != '' ? str_escape($args['ds_formulario']) : "DEFAULT").",
				       arquivo              = ".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "arquivo").",
				       arquivo_nome         = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "arquivo_nome").",
				       fl_tipo              = ".(trim($args['fl_tipo']) != '' ? str_escape($args['fl_tipo']) : "DEFAULT").",
				       cd_usuario_alteracao = ".intval($args['cd_usuario']).",
					   dt_alteracao         = CURRENT_TIMESTAMP
				 WHERE cd_formulario = ".intval($args['cd_formulario']).";";
		}
		
		$this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.formulario
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
		     WHERE cd_formulario = ".intval($args['cd_formulario']).";";
			 
		$this->db->query($qr_sql);
	}
}
?>