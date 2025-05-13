<?php
set_title('Atendimento Invidualizado');
$this->load->view('header');
?>
<script>
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/atendimento_individual"); ?>';
    }
	
	function ir_cadastro()
    {
        location.href='<?php echo site_url("ecrm/atendimento_individual/cadastro/".intval($row['cd_atendimento_individual'])); ?>';
    }
	
	function listar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
	
		$.post('<?php echo site_url('ecrm/atendimento_individual/listar_acompanhamento');?>',
		{
			cd_atendimento_individual : $('#cd_atendimento_individual').val()
		},
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}
	
	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'CaseInsensitiveString',
			'DateTimeBR',
			'CaseInsensitiveString',
			null
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
	
	function salvar_acompahamento()
	{
		var ds_atendimento_individual_acompanhamento = $('#ds_atendimento_individual_acompanhamento').val();
		
		if(ds_atendimento_individual_acompanhamento != '')
		{
			$.post('<?php echo site_url('ecrm/atendimento_individual/salvar_acompahamento');?>',
			{
				cd_atendimento_individual                : $('#cd_atendimento_individual').val(),
				ds_atendimento_individual_acompanhamento : ds_atendimento_individual_acompanhamento
			},
			function(data)
			{
				listar();
                
                $('#ds_atendimento_individual_acompanhamento').val('')
			});
		}
		else
		{
			alert('Informe a descrição do acompanhamento');
		}
	}
	
	function excluir_acompahamento(cd_atendimento_individual_acompanhamento)
	{	
		if(confirm('Deseja excluir o acompanhamento?'))
		{
			$.post('<?php echo site_url('ecrm/atendimento_individual/excluir_acompahamento');?>',
			{
				cd_atendimento_individual_acompanhamento : cd_atendimento_individual_acompanhamento
			},
			function(data)
			{
				listar();
			});
		}
	}
	
	$(function(){
		listar();
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Acompanhamento', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_start_box( "default_box", "Cadastro" );
		echo form_default_hidden('cd_atendimento_individual', '', $row['cd_atendimento_individual']);
		echo form_default_row('ano_numero', 'Ano/Número :', $row['ano_numero']);
		echo form_default_row('re', 'Participante (Emp/RE/Seq) :', $row['cd_empresa'].'/'.$row['cd_registro_empregado'].'/'.$row['seq_dependencia']);
		echo form_default_row('nome', 'Nome :', $row['nome']);
		if(trim($row['dt_atendimento']) != '')
		{
			echo form_default_row('dt_atendimento', 'Dt. Atendimento :', $row['dt_atendimento']);
		}
		echo form_default_textarea('ds_atendimento_individual_acompanhamento', 'Descrição :', '', 'style="height:150px;"');
	echo form_end_box("default_box");
	echo form_command_bar_detail_start();     
		echo button_save("Salvar", "salvar_acompahamento()");
	echo form_command_bar_detail_end();
	echo '<div id="result_div"></div>';
    echo br(2);	
echo aba_end();

$this->load->view('footer_interna');
?>