<?php
set_title('Programas e Projetos');
$this->load->view('header');
?>
<script>
	<?php
		echo form_default_js_submit(array(), 'form_valida(form)');
	?>

	function form_valida(form)
	{
		var fl_marcado = false;

		$("input[type='checkbox'][id='indicador']").each( 
			function() 
			{ 
				if (this.checked) 
				{ 
					fl_marcado = true;
				} 
			}
		);	

		if(!fl_marcado)
		{
			alert("Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação.)");
			return false;
		}
		else
		{
			if(confirm("Salvar?"))
			{
				form.submit();
			}
		}	
	}

	function ir_lista()
	{
		location.href = "<?= site_url('gestao/projeto') ?>";
	}

	function ir_cadastro()
	{
		location.href = "<?= site_url('gestao/projeto/cadastro/'.$row['cd_projeto']) ?>";
	}

	function ir_custo()
	{
		location.href = "<?= site_url('gestao/projeto/custo/'.$row['cd_projeto']) ?>";
	}

	function ir_cronograma()
	{
		location.href = "<?= site_url('gestao/projeto/cronograma/'.$row['cd_projeto']) ?>";
	}

	$(function(){
        $("#FilterTextBox_table").keypress(function(event){
            if (event.keyCode == 13) 
            {
                event.preventDefault();
                return false;
            }
        });  

        $("#indicador_table tbody tr:has(td)").each(function(){
            var t = $(this).text().toLowerCase();
            $("<td class=\'indexColumn\' style=\'display:none;\'></td>").hide().text(removeAccents_table(t)).appendTo(this);
        });

        $("#FilterTextBox_table").keyup(function(event){
            if (event.keyCode == 27) 
            {
                $("#FilterTextBox_table").val("").keyup();
            }
            else
            {
                var s = $(this).val();
                    s = removeAccents_table(s);
                    s = s.toLowerCase().split(" ");
                    

                $("#indicador_table tbody tr:hidden").show();
                $.each(s, function(){
                    $("#indicador_table tbody tr:visible .indexColumn:not(:contains(\'"+ this + "\'))").parent().hide();
                });
                
                var rowCount = $("#indicador_table tbody tr:visible").length;
                $("#gridCount'.$this->id_tabela.'").html(rowCount);
            }
        });
    });
</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
	$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
	$abas[] = array('aba_custo', 'Indicador', TRUE, 'location.reload();');
	$abas[] = array('aba_indicador', 'Custos Projetados', FALSE, 'ir_custo();');
	$abas[] = array('aba_cronograma', 'Cronograma', FALSE, 'ir_cronograma();');

	echo aba_start($abas);
		echo form_open('gestao/projeto/salvar_indicador');
			echo form_start_box('default_box', 'Projeto');
				echo form_default_hidden('cd_projeto', '', $row);	
				echo form_default_row('ds_projeto', 'Projeto :', $row['ds_projeto'], 'style="width:350px;"');
			echo form_end_box('default_box');
			
			echo form_start_box('default_indicador_box', 'Indicadores');
				echo form_default_row('', '', '<div>Procurar: <input type="text" id="FilterTextBox_table" name="FilterTextBox" style="width: 400px;"></div>');
				echo form_default_checkbox_group('indicador', 'Indicadores :*', $indicador, $indicador_checked, 200, 700);
				echo form_default_textarea('ds_indicador', 'Indicador :', $row);
			echo form_end_box('default_indicador_box');
			
			echo form_command_bar_detail_start();
				if($this->session->userdata('divisao') == $row['cd_gerencia_resposanvel'])
				{
					echo button_save('Salvar');	
				}
			echo form_command_bar_detail_end();
		echo form_close();
		echo br(2);
	echo aba_end();
	$this->load->view('footer_interna');
?>