<?php
class avaliacao_habilidade extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

        $this->load->view('cadastro/avaliacao_habilidade/index.php');
    }

    function listar()
    {
        if(CheckLogin())
        {
	        $this->load->model('projetos/Habilidades_model');
	
	        $data['collection'] = array();
	        $result = null;
	
	        // --------------------------
			// filtros ...
	
			$args=array();
	
			$args["descricao"] = $this->input->post("descricao", TRUE);
	
			manter_filtros($args);
	
			// --------------------------
			// listar ...
	
	        $this->Habilidades_model->listar( $result, $args );
	
			$data['collection'] = $result->result_array();
	
	        if( $result )
	        {
	            $data['collection'] = $result->result_array();
	        }
	
	        // --------------------------
	
	        $this->load->view('cadastro/avaliacao_habilidade/partial_result', $data);
        }
    }

	function detalhe($cd=0)
	{
		$sql = " SELECT * FROM projetos.habilidades ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE codigo={codigo} ";
			esc( "{codigo}", intval($cd), $sql );
			$query=$this->db->query($sql);
			$row=$query->row_array();
		}

		if($row) $data['row'] = $row;
		$this->load->view('cadastro/avaliacao_habilidade/detalhe', $data);
	}

	function salvar()
	{
		CheckLogin();

		$codigo=$this->input->post('codigo', TRUE);

		$args["descricao"] = $this->input->post("descricao",TRUE);
		$args["obs"] = $this->input->post("obs",TRUE);
		$args["codigo"] = $this->input->post("codigo",TRUE);

		if(intval($codigo)==0)
		{
			$sql="
				INSERT INTO projetos.habilidades ( descricao 
				, obs 
				) VALUES ( '{descricao}' 
				, '{obs}' 
				)
			";
		}
		else
		{
			$sql="
				UPDATE projetos.habilidades SET 
				descricao = '{descricao}' 
				, obs = '{obs}' 
				 WHERE 
				codigo = {codigo} 
			";
		}

		esc("{descricao}", $args["descricao"], $sql, "str", FALSE);
		esc("{obs}", $args["obs"], $sql, "str", FALSE);
		esc("{codigo}", $args["codigo"], $sql, "int", FALSE);

		$query = $this->db->query($sql);

		redirect( "cadastro/avaliacao_habilidade", "refresh" );
	}
}
?>