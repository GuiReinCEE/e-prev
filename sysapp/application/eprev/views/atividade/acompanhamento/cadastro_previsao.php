<?php
set_title('Acompanhamento de Projetos - Previsão');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('mes', 'ano'),'valida_mes()');
	?>
	
	function valida_mes()
	{
		$.post('<?php echo site_url('/atividade/acompanhamento/previsao_valida_mes'); ?>',
		{
			cd_acomp    : $('#cd_acomp').val(),
			ano         : $('#ano').val(),
			mes         : $('#mes').val(),
			cd_previsao : $('#cd_previsao').val()
		},
		function(data)
		{
			if(data == 0)
			{
				$('form').submit();
			}
			else
			{
				alert('Mês / Ano da previsão já existe');
				return false;
			}
		}, 'html');
	}
	
	function ir_lista()
	{
		location.href='<?php echo site_url("atividade/acompanhamento"); ?>';
	}
	
	function ir_cadastro()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/cadastro/".intval($row['cd_acomp'])); ?>';
	}	
	
	function ir_etapa()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/etapa/".intval($row['cd_acomp'])); ?>';
	}

	function ir_reuniao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/reuniao/".intval($row['cd_acomp'])); ?>';
	}	
	
	function ir_previsao()
	{
		location.href='<?php echo site_url("atividade/acompanhamento/previsao/".intval($row['cd_acomp'])); ?>';
	}	
	
	function imprimir()
    {
        window.open('<?php echo site_url("atividade/acompanhamento/imprimir_previsao/".intval($row['cd_acomp'])."/".$row_previsao['cd_previsao']); ?>');
    }
	
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Acompanhamento', FALSE, 'ir_cadastro();');
$abas[] = array('aba_reuniao', 'Reuniões', FALSE, 'ir_reuniao();');
$abas[] = array('aba_etapas', 'Etapas', FALSE, 'ir_etapa();');
$abas[] = array('aba_previsao', 'Previsão', FALSE, 'ir_previsao();');	
$abas[] = array('aba_previsao', 'Cadastro', TRUE, 'location.reload();');

$status = "Projeto em andamento";
$cor_status = "blue";

if (trim($row['dt_encerramento']) != '') 
{
	$status = 'Projeto encerrado em: '. $row['dt_encerramento'];
	$cor_status = "red";
}	

if (trim($row['dt_cancelamento']) != '') 
{
	$status = 'Projeto cancelado em: '. $row['dt_cancelamento'];
	$cor_status = "red";
}

echo aba_start( $abas );
	echo form_open('atividade/acompanhamento/salvar_previsao');
		echo form_start_box( "default_box", "Acompanhamento" );
			echo form_default_hidden('cd_acomp', '', $row);
			echo form_default_hidden('cd_previsao', '', $row_previsao);
			echo form_default_text('cd_acomp_h', "Código :", intval($row['cd_acomp']), "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('ds_projeto', "Projeto :", $row, "style='width:100%;border: 0px;' readonly" );
			echo form_default_text('status', "Status :", $status, "style='color: ".$cor_status."; font-weight:bold; width:400px;border: 0px;' readonly" );	
		echo form_end_box("default_box");
		echo form_start_box( "default_previsao_box", "Previsão" );
			echo form_default_mes_ano('mes', 'ano', 'Mês / Ano :*', $row_previsao['mes_ano']);
			echo form_default_textarea('descricao', 'Previsão : ', $row_previsao, 'style="height:100px;"');
			echo form_default_textarea('obs', 'Observação : ', $row_previsao, 'style="height:100px;"');
		echo form_end_box("default_previsao_box");
		echo form_command_bar_detail_start();
			if ((trim($row['dt_encerramento']) == '') and (trim($row['dt_cancelamento']) == ''))
			{
				echo button_save("Salvar");
			}
			
			if(intval($row_previsao['cd_previsao']) > 0)
			{
				echo button_save("Imprimir", 'imprimir();', 'botao_disabled');
			}
		echo form_command_bar_detail_end();	
	echo form_close();	
echo aba_end();

$this->load->view('footer_interna');
?>