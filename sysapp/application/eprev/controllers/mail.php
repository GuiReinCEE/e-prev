<?php
class mail extends Controller
{
    function __construct()
    {
        parent::Controller();
		$this->load->library('email');
    }
	
    function enviar($token = "", $cd_email = 0)
    {
		if(($token == md5('fundacaoCEEEenviaEmail@c8ml09')) and (intval($cd_email) > 0))
		{
			//http://10.63.255.150/cieprev/index.php/mail/email_mkt/index/dbaf55a59190fa527dc82e3af0e13e34/2399085
			//"dbaf55a59190fa527dc82e3af0e13e34"
			//2399085
			
			$qr_sql = "
						SELECT cd_email, 
							   dt_envio, 
							   de, 
							   responder,
							   para, 
							   cc, 
							   cco, 
							   assunto, 
							   texto, 
							   dt_email_enviado, 
							   dt_schedule_email, 
							   arquivo_anexo, 
							   div_solicitante,
							   COALESCE(tp_email, 'F') AS tp_email,
							   COALESCE(formato,'TEXT') AS formato
						  FROM projetos.envia_emails
						 WHERE cd_email         = ".intval($cd_email)."
						   AND dt_email_enviado IS NULL 
						   AND COALESCE(dt_schedule_email,dt_envio) <= CURRENT_TIMESTAMP
			          ";
			$ob_res = $this->db->query($qr_sql);			   
			$ar_reg = $ob_res->row_array();			
			
			if(count($ar_reg) > 0)
			{
				$tp_email = (strtoupper(trim($ar_reg['tp_email'])) == "" ? "F" : strtoupper(trim($ar_reg['tp_email'])));
				$ar_config["F"] = array("usuario" => "fundacao", "senha" => "daniel00", "email" => "fundacao@eletroceee.com.br");
				$ar_config["A"] = array("usuario" => "atendimento", "senha" => "@a123456", "email" => "atendimento@eletroceee.com.br");
				
				$cfg = Array();
				$cfg['protocol']     = 'smtp';
				$cfg['smtp_host']    = '10.63.255.8';
				$cfg['smtp_port']    = 25;
				$cfg['smtp_timeout'] = 5;
				$cfg['smtp_user']    = $ar_config[$tp_email]["usuario"];
				$cfg['smtp_pass']    = $ar_config[$tp_email]["senha"];
				$cfg['wordwrap']     = TRUE;
				$cfg['validate']     = TRUE;
				$cfg['charset']      = "utf-8";
				#$cfg['mailtype']     = 'text'; 
				#$cfg['mailtype']     = 'html'; 
				#$cfg['mailtype']     = (strtoupper(trim($ar_reg['formato'])) == "HTML" ? 'html' : 'text');
				
				$cfg['mailtype']     = ((in_array(strtoupper(trim($ar_reg['formato'])), array("HTML","TEXT_HTML"))) ? 'html' : 'text');
				
				$cfg['newline']      = "\r\n";
				#$cfg['send_multipart'] = FALSE;  

				
				$this->email->initialize($cfg);				
				
				#### DE ####
				$this->email->from($ar_config[$tp_email]["email"], trim($ar_reg['de']));
				
				#### RESPONDER PARA ####
				if(trim($ar_reg['responder']) != "")
				{
					$this->email->reply_to(trim($ar_reg['responder']));
				}
				
				#### PARA ####
				if(trim($ar_reg['para']) != "")
				{
					$ar_para = explode(";",str_replace(" ", "",$ar_reg['para']));
					if(count($ar_para) > 0)
					{
						$this->email->to($ar_para);
					}
				}
				
				#### CC ####
				if(trim($ar_reg['cc']) != "")
				{				
					$ar_cc = explode(";",str_replace(" ", "",$ar_reg['cc']));
					if(count($ar_cc) > 0)
					{
						$this->email->cc($ar_cc);
					}	
				}
				
				#### CCO ####
				if(trim($ar_reg['cco']) != "")
				{					
					$ar_cco = explode(";",str_replace(" ", "",$ar_reg['cco']));
					if(count($ar_cco) > 0)
					{
						$this->email->bcc($ar_cco);
					}				
				}				
				
				#### ASSUNTO ####
				$this->email->subject(utf8_encode($ar_reg['assunto']." - Msg: ".$ar_reg['cd_email']));
				
				#### CONTEUDO ####
				if(strtoupper(trim($ar_reg['formato'])) == "HTML")
				{
					#$texto = '<font face="Courier New, verdana, tahoma, arial">'.$texto.'</font>';
					$texto = $ar_reg['texto']."<br><br><br>".'<font face="Courier New, verdana, tahoma, arial" size="1">Id desta mensagem: '.$ar_reg['cd_email'].'</font>';
					$texto.= '<img src="http://www.e-prev.com.br/t/'.$ar_reg['cd_email'].'.png" border="0" width="1" height="1">';
				}
				else if(strtoupper(trim($ar_reg['formato'])) == "TEXT_HTML")
				{
					#$texto = '<font face="Courier New, verdana, tahoma, arial">'.$texto.'</font>';
					$texto = "<pre>".$ar_reg['texto']."</pre><br><br><br>".'<font face="Courier New, verdana, tahoma, arial" size="1">Id desta mensagem: '.$ar_reg['cd_email'].'</font>';
					$texto = nl2br($texto);
				}				
				else
				{
					$texto = $ar_reg['texto']."\n\n\nId desta mensagem: ".$ar_reg['cd_email'];
				}
				
				$this->email->message(utf8_encode($texto));
				
				$this->email->set_alt_message(utf8_encode("Por favor verifique seu leitor de email. Id desta mensagem: ".$ar_reg['cd_email']));		

				
				#### ANEXO ####
				//$this->email->attach("./up/documento_recebido/fb04a3e5d657a7f3f8f6fe7eb545b609.pdf");
				
				#### ENVIA EMAIL ####
				if($this->email->send())
				{
					#echo "<pre>OK: ENVIADO</pre>";
					$qr_sql = "
								UPDATE projetos.envia_emails 
								   SET dt_email_enviado = CAST(timeofday() AS TIMESTAMP)
								 WHERE cd_email = ".intval($ar_reg['cd_email']).";
							  ";
					$this->db->query($qr_sql);
					
					$qr_sql = "					
								INSERT INTO projetos.envia_emails_debug
								     (
										cd_email,
										retorno
									 )
								VALUES
								     (
										".intval($ar_reg['cd_email']).",
										".str_escape(br2nl($this->email->print_debugger()))."
									 );
					          ";
					$this->db->query($qr_sql);
					
					$ar_retorno = array("STATUS" => "OK", "CD_EMAIL" => intval($ar_reg['cd_email']), "RETORNO" => "ENVIADO");
					echo json_encode($ar_retorno);						
				}
				else
				{
					#echo "<pre>ERRO: NAO ENVIADO</pre>";
					$qr_sql = "
								UPDATE projetos.envia_emails 
								   SET dt_email_enviado = CAST(timeofday() AS TIMESTAMP),
								       fl_retornou      = 'N'
								 WHERE cd_email = ".intval($ar_reg['cd_email']).";
							  ";
					$this->db->query($qr_sql);								 
					
					$qr_sql = "			
								INSERT INTO projetos.envia_emails_debug
								     (
										cd_email,
										retorno
									 )
								VALUES
								     (
										".intval($ar_reg['cd_email']).",
										".str_escape(br2nl($this->email->print_debugger()))."
									 );
					          ";
					$this->db->query($qr_sql);	

					$ar_retorno = array("STATUS" => "ERRO", "CD_EMAIL" => intval($ar_reg['cd_email']), "RETORNO" => "NAO ENVIADO");
					echo json_encode($ar_retorno);						
				}
			}
			else
			{
				#echo "<pre>ERRO: NAO HA EMAIL PARA ENVIO</pre>";
				$ar_retorno = array("STATUS" => "ERRO", "CD_EMAIL" => intval($cd_email), "RETORNO" => "NAO HA EMAIL PARA ENVIO");
				echo json_encode($ar_retorno);					
			}
		}
		else
		{
			#echo "<pre>ERRO: TOKEN OU CODIGO DO EMAIL INVALIDO</pre>";
			$ar_retorno = array("STATUS" => "ERRO", "CD_EMAIL" => intval($cd_email), "RETORNO" => "TOKEN OU CODIGO DO EMAIL INVALIDO");
			echo json_encode($ar_retorno);			
		}		
    }
	
    function enviarDivulgacao($token = "", $cd_divulgacao = 0)
    {
		if(($token == md5('fundacaoCEEEenviaEmail@c8ml09')) and (intval($cd_divulgacao) > 0))
		{
			//http://10.63.255.150/cieprev/index.php/mail/email_mkt/index/dbaf55a59190fa527dc82e3af0e13e34/2399085
			//"dbaf55a59190fa527dc82e3af0e13e34"
			//2399085
			
			$qr_sql = "
						SELECT cd_email, 
							   dt_envio, 
							   de, 
							   responder,
							   para, 
							   cc, 
							   cco, 
							   assunto, 
							   texto, 
							   dt_email_enviado, 
							   dt_schedule_email, 
							   arquivo_anexo, 
							   div_solicitante,
							   COALESCE(tp_email, 'F') AS tp_email,
							   COALESCE(formato,'TEXT') AS formato
						  FROM projetos.envia_emails
						 WHERE cd_divulgacao   = ".intval($cd_divulgacao)."
						   AND dt_email_enviado IS NULL 
						   AND COALESCE(dt_schedule_email,dt_envio) <= CURRENT_TIMESTAMP
						 LIMIT 20
			          ";
			$ob_res = $this->db->query($qr_sql);			   
			$ar_reg_email = $ob_res->result_array();			
			
			if(count($ar_reg_email) > 0)
			{
				$tp_email = (strtoupper(trim($ar_reg_email[0]['tp_email'])) == "" ? "F" : strtoupper(trim($ar_reg_email[0]['tp_email'])));
				$ar_config["F"] = array("usuario" => "fundacao", "senha" => "daniel00", "email" => "fundacao@eletroceee.com.br");
				$ar_config["A"] = array("usuario" => "atendimento", "senha" => "@a123456", "email" => "atendimento@eletroceee.com.br");					
				
				$cfg = Array();
				$cfg['protocol']     = 'smtp';
				$cfg['smtp_host']    = '10.63.255.8';
				$cfg['smtp_port']    = 25;
				$cfg['smtp_timeout'] = 5;
				$cfg['smtp_user']    = $ar_config[$tp_email]["usuario"];
				$cfg['smtp_pass']    = $ar_config[$tp_email]["senha"];
				$cfg['wordwrap']     = TRUE;
				$cfg['validate']     = TRUE;
				$cfg['charset']      = "utf-8";
				$cfg['mailtype']     = ((in_array(strtoupper(trim($ar_reg_email[0]['formato'])), array("HTML","TEXT_HTML"))) ? 'html' : 'text');
				$cfg['newline']      = "\r\n";
				$this->email->initialize($cfg);	
			
				echo br().date("Y-m-d H:i:s").br();
				foreach ($ar_reg_email as $ar_reg)	
				{
					echo br();
					
					#### DE ####
					$this->email->from($ar_config[$tp_email]["email"], trim($ar_reg['de']));
					
					#### RESPONDER PARA ####
					if(trim($ar_reg['responder']) != "")
					{
						$this->email->reply_to(trim($ar_reg['responder']));
					}
					
					#### PARA ####
					if(trim($ar_reg['para']) != "")
					{
						$ar_para = explode(";",str_replace(" ", "",$ar_reg['para']));
						if(count($ar_para) > 0)
						{
							$this->email->to($ar_para);
						}
					}
					
					#### CC ####
					if(trim($ar_reg['cc']) != "")
					{				
						$ar_cc = explode(";",str_replace(" ", "",$ar_reg['cc']));
						if(count($ar_cc) > 0)
						{
							$this->email->cc($ar_cc);
						}	
					}
					
					#### CCO ####
					if(trim($ar_reg['cco']) != "")
					{					
						$ar_cco = explode(";",str_replace(" ", "",$ar_reg['cco']));
						if(count($ar_cco) > 0)
						{
							$this->email->bcc($ar_cco);
						}		
					}
					
					#### ASSUNTO ####
					$this->email->subject(utf8_encode($ar_reg['assunto']." - Msg: ".$ar_reg['cd_email']));
					
					#### CONTEUDO ####
					if(strtoupper(trim($ar_reg['formato'])) == "HTML")
					{
						#$texto = '<font face="Courier New, verdana, tahoma, arial">'.$texto.'</font>';
						$texto = $ar_reg['texto']."<br><br><br>".'<font face="Courier New, verdana, tahoma, arial" size="1">Id desta mensagem: '.$ar_reg['cd_email'].'</font>';
						$texto.= '<img src="http://www.e-prev.com.br/t/'.$ar_reg['cd_email'].'.png" border="0" width="1" height="1">';
					}
					else if(strtoupper(trim($ar_reg['formato'])) == "TEXT_HTML")
					{
						#$texto = '<font face="Courier New, verdana, tahoma, arial">'.$texto.'</font>';
						$texto = "<pre>".$ar_reg['texto']."</pre><br><br><br>".'<font face="Courier New, verdana, tahoma, arial" size="1">Id desta mensagem: '.$ar_reg['cd_email'].'</font>';
						$texto = nl2br($texto);
					}				
					else
					{
						$texto = $ar_reg['texto']."\n\n\nId desta mensagem: ".$ar_reg['cd_email'];
					}
					
					$this->email->message(utf8_encode($texto));
					
					$this->email->set_alt_message(utf8_encode("Por favor verifique seu leitor de email. Id desta mensagem: ".$ar_reg['cd_email']));		

					
					#### ANEXO ####
					//$this->email->attach("./up/documento_recebido/fb04a3e5d657a7f3f8f6fe7eb545b609.pdf");
					
					#### ENVIA EMAIL ####
					if($this->email->send())
					{
						#echo "<pre>OK: ENVIADO</pre>";
						$qr_sql = "
									UPDATE projetos.envia_emails 
									   SET dt_email_enviado = CAST(timeofday() AS TIMESTAMP)
									 WHERE cd_email = ".intval($ar_reg['cd_email']).";
								  ";
						$this->db->query($qr_sql);
						
						$qr_sql = "					
									INSERT INTO projetos.envia_emails_debug
										 (
											cd_email,
											retorno
										 )
									VALUES
										 (
											".intval($ar_reg['cd_email']).",
											".str_escape(br2nl($this->email->print_debugger()))."
										 );
								  ";
						$this->db->query($qr_sql);
						
						$ar_retorno = array("STATUS" => "OK", "CD_EMAIL" => intval($ar_reg['cd_email']), "RETORNO" => "ENVIADO");
						flush();
						echo json_encode($ar_retorno);						
						flush();
					}
					else
					{
						#echo "<pre>ERRO: NAO ENVIADO</pre>";
						$qr_sql = "
									UPDATE projetos.envia_emails 
									   SET dt_email_enviado = CAST(timeofday() AS TIMESTAMP),
										   fl_retornou      = 'N'
									 WHERE cd_email = ".intval($ar_reg['cd_email']).";
								  ";
						$this->db->query($qr_sql);								 
						
						$qr_sql = "			
									INSERT INTO projetos.envia_emails_debug
										 (
											cd_email,
											retorno
										 )
									VALUES
										 (
											".intval($ar_reg['cd_email']).",
											".str_escape(br2nl($this->email->print_debugger()))."
										 );
								  ";
						$this->db->query($qr_sql);	

						$ar_retorno = array("STATUS" => "ERRO", "CD_EMAIL" => intval($ar_reg['cd_email']), "RETORNO" => "NAO ENVIADO");
						echo json_encode($ar_retorno);						
					}
				}
				echo br().date("Y-m-d H:i:s").br();
			}
			else
			{
				#echo "<pre>ERRO: NAO HA EMAIL PARA ENVIO</pre>";
				$ar_retorno = array("STATUS" => "ERRO", "CD_DIVULGACAO" => intval($CD_DIVULGACAO), "RETORNO" => "NAO HA EMAIL PARA ENVIO");
				echo json_encode($ar_retorno);					
			}
		}
		else
		{
			#echo "<pre>ERRO: TOKEN OU CODIGO DO EMAIL INVALIDO</pre>";
			$ar_retorno = array("STATUS" => "ERRO", "CD_DIVULGACAO" => intval($CD_DIVULGACAO), "RETORNO" => "TOKEN OU CODIGO DO EMAIL INVALIDO");
			echo json_encode($ar_retorno);			
		}		
    }	
}
?>