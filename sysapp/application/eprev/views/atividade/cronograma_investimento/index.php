<?php
set_title('Cronograma - GIN');
$this->load->view('header');
?>
<script type="text/javascript">    
    function filtrar()
    {
		if($('#cd_analista').val() != "")
        {
			$("#result_div").html("<?php echo loader_html(); ?>");

			$.post('<?php echo site_url('/atividade/cronograma_investimento/listar'); ?>',
			$('#filter_bar_form').serialize(),
			function(data)
			{
				$("#result_div").html(data);
			});
		} 
        else
        {
            alert('Preencha os filtro para exibir a lista.')
        }
    }
	
    function setPrioridade(cd_cronograma_investimento,cd_cronograma_investimento_item)
    {
        $("#ajax_prioridade_valor_" + cd_cronograma_investimento_item).html("<?php echo loader_html("P"); ?>");

        $.post( '<?php echo site_url('/atividade/cronograma_investimento/setPrioridade');?>',
        {
            cd_cronograma_investimento_item : cd_cronograma_investimento_item,
            nr_prioridade      : $("#nr_prioridade_" + cd_cronograma_investimento_item).val()	
        },
        function(data)
        {
			$("#ajax_prioridade_valor_" + cd_cronograma_investimento_item).empty();
			
			$("#nr_prioridade_" + cd_cronograma_investimento_item).hide();
			$("#prioridade_salvar_" + cd_cronograma_investimento_item).hide(); 
			
            $("#prioridade_valor_" + cd_cronograma_investimento_item).html($("#nr_prioridade_" + cd_cronograma_investimento_item).val()); 
			$("#prioridade_valor_" + cd_cronograma_investimento_item).show(); 
			$("#prioridade_editar_" + cd_cronograma_investimento_item).show(); 
			
        });
    }	
	
	function editarPrioridade(cd_cronograma_investimento_item)
	{
		$("#prioridade_valor_" + cd_cronograma_investimento_item).hide(); 
		$("#prioridade_editar_" + cd_cronograma_investimento_item).hide(); 

		$("#prioridade_salvar_" + cd_cronograma_investimento_item).show(); 
		$("#nr_prioridade_" + cd_cronograma_investimento_item).show(); 
		$("#nr_prioridade_" + cd_cronograma_investimento_item).focus();	
	}
	
	
	function setConcluidoCor(cd_cronograma_item)
	{
		$("#concluido_valor_" + cd_cronograma_item).removeClass();
		$("#concluido_valor_" + cd_cronograma_item).addClass(($("#fl_concluido_" + cd_cronograma_item).val() == "N" ? "label label-important" : "label label-info"));
	}
	
    function setConcluido(cd_cronograma_investimento,cd_cronograma_investimento_item)
    {
		$("#ajax_concluido_valor_" + cd_cronograma_investimento_item).html("<?php echo loader_html("P"); ?>");

		$.post( '<?php echo site_url('/atividade/cronograma_investimento/setConcluido');?>',
        {
            cd_cronograma_investimento_item : cd_cronograma_investimento_item,
            fl_concluido      : $("#fl_concluido_" + cd_cronograma_investimento_item).val()	
        },
        function(data)
        {
			$("#ajax_concluido_valor_" + cd_cronograma_investimento_item).empty();
			
			setConcluidoCor(cd_cronograma_investimento_item);
			
			$("#fl_concluido_" + cd_cronograma_investimento_item).hide();
			$("#concluido_salvar_" + cd_cronograma_investimento_item).hide(); 
			
            $("#concluido_valor_" + cd_cronograma_investimento_item).html($("#fl_concluido_" + cd_cronograma_investimento_item +" option:selected").text()); 
			$("#concluido_valor_" + cd_cronograma_investimento_item).show(); 
			$("#concluido_editar_" + cd_cronograma_investimento_item).show(); 
			
        });
    }		
	
	function editarConcluido(cd_cronograma_investimento_item)
	{
		$("#concluido_valor_" + cd_cronograma_investimento_item).hide(); 
		$("#concluido_editar_" + cd_cronograma_investimento_item).hide(); 

		$("#concluido_salvar_" + cd_cronograma_investimento_item).show(); 
		$("#fl_concluido_" + cd_cronograma_investimento_item).show(); 
		$("#fl_concluido_" + cd_cronograma_investimento_item).focus();	
	}	
	
	function configure_result_table(cd_cronograma_investimento)
	{
		if(document.getElementById("cronograma_" + cd_cronograma_investimento))
		{
			var ob_resul = new SortableTable(document.getElementById("cronograma_" + cd_cronograma_investimento),[
				null,
				"Number",
				null,
				"CaseInsensitiveString",
				"Number",
				"DateBR",
				"CaseInsensitiveString",
				null,
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
			ob_resul.sort(1, false);
		}
	}	
	
    function novo()
    {
        location.href="<?php echo site_url('/atividade/cronograma_investimento/cadastro'); ?>";
    } 
	
	$(function(){
		filtrar();
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

$config['button'][] = array('Novo Mês', 'novo()');

$arr_concluido[] = array('value' => 'S', 'text' => 'Sim');
$arr_concluido[] = array('value' => 'N', 'text' => 'Não');

echo aba_start($abas);    
    echo form_list_command_bar($config);
        echo form_start_box_filter('filter_bar', 'Filtros');
        echo filter_dropdown('cd_analista', 'Analista:*', $analista);
        echo form_default_mes_ano('nr_mes', 'nr_ano', 'A partir do mês/ano:');
		echo filter_dropdown('fl_concluido', 'Concluído:', $arr_concluido, array('N'));
    echo form_end_box_filter();
    echo '
		<div id="result_div">'.
			br(2).'
			<span style="color:green;">
				<b>Realize um filtro para exibir a lista</b>
			</span>
		</div>';
	echo br(2);
echo aba_end();

$this->load->view('footer');
?>