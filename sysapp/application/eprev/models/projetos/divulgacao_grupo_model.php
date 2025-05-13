<?php
class Divulgacao_grupo_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT dg.cd_divulgacao_grupo, 
					       dg.ds_divulgacao_grupo, 
						   dg.qr_sql, 
						   dg.cd_lista, 
						   COALESCE(dgt.qt_registro,0) AS qt_registro,
                           TO_CHAR(dg.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   dg.cd_usuario_inclusao,
						   funcoes.get_usuario_nome(dg.cd_usuario_inclusao) AS usuario_inclusao,
						   TO_CHAR(dg.dt_alteracao,'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
                           dg.cd_usuario_alteracao,	
                           funcoes.get_usuario_nome(dg.cd_usuario_alteracao) AS usuario_alteracao,	
                           TO_CHAR(dg.dt_exclusao,'DD/MM/YYYY  HH24:MI:SS') AS dt_exclusao,					   
						   dg.cd_usuario_exclusao,
						   funcoes.get_usuario_nome(dg.cd_usuario_exclusao) AS usuario_exclusao
                      FROM projetos.divulgacao_grupo dg
					  LEFT JOIN projetos.divulgacao_grupo_total dgt
					    ON dgt.cd_divulgacao_grupo = dg.cd_divulgacao_grupo
			         WHERE 1 = 1
                     ".(trim($args["ds_grupo"]) != "" ? "AND funcoes.remove_acento(UPPER(dg.ds_divulgacao_grupo)) LIKE funcoes.remove_acento(UPPER('%".trim($args["ds_grupo"])."%'))" : "")."
                     ".(trim($args['fl_excluido']) == 'S' ? "AND dg.dt_exclusao IS NOT NULL" : "")."
                     ".(trim($args['fl_excluido']) == 'N' ? "AND dg.dt_exclusao IS NULL" : "")."
                  ";
	
        $result = $this->db->query($qr_sql);
    }
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
					SELECT dg.cd_divulgacao_grupo, 
					       dg.ds_divulgacao_grupo, 
						   dg.qr_sql, 
						   dg.cd_lista, 
						   COALESCE(dgt.qt_registro,0) AS qt_registro,
                           TO_CHAR(dg.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
						   dg.cd_usuario_inclusao,
						   funcoes.get_usuario_nome(dg.cd_usuario_inclusao) AS usuario_inclusao,
						   TO_CHAR(dg.dt_alteracao,'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
                           dg.cd_usuario_alteracao,	
						   funcoes.get_usuario_nome(dg.cd_usuario_alteracao) AS usuario_alteracao,						   
						   dg.dt_exclusao, 
						   dg.cd_usuario_exclusao,
						   funcoes.get_usuario_nome(dg.cd_usuario_exclusao) AS usuario_exclusao
                      FROM projetos.divulgacao_grupo dg
					  LEFT JOIN projetos.divulgacao_grupo_total dgt
					    ON dgt.cd_divulgacao_grupo = dg.cd_divulgacao_grupo					  
			         WHERE dg.cd_divulgacao_grupo = ".intval($args['cd_divulgacao_grupo'])."
				  ";
			 
		$result = $this->db->query($qr_sql);
	}	
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_divulgacao_grupo']) == 0)
		{
			$cd_divulgacao_grupo = intval($this->db->get_new_id("projetos.divulgacao_grupo", "cd_divulgacao_grupo"));
			
			$qr_sql = "
						INSERT INTO projetos.divulgacao_grupo
						     (
								cd_divulgacao_grupo, 
								ds_divulgacao_grupo, 
								qr_sql, 
								cd_lista, 
							    cd_usuario_alteracao,
							    cd_usuario_inclusao
							  )
                         VALUES 
						      (
								".intval($cd_divulgacao_grupo).",
								".(trim($args['ds_divulgacao_grupo']) != '' ? "'".$args['ds_divulgacao_grupo']."'" : "DEFAULT").",
								".(trim($args['qr_sql']) != '' ? str_escape($args['qr_sql']) : "DEFAULT").",
								".(trim($args['cd_lista']) != '' ? "'".$args['cd_lista']."'" : "DEFAULT").",
								".intval($args['cd_usuario']).",							  
								".intval($args['cd_usuario'])."							  
							  );
							  
						SELECT rotinas.divulgacao_grupo_qt_registro(".intval($cd_divulgacao_grupo).");
			          ";
		}
		else
		{
			$cd_divulgacao_grupo = intval($args['cd_divulgacao_grupo']);
			
			$qr_sql = "
						UPDATE projetos.divulgacao_grupo
						   SET ds_divulgacao_grupo  = ".(trim($args['ds_divulgacao_grupo']) != '' ? "'".$args['ds_divulgacao_grupo']."'" : "DEFAULT").", 
						       qr_sql               = ".(trim($args['qr_sql']) != '' ? str_escape($args['qr_sql']) : "DEFAULT").", 
							   cd_lista             = ".(trim($args['cd_lista']) != '' ? "'".$args['cd_lista']."'" : "DEFAULT").",
							   dt_alteracao         = CURRENT_TIMESTAMP,
							   cd_usuario_alteracao = ".intval($args['cd_usuario'])."
						 WHERE cd_divulgacao_grupo = ".intval($args['cd_divulgacao_grupo']).";
						 
						SELECT rotinas.divulgacao_grupo_qt_registro(".intval($args['cd_divulgacao_grupo']).");
			          ";
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_divulgacao_grupo;
	}
	
    function total_registro(&$result, $args=array())
    {
        $qr_sql = "SELECT rotinas.divulgacao_grupo_qt_registro(".intval($args['cd_divulgacao_grupo']).");";
        $result = $this->db->query($qr_sql);
    }	

    public function excluir($cd_usuario, $cd_divulgacao_grupo)
    {
        $qr_sql = "
            UPDATE projetos.divulgacao_grupo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_divulgacao_grupo = ".intval($cd_divulgacao_grupo).";";

        $this->db->query($qr_sql);
    }
}
?>