<?php
set_title('Usuário e-prev: Liberar horário');
$this->load->view('header');
?>

<script>
    function filtrar()
    {

		$('#result_div').html("<?php echo loader_html(); ?>");

		$.post('<?php echo site_url("servico/usuario_horario/listar"); ?>',
		{
			cd_usuario_gerencia : $('#cd_usuario_gerencia').val(),
			cd_usuario          : $('#cd_usuario').val()
		},
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});

    }
    
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'DateBR',
            'CaseInsensitiveString',
            'CaseInsensitiveString'
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
        ob_resul.sort(3, true);
    }
	
	function novo()
	{
		location.href = "<?= site_url('servico/usuario_horario/cadastro') ?>";
	}	
	
	$(function(){
		filtrar();
	});	
</script>

<?php
$abas[] = array('aba_lista', 'Horário - Liberado', TRUE, 'location.reload();');

$config['button'][] = array('Nova Liberação', 'novo();');

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
		echo form_default_usuario_ajax('cd_usuario');
    echo form_end_box_filter();
    echo '<div id="result_div"></div>'.br();
echo aba_end();
$this->load->view('footer'); 
?>