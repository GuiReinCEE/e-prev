<?php
class Geral extends Controller 
{
	function __construct()
	{
		parent::Controller();
	}
	
	function upload_multiplo()
	{
		if(CheckLogin())
		{
			$up_input                       = $this->input->post("up_campo", true);
			$config['file_name']            = $this->input->post("name", true);
			$config['upload_path']          = './up/'.$this->input->post("up_dir", true);
			$config['allowed_types']        = implode("|",getExtensaoPermitida()); //verifique o arquivo config/mime.php e app_helper.php
			$config['ignore_validate_mime'] = TRUE; 

			$this->load->library('upload', $config);

			if(!$this->upload->do_upload($up_input))
			{
				$error = array('error' => 
								array(
									"code" => "1",
									"message" => "Erro",
									"details" => utf8_encode(
																str_replace("<p>","",str_replace("</p>","",$this->upload->display_errors()))." [".$this->upload->file_ext."] [Tipo: ".$this->upload->file_type."]"
									                         )
									));
				echo json_encode($error);
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());
				echo json_encode($data);
			}
			
		}
	}	

	function upload($inputname="arquivo", $foldername="", $callback_function_sucesso_js="", $callback_function_falha_js="", $raiz="")
	{
		if(CheckLogin())
		{
			$config['upload_path'] = './up/'.$foldername;
			#$config['allowed_types'] = 'pdf|txt|doc|docx|xls|xlsx|jpg|png|bmp|gif|tif|mp3|msg|sql|csv|rar|zip';
			$config['allowed_types'] = implode("|",getExtensaoPermitida()); //verifique o arquivo config/mime.php e app_helper.php
			$config['remove_spaces'] = TRUE;
			$config['encrypt_name'] = TRUE;

			$this->load->library('upload', $config);

			if(!$this->upload->do_upload($inputname))
			{
				$error = array('error' => $this->upload->display_errors());

				if($callback_function_falha_js!='')
				{
					echo " <script> parent.$callback_function_falha_js('".$error['error']."\\n\\n- Extensão: ".$this->upload->file_ext."\\n\\n- Tipo: ".$this->upload->file_type."'); </script>";
				}
				else 
				{
					echo $error['error'];
				}
			}
			else
			{
				$data = array('upload_data' => $this->upload->data());
				$orig_name=$data['upload_data']['orig_name'];
				$file_name=$data['upload_data']['file_name'];
				if($callback_function_sucesso_js!='') echo " <script> parent.$callback_function_sucesso_js('$file_name|$orig_name'); </script>";
				else echo "Incluído com sucesso";
			}
		}
	}
	
	

	/**
	 * Busca na intranet, realiza uma busca nos menus existentes
	 * 
	 *
	 */
	function buscar()
	{
		$q = $this->db->query("
			SELECT *
			FROM projetos.menu 
			WHERE ( ds_href <> '#' AND ds_href<>'' AND ds_href is not null )

			AND
			(
				funcoes.remove_acento( UPPER(ds_menu) ) LIKE funcoes.remove_acento( UPPER('%' || " . $this->db->escape( $this->input->post("keyword") ) . " || '%') )
				OR UPPER(ds_href) LIKE UPPER('%' || " . $this->db->escape(  $this->input->post("keyword") ) . " || '%')
				OR UPPER(ds_resumo) LIKE UPPER('%' || " . $this->db->escape(  $this->input->post("keyword") ) . " || '%') 
			)
			AND dt_desativado IS NULL

			ORDER BY ds_menu;
		");
		$resultados = array();
		foreach($q->result() as $r)
		{
			$target = "";
			$onclick = "";

			if($r->ds_href=="#")
			{
				/*
				$link = 'javascript:getMenu('.$r->cd_menu.',\'\', \'\');';
				$label_link = "Ítem de Menu";
				*/
			}
			else if( substr($r->ds_href, 0, 7)=='http://' OR substr($r->ds_href, 0, 8)=='https://' )
			{
				$link = $r->ds_href;
				$target = "_blank";
				$label_link = $link;
			}
			else if( strpos( $r->ds_href,'.php') )
			{
				$protocolo = (isset($_SERVER['HTTPS']))?"https":"http";
				$link = $protocolo . "://" . $_SERVER['SERVER_NAME'] . "/cieprev/sysapp/application/migre/" . $r->ds_href;
				$label_link = $link;
			}
			else
			{
				$link = base_url() . 'index.php/' . $r->ds_href;
				$label_link = $link;
			}

			/*if($link!="")
			{*/
				$path = $this->montar_caminho_menu($r->cd_menu_pai);
				$resultados[sizeof($resultados)] = array( 'nome'=>$r->ds_menu
														, 'link'=>$link
														, 'label_link'=>$label_link
														, 'path'=>$path
														, 'resumo'=>$r->ds_resumo
														, 'target'=>$target
														);
			/*}*/
		}

		$data['resultados'] = $resultados;
		$data['keyword'] = $this->input->post("keyword");
		$this->load->view('home/busca', $data);
	}

	private function montar_caminho_menu( $cd_pai, $path = "" )
	{
		$q = $this->db->get_where('projetos.menu', array('cd_menu'=>$cd_pai));
		$r = $q->row();
		if($r->cd_menu_pai!='')
		{
			if($path!='')
			{
				$path = $r->ds_menu . '/' . $path;
			}
			else
			{
				$path = $r->ds_menu;
			}
			$path = $this->montar_caminho_menu( $r->cd_menu_pai, $path );
		}

		return $path; //$r->ds_menu;
	}

	public function criar_menu_para_eprev($cd_menu=0)
	{
		echo menu_extjs_start($cd_menu);
	}

	public function usuarios_dropdown_ajax()
	{
		if( ! CheckLogin() ) exit;
		
		$gerencia = $this->input->post("gerencia", TRUE);
		$combo_id = $this->input->post("combo_id", TRUE);
		
		$combo_id = (trim($combo_id) == '' ? 'cd_usuario' : $combo_id);
		
		$this->load->model('projetos/Usuarios_controledi');
		$collection = $this->Usuarios_controledi->select_dropdown_1($gerencia);
		
		$options = array();

		$options[""] = "Selecione";

		if( $collection!==FALSE )
		{
			foreach( $collection as $item )
			{
				$options[$item["value"]] = $item["text"];
			}
		}

		echo form_dropdown($combo_id, $options, array(), "id='$combo_id'");
		
		//echo "<script>$('#".$combo_id."').msDropDown();$('#".$combo_id."').hide();</script>";
	}

	public function empresas_dropdown_ajax()
	{
		if(!CheckLogin()) { exit; }

		$plano  = $this->input->post('plano', TRUE);
		$combo_id = $this->input->post("combo_id", TRUE);

		$plano    = (trim($plano)  == '' ? -1 : trim($plano));
		$combo_id = (trim($combo_id) == '' ? "cd_empresa" : trim($combo_id));

		$qr_sql = "
				SELECT p.cd_empresa AS value,
				       p.sigla AS text
				  FROM patrocinadoras p
				  JOIN planos_patrocinadoras pp
				    ON pp.cd_empresa = p.cd_empresa
				 WHERE pp.cd_plano = ".intval($plano)."
				 GROUP BY p.cd_empresa
				 ORDER BY sigla;";

		$collection = $this->db->query($qr_sql)->result_array();	

		$options = array();

		if(count($collection) != 1)
		{
			$options[''] = 'Selecione';
		}

		if(count($collection) > 0)
		{
			foreach($collection as $item)
			{
				$options[$item['value']] = $item['text'];
			}
		}

		echo form_dropdown($combo_id, $options, array(), 'id="$combo_id"');
	}

	public function planos_dropdown_ajax()
	{
		if( ! CheckLogin() ) { exit; }
		
		$empresa  = $this->input->post("empresa", TRUE);
		$combo_id = $this->input->post("combo_id", TRUE);
		
		$empresa  = (trim($empresa)  == "" ? -1 : trim($empresa));
		$combo_id = (trim($combo_id) == "" ? "cd_plano" : trim($combo_id));
		
		$query = $this->db->query("
									SELECT a.cd_plano AS value, 
									       a.descricao AS text 
									  FROM public.planos a 
									  JOIN public.planos_patrocinadoras b 
									    ON a.cd_plano=b.cd_plano 
								     WHERE b.cd_empresa=? 
									 ORDER BY a.descricao
								  ", array( ($empresa) ) );
		$collection = $query->result_array();
		
		$options = array();

		if(count($collection) != 1)
		{
			$options[""] = "Selecione";
		}

		if(count($collection) > 0)
		{
			foreach( $collection as $item )
			{
				$options[$item["value"]] = $item["text"];
			}
		}
		echo form_dropdown($combo_id, $options, array(), "id='$combo_id'");
	}

	public function carregar_dropdown()
	{
		if( ! CheckLogin() ) exit;

		$nome        = $this->input->post("nome");
		$tabela      = $this->input->post("tabela");
		$campo_valor = $this->input->post("campo_valor");
		$campo_texto = $this->input->post("campo_texto");
		$selecionado = $this->input->post("selecionado");
		$extra       = $this->input->post("extra");
		$where       = $this->input->post("where");
		$orderby     = $this->input->post("orderby");

		#print_r($_POST); #EXIT;
		
		echo form_dropdown_db($nome, array($tabela, $campo_valor, $campo_texto), $selecionado, $extra, $where, $orderby);
	}

	public function test()
	{
		// echo form_dropdown_db( "name", array('projetos.pre_venda_local', 'cd_pre_venda_local', 'ds_pre_venda_local'), "20" );
	}

	public function cadastro_simples($tabela="", $campo_pk="", $campo_texto="", $callback="", $fechar="")
	{
		if( ! CheckLogin() ) exit;

		$data['table']      = $tabela; // 'projetos.pre_venda_local';
		$data['field_pk']   = $campo_pk; // 'cd_pre_venda_local';
		$data['field_text'] = $campo_texto; // 'ds_pre_venda_local';
		$data['callback']   = $callback;
		$data['fechar']     = $fechar;
		$this->load->view( 'geral/cadastro_simples', $data );
	}

	public function cadastro_simples_salvar()
	{
		if(!CheckLogin())
		{
			echo "<script>alert('Você não está logado');</script>";
			exit;
		}

		#echo "<PRE>"; print_r($_POST); exit;
		
		$esquema_tabela  = $this->input->post('table', TRUE);
		$campo_descricao = $this->input->post('field_text', TRUE);
		$campo_pk        = $this->input->post('field_pk', TRUE);
		$callback        = $this->input->post('callback', TRUE);
		
		$arr = explode('.', $esquema_tabela);

		$table_schema = 'public';
		$table_name   = '';

		if(count($arr) > 1)
		{
			$table_schema = $arr[0];
			$table_name   = $arr[1];
		}
		else
		{
			$table_name = $arr[0];
		}

		$qr_sql = "
			SELECT COUNT(*) AS tl
              FROM information_schema.columns 
             WHERE column_name  = 'dt_alteracao'
               AND table_schema = '".trim($table_schema)."'
               AND table_name   = '".trim($table_name)."';";

        $row = $this->db->query($qr_sql)->row_array();


        if(intval($row['tl']) == 0)
        {
			$qr_sql = "
				INSERT INTO ".$esquema_tabela." 
					 (
						".$campo_descricao.", 
						dt_inclusao, 
						cd_usuario_inclusao
					 )
				VALUES 
					 (
						".str_escape(utf8_decode($this->input->post('descricao', TRUE))).", 
						CURRENT_TIMESTAMP, 
						".usuario_id()."
					 );";
		}
		else
		{
			$qr_sql = "
				INSERT INTO ".$esquema_tabela." 
					 (
						".$campo_descricao.", 
						dt_inclusao, 
						dt_alteracao,
						cd_usuario_inclusao,
						cd_usuario_alteracao
					 )
				VALUES 
					 (
						".str_escape(utf8_decode($this->input->post('descricao', TRUE))).", 
						CURRENT_TIMESTAMP, 
						CURRENT_TIMESTAMP, 
						".usuario_id().",
						".usuario_id()."
					 );";
		}

		#echo "<PRE>".$qr_sql."</PRE>";exit;
		$q  = $this->db->query($qr_sql);
		$id = $this->db->insert_id($esquema_tabela, $campo_pk);
		
		echo "<script>".$callback."(".$id.");</script>";
	}

	function pdf()
	{
		// $html=$this->load->view('teste', array(), true);
		$html=$this->input->post('html_pdf_export');
		$this->load->plugin('to_pdf');
		pdf_create($html, 'filename');
	} 

	/**
	 * Método auxiliar de form_helper.php para realizar uma consulta por CEP
	 * para o método form_default_cep.
	 * a ordem do retorno não deve ser alterada.
	 * deve ser retornada uma string definida pelo parametro enviado por post "return_type" 
	 * 
	 * se return_type for "string", deve retornar os valores separados por | na seguinte ordem
	 * CIDADE|UF|LOGRADOURO|BAIRRO
	 *
	 * se return_type for "json", retorna no formato json colocando nome as colunas: {cidade,uf,endereco,bairro}
	 *
	 * @params string cep
	 * @params string return_type
	 *
	 * @output string logradouro, cidade, uf, bairro para o CEP informado. dados retornados no seguinte formato: CIDADE|UF|LOGRADOURO|BAIRRO ou JSON {cidade,uf,endereo,bairro}
	 */
	function consultar_cep_ajax()
	{
		$cep = $this->input->post('cep');
		$return_type = $this->input->post('return_type');

		$query = $this->db->query("SELECT * FROM geografico.cep WHERE nr_cep=?", array( str_replace("-", "", $cep) ));

		if($query)
		{
			$row = $query->row_array();

			if(trim($return_type)=='string' || trim($return_type)=='')
			{
				$out = "";
				$out.=     $row["ds_localidade"];
				$out.= "|".$row["cd_uf"];
				$out.= "|".$row["tp_logradouro"]." ".$row["ds_logradouro"];
				$out.= "|".$row["ds_bairro_ini"];

				echo $out;
			}
			elseif(trim($return_type)=='json')
			{
				// echo json_encode( $row );
				$out = "cidade:'".$row["ds_localidade"]."',uf:'".$row["cd_uf"]."',endereco:'".$row["tp_logradouro"]." ".$row["ds_logradouro"]."',bairro:'".$row["ds_bairro_ini"]."'";

				echo "{ $out }";
			}
		}
	}
	
	function limpaFiltros()
	{
		$this->db->query("DELETE FROM public.ci_filtros WHERE ds_ip_usuario = '".$_SERVER['REMOTE_ADDR']."'");	
	}
	
	
	public function exportToCSV()
	{
		header("Content-type: application/octet-stream");
		header('Content-Disposition: attachment; filename="dados.csv"');
		$data = stripcslashes($this->input->post('obGridCSVExport'));
		echo $data; 		
		exit;
	}	
}
?>
