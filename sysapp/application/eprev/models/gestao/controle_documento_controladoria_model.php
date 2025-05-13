<?php
class Controle_documento_controladoria_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function get_usuario($usuario = array())
	{
		$qr_sql = "
			SELECT uc.codigo AS value,
			       uc.divisao||' - '||uc.nome AS text,
			       (CASE WHEN uc.codigo IN (".(COUNT($usuario) > 0 ? implode(',', $usuario):0).") THEN 1 
			               ELSE 0 
			        END) AS ordernar
			  FROM projetos.usuarios_controledi uc
			  JOIN funcoes.get_gerencias_vigente() ggv
			  	ON uc.divisao = ggv.codigo
			 WHERE uc.tipo NOT IN ('X')
			 ORDER BY ordernar DESC,uc.divisao, uc.nome ASC;";		

		return $this->db->query($qr_sql)->result_array();
	}

    public function get_tipo()
	{
		$qr_sql = "
			SELECT cd_controle_documento_controladoria_tipo AS value,
                   ds_controle_documento_controladoria_tipo AS text
			  FROM gestao.controle_documento_controladoria_tipo
			 WHERE dt_exclusao IS NULL;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function tipo_listar($args = array())
	{
		$qr_sql = "
			SELECT t.cd_controle_documento_controladoria_tipo,
                   t.ds_controle_documento_controladoria_tipo
			  FROM gestao.controle_documento_controladoria_tipo t
			 WHERE t.dt_exclusao IS NULL
			   ".(intval($args['cd_controle_documento_controladoria_tipo']) > 0 ? "AND t.cd_controle_documento_controladoria_tipo = ".intval($args['cd_controle_documento_controladoria_tipo']) : "")."
			   ".(trim($args['cd_usuario']) != '' ? "AND (SELECT COUNT(*)
			   	                                             FROM gestao.controle_documento_controladoria_tipo_usuario tg
			   	                                            WHERE tg.dt_exclusao IS NULL
			   	                                              AND tg.cd_controle_documento_controladoria_tipo = t.cd_controle_documento_controladoria_tipo
			   	                                              AND tg.cd_usuario                              = '".trim($args['cd_usuario'])."') > 0" : "").";";	

		return $this->db->query($qr_sql)->result_array();
	}

	public function tipo_carrega($cd_controle_documento_controladoria_tipo)
	{
		$qr_sql = "
			SELECT cd_controle_documento_controladoria_tipo,
                   ds_controle_documento_controladoria_tipo
			  FROM gestao.controle_documento_controladoria_tipo
			 WHERE cd_controle_documento_controladoria_tipo = ".intval($cd_controle_documento_controladoria_tipo).";";		

		return $this->db->query($qr_sql)->row_array();
	}

	public function tipo_salvar($args = array())
	{
		$cd_controle_documento_controladoria_tipo = $this->db->get_new_id('gestao.controle_documento_controladoria_tipo', 'cd_controle_documento_controladoria_tipo');

		$qr_sql = "
			INSERT INTO gestao.controle_documento_controladoria_tipo
			     (
			       cd_controle_documento_controladoria_tipo,
			       ds_controle_documento_controladoria_tipo,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			        ".intval($cd_controle_documento_controladoria_tipo).",
			     	".(trim($args['ds_controle_documento_controladoria_tipo']) != '' ? str_escape($args['ds_controle_documento_controladoria_tipo']) : "DEFAULT").",
			        ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
			     );";

		if(count($args['usuario']) > 0)
        {
 			$qr_sql .= "
				INSERT INTO gestao.controle_documento_controladoria_tipo_usuario(cd_controle_documento_controladoria_tipo, cd_usuario, cd_usuario_inclusao, cd_usuario_alteracao)
				SELECT ".intval($cd_controle_documento_controladoria_tipo).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
				  FROM (VALUES (".implode("),(", $args['usuario']).")) x;";
		}

		$this->db->query($qr_sql); 
	}

	public function tipo_atualizar($cd_controle_documento_controladoria_tipo, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.controle_documento_controladoria_tipo
               SET ds_controle_documento_controladoria_tipo	= ".(trim($args['ds_controle_documento_controladoria_tipo']) != '' ? str_escape($args['ds_controle_documento_controladoria_tipo']) : "DEFAULT").",
			       cd_usuario_alteracao                     = ".intval($args['cd_usuario']).",
                   dt_alteracao                             = CURRENT_TIMESTAMP
            WHERE cd_controle_documento_controladoria_tipo = ".intval($cd_controle_documento_controladoria_tipo).";";

        if(count($args['usuario']) > 0)
        {
			 $qr_sql .= "
        		UPDATE gestao.controle_documento_controladoria_tipo_usuario
				   SET cd_usuario_exclusao                      = ".intval($args['cd_usuario']).",
					   dt_exclusao                              = CURRENT_TIMESTAMP
				 WHERE cd_controle_documento_controladoria_tipo = ".intval($cd_controle_documento_controladoria_tipo)."
				   AND dt_exclusao IS NULL
				   AND cd_usuario NOT IN (".implode(",", $args['usuario']).");
	   
				INSERT INTO gestao.controle_documento_controladoria_tipo_usuario(cd_controle_documento_controladoria_tipo, cd_usuario, cd_usuario_inclusao, cd_usuario_alteracao)
				SELECT ".intval($cd_controle_documento_controladoria_tipo).", x.column1, ".intval($args['cd_usuario']).", ".intval($args['cd_usuario'])."
				  FROM (VALUES (".implode("),(", $args['usuario']).")) x
				 WHERE x.column1 NOT IN (SELECT a.cd_usuario
										   FROM gestao.controle_documento_controladoria_tipo_usuario a
										  WHERE a.cd_controle_documento_controladoria_tipo = ".intval($cd_controle_documento_controladoria_tipo)."
											AND a.dt_exclusao IS NULL);";
		}

        $this->db->query($qr_sql);  
	}

	public function get_usuario_check($cd_controle_documento_controladoria_tipo)
	{
		$qr_sql = "
			SELECT uc.divisao||' - '||funcoes.get_usuario_nome(ctu.cd_usuario) AS ds_usuario,
				   ctu.cd_usuario,
				   uc.usuario
              FROM gestao.controle_documento_controladoria_tipo_usuario ctu
              JOIN projetos.usuarios_controledi uc
                ON ctu.cd_usuario = uc.codigo
             WHERE ctu.dt_exclusao IS NULL
               AND ctu.cd_controle_documento_controladoria_tipo = ".intval($cd_controle_documento_controladoria_tipo).";";

        return $this->db->query($qr_sql)->result_array();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cdt.ds_controle_documento_controladoria_tipo,
			       cdc.cd_controle_documento_controladoria,
			       cdt.cd_controle_documento_controladoria_tipo,
			       cdc.arquivo,
			       cdc.arquivo_nome,
			       TO_CHAR(cdc.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
			       TO_CHAR(cdc.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       TO_CHAR(cdc.dt_envio, 'DD/MM/YYYY  HH24:MI:SS') AS dt_envio,
			       cdc.ds_controle_documento_controladoria
			  FROM gestao.controle_documento_controladoria_tipo cdt
			  LEFT JOIN gestao.controle_documento_controladoria cdc
			    ON cdc.cd_controle_documento_controladoria_tipo = cdt.cd_controle_documento_controladoria_tipo
			   AND cdc.dt_exclusao IS NULL
			 WHERE cdt.dt_exclusao IS NULL
			   AND (cdc.cd_controle_documento_controladoria IN (SELECT b.cd_controle_documento_controladoria
															      FROM gestao.controle_documento_controladoria b
															     WHERE b.dt_exclusao IS NULL
															       AND b.cd_controle_documento_controladoria_tipo = cdt.cd_controle_documento_controladoria_tipo
															     ORDER BY b.dt_inclusao DESC 
															     LIMIT 1)
			        OR
			       (SELECT COUNT(*)
					  FROM gestao.controle_documento_controladoria c
					 WHERE c.dt_exclusao IS NULL
					   AND c.cd_controle_documento_controladoria_tipo = cdt.cd_controle_documento_controladoria_tipo) = 0
			       )
				   ".(((trim($args['dt_inclusao_ini']) != '') AND (trim($args['dt_inclusao_fim']) != '')) ? "AND DATE_TRUNC('day', cdc.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_fim']."', 'DD/MM/YYYY')" : "")."
                   ".(((trim($args['dt_envio_ini']) != '') AND (trim($args['dt_envio_fim']) != '')) ? "AND DATE_TRUNC('day', cdc.dt_envio) BETWEEN TO_DATE('".$args['dt_envio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_envio_fim']."', 'DD/MM/YYYY')" : "")."
                   ".(trim($args['fl_envio']) == 'S' ? "AND dt_envio IS NOT NULL" : "")."
                   ".(trim($args['fl_envio']) == 'N' ? "AND dt_envio IS NULL": "")."
                   ".(trim($args['cd_controle_documento_controladoria_tipo']) != '' ? "AND cdt.cd_controle_documento_controladoria_tipo = ".intval($args['cd_controle_documento_controladoria_tipo']) : "")." 
             ORDER BY cdc.dt_inclusao desc;";

        return $this->db->query($qr_sql)->result_array();
	}

	public function lista_cadastro($cd_controle_documento_controladoria_tipo)
	{
		$qr_sql = "
			SELECT cdt.ds_controle_documento_controladoria_tipo,
				   cdc.cd_controle_documento_controladoria,
				   cdc.arquivo,
				   cdc.arquivo_nome,
				   TO_CHAR(cdc.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
				   TO_CHAR(cdc.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(cdc.dt_envio, 'DD/MM/YYYY  HH24:MI:SS') AS dt_envio,
				   cdc.ds_controle_documento_controladoria
              FROM gestao.controle_documento_controladoria_tipo cdt 
              JOIN gestao.controle_documento_controladoria cdc
                ON cdc.cd_controle_documento_controladoria_tipo = cdt.cd_controle_documento_controladoria_tipo
             WHERE cdc.dt_exclusao IS NULL
               AND cdc.cd_controle_documento_controladoria_tipo =  ".intval($cd_controle_documento_controladoria_tipo).";";

        return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_controle_documento_controladoria)
	{
		$qr_sql = "
			SELECT cdt.ds_controle_documento_controladoria_tipo,
				   cdc.cd_controle_documento_controladoria_tipo,
				   cdc.cd_controle_documento_controladoria,
				   cdc.arquivo,
				   cdc.arquivo_nome,
				   TO_CHAR(cdc.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
				   TO_CHAR(cdc.dt_referencia, 'MM/YYYY') AS ds_referencia_email,
				   TO_CHAR(cdc.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(cdc.dt_envio, 'DD/MM/YYYY  HH24:MI:SS') AS dt_envio,
				   cdc.ds_controle_documento_controladoria,
				   cdt.ds_caminho
              FROM gestao.controle_documento_controladoria_tipo cdt 
              JOIN gestao.controle_documento_controladoria cdc
                ON cdc.cd_controle_documento_controladoria_tipo = cdt.cd_controle_documento_controladoria_tipo
             WHERE cdc.dt_exclusao IS NULL
               AND cdc.cd_controle_documento_controladoria =  ".intval($cd_controle_documento_controladoria).";";

        return $this->db->query($qr_sql)->row_array();
	}

	public function envio_email($cd_controle_documento_controladoria, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.controle_documento_controladoria
			   SET cd_usuario_alteracao = ".intval($cd_usuario).",
			       cd_usuario_envio     = ".intval($cd_usuario).",
			       dt_envio 			= CURRENT_TIMESTAMP,
			       dt_alteracao 		= CURRENT_TIMESTAMP
			 WHERE cd_controle_documento_controladoria = ".intval($cd_controle_documento_controladoria).";";

		$this->db->query($qr_sql);	
	}
	
	public function salvar($args = array())
	{
		$cd_controle_documento_controladoria = $this->db->get_new_id('gestao.controle_documento_controladoria', 'cd_controle_documento_controladoria');

		$qr_sql = "
			INSERT INTO gestao.controle_documento_controladoria
			     (
			       cd_controle_documento_controladoria,
			       ds_controle_documento_controladoria,
			       cd_controle_documento_controladoria_tipo,
                   arquivo,
                   arquivo_nome,
                   dt_referencia,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (
			        ".intval($cd_controle_documento_controladoria).",
			     	".(trim($args['ds_controle_documento_controladoria']) != '' ? str_escape($args['ds_controle_documento_controladoria']) : "DEFAULT").",
			     	".(trim($args['cd_controle_documento_controladoria_tipo']) != '' ? intval($args['cd_controle_documento_controladoria_tipo']) : 'DEFAULT').",
			     	".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
			     	".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
			     	".(trim($args['dt_referencia']) != '' ? "TO_TIMESTAMP('".trim($args['dt_referencia'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
			        ".intval($args['cd_usuario']).",
				    ".intval($args['cd_usuario'])."
			     );";

		$this->db->query($qr_sql); 

		return $cd_controle_documento_controladoria;
	}

	public function excluir($cd_controle_documento_controladoria,$cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.controle_documento_controladoria
	           SET cd_usuario_exclusao  = ".intval($cd_usuario).",
	               dt_exclusao          = CURRENT_TIMESTAMP 
	         WHERE cd_controle_documento_controladoria = ".intval($cd_controle_documento_controladoria).";";

        $this->db->query($qr_sql);
	}

	public function get_tipo_minhas($cd_usuario)
	{
		$qr_sql = "
			SELECT cc.cd_controle_documento_controladoria_tipo AS value,
                   cc.ds_controle_documento_controladoria_tipo AS text
			  FROM gestao.controle_documento_controladoria_tipo cc
			 WHERE cc.dt_exclusao IS NULL
			   AND (SELECT COUNT(*)
					  FROM gestao.controle_documento_controladoria_tipo_usuario c
					 WHERE c.dt_exclusao IS NULL
					   AND c.cd_controle_documento_controladoria_tipo = cc.cd_controle_documento_controladoria_tipo
		               AND c.cd_usuario = ".intval($cd_usuario).") > 0";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function minhas_listar($args = array())
	{
		$qr_sql = "
			SELECT cdt.ds_controle_documento_controladoria_tipo,
			       cdt.cd_controle_documento_controladoria_tipo,
				   cdc.cd_controle_documento_controladoria,
				   cdc.arquivo,
				   cdc.arquivo_nome,
				   TO_CHAR(cdc.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
				   TO_CHAR(cdc.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(cdc.dt_envio, 'DD/MM/YYYY  HH24:MI:SS') AS dt_envio,
				   (SELECT COUNT(*)
				   	  FROM gestao.controle_documento_controladoria cd
				   	 WHERE cd.dt_exclusao IS NULL
				   	   AND cd.cd_controle_documento_controladoria_tipo = cdt.cd_controle_documento_controladoria_tipo) AS qt_doc,
				   cdc.ds_controle_documento_controladoria
              FROM gestao.controle_documento_controladoria_tipo cdt 
              JOIN gestao.controle_documento_controladoria cdc
                ON cdc.cd_controle_documento_controladoria_tipo = cdt.cd_controle_documento_controladoria_tipo
               AND cdc.dt_exclusao IS NULL
               AND cdc.dt_envio IS NOT NULL
		 	   AND cdc.arquivo IS NOT NULL
	           AND cdc.arquivo_nome IS NOT NULL
             WHERE cdc.dt_exclusao IS NULL
               AND cdc.cd_controle_documento_controladoria IN (SELECT b.cd_controle_documento_controladoria
															     FROM gestao.controle_documento_controladoria b
															    WHERE b.dt_exclusao IS NULL
															      AND b.cd_controle_documento_controladoria_tipo = cdt.cd_controle_documento_controladoria_tipo
															    ORDER BY b.dt_inclusao DESC
															    LIMIT 1)									     
               AND (SELECT COUNT(*)
					  FROM gestao.controle_documento_controladoria_tipo_usuario c
					 WHERE c.dt_exclusao IS NULL
					   AND c.cd_controle_documento_controladoria_tipo = cdc.cd_controle_documento_controladoria_tipo
		               AND c.cd_usuario = ".intval($args['cd_usuario']).") > 0
				   ".(trim($args['cd_controle_documento_controladoria_tipo']) != '' ? "AND cdt.cd_controle_documento_controladoria_tipo = ".intval($args['cd_controle_documento_controladoria_tipo']) : "" )." 
             ORDER BY cdc.dt_inclusao desc;";

        return $this->db->query($qr_sql)->result_array();
	}

	public function documentos($cd_controle_documento_controladoria_tipo, $args = array())
	{
		$qr_sql = "
			SELECT cdt.ds_controle_documento_controladoria_tipo,
				   cdc.cd_controle_documento_controladoria,
				   cdc.arquivo,
				   cdc.arquivo_nome,
				   TO_CHAR(cdc.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
				   TO_CHAR(cdc.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(cdc.dt_envio, 'DD/MM/YYYY  HH24:MI:SS') AS dt_envio,
				   cdc.ds_controle_documento_controladoria
              FROM gestao.controle_documento_controladoria_tipo cdt 
              JOIN gestao.controle_documento_controladoria cdc
                ON cdc.cd_controle_documento_controladoria_tipo = cdt.cd_controle_documento_controladoria_tipo
             WHERE cdc.dt_exclusao IS NULL
               AND cdc.cd_controle_documento_controladoria_tipo = ".intval($cd_controle_documento_controladoria_tipo).";";

        return $this->db->query($qr_sql)->result_array();
	}
}
?>