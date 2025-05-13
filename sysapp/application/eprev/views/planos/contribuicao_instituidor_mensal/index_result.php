<?php
	/*
		#### FORMAS DE PAGAMENTO ####
		"BCO";"DÉBITO EM CONTA CORRENTE"
		"BDL";"BLOQUETO BANCARIO"
		"CHQ";"CHEQUE"
		"DEP";"DEPÓSITO BANCÁRIO"
		"FLT";"FOLHA PATROCINADORA"
		"FOL";"FOLHA DE PAGAMENTO"
		
		#### CODIGOS_COBRANCAS #####
		2450;"CONTRIBUIÇÃO SINPRO-RS PREV"
	
	*/
	
	#echo "<PRE>".print_r($ar_contribuicao_mensal,true)."</PRE>";
	#echo "<PRE>".print_r($ar_contribuicao_mensal_anterior,true)."</PRE>";
	#echo "<PRE>".print_r($ar_contribuicao_controle_enviado,true)."</PRE>";
	#echo "<PRE>".print_r($ar_contribuicao_mensal_cadastro,true)."</PRE>";
	#exit;
	
	
	
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
	Envio de Contribuição (Mensal) referente à <? echo $NR_MES."/".$NR_ANO;?><BR>
	Plano: <? echo $CD_PLANO; ?><BR>
	Empresa: <? echo $CD_EMPRESA; ?><BR>
</h1>
<table align="center" border="0" cellspacing="10" class="contribuicao_instituidor">
	<tr>
		<td valign="top">
			<?php
				$qt_total_anterior = $ar_contribuicao_mensal_anterior['PMBDL']['TOTAL'] +
				                     $ar_contribuicao_mensal_anterior['PMDCC']['TOTAL'] ;							
			?>
			<table border="0" cellspacing="5" class="ci_geracao">
				<caption>Competência anterior (GFC)</caption>
				<tr>
					<td style="width: 180px;"></td>
					<td align="center">Qtd</td>
				</tr>				
				<tr>
					<td>BDL</td>
					<td>
						<input type="text" value="<? echo $ar_contribuicao_mensal_anterior['PMBDL']['TOTAL'];?>" name="qt_1pg_ant" id="qt_1pg_ant" readonly style="text-align:right; width: 60px;">
					</td>					
				</tr>
				<tr>
					<td>BCO</td>
					<td>
						<input type="text" value="<? echo $ar_contribuicao_mensal_anterior['PMDCC']['TOTAL'];?>" name="qt_bco_ant" id="qt_bco_ant" readonly style="text-align:right; width: 60px;">
					</td>				
				</tr>
				<tr>
					<td colspan="3"><hr></td>
				</tr>				
				<tr>
					<td style="white-space:nowrap;">Total</td>
					<td class="destaca">
						<input type="text" value="<? echo $qt_total_anterior;?>" name="qt_total_ant" id="qt_total_ant" readonly style="text-align:right; width: 60px;">
					</td>					
					<td style="font-size: 65%;"></td>					
				</tr>				
			</table>
		</td>
		<td valign="top">
			<?php
				$qt_cadastro_normal    = $ar_contribuicao_mensal_cadastro['BDL']['NORMAL'] + $ar_contribuicao_mensal_cadastro['BCO']['NORMAL'] ;							
				$qt_cadastro_instituto = $ar_contribuicao_mensal_cadastro['BDL']['INSTITUTO'] + $ar_contribuicao_mensal_cadastro['BCO']['INSTITUTO'] ;							
				$qt_cadastro_total     = $ar_contribuicao_mensal_cadastro['BDL']['TOTAL'] + $ar_contribuicao_mensal_cadastro['BCO']['TOTAL'] ;							
			?>
			<table border="0" cellspacing="5" class="ci_cadastro">
				<caption>Totais Cadastro (GP - CAD)</caption>
				<tr>
					<td style="width: 180px;"></td>
					<td align="center">Instituto</td>
					<td align="center">Normal</td>
					<td align="center">Total</td>
				</tr>				
				<tr>
					<td>BDL</td>
					<td>
						<input type="text" value="<? echo $ar_contribuicao_mensal_cadastro['BDL']['INSTITUTO'];?>" name="qt_cad_bdl_instituto" id="qt_cad_bdl_instituto" readonly style="text-align:right; width: 60px;">
					</td>						
					<td>
						<input type="text" value="<? echo $ar_contribuicao_mensal_cadastro['BDL']['NORMAL'];?>" name="qt_cad_bdl_normal" id="qt_cad_bdl_normal" readonly style="text-align:right; width: 60px;">
					</td>	
					<td>
						<input type="text" value="<? echo $ar_contribuicao_mensal_cadastro['BDL']['TOTAL'];?>" name="qr_cad_bdl_total" id="qr_cad_bdl_total" readonly style="text-align:right; width: 60px;">
					</td>						
				</tr>
				<tr>
					<td>BCO</td>
					<td>
						<input type="text" value="<? echo $ar_contribuicao_mensal_cadastro['BCO']['INSTITUTO'];?>" name="qt_cad_bco_instituto" id="qt_cad_bco_instituto" readonly style="text-align:right; width: 60px;">
					</td>					
					<td>
						<input type="text" value="<? echo $ar_contribuicao_mensal_cadastro['BCO']['NORMAL'];?>" name="qt_cad_bco_normal" id="qt_cad_bco_normal" readonly style="text-align:right; width: 60px;">
					</td>				
					<td>
						<input type="text" value="<? echo $ar_contribuicao_mensal_cadastro['BCO']['TOTAL'];?>" name="qt_cad_bco_total" id="qt_cad_bco_total" readonly style="text-align:right; width: 60px;">
					</td>					
				</tr>
				<tr>
					<td colspan="3"><hr></td>
				</tr>				
				<tr>
					<td style="white-space:nowrap;">Total</td>
					<td class="destaca">
						<input type="text" value="<? echo $qt_cadastro_instituto;?>" name="qt_cadastro_instituto_mes" id="qt_cadastro_instituto_mes" readonly style="text-align:right; width: 60px;">
					</td>						
					<td class="destaca">
						<input type="text" value="<? echo $qt_cadastro_normal;?>" name="qt_cadastro_normal_mes" id="qt_cadastro_normal_mes" readonly style="text-align:right; width: 60px;">
					</td>	
					<td class="destaca">
						<input type="text" value="<? echo $qt_cadastro_total;?>" name="qt_cadastro_total_mes" id="qt_cadastro_total_mes" readonly style="text-align:right; width: 60px;">
					</td>						
					<td style="font-size: 65%;"></td>					
				</tr>				
			</table>
		</td>			
		<td valign="top">
			<?php
				$qt_total_bdl_email = $ar_contribuicao_mensal['PMBDL']['EMAIL'];
				$qt_total_bdl = $ar_contribuicao_mensal['PMBDL']['TOTAL'];
				
				$qt_total_email = $ar_contribuicao_mensal['PMBDL']['EMAIL'] +
				                  $ar_contribuicao_mensal['PMDCC']['EMAIL'] ;
							
				$qt_total = $ar_contribuicao_mensal['PMBDL']['TOTAL'] +
				            $ar_contribuicao_mensal['PMDCC']['TOTAL'] ;							
			?>
			<table border="0" cellspacing="5" class="ci_financeiro">
				<caption>Contribuições para envio (GFC)</caption>
				<tr>
					<td style="width: 180px;"></td>
					<td align="center">Email</td>
					<td align="center">Qtd</td>
				</tr>				
				<tr>
					<td>BDL</td>
					<td>
						<input type="text" value="<? echo $ar_contribuicao_mensal['PMBDL']['EMAIL'];?>" name="qt_1pg_email" id="qt_1pg_email" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo $ar_contribuicao_mensal['PMBDL']['TOTAL'];?>" name="qt_1pg" id="qt_1pg" readonly style="text-align:right; width: 60px;">
					</td>					
				</tr>
				<tr>
					<td>BCO</td>
					<td>
						<input type="text" value="<? echo $ar_contribuicao_mensal['PMDCC']['EMAIL'];?>" name="qt_bco_email" id="qt_bco_email" readonly style="text-align:right; width: 60px;">
					</td>
					<td>
						<input type="text" value="<? echo $ar_contribuicao_mensal['PMDCC']['TOTAL'];?>" name="qt_bco" id="qt_bco" readonly style="text-align:right; width: 60px;">
					</td>				
				</tr>
				<tr>
					<td colspan="3"><hr></td>
				</tr>				
				<tr>
					<td style="white-space:nowrap;">Total</td>
					<td class="destaca">
						<input type="text" value="<? echo $qt_total_email;?>" name="qt_total_email" id="qt_total_email" readonly style="text-align:right; width: 60px;">
					</td>
					<td class="destaca">
						<input type="text" value="<? echo $qt_total;?>" name="qt_total" id="qt_total" readonly style="text-align:right; width: 60px;">
					</td>					
					<td style="font-size: 65%;"></td>					
				</tr>				
			</table>
		</td>
	</tr>
</table>



<?php
	#### INCONSISTENCIAS ####
	if(!$fl_enviado)
	{
		if( intval($qt_total_bdl) != intval($qt_total_bdl_email) )
		{
			$erro = "- Total de BDL com email (".intval($qt_total_bdl_email).") é diferente do Total de BDL (".intval($qt_total_bdl).") ";

			echo "
				<script>
					alert('AVISO\\n\\n".$erro."\\n\\n');
				</script>
			";
		}	
		else
	    {
	        $erro = '';
	    }
			
			echo "
					<center>
						<span class='label label-warning'>".$erro."</span>
					</center>
					<BR>
					<center>
						<input type=\"button\" value=\"Listar sem email\" onclick=\"semEmail();\" class=\"btn btn-warning\">
					</center>						
					
				 ";		
		

		if(intval($qt_total) != intval($qt_cadastro_normal))
		{
			$fl_inconsitencia = 'C';

			if(intval($qt_total) > intval($qt_cadastro_normal))
			{
				$fl_inconsitencia = 'F';
			}

			$erro = "- Total Cadastro (".intval($qt_cadastro_normal).") é diferente do Total Financeiro (".intval($qt_total).")";
			echo "
					<center>
						<BR><BR>
						<span class='label label-important'>
							INCONSISTÊNCIA: ".$erro."  
						</span>
						<BR><BR>
						
						<center>
							<input type=\"button\" value=\"Listar Inconsistências Cadastro\" onclick=\"inconsistencias('C');\" class=\"btn btn-danger\">
							<input type=\"button\" value=\"Listar Inconsistências Financeiro\" onclick=\"inconsistencias('F');\" class=\"btn btn-danger\">	

							<input type=\"button\" value=\"Listar Participantes no Cadastro\" onclick=\"mensalCadastroParticipantes();\" class=\"btn btn-success\">
							
							<input type=\"button\" value=\"Listar Participantes no Financeiro\" onclick=\"mensalParticipantes();\" class=\"btn btn-info\">
							
						</center>
					
						
						<script>
							alert('INCONSISTÊNCIA\\n\\n".$erro."\\n\\n');
						</script>						
						<br><br><br>
						<br><br><br>
					</center>
				 ";	
			exit;
		}
		
		if(intval($qt_total_bdl_email) == 0)
		{
			$erro = "- Não foi encontrado BDL para enviar";
			echo "
					<center>
						<BR><BR>
						<span class='label label-important'>
							".$erro." 
						</span>
						<script>
							alert('Atenção\\n\\n".$erro."\\n\\n');
						</script>						
					</center>
				 ";	

		}		
		
		if(intval($qt_total) == 0)
		{
			$erro = "- Não foi encontrado contribuições para enviar";
			echo "
					<center>
						<BR><BR>
						<span class='label label-important'>
							INCONSISTÊNCIA: ".$erro." 
						</span>
						<script>
							alert('INCONSISTÊNCIA\\n\\n".$erro."\\n\\n');
						</script>						
						<br><br><br>
						<br><br><br>
					</center>
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
			<span class="label label-inverse" style="font-size: 14pt; line-height: 32px;">ATENÇÃO: Os e-mails só podem ser enviados 1 dia após a data de emissão no eletro.</span>
			<BR><BR>			
			<table border="0" align="center">
				<tr>
					<?php
					/*
					<td>Dt Emissão Eletro:</td>
					<td><?php echo form_date("dt_emissao_eletro"); ?></td>
					*/
					?>
					<td><input type="button" value="Gerar" onclick="gerar();" class="btn btn-primary btn-small"></td>
				</tr>
			</table>
			<BR><BR>
			<BR><BR>
			<BR><BR>			
		</td>
		<td style="<? echo ($bt_envia_email == true ? "" : "display:none;"); ?>">
			<BR>
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
		($item["fl_email_enviado"] == "S" ? '<span class="label label-success">Enviado</span>' : '<span class="label label-info">Aguardando envio</span>'),
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