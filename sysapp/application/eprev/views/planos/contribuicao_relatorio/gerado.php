<?php
    set_title('Contribuição - Envio SMS');
    $this->load->view('header');
?>
<script>
    function filtrar()
    {
        $("#result_div").html("<?= loader_html() ?>");
                
        $.post("<?= site_url('planos/contribuicao_relatorio/gerado_listar') ?>",
        $("#filter_bar_form").serialize(),
        function(data)
        {
            $("#result_div").html(data);
            configure_result_table();
        }); 
    }
	
    function configure_result_table()
    {
        if(document.getElementById("table-1"))
        {
            var ob_resul = new SortableTable(document.getElementById("table-1"),[
                null,
                "DateTimeBR",
                "CaseInsensitiveString",
                "Number",
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
    }	

    function ir_lista()
    {
        location.href = "<?= site_url('planos/contribuicao_relatorio') ?>";
    }

    function ver_gerado(cd_contribuicao_relatorio_sms_geracao)
    {
        location.href = "<?= site_url('planos/contribuicao_relatorio/ver_gerado') ?>/"+cd_contribuicao_relatorio_sms_geracao;
    }

    function ir_debito_conta()
    {
        location.href = "<?= site_url('planos/contribuicao_relatorio/debito_conta') ?>";
    }

    $(function(){
		filtrar();
    })
</script>
<?php
    $abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
    $abas[] = array('aba_gerados', 'CSV Gerados', TRUE, 'location.reload();');
    $abas[] = array('aba_debito_conta', 'Débito em Conta', FALSE, 'ir_debito_conta();');

    echo aba_start($abas);
    	echo '<div id="result_div"></div>';
    	echo br(2);
    echo aba_end();
    $this->load->view('footer');
?>