<?php
class Contribuicao_relatorio_model extends Model
{
	function __construct()
    {
        parent::Model();
    }

    public function get_contribuicao_origem()
    {
    	$qr_sql = "
    		SELECT cd_contribuicao_relatorio_origem AS value,
    			   ds_contribuicao_relatorio_origem AS text
    		  FROM projetos.contribuicao_relatorio_origem
    		 WHERE dt_exclusao IS NULL;";

    	return $this->db->query($qr_sql)->result_array();
    }

    public function listar($fl_enviar_sms = 'S', $args = array())
    {
        $qr_sql = "
            SELECT crs.cd_contribuicao_relatorio_sms,
                   cro.ds_contribuicao_relatorio_origem,
				   crs.cd_sms,
			       TO_CHAR(crs.dt_referencia, 'YYYY/MM') AS dt_referencia,
			       crs.cd_empresa, 
			       crs.cd_registro_empregado,
			       crs.seq_dependencia,
			       funcoes.get_plano_nome(crs.cd_plano) AS ds_plano,
			       crs.ds_telefone,
			       TO_CHAR(crs.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       funcoes.get_usuario_nome(crs.cd_usuario_inclusao) AS ds_usuario,
			       (CASE WHEN char_length(crs.ds_telefone) = 11 THEN 'S'
			             ELSE 'N'
			       END) AS fl_status_telefone,
			       (CASE WHEN char_length(crs.ds_telefone) = 11 
                         THEN 'OK'
                         WHEN crs.ds_telefone = '' 
                         THEN 'SEM CELULAR'
			             ELSE 'INCORRETO'
			       END) AS ds_status_telefone,
			       (CASE WHEN char_length(crs.ds_telefone) = 11 
                         THEN 'label-success'
                         WHEN crs.ds_telefone = '' 
                         THEN ''
			             ELSE 'label-important'
			       END) AS ds_class_status_telefone,
			       (SELECT ds_nome FROM funcoes.get_participante(crs.cd_empresa, crs.cd_registro_empregado, crs.seq_dependencia)) AS ds_nome,
			       (SELECT TO_CHAR(crsg.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS')
			          FROM projetos.contribuicao_relatorio_sms_geracao_lista crsgl
			          JOIN projetos.contribuicao_relatorio_sms_geracao crsg
			            ON crsgl.cd_contribuicao_relatorio_sms_geracao = crsg.cd_contribuicao_relatorio_sms_geracao
			         WHERE crsgl.cd_contribuicao_relatorio_sms = crs.cd_contribuicao_relatorio_sms
			         ORDER BY crsg.dt_inclusao DESC
			         LIMIT 1) AS dt_geracao,
			       (SELECT funcoes.get_usuario_nome(crsgl.cd_usuario_inclusao)
			          FROM projetos.contribuicao_relatorio_sms_geracao_lista crsgl
			          JOIN projetos.contribuicao_relatorio_sms_geracao crsg
			            ON crsgl.cd_contribuicao_relatorio_sms_geracao = crsg.cd_contribuicao_relatorio_sms_geracao
			         WHERE crsgl.cd_contribuicao_relatorio_sms = crs.cd_contribuicao_relatorio_sms
			         ORDER BY crsg.dt_inclusao DESC
			         LIMIT 1) AS ds_usuario_geracao,
                   TO_CHAR(s.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_envio_sms,
                   funcoes.get_usuario_nome(s.cd_usuario_inclusao) AS ds_usuario_envio_sms
			  FROM projetos.contribuicao_relatorio_sms crs
			  JOIN projetos.contribuicao_relatorio_origem cro
			    ON cro.cd_contribuicao_relatorio_origem = crs.cd_contribuicao_relatorio_origem
              LEFT JOIN sms.sms s
                ON s.cd_sms = crs.cd_sms
			 WHERE crs.dt_exclusao IS NULL
               ".(trim($fl_enviar_sms) != '' ? "AND crs.fl_enviar_sms = '".trim($fl_enviar_sms)."'" : "")."
			   ".(trim($args['cd_plano_empresa']) != '' ? "AND crs.cd_empresa = ".intval($args['cd_plano_empresa']) : "")."
			   ".(trim($args['cd_plano']) != '' ? "AND crs.cd_plano = ".intval($args['cd_plano']) : '')."
               ".(trim($args['cd_empresa']) != '' ? "AND crs.cd_empresa = ".intval($args['cd_empresa']) : "")."
               ".(trim($args['cd_registro_empregado']) != '' ? "AND crs.cd_registro_empregado = ".intval($args['cd_registro_empregado']) : "")."
               ".(trim($args['seq_dependencia']) != '' ? "AND crs.seq_dependencia = ".intval($args['seq_dependencia']) : "")."
               ".(((trim($args['dt_referencia_ini']) != '') AND (trim($args['dt_referencia_fim']) != '')) ? "AND DATE_TRUNC('day', crs.dt_referencia) BETWEEN TO_DATE('".$args['dt_referencia_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_referencia_fim']."', 'DD/MM/YYYY')" : "")."
			   ".(trim($args['nr_mes']) != '' ? "AND TO_CHAR(crs.dt_referencia, 'MM') = ".str_escape($args['nr_mes']) : "")."
			   ".(trim($args['nr_ano']) != '' ? "AND TO_CHAR(crs.dt_referencia, 'YYYY') = ".str_escape($args['nr_ano']) : "")."
			   ".(trim($args['cd_contribuicao_relatorio_origem']) != '' ? "AND cro.cd_contribuicao_relatorio_origem = ".intval($args['cd_contribuicao_relatorio_origem']) : "")."
			   ".(trim($args['fl_telefone']) == 'O' ? "AND char_length(crs.ds_telefone) = 11" : "")."
               ".(trim($args['fl_telefone']) == 'I' ? "AND char_length(crs.ds_telefone) != 11 AND crs.ds_telefone != ''" : "")."
			   ".(trim($args['fl_telefone']) == 'C' ? "AND crs.ds_telefone = ''" : "")."
               ".(trim($args['fl_envio_sms']) == 'S' ? "AND s.dt_inclusao IS NOT NULL" : "")."
               ".(trim($args['fl_envio_sms']) == 'N' ? "AND s.dt_inclusao IS NULL" : "")."
               ".(trim($args['fl_gerado']) == 'S' ? "AND (SELECT COUNT(*)
												            FROM projetos.contribuicao_relatorio_sms_geracao_lista crsgl
												            JOIN projetos.contribuicao_relatorio_sms_geracao crsg
												              ON crsgl.cd_contribuicao_relatorio_sms_geracao = crsg.cd_contribuicao_relatorio_sms_geracao
												           WHERE crsgl.cd_contribuicao_relatorio_sms = crs.cd_contribuicao_relatorio_sms) > 0" : '')."
               ".(trim($args['fl_gerado']) == 'N' ? "AND (SELECT COUNT(*)
												            FROM projetos.contribuicao_relatorio_sms_geracao_lista crsgl
												            JOIN projetos.contribuicao_relatorio_sms_geracao crsg
												              ON crsgl.cd_contribuicao_relatorio_sms_geracao = crsg.cd_contribuicao_relatorio_sms_geracao
												           WHERE crsgl.cd_contribuicao_relatorio_sms = crs.cd_contribuicao_relatorio_sms) = 0" : '').";";

               /*
                AND 
                 (
                    TO_CHAR(crs.dt_referencia, 'YYYY/MM') = '2018/01'
                    AND 
                    funcoes.cripto_re(crs.cd_empresa, crs.cd_registro_empregado, crs.seq_dependencia) NOT IN (
                        SELECT funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia)
                          FROM temporario.os51583
                    )
                 )
               */

        return $this->db->query($qr_sql)->result_array();
  	}

  	public function get_relatorio_geracao($contribuicao_relatorio = array())
    {
        $qr_sql = "
            SELECT crs.ds_telefone,
                   crs.ds_mensagem
			  FROM projetos.contribuicao_relatorio_sms crs
			 WHERE crs.dt_exclusao IS NULL
			   AND crs.cd_contribuicao_relatorio_sms IN (".implode(',', $contribuicao_relatorio).");";
  
        return $this->db->query($qr_sql)->result_array();
  	}

  	public function salvar_geracao($args = array())
    {
    	$cd_contribuicao_relatorio_sms_geracao = intval($this->db->get_new_id(
    		'projetos.contribuicao_relatorio_sms_geracao', 
    		'cd_contribuicao_relatorio_sms_geracao'
		));

    	$qr_sql = "
            INSERT INTO projetos.contribuicao_relatorio_sms_geracao
                 (
                    cd_contribuicao_relatorio_sms_geracao,
                 	arquivo,
                    cd_usuario_inclusao
                    
                  )
            VALUES
                 (
                    ".intval($cd_contribuicao_relatorio_sms_geracao).",
                 	".(trim($args['arquivo']) != '' ? str_escape($args['arquivo']) : "DEFAULT").",
                 	".intval($args['cd_usuario'])."
                 )";

        $this->db->query($qr_sql);

        return $cd_contribuicao_relatorio_sms_geracao;
    }

    public function atualiza_geracao($args)
    {
    	$qr_sql = "
            INSERT INTO projetos.contribuicao_relatorio_sms_geracao_lista
                 (
                    cd_contribuicao_relatorio_sms_geracao,
                 	cd_contribuicao_relatorio_sms,
                    cd_usuario_inclusao
                    
                  )
            SELECT ".intval($args['cd_contribuicao_relatorio_sms_geracao']).",
            	   x.column1,
                   ".intval($args['cd_usuario'])." 
		      FROM (VALUES (".implode("),(", $args['contribuicao_relatorio']).")) x";

    	$this->db->query($qr_sql);
    }

    public function gerado_listar()
    {
    	$qr_sql = "
    		SELECT crsg.cd_contribuicao_relatorio_sms_geracao,
    		       crsg.arquivo,
    		       TO_CHAR(crsg.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       funcoes.get_usuario_nome(crsg.cd_usuario_inclusao) AS ds_usuario,
			       (SELECT COUNT(*)
		              FROM projetos.contribuicao_relatorio_sms_geracao_lista crsgl
		             WHERE crsgl.cd_contribuicao_relatorio_sms_geracao = crsg.cd_contribuicao_relatorio_sms_geracao) AS tl_registro
			  FROM projetos.contribuicao_relatorio_sms_geracao crsg;";

		return $this->db->query($qr_sql)->result_array();
    }

    public function carrega_gerado($cd_contribuicao_relatorio_sms_geracao)
    {
    	$qr_sql = "
    		SELECT crsg.cd_contribuicao_relatorio_sms_geracao,
    		       crsg.arquivo,
    		       TO_CHAR(crsg.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       funcoes.get_usuario_nome(crsg.cd_usuario_inclusao) AS ds_usuario
			  FROM projetos.contribuicao_relatorio_sms_geracao crsg
			 WHERE crsg.cd_contribuicao_relatorio_sms_geracao = ".intval($cd_contribuicao_relatorio_sms_geracao).";";

		return $this->db->query($qr_sql)->row_array();
    }

    public function gerato_registro_listar($cd_contribuicao_relatorio_sms_geracao)
    {
    	$qr_sql = "
            SELECT crs.cd_contribuicao_relatorio_sms,
                   cro.ds_contribuicao_relatorio_origem,
			       TO_CHAR(crs.dt_referencia, 'YYYY/MM') AS dt_referencia,
			       crs.cd_empresa, 
			       crs.cd_registro_empregado,
			       crs.seq_dependencia,
			       funcoes.get_plano_nome(crs.cd_plano) AS ds_plano,
			       crs.ds_telefone,
			       TO_CHAR(crs.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
			       funcoes.get_usuario_nome(crs.cd_usuario_inclusao) AS ds_usuario,
			       (SELECT ds_nome FROM funcoes.get_participante(crs.cd_empresa, crs.cd_registro_empregado, crs.seq_dependencia)) AS ds_nome
			  FROM projetos.contribuicao_relatorio_sms_geracao_lista crsgl
			  JOIN projetos.contribuicao_relatorio_sms crs
			    ON crs.cd_contribuicao_relatorio_sms = crsgl.cd_contribuicao_relatorio_sms
			  JOIN projetos.contribuicao_relatorio_origem cro
			    ON crs.cd_contribuicao_relatorio_origem = cro.cd_contribuicao_relatorio_origem
			 WHERE crsgl.cd_contribuicao_relatorio_sms_geracao = ".intval($cd_contribuicao_relatorio_sms_geracao).";";
  
        return $this->db->query($qr_sql)->result_array();
    }

    public function atualiza_telefone($cd_usuario)
    {
    	$qr_sql = "
    		UPDATE projetos.contribuicao_relatorio_sms
    		   SET cd_usuario_alteracao = ".intval($cd_usuario).",
    		       dt_alteracao         = CURRENT_TIMESTAMP,
    		       ds_telefone          = funcoes.get_participante_celular(cd_empresa, cd_registro_empregado, seq_dependencia)
    		 WHERE char_length(ds_telefone) != 11
    		   AND ds_telefone != funcoes.get_participante_celular(cd_empresa, cd_registro_empregado, seq_dependencia);";

    	$this->db->query($qr_sql);
    }

  	public function salvar_contribuicao_controle($args = array())
    {
        $qr_sql = "
            INSERT INTO projetos.contribuicao_relatorio_sms
                 (
                    dt_referencia, 
                    cd_contribuicao_relatorio_origem, 
                    cd_plano, 
                    cd_empresa, 
                    cd_registro_empregado, 
                    seq_dependencia, 
                    ds_telefone, 
                    ds_mensagem,
                    fl_enviar_sms,
                    cd_usuario_inclusao, 
                    cd_usuario_alteracao
                  )
            SELECT '".trim($args['nr_ano'])."/".trim($args['nr_mes'])."/01'::date,
                   ".intval($args['cd_contribuicao_relatorio_origem']).",
                   ".intval($args['cd_plano']).",
                   cd_empresa,
                   cd_registro_empregado, 
                   seq_dependencia, 
                   funcoes.get_participante_celular(cd_empresa, cd_registro_empregado, seq_dependencia),
                   (SELECT gera_link 
                      FROM funcoes.gera_link
                         (
                            '".$args['link']."re='|| funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) || '&comp=' || funcoes.cripto_mes_ano(".intval($args['nr_mes_comp']).", ".intval($args['nr_ano_comp'])."), 
                            cd_empresa,
                            cd_registro_empregado,
                            seq_dependencia
                         )
                   ),
                   '".trim($args['fl_enviar_sms'])."',
                   ".intval($args['cd_usuario']).",
                   ".intval($args['cd_usuario'])."
              FROM projetos.contribuicao_controle
             WHERE nr_ano_competencia            = ".trim($args['nr_ano'])."
               AND nr_mes_competencia            = ".trim($args['nr_mes'])."
               AND cd_empresa                    = ".trim($args['cd_empresa'])."
               AND cd_contribuicao_controle_tipo IN ('".implode("', '", $args['controle_tipo'])."');";

        $this->db->query($qr_sql);
    }
	
  	public function enviarSMS($args = array())
    {
        $qr_sql = "
					UPDATE projetos.contribuicao_relatorio_sms AS crs
					   SET cd_sms = (SELECT id_sms 
									   FROM sms.sms_incluir
										  (
												crs.cd_empresa,crs.cd_registro_empregado,crs.seq_dependencia, -- RE do participante
												crs.ds_telefone::NUMERIC, -- numero do celular com DDD (NUMERIC)
												'Família Prev', -- assunto
												'Participante, seu boleto esta disponivel. Acesse o link: ' || crs.ds_mensagem  || ' boleto impresso será apenas para quem optou.', -- conteudo
												4, --tipo do envio (sms.sms_tipo)
												NULL, --data futura
												".intval($args['cd_usuario'])." -- usuario que enviou (usar NULL quando executado por rotina)
										   ))
					 WHERE crs.dt_exclusao IS NULL
					   AND crs.cd_sms IS NULL
					   AND crs.cd_contribuicao_relatorio_sms IN (".implode(',', $args['ar_contribuicao_relatorio']).")
			      ";
		#echo '<PRE>'.$qr_sql; exit;
        $this->db->query($qr_sql);
  	}	
}
?>