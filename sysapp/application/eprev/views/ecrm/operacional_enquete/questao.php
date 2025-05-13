<?php
set_title('Pesquisa - Questão');
$this->load->view('header');
?>
<script>
    <?php 
			echo form_default_js_submit(array(
												'ds_pergunta', 
												'cd_agrupamento',
												'nr_ordem',
												'r1',
												'r2',
												'r3',
												'r4',
												'r5',
												'r6',
												'r7',
												'r8',
												'r9',
												'r10',
												'r11',
												'r12',
												'r13',
												'r14',
												'r15',
												'r_diss',
												'r_justificativa'
											  )); 
	?>
    
	function questaoNovo()
	{
		location.href='<?= site_url("ecrm/operacional_enquete/questao/".intval($ar_cadastro['cd_enquete'])) ?>';
	}
	
    function ir_lista()
    {
        location.href='<?= site_url("ecrm/operacional_enquete") ?>';
    }

    function ir_cadastro()
    {
        location.href='<?= site_url("ecrm/operacional_enquete/cadastro/".intval($ar_cadastro['cd_enquete'])) ?>';
    }	
	
    function ir_estrutura()
    {
        location.href='<?= site_url("ecrm/operacional_enquete/estrutura/".intval($ar_cadastro['cd_enquete'])) ?>';
    }
	
    function ir_resultado()
    {
        location.href='<?= site_url("ecrm/operacional_enquete/resultado/".intval($ar_cadastro['cd_enquete'])) ?>';
    }

    function questaoExcluir()
    {
        var confirmacao = 'Confirma a EXCLUSÃO?\n\n'+
                'Clique [Ok] para Sim\n\n'+
                'Clique [Cancelar] para Não\n\n';

        if(confirm(confirmacao))
        {
           location.href='<?= site_url("ecrm/operacional_enquete/questaoExcluir/".intval($ar_cadastro['cd_enquete'])."/".intval($row['cd_pergunta'])) ?>';
        }
    }
	
	function setResposta(id_resposta)
	{
		$('#rotulo'+id_resposta+'_row').hide();
		$('#legenda'+id_resposta+'_row').hide();
		$('#r'+id_resposta+'_complemento_row').hide();
		$('#r'+id_resposta+'_complemento_rotulo_row').hide();		
		
		if($('#r'+id_resposta).val() == "S")
		{
			$('#rotulo'+id_resposta+'_row').show();
			$('#legenda'+id_resposta+'_row').show();
			$('#r'+id_resposta+'_complemento_row').show();
			setRespostaComplemento(id_resposta);
		}	
	}
	
	function setRespostaComplemento(id_resposta)
	{
		$('#r'+id_resposta+'_complemento_rotulo_row').hide();
		if($('#r'+id_resposta+'_complemento').val() == "S")
		{
			$('#r'+id_resposta+'_complemento_rotulo_row').show();
		}
	}
	
	function setDissertativa()
	{
		$('#rotulo_dissertativa_row').hide();
		if($('#r_diss').val() == "S")
		{
			$('#rotulo_dissertativa_row').show();
		}
	}

	function setJustificativa()
	{
		$('#rotulo_justificativa_row').hide();
		if($('#r_justificativa').val() == "S")
		{
			$('#rotulo_justificativa_row').show();
		}
	}		
	
    $(function() {
		<?php
			for($i=1; $i<=15; $i++)
			{
				echo "
						setResposta(".$i.");

						$('#r".$i."').change(function(){ 
							setResposta(".$i.");
						});
						
						$('#r".$i."_complemento').change(function(){ 
							setRespostaComplemento(".$i.");
						});						
				     ";
			}		
		?>
		
		setDissertativa();
		$('#r_diss').change(function(){ 
			setDissertativa();
		});		
		
		setJustificativa();
		$('#r_justificativa').change(function(){ 
			setJustificativa();
		});			
    });	
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_estrutura', 'Estrutura', FALSE, 'ir_estrutura();');
$abas[] = array('aba_questao', 'Questão', TRUE, 'location.reload();');
$abas[] = array('aba_resultado', 'Resultados', FALSE, 'ir_resultado();');

$ar_flag[] = Array("value" => "S", "text" => "Sim");
$ar_flag[] = Array("value" => "N", "text" => "Não");

echo aba_start( $abas );
    echo form_open('ecrm/operacional_enquete/questaoSalvar');
        echo form_start_box("default_box", "Cadastro");
			echo form_default_hidden('cd_enquete', '', $ar_cadastro['cd_enquete']);
			echo form_default_hidden('cd_pergunta', '', $row['cd_pergunta']);
			echo form_default_row('cd_enquete_label', "Pesquisa:", '<span class="label label-success">'.$ar_cadastro["cd_enquete"].' - '.$ar_cadastro["ds_titulo"].'</span>');
			
			echo form_default_textarea('ds_pergunta', 'Pergunta:(*)', $row, 'style="height: 80px;"');
			echo form_default_dropdown('cd_agrupamento', 'Agrupamento:(*)', $ar_agrupamento, Array($row['cd_agrupamento']));
			echo form_default_integer('nr_ordem', 'Ordem:(*)', $row);
			
			echo form_default_row("","","<hr>");
			
			$nr_conta = 1;
			while($nr_conta <= 15)
			{
				echo form_default_dropdown('r'.$nr_conta,  '<b>Resposta '.$nr_conta.':</b>(*)',  $ar_flag, Array($row['r'.$nr_conta]));
				echo form_default_text('rotulo'.$nr_conta, 'Rótulo:', $row, 'style="width: 250px;"');
				echo form_default_text('legenda'.$nr_conta, 'Legenda gráfico:', $row, 'style="width: 250px;"');
				echo form_default_dropdown('r'.$nr_conta.'_complemento',  'Complemento:',  $ar_flag, Array($row['r'.$nr_conta.'_complemento']));
				echo form_default_text('r'.$nr_conta.'_complemento_rotulo', 'Rótulo Complemento:', $row, 'style="width: 250px;"');
				echo form_default_row("","","<hr>");
				$nr_conta++;
			}
			
			echo form_default_dropdown('r_diss',  '<b>Outra resposta:</b>(*)',  $ar_flag, Array($row['r_diss']));
			echo form_default_text('rotulo_dissertativa', 'Rótulo:', $row, 'style="width: 400px;"');
			echo form_default_row("","","<hr>");

			echo form_default_dropdown('r_justificativa',  '<b>Justificativa (por quê):</b>(*)',  $ar_flag, Array($row['r_justificativa']));
			echo form_default_text('rotulo_justificativa', 'Rótulo:', $row, 'style="width: 400px;"');
			echo form_default_row("","","<hr>");			

			
        echo form_end_box("default_box");
		
        echo form_command_bar_detail_start();    
			if(trim($ar_cadastro['fl_editar']) == "S")
			{
				

				echo button_save("Nova Questão", "questaoNovo()", "botao_verde");
				
				echo button_save("Salvar");
				
				if(intval($row['cd_pergunta']) > 0)
				{
					echo button_save("Excluir", "questaoExcluir()", "botao_vermelho");
				}
            }
        echo form_command_bar_detail_end();
		
    echo form_close();
    echo br(5);	
echo aba_end();

$this->load->view('footer_interna');
?>