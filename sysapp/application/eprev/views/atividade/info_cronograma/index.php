<?php
set_title('Cronograma - Analistas');
$this->load->view('header');
?>
<script type="text/javascript">
    function filtrar()
    {
        if($('#cd_analista').val() != "")
        {
            load();
        } 
        else
        {
            alert('Preencha os filtro para exibir a lista.')
        }
    }
    
    function load()
    {
        $("#result_div").html("<?php echo loader_html(); ?>");

        $.post( '<?php echo base_url().index_page(); ?>/atividade/info_cronograma/listar',
        {
            cd_analista : $('#cd_analista').val(),
            nr_ano      : $('#nr_ano').val(),	
            nr_mes      : $('#nr_mes').val()
        },
        function(data)
        {
            $("#result_div").html(data);
        });
    }
	
    function setPrioridade(cd_cronograma,cd_cronograma_item)
    {
        $("#ajax_prioridade_valor_" + cd_cronograma_item).html("<?php echo loader_html("P"); ?>");

        $.post( '<?php echo base_url().index_page(); ?>/atividade/info_cronograma/setPrioridade',
        {
            cd_cronograma_item : cd_cronograma_item,
            nr_prioridade      : $("#nr_prioridade_" + cd_cronograma_item).val()	
        },
        function(data)
        {
			$("#ajax_prioridade_valor_" + cd_cronograma_item).empty();
			
			$("#nr_prioridade_" + cd_cronograma_item).hide();
			$("#prioridade_salvar_" + cd_cronograma_item).hide(); 
			
            $("#prioridade_valor_" + cd_cronograma_item).html($("#nr_prioridade_" + cd_cronograma_item).val()); 
			$("#prioridade_valor_" + cd_cronograma_item).show(); 
			$("#prioridade_editar_" + cd_cronograma_item).show(); 
			
        });
    }	
	
	function editarPrioridade(cd_cronograma_item)
	{
		$("#prioridade_valor_" + cd_cronograma_item).hide(); 
		$("#prioridade_editar_" + cd_cronograma_item).hide(); 

		$("#prioridade_salvar_" + cd_cronograma_item).show(); 
		$("#nr_prioridade_" + cd_cronograma_item).show(); 
		$("#nr_prioridade_" + cd_cronograma_item).focus();	
	}
	
	
	function setConcluidoCor(cd_cronograma_item)
	{
		$("#concluido_valor_" + cd_cronograma_item).removeClass();
		$("#concluido_valor_" + cd_cronograma_item).addClass(($("#fl_concluido_" + cd_cronograma_item).val() == "N" ? "label label-important" : "label label-info"));
	}
	
    function setConcluido(cd_cronograma,cd_cronograma_item)
    {
		$("#ajax_concluido_valor_" + cd_cronograma_item).html("<?php echo loader_html("P"); ?>");

        $.post( '<?php echo base_url().index_page(); ?>/atividade/info_cronograma/setConcluido',
        {
            cd_cronograma_item : cd_cronograma_item,
            fl_concluido      : $("#fl_concluido_" + cd_cronograma_item).val()	
        },
        function(data)
        {
			$("#ajax_concluido_valor_" + cd_cronograma_item).empty();
			
			setConcluidoCor(cd_cronograma_item);
			
			$("#fl_concluido_" + cd_cronograma_item).hide();
			$("#concluido_salvar_" + cd_cronograma_item).hide(); 
			
            $("#concluido_valor_" + cd_cronograma_item).html($("#fl_concluido_" + cd_cronograma_item +" option:selected").text()); 
			$("#concluido_valor_" + cd_cronograma_item).show(); 
			$("#concluido_editar_" + cd_cronograma_item).show(); 
			
        });
    }		
	
	function editarConcluido(cd_cronograma_item)
	{
		$("#concluido_valor_" + cd_cronograma_item).hide(); 
		$("#concluido_editar_" + cd_cronograma_item).hide(); 

		$("#concluido_salvar_" + cd_cronograma_item).show(); 
		$("#fl_concluido_" + cd_cronograma_item).show(); 
		$("#fl_concluido_" + cd_cronograma_item).focus();	
	}	
	
	function configure_result_table(cd_cronograma)
	{
		if(document.getElementById("cronograma_" + cd_cronograma))
		{
			var ob_resul = new SortableTable(document.getElementById("cronograma_" + cd_cronograma),[
						null,
						"Number",
						null,
						"CaseInsensitiveString",
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
        location.href="<?php echo site_url('/atividade/info_cronograma/cadastro'); ?>";
    } 
</script>
<?php
$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');
$config['button'][] = array('Novo Mês', 'novo()');

echo aba_start($abas);    
    echo form_list_command_bar($config);
        echo form_start_box_filter('filter_bar', 'Filtros');
        echo filter_dropdown('cd_analista', 'Analista:*', $analista);
        echo form_default_mes_ano('nr_mes', 'nr_ano', 'A partir do mês/ano:');
    echo form_end_box_filter();
    
?>
<div id="result_div"><br><br><span style='color:green;'><b>Realize um filtro para exibir a lista</b></span></div>
<br>
<?php
echo aba_end('');
?>
<script>
if($('#cd_analista').val() != '')
{
    load();
} 
</script>
<?php
$this->load->view('footer');
?>