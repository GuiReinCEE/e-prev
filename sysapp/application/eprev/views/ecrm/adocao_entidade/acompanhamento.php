<?php
set_title('Adoção de Entidades');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(Array('dt_adocao_entidade_acompanhamento', 'ds_adocao_entidade_acompanhamento'));
	?>
    function ir_lista()
    {
        location.href='<?php echo site_url("ecrm/adocao_entidade"); ?>';
    }
	
	function ir_cadastro()
    {
        location.href='<?php echo site_url("ecrm/adocao_entidade/cadastro/".intval($row['cd_adocao_entidade'])); ?>';
    }
	
	function listar()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");
	
		$.post('<?php echo site_url('ecrm/adocao_entidade/listar_acompanhamento');?>',
		{
			cd_adocao_entidade : $('#cd_adocao_entidade').val()
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
			'DateTimeBR',
			'CaseInsensitiveString',
			'DateBR',
			'CaseInsensitiveString'
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
		listar();
	});
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_lista', 'Acompanhamento', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo form_open('ecrm/adocao_entidade/salvar_acompanhamento');
		echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('cd_adocao_entidade', '', $row['cd_adocao_entidade']);
			echo form_default_row('ds_adocao_entidade', 'Nome :', $row['ds_adocao_entidade']);
			echo form_default_row('ds_adocao_entidade_periodo', 'Período :', $row['ds_adocao_entidade_periodo']);
			echo form_default_row('ds_adocao_entidade_tipo', 'Tipo :', $row['ds_adocao_entidade_tipo']);
			echo form_default_date('dt_adocao_entidade_acompanhamento', 'Data :*');
			echo form_default_textarea('ds_adocao_entidade_acompanhamento', 'Descrição :*', '', 'style="height:150px;"');
		echo form_end_box("default_box");
		echo form_command_bar_detail_start();     
			echo button_save("Salvar");
		echo form_command_bar_detail_end();
	echo form_close();
	echo '<div id="result_div"></div>';
    echo br(2);	
echo aba_end();

$this->load->view('footer_interna');
?>