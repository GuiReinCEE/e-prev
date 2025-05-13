<?php
class Calendario extends Controller
{
	function __construct()
	{
		parent::Controller();
	}
	
	function index($ano = 0, $tipo = "F")
	{
		CheckLogin();
		
		$this->load->model('projetos/Calendario_model');
		$data = Array();
		$args = Array();
		$result = null;
		
		$data['ano']  = (intval($ano) == 0 ? date("Y") : intval($ano));
		$data['tipo'] = (trim($tipo) == "" ? "F" : trim($tipo));
		$data['ar_data'] = Array();
		$data['ar_tipo'] = Array();
		$data['ar_desc'] = Array();
		$data['ar_url'] = Array();
		
		$args['ano'] = (intval($ano) == 0 ? date("Y") : intval($ano));
		$args['tipo'] = (trim($tipo) == "" ? "F" : trim($tipo));
		
        $this->Calendario_model->datas($result, $args);
		$ar_reg = $result->result_array();		
		foreach($ar_reg  as $ar_item)
		{
			$data['ar_data'][] = $ar_item['dt_feriado'];
			$data['ar_tipo'][$ar_item['dt_feriado']] = $ar_item['tp_calendario'];
			$data['ar_desc'][$ar_item['dt_feriado']] = $ar_item['descricao'];
			$data['ar_url'][$ar_item['dt_feriado']] = $ar_item['ds_url'];
		}

		$this->load->view('home/calendario', $data);
	}
}
?>