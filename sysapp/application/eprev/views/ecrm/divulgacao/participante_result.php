<?php
$this->load->helper('grid');

echo '<div id="mapa_participante" style="height: 400px; width: 100%"></div>';
echo br(2);
echo '<table border="0" width="100%" cellpadding="5" cellspacing="5">';

#### LINHA 1 ####
echo '
			<tr>
				<td valign="top" align="center" >
	 ';
				#### EMPRESA ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_empresa as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_empresa.'" border="0">';
				echo $grid->render();
echo ' 
				</td>
				<td valign="top" align="center">
	 ';
				#### PLANO ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_plano as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_plano.'" border="0">';
				echo $grid->render();
echo ' 
				</td>
				<td valign="top" align="center">
	 ';
				#### TEMPO PLANO ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_tempo_plano as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_tempo_plano.'" border="0">';
				echo $grid->render();
echo ' 
				</td>
			</tr>
	 ';	 
	 
	 
	 
	 
#### LINHA 2 ####	 
echo '
			<tr>
				<td valign="top" align="center" >
	 ';
				#### TIPO ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_tipo as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_tipo.'" border="0">';
				echo $grid->render();
echo ' 
				</td>
				<td valign="top" align="center">
	 ';
				#### SENHA ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_senha as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_senha.'" border="0">';
				echo $grid->render();
echo ' 
				</td>
				<td valign="top" align="center">
	 ';
				#### SEXO ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_sexo as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_sexo.'" border="0">';
				echo $grid->render();
echo ' 
				</td>
			</tr>
	 ';	 
	 
	 
	 
	 
#### LINHA 3 ####	 
echo '
			<tr>
				<td valign="top" align="center" >
	 ';
				#### IDADE ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_idade as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_idade.'" border="0">';
				echo $grid->render();
echo ' 
				</td>
				<td valign="top" align="center">
	 ';
				#### RENDA ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_renda as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_renda.'" border="0">';
				echo $grid->render();
echo ' 
				</td>
				<td valign="top" align="center">
	 ';
				#### UF ####
				$body = array();
				$head = array('Item','Quantidade');
				foreach($ar_uf as $item)
				{
					$body[] = array(
						array($item["ds_item"],"text-align:left;"),
						array(number_format($item["qt_item"],0,",","."),'text-align:right;','int')
					);
				}

				$grid = new grid();
				$grid->head = $head;
				$grid->body = $body;
				$grid->view_count = false;
				echo '<img src="'.$img_uf.'" border="0">';
				echo $grid->render();
echo ' 
				</td>
			</tr>
	 ';		 
	 
echo '</table>';
echo br(5);
?>
<script>
	function iniciarMapa() {
		var markers = [];	
		
		var options = {
			zoom: 6,
			center: new google.maps.LatLng(-30.030037,-51.228660),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		var map = new google.maps.Map(document.getElementById("mapa_participante"), options);

		$.post('<?php echo site_url("ecrm/divulgacao/participanteMapaCidade/");?>',
			$("#filter_bar_form").serialize(),
			function(data)
			{
				
				var latlngbounds = new google.maps.LatLngBounds();
			
				$.each(data, function(index, ponto) {
					var marker = new google.maps.Marker({
						  position: new google.maps.LatLng(ponto.latitude, ponto.longitude),
						  map: map,
						  icon: 'http://maps.gstatic.com/mapfiles/ridefinder-images/mm_20_red.png',
						  title: ponto.ds_cidade
					  });			
					
					markers.push(marker);
					latlngbounds.extend(marker.position);
				});
				
				var markerCluster = new MarkerClusterer(map, markers);
				map.fitBounds(latlngbounds);
			},
			'json'
		);		
	}	
	$(function(){
		iniciarMapa();
	});
</script>