<?php
set_title('Treinamento Colaborador - Agenda');
$this->load->view('header');
?>
<script>
<?php
	echo form_default_js_submit(Array('dt_agenda', 'hr_ini', 'hr_fim'));
?>
    function ir_lista()
    {
        location.href = "<?= site_url('cadastro/treinamento_diretoria_conselhos') ?>";
    }
	
    function ir_cadastro()
    {
        location.href="<?= site_url('cadastro/treinamento_diretoria_conselhos/cadastro/'.$cd_treinamento_diretoria_conselhos) ?>";
    }
	
    function agenda_excluir(cd_treinamento_diretoria_conselhos_agenda, cd_treinamento_diretoria_conselhos)
    {
    	var confirmacao = "Deseja excluir a Agenda?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 	

        if(confirm(confirmacao))
		{
			location.href = "<?= site_url('cadastro/treinamento_diretoria_conselhos/agenda_excluir') ?>"+"/"+cd_treinamento_diretoria_conselhos_agenda+"/"+cd_treinamento_diretoria_conselhos;
		}
    }
	
    function agenda_atualizar()
    {
        if(confirm("ATENÇÃO\n\nDeseja Atualizar a Agenda?\n\n"))
		{
			location.href = "<?= site_url('cadastro/treinamento_diretoria_conselhos/agenda_atualizar/'.$cd_treinamento_diretoria_conselhos) ?>";
		}
    }	
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'DateBR'
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
	
	$(function(){
		configure_result_table();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_agenda', 'Agenda', TRUE, 'location.reload();');

echo aba_start($abas);
    echo form_open('cadastro/treinamento_diretoria_conselhos/agenda_salvar');
    echo form_start_box( "default_box", "Cadastro" );
        echo form_default_hidden('cd_treinamento_diretoria_conselhos', '', $cd_treinamento_diretoria_conselhos);
        echo form_default_date('dt_agenda', 'Dt Agenda: (*)');
        echo form_default_time('hr_ini', 'Hr Início: (*)');
        echo form_default_time('hr_fim', 'Hr Final: (*)');
    echo form_end_box("default_box");
	
	echo form_command_bar_detail_start();
		echo button_save('Salvar');
		echo button_save('Atualizar Agenda','agenda_atualizar()', 'botao_verde');
	echo form_command_bar_detail_end();
	
	echo form_start_box('default_box', 'Agenda');
		$body = array();
		$head = array( 
			'Data', 'Hr Início', 'Hr Final', ''
		);
		
		foreach($collection as $item)
		{
			$body[] = array(
				$item['dt_agenda'],
				$item['hr_ini'],
				$item['hr_fim'],
				'<a onclick="agenda_excluir('.$item['cd_treinamento_diretoria_conselhos_agenda'].', '.$cd_treinamento_diretoria_conselhos.')" href="javascript:void(0);">[Excluir]</a>'
			);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;
		echo $grid->render();

	echo form_end_box("default_box");
	
	echo br(5);	

echo aba_end();

$this->load->view('footer_interna');

?>