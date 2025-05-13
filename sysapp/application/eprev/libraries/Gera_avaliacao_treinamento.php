<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Gera_avaliacao_treinamento
{
	public function monta_formulario($cd_treinamento_colaborador_formulario)
	{
		$ci =& get_instance();

		$ci->load->model('projetos/treinamento_colaborador_formulario_model');
		
		$cadastro = $ci->treinamento_colaborador_formulario_model->carrega($cd_treinamento_colaborador_formulario);
		
		$formulario = array(
			'cd' 	    => $cadastro['cd_treinamento_colaborador_formulario'],
			'ds'	    => $cadastro['ds_treinamento_colaborador_formulario'],
			'fl_para'   => $cadastro['fl_enviar_para'],
			'tipo'	    => array(),
			'estrutura' => array()			
		);

		$campo_adicional   = array();
		$campo_obrigatorio = array();
		
		$tipo = $ci->treinamento_colaborador_formulario_model->get_formulario_tipo(intval($cd_treinamento_colaborador_formulario));

		foreach($tipo as $key => $item) 
		{
			$formulario['tipo'][$key] = array(
				'cd' => $item['cd_treinamento_colaborador_tipo'],
				'ds' => utf8_encode($item['ds_treinamento_colaborador_tipo'])
			);
		}
		
		$estrutura = $ci->treinamento_colaborador_formulario_model->estrutura_listar($cd_treinamento_colaborador_formulario);
		
		foreach($estrutura as $key => $item)
		{
			$sub_estrutura = $ci->treinamento_colaborador_formulario_model->estrutura_listar($cd_treinamento_colaborador_formulario, $item['cd_treinamento_colaborador_formulario_estrutura']);

			$sub_formulario = array();
			
			foreach($sub_estrutura as $key2 => $item2)
			{	
				$configurar_sub_estrutura = $ci->treinamento_colaborador_formulario_model->configurar_listar($item2['cd_treinamento_colaborador_formulario_estrutura']);
	
				$configurar_formulario_sub_estrutura = array();
				
				foreach($configurar_sub_estrutura as $key3 => $item3)
				{
					$configurar_formulario_sub_estrutura[$key3] = array(
						'cd' => $item3['cd_treinamento_colaborador_formulario_estrutura_conf'], 
						'ds' => utf8_encode($item3['ds_treinamento_colaborador_formulario_estrutura_conf'])
					);
				}
				
				if(trim($item2['fl_obrigatorio']) == 'S')
				{
					$campo_obrigatorio[] = $item2['cd_treinamento_colaborador_formulario_estrutura'];
				}

				$sub_formulario[$key2] = array(
					'cd'   => $item2['cd_treinamento_colaborador_formulario_estrutura'], 
					'tp'   => $item2['fl_tipo'],
					'ds'   => $item2['nr_treinamento_colaborador_formulario_estrutura'].') '.utf8_encode($item2['ds_treinamento_colaborador_formulario_estrutura']),
					'obr'  => $item2['fl_obrigatorio'],
					'conf' => $configurar_formulario_sub_estrutura
				);
			}
			
			$configurar = $ci->treinamento_colaborador_formulario_model->configurar_listar($item['cd_treinamento_colaborador_formulario_estrutura']);
	
			$configurar_formulario = array();
			
			foreach($configurar as $key3 => $item3)
			{
				if(trim($item3['fl_campo_adicional']) == 'S')
				{
					$campo_adicional[$item['cd_treinamento_colaborador_formulario_estrutura']] = $item3['cd_treinamento_colaborador_formulario_estrutura_conf'];
				}

				$configurar_formulario[$key3] = array(
					'cd'  => $item3['cd_treinamento_colaborador_formulario_estrutura_conf'],
					'ds'  => utf8_encode($item3['ds_treinamento_colaborador_formulario_estrutura_conf']),
					'obs' => $item3['fl_campo_adicional']
				);
			}

			if(trim($item['fl_obrigatorio']) == 'S')
			{
				$campo_obrigatorio[] = $item['cd_treinamento_colaborador_formulario_estrutura'];
			}
			
			$formulario['estrutura'][$key] = array(
				'cd'   => $item['cd_treinamento_colaborador_formulario_estrutura'], 
				'tp'   => $item['fl_tipo'],
				'ds'   => $item['nr_treinamento_colaborador_formulario_estrutura'].') '.utf8_encode($item['ds_treinamento_colaborador_formulario_estrutura']),
				'obr'  => $item['fl_obrigatorio'],
				'sub'  => $sub_formulario,
				'conf' => $configurar_formulario	
			);		
		}

		$formulario['campo_obrigatorio'] = $campo_obrigatorio;	
		$formulario['campo_adicional']   = $campo_adicional;	
		
		return $formulario;
	}

	public function pdf($cd_treinamento_colaborador_resposta)
	{
		$ci =& get_instance();
		
		$ci->load->model('projetos/avaliacao_treinamento_model');

        $treinamento = $ci->avaliacao_treinamento_model->carrega($cd_treinamento_colaborador_resposta);

        $formulario = json_decode($treinamento['ds_formulario'], true);
        $respostas  = json_decode($treinamento['ds_formulario_respondido'], true);
		
		$ci->load->plugin('fpdf');
				
		$ob_pdf = new PDF();
		$ob_pdf->AddFont('segoeuil');
		$ob_pdf->AddFont('segoeuib');	
		$ob_pdf->SetNrPag(true);
		$ob_pdf->SetMargins(10, 14, 5);
		$ob_pdf->header_exibe = true;
		$ob_pdf->header_logo = true;
		$ob_pdf->header_titulo = true;
		$ob_pdf->header_titulo_texto = $treinamento['ds_treinamento_colaborador_formulario'].' - '.$treinamento['enviar_para'];
		$ob_pdf->SetLineWidth(0);
		$ob_pdf->SetDrawColor(0, 0, 0);
		$ob_pdf->AddPage();
		
		$ob_pdf->SetFont('segoeuil', '', 10);
		$ob_pdf->MultiCell(190, 4.5, 'Colaborador: '.$treinamento['colaborador'], 0, 'L');
		$ob_pdf->MultiCell(190, 4.5, 'Treinamento: '.$treinamento['nome'], 0, 'L');
		$ob_pdf->MultiCell(190, 4.5, 'Promotor: '.$treinamento['promotor'], 0, 'L');
		$ob_pdf->MultiCell(190, 4.5, 'Período: '.$treinamento['dt_inicio'].' - '.$treinamento['dt_final'], 0, 'L');
		$ob_pdf->MultiCell(190, 4.5, 'Data Envio Avaliação: '.$treinamento['dt_inclusao'], 0, 'L');
		$ob_pdf->MultiCell(190, 4.5, 'Data Finalizado: '.$treinamento['dt_finalizado'], 0, 'L');
		
		$ob_pdf->SetY($ob_pdf->GetY() + 5);
		
		foreach($formulario['estrutura'] as $key => $item)
		{	
			if(trim($item['tp']) == 'D')
			{
				$ob_pdf->SetFont('segoeuib', '', 12);
				$ob_pdf->SetY($ob_pdf->GetY() + 5);
				$ob_pdf->MultiCell(190, 4.5, utf8_decode($item['ds']), 0, 'L');
				$ob_pdf->SetFont('segoeuil', '', 12);
				$ob_pdf->SetY($ob_pdf->GetY() + 5);
				$ob_pdf->MultiCell(190, 4.5, utf8_decode($respostas['estrutura_'.$item['cd']]), 0, 'J');
			}
			
			else if(trim($item['tp']) == 'O')
			{
				$ob_pdf->SetFont('segoeuib', '', 12);
				$ob_pdf->SetY($ob_pdf->GetY() + 5);
				$ob_pdf->MultiCell(190, 4.5, utf8_decode($item['ds']), 0, 'L');
				$ob_pdf->SetFont('segoeuil', '', 12);
				if(count($item['sub']) == 0)
                { 
					foreach($item['conf'] as $key2 => $item2)
					{
						$checked = '( '.(intval($item2['cd']) == intval($respostas['estrutura_'.$item['cd']]) ? 'X' : '  ').' )   ';
						
						$ob_pdf->MultiCell(190, 4.5, $checked.utf8_decode($item2['ds']), 0, 'L');
					}					
				}
				else
				{	
					foreach ($item['sub'] as $key2 => $item2) 
                    {
						$ob_pdf->SetY($ob_pdf->GetY() + 5);
						$ob_pdf->SetFont('segoeuib', '', 10);
						$ob_pdf->MultiCell(190, 4.5, utf8_decode($item2['ds']), 0, 'L');
						$ob_pdf->SetFont('segoeuil', '', 10);
                        foreach ($item2['conf'] as $key3 => $item3) 
						{
							$checked = '( '.(intval($item3['cd']) == intval($respostas['estrutura_'.$item2['cd']]) ? 'X' : '  ').' )   ';
						
							$ob_pdf->MultiCell(190, 4.5, $checked.utf8_decode($item3['ds']), 0, 'L');
						}
					}
				}
			}
			else if(trim($item['tp']) == 'S')
			{
				$ob_pdf->SetFont('segoeuib', '', 12);
				$ob_pdf->SetY($ob_pdf->GetY() + 5);
				$ob_pdf->MultiCell(190, 4.5, utf8_decode($item['ds']), 0, 'L');
				$ob_pdf->SetFont('segoeuil', '', 12);
				$ob_pdf->SetY($ob_pdf->GetY() + 5);
				
				foreach($item['conf'] as $key2 => $item2)
				{
					foreach($respostas['estrutura_'.$item['cd']] as $key3 => $item3)
					{
						if(intval($item2['cd']) == intval($item3))
						{
							$checked = '( X )   ';
							
							break;
						}
						else
						{
							$checked = '(    )   ';
						}
					}
					$ob_pdf->MultiCell(190, 4.5, $checked.utf8_decode($item2['ds']), 0, 'L');
					
					if($item2['obs'] == 'S')
					{
						$ob_pdf->MultiCell(190, 4.5, utf8_decode($respostas['estrutura_obs_'.$item['cd']]), 0, 'L');
					}
				}
			}
		}
	    $ob_pdf->Output();
	}
	/*
	
*/
}