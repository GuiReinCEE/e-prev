<?php
set_title("Pós-Venda - Emails");
$this->load->view('header');
?>
<script>
    <?php
        echo form_default_js_submit(array('cd_empresa', 'cd_registro_empregado', 'seq_dependencia'), 'form_eniva_email(form);');
    ?>
        
	function form_eniva_email(form)
	{
		if(confirm("ATENÇÃO: Após o envio não é possível reverter a ação.\n\nDeseja realmente enviar o Pós-Venda?"))
		{
			form.submit();
		}
	}
	
	function filtrar()
	{
        $('#result_div').html("<?php echo loader_html(); ?>")
		
        $.post('<?php echo site_url("ecrm/posvenda/listar_email") ?>',
		$('#filter_bar_form').serialize(),
        function(data)
        {
			$('#result_div').html(data)
            configure_result_table();
        }); 
    }
	
	function listar_result()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			"Number",
			"DateTimeBR",
			"DateTimeBR",
			"CaseInsensitiveString", 
			"RE", 
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
   
    function ir_relatorio()
	{
		location.href='<?php echo site_url('ecrm/posvenda/relatorio'); ?>';
	}
    
    function ir_lista()
	{
		location.href='<?php echo site_url('ecrm/posvenda'); ?>';
	}
    
    $(function(){
        filtrar();
    });
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_envia_email', 'Emails', TRUE, "location.reload();");
$abas[] = array('aba_relatorio', 'Relatório', FALSE, 'ir_relatorio();');

$conf = array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome');

echo aba_start( $abas );
	echo form_open('ecrm/posvenda/envia_email_salvar');
        echo form_start_box("cadastro", "Cadastro");
            echo form_default_participante($conf, "Participante :", Array('cd_empresa' => '', 'cd_registro_empregado' => '', 'seq_dependencia' => 0), false);
            echo form_default_row("", "", '<input type="button" value="Enviar Pós-Venda" onclick="salvar(this.form);" class="botao"');
        echo form_end_box("cadastro");
	echo form_close();
	echo form_list_command_bar();	
	echo form_start_box_filter('filter_bar', 'Filtros');
		echo filter_date_interval('dt_ini', 'dt_fim', 'Dt Cadastro :', calcular_data('','6 month'), date('d/m/Y'));
	echo form_end_box_filter();
    echo '
        <div id="result_div">'.
            br(2).'
            <span class="label label-success">Realize um filtro para exibir a lista</span>
        </div>';
echo aba_end();
$this->load->view('footer');
?>