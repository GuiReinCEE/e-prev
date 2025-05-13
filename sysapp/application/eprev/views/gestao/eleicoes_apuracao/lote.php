<?php
set_title('Eleições - Lote');
$this->load->view('header');
?>
<script>
	function lista()
    {
        location.href='<?php echo site_url("gestao/eleicoes_apuracao"); ?>';
    }
	
	function ir_apuracao()
    {
        location.href='<?php echo site_url("gestao/eleicoes_apuracao/apuracao/".$id_eleicao); ?>';
    }
	
	function cancelar_lote(cd_lote)
	{
		var confirmacao = 'Deseja CANCELAR o lote nº '+cd_lote+'?\n\n'+
						  'Clique [Ok] para Sim\n\n'+
						  'Clique [Cancelar] para Não\n\n';	

		if(confirm(confirmacao))
		{		
			location.href = '<?php echo site_url("gestao/eleicoes_apuracao/cancelar_lote/".$id_eleicao); ?>' + "/" + cd_lote;
		}		
	}
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'lista();');
$abas[] = array('aba_lista', 'Apuração', FALSE, 'ir_apuracao()');
$abas[] = array('aba_lista', 'Lotes', TRUE, 'location.reload();');

$cd_lote = 0;

$this->load->helper('grid');
$grid = new grid();
$grid ->view_count = false;
$body = array();
$head = array(
	'Cargo',
	'Candidato',
	'Total de votos'
);

echo aba_start($abas);

	echo form_start_box("default_box", $row["nome"]);
		echo form_default_hidden('ds_eleicao', '', $row['nome']);
		echo form_default_hidden('id_eleicao', '', intval($row['id_eleicao']));
		echo form_default_hidden('cd_tipo', '', $row['cd_tipo']);
		echo form_default_text("dt_hr_abertura", "Dt Abertura:", $row, 'style="width:100px; border:0" readonly');
		echo (trim($row["dt_hr_fechamento"]) != "" ? form_default_text("dt_hr_fechamento", "Dt Encerramento:", $row, 'style="width:100px; border:0" readonly') : "");
		echo form_default_text("qt_cadastro", "Cadastro Eleitoral:", number_format($row["qt_cadastro"],0,",",""), 'style="width:100px; text-align:right; border:0; font-weight: bold;" readonly');
		echo form_default_text("qt_total_recebido", "Kits recebidos:", number_format($row["num_votos"],0,",",""), 'style="width:100px; text-align:right; border:0; color: blue; font-weight: bold;" readonly');
		
		echo form_default_row("", "Kits inválidos:", 
		form_input(array("name"=>"qt_total_invalido-total", "id"=>"qt_total_invalido-total", "style"=>"width:100px; text-align:right; border:0; color: red; font-weight: bold;"), number_format($row["invalidados"],0,",",""), "readonly"));			
		
		echo form_default_text("qt_total_apurado", "Votos válidos:", number_format($row["votos_apurados"],0,",",""), 'style="width:100px; text-align:right; border:0; color: green; font-weight: bold;" readonly');
		echo form_default_text("qt_kit_apurado", "Kits apurados:", number_format($row["votos_apurados"] + $row["invalidados"],0,",",""), 'style="width:100px; text-align:right; border:0; color: brown; font-weight: bold;" readonly');
	echo form_end_box("default_box");
 	 	

	for($i=0; $i < count($collection); $i++)
	{
		$body[] = array(
			array($collection[$i]["ds_cargo"],'text-align:left'),
			array($collection[$i]["ds_candidato"],'text-align:left'),
			array($collection[$i]["qt_total_candidato"],'text-align:right; font-weight:bold;')
		);
	
		if(intval($cd_lote) != intval($collection[$i]['cd_lote']))
		{
			echo form_start_box("default_box", 'LOTE Nº '.$collection[$i]['cd_lote'].(trim($collection[$i]['dt_cancela']) != '' ? ' (Cancelado)' : ''));
				
		}
		
		$cd_lote = $collection[$i]['cd_lote'];
		
		if(intval($i+1) < count($collection)) 
		{
			if(intval($cd_lote) != intval($collection[$i+1]['cd_lote']))
			{
					$grid->head = $head;
					$grid->body = $body;
					echo $grid->render();
					
					if($collection[$i]['dt_cancela'] != "")
					{
						echo '<span style="color:red; font-weight: bold;">Lote cancelado em '.$collection[$i]['dt_cancela'].' por '.$collection[$i]['ds_usuario'].'.</span>';
					}
					else
					{
						if(trim($collection[$i]['situacao']) == 'A')
						{
							echo button_save("Cancelar Lote","cancelar_lote(".intval($cd_lote).")","botao_vermelho");
						}
					}
				echo form_end_box("default_box");
				echo br();
				$body = array();	
			}
		}
		else
		{
				$grid->head = $head;
				$grid->body = $body;
				echo $grid->render();
				
				if($collection[$i]['dt_cancela'] != "")
				{
					echo '<span style="color:red; font-weight: bold;">Lote cancelado em '.$collection[$i]['dt_cancela'].' por '.$collection[$i]['ds_usuario'].'.</span>';
				}
				else
				{
					if(trim($collection[$i]['situacao']) == 'A')
					{
						echo button_save("Cancelar Lote","cancelar_lote(".intval($cd_lote).")","botao_vermelho");
					}
				}
			echo form_end_box("default_box");
			echo br();
		}
		
	}
echo aba_end();
$this->load->view('footer_interna');
?>