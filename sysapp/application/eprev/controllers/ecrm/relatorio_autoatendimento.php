<?php
class relatorio_autoatendimento extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

		if(gerencia_in(array('GAP')))
		{
			$this->load->view('ecrm/relatorio_autoatendimento/index.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }

    function gerarPDF()
    {
		CheckLogin();

		if(gerencia_in(array('GAP')))
		{
			$this->load->model('projetos/relatorio_autoatendimento_model');
            $this->load->plugin('fpdf');

            $data['collection'] = array();
            $result = null;
            $args = array();
            $count = 0;
            $ds_tema = 'pastel';

            $args['dt_ini'] = $this->input->post('dt_ini', TRUE);
            $args['dt_fim'] = $this->input->post('dt_fim', TRUE);

            $qt_telefone = 0;
            $qt_pessoal  = 0;
            $qt_email    = 0;
            $qt_total    = 0;
            $ar_media_telefone = Array();
            $ar_media_pessoal  = Array();
            $ar_media_email    = Array();
            $ar_grafico = Array();

            $ob_pdf = new PDF();

            $AR_THEMA = $ob_pdf->getTema();

            $ob_pdf->SetNrPag(true);
            $ob_pdf->SetMargins(10,14,5);
            $ob_pdf->header_exibe = true;
            $ob_pdf->header_logo = true;
            $ob_pdf->header_titulo = true;
            $ob_pdf->header_titulo_texto = "";

            ################################################## CAPA ##################################################
            $ob_pdf->AddPage();
            $ob_pdf->SetX(10);

            $ob_pdf->SetY(100);
            $ob_pdf->SetFont('Courier','B',22);
            $ob_pdf->MultiCell(190, 4.5, "Relatório do Autoatendimento",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY());
            $ob_pdf->MultiCell(190, 4, "____________________________",0,"C");

            $ob_pdf->SetFont('Courier','',14);
            $ob_pdf->SetY($ob_pdf->GetY() + 10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre ".$args['dt_ini']." e ".$args['dt_fim'], 0, "C");

            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->SetY($ob_pdf->GetY() + 20);
            $ob_pdf->MultiCell(190, 4.5, "Relatório de responsabilidade da", 0, "C");
            $ob_pdf->SetY($ob_pdf->GetY() + 5);
            $ob_pdf->MultiCell(190, 4.5, "Gerência de Atendimento ao Participante", 0, "C");

	        
			################################################## PATROCINADORA/INSTITUIDOR ##################################################
            $this->relatorio_autoatendimento_model->empresa( $result, $args );
            $collection = $result->result_array();

            $qt_total = 0;
            $ar_grafico = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Patrocinadora/Instituidor",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_ini']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            #### CABEÇALHO ####
            $ob_pdf->SetXY(55, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(70,28));
            $ob_pdf->SetAligns(array('L','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Patrocinadora/Instituidor", "Total"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico[$ar_reg['empresa']] = $ar_reg['qt_total'];
                #### LINHAS ####
                $ob_pdf->SetX(55);
                $ob_pdf->Row(array($ar_reg['empresa'],number_format($ar_reg['qt_total'],0,',','.')));
                $qt_total+= $ar_reg['qt_total'];
            }
            #### TOTALIZADOR ####
            $ob_pdf->SetX(55);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array('Total',
                               $qt_total));

            if(count($ar_grafico) > 0)
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(50, $ob_pdf->GetY() + 10);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }
			
            ################################################## TIPO PARTICIPANTE ##################################################
            $this->relatorio_autoatendimento_model->tipo_participante( $result, $args );
            $collection = $result->result_array();

            $qt_total = 0;
            $ar_grafico = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Tipo Participante",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_ini']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            #### CABEÇALHO ####
            $ob_pdf->SetXY(65, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(52,28));
            $ob_pdf->SetAligns(array('L','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Tipo", "Total"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico[$ar_reg['tipo']] = $ar_reg['qt_total'];
                #### LINHAS ####
                $ob_pdf->SetX(65);
                $ob_pdf->Row(array($ar_reg['tipo'],number_format($ar_reg['qt_total'],0,',','.')));
                $qt_total+= $ar_reg['qt_total'];
            }
            #### TOTALIZADOR ####
            $ob_pdf->SetX(65);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array('Total',
                               $qt_total));

            if(count($ar_grafico) > 0)
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(50, $ob_pdf->GetY() + 10);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }
			
            ################################################## TIPO SENHA PARTICIPANTE ##################################################
            $this->relatorio_autoatendimento_model->tipo_senha_participante( $result, $args );
            $collection = $result->result_array();

            $qt_total = 0;
            $ar_grafico = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Tipo Senha Participante",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_ini']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            #### CABEÇALHO ####
            $ob_pdf->SetXY(65, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(52,28));
            $ob_pdf->SetAligns(array('L','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Tipo", "Total"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico[$ar_reg['tipo']] = $ar_reg['qt_total'];
                #### LINHAS ####
                $ob_pdf->SetX(65);
                $ob_pdf->Row(array($ar_reg['tipo'],number_format($ar_reg['qt_total'],0,',','.')));
                $qt_total+= $ar_reg['qt_total'];
            }
            #### TOTALIZADOR ####
            $ob_pdf->SetX(65);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array('Total',
                               $qt_total));

            if(count($ar_grafico) > 0)
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(50, $ob_pdf->GetY() + 10);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }			
			
			#### GERA PDF ####
			$ob_pdf->Output();
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }
}
?>