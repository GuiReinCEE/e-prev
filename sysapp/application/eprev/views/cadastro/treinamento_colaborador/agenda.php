<?php
set_title('Treinamento Colaborador - Agenda');
$this->load->view('header');
?>
<script>
<?php
	echo form_default_js_submit(Array('dt_agenda', 'hr_ini', 'hr_fim'));
?>
    function irLista()
	{
		location.href='<?php echo site_url("cadastro/treinamento_colaborador"); ?>';
	}
	
    function irCadastro()
    {
        location.href='<?php echo site_url("cadastro/treinamento_colaborador/cadastro/".$ano."/".$numero); ?>';
    }	
    
    function agendaExcluir(cd_treinamento_colaborador_agenda)
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir a Agenda?\n\n"))
		{
			location.href='<?php echo site_url("cadastro/treinamento_colaborador/agendaExcluir/".$ano."/".$numero); ?>/' + cd_treinamento_colaborador_agenda;
		}
    }	
	
    function agendaAtualizar()
    {
        if(confirm("ATENÇÃO\n\nDeseja Atualizar a Agenda?\n\n"))
		{
			location.href='<?php echo site_url("cadastro/treinamento_colaborador/agendaAtualizar/".$ano."/".$numero); ?>';
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
$abas[] = array('aba_lista', 'Lista', FALSE, 'irLista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'irCadastro();');
$abas[] = array('aba_agenda', 'Agenda', TRUE, 'location.reload();');

echo aba_start( $abas );
    echo form_open('cadastro/treinamento_colaborador/agendaSalvar', 'name="filter_bar_form"');
    echo form_start_box( "default_box", "Cadastro" );
        echo form_default_hidden('numero', "", $numero);
        echo form_default_hidden('ano', "", $ano);
        echo form_default_date("dt_agenda", "Dt Agenda:(*) ");
        echo form_default_time('hr_ini', 'Hr Início:(*) ');
        echo form_default_time('hr_fim', 'Hr Final:(*) ');
    echo form_end_box("default_box");
	
	echo form_command_bar_detail_start();
		echo button_save("Salvar");
		echo button_save("Atualizar Agenda","agendaAtualizar()","botao_verde");
	echo form_command_bar_detail_end();
	

	echo form_start_box( "default_box", "Agenda" );
		$body=array();
		$head = array( 
			'Data', 'Hr Início', 'Hr Final', ''
		);
		
		foreach( $collection as $item )
		{
			$body[] = array(
				$item['dt_agenda'],
				$item['hr_ini'],
				$item['hr_fim'],
				'<a onclick="agendaExcluir('.$item["cd_treinamento_colaborador_agenda"].')" href="javascript:void(0);">[Excluir]</a>'
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