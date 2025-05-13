<?php
set_title('Plano Fiscal - Parecer - Lista');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        $('#result_div').html("<?php echo loader_html(); ?>");

        $.post('<?php echo site_url('gestao/plano_fiscal_parecer/listar')?>', $('#filter_bar_form').serialize(),
        function(data)
        {
            $('#result_div').html(data);
            configure_result_table();
        });
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'CaseInsensitiveString',
            null,
            'Number',
            'Number',
            'Number',
            'Number',
            'Number',
            'CaseInsensitiveString',
            'DateTimeBR',
            'DateTimeBR',
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

    function novo()
    {
        location.href='<?php echo site_url("gestao/plano_fiscal_parecer/cadastro/"); ?>';
    }
    
    function ir_relatorio()
    {
        location.href='<?php echo site_url("gestao/plano_fiscal_parecer/relatorio"); ?>';
    }

    $(function(){
		if($("#nr_ano").val() == "")
		{
			var d = new Date();
			var n = d.getFullYear(); 			
			
			$("#nr_ano").val(n);
		}
	   
		filtrar();
    });
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$abas[] = array('aba_lista', 'Relat�rio', FALSE, 'ir_relatorio();');

$config['button'][] = array('Novo', 'novo()');

echo aba_start( $abas );
    echo form_list_command_bar($config);
    echo form_start_box_filter();
		echo filter_integer_ano('nr_ano', 'nr_mes', 'Ano/M�s :');
    echo form_end_box_filter();
    echo '<div id="result_div"></div>';
    echo br(2);

echo aba_end();

$this->load->view('footer'); ?>.