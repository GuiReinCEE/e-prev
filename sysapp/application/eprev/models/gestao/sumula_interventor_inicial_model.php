<?php

class Sumula_interventor_inicial_model extends Model
{
	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cd_sumula_interventor,
			       nr_sumula_interventor,
			       TO_CHAR(dt_sumula_interventor, 'DD/MM/YYYY') AS dt_sumula_interventor,
                   TO_CHAR(dt_divulgacao, 'DD/MM/YYYY') AS dt_divulgacao,
                   arquivo_pauta_nome,
  				   arquivo_pauta,
  				   arquivo_sumula_nome,
                   arquivo_sumula
			  FROM gestao.sumula_interventor_inicial
			 WHERE dt_exclusao IS NULL
			   ".(trim($args['nr_sumula_interventor']) != '' ? "AND nr_sumula_interventor = ".intval($args['nr_sumula_interventor']) : '')."
			   ".(((trim($args['dt_sumula_ini']) != '') AND (trim($args['dt_sumula_fim']) != '')) ? " AND DATE_TRUNC('day', dt_sumula_interventor) BETWEEN TO_DATE('".$args['dt_sumula_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_sumula_fim']."', 'DD/MM/YYYY')" : '')."
			   ".(((trim($args['dt_divulgacao_ini']) != '') AND (trim($args['dt_divulgacao_fim']) != '')) ? " AND DATE_TRUNC('day', dt_divulgacao) BETWEEN TO_DATE('".$args['dt_divulgacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_divulgacao_fim']."', 'DD/MM/YYYY')" : '')."
			  ORDER BY nr_sumula_interventor DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_proximo_numero()
	{
		$qr_sql = "
	        SELECT nr_sumula_interventor + 1 AS nr_sumula_interventor
	          FROM gestao.sumula_interventor_inicial
	         WHERE dt_exclusao IS NULL
	         ORDER BY nr_sumula_interventor DESC 
	         LIMIT 1;";

      	return $this->db->query($qr_sql)->row_array();
	}

	public function carrega($cd_sumula_interventor)
	{
		$qr_sql = "
			SELECT cd_sumula_interventor,
			       nr_sumula_interventor,
			       TO_CHAR(dt_sumula_interventor, 'DD/MM/YYYY') AS dt_sumula_interventor,
                   TO_CHAR(dt_divulgacao, 'DD/MM/YYYY') AS dt_divulgacao,
                   TO_CHAR(dt_publicacao_libera, 'DD/MM/YYYY') AS dt_publicacao_libera,
                   TO_CHAR(dt_publicacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_publicacao,
                   funcoes.get_usuario_nome(cd_usuario_publicacao) AS ds_usuario_publicacao,			
                   arquivo_pauta_nome,
  				   arquivo_pauta,
  				   arquivo_sumula_nome,
                   arquivo_sumula
			  FROM gestao.sumula_interventor_inicial
			 WHERE cd_sumula_interventor = ".intval($cd_sumula_interventor).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function valida_numero_sumula($cd_sumula_interventor, $nr_sumula_interventor)
    {
      	$qr_sql = "
	        SELECT COUNT(*) AS valida
	          FROM gestao.sumula_interventor_inicial
	         WHERE dt_exclusao IS NULL
	           AND nr_sumula_interventor = ".intval($nr_sumula_interventor)." 
	           AND cd_sumula_interventor != ".intval($cd_sumula_interventor).";";

  		return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
    	$cd_sumula_interventor = intval($this->db->get_new_id('gestao.sumula_interventor_inicial', 'cd_sumula_interventor'));

    	$qr_sql = "
            INSERT INTO gestao.sumula_interventor_inicial
                 (
                   cd_sumula_interventor,
                   nr_sumula_interventor,
                   dt_sumula_interventor,
                   dt_divulgacao,
                   arquivo_pauta_nome,
  				   arquivo_pauta,
  				   arquivo_sumula_nome,
                   arquivo_sumula,
                   cd_usuario_inclusao,
                   cd_usuario_alteracao
                 )
            VALUES
                 (
                   ".intval($cd_sumula_interventor).",
                   ".(intval($args['nr_sumula_interventor']) > 0 ? intval($args['nr_sumula_interventor']) : "DEFAULT").",
                   ".(trim($args['dt_sumula_interventor']) != '' ? "TO_DATE('".$args['dt_sumula_interventor']."', 'DD/MM/YYYY')" :  "DEFAULT").",
                   ".(trim($args['dt_divulgacao']) != '' ? "TO_DATE('".$args['dt_divulgacao']."', 'DD/MM/YYYY')" :  "DEFAULT").",
                   ".(trim($args['arquivo_pauta_nome']) != '' ? "'".trim($args['arquivo_pauta_nome'])."'" :  "DEFAULT").",
                   ".(trim($args['arquivo_pauta']) != '' ? "'".trim($args['arquivo_pauta'])."'" :  "DEFAULT").",
                   ".(trim($args['arquivo_sumula_nome']) != '' ? "'".trim($args['arquivo_sumula_nome'])."'" :  "DEFAULT").",
                   ".(trim($args['arquivo_sumula']) != '' ? "'".trim($args['arquivo_sumula'])."'" :  "DEFAULT").",
                   ".intval($args['cd_usuario']).",
                   ".intval($args['cd_usuario'])." 
                 )";
            
        $this->db->query($qr_sql);

        return $cd_sumula_interventor;
    }

    public function atualizar($cd_sumula_interventor, $args = array())
    {
    	$qr_sql = "
            UPDATE gestao.sumula_interventor_inicial
               SET nr_sumula_interventor = ".(intval($args['nr_sumula_interventor']) > 0 ? intval($args['nr_sumula_interventor']) : "DEFAULT").",
                   dt_sumula_interventor = ".(trim($args['dt_sumula_interventor']) != '' ? "TO_DATE('".$args['dt_sumula_interventor']."', 'DD/MM/YYYY')" :  "DEFAULT").",
                   dt_divulgacao         = ".(trim($args['dt_divulgacao']) != '' ? "TO_DATE('".$args['dt_divulgacao']."', 'DD/MM/YYYY')" :  "DEFAULT").",
                   arquivo_pauta_nome    = ".(trim($args['arquivo_pauta_nome']) != '' ? "'".trim($args['arquivo_pauta_nome'])."'" :  "DEFAULT").",
  				   arquivo_pauta         = ".(trim($args['arquivo_pauta']) != '' ? "'".trim($args['arquivo_pauta'])."'" :  "DEFAULT").",
  				   arquivo_sumula_nome   = ".(trim($args['arquivo_sumula_nome']) != '' ? "'".trim($args['arquivo_sumula_nome'])."'" :  "DEFAULT").",
                   arquivo_sumula        = ".(trim($args['arquivo_sumula']) != '' ? "'".trim($args['arquivo_sumula'])."'" :  "DEFAULT").",
                   cd_usuario_alteracao  = ".(trim($args['cd_usuario']) != '' ? intval($args['cd_usuario']) : "DEFAULT")." ,
                   dt_alteracao          = CURRENT_TIMESTAMP
             WHERE cd_sumula_interventor = ".intval($cd_sumula_interventor);
        
        $this->db->query($qr_sql);
    }

    public function publicar($cd_sumula_interventor, $cd_usuario, $dt_publicacao_libera = '')
    {
        $qr_sql = "
			UPDATE gestao.sumula_interventor_inicial
			   SET dt_publicacao_libera  = ".(trim($dt_publicacao_libera) == '' ? "NULL" : "TO_DATE('".$dt_publicacao_libera."','DD/MM/YYYY')").",
				   dt_publicacao         = ".(trim($dt_publicacao_libera) == '' ? "NULL" : "CURRENT_TIMESTAMP").",
				   cd_usuario_publicacao = ".(trim($dt_publicacao_libera) == '' ? "NULL" : intval($cd_usuario)).",
                   cd_usuario_alteracao  = ".intval($cd_usuario).",
                   dt_alteracao          = CURRENT_TIMESTAMP 
			 WHERE cd_sumula_interventor = ".intval($cd_sumula_interventor).";";

        $this->db->query($qr_sql);
	}	
}