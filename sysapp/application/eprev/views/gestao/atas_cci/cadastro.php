<?php
set_title('Atas CCI - Cadastro');
$this->load->view('header');
?>
<script>
<?php echo form_default_js_submit(Array('nr_reuniao', 'dt_reuniao'), 'valida_data()'); ?>
    
function ir_lista()
{
	location.href='<?php echo site_url("gestao/atas_cci"); ?>';
}   

function valida_data()
{
	if(($('#dt_ata_cci').val() != '')  || ($('#dt_sumula_cci').val() != '')  || ($('#dt_anexo_cci').val() != ''))
	{
		$('form').submit();

	}
	else
	{
		alert('Informe uma das seguinte datas:\nDt Ata CCI, Dt Súmula CCI, Dt Anexos CCI.');
		return false;
	}
}

function seleciona_homologado_conselho_fiscal(homologado_conselho_fiscal)
{
	if(homologado_conselho_fiscal == 'S')
	{
		$("#dt_homologado_conselho_fiscal_row").show();
		$("#nr_ata_conselho_fiscal_row").show();
	}
	else
	{
		$("#dt_homologado_conselho_fiscal_row").hide();
		$("#dt_homologado_conselho_fiscal").val('');
		$("#nr_ata_conselho_fiscal_row").hide();
		$("#nr_ata_conselho_fiscal").val('');
	}
}

function excluir()
{
	var confirmacao = 'Deseja excluir?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';
	
	if(confirm(confirmacao))
	{
	   location.href='<?php echo site_url("gestao/atas_cci/excluir/".intval($row['cd_atas_cci'])); ?>';
	}
}
	
function ir_acompanhamento()
{
	location.href='<?php echo site_url("gestao/atas_cci/acompanhamento/".intval($row['cd_atas_cci'])); ?>';
}

function lista_etapas_investimento()
{
	$('#ob_etapas_investimento').html("<?php echo loader_html(); ?>");

    $.post( '<?php echo site_url('/gestao/atas_cci/lista_etapas_investimento') ?>',
    {
		cd_atas_cci : $('#cd_atas_cci').val()
    },
    function(data)
    {
		$('#ob_etapas_investimento').html(data);
    });
}

$(function(){
	lista_etapas_investimento();
	
	seleciona_homologado_conselho_fiscal('<?= trim($row['fl_homologado_conselho_fiscal']) ?>');

});

function checked_etapa(i, total)
{
	var etapas = [];

	bol_checked = $('#etapa_'+i).is(':checked');
	
	if(bol_checked)
	{		
		for(j=0; j < i; j++)
		{
			if(!$('#etapa_'+j).is(':checked'))
			{	
				$('#etapa_'+j).attr('checked','checked');
			}
		}
	}
	
	for(j=0; j < total; j++)
	{
		if($('#etapa_'+j).is(':checked'))
		{
			etapas.push($('#etapa_'+j).val());
		}
	}
	
	$.post( '<?php echo site_url('/gestao/atas_cci/checked_etapa') ?>',
    {
		"etapas[]"  : etapas,
		cd_atas_cci : $('#cd_atas_cci').val()
    },
    function(data)
    {
		
    });
	
}

</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');

if ($row['cd_atas_cci'] > 0) 
{
	$abas[] = array('aba_lista', 'Acompanhamento', FALSE, 'ir_acompanhamento();');
}

$arr_dropdown[] = array('value' => 'S', 'text' => 'Sim');
$arr_dropdown[] = array('value' => 'N', 'text' => 'Não');

$arr_homologacao[] = array('value' => 'S', 'text' => 'Se Aplica');
$arr_homologacao[] = array('value' => 'N', 'text' => 'Não se Aplica');

	$body = array();
	$head = array(
		'Dt Inclusão',
		'Protocolo',
		'Situação',
		'#'
	);

	foreach($assinatura as $item )
	{
		$FL_CHECK_RECUSA = FALSE;
		if ($item['fl_status'] == 'RUNNING')
		{
			$FL_CHECK_RECUSA      = TRUE;
		}
		
		$body[] = array(
			$item["dt_inclusao"],
			$item["id_doc"],
			'<span class="'.$item["cor_status"].'">'.$item["ds_status"].'</span>',
			anchor("https://www.fcprev.com.br/fundacaofamilia/index.php/assinatura_documento/index/".$item["id_doc"], "[consultar situação]", array('target' => "_blank")),
		);
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->view_count = false;
	$grid->body = $body;

echo aba_start( $abas );
    echo form_open('gestao/atas_cci/salvar', 'name="filter_bar_form"');
		
		if(intval($row['cd_atas_cci']) > 0)
		{
			echo form_start_box("default_ass_box", "Assinatura" );
				echo $grid->render();
			echo form_end_box("default_ass_box");
		}
	
        echo form_start_box( "default_box", "Cadastro" );
			echo form_default_hidden('cd_atas_cci', '', $row);
			
			if(gerencia_in(array('GC')))
			{
				echo form_default_text('nr_reuniao', 'Nº Reunião:*', $row);
				echo form_default_date('dt_reuniao', 'Dt Reunião:*', $row);
				if(trim($row['fl_ata_cci']) != 'S')
				{
					echo form_default_date('dt_ata_cci', 'Dt Ata CCI:', $row);
				}
				else
				{
					echo form_default_text('dt_ata_cci', "Dt Ata CCI:", $row, "style='width:100%;border: 0px;' readonly");
				}
				if(trim($row['fl_sumula_cci']) != 'S')
				{
					echo form_default_date('dt_sumula_cci', 'Dt Súmula CCI:', $row);
				}
				else
				{
					echo form_default_text('dt_sumula_cci', "Dt Súmula CCI:", $row, "style='width:100%;border: 0px;' readonly");
				}
				
				if(trim($row['fl_anexo_cci']) != 'S')
				{
					echo form_default_date('dt_anexo_cci', 'Dt Anexos CCI:', $row);
				}
				else
				{
					echo form_default_text('dt_anexo_cci', "Dt Anexos CCI:", $row, "style='width:100%;border: 0px;' readonly");
				}
				
				echo form_default_dropdown('fl_ata_cci', 'Ata CCI :', $arr_dropdown, array($row['fl_ata_cci']));
				echo form_default_dropdown('fl_sumula_cci', 'Súmula CCI :', $arr_dropdown, array($row['fl_sumula_cci']));
				echo form_default_dropdown('fl_anexo_cci', 'Anexos CCI :', $arr_dropdown, array($row['fl_anexo_cci']));
				echo form_default_date('dt_homologado_diretoria', 'Dt Homologado DE :', $row);
				echo form_default_text('nr_ata_diretoria', 'Ata DE :', $row);
				echo form_default_dropdown('fl_homologado_conselho_fiscal', 'Homologação CD :', $arr_homologacao,  $row['fl_homologado_conselho_fiscal'], 'onchange="seleciona_homologado_conselho_fiscal(this.value)"');
				echo form_default_date('dt_homologado_conselho_fiscal', 'Dt Homologado CD :', $row);
				echo form_default_integer('nr_ata_conselho_fiscal', 'Ata CD :', $row);
				echo form_default_dropdown('fl_publicado_alchemy', 'Publicado no Liquid :', $arr_dropdown, array($row['fl_publicado_alchemy']));
				echo form_default_dropdown('fl_publicado_eprev', 'Publicado no E-prev :', $arr_dropdown, array($row['fl_publicado_eprev']));
				
			}
			else
			{
				echo form_default_text('nr_reuniao', "Nº Reunião:", $row, "style='width:100%;border: 0px;' readonly");
				echo form_default_text('dt_reuniao', "Dt Reunião:", $row, "style='width:100%;border: 0px;' readonly");
				if(trim($row['fl_ata_cci']) == 'S')
				{
					echo form_default_text('dt_ata_cci', "Dt Ata CCI:", $row, "style='width:100%;border: 0px;' readonly");
				}
				
				if(trim($row['fl_sumula_cci']) == 'S')
				{
					echo form_default_text('dt_sumula_cci', "Dt Súmula CCI:", $row, "style='width:100%;border: 0px;' readonly");
				}
				
				if(trim($row['fl_anexo_cci']) == 'S')
				{
					echo form_default_text('dt_anexo_cci', "Dt Anexos CCI:", $row, "style='width:100%;border: 0px;' readonly");
				}
				
				echo form_default_hidden('fl_ata_cci', "Ata CCI:", $row['fl_ata_cci']);
				echo form_default_text('row_ata_cci', "Ata CCI:", ($row['fl_ata_cci'] == 'S' ? 'Sim' : ($row['fl_ata_cci'] == 'N' ? 'Não' : '')), "style='width:100%;border: 0px;' readonly");
				
				echo form_default_hidden('fl_sumula_cci', "Súmula CCI:", $row['fl_sumula_cci']);
				echo form_default_text('row_sumula_cci', "Súmula CCI:", ($row['fl_sumula_cci'] == 'S' ? 'Sim' : ($row['fl_sumula_cci'] == 'N' ? 'Não' : '')), "style='width:100%;border: 0px;' readonly");
				
				echo form_default_hidden('fl_anexo_cci', "Anexos CCI:", $row['fl_anexo_cci']);
				echo form_default_text('row_anexo_cci', "Anexos CCI:", ($row['fl_anexo_cci'] == 'S' ? 'Sim' : ($row['fl_anexo_cci'] == 'N' ? 'Não' : '')), "style='width:100%;border: 0px;' readonly");
				
				echo form_default_text('dt_homologado_diretoria', "Dt Homologado DE:", $row, "style='width:100%;border: 0px;' readonly");
				echo form_default_text('nr_ata_diretoria', "Ata DE:", $row, "style='width:100%;border: 0px;' readonly");
				
				echo form_default_hidden('fl_publicado_alchemy', "Publicado no Alchemy:", $row['fl_publicado_alchemy']);
				echo form_default_text('row_publicado_alchemy', "Publicado no Alchemy:", ($row['fl_publicado_alchemy'] == 'S' ? 'Sim' : ($row['fl_publicado_alchemy'] == 'N' ? 'Não' : '')), "style='width:100%;border: 0px;' readonly");
				
				echo form_default_hidden('fl_publicado_eprev', "Publicado no E-prev:", $row['fl_publicado_eprev']);
				echo form_default_text('row_publicado_eprev', "Publicado no E-prev:", ($row['fl_publicado_eprev'] == 'S' ? 'Sim' : (trim($row['fl_publicado_eprev']) == 'N' ? 'Não' : '')), "style='width:100%;border: 0px;' readonly");
				
			}
			
			if(gerencia_in(array('GIN')))
			{
				echo form_default_dropdown('cd_responsavel_investimento', 'Responsável GIN:', $ar_responsavel_gin, array($row['cd_responsavel_investimento']));
			}
			else
			{
				echo form_default_hidden('cd_responsavel_investimento', 'Responsável GIN:', $row);
				foreach($ar_responsavel_gin as $ar_tmp)
				{
					if($ar_tmp["value"] == $row['cd_responsavel_investimento'])
					{
						echo form_default_row('', 'Responsável GIN:', $ar_tmp["text"]);
						break;
					}
				}
			}
			
			echo form_default_row('', 'Etapas GIN:', '<div id="ob_etapas_investimento"></div>');
			
        echo form_end_box("default_box");
		
        echo form_command_bar_detail_start();     
            echo button_save("Salvar");
			if((intval($row['cd_atas_cci']) > 0) and (gerencia_in(array('GC'))))
			{
				echo button_save("Excluir", "excluir()", "botao_vermelho");
			}
        echo form_command_bar_detail_end();
    echo form_close();
    echo br(5);
echo aba_end();

$this->load->view('footer_interna');
?>