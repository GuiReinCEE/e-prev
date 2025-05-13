<?php
class Atividade_script_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function listar($cd_atividade)
    {
        $qr_sql = "
            SELECT cd_atividade_script,
                   ds_atividade_script,
                   arquivo,
                   arquivo_nome,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario,
                   cd_usuario_inclusao
              FROM projetos.atividade_script 
             WHERE dt_exclusao IS NULL
               AND cd_atividade = '".trim($cd_atividade)."';";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar($cd_atividade, $args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.atividade_script
                 (
                    cd_atividade,
                    ds_atividade_script,
                    arquivo,
                    arquivo_nome,
                    cd_usuario_inclusao
                 )
            VALUES 
                 (
                    ".intval($cd_atividade).",
                    ".(trim($args['ds_atividade_script']) != '' ? str_escape($args['ds_atividade_script']) : "DEFAULT").",
                    ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != '' ? "'".trim($args['arquivo_nome'])."'" : "DEFAULT").",
                    ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function excluir($cd_atividade_script, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.atividade_script
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
				   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_atividade_script = ".intval($cd_atividade_script).";";
             
		$this->db->query($qr_sql);
	}
}
?>