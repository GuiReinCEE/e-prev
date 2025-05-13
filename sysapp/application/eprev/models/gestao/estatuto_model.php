<?php
class Estatuto_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

	public function listar($args = array())
	{
		$qr_sql = "
         	SELECT cd_estatuto,
         	       TO_CHAR(dt_aprovacao_cd, 'DD/MM/YYYY') AS dt_aprovacao_cd,
			       nr_ata_cd,
			       TO_CHAR(dt_envio_spc, 'DD/MM/YYYY') AS dt_envio_spc,
			       TO_CHAR(dt_aprovacao_spc, 'DD/MM/YYYY') AS dt_aprovacao_spc,
			       ds_aprovacao_spc,
			       arquivo,
			       arquivo_nome
		      FROM gestao.estatuto
			 WHERE dt_exclusao IS NULL
               ".(((trim($args['dt_aprovacao_cd_ini']) != '') AND trim($args['dt_aprovacao_cd_fim']) != '') ? "AND DATE_TRUNC('day', dt_aprovacao_cd) BETWEEN TO_DATE('".$args['dt_aprovacao_cd_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_aprovacao_cd_fim']."', 'DD/MM/YYYY')" : '')."
               ".(((trim($args['dt_aprovacao_spc_ini']) != '') AND trim($args['dt_aprovacao_spc_fim']) != '') ? "AND DATE_TRUNC('day', dt_aprovacao_spc) BETWEEN TO_DATE('".$args['dt_aprovacao_spc_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_aprovacao_spc_fim']."', 'DD/MM/YYYY')" : '')."
	         ORDER BY dt_aprovacao_cd DESC;";

		return $this->db->query($qr_sql)->result_array();	
	}

	public function carrega($cd_estatuto)
	{
		$qr_sql = "
			SELECT cd_estatuto,
         	       TO_CHAR(dt_aprovacao_cd, 'DD/MM/YYYY') AS dt_aprovacao_cd,
			       nr_ata_cd,
			       TO_CHAR(dt_envio_spc, 'DD/MM/YYYY') AS dt_envio_spc,
			       TO_CHAR(dt_aprovacao_spc, 'DD/MM/YYYY') AS dt_aprovacao_spc,
			       TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       ds_aprovacao_spc,
			       arquivo,
			       arquivo_nome,
			       funcoes.get_usuario_nome(cd_usuario_envio) AS ds_usuario_envio
			  FROM gestao.estatuto 
			 WHERE cd_estatuto = ".intval($cd_estatuto).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.estatuto
			     (
			       dt_aprovacao_cd,
			       dt_envio_spc,
			       dt_aprovacao_spc,
			       ds_aprovacao_spc,
			       nr_ata_cd,
                   arquivo,
			       arquivo_nome,
			       cd_usuario_inclusao,
               	   cd_usuario_alteracao
			     )
			VALUES
			     (
				    ".(trim($args['dt_aprovacao_cd']) != '' ? "TO_DATE('".$args['dt_aprovacao_cd']."', 'DD/MM/YYYY')" : "DEFAULT").",
				    ".(trim($args['dt_envio_spc']) != '' ? "TO_DATE('".$args['dt_envio_spc']."', 'DD/MM/YYYY')" : "DEFAULT").",
				    ".(trim($args['dt_aprovacao_spc']) != '' ? "TO_DATE('".$args['dt_aprovacao_spc']."', 'DD/MM/YYYY')" : "DEFAULT").",
			     	".(trim($args['ds_aprovacao_spc']) != '' ? str_escape($args['ds_aprovacao_spc']) : "DEFAULT").",                    			        
                    ".(trim($args['nr_ata_cd']) != '' ? intval($args['nr_ata_cd']) : "DEFAULT").",
				    ".(trim($args['arquivo']) != "" ? "'".$args['arquivo']."'" : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != "" ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
			     );";

		$this->db->query($qr_sql); 
	}

	public function atualizar($cd_estatuto, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.estatuto
			   SET dt_aprovacao_cd      = ".(trim($args['dt_aprovacao_cd']) != '' ? "TO_DATE('".$args['dt_aprovacao_cd']."', 'DD/MM/YYYY')" : "DEFAULT").",
			       nr_ata_cd            = ".(intval($args['nr_ata_cd']) > 0 ? intval($args['nr_ata_cd']) : "DEFAULT").",
			       dt_envio_spc         = ".(trim($args['dt_envio_spc']) != '' ? "TO_DATE('".$args['dt_envio_spc']."', 'DD/MM/YYYY')" : "DEFAULT").",
			       dt_aprovacao_spc     = ".(trim($args['dt_aprovacao_spc']) != '' ? "TO_DATE('".$args['dt_aprovacao_spc']."', 'DD/MM/YYYY')" : "DEFAULT").",
			       ds_aprovacao_spc     = ".(trim($args['ds_aprovacao_spc']) != '' ? str_escape($args['ds_aprovacao_spc']) : "DEFAULT").",
			       arquivo              = ".(trim($args['arquivo']) != "" ? "'".$args['arquivo']."'" : "DEFAULT").",
			       arquivo_nome         = ".(trim($args['arquivo_nome']) != "" ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
               	   cd_usuario_alteracao = ".intval($args['cd_usuario']).", 
			       dt_alteracao         =  CURRENT_TIMESTAMP                   
             WHERE cd_estatuto = ".intval($cd_estatuto).";";

        $this->db->query($qr_sql);  
	}

	public function get_estatuto()
	{
		$qr_sql = "
			SELECT arquivo, 
			       arquivo_nome
              FROM gestao.estatuto
             WHERE dt_exclusao IS NULL
             ORDER BY dt_aprovacao_cd DESC 
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
	}

	public function enviar($cd_estatuto, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.estatuto
               SET cd_usuario_envio = ".intval($cd_usuario).", 
			       dt_envio         =  CURRENT_TIMESTAMP
             WHERE cd_estatuto = ".intval($cd_estatuto).";";

        $this->db->query($qr_sql);  
	}
}
?>