<?php
class demonstrativo_estatistico_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

	public function listar($args = array())
	{
		$qr_sql = "
			SELECT ce.cd_demonstrativo_estatistico,
			       TO_CHAR(ce.dt_referencia, 'YYYY/MM') AS dt_referencia,
			       TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       ce.arquivo,
			       ce.arquivo_nome,
			       ce.arquivo_planilha,
			       ce.arquivo_planilha_nome,
			       ce.arquivo_ceeeprev,
			       ce.arquivo_ceeeprev_nome,
			       ce.arquivo_ceeeprev_planilha,
			       ce.arquivo_ceeeprev_planilha_nome,
			       (CASE WHEN (SELECT ce2.cd_demonstrativo_estatistico
			                     FROM gestao.demonstrativo_estatistico ce2
			                    WHERE ce2.dt_exclusao IS NULL
			                    ORDER BY ce2.dt_referencia DESC 
			                    LIMIT 1) = ce.cd_demonstrativo_estatistico
			                    AND '".trim($args['fl_envio'])."' = 'N'

			             THEN 'S'
			             ELSE 'N'
			        END) AS fl_editar
			  FROM gestao.demonstrativo_estatistico ce
			 WHERE ce.dt_exclusao IS NULL
			 ".(trim($args['fl_envio']) == 'S' ? "AND ce.dt_envio IS NOT NULL" : "").";";

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_demonstrativo_estatistico)
	{
		$qr_sql = "
			SELECT cd_demonstrativo_estatistico,
			       TO_CHAR(dt_referencia, 'YYYY/MM') AS dt_referencia,
			       TO_CHAR(dt_referencia, 'MM/YYYY') AS mes_ano,
			       TO_CHAR(dt_referencia, 'YYYY') AS nr_ano,
			       TO_CHAR(dt_referencia, 'MM') AS nr_mes,
			       arquivo,
			       arquivo_nome,
			       arquivo_planilha,
			       arquivo_planilha_nome,
			       arquivo_ceeeprev,
			       arquivo_ceeeprev_nome,
			       arquivo_ceeeprev_planilha,
			       arquivo_ceeeprev_planilha_nome,
			       TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
			       funcoes.get_usuario_nome(cd_usuario_envio) AS ds_usuario_envio
			  FROM gestao.demonstrativo_estatistico 
			 WHERE cd_demonstrativo_estatistico = ".intval($cd_demonstrativo_estatistico).";";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$qr_sql = "
			INSERT INTO gestao.demonstrativo_estatistico
			     (
			       dt_referencia,
			       arquivo,
			       arquivo_nome,
			       arquivo_planilha,
			       arquivo_planilha_nome,
			       arquivo_ceeeprev,
			       arquivo_ceeeprev_nome,
			       arquivo_ceeeprev_planilha,
			       arquivo_ceeeprev_planilha_nome,
			       cd_usuario_inclusao,
               	   cd_usuario_alteracao
			     )
			VALUES
			     (
				    ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
				    ".(trim($args['arquivo']) != "" ? "'".$args['arquivo']."'" : "DEFAULT").",
                    ".(trim($args['arquivo_nome']) != "" ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
                    ".(trim($args['arquivo_planilha']) != "" ? "'".$args['arquivo_planilha']."'" : "DEFAULT").",
                    ".(trim($args['arquivo_planilha_nome']) != "" ? "'".$args['arquivo_planilha_nome']."'" : "DEFAULT" ).",
                    ".(trim($args['arquivo_ceeeprev']) != "" ? "'".$args['arquivo_ceeeprev']."'" : "DEFAULT").",
                    ".(trim($args['arquivo_ceeeprev_nome']) != "" ? "'".$args['arquivo_ceeeprev_nome']."'" : "DEFAULT" ).",
                    ".(trim($args['arquivo_ceeeprev_planilha']) != "" ? "'".$args['arquivo_ceeeprev_planilha']."'" : "DEFAULT").",
                    ".(trim($args['arquivo_ceeeprev_planilha_nome']) != "" ? "'".$args['arquivo_ceeeprev_planilha_nome']."'" : "DEFAULT" ).",
				    ".intval($args['cd_usuario']).",
                    ".intval($args['cd_usuario'])."    
			     );";

		$this->db->query($qr_sql); 

	}

	public function atualizar($cd_demonstrativo_estatistico, $args = array())
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_estatistico
               SET dt_referencia                  = ".(trim($args['dt_referencia']) != '' ? "TO_DATE('".$args['dt_referencia']."', 'DD/MM/YYYY')" : "DEFAULT").",
                   arquivo_nome                   = ".(trim($args['arquivo_nome']) != "" ? "'".$args['arquivo_nome']."'" : "DEFAULT" ).",
                   arquivo                        = ".(trim($args['arquivo']) != "" ? "'".$args['arquivo']."'" : "DEFAULT").",
                   arquivo_planilha               = ".(trim($args['arquivo_planilha']) != "" ? "'".$args['arquivo_planilha']."'" : "DEFAULT" ).",
                   arquivo_planilha_nome          = ".(trim($args['arquivo_planilha_nome']) != "" ? "'".$args['arquivo_planilha_nome']."'" : "DEFAULT").",
                   arquivo_ceeeprev_nome          = ".(trim($args['arquivo_ceeeprev_nome']) != "" ? "'".$args['arquivo_ceeeprev_nome']."'" : "DEFAULT" ).",
                   arquivo_ceeeprev               = ".(trim($args['arquivo_ceeeprev']) != "" ? "'".$args['arquivo_ceeeprev']."'" : "DEFAULT").",
                   arquivo_ceeeprev_planilha      = ".(trim($args['arquivo_ceeeprev_planilha']) != "" ? "'".$args['arquivo_ceeeprev_planilha']."'" : "DEFAULT" ).",
                   arquivo_ceeeprev_planilha_nome = ".(trim($args['arquivo_ceeeprev_planilha_nome']) != "" ? "'".$args['arquivo_ceeeprev_planilha_nome']."'" : "DEFAULT").",
			       cd_usuario_alteracao           = ".intval($args['cd_usuario']).", 
			       dt_alteracao                   =  CURRENT_TIMESTAMP                   
             WHERE cd_demonstrativo_estatistico = ".intval($cd_demonstrativo_estatistico).";";

        $this->db->query($qr_sql);  
	}

	public function enviar($cd_demonstrativo_estatistico, $cd_usuario)
	{
		$qr_sql = "
			UPDATE gestao.demonstrativo_estatistico
               SET cd_usuario_envio = ".intval($cd_usuario).", 
			       dt_envio         =  CURRENT_TIMESTAMP
             WHERE cd_demonstrativo_estatistico = ".intval($cd_demonstrativo_estatistico).";";

        $this->db->query($qr_sql);  
	}

	function excluir($cd_demonstrativo_estatistico, $cd_usuario)
    {
        $qr_sql = "
                UPDATE gestao.demonstrativo_estatistico
                SET dt_exclusao = CURRENT_TIMESTAMP,
                cd_usuario_exclusao = ".intval($cd_usuario)."
                WHERE cd_demonstrativo_estatistico = ".intval($cd_demonstrativo_estatistico).";";
        
        $this->db->query($qr_sql);
    }
}
?>