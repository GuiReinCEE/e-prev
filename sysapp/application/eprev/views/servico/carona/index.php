<?php
set_title('Mão na Roda');
$this->load->view('header');
?>
<script type="text/javascript">
    function filtrar()
    {
        load();
    }

    function load()
    {
        document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";
        
        $.post( '<?php echo base_url() . index_page(); ?>/servico/carona/listar'
            ,{
                vagas: $('#vagas').val()
                ,gerencia: $('#usuario_gerencia').val()
                ,usuario: $('#usuario').val()
            }
            ,
        function(data)
            {
                document.getElementById("result_div").innerHTML = data;
                configure_result_table();
            }
        );
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'Number'
            ,'CaseInsensitiveString'
            ,'CaseInsensitiveString'
            ,'CaseInsensitiveString'
            ,'CaseInsensitiveString'
            ,'CaseInsensitiveString'
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
        location.href='<?php echo site_url("servico/carona/cadastro"); ?>';
    }

    function entrar_carona(cd_carona)
	{
		var aviso = "Atenção\n\nDeseja entrar na carona?\n\n";

		if(confirm(aviso))
		{
            location.href='<?php echo site_url("servico/carona/entrar"); ?>'+'/'+ cd_carona;
		}
	}

    function sair_carona(cd_carona_caroneiro)
	{
		var aviso = "Atenção\n\nDeseja sair da carona?\n\n";

		if(confirm(aviso))
		{
            location.href='<?php echo site_url("servico/carona/sair"); ?>'+'/'+ cd_carona_caroneiro;
		}
	}


</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$ar_anos[] = array('value'=> 'sim', 'text'=> 'Sim');
$ar_anos[] = array('value'=> 'nao', 'text'=> 'Não');

echo aba_start( $abas );

    $config['button'][]=array('Novo', 'novo()');

    echo form_list_command_bar($config);

    echo form_start_box_filter();
        echo filter_usuario_ajax('usuario','','','Usuário:');
        echo filter_dropdown('vagas', 'Há Vagas:', $ar_anos);
    echo form_end_box_filter();

?>
<div id="result_div"></div>
<br />

<script type="text/javascript">
	filtrar();
</script>
<?php
echo aba_end();
$this->load->view('footer');
?>