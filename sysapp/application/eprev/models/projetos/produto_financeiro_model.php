<?php
class Produto_financeiro_model extends Model
{

    function __construct()
    {
		parent::Model();
    }
	
	function origem(&$result, $args=array())
    {
		$qr_sql = "
			SELECT cd_produto_financeiro_origem AS value,
				   ds_produto_financeiro_origem AS text
			  FROM projetos.produto_financeiro_origem
			 WHERE dt_exclusao IS NULL
			 ORDER BY ds_produto_financeiro_origem ASC";
		
		$result = $this->db->query($qr_sql);
	}
	
	function responsavel(&$result, $args=array())
    {
		$qr_sql = "
			SELECT pf.cd_usuario_responsavel AS value,
				   uc.nome AS text
			  FROM projetos.produto_financeiro pf
			  JOIN projetos.usuarios_controledi uc
				ON pf.cd_usuario_responsavel = uc.codigo
			 WHERE pf.dt_exclusao IS NULL
			 ORDER BY uc.nome ASC";
		
		$result = $this->db->query($qr_sql);
	}
	
	function revisor(&$result, $args=array())
    {
		$qr_sql = "
			SELECT pf.cd_usuario_revisor AS value,
				   uc.nome AS text
			  FROM projetos.produto_financeiro pf
			  JOIN projetos.usuarios_controledi uc
				ON pf.cd_usuario_revisor = uc.codigo
			 WHERE pf.dt_exclusao IS NULL
			 ORDER BY uc.nome DESC";
		
		$result = $this->db->query($qr_sql);
	}
	
	function entidade_fornecedor(&$result, $args=array())
    {
		$qr_sql = "
			SELECT rsi.cd_reuniao_sg_instituicao AS value,
				   rsi.ds_reuniao_sg_instituicao AS text
			  FROM projetos.reuniao_sg_instituicao rsi
			  JOIN projetos.produto_financeiro pf
				ON pf.cd_reuniao_sg_instituicao = rsi.cd_reuniao_sg_instituicao
			 WHERE pf.dt_exclusao IS NULL
			   AND rsi.dt_exclusao IS NULL
			 ORDER BY rsi.ds_reuniao_sg_instituicao ASC";
		
		$result = $this->db->query($qr_sql);
	}
		
	function usuarios_gin(&$result, $args=array())
    {
		$qr_sql = "
			SELECT codigo AS value,
				   nome AS text
			  FROM projetos.usuarios_controledi
			 WHERE divisao = 'GIN'
			   AND tipo <> 'X'	
			 ORDER BY nome ASC";
		
		$result = $this->db->query($qr_sql);
	}
	
	function listar(&$result, $args=array())
    {	
		$qr_sql = "
			SELECT pf.cd_produto_financeiro,
				   pf.ds_produto,
				   TO_CHAR(pf.dt_recebido, 'DD/MM/YYYY') AS dt_recebido,
				   TO_CHAR(pf.dt_conclusao, 'DD/MM/YYYY') AS dt_conclusao,
				   COALESCE((SELECT SUM((pfes.nr_concluido * pfes.nr_peso) / 100)
					           FROM projetos.produto_financeiro_etapa_status pfes
					          WHERE pfes.cd_produto_financeiro = pf.cd_produto_financeiro
						        AND pfes.dt_exclusao IS NULL), 0) AS nr_concluido,
				   uc.nome AS responsavel,
				   uc2.nome AS revisor,
				   rsi.ds_reuniao_sg_instituicao,
				   CASE WHEN pf.dt_atualizacao > (SELECT MAX(pfes1.dt_atualizacao)
                                                    FROM projetos.produto_financeiro_etapa_status pfes1
                                                   WHERE pfes1.dt_exclusao IS NULL)
						THEN TO_CHAR(pf.dt_atualizacao, 'DD/MM/YYYY HH24:MI:SS')
						ELSE TO_CHAR((SELECT MAX(pfes1.dt_atualizacao)
                                        FROM projetos.produto_financeiro_etapa_status pfes1
                                       WHERE pfes1.dt_exclusao IS NULL), 'DD/MM/YYYY HH24:MI:SS')
				   END AS dt_atualizacao
			  FROM projetos.produto_financeiro pf
			  JOIN projetos.usuarios_controledi uc
			    ON pf.cd_usuario_responsavel = uc.codigo
			  JOIN projetos.usuarios_controledi uc2
			    ON pf.cd_usuario_revisor = uc2.codigo
			  JOIN projetos.reuniao_sg_instituicao rsi
				ON pf.cd_reuniao_sg_instituicao = rsi.cd_reuniao_sg_instituicao
			 WHERE pf.dt_exclusao IS NULL
			   AND rsi.dt_exclusao IS NULL
			   ".(trim($args['ds_produto']) != '' ? "AND pf.ds_produto LIKE ('%".trim($args['ds_produto'])."%')" : '')."
			   ".(trim($args['cd_produto_financeiro_origem']) != '' ? "AND pf.cd_produto_financeiro_origem = ".intval($args['cd_produto_financeiro_origem']) : '')."
			   ".(trim($args['cd_reuniao_sg_instituicao']) != '' ? "AND pf.cd_reuniao_sg_instituicao = ".intval($args['cd_reuniao_sg_instituicao']) : '')."
			   ".(trim($args['cd_usuario_responsavel']) != '' ? "AND pf.cd_usuario_responsavel = ".intval($args['cd_usuario_responsavel']) : '')."
			   ".(trim($args['cd_usuario_revisor']) != '' ? "AND pf.cd_usuario_revisor = ".intval($args['cd_usuario_revisor']) : '')."
			   " . (((trim($args['dt_recebido_ini']) != "") and (trim($args['dt_recebido_fim']) != "")) ? " AND DATE_TRUNC('day', pf.dt_recebido) BETWEEN TO_DATE('" . $args['dt_recebido_ini'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_recebido_fim'] . "', 'DD/MM/YYYY')" : "") . "
			   " . (((trim($args['dt_cadastro_ini']) != "") and (trim($args['dt_cadastro_fim']) != "")) ? " AND DATE_TRUNC('day', pf.dt_inclusao) BETWEEN TO_DATE('" . $args['dt_cadastro_ini'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_cadastro_fim'] . "', 'DD/MM/YYYY')" : "") . "
			   " . (((trim($args['dt_conclusao_ini']) != "") and (trim($args['dt_conclusao_ini']) != "")) ? " AND DATE_TRUNC('day', pf.dt_conclusao) BETWEEN TO_DATE('" . $args['dt_conclusao_ini'] . "', 'DD/MM/YYYY') AND TO_DATE('" . $args['dt_conclusao_ini'] . "', 'DD/MM/YYYY')" : "") . "
			 ORDER BY pf.dt_recebido DESC";

		#echo "<PRE>".$qr_sql."</PRE>"; exit;
			 
		$result = $this->db->query($qr_sql);
	}
	
	function cadastro(&$result, $args=array())
    {
		$qr_sql = "
			SELECT cd_produto_financeiro,
                   TO_CHAR(dt_recebido, 'DD/MM/YYYY') AS dt_recebido,
                   ds_produto,
				   cd_produto_financeiro_origem,
				   cd_reuniao_sg_instituicao,
				   contato,
				   cd_usuario_responsavel,
				   cd_usuario_revisor,
				   cd_usuario_inclusao,
				   dt_conclusao
		      FROM projetos.produto_financeiro
			 WHERE cd_produto_financeiro = ".intval($args['cd_produto_financeiro']);
		$result = $this->db->query($qr_sql);	
	}
	
	function salvar(&$result, $args=array())
	{
		$retorno = 0;

		if(intval($args['cd_produto_financeiro']) > 0)
		{
			$retorno = intval($args['cd_produto_financeiro']);
			
			$qr_sql = "
				UPDATE projetos.produto_financeiro
				   SET dt_recebido                  = TO_DATE('".trim($args['dt_recebido'])."', 'DD/MM/YYYY'),
					   ds_produto                   = '".trim($args['ds_produto'])."',
				       cd_produto_financeiro_origem = ".intval($args['cd_produto_financeiro_origem']).",
					   cd_reuniao_sg_instituicao    = ".intval($args['cd_reuniao_sg_instituicao']).",
					   contato                      = ".(trim($args['contato']) != '' ? "'".trim($args['contato'])."'"  : "DEFAULT").",
					   cd_usuario_responsavel       = ".intval($args['cd_usuario_responsavel']).",
					   cd_usuario_revisor           = ".intval($args['cd_usuario_revisor']).",
					   cd_usuario_atualizacao       = ".intval($args['cd_usuario']).",
					   dt_conclusao                 = TO_DATE('".trim($args['dt_conclusao'])."', 'DD/MM/YYYY'),
					   dt_atualizacao               = CURRENT_TIMESTAMP  
				 WHERE cd_produto_financeiro = ".intval($retorno).";";
		}
		else
		{
			$retorno = intval($this->db->get_new_id("projetos.produto_financeiro", "cd_produto_financeiro"));
			
			$qr_sql = "
				INSERT INTO projetos.produto_financeiro
				     (
						cd_produto_financeiro,
						dt_recebido,
						ds_produto,
						cd_produto_financeiro_origem,
						cd_reuniao_sg_instituicao,
						contato,
						cd_usuario_responsavel,
						cd_usuario_revisor,
						cd_usuario_inclusao,
						cd_usuario_atualizacao,
						dt_conclusao
					 )
			    VALUES
				     (
						".intval($retorno).",
						TO_DATE('".trim($args['dt_recebido'])."', 'DD/MM/YYYY'),
						'".trim($args['ds_produto'])."',
						".intval($args['cd_produto_financeiro_origem']).",
						".intval($args['cd_reuniao_sg_instituicao']).",
						".(trim($args['contato']) != '' ? "'".trim($args['contato'])."'"  : "DEFAULT").",
						".intval($args['cd_usuario_responsavel']).",
						".intval($args['cd_usuario_revisor']).",
						".intval($args['cd_usuario']).",
						".intval($args['cd_usuario']).",
						TO_DATE('".trim($args['dt_conclusao'])."', 'DD/MM/YYYY')
					 )";
			
		}
		
		$this->db->query($qr_sql);	
		
		return $retorno;
	}
	
	function salvar_etapas(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.produto_financeiro_etapa_status
			     (
					cd_produto_financeiro,
					cd_produto_financeiro_etapa,
					cd_usuario_inclusao,
					cd_usuario_atualizacao,
					dt_atualizacao,
					nr_peso
				 )
		    VALUES
			     (
				   ".intval($args['cd_produto_financeiro']).",
				   ".intval($args['cd_produto_financeiro_etapa']).",
				   ".intval($args['cd_usuario']).",
				   ".intval($args['cd_usuario']).",
				   CURRENT_TIMESTAMP,
				   0
				 );
			
			";
/*
UPDATE projetos.produto_financeiro_etapa_status AS p
			   SET nr_peso = TRUNC((100 / (COALESCE((SELECT COUNT(*)
                                                       FROM projetos.produto_financeiro_etapa_status p1
                                                      WHERE p1.cd_produto_financeiro = p.cd_produto_financeiro
													    AND p1.dt_exclusao IS NULL),1))))
             WHERE p.cd_produto_financeiro = ".intval($args['cd_produto_financeiro']).";

            UPDATE projetos.produto_financeiro_etapa_status AS p
               SET nr_peso = nr_peso + (100 - (SELECT SUM(p1.nr_peso)
                                                 FROM projetos.produto_financeiro_etapa_status p1
                                                WHERE p1.cd_produto_financeiro = p.cd_produto_financeiro
												  AND p1.dt_exclusao IS NULL))
             WHERE p.cd_produto_financeiro = ".intval($args['cd_produto_financeiro'])."
               AND p.cd_produto_financeiro_etapa_status  = (SELECT MAX(p2.cd_produto_financeiro_etapa_status)
                                                              FROM projetos.produto_financeiro_etapa_status p2
                                                             WHERE p2.cd_produto_financeiro = p.cd_produto_financeiro
															   AND p2.dt_exclusao IS NULL);
*/		
		$this->db->query($qr_sql);	
	}
	
	function listar_etapas(&$result, $args=array())
	{
		$qr_sql = "
			SELECT pfes.cd_produto_financeiro,
			       pfes.cd_produto_financeiro_etapa_status,
				   pfes.cd_produto_financeiro_etapa,
				   pfe.ds_produto_financeiro_etapa, 
			       pfes.nr_peso,
				   pfes.nr_concluido,
				   pfes.observacao,
				   pfes.nr_ordem
			  FROM projetos.produto_financeiro_etapa_status pfes
			  JOIN projetos.produto_financeiro_etapa pfe
			    ON pfes.cd_produto_financeiro_etapa = pfe.cd_produto_financeiro_etapa
			 WHERE pfes.cd_produto_financeiro = ".intval($args['cd_produto_financeiro'])."
			   AND pfes.dt_exclusao IS NULL
			   AND pfe.dt_exclusao IS NULL
			 ORDER BY pfes.nr_ordem ASC";
		
		$result = $this->db->query($qr_sql);
	}
	
	function atualiza_etapas(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.produto_financeiro_etapa_status
			   SET nr_peso      = ".intval($args['nr_peso']).",
			       nr_concluido = ".intval($args['nr_concluido']).",
				   observacao   = ".(trim($args['observacao']) != '' ? "'".trim($args['observacao'])."'"  : "DEFAULT").",
				   nr_ordem     = ".(trim($args['nr_ordem']) != '' ? intval($args['nr_ordem'])  : "DEFAULT")."
			 WHERE cd_produto_financeiro_etapa_status = ".intval($args['cd_produto_financeiro_etapa_status']).";";

		$this->db->query($qr_sql);
	}
	
	function excluir_etapa(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.produto_financeiro_etapa_status
			   SET dt_exclusao = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_produto_financeiro_etapa_status = ".intval($args['cd_produto_financeiro_etapa_status']).";
			 
			";

/*
UPDATE projetos.produto_financeiro_etapa_status AS p
			   SET nr_peso = TRUNC((100 / (COALESCE((SELECT COUNT(*)
                                                       FROM projetos.produto_financeiro_etapa_status p1
                                                      WHERE p1.cd_produto_financeiro = p.cd_produto_financeiro
													    AND p1.dt_exclusao IS NULL),1))))
             WHERE p.cd_produto_financeiro = ".intval($args['cd_produto_financeiro']).";

            UPDATE projetos.produto_financeiro_etapa_status AS p
               SET nr_peso = nr_peso + (100 - (SELECT SUM(p1.nr_peso)
                                                 FROM projetos.produto_financeiro_etapa_status p1
                                                WHERE p1.cd_produto_financeiro = p.cd_produto_financeiro
												  AND p1.dt_exclusao IS NULL))
             WHERE p.cd_produto_financeiro = ".intval($args['cd_produto_financeiro'])."
               AND p.cd_produto_financeiro_etapa_status  = (SELECT MAX(p2.cd_produto_financeiro_etapa_status)
                                                              FROM projetos.produto_financeiro_etapa_status p2
                                                             WHERE p2.cd_produto_financeiro = p.cd_produto_financeiro
															   AND p2.dt_exclusao IS NULL);
*/			
			
		$this->db->query($qr_sql);
	}
	
	function listar_todas_etapas(&$result, $args=array())
	{
		$qr_sql = "
			SELECT dropdown_db.cd_produto_financeiro_etapa
			  FROM projetos.produto_financeiro_etapa dropdown_db
			 WHERE dropdown_db.dt_exclusao IS NULL 
		 	   AND 0 = (SELECT COUNT(*) 
			              FROM projetos.produto_financeiro_etapa_status pfes 
					     WHERE pfes.cd_produto_financeiro = ".intval($args['cd_produto_financeiro'])." 
					       AND pfes.dt_exclusao IS NULL 
						   AND pfes.cd_produto_financeiro_etapa = dropdown_db.cd_produto_financeiro_etapa);";
						   
	    $result = $this->db->query($qr_sql);
	}
	
	function salvar_anexo(&$result, $args=array())
	{
		$qr_sql = "
			INSERT INTO projetos.produto_financeiro_anexo
			     (
					cd_produto_financeiro,
					arquivo,
					arquivo_nome,
					cd_usuario_inclusao
				 )
		    VALUES
			     (
					".intval($args['cd_produto_financeiro']).",
					'".trim($args['arquivo'])."',
					'".trim($args['arquivo_nome'])."',
					".intval($args['cd_usuario'])."
				 )";
				 
		$result = $this->db->query($qr_sql);
	}
	
	function listar_anexos(&$result, $args=array())
	{
		$qr_sql = "
			SELECT cd_produto_financeiro_anexo,
				   arquivo,
				   arquivo_nome,
				   TO_CHAR(dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_inclusao
			  FROM projetos.produto_financeiro_anexo
			 WHERE dt_exclusao IS NULL
               AND cd_produto_financeiro = ". intval($args['cd_produto_financeiro']);
		$result = $this->db->query($qr_sql);
	}
	
	function excluir_anexo(&$result, $args=array())
	{
		$qr_sql = "
			UPDATE projetos.produto_financeiro_anexo
			   SET dt_exclusao         = CURRENT_TIMESTAMP,
			       cd_usuario_exclusao = ".intval($args['cd_usuario'])."
			 WHERE cd_produto_financeiro = ". intval($args['cd_produto_financeiro']);
		$result = $this->db->query($qr_sql);
	}
	
}
?>