<?php
class atendimento_protocolo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

  function get_cd_usuario($usuario)
  {
    $qr_sql = "SELECT funcoes.get_usuario('".trim($usuario)."') AS cd_usuario";

    return $this->db->query($qr_sql)->row_array();
  }

    function listar( &$result, $args=array() )
	{
        $sql = "
            SELECT a.cd_atendimento_protocolo,
                   a.nome,
				   a.cd_gerencia_origem,
                   papt.nome as tipo_nome,
                   papd.nome as discriminacao_nome,
                   a.identificacao,
                   to_char(a.dt_inclusao, 'DD/MM/YYYY HH24:MI') AS dt_inclusao,
                   to_char(a.dt_recebimento, 'DD/MM/YYYY HH24:MI') AS dt_recebimento,
                   TO_CHAR(a.dt_devolucao, 'DD/MM/YYYY') AS dt_devolucao,
                   a.cd_usuario_recebimento,
                   a.cd_usuario_inclusao,
                   a.destino AS ds_destino,
                   a.cd_empresa,
                   a.cd_registro_empregado,
                   a.seq_dependencia,
                   to_char(a.dt_cancelamento, 'DD/MM/YYYY HH24:MI') AS dt_cancelamento,
                   a.motivo_cancelamento,
                   b.guerra as nome_gap,
                   c.guerra as nome_gad,
                   a.cd_atendimento_protocolo_tipo,
                   a.cd_atendimento_protocolo_discriminacao,
                   a.cd_atendimento,
                   a.cd_encaminhamento,
				   a.ds_descricao_tipo
              FROM projetos.atendimento_protocolo a
              JOIN projetos.usuarios_controledi b
                ON a.cd_usuario_inclusao = b.codigo
              LEFT JOIN projetos.usuarios_controledi c
                ON a.cd_usuario_recebimento = c.codigo
		      LEFT JOIN projetos.atendimento_protocolo_tipo papt
		        ON a.cd_atendimento_protocolo_tipo = papt.cd_atendimento_protocolo_tipo
		      LEFT JOIN projetos.atendimento_protocolo_discriminacao papd
		        ON a.cd_atendimento_protocolo_discriminacao = papd.cd_atendimento_protocolo_discriminacao
             WHERE 1=1
             ".((trim($args['cd_atendimento_protocolo_discriminacao']) != "") ? " AND a.cd_atendimento_protocolo_discriminacao = ".intval($args['cd_atendimento_protocolo_discriminacao']) : "")."
             ".((trim($args['cd_atendimento_protocolo_tipo']) != "") ? " AND a.cd_atendimento_protocolo_tipo = ".intval($args['cd_atendimento_protocolo_tipo']) : "")."
             ".((trim($args['cd_empresa']) != "") ? " AND a.cd_empresa = ".intval($args['cd_empresa']) : "")."
             ".((trim($args['cd_registro_empregado']) != "") ? " AND a.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
             ".((trim($args['seq_dependencia']) != "") ? " AND a.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
             ".((trim($args['nome']) != "") ? " AND UPPER(a.nome) like UPPER('%".trim($args['nome'])."%')" : "")."
             
			 ".((trim($args['fl_recebido']) == "S") ? " AND a.dt_recebimento IS NOT NULL " : "")."
			 ".((trim($args['fl_recebido']) == "N") ? " AND a.dt_recebimento IS NULL " : "")."

			 ".((trim($args['fl_cancelado']) == "S") ? " AND a.dt_cancelamento IS NOT NULL " : "")."
			 ".((trim($args['fl_cancelado']) == "N") ? " AND a.dt_cancelamento IS NULL " : "")."			 
			 
			 ".((trim($args['cd_gerencia_origem']) != "") ? " AND a.cd_gerencia_origem = '".trim($args['cd_gerencia_origem'])."' " : "")."			 
			 
             ".((trim($args['identificacao']) != "") ? " AND UPPER(a.identificacao) like UPPER('%".trim($args['identificacao'])."%')" : "")."
             ".((trim($args['cd_atendimento']) != "") ? " AND a.cd_atendimento = ".intval($args['cd_atendimento']) : "")."
             ".((trim($args['cd_encaminhamento']) != "") ? " AND a.cd_encaminhamento = ".intval($args['cd_encaminhamento']) : "")."
             ".(((trim($args['dt_inclusao_inicial']) != "") and  (trim($args['dt_inclusao_final']) != "")) ? " AND DATE_TRUNC('day', a.dt_inclusao) BETWEEN TO_DATE('".$args['dt_inclusao_inicial']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_inclusao_final']."', 'DD/MM/YYYY')" : "")."
             ".(((trim($args['hr_inclusao_inicial']) != "") and  (trim($args['hr_inclusao_final']) != "")) ? " AND (a.dt_inclusao) BETWEEN TO_TIMESTAMP('".$args['dt_inclusao_inicial']. ' '. $args['hr_inclusao_inicial']."', 'DD/MM/YYYY HH24:MI') AND TO_TIMESTAMP('".$args['dt_inclusao_final'].' '. $args['hr_inclusao_final']."', 'DD/MM/YYYY HH24:MI')" : "")."
             ".((trim($args['cd_usuario_inclusao']) != "") ? " AND a.cd_usuario_inclusao = ".intval($args['cd_usuario_inclusao']) : "")."
             ".(((trim($args['dt_recebimento_inicial']) != "") and  (trim($args['dt_recebimento_final']) != "")) ? " AND DATE_TRUNC('day', a.dt_recebimento) BETWEEN TO_DATE('".$args['dt_recebimento_inicial']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_recebimento_final']."', 'DD/MM/YYYY')" : "")."
             ".(((trim($args['dt_devolucao_inicial']) != "") and  (trim($args['dt_devolucao_final']) != "")) ? " AND DATE_TRUNC('day', a.dt_devolucao) BETWEEN TO_DATE('".$args['dt_devolucao_inicial']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_devolucao_final']."', 'DD/MM/YYYY')" : "")."
             ".((trim($args['fl_devolvido']) == "S") ? " AND a.dt_devolucao IS NOT NULL " : "")."
			 ".((trim($args['fl_devolvido']) == "N") ? " AND a.dt_devolucao IS NULL " : "")."	
             ORDER BY a.dt_inclusao DESC
            ";

        #echo "<pre>".$sql."</pre><br/>";exit;
		
        $result = $this->db->query($sql);
    }

    function remetente(&$result, $args=array())
    {
        $sql = "
			SELECT a.codigo AS value,
				   a.nome   AS text
			  FROM projetos.usuarios_controledi a
			 WHERE EXISTS
				 (
				   SELECT 1
					 FROM projetos.atendimento_protocolo b
				    WHERE a.codigo = b.cd_usuario_inclusao
				 )
		   ORDER BY a.nome";

        $result = $this->db->query($sql);
    }
	
    function matriz_documento(&$result, $args=array())
    {
        $sql = "
				SELECT apd.cd_atendimento_protocolo_discriminacao AS cd_discriminacao
				  FROM public.tipo_documentos td
				  LEFT JOIN projetos.atendimento_protocolo_discriminacao apd
				    ON apd.cd_documento = td.cd_tipo_doc
				 WHERE apd.dt_exclusao IS NULL
				   AND td.cd_tipo_doc = ".intval($args["cd_tipo_doc"])."
				 ORDER BY td.cd_tipo_doc
		      ";

        $result = $this->db->query($sql);
    }	
	
    function comboGerenciaOrigem(&$result, $args=array())
    {
        $qr_sql = "
			SELECT a.codigo AS value,
				   a.codigo || ' - ' || a.nome AS text
			  FROM projetos.divisoes a
			 WHERE 0 < (SELECT COUNT(*)
				  	      FROM projetos.atendimento_protocolo b
						 WHERE b.cd_gerencia_origem = a.codigo)
			 ORDER BY text
			      ";
		$result = $this->db->query($qr_sql);
    }	

    function tipo(&$result, $args=array())
    {
        $sql = "
			SELECT cd_atendimento_protocolo_tipo AS value,
                   nome                          AS text
			  FROM projetos.atendimento_protocolo_tipo
			 WHERE dt_exclusao IS NULL
		     ORDER BY nome";
        
        $result = $this->db->query($sql);
    }

    function discriminacao(&$result, $args=array())
    {
        $sql = "
			SELECT cd_atendimento_protocolo_discriminacao AS value,
                   nome                                   AS text
			  FROM projetos.atendimento_protocolo_discriminacao
			 WHERE dt_exclusao IS NULL
		     ORDER BY nome";

        $result = $this->db->query($sql);
    }

    function receber(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.atendimento_protocolo
               SET cd_usuario_recebimento = ".$args['cd_usuario_logado'].",
                   dt_recebimento         = CURRENT_TIMESTAMP
             WHERE cd_atendimento_protocolo = ".$args['cd_atendimento_protocolo'];

        $result = $this->db->query($qr_sql);
    }

    function cancelar(&$result, $args=array())
    {
        $qr_sql = "
            UPDATE projetos.atendimento_protocolo
               SET dt_cancelamento         = CURRENT_TIMESTAMP,
			       cd_usuario_cancelamento = ".intval($args["cd_usuario"]).",
                   motivo_cancelamento     = '".trim($args['ds_motivo'])."'
             WHERE cd_atendimento_protocolo = ".intval($args['cd_atendimento_protocolo']);

        $result = $this->db->query($qr_sql);
    }

    function encaminhamento(&$result, $args=array())
    {
        $qr_sql = "
            SELECT cd_atendimento,
                   cd_encaminhamento
			  FROM projetos.atendimento_encaminhamento pae
			 WHERE
                 (
				   cd_empresa            = ".intval($args['cd_empresa'])."
			   AND cd_registro_empregado = ".intval($args['cd_registro_empregado'])."
			   AND seq_dependencia       = ".intval($args['seq_dependencia'])."
			   AND dt_cancelado IS NULL
			   AND dt_encaminhamento > ( current_timestamp - '5 days'::interval )
		       AND NOT EXISTS
                 (
				    SELECT 1
					  FROM projetos.atendimento_protocolo pap
				     WHERE pap.cd_atendimento    = pae.cd_atendimento
					   AND pap.cd_encaminhamento = pae.cd_encaminhamento
				  )
                 OR
                  (
                   cd_atendimento    = ".intval($args['cd_atendimento'])."
               AND cd_encaminhamento = ".intval($args['cd_encaminhamento'])."
                  )
			)";

        $result = $this->db->query($qr_sql);
    }

    function textoEncaminhamento(&$result, $args=array())
    {
        $qr_sql = "
            SELECT texto_encaminhamento
			  FROM projetos.atendimento_encaminhamento
			 WHERE cd_atendimento    = ".intval($args['cd_atendimento'])."
			   AND cd_encaminhamento = ".intval($args['cd_encaminhamento']);

        $result = $this->db->query($qr_sql);
    }

    function carrega(&$result, $args=array())
    {
        $qr_sql =
            "SELECT a.cd_atendimento_protocolo,
                    a.nome,
                    a.tipo,
                    a.identificacao AS ds_identificacao,
                    to_char(a.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
                    a.cd_usuario_recebimento,
                    to_char(a.dt_recebimento, 'DD/MM/YYYY HH24:MI:SS') AS dt_recebimento,
                    TO_CHAR(a.dt_devolucao, 'DD/MM/YYYY') AS dt_devolucao,
                    TO_CHAR(a.dt_devolvido, 'DD/MM/YYYY HH24:MI:SS') AS dt_devolvido,
                    a.ds_descricao_devolvido,
                    a.cd_usuario_inclusao,
                    a.destino AS ds_destino,
                    a.cd_empresa,
                    a.cd_registro_empregado,
                    a.seq_dependencia,
                    to_char(a.dt_cancelamento, 'DD/MM/YYYY HH24:MI:SS') AS dt_cancelamento,
                    a.motivo_cancelamento AS ds_motivo,
                    b.guerra as nome_gap,
                    c.guerra as nome_gad,
                    a.cd_atendimento_protocolo_tipo,
                    a.cd_atendimento_protocolo_discriminacao,
                    a.cd_atendimento,
                    a.cd_encaminhamento,
					a.ds_descricao_tipo
               FROM projetos.atendimento_protocolo a
               JOIN projetos.usuarios_controledi b
                 ON a.cd_usuario_inclusao = b.codigo
               LEFT JOIN projetos.usuarios_controledi c
                 ON a.cd_usuario_recebimento = c.codigo
              WHERE cd_atendimento_protocolo = ".intval($args['cd_atendimento_protocolo'])."
              ORDER BY a.dt_inclusao DESC";

        $result = $this->db->query($qr_sql);
    }

    function salvar(&$result, $args=array())
    {
        $retorno = intval($args['cd_atendimento_protocolo']);

        if(intval($args['cd_atendimento_protocolo']) == 0)
        {
            $new_id = intval($this->db->get_new_id("projetos.atendimento_protocolo", "cd_atendimento_protocolo"));

            $retorno = $new_id;

            $qr_sql = "
                 INSERT INTO projetos.atendimento_protocolo
                      (
                        cd_atendimento_protocolo,
                        nome,
                        identificacao,
                        dt_inclusao,
                        cd_usuario_inclusao,
						cd_gerencia_origem,
                        destino,
                        cd_empresa,
                        cd_registro_empregado,
                        seq_dependencia,
                        cd_atendimento_protocolo_tipo,
                        cd_atendimento_protocolo_discriminacao,
                        cd_atendimento,
                        cd_encaminhamento,
                        tipo,
						ds_descricao_tipo
                      )
                 VALUES
                      (
                        ".intval($new_id).",
                        ".str_escape($args['nome']).",
                        ".str_escape($args['ds_identificacao']).",
                        CURRENT_TIMESTAMP,
                        ".trim($args['cd_usuario_inclusao'])." ,
						".(trim($args['cd_gerencia_origem']) == "" ? "DEFAULT" : "'".$args['cd_gerencia_origem']."'").",
                        ".(trim($args['ds_destino']) == "" ? "DEFAULT" : str_escape($args['ds_destino'])).",
                        ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : $args['cd_empresa']).",
                        ".(trim($args['cd_registro_empregado']) == "" ? "DEFAULT" : $args['cd_registro_empregado']).",
                        ".(trim($args['seq_dependencia']) == "" ? "DEFAULT" : $args['seq_dependencia']).",
                        ".(trim($args['cd_atendimento_protocolo_tipo']) == "" ? "DEFAULT" : $args['cd_atendimento_protocolo_tipo']).",
                        ".(trim($args['cd_atendimento_protocolo_discriminacao']) == "" ? "DEFAULT" : $args['cd_atendimento_protocolo_discriminacao']).",
                        ".(trim($args['cd_atendimento']) == "" ? "DEFAULT" : $args['cd_atendimento']).",
                        ".(trim($args['cd_encaminhamento']) == "" ? "DEFAULT" : $args['cd_encaminhamento']).", 
						'',
                        ".(trim($args['ds_descricao_tipo']) == "" ? "DEFAULT" : str_escape($args['ds_descricao_tipo']))."
                      );";
  
        }
        else
        {
            $qr_sql = "
                UPDATE projetos.atendimento_protocolo
                   SET tipo                                   = '',
                       nome                                   = ".str_escape($args['nome']).",
                       identificacao                          = ".str_escape($args['ds_identificacao']).",
                       destino                                = ".str_escape($args['ds_destino']).",
                       cd_empresa                             = ".(trim($args['cd_empresa']) == "" ? "DEFAULT" : $args['cd_empresa']).",
                       cd_registro_empregado                  = ".(trim($args['cd_registro_empregado']) == "" ? "DEFAULT" : $args['cd_registro_empregado']).",
                       seq_dependencia                        = ".(trim($args['seq_dependencia']) == "" ? "DEFAULT" : $args['seq_dependencia']).",
                       cd_atendimento_protocolo_tipo          = ".(trim($args['cd_atendimento_protocolo_tipo']) == "" ? "DEFAULT" : $args['cd_atendimento_protocolo_tipo']).",
                       cd_atendimento_protocolo_discriminacao = ".(trim($args['cd_atendimento_protocolo_discriminacao']) == "" ? "DEFAULT" : $args['cd_atendimento_protocolo_discriminacao']).",
                       cd_atendimento                         = ".(trim($args['cd_atendimento']) == "" ? "DEFAULT" : $args['cd_atendimento']).",
                       cd_encaminhamento                      = ".(trim($args['cd_encaminhamento']) == "" ? "DEFAULT" : $args['cd_encaminhamento']).",
					   ds_descricao_tipo                      = ".(trim($args['ds_descricao_tipo']) == "" ? "DEFAULT" : str_escape($args['ds_descricao_tipo']))."
                 WHERE cd_atendimento_protocolo = ".intval($args['cd_atendimento_protocolo']).";";

        }

		#echo "<PRE>$qr_sql</PRE>"; exit;
		
        $result = $this->db->query($qr_sql);

        return $retorno;
    }

    public function salvar_devolucao($args = array())
    {
        $qr_sql = "
            UPDATE projetos.atendimento_protocolo
               SET dt_devolucao           = ".(trim($args['dt_devolucao']) != '' ? "TO_TIMESTAMP('".trim($args['dt_devolucao'])."', 'DD/MM/YYYY HH24:MI')" : "DEFAULT").", 
                   ds_descricao_devolvido = ".(trim($args['ds_descricao_devolvido']) != '' ? str_escape($args['ds_descricao_devolvido']) : "DEFAULT").",
                   cd_usuario_devolvido   = ".(intval($args['cd_usuario']) > 0 ? intval($args['cd_usuario']) : "DEFAULT").",
                   dt_devolvido           = CURRENT_TIMESTAMP
             WHERE cd_atendimento_protocolo = ".intval($args['cd_atendimento_protocolo']).";";

        $this->db->query($qr_sql);
    }

    function malaDireta(&$result, $args=array())
    {
        $qr_sql ="
            INSERT INTO projetos.mala_direta_integracao
				 (
                   cd_empresa,
                   cd_registro_empregado,
                   seq_dependencia,
                   usuario
                 )
		    VALUES
				 (
                   ".intval($args['cd_empresa']).",
                   ".intval($args['cd_registro_empregado']).",
                   ".intval($args['seq_dependencia']).",
                   UPPER('".trim($args['ds_usuario'])."')
                 );
            ";
        $result = $this->db->query($qr_sql);
    }

    function malaDiretaLimpar(&$result, $args=array())
    {
        $qr_sql ="
            DELETE FROM projetos.mala_direta_integracao
             WHERE UPPER(usuario) = UPPER('".$args['ds_usuario']."')
            ";
        $result = $this->db->query($qr_sql);
    }

    function carrega_descricao(&$result, $args=array())
    {
        $qr_sql = "
                SELECT identificacao AS ds_identificacao
                  FROM projetos.atendimento_protocolo
                 WHERE cd_atendimento_protocolo = ".$args['cd_atendimento_protocolo'];

        $result = $this->db->query($qr_sql);
    }
}
