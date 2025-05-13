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
			<td><b>C�lculos</b></td>
			<td><b>Gera��o<br>Indicador</b></td>
			<td><b>Gera��o<br>IGP</b></td>
			<td><b>Gr�ficos</b></td>
			<td><b>Hist�rico</b></td>
			<td><b>M�dia</b></td>
			<td><b>Tend�ncia</b></td>
			<td><b>Obs</b></td>
			</tr>";

		echo "<tr><td>".anchor('igp/avaliacao',"Avalia��o")."</td>
			<td>$done</td>
			<td>$manual</td>
			<td>$done</td>
			<td>$done</td>
			<td>$do</td>
			<td>$ok</td>
			<td>$do</td>
			<td>(autom�tico apenas IGP) (gr�fico do indicador MANUAL)</td>
			</tr>";

		echo "<tr><td>".anchor('igp/beneficio_erro',"Benef�cio Erro")."</td>
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td>
			<td>$done</td>
			<td>$ok</td> 
			<td></td> 
			</tr>";

		echo "<tr><td>".anchor('igp/calculo_inicial',"C�lculo Inicial")."</td>					
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

		echo "<tr><td>".anchor('igp/equilibrio',"Equil�brio")."</td>
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$done</td>
			<td>$done</td>
			<td>$ok</td> 
			<td></td> 
			</tr>";

		echo "<tr><td>".anchor('igp/informatica',"Inform�tica")."</td>							
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

		echo "<tr><td>".anchor('igp/reclamacao',"Reclama��o")."</td>							
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
			<td>Foi retirado do IGP, substitu�do pela Rentabilidade CI</td> 
			</tr>";

		echo "<tr><td>".anchor('igp/satisfacao_colab',"Satisfa��o dos Colaboradores")."</td>	
			<td>$done</td> 
			<td>$manual</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$do</td>
			<td>$ok</td>
			<td>$do</td>
			<td>(autom�tico apenas IGP) (gr�fico do indicador MANUAL)</td> 
			</tr>";

		echo "<tr><td>".anchor('igp/satisfacao_partic',"Satisfa��o dos Participantes")."</td>	
			<td>$done</td> 
			<td>$manual</td> 
			<td>$done</td> 
			<td>$done</td> 
			<td>$do</td>
			<td>$ok</td>
			<td>$do</td>
			<td>(autom�tico apenas IGP) (gr�fico do indicador MANUAL)</td> 
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

		echo "<tr><td>".anchor('igp/variacao_orcamentaria',"Varia��o Or�ament�ria")."</td>		
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

		
		echo "Formata��o das C�lulas - $do".br(2);
		echo "Oculta��o de Colunas - $do".br(2);
		echo "Permiss�o de usu�rios - $do (configurar depois do fim)".br(2);
		echo "Replica��o do per�odo - $do (configurar depois do fim)".br(2);
		echo "Proibir altera��o ap�s fechamento do per�odo - $do".br(2);

		echo anchor('igp/igp','IGP').br();
	}
}
?>