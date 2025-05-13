<script>
	function alterar_motivo(cd_registro_empregado)
    {
        $("#ajax_motivo_valor_" + cd_registro_empregado).html("<?= loader_html('P') ?>");

        $.post("<?= site_url('ecrm/seguro_obito/altera_motivo') ?>",
        {
            cd_registro_empregado : cd_registro_empregado,
            ds_motivo_pendencia : $("#nr_motivo_" + cd_registro_empregado).val()	
        },
        function(data)
        {
			$("#ajax_motivo_valor_" + cd_registro_empregado).empty();
			
			$("#nr_motivo_" + cd_registro_empregado).hide();
			$("#motivo_salvar_" + cd_registro_empregado).hide(); 
			
            $("#motivo_valor_" + cd_registro_empregado).html($("#nr_motivo_" + cd_registro_empregado).val()); 
			$("#motivo_valor_" + cd_registro_empregado).show(); 
			$("#motivo_editar_" + cd_registro_empregado).show();
        });
    }	
	
	function editar_motivo(cd_registro_empregado)
	{
		$("#motivo_valor_" + cd_registro_empregado).hide(); 
		$("#motivo_editar_" + cd_registro_empregado).hide(); 

		$("#motivo_salvar_" + cd_registro_empregado).show(); 
		$("#nr_motivo_" + cd_registro_empregado).show(); 
		$("#nr_motivo_" + cd_registro_empregado).focus();	
	}
</script>

<?php
	$head = array(
		'RE',
		'Nome',
		'Dt. Inclusão',
		'Dt. Confirmação',
		'Usuário Confirmação',
		'Motivo da Pendência',
		'',
		''
	);

	$body = array();

	foreach ($collection as $item)
	{	
		$config = array(
			'name'   => 'nr_motivo_'.$item['cd_registro_empregado'], 
			'id'     => 'nr_motivo_'.$item['cd_registro_empregado'],
			'onblur' => 'alterar_motivo('.$item['cd_registro_empregado'].');',
			'style'  => 'display:none;',
			'rows'   => '5',
			'cols'   => '50'
		);
		
		$body[] = array(
			$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
			array($item['nome'],'text-align: left;'),
			'<span class="label label-warning">'.$item['dt_inclusao'].'</span>',
			'<span class="label label-success">'.$item['dt_confirmacao'].'</span>',
			'<span class="label label-success">'.$item['usuario_confirmacao'].'</span>',
			array('<span id="ajax_motivo_valor_'.$item['cd_registro_empregado'].'"></span> '.
			'<span id="motivo_valor_'.$item['cd_registro_empregado'].'">'.$item['ds_motivo_pendencia'].'</span>'.
			form_textarea($config, $item['ds_motivo_pendencia']), 'text-align:justify;'),
			($item['dt_confirmacao'] == '' ? '<a id="motivo_editar_'.$item['cd_registro_empregado'].'" href="javascript:void(0);" onclick="editar_motivo('.$item['cd_registro_empregado'].');" title="Editar Motivo">[motivo pendência]</a>'.
			'<a id="motivo_salvar_'.$item['cd_registro_empregado'].'" href="javascript:void(0);" style="display:none;" title="Salvar Motivo">[salvar]</a>' : ''),
			anchor('ecrm/seguro_obito/formulario/'.$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'], '[formulário]', array('target' => '_blank')).' '.
			($item['usuario_confirmacao'] == '' ? '<a href="javascript:void(0)" onclick="confirmar('.$item['cd_empresa'].','.$item['cd_registro_empregado'].','.$item['seq_dependencia'].')">[confirmar]</a>' : '')
		); 
	}

	$this->load->helper('grid');
	$grid = new grid();
	$grid->head = $head;
	$grid->body = $body;
	echo $grid->render();
?>