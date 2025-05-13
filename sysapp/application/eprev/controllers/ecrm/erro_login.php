<?php
class erro_login extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

		if( !gerencia_in( array('GAP','GI') ) )
		{
			exibir_mensagem( "Usuário sem acesso para visualizar essa página." );
		}
		else
		{
	        $this->load->view('ecrm/erro_login/index.php');
		}
    }

    function listar()
    {
        CheckLogin();

		if( !gerencia_in( array('GAP','GI') ) )
		{
			exibir_mensagem( "Usuário sem acesso para visualizar essa página." );
		}
		else
		{
			$this->load->model('projetos/Erro_login_model');

			$data['collection'] = array();
			$result = null;

			// --------------------------
			// filtros ...

			$args=array();

			$args["cd_registro_empregado"] = intval($this->input->post("cd_registro_empregado", TRUE));

			manter_filtros($args);

			// --------------------------
			// listar ...

			$this->Erro_login_model->listar( $result, $args );

			$data['collection'] = $result->result_array();

			if( $result )
			{
				$data['collection'] = $result->result_array();
			}

			// --------------------------

			$this->load->view('ecrm/erro_login/partial_result', $data);
		}
    }

	function detalhe($cd=0)
	{
		$sql = "  ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( intval($cd)>0 )
		{
			$sql .= " WHERE ={} ";
			esc( "{}", intval($cd), $sql );
			$query=$this->db->query($sql);
			$row=$query->row_array();
		}

		if($row) $data['row'] = $row;
		$this->load->view('ecrm/erro_login/detalhe', $data);
	}

	function salvar()
	{
		CheckLogin();

		$codigo=$this->input->post('', TRUE);

		

		if(intval($codigo)==0)
		{
			$sql="
			
			";
		}
		else
		{
			$sql="
			
			";
		}

		

		$query = $this->db->query($sql);

		redirect( "ecrm/erro_login", "refresh" );
	}

	function excluir($id)
	{
		CheckLogin();

		$sql = "
		UPDATE 
		SET dt_exclusao=current_timestamp, cd_usuario_exclusao={cd_usuario_exclusao}
		WHERE md5(::varchar)='{}'
		";
		esc("{cd_usuario_exclusao}", usuario_id(), $sql, 'int');
		esc("{}", $id, $sql, 'str');

		$query=$this->db->query($sql);

		// echo $sql;

		redirect( 'ecrm/erro_login', 'refresh' );
	}
}
?>