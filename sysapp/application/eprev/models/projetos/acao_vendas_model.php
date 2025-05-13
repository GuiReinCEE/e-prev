<?php
class Acao_vendas_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_acao_vendas,
				   funcoes.get_usuario_nome(cd_usuario_responsavel) AS ds_usuario_responsavel,
			       ds_acao_vendas,
			       TO_CHAR(dt_acao_vendas, 'DD/MM/YYYY') AS dt_acao_vendas,
			       TO_CHAR(dt_acao_vendas, 'HH24:MI') AS hr_acao_vendas,
			       nr_contatos,
			       nr_fechamento
			  FROM projetos.acao_vendas
			 WHERE dt_exclusao IS NULL
		       ".(intval($args['cd_usuario_responsavel']) > 0 ? "AND cd_usuario_responsavel = ".intval($args['cd_usuario_responsavel']) : "")."
               ".(((trim($args['dt_acao_vendas_ini']) != '') AND trim($args['dt_acao_vendas_fim']) != '') ? "AND DATE_TRUNC('day', dt_acao_vendas) BETWEEN TO_DATE('".$args['dt_acao_vendas_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_acao_vendas_fim']."', 'DD/MM/YYYY')" : '')."
               ORDER BY dt_acao_vendas DESC";

		return $this->db->query($qr_sql)->result_array();	
	}

	public function get_usuarios()
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('GCM')";

        return $this->db->query($qr_sql)->result_array();
    }

	public function carrega($cd_acao_vendas)
	{
		$qr_sql = "
			SELECT cd_acao_vendas,
				   cd_usuario_responsavel,
			       ds_acao_vendas,
			       TO_CHAR(dt_acao_vendas, 'DD/MM/YYYY') AS dt_acao_vendas,
			       TO_CHAR(dt_acao_vendas, 'HH24:MI') AS hr_acao_vendas,
			       nr_contatos,
			       nr_fechamento
			  FROM projetos.acao_vendas
			 WHERE cd_acao_vendas = ".intval($cd_acao_vendas).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.acao_vendas
			     (
			       ds_acao_vendas,
			       dt_acao_vendas,
			       nr_contatos,
			       nr_fechamento,
			       cd_usuario_responsavel,
			       cd_usuario_inclusao,
			       cd_usuario_alteracao
			     )
			VALUES
			     (  
			        ".(trim($args['ds_acao_vendas']) != '' ? str_escape($args['ds_acao_vendas']) : "DEFAULT").",  
			        ".(trim($args['dt_acao_vendas']) != '' ? "TO_TIMESTAMP('".trim($args['dt_acao_vendas'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
                    ".(trim($args['nr_contatos']) != '' ? intval($args['nr_contatos']) : "DEFAULT").",
                    ".(trim($args['nr_fechamento']) != '' ? intval($args['nr_fechamento']) : "DEFAULT").",
			     	".(intval($args['cd_usuario_responsavel']) > 0 ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
			     );";

		$this->db->query($qr_sql); 
	}

	public function atualizar($cd_acao_vendas, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.acao_vendas
			   SET ds_acao_vendas         = ".(trim($args['ds_acao_vendas']) != '' ? str_escape($args['ds_acao_vendas']) : "DEFAULT").",
			       dt_acao_vendas         = ".(trim($args['dt_acao_vendas']) != '' ? "TO_DATE('".$args['dt_acao_vendas']."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").",
			       nr_contatos            = ".(trim($args['nr_contatos']) != ''  ? intval($args['nr_contatos']) : "DEFAULT").",
			       nr_fechamento          = ".(trim($args['nr_fechamento']) != '' ? intval($args['nr_fechamento']) : "DEFAULT").",
			       cd_usuario_responsavel = ".(intval($args['cd_usuario_responsavel']) > 0 ? intval($args['cd_usuario_responsavel']) : "DEFAULT").",
               	   cd_usuario_alteracao   = ".intval($args['cd_usuario']).", 
			       dt_alteracao           =  CURRENT_TIMESTAMP                   
             WHERE cd_acao_vendas = ".intval($cd_acao_vendas).";";

        $this->db->query($qr_sql);  
	}
}
?>