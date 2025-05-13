<?php
class tipo_documentos_juridico extends Controller
{
	function __construct()
	{
		parent::Controller();
	}

	function form_busca_por_nome()
	{
		$this->load->helper('string');
		$data['id']=md5( random_string() );
		$data['jscallback']=$this->input->post('jscallback');
		$data['close']=$this->input->post('close');
		$this->load->view('ajax_simplemodal/tipo_documentos_juridico/busca_por_nome',$data);
	}

	function form_busca_por_nome_post()
	{
		$jscallback    = $this->input->post('jscallback');
		$descricao_doc = $this->input->post('descricao_doc');		

		$qr_sql = "
					SELECT DISTINCT dj.descricao, 
					       dj.cd_documento
		              FROM public.documentos_juridicos dj
		             WHERE UPPER(funcoes.remove_acento(dj.descricao)) LIKE UPPER(funcoes.remove_acento('%".trim(utf8_decode($descricao_doc))."%'))
		             ORDER BY dj.descricao
		          ";
		#echo $qr_sql;
				  
		$ob_resul = $this->db->query($qr_sql);
		$ar_reg   = $ob_resul->result_array();

		$body = array();
		$head = array('','Código', 'Nome');

		foreach($ar_reg as $item )
		{
		    $body[] = array(
		        "<a href='javascript:void(0);' onclick='$jscallback( ".$item['cd_documento']." )'>Selecionar</a>",
				$item['cd_documento'],
		        array($item['descricao'],'text-align:left;')
		    );
		}

		$this->load->helper('grid');
		$grid = new grid();
		$grid->head = $head;
		$grid->body = $body;
		echo $grid->render();
	}

	function nome()
	{
		$cd_tipo_doc = (int)$this->input->post('cd_tipo_doc');

		$sql = "
				SELECT DISTINCT dj.descricao, dj.cd_documento
				  FROM public.documentos_juridicos dj
				 WHERE dj.cd_documento=?
		       ";

		$query = $this->db->query( $sql, array(intval($cd_tipo_doc)) );
		$ret = "";
		if($query)
		{
			$row = $query->row_array();
			if($row)
			{
				$ret = $row["descricao"];
			}
		}

		echo $ret;
	}
}
?>