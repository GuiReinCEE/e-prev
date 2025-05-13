<?php
$head = array(
  'Prioridade',
  '',
  'Descrição',
  'Restam',
  'Dt Limite',
  'Concluído',
  '',
  'Observação'
);

$this->load->helper('grid');
$grid = new grid();
$grid->view_count = false;
$grid->head = $head;

echo '<div style="text-align:left">';
foreach ($collection as $item)
{
    $body = array();
    
	echo "
			<BR>
			<fieldset style='padding: 10px;'>
			<legend>".anchor('atividade/cronograma_investimento/cadastro/'.$item['cd_cronograma_investimento'],$item['mes_ano'],"style='margin-left: 5px; margin-right: 5px; font-family: Calibri, Arial;font-size: 20pt;font-weight: bold;'")."</legend>
			
			
		 ";
    $nr_sim = $item['tl_item_concluido'];
    $nr_tot = $item['tl_item'];
	foreach($item['item'] as $item2)
    {		
		$config = array(
						"name"=>"nr_prioridade_".$item2['cd_cronograma_investimento_item'], 
						"id"=>"nr_prioridade_".$item2['cd_cronograma_investimento_item'],
						"onblur"=>"setPrioridade(".$item['cd_cronograma_investimento'].", ".$item2['cd_cronograma_investimento_item'].");",
						"style"=>"display:none; width:50px;"
						);
						
        $config_sel = array(
						"id"=>"fl_concluido_".$item2['cd_cronograma_investimento_item'],
						"onblur"=>"setStatus(".$item['cd_cronograma_investimento'].", ".$item2['cd_cronograma_investimento_item'].");",
						"style"=>"display:none; width:50px;"
						);
						
		$body[] = array(
			'<span id="ajax_prioridade_valor_'.$item2['cd_cronograma_investimento_item'].'"></span> '.'<span id="prioridade_valor_'.$item2['cd_cronograma_investimento_item'].'">'.$item2['nr_prioridade'].'</span>'
			.form_input($config,$item2['nr_prioridade'])."<script> jQuery(function($){ $('#cd_cronograma_investimento_item_".$item2['cd_cronograma_investimento_item']."').numeric(); }); </script>"
			,
			'<a id="prioridade_editar_'.$item2['cd_cronograma_investimento_item'].'" href="javascript: void(0)" onclick="editarPrioridade('.$item2['cd_cronograma_investimento_item'].');" title="Editar a prioridade">[editar]</a>'
			.'<a id="prioridade_salvar_'.$item2['cd_cronograma_investimento_item'].'" href="javascript: void(0)" style="display:none" title="Salvar a prioridade">[salvar]</a>'
			,
			array(anchor('atividade/cronograma_investimento/item/'.$item['cd_cronograma_investimento'].'/'.$item2['cd_cronograma_investimento_item'],nl2br($item2['descricao'])),'text-align:left'),
			
	
			(trim($item2['fl_concluido']) == 'N'  ? '<span class="label '.(intval($item2['restam']) <= 3 ? 'label-important' : 'label-success').'">'.$item2['restam'].'</span>' : ''),
			$item2['dt_limite'],
			'<span id="ajax_concluido_valor_'.$item2['cd_cronograma_investimento_item'].'"></span> '.($item2['fl_concluido'] == 'S' ? '<span id="concluido_valor_'.$item2['cd_cronograma_investimento_item'].'" class="label label-info";>Sim</span>' : '<span id="concluido_valor_'.$item2['cd_cronograma_investimento_item'].'" class="label label-important">Não</span>')
			
			.
			form_dropdown("fl_concluido_".$item2['cd_cronograma_investimento_item'], Array('S'=> 'Sim', 'N' => 'Não'), array($item2['fl_concluido']))
			."<script> 
					$('#fl_concluido_".$item2['cd_cronograma_investimento_item']."').hide();
					$('#fl_concluido_".$item2['cd_cronograma_investimento_item']."').blur(function() {  setConcluido(".$item['cd_cronograma_investimento'].", ".$item2['cd_cronograma_investimento_item']."); });
			</script>"
			,
			'<a id="concluido_editar_'.$item2['cd_cronograma_investimento_item'].'" href="javascript: void(0)" onclick="editarConcluido('.$item2['cd_cronograma_investimento_item'].');" title="Editar o Status">[editar]</a>'
			.'<a id="concluido_salvar_'.$item2['cd_cronograma_investimento_item'].'" href="javascript: void(0)" style="display:none" title="Salvar o Status">[salvar]</a>'
			,			
			array(nl2br($item2['observacao']),'text-align:justify')
        );
    }

	$percentual = 0;
	if($nr_tot > 0)
	{
		echo progressbar(intval( ($nr_sim * 100) / $nr_tot )).br(2);
	}
	
    
	$grid->id_tabela = "cronograma_".$item['cd_cronograma_investimento'];
	$grid->body = $body;
	$grid->w_detalhe = true;
	$grid->w_detalhe_col_iniciar = 2;	
	echo $grid->render();
	

	
	echo "
			</fieldset>
			<BR>
		";
	echo '	
			<script>
				configure_result_table('.$item['cd_cronograma_investimento'].');
			</script>
		';
}
echo "
		</div>
		<BR>
	 ";
?>