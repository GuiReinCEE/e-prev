<?php
class software extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();

		// Liberado apenas para usuários da GI
		if( !gerencia_in(array('GI')) )
		{ 
			exibir_mensagem("Esse programa não está disponível para seu usuário."); 
		}
		else
		{
			$this->load->view('cadastro/software/index.php');
		}
    }

    function listar()
    {
        CheckLogin();

		// Liberado apenas para usuários da GI
		if( !gerencia_in(array('GI')) ) 
		{ 
			exibir_mensagem("Esse programa não está disponível para seu usuário."); 
		}
		else
		{
			$this->load->model('projetos/Programas_model');

			$data['collection'] = array();
			$result = null;

			// --------------------------
			// filtros ...

			$args=array();

			$args["programa"] = $this->input->post("programa", TRUE);

			// --------------------------
			// listar ...

			$this->Programas_model->listar( $result, $args );

			$data['collection'] = $result->result_array();

			if( $result )
			{
				$data['collection'] = $result->result_array();
			}

			// --------------------------

			$this->load->view('cadastro/software/partial_result', $data);
		}
    }

	function salvar()
	{
		CheckLogin();

		$duplicado = false;

		$codigo=$this->input->post('programa_md5', TRUE);

		$args["definicao"] = $this->input->post("definicao",TRUE);
		$args["programa"] = $this->input->post("programa",TRUE);
		$args["cd_divisao"] = $this->input->post("cd_divisao",TRUE);
		$args["tipo_programa"] = $this->input->post("tipo_programa",TRUE);
		$args["fabricante"] = $this->input->post("fabricante",TRUE);
		$args["tipo_licenciamento"] = $this->input->post("tipo_licenciamento",TRUE);
		$args["num_licencas"] = $this->input->post("num_licencas",TRUE);
		$args["programa"] = $this->input->post("programa",TRUE);
		
		if(trim($codigo)==md5(''))
		{
			$constr = $this->db->query("SELECT COUNT(*) AS q FROM projetos.programas WHERE md5(programa)='$codigo'")->row_array();

			if(intval($constr)>0)
			{
				$duplicado = true;
			}

			$sql="
				INSERT INTO projetos.programas
				(
				definicao
				, programa
				, cd_divisao
				, tipo_programa
				, fabricante
				, tipo_licenciamento
				, num_licencas
				, dt_cadastro
				)
				VALUES 
				(
				'{definicao}'
				, '{programa}'
				, '{cd_divisao}'
				, '{tipo_programa}'
				, '{fabricante}'
				, '{tipo_licenciamento}'
				, {num_licencas}
				, CURRENT_DATE
				)
			";
		}
		else
		{
			$sql="
			UPDATE projetos.programas 

			SET 

			 programa = '{programa}' 
			, definicao = '{definicao}' 
			, cd_divisao = '{cd_divisao}'
			, tipo_programa = '{tipo_programa}'
			, fabricante = '{fabricante}' 
			, tipo_licenciamento = '{tipo_licenciamento}'
			, num_licencas = {num_licencas} 

			WHERE md5(programa) = '{programa_md5}'
			";
		}

		esc("{definicao}", $args["definicao"], $sql, "str", FALSE);
		esc("{programa}", $args["programa"], $sql, "str", FALSE);
		esc("{cd_divisao}", $args["cd_divisao"], $sql, "str", FALSE);
		esc("{tipo_programa}", $args["tipo_programa"], $sql, "str", FALSE);
		esc("{fabricante}", $args["fabricante"], $sql, "str", FALSE);
		esc("{tipo_licenciamento}", $args["tipo_licenciamento"], $sql, "str", FALSE);
		esc("{num_licencas}", $args["num_licencas"], $sql, "int", FALSE);

		esc("{programa_md5}", $codigo, $sql, "str", FALSE);

		if( !$duplicado )
		{
			$query = $this->db->query($sql);

			if( $query )
			{
				// echo $sql;
				redirect( "cadastro/software/detalhe/".$codigo, "refresh" );
			}
			else
			{
				exibir_mensagem("Desculpe, ocorreu uma falha, vamos corrigir, tente novamente em alguns minutos!");
			}
		}
		else
		{
			exibir_mensagem("Um software com esse nome já existe, não é possível cadastrar outro software com o mesmo nome!");
		}
	}

	function detalhe($cd='')
	{
		$sql = " SELECT * FROM projetos.programas ";
		$row=array();
		$query = $this->db->query( $sql . ' LIMIT 1 ' );
		$fields = $query->field_data();
		foreach( $fields as $field )
		{
			$row[$field->name] = '';
		}

		if( trim($cd)>'' )
		{
			$sql .= " WHERE md5(programa)='{programa}' ";
			esc( "{programa}", $cd, $sql );
			$query=$this->db->query($sql);
			$row=$query->row_array();
		}

		if($row) $data['row'] = $row;
		$this->load->view('cadastro/software/detalhe', $data);
	}

}
