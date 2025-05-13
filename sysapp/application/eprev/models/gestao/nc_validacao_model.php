<?php
class Nc_validacao_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

	public function carrega($cd_nao_conformidade)
	{
		$qr_sql = "
			SELECT nc.cd_nao_conformidade,
                   TO_CHAR(nc.dt_cadastro,'DD/MM/YYYY') AS dt_cadastro,
                   TO_CHAR(nc.dt_alteracao,'DD/MM/YYYY') AS dt_alteracao,
                   TO_CHAR((nc.dt_cadastro + '15 days'),'DD/MM/YYYY') AS dt_limite_apres,
                   CASE WHEN CURRENT_DATE <= CAST((nc.dt_cadastro + '15 days') AS DATE)  
                        THEN 'N'
                        ELSE 'S'
                   END AS fl_limite_apres,
                   CASE WHEN (COALESCE(nc.disposicao,'') = '' OR COALESCE(nc.causa,'') = '')
                        THEN 'N'
                        ELSE 'S'
                   END AS fl_apresenta_ac,
                   nc.cd_processo,                  
                   pp.procedimento AS ds_processo,
                   nc.descricao,                 
                   nc.disposicao,                
                   nc.evidencias,				  
                   nc.acao_corretiva,			  
                   nc.causa,                     
                   TO_CHAR(nc.data_fechamento,'DD/MM/YYYY') AS dt_encerramento,   
                   TO_CHAR(nc.dt_implementacao,'DD/MM/YYYY') AS dt_implementacao,	
                   nc.cd_responsavel,           
                   funcoes.get_usuario_nome(nc.cd_responsavel) AS ds_responsavel,
                   funcoes.get_usuario_nome(nc.cd_substituto) AS ds_substituto,
                   nc.cd_gerente,				  
                   nc.aberto_por,           
                   funcoes.get_usuario_nome(nc.aberto_por) AS aberto_por_nome,
                   funcoes.nr_nc(nc.nr_ano,nc.nr_nc) AS numero_cad_nc,   	      
                   pp.envolvidos,
                   COALESCE(ac.cd_nao_conformidade,0) AS fl_ac,
				   nc.cd_substituto,
				   nc.cd_nao_conformidade_origem_evento,
				   ncoe.ds_nao_conformidade_origem_evento,
				   nc.ds_analise_abrangencia
              FROM projetos.nao_conformidade nc
			  JOIN projetos.nao_conformidade_origem_evento ncoe
			    ON ncoe.cd_nao_conformidade_origem_evento = nc.cd_nao_conformidade_origem_evento
			  LEFT JOIN projetos.processos pp                       
			 	ON pp.cd_processo = nc.cd_processo
			  LEFT JOIN projetos.acao_corretiva ac
				ON ac.cd_nao_conformidade = nc.cd_nao_conformidade
             WHERE nc.cd_nao_conformidade = ".intval($cd_nao_conformidade).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_usuario()
    {
        $qr_sql = "
            SELECT uc.codigo AS value,
                   uc.nome AS text
              FROM projetos.usuarios_controledi uc
             WHERE uc.divisao NOT IN ('FC','SNG','CF','CEE')
             ORDER BY uc.nome;";

        return $this->db->query($qr_sql)->result_array();
    }

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.rig
			     (
			       dt_referencia,
			       arquivo,
			       arquivo_nome,
			       cd_usuario_inclusao,
               	   cd_usuario_alteracao
			     )
			VALUES
			     (
				    ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
				    ".(trim($args['arquivo']) != "" ? "'".$args['arquivo']."'" : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != "" ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
			     );";

		$this->db->query($qr_sql); 

	}

	public function atualizar($cd_rig, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.rig
               SET dt_referencia        = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   arquivo_nome         = ".(trim($args['arquivo_nome']) != "" ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
                   arquivo              = ".(trim($args['arquivo']) != "" ? "'".$args['arquivo']."'" : "DEFAULT").",
			       cd_usuario_alteracao = ".intval($args['cd_usuario']).", 
			       dt_alteracao         =  CURRENT_TIMESTAMP                   
             WHERE cd_rig = ".intval($cd_rig).";";

        $this->db->query($qr_sql);  
	}

	public function get_rig()
	{
		$qr_sql = "
			SELECT arquivo, 
			       arquivo_nome
              FROM gestao.rig
             WHERE dt_exclusao IS NULL
             ORDER BY dt_referencia DESC 
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
	}

	public function enviar($cd_rig, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.rig
               SET cd_usuario_envio = ".intval($cd_usuario).", 
			       dt_envio         =  CURRENT_TIMESTAMP
             WHERE cd_rig = ".intval($cd_rig).";";

        $this->db->query($qr_sql);  
	}
}
?>