<?php
class Controladoria_cumprimento_projeto_modernidade_model extends Model
{
	function __construct()
	{
		parent::Model();
    }

    public function listar($cd_indicador_tabela)
    {
        $qr_sql = "
            SELECT cd_controladoria_cumprimento_projeto_modernidade,
                   cd_indicador_tabela,
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   TO_CHAR(dt_referencia, 'MM') AS mes_referencia,
                   TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano_referencia,
                   dt_referencia,
                   fl_media,
                   nr_etapas_previstas,
                   nr_etapas_cumpridas,
                   nr_percentual_cumpridas,
                   nr_meta,
                   ds_observacao
              FROM indicador_plugin.controladoria_cumprimento_projeto_modernidade
             WHERE (fl_media = 'S' OR cd_indicador_tabela = ".intval($cd_indicador_tabela).")
               AND dt_exclusao IS NULL
             ORDER BY dt_referencia ASC
        ";

        return $this->db->query($qr_sql)->result_array();
    }

	public function carrega_referencia()
	{
		$qr_sql = "
			SELECT TO_CHAR(dt_referencia + '1 month'::interval, 'DD/MM/YYYY') AS dt_referencia_n, 
			       TO_CHAR(dt_referencia + '1 month'::interval, 'MM') AS ds_mes_referencia_n, 
                   TO_CHAR(dt_referencia + '1 month'::interval, 'YYYY') AS ds_ano_referencia_n,
                   nr_meta,
				   cd_indicador_tabela 
			  FROM indicador_plugin.controladoria_cumprimento_projeto_modernidade
			 WHERE dt_exclusao IS NULL 
			 ORDER BY dt_referencia DESC 
			 LIMIT 1;";
			 
		return $this->db->query($qr_sql)->row_array();
    }
    
    public function carrega($cd_controladoria_cumprimento_projeto_modernidade)
    {
        $qr_sql = "
            SELECT cd_controladoria_cumprimento_projeto_modernidade,
                   TO_CHAR(dt_referencia, 'DD/MM/YYYY') AS dt_referencia, 
                   TO_CHAR(dt_referencia, 'MM') AS mes_referencia, 
                   TO_CHAR(dt_referencia, 'YYYY') AS ano_referencia,
                   fl_media,
                   nr_etapas_previstas,
                   nr_etapas_cumpridas,
                   nr_meta,
                   ds_observacao
              FROM indicador_plugin.controladoria_cumprimento_projeto_modernidade
             WHERE dt_exclusao                                      IS NULL
               AND cd_controladoria_cumprimento_projeto_modernidade = ".intval($cd_controladoria_cumprimento_projeto_modernidade).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            INSERT INTO indicador_plugin.controladoria_cumprimento_projeto_modernidade
                (
                    cd_indicador_tabela,
                    dt_referencia,
                    fl_media,
                    ds_observacao,
                    nr_etapas_previstas,
                    nr_etapas_cumpridas,
                    nr_meta,
                    nr_percentual_cumpridas,
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                )
           VALUES
                (
                    ".(intval($args['cd_indicador_tabela']) > 0 ? intval($args['cd_indicador_tabela']) : "DEFAULT").",
                    ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                    ".(trim($args['fl_media']) != '' ? str_escape($args['fl_media']) : "DEFAULT").",
                    ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                    ".(trim($args['nr_etapas_previstas']) != '' ? floatval($args['nr_etapas_previstas']) : "DEFAULT").",
                    ".(trim($args['nr_etapas_cumpridas']) != '' ? floatval($args['nr_etapas_cumpridas']) : "DEFAULT").",
                    ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
                    ".(trim($args['nr_percentual_cumpridas']) != '' ? floatval($args['nr_percentual_cumpridas']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                    ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT")."
                );";

        $this->db->query($qr_sql);
    }

    public function atualizar($cd_controladoria_cumprimento_projeto_modernidade, $args = array())
    {
        $qr_sql = "
            UPDATE indicador_plugin.controladoria_cumprimento_projeto_modernidade
               SET dt_referencia           = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".trim($args['dt_referencia'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                   ds_observacao           = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
                   nr_etapas_previstas     = ".(trim($args['nr_etapas_previstas']) != '' ? floatval($args['nr_etapas_previstas']) : "DEFAULT").",
                   nr_etapas_cumpridas     = ".(trim($args['nr_etapas_cumpridas']) != '' ? floatval($args['nr_etapas_cumpridas']) : "DEFAULT").",
                   nr_meta                 = ".(trim($args['nr_meta']) != '' ? floatval($args['nr_meta']) : "DEFAULT").",
                   nr_percentual_cumpridas = ".(trim($args['nr_percentual_cumpridas']) != '' ? floatval($args['nr_percentual_cumpridas']) : "DEFAULT").",
                   cd_usuario_alteracao    = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_alteracao            = CURRENT_TIMESTAMP
             WHERE cd_controladoria_cumprimento_projeto_modernidade = ".intval($cd_controladoria_cumprimento_projeto_modernidade).";";

        $this->db->query($qr_sql);
    }

    public function excluir($cd_controladoria_cumprimento_projeto_modernidade, $cd_usuario)
    {
        $qr_sql = "
            UPDATE indicador_plugin.controladoria_cumprimento_projeto_modernidade
               SET cd_usuario_exclusao = ".(intval($cd_usuario) > 0 ? intval($cd_usuario) : "DEFAULT").",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_controladoria_cumprimento_projeto_modernidade = ".intval($cd_controladoria_cumprimento_projeto_modernidade).";";

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