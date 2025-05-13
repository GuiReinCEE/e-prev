<?php
class Jogo_model extends Model
{
	function __construct()
	{
		parent::Model();
	}
	
	function jogo(&$result, $args=array())
	{
		$qr_sql = "
					SELECT j.cd_jogo,
						   j.ds_jogo,
						   TO_CHAR(j.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   TO_CHAR(j.dt_inicio,'DD/MM/YYYY') AS dt_inicio,
						   TO_CHAR(j.dt_inicio,'HH24:MI') AS hr_inicio,
						   TO_CHAR(j.dt_final,'DD/MM/YYYY') AS dt_final,
						   TO_CHAR(j.dt_final,'HH24:MI') AS hr_final,
						   TO_CHAR(j.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao,
						   REPLACE(cor_fundo,'#','') AS cor_fundo,
						   REPLACE(cor_pergunta,'#','') AS cor_pergunta,
						   REPLACE(cor_acerto,'#','') AS cor_acerto,
						   REPLACE(cor_acerto_mensagem,'#','') AS cor_acerto_mensagem,
						   cd_jogo_pos,
						   cd_jogo_pre,
						   fl_exibe_resultado,
						   fl_randomico,
						   qt_randomico,
						   fl_tempo_exibe,
						   fl_audio,
						   nr_margem_pergunta,
						   nr_largura_pergunta,						   
						   nr_altura_pergunta,
						   nr_tamanho_fonte_pergunta,
					       nr_tamanho_fonte_resposta,
					       nr_tamanho_fonte_acerto,
					       nr_tamanho_fonte_acerto_mensagem,						   
						   tp_jogo,
						   cd_jogo_pergunta_fixa_inicio,
						   cd_jogo_pergunta_fixa_ultima
					  FROM projetos.jogo j
					 WHERE j.cd_jogo = {cd_jogo}
		          ";
		esc("{cd_jogo}", $args["cd_jogo"], $qr_sql);

		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	

	function jogoSalvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_jogo']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE projetos.jogo
						   SET ds_jogo      = ".(trim($args['ds_jogo']) == "" ? "DEFAULT" : "'".$args['ds_jogo']."'").",
							   dt_inicio    = ".(trim($args['dt_inicio']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['dt_inicio']." ".$args['hr_inicio']."','DD/MM/YYYY HH24:MI')").", 
							   dt_final     = ".(trim($args['dt_final']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['dt_final']." ".$args['hr_final']."','DD/MM/YYYY HH24:MI')").",
							   cd_jogo_pre  = ".(intval($args['cd_jogo_pre']) == 0 ? "DEFAULT" : $args['cd_jogo_pre']).",
							   cd_jogo_pos  = ".(intval($args['cd_jogo_pos']) == 0 ? "DEFAULT" : $args['cd_jogo_pos']).",
							   cor_fundo    = ".(trim($args['cor_fundo']) == "" ? "DEFAULT" : "'#".$args['cor_fundo']."'").",
							   cor_pergunta = ".(trim($args['cor_pergunta']) == "" ? "DEFAULT" : "'#".$args['cor_pergunta']."'").",
							   cor_acerto   = ".(trim($args['cor_acerto']) == "" ? "DEFAULT" : "'#".$args['cor_acerto']."'").",
							   cor_acerto_mensagem = ".(trim($args['cor_acerto_mensagem']) == "" ? "DEFAULT" : "'#".$args['cor_acerto_mensagem']."'").",
							   fl_exibe_resultado  = ".(trim($args['fl_exibe_resultado']) == "" ? "DEFAULT" : "'".$args['fl_exibe_resultado']."'").",
							   fl_randomico        = ".(trim($args['fl_randomico']) == "" ? "DEFAULT" : "'".$args['fl_randomico']."'").",
							   tp_jogo             = ".(trim($args['tp_jogo']) == "" ? "DEFAULT" : "'".$args['tp_jogo']."'").",
							   qt_randomico        = ".(intval($args['qt_randomico']) == 0 ? "DEFAULT" : $args['qt_randomico']).",
							   fl_tempo_exibe      = ".(trim($args['fl_tempo_exibe']) == "" ? "DEFAULT" : "'".$args['fl_tempo_exibe']."'").",
							   fl_audio            = ".(trim($args['fl_audio']) == "" ? "DEFAULT" : "'".$args['fl_audio']."'").",
							   nr_tamanho_fonte_pergunta  = ".(intval($args['nr_tamanho_fonte_pergunta'])  == 0 ? "DEFAULT" : $args['nr_tamanho_fonte_pergunta']).",
							   nr_tamanho_fonte_resposta  = ".(intval($args['nr_tamanho_fonte_resposta'])  == 0 ? "DEFAULT" : $args['nr_tamanho_fonte_resposta']).",
							   nr_tamanho_fonte_acerto    = ".(intval($args['nr_tamanho_fonte_acerto'])  == 0 ? "DEFAULT" : $args['nr_tamanho_fonte_acerto']).",
							   nr_tamanho_fonte_acerto_mensagem  = ".(intval($args['nr_tamanho_fonte_acerto_mensagem'])  == 0 ? "DEFAULT" : $args['nr_tamanho_fonte_acerto_mensagem']).",
							   nr_margem_pergunta  = ".(intval($args['nr_margem_pergunta'])  == 0 ? "DEFAULT" : $args['nr_margem_pergunta']).",
							   nr_largura_pergunta = ".(intval($args['nr_largura_pergunta']) == 0 ? "DEFAULT" : $args['nr_largura_pergunta']).",
							   nr_altura_pergunta  = ".(intval($args['nr_altura_pergunta'])  == 0 ? "DEFAULT" : $args['nr_altura_pergunta'])."
						 WHERE cd_jogo = ".intval($args['cd_jogo'])."
					  ";		
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_jogo']);	
		}
		else
		{
			##INSERT
			$new_id = intval($this->db->get_new_id("projetos.jogo", "cd_jogo"));
			$qr_sql = " 
						INSERT INTO projetos.jogo
						     (
							   cd_jogo, 
							   ds_jogo, 
							   dt_inicio, 
							   dt_final, 
                               cd_usuario_inclusao,
							   cd_jogo_pre,
							   cd_jogo_pos,
							   cor_fundo,    
							   cor_pergunta,
							   cor_acerto,  
							   cor_acerto_mensagem,
							   fl_exibe_resultado,
						       fl_randomico,
						       tp_jogo,
						       qt_randomico,
							   fl_tempo_exibe,							   
							   fl_audio,
							   
                               nr_tamanho_fonte_pergunta,	
                               nr_tamanho_fonte_resposta,	
                               nr_tamanho_fonte_acerto,	
                               nr_tamanho_fonte_acerto_mensagem,	
                               nr_margem_pergunta,							   
                               nr_largura_pergunta,							   
                               nr_altura_pergunta							   
							 )
                        VALUES 
						     (
							   ".$new_id.",
							   ".(trim($args['ds_jogo']) == "" ? "DEFAULT" : "'".$args['ds_jogo']."'").",
							   ".(trim($args['dt_inicio']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['dt_inicio']." ".$args['hr_inicio']."','DD/MM/YYYY HH24:MI')").",
							   ".(trim($args['dt_final']) == "" ? "DEFAULT" : "TO_TIMESTAMP('".$args['dt_final']." ".$args['hr_final']."','DD/MM/YYYY HH24:MI')").",
							   ".(intval($args['cd_usuario']) == 0 ? "DEFAULT" : $args['cd_usuario']).",
							   ".(intval($args['cd_jogo_pre']) == 0 ? "DEFAULT" : $args['cd_jogo_pre']).",
							   ".(intval($args['cd_jogo_pos']) == 0 ? "DEFAULT" : $args['cd_jogo_pos']).",
							   ".(trim($args['cor_fundo']) == "" ? "DEFAULT" : "'#".$args['cor_fundo']."'").",
							   ".(trim($args['cor_pergunta']) == "" ? "DEFAULT" : "'#".$args['cor_pergunta']."'").",
							   ".(trim($args['cor_acerto']) == "" ? "DEFAULT" : "'#".$args['cor_acerto']."'").",
							   ".(trim($args['cor_acerto_mensagem']) == "" ? "DEFAULT" : "'#".$args['cor_acerto_mensagem']."'").",
							   ".(trim($args['fl_exibe_resultado']) == "" ? "DEFAULT" : "'".$args['fl_exibe_resultado']."'").",
							   ".(trim($args['fl_randomico']) == "" ? "DEFAULT" : "'".$args['fl_randomico']."'").",
							   ".(trim($args['tp_jogo']) == "" ? "DEFAULT" : "'".$args['tp_jogo']."'").",
							   ".(intval($args['qt_randomico']) == 0 ? "DEFAULT" : $args['qt_randomico']).",
							   ".(trim($args['fl_tempo_exibe']) == "" ? "DEFAULT" : "'".$args['fl_tempo_exibe']."'").",
							   ".(trim($args['fl_audio']) == "" ? "DEFAULT" : "'".$args['fl_audio']."'").",
							   ".(intval($args['nr_tamanho_fonte_pergunta']) == 0 ? "DEFAULT" : $args['nr_tamanho_fonte_pergunta']).",
							   ".(intval($args['nr_tamanho_fonte_resposta']) == 0 ? "DEFAULT" : $args['nr_tamanho_fonte_resposta']).",
							   ".(intval($args['nr_tamanho_fonte_acerto']) == 0 ? "DEFAULT" : $args['nr_tamanho_fonte_acerto']).",
							   ".(intval($args['nr_tamanho_fonte_acerto_mensagem']) == 0 ? "DEFAULT" : $args['nr_tamanho_fonte_acerto_mensagem']).",
							   ".(intval($args['nr_margem_pergunta']) == 0 ? "DEFAULT" : $args['nr_margem_pergunta']).",
							   ".(intval($args['nr_largura_pergunta']) == 0 ? "DEFAULT" : $args['nr_largura_pergunta']).",
							   ".(intval($args['nr_altura_pergunta']) == 0 ? "DEFAULT" : $args['nr_altura_pergunta'])."
							 );			
					  ";
			$this->db->query($qr_sql);	
			$retorno = $new_id;			
		}
		
		#echo "<pre>$qr_sql</pre>";
		#exit;
		
		return $retorno;
	}	
	
	function jogoFixaPergunta(&$result, $args=array())
	{
		if(intval($args['cd_jogo']) > 0)
		{
			$qr_sql = " 
						UPDATE projetos.jogo
						   SET cd_jogo_pergunta_fixa_inicio = ".(intval($args['cd_jogo_pergunta_fixa_inicio']) == 0 ? "DEFAULT" : $args['cd_jogo_pergunta_fixa_inicio']).",
						       cd_jogo_pergunta_fixa_ultima = ".(intval($args['cd_jogo_pergunta_fixa_ultima']) == 0 ? "DEFAULT" : $args['cd_jogo_pergunta_fixa_ultima'])."
						 WHERE cd_jogo = ".intval($args['cd_jogo'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}	
	
	function jogoExcluir(&$result, $args=array())
	{
		if(intval($args['cd_jogo']) > 0)
		{
			$qr_sql = " 
						UPDATE projetos.jogo
						   SET dt_exclusao         = CURRENT_TIMESTAMP,
						       cd_usuario_exclusao = ".$args['cd_usuario']."
						 WHERE cd_jogo = ".intval($args['cd_jogo'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}	
	
	function jogoExcluirResposta(&$result, $args=array())
	{
		if(intval($args['cd_jogo']) > 0)
		{
			$qr_sql = " 
						DELETE FROM projetos.jogo_pergunta_resposta
						 WHERE cd_jogo = ".intval($args['cd_jogo']).";
						 
						DELETE FROM projetos.jogo_resposta_tempo
						 WHERE cd_jogo = ".intval($args['cd_jogo']).";						 
					  ";			
			$this->db->query($qr_sql);
		}
	}		
	
	function pergunta(&$result, $args=array())
	{
		$qr_sql = "
					SELECT jp.cd_jogo,
					       jp.cd_jogo_pergunta,
						   jp.ds_pergunta,
						   jp.ds_complemento,
						   jp.nr_ordem,
						   TO_CHAR(jp.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   TO_CHAR(jp.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao,
						   jp.fl_exibe_resposta
					  FROM projetos.jogo_pergunta jp
					 WHERE jp.cd_jogo_pergunta = {cd_jogo_pergunta}
		          ";
		esc("{cd_jogo_pergunta}", $args["cd_jogo_pergunta"], $qr_sql);

		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function perguntaSalvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_jogo_pergunta']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE projetos.jogo_pergunta
						   SET ds_pergunta       = ".(trim($args['ds_pergunta']) == "" ? "DEFAULT" : "'".$args['ds_pergunta']."'").",
							   ds_complemento    = ".(trim($args['ds_complemento']) == "" ? "DEFAULT" : "'".$args['ds_complemento']."'").", 
							   fl_exibe_resposta = ".(trim($args['fl_exibe_resposta']) == "" ? "DEFAULT" : "'".$args['fl_exibe_resposta']."'").", 
							   nr_ordem          = ".(trim($args['nr_ordem']) == "" ? "DEFAULT" : $args['nr_ordem'])."
						 WHERE cd_jogo_pergunta = ".intval($args['cd_jogo_pergunta'])."
					  ";			
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_jogo_pergunta']);	
		}
		else
		{
			##INSERT
			$new_id = intval($this->db->get_new_id("projetos.jogo_pergunta", "cd_jogo_pergunta"));
			$qr_sql = " 
						INSERT INTO projetos.jogo_pergunta
						     (
                               cd_jogo_pergunta, 
							   cd_jogo, 
							   ds_pergunta, 
							   ds_complemento, 
							   nr_ordem, 
							   fl_exibe_resposta,
                               cd_usuario_inclusao
							 )
                        VALUES 
						     (
							   ".$new_id.",
							   ".(trim($args['cd_jogo']) == "" ? "DEFAULT" : $args['cd_jogo']).",
							   ".(trim($args['ds_pergunta']) == "" ? "DEFAULT" : "'".$args['ds_pergunta']."'").",
							   ".(trim($args['ds_complemento']) == "" ? "DEFAULT" : "'".$args['ds_complemento']."'").",
							   ".(trim($args['nr_ordem']) == "" ? "DEFAULT" : $args['nr_ordem']).",
							   ".(trim($args['fl_exibe_resposta']) == "" ? "DEFAULT" : "'".$args['fl_exibe_resposta']."'").", 
							   ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : $args['cd_usuario'])."
							 );			
					  ";
			$this->db->query($qr_sql);	
			$retorno = $new_id;			
		}
		return $retorno;
	}
	
	function perguntaExcluir(&$result, $args=array())
	{
		if(intval($args['cd_jogo_pergunta']) > 0)
		{
			$qr_sql = " 
						UPDATE projetos.jogo_pergunta
						   SET dt_exclusao         = CURRENT_TIMESTAMP,
						       cd_usuario_exclusao = ".$args['cd_usuario']."
						 WHERE cd_jogo_pergunta = ".intval($args['cd_jogo_pergunta'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}	
	
	function perguntaItem(&$result, $args=array())
	{
		$qr_sql = "
					SELECT jpi.cd_jogo_pergunta,
					       jpi.cd_jogo_pergunta_item,
						   jpi.ds_item,
						   jpi.nr_ordem,
						   jpi.fl_certo,
						   jpi.vl_resposta,
						   CASE WHEN jpi.fl_certo = 'S' THEN 'SIM' ELSE 'NÃO' END AS ds_certo,
						   TO_CHAR(jpi.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   TO_CHAR(jpi.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao
					  FROM projetos.jogo_pergunta_item jpi
					 WHERE jpi.cd_jogo_pergunta_item = {cd_jogo_pergunta_item}
		          ";
		esc("{cd_jogo_pergunta_item}", $args["cd_jogo_pergunta_item"], $qr_sql);

		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function perguntaItemSalvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_jogo_pergunta_item']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE projetos.jogo_pergunta_item
						   SET ds_item  = ".(trim($args['ds_item']) == "" ? "DEFAULT" : "'".$args['ds_item']."'").",
							   fl_certo = ".(trim($args['fl_certo']) == "" ? "DEFAULT" : "'".$args['fl_certo']."'").", 
							   nr_ordem = ".(intval($args['nr_ordem']) == 0 ? "DEFAULT" : intval($args['nr_ordem'])).",
							   vl_resposta = ".(intval($args['vl_resposta']) == 0 ? "DEFAULT" : intval($args['vl_resposta']))."
						 WHERE cd_jogo_pergunta_item = ".intval($args['cd_jogo_pergunta_item'])."
					  ";			
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_jogo_pergunta_item']);	
		}
		else
		{
			##INSERT
			$new_id = intval($this->db->get_new_id("projetos.jogo_pergunta_item", "cd_jogo_pergunta_item"));
			$qr_sql = " 
						INSERT INTO projetos.jogo_pergunta_item
						     (
                               cd_jogo_pergunta_item,
							   cd_jogo_pergunta, 
							   ds_item, 
							   fl_certo, 
							   nr_ordem,
							   vl_resposta,
                               cd_usuario_inclusao
							 )
                        VALUES 
						     (
							   ".$new_id.",
							   ".(trim($args['cd_jogo_pergunta']) == "" ? "DEFAULT" : $args['cd_jogo_pergunta']).",
							   ".(trim($args['ds_item']) == "" ? "DEFAULT" : "'".$args['ds_item']."'").",
							   ".(trim($args['fl_certo']) == "" ? "DEFAULT" : "'".$args['fl_certo']."'").",
							   ".(intval($args['nr_ordem']) == 0 ? "DEFAULT" : intval($args['nr_ordem'])).",
							   ".(intval($args['vl_resposta']) == 0 ? "DEFAULT" : intval($args['vl_resposta'])).",
							   ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : $args['cd_usuario'])."
							 );			
					  ";
			$this->db->query($qr_sql);	
			#echo "<pre>$qr_sql</pre>"; exit;
			$retorno = $new_id;			
		}
		return $retorno;
	}	
	
	function perguntaItemExcluir(&$result, $args=array())
	{
		if(intval($args['cd_jogo_pergunta_item']) > 0)
		{
			$qr_sql = " 
						UPDATE projetos.jogo_pergunta_item
						   SET dt_exclusao         = CURRENT_TIMESTAMP,
						       cd_usuario_exclusao = ".$args['cd_usuario']."
						 WHERE cd_jogo_pergunta_item = ".intval($args['cd_jogo_pergunta_item'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}	
	
	function acerto(&$result, $args=array())
	{
		$qr_sql = "
					SELECT ja.cd_jogo,
					       ja.cd_jogo_acerto,
						   ja.ds_mensagem,
						   ja.qt_inicio,
						   ja.qt_final,
						   TO_CHAR(ja.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   TO_CHAR(ja.dt_exclusao,'DD/MM/YYYY HH24:MI') AS dt_exclusao
					  FROM projetos.jogo_acerto ja
					 WHERE ja.cd_jogo_acerto = {cd_jogo_acerto}
		          ";
		esc("{cd_jogo_acerto}", $args["cd_jogo_acerto"], $qr_sql);

		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function acertoSalvar(&$result, $args=array())
	{
		$retorno = 0;
		
		if(intval($args['cd_jogo_acerto']) > 0)
		{
			##UPDATE
			$qr_sql = " 
						UPDATE projetos.jogo_acerto
						   SET ds_mensagem = ".(trim($args['ds_mensagem']) == "" ? "DEFAULT" : "'".$args['ds_mensagem']."'").",
							   qt_inicio   = ".(trim($args['qt_inicio']) == "" ? "DEFAULT" : $args['qt_inicio']).", 
							   qt_final    = ".(trim($args['qt_final']) == "" ? "DEFAULT" : $args['qt_final'])." 
						 WHERE cd_jogo_acerto = ".intval($args['cd_jogo_acerto'])."
					  ";			
			$this->db->query($qr_sql);
			$retorno = intval($args['cd_jogo_acerto']);	
		}
		else
		{
			##INSERT
			$new_id = intval($this->db->get_new_id("projetos.jogo_acerto", "cd_jogo_acerto"));
			$qr_sql = " 
						INSERT INTO projetos.jogo_acerto
						     (
                               cd_jogo_acerto,
							   cd_jogo, 
							   ds_mensagem, 
							   qt_inicio, 
							   qt_final, 
                               cd_usuario_inclusao
							 )
                        VALUES 
						     (
							   ".$new_id.",
							   ".(trim($args['cd_jogo']) == "" ? "DEFAULT" : $args['cd_jogo']).",
							   ".(trim($args['ds_mensagem']) == "" ? "DEFAULT" : "'".$args['ds_mensagem']."'").",
							   ".(trim($args['qt_inicio']) == "" ? "DEFAULT" : $args['qt_inicio']).",
							   ".(trim($args['qt_final']) == "" ? "DEFAULT" : $args['qt_final']).",
							   ".(trim($args['cd_usuario']) == "" ? "DEFAULT" : $args['cd_usuario'])."
							 );			
					  ";
			$this->db->query($qr_sql);	
			#echo "<pre>$qr_sql</pre>"; exit;
			$retorno = $new_id;			
		}
		return $retorno;
	}	
	
	function acertoExcluir(&$result, $args=array())
	{
		if(intval($args['cd_jogo_acerto']) > 0)
		{
			$qr_sql = " 
						UPDATE projetos.jogo_acerto
						   SET dt_exclusao         = CURRENT_TIMESTAMP,
						       cd_usuario_exclusao = ".$args['cd_usuario']."
						 WHERE cd_jogo_acerto = ".intval($args['cd_jogo_acerto'])."
					  ";			
			$this->db->query($qr_sql);
		}
	}	
	
	function acertoListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT ja.cd_jogo_acerto,
						   ja.cd_jogo,
						   ja.qt_inicio,
						   ja.qt_final,
						   ja.ds_mensagem
					  FROM projetos.jogo_acerto ja
					 WHERE ja.cd_jogo = {cd_jogo}
					   AND ja.dt_exclusao IS NULL
					 ORDER BY ja.qt_inicio ASC
		          ";
		esc("{cd_jogo}", $args["cd_jogo"], $qr_sql);

		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function perguntaListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT jp.cd_jogo_pergunta,
						   jp.ds_pergunta,
						   jp.ds_complemento,
						   jp.nr_ordem,
						   jp.fl_exibe_resposta
					  FROM projetos.jogo_pergunta jp
					 WHERE jp.cd_jogo = {cd_jogo}
					   AND jp.dt_exclusao IS NULL
					 ORDER BY jp.nr_ordem ASC
		          ";
		esc("{cd_jogo}", $args["cd_jogo"], $qr_sql);

		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function perguntaListarResultado(&$result, $args=array())
	{
		$qr_sql = "
					SELECT jp.cd_jogo_pergunta,
					       jp.ds_pergunta,
					       jp.ds_complemento,
					       jp.nr_ordem,
					       COUNT(jpr.cd_jogo_pergunta_resposta) AS qt_resposta   
					  FROM projetos.jogo_pergunta jp
                      JOIN projetos.jogo_pergunta_item jpi
					    ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
					   AND jpi.dt_exclusao IS NULL
				      LEFT JOIN projetos.jogo_pergunta_resposta jpr
					    ON jpr.cd_jogo_pergunta_item = jpi.cd_jogo_pergunta_item
				       AND jpr.cd_jogo              = jp.cd_jogo
					 WHERE jp.cd_jogo = ".intval($args["cd_jogo"])."
					   AND jp.dt_exclusao IS NULL
                     GROUP BY  jp.cd_jogo_pergunta,
						   jp.ds_pergunta,
						   jp.ds_complemento,
						   jp.nr_ordem
					 ORDER BY jp.nr_ordem ASC
		          ";

		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function perguntaItemListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT jpi.cd_jogo_pergunta,
					       jpi.cd_jogo_pergunta_item,
					       jpi.ds_item,
					       jpi.nr_ordem,
					       jpi.fl_certo,
						   jpi.vl_resposta
					  FROM projetos.jogo_pergunta_item jpi
					 WHERE jpi.cd_jogo_pergunta = {cd_jogo_pergunta}
					   AND jpi.dt_exclusao IS NULL
					 ORDER BY jpi.nr_ordem ASC
		          ";
		esc("{cd_jogo_pergunta}", $args["cd_jogo_pergunta"], $qr_sql);

		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function perguntaItemListarResultado(&$result, $args=array())
	{
		$qr_sql = "
					SELECT jpi.cd_jogo_pergunta,
					       jpi.cd_jogo_pergunta_item,
					       jpi.ds_item,
					       jpi.nr_ordem,
					       jpi.fl_certo,
					       COUNT(jpr.*) AS qt_resposta
					  FROM projetos.jogo_pergunta_item jpi
					  LEFT JOIN projetos.jogo_pergunta_resposta jpr
					    ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
					 WHERE jpi.cd_jogo_pergunta = ".intval($args["cd_jogo_pergunta"])."
					   AND jpi.dt_exclusao IS NULL
                     GROUP BY jpi.cd_jogo_pergunta,
					       jpi.cd_jogo_pergunta_item,
					       jpi.ds_item,
					       jpi.nr_ordem,
					       jpi.fl_certo
					 ORDER BY jpi.nr_ordem ASC
		          ";

		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function jogoListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT j.cd_jogo,
						   j.ds_jogo,
						   TO_CHAR(j.dt_inclusao,'DD/MM/YYYY HH24:MI') AS dt_inclusao,
						   TO_CHAR(j.dt_inicio,'DD/MM/YYYY HH24:MI') AS dt_inicio,
						   TO_CHAR(j.dt_final,'DD/MM/YYYY HH24:MI') AS dt_final,
						   uc.nome AS ds_usuario
					  FROM projetos.jogo j
					  JOIN projetos.usuarios_controledi uc
					    ON uc.codigo = j.cd_usuario_inclusao
					 WHERE j.dt_exclusao IS NULL
						".(((trim($args["dt_inclusao_ini"]) != "") and (trim($args["dt_inclusao_fim"]) != "")) ? "AND CAST(j.dt_inclusao AS DATE) BETWEEN TO_DATE('".trim($args["dt_inclusao_ini"])."','DD/MM/YYYY') AND TO_DATE('".trim($args["dt_inclusao_fim"])."','DD/MM/YYYY')" : "")."
					 ORDER BY j.dt_inclusao DESC
		          ";
		// echo "<pre>$qr_sql</pre>";
		$result = $this->db->query($qr_sql);
	}	
	
	function resultado(&$result, $args=array())
	{
		$qr_sql = "
				SELECT COALESCE(p.nome,COALESCE(UPPER(uc.nome),'SEM IDENTIFICAÇÃO')) AS nome_jogador, 
				       COALESCE(p.cd_empresa,0) AS cd_empresa, 
					   COALESCE(p.cd_registro_empregado,0) AS cd_registro_empregado,
					   COALESCE(p.seq_dependencia,0) AS seq_dependencia, 
					   EXTRACT(years FROM AGE(CURRENT_DATE - (jpr.dt_inclusao - p.dt_nascimento))) AS idade,
					   p.sexo,
					   j.tp_jogo,
					   TO_CHAR(jpr.dt_inclusao, 'DD/MM/YYYY HH24:MI:SS') AS dt_jogo,
					   (SELECT TO_CHAR((jrt.dt_fim_jogo - jrt.dt_ini_jogo), 'HH24:MI:SS')
					      FROM projetos.jogo_resposta_tempo jrt
					     WHERE jrt.cd_chave = jpr.cd_chave
					     LIMIT 1) AS hr_tempo,
					   SUM(CASE WHEN j.tp_jogo = 'A' THEN (CASE WHEN jpi.fl_certo = 'S' THEN 1 ELSE 0 END) 
						        WHEN j.tp_jogo = 'V' THEN jpi.vl_resposta
						        ELSE 0 END) AS qt_acerto
				  FROM projetos.jogo_pergunta jp
				  JOIN projetos.jogo j
					ON j.cd_jogo = jp.cd_jogo
				   AND j.dt_exclusao IS NULL 				  
				  JOIN projetos.jogo_pergunta_item jpi
					ON jpi.cd_jogo_pergunta = jp.cd_jogo_pergunta
				  JOIN projetos.jogo_pergunta_resposta jpr
					ON jpr.cd_jogo_pergunta_item     = jpi.cd_jogo_pergunta_item
				   AND jpr.cd_jogo                   = jp.cd_jogo
				  LEFT JOIN public.participantes p
					ON funcoes.cripto_re(cd_empresa, cd_registro_empregado, seq_dependencia) = jpr.cd_chave
                  LEFT JOIN projetos.usuarios_controledi uc 
                    ON funcoes.cripto_re(COALESCE(uc.cd_patrocinadora,99), COALESCE(uc.cd_registro_empregado, codigo), 0) = jpr.cd_chave
				 WHERE MD5(CAST(jp.cd_jogo AS TEXT)) = MD5('".intval($args["cd_jogo"])."')
					".(((trim($args["dt_jogo_ini"]) != "") and (trim($args["dt_jogo_fim"]) != "")) ? " AND CAST(jpr.dt_inclusao AS DATE) BETWEEN TO_DATE('".$args["dt_jogo_ini"]."','DD/MM/YYYY') AND TO_DATE('".$args["dt_jogo_fim"]."','DD/MM/YYYY')" : "")."
					".(trim($args["sexo"]) != "" ? "AND p.sexo = '".trim($args["sexo"])."'" : "")."	

					".(intval($args["idade"]) == 1 ? "AND EXTRACT(years FROM AGE(CURRENT_DATE - (jpr.dt_inclusao - p.dt_nascimento))) BETWEEN 0 AND 20" : "")."	
					".(intval($args["idade"]) == 2 ? "AND EXTRACT(years FROM AGE(CURRENT_DATE - (jpr.dt_inclusao - p.dt_nascimento))) BETWEEN 21 AND 30" : "")."	
					".(intval($args["idade"]) == 3 ? "AND EXTRACT(years FROM AGE(CURRENT_DATE - (jpr.dt_inclusao - p.dt_nascimento))) BETWEEN 31 AND 40" : "")."	
					".(intval($args["idade"]) == 4 ? "AND EXTRACT(years FROM AGE(CURRENT_DATE - (jpr.dt_inclusao - p.dt_nascimento))) BETWEEN 41 AND 50" : "")."	
					".(intval($args["idade"]) == 5 ? "AND EXTRACT(years FROM AGE(CURRENT_DATE - (jpr.dt_inclusao - p.dt_nascimento))) BETWEEN 51 AND 60" : "")."	
					".(intval($args["idade"]) == 6 ? "AND EXTRACT(years FROM AGE(CURRENT_DATE - (jpr.dt_inclusao - p.dt_nascimento))) > 60" : "")."	
					
				 GROUP BY nome_jogador,
						  p.cd_empresa, 
					      p.cd_registro_empregado, 
					      p.seq_dependencia,
                          idade,
						  p.sexo,
						  j.tp_jogo,
						  dt_jogo,
						  hr_tempo
					".(intval($args["qt_acerto"]) > 0 ? "HAVING SUM(CASE WHEN jpi.fl_certo = 'S' THEN 1 ELSE 0 END) = ".intval($args["qt_acerto"]) : "")."	  
				 ORDER BY dt_jogo ASC,
						  nome_jogador ASC		
                  ";		

		#echo "<pre style='text-align:left;'>$qr_sql</pre>";EXIT;
		$result = $this->db->query($qr_sql);
	}
	
	function jogoCombo( &$result, $args=array() )
	{
		$qr_sql = "
					SELECT cd_jogo AS value, 
					       ds_jogo AS text 
					  FROM projetos.jogo 
					 WHERE dt_exclusao IS NULL
					 ORDER BY ds_jogo
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	
	function resumoListar(&$result, $args=array())
	{
		$qr_sql = "
					SELECT CASE WHEN tipo_participante = 'APOS' THEN 'APOSENTADO'
								WHEN tipo_participante = 'PENS' THEN 'PENSIONISTA'
								WHEN tipo_participante = 'ATIV' THEN 'ATIVO'
								WHEN tipo_participante = 'CTP' THEN 'CTP'
								WHEN tipo_participante = 'EXAU' THEN 'EX-AUTARQUICO'
								WHEN tipo_participante = 'AUXD' THEN 'AUXILIO DOENCA'
								WHEN tipo_participante = 'SEMP' THEN 'SEM PLANO'
								ELSE 'Não Identificado'
					       END AS tipo_participante, 
					       sexo,
						   idade_faixa, 
						   renda_faixa, 
						   cidade_faixa
                      FROM projetos.jogo_resposta_tracker
					 WHERE 1 = 1
					 ".(intval($args['cd_jogo']) > 0 ? "AND cd_jogo = ".intval($args['cd_jogo']) : "")."					  
					 ".(trim($args['cd_tipo_participante']) != "" ? "AND tipo_participante = '".trim($args['cd_tipo_participante']) ."'" : "")."					  
					 ".(trim($args['cd_sexo']) != "" ? "AND sexo = '".trim($args['cd_sexo']) ."'" : "")."					  
					 ".(trim($args['cd_idade']) != "" ? "AND idade_faixa = '".trim($args['cd_idade']) ."'" : "")."					  
					 ".(trim($args['cd_renda']) != "" ? "AND renda_faixa = '".trim($args['cd_renda']) ."'" : "")."					  
					 ".(trim($args['cd_cidade']) != "" ? "AND cidade_faixa = '".trim($args['cd_cidade']) ."'" : "")."					  
					 ORDER BY tipo_participante
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function comboTipoParticipante(&$result, $args=array())
	{
		$qr_sql = "
					SELECT DISTINCT tipo_participante AS value,
					       CASE WHEN tipo_participante = 'APOS' THEN 'APOSENTADO'
								WHEN tipo_participante = 'PENS' THEN 'PENSIONISTA'
								WHEN tipo_participante = 'ATIV' THEN 'ATIVO'
								WHEN tipo_participante = 'CTP' THEN 'CTP'
								WHEN tipo_participante = 'EXAU' THEN 'EX-AUTARQUICO'
								WHEN tipo_participante = 'AUXD' THEN 'AUXILIO DOENCA'
								WHEN tipo_participante = 'SEMP' THEN 'SEM PLANO'
								ELSE 'Não Identificado'
					       END AS text
                      FROM projetos.jogo_resposta_tracker
					 ORDER BY text
		          ";
		$result = $this->db->query($qr_sql);
	}	

	function comboIdade(&$result, $args=array())
	{
		$qr_sql = "
					SELECT DISTINCT idade_faixa AS value,
					       idade_faixa AS text
                      FROM projetos.jogo_resposta_tracker
					 ORDER BY text
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function comboRenda(&$result, $args=array())
	{
		$qr_sql = "
					SELECT DISTINCT renda_faixa AS value,
					       renda_faixa AS text
                      FROM projetos.jogo_resposta_tracker
					 ORDER BY text
		          ";
		$result = $this->db->query($qr_sql);
	}	

	function comboCidade(&$result, $args=array())
	{
		$qr_sql = "
					SELECT DISTINCT cidade_faixa AS value,
					       cidade_faixa AS text
                      FROM projetos.jogo_resposta_tracker
					 ORDER BY text
		          ";
		$result = $this->db->query($qr_sql);
	}	
	
	function comboJogo(&$result, $args=array())
	{
		$qr_sql = "
					SELECT cd_jogo AS value,
					       ds_jogo AS text
                      FROM projetos.jogo
					 WHERE dt_exclusao IS NULL
					 ORDER BY text
		          ";
		$result = $this->db->query($qr_sql);
	}	

}
?>