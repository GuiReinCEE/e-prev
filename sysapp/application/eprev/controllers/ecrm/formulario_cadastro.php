<?php
class Formulario_cadastro extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    private function get_permissao()
    {
        if(gerencia_in(array('GRSC')))
        {
            return TRUE;
        }
        #Vanessa dos Santos Dornelles
        else if($this->session->userdata('codigo') == 146)
        {
            return TRUE;
        }
        #Luciano Rodriguez
        else if($this->session->userdata('codigo') == 251)
        {
            return TRUE;
        }
        #Julia Graciely Goncalves dos Santos
        else if($this->session->userdata('codigo') == 384)
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    public function index()
    {        
        if($this->get_permissao())
        {
            $this->load->view('ecrm/formulario_cadastro/index.php');
        }
        else 
        {
            exibir_mensagem('ACESSO NУO PERMITIDO');
        }
    }

    public function gera($cd_plano = 0, $cd_empresa = 0, $cd_registro_empregado = 0, $seq_dependencia = 0)
    {
        $this->load->model('public/formulario_cadastro_model');

        $args = array(
            'cd_empresa' => $cd_empresa,
            'cd_plano'   => $cd_plano
        );

        if($cd_registro_empregado > 0)
        {
            $count = $this->formulario_cadastro_model->verifica_plano_patrocinadora($args);
            
            if($count['count'] == 0)
            {
                exibir_mensagem('Esta empresa ('.$cd_empresa.') nуo possue esse plano ('.intval($cd_plano).').');
            }
        } 

        switch ($args['cd_plano'])
        {
            /*
            case 1:
                if($args['cd_empresa'] == 3)
                {
                    $this->cgtee($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                }
                break;
                */
            case 2:
                $this->ceeeprev($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;
            case 6:
                $this->crmprev($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;
            case 7:
                $this->senge($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;
            case 9:
                $this->familia($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;
            case 10:
                $this->municipio($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;
            case 11:
                $this->ieabprev($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;
                /*
            case 8:
                $this->sinpro($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;
            
                
            case 21:
                $this->inpel($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;	
            */
            case 21:
                $this->familiacorporativo($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;  
            case 22:
                $this->ceran($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break; 
            case 23:
                $this->foz($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;     		
        }
    }
    
	public function familiaFichaInscricao($cd_cadastro = "", $tp_cadastro = "")
    {
        #### FORMULARIO DA AREA CORPORATIVA DO PLANO FAMILIA ####
		$this->load->model('public/formulario_cadastro_model');
		
		$this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetMargins(10, 14, 5); 		
        $ob_pdf->AddPage();  

		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/familia_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
		
        if((trim($cd_cadastro) != "") and (in_array($tp_cadastro, array("C","D"))))
        {
            $args['cd_cadastro'] = $cd_cadastro;
            $args['tp_cadastro'] = $tp_cadastro;
            
            $row = $this->formulario_cadastro_model->familiaFichaInscricao($args); 

			if(count($row) > 1)
			{
				$ob_pdf->SetFont('segoeuil','',11);
				$ob_pdf->Text(20,61,trim($row['nome']) );
				$ob_pdf->Text(20,71, trim($row['dt_nascimento']));         
				$ob_pdf->Text(182,71,(trim($row['fl_sexo']) == 'F' ? 'X' : '') );
				$ob_pdf->Text(192.3,71,(trim($row['fl_sexo']) == 'M' ? 'X' : ''));
				$ob_pdf->Text(20,81,trim($row['cpf']));
				$ob_pdf->Text(20,106,trim($row['endereco']));
				$ob_pdf->Text(180,106, trim($row['complemento']));
				$ob_pdf->Text(20,114,trim($row['bairro']));
				$ob_pdf->Text(70,114,trim($row['cidade']));
				$ob_pdf->Text(157,114, trim($row['uf']));
				$ob_pdf->Text(172,114, trim($row['cep']));
				$ob_pdf->Text(20,123, trim($row['telefone_1']));
				$ob_pdf->Text(90,123, trim($row['telefone_2']));
				if(trim($row['email_1']) != '')
				{
					$e_mail = trim($row['email_1']);
				}
				else if(trim($row['email_2']) != '')
				{
					$e_mail = trim($row['email_2']);
				}
				else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
				{
					$e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
				}
				else
				{
					$e_mail = '';
				}     
				$ob_pdf->Text(20,131, trim($e_mail));
			}
        }
				
		$ob_pdf->AddPage();
		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/familia_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        $ob_pdf->Output();
    }	
	
    public function familia($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('public/formulario_cadastro_model');
		
		$this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetMargins(10, 14, 5); 		
        $ob_pdf->AddPage();  

		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/familia_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
		
        if(intval($cd_registro_empregado) > 0)
        {
            $args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $row = $this->formulario_cadastro_model->get_campos_pdf($args); 

			$ob_pdf->SetFont('segoeuil','',11);
            $ob_pdf->Text(15,54,trim($row['nome']) );
            $ob_pdf->Text(15,73, trim($row['dt_nascimento']));         
            $ob_pdf->Text(182.5,73,(trim($row['fl_sexo']) == 'F' ? 'X' : '') );//F
            $ob_pdf->Text(192.8,73,(trim($row['fl_sexo']) == 'M' ? 'X' : ''));
            
            switch ($row['cd_estado_civil'])
            {
                case 1:
                    $ob_pdf->Text(72,74,'X');
                    break;
                case 2:
                    $ob_pdf->Text(91.5,74,'X');
                    break;
                case 4:
                    $ob_pdf->Text(156.5,74,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(132,74,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(110,74,'X');
                    break;          
            }   

            $ob_pdf->Text(15,84,trim($row['cpf_mf']));

            $ob_pdf->Text(15,109.5,trim($row['endereco']));
          
            $ob_pdf->Text(150,109.5, intval($row['nr_endereco']));
            $ob_pdf->Text(180,109.5, intval($row['complemento_endereco']));
            
            $ob_pdf->Text(15,117.5,trim($row['bairro']));
            $ob_pdf->Text(65,117.5,trim($row['cidade']));
            $ob_pdf->Text(156,117.5, trim($row['unidade_federativa']));
            $ob_pdf->Text(170,117.5, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(15,125.5, trim($row['telefone_1']));
            $ob_pdf->Text(80,125.5, trim($row['telefone_2']));
            if(trim($row['email_1']) != '')
            {
                $e_mail = trim($row['email_1']);
            }
            else if(trim($row['email_2']) != '')
            {
                $e_mail = trim($row['email_2']);
            }
            else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
            {
                $e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
            }
            else
            {
                $e_mail = '';
            }     
            $ob_pdf->Text(15,134, trim($e_mail));
           
        }
				
		$ob_pdf->AddPage();
		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/familia_verso_01.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/familia_verso_02.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);   

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/familia_termo_01.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/familia_termo_02.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/familia_termo_03.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  
        
        $ob_pdf->Output();
    }
    
    public function cgtee($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('public/formulario_cadastro_model');
		
		$this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetMargins(10, 14, 5); 		
        $ob_pdf->AddPage();
		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/cgtee_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        if(intval($cd_registro_empregado) > 0)
        {
           $args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $row = $this->formulario_cadastro_model->get_campos_pdf($args);            
            
            $ob_pdf->SetFont('segoeuil','',11);
            $ob_pdf->Text(22,39.4, trim($row['cd_registro_empregado']));
            $ob_pdf->Text(140,39.4, trim($row['dt_admissao_dia']));	
            $ob_pdf->Text(149,39.4, trim($row['dt_admissao_mes']));
            $ob_pdf->Text(157.5,39.4, trim($row['dt_admissao_ano']));
            $ob_pdf->Text(20,58, trim($row['nome']));
            $ob_pdf->Text(20,67.5, trim($row['dt_nascimento']));       
            $ob_pdf->Text(182,67.5, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(192.3,67.5, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));

            switch ($row['cd_estado_civil'])
            {
                case 1:
                    $ob_pdf->Text(71.2,67.5,'X');
                    break;
                case 2:
                    $ob_pdf->Text(91,67.5,'X');
                    break;
                case 4:
                    $ob_pdf->Text(155.5,67.5,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(131.5,67.5,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(109,67.5,'X');
                    break;          
            }   

            $ob_pdf->Text(20,77, trim($row['cpf_mf']));
            $ob_pdf->Text(20,102.4, trim($row['endereco']));
            $ob_pdf->Text(150,102.4, intval($row['nr_endereco']));
            $ob_pdf->Text(180,102.4, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,111, trim($row['bairro']));
            $ob_pdf->Text(65,111, trim($row['cidade']));
            $ob_pdf->Text(156.2,111, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,111,  trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,119.2, trim($row['telefone_1']));
            $ob_pdf->Text(81,119.2, trim($row['telefone_2'])); 
            if(trim($row['email_1']) != '')
            {
                $e_mail = trim($row['email_1']);
            }
            else if(trim($row['email_2']) != '')
            {
                $e_mail = trim($row['email_2']);
            }
            else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
            {
                $e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
            }
            else
            {
                $e_mail = '';
            }               
            $ob_pdf->Text(20,136.2, trim($e_mail));
            $ob_pdf->Text(20,127.8, trim($row['descricao_grau_instrucao']));
        }
		
        $ob_pdf->AddPage();
		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/cgtee_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
                
        $ob_pdf->Output();
    }
    
    public function crmprev($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('public/formulario_cadastro_model');
		
		$this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetMargins(10, 14, 5); 		
        $ob_pdf->AddPage();
		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/crmprev_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
		if(intval($cd_registro_empregado) > 0)
        {
            $args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $row = $this->formulario_cadastro_model->get_campos_pdf($args);            
            
            $ob_pdf->SetFont('segoeuil','',11);
            $ob_pdf->Text(23.5,45.4, trim($args['cd_empresa']));
            $ob_pdf->Text(80,45.4, trim($row['cd_registro_empregado']));
            $ob_pdf->Text(170,45.4, trim($row['dt_admissao_dia']));
            $ob_pdf->Text(182,45.4, trim($row['dt_admissao_mes']));
            $ob_pdf->Text(192.5,45.4, trim($row['dt_admissao_ano']));

            $ob_pdf->Text(20,64, trim($row['nome']));

            $ob_pdf->Text(20,87, trim($row['dt_nascimento']));      
            $ob_pdf->Text(183,87, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(193.3,87, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));


            switch ($row['cd_estado_civil'])
            {
                case 1:
                    $ob_pdf->Text(73.2,87,'X');
                    break;
                case 2:
                    $ob_pdf->Text(93,87,'X');
                    break;
                case 4:
                    $ob_pdf->Text(157.5,87,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(133.5,87,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(111,87,'X');
                    break;          
            }   

            $ob_pdf->Text(20,95.5, trim($row['cpf_mf']));
            $ob_pdf->Text(20,121.5, trim($row['endereco']));
            $ob_pdf->Text(150,121.5, intval($row['nr_endereco']));
            $ob_pdf->Text(180,121.5, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,130, trim($row['bairro']));
            $ob_pdf->Text(65,130, trim($row['cidade']));
            $ob_pdf->Text(156.2,130, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,130, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,138.5, trim($row['telefone_1']));
            $ob_pdf->Text(81,138.5, trim($row['telefone_2']));
            if(trim($row['email_1']) != '')
            {
                $e_mail = trim($row['email_1']);
            }
            else if(trim($row['email_2']) != '')
            {
                $e_mail = trim($row['email_2']);
            }
            else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
            {
                $e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
            }
            else
            {
                $e_mail = '';
            }              
            $ob_pdf->Text(20,147, trim($e_mail));
            //$ob_pdf->Text(20,142.5, trim($row['descricao_grau_instrucao']));     
        }
		
        $ob_pdf->AddPage();
		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/crmprev_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);   


        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/crmprev_termo_01.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/crmprev_termo_02.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/crmprev_termo_03.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->Output();
    }
    
    public function ceeeprev($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('public/formulario_cadastro_model');
		
		$this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetMargins(10, 14, 5); 		
        $ob_pdf->AddPage();
		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/ceeeprev_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        if(intval($cd_registro_empregado) > 0)
        {
            $args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $row = $this->formulario_cadastro_model->get_campos_pdf($args);            
            
             $ob_pdf->SetFont('segoeuil','',11);
            $ob_pdf->Text(23,41.2,  trim($row['cd_registro_empregado']));
            $ob_pdf->Text(140,41.2, trim($row['dt_admissao_dia']));
            $ob_pdf->Text(149,41.2, trim($row['dt_admissao_mes']));
            $ob_pdf->Text(158.5,41.2, trim($row['dt_admissao_ano']));
            $ob_pdf->Text(20,60, trim($row['nome']) );
            $ob_pdf->Text(20,69.2, trim($row['dt_nascimento']));     
            $ob_pdf->Text(182,69.5, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(192.3,69.5, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));

            switch ($row['cd_estado_civil'])
            {
                case 1:
                    $ob_pdf->Text(71.2,69.5,'X');
                    break;
                case 2:
                    $ob_pdf->Text(91,69.5,'X');
                    break;
                case 4:
                    $ob_pdf->Text(155.5,69.5,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(131.5,69.5,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(109,69.5,'X');
                    break;          
            }   

            $ob_pdf->Text(20,78, trim($row['cpf_mf']));
            $ob_pdf->Text(20,104.5, trim($row['endereco']));
            $ob_pdf->Text(150,104.5, intval($row['nr_endereco']));
            $ob_pdf->Text(180,104.5, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,113, trim($row['bairro']));
            $ob_pdf->Text(65,113, trim($row['cidade']));
            $ob_pdf->Text(156.2,113, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,113, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,121.5, trim($row['telefone_1']));
            $ob_pdf->Text(81,121.5, trim($row['telefone_2'])); 

            $ob_pdf->Text(20,129.8, trim($row['descricao_grau_instrucao'])); 
            
            if(trim($row['email_1']) != '')
            {
                $e_mail = trim($row['email_1']);
            }
            else if(trim($row['email_2']) != '')
            {
                $e_mail = trim($row['email_2']);
            }
            else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
            {
                $e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
            }
            else
            {
                $e_mail = '';
            }     

            $ob_pdf->Text(20,138.5, trim($e_mail));         
        }
		
        $ob_pdf->AddPage();
		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/ceeeprev_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
                
        $ob_pdf->Output();
    }
	
    public function familiacorporativo($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('public/formulario_cadastro_model');
  
        $this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetMargins(10, 14, 5);         
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/familiacorporativo_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        if(intval($cd_registro_empregado) > 0)
        {
            $args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $row = $this->formulario_cadastro_model->get_campos_pdf($args);            
            
            $ob_pdf->SetFont('segoeuil','',11);
            $ob_pdf->Text(23.5,40, trim($args['cd_empresa']));
            $ob_pdf->Text(80,40, trim($row['cd_registro_empregado']));
            $ob_pdf->Text(170,40, trim($row['dt_admissao_dia']));
            $ob_pdf->Text(180,40, trim($row['dt_admissao_mes']));
            $ob_pdf->Text(190,40, trim($row['dt_admissao_ano']));

            $ob_pdf->Text(20,59.5, trim($row['nome']));

            $ob_pdf->Text(20,84.5, trim($row['dt_nascimento']));      
            $ob_pdf->Text(183,84.5, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(193.3,84.5, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));


            switch ($row['cd_estado_civil'])
            {
                case 1:
                    $ob_pdf->Text(73.2,84.5,'X');
                    break;
                case 2:
                    $ob_pdf->Text(93,84.5,'X');
                    break;
                case 4:
                    $ob_pdf->Text(157.5,84.5,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(133.5,84.5,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(111,84.5,'X');
                    break;          
            }   

            $ob_pdf->Text(20,92.5, trim($row['cpf_mf']));
            $ob_pdf->Text(20,119.5, trim($row['endereco']));
            $ob_pdf->Text(150,119.5, intval($row['nr_endereco']));
            $ob_pdf->Text(180,119.5, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,128, trim($row['bairro']));
            $ob_pdf->Text(65,128, trim($row['cidade']));
            $ob_pdf->Text(156.2,128, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,128, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,136, trim($row['telefone_1']));
            $ob_pdf->Text(81,136, trim($row['telefone_2']));
            if(trim($row['email_1']) != '')
            {
                $e_mail = trim($row['email_1']);
            }
            else if(trim($row['email_2']) != '')
            {
                $e_mail = trim($row['email_2']);
            }
            else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
            {
                $e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
            }
            else
            {
                $e_mail = '';
            }              
            $ob_pdf->Text(20,145, trim($e_mail));
            //$ob_pdf->Text(20,142.5, trim($row['descricao_grau_instrucao']));     
        }
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/familiacorporativo_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);   

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/familiacorporativo_termo_01.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/familiacorporativo_termo_02.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/familiacorporativo_termo_03.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->Output();
    }	
	
    public function ceran($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('public/formulario_cadastro_model');
        
        $this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetMargins(10, 14, 5);         
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/ceran_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        if(intval($cd_registro_empregado) > 0)
        {
            $args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $row = $this->formulario_cadastro_model->get_campos_pdf($args);            
            
            $ob_pdf->SetFont('segoeuil','',11);
            $ob_pdf->Text(23.5,36.5, trim($args['cd_empresa']));


            $ob_pdf->Text(95,36.5, trim($row['cd_registro_empregado']));  

            $ob_pdf->Text(167,36.5, trim($row['dt_admissao_dia']));
            $ob_pdf->Text(175,36.5, trim($row['dt_admissao_mes']));
            $ob_pdf->Text(183.5,36.5, trim($row['dt_admissao_ano']));

            $ob_pdf->Text(20,55, trim($row['nome']));
            $ob_pdf->Text(20,78, trim($row['dt_nascimento']));      
            $ob_pdf->Text(183,78, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(193.3,78, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));


            switch ($row['cd_estado_civil'])
            {
                case 1:
                    $ob_pdf->Text(73.2,78,'X');
                    break;
                case 2:
                    $ob_pdf->Text(93,78,'X');
                    break;
                case 4:
                    $ob_pdf->Text(157.5,78,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(133.5,78,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(111,78,'X');
                    break;          
            }   

            $ob_pdf->Text(20,87.5, trim($row['cpf_mf']));
            $ob_pdf->Text(20,113.5, trim($row['endereco']));
            $ob_pdf->Text(150,113.5, intval($row['nr_endereco']));
            $ob_pdf->Text(180,113.5, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,121.5, trim($row['bairro']));
            $ob_pdf->Text(65,121.5, trim($row['cidade']));
            $ob_pdf->Text(156.2,121.5, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,121.5, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,130.5, trim($row['telefone_1']));
            $ob_pdf->Text(81,130.5, trim($row['telefone_2']));
            if(trim($row['email_1']) != '')
            {
                $e_mail = trim($row['email_1']);
            }
            else if(trim($row['email_2']) != '')
            {
                $e_mail = trim($row['email_2']);
            }
            else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
            {
                $e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
            }
            else
            {
                $e_mail = '';
            }              
            $ob_pdf->Text(20,147, trim($e_mail));
            //$ob_pdf->Text(20,142.5, trim($row['descricao_grau_instrucao']));     
        }
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/ceran_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);   


        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/ceran_termo_01.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/ceran_termo_02.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/ceran_termo_03.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->Output();
    }

    public function foz($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('public/formulario_cadastro_model');
        
        $this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetMargins(10, 14, 5);         
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/foz_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        if(intval($cd_registro_empregado) > 0)
        {
            $args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $row = $this->formulario_cadastro_model->get_campos_pdf($args);            
            
            $ob_pdf->SetFont('segoeuil','',11);
            $ob_pdf->Text(23.5,37, trim($args['cd_empresa']));


            $ob_pdf->Text(90,37, trim($row['cd_registro_empregado']));  

            $ob_pdf->Text(167,37, trim($row['dt_admissao_dia']));
            $ob_pdf->Text(175.5,37, trim($row['dt_admissao_mes']));
            $ob_pdf->Text(186,37, trim($row['dt_admissao_ano']));

            $ob_pdf->Text(20,55, trim($row['nome']));
            $ob_pdf->Text(20,81, trim($row['dt_nascimento']));      
            $ob_pdf->Text(183,81, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(193.3,81, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));


            switch ($row['cd_estado_civil'])
            {
                case 1:
                    $ob_pdf->Text(73.2,81,'X');
                    break;
                case 2:
                    $ob_pdf->Text(93,81,'X');
                    break;
                case 4:
                    $ob_pdf->Text(157.5,81,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(133.5,81,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(111,81,'X');
                    break;          
            }   

            $ob_pdf->Text(20,89.5, trim($row['cpf_mf']));
            $ob_pdf->Text(20,116.5, trim($row['endereco']));
            $ob_pdf->Text(150,116.5, intval($row['nr_endereco']));
            $ob_pdf->Text(180,116.5, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,124.5, trim($row['bairro']));
            $ob_pdf->Text(65,124.5, trim($row['cidade']));
            $ob_pdf->Text(156.2,124.5, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,124.5, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,133, trim($row['telefone_1']));
            $ob_pdf->Text(81,133, trim($row['telefone_2']));
            if(trim($row['email_1']) != '')
            {
                $e_mail = trim($row['email_1']);
            }
            else if(trim($row['email_2']) != '')
            {
                $e_mail = trim($row['email_2']);
            }
            else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
            {
                $e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
            }
            else
            {
                $e_mail = '';
            }              
            $ob_pdf->Text(20,150, trim($e_mail));
            //$ob_pdf->Text(20,142.5, trim($row['descricao_grau_instrucao']));     
        }
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/foz_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);   


        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/foz_termo_01.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/foz_termo_02.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/foz_termo_03.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->Output();
    }

    public function sengeFichaInscricao($cd_cadastro = "")
    {
        #### FORMULARIO DA AREA CORPORATIVA DO PLANO SENGE ####
		
		$this->load->model('public/formulario_cadastro_model');
        
        $this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib'); 
        $ob_pdf->SetMargins(10, 14, 5);       
        $ob_pdf->AddPage();
                
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/senge_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        if(trim($cd_cadastro) != "")
        {
            $args['cd_cadastro'] = $cd_cadastro;
			
            $row = $this->formulario_cadastro_model->sengeFichaInscricao($args); 

			if(count($row) > 1)
			{			
				$ob_pdf->SetFont('segoeuil','',11);
				$ob_pdf->Text(20,60.5, trim($row['nome']));
				$ob_pdf->Text(20,70, trim($row['dt_nascimento']));              
				$ob_pdf->Text(181.8,70, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
				$ob_pdf->Text(192,70, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));
				$ob_pdf->Text(20,79.5, trim($row['cpf']));
				$ob_pdf->Text(20,105, trim($row['endereco']));
				$ob_pdf->Text(180,105, trim($row['complemento']));
				$ob_pdf->Text(20,113.5, trim($row['bairro']));
				$ob_pdf->Text(65,113.5, trim($row['cidade']));
				$ob_pdf->Text(156.2,113.5, trim($row['uf']));
				$ob_pdf->Text(174,113.5, trim($row['cep']));
				$ob_pdf->Text(20,122, trim($row['telefone_1']));
				$ob_pdf->Text(81,122, trim($row['telefone_2']));
				if(trim($row['email_1']) != '')
				{
					$e_mail = trim($row['email_1']);
				}
				else if(trim($row['email_2']) != '')
				{
					$e_mail = trim($row['email_2']);
				}
				else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
				{
					$e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
				}
				else
				{
					$e_mail = '';
				}               
				$ob_pdf->Text(20,130.5, trim($e_mail));
			}
        }
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/senge_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        $ob_pdf->Output();
    }	
	
    public function senge($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('public/formulario_cadastro_model');
        
        $this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib'); 
        $ob_pdf->SetMargins(10, 14, 5);       
        $ob_pdf->AddPage();
                
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/senge_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        if(intval($cd_registro_empregado) > 0)
        {
            $args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $row = $this->formulario_cadastro_model->get_campos_pdf($args);          
                        
            $ob_pdf->SetFont('segoeuil','',11);
            //$ob_pdf->Text(122,39.5, trim($row['cd_registro_empregado']));  
            $ob_pdf->Text(20,53.5, trim($row['nome']));
            $ob_pdf->Text(20,78, trim($row['dt_nascimento']));              
            $ob_pdf->Text(181.8,78, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(192,78, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));

            switch (intval($row['cd_estado_civil']))
            {
                case 1:
                    $ob_pdf->Text(71.2,78,'X');
                    break;
                case 2:
                    $ob_pdf->Text(91,78,'X');
                    break;
                case 4:
                    $ob_pdf->Text(155.5,78,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(131.5,78,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(109,78,'X');
                    break;          
            }               

            $ob_pdf->Text(20,86, trim($row['cpf_mf']));
            $ob_pdf->Text(20,114, trim($row['endereco']));
            $ob_pdf->Text(150,114, intval($row['nr_endereco']));
            $ob_pdf->Text(180,114, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,123.5, trim($row['bairro']));
            $ob_pdf->Text(65,123.5, trim($row['cidade']));
            $ob_pdf->Text(156.2,123.5, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,123.5, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,133, trim($row['telefone_1']));
            $ob_pdf->Text(81,133, trim($row['telefone_2']));
            if(trim($row['email_1']) != '')
            {
                $e_mail = trim($row['email_1']);
            }
            else if(trim($row['email_2']) != '')
            {
                $e_mail = trim($row['email_2']);
            }
            else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
            {
                $e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
            }
            else
            {
                $e_mail = '';
            }               
            $ob_pdf->Text(20,141.5, trim($e_mail));
        }
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/senge_verso_01.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/senge_verso_02.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/senge_termo_01.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/senge_termo_02.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/senge_termo_03.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        $ob_pdf->Output();
    }

    public function sinproFichaInscricao($cd_cadastro = "")
    {
        #### FORMULARIO DA AREA CORPORATIVA DO PLANO SINPRORS ####
		
		$this->load->model('public/formulario_cadastro_model');
        
        $this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetMargins(10, 14, 5);        
        $ob_pdf->AddPage();
                
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/sinpro_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        if(trim($cd_cadastro) != "")
        {
            $args['cd_cadastro'] = $cd_cadastro;
			
            $row = $this->formulario_cadastro_model->sinproFichaInscricao($args); 

			if(count($row) > 1)
			{        
				$ob_pdf->SetFont('segoeuil','',11);
				$ob_pdf->Text(20,60.5, trim($row['nome']));
				$ob_pdf->Text(20,70.5, trim($row['dt_nascimento']));              
				$ob_pdf->Text(181.8,70.5, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
				$ob_pdf->Text(192,70.5, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));
				$ob_pdf->Text(20,80, trim($row['cpf']));
				$ob_pdf->Text(20,105.5, trim($row['endereco']));
				$ob_pdf->Text(180,105.5, trim($row['complemento']));
				$ob_pdf->Text(20,114, trim($row['bairro']));
				$ob_pdf->Text(65,114, trim($row['cidade']));
				$ob_pdf->Text(156.2,114, trim($row['uf']));
				$ob_pdf->Text(174,114, trim($row['cep']));
				$ob_pdf->Text(20,122.5, trim($row['telefone_1']));
				$ob_pdf->Text(81,122.5, trim($row['telefone_2'])); 
				if(trim($row['email_1']) != '')
				{
					$e_mail = trim($row['email_1']);
				}
				else if(trim($row['email_2']) != '')
				{
					$e_mail = trim($row['email_2']);
				}
				else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
				{
					$e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
				}
				else
				{
					$e_mail = '';
				}              
				$ob_pdf->Text(20,130.5, trim($e_mail));
			}
        }
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/sinpro_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        $ob_pdf->Output();
    }

    public function sinpro($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('public/formulario_cadastro_model');
        
        $this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetMargins(10, 14, 5);        
        $ob_pdf->AddPage();
                
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/sinpro_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        if(intval($cd_registro_empregado) > 0)
        {
            $args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $row = $this->formulario_cadastro_model->get_campos_pdf($args);          
                        
            $ob_pdf->SetFont('segoeuil','',11);
           // $ob_pdf->Text(122,39.5, trim($row['cd_registro_empregado'])); 
            $ob_pdf->Text(20,60.5, trim($row['nome']));
            $ob_pdf->Text(20,70.5, trim($row['dt_nascimento']));              
            $ob_pdf->Text(181.8,70.5, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(192,70.5, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));

            switch (intval($row['cd_estado_civil']))
            {
                case 1:
                    $ob_pdf->Text(71.2,70.5,'X');
                    break;
                case 2:
                    $ob_pdf->Text(91,70.5,'X');
                    break;
                case 4:
                    $ob_pdf->Text(155.5,70.5,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(131.5,70.5,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(109,70.5,'X');
                    break;          
            }               

            $ob_pdf->Text(20,80, trim($row['cpf_mf']));
            $ob_pdf->Text(20,105.5, trim($row['endereco']));
            $ob_pdf->Text(150,105.5, intval($row['nr_endereco']));
            $ob_pdf->Text(180,105.5, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,114, trim($row['bairro']));
            $ob_pdf->Text(65,114, trim($row['cidade']));
            $ob_pdf->Text(156.2,114, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,114, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,122.5, trim($row['telefone_1']));
            $ob_pdf->Text(81,122.5, trim($row['telefone_2'])); 
            if(trim($row['email_1']) != '')
            {
                $e_mail = trim($row['email_1']);
            }
            else if(trim($row['email_2']) != '')
            {
                $e_mail = trim($row['email_2']);
            }
            else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
            {
                $e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
            }
            else
            {
                $e_mail = '';
            }              
            $ob_pdf->Text(20,130.5, trim($e_mail));
        }
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/sinpro_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        $ob_pdf->Output();
    }

    public function municipio($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('public/formulario_cadastro_model');
  
        $this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetMargins(10, 14, 5);         
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/municipio_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        if(intval($cd_registro_empregado) > 0)
        {
            $args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $row = $this->formulario_cadastro_model->get_campos_pdf($args);            
            
            $ob_pdf->SetFont('segoeuil','',11);
            $ob_pdf->Text(23.5,45.4, trim($args['cd_empresa']));
            $ob_pdf->Text(80,45.4, trim($row['cd_registro_empregado']));
            $ob_pdf->Text(162,45.4, trim($row['dt_admissao_dia']));
            $ob_pdf->Text(172,45.4, trim($row['dt_admissao_mes']));
            $ob_pdf->Text(182.5,45.4, trim($row['dt_admissao_ano']));

            $ob_pdf->Text(20,64, trim($row['nome']));

            $ob_pdf->Text(20,95.5, trim($row['dt_nascimento']));      
            $ob_pdf->Text(183,95.5, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(193.3,95.5, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));


            switch ($row['cd_estado_civil'])
            {
                case 1:
                    $ob_pdf->Text(73.2,95.5,'X');
                    break;
                case 2:
                    $ob_pdf->Text(93,95.5,'X');
                    break;
                case 4:
                    $ob_pdf->Text(157.5,95.5,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(133.5,95.5,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(111,95.5,'X');
                    break;          
            }   

            $ob_pdf->Text(20,105.5, trim($row['cpf_mf']));
            $ob_pdf->Text(20,132.5, trim($row['endereco']));
            $ob_pdf->Text(150,132.5, intval($row['nr_endereco']));
            $ob_pdf->Text(180,132.5, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,142, trim($row['bairro']));
            $ob_pdf->Text(65,142, trim($row['cidade']));
            $ob_pdf->Text(156.2,142, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,142, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,151, trim($row['telefone_1']));
            $ob_pdf->Text(81,151, trim($row['telefone_2']));
            if(trim($row['email_1']) != '')
            {
                $e_mail = trim($row['email_1']);
            }
            else if(trim($row['email_2']) != '')
            {
                $e_mail = trim($row['email_2']);
            }
            else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
            {
                $e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
            }
            else
            {
                $e_mail = '';
            }              
            $ob_pdf->Text(20,160, trim($e_mail));
            //$ob_pdf->Text(20,142.5, trim($row['descricao_grau_instrucao']));     
        }
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/municipio_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);   


        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/municipio_termo_01.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/municipio_termo_02.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/municipio_termo_03.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);  

        $ob_pdf->Output();
    }

    public function ieabprev($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('public/formulario_cadastro_model');
        
        $this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
        $ob_pdf->AddFont('segoeuil');
        $ob_pdf->AddFont('segoeuib'); 
        $ob_pdf->SetMargins(10, 14, 5);       
        $ob_pdf->AddPage();
                
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/ieabprev_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        if(intval($cd_registro_empregado) > 0)
        {
            $args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $row = $this->formulario_cadastro_model->get_campos_pdf($args);          
                        
            $ob_pdf->SetFont('segoeuil','',11);
            //$ob_pdf->Text(122,39.5, trim($row['cd_registro_empregado']));  
            $ob_pdf->Text(20,54, trim($row['nome']));
            $ob_pdf->Text(20,78, trim($row['dt_nascimento']));              
            $ob_pdf->Text(181.8,78, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(192,78, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));

            switch (intval($row['cd_estado_civil']))
            {
                case 1:
                    $ob_pdf->Text(71.2,78,'X');
                    break;
                case 2:
                    $ob_pdf->Text(91,78,'X');
                    break;
                case 4:
                    $ob_pdf->Text(155.5,78,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(131.5,78,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(109,78,'X');
                    break;          
            }               

            $ob_pdf->Text(20,86, trim($row['cpf_mf']));
            $ob_pdf->Text(20,114, trim($row['endereco']));
            $ob_pdf->Text(150,114, intval($row['nr_endereco']));
            $ob_pdf->Text(180,114, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,123.5, trim($row['bairro']));
            $ob_pdf->Text(65,123.5, trim($row['cidade']));
            $ob_pdf->Text(156.2,123.5, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,123.5, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,132.5, trim($row['telefone_1']));
            $ob_pdf->Text(81,132.5, trim($row['telefone_2']));
            if(trim($row['email_1']) != '')
            {
                $e_mail = trim($row['email_1']);
            }
            else if(trim($row['email_2']) != '')
            {
                $e_mail = trim($row['email_2']);
            }
            else if((trim($row['email_1']) != '') AND (trim($row['email_2']) != ''))
            {
                $e_mail = trim($row['email_1']).'/'.trim($row['email_2']);
            }
            else
            {
                $e_mail = '';
            }               
            $ob_pdf->Text(20,141.5, trim($e_mail));
        }
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/ieabprev_verso_01.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/ieabprev_verso_02.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/ieabprev_termo_01.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/ieabprev_termo_02.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);

        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/ieabprev_termo_03.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        $ob_pdf->Output();
    }
	
	public function termo_de_aceite($cd_empresa = -1, $cd_registro_empregado = -1, $seq_dependencia = -1)
    {
        #ANTIGO: http://www.e-prev.com.br/controle_projetos/rel_contratos_ccin.php?emp=9&re=7536&seq=0&tr=5email_1
		#NOVO (10/2015): http://www.e-prev.com.br/cieprev/index.php/ecrm/formulario_cadastro/termo_de_aceite/9/7536/0
		$this->load->model('public/formulario_cadastro_model');
		
        $this->load->plugin('fpdf');
        
		$ob_pdf = new PDF();
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');				
		$ob_pdf->SetNrPag(true);
		$ob_pdf->SetMargins(10, 14, 5);
		$ob_pdf->header_exibe = true;
		$ob_pdf->header_logo = true;
		$ob_pdf->header_titulo = true;
		$ob_pdf->header_titulo_texto = 'TERMO DE ACEITE';		
        
		$ob_pdf->AddPage();
		$ob_pdf->SetY($ob_pdf->GetY() + 1);		
		
        if(intval($cd_registro_empregado) > 0)
        {
			$this->load->plugin('qrcode');
			$qrcode = new QRcode(utf8_encode('18-'.$cd_empresa.'-'.$cd_registro_empregado.'-'.$seq_dependencia.'-CADP0303-295'), 'L');
			$qrcode->disableBorder();
			$qrcode->displayFPDF($ob_pdf,182.5,10,15);
			
			$args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $ar_participante = $this->formulario_cadastro_model->get_participante($args);            
		}
		else
		{
			$ar_participante['nome']                  = '';
			$ar_participante['cpf']                   = '';
			$ar_participante['cd_empresa']            = '';
			$ar_participante['cd_registro_empregado'] = '';
			$ar_participante['seq_dependencia']       = '';
			$ar_participante['nro_documento']         = '';
			$ar_participante['dt_expedicao']          = '';
			$ar_participante['orgao_expedidor']       = '';
		}
		
		$ob_pdf->SetFont('segoeuil', '', 12);
		$ob_pdf->MultiCell(190, 5,'Nome: '.$ar_participante['nome']);
		$ob_pdf->MultiCell(190, 5,'Empresa: '.$ar_participante['cd_empresa'].'    Re: '.$ar_participante['cd_registro_empregado'].'    Sequъncia: '.$ar_participante['seq_dependencia']);
		$ob_pdf->MultiCell(190, 5,'CPF: '.$ar_participante['cpf']);	
		if(trim($ar_participante['nro_documento']) != '')
		{
			$ob_pdf->MultiCell(190, 5,'RG: '.$ar_participante['nro_documento'].'    Data Exp.: '.$ar_participante['dt_expedicao'].'    Orgуo Exp.: '.$ar_participante['orgao_expedidor']);	
		}			
		
		$ob_pdf->SetY($ob_pdf->GetY() + 3);	
		$ob_pdf->SetFont('segoeuib','',14);
		$ob_pdf->MultiCell(190, 5,'Sistema de Autoatendimento - Call Center e Internet');
		
		$ob_pdf->SetY($ob_pdf->GetY() + 3);	
		$ob_pdf->SetFont('segoeuil', '', 12);
		$ob_pdf->MultiCell(190, 5, '1. Aceitaчуo das Condiчѕes de Uso

1.1. O presente Termo de Aceite tem por finalidade normatizar o uso do serviчo oferecido pelo Sistema de Autoatendimento da Fundaчуo CEEE. Ao usar e/ou enviar dados pessoais р Fundaчуo CEEE, o usuсrio declara que leu e concordou com a versуo mais recente do Termo e se vincula, automсtica e irrevogavelmente, рs regras nele contidas.
 
1.2. O serviчo oferecido nуo envolverс quaisquer єnus para o usuсrio, exceto os da conexуo remota (provedor de acesso a internet) para acesso ao Sistema ou linha telefєnica, que caberс рquele possuir . Da mesma forma, nуo haverс qualquer vantagem ou retribuiчуo ao usuсrio pelas informaчѕes e dados que cadastrar no sistema e pela disposiчуo que deles vier a ser feita pela Fundaчуo CEEE a qualquer tempo. 
 
2. Descriчуo do serviчo
 
2.1. A Fundaчуo CEEE, atravщs do Sistema de Autoatendimento, tem por objetivo coletar e armazenar informaчѕes dos usuсrios tendo como propѓsito o cumprimento de sua missуo institucional de oferecer, desenvolver e administrar soluчѕes de previdъncia complementar, com transparъncia, presteza e seguranчa ao quadro de participantes. 

2.2. O Sistema de Autoatendimento consiste em permitir ao usuсrio acessar/consultar serviчos/informaчѕes  de forma autєnoma, sem a interferъncia do atendente, seja atravщs da internet ou do telefone.
 
2.3. Os serviчos oferecidos por meio do Sistema de Autoatendimento sуo os seguintes: 

2.3.1. Consultas:
a) Contracheques assistidos;
b) Dщbitos;
c) Restituiчуo de contribuiчѕes;
d) Emprщstimo em andamento;
e) Apѓlices de Seguros;
f) Extratos;
g) Demonstrativo de Rentabilidade.

2.3.2. Simulaчѕes:
a)Cсlculos e liquidaчѕes de emprщstimos;
b) Benefэcios.
 
3. Senha e seguranчa
 
3.1. Todo usuсrio que utilizar o serviчo щ responsсvel pela guarda segura e pela confidencialidade da sua senha, alщm de ser inteiramente responsсvel por toda e qualquer atividade, lanчamento e registro de informaчѕes que ocorram sob o uso da mesma, inclusive para efeitos legais.
 
3.2. O usuсrio concorda em notificar imediatamente a Fundaчуo CEEE sobre qualquer uso nуo autorizado da sua senha ou qualquer quebra de seguranчa de que tome conhecimento.
 
3.3. A Fundaчуo CEEE nуo serс responsсvel por qualquer perda que possa ocorrer como conseqќъncia do uso nуo-autorizado por terceiros da  senha de usuсrio, com ou sem o conhecimento do participante.
 
3.4. Para proteger o sigilo da  senha pessoal, recomenda-se ao usuсrio:
 
a) sair de sua conta ao final de cada sessуo e assegurar que a mesma nуo seja acessada por terceiros nуo autorizados; e,

b) nуo informar sua senha, nem mesmo р Fundaчуo CEEE, seja por e-mail, telefone ou outros meios de comunicaчуo.
 
4. Compartilhamento das informaчѕes
 
Todas as informaчѕes enviadas р Fundaчуo CEEE, recebidas de participantes, poderуo ser por esta disponibilizadas para acesso interno ou exibidas na rede interna da Entidade  
 
5. Conduta e Obrigaчѕes do Usuсrio
 
Como condiчуo para utilizar o serviчo de Autoatendimento, o usuсrio concorda em:
 
a) aceitar que o usuсrio щ o њnico responsсvel por toda e qualquer informaчуo cadastrada em seu cadastro, estando sujeito рs conseqќъncias, administrativas, jurэdicas e legais, decorrentes de declaraчѕes falsas ou inexatas que vierem a causar prejuэzos р Fundaчуo CEEE ou a terceiros;

b) nуo utilizar o serviчo para transmitir/divulgar material ilэcito, proibido ou difamatѓrio, que viole a privacidade de terceiros, ou que seja abusivo, ameaчador, discriminatѓrio, injurioso, ou calunioso;

c) nуo transmitir e/ou divulgar qualquer material que viole direitos de terceiros, incluindo direitos de propriedade intelectual;

d) nуo obter ou tentar obter acesso nуo-autorizado a outros sistemas ou redes de computadores conectados ao serviчo (aчѕes de hacker);

e) nуo interferir ou interromper os serviчos de telefone, as redes ou servidores conectados ao serviчo;

f) nуo criar falsa identidade ou utilizar-se de subterfњgios com a finalidade de enganar outras pessoas ou de obter benefэcios;

6. Conduta e Obrigaчѕes da Fundaчуo CEEE

A Fundaчуo obriga-se a preservar a integridade, a fidelidade, a exatidуo e a correчуo dos dados e informaчѕes pessoais cadastradas,  bem como, nуo permitir o acesso as informaчѕes relativas aos dados de identificaчуo, tais como endereчo residencial, telefone, filiaчуo, ano do nascimento ou CPF do usuсrio, assim como dados financeiros do participante, pelas quais a Fundaчуo CEEE se compromete р sua nуo divulgaчуo pњblica. 
 
7. A Fundaчуo CEEE reserva-se o direito de:

a) compartilhar e/ou exibir os dados estatэsticos dos usuсrios do serviчo, consoante descrito no item 4;

b) cancelar o acesso do usuсrio ao serviчo sempre que verificar a mс-utilizaчуo por este do Sistema de Auto Atendimento, ou a prсtica de abusos na sua utilizaчуo. Entende-se por abuso toda e qualquer atividade que ocasione prejuэzo ou lesуo de direitos a terceiros. A prсtica de ato delituoso por meio do Sistema de Auto Atendimento ocasionarс a sua apuraчуo por meio de investigaчуo e, caso constatada a responsabilidade do usuсrio, a adoчуo de medidas administrativas repressivas que poderуo envolver a perda de acesso ao sistema, atribuэdos pela Fundaчуo CEEE ao eventual responsсvel, sem prejuэzo das medidas legais cabэveis a espщcie.

8. Modificaчѕes deste Termo de Aceite   e do Sistema de Auto Atendimento
 
8.1. A Fundaчуo CEEE reserva-se o direito de alterar o conteњdo deste Termo de Aceite, bem como do Sistema de Auto Atendimento, toda a vez que entender pertinente.

9. Legislaчуo Aplicсvel
 
Aplica-se ao presente Termo, e рs responsabilidades nele contidas, toda a legislaчуo federal, estadual e municipal vigente.

10. Estou ciente e de pleno acordo com o presente Termo de Aceite.


DECLARO QUE:


[   ] Aceito           [   ] Nуo Aceito




__________________________,_____ de ______________________ de __________.





__________________________   
Assinatura do Participante

');
		
        $ob_pdf->Output();
    }	
}
?>