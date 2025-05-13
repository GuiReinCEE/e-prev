<?php
set_title('Empréstimo - Docs - EMP_003 ---- '.$row['cd_empresa']."/".$row['cd_registro_empregado']."/".$row['seq_dependencia']);
$this->load->view('header');
?>
<style>
.bordaCallCenter {
    border: thin solid #990000;
}
</style>
<script>
	function checkOpcaoEletronico()
	{
		if($("#cd_opcao_envio").val() == "I")
		{
			return true;
		}
		else
		{
			return false
		}
	}

	function imprimeNotaPromissoria() 
	{
		if(checkOpcaoEletronico())
		{
			alert("Este participante optou por ELETRÔNICO");
		}		
		
		var ds_url = "<?= site_url('ecrm/emprestimo_np/completa/'.$row['cd_contrato']) ?>";
		var nr_width  = (screen.width - 10);
		var nr_height = (screen.height - 80);
		var nr_left = ((screen.width - 10) - nr_width) / 2;
		var nr_top = ((screen.height - 80) - nr_height) / 2;

		window.open(ds_url, "wViewNP", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=yes,status=no,titlebar=no,toolbar=yes");
	}	

	function imprimeContrato() 
	{
		if(checkOpcaoEletronico())
		{
			alert("Este participante optou por ELETRÔNICO");
		}		
		
		var ds_url = "http://<?= $_SERVER["SERVER_NAME"]?>/controle_projetos/contrato_emprestimo.php";
			ds_url += "?cd_contrato=<?= $row['cd_contrato']?>";
			ds_url += "&fl_cad=S&fl_fin=S&fl_ass=S";
		
		var nr_width  = (screen.width - 10);
		var nr_height = (screen.height - 80);
		var nr_left = ((screen.width - 10) - nr_width) / 2;
		var nr_top = ((screen.height - 80) - nr_height) / 2;

		window.open(ds_url, "wViewContrato", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=yes,status=no,titlebar=no,toolbar=yes");
	}


	function imprimeContratoResumido() 
	{
		if(checkOpcaoEletronico())
		{
			alert("Este participante optou por ELETRÔNICO");
		}		
		
		var ds_url = "http://<?= $_SERVER["SERVER_NAME"]?>/controle_projetos/contrato_emprestimo.php";
			ds_url += "?cd_contrato=<?= $row['cd_contrato']?>";
		ds_url += "&fl_cad=S&fl_fin=S&fl_ass=S&tp_imp=1";

		var nr_width  = (screen.width - 10);
		var nr_height = (screen.height - 80);
		var nr_left = ((screen.width - 10) - nr_width) / 2;
		var nr_top = ((screen.height - 80) - nr_height) / 2;

		window.open(ds_url, "wViewContratoResumido", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=yes,status=no,titlebar=no,toolbar=yes");		 									
	}

	function imprimeDemonstrativo() 
	{
		if(checkOpcaoEletronico())
		{
			alert("Este participante optou por ELETRÔNICO");
		}		
		
		var ds_url = "http://<?= $_SERVER["SERVER_NAME"]?>/controle_projetos/contrato_emprestimo.php";
			ds_url += "?cd_contrato=<?= $row['cd_contrato']?>";
			ds_url += "&fl_cad=S&fl_fin=S&fl_ass=S&tp_imp=3&fl_dem=S";
		
		var nr_width  = (screen.width - 10);
		var nr_height = (screen.height - 80);
		var nr_left = ((screen.width - 10) - nr_width) / 2;
		var nr_top = ((screen.height - 80) - nr_height) / 2;

		window.open(ds_url, "wViewDemonstrativo", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=yes,status=no,titlebar=no,toolbar=yes");		 									
	}
	
	function imprimeContrato13()
	{
		var ds_url = "http://<?= $_SERVER["SERVER_NAME"]?>/cieprev/up/contrato_emprestimo13/contrato_emprestimo13-20210205.pdf";

		var nr_width  = (screen.width - 10);
		var nr_height = (screen.height - 80);
		var nr_left = ((screen.width - 10) - nr_width) / 2;
		var nr_top = ((screen.height - 80) - nr_height) / 2;

		window.open(ds_url, "wViewDemonstrativo", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=yes,status=no,titlebar=no,toolbar=yes");		 									
	}	
	
	function imprimeContratoResumido13() 
	{
		var ds_url = "http://<?= $_SERVER["SERVER_NAME"]?>/controle_projetos/contrato_emprestimo.php";
			ds_url += "?cd_contrato=<?= $row['cd_contrato']?>";
		ds_url += "&fl_cad=S&fl_fin=S&fl_ass=S&tp_imp=1&fl_emp_13=S";

		var nr_width  = (screen.width - 10);
		var nr_height = (screen.height - 80);
		var nr_left = ((screen.width - 10) - nr_width) / 2;
		var nr_top = ((screen.height - 80) - nr_height) / 2;

		window.open(ds_url, "wViewContratoResumido13", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=yes,status=no,titlebar=no,toolbar=yes");		 									
	}	
	
	function imprimeAcordo13()
	{
		var ds_url = "http://<?= $_SERVER["SERVER_NAME"]?>/cieprev/up/contrato_emprestimo13/acordo_emprestimo13-20190314-2vias.pdf";
		
		var nr_width  = (screen.width - 10);
		var nr_height = (screen.height - 80);
		var nr_left = ((screen.width - 10) - nr_width) / 2;
		var nr_top = ((screen.height - 80) - nr_height) / 2;

		window.open(ds_url, "wViewAcordoEmp13", "left="+nr_left+",top="+nr_top+",width="+nr_width+",height="+nr_height+",scrollbars=yes,resizable=yes,directories=no,location=no,menubar=yes,status=no,titlebar=no,toolbar=yes");		 									
	}	
	
	$(function(){
		<?php
			if($fl_mostrar_header == "N")
			{
				echo '$(".header_fundo").hide();';
			}
		?>
	});
</script>
<BR>	
<table border="0" width="500" align="center" cellpadding="1" cellspacing="5">
	<tr> 
		<td colspan="2" bgcolor="#F9EDBE" style="border: 1px solid #F0C36D;"> 
			<div align="center">
				<font color="#3058ED" size="3" face="Tahoma, Verdana, Arial"><b>EMPRÉSTIMO CONCEDIDO COM SUCESSO</b></font>
			</div>
		</td>
	</tr>
<?php
	if($row['origem_contrato'] == 'C')
	{
?>
	<tr>
		<td colspan="2">
			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bordaCallCenter">
				<tr>
					<td bgcolor="#990000" align="center">
						<b><font color="#FFFFFF" face="Tahoma, Verdana, Arial">Mensagem Call Center</font></b>
					</td>
				</tr>
				<tr>
					<td class="style10" style="padding: 3px;">
					<?php
						if(strtoupper($row['cd_opcao_envio']) == "I")
						{
							echo '
								<span class="textoCallCenter" style="font-size: 10pt;">
									O número do contrato é <b>'.$row['cd_contrato'].'</b>
									<br><br>
									O demonstrativo deste empréstimo contendo todas as taxas aplicadas no mês, será enviado para a seu email(s): 
									<BR><BR>
									'.(trim(strtoupper($row['email'])) == "NULL" ? "" : $row['email']).'
									'.(trim(strtoupper($row['email_profissional'])) == "NULL" ? "" : br().$row['email_profissional']).'
									<BR><BR>
								</span>							
							';							
						}
						else
						{
							echo '
								<span class="textoCallCenter" style="font-size: 10pt;">
									O número do contrato é <b>'.$row['cd_contrato'].'</b>
									<br><br>
									O demonstrativo deste empréstimo contendo todas as taxas aplicadas no mês, será enviado para a sua residência.
									<br><br>
								</span>
							';
						}
					?>	
					</td>
				</tr>
			</table>
		</td>
	</tr>
<?php
	}
	elseif($row['origem_contrato'] == 'N')
	{
?>
	<tr>
		<td colspan="2">
			<table width="100%" border="0" cellpadding="5" cellspacing="0" class="bordaCallCenter">
				<tr>
					<td bgcolor="#990000" align="center">
						<b><font color="#FFFFFF" face="Tahoma, Verdana, Arial">Mensagem Atendente</font></b>
					</td>
				</tr>
				<tr>
					<td align="center" style="padding: 3px;">
					<?php
						if($usuario_confirmacao == 'A')
						{
							echo '
								<span class="textoCallCenter" style="font-size: 10pt;">
									'.($row['fl_emprestimo_13'] == "S" ? "<BR>EMPRÉSTIMO 13º<BR>" : "").'
									<BR>
									ENTREGAR A 2º VIA DA PROPOSTA
									<BR><BR>
								</span>							
							';
						}
						else
						{
							echo '
								<span class="textoCallCenter" style="font-size: 10pt;">
									'.($row['fl_emprestimo_13'] == "S" ? "<BR>EMPRÉSTIMO 13º<BR>" : "").'
									<BR>
									O demonstrativo será enviado para a seu email(s): 
									<BR>
									'.(trim(strtoupper($row['email'])) == "NULL" ? "" : $row['email']).'
									'.(trim(strtoupper($row['email_profissional'])) == "NULL" ? "" : br().$row['email_profissional']).'
									<BR><BR>
									(PARA OS SEM E-MAIL. ENTREGAR DEMONSTRATIVO)
									<BR><BR>
								</span>							
							';				
						}
					?>	
					</td>
				</tr>
			</table>
		</td>
	</tr>
<?php		
	}
?>
	<tr>
		<td>
			<table width="245" border="0" cellpadding="0" cellspacing="1" class="bordaCallCenter">
				<tr>
					<td bgcolor="#990000">
						<div align="center">
							<b><font color="#FFFFFF" face="Tahoma, Verdana, Arial">Opção Empréstimo</font></b>
						</div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#Fafafa">
						<table align="center" width="100%" border="0" cellpadding="1" cellspacing="0">
							<tr>
								<td class="style2" style="text-align:center; color: #000000;">
								<BR>
								<b>
								<?php
									if($row['forma_calculo'] == "O") ## POS-FIXADO
									{
										echo 'PÓS-FIXADO';
									}
									
									if($row['forma_calculo'] == "P") ## PRE-FIXADO
									{
										echo 'PREFIXADO';
									}								
								?>
								</b><BR><BR>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>	
		</td>
		<td>
			<table width="245" border="0" cellpadding="0" cellspacing="1" class="bordaCallCenter">
				<tr>
					<td bgcolor="#990000">
						<div align="center">
							<b><font color="#FFFFFF" face="Tahoma, Verdana, Arial">Opção</font></b>
						</div>
					</td>
				</tr>
				<tr>
					<td bgcolor="#Fafafa">
						<table align="center" width="100%" border="0" cellpadding="1" cellspacing="0">
							<tr>
								<td class="style2" style="text-align:center; color: #000000;">
									<BR><b><?= $row['opcao_envio']?></b><BR><BR>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>	
		</td>
	</tr>    
    <tr> 
		<td width="160"><strong><font size="2" face="Tahoma, Verdana, Arial">Contrato</font></strong></td>
		<td><font size="2" face="Tahoma, Verdana, Arial"><strong><b><?= $row['cd_contrato']?></b></strong></font></td>
	</tr>
	<tr> 
		<td width="160" bgcolor="#F0F0F0"><strong><font size="2" face="Tahoma, Verdana, Arial">Empresa</font></strong></td>
		<td bgcolor="#F0F0F0"><font size="2" face="Tahoma, Verdana, Arial"><?= $row['cd_empresa']?> - <?= $row['sigla']?></font></td>
	</tr>
	<tr> 
		<td width="160"><strong><font size="2" face="Tahoma, Verdana, Arial">RE</font></strong></td>
		<td><font size="2" face="Tahoma, Verdana, Arial"><?= $row['cd_registro_empregado']?></font></td>
	</tr>
	<tr> 
		<td width="160" bgcolor="#F0F0F0"><strong><font size="2" face="Tahoma, Verdana, Arial">Seq</font></strong></td>
		<td bgcolor="#F0F0F0"><font size="2" face="Tahoma, Verdana, Arial"><?= $row['seq_dependencia']?></font></td>
	</tr>
	<tr> 
		<td width="160"><strong><font size="2" face="Tahoma, Verdana, Arial">Nome</font></strong></td>
		<td><font size="2" face="Tahoma, Verdana, Arial"><?= $row['nome']?></font></td>
	</tr>
	<tr> 
		<td width="160" bgcolor="#F0F0F0"><strong><font size="2" face="Tahoma, Verdana, Arial">Data Depósito</font></strong></td>
		<td bgcolor="#F0F0F0"><font size="2" face="Tahoma, Verdana, Arial"><b><?= $row['dt_deposito']?></b></font></td>
	</tr>
	<tr> 
		<td width="160"><strong><font size="2" face="Tahoma, Verdana, Arial">Valor Depósito</font></strong></td>
		<td><font size="2" face="Tahoma, Verdana, Arial"><b><?= $row['vlr_deposito']?></b></font></td>
	</tr>
	<tr bgcolor="#F0F0F0"> 
		<td width="160"><strong><font size="2" face="Tahoma, Verdana, Arial">Número de Prestações</font></strong></td>
		<td><font size="2" face="Tahoma, Verdana, Arial"><?= $row['nro_prestacoes']?></font></td>
	</tr>
	<tr> 
		<td width="160"><strong><font size="2" face="Tahoma, Verdana, Arial">Data Primeira Prest</font></strong></td>
		<td><font size="2" face="Tahoma, Verdana, Arial"><?= $row['dt_primeira_prestacao']?></font></td>
	</tr>
	<tr bgcolor="#F0F0F0"> 
		<td width="160"><strong><font size="2" face="Tahoma, Verdana, Arial">Data Última Prest</font></strong></td>
		<td><font size="2" face="Tahoma, Verdana, Arial"><?= $row['dt_ultima_prestacao']?></font></td>
	</tr>
	<tr> 
		<td width="160">
			<strong>
				<font size="2" face="Tahoma, Verdana, Arial">
					<?php
						if($row['forma_calculo'] == "O") ## POS-FIXADO
						{
							echo '1ª Prest. Projetada';
						}
						
						if($row['forma_calculo'] == "P") ## PRE-FIXADO
						{
							echo 'Primeira Prestação';
						}								
					?>
				</font>
			</strong>
		</td>
		<td><font size="2" face="Tahoma, Verdana, Arial"><b><?= $row['vlr_prestacao']?></b></font></td>
	</tr>
	<tr> 
		<td width="160" bgcolor="#F0F0F0"><strong><font size="2" face="Tahoma, Verdana, Arial">Banco</font></strong></td>
		<td bgcolor="#F0F0F0"><font size="2" face="Tahoma, Verdana, Arial"><?= $row['cd_instituicao']?></font></td>
	</tr>
	<tr> 
		<td width="160"><strong><font size="2" face="Tahoma, Verdana, Arial">Ag&ecirc;ncia</font></strong></td>
		<td><font size="2" face="Tahoma, Verdana, Arial"><?= $row['cd_agencia']?></font></td>
	</tr>
	<tr> 
		<td width="160" bgcolor="#F0F0F0"><strong><font size="2" face="Tahoma, Verdana, Arial">Conta</font></strong></td>
		<td bgcolor="#F0F0F0"><font size="2" face="Tahoma, Verdana, Arial"><?= $row['conta']?></font></td>
	</tr>   
	<tr> 
		<td colspan="2">
		    <table border="0" align="center" cellpadding="2" cellspacing="0" width="100%">
				<tr>
					<td bgcolor="#006633">
						<div align="center">
							<font color="#FFFFFF" size="2" face="Tahoma, Verdana, Arial">
								<strong>IMPRESSÃO DE DOCUMENTOS</strong>
							</font> 
						</div>
					</td>
				</tr>
				<tr> 
					<td bgcolor="#F0F0F0">
						<BR>
						<div align="center">
							<?php
								if($row['fl_emprestimo_13'] == "S")
								{
									echo '
											<input type="button" onclick="imprimeContrato13();" value="Contrato 13º" class="btn btn-mini" style="width: 110px;"> 
											<input type="button" onclick="imprimeContratoResumido13();" value="Proposta 13º" class="btn btn-mini" style="width: 110px;"> 
									     ';
								}
								else
								{
									echo '
											<input type="button" onclick="imprimeContrato();" value="Contrato" class="btn btn-mini" style="width: 110px;"> 
											<input type="button" onclick="imprimeContratoResumido();" value="Resumido" class="btn btn-mini" style="width: 110px;"> 
									     ';									
								}
							?>

							<input type="button" onclick="imprimeDemonstrativo();" value="Demonstrativo" class="btn btn-mini" style="width: 110px;"> 
							<input type="button" onclick="imprimeNotaPromissoria();" value="Nota Promissoria" class="btn btn-mini" style="width: 110px;">  
		
							<BR><BR>
							<input type="button" onclick="imprimeAcordo13();" value="Acordo 13º" class="btn btn-mini" style="width: 110px;"> 
							
						</div>
						<BR>
					</td>
				</tr>					
			</table>
		</td>
	</tr>
	<tr> 
		<td align="left">
			<div style="text-align:left; font-size: 7pt;"><?= date("d/m/Y H:i:s")?></div>		
		</td>
		<td align="right">
			<div style="text-align:right; font-size: 7pt;"><?= "[".$row["origem_contrato"]."]"."[".$SKT_IP.":".$SKT_PORTA."]"?></div>		
		</td>		
	</tr>
</table>  
	
<?php
    echo br(10);
    $this->load->view('footer_interna');
?>	