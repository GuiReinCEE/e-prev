<?php
class Contribuicao_patroc_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT cp.cd_contribuicao_patroc,
				   cp.cd_empresa||'/'||cp.cd_registro_empregado||'/'||cp.seq_dependencia AS re,				   
				   projetos.participante_nome(cd_empresa, cd_registro_empregado, seq_dependencia) AS nome,
				   TO_CHAR(cp.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_solicitacao,
				   cp.ds_email,
				   cp.ds_telefone,
				   (SELECT cpa.dt_inclusao || ' : ' || cpa.ds_descricao
				      FROM autoatendimento.contribuicao_patroc_acompanhamento cpa
				     WHERE cpa.cd_contribuicao_patroc = cp.cd_contribuicao_patroc
				       AND cpa.dt_exclusao IS NULL
				     ORDER BY cpa.dt_inclusao DESC
				     LIMIT 1) AS ds_acompanhamento
			  FROM autoatendimento.contribuicao_patroc cp
			 WHERE 1 = 1
			   ".(trim($args['cd_empresa']) != '' ? "AND cp.cd_empresa = ".intval($args['cd_empresa']) : "")."
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND cp.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."			   
	           ".(trim($args['seq_dependencia']) != '' ? "AND cp.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
			   ".(((trim($args['dt_solicitacao_ini']) != '') AND (trim($args['dt_solicitacao_fim']) != '')) ? " AND DATE_TRUNC('day', cp.dt_inclusao) BETWEEN TO_DATE('".$args['dt_solicitacao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_solicitacao_fim']."', 'DD/MM/YYYY')" : "")."
	           ORDER BY cp.dt_inclusao;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_contribuicao_patroc)
	{
		$qr_sql = "
			SELECT cp.cd_contribuicao_patroc,
			       projetos.participante_nome(cp.cd_empresa, cp.cd_registro_empregado, cp.seq_dependencia) AS nome,
				   cp.cd_empresa||'/'||cp.cd_registro_empregado||'/'||cp.seq_dependencia AS re,
				   TO_CHAR(cp.dt_inclusao,'DD/MM/YYYY HH24:MI:SS') AS dt_solicitacao,
				   cp.ds_email,
				   cp.ds_telefone
			  FROM autoatendimento.contribuicao_patroc cp
			 WHERE cp.cd_contribuicao_patroc = ".intval($cd_contribuicao_patroc).";";

		return $this->db->query($qr_sql)->row_array();
	}

    public function listar_acompanhamento($cd_contribuicao_patroc)
    {
    	$qr_sql = "
			SELECT cd_contribuicao_patroc_acompanhamento,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       ds_descricao,
			       cd_usuario_inclusao,
			       funcoes.get_usuario_nome(cd_usuario_inclusao) AS ds_usuario_inclusao
			  FROM autoatendimento.contribuicao_patroc_acompanhamento
			 WHERE dt_exclusao IS NULL
			   AND cd_contribuicao_patroc = ".intval($cd_contribuicao_patroc).";";

    	return $this->db->query($qr_sql)->result_array();
    }

	public function salvar_acompanhamento($args = array())
	{
		$qr_sql = "
			INSERT INTO autoatendimento.contribuicao_patroc_acompanhamento
			     (
			       cd_contribuicao_patroc,
			       ds_descricao,
			       cd_usuario_inclusao,
               	   cd_usuario_alteracao
			     )                  
			VALUES
			     (
			        ".intval($args['cd_contribuicao_patroc']).",
			     	".(trim($args['ds_descricao']) != '' ? str_escape($args['ds_descricao']) : "DEFAULT").",                    		
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."
			     );";

        $this->db->query($qr_sql);
	}    

 	public function excluir_acompanhamento($cd_contribuicao_patroc_acompanhamento, $args = array())
    {
        $qr_sql = "
            UPDATE autoatendimento.contribuicao_patroc_acompanhamento
               SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_contribuicao_patroc_acompanhamento = ".intval($cd_contribuicao_patroc_acompanhamento).";";

        $this->db->query($qr_sql);
    }
}
?>