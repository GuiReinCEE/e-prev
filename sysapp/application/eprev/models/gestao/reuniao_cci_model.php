<?php
class reuniao_cci_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function tipo(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_reuniao_cci_tipo AS value, 
                   ds_reuniao_cci_tipo AS text
              FROM gestao.reuniao_cci_tipo
             WHERE dt_exclusao IS NULL;";
        
        $result = $this->db->query($qr_sql);
    }

	function listar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT rcci.cd_reuniao_cci, 
			       gestao.nr_reuniao_cci(rcci.nr_ano, rcci.nr_numero) AS ano_numero,
                   TO_CHAR(rcci.dt_reuniao_cci, 'DD/MM/YYYY') AS dt_reuniao_cci, 
                   TO_CHAR(rcci.dt_reuniao_cci, 'HH24:MI') AS hr_reuniao_cci,
                   t.ds_reuniao_cci_tipo,
                   l.ds_reuniao_cci_local,
                   uc1.nome AS usuario_coordenador_cci,
                   TO_CHAR(rcci.dt_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_enviado, 
                   uc2.nome AS usuario_enviado,
                   TO_CHAR(rcci.dt_aprovado, 'DD/MM/YYYY HH24:MI:SS') AS dt_aprovado,
                   TO_CHAR(rcci.dt_desaprovado, 'DD/MM/YYYY HH24:MI:SS') AS dt_desaprovado,
                   CASE WHEN rcci.dt_aprovado IS NOT NULL THEN 'Aprovado'
                        WHEN rcci.dt_aprovado IS NULL AND rcci.dt_desaprovado IS NOT NULL THEN 'Desaprovado'
                        ELSE 'Aguardando Aprovaчуo'
                   END AS status,
                   CASE WHEN rcci.dt_aprovado IS NOT NULL THEN 'label label-success'
                        WHEN rcci.dt_aprovado IS NULL AND rcci.dt_desaprovado IS NOT NULL THEN 'label label-important'
                        ELSE 'label label-warning'
                   END AS class_status
			  FROM gestao.reuniao_cci rcci
              LEFT JOIN gestao.reuniao_cci_tipo t
                ON t.cd_reuniao_cci_tipo = rcci.cd_reuniao_cci_tipo
              LEFT JOIN gestao.reuniao_cci_local l
                ON l.cd_reuniao_cci_local = rcci.cd_reuniao_cci_local
              LEFT JOIN projetos.usuarios_controledi uc1
                ON uc1.codigo = rcci.cd_usuario_coordenador_cci
              LEFT JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = rcci.cd_usuario_enviado
		     WHERE rcci.dt_exclusao IS NULL
               ".(trim($args['nr_ano']) != '' ? "AND rcci.nr_ano = ".intval($args['nr_ano']) : "")."
			   ".(trim($args['nr_numero']) != '' ? "AND rcci.nr_numero = ".intval($args['nr_numero']) : "")."
               ".(trim($args['cd_reuniao_cci_tipo']) != '' ? "AND rcci.cd_reuniao_cci_tipo = ".intval($args['cd_reuniao_cci_tipo']) : "")."    
               ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? " AND DATE_TRUNC('day', rcci.dt_reuniao_cci) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['fl_status']) == 'A' ? "AND rcci.dt_aprovado IS NOT NULL" : "")."
			   ".(trim($args['fl_status']) == 'D' ? "AND rcci.dt_desaprovado IS NOT NULL" : "").";";

        $result = $this->db->query($qr_sql);
    }
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT rcci.cd_reuniao_cci, 
			       gestao.nr_reuniao_cci(rcci.nr_ano, rcci.nr_numero) AS ano_numero,
                   rcci.nr_ano,
                   rcci.nr_numero,
                   TO_CHAR(rcci.dt_reuniao_cci, 'DD/MM/YYYY') AS dt_reuniao_cci, 
                   TO_CHAR(rcci.dt_reuniao_cci, 'HH24:MI') AS hr_reuniao_cci,
                   rcci.cd_reuniao_cci_tipo,
                   rcci.cd_reuniao_cci_local,
                   uc1.divisao AS cd_gerencia_coordenador_cci,
                   uc1.nome AS usuario_coordenador_cci,
                   rcci.cd_usuario_coordenador_cci,
                   TO_CHAR(rcci.dt_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_enviado, 
                   TO_CHAR(rcci.dt_aprovado, 'DD/MM/YYYY HH24:MI:SS') AS dt_aprovado,
                   TO_CHAR(rcci.dt_desaprovado, 'DD/MM/YYYY HH24:MI:SS') AS dt_desaprovado,
                   (SELECT COUNT(mei.*)
                      FROM gestao.reuniao_cci_membro_efetivo_item mei
                     WHERE mei.dt_exclusao IS NULL
                       AND rcci.cd_reuniao_cci = mei.cd_reuniao_cci) AS qt_membro_efetivo,
                   (SELECT COUNT(ci.*)
                      FROM gestao.reuniao_cci_convidado_item ci
                     WHERE ci.dt_exclusao IS NULL
                       AND rcci.cd_reuniao_cci = ci.cd_reuniao_cci) AS qt_convidado,
                   (SELECT COUNT(p.*)
                      FROM gestao.reuniao_cci_pauta p
                     WHERE p.dt_exclusao IS NULL
                       AND rcci.cd_reuniao_cci = p.cd_reuniao_cci) AS qt_pauta,
                   CASE WHEN rcci.dt_aprovado IS NULL AND rcci.dt_desaprovado IS NULL THEN 'S'
                        ELSE 'N'
                   END AS fl_edicao,
                   ct.ds_reuniao_cci_tipo,
                   cl.ds_reuniao_cci_local
			  FROM gestao.reuniao_cci rcci
              LEFT JOIN projetos.usuarios_controledi uc1
                ON uc1.codigo = rcci.cd_usuario_coordenador_cci
              JOIN gestao.reuniao_cci_tipo ct
                ON rcci.cd_reuniao_cci_tipo = ct.cd_reuniao_cci_tipo
              JOIN gestao.reuniao_cci_local cl
                ON rcci.cd_reuniao_cci_local = cl.cd_reuniao_cci_local
		     WHERE rcci.cd_reuniao_cci = ".intval($args['cd_reuniao_cci']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_reuniao_cci']) == 0)
		{
			$cd_reuniao_cci = intval($this->db->get_new_id("gestao.reuniao_cci", "cd_reuniao_cci"));
		
			$qr_sql = "
				INSERT INTO gestao.reuniao_cci
				     (
					   cd_reuniao_cci,
					   nr_ano,
                       nr_numero,
                       dt_reuniao_cci,
                       cd_reuniao_cci_tipo,
                       cd_reuniao_cci_local,
                       cd_usuario_coordenador_cci,
                       cd_usuario_inclusao, 
					   cd_usuario_alteracao
					 )
                VALUES 
				     (
					   ".intval($cd_reuniao_cci).",
                       ".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
                       ".(trim($args['nr_numero']) != '' ? intval($args['nr_numero']) : "DEFAULT").",
                       ".(((trim($args['dt_reuniao_cci']) != "") AND (trim($args['hr_reuniao_cci']) != "")) ? "TO_TIMESTAMP('".trim($args['dt_reuniao_cci'])." ".trim($args['hr_reuniao_cci'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
					   ".(trim($args['cd_reuniao_cci_tipo']) != '' ? intval($args['cd_reuniao_cci_tipo']) : "DEFAULT").",
                       ".(trim($args['cd_reuniao_cci_local']) != '' ? intval($args['cd_reuniao_cci_local']) : "DEFAULT").",
                       ".(trim($args['cd_usuario_coordenador_cci']) != '' ? intval($args['cd_usuario_coordenador_cci']) : "DEFAULT").",
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$cd_reuniao_cci = intval($args['cd_reuniao_cci']);
		
			$qr_sql = "
				UPDATE gestao.reuniao_cci
                   SET nr_ano                     = ".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
                       nr_numero                  = ".(trim($args['nr_numero']) != '' ? intval($args['nr_numero']) : "DEFAULT").",
                       dt_reuniao_cci             = ".(((trim($args['dt_reuniao_cci']) != "") AND (trim($args['hr_reuniao_cci']) != "")) ? "TO_TIMESTAMP('".trim($args['dt_reuniao_cci'])." ".trim($args['hr_reuniao_cci'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                       cd_reuniao_cci_tipo        = ".(trim($args['cd_reuniao_cci_tipo']) != '' ? intval($args['cd_reuniao_cci_tipo']) : "DEFAULT").",
                       cd_reuniao_cci_local       = ".(trim($args['cd_reuniao_cci_local']) != '' ? intval($args['cd_reuniao_cci_local']) : "DEFAULT").",
                       cd_usuario_coordenador_cci = ".(trim($args['cd_usuario_coordenador_cci']) != '' ? intval($args['cd_usuario_coordenador_cci']) : "DEFAULT").",
					   cd_usuario_alteracao       = ".intval($args['cd_usuario']).",
					   dt_alteracao               = CURRENT_TIMESTAMP
				WHERE cd_reuniao_cci = ".intval($args['cd_reuniao_cci']).";";
		}

		$result = $this->db->query($qr_sql);
		
		return $cd_reuniao_cci;
	}
    
    function salvar_membro_efetivo(&$result, $args=array())
    {
        $qr_sql = "
            INSERT INTO gestao.reuniao_cci_membro_efetivo_item
                 (
                   cd_reuniao_cci_membro_efetivo, 
                   cd_reuniao_cci, 
                   cd_usuario_inclusao
                 )
            VALUES 
                 (
                   ".(trim($args['cd_reuniao_cci_membro_efetivo']) != '' ? intval($args['cd_reuniao_cci_membro_efetivo']) : "DEFAULT").",
                   ".(trim($args['cd_reuniao_cci']) != '' ? intval($args['cd_reuniao_cci']) : "DEFAULT").",
                   ".intval($args['cd_usuario'])."
                 );";
        $result = $this->db->query($qr_sql);
    }
    
    function listar_membro_efetivo(&$result, $args=array())
    {
        $qr_sql = "
            SELECT mei.cd_reuniao_cci_membro_efetivo_item,
                   TO_CHAR(mei.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   me.ds_reuniao_cci_membro_efetivo,
                   uc.nome
              FROM gestao.reuniao_cci_membro_efetivo_item mei
              JOIN gestao.reuniao_cci_membro_efetivo me
                ON me.cd_reuniao_cci_membro_efetivo = mei.cd_reuniao_cci_membro_efetivo
              JOIN projetos.usuarios_controledi uc 
                ON uc.codigo = mei.cd_usuario_inclusao
             WHERE mei.dt_exclusao IS NULL
               AND mei.cd_reuniao_cci = ".intval($args['cd_reuniao_cci']).";";
        
        $result = $this->db->query($qr_sql);
    }
    
    function excluir_membro_efetivo(&$result, $args=array())
    {
        $qr_sql = "
			UPDATE gestao.reuniao_cci_membro_efetivo_item
               SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_reuniao_cci_membro_efetivo_item = ".intval($args['cd_reuniao_cci_membro_efetivo_item']).";";
			 
		$result = $this->db->query($qr_sql);
    }
    
    function salvar_convidado(&$result, $args=array())
    {
        $qr_sql = "
            INSERT INTO gestao.reuniao_cci_convidado_item
                 (
                   cd_reuniao_cci_convidado, 
                   cd_reuniao_cci, 
                   cd_usuario_inclusao
                 )
            VALUES 
                 (
                   ".(trim($args['cd_reuniao_cci_convidado']) != '' ? intval($args['cd_reuniao_cci_convidado']) : "DEFAULT").",
                   ".(trim($args['cd_reuniao_cci']) != '' ? intval($args['cd_reuniao_cci']) : "DEFAULT").",
                   ".intval($args['cd_usuario'])."
                 );";
        $result = $this->db->query($qr_sql);
    }
    
    function listar_convidado(&$result, $args=array())
    {
        $qr_sql = "
            SELECT mei.cd_reuniao_cci_convidado_item,
                   TO_CHAR(mei.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   me.ds_reuniao_cci_convidado,
                   uc.nome
              FROM gestao.reuniao_cci_convidado_item mei
              JOIN gestao.reuniao_cci_convidado me
                ON me.cd_reuniao_cci_convidado = mei.cd_reuniao_cci_convidado
              JOIN projetos.usuarios_controledi uc 
                ON uc.codigo = mei.cd_usuario_inclusao
             WHERE mei.dt_exclusao IS NULL
               AND mei.cd_reuniao_cci = ".intval($args['cd_reuniao_cci']).";";
        
        $result = $this->db->query($qr_sql);
    }
    
    function excluir_convidado(&$result, $args=array())
    {
        $qr_sql = "
			UPDATE gestao.reuniao_cci_convidado_item
               SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_reuniao_cci_convidado_item = ".intval($args['cd_reuniao_cci_convidado_item']).";";
			 
		$result = $this->db->query($qr_sql);
    }
    
    function salvar_pauta(&$result, $args=array())
    {
        $qr_sql = "
            INSERT INTO gestao.reuniao_cci_pauta
                 (
                   cd_reuniao_cci, 
                   ds_reuniao_cci_pauta,  
                   cd_usuario_inclusao
                 )
            VALUES 
                 (
                   ".(trim($args['cd_reuniao_cci']) != '' ? intval($args['cd_reuniao_cci']) : "DEFAULT").",
                   ".(trim($args['ds_reuniao_cci_pauta']) != '' ? str_escape($args['ds_reuniao_cci_pauta']) : "DEFAULT").",
                   ".intval($args['cd_usuario'])."
                 );";
        
        $result = $this->db->query($qr_sql);
    }
    
    function listar_pauta(&$result, $args=array())
    {
        $qr_sql = "
            SELECT rcp.cd_reuniao_cci_pauta,
                   TO_CHAR(rcp.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   rcp.ds_reuniao_cci_pauta,
                   uc.nome,
                   CASE WHEN rcp.dt_aprovado IS NULL THEN 'Desaprovado'
                        ELSE 'Aprovado'
                   END AS status,
                   CASE WHEN rcp.dt_aprovado IS NULL THEN 'label label-important'
                        ELSE 'label label-success'
                   END AS class_status
              FROM gestao.reuniao_cci_pauta rcp
              JOIN projetos.usuarios_controledi uc 
                ON uc.codigo = rcp.cd_usuario_inclusao
             WHERE rcp.dt_exclusao IS NULL
               AND rcp.cd_reuniao_cci = ".intval($args['cd_reuniao_cci']).";";
        
        $result = $this->db->query($qr_sql);
    }
    
    function listar_pauta_aprovada(&$result, $args=array())
    {
        $qr_sql = "
            SELECT rcp.ds_reuniao_cci_pauta
              FROM gestao.reuniao_cci_pauta rcp
             WHERE rcp.dt_exclusao IS NULL
               AND dt_aprovado IS NOT NULL
               AND rcp.cd_reuniao_cci = ".intval($args['cd_reuniao_cci'])."
             ORDER BY rcp.dt_inclusao;";
        
        $result = $this->db->query($qr_sql);
    }
    
    function excluir_pauta(&$result, $args=array())
    {
        $qr_sql = "
			UPDATE gestao.reuniao_cci_pauta
               SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_reuniao_cci_pauta = ".intval($args['cd_reuniao_cci_pauta']).";";
			 
		$result = $this->db->query($qr_sql);
    }
    
    function enviar(&$result, $args=array())
    {
        $qr_sql = "
			UPDATE gestao.reuniao_cci
               SET cd_usuario_enviado = ".intval($args['cd_usuario']).",
			       dt_enviado         = CURRENT_TIMESTAMP
			 WHERE cd_reuniao_cci = ".intval($args['cd_reuniao_cci']).";";
			 
		$result = $this->db->query($qr_sql);
    }
    
    function listar_minhas(&$result, $args=array())
    {
        $qr_sql = "
			SELECT rcci.cd_reuniao_cci, 
			       gestao.nr_reuniao_cci(rcci.nr_ano, rcci.nr_numero) AS ano_numero,
                   TO_CHAR(rcci.dt_reuniao_cci, 'DD/MM/YYYY') AS dt_reuniao_cci, 
                   TO_CHAR(rcci.dt_reuniao_cci, 'HH24:MI') AS hr_reuniao_cci,
                   t.ds_reuniao_cci_tipo,
                   l.ds_reuniao_cci_local,
                   uc1.nome AS usuario_coordenador_cci,
                   TO_CHAR(rcci.dt_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_enviado, 
                   uc2.nome AS usuario_enviado,
                   TO_CHAR(rcci.dt_aprovado, 'DD/MM/YYYY HH24:MI:SS') AS dt_aprovado,
                   TO_CHAR(rcci.dt_desaprovado, 'DD/MM/YYYY HH24:MI:SS') AS dt_desaprovado,
                   CASE WHEN rcci.dt_aprovado IS NOT NULL THEN 'Aprovado'
                        WHEN rcci.dt_aprovado IS NULL AND rcci.dt_desaprovado IS NOT NULL THEN 'Desaprovado'
                        ELSE 'Aguardando Aprovaчуo'
                   END AS status,
                   CASE WHEN rcci.dt_aprovado IS NOT NULL THEN 'label label-success'
                        WHEN rcci.dt_aprovado IS NULL AND rcci.dt_desaprovado IS NOT NULL THEN 'label label-important'
                        ELSE 'label label-warning'
                   END AS class_status
			  FROM gestao.reuniao_cci rcci
              LEFT JOIN gestao.reuniao_cci_tipo t
                ON t.cd_reuniao_cci_tipo = rcci.cd_reuniao_cci_tipo
              LEFT JOIN gestao.reuniao_cci_local l
                ON l.cd_reuniao_cci_local = rcci.cd_reuniao_cci_local
              LEFT JOIN projetos.usuarios_controledi uc1
                ON uc1.codigo = rcci.cd_usuario_coordenador_cci
              LEFT JOIN projetos.usuarios_controledi uc2
                ON uc2.codigo = rcci.cd_usuario_enviado
		     WHERE rcci.dt_exclusao IS NULL
               AND rcci.cd_usuario_coordenador_cci = ".intval($args['cd_usuario'])."
               AND rcci.dt_enviado IS NOT NULL
               AND rcci.dt_aprovado IS NULL 
               AND rcci.dt_desaprovado IS NULL
               ".(trim($args['nr_ano']) != '' ? "AND rcci.nr_ano = ".intval($args['nr_ano']) : "")."
			   ".(trim($args['nr_numero']) != '' ? "AND rcci.nr_numero = ".intval($args['nr_numero']) : "")."
               ".(trim($args['cd_reuniao_cci_tipo']) != '' ? "AND rcci.cd_reuniao_cci_tipo = ".intval($args['cd_reuniao_cci_tipo']) : "")."    
               ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? " AND DATE_TRUNC('day', rcci.dt_reuniao_cci) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['fl_status']) == 'A' ? "AND rcci.dt_aprovado IS NOT NULL" : "")."
			   ".(trim($args['fl_status']) == 'D' ? "AND rcci.dt_desaprovado IS NOT NULL" : "").";";

        $result = $this->db->query($qr_sql);
    }
    
    function aprovar(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE gestao.reuniao_cci_pauta
               SET cd_usuario_aprovado = ".intval($args['cd_usuario']).",
                   dt_aprovado         = CURRENT_TIMESTAMP
             WHERE cd_reuniao_cci = ".intval($args['cd_reuniao_cci'])."
               AND cd_reuniao_cci_pauta IN ('".implode("','", $args['reuniao_cci_pauta'])."');
                   
            UPDATE gestao.reuniao_cci
               SET cd_usuario_aprovado = ".intval($args['cd_usuario']).",
                   dt_aprovado         = CURRENT_TIMESTAMP
             WHERE cd_reuniao_cci = ".intval($args['cd_reuniao_cci']).";";
        
        
        $result = $this->db->query($qr_sql);
    }
    
    function desaprovar(&$result, $args=array())
    {
        $qr_sql = "                   
            UPDATE gestao.reuniao_cci
               SET cd_usuario_desaprovado = ".intval($args['cd_usuario']).",
                   dt_desaprovado         = CURRENT_TIMESTAMP
             WHERE cd_reuniao_cci = ".intval($args['cd_reuniao_cci']).";";
        
        
        $result = $this->db->query($qr_sql);
    }
   
}
?>