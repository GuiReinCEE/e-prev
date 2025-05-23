<?php
class Regimento_interno_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

	public function listar($cd_regimento_interno_tipo, $args = array())
	{
		$qr_sql = "
		    SELECT ri.cd_regimento_interno,
                   TO_CHAR(ri.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
                   ri.nr_versao, 
                   ri.arquivo,
                   ri.arquivo_nome,
				   ri.tempo_vencimento,
                   rit.ds_regimento_interno_tipo,
                   rit.cd_gerencia_responsavel,
                   CASE WHEN rit.fl_publicado_site = 'S'
                        THEN 'Sim'
                        ELSE 'Não'
                   END AS ds_publicado_site
              FROM gestao.regimento_interno ri
              JOIN gestao.regimento_interno_tipo  rit
                ON ri.cd_regimento_interno_tipo = rit.cd_regimento_interno_tipo
             WHERE ri.dt_exclusao IS NULL
               AND ri.cd_regimento_interno_tipo = ".intval($cd_regimento_interno_tipo)." 
               ".(((trim($args['dt_referencia']) != '') AND trim($args['dt_referencia_fim']) != '') ? "AND DATE_TRUNC('day', dt_referencia) BETWEEN TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYY') AND TO_DATE('".$args['dt_referencia_fim']."', 'DD/MM/YYY')" : '')."
             ORDER BY nr_versao DESC
             LIMIT 1 ;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_regimento_interno_tipo()
	{
		$qr_sql = "
			SELECT cd_regimento_interno_tipo AS value,
                   ds_regimento_interno_tipo AS text
			  FROM gestao.regimento_interno_tipo
			 WHERE dt_exclusao IS NULL
             ORDER BY ds_regimento_interno_tipo DESC;";

		return $this->db->query($qr_sql)->result_array();
	}	

	public function lista_regimento_interno_tipo($args = array())
	{
		$qr_sql = "
			SELECT cd_regimento_interno_tipo,
                   ds_regimento_interno_tipo 
			  FROM gestao.regimento_interno_tipo 
			 WHERE dt_exclusao IS NULL
			 ".(trim($args['cd_regimento_interno_tipo']) != '' ? "AND cd_regimento_interno_tipo = ".intval($args['cd_regimento_interno_tipo']) : "")." 
             ORDER BY ds_regimento_interno_tipo DESC;";

		return $this->db->query($qr_sql)->result_array();
	}	
  
	public function carrega($cd_regimento_interno)
	{
		$qr_sql = "
			SELECT ri.cd_regimento_interno,
			       TO_CHAR(ri.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
			       TO_CHAR(ri.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       ri.tempo_vencimento,
			       ri.nr_versao,
			       ri.arquivo,
			       ri.arquivo_nome,
			       ri.cd_regimento_interno_tipo,
			       rit.ds_regimento_interno_tipo,
			       funcoes.get_usuario_nome(ri.cd_usuario_envio) AS ds_usuario_envio
			  FROM gestao.regimento_interno ri
			  JOIN gestao.regimento_interno_tipo  rit
                ON ri.cd_regimento_interno_tipo = rit.cd_regimento_interno_tipo
			 WHERE ri.cd_regimento_interno = ".intval($cd_regimento_interno).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.regimento_interno
			     (
			       dt_referencia,
			       tempo_vencimento,
			       nr_versao,
			       cd_regimento_interno_tipo,
			       arquivo,
			       arquivo_nome,
			       cd_usuario_inclusao,
               	   cd_usuario_alteracao 	 
			     )           
                       
			VALUES
			     (
				    ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
				    ".(trim($args['tempo_vencimento']) != '' ? intval($args['tempo_vencimento']) : "DEFAULT").",
                    ".(trim($args['nr_versao']) != '' ? intval($args['nr_versao']) : "DEFAULT").",			        
			     	".(trim($args['cd_regimento_interno_tipo']) != '' ? intval($args['cd_regimento_interno_tipo']) : "DEFAULT").",
				    ".(trim($args['arquivo']) != '' ? "'".$args['arquivo']."'" : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != '' ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
			     );";

		$this->db->query($qr_sql); 
	}

	public function atualizar($cd_regimento_interno, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.regimento_interno
               SET dt_referencia             = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
 	               tempo_vencimento          = ".(trim($args['tempo_vencimento']) != '' ? intval($args['tempo_vencimento']) : "DEFAULT").",               
 	               nr_versao                 = ".(trim($args['nr_versao']) != '' ? intval($args['nr_versao']) : "DEFAULT").",               
                   cd_regimento_interno_tipo = ".(trim($args['cd_regimento_interno_tipo']) != '' ? intval($args['cd_regimento_interno_tipo']) : "DEFAULT").",
                   arquivo_nome              = ".(trim($args['arquivo_nome']) != '' ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
                   arquivo                   = ".(trim($args['arquivo']) != '' ? "'".$args['arquivo']."'" : "DEFAULT").",
			       cd_usuario_alteracao      = ".intval($args['cd_usuario']).", 
			       dt_alteracao              =  CURRENT_TIMESTAMP                   
             WHERE cd_regimento_interno      = ".intval($cd_regimento_interno).";";

        $this->db->query($qr_sql);  
	}

	public function get_versao($cd_regimento_interno_tipo)
	{
		$qr_sql = "
			SELECT (nr_versao + 1) nr_versao
			  FROM gestao.regimento_interno
			 WHERE cd_regimento_interno_tipo = ".intval($cd_regimento_interno_tipo)."
			 ORDER BY nr_versao DESC 
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function lista_versoes_anteriores($cd_regimento_interno, $cd_regimento_interno_tipo)
	{
		$qr_sql = "
			SELECT ri.cd_regimento_interno,
                   TO_CHAR(ri.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
                   ri.nr_versao, 
                   ri.arquivo,
                   ri.arquivo_nome,
                   rit.ds_regimento_interno_tipo 
              FROM gestao.regimento_interno ri
              JOIN gestao.regimento_interno_tipo  rit
                ON ri.cd_regimento_interno_tipo = rit.cd_regimento_interno_tipo
             WHERE ri.dt_exclusao IS NULL
               AND ri.cd_regimento_interno_tipo = ".intval($cd_regimento_interno_tipo)."
               AND ri.cd_regimento_interno      != ".intval($cd_regimento_interno)."
	         ORDER BY nr_versao DESC";

		return $this->db->query($qr_sql)->result_array();
	}

	public function enviar($cd_regimento_interno, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.regimento_interno
               SET cd_usuario_envio = ".intval($cd_usuario).", 
			       dt_envio         =  CURRENT_TIMESTAMP
             WHERE cd_regimento_interno = ".intval($cd_regimento_interno).";";

        $this->db->query($qr_sql);  
	}
}
?>