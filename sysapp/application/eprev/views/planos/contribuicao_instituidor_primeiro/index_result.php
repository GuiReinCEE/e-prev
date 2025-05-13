<?php
	/*
		#### FORMAS DE PAGAMENTO ####
		"BCO";"DÉBITO EM CONTA CORRENTE"
		"BDL";"BLOQUETO BANCARIO"
		"CHQ";"CHEQUE"
		"DEP";"DEPÓSITO BANCÁRIO"
		"FLT";"FOLHA PATROCINADORA"
		"FOL";"FOLHA DE PAGAMENTO"
	*/
	
	#echo "<PRE>".print_r($ar_cadastro,true)."</PRE>";
	#echo "<PRE>".print_r($ar_geracao,true)."</PRE>";
	#echo "<PRE>".print_r($ar_financeiro,true)."</PRE>";
	#echo "<PRE>".print_r($ar_financeiro_email,true)."</PRE>";
	#exit;
	

	if(count($ar_cadastro) == 0)
	{
		echo "
				<br><br><br>
				<center>
					<h1 style='font-family: Calibri, Arial; font-size: 15pt; color:blue;'>
						A GP - CADASTRO não confirmou as inscrições para esta competência
					</h1>
				</center>
				<br><br><br>
			 ";		
		exit;
	}
	elseif(count($ar_geracao) == 0)
	{
		echo "
				<br><br><br>
				<center>
					<h1 style='font-family: Calibri, Arial; font-size: 15pt; color:blue;'>
						A GP - RECEITA não gerou as contribuições para esta competência
					</h1>
				</center>
				<br><br><br>
			 ";		
		exit;		
	}
	elseif(count($ar_financeiro) == 0)
	{
		echo "
				<br><br><br>
				<center>
					<h1 style='font-family: Calibri, Arial; font-size: 15pt; color:red;'>
						ERRO: Não foi encontrado registros
					</h1>
				</center>
				<br><br><br>
			 ";		
		exit;		
	}	
	
?>
<BR>
<style>
	.contribuicao_instituidor * {
		font-family: Verdana, Tahoma, Arial;
		font-size: 10pt;
		font-weight: normal;
	}
	
	.contribuicao_instituidor hr {
		border-width: 0;
		height: 1px;
		border-top-width: 1px;
		border-top-color: gray;
		border-top-style: dashed;

	}	

	.ci_cadastro * {
		font-family: Verdana, Tahoma, Arial;
		font-size: 10pt;
		font-weight: normal;
	}
	
	.ci_cadastro {
		border: 1px solid #64992C;
	}	
	
	.ci_cadastro input{
		border: 1px solid gray;
		padding-right: 3px;
	}	
	
	.ci_cadastro caption {
		white-space:nowrap;
		border: 1px solid #64992C;
		font-family: Verdana, Tahoma, Arial;
		font-size: 10pt;
		font-weight: bold;
		text-align: center;
		line-height: 25px;
		background-color: #64992C;
		color: #FFFFFF;
	}	
	
	.ci_geracao * {
		font-family: Verdana, Tahoma, Arial;
		font-size: 10pt;
		font-weight: normal;
	}
	
	.ci_geracao {
		border: 1px solid #B36D00;
	}	
	
	.ci_geracao input{
		border: 1px solid gray;
		padding-right: 3px;
	}	
	
	.ci_geracao caption {
		white-space:nowrap;
		border: 1px solid #B36D00;
		font-family: Verdana, Tahoma, Arial;
		font-size: 10pt;
		font-weight: bold;
		text-align: center;
		line-height: 25px;
		background-color: #B36D00;
		color: #FFFFFF;
	}	
	
	
	.ci_financeiro * {
		font-family: Verdana, Tahoma, Arial;
		font-size: 10pt;
		font-weight: normal;
	}
	
	.ci_financeiro {
		border: 1px solid #0B5394;
	}	
	
	.ci_financeiro input{
		border: 1px solid gray;
		padding-right: 3px;
	}	
	
	.ci_financeiro caption {
		white-space:nowrap;
		border: 1px solid #0B5394;
		font-family: Verdana, Tahoma, Arial;
		font-size: 10pt;
		font-weight: bold;
		text-align: center;
		line-height: 25px;
		background-color: #0B5394;
		color: #FFFFFF;
	}

	.destaca * {
		font-weight: bold;
	}
</style>

<h1 style="text-align:left;">
	Envio de Contribuição (Primeiro Pagamento) referente à <? echo $NR_MES."/".$NR_ANO;?><BR>
	Plano: <? echo $CD_PLANO; ?><BR>
	Empresa: <? echo $CD_EMPRESA; ?><BR>
</h1>

<table align="center" border="0" cellspacing="10" class="contribuicao_instituidor">
	<tr>
		<td valign="top">
			<!-- GP - CADASTRO -->
			<?php
				$qt_total_cadastro = ($ar_cadastro['tot_bdl_cadastro'] + 
									  $ar_cadastro['tot_debito_cc_cadastro'])
			?>
			<table border="0" cellspacing="5" class="ci_cadastro">
				<caption>Confirmação de Inscrição - GP - CAD<BR>(<? echo $ar_cadastro['usuario_cadastro'];?>)</caption>
				<tr>
					<td style="width: 100px;"></td>
					<td align="center">Qtd</td>
					<td align="center">Vlr</td>
				</tr>	
				<tr>
					<td colspan="3"><hr></td>
				</tr>					
				<tr>
					<td>BDL Pago</td>
					<td>
						<input type="text" value="<? echo $ar_cadastro['tot_bdl_pg_antecipado_cadastro'];?>" name="tot_bdl_pg_antecipado_cadastro" id="tot_bdl_pg_antecipado_cadastro" readonly style="text-align:right; width: 60px;">
					</td>
					<td></td>
				</tr>	
				<tr>
					<td colspan="3"><hr></td>
				</tr>				
				<tr class="destaca">
					<td>BDL</td>
					<td>
						<input type="text" value="<? echo $ar_cadastro['tot_bdl_cadastro'];?>" name="tot_bdl_cadastro" id="tot_bdl_cadastro" readonly style="text-align:right; width: 60px;">
					</td>
					<td></td>
				</tr>
				<tr class="destaca">
					<td>BCO</td>
					<td>
						<input type="text" value="<? echo $ar_cadastro['tot_debito_cc_cadastro'];?>" name="tot_debito_cc_cadastro" id="tot_debito_cc_cadastro" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo number_format($ar_cadastro['vlr_debito_cc_cadastro'],2,',','.');?>" name="vlr_debito_cc_cadastro" id="vlr_debito_cc_cadastro" readonly style="text-align:right; width: 80px;">
					</td>					
				</tr>
				<tr>
					<td>Folha</td>
					<td>
						<input type="text" value="<? echo $ar_cadastro['tot_folha_cadastro'];?>" name="tot_folha_cadastro" id="tot_folha_cadastro" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo number_format($ar_cadastro['vlr_folha_cadastro'],2,',','.');?>" name="vlr_folha_cadastro" id="vlr_folha_cadastro" readonly style="text-align:right; width: 80px;">
					</td>					
				</tr>	
				<tr>
					<td>Folha Patroc</td>
					<td>
						<input type="text" value="<? echo $ar_cadastro['tot_fol_ter_cadastro'];?>" name="tot_fol_ter_cadastro" id="tot_fol_ter_cadastro" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo number_format($ar_cadastro['vlr_fol_ter_cadastro'],2,',','.');?>" name="vlr_fol_ter_cadastro" id="vlr_fol_ter_cadastro" readonly style="text-align:right; width: 80px;">
					</td>					
				</tr>				
				<tr>
					<td>Cheque</td>
					<td>
						<input type="text" value="<? echo $ar_cadastro['tot_cheque_cadastro'];?>" name="tot_cheque_cadastro" id="tot_cheque_cadastro" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo number_format($ar_cadastro['vlr_cheque_cadastro'],2,',','.');?>" name="vlr_cheque_cadastro" id="vlr_cheque_cadastro" readonly style="text-align:right; width: 80px;">
					</td>					
				</tr>	
				<tr>
					<td>Depósito</td>
					<td>
						<input type="text" value="<? echo $ar_cadastro['tot_deposito_cadastro'];?>" name="tot_deposito_cadastro" id="tot_deposito_cadastro" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo number_format($ar_cadastro['vlr_deposito_cadastro'],2,',','.');?>" name="vlr_deposito_cadastro" id="vlr_deposito_cadastro" readonly style="text-align:right; width: 80px;">
					</td>					
				</tr>
				<tr>
					<td colspan="3"><hr></td>
				</tr>				
				<tr>
					<td style="white-space:nowrap;">Total p/ Envio</td>
					<td>
						<input type="text" value="<? echo $qt_total_cadastro;?>" name="qt_total_cadastro" id="qt_total_cadastro" readonly style="text-align:right; width: 60px;">
					</td>
					<td style="font-size: 65%;">(BDL+BCO)</td>					
				</tr>				
			</table>
		</td>
		
		<td valign="top">
			<!-- GP - RECEITA -->
			<?php
				$qt_total_gerado = ($ar_geracao['tot_bdl_gerado'] + 
									$ar_geracao['tot_debito_cc_gerado'] )
			?>
			<table border="0" cellspacing="5" class="ci_geracao">
				<caption>Geração de Contribuição - GP - REC<BR>(<? echo $ar_geracao['usuario_geracao'];?>)</caption>
				<tr>
					<td style="width: 100px;"></td>
					<td align="center">Qtd</td>
					<td align="center">Vlr</td>
				</tr>	
				<tr>
					<td colspan="3"><hr></td>
				</tr>					
				<tr>
					<td>BDL Pago</td>
					<td>
						<input type="text" value="<? echo $ar_geracao['tot_bdl_pg_antecipado_gerado'];?>" name="tot_bdl_pg_antecipado_gerado" id="tot_bdl_pg_antecipado_gerado" readonly style="text-align:right; width: 60px;">
					</td>
					<td></td>
				</tr>	
				<tr>
					<td colspan="3"><hr></td>
				</tr>				
				<tr class="destaca">
					<td>BDL</td>
					<td>
						<input type="text" value="<? echo $ar_geracao['tot_bdl_gerado'];?>" name="tot_bdl_gerado" id="tot_bdl_gerado" readonly style="text-align:right; width: 60px;">
					</td>
					<td></td>
				</tr>
				<tr class="destaca">
					<td>BCO</td>
					<td>
						<input type="text" value="<? echo $ar_geracao['tot_debito_cc_gerado'];?>" name="tot_debito_cc_gerado" id="tot_debito_cc_gerado" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo number_format($ar_geracao['vlr_debito_cc_gerado'],2,',','.');?>" name="vlr_debito_cc_gerado" id="vlr_debito_cc_gerado" readonly style="text-align:right; width: 80px;">
					</td>					
				</tr>
				<tr>
					<td>Folha</td>
					<td>
						<input type="text" value="<? echo $ar_geracao['tot_folha_gerado'];?>" name="tot_folha_gerado" id="tot_folha_gerado" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo number_format($ar_geracao['vlr_folha_gerado'],2,',','.');?>" name="vlr_folha_gerado" id="vlr_folha_gerado" readonly style="text-align:right; width: 80px;">
					</td>					
				</tr>	
				<tr>
					<td>Folha Patroc</td>
					<td>
						<input type="text" value="<? echo $ar_geracao['tot_fol_ter_gerado'];?>" name="tot_fol_ter_gerado" id="tot_fol_ter_gerado" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo number_format($ar_geracao['vlr_fol_ter_gerado'],2,',','.');?>" name="vlr_fol_ter_gerado" id="vlr_fol_ter_gerado" readonly style="text-align:right; width: 80px;">
					</td>					
				</tr>					
				<tr>
					<td>Cheque</td>
					<td>
						<input type="text" value="<? echo $ar_geracao['tot_cheque_gerado'];?>" name="tot_cheque_gerado" id="tot_cheque_gerado" readonly style="text-align:right; width: 60px;">
					</td>
					<td></td>					
				</tr>	
				<tr>
					<td>Depósito</td>
					<td>
						<input type="text" value="<? echo $ar_geracao['tot_deposito_gerado'];?>" name="tot_deposito_gerado" id="tot_deposito_gerado" readonly style="text-align:right; width: 60px;">
					</td>
					<td></td>					
				</tr>
				<tr>
					<td colspan="3"><hr></td>
				</tr>				
				<tr>
					<td style="white-space:nowrap;">Total p/ Envio</td>
					<td>
						<input type="text" value="<? echo $qt_total_gerado;?>" name="qt_total_gerado" id="qt_total_gerado" readonly style="text-align:right; width: 60px;">
					</td>
					<td style="font-size: 65%;">(BDL+BCO)</td>					
				</tr>				
			</table>
		</td>
		
		<td valign="top">
			<!-- GFC -->
			<?php
				$qt_total_financeiro = ($ar_financeiro['BDL']['qt_total'] + 
				                        $ar_financeiro['BCO']['qt_total'] );
										
				$qt_total_financeiro_email = ($ar_financeiro_email['BDL']['qt_total'] + 
				                              $ar_financeiro_email['BCO']['qt_total']);										
			?>
			<table border="0" cellspacing="5" class="ci_financeiro">
				<caption>Envio de Cobrança - GFC<BR>(<? echo $ar_financeiro['usuario_envio'];?>)</caption>
				<tr>
					<td style="width: 100px;"></td>
					<td align="center">Email</td>
					<td align="center">Qtd</td>
					<td align="center">Vlr</td>
				</tr>	
				<tr>
					<td colspan="4"><hr></td>
				</tr>					
				<tr class="destaca">
					<td><BR></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td colspan="4"><hr></td>
				</tr>				
				</tr>				
				<tr class="destaca">
					<td>BDL</td>
					<td>
						<input type="text" value="<? echo $ar_financeiro_email['BDL']['qt_total'];?>" name="tot_bdl_financeiro_email" id="tot_bdl_financeiro_email" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo $ar_financeiro['BDL']['qt_total'];?>" name="tot_bdl_financeiro" id="tot_bdl_financeiro" readonly style="text-align:right; width: 60px;">
					</td>
					<td></td>
				</tr>
				<tr class="destaca">
					<td>BCO</td>
					<td>
						<input type="text" value="<? echo $ar_financeiro_email['BCO']['qt_total'];?>" name="tot_debito_cc_financeiro_email" id="tot_debito_cc_financeiro_email" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo $ar_financeiro['BCO']['qt_total'];?>" name="tot_debito_cc_financeiro" id="tot_debito_cc_financeiro" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo number_format($ar_financeiro['BCO']['vl_total'],2,',','.');?>" name="vlr_debito_cc_financeiro" id="vlr_debito_cc_financeiro" readonly style="text-align:right; width: 80px;">
					</td>					
				</tr>
				<tr>
					<td>Folha</td>
					<td></td>
					<td>
						<input type="text" value="<? echo $ar_geracao['tot_folha_gerado'];?>" name="tot_folha_financeiro" id="tot_folha_financeiro" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo number_format($ar_geracao['vlr_folha_gerado'],2,',','.');?>" name="vlr_folha_financeiro" id="vlr_folha_financeiro" readonly style="text-align:right; width: 80px;">
					</td>					
				</tr>
				<tr>
					<td>Folha Patroc</td>
					<td></td>
					<td>
						<input type="text" value="<? echo $ar_geracao['tot_fol_ter_gerado'];?>" name="tot_folha_ter_financeiro" id="tot_folha_ter_financeiro" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo number_format($ar_geracao['vlr_fol_ter_gerado'],2,',','.');?>" name="vlr_folha_ter_financeiro" id="vlr_folha_ter_financeiro" readonly style="text-align:right; width: 80px;">
					</td>					
				</tr>				
				<tr>
					<td>Cheque</td>
					<td></td>
					<td>
						<input type="text" value="<? echo $ar_geracao['tot_cheque_gerado'];?>" name="tot_cheque_financeiro" id="tot_cheque_financeiro" readonly style="text-align:right; width: 60px;">
					</td>
					<td></td>					
				</tr>	
				<tr>
					<td>Depósito</td>
					<td></td>
					<td>
						<input type="text" value="<? echo $ar_geracao['tot_deposito_gerado'];?>" name="tot_deposito_financeiro" id="tot_deposito_financeiro" readonly style="text-align:right; width: 60px;">
					</td>
					<td></td>					
				</tr>
				<tr>
					<td colspan="4"><hr></td>
				</tr>				
				<tr>
					<td style="white-space:nowrap;">Total p/ Envio</td>
					<td class="destaca">
						<input type="text" value="<? echo $qt_total_financeiro_email;?>" name="qt_total_financeiro_email" id="qt_total_financeiro_email" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo $qt_total_financeiro;?>" name="qt_total_financeiro" id="qt_total_financeiro" readonly style="text-align:right; width: 60px;">
					</td>
					<td style="font-size: 65%;">(BDL+BCO)</td>					
				</tr>				
			</table>
		</td>		
	</tr>
</table>


<?php
	#### INCONSISTENCIAS ####
	if(!$fl_enviado)
	{
		if( intval($ar_geracao['tot_bdl_gerado']) != intval($ar_financeiro['BDL']['qt_total']) )
		{
			$erro = "- Total BDL Gerado (".intval($ar_geracao['tot_bdl_gerado']).") é diferente do Total BDL para Envio (".intval($ar_financeiro['BDL']['qt_total']).")";
			echo "
					<center>
						<span class='label label-warning'>
							".$erro."
						</span>
					</center>
					<script>
						alert('INCONSISTÊNCIA');
					</script>
					<br><br><br>
					<br><br><br>
				 ";		
			exit;		
		}

		if( intval($ar_geracao['tot_bdl_gerado']) != intval($ar_financeiro_email['BDL']['qt_total']) )
		{
			$erro = "";
			echo "
					
					<center>
						<span class='label label-warning'>
							- Total BDL Gerado (".intval($ar_geracao['tot_bdl_gerado']).") é diferente do Total BDL com email para envio (".intval($ar_financeiro_email['BDL']['qt_total']).")
						</span>
					</center>
					<BR>
					<center>
						<input type=\"button\" value=\"Listar sem email\" onclick=\"semEmail();\" class=\"btn btn-warning btn-mini\" style=\"width: 120px;\">
					</center>					
					<script>
						alert('INCONSISTÊNCIA');
					</script>					
					<br><br><br>
					<br><br><br>
				 ";		
			exit;		
		}	
		
		if( intval($ar_geracao['tot_debito_cc_gerado']) != intval($ar_financeiro['BCO']['qt_total']) )
		{
			$erro = "- Total BCO Gerado (".intval($ar_geracao['tot_debito_cc_gerado']).") é diferente do Total BCO para Envio (".intval($ar_financeiro['BCO']['qt_total']).")";
			echo "
					<center>
						<span class='label label-warning'>
							".$erro."
						</span>
					</center>
					<BR>
					<center>
						<input type=\"button\" value=\"Listar sem email\" onclick=\"semEmail();\" class=\"btn btn-warning btn-mini\" style=\"width: 120px;\">
					</center>					
					<script>
						alert('INCONSISTÊNCIA');
					</script>				
					<br><br><br>
					<br><br><br>
				 ";		
			exit;		
		}

		if( intval($ar_geracao['tot_debito_cc_gerado']) != intval($ar_financeiro_email['BCO']['qt_total']) )
		{
			$erro = "- Total BCO Gerado (".intval($ar_geracao['tot_debito_cc_gerado']).") é diferente do Total BCO com email para envio (".intval($ar_financeiro_email['BCO']['qt_total']).")";
			echo "
					<center>
						<span class='label label-warning'>
							".$erro."
						</span>
					</center>
					<script>
						alert('".$erro."');
					</script>					
				 ";		
		}	
		
		if(intval($qt_total_financeiro_email) == 0)
		{
			$erro = "- Não foi encontrado contribuições para enviar";
			echo "
					<center>
						<span class='label label-warning'>
							".$erro."  
						</span>
						<br><br><br>
						<br><br><br>
					</center>
					<script>
						alert('INCONSISTÊNCIA');
					</script>				
				 ";	
			exit;
		}		
	}
	
	

	#### BOTOES ####
	$bt_gerar        = false;
	$bt_envia_email  = false;
	
	#echo "fl_enviado => $fl_enviado | fl_gerado => $fl_gerado <BR>";
	
	if($fl_enviado)
	{
		$bt_gerar        = false;
		$bt_envia_email  = false;	
	}
	elseif($fl_gerado)
	{
		$bt_gerar        = false;
		$bt_envia_email  = true;		
	}
	else
	{
		$bt_gerar        = true;
		$bt_envia_email  = false;		
	}
	
?>
<table border="0" align="center" cellspacing="20">
	<tr style="height: 30px;">
		<td style="<? echo ($bt_gerar == true ? "" : "display:none;"); ?>">
			<input type="button" value="Gerar" onclick="gerar();" class="btn btn-primary">
			<BR><BR>
			<BR><BR>
			<BR><BR>
		</td>
		<td style="<? echo ($bt_envia_email == true ? "" : "display:none;"); ?>">
			<input type="button" value="Enviar Emails" onclick="enviarEmail();" class="btn btn-danger">
		</td>	
	</tr>
</table>


<div style="text-align:center; width: 100%; <? echo ($bt_gerar == true ? "display:none;" : ""); ?>">
<?php
$body=array();
$head = array( 
	'EMP/RE/SEQ',  
	'Nome',
	'Forma',
	'Situação',
	'Dt Geração'

);

foreach($ar_contribuicao_controle as $item)
{
	$body[] = array(
	    $item["cd_empresa"]."/".$item["cd_registro_empregado"]."/".$item["seq_dependencia"],
		array($item["nome"],"text-align:left;"),
		array($item["ds_contribuicao_controle_tipo"],"text-align:left;"),
		($item["fl_email_enviado"] == "S" ? '<span style="font-weight: bold; color: green;">Enviado</span>' : '<span style="font-weight: bold; color: blue;">Aguardando envio</span>'),
		$item["dt_geracao"]
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;
echo $grid->render();
?>
</div>
<BR><BR><BR>