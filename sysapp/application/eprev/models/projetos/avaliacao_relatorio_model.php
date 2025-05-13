<?php
class avaliacao_relatorio_model extends Model
{
	function __construct()
	{
		parent::Model();
	}

	function listar( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cp.cd_avaliacao_capa,
			       TO_CHAR(cp.dt_publicacao,'DD/MM/YYYY HH24:MI:SS') AS dt_publicacao,
                   cp.grau_escolaridade,
                   cp.cd_usuario_avaliado,
				   av.cd_registro_empregado AS re_avaliado,
				   av.nome AS nome_avaliado,
				   cp.cd_usuario_avaliador,
				   avr.cd_registro_empregado AS re_avaliador,
                   avr.nome AS nome_avaliador,
                   cp.dt_periodo,
                   cp.media_geral,
                   cp.tipo_promocao,
				   cp.cd_cargo,
				   c.nome_cargo,
				   c.cd_familia,
				   fc.nome_familia,
				   cp.fl_acordo,
				   av.divisao,
				   CASE WHEN cp.tipo_promocao = 'V' THEN 'Vertical'
						WHEN cp.tipo_promocao = 'H' THEN 'Horizontal'
				        ELSE 'Não identificado'
				   END AS tipo_promocao,
				   CASE WHEN cp.tipo_promocao = 'V' THEN 'label-info'
						WHEN cp.tipo_promocao = 'H' THEN 'label-success'
				        ELSE ''
				   END AS tipo_promocao_color,
				   CASE WHEN cp.fl_acordo = 'A' THEN 'Concordou com o resultado'
				        WHEN cp.fl_acordo = 'C' THEN 'Ciente do resultado'
						ELSE 'Não informado'
				   END AS acordo,
				   CASE WHEN cp.fl_acordo = 'C' THEN 'label-info'
						WHEN cp.fl_acordo = 'A' THEN 'label-success'
				        ELSE ''
				   END AS acordo_color,
				   (SELECT cd_avaliacao 
				      FROM projetos.avaliacao 
					 WHERE tipo = 'A' 
					   AND cd_avaliacao_capa = cp.cd_avaliacao_capa) AS cd_avaliacao_tipo_a,
				   (SELECT cd_avaliacao 
				      FROM projetos.avaliacao 
					 WHERE tipo = 'S' 
					   AND cd_avaliacao_capa = cp.cd_avaliacao_capa) AS cd_avaliacao_tipo_s,
					CASE WHEN (0 < (SELECT COUNT(c1.*)
									  FROM projetos.avaliacao_capa c1
									  LEFT JOIN projetos.avaliacao_capa c2 -- VERTICAL
									    ON c2.dt_periodo = (c1.dt_periodo - 1)
									   AND c2.cd_usuario_avaliado = c1.cd_usuario_avaliado
									   AND c2.tipo_promocao = 'V'
									  LEFT JOIN projetos.avaliacao_capa c3
									    ON c3.dt_periodo = (c1.dt_periodo - 1)
									   AND c3.cd_usuario_avaliado = c1.cd_usuario_avaliado
									   AND c3.tipo_promocao = 'H'
									   AND COALESCE(c3.media_geral,0) > 70
									  JOIN projetos.usuario_matriz um
									    ON um.cd_usuario = c1.cd_usuario_avaliado
									   AND um.tipo_promocao = 'H'
									   AND um.dt_admissao = um.dt_promocao
									  JOIN projetos.matriz_salarial ms
									    ON ms.cd_matriz_salarial = um.cd_matriz_salarial
									   AND ms.faixa = 'B'
									 WHERE c2.cd_avaliacao_capa IS NULL
									   AND c3.cd_avaliacao_capa IS NOT NULL
									   AND 3 = (SELECT COUNT(*) 
								 			      FROM projetos.avaliacao_capa cc 
							 					 WHERE cc.cd_usuario_avaliado = c1.cd_usuario_avaliado)
									   AND c1.cd_avaliacao_capa = cp.cd_avaliacao_capa)) THEN 'Sim'
						 ELSE 'Não'
				    END AS promocao_dupla
              FROM projetos.avaliacao_capa cp
              JOIN projetos.usuarios_controledi av
                ON cp.cd_usuario_avaliado = av.codigo
              LEFT JOIN projetos.usuarios_controledi avr
                ON cp.cd_usuario_avaliador = avr.codigo
			  LEFT JOIN projetos.cargos c
			    ON c.cd_cargo = cp.cd_cargo
			  LEFT JOIN projetos.familias_cargos fc
			    ON fc.cd_familia = c.cd_familia
             WHERE dt_publicacao IS NOT NULL
			   ".(trim($args['ano']) != "" ? "AND cp.dt_periodo = ".intval($args['ano']) : "")."
			   ".(trim($args['cd_usuario_avaliado']) != "" ? "AND cp.cd_usuario_avaliado = ".intval($args['cd_usuario_avaliado']) : "")."
			   ".(trim($args['cd_usuario_avaliado_gerencia']) != "" ? "AND av.divisao = '".trim($args['cd_usuario_avaliado_gerencia'])."'" : "")."
			   ".(trim($args['tipo_promocao']) != "" ? "AND cp.tipo_promocao = '".trim($args['tipo_promocao'])."'" : "")."
			   ".(intval($args['cd_avaliacao_capa']) > 0 ? "AND cp.cd_avaliacao_capa = '".trim($args['cd_avaliacao_capa'])."'" : "")."
			   ".(trim($args['fl_promocao']) == 'S' ? "AND (0 < (SELECT COUNT(c1.*)
																	 FROM projetos.avaliacao_capa c1
																	 LEFT JOIN projetos.avaliacao_capa c2 -- VERTICAL
																	   ON c2.dt_periodo = (c1.dt_periodo - 1)
																	  AND c2.cd_usuario_avaliado = c1.cd_usuario_avaliado
																	  AND c2.tipo_promocao = 'V'
																	 LEFT JOIN projetos.avaliacao_capa c3
																	   ON c3.dt_periodo = (c1.dt_periodo - 1)
																	  AND c3.cd_usuario_avaliado = c1.cd_usuario_avaliado
																	  AND c3.tipo_promocao = 'H'
																	  AND COALESCE(c3.media_geral,0) > 70
																	 JOIN projetos.usuario_matriz um
																	   ON um.cd_usuario = c1.cd_usuario_avaliado
																	  AND um.tipo_promocao = 'H'
																	  AND um.dt_admissao = um.dt_promocao
																	 JOIN projetos.matriz_salarial ms
																	   ON ms.cd_matriz_salarial = um.cd_matriz_salarial
																	  AND ms.faixa = 'B'
																	WHERE c2.cd_avaliacao_capa IS NULL
																	  AND c3.cd_avaliacao_capa IS NOT NULL
																	  AND 3 = (SELECT COUNT(*) 
																			     FROM projetos.avaliacao_capa cc 
																			    WHERE cc.cd_usuario_avaliado = c1.cd_usuario_avaliado)
																	  AND c1.cd_avaliacao_capa = cp.cd_avaliacao_capa))" : "")."
			   ".(trim($args['fl_promocao']) == 'N' ? "AND (0 = (SELECT COUNT(c1.*)
																	 FROM projetos.avaliacao_capa c1
																	 LEFT JOIN projetos.avaliacao_capa c2 -- VERTICAL
																	   ON c2.dt_periodo = (c1.dt_periodo - 1)
																	  AND c2.cd_usuario_avaliado = c1.cd_usuario_avaliado
																	  AND c2.tipo_promocao = 'V'
																	 LEFT JOIN projetos.avaliacao_capa c3
																	   ON c3.dt_periodo = (c1.dt_periodo - 1)
																	  AND c3.cd_usuario_avaliado = c1.cd_usuario_avaliado
																	  AND c3.tipo_promocao = 'H'
																	  AND COALESCE(c3.media_geral,0) > 70
																	 JOIN projetos.usuario_matriz um
																	   ON um.cd_usuario = c1.cd_usuario_avaliado
																	  AND um.tipo_promocao = 'H'
																	  AND um.dt_admissao = um.dt_promocao
																	 JOIN projetos.matriz_salarial ms
																	   ON ms.cd_matriz_salarial = um.cd_matriz_salarial
																	  AND ms.faixa = 'B'
																	WHERE c2.cd_avaliacao_capa IS NULL
																	  AND c3.cd_avaliacao_capa IS NOT NULL
																	  AND 3 = (SELECT COUNT(*) 
																			     FROM projetos.avaliacao_capa cc 
																			    WHERE cc.cd_usuario_avaliado = c1.cd_usuario_avaliado)
																	  AND c1.cd_avaliacao_capa = cp.cd_avaliacao_capa))" : "")."
			 ORDER BY av.divisao, av.nome ASC;";
			  
		$result = $this->db->query($qr_sql);
	}
	
	function resultado( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT * 
			  FROM (SELECT 'A' AS tipo, * 
					  FROM projetos.avaliacao_nota_avaliado(".intval($args['cd_avaliacao_capa']).") 
						AS (
							grau_institucional numeric, 
							grau_escolaridade numeric, 
							grau_responsabilidade numeric, 
							grau_especific numeric, 
							grau_1 numeric, 
							grau_2 numeric, 
							nota_final numeric
						   )
                     UNION
					SELECT 'S' AS tipo, * 
                      FROM projetos.avaliacao_nota_superior(".intval($args['cd_avaliacao_capa']).") 
                        AS (
							grau_institucional numeric, 
							grau_escolaridade numeric, 
							grau_responsabilidade numeric, 
							grau_especific numeric, 
							grau_1 numeric, 
							grau_2 numeric, 
							nota_final numeric
						   )
                     UNION
                    SELECT 'C' AS tipo, * 
                      FROM projetos.avaliacao_nota_comite(".intval($args['cd_avaliacao_capa']).") 
                        AS (
							grau_institucional numeric, 
							grau_escolaridade numeric, 
							grau_responsabilidade numeric, 
							grau_especific numeric, 
							grau_1 numeric, 
							grau_2 numeric, 
							nota_final numeric
			               )) t
             WHERE t.tipo  = '".trim($args['tipo'])."'";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function expectativas( &$result, $args=array() )
	{
		$qr_sql = "
			 SELECT aa.cd_avaliacao_aspecto,
					aa.aspecto,
					aa.resultado_esperado,
					aa.acao
			   FROM projetos.avaliacao_aspecto aa
			   JOIN projetos.avaliacao a 
				 ON a.cd_avaliacao = aa.cd_avaliacao
			  WHERE a.cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."
				AND tipo                = 'S';";
		
		$result = $this->db->query($qr_sql);
	}
	
	function listar_avaliado_comite( &$result, $args=array() )
	{
		$qr_sql = "
			SELECT cp.cd_avaliacao_capa, 
			       cp.dt_periodo AS periodo, 
				   av.nome AS avaliado, 
				   av.divisao,
				   cp.tipo_promocao,
				   CASE WHEN cp.tipo_promocao = 'V' THEN 'Vertical'
						WHEN cp.tipo_promocao = 'H' THEN 'Horizontal'
				        ELSE 'Não identificado'
				   END AS tipo_promocao,
				   CASE WHEN cp.tipo_promocao = 'V' THEN 'blue'
						WHEN cp.tipo_promocao = 'H' THEN 'green'
				        ELSE 'black'
				   END AS tipo_promocao_color  
    		  FROM projetos.avaliacao_capa cp
    		  JOIN projetos.usuarios_controledi av 
			    ON av.codigo = cp.cd_usuario_avaliado
    		 WHERE EXISTS ( SELECT 1 
					          FROM projetos.avaliacao_comite ac
					         WHERE ac.cd_avaliacao_capa = cp.cd_avaliacao_capa
							   AND ac.dt_exclusao       IS NULL
					      )
				".(trim($args['ano']) != "" ? "AND cp.dt_periodo = ".intval($args['ano']) : "")."
			    ".(trim($args['cd_usuario_avaliado']) != "" ? "AND cp.cd_usuario_avaliado = ".intval($args['cd_usuario_avaliado']) : "")."
			    ".(trim($args['cd_usuario_avaliado_gerencia']) != "" ? "AND av.divisao = '".trim($args['cd_usuario_avaliado_gerencia'])."'" : "")."
			    ".(trim($args['tipo_promocao']) != "" ? "AND cp.tipo_promocao = '".trim($args['tipo_promocao'])."'" : "")."
			    ".(trim($args['fl_avaliado']) == "S" ? "AND (0 <  (SELECT COUNT(*) 
																	 FROM projetos.avaliacao 
																    WHERE cd_avaliacao_capa = cp.cd_avaliacao_capa 
																 	  AND dt_conclusao IS NOT NULL))" : "")."
				".(trim($args['fl_avaliado']) == "N" ? "AND (0 =  (SELECT COUNT(*) 
																	 FROM projetos.avaliacao 
																    WHERE cd_avaliacao_capa = cp.cd_avaliacao_capa 
																	  AND dt_conclusao IS NOT NULL))" : "")."
    		 ORDER BY cp.dt_periodo, av.nome";
			 
		$result = $this->db->query($qr_sql);
	}
	
	function avaliacao_comite( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT av.nome, 
						   (SELECT COUNT(*) 
							  FROM projetos.avaliacao 
							 WHERE cd_usuario_avaliador = c.cd_usuario_avaliador 
							   AND cd_avaliacao_capa = c.cd_avaliacao_capa 
							   AND dt_conclusao IS NOT NULL) AS ja_avaliou 
					  FROM projetos.avaliacao_comite c
					  JOIN projetos.usuarios_controledi av 
						ON av.codigo = c.cd_usuario_avaliador
					 WHERE c.cd_avaliacao_capa = ".intval($args['cd_avaliacao_capa'])."
					   AND c.dt_exclusao IS NULL
					 ORDER BY av.nome;
			      ";
			 
		$result = $this->db->query($qr_sql);
	}
	

}	
?>