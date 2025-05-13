<?php
set_title('Pauta CCI - Assuntos');
$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array(
		'nr_item',
		'cd_gerencia_responsavel',
		'cd_usuario_responsavel',
		'cd_gerencia_substituto',
		'cd_usuario_substituto',
		'ds_recomendacao',
		'ds_pauta_cci_assunto'
	)) ?>

	function ir_lista()
	{
		location.href = '<?= site_url('gestao/pauta_cci') ?>';
	}

	function ir_cadastro()
	{
		location.href = '<?= site_url('gestao/pauta_cci/cadastro/'.$row['cd_pauta_cci']) ?>';
	}
	function cancelar()
	{
		location.href = "<?= site_url('gestao/pauta_cci/assunto/'.$row['cd_pauta_cci']) ?>";
	}

	function pauta()
	{
		window.open("<?= site_url('gestao/pauta_cci/pauta/'.$row['cd_pauta_cci']) ?>");
	}

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById('table-1'),
		[   
			'Number',
			null,
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',	    
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'CaseInsensitiveString',
		    'Number',
		    null
		]);

		ob_resul.onsort = function ()
		{
			var rows = ob_resul.tBody.rows;
			var l = rows.length;
			for (var i = 0; i < l; i++)
			{
				removeClassName( rows[i], i % 2 ? 'sort-par' : 'sort-impar' );
				addClassName( rows[i], i % 2 ? 'sort-impar' : 'sort-par' );
			}
		};
		ob_resul.sort(0, false);
	}
		
	function get_usuarios(cd_gerencia, campo)
	{
		$.post("<?= site_url('gestao/pauta_cci/get_usuarios') ?>",
		{
			cd_gerencia : cd_gerencia
		},
		function(data)
		{
			if(campo == 0)
			{
				var select = $('#cd_usuario_responsavel'); 
			}
			else if(campo == 1)
			{
				var select = $('#cd_usuario_substituto'); 
			}
			
			if(select.prop) 
			{
				var options = select.prop('options');
			}
			else
			{
				var options = select.attr('options');
			}
			
			$('option', select).remove();
			
			options[options.length] = new Option('Selecione', '');
			
			$.each(data, function(val, text) {
				options[options.length] = new Option(text.text, text.value);
			});
			
		}, 'json', true);
	}

	
   	function editar_ordem(cd_pauta_cci_assunto)
	{
		$("#ordem_valor_"  + cd_pauta_cci_assunto).hide(); 
		$("#ordem_editar_" + cd_pauta_cci_assunto).hide(); 

		$("#ordem_salvar_" + cd_pauta_cci_assunto).show(); 
		$("#nr_item"       + cd_pauta_cci_assunto).show(); 
		$("#nr_item"       + cd_pauta_cci_assunto).focus();	
	}

	function set_ordem(cd_pauta_cci, cd_pauta_cci_assunto)
    {
        $("#ajax_ordem_valor_" + cd_pauta_cci_assunto).html("<?= loader_html("P") ?>");

        $.post("<?= site_url('gestao/pauta_cci/set_ordem') ?>/" + cd_pauta_cci_assunto,
        {
            nr_item : $("#nr_item" + cd_pauta_cci_assunto).val()	
        },
        function(data)
        {
			$("#ajax_ordem_valor_" + cd_pauta_cci_assunto).empty();
			
			$("#nr_item"+ cd_pauta_cci_assunto).hide();
			$("#ordem_salvar_"+ cd_pauta_cci_assunto).hide(); 
			
            $("#ordem_valor_"+ cd_pauta_cci_assunto).html($("#nr_item" + cd_pauta_cci_assunto).val()); 
			$("#ordem_valor_"+ cd_pauta_cci_assunto).show(); 
			$("#ordem_editar_"+ cd_pauta_cci_assunto).show(); 
			
        });
    }

    function aprovar()
	{
		var confirmacao = 'Deseja encerrar a pauta?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('gestao/pauta_cci/aprovar/'.$row['cd_pauta_cci']) ?>';
		}
	}	

	function reabrir()
	{
		var confirmacao = 'Deseja reabrir a pauta?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('gestao/pauta_cci/reabrir/'.$row['cd_pauta_cci']) ?>';
		}
	}

	function excluir(cd_pauta_cci_assunto)
	{
		var confirmacao = 'Deseja excluir o assunto da pauta?\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('gestao/pauta_cci/assunto_excluir/'.$row['cd_pauta_cci']) ?>/' + cd_pauta_cci_assunto;
		}
	}

	function remover(cd_pauta_cci_assunto)
	{
		var confirmacao = 'Deseja remover o assunto da pauta?\n\n'+
			'Removendo o assunto o mesma será incluído na próxima pauta\n\n'+
			'Clique [Ok] para Sim\n\n'+
			'Clique [Cancelar] para Não\n\n';

		if(confirm(confirmacao))
		{ 
			location.href = '<?= site_url('gestao/pauta_cci/assunto_remover/'.$row['cd_pauta_cci']) ?>/' + cd_pauta_cci_assunto;
		}
	}
    
    $(function(){
		configure_result_table();
	})
     
</script>

<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', FALSE, 'ir_cadastro();');
$abas[] = array('aba_assunto', 'Assuntos', TRUE, 'location.reload();');

$head = array(
	'Número do Item',
	'',
	'Assunto',
	'Gerência Responsável',
	'Responsável',
	'Gerência Substituto',
	'Substituto',
	'Recomendação',
	'Qt. Arquivos',
	''
);

$body = array();


foreach($collection as $item)
{
	if((trim($row['dt_aprovacao']) != '') OR (trim($item['dt_removido']) != ''))
	{
		$fl_editar = false;
	}
	else
	{
		$fl_editar = true;
	}
    
	$link = 
		anchor('gestao/pauta_cci/anexo/'.$row['cd_pauta_cci'].'/'.$item['cd_pauta_cci_assunto'], '[arquivos]').'<br/>'.
		'<a href="javascript:void(0)" onclick="excluir('.$item['cd_pauta_cci_assunto'].')">[excluir]</a><br>'.
		'<a href="javascript:void(0)" onclick="remover('.$item['cd_pauta_cci_assunto'].')">[remover da pauta]</a>';;

	if(trim($item['dt_removido']) != '')
	{
		$link = 'Removido da Pauta';
	}
	else if(trim($row['dt_aprovacao']) != '')
	{
		$link = anchor('gestao/pauta_cci/anexo/'.$row['cd_pauta_cci'].'/'.$item['cd_pauta_cci_assunto'], '[arquivos]');
	}

	$config = array(
		'name'   => 'nr_item'.$item['cd_pauta_cci_assunto'], 
		'id'     => 'nr_item'.$item['cd_pauta_cci_assunto'],
		'onblur' => "set_ordem(".$row['cd_pauta_cci'].", ".$item['cd_pauta_cci_assunto'].");",
		'style'  => "display:none; width:50px;"
	);

	$body[] = array(
		'<span id="ajax_ordem_valor_'.$item['cd_pauta_cci_assunto'].'"></span> '.'<span id="ordem_valor_'.$item['cd_pauta_cci_assunto'].'">'.$item['nr_item'].'</span>'.
		form_input($config, $item['nr_item'])."<script> jQuery(function($){ $('#cd_pauta_cci_assunto_".$item['cd_pauta_cci_assunto']."').numeric(); }); </script>",
		($fl_editar ? 
		'<a id="ordem_editar_'.$item['cd_pauta_cci_assunto'].'" href="javascript: void(0)" onclick="editar_ordem('.$item['cd_pauta_cci_assunto'].');" title="Editar a ordem">[editar]</a>
		<a id="ordem_salvar_'.$item['cd_pauta_cci_assunto'].'" href="javascript: void(0)" style="display:none" title="Salvar a ordem">[salvar]</a>' : ''),

		array(
			nl2br(
			($fl_editar 
				? anchor('gestao/pauta_cci/assunto/'.$row['cd_pauta_cci'].'/'.$item['cd_pauta_cci_assunto'], $item['ds_pauta_cci_assunto']) 
				: $item['ds_pauta_cci_assunto']
			)), 
		'text-align:justify;'),

		($fl_editar ? anchor('gestao/pauta_cci/assunto/'.$row['cd_pauta_cci'].'/'.$item['cd_pauta_cci_assunto'],$item['cd_gerencia_responsavel']) : $item['cd_gerencia_responsavel']),

		array(($fl_editar ? anchor('gestao/pauta_cci/assunto/'.$row['cd_pauta_cci'].'/'.$item['cd_pauta_cci_assunto'], $item['usuario_responsavel']) : $item['usuario_responsavel']), "text-align:left;"),

		($fl_editar ? anchor('gestao/pauta_cci/assunto/'.$row['cd_pauta_cci'].'/'.$item['cd_pauta_cci_assunto'],$item['cd_gerencia_substituto']) : $item['cd_gerencia_substituto']),

		array(($fl_editar ? anchor('gestao/pauta_cci/assunto/'.$row['cd_pauta_cci']."/".$item['cd_pauta_cci_assunto'],$item['usuario_substituto']) : $item['usuario_substituto']), "text-align:left;"),

		array(
			  nl2br(
			  $fl_editar
			  ? anchor("gestao/pauta_cci/assunto/".$row['cd_pauta_cci']."/".$item['cd_pauta_cci_assunto'], $item['ds_recomendacao']) 
			  : $item['ds_recomendacao']), 
		'text-align:justify;'),

		$item['qt_arquivo'],
		$link	
	);
}

$this->load->helper('grid');
$grid = new grid();
$grid->head = $head;
$grid->body = $body;

echo aba_start($abas);
	echo form_open('gestao/pauta_cci/assunto_salvar');
		echo form_start_box('default_box', 'Pauta');
			echo form_default_hidden('cd_pauta_cci', '', $row);	
			echo form_default_hidden('cd_pauta_cci_assunto', '', $assunto);	
			echo form_default_row('nr_pauta_cci', 'Número da Ata :', '<label class="label label-inverse">'.$row['nr_pauta_cci'].'</label>');
			echo form_default_row('ds_local', 'Local :', $row['ds_local']);
			echo form_default_row('dt_reuniao', 'Dt. Reunião :', $row['dt_pauta_cci']." ".$row['hr_pauta_cci']);
			
			if(trim($row['dt_aprovacao']) != '')
			{
				echo form_default_row('cd_usuario_aprovacao', 'Usuário Encerramento :', $row['cd_usuario_aprovacao']);
			    echo form_default_row('dt_aprovacao', 'Dt. Encerramento :', $row['dt_aprovacao']);
			}
		echo form_end_box('default_box');
		echo form_command_bar_detail_start();
			if(trim($row['dt_aprovacao']) == '') 
			{
			    if((count($collection) > 0) AND (intval($row['tl_sem_recomendacao']) == 0) AND (intval($row['tl_nao_removido']) > 0))
				{
					echo button_save('Encerrar', 'aprovar()', 'botao_vermelho');
				}
			}
			else
			{	
				echo button_save('Reabrir', 'reabrir()', 'botao_amarelo');
			}

		 	if((intval($row['tl_assuntos']) > 0))
			{
				echo button_save('Pauta','pauta()','botao_verde');
			}
		echo form_command_bar_detail_end();
		if(trim($row['dt_aprovacao']) == '')
		{
		echo form_start_box('default_cadastro_box', 'Cadastro');
			echo form_default_integer('nr_item', 'Número do Item : (*)', $assunto);
			echo form_default_gerencia('cd_gerencia_responsavel', 'Gerência Responsável: (*)', $assunto['cd_gerencia_responsavel'], '	onchange="get_usuarios(this.value, 0)"');

			echo form_default_dropdown('cd_usuario_responsavel', 'Responsável : (*)', $responsavel ,$assunto['cd_usuario_responsavel']);
			echo form_default_gerencia('cd_gerencia_substituto', 'Gerência Substituto: (*)', $assunto['cd_gerencia_substituto'], 'onchange="get_usuarios(this.value, 1)"');
			echo form_default_dropdown('cd_usuario_substituto', 'Substituto : (*)', $substituto , $assunto['cd_usuario_substituto']);
			echo form_default_textarea('ds_pauta_cci_assunto', 'Assunto : (*)', $assunto, 'style="height:80px;"');

			if(intval($assunto['cd_pauta_cci_assunto']) > 0)
			{
				echo form_default_textarea('ds_recomendacao', 'Recomendação :', $assunto, 'style="height:80px;"');
			}
		echo form_end_box('default_cadastro_box');
		echo form_command_bar_detail_start();
			echo button_save('Salvar');
			if(intval($assunto['cd_pauta_cci_assunto']) > 0)
			{
				echo button_save('Cancelar', 'cancelar()', 'botao_disabled');	
			}
		echo form_command_bar_detail_end();
		}
	echo form_close();
        
	echo $grid->render();
	echo br(2);
echo aba_end();

$this->load->view('footer_interna');
?>