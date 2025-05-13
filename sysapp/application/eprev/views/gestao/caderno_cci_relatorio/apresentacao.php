<html>
	<head>
		<title>Caderno CCI</title>
		<style type="text/css">
			@font-face {
				font-family: 'YanoneKaffeesatzRegular';
				src: url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.eot');
				src: url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.eot?#iefix') format('embedded-opentype'),
					 url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.woff') format('woff'),
					 url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.ttf') format('truetype'),
					 url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.svg#YanoneKaffeesatzRegular') format('svg');
				font-weight: normal;
				font-style: normal;
			}

			@font-face {
				font-family: 'AldrichRegular';
				src: url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.eot');
				src: url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.eot?#iefix') format('embedded-opentype'),
					 url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.woff') format('woff'),
					 url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.ttf') format('truetype'),
					 url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.svg#AldrichRegular') format('svg');
				font-weight: normal;
				font-style: normal;
			}	

			
			@font-face {
				font-family: 'FrancoisOneRegular';
				src: url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.eot');
				src: url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.eot?#iefix') format('embedded-opentype'),
					 url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.woff') format('woff'),
					 url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.ttf') format('truetype'),
					 url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.svg#AldrichRegular') format('svg');
				font-weight: normal;
				font-style: normal;
			}		
		</style>		
		
		<link href="<?= base_url() ?>bootstrap-3.3.1/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="<?= base_url() ?>js/jquery-plugins/qtip2/jquery.qtip.min.css" rel="stylesheet" type="text/css" />
		<!--[if lte IE 8]>
		<script language="javascript" type="text/javascript" src="<?= base_url() ?>js/jquery-plugins/flot/excanvas.min.js"></script>
		<![endif]-->
		<script src="<?= base_url() ?>js/jquery-1.11.3.min.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>bootstrap-3.3.1/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.orderBars.js" type="text/javascript"></script>		
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.symbol.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.navigate.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/flot/jquery.flot.pie.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/jquery.price_format.1.7.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/jquery-plugins/qtip2/jquery.qtip.min.js" type="text/javascript"></script>
		
		<script src="<?= base_url() ?>js/FileSaver/FileSaver.min.js" type="text/javascript"></script>
		<script src="<?= base_url() ?>js/html2canvas/dist/html2canvas.js" type="text/javascript"></script>

		<script>
			$(function() {

				var options;
				var data;
				var str = "";

				var grid = {
					hoverable       : true,
					markings        : [ { xaxis: { from: 0 }, yaxis: { from: 0, to: 0 }, color: "#949494" }],
					backgroundColor : { colors: ["#ffffff", "#EDF5FF"] },
				};

				var series_linha = {
					points : 
					{
						show   : true,
						radius : 3
					}
				};

				<? if($fl_ano_anterior) : ?>

				var ticks = [
					[0, "DEZ/<?= $row["nr_ano"]-1 ?>"],
					[1, "JAN"],
					[2, "FEV"],
					[3, "MAR"],
					[4, "ABR"],
					[5, "MAI"],
					[6, "JUN"],
					[7, "JUL"],
					[8, "AGO"],
					[9, "SET"],
					[10, "OUT"],
					[11, "NOV"],
					[12, "DEZ"]
				];

				<? else : ?>

				var ticks = [
					[0, "JAN"],
					[1, "FEV"],
					[2, "MAR"],
					[3, "ABR"],
					[4, "MAI"],
					[5, "JUN"],
					[6, "JUL"],
					[7, "AGO"],
					[8, "SET"],
					[9, "OUT"],
					[10, "NOV"],
					[11, "DEZ"]
				];

				<? endif; ?>

				var font = {
					size    : 20,
					style   : "italic",
					weight  : "bold",
					family  : "sans-serif",
					variant : "small-caps",
					color   : "#7B7B7B"
				};
				
				<? foreach ($collection as $key => $item): ?>

				<? if($item["tp_grafico"] == "L" OR $item["tp_grafico"] == "B" OR $item["tp_grafico"] == "P" OR $item["tp_grafico"] == "A") : ?>

				options = 
				{
					<? if($item["tp_grafico"] == "L") : ?>
					series: series_linha,
					grid: grid,	
					xaxis: 
					{ 
						ticks: ticks,
						font: font
					},
					<? elseif($item["tp_grafico"] == "B") : ?>
					grid: {
						hoverable : true,
						markings  : [ { xaxis: { from: -5 }, yaxis: { from: 0, to: 0 }, color: "#949494" }]
					},
					xaxis: 
					{ 
						show: false
					},
					<? elseif($item["tp_grafico"] == "A") : ?>

					series: {
		                bars: {
		                    show: true,
		                    order: 1
		                }
		            },
		            grid: {
						hoverable : true,
						markings  : [ { xaxis: { from: -5 }, yaxis: { from: 0, to: 0 }, color: "#949494" }]
					},
		            xaxis: { 
		              	show: true,
		              	font: font,
					 	ticks: [
					 	<? foreach ($item["agrupamento"]["ticks"] as $key2 => $item2): ?>
					 		[<?= $key2 ?>, "<?= utf8_decode($item2) ?>"]<?=(isset($item["agrupamento"]["ticks"][($key2+1)]) ? "," : "")?>
					 	<? endforeach; ?>
					 	]		  
				  	},
					<? elseif($item["tp_grafico"] == "P") : ?>
					grid: grid,						
					series: {
						pie: { 
							show: true,
							radius: 1,
							label: {
								show: true,
								radius: 5/8,
								formatter: function(label, series){
									$("#valorLabelFormatar").val(series.percent.toFixed(2));
									$("#valorLabelFormatar").priceFormat({
										prefix             : '',
										clearPrefix        : false,
										suffix             : '',
										clearSufix         : false,
										centsSeparator     : ',',
										thousandsSeparator : '.',
										centsLimit         : 2,
										allowNegative      : true
									}); 									
									return '<div style="font-size:14pt; font-family:FrancoisOneRegular; border-style: solid; border-width: 2px; border-radius: 5px; background-color: #ffffff; padding: 2px; margin: 3px;">'+ $("#valorLabelFormatar").val() +'</div>';
								}
							}
						}
					},
					legend: {
						show: false
					},					
					<? endif; ?>

					legend: {  
						container: $("#legendContainer_<?=$key?>"),
						backgroundOpacity: 0.5,
			            backgroundColor: "green",
			            noColumns: 4,
			            position: "ne"
					},
					yaxis:
					{
						tickFormatter: function (val, axis) 
						{ 
							return valorToFormatado(val) + " %"
						},
						autoscaleMargin: 0.2,
						font: font
				    },
				    <? if(isset($item["cor"]) AND is_array($item["cor"]) AND count($item["cor"]) > 0): ?>
						colors: <?= '["'.implode($item["cor"], '", "').'"]' ?>
					<? else : ?>
						<? $collection[$key]["cor"] = explode('","', '#0000ee","#008001","#e0642e","#b02ee0","#363636","red","#8b4545","#2e97e0","#e0b02e","#cfcfcf","#cd6090","#006400","#e0d62e","#0000ff","#ff00ff","#ff8c00","#ffd700","#cd853f","#bce02e'); ?>
		
						colors: ["#0000ee","#008001","#e0642e", "#b02ee0",  "#363636", "red", "#8b4545", "#2e97e0","#e0b02e","#cfcfcf","#cd6090","#006400","#e0d62e","#0000ff","#ff00ff","#ff8c00","#ffd700","#cd853f","#bce02e"]
					<? endif; ?>
				};

				<? if($item["tp_grafico"] == "L" OR $item["tp_grafico"] == "B" OR $item["tp_grafico"] == "P") : ?>
				data =
				[
				<? foreach ($item["valores"] as $key2 => $val): ?>
					{
					<? if($item["tp_grafico"] == "L") : ?>
					
						data:
						[
							<? foreach ($val as $key3 => $val2): ?>
								[<?=$key3?>, <?=$val2?>] <?=(isset($val[($key3+1)]) ? "," : "")?>
							<? endforeach; ?>
						],
						label: "<?=utf8_decode($item["legenda"][$key2])?>",
						lines: 
						{ 
							show: true
						}
					<? elseif($item["tp_grafico"] == "B") : ?>
				
						data:
						[
							[<?=$key2?>, <?=$val[(intval($row["mes"])-1)]?>] <?=(isset($val[($key2+1)]) ? "," : "")?>
						],
						label: "<?=utf8_decode($item["legenda"][$key2])?>",
						bars: 
						{ 
							show: true,
							barWidth: 0.6,
							align: "center"
						}
					<? elseif($item["tp_grafico"] == "P") : ?>
						data:
						[
							[<?=$key2?>, <?=$val?>] <?=(isset($val[($key2+1)]) ? "," : "")?>
						],
						label: "<?=utf8_decode($item["legenda"][$key2])?>",
						pie: 
						{ 
							show: true
						}
					<? endif; ?>
					}<?=(isset($item["valores"][($key2+1)]) ? "," : ",")?>
				<? endforeach; ?>
				];
				<? elseif($item["tp_grafico"] == "A"): ?>
				data = 
				[
					<? foreach ($item["agrupamento"]["serie"] as $key2 => $val): ?>
					{
						data: 
		                [
		                	<? foreach ($val as $key3 => $val2): ?>
								[<?=$key3?>, <?=$val2?>] <?=(isset($val[($key3+1)]) ? "," : "")?>
							<? endforeach; ?>
		                ],
		                label: "<?= utf8_decode($item["agrupamento"]["legenda"][$key2]) ?>",
		                bars: 
						{ 
							show: true,
							<? if(count($item["agrupamento"]["serie"]) <= 3): ?>
							barWidth: 0.3
							<? else: ?>
							barWidth: 0.23
							<? endif; ?>
						}
                    
		            }<?=(isset($item["agrupamento"]["serie"][($key2+1)]) ? "," : "")?>    
           			<? endforeach; ?>
				];
				<? endif; ?>

				<? if($item["tp_grafico"] == "L" OR $item["tp_grafico"] == "B" OR $item["tp_grafico"] == "A") : ?>
					str = str + (str != "" ? "," : "" ) + "#placeholder_<?=$key?>" ;
				<? endif; ?>

				<? if($item["tp_grafico"] == "L") : ?>

				var p_<?=$key?> = $.plot($("#placeholder_<?=$key?>"), data, options);

				var idx_min = 99999;
				var points = p_<?=$key?>.getData();

				for(var k = 0; k < points.length; k++)
				{                                          
		            var x = points[k].data;
		            var idx = x.length-1;

		            while(idx >= 0)
		            {
                        if((x) && (x[idx][1] !== undefined))
                        {
                           	if(idx < idx_min)
							{
                               idx_min = idx;
                            }

                           	break;
                        }

                        idx = idx - 1;
		            }
				}

				var ar_lb = [];
				var nr_label = 0;

				for(var k = 0; k < points.length; k++)
				{               
					var x = points[k].data;
		            var idx = x.length-1;
		            var val = "";

		            while(idx >= 0)
		            {
	                    if((x) && (x[idx][1] !== undefined))
	                    {
                           val = x[idx][1];
                           break;
	                    }

	                    idx = idx - 1;
		            }

		            var o = p_<?=$key?>.pointOffset({x: idx, y: val});
		         
		            divValue(o.left-20, o.top-35, val, p_<?=$key?>, points[k].color, nr_label);
					ar_lb.push({"id" : nr_label, "valor" : val});
					nr_label = nr_label + 1;

		            if (idx > idx_min)
		            {
                        val = x[idx_min][1];
                        
                        var o = p_<?=$key?>.pointOffset({x: idx_min, y: val});

                        divValue(o.left-20, o.top-35, val, p_<?=$key?>, points[k].color, nr_label); 
						ar_lb.push({"id" : nr_label, "valor" : val});
						nr_label = nr_label + 1;						
           			}
				}
				
				ar_lb.sort(function(obj1, obj2) {
					return obj1.valor - obj2.valor;
				});
				
				for (xx = 0; xx < 20; xx++)
				{
					var mm = 0;
					var labels = [];				
					$.each(ar_lb, function(key, obj) {
						
						var label = $("#placeholder_<?=$key?>").children("#idxLabel"+obj.id);
						var label_pos = getPositions(label);
						for(var j=0; j<labels.length; j++)
						{
							var tmpPos = getPositions(labels[j]);
							var horizontalMatch = comparePositions(label_pos[0], tmpPos[0]);
							var verticalMatch = comparePositions(label_pos[1], tmpPos[1]);			
							var match = horizontalMatch && verticalMatch;				
							if(match)
							{
								mm = mm+1;
								var newTop = tmpPos[1][0] - (label.height() + 12);
								label.css('top', newTop);
								labelTop = newTop;
							}	
						}					
						labels.push(label);
					});	
					
					//console.log("<?=$key?> => " + xx + " => " + mm);
					if(mm == 0)
					{
						break;
					}
				}
				
				<? elseif($item["tp_grafico"] == "B" OR $item["tp_grafico"] == "A") : ?>
				
				var p_<?=$key?> = $.plot($("#placeholder_<?=$key?>"), data, options);
				var points = p_<?=$key?>.getData();
				var ctx = p_<?=$key?>.getCanvas().getContext("2d");
				
				var xaxis = p_<?=$key?>.getXAxes()[0];
				var yaxis = p_<?=$key?>.getYAxes()[0];
				var offset = p_<?=$key?>.getPlotOffset();

				for(var k = 0; k < points.length; k++)
				{
					var data = points[k].data;

					for (var i = 0; i < data.length; i++)
					{
						var text = valorToFormatado(parseFloat($("#valorLabelFormatar").val()).toFixed(2) + '') ;
						var metrics = ctx.measureText(text);

						<? if($item["tp_grafico"] == "A") : ?>
						var xPos = (xaxis.p2c(data[i][3]) + offset.left + 40) - metrics.width ;
						<? elseif($item["tp_grafico"] == "B") : ?>
						var xPos = (xaxis.p2c(data[i][0]) + offset.left) - metrics.width;
						<? endif; ?>	

						var yPos = (yaxis.p2c(data[i][1]) + offset.top - 5) - 16;
						
						divValue(xPos, yPos, parseFloat(data[i][1]), p_<?=$key?>, points[k].color, 0);
					}					
				}

				<? elseif($item["tp_grafico"] == "P") : ?>
				p = $.plot($("#placeholder_<?=$key?>"), data, options);
				<? endif; ?>
				
				<? elseif($item["tp_grafico"] == "T"): ?>
				height =  $("#grafico_<?=$key?> table").height();
				
				if(height < 500)
				{
					margin = 500 - height;
					
					if(margin > 250)
					{
						margin = 250;
					}

					//$("#grafico_<?=$key?> table").css("margin-top", margin);
				}
				<? endif; ?>

				<? if(($item["tp_grafico"] == "P") OR ($item["tp_grafico"] == "L") OR ($item["tp_grafico"] == "B")): ?>
					$("#countLegenda_<?=$key?>").val(<?= count($item["legenda"]) ?>);
				<? elseif($item["tp_grafico"] == "A"): ?>
					$("#countLegenda_<?=$key?>").val(<?= count($item["agrupamento"]["serie"]) ?>);
				<? endif; ?>

				<? endforeach; ?>
				
				if(str != "")
				{	
					var tooltip = $(str).qtip({
						style: { classes: 'customTip' },
						id: 'flot',
						prerender: true,
						content: ' ',
						position: {
							target: 'mouse',
							my: 'bottom left',  // Position my top left...
							at: 'top left', // at the bottom right of...
							viewport: $('#flot'),
							adjust: {
								x: 5
							}
						},
						show: false,
						hide: {
							event: false,
							fixed: true
						}
					});

					var previousPoint = null;

					$(str).bind("plothover", function (event, coords, item) {
						var graph = $(this),
							api = graph.qtip(),
							previousPoint;

						if (!item) 
						{
							api.cache.point = false;
							return api.hide(item);
						}

						previousPoint = api.cache.point;

						if (previousPoint !== item.dataIndex) 
						{
							api.cache.point = item.dataIndex;
							api.set('content.text', "<center>"+ item.series.label + "<br/>" + valorToFormatado(item.datapoint[1].toFixed(2)) + "</center>");
							api.elements.tooltip.stop(1, 1);
							api.show(item);
						}
					});
				}
			});
			
			function getPositions(box) 
			{
				var $box = $(box);
				var pos = $box.position();
				var width = $box.width();
				var height = $box.height();
				return [ [ pos.left, pos.left + width ], [ pos.top, pos.top + height ] ];
			}	
			
			function comparePositions(p1, p2) 
			{
				var x1 = p1[0] < p2[0] ? p1 : p2;
				var x2 = p1[0] < p2[0] ? p2 : p1;
				return x1[1] > x2[0] || x1[0] === x2[0] ? true : false;
			}						
			
			function divValue(left, top, value, p, cor, index)
			{
				$('<div class="data-point-label" id="idxLabel'+index+'">'+ valorToFormatado(value.toFixed(2)) +'</div>').css( {
                   position: 'absolute',
                   display: 'none',
                   left: left,
                   top:  top,
				   'font-size': '14pt',
				   'font-family': 'FrancoisOneRegular',
                   'border-style': 'solid',
                   'border-width': '2px',
                   'border-color': cor,
                   'border-radius': '5px',
                   'background-color': '#ffffff',
				   color: cor,
                   padding: '2px',
                   margin: '3px'
                }).appendTo(eval(p).getPlaceholder()).fadeIn('slow');  
			}
		
			function valorToFormatado(valor)
			{
				if(valor.toString().indexOf(".") > 0)
				{
					var ar_tmp = valor.toString().split(".");
					ar_tmp[1] = ar_tmp[1].substring(0, 2);
					
					var preenche = ar_tmp[1].length;
					
					for(x = 0; x < (2 - preenche); x++)
					{
						ar_tmp[1] += "0";
					}
					
					valor = ar_tmp[0] + "." + ar_tmp[1];
				}
				else
				{	
					valor += ".00";
				}
				
				$('#convVal').val(valor);
				$('#convVal').priceFormat({ prefix: '',centsSeparator: ',',thousandsSeparator: '.', allowNegative: true }); 
				
				return $('#convVal').val();
			}	

			function next()
			{
				var atual = $("#graficoAtual").val();
				var final = $("#graficoFinal").val();

				if((parseInt(atual)+1) < final)
				{	
					$("#grafico_"+atual).hide();
					atual++;
					$("#grafico_"+atual).show();
					
					if((atual+1) == final)
					{
						$("#btn_next").prop("disabled", true);
						$("#btn_forward").prop("disabled", true);

						$('#btn_next').tooltip('hide');
						$('#btn_forward').tooltip('hide');
					}

					if(atual > 0)
					{
						$("#btn_prev").prop("disabled", false);
						$("#btn_backward").prop("disabled", false);
					}

					$("#numero_paginacao").html((parseInt(atual)+1)+"/"+final);

					$("#graficoAtual").val(atual);

					resize_well(atual);
				}
			}

			function prev()
			{
				var atual = $("#graficoAtual").val();
				var final = $("#graficoFinal").val();

				if(atual > 0)
				{
					$("#grafico_"+atual).hide();
					atual--;
					$("#grafico_"+atual).show();
					
					if((atual+1) < final)
					{
						$("#btn_next").prop("disabled", false);
						$("#btn_forward").prop("disabled", false);
					}

					if(atual == 0)
					{
						$("#btn_prev").prop("disabled", true);
						$("#btn_backward").prop("disabled", true);

						$('#btn_prev').tooltip('hide');
						$('#btn_backward').tooltip('hide');
					}

					$("#numero_paginacao").html((parseInt(atual)+1)+"/"+final);

					$("#graficoAtual").val(atual);

					resize_well(atual);
				}
			}

			function primeiro()
			{
				var atual = $("#graficoAtual").val();
				var final = $("#graficoFinal").val();

				$("#grafico_"+atual).hide();

				$("#btn_prev").prop("disabled", true);
				$("#btn_backward").prop("disabled", true);

				$("#btn_next").prop("disabled", false);
				$("#btn_forward").prop("disabled", false);

				$('#btn_prev').tooltip('hide');
				$('#btn_backward').tooltip('hide');

				$("#grafico_"+atual).hide();

				$("#grafico_0").show();

				$("#graficoAtual").val(0);

				$("#numero_paginacao").html("1/"+final);

				resize_well(0);
			}

			function ultimo()
			{
				var atual = $("#graficoAtual").val();
				var final = $("#graficoFinal").val();

				$("#grafico_"+atual).hide();

				$("#btn_prev").prop("disabled", false);
				$("#btn_backward").prop("disabled", false);

				$("#btn_next").prop("disabled", true);
				$("#btn_forward").prop("disabled", true);

				$('#btn_next').tooltip('hide');
				$('#btn_forward').tooltip('hide');

				$("#grafico_"+atual).hide();

				$("#grafico_"+(final-1)).show();

				$("#graficoAtual").val(final-1);

				$("#numero_paginacao").html(final+"/"+final);

				resize_well(final-1);
			}

			function operaEvento(evento)
			{
				if(evento.which == 39)
				{
					next();
				}
				else if(evento.which == 37)
				{
					prev();
				}
			}
			
			function resize_well(atual)
			{
				var i = $("#graficoAtual").val();

				window.scrollTo(0, 0);

				var pagina_height = $("#paginaHeight").val();

				$("#pagina").height(pagina_height);

				$("#pagina .well-black").css("height", "92%");

				var table_row = parseInt($("#countLegenda_"+i+"").val());

				if(table_row > 12)
				{
					$("#grafico_"+i+" .legend-box").css({'font-size':'150%'});
				}
				else if(table_row > 8)
				{
					$("#grafico_"+i+" .legend-box").css({'font-size':'170%'});
				}
				else
				{
					$("#grafico_"+i+" .legend-box").css({'font-size':'190%'});
				}

				zoom();
			}	

			function zoom()
			{
				var zoom_atual = parseInt($(".relatorio_table").css('font-size'));
				var atual      = $("#graficoAtual").val();

				if($("#slide_tp_"+atual).val() != "T")
				{
					$("#btn_zoomout").prop("disabled", true);
					$("#btn_zoomin").prop("disabled", true);

					$('#btn_zoomout').tooltip('hide');
					$('#btn_zoomin').tooltip('hide');
				}
				else
				{
					if(zoom_atual < 40)
					{
						$("#btn_zoomin").prop("disabled", false);
					}
					else
					{
						$("#btn_zoomin").prop("disabled", true);
						$('#btn_zoomin').tooltip('hide');
					}

					if(zoom_atual >= 25)
					{	
						$("#btn_zoomout").prop("disabled", false);
					}
					else
					{	
						$("#btn_zoomout").prop("disabled", true);
						$('#btn_zoomout').tooltip('hide');
					}
				}
			}

			function zoomout()
			{
				var zoom_atual = parseInt($(".relatorio_table").css('font-size'));

				$(".relatorio_table").css('font-size', zoom_atual+5)

				zoom();
			}

			function zoomin()
			{
				var zoom_atual = parseInt($(".relatorio_table").css('font-size'));

				$(".relatorio_table").css('font-size', zoom_atual-5)

				zoom();
			}

			function gera_image(i)
			{
				var nr_final = $("#graficoFinal").val() - 1;
				var nr_tb    = $("#grafico_"+i+" .table_table").height();
				var nr_html  = $("#grafico_"+i+" .texto_html").height();

				$(".relatorio_table").css('font-size', 25)

				var table_row = parseInt($("#countLegenda_"+i+"").val());

				if(table_row > 12)
				{
					$("#grafico_"+i+" .legend-box").css({'font-size':'150%'});
				}
				else
				{
					$("#grafico_"+i+" .legend-box").css({'font-size':'190%'});
				}

				if(nr_tb != null)
				{
					//TABELA
					nr_tb = 0;

					$("#grafico_"+i+" .table_table").each(function(index) {
						nr_tb+=$(this ).height();
					});					

					var nr_roda  = $("#rodape_"+i).height();
					var tam = (nr_tb + nr_roda + 200);
					
					if($("#paginaHeight").val() > tam)
					{
						$("#paginaItem").css({'height':'92%'});	
					}
					else
					{
						$("#paginaItem").css({'height': tam + 'px'});	
					}
				}
				else if(nr_html != null)
				{
					//HTML
					nr_html = 0;

					$("#grafico_"+i+" .texto_html").each(function(index) {
						nr_html+=$(this ).height();
					});					

					var tam = (nr_html + 200);
					
					if($("#paginaHeight").val() > tam)
					{
						$("#paginaItem").css({'height':'92%'});	
					}
					else
					{
						$("#paginaItem").css({'height': tam + 'px'});	
					}
				}
				else
				{
					//GRAFICO
					$("#pagina").height($("#paginaHeight").val());
					$("#paginaItem").css({'height':'92%'});	
				}

				html2canvas($("#grafico_"+i), {
					onrendered: function(canvas) {
						$.post("<?= site_url('gestao/caderno_cci_relatorio/salvar_imagem') ?>", 
						{
							id_imagem : i,
							nr_mes    : <?php echo $row["mes"]?>,
							nr_ano    : <?php echo $row["nr_ano"]?>,
							ob_imagem : canvas.toDataURL('image/png')
						},
						function(data){
							var obj = data; 

							if(i < nr_final)
							{
								next();
								i++;
								setTimeout(gera_image, 300, i);
							}
							else
							{
								//$("#grafico_"+i+" .legend-box").css({'font-size':'250%'});
								
								gera_pdf((i+1));
							}
							
						});
					}
				});
			}
			
			function gera_pdf(qt)
			{
				location.href = "<?php echo site_url('gestao/caderno_cci_relatorio/gera_pdf/'.$row["nr_ano"]."/".$row["mes"]); ?>/" + qt;
			}
			
			$(function(){
				var atual = $("#graficoAtual").val();
				var final = $("#graficoFinal").val();
				
				resize_well(atual);

				$('[data-toggle="tooltip"]').tooltip(); 

				var i = 1;
				
				while(i <= final)
				{
					$("#grafico_"+i).hide();

					i++;
				}

				$("#numero_paginacao").html("1/"+final);

				$("#btn_prev").prop("disabled", true);
				$("#btn_backward").prop("disabled", true);

				$(document).keyup(operaEvento);

				$("#paginaHeight").val($("#pagina").height());
				
				$("#btn_gera_pdf").on("click", function(e) {
					e.preventDefault();

					primeiro();
					
					gera_image($("#graficoAtual").val());
			    });

			    var background = "";
			    var color      = "";

			    $('table.relatorio_table tbody tr').hover(
				    function(){
				    	background = $(this).css('background-color');
				    	color      = $(this).css('color');

				    	$(this).css('background-color', "#006FE6");
				    	$(this).css('color', "#FFFFFF")
				    },
				    function(){
				        $(this).css('background-color', background);
				        $(this).css('color', color);
				    }
			    );
			});
		</script>
		<style>
			* {
				margin: 0;
				padding: 0;
			}

			body {
			    font-family: Arial;
			    overflow-y : hidden;
			}

			#pagina {
				height: 93%;
				width: 100%;
				position: relative;
				top: -20px;
			}

			#pagina .well-black {
				overflow-y : auto;
			}

			#header {
				margin: 2px;
			}

			#logoFundacao {
				width: 300px;
				margin-left:10px;
			}

			#logoFundacao img {
				width: 300px;
			}

			#titulo {
			  font-family: 'YanoneKaffeesatzRegular';
			  left: 50%;
			  margin-left: -140px; /* A metade de sua largura. */
			  position: absolute;
			  width: 350px; /* O valor que você desejar. */
			}

			.grafico-box {
				width:100%; 
				height:80%; 
			}

			.legend-box {
				font-size:210%;  
				margin-top:10px;
			}

			.legend-box .legendLabel { 
				padding-right:15px; 
			}

			.legendColorBox {
				padding-right: 15px;
			}

			.customTip {
				font-size:20px;
				padding:10px;
				font-family: 'FrancoisOneRegular';
			}

			.legendLabel {
				color: #111111;
				font-family: 'FrancoisOneRegular';
			}		

			#numeracao {
				position:relative; 
				float:right;
				right: 20px;
			}

			#acoes {
				position:relative; 
				float:left;
				left: 20px;
			}

			.table-responsive .table {
				margin-bottom: 0;
			}

			.rodape-box {
				font-size: 150%;
			}

			.relatorio_table {
				font-size: 25px;
			}

			#titulo h1 {
				font-size: 350%;
			}

			.relatorio_titulo {
				margin-top:0px; 
				font-family: 'FrancoisOneRegular';
				font-size: 280%;
			}

			.legend-val-box {
				font-family:FrancoisOneRegular;position:relative; 
				float:left; 
				width:5%; 
				text-align:right; 
				left:20px;
			}

			.legendColorBox div div {
			    border-width: 8px !important;
			}

			.flot-x-axis {
				margin-top: 10px;
			}
		</style>
	</head>
	<body>
		<input type="hidden" name="paginaHeight" id="paginaHeight" value="">
		<input type="hidden" name="valorLabelFormatar" id="valorLabelFormatar" value="">
		<input type="hidden" name="convVal" id="convVal" value="">
		<input type="hidden" name="graficoAtual" id="graficoAtual" value="0">
		<input type="hidden" name="graficoFinal" id="graficoFinal" value="<?=count($collection)?>">

		<div id="header">
			<div id="header_one">
				<div id="titulo">
						<h1><?= mes_extenso($row["mes"])." de ".$row["nr_ano"]?></h1>
					</div>
				<div id="logoFundacao">
					<img src="<?=base_url() ?>img/logo_ffp.png"/>
				</div>
			</div>
			<br/>
			<div id="acoes">
				<div class="btn-toolbar" role="toolbar" aria-label="...">
			  		<div class="btn-group" role="group" aria-label="Navegação">
				  		<button type="button" data-toggle="tooltip" title="Primeiro Slide" class="btn btn-default glyphicon glyphicon-fast-backward" onclick="primeiro();" id="btn_backward"></button>
						<button type="button" data-toggle="tooltip" title="<< Anterior" class="btn btn-default glyphicon glyphicon-backward" onclick="prev();" id="btn_prev"></button>
						<button type="button" data-toggle="tooltip" title="Próximo >>" class="btn btn-default glyphicon glyphicon-forward" onclick="next();" id="btn_next"></button>
						<button type="button" data-toggle="tooltip" title="Último Slide" class="btn btn-default glyphicon glyphicon-fast-forward" onclick="ultimo();" id="btn_forward"></button>
				  	</div>

				  	<div class="btn-group" role="group" aria-label="Zoom">
				  		<button type="button" title="Menos Zoom" data-toggle="tooltip" class="btn btn-default glyphicon glyphicon-zoom-out" id="btn_zoomout" onclick="zoomin()"></button>
				  		<button type="button" title="Mais Zoom" data-toggle="tooltip" class="btn btn-default glyphicon glyphicon glyphicon-zoom-in" id="btn_zoomin" onclick="zoomout()"></button>
				  	</div>

				  	<div class="btn-group" role="group" aria-label="Ações">
				  		<button type="button" title="Gerar PDF" data-toggle="tooltip" class="btn btn-default glyphicon glyphicon-print" id="btn_gera_pdf"></button>
				  	</div>	
				</div>
			</div>
			<div id="numeracao">
				<span id="numero_paginacao" class="badge" style="font-size:20px;"></span>
			</div>
			<br/><br/><br/>
		</div>
		
		<div id="pagina">
			<div id="paginaItem" class="span12 well well-black" style="height:92%; margin-bottom:0px;">
			<? foreach ($collection as $key => $item): ?>
				<? $i = 0; ?>
				<div id="grafico_<?=$key?>" style="height:100%; width:100%;">
					<? if($item["tp_grafico"] != "E") : ?>
					<center>
						<h2 class="relatorio_titulo"><?= utf8_decode($item["titulo"]) ?></h2>
					</center>
					<? endif;?>
					<? if($item["tp_grafico"] == "L" OR $item["tp_grafico"] == "B" OR $item["tp_grafico"] == "P" OR $item["tp_grafico"] == "A"): ?>
					<input type="hidden" name="slide_tp_<?=$key?>" id="slide_tp_<?=$key?>" value="G">
		            <div id="placeholder_<?=$key?>" class="grafico-box" style="position:relative; float:left; width:97%; "></div>
					<div id="legendContainer_<?=$key?>" class="legend-box" style="position:relative; float:left; width:100%;"></div>   
					
					<input type="hidden" name="countLegenda_<?=$key?>" id="countLegenda_<?=$key?>" value="">

					<? elseif($item["tp_grafico"] == "T"): ?>
					<input type="hidden" name="slide_tp_<?=$key?>" id="slide_tp_<?=$key?>" value="T">
					<div class="grafico-box">
						<div class="table-responsive">
							<? if((is_array($item["tab_campo"])) AND (count($item["tab_campo"]) > 0)): ?>
							
							<table class="table table-striped table-bordered table_table relatorio_table">
								<thead>
									<tr style="background:#327CAA; color:white; font-weight:bold;">
										<th <?=($item["rowspan"] ? 'rowspan="2"' : '')?> ><?=utf8_decode($item["ds_mes"])?></th>
										<? foreach ($item["tab_campo"] as $key2 => $item2): ?>
										
										<? switch ($item2) 
										{
										    case "nr_participacao":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Part. (%)</th>";
										        break;
										    case "nr_valor_atual":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Valor em mil R$</th>";
										        break;
										    case "nr_valor_integralizar":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Valor a Integralizar</th>";
										        break;
										     case "nr_realizado":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Realizado em mil R$</th>";
										        break;
										    case "nr_fluxo":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Fluxo de Caixa</th>";
										        break;
										    case "nr_participacao_fundo":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Part. no Fundo (%)</th>";
										        break;
										    case "nr_taxa_adm":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Taxa de Adm.</th>";
										        break;
										    case "nr_ano_vencimento":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Vencimento</th>";
										        break;
										    case "nr_participacao_metro":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Part. M² (%)</th>";
										        break;
										    case "nr_metro":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">M²</th>";
										        break;
										    case "nr_quantidade":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Quantidade</th>";
										        break;
										    case "nr_rentabilidade":
										        echo "<th style='text-align:center;' colspan='2'>Rentabilidade</th>";
										        break;
										    case "nr_politica_min":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Limite Política Mín (%)</th>";
										        break;
										    case "nr_alocacao_estrategica":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Alocação Estratégica (%)</th>";
										        break;
										    case "nr_politica_max":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Limite Política Máx (%)</th>";
										        break;
										    case "nr_legal_max":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Limites Legal (%)</th>";
										        break;
										}?>
										
										<? endforeach; ?>
									</tr>

									<? if($item["rowspan"]): ?>
									<tr style="background:#327CAA; color:white; font-weight:bold;">
										<th style='text-align:center;'>Mês</th>
										<th style='text-align:center;'>Ano</th>
									</tr>
									<? endif; ?>
								</thead>
								<tbody>
									<? foreach ($item["tabelas"] as $key2 => $item2): ?>
									<? if($i > 0 AND $key2 == ($i+2)) : ?>
									<? if(($key2 % 2) == 0) : ?>
									<tr style="height:35px;">
									<? else : ?>
									<tr style="background-color: #7BB0E7; height:35px;">
									<? endif;?>
										<td></td>
										<? foreach ($item["tab_campo"] as $key3 => $item3): ?>
											<? if($item3 == "nr_rentabilidade"): ?>
												<td></td>
												<td></td>
											<? else:?>
												<td></td>
											<? endif;?>
										<? endforeach; ?>
									</tr>
									<? endif;?>

									<? if(($item2["tipo"] == "T") OR ($item2["linha"] == "S")) : ?>
									<tr style="background-color: #F58C36;">
									<? elseif(($key2 % 2) == 0) : ?>
									<tr style="background-color: #7BB0E7;">
									<? else : ?>
									<tr>
									<? endif;?>
										<td style="<?=($item2["negrito"] == "S" ? "font-weight:bold;" : "")?>">
											<? if(isset($item2["tab"])) : ?>
												<? for ($t=0; $t < $item2["tab"] ; $t++) :?>
													&emsp;											
												<? endfor; ?>
											<? endif;?>
											<?=utf8_decode($item2["ds"]) ?>
										</td>
										<? foreach ($item["tab_campo"] as $key3 => $item3): ?>

										<? if($item3 == "nr_rentabilidade"): ?>
											<td style="text-align:right; <?=($item2["negrito"] == "S" ? "font-weight:bold;" : "")?>" ><?=(isset($item2["rentabilidade_mes"]) ? (app_decimal_para_db($item2["rentabilidade_mes"]) == 0 ? "--" : $item2["rentabilidade_mes"]) : "--")?></td>
											<td style="text-align:right; <?=($item2["negrito"] == "S" ? "font-weight:bold;" : "")?>" ><?=(isset($item2["rentabilidade_ano"]) ? (app_decimal_para_db($item2["rentabilidade_ano"]) == 0 ? "--" : $item2["rentabilidade_ano"]) : "--")?></td>
										<? else:?>
											<td style="text-align:right; <?=($item2["negrito"] == "S" ? "font-weight:bold;" : "")?>"><?=(isset($item2[$item3]) ? (app_decimal_para_db($item2[$item3]) == 0 ? "--" : $item2[$item3]) : "--")?></td>
										<? endif;?>

										<? endforeach; ?>
									</tr>
									<? $i = $key2; ?>
									<? endforeach; ?>
								</tbody>
							</table>
							<? if(count($item["tabela_anterior"]) > 0) : ?>
							<br/>
							<table class="table table-striped table-bordered table_table relatorio_table">
								<thead>
									<tr style="background:#327CAA; color:white; font-weight:bold;">
										<th <?=($item["rowspan"] ? 'rowspan="2"' : '')?> >Dezembro/<?= $row["nr_ano"]-1 ?></th>
										<? foreach ($item["tab_campo"] as $key2 => $item2): ?>
										
										<? switch ($item2) 
										{
										    case "nr_participacao":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Part. (%)</th>";
										        break;
										    case "nr_valor_atual":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Valor em mil R$</th>";
										        break;
										    case "nr_valor_integralizar":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Valor a Integralizar</th>";
										        break;
										     case "nr_realizado":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Realizado em mil R$</th>";
										        break;
										    case "nr_fluxo":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Fluxo de Caixa</th>";
										        break;
										    case "nr_participacao_fundo":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Part. no Fundo (%)</th>";
										        break;
										    case "nr_taxa_adm":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Taxa de Adm.</th>";
										        break;
										    case "nr_ano_vencimento":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Vencimento</th>";
										        break;
										    case "nr_participacao_metro":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Part. M² (%)</th>";
										        break;
										    case "nr_metro":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">M²</th>";
										        break;
										    case "nr_quantidade":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Quantidade</th>";
										        break;
										    case "nr_rentabilidade":
										        echo "<th style='text-align:center;' colspan='2'>Rentabilidade</th>";
										        break;
										    case "nr_politica_min":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Limite Política Mín (%)</th>";
										        break;
										    case "nr_alocacao_estrategica":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Alocação Estratégica (%)</th>";
										        break;
										    case "nr_politica_max":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Limite Política Máx (%)</th>";
										        break;
										    case "nr_legal_max":
										        echo "<th style='text-align:center;' ".($item["rowspan"] ? "rowspan='2'" : "").">Limites Legal (%)</th>";
										        break;
										}?>
										
										<? endforeach; ?>
									</tr>

									<? if($item["rowspan"]): ?>
									<tr style="background:#327CAA; color:white; font-weight:bold;">
										<th style='text-align:center;'>Mês</th>
										<th style='text-align:center;'>Ano</th>
									</tr>
									<? endif; ?>
								</thead>
								<tbody>
									<? foreach ($item["tabela_anterior"] as $key2 => $item2): ?>
									<? if($i > 0 AND $key2 == ($i+2)) : ?>
									<? if(($key2 % 2) == 0) : ?>
									<tr style="height:35px;">
									<? else : ?>
									<tr style="background-color: #7BB0E7; height:35px;">
									<? endif;?>
										<td></td>
										<? foreach ($item["tab_campo"] as $key3 => $item3): ?>
											<? if($item3 == "nr_rentabilidade"): ?>
												<td></td>
												<td></td>
											<? else:?>
												<td></td>
											<? endif;?>
										<? endforeach; ?>
									</tr>
									<? endif;?>

									<? if(($item2["tipo"] == "T") OR ($item2["linha"] == "S")) : ?>
									<tr style="background-color: #F58C36;">
									<? elseif(($key2 % 2) == 0) : ?>
									<tr style="background-color: #7BB0E7;">
									<? else : ?>
									<tr>
									<? endif;?>
										<td style="<?=($item2["negrito"] == "S" ? "font-weight:bold;" : "")?>">
											<? if(isset($item2["tab"])) : ?>
												<? for ($t=0; $t < $item2["tab"] ; $t++) :?>
													&emsp;											
												<? endfor; ?>
											<? endif;?>
											<?= utf8_decode($item2["ds"]) ?>
										</td>
										<? foreach ($item["tab_campo"] as $key3 => $item3): ?>

										<? if($item3 == "nr_rentabilidade"): ?>
											<td style="text-align:right; <?=($item2["negrito"] == "S" ? "font-weight:bold;" : "")?>" ><?=(isset($item2["rentabilidade_mes"]) ? (app_decimal_para_db($item2["rentabilidade_mes"]) == 0 ? "--" : $item2["rentabilidade_mes"]) : "--")?></td>
											<td style="text-align:right; <?=($item2["negrito"] == "S" ? "font-weight:bold;" : "")?>" ><?=(isset($item2["rentabilidade_ano"]) ? (app_decimal_para_db($item2["rentabilidade_ano"]) == 0 ? "--" : $item2["rentabilidade_ano"]) : "--")?></td>
										<? else:?>
											<td style="text-align:right; <?=($item2["negrito"] == "S" ? "font-weight:bold;" : "")?>"><?=(isset($item2[$item3]) ? (app_decimal_para_db($item2[$item3]) == 0 ? "--" : $item2[$item3]) : "--")?></td>
										<? endif;?>

										<? endforeach; ?>
									</tr>
									<? $i = $key2; ?>
									<? endforeach; ?>
								</tbody>
							</table>

							<? endif;?>
							<? else: ?>
							<center><h1 style="color:red;">Nenhum campo para ser visualizado foi cadastrado na configuração.</h1></center>
							<? endif;?>
							<div id="rodape_<?=$key?>" class="rodape-box"><b><?=(isset($item["nota_rodape"]) ? br().utf8_decode($item["nota_rodape"]) : "")?></b></div> 
							<br/><br/><br/>  
						</div>
					</div>
					<div class="legend-box"></div>   
					<? elseif($item["tp_grafico"] == "R") : ?>
					<input type="hidden" name="slide_tp_<?=$key?>" id="slide_tp_<?=$key?>" value="T">
					<div class="grafico-box">
						<div class="table-responsive">
							<table class="table table-striped table-bordered table_table relatorio_table">
								<thead>
									<tr style="background:#327CAA; color:white; font-weight:bold;">
										<th style="text-align:center;">Ano</th>
										<th style="text-align:center;">Nominal</th>
										<th style="text-align:center;">INPC</th>
										<th style="text-align:center;">Real</th>
									</tr>
								</thead>
								<tbody>
									<? foreach ($item["rent_hist"] as $key_rent => $item_rent): ?>
										<? if($item_rent[0] == $row["nr_ano"]) : ?>
										<tr style="background-color: #F58C36;">
										<? elseif(($key_rent % 2) == 0) : ?>
										<tr style="background-color: #7BB0E7;">
										<? else : ?>
										<tr style="background-color: #FFFFFF;">
										<? endif;?>
											<? foreach ($item_rent as $key_rent_item => $item_rent_item): ?>
											
											<?
												$class_table = "";

												if($key_rent_item == 0) 
												{
													$class_table = "text-align:center; font-weight:bold;";
												}
												elseif($item_rent[0] == $row["nr_ano"])
												{
													$class_table = "text-align:right; font-weight:bold;";
												}
												else
												{
													$class_table = "text-align:right;";
												}
											?>

											<td style="<?= $class_table ?>"><?=(isset($item_rent_item) ? $item_rent_item : "")?></td>
											<? endforeach; ?>
										</tr>
									<? endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
					<div class="legend-box"></div>  
					<? elseif($item["tp_grafico"] == "E") : ?>
					<input type="hidden" name="slide_tp_<?=$key?>" id="slide_tp_<?=$key?>" value="E">
				 	<div class="grafico-box">
				 		<div class="texto_html" >
							<?= $item["ds_html"] ?>
						</div>	 		
			 		</div>
					<? endif;?>
				</div>
			<? endforeach; ?>
			</div>
		</div>
	</body>
</html>