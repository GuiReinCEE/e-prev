<?php
class Cadastro_atividades_model extends Model
{
    public function listar($cd_indicador_tabela)
    {
        $qr_sql = "
            SELECT cd_cadastro_atividades,
                   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
                   
                   nr_atividade_aberta,
                   nr_atividade_andamento,
                   nr_atividade_concluida,
                   nr_atividade_cancelada,
                   nr_atividade_acumulada,
                   nr_atividade_atendidas,
                   nr_meta,
                   nr_tempo_min,
                   nr_tempo_hora,

                   ds_observacao,

                   fl_media
              FROM indicador_plugin.cadastro_atividades
             WHERE dt_exclusao IS NULL
               AND (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
             ORDER BY dt_referencia ASC;";

    return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_referencia()
    {
        $qr_sql = "
            SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
                   nr_meta,
				   cd_indicador_tabela 
			  FROM indicador_plugin.cadastro_atividades
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
             LIMIT 1;";
             
        return $this->db->query($qr_sql)->row_array();
    }

    public function carrega($cd_cadastro_atividades)
    {
        $qr_sql = "
            SELECT cd_cadastro_atividades,
                   TO_CHAR(dt_referencia, 'DD/MM/YYYY') AS dt_referencia, 
                   ds_observacao,
                   
                   nr_atividade_aberta,
                   nr_atividade_andamento,
                   nr_atividade_concluida,
                   nr_atividade_cancelada,
                   nr_atividade_acumulada,
                   nr_atividade_atendidas,
                   nr_meta,
                   nr_tempo_min,
                   nr_tempo_hora,

                   nr_meta      
              FROM indicador_plugin.cadastro_atividades
             WHERE dt_exclusao IS NULL 
               AND cd_cadastro_atividades = ".intval($cd_cadastro_atividades).";";
             
        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO indicador_plugin.cadastro_atividades
                (
                    cd_indicador_tabela, 
                    dt_referencia, 
                    ds_observacao,
                    fl_media,
                    
                    nr_atividade_aberta,
                    nr_atividade_andamento,
                    nr_atividade_concluida,
                    nr_atividade_cancelada,
                    nr_atividade_acumulada,
                    nr_atividade_atendidas,
                    nr_meta,
                    nr_tempo_min,
                    nr_tempo_hora,

                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                ) 
            VALUES 
                ( 
                    ".(intval($args['cd_indicador_tabela']) != 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
                    ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                    ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",

                    ".(trim($args['nr_atividade_aberta']) != '' ? intval($args['nr_atividade_aberta']) : "DEFAULT").",
                    ".(trim($args['nr_atividade_andamento']) != '' ? intval($args['nr_atividade_andamento']) : "DEFAULT").",
                    ".(trim($args['nr_atividade_concluida']) != '' ? intval($args['nr_atividade_concluida']) : "DEFAULT").",
                    ".(trim($args['nr_atividade_cancelada']) != '' ? intval($args['nr_atividade_cancelada']) : "DEFAULT").",
                    ".(trim($args['nr_atividade_acumulada']) != '' ? intval($args['nr_atividade_acumulada']) : "DEFAULT").",

                    ".(trim($args['nr_atividade_atendidas']) != '' ? floatval($args['nr_atividade_atendidas']) : "DEFAULT").",
                    ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",

                    ".(trim($args['nr_tempo_min']) != '' ? intval($args['nr_tempo_min']) : "DEFAULT").",
                    ".(trim($args['nr_tempo_hora']) != '' ? intval($args['nr_tempo_hora']) : "DEFAULT").",

                    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
                );";

        $this->db->query($qr_sql);
        }

    public function atualizar($cd_cadastro_atividades, $args = array())
    {
        $qr_sql = "
            UPDATE indicador_plugin.cadastro_atividades 
               SET dt_referencia          = ".(trim($args['dt_referencia']) != '' ?  "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").", 
                   ds_observacao          = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",

                   nr_atividade_aberta    = ".(trim($args['nr_atividade_aberta']) != '' ? intval($args['nr_atividade_aberta']) : "DEFAULT").",
                   nr_atividade_andamento = ".(trim($args['nr_atividade_andamento']) != '' ? intval($args['nr_atividade_andamento']) : "DEFAULT").",
                   nr_atividade_concluida = ".(trim($args['nr_atividade_concluida']) != '' ? intval($args['nr_atividade_concluida']) : "DEFAULT").",
                   nr_atividade_cancelada = ".(trim($args['nr_atividade_cancelada']) != '' ? intval($args['nr_atividade_cancelada']) : "DEFAULT").",
                   nr_atividade_acumulada = ".(trim($args['nr_atividade_acumulada']) != '' ? intval($args['nr_atividade_acumulada']) : "DEFAULT").",
                   
                   nr_atividade_atendidas = ".(trim($args['nr_atividade_atendidas']) != '' ? floatval($args['nr_atividade_atendidas']) : "DEFAULT").",  
                   nr_meta                = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",  
                   
                   nr_tempo_min           = ".(trim($args['nr_tempo_min']) != '' ? intval($args['nr_tempo_min']) : "DEFAULT").",
                   nr_tempo_hora          = ".(trim($args['nr_tempo_hora']) != '' ? intval($args['nr_tempo_hora']) : "DEFAULT").",

                   cd_usuario_alteracao   = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao           = CURRENT_TIMESTAMP
             WHERE cd_cadastro_atividades = ".intval($cd_cadastro_atividades).";";

        $this->db->query($qr_sql);
    }

    public function excluir($cd_cadastro_atividades, $cd_usuario)
    {
        $qr_sql = "
            UPDATE indicador_plugin.cadastro_atividades 
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_cadastro_atividades = ".intval($cd_cadastro_atividades).";";

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