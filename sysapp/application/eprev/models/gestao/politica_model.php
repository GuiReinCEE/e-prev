<?php
class Politica_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

	public function listar($cd_politica_tipo, $args = array())
	{
		$qr_sql = "
		    SELECT ri.cd_politica,
                   TO_CHAR(ri.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
                   ri.nr_versao, 
                   ri.arquivo,
                   ri.arquivo_nome,
                   rit.ds_politica_tipo,
                   rit.cd_gerencia_responsavel,
                   CASE WHEN rit.fl_publicado_site = 'S'
                        THEN 'Sim'
                        ELSE 'Não'
                   END AS ds_publicado_site
              FROM gestao.politica ri
              JOIN gestao.politica_tipo  rit
                ON ri.cd_politica_tipo = rit.cd_politica_tipo
             WHERE ri.dt_exclusao IS NULL
               AND ri.cd_politica_tipo = ".intval($cd_politica_tipo)." 
               ".(((trim($args['dt_referencia']) != '') AND trim($args['dt_referencia_fim']) != '') ? "AND DATE_TRUNC('day', dt_referencia) BETWEEN TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYY') AND TO_DATE('".$args['dt_referencia_fim']."', 'DD/MM/YYY')" : '')."
             ORDER BY nr_versao DESC
             LIMIT 1 ;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_politica_tipo()
	{
		$qr_sql = "
			SELECT cd_politica_tipo AS value,
                   ds_politica_tipo AS text
			  FROM gestao.politica_tipo
			 WHERE dt_exclusao IS NULL
             ORDER BY ds_politica_tipo DESC;";

		return $this->db->query($qr_sql)->result_array();
	}	

	public function lista_politica_tipo($args = array())
	{
		$qr_sql = "
			SELECT cd_politica_tipo,
                   ds_politica_tipo 
			  FROM gestao.politica_tipo 
			 WHERE dt_exclusao IS NULL
			 ".(trim($args['cd_politica_tipo']) != '' ? "AND cd_politica_tipo = ".intval($args['cd_politica_tipo']) : "")." 
             ORDER BY ds_politica_tipo DESC;";

		return $this->db->query($qr_sql)->result_array();
	}	
  
	public function carrega($cd_politica)
	{
		$qr_sql = "
			SELECT ri.cd_politica,
			       TO_CHAR(ri.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
			       TO_CHAR(ri.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       ri.nr_versao,
			       ri.arquivo,
			       ri.arquivo_nome,
			       ri.cd_politica_tipo,
			       rit.ds_politica_tipo,
			       funcoes.get_usuario_nome(ri.cd_usuario_envio) AS ds_usuario_envio
			  FROM gestao.politica ri
			  JOIN gestao.politica_tipo  rit
                ON ri.cd_politica_tipo = rit.cd_politica_tipo
			 WHERE ri.cd_politica = ".intval($cd_politica).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.politica
			     (
			       dt_referencia,
			       nr_versao,
			       cd_politica_tipo,
			       arquivo,
			       arquivo_nome,
			       cd_usuario_inclusao,
               	   cd_usuario_alteracao 	 
			     )           
                       
			VALUES
			     (
				    ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['nr_versao']) != '' ? intval($args['nr_versao']) : "DEFAULT").",			        
			     	".(trim($args['cd_politica_tipo']) != '' ? intval($args['cd_politica_tipo']) : "DEFAULT").",
				    ".(trim($args['arquivo']) != '' ? "'".$args['arquivo']."'" : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != '' ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
			     );";

		$this->db->query($qr_sql); 
	}

	public function atualizar($cd_politica, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.politica
               SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
 	               nr_versao            = ".(trim($args['nr_versao']) != '' ? intval($args['nr_versao']) : "DEFAULT").",               
                   cd_politica_tipo     = ".(trim($args['cd_politica_tipo']) != '' ? intval($args['cd_politica_tipo']) : "DEFAULT").",
                   arquivo_nome         = ".(trim($args['arquivo_nome']) != '' ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
                   arquivo              = ".(trim($args['arquivo']) != '' ? "'".$args['arquivo']."'" : "DEFAULT").",
			       cd_usuario_alteracao = ".intval($args['cd_usuario']).", 
			       dt_alteracao         =  CURRENT_TIMESTAMP                   
             WHERE cd_politica = ".intval($cd_politica).";";

        $this->db->query($qr_sql);  
	}

	public function get_versao($cd_politica_tipo)
	{
		$qr_sql = "
			SELECT (nr_versao + 1) nr_versao
			  FROM gestao.politica
			 WHERE cd_politica_tipo = ".intval($cd_politica_tipo)."
			 ORDER BY nr_versao DESC 
			 LIMIT 1;";

		return $this->db->query($qr_sql)->row_array();
	}

	public function lista_versoes_anteriores($cd_politica, $cd_politica_tipo)
	{
		$qr_sql = "
			SELECT ri.cd_politica,
                   TO_CHAR(ri.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
                   ri.nr_versao, 
                   ri.arquivo,
                   ri.arquivo_nome,
                   rit.ds_politica_tipo 
              FROM gestao.politica ri
              JOIN gestao.politica_tipo  rit
                ON ri.cd_politica_tipo = rit.cd_politica_tipo
             WHERE ri.dt_exclusao IS NULL
               AND ri.cd_politica_tipo = ".intval($cd_politica_tipo)."
               AND ri.cd_politica      != ".intval($cd_politica)."
	         ORDER BY nr_versao DESC";

		return $this->db->query($qr_sql)->result_array();
	}

	public function enviar($cd_politica, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.politica
               SET cd_usuario_envio = ".intval($cd_usuario).", 
			       dt_envio         =  CURRENT_TIMESTAMP
             WHERE cd_politica = ".intval($cd_politica).";";

        $this->db->query($qr_sql);  
	}
}
?>