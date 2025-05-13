<?php
class Investimento_alcadas_model extends Model
{
    public function listar($cd_indicador_tabela)
    {
        $qr_sql = "
            SELECT cd_investimento_alcadas,
                   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
                   ds_observacao,
                   fl_media,
                   nr_meta,
                   nr_alcadas_atendidas
              FROM indicador_plugin.investimento_alcadas
             WHERE dt_exclusao IS NULL
               AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
             ORDER BY dt_referencia ASC;";
         
        return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_referencia($nr_ano_referencia)
    {
        $qr_sql = "
            SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n,
                   nr_meta, 
				   cd_indicador_tabela ,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.investimento_alcadas
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.investimento_alcadas
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
             LIMIT 1;";
             
        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega($cd_investimento_alcadas)
    {
        $qr_sql = "
            SELECT cd_investimento_alcadas,
                   TO_CHAR(dt_referencia,'DD/MM/YYYY') AS dt_referencia,
                   cd_indicador_tabela,
                   fl_media,
                   ds_observacao,
                   nr_meta,
                   nr_alcadas_atendidas
              FROM indicador_plugin.investimento_alcadas
             WHERE cd_investimento_alcadas = ".intval($cd_investimento_alcadas).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO indicador_plugin.investimento_alcadas
            (
                cd_indicador_tabela, 
                dt_referencia, 
                ds_observacao,
                fl_media,
                nr_meta,
                nr_alcadas_atendidas,
                cd_usuario_inclusao,
                cd_usuario_alteracao
            ) 
        VALUES 
            ( 
                ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
                ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",                  
                ".(intval($args['nr_meta']) != '' ? intval($args['nr_meta']) : "DEFAULT").",
                ".(intval($args['nr_alcadas_atendidas']) != '' ? intval($args['nr_alcadas_atendidas']) : "DEFAULT").",
                ".intval($args['cd_usuario']).",
                ".intval($args['cd_usuario'])."
            );";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_investimento_alcadas, $args = array())
    {
        $qr_sql = "
            UPDATE indicador_plugin.investimento_alcadas 
               SET dt_referencia              = ".(trim($args['dt_referencia']) != '' ?  "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").", 
                   ds_observacao              = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                   nr_meta                    = ".(intval($args['nr_meta']) != '' ? intval($args['nr_meta']) : "DEFAULT").",
                   nr_alcadas_atendidas       = ".(intval($args['nr_alcadas_atendidas']) != '' ? intval($args['nr_alcadas_atendidas']) : "DEFAULT").",
                   cd_usuario_alteracao       = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao               = CURRENT_TIMESTAMP
             WHERE cd_investimento_alcadas = ".intval($cd_investimento_alcadas).";";

        $this->db->query($qr_sql);
    }

    public function excluir($cd_investimento_alcadas, $cd_usuario)
    {
        $qr_sql = "
            UPDATE indicador_plugin.investimento_alcadas 
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_investimento_alcadas = ".intval($cd_investimento_alcadas).";";

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