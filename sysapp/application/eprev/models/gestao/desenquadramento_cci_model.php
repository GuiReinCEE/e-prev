<?php
class desenquadramento_cci_model extends Model
{
    function __construct()
    {
        parent::Model();
    }

    function fundo(&$result, $args=array())
    {
        $qr_sql = "
			SELECT cd_desenquadramento_cci_fundo AS value, 
			       ds_desenquadramento_cci_fundo AS text
			  FROM gestao.desenquadramento_cci_fundo
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_desenquadramento_cci_fundo;";

        $result = $this->db->query($qr_sql);
    }
	
	function listar(&$result, $args=array())
    {
        $qr_sql = "
			SELECT dc.cd_desenquadramento_cci, 
			       gestao.nr_desenquadramento_cci(dc.nr_ano, dc.nr_numero) AS ano_numero,
				   TO_CHAR(dc.dt_desenquadramento_cci, 'DD/MM/YYYY') AS dt_desenquadramento_cci, 
                   ds_desenquadramento_cci_fundo, 
				   CASE WHEN dc.fl_status = 'P' THEN 'Desenquadrado'
				        WHEN dc.fl_status = 'R' THEN 'Regularizado'
				        WHEN dc.fl_status = 'D' THEN 'Desenquadramento Passivo'
						ELSE ''
				   END AS status,
				   CASE WHEN dc.fl_status = 'P' THEN 'label-important'
				        WHEN dc.fl_status = 'R' THEN 'label-success'
				        WHEN dc.fl_status = 'D' THEN 'label-warning'
						ELSE ''
				   END AS class_status,
                   TO_CHAR(dc.dt_encaminhado, 'DD/MM/YYYY HH24:MI:SS') AS dt_encaminhado,
                   TO_CHAR(dc.dt_enviado, 'DD/MM/YYYY HH24:MI:SS') AS dt_enviado,
				   dcf.ds_desenquadramento_cci_fundo,
				   uc.nome AS usuario_inclusao,
				   uc2.nome AS usuario_enviado,
				   dc.cd_desenquadramento_cci_pai,
				   gestao.nr_desenquadramento_cci(dc1.nr_ano, dc1.nr_numero) AS ano_numero_pai,
				   TO_CHAR(dc1.dt_desenquadramento_cci, 'DD/MM/YYYY') AS dt_desenquadramento_cci_pai,
				   gestao.nr_desenquadramento_cci(dc2.nr_ano, dc2.nr_numero) AS ano_numero_filho,
				   TO_CHAR(dc2.dt_desenquadramento_cci, 'DD/MM/YYYY') AS dt_desenquadramento_cci_filho
			  FROM gestao.desenquadramento_cci dc
			  JOIN gestao.desenquadramento_cci_fundo dcf
				ON dcf.cd_desenquadramento_cci_fundo = dc.cd_desenquadramento_cci_fundo
			  JOIN projetos.usuarios_controledi uc 
			    ON uc.codigo = dc.cd_usuario_inclusao
			  LEFT JOIN projetos.usuarios_controledi uc2
			    ON uc2.codigo = dc.cd_usuario_enviado
			  LEFT JOIN gestao.desenquadramento_cci dc1
                ON dc1.cd_desenquadramento_cci = dc.cd_desenquadramento_cci_pai
			  LEFT JOIN gestao.desenquadramento_cci dc2
                ON dc2.cd_desenquadramento_cci_pai = dc.cd_desenquadramento_cci				
		     WHERE dc.dt_exclusao IS NULL
			   AND dc2.cd_desenquadramento_cci_pai IS NULL -- NAO MOSTRA SE TEM UM PAI
			   ".(trim($args['nr_ano']) != '' ? "AND dc.nr_ano = ".intval($args['nr_ano']) : "")."
			   ".(trim($args['nr_numero']) != '' ? "AND dc.nr_numero = ".intval($args['nr_numero']) : "")."
			   ".(trim($args['cd_desenquadramento_cci_fundo']) != '' ? "AND dc.cd_desenquadramento_cci_fundo = ".intval($args['cd_desenquadramento_cci_fundo']) : "")."
			   ".(trim($args['fl_status']) != '' ? "AND dc.fl_status = '".trim($args['fl_status'])."'" : "")."
			   ".(trim($args['fl_encaminhado']) == 'S' ? "AND dc.dt_encaminhado IS NOT NULL" : "")."
			   ".(trim($args['fl_encaminhado']) == 'N' ? "AND dc.dt_encaminhado IS NULL" : "")."
			   ".(trim($args['fl_envio']) == 'S' ? "AND dc.dt_enviado IS NOT NULL" : "")."
			   ".(trim($args['fl_envio']) == 'N' ? "AND dc.dt_enviado IS NULL" : "")."
			   ".(((trim($args['dt_ini']) != "") AND (trim($args['dt_fim']) != "")) ? " AND DATE_TRUNC('day', dc.dt_desenquadramento_cci) BETWEEN TO_DATE('".$args['dt_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_fim']."', 'DD/MM/YYYY')" : "")."
               ".(((trim($args['dt_relatorio_ini']) != "") AND (trim($args['dt_relatorio_fim']) != "")) ? " AND (DATE_TRUNC('day', dc.dt_enviado) BETWEEN TO_DATE('".$args['dt_relatorio_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_relatorio_fim']."', 'DD/MM/YYYY') OR dc.fl_status = 'P')" : "")."
			   ".(((trim($args['dt_encaminhado_ini']) != "") AND (trim($args['dt_encaminhado_fim']) != "")) ? " AND DATE_TRUNC('day', dc.dt_encaminhado) BETWEEN TO_DATE('".$args['dt_encaminhado_ini']."', 'DD/MM/YYYY') AND TO_DATE('".$args['dt_encaminhado_fim']."', 'DD/MM/YYYY') " : "").";";

        $result = $this->db->query($qr_sql);
    }
	
	function carrega(&$result, $args=array())
	{
		$qr_sql = "
			SELECT dc.cd_desenquadramento_cci,
			       dc.cd_desenquadramento_cci_pai,
			       gestao.nr_desenquadramento_cci(dc.nr_ano, dc.nr_numero) AS ano_numero,
			       TO_CHAR(dc.dt_desenquadramento_cci, 'DD/MM/YYYY') AS dt_desenquadramento_cci, 
				   dc.cd_desenquadramento_cci_fundo,
				   dc.cd_desenquadramento_cci_administrador,
				   dc.cd_desenquadramento_cci_gestor,
				   dc.regra,
				   dc.ds_desenquadramento_cci,
				   dc.providencias_adotadas,
				   dc.fl_status,
				   dc.observacao,
				   TO_CHAR(dc.dt_regularizado, 'DD/MM/YYYY') AS dt_regularizado, 
				   TO_CHAR(dc.dt_encaminhado, 'DD/MM/YYYY') AS dt_encaminhado,
				   TO_CHAR(dc.dt_enviado, 'DD/MM/YYYY') AS dt_enviado,
				   dcf.ds_desenquadramento_cci_fundo,
				   dca.ds_desenquadramento_cci_administrador,
				   dcg.ds_desenquadramento_cci_gestor,
				   uc.nome AS usuario_inclusao,
				   uc2.nome AS usuario_enviado,
				   gestao.nr_desenquadramento_cci(dc1.nr_ano, dc1.nr_numero) AS ano_numero_pai,
				   TO_CHAR(dc1.dt_desenquadramento_cci, 'DD/MM/YYYY') AS dt_desenquadramento_cci_pai,
				   gestao.nr_desenquadramento_cci(dc2.nr_ano, dc2.nr_numero) AS ano_numero_filho,
				   TO_CHAR(dc2.dt_desenquadramento_cci, 'DD/MM/YYYY') AS dt_desenquadramento_cci_filho,
				   CASE WHEN dc.fl_status = 'P' THEN 'Desenquadrado'
					    WHEN dc.fl_status = 'R' THEN 'Regularizado'
					    WHEN dc.fl_status = 'D' THEN 'Desenquadramento Passivo'
					   ELSE ''
				   END AS ds_status,
				   TO_CHAR(dc.dt_enviado, 'YYYY') AS ds_ano_edicao,
			       TO_CHAR(dc.dt_enviado, 'MM') AS ds_mes_edicao
			  FROM gestao.desenquadramento_cci dc
			  LEFT JOIN gestao.desenquadramento_cci_fundo dcf
			    ON dcf.cd_desenquadramento_cci_fundo = dc.cd_desenquadramento_cci_fundo
			  LEFT JOIN gestao.desenquadramento_cci_administrador dca
			    ON dca.cd_desenquadramento_cci_administrador = dc.cd_desenquadramento_cci_administrador
			  LEFT JOIN gestao.desenquadramento_cci_gestor dcg
			    ON dcg.cd_desenquadramento_cci_gestor = dc.cd_desenquadramento_cci_gestor
		      JOIN projetos.usuarios_controledi uc 
			    ON uc.codigo = dc.cd_usuario_inclusao
			  LEFT JOIN projetos.usuarios_controledi uc2
			    ON uc2.codigo = dc.cd_usuario_enviado
			  LEFT JOIN gestao.desenquadramento_cci dc1
                ON dc1.cd_desenquadramento_cci = dc.cd_desenquadramento_cci_pai
			  LEFT JOIN gestao.desenquadramento_cci dc2
                ON dc2.cd_desenquadramento_cci_pai = dc.cd_desenquadramento_cci
			 WHERE dc.cd_desenquadramento_cci = ".intval($args['cd_desenquadramento_cci']).";";
			 
		$result = $this->db->query($qr_sql);
	}

	public function carrega_md5($cd_desenquadramento_cci_md5)
	{
		$qr_sql = "
			SELECT cd_desenquadramento_cci
			  FROM gestao.desenquadramento_cci
			 WHERE MD5(cd_desenquadramento_cci::text) = '".trim($cd_desenquadramento_cci_md5)."'
			   AND dt_exclusao IS NULL;";
			 
		return $this->db->query($qr_sql)->row_array();
	}
	
	function salvar(&$result, $args=array())
	{
		if(intval($args['cd_desenquadramento_cci']) == 0)
		{
			$cd_desenquadramento_cci = intval($this->db->get_new_id("gestao.desenquadramento_cci", "cd_desenquadramento_cci"));
		
			$qr_sql = "
				INSERT INTO gestao.desenquadramento_cci
				     (
					   cd_desenquadramento_cci,
					   cd_desenquadramento_cci_pai,
                       dt_desenquadramento_cci, 
                       cd_desenquadramento_cci_fundo, 
					   cd_desenquadramento_cci_administrador, 
                       cd_desenquadramento_cci_gestor, 
					   ds_desenquadramento_cci, 
					   regra, 
                       observacao, 
					   providencias_adotadas, 
					   fl_status, 
					   dt_regularizado, 
                       cd_usuario_inclusao, 
					   cd_usuario_alteracao
					 )
                VALUES 
				     (
					   ".intval($cd_desenquadramento_cci).",
					   ".(intval($args['cd_desenquadramento_cci_pai']) > 0  ? intval($args['cd_desenquadramento_cci_pai']) : "DEFAULT").",
					   ".(trim($args['dt_desenquadramento_cci']) != '' ? "TO_DATE('".trim($args['dt_desenquadramento_cci'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					   ".(trim($args['cd_desenquadramento_cci_fundo']) != '' ? intval($args['cd_desenquadramento_cci_fundo']) : "DEFAULT").",
					   ".(trim($args['cd_desenquadramento_cci_administrador']) != '' ? intval($args['cd_desenquadramento_cci_administrador']) : "DEFAULT").",
					   ".(trim($args['cd_desenquadramento_cci_gestor']) != '' ? intval($args['cd_desenquadramento_cci_gestor']) : "DEFAULT").",
					   ".(trim($args['ds_desenquadramento_cci']) != '' ? str_escape($args['ds_desenquadramento_cci']) : "DEFAULT").",
					   ".(trim($args['regra']) != '' ? str_escape($args['regra']) : "DEFAULT").",
					   ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
					   ".(trim($args['providencias_adotadas']) != '' ? str_escape($args['providencias_adotadas']) : "DEFAULT").",
					   ".(trim($args['fl_status']) != '' ? str_escape($args['fl_status']) : "DEFAULT").",
					   ".(trim($args['dt_regularizado']) != '' ? "TO_DATE('".trim($args['dt_regularizado'])."', 'DD/MM/YYYY')" : "DEFAULT").",
					   ".intval($args['cd_usuario']).",
					   ".intval($args['cd_usuario'])."
					 );";
		}
		else
		{
			$cd_desenquadramento_cci = intval($args['cd_desenquadramento_cci']);
		
			$qr_sql = "
				UPDATE gestao.desenquadramento_cci
                   SET dt_desenquadramento_cci               = ".(trim($args['dt_desenquadramento_cci']) != '' ? "TO_DATE('".trim($args['dt_desenquadramento_cci'])."', 'DD/MM/YYYY')" : "DEFAULT").",
                       cd_desenquadramento_cci_fundo         = ".(trim($args['cd_desenquadramento_cci_fundo']) != '' ? intval($args['cd_desenquadramento_cci_fundo']) : "DEFAULT").",
					   cd_desenquadramento_cci_administrador = ".(trim($args['cd_desenquadramento_cci_administrador']) != '' ? intval($args['cd_desenquadramento_cci_administrador']) : "DEFAULT").",
                       cd_desenquadramento_cci_gestor        = ".(trim($args['cd_desenquadramento_cci_gestor']) != '' ? intval($args['cd_desenquadramento_cci_gestor']) : "DEFAULT").",
					   ds_desenquadramento_cci               = ".(trim($args['ds_desenquadramento_cci']) != '' ? str_escape($args['ds_desenquadramento_cci']) : "DEFAULT").",
                       regra                                 = ".(trim($args['regra']) != '' ? str_escape($args['regra']) : "DEFAULT").",
					   observacao                            = ".(trim($args['observacao']) != '' ? str_escape($args['observacao']) : "DEFAULT").",
					   providencias_adotadas                 = ".(trim($args['providencias_adotadas']) != '' ? str_escape($args['providencias_adotadas']) : "DEFAULT").",
					   fl_status                             = ".(trim($args['fl_status']) != '' ? str_escape($args['fl_status']) : "DEFAULT").",
                       dt_regularizado                       = ".(trim($args['dt_regularizado']) != '' ? "TO_DATE('".trim($args['dt_regularizado'])."', 'DD/MM/YYYY')" : "DEFAULT").",     
					   cd_usuario_alteracao                  = ".intval($args['cd_usuario']).",
					   dt_alteracao                          = CURRENT_TIMESTAMP
				WHERE cd_desenquadramento_cci = ".intval($args['cd_desenquadramento_cci']).";";
		}
		
		$result = $this->db->query($qr_sql);
		
		return $cd_desenquadramento_cci;
	}

	function excluir($args=array())
	{
		$qr_sql = "
			UPDATE gestao.desenquadramento_cci
               SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
			       dt_exclusao         = CURRENT_TIMESTAMP
			 WHERE cd_desenquadramento_cci = ".intval($args['cd_desenquadramento_cci']).";";
			 	  		
		$result = $this->db->query($qr_sql);
	}
	
	function encaminhar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.desenquadramento_cci
               SET cd_usuario_encaminhado = ".intval($args['cd_usuario']).",
			       dt_encaminhado         = CURRENT_TIMESTAMP
			 WHERE cd_desenquadramento_cci = ".intval($args['cd_desenquadramento_cci']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function confirmar(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.desenquadramento_cci
               SET cd_usuario_enviado = ".intval($args['cd_usuario']).",
			       dt_enviado         = CURRENT_TIMESTAMP
			 WHERE cd_desenquadramento_cci = ".intval($args['cd_desenquadramento_cci']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function devolver(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.desenquadramento_cci
               SET cd_usuario_encaminhado = NULL,
			       dt_encaminhado         = NULL
			 WHERE cd_desenquadramento_cci = ".intval($args['cd_desenquadramento_cci']).";";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function lista_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			SELECT ac.cd_desenquadramento_cci_acompanhamento,
			       ac.descricao,
				   TO_CHAR(ac.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao,
				   uc.nome
			  FROM gestao.desenquadramento_cci_acompanhamento ac
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = ac.cd_usuario_inclusao
			 WHERE ac.dt_exclusao IS NULL
			   AND ac.cd_desenquadramento_cci = ".intval($args['cd_desenquadramento_cci'])."
			 ORDER BY ac.dt_inclusao DESC";
			   
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO gestao.desenquadramento_cci_acompanhamento
				 (
				   cd_desenquadramento_cci,
				   descricao,
				   cd_usuario_inclusao,
				   cd_usuario_alteracao
				 )
			VALUES
				 (
				   ".intval($args['cd_desenquadramento_cci']).",
				   ".str_escape(utf8_decode($args['descricao'])).",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario'])."
				 )";
		
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_acompanhamento(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.desenquadramento_cci_acompanhamento
			   SET dt_exclusao = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_desenquadramento_cci_acompanhamento = ".intval($args['cd_desenquadramento_cci_acompanhamento']);
			 
		$result = $this->db->query($qr_sql);
	}
	
	function lista_anexo(&$result, $args=array())
	{
		$qr_sql = "
			SELECT an.cd_desenquadramento_cci_anexo,
			       an.arquivo,
				   an.arquivo_nome,
				   uc.nome,
				   TO_CHAR(an.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM gestao.desenquadramento_cci_anexo an
			  JOIN projetos.usuarios_controledi uc
			    ON uc.codigo = an.cd_usuario_inclusao
			 WHERE an.dt_exclusao IS NULL
			   AND an.cd_desenquadramento_cci = ".intval($args['cd_desenquadramento_cci'])."";
			
		$result = $this->db->query($qr_sql);
	}
	
	function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO gestao.desenquadramento_cci_anexo
			     (
					cd_desenquadramento_cci,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_desenquadramento_cci']).",
					".str_escape($args['arquivo']).",
					".str_escape($args['arquivo_nome']).",
					".intval($args['cd_usuario'])."
				 )";
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE gestao.desenquadramento_cci_anexo
			   SET cd_usuario_exclusao = ".intval($args['cd_usuario']).",
				   dt_exclusao         = CURRENT_TIMESTAMP
		     WHERE cd_desenquadramento_cci_anexo = ".intval($args['cd_desenquadramento_cci_anexo']).";";
		$this->db->query($qr_sql);
	}	
}
?>