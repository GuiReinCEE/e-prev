<?php
class Acompanhamento_registro_operacional_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT ap.cd_acompanhamento_registro_operacional,
		           ap.cd_acomp,
		           p.nome AS ds_projeto,
		           ap.cd_acompanhamento_registro_operacional AS cd_operacional,
		           ap.ds_nome AS ds_registro,
		           TO_CHAR(ap.dt_finalizado,'DD/MM/YYYY') AS dt_finalizado,
		           ap.cd_usuario
		      FROM projetos.acompanhamento_registro_operacional ap
			  JOIN projetos.usuarios_controledi uc
			    ON ap.cd_usuario = uc.codigo
		      JOIN projetos.acompanhamento_projetos app
			    ON ap.cd_acomp = app.cd_acomp
		      JOIN projetos.projetos p
			    ON app.cd_projeto = p.codigo
		     WHERE ap.dt_exclusao IS NULL
		       AND ap.cd_usuario = ".intval($args["cd_usuario"]).";";

		$result = $this->db->query($qr_sql);
	}
	
	function projetos( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT ap.cd_acomp AS value,
				   p.nome AS text
			  FROM projetos.acompanhamento_projetos ap
			  JOIN projetos.projetos p 
				ON ap.cd_projeto      = p.codigo
			   AND (ap.dt_encerramento IS NULL OR ap.cd_acomp = ".intval($args['cd_acomp']).")
			 WHERE (0 < (SELECT COUNT(*)
						  FROM projetos.projetos_envolvidos pe
						 WHERE pe.cd_projeto   = p.codigo
						   AND pe.cd_envolvido = ".intval($args['cd_usuario'])."))
			   OR (0 < (SELECT COUNT(*)
			  		      FROM projetos.analista_projeto ap
						 WHERE ap.cd_projeto   = p.codigo
						   AND ap.cd_analista = ".intval($args['cd_usuario'])."))
			 ORDER BY p.nome";
		#echo '<pre>'.$qr_sql;
			
		$result = $this->db->query($qr_sql);
	}
	
	function carrega( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT aro.cd_acompanhamento_registro_operacional,
				   aro.cd_acomp,
				   aro.ds_nome,
				   aro.ds_processo_faz,
                   aro.ds_processo_executado,
				   aro.ds_calculo,
				   aro.ds_responsaveis,
				   aro.ds_requesito,
				   aro.ds_necessario,
				   aro.ds_integridade,
				   aro.ds_resultado,
				   aro.ds_local,
				   aro.dt_finalizado,
				   uc.nome,
				   p.nome AS projeto,
				   aro.ds_processo_faz_complemento,
				   aro.ds_processo_executado_complemento,
				   aro.ds_calculo_complemento,
				   aro.ds_requesito_complemento,
				   aro.ds_necessario_complemento,
				   aro.ds_integridade_complemento,
				   aro.ds_resultado_complemento
			  FROM projetos.acompanhamento_registro_operacional aro
			  JOIN projetos.acompanhamento_projetos ap
			    ON ap.cd_acomp = aro.cd_acomp
			  JOIN projetos.projetos p
			    ON ap.cd_projeto = p.codigo	 
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = aro.cd_usuario
			 WHERE aro.cd_acompanhamento_registro_operacional = ".intval($args['cd_acompanhamento_registro_operacional']).";";
 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar( &$result, $args=array() )
	{
		if(intval($args['cd_acompanhamento_registro_operacional']) == 0)
		{
			$cd_acompanhamento_registro_operacional = intval($this->db->get_new_id("projetos.acompanhamento_registro_operacional", "cd_acompanhamento_registro_operacional"));
		
			$qr_sql = "
				INSERT INTO projetos.acompanhamento_registro_operacional
					 (
					   cd_acompanhamento_registro_operacional,
					   cd_acomp,
					   ds_nome,
					   ds_processo_faz,
                       ds_processo_executado,
					   ds_calculo,
					   ds_responsaveis,
					   ds_requesito,
					   ds_necessario,
					   ds_integridade,
					   ds_resultado,
					   ds_local,
					   cd_usuario
					 )
				VALUES
				     (
					   ".$cd_acompanhamento_registro_operacional.",
					   ".($args['cd_acomp'] != '' ? intval($args['cd_acomp']) : "DEFAULT").",
					   ".($args['ds_nome'] != '' ? "'".trim($args['ds_nome'])."'" : "DEFAULT").",
					   ".($args['ds_processo_faz'] != '' ? "'".trim($args['ds_processo_faz'])."'" : "DEFAULT").",
					   ".($args['ds_processo_executado'] != '' ? "'".trim($args['ds_processo_executado'])."'" : "DEFAULT").",
					   ".($args['ds_calculo'] != '' ? "'".trim($args['ds_calculo'])."'" : "DEFAULT").",
					   ".($args['ds_responsaveis'] != '' ? "'".trim($args['ds_responsaveis'])."'" : "DEFAULT").",
					   ".($args['ds_requesito'] != '' ? "'".trim($args['ds_requesito'])."'" : "DEFAULT").",
					   ".($args['ds_necessario'] != '' ? "'".trim($args['ds_necessario'])."'" : "DEFAULT").",
					   ".($args['ds_integridade'] != '' ? "'".trim($args['ds_integridade'])."'" : "DEFAULT").",
					   ".($args['ds_resultado'] != '' ? "'".trim($args['ds_resultado'])."'" : "DEFAULT").",
					   ".($args['ds_local'] != '' ? "'".trim($args['ds_local'])."'" : "DEFAULT").",
					   ".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.acompanhamento_registro_operacional
				   SET cd_acomp              = ".($args['cd_acomp'] != '' ? intval($args['cd_acomp']) : "DEFAULT").",
					   ds_nome               = ".($args['ds_nome'] != '' ? "'".trim($args['ds_nome'])."'" : "DEFAULT").",
					   ds_processo_faz       = ".($args['ds_processo_faz'] != '' ? "'".trim($args['ds_processo_faz'])."'" : "DEFAULT").",
                       ds_processo_executado = ".($args['ds_processo_executado'] != '' ? "'".trim($args['ds_processo_executado'])."'" : "DEFAULT").",
					   ds_calculo            = ".($args['ds_calculo'] != '' ? "'".trim($args['ds_calculo'])."'" : "DEFAULT").",
					   ds_responsaveis       = ".($args['ds_responsaveis'] != '' ? "'".trim($args['ds_responsaveis'])."'" : "DEFAULT").",
					   ds_requesito          = ".($args['ds_requesito'] != '' ? "'".trim($args['ds_requesito'])."'" : "DEFAULT").",
					   ds_necessario         = ".($args['ds_necessario'] != '' ? "'".trim($args['ds_necessario'])."'" : "DEFAULT").",
					   ds_integridade        = ".($args['ds_integridade'] != '' ? "'".trim($args['ds_integridade'])."'" : "DEFAULT").",
					   ds_resultado          = ".($args['ds_resultado'] != '' ? "'".trim($args['ds_resultado'])."'" : "DEFAULT").",
					   ds_local              = ".($args['ds_local'] != '' ? "'".trim($args['ds_local'])."'" : "DEFAULT")."
				 WHERE cd_acompanhamento_registro_operacional = ".intval($args['cd_acompanhamento_registro_operacional']).";";
				 
			$cd_acompanhamento_registro_operacional = intval($args['cd_acompanhamento_registro_operacional']);
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_acompanhamento_registro_operacional;
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.acompanhamento_registro_operacional
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_acompanhamento_registro_operacional = ".intval($args['cd_acompanhamento_registro_operacional']).";" ;
			 
		$result = $this->db->query($qr_sql);
	}
	
	function finalizar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.acompanhamento_registro_operacional
			   SET dt_finalizado         = CURRENT_TIMESTAMP,
			       cd_usuario_finalizado = ".intval($args['cd_usuario'])."
			 WHERE cd_acompanhamento_registro_operacional = ".intval($args['cd_acompanhamento_registro_operacional']).";" ;
	
		$result = $this->db->query($qr_sql);
	}
	
	function anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT uc.nome,
			       a.arquivo_nome,
				   TO_CHAR(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM projetos.acompanhamento_registro_operacional_anexo a
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = a.cd_usuario_inclusao
			 WHERE a.dt_exclusao IS NULL 
			   AND cd_acompanhamento_registro_operacional = ".intval($args['cd_acompanhamento_registro_operacional']).";";
			   
		$result = $this->db->query($qr_sql);
	}
}
?>