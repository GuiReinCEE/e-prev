<?php
class Financeiro_inadimplencia_previdenciaria_model extends Model
{
	function __construct()
	{
		parent::Model();
    }

    public function listar($cd_indicador_tabela)
    {
        $qr_sql = "
            SELECT cd_financeiro_inadimplencia_previdenciaria,
                   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
                   ds_observacao,
                   fl_media,
                   ds_tabela,
                   nr_carga_resultado,
                   nr_inadimplencia_resultado,
                   nr_meta_resultado,
                   nr_resultado_resultado
              FROM indicador_plugin.financeiro_inadimplencia_previdenciaria
             WHERE dt_exclusao                            IS NULL
               AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
             ORDER BY dt_referencia ASC;";
        
        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_referencia()
    {
        $qr_sql = "
            SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
                   cd_indicador_tabela,
                   nr_meta_ceee,
                   nr_meta_cgtee,
                   nr_meta_rge,
                   nr_meta_rgesul,
                   nr_meta_ceeemigrado,
                   nr_meta_fundacaomigrado,
                   nr_meta_ceeenovos,
                   nr_meta_fundacaonovos,
                   nr_meta_crm,
                   nr_meta_inpel,
                   nr_meta_foz,
                   nr_meta_ceran
			  FROM indicador_plugin.financeiro_inadimplencia_previdenciaria
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
             LIMIT 1;";
             
        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega($cd_financeiro_inadimplencia_previdenciaria)
    {
        $qr_sql = "
            SELECT cd_financeiro_inadimplencia_previdenciaria,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   ds_observacao,

                   nr_carga_ceee,
                   nr_inadimplencia_ceee,
                   nr_meta_ceee,

                   nr_carga_cgtee,
                   nr_inadimplencia_cgtee,
                   nr_meta_cgtee,

                   nr_carga_rge,
                   nr_inadimplencia_rge,
                   nr_meta_rge,

                   nr_carga_rgesul,
                   nr_inadimplencia_rgesul,
                   nr_meta_rgesul,

                   nr_carga_ceeemigrado,
                   nr_inadimplencia_ceeemigrado,
                   nr_meta_ceeemigrado,

                   nr_carga_fundacaomigrado,
                   nr_inadimplencia_fundacaomigrado,
                   nr_meta_fundacaomigrado,

                   nr_carga_ceeenovos,
                   nr_inadimplencia_ceeenovos,
                   nr_meta_ceeenovos,

                   nr_carga_fundacaonovos,
                   nr_inadimplencia_fundacaonovos,
                   nr_meta_fundacaonovos,

                   nr_carga_crm,
                   nr_inadimplencia_crm,
                   nr_meta_crm,

                   nr_carga_inpel,
                   nr_inadimplencia_inpel,
                   nr_meta_inpel,

                   nr_carga_foz,
                   nr_inadimplencia_foz,
                   nr_meta_foz,

                   nr_carga_ceran,
                   nr_inadimplencia_ceran,
                   nr_meta_ceran
              FROM indicador_plugin.financeiro_inadimplencia_previdenciaria
             WHERE cd_financeiro_inadimplencia_previdenciaria = ".intval($cd_financeiro_inadimplencia_previdenciaria)."
               AND dt_exclusao                                IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }
    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO indicador_plugin.financeiro_inadimplencia_previdenciaria
            (
                cd_indicador_tabela, 
                dt_referencia, 
                ds_observacao,
                fl_media,
                ds_tabela,

                nr_carga_ceee,
                nr_inadimplencia_ceee,
                nr_meta_ceee,
                nr_percentual_ceee,
                nr_ponderada_ceee,
                nr_resultado_ceee,
            
                nr_carga_cgtee,
                nr_inadimplencia_cgtee,
                nr_meta_cgtee,
                nr_percentual_cgtee,
                nr_ponderada_cgtee,
                nr_resultado_cgtee,
            
                nr_carga_rge,
                nr_inadimplencia_rge,
                nr_meta_rge,
                nr_percentual_rge,
                nr_ponderada_rge,
                nr_resultado_rge,
            
                nr_carga_rgesul,
                nr_inadimplencia_rgesul,
                nr_meta_rgesul,
                nr_percentual_rgesul,
                nr_ponderada_rgesul,
                nr_resultado_rgesul,
            
                nr_carga_ceeemigrado,
                nr_inadimplencia_ceeemigrado,
                nr_meta_ceeemigrado,
                nr_percentual_ceeemigrado,
                nr_ponderado_ceeemigrado,
                nr_resultado_ceeemigrado,
            
                nr_carga_fundacaomigrado,
                nr_inadimplencia_fundacaomigrado,
                nr_meta_fundacaomigrado,
                nr_percentual_fundacaomigrado,
                nr_ponderada_fundacaomigrado,
                nr_resultado_fundacaomigrado,
            
                nr_carga_ceeenovos,
                nr_inadimplencia_ceeenovos,
                nr_meta_ceeenovos,
                nr_percentual_ceeenovos,
                nr_ponderada_ceeenovos,
                nr_resultado_ceeenovos,
            
                nr_carga_fundacaonovos,
                nr_inadimplencia_fundacaonovos,
                nr_meta_fundacaonovos,
                nr_percentual_fundacaonovos,
                nr_ponderada_fundacaonovos,
                nr_resultado_fundacaonovos,
            
                nr_carga_crm,
                nr_inadimplencia_crm,
                nr_meta_crm,
                nr_percentual_crm,
                nr_ponderada_crm,
                nr_resultado_crm,
            
                nr_carga_inpel,
                nr_inadimplencia_inpel,
                nr_meta_inpel,
                nr_percentual_inpel,
                nr_ponderada_inpel,
                nr_resultado_inpel,
            
                nr_carga_foz,
                nr_inadimplencia_foz,
                nr_meta_foz,
                nr_percentual_foz,
                nr_ponderada_foz,
                nr_resultado_foz,
            
                nr_carga_ceran,
                nr_inadimplencia_ceran,
                nr_meta_ceran,
                nr_percentual_ceran,
                nr_ponderada_ceran,
                nr_resultado_ceran,
                
                nr_carga_resultado,
                nr_inadimplencia_resultado,
                nr_meta_resultado,
                nr_percentual_resultado,
                nr_ponderada_resultado,
                nr_resultado_resultado,

                cd_usuario_inclusao,
                cd_usuario_alteracao
            ) 
        VALUES 
            ( 
                ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
                ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
                ".(trim($args['ds_tabela']) != '' ? str_escape($args['ds_tabela']) : "DEFAULT").",

                ".(trim($args['nr_carga_ceee']) != 0 ? floatval($args['nr_carga_ceee']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_ceee']) != 0 ? floatval($args['nr_inadimplencia_ceee']) : "DEFAULT").",
                ".(trim($args['nr_meta_ceee']) != 0 ? floatval($args['nr_meta_ceee']) : "DEFAULT").",
                ".(trim($args['nr_percentual_ceee']) != 0 ? floatval($args['nr_percentual_ceee']) : "DEFAULT").",
                ".(trim($args['nr_ponderada_ceee']) != 0 ? floatval($args['nr_ponderada_ceee']) : "DEFAULT").",
                ".(trim($args['nr_resultado_ceee']) != 0 ? floatval($args['nr_resultado_ceee']) : "DEFAULT").",

                ".(trim($args['nr_carga_cgtee']) != 0 ? floatval($args['nr_carga_cgtee']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_cgtee']) != 0 ? floatval($args['nr_inadimplencia_cgtee']) : "DEFAULT").",
                ".(trim($args['nr_meta_cgtee']) != 0 ? floatval($args['nr_meta_cgtee']) : "DEFAULT").",
                ".(trim($args['nr_percentual_cgtee']) != 0 ? floatval($args['nr_percentual_cgtee']) : "DEFAULT").",
                ".(trim($args['nr_ponderada_cgtee']) != 0 ? floatval($args['nr_ponderada_cgtee']) : "DEFAULT").",
                ".(trim($args['nr_resultado_cgtee']) != 0 ? floatval($args['nr_resultado_cgtee']) : "DEFAULT").",

                ".(trim($args['nr_carga_rge']) != 0 ? floatval($args['nr_carga_rge']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_rge']) != 0 ? floatval($args['nr_inadimplencia_rge']) : "DEFAULT").",
                ".(trim($args['nr_meta_rge']) != 0 ? floatval($args['nr_meta_rge']) : "DEFAULT").",
                ".(trim($args['nr_percentual_rge']) != 0 ? floatval($args['nr_percentual_rge']) : "DEFAULT").",
                ".(trim($args['nr_ponderada_rge']) != 0 ? floatval($args['nr_ponderada_rge']) : "DEFAULT").",
                ".(trim($args['nr_resultado_rge']) != 0 ? floatval($args['nr_resultado_rge']) : "DEFAULT").",

                ".(trim($args['nr_carga_rgesul']) != 0 ? floatval($args['nr_carga_rgesul']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_rgesul']) != 0 ? floatval($args['nr_inadimplencia_rgesul']) : "DEFAULT").",
                ".(trim($args['nr_meta_rgesul']) != 0 ? floatval($args['nr_meta_rgesul']) : "DEFAULT").",
                ".(trim($args['nr_percentual_rgesul']) != 0 ? floatval($args['nr_percentual_rgesul']) : "DEFAULT").",
                ".(trim($args['nr_ponderada_rgesul']) != 0 ? floatval($args['nr_ponderada_rgesul']) : "DEFAULT").",
                ".(trim($args['nr_resultado_rgesul']) != 0 ? floatval($args['nr_resultado_rgesul']) : "DEFAULT").",

                ".(trim($args['nr_carga_ceeemigrado']) != 0 ? floatval($args['nr_carga_ceeemigrado']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_ceeemigrado']) != 0 ? floatval($args['nr_inadimplencia_ceeemigrado']) : "DEFAULT").",
                ".(trim($args['nr_meta_ceeemigrado']) != 0 ? floatval($args['nr_meta_ceeemigrado']) : "DEFAULT").",
                ".(trim($args['nr_percentual_ceeemigrado']) != 0 ? floatval($args['nr_percentual_ceeemigrado']) : "DEFAULT").",
                ".(trim($args['nr_ponderado_ceeemigrado']) != 0 ? floatval($args['nr_ponderado_ceeemigrado']) : "DEFAULT").",
                ".(trim($args['nr_resultado_ceeemigrado']) != 0 ? floatval($args['nr_resultado_ceeemigrado']) : "DEFAULT").",

                ".(trim($args['nr_carga_fundacaomigrado']) != 0 ? floatval($args['nr_carga_fundacaomigrado']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_fundacaomigrado']) != 0 ? floatval($args['nr_inadimplencia_fundacaomigrado']) : "DEFAULT").",
                ".(trim($args['nr_meta_fundacaomigrado']) != 0 ? floatval($args['nr_meta_fundacaomigrado']) : "DEFAULT").",
                ".(trim($args['nr_percentual_fundacaomigrado']) != 0 ? floatval($args['nr_percentual_fundacaomigrado']) : "DEFAULT").",
                ".(trim($args['nr_ponderada_fundacaomigrado']) != 0 ? floatval($args['nr_ponderada_fundacaomigrado']) : "DEFAULT").",
                ".(trim($args['nr_resultado_fundacaomigrado']) != 0 ? floatval($args['nr_resultado_fundacaomigrado']) : "DEFAULT").",

                ".(trim($args['nr_carga_ceeenovos']) != 0 ? floatval($args['nr_carga_ceeenovos']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_ceeenovos']) != 0 ? floatval($args['nr_inadimplencia_ceeenovos']) : "DEFAULT").",
                ".(trim($args['nr_meta_ceeenovos']) != 0 ? floatval($args['nr_meta_ceeenovos']) : "DEFAULT").",
                ".(trim($args['nr_percentual_ceeenovos']) != 0 ? floatval($args['nr_percentual_ceeenovos']) : "DEFAULT").",
                ".(trim($args['nr_ponderada_ceeenovos']) != 0 ? floatval($args['nr_ponderada_ceeenovos']) : "DEFAULT").",
                ".(trim($args['nr_resultado_ceeenovos']) != 0 ? floatval($args['nr_resultado_ceeenovos']) : "DEFAULT").",

                ".(trim($args['nr_carga_fundacaonovos']) != 0 ? floatval($args['nr_carga_fundacaonovos']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_fundacaonovos']) != 0 ? floatval($args['nr_inadimplencia_fundacaonovos']) : "DEFAULT").",
                ".(trim($args['nr_meta_fundacaonovos']) != 0 ? floatval($args['nr_meta_fundacaonovos']) : "DEFAULT").",
                ".(trim($args['nr_percentual_fundacaonovos']) != 0 ? floatval($args['nr_percentual_fundacaonovos']) : "DEFAULT").",
                ".(trim($args['nr_ponderada_fundacaonovos']) != 0 ? floatval($args['nr_ponderada_fundacaonovos']) : "DEFAULT").",
                ".(trim($args['nr_resultado_fundacaonovos']) != 0 ? floatval($args['nr_resultado_fundacaonovos']) : "DEFAULT").",

                ".(trim($args['nr_carga_crm']) != 0 ? floatval($args['nr_carga_crm']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_crm']) != 0 ? floatval($args['nr_inadimplencia_crm']) : "DEFAULT").",
                ".(trim($args['nr_meta_crm']) != 0 ? floatval($args['nr_meta_crm']) : "DEFAULT").",
                ".(trim($args['nr_percentual_crm']) != 0 ? floatval($args['nr_percentual_crm']) : "DEFAULT").",
                ".(trim($args['nr_ponderada_crm']) != 0 ? floatval($args['nr_ponderada_crm']) : "DEFAULT").",
                ".(trim($args['nr_resultado_crm']) != 0 ? floatval($args['nr_resultado_crm']) : "DEFAULT").",

                ".(trim($args['nr_carga_inpel']) != 0 ? floatval($args['nr_carga_inpel']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_inpel']) != 0 ? floatval($args['nr_inadimplencia_inpel']) : "DEFAULT").",
                ".(trim($args['nr_meta_inpel']) != 0 ? floatval($args['nr_meta_inpel']) : "DEFAULT").",
                ".(trim($args['nr_percentual_inpel']) != 0 ? floatval($args['nr_percentual_inpel']) : "DEFAULT").",
                ".(trim($args['nr_ponderada_inpel']) != 0 ? floatval($args['nr_ponderada_inpel']) : "DEFAULT").",
                ".(trim($args['nr_resultado_inpel']) != 0 ? floatval($args['nr_resultado_inpel']) : "DEFAULT").",

                ".(trim($args['nr_carga_foz']) != 0 ? floatval($args['nr_carga_foz']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_foz']) != 0 ? floatval($args['nr_inadimplencia_foz']) : "DEFAULT").",
                ".(trim($args['nr_meta_foz']) != 0 ? floatval($args['nr_meta_foz']) : "DEFAULT").",
                ".(trim($args['nr_percentual_foz']) != 0 ? floatval($args['nr_percentual_foz']) : "DEFAULT").",
                ".(trim($args['nr_ponderada_foz']) != 0 ? floatval($args['nr_ponderada_foz']) : "DEFAULT").",
                ".(trim($args['nr_resultado_foz']) != 0 ? floatval($args['nr_resultado_foz']) : "DEFAULT").",

                ".(trim($args['nr_carga_ceran']) != 0 ? floatval($args['nr_carga_ceran']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_ceran']) != 0 ? floatval($args['nr_inadimplencia_ceran']) : "DEFAULT").",
                ".(trim($args['nr_meta_ceran']) != 0 ? floatval($args['nr_meta_ceran']) : "DEFAULT").",
                ".(trim($args['nr_percentual_ceran']) != 0 ? floatval($args['nr_percentual_ceran']) : "DEFAULT").",
                ".(trim($args['nr_ponderada_ceran']) != 0 ? floatval($args['nr_ponderada_ceran']) : "DEFAULT").",
                ".(trim($args['nr_resultado_ceran']) != 0 ? floatval($args['nr_resultado_ceran']) : "DEFAULT").",

                ".(trim($args['nr_carga_resultado']) != 0 ? floatval($args['nr_carga_resultado']) : "DEFAULT").",
                ".(trim($args['nr_inadimplencia_resultado']) != 0 ? floatval($args['nr_inadimplencia_resultado']) : "DEFAULT").",
                ".(trim($args['nr_meta_resultado']) != 0 ? floatval($args['nr_meta_resultado']) : "DEFAULT").",
                ".(trim($args['nr_percentual_resultado']) != 0 ? floatval($args['nr_percentual_resultado']) : "DEFAULT").",
                ".(trim($args['nr_ponderada_resultado']) != 0 ? floatval($args['nr_ponderada_resultado']) : "DEFAULT").",
                ".(trim($args['nr_resultado_resultado']) != 0 ? floatval($args['nr_resultado_resultado']) : "DEFAULT").",

                ".intval($args['cd_usuario']).",
                ".intval($args['cd_usuario'])."
            );";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_financeiro_inadimplencia_previdenciaria, $args = array())
    {
        $qr_sql = "
            UPDATE indicador_plugin.financeiro_inadimplencia_previdenciaria 
               SET dt_referencia                    = ".(trim($args['dt_referencia']) != '' ?  "TO_DATE('".trim($args['dt_referencia'])."',               'DD/MM/YYYY')" : "DEFAULT").", 
                   ds_observacao                    = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                   ds_tabela                        = ".(trim($args['ds_tabela']) != '' ? str_escape($args['ds_tabela']) : "DEFAULT").",

                   nr_carga_ceee                    = ".(trim($args['nr_carga_ceee']) != 0 ? floatval($args['nr_carga_ceee']) : "DEFAULT").", 
                   nr_inadimplencia_ceee            = ".(trim($args['nr_inadimplencia_ceee']) != 0 ? floatval($args['nr_inadimplencia_ceee']) : "DEFAULT").", 
                   nr_meta_ceee                     = ".(trim($args['nr_meta_ceee']) != 0 ? floatval($args['nr_meta_ceee']) : "DEFAULT").", 
                   nr_percentual_ceee               = ".(trim($args['nr_percentual_ceee']) != 0 ? floatval($args['nr_percentual_ceee']) : "DEFAULT").",
                   nr_ponderada_ceee                = ".(trim($args['nr_ponderada_ceee']) != 0 ? floatval($args['nr_ponderada_ceee']) : "DEFAULT").", 
                   nr_resultado_ceee                = ".(trim($args['nr_resultado_ceee']) != 0 ? floatval($args['nr_resultado_ceee']) : "DEFAULT").", 
                        
                   nr_carga_cgtee                   = ".(trim($args['nr_carga_cgtee']) != 0 ? floatval($args['nr_carga_cgtee']) : "DEFAULT").", 
                   nr_inadimplencia_cgtee           = ".(trim($args['nr_inadimplencia_cgtee']) != 0 ? floatval($args['nr_inadimplencia_cgtee']) : "DEFAULT").", 
                   nr_meta_cgtee                    = ".(trim($args['nr_meta_cgtee']) != 0 ? floatval($args['nr_meta_cgtee']) : "DEFAULT").", 
                   nr_percentual_cgtee              = ".(trim($args['nr_percentual_cgtee']) != 0 ? floatval($args['nr_percentual_cgtee']) : "DEFAULT").",
                   nr_ponderada_cgtee               = ".(trim($args['nr_ponderada_cgtee']) != 0 ? floatval($args['nr_ponderada_cgtee']) : "DEFAULT").", 
                   nr_resultado_cgtee               = ".(trim($args['nr_resultado_cgtee']) != 0 ? floatval($args['nr_resultado_cgtee']) : "DEFAULT").", 

                   nr_carga_rge                     = ".(trim($args['nr_carga_rge']) != 0 ? floatval($args['nr_carga_rge']) : "DEFAULT").", 
                   nr_inadimplencia_rge             = ".(trim($args['nr_inadimplencia_rge']) != 0 ? floatval($args['nr_inadimplencia_rge']) : "DEFAULT").", 
                   nr_meta_rge                      = ".(trim($args['nr_meta_rge']) != 0 ? floatval($args['nr_meta_rge']) : "DEFAULT").", 
                   nr_percentual_rge                = ".(trim($args['nr_percentual_rge']) != 0 ? floatval($args['nr_percentual_rge']) : "DEFAULT").",
                   nr_ponderada_rge                 = ".(trim($args['nr_ponderada_rge']) != 0 ? floatval($args['nr_ponderada_rge']) : "DEFAULT").", 
                   nr_resultado_rge                 = ".(trim($args['nr_resultado_rge']) != 0 ? floatval($args['nr_resultado_rge']) : "DEFAULT").", 

                   nr_carga_rgesul                  = ".(trim($args['nr_carga_rgesul']) != 0 ? floatval($args['nr_carga_rgesul']) : "DEFAULT").", 
                   nr_inadimplencia_rgesul          = ".(trim($args['nr_inadimplencia_rgesul']) != 0 ? floatval($args['nr_inadimplencia_rgesul']) : "DEFAULT").", 
                   nr_meta_rgesul                   = ".(trim($args['nr_meta_rgesul']) != 0 ? floatval($args['nr_meta_rgesul']) : "DEFAULT").", 
                   nr_percentual_rgesul             = ".(trim($args['nr_percentual_rgesul']) != 0 ? floatval($args['nr_percentual_rgesul']) : "DEFAULT").",
                   nr_ponderada_rgesul              = ".(trim($args['nr_ponderada_rgesul']) != 0 ? floatval($args['nr_ponderada_rgesul']) : "DEFAULT").", 
                   nr_resultado_rgesul              = ".(trim($args['nr_resultado_rgesul']) != 0 ? floatval($args['nr_resultado_rgesul']) : "DEFAULT").", 

                   nr_carga_ceeemigrado             = ".(trim($args['nr_carga_ceeemigrado']) != 0 ? floatval($args['nr_carga_ceeemigrado']) : "DEFAULT").", 
                   nr_inadimplencia_ceeemigrado     = ".(trim($args['nr_inadimplencia_ceeemigrado']) != 0 ? floatval($args['nr_inadimplencia_ceeemigrado']) : "DEFAULT").", 
                   nr_meta_ceeemigrado              = ".(trim($args['nr_meta_ceeemigrado']) != 0 ? floatval($args['nr_meta_ceeemigrado']) : "DEFAULT").", 
                   nr_percentual_ceeemigrado        = ".(trim($args['nr_percentual_ceeemigrado']) != 0 ? floatval($args['nr_percentual_ceeemigrado']) : "DEFAULT").",
                   nr_ponderado_ceeemigrado         = ".(trim($args['nr_ponderado_ceeemigrado']) != 0 ? floatval($args['nr_ponderado_ceeemigrado']) : "DEFAULT").", 
                   nr_resultado_ceeemigrado         = ".(trim($args['nr_resultado_ceeemigrado']) != 0 ? floatval($args['nr_resultado_ceeemigrado']) : "DEFAULT").", 

                   nr_carga_fundacaomigrado         = ".(trim($args['nr_carga_fundacaomigrado']) != 0 ? floatval($args['nr_carga_fundacaomigrado']) : "DEFAULT").", 
                   nr_inadimplencia_fundacaomigrado = ".(trim($args['nr_inadimplencia_fundacaomigrado']) != 0 ? floatval($args['nr_inadimplencia_fundacaomigrado']) : "DEFAULT").", 
                   nr_meta_fundacaomigrado          = ".(trim($args['nr_meta_fundacaomigrado']) != 0 ? floatval($args['nr_meta_fundacaomigrado']) : "DEFAULT").", 
                   nr_percentual_fundacaomigrado    = ".(trim($args['nr_percentual_fundacaomigrado']) != 0 ? floatval($args['nr_percentual_fundacaomigrado']) : "DEFAULT").",
                   nr_ponderada_fundacaomigrado     = ".(trim($args['nr_ponderada_fundacaomigrado']) != 0 ? floatval($args['nr_ponderada_fundacaomigrado']) : "DEFAULT").", 
                   nr_resultado_fundacaomigrado     = ".(trim($args['nr_resultado_fundacaomigrado']) != 0 ? floatval($args['nr_resultado_fundacaomigrado']) : "DEFAULT").", 

                   nr_carga_ceeenovos               = ".(trim($args['nr_carga_ceeenovos']) != 0 ? floatval($args['nr_carga_ceeenovos']) : "DEFAULT").", 
                   nr_inadimplencia_ceeenovos       = ".(trim($args['nr_inadimplencia_ceeenovos']) != 0 ? floatval($args['nr_inadimplencia_ceeenovos']) : "DEFAULT").", 
                   nr_meta_ceeenovos                = ".(trim($args['nr_meta_ceeenovos']) != 0 ? floatval($args['nr_meta_ceeenovos']) : "DEFAULT").", 
                   nr_percentual_ceeenovos          = ".(trim($args['nr_percentual_ceeenovos']) != 0 ? floatval($args['nr_percentual_ceeenovos']) : "DEFAULT").",
                   nr_ponderada_ceeenovos           = ".(trim($args['nr_ponderada_ceeenovos']) != 0 ? floatval($args['nr_ponderada_ceeenovos']) : "DEFAULT").", 
                   nr_resultado_ceeenovos           = ".(trim($args['nr_resultado_ceeenovos']) != 0 ? floatval($args['nr_resultado_ceeenovos']) : "DEFAULT").", 

                   nr_carga_fundacaonovos           = ".(trim($args['nr_carga_fundacaonovos']) != 0 ? floatval($args['nr_carga_fundacaonovos']) : "DEFAULT").", 
                   nr_inadimplencia_fundacaonovos   = ".(trim($args['nr_inadimplencia_fundacaonovos']) != 0 ? floatval($args['nr_inadimplencia_fundacaonovos']) : "DEFAULT").", 
                   nr_meta_fundacaonovos            = ".(trim($args['nr_meta_fundacaonovos']) != 0 ? floatval($args['nr_meta_fundacaonovos']) : "DEFAULT").", 
                   nr_percentual_fundacaonovos      = ".(trim($args['nr_percentual_fundacaonovos']) != 0 ? floatval($args['nr_percentual_fundacaonovos']) : "DEFAULT").",
                   nr_ponderada_fundacaonovos       = ".(trim($args['nr_ponderada_fundacaonovos']) != 0 ? floatval($args['nr_ponderada_fundacaonovos']) : "DEFAULT").", 
                   nr_resultado_fundacaonovos       = ".(trim($args['nr_resultado_fundacaonovos']) != 0 ? floatval($args['nr_resultado_fundacaonovos']) : "DEFAULT").", 

                   nr_carga_crm                     = ".(trim($args['nr_carga_crm']) != 0 ? floatval($args['nr_carga_crm']) : "DEFAULT").", 
                   nr_inadimplencia_crm             = ".(trim($args['nr_inadimplencia_crm']) != 0 ? floatval($args['nr_inadimplencia_crm']) : "DEFAULT").", 
                   nr_meta_crm                      = ".(trim($args['nr_meta_crm']) != 0 ? floatval($args['nr_meta_crm']) : "DEFAULT").", 
                   nr_percentual_crm                = ".(trim($args['nr_percentual_crm']) != 0 ? floatval($args['nr_percentual_crm']) : "DEFAULT").",
                   nr_ponderada_crm                 = ".(trim($args['nr_ponderada_crm']) != 0 ? floatval($args['nr_ponderada_crm']) : "DEFAULT").", 
                   nr_resultado_crm                 = ".(trim($args['nr_resultado_crm']) != 0 ? floatval($args['nr_resultado_crm']) : "DEFAULT").", 

                   nr_carga_inpel                   = ".(trim($args['nr_carga_inpel']) != 0 ? floatval($args['nr_carga_inpel']) : "DEFAULT").", 
                   nr_inadimplencia_inpel           = ".(trim($args['nr_inadimplencia_inpel']) != 0 ? floatval($args['nr_inadimplencia_inpel']) : "DEFAULT").", 
                   nr_meta_inpel                    = ".(trim($args['nr_meta_inpel']) != 0 ? floatval($args['nr_meta_inpel']) : "DEFAULT").", 
                   nr_percentual_inpel              = ".(trim($args['nr_percentual_inpel']) != 0 ? floatval($args['nr_percentual_inpel']) : "DEFAULT").",
                   nr_ponderada_inpel               = ".(trim($args['nr_ponderada_inpel']) != 0 ? floatval($args['nr_ponderada_inpel']) : "DEFAULT").", 
                   nr_resultado_inpel               = ".(trim($args['nr_resultado_inpel']) != 0 ? floatval($args['nr_resultado_inpel']) : "DEFAULT").", 

                   nr_carga_foz                     = ".(trim($args['nr_carga_foz']) != 0 ? floatval($args['nr_carga_foz']) : "DEFAULT").", 
                   nr_inadimplencia_foz             = ".(trim($args['nr_inadimplencia_foz']) != 0 ? floatval($args['nr_inadimplencia_foz']) : "DEFAULT").", 
                   nr_meta_foz                      = ".(trim($args['nr_meta_foz']) != 0 ? floatval($args['nr_meta_foz']) : "DEFAULT").", 
                   nr_percentual_foz                = ".(trim($args['nr_percentual_foz']) != 0 ? floatval($args['nr_percentual_foz']) : "DEFAULT").",
                   nr_ponderada_foz                 = ".(trim($args['nr_ponderada_foz']) != 0 ? floatval($args['nr_ponderada_foz']) : "DEFAULT").", 
                   nr_resultado_foz                 = ".(trim($args['nr_resultado_foz']) != 0 ? floatval($args['nr_resultado_foz']) : "DEFAULT").", 

                   nr_carga_ceran                   = ".(trim($args['nr_carga_ceran']) != 0 ? floatval($args['nr_carga_ceran']) : "DEFAULT").", 
                   nr_inadimplencia_ceran           = ".(trim($args['nr_inadimplencia_ceran']) != 0 ? floatval($args['nr_inadimplencia_ceran']) : "DEFAULT").", 
                   nr_meta_ceran                    = ".(trim($args['nr_meta_ceran']) != 0 ? floatval($args['nr_meta_ceran']) : "DEFAULT").", 
                   nr_percentual_ceran              = ".(trim($args['nr_percentual_ceran']) != 0 ? floatval($args['nr_percentual_ceran']) : "DEFAULT").",
                   nr_ponderada_ceran               = ".(trim($args['nr_ponderada_ceran']) != 0 ? floatval($args['nr_ponderada_ceran']) : "DEFAULT").", 
                   nr_resultado_ceran               = ".(trim($args['nr_resultado_ceran']) != 0 ? floatval($args['nr_resultado_ceran']) : "DEFAULT").", 

                   nr_carga_resultado               = ".(trim($args['nr_carga_resultado']) != 0 ? floatval($args['nr_carga_resultado']) : "DEFAULT").", 
                   nr_inadimplencia_resultado       = ".(trim($args['nr_inadimplencia_resultado']) != 0 ? floatval($args['nr_inadimplencia_resultado']) : "DEFAULT").", 
                   nr_meta_resultado                = ".(trim($args['nr_meta_resultado']) != 0 ? floatval($args['nr_meta_resultado']) : "DEFAULT").", 
                   nr_percentual_resultado          = ".(trim($args['nr_percentual_resultado']) != 0 ? floatval($args['nr_percentual_resultado']) : "DEFAULT").",
                   nr_ponderada_resultado           = ".(trim($args['nr_ponderada_resultado']) != 0 ? floatval($args['nr_ponderada_resultado']) : "DEFAULT").", 
                   nr_resultado_resultado           = ".(trim($args['nr_resultado_resultado']) != 0 ? floatval($args['nr_resultado_resultado']) : "DEFAULT").", 
                        
                   cd_usuario_alteracao             = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao                     = CURRENT_TIMESTAMP

             WHERE cd_financeiro_inadimplencia_previdenciaria = ".intval($cd_financeiro_inadimplencia_previdenciaria).";";

        $this->db->query($qr_sql);
    }

    public function excluir($cd_financeiro_inadimplencia_previdenciaria, $cd_usuario)
    {
        $qr_sql = "
            UPDATE indicador_plugin.financeiro_inadimplencia_previdenciaria 
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_financeiro_inadimplencia_previdenciaria = ".intval($cd_financeiro_inadimplencia_previdenciaria).";";

        $this->db->query($qr_sql);
    }

	public function fechar_periodo($cd_indicador_tabela, $cd_usuario)
	{
		$qr_sql = "
			UPDATE indicador.indicador_tabela 
			   SET dt_fechamento_periodo         = CURRENT_TIMESTAMP, 
			       cd_usuario_fechamento_periodo = ".intval($cd_usuario)." 
		     WHERE cd_indicador_tabela = ".intval($cd_indicador_tabela).";";

		$this->db->query($qr_sql);
	}
}