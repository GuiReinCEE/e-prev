<?php
class Digitalizado_model extends Model
{
    function __construct()
	{
		parent::Model();
	}

    function listar(&$result, $args=array())
    {
        $this->load->library('listfiles', $args['ar_tipo']);
		
        $result = $this->listfiles->getFiles('../digitalizado/'.strtolower($args['dir']));  
    }
	
	function listaProtocoloInterno(&$result, $args=array())
	{
		$qr_sql = "
					SELECT dri.cd_documento_recebido,
					       funcoes.nr_documento_recebido(dr.nr_ano, dr.nr_contador) AS nr_documento_recebido
					  FROM projetos.documento_recebido_item dri
					  JOIN projetos.documento_recebido dr
					    ON dr.cd_documento_recebido = dri.cd_documento_recebido
					 WHERE dri.dt_exclusao IS NULL
					   AND UPPER(dri.arquivo) = UPPER('".$args['arquivo']."')
					ORDER BY dri.dt_cadastro
		          ";
		
		#echo "<pre style='text-align:left;'>".print_r($args,true).$qr_sql."</pre>"; #exit;				  		
		$result = $this->db->query($qr_sql);
	}
	
	function usuarioCombo(&$result, $args=array())
	{
		$qr_sql = "
					SELECT a.usuario AS value, 
						   a.nome || ' [' || a.usuario || ']' AS text 
					  FROM projetos.usuarios_controledi a 
					 WHERE a.divisao = '".$args['divisao']."'
					   AND a.tipo <> 'X'
					 ORDER BY a.nome
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function avisoGAP(&$result, $args=array())
	{
		if($args['email'] != "")
		{
			$qr_sql = "
						INSERT INTO projetos.envia_emails 
							 (
								dt_envio, 
								de, 
								para, 
								cc, 
								cco, 
								assunto, 
								texto,
								cd_evento
							 )
						VALUES 
							 (
								CURRENT_TIMESTAMP, 
								'Documentos Digitalizado',
								'".$args['email']."',
								'', 
								'',
								'Documentos Digitalizados não encaminhados',
								'Verifique os documentos digitalizados não encaminhados, no link abaixo.
								
http://www.e-prev.com.br/cieprev/index.php/ecrm/digitalizado								
								',
								135
							 );
					  ";
			#echo $qr_sql;
			$result = $this->db->query($qr_sql);
		}
	}	
	
    function notificacao(&$result, $args=array())
    {
        $qr_sql = "
					SELECT COUNT(*) AS qt_doc
					  FROM gestao.pendencia_doc_digitalizado()
					 WHERE funcoes.get_usuario(usuario) = ".intval($args["cd_usuario"])."
			      ";
             
        $result = $this->db->query($qr_sql);
    }	

    function carregar_tipo_solicitacao()
	{
		$qr_sql = "
			SELECT cd_documento_recebido_tipo_solic AS value,
                   ds_documento_recebido_tipo_solic AS text
		      FROM projetos.documento_recebido_tipo_solic 
	         WHERE dt_exclusao IS NULL
	         ORDER BY nr_ordem, ds_documento_recebido_tipo_solic;";

	    return $this->db->query($qr_sql)->result_array();
	}

	public function get_digitalizado($id_documento)
	{
		$qr_sql = "
			SELECT cd_digitalizado,
			       cd_documento,
				   cd_empresa,
				   cd_registro_empregado,
				   seq_dependencia
			  FROM projetos.digitalizado
			 WHERE id_documento = '".trim($id_documento)."';";

		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar_digitalizado($args = array())
	{
		$qr_sql = "
			INSERT INTO projetos.digitalizado
			     (
            		id_documento, 
            		cd_documento, 
            		cd_empresa, 
            		cd_registro_empregado, 
            		seq_dependencia,
            		cd_usuario_inclusao,
            		cd_usuario_alteracao
                 )
    		VALUES 
    		     (
					".str_escape($args['id_documento']).",
					".(intval($args['cd_documento']) > 0 ? intval($args['cd_documento']) : "DEFAULT").",
					".(intval($args['cd_empresa']) > 0 ? intval($args['cd_empresa']) : "DEFAULT").",
					".(intval($args['cd_registro_empregado']) > 0 ? intval($args['cd_registro_empregado']) : "DEFAULT").",
					".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
    		     );";

    	$this->db->query($qr_sql);
	}

	public function atualizar_digitalizado($cd_digitalizado, $args = array())
	{
		$qr_sql = "
		    UPDATE projetos.digitalizado
		       SET cd_documento          = ".(intval($args['cd_documento']) > 0 ? intval($args['cd_documento']) : "DEFAULT").",
            	   cd_empresa            = ".(intval($args['cd_empresa']) > 0 ? intval($args['cd_empresa']) : "DEFAULT").",
            	   cd_registro_empregado = ".(intval($args['cd_registro_empregado']) > 0 ? intval($args['cd_registro_empregado']) : "DEFAULT").", 
            	   seq_dependencia       = ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
            	   cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
            	   dt_alteracao          = CURRENT_TIMESTAMP
		     WHERE cd_digitalizado = ".intval($cd_digitalizado).";";

    	$this->db->query($qr_sql);
	}
}
?>