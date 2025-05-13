<?php
set_title('Resumo do Protocolo Interno');
$this->load->view('header');
?>
<script>
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/cadastro_protocolo_interno"); ?>';
	}
	
	function ir_relatorio()
	{
		location.href="<?php echo site_url('ecrm/cadastro_protocolo_interno/relatorio'); ?>";
	}
	
	function filtrar()
    {
        load();
    }

    function load()
    {
		$('#result_div').html("<?php echo loader_html(); ?>");

        $.post( '<?php echo site_url('/ecrm/cadastro_protocolo_interno/resumo_lista');?>',
		{
            cd_tipo_doc           : $('#cd_tipo_doc').val(),
			dt_envio_inicio       : $('#dt_envio_inicio').val(),
			dt_envio_fim          : $('#dt_envio_fim').val(),
			dt_recebimento_inicio : $('#dt_recebimento_inicio').val(),
			dt_recebimento_fim    : $('#dt_recebimento_fim').val(),
			cd_usuario_envio      : $('#cd_usuario_envio').val(),
			cd_usuario_destino    : $('#cd_usuario_destino').val(), 
			cd_usuario_encerrado  : $('#cd_usuario_encerrado').val()
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
			'Number',
            'CaseInsensitiveString', 
            'Number'
        ]);
        ob_resul.onsort = function()
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
	
	$(function(){
        filtrar();
    });
	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', false, 'ir_lista();');
$abas[] = array('aba_relatorio', 'Relatório', false, 'ir_relatorio();');
$abas[] = array('aba_resumo', 'Resumo', true, 'void(0);');

echo aba_start($abas);
	echo form_list_command_bar();
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo form_default_tipo_documento();
        echo filter_date_interval('dt_envio_inicio', 'dt_envio_fim', 'Data de envio:',calcular_data('','1 month'), date('d/m/Y'));
        echo filter_date_interval('dt_recebimento_inicio', 'dt_recebimento_fim', 'Data de recebimento:');
        echo filter_dropdown('cd_usuario_envio', 'Remetente:', $usuario_envio_dd);
        echo filter_dropdown('cd_usuario_destino', 'Destino:', $usuario_destino_dd);
        echo filter_dropdown('cd_usuario_encerrado', 'Encerrado por:', $usuario_encerrado_dd);
	echo form_end_box_filter();
	echo '<div id="result_div"></div>';
echo aba_end();
$this->load->view('footer'); 
?>