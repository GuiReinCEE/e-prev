<?
	include ('inc/pdfClasses/class.ezpdf.php');
	include_once('inc/sessao.php');
	include_once('inc/conexao.php');
	include_once('inc/class.TemplatePower.inc.php');
	include_once('inc/jpgraph.php');
	include_once('inc/jpgraph_pie.php');
	include_once('inc/jpgraph_pie3d.php');
// ---------------------------------------------------------------------------------
// RELATÓRIO DE PESQUISAS
// --------------
// Estrutura:
// 1. Capa
// 2. Índice
// 3. Conteúdo
// 4. Encerramento (DADOS PARA PUBLICAÇÃO)
// ---------------------------------------------------------------------------------
	$meses = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	$mes_hoje = date("m");
	$ano_hoje = date("Y");
	$dia_hoje = date("d");
// --------------------------------------------------------------------------------- Capa (Dados da edição):
	$pdf =& new Cezpdf();
	$pdf->selectFont('inc/pdfClasses/fonts/Helvetica.afm');
	$sql = "select 	nome, obs1, obs2
			from 	mala_direta 
			where 	username = 'SGUIMARAES' order by username, cd_registro_empregado";
//	echo $sql;
	$rs = pg_exec($db, $sql);
	$cont = 0;
	$pdf->ezStartPageNumbers(-40,20,10,'','',1);
//-------------------------------------------------------------- 
	while ($reg = pg_fetch_array($rs)) {
		$pdf->setColor(0.0,0.0,0.0); // Para imprimir o texto em preto.
		$pdf->addpngFromFile('img/img_logo_fundacao_prev7.png',29,750,220,0);	
		$pdf->addJpegFromFile('img/img_plano_aes.jpg',510,730,62,0);
		$pdf->addJpegFromFile('img/img_marcadagua.jpg',200,30,395,0);
		$pdf->setstrokeColor(0.0,0.0,0.0); // <= Muda a cor das linhas para preto
		$pdf->setlinestyle(1);
		$pdf->line(27,747,260,747);
		$opc = array(justification=>'left', spacing=>1.5);  
		$opc2 = array(justification=>'center', spacing=>1.5);  
		$pdf->ezSetMargins(30,30,50,50);
		$pdf->ezSetY(720);
		$data_ingresso = $dia_hoje . ' de ' . $meses[$mes_hoje-1]  . ' de ' . $ano_hoje . '.';	
		$data_ingresso = '22 de Junho de 2007.';	// OS 13386
		$pdf->ezText('Porto Alegre, ' . $data_ingresso, 12, $opc);
		$pdf->ezSetY(690);
		$pdf->ezText('Ilmo(a) Sr.(a)', 12, $opc);
		$pdf->ezText($reg['nome'], 12, $opc);
		$opc = array(justification=>'left', spacing=>1.2);
		$pdf->ezSetY(630);
		$pdf->ezText('Informamos que foi aprovada pela Secretaria de Previdência Complementar a alteração ', 12, $opc);
		$pdf->ezText('regulamentar do Plano Único da AES Sul que afeta o cálculo do seu benefício. ', 12, $opc);
		$pdf->ezSetY(570);
		$pdf->ezText('Desta forma, o seu benefício será alterado de R$ '.$reg['obs1'], 15, $opc2);
		$pdf->ezSetY(530);
		$pdf->ezText('para R$ '.$reg['obs2'], 15, $opc2);
		$pdf->ezSetY(490);
		$pdf->ezText('Esta alteração passará a vigorar a partir de 1º de julho de 2007.', 15, $opc2);
		$pdf->ezSetY(440);
		$pdf->ezText('<b>Entenda a alteração</b>', 18, $opc2);
		$opc = array(justification=>'full', spacing=>1.0);  
		$pdf->ezText('Em dezembro de 2003, a Emenda Constitucional nº 41 do Governo Federal elevou o teto da Previdência Social de R$ 1.869,34  para R$ 2.400,00.  Considerando que a Fundação CEEE utiliza a média dos últimos 36 salários limitados ao teto da previdência no cálculo do benefício, este aumento repercutiu gradativamente nos valores de benefícios  iniciados a partir  de janeiro  de 2004.', 12, $opc);
		$pdf->ezSetY(328);
		$pdf->ezText('A alteração regulamentar foi aprovada pelo Conselho Deliberativo, pela AES Sul e pela Secretaria de Previdência Complementar. O  novo  texto,  aprovado  pela portaria nº 1.167, editada no Diário Oficial da União em 06 DE JUNHO DE 2007 prevê a manutenção  do  valor  do teto  da  Previdência  Social  nos  níveis vigentes antes da  Emenda  Constitucional  nº 41. Este   valor  é  atualizado anualmente pelo INPC.', 12, $opc);
		$pdf->ezSetY(245);
		$pdf->ezText('Artigos do regulamento que foram alterados: 46 e 48. O regulamento está disponível na íntegra no site da Fundação: www.fundacaoceee.com.br', 12, $opc);
		$pdf->ezSetY(180);
		$pdf->ezText('Para mais informações consulte a edição de número 2 da  "Fundação CEEE em revista" onde está publicada a  matéria "Como é calculado o benefício no Plano Único" ou ligue para 0800 51 2596.', 12, $opc);
		$pdf->ezSetY(110);
		$pdf->ezText('Atenciosamente,', 12, $opc);		
		$pdf->ezSetY(75);
		$pdf->ezText('Diretoria Executiva', 12, $opc);		
		$pdf->ezText('Fundação CEEE.', 12, $opc);
		$pdf->setstrokeColor(0.2,0.5,0.2); // <= Muda a cor das linhas para verde
		$pdf->rectangle(20,450,555,120); // <= Desenha a margem externa
		$pdf->rectangle(20,200,555,230); // <= Desenha a margem externa
		$pdf->ezNewPage();
	}
	
	$pdf->setColor(1.0,1.0,1.0); // Para imprimir o número da página em branco.
//---------------------------------------------------------------------------
	pg_close($db);
//---------------------------------------------------------------------------
	$pdf->ezStream();
 function theRealStripTags2($string)
{

    $tam=strlen($string);
    // tam have number of cars the string

    $newstring="";
    // newstring will be returned

    $tag=0;
    /* tag = 0 => copy car from string to newstring
       tag > 0 => don't copy. Find one or mor tag '<' and
          need to find '>'. If we find 3 '<' need to find
          all 3 '>'
    */

    /* I am C programm. seek in a string is natural for me
        and more efficient

        Problem: copy a string to another string is more
        efficient but use more memory!!!
    */
    for ($i=0; $i < $tam; $i++){

        /* If I find one '<', $tag++ and continue whithout copy*/
        if ($string{$i} == '<'){
            $tag++;
            continue;
        }

        /* if I find '>', decrease $tag and continue */
        if ($string{$i} == '>'){
            if ($tag){
                $tag--;
            }
        /* $tag never be negative. If string is "<b>test</b>>" (error, of course)
            $tag stop in 0
        */
            continue;
        }

        /* if $tag is 0, can copy */
        if ($tag == 0){
            $newstring .= $string{$i}; // simple copy, only car
        }
    }
        return $newstring;
}
function unhtmlentities ($string) {
   $trans_tbl1 = get_html_translation_table (HTML_ENTITIES);
   foreach ( $trans_tbl1 as $ascii => $htmlentitie ) {
        $trans_tbl2[$ascii] = '&#'.ord($ascii).';';
   }
   $trans_tbl1 = array_flip ($trans_tbl1);
   $trans_tbl2 = array_flip ($trans_tbl2);
   return strtr (strtr ($string, $trans_tbl1), $trans_tbl2);
}
?>