<?php
set_title('Treinamento Colaborador');
$this->load->view('header');
?>
<script>
<?php
		echo form_default_js_submit(Array('nome', 'promotor'));
?>
    function irLista()
	{
		location.href='<?php echo site_url("cadastro/treinamento_colaborador"); ?>';
	}
    
    function excluirTreinamento()
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir o Treinamento?\n\n"))
		{
			location.href='<?php echo site_url("cadastro/treinamento_colaborador/excluir/".$row['ano']."/".$row['numero']); ?>';
		}
    }	
	
    function irColaborador()
    {
        location.href='<?php echo site_url("cadastro/treinamento_colaborador/colaborador/".$row['ano']."/".$row['numero']); ?>';
    }
	
    function irAgenda()
    {
        location.href='<?php echo site_url("cadastro/treinamento_colaborador/agenda/".$row['ano']."/".$row['numero']); ?>';
    }	
    
    function excluirColaborador(cd_treinamento_colaborador_item)
    {
        if(confirm("ATENÇÃO\n\nDeseja excluir o colabordor?\n\n"))
		{
			location.href='<?php echo site_url("cadastro/treinamento_colaborador/excluirColaborador"); ?>' + "/" + cd_treinamento_colaborador_item;
		}
    }

    function editar_colaborador(cd_treinamento_colaborador_item)
    {
    	location.href = "<?= site_url('cadastro/treinamento_colaborador/colaborador/'.$row['ano'].'/'.$row['numero']); ?>/"+cd_treinamento_colaborador_item;
    }

    function configure_result_table()
    {
        var ob_resul = new SortableTable(document.getElementById("table-1"),
        [
            'RE', 
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'CaseInsensitiveString',
            'DateTimeBR',
            'DateTimeBR',
            'DateTimeBR',
            'DateTimeBR',
            null,
            null,
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
  		<?php if($row['numero'] > 0 AND $row['ano'] > 0): ?>
		configure_result_table();
		<?php endif; ?>
    });

</script>
<?php
	$abas[] = array('aba_lista', 'Lista', FALSE, 'irLista();');
	$abas[] = array('aba_nc', 'Cadastro', TRUE, 'location.reload();');
	if($row['numero'] > 0 AND $row['ano'] > 0)
	{
		$abas[] = array('aba_agenda', 'Agenda', FALSE, 'irAgenda();');

		$head = array( 
			'RE', 'Nome', 'Gerência', 'Centro de Custo', 'Dt. Envio Avaliação', 'Dt. Envia Email', 'Dt. Visualização Email', 'Dt. Termino Avaliação', 'Certificado', 'Bem-Estar' ,'#'
		);

		$body = array();

		foreach($collection as $item)
		{
			$link_encerrar = '';

			if(trim($item['dt_inclusao']) != '' AND trim($item['dt_finalizado']) == '')
			{
				$link_encerrar = anchor(site_url('cadastro/treinamento_colaborador/encerrar_avaliacao/'.$item['cd_treinamento_colaborador_resposta']), '[encerrar]');
			}

			$link_pdf = '';

			if(trim($item['dt_finalizado']) != '' AND trim($item['ds_justificativa_finalizado']) == '')
			{
				$link_pdf = anchor(site_url('servico/avaliacao_treinamento/pdf/'.$item['cd_treinamento_colaborador_resposta']), '[PDF]', array('target' => '_blank'));
			}

			$link_excluir = '<a onclick="excluirColaborador('.$item["cd_treinamento_colaborador_item"].')" href="javascript:void(0);">[excluir]</a>';

			$link_editar = '<a onclick="editar_colaborador('.$item["cd_treinamento_colaborador_item"].')" href="javascript:void(0);">[editar]</a>';

			$link_avaliacao = '';

			if(intval($item['cd_treinamento_colaborador_resposta']) > 0 AND trim($item['dt_finalizado']) == '')
			{
				$link_avaliacao = anchor(site_url('servico/avaliacao_treinamento/cadastro/'.$item['cd_treinamento_colaborador_resposta']), '[avaliação]');
			}


			$body[] = array(
				$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
				array($item['nome'],'style="text-align:left;"'),
				$item['area_gerencia'],
				$item['centro_custo'],
				$item['dt_inclusao'],
				$item['dt_email_enviado'],
				$item['dt_email_visualizado'],
				array($item['dt_finalizado'].(trim($item['ds_justificativa_finalizado']) != '' ? ' : '.trim($item['ds_justificativa_finalizado']) : ''),'style="text-align:justify;"'),
				(trim($item['fl_certificado']) == 'S'
	        		? array(anchor(base_url().'up/certificado_treinamento/'.$item['arquivo'], $item['arquivo_nome'], array('target' => '_blank')), 'text-align:left;')
	        		: array($item['ds_justificativa'], 'text-align:justify;')

	        	),
	        	"",
	        	$link_editar.' '.$link_pdf.' '.$link_excluir.' '.$link_encerrar.' '.$link_avaliacao
			);
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;
	}

	$certificado = array(
		array('value' => 'N', 'text' => 'Não'),
		array('value' => 'S', 'text' => 'Sim'),
	);

	echo aba_start($abas);
	    echo form_open('cadastro/treinamento_colaborador/salvar', 'name="filter_bar_form"');
	    echo form_start_box( "default_box", "Cadastro" );
	        if($row['numero'] > 0 AND $row['ano'] > 0)
	        {
	            echo form_default_text('num', "Número:", $row['numero_a'], "style='width:100%;border: 0px;'" );
	        }
	        
			echo (trim($row['dt_exclusao']) != "" ? form_default_text('dt_exclusao', "Dt Exclusão:", $row, "style='border: 0px; color: red; font-weight:bold;' readonly" ) : "");
			
	        echo form_default_hidden('numero', "", $row);
	        echo form_default_hidden('ano', "", $row);
	        echo form_default_text('nome', "Nome do Evento:*", $row, "style='width:100%;'" );
	        echo form_default_text('promotor', "Promotor:*", $row, "style='width:100%;'" );
	        echo form_default_textarea('endereco', "Endereço:", $row, "style='width:500px; height:70px;'");
	        echo form_default_text('cidade', "Cidade:", $row , "style='width:100%;'");
	        echo form_default_dropdown('uf', 'UF:', $arr_uf, array($row['uf']));
	        echo form_default_date("dt_inicio", "Dt Início: ", $row);
	        echo form_default_time('hr_inicio', 'Hr Início:', $row);
	        echo form_default_date("dt_final", "Dt Final: ", $row);
	        echo form_default_time('hr_final', 'Hr Final:', $row);
	        echo form_default_decimal('carga_horaria', 'Carga Horária:(Horas)', $row);
	        echo form_default_decimal('vl_unitario', 'Valor Unitário', $row);
	        echo form_default_dropdown_db( "cd_treinamento_colaborador_tipo", "Tipo:"
	            , array( 'projetos.treinamento_colaborador_tipo', 'cd_treinamento_colaborador_tipo', 'ds_treinamento_colaborador_tipo' )
	            , array($row['cd_treinamento_colaborador_tipo'])
	            , "", "", TRUE
	        );
	        echo form_default_dropdown('fl_certificado', 'Certificado:', $certificado, array($row['fl_certificado']));
	        echo form_default_dropdown('fl_bem_estar', 'Bem-Estar:', $certificado, array($row['fl_bem_estar']));
	        
	    echo form_end_box("default_box");
		
		if(trim($row['dt_exclusao']) == "")
		{
			echo form_command_bar_detail_start();
				echo button_save("Salvar Treinamento");
				if((intval($row['numero']) > 0) AND (intval($row['ano']) > 0))
				{
					echo button_save("Adicionar Colaborador","irColaborador()","botao_verde");
					echo button_save("Excluir Treinamento","excluirTreinamento()","botao_vermelho");
				}
			echo form_command_bar_detail_end();
			
			if((intval($row['numero']) > 0) AND (intval($row['ano']) > 0))
			{
				echo form_start_box( "default_box", "Colaborador" );
					
					echo $grid->render();

				echo form_end_box("default_box");
			}
			
			if((intval($row['numero']) > 0) AND (intval($row['ano']) > 0) AND (count($collection_gerencia) > 0))
			{
				$head = array( 
					'Avaliador', 'RE', 'Nome', 'Gerência', 'Centro de Custo', 'Dt. Envio Avaliação', 'Dt. Envia Email', 'Dt. Visualização Email', 'Dt. Termino Avaliação', '#'
				);

				$body = array();
				
				foreach($collection_gerencia as $item)
				{
					$body[] = array(
						array($item['avaliador'],'style="text-align:left;"'),
						$item['cd_empresa'].'/'.$item['cd_registro_empregado'].'/'.$item['seq_dependencia'],
						array($item['nome'],'style="text-align:left;"'),
						$item['area_gerencia'],
						$item['centro_custo'],
						$item['dt_inclusao'],
						$item['dt_email_enviado'],
						$item['dt_email_visualizado'],
						$item['dt_finalizado'],	
						(trim($item['dt_finalizado']) != '' 
							? anchor(site_url('servico/avaliacao_treinamento/pdf/'.$item['cd_treinamento_colaborador_resposta']), '[PDF]', array('target' => "_blank")) 
							: ''
						)
						//<a onclick="excluirColaborador('.$item["cd_treinamento_colaborador_item"].')" href="javascript:void(0);">[excluir]</a>
					);
				}

				$this->load->helper('grid');
				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;

				echo form_start_box('default_box', 'Gerência');
					echo $grid->render();
				echo form_end_box('default_box');
			}
		}
		echo br(3);	

	echo aba_end();

	$this->load->view('footer_interna');

?>