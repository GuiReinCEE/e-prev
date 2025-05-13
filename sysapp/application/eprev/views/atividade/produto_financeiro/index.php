<?php
set_title('Acompanhamento de Produtos');
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

        $.post( '<?php echo base_url() . index_page(); ?>/atividade/produto_financeiro/listar',
		{
			ds_produto : $('#ds_produto').val(),
			cd_produto_financeiro_origem : $('#cd_produto_financeiro_origem').val(),
			cd_reuniao_sg_instituicao : $('#cd_reuniao_sg_instituicao').val(),
			cd_usuario_responsavel : $('#cd_usuario_responsavel').val(),
			cd_usuario_revisor : $('#cd_usuario_revisor').val(),
			dt_recebido_ini : $('#dt_recebido_ini').val(),
			dt_recebido_fim : $('#dt_recebido_fim').val(),
			dt_cadastro_ini : $('#dt_cadastro_ini').val(),
			dt_cadastro_fim : $('#dt_cadastro_fim').val(),
			dt_conclusao_ini : $('#dt_conclusao_ini').val(),
			dt_conclusao_fim : $('#dt_conclusao_fim').val()
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
			'Number',
            'CaseInsensitiveString', 
            'CaseInsensitiveString',
			'DateTimeBR',
			'DateBR',
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
        ob_resul.sort(3, true);
    }

    function novo()
    {
        location.href='<?php echo site_url("atividade/produto_financeiro/cadastro/"); ?>';
    }
	
	function aumentar(cd)
	{
		$('#mais_'+cd).hide();
		$('#menos_'+cd).show();
		$('#produto_financeiro_'+cd).show();
	}
	
	function diminuir(cd)
	{
		$('#menos_'+cd).hide();
		$('#mais_'+cd).show();
		$('#produto_financeiro_'+cd).hide();
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
	echo form_start_box_filter();
		echo filter_text('ds_produto', 'Produto :');
		echo filter_dropdown('cd_produto_financeiro_origem', 'Origem :', $arr_origem);
		echo filter_dropdown('cd_reuniao_sg_instituicao', 'Entidade/Fornecedor :', $arr_entidade_fornecedor);
		echo filter_dropdown('cd_usuario_responsavel', 'Responsável :', $arr_responsavel);
		echo filter_dropdown('cd_usuario_revisor', 'Revisor  :', $arr_revisor);
		echo filter_date_interval('dt_recebido_ini', 'dt_recebido_fim', 'Dt Recebido :',calcular_data('','1 month'), date('d/m/Y'));
		echo filter_date_interval('dt_cadastro_ini', 'dt_cadastro_fim', 'Dt Cadastro :');
		echo filter_date_interval('dt_conclusao_ini', 'dt_conclusao_fim', 'Dt Conclusão :');
    echo form_end_box_filter();
	
	echo'<div id="result_div"></div>';

	echo br();
	echo aba_end();

$this->load->view('footer');
?>