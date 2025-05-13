<?php
class Financeiro_divulgacoes_extratos_planos_prazo_model extends Model
{
    public function listar($cd_indicador_tabela)
    {
        $qr_sql = "
            SELECT cd_financeiro_divulgacoes_extratos_planos_prazo,
                   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
                   ds_observacao,
                   fl_media,
                   nr_resultado,
                   nr_meta
              FROM indicador_plugin.financeiro_divulgacoes_extratos_planos_prazo
             WHERE dt_exclusao IS NULL
               AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
             ORDER BY dt_referencia ASC;";
     
        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega($cd_financeiro_divulgacoes_extratos_planos_prazo)
    {
        $qr_sql = "
            SELECT cd_financeiro_divulgacoes_extratos_planos_prazo,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   ds_observacao,
                   nr_resultado,
                   nr_meta
              FROM indicador_plugin.financeiro_divulgacoes_extratos_planos_prazo
             WHERE dt_exclusao IS NULL
               AND cd_financeiro_divulgacoes_extratos_planos_prazo = ".intval($cd_financeiro_divulgacoes_extratos_planos_prazo).";";
 
        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega_referencia()
    {
        $qr_sql = "
            SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
                   nr_meta,
                   cd_indicador_tabela 
              FROM indicador_plugin.financeiro_divulgacoes_extratos_planos_prazo
             WHERE dt_exclusao IS NULL 
             ORDER BY dt_referencia DESC 
             LIMIT 1;";
             
        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO indicador_plugin.financeiro_divulgacoes_extratos_planos_prazo
            (
                cd_indicador_tabela, 
                dt_referencia, 
                ds_observacao,
                fl_media,
                nr_resultado, 
                nr_meta,
                cd_usuario_inclusao,
                cd_usuario_alteracao
            ) 
        VALUES 
            ( 
                ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
                ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
                ".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").", 
                ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").", 
                ".intval($args['cd_usuario']).",
                ".intval($args['cd_usuario'])."
            );";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_financeiro_divulgacoes_extratos_planos_prazo, $args = array())
    {
        $qr_sql = "
            UPDATE indicador_plugin.financeiro_divulgacoes_extratos_planos_prazo 
               SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ?  "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").", 
                   ds_observacao        = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                   nr_resultado         = ".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").",  
                   nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",  
                   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_financeiro_divulgacoes_extratos_planos_prazo = ".intval($cd_financeiro_divulgacoes_extratos_planos_prazo).";";

        $this->db->query($qr_sql);
    }

    public function excluir($cd_financeiro_divulgacoes_extratos_planos_prazo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE indicador_plugin.financeiro_divulgacoes_extratos_planos_prazo 
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_financeiro_divulgacoes_extratos_planos_prazo = ".intval($cd_financeiro_divulgacoes_extratos_planos_prazo).";";

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