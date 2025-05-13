<?php
class Avaliacao_treinamento_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($cd_usuario, $args = array())
	{
		$qr_sql = "
			SELECT tcr.cd_treinamento_colaborador_resposta,
				   tci.nome AS colaborador,
			       tc.nome,
				   tc.promotor,
				   TO_CHAR(tc.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
			       TO_CHAR(tc.dt_final, 'DD/MM/YYYY') AS dt_final,
				   CASE WHEN tcr.dt_finalizado IS NOT NULL AND tcr.ds_justificativa_finalizado IS NOT NULL THEN 'Finalizado pelo RH'
				        WHEN tcr.ds_formulario_respondido IS NULL THEN 'Aguardando Início'
						WHEN tcr.ds_formulario_respondido IS NOT NULL AND tcr.dt_finalizado IS NULL THEN 'Em Andamento'
						WHEN tcr.dt_finalizado IS NOT NULL THEN 'Finalizado'
						ELSE ''
				   END AS status,
				   CASE WHEN tcr.dt_finalizado IS NOT NULL AND tcr.ds_justificativa_finalizado IS NOT NULL THEN 'label label-success'
				        WHEN tcr.ds_formulario_respondido IS NULL THEN 'label label-important'
						WHEN tcr.ds_formulario_respondido IS NOT NULL AND tcr.dt_finalizado IS NULL THEN 'label label-info'
						WHEN tcr.dt_finalizado IS NOT NULL THEN 'label label-success'
						ELSE ''
				   END AS status_label,
				   CASE WHEN tcr.dt_finalizado IS NOT NULL AND tcr.ds_justificativa_finalizado IS NOT NULL THEN ''
				        WHEN tcr.dt_finalizado IS NOT NULL THEN 'S'
						ELSE 'N'
				   END AS fl_finalizado,
				   TO_CHAR(tcr.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(tcr.dt_finalizado, 'DD/MM/YYYY HH24:MI:SS') AS dt_finalizado
			  FROM projetos.treinamento_colaborador_resposta tcr
			  JOIN projetos.treinamento_colaborador_item tci
			    ON tci.cd_treinamento_colaborador_item = tcr.cd_treinamento_colaborador_item
		      JOIN projetos.treinamento_colaborador tc
			    ON tc.cd_treinamento_colaborador = tcr.cd_treinamento_colaborador
			 WHERE tc.dt_exclusao IS NULL
			   AND tci.dt_exclusao IS NULL
			   AND tcr.cd_usuario = ".intval($cd_usuario)."
			   ".(trim($args['nome']) != '' ? "AND UPPER(tc.nome) LIKE UPPER('%".trim($args['nome'])."%')" : "")."
			   ".(trim($args['status']) == 'I' ? "AND tcr.dt_finalizado IS NULL AND tcr.ds_justificativa_finalizado IS NOT NULL AND tcr.ds_formulario_respondido IS NOT NULL" : "")."
			   ".(trim($args['status']) == 'A' ? "AND tcr.dt_finalizado IS NULL AND tcr.ds_formulario_respondido IS NOT NULL" : "")."
			   ".(trim($args['status']) == 'F' ? "AND tcr.dt_finalizado IS NOT NULL" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_treinamento_colaborador_resposta)
	{
		$qr_sql = "
			SELECT tcr.cd_treinamento_colaborador_resposta,
				   tcr.cd_treinamento_colaborador_formulario,
				   tcf.fl_enviar_para,
				   CASE WHEN tcf.fl_enviar_para = 'C' THEN 'Colaborador'
						ELSE 'Gestor'
				   END AS enviar_para,
				   tcf.ds_treinamento_colaborador_formulario,
				   tc.cd_treinamento_colaborador,
				   tci.nome AS colaborador,
			       tc.nome,
				   tc.promotor,
				   TO_CHAR(tc.dt_inicio, 'DD/MM/YYYY') AS dt_inicio,
			       TO_CHAR(tc.dt_final, 'DD/MM/YYYY') AS dt_final,
				   CASE WHEN tcr.ds_formulario_respondido IS NULL THEN 'Aguardando Início'
						WHEN tcr.ds_formulario_respondido IS NOT NULL AND tcr.dt_finalizado IS NULL THEN 'Em Andamento'
						WHEN tcr.dt_finalizado IS NOT NULL THEN 'Finalizado'
						ELSE ''
				   END AS status,
				   CASE WHEN tcr.ds_formulario_respondido IS NULL THEN 'label label-important'
						WHEN tcr.ds_formulario_respondido IS NOT NULL AND tcr.dt_finalizado IS NULL THEN 'label label-info'
						WHEN tcr.dt_finalizado IS NOT NULL THEN 'label label-success'
						ELSE ''
				   END AS status_label,
				   CASE WHEN tcr.dt_finalizado IS NOT NULL THEN 'S'
						ELSE 'N'
				   END AS fl_finalizado,
				   TO_CHAR(tcr.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   TO_CHAR(tcr.dt_alteracao, 'DD/MM/YYYY HH24:MI:SS') AS dt_alteracao,
				   TO_CHAR(tcr.dt_finalizado, 'DD/MM/YYYY HH24:MI:SS') AS dt_finalizado,
				   tcr.cd_usuario,
				   tcr.ds_formulario_respondido,
				   tcr.ds_formulario
			  FROM projetos.treinamento_colaborador_resposta tcr
			  JOIN projetos.treinamento_colaborador_formulario tcf
				ON tcf.cd_treinamento_colaborador_formulario = tcr.cd_treinamento_colaborador_formulario
			  JOIN projetos.treinamento_colaborador_item tci
			    ON tci.cd_treinamento_colaborador_item = tcr.cd_treinamento_colaborador_item
			  JOIN projetos.treinamento_colaborador tc
			    ON tc.cd_treinamento_colaborador = tcr.cd_treinamento_colaborador
			 WHERE tc.dt_exclusao IS NULL
			   AND tcr.cd_treinamento_colaborador_resposta = ".intval($cd_treinamento_colaborador_resposta).";";
			   
		return $this->db->query($qr_sql)->row_array();
	}

	public function atualizar_formulario($cd_treinamento_colaborador_resposta, $ds_formulario, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.treinamento_colaborador_resposta
			   SET ds_formulario        = '".trim($ds_formulario)."',
			       cd_usuario_alteracao = ".intval($cd_usuario).",
                   dt_alteracao         = CURRENT_TIMESTAMP
			 WHERE cd_treinamento_colaborador_resposta = ".intval($cd_treinamento_colaborador_resposta).";";

		$this->db->query($qr_sql);
	}

	public function atualizar_resposta($cd_treinamento_colaborador_resposta, $ds_formulario_respondido, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.treinamento_colaborador_resposta
			   SET ds_formulario_respondido = ".str_escape($ds_formulario_respondido).",
			       cd_usuario_alteracao     = ".intval($cd_usuario).",
                   dt_alteracao             = CURRENT_TIMESTAMP
			 WHERE cd_treinamento_colaborador_resposta = ".intval($cd_treinamento_colaborador_resposta).";";

		$this->db->query($qr_sql);
	}

	public function finalizar_avaliacao($cd_treinamento_colaborador_resposta, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.treinamento_colaborador_resposta
			   SET cd_usuario_finalizado     = ".intval($cd_usuario).",
                   dt_finalizado             = CURRENT_TIMESTAMP
			 WHERE cd_treinamento_colaborador_resposta = ".intval($cd_treinamento_colaborador_resposta).";";

		$this->db->query($qr_sql);
	}
}