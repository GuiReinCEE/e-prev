<?php
class Municipio_arq_ret_model extends Model
{
	function __construct()
	{
		parent::model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT mae.cd_municipio_arq_ret,
                   mae.cd_municipio_arq_tipo,
                   mat.ds_municipio_arq_tipo,
                   p.sigla AS ds_empresa,
                   ei.descricao AS ds_empresa_integradora,
                   TO_CHAR(mae.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                   TO_CHAR(mae.dt_municipio_arq_ret, 'YYYY/MM') AS dt_municipio_arq_ret,
                   TO_CHAR(mae.dt_status, 'DD/MM/YYYY HH24:MI:SS') AS dt_status,
                   mae.ds_arquivo,
                   mae.ds_arquivo_nome,
                   CASE WHEN mae.tp_status = 'E'
                        THEN 'Encaminhado'
                        WHEN mae.tp_status = 'A'
                        THEN 'Aceito'
                        WHEN mae.tp_status = 'R'
                        THEN 'Recusado'
                        ELSE ''
                   END AS ds_status,
                   CASE WHEN mae.tp_status = 'E'
                        THEN 'label label-info'
                        WHEN mae.tp_status = 'A'
                        THEN 'label label-success'
                        WHEN mae.tp_status = 'R'
                        THEN 'label label-important'
                        ELSE ''
                   END AS ds_class_status,
                   
                   mae.tp_status,
                   mae.cd_usuario
              FROM extranet_new.municipio_arq_ret mae
              JOIN extranet_new.municipio_arq_tipo mat
                ON mat.cd_municipio_arq_tipo = mae.cd_municipio_arq_tipo
              JOIN patrocinadoras p
                ON p.cd_empresa = mae.cd_empresa
              LEFT JOIN public.empresas_integradoras ei
                ON mae.cd_empresa = ei.cd_empresa and mae.cd_empresas_integradoras = ei.empresa_integradora
             WHERE mae.dt_exclusao IS NULL
			   ".(trim($args['cd_empresa']) != '' ? "AND mae.cd_empresa = ".intval($args['cd_empresa']) : "")."
			   ".(((trim($args['dt_encaminhamento_ini']) != '') AND (trim($args['dt_encaminhamento_fim']) != '')) ? "AND DATE_TRUNC('day', mae.dt_inclusao) BETWEEN TO_DATE('".$args['dt_encaminhamento_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encaminhamento_fim']."', 'DD/MM/YYYY')" : "")."
		       ".(trim($args['tp_status']) != '' ? "AND mae.tp_status = '".trim($args['tp_status'])."'" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_empresa()
    {
        $qr_sql = "
            SELECT p.cd_empresa AS value,
			       p.sigla AS text,
			       cd_plano
			  FROM patrocinadoras p
			  JOIN planos_patrocinadoras pp
			    ON pp.cd_empresa = p.cd_empresa
			 WHERE pp.cd_plano = 10
			 ORDER BY p.sigla;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function excluir($cd_municipio_arq_ret, $cd_usuario)
    {
    	$qr_sql = "
    		UPDATE extranet_new.municipio_arq_ret
			   SET cd_usuario_exclusao = ".intval($cd_usuario).", 
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_municipio_arq_ret = ".intval($cd_municipio_arq_ret).";";

		$this->db->query($qr_sql);
    }
}