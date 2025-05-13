<?php
set_title('Reuniões SG - Controle');
$this->load->view('header');
?>
<script>
    function filtrar()
    {
        load();
    }

    function load()
    {
        document.getElementById("result_div").innerHTML = "<?php echo loader_html(); ?>";

        $.post( '<?php echo base_url() . index_page(); ?>/atividade/reuniao_sg_permissao/listar',
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
            'CaseInsensitiveString', 
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
        ob_resul.sort(0, true);
    }

    function novo()
    {
        location.href='<?php echo site_url("atividade/reuniao_sg_permissao/cadastro/"); ?>';
    }
	
	function excluir(cd_reuniao_sg_permissao)
	{
		var confirmacao = 'Deseja excluir?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';
        
        if(confirm(confirmacao))
        {
           location.href='<?php echo site_url("atividade/reuniao_sg_permissao/excluir"); ?>/'+cd_reuniao_sg_permissao;
        }
	}
	
	$(function(){
		filtrar();
	});
    
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

echo aba_start($abas);

	$config['button'][] = array('Novo', 'novo()');
	echo form_list_command_bar($config);

	
	echo'<div id="result_div"></div>';

	echo br();
	echo aba_end();

$this->load->view('footer');
?>