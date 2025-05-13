<?php
set_title('Copa - Tabela');
$this->load->view('header');
?>
<script>
function filtrar()
{
	grupos();
	oitavas();
	quartas();
	semis();
	terceiro();
	finalcopa();
}

function grupos()
{
	$("#grupos_div").html("<?php echo loader_html();?>");
    $.post('<?php echo site_url('copa/copa/listar');?>',
	{
		cd_fase    : 1
	},
    function(data)
    {
		$("#grupos_div").html(data);
		configTable(1);
		grupo(1);
    });
}

function oitavas()
{
	$("#oitavas_div").html("<?php echo loader_html();?>");
    $.post('<?php echo site_url('copa/copa/listar');?>',
	{
		cd_fase    : 2
	},
    function(data)
    {
		$("#oitavas_div").html(data);
		configTable(2);
    });
}

function quartas()
{
	$("#quartas_div").html("<?php echo loader_html();?>");
    $.post('<?php echo site_url('copa/copa/listar');?>',
	{
		cd_fase    : 3
	},
    function(data)
    {
		$("#quartas_div").html(data);
		configTable(3);
    });
}

function semis()
{
	$("#semis_div").html("<?php echo loader_html();?>");
    $.post('<?php echo site_url('copa/copa/listar');?>',
	{
		cd_fase    : 4
	},
    function(data)
    {
		$("#semis_div").html(data);
		configTable(4);
    });
}

function terceiro()
{
	$("#terceiro_div").html("<?php echo loader_html();?>");
    $.post('<?php echo site_url('copa/copa/listar');?>',
	{
		cd_fase    : 5
	},
    function(data)
    {
		$("#terceiro_div").html(data);
		configTable(5);
    });
}

function finalcopa()
{
	$("#final_div").html("<?php echo loader_html();?>");
    $.post('<?php echo site_url('copa/copa/listar');?>',
	{
		cd_fase    : 6
	},
    function(data)
    {
		$("#final_div").html(data);
		configTable(6);
    });
}

function grupo(cd_fase)
{
	$("#tbClassifica").html("<?php echo loader_html();?>");
    $.post('<?php echo site_url('copa/copa/grupo');?>',
	{},
    function(data)
    {
		$("#tbClassifica-"+cd_fase).html(data);
    });
}

function setResultadoTabela(cd_fase, cd_jogo, nr_pais, nr_gol_pais)
{
    $.post('<?php echo site_url('copa/copa/setResultadoTabela');?>',
	{
		cd_jogo     : cd_jogo,
		nr_pais     : nr_pais,
		nr_gol_pais : nr_gol_pais
	},
    function(data)
    {
		grupo();
		if(cd_fase == 1)
		{
			oitavas();
			quartas();
			semis();
			terceiro();
			finalcopa();		
		}
		else if(cd_fase == 2)
		{
			quartas();
			semis();
			terceiro();
			finalcopa();		
		}	
		else if(cd_fase == 3)
		{
			semis();
			terceiro();
			finalcopa();		
		}	
		else if(cd_fase == 4)
		{
			terceiro();
			finalcopa();		
		}		
    });
}

function setResultadoProrrogacao(cd_fase, cd_jogo, nr_pais, nr_gol_pais)
{
    $.post('<?php echo site_url('copa/copa/setResultadoProrrogacao');?>',
	{
		cd_jogo     : cd_jogo,
		nr_pais     : nr_pais,
		nr_gol_pais : nr_gol_pais
	},
    function(data)
    {
		grupo();
		if(cd_fase == 1)
		{
			oitavas();
			quartas();
			semis();
			terceiro();
			finalcopa();		
		}
		else if(cd_fase == 2)
		{
			quartas();
			semis();
			terceiro();
			finalcopa();		
		}	
		else if(cd_fase == 3)
		{
			semis();
			terceiro();
			finalcopa();		
		}	
		else if(cd_fase == 4)
		{
			terceiro();
			finalcopa();		
		}		
    });
}

function setResultadoPenaltis(cd_fase, cd_jogo, nr_pais, nr_gol_pais)
{
    $.post('<?php echo site_url('copa/copa/setResultadoPenaltis');?>',
	{
		cd_jogo     : cd_jogo,
		nr_pais     : nr_pais,
		nr_gol_pais : nr_gol_pais
	},
    function(data)
    {
		grupo();
		if(cd_fase == 1)
		{
			oitavas();
			quartas();
			semis();
			terceiro();
			finalcopa();		
		}
		else if(cd_fase == 2)
		{
			quartas();
			semis();
			terceiro();
			finalcopa();		
		}	
		else if(cd_fase == 3)
		{
			semis();
			terceiro();
			finalcopa();		
		}	
		else if(cd_fase == 4)
		{
			terceiro();
			finalcopa();		
		}		
    });
}

function getAcertouResultado(cd_jogo)
{
    $.post('<?php echo site_url('copa/copa/getAcertouResultado');?>',
	{
		cd_jogo : cd_jogo
	},
    function(data)
    {
		$("#gridWindowTitulo").html("Pontuadores");
		$("#gridWindowTexto").html(data);
		gridWindowShowModal();
    });
}


function configTable(cd_fase)
{
	var ob_resul = new SortableTable(document.getElementById("table-" + cd_fase),
	[
		'Number', //ref
		'DateTimeBR', //dt
		null, //band1
		'CaseInsensitiveString', //time1
		'Number', // p1
		null, //x
		'Number', //p2
		'CaseInsensitiveString', //time2
		null, //band2
		'CaseInsensitiveString', 
		'CaseInsensitiveString'
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
	filtrar();
	


});

function ir_minha()
{
	location.href='<?php echo site_url("copa/copa/minha/");?>';
}

function ir_resultado()
{
	location.href='<?php echo site_url("copa/copa/resultado/");?>';
}	

function ir_regulamento()
{
	location.href='<?php echo site_url("copa/copa/regulamento/");?>';
}	
</script>
<?php
$abas[] = array('aba_tab', 'Tabela', TRUE, 'location.reload();');
$abas[] = array('aba_pal', 'Palpite', FALSE, 'ir_minha();');
$abas[] = array('aba_res', 'Resultado', FALSE, 'ir_resultado();');
$abas[] = array('aba_reg', 'Regulamento', FALSE, 'ir_regulamento();');

echo aba_start( $abas );
	echo '<div style="height: 4400px;">';


		
	

	

		echo '<div id="semis_div" style="text-align:center;"></div>';
		echo br(2);		
		
		echo '<div id="terceiro_div" style="text-align:center;"></div>';
		echo br(2);
		
		echo '<div id="final_div" style="text-align:center;"></div>';
		echo br(2);		
	
		echo '<div id="quartas_div" style="text-align:center;"></div>';
		echo br(2);		
	
		echo '<div id="oitavas_div" style="text-align:center;"></div>';
		echo br(2);		
	
	
		echo '<div id="grupos_div" style="text-align:center;"></div>';
		echo br(2);	
		
	echo '</div>';
echo aba_end();
$this->load->view('footer'); 
?>