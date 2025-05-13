<?php
class Correspondencias_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	public function listar($args  = array())
	{
		$qr_sql = "
			SELECT c.cd_correspondencia,
			       funcoes.nr_sg_correspondencia(c.ano::INTEGER, c.numero::INTEGER) AS ano_numero,
                   c.solicitante_nome AS solicitante,
                   c.assinatura_nome AS assinatura,
                   c.destinatario_nome,
                   TO_CHAR(c.data,'DD/MM/YYYY') AS data,
                   c.destinatario_seq,
				   c.destinatario_emp,
				   c.destinatario_re,
				   c.assunto
              FROM projetos.correspondencias c
             WHERE c.dt_exclusao IS NULL
               AND ((c.fl_restrito = 'S' AND funcoes.get_usuario_area(c.cd_usuario_inclusao) = '".trim($args['cd_gerencia'])."') OR c.fl_restrito = 'N')
               ".(trim($args['ano']) != '' ? "AND c.ano = ".intval($args['ano']) : '')."
               ".(trim($args['numero']) != '' ? "AND c.numero = ".intval($args['numero']) : '')."
               ".(trim($args['solicitante']) != '' ? "AND UPPER(c.solicitante_nome) LIKE UPPER('%".trim($args['solicitante'])."%')" : '')."
               ".(trim($args['assinatura']) != '' ? "AND UPPER(c.assinatura_nome) LIKE UPPER('%".trim($args['assinatura'])."%')" : '')."
               ".(trim($args['destinatario_nome']) != '' ? "AND UPPER(c.destinatario_nome) LIKE UPPER('%".trim($args['destinatario_nome'])."%')" : '')."
			   ".(((trim($args['data_ini']) != '') AND (trim($args['data_fim']) != '')) ? " AND DATE_TRUNC('day', c.data) BETWEEN TO_DATE('".$args['data_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['data_fim']."', 'DD/MM/YYYY')" : "").";";
			 
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function get_gerencia()
	{
		$qr_sql = "
			SELECT codigo AS value,
			       nome AS text 
			  FROM funcoes.get_gerencias_vigente();";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function get_gerencia_anteriores($divisao)
	{
		$qr_sql = "
			SELECT codigo AS value,
			       nome AS text 
			  FROM projetos.divisoes d
		     WHERE tipo IN ('DIV', 'ASS')
		       AND dt_vigencia_ini <= CURRENT_TIMESTAMP
		       AND (dt_vigencia_fim >= CURRENT_TIMESTAMP OR dt_vigencia_fim IS NULL)
		        OR codigo = ".str_escape($divisao)."
		     ORDER BY nome
			  ;";		

		return $this->db->query($qr_sql)->result_array();
	}

	public function carrega($cd_correspondencia)
	{
		$qr_sql = "
			SELECT funcoes.nr_sg_correspondencia(a.ano::INTEGER, a.numero::INTEGER) AS ano_numero,
			       a.cd_correspondencia, 
			       a.numero,
				   a.ano,
				   a.divisao,
				   a.solicitante_emp,
				   a.solicitante_re,
				   a.solicitante_seq,
                   a.solicitante_nome,
				   a.assinatura_emp,
				   a.assinatura_re,
				   a.assinatura_seq,
				   a.assinatura_nome,
				   a.destinatario_seq,
				   a.destinatario_emp,
				   a.destinatario_re,
				   a.cd_empresa,
				   a.cd_registro_empregado,
				   a.seq_dependencia,
				   a.destinatario_nome,
				   a.assunto,
				   a.protocolo,
				   a.retorno_protocolo,
				   TO_CHAR(a.data,'DD/MM/YYYY') AS data,
				   a.interno,
				   a.tipo_protocolo,
				   a.cd_usuario_inclusao,
				   a.fl_restrito,
				   funcoes.get_usuario_area(a.cd_usuario_inclusao) AS cd_gerencia_inclusao
			  FROM projetos.correspondencias a
			 WHERE cd_correspondencia = ".intval($cd_correspondencia).";";
		
		return $this->db->query($qr_sql)->row_array();
	}

	public function salvar($args = array())
	{
		$cd_correspondencia = intval($this->db->get_new_id("projetos.correspondencias", "cd_correspondencia"));
		
		$qr_sql = "
			INSERT INTO projetos.correspondencias 
				 (  
					cd_correspondencia,
					divisao,
					solicitante_emp,
					solicitante_re,
					solicitante_seq,
					solicitante_nome, 
					assinatura_emp,
					assinatura_re, 
					assinatura_seq, 
					assinatura_nome,
					cd_empresa,
					cd_registro_empregado, 
					seq_dependencia,	
					destinatario_emp,
					destinatario_re, 
					destinatario_seq, 					
					destinatario_nome, 
					assunto, 
					data,
					fl_restrito,
					cd_usuario_inclusao,
					cd_usuario_alteracao
				 ) 
			VALUES 
				 ( 
					".intval($cd_correspondencia).",
					".(trim($args['divisao']) != '' ? "'".trim($args['divisao'])."'" : "DEFAULT").",
					".(trim($args['solicitante_emp']) != '' ? intval($args['solicitante_emp']) : "DEFAULT").",
					".(trim($args['solicitante_re']) != '' ? intval($args['solicitante_re']) : "DEFAULT").",
					".(trim($args['solicitante_seq']) != '' ? intval($args['solicitante_seq']) : "DEFAULT").",
					".(trim($args['solicitante_nome']) != '' ? str_escape($args['solicitante_nome']) : "DEFAULT").",
					".(trim($args['assinatura_emp']) != '' ? intval($args['assinatura_emp']) : "DEFAULT").",
					".(trim($args['assinatura_re']) != '' ? intval($args['assinatura_re']) : "DEFAULT").",
					".(trim($args['assinatura_seq']) != '' ? intval($args['assinatura_seq']) : "DEFAULT").",
					".(trim($args['assinatura_nome']) != '' ? str_escape($args['assinatura_nome']) : "DEFAULT").",
					".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
					".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
					".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
					".(trim($args['destinatario_emp']) != '' ? intval($args['destinatario_emp']) : "DEFAULT").",
					".(trim($args['destinatario_re']) != '' ? intval($args['destinatario_re']) : "DEFAULT").",
					".(trim($args['destinatario_seq']) != '' ? intval($args['destinatario_seq']) : "DEFAULT").",
					".(trim($args['destinatario_nome']) != '' ? str_escape($args['destinatario_nome']) : "DEFAULT").",
					".(trim($args['assunto']) != '' ? str_escape($args['assunto']) : "DEFAULT").",
					".(trim($args['data']) != '' ? "TO_DATE('".$args['data']."', 'DD/MM/YYYY')" : "DEFAULT").",
					".(trim($args['fl_restrito']) != '' ? "'".trim($args['fl_restrito'])."'" : "DEFAULT").",
					".intval($args['cd_usuario']).",
					".intval($args['cd_usuario'])."
				 );";

		$this->db->query($qr_sql);
		
		return $cd_correspondencia;
    }

	public function atualizar($cd_correspondencia, $args = array())
	{
		$qr_sql = "
			UPDATE projetos.correspondencias 
			   SET divisao               = ".(trim($args['divisao']) != '' ? "'".trim($args['divisao'])."'" : "DEFAULT").",
			       solicitante_emp       = ".(trim($args['solicitante_emp']) != '' ? intval($args['solicitante_emp']) : "DEFAULT").",
				   solicitante_re        = ".(trim($args['solicitante_re']) != '' ? intval($args['solicitante_re']) : "DEFAULT").",
				   solicitante_seq       = ".(trim($args['solicitante_seq']) != '' ? intval($args['solicitante_seq']) : "DEFAULT").",
				   solicitante_nome      = ".(trim($args['solicitante_nome']) != '' ? str_escape($args['solicitante_nome']) : "DEFAULT").",
				   assinatura_emp        = ".(trim($args['assinatura_emp']) != '' ? intval($args['assinatura_emp']) : "DEFAULT").",
				   assinatura_re         = ".(trim($args['assinatura_re']) != '' ? intval($args['assinatura_re']) : "DEFAULT").",
				   assinatura_seq        = ".(trim($args['assinatura_seq']) != '' ? intval($args['assinatura_seq']) : "DEFAULT").",
				   assinatura_nome       = ".(trim($args['assinatura_nome']) != '' ? str_escape($args['assinatura_nome']) : "DEFAULT").",
				   cd_empresa            = ".(trim($args['cd_empresa']) != '' ? intval($args['cd_empresa']) : "DEFAULT").",
				   cd_registro_empregado = ".(trim($args['cd_registro_empregado']) != '' ? intval($args['cd_registro_empregado']) : "DEFAULT").",
				   seq_dependencia       = ".(trim($args['seq_dependencia']) != '' ? intval($args['seq_dependencia']) : "DEFAULT").",
				   destinatario_emp      = ".(trim($args['destinatario_emp']) != '' ? intval($args['destinatario_emp']) : "DEFAULT").",
				   destinatario_re       = ".(trim($args['destinatario_re']) != '' ? intval($args['destinatario_re']) : "DEFAULT").",
				   destinatario_seq      = ".(trim($args['destinatario_seq']) != '' ? intval($args['destinatario_seq']) : "DEFAULT").",
				   destinatario_nome     = ".(trim($args['destinatario_nome']) != '' ? str_escape($args['destinatario_nome']) : "DEFAULT").",
				   assunto               = ".(trim($args['assunto']) != '' ? str_escape($args['assunto']) : "DEFAULT").",
				   data                  = ".(trim($args['data']) != '' ? "TO_DATE('".$args['data']."', 'DD/MM/YYYY')" : "DEFAULT").",
				   fl_restrito           = ".(trim($args['fl_restrito']) != '' ? "'".trim($args['fl_restrito'])."'" : "DEFAULT").",
				   cd_usuario_alteracao  = ".intval($args['cd_usuario']).",
				   dt_alteracao          = CURRENT_TIMESTAMP
			 WHERE cd_correspondencia = ".intval($cd_correspondencia).";";
		
	    $this->db->query($qr_sql);
	}

	public function excluir($cd_correspondencia, $cd_usuario)
	{
		$qr_sql = "
			UPDATE projetos.correspondencias
			   SET cd_usuario_exclusao = ".intval($cd_usuario).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_correspondencia = ".intval($cd_correspondencia).";";

		$this->db->query($qr_sql);
	}

	public function get_usuario($ds_usuario)
	{
		#### INTEGRAÇÃO ELETRO ####
		$qr_sql = "
			SELECT uc.codigo, 
			       uc.divisao,
                   uc.cd_patrocinadora,
				   uc.cd_registro_empregado,
				   uc.nome
			  FROM projetos.usuarios_controledi uc
			 WHERE TRIM(UPPER(uc.usuario)) = TRIM(UPPER('".$ds_usuario."'));";

		return $this->db->query($qr_sql)->row_array();
	}

	public function get_re_diretor($ds_diretoria = 'SEG')
	{
		#### INTEGRAÇÃO ELETRO ####
		$qr_sql = "
			SELECT cd_registro_empregado, 
			       nome
			  FROM projetos.usuarios_controledi 
			 WHERE divisao   = 'DE' 
			   AND tipo      = 'D' 
			   AND diretoria = '".trim($ds_diretoria)."';";

		return $this->db->query($qr_sql)->row_array();
	}	

	public function get_correspondencia($cd_correspondencia)
	{
		#### INTEGRAÇÃO ELETRO ####
		$qr_sql = "
			SELECT funcoes.nr_sg_correspondencia(ano::INTEGER, numero::INTEGER) AS nr_numero
			  FROM projetos.correspondencias
			 WHERE cd_correspondencia = ".intval($cd_correspondencia).";";

		return $this->db->query($qr_sql)->row_array();
	}	

	public function correspondencias_total_gerencia($date_ini, $date_fim)
	{
		$qr_sql = "
			SELECT DISTINCT divisao, 
			       nome,  
				   COUNT(*) AS total 
			  FROM projetos.correspondencias c
			  JOIN projetos.divisoes d
			    ON c.divisao = d.codigo
			 WHERE DATE_TRUNC('day', data) BETWEEN TO_DATE('".trim($date_ini)."','DD/MM/YYYY') AND TO_DATE('".trim($date_fim)."','DD/MM/YYYY') 
			 GROUP BY divisao, nome
			 ORDER BY total DESC";

		return $this->db->query($qr_sql)->result_array();
	}
	
	public function gerencia_relatorio()
	{
		$qr_sql = "
			SELECT codigo, 
			       nome 
			  FROM projetos.divisoes 
			 WHERE tipo NOT IN ('COM', 'CON')
			 ORDER BY codigo";
			 
		return $this->db->query($qr_sql)->result_array();
	}
	
	public function correspondencias_gerencia($date_ini, $date_fim, $cd_gerencia)
	{
		$qr_sql = "
			SELECT funcoes.nr_sg_correspondencia(ano::INTEGER, numero::INTEGER) AS ano_numero, 
				   destinatario_nome, 
				   assunto 
			  FROM projetos.correspondencias
			 WHERE DATE_TRUNC('day', data) BETWEEN TO_DATE('".trim($date_ini)."','DD/MM/YYYY') AND TO_DATE('".trim($date_fim)."','DD/MM/YYYY') 
			   AND divisao = '".trim($cd_gerencia)."'
			 ORDER BY numero;";
		
		return $this->db->query($qr_sql)->result_array();
	}

	public function get_re_usuario($ds_diretoria)
	{
		$qr_sql = "
			SELECT cd_registro_empregado,
			       UPPER(nome) AS ds_nome
			  FROM funcoes.get_usuario_gerente('".trim($ds_diretoria)."')
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = get_usuario_gerente";

		return $this->db->query($qr_sql)->row_array();
	}

	public function listar_anexo($cd_correspondencia)
	{
		$qr_sql = "
			SELECT aa.cd_correspondencia_anexo,
				   aa.arquivo,
				   aa.arquivo_nome,
				   aa.cd_usuario_inclusao,
				   TO_CHAR(aa.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   funcoes.get_usuario_nome(aa.cd_usuario_inclusao) AS ds_usuario
			  FROM projetos.correspondencias_anexo aa
			 WHERE aa.cd_correspondencia = ".intval($cd_correspondencia)."
			   AND aa.dt_exclusao IS NULL
			 ORDER BY aa.dt_inclusao DESC;";

		return $this->db->query($qr_sql)->result_array();
	}

	public function salvar_anexo($cd_correspondencia, $args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.correspondencias_anexo
                 (
                    cd_correspondencia,
                    arquivo, 
                    arquivo_nome, 
                    cd_usuario_inclusao
                 )
            VALUES 
                 (
                    ".intval($cd_correspondencia).",
                    '".trim($args['arquivo'])."',
                    '".trim($args['arquivo_nome'])."',
                    ".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);
    }

    public function excluir_anexo($cd_correspondencia_anexo, $cd_usuario)
    {
        $qr_sql = "
            UPDATE projetos.correspondencias_anexo
               SET cd_usuario_exclusao = ".intval($cd_usuario).",
                   dt_exclusao         = CURRENT_TIMESTAMP
             WHERE cd_correspondencia_anexo = ".intval($cd_correspondencia_anexo).";";

        $this->db->query($qr_sql);
    }
}