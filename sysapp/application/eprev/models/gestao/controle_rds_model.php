<?php

class Controle_rds_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    public function listar($cd_gerencia, $args = array(), $fl_gerente = 'N')
    {
        $qr_sql = "
			SELECT c.cd_controle_rds,
				   MD5(c.cd_controle_rds::TEXT) AS cd_controle_rds_md5,
				   gestao.nr_controle_rds(c.nr_ano, c.nr_rds) AS nr_ano_numero,
				   c.ds_controle_rds,
				   TO_CHAR(c.dt_inclusao, 'DD/MM/YYYY') AS dt_rds,
				   c.nr_ata,
				   c.arquivo,
				   c.arquivo_nome,
				   TO_CHAR(c.dt_reuniao, 'DD/MM/YYYY') AS dt_reuniao,
				   TO_CHAR(c.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(c.cd_usuario_inclusao) AS ds_usuario_inclusao
			  FROM gestao.controle_rds c
			 WHERE c.dt_exclusao IS NULL
			   AND (fl_restrito = 'N' OR fl_restrito = 'S' AND ".(gerencia_in(array('GRC', 'DE')) ? "1 = 1" : (trim($fl_gerente) == 'S' ? "(
			   				SELECT COUNT(*) 
			   				  FROM gestao.controle_rds_area ca
			   				 WHERE ca.dt_exclusao IS NULL
			   				   AND ca.cd_area = '".trim($cd_gerencia)."'
			   				   AND ca.cd_controle_rds = c.cd_controle_rds) > 0" : "1 = 0")).")
			   ".(trim($args['nr_rds']) != '' ? "AND c.nr_rds = ".intval($args['nr_rds']) : "")." 
			   ".(trim($args['nr_ano']) != '' ? "AND c.nr_ano = ".intval($args['nr_ano']) : "")."					 
			   ".(trim($args['nr_ata']) != '' ? "AND c.nr_ata = ".intval($args['nr_ata']) : "")."					 
			   ".(((trim($args['dt_rds_ini']) != '') AND  (trim($args['dt_rds_fim']) != '')) ? " AND DATE_TRUNC('day', c.dt_inclusao) BETWEEN TO_DATE('".$args['dt_rds_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_rds_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(((trim($args['dt_reuniao_ini']) != '') AND (trim($args['dt_reuniao_fim']) != '')) ? " AND DATE_TRUNC('day', c.dt_reuniao) BETWEEN TO_DATE('".$args['dt_reuniao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_reuniao_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args["ds_controle_rds"]) != '' ? "AND UPPER(funcoes.remove_acento(c.ds_controle_rds)) LIKE UPPER(funcoes.remove_acento('%".trim($args['ds_controle_rds'])."%'))" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }
	
	public function carrega($cd_controle_rds, $fl_md5 = 'N')
    {
        $qr_sql = "
			SELECT cd_controle_rds, 
				   nr_ata,
				   nr_rds,
				   nr_ano,
				   gestao.nr_controle_rds(nr_ano, nr_rds) AS nr_ano_numero,
				   ds_controle_rds, 
				   TO_CHAR(dt_reuniao, 'DD/MM/YYYY') AS dt_reuniao, 
				   arquivo, 
				   arquivo_nome, 
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY') AS dt_rds, 
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao, 
				   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao,
				   fl_restrito
			  FROM gestao.controle_rds
			 WHERE ".($fl_md5 == 'S' ? "MD5(cd_controle_rds::TEXT) = '".$cd_controle_rds."'" : "cd_controle_rds = ".intval($cd_controle_rds)).";";

        return $this->db->query($qr_sql)->row_array();
    }
	
	public function salvar($args)
    {
    	$cd_controle_rds = intval($this->db->get_new_id('gestao.controle_rds', 'cd_controle_rds'));
		
		$qr_sql = "
			INSERT INTO gestao.controle_rds
				 (
				   cd_controle_rds,
				   nr_rds,
				   nr_ano,
				   ds_controle_rds, 
				   nr_ata,
				   dt_reuniao,
				   arquivo, 
				   arquivo_nome,
				   fl_restrito,
				   cd_usuario_inclusao, 
				   cd_usuario_alteracao
				 )
			VALUES 
				 (
					".intval($cd_controle_rds).",
					".(trim($args['nr_rds']) != '' ? intval($args['nr_rds']) : "DEFAULT").",
					".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
					".(trim($args['ds_controle_rds']) != '' ? str_escape($args['ds_controle_rds']) : "DEFAULT").",
					".(trim($args['nr_ata']) != '' ? intval($args['nr_ata']) : "DEFAULT").",
					".(trim($args['dt_reuniao']) != '' ? "TO_DATE('".trim($args['dt_reuniao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
					".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
					".(trim($args['fl_restrito']) != '' ? "'".trim($args['fl_restrito'])."'" : "DEFAULT").",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);
		
		return $cd_controle_rds;
    }

    public function atualizar($cd_controle_rds, $args)
    {		
		$qr_sql = "
			UPDATE gestao.controle_rds
			   SET nr_ano               = ".(trim($args['nr_ano']) != '' ? intval($args['nr_ano']) : "DEFAULT").",
			       nr_ata               = ".(trim($args['nr_ata']) != '' ? intval($args['nr_ata']) : "DEFAULT").",
				   dt_reuniao           = ".(trim($args['dt_reuniao']) != '' ? "TO_DATE('".trim($args['dt_reuniao'])."', 'DD/MM/YYYY')" : "DEFAULT").",
			       ds_controle_rds      = ".(trim($args['ds_controle_rds']) != '' ? str_escape($args['ds_controle_rds']) : "DEFAULT").",
				   arquivo              = ".(trim($args['arquivo']) != '' ? "'".trim($args['arquivo'])."'" : "DEFAULT").",
				   arquivo_nome         = ".(trim($args['arquivo_nome']) != '' ? str_escape($args['arquivo_nome']) : "DEFAULT").",
				   fl_restrito          = ".(trim($args['fl_restrito']) != '' ? "'".trim($args['fl_restrito'])."'" : "DEFAULT").",
				   nr_rds               = ".(trim($args['nr_rds']) != '' ? intval($args['nr_rds']) : "DEFAULT").",
				   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
				   dt_alteracao         = CURRENT_TIMESTAMP 
			 WHERE cd_controle_rds = ".intval($cd_controle_rds).";;";

		$this->db->query($qr_sql);
    }
	
	public function excluir($cd_controle_rds, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.controle_rds
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
				   dt_exclusao         = CURRENT_TIMESTAMP 
			 WHERE cd_controle_rds = ".intval($cd_controle_rds).";";
			 
		$this->db->query($qr_sql);
	}

	public function get_gerencia_rds($cd_controle_rds)
	{
		$qr_sql = "
			SELECT cra.cd_area
			  FROM gestao.controle_rds_area cra
			 WHERE cra.cd_controle_rds  = ".intval($cd_controle_rds)."
			   AND cra.dt_exclusao IS NULL
			 ORDER BY cra.cd_area; ";

		return $this->db->query($qr_sql)->result_array();
	}	
}
?>