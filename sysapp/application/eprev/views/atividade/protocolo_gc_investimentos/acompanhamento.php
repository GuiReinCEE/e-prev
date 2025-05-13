<?php
	set_title('Controle de Documentos GC Investimento');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('acompanhamento')); ?>

	function ir_lista()
	{
		location.href = "<?= site_url('atividade/protocolo_gc_investimentos') ?>";
	}
	
    function ir_cadastro()
	{
		location.href = "<?= site_url('atividade/protocolo_gc_investimentos/cadastro/'.$row['cd_protocolo_gc_investimentos']) ?>";
	}
    
    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            "CaseInsensitiveString",
            "CaseInsensitiveString",
            "DateTimeBR"
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
        ob_resul.sort(2, true);
    }
    
    $(function(){
        configure_result_table();
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_cadastro', 'Acompanhamento', TRUE, 'location.reload();');


	$head = array( 
	    'Acompanhamento',
	    'Usuário',
	    'Dt. Inclusão'
	);

	$body = array();

	foreach($collection as $item)
	{
	    $body[] = array(
	        array(nl2br($item['acompanhamento']), 'text-align:justify'),
	        array($item['ds_usuario'], 'text-align:left'),
	        $item['dt_inclusao']
	    );
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;
	
	echo aba_start($abas);
		echo form_open('atividade/protocolo_gc_investimentos/salvar_acompanhamento');
			echo form_start_box("default_box", "Cadastro");
				echo form_default_hidden('cd_protocolo_gc_investimentos', '', $row);	
				echo form_default_row('', 'Documento:', $row['documento']);
				echo form_default_textarea('observacao', 'Observação :', $row, 'style="border: 0px;" readonly');
				
				if(trim($row['dt_envio_gc']) != '')
				{
					echo form_default_row('dt_envio_gc', 'Dt Envio:', $row['dt_envio_gc']);
					echo form_default_row('ds_usuario_envio_gc', 'Envio por:', $row['ds_usuario_envio_gc']);
				}
				
				if(trim($row['dt_recebido']) != '')
				{
					echo form_default_row('dt_recebido', 'Dt Recebido:', $row['dt_recebido']);
					echo form_default_row('ds_usuario_recebido', 'Recebido por:', $row['ds_usuario_recebido']);
					echo form_default_row('dt_envio_sg', 'Dt Envio SG:', $row['dt_envio_sg']);
					echo form_default_row('cd_gerencia_sg', 'Gerência:', $row['cd_gerencia_sg']);
					echo form_default_row('ds_usuario_sg', 'Usuário:', $row['ds_usuario_sg']);
					echo form_default_row('dt_expedicao', 'Dt Expedição:', $row['dt_expedicao']);
				}
				
				if(trim($row['dt_recusado']) != '')
				{
					echo form_default_row('dt_recusado', 'Dt Recusado:', $row['dt_recusado']);
					echo form_default_row('ds_usuario_recusado', 'Recusado por:', $row['ds_usuario_recusado']);
					echo form_default_textarea('ds_justificativa', 'Justificativa:', $row['ds_justificativa']);
				}
				
				if(trim($row['dt_encerrar']) != '')
				{
					echo form_default_row('dt_encerrar', 'Dt Encerrado:', $row['dt_encerrar']);
					echo form_default_row('ds_usuario_encerrar', 'Encerrado por:', $row['ds_usuario_encerrar']);
				}
			echo form_end_box('default_box');
	        echo form_start_box('default_justificativa_box', 'Acompanhamento');
	            echo form_default_textarea('acompanhamento', 'Descrição: (*)') ;
	        echo form_end_box('default_justificativa_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
	    echo $grid->render();
	    echo br();
	echo aba_end();
	$this->load->view('footer_interna');
?>