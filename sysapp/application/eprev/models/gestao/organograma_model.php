<?php
class Organograma_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT ce.cd_organograma,
			       TO_CHAR(ce.dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
			       ce.arquivo,
			       ce.arquivo_nome,
			       (CASE WHEN (SELECT ce2.cd_organograma
			                     FROM gestao.organograma ce2
			                    WHERE ce2.dt_exclusao IS NULL
			                    ORDER BY ce2.dt_referencia DESC 
			                    LIMIT 1) = ce.cd_organograma
			             THEN 'S'
			             ELSE 'N'
			        END) AS fl_editar
			  FROM gestao.organograma ce
			 WHERE ce.dt_exclusao IS NULL
               ".(((trim($args['dt_referencia']) != '') AND trim($args['dt_referencia_fim']) != '') ? "AND DATE_TRUNC('day', dt_referencia) BETWEEN TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYY') AND TO_DATE('".$args['dt_referencia_fim']."', 'DD/MM/YYY')" : '').";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_organograma)
	{
		$qr_sql = "
			SELECT cd_organograma,
			       TO_CHAR(dt_referencia, 'DD/MM/YYYY') AS dt_referencia,
			       arquivo,
			       arquivo_nome,
			       TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       funcoes.get_usuario_nome(cd_usuario_envio) AS ds_usuario_envio
			  FROM gestao.organograma 
			 WHERE cd_organograma = ".intval($cd_organograma).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.organograma
			     (
			       dt_referencia,
			       arquivo,
			       arquivo_nome,
			       cd_usuario_inclusao,
               	   cd_usuario_alteracao
			     )
			VALUES
			     (
				    ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
				    ".(trim($args['arquivo']) != "" ? "'".$args['arquivo']."'" : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != "" ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
			     );";

		$this->db->query($qr_sql); 

	}

	public function atualizar($cd_organograma, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.organograma
               SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   arquivo_nome         = ".(trim($args['arquivo_nome']) != "" ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
                   arquivo              = ".(trim($args['arquivo']) != "" ? "'".$args['arquivo']."'" : "DEFAULT").",
			       cd_usuario_alteracao = ".intval($args['cd_usuario']).", 
			       dt_alteracao         =  CURRENT_TIMESTAMP                   
             WHERE cd_organograma = ".intval($cd_organograma).";";

        $this->db->query($qr_sql);  
	}

	public function get_organograma()
	{
		$qr_sql = "
			SELECT arquivo, 
			       arquivo_nome
              FROM gestao.organograma
             WHERE dt_exclusao IS NULL
             ORDER BY dt_referencia DESC 
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
	}

	public function enviar($cd_organograma, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.organograma
               SET cd_usuario_envio = ".intval($cd_usuario).", 
			       dt_envio         =  CURRENT_TIMESTAMP
             WHERE cd_organograma = ".intval($cd_organograma).";";

        $this->db->query($qr_sql);  
	}
}
?>