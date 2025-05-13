<?php
set_title('Relacionamento - Empresas');
$this->load->view('header');
?>
<script>
	<?php echo form_default_js_submit( array("dt_contato", "cd_empresa_contato_atividade ", "cd_empresa_origem_contato", "ds_contato") ); ?>
	
	function ir_lista()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa"); ?>';
	}

	function ir_cadastro()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/cadastro/".$row['cd_empresa']); ?>';
	}
	
	function ir_anexo()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/anexo/". $row['cd_empresa']); ?>';
	}

	function ir_pessoas()
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/pessoas/".intval($row['cd_empresa'])); ?>';
	}
	
	function ir_anexo_contato(cd_empresa_contato)
	{
		location.href='<?php echo site_url("ecrm/relacionamento_empresa/contato_anexo/".intval($row['cd_empresa'])); ?>/'+cd_empresa_contato;
	}
	
	function ir_agenda()
	{
		location.href='<?php echo site_url(  "ecrm/relacionamento_empresa/agenda/".intval($row['cd_empresa'])); ?>';
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"DateBR", 
			"CaseInsensitiveString", 
			"CaseInsensitiveString", 
			"CaseInsensitiveString", 
			null,
			"CaseInsensitiveString",
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
		ob_resul.sort(0, true);
	}
	
	function listar_contato()
	{		
		$('#result_div').html("<?php echo loader_html(); ?>");
				
		$.post('<?php echo site_url('/ecrm/relacionamento_empresa/listar_contato'); ?>',
		{
			cd_empresa : $('#cd_empresa').val()
		},
		function(data)
		{
			$('#result_div').html(data);
			configure_result_table();
		});
	}
	
	function excluir_contato(cd_empresa_contato)
	{
		if(confirm("ATENÇÃO\n\nDeseja excluir o contato?\n\nClique [Ok] para Sim\nClique [Cancelar] para Não\n\n"))
		{
			$.post( '<?php echo site_url('/ecrm/relacionamento_empresa/excluir_contato'); ?>',
			{
				cd_empresa_contato : cd_empresa_contato
			},
			function(data)
			{			
				listar_contato();
			});
		}
	}
	
	$(function(){
		listar_contato();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_empresa', 'Empresa', FALSE, 'ir_cadastro();');
$abas[] = array('aba_contato', 'Contato', TRUE, 'location.reload();');
$abas[] = array('aba_agenda', 'Agenda', FALSE, 'ir_agenda();');
$abas[] = array('aba_pessoa', 'Pessoa', FALSE, 'ir_pessoas();');
$abas[] = array('aba_pessoa', 'Anexo', FALSE, 'ir_anexo();');

$config_cont = array('expansao.empresa_origem_contato', 'cd_empresa_origem_contato', 'ds_empresa_origem_contato');
$config_ativ = array('expansao.empresa_contato_atividade', 'cd_empresa_contato_atividade', 'ds_empresa_contato_atividade');

echo aba_start( $abas );
	echo form_open('ecrm/relacionamento_empresa/salvar_contato');
		echo form_start_box("contatos_box", "Contatos");
			echo form_hidden('cd_empresa', $row['cd_empresa']);
			echo form_hidden('cd_empresa_contato', $row['cd_empresa_contato']);
			echo form_default_date("dt_contato", "Dt. do Contato :*", $row);
			echo form_default_dropdown_db('cd_empresa_contato_atividade', 'Atividade :*', $config_ativ, array($row['cd_empresa_contato_atividade']), '', '', TRUE);	
			echo form_default_dropdown_db('cd_empresa_origem_contato', 'Origem :*', $config_cont, array($row['cd_empresa_origem_contato']), '', '', TRUE);	
			echo form_default_textarea("ds_contato", "Descrição :*", $row);
		echo form_end_box("contatos_box");
		echo form_command_bar_detail_start();
			echo button_save('Salvar Contato');
		echo form_command_bar_detail_end();
	echo form_close();	
	echo br();
	echo '<div id="result_div"></div>';
echo aba_end();	
$this->load->view('footer_interna');
?>