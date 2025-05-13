<?php

function getNavegador()
{
	$ci = &get_instance();
	$ci->load->library('user_agent');

	$ar_reg = Array();
	
	$ar_reg['is_browser'] = $ci->agent->is_browser();
	$ar_reg['browser']    = $ci->agent->browser();
	
	$ar_reg['is_robot'] = $ci->agent->is_robot();
	$ar_reg['robot']    = $ci->agent->robot();
	
	$ar_reg['is_mobile'] = $ci->agent->is_mobile();
	$ar_reg['mobile']    = $ci->agent->mobile();
	
	
	$ar_reg['platform'] = $ci->agent->platform();
	$ar_reg['version']  = $ci->agent->version();
	
	return $ar_reg;
}


function set_title($value='')
{
	$ci = &get_instance();

	$ci->session->set_userdata( array('HEADER_TITLE'=>$value) ); 
}

function get_title()
{
	$ci = &get_instance();

	$title = $ci->session->userdata('HEADER_TITLE');

	if($title=="") $title="ePrev";
	return $title;
}

function get_header()
{
	return 'header-azul.php';
}

function get_header_sem_topo()
{
	return 'header-azul-sem-menu.php';
}

function eprev_url()
{
	/*$protocolo = (isset($_SERVER['HTTPS']))?"https://":"http://";
	$eprev_url = $protocolo . $_SERVER['SERVER_NAME'];*/

	return base_url_eprev();
}

function esc( $search, $replace, &$content, $type='string', $AJAX=true )
{
	$ci = &get_instance();

	if($AJAX)
	{
		if(strtolower($type)=='str'||strtolower($type)=='string') $content = str_replace( $search, $ci->db->escape_str( utf8_decode($replace) ), $content );
	}
	else 
	{
		if(strtolower($type)=='str'||strtolower($type)=='string') $content = str_replace( $search, $ci->db->escape_str( $replace ), $content );
	}

	if(strtolower($type)=='int'||strtolower($type)=='integer') $content = str_replace( $search, intval($replace), $content );
	
	if(strtolower($type)=='float') $content = str_replace( $search, floatval($replace), $content );
}

function loader_html($tp = "N")
{
	if($tp == "P")
	{
		return "<img src='".base_url()."loader_p.gif' border='0'>";
	}
	else if($tp == "N")
	{
		return "<img style='margin:10;' src='".base_url()."loader.gif' border='0'>";
	}	
	else
	{
		return "<img style='margin:10;' src='".base_url()."loader.gif' border='0'>";
	}
}

function parray($a)
{
	echo '<pre>';
	var_dump($a);
	echo '</pre>';
}

if( ! function_exists('exibir_mensagem') )
{
	function exibir_mensagem( $mensagem )
	{
		$ci=&get_instance();
		$ci->load->view('mensagem',array('mensagem'=>$mensagem));
	}
}

function pasta_gravacao()
{
	return base_url().'../gravacoes/';
}

/**
 * Usa postgres para buscar uma data de refencia calculada de um intervalo. 
 * Será subtração se o parametro $oper = '-'
 * Será adição se o parametro $oper = '+'
 * Serão calculados apenas dias úteis se o parametro $dias_uteis = true (nesse caso intervalo deve ser um númer inteiro)
 * 
 * @param string $data_referencia Data no formato DD/MM/YYYY (d/m/Y), por padrão em branco, indica data atual
 * @param string $intervalo Se $dias_uteis=false, deve ser informado da mesma forma que os intervalos (::inteval) do postgres, por padrão em branco indica '1 week'. Mas se o parametro $dias_uteis=true esse parametro deve ser um inteiro representando o número de dias úteis a ser calculado, informando ZERO para buscar um dia útil para a data de referencia.
 * @param string $oper '+' ou '-'. Sinal do operador usado, por padrão o operador de subtração (-) pois originalmente essa função só subtraia.
 * @param boolean $dias_uteis true se o calculo deve ser feito com dias úteis, false o contrário. Se informar TRUE o intervalo deve ser um número inteiro e será sempre calculado por DIAS
 *
 * @return string Data no formato DD/MM/YYYY devidamente calculada
 */
function calcular_data( $data_referencia='', $intervalo='', $oper="-", $dias_uteis=false )
{
	$ci=&get_instance();

	if($dias_uteis)
	{
		if($data_referencia=='') {$data_referencia=date('d/m/Y');}
		if($oper=='-'){$oper='ANTES';}else{$oper='DEPOIS';}
		$intervalo=intval($intervalo);
		$q=$ci->db->query("SELECT to_char( funcoes.dia_util('$oper', TO_DATE('$data_referencia','DD/MM/YYYY'), $intervalo), 'DD/MM/YYYY' ) as resultado");
		$r = $q->row_array();
	}
	else
	{
		if($data_referencia=='') {$data_referencia=date('d/m/Y');}
		if($intervalo=='') {$intervalo='1 week';}

		$q=$ci->db->query("SELECT to_char( to_date('$data_referencia','DD/MM/YYYY') $oper '$intervalo'::interval, 'DD/MM/YYYY' ) AS resultado");
		$r = $q->row_array();
	}

	return $r['resultado'];
}

/**
 * @param integer $valor 
 * @param string  $origem Qual a unidade do valor original
 * @param integer $precisao Número de casas de precisão
 * @return array
 
 * 1 Byte = 8 Bit
 * 1 Kilobyte = 1024 Bytes
 * 1 Megabyte = 1048576 Bytes
 * 1 Gigabyte = 1073741824 Bytes
 * 1 Terabyte = 1099511627776 Bytes
 */
function converte_byte($valor, $origem="B", $precisao=0) 
{
	$unidade  = Array('B', 'KB', 'MB', 'GB', 'TB');
	$ar_valor = Array('B' => 0, 'KB' => 0, 'MB' => 0, 'GB' => 0, 'TB' => 0, 'ERRO' => false);
	if(in_array($origem, $unidade))
	{
			switch ($origem)
			{
				CASE 'B': 
							$ar_valor['ERRO'] = false;
							$ar_valor['B']  = round(($valor),$precisao);
							$ar_valor['KB'] = round((($valor/1024*100000)/100000),$precisao);
							$ar_valor['MB'] = round((($valor/1048576*100000)/100000),$precisao);
							$ar_valor['GB'] = round((($valor/1073741824*100000)/100000),$precisao);
							$ar_valor['TB'] = round((($valor*100000/1099511627776)/100000),$precisao);
							break;
							
				CASE 'KB': 
							$ar_valor['ERRO'] = false;
							$ar_valor['B']  = round((($valor*1024*100000)/100000),$precisao);
							$ar_valor['KB'] = round(($valor),$precisao);
							$ar_valor['MB'] = round((($valor/1024*100000)/100000),$precisao);
							$ar_valor['GB'] = round((($valor/1048576*100000)/100000),$precisao);
							$ar_valor['TB'] = round((($valor*100000/1073741824)/100000),$precisao);	
							break;

				CASE 'MB': 
							$ar_valor['ERRO'] = false;
							$ar_valor['B']  = round((($valor*1048576*100000)/100000),$precisao);
							$ar_valor['KB'] = round((($valor*1024*100000)/100000),$precisao);
							$ar_valor['MB'] = round(($valor),$precisao);
							$ar_valor['GB'] = round((($valor/1024*100000)/100000),$precisao);
							$ar_valor['TB'] = round((($valor*100000/1048576)/100000),$precisao);
							break;

				CASE 'GB': 
							$ar_valor['ERRO'] = false;
							$ar_valor['B']  = round((($valor*1073741824*100000)/100000),$precisao);
							$ar_valor['KB'] = round((($valor*1048576*100000)/100000),$precisao);
							$ar_valor['MB'] = round((($valor*1024*100000)/100000),$precisao);
							$ar_valor['GB'] = round(($valor),$precisao);
							$ar_valor['TB'] = round((($valor*100000/1024)/100000),$precisao);
							break;

				CASE 'TB': 
							$ar_valor['ERRO'] = false;
							$ar_valor['B']  = round((($valor*1099511627776*100000)/100000),$precisao);
							$ar_valor['KB'] = round((($valor*1073741824*100000)/100000),$precisao);
							$ar_valor['MB'] = round((($valor*1048576*100000)/100000),$precisao);
							$ar_valor['GB'] = round((($valor*1024*100000)/100000),$precisao);
							$ar_valor['TB'] = round(($valor),$precisao);
							break;
			}
	}
	else
	{
		$ar_valor['ERRO'] = true;
	}
	
	return $ar_valor;
}

/**
 *
 * Enviar email
 *
 * @param array $args
 *				$args['de']
 *				$args['para']
 *				$args['para_usuario_id'] busca na tabela de usuários o email
 *				$args['cc']
 *				$args['assunto']
 *				$args['mensagem']
 *				$args['cd_evento']  enums_helper.php enum_projetos_eventos
 *
 * @return bool
 *
 */
function enviar_email($args)
{
	$sql="
	INSERT INTO projetos.envia_emails
	( 
		dt_envio 
		, de 
		, para 
		, cc 
		, assunto 
		, texto 
		, cd_evento
	)
	VALUES 
	(
		CURRENT_TIMESTAMP
		, '{de}'
		, '{para}'
		, '{cc}'
		, '{assunto}'
		, '{texto}'
		, {cd_evento}
	)
	";

	if( !isset($args["cc"]) ){ $args["cc"]=''; }
	if( !isset($args["cd_evento"]) ){ $args["cd_evento"]='null'; }

	esc("{de}", $args["de"], $sql, "str", FALSE);
	esc("{para}", $args["para"], $sql, "str", FALSE);
	esc("{cc}", $args["cc"], $sql, "str", FALSE);
	esc("{assunto}", $args["assunto"], $sql, "str", FALSE);
	esc("{texto}", $args["mensagem"], $sql, "str", FALSE);
	esc("{cd_evento}", $args["cd_evento"], $sql, "str", FALSE);

	$ci=&get_instance();
	$query=$ci->db->query($sql);

	if($query){ return true; } else { return false; }
}

function skin()
{
	return base_url()."skins/skin002/";
}

function anchor_img($a,$i,$title)
{
	return anchor( $a, "<img src='" . $i . "' border='0' alt='".$title."' title='".$title."' />" );
}

function ifr( $url )
{
	return "<html><body bgcolor='' topmargin='0' leftmargin='0'><iframe frameborder='0' src='$url' width='100%' height='100%'></iframe></body></html>";
}

function usar_template($arquivo,$args=array())
{
	$filename='sysapp/application/eprev/templates/'.$arquivo;
	$handle=fopen($filename,'r');
	$conteudo=fread( $handle, filesize($filename) );
	fclose($handle);

	foreach($args as $key => $value)
	{
		$conteudo = str_replace( $key,$value,$conteudo );
	}

	return $conteudo;
}

function gera_link($url_completa, $cd_empresa = "NULL", $cd_registro_empregado = "NULL", $seq_dependencia = "NULL")
{
	$ci=&get_instance();
	$sql = "
				SELECT funcoes.gera_link('{url_completa}',{cd_empresa},{cd_registro_empregado},{seq_dependencia}) AS link_gerado;
		   ";
	esc('{url_completa}', $url_completa, $sql, 'str');
	esc('{cd_empresa}'            , $cd_empresa, $sql);
	esc('{cd_registro_empregado}', $cd_registro_empregado, $sql);
	esc('{seq_dependencia}'      , $seq_dependencia, $sql);
	
	$q=$ci->db->query( $sql );
	$r=$q->row_array();
	
	return $r['link_gerado'];
}

function h1($t)
{
	return "<h1>$t</h1>";
}

function mes_format($mes,$formato='mmm',$lenguage='pt-br')
{
	if($lenguage=='pt-br')
	{
		if($formato=='mmm')
		{
			$ames=array('Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez');
			$ret = $ames[$mes-1];
		}
		elseif($formato=='mmmm')
		{
			$ames=array('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro');
			$ret = $ames[$mes-1];
		}
		else
		{
			$ret = $mes;
		}
	}
	else if($lenguage=='en-us')
	{
		if($formato=='mmm')
		{
			$ames=array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
			$ret = $ames[$mes-1];
		}
		elseif($formato=='mmmm')
		{
			$ames=array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
			$ret = $ames[$mes-1];
		}
		else
		{
			$ret = $mes;
		}
	}
	else
	{
		$ret = $mes;
	}
	
	return $ret;
}

function mes_date_js($mes)
{
	$int = intval($mes);

	$int --;

	if($int < 10)
	{
		$int = '0'.$int;
	}

	return $int;
}


function app_decimal_para_db($v)
{
    $v = str_replace( '.', '', $v );
    return str_replace( ',', '.', $v );
        
}

function app_decimal_para_php($v)
{
	return str_replace( '.', ',', $v );
}

function arrayToUTF8($valor) 
{
   return utf8_encode($valor);
}

function explodePipe($valor)
{
	return explode("|",$valor);
}

function getIPWSVoip()
{
	#### IPS DA CENTRAL DE FAX ####
	
	$ob_ci = &get_instance();
	$qr_sql = "
				SELECT ip FROM asterisk.ip;
		      ";
	$ob_resul = $ob_ci->db->query($qr_sql);
	$ar_reg = $ob_resul->row_array();	
	return $ar_reg['ip'];
	
	#$ar_ip[] = "10.63.255.9";
	#$ar_ip[] = "10.63.255.14";
	#$ar_ip[] = "10.63.255.15";

	/*
	$nr_conta = 0;
	$nr_fim   = count($ar_ip);
	while($nr_conta < $nr_fim)
	{
		$fp = @fsockopen($ar_ip[$nr_conta], 80, $errno, $errstr, 5);
		if($fp)
		{
			return $ar_ip[$nr_conta];
		}
	}
	return $ar_ip[0];		
	*/
}


/**
 * This function creates an array with column names up until the column
 * you specified.
*/
function seq_caracter($end_column = 100, $first_letters = '')
{
  #EXEMPLO echo "<PRE>".print_r( seq_caracter(60), true)."</PRE>";
  $columns = array();
  $length = strlen($end_column);
  $letters = range('A', 'Z');

  // Iterate over 26 letters.
  foreach ($letters as $letter) {
      // Paste the $first_letters before the next.
      $column = $first_letters . $letter;

      // Add the column to the final array.
      $columns[] = $column;

      // If it was the end column that was added, return the columns.
      if ($column == $end_column)
          return $columns;
  }

  // Add the column children.
  foreach ($columns as $column) {
      // Don't itterate if the $end_column was already set in a previous itteration.
      // Stop iterating if you've reached the maximum character length.
      if (!in_array($end_column, $columns) && strlen($column) < $length) {
          $new_columns = seq_caracter($end_column, $column);
          // Merge the new columns which were created with the final columns array.
          $columns = array_merge($columns, $new_columns);
      }
  }

  return $columns;
}

function str_escape($valor, $fl_trim = true)
{
	$ci = &get_instance();
	
	if($fl_trim)
	{
		$valor = trim($valor);
	}

	return $ci->db->escape($valor);
}

function mes_extenso($mes = '')
{
	$ar_mes = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
	
	if(trim($mes) == '')
	{
		return $ar_mes[date('m') - 1];
	}
	else
	{
		return $ar_mes[$mes - 1];
	}
}

function br2nl($string)
{
    return preg_replace('#<br\s*?/?>#i', "\n", $string); 
} 

function getExtensaoPermitida()
{
	return array('pdf','txt','doc','docx','xls','xlsx','ppt','pptx','jpg','jpeg','png','bmp','gif','tif','mp3','msg','sql','csv','rar','zip','wav', 'xlsm', 'm4a', 'wav');
}

function getListner()
{
	$ar_reg = Array("IP"=>"","PORTA"=>"");
	
	if(($_SERVER['SERVER_ADDR'] == '10.63.255.5') OR ($_SERVER['SERVER_ADDR'] == '10.63.255.7'))
	{
		$ar_conf = getListenerConf();
		
		if((array_key_exists("ip", $ar_conf)) AND (intval($ar_conf['porta']) > 0))
		{
			$ar_reg["IP"]    = $ar_conf['ip'];
			$ar_reg["PORTA"] = $ar_conf['porta'];
		}
		else
		{
			$ar_reg["IP"]    = '10.63.255.16';
			$ar_reg["PORTA"] = '9731';
		}
	}
	else
	{
		if($_SERVER['REMOTE_ADDR'] == '10.63.255.x')//PARA TESTES
		{
			$ar_reg["IP"] = '10.63.255.x'; 
		}
		else
		{
			$ar_reg["IP"] = '10.63.255.16'; 
		}
		$ar_reg["PORTA"] = '4444';   
	} 

	return $ar_reg;
}

function getListenerConf()
{
	$qr_sql = " 
				SELECT ip,
					   porta,
					   CURRENT_TIMESTAMP - ultima_resposta AS tempo
				  FROM projetos.adm_listner
				 WHERE banco = 'ELETRO1' 
				   AND situacao = 'A'
				   AND ultima_resposta > CURRENT_DATE 
				   AND porta NOT IN ('3625','9999')
				 ORDER BY tempo DESC
				 LIMIT 5									
			  ";

	$ob_ci = &get_instance();
	$ob_resul = $ob_ci->db->query($qr_sql);
	$ar_reg = $ob_resul->result_array();		
	
	foreach($ar_reg as $ar_item)
	{
		$fp = @fsockopen($ar_item['ip'], $ar_item['porta'], $errno, $errstr, 5);
		if($fp)
		{
			return $ar_item;
		}
	}
	return Array();
}


function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) 
{
	$sort_col = array();

    foreach ($arr as $key=> $row) 
    {
        $sort_col[$key] = $row[$col];
    }

	array_multisort($sort_col, $dir, $arr);
}

function indicador_status($fl_meta = "", $fl_direcao = "", $fl_url = "N")
{
	$ds_meta    = "sem";
	$ds_direcao = "cont";
	
	if(trim($fl_meta) == "S")
	{
		$ds_meta = "sim";
	}
	elseif(trim($fl_meta) == "N")
	{
		$ds_meta = "nao";
	}
	
	if(trim($fl_direcao) == "C")
	{
		$ds_direcao = "up";
	}
	elseif(trim($fl_direcao) == "B")
	{
		$ds_direcao = "down";
	}	
	
	if(trim($fl_url) == "S")
	{
		return "img/indicador_status/meta_".$ds_meta."_".$ds_direcao.".png";
	}
	else
	{
		return '<img src="'.base_url().'img/indicador_status/meta_'.$ds_meta.'_'.$ds_direcao.'.png" border="0">';
	}
}

function indicador_status_check($nr_valor = 0, $nr_valor_anterior = 0, $nr_meta = 0, $tp_analise = "")
{
	$qr_sql = " 
				SELECT fl_meta, fl_direcao 
                  FROM indicador.resultado_status(".floatval($nr_valor).", ".floatval($nr_valor_anterior).", ".floatval($nr_meta).", '".trim($tp_analise)."')
			  ";

	$ob_ci = &get_instance();
	$ob_resul = $ob_ci->db->query($qr_sql);
	$ar_reg = $ob_resul->row_array();		

	return $ar_reg; 
}

function calculo_projetado_mensal($nr, $mes)
{
	$nr_mes = pow((1+($nr/100)), (1/12));

	$valor = 0;

	$i = 1;

	while($i <= 12)
	{
		$nr_mes_atual   = pow($nr_mes, $i);
		$nr_mes_percent = (($nr_mes_atual-1)*100);

		if($i == intval($mes))
		{
			$valor = $nr_mes_percent;

			break;
		}

		$i++;
	}

	return $valor;
}

function calculo_acumulado($valores, $mes)
{
	$i = 1;

	$realizado_acumulado = 0;

	$valor = 0;

	while($i <= 12)
	{
		if(isset($valores[$i-1]))
		{
			$item = $valores[$i-1];

			$realizado = ($item/100)+1;

			if($realizado_acumulado == 0)
			{
				$realizado_acumulado = $realizado;
			}
			else
			{
				$realizado_acumulado = $realizado_acumulado * $realizado;
			}

			if($i == intval($mes))
			{
				$valor = ($realizado_acumulado-1)*100;

				break;
			}
		}

		$i ++;
	}

	return $valor;
}
?>