<?php
	include_once('inc/sessao_auto_atendimento.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	ini_set('auto_detect_line_endings', true);
	include_once('inc/class.SocketAbstraction.inc.php');
	include_once('inc/ePrev.Service.Projetos.php');
	include( 'oo/start.php' );
	using( array( 'public.bloqueto' ) );


	$tpl = new TemplatePower('tpl/tpl_auto_atendimento.html');
	$tpl->prepare();
	include_once('auto_atendimento_monta_sessao.php');
	
	$tabela_instituidor = '
							<BR>
							<div style="font-family: calibri, arial; font-size: 12pt; width: 100%; text-align: justify;">
								CONTRIBUIÇÕES SUSPENSAS
								<BR><BR>
								A contribuição mensal de seu plano previdenciário vence sempre no dia 10 de cada mês. Este prazo é antecipado, caso esta data não caia em dia útil. 
								<BR><BR>
								Se você não pagar até a data do vencimento, a contribuição será automaticamente suspensa. Isso <b>NÃO</b> significa que você está com pendência no plano. 
								<BR><BR>
								Você pode deixar de pagar a contribuição vencida e substituí-la por um aporte (contribuição voluntária) no valor que desejar.
								<BR><BR>
								Você também pode pagar até as três últimas contribuições suspensas, gerando os boletos correspondentes aos meses abaixo. No entanto, incidirão encargos no valor de cada contribuição.
								<BR><BR>
								Se você optar pelo aporte (contribuição voluntária), <b>NÃO</b> haverá incidência de encargos.
							</div>

							<BR>
							<div class="link_contrib">
								Contribuições Suspensas disponíveis para pagamento:
								<BR><BR>
							</div>
							<table width="100%" cellspacing="1" cellpadding="0" border="0">
						  ';

	#### LOG ####
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
					   'CONTRIBUICAO_SUSPENSA'
					 )
		      ";
	@pg_query($db,$qr_sql); 

    
	$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

	
	$qr_sql = "
				SELECT COUNT(*) AS fl_ativo
				  FROM public.participantes
				 WHERE cd_empresa            = ".$_SESSION['EMP']."
				   AND cd_registro_empregado = ".$_SESSION['RE']."
				   AND seq_dependencia       = ".$_SESSION['SEQ']."
			       AND tipo_folha            IN (2,3,4,5,9,10,14,15,20,30,35,40,45,50,55,60,65,70,75,80)
		      ";
	$ob_resul = pg_query($db, $qr_sql);
	$ar_reg = pg_fetch_array($ob_resul);	
	
	if(intval($ar_reg['fl_ativo']) > 0)
	{
		$conteudo = '
					<br><br><br>
					<center>
						<h1 style="font-family: Calibri, Arial; font-size: 15pt;">
							Área somente para ATIVOS.
						</h1>
					</center>
					<br><br><br>
					';	
	}
    elseif(intval($_SESSION['PLANO']) == 9) #### FAMILIA ####
	{		
		$qr_sql = "
					SELECT ano_competencia, 
						   mes_competencia,
						   ds_boleto,
						   nr_competencia,
						   id_suspensao_presumida,
						   funcoes.cripto_mes_ano(mes_competencia,ano_competencia) AS comp,
                           (SELECT funcoes.cripto_re(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].")) AS re
					  FROM boleto.boleto_instituidor((SELECT funcoes.cripto_re(".$_SESSION['EMP'].",".$_SESSION['RE'].",".$_SESSION['SEQ'].")))
					 WHERE id_suspensao_presumida IN ('S')
					 ORDER BY nr_ordem, ano_competencia DESC, mes_competencia DESC
					 ".(($_SESSION['EMP'] == 24 and $_SESSION['RE'] == 10731) ? "" : "LIMIT 3")."
				  ";
		$ob_resul = pg_query($db,$qr_sql);		  
		#echo "<PRE>$qr_sql</PRE>"; exit;		
		
		#echo "<PRE>$qr_sql</PRE>";
		
		if(pg_num_rows($ob_resul) > 0)
		{
			#### MENSAL E ATRASADOS ####
			while ($ar_bloqueto = pg_fetch_array($ob_resul)) 
			{

				
				$linha_inst = '
								<tr>
									<td>
										<a class="link_contrib" href="familia_pagamento_valor.php?re={RE}&comp={COMP}"  target="_blank">- {mes_extenso} de {ano}</a>
									</td>
								</tr>
							  ';
				$linha_inst = str_replace('{RE}', $ar_bloqueto['re'], $linha_inst);
				$linha_inst = str_replace('{COMP}', $ar_bloqueto['comp'], $linha_inst);
				$linha_inst = str_replace('{ano}', $ar_bloqueto['ano_competencia'], $linha_inst);
				$linha_inst = str_replace('{mes_extenso}', $meses[$ar_bloqueto['mes_competencia']-1], $linha_inst);				
				$tabela_instituidor.= $linha_inst;

			}
		}
		else
		{
			$tabela_instituidor.= '
								<tr>
									<td>
									<div style="font-family: calibri, arial; font-size: 12pt; width: 100%; text-align: justify;">
									Sem contribuições suspensas.
									</div>
									</td>
								</tr>			
			                      ';
		}
		$tabela_instituidor.="</table>
		
		<BR>
		<div style='font-family: calibri, arial; font-size: 12pt; width: 100%; text-align: justify;'>
			Se você quer pagar contribuições anteriores ao período acima, entre em contato com a Central de Relacionamento da Fundação Família Previdência.
		</div>
		<BR><BR>
		
		";
	} # FIM FAMILIA
	else
	{
		$tabela_instituidor.= '
							<tr>
								<td>
									<div style="font-family: calibri, arial; font-size: 12pt; width: 100%; text-align: justify;">
									Sem contribuições suspensas.
									</div>
								</td>
							</tr>
							</table>							
							  ';
	}
	
	$conteudo = str_replace('{msg}', $msg, $conteudo);
	$conteudo = str_replace('{tabela_patrocinadora}', $tabela_patrocinadora, $conteudo);
	$conteudo = str_replace('{tabela_instituidor}', $tabela_instituidor, $conteudo);

//--------------------------------------------------------------------------------------------------
	$tpl->assign('conteudo',$conteudo);
	$tpl->printToScreen();
	pg_close($db);
//--------------------------------------------------------------------------------------------------
?>