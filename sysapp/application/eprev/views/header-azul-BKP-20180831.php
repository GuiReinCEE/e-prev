<?php header('Content-Type: text/html; charset=ISO-8859-1'); 
if(!isset($fl_exibe_topo))
{
	$fl_exibe_topo = TRUE;
}
#<!doctype html>
#<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
#<META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT">
#<meta http-equiv="X-UA-Compatible" content="chrome=1" />
#<meta http-equiv="X-UA-Compatible" content="IE=edge" >
?>
<!doctype html>
<html>
<head>
	<title>e-prev [<?php echo get_title(); ?>]</title>
	<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
	
	<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico" type="image/x-icon" >
	
	<style type="text/css">
		@font-face {
			font-family: 'YanoneKaffeesatzRegular';
			src: url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.eot');
			src: url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.eot?#iefix') format('embedded-opentype'),
				 url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.woff') format('woff'),
				 url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.ttf') format('truetype'),
				 url('<?php echo base_url(); ?>fonts/Yanone_Kaffeesatz/yanonekaffeesatz-regular-webfont.svg#YanoneKaffeesatzRegular') format('svg');
			font-weight: normal;
			font-style: normal;
		}

		@font-face {
			font-family: 'AldrichRegular';
			src: url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.eot');
			src: url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.eot?#iefix') format('embedded-opentype'),
				 url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.woff') format('woff'),
				 url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.ttf') format('truetype'),
				 url('<?php echo base_url(); ?>fonts/Aldrich/aldrich-regular-webfont.svg#AldrichRegular') format('svg');
			font-weight: normal;
			font-style: normal;
		}	

		
		@font-face {
			font-family: 'FrancoisOneRegular';
			src: url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.eot');
			src: url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.eot?#iefix') format('embedded-opentype'),
				 url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.woff') format('woff'),
				 url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.ttf') format('truetype'),
				 url('<?php echo base_url(); ?>fonts/Francois_One/francoisone-webfont.svg#AldrichRegular') format('svg');
			font-weight: normal;
			font-style: normal;
		}		
		
	</style>
	

	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>extjs/resources/css/ext-all.css" />
 	<script type="text/javascript" src="<?php echo base_url(); ?>extjs/adapter/jquery/jquery-1.3.2.min.js"></script>
        
 	<script type="text/javascript" src="<?php echo base_url(); ?>extjs/adapter/jquery/ext-jquery-adapter.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>extjs/ext-all.js"></script>	
	
	
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery-ui-1.8.23.custom.min.js"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/cupertino/jquery-ui-1.8.23.custom.css" />
	
	<link href="<?php echo base_url(); ?>skins/skin002/css/abas_verde.css" rel="stylesheet" type="text/css">
	<link href="<?php echo base_url(); ?>skins/skin002/css/default.css" rel="stylesheet" type="text/css">

	<script type='text/javascript' src='<?php echo base_url(); ?>js/default.js'></script>
	<script type='text/javascript' src='<?php echo base_url(); ?>skins/skin002/sort_table/sortabletable.js'></script>
	<link type='text/css' rel='StyleSheet' href='<?php echo base_url(); ?>skins/skin002/sort_table/sortabletable.css'>

	<script src="<?php echo base_url(); ?>js/jquery-plugins/jquery.maskedinput-1.2.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>js/jquery-plugins/jquery.price_format.1.7.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>js/jquery-plugins/jquery-numeric-pack.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>js/jquery-plugins/jquery.md5.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>js/jquery-plugins/jquery.simplemodal-1.3.3.min.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>js/jquery-plugins/jquery.textarearesizer.compressed.js" type="text/javascript"></script>
	<script src="<?php echo base_url(); ?>js/jquery-plugins/jquery.fontResizer-2.0.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>js/jquery-plugins/jquery.simpletreeview.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>js/jquery-plugins/jquery.progressbar.min.js" type="text/javascript"></script>
    <script src="<?php echo base_url(); ?>js/jquery-plugins/jquery.cpf-validate.min.js" type="text/javascript"></script>
	
    
	<script src="<?php echo base_url(); ?>js/jquery-plugins/jquery.cookie.js" type="text/javascript"></script>
	
	
	<link type='text/css' href='<?php echo base_url(); ?>js/janela/basic.css' rel='stylesheet' media='screen' />
	<!-- IE 6 "fixes" -->
	<!--[if lt IE 7]>
	<link type='text/css' href='<?php echo base_url(); ?>js/janela/basic_ie.css' rel='stylesheet' media='screen' />
	<![endif]-->	


	<!-- Editor HTML -->
	<script type="text/javascript" src="<?php echo base_url(); ?>js/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/ckeditor/adapters/jquery.js"></script>
	

	<!-- Status Bar -->
	<link type="text/css" href="<?php echo base_url(); ?>js/jixedbar/themes/default/jx.stylesheet.css" rel="stylesheet" />
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jixedbar/src/jquery.jixedbar.js"></script>
	
	
	<script src="<?php echo base_url(); ?>js/jquery-plugins/colorpicker.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>js/jquery-plugins/colorpicker.css" />
	
	
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/jquery.dd.css" />
	<script src="<?php echo base_url(); ?>js/jquery-plugins/jquery.dd.js" type="text/javascript"></script>
		
	<script src="<?php echo base_url(); ?>js/date_pack.js" type="text/javascript"></script>

	<!-- calendar stylesheet -->
	<link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url() ?>js/jscalendar/calendar-eprev.css" title="win2k-cold-1" />
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/calendar.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/lang/calendar-br.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/jscalendar/calendar-setup.js"></script>
	
	
	<!-- upload multiplo -->
	<link rel="stylesheet" href="<?php echo base_url() ?>js/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css" type="text/css" media="screen" />
	<script type="text/javascript" src="<?php echo base_url() ?>js/plupload/plupload.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/plupload/plupload.gears.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/plupload/plupload.silverlight.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/plupload/plupload.flash.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/plupload/plupload.html4.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/plupload/plupload.html5.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/plupload/jquery.plupload.queue/jquery.plupload.queue.js"></script>
	<script type="text/javascript" src="<?php echo base_url() ?>js/plupload/i18n/pt-br.js"></script>	
	
	<!-- tree view -->
	<link type="text/css" href="<?php echo base_url(); ?>js/jquery.treeview/jquery.treeview.css" rel="stylesheet" />
	<script type="text/javascript" src="<?php echo base_url() ?>js/jquery.treeview/jquery.treeview.js"></script>
	

	<!-- crop imagem -->
	<script src="<?php echo base_url(); ?>js/jCrop/js/jquery.Jcrop.js" type="text/javascript"></script>
	<link rel="stylesheet" href="<?php echo base_url(); ?>js/jCrop/css/jquery.Jcrop.css" type="text/css" />	
	
	<!-- Bootstrap -->
	<?php
		if(preg_match('/(?i)MSIE [1-7].0/',$_SERVER['HTTP_USER_AGENT']))
		{
			echo '<link href="'.base_url().'bootstrap/css/bootstrap-ie.css" rel="stylesheet" media="screen">';
		}
		else
		{
			echo '<link href="'.base_url().'bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">';
		}
	?>	
	
	<!-- style para editor de codigo (sql, php, js, ...) -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>js/codemirror-3.14/lib/codemirror.css">
    <script src="<?php echo base_url(); ?>js/codemirror-3.14/lib/codemirror.js"></script>
    <script src="<?php echo base_url(); ?>js/codemirror-3.14/addon/mode/loadmode.js"></script>
	<script src="<?php echo base_url(); ?>js/codemirror-3.14/addon/selection/active-line.js"></script>
	<link rel="stylesheet" href="<?php echo base_url(); ?>js/codemirror-3.14/theme/neat.css">
	<style type='text/css'>
		.CodeMirror {
			border: 1px solid #eee;
		}
		
		.CodeMirror-activeline-background {
			background: #e8f2ff !important;
		}
	</style>

	
	
<script type="text/javascript">   
	/*CALLBACK PARA FUNCAO .HTML()*/	
	(function ($) {
		// create a reference to the old .html() function
		var htmlOriginal = $.fn.html;
		
		// redefine the .html() function to accept a callback
		$.fn.html = function(html,callback){
		  // run the old .html() function with the first parameter
		  var ret = htmlOriginal.apply(this, arguments);
		  // run the callback (if it is defined)
		  if(typeof callback == "function"){
			callback();
		  }
		  // make sure chaining is not broken
		  return ret;
		}
	})(jQuery);

	jQuery.fn.extend({
		param: function( a ) {
			var s = [];

			// If an array was passed in, assume that it is an array
			// of form elements
			if ( a.constructor == Array || a.jquery ){
				// Serialize the form elements
				jQuery.each( a, function(){
					s.push(unescape(encodeURIComponent(escape( this.name ))).replace(/\+/g, "%2B") + "=" + unescape(encodeURIComponent(escape( this.value ))).replace(/\+/g, "%2B"));
				});
			}
			// Otherwise, assume that it's an object of key/value pairs
			else{
				// Serialize the key/values
				for ( var j in a )
					// If the value is an array then the key names need to be repeated
					if ( a[j] && a[j].constructor == Array )
						jQuery.each( a[j], function(){
							s.push(unescape(encodeURIComponent(escape( j ))).replace(/\+/g, "%2B") + "=" + unescape(encodeURIComponent(escape( this ))).replace(/\+/g, "%2B"));
						});
					else
						s.push(unescape(encodeURIComponent(escape( j ))).replace(/\+/g, "%2B") + "=" + unescape(encodeURIComponent(escape( a[j] ))).replace(/\+/g, "%2B"));
			}
			// Return the resulting serialization
			return s.join("&").replace(/ /g, "+");
		},
		serialize: function() {
			return this.param(this.serializeArray());
		}
	});  

	
	/*EXPORTA TABLE PARA CSV*/	
	jQuery.fn.table2CSV = function(options) {
		var options = jQuery.extend({
			separator: ';',
			header: [],
			delivery: 'file' // popup, value, file
		},
		options);

		var csvData = [];
		var headerArr = [];
		var el = this;

		//header
		var numCols = options.header.length;
		var tmpRow = []; // construct header avalible array

		if (numCols > 0) {
			for (var i = 0; i < numCols; i++) {
				tmpRow[tmpRow.length] = formatData(options.header[i]);
			}
		} else {
			$(el).filter(':visible').find('th').each(function() {
				if ($(this).css('display') != 'none') tmpRow[tmpRow.length] = formatData($(this).html());
			});
		}

		row2CSV(tmpRow);

		// actual data
		$(el).find('tr').each(function() {
			var tmpRow = [];
			$(this).filter(':visible').find('td').each(function() {
				if ($(this).css('display') != 'none') tmpRow[tmpRow.length] = formatData($(this).html());
			});
			row2CSV(tmpRow);
		});
		if (options.delivery == 'popup') {
			var mydata = csvData.join('\n');
			return popup(mydata);
		} 
		else if (options.delivery == 'file') {
			var mydata = csvData.join('\n');
			return submitTofile(mydata);
		}	
		else {
			var mydata = csvData.join('\n');
			return mydata;
		}

		function row2CSV(tmpRow) {
			var tmp = tmpRow.join('') // to remove any blank rows
			// alert(tmp);
			if (tmpRow.length > 0 && tmp != '') {
				var mystr = tmpRow.join(options.separator);
				csvData[csvData.length] = mystr;
			}
		}
		function formatData(input) {
			// mask " with "
			var regexp = new RegExp(/["]/g); //"
			var output = input.replace(regexp, '""');
			// TODO: mask \""; at the end, so openoffice can open it correctly			
			
			
			// strip HTML
			output = output.replace("<br>"," ");
			if(!( output != null && typeof output === 'object')) output = "<span>"+output+"</span>"; // to be able to use jquery
			output = $(output).text().trim();
			
			if (output == "") return '';
			return '"' + output + '"';			
			
			/*
			//HTML
			var regexp = new RegExp(/\<[^\<]+\>/g);
			var output = output.replace(regexp, "");
			if (output == "") return '';
			return '"' + output + '"';
			*/
		}
		function popup(data) {
			var generator = window.open('', 'csv', 'height=400,width=600');
			generator.document.write('<html><head><title>CSV</title>');
			generator.document.write('</head><body >');
			generator.document.write('<textArea cols=70 rows=15 wrap="off" >');
			generator.document.write(data);
			generator.document.write('</textArea>');
			generator.document.write('</body></html>');
			generator.document.close();
			return true;
		}
		function submitTofile(data) {
			$("#formGridCSVExport").remove();
			$form = $('<form style="display:none;" id="formGridCSVExport" action="<?php echo site_url("/geral/exportToCSV"); ?>" method="post" target="_blank"><textarea name="obGridCSVExport">'+data+'</textarea></form>');
			$('body').append($form);
			$("#formGridCSVExport").submit();		
			$("#formGridCSVExport").remove();		
			return true;
		}	
	};	
	

	function realizar_login()
	{
		var f = document.forms[0];
		f.action = document.getElementById('base_url').value + 'login/entrar';
		f.submit();
	}
	
	function buscar()
	{
		var f = document.form_busca_header;
		f.action = '<?php echo site_url('geral/buscar'); ?>';
		f.submit();
	}
	
	function enterBuscar(e)
	{
		if(e.keyCode==13)
		{
			buscar();
			return false;
		}
		else
		{
			return true;
		}
	}
	
    $(document).ready(function()
	{
		enter2tab();
		setCorFocus();
		
		/* jQuery textarea resizer plugin usage */
		$('textarea.resizable:not(.processed)').TextAreaResizer();			
    });
 	

	
</script>

<style>
	.autocompleteLoading {
		background: url('<?php echo base_url(); ?>loader_p.gif') no-repeat center right;
	}

	.highlight {
		background-color: #FFFFE0;
	}	
	
	.highlight_borda {
		border: 1px solid #000080;
		outline: 1px solid #000080;
	}	
	
	.highlight_readonly {
		border: 1px solid #DCDCDC;
		background: #F9F9F9;
		color: #565656;
	}		
</style>

<?php 
	if($fl_exibe_topo) #exibe menu
	{
?>	
<script type="text/javascript">  
	Ext.onReady(function() {
		Ext.BLANK_IMAGE_URL = '<?php echo base_url() ?>extjs/resources/images/default/s.gif';
		
		Ext.onReady(function() {

			var atividade = new Ext.menu.Menu({ id: 'atividade', items: <?php echo menu_extjs_start(8); ?> });
			var cadastro = new Ext.menu.Menu({ id: 'cadastro', items: <?php echo menu_extjs_start(40); ?> });
			var ecrm = new Ext.menu.Menu({ id: 'ecrm', items: <?php echo menu_extjs_start(4); ?> });
			var gestao = new Ext.menu.Menu({ id: 'gestao', items: <?php echo menu_extjs_start(29); ?> });
			var intranet = new Ext.menu.Menu({ id: 'intranet', items: <?php echo menu_extjs_start(281); ?> });
			var planos = new Ext.menu.Menu({ id: 'planos', items: <?php echo menu_extjs_start(16); ?> });
			var servicos = new Ext.menu.Menu({ id: 'servicos', items: <?php echo menu_extjs_start(31); ?> });

			var toolBar = new Ext.Toolbar({
				id:'toolBar'
				, renderTo: 'MenuDiv'
				, items:[
					new Ext.Button({text:'Início',handler:function(){ location.href='<?php echo site_url("home"); ?>' }})
					, new Ext.Button({text:'Atividades',menu:atividade})
					, new Ext.Button({text:'Cadastros',menu:cadastro})
					, new Ext.Button({text:'e-CRM',menu:ecrm})
					, new Ext.Button({text:'Gestão',menu:gestao})
					, new Ext.Button({text:'Intranet',menu:intranet})
					, new Ext.Button({text:'Planos',menu:planos})
					, new Ext.Button({text:'Serviços',menu:servicos})
				]
			});

		});


     

		
		/* jQuery select estilo combo */
		//$("body select").msDropDown();
		//$("body select").hide();
	});
</script>
<?php
	} #exibe menu
?>

<script type="text/javascript">  


	dateRanges = new Object();
	dateRanges.today        = function(d1,d2) {},
	dateRanges.yesterday    = function(d1,d2) {
		d1.addDays(-1);
		d2.addDays(-1);
	},
	dateRanges.tomorrow    = function(d1,d2) {
		d1.addDays(+1);
		d2.addDays(+1);
	},	
	dateRanges.last7days    = function(d1,d2) {
		d1.addDays(-7);
	},
	dateRanges.last15days   = function(d1,d2) {
		d1.addDays(-15);
	},	
	dateRanges.last30days   = function(d1,d2) {
		d1.addDays(-30);
	},
	dateRanges.next7days    = function(d1,d2) {
		d2.addDays(+7);
	},
	dateRanges.next15days   = function(d1,d2) {
		d2.addDays(+15);
	},	
	dateRanges.next30days   = function(d1,d2) {
		d2.addDays(+30);
	},	
	dateRanges.currentMonth = function(d1,d2) {
		d1.setDate(1);
		d2.setDate(d1.getDaysInMonth());
	},
	dateRanges.lastMonth    = function(d1,d2) {
		d1.setDate(1);
		d1.addMonths(-1);
		d2.setDate(1);
		d2.addDays(-1);
	},
	dateRanges.nextMonth    = function(d1,d2) {
		d1.setDate(1);
		d1.addMonths(+1);
		d2.addMonths(+1);
		d2.setDate(d2.getDaysInMonth());
	},	
	dateRanges.currentYear  = function(d1,d2) {
		d1.setDate(1);
		d1.setMonth(0);
		d2.setDate(1);
		d2.setMonth(11);
		d2.addDays(30);
	},
	dateRanges.lastYear     = function(d1,d2) {
		d1.setDate(1);
		d1.setMonth(0);
		d1.addYears(-1);
		d2.setDate(1);
		d2.setMonth(0);
		d2.addDays(-1);
	}	
	dateRanges.nextYear     = function(d1,d2) {
		d1.setDate(1);
		d1.setMonth(0);
		d1.addYears(+1);
		d2.setMonth(11);
		d2.addYears(+1);
		d2.setDate(31);
	}	
</script>
	
<style type="text/css">
	div.grippie {
		background:#EEEEEE url('<?php echo base_url(); ?>js/jquery-plugins/images/text_area_resize.png') no-repeat scroll center 2px;
		border-color:#DDDDDD;
		border-style:solid;
		border-width:0pt 1px 1px;
		cursor:s-resize;
		height:10px;
		overflow:hidden;
	}
	
	.resizable-textarea textarea {
		display:block;
		margin-bottom:0pt;
		width:95%;
		height: 20%;
		resize:both;
	}

	.textarea_impressao {
		display: none; 
	}
	
	@media print {
		#griWindow {
			display: none; 
		}	
	
		.resizable-textarea textarea { 
			display: none; 
		}
		
		.grippie {
			display: none; 
		}
		
		.textarea_impressao {
			display: block; 
			white-space: pre-wrap;
			font-size: 10pt;				
		}			
	}	
</style>		
	
<style>
	.header_fundo {
		margin: 0;
		padding: 0;
		width: 100%;
		height: 80px;
		background: url( '<?php echo base_url(); ?>skins/skin002/img/header/fundo.png' );
		border-bottom: 1px solid #A9BFD3;
	}
	
	.header_logo {
		/*float:left;*/
		margin-top: 5px;
		margin-left: 13px;
	}
	
	.header_tabela {
		float:left;
		border-collapse: collapse;
		margin-left: 20px;
		height: 80px;
	}

	.header_titulo {
		font-family: 'YanoneKaffeesatzRegular', Arial, Verdana;
		font-size: 24pt;
		line-height:65px
		width: 50%;
	}	

	.header_usuario {
		font-family: 'AldrichRegular',Arial, Verdana;
		font-size: 10pt;
		font-weight: normal;
		line-height:65px
		width: 50%;
	}	
	
	.header_menu_canto1 {
		width: 10px;
		height: 30px;
		background: url('<?php echo base_url(); ?>skins/skin002/img/header/menu_canto1.png');
	}

	.header_menu_canto2 {
		width: 10px;
		height: 30px;
		background: url('<?php echo base_url(); ?>skins/skin002/img/header/menu_canto2.png');
	}	

	.header_menu_meio {
		height: 30px;
		background: url('<?php echo base_url(); ?>skins/skin002/img/header/menu_meio.png');
	}	
	
	.header_menu {
		height: 30px;
	}

	.x-toolbar {
		background: none;
		border: 0px;
	}

	#header_pesquisa {
		width: 130px;
		font-family: Arial, Verdana;
		font-size: 10pt;
		border-top: 1px solid #A9BFD3;
		border-left: 1px solid #A9BFD3;
		border-bottom: 1px solid #A9BFD3;
		border-right: 1px solid #A9BFD3;
	}
	
	.botao_busca_topo {
		border: 1px solid #75AAD6;
		font-family: Arial, Verdana;
		font-size: 8pt;
		color: #00287A;
		background: url('<?php echo base_url(); ?>skins/skin002/img/header/bgBtnBlue.gif') #ecf1f4;
		font-weight: bold;
		vertical-align: middle;
	}
</style>

<style>
	.notify_conta {
		<?php
			if(preg_match('/(?i)MSIE [1-8].0/',$_SERVER['HTTP_USER_AGENT']))
			{
				echo 'background: #F93A43;';
			}
		?>
		background-image: linear-gradient(#F93A43 0%, #DF121C 100%);
		background-position:initial initial;
		background-repeat:initial initial;
		border-bottom-left-radius:2px;
		border-bottom-right-radius:2px;
		border-top-left-radius:2px;
		border-top-right-radius:2px;
		color:#FFFFFF;
		display:inline;
		float:right;
		font-size: 10px;
		margin-right:3px;
		padding:0 3px;		
	}

	.notify_nao{
		background-position: 0px 6px;
		background-image: url('<?php echo base_url(); ?>img/notify_pendencia.png');
		background-repeat: no-repeat;
		width: 16px;
		height: 24px;
		text-align: left;
		padding-left: 8px;
		
		opacity: 0.6;
	}	
	
	.notify_sim{
		background-position: 0px 6px;
		background-image: url('<?php echo base_url(); ?>img/notify_pendencia.png');
		background-repeat: no-repeat;
		width: 20px;
		height: 24px;
		text-align: left;
		padding-left: 8px;

		
		opacity: 0.8;
		-webkit-animation: ring 8s 1s ease-in-out infinite;
		-webkit-transform-origin: 50% 4px;
		-moz-animation: ring 8s 1s ease-in-out infinite;
		-moz-transform-origin: 50% 4px;
		animation: ring 8s 1s ease-in-out infinite;
		transform-origin: 50% 4px;
	}

	@-webkit-keyframes ring {
		0% { -webkit-transform: rotateZ(0); }
		1% { -webkit-transform: rotateZ(30deg); }
		3% { -webkit-transform: rotateZ(-28deg); }
		5% { -webkit-transform: rotateZ(34deg); }
		7% { -webkit-transform: rotateZ(-32deg); }
		9% { -webkit-transform: rotateZ(30deg); }
		11% { -webkit-transform: rotateZ(-28deg); }
		13% { -webkit-transform: rotateZ(26deg); }
		15% { -webkit-transform: rotateZ(-24deg); }
		17% { -webkit-transform: rotateZ(22deg); }
		19% { -webkit-transform: rotateZ(-20deg); }
		21% { -webkit-transform: rotateZ(18deg); }
		23% { -webkit-transform: rotateZ(-16deg); }
		25% { -webkit-transform: rotateZ(14deg); }
		27% { -webkit-transform: rotateZ(-12deg); }
		29% { -webkit-transform: rotateZ(10deg); }
		31% { -webkit-transform: rotateZ(-8deg); }
		33% { -webkit-transform: rotateZ(6deg); }
		35% { -webkit-transform: rotateZ(-4deg); }
		37% { -webkit-transform: rotateZ(2deg); }
		39% { -webkit-transform: rotateZ(-1deg); }
		41% { -webkit-transform: rotateZ(1deg); }

		43% { -webkit-transform: rotateZ(0); }
		100% { -webkit-transform: rotateZ(0); }
	}

	@-moz-keyframes ring {
		0% { -moz-transform: rotate(0); }
		1% { -moz-transform: rotate(30deg); }
		3% { -moz-transform: rotate(-28deg); }
		5% { -moz-transform: rotate(34deg); }
		7% { -moz-transform: rotate(-32deg); }
		9% { -moz-transform: rotate(30deg); }
		11% { -moz-transform: rotate(-28deg); }
		13% { -moz-transform: rotate(26deg); }
		15% { -moz-transform: rotate(-24deg); }
		17% { -moz-transform: rotate(22deg); }
		19% { -moz-transform: rotate(-20deg); }
		21% { -moz-transform: rotate(18deg); }
		23% { -moz-transform: rotate(-16deg); }
		25% { -moz-transform: rotate(14deg); }
		27% { -moz-transform: rotate(-12deg); }
		29% { -moz-transform: rotate(10deg); }
		31% { -moz-transform: rotate(-8deg); }
		33% { -moz-transform: rotate(6deg); }
		35% { -moz-transform: rotate(-4deg); }
		37% { -moz-transform: rotate(2deg); }
		39% { -moz-transform: rotate(-1deg); }
		41% { -moz-transform: rotate(1deg); }

		43% { -moz-transform: rotate(0); }
		100% { -moz-transform: rotate(0); }
	}

	@keyframes ring {
		0% { transform: rotate(0); }
		1% { transform: rotate(30deg); }
		3% { transform: rotate(-28deg); }
		5% { transform: rotate(34deg); }
		7% { transform: rotate(-32deg); }
		9% { transform: rotate(30deg); }
		11% { transform: rotate(-28deg); }
		13% { transform: rotate(26deg); }
		15% { transform: rotate(-24deg); }
		17% { transform: rotate(22deg); }
		19% { transform: rotate(-20deg); }
		21% { transform: rotate(18deg); }
		23% { transform: rotate(-16deg); }
		25% { transform: rotate(14deg); }
		27% { transform: rotate(-12deg); }
		29% { transform: rotate(10deg); }
		31% { transform: rotate(-8deg); }
		33% { transform: rotate(6deg); }
		35% { transform: rotate(-4deg); }
		37% { transform: rotate(2deg); }
		39% { transform: rotate(-1deg); }
		41% { transform: rotate(1deg); }

		43% { transform: rotate(0); }
		100% { transform: rotate(0); }
	}	
</style>
<script type="text/javascript">
	var title_notify = "";
	function getNotificaoPendencia()
	{
		title_notify = title_notify == "" ? document.title : title_notify;
		$.post('<?php echo site_url('atividade/pendencia_minha/checar');?>',
        {},
        function(data)
        {
			if(data.qt_pendencia > 0)
			{
				if ($.browser.msie) 
				{
					$("#ob_notifica_pendencia").html('<div class="notify_conta">'+data.qt_pendencia+'</div>');
				}				
				else
				{
					$("#ob_notifica_pendencia").html('<id class="notify_conta">'+data.qt_pendencia+'</id>');
				}
				
				$("#ob_notifica_pendencia").removeClass("notify_nao");
				$("#ob_notifica_pendencia").addClass("notify_sim");	
				
				document.title = "("+data.qt_pendencia+") " + title_notify;
			}
			else
			{
				$("#ob_notifica_pendencia").html("");
				$("#ob_notifica_pendencia").removeClass("notify_sim");
				$("#ob_notifica_pendencia").addClass("notify_nao");	
				
				document.title = title_notify;
			}
        },
		'json');	
	}
	
	$(function(){
		getNotificaoPendencia();
		//setInterval(function(){getNotificaoPendencia()},30000);
	});
</script>

<style>
	.notify_atividade_conta {
		<?php
			if(preg_match('/(?i)MSIE [1-8].0/',$_SERVER['HTTP_USER_AGENT']))
			{
				echo 'background: #4D90FE;';
			}
		?>
		background-image: linear-gradient(#4D90FE 0%, #4787ED 100%);
		background-position:initial initial;
		background-repeat:initial initial;
		border-bottom-left-radius:2px;
		border-bottom-right-radius:2px;
		border-top-left-radius:2px;
		border-top-right-radius:2px;
		color:#FFFFFF;
		display:inline;
		float:right;
		font-size: 10px;
		margin-right:3px;
		padding:0 3px;		
	}

	.notify_atividade_nao{
		background-position: 0px 6px;
		background-image: url('<?php echo base_url(); ?>img/notify_atividade.png');
		background-repeat: no-repeat;
		width: 16px;
		height: 24px;
		text-align: left;
		padding-left: 8px;
		
		opacity: 0.5;
	}	
	
	.notify_atividade_sim{
		background-position: 0px 6px;
		background-image: url('<?php echo base_url(); ?>img/notify_atividade.png');
		background-repeat: no-repeat;
		width: 20px;
		height: 24px;
		text-align: left;
		padding-left: 8px;
		
		opacity: 0.8;
	}
</style>
<script type="text/javascript">
	function getNotificaoAtividade()
	{
		$.post('<?php echo site_url('atividade/minhas/notificacao');?>',
        {},
        function(data)
        {
			if(data.qt_atividade > 0)
			{
				if ($.browser.msie) 
				{
					$("#ob_notifica_atividade").html('<div class="notify_atividade_conta">'+data.qt_atividade+'</div>');
				}				
				else
				{
					$("#ob_notifica_atividade").html('<id class="notify_atividade_conta">'+data.qt_atividade+'</id>');
				}
				
				$("#ob_notifica_atividade").removeClass("notify_atividade_nao");
				$("#ob_notifica_atividade").addClass("notify_atividade_sim");	
			}
			else
			{
				$("#ob_notifica_atividade").html("");
				$("#ob_notifica_atividade").removeClass("notify_atividade_sim");
				$("#ob_notifica_atividade").addClass("notify_atividade_nao");	
			}
        },
		'json');	
	}
	
	$(function(){
		getNotificaoAtividade();
		<?php
			#if($this->session->userdata('divisao_ant') == "GI")
			#{
			#	echo "getNotificaoAtividade();";
			#}
		?>
	});
</script>

<?php
#### AVATAR BUGFIX IE 6,7,8 ####
if(preg_match('/(?i)MSIE [1-8].0/',$_SERVER['HTTP_USER_AGENT']))
{
	echo '
			<script type="text/javascript" src="'.base_url().'js/corner/justcorners.js"></script>
			<script type="text/javascript" src="'.base_url().'js/corner/corner.js"></script>
		 ';
}
?>	
<style>
/* AVATAR */

/* Circle Avatar Styles */
.circle {
	line-height: 0;		/* remove line-height */ 
	display: inline-block;	/* circle wraps image */
	margin: 5px;
	border-radius: 50%;	
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;		
	transition: linear 0.25s;
	height: 48px;
	width: 48px;
	-webkit-box-shadow: 0 0 0 3px #fff, 0 0 0 4px #999, 0 2px 5px 4px rgba(0,0,0,.2);
    -moz-box-shadow: 0 0 0 3px #fff, 0 0 0 4px #999, 0 2px 5px 4px rgba(0,0,0,.2);
    box-shadow: 0 0 0 3px #fff, 0 0 0 4px #999, 0 2px 5px 4px rgba(0,0,0,.2);		
}
.circle img {
	border-radius: 50%;	/* relative value for adjustable image size */
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;	
}
.circle:hover {
	transition: ease-out 0.2s;
	-webkit-transition: ease-out 0.2s;
}
a.circle {
	color: transparent;
} /* IE fix: removes blue border */
</style>
<body>
	<?php 
		if($fl_exibe_topo) #exibe menu
		{
	?>
	<div class="header_fundo" style='display:;'>

		<table cellpadding="0" cellspacing="0">
		<tr>
		<td>
			<div class="header_logo">
				<img src="<?php echo base_url(); ?>skins/skin002/img/header/logo_eprev.png">
			</div>
		</td>
		<td>
			<table border="0" class="header_tabela" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="header_titulo">
									<?php echo get_title(); ?>
								</td>
								<td align="right" class="header_usuario">
									<?php
										if( $this->session->userdata('guerra')=="" )
										{
											echo "Anônimo";
										}
										else
										{
											echo $this->session->userdata('guerra') . ' - ' . $this->session->userdata('divisao');
											echo " <a class='header_usuario' href='" . base_url().index_page() . "/login/sair'>[sair]</a>";
											
											$this->load->database();
											echo '<span style="font-size: 70%;"><BR>'.$this->db->hostname.'</span>';
										}
									?>
								</td>						
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td class="header_menu">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td class="header_menu_canto1"></td>

								<td class="header_menu_meio">
									<a href="<?php echo site_url('atividade/pendencia_minha'); ?>">
									<div id="ob_notifica_pendencia" class="notify_nao" title="Minhas pendências"></div>	
									</a>
								</td>	
								<?php
									#if($this->session->userdata('divisao_ant') == "GI")
									#{
								?>
								<td class="header_menu_meio" style="padding-left: 3px;">
									<a href="<?php echo site_url('atividade/minhas'); ?>">
									<div id="ob_notifica_atividade" class="notify_atividade_nao" title="Minhas atividades"></div>	
									</a>
								</td>		
								<?php
									#}
								?>								
								<td class="header_menu_meio" >
									<div id="MenuDiv"></div>
								</td>
								<td class="header_menu_meio" >
									<form method="post" name="form_busca_header">
									<table border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td style="padding-left: 5px;">
												<input type="text" name="keyword" id="keyword" title="Procure no e-prev" onkeypress="return handleEnter(this, event);" style=' width: 80px; height: 19px; font-family: verdana,arial; font-size: 8pt; border:1px solid #A5BFCE;' />
											</td>
											<td style="padding-left: 3px; padding-right: 5px;">
												<input type="button" value="Ok" class="botao_busca_topo" style="width: 25px;" onclick="buscar();" />
											</td>

										</tr>
									</table>
									</form>
								</td>
								<td class="header_menu_canto2"></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
		<td>
			<div class="header_logo">
				<?php
					if(trim($this->session->userdata('usuario')) != "")
					{
						$avatar_arquivo = $this->session->userdata('avatar');
						
						if(trim($avatar_arquivo) == "")
						{
							$avatar_arquivo = $this->session->userdata('usuario').".png";
						}
						
						if(!file_exists( "./up/avatar/".$avatar_arquivo))
						{
							$avatar_arquivo = "user.png";
						}
						
						/*
						echo '
								<a href="'.base_url().'index.php/cadastro/avatar/" class="circle" id="avatarEprev" title="Clique aqui para ajustar a foto">
									<img class="corner iradius24" height="48" width="48" src="'.base_url().'up/avatar/'.$avatar_arquivo.'" alt="'.$this->session->userdata('usuario').'">
								</a>											
							 ';	
						*/
						echo '
								<a href="#" class="circle" id="avatarEprev">
									<img class="corner iradius24" height="48" width="48" src="'.base_url().'up/avatar/'.$avatar_arquivo.'" alt="'.$this->session->userdata('usuario').'">
								</a>											
							 ';						
					}
				?>
			</div>
		</td>		
		</tr>
		</table>
	</div>

	<input id="root" name="root" type="hidden" value="<?php echo base_url(); ?>" />
  	<input id="base_url" name="base_url" type="hidden" value="<?php echo base_url(); ?>index.php/" />
	<input type="hidden" name="current_page" id="current_page" value="0" />

	<?php
		} #exibe menu
	?>

<div id="conteudo">
<script>
	var getGridRowFontePadrao = 26;
	var getGridRowFonteAtual  = 26;
	
	function getGridRow(obj,id_tabela,col_iniciar)
	{
		var header = $("#" + id_tabela).find("thead").find("tr");
		var ar_coluna = [];

		var conteudo = '<div id="gridDetalheTabItens">';
			conteudo+= '<ul>';
		var i = 0;
			$(header).children().each(function() { 
				$(this).removeClass();
				ar_coluna.push([$(this).text(),$(this).css('display')]);
				
				if((i > 0) && ($(this).css('display') != 'none'))
				{
					conteudo+= ' <li><a href="#gridDetalheTabItem-' + i + '">' + $(this).text() + '</a></li> ';
				}
				i++;
			});			
		conteudo+= '</ul>';
		conteudo+= '<div id="gridWindowTabObjItens">';
		
		var tr = $(obj).parent().parent();
		
		var i = 0;
		tr.find('td').each(function() { 
		
			if((i > 0) && (ar_coluna[i][1] != 'none'))
			{
				conteudo+= '<div id="gridDetalheTabItem-' + i + '"><div class="gridDetalheTabItemConteudo">' + $(this).html() + '</div></div>';
			}	
			i++;
		});		

		conteudo+= '</div>';
		conteudo+= '</div>';
		
		$('#gridDetalheTabConteudo').html(conteudo);
		
		$("#gridDetalheTab").dialog({
			width: $(window).width() - 20,
			height: $(window).height() - 20,
			modal: true,
			draggable: false,
			resizable: false,
			
			buttons: {
				"Padrão": function() {
					getGridRowFonteAtual = getGridRowFontePadrao;
					getGridRowSetFont(getGridRowFonteAtual);
				},			
				"Mais": function() {
					getGridRowFonteAtual = getGridRowFonteAtual + 2;
					getGridRowSetFont(getGridRowFonteAtual);
				},			
				"Menos": function() {
					getGridRowFonteAtual = getGridRowFonteAtual - 2;
					getGridRowSetFont(getGridRowFonteAtual);
				}
			},			
			
			open: function(event, ui) {
				$('.ui-dialog-buttonpane')
					.find('button:contains("Padrão")')
					.html('<span class="ui-button-text"><img src="<?php echo base_url(); ?>img/grid/font_size_padrao.png" border="0"></span>');				
				$('.ui-dialog-buttonpane')
					.find('button:contains("Mais")')
					.html('<span class="ui-button-text"><img src="<?php echo base_url(); ?>img/grid/font_size_mais.png" border="0"></span>');		
				$('.ui-dialog-buttonpane')
					.find('button:contains("Menos")')
					.html('<span class="ui-button-text"><img src="<?php echo base_url(); ?>img/grid/font_size_menos.png" border="0"></span>');		

				var scrollPosition = [self.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft,
									  self.pageYOffset || document.documentElement.scrollTop  || document.body.scrollTop];
				var html = jQuery('html');
					html.data('scroll-position', scrollPosition);
					html.data('previous-overflow', html.css('overflow'));
					html.css('overflow', 'hidden');
				window.scrollTo(scrollPosition[0], scrollPosition[1]);	

				$("#gridDetalheTab").dialog("option", "position", "center");
				
			},	

			close: function(event, ui) {
				var html = jQuery('html');
				var scrollPosition = html.data('scroll-position');
				html.css('overflow', html.data('previous-overflow'));
				window.scrollTo(scrollPosition[0], scrollPosition[1])				
			}			
		});
		
		$("#gridDetalheTab").tabs("destroy");
		$("#gridDetalheTab").tabs({ 
			selected: col_iniciar
		});
		
		$('.gridDetalheTabItemConteudo').css('height', ($('#gridDetalheTab').height() - $('#gridDetalheTabItens').find("ul").height() - 40) + 'px');

		$('#gridDetalheTab').bind("dialogresize", function(event, ui) {
			console.log("DR => " + $('#gridDetalheTab').height() );
			
			$('.gridDetalheTabItemConteudo').css('height', ($('#gridDetalheTab').height() - $('#gridDetalheTabItens').find("ul").height() - 40) + 'px');
		});		
		
		getGridRowSetFont(getGridRowFontePadrao);
		
		
	
	}

	function getGridRowSetFont(nr_fonte)
	{
		$('.gridDetalheTabItemConteudo').each(function() { 
			$(this).addClass("gridDetalheTabItemConteudo");
			
			$(this).css({		
				fontFamily: "Calibri, Arial",
				fontSize: nr_fonte+"pt",
				fontWeight: "normal",
				color: "#000000",
				textAlign: "justify"	
			});
		});	
		
		$('.gridDetalheTabItemConteudo').children().each(function() { 
			$(this).addClass("gridDetalheTabItemConteudo");
			
			$(this).css({		
				fontFamily: "Calibri, Arial",
				fontSize: nr_fonte+"pt",
				fontWeight: "normal",
				color: "#000000",
				textAlign: "justify"	
			});
		});		

		$('.gridDetalheTabItemConteudo').children().children().each(function() { 
			$(this).addClass("gridDetalheTabItemConteudo");
			
			$(this).css({		
				fontFamily: "Calibri, Arial",
				fontSize: nr_fonte+"pt",
				fontWeight: "normal",
				color: "#000000",
				textAlign: "justify"	
			});
		});			

		$('.gridDetalheTabItemConteudo').children().children().children().each(function() { 
			$(this).addClass("gridDetalheTabItemConteudo");
			
			$(this).css({		
				fontFamily: "Calibri, Arial",
				fontSize: nr_fonte+"pt",
				fontWeight: "normal",
				color: "#000000",
				textAlign: "justify"	
			});
		});	
	}
	
	$.isNotIE = function ()
	{
		var rv = -1;
		if (navigator.appName == 'Microsoft Internet Explorer')
		{
			var ua = navigator.userAgent;
			var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null) rv = parseFloat( RegExp.$1 );
		}
		if (rv == -1) return true;
	};

	function gridWindowShow(titulo, id_texto)
	{
		$('#gridWindowTitulo').html(titulo);
		
		$("#gridWindowTexto").empty();
		$('#gridWindowTexto').html($("#" + id_texto).html());
		
		
		
		$('#gridWindowTexto').children().each(function() { 
			if($(this).attr("id") == (id_texto + "_iconWindow"))
			{
				$(this).remove();
			}
		
			$(this).css({		
				fontFamily: "Calibri, Arial",
				fontSize: "22pt",
				fontWeight: "normal",
				color: "#000000",
				textAlign: "justify"	
			});
		});

		
		gridWindowShowModal();
	}

	function gridWindowShowModal()
	{
		$('#gridWindow').modal({
			focus:false,
			autoResize: true,
			containerCss:{
				width:($(window).width() - (($(window).width() * 5) / 100)),
				height:($(window).height() - (($(window).height() * 10) / 100))
				},
			onClose: function (dialog) {
				$.modal.close();
			}					
		});	

		if ($.isNotIE())
		{
			$("#gridWindowTexto").css({		
				overflowY: "auto",
				paddingRight: 15,
				width:($(window).width() - (($(window).width() * 8) / 100)),
				height:($(window).height() - (($(window).height() * 25) / 100))
			});		
		}
		else
		{
			$("#gridWindowTexto").css({		
				overflowY: "auto",
				paddingRight: 15,
				width:($(window).width() - (($(window).width() * 8) / 100)),
				height:($(window).height() - (($(window).height() * 25) / 100))
			});			
		}
	}

	$(window).resize(function() {
		if($('#gridWindow').is(":visible"))
		{
			$.modal.close();
			gridWindowShowModal();
		}
	
		if($("#gridDetalheTab").dialog("isOpen"))
		{		
			$("#gridDetalheTab").dialog("option", "width", ($(window).width() - 20));
			$("#gridDetalheTab").dialog("option", "height", ($(window).height() - 20));
			$('.gridDetalheTabItemConteudo').css('height', ($('#gridDetalheTab').height() - $('#gridDetalheTabItens').find("ul").height() - 40) + 'px');	
			$("#gridDetalheTab").dialog("option", "position", "center");
		}
	});	
	
	$(window).scroll(function() {
		if($("#gridDetalheTab").dialog("isOpen"))
		{
			//$("#gridDetalheTab").dialog("option", "position", "center");
		}
	});	
</script>
<style>
	/* detalhe TR */
	.gridWindowTab {
		width: 95%;
	}
	
	.gridWindowTab li {
		float: left;
		list-style: none outside none;
	}
	
	.gridWindowTab ul { 
		background:#F4F4F4;
		padding:5px; 
		float:left; 
		
		width: 90%;
		position:fixed;
		margin-top:10px;
	}	
	
	.gridWindowTab ul, .gridWindowTab a { 
		border-radius:4px; 
		-moz-border-radius:4px; 
	}
	
	.gridWindowTab ul a { 
		display:block; 
		background:#F4F4F4;
		color:#777777; 
		padding:0 13px; 
		font:bold 18pt Calibri, Arial, Georgia;
		text-decoration:none; 
	}

	.gridWindowTab a.gridColSelecionada { 
		background:#E4EBF3; 
		color:#4183C4; 
	}

	.gridWindowTab div {
		background: none repeat scroll 0 0 #FFFFFF;
		clear: left;
		font: 26pt Calibri, Arial, Georgia;
		padding: 10px 10px 8px;
		font-weight: bold;
		color: #000000;	
	}

	.gridWindowTabItens > div { 
		display:none; float:left; margin:0.1em 0 0 0.5em; 
	}
	
	.gridWindowTabTexto {
		font-family: Calibri, Arial;
		font-size: 26pt;
		font-weight: normal;
		color: #000000;
		text-align:justify;
		overflow: auto;
	}	
	
	
	/* detalhe TD */
	.gridWindowTitulo {
		font-family: Calibri, Arial;
		font-size: 20pt;
		font-weight: bold;
		color: #000000;
	}
	
	.gridWindowTexto {
		font-family: Calibri, Arial;
		font-size: 26pt;
		font-weight: normal;
		color: #000000;
		text-align:justify;
		overflow: auto;
	}

</style>
<div id="gridWindow" class="basic-modal-content">
	<div id="gridWindowTitulo" class="gridWindowTitulo">
	</div>
	<BR>
	<div id="gridWindowTexto" class="gridWindowTexto">
	</div>
</div>

<style>
	.ui-state-default a {
		font-size: 14pt;
	}
	
	#gridDetalheTab {
		background: #E2E4E5;
	}
	
	#gridDetalheTabConteudo {
		background: #E2E4E5;
	}
	
	.gridDetalheTabItemConteudo {
		overflow: auto;
		height: 300px;
		padding-right: 10px;
		font-family: Calibri, Arial;
		font-size: 26pt;
		font-weight: normal;
		color: #000000;
		text-align:justify;	
		background: #E2E4E5;
	}
</style>
<div id="gridDetalheTab" title="Detalhe" style="display:none;">
	<div id="gridDetalheTabConteudo">
	</div>
</div>

<script>
	function windowPadraoShow()
	{
		$('#windowPadrao').dialog({
			width: $(window).width() - 50,
			height: $(window).height() - 80,
			modal: true
		});	
	}
	
	$(window).scroll(function() {
		if($("#windowPadrao").dialog("isOpen"))
		{
			$("#windowPadrao").dialog("option", "position", "center");
		}
	});		
</script>
<div id="windowPadrao" title="" style="display:none;">
	<div id="windowPadraoConteudo">
	</div>
</div>
<!-- DIV CONTEUDO -->