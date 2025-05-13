<?php
set_title('Copa - Tabela');
$this->load->view('header');
?>
<script>
$(function(){
});

function ir_tabela()
{
	location.href='<?php echo site_url("copa/copa/");?>';
}
function ir_minha()
{
	location.href='<?php echo site_url("copa/copa/minha/");?>';
}

function ir_resultado()
{
	location.href='<?php echo site_url("copa/copa/resultado/");?>';
}	
</script>
<?php
$abas[] = array('aba_tab', 'Tabela', FALSE, 'ir_tabela();');
$abas[] = array('aba_pal', 'Palpite', FALSE, 'ir_minha();');
$abas[] = array('aba_res', 'Resultado', FALSE, 'ir_resultado();');
$abas[] = array('aba_reg', 'Regulamento', TRUE, 'location.reload();');

echo aba_start( $abas );
	echo '
		<center>
		<div style="width: 780px;">
			<table width="100%" border="0" cellspacing="1" cellpadding="4" align="center">
				<tr>
					<td style="padding: 10px;" valign="top" bgcolor="#157fc0" align="left">
						<span style="color:#ffffff;font-family:verdana, arial;font-size:medium;"><b>REGRA 1: Valor e Prazo para Inscrição </b></span>
					</td>
				</tr>
				<tr bgcolor="#f0f0f8">
					<td align="left" style="padding: 10px;" align="left">
						<p>
							<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">
								Para participar não é necessário pagar a inscrição, somente é necessário pagar para concorrer a premiação.
								<BR><BR>
								Para participante basta informar os palpites, que devem ser preenchidos diretamente no e-prev até o dia <span style="color:#cc0000;"><b>14 de Junho de 2018 às 11 horas</b></span>. Depois deste prazo as inscrições estarão encerradas.
								<BR><BR>
								Para quem deseja concorrer a premiação, o valor de inscrição é de <span style="color:#cc0000;"><b>R$ 50,00</b></span> (cinquenta reais), devendo ser pago até o dia <span style="color:#cc0000;"><b>14 de Junho de 2018 às 11 horas</b></span> para o Ricardo Tortorelli.
							</span>
						</p>
						<BR>
					</td>
				</tr>
			</table>			
			<BR><BR>
			
			<table width="100%" border="0" cellspacing="1" cellpadding="4" align="center">
				<tbody>
					<tr>
						<td align="left" style="padding: 10px;" valign="top" bgcolor="#157fc0" align="left">
							<span style="color:#ffffff;font-family:verdana, arial;font-size:medium;"><b>REGRA 2: Premiação</b></span>
						</td>
					</tr>
					<tr>
						<td align="left" style="padding: 10px;" valign="top" bgcolor="#f0f0f8">
							<p align="left"><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">O valor total arrecadado será distribuído da seguinte maneira:</span></p>
							<BR>
							<p align="left"><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">1º Colocado:<b> <span style="color:#cc0000;">50%</span></b> (cinquenta por cento)</span></p>
							<BR>
							<p align="left"><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">2º Colocado:<b> <span style="color:#cc0000;">30%</span></b> (trinta por cento)</span></p>
							<BR>
							<p align="left"><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">3º Colocado:<b> <span style="color:#cc0000;">20%</span> </b>(vinte por cento) </span></p>
							<BR>
							<p align="left"><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Observação: Se houver empate entre um ou mais participantes, o prêmio será dividido entre eles.</span></p>
							<p>&nbsp;</p>
						</td>
					</tr>
				</tbody>
			</table>			
			<BR><BR>	
			
		
			
			<table width="100%" border="0" cellspacing="1" cellpadding="4" align="center">
				<tbody>
					<tr>
						<td align="left" style="padding: 10px;" valign="top" bgcolor="#157fc0" align="center" colspan="7">
							<div align="left"><span style="color:#ffffff;font-family:verdana, arial;font-size:medium;"><b>REGRA 3: Pontuação</b></span></div>
						</td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" colspan="6">
							<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Será possível marcar até 280 (pontos) por volante de palpites, assim distribuídos:</span>
							<BR>
							<BR>
							<span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">1ª Fase &ndash; Total 48 jogos = <span style="color:#990000;">144 </span>pontos no máximo</span></b></span><p></p>
							<BR>
							<p><span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">Oitavas, Quartas, Semifinais e Finais &ndash; Total 16 jogos = <span style="color:#990000;">96 </span>pontos no máximo</span></b></span></p>
							<BR>
							<p><span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">Acertando o Campeão, Vice, 3º e 4º Lugares =&nbsp; <span style="color:#990000;">40 </span>pontos no máximo</span></b></span></b></span></p>
							<BR>
						</td>
					</tr>
					<tr bgcolor="#8090c8">
						<td align="left" style="padding: 10px;" bgcolor="#157fc0" colspan="6">
							<span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>1ª Fase &ndash; Total 48 jogos = 144 pontos no máximo</b></span>
						</td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" colspan="6">
							<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Os apostadores marcarão pontos a cada jogo, até um máximo de <b>3 pontos</b> por jogo.</span>
							<BR><BR>
							<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Acertando o resultado do jogo (coluna 1, empate, ou coluna 2) :<b> 1 ponto</b></span></p>
							<BR>
							<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Acertando o número de gols feitos pelo time A:<b> 1 ponto</b></span></p>
							<BR>
							<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Acertando o número de gols feitos pelo time B:<b> 1 ponto</b></span><br>
							<BR>
							<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Vamos pegar como exemplo o<b> Jogo 2</b> da Copa passada: <b>Uruguai</b> x <b>França</b></span></p>
							<BR><BR>
						</td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" width="30%"><span style="color:#0780C6;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>Aposta do Participante A: </b></span></td>
						<td align="left" style="padding: 10px;" width="12%"><span style="color:#0780C6;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">URUGUAI</span></b></span></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><span style="color:#0780C6;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>3</b></span></div></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><span style="color:#0780C6;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">X</span></b></span></div></td>
						<td align="left" style="padding: 10px;" width="6%"><div align="center"><span style="color:#0780C6;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>0</b></span></div></td>
						<td align="left" style="padding: 10px;" ><span style="color:#000000;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>FRANÇA</b></span></td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" ><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Resultado do jogo: </span></b></td>
						<td align="left" style="padding: 10px;" width="12%"><div align="left"><b><span style="color:#0780C6;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">URUGUAI</span></b></span></b></div></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">3</span></b></div></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">X </span></b></div></td>
						<td align="left" style="padding: 10px;" width="6%"><div align="center"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">0</span></b></div></td>
						<td align="left" style="padding: 10px;" ><b><span style="color:#000000;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>FRANÇA</b></span></b></td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" colspan="6"><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Neste caso o <b><span style="color:#0780C6;">Participante A</span></b> terá marcado <b>3 (três) pontos</b>, <b>1 (um) ponto</b> por ter acertado a coluna 1 (Uruguai) e <b>1 (um)</b> <b>ponto</b> por ter acertado o número de gols marcados pelo Uruguai (três) e <b>1 (um)</b> <b>ponto</b> por ter acertado o número de gols marcados pela França (zero). </span></td>
					</tr>
					<tr bgcolor="#f0f0f8"><td colspan="7"><BR></td></tr>	
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" ><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#6E06E5;">Aposta do Participante B:</span> </b></span></td>
						<td align="left" style="padding: 10px;" width="12%"><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">URUGUAI</span></b></span></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><span style="color:#6E06E5;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>1</b></span></div></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">X</span></b></span></div></td>
						<td align="left" style="padding: 10px;" width="6%"><div align="center"><span style="color:#6E06E5;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>0</b></span></div></td>
						<td align="left" style="padding: 10px;" ><span style="color:#000000;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>FRANÇA</b></span></td>
					</tr>

					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" ><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Resultado do jogo: </span></b></td>
						<td align="left" style="padding: 10px;" width="12%"><div align="left"><b><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">URUGUAI</span></b></span></b></div></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">3</span></b></div></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">X </span></b></div></td>
						<td align="left" style="padding: 10px;" width="6%"><div align="center"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">0</span></b></div></td>
						<td align="left" style="padding: 10px;" ><b><span style="color:#000000;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>FRANÇA</b></span></b></td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td colspan="6"  style="padding: 10px;"><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Neste caso o <b><span style="color:#6E06E5;">Participante B</span></b> terá marcado <b>2 (dois) pontos</b>, <b>1 (um) ponto</b> por ter acertado a coluna 1 (Uruguai) e <b>1 (um)</b> <b>ponto</b> por ter acertado o número de gols marcados pela França (zero).</span></td>
					</tr>
					<tr bgcolor="#f0f0f8"><td colspan="7"><BR></td></tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" ><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#006600;">Aposta do Participante C:</span> </b></span></td>
						<td align="left" style="padding: 10px;" width="12%"><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">URUGUAI</span></b></span></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><span style="color:#3030ff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#006800;">0</span></b></span></div></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">X</span></b></span></div></td>
						<td align="left" style="padding: 10px;" width="6%"><div align="center"><span style="color:#3030ff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#006800;">0</span></b></span></div></td>
						<td align="left" style="padding: 10px;" ><span style="color:#000000;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>FRANÇA</b></span></td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" ><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Resultado do jogo: </span></b></td>
						<td align="left" style="padding: 10px;" width="12%"><div align="left"><b><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">URUGUAI</span></b></span></b></div></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">3</span></b></div></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">X </span></b></div></td>
						<td align="left" style="padding: 10px;" width="6%"><div align="center"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">0</span></b></div></td>
						<td align="left" style="padding: 10px;" ><b><span style="color:#000000;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>FRANÇA</b></span></b></td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" colspan="6" style="padding: 10px;"><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Neste caso o </span><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#006600;">Participante C</span></b></span><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"> terá marcado <b>1 (um) ponto</b>, por somente ter acertado o número de gols marcados pela França (zero).</span></td>
					</tr>
					<tr bgcolor="#f0f0f8"><td colspan="7"><BR></td></tr>					
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" ><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#009385;">Aposta do Participante D:</span> </b></span></td>
						<td align="left" style="padding: 10px;" width="12%"><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">URUGUAI</span></b></span></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><span style="color:#3030ff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#009385;">1</span></b></span></div></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">X</span></b></span></div></td>
						<td align="left" style="padding: 10px;" width="6%"><div align="center"><span style="color:#3030ff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#009385;">2</span></b></span></div></td>
						<td align="left" style="padding: 10px;" ><span style="color:#000000;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>FRANÇA</b></span></td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" ><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Resultado do jogo: </span></b></td>
						<td align="left" style="padding: 10px;" width="12%"><div align="left"><b><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#000000;">URUGUAI</span></b></span></b></div></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">3</span></b></div></td>
						<td align="left" style="padding: 10px;" width="3%"><div align="center"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">X </span></b></div></td>
						<td align="left" style="padding: 10px;" width="6%"><div align="center"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">0</span></b></div></td>
						<td align="left" style="padding: 10px;" ><b><span style="color:#000000;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>FRANÇA</b></span></b></td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" colspan="6">
							<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Neste caso o </span><span style="color:#ff0033;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><span style="color:#009385;">Participante D </span></b></span><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">não terá marcado <b>nenhum ponto.</b></span>
							<BR><BR>
						</td>
					</tr>
					<tr bgcolor="#8090c8">
						<td align="left" style="padding: 10px;" bgcolor="#157fc0" colspan="6">
							<span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>2ª Fase : Oitavas de Final, Quartas de Final, Semi-Finais e Finais &ndash; </b><br>
							<span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>Total 16 jogos = 96 pontos no máximo</b></span></span></span></span>
						</td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" colspan="6">
							<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Os apostadores <strong>continuarão</strong> a marcar pontos a cada jogo como na 1ª Fase, <strong>independentemente</strong> das seleções que se classificarem:</span></span>
							<BR>
							<BR>
							<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Acertando o resultado do jogo (coluna 1, empate, ou coluna 2) :<b> 2 pontos</b></span>
							<BR>
							<BR>
							<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Acertando o número de gols feitos pelo time A:<b> 2 pontos</b></span></p>
							<BR>
							<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Acertando o número de gols feitos pelo time B:<b> 2 pontos</b></span></p>
							<BR>
						</td>
					</tr>
					<tr bgcolor="#8090c8">
						<td align="left" style="padding: 10px;" bgcolor="#157fc0" colspan="6">
							<span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>Finais: Total 40 pontos no máximo</b></span>
						</td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" colspan="6">
							<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Os apostadores que acertarem as seleções que terminarem a Copa do Mundo de 2018 como Campeã, Vice-Campeã, Terceira Colocada e Quarta Colocada marcarão respectivamente:</span>
							<BR><BR>
						</td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" colspan="5"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Seleção Campeã</span></b></td>
						<td align="left" style="padding: 10px;" width="50%"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">16 pontos</span></b></td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" colspan="5"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Seleção Vice-Campeã</span></b></td>
						<td align="left" style="padding: 10px;" width="50%"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">12 pontos</span></b></td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" colspan="5"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Seleção Terceira Colocada</span></b></td>
						<td align="left" style="padding: 10px;" width="50%"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">8 pontos</span></b></td>
					</tr>
					<tr bgcolor="#f0f0f8">
						<td align="left" style="padding: 10px;" colspan="5"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Seleção Quarta Colocada</span></b></td>
						<td align="left" style="padding: 10px;" width="50%"><b><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">4 pontos</span></b></td>
					</tr>
				</tbody>
			</table>			
			<BR><BR>
			
			
<table width="100%" cellspacing="1" cellpadding="4" border="0" align="center">
<tbody>
<tr>
<td style="padding: 10px;"  valign="top" bgcolor="#157fc0" align="center" colspan="5">
<div align="left"><span style="color:#ffffff;font-family:verdana, arial;font-size:medium;"><b>REGRA 4: Como Jogar </b></span></div>
</td>
</tr>
<tr bgcolor="#8098d0">
<td align="left" style="padding: 10px;" bgcolor="#157fc0" colspan="4"><span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>1ª Fase &ndash; Total 48 jogos = 144 pontos no máximo</b></span></td>
</tr>
<tr bgcolor="#f0f0f8">
<td align="left" style="padding: 10px;" colspan="4">
<div align="left">
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Na página de palpites o participante encontrará uma tabela com todos os 48 (quarenta e oito) jogos da 1ª Fase da Copa do Mundo de 2018.</span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Os jogos estão numerados de <b>1 a 48</b> dentro de seus respectivos grupos e o apostador deverá escolher os resultados para os <b>48 jogos</b>.</span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Deve-se atentar para o seguinte detalhe:</span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">De acordo com os palpites do apostador, o sistema automaticamente identificará as seleções <b>primeira e segunda colocadas</b> de cada grupo e montará a tabela das <b>Oitavas-de-final</b> de acordo com esses palpites. Dessa maneira o participante não terá a necesidade de ficar somando os pontos de cada seleção para identificar quem passará para a próxima fase.</span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Para facilitar as coisas foi adotado o seguinte <b>critério de desempate</b>, no caso de os palpites do apostador levarem ao empate em número de pontos ganhos de duas ou mais seleções em primeiro ou segundo lugar em cada grupo: </span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>1º</b> Saldo de Gols</span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>2º</b> Número de Gols Marcados</span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>3º</b> Confronto direto</span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>4º</b> Ranking da FIFA de abril de 2018</span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Após o preenchimento dos palpites para os <b>48 jogos</b> da primeira fase, verifique se os seus palpites estão de acordo com sua preferência.</span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Se não estiver satisfeito com seus palpites, basta alterá-los.</span></p>
<BR>
</div>
</td>
</tr>
<tr bgcolor="#8098d0">
<td align="left" style="padding: 10px;" bgcolor="#157fc0" colspan="4"><span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b>2ª Fase : Oitavas de Final, Quartas de Final, Semi-Finais e Finais -</b></span></span><span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><span style="color:#ffffff;font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><b><br>
Total 16 jogos = 96 pontos no máximo&nbsp;</b></span></span></td>
</tr>
<tr bgcolor="#f0f0f8">
<td align="left" style="padding: 10px;" colspan="4">

<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">
<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Como foi dito anteriormente, o sistema automaticamente identifica as seleções <b>primeira e segunda colocadas</b> de cada grupo e monta a tabela das <b>Oitavas-de-final</b> de acordo com os palpites de cada apostador. </span></span>
<BR><BR>
<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;"><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Se, por ventura, o apostador não ficar satisfeito com suas escolhas, basta alterar os resultados dos jogos que desejar, com isto o sistema montará novamente a tabela das <b>Oitavas-de-final</b> de acordo com seus novos palpites.</span></span>
<BR><BR>
<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Cabe ressaltar que a partir das <b>Oitavas-de-final</b>, o resultado a ser considerado para fins do Bolão é o <b>placar final da partida no tempo regulamentar</b>, ou seja, sem considerar prorrogação ou decisão por penaltis no caso de a partida terminar empatada.</span><p></p>

<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">A partir das <b>Oitavas-de-final</b> o sistema de apostas se manterá o mesmo para as fases seguintes: basta digitar o resultado desejado para cada jogo da fase, lembrando que você pode apostar no empate entre duas seleções. Neste caso, você deverá informar qual das seleções que você acredita que vencerá o confronto na prorrogação ou nos penaltis, para que o sistema identifique qual delas passará para a próxima fase.</span></p>
<BR>
<p><strong><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Lembre-se que independentemente das seleções que passarem para as fases seguintes, você estará concorrendo com os placares em que apostar.</span></strong></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Depois de ter completado o preenchimento dos resultados das Finais, o sistema gerará automaticamete uma tabela com todos os seus palpites e também com a classificação final da Seleção Campeã, Vice-Campeã, Terceira Colocada e Quarta Colocada.</span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Neste momento você pode conferir todos os seus palpites e imprimir a tabela completa.</span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Se desejar alterar alguma coisa, basta voltar para as fases anteriores e alterar os resultados dos jogos que desejar.</span></p>
<BR>
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Mas atenção:<span style="color:#ff3333;"><b> após <span style="color:#000000;">14 de junho às 11 horas</span> não será mais possível alterar seus palpites!!!</b></span></span></p>
<BR>
</td>

</tr>
</tbody>
</table>
<BR><BR>

<table width="100%" cellspacing="1" cellpadding="4" border="0" align="center">
<tbody>
<tr>
<td style="padding: 10px;" valign="top" bgcolor="#157fc0" align="center" colspan="5">
<div align="left"><span style="color:#ffffff;font-family:verdana, arial;font-size:medium;"><b>REGRA 5: Conferindo os Resultados durante a Copa 2018</b></span></div>
</td>
</tr>
<tr bgcolor="#f0f0f8">
<td style="padding: 10px;" colspan="4">
<div align="left">
<p><span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">Após 14 de junho às 11 horas não será mais possível alterar seus palpites. Assim, na próxima vez em que acessar o Bolão clique na aba Resultados. Nesta página você poderá ver quantos pontos já marcou no bolão da Copa do Mundo 2018, quantos pontos os outros concorrentes marcaram e também qual o seu posicionamento em relação aos outros participantes.</span></p>
</div>
<BR>
</td>
</tr>
</tbody>
</table>
<BR><BR>


<table width="100%" cellspacing="1" cellpadding="4" border="0" align="center">
<tbody>
<tr>
<td style="padding: 10px;" valign="top" bgcolor="#157fc0" align="center" colspan="5">
<div align="left"><span style="color:#ffffff;font-family:verdana, arial;font-size:medium;"><b>REGRA 6: Atualizações dos Resultados no Site</b></span></div>
</td>
</tr>
<tr bgcolor="#f0f0f8">
<td align="left" style="padding: 10px;" height="15" colspan="4">
	<span style="font-family:Verdana, Arial, Helvetica, sans-serif;font-size:small;">
	Durante toda a Copa do Mundo, o e-prev será atualizado diariamente até às 22:00 horas com os resultados dos jogos daquele dia.
	<BR><BR>
	Desta maneira todos poderão acompanhar os resultados do Bolão diariamente na Internet pelo seu computador, tablet ou celular através do e-prev.
	</span>
	<BR>
	<BR>
</td>
</tr>
</tbody>
</table>
<BR><BR>


			
			
			
			
			
			
		</div>
		<center>
	     ';
	echo br(10);
echo aba_end();
$this->load->view('footer'); 
?>