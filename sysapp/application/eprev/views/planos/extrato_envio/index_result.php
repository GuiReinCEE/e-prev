<?php
$ar_plano   = array(2,6,7,8,9,21,22,23);
$ar_empresa = array(0,6,7,8,9,10,12,19,20,21,22,23);

if(!((in_array($cd_plano, $ar_plano)) and (in_array($cd_empresa, $ar_empresa))))
{
	echo br(2).'<span class="label label-important">Empresa/Plano não tem autorização para enviar e-mail</span>';
	exit;
}
	
	
echo form_open('planos/extrato_envio/enviar', array('id' => 'formParticipanteExtrato'));	
?>  
<BR><BR>
<style>
	.ci_financeiro hr {
		border-width: 0;
		height: 1px;
		border-top-width: 1px;
		border-top-color: gray;
		border-top-style: dashed;
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
<table width="400" border="0" cellspacing="5" class="ci_financeiro">
	<caption>Dados para envio</caption>
	<tr>
		<td>Plano:</td>
		<td align="right">
			<input type="text" value="<?php echo $cd_plano; ?>" name="r_cd_plano" id="r_cd_plano" readonly style="text-align:right; width: 90px;">
		</td>					
	</tr>
	<tr>
		<td>Empresa:</td>
		<td align="right">
			<input type="text" value="<?php echo $cd_empresa; ?>" name="r_cd_empresa" id="r_cd_empresa" readonly style="text-align:right; width: 90px;">
		</td>				
	</tr>
	<tr>
		<td>Mês:</td>
		<td align="right">
			<input type="text" value="<?php echo $nr_mes; ?>" name="r_nr_mes" id="r_nr_mes" readonly style="text-align:right; width: 90px;">
		</td>				
	</tr>	
	<tr>
		<td>Ano:</td>
		<td align="right">
			<input type="text" value="<?php echo $nr_ano; ?>" name="r_nr_ano" id="r_nr_ano" readonly style="text-align:right; width: 90px;">
		</td>				
	</tr>
	<tr>
		<td>Nr Extrato:</td>
		<td align="right">
			<input type="text" value="<?php echo $nr_extrato; ?>" name="r_nr_extrato" id="r_nr_extrato" readonly style="text-align:right; width: 90px;">
		</td>				
	</tr>	
	<tr>
		<td>Dt Envio:</td>
		<td align="right">
			<input type="text" value="<?php echo $dt_envio; ?>" name="r_dt_envio" id="r_dt_envio" readonly style="text-align:right; width: 90px;">
		</td>				
	</tr>	
	<tr>
		<td colspan="3"><hr></td>
	</tr>		
	<tr>
		<td style="white-space:nowrap;">Quantidade total</td>
		<td class="destaca" align="right">
			<input type="text" value="<?php echo count($ar_lista_total); ?>" name="r_qt_total" id="r_qt_total" readonly style="text-align:right; width: 90px;">
		</td>					
	</tr>	
	<tr>
		<td style="white-space:nowrap;">Quantidade COM E-mail</td>
		<td class="destaca" align="right">
			<input type="text" value="<?php echo count($ar_lista); ?>" name="r_qt_total" id="r_qt_total" readonly style="text-align:right; width: 90px;">
		</td>					
	</tr>
	<tr>
		<td colspan="3"><hr></td>
	</tr>
	<tr>
		<td>Dt Geração:</td>
		<td align="right">
			<span class="label"><?php echo $ar_controle["dt_gerado"]; ?></span>
		</td>				
	</tr>
	<tr>
		<td>Dt Envio E-mail:</td>
		<td align="right">
			<span class="label label-warning"><?php echo $ar_controle["dt_enviado"]; ?></span>
		</td>				
	</tr>
	<tr>
		<td colspan="3"><hr></td>
	</tr>
	<tr>
		<td>Dt Envio Agendado:</td>
		<td align="right">
			<span class="label label-inverse"><?php echo $ar_email["dt_agendado"]; ?></span>
		</td>				
	</tr>	
	<tr>
		<td>E-mails aguardando envio:</td>
		<td align="right">
			<span class="label label-success"><?php echo $ar_email["qt_aguardando"]; ?></span>
		</td>				
	</tr>
	<tr>
		<td>E-mails enviados:</td>
		<td align="right">
			<span class="label label-info"><?php echo $ar_email["qt_enviado"]; ?></span>
		</td>				
	</tr>	
	<tr>
		<td>E-mails não enviados:</td>
		<td align="right">
			<span class="label label-important"><?php echo $ar_email["qt_enviado_nao"]; ?></span>
		</td>				
	</tr>		
</table>
<?php
	if($ar_controle['fl_gerado'] == "N")
	{
		foreach($ar_lista as $item)
		{
			echo form_hidden(array('name' => 'cd_participante[]', 'value' => $item["re_cripto"]));
		}
	}
echo form_close();

$body = array();
$head = array( 
	"RE",
	"Nome"
);

$this->load->helper("grid");
$grid = new grid();
$grid->head = $head;

if($ar_controle['fl_gerado'] == "N")
{
	//echo br(1).button_save("Enviar e-mail(s)",'enviarEmail()',"botao_vermelho");

	foreach($ar_lista_sem_email as $item)
	{
		$body[] = array(
			$item["cd_empresa"].'/'.$item["cd_registro_empregado"].'/'.$item["seq_dependencia"],
			array($item['nome'], 'text-align:left')
		);	
	}
	
	$grid->body = $body;

	echo form_start_box("default_box", "Participantes sem Email");
		echo $grid->render();
	echo form_end_box("default_box");
}
else
{
	foreach($ar_lista as $item)
	{
		$body[] = array(
			$item["cd_empresa"].'/'.$item["cd_registro_empregado"].'/'.$item["seq_dependencia"],
			array($item['nome'], 'text-align:left')
		);
	}
	
	$grid->body = $body;

	echo form_start_box("default_box", "Extratos Enviados - Participantes com Email");
		echo $grid->render();
	echo form_end_box("default_box");
}
echo br(5);
?>