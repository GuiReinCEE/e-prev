<?php
class Igp_status extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function index()
	{
		$done = "<span style='color:GREEN;font-weight:bold;'>Done</span>";
		$do = "<span style='color:RED;font-weight:bold;'>DO</span>";
		$manual = "<span style='color:ORANGE;font-weight:bold;'>Manual</span>";
		$ok = "<span style='color:ORANGE;font-weight:bold;'>OK</span>";
		$cancel = "<span style='color:ORANGE;font-weight:bold;'>Cancel</span>";

		echo "<table cellpadding='5'>";

		echo "<tr><td><b>Indicador</b></td>
			<td><b>Cálculos</b></td>
			<td><b>Geração<br>Indicador</b></td>
			<td><b>Geração<br>IGP</b></td>
			<td><b>Gráficos</b></td>
			<td><b>Histórico</b></td>
			<td><b>Média</b></td>
			<td><b>Tendência</b></td>
			<td><b>Obs</b></td>
			</tr>";

		echo "<tr><td>".anchor('igp/avaliacao',"Avaliação")."</td>
			<td>$done</td>
			<td>$manual</td>
			<td>$done</td>
			<td>$done</td>
			<td>$do</td>
			<td>$ok</td>
			<td>$do</td>
			<td>(automático apenas IGP) (gráfico do indicador MANUAL)</td>
			</tr>";

		echo "<tr><td>".anchor('igp/beneficio_erro',"Benefício Erro")."</td>
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td>
			<td>$done</td>
			<td>$ok</td> 
			<td></td> 
			</tr>";

		echo "<tr><td>".anchor('igp/calculo_inicial',"Cálculo Inicial")."</td>					
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td>
			<td>$done</td>
			<td>$ok</td> 
			<td></td> 
			</tr>";

		echo "<tr><td>".anchor('igp/custo_administrativo',"Custo Administrativo")."</td>
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td>
			<td>$done</td>
			<td>$ok</td> 
			<td></td> 
			</tr>";

		echo "<tr><td>".anchor('igp/equilibrio',"Equilíbrio")."</td>
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td>
			<td>$done</td>
			<td>$ok</td> 
			<td></td> 
			</tr>";

		echo "<tr><td>".anchor('igp/informatica',"Informática")."</td>							
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td>
			<td>$done</td>
			<td>$ok</td> 
			<td></td> 
			</tr>";

		echo "<tr><td>".anchor('igp/participante',"Participante")."</td>						
			<td>$done</td> 
			<td>$manual</td> 
			<td>$done</td>
			<td>$done</td>
			<td>$do</td>
			<td>$ok</td>
			<td>$ok</td> 
			<td></td> 
			</tr>";

		echo "<tr><td>".anchor('igp/reclamacao',"Reclamação")."</td>							
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td>
			<td>$done</td>
			<td>$ok</td> 
			<td></td> 
			</tr>";

		echo "<tr><td>".anchor('igp/rpp',"RPP")."</td>											
			<td>$cancel</td> 
			<td>$cancel</td> 
			<td>$cancel</td> 
			<td>$cancel</td> 
			<td>$cancel</td>
			<td>$cancel</td>
			<td>$cancel</td> 
			<td>Foi retirado do IGP, substituído pela Rentabilidade CI</td> 
			</tr>";

		echo "<tr><td>".anchor('igp/satisfacao_colab',"Satisfação dos Colaboradores")."</td>	
			<td>$done</td> 
			<td>$manual</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$do</td>
			<td>$ok</td>
			<td>$do</td>
			<td>(automático apenas IGP) (gráfico do indicador MANUAL)</td> 
			</tr>";

		echo "<tr><td>".anchor('igp/satisfacao_partic',"Satisfação dos Participantes")."</td>	
			<td>$done</td> 
			<td>$manual</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$do</td>
			<td>$ok</td>
			<td>$do</td>
			<td>(automático apenas IGP) (gráfico do indicador MANUAL)</td> 
			</tr>";

		echo "<tr><td>".anchor('igp/treinamento',"Treinamento")."</td>							
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td>
			<td>$done</td>
			<td>$ok</td> 
			<td></td> 
			</tr>";

		echo "<tr><td>".anchor('igp/variacao_orcamentaria',"Variação Orçamentária")."</td>		
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td>
			<td>$done</td>
			<td>$ok</td> 
			<td></td> 
			</tr>";

		echo "<tr><td>".anchor('igp/rentabilidade_ci',"Rentabilidade CI")."</td>		
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$ok</td>
			<td>$done</td>
			<td>$ok</td> 
			<td></td> 
			</tr>";

		echo "</table>".br(2);

		
		echo "Formatação das Células - $do".br(2);
		echo "Ocultação de Colunas - $do".br(2);
		echo "Permissão de usuários - $do (configurar depois do fim)".br(2);
		echo "Replicação do período - $do (configurar depois do fim)".br(2);
		echo "Proibir alteração após fechamento do período - $do".br(2);

		echo anchor('igp/igp','IGP').br();
	}
}
?>