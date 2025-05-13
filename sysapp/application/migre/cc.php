<?php
	include_once('inc/sessao.php');
	
	#### REDIRECIONA PARA NOVA TELA (23/12/2015) ####
	echo '<META HTTP-EQUIV="Refresh" CONTENT="0;URL='.base_url()."index.php/servico/contracheque".'">';
	exit;
	
	
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');

	session_start();
	
	$referer = $_SERVER["HTTP_REFERER"];
	
	if ($_SESSION["CCLogin"] == "VALID" ) 
	{
		$_SESSION["CCLogin"] = null;
	}
	else
	{
		if (!strpos( $referer , "/cc.php" ))
		{
			header("location: cc_login.php");
		}
	}

	$tpl = new TemplatePower('tpl/tpl_cc'.(trim($_POST['cc_imprimir']) == "S"? '_impressao' : '').'.html');
	
	$tpl->prepare();
	$PROG = str_replace('/u/www/controle_projetos/', '', __FILE__);
	include_once('inc/skin.php');
	$tpl->assign('usuario', $N);
	$tpl->assign('divsao', $D);
	
	$qr_sql = "
				SELECT COALESCE(cd_registro_empregado,0) AS cd_registro_empregado,
				       indic_04 
				  FROM projetos.usuarios_controledi 
				 WHERE codigo = ".$_SESSION['Z']."
		      ";
    $ob_resul = pg_query($db, $qr_sql);
	$ar_reg = pg_fetch_array($ob_resul);
   
	$_POST['cc_registro_empregado'] = $ar_reg['cd_registro_empregado'];
	$INDIC_04 = $ar_reg['indic_04'];


	
	
	if ($INDIC_04 == '*')
	{
		if(trim($_POST['rh_registro_empregado']) != "")
		{
			$_POST['cc_registro_empregado'] = $_POST['rh_registro_empregado'];
		}      
		
		$tpl->newBlock('blk_consulta_re');
		$tpl->assign('rh_registro_empregado',$_POST['cc_registro_empregado']);
	}
	$tpl->assignGlobal('cc_registro_empregado', $_POST['cc_registro_empregado']);  


	$qr_sql = "
				SELECT DISTINCT (TO_CHAR(c.dt_pgto, 'YYYY-MM')) AS cd_mes_ano,
				       TO_CHAR(c.dt_pgto, 'MM/YYYY') AS ds_mes_ano,
					   TO_CHAR(c.dt_pgto, 'YYYY-MM-DD') AS dt_pagamento,
				       c.dt_pgto
				  FROM projetos.contracheque c, 
				       public.participantes p
				 WHERE p.cd_registro_empregado = c.cd_registro_empregado
				   AND p.cd_empresa            = 9
				   AND p.seq_dependencia       = 0
				   AND c.cd_empresa            = 1
				   AND c.cd_registro_empregado = ".$_POST['cc_registro_empregado']."
				   AND c.tipo                  <> 'C'
				   ".($INDIC_04 == '*' ? '' : 'AND dt_liberacao IS NOT NULL' )." 	
				 ORDER BY c.dt_pgto DESC
              ";
    $ob_resul = pg_query($db, $qr_sql);
	$nr_conta = 0;
	while($ar_reg = pg_fetch_array($ob_resul))
	{
		if(($nr_conta == 0 ) and (trim($_POST['cc_mes_ano']) == ""))
		{
			$_POST['cc_mes_ano'] = $ar_reg['dt_pagamento'];
		}
		
		$tpl->newBlock('lista_mes_ano');
		$tpl->assign('cd_mes_ano',$ar_reg['dt_pagamento']);	
		$tpl->assign('ds_mes_ano',$ar_reg['ds_mes_ano']);	
		$tpl->assign('ck_mes_ano',($_POST['cc_mes_ano'] == $ar_reg['dt_pagamento'] ? 'selected' : ''));
	}

	
	$qr_sql  = " 
				SELECT c.*,
                       CASE WHEN tipo = 'P' THEN 'blue'
					        WHEN tipo = 'D' THEN 'red'
							ELSE ''
					   END AS tp_cor,
					   p.nome,                                                      
					   p.logradouro,                                                
					   p.bairro,                                                    
					   p.cidade, p.cep, p.complemento_cep,                          
					   p.unidade_federativa,                                        
					   to_char(c.dt_pgto, 'DD/MM/YYYY') as data_pagamento,          
					   c.dt_pgto, 											         
					   p.cd_empresa as empresa,                                    
					   e.nome_empresa,                                             
					   to_char(c.dt_pgto, 'MM')   as mes,                          
					   to_char(c.dt_pgto, 'YYYY') as ano                           
				  FROM projetos.contracheque c,                                     
					   participantes p,                                            
					   patrocinadoras e                                            
				 WHERE p.cd_registro_empregado = c.cd_registro_empregado           
				   AND e.cd_empresa            = p.cd_empresa                                 
				   AND p.cd_empresa            = 9                                            
				   AND p.seq_dependencia       = 0                                        
				   AND c.cd_empresa            = 1                                            
				   AND c.cd_registro_empregado = ".$_POST['cc_registro_empregado']."
				   AND c.tipo                  <> 'C' 
                   ".($INDIC_04 == '*' ? '' : 'AND c.dt_liberacao IS NOT NULL' )." 
				   AND DATE_TRUNC('day', c.dt_pgto) = TO_DATE('".$_POST['cc_mes_ano']."','YYYY-MM-DD')
                 ORDER BY c.tipo DESC, 
				          c.codigo ASC                                                      
			  ";
	$ob_resul  = pg_query($db, $qr_sql);
	
	//echo "<!-- ".$qr_sql." -->";
	
	if (pg_num_rows($ob_resul) > 0) 
	{
		$reg = pg_fetch_array($ob_resul);
		$tpl->newBlock('blk_conteudo_cc');
		$tpl->assign('empresa', $reg['empresa'] . " - " . $reg['nome_empresa']);
		$tpl->assign('nome',$reg['nome']);
		$tpl->assign('dt_pagamento',$reg['data_pagamento']);
		$tpl->assign('dp',$reg['data_pagamento']);
		$tpl->assign('mes_ano', $reg['mes'].'/'.$reg['ano']);
		$tpl->assign('desc_folha',$reg['descricao_folha']);
		$tpl->assign('banco',$reg['banco']);
		$tpl->assign('agencia',$reg['agencia']);
		$tpl->assign('conta',$reg['conta']);
		$tpl->assign('endereco',$reg['logradouro']);
		$tpl->assign('bairro',$reg['bairro']);
		$tpl->assign('cidade',$reg['cidade']);
		$tpl->assign('uf',$reg['unidade_federativa']);
		$tpl->assign('cep',$reg['cep']);
		$tpl->assign('complemento_cep',$reg['complemento_cep']);
		$tpl->assign('r',$re_consulta);	
		$tpl->assign('cc_imprimir',(trim($_POST['cc_imprimir']) == "S"? 'S' : 'N'));	
					
					
					
					
		$valor = number_format($reg['valor'],2,",",".");
		$tpl->newBlock("contra_cheque"); 
		if($reg['tp_cor'] != "")
		{
			$tpl->assign('tp_cor'    , "color:".$reg['tp_cor'].";");
		}
		$tpl->assign('codigo'    , $reg['codigo']);
		$tpl->assign('verba'     , $reg['verba']);
		$tpl->assign('descricao' , $reg['descricao']);
		$tpl->assign('ref'       , number_format($reg['referencia'],2,",","."));
		$tpl->assign('valor'     , $valor);
		$tpl->assign('tipo'      , $reg['tipo']);		
   		
		while ($reg = pg_fetch_array($ob_resul))
		{
			$valor = number_format($reg['valor'],2,",",".");
			if ($reg['codigo'] <> '') 
			{
		  		if ($reg['tipo'] != 'B') 
				{
					$tpl->newBlock("contra_cheque"); 
					if($reg['tp_cor'] != "")
					{
						$tpl->assign('tp_cor'    , "color:".$reg['tp_cor'].";");
					}					
					$tpl->assign('codigo'    , $reg['codigo']);
					$tpl->assign('verba'     , $reg['verba']);
					$tpl->assign('descricao' , $reg['descricao']);
					$tpl->assign('ref'       , number_format($reg['referencia'],2,",","."));
					$tpl->assign('valor'     , $valor);
					$tpl->assign('tipo'      , $reg['tipo']);
				}
			}
			else 
			{
				$tpl->gotoBlock('blk_conteudo_cc');
				switch (trim($reg['descricao'])) 
				{
					case 'Salario Base':
						 $tpl->assign('sal_base', $valor);
						 break;
					case 'Base INSS':
						 $tpl->assign('contrib_inss', $valor);
						 break;
					case 'Base IRRF':
						 $tpl->assign('base_irrf', $valor);
						 break;
					case 'Base FGTS':
						 $tpl->assign('base_fgts', $valor);
						 break;
					case 'FGTS Mês':
						 $tpl->assign('fgts_mes', $valor);
						 break;
					case 'Rendimentos':
						 $tpl->assign('rendimentos', $valor);
						 break;
					case 'Descontos':
						 $tpl->assign('descontos', $valor);
						 break;
					case 'Líquido':
						 $tpl->assign('liquido', $valor);
						 break;
				}
			}
		}
		
	}
	else 
	{
		$tpl->newBlock('blk_mensagem');
	}      

	$tpl->printToScreen();
?>