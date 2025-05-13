<?php
set_title('Copa - Palpite');
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
	palpiteVerifica();
}

function grupos()
{
	$("#grupos_div").html("<?php echo loader_html();?>");
    $.post('<?php echo site_url('copa/copa/minhaListar');?>',
	{
		cd_fase            : 1,
		cd_usuario_palpite : $("#cd_usuario_palpite").val()
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
    $.post('<?php echo site_url('copa/copa/minhaListar');?>',
	{
		cd_fase            : 2,
		cd_usuario_palpite : $("#cd_usuario_palpite").val()
	},
    function(data)
    {
		$("#oitavas_div").html(data);
		configTable(2);
		palpiteVerifica();
    });
}

function quartas()
{
	$("#quartas_div").html("<?php echo loader_html();?>");
    $.post('<?php echo site_url('copa/copa/minhaListar');?>',
	{
		cd_fase            : 3,
		cd_usuario_palpite : $("#cd_usuario_palpite").val()
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
    $.post('<?php echo site_url('copa/copa/minhaListar');?>',
	{
		cd_fase            : 4,
		cd_usuario_palpite : $("#cd_usuario_palpite").val()
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
    $.post('<?php echo site_url('copa/copa/minhaListar');?>',
	{
		cd_fase            : 5,
		cd_usuario_palpite : $("#cd_usuario_palpite").val()
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
    $.post('<?php echo site_url('copa/copa/minhaListar');?>',
	{
		cd_fase            : 6,
		cd_usuario_palpite : $("#cd_usuario_palpite").val()
	},
    function(data)
    {
		$("#final_div").html(data);
		configTable(6);
    });
}

function setResultado(cd_fase, cd_jogo, nr_pais, nr_gol_pais)
{
    $.post('<?php echo site_url('copa/copa/setResultado');?>',
	{
		cd_jogo     : cd_jogo,
		nr_pais     : nr_pais,
		nr_gol_pais : nr_gol_pais
	},
    function(data)
    {
		grupo(1);
		palpiteVerifica();
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

function setVencedor(cd_fase, cd_jogo, cd_vencedor)
{
	$.post('<?php echo site_url('copa/copa/setVencedor');?>',
	{
		cd_jogo     : cd_jogo,
		cd_vencedor : cd_vencedor
	},
    function(data)
    {
		grupo(1);
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

function grupo(cd_fase)
{
	$("#tbClassifica").html("<?php echo loader_html();?>");
    $.post('<?php echo site_url('copa/copa/minhaGrupo');?>',
	{
		cd_usuario_palpite : $("#cd_usuario_palpite").val()
	},
    function(data)
    {
		$("#tbClassifica-"+cd_fase).html(data);
    });
}

function palpiteVerifica()
{
	$("#obPreencherOK1-S").hide();
	$("#obPreencherOK1-N").hide();
	$("#obPreencherOK2-S").hide();
	$("#obPreencherOK2-N").hide();
	
    $.post('<?php echo site_url('copa/copa/palpiteVerifica');?>',
	{},
    function(data)
    {
		$("#obPreencherOK1-S").hide();
		$("#obPreencherOK1-N").show();
		$("#obPreencherOK2-S").hide();
		$("#obPreencherOK2-N").show();	
		
		var obj = data;
		if(obj)
		{			
			if(obj.fl_palpite == "S")
			{
				$("#obPreencherOK1-S").show();
				$("#obPreencherOK1-N").hide();
				$("#obPreencherOK2-S").show();
				$("#obPreencherOK2-N").hide();				
			}
		}
    },
	'json');
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
		'CaseInsensitiveString', 
		'Number'
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

function ir_tabela()
{
	location.href='<?php echo site_url("copa/copa/");?>';
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
$abas[] = array('aba_tab', 'Tabela', FALSE, 'ir_tabela();');
$abas[] = array('aba_pal', 'Palpite', TRUE, 'location.reload();');
$abas[] = array('aba_res', 'Resultado', FALSE, 'ir_resultado();');
$abas[] = array('aba_reg', 'Regulamento', FALSE, 'ir_regulamento();');
echo form_input(array("type" => "hidden", "id" => "cd_usuario_palpite", "name" => "cd_usuario_palpite"), $cd_usuario_palpite, "readonly");
echo aba_start( $abas );
	
	
	echo '<div style="height: 4300px;">';
			if($cd_usuario_palpite == $this->session->userdata('codigo'))
			{
			echo '	
					<center>
					<div style="display:none;" id="obPreencherOK1-N">
						<span class="label label-important">Você NÃO FINALIZOU o seu palpite</span>
						<BR>
						<BR>
						<input type="button" value="Verificar Preenchimento" class="btn btn-small btn-warning" onclick="palpiteVerifica();">						
					</div>
					<div style="display:none;" id="obPreencherOK1-S">
						<span class="label label-success">Você FINALIZOU o seu palpite</span>
					</div>	
					</center>
			  ';
			}
			else
			{
				echo '	
						<center>
							<div>
								<span class="label label-success" style="font-size: 180%; line-height: 34px;">'.$ds_usuario_palpite.'</span>
							</div>	
						</center>
				  ';
			}
		echo br();
	

		


		

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
		
		if($cd_usuario_palpite == $this->session->userdata('codigo'))
		{		
		echo '			
				<center>
				<div style="display:none;" id="obPreencherOK2-N">
					<span class="label label-important">Você NÃO FINALIZOU o seu palpite</span>
					<BR>
					<BR>
					<input type="button" value="Verificar Preenchimento" class="btn btn-small btn-warning" onclick="palpiteVerifica();">
				</div>
				<div style="display:none;" id="obPreencherOK2-S">
					<span class="label label-success">Você FINALIZOU o seu palpite</span>
				</div>	
				</center>
			  ';
		echo br(2);
		}		
	echo '</div>';
echo aba_end();
$this->load->view('footer'); 
?>