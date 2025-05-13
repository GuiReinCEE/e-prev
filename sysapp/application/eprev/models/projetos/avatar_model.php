<?php
class avatar_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function usuario(&$result, $args=array())
    {       
        $qr_sql = "
                    SELECT uc.codigo, 
                           uc.usuario, 
                           uc.nome, 
                           uc.guerra,
						   uc.avatar
                      FROM projetos.usuarios_controledi uc
					 WHERE MD5(uc.codigo::TEXT)  = '".$args["cd_usuario"]."'
					    OR MD5(uc.usuario::TEXT) = '".$args["usuario"]."'
                  ";
        $result = $this->db->query($qr_sql);
    }	
	
    function carrega(&$result, $args=array())
    {       
        $qr_sql = "
                    SELECT a.cd_avatar, 
                           a.cd_usuario, 
                           a.arquivo, 
                           a.arquivo_nome, 
                           a.arquivo_avatar, 
                           a.crop_x, 
                           a.crop_y, 
                           a.crop_largura, 
                           a.crop_altura, 
                           a.dt_inclusao, 
                           a.cd_usuario_inclusao
                      FROM projetos.avatar a
                     WHERE a.dt_exclusao IS NULL
                       AND MD5(a.cd_usuario::TEXT) = '".$args["cd_usuario"]."'
                     ORDER BY a.dt_inclusao DESC
                     LIMIT 1
                  ";
        $result = $this->db->query($qr_sql);
		
		#echo "<PRE>$qr_sql</PRE>"; exit;
    }

    function salvar(&$result, $args=array())
    {
        $qr_sql = "
					INSERT INTO projetos.avatar
					     (
							cd_usuario, 
							arquivo, 
							arquivo_nome, 
							arquivo_avatar, 
							crop_x, 
							crop_y, 
							crop_largura, 
							crop_altura, 
							cd_usuario_inclusao
					     )
                    VALUES
                         (
                            ".(trim($args["cd_usuario"]) != "" ? intval($args["cd_usuario"]) : "DEFAULT").",
                            ".(trim($args["arquivo"]) != "" ? "'".trim($args["arquivo"])."'" : "DEFAULT").",
                            ".(trim($args["arquivo_nome"]) != "" ? "'".trim($args["arquivo_nome"])."'" : "DEFAULT").",
                            ".(trim($args["arquivo_avatar"]) != "" ? "'".trim($args["arquivo_avatar"])."'" : "DEFAULT").",
                            ".(trim($args["crop_x"]) != "" ? floatval($args["crop_x"]) : "DEFAULT").",							
                            ".(trim($args["crop_y"]) != "" ? floatval($args["crop_y"]) : "DEFAULT").",							
                            ".(trim($args["crop_largura"]) != "" ? floatval($args["crop_largura"]) : "DEFAULT").",							
                            ".(trim($args["crop_altura"]) != "" ? floatval($args["crop_altura"]) : "DEFAULT").",							
                            ".intval($args["cd_usuario_inclusao"])."
                         );
						 
					UPDATE projetos.usuarios_controledi
					   SET avatar = ".(trim($args["arquivo_avatar"]) != "" ? "'".trim($args["arquivo_avatar"])."'" : "NULL")."
					 WHERE codigo = ".intval($args["cd_usuario"]).";
                  ";
		
        #echo "<PRE>$qr_sql".print_r($args,true)."</PRE>"; exit;
		$result = $this->db->query($qr_sql);
    }
}