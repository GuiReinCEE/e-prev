<?php
	set_title('Indicação');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('cd_usuario', 'ds_indicado', 'nr_telefone', 'ds_tipo_indicacao')); ?>

	function cancelar()
	{
		location.href = "<?= site_url('planos/indicacao') ?>";
	}

    function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
		    "CaseInsensitiveString",
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
				removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
				addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
			}
		};
		ob_resul.sort(0, true);
	}

    $(function(){
		configure_result_table();
	});
</script>
<style>
	.banner
	{
		text-align: center;
	}
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

    $head = array( 
        'Área',
        'Nome',
        'Nome do Indicado',
		'Telefone',
		'E-mail',
		'Tipo de indicação',
		'Grau de Parentesco',
		'Cidade',
		
		'Observações da Indicação'
    );

    $body = array();

    foreach($collection as $item)
	{
        $body[] = array(
            array($item['area'], 'text-align:left;'),
            array($item['nome'], 'text-align:left;'),
            array($item['ds_indicado'], 'text-align:left;'),
            $item['nr_telefone'],
            array($item['ds_email'], 'text-align:left;'),
            array($item['ds_tipo_indicacao'], 'text-align:left;'),
            $item['ds_parentesco'],
            array($item['ds_cidade'], 'text-align:left;'),
            array($item['ds_observacao'], 'text-align:justify;')
        );
    }

    $this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;

	echo aba_start($abas);
		echo '<div class="banner">';
			echo '<img src="'.base_url('img/banner_formulario_20220222.png').'">';
		echo '</div>';
	 	echo form_open('planos/indicacao/salvar');
            echo form_start_box('default_box', 'Cadastro');
            	echo form_default_dropdown('cd_usuario_indicacao', 'Nome: (*)', $drop_usuarios, $cd_usuario);
            	echo form_default_text('ds_indicado', 'Nome do Indicado: (*)', '', 'style="width:500px;"');
            	echo form_default_telefone('nr_telefone', 'Telefone: (*)', '');
            	echo form_default_dropdown('ds_tipo_indicacao', 'Tipo de indicação: (*)', $drop_tipo_indicacao, '');
            	echo form_default_text('ds_email', 'Email:', '', 'style="width:500px;"');
            	echo form_default_dropdown('ds_parentesco', 'Grau de Parentesco:', $drop_parentesco, '');
            	echo form_default_text('ds_cidade', 'Cidade:', '', 'style="width:500px;"');
            	echo form_default_textarea('ds_observacao', 'Observações da Indicação:', '', 'style="width: 500px; height: 80px;"');
            echo form_end_box('default_box');
            echo form_command_bar_detail_start();
				echo button_save('Salvar');	
			echo form_command_bar_detail_end();
        echo form_close();
        echo br();
        echo $grid->render();
        echo br(5);
	echo aba_end();
	$this->load->view('footer');
?>