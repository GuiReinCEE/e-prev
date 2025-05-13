<?php

$ar_cidade = "";
$ar_uf = "";
foreach($collection as $item)
{
	if(trim($item['cidade']) != "")
	{
		$ar_cidade.= (trim($ar_cidade) != "" ? "," : "").$item['cidade'];
	}
	
	if(trim($item['uf']) != "")
	{
		$ar_uf.= (trim($ar_uf) != "" ? "," : "").$item['uf'];
	}
}
?>
<div id="mapa_participante" style="height: 600px; width: 100%"></div>

<form id='mapa_form' name='mapa_form' onsubmit='return false;'>
<input type="hidden" name="ar_cidade" name="ar_cidade" value="<?php echo $ar_cidade;?>">
<input type="hidden" name="ar_uf" name="ar_uf" value="<?php echo $ar_uf;?>">
</form>
<script>
	function iniciarMapa() {
		var markers = [];	
		
		var options = {
			zoom: 6,
			center: new google.maps.LatLng(-30.030037,-51.228660),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};

		var map = new google.maps.Map(document.getElementById("mapa_participante"), options);

		$.post('<?php echo site_url("ecrm/relacionamento_empresa/mapaCidade/");?>',
			$("#mapa_form").serialize(),
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