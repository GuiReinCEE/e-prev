<?php

class Formulario_peculio extends Controller
{

    function __construct()
    {
        parent::Controller();

        CheckLogin();

        $this->load->model('public/formulario_peculio_model');
    }

    function index($cd_empresa = -1, $cd_registro_empregado = 0, $seq_dependencia = 0)
    {
        if (gerencia_in(array('GAP')))
        {
            if(intval($cd_registro_empregado) > 0)
            {
                $result = null;
                $args = array();
                $data = array();

                $args['cd_empresa'] = intval($cd_empresa);
                $args['cd_registro_empregado'] = intval($cd_registro_empregado);
                $args['seq_dependencia'] = intval($seq_dependencia);
                $args['cd_usuario'] = 105;


                $this->formulario_peculio_model->participante($result, $args);
                $arr = $result->row_array();

                $this->formulario_peculio_model->get_assinatura($result, $args);
                $assinatura = $result->row_array();

                $this->load->plugin('fpdf');

                $ob_pdf = new PDF('P', 'mm', 'A4');
                $ob_pdf->SetNrPag(true);
                $ob_pdf->SetMargins(10, 14, 5);
                $ob_pdf->header_exibe = true;
                $ob_pdf->header_logo = true;
                $ob_pdf->header_titulo = false;

                $ob_pdf->AddPage();
                $ob_pdf->SetFont('Courier', '', 12);
                $ob_pdf->MultiCell(190, 20, "Porto Alegre, " . date('d/m/Y'), '0', 'R');
                $ob_pdf->MultiCell(0, 5, "Aos Familiares de ", '0', 'L');
                $ob_pdf->SetFont('Courier', 'B', 14);
                $ob_pdf->MultiCell(0, 7, $arr['nome'], '0', 'L');
                $ob_pdf->SetFont('Courier', '', 12);
                $ob_pdf->MultiCell(0, 5, "RE: " . $arr['cd_empresa'] . "/" . $arr['cd_registro_empregado'] . "/" . $arr['seq_dependencia'] . "
CPF: " . $arr['cpf_mf'] . "
" . trim($arr['endereco']) . ", " . $arr['nr_endereco'] . " / " . $arr['complemento_endereco'] . " - " . $arr['bairro'] . "
" . $arr['cidade'] . " - " . $arr['unidade_federativa'] . "
" . $arr['cep'], '0', 'L');

                $ob_pdf->MultiCell(0, 30, "Senhores,", '0', 'L');

                $ob_pdf->MultiCell(190, 5, "Com o recebimento da comunicaчуo do ѓbito do Sr(a) " . $arr['nome'] . ", informamos a existъncia do benefэcio de Pecњlio por Morte, devido р(s) pessoa(s) designada(s) pelo participante ou ao representante legal do espѓlio desse.

Considerando que o ѓbito ocorreu em " . $arr['dt_obito'] . " e atщ a presente data nуo houve manifestaчуo da(s) parte(s) interessada(s), solicitamos contatar com esta Fundaчуo CEEE, para obter mais informaчѕes sobre este benefэcio, atravщs do nosso teleatendimento - 0800 512596, ligaчуo gratuita.", '0', 'J');


                $ob_pdf->MultiCell(0, 60, "Att,", '0', 'L');

                list($width, $height) = getimagesize('./img/assinatura/' . $assinatura['assinatura']);
                $ob_pdf->Image('./img/assinatura/' . $assinatura['assinatura'], -22, $ob_pdf->GetY() - 30, $ob_pdf->ConvertSize($width / 2.5), $ob_pdf->ConvertSize($height / 2.5));

                $ob_pdf->MultiCell(0, 5, "Luiz Eduardo Motta,
Gerente de Atendimento;", '0', 'L');


                $ob_pdf->Output();
            }
            else
            {
                exibir_mensagem("PARTICIPANTE NУO INFORMADO");
            }
        }
        else
        {
            exibir_mensagem("ACESSO NУO PERMITIDO");
        }
    }

}

?>