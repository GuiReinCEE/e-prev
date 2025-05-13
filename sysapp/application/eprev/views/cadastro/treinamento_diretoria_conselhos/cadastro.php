<?php
	set_title('Treinamento - Diretoria e Conselhos - Cadastro');
	$this->load->view('header');
?>
<script>
	<?= form_default_js_submit(array('ds_nome', 'ds_promotor')) ?> 

	function ir_lista()
    {
        location.href = "<?= site_url('cadastro/treinamento_diretoria_conselhos') ?>";
    }
    
    function excluir_treinamento()
    {
    	var confirmacao = "Deseja excluir a Treinamento?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 

        if(confirm(confirmacao))
		{
			location.href = "<?= site_url('cadastro/treinamento_diretoria_conselhos/excluir/'.$row['cd_treinamento_diretoria_conselhos']) ?>";
		}
    }	
	
    function ir_colaborador()
    {
		location.href="<?= site_url('cadastro/treinamento_diretoria_conselhos/colaborador/'.$row['cd_treinamento_diretoria_conselhos']) ?>";
    }
    
	function ir_agenda()
    {
        location.href="<?= site_url('cadastro/treinamento_diretoria_conselhos/agenda/'.$row['cd_treinamento_diretoria_conselhos']) ?>";
    }
	
    function excluir_colaborador(cd_treinamento_diretoria_conselhos_item, cd_treinamento_diretoria_conselhos)
    {
    	var confirmacao = "Deseja excluir o Colaborador?\n\n"+
                          "Clique [Ok] para Sim\n\n"+
                          "Clique [Cancelar] para Não\n\n"; 	

        if(confirm(confirmacao))
		{
			location.href = "<?= site_url('cadastro/treinamento_diretoria_conselhos/excluir_colaborador') ?>"+"/"+cd_treinamento_diretoria_conselhos_item+"/"+cd_treinamento_diretoria_conselhos;
		}
    }

    function editar_colaborador(cd_treinamento_diretoria_conselhos_item, cd_treinamento_diretoria_conselhos)
    {
		location.href = "<?= site_url('cadastro/treinamento_diretoria_conselhos/colaborador') ?>"+"/"+cd_treinamento_diretoria_conselhos+"/"+cd_treinamento_diretoria_conselhos_item;
    }

	function configure_result_table()
	{
		var ob_resul = new SortableTable(document.getElementById("table-1"),
		[
			'RE', 
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			'CaseInsensitiveString',
			null
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
	
	$(function(){
		if(parseInt($("#cd_treinamento_diretoria_conselhos").val()) > 0)
		{
			configure_result_table();
		}
	});
</script>
<?php
$abas[] = array('aba_lista', 'Lista', FALSE, 'ir_lista();');
$abas[] = array('aba_cadastro', 'Cadastro', TRUE, 'location.reload();');

if(intval($row['cd_treinamento_diretoria_conselhos']) > 0)
{
	$abas[] = array('aba_agenda', 'Agenda', FALSE, 'ir_agenda();');
}

$config_tipo = array('projetos.treinamento_colaborador_tipo', 'cd_treinamento_colaborador_tipo', 'ds_treinamento_colaborador_tipo');

echo aba_start($abas);
    echo form_open('cadastro/treinamento_diretoria_conselhos/salvar');
	    echo form_start_box('default_box', 'Cadastro');
	        
	        if(intval($row['cd_treinamento_diretoria_conselhos']) > 0)
	        {
	            echo form_default_row('nr_numero', 'Número:', $row['nr_numero'], 'style="width:300px"' );
	        }
	        
	        echo form_default_hidden('cd_treinamento_diretoria_conselhos', '', $row);
	        echo form_default_text('ds_nome', 'Nome do Evento: (*)', $row, 'style="width:300px;"' );
	        echo form_default_text('ds_promotor', 'Promotor: (*)', $row, 'style="width:300px;"' );
	        echo form_default_textarea('ds_endereco', 'Endereço:', $row, 'style="width:500px; height:70px;"');
	        echo form_default_text('ds_cidade', 'Cidade:', $row , 'style="width:300px;"');
	        echo form_default_dropdown('ds_uf', 'UF:', $uf, array($row['ds_uf']));
	        echo form_default_date('dt_inicio', 'Dt Início: ', $row);
	        echo form_default_time('hr_inicio', 'Hr Início:', $row);
	        echo form_default_date('dt_final', 'Dt Final: ', $row);
	        echo form_default_time('hr_final', 'Hr Final:', $row);
	        echo form_default_decimal('nr_carga_horaria', 'Carga Horária:(Horas)', $row);
	        echo form_default_decimal('vl_unitario', 'Valor Unitário', $row);
	        echo form_default_dropdown_db('cd_treinamento_colaborador_tipo', 'Tipo:', $config_tipo, array($row['cd_treinamento_colaborador_tipo']), '', '', TRUE);
	        
	    echo form_end_box('default_box');
		
		echo form_command_bar_detail_start();
			echo button_save('Salvar Treinamento');
			
			if(intval($row['cd_treinamento_diretoria_conselhos']) > 0)
			{
				echo button_save('Adicionar Colaborador','ir_colaborador()','botao_verde');
				echo button_save('Excluir Treinamento','excluir_treinamento()','botao_vermelho');
			}
		echo form_command_bar_detail_end();
	echo form_close();
	
	if(intval($row['cd_treinamento_diretoria_conselhos']) > 0)
	{
		$head = array( 
			'RE', 
			'Nome', 
			'Gerência', 
			'Centro de Custo',
			'Certificado',
			''
		);

		$body = array();
		
		foreach($collection as $item)
		{
			$body[] = array(
				$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
				array($item['ds_nome'],'style="text-align:left;"'),
				$item['area_gerencia'],
				$item['ds_centro_custo'],
				anchor(base_url().'up/treinamento_diretoria_conselhos/'.$item['arquivo'], $item['arquivo_nome']),
				'<a onclick="editar_colaborador('.$item['cd_treinamento_diretoria_conselhos_item'].', '.$row['cd_treinamento_diretoria_conselhos'].')" href="javascript:void(0);">[Editar]</a>'
				.'<br>'.
				'<a onclick="excluir_colaborador('.$item['cd_treinamento_diretoria_conselhos_item'].', '.$row['cd_treinamento_diretoria_conselhos'].')" href="javascript:void(0);">[Excluir]</a>'
			);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;

		echo $grid->render();
	}

    echo br(2);
echo aba_end();
$this->load->view('footer_interna');
?>