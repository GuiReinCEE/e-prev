<?php
set_title("SINTAE / Contribuição Normal Mensal");
$this->load->view("header");
?>
<script type="text/javascript">
<!--
function cpp(ob)
{
	redir( '', '<?php echo site_url("sintae/contribuicao/primeiro"); ?>' );
}
function pm(ob)
{
	redir( '', '<?php echo site_url("sintae/contribuicao/mensal"); ?>' );
}
//-->
</script>
<?php

$abas[0] = array('aba_pri_pg', 'PRIMEIRO PAGAMENTO', FALSE, 'cpp(this)');
$abas[1] = array('aba_men_pg', 'PAGAMENTO MENSAL', TRUE, 'pm(this)');

echo aba_start( $abas );
echo form_start_box("contribuicao", "Contribuição Normal", FALSE);
?>
<style>
.contr-main, table
{
	font-family:arial;
	font-size:12px;
}

.contr-main input
{
	text-align:right;
	width:50px;
	border-style:solid;
}

.contr-box 
{
	background:#eeeeee;
	padding:10px;
	margin:10px;
}

.left
{

}

.disabled
{
	background:#D3D0C7;
	color:#000000;
}
.contr-cmd
{
	text-align:center;
}

.contr-err
{
	color:red;
}

</style>

	<table class="contr-main" align="center"><tr><td valign="top">
	<div class="contr-box">

		<b>Confirmação de Inscrição - GAP</b>
		<table>
			<tbody>
			<tr>
				<td/>
				<td align="center">Qtd</td>
				<td align="center">Vlr</td>
			</tr>
			<tr>
				<td>Total</td>
				<td><input type="text" value="0" readonly class="number disabled"/></td>
				<!--    -->
				<td/>
			</tr>
			<tr>
				<td>BDL</td>
				<td><input type="text" value="0" readonly class="number"/></td>
				<td/>
			</tr>
			<tr>
				<td>Cheque</td>
				<td><input type="text" value="0" readonly class="number disabled"/></td>
				<td><input type="text" value="0.00" readonly class="float disabled"/></td>
			</tr>
			<tr>
				<td>BCO</td>
				<td><input type="text" value="0" readonly class="number"/></td>
				<td><input type="text" value="0.00" readonly class="float"/></td>
			</tr>
			<tr>
				<td>Depósito</td>
				<td><input type="text" value="0" readonly class="number disabled"/></td>
				<td><input type="text" value="0.00" readonly class="float disabled"/></td>
			</tr>
			<tr>
				<td>Folha</td>
				<td><input type="text" value="" readonly class="number disabled"/></td>
				<td><input type="text" value="" readonly class="float disabled"/></td>
			</tr>
		</tbody>
		</table>

	</div></td><td valign="top">

	<div class="contr-box">
				 <b>Geração de Contribuição - GB</b>

				<table>
					<tbody><tr>
						<td/>
						<td align="center">Qtd</td>
						<td align="center">Vlr</td>
					</tr>
					<tr>
						<td>Geral</td>
						<td><input type="text" value="0" readonly class="number disabled"/></td>
						<td><input type="text" value="0.00" readonly class="float disabled"/></td>
					</tr>
					<tr>
						<td>BDL</td>
						<td><input type="text" value="0" readonly class="number"/></td>
						<td></td>
					</tr>
					<tr>
						<td>Cheque</td>
						<td><input type="text" value="0" readonly class="number disabled"/></td>
						<td></td>
					</tr>
					<tr>
						<td>BCO</td>
						<td><input type="text" value="0" readonly class="number"/></td>
						<td><input type="text" value="0.00" readonly class="float"/></td>
					</tr>
					<tr>
						<td>Depósito</td>
						<td><input type="text" value="0" readonly class="number disabled"/></td>
						<td></td>
					</tr>
					<tr>
						<td>Folha</td>
						<td><input type="text" value="" readonly class="number disabled"/></td>
						<td><input type="text" value="" readonly class="float disabled"/></td>
					</tr>
				</tbody></table>

	</div></td><td valign="top">
			
	<div class="contr-box">

				<b>Envio de Cobrança - GF</b>

				<table>
					<tbody><tr>
						<td/>
						<td align="center">Emails</td>
						<td align="center">Qtd</td>
						<td align="center">Vlr</td>
					</tr>
					<tr>
						<td>Geral</td>
						<td/>
						<td><input type="text" value="0" readonly class="number disabled"/></td>
						<td><input type="text" value="0.00" readonly class="float disabled"/></td>
					</tr>
					<tr>
						<td>BDL</td>
												<td><input type="text" value="0" readonly class="number" style=""/></td>
						<td><input type="text" value="0" readonly class="number" id="tot_bdl_enviado__text"/></td>
						<td><input type="text" value="0.00" readonly class="float" id="vlr_bdl_enviado__text"/></td>
					</tr>
					<tr>
						<td>Cheque</td>
						<td/>
						<td><input type="text" value="0" readonly class="number disabled"/></td>
						<td></td>
					</tr>
					<tr>
						<td>BCO</td>
						<td><input type="text" value="0" readonly class="number" style=""/></td>
						<td><input type="text" value="0" readonly class="number" id="tot_bco_enviado__text"/></td>
						<td><input type="text" value="0.00" readonly class="float" id="vlr_bco_enviado__text"/></td>
					</tr>
					<tr>
						<td>Depósito</td>
						<td/>
						<td><input type="text" value="0" readonly class="number disabled"/></td>
						<td></td>
					</tr>
					<tr>
						<td>Folha</td>
						<td/>
						<td><input type="text" value="" readonly class="number disabled"/></td>
						<td><input type="text" value="" readonly class="float disabled"/></td>
					</tr>
					<tr>
						<td>Gerados</td>
						<td><input type="text" value="0" readonly class="number" title="Emails gerados para envio"/></td>
						<td/>
						<td/>
					</tr>
				</tbody></table>

	</div></td></tr></table>

	<br/>

	<br/>

	<div class="contr-cmd">
		<input type="button" onclick="esta.gerar_cobranca_1ro__click()" class="botao" value="Gerar" id="gerar_button" disabled />
	
		<input type="button" onclick="esta.listar_gerados_1ro__click()" class="botao" value="Listar Gerados" id="listar_button" disabled />
	
		<input type="button" disabled onclick="ver_lista_sem_email();" class="botao" value="Listar Sem Email" id="listar_sem_email_button"/>
	
		<input type="button" onclick="esta.enviar_email_1ro__click()" class="botao" value="Enviar Emails" id="enviar_button" disabled />
	
		<div><hr/><table align="center" class="contr-err"><tbody><tr><td><b>Inconsistências</b><br/><br/>Não existe primeiro pagamento para esta competência<br/>Não existe geração de primeiro pagamento para esta competência<br/></td></tr></tbody></table></div>
	</div>
<?
echo form_end_box("contribuicao", FALSE);
echo aba_end();

$this->load->view("footer");
?>