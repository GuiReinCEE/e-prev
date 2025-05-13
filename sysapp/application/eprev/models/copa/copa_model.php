<?php
class copa_model extends Model
{
    function __construct()
    {
        parent::Model();
    }
    
    function listar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT t.cd_jogo,
					       t.cd_fase,
					       TO_CHAR(t.dt_jogo,'DD/MM/YYYY HH24:MI') AS dt_jogo,
						   t.ds_estadio,
						   p1.cd_grupo,
						   
						   t.cd_pais_1,
						   COALESCE(p1.bandeira,'p_30x30.png') AS bandeira_1,
						   p1.ds_pais AS ds_pais_1,
						   COALESCE(p1.sigla,p1.ds_pais) AS sigla_1,
						   t.nr_gol_pais_1,
						  					   
						   t.cd_pais_2,
						   COALESCE(p2.bandeira,'p_30x30.png') AS bandeira_2,
						   p2.ds_pais AS ds_pais_2,
						   COALESCE(p2.sigla,p2.ds_pais) AS sigla_2,
						   t.nr_gol_pais_2,
						   
                           t.nr_gol_pais_1_prorroga,
						   t.nr_gol_pais_2_prorroga,
						   t.nr_gol_pais_1_penaltis,
						   t.nr_gol_pais_2_penaltis,						   
						   
						   CASE WHEN copa.vencedor_jogo(t.cd_jogo,t.cd_usuario) = t.cd_pais_1 THEN 1
						        WHEN copa.vencedor_jogo(t.cd_jogo,t.cd_usuario) = t.cd_pais_2 THEN 2
								ELSE NULL
						   END AS nr_vencedor,
						   
						   u.nr_ponto, 
						   u.nr_ponto_extra,
						   
						   copa.resultado_jogo(t.cd_jogo) AS resultado
						   
					  FROM copa.tabela t
					  JOIN copa.pais p1
						ON p1.cd_pais    = t.cd_pais_1
					   AND p1.cd_usuario = t.cd_usuario
					  JOIN copa.pais p2
						ON p2.cd_pais    = t.cd_pais_2
					   AND p2.cd_usuario = t.cd_usuario
					  LEFT JOIN copa.usuario u
					    ON u.cd_usuario = t.cd_usuario
					   AND u.cd_jogo    = t.cd_jogo
					 WHERE t.cd_usuario = ".intval($args['cd_usuario'])."
					   AND t.cd_fase    = ".intval($args['cd_fase'])."
					 ORDER BY t.cd_jogo
					 --ORDER BY (CASE WHEN t.cd_fase = 1 THEN p1.cd_grupo ELSE NULL END), t.dt_jogo
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }

    function setResultadoTabela(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE copa.tabela
					   SET nr_gol_pais_".intval($args['nr_pais'])." = ".(trim($args['nr_gol_pais']) == "" ? "NULL" : intval($args['nr_gol_pais']))."
					 WHERE cd_usuario = 0
					   AND cd_jogo    = ".intval($args['cd_jogo']).";
					   
					UPDATE copa.usuario AS u
					   SET nr_ponto = x.nr_ponto
					  FROM (SELECT t.cd_usuario, t.cd_jogo, copa.palpite_pontos(t.cd_jogo, t.cd_usuario) AS nr_ponto
							  FROM copa.tabela t
							 WHERE t.cd_usuario > 0
							   AND t.cd_jogo    = ".intval($args['cd_jogo']).") x
					 WHERE u.cd_usuario = x.cd_usuario
					   AND u.cd_jogo    = x.cd_jogo;	

					SELECT copa.palpite_pontos_extra(t.cd_usuario)
					  FROM copa.tabela t
					 WHERE t.cd_jogo    = 64
					   AND t.cd_usuario > 0;

					UPDATE copa.usuario AS u
					   SET nr_posicao = ur.nr_posicao
					  FROM copa.usuario_resultado(".intval($args['cd_jogo']).") ur 
					 WHERE ur.cd_usuario = u.cd_usuario
					   AND ur.cd_jogo    = u.cd_jogo;					   
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }
	
    function setResultadoProrrogacao(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE copa.tabela
					   SET nr_gol_pais_".intval($args['nr_pais'])."_prorroga = ".(trim($args['nr_gol_pais']) == "" ? "NULL" : intval($args['nr_gol_pais']))."
					 WHERE cd_usuario = 0
					   AND cd_jogo    = ".intval($args['cd_jogo']).";
					   
					UPDATE copa.usuario AS u
					   SET nr_ponto = x.nr_ponto
					  FROM (SELECT t.cd_usuario, t.cd_jogo, copa.palpite_pontos(t.cd_jogo, t.cd_usuario) AS nr_ponto
							  FROM copa.tabela t
							 WHERE t.cd_usuario > 0
							   AND t.cd_jogo    = ".intval($args['cd_jogo']).") x
					 WHERE u.cd_usuario = x.cd_usuario
					   AND u.cd_jogo    = x.cd_jogo;	

					SELECT copa.palpite_pontos_extra(t.cd_usuario)
					  FROM copa.tabela t
					 WHERE t.cd_jogo    = 64
					   AND t.cd_usuario > 0;

					UPDATE copa.usuario AS u
					   SET nr_posicao = ur.nr_posicao
					  FROM copa.usuario_resultado(".intval($args['cd_jogo']).") ur 
					 WHERE ur.cd_usuario = u.cd_usuario
					   AND ur.cd_jogo    = u.cd_jogo;					   
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }	
	
    function setResultadoPenaltis(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE copa.tabela
					   SET nr_gol_pais_".intval($args['nr_pais'])."_penaltis = ".(trim($args['nr_gol_pais']) == "" ? "NULL" : intval($args['nr_gol_pais']))."
					 WHERE cd_usuario = 0
					   AND cd_jogo    = ".intval($args['cd_jogo']).";
					   
					UPDATE copa.usuario AS u
					   SET nr_ponto = x.nr_ponto
					  FROM (SELECT t.cd_usuario, t.cd_jogo, copa.palpite_pontos(t.cd_jogo, t.cd_usuario) AS nr_ponto
							  FROM copa.tabela t
							 WHERE t.cd_usuario > 0
							   AND t.cd_jogo    = ".intval($args['cd_jogo']).") x
					 WHERE u.cd_usuario = x.cd_usuario
					   AND u.cd_jogo    = x.cd_jogo;	

					SELECT copa.palpite_pontos_extra(t.cd_usuario)
					  FROM copa.tabela t
					 WHERE t.cd_jogo    = 64
					   AND t.cd_usuario > 0;	

					UPDATE copa.usuario AS u
					   SET nr_posicao = ur.nr_posicao
					  FROM copa.usuario_resultado(".intval($args['cd_jogo']).") ur 
					 WHERE ur.cd_usuario = u.cd_usuario
					   AND ur.cd_jogo    = u.cd_jogo;					   
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }	
	
    function setResultado(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE copa.tabela
					   SET nr_gol_pais_".intval($args['nr_pais'])." = ".(trim($args['nr_gol_pais']) == "" ? "NULL" : intval($args['nr_gol_pais']))."
					 WHERE cd_usuario = ".intval($args['cd_usuario'])."
					   AND cd_jogo    = ".intval($args['cd_jogo'])."
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }

    function setVencedor(&$result, $args=array())
    {
        $qr_sql = "
					UPDATE copa.tabela
					   SET nr_gol_pais_1_prorroga = ".(intval($args['cd_vencedor']) == 0 ? "NULL" : (intval($args['cd_vencedor']) == 1 ? 1 : 0)).",
					       nr_gol_pais_2_prorroga = ".(intval($args['cd_vencedor']) == 0 ? "NULL" : (intval($args['cd_vencedor']) == 2 ? 1 : 0))."
					 WHERE cd_usuario = ".intval($args['cd_usuario'])."
					   AND cd_jogo    = ".intval($args['cd_jogo'])."
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }	

    function grupo(&$result, $args=array())
    {
        $qr_sql = "
					SELECT sigla,
					       COALESCE(bandeira,'p_30x30.png') AS bandeira,
					       ds_pais, 
					       cd_grupo, 
						   nr_vitoria, 
						   nr_empate, 
						   nr_derrota, 
						   nr_gol_pro, 
						   nr_gol_contra, 
						   nr_saldo, 
						   nr_pontos, 
						   nr_rank, 
						   nr_classifica 
					  FROM copa.pais
					 WHERE cd_usuario = ".intval($args['cd_usuario'])."
					   AND cd_grupo   = '".trim($args['cd_grupo'])."'
					 ORDER BY nr_classifica ASC
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    } 

    function resultadoListar(&$result, $args=array())
    {
        $qr_sql = "
					SELECT u.cd_usuario, 
						   funcoes.get_usuario_nome(u.cd_usuario) AS nome, 
					       p.ds_pais,
                           p.sigla,
                           p.bandeira,
						   CASE WHEN COALESCE(up.cd_usuario,-1) > 0 THEN 'S' ELSE 'N' END fl_pagou,
						   (SUM(COALESCE(u.nr_ponto,0)) + SUM(COALESCE(u.nr_ponto_extra,0))) AS nr_ponto
					  FROM copa.usuario u
					  LEFT JOIN copa.usuario_pagou up
						ON up.cd_usuario = u.cd_usuario
                      LEFT JOIN copa.pais p
                        ON p.cd_usuario = u.cd_usuario
                       AND p.cd_pais    = copa.vencedor_jogo(64, p.cd_usuario)						
					 WHERE 1 = 1
					    --".(trim($args["fl_palpite"]) != "" ? "AND '".trim($args["fl_palpite"])."' = copa.palpite_verifica(u.cd_usuario)" : "")."
						".(trim($args["fl_palpite"]) != "" ? "AND u.fl_palpite = '".trim($args["fl_palpite"])."'" : "")."
					    ".(trim($args["fl_pagou"]) != "" ? "AND '".trim($args["fl_pagou"])."' = (CASE WHEN COALESCE(up.cd_usuario,-1) > 0 THEN 'S' ELSE 'N' END)" : "")."
					 GROUP BY u.cd_usuario, 
					          nome, 
					          p.ds_pais,
                              p.sigla,
                              p.bandeira,							  
							  fl_pagou
					 ORDER BY nr_ponto DESC, nome
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }	
	
    function palpiteVerifica(&$result, $args=array())
    {
        $qr_sql = "
					SELECT palpite_verifica AS fl_palpite
					  FROM copa.palpite_verifica(".$args['cd_usuario'].")
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }	
	
	function getNomeUsuario(&$result, $args=array())
    {
        $qr_sql = "
					SELECT get_usuario_nome AS nome
					  FROM funcoes.get_usuario_nome(".$args['cd_usuario'].")
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }		
	
	function getAcertouResultado(&$result, $args=array())
    {
		#array_to_string(array_agg(distinct TRIM(SUBSTRING(funcoes.get_usuario_nome(t.cd_usuario), 1, STRPOS(funcoes.get_usuario_nome(t.cd_usuario), ' '))) ),', ') AS acertadores
        $qr_sql = "
					SELECT array_to_string(array_agg(distinct funcoes.get_usuario_nome(t.cd_usuario)),', ') AS acertadores
					  FROM copa.tabela t
					  JOIN copa.usuario u
						ON u.cd_usuario = t.cd_usuario
					   AND u.cd_jogo    = t.cd_jogo
					   AND u.nr_ponto   IN (3,6)
					 WHERE t.cd_jogo = ".intval($args['cd_jogo'])."
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }	

	function getUsuarioResultado(&$result, $args=array())
    {
		#array_to_string(array_agg(distinct TRIM(SUBSTRING(funcoes.get_usuario_nome(t.cd_usuario), 1, STRPOS(funcoes.get_usuario_nome(t.cd_usuario), ' '))) ),', ') AS acertadores
        $qr_sql = "
					SELECT funcoes.get_usuario_nome(t.cd_usuario) AS nome,
					       u.nr_ponto
					  FROM copa.tabela t
					  JOIN copa.usuario u
						ON u.cd_usuario = t.cd_usuario
					   AND u.cd_jogo    = t.cd_jogo
					   AND COALESCE(u.nr_ponto,0) > 0
					 WHERE t.cd_jogo = ".intval($args['cd_jogo'])."
					 ORDER BY u.nr_ponto DESC, nome
                  ";
		#echo "<PRE>$qr_sql</PRE>";
        $result = $this->db->query($qr_sql);
    }	
}
?>