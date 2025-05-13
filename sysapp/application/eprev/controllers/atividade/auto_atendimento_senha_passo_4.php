<?php
	include_once('inc/sessao_senha.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	
	$qr_sql = "
				INSERT INTO public.log_acessos_usuario 
					 (
					   sid,
					   hora,
					   pagina
					 )
				VALUES
					 (
					   ".$_SESSION['SID'].", 
					   CURRENT_TIMESTAMP,
					   'AUTO_ATENDIMENTO_SENHA_PASSO_4'
					 );
			  ";
	@pg_query($db,$qr_sql);
	
	$qr_sql = "
				SELECT pp.nome_empresa,
                       p.nome,
					   p.email,
					   p.email_profissional,
					   (SELECT COUNT(*)
                          FROM public.participantes_ccin pc1
                         WHERE pc1.dt_validade = TO_TIMESTAMP('26/02/2010 17:09:02','DD/MM/YYYY HH24:MI:SS')
                           AND pc1.cd_empresa            = p.cd_empresa
                           AND pc1.cd_registro_empregado = p.cd_registro_empregado
                           AND pc1.seq_dependencia       = p.seq_dependencia) AS fl_contrato_pos,
					   COALESCE(p.endereco,'') || COALESCE(',' || p.nr_endereco,'') || COALESCE(' - ' || p.complemento_endereco,'') AS endereco,
					   COALESCE(p.cidade,'') AS cidade,
					   COALESCE(p.unidade_federativa,'') AS uf,
					   TO_CHAR(p.cep,'FM00000') || '-' || TO_CHAR(p.complemento_cep,'FM000') AS cep
				  FROM public.participantes p
				  JOIN public.patrocinadoras pp
					ON pp.cd_empresa             = p.cd_empresa 
				 WHERE p.cd_empresa             = ".$_SESSION['EMP']."
				   AND p.cd_registro_empregado  = ".$_SESSION['RE']."
				   AND p.seq_dependencia        = ".$_SESSION['SEQ']."
				   AND (p.email LIKE '%@%' OR p.email_profissional LIKE '%@%' OR 2 = ".intval($_SESSION['TPS']).")
			  ";
				   
	$ob_resul = pg_query($db, $qr_sql);	
	
  	if(pg_num_rows($ob_resul) == 0)
	{
		$tpl = new TemplatePower('tpl/tpl_auto_atendimento_senha_sem_email.html');
		$tpl->prepare();
		$tpl->printToScreen();
		exit;
	}
	else
	{
		$ar_reg = pg_fetch_array($ob_resul);
		
		if((intval($_SESSION['TPS']) == 2) and (intval($ar_reg["fl_contrato_pos"]) > 0)) #### CONTRATO ALTERADO PELO EMPRESTIMO POS ####
		{
			$tpl = new TemplatePower('tpl/tpl_auto_atendimento_senha_contrato_pos.html');	
			$tpl->prepare();
			$tpl->printToScreen();
			exit;			
		}
		else
		{
			$tpl = new TemplatePower('tpl/tpl_auto_atendimento_senha_passo_4.html');	
			$tpl->prepare();
			
			$tpl->assign('ds_empresa', $ar_reg['nome_empresa']);
			$tpl->assign('ds_nome',    $ar_reg['nome']);
			$tpl->assign('endereco',   $ar_reg['endereco']);
			$tpl->assign('cidade',     $ar_reg['cidade']);
			$tpl->assign('uf',         $ar_reg['uf']);
			$tpl->assign('cep',        $ar_reg['cep']);
			$tpl->assign('emp', $_SESSION['EMP']);
			$tpl->assign('re',  $_SESSION['RE']);
			$tpl->assign('seq', $_SESSION['SEQ']);
			$tpl->assign('fl_box_email', (intval($_SESSION['TPS']) == 2 ? "display:none;" : ""));
			$tpl->assign('fl_box_endereco', (intval($_SESSION['TPS']) != 2 ? "display:none;" : ""));
			$_SESSION['NR_SEG'] = rand(111111, 999999);
			$tpl->assign('nr_codigo_seguranca', $_SESSION['NR_SEG']);

			$email_confirma = $ar_reg['email'];
			if((trim($ar_reg['email']) != "") and (trim($ar_reg['email_profissional']) != ""))
			{
				$email_confirma = trim($ar_reg['email'])." e ".trim($ar_reg['email_profissional']);
			}
			elseif((trim($ar_reg['email']) == "") and (trim($ar_reg['email_profissional']) != ""))
			{
				$email_confirma = $ar_reg['email_profissional'];
			}

			$tpl->assign('email_confirma', $email_confirma);
			$tpl->printToScreen();
			exit;
		}
	}
	
?>
