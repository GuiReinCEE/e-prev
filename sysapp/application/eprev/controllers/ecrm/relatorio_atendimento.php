<?php
class Relatorio_atendimento extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    public function index()
    {
		if(gerencia_in(array('GRSC')))
		{
			$this->load->view('ecrm/relatorio_atendimento/index');
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }

    public function gerarPDF()
    {
		if(gerencia_in(array('GRSC')))
		{
			$this->load->model('projetos/relatorio_atendimento_model');
            $this->load->plugin('fpdf');

            $data['collection'] = array();
            $result = null;
            $args = array();
            $count = 0;
            $ds_tema = 'pastel';

            $args['dt_inicio'] = $this->input->post('dt_inicio', TRUE);
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

            #echo '################################################## CAPA ##################################################';
            $this->relatorio_atendimento_model->lista_capa( $result, $args );
            $ar_capa = $result->row_array();

            $this->relatorio_atendimento_model->lista_tempo_espera( $result, $args );
            $ar_tempo_espera = $result->row_array();

            $ob_pdf->AddPage();

            $ob_pdf->SetX(10);
            #$ob_pdf->Image('img/img_logo_fundacao_prev7.jpg', 45, 25, ConvertSize(440,$ob_pdf->pgwidth), ConvertSize(95,$ob_pdf->pgwidth),'','',false);

            $ob_pdf->SetY(100);
            $ob_pdf->SetFont('Courier','B',22);
            $ob_pdf->MultiCell(190, 4.5, "Relatório de Atendimentos",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY());
            $ob_pdf->MultiCell(190, 4, "____________________________",0,"C");

            $ob_pdf->SetFont('Courier','',14);
            $ob_pdf->SetY($ob_pdf->GetY() + 10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre ".$args['dt_inicio']." e ".$args['dt_fim'], 0, "C");
            $ob_pdf->SetY($ob_pdf->GetY() + 5);
            $ob_pdf->MultiCell(190, 4.5, "Número total de atendimento: ".$ar_capa['qt_atendimento'], 0, "C");
            $ob_pdf->SetY($ob_pdf->GetY() + 5);
            $ob_pdf->MultiCell(190, 4.5, "Média de tempo de atendimento: ".$ar_capa['hr_media'], 0, "C");
            $ob_pdf->SetY($ob_pdf->GetY() + 5);
            $ob_pdf->MultiCell(190, 4.5, "Média de tempo de espera na central: ".$ar_tempo_espera['hr_media'], 0, "C");
            $ob_pdf->SetY($ob_pdf->GetY() + 5);
            $ob_pdf->MultiCell(190, 4.5, "Desvio padrão de espera na central: ".$ar_tempo_espera['hr_desvio'], 0, "C");

            #$ob_pdf->SetFont('Courier','B',16);
            #$ob_pdf->SetY($ob_pdf->GetY() + 20);
            #$ob_pdf->MultiCell(190, 4.5, "Relatório de responsabilidade da", 0, "C");
            #$ob_pdf->SetY($ob_pdf->GetY() + 5);
            #$ob_pdf->MultiCell(190, 4.5, "Gerência de Atendimento ao Participante", 0, "C");

            #echo '################################################## ATENDENTE ##################################################';
            $this->relatorio_atendimento_model->lista_atendente( $result, $args );
            $collection = $result->result_array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Atendimento por atendente",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(50,20,20,20,20,20,20,20));
            $ob_pdf->SetAligns(array('L','C','C','C','C','C','C','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Nome do atendente", "Total", "Telefone", "Pessoal", "Email", "Telefone", "Pessoal", "Email"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico[$ar_reg['guerra']] = $ar_reg['qt_total'];
                #### LINHAS ####
                $ob_pdf->SetX(10);
                $ob_pdf->Row(array($ar_reg['guerra'],
                                   number_format($ar_reg['qt_total'],0,',','.'),
                                   number_format($ar_reg['qt_telefone'],0,',','.'),
                                   number_format($ar_reg['qt_pessoal'],0,',','.'),
                                   number_format($ar_reg['qt_email'],0,',','.'),
                                   $ar_reg['hr_media_telefone'],
                                   $ar_reg['hr_media_pessoal'],
                                   $ar_reg['hr_media_email']));

                $qt_telefone += $ar_reg['qt_telefone'];
                $qt_pessoal  += $ar_reg['qt_pessoal'];
                $qt_email    += $ar_reg['qt_email'];
                $qt_total    += $ar_reg['qt_total'];

                if(trim($ar_reg['hr_media_telefone']) != "")
                {
                    $ar_media_telefone[] = strtotime($ar_reg['hr_media_telefone']);
                }

                if(trim($ar_reg['hr_media_pessoal']) != "")
                {
                    $ar_media_pessoal[] = strtotime($ar_reg['hr_media_pessoal']);
                }

                if(trim($ar_reg['hr_media_email']) != "")
                {
                    $ar_media_email[] = strtotime($ar_reg['hr_media_email']);
                }
            }

            #### TOTALIZADOR ####
            $hr_media_telefone = 0;
            if((count($ar_media_telefone) > 0) and (array_sum($ar_media_telefone) > 0))
            {
                $hr_media_telefone = date("H:i:s", array_sum($ar_media_telefone)/count($ar_media_telefone));
            }

            $hr_media_pessoal = 0;
            if((count($ar_media_pessoal) > 0) and (array_sum($ar_media_pessoal) > 0))
            {
                $hr_media_pessoal = date("H:i:s", array_sum($ar_media_pessoal)/count($ar_media_pessoal));
            }

            $hr_media_email = 0;
            if((count($ar_media_email) > 0) and (array_sum($ar_media_email) > 0))
            {
                $hr_media_email = date("H:i:s", array_sum($ar_media_email)/count($ar_media_email));
            }

            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->SetX(10);
            $ob_pdf->Row(array("Total",
                               number_format($qt_total,0,',','.'),
                               number_format($qt_telefone,0,',','.'),
                               number_format($qt_pessoal,0,',','.'),
                               number_format($qt_email,0,',','.'),
                               $hr_media_telefone,
                               $hr_media_pessoal,
                               $hr_media_email));

            /*
            if(count($ar_grafico) > 0)
            {
                $ob_pdf->AddPage();
                $ob_pdf->SetMargins(10,14,5);
                $ob_pdf->SetMargins(10,14,5);
                $ob_pdf->SetXY(10,25);
                #### GRAFICO ####
                $ob_pdf->SetXY(50, $ob_pdf->GetY() + 20);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }
	        */

            #echo '################################################## HORÁRIO ##################################################';
            $this->relatorio_atendimento_model->lista_horario( $result, $args );
            $collection = $result->result_array();

            $qt_telefone = 0;
            $qt_pessoal  = 0;
            $qt_email    = 0;
            $qt_total    = 0;
            $ar_media_telefone = Array();
            $ar_media_pessoal  = Array();
            $ar_media_email    = Array();
            $ar_grafico = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Atendimento por horário",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            #### CABEÇALHO ####
            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(50,20,20,20,20,20,20,20));
            $ob_pdf->SetAligns(array('L','C','C','C','C','C','C','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Horário", "Total", "Telefone", "Pessoal", "Email", "Telefone", "Pessoal", "Email"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico[$ar_reg['hr_ini']] = $ar_reg['qt_total'];
                #### LINHAS ####
                $ob_pdf->SetX(10);
                $ob_pdf->Row(array($ar_reg['hr_ini'],
                                   number_format($ar_reg['qt_total'],0,',','.'),
                                   number_format($ar_reg['qt_telefone'],0,',','.'),
                                   number_format($ar_reg['qt_pessoal'],0,',','.'),
                                   number_format($ar_reg['qt_email'],0,',','.'),
                                   ($ar_reg['hr_media_telefone'] == "00:00:00" ? "" : $ar_reg['hr_media_telefone']),
                                   ($ar_reg['hr_media_pessoal'] == "00:00:00" ? "" : $ar_reg['hr_media_pessoal']),
                                   ($ar_reg['hr_media_email'] == "00:00:00" ? "" : $ar_reg['hr_media_email'])));

                $qt_telefone += $ar_reg['qt_telefone'];
                $qt_pessoal  += $ar_reg['qt_pessoal'];
                $qt_email    += $ar_reg['qt_email'];
                $qt_total    += $ar_reg['qt_total'];

                if(trim($ar_reg['hr_media_telefone']) != "00:00:00")
                {
                    $ar_media_telefone[] = strtotime($ar_reg['hr_media_telefone']);
                }

                if(trim($ar_reg['hr_media_pessoal']) != "00:00:00")
                {
                    $ar_media_pessoal[] = strtotime($ar_reg['hr_media_pessoal']);
                }

                if(trim($ar_reg['hr_media_email']) != "00:00:00")
                {
                    $ar_media_email[] = strtotime($ar_reg['hr_media_email']);
                }
            }

            $hr_media_telefone = 0;
            if((count($ar_media_telefone) > 0) and (array_sum($ar_media_telefone) > 0))
            {
                $hr_media_telefone = date("H:i:s", array_sum($ar_media_telefone)/count($ar_media_telefone));
            }

            $hr_media_pessoal = 0;
            if((count($ar_media_pessoal) > 0) and (array_sum($ar_media_pessoal) > 0))
            {
                $hr_media_pessoal = date("H:i:s", array_sum($ar_media_pessoal)/count($ar_media_pessoal));
            }

            $hr_media_email = 0;
            if((count($ar_media_email) > 0) and (array_sum($ar_media_email) > 0))
            {
                $hr_media_email = date("H:i:s", array_sum($ar_media_email)/count($ar_media_email));
            }

            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->SetX(10);
            $ob_pdf->Row(array("Total",
                               number_format($qt_total,0,',','.'),
                               number_format($qt_telefone,0,',','.'),
                               number_format($qt_pessoal,0,',','.'),
                               number_format($qt_email,0,',','.'),
                               $hr_media_telefone,
                               $hr_media_pessoal,
                               $hr_media_email));

            if(count($ar_grafico) > 0)
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(50, $ob_pdf->GetY() + 20);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }
 
            #echo '################################################## TIPO DE ATENDIMENTO ##################################################';
            $this->relatorio_atendimento_model->lista_tipo_atendimento( $result, $args );
            $collection = $result->result_array();

            $qt_avulso         = 0;
            $qt_normal         = 0;
            $qt_nao_partipante = 0;
            $ar_grafico = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Resultados por tipo de Atendimento",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(65,28,28,28,40));
            $ob_pdf->SetAligns(array('L','C','C','C','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Tipo de atendimento", "Total", "Avulso", "Normal", "Não Participante"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico[$ar_reg['ds_tipo_atendimento']] = $ar_reg['qt_avulso'] + $ar_reg['qt_normal'] + $ar_reg['qt_nao_partipante'];
                #### LINHAS ####
                $ob_pdf->SetX(10);
                $ob_pdf->Row(array($ar_reg['ds_tipo_atendimento'],
                                   number_format($ar_reg['qt_avulso'] + $ar_reg['qt_normal'] + $ar_reg['qt_nao_partipante'],0,',','.'),
                                   number_format($ar_reg['qt_avulso'],0,',','.'),
                                   number_format($ar_reg['qt_normal'],0,',','.'),
                                   number_format($ar_reg['qt_nao_partipante'],0,',','.')));

                $qt_avulso         += $ar_reg['qt_avulso'];
                $qt_normal         += $ar_reg['qt_normal'];
                $qt_nao_partipante += $ar_reg['qt_nao_partipante'];
            }

            #### TOTALIZADOR ####
            $ob_pdf->SetX(10);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array('Total',
                               number_format($qt_avulso + $qt_normal + $qt_nao_partipante,0,',','.'),
                               number_format($qt_avulso,0,',','.'),
                               number_format($qt_normal,0,',','.'),
                               number_format($qt_nao_partipante,0,',','.')));
            if(count($ar_grafico) > 0)
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(50, $ob_pdf->GetY() + 20);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }
            
            #echo '################################################## PROGRAMAS ##################################################';
            $this->relatorio_atendimento_model->lista_programas( $result, $args );
            $collection = $result->result_array();

            $qt_programa         = 0;
            $ar_media_tempo = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Resultados por Programa",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetXY(45, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(50,28,40));
            $ob_pdf->SetAligns(array('L','C','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Programa", "Total de acesso", "Tempo Médio por Atendimento"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico['TOTAL'][$ar_reg['tp_programa']] = $ar_reg['qt_programa'];

                if($ar_reg['tp_programa'] != "Cadastro")
                {
                    $ar_grafico['OUTROS'][$ar_reg['tp_programa']] = $ar_reg['qt_programa'];
                }

                #### LINHAS ####
                $ob_pdf->SetX(45);
                $ob_pdf->Row(array($ar_reg['tp_programa'],
                                   number_format($ar_reg['qt_programa'],0,',','.'),
                                   $ar_reg['qt_tempo']));

                $qt_programa         += $ar_reg['qt_programa'];
                if(trim($ar_reg['qt_tempo']) != "")
                {
                    $ar_media_tempo[] = strtotime($ar_reg['qt_tempo']);
                }

            }

            #### TOTALIZADOR ####
            $hr_media_tempo = 0;
            if((count($ar_media_tempo) > 0) and (array_sum($ar_media_tempo) > 0))
            {
                $hr_media_tempo = date("H:i:s", array_sum($ar_media_tempo)/count($ar_media_tempo));
            }
            $ob_pdf->SetX(45);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array('Total',
                               number_format($qt_programa ,0,',','.'),
                               $hr_media_tempo));

            $ob_pdf->SetFont('Courier','',10);

            if(count($ar_grafico) > 0 && isset($ar_grafico['TOTAL']))
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(10, $ob_pdf->GetY() + 10);
                $ob_pdf->MultiCell(190, 4.5, "Com cadastro:",0);
                $ob_pdf->SetXY(50, $ob_pdf->GetY() );
                $ob_pdf->PieChart(150, 60, $ar_grafico['TOTAL'], '%l (%p)', $AR_THEMA[$ds_tema]);

                #### GRAFICO ####
                $ob_pdf->SetXY(10, $ob_pdf->GetY() + 20);
                $ob_pdf->MultiCell(190, 4.5, "Sem cadastro:",0);
                $ob_pdf->SetXY(50, $ob_pdf->GetY());
                $ob_pdf->PieChart(150, 60, $ar_grafico['OUTROS'], '%l (%p)', $AR_THEMA[$ds_tema]);
            }

            #echo '################################################## PROGRAMA / TIPO ATENDIMENTO ##################################################';
            $this->relatorio_atendimento_model->lista_programa_tipo_atendimento( $result, $args );
            $collection = $result->result_array();

            $qt_telefone = 0;
            $qt_pessoal  = 0;
            $qt_email    = 0;
            $qt_total    = 0;
            $ar_media_telefone = Array();
            $ar_media_pessoal  = Array();
            $ar_media_email    = Array();
            $ar_grafico       = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Atendimento por Programa",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            #### CABEÇALHO ####
            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(50,20,20,20,20,20,20,20));
            $ob_pdf->SetAligns(array('L','C','C','C','C','C','C','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Horário", "Total", "Telefone", "Pessoal", "Email", "Telefone", "Pessoal", "Email"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico['TELEFONE'][$ar_reg['tp_programa']] = $ar_reg['qt_telefone'];
                $ar_grafico['PESSOAL'][$ar_reg['tp_programa']] = $ar_reg['qt_pessoal'];
                $ar_grafico['EMAIL'][$ar_reg['tp_programa']] = $ar_reg['qt_email'];

                #### LINHAS ####
                $ob_pdf->SetX(10);
                $ob_pdf->Row(array($ar_reg['tp_programa'],
                                   number_format($ar_reg['qt_total'],0,',','.'),
                                   number_format($ar_reg['qt_telefone'],0,',','.'),
                                   number_format($ar_reg['qt_pessoal'],0,',','.'),
                                   number_format($ar_reg['qt_email'],0,',','.'),
                                   ($ar_reg['hr_media_telefone'] == "00:00:00" ? "" : $ar_reg['hr_media_telefone']),
                                   ($ar_reg['hr_media_pessoal'] == "00:00:00" ? "" : $ar_reg['hr_media_pessoal']),
                                   ($ar_reg['hr_media_email'] == "00:00:00" ? "" : $ar_reg['hr_media_email'])));

                $qt_telefone += $ar_reg['qt_telefone'];
                $qt_pessoal  += $ar_reg['qt_pessoal'];
                $qt_email    += $ar_reg['qt_email'];
                $qt_total    += $ar_reg['qt_total'];
                if(trim($ar_reg['hr_media_telefone']) != "00:00:00")
                {
                    $ar_media_telefone[] = strtotime($ar_reg['hr_media_telefone']);
                }
                if(trim($ar_reg['hr_media_pessoal']) != "00:00:00")
                {
                    $ar_media_pessoal[] = strtotime($ar_reg['hr_media_pessoal']);
                }
                if(trim($ar_reg['hr_media_email']) != "00:00:00")
                {
                    $ar_media_email[] = strtotime($ar_reg['hr_media_email']);
                }
            }

            #### TOTALIZADOR ####
            $hr_media_telefone = 0;
            if((count($ar_media_telefone) > 0) and (array_sum($ar_media_telefone) > 0))
            {
                $hr_media_telefone = date("H:i:s", array_sum($ar_media_telefone)/count($ar_media_telefone));
            }

            $hr_media_pessoal = 0;
            if((count($ar_media_pessoal) > 0) and (array_sum($ar_media_pessoal) > 0))
            {
                $hr_media_pessoal = date("H:i:s", array_sum($ar_media_pessoal)/count($ar_media_pessoal));
            }

            $hr_media_email = 0;
            if((count($ar_media_email) > 0) and (array_sum($ar_media_email) > 0))
            {
                $hr_media_email = date("H:i:s", array_sum($ar_media_email)/count($ar_media_email));
            }

            $ob_pdf->SetX(10);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Total",
                               number_format($qt_total,0,',','.'),
                               number_format($qt_telefone,0,',','.'),
                               number_format($qt_pessoal,0,',','.'),
                               number_format($qt_email,0,',','.'),
                               $hr_media_telefone,
                               $hr_media_pessoal,
                               $hr_media_email));


            $ob_pdf->SetFont('Courier','',10);
            /*
            if(count($ar_grafico) > 0)
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(10, $ob_pdf->GetY() + 10);
                $ob_pdf->MultiCell(190, 4.5, "Telefone:",0);
                $ob_pdf->SetXY(50, $ob_pdf->GetY() );
                $ob_pdf->PieChart(150, 60, $ar_grafico['TELEFONE'], '%l (%p)', $AR_THEMA[$ds_tema]);

                if(
                    intval($ar_grafico['PESSOAL']['Cadastro']) > 0 OR 
                    intval($ar_grafico['PESSOAL']['Empréstimos']) > 0 OR 
                    intval($ar_grafico['PESSOAL']['Investimentos']) > 0 OR 
                    intval($ar_grafico['PESSOAL']['Previdenciário']) > 0 OR 
                    intval($ar_grafico['PESSOAL']['Seguros']) > 0
                )
                {
                    #### GRAFICO ####
                    $ob_pdf->SetXY(10, $ob_pdf->GetY() + 20);
                    $ob_pdf->MultiCell(190, 4.5, "Pessoal:",0);
                    $ob_pdf->SetXY(50, $ob_pdf->GetY());
                    $ob_pdf->PieChart(150, 60, $ar_grafico['PESSOAL'], '%l (%p)', $AR_THEMA[$ds_tema]);
                }

                if(
                    intval($ar_grafico['EMAIL']['Cadastro']) > 0 OR 
                    intval($ar_grafico['EMAIL']['Empréstimos']) > 0 OR 
                    intval($ar_grafico['EMAIL']['Investimentos']) > 0 OR 
                    intval($ar_grafico['EMAIL']['Previdenciário']) > 0 OR 
                    intval($ar_grafico['EMAIL']['Seguros']) > 0
                )
                {
                    #### GRAFICO ####
                    $ob_pdf->SetXY(15, $ob_pdf->GetY() + 20);
                    $ob_pdf->MultiCell(190, 4.5, "Email:",0);
                    $ob_pdf->SetXY(50, $ob_pdf->GetY());
                    $ob_pdf->PieChart(150, 60, $ar_grafico['EMAIL'], '%l (%p)', $AR_THEMA[$ds_tema]);
                }
            }
			*/
            #echo '################################################## EMPRESAS/PLANOS ##################################################';
            #exit;
            $this->relatorio_atendimento_model->lista_empresa_planos( $result, $args );
            $collection = $result->result_array();

            $qt_total = 0;
            $ar_grafico = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Resultados por Empresas/Planos",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetXY(40, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(52,50,28));
            $ob_pdf->SetAligns(array('L','L', 'R'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Empresa", "Plano", "Total"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico[$ar_reg['sigla'] . '-' . $ar_reg['descricao'] ] = $ar_reg['total'];
                #### LINHAS ####
                $ob_pdf->SetX(40);
                $ob_pdf->Row(array($ar_reg['sigla'], $ar_reg['descricao'],
                                   number_format($ar_reg['total'],0,',','.')));

                $qt_total         += $ar_reg['total'];
            }
            #### TOTALIZADOR ####
            $ob_pdf->SetX(40);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array('', 'Total',$qt_total));
            
          /*
            if(count($ar_grafico) > 0)
            {
                $ob_pdf->AddPage();
                $ob_pdf->SetMargins(10,14,5);
                $ob_pdf->SetXY(10,25);
                $ob_pdf->SetFont('Courier','B',16);
                $ob_pdf->MultiCell(190, 4.5, "Resultados por Empresas/Planos",0,"C");
                $ob_pdf->SetY($ob_pdf->GetY() + 2);
                $ob_pdf->SetFont('Courier','',10);
                $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
                $ob_pdf->SetY($ob_pdf->GetY() + 4);
          
                #### GRAFICO ####
                $ob_pdf->SetXY(30, $ob_pdf->GetY() + 70);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }
            */

			#echo '################################################## EMPRESA - PLANO / TIPO ATENDIMENTO ##################################################';
            #exit;
            $this->relatorio_atendimento_model->lista_empresa_planos_tipo_atendimento( $result, $args );
            $collection = $result->result_array();

            $qt_telefone = 0;
            $qt_pessoal  = 0;
            $qt_email    = 0;
            $qt_total    = 0;
            $ar_media_telefone = Array();
            $ar_media_pessoal  = Array();
            $ar_media_email    = Array();
            $ar_grafico       = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Atendimento por Empresa/Plano",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            #### CABEÇALHO ####
            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(50,20,20,20,20,20,20,20));
            $ob_pdf->SetAligns(array('L','C','C','C','C','C','C','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Horário", "Total", "Telefone", "Pessoal", "Email", "Telefone", "Pessoal", "Email"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico['TELEFONE'][$ar_reg['emp_plano']] = $ar_reg['qt_telefone'];
                $ar_grafico['PESSOAL'][$ar_reg['emp_plano']] = $ar_reg['qt_pessoal'];
                $ar_grafico['EMAIL'][$ar_reg['emp_plano']] = $ar_reg['qt_email'];

                #### LINHAS ####
                $ob_pdf->SetX(10);
                $ob_pdf->Row(array($ar_reg['emp_plano'],
                                   number_format($ar_reg['qt_total'],0,',','.'),
                                   number_format($ar_reg['qt_telefone'],0,',','.'),
                                   number_format($ar_reg['qt_pessoal'],0,',','.'),
                                   number_format($ar_reg['qt_email'],0,',','.'),
                                   ($ar_reg['hr_media_telefone'] == "00:00:00" ? "" : $ar_reg['hr_media_telefone']),
                                   ($ar_reg['hr_media_pessoal'] == "00:00:00" ? "" : $ar_reg['hr_media_pessoal']),
                                   ($ar_reg['hr_media_email'] == "00:00:00" ? "" : $ar_reg['hr_media_email'])));

                $qt_telefone += $ar_reg['qt_telefone'];
                $qt_pessoal  += $ar_reg['qt_pessoal'];
                $qt_email    += $ar_reg['qt_email'];
                $qt_total    += $ar_reg['qt_total'];
                if(trim($ar_reg['hr_media_telefone']) != "00:00:00")
                {
                    $ar_media_telefone[] = strtotime($ar_reg['hr_media_telefone']);
                }
                if(trim($ar_reg['hr_media_pessoal']) != "00:00:00")
                {
                    $ar_media_pessoal[] = strtotime($ar_reg['hr_media_pessoal']);
                }
                if(trim($ar_reg['hr_media_email']) != "00:00:00")
                {
                    $ar_media_email[] = strtotime($ar_reg['hr_media_email']);
                }
            }

            #### TOTALIZADOR ####
            $hr_media_telefone = 0;
            if((count($ar_media_telefone) > 0) and (array_sum($ar_media_telefone) > 0))
            {
                $hr_media_telefone = date("H:i:s", array_sum($ar_media_telefone)/count($ar_media_telefone));
            }

            $hr_media_pessoal = 0;
            if((count($ar_media_pessoal) > 0) and (array_sum($ar_media_pessoal) > 0))
            {
                $hr_media_pessoal = date("H:i:s", array_sum($ar_media_pessoal)/count($ar_media_pessoal));
            }

            $hr_media_email = 0;
            if((count($ar_media_email) > 0) and (array_sum($ar_media_email) > 0))
            {
                $hr_media_email = date("H:i:s", array_sum($ar_media_email)/count($ar_media_email));
            }

            $ob_pdf->SetX(10);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Total",
                               number_format($qt_total,0,',','.'),
                               number_format($qt_telefone,0,',','.'),
                               number_format($qt_pessoal,0,',','.'),
                               number_format($qt_email,0,',','.'),
                               $hr_media_telefone,
                               $hr_media_pessoal,
                               $hr_media_email));
 
            /*
            if(count($ar_grafico) > 0)
            {
				$ob_pdf->AddPage();
				$ob_pdf->SetMargins(10,14,5);
				$ob_pdf->SetXY(10,25);
				$ob_pdf->SetFont('Courier','B',16);
				$ob_pdf->MultiCell(190, 4.5, "Atendimento por Empresa/Plano",0,"C");
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('Courier','',10);
				$ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
				$ob_pdf->SetY($ob_pdf->GetY() + 4);							   
				$ob_pdf->SetFont('Courier','',10);				
				
                #### GRAFICO ####
				$ob_pdf->SetFont('Courier','B',18);
				$ob_pdf->MultiCell(190, 4.5, "TELEFONE",0,"C");				
                $ob_pdf->SetXY(10, $ob_pdf->GetY() + 70);
				$ob_pdf->SetFont('Courier','',10);
                $ob_pdf->PieChart(150, 60, $ar_grafico['TELEFONE'], '%l (%p)', $AR_THEMA[$ds_tema]);

				$ob_pdf->AddPage();
				$ob_pdf->SetMargins(10,14,5);
				$ob_pdf->SetXY(10,25);
				$ob_pdf->SetFont('Courier','B',16);
				$ob_pdf->MultiCell(190, 4.5, "Atendimento por Empresa/Plano",0,"C");
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('Courier','',10);
				$ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
				$ob_pdf->SetY($ob_pdf->GetY() + 4);						   
				$ob_pdf->SetFont('Courier','',10);					
				
                #### GRAFICO ####
				$ob_pdf->SetFont('Courier','B',18);
				$ob_pdf->MultiCell(190, 4.5, "PESSOAL",0,"C");				
                $ob_pdf->SetXY(10, $ob_pdf->GetY() + 70);
				$ob_pdf->SetFont('Courier','',10);
                $ob_pdf->PieChart(150, 60, $ar_grafico['PESSOAL'], '%l (%p)', $AR_THEMA[$ds_tema]);

				
				$ob_pdf->AddPage();
				$ob_pdf->SetMargins(10,14,5);
				$ob_pdf->SetXY(10,25);
				$ob_pdf->SetFont('Courier','B',16);
				$ob_pdf->MultiCell(190, 4.5, "Atendimento por Empresa/Plano",0,"C");
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetFont('Courier','',10);
				$ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
				$ob_pdf->SetY($ob_pdf->GetY() + 4);						   
				$ob_pdf->SetFont('Courier','',10);				
				
              
                if(array_sum(array_values($ar_grafico['EMAIL'])) > 0)
                {
                    #### GRAFICO ####
    				$ob_pdf->SetFont('Courier','B',18);
    				$ob_pdf->MultiCell(190, 4.5, "EMAIL",0,"C");				
                    $ob_pdf->SetXY(10, $ob_pdf->GetY() + 70);
    				$ob_pdf->SetFont('Courier','',10);
                    $ob_pdf->PieChart(150, 60, $ar_grafico['EMAIL'], '%l (%p)', $AR_THEMA[$ds_tema]);
                }
                
            }			
			*/
      
			#echo '################################################## EMAIL RT ##################################################';
            $this->relatorio_atendimento_model->lista_email_rt( $result, $args );
            $collection = $result->result_array();

            $qt_total = 0;
            $ar_grafico = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "E-mails RT",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetXY(40, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(52,50));
            $ob_pdf->SetAligns(array('L','R'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Situação", "Quantidade"));
            $ob_pdf->SetFont('Courier','',10);
			
            foreach($collection as $ar_reg)
            {
                $ar_grafico[$ar_reg['situacao']] = $ar_reg['qt_email'];
                #### LINHAS ####
                $ob_pdf->SetX(40);
                $ob_pdf->Row(array($ar_reg['situacao'], number_format($ar_reg['qt_email'],0,',','.')));

                $qt_total += $ar_reg['qt_email'];
            }
            #### TOTALIZADOR ####
            $ob_pdf->SetX(40);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array('Total',$qt_total));

            if(count($ar_grafico) > 0)
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(30, $ob_pdf->GetY() + 30);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }			
			
			#echo '################################################## EMAIL RT - TEMPO DE RESPOSTA ##################################################';
            $this->relatorio_atendimento_model->lista_email_tempo_resposta_rt($result, $args);
            $collection = $result->result_array();

            $qt_total = 0;
			$hr_real  = 0;
			$hr_util  = 0;
            $ar_grafico = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "E-mails RT - Tempo de Resposta em horas",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetXY(50, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(30,25,25,25));
            $ob_pdf->SetAligns(array('C','C','C','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Dt Resposta", "Quantidade", "Tempo Real", "Tempo Útil"));
            $ob_pdf->SetFont('Courier','',10);
			$ob_pdf->SetAligns(array('C','R','R','R'));
            foreach($collection as $ar_reg)
            {
                $v1 = 0;
                $v2 = 0;
                
                if(intval($ar_reg['qt_email']) > 0)
                {
                    $v1 = (floatval($ar_reg['hr_real']) / intval($ar_reg['qt_email']));
                    $v2 = (floatval($ar_reg['hr_util']) / intval($ar_reg['qt_email']));
                }
                #### LINHAS ####
                $ob_pdf->SetX(50);
                $ob_pdf->Row(
								array(
									$ar_reg['dt_referencia'], 
									number_format(intval($ar_reg['qt_email']),0,',','.'),
									number_format($v1,2,',','.'),
									number_format($v2,2,',','.')
								)
							);

                $qt_total += intval($ar_reg['qt_email']);
                $hr_real  += floatval($ar_reg['hr_real']);
                $hr_util  += floatval($ar_reg['hr_util']);
            }
            #### TOTALIZADOR ####
            $t1 = 0;
            $t2 = 0;
            if(intval($qt_total) > 0)
            {
               $t1 = floatval($hr_real) / intval($qt_total);
               $t2 = floatval($hr_util) / intval($qt_total);
            }

            $ob_pdf->SetX(50);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(
							array(
									'Total',
									number_format(intval($qt_total),0,',','.'),
									number_format($t1,2,',','.'),
									number_format($t2,2,',','.')									
								 )
						);
						
            $ob_pdf->SetY($ob_pdf->GetY() + 6);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Observação: A quantidade compreende Resolvidos e Rejeitados", 0, 'C');	


            #echo '################################################## EMAIL RT INTERNO ##################################################';
            $this->relatorio_atendimento_model->lista_email_rt_interno( $result, $args );
            $collection = $result->result_array();

            $qt_total = 0;
            $ar_grafico = Array();

            $ob_pdf->AddPage();
            $ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "E-mails RT - Interno",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            $ob_pdf->SetXY(40, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(52,50));
            $ob_pdf->SetAligns(array('L','R'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Situação", "Quantidade"));
            $ob_pdf->SetFont('Courier','',10);
            
            foreach($collection as $ar_reg)
            {
                $ar_grafico[$ar_reg['situacao']] = $ar_reg['qt_email'];
                #### LINHAS ####
                $ob_pdf->SetX(40);
                $ob_pdf->Row(array($ar_reg['situacao'], number_format($ar_reg['qt_email'],0,',','.')));

                $qt_total += $ar_reg['qt_email'];
            }
            #### TOTALIZADOR ####
            $ob_pdf->SetX(40);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array('Total',$qt_total));

            if(count($ar_grafico) > 0)
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(30, $ob_pdf->GetY() + 30);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }           					
			
            #echo '################################################## TIPO PARTICIPANTE ##################################################';
            $this->relatorio_atendimento_model->lista_tipo_participante( $result, $args );
            $collection = $result->result_array();

            $qt_total = 0;
            $ar_grafico = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Atendimentos por Tipo Participante",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
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
                $ob_pdf->Row(array($ar_reg['tipo'],
                                   number_format($ar_reg['qt_total'],0,',','.')));

                $qt_total         += $ar_reg['qt_total'];
            }
            #### TOTALIZADOR ####
            $ob_pdf->SetX(65);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array('Total',
                               $qt_total));

            if(count($ar_grafico) > 0)
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(50, $ob_pdf->GetY() + 5);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }
			
            #echo '################################################## RECLAMAÇÕES ##################################################';
            $this->relatorio_atendimento_model->lista_reclamacoes( $result, $args );
            $collection = $result->result_array();

            $qt_total = 0;
            $ar_grafico = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Reclamações",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            #### CABEÇALHO ####
            $ob_pdf->SetXY(65, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(52,28));
            $ob_pdf->SetAligns(array('L','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Programa", "Total"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico[$ar_reg['ds_programa']] = $ar_reg['qt_total'];
                #### LINHAS ####
                $ob_pdf->SetX(65);
                $ob_pdf->Row(array($ar_reg['ds_programa'],
                                   number_format($ar_reg['qt_total'],0,',','.')));

                $qt_total         += $ar_reg['qt_total'];
            }
            #### TOTALIZADOR ####
            $ob_pdf->SetX(65);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array('Total',
                               $qt_total));

            if(count($ar_grafico) > 0)
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(50, $ob_pdf->GetY() + 5);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }

            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 30);

            $this->relatorio_atendimento_model->lista_reclamacoes_lista( $result, $args );
            $collection = $result->result_array();

            $ds_programa = "";

            foreach($collection as $ar_reg)
            {
                $ob_pdf->SetLineWidth(0);
                $ob_pdf->SetDrawColor(0,0,0);
                $ob_pdf->SetWidths(array(28,28,106,28));
                $ob_pdf->SetAligns(array('L','C','J','C'));

                if($ds_programa != $ar_reg['ds_programa'])
                {
                    #### TITULO ####
                    $ob_pdf->SetFont('Courier','B',12);
                    $ob_pdf->SetXY(10, $ob_pdf->GetY() + 2);
                    $ob_pdf->MultiCell(190, 4.5, $ar_reg['ds_programa'],0,"C");
                    #### CABEÇALHO ####
                    $ob_pdf->SetFont('Courier','B',10);
                    $ob_pdf->SetX(10);
                    $ob_pdf->Row(array("Atendimento", "Participante", "Reclamação", "Retorno"));
                    $ds_programa = $ar_reg['ds_programa'];
                }

                #### LINHAS ####
                $ob_pdf->SetX(10);
                $ob_pdf->SetLineWidth(0);
                $ob_pdf->SetDrawColor(0,0,0);
                $ob_pdf->SetFont('Courier','',10);
                $ob_pdf->Row(array($ar_reg['cd_atendimento'],
                                   $ar_reg['cd_empresa']."/".$ar_reg['cd_registro_empregado']."/".$ar_reg['seq_dependencia'],
                                   $ar_reg['obs'],
                                   $ar_reg['dt_retorno']));
            }

            #echo '################################################## SUGESTOES ##################################################';
            $this->relatorio_atendimento_model->lista_sugestoes( $result, $args );
            $collection = $result->result_array();

            $qt_total = 0;
            $ar_grafico = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Sugestões",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            #### CABEÇALHO ####
            $ob_pdf->SetXY(65, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(52,28));
            $ob_pdf->SetAligns(array('L','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Programa", "Total"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico[$ar_reg['ds_programa']] = $ar_reg['qt_total'];
                #### LINHAS ####
                $ob_pdf->SetX(65);
                $ob_pdf->Row(array($ar_reg['ds_programa'],
                                   number_format($ar_reg['qt_total'],0,',','.')));

                $qt_total         += $ar_reg['qt_total'];
            }
            #### TOTALIZADOR ####
            $ob_pdf->SetX(65);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array('Total',
                               $qt_total));

            if(count($ar_grafico) > 0)
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(50, $ob_pdf->GetY() + 5);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }

            $this->relatorio_atendimento_model->lista_sugestoes_lista( $result, $args );
            $collection = $result->result_array();

            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 30);
            $ds_programa = "";

            foreach($collection as $ar_reg)
            {
                $ob_pdf->SetLineWidth(0);
                $ob_pdf->SetDrawColor(0,0,0);
                $ob_pdf->SetWidths(array(28,28,96,28));
                $ob_pdf->SetAligns(array('L','C','J','C'));

                if($ds_programa != $ar_reg['ds_programa'])
                {
                    #### TITULO ####
                    $ob_pdf->SetFont('Courier','B',12);
                    $ob_pdf->SetXY(10, $ob_pdf->GetY() + 2);
                    $ob_pdf->MultiCell(190, 4.5, $ar_reg['ds_programa'],0,"C");
                    #### CABEÇALHO ####
                    $ob_pdf->SetFont('Courier','B',10);
                    $ob_pdf->SetX(10);
                    $ob_pdf->Row(array("Atendimento", "Participante", "Sugestão", "Retorno"));
                    $ds_programa = $ar_reg['ds_programa'];
                }

                #### LINHAS ####
                $ob_pdf->SetX(10);
                $ob_pdf->SetLineWidth(0);
                $ob_pdf->SetDrawColor(0,0,0);
                $ob_pdf->SetFont('Courier','',10);
                $ob_pdf->Row(array($ar_reg['cd_atendimento'],
                                   $ar_reg['cd_empresa']."/".$ar_reg['cd_registro_empregado']."/".$ar_reg['seq_dependencia'],
                                   $ar_reg['obs'],
                                   $ar_reg['dt_retorno']));
            }

            #echo '################################################## ENCAMINHAMENTOS - EMAIL RT INTERNO ##################################################';
            $this->relatorio_atendimento_model->lista_email_rt_interno_tickets( $result, $args );
            $collection = $result->result_array();

            $ob_pdf->AddPage();
            $ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Encaminhamentos - E-mails RT - Interno",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            #### TITULO ####
            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->MultiCell(190, 4.5, "Total de encaminhamentos: ".count($collection),0,"C");


            #### CABEÇALHO ####
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(13,22,20,80, 40, 15));
            $ob_pdf->SetAligns(array('C','C','C','J', 'J', 'L'));
            $ob_pdf->SetFont('Courier','B',8);
            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 5);
            $ob_pdf->Row(array("Cód", "Dt. Cad", "Situação", "Assunto", "Fila", "Usuário"));

            foreach($collection as $ar_reg)
            {
                #### LINHAS ####
                $ob_pdf->SetX(10);
                $ob_pdf->SetLineWidth(0);
                $ob_pdf->SetDrawColor(0,0,0);
                $ob_pdf->SetFont('Courier','',8);
                $ob_pdf->Row(array($ar_reg['codigo'],
                                   $ar_reg['dt_cadastro'],
                                   $ar_reg['situacao'],
                                   utf8_decode($ar_reg['assunto']),
                                   utf8_decode($ar_reg['fila']),
                                   $ar_reg['usuario']));
            }

            #echo '################################################## ENCAMINHAMENTOS ##################################################';
            $this->relatorio_atendimento_model->lista_encaminhamentos( $result, $args );
            $collection = $result->result_array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Encaminhamentos",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            #### TITULO ####
            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->MultiCell(190, 4.5, "Total de encaminhamentos: ".count($collection),0,"C");


            #### CABEÇALHO ####
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(28,28,134));
            $ob_pdf->SetAligns(array('L','C','J'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 5);
            $ob_pdf->Row(array("Atendimento", "Participante", "Encaminhamento"));

            foreach($collection as $ar_reg)
            {
                #### LINHAS ####
                $ob_pdf->SetX(10);
                $ob_pdf->SetLineWidth(0);
                $ob_pdf->SetDrawColor(0,0,0);
                $ob_pdf->SetFont('Courier','',10);
                $ob_pdf->Row(array($ar_reg['cd_atendimento'],
                                   $ar_reg['cd_empresa']."/".$ar_reg['cd_registro_empregado']."/".$ar_reg['seq_dependencia'],
                                   $ar_reg['obs']));
            }

            $this->relatorio_atendimento_model->lista_encaminhamentos_gerencias( $result, $args );
            $collection = $result->result_array();

            $qt_total         = 0;
            $ar_grafico       = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Encaminhamentos para Gerências",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);

            #### CABEÇALHO ####
            $ob_pdf->SetXY(65, $ob_pdf->GetY() + 5);
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(52,28));
            $ob_pdf->SetAligns(array('L','C'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array("Gerência", "Total"));
            $ob_pdf->SetFont('Courier','',10);

            foreach($collection as $ar_reg)
            {
                $ar_grafico[$ar_reg['area']] = $ar_reg['qt_area'];
                #### LINHAS ####
                $ob_pdf->SetX(65);
                $ob_pdf->Row(array($ar_reg['area'],
                                   number_format($ar_reg['qt_area'],0,',','.')));

                $qt_total         += $ar_reg['qt_area'];
            }
            #### TOTALIZADOR ####
            $ob_pdf->SetX(65);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->Row(array('Total',
                               $qt_total));

            if(count($ar_grafico) > 0)
            {
                #### GRAFICO ####
                $ob_pdf->SetXY(50, $ob_pdf->GetY() + 5);
                $ob_pdf->PieChart(150, 60, $ar_grafico, '%l (%p)', $AR_THEMA[$ds_tema]);
            }

            #echo '################################################## ENCAMINHAMENTOS PARA GERENCIAS ##################################################';
            $this->relatorio_atendimento_model->lista_encaminhamentos_gerencias_listar( $result, $args );
            $collection = $result->result_array();
            
            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 25);

            #### CABEÇALHO ####
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(28,28,20,114));
            $ob_pdf->SetAligns(array('L','C','C','J'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 5);
            $ob_pdf->Row(array("Atividade", "Participante", "Gerência", "Encaminhamento"));

            foreach($collection as $ar_reg)
            {
                #### LINHAS ####
                $ob_pdf->SetX(10);
                $ob_pdf->SetLineWidth(0);
                $ob_pdf->SetDrawColor(0,0,0);
                $ob_pdf->SetFont('Courier','',10);
                $ob_pdf->Row(array($ar_reg['numero'],
                                   $ar_reg['cd_empresa']."/".$ar_reg['cd_registro_empregado']."/".$ar_reg['cd_sequencia'],
                                   $ar_reg['area'],
                                   $ar_reg['descricao']));
            }

            #echo '################################################## RETORNO ATENDIMENTOS ##################################################';
            $this->relatorio_atendimento_model->lista_atendimento_retorno_listar( $result, $args );
            $collection = $result->result_array();
			
            $qt_total         = 0;
            $ar_grafico       = Array();

            $ob_pdf->AddPage();
			$ob_pdf->SetMargins(10,14,5);
            $ob_pdf->SetXY(10,25);
            $ob_pdf->SetFont('Courier','B',16);
            $ob_pdf->MultiCell(190, 4.5, "Retornos de Atendimentos",0,"C");
            $ob_pdf->SetY($ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','',10);
            $ob_pdf->MultiCell(190, 4.5, "Período entre: ". $args['dt_inicio']. " e ".  $args['dt_fim'], 0, 'C');
            $ob_pdf->SetY($ob_pdf->GetY() + 4);
			
            #### TITULO ####
            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 2);
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->MultiCell(190, 4.5, "Total de retornos: ".count($collection),0,"C");			
			
            #### CABEÇALHO ####
            $ob_pdf->SetLineWidth(0);
            $ob_pdf->SetDrawColor(0,0,0);
            $ob_pdf->SetWidths(array(28,28,134));
            $ob_pdf->SetAligns(array('L','C','J'));
            $ob_pdf->SetFont('Courier','B',10);
            $ob_pdf->SetXY(10, $ob_pdf->GetY() + 5);
            $ob_pdf->Row(array("Atendimento", "Participante", "Retorno"));

            foreach($collection as $ar_reg)
            {
                #### LINHAS ####
                $ob_pdf->SetX(10);
                $ob_pdf->SetLineWidth(0);
                $ob_pdf->SetDrawColor(0,0,0);
                $ob_pdf->SetFont('Courier','',10);
                $ob_pdf->Row(array($ar_reg['cd_atendimento'],
                                   $ar_reg['cd_empresa']."/".$ar_reg['cd_registro_empregado']."/".$ar_reg['seq_dependencia'],
                                   $ar_reg['retorno']));
            }			
			
            
			#### GERA PDF ####
			$ob_pdf->Output();
		}
		else
		{
			exibir_mensagem('ACESSO NÃO PERMITIDO');
		}
    }
}
?>