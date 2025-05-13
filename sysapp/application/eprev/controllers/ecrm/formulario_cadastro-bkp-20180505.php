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
        if(gerencia_in(array('GE')))
        {
            return TRUE;
        }
        #Vanessa dos Santos Dornelles
        else if($this->session->userdata('codigo') == 146)
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
            exibir_mensagem('ACESSO N�O PERMITIDO');
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
                exibir_mensagem('Esta empresa ('.$cd_empresa.') n�o possue esse plano ('.intval($cd_plano).').');
            }
        } 

        switch ($args['cd_plano'])
        {
            case 1:
                if($args['cd_empresa'] == 3)
                {
                    $this->cgtee($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                }
                break;
            case 2:
                $this->ceeeprev($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;
            case 6:
                $this->crmprev($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;
            case 7:
                $this->senge($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;
            case 8:
                $this->sinpro($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;
            case 9:
                $this->familia($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;
            case 21:
                $this->inpel($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;			
            case 22:
                $this->ceran($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break; 
            case 23:
                $this->foz($cd_empresa, $cd_registro_empregado, $seq_dependencia);
                break;     		
        }
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
            $ob_pdf->Text(20,61,trim($row['nome']) );
            $ob_pdf->Text(20,71, trim($row['dt_nascimento']));         
            $ob_pdf->Text(182,71,(trim($row['fl_sexo']) == 'F' ? 'X' : '') );
            $ob_pdf->Text(192.3,71,(trim($row['fl_sexo']) == 'M' ? 'X' : ''));
            
            switch ($row['cd_estado_civil'])
            {
                case 1:
                    $ob_pdf->Text(71.2,71.3,'X');
                    break;
                case 2:
                    $ob_pdf->Text(91,71.3,'X');
                    break;
                case 4:
                    $ob_pdf->Text(155.5,71.3,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(131.5,71.3,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(109,71.3,'X');
                    break;          
            }   

            $ob_pdf->Text(20,81,trim($row['cpf_mf']));
            $ob_pdf->Text(20,106,trim($row['endereco']));
            $ob_pdf->Text(150,106, intval($row['nr_endereco']));
            $ob_pdf->Text(180,106, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,114,trim($row['bairro']));
            $ob_pdf->Text(70,114,trim($row['cidade']));
            $ob_pdf->Text(157,114, trim($row['unidade_federativa']));
            $ob_pdf->Text(172,114, trim($row['cep'].'-'.$row['complemento_cep']));
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
				
		$ob_pdf->AddPage();
		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/familia_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
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
            $ob_pdf->Text(23.5,45.4, trim($row['cd_registro_empregado']));
            $ob_pdf->Text(149,45.4, trim($row['dt_admissao_dia']));
            $ob_pdf->Text(162,45.4, trim($row['dt_admissao_mes']));
            $ob_pdf->Text(176.5,45.4, trim($row['dt_admissao_ano']));
            $ob_pdf->Text(20,64, trim($row['nome']));
            $ob_pdf->Text(20,73.5, trim($row['dt_nascimento']));      
            $ob_pdf->Text(182,73.5, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(192.3,73.5, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));

            switch ($row['cd_estado_civil'])
            {
                case 1:
                    $ob_pdf->Text(71.2,73.5,'X');
                    break;
                case 2:
                    $ob_pdf->Text(91,73.5,'X');
                    break;
                case 4:
                    $ob_pdf->Text(155.5,73.5,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(131.5,73.5,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(109,73.5,'X');
                    break;          
            }   

            $ob_pdf->Text(20,83, trim($row['cpf_mf']));
            $ob_pdf->Text(20,108.7, trim($row['endereco']));
            $ob_pdf->Text(150,108.7, intval($row['nr_endereco']));
            $ob_pdf->Text(180,108.7, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,117, trim($row['bairro']));
            $ob_pdf->Text(65,117, trim($row['cidade']));
            $ob_pdf->Text(156.2,117, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,117, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,125.2, trim($row['telefone_1']));
            $ob_pdf->Text(81,125.2, trim($row['telefone_2']));
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
            $ob_pdf->Text(20,134.2, trim($e_mail));
            $ob_pdf->Text(20,142.5, trim($row['descricao_grau_instrucao']));     
        }
		
        $ob_pdf->AddPage();
		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/crmprev_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);       
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

            $ob_pdf->Text(20,129.8, trim($e_mail));
            $ob_pdf->Text(20,138.5, trim($row['descricao_grau_instrucao'])); 
            
        }
		
        $ob_pdf->AddPage();
		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/ceeeprev_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
                
        $ob_pdf->Output();
    }
	
    public function inpel($cd_empresa, $cd_registro_empregado, $seq_dependencia)
    {
        $this->load->model('public/formulario_cadastro_model');

        $this->load->plugin('fpdf');
        
        $ob_pdf = new PDF();
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');
        $ob_pdf->SetMargins(10, 14, 5); 		
        $ob_pdf->AddPage();
		$ob_pdf->setXY(0,0);
		$ob_pdf->Image('./img/cadastro_formulario/inpel_frente.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
        if(intval($cd_registro_empregado) > 0)
        {
            $args = array(
                'cd_empresa'            => $cd_empresa,
                'cd_registro_empregado' => $cd_registro_empregado,
                'seq_dependencia'       => $seq_dependencia
            );
            
            $row = $this->formulario_cadastro_model->get_campos_pdf($args);            
            
			$ob_pdf->SetFont('segoeuil','',11);
            $ob_pdf->Text(21.5,39, trim($row['cd_registro_empregado']));	
            $ob_pdf->Text(140,39, trim($row['dt_admissao_dia']));
            $ob_pdf->Text(149,39, trim($row['dt_admissao_mes']));
            $ob_pdf->Text(158.5,39, trim($row['dt_admissao_ano']));
            $ob_pdf->Text(20,57, trim($row['nome']));
            $ob_pdf->Text(20,67, trim($row['dt_nascimento']));	            
            $ob_pdf->Text(181.8,67, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
			$ob_pdf->Text(192,67, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));

            switch ($row['cd_estado_civil'])
            {
                case 1:
                    $ob_pdf->Text(71.2,67,'X');
                    break;
                case 2:
                    $ob_pdf->Text(91,67,'X');
                    break;
                case 4:
                    $ob_pdf->Text(155.5,67,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(131.5,67,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(109,67,'X');
                    break;          
            }              	

            $ob_pdf->Text(20,76.5, trim($row['cpf_mf']));
            $ob_pdf->Text(20,101.8, trim($row['endereco']));
            $ob_pdf->Text(150,101.8, intval($row['nr_endereco']));
            $ob_pdf->Text(180,101.8, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,110, trim($row['bairro']));
            $ob_pdf->Text(65,110, trim($row['cidade']));
            $ob_pdf->Text(156.2,110, trim($row['unidade_federativa']) );
            $ob_pdf->Text(174,110, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,119, trim($row['telefone_1']));
            $ob_pdf->Text(81,119, trim($row['telefone_2'])); 
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
            $ob_pdf->Text(20,127.5, trim($e_mail));
            $ob_pdf->Text(20,136, trim($row['descricao_grau_instrucao']));
		}
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/inpel_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
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
            
            $ob_pdf->Text(21.5,36.5, trim($row['cd_registro_empregado']));  
            $ob_pdf->Text(140,36.5, trim($row['dt_admissao_dia']));
            $ob_pdf->Text(149,36.5, trim($row['dt_admissao_mes']));
            $ob_pdf->Text(158.5,36.5, trim($row['dt_admissao_ano']));
            $ob_pdf->Text(20,55, trim($row['nome']));
            $ob_pdf->Text(20,64.5, trim($row['dt_nascimento']));              
            $ob_pdf->Text(181.8,64.5, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(192,64.5, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));

            switch (intval($row['cd_estado_civil']))
            {
                case 1:
                    $ob_pdf->Text(71.2,64.5,'X');
                    break;
                case 2:
                    $ob_pdf->Text(91,64.5,'X');
                    break;
                case 4:
                    $ob_pdf->Text(155.5,64.5,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(131.5,64.5,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(109,64.5,'X');
                    break;          
            }               

            $ob_pdf->Text(20,74, trim($row['cpf_mf']));
            $ob_pdf->Text(20,99.5, trim($row['endereco']));
            $ob_pdf->Text(150,99.5, intval($row['nr_endereco']));
            $ob_pdf->Text(180,99.5, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,108, trim($row['bairro']));
            $ob_pdf->Text(65,108, trim($row['cidade']));
            $ob_pdf->Text(156.2,108, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,108, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,116.5, trim($row['telefone_1']));
            $ob_pdf->Text(81,116.5, trim($row['telefone_2'])); 
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
            $ob_pdf->Text(20,124.8, trim($row['descricao_grau_instrucao']));
            $ob_pdf->Text(20,133.5, trim($e_mail)); 
        }
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/ceran_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
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
            
            $ob_pdf->Text(21.5,37, trim($row['cd_registro_empregado']));  
            $ob_pdf->Text(140,37, trim($row['dt_admissao_dia']));
            $ob_pdf->Text(149,37, trim($row['dt_admissao_mes']));
            $ob_pdf->Text(158.5,37, trim($row['dt_admissao_ano']));
            $ob_pdf->Text(20,56, trim($row['nome']));
            $ob_pdf->Text(20,65.5, trim($row['dt_nascimento']));              
            $ob_pdf->Text(181.8,65.5, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(192,65.5, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));

            switch (intval($row['cd_estado_civil']))
            {
                case 1:
                    $ob_pdf->Text(71.2,65.5,'X');
                    break;
                case 2:
                    $ob_pdf->Text(91,65.5,'X');
                    break;
                case 4:
                    $ob_pdf->Text(155.5,65.5,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(131.5,65.5,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(109,65.5,'X');
                    break;          
            }               

            $ob_pdf->Text(20,75, trim($row['cpf_mf']));
            $ob_pdf->Text(20,100.5, trim($row['endereco']));
            $ob_pdf->Text(150,100.5, intval($row['nr_endereco']));
            $ob_pdf->Text(180,100.5, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,109, trim($row['bairro']));
            $ob_pdf->Text(65,109, trim($row['cidade']));
            $ob_pdf->Text(156.2,109, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,109, trim($row['cep'].'-'.$row['complemento_cep']));
            $ob_pdf->Text(20,117.5, trim($row['telefone_1']));
            $ob_pdf->Text(81,117.5, trim($row['telefone_2']));
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
            $ob_pdf->Text(20,125.5, trim($row['descricao_grau_instrucao']));
            $ob_pdf->Text(20,134.5, trim($e_mail));
        }
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/foz_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
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
            $ob_pdf->Text(20,60.5, trim($row['nome']));
            $ob_pdf->Text(20,70, trim($row['dt_nascimento']));              
            $ob_pdf->Text(181.8,70, (trim($row['fl_sexo']) == 'F' ? 'X' : ''));
            $ob_pdf->Text(192,70, (trim($row['fl_sexo']) == 'M' ? 'X' : ''));

            switch (intval($row['cd_estado_civil']))
            {
                case 1:
                    $ob_pdf->Text(71.2,70,'X');
                    break;
                case 2:
                    $ob_pdf->Text(91,70,'X');
                    break;
                case 4:
                    $ob_pdf->Text(155.5,70,'X');
                    break;          
                case 6:
                    $ob_pdf->Text(131.5,70,'X');
                    break; 
                case 7:
                    $ob_pdf->Text(109,70,'X');
                    break;          
            }               

            $ob_pdf->Text(20,79.5, trim($row['cpf_mf']));
            $ob_pdf->Text(20,105, trim($row['endereco']));
            $ob_pdf->Text(150,105, intval($row['nr_endereco']));
            $ob_pdf->Text(180,105, intval($row['complemento_endereco']));
            $ob_pdf->Text(20,113.5, trim($row['bairro']));
            $ob_pdf->Text(65,113.5, trim($row['cidade']));
            $ob_pdf->Text(156.2,113.5, trim($row['unidade_federativa']));
            $ob_pdf->Text(174,113.5, trim($row['cep'].'-'.$row['complemento_cep']));
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
        
        $ob_pdf->AddPage();
        $ob_pdf->setXY(0,0);
        $ob_pdf->Image('./img/cadastro_formulario/senge_verso.jpg', $ob_pdf->GetX(), $ob_pdf->GetY(), $ob_pdf->ConvertSize(800), $ob_pdf->ConvertSize(1131),'','',false);
        
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
		$ob_pdf->MultiCell(190, 5,'Empresa: '.$ar_participante['cd_empresa'].'    Re: '.$ar_participante['cd_registro_empregado'].'    Sequ�ncia: '.$ar_participante['seq_dependencia']);
		$ob_pdf->MultiCell(190, 5,'CPF: '.$ar_participante['cpf']);	
		if(trim($ar_participante['nro_documento']) != '')
		{
			$ob_pdf->MultiCell(190, 5,'RG: '.$ar_participante['nro_documento'].'    Data Exp.: '.$ar_participante['dt_expedicao'].'    Org�o Exp.: '.$ar_participante['orgao_expedidor']);	
		}			
		
		$ob_pdf->SetY($ob_pdf->GetY() + 3);	
		$ob_pdf->SetFont('segoeuib','',14);
		$ob_pdf->MultiCell(190, 5,'Sistema de Autoatendimento - Call Center e Internet');
		
		$ob_pdf->SetY($ob_pdf->GetY() + 3);	
		$ob_pdf->SetFont('segoeuil', '', 12);
		$ob_pdf->MultiCell(190, 5, '1. Aceita��o das Condi��es de Uso

1.1. O presente Termo de Aceite tem por finalidade normatizar o uso do servi�o oferecido pelo Sistema de Autoatendimento da Funda��o CEEE. Ao usar e/ou enviar dados pessoais � Funda��o CEEE, o usu�rio declara que leu e concordou com a vers�o mais recente do Termo e se vincula, autom�tica e irrevogavelmente, �s regras nele contidas.
 
1.2. O servi�o oferecido n�o envolver� quaisquer �nus para o usu�rio, exceto os da conex�o remota (provedor de acesso a internet) para acesso ao Sistema ou linha telef�nica, que caber� �quele possuir . Da mesma forma, n�o haver� qualquer vantagem ou retribui��o ao usu�rio pelas informa��es e dados que cadastrar no sistema e pela disposi��o que deles vier a ser feita pela Funda��o CEEE a qualquer tempo. 
 
2. Descri��o do servi�o
 
2.1. A Funda��o CEEE, atrav�s do Sistema de Autoatendimento, tem por objetivo coletar e armazenar informa��es dos usu�rios tendo como prop�sito o cumprimento de sua miss�o institucional de oferecer, desenvolver e administrar solu��es de previd�ncia complementar, com transpar�ncia, presteza e seguran�a ao quadro de participantes. 

2.2. O Sistema de Autoatendimento consiste em permitir ao usu�rio acessar/consultar servi�os/informa��es  de forma aut�noma, sem a interfer�ncia do atendente, seja atrav�s da internet ou do telefone.
 
2.3. Os servi�os oferecidos por meio do Sistema de Autoatendimento s�o os seguintes: 

2.3.1. Consultas:
a) Contracheques assistidos;
b) D�bitos;
c) Restitui��o de contribui��es;
d) Empr�stimo em andamento;
e) Ap�lices de Seguros;
f) Extratos;
g) Demonstrativo de Rentabilidade.

2.3.2. Simula��es:
a)C�lculos e liquida��es de empr�stimos;
b) Benef�cios.
 
3. Senha e seguran�a
 
3.1. Todo usu�rio que utilizar o servi�o � respons�vel pela guarda segura e pela confidencialidade da sua senha, al�m de ser inteiramente respons�vel por toda e qualquer atividade, lan�amento e registro de informa��es que ocorram sob o uso da mesma, inclusive para efeitos legais.
 
3.2. O usu�rio concorda em notificar imediatamente a Funda��o CEEE sobre qualquer uso n�o autorizado da sua senha ou qualquer quebra de seguran�a de que tome conhecimento.
 
3.3. A Funda��o CEEE n�o ser� respons�vel por qualquer perda que possa ocorrer como conseq��ncia do uso n�o-autorizado por terceiros da  senha de usu�rio, com ou sem o conhecimento do participante.
 
3.4. Para proteger o sigilo da  senha pessoal, recomenda-se ao usu�rio:
 
a) sair de sua conta ao final de cada sess�o e assegurar que a mesma n�o seja acessada por terceiros n�o autorizados; e,

b) n�o informar sua senha, nem mesmo � Funda��o CEEE, seja por e-mail, telefone ou outros meios de comunica��o.
 
4. Compartilhamento das informa��es
 
Todas as informa��es enviadas � Funda��o CEEE, recebidas de participantes, poder�o ser por esta disponibilizadas para acesso interno ou exibidas na rede interna da Entidade  
 
5. Conduta e Obriga��es do Usu�rio
 
Como condi��o para utilizar o servi�o de Autoatendimento, o usu�rio concorda em:
 
a) aceitar que o usu�rio � o �nico respons�vel por toda e qualquer informa��o cadastrada em seu cadastro, estando sujeito �s conseq��ncias, administrativas, jur�dicas e legais, decorrentes de declara��es falsas ou inexatas que vierem a causar preju�zos � Funda��o CEEE ou a terceiros;

b) n�o utilizar o servi�o para transmitir/divulgar material il�cito, proibido ou difamat�rio, que viole a privacidade de terceiros, ou que seja abusivo, amea�ador, discriminat�rio, injurioso, ou calunioso;

c) n�o transmitir e/ou divulgar qualquer material que viole direitos de terceiros, incluindo direitos de propriedade intelectual;

d) n�o obter ou tentar obter acesso n�o-autorizado a outros sistemas ou redes de computadores conectados ao servi�o (a��es de hacker);

e) n�o interferir ou interromper os servi�os de telefone, as redes ou servidores conectados ao servi�o;

f) n�o criar falsa identidade ou utilizar-se de subterf�gios com a finalidade de enganar outras pessoas ou de obter benef�cios;

6. Conduta e Obriga��es da Funda��o CEEE

A Funda��o obriga-se a preservar a integridade, a fidelidade, a exatid�o e a corre��o dos dados e informa��es pessoais cadastradas,  bem como, n�o permitir o acesso as informa��es relativas aos dados de identifica��o, tais como endere�o residencial, telefone, filia��o, ano do nascimento ou CPF do usu�rio, assim como dados financeiros do participante, pelas quais a Funda��o CEEE se compromete � sua n�o divulga��o p�blica. 
 
7. A Funda��o CEEE reserva-se o direito de:

a) compartilhar e/ou exibir os dados estat�sticos dos usu�rios do servi�o, consoante descrito no item 4;

b) cancelar o acesso do usu�rio ao servi�o sempre que verificar a m�-utiliza��o por este do Sistema de Auto Atendimento, ou a pr�tica de abusos na sua utiliza��o. Entende-se por abuso toda e qualquer atividade que ocasione preju�zo ou les�o de direitos a terceiros. A pr�tica de ato delituoso por meio do Sistema de Auto Atendimento ocasionar� a sua apura��o por meio de investiga��o e, caso constatada a responsabilidade do usu�rio, a ado��o de medidas administrativas repressivas que poder�o envolver a perda de acesso ao sistema, atribu�dos pela Funda��o CEEE ao eventual respons�vel, sem preju�zo das medidas legais cab�veis a esp�cie.

8. Modifica��es deste Termo de Aceite   e do Sistema de Auto Atendimento
 
8.1. A Funda��o CEEE reserva-se o direito de alterar o conte�do deste Termo de Aceite, bem como do Sistema de Auto Atendimento, toda a vez que entender pertinente.

9. Legisla��o Aplic�vel
 
Aplica-se ao presente Termo, e �s responsabilidades nele contidas, toda a legisla��o federal, estadual e municipal vigente.

10. Estou ciente e de pleno acordo com o presente Termo de Aceite.


DECLARO QUE:


[   ] Aceito           [   ] N�o Aceito




__________________________,_____ de ______________________ de __________.





__________________________   
Assinatura do Participante

');
		
        $ob_pdf->Output();
    }	
}
?>