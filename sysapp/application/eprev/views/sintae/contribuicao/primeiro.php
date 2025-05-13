<?php
set_title("SINTAE / Contribuição Normal Primeiro Pgto");
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
	function ver_lista_sem_email()
	{
		$('#contribuicao').hide();
		$('#participantes_sem_email').show();
	}
	function ver_contribuicao()
	{
		$('#contribuicao').show();
		$('#participantes_sem_email').hide();
	}
//-->
</script>
<?php
$abas[0] = array('aba_pri_pg', 'PRIMEIRO PAGAMENTO', TRUE, 'cpp(this)');
$abas[1] = array('aba_men_pg', 'PAGAMENTO MENSAL', FALSE, 'pm(this)');

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
				<td><input type="text" value="<?php echo $inscricao['total']['quantidade']; ?>" readonly class="disabled"/></td>
				<!--    -->
				<td/>
			</tr>
			<tr>
				<td>BDL</td>
				<td><input type="text" value="<?php echo $inscricao['bdl']['quantidade']; ?>" readonly class="number"/></td>
				<td/>
			</tr>
			<tr>
				<td>Cheque</td>
				<td><input type="text" value="<?php echo $inscricao['cheque']['quantidade']; ?>" readonly class="disabled"/></td>
				<td><input type="text" value="<?php echo $inscricao['cheque']['valor']; ?>" readonly class="disabled"/></td>
			</tr>
			<tr>
				<td>BCO</td>
				<td><input type="text" value="<?php echo $inscricao['bco']['quantidade']; ?>" readonly class="number"/></td>
				<td><input type="text" value="<?php echo $inscricao['bco']['valor']; ?>" readonly /></td>
			</tr>
			<tr>
				<td>Depósito</td>
				<td><input type="text" value="<?php echo $inscricao['deposito']['quantidade']; ?>" readonly class="disabled"/></td>
				<td><input type="text" value="<?php echo $inscricao['deposito']['valor']; ?>" readonly class="disabled"/></td>
			</tr>
			<tr>
				<td>Folha</td>
				<td><input type="text" value="<?php echo $inscricao['folha']['quantidade']; ?>" readonly class="disabled"/></td>
				<td><input type="text" value="<?php echo $inscricao['folha']['valor']; ?>" readonly class="disabled"/></td>
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
				<td><input type="text" value="<?php echo $geracao['geral']['quantidade']; ?>" readonly class="disabled"/></td>
				<td><input type="text" value="<?php echo $geracao['geral']['valor']; ?>" readonly class="disabled"/></td>
			</tr>
			<tr>
				<td>BDL</td>
				<td><input type="text" value="<?php echo $geracao['bdl']['quantidade']; ?>" readonly class="number"/></td>
				<td></td>
			</tr>
			<tr>
				<td>Cheque</td>
				<td><input type="text" value="<?php echo $geracao['cheque']['quantidade']; ?>" readonly class="disabled"/></td>
				<td></td>
			</tr>
			<tr>
				<td>BCO</td>
				<td><input type="text" value="<?php echo $geracao['bco']['quantidade']; ?>" readonly class="number"/></td>
				<td><input type="text" value="<?php echo $geracao['bco']['valor']; ?>" readonly /></td>
			</tr>
			<tr>
				<td>Depósito</td>
				<td><input type="text" value="<?php echo $geracao['deposito']['quantidade']; ?>" readonly class="disabled"/></td>
				<td></td>
			</tr>
			<tr>
				<td>Folha</td>
				<td><input type="text" value="<?php echo $geracao['folha']['quantidade']; ?>" readonly class="disabled"/></td>
				<td><input type="text" value="<?php echo $geracao['folha']['valor']; ?>" readonly class="disabled"/></td>
			</tr>
		</tbody></table>
	</div></td><td valign="top">

	<div class="contr-box">

		<b>Envio de Cobrança - GF</b>

		<table>
		<tbody>
			<tr>
				<td/>
				<td align="center">Emails</td>
				<td align="center">Qtd</td>
				<td align="center">Vlr</td>
			</tr>
			<tr>
				<td>Geral</td>
				<td/>
				<td><input type="text" value="<?php echo $envio['geral']['quantidade']; ?>" readonly class="disabled"/></td>
				<td><input type="text" value="<?php echo $envio['geral']['valor']; ?>" readonly class="disabled"/></td>
			</tr>
			<tr>
				<td>BDL</td>
				<td><input type="text" value="<?php echo $envio['bdl']['email']; ?>" readonly class="number" style=""/></td>
				<td><input type="text" value="<?php echo $envio['bdl']['quantidade']; ?>" readonly id="tot_bdl_enviado__text"/></td>
				<td><input type="text" value="<?php echo $envio['geral']['valor']; ?>" readonly id="vlr_bdl_enviado__text"/></td>
			</tr>
			<tr>
				<td>Cheque</td>
				<td/>
				<td><input type="text" value="<?php echo $envio['cheque']['quantidade']; ?>" readonly class="disabled"/></td>
				<td></td>
			</tr>
			<tr>
				<td>BCO</td>
				<td><input type="text" value="<?php echo $envio['bco']['email']; ?>" readonly class="number" style=""/></td>
				<td><input type="text" value="<?php echo $envio['bco']['quantidade']; ?>" readonly id="tot_bco_enviado__text"/></td>
				<td><input type="text" value="<?php echo $envio['bco']['valor']; ?>" readonly id="vlr_bco_enviado__text"/></td>
			</tr>
			<tr>
				<td>Depósito</td>
				<td/>
				<td><input type="text" value="<?php echo $envio['deposito']['quantidade']; ?>" readonly class="disabled"/></td>
				<td></td>
			</tr>
			<tr>
				<td>Folha</td>
				<td/>
				<td><input type="text" value="<?php echo $envio['folha']['quantidade']; ?>" readonly class="disabled"/></td>
				<td><input type="text" value="<?php echo $envio['folha']['valor']; ?>" readonly class="disabled"/></td>
			</tr>
			<tr>
				<td>Gerados</td>
				<td><input type="text" value="<?php echo $envio['gerados']['email']; ?>" readonly class="number" title="Emails gerados para envio"/></td>
				<td/>
				<td/>
			</tr>
		</tbody>
		</table>

	</div>

	</td>
	</tr>
	</table>

	<br/>
	<br/>

	<div class="contr-cmd">

		<input type="button" onclick="esta.gerar_cobranca_1ro__click()" class="botao" value="Gerar" id="gerar_button" <?php if(!$comandos['gerar']) echo "disabled"; ?> />

		<input type="button" onclick="esta.listar_gerados_1ro__click()" class="botao" value="Listar Gerados" id="listar_button" <?php if(!$comandos['listar_gerados']) echo "disabled"; ?> />

		<input type="button" onclick="ver_lista_sem_email();" class="botao" value="Listar Sem Email" id="listar_sem_email_button" <?php if(!$comandos['listar_gerados_sem_email']) echo "disabled"; ?> />

		<input type="button" onclick="esta.enviar_email_1ro__click()" class="botao" value="Enviar Emails" id="enviar_button" disabled />

		<?php if( sizeof($incons)>0 ): ?>
			<hr>
			<div><table align="center" class="contr-err"><tbody><tr><td>

					<b>Inconsistências</b><br/><br/>
					<?php foreach($incons as $item): ?>
						<?php echo $item; ?><br />
					<?php endforeach; ?>

			</td></tr></tbody></table></div>
		<?php endif; ?>

	</div>

<?php
echo form_end_box("contribuicao", FALSE);

// BOX : lista participantes sem email!
echo form_start_box("participantes_sem_email", "Participantes sem Email", FALSE);

	foreach($participantes_sem_email as $item)
	{
		echo $item['nome'].'<br />';
	}
	echo form_command_bar_detail_start();
    echo form_command_bar_detail_button("Voltar", "ver_contribuicao()");
	echo form_command_bar_detail_end();

echo form_end_box("participantes_sem_email", FALSE);
// BOX : lista participantes sem email!

echo aba_end();

$this->load->view("footer");
?>