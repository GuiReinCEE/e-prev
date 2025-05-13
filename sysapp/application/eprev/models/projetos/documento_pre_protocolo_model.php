<?php
class documento_pre_protocolo_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

	function listar(&$result, $args=array())
    {	
		$qr_sql = "
			SELECT dpp.cd_documento_pre_protocolo, 
			       dpp.cd_tipo_doc, 
				   dpp.cd_empresa, 
				   dpp.cd_registro_empregado, 
                   dpp.seq_dependencia, 
				   dpp.ds_observacao, 
				   dpp.nr_folha, 
				   dpp.arquivo, 
				   dpp.arquivo_nome, 
                   dpp.fl_descartar, 
				   TO_CHAR(dpp.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   dpp.dt_criacao_protocolo, 
                   dpp.cd_usuario_criacao_protocolo,
				   COALESCE(td.nome_documento,'Não informado') AS descricao_documento,
				   uc.nome AS usuario,
				   dpp.nome
              FROM projetos.documento_pre_protocolo dpp
			  LEFT JOIN public.tipo_documentos td
                ON dpp.cd_tipo_doc = td.cd_tipo_doc
			  JOIN projetos.usuarios_controledi uc 
                ON dpp.cd_usuario_inclusao = uc.codigo
			 WHERE dpp.dt_exclusao IS NULL
			   AND dpp.dt_criacao_protocolo IS NULL
			   ".(trim($args['fl_protocolo']) == 'PD' ? "AND dpp.cd_empresa IS NOT NULL AND dpp.cd_registro_empregado IS NOT NULL AND dpp.seq_dependencia IS NOT NULL" : "")."
			   ".(((trim($args['fl_protocolo']) == "PD") AND (trim($args['fl_tipo_protocolo']) == "D")) ? " AND dpp.arquivo IS NOT NULL AND dpp.arquivo_nome IS NOT NULL" : "")."
			   ".(((trim($args['fl_protocolo']) == "PD") AND (trim($args['fl_tipo_protocolo']) == "P")) ? " AND dpp.arquivo IS NULL AND dpp.arquivo_nome IS NULL" : "").";";

        $result = $this->db->query($qr_sql);
    }
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_documento_pre_protocolo, 
			       cd_tipo_doc, 
				   cd_empresa, 
				   cd_registro_empregado, 
                   seq_dependencia, 
				   ds_observacao, 
				   nr_folha, 
				   arquivo, 
				   arquivo_nome, 
                   fl_descartar,
				   'N' AS fl_manter,
				   nome
              FROM projetos.documento_pre_protocolo
			 WHERE cd_documento_pre_protocolo = ".intval($args['cd_documento_pre_protocolo']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_documento_pre_protocolo']) == 0)
		{
			$qr_sql = "
				INSERT INTO projetos.documento_pre_protocolo
				     (
						cd_tipo_doc, 
						cd_empresa, 
						cd_registro_empregado, 
						seq_dependencia, 
						ds_observacao, 
						nr_folha, 
						fl_descartar, 
						arquivo,
						arquivo_nome,
						nome,
						cd_usuario_inclusao, 
						cd_usuario_alteracao
					 )
				VALUES 
				     (
					   ".(trim($args['cd_tipo_doc']) != '' ? intval($args['cd_tipo_doc']) : "DEFAULT").",
					   ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
					   ".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
					   ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
					   ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					   ".(trim($args['nr_folha']) != '' ? intval($args['nr_folha']) : "DEFAULT").",
					   ".(trim($args['fl_descartar']) != '' ? str_escape($args['fl_descartar']) : "DEFAULT").",
					   ".(trim($args['arquivo']) != "" ? "'".$args['arquivo']."'" : "DEFAULT").",
					   ".(trim($args['arquivo_nome']) != "" ? "'".utf8_decode($args['arquivo_nome'])."'" : "DEFAULT").",
					   ".(trim($args['nome']) != "" ? str_escape($args['nome']) : "DEFAULT").",
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$qr_sql = "
				UPDATE projetos.documento_pre_protocolo
				   SET cd_tipo_doc           = ".(trim($args['cd_tipo_doc']) != '' ? intval($args['cd_tipo_doc']) : "DEFAULT").",
				       cd_empresa            = ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
					   cd_registro_empregado = ".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
					   seq_dependencia       = ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
					   ds_observacao         = ".(trim($args['ds_observacao']) != '' ? str_escape($args['ds_observacao']) : "DEFAULT").",
					   nr_folha              = ".(trim($args['nr_folha']) != '' ? intval($args['nr_folha']) : "DEFAULT").",
					   fl_descartar          = ".(trim($args['fl_descartar']) != '' ? str_escape($args['fl_descartar']) : "DEFAULT").",
					   arquivo               = ".(trim($args['arquivo']) != "" ? "'".$args['arquivo']."'" : "DEFAULT").",
					   arquivo_nome          = ".(trim($args['arquivo_nome']) != "" ? "'".utf8_decode($args['arquivo_nome'])."'" : "DEFAULT").",
					   nome                  = ".(trim($args['nome']) != "" ? str_escape($args['nome']) : "DEFAULT").",
					   cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
					   dt_alteracao          = CURRENT_TIMESTAMP
				 WHERE cd_documento_pre_protocolo = ".intval($args['cd_documento_pre_protocolo']).";";
		}
		
		$result = $this->db->query($qr_sql);
	}
	
	function descartar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT fl_descarte 
			  FROM projetos.documento_protocolo_descarte 
			 WHERE dt_exclusao IS NULL
			   AND cd_documento = ".intval($args['cd_tipo_doc'])."
			   AND cd_divisao   = '".trim($args['cd_divisao'])."';";
					  
        $result = $this->db->query($qr_sql);
    }
	
	function gera_protocolo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.documento_pre_protocolo
			   SET cd_documento                  = ".(trim($args['cd_documento']) != '' ? intval($args['cd_documento']) : "DEFAULT").",
				   tipo_documento_criado         = ".(trim($args['tipo_documento_criado']) != '' ? "'".trim($args['tipo_documento_criado'])."'" : "DEFAULT").",
				   cd_usuario_criacao_protocolo  = ".intval($args['cd_usuario']).",
				   dt_criacao_protocolo          = CURRENT_TIMESTAMP
			 WHERE cd_documento_pre_protocolo = ".intval($args['cd_documento_pre_protocolo']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function excluir(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.documento_pre_protocolo
			   SET cd_usuario_exclusao  = ".intval($args['cd_usuario']).",
				   dt_exclusao          = CURRENT_TIMESTAMP
			 WHERE cd_documento_pre_protocolo = ".intval($args['cd_documento_pre_protocolo']).";";
			 
		$result = $this->db->query($qr_sql);
	}
}
?>