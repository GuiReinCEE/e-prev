<?
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	$tpl = new TemplatePower('tpl/tpl_svn_estatistica.html');
	$tpl->assignInclude('mn_sup', 'menu/menu_projetos.htm');
   
	$tpl->prepare();
	$tpl->assign('n', $n);
	
	header( 'location:'.base_url().'index.php/servico/svn_estatistica/index/'.$_REQUEST['tipo']);
   
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);	
	$tpl->assign('tipo', $_REQUEST['tipo']);	
	
	
	#### MES ####
	$qr_select = " 
					SELECT TO_CHAR(dt_revisao,'MM/YYYY') AS dt_mes,
						   TO_CHAR(dt_revisao,'YYYY-MM') AS dt_mes_ingles,
					       SUM(nr_tamanho) AS nr_tamanho,
					       ROUND((SUM(nr_tamanho) / (SELECT SUM(nr_tamanho)
					                                   FROM svn.revisoes
					                                  WHERE dt_revisao = (SELECT MIN(dt_revisao)
					                                                        FROM svn.revisoes
																		   WHERE ds_repositorio = '".$_REQUEST['tipo']."')
														AND ds_repositorio = '".$_REQUEST['tipo']."') * 100),2) AS pr_crescimento
					  FROM svn.revisoes
					 WHERE dt_revisao > (SELECT MIN(dt_revisao)
					                       FROM svn.revisoes
										  WHERE ds_repositorio = '".$_REQUEST['tipo']."')
					   AND ds_repositorio = '".$_REQUEST['tipo']."'
					 GROUP BY dt_mes, 
					          dt_mes_ingles
					 ORDER BY dt_mes_ingles				 
				 ";
	$ob_resul = pg_query($db, $qr_select);
	$nr_conta = 0;
	while($ob_reg = pg_fetch_object($ob_resul))
	{
		$tpl->newBlock('BCK_tamanho_diario');
		if(($nr_conta % 2) != 0)
		{
			$tpl->assign('bg_color', '#F4F4F4');
		}			
		$tpl->assign('dt_mes',         $ob_reg->dt_mes);	
		$tpl->assign('nr_tamanho',     number_format($ob_reg->nr_tamanho,2, ',', '.'));
		$tpl->assign('pr_crescimento', number_format($ob_reg->pr_crescimento,2, ',', '.'));
		$nr_conta++;
	}	
	
	#### INICIO ####
	$qr_select = " 
					SELECT SUM(nr_tamanho) AS nr_tamanho
					  FROM svn.revisoes
					 WHERE dt_revisao = (SELECT MIN(dt_revisao)
										   FROM svn.revisoes
										  WHERE ds_repositorio = '".$_REQUEST['tipo']."')	
					   AND ds_repositorio = '".$_REQUEST['tipo']."'										   
				 ";
	$ob_resul = pg_query($db, $qr_select);	
	$ob_reg   = pg_fetch_object($ob_resul);
	$tpl->newBlock('BCK_tamanho_inicial');
	$tpl->assign('nr_tamanho', number_format($ob_reg->nr_tamanho,2, ',', '.'));
	$nr_tamanho_inicial = $ob_reg->nr_tamanho;
	
	#### TOTAL ####
	$qr_select = " 
					SELECT SUM(nr_tamanho) AS nr_tamanho
	                  FROM svn.revisoes	
                     WHERE dt_revisao < DATE_TRUNC('month', CURRENT_DATE)
                       AND ds_repositorio = '".$_REQUEST['tipo']."'					 
				 ";
	$ob_resul = pg_query($db, $qr_select);	
	$ob_reg   = pg_fetch_object($ob_resul);
	$tpl->newBlock('BCK_tamanho_total');
	$tpl->assign('nr_tamanho', number_format($ob_reg->nr_tamanho,2, ',', '.'));
	$nr_tamanho_atual = $ob_reg->nr_tamanho;
	
	#### MEDIA ####
	$qr_select = " 
					SELECT ROUND(AVG(d.nr_tamanho),2) AS nr_tamanho,
					       ROUND(AVG(d.pr_crescimento),2) AS pr_crescimento
					FROM (SELECT TO_CHAR(dt_revisao,'MM/YYYY') AS dt_mes,
							     SUM(nr_tamanho) AS nr_tamanho,
							     ROUND((SUM(nr_tamanho) / (SELECT SUM(nr_tamanho)
														     FROM svn.revisoes
														    WHERE dt_revisao = (SELECT MIN(dt_revisao)
																				  FROM svn.revisoes
																				 WHERE ds_repositorio = '".$_REQUEST['tipo']."')
															  AND ds_repositorio = '".$_REQUEST['tipo']."') * 100),2) AS pr_crescimento
						    FROM svn.revisoes
						   WHERE dt_revisao > (SELECT MIN(dt_revisao)
											     FROM svn.revisoes
											    WHERE ds_repositorio = '".$_REQUEST['tipo']."')
							 AND dt_revisao < DATE_TRUNC('month', CURRENT_DATE)
							 AND ds_repositorio = '".$_REQUEST['tipo']."'
						   GROUP BY TO_CHAR(dt_revisao,'MM/YYYY')) AS d			 
				 ";
	$ob_resul = pg_query($db, $qr_select);	
	$ob_reg   = pg_fetch_object($ob_resul);
	$tpl->newBlock('BCK_tamanho_media');
	$tpl->assign('nr_tamanho',     number_format($ob_reg->nr_tamanho,2, ',', '.'));
	$tpl->assign('pr_crescimento', number_format($ob_reg->pr_crescimento,2, ',', '.'));
	$nr_media = $ob_reg->pr_crescimento;	
/*	
	#### 1 ANO ####
	$tpl->newBlock('BCK_projecao');
	$tpl->assign('dt_ano', '1 Ano');
	$tpl->assign('nr_mb', number_format($nr_tamanho_inicial + ((($nr_media * $nr_tamanho_inicial)/100) * 12),2, ',', '.'));
	$tpl->assign('nr_gb', number_format(($nr_tamanho_inicial + ((($nr_media * $nr_tamanho_inicial)/100) * 12))/1024,2, ',', '.'));
*/
	#### 5 ANOS ####
	$tpl->newBlock('BCK_projecao');
	$tpl->assign('bg_color', '#F4F4F4');
	$tpl->assign('dt_ano', '5 Anos');
	$tpl->assign('nr_mb', number_format($nr_tamanho_inicial + ((($nr_media * $nr_tamanho_inicial)/100) * (12 * 5)),2, ',', '.'));
	$tpl->assign('nr_gb', number_format(($nr_tamanho_inicial + ((($nr_media * $nr_tamanho_inicial)/100) * (12 * 5)))/1024,2, ',', '.'));

	#### 10 ANOS ####
	$tpl->newBlock('BCK_projecao');
	$tpl->assign('dt_ano', '10 Anos');
	$tpl->assign('nr_mb', number_format($nr_tamanho_inicial + ((($nr_media * $nr_tamanho_inicial)/100) * (12 * 10)),2, ',', '.'));
	$tpl->assign('nr_gb', number_format(($nr_tamanho_inicial + ((($nr_media * $nr_tamanho_inicial)/100) * (12 * 10)))/1024,2, ',', '.'));

	#### 15 ANOS ####
	$tpl->newBlock('BCK_projecao');
	$tpl->assign('bg_color', '#F4F4F4');
	$tpl->assign('dt_ano', '15 Anos');
	$tpl->assign('nr_mb', number_format($nr_tamanho_inicial + ((($nr_media * $nr_tamanho_inicial)/100) * (12 * 15)),2, ',', '.'));
	$tpl->assign('nr_gb', number_format(($nr_tamanho_inicial + ((($nr_media * $nr_tamanho_inicial)/100) * (12 * 15)))/1024,2, ',', '.'));	
	
	#### 20 ANOS ####
	$tpl->newBlock('BCK_projecao');
	$tpl->assign('dt_ano', '20 Anos');
	$tpl->assign('nr_mb', number_format($nr_tamanho_inicial + ((($nr_media * $nr_tamanho_inicial)/100) * (12 * 20)),2, ',', '.'));
	$tpl->assign('nr_gb', number_format(($nr_tamanho_inicial + ((($nr_media * $nr_tamanho_inicial)/100) * (12 * 20)))/1024,2, ',', '.'));
	
	$tpl->printToScreen();

?>