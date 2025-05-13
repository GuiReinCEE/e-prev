<?php
class ri_perfil_participante extends Controller
{
    function __construct()
    {
        parent::Controller();
    }
	
    function index()
    {
		CheckLogin();
		if(gerencia_in(Array('GRI','GAP','GA','GI')))
		{
			$args = Array();	
			$data = Array();	
			$this->load->view('ecrm/ri_perfil_participante/index.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		
    }	
	
	function planoListar()
    {
		CheckLogin();
		if(gerencia_in(Array('GRI','GAP','GA','GI')))
		{
			$this->load->model('public/RI_perfil_participante_model');
			$args = Array();	
			$data = Array();	
			
			$this->RI_perfil_participante_model->plano($result, $args);
			$data['ar_plano'] = $result->result_array();				
			$nr_conta = 0;
			foreach($data['ar_plano'] as $ar_item)
			{
				$args['cd_plano'] = $ar_item['cd_plano'];
				$this->RI_perfil_participante_model->Ativo($result, $args);
				$row = $result->row_array();				
				$data['ar_plano'][$nr_conta]['AT'] = $row['qt_total'];			

				$this->RI_perfil_participante_model->Assistido($result, $args);
				$row = $result->row_array();				
				$data['ar_plano'][$nr_conta]['AS'] = $row['qt_total'];
				
				$this->RI_perfil_participante_model->Pensao($result, $args);
				$row = $result->row_array();				
				$data['ar_plano'][$nr_conta]['PA'] = $row['qt_total'];		

				$this->RI_perfil_participante_model->Pensionista($result, $args);
				$row = $result->row_array();				
				$data['ar_plano'][$nr_conta]['PE'] = $row['qt_total'];					
				
				$nr_conta++;
			}
			
			$this->load->view('ecrm/ri_perfil_participante/index_result.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	

	function planoEmpresa()
    {
		CheckLogin();
		if(gerencia_in(Array('GRI','GAP','GA','GI')))
		{
			$this->load->model('public/RI_perfil_participante_model');
			$args = Array();	
			$data = Array();	
			
			$this->RI_perfil_participante_model->planoEmpresa($result, $args);
			$data['ar_plano'] = $result->result_array();				
			$nr_conta = 0;
			foreach($data['ar_plano'] as $ar_item)
			{
				$args['cd_empresa'] = $ar_item['cd_empresa'];
				$args['cd_plano']   = $ar_item['cd_plano'];
				$this->RI_perfil_participante_model->Ativo($result, $args);
				$row = $result->row_array();				
				$data['ar_plano'][$nr_conta]['AT'] = $row['qt_total'];			

				$this->RI_perfil_participante_model->Assistido($result, $args);
				$row = $result->row_array();				
				$data['ar_plano'][$nr_conta]['AS'] = $row['qt_total'];

				$this->RI_perfil_participante_model->Pensao($result, $args);
				$row = $result->row_array();				
				$data['ar_plano'][$nr_conta]['PA'] = $row['qt_total'];	
				
				$this->RI_perfil_participante_model->Pensionista($result, $args);
				$row = $result->row_array();				
				$data['ar_plano'][$nr_conta]['PE'] = $row['qt_total'];				
				
				$nr_conta++;
			}
			
			$this->load->view('ecrm/ri_perfil_participante/index_empresa_result.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
    function sexo()
    {
		CheckLogin();
		if(gerencia_in(Array('GRI','GAP','GA','GI')))
		{
			$args = Array();	
			$data = Array();	
			$this->load->view('ecrm/ri_perfil_participante/sexo.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		
    }	
	
    function sexoListar()
    {
		CheckLogin();
		if(gerencia_in(Array('GRI','GAP','GA','GI')))
		{
			$this->load->model('public/RI_perfil_participante_model');
			$args = Array();	
			$data = Array();	
			
			$data['ar_sexo']['AT'] = Array("M" => 0, "F" =>0);
			$data['ar_sexo']['AS'] = Array("M" => 0, "F" =>0);
			$data['ar_sexo']['PA'] = Array("M" => 0, "F" =>0);
			$data['ar_sexo']['PE'] = Array("M" => 0, "F" =>0);
			
			### HOMENS ####
			$args['sexo'] = 'M';
			
			$this->RI_perfil_participante_model->Ativo($result, $args);
			$row = $result->row_array();				
			$data['ar_sexo']['AT'][$args['sexo']] = $row['qt_total'];
			
			$this->RI_perfil_participante_model->Assistido($result, $args);
			$row = $result->row_array();				
			$data['ar_sexo']['AS'][$args['sexo']] = $row['qt_total'];

			$this->RI_perfil_participante_model->Pensao($result, $args);
			$row = $result->row_array();				
			$data['ar_sexo']['PA'][$args['sexo']] = $row['qt_total'];				
			
			$this->RI_perfil_participante_model->Pensionista($result, $args);
			$row = $result->row_array();				
			$data['ar_sexo']['PE'][$args['sexo']] = $row['qt_total'];	

			### MULHERES ####
			$args['sexo'] = 'F';
			
			$this->RI_perfil_participante_model->Ativo($result, $args);
			$row = $result->row_array();				
			$data['ar_sexo']['AT'][$args['sexo']] = $row['qt_total'];
			
			$this->RI_perfil_participante_model->Assistido($result, $args);
			$row = $result->row_array();				
			$data['ar_sexo']['AS'][$args['sexo']] = $row['qt_total'];

			$this->RI_perfil_participante_model->Pensao($result, $args);
			$row = $result->row_array();				
			$data['ar_sexo']['PA'][$args['sexo']] = $row['qt_total'];			
			
			$this->RI_perfil_participante_model->Pensionista($result, $args);
			$row = $result->row_array();				
			$data['ar_sexo']['PE'][$args['sexo']] = $row['qt_total'];			
			
			
			$this->load->view('ecrm/ri_perfil_participante/sexo_result.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		
    }
	
    function sexoPlano()
    {
		CheckLogin();
		if(gerencia_in(Array('GRI','GAP','GA','GI')))
		{
			$this->load->model('public/RI_perfil_participante_model');
			$args = Array();	
			$data = Array();	
			
			$data['ar_categoria'][] = array("codigo" => "F", "desc" => "FEMININO");
			$data['ar_categoria'][] = array("codigo" => "M", "desc" => "MASCULINO");
			
			$this->RI_perfil_participante_model->plano($result, $args);
			$data['ar_plano'] = $result->result_array();				
			$nr_conta = 0;
			foreach($data['ar_plano'] as $ar_item)
			{
				$args['cd_plano'] = $ar_item['cd_plano'];

				foreach($data['ar_categoria'] as $ar_item_categ)
				{
					$args['sexo'] = $ar_item_categ['codigo'];

					$this->RI_perfil_participante_model->Ativo($result, $args);
					$row = $result->row_array();				
					$data['ar_plano'][$nr_conta]['AT'][$args['sexo']] = $row['qt_total'];	
					
					$this->RI_perfil_participante_model->Assistido($result, $args);
					$row = $result->row_array();				
					$data['ar_plano'][$nr_conta]['AS'][$args['sexo']] = $row['qt_total'];	

					$this->RI_perfil_participante_model->Pensao($result, $args);
					$row = $result->row_array();				
					$data['ar_plano'][$nr_conta]['PA'][$args['sexo']] = $row['qt_total'];						
					
					$this->RI_perfil_participante_model->Pensionista($result, $args);
					$row = $result->row_array();				
					$data['ar_plano'][$nr_conta]['PE'][$args['sexo']] = $row['qt_total'];	
				}
			
					
				$nr_conta++;
			}
			
			$this->load->view('ecrm/ri_perfil_participante/sexo_plano_result.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		
    }	

    
	
	
    function idade()
    {
		CheckLogin();
		if(gerencia_in(Array('GRI','GAP','GA','GI')))
		{
			$args = Array();	
			$data = Array();	
			$this->load->view('ecrm/ri_perfil_participante/idade.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		
    }	
	
    function idadeListar()
    {
		CheckLogin();
		if(gerencia_in(Array('GRI','GAP','GA','GI')))
		{
			$this->load->model('public/RI_perfil_participante_model');
			$args = Array();	
			$data = Array();	
			
			$data['ar_categoria'][] = array("desc" => "De 0 a 18 anos",  "min" => 0,  "max" => 18);
			$data['ar_categoria'][] = array("desc" => "De 19 a 24 anos", "min" => 19, "max" => 24);
			$data['ar_categoria'][] = array("desc" => "De 25 a 29 anos", "min" => 25, "max" => 29);
			$data['ar_categoria'][] = array("desc" => "De 30 a 34 anos", "min" => 30, "max" => 34);
			$data['ar_categoria'][] = array("desc" => "De 35 a 34 anos", "min" => 35, "max" => 39);
			$data['ar_categoria'][] = array("desc" => "De 40 a 44 anos", "min" => 40, "max" => 44);
			$data['ar_categoria'][] = array("desc" => "De 45 a 49 anos", "min" => 45, "max" => 49);
			$data['ar_categoria'][] = array("desc" => "De 50 a 54 anos", "min" => 50, "max" => 54);
			$data['ar_categoria'][] = array("desc" => "De 55 a 59 anos", "min" => 55, "max" => 59);
			$data['ar_categoria'][] = array("desc" => "De 60 a 64 anos", "min" => 60, "max" => 64);
			$data['ar_categoria'][] = array("desc" => "De 65 a 69 anos", "min" => 65, "max" => 69);
			$data['ar_categoria'][] = array("desc" => "De 70 a 74 anos", "min" => 70, "max" => 74);
			$data['ar_categoria'][] = array("desc" => "Mais de 75 anos", "min" => 75, "max" => 999999);
			
			
			$this->RI_perfil_participante_model->plano($result, $args);
			$data['ar_plano'] = $result->result_array();				
			$nr_conta = 0;
			foreach($data['ar_plano'] as $ar_item)
			{
				$args['cd_plano'] = $ar_item['cd_plano'];
				
				$nr_conta_idade = 0;
				foreach ($data['ar_categoria'] as $ar_idade)
				{
					$args['ar_idade'] = $ar_idade;
					$this->RI_perfil_participante_model->Ativo($result, $args);
					$row = $result->row_array();				
					$data['ar_plano'][$nr_conta]['AT'][$nr_conta_idade] = $row['qt_total'];	
					
					$this->RI_perfil_participante_model->Assistido($result, $args);
					$row = $result->row_array();				
					$data['ar_plano'][$nr_conta]['AS'][$nr_conta_idade] = $row['qt_total'];	

					$this->RI_perfil_participante_model->Pensao($result, $args);
					$row = $result->row_array();				
					$data['ar_plano'][$nr_conta]['PA'][$nr_conta_idade] = $row['qt_total'];						
					
					$this->RI_perfil_participante_model->Pensionista($result, $args);
					$row = $result->row_array();				
					$data['ar_plano'][$nr_conta]['PE'][$nr_conta_idade] = $row['qt_total'];						
					
					$nr_conta_idade++;
				}
				$nr_conta++;
			}
			
			$this->load->view('ecrm/ri_perfil_participante/idade_result.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		
    }	
	
    function idadeResumo()
    {
		CheckLogin();
		if(gerencia_in(Array('GRI','GAP','GA','GI')))
		{
			$this->load->model('public/RI_perfil_participante_model');
			$args = Array();	
			$data = Array();	
			
			$data['ar_categoria'][] = array("desc" => "De 0 a 18 anos",  "min" => 0,  "max" => 18);
			$data['ar_categoria'][] = array("desc" => "De 19 a 24 anos", "min" => 19, "max" => 24);
			$data['ar_categoria'][] = array("desc" => "De 25 a 29 anos", "min" => 25, "max" => 29);
			$data['ar_categoria'][] = array("desc" => "De 30 a 34 anos", "min" => 30, "max" => 34);
			$data['ar_categoria'][] = array("desc" => "De 35 a 34 anos", "min" => 35, "max" => 39);
			$data['ar_categoria'][] = array("desc" => "De 40 a 44 anos", "min" => 40, "max" => 44);
			$data['ar_categoria'][] = array("desc" => "De 45 a 49 anos", "min" => 45, "max" => 49);
			$data['ar_categoria'][] = array("desc" => "De 50 a 54 anos", "min" => 50, "max" => 54);
			$data['ar_categoria'][] = array("desc" => "De 55 a 59 anos", "min" => 55, "max" => 59);
			$data['ar_categoria'][] = array("desc" => "De 60 a 64 anos", "min" => 60, "max" => 64);
			$data['ar_categoria'][] = array("desc" => "De 65 a 69 anos", "min" => 65, "max" => 69);
			$data['ar_categoria'][] = array("desc" => "De 70 a 74 anos", "min" => 70, "max" => 74);
			$data['ar_categoria'][] = array("desc" => "Mais de 75 anos", "min" => 75, "max" => 999999);
				
			$nr_conta = 0;
			foreach ($data['ar_categoria'] as $ar_idade)
			{
				$args['ar_idade'] = $ar_idade;
				$this->RI_perfil_participante_model->Ativo($result, $args);
				$row = $result->row_array();				
				$data['ar_categoria'][$nr_conta]['AT'] = $row['qt_total'];	
				
				$this->RI_perfil_participante_model->Assistido($result, $args);
				$row = $result->row_array();				
				$data['ar_categoria'][$nr_conta]['AS'] = $row['qt_total'];	

				$this->RI_perfil_participante_model->Pensao($result, $args);
				$row = $result->row_array();				
				$data['ar_categoria'][$nr_conta]['PA'] = $row['qt_total'];					
				
				$this->RI_perfil_participante_model->Pensionista($result, $args);
				$row = $result->row_array();				
				$data['ar_categoria'][$nr_conta]['PE'] = $row['qt_total'];						
				
				$nr_conta++;
			}
			
			$this->load->view('ecrm/ri_perfil_participante/idade_resumo_result.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
		
    }	
}
