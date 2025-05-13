<?php
set_title('Cenário Legal - Responsáveis');
$this->load->view('header');
?>
<script>
<?php echo form_default_js_submit(array("ds_empresa" ));?>
function filtrar()
{
	$("#result_div").html("<?php echo loader_html(); ?>");
	
    $.post( '<?php echo site_url('gestao/cenario_legal_responsavel/listar');?>',
	$("#filter_bar_form").serialize(),
    function(data)
    {
		$("#result_div").html(data);
        configure_result_table();
    });
}

function configure_result_table()
{
    var ob_resul = new SortableTable(document.getElementById("table-1"),
    [
		'CaseInsensitiveString',
		'CaseInsensitiveString',
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
    ob_resul.sort(0, false);
}

function carrega_usuario(cd_gerencia)
{
	$.post('<?php echo site_url('gestao/cenario_legal_responsavel/carrega_usuario') ?>', 
	{
		cd_gerencia : cd_gerencia
	},
	function (data){ 
	
		var select = $('#cd_usuario');
				
		if(select.prop) {
			var options = select.prop('options');
		}
		else 
		{
			var options = select.attr('options');
		}
		
		$('option', select).remove();
		
		options[options.length] = new Option('Selecione', '');
		$.each(data, function(val, text) {
			options[options.length] = new Option(text.text, text.value);
		});
	
	}, 'json');
}

function remover(cd_usuario)
{
	if(confirm('Deseja remover o Responsável.'))
	{
		$.post('<?php echo site_url('gestao/cenario_legal_responsavel/remover') ?>', 
		{
			cd_usuario : cd_usuario
		},
		function (data){ 
			filtrar();
		});
	}
}


$(function(){
	filtrar();
})

</script>
<?php
$abas[] = array('aba_lista', 'Responsáveis', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open('gestao/cenario_legal_responsavel/salvar');
		echo form_start_box("default_box", "Cadastro");
			echo form_default_dropdown('cd_gerencia', 'Gerência :*', $arr_gerencia, array(), 'onchange="carrega_usuario($(this).val())"');
			echo form_default_dropdown('cd_usuario', 'Usuário :*', array());
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();
			echo button_save("Salvar");
		echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
	echo br();
echo aba_end();
$this->load->view('footer'); 
?>