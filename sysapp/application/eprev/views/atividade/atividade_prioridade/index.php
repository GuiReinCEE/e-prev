<?php
	set_title('Atividade - Prioridade');
	$this->load->view('header');
?>
<script>
	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
		
		$.post("<?= site_url('atividade/atividade_prioridade/listar') ?>",
		$("#filter_bar_form").serialize(),
		function(data)
		{
			$("#result_div").html(data);
			configure_result_table();
		});
	}
	
	function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("tabela_atividades"),
        [
			'Number',
			'DateTimeBR',
			'CaseInsensitiveString', 
			'CaseInsensitiveString',			
			'CaseInsensitiveString', 
			'Number',
			null,
			'Number',
			'DateTimeBR', 
			'CaseInsensitiveString'
        ]);
        ob_resul.onsort = function ()
        {
            var rows = ob_resul.tBody.rows;
            var l = rows.length;
            for (var i = 0; i < l; i++)
            {
                removeClassName(rows[i], i % 2 ? "sort-par" : "sort-impar");
                addClassName(rows[i], i % 2 ? "sort-impar" : "sort-par");
            }
        };
        ob_resul.sort(5, false);
    }

    function reordenar()
    {
    	var ipts = $("#tabela_atividades>tbody").find("input");

    	var numero;
    	var status;
    	var prior_atual;
    	var i = 1;
    	var prior_prox = 0;

    	jQuery.each(ipts, function(index){
 			
			if(this.name == "ar_prioridade[]")
			{
				numero_os   = $(this).attr("data-numero");
				status      = $("#status_atividade_"+numero_os).val();
				prior_atual = this.value;
				resultado = $('#resultado').html();

				if(prior_atual > i)
				{
					if((status != "ETES") && (status != "EMAN"))
					{
						if(prior_prox > 0)
						{
							$(this).val(prior_prox);
							$(this).change();
							prior_prox = 0;
						}
						else
						{
							$(this).val(i);
							$(this).change();
						}
					}
					else
					{
						if(prior_prox == 0) 
						{
							prior_prox = i;
						}	
					}	
				}

				if((status != "ETES") && (status != "EMAN"))
				{
					i ++;
				}
				else if(prior_atual <= ipts.length)
				{
					i ++;
				}
			}
        });	

       	alert("Verifique a ordenação e clique em Salvar Prioridades(s)");
    }

	function set_prioridade()
	{
		var ar_prior = new Array();
        var ipts = $("#tabela_atividades>tbody").find("input");
		
        jQuery.each(ipts, function(){
 			
			if(this.name == "ar_prioridade[]")
			{
				ar_prior.push(this.value);
			}
        });			
		
		var fl_repetido = false;
		for(var i = 0; i < ar_prior.length; i++) 
		{
			var nr_ocorrenria = 0;
			
			for(var x = 0; x < ar_prior.length; x++) 
			{			
				if (ar_prior[i] == ar_prior[x])
				{
					nr_ocorrenria++;
				}
			}
			
			if(nr_ocorrenria > 1)
			{
				fl_repetido = true;
				break;
			}
		}		

		if(fl_repetido)
		{
			alert("ERRO\n\nExiste(m) prioridade(s) repetida(s)\n\n");
			return false;
		}
		else
		{
			var confirmacao = 'Confirma a definição de Prioridade(s) para(s) a(s) Atividade(s)?\n\n'+
				'Clique [Ok] para Sim\n\n'+
				'Clique [Cancelar] para Não\n\n';
			
			if(confirm(confirmacao))
			{
				$("#formSetPrioridadeAtividades").submit();
			}		
		}
	}
	
	$(function(){
		<?php
			if($fl_atendente_info)
			{
				echo '$("#cd_atendente_row").hide();';
			}
			else
			{
				echo '$("#cd_area_solicitante_row").hide();';
			}
		?>
		
		if(($("#cd_atendente").val() != "") && ($("#cd_area_solicitante").val() != ""))
		{
			filtrar();
		}
	});
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	echo aba_start($abas);
		echo form_list_command_bar();
		echo form_start_box_filter();
			echo form_default_hidden('fl_atividade_prior_editar', 'Editar', $fl_atividade_prior_editar);
			if(intval($cd_atendente) > 0)
			{
				echo filter_dropdown('cd_atendente', 'Atendente:(*)', $ar_atendente, $cd_atendente);
			}
			else
			{
				echo filter_dropdown('cd_atendente', 'Atendente:(*)', $ar_atendente);
			}

			if(trim($cd_area_solicitante) != '')
			{
				echo filter_dropdown('cd_area_solicitante', 'Área Solicitante:(*)', $ar_area_solicitante, $cd_area_solicitante);
			}
			else
			{
				echo filter_dropdown('cd_area_solicitante', 'Área Solicitante:(*)', $ar_area_solicitante);
			}
			
		echo form_end_box_filter();
		echo br();
		echo '
			<div id="result_div">
				<span class="label label-success">Realize um filtro para exibir a listar</span>
			</div>';
		echo br(2); 
	echo aba_end();
	
	$this->load->view('footer_interna');
?>