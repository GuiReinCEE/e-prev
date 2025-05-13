<?php
	set_title('Pós Venda - Respostas');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit(array('acompanhamento', 'cd_pos_venda_participante')) ?>
        
	function ir_lista()
	{
		location.href = "<?= site_url('ecrm/posvenda') ?>";
	}

	function ir_relatorio_email()
	{
		location.href = "<?= site_url('ecrm/posvenda/relatorio_email') ?>";
	}
    
    function ir_relatorio()
	{
		location.href = "<?= site_url('ecrm/posvenda/relatorio') ?>";
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-acompanhamento"),
        [
            "Number",
            "DateTimeBR",
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
		ob_resul.sort(1, true);
	}

	$(function(){
		configure_result_table();
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_envia_email', 'Emails', FALSE, 'ir_emails();');
	$abas[] = array('aba_relatorio', 'Relatório', FALSE, 'ir_relatorio_email();');
	$abas[] = array('aba_relatorio', 'Formulário', TRUE, 'location.reload();');

	$body = array();
	$head = array( 
		'Pergunta',
		'Respostas'
	);

	foreach($collection as $item)
	{
	    $resposta = '';
	    
	    foreach($item['resposta'] as $item2)
	    {
	        $resposta .= '<b>'.$item2['ds_resposta'].'</b>'.br();
	        
	        if(trim($item2['complemento']) != "")
	        {
	            $resposta .= nl2br(strip_tags($item2['complemento']));
	        }
	    }
	    
	    $body[] = array(
	        array($item['ds_pergunta'], 'text-align:left;'),
	        array(nl2br($resposta), 'text-align:justify;')
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	$head = array( 
		'',
		'Data',
		'Acompamento',
		'Usuário'
	);

	$body = array();

	foreach($collection_acompanhamento as $item)
	{
		$body[] = array(
	        $item['cd_pos_venda_participante_acompanhamento'],
	        $item['dt_inclusao'],
	        array($item['acompanhamento'], 'text-align:justify;'),
	        array($item['nome'], 'text-align:left;')
		);
	}

	$grid_acompanhamento = new grid();
	$grid_acompanhamento->id_tabela = 'table-acompanhamento';
	$grid_acompanhamento->head = $head;
	$grid_acompanhamento->body = $body;

	echo aba_start($abas);
	    echo form_open('ecrm/posvenda/salvar_acompanhamento');
			echo form_start_box('default_acopanhamento_box', 'Cadastro');
				echo form_default_row('', 'RE:', $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia'], 'readonly style="width:100%; border: 0px;"');
				echo form_default_row('', 'Nome:', $row['nome'], 'readonly style="width:100%; border: 0px;"');
				echo form_default_row('', 'Dt Início:', $row['dt_inicio'], 'readonly style="width:100%; border: 0px;"');
				echo form_default_text('', 'Dt Fim:', $row['dt_final'], 'readonly style="width:100%; border: 0px;"');
				echo form_default_hidden('cd_pos_venda_participante', 'Cod. Pós Venda:', $row['cd_pos_venda_participante']);
				echo form_default_textarea('acompanhamento', 'Acompanhamento: (*)', '', 'style="width: 500px; height: 60px;"');
			echo form_end_box('default_acopanhamento_box');
			echo form_command_bar_detail_start();
				echo button_save('Salvar');
			echo form_command_bar_detail_end();
		echo form_close();
	    echo $grid_acompanhamento->render();
	    echo form_start_box('default_respostas_box', 'Respostas');
	        echo $grid->render();
	    echo form_end_box('default_respostas_box');
	    echo br(2);
	echo aba_end();

	$this->load->view('footer');
?>