<?php
class Atendimento_retencao_cliente_model extends Model
{
    public function listar($cd_indicador_tabela)
    {
        $qr_sql = "
            SELECT cd_atendimento_retencao_cliente,
                   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
                   nr_interesse,
                   nr_efetivas,
                   nr_negociacao,
                   nr_resultado,
                   nr_meta,
                   nr_nao_retido,
                   nr_cliente,
                   ds_observacao,
                   fl_media
              FROM indicador_plugin.atendimento_retencao_cliente
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
				   cd_indicador_tabela,
				   (SELECT COUNT(*)
				      FROM indicador_plugin.atendimento_retencao_cliente
				     WHERE TO_CHAR(dt_referencia, 'YYYY')::integer = ".intval($nr_ano_referencia)."
				       AND dt_exclusao IS NULL) AS qt_ano
			  FROM indicador_plugin.atendimento_retencao_cliente
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
             LIMIT 1;";
             
        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega($cd_atendimento_retencao_cliente)
    {
        $qr_sql = "
            SELECT cd_atendimento_retencao_cliente,
                   TO_CHAR(dt_referencia, 'DD/MM/YYYY') AS dt_referencia, 
                   ds_observacao,
                   nr_interesse,
                   nr_efetivas,
                   nr_nao_retido,
                   nr_negociacao,
                   nr_cliente,
                   nr_meta      
              FROM indicador_plugin.atendimento_retencao_cliente
             WHERE dt_exclusao IS NULL 
               AND cd_atendimento_retencao_cliente = ".intval($cd_atendimento_retencao_cliente).";";
             
        return $this->db->query($qr_sql)->row_array();
    }

    public function get_valores($ano, $mes)
    {
        $qr_sql = "
            SELECT SUM(y.tl_interesse) AS nr_interesse,
                   SUM(y.tl_retencao) AS nr_efetivas,
                   SUM(y.tl_negociacao) AS nr_negociacao 
              FROM (
            SELECT COUNT(x.*) AS tl_interesse,
                   0 AS tl_retencao,
                   0 AS tl_negociacao     
              FROM (
            SELECT DISTINCT cd_empresa, cd_registro_empregado, seq_dependencia, fl_retido
              FROM projetos.atendimento_retencao ar
             WHERE ar.dt_exclusao IS NULL
               AND TO_CHAR(dt_inclusao, 'MM')::integer   = ".intval($mes)."
               AND TO_CHAR(dt_inclusao, 'YYYY')::integer = ".intval($ano)."
               AND fl_retido                             IS NOT NULL
                ) x
    
            UNION
            SELECT 0 AS tl_interesse,
                   COUNT(x.*) AS tl_retencao,
                   0 AS tl_negociacao
              FROM (
            SELECT DISTINCT cd_empresa, cd_registro_empregado, seq_dependencia, fl_retido
              FROM projetos.atendimento_retencao ar
             WHERE ar.dt_exclusao IS NULL
               AND TO_CHAR(dt_inclusao, 'MM')::integer   = ".intval($mes)."
               AND TO_CHAR(dt_inclusao, 'YYYY')::integer = ".intval($ano)."
               AND fl_retido                             = 'S'
                ) x
    
            UNION
            SELECT 0 AS tl_interesse,
                   0 AS tl_retencao,
                   COUNT(x.*) AS tl_negociacao
              FROM (
            SELECT DISTINCT cd_empresa, cd_registro_empregado, seq_dependencia, fl_retido
              FROM projetos.atendimento_retencao ar
             WHERE ar.dt_exclusao IS NULL
               AND TO_CHAR(dt_inclusao, 'MM')::integer   = ".intval($mes)."
               AND TO_CHAR(dt_inclusao, 'YYYY')::integer = ".intval($ano)."
               AND fl_retido                             IS NULL
                ) x
                ) y;";
            
        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
        INSERT INTO indicador_plugin.atendimento_retencao_cliente
            (
                cd_indicador_tabela, 
                dt_referencia, 
                ds_observacao,
                fl_media,
                nr_interesse, 
                nr_cliente,
                nr_efetivas, 
                nr_resultado, 
                nr_nao_retido,
                nr_negociacao, 
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
                ".(trim($args['nr_interesse']) != '' ? floatval($args['nr_interesse']) : "DEFAULT").",
                ".(trim($args['nr_cliente']) != '' ? floatval($args['nr_cliente']) : "DEFAULT").",
                ".(trim($args['nr_efetivas']) != '' ? floatval($args['nr_efetivas']) : "DEFAULT").",
                ".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").",
                ".(trim($args['nr_nao_retido']) != '' ? floatval($args['nr_nao_retido']) : "DEFAULT").",
                ".(trim($args['nr_negociacao']) != '' ? floatval($args['nr_negociacao']) : "DEFAULT").",
                ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
                ".intval($args['cd_usuario']).",
                ".intval($args['cd_usuario'])."
            );";

    $this->db->query($qr_sql);
    }

    public function atualizar($cd_atendimento_retencao_cliente, $args = array())
    {
        $qr_sql = "
            UPDATE indicador_plugin.atendimento_retencao_cliente 
               SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ?  "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").", 
                   ds_observacao        = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                   nr_interesse         = ".(trim($args['nr_interesse']) != '' ? floatval($args['nr_interesse']) : "DEFAULT").", 
                   nr_efetivas          = ".(trim($args['nr_efetivas']) != '' ? floatval($args['nr_efetivas']) : "DEFAULT").", 
                   nr_cliente 			= ".(trim($args['nr_cliente']) != '' ? floatval($args['nr_cliente']) : "DEFAULT").",
                   nr_resultado         = ".(trim($args['nr_resultado']) != '' ? floatval($args['nr_resultado']) : "DEFAULT").",  
                   nr_nao_retido        = ".(trim($args['nr_nao_retido']) != '' ? floatval($args['nr_nao_retido']) : "DEFAULT").",  
                   nr_negociacao        = ".(trim($args['nr_negociacao']) != '' ? floatval($args['nr_negociacao']) : "DEFAULT").",  
                   nr_meta              = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",  
                   cd_usuario_alteracao = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_atendimento_retencao_cliente = ".intval($cd_atendimento_retencao_cliente).";";

        $this->db->query($qr_sql);
    }

    public function excluir($cd_atendimento_retencao_cliente, $cd_usuario)
    {
        $qr_sql = "
            UPDATE indicador_plugin.atendimento_retencao_cliente 
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_atendimento_retencao_cliente = ".intval($cd_atendimento_retencao_cliente).";";

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