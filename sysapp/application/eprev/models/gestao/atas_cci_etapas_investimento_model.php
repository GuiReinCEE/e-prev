<?php
class atas_cci_etapas_investimento_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
	
	function listar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_atas_cci_etapas_investimento, 
			       ds_atas_cci_etapas_investimento, 
                   qt_dias, 
				   fl_dia_util, 
				   email,
				   CASE WHEN fl_dia_util = 'S' THEN 'Sim'
				        ELSE 'Não'
				   END AS dia_util
              FROM gestao.atas_cci_etapas_investimento
			 WHERE dt_exclusao IS NULL
			   ".(trim($args['ds_atas_cci_etapas_investimento']) != '' ? "AND UPPER(funcoes.remove_acento(ds_atas_cci_etapas_investimento)) LIKE UPPER(funcoes.remove_acento('%".trim($args["ds_atas_cci_etapas_investimento"])."%'))" : "")."

			   ".(trim($args['email']) != '' ? "AND UPPER(funcoes.remove_acento(email)) LIKE UPPER(funcoes.remove_acento('%".trim($args["email"])."%'))" : "").";";

        $result = $this->db->query($qr_sql);
    }
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_atas_cci_etapas_investimento,
			       ds_atas_cci_etapas_investimento,
				   qt_dias,
				   fl_dia_util,
				   ds_assunto,
				   ds_texto,
				   fl_responsavel,
				   email
			  FROM gestao.atas_cci_etapas_investimento
			 WHERE cd_atas_cci_etapas_investimento = ".intval($args['cd_atas_cci_etapas_investimento']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_atas_cci_etapas_investimento']) == 0)
		{
			$qr_sql = "
				INSERT INTO gestao.atas_cci_etapas_investimento
				     (
						ds_atas_cci_etapas_investimento, 
						qt_dias, 
						fl_dia_util, 
						ds_assunto, 
						ds_texto, 
						fl_responsavel, 
						email, 
                        cd_usuario_inclusao, 
						cd_usuario_alteracao
				     )
                VALUES 
				    (
						".(trim($args['ds_atas_cci_etapas_investimento']) != '' ? str_escape($args['ds_atas_cci_etapas_investimento']) : "DEFAULT").",
						".(trim($args['qt_dias']) != '' ? intval($args['qt_dias']) : "DEFAULT").",
						".(trim($args['fl_dia_util']) != '' ? str_escape($args['fl_dia_util']) : "DEFAULT").",
						".(trim($args['ds_assunto']) != '' ? str_escape($args['ds_assunto']) : "DEFAULT").",
						".(trim($args['ds_texto']) != '' ? str_escape($args['ds_texto']) : "DEFAULT").",
						".(trim($args['fl_responsavel']) != '' ? str_escape($args['fl_responsavel']) : "DEFAULT").",
						".(trim($args['email']) != '' ? str_escape($args['email']) : "DEFAULT").",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario'])."
					);";
		}
		else
		{
			$qr_sql = "
				UPDATE gestao.atas_cci_etapas_investimento
                   SET ds_atas_cci_etapas_investimento = ".(trim($args['ds_atas_cci_etapas_investimento']) != '' ? str_escape($args['ds_atas_cci_etapas_investimento']) : "DEFAULT").",
                       qt_dias                         = ".(trim($args['qt_dias']) != '' ? intval($args['qt_dias']) : "DEFAULT").",
					   fl_dia_util                     = ".(trim($args['fl_dia_util']) != '' ? str_escape($args['fl_dia_util']) : "DEFAULT").",
					   ds_assunto                      = ".(trim($args['ds_assunto']) != '' ? str_escape($args['ds_assunto']) : "DEFAULT").",
					   ds_texto                        = ".(trim($args['ds_texto']) != '' ? str_escape($args['ds_texto']) : "DEFAULT").",
					   fl_responsavel                  = ".(trim($args['fl_responsavel']) != '' ? str_escape($args['fl_responsavel']) : "DEFAULT").",
                       email                           = ".(trim($args['email']) != '' ? str_escape($args['email']) : "DEFAULT").",
					   cd_usuario_alteracao            = ".intval($args['cd_usuario']).",
					   dt_alteracao                    = CURRENT_TIMESTAMP
                 WHERE cd_atas_cci_etapas_investimento = ".intval($args['cd_atas_cci_etapas_investimento']).";";
		}
		
		$result = $this->db->query($qr_sql);
	}
}
?>