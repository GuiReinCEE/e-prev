<?php
	set_title('Sistema - Rotina');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_sistema_rotina'), 'valida(form);');?>

	function ir_lista()
	{
		location.href = '<?= site_url('/servico/sistema_eprev') ?>';
	}

	function ir_cadastro()
	{
		location.href = '<?= site_url('servico/sistema_eprev/cadastro/'.$sistema['cd_sistema']) ?>';
	}

	function ir_acompanhamento()
	{
		location.href = "<?= site_url('servico/sistema_eprev/acompanhamento/'.intval($sistema['cd_sistema']))?>";
	}

	function ir_atividade()
	{
		location.href = "<?= site_url('servico/sistema_eprev/atividade/'.intval($sistema['cd_sistema'])) ?>";
	}

	function ir_anexo()
	{
		location.href = "<?= site_url('servico/sistema_eprev/anexo/'.intval($sistema['cd_sistema'])) ?>";
	}

	function cancelar()
	{
		location.href = "<?= site_url('servico/sistema_eprev/rotina/'.intval($sistema['cd_sistema'])) ?>";
	}

	function ir_metodo()
	{
		location.href = "<?= site_url('servico/sistema_eprev/metodo/'.intval($sistema['cd_sistema'])) ?>";
	}

	function ir_pendencia()
	{
		location.href = "<?= site_url('servico/sistema_eprev/pendencia/'.intval($sistema['cd_sistema'])) ?>";
	}

	function valida(form)
	{
		$.post("<?= site_url('servico/sistema_eprev/valida_evento') ?>",
		{
			cd_evento : $('#cd_evento').val()
		},
		function(data)
		{
			if(data['valida'] == 0)
			{
				alert('Número de evento não existe');
				return false;
			}
			else
			{
				$('form').submit(); 
			}

		}, 'json', true);
	}

	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
        	'CaseInsensitiveString',
			'CaseInsensitiveString',
			'Number',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'DateTimeBR'
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
        ob_resul.sort(5, true);
    }

    
    $(function(){
		configure_result_table();
	});
</script>
<?php 
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_metodo', 'Método',FALSE, 'ir_metodo();');
	$abas[] = array('aba_rotina', 'Rotina', TRUE, 'location.reload();');
	$abas[] = array('aba_pendencia', 'Pendências', FALSE, 'ir_pendencia();');
	$abas[] = array('aba_atividade', 'Atividade', FALSE, 'ir_atividade();');
	$abas[] = array('aba_acompanhamento', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
	$abas[] = array('aba_anexo', 'Anexo', FALSE, 'ir_anexo();');
	 

	$head = array(
		'Rotina',
		'Job',
		'Cód. Evento',
		'Execução',
		'Descrição',
		'Dt Inclusão'
	);

	$body = array();

	foreach($collection as $item )
	{	
	    $body[] = array(
			array(anchor('servico/sistema_eprev/rotina/'.$item['cd_sistema'].'/'.$item['cd_sistema_rotina'],$item['ds_sistema_rotina']), 'text-align:left;'),
			array(nl2br($item['ds_job']), 'text-align:left;'),
			anchor('servico/eventos/envia_email/'.$item['cd_evento'],$item['cd_evento']),
			array(nl2br($item['ds_execucao']), 'text-align:justify;'),
			array(nl2br($item['ds_descricao']), 'text-align:justify;'),
			$item['dt_inclusao']
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	$grid->view_count = false;

	echo aba_start($abas);
		echo form_open('servico/sistema_eprev/rotina_salvar');
			echo form_start_box('default_sistema_box', 'Sistema');
					echo form_default_hidden('cd_sistema', '', $sistema['cd_sistema']);
					echo form_default_row('ds_sistema', 'Sistema:', $sistema['ds_sistema']);
					echo form_default_row('cd_gerencia_responsavel', 'Gerência Responsável:', $sistema['cd_gerencia_responsavel']);		
					echo form_default_row('cd_usuario_responsavel', 'Responsável:', $sistema['ds_responsavel']);			
				echo form_end_box('default_sistema_box');

			echo form_start_box('default_sistema_box', ' Cadastro');
				echo form_default_hidden('cd_sistema_rotina', '', $row['cd_sistema_rotina']);
				echo form_default_text('ds_sistema_rotina', 'Rotina: (*)', $row['ds_sistema_rotina'], 'style="width:350px;"');
				echo form_default_text('ds_job', 'Job: (*)', $row['ds_job'], 'style="width:350px;"');
				echo form_default_integer('cd_evento', 'Cód. Evento: ', $row['cd_evento']);
				echo form_default_textarea('ds_execucao', 'Execução: ', $row['ds_execucao'], 'style="height:100px;"');
				echo form_default_textarea('ds_descricao', 'Descrição: ', $row['ds_descricao'], 'style="height:100px;"');			
			echo form_end_box('default_sistema_box');
			echo form_command_bar_detail_start();
			    echo button_save('Salvar');	
			    if(intval($row['cd_sistema_rotina']) >0)
			    {
			    	echo button_save('Cancelar', 'cancelar()', 'botao_disabled');
			    }
			echo form_command_bar_detail_end();
		echo form_close();
		echo br();
	    echo $grid->render();
		echo br(2);
	echo aba_end();
	
	$this->load->view('footer_interna');
?>