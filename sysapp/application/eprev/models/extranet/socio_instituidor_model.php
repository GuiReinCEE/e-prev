<?php
class Socio_instituidor_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    public function get_gerencia()
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_gerencias_vigente()  
             ORDER BY nome;";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_ultima_data_validade($cd_empresa)
    {
        $qr_sql = "
            SELECT TO_CHAR(si.dt_validacao, 'DD/MM/YYYY') AS dt_ult_validacao
              FROM extranet.socio_instituidor si
             WHERE si.dt_validacao IS NOT NULL
               AND si.cd_empresa = ".intval($cd_empresa)."
             ORDER BY si.dt_validacao DESC
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_usuarios($divisao)
    {
        $qr_sql = "
            SELECT codigo AS value,
                   nome AS text
              FROM funcoes.get_usuario_gerencia('".trim($divisao)."')";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_empresas()
    {
        $qr_sql = "
            SELECT p.cd_empresa AS value,
                   p.sigla AS text
              FROM public.patrocinadoras p
             WHERE p.tipo_cliente = 'I'
             ORDER BY p.sigla;";
   
        return $this->db->query($qr_sql)->result_array();
    }

    public function get_categoria()
    {
        $qr_sql = "
            SELECT cd_socio_instituidor_categoria AS value,
                   ds_socio_instituidor_categoria AS text
              FROM extranet.socio_instituidor_categoria
             WHERE dt_exclusao IS NULL
             ORDER BY ds_socio_instituidor_categoria;";
        
        return $this->db->query($qr_sql)->result_array();
    }

    public function listar($args = array())
    {
        $qr_sql = "
            SELECT si.cd_socio_instituidor,
                   si.nome,
                   si.cpf,
                   si.cpf_participante,
                   si.cd_empresa,
                   si.fl_socio,
                   funcoes.get_usuario_nome(si.cd_usuario_indicacao) AS ds_usuario_indicacao,
                   si.id_situacao,
                   funcoes.get_usuario_nome(si.cd_usuario_inclusao) AS ds_nome_inclusao,
                   COALESCE(u.nome, funcoes.get_usuario_nome(si.cd_usuario_validacao_fceee)) AS ds_nome_validacao,
                   si.cd_socio_instituidor_pacote,
                   TO_CHAR(si.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
                   TO_CHAR(si.dt_validacao,'DD/MM/YYYY HH24:MI') AS dt_validacao,
                   TO_CHAR(sip.dt_envio,'DD/MM/YYYY HH24:MI') AS dt_envio,
                   p.sigla AS ds_empresa,
                   (CASE WHEN si.cd_gerencia_indicacao IS NULL THEN 'Não'
                         ELSE si.cd_gerencia_indicacao 
                         END) AS ds_gerencia_indicacao,
                   (CASE WHEN si.id_situacao = 1 THEN 'Sócio'
                         WHEN si.id_situacao = 2 THEN 'Não Sócio'
                         WHEN si.id_situacao = 3 THEN 'Vinculado'
                         WHEN si.id_situacao = 4 THEN 'Não Vinculado'
                         ELSE 'Não informado'
                   END) AS ds_socio,
                   (CASE WHEN si.id_situacao = 1 THEN 'label-success'
                         WHEN si.id_situacao = 2 THEN 'label-important'
                         WHEN si.id_situacao = 3 THEN 'label-info'
                         WHEN si.id_situacao = 4 THEN 'label-warning'
                         ELSE 'label-inverse'
                   END) AS class_socio,
                   sic.ds_socio_instituidor_categoria
              FROM extranet.socio_instituidor si
              JOIN public.patrocinadoras p
                ON p.cd_empresa = si.cd_empresa
              JOIN extranet.socio_instituidor_pacote sip
                ON sip.cd_socio_instituidor_pacote = si.cd_socio_instituidor_pacote
              LEFT JOIN extranet.usuario u
                ON u.cd_usuario = si.cd_usuario_validacao
              LEFT JOIN extranet.socio_instituidor_categoria sic
                ON sic.cd_socio_instituidor_categoria = si.cd_socio_instituidor_categoria
             WHERE 1 = 1
               ".(trim($args['cd_empresa']) != '' ? "AND si.cd_empresa = ".intval($args['cd_empresa']) : '')."
               ".(trim($args['cd_gerencia_indicacao']) != '' ? "AND si.cd_gerencia_indicacao = '".trim($args['cd_gerencia_indicacao'])."'" : "")."
               ".(trim($args['cd_socio_instituidor_categoria']) != '' ? " AND si.cd_socio_instituidor_categoria = ".intval($args['cd_socio_instituidor_categoria']) : "")."
               ".(intval($args['id_situacao']) > 0 ? "AND si.id_situacao = ".$args['id_situacao'] : (trim($args['id_situacao']) != '' ? "AND si.id_situacao IS NULL" : ''))."
               ".(trim($args['cpf']) != '' ? "AND si.cpf = '".$args['cpf']."'" : "")."
               ".(trim($args['cpf_participante']) != '' ? "AND si.cpf_participante = '".$args['cpf_participante']."'" : "")."
               ".((trim($args['dt_inclusao_ini']) != '' AND trim($args['dt_inclusao_fim']) != '') ? " AND CAST(si.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args['dt_inclusao_ini']."','DD/MM/YYYY') AND TO_DATE('".$args["dt_inclusao_fim"]."','DD/MM/YYYY')" : "")."
               ".((trim($args['dt_validacao_ini']) != '' AND trim($args['dt_validacao_fim']) != '') ? " AND CAST(si.dt_validacao AS DATE) BETWEEN TO_DATE('".$args['dt_validacao_ini']."','DD/MM/YYYY') AND TO_DATE('".$args['dt_validacao_fim']."','DD/MM/YYYY')" : "").";";
       
        return $this->db->query($qr_sql)->result_array();
    }

    public function listar_anterior($cpf, $cd_socio_instituidor, $cd_empresa)
    {
        $qr_sql = "
            SELECT si.cd_socio_instituidor_pacote,
                   si.cd_empresa,
                   si.cd_socio_instituidor,
                   TO_CHAR(si.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
                   TO_CHAR(si.dt_validacao,'DD/MM/YYYY HH24:MI') AS dt_validacao,
                   (CASE WHEN si.id_situacao = 1 THEN 'Sócio'
                         WHEN si.id_situacao = 2 THEN 'Não Sócio'
                         WHEN si.id_situacao = 3 THEN 'Vinculado'
                         WHEN si.id_situacao = 4 THEN 'Não Vinculado'
                         ELSE 'Não informado'
                   END) AS ds_socio,
                   (CASE WHEN si.id_situacao = 1 THEN 'label-success'
                         WHEN si.id_situacao = 2 THEN 'label-important'
                         WHEN si.id_situacao = 3 THEN 'label-info'
                         WHEN si.id_situacao = 4 THEN 'label-warning'
                         ELSE 'label-inverse'
                   END) AS class_socio
              FROM extranet.socio_instituidor si
             WHERE si.cpf                  = '".$cpf."'
               AND si.cd_empresa           = ".intval($cd_empresa)."
               AND si.cd_socio_instituidor != ".intval($cd_socio_instituidor).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function get_socio_instituidor_pacote($cd_usuario)
    {
    	$cd_socio_instituidor_pacote = intval($this->db->get_new_id('extranet.socio_instituidor_pacote', 'cd_socio_instituidor_pacote'));
        
        $qr_sql = "
        	INSERT INTO extranet.socio_instituidor_pacote 
                 (
                    cd_socio_instituidor_pacote,
                    cd_usuario_inclusao
                 )
            VALUES
                 (  
                    ".intval($cd_socio_instituidor_pacote).",
                    ".intval($cd_usuario)."
                 );";
        
        $this->db->query($qr_sql);
        
        return $cd_socio_instituidor_pacote;
    }

    public function cadastro($cd_socio_instituidor_pacote, $cd_empresa = '')
    {
        $qr_sql = "
            SELECT si.cd_socio_instituidor,
			       si.cd_socio_instituidor_pacote,
                   si.nome,
                   si.cpf,
                   si.cpf_participante,
				   si.cd_empresa,
				   si.cd_usuario_indicacao,
                   si.cd_gerencia_indicacao,
                   p.sigla AS ds_empresa,
                   (CASE WHEN si.id_situacao = 1 THEN 'label-success'
                         WHEN si.id_situacao = 2 THEN 'label-important'
					 	 WHEN si.id_situacao = 3 THEN 'label-info'
                         WHEN si.id_situacao = 4 THEN 'label-warning'
                         ELSE 'label-inverse'
                   END) AS class_socio,
                   (CASE WHEN si.id_situacao = 1 THEN 'Sócio'
                         WHEN si.id_situacao = 2 THEN 'Não Sócio'
						 WHEN si.id_situacao = 3 THEN 'Vinculado'
                         WHEN si.id_situacao = 4 THEN 'Não Vinculado'
                         ELSE 'Não informado'
                   END) AS ds_socio,
                   sic.ds_socio_instituidor_categoria
              FROM extranet.socio_instituidor si
              JOIN public.patrocinadoras p
                ON p.cd_empresa = si.cd_empresa
              LEFT JOIN extranet.socio_instituidor_categoria sic
                ON sic.cd_socio_instituidor_categoria = si.cd_socio_instituidor_categoria
             WHERE si.cd_socio_instituidor_pacote = ".intval($cd_socio_instituidor_pacote)."
               ".(trim($cd_empresa) != '' ? "AND si.cd_empresa = ".intval($cd_empresa) : "").";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function verifica_cpf($cpf, $cd_empresa)
    {
        $qr_sql = "
            SELECT TO_CHAR(si.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
                   TO_CHAR(si.dt_validacao,'DD/MM/YYYY HH24:MI') AS dt_validacao,
                   (CASE WHEN si.id_situacao = 1 THEN 'Sócio'
                         WHEN si.id_situacao = 2 THEN 'Não Sócio'
                         WHEN si.id_situacao = 3 THEN 'Vinculado'
                         WHEN si.id_situacao = 4 THEN 'Não Vinculado'
                         ELSE 'Não informado'
                   END) AS ds_socio
              FROM extranet.socio_instituidor si
             WHERE si.cpf        = '".trim($cpf)."'
               AND si.cd_empresa = ".intval($cd_empresa)."
             ORDER BY si.dt_inclusao DESC
             LIMIT 1;";

        return $this->db->query($qr_sql)->row_array();
    }

    public function get_socio_instituidor($cd_socio_instituidor)
    {
        $qr_sql = "
            SELECT si.cd_socio_instituidor_pacote,
                   si.cd_socio_instituidor,
                   si.nome,
                   si.cpf,
                   si.cpf_participante,
                   si.cd_empresa,
                   si.cd_gerencia_indicacao,
                   si.cd_usuario_indicacao,
                   TO_CHAR(sip.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio
              FROM extranet.socio_instituidor si
              LEFT JOIN extranet.socio_instituidor_pacote sip
                ON sip.cd_socio_instituidor_pacote = si.cd_socio_instituidor_pacote
             WHERE si.cd_socio_instituidor = ".intval($cd_socio_instituidor).";";
     
        return $this->db->query($qr_sql)->row_array();
    }   

    public function get_envio($cd_socio_instituidor_pacote)
    {
        $qr_sql = "
            SELECT TO_CHAR(dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio
              FROM extranet.socio_instituidor_pacote
             WHERE cd_socio_instituidor_pacote = ".intval($cd_socio_instituidor_pacote).";";

        return $this->db->query($qr_sql)->row_array();
    }

    public function excluir($cd_socio_instituidor)
    {
        $qr_sql = "
        	DELETE 
        	  FROM extranet.socio_instituidor
             WHERE cd_socio_instituidor = ".intval($cd_socio_instituidor).";";

        $this->db->query($qr_sql);
    }

    public function excluir_pacote($cd_socio_instituidor_pacote)
    {
        $qr_sql = "
            DELETE 
              FROM extranet.socio_instituidor
             WHERE cd_socio_instituidor_pacote = ".intval($cd_socio_instituidor_pacote).";

            DELETE 
              FROM extranet.socio_instituidor_pacote
             WHERE cd_socio_instituidor_pacote = ".intval($cd_socio_instituidor_pacote).";";

        $this->db->query($qr_sql);
    }

    public function salvar($args = array())
    {
    	$cd_socio_instituidor = intval($this->db->get_new_id('extranet.socio_instituidor', 'cd_socio_instituidor'));
			
		$qr_sql = "
			INSERT INTO extranet.socio_instituidor
				 (
				   cd_socio_instituidor,
				   nome,
				   cpf,
				   cpf_participante,
				   cd_empresa,
                   cd_gerencia_indicacao,
				   cd_socio_instituidor_pacote,
				   cd_usuario_inclusao,
				   cd_usuario_indicacao
				 )
			VALUES
				 (
				   ".$cd_socio_instituidor.",
				   ".(trim($args['nome']) != '' ? "UPPER(funcoes.remove_acento(".str_escape($args['nome'])."))" : "DEFAULT").",
				   ".(trim($args['cpf']) != '' ? str_escape($args['cpf']) : "DEFAULT").",
				   ".(trim($args['cpf_participante']) != '' ? str_escape($args['cpf_participante']) : "DEFAULT").",
                   ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
				   ".(trim($args['cd_gerencia_indicacao']) != '' ? str_escape($args['cd_gerencia_indicacao']) : "DEFAULT").",
                   ".(intval($args['cd_socio_instituidor_pacote']) > 0 ? $args['cd_socio_instituidor_pacote'] : "DEFAULT").",
				   ".intval($args['cd_usuario']).",
				   ".(trim($args['cd_usuario_indicacao']) != '' ? str_escape($args['cd_usuario_indicacao']) : "DEFAULT")."
				 );";

		$this->db->query($qr_sql);
	}

	public function atualizar($cd_socio_instituidor, $args)
    {
    	$qr_sql = "
			UPDATE extranet.socio_instituidor
			   SET nome                  = ".(trim($args['nome']) != '' ? "UPPER(funcoes.remove_acento(".str_escape($args['nome'])."))" : "DEFAULT").",
				   cpf                   = ".(trim($args['cpf']) != '' ? str_escape($args['cpf']) : "DEFAULT").",
				   cpf_participante      = ".(trim($args['cpf_participante']) != '' ? str_escape($args['cpf_participante']) : "DEFAULT").",
				   cd_empresa            = ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
                   cd_gerencia_indicacao =  ".(trim($args['cd_gerencia_indicacao']) != '' ? str_escape($args['cd_gerencia_indicacao']) : "DEFAULT")."
			 WHERE cd_socio_instituidor  = ".intval($cd_socio_instituidor).";";

		$this->db->query($qr_sql);
    }

    public function get_empresas_pacote($cd_socio_instituidor_pacote)
    {
        $qr_sql = "
        	SELECT DISTINCT cd_empresa
              FROM extranet.socio_instituidor
             WHERE cd_socio_instituidor_pacote = ".intval($cd_socio_instituidor_pacote).";";
        
        return $this->db->query($qr_sql)->result_array();
    }

    public function enviar($cd_socio_instituidor_pacote, $cd_usuario)
    {
        $qr_sql = "
        	UPDATE extranet.socio_instituidor_pacote
               SET cd_usuario_envio = ".intval($cd_usuario).",
                   dt_envio         = CURRENT_TIMESTAMP
             WHERE cd_socio_instituidor_pacote = ".intval($cd_socio_instituidor_pacote).";";

        $this->db->query($qr_sql);
    }

    public function get_sigla_patrocinadora($cd_empresa)
    {
    	$qr_sql = "SELECT sigla FROM funcoes.get_patrocinadora(".intval($cd_empresa).");";

    	return $this->db->query($qr_sql)->row_array();
    }

    public function get_usuario_envio_email($cd_empresa)
    {
    	$qr_sql = "
    		SELECT cd_usuario, 
                   usuario, 
                   senha, 
                   cd_empresa, 
                   dt_inclusao, 
                   dt_exclusao, 
                   nome, 
                   email
              FROM extranet.usuario
             WHERE dt_exclusao IS NULL
               AND cd_empresa  = ".intval($cd_empresa).";";

        return $this->db->query($qr_sql)->result_array();
    }

    public function listar_email($args = array())
    {
        $qr_sql = "
            SELECT ee.cd_email,
                   TO_CHAR(ee.dt_envio, 'DD/MM/YYYY HH24:MI:SS') AS dt_email,
                   TO_CHAR(ee.dt_email_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio,
                   ee.de,
                   ee.para,
                   ee.cc,
                   ee.cco,
                   ee.assunto,
                   ee.fl_retornou
              FROM projetos.envia_emails ee
             WHERE ee.cd_evento = 104
                " . (((trim($args['dt_email_ini']) != '') AND (trim($args['dt_email_fim']) != '')) ? "AND CAST(ee.dt_envio AS DATE) BETWEEN TO_DATE('" . trim($args["dt_email_ini"]) . "','DD/MM/YYYY') AND TO_DATE('" . trim($args["dt_email_fim"]) . "','DD/MM/YYYY') " : "") . "
		          ";
 
        return $this->db->query($qr_sql)->result_array();
    }

    public function get_valida_participante_dependente($cpf)
	{
		$qr_sql = "
			SELECT (CASE WHEN COUNT(*) > 0 THEN 1
			       ELSE 2 
			       END) AS id_situacao
			  FROM participantes
			 WHERE funcoes.format_cpf(cpf_mf) = ".str_escape($cpf)."
			   AND projetos.participante_tipo(cd_empresa, cd_registro_empregado, seq_dependencia) IN ('APOS', 'EXAU', 'CTP', 'AUXD', 'ATIV', 'PENS');";

		return $this->db->query($qr_sql)->row_array();
	}

	public function valida_socio_interno($args = array())
	{
		$qr_sql = "
			UPDATE extranet.socio_instituidor AS siu
			   SET id_situacao                 = x.id_situacao,
				   cd_usuario_validacao_fceee  = ".intval($args['cd_usuario']).",
				   dt_validacao                = CURRENT_TIMESTAMP
			  FROM (SELECT si.cd_socio_instituidor,
				           (SELECT (CASE WHEN COUNT(*) > 0 
							  	         THEN 1 --SOCIO
							             ELSE 2 --NAO SOCIO
						           END) AS id_situacao
				              FROM participantes
				             WHERE funcoes.format_cpf(cpf_mf) = si.cpf_participante
				               AND projetos.participante_tipo(cd_empresa, cd_registro_empregado, seq_dependencia) IN ('APOS', 'EXAU', 'CTP', 'AUXD', 'ATIV', 'PENS')) AS id_situacao
			          FROM extranet.socio_instituidor si
			         WHERE si.cd_socio_instituidor_pacote = ".intval($args['cd_socio_instituidor_pacote'])."
			           AND si.cd_empresa                  = ".intval($args['cd_empresa']).") x
			 WHERE siu.cd_socio_instituidor = x.cd_socio_instituidor;

			SELECT extranet.socio_instituidor_atualiza_ora(".intval($args['cd_empresa']).");";

        $this->db->query($qr_sql);
	}
}

?>