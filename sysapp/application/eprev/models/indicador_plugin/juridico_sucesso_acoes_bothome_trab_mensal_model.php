<?php
class Juridico_sucesso_acoes_bothome_trab_mensal_model extends Model
{
    public function listar($cd_indicador_tabela)
    {
        $qr_sql = "
            SELECT cd_juridico_sucesso_acoes_bothome_trab_mensal,
                   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
                   ds_observacao,
                   ds_tabela,
                   fl_media,

                   nr_inicial,
       
                   nr_improcede_1,			    
                   nr_parcial_1,
                   nr_procede_1,
       
                   nr_improcede_2,			    
                   nr_parcial_2,
                   nr_procede_2,
    
                   nr_improcede_3,			    
                   nr_parcial_3,
                   nr_procede_3,

                   nr_total_1,
                   nr_total_2,
                   nr_total_3,

                   nr_totalizador,

                   nr_total,

                   nr_improcede_total,
                   nr_parcial_total,
                   nr_procede_total,

                   pr_improcede_1, 
                   pr_parcial_1, 
                   pr_procede_1,

                   pr_improcede_2, 
                   pr_parcial_2, 
                   pr_procede_2,

                   pr_improcede_3, 
                   pr_parcial_3, 
                   pr_procede_3,

                   pr_improcede, 
                   pr_parcial, 
                   pr_procede,

                   nr_improc_min,
				   nr_improc_max,
				   nr_parcial_min,
				   nr_parcial_max,
				   nr_proc_min,
				   nr_proc_max,

				   fl_meta_improc,
				   fl_direcao_improc,
				   fl_meta_parcial,
				   fl_direcao_parcial,
				   fl_meta_proc,
				   fl_direcao_proc
              FROM indicador_plugin.juridico_sucesso_acoes_bothome_trab_mensal
             WHERE dt_exclusao IS NULL
               AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
             ORDER BY dt_referencia ASC;";
         
        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_referencia()
    {
        $qr_sql = "
            SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
				   cd_indicador_tabela,
				   nr_improc_min,
		           nr_improc_max,
		           nr_parcial_min,
		           nr_parcial_max,
		           nr_proc_min,
		           nr_proc_max
			  FROM indicador_plugin.juridico_sucesso_acoes_bothome_trab_mensal
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
             LIMIT 1;";
             
        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_referencia_status($dt_referencia, $ds_calculo)
    {
        $qr_sql = "
            SELECT cd_indicador_tabela,
				   pr_improcede,
		           nr_improc_min,
		           pr_parcial,
		           nr_parcial_min,
		           pr_procede,
		           nr_proc_min,
		           dt_referencia
              FROM indicador_plugin.juridico_sucesso_acoes_bothome_trab_mensal
             WHERE dt_exclusao IS NULL
               AND fl_media ".trim($ds_calculo)." 'N'
               AND dt_referencia < '".$dt_referencia."' 
             ORDER BY dt_referencia DESC
             LIMIT 1;";
             
        return $this->db->query($qr_sql)->row_array();
    }

    public function get_status($nr_valor, $nr_valor_anterior, $nr_meta, $ds_calculo)
    {
        $qr_sql = "
            SELECT * 
              FROM indicador.resultado_status(".floatval($nr_valor).", ".floatval($nr_valor_anterior).", ".floatval($nr_meta).", '".trim($ds_calculo)."');";
             
        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega($cd_juridico_sucesso_acoes_bothome_trab_mensal)
    {
        $qr_sql = "
            SELECT cd_juridico_sucesso_acoes_bothome_trab_mensal,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   ds_observacao,

                   nr_inicial,

                   nr_improcede_1,
                   nr_parcial_1,
                   nr_procede_1,

                   nr_improcede_2,
                   nr_parcial_2,
                   nr_procede_2,

                   nr_improcede_3,
                   nr_parcial_3,
                   nr_procede_3,

                   nr_improc_min,
				   nr_improc_max,
				   nr_parcial_min,
				   nr_parcial_max,
				   nr_proc_min,
				   nr_proc_max
              FROM indicador_plugin.juridico_sucesso_acoes_bothome_trab_mensal
             WHERE cd_juridico_sucesso_acoes_bothome_trab_mensal = ".intval($cd_juridico_sucesso_acoes_bothome_trab_mensal).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO indicador_plugin.juridico_sucesso_acoes_bothome_trab_mensal
            (
                cd_indicador_tabela, 
                dt_referencia, 
                ds_observacao,
                ds_tabela,
                fl_media,

                nr_inicial, 

                nr_improcede_1, 
                nr_parcial_1, 
                nr_procede_1,


                nr_improcede_2, 
                nr_parcial_2, 
                nr_procede_2,

                nr_improcede_3, 
                nr_parcial_3, 
                nr_procede_3,
                
                nr_total_1,
                nr_total_2,
                nr_total_3,

                nr_totalizador,

                nr_total,

                nr_improcede_total,
                nr_parcial_total,
                nr_procede_total,

                pr_improcede_1, 
                pr_parcial_1, 
                pr_procede_1,

                pr_improcede_2, 
                pr_parcial_2, 
                pr_procede_2,
 
                pr_improcede_3, 
                pr_parcial_3, 
                pr_procede_3,

                pr_improcede, 
                pr_parcial, 
                pr_procede,

				nr_improc_min,
				nr_improc_max,
				nr_parcial_min,
				nr_parcial_max,
				nr_proc_min,
				nr_proc_max,

				fl_meta_improc,
			    fl_direcao_improc,
			    fl_meta_parcial,
			    fl_direcao_parcial,
			    fl_meta_proc,
			    fl_direcao_proc,

                cd_usuario_inclusao,
                cd_usuario_alteracao
            ) 
        VALUES 
            ( 
                ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
                ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                ".(trim($args['ds_tabela']) != '' ? str_escape($args['ds_tabela']) : "DEFAULT").",
                ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",

                ".(trim($args['nr_inicial']) != '' ? floatval($args['nr_inicial']) : "DEFAULT").",

                ".(trim($args['nr_improcede_1']) != '' ? floatval($args['nr_improcede_1']) : "DEFAULT").",
                ".(trim($args['nr_parcial_1']) != '' ? floatval($args['nr_parcial_1']) : "DEFAULT").",
                ".(trim($args['nr_procede_1']) != '' ? floatval($args['nr_procede_1']) : "DEFAULT").",

                ".(trim($args['nr_improcede_2']) != '' ? floatval($args['nr_improcede_2']) : "DEFAULT").",
                ".(trim($args['nr_parcial_2']) != '' ? floatval($args['nr_parcial_2']) : "DEFAULT").",
                ".(trim($args['nr_procede_2']) != '' ? floatval($args['nr_procede_2']) : "DEFAULT").",

                ".(trim($args['nr_improcede_3']) != '' ? floatval($args['nr_improcede_3']) : "DEFAULT").",
                ".(trim($args['nr_parcial_3']) != '' ? floatval($args['nr_parcial_3']) : "DEFAULT").",
                ".(trim($args['nr_procede_3']) != '' ? floatval($args['nr_procede_3']) : "DEFAULT").", 

                ".(trim($args['nr_total_1']) != '' ? floatval($args['nr_total_1']) : "DEFAULT").",
                ".(trim($args['nr_total_2']) != '' ? floatval($args['nr_total_2']) : "DEFAULT").",
                ".(trim($args['nr_total_3']) != '' ? floatval($args['nr_total_3']) : "DEFAULT").",

                ".(trim($args['nr_totalizador']) != '' ? floatval($args['nr_totalizador']) : "DEFAULT").",

                ".(trim($args['nr_total']) != '' ? floatval($args['nr_total']) : "DEFAULT").",

                ".(trim($args['nr_improcede_total']) != '' ? floatval($args['nr_improcede_total']) : "DEFAULT").",
                ".(trim($args['nr_parcial_total']) != '' ? floatval($args['nr_parcial_total']) : "DEFAULT").",
                ".(trim($args['nr_procede_total']) != '' ? floatval($args['nr_procede_total']) : "DEFAULT").",
                
                ".(trim($args['pr_improcede_1']) != '' ? floatval($args['pr_improcede_1']) : "DEFAULT").",
                ".(trim($args['pr_parcial_1']) != '' ? floatval($args['pr_parcial_1']) : "DEFAULT").",
                ".(trim($args['pr_procede_1']) != '' ? floatval($args['pr_procede_1']) : "DEFAULT").",

                ".(trim($args['pr_improcede_2']) != '' ? floatval($args['pr_improcede_2']) : "DEFAULT").",
                ".(trim($args['pr_parcial_2']) != '' ? floatval($args['pr_parcial_2']) : "DEFAULT").",
                ".(trim($args['pr_procede_2']) != '' ? floatval($args['pr_procede_2']) : "DEFAULT").",

                ".(trim($args['pr_improcede_3']) != '' ? floatval($args['pr_improcede_3']) : "DEFAULT").",
                ".(trim($args['pr_parcial_3']) != '' ? floatval($args['pr_parcial_3']) : "DEFAULT").",
                ".(trim($args['pr_procede_3']) != '' ? floatval($args['pr_procede_3']) : "DEFAULT").",   

                ".(trim($args['pr_improcede']) != '' ? floatval($args['pr_improcede']) : "DEFAULT").",
                ".(trim($args['pr_parcial']) != '' ? floatval($args['pr_parcial']) : "DEFAULT").",
                ".(trim($args['pr_procede']) != '' ? floatval($args['pr_procede']) : "DEFAULT").",

                ".(trim($args['nr_improc_min']) != '' ? floatval($args['nr_improc_min']) : "DEFAULT").",   
                ".(trim($args['nr_improc_max']) != '' ? floatval($args['nr_improc_max']) : "DEFAULT").",   
                ".(trim($args['nr_parcial_min']) != '' ? floatval($args['nr_parcial_min']) : "DEFAULT").",   
                ".(trim($args['nr_parcial_max']) != '' ? floatval($args['nr_parcial_max']) : "DEFAULT").",   
                ".(trim($args['nr_proc_min']) != '' ? floatval($args['nr_proc_min']) : "DEFAULT").",   
                ".(trim($args['nr_proc_max']) != '' ? floatval($args['nr_proc_max']) : "DEFAULT").",   

                ".(trim($args['fl_meta_improc']) != '' ? str_escape($args['fl_meta_improc']) : "DEFAULT").",
                ".(trim($args['fl_direcao_improc']) != '' ? str_escape($args['fl_direcao_improc']) : "DEFAULT").",
                ".(trim($args['fl_meta_parcial']) != '' ? str_escape($args['fl_meta_parcial']) : "DEFAULT").",
                ".(trim($args['fl_direcao_parcial']) != '' ? str_escape($args['fl_direcao_parcial']) : "DEFAULT").",
                ".(trim($args['fl_meta_proc']) != '' ? str_escape($args['fl_meta_proc']) : "DEFAULT").",
                ".(trim($args['fl_direcao_proc']) != '' ? str_escape($args['fl_direcao_proc']) : "DEFAULT").",

                ".intval($args['cd_usuario']).",
                ".intval($args['cd_usuario'])."
            );";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_juridico_sucesso_acoes_bothome_trab_mensal, $args = array())
    {
        $qr_sql = "
            UPDATE indicador_plugin.juridico_sucesso_acoes_bothome_trab_mensal 
               SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ?  "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").", 
                   ds_observacao        = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                   ds_tabela            = ".(trim($args['ds_tabela']) != '' ? str_escape($args['ds_tabela']) : "DEFAULT").",

                   nr_inicial           = ".(trim($args['nr_inicial']) != '' ? floatval($args['nr_inicial']) : "DEFAULT").", 
         
                   nr_improcede_1       = ".(trim($args['nr_improcede_1']) != '' ? floatval($args['nr_improcede_1']) : "DEFAULT").", 
                   nr_parcial_1         = ".(trim($args['nr_parcial_1']) != '' ? floatval($args['nr_parcial_1']) : "DEFAULT").", 
                   nr_procede_1         = ".(trim($args['nr_procede_1']) != '' ? floatval($args['nr_procede_1']) : "DEFAULT").",

                   nr_improcede_2       = ".(trim($args['nr_improcede_2']) != '' ? floatval($args['nr_improcede_2']) : "DEFAULT").", 
                   nr_parcial_2         = ".(trim($args['nr_parcial_2']) != '' ? floatval($args['nr_parcial_2']) : "DEFAULT").", 
                   nr_procede_2         = ".(trim($args['nr_procede_2']) != '' ? floatval($args['nr_procede_2']) : "DEFAULT").",
 
                   nr_improcede_3       = ".(trim($args['nr_improcede_3']) != '' ? floatval($args['nr_improcede_3']) : "DEFAULT").", 
                   nr_parcial_3         = ".(trim($args['nr_parcial_3']) != '' ? floatval($args['nr_parcial_3']) : "DEFAULT").", 
                   nr_procede_3         = ".(trim($args['nr_procede_3']) != '' ? floatval($args['nr_procede_3']) : "DEFAULT").",

                   nr_total_1           = ".(trim($args['nr_total_1']) != '' ? floatval($args['nr_total_1']) : "DEFAULT").",
                   nr_total_2           = ".(trim($args['nr_total_2']) != '' ? floatval($args['nr_total_2']) : "DEFAULT").",
                   nr_total_3           = ".(trim($args['nr_total_3']) != '' ? floatval($args['nr_total_3']) : "DEFAULT").",

                   nr_totalizador       = ".(trim($args['nr_totalizador']) != '' ? floatval($args['nr_totalizador']) : "DEFAULT").",

                   nr_total             = ".(trim($args['nr_total']) != '' ? floatval($args['nr_total']) : "DEFAULT").",

                   nr_improcede_total   = ".(trim($args['nr_improcede_total']) != '' ? floatval($args['nr_improcede_total']) : "DEFAULT").",
                   nr_parcial_total     = ".(trim($args['nr_parcial_total']) != '' ? floatval($args['nr_parcial_total']) : "DEFAULT").",
                   nr_procede_total     = ".(trim($args['nr_procede_total']) != '' ? floatval($args['nr_procede_total']) : "DEFAULT").",

                   pr_improcede_1       = ".(trim($args['pr_improcede_1'])!= '' ? floatval($args['pr_improcede_1']) : "DEFAULT").", 
                   pr_parcial_1         = ".(trim($args['pr_parcial_1']) != '' ? floatval($args['pr_parcial_1']) : "DEFAULT").", 
                   pr_procede_1         = ".(trim($args['pr_procede_1']) != '' ? floatval($args['pr_procede_1']) : "DEFAULT").",

                   pr_improcede_2       = ".(trim($args['pr_improcede_2']) != '' ? floatval($args['pr_improcede_2']) : "DEFAULT").", 
                   pr_parcial_2         = ".(trim($args['pr_parcial_2']) != '' ? floatval($args['pr_parcial_2']) : "DEFAULT").", 
                   pr_procede_2         = ".(trim($args['pr_procede_2']) != '' ? floatval($args['pr_procede_2']) : "DEFAULT").",

                   pr_improcede_3       = ".(trim($args['pr_improcede_3']) != '' ? floatval($args['pr_improcede_3']) : "DEFAULT").", 
                   pr_parcial_3         = ".(trim($args['pr_parcial_3']) != '' ? floatval($args['pr_parcial_3']) : "DEFAULT").", 
                   pr_procede_3         = ".(trim($args['pr_procede_3']) != '' ? floatval($args['pr_procede_3']) : "DEFAULT").",

                   pr_improcede         =".(trim($args['pr_improcede']) != '' ? floatval($args['pr_improcede']) : "DEFAULT").",
                   pr_parcial           =".(trim($args['pr_parcial']) != '' ? floatval($args['pr_parcial']) : "DEFAULT").",
                   pr_procede           =".(trim($args['pr_procede']) != '' ? floatval($args['pr_procede']) : "DEFAULT").",  

				   nr_improc_min        = ".(trim($args['nr_improc_min']) != '' ? floatval($args['nr_improc_min']) : "DEFAULT").",   
				   nr_improc_max 		= ".(trim($args['nr_improc_max']) != '' ? floatval($args['nr_improc_max']) : "DEFAULT").",   
				   nr_parcial_min 		= ".(trim($args['nr_parcial_min']) != '' ? floatval($args['nr_parcial_min']) : "DEFAULT").",   
				   nr_parcial_max 		= ".(trim($args['nr_parcial_max']) != '' ? floatval($args['nr_parcial_max']) : "DEFAULT").",   
				   nr_proc_min 			= ".(trim($args['nr_proc_min']) != '' ? floatval($args['nr_proc_min']) : "DEFAULT").",   
				   nr_proc_max 			= ".(trim($args['nr_proc_max']) != '' ? floatval($args['nr_proc_max']) : "DEFAULT").",   

				   fl_meta_improc       = ".(trim($args['fl_meta_improc']) != '' ? str_escape($args['fl_meta_improc']) : "DEFAULT").",
			       fl_direcao_improc    = ".(trim($args['fl_direcao_improc']) != '' ? str_escape($args['fl_direcao_improc']) : "DEFAULT").",
			       fl_meta_parcial      = ".(trim($args['fl_meta_parcial']) != '' ? str_escape($args['fl_meta_parcial']) : "DEFAULT").",
			       fl_direcao_parcial   = ".(trim($args['fl_direcao_parcial']) != '' ? str_escape($args['fl_direcao_parcial']) : "DEFAULT").",
			       fl_meta_proc         = ".(trim($args['fl_meta_proc']) != '' ? str_escape($args['fl_meta_proc']) : "DEFAULT").",
			       fl_direcao_proc      = ".(trim($args['fl_direcao_proc']) != '' ? str_escape($args['fl_direcao_proc']) : "DEFAULT").",

                   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP

             WHERE cd_juridico_sucesso_acoes_bothome_trab_mensal = ".intval($cd_juridico_sucesso_acoes_bothome_trab_mensal).";";

        $this->db->query($qr_sql);
    }

    public function excluir($cd_juridico_sucesso_acoes_bothome_trab_mensal, $cd_usuario)
    {
        $qr_sql = "
            UPDATE indicador_plugin.juridico_sucesso_acoes_bothome_trab_mensal 
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_juridico_sucesso_acoes_bothome_trab_mensal = ".intval($cd_juridico_sucesso_acoes_bothome_trab_mensal).";";

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