<?php
$ar_fase[1] = "Primeira Fase";
$ar_fase[2] = "Oitavas de Final";
$ar_fase[3] = "Quartas de Final";
$ar_fase[4] = "Semi-final";
$ar_fase[5] = "Decisão do terceiro lugar";
$ar_fase[6] = "Decisão da Copa do Mundo 2018";

#if($cd_usuario_palpite == $this->session->userdata('codigo'))
if(1==0)
{
#EDIÇÃO
echo '
		<table border="0" cellspacing="2" cellpadding="2" align="center">
			<tr>
				<td valign="top">
					<table id="table-'.$cd_fase.'" class="sort-table" cellspacing="2" cellpadding="2" align="center">
						<caption style="font-weight:bold; font-size: 12pt; #535C65; font-family: calibri, tahoma,verdana,helvetica; ">'.$ar_fase[$cd_fase].'</caption>
						<thead>
						<tr>
							<th width="40">Ref</th>
							<th width="100">Dt Jogo</th>
							<th width="40"></th>
							<th width="50"></th>
							<th width="55"></th>
							<th width="32"></th>
							<th width="55"></th>
							<th width="50"></th>
							<th width="40"></th>
							<th width="68">'.($cd_fase == 1 ? "Grupo" : "Vencedor").'</th>
							<th width="75">Local</th>				
							<th width="75">Pontos</th>				
						</tr>
						</thead>
						<tbody>
				  ';
				foreach ($collection as $item)
				{	  
					echo '	  
							<tr onmouseout="sortSetClassOut(this);" onmouseover="sortSetClassOver(this);">
								<td>'.$item["cd_jogo"].'</td>
								<td align="center">'.$item["dt_jogo"].'</td>
								<td align="center"><img src="'.base_url()."img/copa/".$item["bandeira_1"].'" border="0" title="'.$item["ds_pais_1"].'"></td>
								<td align="center" style="font-weight:bold; font-size: 160%;">'.$item["sigla_1"].'</td>
								<td align="center" style="font-weight:bold; font-size: 160%;">
									'.form_input(array("id" => $item["cd_jogo"].'_'.$item["cd_pais_1"].'_pg1'), $item["nr_gol_pais_1"], 'style="color:#0046AD; font-weight:bold; font-size: 160%; width: 40px; text-align:center;"').'
									<script> 
										jQuery(function($){ 
											$("#'.$item["cd_jogo"].'_'.$item["cd_pais_1"].'_pg1").numeric();
											$("#'.$item["cd_jogo"].'_'.$item["cd_pais_1"].'_pg1").change(function() {
												setResultado('.$item["cd_fase"].', '.$item["cd_jogo"].', 1, $("#'.$item["cd_jogo"].'_'.$item["cd_pais_1"].'_pg1").val());
												
												$("#'.$item["cd_jogo"].'_vence").hide();
												if(($("#'.$item["cd_jogo"].'_'.$item["cd_pais_1"].'_pg1").val() != "") && ($("#'.$item["cd_jogo"].'_'.$item["cd_pais_2"].'_pg2").val() != ""))
												{
													if($("#'.$item["cd_jogo"].'_'.$item["cd_pais_1"].'_pg1").val() == $("#'.$item["cd_jogo"].'_'.$item["cd_pais_2"].'_pg2").val())
													{
														$("#'.$item["cd_jogo"].'_vence").val(0);
														$("#'.$item["cd_jogo"].'_vence").show();
													}
												}												
											});							
										}); 
									</script>					
								</td>
								<td align="center"><img src="'.base_url().'img/copa/x.png" border="0"></td>
								<td align="center" style="font-weight:bold; font-size: 160%;">
									'.form_input(array("id" => $item["cd_jogo"].'_'.$item["cd_pais_2"].'_pg2'), $item["nr_gol_pais_2"], 'style="color:#0046AD; font-weight:bold; font-size: 160%; width: 40px; text-align:center;"').'
									<script> 
										jQuery(function($){ 
											$("#'.$item["cd_jogo"].'_'.$item["cd_pais_2"].'_pg2").numeric();
											$("#'.$item["cd_jogo"].'_'.$item["cd_pais_2"].'_pg2").change(function() {
												setResultado('.$item["cd_fase"].', '.$item["cd_jogo"].', 2, $("#'.$item["cd_jogo"].'_'.$item["cd_pais_2"].'_pg2").val());
												
												$("#'.$item["cd_jogo"].'_vence").hide();
												if(($("#'.$item["cd_jogo"].'_'.$item["cd_pais_1"].'_pg1").val() != "") && ($("#'.$item["cd_jogo"].'_'.$item["cd_pais_2"].'_pg2").val() != ""))
												{
													if($("#'.$item["cd_jogo"].'_'.$item["cd_pais_1"].'_pg1").val() == $("#'.$item["cd_jogo"].'_'.$item["cd_pais_2"].'_pg2").val())
													{
														$("#'.$item["cd_jogo"].'_vence").val(0);
														$("#'.$item["cd_jogo"].'_vence").show();
													}
												}
											});							
										}); 
									</script>				
								</td>
								<td align="center" style="font-weight:bold; font-size: 160%;">'.$item["sigla_2"].'</td>
								<td align="center"><img src="'.base_url()."img/copa/".$item["bandeira_2"].'" border="0" title="'.$item["ds_pais_2"].'"></td>
								<td align="center" style="font-weight:bold; font-size: 160%; color:#535C65;">
								
									'.($cd_fase == 1 ? $item["cd_grupo"] : 
										'
										<select id="'.$item["cd_jogo"].'_vence" style="'.((((trim($item["nr_gol_pais_1"]) != "") and (trim($item["nr_gol_pais_2"]) != "")) and ($item["nr_gol_pais_1"] == $item["nr_gol_pais_2"])) ? "" : "display:none").'; color:#0046AD; font-weight:bold; font-size: 100%;">
											<option value="0" '.(intval($item["nr_vencedor"]) == 0 ? "selected" : "").'></option>
											<option value="1" '.(intval($item["nr_vencedor"]) == 1 ? "selected" : "").'>'.$item["sigla_1"].'</option>
											<option value="2" '.(intval($item["nr_vencedor"]) == 2 ? "selected" : "").'>'.$item["sigla_2"].'</option>
										</select>
										<script> 
											jQuery(function($){ 
												$("#'.$item["cd_jogo"].'_vence").change(function() {
													setVencedor('.$item["cd_fase"].', '.$item["cd_jogo"].', $("#'.$item["cd_jogo"].'_vence").val());
												})							
											}); 
										</script>										
										').'
								</td>
								<td>'.$item["ds_estadio"].'</td>
								<td align="center" >
									'.(!in_array(intval($item["cd_jogo"]), array(63,64)) ? '<span style="font-weight:bold; font-size: 220%; color:#028639;">'.intval($item["nr_ponto"]).'</span>' : 
										'								
										
										<table border="0" cellspacing="0" cellpadding="0" align="center">
											<tr>
												<td align="center">Pontos</td>
												<td align="center">+</td>
												<td align="center">Extra</td>
												<td align="center">=</td>
												<td align="center">Total</td>
											</tr>											
											<tr>
												<td align="center" style="font-weight:bold;">'.intval($item["nr_ponto"]).'</td>
												<td align="center">+</td>
												<td align="center" style="font-weight:bold;">'.intval($item["nr_ponto_extra"]).'</td>
												<td align="center">=</td>
												<td align="center" style="font-weight:bold; font-size: 220%; color:#028639;">'.(intval($item["nr_ponto"]) + intval($item["nr_ponto_extra"])).'</td>
											</tr>											
										</table>
										').'										
								
								</td>
							</tr>
						  ';	
				}	  
echo '			
							</tbody>
						</table>
				</td>
				<td width="30"></td>
				<td width="410" align="left" valign="top" id="tbClassifica-'.$cd_fase.'">
				</td>
			</tr>
		</table>
     ';
}
else
{
echo '
		<table border="0" cellspacing="2" cellpadding="2" align="center">
			<tr>
				<td valign="top">
					<table width="690" id="table-'.$cd_fase.'" class="sort-table" cellspacing="2" cellpadding="2" align="center">
						<caption style="font-weight:bold; font-size: 12pt; #535C65; font-family: calibri, tahoma,verdana,helvetica; ">'.$ar_fase[$cd_fase].'</caption>
						<thead>
						<tr>
							<th width="35">Ref</th>
							<th width="114">Dt Jogo</th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th>'.($cd_fase == 1 ? "Grupo" : "Vencedor").'</th>
							<th>Local</th>		
							<th width="75">Pontos</th>	
							<th>Acertadores</th>							
						</tr>
						</thead>
						<tbody>
				  ';
			foreach ($collection as $item)
			{	  
				echo '	  
						<tr onmouseout="sortSetClassOut(this);" onmouseover="sortSetClassOver(this);">
							<td valign="middle" >'.$item["cd_jogo"].'</td>
							<td valign="middle" align="center">'.$item["dt_jogo"].'</td>
							<td valign="middle" align="center"><img src="'.base_url()."img/copa/".$item["bandeira_1"].'" border="0" title="'.$item["ds_pais_1"].'"></td>
							<td valign="middle" align="center" style="font-weight:bold; font-size: 160%;">'.$item["sigla_1"].'</td>
							<td valign="middle" align="center" style="font-weight:bold; font-size: 220%; color:#0046AD;">'.$item["nr_gol_pais_1"].'</td>
							<td valign="middle" align="center"><img src="'.base_url().'img/copa/x.png" border="0"></td>
							<td valign="middle" align="center" style="font-weight:bold; font-size: 220%; color:#0046AD;">'.$item["nr_gol_pais_2"].'</td>
							<td valign="middle" align="center" style="font-weight:bold; font-size: 160%;">'.$item["sigla_2"].'</td>
							<td valign="middle" align="center"><img src="'.base_url()."img/copa/".$item["bandeira_2"].'" border="0" title="'.$item["ds_pais_2"].'"></td>
							<td valign="middle" align="center" style="font-weight:bold; font-size: 160%; color:#535C65;">
									'.(
										$cd_fase == 1 ? $item["cd_grupo"] : 
										(intval($item["nr_vencedor"]) == 1 ? $item["sigla_1"] : (intval($item["nr_vencedor"]) == 2 ? $item["sigla_2"] : ""))
									  ).'
								</td>							
							
							
							<td valign="middle" >'.$item["ds_estadio"].'</td>
							<td align="center" >
								<div>							
								'.(!in_array(intval($item["cd_jogo"]), array(63,64)) ? '<span style="font-weight:bold; font-size: 220%; color:#028639;">'.intval($item["nr_ponto"]).'</span>' : 
									'								
									
									<table border="0" cellspacing="0" cellpadding="0" align="center">
										<tr>
											<td align="center">Pontos</td>
											<td align="center">+</td>
											<td align="center">Extra</td>
											<td align="center">=</td>
											<td align="center">Total</td>
										</tr>											
										<tr>
											<td align="center" style="font-weight:bold;">'.intval($item["nr_ponto"]).'</td>
											<td align="center">+</td>
											<td align="center" style="font-weight:bold;">'.intval($item["nr_ponto_extra"]).'</td>
											<td align="center">=</td>
											<td align="center" style="font-weight:bold; font-size: 220%; color:#028639;">'.(intval($item["nr_ponto"]) + intval($item["nr_ponto_extra"])).'</td>
										</tr>											
									</table>
									').'										
									<BR>
									<span style="font-size: 80%; line-height: 10px; white-space:nowrap; font-weight:bold;">'.str_replace("{BASEURL}",base_url(),$item["resultado"]).'</span>
								</div>							
								
							</td>
							<td id="obAcertador_'.$item["cd_jogo"].'">
								<a href="javascript: void();"><img src="'.base_url().'img/grid/zoom_detalhe.png" border="0" onclick="getAcertouResultado('.$item["cd_jogo"].');"></a>
							</td>							
						</tr>
					  ';					  
			}	  
echo '			
						</tbody>
					</table>
				</td>
				<td width="30"></td>
				<td width="410" align="left" valign="top" id="tbClassifica-'.$cd_fase.'">
				</td>
			</tr>
		</table>
     ';
}
?>
