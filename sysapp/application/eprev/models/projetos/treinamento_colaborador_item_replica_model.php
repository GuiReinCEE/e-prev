<?php
class Treinamento_colaborador_item_replica_model extends Model
{
    public function listar($cd_registro_empregado, $args = array())
    {
        $qr_sql = "
            SELECT tc.nome,
                   funcoes.nr_treinamento_colaborador(tc.ano, tc.numero) AS numero,
                   funcoes.get_usuario_nome(tcir.cd_usuario_concluido) AS ds_usuario,
                   tc.promotor,
                   tc.cidade,
                   tc.uf,
                   TO_CHAR(tc.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
                   TO_CHAR(tc.dt_final, 'DD/MM/YYYY') AS dt_final,
                   TO_CHAR(tcir.dt_concluido, 'DD/MM/YYYY') AS dt_finalizado,
                   tct.ds_treinamento_colaborador_tipo,
                   tci.cd_treinamento_colaborador_item,
                   (CASE WHEN tcir.fl_aplica_replica = 'S' THEN 'Sim' ELSE 'Não' END) AS fl_aplica_replica
              FROM projetos.treinamento_colaborador_item_replica tcir 
              JOIN projetos.treinamento_colaborador_item tci
                ON tci.cd_treinamento_colaborador_item = tcir.cd_treinamento_colaborador_item
              JOIN projetos.treinamento_colaborador tc
                ON tc.numero = tci.numero
               AND tc.ano = tci.ano
              JOIN projetos.treinamento_colaborador_tipo tct
                ON tct.cd_treinamento_colaborador_tipo = tc.cd_treinamento_colaborador_tipo
             WHERE tci.cd_registro_empregado = ".intval($cd_registro_empregado)."
               AND tci.dt_exclusao IS NULL
               ".(trim($args['nome']) != '' ? "AND UPPER(tc.nome) LIKE UPPER('%".trim($args['nome'])."%')" : "")."
			   ".(trim($args['numero']) != '' ? "AND tc.numero = ".intval($args['numero']) : "")."
			   ".(trim($args['ano']) != '' ? "AND tc.ano = ".intval($args['ano']) : "")."
			   ".(((trim($args['dt_inicio_ini']) != '') AND (trim($args['dt_inicio_fim']) != '')) ? " AND DATE_TRUNC('day', tc.dt_inicio) BETWEEN TO_DATE('".$args['dt_inicio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inicio_fim']."', 'DD/MM/YYYY')" : "")."						 
               ".(((trim($args['dt_final_ini']) != '') AND (trim($args['dt_final_fim']) != '')) ? " AND DATE_TRUNC('day', tc.dt_final) BETWEEN TO_DATE('".$args['dt_final_ini']."', 'DD/MM/YYYY') AND TO_yDATE('".$args['dt_final_fim']."', 'DD/MM/YYYY')" : "")."
               ".(trim($args['cd_treinamento_colaborador_tipo']) != '' ? "AND tc.cd_treinamento_colaborador_tipo = '".intval($args['cd_treinamento_colaborador_tipo'])."'" : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

	public function get_tipo()
	{
		$qr_sql = "
			SELECT cd_treinamento_colaborador_tipo AS value,
                   ds_treinamento_colaborador_tipo AS text
              FROM projetos.treinamento_colaborador_tipo
             WHERE dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
	}

    public function carrega($cd_treinamento_colaborador_item)
    {
        $qr_sql = "
            SELECT tc.nome,
                   funcoes.nr_treinamento_colaborador(tc.ano, tc.numero) AS numero,
                   funcoes.get_usuario_nome(tcir.cd_usuario_concluido) AS ds_usuario,
                   tc.promotor,
                   tc.cidade,
                   tc.uf,
                   TO_CHAR(tc.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
                   TO_CHAR(tc.dt_final, 'DD/MM/YYYY') AS dt_final,
                   TO_CHAR(tcir.dt_concluido, 'DD/MM/YYYY') AS dt_finalizado,
                   tc.cd_treinamento_colaborador,
                   tct.ds_treinamento_colaborador_tipo,
                   tci.cd_treinamento_colaborador_item,
                   tcir.cd_treinamento_colaborador_item_replica,
                   tcir.ds_justificativa,
                   (CASE WHEN tcir.fl_aplica_replica = 'S' THEN 'Sim' ELSE 'Não' END) AS fl_aplica,
                   fl_aplica_replica,
                   tcir.dt_concluido,
                   TO_CHAR(tcir.dt_limite, 'DD/MM/YYYY') AS dt_limite
              FROM projetos.treinamento_colaborador_item_replica tcir 
              JOIN projetos.treinamento_colaborador_item tci
                ON tci.cd_treinamento_colaborador_item = tcir.cd_treinamento_colaborador_item
              JOIN projetos.treinamento_colaborador tc
                ON tc.numero = tci.numero
               AND tc.ano = tci.ano
              JOIN projetos.treinamento_colaborador_tipo tct
                ON tct.cd_treinamento_colaborador_tipo = tc.cd_treinamento_colaborador_tipo
             WHERE tci.cd_treinamento_colaborador_item = ".intval($cd_treinamento_colaborador_item)."
               AND tci.dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function salvar($args = array())
    {
        $qr_sql = "
            UPDATE projetos.treinamento_colaborador_item_replica
               SET fl_aplica_replica    = ".(trim($args['fl_aplica_replica']) != '' ? str_escape($args['fl_aplica_replica']) : "DEFAULT").", 
                   ds_justificativa     = ".(trim($args['ds_justificativa']) != '' ? str_escape($args['ds_justificativa']) : "DEFAULT").", 
                   dt_limite            = ".(trim($args['dt_limite']) != '' ? "TO_TIMESTAMP('".trim($args['dt_limite'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").", 
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_treinamento_colaborador_item_replica = ".intval($args['cd_treinamento_colaborador_item_replica']).";";

        $this->db->query($qr_sql);
    }

    public function finalizar($cd_treinamento_colaborador_item_replica, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.treinamento_colaborador_item_replica
               SET cd_usuario_concluido = ".intval($cd_usuario).",
                   dt_concluido         = CURRENT_TIMESTAMP
             WHERE cd_treinamento_colaborador_item_replica = ".intval($cd_treinamento_colaborador_item_replica).";";

        $this->db->query($qr_sql);
    }

    public function lista_acompanhamento($cd_treinamento_colaborador_item_replica)
    {
        $qr_sql = "
            SELECT cd_treinamento_colaborador_item_replica_acompanhamento,
                   ds_acompanhamento,
                   TO_CHAR(dt_inclusao, 'DD/MM/YYYY') AS dt_inclusao,
                   funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
              FROM projetos.treinamento_colaborador_item_replica_acompanhamento 
             WHERE cd_treinamento_colaborador_item_replica = ".intval($cd_treinamento_colaborador_item_replica)."
               AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function salvar_acompanhamento($args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.treinamento_colaborador_item_replica_acompanhamento
                 (
                    cd_treinamento_colaborador_item_replica,
                    ds_acompanhamento, 
                    cd_usuario_inclusao,
                    cd_usuario_alteracao
                 )
            VALUES 
                 (
                   ".(intval($args['cd_treinamento_colaborador_item_replica']) > 0 ? intval($args['cd_treinamento_colaborador_item_replica']) : "DEFAULT").",
                   ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",                   
                   ".intval($args['cd_usuario']).",
                   ".intval($args['cd_usuario'])."
                 );";

        $this->db->query($qr_sql);
    }

    public function atualizar_acompanhamento($cd_treinamento_colaborador_item_replica_acompanhamento, $args = array())
    {
        $qr_sql = "
            UPDATE projetos.treinamento_colaborador_item_replica_acompanhamento
               SET ds_acompanhamento    = ".(trim($args['ds_acompanhamento']) != '' ? str_escape($args['ds_acompanhamento']) : "DEFAULT").",
                   cd_usuario_alteracao = ".intval($args['cd_usuario']).",
                   dt_alteracao         = CURRENT_TIMESTAMP
             WHERE cd_treinamento_colaborador_item_replica_acompanhamento = ".intval($cd_treinamento_colaborador_item_replica_acompanhamento).";";

        $this->db->query($qr_sql);
    }

    public function carrega_acompanhamento($cd_treinamento_colaborador_item_replica_acompanhamento)
    {
        $qr_sql = "
            SELECT cd_treinamento_colaborador_item_replica_acompanhamento,
                   ds_acompanhamento 
              FROM projetos.treinamento_colaborador_item_replica_acompanhamento
             WHERE cd_treinamento_colaborador_item_replica_acompanhamento = ".intval($cd_treinamento_colaborador_item_replica_acompanhamento)."
               AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function verifica_acompanhamento($cd_treinamento_colaborador_item_replica)
    {
        $qr_sql = "
            SELECT dt_concluido
              FROM projetos.treinamento_colaborador_item_replica
             WHERE cd_treinamento_colaborador_item_replica = ".intval($cd_treinamento_colaborador_item_replica)."
               AND dt_exclusao IS NULL;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function excluir($cd_treinamento_colaborador_item_replica_acompanhamento, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.treinamento_colaborador_item_replica_acompanhamento
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_treinamento_colaborador_item_replica_acompanhamento = ".intval($cd_treinamento_colaborador_item_replica_acompanhamento).";";

        $this->db->query($qr_sql);
    }
}