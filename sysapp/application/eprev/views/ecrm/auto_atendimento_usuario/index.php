<?php
	set_title('Autoatendimento Usuário - Lista');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		load();
	}
	
	function load()
	{
		$("#result_div").html("<?php echo loader_html(); ?>");

		$.post('<?php echo base_url().index_page(); ?>/ecrm/auto_atendimento_usuario/listar',
			{
				cd_situacao : $('#cd_situacao').val()
			}
			,
			function(data)
			{
				$("#result_div").html(data);
				configura_tabela();
			}
		);
	}
	
	function configura_tabela()
	{
		var ob_resul = new SortableTable(document.getElementById("tabela_auto_atendimento_usuario"),
		[
			"CaseInsensitiveString",
			"CaseInsensitiveString",
			"DateTimeBR",			
			"CaseInsensitiveString",
			"DateTimeBR",			
			"CaseInsensitiveString",
			"DateTimeBR"						
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

	function excluirUsuario(cd_auto_atendimento_usuario)
	{
		if(confirm("Deseja excluir?"))
		{
			$.post('<?php echo base_url().index_page(); ?>/ecrm/auto_atendimento_usuario/excluir',
				{
					cd_auto_atendimento_usuario : cd_auto_atendimento_usuario
				}
				,
				function(data)
				{
					if(data != "")
					{
						$("#dt_exclusao_" + cd_auto_atendimento_usuario).html(data)
					}
				}
			);
		}
	}	
	
	function incluirUsario()
	{
		location.href='<?php echo site_url("ecrm/auto_atendimento_usuario/cadastro"); ?>';
	}
</script>
<?php
    $ar_situacao[] = array('value' => 'A','text' => 'Ativo');
    $ar_situacao[] = array('value' => 'E','text' => 'Excluído');

	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start( $abas );

	$config['button'][]=array('Liberar Usuário', 'incluirUsario();');
	echo form_list_command_bar($config);
    echo form_start_box_filter('filter_bar', 'Filtros', false);	
		echo filter_dropdown('cd_situacao', 'Situação:', $ar_situacao, array('cd_situacao' => 'A'));
	echo form_end_box_filter();	

?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br>
<br>
<br>
<script>
$(document).ready(function() {
	filtrar();
});
	
</script>
<?php
	echo aba_end(''); 
	$this->load->view('footer');
?>