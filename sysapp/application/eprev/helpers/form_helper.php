<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Form Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/form_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('form_open'))
{
	/**
	 * Form Declaration
	 *
	 * Creates the opening portion of the form.
	 *
	 * @access	public
	 * @param	string	the URI segments of the form destination
	 * @param	array	a key/value pair of attributes
	 * @param	array	a key/value pair hidden data
	 * @return	string
	 */
	function form_open($action = '', $attributes = '', $hidden = array())
	{
		$CI =& get_instance();

		if ($attributes == '')
		{
			$attributes = 'method="post"';
		}

		$action = ( strpos($action, '://') === FALSE) ? $CI->config->site_url($action) : $action;

		$form = '<form action="'.$action.'"';

		$form .= _attributes_to_string($attributes, TRUE);

		$form .= '>';

		if (is_array($hidden) AND count($hidden > 0))
		{
			$form .= form_hidden($hidden);
		}

		return $form;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_open_multipart'))
{
	/**
	 * Form Declaration - Multipart type
	 *
	 * Creates the opening portion of the form, but with "multipart/form-data".
	 *
	 * @access	public
	 * @param	string	the URI segments of the form destination
	 * @param	array	a key/value pair of attributes
	 * @param	array	a key/value pair hidden data
	 * @return	string
	 */
	function form_open_multipart($action, $attributes = array(), $hidden = array())
	{
		$attributes['enctype'] = 'multipart/form-data';
		return form_open($action, $attributes, $hidden);
	}
}

// ------------------------------------------------------------------------


if ( ! function_exists('progressbar'))
{
	/**
	 * Componente ProgressBar
	 *
	 * Cria barra de progresso.
	 *
	 * @access	public
	 * @param	string	id do objeto
	 * @param	integer	valor do progresso
	 * @return	string
	 */
	function progressbar($valor=0,$id="")
	{
		$id = (trim($id) == "" ? "pb_".md5(uniqid(rand(), true)) : trim($id));
		return '<span id="'.$id.'" class="progressBar"></span><script>$("#'.$id.'").progressBar('.intval($valor).');</script>';
	}
}



// ------------------------------------------------------------------------

if ( ! function_exists('form_hidden'))
{
	/**
	 * Hidden Input Field
	 *
	 * Generates hidden fields. You can pass a simple key/value string or an associative
	 * array with multiple values.
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	string
	 */
	function form_hidden($name, $value = '')
	{
		if ( ! is_array($name))
		{
			return '<input type="hidden" id="'.$name.'" name="'.$name.'" value="'.form_prep($value).'" />';
		}

		$form = '';
		foreach ($name as $name => $value)
		{
			$form .= '<input type="hidden" id="'.$name.'" name="'.$name.'" value="'.form_prep($value).'" />';
		}

		return $form;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_input'))
{
	/**
	 * Text Input Field
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_input($data = '', $value = '', $extra = '')
	{
		$defaults = array('type' => 'text', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

		return "<input ".parse_form_attributes($data, $defaults).$extra."/>\n";
	}

/*	 
	function form_input($data = '', $value = '', $extra = '')
	{
		$defaults = array('type' => 'text', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

		if(!in_array("type", array_keys($data)))
		{
			$data['type'] = 'text';
		}
		
		if($data['type'] == "text")
		{
			if(!in_array("style", array_keys($data)))
			{
				$data['style'] = 'height: 20px; border: 1px inset #c3c3c3; padding-left: 2px; padding-right: 2px;';
			}		

			/*
			$ar_style = explode(";",$data['style']);
			$nr_conta = 0;
			$nr_fim   = count($ar_style);
			while($nr_conta < $nr_fim)
			{
				$ar_item = explode(":",$ar_style[$nr_conta]);
				
				if(!in_array("width", array_keys($ar_item)))
				{
					//$ar_style[$nr_conta] = "width:100%";
				}

				$nr_conta++;
			}
			$data['style'] = implode(";",$ar_style);
			*/
		//}
		

		

/*
		print_r($data);
		echo "<BR><BR>";
		print_r(array_keys($data));
		echo "<BR><BR><BR><BR>";*/
		/*
		return "<input ".parse_form_attributes($data, $defaults).$extra." >\n";
	}*/
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_password'))
{
	/**
	 * Password Field
	 *
	 * Identical to the input function but adds the "password" type
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_password($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'password';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_upload'))
{
	/**
	 * Upload Field
	 *
	 * Identical to the input function but adds the "file" type
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_upload($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'file';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_textarea'))
{
	/**
	 * Textarea field
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_textarea($data = '', $value = '', $extra = '')
	{
		$defaults = array('name' => (( ! is_array($data)) ? $data : ''), 'cols' => '70', 'rows' => '10');

		if ( ! is_array($data) OR ! isset($data['value']))
		{
			$val = $value;
		}
		else
		{
			$val = $data['value'];
			unset($data['value']); // textareas don't use the value attribute
		}

		$div = "<div style='width: 500px;'></div>";
		if(trim($extra) != "")
		{
			$i = strpos($extra, "width:");	
			if($i > 0)
			{
				$e = substr($extra, $i);
				$i = strpos($e, ";");
				$e = substr($e, 0, $i);
				$e = str_replace("width:","",$e);
				$e = str_replace(";","",$e);
				
				$i = strpos($e, "px");
				if($i > 0)
				{				
					$div = "<div style='width: ".$e.";'></div>";
				}
			}
		}
		
		return "<textarea ".parse_form_attributes($data, $defaults).$extra.">".$val."</textarea><div class='textarea_impressao' >".nl2br(htmlentities($val))."</div>".$div;
	}
}

// ------------------------------------------------------------------------

if( ! function_exists('form_dropdown_db') )
{
	function form_dropdown_db($name = '', $config=array(), $selected='', $extra="", $where=" dropdown_db.dt_exclusao IS NULL ", $orderby="text")
	{
		$collection = Array();
		$tabela = $config[0];
		$coluna_pk = $config[1];
		$coluna_texto = $config[2];
		
		$orderby = (trim($orderby) == "" ? $coluna_texto : $orderby);		
		

		$ci = &get_instance();
		
		$qr_sql = "
					SELECT dropdown_db.".$coluna_pk." as value, 
						   dropdown_db.".$coluna_texto." as text 
					  FROM ".$tabela." dropdown_db
					 WHERE ".(trim($where) == "" ? " 1 = 1 " : trim($where))."
					 ORDER BY ".$orderby.";
				  ";
				  
		#echo "<PRE>".$qr_sql."</PRE>";exit;		  
		
		$q = $ci->db->query($qr_sql);		
		if($q)
		{
			$collection = $q->result_array();
		}

		// montar objeto <SELECT>
		$options = array();

		$options[""] = "Selecione";

		if( $collection!==FALSE )
		{
			foreach( $collection as $item )
			{
				$options[$item["value"]] = $item["text"];
			}
		}
		
		return form_dropdown($name, $options, array($selected), $extra);
	}
}

if ( ! function_exists('form_dropdown'))
{
	/**
	 * Drop-down Menu
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_dropdown($name = '', $options = array(), $selected = array(), $extra = '')
	{
		if ( ! is_array($selected))
		{
			$selected = array($selected);
		}

		if ($extra != '') $extra = ' '.$extra;

		$multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

		$form = '<select id="'.$name.'" name="'.$name.'"'.$extra.$multiple." onkeypress='handleEnter(this, event);'>\n";

		foreach ($options as $key => $val)
		{
			$key = (string) $key;
			$val = (string) $val;

			$sel = (in_array($key, $selected))?' selected="selected"':'';

			$form .= '<option value="'.$key.'"'.$sel.'>'.$val."</option>\n";
		}

		$form .= "</select>
				  ";

		return $form;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_checkbox'))
{
	/**
	 * Checkbox Field
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	string
	 * @return	string
	 */
	function form_checkbox($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		$defaults = array('type' => 'checkbox', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

		if (is_array($data) AND array_key_exists('checked', $data))
		{
			$checked = $data['checked'];

			if ($checked == FALSE)
			{
				unset($data['checked']);
			}
			else
			{
				$data['checked'] = 'checked';
			}
		}

		if ($checked == TRUE)
		$defaults['checked'] = 'checked';
		else
		unset($defaults['checked']);

		return "<input ".parse_form_attributes($data, $defaults).$extra." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_radio'))
{
	/**
	 * Radio Button
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	string
	 * @return	string
	 */
	function form_radio($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'radio';
		return form_checkbox($data, $value, $checked, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_submit'))
{
	/**
	 * Submit Button
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_submit($data = '', $value = '', $extra = '')
	{
		$defaults = array('type' => 'submit', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

		return "<input ".parse_form_attributes($data, $defaults).$extra." class='btn btn-small btn-primary'/>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_reset'))
{
	/**
	 * Reset Button
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_reset($data = '', $value = '', $extra = '')
	{
		$defaults = array('type' => 'reset', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

		return "<input ".parse_form_attributes($data, $defaults).$extra." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_button'))
{
	/**
	 * Form Button
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_button($data = '', $content = '', $extra = '')
	{
		$defaults = array('name' => (( ! is_array($data)) ? $data : ''), 'type' => 'submit');

		if ( is_array($data) AND isset($data['content']))
		{
			$content = $data['content'];
			unset($data['content']); // content is not an attribute
		}

		return "<button ".parse_form_attributes($data, $defaults).$extra.">".$content."</button>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_label'))
{
	/**
	 * Form Label Tag
	 *
	 * @access	public
	 * @param	string	The text to appear onscreen
	 * @param	string	The id the label applies to
	 * @param	string	Additional attributes
	 * @return	string
	 */
	function form_label($label_text = '', $id = '', $attributes = array())
	{

		$label = '<label';

		if ($id != '')
		{
			$label .= " for=\"$id\"";
		}

		if (is_array($attributes) AND count($attributes) > 0)
		{
			foreach ($attributes as $key => $val)
			{
				$label .= ' '.$key.'="'.$val.'"';
			}
		}

		$label .= ">$label_text</label>";

		return $label;
	}
}

// ------------------------------------------------------------------------
if ( ! function_exists('form_fieldset'))
{
	/**
	 * Fieldset Tag
	 *
	 * Used to produce <fieldset><legend>text</legend>.  To close fieldset
	 * use form_fieldset_close()
	 *
	 * @access	public
	 * @param	string	The legend text
	 * @param	string	Additional attributes
	 * @return	string
	 */
	function form_fieldset($legend_text = '', $attributes = array())
	{
		$fieldset = "<fieldset";

		$fieldset .= _attributes_to_string($attributes, FALSE);

		$fieldset .= ">\n";

		if ($legend_text != '')
		{
			$fieldset .= "<legend>$legend_text</legend>\n";
		}

		return $fieldset;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_fieldset_close'))
{
	/**
	 * Fieldset Close Tag
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function form_fieldset_close($extra = '')
	{
		return "</fieldset>\n".$extra;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_close'))
{
	/**
	 * Form Close Tag
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function form_close($extra = '')
	{
		return "</form>\n".$extra;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_prep'))
{
	/**
	 * Form Prep
	 *
	 * Formats text so that it can be safely placed in a form field in the event it has HTML tags.
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function form_prep($str = '')
	{
		if ($str === '')
		{
			return '';
		}

		$temp = '__TEMP_AMPERSANDS__';

		// Replace entities to temporary markers so that
		// htmlspecialchars won't mess them up
		$str = preg_replace("/&#(\d+);/", "$temp\\1;", $str);
		$str = preg_replace("/&(\w+);/",  "$temp\\1;", $str);

		$str = htmlspecialchars($str, ENT_COMPAT, 'ISO-8859-1');

		// In case htmlspecialchars misses these.
		$str = str_replace(array("'", '"'), array("&#39;", "&quot;"), $str);

		// Decode the temp markers back to entities
		$str = preg_replace("/$temp(\d+);/","&#\\1;",$str);
		$str = preg_replace("/$temp(\w+);/","&\\1;",$str);

		return $str;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('parse_form_attributes'))
{
	/**
	 * Parse the form attributes
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @access	private
	 * @param	array
	 * @param	array
	 * @return	string
	 */
	function parse_form_attributes($attributes, $default)
	{
		if (is_array($attributes))
		{
			foreach ($default as $key => $val)
			{
				if (isset($attributes[$key]))
				{
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}

			if (count($attributes) > 0)
			{
				$default = array_merge($default, $attributes);
			}
		}

		$att = '';
		foreach ($default as $key => $val)
		{
			if ($key == 'value')
			{
				$val = form_prep($val);
			}

			$att .= $key . '="' . $val . '" ';
		}

		return $att;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_attributes_to_string'))
{
	/**
	 * Attributes To String
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @access	private
	 * @param	mixed
	 * @param	bool
	 * @return	string
	 */
	function _attributes_to_string($attributes, $formtag = FALSE)
	{
		if (is_string($attributes) AND strlen($attributes) > 0)
		{
			if ($formtag == TRUE AND strpos($attributes, 'method=') === FALSE)
			{
				$attributes .= ' method="post"';
			}

			return ' '.$attributes;
		}

		if (is_object($attributes) AND count($attributes) > 0)
		{
			$attributes = (array)$attributes;
		}

		if (is_array($attributes) AND count($attributes) > 0)
		{
			$atts = '';

			if ( ! isset($attributes['method']) AND $formtag === TRUE)
			{
			 $atts .= ' method="post"';
			}

			foreach ($attributes as $key => $val)
			{
			 $atts .= ' '.$key.'="'.$val.'"';
			}

			return $atts;
		}
	}
}

if ( ! function_exists('form_date'))
{
	function form_date($id, $value="",$filtro=false, $disable=false)
	{
		if($filtro==TRUE)
		{
			// filtros
			$ci = &get_instance();
			$filtro = resgatar_filtro( $id );
			if(  trim( $filtro )!=''  )
			{
				$value=$filtro;
			}
			/////
		}

		$js = '
		<script type="text/javascript">
			Calendar.setup({
			        inputField     :    "'.$id.'",      	// id of the input field
			        ifFormat       :    "%d/%m/%Y",       	// format of the input field
			        showsTime      :    false,            	// will display a time selector
			        button         :    "'.$id.'_trigger",  // trigger for the calendar (button ID)
			        singleClick    :    true,           	// double-click mode
			        timeFormat     :    "24"
		    });

		    jQuery(function($){
			   $("#'.$id.'").mask("99/99/9999");
			});
		    
		</script>
		';
		$attr = array('name'=>$id, 'id'=>$id, 'style'=>'width:75px;', 'value'=>$value);
		if($disable)
		{
			$attr['disabled']='disabled';
		}
		$input = form_input($attr);
		$button='';
		if(!$disable)
		{
			#$attr = array('type'=>'button', 'name'=>$id.'_trigger', 'id'=>$id.'_trigger', 'value'=>'...', 'class'=>'botao_disabled', 'style'=>' height: 19px; margin-bottom: 2px;');
			$attr = array('type'=>'button', 'name'=>$id.'_trigger', 'id'=>$id.'_trigger', 'value'=>'', 'class'=>'botao_calendario');
			$button = form_input( $attr );
		}

		return $input . $button . $js . "\n";

	}
}

if ( ! function_exists('form_time'))
{
	function form_time($id, $value="")
	{
		$js = '
		<script>
		    jQuery(function($){
			   $("#'.$id.'").mask("99:99");
			});
		</script>
		';

		$attr = array('name'=>$id, 'id'=>$id, 'style'=>'width:75px', 'value'=>$value, 'style'=>'width:50px');
		$input = form_input($attr);

		return $input . $js . "\n";
	}
}

if (!function_exists('form_default_qrcode'))
{
	/*
	cd_origem             (1) -- Relatório de origem: Tabela ORIGENS_QR_CODES (ORACLE)
	cd_empresa            (2) -- Empresa
	cd_registro_empregado (3) -- RE
	seq_dependencia       (4) -- Seq. Dep.
	tela_destino          (5) -- Tela de destino
	cd_digitalizacao      (6) -- Código do documento para digitalização
	*/
	function form_default_qrcode($ar_config)
	{
		$attr = array("name"=>$ar_config["id"], "id"=>$ar_config["id"]);

		$function_ret = (trim($ar_config["callback"]) == "" ? $ar_config["id"]."_function_ret" : trim($ar_config["callback"]));
		
		#
		return '
				<tr id="'.$ar_config["id"].'_row">
				<td class="coluna-padrao-form" valign="middle"><label class="label-padrao-form" for="'.$ar_config["id"].'">'.$ar_config["caption"].'</label></td>
				<td class="coluna-padrao-form-objeto" valign="bottom">
					'.form_input($attr, $ar_config["value"],'style="background: url('.base_url().'img/qrcode.gif) no-repeat; padding-left:20px; border: 1px solid gray;"').'
					<script>
						function '.$ar_config["id"].'_function_ret(json)
						{
							return false;
						}
					
						$("#'.$ar_config["id"].'").focus(function(e) 
						{
							$(this).val("");
						});					
					
						$("#'.$ar_config["id"].'").change(function(e) 
						{
							var ds_data = $(this).val();
							var ar_data = ds_data.split("|");
							
							if(ar_data.length == 1)
							{
								ar_data = ds_data.split("}");
							}
							
							if(ar_data.length == 1)
							{
								ar_data = ds_data.split("-");
							}
							
							if(ar_data.length >= 6)
							{
								var json = {
									result                : true, 
									cd_origem             : ar_data[0],
									cd_empresa            : ar_data[1],           	
									cd_registro_empregado : ar_data[2],
									seq_dependencia       : ar_data[3], 
									tela_destino          : ar_data[4].toUpperCase(),   	
									cd_digitalizacao      : ar_data[5]
								};
							}
							else
							{
								var json = {
									result                : false, 
									cd_origem             : null,
									cd_empresa            : null,           	
									cd_registro_empregado : null,
									seq_dependencia       : null, 
									tela_destino          : null,   	
									cd_digitalizacao      : null
								};
							}
							
							'.trim($function_ret).'(json);
						});					
					</script>
					</td>
				</tr>
			   ';
	}
}

if ( ! function_exists('form_default_integer_ano'))
{
	function form_default_integer_ano($id_1, $id_2, $caption="", $value_1="", $value_2="", $filtro=false)
	{
		$attr_1 = array( "name"=>$id_1, "id"=>$id_1, "onkeypress"=>"handleEnter(this, event);", "style" => "width:60px" );
		$attr_2 = array( "name"=>$id_2, "id"=>$id_2, "onkeypress"=>"handleEnter(this, event);", "style" => "width:60px" );
		
		if($filtro==TRUE)
		{
			// filtros
			$ci = &get_instance();
			$filtro = resgatar_filtro( $id_1 );
			if(  trim( $filtro )!=''  )
			{
				$value_1 = $filtro;
			}
			
			$filtro = resgatar_filtro( $id_2 );
			if(  trim( $filtro )!=''  )
			{
				$value_2 = $filtro;
			}
			
		}
		
		$output = '
			<script>
			jQuery(function($){
			   $("#'.$id_1.'").numeric();
			   $("#'.$id_2.'").numeric();
			});
			</script>
			<tr id="'.$id_1.'_'.$id_2.'_row">
				<td class="coluna-padrao-form">
					<label class="label-padrao-form" for="'.$id_1.'">
						'.$caption.'
					</label>
				</td>
				<td class="coluna-padrao-form-objeto">
				'.form_input($attr_1, $value_1).' / '.form_input($attr_2, $value_2).'
				</td>
			</tr>';
			
		return $output . "\n";
	}
}

if ( ! function_exists('form_default_integer_interval'))
{
	function form_default_integer_interval($id_1, $id_2, $caption="", $value_1="", $value_2="", $filtro=false)
	{
		$attr_1 = array( "name"=>$id_1, "id"=>$id_1, "onkeypress"=>"handleEnter(this, event);", "style" => "width:60px" );
		$attr_2 = array( "name"=>$id_2, "id"=>$id_2, "onkeypress"=>"handleEnter(this, event);", "style" => "width:60px" );
	
		if($filtro==TRUE)
		{
			// filtros
			$ci = &get_instance();
			$filtro = resgatar_filtro( $id_1 );
			if(  trim( $filtro )!=''  )
			{
				$value_1 = $filtro;
			}
			
			$filtro = resgatar_filtro( $id_2 );
			if(  trim( $filtro )!=''  )
			{
				$value_2 = $filtro;
			}
			
		}
	
		$output = "
			<script>
			jQuery(function($){
			   $('#".$id_1."').numeric();
			   $('#".$id_2."').numeric();
			});
			</script>
			<tr>
				<td class='coluna-padrao-form'>
					<label class='label-padrao-form' for='$id_1'>
						$caption
					</label>
				</td>
				<td class='coluna-padrao-form-objeto'>
				".form_input($attr_1, $value_1)." até ".form_input($attr_2, $value_2)."
				</td>
			</tr>";
			
		return $output . "\n";
	}
}

if ( ! function_exists('form_default_date_interval'))
{
	/**
	 * Cria dentro de uma caixa de filtros, um intervalo de datas
	 *
	 * @param string $id_1
	 * @param string $id_2
	 * @param string $caption
	 * @param string $value_1
	 * @param string $value_2
	 * @return string
	 */
	function form_default_date_interval($id_1, $id_2, $caption="", $value_1="", $value_2="", $filtro=false)
	{
		$output = "
		<tr>
		<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id_1'>$caption</label></td>
		<td class='coluna-padrao-form-objeto'>
			<select name='".$id_1."_".$id_2."_shortcut' id='".$id_1."_".$id_2."_shortcut'>
				<option value='' selected='selected'>Padrão</option>
				<option value='reset'>Limpar</option>
				<option value='today'>Hoje</option>
				<option value='yesterday'>Ontem</option>
				<option value='tomorrow'>Amanhã</option>
				<option value='last7days'>Últimos 7 dias</option>
				<option value='last15days'>Últimos 15 dias</option>
				<option value='last30days'>Últimos 30 dias</option>
				<option value='last60days'>Últimos 60 dias</option>
				<option value='last90days'>Últimos 90 dias</option>
				<option value='next7days'>Próximos 7 dias</option>
				<option value='next15days'>Próximos 15 dias</option>
				<option value='next30days'>Próximos 30 dias</option>				
				<option value='next60days'>Próximos 60 dias</option>				
				<option value='next90days'>Próximos 90 dias</option>				
				<option value='currentMonth'>Este mês</option>
				<option value='lastMonth'>Mês passado</option>
				<option value='nextMonth'>Próximo Mês</option>
				<option value='currentYear'>Este ano</option>
				<option value='lastYear'>Ano passado</option>
				<option value='nextYear'>Próximo Ano</option>
			</select>
			
			".form_date($id_1,$value_1,$filtro).form_date($id_2,$value_2,$filtro)."
			
			<script>
				$('select#".$id_1."_".$id_2."_shortcut').change(function() {
					var selected = $(this).find(':selected');
					var d1 = new Date();
					var d2 = new Date();
					var fun = selected.val();
					
					if($(this).val() == 'reset')
					{
						$('#".$id_1."').val('');
						$('#".$id_2."').val('');					
					}
					else if(fun != '')
					{
						var r = dateRanges[fun](d1,d2);
						if (r == null) 
						{
							$('#".$id_1."').val(d1.asString());
							$('#".$id_2."').val(d2.asString());
						}
						else 
						{
							$('#".$id_1."').val('');
							$('#".$id_2."').val('');
						}
					}
					else 
					{
						$('#".$id_1."').val('".$value_1."');
						$('#".$id_2."').val('".$value_2."');
					}					
				});
			</script>			
			</td>
		</tr>
		";

		return $output . "\n";
	}
}



function filter_text_autocomplete($id, $caption="", $source="", $callback="", $value="", $extra="", $maxlength=0)
{
	return form_default_text_autocomplete($id, $caption, $source, $callback, $value, $extra, $maxlength, TRUE);
}

if (!function_exists('form_default_text_autocomplete'))
{
	function form_default_text_autocomplete($id, $caption="", $source="", $callback="", $value="", $extra="", $maxlength=0, $filtro=false)
	{
		if(is_array($value))
		{
			if(isset($value[$id])) $value = $value[$id]; else $value='';
		}

		if(trim($value)=='' && $filtro==TRUE)
		{
			// filtros
			$ci = &get_instance();
			$filtro = resgatar_filtro( $id );
			if(  trim( $filtro )!=''  )
			{
				$value=$filtro;
			}
			/////
		}
		
		$js_autocomplete = '
							<script>
								function '.$id.'_function_autocomplete()
								{
								   $("#'.$id.'").autocomplete({
										minLength: 2,
										source: function(request, response) {
											$.ajax({
												url      : "'.$source.'",
												data     : request,
												dataType : "json",
												type     : "POST",
												success  : function(data){
													response(data);
													$("#'.$id.'").removeClass("autocompleteLoading");
												}
											});
										},			
										select: function(event, ui) {
											'.(trim($callback) != "" ? $callback.'(ui);' : '').'
										},
										search : function(){
											$("#'.$id.'").addClass("autocompleteLoading");
										},
										open : function(){
											$("#'.$id.'").removeClass("autocompleteLoading");
										}
								   });                                
								}	

								'.$id.'_function_autocomplete();
							</script>
		                   ';
		

		$attr = array("name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);");

		$output = "
					<tr id='".$id."_row'>
						<td class='coluna-padrao-form'><label class='label-padrao-form' for='".$id."'>".$caption."</label></td>
						<td class='coluna-padrao-form-objeto'>
							".form_input($attr, $value, $extra)."
							".$js_autocomplete."
						</td>
					</tr>
		          ";

		return $output;
	}
}



if ( ! function_exists('form_default_text'))
{
	/**
	 * Text Input Field - Date Object
	 *
	 * @param int $id				NAME e ID do input
	 * @param string $caption		Legenda
	 * @param string/array $value	Passar o valor do campo, ou um array contendo um campo com nome igual ao $id
	 * @param string $extra			Extensão para atributos do objeto INPUT (p.ex.:    style='display:none;'
	 */
	function form_default_text($id, $caption="", $value="", $extra="", $maxlength=0, $filtro=false)
	{
		if(is_array($value))
		{
			if(isset($value[$id])) $value = $value[$id]; else $value='';
		}

		if(trim($value)=='' && $filtro==TRUE)
		{
			// filtros
			$ci = &get_instance();
			$filtro = resgatar_filtro( $id );
			if(  trim( $filtro )!=''  )
			{
				$value=$filtro;
			}
			/////
		}

		$attr = array( "name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);" );

		$output = "
		<tr id='".$id."_row'>
		<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
		<td class='coluna-padrao-form-objeto'>
			".form_input($attr, $value, $extra)."
			</td>
		</tr>
		";

		return $output . "\n";
	}
}

function form_default_password($id, $caption="", $value="", $extra="", $maxlength=0)
{
	if(is_array($value))
	{
		if(isset($value[$id])) $value = $value[$id]; else $value='';
	}

	$attr = array( "name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);" );

	$output = "
	<tr>
	<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
	<td class='coluna-padrao-form-objeto'>
		".form_password($attr, $value, $extra)."
		</td>
	</tr>
	";

	return $output . "\n";
}


function form_default_upload($id, $caption="", $value="", $extra="")
{
	if(is_array($value))
	{
		if(isset($value[$id])) $value = $value[$id]; else $value='';
	}

	$attr = array( "name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);" );
	$output = "
	<tr>
	<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
	<td class='coluna-padrao-form-objeto'>
		".form_upload($attr, $value, $extra)."
		</td>
	</tr>
	";

	return $output . "\n";
}

/**
 * Cria um objeto padrão de UPLOAD integrado a um IFRAME que já programa o
 * upload na pasta informada, e exibe um link para abrir o arquivo assim que o
 * upload é feito
 * 
 * @param string $id nome do objeto que receberá o nome do arquivo depois do upload, esse parametro forma o nome dos seguintes objetos.
 * 
 * $id : HIDDEN com o nome gerado para o arquivo
 * 
 * $id_nome : HIDDEN com o nome do arquivo antes de realizar o UPLOAD - nome amigável
 * 
 * $id_file : INPUT FILE
 * 
 * @param string $pasta Pasta no servidor para upload do arquivo
 * 
 * @param string $caption opcional Rótulo da linha criada para o objeto
 * 
 * @param string $value opcional Para o caso do campo ser carregado já preenchido. Deve ser informado no padrão String "[ARQUIVO]|[NOME_AMIGAVEL]" ou Array("[ARQUIVO]","[NOME_AMIGAVEL]")
 * 
 * @param string $raiz 
 * 
 * @param boolean $fl_editar opcional permitir editar arquivo anexado 
 *
 */
function form_default_upload_iframe($id, $pasta, $caption="", $value="", $raiz="", $fl_editar=TRUE, $callback="")
{
	if(is_array($value))
	{
		$value = implode("|", $value);
	}
	
	if(trim($value) == "|")
	{
		$value = "";
	}
	
	$js = "
			<script>
				function enviar_arquivo_$id(f,act)
				{
					if(jQuery.trim($('#".$id."_file').val()) == '')
					{
						alert('Informe o arquivo');
					}
					else
					{
						old_act=f.action;
						enc=f.encoding;
						f.target='".$id."_upload_iframe';
						f.encoding='multipart/form-data';
						f.action=act;
						f.submit();

						f.action=old_act;
						f.encoding=enc;
						f.target='';
					}
				}
				function sucesso_$id(request)
				{
					var ar_request = request.toString().split('|');
					$('#".$id."').val( ar_request[0] );
					$('#".$id."_nome').val(ar_request[1]);

					$('#".$id."_div').html('<a href=\"".base_url()."up/".$pasta."/'+ar_request[0]+'\" target=\"_blank\">[<img src=\"".base_url()."img/arquivo_ver.png\" border=\"0\"> ver arquivo]</a>&nbsp&nbsp&nbsp&nbsp<a id=\"".$id."_remover_file\" href=\"javascript:void(0);\" onclick=\"remover_arquivo_$id(false);\">[<img src=\"".base_url()."img/arquivo_excluir.png\" border=\"0\"> remover arquivo]</a>');

					$('#".$id."_resetar_file').html('<INPUT TYPE=\"file\" NAME=\"".$id."_file\" id=\"".$id."_file\" SIZE=\"40\">');
					$('#".$id."_resetar_file_cancelar').html('<INPUT CLASS=\"btn btn-mini\" TYPE=\"button\" VALUE=\"Cancelar\" ONCLICK=\"sucesso_".$id."(\\'' + request + '\\');\">');
					
					$('#".$id."_upload_div').hide();
					
					".$callback."
				}
				function falha_$id(request)
				{
					var msg = request.replace('<p>','');
						msg = msg.replace('</p>','');
					
					alert(jQuery.trim(msg));
				}
				function remover_arquivo_$id(fl_sem_msg)
				{
					if(typeof fl_sem_msg === 'undefined') 
					{  
						fl_sem_msg = false;
					}
					
					if(fl_sem_msg)
					{
						$('#".$id."').val('');
						$('#".$id."_nome').val('');
						$('#".$id."_div').html('');
						$('#".$id."_upload_div').show();
					}
					else
					{
						var confirmacao = 'Deseja remover o arquivo?\\n\\nPara SIM clique [Ok] e para NÃO clique [Cancelar]';
						if(confirm(confirmacao))
						{
							$('#".$id."').val('');
							$('#".$id."_nome').val('');
							$('#".$id."_div').html('');
							$('#".$id."_upload_div').show();
						}					
					}
					
				}
				".((trim($value) != '') ? "sucesso_$id('".$value."');" : "" )."
				
				".((!$fl_editar) ? "$('#".$id."_remover_file').hide()" : "" )."
			</script>
          ";

	$objetos = "
				<INPUT TYPE='hidden' NAME='".$id."' ID='".$id."'>
				<INPUT TYPE='hidden' NAME='".$id."_nome' ID='".$id."_nome'>
				<div id='".$id."_div'></div>
				<div id='".$id."_upload_div'>
					<SPAN id='".$id."_resetar_file'><INPUT TYPE='file' NAME='".$id."_file' id='".$id."_file' SIZE='40'></SPAN>
					<SPAN><INPUT CLASS='btn btn-mini btn-info' TYPE='button' VALUE='Anexar arquivo' ONCLICK='enviar_arquivo_".$id."(this.form, \"".site_url("/geral/upload/".$id."_file/".$pasta."/sucesso_".$id."/falha_".$id."/".$raiz)."\")'></SPAN>
					<SPAN id='".$id."_resetar_file_cancelar'>".((trim($value) != '') ? "<INPUT CLASS='btn btn-mini' TYPE='button' VALUE='Cancelar' ONCLICK='sucesso_".$id."(\"".$value."\");'>" : "" )."</SPAN>
				</div>
				<IFRAME NAME='".$id."_upload_iframe' style='display:none;'></IFRAME>
				";
	
	$output = "
				<tr id='".$id."_row'>
					<td class='coluna-padrao-form'><label class='label-padrao-form' for='".$id."'>".$caption."</label></td>
					<td class='coluna-padrao-form-objeto'>".$objetos.$js."</td>
				</tr>
			  ";

	return $output;
}

/**
 * form_default_cep
 * Renderiza input text com mascara para CEP (99999-999)
 *
 * @param array $config		pacote de parametros opcionais para configuração do objeto
 *							$config['extra']	string		atributos do objeto INPUT
 *							$config['db']		boolean		true para realizar consulta na base resgatando uf, cidade, logradouro e bairro
 *							$config['callback_function']	string		nome da função javascript para retornar informações da consulta ajax por CEP
 *							$config['return_type']			string		json/string		definição do tipo de retorno, por enquanto suporte a JSON ou Stirng separada por PIPE (|)
 */
function form_default_cep($id, $caption="", $value="", $config=array())
{
	$config['extra'] = ( isset($config['extra']) )?$config['extra']:"";
	$config['db'] = ( isset($config['db']) )?$config['db']:FALSE;
	$config['callback_function'] = ( isset($config['callback_function']) )?$config['callback_function']:"";
	$config['return_type'] = ( isset($config['return_type']) )?$config['return_type']:"string";

	if(is_array($value))
	{
		if(isset($value[$id])) $value = $value[$id]; else $value='';
	}

	$attr = array( "name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);" );



	$db_bt = "";
	$db_js = "";
	if($config['db'])
	{
		$post = "";
		if($config['return_type']=='json')
		{
			$post = '$.post( url, { cep:$("#'.$id.'").val(), return_type:"'.$config['return_type'].'"}, '.$config['callback_function'].', "json" );';
		}
		else
		{
			$post = '$.post( url, { cep:$("#'.$id.'").val(), return_type:"'.$config['return_type'].'"}, '.$config['callback_function'].' );';
		}

		$db_js = '
					if($("#'.$id.'").val() != "")
					{
						url = "'.site_url('geral/consultar_cep_ajax').'";
						'.$post.'
					}
		         ';
		$db_bt = "<input type='button' class='btn btn-mini' value='Consultar' onclick='consultar_cep_" . $id . "();'>";
	}

	
	$js = '
		<script type="text/javascript">
			function consultar_cep_'.$id.'()
			{
				'.$db_js.'
			}

			jQuery(function($){
			   $("#'.$id.'").mask("99999-999", { completed: function(){ consultar_cep_'.$id.'(); }});
			});
		</script>
		';	
	
	$output = "
	<tr>
	<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
	<td class='coluna-padrao-form-objeto'>
		" . form_input($attr, $value, $config['extra']) . $db_bt . $js . "
		</td>
	</tr>
	";

	return $output . "\n";
}

function form_default_decimal($id, $caption="", $value="", $config=array())
{
	$config['prefix'] = ( isset($config['prefix']) )?$config['prefix']:"";
	$config['centsSeparator'] = ( isset($config['centsSeparator']) )?$config['centsSeparator']:",";
	$config['thousandsSeparator'] = ( isset($config['thousandsSeparator']) )?$config['thousandsSeparator']:".";
	$config['limit'] = ( isset($config['limit']) )?$config['limit']:"9";
	$config['centsLimit'] = ( isset($config['centsLimit']) )?$config['centsLimit']:"2";

	$config['extra'] = ( isset($config['extra']) )?$config['extra']:"";

	if(is_array($value))
	{
		if(isset($value[$id])) $value = $value[$id]; else $value='';
	}

	$attr = array( "name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);" );

	$js = "
	<script type='text/javascript'>
		$('#".$id."').priceFormat({
			prefix: '".$config['prefix']."'
			, centsSeparator: '".$config['centsSeparator']."'
			, thousandsSeparator: '".$config['thousandsSeparator']."'
			, limit: ".$config['limit']."
			, centsLimit: ".$config['centsLimit']."
		});
	</script>
	";

	$output = "
	<tr>
	<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
	<td class='coluna-padrao-form-objeto'>
		" . form_input($attr, $value, $config['extra']) . $js . "
		</td>
	</tr>
	";

	return $output . "\n";
}

function form_default_telefone($id, $caption="", $value="", $extra="")
{
	if(is_array($value))
	{
		if(isset($value[$id])) $value = $value[$id]; else $value='';
	}

	$attr = array( "name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);" );

	$js = '
		<script type="text/javascript">
		    jQuery(function($){
			   $("#'.$id.'").mask("(99)999999999");
			});
		</script>
	';

	$output = "
	<tr>
	<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
	<td class='coluna-padrao-form-objeto'>
		" . form_input($attr, $value, $extra) . $js . "
		</td>
	</tr>
	";

	return $output . "\n";
}

function form_default_mask($id, $caption="", $mask="", $value="", $extra="")
{
	if(is_array($value))
	{
		if(isset($value[$id])) $value = $value[$id]; else $value='';
	}

	$attr = array( "name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);" );

	$js = '
		<script type="text/javascript">
		    jQuery(function($){
			   $("#'.$id.'").mask("'.$mask.'");
			});
		</script>
	';

	$output = "
	<tr>
	<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
	<td class='coluna-padrao-form-objeto'>
		".form_input($attr, $value, $extra).$js."
		</td>
	</tr>
	";

	return $output . "\n";
}

if ( ! function_exists('form_default_hidden'))
{
	/**
	 * Campo com parametros equivalentes ao form_default_text
	 * porém não usa os parametros caption, extra e maxlen,
	 * mas os parametros são mantidos para facilitar os testes
	 * quando desejar exibir os valores basta trocar a função 
	 * de _hidden para _text
	 *
	 * @param string $id
	 * @param string $caption	não usado
	 * @param $value
	 * @param $extra			não usado
	 * @param $maxlen			não usado
	 *
	 * @return string
	 */
	function form_default_hidden($id, $caption="", $value="", $extra="", $maxlen=0, $filtro=false)
	{
		if(is_array($value))
		{
			$value = ((isset($value[$id])) ? $value = $value[$id] : "");
		}
		
		if((trim($value) == "") and ($filtro == TRUE))
		{
			$vl_filtro = resgatar_filtro($id);
			$value = ((trim($vl_filtro) != "") ? $vl_filtro : $value);
		}		

		$output = "
					<tr style='display:none;' id='".$id."_row'>
						<td class='coluna-padrao-form'></td>
						<td class='coluna-padrao-form-objeto'>
							".form_hidden($id, $value)."
						</td>
					</tr>
				 ";

		return $output;
	}
}

if( ! function_exists('form_default_empresa') )
{

	function form_default_empresa($id, $empresa_selecionada="", $label="Empresa", $tipo_empresa="", $extra="", $filtro=false)
	{
		$ci = &get_instance();
		
		if(trim($empresa_selecionada)=='' && $filtro==true)
		{
			$filtro = resgatar_filtro( $id );
			if(  trim( $filtro )!=''  )
			{
				$empresa_selecionada=$filtro;
			}
		}
	
		$query = $ci->db->query("
								SELECT cd_empresa AS value, 
								       sigla AS text 
							      FROM public.patrocinadoras 
								 WHERE 1 = 1
								 ".(trim($tipo_empresa) != "" ? "AND tipo_cliente = '".trim($tipo_empresa)."'" : "")."
								 ORDER BY sigla
							    ");
		$empresa_dd = $query->result_array();
	
		return form_default_dropdown($id, $label, $empresa_dd, array($empresa_selecionada), $extra);
	}
}

if ( ! function_exists('form_default_integer'))
{
	/**
	 * Text Input Field - Date Object
	 *
	 * @param int $id				NAME e ID do input
	 * @param string $caption		Legenda
	 * @param string/array $value	Passar o valor do campo, ou um array contendo um campo com nome igual ao $id
	 * @param string $extra			Extensão para atributos do objeto INPUT (p.ex.:    style='display:none;'
	 */
	function form_default_integer($id, $caption="", $value="", $extra="", $filtro=false)
	{
		if(is_array($value))
		{
			if(isset($value[$id])) $value = $value[$id]; else $value='';
		}
	
		if(trim($value)=='' && $filtro==true)
		{
			// filtros
			$ci = &get_instance();
			$filtro = resgatar_filtro( $id );
			if(  trim( $filtro )!=''  )
			{
				$value=$filtro;
			}
			/////
		}

		$attr = array( "name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);" );

		$id_row = $id."_row";
		
		$output = "
		<tr id='$id_row'>
		<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
		<td class='coluna-padrao-form-objeto'>
			".form_input($attr, $value, $extra)."
			</td>
		</tr>
		";

		$js="<script>
		jQuery(function($){
		   $('#".$id."').numeric();
		});
		</script>";

		return $output . $js . "\n";
	}
}


function filter_cnpj($id, $caption="", $value="", $extra="")
{
	return form_default_cnpj($id, $caption, $value, $extra,TRUE);
}

if ( ! function_exists('form_default_cnpj'))
{
	/**
	 * Text Input Field - Date Object
	 *
	 * @param int $id				NAME e ID do input
	 * @param string $caption		Legenda
	 * @param string/array $value	Passar o valor do campo, ou um array contendo um campo com nome igual ao $id
	 * @param string $extra			Extensão para atributos do objeto INPUT (p.ex.:    style='display:none;'
	 */
	function form_default_cnpj($id, $caption="", $value="", $extra="", $filtro=false)
	{
		if(is_array($value))
		{
			if(isset($value[$id])) $value = $value[$id]; else $value='';
		}

		if(trim($value)=='' && $filtro==true)
		{
			// filtros
			$ci = &get_instance();
			$filtro = resgatar_filtro( $id );
			if(  trim( $filtro )!=''  )
			{
				$value=$filtro;
			}
			/////
		}

		$attr = array("name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);" );

		$id_row = $id."_row";
		
		$output = "
					<tr id='".$id_row."'>
						<td class='coluna-padrao-form'><label class='label-padrao-form' for='".$id."'>".$caption."</label></td>
						<td class='coluna-padrao-form-objeto'>
							".form_input($attr, $value, $extra)."
							<script>
								jQuery(function($){
								   $('#".$id."').mask('99.999.999/9999-99');
								});
							</script>							
						</td>
					</tr>
		          ";
		return $output;
	}
}


if ( ! function_exists('form_default_cpf'))
{
	/**
	 * Text Input Field - Date Object
	 *
	 * @param int $id				NAME e ID do input
	 * @param string $caption		Legenda
	 * @param string/array $value	Passar o valor do campo, ou um array contendo um campo com nome igual ao $id
	 * @param string $extra			Extensão para atributos do objeto INPUT (p.ex.:    style='display:none;'
	 */
	function form_default_cpf($id, $caption="", $value="", $extra="", $filtro=false)
	{
		if(is_array($value))
		{
			if(isset($value[$id])) $value = $value[$id]; else $value='';
		}

		if(trim($value)=='' && $filtro==true)
		{
			// filtros
			$ci = &get_instance();
			$filtro = resgatar_filtro( $id );
			if(  trim( $filtro )!=''  )
			{
				$value=$filtro;
			}
			/////
		}

		$attr = array("name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);" );

		$id_row = $id."_row";
		
		$output = "
					<tr id='".$id_row."'>
						<td class='coluna-padrao-form'><label class='label-padrao-form' for='".$id."'>".$caption."</label></td>
						<td class='coluna-padrao-form-objeto'>
							".form_input($attr, $value, $extra)."
							<script>
								jQuery(function($){
								   $('#".$id."').mask('999.999.999-99',{completed:function(){ if(!$('#".$id."').validateCPF()){ alert('ATENÇÃO\\n\\nCPF INVÁLIDO\\n\\n');	$('#".$id."').val(''); $('#".$id."').focus(); }}});
								});
							</script>							
						</td>
					</tr>
		          ";
		return $output;
	}
}



if ( ! function_exists('form_default_float'))
{
	/**
	 * Text Input - configurado para números decimais
	 *
	 * @param int $id				NAME e ID do input
	 * @param string $caption		Legenda
	 * @param string/array $value	Passar o valor do campo, ou um array contendo um campo com nome igual ao $id
	 * @param string $extra			Extensão para atributos do objeto INPUT (p.ex.:    style='display:none;'
	 */
	function form_default_float($id, $caption="", $value="", $extra="")
	{
		if(is_array($value))
		{
			if(isset($value[$id])) $value = $value[$id]; else $value='';
		}

		$attr = array( "name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);" );

		$id_row = $id."_row";
		
		$output = "
		<tr id='$id_row'>
		<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
		<td class='coluna-padrao-form-objeto'>
			".form_input($attr, $value, $extra)."
			</td>
		</tr>
		";

		$js = "";
		/*
		$js = "
				<script>
					$('#".$id."').priceFormat({
						prefix: '',
						centsSeparator: ',',
						thousandsSeparator: '.'
					}); 
					$('#".$id."').css('text-align','right');					
				</script>		
		      ";
		*/
		return $output.$js."\n";
	}
}

if ( ! function_exists('form_default_numeric'))
{
	/**
	 * Text Input - configurado para números decimais
	 *
	 * @param int $id				NAME e ID do input
	 * @param string $caption		Legenda
	 * @param string/array $value	Passar o valor do campo, ou um array contendo um campo com nome igual ao $id
	 * @param string $extra			Extensão para atributos do objeto INPUT (p.ex.:    style='display:none;'
	 */
	function form_default_numeric($id, $caption="", $value="", $extra="", $config=array())
	{
		if(is_array($value))
		{
			if(isset($value[$id]))
			{
				$value = $value[$id]; 
			}
			else 
			{
				$value='';
			}
		}
		
		if(is_array($config))
		{
			$config['prefix']             = ((array_key_exists("prefix", $config))             ? $config['prefix']             : "");
			$config['clearPrefix']        = ((array_key_exists("clearPrefix", $config))        ? $config['clearPrefix']        : "false");
			$config['suffix']             = ((array_key_exists("suffix", $config))             ? $config['suffix']             : "");
			$config['clearSufix']         = ((array_key_exists("clearSufix", $config))         ? $config['clearSufix']         : "false");
			$config['centsSeparator']     = ((array_key_exists("centsSeparator", $config))     ? $config['centsSeparator']     : ",");
			$config['thousandsSeparator'] = ((array_key_exists("thousandsSeparator", $config)) ? $config['thousandsSeparator'] : ".");
			$config['centsLimit']         = ((array_key_exists("centsLimit", $config))         ? intval($config['centsLimit']) : 2);
			$config['allowNegative']      = ((array_key_exists("allowNegative", $config))      ? $config['allowNegative']      : "true");
			
			$attr = array( "name"=>$id, "id"=>$id, "onkeypress"=>"handleEnter(this, event);" );
			
			$js = "
					<script>
						$('#".$id."').priceFormat({
							prefix             : '".$config['prefix']."',
							clearPrefix        : ".$config['clearPrefix'].",
							suffix             : '".$config['suffix']."',
							clearSufix         : ".$config['clearSufix'].",
							centsSeparator     : '".$config['centsSeparator']."',
							thousandsSeparator : '".$config['thousandsSeparator']."',
							centsLimit         : ".$config['centsLimit'].",
							allowNegative      : ".$config['allowNegative']."
						}); 
					</script>
				  ";			
			
			$campo = form_input($attr, $value, $extra).$js;
		}
		else
		{
			$campo = "<b style='color:red'>ERRO - Parâmetro config não é array</b>";
		}

		$id_row = $id."_row";
		
		$output = "
					<tr id='".$id_row."'>
					<td class='coluna-padrao-form'><label class='label-padrao-form' for='".$id."'>".$caption."</label></td>
					<td class='coluna-padrao-form-objeto'>
						".$campo."
						</td>
					</tr>
		          ";


		return $output;
	}
}

if ( ! function_exists('form_default_info'))
{
	/**
	 * Linha padrão de formulário para inserir apenas uma informação dentro de um <SPAN>
	 *
	 * @param unknown_type $id
	 * @param unknown_type $caption
	 * @param unknown_type $value
	 * @return unknown
	 */
	function form_default_info($id, $caption="", $value="")
	{
		$attr = array( "name"=>$id, "id"=>$id );

		$output = "
		<tr>
		<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
		<td class='coluna-padrao-form-objeto'><span id='$id'>$value</span></td>
		</tr>
		";

		return $output . "\n";
	}
}

if ( ! function_exists('form_default_editor_code'))
{
	/**
	 * Text Input Field - Date Object
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_default_editor_code($id, $caption="", $value="",$extra="",$tipo="sql")
	{
		$width = 500;
		$height = 300;
		if(trim($extra) != "")
		{
			$p = strpos(strtolower($extra), 'height');
			$a = substr(strtolower($extra), $p);
			$p = strpos($a, ';');
			$a = substr($a, 0, $p);
			$a = str_replace('height','',$a);
			$a = str_replace(':','',$a);
			$a = str_replace('px','',$a);
			$a = str_replace('%','',$a);
			$height = intval($a);
			
			$p = strpos(strtolower($extra), 'width');
			$a = substr(strtolower($extra), $p);
			$p = strpos($a, ';');
			$a = substr($a, 0, $p);
			$a = str_replace('width','',$a);
			$a = str_replace(':','',$a);
			$a = str_replace('px','',$a);
			$a = str_replace('%','',$a);
			$width = intval($a);			
		}
		
		$mode = ($tipo == "sql" ? "text/x-sql" : $tipo);
		
		$js = "
				<script type='text/javascript'>
					$(function() {
						CodeMirror.modeURL = '".base_url()."js/codemirror-3.14/mode/%N/%N.js';
						var editor = CodeMirror.fromTextArea(document.getElementById('".$id."'), { 
										mode: '".$mode."',
										styleActiveLine: true,
										showCursorWhenSelecting: true,
										lineWrapping: true,
										indentWithTabs: true,
										smartIndent: true,
										lineNumbers: true,
										matchBrackets : true,
										autofocus: true
									});
							editor.setSize(".$width.", ".$height.");
						CodeMirror.autoLoadMode(editor, '".$tipo."');	
						
						$('.CodeMirror').resizable({
							resize: function() {
								editor.setSize($(this).width(), $(this).height());
							}
						});							
					});				
				</script>		
		      ";
		if(is_array($value))
		{
			$value = $value[$id];
		}
		
		$attr = array("name"=>$id, "id"=>$id);

		$output = "
					<tr id='".$id."_row'>
						<td class='coluna-padrao-form' valign='top'><label class='label-padrao-form' for='".$id."'>".$caption."</label></td>
						<td class='coluna-padrao-form-objeto'>
							".form_textarea($attr,$value,$extra)."
							".$js."
						</td>
					</tr>
		          ";
		return $output;
	}
}

if ( ! function_exists('form_default_editor_html_tinymce'))
{
	/**
	 * Text Input Field - Date Object
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_default_editor_html_tinymce($id, $caption="", $value="",$extra="",$fl_url_absoluta=false)
	{
		$height = 0;
		if(trim($extra) != "")
		{
			$p = strpos(strtolower($extra), 'height');
			$a = substr(strtolower($extra), $p);
			$p = strpos($a, ';');
			$a = substr($a, 0, $p);
			$a = str_replace('height','',$a);
			$a = str_replace(':','',$a);
			$a = str_replace('px','',$a);
			$a = str_replace('%','',$a);
			$height = intval($a);
			
			
			$p = strpos(strtolower($extra), 'width');
			$a = substr(strtolower($extra), $p);
			$p = strpos($a, ';');
			$a = substr($a, 0, $p);
			$a = str_replace('width','',$a);
			$a = str_replace(':','',$a);
			$a = str_replace('px','',$a);
			$a = str_replace('%','',$a);
			$width = intval($a);			
		}

		//($(document).width() - ($(document).width() 0.7))

		$filemanager        = base_url()."responsive_filemanager/filemanager/";
		$editor_html_upload = site_url("geral/editor_html_upload");
		
		$js = "
				<script type='text/javascript'>
					tinymce.init({
						selector: 'textarea#".$id."',
						language: 'pt_BR',
						width : 1000,
						height: 600,
						plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons responsivefilemanager',
						imagetools_cors_hosts: ['picsum.photos'],
						menubar: 'file edit view insert format tools table help',
						toolbar: 'undo redo | fullscreen  preview | responsivefilemanager insertfile image media link anchor | removeformat bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor | pagebreak | charmap emoticons | save print | ltr rtl',
						toolbar_sticky: true,
						autosave_ask_before_unload: true,
						autosave_interval: '30s',
						autosave_prefix: '{path}{query}-{id}-',
						autosave_restore_when_empty: false,
						autosave_retention: '2m',
						image_advtab: true,
						content_css: '//www.tiny.cloud/css/codepen.min.css',
						importcss_append: true,
						template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
						template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',

						image_caption: true,
						quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
						noneditable_noneditable_class: 'mceNonEditable',
						toolbar_mode: 'sliding',
						contextmenu: 'link image imagetools table',

						relative_urls: false,
						convert_urls: false,
						
						external_filemanager_path: '".$filemanager."',
						filemanager_title: 'Responsive Filemanager' ,
						external_plugins: { 'filemanager' : '".$filemanager."/plugin.min.js' },						
					  
					  
						images_upload_url : '".$editor_html_upload."',
						automatic_uploads : true,

						images_upload_handler : function(blobInfo, success, failure) {
							var xhr, formData;

							xhr = new XMLHttpRequest();
							xhr.withCredentials = false;
							xhr.open('POST', '".$editor_html_upload."');

							xhr.onload = function() {
								var json;

								if (xhr.status != 200) {
									failure('HTTP Error: ' + xhr.status);
									return;
								}

								json = JSON.parse(xhr.responseText);

								if (!json || typeof json.file_path != 'string') {
									failure('Invalid JSON: ' + xhr.responseText);
									return;
								}

								success(json.file_path);
							};

							formData = new FormData();
							formData.append('file', blobInfo.blob(), blobInfo.filename());

							xhr.send(formData);
						},					  
  
					});
				</script>		
		      ";	
			  
		if(is_array($value))
		{
			$value = $value[$id];
		}
		
		$attr = array("name"=>$id, "id"=>$id);

		$output = "
					<tr id='".$id."_row'>
						<td class='coluna-padrao-form' valign='top'><label class='label-padrao-form' for='".$id."'>".$caption."</label></td>
						<td class='coluna-padrao-form-objeto'>
							".form_textarea($attr,$value,$extra)."
							".$js."
						</td>
					</tr>
		          ";
		return $output;
	}
}

if ( ! function_exists('form_default_editor_html'))
{
	/**
	 * Text Input Field - Date Object
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_default_editor_html($id, $caption="", $value="",$extra="",$fl_url_absoluta=false)
	{
		$height = 0;
		if(trim($extra) != "")
		{
			$p = strpos(strtolower($extra), 'height');
			$a = substr(strtolower($extra), $p);
			$p = strpos($a, ';');
			$a = substr($a, 0, $p);
			$a = str_replace('height','',$a);
			$a = str_replace(':','',$a);
			$a = str_replace('px','',$a);
			$a = str_replace('%','',$a);
			$height = intval($a);
		}

		$flAbsoluteURL = "?flAbsoluteURL=0";
		if($fl_url_absoluta)
		{
			$flAbsoluteURL = "?flAbsoluteURL=1";
		}

		
		$pdw_file_browser = base_url()."pdw_file_browser/".$flAbsoluteURL;
		
		$filemanager        = base_url()."responsive_filemanager/filemanager";
		$editor_html_upload = site_url("geral/editor_html_upload");		
		
		$js = "
				<script type='text/javascript'>
					CKEDITOR.config.font_names =
						'Arial/Arial, Helvetica, sans-serif;' +
						'Calibri/Calibri, Arial, sans-serif;' +
						'Comic Sans MS/Comic Sans MS, cursive;' +
						'Courier New/Courier New, Courier, monospace;' +
						'Georgia/Georgia, serif;' +
						'Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;' +
						'Tahoma/Tahoma, Geneva, sans-serif;' +
						'Times New Roman/Times New Roman, Times, serif;' +
						'Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;' +
						'Verdana/Verdana, Geneva, sans-serif';	
					
					CKEDITOR.replace(
						'".$id."',
						{
							toolbar:
							[
								[ 'Maximize','-','SelectAll','-','Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo','-','Find','Replace' ],
								[ 'Font','FontSize','-','SpecialChar'], 
								[ 'Bold','Italic','Underline','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','TextColor','BGColor','-','Strike','Subscript','Superscript','-','RemoveFormat','-','NumberedList','BulletedList','-','Outdent','Indent' ],
								[ 'Link','Unlink','-','Image','Table','-','HorizontalRule','Anchor'],
								[ 'ShowBlocks','Templates','Source' ],
								[ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ]
							],
         
							".($height > 0 ? "height: '".$height."px'," : "")."
		 
							filebrowserBrowseUrl : '".$filemanager."/dialog.php?type=2&editor=ckeditor&fldr=',
							filebrowserUploadUrl : '".$filemanager."/dialog.php?type=2&editor=ckeditor&fldr=',
							filebrowserImageBrowseUrl : '".$filemanager."/dialog.php?type=1&editor=ckeditor&fldr=',
							
							skin : 'office2003',
							language: 'pt-br',
							enterMode : CKEDITOR.ENTER_BR
						}
					);
					
				</script>		
		      ";
		if(is_array($value))
		{
			$value = $value[$id];
		}
		
		$attr = array("name"=>$id, "id"=>$id);

		$output = "
					<tr id='".$id."_row'>
						<td class='coluna-padrao-form' valign='top'><label class='label-padrao-form' for='".$id."'>".$caption."</label></td>
						<td class='coluna-padrao-form-objeto'>
							".form_textarea($attr,$value,$extra)."
							".$js."
						</td>
					</tr>
		          ";
		return $output;
	}
}


if ( ! function_exists('form_default_editor_html_bkp_20200715'))
{
	/**
	 * Text Input Field - Date Object
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_default_editor_html_bkp_20200715($id, $caption="", $value="",$extra="",$fl_url_absoluta=false)
	{
		$height = 0;
		if(trim($extra) != "")
		{
			$p = strpos(strtolower($extra), 'height');
			$a = substr(strtolower($extra), $p);
			$p = strpos($a, ';');
			$a = substr($a, 0, $p);
			$a = str_replace('height','',$a);
			$a = str_replace(':','',$a);
			$a = str_replace('px','',$a);
			$a = str_replace('%','',$a);
			$height = intval($a);
		}

		$flAbsoluteURL = "?flAbsoluteURL=0";
		if($fl_url_absoluta)
		{
			$flAbsoluteURL = "?flAbsoluteURL=1";
		}

		
		$pdw_file_browser = base_url()."pdw_file_browser/".$flAbsoluteURL;
		$js = "
				<script type='text/javascript'>
					CKEDITOR.config.font_names =
						'Arial/Arial, Helvetica, sans-serif;' +
						'Calibri/Calibri, Arial, sans-serif;' +
						'Comic Sans MS/Comic Sans MS, cursive;' +
						'Courier New/Courier New, Courier, monospace;' +
						'Georgia/Georgia, serif;' +
						'Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif;' +
						'Tahoma/Tahoma, Geneva, sans-serif;' +
						'Times New Roman/Times New Roman, Times, serif;' +
						'Trebuchet MS/Trebuchet MS, Helvetica, sans-serif;' +
						'Verdana/Verdana, Geneva, sans-serif';	
					
					CKEDITOR.replace(
						'".$id."',
						{
							toolbar:
							[
								[ 'Maximize','-','SelectAll','-','Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo','-','Find','Replace' ],
								[ 'Font','FontSize','-','SpecialChar'], 
								[ 'Bold','Italic','Underline','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','TextColor','BGColor','-','Strike','Subscript','Superscript','-','RemoveFormat','-','NumberedList','BulletedList','-','Outdent','Indent' ],
								[ 'Link','Unlink','-','Image','Table','-','HorizontalRule','Anchor'],
								[ 'ShowBlocks','Templates','Source' ],
								[ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ]
							],
         
							".($height > 0 ? "height: '".$height."px'," : "")."
		 
							filebrowserBrowseUrl : '".$pdw_file_browser."',
							filebrowserImageBrowseUrl : '".$pdw_file_browser."',
							skin : 'office2003',
							language: 'pt-br',
							enterMode : CKEDITOR.ENTER_BR
						}
					);
					
				</script>		
		      ";
		if(is_array($value))
		{
			$value = $value[$id];
		}
		
		$attr = array("name"=>$id, "id"=>$id);

		$output = "
					<tr id='".$id."_row'>
						<td class='coluna-padrao-form' valign='top'><label class='label-padrao-form' for='".$id."'>".$caption."</label></td>
						<td class='coluna-padrao-form-objeto'>
							".form_textarea($attr,$value,$extra)."
							".$js."
						</td>
					</tr>
		          ";
		return $output;
	}
}

if ( ! function_exists('form_default_textarea'))
{
	/**
	 * Text Input Field - Date Object
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_default_textarea($id, $caption="", $value="",$extra="")
	{
		if(is_array($value))
		{
			$value = $value[$id];
		}
		
		$attr = array( "name"=>$id, "id"=>$id, "class"=>"resizable" );

		$output = "
		<tr id='".$id."_row'>
		<td class='coluna-padrao-form' valign='top'><label class='label-padrao-form' for='$id'>$caption</label></td>
		<td class='coluna-padrao-form-objeto'>
			".form_textarea($attr,$value,$extra)."
			<BR>
			
			</td>
		</tr>
		";

		return $output . "\n";
	}
}

if ( ! function_exists('form_default_row') )
{
	/**
	 * Linha da tabela que forma o formulário com inserção de qualquer objeto
	 *
	 * @param 	string 	$caption	Legenda
	 * @param 	string 	$content	Conteúdo da 2ª coluna - html do objeto
	 * @return unknown
	 */
	function form_default_row($id, $caption, $content)
	{
		$output = "
					<tr id='".$id."_row'>
						<td class='coluna-padrao-form' valign='top'><label class='label-padrao-form'>".$caption."</label></td>
						<td class='coluna-padrao-form-objeto' id='".$id."_item'>".$content."</td>
					</tr>
				  ";

		return $output;
	}
}

if ( ! function_exists('form_start_box') )
{
	/**
	 * Iniciar uma Caixa padrão do sistema
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_start_box($id,$caption,$with_table=TRUE,$abre_fecha=TRUE,$extra="")
	{		
		$botao = "";
		
		if($with_table) $table="<table cellpadding='10' border='0'>"; else $table="";
		
		if($abre_fecha)
		{
			$botao = "<div style='float:right;margin-right:10px;' id='".$id."_btn_box'>
				".form_hidden($id."_btn_funcao_box", 0)."
				<a href='javascript:void(0)' style='font-weight:bold; text-decoration: none;' onclick='".$id."_box_recolher()' id='".$id."_box_recolher' title='Recolher'>
					<img src='".base_url()."img/box_recolhe.png' border='0'/>
				</a>
			</div>";
		}

		$output = "
		<script>
			function ".$id."_box_recolher()
			{
				var funcao = $('#".$id."_btn_funcao_box').val();
				
				if(funcao == 0)
				{
					$('#".$id."_btn_funcao_box').val(1);
					$('#".$id."_content').fadeOut(200);
					$('#".$id."_box_recolher').html(\"<img src='".base_url()."img/box_expande.png' border='0'/>\");
					$('#".$id."_box_recolher').attr('title', 'Expandir');
				}
				else
				{
					$('#".$id."_btn_funcao_box').val(0);
					$('#".$id."_content').fadeIn(200);
					$('#".$id."_box_recolher').html('[-]');
					$('#".$id."_box_recolher').html(\"<img src='".base_url()."img/box_recolhe.png' border='0'/>\");
					$('#".$id."_box_recolher').attr('title', 'Recolher');
				}
			}
		</script>
		
		<div class='box' $extra id='$id'>
			<div id='".$id."_title' class='title'>
				<div style='float:left'>$caption</div>
				".$botao."
			</div>
		<div class='content' id='".$id."_content'>

			$table
		";

		return $output . "\n";
	}
}

if ( ! function_exists('form_end_box'))
{
	/**
	 * Fechamento de box agrupador de campos
	 *
	 * @param string $id Campo opcional que não é usado, serve apenas para facilitar identificação de qual box está sendo fechado
	 * @return unknown
	 */
	function form_end_box($id="",$with_table=TRUE)
	{
		if($with_table) $table="</table>"; else $table="";

		$output = "
			$table

			</div>

		</div>
		";

		return $output . "\n";
	}
}

if ( ! function_exists('form_start_box_filter') )
{
	/**
	 * Inicia uma Caixa de Filtros
	 *
	 * @param string $id
	 * @param string $caption
	 * @return string
	 */
	function form_start_box_filter($id='filter_bar', $caption='Filtros', $visible=TRUE)
	{
		$display = ($visible)?"":"display:none;";
		$output = "
		<form id='".$id."_form' name='".$id."_form' onsubmit='return false;'>
		<div id='$id' class='filter-bar' style='".$display."padding-bottom:10px;'>

		<h3>$caption</h3>

		<table cellpadding='0' cellspacing='0' align='center' border='0'>
		";

		return $output . "\n";
	}
}

if ( ! function_exists('form_end_box_filter'))
{
	/**
	 * Fechamento de box agrupador de campos
	 *
	 * @param string $v Campo opcional que não é usado, serve apenas para facilitar identificação de qual box está sendo fechado
	 * @return unknown
	 */
	function form_end_box_filter($v="")
	{
		$output = "
			</table>
			<br>
			<input type='button' class='btn btn-mini btn-primary' value='Filtrar' onclick='filtrar(this.form);'>
			<input type='button' class='btn btn-mini' value='Limpar' onclick='limparFormularioFiltros()'>				
			<input type='button' class='btn btn-mini' value='Imprimir' onclick='imprimir_fncdef(this.form);'>
			<!--<input type='button'
				class='btn btn-mini' 
				value='Exportar PDF' 
				onclick='exportarpdf_fncdef();' 
				/>-->
			<input type='button' class='btn btn-mini' value='Esconder filtros' onclick='$(\"#filter_bar\").hide();$(\"#exibir_filtro_button\").show();' />
			<script>
				function limparFormularioFiltros()
				{
					$.post('".site_url('/geral/limpaFiltros')."',{},function(data){ location.reload(); });				
				}
			</script>
		</div>
			</form>
		";

		return $output . "\n";
	}
}

if ( ! function_exists('form_default_date'))
{
	function form_default_date($id, $caption="", $value="", $id_row="", $disable=false, $filtro=false)
	{
		if( $id_row=="" )
		{
			$id_row = $id."_row";
		}
		if(is_array($value))
		{
			if(isset($value[$id]))
			{
				$value=$value[$id];
			}
			else
			{
				$value="";
			}
		}
		
		if(trim($value)=='' && $filtro==true)
		{
			// filtros
			$ci = &get_instance();
			$filtro = resgatar_filtro( $id );
			if(  trim( $filtro )!=''  )
			{
				$value=$filtro;
			}
			/////
		}		
		
		$attr = array( "name"=>$id, "id"=>$id, "value"=>$value );

		$output = "
		<tr id='$id_row'>
		<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
		<td class='coluna-padrao-form-objeto'>
			".form_date($id, $value, FALSE, $disable)."
			</td>
		</tr>
		";

		return $output . "\n";
	}
}

if ( ! function_exists('form_default_color'))
{
	function form_default_color($id, $caption="", $value="", $id_row="", $extra="")
	{
		if( $id_row=="" )
		{
			$id_row = $id."_row";
		}
		if(is_array($value))
		{
			if(isset($value[$id]))
			{
				$value=$value[$id];
			}
			else
			{
				$value="";
			}
		}

		$arr_replace = array("[", "]");

		$name = $id;

		$id = str_replace($arr_replace , '_', $name);

		$attr = array( "name"=>$name, "id"=>$id );

	
		$output = "
		<tr id='$id_row'>
			<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
			<td class='coluna-padrao-form-objeto'>
				<table border='0' cellspacing='0' cellpadding='0'>
					<tr>
						<td>
							".form_input($attr, $value, $extra)."
						</td>
						<td valing='middle'>
							<div>
								<div class='colorpicker_colorSelector'>
									<div id='".$id."_colorSelector' style='background-color: #".$value."'></div>
								</div>
							</div>
						</td>
					</tr>
				</table>	
				<script>
					$('#".$id."_colorSelector').click(function() {
						$('#".$id."').val(".$id."_rgb2hex($('#".$id."_colorSelector').css('backgroundColor')))
						$('#".$id."').ColorPickerShow();
					});
						
					$('#".$id."').ColorPicker({
						onSubmit: function(hsb, hex, rgb, el) {
							$(el).val(hex);
							$(el).ColorPickerHide();
							$('#".$id."_colorSelector').css('backgroundColor', '#' + hex);
						},
						onBeforeShow: function () {
							$(this).ColorPickerSetColor(this.value);
						}
					})
					.bind('keyup', function(){
						$(this).ColorPickerSetColor(this.value);
					});
					
					function ".$id."_rgb2hex(rgb) {
						rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
						function hex(x) {
							return parseInt(x).toString(16);
						}
						return hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
					}
				</script>					
			</td>
		</tr>
		";

		return $output . "\n";
	}
}

if (!function_exists('form_default_upload_multiplo'))
{
	function form_default_upload_multiplo($id, $caption="", $up_dir="", $callback_final="", $callback_item="")
	{
		$js = '
				<script type="text/javascript">
					$(function() {
						$("#'.$id.'").pluploadQueue({
							file_data_name : "up_'.$id.'",
							multipart_params : { 
								"up_campo" : "up_'.$id.'",
								"up_dir" : "'.$up_dir.'" 
							},						
							runtimes: "html5,gears,silverlight,flash,html4",
							url: "'.site_url('geral/upload_multiplo').'",
							max_file_size: "100mb",
							unique_names : true,
							sortable: true,		
							flash_swf_url: "'.base_url().'js/plupload/plupload.flash.swf",
							silverlight_xap_url: "'.base_url().'js/plupload/plupload.silverlight.xap",
							filters: [ {title: "Arquivos Permitidos", extensions: "'.implode(",",getExtensaoPermitida()).'"} ],

							preinit: {
								Init: function(up, info) {
									$(".plupload_header").hide();
									$(\'<a href="javascript: void(0);" id="btClearUpload_'.$id.'" class="plupload_button_limpar">Limpar</a>\').click(function(){ $("#'.$id.'").pluploadQueue().splice();  return false; }).appendTo(".plupload_buttons");
								}
							},

							init: {
								StateChanged: function(up) {
									if(up.state == plupload.STARTED){
										$("#btClearUpload_'.$id.'").hide(); 
									}
									else if(up.state == plupload.STOPPED){
										$("#btClearUpload_'.$id.'").show();    
									}
								}	
							},
							init: {
								FileUploaded: function(up, file, info) {
									var obj = JSON.parse(info.response);
								
									if (obj.error)
									{
										if (obj.error.code)
										{
											up.trigger("Error", {
												code : obj.error.code,
												message : obj.error.message,
												details : obj.error.details,
												file : file
											});
											
											alert(obj.error.details);
										}									
									}
									
									'.(trim($callback_item) == "" ? "" : trim($callback_item)."(file, info);").'
								},
								UploadComplete: function(up, files) {
									'.(trim($callback_final) == "" ? "" : trim($callback_final)."(up.total.uploaded, up.total.failed, files);").'
								}								
							}		
						});
					});
				</script>		
		      ';
		
		$output = '
					<tr id="'.$id.'_row">
						<td valign="top" class="coluna-padrao-form">
							<label class="label-padrao-form">'.$caption.'</label>
						</td>
						<td valign="top" class="coluna-padrao-form-objeto">
							<div id="'.$id.'" style="width: 500px;">aguarde...</div>
							'.$js.'
						</td>
					</tr>
				  ';

		return $output;
	}
}


if ( ! function_exists('form_default_time'))
{
	function form_default_time($id, $caption="", $value="", $id_row="")
	{
		if( $id_row=="" )
		{
			$id_row = $id."_row";
		}
		if(is_array($value))
		{
			$value=$value[$id];
		}
		$attr = array( "name"=>$id, "id"=>$id, "value"=>$value );

		$output = "
		<tr id='$id_row'>
		<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
		<td class='coluna-padrao-form-objeto'>
			".form_time($id, $value)."
			</td>
		</tr>
		";

		return $output . "\n";
	}
}

if( ! function_exists('form_default_usuario_ajax') )
{
	/**
	 * Enter description here 
	 *
	 * @param $id Pode ser um array com duas posições, primeiro o ID do objeto
	 * da gerencia, depois com ID do objeto do usuário. Pode ser uma string
	 * apenas com ID do objeto do usuário, nesse caso o nome do objeto para
	 * gerência recebe o nome "$id_gerencia"
	 * @param string 		$gerencia_selecionada	Para carregar o combo das gerencias com uma gerencia já selecionada
	 * @param string 		$usuario_selecionado	Para carregar o combo de usuários com um usuário já selecionado, Se usuario_selecionado preenchido então gerencia_selecionada é ignorada e a gerencia do usuário gravada na base é usada
	 * @param string 		$label_usuario			Rótulo para o objeto de usuários
	 * @param string 		$label_gerencia			Rótulo para o objeto de gerencias
	 *
	 * @return string								Código HTML para criação de objetos
	 */
	function form_default_usuario_ajax($id, $gerencia_selecionada='', $usuario_selecionado='', $label_usuario="Usuário: ", $label_gerencia="Gerência: ")
	{
		$ci = &get_instance();

		if(is_array($id))
		{
			$id_objeto_gerencia = $id[0];
			$id_objeto_usuario = $id[1];
		}
		else
		{
			$id_objeto_usuario = $id;
			$id_objeto_gerencia = $id_objeto_usuario . "_gerencia";
		}

		if(intval($usuario_selecionado)> 0)
		{
			$q = $ci->db->query("select divisao from projetos.usuarios_controledi where codigo=?", array(intval($usuario_selecionado)));
			$r = $q->row_array();
			$gerencia_selecionada = $r['divisao'];
		}

		$js = "
		<script>
			function load_users___$id_objeto_usuario()
			{
				$.post( '".site_url('geral/usuarios_dropdown_ajax/')."', 
				{
					gerencia : $('#$id_objeto_gerencia').val(),
					combo_id : '$id_objeto_usuario'
				}, 
				function(data){ 
					$('#".$id_objeto_usuario."_div').html(data); 
					$('#".$id_objeto_usuario."').val($('#".$id_objeto_usuario."_default_init').val()); 
				} );
			}
		</script>
		";

		$ci->load->model('projetos/Divisoes');
		$ci->load->model('projetos/Usuarios_controledi');
		$divisoes_dropdown = $ci->Divisoes->select_dropdown();
		$usuarios_dropdown = $ci->Usuarios_controledi->select_dropdown_1('GI');

		$output = form_hidden($id_objeto_usuario."_default_init", $usuario_selecionado).form_default_dropdown($id_objeto_gerencia, $label_gerencia, $divisoes_dropdown, array($gerencia_selecionada), "onchange='load_users___$id_objeto_usuario();'");
		if( $gerencia_selecionada=="" )
		{
			$output .= form_default_row($id_objeto_usuario, $label_usuario, "<div id='".$id_objeto_usuario."_div'><select id='$id_objeto_usuario' name='$id_objeto_usuario'><option value=''>Selecione</option></select></div>");
		}
		else
		{
			$output .= form_default_usuario_dropdown( $id_objeto_usuario, $label_usuario, $gerencia_selecionada, $usuario_selecionado );
		}
		return $output . $js . "\n";
	}
}

if(!function_exists('form_default_plano_empresa_ajax'))
{
	function form_default_plano_empresa_ajax($id, $plano_selecionado = '', $empresa_selecionada = '', $label_plano = 'Plano', $label_empresa = 'Empresa', $where = '')
	{
		$ci = &get_instance();

		if(is_array($id))
		{
			$id_objeto_plano = $id[0];
			$id_objeto_empresa = $id[1];
		}
		else
		{
			$id_objeto_plano   = $id;
			$id_objeto_empresa = $id_objeto_plano.'_empresa';
		}

		$js = '
				<script>
					function carregar_empresas___'.$id_objeto_empresa.'()
					{
						$.post("'.site_url('geral/empresas_dropdown_ajax/').'", 
						{
							plano  : $("#'.$id_objeto_plano.'").val(),
							combo_id : "'.$id_objeto_empresa.'"
						}, 
						function(data)
						{ 
							$("#'.$id_objeto_empresa.'_div").html(data); 
							$("#'.$id_objeto_empresa.'").val("'.$empresa_selecionada.'"); 
						} );
					}
				</script>
		      ';

		$qr_sql = "
			SELECT cd_plano AS value,
			       descricao AS text
			  FROM planos
			 WHERE 1 = 1
			   ".(trim($where) != '' ? trim($where) : '').";";

		$plano = $ci->db->query($qr_sql)->result_array();
			
		$output = form_default_dropdown($id_objeto_plano, $label_plano, $plano, array($plano_selecionado), "onchange='carregar_empresas___$id_objeto_empresa();'");

		$options_empresa = array();

		if(trim($plano_selecionado) != "")
		{		
			$qr_sql = "
				SELECT p.cd_empresa AS value,
				       p.sigla AS text
				  FROM patrocinadoras p
				  JOIN planos_patrocinadoras pp
				    ON pp.cd_empresa = p.cd_empresa
				 WHERE pp.cd_plano = ".intval($plano_selecionado)."
				 GROUP BY p.cd_empresa
				 ORDER BY sigla;";

			$empresa = $ci->db->query($qr_sql)->result_array();	

			if(count($empresa) != 1)
			{
				$options_empresa[""] = "Selecione";
			}

			if($empresa !== FALSE )
			{
				foreach($empresa as $item_empresa)
				{
					$options_empresa[$item_empresa["value"]] = $item_empresa["text"];
				}
			}			
		}
		else
		{
			$options_empresa[""] = "Selecione";
		}

		$output .= form_default_row($id_objeto_empresa, $label_empresa, "<div id='".$id_objeto_empresa."_div'>".form_dropdown($id_objeto_empresa, $options_empresa, array($empresa_selecionada), "id='".$id_objeto_empresa."'"));

		return $output.$js."\n";
	}
}

if( ! function_exists('form_default_plano_ajax') )
{
	/**
	 * Enter description here 
	 *
	 * @param $id Pode ser um array com duas posições, primeiro o ID do objeto
	 * da empresa, depois com ID do objeto do plano. Pode ser uma string
	 * apenas com ID do objeto do usuário, nesse caso o nome do objeto para
	 * gerência recebe o nome "$id_gerencia"
	 * @param string 		$empresa_selecionada	Para carregar o combo das empresas com uma empresa já selecionada
	 * @param string 		$plano_selecionado		Para carregar o combo de planoscom um usuário já selecionado
	 * @param string 		$label_plano			Rótulo para o objeto de planos
	 * @param string 		$label_empresa			Rótulo para o objeto de empresas
	 * @param string 		$tipo_empresa			Filtro as empresas pelo campo tipo_cliente (P = Patrocinadora, I = Instituidor)
	 * @param string 		$where					Comando SQL para ser utilizado no WHERE
	 *
	 * @return string								Código HTML para criação de objetos
	 */
	function form_default_plano_ajax($id, $empresa_selecionada='', $plano_selecionado='', $label_plano="Plano", $label_empresa="Empresa", $tipo_empresa="", $where="")
	{
		$ci = &get_instance();

		if(is_array($id))
		{
			$id_objeto_empresa = $id[0];
			$id_objeto_plano = $id[1];
		}
		else
		{
			$id_objeto_plano = $id;
			$id_objeto_empresa = $id_objeto_plano . "_empresa";
		}
		
		$js = "
				<script>
					function carregar_planos___$id_objeto_plano()
					{
						var url = '".site_url('geral/planos_dropdown_ajax/')."';

						$.post( url, 
						{
							empresa  : $('#".$id_objeto_empresa."').val(),
							combo_id : '".$id_objeto_plano."'
						}, 
						function(data)
						{ 
							$('#".$id_objeto_plano."_div').html(data); 
							$('#".$id_objeto_plano."').val('".$plano_selecionado."'); 
						} );
					}
				</script>
		      ";

		$query = $ci->db->query("
									SELECT cd_empresa AS value, 
									       sigla AS text 
								      FROM public.patrocinadoras 
									 WHERE 1 = 1
									 ".(trim($tipo_empresa) != "" ? "AND tipo_cliente = '".trim($tipo_empresa)."'" : "")."
									 ".(trim($where) != "" ? trim($where) : "")."
									 ORDER BY sigla
							    ");
		$empresa_dd = $query->result_array();

		#### CAMPO EMPRESA ###
		$output = form_default_dropdown($id_objeto_empresa, $label_empresa, $empresa_dd, array($empresa_selecionada), "onchange='carregar_planos___$id_objeto_plano();'");
		
		$options_plano = array();
		if(trim($empresa_selecionada) != "")
		{		
			$query = $ci->db->query("
								    SELECT a.cd_plano AS value, 
									       a.descricao AS text 
									  FROM public.planos a 
									  JOIN public.planos_patrocinadoras b 
									    ON a.cd_plano=b.cd_plano 
								     WHERE b.cd_empresa = ".intval($empresa_selecionada)."
									 ORDER BY a.descricao
									");
			$plano_dd = $query->result_array();		
			

			if(count($plano_dd) != 1)
			{
				$options_plano[""] = "Selecione";
			}

			if($plano_dd!==FALSE )
			{
				foreach($plano_dd as $item_plano )
				{
					$options_plano[$item_plano["value"]] = $item_plano["text"];
				}
			}			
		}
		else
		{
			$options_plano[""] = "Selecione";
		}

		#### CAMPO PLANO ###
		$output .= form_default_row($id_objeto_plano, $label_plano, "<div id='".$id_objeto_plano."_div'>".form_dropdown($id_objeto_plano, $options_plano, array($plano_selecionado), "id='".$id_objeto_plano."'"));
		
		return $output . $js . "\n";
	}
}

/**
 * Bem semelhante a form_default_usuario_ajax, no entanto
 * não exige que seja criado dentro de um BOX  padrão  de
 * formulário. pode ser criado em quaisquer circunstancia
 *
 */
function form_usuario_ajax($id, $gerencia_selecionada='', $usuario_selecionado='', $label_usuario="Usuário", $label_gerencia="Gerência")
{
	$ci = &get_instance();

	if(is_array($id))
	{
		$id_objeto_gerencia = $id[0];
		$id_objeto_usuario = $id[1];
	}
	else
	{
		$id_objeto_usuario = $id;
		$id_objeto_gerencia = $id_objeto_usuario . "_gerencia";
	}

	if(trim($usuario_selecionado)!="")
	{
		$q = $ci->db->query("select divisao from projetos.usuarios_controledi where codigo=?", array(intval($usuario_selecionado)));
		$r = $q->row_array();
		$gerencia_selecionada = $r['divisao'];
	}

	$js = "
	<script>
		function load_users___$id_objeto_usuario()
		{
			var url = '".site_url('geral/usuarios_dropdown_ajax/')."/'+$('#$id_objeto_gerencia').val()+'/$id_objeto_usuario';

			$.post( url, {}, function(data){ $('#".$id_objeto_usuario."_div').html(data); } );
		}
	</script>
	";

	$ci->load->model('projetos/Divisoes');
	$divisoes_dropdown = $ci->Divisoes->select_dropdown();

	// $output = form_default_dropdown($id_objeto_gerencia, $label_gerencia, $divisoes_dropdown, array($gerencia_selecionada), "onchange='load_users___$id_objeto_usuario();'");
	$options[''] = 'Selecione';
	foreach( $divisoes_dropdown as $item )
	{
		$options[$item["value"]] = $item["text"];
	}
	$output = form_dropdown($id_objeto_gerencia, $options, array($gerencia_selecionada), "id='$id_objeto_gerencia' onchange='load_users___$id_objeto_usuario();'");

	if( $gerencia_selecionada=="" )
	{
		$output .= "<span id='".$id_objeto_usuario."_div'><select id='$id_objeto_usuario' name='$id_objeto_usuario'><option value=''>Selecione</option></select></span>";
	}
	else
	{
		// $output .= form_default_usuario_dropdown( $id_objeto_usuario, $label_usuario, $gerencia_selecionada, $usuario_selecionado );

		$ci->load->model('projetos/Usuarios_controledi');
		$collection = $ci->Usuarios_controledi->select_dropdown_1($gerencia_selecionada, array("cd_usuario" => $usuario_selecionado));

		$options = array();
		$options[""] = "Selecione";
		if( $collection!==FALSE )
		{
			foreach( $collection as $item )
			{
				$options[$item["value"]] = $item["text"];
			}
		}
		$output .= "<span id='".$id_objeto_usuario."_div'>".form_dropdown($id_objeto_usuario, $options, array($usuario_selecionado), "id='$id_objeto_usuario'")."</div>";
	}
	return $output . $js . "\n";
}

if( ! function_exists('form_default_usuario_dropdown') )
{
	/**
	 * Cria um conjunto aninhado de <SELECT>. Composto por 2 <SELECT>, um para gerência, o outro para usuários. O Combo de Gerencia filtra o Combo de Usuário com ajax
	 *
	 * @param string $id
	 * @param string $caption
	 * @param string $gerencia_selecionada
	 * @param string $usuario_selecionado
	 * @return string
	 */
	function form_default_usuario_dropdown($id, $caption, $gerencia_selecionada='', $usuario_selecionado='')
	{
		$ci = &get_instance();
		$ci->load->model('projetos/Usuarios_controledi');
		$usuarios_dropdown = $ci->Usuarios_controledi->select_dropdown_1($gerencia_selecionada, array("cd_usuario" => $usuario_selecionado));

		$output = form_default_dropdown($id, $caption, $usuarios_dropdown, array($usuario_selecionado));

		return $output."\n";
	}
}

if(!function_exists('form_default_diretoria'))
{
	function form_default_diretoria($id, $caption="Diretoria: ", $diretoria_selecionada="", $onchange="")
	{
		$ci = &get_instance();

		$qr_sql = "
					SELECT cd_diretoria AS value, 
						   ds_diretoria AS text
					  FROM projetos.diretoria
					 WHERE dt_exclusao IS NULL
					 ORDER BY ds_diretoria
				  ";
		$ob_resul = $ci->db->query($qr_sql);
		$ar_reg = $ob_resul->result_array();
		
		$onchange = (trim($onchange) != "" ? 'onchange="'.trim($onchange).'"' : "");

		$output = form_default_dropdown($id, $caption, $ar_reg, array($diretoria_selecionada), $onchange);
		
		return $output;
	}
}
function filter_diretoria($id, $caption="Diretoria: ", $diretoria_selecionada="", $onchange="")
{
	return form_default_diretoria($id, $caption, $diretoria_selecionada, $onchange="");
}


function get_session_id()
{
	if(!session_id())
	{ 
		session_start(); 
	}

	return session_id();
}

function manter_filtros( $args )
{
	$ci = &get_instance();
	#$ds_controller = get_class($ci);
	$id_sessao = get_session_id();
	$ds_controller = $ci->uri->segment(1); // n=1 for controller, n=2 for method, etc
	$ds_controller.= ((trim($ci->uri->segment(2)) != "") ? "-".trim($ci->uri->segment(2)) : "");	

	#echo "<PRE>".print_r($ci,true)."</PRE>";exit;

	//echo '<div style="display:none">SESSION_ID: '.get_session_id(). '</div>';
	
	$qr_sql = "
				DELETE 
				  FROM public.ci_filtros 
				 WHERE ds_session_id <> '".$id_sessao."'
				   AND ds_ip_usuario = '".$_SERVER['REMOTE_ADDR']."'
	          ";
	$ci->db->query($qr_sql);
	
	
	foreach($args as $key=>$value)
	{
		#### VERIFICAR PARA MANTER DADOS EM ARRAY ####
		if(is_array($value))
		{
			$value = implode($value,"|");
		}
		
		if(!is_array($value))
		{
			$value = utf8_decode($value);

			$qr_sql = "
						SELECT cd_ci_filtros
						  FROM public.ci_filtros 
						 WHERE ds_ip_usuario = '".$_SERVER['REMOTE_ADDR']."'
						   AND ds_controller = '".$ds_controller."'
						   AND ds_nome       = '".$key."'
			          ";
			$ob_res = $ci->db->query($qr_sql);
			
			if($ob_res->num_rows() > 0)
			{
				$ar_reg = $ob_res->row_array();
				$qr_sql = "
							UPDATE public.ci_filtros 
							   SET ds_valor       = '".$value."',
							       dt_atualizacao = CURRENT_TIMESTAMP
							 WHERE cd_ci_filtros = ".$ar_reg["cd_ci_filtros"];
				$ci->db->query($qr_sql);
			}
			else
			{
				$qr_sql = "
							INSERT INTO public.ci_filtros
							     ( 
								   ds_session_id, 
								   ds_ip_usuario, 
								   ds_controller,
								   ds_nome, 
								   ds_valor, 
								   cd_usuario 
								 )
					        VALUES 
							     ( 
									'".$id_sessao."' , 
									'".$_SERVER['REMOTE_ADDR']."', 
									'".$ds_controller."', 
									'".$key."', 
									'".$value."', 
									".usuario_id()." 
								 )
				          ";
				$ci->db->query($qr_sql);
			}
		}
	}
}

function resgatar_filtro($ds_nome)
{
	$ci = &get_instance();
	$id_sessao = get_session_id();	
	#$ds_controller = get_class($ci);
	
	$ds_controller = $ci->uri->segment(1); // n=1 for controller, n=2 for method, etc
	$ds_controller.= ((trim($ci->uri->segment(2)) != "") ? "-".trim($ci->uri->segment(2)) : "");	
	
	$qr_sql = "
				SELECT ds_valor 
				  FROM ci_filtros 
				 WHERE ds_ip_usuario = '".$_SERVER['REMOTE_ADDR']."'
				   AND ds_controller = '".$ds_controller."'
				   AND ds_nome       = '".$ds_nome."'
			  ";
	$ob_res = $ci->db->query($qr_sql);
	
	if($ob_res->num_rows() > 0)
	{
		$ar_reg = $ob_res->row_array();
		return $ar_reg['ds_valor'];
	}
	else
	{
		return "";
	}
}

if ( ! function_exists('form_default_checkbox_group'))
{
	/**
	 * Renderiza uma LISTA de checkbox usando a $collection que representa um array
	 *
	 * @param unknown_type $id
	 * @param unknown_type $caption
	 * @param array $checked				Deve ter 2 colunas, uma chamada TEXT e a outra VALUE respectivamente com os valores de text e value do objeto <OPTION>
	 * @param unknown_type $selected
	 * @param unknown_type $extra
	 * @param boolean $filtro				TRUE indica que o campo esta sendo criado para filtro e o valor pode ser recuperado do banco de dados (recurso para manter filtros)
	 * @return unknown
	 */
	function form_default_checkbox_group($id, $caption="", $collection=array(), $checked=array(), $height = 100 , $width = 500, $extra="", $fl_filtro=FALSE)
	{
		if((count($checked) == 0) and  ($fl_filtro==TRUE))
		{
			$ci = &get_instance();
			$filtro = resgatar_filtro( $id );
			if(trim($filtro) != '')
			{
				$checked = explode("|", $filtro);
			}
		}
		
		$options = array();

		if($collection !== FALSE)
		{
			foreach($collection as $item)
			{
				$fl_check = false;
				if(in_array($item["value"], $checked))
				{
					$fl_check = true;
				}
				
				$options[] = "<tr>
								<td>".form_checkbox($id."[]", $item["value"], $fl_check, "id='".$id."' ".$extra)."</td>
								<td><label class='label-padrao-form'>".$item["text"]."</label></td>
							  </tr>";
			}
		}		
		$output = "
		<tr id='".$id."_row'>
		<td class='coluna-padrao-form' valign='top'><label class='label-padrao-form'>".$caption."</label></td>
		<td class='coluna-padrao-form-objeto' height='25'>
			".($fl_filtro ? form_checkbox($id."_checkall", "", TRUE, "id='".$id."_checkall' onclick='checkall_".$id."()'")." Marcar/Desmarcar Todos" : "")."
			<script>
				function checkall_".str_replace("[","",str_replace("]","",$id))."()
				{
					var ipts = $('#".$id."_row').find('input:checkbox');
					var check = document.getElementById('".$id."_checkall');
					check.checked ?
						jQuery.each(ipts, function(){
						this.checked = true;
					}) :
						jQuery.each(ipts, function(){
						this.checked = false;
					});				
				}
			</script>
			<div style='height:".$height."px; width:".$width."px; overflow: auto; border: 1px solid gray; padding: 4px;'>
				<table border='0' cellspaccing='0' cellpadding='0' id='".$id."_table'>".implode(" ",$options)."</table>
			</div>
		</tr>
		";

		return $output;
	}
}




if ( ! function_exists('form_default_dropdown'))
{
	/**
	 * Renderiza um DROPDOWN usando a $collection que representa um array para formação do OPTION
	 *
	 * @param unknown_type $id
	 * @param unknown_type $caption
	 * @param array $collection				Deve ter 2 colunas, uma chamada TEXT e a outra VALUE respectivamente com os valores de text e value do objeto <OPTION>
	 * @param unknown_type $selected
	 * @param unknown_type $extra
	 * @param boolean $filtro				TRUE indica que o campo esta sendo criado para filtro e o valor pode ser recuperado do banco de dados (recurso para manter filtros)
	 * @return unknown
	 */
	function form_default_dropdown($id, $caption="", $collection=array(), $selected=array(), $extra="", $filtro=FALSE)
	{
		if(sizeof($selected)==0 && $filtro==TRUE)
		{
			// filtros
			$ci = &get_instance();
			$filtro = resgatar_filtro( $id );
			if(trim($filtro) != '')
			{
				$selected=explode("|", $filtro);
			}
			/////
		}

		$options = array();

		$options[""] = "Selecione";

		if( $collection!==FALSE )
		{
			foreach( $collection as $item )
			{
				$options[$item["value"]] = $item["text"];
			}
		}
		
		$id_row = $id."_row";
		
		$output = "
		<tr id='$id_row'>
		<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
		<td class='coluna-padrao-form-objeto' height='25'>
			<div id='".$id."_div'>" . form_dropdown($id, $options, $selected, "id='$id' ".$extra) . "</div>
			</td>
		</tr>
		";

		return $output . "\n";
	}
}

if( ! function_exists('form_default_dropdown_db') )
{
	/**
	 * Drop-down com fonte do banco de dados
	 * 
	 * Renderiza um dropdown realizando uma consulta no banco de dados com base em informações passadas por parametro, 
	 * o objeto é renderizado em linha padrão de formulário com label e campo!
	 *
	 * @param int 		$id							Atributos NAME e ID do <SELECT>
	 * @param string 	$caption
	 * @param array 	$db_conf					Array com as configuração de consulta  $config[0]='esquema.tabela'; $config[1]='coluna_pk'; $config[2]='coluna_descricao';
	 * @param array 	$selected					Array com os valores que deve ser selecionados ao carregar o SELECT
	 * @param string 	$extra
	 * @param string 	$id_row						Atributo ID da <TR> ou BOX onde o objeto está inserido
	 * @param boolean 	$integrar_cadastro_simples	True Cria um Link para carregar um formulário que permite incluir um novo registro na tabela usada para carregar o objeto <SELECT>, o formulário será carregado dentro de um IFRAME
	 * @param string 	$where						Filtros para concatenar a query, por padrão "dt_exclusao IS NULL".
	 * @return string
	 */
	function form_default_dropdown_db(
		$id
		, $caption=""
		, $db_conf=array()
		, $selected=array()
		, $extra=""
		, $id_row=""
		, $integrar_cadastro_simples=array(FALSE,TRUE)
		, $where=' dropdown_db.dt_exclusao IS NULL '
		, $orderby=''
		, $label_botao= "Novo"
	)
	{
		$fl_tabela = TRUE;
		if(is_array($integrar_cadastro_simples))
		{
			$fl_tabela = $integrar_cadastro_simples[1];
			$integrar_cadastro_simples = $integrar_cadastro_simples[0];
		}
		
		$collection = array();

		$orderby = (trim($orderby) == "" ? $db_conf[2] : $orderby);

		// acessar banco de dados
		$ci = &get_instance();
		$q = $ci->db->query("
							SELECT dropdown_db.".$db_conf[1]." as value, 
							       dropdown_db.".$db_conf[2]." as text 
			                  FROM ".$db_conf[0]." dropdown_db
							 WHERE ".(trim($where) == "" ? " 1 = 1 " : trim($where))."
			                 ORDER BY ".$orderby.";
							");
		if($q)
		{
			$collection = $q->result_array();
		}

		// montar objeto <SELECT>
		$options = array();

		$options[""] = "Selecione";

		if( $collection!==FALSE )
		{
			foreach( $collection as $item )
			{
				$options[$item["value"]] = $item["text"];
			}
		}

		$js = '';
		$botao_cadastro_simples = '';
		if($integrar_cadastro_simples)
		{
			$js = "
			<script>
			
			function abrir_novo_".$id."()
			{
				$('#novo_registro_janela_".$id."').dialog({
					width: 300,
					height: 170,
					modal: true,
					draggable: false,
					resizable: false,
					
					buttons: {
						'Incluir': function() {
							if(jQuery.trim($('#reg_descricao_".$id."').val()) != '')
							{
								$.post('".site_url('geral/cadastro_simples_salvar')."',
								{
									table      : $('#reg_table_".$id."').val(),
									field_text : $('#reg_field_text_".$id."').val(),
									field_pk   : $('#reg_field_pk_".$id."').val(),
									callback   : $('#reg_callback_".$id."').val(),
									descricao  : $('#reg_descricao_".$id."').val()
								},
								function(data)
								{
									$('#novo_registro_retorno_".$id."').html(data);
									$(this).dialog('close');
								});
							}
							else
							{
								alert('Informe o campo');
								$('#reg_descricao_".$id."').focus();
							}
						},
						'Fechar': function() {
							$(this).dialog('close');
						}
					},
					
					open: function(event, ui) {
						var scrollPosition = [self.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft,
											  self.pageYOffset || document.documentElement.scrollTop  || document.body.scrollTop];
						var html = jQuery('html');
							html.data('scroll-position', scrollPosition);
							html.data('previous-overflow', html.css('overflow'));
							html.css('overflow', 'hidden');
						window.scrollTo(scrollPosition[0], scrollPosition[1]);	

						$('#novo_registro_janela_".$id."').dialog('option', 'position', 'center');
					},	

					close: function(event, ui) {
						var html = jQuery('html');
						var scrollPosition = html.data('scroll-position');
						html.css('overflow', html.data('previous-overflow'));
						window.scrollTo(scrollPosition[0], scrollPosition[1])				
					}			
				});					
			}
			
			function callback_".$id."(id_inserido)
			{
				$.post('".site_url('geral/carregar_dropdown')."', 
					{
						nome        : '".$id."',
						tabela      : '".$db_conf[0]."',
						campo_valor : '".$db_conf[1]."',
						campo_texto : '".$db_conf[2]."',
						selecionado : id_inserido,
						extra       : $('#form_default_dropdown_db_".$id."_extra').val(),
						where       : $('#form_default_dropdown_db_".$id."_where').val(),
						orderby     : $('#form_default_dropdown_db_".$id."_orderby').val()
					} 
					, 
					function(data)
					{
						$('#div_".$id."').html(data);
						$('#novo_registro_janela_".$id."').dialog('close');
					}
				);
			}
			</script>
			<input type='hidden' id='form_default_dropdown_db_".$id."_extra' value='".$extra."'>
			<input type='hidden' id='form_default_dropdown_db_".$id."_where' value='".$where."'>
			<input type='hidden' id='form_default_dropdown_db_".$id."_orderby' value='".$orderby."'>
			";
			
			$form_janela = "";
			$form_janela.=		form_input(array('type' => 'hidden', 'name' => 'reg_table_'.$id,      'id' => 'reg_table_'.$id,      'value' => $db_conf[0]));
			$form_janela.=		form_input(array('type' => 'hidden', 'name' => 'reg_field_pk_'.$id,   'id' => 'reg_field_pk_'.$id,   'value' => $db_conf[1]));
			$form_janela.=		form_input(array('type' => 'hidden', 'name' => 'reg_field_text_'.$id, 'id' => 'reg_field_text_'.$id, 'value' => $db_conf[2]));
			$form_janela.=		form_input(array('type' => 'hidden', 'name' => 'reg_callback_'.$id,   'id' => 'reg_callback_'.$id,   'value' => "callback_".$id));
			$form_janela.=		form_label($caption, 'reg_descricao_'.$id, array('style' => 'display:block;'));
			$form_janela.=		form_input(array('name' => 'reg_descricao_'.$id,  'id' => 'reg_descricao_'.$id,  'value' => "",'class' => "text ui-widget-content ui-corner-all",'style' => 'display:block; width:90%; padding: .4em;'));
			
			$botao_cadastro_simples = "
				<div id='novo_registro_janela_".$id."' title='Cadastro' style='display:none;'>
					".$form_janela."
				</div>
				<input id='novo_registro_".$id."' type='button' class='btn btn-mini' value='".$label_botao."' onclick='abrir_novo_".$id."();' />
				<div id='novo_registro_retorno_".$id."'></div>
			";
		}

		
		if($fl_tabela)
		{
			$output = "
						<tr id='".$id_row."'>
							<td class='coluna-padrao-form'><label class='label-padrao-form' for='".$id."'>".$caption."</label></td>
							<td class='coluna-padrao-form-objeto'>
								<div style='float:left' id='div_".$id."'>
									".form_dropdown($id, $options, $selected, $extra)."
								</div>
								".$botao_cadastro_simples."
							</td>
						</tr>
					  ";
		}
		else
		{
			$output = "
						<div id='div_".$id."'>
							".form_dropdown($id, $options, $selected, $extra)."
						</div>
						".$botao_cadastro_simples."
					  ";		
		}

		return $js.$output . "";
	}
}

if ( ! function_exists('form_default_tipo_documento') )
{
	function form_default_tipo_documento($config=array())
	{
		$id              = (!isset($config['id_codigo']) ? 'cd_tipo_doc' : $config['id_codigo']);
		$idNome          = (!isset($config['id_nome']) ? 'nome_documento' : $config['id_nome']);
		$value           = (!isset($config['value']) ? '' : $config['value']);
		$caption         = (!isset($config['caption']) ? 'Documento: ' : $config['caption']);
		$callback_buscar = (!isset($config['callback_buscar']) ? '' : $config['callback_buscar']);
		$formulario      = (!isset($config['formulario']) ? TRUE : $config['formulario']);
		
		$script = "
<script>
	function consultar_tipo_documentos_focus__$id()
	{
		if( $('#$id').val()!='' )
		{
			consultar_tipo_documentos__$id()
		}

	}
	
	function consultar_tipo_documentos__$id()
	{
		if( $('#$id').val()!='' )
		{
			$.post( '".base_url().index_page()."/ajax/tipo_documentos/nome', { cd_tipo_doc:$('#$id').val() },
			function(data) {
				$('#$idNome').val( data );

				$callback_buscar

			});
		}
		else
		{
			$('#$idNome').val('');

			if($('#$id').val()=='' || $('#$id').val()=='0'){consultar_tipo_documentos_por_nome_$id();}
		}
	}

	function consultar_tipo_documentos_por_nome_".$id."_callback(e,r,s)
	{
		$('#$id').val(e);

		consultar_tipo_documentos__$id();

		close_$id();
	}

	function consultar_tipo_documentos_por_nome_$id()
	{
		$.post('".site_url("ajax/tipo_documentos/form_busca_por_nome")."', 
		{
			jscallback : 'consultar_tipo_documentos_por_nome_".$id."_callback',
			close      : 'close_$id()'
		}, 
		function(data)
		{ 
			$('#windowPadraoConteudo').html(data);
			$('#windowPadrao').attr('title','Buscar');
			windowPadraoShow();			
		});
	}

	function close_$id()
	{
		$('#windowPadrao').dialog('close');
		$('#".$id."').focus();
	}

	jQuery(function($){
		$('#".$id."').numeric('');
	});
</script>
";

	$span="<input id='$idNome' style='width:300px;' readonly='true'>";

	$campo_doc =  form_input(array( "value"=>$value, "name"=>$id, "id"=>$id, "title"=>"Código do tipo de documento","style"=>"width:50px;", "onkeypress"=>"handleEnter(this, event);", "maxlength"=>"4"), "", "") ."
			      ". form_input(array( "type"=>"button", "onclick"=>"consultar_tipo_documentos__$id();", "onfocus"=>"consultar_tipo_documentos_focus__$id();", "class"=>"btn btn-mini buscar_tipo_documento","style" => "height: 20px; margin-bottom: 5px;"), "Buscar", "");
	if($formulario)
	{
		$output = "
					<tr>
						<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
						<td class='coluna-padrao-form-objeto' valign='top'>
							$campo_doc			
							$span
							$script
						</td>
					</tr>
				  ";	
	}
	else
	{
		$output = "
					$campo_doc			
					$span
					$script
				  ";	
	}

	$div_busca_por_nome = '
	<div id="tipo_documentos_busca_por_nome_div__'.$id.'" style="position:absolute;top:1%;left:1%;width:680px; padding: 8px; border: 1px solid #006633; background: #FFFFFF; display:none;"></div>';

	return $output . $div_busca_por_nome . "\n";
	}
}

if ( ! function_exists('form_default_tipo_documento_juridico') )
{
	function form_default_tipo_documento_juridico($config=array())
	{
		if( !isset($config['id_codigo']) ){ $config['id_codigo']='cd_tipo_doc'; }
		if( !isset($config['id_nome']) ){ $config['id_nome']='nome_documento'; }
		if( !isset($config['value']) ){ $config['value']=''; }
		if( !isset($config['caption']) ){ $config['caption']='Documento'; }
		$formulario  = (!isset($config['formulario']) ? TRUE : $config['formulario']);
		
		$id=$config['id_codigo'];
		$idNome=$config['id_nome'];
		$value=$config['value'];
		$caption=$config['caption'];

		$script = "
<script>
	function consultar_tipo_documentos_juridico_focus__$id()
	{
		if( $('#$id').val()!='' )
		{
			consultar_tipo_documentos_juridico__$id()
		}

	}

	function consultar_tipo_documentos_juridico__$id()
	{
		if( $('#$id').val()!='' )
		{
			$.post( '".base_url().index_page()."/ajax/tipo_documentos_juridico/nome', { cd_tipo_doc:$('#$id').val() },
			function(data) {
				$('#$idNome').val( data );
			});
		}
		else
		{
			$('#$idNome').val('');

			if($('#$id').val()=='' || $('#$id').val()=='0'){consultar_tipo_documentos_juridico_por_nome_$id();}
		}
	}

	function consultar_tipo_documentos_juridico_por_nome_".$id."_callback(e,r,s)
	{
		$('#$id').val(e);

		consultar_tipo_documentos_juridico__$id();

		close_$id();
	}

	function consultar_tipo_documentos_juridico_por_nome_$id()
	{
		$.post('".site_url("ajax/tipo_documentos_juridico/form_busca_por_nome")."', 
		{
			jscallback : 'consultar_tipo_documentos_juridico_por_nome_".$id."_callback',
			close      : 'close_$id()'
		}, 
		function(data)
		{ 
			$('#windowPadraoConteudo').html(data);
			$('#windowPadrao').attr('title','Buscar');
			windowPadraoShow();			
		});		
	}

	function close_$id()
	{
		$('#windowPadrao').dialog('close');
		$('#".$id."').focus();
	}

	jQuery(function($){
		$('#".$id."').numeric('');
	});
</script>
";

	$span="<input id='$idNome' style='width:300px;' readonly='true'>";
	
	$campo_doc =  form_input(array( "value"=>$value, "name"=>$id, "id"=>$id, "title"=>"Código do tipo de documento","style"=>"width:50px;", "onkeypress"=>"handleEnter(this, event);", "maxlength"=>"4"), "", "") ."
			      ". form_input(array( "type"=>"button", "onclick"=>"consultar_tipo_documentos_juridico__$id();", "onfocus"=>"consultar_tipo_documentos_juridico_focus__$id();", "class"=>"btn btn-mini buscar_tipo_documento_juridico","style" => "height: 20px; margin-bottom: 5px;"), "Buscar", "");

	if($formulario)
	{
		$output = "
					<tr>
						<td class='coluna-padrao-form'><label class='label-padrao-form' for='$id'>$caption</label></td>
						<td class='coluna-padrao-form-objeto' valign='top'>
							$campo_doc			
							$span
							$script
						</td>
					</tr>
				  ";	
	}
	else
	{
		$output = "
					$campo_doc			
					$span
					$script
				  ";	
	}

	$div_busca_por_nome = '
	<div id="tipo_documentos_juridico_busca_por_nome_div__'.$id.'" style="position:absolute;top:1%;left:1%;width:680px; padding: 8px; border: 1px solid #006633; background: #FFFFFF; display:none;"></div>';

	return $output . $div_busca_por_nome . "\n";
	}
}

if ( ! function_exists('form_default_participante') )
{
	/**
	 * Cria conjunto de objetos para seleção de participante pela PK EMP/RE/SEQ
	 *
	 * @param array 	$ids					Definição dos nomes dos objetos
	 * inputs respectivamente de EMP, RE, SEQ, NOME (INPUT q recebe o nome)
	 * @param string 	$caption	
	 * @param array 	$values					Definição dos valores dos objetos inputs, o array deve ter uma KEY com os mesmos valores usados no parametro $ids, exceto o NOME
	 * @param array 	$exibir_botao_buscar	boolean
	 * @return unknown
	 */
	function form_default_participante( 
		$ids=array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante')
		, $caption='Participante:'
		, $values=false
		, $nome_integrado=false
		, $exibir_botao_buscar=true
		, $callback_apos_consultar=''
		, $formulario=true
	)
	{
		$script = "
<script>
	function consultar_participante_focus__".$ids[0]."()
	{
		var emp = $('#".$ids[0]."').val();
		var re  = $('#".$ids[1]."').val();
		var seq = $('#".$ids[2]."').val();
		
		if( emp!='' && re!='' && seq !='' && re!='0' )
		{		
			consultar_participante__".$ids[0]."();
		}
	}

	function consultar_participante__".$ids[0]."()
	{
		var emp = $('#".$ids[0]."').val();
		var re  = $('#".$ids[1]."').val();
		var seq = $('#".$ids[2]."').val();

		if( emp!='' && re!='' && seq !='' && re!='0' )
		{
			$.post(  '".base_url().index_page()."/ajax/participante/nome', { emp:emp, re:re, seq:seq },
			function(data){
				$('#".$ids[3]."').val( data );

				".$callback_apos_consultar."

			}
		);
		}
		else
		{
			$('#".$ids[3]."').val('');
			
			if(re=='' || re=='0'){consultar_participante_por_nome_".$ids[0]."();}
		}
	}

	function consultar_participante_por_nome_".$ids[0]."_callback(e,r,s)
	{
		$('#".$ids[0]."').val(e);
		$('#".$ids[1]."').val(r);
		$('#".$ids[2]."').val(s);

		consultar_participante__".$ids[0]."();

		close_".$ids[0]."();
	}

	function consultar_participante_por_nome_".$ids[0]."()
	{
		$.post('".site_url("ajax/participante/form_busca_por_nome")."', 
		{
			jscallback : 'consultar_participante_por_nome_".$ids[0]."_callback',
			close      : 'close_".$ids[0]."()'
		}, 
		function(data)
		{ 
			$('#windowPadraoConteudo').html(data);
			$('#windowPadrao').attr('title','Buscar');
			windowPadraoShow();			
		});		
	}

	function close_".$ids[0]."()
	{
		$('#windowPadrao').dialog('close');
		$('#".$ids[0]."').focus();
	}

	jQuery(function($){
		$('#".$ids[0]."').numeric('');
		$('#".$ids[1]."').numeric('');
		$('#".$ids[2]."').numeric('');
	});
</script>
";

	if(!$values)
	{
		$values[$ids[0]]='';
		$values[$ids[1]]='';
		$values[$ids[2]]='';
	}

	$span='';
	if(!$nome_integrado)
	{
		$span="<input id='".$ids[3]."' name='".$ids[3]."' style='width:300px;' readonly='true'>";
	}

	$campo_part =  form_input(array( "value"=>$values[$ids[0]], "name"=>$ids[0], "id"=>$ids[0], "title"=>"Código da empresa","style"=>"width:50px; ", "onkeypress"=>"handleEnter(this, event);", "maxlength"=>"3"), "", "") ."
							". form_input(array( "value"=>$values[$ids[1]], "name"=>$ids[1], "id"=>$ids[1], "title"=>"Registro do empregado","style"=>"width:100px; ", "onkeypress"=>"handleEnter(this, event);", "maxlength"=>"10"), "", "") ."
							". form_input(array( "value"=>$values[$ids[2]], "name"=>$ids[2], "id"=>$ids[2], "title"=>"Sequência","style"=>"width:50px; ", "onkeypress"=>"handleEnter(this, event);", "maxlength"=>"2"), "", "") ."
							". (($exibir_botao_buscar)?form_input(array( "type"=>"button", "onclick"=>"consultar_participante__$ids[0]();", "onfocus"=>"consultar_participante_focus__$ids[0]();", "class"=>"btn btn-mini buscar_participante", "style" => "height: 20px; margin-bottom: 5px;"), "Buscar", ""):"");
	if($formulario)
	{
		$output = "
					<tr>
						<td class='coluna-padrao-form'><label class='label-padrao-form' for='".$ids[0]."'>".$caption."</label></td>
						<td class='coluna-padrao-form-objeto' valign='top'>
							".$campo_part."
							".$span."
							".$script."
						</td>
					</tr>
				  ";	
	}
	else
	{
		$output = "
					".$campo_part."
					".$span."
					".$script."
				  ";	
	}

	
	$div_busca_por_nome = '
	<div id="participante_busca_por_nome_div__'.$ids[0].'" style="position:absolute;top:1%;left:1%;width:680px; padding: 8px; border: 1px solid #006633; background: #FFFFFF; display:none;"></div>';

	return $output . $div_busca_por_nome . "\n";
	}
}

/**
 * form_default_participante_trigger
 * 
 * Busca participante pela PK (emp/re/seq) e devolve string no formato JSON para função definida pelo programador
 *
 * @param	array	$config					parametros para configuração de busca e retorno
 *					  $config['emp']['id']			ID do objeto HTML para código da empresa
 *					  $config['emp']['value']		VALUE do objeto HTML para código da empresa
 *					  $config['re']['id']
 *					  $config['re']['value']
 *					  $config['seq']['id']			sequencia
 *					  $config['seq']['value']		sequencia
 *					  $config['caption']			Rótulo do campo
 *					  $config['callback']			nome da função javascript para retorno, essa função receberá um parametro JSON
 *
 * @return
 */
function form_default_participante_trigger( $config )
{
	if(!isset($config['emp']['id'])){ $config['emp']['id']='cd_empresa'; }
	if(!isset($config['re']['id'])){ $config['re']['id']='cd_registro_empregado'; }
	if(!isset($config['seq']['id'])){ $config['seq']['id']='seq_dependencia'; }

	if(!isset($config['emp']['value'])){ $config['emp']['value']=''; }
	if(!isset($config['re']['value'])){ $config['re']['value']=''; }
	if(!isset($config['seq']['value'])){ $config['seq']['value']=''; }

	if(!isset($config['caption'])){ $config['caption']='Participante:'; }
	if(!isset($config['callback'])){ echo 'Informe a função para callback: $config["callback"]'; return false; }
	if(!isset($config['row_id'])){ $config['row_id']=''; }

	$emp = $config['emp']['id'];
	$re = $config['re']['id'];
	$seq = $config['seq']['id'];
	$callback = $config['callback'];

	$script = "
		<script type='text/javascript'>
			function consultar_participante_focus__$emp()
			{
				emp = $('#$emp').val();
				re  = $('#$re').val();
				seq = $('#$seq').val();
				
				if( emp!='' && re!='' && seq !='' && re!='0' )
				{		
					consultar_participante__$emp();
				}
			}		
		
			function consultar_participante__$emp()
			{
				emp = $('#$emp').val();
				re  = $('#$re').val();
				seq = $('#$seq').val();

				if( emp!='' && re!='' && seq !='' )
				{
					$.post(  '".base_url().index_page()."/ajax/participante/json_object', { emp:emp, re:re, seq:seq }, $callback, 'json' );
				}
			}

			jQuery(function($){
				$('#".$emp."').numeric('');
				$('#".$re."').numeric('');
				$('#".$seq."').numeric('');
			});
		</script>
	";

	$output = "
	<tr id='".$config['row_id']."'>
	<td class='coluna-padrao-form'><label class='label-padrao-form' for='$emp'>".$config['caption']."</label></td>
	<td class='coluna-padrao-form-objeto' valign='top'>
		" . form_input(array( "value"=>$config['emp']['value'], "name"=>$emp, "id"=>$emp, "title"=>"Código da empresa","style"=>"width:50px;", "onkeypress"=>"handleEnter(this, event);", "maxlength"=>"3"), "", "") . "
		" . form_input(array( "value"=>$config['re']['value'], "name"=>$re, "id"=>$re, "title"=>"Registro do empregado","style"=>"width:100px;", "onkeypress"=>"handleEnter(this, event);", "maxlength"=>"10"), "", "") . "
		" . form_input(array( "value"=>$config['seq']['value'], "name"=>$seq, "id"=>$seq, "title"=>"Sequência","style"=>"width:50px;", "onkeypress"=>"handleEnter(this, event);", "maxlength"=>"2"), "", "") . "
		" . form_input(array( "type"=>"button", "onclick"=>"consultar_participante__$emp();", "onfocus"=>"consultar_participante_focus__$emp();", "class"=>"btn btn-mini buscar_participante", "style" => "height: 20px; margin-bottom: 2px;"), "Buscar", "") . "
	$script
	</td>
	</tr>
	";

	return $output . "\n";
}

if ( ! function_exists('form_default_lista_simples') ) 
{
	/**
	 * Cria uma lista com apenas uma coluna de informação e outra coluna com um botão de excluir
	 *
	 * @param string $info			Label da lista
	 * @param string $collection	Uma coleção de arrays, cada array deve ter a seguinte configuração: array( $field_pk_id=>'999', 'label'=>'xxxxxxxxxxx' )
	 * @param string $uri			"controller/método" q será utilizado para exclusão, esse método deve estar no padrão recebendo a PK por GET criptografada com MD5
	 * @param string $field_pk_id	Nome da coluna do banco de dados que é a chave primária da tabela
	 * @return string
	 */
	function form_default_lista_simples( $info, $collection, $uri, $field_pk_id )
	{
		$output = "
			<br>
			<div class='simple-list' style='width:500px;'>
				<div>$info</div>
		";
		foreach( $collection as $item )
		{
			if($uri)
			{
				$output .= "
					<div><label style='display: inline-block; width:100px;'><a href='".site_url($uri.'/'.md5($item[$field_pk_id]))."' onclick='return confirm(\"Excluir?\");'>Excluir</a></label><label class='name'>".$item['label']."</label></div>
				";
			}
			else
			{
				$output .= "
					<div><label class='name'>".$item['label']."</label></div>
				";
			}
		}
		$output .= "
			</div>
		";
		
		return form_default_row("", "", $output);
	}
}

if( ! function_exists('form_default_js_submit') )
{
	/**
	 * Criar uma validação javascript extremamente simples com lista de objetos passada por parametro
	 *
	 * @param array $fields
	 * @return string
	 */
	function form_default_js_submit( $fields=array(), $call_if_ok="" )
	{
		$js="

		function salvar( form )
		{

		";

		foreach( $fields as $field )
		{
			if( ! is_array($field) )
			{
				$js.='
				if( $("#'.$field.'").val()=="" )
				{
					alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação).\n\n['.$field.']" );
					$("#'.$field.'").focus();
					return false;
				}
				';
			}
			else
			{
				$js.='
				if( $("#'.$field[0].'").val()=="" )
				{
					alert( "Informe os campos obrigatórios! \n\n(os campos obrigatórios tem um * logo após a identificação)\n\n['.$field[0].']" );
					$("#'.$field[0].'").focus();
					return false;
				}
				';
				
				if( $field[1]=='hora' || $field[1]=='time' )
				{
					$js.='
					if( ! hora_valida( $("#'.$field[0].'").val() ) )
					{
						alert( "Hora inválida!" );
						$("#'.$field[0].'").focus();
						return false;
					}
					
				';
				}
				
				if( $field[1]=='data' || $field[1]=='date' )
				{
					$js.='
					if( ! data_valida( $("#'.$field[0].'").val() ) )
					{
						alert( "Data inválida!" );
						$("#'.$field[0].'").focus();
						return false;
					}
					';
				}

				if( $field[1]=='float' )
				{
					$js.='
					if( ! decimal_valido( $("#'.$field[0].'").val() ) )
					{
						alert( "Número decimal inválido!" );
						$("#'.$field[0].'").focus();
						return false;
					}
					';
				}
			}
		}

		if( $call_if_ok!="" )
		{
			$js .= "
				$call_if_ok
			";
		}
		else
		{
			$js .= "

				if( confirm('Salvar?') )
				{
					form.submit();
				}
			";
		}

		$js .= "
			}
		";

		return $js;
	}
}

if( ! function_exists('aba_start') )
{
	/**
	 * Cria container de abas padrão do sistema
	 *
	 * @param string $id_container	ID da div que será o container de abas
	 * @param array(array()) $abas	configuração de cada aba, array com as seguintes posições:
	 * 
	 * array(<br>
	 * 		array( [ID], [LABEL], [SELECTED], [ONCLICK] )
	 * )
	 * 
	 * [ID]: 		string	required	ID da <LI> que representa a aba
	 * [LABEL]: 	string	required	Texto que será usado como rótulo da aba
	 * [SELECTED]: 	boolean	required	TRUE indica a aba selecionada
	 * [ONCLICK]: 	string	required	rotina javascript que irá rodar ao clique na aba
	 *
	 * @return unknown
	 */
	function aba_start($abas)
	{
		$ci = &get_instance();		
		$ci->load->library('user_agent');
		$brs = "<br /><br /><br />";
		/*
		if( strpos( $ci->agent->agent_string(), "Firefox") || strpos( $ci->agent->agent_string(), "Chrome") )
		{
			$brs = "<br /><br /><br />";
		}
		*/
		
		$output = "
<div class='aba_definicao'>
	<div id='aba'>
		<ul>";

		foreach($abas as $aba)
		{
			$selecionada=(isset($aba[2]) && $aba[2])?"abaSelecionada":"";
			$onclick=(isset($aba[3]))?$aba[3]:"";
			$output .= "
			<li id='".$aba[0]."' class='".$selecionada."' onclick=\"$onclick\">
				<span>".$aba[1]."</span>
			</li>";
		}
		$output .= "

		</ul>
	</div>
	<div class='div_aba_content'>$brs";

		return $output;
	}
}

if( ! function_exists('aba_end') )
{
	/**
	 * Fechar container de abas
	 *
	 * @param string $id
	 * @return string
	 */
	function aba_end($id='')
	{
		return '
	</div>
</div>
';
	}
}

if( ! function_exists('form_list_command_bar') )
{
	/**
	 * Cria uma barra de comandos padrão para a lista originalmente com os botões de filtrar e novo registro
	 *
	 * TODO: DOCUMENTAR ESSE MÉTODO NA BC
	 *
	 * @param array $config		Configurações para formação da barra no seguinte formato:
	 * 
	 *			$config['id']: Atributo ID da DIV principal
	 *
	 *			$config['filter']: Array com 2 posições, posição 0: Label do botão para exibir filtro, posição 1: onclick (js) do botão
	 *
	 *			$config['new']: boolean ou array com 2 posições.
	 *
	 *				TRUE: Criar botão Novo padrão
	 *				FALSE: Não criar botão Novo
	 *				Array com 2 posições, posição 0: Label do botão; posição 1: onclick (js) do botão
	 *			
	 *			$config['button']: Coleção de arrays para configurar uma lista de botões (INPUT BUTTON) que serão renderizados na barra de comandos.
	 *
	 *				$button[0] : atributo VALUE do INPUT
	 *				$button[1] : atributo ONCLICK do INPUT
	 *				$button[2] : (opcional) atributo ID e NAME do INPUT
	 *
	 *				$config['button'][] = $button;
	 *
	 *				Por Exemplo:
	 *
	 *				$config['button'][] = array( 'Enviar email', 'send_mail_js_function()', 'send_mail_btn' );
	 *				$config['button'][] = array( 'Gerar PDF', 'pdf_export_js_function()', 'pdf_export_btn' );
	 *				$config['button'][] = array( 'Imprimir', 'print_js_function()' );
	 *
	 * @return string
	 */
	function form_list_command_bar($config=array())
	{
		if(!isset($config['id'])) 		$config['id'] = 'command_bar';
		if(!isset($config['filter'])) 	$config['filter'] = array("Exibir Filtro",'$("#filter_bar").show();$("#exibir_filtro_button").hide();setCorFocus();');
		if(!isset($config['new']))		$config['new'] = FALSE;

		if($config['new'])
		{
			if(!is_array($config['new']))
			{
				// valor padrão para $config['new']
				$config['new'] = array('Novo', 'novo();');
			}
		}

		$id = $config['id'];
		$filter = $config['filter'];
		$new = $config['new'];

		$output = "<div id='$id' class='command-bar'>";

		if($filter)
		{
			if(sizeof($filter)<2) { echo 'Parametro $filter deve ser um array de 2 posições!';exit; }
			$output .= "<input id='exibir_filtro_button' name='exibir_filtro_button' type='button' value='$filter[0]' class='btn btn-mini' onclick='$filter[1]' />";
		}
		if($new)
		{
			if(sizeof($new)<2) { echo 'Parametro $new deve ser um array de 2 posições!';exit; }
			$output .= " <input type='button' value='$new[0]' class='btn btn-primary btn-mini' onclick='$new[1]' />";
		}

		if(isset($config['button']))
		{
			foreach($config['button'] as $button)
			{
				if(sizeof($button)<2) { echo 'Parametro $button deve ser um array de 2 posições!'; exit; }
				
				if(!isset($button[2])) { $button[2] = ''; }
				
				if(!isset($button[3])) 
				{ 
					$button[3] = 'btn btn-primary btn-mini'; 
				}				
				elseif($button[3] == "botao")
				{
					$button[3] = "btn btn-mini btn-primary";
				}
				else if($button[3] == "botao_vermelho")
				{
					$button[3] = "btn btn-mini btn-danger";
				}
				else if($button[3] == "botao_verde")
				{
					$button[3] = "btn btn-mini btn-success";
				}
				else if($button[3] == "botao_amarelo")
				{
					$button[3] = "btn btn-mini btn-warning";
				}		
				else if($button[3] == "botao_disabled")
				{
					$button[3] = "btn btn-mini";
				}				
				
				$output .= " <input type='button' value='$button[0]' class='$button[3]' onclick='$button[1]' id='$button[2]' name='$button[2]' />";
			}
		}

		$output .= '<br /><br />';

		return $output;
	}
}

if( ! function_exists('form_command_bar_detail_start') )
{
	/**
	 * Inicia uma barra de comandos para qualquer página com detalhes de algum registro
	 *
	 * @return string
	 */
	function form_command_bar_detail_start($id='command_bar' , $extra="")
	{
		return '<div id="'.$id.'" class="command-bar" '.$extra.'>';
	}
}

if(!function_exists('form_command_bar_detail_button'))
{
	/**
	 * Cria um botão de comando padrão. Deve estar dentro da caixa de comandos aberta por 'form_command_bar_detail_start'
	 *
	 * @param string $value
	 * @param string $onclick	Função javascript acionada pelo botão, NÃO NÃO NÃO deve ser usado aspas duplas
	 * @param string $extra		Extensão do objeto <INPUT para criar atributos livremente
	 * @return string
	 */
	function form_command_bar_detail_button( $value, $onclick, $extra="" )
	{
		return '
		<input type="button"
	                   value="'.$value.'"
	                   class="btn btn-small"
					   style="margin:0 3 0 3;padding:0 3 0 3;"
	                   onclick="'.$onclick.'"
	                   '.$extra.'
	                   />
		';
	}
}

if(!function_exists('comando'))
{
	function comando($id, $value, $js, $conf=array())
	{
		$title='';
		if(isset($conf['title']))
		{ 
			$title=$conf['title']; 
		}
		
		$class='btn btn-small';
		if(isset($conf['class']))
		{ 
			$class=$conf['class']; 
		}		
		
		if($class == "botao")
		{
			$class = "btn btn-small btn-primary";
		}
		else if($class == "botao_vermelho")
		{
			$class = "btn btn-small btn-danger";
		}
		else if($class == "botao_verde")
		{
			$class = "btn btn-small btn-success";
		}
		else if($class == "botao_amarelo")
		{
			$class = "btn btn-small btn-warning";
		}		
		else if($class == "botao_disabled")
		{
			$class = "btn btn-small";
		}			
		
		$out="<input type='button' class='".$class."' id='".$id."' name='".$id."' value='".$value."' onclick='".$js."' title='".$title."' />";
		return $out;
	}
}

/*
function br($n=1)
{
	$o="";
	for($i=0;$i<$n;$i++)
	{
		$o.="<br />";
	}
	return $o;
}
*/

function nbsp($n=1)
{
	$o="";
	for($i=0;$i<$n;$i++)
	{
		$o.="&nbsp";
	}
	return $o;
}


if(!function_exists('button_save'))
{
	function button_save($value='Salvar', $onclick='salvar(this.form);', $class="botao", $extra="")
	{
		if(trim($onclick) == '')
		{
			$onclick = 'salvar(this.form);';
		}
	
		if($class == "botao")
		{
			$class = "btn btn-small btn-primary";
		}
		else if($class == "botao_vermelho")
		{
			$class = "btn btn-small btn-danger";
		}
		else if($class == "botao_verde")
		{
			$class = "btn btn-small btn-success";
		}
		else if($class == "botao_amarelo")
		{
			$class = "btn btn-small btn-warning";
		}		
		else if($class == "botao_disabled")
		{
			$class = "btn btn-small";
		}		
		
		$output = "
		<input type='button' value='".$value."' class='".$class."' onclick='".$onclick."' ".$extra." />
		";

		return $output;
	}
}

if(!function_exists('button_delete'))
{
	/**
	 * Criar um botão padrão de exclusão
	 *
	 * @param string 	$uri		'controller/método' responsável pela exclusão
	 * @param int 		$pk			Código do registro que será excluído
	 * @param array 	$config		Coleção de configurações do botão
	 * @return string
	 */
	function button_delete($uri,$pk,$config=array())
	{
		$link = site_url( $uri ) . '/' . md5(intval($pk));
		$link = "excluir(   '$link'   )";

		if(!isset($config['id'])) 		$config['id']='';
		if(!isset($config['value'])) 	$config['value']='Excluir';
		if(!isset($config['class'])) 	$config['class']='btn btn-small btn-danger'; #$config['class']='botao_vermelho';
		if(!isset($config['style']))	$config['style']='margin:0 3 0 3;padding:0 3 0 3;';
		if(!isset($config['extra'])) 	$config['extra']='';

		return '<input type="button"
					   id="'.$config['id'].'"
					   name="'.$config['id'].'"
	                   value="'.$config['value'].'"
	                   class="'.$config['class'].'"
					   style="'.$config['style'].'"
	                   onclick="'.$link.'"
	                   '.$config['extra'].'
	                   />';
	}
}

if( !function_exists('form_command_bar_detail_end') )
{
	/**
	 * Finaliza uma caixa de botões de comando para qualquer página com detalhes de algum registro
	 *
	 * @return string
	 */
	function form_command_bar_detail_end()
	{
		return '</div>';
	}
}

if(!function_exists('form_list_button_edit'))
{
	function form_list_button_edit($uri, $id)
	{
		$link = site_url($uri)."/".$id;
		$link = "location.href='$link'";
		$link = "onclick=\"$link\"";
		$output = form_input( array('type'=>'button'), "Editar", $link . " class='btn btn-small'" );
		return $output;
	}
}

function filter_checkbox_group($id, $caption="", $collection=array(), $checked=array(), $height = 100 , $width = 500, $extra="")
{
	return form_default_checkbox_group($id, $caption, $collection, $checked, $height, $width, $extra, TRUE);
}

function filter_mes_ano($id1, $id2, $rotulo='Mês', $dt_referencia='')
{
	return form_default_mes_ano($id1,$id2, $rotulo, $dt_referencia, true);
}

function filter_dropdown($id, $caption="", $collection=array(), $selected=array(), $extra="")
{
	return form_default_dropdown($id, $caption, $collection, $selected, $extra, TRUE);
}

function filter_empresa($id, $empresa_selecionada="", $label="Empresa", $tipo_empresa="", $extra="")
{
	return form_default_empresa($id, $empresa_selecionada, $label, $tipo_empresa, $extra, TRUE);
}

function filter_text($id, $caption="", $value="", $extra="", $maxlength=0)
{
	return form_default_text($id, $caption, $value, $extra, $maxlength, TRUE);
}

function filter_integer($id, $caption="", $value="", $extra="")
{
	return form_default_integer($id, $caption, $value, $extra,TRUE);
}

function filter_date($id, $caption="", $value="", $id_row="", $disable=false)
{
	return form_default_date($id, $caption, $value, $id_row, $disable, TRUE);
}

function filter_cpf($id, $caption="", $value="", $extra="")
{
	return form_default_cpf($id, $caption, $value, $extra,TRUE);
}

function filter_date_interval($id_1, $id_2, $caption="", $value_1="", $value_2="")
{
	return form_default_date_interval($id_1, $id_2, $caption, $value_1, $value_2, TRUE);
}

function filter_integer_interval($id_1, $id_2, $caption="", $value_1="", $value_2="")
{
	return form_default_integer_interval($id_1, $id_2, $caption, $value_1, $value_2, TRUE);
}

function filter_integer_ano($id_1, $id_2, $caption="", $value_1="", $value_2="")
{
	return form_default_integer_ano($id_1, $id_2, $caption, $value_1, $value_2, TRUE);
}

function filter_hidden($id, $caption="", $value="", $extra="", $maxlen=0)
{
	return form_default_hidden($id, $caption, $value, $extra, $maxlen, TRUE);
}



function filter_usuario_ajax($id, $gerencia_selecionada='', $usuario_selecionado='', $label_usuario="Usuário:", $label_gerencia="Gerência:")
{
	$ci = &get_instance();
	if($gerencia_selecionada=='')
	{
		if(is_array($id))
		{
			$gerencia_selecionada = resgatar_filtro( $id[0] );
		}
		else
		{
			$gerencia_selecionada = resgatar_filtro( $id . '_gerencia' );
		}
	}
	if($usuario_selecionado=='')
	{
		if(is_array($id))
		{
			$usuario_selecionado = resgatar_filtro( $id[1] );
		}
		else
		{
			$usuario_selecionado = resgatar_filtro( $id );
		}
	}

	return form_default_usuario_ajax($id,$gerencia_selecionada,$usuario_selecionado,$label_usuario,$label_gerencia);
}

function filter_participante( 
		$ids=array('cd_empresa','cd_registro_empregado','seq_dependencia', 'nome_participante')
		, $caption='Participante:'
		, $values=false
		, $nome_integrado=false
		, $exibir_botao_buscar=true
		, $callback_apos_consultar=''
	)
{
	if(!isset($values[$ids[0]])){$values[$ids[0]] = resgatar_filtro($ids[0]);}
	if(!isset($values[$ids[1]])){$values[$ids[1]] = resgatar_filtro($ids[1]);}
	if(!isset($values[$ids[2]])){$values[$ids[2]] = resgatar_filtro($ids[2]);}
	//echo $values[$ids[1]];
	if( $values[$ids[0]]=='-1' ){ $values[$ids[0]]=''; }
	if( $values[$ids[1]]=="0" ){ $values[$ids[1]]=''; }
	if( $values[$ids[2]]=='-1' ){ $values[$ids[2]]=''; }
	//echo $values[$ids[1]]	;
	return form_default_participante($ids
		, $caption
		, $values
		, $nome_integrado
		, $exibir_botao_buscar
		, $callback_apos_consultar);
}

function filter_plano_ajax( $id, $empresa_selecionada='', $plano_selecionado='', $label_empresa="Empresa", $label_plano="Plano", $tipo_empresa="", $where="")
{
	if(is_array($id))
	{
		$id_objeto_empresa = $id[0];
		$id_objeto_plano = $id[1];
	}
	else
	{
		$id_objeto_plano = $id;
		$id_objeto_empresa = $id_objeto_plano . "_empresa";
	}
	
	if(trim($empresa_selecionada) == '')
	{
		$empresa_selecionada = resgatar_filtro($id_objeto_empresa);
	}	
	
	if(trim($plano_selecionado) == '')
	{
		$plano_selecionado = resgatar_filtro($id_objeto_plano);
	}	

	return form_default_plano_ajax( $id, $empresa_selecionada, $plano_selecionado, $label_plano, $label_empresa, $tipo_empresa,$where);
}

function filter_plano_empresa_ajax($id, $empresa_selecionada = '', $plano_selecionado = '', $label_empresa = 'Empresa', $label_plano = 'Plano',  $where = '')
{
	if(is_array($id))
	{
		$id_objeto_plano = $id[0];
		$id_objeto_empresa = $id[1];
	}
	else
	{
		$id_objeto_plano   = $id;
		$id_objeto_empresa = $id_objeto_plano . '_empresa';
	}

	if(trim($empresa_selecionada) == '')
	{
		$empresa_selecionada = resgatar_filtro($id_objeto_empresa);
	}	
	
	if(trim($plano_selecionado) == '')
	{
		$plano_selecionado = resgatar_filtro($id_objeto_plano);
	}	

	return form_default_plano_empresa_ajax($id, $empresa_selecionada, $plano_selecionado, $label_empresa, $label_plano, $where);
}


function filter_mes($id, $rotulo="Mês:", $selecionado="", $extra="")
{
	return form_default_mes($id, $rotulo="Mês:", $selecionado="", $extra="", $filtro=TRUE);
}


if (!function_exists('form_default_mes'))
{
	/**
	 * Text Input Field - Date Object
	 *
	 * @param int $id				NAME e ID do input
	 * @param string $caption		Legenda
	 * @param string/array $value	Passar o valor do campo, ou um array contendo um campo com nome igual ao $id
	 * @param string $extra			Extensão para atributos do objeto INPUT (p.ex.:    style='display:none;'
	 */
	function form_default_mes($id, $rotulo="Mês:", $selecionado="", $extra="", $filtro=FALSE)
	{
		$ar_mes[] = array('value'=>'','text'=>'Todos');
		$ar_mes[] = array('value'=>'01','text'=>'Janeiro');
		$ar_mes[] = array('value'=>'02','text'=>'Fevereiro');
		$ar_mes[] = array('value'=>'03','text'=>'Março');
		$ar_mes[] = array('value'=>'04','text'=>'Abril');
		$ar_mes[] = array('value'=>'05','text'=>'Maio');
		$ar_mes[] = array('value'=>'06','text'=>'Junho');
		$ar_mes[] = array('value'=>'07','text'=>'Julho');
		$ar_mes[] = array('value'=>'08','text'=>'Agosto');
		$ar_mes[] = array('value'=>'09','text'=>'Setembro');
		$ar_mes[] = array('value'=>'10','text'=>'Outubro');
		$ar_mes[] = array('value'=>'11','text'=>'Novembro');
		$ar_mes[] = array('value'=>'12','text'=>'Dezembro');		
		
		$selecionado = array($selecionado);
		
		return form_default_dropdown($id, $rotulo, $ar_mes, $selecionado, $extra, $filtro);
	}
}

function form_default_mes_ano($id1,$id2, $rotulo='Mês', $dt_referencia='', $filtro = false)
{
	$a = explode('/', $dt_referencia);
	
	if(sizeof($a)==3)
	{
		$dia=$a[0];
		$mes=$a[1];
		$ano=$a[2];
	}
	else
	{
		$dia='';
		$mes='';
		$ano='';
	}
	
	if($filtro==TRUE)
	{
		// filtros
		$ci = &get_instance();
		$filtro = resgatar_filtro( $id1 );
		if(  trim( $filtro )!=''  )
		{
			$mes = $filtro;
		}
		
		$filtro = resgatar_filtro( $id2 );
		if(  trim( $filtro )!=''  )
		{
			$ano = $filtro;
		}
	}
	
	$meses[]=array('value'=>'','text'=>'Selecione');
	$meses[]=array('value'=>'01','text'=>'Janeiro');
	$meses[]=array('value'=>'02','text'=>'Fevereiro');
	$meses[]=array('value'=>'03','text'=>'Março');
	$meses[]=array('value'=>'04','text'=>'Abril');
	$meses[]=array('value'=>'05','text'=>'Maio');
	$meses[]=array('value'=>'06','text'=>'Junho');
	$meses[]=array('value'=>'07','text'=>'Julho');
	$meses[]=array('value'=>'08','text'=>'Agosto');
	$meses[]=array('value'=>'09','text'=>'Setembro');
	$meses[]=array('value'=>'10','text'=>'Outubro');
	$meses[]=array('value'=>'11','text'=>'Novembro');
	$meses[]=array('value'=>'12','text'=>'Dezembro');
	
	if($ano==''){$ano=date('Y');}
	
	$o='';
	$o .= "<select id='$id1' name='$id1'>";
	foreach($meses as $item)
	{
		if($mes==$item['value']){$selected=' selected';}else{$selected='';}
		$o.="<option value='".$item['value']."'$selected>".$item['text']."</option>";
	}
	$o .= "</select>";
	$o .= " / <input id='$id2' name='$id2' value='$ano' style='width:50px;' maxlenght='4' />";
	
	return form_default_row($id1.'_'.$id2, $rotulo, $o );
}


function anchor_file($uri = '', $title = '', $attributes = '')
{
	$ci = &get_instance();	
	
	$ci->load->library('user_agent');

	if(strtoupper($ci->agent->browser()) == 'FIREFOX')
	{
		$uri = 'file:///'.$uri;
	}
	else
	{
		$uri = 'file://'.$uri;
	}
	
	return anchor($uri, $title, $attributes);
	
}

function filter_processo($id, $caption="", $value="", $extra="")
{
	return form_default_processo($id, $caption, $value, $extra, TRUE, TRUE);
}

function form_default_processo($id, $caption="", $value="", $extra="", $fl_vigencia=FALSE, $filtro=FALSE)
{
	$ci = &get_instance();
	
	if((trim($value) == '') AND ($filtro == TRUE))
	{
		$filtro = resgatar_filtro($id);
		if(trim($filtro) != "")
		{
			$value = $filtro;
		}
	}	
	
	$qr_sql = "
				SELECT p.cd_processo, 
					   p.procedimento AS ds_processo
				  FROM projetos.processos p
				 WHERE p.dt_exclusao IS NULL
				   AND p.dt_ini_vigencia <= CURRENT_DATE
				   AND COALESCE(p.dt_fim_vigencia, CURRENT_DATE) >= CURRENT_DATE
				 ORDER BY ds_processo
			  ";
	$ob_resul = $ci->db->query($qr_sql);		
	$ar_vigente_sim = $ob_resul->result_array();
	
	$qr_sql = "
				SELECT p.cd_processo, 
					   p.procedimento AS ds_processo
				  FROM projetos.processos p
				 WHERE p.dt_exclusao IS NULL
				   AND COALESCE(p.dt_fim_vigencia, CURRENT_DATE) < CURRENT_DATE
				   ".($fl_vigencia == FALSE ? "AND p.cd_processo = ".intval($value) : "")."
				 ORDER BY ds_processo
			  ";
	$ob_resul = $ci->db->query($qr_sql);		
	$ar_vigente_nao = $ob_resul->result_array();	

	$fl_grupo = (((count($ar_vigente_sim) > 0) AND (count($ar_vigente_nao) > 0)) ? TRUE : FALSE);

	$cb = '<select id="'.$id.'" name="'.$id.'" '.$extra.'>';
	$cb.= '<option value="">Selecione</option>';
	
	$cb.= ($fl_grupo == TRUE ? '<optgroup label="Vigente">' : '');
	foreach($ar_vigente_sim as $item)
	{
		$cb.= '<option value="'.$item['cd_processo'].'" '.((intval($value) == intval($item["cd_processo"]) ? "selected": "")).'>'.$item['ds_processo'].'</option>';
	}
	$cb.= ($fl_grupo == TRUE ? '</optgroup>' : '');

	$cb.= ($fl_grupo == TRUE ? '<optgroup label="Prescrito">' : '');
	foreach($ar_vigente_nao as $item)
	{
		$cb.= '<option value="'.$item['cd_processo'].'" '.((intval($value) == intval($item["cd_processo"]) ? "selected": "")).'>'.$item['ds_processo'].'</option>';
	}	
	$cb.= ($fl_grupo == TRUE ? '</optgroup>' : '');
	$cb.= '</select>';

	return form_default_row('', $caption, $cb);
}

// ------------------------------------------------------------------------

function filter_gerencia($id, $caption = '', $value = '', $extra = '')
{
	return form_default_gerencia($id, $caption, $value, $extra, TRUE, TRUE);
}

if ( ! function_exists('form_default_gerencia'))
{
	function form_default_gerencia($id, $caption = '', $value = '', $extra = '', $fl_vigencia = FALSE, $filtro = FALSE)
	{
		$CI = &get_instance();

		if((trim($value) == '') AND ($filtro == TRUE))
		{
			$filtro = resgatar_filtro($id);

			if(trim($filtro) != '')
			{
				$value = $filtro;
			}
		}	

		$qr_sql = "
			SELECT codigo,
			       codigo || ' - ' || nome AS text
			  FROM projetos.divisoes
			 WHERE tipo IN ('DIV', 'ASS')
			   AND dt_exclusao IS NULL
			   AND dt_vigencia_ini <= CURRENT_DATE
			   AND COALESCE(dt_vigencia_fim, CURRENT_DATE) >= CURRENT_DATE
			 ORDER BY nome;";

		$gerencia_vigente = $CI->db->query($qr_sql)->result_array();

		$qr_sql = "
			SELECT codigo,
			       codigo || ' - ' || nome AS text
			  FROM projetos.divisoes
			 WHERE tipo IN ('DIV', 'ASS')
			   AND dt_exclusao IS NULL
			   AND COALESCE(dt_vigencia_fim, CURRENT_DATE) < CURRENT_DATE
			   ".(!$fl_vigencia ? "AND codigo = '".trim($value)."'" : "")."
			 ORDER BY nome;";

		$gerencia_nao_vigente = $CI->db->query($qr_sql)->result_array();

		$fl_grupo = (((count($gerencia_vigente) > 0) AND (count($gerencia_nao_vigente) > 0)) ? TRUE : FALSE);

		$cb = '<select id="'.$id.'" name="'.$id.'" '.$extra.'>';
		$cb.= '<option value="">Selecione</option>';
		
		$cb.= ($fl_grupo == TRUE ? '<optgroup label="Vigente">' : '');

		foreach($gerencia_vigente as $item)
		{
			$cb.= '<option value="'.$item['codigo'].'" '.((trim($value) == trim($item["codigo"]) ? "selected": "")).'>'.$item['text'].'</option>';
		}
		$cb.= ($fl_grupo == TRUE ? '</optgroup>' : '');

		$cb.= ($fl_grupo == TRUE ? '<optgroup label="Prescrito">' : '');

		foreach($gerencia_nao_vigente as $item)
		{
			$cb.= '<option value="'.$item['codigo'].'" '.((trim($value) == trim($item["codigo"]) ? "selected": "")).'>'.$item['text'].'</option>';
		}	
		$cb.= ($fl_grupo == TRUE ? '</optgroup>' : '');
		$cb.= '</select>';

		return form_default_row($id, $caption, $cb);
	}
}

// ------------------------------------------------------------------------

function filter_dropdown_optgroup($id, $caption = '', $collection = array(), $selected = array(), $extra = '')
{
	return form_default_dropdown_optgroup($id, $caption, $collection, $selected, $extra, TRUE);
}

if ( ! function_exists('form_default_dropdown_optgroup'))
{
	function form_default_dropdown_optgroup($id, $caption = '', $collection = array(), $selected = array(), $extra = '', $filtro = FALSE)
	{
		$CI = &get_instance();

		if(sizeof($selected)==0 && $filtro==TRUE)
		{
			$filtro = resgatar_filtro($id);

			if(trim($filtro) != '')
			{
				$selected = explode('|', $filtro);
			}
		}
	
		$form = '<select name="'.$id.'"'.$extra.' onkeypress="handleEnter(this, event);">'."\n";

		$options = array();

		$options[''] = 'Selecione';

		if($collection !== FALSE)
		{
			foreach($collection as $item)
			{
				$options[$item['value']] = $item['text'];
			}
		}

		foreach ($options as $key => $val)
		{
			$key = (string) $key;

			if (is_array($val))
			{
				$optgroup = array();

				if($val !== FALSE)
				{
					foreach($val as $item)
					{
						$optgroup[$item['value']] = $item['text'];
					}
				}

				$form .= '<optgroup label="'.$key.'">'."\n";

				foreach ($optgroup as $optgroup_key => $optgroup_val)
				{
					$sel = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';

					$form .= '<option value="'.$optgroup_key.'"'.$sel.'>'.(string) $optgroup_val."</option>\n";
				}

				$form .= '</optgroup>'."\n";
			}
			else
			{
				$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';

				$form .= '<option value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
			}
		}

		$form .= '</select>';

		return form_default_row('', $caption, $form);
	}
}

/* End of file form_helper.php */
/* Location: ./system/helpers/form_helper.php */