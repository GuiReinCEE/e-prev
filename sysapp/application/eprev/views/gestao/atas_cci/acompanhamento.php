<?php
set_title('Atas CCI - Acompanhamento');
$this->load->view('header');
?>

<script>
<?php
echo form_default_js_submit(Array('descricao'));
?>

function ir_lista()
{
	location.href='<?php echo site_url("gestao/atas_cci"); ?>';
}

function ir_cadastro()
{
	location.href='<?php echo site_url("gestao/atas_cci/cadastro/".$cd_atas_cci); ?>';
}

function filtrar()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post( '<?php echo site_url('gestao/atas_cci/listar_acompanhamento'); ?>/',
	{
		cd_atas_cci : $('#cd_atas_cci').val()
	},
	function(data)
	{
		$('#result_div').html(data);
		configure_result_table();
	});
}

function configure_result_table()
{
	var ob_resul = new SortableTable(document.getElementById("table-1"),[
		"DateTimeBR",
		"CaseInsensitiveString",
		"CaseInsensitiveString",
		null
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
	ob_resul.sort(0, true);
}

function excluir(cd_atas_cci_acompanhamento)
{
	var confirmacao = 'Deseja excluir o acompanhamento?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para N�o\n\n';
	
	if(confirm(confirmacao))
	{
		location.href='<?php echo site_url("gestao/atas_cci/excluir_acompanhamento/".$cd_atas_cci); ?>/' + cd_atas_cci_acompanhamento;
	}
}

function editar(cd_atas_cci_acompanhamento)
{
	$.post( '<?php echo site_url('gestao/atas_cci/carrega_acompanhamento'); ?>/',
	{
		cd_atas_cci_acompanhamento : cd_atas_cci_acompanhamento
	},
	function(data)
	{
		if(data)
		{
			$('#descricao').val(data.descricao);
			$('#cd_atas_cci_acompanhamento').val(cd_atas_cci_acompanhamento);
			
			$('#btn_cancelar').show();
		}
	}, 'json');
}

function cancelar()
{
	$('#descricao').val('');
	$('#cd_atas_cci_acompanhamento').val(0);
	$('#btn_cancelar').hide();
}

$(function(){
	filtrar();
	$('#btn_cancelar').hide();
})
	
</script>

<?php

$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_nc', 'Acompanhamento', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open('gestao/atas_cci/salvar_acompanhamento', 'name="filter_bar_form"');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_atas_cci', '', $cd_atas_cci);
			echo form_default_hidden('cd_atas_cci_acompanhamento', '', 0);
			echo form_default_textarea('descricao', 'Descri��o : *', '', "style='height:100px;'");
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();         
            echo button_save("Salvar");
			echo button_save("Cancelar", 'cancelar();', 'botao_disabled', 'id="btn_cancelar"');
        echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
    echo br(5);	

echo aba_end();

$this->load->view('footer_interna');
?>