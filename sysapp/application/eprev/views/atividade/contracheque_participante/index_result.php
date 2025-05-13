<?php
	#echo "<PRE>".print_r($ar_resumo,true)."</PRE>";
	
	if(count($ar_resumo) == 0)
	{
		echo "
				<br><br><br>
				<center>
					<h1 style='font-family: Calibri, Arial; font-size: 15pt; color:red;'>
						Não há informação para esta Data de Pagamento
					</h1>
				</center>
				<br><br><br>
			 ";		
		exit;
	}
	
	if((!isset($ar_libera)) or (!isset($ar_libera["fl_libera"])))
	{
		echo "
				<br><br><br>
				<center>
					<h1 style='font-family: Calibri, Arial; font-size: 15pt; color:red;'>
						Não há informação para esta Data de Pagamento
					</h1>
				</center>
				<br><br><br>
			 ";		
		exit;	
	}	
	
	if($ar_libera["fl_libera"] == "N")
	{
		echo "
				<br><br><br>
				<center>
					<h1 style='font-family: Calibri, Arial; font-size: 15pt; color:red;'>
						Você só pode enviar o contracheque a partir de ".$ar_libera["dt_libera"]."
					</h1>
				</center>
				<br><br><br>
			 ";		
		exit;	
	}

	if((!$bt_gerar) and (!$bt_envia_email) and (trim($dt_envio_email) != ""))
	{
		echo "
				<br>
				<center>
					<h1 style='font-family: Calibri, Arial; font-size: 15pt; color:blue;'>
						Contracheque enviado em ".trim($dt_envio_email)." por ".trim($ds_envio_email)."
					</h1>
				</center>

			 ";		
	}
?>
<BR>
<?php echo $dt_pagamento; ?>
<table border="0" align="center" cellspacing="20">
	<tr style="height: 30px;">
		<td style="<? echo ($bt_gerar == true ? "" : "display:none;"); ?>">
			<input type="button" value="Gerar" onclick="gerar();" class="botao_verde" style="width: 120px;">
		</td>
		<td style="<? echo ($bt_envia_email == true ? "" : "display:none;"); ?>">
			<input type="button" value="Enviar Emails" onclick="enviar_email();" class="botao_vermelho" style="width: 120px;">
		</td>	
	</tr>
</table>
<script>
	function gerar()
	{
		var confirmacao = 'Confirma a geração para a data de pagamento <?php echo $dt_pagamento; ?>?\n\n'+
						  'Clique [Ok] para Sim\n\n'+
						  'Clique [Cancelar] para Não\n\n';
						  
		if(confirm(confirmacao))
		{		
			$('#result_div').html("<?php echo loader_html(); ?>");

			$.post('<?php echo site_url('atividade/contracheque_participante/gerar'); ?>',
			{
				dt_pagamento : "<?php echo $dt_pagamento; ?>"
			},
			function(data)
			{
				$('#result_div').html(data);
			});		
		}
	}
	
	function enviar_email()
	{
		var confirmacao = 'ATENÇÃO\n\nConfirma o ENVIO DE EMAILS para a data de pagamento <?php echo $dt_pagamento; ?>?\n\n'+
						  'Clique [Ok] para Sim\n\n'+
						  'Clique [Cancelar] para Não\n\n';
						  
		if(confirm(confirmacao))
		{		
			$('#result_div').html("<?php echo loader_html(); ?>");

			$.post('<?php echo site_url('atividade/contracheque_participante/enviarEmail'); ?>',
			{
				dt_pagamento : "<?php echo $dt_pagamento; ?>"
			},
			function(data)
			{
				$('#result_div').html(data);
			});		
		}
	}	
</script>
	
<?php
	$body = array();
	$head = array("Folha","Descrição","Qt Participantes","Qt Com Email");

	foreach($ar_resumo as $item)
	{
		$body[] = array(
			$item["tipo_folha"],
			array($item["descricao_folha"],'text-align:left;'),
			array($item["qt_total"],'text-align:right;','int'),
			array($item["qt_email"],'text-align:right;','int')
	   );
	}
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;
	echo '<center><div style="width: 40%;">'.$grid->render().'</div></center>';
	
	echo br(5);
?>
