<?php 
//=======================================================================
// File:	JPGRAPH.PHP
// Description:	PHP4 Graph Plotting library. Base module.
// Created: 	2001-01-08
// Author:	Johan Persson (johanp@aditus.nu)
// Ver:		$Id: jpgraph.php,v 1.242.2.1 2003/05/30 20:41:05 aditus Exp $
//
// License:	This code is released under QPL 1.0 
// Copyright (C) 2001,2002,2003 Johan Persson 
//========================================================================

//------------------------------------------------------------------------
// Directories for cache and font directory.
// Leave them undefined to use default values. 
// 
// Default values used if these defines are left commented out are:
// 
// UNIX: 
//   CACHE_DIR = /tmp/jpgraph_cache/
//   TTF_DIR   = /usr/X11R6/lib/X11/fonts/truetype/
//
// WINDOWS:
//   CACHE_DIR = $SERVER_TEMP/jpgraph_cache/
//   TTF_DIR   = $SERVER_SYSTEMROOT/fonts/
//    
//
//------------------------------------------------------------------------

// The full absolute name of the directory to be used to store the
// cached image files. This directory will not be used if the USE_CACHE
// define (further down) is false. If you enable the cache please note that
// this directory MUST be readable and writable for the process running PHP. 
// Must end with '/'
// DEFINE("CACHE_DIR","/tmp/jpgraph_cache/");

// Directory for jpGraph TTF fonts. Must end with '/'
// DEFINE("TTF_DIR","/usr/X11R6/lib/X11/fonts/truetype/");


//-------------------------------------------------------------------------
// Cache directory specification for use with CSIM graphs that are
// using the cache.
// The directory must be the filesysystem name as seen by PHP
// and the 'http' version must be the same directory but as 
// seen by the HTTP server relative to the 'htdocs' ddirectory. 
// If a relative path is specified it is taken to be relative from where
// the image script is executed.
// Note: The default setting is to create a subdirectory in the 
// directory from where the image script is executed and store all files
// there. As ususal this directory must be writeable by the PHP process.
DEFINE("CSIMCACHE_DIR","csimcache/"); 
DEFINE("CSIMCACHE_HTTP_DIR","csimcache/");

//------------------------------------------------------------------------
// Various JpGraph Settings. Adjust accordingly to your
// preferences. Note that cache functionality is turned off by
// default (Enable by setting USE_CACHE to true)
//------------------------------------------------------------------------

// Deafult graphic format set to "auto" which will automatically
// choose the best available format in the order png,gif,jpg
// (The supported format depends on what your PHP installation supports)
DEFINE("DEFAULT_GFORMAT","auto");

// Should the image be a truecolor image? 
// Note 1: Has only effect with GD 2.0.1 and above.
// Note 2: GD 2.0.1 + PHP 4.0.6 on Win32 crashes when trying to use 
// trucolor. Truecolor support is to be considered alpha since GD 2.x
// is still not considered stable (especially on Win32). 
// Note 3: MUST be enabled to get background images working with GD2
// Note 4: If enabled then truetype fonts will look very ugly with GD 2.0.1
// => You can't have both background images and truetype fonts in the same
// image until these bugs has been fixed in GD 2.01. There is a patch
// available for GD 2.0.1 though. See the README file.
DEFINE('USE_TRUECOLOR',true);

// Specify what version of the GD library is installed.
// If this is set to 'auto' the version will be automatically 
// determined.
// However since determining the library takes ~1ms you can also 
// manually specify the version if you know what version you have. 
// This means that you should 
// set this define to true if you have GD 2.x installed to save 1ms. 
DEFINE("USE_LIBRARY_GD2",'auto');

// Should the cache be used at all? By setting this to false no
// files will be generated in the cache directory.  
// The difference from READ_CACHE being that setting READ_CACHE to
// false will still create the image in the cache directory
// just not use it. By setting USE_CACHE=false no files will even
// be generated in the cache directory.
DEFINE("USE_CACHE",false);

// Should we try to find an image in the cache before generating it? 
// Set this define to false to bypass the reading of the cache and always
// regenerate the image. Note that even if reading the cache is 
// disabled the cached will still be updated with the newly generated
// image. Set also "USE_CACHE" below.
DEFINE("READ_CACHE",true);

// Determine if the error handler should be image based or purely
// text based. Image based makes it easier since the script will
// always return an image even in case of errors.
DEFINE("USE_IMAGE_ERROR_HANDLER",true);

// If the color palette is full should JpGraph try to allocate
// the closest match? If you plan on using background images or
// gradient fills it might be a good idea to enable this.
// If not you will otherwise get an error saying that the color palette is 
// exhausted. The drawback of using approximations is that the colors 
// might not be exactly what you specified. 
// Note1: This does only apply to paletted images, not truecolor 
// images since they don't have the limitations of maximum number
// of colors.
DEFINE("USE_APPROX_COLORS",true);

// Special unicode cyrillic language support
DEFINE("LANGUAGE_CYRILLIC",false);

// If you are setting this config to true the conversion
// will assume that the input text is windows 1251, if
// false it will assume koi8-r
DEFINE("CYRILLIC_FROM_WINDOWS",false);

// Should usage of deprecated functions and parameters give a fatal error?
// (Useful to check if code is future proof.)
DEFINE("ERR_DEPRECATED",true);

// Should the time taken to generate each picture be branded to the lower
// left in corner in each generated image? Useful for performace measurements
// generating graphs
DEFINE("BRAND_TIMING",false);

// What format should be used for the timing string?
DEFINE("BRAND_TIME_FORMAT","(%01.3fs)");

//------------------------------------------------------------------------
// The following constants should rarely have to be changed !
//------------------------------------------------------------------------

// What group should the cached file belong to
// (Set to "" will give the default group for the "PHP-user")
// Please note that the Apache user must be a member of the
// specified group since otherwise it is impossible for Apache
// to set the specified group.
DEFINE("CACHE_FILE_GROUP","wwwadmin");

// What permissions should the cached file have
// (Set to "" will give the default persmissions for the "PHP-user")
DEFINE("CACHE_FILE_MOD",0664);

// Decide if we should use the bresenham circle algorithm or the
// built in Arc(). Bresenham gives better visual apperance of circles 
// but is more CPU intensive and slower then the built in Arc() function
// in GD. Turned off by default for speed
DEFINE("USE_BRESENHAM",false);

// Special file name to indicate that we only want to calc
// the image map in the call to Graph::Stroke() used
// internally from the GetHTMLCSIM() method.
DEFINE("_CSIM_SPECIALFILE","_csim_special_");

// HTTP GET argument that is used with image map
// to indicate to the script to just generate the image
// and not the full CSIM HTML page.
DEFINE("_CSIM_DISPLAY","_jpg_csimd");

// Special filename for Graph::Stroke(). If this filename is given
// then the image will NOT be streamed to browser of file. Instead the
// Stroke call will return the handler for the created GD image.
DEFINE("_IMG_HANDLER","__handle");

// DON'T SET THIS FLAG YORSELF THIS IS ONLY FOR INTERNAL TESTING
// PURPOSES. ENABLING THIS FLAG WILL MAKE SOME OF YOUR SCRIPT 
// STOP WORKING
// Enable some extra debug information for CSIM etc to be shown. 
DEFINE("JPG_DEBUG",false);

// Version info
DEFINE('JPG_VERSION','1.12');

//------------------------------------------------------------------------
// Automatic settings of path for cache and font directory
// if they have not been previously specified
//------------------------------------------------------------------------
if (!defined('CACHE_DIR')) {
    if ( strstr( PHP_OS, 'WIN') ) {
        if( empty($_SERVER['TEMP']) ) {
	    die('JpGraph Error: No path specified for CACHE_DIR. Please specify a path for that DEFINE in jpgraph.php');
        }
	else {
	   DEFINE('CACHE_DIR', $_SERVER['TEMP'] . '/');
        }
    } else {
	DEFINE('CACHE_DIR','/tmp/jpgraph_cache/');
    }
}

if (!defined('TTF_DIR')) {
    if (strstr( PHP_OS, 'WIN') ) {
        if( empty($_SERVER['SystemRoot']) ) {
	    die('JpGraph Error: No path specified for TTF_DIR. Please specify a path for that DEFINE in jpgraph.php');
        }
	else {
	  DEFINE('TTF_DIR', $_SERVER['SystemRoot'] . '/fonts/');
        }
    } else {
//	DEFINE('TTF_DIR','/usr/X11R6/lib/X11/fonts/truetype/');
	DEFINE('TTF_DIR','/u/www/controle_projetos/inc/');
    }
}

//------------------------------------------------------------------
// Constants which are used as parameters for the method calls
//------------------------------------------------------------------

// TTF Font families
DEFINE("FF_COURIER",10);
DEFINE("FF_VERDANA",11);
DEFINE("FF_TIMES",12);
DEFINE("FF_COMIC",14);
DEFINE("FF_ARIAL",15);
DEFINE("FF_GEORGIA",16);
DEFINE("FF_TREBUCHE",17);

// Chinese font
DEFINE("FF_SIMSUN",18);

// Older deprecated fonts 
DEFINE("FF_BOOK",91);    // Deprecated fonts from 1.9
DEFINE("FF_HANDWRT",92); // Deprecated fonts from 1.9

// TTF Font styles
DEFINE("FS_NORMAL",9001);
DEFINE("FS_BOLD",9002);
DEFINE("FS_ITALIC",9003);
DEFINE("FS_BOLDIT",9004);
DEFINE("FS_BOLDITALIC",9004);

//Definitions for internal font, new style
DEFINE("FF_FONT0",1);
DEFINE("FF_FONT1",2);
DEFINE("FF_FONT2",4);

//Definitions for internal font, old style
// (Only defined here to be able to generate an error mesage
// when used)
DEFINE("FONT0",99);		// Deprecated from 1.2
DEFINE("FONT1",98);		// Deprecated from 1.2
DEFINE("FONT1_BOLD",97);	// Deprecated from 1.2
DEFINE("FONT2",96);		// Deprecated from 1.2
DEFINE("FONT2_BOLD",95); 	// Deprecated from 1.2

// Tick density
DEFINE("TICKD_DENSE",1);
DEFINE("TICKD_NORMAL",2);
DEFINE("TICKD_SPARSE",3);
DEFINE("TICKD_VERYSPARSE",4);

// Side for ticks and labels. 
DEFINE("SIDE_LEFT",-1);
DEFINE("SIDE_RIGHT",1);
DEFINE("SIDE_DOWN",-1);
DEFINE("SIDE_BOTTOM",-1);
DEFINE("SIDE_UP",1);
DEFINE("SIDE_TOP",1);

// Legend type stacked vertical or horizontal
DEFINE("LEGEND_VERT",0);
DEFINE("LEGEND_HOR",1);

// Mark types for plot marks
DEFINE("MARK_SQUARE",1);
DEFINE("MARK_UTRIANGLE",2);
DEFINE("MARK_DTRIANGLE",3);
DEFINE("MARK_DIAMOND",4);
DEFINE("MARK_CIRCLE",5);
DEFINE("MARK_FILLEDCIRCLE",6);
DEFINE("MARK_CROSS",7);
DEFINE("MARK_STAR",8);
DEFINE("MARK_X",9);
DEFINE("MARK_LEFTTRIANGLE",10);
DEFINE("MARK_RIGHTTRIANGLE",11);
DEFINE("MARK_FLASH",12);
DEFINE("MARK_IMG",13);

// Builtin images
DEFINE("MARK_IMG_PUSHPIN",50);
DEFINE("MARK_IMG_SPUSHPIN",50);
DEFINE("MARK_IMG_LPUSHPIN",51);
DEFINE("MARK_IMG_DIAMOND",52);
DEFINE("MARK_IMG_SQUARE",53);
DEFINE("MARK_IMG_STAR",54);
DEFINE("MARK_IMG_BALL",55);
DEFINE("MARK_IMG_SBALL",55);
DEFINE("MARK_IMG_MBALL",56);
DEFINE("MARK_IMG_LBALL",57);
DEFINE("MARK_IMG_BEVEL",58);

// Styles for gradient color fill
DEFINE("GRAD_VER",1);
DEFINE("GRAD_VERT",1);
DEFINE("GRAD_HOR",2);
DEFINE("GRAD_MIDHOR",3);
DEFINE("GRAD_MIDVER",4);
DEFINE("GRAD_CENTER",5);
DEFINE("GRAD_WIDE_MIDVER",6);
DEFINE("GRAD_WIDE_MIDHOR",7);
DEFINE("GRAD_LEFT_REFLECTION",8);
DEFINE("GRAD_RIGHT_REFLECTION",9);

// Inline defines
DEFINE("INLINE_YES",1);
DEFINE("INLINE_NO",0);

// Format for background images
DEFINE("BGIMG_FILLPLOT",1);
DEFINE("BGIMG_FILLFRAME",2);
DEFINE("BGIMG_COPY",3);
DEFINE("BGIMG_CENTER",4);

// Depth of objects
DEFINE("DEPTH_BACK",0);
DEFINE("DEPTH_FRONT",1);

// Direction
DEFINE("VERTICAL",1);
DEFINE("HORIZONTAL",0);

// Constants for types of static bands in plot area
DEFINE("BAND_RDIAG",1);	// Right diagonal lines
DEFINE("BAND_LDIAG",2); // Left diagonal lines
DEFINE("BAND_SOLID",3); // Solid one color
DEFINE("BAND_VLINE",4); // Vertical lines
DEFINE("BAND_HLINE",5);  // Horizontal lines
DEFINE("BAND_3DPLANE",6);  // "3D" Plane
DEFINE("BAND_HVCROSS",7);  // Vertical/Hor crosses
DEFINE("BAND_DIAGCROSS",8); // Diagonal crosses

// Axis styles for scientific style axis
DEFINE('AXSTYLE_SIMPLE',1);
DEFINE('AXSTYLE_BOXIN',2);
DEFINE('AXSTYLE_BOXOUT',3);
DEFINE('AXSTYLE_YBOXIN',4);
DEFINE('AXSTYLE_YBOXOUT',5);

// Style for title backgrounds
DEFINE('TITLEBKG_STYLE1',1);
DEFINE('TITLEBKG_STYLE2',2);
DEFINE('TITLEBKG_STYLE3',3);
DEFINE('TITLEBKG_FRAME_NONE',0);
DEFINE('TITLEBKG_FRAME_FULL',1);
DEFINE('TITLEBKG_FRAME_BOTTOM',2);
DEFINE('TITLEBKG_FRAME_BEVEL',3);
DEFINE('TITLEBKG_FILLSTYLE_HSTRIPED',1);
DEFINE('TITLEBKG_FILLSTYLE_VSTRIPED',2);
DEFINE('TITLEBKG_FILLSTYLE_SOLID',3);

// Width of tab titles
DEFINE('TABTITLE_WIDTHFIT',0);
DEFINE('TABTITLE_WIDTHFULL',-1);

//
// Get hold of gradient class (In Version 2.x)
// A client of the library has to manually include this
//
include "jpgraph_gradient.php";

//
// First of all set up a default error handler
//

//=============================================================
// The default trivial text error handler.
//=============================================================
class JpGraphErrObject {
    function JpGraphErrObject() {
	// Empty. Reserved for future use
    }

    // If aHalt is true then execution can't continue. Typical used for
    // fatal errors
    function Raise($aMsg,$aHalt=true) {
	$aMsg = "<b>JpGraph Error:</b> ".$aMsg;
	if( $aHalt )
	    die($aMsg);
	else 
	    echo $aMsg."<p>";
    }
}

//==============================================================
// An image based error handler
//==============================================================
class JpGraphErrObjectImg {

    function Raise($aMsg,$aHalt=true) {
	if( headers_sent() ) {
	    // Special case for headers already sent error. Dont
	    // return an image since it can't be displayed
	    die("<b>JpGraph Error:</b> ".$aMsg);		
	}

	// Create an image that contains the error text.
	$w=450; $h=110;
	$img = new Image($w,$h);
	$img->SetColor("darkred");
	$img->Rectangle(0,0,$w-1,$h-1);
	$img->SetFont(FF_FONT1,FS_BOLD);
	$img->StrokeText(10,20,"JpGraph Error:");
	$img->SetColor("black");
	$img->SetFont(FF_FONT1,FS_NORMAL);
	$txt = new Text(wordwrap($aMsg,70),10,20);
	$txt->Align("left","top");
	$txt->Stroke($img);
	$img->Headers();
	$img->Stream();
	die();
    }
}

//
// A wrapper class that is used to access the specified error object
// (to hide the global error parameter and avoid having a GLOBAL directive
// in all methods.
//
class JpGraphError {
    function Install($aErrObject) {
	GLOBAL $__jpg_err;
	$__jpg_err = $aErrObject;
    }
    function Raise($aMsg,$aHalt=true){
	GLOBAL $__jpg_err;
	$tmp = new $__jpg_err;
	$tmp->Raise($aMsg,$aHalt);
    }
}

//
// ... and install the default error handler
//
if( USE_IMAGE_ERROR_HANDLER ) {
    JpGraphError::Install("JpGraphErrObjectImg");
}
else {
    JpGraphError::Install("JpGraphErrObject");
}


//
//Check if there were any warnings, perhaps some wrong includes by the
//user
//
if( isset($GLOBALS['php_errormsg']) ) {
    JpGraphError::Raise("<b>General PHP error:</b><br>".$GLOBALS['php_errormsg']);
}


//
// Routine to determine if GD1 or GD2 is installed
//
function CheckGDVersion() {
    ob_start();
    phpinfo(8); // Just get the modules loaded
    $a = ob_get_contents();
    ob_end_clean();
    if( preg_match('/.*GD Version.*(1\.).*/',$a,$m) ) {
	$r=1;$v=$m[1];
    }
    elseif( preg_match('/.*GD Version.*(2\.).*/',$a,$m) ) {
	$r=2;$v=$m[1];
    }
    else {
	$r=0;$v=$m[1];
    }
    return $r;
}

//
// Check what version of the GD library is installed.
//
if( USE_LIBRARY_GD2 === 'auto' ) {
    $gdversion = CheckGDVersion();
    if( $gdversion == 2 ) {
	$GLOBALS['gd2'] = true;
	$GLOBALS['copyfunc'] = 'imagecopyresampled';
    }
    elseif( $gdversion == 1 ) {
	$GLOBALS['gd2'] = false;
	$GLOBALS['copyfunc'] = 'imagecopyresized';
    }
    else {
	JpGraphError::Raise(" Your PHP installation does not seem to 
	have the required GD library.
	Please see the PHP documentation on how to install and enable the GD library.");
    }
}
else {
    $GLOBALS['gd2'] = USE_LIBRARY_GD2;
    $GLOBALS['copyfunc'] = USE_LIBRARY_GD2 ? 'imagecopyresampled' : 'imagecopyresized';
}

// Usefull mathematical function
function sign($a) {return $a >= 0 ? 1 : -1;}

// Utility function to generate an image name based on the filename we
// are running from and assuming we use auto detection of graphic format
// (top level), i.e it is safe to call this function
// from a script that uses JpGraph
function GenImgName() {
    global $HTTP_SERVER_VARS;
    $supported = imagetypes();
    if( $supported & IMG_PNG )
	$img_format="png";
    elseif( $supported & IMG_GIF )
	$img_format="gif";
    elseif( $supported & IMG_JPG )
	$img_format="jpeg";
    if( !isset($HTTP_SERVER_VARS['PHP_SELF']) )
	JpGraphError::Raise(" Can't access PHP_SELF, PHP global variable. You can't run PHP from command line
		if you want to use the 'auto' naming of cache or image files.");
    $fname=basename($HTTP_SERVER_VARS['PHP_SELF']);
    // Replace the ".php" extension with the image format extension
    return substr($fname,0,strlen($fname)-4).".".$img_format;
}

class LanguageConv {
    var $g2312 = null ;

    function Convert($aTxt,$aFF) {
	if( LANGUAGE_CYRILLIC ) {
	    if( CYRILLIC_FROM_WINDOWS ) {
		$aTxt = convert_cyr_string($aTxt, "w", "k"); 
	    }
	    $isostring = convert_cyr_string($aTxt, "k", "i");
	    $unistring = LanguageConv::iso2uni($isostring);
	    return $unistring;
	}
	elseif( $aFF === FF_SIMSUN ) {
	    // Do Chinese conversion
	    if( $this->g2312 == null ) {
		include_once 'jpgraph_gb2312.php' ;
		$this->g2312 = new GB2312toUTF8();
	    }
	    return $this->g2312->gb2utf8($aTxt);
	}
	else 
	    return $aTxt;
    }

    // Translate iso encoding to unicode
    function iso2uni ($isoline){
	for ($i=0; $i < strlen($isoline); $i++){
	    $thischar=substr($isoline,$i,1);
	    $charcode=ord($thischar);
	    $uniline.=($charcode>175) ? "&#" . (1040+($charcode-176)). ";" : $thischar;
	}
	return $uniline;
    }
}

//===================================================
// CLASS JpgTimer
// Description: General timing utility class to handle
// timne measurement of generating graphs. Multiple
// timers can be started by pushing new on a stack.
//===================================================
class JpgTimer {
    var $start;
    var $idx;	
//---------------
// CONSTRUCTOR
    function JpgTimer() {
	$this->idx=0;
    }

//---------------
// PUBLIC METHODS	

    // Push a new timer start on stack
    function Push() {
	list($ms,$s)=explode(" ",microtime());	
	$this->start[$this->idx++]=floor($ms*1000) + 1000*$s;	
    }

    // Pop the latest timer start and return the diff with the
    // current time
    function Pop() {
	assert($this->idx>0);
	list($ms,$s)=explode(" ",microtime());	
	$etime=floor($ms*1000) + (1000*$s);
	$this->idx--;
	return $etime-$this->start[$this->idx];
    }
} // Class

$gJpgBrandTiming = BRAND_TIMING;
//===================================================
// CLASS DateLocale
// Description: Hold localized text used in dates
// ToDOo: Rewrite this to use the real local locale
// instead.
//===================================================
class DateLocale {
 
    var $iLocale = 'C'; // environmental locale be used by default

    var $iDayAbb = null;
    var $iShortDay = null;
    var $iShortMonth = null;
    var $iMonthName = null;

//---------------
// CONSTRUCTOR	
    function DateLocale() {
	settype($this->iDayAbb, 'array');
	settype($this->iShortDay, 'array');
	settype($this->iShortMonth, 'array');
	settype($this->iMonthName, 'array');


	$this->Set('C');
    }

//---------------
// PUBLIC METHODS	
    function Set($aLocale) {
	if ( in_array($aLocale, array_keys($this->iDayAbb)) ){ 
	    $this->iLocale = $aLocale;
	    return TRUE;  // already cached nothing else to do!
	}

	$pLocale = setlocale(LC_TIME, 0); // get current locale for LC_TIME
	$res = setlocale(LC_TIME, $aLocale);
	if ( ! $res ){
	    JpGraphError::Raise("You are trying to use the locale ($aLocale) which your PHP installation does not support. Hint: Use '' to indicate the default locale for this geographic region.");
	    return FALSE;
	}
 
	$this->iLocale = $aLocale;

	for ( $i = 0, $ofs = 0 - strftime('%w'); $i < 7; $i++, $ofs++ ){
	    $day = strftime('%a', strtotime("$ofs day"));
	    $day{0} = strtoupper($day{0});
	    $this->iDayAbb[$aLocale][]= $day{0};
	    $this->iShortDay[$aLocale][]= $day;
	}

	for($i=1; $i<=12; ++$i) {
	    list($short ,$full) = explode('|', strftime("%b|%B",strtotime("2001-$i-01")));
	    $this->iShortMonth[$aLocale][] = ucfirst($short);
	    $this->iMonthName [$aLocale][] = ucfirst($full);
	}
	
	
	setlocale(LC_TIME, $pLocale);

	return TRUE;
    }


    function GetDayAbb() {
	return $this->iDayAbb[$this->iLocale];
    }
	
    function GetShortDay() {
	return $this->iShortDay[$this->iLocale];
    }

    function GetShortMonth() {
	return $this->iShortMonth[$this->iLocale];
    }
	
    function GetShortMonthName($aNbr) {
	return $this->iShortMonth[$this->iLocale][$aNbr];
    }

    function GetLongMonthName($aNbr) {
	return $this->iMonthName[$this->iLocale][$aNbr];
    }

    function GetMonth() {
	return $this->iMonthName[$this->iLocale];
    }
}

$gDateLocale = new DateLocale();
$gJpgDateLocale = new DateLocale();


//===================================================
// CLASS FuncGenerator
// Description: Utility class to help generate data for function plots. 
// The class supports both parametric and regular functions.
//===================================================
class FuncGenerator {
    var $iFunc='',$iXFunc='',$iMin,$iMax,$iStepSize;
	
    function FuncGenerator($aFunc,$aXFunc='') {
	$this->iFunc = $aFunc;
	$this->iXFunc = $aXFunc;
    }
	
    function E($aXMin,$aXMax,$aSteps=50) {
	$this->iMin = $aXMin;
	$this->iMax = $aXMax;
	$this->iStepSize = ($aXMax-$aXMin)/$aSteps;

	if( $this->iXFunc != '' )
	    $t = 'for($i='.$aXMin.'; $i<='.$aXMax.'; $i += '.$this->iStepSize.') {$ya[]='.$this->iFunc.';$xa[]='.$this->iXFunc.';}';
	elseif( $this->iFunc != '' )
	    $t = 'for($x='.$aXMin.'; $x<='.$aXMax.'; $x += '.$this->iStepSize.') {$ya[]='.$this->iFunc.';$xa[]=$x;} $x='.$aXMax.';$ya[]='.$this->iFunc.';$xa[]=$x;';
	else
	    JpGraphError::Raise('FuncGenerator : No function specified. ');
			
	@eval($t);
		
	// If there is an error in the function specifcation this is the only
	// way we can discover that.
	if( empty($xa) || empty($ya) )
	    JpGraphError::Raise('FuncGenerator : Syntax error in function specification ');
				
	return array($xa,$ya);
    }
}


//=======================================================
// CLASS Footer
// Description: Encapsulates the footer line in the Graph
//
//=======================================================
class Footer {
    var $left,$center,$right;
    var $iLeftMargin = 3;
    var $iRightMargin = 3;
    var $iBottomMargin = 3;

    function Footer() {
	$this->left = new Text();
	$this->left->ParagraphAlign('left');
	$this->center = new Text();
	$this->center->ParagraphAlign('center');
	$this->right = new Text();
	$this->right->ParagraphAlign('right');
    }

    function Stroke($aImg) {
	$y = $aImg->height - $this->iBottomMargin;
	$x = $this->iLeftMargin;
	$this->left->Align('left','bottom');
	$this->left->Stroke($aImg,$x,$y);

	$x = ($aImg->width - $this->iLeftMargin - $this->iRightMargin)/2;
	$this->center->Align('center','bottom');
	$this->center->Stroke($aImg,$x,$y);

	$x = $aImg->width - $this->iRightMargin;
	$this->right->Align('right','bottom');
	$this->right->Stroke($aImg,$x,$y);
    }
}

    DEFINE('BGRAD_FRAME',1);
    DEFINE('BGRAD_MARGIN',2);
    DEFINE('BGRAD_PLOT',3);



//===================================================
// CLASS Graph
// Description: Main class to handle graphs
//===================================================
class Graph {
    var $cache=null;		// Cache object (singleton)
    var $img=null;			// Img object (singleton)
    var $plots=array();	// Array of all plot object in the graph (for Y 1 axis)
    var $y2plots=array();// Array of all plot object in the graph (for Y 2 axis)
    var $xscale=null;		// X Scale object (could be instance of LinearScale or LogScale
    var $yscale=null,$y2scale=null;
    var $cache_name;		// File name to be used for the current graph in the cache directory
    var $xgrid=null;		// X Grid object (linear or logarithmic)
    var $ygrid=null,$y2grid=null; //dito for Y
    var $doframe=true,$frame_color=array(0,0,0), $frame_weight=1;	// Frame around graph
    var $boxed=false, $box_color=array(0,0,0), $box_weight=1;		// Box around plot area
    var $doshadow=false,$shadow_width=4,$shadow_color=array(102,102,102);	// Shadow for graph
    var $xaxis=null;		// X-axis (instane of Axis class)
    var $yaxis=null, $y2axis=null;	// Y axis (instance of Axis class)
    var $margin_color=array(200,200,200);	// Margin color of graph
    var $plotarea_color=array(255,255,255);	// Plot area color
    var $title,$subtitle,$subsubtitle; 	// Title and subtitle(s) text object
    var $axtype="linlin";	// Type of axis
    var $xtick_factor;	// Factot to determine the maximum number of ticks depending on the plot with
    var $texts=null;		// Text object to ge shown in the graph
    var $lines=null;
    var $bands=null;
    var $text_scale_off=0;	// Text scale offset in world coordinates
    var $background_image="",$background_image_type=-1,$background_image_format="png";
    var $background_image_bright=0,$background_image_contr=0,$background_image_sat=0;
    var $image_bright=0, $image_contr=0, $image_sat=0;
    var $inline;
    var $showcsim=0,$csimcolor="red"; //debug stuff, draw the csim boundaris on the image if <>0
    var $grid_depth=DEPTH_BACK;	// Draw grid under all plots as default
    var $iAxisStyle = AXSTYLE_SIMPLE;
    var $iCSIMdisplay=false,$iHasStroked = false;
    var $footer;
    var $csimcachename = '', $csimcachetimeout = 0;
    var $iDoClipping = false;
    var $y2orderback=true;
    var $tabtitle;
    var $bkg_gradtype=-1,$bkg_gradstyle=BGRAD_MARGIN;
    var $bkg_gradfrom='navy', $bkg_gradto='silver';
    var $titlebackground = false;
    var	$titlebackground_color = 'lightblue',
	$titlebackground_style = 1,
	$titlebackground_framecolor = 'blue',
	$titlebackground_framestyle = 2,
	$titlebackground_frameweight = 1,
	$titlebackground_bevelheight = 3 ;
    var $titlebkg_fillstyle=TITLEBKG_FILLSTYLE_SOLID;
    var $titlebkg_scolor1='black',$titlebkg_scolor2='white';
    var $framebevel = false, $framebeveldepth = 2 ;
    var $framebevelborder = false, $framebevelbordercolor='black';
    var $framebevelcolor1='white@0.4', $framebevelcolor2='black@0.4';

//---------------
// CONSTRUCTOR

    // aWIdth 		Width in pixels of image
    // aHeight  	Height in pixels of image
    // aCachedName	Name for image file in cache directory 
    // aTimeOut		Timeout in minutes for image in cache
    // aInline		If true the image is streamed back in the call to Stroke()
    //			If false the image is just created in the cache
    function Graph($aWidth=300,$aHeight=200,$aCachedName="",$aTimeOut=0,$aInline=true) {
	GLOBAL $gJpgBrandTiming;
	// If timing is used create a new timing object
	if( $gJpgBrandTiming ) {
	    global $tim;
	    $tim = new JpgTimer();
	    $tim->Push();
	}
		
	// Automatically generate the image file name based on the name of the script that
	// generates the graph
	if( $aCachedName=="auto" )
	    $aCachedName=GenImgName();
			
	// Should the image be streamed back to the browser or only to the cache?
	$this->inline=$aInline;
		
	$this->img	= new RotImage($aWidth,$aHeight);

	$this->cache 	= new ImgStreamCache($this->img);
	$this->cache->SetTimeOut($aTimeOut);

	$this->title = new Text();
	$this->title->ParagraphAlign('center');
	$this->title->SetFont(FF_FONT2,FS_BOLD);
	$this->title->SetMargin(3);

	$this->subtitle = new Text();
	$this->subtitle->ParagraphAlign('center');

	$this->subsubtitle = new Text();
	$this->subsubtitle->ParagraphAlign('center');

	$this->legend = new Legend();
	$this->footer = new Footer();

	// If the cached version exist just read it directly from the
	// cache, stream it back to browser and exit
	if( $aCachedName!="" && READ_CACHE && $aInline )
	    if( $this->cache->GetAndStream($aCachedName) ) {
		exit();
	    }
				
	$this->cache_name = $aCachedName;
	$this->SetTickDensity(); // Normal density

	$this->tabtitle = new GraphTabTitle();
    }
//---------------
// PUBLIC METHODS	

    // Should the grid be in front or back of the plot?
    function SetGridDepth($aDepth) {
	$this->grid_depth=$aDepth;
    }
	
    // Specify graph angle 0-360 degrees.
    function SetAngle($aAngle) {
	$this->img->SetAngle($aAngle);
    }

    function SetAlphaBlending($aFlg=true) {
	$this->img->SetAlphaBlending($aFlg);
    }

    // Shortcut to image margin
    function SetMargin($lm,$rm,$tm,$bm) {
	$this->img->SetMargin($lm,$rm,$tm,$bm);
    }

    function SetY2OrderBack($aBack=true) {
	$this->y2orderback = $aBack;
    }

    // Rotate the graph 90 degrees and set the margin 
    // when we have done a 90 degree rotation
    function Set90AndMargin($lm=0,$rm=0,$tm=0,$bm=0) {
	$lm = $lm ==0 ? floor(0.2 * $this->img->width)  : $lm ;
	$rm = $rm ==0 ? floor(0.1 * $this->img->width)  : $rm ;
	$tm = $tm ==0 ? floor(0.2 * $this->img->height) : $tm ;
	$bm = $bm ==0 ? floor(0.1 * $this->img->height) : $bm ;

	$adj = ($this->img->height - $this->img->width)/2;
	$this->img->SetMargin($tm-$adj,$bm-$adj,$rm+$adj,$lm+$adj);
	$this->img->SetCenter(floor($this->img->width/2),floor($this->img->height/2));
	$this->SetAngle(90);
	$this->xaxis->SetLabelAlign('right','center');
	$this->yaxis->SetLabelAlign('center','bottom');
    }
	
    function SetClipping($aFlg=true) {
	$this->iDoClipping = $aFlg ;
    }

    // Add a plot object to the graph
    function Add(&$aPlot) {
	if( $aPlot == null )
	    JpGraphError::Raise("<b></b> Graph::Add() You tried to add a null plot to the graph.");
	if( is_array($aPlot) && count($aPlot) > 0 )
	    $cl = get_class($aPlot[0]);
	else
	    $cl = get_class($aPlot);

	if( $cl == 'text' ) 
	    $this->AddText($aPlot);
	elseif( $cl == 'plotline' )
	    $this->AddLine($aPlot);
	elseif( $cl == 'plotband' )
	    $this->AddBand($aPlot);
	else
	    $this->plots[] = &$aPlot;
    }

    // Add plot to second Y-scale
    function AddY2(&$aPlot) {
	if( $aPlot == null )
	    JpGraphError::Raise("<b></b> Graph::AddY2() You tried to add a null plot to the graph.");	
	$this->y2plots[] = &$aPlot;
    }
	
    // Add text object to the graph
    function AddText(&$aTxt) {
	if( $aTxt == null )
	    JpGraphError::Raise("<b></b> Graph::AddText() You tried to add a null text to the graph.");		
	if( is_array($aTxt) ) {
	    for($i=0; $i < count($aTxt); ++$i )
		$this->texts[]=&$aTxt[$i];
	}
	else
	    $this->texts[] = &$aTxt;
    }
	
    // Add a line object (class PlotLine) to the graph
    function AddLine(&$aLine) {
	if( $aLine == null )
	    JpGraphError::Raise("<b></b> Graph::AddLine() You tried to add a null line to the graph.");		
	if( is_array($aLine) ) {
	    for($i=0; $i<count($aLine); ++$i )
		$this->lines[]=&$aLine[$i];
	}
	else
	    $this->lines[] = &$aLine;
    }

    // Add vertical or horizontal band
    function AddBand(&$aBand) {
	if( $aBand == null )
	    JpGraphError::Raise(" Graph::AddBand() You tried to add a null band to the graph.");
	if( is_array($aBand) ) {
	    for($i=0; $i<count($aBand); ++$i )
		$this->bands[] = &$aBand[$i];
	}
	else
	    $this->bands[] = &$aBand;
    }

    function SetBackgroundGradient($aFrom='navy',$aTo='silver',$aGradType=GRAD_HOR,$aStyle=BGRAD_FRAME) {
	$this->bkg_gradtype=$aGradType;
	$this->bkg_gradstyle=$aStyle;
	$this->bkg_gradfrom = $aFrom;
	$this->bkg_gradto = $aTo;
    } 
	
    // Specify a background image
    function SetBackgroundImage($aFileName,$aBgType=BGIMG_FILLPLOT,$aImgFormat="auto") {

	if( $GLOBALS['gd2'] && !USE_TRUECOLOR ) {
	    JpGraphError::Raise("You are using GD 2.x and are trying to use a background images on a non truecolor image. To use background images with GD 2.x you <b>must</b> enable truecolor by setting the USE_TRUECOLOR constant to TRUE. Due to a bug in GD 2.0.1 using any truetype fonts with truecolor images will result in very poor quality fonts.");
	}

	// Get extension to determine image type
	if( $aImgFormat == "auto" ) {
	    $e = explode('.',$aFileName);
	    if( !$e ) {
		JpGraphError::Raise('Incorrect file name for Graph::SetBackgroundImage() : '.$aFileName.' Must have a valid image extension (jpg,gif,png) when using autodetection of image type');
	    }

	    $valid_formats = array('png', 'jpg', 'gif');
	    $aImgFormat = strtolower($e[count($e)-1]);
	    if ($aImgFormat == 'jpeg')  {
		$aImgFormat = 'jpg';
	    }
	    elseif (!in_array($aImgFormat, $valid_formats) )  {
		JpGraphError::Raise('Unknown file extension ($aImgFormat) in Graph::SetBackgroundImage() for filename: '.$aFileName);
	    }    
	}

	$this->background_image = $aFileName;
	$this->background_image_type=$aBgType;
	$this->background_image_format=$aImgFormat;
    }
	
    // Adjust brightness and constrast for background image
    function AdjBackgroundImage($aBright,$aContr=0,$aSat=0) {
	$this->background_image_bright=$aBright;
	$this->background_image_contr=$aContr;
	$this->background_image_sat=$aSat;
    }
	
    // Adjust brightness and constrast for image
    function AdjImage($aBright,$aContr=0,$aSat=0) {
	$this->image_bright=$aBright;
	$this->image_contr=$aContr;
	$this->image_sat=$aSat;
    }

    // Specify axis style (boxed or single)
    function SetAxisStyle($aStyle) {
        $this->iAxisStyle = $aStyle ;
    }
	
    // Set a frame around the plot area
    function SetBox($aDrawPlotFrame=true,$aPlotFrameColor=array(0,0,0),$aPlotFrameWeight=1) {
	$this->boxed = $aDrawPlotFrame;
	$this->box_weight = $aPlotFrameWeight;
	$this->box_color = $aPlotFrameColor;
    }
	
    // Specify color for the plotarea (not the margins)
    function SetColor($aColor) {
	$this->plotarea_color=$aColor;
    }
	
    // Specify color for the margins (all areas outside the plotarea)
    function SetMarginColor($aColor) {
	$this->margin_color=$aColor;
    }
	
    // Set a frame around the entire image
    function SetFrame($aDrawImgFrame=true,$aImgFrameColor=array(0,0,0),$aImgFrameWeight=1) {
	$this->doframe = $aDrawImgFrame;
	$this->frame_color = $aImgFrameColor;
	$this->frame_weight = $aImgFrameWeight;
    }

    function SetFrameBevel($aDepth=3,$aBorder=false,$aBorderColor='black',$aColor1='white@0.4',$aColor2='darkgray@0.4',$aFlg=true) {
	$this->framebevel = $aFlg ;
	$this->framebeveldepth = $aDepth ;
	$this->framebevelborder = $aBorder ;
	$this->framebevelbordercolor = $aBorderColor ;
	$this->framebevelcolor1 = $aColor1 ;
	$this->framebevelcolor2 = $aColor2 ;

	$this->doshadow = false ;
    }

    // Set the shadow around the whole image
    function SetShadow($aShowShadow=true,$aShadowWidth=5,$aShadowColor=array(102,102,102)) {
	$this->doshadow = $aShowShadow;
	$this->shadow_color = $aShadowColor;
	$this->shadow_width = $aShadowWidth;
	$this->footer->iBottomMargin += $aShadowWidth;
	$this->footer->iRightMargin += $aShadowWidth;
    }

    // Specify x,y scale. Note that if you manually specify the scale
    // you must also specify the tick distance with a call to Ticks::Set()
    function SetScale($aAxisType,$aYMin=1,$aYMax=1,$aXMin=1,$aXMax=1) {
	$this->axtype = $aAxisType;

	if( $aYMax < $aYMin || $aXMax < $aXMin )
	    JpGraphError::Raise('Graph::SetScale(): Specified Max value must be larger than the specified Min value.');

	$yt=substr($aAxisType,-3,3);
	if( $yt=="lin" )
	    $this->yscale = new LinearScale($aYMin,$aYMax);
	elseif( $yt == "int" ) {
	    $this->yscale = new LinearScale($aYMin,$aYMax);
	    $this->yscale->SetIntScale();
	}
	elseif( $yt=="log" )
	    $this->yscale = new LogScale($aYMin,$aYMax);
	else
	    JpGraphError::Raise("Unknown scale specification for Y-scale. ($aAxisType)");
			
	$xt=substr($aAxisType,0,3);
	if( $xt == "lin" || $xt == "tex" ) {
	    $this->xscale = new LinearScale($aXMin,$aXMax,"x");
	    $this->xscale->textscale = ($xt == "tex");
	}
	elseif( $xt == "int" ) {
	    $this->xscale = new LinearScale($aXMin,$aXMax,"x");
	    $this->xscale->SetIntScale();
	}
	elseif( $xt == "log" )
	    $this->xscale = new LogScale($aXMin,$aXMax,"x");
	else
	    JpGraphError::Raise(" Unknown scale specification for X-scale. ($aAxisType)");

	$this->xscale->Init($this->img);
	$this->yscale->Init($this->img);						
					
	$this->xaxis = new Axis($this->img,$this->xscale);
	$this->yaxis = new Axis($this->img,$this->yscale);
	$this->xgrid = new Grid($this->xaxis);
	$this->ygrid = new Grid($this->yaxis);	
	$this->ygrid->Show();			
    }
	
    // Specify secondary Y scale
    function SetY2Scale($aAxisType="lin",$aY2Min=1,$aY2Max=1) {
	if( $aAxisType=="lin" ) 
	    $this->y2scale = new LinearScale($aY2Min,$aY2Max);
	elseif( $aAxisType == "int" ) {
	    $this->y2scale = new LinearScale($aY2Min,$aY2Max);
	    $this->y2scale->SetIntScale();
	}
	elseif( $aAxisType=="log" ) {
	    $this->y2scale = new LogScale($aY2Min,$aY2Max);
	}
	else JpGraphError::Raise("JpGraph: Unsupported Y2 axis type: $axtype<br>");
			
	$this->y2scale->Init($this->img);	
	$this->y2axis = new Axis($this->img,$this->y2scale);
	$this->y2axis->scale->ticks->SetDirection(SIDE_LEFT); 
	$this->y2axis->SetLabelSide(SIDE_RIGHT); 
		
	// Deafult position is the max x-value
	$this->y2grid = new Grid($this->y2axis);							
    }
	
    // Specify density of ticks when autoscaling 'normal', 'dense', 'sparse', 'verysparse'
    // The dividing factor have been determined heuristically according to my aesthetic 
    // sense (or lack off) y.m.m.v !
    function SetTickDensity($aYDensity=TICKD_NORMAL,$aXDensity=TICKD_NORMAL) {
	$this->xtick_factor=30;
	$this->ytick_factor=25;		
	switch( $aYDensity ) {
	    case TICKD_DENSE:
		$this->ytick_factor=12;			
		break;
	    case TICKD_NORMAL:
		$this->ytick_factor=25;			
		break;
	    case TICKD_SPARSE:
		$this->ytick_factor=40;			
		break;
	    case TICKD_VERYSPARSE:
		$this->ytick_factor=100;			
		break;		
	    default:
		JpGraphError::Raise("JpGraph: Unsupported Tick density: $densy");
	}
	switch( $aXDensity ) {
	    case TICKD_DENSE:
		$this->xtick_factor=15;							
		break;
	    case TICKD_NORMAL:
		$this->xtick_factor=30;			
		break;
	    case TICKD_SPARSE:
		$this->xtick_factor=45;					
		break;
	    case TICKD_VERYSPARSE:
		$this->xtick_factor=60;								
		break;		
	    default:
		JpGraphError::Raise("JpGraph: Unsupported Tick density: $densx");
	}		
    }
	

    // Get a string of all image map areas	
    function GetCSIMareas() {
	if( !$this->iHasStroked )
	    $this->Stroke(_CSIM_SPECIALFILE);
	$csim=$this->legend->GetCSIMAreas();

	$n = count($this->plots);
	for( $i=0; $i<$n; ++$i ) 
	    $csim .= $this->plots[$i]->GetCSIMareas();

	$n = count($this->y2plots);
	for( $i=0; $i<$n; ++$i ) 
	    $csim .= $this->y2plots[$i]->GetCSIMareas();

	return $csim;
    }
	
    // Get a complete <MAP>..</MAP> tag for the final image map
    function GetHTMLImageMap($aMapName) {
	$im = "<MAP NAME=\"$aMapName\">\n";
	$im .= $this->GetCSIMareas();
	$im .= "</MAP>"; 
	return $im;
    }

    function CheckCSIMCache($aCacheName,$aTimeOut=60) {
	global $HTTP_SERVER_VARS;

	if( $aCacheName=='auto' )
	    $aCacheName=basename($HTTP_SERVER_VARS['PHP_SELF']);

	$this->csimcachename = CSIMCACHE_DIR.$aCacheName;
	$this->csimcachetimeout = $aTimeOut;

	// First determine if we need to check for a cached version
	// This differs from the standard cache in the sense that the
	// image and CSIM map HTML file is written relative to the directory
	// the script executes in and not the specified cache directory.
	// The reason for this is that the cache directory is not necessarily
	// accessible from the HTTP server.
	if( $this->csimcachename != '' ) {
	    $dir = dirname($this->csimcachename);
	    $base = basename($this->csimcachename);
	    $base = strtok($base,'.');
	    $suffix = strtok('.');
	    $basecsim = $dir.'/'.$base.'_csim_.html';
	    $baseimg = $dir.'/'.$base.'.'.$this->img->img_format;

	    $timedout=false;
		
	    // Does it exist at all ?
	    
	    if( file_exists($basecsim) && file_exists($baseimg) ) {
		// Check that it hasn't timed out
		$diff=time()-filemtime($basecsim);
		if( $this->csimcachetimeout>0 && ($diff > $this->csimcachetimeout*60) ) {
		    $timedout=true;
		    @unlink($basecsim);
		    @unlink($baseimg);
		}
		else {
		    if ($fh = @fopen($basecsim, "r")) {
			fpassthru($fh);
			exit();
		    }
		    else
			JpGraphError::Raise(" Can't open cached CSIM \"$basecsim\" for reading.");
		}
	    }
	}
	return false;
    }

    function StrokeCSIM($aScriptName='',$aCSIMName='',$aBorder=0) {
	GLOBAL $HTTP_GET_VARS;

	if( $aCSIMName=='' ) {
	    // create a random map name
	    srand ((double) microtime() * 1000000);
	    $r = rand(0,100000);
	    $aCSIMName='__mapname'.$r.'__';
	}
	if( empty($HTTP_GET_VARS[_CSIM_DISPLAY]) ) {
	    // First determine if we need to check for a cached version
	    // This differs from the standard cache in the sense that the
	    // image and CSIM map HTML file is written relative to the directory
	    // the script executes in and not the specified cache directory.
	    // The reason for this is that the cache directory is not necessarily
	    // accessible from the HTTP server.
	    if( $this->csimcachename != '' ) {
		$dir = dirname($this->csimcachename);
		$base = basename($this->csimcachename);
		$base = strtok($base,'.');
		$suffix = strtok('.');
		$basecsim = $dir.'/'.$base.'_csim_.html';
		$baseimg = $base.'.'.$this->img->img_format;

		// Check that apache can write to directory specified

		if( file_exists($dir) && !is_writeable($dir) ) {
		    JpgraphError::Raise('Apache/PHP does not have permission to write to the CSIM cache directory ('.$dir.'). Check permissions.');
		}
		
		// Make sure directory exists
		$this->cache->MakeDirs($dir);

		// Write the image file
		$this->Stroke(CSIMCACHE_DIR.$baseimg);

		// Construct wrapper HTML and write to file and send it back to browser
		$htmlwrap = $this->GetHTMLImageMap($aCSIMName)."\n".
		    '<img src="'.CSIMCACHE_HTTP_DIR.$baseimg.'" ISMAP USEMAP="#'.$aCSIMName.'" border='.$aBorder.'>'."\n";
		if($fh =  @fopen($basecsim,'w') ) {
		    fwrite($fh,$htmlwrap);
		    fclose($fh);
		    echo $htmlwrap;
		}
		else
		    JpGraphError::Raise(" Can't write CSIM \"$basecsim\" for writing. Check free space and permissions.");
	    }
	    else {

		if( $aScriptName=='' ) {
		    JpGraphError::Raise('Missing script name in call to StrokeCSIM(). You must specify the name of the actual image script as the first parameter to StrokeCSIM().');
		    exit();
		}

		// Construct the HTML wrapper page
		// Get all user defined URL arguments
		reset($HTTP_GET_VARS);
		
		// This is a JPGRAPH internal defined that prevents
		// us from recursively coming here again
		$urlarg='?'._CSIM_DISPLAY.'=1';

		while( list($key,$value) = each($HTTP_GET_VARS) ) {
		    if( is_array($value) ) {
			$n = count($value);
			for( $i=0; $i < $n; ++$i ) {
			    $urlarg .= '&'.$key.'%5B%5D='.urlencode($value[$i]);
			}
		    }
		    else {
			$urlarg .= '&'.$key.'='.urlencode($value);
		    }
		}
		
		echo $this->GetHTMLImageMap($aCSIMName);

		echo "<img src='".$aScriptName.$urlarg."' ISMAP USEMAP='#".$aCSIMName."' border=$aBorder>";
	    }
	}
	else {
	    $this->Stroke();
	}
    }

    function GetTextsYMinMax() {
	$n = count($this->texts);
	$min=null;
	$max=null;
	for( $i=0; $i < $n; ++$i ) {
	    if( $this->texts[$i]->iScalePosY !== null && 
		$this->texts[$i]->iScalePosX !== null  ) {
		if( $min === null  ) {
		    $min = $max = $this->texts[$i]->iScalePosY ;
		}
		else {
		    $min = min($min,$this->texts[$i]->iScalePosY);
		    $max = max($max,$this->texts[$i]->iScalePosY);
		}
	    }
	}
	if( $min !== null ) {
	    return array($min,$max);
	}
	else
	    return null;
    }

    function GetTextsXMinMax() {
	$n = count($this->texts);
	$min=null;
	$max=null;
	for( $i=0; $i < $n; ++$i ) {
	    if( $this->texts[$i]->iScalePosY !== null && 
		$this->texts[$i]->iScalePosX !== null  ) {
		if( $min === null  ) {
		    $min = $max = $this->texts[$i]->iScalePosX ;
		}
		else {
		    $min = min($min,$this->texts[$i]->iScalePosX);
		    $max = max($max,$this->texts[$i]->iScalePosX);
		}
	    }
	}
	if( $min !== null ) {
	    return array($min,$max);
	}
	else
	    return null;
    }



    function GetXMinMax() {
	list($min,$ymin) = $this->plots[0]->Min();
	list($max,$ymax) = $this->plots[0]->Max();
	foreach( $this->plots as $p ) {
	    list($xmin,$ymin) = $p->Min();
	    list($xmax,$ymax) = $p->Max();			
	    $min = Min($xmin,$min);
	    $max = Max($xmax,$max);
	}
	if( $this->y2axis != null ) {
	    foreach( $this->y2plots as $p ) {
		list($xmin,$ymin) = $p->Min();
			list($xmax,$ymax) = $p->Max();			
			$min = Min($xmin,$min);
			$max = Max($xmax,$max);
	    }		    
	}
	return array($min,$max);
    }

    function AdjustMarginsForTitles() {
	$totrequired = 
	    ($this->title->t != '' ? 
	     $this->title->GetTextHeight($this->img) + $this->title->margin + 5 : 0 ) +
	    ($this->subtitle->t != '' ? 
	     $this->subtitle->GetTextHeight($this->img) + $this->subtitle->margin + 5 : 0 ) + 
	    ($this->subsubtitle->t != '' ? 
	     $this->subsubtitle->GetTextHeight($this->img) + $this->subsubtitle->margin + 5 : 0 ) ;
	

	$btotrequired = 0;
	if( !$this->xaxis->hide && !$this->xaxis->hide_labels ) {
	    // Minimum bottom margin
	    if( $this->xaxis->title->t != '' ) {
		if( $this->img->a == 90 ) 
		    $btotrequired = $this->yaxis->title->GetTextHeight($this->img) + 5 ;
		else
		    $btotrequired = $this->xaxis->title->GetTextHeight($this->img) + 5 ;
	    }
	    else
		$btotrequired = 0;
	    
	    if( $this->img->a == 90 ) {
		$this->img->SetFont($this->yaxis->font_family,$this->yaxis->font_style,
				    $this->yaxis->font_size);
		$lh = $this->img->GetTextHeight('Mg',$this->yaxis->label_angle);
	    }
	    else {
		$this->img->SetFont($this->xaxis->font_family,$this->xaxis->font_style,
				    $this->xaxis->font_size);
		$lh = $this->img->GetTextHeight('Mg',$this->xaxis->label_angle);
	    }
	    
	    $btotrequired += $lh + 5;
	}

	if( $this->img->a == 90 ) {
	    // DO Nothing. It gets too messy to do this properly for 90 deg...
	}
	else{
	    if( $this->img->top_margin < $totrequired ) {
		$this->SetMargin($this->img->left_margin,$this->img->right_margin,
				 $totrequired,$this->img->bottom_margin);
	    }
	    if( $this->img->bottom_margin < $btotrequired ) {
		$this->SetMargin($this->img->left_margin,$this->img->right_margin,
				 $this->img->top_margin,$btotrequired);
	    }
	}
    }

    // Stroke the graph
    // $aStrokeFileName	If != "" the image will be written to this file and NOT
    // streamed back to the browser
    function Stroke($aStrokeFileName="") {		

	// Start by adjusting the margin so that potential titles will fit.
	$this->AdjustMarginsForTitles();

	// If the filename is the predefined value = '_csim_special_'
	// we assume that the call to stroke only needs to do enough
	// to correctly generate the CSIM maps.
	// We use this variable to skip things we don't strictly need
	// to do to generate the image map to improve performance
	// a best we can. Therefor you will see a lot of tests !$_csim in the
	// code below.
	$_csim = ($aStrokeFileName===_CSIM_SPECIALFILE);

	// We need to know if we have stroked the plot in the
	// GetCSIMareas. Otherwise the CSIM hasn't been generated
	// and in the case of GetCSIM called before stroke to generate
	// CSIM without storing an image to disk GetCSIM must call Stroke.
	$this->iHasStroked = true;

	// Do any pre-stroke adjustment that is needed by the different plot types
	// (i.e bar plots want's to add an offset to the x-labels etc)
	for($i=0; $i<count($this->plots) ; ++$i ) {
	    $this->plots[$i]->PreStrokeAdjust($this);
	    $this->plots[$i]->DoLegend($this);
	}
		
	// Any plots on the second Y scale?
	if( $this->y2scale != null ) {
	    for($i=0; $i<count($this->y2plots)	; ++$i ) {
		$this->y2plots[$i]->PreStrokeAdjust($this);
		$this->y2plots[$i]->DoLegend($this);
	    }
	}
		
	// Bail out if any of the Y-axis not been specified and
	// has no plots. (This means it is impossible to do autoscaling and
	// no other scale was given so we can't possible draw anything). If you use manual
	// scaling you also have to supply the tick steps as well.
	if( (!$this->yscale->IsSpecified() && count($this->plots)==0) ||
	    ($this->y2scale!=null && !$this->y2scale->IsSpecified() && count($this->y2plots)==0) ) {
	    $e = "Can't draw unspecified Y-scale.<br>\nYou have either:<br>\n";
	    $e .= "1. Specified an Y axis for autoscaling but have not supplied any plots<br>\n";
	    $e .= "2. Specified a scale manually but have forgot to specify the tick steps";
	    JpGraphError::Raise($e);
	}
		
	// Bail out if no plots and no specified X-scale
	if( (!$this->xscale->IsSpecified() && count($this->plots)==0 && count($this->y2plots)==0) )
	    JpGraphError::Raise("<strong>JpGraph: Can't draw unspecified X-scale.</strong><br>No plots.<br>");

	//Check if we should autoscale y-axis
	if( !$this->yscale->IsSpecified() && count($this->plots)>0 ) {
	    list($min,$max) = $this->GetPlotsYMinMax($this->plots);
 	    $lres = $this->GetLinesYMinMax($this->lines);
	    if( $lres ) {
		list($linmin,$linmax) = $lres ;
		$min = min($min,$linmin);
		$max = max($max,$linmax);
	    }
	    $tres = $this->GetTextsYMinMax();
	    if( $tres ) {
		list($tmin,$tmax) = $tres ;
		$min = min($min,$tmin);
		$max = max($max,$tmax);
	    }
	    $this->yscale->AutoScale($this->img,$min,$max,
				     $this->img->plotheight/$this->ytick_factor);
	}
	elseif( $this->yscale->IsSpecified() && 
		( $this->yscale->auto_ticks || !$this->yscale->ticks->IsSpecified()) ) {
	    // The tick calculation will use the user suplied min/max values to determine
	    // the ticks. If auto_ticks is false the exact user specifed min and max
	    // values will be used for the scale. 
	    // If auto_ticks is true then the scale might be slightly adjusted
	    // so that the min and max values falls on an even major step.
	    $min = $this->yscale->scale[0];
	    $max = $this->yscale->scale[1];
	    $this->yscale->AutoScale($this->img,$min,$max,
				     $this->img->plotheight/$this->ytick_factor,
				     $this->yscale->auto_ticks);
	}

	if( $this->y2scale != null) {
	    if( !$this->y2scale->IsSpecified() && count($this->y2plots)>0 ) {
		list($min,$max) = $this->GetPlotsYMinMax($this->y2plots);
		$this->y2scale->AutoScale($this->img,$min,$max,$this->img->plotheight/$this->ytick_factor);
	    }			
	    elseif( $this->y2scale->IsSpecified() && 
		    ( $this->y2scale->auto_ticks || !$this->y2scale->ticks->IsSpecified()) ) {
		// The tick calculation will use the user suplied min/max values to determine
		// the ticks. If auto_ticks is false the exact user specifed min and max
		// values will be used for the scale. 
		// If auto_ticks is true then the scale might be slightly adjusted
		// so that the min and max values falls on an even major step.
		$min = $this->y2scale->scale[0];
		$max = $this->y2scale->scale[1];
		$this->y2scale->AutoScale($this->img,$min,$max,
					  $this->img->plotheight/$this->ytick_factor,
					  $this->y2scale->auto_ticks);
	    }
	}
				
	//Check if we should autoscale x-axis
	if( !$this->xscale->IsSpecified() ) {
	    if( substr($this->axtype,0,4) == "text" ) {
		$max=0;
		foreach( $this->plots as $p ) {
		    $max=max($max,$p->numpoints-1);
		}
		$min=0;
		if( $this->y2axis != null ) {
		    foreach( $this->y2plots as $p ) {
			$max=max($max,$p->numpoints-1);
		    }		    
		}
		$this->xscale->Update($this->img,$min,$max);
		$this->xscale->ticks->Set($this->xaxis->tick_step,1);
		$this->xscale->ticks->SupressMinorTickMarks();
	    }
	    else {
		list($min,$max) = $this->GetXMinMax();
		$lres = $this->GetLinesXMinMax($this->lines);
		if( $lres ) {
		    list($linmin,$linmax) = $lres ;
		    $min = min($min,$linmin);
		    $max = max($max,$linmax);
		}
		$tres = $this->GetTextsXMinMax();
		if( $tres ) {
		    list($tmin,$tmax) = $tres ;
		    $min = min($min,$tmin);
		    $max = max($max,$tmax);
		}
		$this->xscale->AutoScale($this->img,$min,$max,$this->img->plotwidth/$this->xtick_factor);
	    }
			
	    //Adjust position of y-axis and y2-axis to minimum/maximum of x-scale
	    if( !is_numeric($this->yaxis->pos) && !is_string($this->yaxis->pos) )
	    	$this->yaxis->SetPos($this->xscale->GetMinVal());
	    if( $this->y2axis != null ) {
		if( !is_numeric($this->y2axis->pos) && !is_string($this->y2axis->pos) )
		    $this->y2axis->SetPos($this->xscale->GetMaxVal());
		$this->y2axis->SetTitleSide(SIDE_RIGHT);
	    }
	}	
	elseif( $this->xscale->IsSpecified() &&  
		( $this->xscale->auto_ticks || !$this->xscale->ticks->IsSpecified()) ) {
	    // The tick calculation will use the user suplied min/max values to determine
	    // the ticks. If auto_ticks is false the exact user specifed min and max
	    // values will be used for the scale. 
	    // If auto_ticks is true then the scale might be slightly adjusted
	    // so that the min and max values falls on an even major step.
	    $min = $this->xscale->scale[0];
	    $max = $this->xscale->scale[1];
	    $this->xscale->AutoScale($this->img,$min,$max,
				     $this->img->plotwidth/$this->xtick_factor,
				     false);

	    if( $this->y2axis != null ) {
		if( !is_numeric($this->y2axis->pos) && !is_string($this->y2axis->pos) )
		    $this->y2axis->SetPos($this->xscale->GetMaxVal());
		$this->y2axis->SetTitleSide(SIDE_RIGHT);
	    }

	}
		
	// If we have a negative values and x-axis position is at 0
	// we need to supress the first and possible the last tick since
	// they will be drawn on top of the y-axis (and possible y2 axis)
	// The test below might seem strange the reasone being that if
	// the user hasn't specified a value for position this will not
	// be set until we do the stroke for the axis so as of now it
	// is undefined.
	// For X-text scale we ignore all this since the tick are usually
	// much further in and not close to the Y-axis. Hence the test 
	// for 'text'	

	if( ($this->yaxis->pos==$this->xscale->GetMinVal() || 
	     (is_string($this->yaxis->pos) && $this->yaxis->pos=='min')) &&  
	    !is_numeric($this->xaxis->pos) && $this->yscale->GetMinVal() < 0 && 
	    substr($this->axtype,0,4) != 'text' && $this->xaxis->pos!="min" ) {

	    //$this->yscale->ticks->SupressZeroLabel(false);
	    $this->xscale->ticks->SupressFirst();
	    if( $this->y2axis != null ) {
		$this->xscale->ticks->SupressLast();
	    }
	}
	elseif( !is_numeric($this->yaxis->pos) && $this->yaxis->pos=='max' ) {
	    $this->xscale->ticks->SupressLast();
	}
	

	if( !$_csim ) {
	    $this->StrokePlotArea();
	    $this->StrokeAxis();
	}

	// Stroke bands
	if( $this->bands != null && !$_csim) 
	    for($i=0; $i<count($this->bands); ++$i) {
		// Stroke all bands that asks to be in the background
		if( $this->bands[$i]->depth == DEPTH_BACK )
		    $this->bands[$i]->Stroke($this->img,$this->xscale,$this->yscale);
	    }

	if( $this->grid_depth == DEPTH_BACK && !$_csim) {
	    $this->ygrid->Stroke();
	    $this->xgrid->Stroke();
	}
				
	// Stroke Y2-axis
	if( $this->y2axis != null && !$_csim) {		
	    $this->y2axis->Stroke($this->xscale); 				
	    $this->y2grid->Stroke();
	}
		
	$oldoff=$this->xscale->off;
	if(substr($this->axtype,0,4)=="text") {
	    $this->xscale->off += 
		ceil($this->xscale->scale_factor*$this->text_scale_off*$this->xscale->ticks->minor_step);
	}

	if( $this->iDoClipping ) {
	    $oldimage = $this->img->CloneCanvasH();
	}

	if( ! $this->y2orderback ) {
	    // Stroke all plots for Y1 axis
	    for($i=0; $i < count($this->plots); ++$i) {
		$this->plots[$i]->Stroke($this->img,$this->xscale,$this->yscale);
		$this->plots[$i]->StrokeMargin($this->img);
	    }						
	}

	// Stroke all plots for Y2 axis
	if( $this->y2scale != null )
	    for($i=0; $i< count($this->y2plots); ++$i ) {	
		$this->y2plots[$i]->Stroke($this->img,$this->xscale,$this->y2scale);
	    }		

	if( $this->y2orderback ) {
	    // Stroke all plots for Y1 axis
	    for($i=0; $i < count($this->plots); ++$i) {
		$this->plots[$i]->Stroke($this->img,$this->xscale,$this->yscale);
		$this->plots[$i]->StrokeMargin($this->img);
	    }						
	}


	if( $this->iDoClipping ) {
	    // Clipping only supports graphs at 0 and 90 degrees
	    if( $this->img->a == 0 ) {
		$this->img->CopyCanvasH($oldimage,$this->img->img,
					$this->img->left_margin,$this->img->top_margin,
					$this->img->left_margin,$this->img->top_margin,
					$this->img->plotwidth+1,$this->img->plotheight);
	    }
	    elseif( $this->img->a == 90 ) {
		$adj = ($this->img->height - $this->img->width)/2;
		$this->img->CopyCanvasH($oldimage,$this->img->img,
					$this->img->bottom_margin-$adj,$this->img->left_margin+$adj,
					$this->img->bottom_margin-$adj,$this->img->left_margin+$adj,
					$this->img->plotheight+1,$this->img->plotwidth);
	    }
	    else {
		JpGraphError::Raise('You have enabled clipping. Cliping is only supported for graphs at 0 or 90 degrees rotation. Please adjust you current angle (='.$this->img->a.' degrees) or disable clipping.');
	    }
	    $this->img->Destroy();
	    $this->img->SetCanvasH($oldimage);
	}

	$this->xscale->off=$oldoff;
		
	if( $this->grid_depth == DEPTH_FRONT && !$_csim ) {
	    $this->ygrid->Stroke();
	    $this->xgrid->Stroke();
	}

	// Stroke bands
	if( $this->bands!= null )
	    for($i=0; $i<count($this->bands); ++$i) {
		// Stroke all bands that asks to be in the foreground
		if( $this->bands[$i]->depth == DEPTH_FRONT )
		    $this->bands[$i]->Stroke($this->img,$this->xscale,$this->yscale);
	    }

	// Stroke any lines added
	if( $this->lines != null ) {
	    for($i=0; $i<count($this->lines); ++$i) {
		$this->lines[$i]->Stroke($this->img,$this->xscale,$this->yscale);
	    }
	}
		
	// Finally draw the axis again since some plots may have nagged
	// the axis in the edges.
	if( !$_csim )
	    $this->StrokeAxis();

	if( $this->y2scale != null && !$_csim ) 
	    $this->y2axis->Stroke($this->xscale); 	
		
	if( !$_csim ) {
	    $this->StrokePlotBox();
	}
		
	if( !$_csim ) {
	    // The titles and legends never gets rotated so make sure
	    // that the angle is 0 before stroking them				
	    $aa = $this->img->SetAngle(0);
	    $this->StrokeTitles();
	    $this->footer->Stroke($this->img);
	}

	$this->legend->Stroke($this->img);		

	if( !$_csim ) {

	    $this->StrokeTexts();	
	    $this->img->SetAngle($aa);	
			
	    // Draw an outline around the image map	
	    if(JPG_DEBUG)
		$this->DisplayClientSideaImageMapAreas();		
	    
	    // Adjust the appearance of the image
	    $this->AdjustSaturationBrightnessContrast();

	    // If the filename is given as the special "__handle"
	    // then the image handler is returned and the image is NOT
	    // streamed back
	    if( $aStrokeFileName == _IMG_HANDLER ) {
		return $this->img->img;
	    }
	    else {
		// Finally stream the generated picture					
		$this->cache->PutAndStream($this->img,$this->cache_name,$this->inline,
					   $aStrokeFileName);		
	    }
	}
    }

//---------------
// PRIVATE METHODS	
    function StrokeAxis() {
		
	// Stroke axis
	if( $this->iAxisStyle != AXSTYLE_SIMPLE ) {
	    switch( $this->iAxisStyle ) {
	        case AXSTYLE_BOXIN :
	            $toppos = SIDE_DOWN;
		    $bottompos = SIDE_UP;
	            $leftpos = SIDE_RIGHT;
	            $rightpos = SIDE_LEFT;
	            break;
		case AXSTYLE_BOXOUT :
		    $toppos = SIDE_UP;
	            $bottompos = SIDE_DOWN;	    
	            $leftpos = SIDE_LEFT;
		    $rightpos = SIDE_RIGHT;
	            break;
		case AXSTYLE_YBOXIN:
	            $toppos = -100;
		    $bottompos = SIDE_UP;
	            $leftpos = SIDE_RIGHT;
	            $rightpos = SIDE_LEFT;
		    break;
		case AXSTYLE_YBOXOUT:
		    $toppos = -100;
	            $bottompos = SIDE_DOWN;	    
	            $leftpos = SIDE_LEFT;
		    $rightpos = SIDE_RIGHT;
		    break;
		default:
	            JpGRaphError::Raise('Unknown AxisStyle() : '.$this->iAxisStyle);
	            break;
	    }
	    $this->xaxis->SetPos('min');
	    
	    // By default we hide the first label so it doesn't cross the
	    // Y-axis in case the positon hasn't been set by the user.
	    // However, if we use a box we always want the first value
	    // displayed so we make sure it will be displayed.
	    $this->xscale->ticks->SupressFirst(false);
	    
	    $this->xaxis->SetLabelSide(SIDE_DOWN);
	    $this->xaxis->scale->ticks->SetSide($bottompos);
	    $this->xaxis->Stroke($this->yscale);

	    if( $toppos != -100 ) {
		// To avoid side effects we work on a new copy
		$maxis = $this->xaxis;
		$maxis->SetPos('max');
		$maxis->SetLabelSide(SIDE_UP);
		$maxis->SetLabelMargin(7);
		$this->xaxis->scale->ticks->SetSide($toppos);
		$maxis->Stroke($this->yscale);
	    }

	    $this->yaxis->SetPos('min');
	    $this->yaxis->SetLabelMargin(10);
	    $this->yaxis->SetLabelSide(SIDE_LEFT);
	    $this->yaxis->scale->ticks->SetSide($leftpos);
	    $this->yaxis->Stroke($this->xscale);

	    $myaxis = $this->yaxis;
	    $myaxis->SetPos('max');
	    $myaxis->SetLabelMargin(10);
	    $myaxis->SetLabelSide(SIDE_RIGHT);
	    $myaxis->title->Set('');
	    $myaxis->scale->ticks->SetSide($rightpos);
	    $myaxis->Stroke($this->xscale);
	    
	}
	else {
	    $this->xaxis->Stroke($this->yscale);
	    $this->yaxis->Stroke($this->xscale);		
	}
    }


    // Private helper function for backgound image
    function LoadBkgImage($aImgFormat='',$aFile='') {
	if( $aFile == '' )
	    $aFile = $this->background_image;
	// Remove case sensitivity and setup appropriate function to create image
	// Get file extension. This should be the LAST '.' separated part of the filename
	$e = explode('.',$aFile);
	$ext = strtolower($e[count($e)-1]);
	if ($ext == "jpeg")  {
	    $ext = "jpg";
	}
	
	if( trim($ext) == '' ) 
	    $ext = 'png';  // Assume PNG if no extension specified

	if( $aImgFormat == '' )
	    $imgtag = $ext;
	else
	    $imgtag = $aImgFormat;

	if( $imgtag == "jpg" || $imgtag == "jpeg")
	{
		$f = "imagecreatefromjpeg";
		$imgtag = "jpg";
	}
	else
	{
		$f = "imagecreatefrom".$imgtag;
	}

	// Compare specified image type and file extension
	if( $imgtag != $ext ) {
	    $t = " Background image seems to be of different type (has different file extension)".
		 " than specified imagetype. <br>Specified: '".
		$aImgFormat."'<br>File: '".$aFile."'";
	    JpGraphError::Raise($t);
	}

	$img = @$f($aFile);
	if( !$img ) {
	    JpGraphError::Raise(" Can't read background image: '".$aFile."'");   
	}
	return $img;
    }	

    function StrokeBackgroundGrad() {
	if( $this->bkg_gradtype < 0  ) 
	    return;
	$grad = new Gradient($this->img);
	if( $this->bkg_gradstyle == BGRAD_PLOT ) {
	    $xl = $this->img->left_margin;
	    $yt = $this->img->top_margin;
	    $xr = $xl + $this->img->plotwidth ;
	    $yb = $yt + $this->img->plotheight ;
	}
	else {
	    $xl = 0;
	    $yt = 0;
	    $xr = $xl + $this->img->width - 1;
	    $yb = $yt + $this->img->height - 1;
	}
	if( $this->doshadow  ) {
	    $xr -= $this->shadow_width; 
	    $yb -= $this->shadow_width; 
	}
	$grad->FilledRectangle($xl,$yt,$xr,$yb,
			       $this->bkg_gradfrom,$this->bkg_gradto,
			       $this->bkg_gradtype);
    }

    function StrokeFrameBackground() {
	if( $this->background_image == "" ) 
	    return;

	$bkgimg = $this->LoadBkgImage($this->background_image_format);
	$this->img->_AdjBrightContrast($bkgimg,$this->background_image_bright,
				       $this->background_image_contr);
	$this->img->_AdjSat($bkgimg,$this->background_image_sat);
	$bw = ImageSX($bkgimg);
	$bh = ImageSY($bkgimg);

	// No matter what the angle is we always stroke the image and frame
	// assuming it is 0 degree
	$aa = $this->img->SetAngle(0);
		
	switch( $this->background_image_type ) {
	    case BGIMG_FILLPLOT: // Resize to just fill the plotarea
		$this->StrokeFrame();
		$GLOBALS['copyfunc']($this->img->img,$bkgimg,
				     $this->img->left_margin,$this->img->top_margin,
				     0,0,$this->img->plotwidth,$this->img->plotheight,
				     $bw,$bh);
		break;
	    case BGIMG_FILLFRAME: // Fill the whole area from upper left corner, resize to just fit
		$GLOBALS['copyfunc']($this->img->img,$bkgimg,
				     0,0,0,0,
				     $this->img->width,$this->img->height,
				     $bw,$bh);
		$this->StrokeFrame();
		break;
	    case BGIMG_COPY: // Just copy the image from left corner, no resizing
		$GLOBALS['copyfunc']($this->img->img,$bkgimg,
				     0,0,0,0,
				     $bw,$bh,
				     $bw,$bh);
		$this->StrokeFrame();
		break;
	    case BGIMG_CENTER: // Center original image in the plot area
		$centerx = round($this->img->plotwidth/2+$this->img->left_margin-$bw/2);
		$centery = round($this->img->plotheight/2+$this->img->top_margin-$bh/2);
		$GLOBALS['copyfunc']($this->img->img,$bkgimg,
				     $centerx,$centery,
				     0,0,
				     $bw,$bh,
				     $bw,$bh);
		$this->StrokeFrame();
		break;
	    default:
		JpGraphError::Raise(" Unknown background image layout");
	}			
	$this->img->SetAngle($aa);		
    }

    // Private
    // Draw a frame around the image
    function StrokeFrame() {
	if( !$this->doframe ) return;
	if( $this->background_image_type <= 1 && 
	    ($this->bkg_gradtype < 0 || 
	     ($this->bkg_gradtype > 0 && $this->bkg_gradstyle==BGRAD_PLOT) ) ) 
	    $c = $this->margin_color;
	else
	    $c = false;
	
	if( $this->doshadow ) {
	    $this->img->SetColor($this->frame_color);			
	    $this->img->ShadowRectangle(0,0,$this->img->width,$this->img->height,
					$c,$this->shadow_width,$this->shadow_color);
	}
	elseif( $this->framebevel ) {
	    if( $c ) {
		$this->img->SetColor($this->margin_color);
		$this->img->FilledRectangle(0,0,$this->img->width-1,$this->img->height-1); 
	    }
	    $this->img->Bevel(1,1,$this->img->width-2,$this->img->height-2,
			      $this->framebeveldepth,
			      $this->framebevelcolor1,$this->framebevelcolor2);
	    if( $this->framebevelborder ) {
		$this->img->SetColor($this->framebevelbordercolor);
		$this->img->Rectangle(0,0,$this->img->width-1,$this->img->height-1);
	    }
	}
	else {
	    $this->img->SetLineWeight($this->frame_weight);
	    if( $c ) {
		$this->img->SetColor($this->margin_color);
		$this->img->FilledRectangle(0,0,$this->img->width-1,$this->img->height-1); 
	    }
	    $this->img->SetColor($this->frame_color);
	    $this->img->Rectangle(0,0,$this->img->width-1,$this->img->height-1);		
	}
	$this->StrokeBackgroundGrad();
    }

    // Stroke the plot area with either a solid color or a background image
    function StrokePlotArea() {
	// Note: To be consistent we really should take a possible shadow
	// into account. However, that causes some problem for the LinearScale class
	// since in the current design it does not have any links to class Graph which
	// means it has no way of compensating for the adjusted plotarea in case of a 
	// shadow. So, until I redesign LinearScale we can't compensate for this.
	// So just set the two adjustment parameters to zero for now.
	$boxadj = 0; //$this->doframe ? $this->frame_weight : 0 ;
	$adj = 0; //$this->doshadow ? $this->shadow_width : 0 ;

	if( $this->background_image != "" ) {
	    $this->StrokeFrameBackground();
	}
	else {
	    $aa = $this->img->SetAngle(0);
	    $this->StrokeFrame();
	    if( $this->bkg_gradtype < 0 || 
		($this->bkg_gradtype > 0 && $this->bkg_gradstyle==BGRAD_MARGIN ) ) {
		$this->img->SetAngle($aa);			
		$this->img->PushColor($this->plotarea_color);
		$this->img->FilledRectangle($this->img->left_margin+$boxadj,
					    $this->img->top_margin+$boxadj,
					    $this->img->width-$this->img->right_margin-$adj-2*$boxadj,
					    $this->img->height-$this->img->bottom_margin-$adj-2*$boxadj);	
		$this->img->PopColor();
	    }
	}	
    }	
	
	
    function StrokePlotBox() {
	// Should we draw a box around the plot area?
	if( $this->boxed ) {
	    $this->img->SetLineWeight($this->box_weight);
	    $this->img->SetColor($this->box_color);
	    $this->img->Rectangle(
		$this->img->left_margin,$this->img->top_margin,
		$this->img->width-$this->img->right_margin,
		$this->img->height-$this->img->bottom_margin);
	}						
    }		

    function SetTitleBackgroundFillStyle($aStyle,$aColor1='black',$aColor2='white') {
	$this->titlebkg_fillstyle = $aStyle;
	$this->titlebkg_scolor1 = $aColor1;
	$this->titlebkg_scolor2 = $aColor2;
    }

    function SetTitleBackground($aBackColor='gray', $aStyle=TITLEBKG_STYLE1, $aFrameStyle=TITLEBKG_FRAME_NONE, $aFrameColor='black', $aFrameWeight=1, $aBevelHeight=3, $aEnable=true) {
	$this->titlebackground = $aEnable;
	$this->titlebackground_color = $aBackColor;
	$this->titlebackground_style = $aStyle;
	$this->titlebackground_framecolor = $aFrameColor;
	$this->titlebackground_framestyle = $aFrameStyle;
	$this->titlebackground_frameweight = $aFrameWeight;	
	$this->titlebackground_bevelheight = $aBevelHeight ;
    }


    function StrokeTitles() {

	$margin=3;

	if( $this->titlebackground ) {

	    // Find out height
	    $this->title->margin += 2 ;
	    $h = $this->title->GetTextHeight($this->img)+$this->title->margin+$margin;
	    if( $this->subtitle->t != "" && !$this->subtitle->hide ) {
		$h += $this->subtitle->GetTextHeight($this->img)+$margin+
		    $this->subtitle->margin;
	    }
	    if( $this->subsubtitle->t != "" && !$this->subsubtitle->hide ) {
		$h += $this->subsubtitle->GetTextHeight($this->img)+$margin+
		    $this->subsubtitle->margin;
	    }
	    $this->img->PushColor($this->titlebackground_color);
	    if( $this->titlebackground_style === 1 ) {
		// Inside the frame
		if( $this->framebevel ) {
		    $x1 = $y1 = $this->framebeveldepth + 1 ;
		    $x2 = $this->img->width - $this->framebeveldepth - 2 ; 
		    $this->title->margin += $this->framebeveldepth + 1 ;
		    $h += $y1 ;
		}
		else {
		    $x1 = $y1 = $this->frame_weight;
		    $x2 = $this->img->width - 2*$x1;
		}
	    }
	    elseif( $this->titlebackground_style === 2 ) {
		// Cover the frame as well
		$x1 = $y1 = 0;
		$x2 = $this->img->width - 1 ;
	    }
	    elseif( $this->titlebackground_style === 3) {
		// Cover the frame as well (the difference is that
		// for style==3 a bevel frame border is on top
		// of the title background)
		$x1 = $y1 = 0;
		$x2 = $this->img->width - 1 ;
		$h += $this->framebeveldepth ;
		$this->title->margin += $this->framebeveldepth ;
	    }
	    else {
		JpGraphError::Raise('Unknown title background style.');
	    }

	    if( $this->titlebackground_framestyle === 3 ) {
		$h += $this->titlebackground_bevelheight*2 + 1  ;
		$this->title->margin += $this->titlebackground_bevelheight ;
	    }

	    if( $this->doshadow ) {
		$x2 -= $this->shadow_width ;
	    }
	    
	    if( $this->titlebkg_fillstyle==TITLEBKG_FILLSTYLE_HSTRIPED ) {
		$this->img->FilledRectangle2($x1,$y1,$x2,$h,
					     $this->titlebkg_scolor1,
					     $this->titlebkg_scolor2);
	    }
	    elseif( $this->titlebkg_fillstyle==TITLEBKG_FILLSTYLE_VSTRIPED ) {
		$this->img->FilledRectangle2($x1,$y1,$x2,$h,
					     $this->titlebkg_scolor1,
					     $this->titlebkg_scolor2,2);
	    }
	    else {
		// Solid fill
		$this->img->FilledRectangle($x1,$y1,$x2,$h);
	    }
	    $this->img->PopColor();

	    $this->img->PushColor($this->titlebackground_framecolor);
	    $this->img->SetLineWeight($this->titlebackground_frameweight);
	    if( $this->titlebackground_framestyle == 1 ) {
		// Frame background
		$this->img->Rectangle($x1,$y1,$x2,$h);
	    }
	    elseif( $this->titlebackground_framestyle == 2 ) {
		// Bottom line only
		$this->img->Line($x1,$h,$x2,$h);
	    }
	    elseif( $this->titlebackground_framestyle == 3 ) {
		$this->img->Bevel($x1,$y1,$x2,$h,$this->titlebackground_bevelheight);
	    }
	    $this->img->PopColor();

	    // This is clumsy. But we neeed to stroke the whole graph frame if it is
	    // set to bevel to get the bevel shading on top of the text background
	    if( $this->framebevel && $this->doframe && 
		$this->titlebackground_style === 3 ) {
		$this->img->Bevel(1,1,$this->img->width-2,$this->img->height-2,
				  $this->framebeveldepth,
				  $this->framebevelcolor1,$this->framebevelcolor2);
		if( $this->framebevelborder ) {
		    $this->img->SetColor($this->framebevelbordercolor);
		    $this->img->Rectangle(0,0,$this->img->width-1,$this->img->height-1);
		}
	    }
	}

	// Stroke title
	$y = $this->title->margin; 
	$this->title->Center(0,$this->img->width,$y);
	$this->title->Stroke($this->img);
		
	// ... and subtitle
	$y += $this->title->GetTextHeight($this->img) + $margin + $this->subtitle->margin;
	$this->subtitle->Center(0,$this->img->width,$y);	
	$this->subtitle->Stroke($this->img);

	// ... and subsubtitle
	$y += $this->subtitle->GetTextHeight($this->img) + $margin + $this->subsubtitle->margin;
	$this->subsubtitle->Center(0,$this->img->width,$y);
	$this->subsubtitle->Stroke($this->img);

	// ... and fancy title
	$this->tabtitle->Stroke($this->img);

    }

    function StrokeTexts() {
	// Stroke any user added text objects
	if( $this->texts != null ) {
	    for($i=0; $i<count($this->texts); ++$i) {
		$this->texts[$i]->StrokeWithScale($this->img,$this->xscale,$this->yscale);
	    }
	}
    }

    function DisplayClientSideaImageMapAreas() {
	// Debug stuff - display the outline of the image map areas
	foreach ($this->plots as $p) {
	    $csim.= $p->GetCSIMareas();
	}
	$csim .= $this->legend->GetCSIMareas();
	if (preg_match_all("/area shape=\"(\w+)\" coords=\"([0-9\, ]+)\"/", $csim, $coords)) {
	    $this->img->SetColor($this->csimcolor);
	    for ($i=0; $i<count($coords[0]); $i++) {
		if ($coords[1][$i]=="poly") {
		    preg_match_all('/\s*([0-9]+)\s*,\s*([0-9]+)\s*,*/',$coords[2][$i],$pts);
		    $this->img->SetStartPoint($pts[1][count($pts[0])-1],$pts[2][count($pts[0])-1]);
		    for ($j=0; $j<count($pts[0]); $j++) {
			$this->img->LineTo($pts[1][$j],$pts[2][$j]);
		    }
		} else if ($coords[1][$i]=="rect") {
		    $pts = preg_split('/,/', $coords[2][$i]);
		    $this->img->SetStartPoint($pts[0],$pts[1]);
		    $this->img->LineTo($pts[2],$pts[1]);
		    $this->img->LineTo($pts[2],$pts[3]);
		    $this->img->LineTo($pts[0],$pts[3]);
		    $this->img->LineTo($pts[0],$pts[1]);					
		}
	    }
	}
    }

    function AdjustSaturationBrightnessContrast() {
	// Adjust the brightness and contrast of the image
	if( $this->image_contr || $this->image_bright )
	    $this->img->AdjBrightContrast($this->image_bright,$this->image_contr);
	if( $this->image_sat )											 
	    $this->img->AdjSat($this->image_sat);
    }

    // Text scale offset in world coordinates
    function SetTextScaleOff($aOff) {
	$this->text_scale_off = $aOff;
	$this->xscale->text_scale_off = $aOff;
    }

    // Get Y min and max values for added lines
    function GetLinesYMinMax( $aLines ) {
	$n = count($aLines);
	if( $n == 0 ) return false;
	$min = $aLines[0]->scaleposition ;
	$max = $min ;
	$flg = false;
	for( $i=0; $i < $n; ++$i ) {
	    if( $aLines[$i]->direction == HORIZONTAL ) {
		$flg = true ;
		$v = $aLines[$i]->scaleposition ;
		if( $min > $v ) $min = $v ;
		if( $max < $v ) $max = $v ;
	    }
	}
	return $flg ? array($min,$max) : false ;
    }

    // Get X min and max values for added lines
    function GetLinesXMinMax( $aLines ) {
	$n = count($aLines);
	if( $n == 0 ) return false ;
	$min = $aLines[0]->scaleposition ;
	$max = $min ;
	$flg = false;
	for( $i=0; $i < $n; ++$i ) {
	    if( $aLines[$i]->direction == VERTICAL ) {
		$flg = true ;
		$v = $aLines[$i]->scaleposition ;
		if( $min > $v ) $min = $v ;
		if( $max < $v ) $max = $v ;
	    }
	}
	return $flg ? array($min,$max) : false ;
    }

    // Get min and max values for all included plots
    function GetPlotsYMinMax(&$aPlots) {
	list($xmax,$max) = $aPlots[0]->Max();
	list($xmin,$min) = $aPlots[0]->Min();
	for($i=0; $i<count($aPlots); ++$i ) {
	    list($xmax,$ymax)=$aPlots[$i]->Max();
	    list($xmin,$ymin)=$aPlots[$i]->Min();
	    if (!is_string($ymax) || $ymax != "") $max=max($max,$ymax);
	    if (!is_string($ymin) || $ymin != "") $min=min($min,$ymin);
	}
	if( $min == "" ) $min = 0;
	if( $max == "" ) $max = 0;
	if( $min == 0 && $max == 0 ) {
	    // Special case if all values are 0
	    $min=0;$max=1;			
	}
	return array($min,$max);
    }

} // Class


//===================================================
// CLASS TTF
// Description: Handle TTF font names
//===================================================
class TTF {
    var $font_files,$style_names;
//---------------
// CONSTRUCTOR
    function TTF() {
	$this->style_names=array(FS_NORMAL=>'normal',FS_BOLD=>'bold',FS_ITALIC=>'italic',FS_BOLDITALIC=>'bolditalic');
	// File names for available fonts
	$this->font_files=array(
	    FF_COURIER => array(FS_NORMAL=>'cour', FS_BOLD=>'courbd', FS_ITALIC=>'couri', FS_BOLDITALIC=>'courbi' ),
	    FF_GEORGIA => array(FS_NORMAL=>'georgia', FS_BOLD=>'georgiab', FS_ITALIC=>'georgiai', FS_BOLDITALIC=>'' ),
	    FF_TREBUCHE =>array(FS_NORMAL=>'trebuc', FS_BOLD=>'trebucbd',   FS_ITALIC=>'trebucit', FS_BOLDITALIC=>'trebucbi' ),
	    FF_VERDANA => array(FS_NORMAL=>'verdana', FS_BOLD=>'verdanab',  FS_ITALIC=>'verdanai', FS_BOLDITALIC=>'' ),
	    FF_TIMES =>   array(FS_NORMAL=>'times',   FS_BOLD=>'timesbd',   FS_ITALIC=>'timesi',   FS_BOLDITALIC=>'timesbi' ),
	    FF_COMIC =>   array(FS_NORMAL=>'comic',   FS_BOLD=>'comicbd',   FS_ITALIC=>'',         FS_BOLDITALIC=>'' ),
	    FF_ARIAL =>   array(FS_NORMAL=>'arial',   FS_BOLD=>'arialbd',   FS_ITALIC=>'ariali',   FS_BOLDITALIC=>'arialbi' ) ,
	    FF_SIMSUN =>   array(FS_NORMAL=>'simsun',   FS_BOLD=>'simhei',   FS_ITALIC=>'simsun',   FS_BOLDITALIC=>'simhei' )	    
);
    }

//---------------
// PUBLIC METHODS	
    // Create the TTF file from the font specification
    function File($family,$style=FS_NORMAL) {
	
	if( $family == FF_HANDWRT || $family==FF_BOOK )
	    JpGraphError::Raise('Font families FF_HANDWRT and FF_BOOK are no longer available due to copyright problem with these fonts. Fonts can no longer be distributed with JpGraph. Please download fonts from http://corefonts.sourceforge.net/');

	$fam = @$this->font_files[$family];
	if( !$fam ) JpGraphError::Raise("Specified TTF font family (id=$family) is unknown or does not exist. ".
					"Please note that TTF fonts are not distributed with JpGraph for copyright reasons.". 
					" You can find the MS TTF WEB-fonts (arial, courier etc) for download at ".
					" http://corefonts.sourceforge.net/");
	$f = @$fam[$style];

	if( $f==='' )
	    JpGraphError::Raise('Style "'.$this->style_names[$style].'" is not available for font family '.$this->font_files[$family][FS_NORMAL].'.');
	if( !$f )
	    JpGraphError::Raise("Unknown font style specification [$fam].");
	$f = TTF_DIR.$f.'.ttf';
	if( file_exists($f) === false || is_readable($f) === false ) {
	    JpGraphError::Raise("Font file \"$f\" is not readable or does not exist.");
	}
	return $f;
    }
} // Class

//===================================================
// CLASS LineProperty
// Description: Holds properties for a line
//===================================================
class LineProperty {
    var $iWeight=1, $iColor="black",$iStyle="solid";
    var $iShow=true;
	
//---------------
// PUBLIC METHODS	
    function SetColor($aColor) {
	$this->iColor = $aColor;
    }
	
    function SetWeight($aWeight) {
	$this->iWeight = $aWeight;
    }
	
    function SetStyle($aStyle) {
	$this->iStyle = $aStyle;
    }
		
    function Show($aShow=true) {
	$this->iShow=$aShow;
    }
	
    function Stroke($aImg,$aX1,$aY1,$aX2,$aY2) {
	if( $this->iShow ) {
	    $aImg->SetColor($this->iColor);
	    $aImg->SetLineWeight($this->iWeight);
	    $aImg->SetLineStyle($this->iStyle);			
	    $aImg->StyleLine($aX1,$aY1,$aX2,$aY2);
	}
    }
}


//===================================================
// CLASS Text
// Description: Arbitrary text object that can be added to the graph
//===================================================
class Text {
    var $t,$x=0,$y=0,$halign="left",$valign="top",$color=array(0,0,0);
    var $font_family=FF_FONT1,$font_style=FS_NORMAL,$font_size=12;
    var $hide=false, $dir=0;
    var $boxed=false;	// Should the text be boxed
    var $paragraph_align="left";
    var $margin;
    var $icornerradius=0,$ishadowwidth=3;
    var $iScalePosY=null,$iScalePosX=null;

//---------------
// CONSTRUCTOR

    // Create new text at absolute pixel coordinates
    function Text($aTxt="",$aXAbsPos=0,$aYAbsPos=0) {
	$this->t = $aTxt;
	$this->x = round($aXAbsPos);
	$this->y = round($aYAbsPos);
	$this->margin = 0;
    }
//---------------
// PUBLIC METHODS	
    // Set the string in the text object
    function Set($aTxt) {
	$this->t = $aTxt;
    }
	
    // Alias for Pos()
    function SetPos($aXAbsPos=0,$aYAbsPos=0,$aHAlign="left",$aVAlign="top") {
	$this->Pos($aXAbsPos,$aYAbsPos,$aHAlign,$aVAlign);
    }
    
    // Specify the position and alignment for the text object
    function Pos($aXAbsPos=0,$aYAbsPos=0,$aHAlign="left",$aVAlign="top") {
	$this->x = $aXAbsPos;
	$this->y = $aYAbsPos;
	$this->halign = $aHAlign;
	$this->valign = $aVAlign;
    }

    function SetScalePos($aX,$aY) {
	$this->iScalePosX = $aX;
	$this->iScalePosY = $aY;
    }
	
    // Specify alignment for the text
    function Align($aHAlign,$aVAlign="top",$aParagraphAlign="") {
	$this->halign = $aHAlign;
	$this->valign = $aVAlign;
	if( $aParagraphAlign != "" )
	    $this->paragraph_align = $aParagraphAlign;
    }		
    
    // Alias
    function SetAlign($aHAlign,$aVAlign="top",$aParagraphAlign="") {
	$this->Align($aHAlign,$aVAlign,$aParagraphAlign);
    }

    // Specifies the alignment for a multi line text
    function ParagraphAlign($aAlign) {
	$this->paragraph_align = $aAlign;
    }

    function SetShadow($aShadowColor='gray',$aShadowWidth=3) {
	$this->ishadowwidth=$aShadowWidth;
	$this->shadow=$aShadowColor;
	$this->boxed=true;
    }
	
    // Specify that the text should be boxed. fcolor=frame color, bcolor=border color,
    // $shadow=drop shadow should be added around the text.
    function SetBox($aFrameColor=array(255,255,255),$aBorderColor=array(0,0,0),$aShadowColor=false,$aCornerRadius=4,$aShadowWidth=3) {
	if( $aFrameColor==false )
	    $this->boxed=false;
	else
	    $this->boxed=true;
	$this->fcolor=$aFrameColor;
	$this->bcolor=$aBorderColor;
	// For backwards compatibility when shadow was just true or false
	if( $aShadowColor === true )
	    $aShadowColor = 'gray';
	$this->shadow=$aShadowColor;
	$this->icornerradius=$aCornerRadius;
	$this->ishadowwidth=$aShadowWidth;
    }
	
    // Hide the text
    function Hide($aHide=true) {
	$this->hide=$aHide;
    }
	
    // This looks ugly since it's not a very orthogonal design 
    // but I added this "inverse" of Hide() to harmonize
    // with some classes which I designed more recently (especially) 
    // jpgraph_gantt
    function Show($aShow=true) {
	$this->hide=!$aShow;
    }
	
    // Specify font
    function SetFont($aFamily,$aStyle=FS_NORMAL,$aSize=10) {
	$this->font_family=$aFamily;
	$this->font_style=$aStyle;
	$this->font_size=$aSize;
    }
			
    // Center the text between $left and $right coordinates
    function Center($aLeft,$aRight,$aYAbsPos=false) {
	$this->x = $aLeft + ($aRight-$aLeft	)/2;
	$this->halign = "center";
	if( is_numeric($aYAbsPos) )
	    $this->y = $aYAbsPos;		
    }
	
    // Set text color
    function SetColor($aColor) {
	$this->color = $aColor;
    }
	
    function SetAngle($aAngle) {
	$this->SetOrientation($aAngle);
    }
	
    // Orientation of text. Note only TTF fonts can have an arbitrary angle
    function SetOrientation($aDirection=0) {
	if( is_numeric($aDirection) )
	    $this->dir=$aDirection;	
	elseif( $aDirection=="h" )
	    $this->dir = 0;
	elseif( $aDirection=="v" )
	    $this->dir = 90;
	else JpGraphError::Raise(" Invalid direction specified for text.");
    }
	
    // Total width of text
    function GetWidth($aImg) {
	$aImg->SetFont($this->font_family,$this->font_style,$this->font_size);
	$w = $aImg->GetTextWidth($this->t,$this->dir);
	return $w;	
    }
	
    // Hight of font
    function GetFontHeight($aImg) {
	$aImg->SetFont($this->font_family,$this->font_style,$this->font_size);
	$h = $aImg->GetFontHeight();
	return $h;

    }

    function GetTextHeight($aImg) {
	$aImg->SetFont($this->font_family,$this->font_style,$this->font_size);	
	$h = $aImg->GetTextHeight($this->t,$this->dir);
	return $h;
    }

    // Set the margin which will be interpretated differently depending
    // on the context.
    function SetMargin($aMarg) {
	$this->margin = $aMarg;
    }

    function StrokeWithScale($aImg,$axscale,$ayscale) {
	if( $this->iScalePosX === null ||
	    $this->iScalePosY === null ) {
	    $this->Stroke($aImg);
	}
	else {
	    $this->Stroke($aImg,
			  round($axscale->Translate($this->iScalePosX)),
			  round($ayscale->Translate($this->iScalePosY)));
	}
    }
	
    // Display text in image
    function Stroke($aImg,$x=null,$y=null) {

	if( !empty($x) ) $this->x = round($x);
	if( !empty($y) ) $this->y = round($y);

	// If position been given as a fraction of the image size
	// calculate the absolute position
	if( $this->x < 1 && $this->x > 0 ) $this->x *= $aImg->width;
	if( $this->y < 1 && $this->y > 0 ) $this->y *= $aImg->height;

	$aImg->PushColor($this->color);	
	$aImg->SetFont($this->font_family,$this->font_style,$this->font_size);
	$aImg->SetTextAlign($this->halign,$this->valign);
	if( $this->boxed ) {
	    if( $this->fcolor=="nofill" ) 
		$this->fcolor=false;		
	    $aImg->SetLineWeight(1);
	    $aImg->StrokeBoxedText($this->x,$this->y,$this->t,
				   $this->dir,$this->fcolor,$this->bcolor,$this->shadow,
				   $this->paragraph_align,6,2,$this->icornerradius,
				   $this->ishadowwidth);
	}
	else {
	    $aImg->StrokeText($this->x,$this->y,$this->t,$this->dir,
			      $this->paragraph_align);
	}
	$aImg->PopColor($this->color);	
    }
} // Class

class GraphTabTitle extends Text{
    var $corner = 6 , $posx = 7, $posy = 4;
    var $color='darkred',$fillcolor='lightyellow',$bordercolor='black';
    var $align = 'left', $width=TABTITLE_WIDTHFIT;
    function GraphTabTitle() {
	$this->t = '';
	$this->font_style = FS_BOLD;
	$this->hide = true;
    }

    function SetColor($aTxtColor,$aFillColor='lightyellow',$aBorderColor='black') {
	$this->color = $aTxtColor;
	$this->fillcolor = $aFillColor;
	$this->bordercolor = $aBorderColor;
    }

    function SetFillColor($aFillColor) {
	$this->fillcolor = $aFillColor;
    }

    function SetTabAlign($aAlign) {
	// Synonym for SetPos
	$this->align = $aAlign;
    }

    function SetPos($aAlign) {
	$this->align = $aAlign;
    }
    
    function SetWidth($aWidth) {
	$this->width = $aWidth ;
    }

    function Set($t) {
	$this->t = $t;
	$this->hide = false;
    }

    function SetCorner($aD) {
	$this->corner = $aD ;
    }

    function Stroke($aImg) {
	if( $this->hide ) 
	    return;
	$this->boxed = false;
	$w = $this->GetWidth($aImg) + 2*$this->posx;
	$h = $this->GetTextHeight($aImg) + 2*$this->posy;

	$x = $aImg->left_margin;
	$y = $aImg->top_margin;

	if( $this->width === TABTITLE_WIDTHFIT ) {
	    if( $this->align == 'left' ) {
		$p = array($x,                $y,
			   $x,                $y-$h+$this->corner,
			   $x + $this->corner,$y-$h,
			   $x + $w - $this->corner, $y-$h,
			   $x + $w, $y-$h+$this->corner,
			   $x + $w, $y);
	    }
	    elseif( $this->align == 'center' ) {
		$x += round($aImg->plotwidth/2) - round($w/2);
		$p = array($x, $y,
			   $x, $y-$h+$this->corner,
			   $x + $this->corner, $y-$h,
			   $x + $w - $this->corner, $y-$h,
			   $x + $w, $y-$h+$this->corner,
			   $x + $w, $y);
	    }
	    else {
		$x += $aImg->plotwidth -$w;
		$p = array($x, $y,
			   $x, $y-$h+$this->corner,
			   $x + $this->corner,$y-$h,
			   $x + $w - $this->corner, $y-$h,
			   $x + $w, $y-$h+$this->corner,
			   $x + $w, $y);
	    }
	}
	else {
	    if( $this->width === TABTITLE_WIDTHFULL )
		$w = $aImg->plotwidth ;
	    else
		$w = $this->width ;

	    // Make the tab fit the width of the plot area
	    $p = array($x,                $y,
		       $x,                $y-$h+$this->corner,
		       $x + $this->corner,$y-$h,
		       $x + $w - $this->corner, $y-$h,
		       $x + $w, $y-$h+$this->corner,
		       $x + $w, $y);
	    
	}
	$aImg->SetTextAlign('left','bottom');
	$x += $this->posx;
	$y -= $this->posy;

	$aImg->SetColor($this->fillcolor);
	$aImg->FilledPolygon($p);

	$aImg->SetColor($this->bordercolor);
	$aImg->Polygon($p,true);
	
	$aImg->SetColor($this->color);
	$aImg->SetFont($this->font_family,$this->font_style,$this->font_size);
	$aImg->StrokeText($x,$y,$this->t,0,'center');
    }

}

//===================================================
// CLASS SuperScriptText
// Description: Format a superscript text
//===================================================
class SuperScriptText extends Text {
    var $iSuper="";
    var $sfont_family="",$sfont_style="",$sfont_size=8;
    var $iSuperMargin=2,$iVertOverlap=4,$iSuperScale=0.65;
    var $iSDir=0;
    var $iSimple=false;

    function SuperScriptText($aTxt="",$aSuper="",$aXAbsPos=0,$aYAbsPos=0) {
	parent::Text($aTxt,$aXAbsPos,$aYAbsPos);
	$this->iSuper = $aSuper;
    }

    function FromReal($aVal,$aPrecision=2) {
	// Convert a floating point number to scientific notation
	$neg=1.0;
	if( $aVal < 0 ) {
	    $neg = -1.0;
	    $aVal = -$aVal;
	}
		
	$l = floor(log10($aVal));
	$a = sprintf("%0.".$aPrecision."f",round($aVal / pow(10,$l),$aPrecision));
	$a *= $neg;
	if( $this->iSimple && ($a == 1 || $a==-1) ) $a = '';
	
	if( $a != '' )
	    $this->t = $a.' * 10';
	else {
	    if( $neg == 1 )
		$this->t = '10';
	    else
		$this->t = '-10';
	}
	$this->iSuper = $l;
    }

    function Set($aTxt,$aSuper="") {
	$this->t = $aTxt;
	$this->iSuper = $aSuper;
    }

    function SetSuperFont($aFontFam,$aFontStyle=FS_NORMAL,$aFontSize=8) {
	$this->sfont_family = $aFontFam;
	$this->sfont_style = $aFontStyle;
	$this->sfont_size = $aFontSize;
    }

    // Total width of text
    function GetWidth(&$aImg) {
	$aImg->SetFont($this->font_family,$this->font_style,$this->font_size);
	$w = $aImg->GetTextWidth($this->t);
	$aImg->SetFont($this->sfont_family,$this->sfont_style,$this->sfont_size);
	$w += $aImg->GetTextWidth($this->iSuper);
	$w += $this->iSuperMargin;
	return $w;
    }
	
    // Hight of font (approximate the height of the text)
    function GetFontHeight(&$aImg) {
	$aImg->SetFont($this->font_family,$this->font_style,$this->font_size);	
	$h = $aImg->GetFontHeight();
	$aImg->SetFont($this->sfont_family,$this->sfont_style,$this->sfont_size);
	$h += $aImg->GetFontHeight();
	return $h;
    }

    // Hight of text
    function GetTextHeight(&$aImg) {
	$aImg->SetFont($this->font_family,$this->font_style,$this->font_size);
	$h = $aImg->GetTextHeight($this->t);
	$aImg->SetFont($this->sfont_family,$this->sfont_style,$this->sfont_size);
	$h += $aImg->GetTextHeight($this->iSuper);
	return $h;
    }

    function Stroke($aImg,$ax=-1,$ay=-1) {
	
        // To position the super script correctly we need different
	// cases to handle the alignmewnt specified since that will
	// determine how we can interpret the x,y coordinates
	
	$w = parent::GetWidth($aImg);
	$h = parent::GetTextHeight($aImg);
	switch( $this->valign ) {
	    case 'top':
		$sy = $this->y;
		break;
	    case 'center':
		$sy = $this->y - $h/2;
		break;
	    case 'bottom':
		$sy = $this->y - $h;
		break;
	    default:
		JpGraphError::Raise('PANIC: Internal error in SuperScript::Stroke(). Unknown vertical alignment for text');
		exit();
	}

	switch( $this->halign ) {
	    case 'left':
		$sx = $this->x + $w;
		break;
	    case 'center':
		$sx = $this->x + $w/2;
		break;
	    case 'right':
		$sx = $this->x;
		break;
	    default:
		JpGraphError::Raise('PANIC: Internal error in SuperScript::Stroke(). Unknown horizontal alignment for text');
		exit();
	}

	$sx += $this->iSuperMargin;
	$sy += $this->iVertOverlap;

	// Should we automatically determine the font or
	// has the user specified it explicetly?
	if( $this->sfont_family == "" ) {
	    if( $this->font_family <= FF_FONT2 ) {
		if( $this->font_family == FF_FONT0 ) {
		    $sff = FF_FONT0;
		}
		elseif( $this->font_family == FF_FONT1 ) {
		    if( $this->font_style == FS_NORMAL )
			$sff = FF_FONT0;
		    else
			$sff = FF_FONT1;
		}
		else {
		    $sff = FF_FONT1;
		}
		$sfs = $this->font_style;
		$sfz = $this->font_size;
	    }
	    else {
		// TTF fonts
		$sff = $this->font_family;
		$sfs = $this->font_style;
		$sfz = floor($this->font_size*$this->iSuperScale);		
		if( $sfz < 8 ) $sfz = 8;
	    }	    
	    $this->sfont_family = $sff;
	    $this->sfont_style = $sfs;
	    $this->sfont_size = $sfz;	    
	} 
	else {
	    $sff = $this->sfont_family;
	    $sfs = $this->sfont_style;
	    $sfz = $this->sfont_size;	    
	}

	parent::Stroke($aImg,$ax,$ay);


	// For the builtin fonts we need to reduce the margins
	// since the bounding bx reported for the builtin fonts
	// are much larger than for the TTF fonts.
	if( $sff <= FF_FONT2 ) {
	    $sx -= 2;
	    $sy += 3;
	}

	$aImg->SetTextAlign('left','bottom');	
	$aImg->SetFont($sff,$sfs,$sfz);
	$aImg->PushColor($this->color);	
	$aImg->StrokeText($sx,$sy,$this->iSuper,$this->iSDir,'left');
	$aImg->PopColor();	
    }
}


//===================================================
// CLASS Grid
// Description: responsible for drawing grid lines in graph
//===================================================
class Grid {
    var $img;
    var $scale;
    var $grid_color='#DDDDDD',$grid_mincolor='#DDDDDD';
    var $type="solid";
    var $show=false, $showMinor=false,$weight=1;
    var $fill=false,$fillcolor=array('#EFEFEF','#BBCCFF');
//---------------
// CONSTRUCTOR
    function Grid(&$aAxis) {
	$this->scale = &$aAxis->scale;
	$this->img = &$aAxis->img;
    }
//---------------
// PUBLIC METHODS
    function SetColor($aMajColor,$aMinColor=false) {
	$this->grid_color=$aMajColor;
	if( $aMinColor === false ) 
	    $aMinColor = $aMajColor ;
	$this->grid_mincolor = $aMinColor;
    }
	
    function SetWeight($aWeight) {
	$this->weight=$aWeight;
    }
	
    // Specify if grid should be dashed, dotted or solid
    function SetLineStyle($aType) {
	$this->type = $aType;
    }
	
    // Decide if both major and minor grid should be displayed
    function Show($aShowMajor=true,$aShowMinor=false) {
	$this->show=$aShowMajor;
	$this->showMinor=$aShowMinor;
    }
    
    function SetFill($aFlg=true,$aColor1='lightgray',$aColor2='lightblue') {
	$this->fill = $aFlg;
	$this->fillcolor = array( $aColor1, $aColor2 );
    }
	
    // Display the grid
    function Stroke() {
	if( $this->showMinor ) {
	    $tmp = $this->grid_color;
	    $this->grid_color = $this->grid_mincolor;
	    $this->DoStroke($this->scale->ticks->ticks_pos);

	    $this->grid_color = $tmp;
	    $this->DoStroke($this->scale->ticks->maj_ticks_pos);
	}
	else {
	    $this->DoStroke($this->scale->ticks->maj_ticks_pos);
	}
    }
	
//--------------
// Private methods	
    // Draw the grid
    function DoStroke(&$aTicksPos) {
	if( !$this->show )
	    return;	
	$nbrgrids = count($aTicksPos);	

	if( $this->scale->type=="y" ) {
	    $xl=$this->img->left_margin;
	    $xr=$this->img->width-$this->img->right_margin;
	    
	    if( $this->fill ) {
		// Draw filled areas
		$y2 = $aTicksPos[0];
		$i=1;
		while( $i < $nbrgrids ) {
		    $y1 = $y2;
		    $y2 = $aTicksPos[$i++];
		    $this->img->SetColor($this->fillcolor[$i & 1]);
		    $this->img->FilledRectangle($xl,$y1,$xr,$y2);
		}
	    }

	    $this->img->SetColor($this->grid_color);
	    $this->img->SetLineWeight($this->weight);

	    // Draw grid lines
	    for($i=0; $i<$nbrgrids; ++$i) {
		$y=$aTicksPos[$i];
		if( $this->type == "solid" )
		    $this->img->Line($xl,$y,$xr,$y);
		elseif( $this->type == "dotted" )
		    $this->img->DashedLine($xl,$y,$xr,$y,1,6);
		elseif( $this->type == "dashed" )
		    $this->img->DashedLine($xl,$y,$xr,$y,2,4);
		elseif( $this->type == "longdashed" )
		    $this->img->DashedLine($xl,$y,$xr,$y,8,6);
	    }
	}
	elseif( $this->scale->type=="x" ) {	
	    $yu=$this->img->top_margin;
	    $yl=$this->img->height-$this->img->bottom_margin;
	    $limit=$this->img->width-$this->img->right_margin;

	    if( $this->fill ) {
		// Draw filled areas
		$x2 = $aTicksPos[0];
		$i=1;
		while( $i < $nbrgrids ) {
		    $x1 = $x2;
		    $x2 = min($aTicksPos[$i++],$limit) ;
		    $this->img->SetColor($this->fillcolor[$i & 1]);
		    $this->img->FilledRectangle($x1,$yu,$x2,$yl);
		}
	    }

	    $this->img->SetColor($this->grid_color);
	    $this->img->SetLineWeight($this->weight);

	    // We must also test for limit since we might have
	    // an offset and the number of ticks is calculated with
	    // assumption offset==0 so we might end up drawing one
	    // to many gridlines
	    $i=0;
	    $x=$aTicksPos[$i];	    
	    while( $i<count($aTicksPos) && ($x=$aTicksPos[$i]) <= $limit ) {
		if( $this->type == "solid" )				
		    $this->img->Line($x,$yl,$x,$yu);
		elseif( $this->type == "dotted" )
		    $this->img->DashedLine($x,$yl,$x,$yu,1,6);
		elseif( $this->type == "dashed" )
		    $this->img->DashedLine($x,$yl,$x,$yu,2,4);
		elseif( $this->type == "longdashed" )
		    $this->img->DashedLine($x,$yl,$x,$yu,8,6);	
		++$i;  
	    }
	}	
	else {
	    JpGraphError::Raise('Internal error: Unknown grid axis ['.$this->scale->type.']');
	}
	return true;
    }
} // Class

//===================================================
// CLASS Axis
// Description: Defines X and Y axis. Notes that at the
// moment the code is not really good since the axis on
// several occasion must know wheter it's an X or Y axis.
// This was a design decision to make the code easier to
// follow. 
//===================================================
class Axis {
    var $pos = false;
    var $weight=1;
    var $color=array(0,0,0),$label_color=array(0,0,0);
    var $img=null,$scale=null; 
    var $hide=false;
    var $ticks_label=false, $ticks_label_colors=null;
    var $show_first_label=true,$show_last_label=true;
    var $label_step=1; // Used by a text axis to specify what multiple of major steps
    // should be labeled.
    var $tick_step=1;
    var $labelPos=0;   // Which side of the axis should the labels be?
    var $title=null,$title_adjust,$title_margin,$title_side=SIDE_LEFT;
    var $font_family=FF_FONT1,$font_style=FS_NORMAL,$font_size=12,$label_angle=0;
    var $tick_label_margin=5;
    var $label_halign = '',$label_valign = '', $label_para_align='left';
    var $hide_line=false,$hide_labels=false;
    //var $hide_zero_label=false;

//---------------
// CONSTRUCTOR
    function Axis(&$img,&$aScale,$color=array(0,0,0)) {
	$this->img = &$img;
	$this->scale = &$aScale;
	$this->color = $color;
	$this->title=new Text("");
		
	if( $aScale->type=="y" ) {
	    $this->title_margin = 25;
	    $this->title_adjust="middle";
	    $this->title->SetOrientation(90);
	    $this->tick_label_margin=7;
	    $this->labelPos=SIDE_LEFT;
	    //$this->SetLabelFormat('%.1f');
	}
	else {
	    $this->title_margin = 5;
	    $this->title_adjust="high";
	    $this->title->SetOrientation(0);			
	    $this->tick_label_margin=3;
	    $this->labelPos=SIDE_DOWN;
	    //$this->SetLabelFormat('%.0f');
	}
    }
//---------------
// PUBLIC METHODS	
	
    function SetLabelFormat($aFormStr) {
	$this->scale->ticks->SetLabelFormat($aFormStr);
    }
	
    function SetLabelFormatString($aFormStr) {
	$this->scale->ticks->SetLabelFormat($aFormStr);
    }
	
    function SetLabelFormatCallback($aFuncName) {
	$this->scale->ticks->SetFormatCallback($aFuncName);
    }

    function SetLabelAlign($aHAlign,$aVAlign="top",$aParagraphAlign='left') {
	$this->label_halign = $aHAlign;
	$this->label_valign = $aVAlign;
	$this->label_para_align = $aParagraphAlign;
    }		

    // Don't display the first label
    function HideFirstTickLabel($aShow=false) {
	$this->show_first_label=$aShow;
    }

    function HideLastTickLabel($aShow=false) {
	$this->show_last_label=$aShow;
    }

    function HideTicks($aHideMinor=true,$aHideMajor=true) {
	$this->scale->ticks->SupressMinorTickMarks($aHideMinor);
	$this->scale->ticks->SupressTickMarks($aHideMajor);
    }

    // Hide zero label
    function HideZeroLabel($aFlag=true) {
	$this->scale->ticks->SupressZeroLabel();
	//$this->hide_zero_label = $aFlag;
    }
	
    function HideFirstLastLabel() {
	// The two first calls to ticks method will supress 
	// automatically generated scale values. However, that
	// will not affect manually specified value, e.g text-scales.
	// therefor we also make a kludge here to supress manually
	// specified scale labels.
	$this->scale->ticks->SupressLast();
	$this->scale->ticks->SupressFirst();
	$this->show_first_label	= false;
	$this->show_last_label = false;
    }
	
    // Hide the axis
    function Hide($aHide=true) {
	$this->hide=$aHide;
    }

    // Hide the actual axis-line, but still print the labels
    function HideLine($aHide=true) {
	$this->hide_line = $aHide;
    }

    function HideLabels($aHide=true) {
	$this->hide_labels = $aHide;
    }
    

    // Weight of axis
    function SetWeight($aWeight) {
	$this->weight = $aWeight;
    }

    // Axis color
    function SetColor($aColor,$aLabelColor=false) {
	$this->color = $aColor;
	if( !$aLabelColor ) $this->label_color = $aColor;
	else $this->label_color = $aLabelColor;
    }
	
    // Title on axis
    function SetTitle($aTitle,$aAdjustAlign="high") {
	$this->title->Set($aTitle);
	$this->title_adjust=$aAdjustAlign;
    }
	
    // Specify distance from the axis
    function SetTitleMargin($aMargin) {
	$this->title_margin=$aMargin;
    }
	
    // Which side of the axis should the axis title be?
    function SetTitleSide($aSideOfAxis) {
	$this->title_side = $aSideOfAxis;
    }

    // Utility function to set the direction for tick marks
    function SetTickDirection($aDir) {
    	// Will be deprecated from 1.7    	
    	if( ERR_DEPRECATED )
	    JpGraphError::Raise('Axis::SetTickDirection() is deprecated. Use Axis::SetTickSide() instead');
	$this->scale->ticks->SetSide($aDir);
    }
    
    function SetTickSide($aDir) {
	$this->scale->ticks->SetSide($aDir);
    }
	
    // Specify text labels for the ticks. One label for each data point
    function SetTickLabels($aLabelArray,$aLabelColorArray=null) {
	$this->ticks_label = $aLabelArray;
	$this->ticks_label_colors = $aLabelColorArray;
    }
	
    // How far from the axis should the labels be drawn
    function SetTickLabelMargin($aMargin) {
	if( ERR_DEPRECATED )    	
	    JpGraphError::Raise('SetTickLabelMargin() is deprecated. Use Axis::SetLabelMargin() instead.');
      	$this->tick_label_margin=$aMargin;
    }

    function SetLabelMargin($aMargin) {
	$this->tick_label_margin=$aMargin;
    }
	
    // Specify that every $step of the ticks should be displayed starting
    // at $start
    // DEPRECATED FUNCTION: USE SetTextTickInterval() INSTEAD
    function SetTextTicks($step,$start=0) {
	JpGraphError::Raise(" SetTextTicks() is deprecated. Use SetTextTickInterval() instead.");		
    }

    // Specify that every $step of the ticks should be displayed starting
    // at $start	
    function SetTextTickInterval($aStep,$aStart=0) {
	$this->scale->ticks->SetTextLabelStart($aStart);
	$this->tick_step=$aStep;
    }
	 
    // Specify that every $step tick mark should have a label 
    // should be displayed starting
    function SetTextLabelInterval($aStep) {
	if( $aStep < 1 )
	    JpGraphError::Raise(" Text label interval must be specified >= 1.");
	$this->label_step=$aStep;
    }
	
    // Which side of the axis should the labels be on?
    function SetLabelPos($aSidePos) {
    	// This will be deprecated from 1.7
	if( ERR_DEPRECATED )    	
	    JpGraphError::Raise('SetLabelPos() is deprecated. Use Axis::SetLabelSide() instead.');
	$this->labelPos=$aSidePos;
    }
    
    function SetLabelSide($aSidePos) {
	$this->labelPos=$aSidePos;
    }

    // Set the font
    function SetFont($aFamily,$aStyle=FS_NORMAL,$aSize=10) {
	$this->font_family = $aFamily;
	$this->font_style = $aStyle;
	$this->font_size = $aSize;
    }

    // Position for axis line on the "other" scale
    function SetPos($aPosOnOtherScale) {
	$this->pos=$aPosOnOtherScale;
    }
	
    // Specify the angle for the tick labels
    function SetLabelAngle($aAngle) {
	$this->label_angle = $aAngle;
    }	
	
    // Stroke the axis.
    function Stroke($aOtherAxisScale) {		
	if( $this->hide ) return;		
	if( is_numeric($this->pos) ) {
	    $pos=$aOtherAxisScale->Translate($this->pos);
	}
	else {	// Default to minimum of other scale if pos not set		
	    if( ($aOtherAxisScale->GetMinVal() >= 0 && $this->pos==false) || $this->pos=="min" ) {
		$pos = $aOtherAxisScale->scale_abs[0];
	    }
	    elseif($this->pos == "max") {
		$pos = $aOtherAxisScale->scale_abs[1];
	    }
	    else { // If negative set x-axis at 0
		$this->pos=0;
		$pos=$aOtherAxisScale->Translate(0);
	    }
	}	
	$this->img->SetLineWeight($this->weight);
	$this->img->SetColor($this->color);		
	$this->img->SetFont($this->font_family,$this->font_style,$this->font_size);
	if( $this->scale->type == "x" ) {
	    if( !$this->hide_line ) 
		$this->img->FilledRectangle($this->img->left_margin,$pos,
					    $this->img->width-$this->img->right_margin,$pos+$this->weight-1);
	    $y=$pos+$this->img->GetFontHeight()+$this->title_margin+$this->title->margin;
	    if( $this->title_adjust=="high" )
		$this->title->Pos($this->img->width-$this->img->right_margin,$y,"right","top");
	    elseif( $this->title_adjust=="middle" || $this->title_adjust=="center" ) 
		$this->title->Pos(($this->img->width-$this->img->left_margin-$this->img->right_margin)/2+$this->img->left_margin,$y,"center","top");
	    elseif($this->title_adjust=="low")
		$this->title->Pos($this->img->left_margin,$y,"left","top");
	    else {	
		JpGraphError::Raise('Unknown alignment specified for X-axis title. ('.$this->title_adjust.')');
	    }
	}
	elseif( $this->scale->type == "y" ) {
	    // Add line weight to the height of the axis since
	    // the x-axis could have a width>1 and we want the axis to fit nicely together.
	    if( !$this->hide_line ) 
		$this->img->FilledRectangle($pos-$this->weight+1,$this->img->top_margin,
					    $pos,$this->img->height-$this->img->bottom_margin+$this->weight-1);
	    $x=$pos ;
	    if( $this->title_side == SIDE_LEFT ) {
		$x -= $this->title_margin;
		$x -= $this->title->margin;
		$halign="right";
	    }
	    else {
		$x += $this->title_margin;
		$x += $this->title->margin;
		$halign="left";
	    }
	    // If the user has manually specified an hor. align
	    // then we override the automatic settings with this
	    // specifed setting. Since default is 'left' we compare
	    // with that. (This means a manually set 'left' align
	    // will have no effect.)
	    if( $this->title->halign != 'left' ) 
		$halign = $this->title->halign;
	    if( $this->title_adjust=="high" ) 
		$this->title->Pos($x,$this->img->top_margin,$halign,"top"); 
	    elseif($this->title_adjust=="middle" || $this->title_adjust=="center")  
		$this->title->Pos($x,($this->img->height-$this->img->top_margin-$this->img->bottom_margin)/2+$this->img->top_margin,$halign,"center");
	    elseif($this->title_adjust=="low")
		$this->title->Pos($x,$this->img->height-$this->img->bottom_margin,$halign,"bottom");
	    else	
		JpGraphError::Raise('Unknown alignment specified for Y-axis title. ('.$this->title_adjust.')');
		
	}
	$this->scale->ticks->Stroke($this->img,$this->scale,$pos);
	if( !$this->hide_labels ) {
	    $this->StrokeLabels($pos);
	}
	$this->title->Stroke($this->img);
    }

//---------------
// PRIVATE METHODS	
    // Draw all the tick labels on major tick marks
    function StrokeLabels($aPos,$aMinor=false,$aAbsLabel=false) {

	$this->img->SetColor($this->label_color);
	$this->img->SetFont($this->font_family,$this->font_style,$this->font_size);
	$yoff=$this->img->GetFontHeight()/2;

	// Only draw labels at major tick marks
	$nbr = count($this->scale->ticks->maj_ticks_label);

	// We have the option to not-display the very first mark
	// (Usefull when the first label might interfere with another
	// axis.)
	$i = $this->show_first_label ? 0 : 1 ;
	if( !$this->show_last_label ) --$nbr;
	// Now run through all labels making sure we don't overshoot the end
	// of the scale.	
	$ncolor=0;
	if( isset($this->ticks_label_colors) )
	    $ncolor=count($this->ticks_label_colors);
	
	while( $i<$nbr ) {
	    // $tpos holds the absolute text position for the label
	    $tpos=$this->scale->ticks->maj_ticklabels_pos[$i];

	    // Note. the $limit is only used for the x axis since we
	    // might otherwise overshoot if the scale has been centered
	    // This is due to us "loosing" the last tick mark if we center.
	    if( $this->scale->type=="x" && $tpos > $this->img->width-$this->img->right_margin+1 ) {
	    	return; 
	    }
	    // we only draw every $label_step label
	    if( ($i % $this->label_step)==0 ) {

		// Set specific label color if specified
		if( $ncolor > 0 )
		    $this->img->SetColor($this->ticks_label_colors[$i % $ncolor]);
		
		// If the label has been specified use that and in other case
		// just label the mark with the actual scale value 
		$m=$this->scale->ticks->GetMajor();
				
		// ticks_label has an entry for each data point and is the array
		// that holds the labels set by the user. If the user hasn't 
		// specified any values we use whats in the automatically asigned
		// labels in the maj_ticks_label
		if( isset($this->ticks_label[$i*$m]) )
		    $label=$this->ticks_label[$i*$m];
		else {
		    if( $aAbsLabel ) 
			$label=abs($this->scale->ticks->maj_ticks_label[$i]);
		    else
			$label=$this->scale->ticks->maj_ticks_label[$i];
		    if( $this->scale->textscale ) {
			++$label;
			
		    }
		}
					
		//if( $this->hide_zero_label && $label==0.0 ) {
		//	++$i;
		//	continue;
		//}					
					
		if( $this->scale->type == "x" ) {
		    if( $this->labelPos == SIDE_DOWN ) {
			if( $this->label_angle==0 || $this->label_angle==90 ) {
			    if( $this->label_halign=='' && $this->label_valign=='')
				$this->img->SetTextAlign('center','top');
			    else
			    	$this->img->SetTextAlign($this->label_halign,$this->label_valign);
			    
			}
			else {
			    if( $this->label_halign=='' && $this->label_valign=='')
				$this->img->SetTextAlign("right","top");
			    else
				$this->img->SetTextAlign($this->label_halign,$this->label_valign);
			}

			$this->img->StrokeText($tpos,$aPos+$this->tick_label_margin,$label,
					       $this->label_angle,$this->label_para_align);
		    }
		    else {
			if( $this->label_angle==0 || $this->label_angle==90 ) {
			    if( $this->label_halign=='' && $this->label_valign=='')
				$this->img->SetTextAlign("center","bottom");
			    else
			    	$this->img->SetTextAlign($this->label_halign,$this->label_valign);
			}
			else {
			    if( $this->label_halign=='' && $this->label_valign=='')
				$this->img->SetTextAlign("right","bottom");
			    else
			    	$this->img->SetTextAlign($this->label_halign,$this->label_valign);
			}
			$this->img->StrokeText($tpos,$aPos-$this->tick_label_margin,$label,
					       $this->label_angle,$this->label_para_align);
		    }
		}
		else {
		    // scale->type == "y"
		    //if( $this->label_angle!=0 ) 
		    //JpGraphError::Raise(" Labels at an angle are not supported on Y-axis");
		    if( $this->labelPos == SIDE_LEFT ) { // To the left of y-axis					
			if( $this->label_halign=='' && $this->label_valign=='')	
			    $this->img->SetTextAlign("right","center");
			else
			    $this->img->SetTextAlign($this->label_halign,$this->label_valign);
			$this->img->StrokeText($aPos-$this->tick_label_margin,$tpos,$label,$this->label_angle,$this->label_para_align);	
		    }
		    else { // To the right of the y-axis
			if( $this->label_halign=='' && $this->label_valign=='')	
			    $this->img->SetTextAlign("left","center");
			else
			    $this->img->SetTextAlign($this->label_halign,$this->label_valign);
			$this->img->StrokeText($aPos+$this->tick_label_margin,$tpos,$label,$this->label_angle,$this->label_para_align);	
		    }
		}
	    }
	    ++$i;	
	}								
    }			

} // Class

//===================================================
// CLASS Ticks
// Description: Abstract base class for drawing linear and logarithmic
// tick marks on axis
//===================================================
class Ticks {
    var $minor_abs_size=3, $major_abs_size=5;
    var $direction=1; // Should ticks be in(=1) the plot area or outside (=-1)?
    var $scale;
    var $is_set=false;
    var $precision;
    var $supress_zerolabel=false,$supress_first=false;
    var $supress_last=false,$supress_tickmarks=false,$supress_minor_tickmarks=false;
    var $mincolor="",$majcolor="";
    var $weight=1;
    var $label_formatstr='';   // C-style format string to use for labels
    var $label_formfunc='';


//---------------
// CONSTRUCTOR
    function Ticks(&$aScale) {
	$this->scale=&$aScale;
	$this->precision = -1;
    }

//---------------
// PUBLIC METHODS	
    // Set format string for automatic labels
    function SetLabelFormat($aFormatString) {
	$this->label_formatstr=$aFormatString;
    }
	
    function SetFormatCallback($aCallbackFuncName) {
	$this->label_formfunc = $aCallbackFuncName;
    }
	
    // Don't display the first zero label
    function SupressZeroLabel($aFlag=true) {
	$this->supress_zerolabel=$aFlag;
    }
	
    // Don't display minor tick marks
    function SupressMinorTickMarks($aHide=true) {
	$this->supress_minor_tickmarks=$aHide;
    }
	
    // Don't display major tick marks
    function SupressTickMarks($aHide=true) {
	$this->supress_tickmarks=$aHide;
    }
	
    // Hide the first tick mark
    function SupressFirst($aHide=true) {
	$this->supress_first=$aHide;
    }
	
    // Hide the last tick mark
    function SupressLast($aHide=true) {
	$this->supress_last=$aHide;
    }

    // Size (in pixels) of minor tick marks
    function GetMinTickAbsSize() {
	return $this->minor_abs_size;
    }
	
    // Size (in pixels) of major tick marks
    function GetMajTickAbsSize() {
	return $this->major_abs_size;		
    }
	
    function SetSize($aMajSize,$aMinSize=3) {
	$this->major_abs_size = $aMajSize;		
	$this->minor_abs_size = $aMinSize;		
    }

    // Have the ticks been specified
    function IsSpecified() {
	return $this->is_set;
    }
	
    // Set the distance between major and minor tick marks
    function Set($aMaj,$aMin) {
	// "Virtual method"
	// Should be implemented by the concrete subclass
	// if any action is wanted.
    }
	
    // Specify number of decimals in automatic labels
    // Deprecated from 1.4. Use SetFormatString() instead
    function SetPrecision($aPrecision) { 	
    	if( ERR_DEPRECATED )
	    JpGraphError::Raise('Ticks::SetPrecision() is deprecated. Use Ticks::SetLabelFormat() (or Ticks::SetFormatCallback()) instead');
	$this->precision=$aPrecision;
    }

    function SetSide($aSide) {
	$this->direction=$aSide;
    }
	
    // Which side of the axis should the ticks be on
    function SetDirection($aSide=SIDE_RIGHT) {
	$this->direction=$aSide;
    }
	
    // Set colors for major and minor tick marks
    function SetMarkColor($aMajorColor,$aMinorColor="") {
	$this->SetColor($aMajorColor,$aMinorColor);
    }
    
    function SetColor($aMajorColor,$aMinorColor="") {
	$this->majcolor=$aMajorColor;
		
	// If not specified use same as major
	if( $aMinorColor=="" ) 
	    $this->mincolor=$aMajorColor;
	else
	    $this->mincolor=$aMinorColor;
    }
	
    function SetWeight($aWeight) {
	$this->weight=$aWeight;
    }
	
} // Class

//===================================================
// CLASS LinearTicks
// Description: Draw linear ticks on axis
//===================================================
class LinearTicks extends Ticks {
    var $minor_step=1, $major_step=2;
    var $xlabel_offset=0,$xtick_offset=0;
    var $label_offset=0; // What offset should the displayed label have
    // i.e should we display 0,1,2 or 1,2,3,4 or 2,3,4 etc
    var $text_label_start=0;
//---------------
// CONSTRUCTOR
    function LinearTicks() {
	$this->precision = -1;
    }

//---------------
// PUBLIC METHODS	
	
	
    // Return major step size in world coordinates
    function GetMajor() {
	return $this->major_step;
    }
	
    // Return minor step size in world coordinates
    function GetMinor() {
	return $this->minor_step;
    }
	
    // Set Minor and Major ticks (in world coordinates)
    function Set($aMajStep,$aMinStep=false) {
	if( $aMinStep==false ) 
	    $aMinStep=$aMajStep;
    	
	if( $aMajStep <= 0 || $aMinStep <= 0 ) {
	    JpGraphError::Raise(" Minor or major step size is 0. Check that you haven't
				got an accidental SetTextTicks(0) in your code.<p>
				If this is not the case you might have stumbled upon a bug in JpGraph.
				Please report this and if possible include the data that caused the
				problem.");
	}
		
	$this->major_step=$aMajStep;
	$this->minor_step=$aMinStep;
	$this->is_set = true;
    }

    // Draw linear ticks
    function Stroke(&$img,&$scale,$pos) {
	$maj_step_abs = $scale->scale_factor*$this->major_step;		
	$min_step_abs = $scale->scale_factor*$this->minor_step;		

	if( $min_step_abs==0 || $maj_step_abs==0 ) 
	    JpGraphError::Raise(" A plot has an illegal scale. This could for example be that you are trying to use text autoscaling to draw a line plot with only one point or that the plot area is too small. Try increasing the graph size or correct the lineplot.");
	$limit = $scale->scale_abs[1];	
	$nbrmajticks=floor(1.000001*(($scale->GetMaxVal()-$scale->GetMinVal())/$this->major_step))+1;
	$first=0;
		
	// If precision hasn't been specified set it to a sensible value
	if( $this->precision==-1 ) { 
	    $t = log10($this->minor_step);
	    if( $t > 0 )
		$precision = 0;
	    else
		$precision = -floor($t);
	}
	else
	    $precision = $this->precision;
			
	$img->SetLineWeight($this->weight);			
		
	// Handle ticks on X-axis
	if( $scale->type == "x" ) {

	    // Draw the minor tick marks
			
	    $yu = $pos - $this->direction*$this->GetMinTickAbsSize();
	    $label = $scale->GetMinVal();	    
	    $x=$scale->scale_abs[0];
	    $i=0;
	    $j=0;
	    $step = round($maj_step_abs/$min_step_abs);
	    while( $x < $limit ) {
		$this->ticks_pos[]=$x;
		$this->ticks_label[]=$label;
		$label+=$this->minor_step;
 		if( !$this->supress_tickmarks && !$this->supress_minor_tickmarks) {
		    if( $this->mincolor!="" ) $img->PushColor($this->mincolor);
		    $img->Line($x,$pos,$x,$yu); 
		    if( $this->mincolor!="" ) $img->PopColor();
		}

		if( $i % $step == 0 ) {
		    $this->maj_ticks_pos[$j]=round($x);//$xtick;				
		    ++$j;
		}

		++$i;
		$x += $min_step_abs;
		
	    }
	    $this->maj_ticks_pos[$j]=$x;

	    // Draw the major tick marks
			
	    $yu = $pos - $this->direction*$this->GetMajTickAbsSize();
			
	    $label = (float)$scale->GetMinVal()+$this->text_label_start+$this->label_offset;	
			
	    $start_abs=$scale->scale_factor*$this->text_label_start;
			
	    $nbrmajticks=ceil(($scale->GetMaxVal()-$scale->GetMinVal()-$this->text_label_start )/$this->major_step)+1;	
	    
	    
	    $x = $scale->scale_abs[0]+$start_abs+$this->xlabel_offset*$min_step_abs;	
	    for( $i=0; $label<=$scale->GetMaxVal()+$this->label_offset; ++$i ) {

		// Apply format
		if( $this->label_formfunc != "" ) {
		    $f=$this->label_formfunc;
		    $l = $f($label);
		}	
		elseif( $this->label_formatstr != "" ) 
		    $l = sprintf($this->label_formatstr,$label);
		else {
		    $v = round($label,$precision);
		    $l = sprintf("%01.".$precision."f",$v);
		}
					
		if( ($this->supress_zerolabel && $l==0) || 
		    ($this->supress_first && $i==0) ||
		    ($this->supress_last  && $i==$nbrmajticks-1) ) {
		    $l="";
		}

		$this->maj_ticks_label[$i]=$l;
		$label+=$this->major_step;
		$this->maj_ticklabels_pos[$i] = $x;				
//		$this->maj_ticklabels_pos[$i] = $this->maj_ticks_pos[$i];

		// The x-position of the tick marks can be different from the labels.
		// Note that we record the tick position (not the label) so that the grid
		// happen upon tick marks and not labels.
		$xtick=$scale->scale_abs[0]+$start_abs+$this->xtick_offset*$min_step_abs+$i*$maj_step_abs;
		$this->maj_ticks_pos[$i]=$xtick;
		if(!($this->xtick_offset > 0 && $i==$nbrmajticks-1) && 
		   !$this->supress_tickmarks) {
		    if( $this->majcolor!="" ) $img->PushColor($this->majcolor);
		    $img->Line($this->maj_ticks_pos[$i],$pos,$this->maj_ticks_pos[$i],$yu);
		    if( $this->majcolor!="" ) $img->PopColor();
		}

		$x += $maj_step_abs;
	    }


	}
	elseif( $scale->type == "y" ) {

	    // Draw the major tick marks
	    $xr = $pos + $this->direction*$this->GetMajTickAbsSize();
	    $label = $scale->GetMinVal();
	    
	    $tmpmaj=array();
	    $tmpmin=array();

	    for( $i=0; $i<$nbrmajticks; ++$i) {
		$y=$scale->scale_abs[0]+$i*$maj_step_abs;				

		$tmpmaj[]=$y;

	
		// THe following two lines might seem to be unecessary but they are not!
		// The reason being that for X-axis we separate the position of the labels
		// and the tick marks which we don't do for the Y-axis.
		// We therefore need to make sure both arrays are corcectly filled
		// since Axis::StrokeLabels() uses the label positions and Grid::Stroke() uses
		// the tick positions.
		$this->maj_ticklabels_pos[$i]=$y;
		$this->maj_ticks_pos[$i]=$y;
		
		if( $this->label_formfunc != "" ) {
		    $f=$this->label_formfunc;
		    $l = $f($label);
		}	
		elseif( $this->label_formatstr != "" ) 
		    $l = sprintf($this->label_formatstr,$label);
		else
		    $l = sprintf("%01.".$precision."f",round($label,$precision));
								
		if( ($this->supress_zerolabel && ($l + 0)==0) ||  ($this->supress_first && $i==0) ||
		    ($this->supress_last  && $i==$nbrmajticks-1) ) {
		    $l="";
		}
		
		$this->maj_ticks_label[$i]=$l; 
		$label+=$this->major_step;	
		if( !$this->supress_tickmarks ) {
		    if( $this->majcolor!="" ) $img->PushColor($this->majcolor);
		    $img->Line($pos,$y,$xr,$y);	
		    if( $this->majcolor!="" ) $img->PopColor();
		}
	    }

	    // Draw the minor tick marks
	    $xr = $pos + $this->direction*$this->GetMinTickAbsSize();
	    $label = $scale->GetMinVal();	
	    for( $i=0,$y=$scale->scale_abs[0]; $y>=$limit; ) {

		$tmpmin[]=$y;

		$this->ticks_pos[$i]=$y;
		$this->ticks_label[$i]=$label;				
		$label+=$this->minor_step;				
		if( !$this->supress_tickmarks && !$this->supress_minor_tickmarks)	{
		    if( $this->mincolor!="" ) $img->PushColor($this->mincolor);
		    $img->Line($pos,$y,$xr,$y);
		    if( $this->mincolor!="" ) $img->PopColor();
		}
		++$i;
		$y=$scale->scale_abs[0]+$i*$min_step_abs;
	    }	
	}	
    }
//---------------
// PRIVATE METHODS
    // Spoecify the offset of the displayed tick mark with the tick "space"
    // Legal values for $o is [0,1] used to adjust where the tick marks and label 
    // should be positioned within the major tick-size
    // $lo specifies the label offset and $to specifies the tick offset
    // this comes in handy for example in bar graphs where we wont no offset for the
    // tick but have the labels displayed halfway under the bars.
    function SetXLabelOffset($aLabelOff,$aTickOff=-1) {
	$this->xlabel_offset=$aLabelOff;
	if( $aTickOff==-1 )	// Same as label offset
	    $this->xtick_offset=$aLabelOff;
	else
	    $this->xtick_offset=$aTickOff;
	if( $aLabelOff>0 )
	    $this->SupressLast();	// The last tick wont fit
    }

    // Which tick label should we start with?
    function SetTextLabelStart($aTextLabelOff) {
	$this->text_label_start=$aTextLabelOff;
    }
	
} // Class

//===================================================
// CLASS LinearScale
// Description: Handle linear scaling between screen and world 
//===================================================
class LinearScale {
    var $scale=array(0,0);
    var $scale_abs=array(0,0);
    var $scale_factor; // Scale factor between world and screen
    var $world_size;	// Plot area size in world coordinates
    var $world_abs_size; // Plot area size in pixels
    var $off; // Offset between image edge and plot area
    var $type; // is this x or y scale ?
    var $ticks=null; // Store ticks
    var $autoscale_min=false; // Forced minimum value, auto determine max
    var $autoscale_max=false; // Forced maximum value, auto determine min
    var $gracetop=0,$gracebottom=0;
    var $intscale=false; // Restrict autoscale to integers
    var $textscale=false; // Just a flag to let the Plot class find out if
    // we are a textscale or not. This is a cludge since
    // this ionformatyion is availabale in Graph::axtype but
    // we don't have access to the graph object in the Plots
    // stroke method. So we let graph store the status here
    // when the linear scale is created. A real cludge...
    var $text_scale_off = 0;
    var $auto_ticks=false; // When using manual scale should the ticks be automatically set?
    var $name = 'lin';
//---------------
// CONSTRUCTOR
    function LinearScale($aMin=0,$aMax=0,$aType="y") {
	assert($aType=="x" || $aType=="y" );
	assert($aMin<=$aMax);
		
	$this->type=$aType;
	$this->scale=array($aMin,$aMax);		
	$this->world_size=$aMax-$aMin;	
	$this->ticks = new LinearTicks();
    }

//---------------
// PUBLIC METHODS	
    // Second phase constructor
    function Init(&$aImg) {
	$this->InitConstants($aImg);	
	// We want image to notify us when the margins changes so we 
	// can recalculate the constants.
	// PHP <= 4.04 BUGWARNING: IT IS IMPOSSIBLE TO DO THIS IN THE CONSTRUCTOR
	// SINCE (FOR SOME REASON) IT IS IMPOSSIBLE TO PASS A REFERENCE
	// TO 'this' INSTEAD IT WILL ADD AN ANONYMOUS COPY OF THIS OBJECT WHICH WILL
	// GET ALL THE NOTIFICATIONS. (This took a while to track down...)
		
	// Add us as an observer to class Image
	$aImg->AddObserver("InitConstants",$this);
    }
	
    // Check if scale is set or if we should autoscale
    // We should do this is either scale or ticks has not been set
    function IsSpecified() {
	if( $this->GetMinVal()==$this->GetMaxVal() ) {		// Scale not set
	    return false;
	}
	return true;
    }
	
    // Set the minimum data value when the autoscaling is used. 
    // Usefull if you want a fix minimum (like 0) but have an
    // automatic maximum
    function SetAutoMin($aMin) {
	$this->autoscale_min=$aMin;
    }

    // Set the minimum data value when the autoscaling is used. 
    // Usefull if you want a fix minimum (like 0) but have an
    // automatic maximum
    function SetAutoMax($aMax) {
	$this->autoscale_max=$aMax;
    }

    // If the user manually specifies a scale should the ticks
    // still be set automatically?
    function SetAutoTicks($aFlag=true) {
	$this->auto_ticks = $aFlag;
    }

    // Specify scale "grace" value (top and bottom)
    function SetGrace($aGraceTop,$aGraceBottom=0) {
	if( $aGraceTop<0 || $aGraceBottom < 0  )
	    JpGraphError::Raise(" Grace must be larger then 0");
	$this->gracetop=$aGraceTop;
	$this->gracebottom=$aGraceBottom;
    }
	
    // Get the minimum value in the scale
    function GetMinVal() {
	return $this->scale[0];
    }
	
    // get maximum value for scale
    function GetMaxVal() {
	return $this->scale[1];
    }
		
    // Specify a new min/max value for sclae	
    function Update(&$aImg,$aMin,$aMax) {
	$this->scale=array($aMin,$aMax);		
	$this->world_size=$aMax-$aMin;		
	$this->InitConstants($aImg);					
    }
	
    // Translate between world and screen
    function Translate($aCoord) {
	return $this->off+($aCoord - $this->GetMinVal()) * $this->scale_factor; 
    }
	
    // Relative translate (don't include offset) usefull when we just want
    // to know the relative position (in pixels) on the axis
    function RelTranslate($aCoord) {
	return ($aCoord - $this->GetMinVal()) * $this->scale_factor; 
    }
	
    // Restrict autoscaling to only use integers
    function SetIntScale($aIntScale=true) {
	$this->intscale=$aIntScale;
    }
	
    // Calculate an integer autoscale
    function IntAutoScale(&$img,$min,$max,$maxsteps,$majend=true) {
	// Make sure limits are integers
	$min=floor($min);
	$max=ceil($max);
	if( abs($min-$max)==0 ) {
	    --$min; ++$max;
	}
	$maxsteps = floor($maxsteps);
		
	$gracetop=round(($this->gracetop/100.0)*abs($max-$min));
	$gracebottom=round(($this->gracebottom/100.0)*abs($max-$min));
	if( is_numeric($this->autoscale_min) ) {
	    $min = ceil($this->autoscale_min);
	    if( $min >= $max ) {
		JpGraphError::Raise('You have specified a min value with SetAutoMin() which is larger than the maximum value used for the scale. This is not possible.');
		die();
	    }
	}

	if( is_numeric($this->autoscale_max) ) {
	    $max = ceil($this->autoscale_max);
	    if( $min >= $max ) {
		JpGraphError::Raise('You have specified a max value with SetAutoMax() which is smaller than the miminum value used for the scale. This is not possible.');
		die();
	    }
	}

	if( abs($min-$max ) == 0 ) {
	    ++$max;
	    --$min;
	}
			
	$min -= $gracebottom;
	$max += $gracetop;		

	// First get tickmarks as multiples of 1, 10, ...	
	if( $majend ) {
	    list($num1steps,$adj1min,$adj1max,$maj1step) = 
		$this->IntCalcTicks($maxsteps,$min,$max,1);
	}
	else {
	    $adj1min = $min;
	    $adj1max = $max;
	    list($num1steps,$maj1step) = 
		$this->IntCalcTicksFreeze($maxsteps,$min,$max,1);
	}

	if( abs($min-$max) > 2 ) {
	    // Then get tick marks as 2:s 2, 20, ...
	    if( $majend ) {
		list($num2steps,$adj2min,$adj2max,$maj2step) = 
		    $this->IntCalcTicks($maxsteps,$min,$max,5);
	    }
	    else {
		$adj2min = $min;
		$adj2max = $max;
		list($num2steps,$maj2step) = 
		    $this->IntCalcTicksFreeze($maxsteps,$min,$max,5);
	    }
	}
	else {
	    $num2steps = 10000;	// Dummy high value so we don't choose this
	}
	
	if( abs($min-$max) > 5 ) {	
	    // Then get tickmarks as 5:s 5, 50, 500, ...
	    if( $majend ) {
		list($num5steps,$adj5min,$adj5max,$maj5step) = 
		    $this->IntCalcTicks($maxsteps,$min,$max,2);
	    }
	    else {
		$adj5min = $min;
		$adj5max = $max;
		list($num5steps,$maj5step) = 
		    $this->IntCalcTicksFreeze($maxsteps,$min,$max,2);
	    }
	}
	else {
	    $num5steps = 10000;	// Dummy high value so we don't choose this		
	}
	
	// Check to see whichof 1:s, 2:s or 5:s fit better with
	// the requested number of major ticks		
	$match1=abs($num1steps-$maxsteps);		
	$match2=abs($num2steps-$maxsteps);
	if( !empty($maj5step) && $maj5step > 1 )
	    $match5=abs($num5steps-$maxsteps);
	else
	    $match5=10000; 	// Dummy high value 
		
	// Compare these three values and see which is the closest match
	// We use a 0.6 weight to gravitate towards multiple of 5:s 
	if( $match1 < $match2 ) {
	    if( $match1 < $match5 )
		$r=1;			
	    else 
		$r=3;
	}
	else {
	    if( $match2 < $match5 )
		$r=2;			
	    else 
		$r=3;		
	}	
	// Minsteps are always the same as maxsteps for integer scale
	switch( $r ) {
	    case 1:
		$this->Update($img,$adj1min,$adj1max);
		$this->ticks->Set($maj1step,$maj1step);
		break;			
	    case 2:
		$this->Update($img,$adj2min,$adj2max);		
		$this->ticks->Set($maj2step,$maj2step);
		break;									
	    case 3:
		$this->Update($img,$adj5min,$adj5max);
		$this->ticks->Set($maj5step,$maj2step);		
		break;			
	}		
    }
	
	
    // Calculate autoscale. Used if user hasn't given a scale and ticks
    // $maxsteps is the maximum number of major tickmarks allowed.
    function AutoScale(&$img,$min,$max,$maxsteps,$majend=true) {
	if( $this->intscale ) {	
	    $this->IntAutoScale($img,$min,$max,$maxsteps,$majend);
	    return;
	}
	if( abs($min-$max) < 0.00001 ) {
	    // We need some difference to be able to autoscale
	    // make it 5% above and 5% below value
	    if( $min==0 && $max==0 ) {		// Special case
		$min=-1; $max=1;
	    }
	    else {
		$delta = (abs($max)+abs($min))*0.005;
		$min -= $delta;
		$max += $delta;
	    }
	}
		
	$gracetop=($this->gracetop/100.0)*abs($max-$min);
	$gracebottom=($this->gracebottom/100.0)*abs($max-$min);
	if( is_numeric($this->autoscale_min) ) {
	    $min = $this->autoscale_min;
	    if( $min >= $max ) {
		JpGraphError::Raise('You have specified a min value with SetAutoMin() which is larger than the maximum value used for the scale. This is not possible.');
		die();
	    }
	    if( abs($min-$max ) < 0.00001 )
		$max *= 1.2;
	}

	if( is_numeric($this->autoscale_max) ) {
	    $max = $this->autoscale_max;
	    if( $min >= $max ) {
		JpGraphError::Raise('You have specified a max value with SetAutoMax() which is smaller than the miminum value used for the scale. This is not possible.');
		die();
	    }
	    if( abs($min-$max ) < 0.00001 )
		$min *= 0.8;
	}

	
	$min -= $gracebottom;
	$max += $gracetop;

	// First get tickmarks as multiples of 0.1, 1, 10, ...	
	if( $majend ) {
	    list($num1steps,$adj1min,$adj1max,$min1step,$maj1step) = 
		$this->CalcTicks($maxsteps,$min,$max,1,2);
	}
	else {
	    $adj1min=$min;
	    $adj1max=$max;
	    list($num1steps,$min1step,$maj1step) = 
		$this->CalcTicksFreeze($maxsteps,$min,$max,1,2,false);
	}
		
	// Then get tick marks as 2:s 0.2, 2, 20, ...
	if( $majend ) {
	    list($num2steps,$adj2min,$adj2max,$min2step,$maj2step) = 
		$this->CalcTicks($maxsteps,$min,$max,5,2);
	}
	else {
	    $adj2min=$min;
	    $adj2max=$max;
	    list($num2steps,$min2step,$maj2step) = 
		$this->CalcTicksFreeze($maxsteps,$min,$max,5,2,false);
	}
		
	// Then get tickmarks as 5:s 0.05, 0.5, 5, 50, ...
	if( $majend ) {
	    list($num5steps,$adj5min,$adj5max,$min5step,$maj5step) = 
		$this->CalcTicks($maxsteps,$min,$max,2,5);		
	}
	else {
	    $adj5min=$min;
	    $adj5max=$max;
	    list($num5steps,$min5step,$maj5step) = 
		$this->CalcTicksFreeze($maxsteps,$min,$max,2,5,false);
	}

	// Check to see whichof 1:s, 2:s or 5:s fit better with
	// the requested number of major ticks		
	$match1=abs($num1steps-$maxsteps);		
	$match2=abs($num2steps-$maxsteps);
	$match5=abs($num5steps-$maxsteps);
	// Compare these three values and see which is the closest match
	// We use a 0.8 weight to gravitate towards multiple of 5:s 
	$r=$this->MatchMin3($match1,$match2,$match5,0.8);
	switch( $r ) {
	    case 1:
		$this->Update($img,$adj1min,$adj1max);
		$this->ticks->Set($maj1step,$min1step);
		break;			
	    case 2:
		$this->Update($img,$adj2min,$adj2max);		
		$this->ticks->Set($maj2step,$min2step);
		break;									
	    case 3:
		$this->Update($img,$adj5min,$adj5max);
		$this->ticks->Set($maj5step,$min5step);		
		break;			
	}
    }

//---------------
// PRIVATE METHODS	

    // This method recalculates all constants that are depending on the
    // margins in the image. If the margins in the image are changed
    // this method should be called for every scale that is registred with
    // that image. Should really be installed as an observer of that image.
    function InitConstants(&$img) {
	if( $this->type=="x" ) {
	    $this->world_abs_size=$img->width - $img->left_margin - $img->right_margin;
	    $this->off=$img->left_margin;
	    $this->scale_factor = 0;
	    if( $this->world_size > 0 )
		$this->scale_factor=$this->world_abs_size/($this->world_size*1.0);
	}
	else { // y scale
	    $this->world_abs_size=$img->height - $img->top_margin - $img->bottom_margin; 
	    $this->off=$img->top_margin+$this->world_abs_size;			
	    $this->scale_factor = 0;			
	    if( $this->world_size > 0 )			
		$this->scale_factor=-$this->world_abs_size/($this->world_size*1.0);	
	}
	$size = $this->world_size * $this->scale_factor;
	$this->scale_abs=array($this->off,$this->off + $size);	
    }
	
    // Initialize the conversion constants for this scale
    // This tries to pre-calculate as much as possible to speed up the
    // actual conversion (with Translate()) later on
    // $start	=scale start in absolute pixels (for x-scale this is an y-position
    //				 and for an y-scale this is an x-position
    // $len 		=absolute length in pixels of scale 			
    function SetConstants($aStart,$aLen) {
	$this->world_abs_size=$aLen;
	$this->off=$aStart;
		
	if( $this->world_size<=0 ) {
	    JpGraphError::Raise("<b>JpGraph Fatal Error</b>:<br>
		 You have unfortunately stumbled upon a bug in JpGraph. <br>
		 It seems like the scale range is ".$this->world_size." [for ".
				$this->type." scale] <br>
	         Please report Bug #01 to jpgraph@aditus.nu and include the script
		 that gave this error. <br>
		 This problem could potentially be caused by trying to use \"illegal\"
		 values in the input data arrays (like trying to send in strings or
		 only NULL values) which causes the autoscaling to fail.");
	}
		
	// scale_factor = number of pixels per world unit
	$this->scale_factor=$this->world_abs_size/($this->world_size*1.0);
		
	// scale_abs = start and end points of scale in absolute pixels
	$this->scale_abs=array($this->off,$this->off+$this->world_size*$this->scale_factor);		
    }
	
	
    // Calculate number of ticks steps with a specific division
    // $a is the divisor of 10**x to generate the first maj tick intervall
    // $a=1, $b=2 give major ticks with multiple of 10, ...,0.1,1,10,...
    // $a=5, $b=2 give major ticks with multiple of 2:s ...,0.2,2,20,...
    // $a=2, $b=5 give major ticks with multiple of 5:s ...,0.5,5,50,...
    // We return a vector of
    // 	[$numsteps,$adjmin,$adjmax,$minstep,$majstep]
    // If $majend==true then the first and last marks on the axis will be major
    // labeled tick marks otherwise it will be adjusted to the closest min tick mark
    function CalcTicks($maxsteps,$min,$max,$a,$b,$majend=true) {
	$diff=$max-$min; 
	if( $diff==0 )
	    $ld=0;
	else
	    $ld=floor(log10($diff));

	// Gravitate min towards zero if we are close		
	if( $min>0 && $min < pow(10,$ld) ) $min=0;
		
	//$majstep=pow(10,$ld-1)/$a; 
	$majstep=pow(10,$ld)/$a; 
	$minstep=$majstep/$b;
	
	$adjmax=ceil($max/$minstep)*$minstep;
	$adjmin=floor($min/$minstep)*$minstep;	
	$adjdiff = $adjmax-$adjmin;
	$numsteps=$adjdiff/$majstep; 
	
	while( $numsteps>$maxsteps ) {
	    $majstep=pow(10,$ld)/$a; 
	    $numsteps=$adjdiff/$majstep;
	    ++$ld;
	}

	$minstep=$majstep/$b;
	$adjmin=floor($min/$minstep)*$minstep;	
	$adjdiff = $adjmax-$adjmin;		
	if( $majend ) {
	    $adjmin = floor($min/$majstep)*$majstep;	
	    $adjdiff = $adjmax-$adjmin;		
	    $adjmax = ceil($adjdiff/$majstep)*$majstep+$adjmin;
	}
	else
	    $adjmax=ceil($max/$minstep)*$minstep;

	return array($numsteps,$adjmin,$adjmax,$minstep,$majstep);
    }

    function CalcTicksFreeze($maxsteps,$min,$max,$a,$b) {
	// Same as CalcTicks but don't adjust min/max values
	$diff=$max-$min; 
	if( $diff==0 )
	    $ld=0;
	else
	    $ld=floor(log10($diff));

	//$majstep=pow(10,$ld-1)/$a; 
	$majstep=pow(10,$ld)/$a; 
	$minstep=$majstep/$b;
	$numsteps=floor($diff/$majstep); 
	
	while( $numsteps > $maxsteps ) {
	    $majstep=pow(10,$ld)/$a; 
	    $numsteps=floor($diff/$majstep);
	    ++$ld;
	}
	$minstep=$majstep/$b;
	return array($numsteps,$minstep,$majstep);
    }

	
    function IntCalcTicks($maxsteps,$min,$max,$a,$majend=true) {
	$diff=$max-$min; 
	if( $diff==0 )
	    JpGraphError::Raise('Can\'t automatically determine ticks since min==max.');
	else
	    $ld=floor(log10($diff));
		
	// Gravitate min towards zero if we are close		
	if( $min>0 && $min < pow(10,$ld) ) $min=0;
		
	if( $ld == 0 ) $ld=1;
	
	if( $a == 1 ) 
	    $majstep = 1;
	else
	    $majstep=pow(10,$ld)/$a; 
	$adjmax=ceil($max/$majstep)*$majstep;

	$adjmin=floor($min/$majstep)*$majstep;	
	$adjdiff = $adjmax-$adjmin;
	$numsteps=$adjdiff/$majstep; 
	while( $numsteps>$maxsteps ) {
	    $majstep=pow(10,$ld)/$a; 
	    $numsteps=$adjdiff/$majstep;
	    ++$ld;
	}
		
	$adjmin=floor($min/$majstep)*$majstep;	
	$adjdiff = $adjmax-$adjmin;		
	if( $majend ) {
	    $adjmin = floor($min/$majstep)*$majstep;	
	    $adjdiff = $adjmax-$adjmin;		
	    $adjmax = ceil($adjdiff/$majstep)*$majstep+$adjmin;
	}
	else
	    $adjmax=ceil($max/$majstep)*$majstep;
			
	return array($numsteps,$adjmin,$adjmax,$majstep);		
    }


    function IntCalcTicksFreeze($maxsteps,$min,$max,$a) {
	// Same as IntCalcTick but don't change min/max values
	$diff=$max-$min; 
	if( $diff==0 )
	    JpGraphError::Raise('Can\'t automatically determine ticks since min==max.');
	else
	    $ld=floor(log10($diff));
		
	if( $ld == 0 ) $ld=1;
	
	if( $a == 1 ) 
	    $majstep = 1;
	else
	    $majstep=pow(10,$ld)/$a; 

	$numsteps=floor($diff/$majstep); 
	while( $numsteps > $maxsteps ) {
	    $majstep=pow(10,$ld)/$a; 
	    $numsteps=floor($diff/$majstep);
	    ++$ld;
	}
					
	return array($numsteps,$majstep);		
    }


	
    // Determine the minimum of three values witha  weight for last value
    function MatchMin3($a,$b,$c,$weight) {
	if( $a < $b ) {
	    if( $a < ($c*$weight) ) 
		return 1; // $a smallest
	    else 
		return 3; // $c smallest
	}
	elseif( $b < ($c*$weight) ) 
	    return 2; // $b smallest
	return 3; // $c smallest
    }
} // Class

//===================================================
// CLASS RGB
// Description: Color definitions as RGB triples
//===================================================
class RGB {
    var $rgb_table;
    var $img;
    function RGB($aImg=null) {
	$this->img = $aImg;
		
	// Conversion array between color names and RGB
	$this->rgb_table = array(
	    "aqua"=> array(0,255,255),		
	    "lime"=> array(0,255,0),		
	    "teal"=> array(0,128,128),
	    "whitesmoke"=>array(245,245,245),
	    "gainsboro"=>array(220,220,220),
	    "oldlace"=>array(253,245,230),
	    "linen"=>array(250,240,230),
	    "antiquewhite"=>array(250,235,215),
	    "papayawhip"=>array(255,239,213),
	    "blanchedalmond"=>array(255,235,205),
	    "bisque"=>array(255,228,196),
	    "peachpuff"=>array(255,218,185),
	    "navajowhite"=>array(255,222,173),
	    "moccasin"=>array(255,228,181),
	    "cornsilk"=>array(255,248,220),
	    "ivory"=>array(255,255,240),
	    "lemonchiffon"=>array(255,250,205),
	    "seashell"=>array(255,245,238),
	    "mintcream"=>array(245,255,250),
	    "azure"=>array(240,255,255),
	    "aliceblue"=>array(240,248,255),
	    "lavender"=>array(230,230,250),
	    "lavenderblush"=>array(255,240,245),
	    "mistyrose"=>array(255,228,225),
	    "white"=>array(255,255,255),
	    "black"=>array(0,0,0),
	    "darkslategray"=>array(47,79,79),
	    "dimgray"=>array(105,105,105),
	    "slategray"=>array(112,128,144),
	    "lightslategray"=>array(119,136,153),
	    "gray"=>array(190,190,190),
	    "lightgray"=>array(211,211,211),
	    "midnightblue"=>array(25,25,112),
	    "navy"=>array(0,0,128),
	    "cornflowerblue"=>array(100,149,237),
	    "darkslateblue"=>array(72,61,139),
	    "slateblue"=>array(106,90,205),
	    "mediumslateblue"=>array(123,104,238),
	    "lightslateblue"=>array(132,112,255),
	    "mediumblue"=>array(0,0,205),
	    "royalblue"=>array(65,105,225),
	    "blue"=>array(0,0,255),
	    "dodgerblue"=>array(30,144,255),
	    "deepskyblue"=>array(0,191,255),
	    "skyblue"=>array(135,206,235),
	    "lightskyblue"=>array(135,206,250),
	    "steelblue"=>array(70,130,180),
	    "lightred"=>array(211,167,168),
	    "lightsteelblue"=>array(176,196,222),
	    "lightblue"=>array(173,216,230),
	    "powderblue"=>array(176,224,230),
	    "paleturquoise"=>array(175,238,238),
	    "darkturquoise"=>array(0,206,209),
	    "mediumturquoise"=>array(72,209,204),
	    "turquoise"=>array(64,224,208),
	    "cyan"=>array(0,255,255),
	    "lightcyan"=>array(224,255,255),
	    "cadetblue"=>array(95,158,160),
	    "mediumaquamarine"=>array(102,205,170),
	    "aquamarine"=>array(127,255,212),
	    "darkgreen"=>array(0,100,0),
	    "darkolivegreen"=>array(85,107,47),
	    "darkseagreen"=>array(143,188,143),
	    "seagreen"=>array(46,139,87),
	    "mediumseagreen"=>array(60,179,113),
	    "lightseagreen"=>array(32,178,170),
	    "palegreen"=>array(152,251,152),
	    "springgreen"=>array(0,255,127),
	    "lawngreen"=>array(124,252,0),
	    "green"=>array(0,255,0),
	    "chartreuse"=>array(127,255,0),
	    "mediumspringgreen"=>array(0,250,154),
	    "greenyellow"=>array(173,255,47),
	    "limegreen"=>array(50,205,50),
	    "yellowgreen"=>array(154,205,50),
	    "forestgreen"=>array(34,139,34),
	    "olivedrab"=>array(107,142,35),
	    "darkkhaki"=>array(189,183,107),
	    "khaki"=>array(240,230,140),
	    "palegoldenrod"=>array(238,232,170),
	    "lightgoldenrodyellow"=>array(250,250,210),
	    "lightyellow"=>array(255,255,200),
	    "yellow"=>array(255,255,0),
	    "gold"=>array(255,215,0),
	    "lightgoldenrod"=>array(238,221,130),
	    "goldenrod"=>array(218,165,32),
	    "darkgoldenrod"=>array(184,134,11),
	    "rosybrown"=>array(188,143,143),
	    "indianred"=>array(205,92,92),
	    "saddlebrown"=>array(139,69,19),
	    "sienna"=>array(160,82,45),
	    "peru"=>array(205,133,63),
	    "burlywood"=>array(222,184,135),
	    "beige"=>array(245,245,220),
	    "wheat"=>array(245,222,179),
	    "sandybrown"=>array(244,164,96),
	    "tan"=>array(210,180,140),
	    "chocolate"=>array(210,105,30),
	    "firebrick"=>array(178,34,34),
	    "brown"=>array(165,42,42),
	    "darksalmon"=>array(233,150,122),
	    "salmon"=>array(250,128,114),
	    "lightsalmon"=>array(255,160,122),
	    "orange"=>array(255,165,0),
	    "darkorange"=>array(255,140,0),
	    "coral"=>array(255,127,80),
	    "lightcoral"=>array(240,128,128),
	    "tomato"=>array(255,99,71),
	    "orangered"=>array(255,69,0),
	    "red"=>array(255,0,0),
	    "hotpink"=>array(255,105,180),
	    "deeppink"=>array(255,20,147),
	    "pink"=>array(255,192,203),
	    "lightpink"=>array(255,182,193),
	    "palevioletred"=>array(219,112,147),
	    "maroon"=>array(176,48,96),
	    "mediumvioletred"=>array(199,21,133),
	    "violetred"=>array(208,32,144),
	    "magenta"=>array(255,0,255),
	    "violet"=>array(238,130,238),
	    "plum"=>array(221,160,221),
	    "orchid"=>array(218,112,214),
	    "mediumorchid"=>array(186,85,211),
	    "darkorchid"=>array(153,50,204),
	    "darkviolet"=>array(148,0,211),
	    "blueviolet"=>array(138,43,226),
	    "purple"=>array(160,32,240),
	    "mediumpurple"=>array(147,112,219),
	    "thistle"=>array(216,191,216),
	    "snow1"=>array(255,250,250),
	    "snow2"=>array(238,233,233),
	    "snow3"=>array(205,201,201),
	    "snow4"=>array(139,137,137),
	    "seashell1"=>array(255,245,238),
	    "seashell2"=>array(238,229,222),
	    "seashell3"=>array(205,197,191),
	    "seashell4"=>array(139,134,130),
	    "AntiqueWhite1"=>array(255,239,219),
	    "AntiqueWhite2"=>array(238,223,204),
	    "AntiqueWhite3"=>array(205,192,176),
	    "AntiqueWhite4"=>array(139,131,120),
	    "bisque1"=>array(255,228,196),
	    "bisque2"=>array(238,213,183),
	    "bisque3"=>array(205,183,158),
	    "bisque4"=>array(139,125,107),
	    "peachPuff1"=>array(255,218,185),
	    "peachpuff2"=>array(238,203,173),
	    "peachpuff3"=>array(205,175,149),
	    "peachpuff4"=>array(139,119,101),
	    "navajowhite1"=>array(255,222,173),
	    "navajowhite2"=>array(238,207,161),
	    "navajowhite3"=>array(205,179,139),
	    "navajowhite4"=>array(139,121,94),
	    "lemonchiffon1"=>array(255,250,205),
	    "lemonchiffon2"=>array(238,233,191),
	    "lemonchiffon3"=>array(205,201,165),
	    "lemonchiffon4"=>array(139,137,112),
	    "ivory1"=>array(255,255,240),
	    "ivory2"=>array(238,238,224),
	    "ivory3"=>array(205,205,193),
	    "ivory4"=>array(139,139,131),
	    "honeydew"=>array(193,205,193),
	    "lavenderblush1"=>array(255,240,245),
	    "lavenderblush2"=>array(238,224,229),
	    "lavenderblush3"=>array(205,193,197),
	    "lavenderblush4"=>array(139,131,134),
	    "mistyrose1"=>array(255,228,225),
	    "mistyrose2"=>array(238,213,210),
	    "mistyrose3"=>array(205,183,181),
	    "mistyrose4"=>array(139,125,123),
	    "azure1"=>array(240,255,255),
	    "azure2"=>array(224,238,238),
	    "azure3"=>array(193,205,205),
	    "azure4"=>array(131,139,139),
	    "slateblue1"=>array(131,111,255),
	    "slateblue2"=>array(122,103,238),
	    "slateblue3"=>array(105,89,205),
	    "slateblue4"=>array(71,60,139),
	    "royalblue1"=>array(72,118,255),
	    "royalblue2"=>array(67,110,238),
	    "royalblue3"=>array(58,95,205),
	    "royalblue4"=>array(39,64,139),
	    "dodgerblue1"=>array(30,144,255),
	    "dodgerblue2"=>array(28,134,238),
	    "dodgerblue3"=>array(24,116,205),
	    "dodgerblue4"=>array(16,78,139),
	    "steelblue1"=>array(99,184,255),
	    "steelblue2"=>array(92,172,238),
	    "steelblue3"=>array(79,148,205),
	    "steelblue4"=>array(54,100,139),
	    "deepskyblue1"=>array(0,191,255),
	    "deepskyblue2"=>array(0,178,238),
	    "deepskyblue3"=>array(0,154,205),
	    "deepskyblue4"=>array(0,104,139),
	    "skyblue1"=>array(135,206,255),
	    "skyblue2"=>array(126,192,238),
	    "skyblue3"=>array(108,166,205),
	    "skyblue4"=>array(74,112,139),
	    "lightskyblue1"=>array(176,226,255),
	    "lightskyblue2"=>array(164,211,238),
	    "lightskyblue3"=>array(141,182,205),
	    "lightskyblue4"=>array(96,123,139),
	    "slategray1"=>array(198,226,255),
	    "slategray2"=>array(185,211,238),
	    "slategray3"=>array(159,182,205),
	    "slategray4"=>array(108,123,139),
	    "lightsteelblue1"=>array(202,225,255),
	    "lightsteelblue2"=>array(188,210,238),
	    "lightsteelblue3"=>array(162,181,205),
	    "lightsteelblue4"=>array(110,123,139),
	    "lightblue1"=>array(191,239,255),
	    "lightblue2"=>array(178,223,238),
	    "lightblue3"=>array(154,192,205),
	    "lightblue4"=>array(104,131,139),
	    "lightcyan1"=>array(224,255,255),
	    "lightcyan2"=>array(209,238,238),
	    "lightcyan3"=>array(180,205,205),
	    "lightcyan4"=>array(122,139,139),
	    "paleturquoise1"=>array(187,255,255),
	    "paleturquoise2"=>array(174,238,238),
	    "paleturquoise3"=>array(150,205,205),
	    "paleturquoise4"=>array(102,139,139),
	    "cadetblue1"=>array(152,245,255),
	    "cadetblue2"=>array(142,229,238),
	    "cadetblue3"=>array(122,197,205),
	    "cadetblue4"=>array(83,134,139),
	    "turquoise1"=>array(0,245,255),
	    "turquoise2"=>array(0,229,238),
	    "turquoise3"=>array(0,197,205),
	    "turquoise4"=>array(0,134,139),
	    "cyan1"=>array(0,255,255),
	    "cyan2"=>array(0,238,238),
	    "cyan3"=>array(0,205,205),
	    "cyan4"=>array(0,139,139),
	    "darkslategray1"=>array(151,255,255),
	    "darkslategray2"=>array(141,238,238),
	    "darkslategray3"=>array(121,205,205),
	    "darkslategray4"=>array(82,139,139),
	    "aquamarine1"=>array(127,255,212),
	    "aquamarine2"=>array(118,238,198),
	    "aquamarine3"=>array(102,205,170),
	    "aquamarine4"=>array(69,139,116),
	    "darkseagreen1"=>array(193,255,193),
	    "darkseagreen2"=>array(180,238,180),
	    "darkseagreen3"=>array(155,205,155),
	    "darkseagreen4"=>array(105,139,105),
	    "seagreen1"=>array(84,255,159),
	    "seagreen2"=>array(78,238,148),
	    "seagreen3"=>array(67,205,128),
	    "seagreen4"=>array(46,139,87),
	    "palegreen1"=>array(154,255,154),
	    "palegreen2"=>array(144,238,144),
	    "palegreen3"=>array(124,205,124),
	    "palegreen4"=>array(84,139,84),
	    "springgreen1"=>array(0,255,127),
	    "springgreen2"=>array(0,238,118),
	    "springgreen3"=>array(0,205,102),
	    "springgreen4"=>array(0,139,69),
	    "chartreuse1"=>array(127,255,0),
	    "chartreuse2"=>array(118,238,0),
	    "chartreuse3"=>array(102,205,0),
	    "chartreuse4"=>array(69,139,0),
	    "olivedrab1"=>array(192,255,62),
	    "olivedrab2"=>array(179,238,58),
	    "olivedrab3"=>array(154,205,50),
	    "olivedrab4"=>array(105,139,34),
	    "darkolivegreen1"=>array(202,255,112),
	    "darkolivegreen2"=>array(188,238,104),
	    "darkolivegreen3"=>array(162,205,90),
	    "darkolivegreen4"=>array(110,139,61),
	    "khaki1"=>array(255,246,143),
	    "khaki2"=>array(238,230,133),
	    "khaki3"=>array(205,198,115),
	    "khaki4"=>array(139,134,78),
	    "lightgoldenrod1"=>array(255,236,139),
	    "lightgoldenrod2"=>array(238,220,130),
	    "lightgoldenrod3"=>array(205,190,112),
	    "lightgoldenrod4"=>array(139,129,76),
	    "yellow1"=>array(255,255,0),
	    "yellow2"=>array(238,238,0),
	    "yellow3"=>array(205,205,0),
	    "yellow4"=>array(139,139,0),
	    "gold1"=>array(255,215,0),
	    "gold2"=>array(238,201,0),
	    "gold3"=>array(205,173,0),
	    "gold4"=>array(139,117,0),
	    "goldenrod1"=>array(255,193,37),
	    "goldenrod2"=>array(238,180,34),
	    "goldenrod3"=>array(205,155,29),
	    "goldenrod4"=>array(139,105,20),
	    "darkgoldenrod1"=>array(255,185,15),
	    "darkgoldenrod2"=>array(238,173,14),
	    "darkgoldenrod3"=>array(205,149,12),
	    "darkgoldenrod4"=>array(139,101,8),
	    "rosybrown1"=>array(255,193,193),
	    "rosybrown2"=>array(238,180,180),
	    "rosybrown3"=>array(205,155,155),
	    "rosybrown4"=>array(139,105,105),
	    "indianred1"=>array(255,106,106),
	    "indianred2"=>array(238,99,99),
	    "indianred3"=>array(205,85,85),
	    "indianred4"=>array(139,58,58),
	    "sienna1"=>array(255,130,71),
	    "sienna2"=>array(238,121,66),
	    "sienna3"=>array(205,104,57),
	    "sienna4"=>array(139,71,38),
	    "burlywood1"=>array(255,211,155),
	    "burlywood2"=>array(238,197,145),
	    "burlywood3"=>array(205,170,125),
	    "burlywood4"=>array(139,115,85),
	    "wheat1"=>array(255,231,186),
	    "wheat2"=>array(238,216,174),
	    "wheat3"=>array(205,186,150),
	    "wheat4"=>array(139,126,102),
	    "tan1"=>array(255,165,79),
	    "tan2"=>array(238,154,73),
	    "tan3"=>array(205,133,63),
	    "tan4"=>array(139,90,43),
	    "chocolate1"=>array(255,127,36),
	    "chocolate2"=>array(238,118,33),
	    "chocolate3"=>array(205,102,29),
	    "chocolate4"=>array(139,69,19),
	    "firebrick1"=>array(255,48,48),
	    "firebrick2"=>array(238,44,44),
	    "firebrick3"=>array(205,38,38),
	    "firebrick4"=>array(139,26,26),
	    "brown1"=>array(255,64,64),
	    "brown2"=>array(238,59,59),
	    "brown3"=>array(205,51,51),
	    "brown4"=>array(139,35,35),
	    "salmon1"=>array(255,140,105),
	    "salmon2"=>array(238,130,98),
	    "salmon3"=>array(205,112,84),
	    "salmon4"=>array(139,76,57),
	    "lightsalmon1"=>array(255,160,122),
	    "lightsalmon2"=>array(238,149,114),
	    "lightsalmon3"=>array(205,129,98),
	    "lightsalmon4"=>array(139,87,66),
	    "orange1"=>array(255,165,0),
	    "orange2"=>array(238,154,0),
	    "orange3"=>array(205,133,0),
	    "orange4"=>array(139,90,0),
	    "darkorange1"=>array(255,127,0),
	    "darkorange2"=>array(238,118,0),
	    "darkorange3"=>array(205,102,0),
	    "darkorange4"=>array(139,69,0),
	    "coral1"=>array(255,114,86),
	    "coral2"=>array(238,106,80),
	    "coral3"=>array(205,91,69),
	    "coral4"=>array(139,62,47),
	    "tomato1"=>array(255,99,71),
	    "tomato2"=>array(238,92,66),
	    "tomato3"=>array(205,79,57),
	    "tomato4"=>array(139,54,38),
	    "orangered1"=>array(255,69,0),
	    "orangered2"=>array(238,64,0),
	    "orangered3"=>array(205,55,0),
	    "orangered4"=>array(139,37,0),
	    "deeppink1"=>array(255,20,147),
	    "deeppink2"=>array(238,18,137),
	    "deeppink3"=>array(205,16,118),
	    "deeppink4"=>array(139,10,80),
	    "hotpink1"=>array(255,110,180),
	    "hotpink2"=>array(238,106,167),
	    "hotpink3"=>array(205,96,144),
	    "hotpink4"=>array(139,58,98),
	    "pink1"=>array(255,181,197),
	    "pink2"=>array(238,169,184),
	    "pink3"=>array(205,145,158),
	    "pink4"=>array(139,99,108),
	    "lightpink1"=>array(255,174,185),
	    "lightpink2"=>array(238,162,173),
	    "lightpink3"=>array(205,140,149),
	    "lightpink4"=>array(139,95,101),
	    "palevioletred1"=>array(255,130,171),
	    "palevioletred2"=>array(238,121,159),
	    "palevioletred3"=>array(205,104,137),
	    "palevioletred4"=>array(139,71,93),
	    "maroon1"=>array(255,52,179),
	    "maroon2"=>array(238,48,167),
	    "maroon3"=>array(205,41,144),
	    "maroon4"=>array(139,28,98),
	    "violetred1"=>array(255,62,150),
	    "violetred2"=>array(238,58,140),
	    "violetred3"=>array(205,50,120),
	    "violetred4"=>array(139,34,82),
	    "magenta1"=>array(255,0,255),
	    "magenta2"=>array(238,0,238),
	    "magenta3"=>array(205,0,205),
	    "magenta4"=>array(139,0,139),
	    "mediumred"=>array(140,34,34),         
	    "orchid1"=>array(255,131,250),
	    "orchid2"=>array(238,122,233),
	    "orchid3"=>array(205,105,201),
	    "orchid4"=>array(139,71,137),
	    "plum1"=>array(255,187,255),
	    "plum2"=>array(238,174,238),
	    "plum3"=>array(205,150,205),
	    "plum4"=>array(139,102,139),
	    "mediumorchid1"=>array(224,102,255),
	    "mediumorchid2"=>array(209,95,238),
	    "mediumorchid3"=>array(180,82,205),
	    "mediumorchid4"=>array(122,55,139),
	    "darkorchid1"=>array(191,62,255),
	    "darkorchid2"=>array(178,58,238),
	    "darkorchid3"=>array(154,50,205),
	    "darkorchid4"=>array(104,34,139),
	    "purple1"=>array(155,48,255),
	    "purple2"=>array(145,44,238),
	    "purple3"=>array(125,38,205),
	    "purple4"=>array(85,26,139),
	    "mediumpurple1"=>array(171,130,255),
	    "mediumpurple2"=>array(159,121,238),
	    "mediumpurple3"=>array(137,104,205),
	    "mediumpurple4"=>array(93,71,139),
	    "thistle1"=>array(255,225,255),
	    "thistle2"=>array(238,210,238),
	    "thistle3"=>array(205,181,205),
	    "thistle4"=>array(139,123,139),
	    "gray1"=>array(10,10,10),
	    "gray2"=>array(40,40,30),
	    "gray3"=>array(70,70,70),
	    "gray4"=>array(100,100,100),
	    "gray5"=>array(130,130,130),
	    "gray6"=>array(160,160,160),
	    "gray7"=>array(190,190,190),
	    "gray8"=>array(210,210,210),
	    "gray9"=>array(240,240,240),
	    "darkgray"=>array(100,100,100),
	    "darkblue"=>array(0,0,139),
	    "darkcyan"=>array(0,139,139),
	    "darkmagenta"=>array(139,0,139),
	    "darkred"=>array(139,0,0),
	    "silver"=>array(192, 192, 192),
	    "eggplant"=>array(144,176,168),
	    "lightgreen"=>array(144,238,144));		
    }
//----------------
// PUBLIC METHODS
    // Colors can be specified as either
    // 1. #xxxxxx			HTML style
    // 2. "colorname" 	as a named color
    // 3. array(r,g,b)	RGB triple
    // This function translates this to a native RGB format and returns an 
    // RGB triple.
    function Color($aColor) {
	if (is_string($aColor)) {

	    // Strip of any alpha factor
	    $pos = strpos($aColor,'@');
	    if( $pos === false ) {
		$alpha = 0;
	    }
	    else {
		$pos2 = strpos($aColor,':');
		if( $pos2===false ) 
		    $pos2 = $pos-1; // Sentinel
		if( $pos > $pos2 ) {
		    $alpha = substr($aColor,$pos+1);
		    $aColor = substr($aColor,0,$pos);
		}
		else {
		    $alpha = substr($aColor,$pos+1,$pos2-$pos-1);
		    $aColor = substr($aColor,0,$pos).substr($aColor,$pos2);
		}
	    }

	    // Extract potential adjustment figure at end of color
	    // specification
	    $pos = strpos($aColor,":");
	    if( $pos === false ) {
		$adj = 1.0;
	    }
	    else {
		$adj = 0.0 + substr($aColor,$pos+1);
		$aColor = substr($aColor,0,$pos);
	    }
	    if( $adj < 0 )
		JpGraphError::Raise('Adjustment factor for color must be > 0');

	    if (substr($aColor, 0, 1) == "#") {
		$r = hexdec(substr($aColor, 1, 2));
		$g = hexdec(substr($aColor, 3, 2));
		$b = hexdec(substr($aColor, 5, 2));
	    } else {
      		if(!isset($this->rgb_table[$aColor]) )
		    JpGraphError::Raise(" Unknown color: <strong>$aColor</strong>");
		$tmp=$this->rgb_table[$aColor];
		$r = $tmp[0];
		$g = $tmp[1];
		$b = $tmp[2];
	    }
	    // Scale adj so that an adj=2 always
	    // makes the color 100% white (i.e. 255,255,255. 
	    // and adj=1 neutral and adj=0 black.
	    if( $adj > 1 ) {
		$m = ($adj-1.0)*(255-min(255,min($r,min($g,$b))));
		return array(min(255,$r+$m), min(255,$g+$m), min(255,$b+$m),$alpha);
	    }
	    elseif( $adj < 1 ) {
		$m = ($adj-1.0)*max(255,max($r,max($g,$b)));
		return array(max(0,$r+$m), max(0,$g+$m), max(0,$b+$m),$alpha);
	    }
	    else {
		return array($r,$g,$b,$alpha);
	    }

	} elseif( is_array($aColor) ) {
	    if( count($aColor)==3 ) {
		$aColor[3]=0;
		return $aColor;
	    }
	    else
		return $aColor;
	}
	else
	    JpGraphError::Raise(" Unknown color specification: $aColor , size=".count($aColor));
    }
	
    // Compare two colors
    // return true if equal
    function Equal($aCol1,$aCol2) {
	$c1 = $this->Color($aCol1);
	$c2 = $this->Color($aCol2);
	if( $c1[0]==$c2[0] && $c1[1]==$c2[1] && $c1[2]==$c2[2] )
	    return true;
	else
	    return false;
    }
	
    // Allocate a new color in the current image
    // Return new color index, -1 if no more colors could be allocated
    function Allocate($aColor,$aAlpha=0.0) {
	list ($r, $g, $b, $a) = $this->color($aColor);
	// If alpha is specified in the color string then this
	// takes precedence over the second argument
	if( $a > 0 )
	    $aAlpha = $a;
	if(@$GLOBALS['gd2']==true) {
	    if( $aAlpha < 0 || $aAlpha > 1 ) {
		JpGraphError::Raise('Alpha parameter for color must be between 0.0 and 1.0');
		exit(1);
	    }
	    return imagecolorresolvealpha($this->img, $r, $g, $b, round($aAlpha * 127));
	} else {
	    $index = imagecolorexact($this->img, $r, $g, $b);
	    if ($index == -1) {
      		$index = imagecolorallocate($this->img, $r, $g, $b);
      		if( USE_APPROX_COLORS && $index == -1 )
		    $index = imagecolorresolve($this->img, $r, $g, $b);
	    } 
	    return $index;
	}
    }
} // Class

	
//===================================================
// CLASS Image
// Description: Wrapper class with some goodies to form the
// Interface to low level image drawing routines.
//===================================================
class Image {
    var $img_format;
    var $expired=true;
    var $img;
    var $left_margin=30,$right_margin=30,$top_margin=20,$bottom_margin=30;
    var $plotwidth=0,$plotheight=0;
    var $rgb=null;
    var $current_color,$current_color_name;
    var $lastx=0, $lasty=0;
    var $width, $height;
    var $line_weight=1;
    var $line_style=1;	// Default line style is solid
    var $obs_list=array();
    var $font_size=12,$font_family=FF_FONT1, $font_style=FS_NORMAL;
    var $font_file='';
    var $text_halign="left",$text_valign="bottom";
    var $ttf=null;
    var $use_anti_aliasing=false;
    var $quality=null;
    var $colorstack=array(),$colorstackidx=0;
    var $canvascolor = 'white' ;
    var $langconv = null ;

    //---------------
    // CONSTRUCTOR
    function Image($aWidth,$aHeight,$aFormat=DEFAULT_GFORMAT) {
	$this->CreateImgCanvas($aWidth,$aHeight);
	$this->SetAutoMargin();		

	if( !$this->SetImgFormat($aFormat) ) {
	    JpGraphError::Raise("JpGraph: Selected graphic format is either not supported or unknown [$aFormat]");
	}
	$this->ttf = new TTF();
	$this->langconv = new LanguageConv();
    }

    // Should we use anti-aliasing. Note: This really slows down graphics!
    function SetAntiAliasing() {
	$this->use_anti_aliasing=true;
    }

    function CreateRawCanvas($aWidth=0,$aHeight=0) {
	if( @$GLOBALS['gd2']==true && USE_TRUECOLOR ) {
	    $this->img = @imagecreatetruecolor($aWidth, $aHeight);
	    if( $this->img < 1 ) {
		die("<font color=red><b>JpGraph Error:</b></font> Can't create truecolor image. Check that you really have GD2 library installed.");
	    }
	    $this->SetAlphaBlending();
	} else {
	    $this->img = @imagecreate($aWidth, $aHeight);	
	    if( $this->img < 1 ) {
		die("<font color=red><b>JpGraph Error:</b></font> Can't create image. Check that you really have the GD library installed.");
	    }
	}
	if( $this->rgb != null ) 
	    $this->rgb->img = $this->img ;
	else
	    $this->rgb = new RGB($this->img);				
    }

    function CloneCanvasH() {
	$oldimage = $this->img;
	$this->CreateRawCanvas($this->width,$this->height);
	imagecopy($this->img,$oldimage,0,0,0,0,$this->width,$this->height);
	return $oldimage;
    }
    
    function CreateImgCanvas($aWidth=0,$aHeight=0) {

	$old = array($this->img,$this->width,$this->height);
	
	$aWidth = round($aWidth);
	$aHeight = round($aHeight);

	$this->width=$aWidth;
	$this->height=$aHeight;		

	
	if( $aWidth==0 || $aHeight==0 ) {
	    // We will set the final size later. 
	    // Note: The size must be specified before any other
	    // img routines that stroke anything are called.
	    $this->img = null;
	    $this->rgb = null;
	    return $old;
	}
	
	$this->CreateRawCanvas($aWidth,$aHeight);
		
	// Set canvas color (will also be the background color for a 
	// a pallett image
	$this->SetColor($this->canvascolor);	
	$this->FilledRectangle(0,0,$aWidth,$aHeight);

	return $old ;
    }
	

    function CopyCanvasH($aToHdl,$aFromHdl,$aToX,$aToY,$aFromX,$aFromY,$aWidth,$aHeight,$aw=-1,$ah=-1) {
	if( $aw === -1 ) {
	    $aw = $aWidth;
	    $ah = $aHeight;
	    $f = 'imagecopyresized';
	}
	else {
	    $f = $GLOBALS['copyfunc'] ;
	}
	$f($aToHdl,$aFromHdl,
	   $aToX,$aToY,$aFromX,$aFromY, $aWidth,$aHeight,$aw,$ah);
    }

    function SetCanvasH($aHdl) {
	$this->img = $aHdl;
	$this->rgb->img = $aHdl;
    }

    function SetCanvasColor($aColor) {
	$this->canvascolor = $aColor ;
    }

    function SetAlphaBlending($aFlg=true) {
	if( $GLOBALS['gd2'] )
	    ImageAlphaBlending($this->img,$aFlg);
	else 
	    JpGraphError::Raise('You only seem to have GD 1.x installed. To enable Alphablending requires GD 2.x or higher. Please install GD or make sure the constant USE_GD2 is specified correctly to reflect your installation. By default it tries to autodetect what version of GD you have installed. On some very rare occasions it may falsely detect GD2 where only GD1 is installed. You must then set USE_GD2 to false.');
    }

	
    function SetAutoMargin() {	
	GLOBAL $gJpgBrandTiming;
	$min_bm=0;
	if( $gJpgBrandTiming )
	    $min_bm=15;		
	$lm = max(0,$this->width/7);
	$rm = max(0,$this->width/10);
	$tm = max(0,$this->height/7);
	$bm = max($min_bm,$this->height/7);
	$this->SetMargin($lm,$rm,$tm,$bm);		
    }

				
    //---------------
    // PUBLIC METHODS	

    // Add observer. The observer will be notified when
    // the margin changes
    function AddObserver($aMethod,&$aObject) {
	$this->obs_list[]=array($aMethod,&$aObject);
    }
	
    // Call all observers
    function NotifyObservers() {
	//	foreach($this->obs_list as $o)
	//		$o[1]->$o[0]($this);
	for($i=0; $i < count($this->obs_list); ++$i) {
	    $obj = & $this->obs_list[$i][1];
	    $method = $this->obs_list[$i][0];
	    $obj->$method($this);
	}
    }	
	
    function SetFont($family,$style=FS_NORMAL,$size=10) {
	if($family==FONT1_BOLD || $family==FONT2_BOLD || $family==FONT0 || $family==FONT1 || $family==FONT2 )
	    JpGraphError::Raise(" Usage of FONT0, FONT1, FONT2 is deprecated. Use FF_xxx instead.");
	
	
	$this->font_family=$family;
	$this->font_style=$style;
	$this->font_size=$size;
	$this->font_file='';
	if( ($this->font_family==FF_FONT1 || $this->font_family==FF_FONT2) && $this->font_style==FS_BOLD ){
	    ++$this->font_family;
	}
	if( $this->font_family > FF_FONT2+1 ) { // A TTF font so get the font file

	    // Check that this PHP has support for TTF fonts
	    if( !function_exists('imagettfbbox') ) {
		JpGraphError::Raise('This PHP build has not been configured with TTF support. You need to recompile your PHP installation with FreeType support.');
		exit();
	    }
	    $this->font_file = $this->ttf->File($this->font_family,$this->font_style);
	}
    }

    // Get the specific height for a text string
    function GetTextHeight($txt="",$angle=0) {
	$tmp = split("\n",$txt);
	$n = count($tmp);
	$m=0;
	for($i=0; $i< $n; ++$i)
	    $m = max($m,strlen($tmp[$i]));

	if( $this->font_family <= FF_FONT2+1 ) {
	    if( $angle==0 )
		return $n*imagefontheight($this->font_family);
	    else 
		return $m*imagefontwidth($this->font_family);
	}
	else {
	    $bbox = $this->GetTTFBBox($txt,$angle);
	    return $bbox[1]-$bbox[5];
	}
    }
	
    // Estimate font height
    function GetFontHeight($angle=0) {
	$txt = "XOMg";
	return $this->GetTextHeight($txt,$angle);
    }
	
    // Approximate font width with width of letter "O"
    function GetFontWidth($angle=0) {
	$txt = 'O';
	return $this->GetTextWidth($txt,$angle);
    }
	
    // Get actual width of text in absolute pixels
    function GetTextWidth($txt,$angle=0) {

	$tmp = split("\n",$txt);
	$n = count($tmp);
	if( $this->font_family <= FF_FONT2+1 ) {

	    $m=0;
	    for($i=0; $i < $n; ++$i) {
		$l=strlen($tmp[$i]);
		if( $l > $m ) {
		    $m = $l;
		}
	    }

	    if( $angle==0 ) {
		$width=$m*imagefontwidth($this->font_family);
		return $width;
	    }
	    else {
		// 90 degrees internal so height becomes width
		return $n*imagefontheight($this->font_family); 
	    }
	}
	else {
	    // For TTF fonts we must walk through a lines and find the 
	    // widest one which we use as the width of the multi-line
	    // paragraph
	    $m=0;
	    for( $i=0; $i < $n; ++$i ) {
		$bbox = $this->GetTTFBBox($tmp[$i],$angle);
		$mm =  $bbox[2] - $bbox[0];
		if( $mm > $m ) 
		    $m = $mm;
	    }
	    return $m;
	}
    }
	
    // Draw text with a box around it
    function StrokeBoxedText($x,$y,$txt,$dir=0,$fcolor="white",$bcolor="black",
			     $shadowcolor=false,$paragraph_align="left",
			     $xmarg=6,$ymarg=4,$cornerradius=0,$dropwidth=3) {

	if( !is_numeric($dir) ) {
	    if( $dir=="h" ) $dir=0;
	    elseif( $dir=="v" ) $dir=90;
	    else JpGraphError::Raise(" Unknown direction specified in call to StrokeBoxedText() [$dir]");
	}
		
	if( $this->font_family >= FF_FONT0 && $this->font_family <= FF_FONT2+1) {	
	    $width=$this->GetTextWidth($txt,$dir) ;
	    $height=$this->GetTextHeight($txt,$dir) ;
	}
	else {
	    $width=$this->GetBBoxWidth($txt,$dir) ;
	    $height=$this->GetBBoxHeight($txt,$dir) ;
	}
	
	$height += 2*$ymarg;
	$width  += 2*$xmarg;

	if( $this->text_halign=="right" ) $x -= $width;
	elseif( $this->text_halign=="center" ) $x -= $width/2;
	if( $this->text_valign=="bottom" ) $y -= $height;
	elseif( $this->text_valign=="center" ) $y -= $height/2;
	
	if( $shadowcolor ) {
	    $this->PushColor($shadowcolor);
	    $this->FilledRoundedRectangle($x-$xmarg+$dropwidth,$y-$ymarg+$dropwidth,
					  $x+$width+$dropwidth,$y+$height+$dropwidth,
					  $cornerradius);
	    $this->PopColor();
	    $this->PushColor($fcolor);
	    $this->FilledRoundedRectangle($x-$xmarg,$y-$ymarg,$x+$width,$y+$height,$cornerradius);		
	    $this->PopColor();
	    $this->PushColor($bcolor);
	    $this->RoundedRectangle($x-$xmarg,$y-$ymarg,$x+$width,$y+$height,$cornerradius);
	    $this->PopColor();
	}
	else {
	    if( $fcolor ) {
		$oc=$this->current_color;
		$this->SetColor($fcolor);
		$this->FilledRoundedRectangle($x-$xmarg,$y-$ymarg,$x+$width,$y+$height,$cornerradius);
		$this->current_color=$oc;
	    }
	    if( $bcolor ) {
		$oc=$this->current_color;
		$this->SetColor($bcolor);			
		$this->RoundedRectangle($x-$xmarg,$y-$ymarg,$x+$width,$y+$height,$cornerradius);
		$this->current_color=$oc;			
	    }
	}
		
	$h=$this->text_halign;
	$v=$this->text_valign;
	$this->SetTextAlign("left","top");
	$this->StrokeText($x, $y, $txt, $dir, $paragraph_align);
	$this->SetTextAlign($h,$v);
    }

    // Set text alignment	
    function SetTextAlign($halign,$valign="bottom") {
	$this->text_halign=$halign;
	$this->text_valign=$valign;
    }
	

    function _StrokeBuiltinFont($x,$y,$txt,$dir=0,$paragraph_align="left") {

	if( is_numeric($dir) && $dir!=90 && $dir!=0) 
	    JpGraphError::Raise(" Internal font does not support drawing text at arbitrary angle. Use TTF fonts instead.");
	
	$h=$this->GetTextHeight($txt);
	$fh=$this->GetFontHeight();
	$w=$this->GetTextWidth($txt);
	
	if( $this->text_halign=="right") 				
	    $x -= $dir==0 ? $w : $h;
	elseif( $this->text_halign=="center" ) {
	    // For center we subtract 1 pixel since this makes the middle
	    // be prefectly in the middle
	    $x -= $dir==0 ? $w/2-1 : $h/2;
	}
	if( $this->text_valign=="top" )
	    $y += $dir==0 ? $h : $w;
	elseif( $this->text_valign=="center" ) 				
	    $y += $dir==0 ? $h/2 : $w/2;
	
	if( $dir==90 )
	    imagestringup($this->img,$this->font_family,$x,$y,$txt,$this->current_color);
	else {
	    if( ereg("\n",$txt) ) { 
		$tmp = split("\n",$txt);
		for($i=0; $i < count($tmp); ++$i) {
		    $w1 = $this->GetTextWidth($tmp[$i]);
		    if( $paragraph_align=="left" ) {
			imagestring($this->img,$this->font_family,$x,$y-$h+1+$i*$fh,$tmp[$i],$this->current_color);
		    }
		    elseif( $paragraph_align=="right" ) {
			imagestring($this->img,$this->font_family,$x+($w-$w1),
				    $y-$h+1+$i*$fh,$tmp[$i],$this->current_color);
		    }
		    else {
			imagestring($this->img,$this->font_family,$x+$w/2-$w1/2,
				    $y-$h+1+$i*$fh,$tmp[$i],$this->current_color);
		    }
		}
	    } 
	    else {
		//Put the text
		imagestring($this->img,$this->font_family,$x,$y-$h+1,$txt,$this->current_color);
	    }
	}
    }

    function AddTxtCR($aTxt) {
	// If the user has just specified a '\n'
	// instead of '\n\t' we have to add '\r' since
	// the width will be too muchy otherwise since when
	// we print we stroke the individually lines by hand.
	$e = explode("\n",$aTxt);
	$n = count($e);
	for($i=0; $i<$n; ++$i) {
	    $e[$i]=str_replace("\r","",$e[$i]);
	}
	return implode("\n\r",$e);
    }

    function GetTTFBBox($aTxt,$aAngle=0) {
	$bbox = @ImageTTFBBox($this->font_size,$aAngle,$this->font_file,$aTxt);
	if( $bbox === false ) {
	    JpGraphError::Raise("There is either a configuration problem with TrueType or a problem reading font file (".$this->font_file."). Make sure file exists and is in a readable place for the HTTP process. (If 'basedir' restriction is enabled in PHP then the font file must be located in the document root.). It might also be a wrongly installed FreeType library. Try uppgrading to at least FreeType 2.1.13 and recompile GD with the correct setup so it can find the new FT library.");
	}
	return $bbox;
    }

    function GetBBoxTTF($aTxt,$aAngle=0) {
	// Normalize the bounding box to become a minimum
	// enscribing rectangle

	$aTxt = $this->AddTxtCR($aTxt);

	if( !is_readable($this->font_file) ) {
	    JpGraphError::Raise('Can not read font file ('.$this->font_file.') in call to Image::GetBBoxTTF. Please make sure that you have set a font before calling this method and that the font is installed in the TTF directory.');
	}
	$bbox = $this->GetTTFBBox($aTxt,$aAngle);

	if( $aAngle==0 ) 
	    return $bbox;
	if( $aAngle >= 0 ) {
	    if(  $aAngle <= 90 ) { //<=0		
		$bbox = array($bbox[6],$bbox[1],$bbox[2],$bbox[1],
			      $bbox[2],$bbox[5],$bbox[6],$bbox[5]);
	    }
	    elseif(  $aAngle <= 180 ) { //<= 2
		$bbox = array($bbox[4],$bbox[7],$bbox[0],$bbox[7],
			      $bbox[0],$bbox[3],$bbox[4],$bbox[3]);
	    }
	    elseif(  $aAngle <= 270 )  { //<= 3
		$bbox = array($bbox[2],$bbox[5],$bbox[6],$bbox[5],
			      $bbox[6],$bbox[1],$bbox[2],$bbox[1]);
	    }
	    else {
		$bbox = array($bbox[0],$bbox[3],$bbox[4],$bbox[3],
			      $bbox[4],$bbox[7],$bbox[0],$bbox[7]);
	    }
	}
	elseif(  $aAngle < 0 ) {
	    if( $aAngle <= -270 ) { // <= -3
		$bbox = array($bbox[6],$bbox[1],$bbox[2],$bbox[1],
			      $bbox[2],$bbox[5],$bbox[6],$bbox[5]);
	    }
	    elseif( $aAngle <= -180 ) { // <= -2
		$bbox = array($bbox[0],$bbox[3],$bbox[4],$bbox[3],
			      $bbox[4],$bbox[7],$bbox[0],$bbox[7]);
	    }
	    elseif( $aAngle <= -90 ) { // <= -1
		$bbox = array($bbox[2],$bbox[5],$bbox[6],$bbox[5],
			      $bbox[6],$bbox[1],$bbox[2],$bbox[1]);
	    }
	    else {
		$bbox = array($bbox[0],$bbox[3],$bbox[4],$bbox[3],
			      $bbox[4],$bbox[7],$bbox[0],$bbox[7]);
	    }
	}	
	return $bbox;
    }

    function GetBBoxHeight($aTxt,$aAngle=0) {
	$box = $this->GetBBoxTTF($aTxt,$aAngle);
	return $box[1]-$box[7]+1;
    }

    function GetBBoxWidth($aTxt,$aAngle=0) {
	$box = $this->GetBBoxTTF($aTxt,$aAngle);
	return $box[2]-$box[0]+1;	
    }

    function _StrokeTTF($x,$y,$txt,$dir=0,$paragraph_align="left",$debug=false) {

	// Remember the anchor point before adjustment
	if( $debug ) {
	    $ox=$x;
	    $oy=$y;
	}

	if( !ereg("\n",$txt) || ($dir>0 && ereg("\n",$txt)) ) {
	    // Format a single line

	    $txt = $this->AddTxtCR($txt);

	    $bbox=$this->GetBBoxTTF($txt,$dir);
	    
	    // Align x,y ot lower left corner of bbox
	    $x -= $bbox[0];
	    $y -= $bbox[1];

	    // Note to self: "topanchor" is deprecated after we changed the
	    // bopunding box stuff. 
	    if( $this->text_halign=="right" || $this->text_halign=="topanchor" ) 
		$x -= $bbox[2]-$bbox[0];
	    elseif( $this->text_halign=="center" ) $x -= ($bbox[2]-$bbox[0])/2; 
	    
	    if( $this->text_valign=="top" ) $y += abs($bbox[5])+$bbox[1];
	    elseif( $this->text_valign=="center" ) $y -= ($bbox[5]-$bbox[1])/2; 

	    ImageTTFText ($this->img, $this->font_size, $dir, $x, $y, 
			  $this->current_color,$this->font_file,$txt); 

            if( $debug ) {
		// Draw the bounding rectangle and the bounding box
		$box=ImageTTFBBox($this->font_size,$dir,$this->font_file,$txt);
		$p = array();
		$p1 = array();
		for($i=0; $i < 4; ++$i) {
		    $p[] = $bbox[$i*2]+$x;
		    $p[] = $bbox[$i*2+1]+$y;
		    $p1[] = $box[$i*2]+$x;
		    $p1[] = $box[$i*2+1]+$y;
		}

		// Draw bounding box
		$this->PushColor('green');
		$this->Polygon($p1,true);
		$this->PopColor();
		
		// Draw bounding rectangle
		$this->PushColor('darkgreen');
		$this->Polygon($p,true);
		$this->PopColor();
		
		// Draw a cross at the anchor point
		$this->PushColor('red');
		$this->Line($ox-15,$oy,$ox+15,$oy);
		$this->Line($ox,$oy-15,$ox,$oy+15);
		$this->PopColor();
            }
	}
	else {
	    // Format a text paragraph
	    $fh=$this->GetFontHeight();

	    // Line margin is 15% of font height
	    $linemargin=round($fh*0.15);
	    $fh += $linemargin;
	    $w=$this->GetTextWidth($txt);

	    $y -= $linemargin/2;
	    $tmp = split("\n",$txt);
	    $nl = count($tmp);
	    $h = $nl * $fh;

	    if( $this->text_halign=="right") 				
		$x -= $dir==0 ? $w : $h;
	    elseif( $this->text_halign=="center" ) {
		$x -= $dir==0 ? $w/2 : $h/2;
	    }
	    
	    if( $this->text_valign=="top" )
		$y +=	$dir==0 ? $h : $w;
	    elseif( $this->text_valign=="center" ) 				
		$y +=	$dir==0 ? $h/2 : $w/2;

	    // Here comes a tricky bit. 
	    // Since we have to give the position for the string at the
	    // baseline this means thaht text will move slightly up
	    // and down depending on any of it's character descend below
	    // the baseline, for example a 'g'. To adjust the Y-position
	    // we therefore adjust the text with the baseline Y-offset
	    // as used for the current font and size. This will keep the
	    // baseline at a fixed positoned disregarding the actual 
	    // characters in the string. 
	    $standardbox = $this->GetTTFBBox('Gg',$dir);
	    $yadj = $standardbox[1];
	    $xadj = $standardbox[0];
	    for($i=0; $i < $nl; ++$i) {
		$wl = $this->GetTextWidth($tmp[$i]);
		$bbox = $this->GetTTFBBox($tmp[$i],$dir);
		if( $paragraph_align=="left" ) {
		    $xl = $x; 
		}
		elseif( $paragraph_align=="right" ) {
		    $xl = $x + ($w-$wl);
		}
		else {
		    // Center
		    $xl = $x + $w/2 - $wl/2 ;
		}

		$xl -= $bbox[0];
		$yl = $y  - $yadj; 
		$xl = $xl  - $xadj; 
		ImageTTFText ($this->img, $this->font_size, $dir, 
			      $xl, $yl-($h-$fh)+$fh*$i,
			      $this->current_color,$this->font_file,$tmp[$i]); 


		if( $debug  ) {
		    // Draw the bounding rectangle around each line
		    $box=ImageTTFBBox($this->font_size,$dir,$this->font_file,$tmp[$i]);
		    $p = array();
		    for($j=0; $j < 4; ++$j) {
			$p[] = $bbox[$j*2]+$xl;
			$p[] = $bbox[$j*2+1]+$yl-($h-$fh)+$fh*$i;
		    }
		    
		    // Draw bounding rectangle
		    $this->PushColor('darkgreen');
		    $this->Polygon($p,true);
		    $this->PopColor();
		}
	    }

	    if( $debug ) {
		
		// Draw a cross at the anchor point
		$this->PushColor('red');
		$this->Line($ox-25,$oy,$ox+25,$oy);
		$this->Line($ox,$oy-25,$ox,$oy+25);
		$this->PopColor();
	    }

	}
    }
	
    function StrokeText($x,$y,$txt,$dir=0,$paragraph_align="left",$debug=false) {

	$x = round($x);
	$y = round($y);

	// Do special language encoding
	$txt = $this->langconv->Convert($txt,$this->font_family);

	if( !is_numeric($dir) )
	    JpGraphError::Raise(" Direction for text most be given as an angle between 0 and 90.");
			
	if( $this->font_family >= FF_FONT0 && $this->font_family <= FF_FONT2+1) {	
	    $this->_StrokeBuiltinFont($x,$y,$txt,$dir,$paragraph_align,$debug);
	}
	elseif($this->font_family >= FF_COURIER && $this->font_family <= FF_BOOK)  { 
	    $this->_StrokeTTF($x,$y,$txt,$dir,$paragraph_align,$debug);
	}
	else
	    JpGraphError::Raise(" Unknown font font family specification. ");
    }
	
    function SetMargin($lm,$rm,$tm,$bm) {
	$this->left_margin=$lm;
	$this->right_margin=$rm;
	$this->top_margin=$tm;
	$this->bottom_margin=$bm;
	$this->plotwidth=$this->width - $this->left_margin-$this->right_margin ; 
	$this->plotheight=$this->height - $this->top_margin-$this->bottom_margin ;
	if( $this->plotwidth < 0  || $this->plotheight < 0 )
	    JpGraphError::raise("To small plot area. ($lm,$rm,$tm,$bm : $this->plotwidth x $this->plotheight). With the given image size and margins there is to little space left for the plot. Increase the plot size or reduce the margins.");
	$this->NotifyObservers();
    }

    function SetTransparent($color) {
	imagecolortransparent ($this->img,$this->rgb->allocate($color));
    }
	
    function SetColor($color,$aAlpha=0) {
	$this->current_color_name = $color;
	$this->current_color=$this->rgb->allocate($color,$aAlpha);
	if( $this->current_color == -1 ) {
	    $tc=imagecolorstotal($this->img);
	    JpGraphError::Raise("Can't allocate any more colors.
				Image has already allocated maximum of <b>$tc colors</b>. 
				This might happen if you have anti-aliasing turned on
				together with a background image or perhaps gradient fill 
				since this requires many, many colors. Try to turn off
				anti-aliasing.<p>
				If there is still a problem try downgrading the quality of
				the background image to use a smaller pallete to leave some 
				entries for your graphs. You should try to limit the number
				of colors in your background image to 64.<p>
				If there is still problem set the constant 
<pre>
DEFINE(\"USE_APPROX_COLORS\",true);
</pre>
				in jpgraph.php This will use approximative colors
				when the palette is full.
				<p>
				Unfortunately there is not much JpGraph can do about this
				since the palette size is a limitation of current graphic format and
				what the underlying GD library suppports."); 
	}
	return $this->current_color;
    }
	
    function PushColor($color) {
	if( $color != "" ) {
	    $this->colorstack[$this->colorstackidx]=$this->current_color_name;
	    $this->colorstack[$this->colorstackidx+1]=$this->current_color;
	    $this->colorstackidx+=2;
	    $this->SetColor($color);
	}
	else {
	    JpGraphError::Raise("Color specified as empty string in PushColor().");
	}
    }
	
    function PopColor() {
	if($this->colorstackidx<1)
	    JpGraphError::Raise(" Negative Color stack index. Unmatched call to PopColor()");
	$this->current_color=$this->colorstack[--$this->colorstackidx];
	$this->current_color_name=$this->colorstack[--$this->colorstackidx];
    }
	
	
    // Why this duplication? Because this way we can call this method
    // for any image and not only the current objsct
    function AdjSat($sat) {	$this->_AdjSat($this->img,$sat);	}	
	
    function _AdjSat($img,$sat) {
	$nbr = imagecolorstotal ($img);
	for( $i=0; $i<$nbr; ++$i ) {
	    $colarr = imagecolorsforindex ($img,$i);
	    $rgb[0]=$colarr["red"];
	    $rgb[1]=$colarr["green"];
	    $rgb[2]=$colarr["blue"];
	    $rgb = $this->AdjRGBSat($rgb,$sat);
	    imagecolorset ($img, $i, $rgb[0], $rgb[1], $rgb[2]);
	}
    }
	
    function AdjBrightContrast($bright,$contr=0) {
	$this->_AdjBrightContrast($this->img,$bright,$contr);
    }
    function _AdjBrightContrast($img,$bright,$contr=0) {
	if( $bright < -1 || $bright > 1 || $contr < -1 || $contr > 1 )
	    JpGraphError::Raise(" Parameters for brightness and Contrast out of range [-1,1]");		
	$nbr = imagecolorstotal ($img);
	for( $i=0; $i<$nbr; ++$i ) {
	    $colarr = imagecolorsforindex ($img,$i);
	    $r = $this->AdjRGBBrightContrast($colarr["red"],$bright,$contr);
	    $g = $this->AdjRGBBrightContrast($colarr["green"],$bright,$contr);
	    $b = $this->AdjRGBBrightContrast($colarr["blue"],$bright,$contr);		
	    imagecolorset ($img, $i, $r, $g, $b);
	}
    }
	
    // Private helper function for adj sat
    // Adjust saturation for RGB array $u. $sat is a value between -1 and 1
    // Note: Due to GD inability to handle true color the RGB values are only between
    // 8 bit. This makes saturation quite sensitive for small increases in parameter sat.
    // 
    // Tip: To get a grayscale picture set sat=-100, values <-100 changes the colors
    // to it's complement.
    // 
    // Implementation note: The saturation is implemented directly in the RGB space
    // by adjusting the perpendicular distance between the RGB point and the "grey"
    // line (1,1,1). Setting $sat>0 moves the point away from the line along the perp.
    // distance and a negative value moves the point closer to the line.
    // The values are truncated when the color point hits the bounding box along the
    // RGB axis.
    // DISCLAIMER: I'm not 100% sure this is he correct way to implement a color 
    // saturation function in RGB space. However, it looks ok and has the expected effect.
    function AdjRGBSat($rgb,$sat) {
	// TO DO: Should be moved to the RGB class
	// Grey vector
	$v=array(1,1,1);

	// Dot product
	$dot = $rgb[0]*$v[0]+$rgb[1]*$v[1]+$rgb[2]*$v[2];

	// Normalize dot product
	$normdot = $dot/3;	// dot/|v|^2

	// Direction vector between $u and its projection onto $v
	for($i=0; $i<3; ++$i)
	    $r[$i] = $rgb[$i] - $normdot*$v[$i];

	// Adjustment factor so that sat==1 sets the highest RGB value to 255
	if( $sat > 0 ) {
	    $m=0;
	    for( $i=0; $i<3; ++$i) {
		if( sign($r[$i]) == 1 && $r[$i]>0)
		    $m=max($m,(255-$rgb[$i])/$r[$i]);
	    }
	    $tadj=$m;
	}
	else
	    $tadj=1;
		
	$tadj = $tadj*$sat;	
	for($i=0; $i<3; ++$i) {
	    $un[$i] = round($rgb[$i] + $tadj*$r[$i]);		
	    if( $un[$i]<0 ) $un[$i]=0;		// Truncate color when they reach 0
	    if( $un[$i]>255 ) $un[$i]=255;// Avoid potential rounding error
	}		
	return $un;	
    }	

    // Private helper function for AdjBrightContrast
    function AdjRGBBrightContrast($rgb,$bright,$contr) {
	// TO DO: Should be moved to the RGB class
	// First handle contrast, i.e change the dynamic range around grey
	if( $contr <= 0 ) {
	    // Decrease contrast
	    $adj = abs($rgb-128) * (-$contr);
	    if( $rgb < 128 ) $rgb += $adj;
	    else $rgb -= $adj;
	}
	else { // $contr > 0
	    // Increase contrast
	    if( $rgb < 128 ) $rgb = $rgb - ($rgb * $contr);
	    else $rgb = $rgb + ((255-$rgb) * $contr);
	}
	
	// Add (or remove) various amount of white
	$rgb += $bright*255;	
	$rgb=min($rgb,255);
	$rgb=max($rgb,0);
	return $rgb;	
    }
	
    function SetLineWeight($weight) {
	$this->line_weight = $weight;
    }
	
    function SetStartPoint($x,$y) {
	$this->lastx=round($x);
	$this->lasty=round($y);
    }
	
    function Arc($cx,$cy,$w,$h,$s,$e) {
	// GD Arc doesn't like negative angles
	while( $s < 0) $s += 360;
	while( $e < 0) $e += 360;
    	
	imagearc($this->img,round($cx),round($cy),round($w),round($h),
		 $s,$e,$this->current_color);
    }
    
    function FilledArc($xc,$yc,$w,$h,$s,$e,$style="") {

	if( $GLOBALS['gd2'] ) {
	    while( $s < 0 ) $s += 360;
	    while( $e < 0 ) $e += 360;
	    if( $style=="" ) 
		$style=IMG_ARC_PIE;
	    imagefilledarc($this->img,round($xc),round($yc),round($w),round($h),
			   round($s),round($e),$this->current_color,$style);
	    return;
	}


	// In GD 1.x we have to do it ourself interesting enough there is surprisingly
	// little difference in time between doing it PHP and using the optimised GD 
	// library (roughly ~20%) I had expected it to be at least 100% slower doing it
	// manually with a polygon approximation in PHP.....
	$fillcolor = $this->current_color_name;

	$w /= 2; // We use radius in our calculations instead
	$h /= 2;

	// Setup the angles so we have the same conventions as the builtin
	// FilledArc() which is a little bit strange if you ask me....

	$s = 360-$s;
	$e = 360-$e;

	if( $e > $s ) {
	    $e = $e - 360;
	    $da = $s - $e; 
	}
	$da = $s-$e;

	// We use radians
	$s *= M_PI/180;
	$e *= M_PI/180;
	$da *= M_PI/180;

	// Calculate a polygon approximation
	$p[0] = $xc;
	$p[1] = $yc;

	// Heuristic on how many polygons we need to make the
	// arc look good
	$numsteps = round(8 * abs($da) * ($w+$h)*($w+$h)/1500);

	if( $numsteps == 0 ) return;
	if( $numsteps < 7 ) $numsteps=7;
	$delta = abs($da)/$numsteps;
	
	$pa=array();
	$a = $s;
	for($i=1; $i<=$numsteps; ++$i ) {
	    $p[2*$i] = round($xc + $w*cos($a));
	    $p[2*$i+1] = round($yc - $h*sin($a));
	    //$a = $s + $i*$delta; 
	    $a -= $delta; 
	    $pa[2*($i-1)] = $p[2*$i];
	    $pa[2*($i-1)+1] = $p[2*$i+1];
	}

	// Get the last point at the exact ending angle to avoid
	// any rounding errors.
	$p[2*$i] = round($xc + $w*cos($e));
	$p[2*$i+1] = round($yc - $h*sin($e));
	$pa[2*($i-1)] = $p[2*$i];
	$pa[2*($i-1)+1] = $p[2*$i+1];
	$i++;

	$p[2*$i] = $xc;
    	$p[2*$i+1] = $yc;
	if( $fillcolor != "" ) {
	    $this->PushColor($fillcolor);
	    imagefilledpolygon($this->img,$p,count($p)/2,$this->current_color);
	    $this->PopColor();
	}
    }

    function FilledCakeSlice($cx,$cy,$w,$h,$s,$e) {
	$this->CakeSlice($cx,$cy,$w,$h,$s,$e,$this->current_color_name);
    }

    function CakeSlice($xc,$yc,$w,$h,$s,$e,$fillcolor="",$arccolor="") {
	$s = round($s); $e = round($e);
	$w = round($w); $h = round($h);
	$xc = round($xc); $yc = round($yc);
	$this->PushColor($fillcolor);
	$this->FilledArc($xc,$yc,2*$w,2*$h,$s,$e);
	$this->PopColor();
	if( $arccolor != "" ) {
	    $this->PushColor($arccolor);
	    // We add 2 pixels to make the Arc() better aligned with
	    // the filled arc. 
	    if( $GLOBALS['gd2'] ) {
		imagefilledarc($this->img,$xc,$yc,2*$w,2*$h,$s,$e,$this->current_color_name,
			       IMG_ARC_NOFILL | IMG_ARC_EDGED ) ;
	    }
	    else {
		$this->Arc($xc,$yc,2*$w+2,2*$h+2,$s,$e);
		$xx = $w * cos(2*M_PI - $s*M_PI/180) + $xc;
		$yy = $yc - $h * sin(2*M_PI - $s*M_PI/180);
		$this->Line($xc,$yc,$xx,$yy);
		$xx = $w * cos(2*M_PI - $e*M_PI/180) + $xc;
		$yy = $yc - $h * sin(2*M_PI - $e*M_PI/180);
		$this->Line($xc,$yc,$xx,$yy);
	    }
	    $this->PopColor();
	}
    }

    function Ellipse($xc,$yc,$w,$h) {
	$this->Arc($xc,$yc,$w,$h,0,360);
    }
	
    // Breseham circle gives visually better result then using GD
    // built in arc(). It takes some more time but gives better
    // accuracy.
    function BresenhamCircle($xc,$yc,$r) {
	$d = 3-2*$r;
	$x = 0;
	$y = $r;
	while($x<=$y) {
	    $this->Point($xc+$x,$yc+$y);			
	    $this->Point($xc+$x,$yc-$y);
	    $this->Point($xc-$x,$yc+$y);
	    $this->Point($xc-$x,$yc-$y);
			
	    $this->Point($xc+$y,$yc+$x);
	    $this->Point($xc+$y,$yc-$x);
	    $this->Point($xc-$y,$yc+$x);
	    $this->Point($xc-$y,$yc-$x);
			
	    if( $d<0 ) $d += 4*$x+6;
	    else {
		$d += 4*($x-$y)+10;		
		--$y;
	    }
	    ++$x;
	}
    }
			
    function Circle($xc,$yc,$r) {
	if( USE_BRESENHAM )
	    $this->BresenhamCircle($xc,$yc,$r);
	else {

	    /*
            // Some experimental code snippet to see if we can get a decent 
	    // result doing a trig-circle
	    // Create an approximated circle with 0.05 rad resolution
	    $end = 2*M_PI;
	    $l = $r/10;
	    if( $l < 3 ) $l=3;
	    $step_size = 2*M_PI/(2*$r*M_PI/$l);
	    $pts = array();
	    $pts[] = $r + $xc;
	    $pts[] = $yc;
	    for( $a=$step_size; $a <= $end; $a += $step_size ) {
		$pts[] = round($xc + $r*cos($a));
		$pts[] = round($yc - $r*sin($a));
	    }
	    imagepolygon($this->img,$pts,count($pts)/2,$this->current_color);
	    */

	    $this->Arc($xc,$yc,$r*2,$r*2,0,360);		

	    // For some reason imageellipse() isn't in GD 2.0.1, PHP 4.1.1
	    //imageellipse($this->img,$xc,$yc,$r,$r,$this->current_color);
	}
    }
	
    function FilledCircle($xc,$yc,$r) {
	if( $GLOBALS['gd2'] ) {
	    imagefilledellipse($this->img,round($xc),round($yc),
	    		       2*$r,2*$r,$this->current_color);
	}
	else {
	    for( $i=1; $i < 2*$r; $i += 2 ) {
		// To avoid moire patterns we have to draw some
		// 1 extra "skewed" filled circles
		$this->Arc($xc,$yc,$i,$i,0,360);
		$this->Arc($xc,$yc,$i+1,$i,0,360);
		$this->Arc($xc,$yc,$i+1,$i+1,0,360);
	    }
	}	
    }
	
    // Linear Color InterPolation
    function lip($f,$t,$p) {
	$p = round($p,1);
	$r = $f[0] + ($t[0]-$f[0])*$p;
	$g = $f[1] + ($t[1]-$f[1])*$p;
	$b = $f[2] + ($t[2]-$f[2])*$p;
	return array($r,$g,$b);
    }

    // Anti-aliased line. 
    // Note that this is roughly 8 times slower then a normal line!
    function WuLine($x1,$y1,$x2,$y2) {
	// Get foreground line color
	$lc = imagecolorsforindex($this->img,$this->current_color);
	$lc = array($lc["red"],$lc["green"],$lc["blue"]);

	$dx = $x2-$x1;
	$dy = $y2-$y1;
	
	if( abs($dx) > abs($dy) ) {
	    if( $dx<0 ) {
		$dx = -$dx;$dy = -$dy;
		$tmp=$x2;$x2=$x1;$x1=$tmp;
		$tmp=$y2;$y2=$y1;$y1=$tmp;
	    }
	    $x=$x1<<16; $y=$y1<<16;
	    $yinc = ($dy*65535)/$dx;
	    while( ($x >> 16) < $x2 ) {
				
		$bc = @imagecolorsforindex($this->img,imagecolorat($this->img,$x>>16,$y>>16));
		if( $bc <= 0 ) {
		    JpGraphError::Raise('Problem with color palette and your GD setup. Please disable anti-aliasing or use GD2 with true-color. If you have GD2 library installed please make sure that you have set the USE_GD2 constant to true and that truecolor is enabled.');
		}
		$bc=array($bc["red"],$bc["green"],$bc["blue"]);
				
		$this->SetColor($this->lip($lc,$bc,($y & 0xFFFF)/65535));
		imagesetpixel($this->img,$x>>16,$y>>16,$this->current_color);
		$this->SetColor($this->lip($lc,$bc,(~$y & 0xFFFF)/65535));
		imagesetpixel($this->img,$x>>16,($y>>16)+1,$this->current_color);
		$x += 65536; $y += $yinc;
	    }
	}
	else {
	    if( $dy<0 ) {
		$dx = -$dx;$dy = -$dy;
		$tmp=$x2;$x2=$x1;$x1=$tmp;
		$tmp=$y2;$y2=$y1;$y1=$tmp;
	    }
	    $x=$x1<<16; $y=$y1<<16;
	    $xinc = ($dx*65535)/$dy;	
	    while( ($y >> 16) < $y2 ) {
				
		$bc = @imagecolorsforindex($this->img,imagecolorat($this->img,$x>>16,$y>>16));
		if( $bc <= 0 ) {
		    JpGraphError::Raise('Problem with color palette and your GD setup. Please disable anti-aliasing or use GD2 with true-color. If you have GD2 library installed please make sure that you have set the USE_GD2 constant to true and truecolor is enabled.');

		}

		$bc=array($bc["red"],$bc["green"],$bc["blue"]);				
				
		$this->SetColor($this->lip($lc,$bc,($x & 0xFFFF)/65535));
		imagesetpixel($this->img,$x>>16,$y>>16,$this->current_color);
		$this->SetColor($this->lip($lc,$bc,(~$x & 0xFFFF)/65535));
		imagesetpixel($this->img,($x>>16)+1,$y>>16,$this->current_color);
		$y += 65536; $x += $xinc;
	    }
	}
	$this->SetColor($lc);
	imagesetpixel($this->img,$x2,$y2,$this->current_color);		
	imagesetpixel($this->img,$x1,$y1,$this->current_color);			
    }

    // Set line style dashed, dotted etc
    function SetLineStyle($s) {
	if( is_numeric($s) ) {
	    if( $s<1 || $s>4 ) 
		JpGraphError::Raise(" Illegal numeric argument to SetLineStyle(): ($s)");
	}
	elseif( is_string($s) ) {
	    if( $s == "solid" ) $s=1;
	    elseif( $s == "dotted" ) $s=2;
	    elseif( $s == "dashed" ) $s=3;
	    elseif( $s == "longdashed" ) $s=4;
	    else JpGraphError::Raise(" Illegal string argument to SetLineStyle(): $s");
	}
	else JpGraphError::Raise(" Illegal argument to SetLineStyle $s");
	$this->line_style=$s;
    }
	
    // Same as Line but take the line_style into account
    function StyleLine($x1,$y1,$x2,$y2) {
	switch( $this->line_style ) {
	    case 1:// Solid
		$this->Line($x1,$y1,$x2,$y2);
		break;
	    case 2: // Dotted
		$this->DashedLine($x1,$y1,$x2,$y2,1,6);
		break;
	    case 3: // Dashed
		$this->DashedLine($x1,$y1,$x2,$y2,2,4);
		break;
	    case 4: // Longdashes
		$this->DashedLine($x1,$y1,$x2,$y2,8,6);
		break;
	    default:
		JpGraphError::Raise(" Unknown line style: $this->line_style ");
		break;
	}
    }

    function Line($x1,$y1,$x2,$y2) {

	$x1 = round($x1);
	$x2 = round($x2);
	$y1 = round($y1);
	$y2 = round($y2);

	if( $this->line_weight==0 ) return;
	if( $this->use_anti_aliasing ) {
	    $dx = $x2-$x1;
	    $dy = $y2-$y1;
	    // Vertical, Horizontal or 45 lines don't need anti-aliasing
	    if( $dx!=0 && $dy!=0 && $dx!=$dy ) {
		$this->WuLine($x1,$y1,$x2,$y2);
		return;
	    }
	}
	if( $this->line_weight==1 ) {
	    imageline($this->img,$x1,$y1,$x2,$y2,$this->current_color);
	}
	elseif( $x1==$x2 ) {		// Special case for vertical lines
	    imageline($this->img,$x1,$y1,$x2,$y2,$this->current_color);
	    $w1=floor($this->line_weight/2);
	    $w2=floor(($this->line_weight-1)/2);
	    for($i=1; $i<=$w1; ++$i) 
		imageline($this->img,$x1+$i,$y1,$x2+$i,$y2,$this->current_color);
	    for($i=1; $i<=$w2; ++$i) 
		imageline($this->img,$x1-$i,$y1,$x2-$i,$y2,$this->current_color);
	}
	elseif( $y1==$y2 ) {		// Special case for horizontal lines
	    imageline($this->img,$x1,$y1,$x2,$y2,$this->current_color);
	    $w1=floor($this->line_weight/2);
	    $w2=floor(($this->line_weight-1)/2);
	    for($i=1; $i<=$w1; ++$i) 
		imageline($this->img,$x1,$y1+$i,$x2,$y2+$i,$this->current_color);
	    for($i=1; $i<=$w2; ++$i) 
		imageline($this->img,$x1,$y1-$i,$x2,$y2-$i,$this->current_color);		
	}
	else {	// General case with a line at an angle
	    $a = atan2($y1-$y2,$x2-$x1);
	    // Now establish some offsets from the center. This gets a little
	    // bit involved since we are dealing with integer functions and we
	    // want the apperance to be as smooth as possible and never be thicker
	    // then the specified width.
			
	    // We do the trig stuff to make sure that the endpoints of the line
	    // are perpendicular to the line itself.
	    $dx=(sin($a)*$this->line_weight/2);
	    $dy=(cos($a)*$this->line_weight/2);

	    $pnts = array($x2+$dx,$y2+$dy,$x2-$dx,$y2-$dy,$x1-$dx,$y1-$dy,$x1+$dx,$y1+$dy);
	    imagefilledpolygon($this->img,$pnts,count($pnts)/2,$this->current_color);
	}		
	$this->lastx=$x2; $this->lasty=$y2;		
    }

    function Polygon($p,$closed=FALSE) {
	if( $this->line_weight==0 ) return;
	$n=count($p);
	$oldx = $p[0];
	$oldy = $p[1];
	for( $i=2; $i < $n; $i+=2 ) {
	    $this->Line($oldx,$oldy,$p[$i],$p[$i+1]);
	    $oldx = $p[$i];
	    $oldy = $p[$i+1];
	}
	if( $closed )
	    $this->Line($oldx,$oldy,$p[0],$p[1]);
    }
	
    function FilledPolygon($pts) {
	$n=count($pts);
	for($i=0; $i < $n; ++$i) 
	    $pts[$i] = round($pts[$i]);
	imagefilledpolygon($this->img,$pts,count($pts)/2,$this->current_color);
    }
	
    function Rectangle($xl,$yu,$xr,$yl) {
	$this->Polygon(array($xl,$yu,$xr,$yu,$xr,$yl,$xl,$yl,$xl,$yu));
    }
	
    function FilledRectangle($xl,$yu,$xr,$yl) {
	$this->FilledPolygon(array($xl,$yu,$xr,$yu,$xr,$yl,$xl,$yl));
    }

    function FilledRectangle2($xl,$yu,$xr,$yl,$color1,$color2,$style=1) {
	// Fill a rectangle with lines of two colors
	if( $style===1 ) {
	    // Horizontal stripe
	    if( $yl < $yu ) {
		$t = $yl; $yl=$yu; $yu=$t;
	    }
	    for( $y=$yu; $y <= $yl; ++$y) {
		$this->SetColor($color1);
		$this->Line($xl,$y,$xr,$y);
		++$y;
		$this->SetColor($color2);
		$this->Line($xl,$y,$xr,$y);
	    }
	}
	else {
	    if( $xl < $xl ) {
		$t = $xl; $xl=$xr; $xr=$t;
	    }
	    for( $x=$xl; $x <= $xr; ++$x) {
		$this->SetColor($color1);
		$this->Line($x,$yu,$x,$yl);
		++$x;
		$this->SetColor($color2);
		$this->Line($x,$yu,$x,$yl);
	    }
	}
    }

    function ShadowRectangle($xl,$yu,$xr,$yl,$fcolor=false,$shadow_width=3,$shadow_color=array(102,102,102)) {
	// This is complicated by the fact that we must also handle the case where
        // the reactangle has no fill color
	$this->PushColor($shadow_color);
	$this->FilledRectangle($xr-$shadow_width,$yu+$shadow_width,$xr,$yl-$shadow_width-1);
	$this->FilledRectangle($xl+$shadow_width,$yl-$shadow_width,$xr,$yl);
	//$this->FilledRectangle($xl+$shadow_width,$yu+$shadow_width,$xr,$yl);
	$this->PopColor();
	if( $fcolor==false )
	    $this->Rectangle($xl,$yu,$xr-$shadow_width-1,$yl-$shadow_width-1);
	else {		
	    $this->PushColor($fcolor);
	    $this->FilledRectangle($xl,$yu,$xr-$shadow_width-1,$yl-$shadow_width-1);
	    $this->PopColor();
	    $this->Rectangle($xl,$yu,$xr-$shadow_width-1,$yl-$shadow_width-1);
	}
    }

    function FilledRoundedRectangle($xt,$yt,$xr,$yl,$r=5) {
	if( $r==0 ) {
	    $this->FilledRectangle($xt,$yt,$xr,$yl);
	    return;
	}

	// To avoid overlapping fillings (which will look strange
	// when alphablending is enabled) we have no choice but 
	// to fill the five distinct areas one by one.
	
	// Center square
	$this->FilledRectangle($xt+$r,$yt+$r,$xr-$r,$yl-$r);
	// Top band
	$this->FilledRectangle($xt+$r,$yt,$xr-$r,$yt+$r-1);
	// Bottom band
	$this->FilledRectangle($xt+$r,$yl-$r+1,$xr-$r,$yl);
	// Left band
	$this->FilledRectangle($xt,$yt+$r+1,$xt+$r-1,$yl-$r);
	// Right band
	$this->FilledRectangle($xr-$r+1,$yt+$r,$xr,$yl-$r);

	// Topleft & Topright arc
	$this->FilledArc($xt+$r,$yt+$r,$r*2,$r*2,180,270);
	$this->FilledArc($xr-$r,$yt+$r,$r*2,$r*2,270,360);

	// Bottomleft & Bottom right arc
	$this->FilledArc($xt+$r,$yl-$r,$r*2,$r*2,90,180);
	$this->FilledArc($xr-$r,$yl-$r,$r*2,$r*2,0,90);

    }

    function RoundedRectangle($xt,$yt,$xr,$yl,$r=5) {    

	if( $r==0 ) {
	    $this->Rectangle($xt,$yt,$xr,$yl);
	    return;
	}

	// Top & Bottom line
	$this->Line($xt+$r,$yt,$xr-$r,$yt);
	$this->Line($xt+$r,$yl,$xr-$r,$yl);

	// Left & Right line
	$this->Line($xt,$yt+$r,$xt,$yl-$r);
	$this->Line($xr,$yt+$r,$xr,$yl-$r);

	// Topleft & Topright arc
	$this->Arc($xt+$r,$yt+$r,$r*2,$r*2,180,270);
	$this->Arc($xr-$r,$yt+$r,$r*2,$r*2,270,360);

	// Bottomleft & Bottomright arc
	$this->Arc($xt+$r,$yl-$r,$r*2,$r*2,90,180);
	$this->Arc($xr-$r,$yl-$r,$r*2,$r*2,0,90);
    }

    function FilledBevel($x1,$y1,$x2,$y2,$depth=2,$color1='white@0.4',$color2='darkgray@0.4') {
	$this->FilledRectangle($x1,$y1,$x2,$y2);
	$this->Bevel($x1,$y1,$x2,$y2,$depth,$color1,$color2);
    }

    function Bevel($x1,$y1,$x2,$y2,$depth=2,$color1='white@0.4',$color2='black@0.5') {
	$this->PushColor($color1);
	for( $i=0; $i < $depth; ++$i ) {
	    $this->Line($x1+$i,$y1+$i,$x1+$i,$y2-$i);
	    $this->Line($x1+$i,$y1+$i,$x2-$i,$y1+$i);
	}
	$this->PopColor();
	
	$this->PushColor($color2);
	for( $i=0; $i < $depth; ++$i ) {
	    $this->Line($x1+$i,$y2-$i,$x2-$i,$y2-$i);
	    $this->Line($x2-$i,$y1+$i,$x2-$i,$y2-$i-1);
	}
	$this->PopColor();
    }

    function StyleLineTo($x,$y) {
	$this->StyleLine($this->lastx,$this->lasty,$x,$y);
	$this->lastx=$x;
	$this->lasty=$y;
    }
	
    function LineTo($x,$y) {
	$this->Line($this->lastx,$this->lasty,$x,$y);
	$this->lastx=$x;
	$this->lasty=$y;
    }
	
    function Point($x,$y) {
	imagesetpixel($this->img,round($x),round($y),$this->current_color);
    }
	
    function Fill($x,$y) {
	imagefill($this->img,round($x),round($y),$this->current_color);
    }

    function FillToBorder($x,$y,$aBordColor) {
	$bc = $this->rgb->allocate($aBordColor);
	if( $bc == -1 ) {
	    JpGraphError::Raise('Image::FillToBorder : Can not allocate more colors');
	    exit();
	}
	imagefilltoborder($this->img,round($x),round($y),$bc,$this->current_color);
    }
	
    function DashedLine($x1,$y1,$x2,$y2,$dash_length=1,$dash_space=4) {
	// Code based on, but not identical to, work by Ariel Garza and James Pine
	$line_length = ceil (sqrt(pow(($x2 - $x1),2) + pow(($y2 - $y1),2)) );
	$dx = ($line_length) ? ($x2 - $x1) / $line_length : 0;
	$dy = ($line_length) ? ($y2 - $y1) / $line_length : 0;
	$lastx = $x1; $lasty = $y1;
	$xmax = max($x1,$x2);
	$xmin = min($x1,$x2);
	$ymax = max($y1,$y2);
	$ymin = min($y1,$y2);
	for ($i = 0; $i < $line_length; $i += ($dash_length + $dash_space)) {
	    $x = ($dash_length * $dx) + $lastx;
	    $y = ($dash_length * $dy) + $lasty;
			
	    // The last section might overshoot so we must take a computational hit
	    // and check this.
	    if( $x>$xmax ) $x=$xmax;
	    if( $y>$ymax ) $y=$ymax;
			
	    if( $x<$xmin ) $x=$xmin;
	    if( $y<$ymin ) $y=$ymin;

	    $this->Line($lastx,$lasty,$x,$y);
	    $lastx = $x + ($dash_space * $dx);
	    $lasty = $y + ($dash_space * $dy);
	} 
    } 

    function SetExpired($aFlg=true) {
	$this->expired = $aFlg;
    }
	
    // Generate image header
    function Headers() {
	if ($this->expired) {
	    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . "GMT");
	    header("Cache-Control: no-cache, must-revalidate");
	    header("Pragma: no-cache");
	}
	header("Content-type: image/$this->img_format");
    }

    // Adjust image quality for formats that allow this
    function SetQuality($q) {
	$this->quality = $q;
    }
	
    // Stream image to browser or to file
    function Stream($aFile="") {
	$func="image".$this->img_format;
	if( $this->img_format=="jpeg" && $this->quality != null ) {
	    $res = @$func($this->img,$aFile,$this->quality);
	}
	else {
	    if( $aFile != "" ) {
		$res = @$func($this->img,$aFile);
	    }
	    else
		$res = @$func($this->img);
	}
	if( !$res )
	    JpGraphError::Raise("Can't create or stream image to file $aFile Check that PHP has enough permission to write a file to the current directory.");
    }
		
    // Clear resource tide up by image
    function Destroy() {
	imagedestroy($this->img);
    }
	
    // Specify image format. Note depending on your installation
    // of PHP not all formats may be supported.
    function SetImgFormat($aFormat) {		
	$aFormat = strtolower($aFormat);
	$tst = true;
	$supported = imagetypes();
	if( $aFormat=="auto" ) {
	    if( $supported & IMG_PNG )
		$this->img_format="png";
	    elseif( $supported & IMG_JPG )
		$this->img_format="jpeg";
	    elseif( $supported & IMG_GIF )
		$this->img_format="gif";
	    else
		JpGraphError::Raise(" Your PHP (and GD-lib) installation does not appear to support any known graphic formats.".
				    "You need to first make sure GD is compiled as a module to PHP. If you also want to use JPEG images".
				    "you must get the JPEG library. Please see the PHP docs for details.");
				
	    return true;
	}
	else {
	    if( $aFormat=="jpeg" || $aFormat=="png" || $aFormat=="gif" ) {
		if( $aFormat=="jpeg" && !($supported & IMG_JPG) )
		    $tst=false;
		elseif( $aFormat=="png" && !($supported & IMG_PNG) ) 
		    $tst=false;
		elseif( $aFormat=="gif" && !($supported & IMG_GIF) ) 	
		    $tst=false;
		else {
		    $this->img_format=$aFormat;
		    return true;
		}
	    }
	    else 
		$tst=false;
	    if( !$tst )
		JpGraphError::Raise(" Your PHP installation does not support the chosen graphic format: $aFormat");
	}
    }	
} // CLASS

//===================================================
// CLASS RotImage
// Description: Exactly as Image but draws the image at
// a specified angle around a specified rotation point.
//===================================================
class RotImage extends Image {
    var $m=array();
    var $a=0;
    var $dx=0,$dy=0,$transx=0,$transy=0; 
	
    function RotImage($aWidth,$aHeight,$a=0,$aFormat=DEFAULT_GFORMAT) {
	$this->Image($aWidth,$aHeight,$aFormat);
	$this->dx=$this->left_margin+$this->plotwidth/2;
	$this->dy=$this->top_margin+$this->plotheight/2;
	$this->SetAngle($a);	
    }
	
    function SetCenter($dx,$dy) {
	$old_dx = $this->dx;
	$old_dy = $this->dy;
	$this->dx=$dx;
	$this->dy=$dy;
	$this->SetAngle($this->a);
	return array($old_dx,$old_dy);
    }
	
    function SetTranslation($dx,$dy) {
	$old = array($this->transx,$this->transy);
	$this->transx = $dx;
	$this->transy = $dy;
	return $old;
    }

    function UpdateRotMatrice()  {
	$a = $this->a;
	$a *= M_PI/180;
	$sa=sin($a); $ca=cos($a);		
	// Create the rotation matrix
	$this->m[0][0] = $ca;
	$this->m[0][1] = -$sa;
	$this->m[0][2] = $this->dx*(1-$ca) + $sa*$this->dy ;
	$this->m[1][0] = $sa;
	$this->m[1][1] = $ca;
	$this->m[1][2] = $this->dy*(1-$ca) - $sa*$this->dx ;
    }

    function SetAngle($a) {
	$tmp = $this->a;
	$this->a = $a;
	$this->UpdateRotMatrice();
	return $tmp;
    }

    function Circle($xc,$yc,$r) {
	// Circle get's rotated through the Arc() call
	// made in the parent class
	parent::Circle($xc,$yc,$r);
    }

    function FilledCircle($xc,$yc,$r) {
	// If we use GD1 then Image::FilledCircle will use a 
	// call to Arc so it will get rotated through the Arc
	// call.
	if( $GLOBALS['gd2'] ) {
	    list($xc,$yc) = $this->Rotate($xc,$yc);
	}
	parent::FilledCircle($xc,$yc,$r);
    }

	
    function Arc($xc,$yc,$w,$h,$s,$e) {
	list($xc,$yc) = $this->Rotate($xc,$yc);
	$s += $this->a;
	$e += $this->a;
	parent::Arc($xc,$yc,$w,$h,$s,$e);
    }

    function FilledArc($xc,$yc,$w,$h,$s,$e) {
	list($xc,$yc) = $this->Rotate($xc,$yc);
	$s += $this->a;
	$e += $this->a;
	parent::FilledArc($xc,$yc,$w,$h,$s,$e);
    }

    function SetMargin($lm,$rm,$tm,$bm) {
	parent::SetMargin($lm,$rm,$tm,$bm);
	$this->dx=$this->left_margin+$this->plotwidth/2;
	$this->dy=$this->top_margin+$this->plotheight/2;
	$this->UpdateRotMatrice();
    }
	
    function Rotate($x,$y) {
	// Optimization. Ignore rotation if Angle==0 || ANgle==360
	if( $this->a == 0 || $this->a == 360 ) {
	    return array($x + $this->transx, $y + $this->transy );
	}
	else {
	    $x1=round($this->m[0][0]*$x + $this->m[0][1]*$y,1) + $this->m[0][2] + $this->transx;
	    $y1=round($this->m[1][0]*$x + $this->m[1][1]*$y,1) + $this->m[1][2] + $this->transy;
	    return array($x1,$y1);
	}
    }
	
    function ArrRotate($pnts) {
	for($i=0; $i < count($pnts)-1; $i+=2)
	    list($pnts[$i],$pnts[$i+1]) = $this->Rotate($pnts[$i],$pnts[$i+1]);
	return $pnts;
    }
	
    function Line($x1,$y1,$x2,$y2) {
	list($x1,$y1) = $this->Rotate($x1,$y1);
	list($x2,$y2) = $this->Rotate($x2,$y2);
	parent::Line($x1,$y1,$x2,$y2);
    }

    function Rectangle($x1,$y1,$x2,$y2) {
	// Rectangle uses Line() so it will be rotated through that call
	parent::Rectangle($x1,$y1,$x2,$y2);
    }
	
    function FilledRectangle($x1,$y1,$x2,$y2) {
	if( $y1==$y2 || $x1==$x2 )
	    $this->Line($x1,$y1,$x2,$y2);
	else 
	    $this->FilledPolygon(array($x1,$y1,$x2,$y1,$x2,$y2,$x1,$y2));
    }
	
    function Polygon($pnts,$closed=FALSE) {
	//Polygon uses Line() so it will be rotated through that call
	parent::Polygon($pnts,$closed);
    }
	
    function FilledPolygon($pnts) {
	parent::FilledPolygon($this->ArrRotate($pnts));
    }
	
    function Point($x,$y) {
	list($xp,$yp) = $this->Rotate($x,$y);
	parent::Point($xp,$yp);
    }
	
    function StrokeText($x,$y,$txt,$dir=0,$paragraph_align="left",$debug=false) {
	list($xp,$yp) = $this->Rotate($x,$y);
	parent::StrokeText($xp,$yp,$txt,$dir,$paragraph_align,$debug);
    }
}

//===================================================
// CLASS ImgStreamCache
// Description: Handle caching of graphs to files
//===================================================
class ImgStreamCache {
    var $cache_dir;
    var $img=null;
    var $timeout=0; 	// Infinite timeout
    //---------------
    // CONSTRUCTOR
    function ImgStreamCache(&$aImg, $aCacheDir=CACHE_DIR) {
	$this->img = &$aImg;
	$this->cache_dir = $aCacheDir;
    }

//---------------
// PUBLIC METHODS	

    // Specify a timeout (in minutes) for the file. If the file is older then the
    // timeout value it will be overwritten with a newer version.
    // If timeout is set to 0 this is the same as infinite large timeout and if
    // timeout is set to -1 this is the same as infinite small timeout
    function SetTimeout($aTimeout) {
	$this->timeout=$aTimeout;	
    }
	
    // Output image to browser and also write it to the cache
    function PutAndStream(&$aImage,$aCacheFileName,$aInline,$aStrokeFileName) {
	// Some debugging code to brand the image with numbe of colors
	// used
	GLOBAL $gJpgBrandTiming;
	
	if( $gJpgBrandTiming ) {
	    global $tim;
	    $t=$tim->Pop()/1000.0;
	    $c=$aImage->SetColor("black");
	    $t=sprintf(BRAND_TIME_FORMAT,round($t,3));
	    imagestring($this->img->img,2,5,$this->img->height-20,$t,$c);			
	}

	// Check if we should stroke the image to an arbitrary file
	if( $aStrokeFileName!="" ) {
	    if( $aStrokeFileName == "auto" )
		$aStrokeFileName = GenImgName();
	    if( file_exists($aStrokeFileName) ) {
		// Delete the old file
		if( !@unlink($aStrokeFileName) )
		    JpGraphError::Raise(" Can't delete cached image $aStrokeFileName. Permission problem?");
	    }
	    $aImage->Stream($aStrokeFileName);
	    return;
	}

	if( $aCacheFileName != "" && USE_CACHE) {

	    $aCacheFileName = $this->cache_dir . $aCacheFileName;
	    if( file_exists($aCacheFileName) ) {
		if( !$aInline ) {
		    // If we are generating image off-line (just writing to the cache)
		    // and the file exists and is still valid (no timeout)
		    // then do nothing, just return.
		    $diff=time()-filemtime($aCacheFileName);
		    if( $diff < 0 )
			JpGraphError::Raise(" Cached imagefile ($aCacheFileName) has file date in the future!!");
		    if( $this->timeout>0 && ($diff <= $this->timeout*60) ) 
			return;		
		}			
		if( !@unlink($aCacheFileName) )
		    JpGraphError::Raise(" Can't delete cached image $aStrokeFileName. Permission problem?");
		$aImage->Stream($aCacheFileName);	
	    }
	    else {
		$this->MakeDirs(dirname($aCacheFileName));
		if( !is_writeable(dirname($aCacheFileName)) ) {
		    JpGraphError::Raise('PHP has not enough permissions to write to the cache file '.$aCacheFileName.'. Please make sure that the user running PHP has write permission for this file if you wan to use the cache system with JpGraph.');
		}
		$aImage->Stream($aCacheFileName);
	    }
			
	    $res=true;
	    // Set group to specified
	    if( CACHE_FILE_GROUP != "" )
		$res = @chgrp($aCacheFileName,CACHE_FILE_GROUP);
	    if( CACHE_FILE_MOD != "" )
		$res = @chmod($aCacheFileName,CACHE_FILE_MOD);
	    if( !$res )
		JpGraphError::Raise(" Can't set permission for cached image $aStrokeFileName. Permission problem?");
			
	    $aImage->Destroy();
	    if( $aInline ) {
		if ($fh = @fopen($aCacheFileName, "rb") ) {
		    $this->img->Headers();
		    fpassthru($fh);
		    return;
		}
		else
		    JpGraphError::Raise(" Cant open file from cache [$aFile]"); 
	    }
	}
	elseif( $aInline ) {
	    $this->img->Headers();	 		
	    $aImage->Stream();	
	    return;
	}
    }
	
    // Check if a given image is in cache and in that case
    // pass it directly on to web browser. Return false if the
    // image file doesn't exist or exists but is to old
    function GetAndStream($aCacheFileName) {
	$aCacheFileName = $this->cache_dir.$aCacheFileName;		 
	if ( USE_CACHE && file_exists($aCacheFileName) && $this->timeout>=0 ) {
	    $diff=time()-filemtime($aCacheFileName);
	    if( $this->timeout>0 && ($diff > $this->timeout*60) ) {
		return false;		
	    }
	    else {
		if ($fh = @fopen($aCacheFileName, "rb")) {
		    $this->img->Headers();
		    fpassthru($fh);
		    return true;
		}
		else
		    JpGraphError::Raise(" Can't open cached image \"$aCacheFileName\" for reading.");
	    }
	} 
	return false;
    }
	
    //---------------
    // PRIVATE METHODS	
    // Create all necessary directories in a path
    function MakeDirs($aFile) {
	$dirs = array();
	while ( !(file_exists($aFile)) ) {
	    $dirs[] = $aFile;
	    $aFile = dirname($aFile);
	}
	for ($i = sizeof($dirs)-1; $i>=0; $i--) {
	    if(! @mkdir($dirs[$i],0777) )
		JpGraphError::Raise(" Can't create directory $aFile. Make sure PHP has write permission to this directory.");
	    // We also specify mode here after we have changed group. 
	    // This is necessary if Apache user doesn't belong the
	    // default group and hence can't specify group permission
	    // in the previous mkdir() call
	    if( CACHE_FILE_GROUP != "" ) {
		$res=true;
		$res =@chgrp($dirs[$i],CACHE_FILE_GROUP);
		$res &= @chmod($dirs[$i],0777);
		if( !$res )
		    JpGraphError::Raise(" Can't set permissions for $aFile. Permission problems?");
	    }
	}
	return true;
    }	
} // CLASS Cache
	
//===================================================
// CLASS Legend
// Description: Responsible for drawing the box containing
// all the legend text for the graph
//===================================================
DEFINE('_DEFAULT_LPM_SIZE',8);
class Legend {
    var $color=array(0,0,0); // Default fram color
    var $fill_color=array(235,235,235); // Default fill color
    var $shadow=true; // Shadow around legend "box"
    var $shadow_color='gray';
    var $txtcol=array();
    var $mark_abs_size=_DEFAULT_LPM_SIZE;
    var $xmargin=10,$ymargin=6,$shadow_width=2;
    var $xpos=0.05, $ypos=0.15, $xabspos=-1, $yabspos=-1;
	var $halign="right", $valign="top";
    var $font_family=FF_FONT1,$font_style=FS_NORMAL,$font_size=12;
    var $font_color='black';
    var $hide=false,$layout_n=1;
    var $weight=1,$frameweight=1;
    var $csimareas='';
//---------------
// CONSTRUCTOR
    function Legend() {
	// Empty
    }
//---------------
// PUBLIC METHODS	
    function Hide($aHide=true) {
	$this->hide=$aHide;
    }
	
    function SetShadow($aShow='gray',$aWidth=2) {
	if( is_string($aShow) ) {
	    $this->shadow_color = $aShow;
	    $this->shadow=true;
	}
	else
	    $this->shadow=$aShow;
	$this->shadow_width=$aWidth;
    }

    function SetMarkAbsSize($aSize) {
	$this->mark_abs_size = $aSize ;
    }

    function SetLineWeight($aWeight) {
	$this->weight = $aWeight;
    }

    function SetFrameWeight($aWeight) {
	$this->frameweight = $aWeight;
    }
	
    function SetLayout($aDirection=LEGEND_VERT) {
	$this->layout_n = $aDirection==LEGEND_VERT ? 1 : 99 ;
    }
	
    function SetColumns($aCols) {
	$this->layout_n = $aCols ;
    }

    function SetLineSpacing($aSpacing) {
	$this->ymargin = $aSpacing ;
    }

    // Set color on frame around box
    function SetColor($aFontColor,$aColor='black') {
	$this->font_color=$aFontColor;
	$this->color=$aColor;
    }
	
    function SetFont($aFamily,$aStyle=FS_NORMAL,$aSize=10) {
	$this->font_family = $aFamily;
	$this->font_style = $aStyle;
	$this->font_size = $aSize;
    }
	
    function SetPos($aX,$aY,$aHAlign="right",$aVAlign="top") {
	$this->Pos($aX,$aY,$aHAlign,$aVAlign);
    }

    function SetAbsPos($aX,$aY,$aHAlign="right",$aVAlign="top") {
	$this->xabspos=$aX;
	$this->yabspos=$aY;
	$this->halign=$aHAlign;
	$this->valign=$aVAlign;
    }


    function Pos($aX,$aY,$aHAlign="right",$aVAlign="top") {
	if( !($aX<1 && $aY<1) )
	    JpGraphError::Raise(" Position for legend must be given as percentage in range 0-1");
	$this->xpos=$aX;
	$this->ypos=$aY;
	$this->halign=$aHAlign;
	$this->valign=$aVAlign;
    }

    function SetFillColor($aColor) {
	$this->fill_color=$aColor;
    }
	
    function Add($aTxt,$aColor,$aPlotmark="",$aLinestyle=0,$csimtarget="",$csimalt="") {
	$this->txtcol[]=array($aTxt,$aColor,$aPlotmark,$aLinestyle,$csimtarget,$csimalt);
    }

    function GetCSIMAreas() {
	return $this->csimareas;
    }
	
    function Stroke(&$aImg) {
	if( $this->hide ) return;

	$aImg->SetFont($this->font_family,$this->font_style,$this->font_size);		

	$n=count($this->txtcol);
	if( $n == 0 ) return;

	// Find out the max width and height of each column to be able
        // to size the legend box.
	$numcolumns = ($n > $this->layout_n ? $this->layout_n : $n);
	for( $i=0; $i < $numcolumns; ++$i ) {
	    $colwidth[$i] = $aImg->GetTextWidth($this->txtcol[$i][0]) +
		            2*$this->xmargin + 2*$this->mark_abs_size;
	    $colheight[$i] = 0;
	}

	// Find our maximum height in each row
	$rows = -1 ;
	for( $i=0; $i < $n; ++$i ) {
	    $h = max($this->mark_abs_size,$aImg->GetTextHeight($this->txtcol[$i][0]))+$this->ymargin;
	    if( $i % $numcolumns == 0 ) {
		$rows++;
		$rowheight[$rows] = 0;
	    }
	    $rowheight[$rows] = max($rowheight[$rows],$h);
	}

	$abs_height = 0;
	for( $i=0; $i <= $rows; ++$i ) {
	    $abs_height += $rowheight[$i] ;
	}

	// Make damn sure that the height is at least as high as mark size + ymargin
	$abs_height = max($abs_height,$this->mark_abs_size+$this->ymargin);
	$abs_height += 2*$this->ymargin;
						
	// Find out the maximum width in each column
	for( $i=$numcolumns; $i < $n; ++$i ) {
	    $colwidth[$i % $numcolumns] = max(
		$aImg->GetTextWidth($this->txtcol[$i][0])+2*$this->xmargin+2*$this->mark_abs_size, 
		$colwidth[$i % $numcolumns]);
	}
	
	// Get the total width
	$mtw = 0;
	for( $i=0; $i < $numcolumns; ++$i ) {
	    $mtw += $colwidth[$i];
	}

	// Find out maximum width we need for legend box
	$abs_width = $mtw+$this->xmargin;

	if( $this->xabspos === -1  && $this->yabspos === -1 ) {
	    $this->xabspos = $this->xpos*$aImg->width ;
	    $this->yabspos = $this->ypos*$aImg->height ;
	}

	// Positioning of the legend box
	if( $this->halign=="left" )
	    $xp = $this->xabspos; 
	elseif( $this->halign=="center" )
	    $xp = $this->xabspos - $abs_width/2; 
	else  
	    $xp = $aImg->width - $this->xabspos - $abs_width;

	$yp=$this->yabspos;
	if( $this->valign=="center" )
	    $yp-=$abs_height/2;
	elseif( $this->valign=="bottom" )
	    $yp-=$abs_height;
			
	// Stroke legend box
	$aImg->SetColor($this->color);	
	$aImg->SetLineWeight($this->frameweight);

	if( $this->shadow )
	    $aImg->ShadowRectangle($xp,$yp,$xp+$abs_width+$this->shadow_width,
				   $yp+$abs_height+$this->shadow_width,
				   $this->fill_color,$this->shadow_width,$this->shadow_color);
	else {
	    $aImg->SetColor($this->fill_color);				
	    $aImg->FilledRectangle($xp,$yp,$xp+$abs_width,$yp+$abs_height);
	    $aImg->SetColor($this->color);							
	    $aImg->Rectangle($xp,$yp,$xp+$abs_width,$yp+$abs_height);
	}

	// x1,y1 is the position for the legend mark
	$aImg->SetLineWeight($this->weight);
	$x1=$xp+$this->mark_abs_size/2+3;
	$y1=$yp + $this->mark_abs_size/2 + $this->ymargin/2; 
	
	$f2 =  round($aImg->GetTextHeight('X')/2);

	$grad = new Gradient($aImg);

	// Now stroke each legend in turn
	$i = 1 ; $row = 0;
	foreach($this->txtcol as $p) {
	    $x1 = round($x1); $y1=round($y1);
	    if ( $p[2] != "" && $p[2]->GetType() > -1 ) {
		// Make a plot mark legend
		$aImg->SetColor($p[1]);
		if( $p[3] > 0 ) {
		    $aImg->SetLineStyle($p[3]);
		    $aImg->StyleLine($x1-3,$y1+$f2,$x1+$this->mark_abs_size+3,$y1+$f2);
		}
		// Stroke a mark with the standard size
		// (As long as it is not an image mark )
		if( $p[2]->GetType() != MARK_IMG ) {
		    $p[2]->iFormatCallback = '';
		    if( $this->mark_abs_size === _DEFAULT_LPM_SIZE )
			$p[2]->SetDefaultWidth();	 
		    else 
		        $p[2]->SetSize($this->mark_abs_size);
		    $p[2]->Stroke($aImg,$x1+$this->mark_abs_size/2,$y1+$f2);
		}
	    } 
	    elseif ( $p[2] != "" && (is_string($p[3]) || $p[3]>0 ) ) {
		// Draw a styled line
		$aImg->SetColor($p[1]);
		$aImg->SetLineStyle($p[3]);
		$aImg->StyleLine($x1,$y1+$f2,$x1+$this->mark_abs_size,$y1+$f2);
		$aImg->StyleLine($x1,$y1+$f2+1,$x1+$this->mark_abs_size,$y1+$f2+1);
	    } 
	    else {
		// Draw a colored box
		$color = $p[1] ;
		$ym =  round($y1 + $f2 - $this->mark_abs_size/2);
		if( is_array($color) && count($color)==3 ) {
		    // The client want a gradient color
		    $grad->FilledRectangle($x1,$ym,
					   $x1+$this->mark_abs_size,$ym+$this->mark_abs_size,
					   $color[0],$color[1],$color[2]);
		}
		else {
		    $aImg->SetColor($p[1]);
		    $aImg->FilledRectangle($x1,$ym,$x1+$this->mark_abs_size,$ym+$this->mark_abs_size);
		}
		$aImg->SetColor($this->color);
		$aImg->SetLineWeight($this->weight);
		$aImg->Rectangle($x1,$ym,$x1+$this->mark_abs_size,$ym+$this->mark_abs_size);
	    }
	    $aImg->SetColor($this->font_color);
	    $aImg->SetFont($this->font_family,$this->font_style,$this->font_size);		
	    $aImg->SetTextAlign("left","top");			
	    $aImg->StrokeText(round($x1+$this->mark_abs_size+$this->xmargin*1.5),$y1,$p[0]);

	    // Add CSIM for Legend if defined
	    if( $p[4] != "" ) {
		$xe = $x1 + $this->xmargin+2*$this->mark_abs_size+$aImg->GetTextWidth($p[0]);
		$ye = $y1 + max($this->mark_abs_size,$aImg->GetTextHeight($p[0]));
		$coords = "$x1,$y1,$xe,$y1,$xe,$ye,$x1,$ye";
		$this->csimareas .= "<area shape=\"poly\" coords=\"$coords\" href=\"".$p[4]."\"";
		if( !empty($p[5]) ) {
		    $tmp=sprintf($p[5],$p[0]);
		    $this->csimareas .= " alt=\"$tmp\" title=\"$tmp\"";
		}
		$this->csimareas .= ">\n";
	    }
	    if( $i >= $this->layout_n ) {
		$x1 = $xp+$this->mark_abs_size/2+3;
		//$y1 += max($aImg->GetTextHeight($p[0]),$this->mark_abs_size)+$this->ymargin;
		$y1 += $rowheight[$row++];
		$i = 1;
	    }
	    else {
		$x1 += $colwidth[($i-1) % $numcolumns] ;
		++$i;
	    }
	}	
    }
} // Class
	

//===================================================
// CLASS DisplayValue
// Description: Used to print data values at data points
//===================================================
class DisplayValue {    
    var $show=false,$format="%.1f",$negformat="";
    var $iFormCallback='';
    var $angle=0;
    var $ff=FF_FONT1,$fs=FS_NORMAL,$fsize=10;
    var $color="navy",$negcolor="";
    var $margin=5,$valign="",$halign="center";
    var $iHideZero=false;

    function Show($aFlag=true) {
	$this->show=$aFlag;
    }

    function SetColor($aColor,$aNegcolor="") {
	$this->color = $aColor;
	$this->negcolor = $aNegcolor;
    }

    function SetFont($aFontFamily,$aFontStyle=FS_NORMAL,$aFontSize=10) {
	$this->ff=$aFontFamily;
	$this->fs=$aFontStyle;
	$this->fsize=$aFontSize;
    }

    function SetMargin($aMargin) {
	$this->margin = $aMargin;
    }

    function SetAngle($aAngle) {
	$this->angle = $aAngle;
    }

    function SetAlign($aHAlign,$aVAlign='') {
	$this->halign = $aHAlign;
	$this->valign = $aVAlign;
    }

    function SetFormat($aFormat,$aNegFormat="") {
	$this->format= $aFormat;
	$this->negformat= $aNegFormat;
    }

    function SetFormatCallback($aFunc) {
	$this->iFormCallback = $aFunc;
    }

    function HideZero($aFlag=true) {
	$this->iHideZero=$aFlag;
    }

    function Stroke($img,$aVal,$x,$y) {
	
	if( $this->show ) 
	{
	    if( $this->negformat=="" ) $this->negformat=$this->format;
	    if( $this->negcolor=="" ) $this->negcolor=$this->color;

	    if( $aVal===NULL || (is_string($aVal) && ($aVal=="" || $aVal=="-" || $aVal=="x" ) ) ) 
		return;

	    if( is_numeric($aVal) && $aVal==0 && $this->iHideZero ) {
		return;
	    }

	    // Since the value is used in different cirumstances we need to check what
	    // kind of formatting we shall use. For example, to display values in a line
	    // graph we simply display the formatted value, but in the case where the user
	    // has already specified a text string we don't fo anything.
	    if( $this->iFormCallback != '' ) {
		$f = $this->iFormCallback;
		$sval = $f($aVal);
	    }
	    elseif( is_numeric($aVal) ) {
		if( $aVal >= 0 )
		    $sval=sprintf($this->format,$aVal);
		else
		    $sval=sprintf($this->negformat,$aVal);
	    }
	    else
		$sval=$aVal;


	    $y = $y-sign($aVal)*$this->margin;

	    $txt = new Text($sval,$x,$y);
	    $txt->SetFont($this->ff,$this->fs,$this->fsize);
	    if( $this->valign == "" ) {
		if( $aVal >= 0 )
		    $valign = "bottom";
		else
		    $valign = "top";
	    }
	    else
		$valign = $this->valign;
	    $txt->Align($this->halign,$valign);

	    $txt->SetOrientation($this->angle);
	    if( $aVal > 0 )
		$txt->SetColor($this->color);
	    else
		$txt->SetColor($this->negcolor);
	    $txt->Stroke($img);
	}
    }
}


//===================================================
// CLASS Plot
// Description: Abstract base class for all concrete plot classes
//===================================================
class Plot {
    var $line_weight=1;
    var $coords=array();
    var $legend='',$hidelegend=false;
    var $csimtargets=array();	// Array of targets for CSIM
    var $csimareas="";			// Resultant CSIM area tags	
    var $csimalts=null;			// ALT:s for corresponding target
    var $color="black";
    var $numpoints=0;
    var $weight=1;	
    var $value;
    var $center=false;
    var $legendcsimtarget='';
    var $legendcsimalt='';
//---------------
// CONSTRUCTOR
    function Plot(&$aDatay,$aDatax=false) {
	$this->numpoints = count($aDatay);
	if( $this->numpoints==0 )
	    JpGraphError::Raise(" Empty data array specified for plot. Must have at least one data point.");
	$this->coords[0]=$aDatay;
	if( is_array($aDatax) )
	    $this->coords[1]=$aDatax;
	$this->value = new DisplayValue();
    }

//---------------
// PUBLIC METHODS	

    // Stroke the plot
    // "virtual" function which must be implemented by
    // the subclasses
    function Stroke(&$aImg,&$aXScale,&$aYScale) {
	JpGraphError::Raise("JpGraph: Stroke() must be implemented by concrete subclass to class Plot");
    }

    function HideLegend($f=true) {
	$this->hidelegend = $f;
    }

    function DoLegend(&$graph) {
	if( !$this->hidelegend )
	    $this->Legend($graph);
    }

    function StrokeDataValue($img,$aVal,$x,$y) {
	$this->value->Stroke($img,$aVal,$x,$y);
    }
	
    // Set href targets for CSIM	
    function SetCSIMTargets($aTargets,$aAlts=null) {
	$this->csimtargets=$aTargets;
	$this->csimalts=$aAlts;		
    }
 	
    // Get all created areas
    function GetCSIMareas() {
	return $this->csimareas;
    }	
	
    // "Virtual" function which gets called before any scale
    // or axis are stroked used to do any plot specific adjustment
    function PreStrokeAdjust(&$aGraph) {
	if( substr($aGraph->axtype,0,4) == "text" && (isset($this->coords[1])) )
	    JpGraphError::Raise("JpGraph: You can't use a text X-scale with specified X-coords. Use a \"int\" or \"lin\" scale instead.");
	return true;	
    }
	
    // Get minimum values in plot
    function Min() {
	if( isset($this->coords[1]) )
	    $x=$this->coords[1];
	else
	    $x="";
	if( $x != "" && count($x) > 0 )
	    $xm=min($x);
	else 
	    $xm=0;
	$y=$this->coords[0];
	if( count($y) > 0 ) {
	    $ym = $y[0];
	    $cnt = count($y);
	    $i=0;
	    while( $i<$cnt && !is_numeric($ym=$y[$i]) )
		$i++;
	    while( $i < $cnt) {
		if( is_numeric($y[$i]) ) 
		    $ym=min($ym,$y[$i]);
		++$i;
	    }			
	}
	else 
	    $ym="";
	return array($xm,$ym);
    }
	
    // Get maximum value in plot
    function Max() {
	if( isset($this->coords[1]) )
	    $x=$this->coords[1];
	else
	    $x="";

	if( $x!="" && count($x) > 0 )
	    $xm=max($x);
	else {
	    //$xm=count($this->coords[0])-1;	// We count from 0..(n-1)
	    $xm = $this->numpoints-1;
	}
	$y=$this->coords[0];
	if( count($y) > 0 ) {
	    if( !isset($y[0]) ) {
		$y[0] = 0;
// Change in 1.5.1 Don't treat this as an error any more. Just silently concert to 0
//		JpGraphError::Raise(" You have not specified a y[0] value!!");
	    }
	    $cnt = count($y);
	    $i=0;
	    while( $i<$cnt && !is_numeric($ym=$y[$i]) )
		$i++;				
	    while( $i < $cnt ) {
		if( is_numeric($y[$i]) ) $ym=max($ym,$y[$i]);
		++$i;
	    }
	}
	else 
	    $ym="";
	return array($xm,$ym);
    }
	
    function SetColor($aColor) {
	$this->color=$aColor;
    }
	
    function SetLegend($aLegend,$aCSIM="",$aCSIMAlt="") {
	$this->legend = $aLegend;
	$this->legendcsimtarget = $aCSIM;
	$this->legendcsimalt = $aCSIMAlt;
    }

    function SetWeight($aWeight) {
	$this->weight=$aWeight;
    }
		
    function SetLineWeight($aWeight=1) {
	$this->line_weight=$aWeight;
    }
	
    function SetCenter($aCenter=true) {
	$this->center = $aCenter;
    }
	
    // This method gets called by Graph class to plot anything that should go
    // into the margin after the margin color has been set.
    function StrokeMargin(&$aImg) {
	return true;
    }

    // Framework function the chance for each plot class to set a legend
    function Legend(&$aGraph) {
	if( $this->legend != "" )
	    $aGraph->legend->Add($this->legend,$this->color,"",0,$this->legendcsimtarget,$this->legendcsimalt);    
    }
	
} // Class

require_once "jpgraph_plotmark.inc" ;

//==============================================================================
// The following section contains classes to implement the "band" functionality
//==============================================================================

// Utility class to hold coordinates for a rectangle
class Rectangle {
    var $x,$y,$w,$h;
    var $xe, $ye;
    function Rectangle($aX,$aY,$aWidth,$aHeight) {
	$this->x=$aX;
	$this->y=$aY;
	$this->w=$aWidth;
	$this->h=$aHeight;
	$this->xe=$aX+$aWidth-1;
	$this->ye=$aY+$aHeight-1;
    }
}

//=====================================================================
// Class RectPattern
// Base class for pattern hierarchi that is used to display patterned
// bands on the graph. Any subclass that doesn't override Stroke()
// must at least implement method DoPattern(&$aImg) which is responsible
// for drawing the pattern onto the graph.
//=====================================================================
class RectPattern {
    var $color;
    var $weight;
    var $rect=null;
    var $doframe=true;
    var $linespacing;	// Line spacing in pixels
    var $iBackgroundColor=-1;  // Default is no background fill
	
    function RectPattern($aColor,$aWeight=1) {
	$this->color = $aColor;
	$this->weight = $aWeight;		
    }

    function SetBackground($aBackgroundColor) {
	$this->iBackgroundColor=$aBackgroundColor;
    }

    function SetPos(&$aRect) {
	$this->rect = $aRect;
    }
	
    function ShowFrame($aShow=true) {
	$this->doframe=$aShow;
    }

    function SetDensity($aDens) {
	if( $aDens <1 || $aDens > 100 )
	    JpGraphError::Raise(" Desity for pattern must be between 1 and 100. (You tried $aDens)");
	// 1% corresponds to linespacing=50
	// 100 % corresponds to linespacing 1
	$this->linespacing = floor(((100-$aDens)/100.0)*50)+1;

    }

    function Stroke(&$aImg) {
	if( $this->rect == null )
	    JpGraphError::Raise(" No positions specified for pattern.");

	if( !(is_numeric($this->iBackgroundColor) && $this->iBackgroundColor==-1) ) {
	    $aImg->SetColor($this->iBackgroundColor);
	    $aImg->FilledRectangle($this->rect->x,$this->rect->y,$this->rect->xe,$this->rect->ye); 
	}

	$aImg->SetColor($this->color);
	$aImg->SetLineWeight($this->weight);

	// Virtual function implemented by subclass
	$this->DoPattern($aImg);

	// Frame around the pattern area
	if( $this->doframe ) 
	    $aImg->Rectangle($this->rect->x,$this->rect->y,$this->rect->xe,$this->rect->ye);
    }

}


//=====================================================================
// Class RectPatternSolid
// Implements a solid band
//=====================================================================
class RectPatternSolid extends RectPattern {

    function RectPatternSolid($aColor="black",$aWeight=1) {
	parent::RectPattern($aColor,$aWeight);
    }

    function DoPattern(&$aImg) {
	$aImg->SetColor($this->color);
	$aImg->FilledRectangle($this->rect->x,$this->rect->y,
			       $this->rect->xe,$this->rect->ye);
    }
}

//=====================================================================
// Class RectPatternHor
// Implements horizontal line pattern
//=====================================================================
class RectPatternHor extends RectPattern {
		
    function RectPatternHor($aColor="black",$aWeight=1,$aLineSpacing=7) {
	parent::RectPattern($aColor,$aWeight);
	$this->linespacing = $aLineSpacing;
    }
		
    function DoPattern(&$aImg) {
	$x0 = $this->rect->x;		
	$x1 = $this->rect->xe;
	$y = $this->rect->y;
	while( $y < $this->rect->ye ) {
	    $aImg->Line($x0,$y,$x1,$y);
	    $y += $this->linespacing;
	}
    }
}

//=====================================================================
// Class RectPatternVert
// Implements vertical line pattern
//=====================================================================
class RectPatternVert extends RectPattern {
    var $linespacing=10;	// Line spacing in pixels
		
    function RectPatternVert($aColor="black",$aWeight=1,$aLineSpacing=7) {
	parent::RectPattern($aColor,$aWeight);
	$this->linespacing = $aLineSpacing;
    }

    //--------------------
    // Private methods
    //
    function DoPattern(&$aImg) {
	$x = $this->rect->x;		
	$y0 = $this->rect->y;
	$y1 = $this->rect->ye;
	while( $x < $this->rect->xe ) {
	    $aImg->Line($x,$y0,$x,$y1);
	    $x += $this->linespacing;
	}
    }
}


//=====================================================================
// Class RectPatternRDiag
// Implements right diagonal pattern
//=====================================================================
class RectPatternRDiag extends RectPattern {
    var $linespacing;	// Line spacing in pixels
		
    function RectPatternRDiag($aColor="black",$aWeight=1,$aLineSpacing=12) {
	parent::RectPattern($aColor,$aWeight);
	$this->linespacing = $aLineSpacing;
    }

    function DoPattern(&$aImg) {
	//  --------------------
	//  | /   /   /   /   /|
	//  |/   /   /   /   / |
	//  |   /   /   /   /  |
	//  --------------------
	$xe = $this->rect->xe;
	$ye = $this->rect->ye;
	$x0 = $this->rect->x + round($this->linespacing/2); 
	$y0 = $this->rect->y;
	$x1 = $this->rect->x; 
	$y1 = $this->rect->y + round($this->linespacing/2);

	while($x0<=$xe && $y1<=$ye) {
	    $aImg->Line($x0,$y0,$x1,$y1);
	    $x0 += $this->linespacing;
	    $y1 += $this->linespacing;
	}

	if( $xe-$x1 > $ye-$y0 ) { 
	    // Width larger than height
	    $x1 = $this->rect->x + ($y1-$ye);
	    $y1 = $ye; 
	    $y0 = $this->rect->y; 
	    while( $x0 <= $xe ) {
		$aImg->Line($x0,$y0,$x1,$y1);
		$x0 += $this->linespacing;
		$x1 += $this->linespacing;
	    }
	    
	    $y0=$this->rect->y + ($x0-$xe);
	    $x0=$xe;
	}
	else {
	    // Height larger than width
	    $diff = $x0-$xe;
	    $y0 = $diff+$this->rect->y;
	    $x0 = $xe;
	    $x1 = $this->rect->x;
	    while( $y1 <= $ye ) {
		$aImg->Line($x0,$y0,$x1,$y1);
		$y1 += $this->linespacing;
		$y0 += $this->linespacing;
	    }
	    
	    $diff = $y1-$ye;
	    $y1 = $ye;
	    $x1 = $diff + $this->rect->x;
	}

	while( $y0 <= $ye ) {
	    $aImg->Line($x0,$y0,$x1,$y1);
	    $y0 += $this->linespacing;		
	    $x1 += $this->linespacing;
	}
    }
}
 
//=====================================================================
// Class RectPatternLDiag
// Implements left diagonal pattern
//=====================================================================
class RectPatternLDiag extends RectPattern {
    var $linespacing;	// Line spacing in pixels
		
    function RectPatternLDiag($aColor="black",$aWeight=1,$aLineSpacing=12) {
	$this->linespacing = $aLineSpacing;
	parent::RectPattern($aColor,$aWeight);
    }

    function DoPattern(&$aImg) {
	//  --------------------
	//  |\   \   \   \   \ |
	//  | \   \   \   \   \|
	//  |  \   \   \   \   |
	//  |------------------|
	$xe = $this->rect->xe;
	$ye = $this->rect->ye;
	$x0 = $this->rect->x + round($this->linespacing/2); 
	$y0 = $this->rect->ye;
	$x1 = $this->rect->x; 
	$y1 = $this->rect->ye - round($this->linespacing/2);

	while($x0<=$xe && $y1>=$this->rect->y) {
	    $aImg->Line($x0,$y0,$x1,$y1);
	    $x0 += $this->linespacing;
	    $y1 -= $this->linespacing;
	}
	if( $xe-$x1 > $ye-$this->rect->y ) { 
	    // Width larger than height
	    $x1 = $this->rect->x + ($this->rect->y-$y1);
	    $y0=$ye; $y1=$this->rect->y; 
	    while( $x0 <= $xe ) {
		$aImg->Line($x0,$y0,$x1,$y1);
		$x0 += $this->linespacing;
		$x1 += $this->linespacing;
	    }
	    
	    $y0=$this->rect->ye - ($x0-$xe);
	    $x0=$xe;
	}
	else {
	    // Height larger than width
	    $diff = $x0-$xe;
	    $y0 = $ye-$diff;
	    $x0 = $xe;
	    while( $y1 >= $this->rect->y ) {
		$aImg->Line($x0,$y0,$x1,$y1);
		$y0 -= $this->linespacing;
		$y1 -= $this->linespacing;
	    }	    
	    $diff = $this->rect->y - $y1;
	    $x1 = $this->rect->x + $diff;
	    $y1 = $this->rect->y;
	}
	while( $y0 >= $this->rect->y ) {
	    $aImg->Line($x0,$y0,$x1,$y1);
	    $y0 -= $this->linespacing;
	    $x1 += $this->linespacing;
	}
    }
}

//=====================================================================
// Class RectPattern3DPlane
// Implements "3D" plane pattern
//=====================================================================
class RectPattern3DPlane extends RectPattern {
    var $alpha=50;  // Parameter that specifies the distance
    // to "simulated" horizon in pixel from the
    // top of the band. Specifies how fast the lines
    // converge.

    function RectPattern3DPlane($aColor="black",$aWeight=1) {
	parent::RectPattern($aColor,$aWeight);
	$this->SetDensity(10);  // Slightly larger default
    }

    function SetHorizon($aHorizon) {
	$this->alpha=$aHorizon;
    }
	
    function DoPattern(&$aImg) {
	// "Fake" a nice 3D grid-effect. 
	$x0 = $this->rect->x + $this->rect->w/2;
	$y0 = $this->rect->y;
	$x1 = $x0;
	$y1 = $this->rect->ye;
	$x0_right = $x0;
	$x1_right = $x1;

	// BTW "apa" means monkey in Swedish but is really a shortform for
	// "alpha+a" which was the labels I used on paper when I derived the
	// geometric to get the 3D perspective right. 
	// $apa is the height of the bounding rectangle plus the distance to the
	// artifical horizon (alpha)
	$apa = $this->rect->h + $this->alpha;

	// Three cases and three loops
	// 1) The endpoint of the line ends on the bottom line
	// 2) The endpoint ends on the side
	// 3) Horizontal lines

	// Endpoint falls on bottom line
	$middle=$this->rect->x + $this->rect->w/2;
	$dist=$this->linespacing;
	$factor=$this->alpha /($apa);
	while($x1>$this->rect->x) {
	    $aImg->Line($x0,$y0,$x1,$y1);
	    $aImg->Line($x0_right,$y0,$x1_right,$y1);
	    $x1 = $middle - $dist;
	    $x0 = $middle - $dist * $factor;
	    $x1_right = $middle + $dist;
	    $x0_right =  $middle + $dist * $factor;
	    $dist += $this->linespacing;
	}

	// Endpoint falls on sides
	$dist -= $this->linespacing;
	$d=$this->rect->w/2;
	$c = $apa - $d*$apa/$dist;
	while( $x0>$this->rect->x ) {
	    $aImg->Line($x0,$y0,$this->rect->x,$this->rect->ye-$c);
	    $aImg->Line($x0_right,$y0,$this->rect->xe,$this->rect->ye-$c);
	    $dist += $this->linespacing;			
	    $x0 = $middle - $dist * $factor;
	    $x1 = $middle - $dist;
	    $x0_right =  $middle + $dist * $factor;			
	    $c = $apa - $d*$apa/$dist;
	}		
		
	// Horizontal lines
	// They need some serious consideration since they are a function
	// of perspective depth (alpha) and density (linespacing)
	$x0=$this->rect->x;
	$x1=$this->rect->xe;
	$y=$this->rect->ye;
		
	// The first line is drawn directly. Makes the loop below slightly
	// more readable.
	$aImg->Line($x0,$y,$x1,$y);
	$hls = $this->linespacing;
		
	// A correction factor for vertical "brick" line spacing to account for
	// a) the difference in number of pixels hor vs vert
	// b) visual apperance to make the first layer of "bricks" look more
	// square.
	$vls = $this->linespacing*0.6;
		
	$ds = $hls*($apa-$vls)/$apa;
	// Get the slope for the "perspective line" going from bottom right
	// corner to top left corner of the "first" brick.
		
	// Uncomment the following lines if you want to get a visual understanding
	// of what this helpline does. BTW this mimics the way you would get the
	// perspective right when drawing on paper.
	/*
	  $x0 = $middle;
	  $y0 = $this->rect->ye;
	  $len=floor(($this->rect->ye-$this->rect->y)/$vls);
	  $x1 = $middle-round($len*$ds);
	  $y1 = $this->rect->ye-$len*$vls;
	  $aImg->PushColor("red");
	  $aImg->Line($x0,$y0,$x1,$y1);
	  $aImg->PopColor();
	*/
		
	$y -= $vls;		
	$k=($this->rect->ye-($this->rect->ye-$vls))/($middle-($middle-$ds));
	$dist = $hls;
	while( $y>$this->rect->y ) {
	    $aImg->Line($this->rect->x,$y,$this->rect->xe,$y);
	    $adj = $k*$dist/(1+$dist*$k/$apa);
	    if( $adj < 2 ) $adj=2;
	    $y = $this->rect->ye - round($adj);
	    $dist += $hls;
	}
    }
}

//=====================================================================
// Class RectPatternCross
// Vert/Hor crosses
//=====================================================================
class RectPatternCross extends RectPattern {
    var $vert=null;
    var $hor=null;
    function RectPatternCross($aColor="black",$aWeight=1) {
	parent::RectPattern($aColor,$aWeight);
	$this->vert = new RectPatternVert($aColor,$aWeight);
	$this->hor  = new RectPatternHor($aColor,$aWeight);
    }

    function SetOrder($aDepth) {
	$this->vert->SetOrder($aDepth);
	$this->hor->SetOrder($aDepth);
    }

    function SetPos(&$aRect) {
	parent::SetPos($aRect);
	$this->vert->SetPos($aRect);
	$this->hor->SetPos($aRect);
    }

    function SetDensity($aDens) {
	$this->vert->SetDensity($aDens);
	$this->hor->SetDensity($aDens);
    }

    function DoPattern(&$aImg) {
	$this->vert->DoPattern($aImg);
	$this->hor->DoPattern($aImg);
    }
}

//=====================================================================
// Class RectPatternDiagCross
// Vert/Hor crosses
//=====================================================================

class RectPatternDiagCross extends RectPattern {
    var $left=null;
    var $right=null;
    function RectPatternDiagCross($aColor="black",$aWeight=1) {
	parent::RectPattern($aColor,$aWeight);
	$this->right = new RectPatternRDiag($aColor,$aWeight);
	$this->left  = new RectPatternLDiag($aColor,$aWeight);
    }

    function SetOrder($aDepth) {
	$this->left->SetOrder($aDepth);
	$this->right->SetOrder($aDepth);
    }

    function SetPos(&$aRect) {
	parent::SetPos($aRect);
	$this->left->SetPos($aRect);
	$this->right->SetPos($aRect);
    }

    function SetDensity($aDens) {
	$this->left->SetDensity($aDens);
	$this->right->SetDensity($aDens);
    }

    function DoPattern(&$aImg) {
	$this->left->DoPattern($aImg);
	$this->right->DoPattern($aImg);
    }

}

//=====================================================================
// Class RectPatternFactory
// Factory class for rectangular pattern 
//=====================================================================
class RectPatternFactory {
    function RectPatternFactory() {
	// Empty
    }
    function Create($aPattern,$aColor,$aWeight=1) {
	switch($aPattern) {
	    case BAND_RDIAG:
		$obj =  new RectPatternRDiag($aColor,$aWeight);
		break;
	    case BAND_LDIAG:
		$obj =  new RectPatternLDiag($aColor,$aWeight);
		break;
	    case BAND_SOLID:
		$obj =  new RectPatternSolid($aColor,$aWeight);
		break;
	    case BAND_VLINE:
		$obj =  new RectPatternVert($aColor,$aWeight);
		break;
	    case BAND_HLINE:
		$obj =  new RectPatternHor($aColor,$aWeight);
		break;
	    case BAND_3DPLANE:
		$obj =  new RectPattern3DPlane($aColor,$aWeight);
		break;
	    case BAND_HVCROSS:
		$obj =  new RectPatternCross($aColor,$aWeight);
		break;
	    case BAND_DIAGCROSS:
		$obj =  new RectPatternDiagCross($aColor,$aWeight);
		break;
	    default:
		JpGraphError::Raise(" Unknown pattern specification ($aPattern)");
	}
	return $obj;
    }
}


//=====================================================================
// Class PlotBand
// Factory class which is used by the client.
// It is reposnsible for factoring the corresponding pattern
// concrete class.
//=====================================================================
class PlotBand {
    var $prect=null;
    var $depth;
    var $dir, $min, $max;

    function PlotBand($aDir,$aPattern,$aMin,$aMax,$aColor="black",$aWeight=1,$aDepth=DEPTH_BACK) {
	$f =  new RectPatternFactory();
	$this->prect = $f->Create($aPattern,$aColor,$aWeight);
	$this->dir = $aDir;
	$this->min = $aMin;
	$this->max = $aMax;
	$this->depth=$aDepth;
    }
	
    // Set position. aRect contains absolute image coordinates
    function SetPos(&$aRect) {
	assert( $this->prect != null ) ;
	$this->prect->SetPos($aRect);
    }
	
    function ShowFrame($aFlag=true) {
	$this->prect->ShowFrame($aFlag);
    }

    // Set z-order. In front of pplot or in the back
    function SetOrder($aDepth) {
	$this->depth=$aDepth;
    }
	
    function SetDensity($aDens) {
	$this->prect->SetDensity($aDens);
    }
	
    function GetDir() {
	return $this->dir;
    }
	
    function GetMin() {
	return $this->min;
    }
	
    function GetMax() {
	return $this->max;
    }
	
    // Display band
    function Stroke(&$aImg,&$aXScale,&$aYScale) {
	assert( $this->prect != null ) ;
	if( $this->dir == HORIZONTAL ) {
	    if( $this->min === 'min' ) $this->min = $aYScale->GetMinVal();
	    if( $this->max === 'max' ) $this->max = $aYScale->GetMaxVal();

            // Only draw the bar if it actually appears in the range
            if ($this->min < $aYScale->GetMaxVal() && $this->max > $aYScale->GetMinVal()) {
	    
	    // Trucate to limit of axis
	    $this->min = max($this->min, $aYScale->GetMinVal());
	    $this->max = min($this->max, $aYScale->GetMaxVal());

	    $x=$aXScale->scale_abs[0];
	    $y=$aYScale->Translate($this->max);
	    $width=$aXScale->scale_abs[1]-$aXScale->scale_abs[0]+1;
	    $height=abs($y-$aYScale->Translate($this->min))+1;
	    $this->prect->SetPos(new Rectangle($x,$y,$width,$height));
	    $this->prect->Stroke($aImg);
            }
	}
	else {	// VERTICAL
	    if( $this->min === 'min' ) $this->min = $aXScale->GetMinVal();
	    if( $this->max === 'max' ) $this->max = $aXScale->GetMaxVal();
            
            // Only draw the bar if it actually appears in the range
	    if ($this->min < $aXScale->GetMaxVal() && $this->max > $aXScale->GetMinVal()) {
	    
	    // Trucate to limit of axis
	    $this->min = max($this->min, $aXScale->GetMinVal());
	    $this->max = min($this->max, $aXScale->GetMaxVal());

	    $y=$aYScale->scale_abs[1];
	    $x=$aXScale->Translate($this->min);
	    $height=abs($aYScale->scale_abs[1]-$aYScale->scale_abs[0]);
	    $width=abs($x-$aXScale->Translate($this->max));
	    $this->prect->SetPos(new Rectangle($x,$y,$width,$height));
	    $this->prect->Stroke($aImg);
            }
	}
    }
}

//===================================================
// CLASS PlotLine
// Description: 
// Data container class to hold properties for a static
// line that is drawn directly in the plot area.
// Usefull to add static borders inside a plot to show
// for example set-values
//===================================================
class PlotLine {
    var $weight=1;
    var $color="black";
    var $direction=-1; 
    var $scaleposition;

//---------------
// CONSTRUCTOR
    function PlotLine($aDir=HORIZONTAL,$aPos=0,$aColor="black",$aWeight=1) {
	$this->direction = $aDir;
	$this->color=$aColor;
	$this->weight=$aWeight;
	$this->scaleposition=$aPos;
    }
	
//---------------
// PUBLIC METHODS	
    function SetPosition($aScalePosition) {
	$this->scaleposition=$aScalePosition;
    }
	
    function SetDirection($aDir) {
	$this->direction = $aDir;
    }
	
    function SetColor($aColor) {
	$this->color=$aColor;
    }
	
    function SetWeight($aWeight) {
	$this->weight=$aWeight;
    }
	
    function Stroke(&$aImg,&$aXScale,&$aYScale) {
	$aImg->SetColor($this->color);
	$aImg->SetLineWeight($this->weight);		
	if( $this->direction == VERTICAL ) {
	    $ymin_abs=$aYScale->Translate($aYScale->GetMinVal());
	    $ymax_abs=$aYScale->Translate($aYScale->GetMaxVal());
	    $xpos_abs=$aXScale->Translate($this->scaleposition);
	    $aImg->Line($xpos_abs, $ymin_abs, $xpos_abs, $ymax_abs);
	}
	elseif( $this->direction == HORIZONTAL ) {
	    $xmin_abs=$aXScale->Translate($aXScale->GetMinVal());
	    $xmax_abs=$aXScale->Translate($aXScale->GetMaxVal());
	    $ypos_abs=$aYScale->Translate($this->scaleposition);
	    $aImg->Line($xmin_abs, $ypos_abs, $xmax_abs, $ypos_abs);
	}
	else
	    JpGraphError::Raise(" Illegal direction for static line");
    }
}

// <EOF>
?>
