<?php
class App_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT COUNT(*) AS qt_participante,
                   pa.sigla AS ds_empresa,
                   pl.descricao AS ds_plano 
              FROM participantes p
              JOIN patrocinadoras pa
                ON pa.cd_empresa = p.cd_empresa
              JOIN planos pl
                ON pl.cd_plano = p.cd_plano
             WHERE funcoes.cripto_re(p.cd_empresa, p.cd_registro_empregado, p.seq_dependencia) IN (
						SELECT ap.re_cripto
					  	  FROM autoatendimento.app_log ap
					     WHERE COALESCE(ap.re_cripto, ''::text) <> ''::text 
					       ".(((trim($args['dt_ini']) != '') AND trim($args['dt_fim']) != '') ? "AND DATE_TRUNC('day', ap.dt_inclusao) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : '')."
					     GROUP BY ap.re_cripto)
             GROUP BY 2, 3
             ORDER BY 1;";

		return $this->db->query($qr_sql)->result_array();
	}

}