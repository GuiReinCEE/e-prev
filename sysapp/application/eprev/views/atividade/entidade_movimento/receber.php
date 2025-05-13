<?php
set_title('Entidade - Movimento');
$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/entidade_movimento"); ?>';
	}
	
	function ir_retorno()
	{
		location.href='<?php echo site_url("atividade/entidade_movimento/retorno/".$row['cd_movimento']); ?>';
	}
	
	function receber()
	{
		var confirmacao = 'Deseja receber o movimento?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
		
		if(confirm(confirmacao))
		{
			location.href='<?php echo site_url('atividade/entidade_movimento/salvar_recebimento/'.$row['cd_movimento']); ?>';
		}
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Movimento', TRUE, 'location.reload();');

if(trim($row['dt_recebido']) != '')
{
	$abas[] = array('aba_lista', 'Retorno', FALSE, 'ir_retorno();');
}

$body = array();
$head = array(
  'Anexo',
  'Dt Inclusão'
);

foreach ($collection as $item)
{            
    $body[] = array(
	    anchor('http://'.$_SERVER['SERVER_NAME'].'/eletroceee/app/up/entidade/'.$item['arquivo'], $item['arquivo_nome'] , array('target' => "_blank")),
		$item['dt_inclusao']
    );
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start( $abas );
	echo form_start_box( "default_box", "Movimento" );
		echo form_default_hidden('cd_movimento', "", $row);	
		echo form_default_text('nr_ano_numero', "Ano/Número :", $row, 'style="font-weight: bold; width:350px;border: 0px;" readonly' );
		echo form_default_text('ds_entidade', "Entidade :", $row, 'style="font-weight: bold; width:350px;border: 0px;" readonly' );
		echo form_default_text('nr_mes_nr_ano', "Mês/Ano Ref :", $row['mes_referencia'].'/'.$row['ano_referencia'], 'style="font-weight: bold; width:350px;border: 0px;" readonly' );
		echo form_default_text('dt_envio', "Dt. Envio :", $row, 'style="font-weight: bold; width:350px;border: 0px;" readonly' );
		echo form_default_text('dt_recebido', "Dt. Recebido :", $row, 'style="font-weight: bold; width:350px;border: 0px;" readonly' );
		echo form_default_text('dt_retorno', "Dt. Retorno :", $row, 'style="font-weight: bold; width:350px;border: 0px;" readonly' );
	echo form_end_box("default_box");
	echo form_command_bar_detail_start();
		if(trim($row['dt_recebido']) == '')
		{
			echo button_save("Receber", "receber();", "botao_verde");	
		}
	echo form_command_bar_detail_end();
	echo $grid->render();
	echo br();
echo aba_end();
$this->load->view('footer_interna');
?>