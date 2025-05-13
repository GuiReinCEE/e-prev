<?php
	set_title('Cenário legal');
	$this->load->view('header');
?>
<script>
    function filtrar()
    {
        if($("#ano").val() == "")
		{
			alert("Informe o Ano");
			$('#ano').focus();
		}
		else
		{
			$("#result_div").html("<?= loader_html() ?>");

			$.post("<?= site_url('gestao/controle_cenario/listar') ?>",
			$("#filter_bar_form").serialize(),
			function(data)
			{
				$("#result_div").html(data);
			});
		}
	}

    function sem_data()
    {
        location.href = "<?= site_url('gestao/controle_cenario/sem_data') ?>";
    }

    function atrasada()
    {
        location.href = "<?= site_url('gestao/controle_cenario/atrasada') ?>";
    }

    $(function(){
    	filtrar();
	});

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
	$abas[] = array('aba_sem_data', 'Sem Data Legal', FALSE, 'sem_data()');
	$abas[] = array('aba_atrasada', 'Atrasada', FALSE, 'atrasada()');

	$ar_mes[] = array('value' => 1, 'text' => 'Janeiro');
	$ar_mes[] = array('value' => 2, 'text' => 'Fevereiro');
	$ar_mes[] = array('value' => 3, 'text' => 'Março');
	$ar_mes[] = array('value' => 4, 'text' => 'Abril');
	$ar_mes[] = array('value' => 5, 'text' => 'Maio');
	$ar_mes[] = array('value' => 6, 'text' => 'Junho');
	$ar_mes[] = array('value' => 7, 'text' => 'Julho');
	$ar_mes[] = array('value' => 8, 'text' => 'Agosto');
	$ar_mes[] = array('value' => 9, 'text' => 'Setembro');
	$ar_mes[] = array('value' => 10, 'text' => 'Outubro');
	$ar_mes[] = array('value' => 11, 'text' => 'Novembro');
	$ar_mes[] = array('value' => 12, 'text' => 'Dezembro');

	foreach($anos as $item)
	{
	    $ar_anos[] = array('value' => $item['ano'], 'text' => $item['ano']);
	}

	$ar_anos[] = array('value' => $item['ano'], 'text' => $item['ano']);

	echo aba_start($abas);
		echo form_list_command_bar(array());
	    echo form_start_box_filter();
            echo filter_dropdown('ano', 'Ano: (*)', $ar_anos, array(date('Y')));
            echo filter_dropdown('mes', 'Mes:', $ar_mes);
	    echo form_end_box_filter();
	    echo '<div id="result_div"></div>';
	    echo br(2);
	echo aba_end();
	$this->load->view('footer');
?>