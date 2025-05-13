<?php
class Certificado_participante extends Controller
{

    function __construct()
    {
        parent::Controller();
    }

    private function get_permissao()
    {
        #Vanessa dos Santos Dornelles
        if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Shaiane de Oliveira Tavares SantAnna
        else if($this->session->userdata('codigo') == 228)
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        #Cristiano Jacobsen
        else if($this->session->userdata('codigo') == 170)
        {
            return TRUE;
        }		
        #Julia Graciely Goncalves dos Santos
        else if($this->session->userdata('codigo') == 384)
        {
            return TRUE;
        }
        #Nalu Cristina Ribeiro das Neves
        else if($this->session->userdata('codigo') == 75)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    function index()
    {
        CheckLogin();
        if($this->get_permissao())
        {
            $args = Array();
            $data = Array();
			
			$this->load->model('projetos/Certificado_participante_model');
			
			$args['cd_usuario_cadastro'] = intval(usuario_id());
                   
			$this->Certificado_participante_model->limpa_tmp($result, $args);
			
            $this->load->view('ecrm/certificado_participante/index.php', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function certificadoLista()
    {
        CheckLogin();
       
        $this->load->model('projetos/Certificado_participante_model');

        $args = Array();
        $data = Array();
        $result = null;

        $args["dt_inicial"] = $this->input->post("dt_inicial", TRUE);
        $args["dt_final"] = $this->input->post("dt_final", TRUE);
        $args["cd_plano"] = $this->input->post("cd_plano", TRUE);

        $args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
        $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
        $args["cd_plano"] = $this->input->post("cd_plano", TRUE);

        $args["cd_empresa"] = (trim($this->input->post("cd_empresa_part", TRUE)) != "" ? trim($this->input->post("cd_empresa_part", TRUE)) : $args["cd_empresa"]);
        $args["cd_registro_empregado"] = (trim($this->input->post("cd_registro_empregado_part", TRUE)) != "" ? trim($this->input->post("cd_registro_empregado_part", TRUE)) : "");
        $args["seq_dependencia"] = (trim($this->input->post("seq_dependencia_part", TRUE)) != "" ? trim($this->input->post("seq_dependencia_part", TRUE)) : "");

        #manter_filtros($args);	

        $this->Certificado_participante_model->certificadoLista($result, $args);
        $data['ar_lista'] = $result->result_array();

        $this->load->view('ecrm/certificado_participante/index_result', $data);
        
    }

    function certificado($tipo = "")
    {
        CheckLogin();
        if($this->get_permissao())
        {
            $this->load->model('projetos/Certificado_participante_model');
            $args = Array();
            $data = Array();
            $result = null;

            $args["dt_inicial"] = $this->input->post("dt_inicial", TRUE);
            $args["dt_final"] = $this->input->post("dt_final", TRUE);
            $args["cd_plano_empresa"] = $this->input->post("cd_plano_empresa", TRUE); ## para manter o filtro ##
            $args["cd_empresa"] = $this->input->post("cd_plano_empresa", TRUE);
            $args["cd_plano"] = $this->input->post("cd_plano", TRUE);

            $args["cd_empresa"] = (trim($this->input->post("cd_empresa_part", TRUE)) != "" ? trim($this->input->post("cd_empresa_part", TRUE)) : $args["cd_empresa"]);
            $args["cd_registro_empregado"] = (trim($this->input->post("cd_registro_empregado_part", TRUE)) != "" ? trim($this->input->post("cd_registro_empregado_part", TRUE)) : "");
            $args["seq_dependencia"] = (trim($this->input->post("seq_dependencia_part", TRUE)) != "" ? trim($this->input->post("seq_dependencia_part", TRUE)) : "");

			$fl_ingresso = (trim($this->input->post("fl_ingresso", TRUE)) == "S" ? TRUE : FALSE);
			
            #manter_filtros($args);	

            $args["part_selecionado"] = $this->input->post("part_selecionado", TRUE);

            if ($tipo == "C") ### COMPLETO
            {
                $fl_frente = "S";
                $fl_verso = "S";
            }
            else if ($tipo == "F") ### FRENTE
            {
                $fl_frente = "S";
                $fl_verso = "N";
            }
            else if ($tipo == "V") ### VERSO
            {
                $fl_frente = "N";
                $fl_verso = "S";
            }
            else
            {
                $fl_frente = "N";
                $fl_verso = "N";
            }

            $this->Certificado_participante_model->certificadoLista($result, $args);
            $ar_certificado = $result->result_array();

            $this->certificadoImprime($ar_certificado, $fl_frente, $fl_ingresso, $fl_verso);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function certificadoRE($cd_empresa, $cd_registro_empregado, $seq_dependencia,$fl_ingresso="N")
    {
        #CheckLogin();
        $this->load->model('projetos/Certificado_participante_model');
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_empresa"] = $cd_empresa;
        $args["cd_registro_empregado"] = $cd_registro_empregado;
        $args["seq_dependencia"] = $seq_dependencia;
        $args["dt_inicial"] = "";
        $args["dt_final"] = "";
        $args["cd_plano"] = "";


        $fl_frente = "S";
		$fl_ingresso = ($fl_ingresso == "S" ? TRUE : FALSE);
        $fl_verso = "S";

        $this->Certificado_participante_model->certificadoLista($result, $args);
        $ar_certificado = $result->result_array();

        $this->certificadoImprime($ar_certificado, $fl_frente, $fl_ingresso, $fl_verso);
    }
	
    function certificadoPadrao($cd_plano, $cd_empresa = -1)
    {
        #CheckLogin();
        $this->load->model('projetos/Certificado_participante_model');
        $args = Array();
        $data = Array();
        $result = null;

        $args["cd_empresa"] = $cd_empresa;
        $args["cd_plano"]   = $cd_plano;

        $fl_frente = "S";
		$fl_ingresso = FALSE;
        $fl_verso = "S";

        $this->Certificado_participante_model->certificadoPadrao($result, $args);
        $ar_certificado = $result->result_array();


        $this->certificadoImprime($ar_certificado, $fl_frente, $fl_ingresso, $fl_verso, true, false);
    }	

    function certificadoImprime($ar_certificado, $fl_frente, $fl_ingresso=true, $fl_verso, $fl_tela=true, $fl_preenche=true, $dir="up/", $arq="certificado.pdf")
    {
        $this->load->plugin('fpdf');

		#echo "<pre>".print_r($ar_certificado,true)."</pre>"; exit;
		
        $ar_mes = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
        $ob_pdf = new PDF('P', 'mm', 'A4');

        foreach ($ar_certificado as $ar_reg)
        {
            if ($fl_frente == "S")
            {
                #### FRENTE ####
                $ob_pdf->AddPage();

                #### BORDA ####
                $ob_pdf->SetDrawColor(0, 136, 190);
                $ob_pdf->SetLineWidth(0.4);
                $ob_pdf->Line(10, 10, 200, 10);
                $ob_pdf->Line(10, 10, 10, 285);
                $ob_pdf->Line(10, 285, 200, 285);
                $ob_pdf->Line(200, 10, 200, 285);

                $ob_pdf->SetLineWidth(1);
                $ob_pdf->Line(12, 12, 198, 12);
                $ob_pdf->Line(12, 12, 12, 283);
                $ob_pdf->Line(12, 283, 198, 283);
                $ob_pdf->Line(198, 12, 198, 283);

				#### RE ####
				$ob_pdf->SetXY(15, 14);
				$ob_pdf->SetFont('Courier', 'B', 10);
				$ob_pdf->MultiCell(182, 4, ($fl_preenche ? ($ar_reg['cd_empresa'] . "/" . $ar_reg['cd_registro_empregado'] . "/" . $ar_reg['seq_dependencia']) : ""), 0, "R");
				
                #### LOGO ####
                $ob_pdf->SetXY(15, 15);
                $ob_pdf->Image('img/logo_ffp.png', 50, 20, $ob_pdf->ConvertSize(380), $ob_pdf->ConvertSize(78));
                $ob_pdf->SetDrawColor(0, 0, 0);
                $ob_pdf->SetLineWidth(0.4);
                $ob_pdf->Line(45, 43, 160, 43);

                $ob_pdf->SetXY(12, 55);
                $ob_pdf->SetFont('Times', '', 24);
                $ob_pdf->MultiCell(186, 6, 'CERTIFICADO DE PARTICIPANTE' , 0, "C");
                $ob_pdf->SetDrawColor(0, 0, 0);
                $ob_pdf->SetLineWidth(0.4);
                $ob_pdf->Line(30, 64, 180, 64);

				if($fl_preenche)
				{
					#### NOME ####
					$ob_pdf->SetXY(12, 75);
					$ob_pdf->SetFont('Times', '', 15);
					$ob_pdf->MultiCell(186, 6, 'A Fundação Família Previdência certifica que', 0, "C");

					$ob_pdf->SetFont('Times', '', 22);
					$ar_reg['nome'] = str_replace(' Das ', ' das ', (str_replace(' Da ', ' da ', (str_replace(' Dos ', ' dos ', str_replace(' De ', ' de ', ucwords(strtolower($ar_reg['nome']))))))));
					$ob_pdf->SetX(12);
					$ob_pdf->MultiCell(186, 14, $ar_reg['nome'] , 0, "C");

					$ob_pdf->SetFont('Times', '', 15);
					$ob_pdf->SetX(12);
					$ob_pdf->MultiCell(186, 6, 'é participante do ' . $ar_reg['nome_plano_certificado'] , 0, "C");

					$ob_pdf->SetFont('Times', '', 15);
					$ob_pdf->SetX(12);
					$ob_pdf->MultiCell(186, 6, 'desde ' . $ar_reg['dia_ingresso'] . ' de ' . $ar_mes[($ar_reg['mes_ingresso'] - 1)] . ' de ' . $ar_reg['ano_ingresso'] . '.', 0, "C");
				}
				else
				{
					$ob_pdf->SetY($ob_pdf->GetY() + 15);
				}
				
                #### LOGO PLANO ####
                $ob_pdf->SetXY($ar_reg['nr_x_logo'], $ob_pdf->GetY() + 4);
                $ob_pdf->Image('img/certificado_logo_plano_' . $ar_reg['cd_plano'] . '.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize($ar_reg['nr_largura_logo']), $ob_pdf->ConvertSize($ar_reg['nr_altura_logo']));

				if(!$fl_preenche)
				{
					$ob_pdf->SetY($ob_pdf->GetY() + 15);
				}				
				
                #### DADOS FCEEE ####
                $ob_pdf->SetXY(12, $ob_pdf->GetY() + 37);
                $ob_pdf->SetFont('Times', '', 15);
                $ob_pdf->MultiCell(186, 6, 'Administradora: Fundação CEEE de Seguridade Social - ELETROCEEE', 0, "C");
                $ob_pdf->SetX(12);
                $ob_pdf->MultiCell(186, 6, 'CNPJ: 90.884.412/0001-24', 0, "C");

                $ob_pdf->SetXY(12, $ob_pdf->GetY() + 3);
                $ob_pdf->SetFont('Times', '', 13);
                $ob_pdf->MultiCell(186, 5, 'Contatos', 0, "C");
                $ob_pdf->SetX(12);
                $ob_pdf->MultiCell(186, 5, 'Atendimento: 0800 510 2596 (de telefone fixo) ou 51 3027 1221 (de telefone celular)', 0, "C");
                $ob_pdf->SetX(12);
                $ob_pdf->MultiCell(186, 5, 'Site: www.fundacaofamiliaprevidencia.com.br', 0, "C");
                $ob_pdf->SetX(12);
                $ob_pdf->MultiCell(186, 5, 'Endereço: Rua dos Andradas, 702/9º - Porto Alegre - RS', 0, "C");

                $ob_pdf->SetXY(12, $ob_pdf->GetY() + 5);
                $ob_pdf->SetFont('Times', '', 15);
                $ob_pdf->MultiCell(186, 6, $ar_reg['nome_plano_certificado'], 0, "C");
                $ob_pdf->SetX(12);
                $ob_pdf->MultiCell(186, 6, 'Cadastro Nacional de Planos de Benefícios: ' . $ar_reg['cd_plano_spc'], 0, "C");

                $ob_pdf->SetFont('Times', '', 15);
                $ob_pdf->SetXY(12, $ob_pdf->GetY() + 5);
                $ob_pdf->MultiCell(186, 6, "O presente Plano de Benefícios é regido por regulamento aprovado pela", 0, "C");
                $ob_pdf->SetX(12);
                $ob_pdf->MultiCell(186, 6, "Superintendência Nacional de Previdência Complementar - PREVIC", 0, "C");
                $ob_pdf->SetX(12);
				if((trim($ar_reg["dt_ingresso"]) != "") and ($fl_ingresso))
				{
					$ob_pdf->MultiCell(186, 8, ($fl_preenche ? ("Porto Alegre, " . trim($ar_reg["dia_ingresso"]) . " de " . $ar_mes[(intval($ar_reg["mes_ingresso"]) - 1)] . " de " . trim($ar_reg["ano_ingresso"]) . ".") : '') , 0, "C");
				}
				else
				{
					$ob_pdf->MultiCell(186, 8, ($fl_preenche ? ("Porto Alegre, " . date("d") . " de " . $ar_mes[(date("m") - 1)] . " de " . date("Y") . ".") : '') , 0, "C");
				}
                
                #### ASSINATURA ####
                $ob_pdf->SetXY(50, $ob_pdf->GetY());
                $ob_pdf->Image('./img/certificado/'.$ar_reg['presidente_assinatura'], $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(421), $ob_pdf->ConvertSize(192));

                $ob_pdf->SetXY(12, $ob_pdf->GetY() + 37);
                $ob_pdf->MultiCell(186, 6, $ar_reg['presidente_nome'], 0, "C");
                $ob_pdf->SetX(12);
                $ob_pdf->MultiCell(186, 6, "Diretor-Presidente", 0, "C");
                
                /*
                $ob_pdf->SetXY(12, $ob_pdf->GetY() + 20);
                $ob_pdf->MultiCell(186, 6, 'Atenciosamente,', 0, "C");
                $ob_pdf->SetX(12);
                $ob_pdf->MultiCell(186, 6, "Fundação CEEE", 0, "C");
                */
            }

            if ($fl_verso == "S")
            {
                #### VERSO ####
                $ob_pdf->AddPage();
                $ob_pdf->SetXY(10, 8);
                $ob_pdf->SetFont('Arial', '', 10);
                $ob_pdf->MultiCell(190, 4, $ar_reg['nome_plano_certificado'], 0, "C");

                $ob_pdf->SetFont('Helvetica', '', $ar_reg['nr_fonte_verso']);
                $ob_pdf->SetXY(8, 16);
                $ob_pdf->MultiCell(98, $ar_reg['nr_altura_linha_verso'], $ar_reg['coluna_1'], 0, "J");
                $ob_pdf->SetXY(108, 16);
                $ob_pdf->MultiCell(95, $ar_reg['nr_altura_linha_verso'], $ar_reg['coluna_2'], 0, "J");

                $ob_pdf->SetXY(20, 267);
                $ob_pdf->Image('img/certificado_logo_plano_' . $ar_reg['cd_plano'] . '.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize($ar_reg['nr_largura_logo']) / 2, $ob_pdf->ConvertSize($ar_reg['nr_altura_logo']) / 2);

                //$ob_pdf->SetXY(95, 270);
                //$ob_pdf->Image('img/certificado_disqueeletro.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(88), $ob_pdf->ConvertSize(50));

                $ob_pdf->SetXY(150, 272);
                $ob_pdf->Image('img/logo_ffp_preto_branco.png', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(180), $ob_pdf->ConvertSize(37));
            }

            if (($fl_frente == "N") and ($fl_verso == "S"))
            {
                break;
            }
        }

        if ($fl_tela)
        {
            $ob_pdf->Output();
        }
        else
        {
            $ob_pdf->Output($dir . $arq, "F");
        }
    }

    function etiqueta()
    {
        CheckLogin();
        if($this->get_permissao())
        {
            $this->load->model('projetos/Certificado_participante_model');
            $this->load->plugin('fpdf');
            $args = Array();
            $data = Array();
            $result = null;

            $args["dt_inicial"] = $this->input->post("dt_inicial", TRUE);
            $args["dt_final"] = $this->input->post("dt_final", TRUE);
            $args["cd_plano_empresa"] = $this->input->post("cd_plano_empresa", TRUE); ## para manter o filtro ##
            $args["cd_empresa"] = $this->input->post("cd_plano_empresa", TRUE);
            $args["cd_plano"] = $this->input->post("cd_plano", TRUE);

            $args["cd_empresa"] = (trim($this->input->post("cd_empresa_part", TRUE)) != "" ? trim($this->input->post("cd_empresa_part", TRUE)) : $args["cd_empresa"]);
            $args["cd_registro_empregado"] = (trim($this->input->post("cd_registro_empregado_part", TRUE)) != "" ? trim($this->input->post("cd_registro_empregado_part", TRUE)) : "");
            $args["seq_dependencia"] = (trim($this->input->post("seq_dependencia_part", TRUE)) != "" ? trim($this->input->post("seq_dependencia_part", TRUE)) : "");

            #manter_filtros($args);	

            $args["part_selecionado"] = $this->input->post("part_selecionado", TRUE);

            $this->Certificado_participante_model->certificadoLista($result, $args);
            $ar_certificado = $result->result_array();

            $ob_pdf = new PDF('P', 'mm', 'Letter');
            $ob_pdf->SetMargins(5, 14, 5);
            $ob_pdf->AddPage();
            $ob_pdf->AddFont('ECTSymbol');

            $nr_x = 0;
            $nr_y = 0;
            $nr_conta = 0;
            $nr_conta_x = 0;
            foreach ($ar_certificado as $ar_reg)
            {
                $ob_pdf->SetXY($ob_pdf->GetX() + $nr_x, $ob_pdf->GetY() + $nr_y);
                $ob_pdf->SetFont('ECTSymbol', '', 16);
                $ob_pdf->Text($ob_pdf->GetX() + 4, $ob_pdf->GetY() + 6.7, $ar_reg['cep_net']);

                $ob_pdf->SetFont('Courier', '', 7);
                $ob_pdf->Text($ob_pdf->GetX() + 4, $ob_pdf->GetY() + 9.5, substr($ar_reg['nome'], 0, 25));
                $re = $ar_reg['cd_empresa'] . " " . $ar_reg['cd_registro_empregado'] . " " . $ar_reg['seq_dependencia'];
                $ob_pdf->Text($ob_pdf->GetX() + (62 - $ob_pdf->GetStringWidth($re)), $ob_pdf->GetY() + 9.5, $re);
                $ob_pdf->Text($ob_pdf->GetX() + 4, $ob_pdf->GetY() + 13, $ar_reg['logradouro']);
                $ob_pdf->Text($ob_pdf->GetX() + 4, $ob_pdf->GetY() + 16.5, substr($ar_reg['bairro'], 0, 15));
                $cidade = $ar_reg['cidade'] . " " . $ar_reg['uf'];
                $ob_pdf->Text($ob_pdf->GetX() + (62 - $ob_pdf->GetStringWidth($cidade)), $ob_pdf->GetY() + 16.5, $cidade);
                $ob_pdf->Text($ob_pdf->GetX() + 4, $ob_pdf->GetY() + 20, $ar_reg['cep']);

                $nr_conta++;
                $nr_conta_x++;

                if ($nr_conta_x == 3)
                {
                    $ob_pdf->SetX(5);
                    $nr_x = 0;
                    $nr_y = 25.5;
                    $nr_conta_x = 0;
                }
                else
                {
                    $nr_x = 68.5;
                    $nr_y = 0;
                }

                if ($nr_conta == 30)
                {
                    $ob_pdf->AddPage();
                    $ob_pdf->SetMargins(5, 14, 5);
                    $nr_conta = 0;
                    $nr_x = 0;
                    $nr_y = 0;
                }
            }
            $ob_pdf->Output();
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

    function protocolo()
    {
        CheckLogin();
        if($this->get_permissao())
        {
            $this->load->model('projetos/Certificado_participante_model');
            $args = Array();
            $data = Array();
            $result = null;

            $args["dt_inicial"] = $this->input->post("dt_inicial", TRUE);
            $args["dt_final"] = $this->input->post("dt_final", TRUE);

            $args["cd_plano_empresa"] = $this->input->post("cd_empresa", TRUE); ## para manter o filtro ##
            $args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
            $args["cd_plano"] = $this->input->post("cd_plano", TRUE);

            $args["cd_empresa"] = (trim($this->input->post("cd_empresa_part", TRUE)) != "" ? trim($this->input->post("cd_empresa_part", TRUE)) : $args["cd_empresa"]);
            $args["cd_registro_empregado"] = (trim($this->input->post("cd_registro_empregado_part", TRUE)) != "" ? trim($this->input->post("cd_registro_empregado_part", TRUE)) : "");
            $args["seq_dependencia"] = (trim($this->input->post("seq_dependencia_part", TRUE)) != "" ? trim($this->input->post("seq_dependencia_part", TRUE)) : "");

            #manter_filtros($args);	

            $args["part_selecionado"] = $this->input->post("part_selecionado", TRUE);


            #echo "<PRE>".print_r($args,true)."</PRE>"; exit;

            $this->Certificado_participante_model->certificado_lista_documentos($result, $args);
            $ar_certificado = $result->result_array();
			
			#echo count($ar_certificado); #exit;

            #echo "<PRE>".print_r($ar_certificado,true)."</PRE>"; exit;
            $re = '';

            $ar_protocolo = Array();
            if (count($ar_certificado) > 0)
            {
                foreach ($ar_certificado as $ar_reg)
                {
                    $prot['cd_empresa'] = $ar_reg['cd_empresa'];
                    $prot['cd_registro_empregado'] = $ar_reg['cd_registro_empregado'];
                    $prot['seq_dependencia'] = $ar_reg['seq_dependencia'];

                    $args['cd_empresa'] = $ar_reg['cd_empresa'];
                    
                    if(intval($ar_reg['cd_documento']) != 273)
                    {
                        $prot['cd_documento'] = $ar_reg['cd_documento'];

                        $this->Certificado_participante_model->getDocumento($result, $prot);
                        $ar_ret = $result->row_array();
                        $ar_reg['cd_documento'] = $prot['cd_documento'];
                        $ar_reg['ds_documento'] = $ar_ret['ds_documento'];
						$ar_reg['tipo'] = 'P'; 

                        if(trim($ar_reg['fl_verificar']) == 'S')
                        {
                            $this->Certificado_participante_model->verificaDocumento($result, $prot);

                            $ar_ret = $result->row_array();
                            if ($ar_ret['fl_documento'] == "S")
                            {
                                $ar_reg['cd_documento'] = $prot['cd_documento'];
                                $ar_protocolo[] = $ar_reg;
                            }
                        }
                        else 
                        {
                            $ar_protocolo[] = $ar_reg;
                        }
                    }
                    
                    if($re != $ar_reg['cd_empresa'].'/'.$ar_reg['cd_registro_empregado'].'/'.$ar_reg['seq_dependencia'])
                    {
                        $prot['cd_documento'] = 273;
                        $this->Certificado_participante_model->getDocumento($result, $prot);
                        $ar_ret = $result->row_array();
                        $ar_reg['cd_documento'] = $prot['cd_documento'];
                        $ar_reg['ds_documento'] = $ar_ret['ds_documento'];
						$ar_reg['tipo'] = 'D'; 
                        $ar_protocolo[] = $ar_reg;
                        
                        $re = $ar_reg['cd_empresa'].'/'.$ar_reg['cd_registro_empregado'].'/'.$ar_reg['seq_dependencia'];
                    }
                 }   
            }
			
			$this->Certificado_participante_model->limpa_tmp($result, Array('cd_usuario_cadastro' => intval(usuario_id())));			
			$args_tmp = Array();
			foreach($ar_protocolo as $item)
			{
				$args_tmp['id']                         = (uniqid(rand(), true));
				$args_tmp['cd_tipo_doc']                = $item['cd_documento'];
				$args_tmp['cd_documento_recebido']      = '';
				$args_tmp['cd_documento_recebido_item'] = '';
				$args_tmp['cd_empresa']                 = $item['cd_empresa'];
				$args_tmp['cd_registro_empregado']      = $item['cd_registro_empregado'];
				$args_tmp['seq_dependencia']            = $item['seq_dependencia'];
				$args_tmp['seq_dependencia']            = $item['seq_dependencia'];
				$args_tmp['re_cripto']                  = $item['re_cripto'];
				$args_tmp['arquivo']                    = '';
				$args_tmp['arquivo_nome']               = '';
				$args_tmp['tipo']                       = $item['tipo'];
				$args_tmp['fl_verificar']               = $item['fl_verificar'];
				$args_tmp['cd_usuario_cadastro']        = intval(usuario_id());
				$this->Certificado_participante_model->salva_certificado_tmp($result, $args_tmp);
			}			
			
			$args['cd_usuario_cadastro'] = intval(usuario_id());
			$this->Certificado_participante_model->certificado_tmp($result, $args);
			$ar_certificado = $result->result_array();
            $data["ar_lista"] = $ar_certificado;
            $this->load->view('ecrm/certificado_participante/protocolo_result', $data);
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }

	function certificadoTMPAdd()
	{
		CheckLogin();
        if($this->get_permissao())
        {	
			$this->load->model('projetos/Certificado_participante_model');
			$args = Array();
			$data = Array();
			$result = null;		
			
			$args['id']                         = $this->input->post("id", TRUE);
			$args['cd_tipo_doc']                = $this->input->post("cd_tipo_doc", TRUE);
			$args['cd_documento_recebido']      = $this->input->post("cd_documento_recebido", TRUE);
			$args['cd_documento_recebido_item'] = $this->input->post("cd_documento_recebido_item", TRUE);
			$args['cd_empresa']                 = $this->input->post("cd_empresa", TRUE);
			$args['cd_registro_empregado']      = $this->input->post("cd_registro_empregado", TRUE);
			$args['seq_dependencia']            = $this->input->post("seq_dependencia", TRUE);
			$args['re_cripto']                  = $this->input->post("re_cripto", TRUE);
			$args['arquivo']                    = $this->input->post("arquivo", TRUE);
			$args['arquivo_nome']               = $this->input->post("arquivo_nome", TRUE);
			$args['tipo']                       = $this->input->post("tipo", TRUE);
			$args['fl_verificar']               = $this->input->post("fl_verificar", TRUE);
			$args['cd_usuario_cadastro']        = intval(usuario_id());
			$this->Certificado_participante_model->salva_certificado_tmp($result, $args);	
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }		
	}
	
	function certificadoTMPDel()
	{
		CheckLogin();
        if($this->get_permissao())
        {
			$this->load->model('projetos/Certificado_participante_model');
			$args = Array();
			$data = Array();
			$result = null;		
			
			$args['id'] = $this->input->post("id", TRUE);
			$this->Certificado_participante_model->excluir_certificado_tmp($result, $args);	
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }			
	}

	public function get_tempo_descarte($cd_documento = 0)
	{
        $args = array();
        $url  = 'http://10.63.255.217:8080/ords/ordsws/tabela_temporalidade_doc/tempo_descarte/index';

        $args = array(
            'id'          => '8dcfac716cf69a12255d63a4abf8b485',
            'cd_tipo_doc' => $cd_documento
        );

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $retorno_json = curl_exec($ch);

        $json = json_decode($retorno_json, true);

    	$vl_arquivo_central = (trim($json['result'][0]['vl_arquivo_central']) != '' ? trim($json['result'][0]['vl_arquivo_central']).' - ' : '');
		$ds_tempo_descarte  = $vl_arquivo_central . $json['result'][0]['id_arquivo_central'];
		$id_classificacao_info_doc  = $json['result'][0]['id_classificacao_info_doc'];

        return array('ds_tempo_descarte' => $ds_tempo_descarte, 'id_classificacao_info_doc' => $id_classificacao_info_doc);
	}	
	
    function protocolo_gerar()
    {
        CheckLogin();
        if($this->get_permissao())
        {
            $this->load->model('projetos/Certificado_participante_model');
            $this->load->plugin('fpdf');
            $args = Array();
            $data = Array();
            $result = null;

            $args['fl_ordenacao_1']      = $this->input->post("fl_ordenacao_1", TRUE);
            $args['fl_tipo_order_1']     = $this->input->post("fl_tipo_order_1", TRUE);
            $args['fl_ordenacao_2']      = $this->input->post("fl_ordenacao_2", TRUE);
            $args['fl_tipo_order_2']     = $this->input->post("fl_tipo_order_2", TRUE);
			$args['cd_usuario_cadastro'] = intval(usuario_id());
			$args['cd_gerencia']         = $this->session->userdata('divisao');
            $args['fl_contrato']         = "";
			$fl_ingresso                 = (trim($this->input->post("fl_ingresso", TRUE)) == "S" ? TRUE : FALSE);
  
			$this->Certificado_participante_model->certificado_tmp($result, $args);
            $ar_doc_tmp= $result->result_array();

			//echo "<pre>".print_r($ar_doc_tmp,TRUE)."</pre>";
			
            if (count($ar_doc_tmp) > 0)
            {
                $ar_prot_gerado = Array();
                $this->load->model('projetos/Documento_protocolo_model');
                $args['ano'] = date('Y');
                $re = array();

                #### DIGITAL ####
                $ar_protocolo = Array();
                foreach ($ar_doc_tmp as $ar_reg)
                {
					$tempo_descarte = $this->get_tempo_descarte($ar_reg['cd_tipo_doc']);
					
                    $prot = Array();
                    $prot['cd_documento_protocolo_item'] = 0;
                    $prot['cd_empresa'] = $ar_reg['cd_empresa'];
                    $prot['cd_registro_empregado'] = $ar_reg['cd_registro_empregado'];
                    $prot['seq_dependencia'] = $ar_reg['seq_dependencia'];
                    $prot['nr_folha'] = 1;
                    $prot['cd_usuario_cadastro'] = intval(usuario_id());
                    $prot['observacao'] = "";
                    $prot['ds_processo'] = "";
					$prot['ds_tempo_descarte']           = utf8_decode($tempo_descarte['ds_tempo_descarte']);
					$prot['id_classificacao_info_doc']   = utf8_decode($tempo_descarte['id_classificacao_info_doc']);
					
					#echo $ar_reg['cd_documento']." | ".$ar_reg['cd_registro_empregado']." | ".intval($ar_reg['cd_documento_recebido_item'])." | ".trim($ar_reg['arquivo']).br(2);
					
                    //CERTIFICADO
					if(intval($ar_reg['cd_documento']) == 273)
					{
						$prot['cd_documento'] = $ar_reg['cd_documento'];
						if ((count($ar_protocolo) == 0) or (intval($ar_protocolo['cd_documento_protocolo']) == 0))
						{
							#### PROTOCOLO DIGITAL ####
							$args['tipo_protocolo'] = "D";
							$this->Documento_protocolo_model->criar_protocolo($args, $msg, $ar_protocolo);
							$ar_prot_gerado[] = $ar_protocolo;
							#Array ( [cd_documento_protocolo] => 1572 [ano] => 2011 [contador] => 216 ) 
						}

						$arq = $ar_reg['cd_empresa'] . "_" . $ar_reg['cd_registro_empregado'] . "_" . $ar_reg['seq_dependencia'] . "_273_" . uniqid(time()) . ".pdf";
						$dir = "up/protocolo_digitalizacao_" . intval($ar_protocolo['cd_documento_protocolo']) . "/";
						$prot['arquivo'] = $arq;
						$prot['arquivo_nome'] = $arq;
						$prot['cd_documento_protocolo'] = intval($ar_protocolo['cd_documento_protocolo']);
						$ar_args_cert['cd_empresa']            = $ar_reg['cd_empresa'];
						$ar_args_cert['cd_registro_empregado'] = $ar_reg['cd_registro_empregado'];
						$ar_args_cert['seq_dependencia']       = $ar_reg['seq_dependencia'];
						$this->Certificado_participante_model->certificadoLista($result, $ar_args_cert);
						$ar_par_cert = $result->result_array();
						$this->certificadoImprime($ar_par_cert, "S", $fl_ingresso, "S", false, true, $dir, $arq);
						$this->Documento_protocolo_model->adicionaDocumento($result, $prot);
					}
					
					//PROTOCOLO	
					if((intval($ar_reg['cd_documento_recebido_item']) > 0) and (trim($ar_reg['arquivo']) != '')) //SOMENTE COM ARQUIVO = DIGITAL
					{
						$prot['cd_documento'] = $ar_reg['cd_documento'];
						if ((count($ar_protocolo) == 0) or (intval($ar_protocolo['cd_documento_protocolo']) == 0))
						{
							#### PROTOCOLO DIGITAL ####
							$args['tipo_protocolo'] = "D";
							$this->Documento_protocolo_model->criar_protocolo($args, $msg, $ar_protocolo);
							$ar_prot_gerado[] = $ar_protocolo;
							#Array ( [cd_documento_protocolo] => 1572 [ano] => 2011 [contador] => 216 ) 
						}
						
						$dir_origem = "up/documento_recebido/";
						$dir = "up/protocolo_digitalizacao_" . intval($ar_protocolo['cd_documento_protocolo']) . "/";
						
						copy("./".$dir_origem.$ar_reg['arquivo'], "./".$dir.$ar_reg['arquivo']);								
						
						$prot['arquivo'] = $ar_reg['arquivo'];
						$prot['arquivo_nome'] = $ar_reg['arquivo_nome'];
						$prot['cd_documento_protocolo'] = intval($ar_protocolo['cd_documento_protocolo']);
						$this->Documento_protocolo_model->adicionaDocumento($result, $prot);
					}
                }
				
                $ar_ret = array();
                #### PAPEL ####
                $ar_protocolo = Array();
                foreach ($ar_doc_tmp as $ar_reg)
                {
                    $prot = Array();
                    $prot['cd_documento_protocolo_item'] = 0;
                    $prot['cd_empresa'] = $ar_reg['cd_empresa'];
                    $prot['cd_registro_empregado'] = $ar_reg['cd_registro_empregado'];
                    $prot['seq_dependencia'] = $ar_reg['seq_dependencia'];
                    $prot['nr_folha'] = 1;
                    $prot['cd_usuario_cadastro'] = intval(usuario_id());
                    $prot['arquivo'] = "";
                    $prot['arquivo_nome'] = "";
                    $prot['observacao'] = "";
                    $prot['ds_processo'] = "";
					$prot['cd_documento'] = $ar_reg['cd_documento'];
                    $prot['cd_tipo_doc']  = $ar_reg['cd_documento'];
                    $args['cd_divisao'] = 'GAP';
                    $args['cd_empresa'] = $ar_reg['cd_empresa'];
					
					#echo $ar_reg['cd_documento']." | ".$ar_reg['cd_registro_empregado']." | ".intval($ar_reg['cd_documento_recebido_item'])." | ".trim($ar_reg['arquivo']).br(2);
					
                    if(intval($prot['cd_documento']) != 273)
                    {
						//CERTIFICADO
						if((intval($ar_reg['cd_documento_recebido']) == 0) and (trim($ar_reg['arquivo']) == ''))
						{
							if(trim($ar_reg['fl_verificar']) == 'S')
							{
								$this->Certificado_participante_model->verificaDocumento($result, $prot);
								$ar_ret = $result->row_array();
							}

							if(((count($ar_ret) > 0) AND ($ar_ret['fl_documento'] == "S")) OR (trim($ar_reg['fl_verificar']) == 'N'))
							{
								if ((count($ar_protocolo) == 0) or (intval($ar_protocolo['cd_documento_protocolo']) == 0))
								{
									#### PROTOCOLO PAPEL DOCUMENTOS ####
									$args['tipo_protocolo'] = "P";
									$this->Documento_protocolo_model->criar_protocolo($args, $msg, $ar_protocolo);
									$ar_prot_gerado[] = $ar_protocolo;
								}

								$prot['cd_tipo_doc'] 	   = $prot['cd_documento'];
								$prot['ds_tempo_descarte'] = utf8_decode($this->get_tempo_descarte($prot['cd_tipo_doc']));
								$prot['cd_divisao'] 	   = 'GAP';
								$this->Documento_protocolo_model->descartar($result, $prot);
								$ar_descarte = $result->row_array();

								$prot['fl_descartar'] = 'N';

								if (count($ar_descarte) > 0)
								{
									$prot['fl_descartar'] = $ar_descarte['fl_descarte'];
								}

								$prot['cd_documento_protocolo'] = intval($ar_protocolo['cd_documento_protocolo']);
								$this->Documento_protocolo_model->adicionaDocumento($result, $prot);
							}
						}
						
						//PROTOCOLO
						if((intval($ar_reg['cd_documento_recebido']) > 0) and (trim($ar_reg['arquivo']) == '')) //SOMENTE SEM ARQUIVO = PAPEL
						{
							if(trim($ar_reg['fl_verificar']) == 'S')
							{
								$this->Certificado_participante_model->verificaDocumento($result, $prot);
								$ar_ret = $result->row_array();
							}

							if(((count($ar_ret) > 0) AND ($ar_ret['fl_documento'] == "S")) OR (trim($ar_reg['fl_verificar']) == 'N'))
							{
								if ((count($ar_protocolo) == 0) or (intval($ar_protocolo['cd_documento_protocolo']) == 0))
								{
									#### PROTOCOLO PAPEL DOCUMENTOS ####
									$args['tipo_protocolo'] = "P";
									$this->Documento_protocolo_model->criar_protocolo($args, $msg, $ar_protocolo);
									$ar_prot_gerado[] = $ar_protocolo;
								}

								$prot['cd_tipo_doc'] = $prot['cd_documento'];
								$prot['cd_divisao'] = 'GAP';
								$this->Documento_protocolo_model->descartar($result, $prot);
								$ar_descarte = $result->row_array();

								$prot['fl_descartar'] = 'N';

								if (count($ar_descarte) > 0)
								{
									$prot['fl_descartar'] = $ar_descarte['fl_descarte'];
								}

								$prot['cd_documento_protocolo'] = intval($ar_protocolo['cd_documento_protocolo']);
								$this->Documento_protocolo_model->adicionaDocumento($result, $prot);
							}
						}
						
                    }
                }
				
                //echo "<PRE>".print_r($ar_prot_gerado,true)."</PRE>"; echo count($ar_prot_gerado); exit;

                if (count($ar_prot_gerado) == 0)
                {
                    exibir_mensagem("ERRO AO GERAR PROTOCOLO");
                }
                else if (count($ar_prot_gerado) == 1)
                {
                    redirect("ecrm/protocolo_digitalizacao/detalhe_atendimento/" . $ar_prot_gerado[0]['cd_documento_protocolo'], "refresh");
                }
                else if (count($ar_prot_gerado) == 2)
                {
                    echo '
                        <script>
                                alert("PROTOCOLO GERADOS:\\n\\n- ' . $ar_prot_gerado[0]['ano'] . "/" . $ar_prot_gerado[0]['contador'] . '\\n\\n- ' . $ar_prot_gerado[1]['ano'] . "/" . $ar_prot_gerado[1]['contador'] . '");
                                location.href="' . base_url() . index_page() . '/ecrm/protocolo_digitalizacao";
                        </script>
                 ';
                }
            }
        }
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
    }
	
	function lista_protocolo_interno()
	{
		CheckLogin();
        if($this->get_permissao())
        {
            $args = array();
            $data = array();
            $result = null;
			
			$this->load->model('projetos/certificado_participante_model');

            $args["nr_ano"] = $this->input->post("nr_ano", TRUE);
			$args["nr_contador"] = $this->input->post("nr_contador", TRUE);
			$args["cd_empresa"] = $this->input->post("cd_empresa", TRUE);
			
			$this->certificado_participante_model->lista_protocolo_interno($result, $args);
			$data['collection'] = $result->result_array();
			
			$this->load->view('ecrm/certificado_participante/protocolo_interno_result', $data);
		}
        else
        {
            exibir_mensagem('ACESSO NÃO PERMITIDO');
        }
	}

}
