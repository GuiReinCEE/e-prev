<?php
class Opcao_envio_documento_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

	function listar(&$result, $args=array())
    {
		$qr_sql = "
			SELECT oe.cd_aa_opcao_envio,
			       p.nome,
				   p.cd_empresa || '/' || p.cd_registro_empregado || '/' || p.seq_dependencia AS re,
				   TO_CHAR(oe.dt_solicitacao, 'DD/MM/YYYY HH24:MI:SS') AS dt_solicitacao
			  FROM projetos.aa_opcao_envio oe
			  JOIN public.participantes p
				ON p.cd_empresa            = oe.cd_empresa
			   AND p.cd_registro_empregado = oe.cd_registro_empregado
			   AND p.seq_dependencia       = oe.seq_dependencia
			 WHERE dt_exclusao IS NULL
			   ".(trim($args['cd_empresa']) != '' ? "AND p.cd_empresa = ".intval($args['cd_empresa']) : '')."
			   ".(trim($args['cd_registro_empregado']) != '' ? "AND p.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : '')."
			   ".(trim($args['seq_dependencia'] != '') != '' ? "AND p.seq_dependencia = ".intval($args['seq_dependencia']) : '')."
			   ".(trim($args['nome']) != '' ? "AND UPPER(p.nome) LIKE UPPER('%".trim($args['nome'])."%')" : "")."
			   ".(((trim($args['dt_solicitao_ini']) != "") AND  (trim($args['dt_solicitao_fim']) != "")) ? " AND DATE_TRUNC('day', oe.dt_solicitacao) BETWEEN TO_DATE('".$args['dt_solicitao_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_solicitao_fim']."', 'DD/MM/YYYY')" : "")."
			 ORDER BY oe.cd_aa_opcao_envio DESC;";
			
		$result = $this->db->query($qr_sql);
	}
	
	function opcao(&$result, $args=array())
	{
		$qr_sql = "
			SELECT g.ds_grupo, 
				   CASE WHEN aoei.cd_opcao = 1 THEN 'ELETRÔNICO' 
				        ELSE 'IMPRESSO' 
				   END AS ds_opcao,
				   CASE WHEN aoei.cd_opcao = 1 THEN 'green' 
				        ELSE 'blue' 
			       END AS cor
			  FROM projetos.aa_opcao_envio_item aoei
		      JOIN public.grupos g
				ON g.cd_grupo = aoei.cd_grupo
			 WHERE aoei.cd_aa_opcao_envio = ".intval($args['cd_aa_opcao_envio'])."
			 ORDER BY g.ds_grupo;";
		
		$result = $this->db->query($qr_sql);
	}
	
}