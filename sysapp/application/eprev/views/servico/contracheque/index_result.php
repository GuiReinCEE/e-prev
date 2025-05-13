<?php
	$vl_sal_base     = 0;
	$vl_contrib_inss = 0;
	$vl_base_irrf    = 0;
	$vl_base_fgts    = 0;
	$vl_fgts_mes     = 0;
	$vl_rendimentos  = 0;
	$vl_descontos    = 0;
	$vl_liquido      = 0;
?>
	<style>
		@media screen {
			.imprimir_cc_sim {
				display: none; 
			}		
		}	
		
		@media print {
			.imprimir_cc_nao {
				display: none; 
			}

			.imprimir_cc_sim {
				display: initial; 
			}	
		}		
	
		.tabela_contracheque * {
			font: 10pt Verdana, Helvetica, arial;
			text-align: left;
		}
		
		.tabela_contracheque th {
			font-weight: bold;
		}		
	</style>
	
	<table class="imprimir_cc_nao" width="500" align="center" cellspacing="2" cellpadding="2">
		<tr> 
			<td>
				<?php
				echo form_start_box("default_competencia", "Período", TRUE, TRUE, 'style="width: 100%;"');
					if(trim($this->session->userdata('indic_04')) == "*")
					{				
						echo form_default_integer('cc_registro_empregado', 'RE do Colaborador:',$cc_registro_empregado);
					}
					
					if(intval($cc_registro_empregado) > 0)
					{
						echo form_default_dropdown('cc_dt_pagamento', 'Mês/Ano:', $ar_competencia, $cc_dt_pagamento);
					}
					else
					{
						echo form_default_hidden('cc_dt_pagamento', 'Mês/Ano:', "");
					}

					echo form_default_row('', '', 
						comando("btFiltrarCC", "Filtrar", 'buscar_contracheque($("#cc_dt_pagamento").val(), $("#cc_registro_empregado").val());', array("class"=>"botao"))
						.nbs(2).
						comando("btImprimirCC", "Imprimir", 'window.print();')
					);
				echo form_end_box("default_competencia");	
				?>
			</td>
		</tr>
	</table>
	<BR>
	<table class="imprimir_cc_sim" width="500" align="center" cellspacing="2" cellpadding="2">
		<tr> 
			<td align="left" style="text-align: left;" width="200">	
				<img src="<?php echo base_url(); ?>/img/logofundacao_contracheque.jpg" border="0">
			</td>
			<td width="300"></td>
		</tr>
	</table>	
	<BR>
	<?php 
		if(intval($cc_registro_empregado) > 0)
		{
	?>
	<table width="500" class="tabela_contracheque" id="table-contracheque-corpo" align="center" cellspacing="2" cellpadding="2">
		<tr> 
			<th style="white-space: nowrap;">Empresa:</th>
			<td>FUNDAÇÃO CEEE DE SEGURIDADE SOCIAL - ELETROCEEE</td>
		</tr>
		<tr> 
			<td></td>
			<td>Rua dos Andradas 702 - Porto Alegre - RS</td>
		</tr>
		<tr> 
			<th style="white-space: nowrap;">CNPJ:</th>
			<td>90.884.412/0001-24</td>
		</tr>		  
		<tr> 
			<th style="white-space: nowrap;">Colaborador:</th>
			<td><?= $ar_contracheque[0]["nome"]?></td>
		</tr>
		<tr> 
			<th style="white-space: nowrap;">Endereço:</th>
			<td><?= $ar_contracheque[0]["endereco"].",".$ar_contracheque[0]["nr_endereco"].$ar_contracheque[0]["complemento_endereco"]." - ".$ar_contracheque[0]["bairro"]?></td>
		</tr>
		<tr> 
			<td></td>
			<td><?= $ar_contracheque[0]["cep"]." - ".$ar_contracheque[0]["cidade"]." - ".$ar_contracheque[0]["uf"]?></td>
		</tr>
		<tr> 
			<th style="white-space: nowrap;">Dt de Pgto:</th>
			<td><?= $ar_contracheque[0]["dt_pagamento"]?></td>
		</tr>
		<tr> 
			<td style="white-space: nowrap;">Mes/Ano:</td>
			<td><?= $ar_contracheque[0]["mes_ano"]?></td>
		</tr>
		<tr> 
			<td style="white-space: nowrap;">Banco / Agência:</td>
			<td><?= $ar_contracheque[0]["banco"]." / ".$ar_contracheque[0]["agencia"]?></td>
		</tr>			
		<tr> 
			<td style="white-space: nowrap;">Conta:</td>
			<td><?= $ar_contracheque[0]["conta"]?></td>
		</tr>			
	</table>
	<br>
	<?php 
		}
	?>
	<table width="500" class="tabela_contracheque" align="center" cellspacing="2" cellpadding="2">
		<tr> 
			<td>Demonstrativo de Pagamento:</td>
		</tr>
	</table>	
	<table width="500" class="sort-table" id="table_contracheque_corpo" align="center" cellspacing="2" cellpadding="2">
		<thead>
		<tr>
			<th>Cód</th>
			<th>Descrição</th>
			<th>Ref.</th>
			<th>Valor</th>
			<th>Tipo</th>
		</tr>
		</thead>
		<tbody>
			<?php
				foreach($ar_contracheque as $item)
				{
					if(trim($item['codigo']) != '') 
					{
						if(strtoupper(trim($item['tipo'])) != 'B') 
						{					
							echo '
									<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
										<td align="center">'.$item["codigo"].'</td>
										<td align="left">'.$item["descricao"].'</td>
										<td align="right">'.number_format($item['referencia'],2,",",".").'</td>					
										<td align="right">'.number_format($item['valor'],2,",",".").'</td>
										<td align="center" style="'.(trim($item["tp_cor"]) != "" ? "color:".$item['tp_cor'].";" : "").'">'.$item["tipo"].'</td>
									</tr>
								 ';
						}
					}
					else
					{
						switch (trim($item['descricao'])) 
						{
							case 'Salario Base': $vl_sal_base     = $item['valor']; break;
							case 'Base INSS'   : $vl_contrib_inss = $item['valor']; break;
							case 'Base IRRF'   : $vl_base_irrf    = $item['valor']; break;
							case 'Base FGTS'   : $vl_base_fgts    = $item['valor']; break;
							case 'FGTS Mês'    : $vl_fgts_mes     = $item['valor']; break;
							case 'Rendimentos' : $vl_rendimentos  = $item['valor']; break;
							case 'Descontos'   : $vl_descontos    = $item['valor']; break;
							case 'Líquido'     : $vl_liquido      = $item['valor']; break;
						}						
					}
				}
			?>
		</tbody>
	</table>	
	<script>
		$(function(){
			var ob_resul = new SortableTable(document.getElementById("table_contracheque_corpo"),
			[
				"CaseInsensitiveString",
				"CaseInsensitiveString",
				"NumberFloatBR",
				"NumberFloatBR",
				"CaseInsensitiveString"
			]);
			ob_resul.onsort = function ()
			{
				var rows = ob_resul.tBody.rows;
				var l = rows.length;
				for (var i = 0; i < l; i++)
				{
					removeClassName( rows[i], i % 2 ? "sort-par" : "sort-impar" );
					addClassName( rows[i], i % 2 ? "sort-impar" : "sort-par" );
				}
			};
			ob_resul.sort(0, false);			
		});
	</script>	
	
	<table width="500" class="sort-table" align="center" cellspacing="2" cellpadding="2">
		<tr> 
			<td nowrap align="center"> 
				<span style="font-size: 7pt; ">SALÁRIO BASE</span>
				<br>
				<span style="font-size: 10pt; "><?= number_format($vl_sal_base,2,",",".")?></span>
			</td>
			<td nowrap align="center"> 
				<span style="font-size: 7pt; ">SAL. CONTR. INSS</span>
				<br>
				<span style="font-size: 10pt; "><?= number_format($vl_contrib_inss,2,",",".")?></span>
			</td>
			<td nowrap align="center"> 
				<span style="font-size: 7pt; ">FGTS DO MES</span>
				<br>
				<span style="font-size: 10pt; "><?= number_format($vl_fgts_mes,2,",",".")?></span>
			</td>
			<td nowrap align="center"> 
				<span style="font-size: 7pt; ">RENDIMENTOS</span>
				<br>
				<span style="font-size: 10pt; "><?= number_format($vl_rendimentos,2,",",".")?></span>
			</td>
		</tr>
		<tr> 
			<td nowrap align="center"> 
				<span style="font-size: 7pt; ">BASE IRRF</span>
				<br>
				<span style="font-size: 10pt; "><?= number_format($vl_base_irrf,2,",",".")?></span>
			</td>
			<td nowrap align="center"> 
				<span style="font-size: 7pt; ">BASE FGTS</span>
				<br>
				<span style="font-size: 10pt; "><?= number_format($vl_base_fgts,2,",",".")?></span>
			</td>
			<td nowrap align="center"> 
				<span style="font-size: 7pt; ">DESCONTOS</span>
				<br>
				<span style="font-size: 10pt; "><?= number_format($vl_descontos,2,",",".")?></span>
			</td>
			<td nowrap align="center"> 
				<span style="font-size: 7pt; "><b>LÍQUIDO</b></span>
				<br>
				<span style="font-size: 10pt;"><b><?= number_format($vl_liquido,2,",",".")?></b></span>
			</td>
		</tr>
	</table>	

	<?php 
		if(count($ar_beneficio) > 0)
		{
	?>
	<BR>
	
	<table width="500" class="tabela_contracheque" align="center" cellspacing="2" cellpadding="2">
		<tr> 
			<td>Extrato de Benefícios:</td>
		</tr>
	</table>
	<table width="500" class="sort-table" id="table_beneficio_corpo" align="center" cellspacing="2" cellpadding="2">
		<thead>
		<tr>
			<th>Descrição</th>
			<th>Partic. Empregado</th>
			<th>Partic. Empresa</th>
			<th>Total</th>
		</tr>
		</thead>
		<tbody>
			<?php
				$ar_resumo = Array();
				$ar_resumo["vl_resumo_empregado"] = 0;
				$ar_resumo["vl_resumo_empresa"]   = 0;
				$ar_resumo["vl_resumo_total"]     = 0;				
			
				foreach($ar_beneficio as $item)
				{
					echo '
							<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
								<td align="left" style="text-transform: capitalize;">'.$item["tipo_beneficio"].'</td>
								<td align="right">'.number_format($item['vl_empregado'],2,",",".").'</td>					
								<td align="right">'.number_format($item['vl_empresa'],2,",",".").'</td>
								<td align="right">'.number_format($item['vl_total'],2,",",".").'</td>
							</tr>
						 ';	
						 
					$ar_resumo["vl_resumo_empregado"]   += floatval($item['vl_empregado']);
					$ar_resumo["vl_resumo_empresa"]     += floatval($item['vl_empresa']);
					$ar_resumo["vl_resumo_total"]       += floatval($item['vl_total']);							 
				}
			?>

			<?php
				echo '
						<tr onmouseover="sortSetClassOver(this);" onmouseout="sortSetClassOut(this);">
							<td align="left"><b>Total</b></td>
							<td align="right"><b>'.number_format($ar_resumo["vl_resumo_empregado"],2,",",".").'</b></td>					
							<td align="right"><b>'.number_format($ar_resumo["vl_resumo_empresa"] ,2,",",".").'</b></td>
							<td align="right"><b>'.number_format($ar_resumo["vl_resumo_total"],2,",",".").'</b></td>
						</tr>
					 ';	
			?>		
		</tbody>
	</table>	
	<?php 
		}
	?>
	
<script>
	$(function(){
		$('html,body').animate({ scrollTop: $("#default_competencia").offset().top} , 'slow');	
	});
</script>	
	
	
	
	
	
	
	
	
	
	
	