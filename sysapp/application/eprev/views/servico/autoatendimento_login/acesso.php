<?php
	set_title('Login Autoatendimento - Acesso');
	$this->load->view('header');
?>
<script>
    <?= form_default_js_submit() ?>
   
    function ir_lista()
    {
        location.href = "<?= site_url('servico/autoatendimento_login') ?>";
    }

	function ir_acesso_quebrado()
    {
        location.href = "<?= site_url('servico/autoatendimento_login/acesso_quebrado/'.intval($login['cd_login'])) ?>";
    }
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
		    "DateTimeBR",
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
	
	$(function(){
		configure_result_table();
	});

</script>

<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_acesso', 'Acesso', TRUE, 'location.reload();');
    $abas[] = array('aba_acesso_quebrado', 'Acesso Quebrado', FALSE, 'ir_acesso_quebrado();');
	
	$head = array(
		'Dt. Acesso',
		'URI'
	);

	$body = array();
	
	foreach ($collection as $item)
	{
		$body[] = array(
			$item['dt_acesso'],
			array($item['ds_uri'], 'text-align:left')
		);	
	}
	
	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	
    echo aba_start($abas);
        echo form_start_box('default_box', 'Cadastro'); 
			echo form_default_row('dt_login', 'Dt. Login:', $login['dt_login']);
			echo form_default_row('re', 'RE:', $login['cd_empresa'].'/'.$login['cd_registro_empregado'].'/'.$login['seq_dependencia']);
			echo form_default_row('cd_usuario', 'Nome:', $login['nome_participante']);
		echo form_end_box('default_box'); 
		echo $grid->render();

        echo br(2);
    echo aba_end();

    $this->load->view('footer_interna');	
?>