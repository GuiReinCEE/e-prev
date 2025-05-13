<?php
class Usuario_horario_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT uh.cd_usuario_horario,
					       uc.codigo,
						   uc.nome,
						   uc.divisao,
						   uc.usuario,
						   uh.cd_usuario,
						   TO_CHAR(uh.dt_liberar,'DD/MM/YYYY') AS dt_liberar,						   
						   TO_CHAR(uh.hr_ini,'HH24:MI') AS hr_ini,						   
						   TO_CHAR(uh.hr_fim,'HH24:MI') AS hr_fim,
						   (CASE WHEN uh.dt_liberar >= CURRENT_DATE THEN 'S' ELSE 'N' END) AS fl_editar
					  FROM projetos.usuario_horario uh
					  JOIN projetos.usuarios_controledi uc
						ON uc.codigo = uh.cd_usuario
					 WHERE uh.dt_exclusao IS NULL
					 ".(trim($args['cd_usuario_gerencia']) != "" ? "AND uc.divisao = '".trim($args['cd_usuario_gerencia'])."'" : "")."
					 ".(intval($args['cd_usuario']) > 0 ? "AND uh.cd_usuario = ".intval($args['cd_usuario']) : "")."
					ORDER BY uc.nome
			      ";

        $result = $this->db->query($qr_sql);
    }
	
    function carrega($cd_usuario_horario)
    {
        $qr_sql = "
					SELECT uh.cd_usuario_horario,
					       uh.cd_usuario,
						   uc.nome,
						   uc.divisao,
						   uc.usuario,						   
					       TO_CHAR(uh.dt_liberar,'DD/MM/YYYY') AS dt_liberar,						   
						   TO_CHAR(uh.hr_ini,'HH24:MI') AS hr_ini,						   
						   TO_CHAR(uh.hr_fim,'HH24:MI') AS hr_fim,
                           uh.ds_obs
					  FROM projetos.usuario_horario uh	
					  JOIN projetos.usuarios_controledi uc
						ON uc.codigo = uh.cd_usuario					  
					 WHERE uh.cd_usuario_horario = ". intval($cd_usuario_horario)."
			      ";
        return $this->db->query($qr_sql)->row_array();
    }	
	
    function salvar($args=array())
    {
        if(intval($args['cd_usuario_horario']) > 0)
        {
            $qr_sql = "
						UPDATE projetos.usuario_horario
						   SET cd_usuario           = ".intval($args['cd_usuario']).",
							   dt_liberar           = ".(trim($args['dt_liberar']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_liberar']."','DD/MM/YYYY')").",
							   hr_ini               = ".(trim($args['hr_ini']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['hr_ini']."','HH24:MI')::TIME").",
							   hr_fim               = ".(trim($args['hr_fim']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['hr_fim']."','HH24:MI')::TIME").",
							   ds_obs               = ".(trim($args['ds_obs']) == "" ? "DEFAULT" : "'".$args['ds_obs']."'").",
							   dt_alteracao         = CURRENT_TIMESTAMP,
							   cd_usuario_alteracao = ".intval($args['cd_usuario_logado'])."
						 WHERE cd_usuario_horario = ".intval($args['cd_usuario_horario'])."
				      ";
            
			#echo $qr_sql; exit;
            $this->db->query($qr_sql);
                
        }
        else
        {
            $qr_sql = "
						INSERT INTO projetos.usuario_horario
							 (
							   cd_usuario,
							   dt_liberar,
							   hr_ini,
							   hr_fim,
							   ds_obs,
							   cd_usuario_inclusao,
							   cd_usuario_alteracao
							 )
						VALUES
							 (
							   ".intval($args['cd_usuario']).",
							   ".(trim($args['dt_liberar']) == "" ? "DEFAULT" : "TO_DATE('".$args['dt_liberar']."','DD/MM/YYYY')").",
							   ".(trim($args['hr_ini']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['hr_ini']."','HH24:MI')::TIME").",
							   ".(trim($args['hr_fim']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['hr_fim']."','HH24:MI')::TIME").",
							   ".(trim($args['ds_obs']) == "" ? "DEFAULT" : "'".$args['ds_obs']."'").",
							   ".(trim($args['cd_usuario_logado']) == "" ? "DEFAULT" : intval($args['cd_usuario_logado'])).",
							   ".(trim($args['cd_usuario_logado']) == "" ? "DEFAULT" : intval($args['cd_usuario_logado']))." 
							 )
					  ";

			#echo $qr_sql; exit;
            $this->db->query($qr_sql);
        }
        
        return intval($args['cd_usuario_horario']);
    }

    function excluir($args)
    {
		$qr_sql = "
					UPDATE projetos.usuario_horario
					   SET dt_exclusao         = CURRENT_TIMESTAMP,
						   cd_usuario_exclusao = ".intval($args['cd_usuario_logado'])."
					 WHERE cd_usuario_horario = ".intval($args['cd_usuario_horario'])."
				  ";
        $this->db->query($qr_sql);
    }	
}
?>