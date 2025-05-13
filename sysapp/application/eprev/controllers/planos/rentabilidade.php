<?php
class Rentabilidade extends Controller
{
    function __construct()
    {
        parent::Controller();

        CheckLogin();
    }

    public function index()
    {
		$this->load->view('planos/rentabilidade/index');
    }

    private function calcula_cotas($ar_dados)
    {
        $nr_fim            = count($ar_dados);
        $nr_conta          = 0;
        $dt_referencia     = '';
        $ar_cota_mes       = array();
        $ar_dt_mes         = array();
        $ar_cota_acumulada = array();
        $ar_titulo         = array();
        $dt_referencia     = '';

        while ($nr_conta < $nr_fim) 
        {
            $ar_reg = $ar_dados[$nr_conta];

            if($nr_conta == 0)
            {
                $nr_anterior = $ar_reg['vl_cota'];
                $nr_conta_acumulada_anterior = 0;           
            }
            else
            {
                $nr_cota_mes = (($ar_reg['vl_cota']/$nr_anterior) - 1) * 100;
                $nr_conta_acumulada = (((($nr_conta_acumulada_anterior / 100) + 1) * (($nr_cota_mes / 100) + 1)) - 1) * 100;
                $ar_cota_mes[] = round($nr_cota_mes,2);
                $ar_cota_acumulada[] = round($nr_conta_acumulada,2);

                $ar_dt_mes[] = $ar_reg['dt_mes']; 
                $nr_anterior = $ar_reg['vl_cota'];
                $nr_conta_acumulada_anterior = $nr_conta_acumulada;
            }

            if(isset($ar_reg['dt_referencia']))
            {
                $dt_referencia = $ar_reg['dt_referencia'];
            }
            else
            {
                $dt_referencia = '';
            }

            $nr_conta++;
        }

        return array(
            'ar_cota_mes'       => $ar_cota_mes,
            'ar_cota_acumulada' => $ar_cota_acumulada,
            'dt_referencia'     => $dt_referencia
        );
    }

    public function listar()
    {
        $this->load->plugin('pchart');

		$this->load->model('projetos/rentabilidade_model');

		$indices = array(); 

		$cd_empresa_plano_empresa = $this->input->post('cd_plano_empresa', TRUE);
		$cd_empresa_plano		  = $this->input->post('cd_plano', TRUE);
		$nr_ano		   		      = $this->input->post('nr_ano', TRUE);	

		if(in_array(intval($cd_empresa_plano), array(2, 6)))
		{
			$indices = $this->rentabilidade_model->get_indice_ano(
                intval($cd_empresa_plano_empresa), 
                intval($cd_empresa_plano), 
                intval($nr_ano)
            );
		}
		elseif(intval($cd_empresa_plano) == 21)
		{
			$indices = $this->rentabilidade_model->get_indice_ano_inpel(
                intval($cd_empresa_plano_empresa), 
                intval($cd_empresa_plano), 
                intval($nr_ano)
            );
		}
		else if(intval($cd_empresa_plano) == 1)
		{
			$planos_patrocinadoras = $this->rentabilidade_model->get_planos_patrocinadoras(
                intval($cd_empresa_plano_empresa), 
                intval($cd_empresa_plano)
            );

            if(count($planos_patrocinadoras) > 0)
            {
    			if(intval($nr_ano) < 2011)
    			{
    				$indices = $this->rentabilidade_model->get_qt_razao_cota_indice_ano(
                        intval($planos_patrocinadoras['cd_empresa_financ']), 
                        intval($planos_patrocinadoras['cd_plano_financ']), 
                        intval($nr_ano)
                    );
    			}	
    			else
    			{
    				$indices = $this->rentabilidade_model->get_sc_calculo_cotas_indice_ano(
                        intval($planos_patrocinadoras['cd_empresa_financ']), 
                        intval($planos_patrocinadoras['cd_plano_financ']), 
                        intval($nr_ano)
                    );
    			}
            }
		}
		else
		{
			$indices = $this->rentabilidade_model->get_indice_ano_outras(
                intval($cd_empresa_plano_empresa), 
                intval($cd_empresa_plano), 
                intval($nr_ano)
            );
		}
			
		$data = $this->calcula_cotas($indices);

		$data['indices'] = $indices;
		$data['nr_ano']  = intval($nr_ano);

        $data['meses'] = array('Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');

        $i = 0;

        while($i <= 11)
        {
            $valores[0][] = (isset($data['ar_cota_mes'][$i]) ? $data['ar_cota_mes'][$i] : '');
            $valores[1][] = (isset($data['ar_cota_acumulada'][$i]) ? $data['ar_cota_acumulada'][$i] : '');
		
			$data['collection'][$i]['ar_cota_mes'] = (isset($data['ar_cota_mes'][$i]) ? $data['ar_cota_mes'][$i] : '');
			$data['collection'][$i]['ar_cota_acumulada'] = (isset($data['ar_cota_mes'][$i]) ? $data['ar_cota_acumulada'][$i] : '');
			$data['collection'][$i]['meses'] = $data['meses'][$i];
			
			$i++;
        }
		
        $data['imagem'] = '';

        if(count($indices) > 0)
        {
            $data['imagem'] = group_barchart($data['meses'], $valores, array('barra', 'linha'), array('Mensal', 'Acumulado'), 700, 400);
		}
		
		$this->load->view('planos/rentabilidade/index_result', $data);
    }
}
?>