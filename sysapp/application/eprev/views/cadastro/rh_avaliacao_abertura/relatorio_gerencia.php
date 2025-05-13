<?php
	set_title('Sistema de Avaliação - Relatório');
	$this->load->view('header');
?>
<script>

	function filtrar()
	{
		$("#result_div").html("<?= loader_html() ?>");
			
		$.post("<?= site_url('cadastro/rh_avaliacao_abertura/listar_relatorio') ?>",
		{
			cd_avaliacao : $("#cd_avaliacao").val(),
			ds_cargo     : $("#ds_cargo").val(),
			cd_gerencia  : $("#cd_gerencia").val()
		},
		function(data)
		{
			$("#result_div").html(data);
		});
	}

    $(function(){
    	filtrar();
	});
</script>
<style>
	div.quadrado_matriz {
		width:110px;
		height:110px;
		border: 1px solid #000;
		float:left;
		line-height:15px;
		text-align: center;
		margin: 4px 4px 4px 4px;
		z-index:1;
	}

	div.quadrado_matriz.span {
		padding: 1px;
	}

	div.circulo_resultado{
		background:green;
		color:#fff;
		width:30px;
		height:30px;
		line-height:30px;
		vertical-align:middle;
		text-align:center;
		z-index: 2;
		border-radius:50%;
		-moz-border-radius:50%;
		-webkit-border-radius:50%;
		position: absolute;
		border:1px solid #000;
		font-weight: bold;
		font-size: 15px;
	}

	.texto_grupo {
        font-size: 20px;
        font-weight: bold;
        color:#1E1E1E;
    }

    @media print {
		div.circulo_resultado{
			color:#000;
		}

		span.span_matriz {
			color:#000 !important; 
		}	
	}
</style>
<?php
	$abas[] = array('aba_lista', 'Lista', TRUE, 'location.reload();');

	$dropdown = array(
		array('value' => 'N', 'text' => 'Não'),
		array('value' => 'S', 'text' => 'Sim')
	);

	echo aba_start($abas); 
		echo form_list_command_bar(array());
		echo form_start_box_filter();

			if(count($gerencia) > 0)
			{
				echo filter_dropdown('cd_gerencia', 'Gerência:', $gerencia);
			}
			else 
			{
				echo form_default_hidden('cd_gerencia', '', $cd_gerencia);	
			}

			echo filter_dropdown('cd_avaliacao', 'Ano:', $avaliacao, $cd_avaliacao);
			echo filter_dropdown('ds_cargo', 'Cargo:', $cargo);
	    echo form_end_box_filter();
		echo '<div id="result_div"></div>';
		echo br(2);
	echo aba_end();

	$this->load->view('footer_interna');
?>