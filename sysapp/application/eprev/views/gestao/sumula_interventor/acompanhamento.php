<?php
set_title('Súmulas Interventor - Cadastro');
$this->load->view('header');
?>

<script>
<?php
echo form_default_js_submit(Array('descricao'));
?>

function ir_lista()
{
	location.href='<?php echo site_url("gestao/sumula_interventor"); ?>';
}

function ir_responsabilidade()
{
	location.href='<?php echo site_url("gestao/sumula_interventor/responsabilidade/".$cd_sumula_interventor); ?>';
}

function ir_cadastro()
{
	location.href='<?php echo site_url("gestao/sumula_interventor/cadastro/".$cd_sumula_interventor); ?>';
}

function load()
{
	$('#result_div').html("<?php echo loader_html(); ?>");

	$.post( '<?php echo site_url('gestao/sumula_interventor/listar_acompanhamento'); ?>/',
	{
		cd_sumula_interventor : $('#cd_sumula_interventor').val()
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
	ob_resul.sort(0, true);
}

function excluir(cd_sumula_interventor_acompanhamento)
{
	var confirmacao = 'Deseja excluir o acompanhamento?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
	
	if(confirm(confirmacao))
	{
		location.href='<?php echo site_url("gestao/sumula_interventor/excluir_acompanhamento/".$cd_sumula_interventor); ?>/' + cd_sumula_interventor_acompanhamento;
	}
}

function listar()
{
	load();
}

$(function(){
	 listar();
})
	
</script>

<?php

$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_lista', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Responsabilidade', FALSE, 'ir_responsabilidade();');
$abas[] = array('aba_nc', 'Acompanhamento', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open('gestao/sumula_interventor/salvar_acompanhamento', 'name="filter_bar_form"');
		echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_sumula_interventor', '', $cd_sumula_interventor);
			echo form_default_textarea('descricao', 'Descrição : *', '', "style='height:100px;'");
			echo form_default_dropdown('cd_sumula_interventor_item', 'Item :', $arr_item);
			echo form_default_row('','','<i style="font-size:90%">Informe quando acompanhamento se refere a um item da súmula.<br/></i>');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();         
            echo button_save("Salvar");
        echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
    echo br(3);	

echo aba_end();

$this->load->view('footer_interna');
?>