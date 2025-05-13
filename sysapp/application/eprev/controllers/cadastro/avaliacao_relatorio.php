<?php
class avaliacao_relatorio extends Controller
{
    function __construct()
    {
        parent::Controller();
		
        CheckLogin();
        $this->load->model('projetos/avaliacao_relatorio_model');
    }
	
	function index()
    {
        if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $args = Array();
			$data = Array();
			$result = null;

            $this->load->view('cadastro/avaliacao_relatorio/index',$data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
    }
	
	public function listar()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $args = Array();
			$data = Array();
			$result = null;

            $args['ano']                          = $this->input->post("ano", TRUE);   
			$args['cd_usuario_avaliado_gerencia'] = $this->input->post("cd_usuario_avaliado_gerencia", TRUE);   
			$args['cd_usuario_avaliado']          = $this->input->post("cd_usuario_avaliado", TRUE);   
			$args['tipo_promocao']                = $this->input->post("tipo_promocao", TRUE);   
			$args['fl_promocao']                  = $this->input->post("fl_promocao", TRUE);   
			$args['cd_avaliacao_capa']            = ''; 
				
			manter_filtros($args);

			$this->avaliacao_relatorio_model->listar($result, $args);
			$data['collection'] = $result->result_array();

			$this->load->view('cadastro/avaliacao_relatorio/partial_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }        
    }
	
	public function comite()
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $args = Array();
			$data = Array();
			$result = null;

            $this->load->view('cadastro/avaliacao_relatorio/comite',$data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }
	}
	
	public function listar_comite()
    {
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $args = Array();
			$data = Array();
			$result = null;

            $args['ano']                          = $this->input->post("ano", TRUE);   
			$args['cd_usuario_avaliado_gerencia'] = $this->input->post("cd_usuario_avaliado_gerencia", TRUE);   
			$args['cd_usuario_avaliado']          = $this->input->post("cd_usuario_avaliado", TRUE);   
			$args['tipo_promocao']                = $this->input->post("tipo_promocao", TRUE);   
			$args['fl_avaliado']                  = $this->input->post("fl_avaliado", TRUE);   
				
			manter_filtros($args);

			$data['collection'] = array();
			
			$this->avaliacao_relatorio_model->listar_avaliado_comite($result, $args);
			$collection = $result->result_array();
			
			$i = 0;
			
			foreach($collection as $item)
			{
				$args['cd_avaliacao_capa'] = $item['cd_avaliacao_capa'];
			
				$data['collection'][$i]['cd_avaliacao_capa']   = $item['cd_avaliacao_capa'];
				$data['collection'][$i]['periodo']             = $item['periodo'];
				$data['collection'][$i]['avaliado']            = $item['avaliado'];
				$data['collection'][$i]['tipo_promocao']       = $item['tipo_promocao'];
				$data['collection'][$i]['tipo_promocao_color'] = $item['tipo_promocao_color'];
				$data['collection'][$i]['divisao']             = $item['divisao'];
				
				$this->avaliacao_relatorio_model->avaliacao_comite($result, $args);
				$comite = $result->result_array();
				
				$j = 0;
				
				foreach($comite as $item2)
				{
					$data['collection'][$i]['comite'][$j]['nome']     = $item2['nome'];
					$data['collection'][$i]['comite'][$j]['ja_avaliou'] = $item2['ja_avaliou'];
					
					$j++;
				}
				
				$i++;
			}

			$this->load->view('cadastro/avaliacao_relatorio/comite_result', $data);
        }
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        }        
    }
	
	
	public function pdf($cd_avaliacao_capa = 0)
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $args = Array();
			$data = Array();
			$result = null;
			
            $args['ano']                          = $this->input->post("ano", TRUE);   
			$args['cd_usuario_avaliado_gerencia'] = $this->input->post("cd_usuario_avaliado_gerencia", TRUE);   
			$args['cd_usuario_avaliado']          = $this->input->post("cd_usuario_avaliado", TRUE);   
			$args['tipo_promocao']                = $this->input->post("tipo_promocao", TRUE);   
			$args['fl_promocao']                  = $this->input->post("fl_promocao", TRUE);   
			$args['cd_avaliacao_capa']            = $cd_avaliacao_capa; 
			
			if(intval($args['cd_avaliacao_capa']) > 0)
			{
				$args['ano']                          = '';   
				$args['cd_usuario_avaliado_gerencia'] = '';   
				$args['cd_usuario_avaliado']          = '';   
				$args['tipo_promocao']                = '';   
			}
			
			$this->avaliacao_relatorio_model->listar($result, $args);
			$collection = $result->result_array();
			
			$this->load->plugin('fpdf');
			
			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');			
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->header_titulo = true;
			$ob_pdf->header_titulo_texto = "Processo de Avaliação";
			
			$ob_pdf->SetFont('segoeuil', '', 12);
			
			foreach($collection as $item)
			{
				$args['cd_avaliacao_capa'] = $item['cd_avaliacao_capa'];
				
				$args['tipo'] = 'A';
				$this->avaliacao_relatorio_model->resultado($result, $args);
				$avaliado = $result->row_array();
				
				$args['tipo'] = 'S';
				$this->avaliacao_relatorio_model->resultado($result, $args);
				$superior = $result->row_array();
				
				$args['tipo'] = 'C';
				$this->avaliacao_relatorio_model->resultado($result, $args);
				$comite = $result->row_array();
				
				$this->avaliacao_relatorio_model->expectativas($result, $args);
				$expectativas = $result->result_array();
			
				$ob_pdf->AddPage();
				
				$ob_pdf->MultiCell(0, 5, "Período: ".$item['dt_periodo'], '0', 'L');
				$ob_pdf->MultiCell(0, 5, "Avaliado: ".$item['nome_avaliado'], '0', 'L');
				$ob_pdf->MultiCell(0, 5, "Avaliador: ".$item['nome_avaliador'], '0', 'L');
				$ob_pdf->MultiCell(0, 5, "Tipo: ".$item['tipo_promocao'], '0', 'L');
			
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetLineWidth(0);
				$ob_pdf->Line(10,$ob_pdf->GetY(),200,$ob_pdf->GetY());	
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				
				$ob_pdf->SetFont('segoeuib','', 10);
				$ob_pdf->SetY($ob_pdf->GetY() + 2.5);
				$ob_pdf->Text(10, $ob_pdf->GetY(), "Resultado:");
				
				
				$ob_pdf->SetY($ob_pdf->GetY() + 5);
				$linha = $ob_pdf->GetY();
								
				$ob_pdf->SetFont('segoeuib','', 8);
				$ob_pdf->Text(10, $linha, "Avaliado:");
				$ob_pdf->SetFont('segoeuil','', 8);
				$ob_pdf->Text(10, $linha + 5, "Competências Institucionais: ".number_format($avaliado["grau_institucional"],2,",","."));
				$ob_pdf->Text(10, $linha + 8, "Escolaridade: ".number_format($avaliado["grau_escolaridade"],2,",","."));
				$ob_pdf->Text(10, $linha + 10, "-----------------------------------------------");
				$ob_pdf->Text(10, $linha + 13, "MÉDIA (M1): ".number_format($avaliado['grau_1'] ,2,",","."));
				$ob_pdf->Text(10, $linha + 20, "Competências Específicas: ".number_format($avaliado["grau_especific"],2,",","."));
				$ob_pdf->Text(10, $linha + 23, "Responsabilidades: ".number_format($avaliado["grau_responsabilidade"],2,",","."));
				$ob_pdf->Text(10, $linha + 25, "-----------------------------------------------");
				$ob_pdf->Text(10, $linha + 28, "MÉDIA (M2): ".number_format($avaliado['grau_2'],2,",","."));
				$ob_pdf->SetFont('segoeuib','', 8);
				$ob_pdf->Text(10, $linha + 35, "Resultado: ".number_format($avaliado['nota_final'],2,",","."));
				$ob_pdf->SetFont('segoeuil','', 6);
			    $ob_pdf->Text(10, $linha + 38, "Resultado=40% de M1 + 60% de M2");
				
				$ob_pdf->SetFont('segoeuib','', 8);
				$ob_pdf->Text(70, $linha, "Superior:");
				$ob_pdf->SetFont('segoeuil','', 8);
				$ob_pdf->Text(70, $linha + 5, "Competências Institucionais: ".number_format($superior["grau_institucional"],2,",","."));
				$ob_pdf->Text(70, $linha + 8, "Escolaridade: ".number_format($superior["grau_escolaridade"],2,",","."));
				$ob_pdf->Text(70, $linha + 10, "-----------------------------------------------");
				$ob_pdf->Text(70, $linha + 13, "MÉDIA (M1): ".number_format($superior["grau_1"],2,",","."));
				$ob_pdf->Text(70, $linha + 20, "Competências Específicas: ".number_format($superior["grau_especific"],2,",","."));
				$ob_pdf->Text(70, $linha + 23, "Responsabilidades: ".number_format($superior["grau_responsabilidade"],2,",","."));
				$ob_pdf->Text(70, $linha + 25, "-----------------------------------------------");
				$ob_pdf->Text(70, $linha + 28, "MÉDIA (M2): ".number_format($superior["grau_2"],2,",","."));
				$ob_pdf->SetFont('segoeuib','', 8);
				$ob_pdf->Text(70, $linha + 35, "Resultado: ".number_format($superior["nota_final"],2,",","."));
				$ob_pdf->SetFont('segoeuil','', 6);
				$ob_pdf->Text(70, $linha + 38, "Resultado=40% de M1 + 60% de M2");
				
				if(count($comite['grau_2']) > 0)
				{
					$ob_pdf->SetFont('segoeuib','', 8);
					$ob_pdf->Text(130, $linha, "Resultado Comitê:");
					$ob_pdf->SetFont('segoeuil','', 8);
					$ob_pdf->Text(130, $linha + 5, "Competências Institucionais: ".number_format($comite["grau_institucional"],2,",","."));
					$ob_pdf->Text(130, $linha + 8, "Escolaridade: ".number_format($comite["grau_escolaridade"],2,",","."));
					$ob_pdf->Text(130, $linha + 10, "-----------------------------------------------");
					$ob_pdf->Text(130, $linha + 13, "MÉDIA (M1): ".number_format($comite["grau_1"],2,",","."));
					$ob_pdf->Text(130, $linha + 20, "Competências Específicas: ".number_format($comite["grau_especific"],2,",","."));
					$ob_pdf->Text(130, $linha + 23, "Responsabilidades: ".number_format($comite["grau_responsabilidade"],2,",","."));
					$ob_pdf->Text(130, $linha + 25, "-----------------------------------------------");
					$ob_pdf->Text(130, $linha + 28, "MÉDIA (M2): ".number_format($comite["grau_2"],2,",","."));
					$ob_pdf->SetFont('segoeuib','', 8);
					$ob_pdf->Text(130, $linha + 35, "Resultado: ".number_format($comite["nota_final"],2,",","."));
					$ob_pdf->SetFont('segoeuil','', 6);
					$ob_pdf->Text(130, $linha + 38, "Resultado=40% de M1 + 60% de M2");
				}
				
				$ob_pdf->SetY($linha + 38);
				
				if(count($expectativas) > 0)
				{
					$ob_pdf->SetY($ob_pdf->GetY() + 2);
					$ob_pdf->SetLineWidth(0);
					$ob_pdf->Line(10,$ob_pdf->GetY(),200,$ob_pdf->GetY());	
					$ob_pdf->SetY($ob_pdf->GetY() + 2);			
					
					$ob_pdf->SetFont('segoeuib','', 10);
					$ob_pdf->SetY($ob_pdf->GetY() + 3);
					$ob_pdf->Text(10, $ob_pdf->GetY(), "Expectativas:");					
					#$ob_pdf->MultiCell(0, 5, "Expectativas:", 0, 1);
					
					$ob_pdf->SetLineWidth(0);
					$ob_pdf->SetDrawColor(0,0,0);
					$ob_pdf->SetWidths(array(40, 150));
					$ob_pdf->SetAligns(array('L','L'));
					$ob_pdf->SetFont('segoeuil', '', 10 );
					$ob_pdf->SetY($ob_pdf->GetY() + 2);
					
					$idx = 0;
					foreach($expectativas as $expectativa)
					{
						if($idx!=0)
						{
							$ob_pdf->SetY($ob_pdf->GetY() + 2);
						}
							
						$ob_pdf->Row(array("Competências",$expectativa['aspecto']));	
						$ob_pdf->Row(array("Resultados esperados",$expectativa['resultado_esperado']));	
						$ob_pdf->Row(array("Ações",$expectativa['acao']));	

						$idx++;
					}
				}
				
				$ob_pdf->SetY($ob_pdf->GetY() + 2);
				$ob_pdf->SetLineWidth(0);
				$ob_pdf->Line(10,$ob_pdf->GetY(),200,$ob_pdf->GetY());	
				$ob_pdf->SetY($ob_pdf->GetY() + 10);	
				
				$ob_pdf->SetFont('segoeuil','', 10);
				$ob_pdf->Text(10,$ob_pdf->GetY(),"Porto Alegre, ".date("d")." de ".mes_extenso()." de ".date("Y").".");		
				
				$ob_pdf->SetY($ob_pdf->GetY() + 8);
				
				$ob_pdf->SetFont('segoeuil','', 12);
				$ob_pdf->Text(10,$ob_pdf->GetY(), "( ".($item['fl_acordo'] == "A" ? "X" : " ")." )");
				$ob_pdf->Text(10,$ob_pdf->GetY()+6,  "( ".($item['fl_acordo'] == "C" ? "X" : " ")." )");

				$ob_pdf->SetFont('segoeuil','', 12);
				$ob_pdf->Text(25,$ob_pdf->GetY(), "Concordo com o resultado da avaliação (houve consenso)");
				$ob_pdf->Text(25,$ob_pdf->GetY()+6,  "Estou ciente do resultado da avaliação (não houve consenso)");
				
				$ob_pdf->SetY($ob_pdf->GetY() + 25);	
				
				$ob_pdf->SetLineWidth(0);
				$ob_pdf->Line(10,$ob_pdf->GetY(),95,$ob_pdf->GetY());	
				$ob_pdf->Line(120,$ob_pdf->GetY(),200,$ob_pdf->GetY());	
				$ob_pdf->SetY($ob_pdf->GetY() + 4);
				$ob_pdf->Text(10,$ob_pdf->GetY(),$item['nome_avaliado']);
				$ob_pdf->Text(120,$ob_pdf->GetY(),$item['nome_avaliador']);
				
			}
			
			$ob_pdf->Output();
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        } 
	}
	
	public function resumo($cd_avaliacao_capa = 0)
	{
		if(gerencia_in(array('GAD')) AND $this->session->userdata('indic_09') == "*")
		{
            $args = Array();
			$data = Array();
			$result = null;
			
            $args['ano']                          = $this->input->post("ano", TRUE);   
			$args['cd_usuario_avaliado_gerencia'] = $this->input->post("cd_usuario_avaliado_gerencia", TRUE);   
			$args['cd_usuario_avaliado']          = $this->input->post("cd_usuario_avaliado", TRUE);   
			$args['tipo_promocao']                = $this->input->post("tipo_promocao", TRUE);   
			$args['fl_promocao']                  = $this->input->post("fl_promocao", TRUE);   
			$args['cd_avaliacao_capa']            = $cd_avaliacao_capa; 
			
			if(intval($args['cd_avaliacao_capa']) > 0)
			{
				$args['ano']                          = '';   
				$args['cd_usuario_avaliado_gerencia'] = '';   
				$args['cd_usuario_avaliado']          = '';   
				$args['tipo_promocao']                = '';   
			}
			
			$this->avaliacao_relatorio_model->listar($result, $args);
			$collection = $result->result_array();
			
			$this->load->plugin('fpdf');
			
			$ob_pdf = new PDF('P', 'mm', 'A4');
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');			
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->header_titulo = true;
			$ob_pdf->header_titulo_texto = "Resumo Avaliação";
			
			$ob_pdf->SetFont('segoeuil', '', 12);
			
			#echo "<PRE>".print_r($collection,true)."</PRE>"; exit;
			
			foreach($collection as $item)
			{
				$args['cd_avaliacao_capa'] = $item['cd_avaliacao_capa'];
				
				$this->avaliacao_relatorio_model->avaliacao_comite($result, $args);
				$ar_avaliacao_comite = $result->result_array();
				$ar_comite = Array();
				foreach($ar_avaliacao_comite as $ar_tmp)
				{
					$ar_comite[] = $ar_tmp["nome"]." (".(intval($ar_tmp["ja_avaliou"]) == 1 ? "avaliou" : "não avaliou").")";
				}
				$integrantes_comite = implode(", ",$ar_comite);
				#echo $integrantes_comite;echo "<PRE>".print_r($ar_avaliacao_comite,true)."</PRE>"; exit;
				
				$args['tipo'] = 'A';
				$this->avaliacao_relatorio_model->resultado($result, $args);
				$avaliado = $result->row_array();
				
				$args['tipo'] = 'S';
				$this->avaliacao_relatorio_model->resultado($result, $args);
				$superior = $result->row_array();
				
				$args['tipo'] = 'C';
				$this->avaliacao_relatorio_model->resultado($result, $args);
				$comite = $result->row_array();
				
				$this->avaliacao_relatorio_model->expectativas($result, $args);
				$expectativas = $result->result_array();
			
				$ob_pdf->AddPage();
				
				#### CABEÇALHO ####
				$ob_pdf->SetXY(10, $ob_pdf->GetY() + 1);
				$ob_pdf->SetLineWidth(0.1);
				$ob_pdf->SetDrawColor(0,0,0);
				
				$ob_pdf->SetFont('segoeuib','', 14);
				$ob_pdf->MultiCell(190, 7, $item['nome_avaliado'], 1, 'L');
				
				$ob_pdf->SetWidths(array(50,140));
				$ob_pdf->SetAligns(array('L','L'));
				$ob_pdf->SetFont('segoeuib','', 9);
				$ob_pdf->Row(array("Ano:",           $item['dt_periodo']));
				$ob_pdf->Row(array("Tipo:",          $item['tipo_promocao']));	
				$ob_pdf->SetFont('segoeuil','', 8);
				$ob_pdf->Row(array("RE:",            $item['re_avaliado']));						
				$ob_pdf->Row(array("Cargo:",         $item['nome_cargo']));						
				$ob_pdf->Row(array("Família:",       $item['nome_familia']));						
				$ob_pdf->Row(array("Avaliador:",     $item['nome_avaliador']." - ".$item['re_avaliador']));						
				$ob_pdf->Row(array("Comitê:",        $integrantes_comite));						
				$ob_pdf->Row(array("Finalizada em:", $item['dt_publicacao']));						
				
				#### AVALIADO ####
				if(intval($avaliado['nota_final']) > 0)
				{				
					$ob_pdf->SetXY(10, $ob_pdf->GetY() + 3);
					
					$ob_pdf->SetFont('segoeuib','', 11);
					$ob_pdf->MultiCell(190, 5, "Resultado do Avaliado", 1, 'L');
					$ob_pdf->SetWidths(array(165,25)); 
					$ob_pdf->SetAligns(array('L','R'));

					$ob_pdf->SetFont('segoeuil','', 9);
					$ob_pdf->Row(array("Competências Institucionais: ",        number_format($avaliado["grau_institucional"],2,",",".")));			
					$ob_pdf->Row(array("Escolaridade: ",                       number_format($avaliado["grau_escolaridade"],2,",",".")));			
					$ob_pdf->SetFont('segoeuib','', 9);                       
					$ob_pdf->Row(array("Média: (M1)",                          number_format($avaliado['grau_1'] ,2,",",".")));			
				
				
					$ob_pdf->SetFont('segoeuil','', 9);
					$ob_pdf->Row(array("Competências Específicas: ",           number_format($avaliado["grau_especific"],2,",",".")));			
					$ob_pdf->Row(array("Responsabilidades: ",                  number_format($avaliado["grau_responsabilidade"],2,",",".")));			
					$ob_pdf->SetFont('segoeuib','', 9);
					$ob_pdf->Row(array("Média: (M2)",                          number_format($avaliado['grau_2'] ,2,",",".")));				
				
					$ob_pdf->SetFont('segoeuib','', 10.5);
					$ob_pdf->Row(array("Resultado: (40% de M1) + (60% de M2)", number_format($avaliado['nota_final'] ,2,",",".")));	
				}
			
				#### AVALIADOR/SUPERIOR ####
				if(intval($superior['nota_final']) > 0)
				{					
					$ob_pdf->SetXY(10, $ob_pdf->GetY() + 3);
					
					$ob_pdf->SetFont('segoeuib','', 11);
					$ob_pdf->MultiCell(190, 5, "Resultado do Superior", 1, 'L');
					$ob_pdf->SetWidths(array(165,25)); 
					$ob_pdf->SetAligns(array('L','R'));

					$ob_pdf->SetFont('segoeuil','', 9);
					$ob_pdf->Row(array("Competências Institucionais: ",        number_format($superior["grau_institucional"],2,",",".")));			
					$ob_pdf->Row(array("Escolaridade: ",                       number_format($superior["grau_escolaridade"],2,",",".")));			
					$ob_pdf->SetFont('segoeuib','', 9);                       
					$ob_pdf->Row(array("Média: (M1)",                          number_format($superior['grau_1'] ,2,",",".")));			
				
				
					$ob_pdf->SetFont('segoeuil','', 9);
					$ob_pdf->Row(array("Competências Específicas: ",           number_format($superior["grau_especific"],2,",",".")));			
					$ob_pdf->Row(array("Responsabilidades: ",                  number_format($superior["grau_responsabilidade"],2,",",".")));			
					$ob_pdf->SetFont('segoeuib','', 9);
					$ob_pdf->Row(array("Média: (M2)",                          number_format($superior['grau_2'] ,2,",",".")));				
				
					$ob_pdf->SetFont('segoeuib','', 10.5);
					$ob_pdf->Row(array("Resultado: (40% de M1) + (60% de M2)", number_format($superior['nota_final'] ,2,",",".")));				
				}
			
				#### COMITE ####
				if(intval($comite['nota_final']) > 0)
				{
					$ob_pdf->SetXY(10, $ob_pdf->GetY() + 3);
					
					$ob_pdf->SetFont('segoeuib','', 11);
					$ob_pdf->MultiCell(190, 5, "Resultado do Comitê", 1, 'L');
					$ob_pdf->SetWidths(array(165,25)); 
					$ob_pdf->SetAligns(array('L','R'));

					$ob_pdf->SetFont('segoeuil','', 9);
					$ob_pdf->Row(array("Competências Institucionais: ",        number_format($comite["grau_institucional"],2,",",".")));			
					$ob_pdf->Row(array("Escolaridade: ",                       number_format($comite["grau_escolaridade"],2,",",".")));			
					$ob_pdf->SetFont('segoeuib','', 9);                       
					$ob_pdf->Row(array("Média: (M1)",                          number_format($comite['grau_1'] ,2,",",".")));			
				
				
					$ob_pdf->SetFont('segoeuil','', 9);
					$ob_pdf->Row(array("Competências Específicas: ",           number_format($comite["grau_especific"],2,",",".")));			
					$ob_pdf->Row(array("Responsabilidades: ",                  number_format($comite["grau_responsabilidade"],2,",",".")));			
					$ob_pdf->SetFont('segoeuib','', 9);
					$ob_pdf->Row(array("Média: (M2)",                          number_format($comite['grau_2'] ,2,",",".")));				
				
					$ob_pdf->SetFont('segoeuib','', 10.5);
					$ob_pdf->Row(array("Resultado: (40% de M1) + (60% de M2)", number_format($comite['nota_final'] ,2,",",".")));				
				}
			
				if(count($expectativas) > 0)
				{
					$ob_pdf->SetXY(10, $ob_pdf->GetY() + 3);
					
					$ob_pdf->SetFont('segoeuib','', 11);
					$ob_pdf->MultiCell(190, 5, "Expectativas", 1, 'L');
					
					$ob_pdf->SetLineWidth(0.1);
					$ob_pdf->SetDrawColor(0,0,0);
					
					$ob_pdf->SetWidths(array(63.5, 63.5, 63));
					$ob_pdf->SetAligns(array('L','L','L'));
					
					$ob_pdf->SetFont('segoeuib','', 8);
					$ob_pdf->Row(array("Competências","Resultados esperados","Ações"));
					
					$ob_pdf->SetFont('segoeuil', '', 8);
					foreach($expectativas as $expectativa)
					{
						$ob_pdf->Row(array($expectativa['aspecto'],$expectativa['resultado_esperado'],$expectativa['acao']));	
					}					
				}
			}
			
			$ob_pdf->Output();
		}
        else
        {
            exibir_mensagem("ACESSO NÃO PERMITIDO");
        } 
	}	
}

?>