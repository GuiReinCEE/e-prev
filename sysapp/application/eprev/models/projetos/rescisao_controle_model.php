<?php
class Rescisao_controle_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar(&$result, $args=array())
	{
		$qr_sql = "
			SELECT t.cd_empresa,
				   t.cd_registro_empregado,
				   t.seq_dependencia,
				   p.nome,
				   TO_CHAR(t.dt_demissao, 'DD/MM/YYYY') AS dt_demissao,
				   TO_CHAR(t.dt_digita_demissao, 'DD/MM/YYYY HH24:MI:SS') AS dt_digita_demissao,
				   TO_CHAR(rc.dt_rescisao, 'DD/MM/YYYY') AS dt_rescisao,
				   TO_CHAR(rc.dt_envio_email, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_email,
				   CASE WHEN (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') THEN 's'
				        ELSE 'n'
				   END AS fl_email,
				   CASE WHEN rc.dt_envio_email IS NOT NULL THEN 'Enviado'
				        WHEN rc.dt_envio_email IS NULL AND rc.dt_rescisao IS NOT NULL THEN 'Aguardando Envio'
						ELSE 'Não enviado'
				   END AS status,
				   CASE WHEN rc.dt_envio_email IS NOT NULL THEN 'green'
				        WHEN rc.dt_envio_email IS  NULL AND rc.dt_rescisao IS NOT NULL THEN 'blue'
						ELSE 'gray'
				   END AS color_status,
				   CASE WHEN rc.dt_envio_email IS NOT NULL THEN 'label label-success'
				        WHEN rc.dt_envio_email IS  NULL AND rc.dt_rescisao IS NOT NULL THEN 'label label-info'
						ELSE 'label'
				   END AS class_status,
				   uc.nome AS nome_usuario,
				   (EXTRACT(YEAR FROM AGE(CURRENT_DATE, p.dt_nascimento))) AS idade
			  FROM public.titulares t
			  JOIN public.participantes p
				ON p.cd_empresa            = t.cd_empresa
			   AND p.cd_registro_empregado = t.cd_registro_empregado
			   AND p.seq_dependencia       = t.seq_dependencia
			  LEFT JOIN projetos.rescisao_controle rc
				ON rc.cd_empresa            = p.cd_empresa
			   AND rc.cd_registro_empregado = p.cd_registro_empregado
			   AND rc.seq_dependencia       = p.seq_dependencia
			  LEFT JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = rc.cd_usuario_envio
			  JOIN patrocinadoras pa
                ON pa.cd_empresa = p.cd_empresa			  
			 WHERE t.dt_demissao IS NOT NULL
			   AND p.dt_recebimento_compl_apos IS NULL
			   AND pa.tipo_cliente = 'P'
			   AND (EXTRACT(YEAR FROM AGE(CURRENT_DATE, p.dt_nascimento))) >= 50
			   ".(((trim($args['dt_digita_demissao_ini']) != "") and  (trim($args['dt_digita_demissao_fim']) != "")) ? " AND CAST(t.dt_digita_demissao AS DATE) BETWEEN TO_DATE('".$args['dt_digita_demissao_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_digita_demissao_fim']."','DD/MM/YYYY')" : "")."
			   ".(trim($args['cd_empresa']) != '' ? "AND t.cd_empresa = ".intval($args['cd_empresa']) : "")."
			   ".(trim($args['fl_status']) == 'A' ? "AND rc.dt_envio_email IS NULL AND rc.dt_rescisao IS NOT NULL" : "")."
			   ".(trim($args['fl_status']) == 'E' ? "AND rc.dt_envio_email IS NOT NULL" : "")."
			   ".(trim($args['fl_status']) == 'N' ? "AND rc.dt_rescisao IS NULL" : "")."
			   ".(trim($args['fl_email']) != '' ? "AND (CASE WHEN (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%') THEN 'S' ELSE 'N' END) = '".trim($args['fl_email'])."'" : "")."
			 ORDER BY t.dt_digita_demissao DESC;";

		$result = $this->db->query($qr_sql);
	}
	
	function adicionar(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.rescisao_controle
			     (
                   cd_empresa, 
				   cd_registro_empregado, 
				   seq_dependencia, 
				   dt_rescisao, 
				   dt_digita_rescisao,
				   cd_usuario_inclusao
				 )
				   SELECT cd_empresa,
						  cd_registro_empregado,
						  seq_dependencia,
				          dt_demissao,
				          dt_digita_demissao,
						  ".intval($args['cd_usuario'])."
				     FROM titulares
				    WHERE cd_empresa              = ".intval($args['cd_empresa'])."
			          AND cd_registro_empregado   = ".intval($args['cd_registro_empregado'])."
			          AND seq_dependencia         = ".intval($args['seq_dependencia']).";";
				 
		$result = $this->db->query($qr_sql);
	}
	
	function remover(&$result, $args=array())
	{
		$qr_sql = "
			DELETE
			  FROM projetos.rescisao_controle
			 WHERE cd_empresa            = ".intval($args['cd_empresa'])."
			   AND cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
			   AND seq_dependencia       = ".intval($args['seq_dependencia']).";";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function enviar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.rescisao_controle
			   SET dt_envio_email   = CURRENT_TIMESTAMP,
			       cd_usuario_envio = ".intval($args['cd_usuario'])."
			 WHERE dt_envio_email IS NULL;";
	
		$result = $this->db->query($qr_sql);
	}
	
	function listar_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT rca.cd_rescisao_controle_acompanhamento,
				   TO_CHAR(rca.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   descricao,
			       uc.nome
			  FROM projetos.rescisao_controle_acompanhamento rca
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = rca.cd_usuario_inclusao
			 WHERE rca.dt_exclusao IS NULL
			   AND rca.cd_empresa              = ".intval($args['cd_empresa'])."
			   AND rca.cd_registro_empregado   = ".intval($args['cd_registro_empregado'])."
			   AND rca.seq_dependencia         = ".intval($args['seq_dependencia']).";";
	
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.rescisao_controle_acompanhamento
			     (
                   cd_empresa, 
				   cd_registro_empregado, 
                   seq_dependencia, 
				   descricao, 
				   cd_usuario_inclusao
                 )
            VALUES 
			     (
				   ".intval($args['cd_empresa']).",
				   ".intval($args['cd_registro_empregado']).",
				   ".intval($args['seq_dependencia']).",
				   ".str_escape($args['descricao']).",
				   ".intval($args['cd_usuario'])."
				 );";
			
		$result = $this->db->query($qr_sql);
	}

}
?>