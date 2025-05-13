<BR>
<center>
<div style="width: 710px;">
<?php

foreach ($collection as $item)
{	
	echo '
			<div class="box-extrato-content box-extrato-statistic">
				<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td rowspan="4">
						<img src="'.base_url()."img/irpf_demonstrativo.png".'" border="0" >
						</td>
					</tr>
					<tr>
						<td><h3 class="title-extrato text-extrato-success" style="margin-left: 10px; '.(trim($item["dt_liberacao"]) == "" ? "color:red !important;" : "").'">ANO '.$item['nr_ano_calendario'].'</h3></td>
					</tr>
					<tr>
						<td><small style="margin-left: 10px; font-size: 9px;">Exercício: '.$item['nr_ano_exercicio'].'</small></td>
					<tr>
						<td>
							
							<a style="text-decoration:none;" href="'.site_url("cadastro/comprovante_irpf_colaborador/pdf/".md5($item["cd_comprovante_irpf_colaborador"])."/".md5($item["cd_registro_empregado"]))."/".session_id().'" target="_blank" title="Clique para abrir o Comprovante de Rendimentos IRRF de Ano Calendário: '.$item['nr_ano_calendario'].'">
								<small style="margin-left: 10px;">Visualizar</small>
							</a>
						</td>
					</tr>
				</table>
			</div>	
	     ';
}
?>
</div>
</center>
