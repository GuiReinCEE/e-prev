<?php
class ri_evento_institucional extends Controller
{
    function __construct()
    {
        parent::Controller();
    }

    function index()
    {
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{	
			$this->load->view('ecrm/ri_evento_institucional/index.php');
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
    }

    function listar()
    {
        CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{		
			$this->load->model('projetos/Eventos_institucionais_model');
			
			$result = null;
			$data = Array();
			$args = Array();

			$args["nome"]    = $this->input->post("nome", TRUE);
			$args["cd_tipo"] = 'EVEI';

			manter_filtros($args);

			$this->Eventos_institucionais_model->listar( $result, $args );
			$data['collection'] = $result->result_array();
			$this->load->view('ecrm/ri_evento_institucional/partial_result', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
    }

	function detalhe($cd = 0)
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{		
			$sql = "   
					SELECT e.cd_evento, 
						   TO_CHAR(e.dt_inicio,'DD/MM/YYYY') AS dt_inicio, 
						   TO_CHAR(e.dt_inicio,'HH24:MI') AS hr_inicio,
						   e.cd_tipo, 
						   e.nome, 
						   e.tipo_divulgacao, 
						   e.cd_cidade, 
						   e.dt_alteracao, 
						   e.local_evento, 
						   e.dt_fim, 
						   e.email_lembrete, 
						   e.lembrete_1hora, 
						   e.lembrete_vespera, 
						   e.texto_lembrete, 
						   e.agenda, 
						   e.dt_marcacao_agenda, 
						   e.dt_exclusao, 
						   e.usu_exclusao,
						   e.qt_inscricao,
						   e.texto_encerramento,
						   TO_CHAR(e.dt_ini_inscricao,'DD/MM/YYYY') AS dt_ini_inscricao, 
						   TO_CHAR(e.dt_ini_inscricao,'HH24:MI') AS hr_ini_inscricao,	
						   TO_CHAR(e.dt_fim_inscricao,'DD/MM/YYYY') AS dt_fim_inscricao, 
						   TO_CHAR(e.dt_fim_inscricao,'HH24:MI') AS hr_fim_inscricao,
                           email_texto,
                           email_assunto,
						   fl_acompanhante,
						   fl_arquivo,
						   fl_observacao,
						   ds_observacao,
						   e.fl_participante,
						   e.ar_participante_tipo,
						   e.participante_msg_valida
					  FROM projetos.eventos_institucionais e
				   ";
			$row = array();
			$query = $this->db->query( $sql . ' LIMIT 1 ' );
			$fields = $query->field_data();
			foreach( $fields as $field )
			{
				$row[$field->name] = '';
			}

			$ar_participante_tipo_checked = Array();
			if( intval($cd)>0 )
			{
				$sql .= " WHERE cd_evento={cd_evento} ";
				esc( "{cd_evento}", intval($cd), $sql );
				$query=$this->db->query($sql);
				$row=$query->row_array();
				
				$ar_participante_tipo_checked = explode(",",$row["ar_participante_tipo"]);
			}

			$data["ar_participante_tipo_checked"] = $ar_participante_tipo_checked;
			
			if($row) $data['row'] = $row;
			$this->load->view('ecrm/ri_evento_institucional/detalhe', $data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
	}

	function salvar()
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{
			$this->load->model('projetos/Eventos_institucionais_model');
			
			$result = null;
			$data = Array();
			$args = Array();		

			$args["cd_evento"]          = $this->input->post("cd_evento",TRUE);
			$args["nome"]               = $this->input->post("nome",TRUE);
			$args["dt_inicio"]          = $this->input->post("dt_inicio",TRUE);
			$args["hr_inicio"]          = $this->input->post("hr_inicio",TRUE);
			$args["qt_inscricao"]       = $this->input->post("qt_inscricao",TRUE);
			$args["dt_ini_inscricao"]   = $this->input->post("dt_ini_inscricao",TRUE);
			$args["hr_ini_inscricao"]   = $this->input->post("hr_ini_inscricao",TRUE);
			$args["dt_fim_inscricao"]   = $this->input->post("dt_fim_inscricao",TRUE);
			$args["hr_fim_inscricao"]   = $this->input->post("hr_fim_inscricao",TRUE);
			$args["cd_cidade"]          = $this->input->post("cd_cidade",TRUE);
			$args["local_evento"]       = $this->input->post("local_evento",TRUE);
			$args["email_texto"]        = $this->input->post("email_texto",TRUE);
			$args["email_assunto"]      = $this->input->post("email_assunto",TRUE);
			$args["fl_acompanhante"]    = $this->input->post("fl_acompanhante",TRUE);
			$args["fl_arquivo"]         = $this->input->post("fl_arquivo",TRUE);
			$args["fl_observacao"]      = $this->input->post("fl_observacao",TRUE);
			$args["ds_observacao"]      = $this->input->post("ds_observacao",TRUE);
			$args["fl_participante"]    = $this->input->post("fl_participante",TRUE);
			$args["texto_encerramento"] = $this->input->post("texto_encerramento",TRUE);
			$args["ar_participante_tipo"] = $this->input->post("ar_participante_tipo",TRUE);
			$args["participante_msg_valida"] = $this->input->post("participante_msg_valida",TRUE);
			$args["cd_usuario"]         = usuario_id();
			$args["cd_tipo"]            = 'EVEI';
		
			$cd_evento_new = $this->Eventos_institucionais_model->salvar($result, $args);
			$dir = "../upload/evento_institucional_".$cd_evento_new;
			#$dir = "/u/www/upload/evento_institucional_".$cd_evento_new;
			if(!is_dir($dir))
			{
				mkdir($dir);
			}
			
			redirect("ecrm/ri_evento_institucional/detalhe/".$cd_evento_new, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
	}

	function excluir($id)
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{		
			$sql = "
					UPDATE projetos.eventos_institucionais
					   SET dt_exclusao  = CURRENT_TIMESTAMP, 
					       usu_exclusao = {cd_usuario_exclusao}
			         WHERE md5(cd_evento::TEXT) = '{cd_evento}'
			       ";
			esc("{cd_usuario_exclusao}", usuario_id(), $sql, 'int');
			esc("{cd_evento}", $id, $sql, 'str');

			$query=$this->db->query($sql);

			redirect( 'ecrm/ri_evento_institucional', 'refresh' );
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}
	
    function imagem($cd_evento = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{
			$this->load->model('projetos/Eventos_institucionais_model');
			$args = Array();	
			$data = Array();
			$data['cd_evento'] = intval($cd_evento);
			
			if(intval($cd_evento) == 0)
			{
				exibir_mensagem("EVENTO NÃO INFORMADO");
			}
			else
			{
				$args['cd_evento'] = intval($cd_evento);
				$this->Eventos_institucionais_model->evento($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/ri_evento_institucional/imagem.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function salvarImagem()
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{
			$args = Array();
			$args["cd_evento"]     = $this->input->post("cd_evento", TRUE);
			$args["img_inscricao"] = $this->input->post("img_inscricao", TRUE);
			$args["img_confirma"]  = $this->input->post("img_confirma", TRUE);
			$args["img_encerra"]   = $this->input->post("img_encerra", TRUE);

			if(intval($args["cd_evento"]) > 0)
			{
				#### INSCRICAO ####
				if(trim($args["img_inscricao"]) != "")
				{
					$ar_tmp = explode(".",$args["img_inscricao"]);
					if(strtolower($ar_tmp[1]) == "jpg")
					{
						list($width, $height) = getimagesize("./up/evento_institucional/".$args["img_inscricao"]); 
						
						if($width > 750)
						{
							echo " 	<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>
									Tamanho atual<br>
									L = $width px<br>
									</div>
								 ";
							exit;
						}
						else
						{
							copy("./up/evento_institucional/".$args["img_inscricao"], "./../eletroceee/img/evento_institucional/".$args["img_inscricao"]);
						}
					}
					else
					{
						echo "
								<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>A extensão do arquivo não é .jpg
								</div>
							 ";
						exit;
					}
					unlink("./up/evento_institucional/".$args["img_inscricao"]);
					
					#### UPDATE ####
					$qr_sql = " 
								UPDATE projetos.eventos_institucionais
								   SET img_inscricao = ".(trim($args['img_inscricao']) == "" ? "NULL" : "'".$args['img_inscricao']."'")."
								 WHERE cd_evento = ".intval($args['cd_evento'])."			
							  ";		
					$this->db->query($qr_sql);					
				}
				
				#### CONFIRMAÇÃO ####
				if(trim($args["img_confirma"]) != "")
				{
					$ar_tmp = explode(".",$args["img_confirma"]);
					if(strtolower($ar_tmp[1]) == "jpg")
					{
						list($width, $height) = getimagesize("./up/evento_institucional/".$args["img_confirma"]); 
						
						if($width > 750)
						{
							echo " 	<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>
									Tamanho atual<br>
									L = $width px<br>
									</div>
								 ";
							exit;
						}
						else
						{
							copy("./up/evento_institucional/".$args["img_confirma"], "./../eletroceee/img/evento_institucional/".$args["img_confirma"]);
						}
					}
					else
					{
						echo "
								<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>A extensão do arquivo não é .jpg
								</div>
							 ";
						exit;
					}
					unlink("./up/evento_institucional/".$args["img_confirma"]);
					
					#### UPDATE ####
					$qr_sql = " 
								UPDATE projetos.eventos_institucionais
								   SET img_confirma = ".(trim($args['img_confirma']) == "" ? "NULL" : "'".$args['img_confirma']."'")."
								 WHERE cd_evento = ".intval($args['cd_evento'])."			
							  ";		
					$this->db->query($qr_sql);					
				}				
				
				#### ENCERRADO ####
				if(trim($args["img_encerra"]) != "")
				{
					$ar_tmp = explode(".",$args["img_encerra"]);
					if(strtolower($ar_tmp[1]) == "jpg")
					{
						list($width, $height) = getimagesize("./up/evento_institucional/".$args["img_encerra"]); 
						
						if($width > 750)
						{
							echo " 	<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>
									Tamanho atual<br>
									L = $width px<br>
									</div>
								 ";
							exit;
						}
						else
						{
							copy("./up/evento_institucional/".$args["img_encerra"], "./../eletroceee/img/evento_institucional/".$args["img_encerra"]);
						}
					}
					else
					{
						echo "
								<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>A extensão do arquivo não é .jpg
								</div>
							 ";
						exit;
					}
					unlink("./up/evento_institucional/".$args["img_encerra"]);
					
					#### UPDATE ####
					$qr_sql = " 
								UPDATE projetos.eventos_institucionais
								   SET img_encerra = ".(trim($args['img_encerra']) == "" ? "NULL" : "'".$args['img_encerra']."'")."
								 WHERE cd_evento = ".intval($args['cd_evento'])."			
							  ";		
					$this->db->query($qr_sql);						
				}				
				
				redirect( "ecrm/ri_evento_institucional/imagem/".$args["cd_evento"], "refresh");	
			}
			else
			{
				echo "ERRO - Evento não identificado";
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}

    function certificado($cd_evento = 0)
    {
		CheckLogin();
	
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{
			$this->load->model('projetos/Eventos_institucionais_model');
			$args = Array();	
			$data = Array();
			$data['cd_evento'] = intval($cd_evento);
			
			if(intval($cd_evento) == 0)
			{
				exibir_mensagem("EVENTO NÃO INFORMADO");
			}
			else
			{
				$args['cd_evento'] = intval($cd_evento);
				$this->Eventos_institucionais_model->evento($result, $args);
				$data['row'] = $result->row_array();	
			}
			$this->load->view('ecrm/ri_evento_institucional/certificado.php',$data);
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}
    }	
	
	function salvarCertificado()
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{
			$args = Array();
			$args["cd_evento"]                = $this->input->post("cd_evento", TRUE);
			$args["certificado_img_frente"]   = $this->input->post("certificado_img_frente", TRUE);
			$args["certificado_img_verso"]    = $this->input->post("certificado_img_verso", TRUE);
			$args["certificado_nome_pos_x"]   = $this->input->post("certificado_nome_pos_x", TRUE);
			$args["certificado_nome_pos_y"]   = $this->input->post("certificado_nome_pos_y", TRUE);
			$args["certificado_nome_tamanho"] = $this->input->post("certificado_nome_tamanho", TRUE);
			$args["certificado_nome_fonte"]   = $this->input->post("certificado_nome_fonte", TRUE);
			$args["certificado_nome_cor"]     = $this->input->post("certificado_nome_cor", TRUE);
			$args["certificado_nome_alinha"]  = $this->input->post("certificado_nome_alinha", TRUE);

			if(intval($args["cd_evento"]) > 0)
			{
				#### UPDATE ####
				$qr_sql = " 
							UPDATE projetos.eventos_institucionais
							   SET certificado_nome_pos_x   = ".(intval($args['certificado_nome_pos_x'])   == 0 ? "DEFAULT"  : intval($args['certificado_nome_pos_x'])).",
							       certificado_nome_pos_y   = ".(intval($args['certificado_nome_pos_y'])   == 0 ? "DEFAULT"  : intval($args['certificado_nome_pos_y'])).",
							       certificado_nome_tamanho = ".(intval($args['certificado_nome_tamanho']) == 0 ? "DEFAULT"  : intval($args['certificado_nome_tamanho'])).",
							       certificado_nome_fonte   = ".(trim($args['certificado_nome_fonte'])     == "" ? "DEFAULT" : "'".trim($args['certificado_nome_fonte'])."'").",
							       certificado_nome_cor     = ".(trim($args['certificado_nome_cor'])       == "" ? "DEFAULT" : "'#".trim($args['certificado_nome_cor'])."'").",
							       certificado_nome_alinha  = ".(trim($args['certificado_nome_alinha'])    == "" ? "DEFAULT" : "'".trim($args['certificado_nome_alinha'])."'")."
							 WHERE cd_evento = ".intval($args['cd_evento'])."			
						  ";		
				$this->db->query($qr_sql);					
				
				#### FRENTE ####
				if(trim($args["certificado_img_frente"]) != "")
				{
					$ar_tmp = explode(".",$args["certificado_img_frente"]);
					if($ar_tmp[1] == "jpg")
					{
						copy("./up/evento_institucional/".$args["certificado_img_frente"], "./../eletroceee/img/evento_institucional/".$args["certificado_img_frente"]);
					}
					else
					{
						echo "
								<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>A extensão do arquivo não é .jpg
								</div>
							 ";
						exit;
					}
					unlink("./up/evento_institucional/".$args["certificado_img_frente"]);
					
					#### UPDATE ####
					$qr_sql = " 
								UPDATE projetos.eventos_institucionais
								   SET certificado_img_frente = ".(trim($args['certificado_img_frente']) == "" ? "NULL" : "'".$args['certificado_img_frente']."'")."
								 WHERE cd_evento = ".intval($args['cd_evento'])."			
							  ";		
					$this->db->query($qr_sql);					
				}
				
				#### VERSO ####
				if(trim($args["certificado_img_verso"]) != "")
				{
					$ar_tmp = explode(".",$args["certificado_img_frente"]);
					if($ar_tmp[1] == "jpg")
					{
						copy("./up/evento_institucional/".$args["certificado_img_verso"], "./../eletroceee/img/evento_institucional/".$args["certificado_img_verso"]);
					}
					else
					{
						echo "
								<div style='margin: 100px; font-family: Verdana, Tahoma, Arial; font-size: 16pt; font-weight: bold;'>
									ERRO:<BR><BR>A extensão do arquivo não é .jpg
								</div>
							 ";
						exit;
					}
					unlink("./up/evento_institucional/".$args["certificado_img_verso"]);
					
					#### UPDATE ####
					$qr_sql = " 
								UPDATE projetos.eventos_institucionais
								   SET certificado_img_verso = ".(trim($args['certificado_img_verso']) == "" ? "NULL" : "'".$args['certificado_img_verso']."'")."
								 WHERE cd_evento = ".intval($args['cd_evento'])."			
							  ";		
					$this->db->query($qr_sql);					
				}					
				
				redirect( "ecrm/ri_evento_institucional/certificado/".$args["cd_evento"], "refresh");	
			}
			else
			{
				echo "ERRO - Evento não identificado";
			}
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}		
	}	
	
	function emailCertificadoEvento($cd_evento = 0)
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{		
			$this->load->model('projetos/Eventos_institucionais_model');
			$result = null;
			$args   = Array();	
			$data   = Array();
			$args['cd_evento'] = intval($cd_evento);

			$this->Eventos_institucionais_model->emailCertificadoEvento($args);			
			
			redirect( "ecrm/ri_evento_institucional/certificado/".$args['cd_evento'], "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
	}
	
	function emailCertificadoEventoIndividual($cd_eventos_institucionais_inscricao = 0)
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{		
			$this->load->model('projetos/Eventos_institucionais_model');
			$result = null;
			$args   = Array();	
			$data   = Array();
			$args['cd_eventos_institucionais_inscricao'] = intval($cd_eventos_institucionais_inscricao);

			$this->Eventos_institucionais_model->emailCertificadoEventoIndividual($args);			
			
			redirect( "ecrm/evento_institucional_inscricao", "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
	}	
	
	function emailConfirma($cd = 0)
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{		
			$qr_sql = "
						SELECT rotinas.email_evento_confirma(".intval($cd).");
					  ";
			$this->db->query($qr_sql);			
			
			redirect( "ecrm/ri_evento_institucional/detalhe/".$cd, "refresh");
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}			
	}	
        
	function codigo_barras_6183_OLD($cd_evento)
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{
			$this->load->model('projetos/eventos_institucionais_model');
			
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_evento'] = $cd_evento;
			
			$this->eventos_institucionais_model->listar_cracha_barras( $result, $args );
			$collection = $result->result_array();
			
			$this->load->plugin('fpdf');
			
			#ETIQUETA PIMACO 6183
			$ob_pdf = new PDF('P','mm','Letter'); 
			$ob_pdf->SetMargins(5,14,5);
			$ob_pdf->AddPage();
			
			$ob_pdf->SetFont('Courier','I',6);
			$ob_pdf->Text(5,5, "Pimaco 6183");
			$ob_pdf->SetFont('Courier','B',6);			
			$ob_pdf->Text(5,8, "ETIQUETA PARA ENVELOPE");			

			$nr_x = 0;
			$nr_y = 0;
			$nr_conta = 0;
			$nr_conta_x = 0;
		   
			foreach ($collection as $ar_reg) 
			{
				$ar_nome = explode(" ",trim($ar_reg['nome']));
				$NOME = $ar_nome[0];

				$NOME_COMP = trim($ar_reg['nome']);

				$pos = strpos($NOME_COMP, " ");
				$NOME_COMP = substr($NOME_COMP,$pos);	


				$NOME_COMP_0 = "";
				$NOME_COMP_1 = "";
				$NOME_COMP_2 = "";

				if(strlen($NOME_COMP) > 30)
				{
					$NOME_COMP_0 = substr($NOME_COMP,0,30);
					$pos = strrpos($NOME_COMP_0, " ");
					$NOME_COMP_0 = substr($NOME_COMP,0,$pos);
					$NOME_COMP_1 = substr($NOME_COMP,$pos);

					if(strlen($NOME_COMP_1) > 30)
					{
						$EMP_TMP = $NOME_COMP_1;
						$NOME_COMP_1 = substr($EMP_TMP,0,30);
						$pos = strrpos($NOME_COMP_1, " ");
						$NOME_COMP_1 = substr($EMP_TMP,0,$pos);
						$NOME_COMP_2 = substr($EMP_TMP,$pos);				
					}
				}
				else
				{
					$NOME_COMP_0 = $NOME_COMP;
				}

				$EMPRESA = $ar_reg['empresa'];
				$EMPRESA_0 = "";
				$EMPRESA_1 = "";
				$EMPRESA_2 = "";

				if(strlen($EMPRESA) > 40)
				{
					$EMPRESA_0 = substr($EMPRESA,0,40);
					$pos = strrpos($EMPRESA_0, " ");
					$EMPRESA_0 = substr($EMPRESA,0,$pos);
					$EMPRESA_1 = substr($EMPRESA,$pos);

					if(strlen($EMPRESA_1) > 40)
					{
						$EMP_TMP = $EMPRESA_1;
						$EMPRESA_1 = substr($EMP_TMP,0,40);
						$pos = strrpos($EMPRESA_1, " ");
						$EMPRESA_1 = substr($EMP_TMP,0,$pos);
						$EMPRESA_2 = substr($EMP_TMP,$pos);				
					}
				}
				else
				{
					$EMPRESA_0 = $EMPRESA;
				}

				$nr_conta++;
				$nr_conta_x++;

				$ob_pdf->SetXY($ob_pdf->GetX() + $nr_x, $ob_pdf->GetY() + $nr_y);
				$ob_pdf->SetFont('Arial','B',20);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME)/2)),$ob_pdf->GetY() + 12, $NOME);	
				$ob_pdf->SetFont('Courier','',14);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_0)/2)),$ob_pdf->GetY() + 17, $NOME_COMP_0);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_1)/2)),$ob_pdf->GetY() + 20.5, $NOME_COMP_1);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_2)/2)),$ob_pdf->GetY() + 23, $NOME_COMP_2);	

				$ob_pdf->SetFont('Courier','',10);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($EMPRESA_0)/2)),$ob_pdf->GetY() + 26, $EMPRESA_0);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($EMPRESA_1)/2)),$ob_pdf->GetY() + 28.5, $EMPRESA_1);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($EMPRESA_2)/2)),$ob_pdf->GetY() + 31, $EMPRESA_2);	
				$ob_pdf->EAN13($ob_pdf->GetX() + 12,$ob_pdf->GetY() + 33,$ar_reg['cd_barra']);
				
				$ob_pdf->SetFont('Courier','',8);
				$ob_pdf->Text($ob_pdf->GetX() + 54, $ob_pdf->GetY() + 36, "CPF");				
				$ob_pdf->SetFont('Arial','',16);
				$ob_pdf->Text($ob_pdf->GetX() + 54, $ob_pdf->GetY() + 42, $ar_reg['cpf']);	
				
				
				#### MARCADOS #####
				$ob_pdf->SetFont('Courier','',18);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth("***************")/2)),$ob_pdf->GetY() + 32, (intval($ar_reg['tp_inscrito']) > 0 ? "***************" : ""));	

				if($nr_conta_x == 2)
				{
					$ob_pdf->SetX(5);
					$nr_x = 0;
					$nr_y = 50.8;
					$nr_conta_x = 0;
				}
				else
				{
					$nr_x = 101.6;
					$nr_y = 0;
				}

				if($nr_conta == 10)
				{
					$ob_pdf->AddPage();
					$ob_pdf->SetMargins(5,14,5);
					$ob_pdf->SetFont('Courier','I',6);
					$ob_pdf->Text(5,5, "Pimaco 6183");
					$ob_pdf->SetFont('Courier','B',6);			
					$ob_pdf->Text(5,8, "ETIQUETA PARA ENVELOPE");					
					$nr_conta = 0;
					$nr_x = 0;
					$nr_y = 0;
				}
			}

			$ob_pdf->Output();
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
	}
        
	function cracha_6183($cd_evento)
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{
			$this->load->model('projetos/eventos_institucionais_model');
			
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_evento'] = $cd_evento;
			
			$this->eventos_institucionais_model->listar_cracha_barras( $result, $args );
			$collection = $result->result_array();
			
			$this->load->plugin('fpdf');
			
			#ETIQUETA PIMACO 6183
			$ob_pdf = new PDF('P','mm','Letter'); 
			$ob_pdf->SetMargins(5,14,5);
			$ob_pdf->AddPage();
			
			$ob_pdf->SetFont('Courier','I',6);
			$ob_pdf->Text(5,5, "Pimaco 6183");
			$ob_pdf->SetFont('Courier','B',6);			
			$ob_pdf->Text(5,8, "ETIQUETA PARA CRACHÁ");		

			$nr_x = 0;
			$nr_y = 0;
			$nr_conta = 0;
			$nr_conta_x = 0;
					
			foreach ($collection as $ar_reg) 
			{
				$ar_nome = explode(" ",trim($ar_reg['nome']));
				$NOME = $ar_nome[0];

				$NOME_COMP = trim($ar_reg['nome']);

				$pos = strpos($NOME_COMP, " ");
				$NOME_COMP = substr($NOME_COMP,$pos);	

				$NOME_COMP_0 = "";
				$NOME_COMP_1 = "";
				$NOME_COMP_2 = "";
	
				if(strlen($NOME_COMP) > 30)
				{
					$NOME_COMP_0 = substr($NOME_COMP,0,30);
					$pos = strrpos($NOME_COMP_0, " ");
					$NOME_COMP_0 = substr($NOME_COMP,0,$pos);
					$NOME_COMP_1 = substr($NOME_COMP,$pos);

					if(strlen($NOME_COMP_1) > 30)
					{
						$EMP_TMP = $NOME_COMP_1;
						$NOME_COMP_1 = substr($EMP_TMP,0,30);
						$pos = strrpos($NOME_COMP_1, " ");
						$NOME_COMP_1 = substr($EMP_TMP,0,$pos);
						$NOME_COMP_2 = substr($EMP_TMP,$pos);				
					}
				}
				else
				{
					$NOME_COMP_0 = $NOME_COMP;
				}		

				$EMPRESA = $ar_reg['empresa'];
				$EMPRESA_0 = "";
				$EMPRESA_1 = "";
				$EMPRESA_2 = "";
	
				if(strlen($EMPRESA) > 40)
				{
					$EMPRESA_0 = substr($EMPRESA,0,40);
					$pos = strrpos($EMPRESA_0, " ");
					$EMPRESA_0 = substr($EMPRESA,0,$pos);
					$EMPRESA_1 = substr($EMPRESA,$pos);

					if(strlen($EMPRESA_1) > 40)
					{
						$EMP_TMP = $EMPRESA_1;
						$EMPRESA_1 = substr($EMP_TMP,0,40);
						$pos = strrpos($EMPRESA_1, " ");
						$EMPRESA_1 = substr($EMP_TMP,0,$pos);
						$EMPRESA_2 = substr($EMP_TMP,$pos);				
					}
				}
				else
				{
					$EMPRESA_0 = $EMPRESA;
				}

				$nr_conta++;
				$nr_conta_x++;

				$ob_pdf->SetXY($ob_pdf->GetX() + $nr_x, $ob_pdf->GetY() + $nr_y);
				$ob_pdf->SetFont('Arial','B',30);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME)/2)),$ob_pdf->GetY() + 13, $NOME);	
				$ob_pdf->SetFont('Courier','',16);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_0)/2)),$ob_pdf->GetY() + 20, $NOME_COMP_0);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_1)/2)),$ob_pdf->GetY() + 25.5, $NOME_COMP_1);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_2)/2)),$ob_pdf->GetY() + 29, $NOME_COMP_2);	

				$ob_pdf->SetFont('Courier','',10);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($EMPRESA_0)/2)),$ob_pdf->GetY() + 32, $EMPRESA_0);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($EMPRESA_1)/2)),$ob_pdf->GetY() + 36, $EMPRESA_1);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($EMPRESA_2)/2)),$ob_pdf->GetY() + 40, $EMPRESA_2);	
				
				#### MARCADOS #####
				$ob_pdf->SetFont('Courier','',18);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth("***************")/2)),$ob_pdf->GetY() + 44, (intval($ar_reg['tp_inscrito']) > 0 ? "***************" : ""));					
	
				if($nr_conta_x == 2)
				{
					$ob_pdf->SetX(5);
					$nr_x = 0;
					$nr_y = 50.8;
					$nr_conta_x = 0;
				}
				else
				{
					$nr_x = 101.6;
					$nr_y = 0;
				}

				if($nr_conta == 10)
				{
					$ob_pdf->AddPage();
					$ob_pdf->SetMargins(5,14,5);
					$ob_pdf->SetFont('Courier','I',6);
					$ob_pdf->Text(5,5, "Pimaco 6183");
					$ob_pdf->SetFont('Courier','B',6);			
					$ob_pdf->Text(5,8, "ETIQUETA PARA CRACHÁ");		
					
					$nr_conta = 0;
					$nr_x = 0;
					$nr_y = 0;
				}
			}

			$ob_pdf->Output();
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
	}
	
	function codigo_barras_6182_OLD($cd_evento)
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{
			$this->load->model('projetos/eventos_institucionais_model');
			
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_evento'] = $cd_evento;
			
			$this->eventos_institucionais_model->listar_cracha_barras( $result, $args );
			$collection = $result->result_array();
			
			$this->load->plugin('fpdf');
			$this->load->plugin('qrcode');
			
			#ETIQUETA PIMACO 6182
			$ob_pdf = new PDF('P','mm','Letter'); 
			$ob_pdf->SetMargins(5,22,5);
			$ob_pdf->AddPage();
			
			$ob_pdf->SetFont('Courier','I',6);
			$ob_pdf->Text(5,5, "Pimaco 6182");
			$ob_pdf->SetFont('Courier','B',6);			
			$ob_pdf->Text(5,8, "ETIQUETA PARA ENVELOPE");

			$nr_x = 0;
			$nr_y = 0;
			$nr_conta = 0;
			$nr_conta_x = 0;
			$nr_lim_ini_pg = 18;
			
			$ob_pdf->SetXY(0,$nr_lim_ini_pg);		
					
			foreach ($collection as $ar_reg) 
			{
				$ar_nome = explode(" ",trim($ar_reg['nome']));
				$NOME = $ar_nome[0];

				$NOME_COMP = trim($ar_reg['nome']);

				$pos = strpos($NOME_COMP, " ");
				$NOME_COMP = substr($NOME_COMP,$pos);	

				$NOME_COMP_0 = "";
				$NOME_COMP_1 = "";
				$NOME_COMP_2 = "";
	
				$nome_tam = 30;
				if(strlen($NOME_COMP) > $nome_tam)
				{
					$NOME_COMP_0 = substr($NOME_COMP,0,$nome_tam);
					$pos = strrpos($NOME_COMP_0, " ");
					$NOME_COMP_0 = substr($NOME_COMP,0,$pos);
					$NOME_COMP_1 = substr($NOME_COMP,$pos);

					if(strlen($NOME_COMP_1) > $nome_tam)
					{
						$EMP_TMP = $NOME_COMP_1;
						$NOME_COMP_1 = substr($EMP_TMP,0,$nome_tam);
						$pos = strrpos($NOME_COMP_1, " ");
						$NOME_COMP_1 = substr($EMP_TMP,0,$pos);
						$NOME_COMP_2 = substr($EMP_TMP,$pos);				
					}
				}
				else
				{
					$NOME_COMP_0 = $NOME_COMP;
				}		

				$nr_conta++;
				$nr_conta_x++;
				
				$ob_pdf->SetXY($ob_pdf->GetX() + $nr_x, $ob_pdf->GetY() + $nr_y);
				$ob_pdf->SetFont('Arial','B',22);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME)/2)),$ob_pdf->GetY() + 13, $NOME);	
				$ob_pdf->SetFont('Courier','',12);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_0)/2)),$ob_pdf->GetY() + 17, $NOME_COMP_0);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_1)/2)),$ob_pdf->GetY() + 20.5, $NOME_COMP_1);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_2)/2)),$ob_pdf->GetY() + 23, $NOME_COMP_2);	
				$ob_pdf->EAN13($ob_pdf->GetX() + 12, $ob_pdf->GetY() + 22,$ar_reg['cd_barra']);
				
				$ob_pdf->SetFont('Courier','',8);
				$ob_pdf->Text($ob_pdf->GetX() + 54, $ob_pdf->GetY() + 25, "CPF");				
				$ob_pdf->SetFont('Arial','',16);
				$ob_pdf->Text($ob_pdf->GetX() + 54, $ob_pdf->GetY() + 31, $ar_reg['cpf']);	
	
				if($nr_conta_x == 2)
				{
					$ob_pdf->SetX(0);
					$nr_x = 0;
					$nr_y = 34;
					$nr_conta_x = 0;
				}
				else
				{
					$nr_x = 104.4;
					$nr_y = 0;
				}

				if($nr_conta == 14)
				{
					$ob_pdf->AddPage();
					$ob_pdf->SetMargins(5,22,5);
					$ob_pdf->SetFont('Courier','I',6);
					$ob_pdf->Text(5,5, "Pimaco 6182");
					$ob_pdf->SetFont('Courier','B',6);			
					$ob_pdf->Text(5,8, "ETIQUETA PARA ENVELOPE");				
					
					$ob_pdf->SetXY(0,$nr_lim_ini_pg);
					$nr_conta = 0;
					$nr_x = 0;
					$nr_y = 0;
				}
			}

			$ob_pdf->Output();
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
	}	
	
	function cracha_6182($cd_evento)
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{
			$this->load->model('projetos/eventos_institucionais_model');
			
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_evento'] = $cd_evento;
			
			$this->eventos_institucionais_model->listar_cracha_barras( $result, $args );
			$collection = $result->result_array();
			
			$this->load->plugin('fpdf');
			
			#ETIQUETA PIMACO 6182
			$ob_pdf = new PDF('P','mm','Letter'); 
			$ob_pdf->SetMargins(5,22,5);
			$ob_pdf->AddPage();
			
			$ob_pdf->SetFont('Courier','I',6);
			$ob_pdf->Text(5,5, "Pimaco 6182");
			$ob_pdf->SetFont('Courier','B',6);			
			$ob_pdf->Text(5,8, "ETIQUETA PARA CRACHÁ");			

			$nr_x = 0;
			$nr_y = 0;
			$nr_conta = 0;
			$nr_conta_x = 0;
					
			$ob_pdf->SetXY(0,20.5);		
					
			foreach ($collection as $ar_reg) 
			{
				$ar_nome = explode(" ",trim($ar_reg['nome']));
				$NOME = $ar_nome[0];

				$NOME_COMP = trim($ar_reg['nome']);

				$pos = strpos($NOME_COMP, " ");
				$NOME_COMP = substr($NOME_COMP,$pos);	

				$NOME_COMP_0 = "";
				$NOME_COMP_1 = "";
				$NOME_COMP_2 = "";
	
				$nome_tam = 22;
				if(strlen($NOME_COMP) > $nome_tam)
				{
					$NOME_COMP_0 = substr($NOME_COMP,0,$nome_tam);
					$pos = strrpos($NOME_COMP_0, " ");
					$NOME_COMP_0 = substr($NOME_COMP,0,$pos);
					$NOME_COMP_1 = substr($NOME_COMP,$pos);

					if(strlen($NOME_COMP_1) > $nome_tam)
					{
						$EMP_TMP = $NOME_COMP_1;
						$NOME_COMP_1 = substr($EMP_TMP,0,$nome_tam);
						$pos = strrpos($NOME_COMP_1, " ");
						$NOME_COMP_1 = substr($EMP_TMP,0,$pos);
						$NOME_COMP_2 = substr($EMP_TMP,$pos);				
					}
				}
				else
				{
					$NOME_COMP_0 = $NOME_COMP;
				}		

				$nr_conta++;
				$nr_conta_x++;
				
				$ob_pdf->SetXY($ob_pdf->GetX() + $nr_x, $ob_pdf->GetY() + $nr_y);
				$ob_pdf->SetFont('Arial','B',36);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME)/2)),$ob_pdf->GetY() + 13, $NOME);	
				$ob_pdf->SetFont('Courier','',21);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_0)/2)),$ob_pdf->GetY() + 20, $NOME_COMP_0);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_1)/2)),$ob_pdf->GetY() + 26, $NOME_COMP_1);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_2)/2)),$ob_pdf->GetY() + 32, $NOME_COMP_2);	
	
				if($nr_conta_x == 2)
				{
					$ob_pdf->SetX(0);
					$nr_x = 0;
					$nr_y = 34;
					$nr_conta_x = 0;
				}
				else
				{
					$nr_x = 104.4;
					$nr_y = 0;
				}

				if($nr_conta == 14)
				{
					$ob_pdf->AddPage();
					$ob_pdf->SetMargins(5,22,5);
					$ob_pdf->SetFont('Courier','I',6);
					$ob_pdf->Text(5,5, "Pimaco 6182");
					$ob_pdf->SetFont('Courier','B',6);			
					$ob_pdf->Text(5,8, "ETIQUETA PARA CRACHÁ");	
					
					$ob_pdf->SetXY(0,20.5);
					$nr_conta = 0;
					$nr_x = 0;
					$nr_y = 0;
				}
			}

			$ob_pdf->Output();
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
	}	
	
	function lista_presente($cd_evento)
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{
			$this->load->model('projetos/eventos_institucionais_model');
			
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_evento'] = $cd_evento;
			
			$this->eventos_institucionais_model->listar_cracha_barras( $result, $args );
			$ar_resul = $result->result_array();
			
			$this->load->plugin('fpdf');
			$this->load->plugin('qrcode');
			
			$ob_pdf = new PDF();
			$ob_pdf->AddFont('segoeuil');
			$ob_pdf->AddFont('segoeuib');				
			$ob_pdf->SetNrPag(true);
			$ob_pdf->SetMargins(10, 14, 5);
			$ob_pdf->header_exibe = true;
			$ob_pdf->header_logo = true;
			$ob_pdf->header_titulo = true;
			$ob_pdf->header_titulo_texto = "LISTA DE PRESENÇA";	
			
			$ob_pdf->AddPage();
			$ob_pdf->SetY($ob_pdf->GetY() + 1);				
		   
			foreach($ar_resul as $ar_reg) 
			{
				$ob_pdf->SetY($ob_pdf->GetY() + 8);	
				
				if($ob_pdf->GetY() > 250)
				{
					$ob_pdf->AddPage();
				}
				
				$qrcode = new QRcode(utf8_encode($ar_reg['cd_barra']), "L");
				$qrcode->disableBorder();
				$qrcode->displayFPDF($ob_pdf,182.5,$ob_pdf->GetY() -2,15);	

				$ob_pdf->SetFont('segoeuib','',18);
				$ob_pdf->MultiCell(150, 8,trim($ar_reg['nome']),0);	
				$ob_pdf->SetFont('segoeuil','',9);
				$ob_pdf->MultiCell(150, 7,trim($ar_reg['cpf']." - ".$ar_reg['empresa']),0);	

				$ob_pdf->SetFont('segoeuil','',36);
				$ob_pdf->Text(160,$ob_pdf->GetY() -7,'[   ]');				
			}

			$ob_pdf->Output();
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
	}	
	
	function codigo_barras_6182($cd_evento)
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{
			$this->load->model('projetos/eventos_institucionais_model');
			
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_evento'] = $cd_evento;
			
			$this->eventos_institucionais_model->listar_cracha_barras( $result, $args );
			$collection = $result->result_array();
			
			$this->load->plugin('fpdf');
			$this->load->plugin('qrcode');
			
			#ETIQUETA PIMACO 6182
			$ob_pdf = new PDF('P','mm','Letter'); 
			$ob_pdf->SetMargins(5,22,5);
			$ob_pdf->AddPage();
			
			$ob_pdf->SetFont('Courier','I',6);
			$ob_pdf->Text(5,5, "Pimaco 6182");
			$ob_pdf->SetFont('Courier','B',6);			
			$ob_pdf->Text(5,8, "ETIQUETA PARA ENVELOPE");

			$nr_x = 0;
			$nr_y = 0;
			$nr_conta = 0;
			$nr_conta_x = 0;
			$nr_lim_ini_pg = 18;
			
			$ob_pdf->SetXY(0,$nr_lim_ini_pg);		
					
			foreach ($collection as $ar_reg) 
			{
				$ar_nome = explode(" ",trim($ar_reg['nome']));
				$NOME = $ar_nome[0];

				$NOME_COMP = trim($ar_reg['nome']);

				$pos = strpos($NOME_COMP, " ");
				$NOME_COMP = substr($NOME_COMP,$pos);	

				$NOME_COMP_0 = "";
				$NOME_COMP_1 = "";
				$NOME_COMP_2 = "";
	
				$nome_tam = 30;
				if(strlen($NOME_COMP) > $nome_tam)
				{
					$NOME_COMP_0 = substr($NOME_COMP,0,$nome_tam);
					$pos = strrpos($NOME_COMP_0, " ");
					$NOME_COMP_0 = substr($NOME_COMP,0,$pos);
					$NOME_COMP_1 = substr($NOME_COMP,$pos);

					if(strlen($NOME_COMP_1) > $nome_tam)
					{
						$EMP_TMP = $NOME_COMP_1;
						$NOME_COMP_1 = substr($EMP_TMP,0,$nome_tam);
						$pos = strrpos($NOME_COMP_1, " ");
						$NOME_COMP_1 = substr($EMP_TMP,0,$pos);
						$NOME_COMP_2 = substr($EMP_TMP,$pos);				
					}
				}
				else
				{
					$NOME_COMP_0 = $NOME_COMP;
				}		

				$nr_conta++;
				$nr_conta_x++;
				
				$ob_pdf->SetXY($ob_pdf->GetX() + $nr_x, $ob_pdf->GetY() + $nr_y);
				$ob_pdf->SetFont('Arial','B',22);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME)/2)),$ob_pdf->GetY() + 13, $NOME);	
				$ob_pdf->SetFont('Courier','',12);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_0)/2)),$ob_pdf->GetY() + 17, $NOME_COMP_0);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_1)/2)),$ob_pdf->GetY() + 20.5, $NOME_COMP_1);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_2)/2)),$ob_pdf->GetY() + 23, $NOME_COMP_2);	
				
				$qrcode = new QRcode(utf8_encode($ar_reg['cd_barra']), "L");
				$qrcode->disableBorder();
				$qrcode->displayFPDF($ob_pdf, $ob_pdf->GetX() + 12, $ob_pdf->GetY() + 21, 14);					
				
				$ob_pdf->SetFont('Courier','',8);
				$ob_pdf->Text($ob_pdf->GetX() + 54, $ob_pdf->GetY() + 25, "CPF");				
				$ob_pdf->SetFont('Arial','',16);
				$ob_pdf->Text($ob_pdf->GetX() + 54, $ob_pdf->GetY() + 31, $ar_reg['cpf']);	
	
				if($nr_conta_x == 2)
				{
					$ob_pdf->SetX(0);
					$nr_x = 0;
					$nr_y = 34;
					$nr_conta_x = 0;
				}
				else
				{
					$nr_x = 104.4;
					$nr_y = 0;
				}

				if($nr_conta == 14)
				{
					$ob_pdf->AddPage();
					$ob_pdf->SetMargins(5,22,5);
					$ob_pdf->SetFont('Courier','I',6);
					$ob_pdf->Text(5,5, "Pimaco 6182");
					$ob_pdf->SetFont('Courier','B',6);			
					$ob_pdf->Text(5,8, "ETIQUETA PARA ENVELOPE");				
					
					$ob_pdf->SetXY(0,$nr_lim_ini_pg);
					$nr_conta = 0;
					$nr_x = 0;
					$nr_y = 0;
				}
			}

			$ob_pdf->Output();
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
	}	
	
	function codigo_barras_6183($cd_evento)
	{
		CheckLogin();
		if(gerencia_in(array('GE','AC','GRC', 'GC')))
		{
			$this->load->model('projetos/eventos_institucionais_model');
			
			$args   = array();
			$data   = array();
			$result = null;	
		
			$args['cd_evento'] = $cd_evento;
			
			$this->eventos_institucionais_model->listar_cracha_barras( $result, $args );
			$collection = $result->result_array();
			
			$this->load->plugin('fpdf');
			$this->load->plugin('qrcode');
			
			#ETIQUETA PIMACO 6183
			$ob_pdf = new PDF('P','mm','Letter'); 
			$ob_pdf->SetMargins(5,14,5);
			$ob_pdf->AddPage();
			
			$ob_pdf->SetFont('Courier','I',6);
			$ob_pdf->Text(5,5, "Pimaco 6183");
			$ob_pdf->SetFont('Courier','B',6);			
			$ob_pdf->Text(5,8, "ETIQUETA PARA ENVELOPE");			

			$nr_x = 0;
			$nr_y = 0;
			$nr_conta = 0;
			$nr_conta_x = 0;
		   
			foreach ($collection as $ar_reg) 
			{
				$ar_nome = explode(" ",trim($ar_reg['nome']));
				$NOME = $ar_nome[0];

				$NOME_COMP = trim($ar_reg['nome']);

				$pos = strpos($NOME_COMP, " ");
				$NOME_COMP = substr($NOME_COMP,$pos);	


				$NOME_COMP_0 = "";
				$NOME_COMP_1 = "";
				$NOME_COMP_2 = "";

				if(strlen($NOME_COMP) > 30)
				{
					$NOME_COMP_0 = substr($NOME_COMP,0,30);
					$pos = strrpos($NOME_COMP_0, " ");
					$NOME_COMP_0 = substr($NOME_COMP,0,$pos);
					$NOME_COMP_1 = substr($NOME_COMP,$pos);

					if(strlen($NOME_COMP_1) > 30)
					{
						$EMP_TMP = $NOME_COMP_1;
						$NOME_COMP_1 = substr($EMP_TMP,0,30);
						$pos = strrpos($NOME_COMP_1, " ");
						$NOME_COMP_1 = substr($EMP_TMP,0,$pos);
						$NOME_COMP_2 = substr($EMP_TMP,$pos);				
					}
				}
				else
				{
					$NOME_COMP_0 = $NOME_COMP;
				}

				$EMPRESA = $ar_reg['empresa'];
				$EMPRESA_0 = "";
				$EMPRESA_1 = "";
				$EMPRESA_2 = "";

				if(strlen($EMPRESA) > 40)
				{
					$EMPRESA_0 = substr($EMPRESA,0,40);
					$pos = strrpos($EMPRESA_0, " ");
					$EMPRESA_0 = substr($EMPRESA,0,$pos);
					$EMPRESA_1 = substr($EMPRESA,$pos);

					if(strlen($EMPRESA_1) > 40)
					{
						$EMP_TMP = $EMPRESA_1;
						$EMPRESA_1 = substr($EMP_TMP,0,40);
						$pos = strrpos($EMPRESA_1, " ");
						$EMPRESA_1 = substr($EMP_TMP,0,$pos);
						$EMPRESA_2 = substr($EMP_TMP,$pos);				
					}
				}
				else
				{
					$EMPRESA_0 = $EMPRESA;
				}

				$nr_conta++;
				$nr_conta_x++;

				$ob_pdf->SetXY($ob_pdf->GetX() + $nr_x, $ob_pdf->GetY() + $nr_y);
				$ob_pdf->SetFont('Arial','B',20);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME)/2)),$ob_pdf->GetY() + 12, $NOME);	
				$ob_pdf->SetFont('Courier','',14);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_0)/2)),$ob_pdf->GetY() + 17, $NOME_COMP_0);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_1)/2)),$ob_pdf->GetY() + 20.5, $NOME_COMP_1);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($NOME_COMP_2)/2)),$ob_pdf->GetY() + 23, $NOME_COMP_2);	

				$ob_pdf->SetFont('Courier','',10);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($EMPRESA_0)/2)),$ob_pdf->GetY() + 26, $EMPRESA_0);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($EMPRESA_1)/2)),$ob_pdf->GetY() + 28.5, $EMPRESA_1);	
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth($EMPRESA_2)/2)),$ob_pdf->GetY() + 31, $EMPRESA_2);	
				#$ob_pdf->EAN13($ob_pdf->GetX() + 12,$ob_pdf->GetY() + 33,$ar_reg['cd_barra']);
				
				$qrcode = new QRcode(utf8_encode($ar_reg['cd_barra']), "L");
				$qrcode->disableBorder();
				$qrcode->displayFPDF($ob_pdf, $ob_pdf->GetX() + 12, $ob_pdf->GetY() + 32, 14);					
				
				$ob_pdf->SetFont('Courier','',8);
				$ob_pdf->Text($ob_pdf->GetX() + 54, $ob_pdf->GetY() + 36, "CPF");				
				$ob_pdf->SetFont('Arial','',16);
				$ob_pdf->Text($ob_pdf->GetX() + 54, $ob_pdf->GetY() + 42, $ar_reg['cpf']);	
				
				
				#### MARCADOS #####
				$ob_pdf->SetFont('Courier','',18);
				$ob_pdf->Text($ob_pdf->GetX() + (50.5 - ($ob_pdf->GetStringWidth("***************")/2)),$ob_pdf->GetY() + 32, (intval($ar_reg['tp_inscrito']) > 0 ? "***************" : ""));	

				if($nr_conta_x == 2)
				{
					$ob_pdf->SetX(5);
					$nr_x = 0;
					$nr_y = 50.8;
					$nr_conta_x = 0;
				}
				else
				{
					$nr_x = 101.6;
					$nr_y = 0;
				}

				if($nr_conta == 10)
				{
					$ob_pdf->AddPage();
					$ob_pdf->SetMargins(5,14,5);
					$ob_pdf->SetFont('Courier','I',6);
					$ob_pdf->Text(5,5, "Pimaco 6183");
					$ob_pdf->SetFont('Courier','B',6);			
					$ob_pdf->Text(5,8, "ETIQUETA PARA ENVELOPE");					
					$nr_conta = 0;
					$nr_x = 0;
					$nr_y = 0;
				}
			}

			$ob_pdf->Output();
		}
		else
		{
			exibir_mensagem("ACESSO NÃO PERMITIDO");
		}				
	}	
}
?>