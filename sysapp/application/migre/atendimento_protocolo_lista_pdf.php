<?php
// ----------------------

include_once('inc/sessao.php');
include_once('inc/conexao.php');
include_once('inc/ePrev.Util.String.php');
include_once('inc/ePrev.Service.Projetos.php');
include_once('inc/ePrev.ADO.Projetos.atendimento_protocolo.php');
include ('inc/fpdf153/fpdf.php');

include 'oo/start.php';
using( array('projetos.atendimento_protocolo_tipo', 'projetos.atendimento_protocolo_discriminacao', 'projetos.usuarios_controledi') );

// ----------------------

function ConvertSize($size=5,$maxsize=0)
{
	if ( stristr($size,'px') ) $size *= 0.2645; //pixels
	elseif ( stristr($size,'cm') ) $size *= 10; //centimeters
	elseif ( stristr($size,'mm') ) $size += 0; //millimeters
	elseif ( stristr($size,'in') ) $size *= 25.4; //inches 
	elseif ( stristr($size,'pc') ) $size *= 38.1/9; //PostScript picas 
	elseif ( stristr($size,'pt') ) $size *= 25.4/72; //72dpi
	elseif ( stristr($size,'%') )
	{
		$size += 0; //make "90%" become simply "90" 
		$size *= $maxsize/100;
	}
	else $size *= 0.2645; //nothing == px

	return $size;
}

// ----------------------

class PDF extends FPDF
{
	var $widths;
	var $aligns;
	var $legends;
	var $wLegend;
	var $sum;
	var $NbVal;		
	var $ar_mes = array("Janeiro","Fevereiro","Março","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");

	function novaPagina()
	{
		$this->AddPage('l');	
		$this->SetTopMargin(15);
		$ar_estilo = (array('width' => 0.4, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0,102,0)));
		$this->RoundedRect(10, 10, 277, 190, 3.50,'1111', 'DF',$ar_estilo, array(255, 255, 255));	
		$this->Image('img/marcadagua.jpg', 153, 63, ConvertSize(500,$ob_pdf->pgwidth), ConvertSize(497,$ob_pdf->pgwidth),'','',false);
		$this->SetX(15);
		$this->SetLineWidth(0);
		$this->SetDrawColor(0,0,0);				
	}		

	function SetWidths($w)
	{
	    //Set the array of column widths
	    $this->widths=$w;
	}

	function SetAligns($a)
	{
	    //Set the array of column alignments
	    $this->aligns=$a;
	}

	function Row($data)
	{
	    //Calculate the height of the row
	    $nb=0;
	    for($i=0;$i<count($data);$i++)
	        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
	    $h=5*$nb;
	    //Issue a page break first if needed
	    $this->CheckPageBreak($h);
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->GetX();
	        $y=$this->GetY();
	        //Draw the border
	        # function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
			$this->Rect($x,$y,$w,$h);
	        //Print the text
	        $this->MultiCell($w,5,$data[$i],0,$a);
	        //Put the position to the right of the cell
	        $this->SetXY($x+$w,$y);
	    }
	    //Go to the next line
	    $this->Ln($h);
	}

	function CheckPageBreak($h)
	{
	    //If the height h would cause an overflow, add a new page immediately
	    if($this->GetY()+$h>$this->PageBreakTrigger)
	        #$this->AddPage($this->CurOrientation); 
			$this->novaPagina();
	}

	function NbLines($w,$txt)
	{
	    //Computes the number of lines a MultiCell of width w will take
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r",'',$txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	            }
	            else
	                $i=$sep+1;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	        }
	        else
	            $i++;
	    }
	    return $nl;
	}		
	
    // Sets line style
    // Parameters:
    // - style: Line style. Array with keys among the following:
    //   . width: Width of the line in user units
    //   . cap: Type of cap to put on the line (butt, round, square). The difference between 'square' and 'butt' is that 'square' projects a flat end past the end of the line.
    //   . join: miter, round or bevel
    //   . dash: Dash pattern. Is 0 (without dash) or array with series of length values, which are the lengths of the on and off dashes.
    //           For example: (2) represents 2 on, 2 off, 2 on , 2 off ...
    //                        (2,1) is 2 on, 1 off, 2 on, 1 off.. etc
    //   . phase: Modifier of the dash pattern which is used to shift the point at which the pattern starts
    //   . color: Draw color. Array with components (red, green, blue)
    function SetLineStyle($style) {
        extract($style);
        if (isset($width)) {
            $width_prev = $this->LineWidth;
            $this->SetLineWidth($width);
            $this->LineWidth = $width_prev;
        }
        if (isset($cap)) {
            $ca = array('butt' => 0, 'round'=> 1, 'square' => 2);
            if (isset($ca[$cap]))
                $this->_out($ca[$cap] . ' J');
        }
        if (isset($join)) {
            $ja = array('miter' => 0, 'round' => 1, 'bevel' => 2);
            if (isset($ja[$join]))
                $this->_out($ja[$join] . ' j');
        }
        if (isset($dash)) {
            $dash_string = '';
            if ($dash) {
                if(preg_match('/^.+,/', $dash))
                    $tab = explode(',', $dash);
                else
                    $tab = array($dash);
                $dash_string = '';
                foreach ($tab as $i => $v) {
                    if ($i > 0)
                        $dash_string .= ' ';
                    $dash_string .= sprintf('%.2f', $v);
                }
            }
            if (!isset($phase) || !$dash)
                $phase = 0;
            $this->_out(sprintf('[%s] %.2f d', $dash_string, $phase));
        }
        if (isset($color)) {
            list($r, $g, $b) = $color;
            $this->SetDrawColor($r, $g, $b);
        }
    }

    // Draws a line
    // Parameters:
    // - x1, y1: Start point
    // - x2, y2: End point
    // - style: Line style. Array like for SetLineStyle
    function Line($x1, $y1, $x2, $y2, $style = null) {
        if ($style)
            $this->SetLineStyle($style);
        parent::Line($x1, $y1, $x2, $y2);
    }

    // Draws a rectangle
    // Parameters:
    // - x, y: Top left corner
    // - w, h: Width and height
    // - style: Style of rectangle (draw and/or fill: D, F, DF, FD)
    // - border_style: Border style of rectangle. Array with some of this index
    //   . all: Line style of all borders. Array like for SetLineStyle
    //   . L: Line style of left border. null (no border) or array like for SetLineStyle
    //   . T: Line style of top border. null (no border) or array like for SetLineStyle
    //   . R: Line style of right border. null (no border) or array like for SetLineStyle
    //   . B: Line style of bottom border. null (no border) or array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $border_style = null;
                parent::Rect($x, $y, $w, $h, $style);
                break;
            case 'DF': case 'FD':
                if (!$border_style || isset($border_style['all'])) {
                    if (isset($border_style['all'])) {
                        $this->SetLineStyle($border_style['all']);
                        $border_style = null;
                    }
                } else
                    $style = 'F';
                parent::Rect($x, $y, $w, $h, $style);
                break;
            default:
                if (!$border_style || isset($border_style['all'])) {
                    if (isset($border_style['all']) && $border_style['all']) {
                        $this->SetLineStyle($border_style['all']);
                        $border_style = null;
                    }
                    parent::Rect($x, $y, $w, $h, $style);
                }
                break;
        }
        if ($border_style) {
            if (isset($border_style['L']) && $border_style['L'])
                $this->Line($x, $y, $x, $y + $h, $border_style['L']);
            if (isset($border_style['T']) && $border_style['T'])
                $this->Line($x, $y, $x + $w, $y, $border_style['T']);
            if (isset($border_style['R']) && $border_style['R'])
                $this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
            if (isset($border_style['B']) && $border_style['B'])
                $this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
        }
    }

    // Draws a Bézier curve (the Bézier curve is tangent to the line between the control points at either end of the curve)
    // Parameters:
    // - x0, y0: Start point
    // - x1, y1: Control point 1
    // - x2, y2: Control point 2
    // - x3, y3: End point
    // - style: Style of rectangule (draw and/or fill: D, F, DF, FD)
    // - line_style: Line style for curve. Array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function Curve($x0, $y0, $x1, $y1, $x2, $y2, $x3, $y3, $style = '', $line_style = null, $fill_color = null) {
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $op = 'f';
                $line_style = null;
                break;
            case 'FD': case 'DF':
                $op = 'B';
                break;
            default:
                $op = 'S';
                break;
        }
        if ($line_style)
            $this->SetLineStyle($line_style);

        $this->_Point($x0, $y0);
        $this->_Curve($x1, $y1, $x2, $y2, $x3, $y3);
        $this->_out($op);
    }

    // Draws an ellipse
    // Parameters:
    // - x0, y0: Center point
    // - rx, ry: Horizontal and vertical radius (if ry = 0, draws a circle)
    // - angle: Orientation angle (anti-clockwise)
    // - astart: Start angle
    // - afinish: Finish angle
    // - style: Style of ellipse (draw and/or fill: D, F, DF, FD, C (D + close))
    // - line_style: Line style for ellipse. Array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - nSeg: Ellipse is made up of nSeg Bézier curves
    function Ellipse($x0, $y0, $rx, $ry = 0, $angle = 0, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
        if ($rx) {
            if (!(false === strpos($style, 'F')) && $fill_color) {
                list($r, $g, $b) = $fill_color;
                $this->SetFillColor($r, $g, $b);
            }
            switch ($style) {
                case 'F':
                    $op = 'f';
                    $line_style = null;
                    break;
                case 'FD': case 'DF':
                    $op = 'B';
                    break;
                case 'C':
                    $op = 's'; // small 's' means closing the path as well
                    break;
                default:
                    $op = 'S';
                    break;
            }
            if ($line_style)
                $this->SetLineStyle($line_style);
            if (!$ry)
                $ry = $rx;
            $rx *= $this->k;
            $ry *= $this->k;
            if ($nSeg < 2)
                $nSeg = 2;

            $astart = deg2rad((float) $astart);
            $afinish = deg2rad((float) $afinish);
            $totalAngle = $afinish - $astart;

            $dt = $totalAngle/$nSeg;
            $dtm = $dt/3;

            $x0 *= $this->k;
            $y0 = ($this->h - $y0) * $this->k;
            if ($angle != 0) {
                $a = -deg2rad((float) $angle);
                $this->_out(sprintf('q %.2f %.2f %.2f %.2f %.2f %.2f cm', cos($a), -1 * sin($a), sin($a), cos($a), $x0, $y0));
                $x0 = 0;
                $y0 = 0;
            }

            $t1 = $astart;
            $a0 = $x0 + ($rx * cos($t1));
            $b0 = $y0 + ($ry * sin($t1));
            $c0 = -$rx * sin($t1);
            $d0 = $ry * cos($t1);
            $this->_Point($a0 / $this->k, $this->h - ($b0 / $this->k));
            for ($i = 1; $i <= $nSeg; $i++) {
                // Draw this bit of the total curve
                $t1 = ($i * $dt) + $astart;
                $a1 = $x0 + ($rx * cos($t1));
                $b1 = $y0 + ($ry * sin($t1));
                $c1 = -$rx * sin($t1);
                $d1 = $ry * cos($t1);
                $this->_Curve(($a0 + ($c0 * $dtm)) / $this->k,
                            $this->h - (($b0 + ($d0 * $dtm)) / $this->k),
                            ($a1 - ($c1 * $dtm)) / $this->k,
                            $this->h - (($b1 - ($d1 * $dtm)) / $this->k),
                            $a1 / $this->k,
                            $this->h - ($b1 / $this->k));
                $a0 = $a1;
                $b0 = $b1;
                $c0 = $c1;
                $d0 = $d1;
            }
            $this->_out($op);
            if ($angle !=0)
                $this->_out('Q');
        }
    }

    // Draws a circle
    // Parameters:
    // - x0, y0: Center point
    // - r: Radius
    // - astart: Start angle
    // - afinish: Finish angle
    // - style: Style of circle (draw and/or fill) (D, F, DF, FD, C (D + close))
    // - line_style: Line style for circle. Array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - nSeg: Ellipse is made up of nSeg Bézier curves
    function Circle($x0, $y0, $r, $astart = 0, $afinish = 360, $style = '', $line_style = null, $fill_color = null, $nSeg = 8) {
        $this->Ellipse($x0, $y0, $r, 0, 0, $astart, $afinish, $style, $line_style, $fill_color, $nSeg);
    }

    // Draws a polygon
    // Parameters:
    // - p: Points. Array with values x0, y0, x1, y1,..., x(np-1), y(np - 1)
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..np-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function Polygon($p, $style = '', $line_style = null, $fill_color = null) {
        $np = count($p) / 2;
        if (!(false === strpos($style, 'F')) && $fill_color) {
            list($r, $g, $b) = $fill_color;
            $this->SetFillColor($r, $g, $b);
        }
        switch ($style) {
            case 'F':
                $line_style = null;
                $op = 'f';
                break;
            case 'FD': case 'DF':
                $op = 'B';
                break;
            default:
                $op = 'S';
                break;
        }
        $draw = true;
        if ($line_style)
            if (isset($line_style['all']))
                $this->SetLineStyle($line_style['all']);
            else { // 0 .. (np - 1), op = {B, S}
                $draw = false;
                if ('B' == $op) {
                    $op = 'f';
                    $this->_Point($p[0], $p[1]);
                    for ($i = 2; $i < ($np * 2); $i = $i + 2)
                        $this->_Line($p[$i], $p[$i + 1]);
                    $this->_Line($p[0], $p[1]);
                    $this->_out($op);
                }
                $p[$np * 2] = $p[0];
                $p[($np * 2) + 1] = $p[1];
                for ($i = 0; $i < $np; $i++)
                    if (!empty($line_style[$i]))
                        $this->Line($p[$i * 2], $p[($i * 2) + 1], $p[($i * 2) + 2], $p[($i * 2) + 3], $line_style[$i]);
            }

        if ($draw) {
            $this->_Point($p[0], $p[1]);
            for ($i = 2; $i < ($np * 2); $i = $i + 2)
                $this->_Line($p[$i], $p[$i + 1]);
            $this->_Line($p[0], $p[1]);
            $this->_out($op);
        }
    }

    // Draws a regular polygon
    // Parameters:
    // - x0, y0: Center point
    // - r: Radius of circumscribed circle
    // - ns: Number of sides
    // - angle: Orientation angle (anti-clockwise)
    // - circle: Draw circumscribed circle or not
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..ns-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
    // - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
    // - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
    function RegularPolygon($x0, $y0, $r, $ns, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
        if ($ns < 3)
            $ns = 3;
        if ($circle)
            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
        $p = null;
        for ($i = 0; $i < $ns; $i++) {
            $a = $angle + ($i * 360 / $ns);
            $a_rad = deg2rad((float) $a);
            $p[] = $x0 + ($r * sin($a_rad));
            $p[] = $y0 + ($r * cos($a_rad));
        }
        $this->Polygon($p, $style, $line_style, $fill_color);
    }

    // Draws a star polygon
    // Parameters:
    // - x0, y0: Center point
    // - r: Radius of circumscribed circle
    // - nv: Number of vertices
    // - ng: Number of gaps (ng % nv = 1 => regular polygon)
    // - angle: Orientation angle (anti-clockwise)
    // - circle: Draw circumscribed circle or not
    // - style: Style of polygon (draw and/or fill) (D, F, DF, FD)
    // - line_style: Line style. Array with one of this index
    //   . all: Line style of all lines. Array like for SetLineStyle
    //   . 0..n-1: Line style of each line. Item is 0 (not line) or like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    // - circle_style: Style of circumscribed circle (draw and/or fill) (D, F, DF, FD) (if draw)
    // - circle_line_style: Line style for circumscribed circle. Array like for SetLineStyle (if draw)
    // - circle_fill_color: Fill color for circumscribed circle. Array with components (red, green, blue) (if draw fill circle)
    function StarPolygon($x0, $y0, $r, $nv, $ng, $angle = 0, $circle = false, $style = '', $line_style = null, $fill_color = null, $circle_style = '', $circle_line_style = null, $circle_fill_color = null) {
        if ($nv < 2)
            $nv = 2;
        if ($circle)
            $this->Circle($x0, $y0, $r, 0, 360, $circle_style, $circle_line_style, $circle_fill_color);
        $p2 = null;
        $visited = null;
        for ($i = 0; $i < $nv; $i++) {
            $a = $angle + ($i * 360 / $nv);
            $a_rad = deg2rad((float) $a);
            $p2[] = $x0 + ($r * sin($a_rad));
            $p2[] = $y0 + ($r * cos($a_rad));
            $visited[] = false;
        }
        $p = null;
        $i = 0;
        do {
            $p[] = $p2[$i * 2];
            $p[] = $p2[($i * 2) + 1];
            $visited[$i] = true;
            $i += $ng;
            $i %= $nv;
        } while (!$visited[$i]);
        $this->Polygon($p, $style, $line_style, $fill_color);
    }

    // Draws a rounded rectangle
    // Parameters:
    // - x, y: Top left corner
    // - w, h: Width and height
    // - r: Radius of the rounded corners
    // - round_corner: Draws rounded corner or not. String with a 0 (not rounded i-corner) or 1 (rounded i-corner) in i-position. Positions are, in order and begin to 0: top left, top right, bottom right and bottom left
    // - style: Style of rectangle (draw and/or fill) (D, F, DF, FD)
    // - border_style: Border style of rectangle. Array like for SetLineStyle
    // - fill_color: Fill color. Array with components (red, green, blue)
    function RoundedRect($x, $y, $w, $h, $r, $round_corner = '1111', $style = '', $border_style = null, $fill_color = null) {
        if ('0000' == $round_corner) // Not rounded
            $this->Rect($x, $y, $w, $h, $style, $border_style, $fill_color);
        else { // Rounded
            if (!(false === strpos($style, 'F')) && $fill_color) {
                list($red, $g, $b) = $fill_color;
                $this->SetFillColor($red, $g, $b);
            }
            switch ($style) {
                case 'F':
                    $border_style = null;
                    $op = 'f';
                    break;
                case 'FD': case 'DF':
                    $op = 'B';
                    break;
                default:
                    $op = 'S';
                    break;
            }
            if ($border_style)
                $this->SetLineStyle($border_style);

            $MyArc = 4 / 3 * (sqrt(2) - 1);

            $this->_Point($x + $r, $y);
            $xc = $x + $w - $r;
            $yc = $y + $r;
            $this->_Line($xc, $y);
            if ($round_corner[0])
                $this->_Curve($xc + ($r * $MyArc), $yc - $r, $xc + $r, $yc - ($r * $MyArc), $xc + $r, $yc);
            else
                $this->_Line($x + $w, $y);

            $xc = $x + $w - $r ;
            $yc = $y + $h - $r;
            $this->_Line($x + $w, $yc);

            if ($round_corner[1])
                $this->_Curve($xc + $r, $yc + ($r * $MyArc), $xc + ($r * $MyArc), $yc + $r, $xc, $yc + $r);
            else
                $this->_Line($x + $w, $y + $h);

            $xc = $x + $r;
            $yc = $y + $h - $r;
            $this->_Line($xc, $y + $h);
            if ($round_corner[2])
                $this->_Curve($xc - ($r * $MyArc), $yc + $r, $xc - $r, $yc + ($r * $MyArc), $xc - $r, $yc);
            else
                $this->_Line($x, $y + $h);

            $xc = $x + $r;
            $yc = $y + $r;
            $this->_Line($x, $yc);
            if ($round_corner[3])
                $this->_Curve($xc - $r, $yc - ($r * $MyArc), $xc - ($r * $MyArc), $yc - $r, $xc, $yc - $r);
            else {
                $this->_Line($x, $y);
                $this->_Line($x + $r, $y);
            }
            $this->_out($op);
        }
    }

    /* PRIVATE METHODS */

    // Sets a draw point
    // Parameters:
    // - x, y: Point
    function _Point($x, $y) {
        $this->_out(sprintf('%.2f %.2f m', $x * $this->k, ($this->h - $y) * $this->k));
    }

    // Draws a line from last draw point
    // Parameters:
    // - x, y: End point
    function _Line($x, $y) {
        $this->_out(sprintf('%.2f %.2f l', $x * $this->k, ($this->h - $y) * $this->k));
    }

    // Draws a Bézier curve from last draw point
    // Parameters:
    // - x1, y1: Control point 1
    // - x2, y2: Control point 2
    // - x3, y3: End point
    function _Curve($x1, $y1, $x2, $y2, $x3, $y3) {
        $this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c', $x1 * $this->k, ($this->h - $y1) * $this->k, $x2 * $this->k, ($this->h - $y2) * $this->k, $x3 * $this->k, ($this->h - $y3) * $this->k));
    }		
	
	function Footer()
	{
	    //Go to 1.5 cm from bottom
	    $this->SetY(-15);
		$this->SetFont('Courier','',10);
		$this->MultiCell($nr_largura, $nr_espaco, date('d')." de ".$this->ar_mes[date('m') -1]." de ".date('Y'), 0, "C");		
		
	    $this->SetY(-12);
	    //Arial italic 8
	    $this->SetFont('Courier','I',8);
	    //Page number
	    $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');	
	}
	
	
	
	function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
	{
		if($cw){
			$d = $b;
			$b = $o - $a;
			$a = $o - $d;
		}else{
			$b += $o;
			$a += $o;
		}
		$a = ($a%360)+360;
		$b = ($b%360)+360;
		if ($a > $b)
			$b +=360;
		$b = $b/360*2*M_PI;
		$a = $a/360*2*M_PI;
		$d = $b-$a;
		if ($d == 0 )
			$d =2*M_PI;
		$k = $this->k;
		$hp = $this->h;
		if($style=='F')
			$op='f';
		elseif($style=='FD' or $style=='DF')
			$op='b';
		else
			$op='s';
		if (sin($d/2))
			$MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
		//first put the center
		$this->_out(sprintf('%.2f %.2f m',($xc)*$k,($hp-$yc)*$k));
		//put the first point
		$this->_out(sprintf('%.2f %.2f l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
		//draw the arc
		if ($d < M_PI/2){
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
		}else{
			$b = $a + $d/4;
			$MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
						$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
						$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
						$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
						$xc+$r*cos($b),
						$yc-$r*sin($b)
						);
		}
		//terminate drawing
		$this->_out($op);
	}

	function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
	{
		$h = $this->h;
		$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
			$x1*$this->k,
			($h-$y1)*$this->k,
			$x2*$this->k,
			($h-$y2)*$this->k,
			$x3*$this->k,
			($h-$y3)*$this->k));
	}			
	
	function PieChart($w, $h, $data, $format, $colors=null)
	{
		$this->SetFont('Courier', '', 10);
		$this->SetLegends($data,$format);

		$XPage = $this->GetX();
		$YPage = $this->GetY();
		$margin = 2;
		$hLegend = 5;
		$radius = min($w - $margin * 4 - $hLegend - $this->wLegend, $h - $margin * 2);
		$radius = floor($radius / 2);
		$XDiag = $XPage + $margin + $radius;
		$YDiag = $YPage + $margin + $radius;
		if($colors == null) {
			for($i = 0;$i < $this->NbVal; $i++) {
				$gray = $i * intval(255 / $this->NbVal);
				$colors[$i] = array($gray,$gray,$gray);
			}
		}

		//Sectors
		$this->SetLineWidth(0.2);
		$angleStart = 0;
		$angleEnd = 0;
		$i = 0;
		foreach($data as $val) {
			$angle = floor(($val * 360) / doubleval($this->sum));
			if ($angle != 0) {
				$angleEnd = $angleStart + $angle;
				$this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
				$this->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
				$angleStart += $angle;
			}
			$i++;
		}
		if ($angleEnd != 360) {
			$this->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
		}

		//Legends
		$this->SetFont('Courier', '', 10);
		$x1 = $XPage + 2 * $radius + 4 * $margin;
		$x2 = $x1 + $hLegend + $margin;
		$y1 = $YDiag - $radius + (2 * $radius - $this->NbVal*($hLegend + $margin)) / 2;
		for($i=0; $i<$this->NbVal; $i++) {
			$this->SetFillColor($colors[$i][0],$colors[$i][1],$colors[$i][2]);
			$this->Rect($x1, $y1, $hLegend, $hLegend, 'DF');
			$this->SetXY($x2,$y1);
			$this->Cell(0,$hLegend,$this->legends[$i]);
			$y1+=$hLegend + $margin;
		}
	}

	function BarDiagram($w, $h, $data, $format, $color=null, $maxVal=0, $nbDiv=4)
	{
		$this->SetFont('Courier', '', 10);
		$this->SetLegends($data,$format);

		$XPage = $this->GetX();
		$YPage = $this->GetY();
		$margin = 2;
		$YDiag = $YPage + $margin;
		$hDiag = floor($h - $margin * 2);
		$XDiag = $XPage + $margin * 2 + $this->wLegend;
		$lDiag = floor($w - $margin * 3 - $this->wLegend);
		if($color == null)
			$color=array(155,155,155);
		if ($maxVal == 0) {
			$maxVal = max($data);
		}
		$valIndRepere = ceil($maxVal / $nbDiv);
		$maxVal = $valIndRepere * $nbDiv;
		$lRepere = floor($lDiag / $nbDiv);
		$lDiag = $lRepere * $nbDiv;
		$unit = $lDiag / $maxVal;
		$hBar = floor($hDiag / ($this->NbVal + 1));
		$hDiag = $hBar * ($this->NbVal + 1);
		$eBaton = floor($hBar * 80 / 100);

		$this->SetLineWidth(0.2);
		$this->Rect($XDiag, $YDiag, $lDiag, $hDiag);

		$this->SetFont('Courier', '', 10);
		$this->SetFillColor($color[0],$color[1],$color[2]);
		$i=0;
		foreach($data as $val) {
			//Bar
			$xval = $XDiag;
			$lval = (int)($val * $unit);
			$yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
			$hval = $eBaton;
			$this->Rect($xval, $yval, $lval, $hval, 'DF');
			//Legend
			$this->SetXY(0, $yval);
			$this->Cell($xval - $margin, $hval, $this->legends[$i],0,0,'R');
			$i++;
		}

		//Scales
		for ($i = 0; $i <= $nbDiv; $i++) {
			$xpos = $XDiag + $lRepere * $i;
			$this->Line($xpos, $YDiag, $xpos, $YDiag + $hDiag);
			$val = $i * $valIndRepere;
			$xpos = $XDiag + $lRepere * $i - $this->GetStringWidth($val) / 2;
			$ypos = $YDiag + $hDiag - $margin;
			$this->Text($xpos, $ypos, $val);
		}
	}

	function SetLegends($data, $format)
	{
		$this->legends=array();
		$this->wLegend=0;
		$this->sum=array_sum($data);
		$this->NbVal=count($data);
		foreach($data as $l=>$val)
		{
			$p=sprintf('%.2f',$val/$this->sum*100).'%';
			$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
			$this->legends[]=$legend;
			$this->wLegend=max($this->GetStringWidth($legend),$this->wLegend);
		}
	}		
	
}

// ----------------------

	class eprev_atendimento_protocolo_lista_pdf
    {
        private $db;
        private $filtro;
        private $divisao;
        private $allow_confirm;
        private $allow_cancel;
        private $allow_edit;
        private $allow_view;

        function __construct( $_db, $_divisao )
        {
            $this->db = $_db;

            $this->filtro = new helper_correspondencia_gap__fetch_by_filter();
            $this->requestParams();

            $this->divisao = $_divisao;
        }

        function __destruct()
        {
            $this->db = null;
        }

        function requestParams()
        {
            if (isset($_POST["filtro__cd_atendimento_protocolo_tipo__select"]))
            {
                $this->filtro->setcd_atendimento_protocolo_tipo( $_POST["filtro__cd_atendimento_protocolo_tipo__select"] );
			}
            if (isset($_POST["filtro__cd_atendimento_protocolo_discriminacao__select"]))
            {
                $this->filtro->setcd_atendimento_protocolo_discriminacao( $_POST["filtro__cd_atendimento_protocolo_discriminacao__select"] );
			}
			if (isset($_POST["FiltroEmpresaText"]))
            {
                $this->filtro->setcd_empresa( $_POST["FiltroEmpresaText"] );
			}
            if (isset($_POST["FiltroREText"]))
            {
                $this->filtro->setcd_registro_empregado( $_POST["FiltroREText"] );
			}
            if (isset($_POST["FiltroSeqText"]))
            {
                $this->filtro->setseq_dependencia( $_POST["FiltroSeqText"] );
			}
            if (isset($_POST["FiltroNomeText"]))
            {
                $this->filtro->set_nome( $_POST["FiltroNomeText"] );
			}
            if (isset($_POST["FiltroDataGapText"]))
            {
                $this->filtro->dt_criacao__inicial = $_POST["FiltroDataGapText"];
			}
            if (isset($_POST["FiltroDataGap_final_Text"]))
            {
                $this->filtro->dt_criacao__final= $_POST["FiltroDataGap_final_Text"];
			}
            if (isset($_POST["FiltroHoraGapText"]))
            {
                $this->filtro->hr_criacao__inicial = $_POST["FiltroHoraGapText"];
			}
            if (isset($_POST["FiltroHoraGap_final_Text"]))
            {
                $this->filtro->hr_criacao__final= $_POST["FiltroHoraGap_final_Text"];
			}
        	if (isset($_POST["filtro__cd_usuario_criacao__select"]))
            {
                $this->filtro->setcd_usuario_criacao( $_POST["filtro__cd_usuario_criacao__select"] );
			}
			
            if (isset($_POST["filtro__cd_atendimento__text"]))
            {
                $this->filtro->setcd_atendimento( $_POST["filtro__cd_atendimento__text"] );
			}
            if (isset($_POST["filtro__cd_encaminhamento__text"]))
            {
                $this->filtro->setcd_encaminhamento( $_POST["filtro__cd_encaminhamento__text"] );
			}
        }

        public function loadLista()
        {
            $entity = new entity_projetos_atendimento_protocolo();
            $service = new service_projetos( $this->db );

            $result = $service->correspondenciaGAP_fetchByFilter( $this->filtro );

            $service = null;

            return $result;
        }

        public function texto_filtro__get($v)
        {
        	$f = new helper_correspondencia_gap__fetch_by_filter();
        	$f = $this->filtro;
	        if( $v==1 )
	        {
	        	$output = "{re}{remessa}{recebimento}";
	        	$re = "";
	        	$sep = "";
	        	if( $f->getcd_empresa()!="" )
	        	{
	        		$re = $f->getcd_empresa();
	        		$sep = "/";
	        	}
	        	if( $f->getcd_registro_empregado()!="" )
	        	{
	        		$re .= $sep . $f->getcd_registro_empregado();
	        		$sep = "/";
	        	}
	        	if( $f->getseq_dependencia()!="" )
	        	{
	        		$re .= $sep . $f->getseq_dependencia();
	        		$sep = "/";
	        	}
	        	
	        	if($re!="")
	        	{
		        	$output = str_replace( "{re}", "EMP/RE/SEQ: " . $re . "  ", $output );
	        	}
	        	else
	        	{
	        		$output = str_replace( "{re}", "", $output );
	        	}
	        	
	        	$remessa = "";
	        	$sep = "";
	        	if( $f->dt_criacao__inicial!="" )
	        	{
	        		$remessa = $f->dt_criacao__inicial . ' ' . $f->hr_criacao__inicial;
	        		$sep = " até ";
	        	}
	        	if( $f->dt_criacao__final!="" )
	        	{
	        		$remessa .= $sep . $f->dt_criacao__final . ' ' . $f->hr_criacao__inicial;
	        		$sep = "";
	        	}
	        	
	        	if($remessa!="")
	        	{
		        	$output = str_replace( "{remessa}", "Remessa: " . $remessa . "  ", $output );
	        	}
	        	else
	        	{
	        		$output = str_replace( "{remessa}", "", $output );
	        	}
	        	
	        	$recebimento = "";
	        	if( $f->getdt_recebimento()!="" )
	        	{
	        		$recebimento = "Recebimento: " . $f->getdt_recebimento() . "  ";
	        	}
	        	
	        	$output = str_replace( "{recebimento}", $recebimento, $output );
	        }
	        if($v==2)
	        {
	        	$output = "{remetente}{tipo}{discriminacao}";
	        	
	        	$remetente = "";
	        	if( $f->getcd_usuario_criacao()!="" )
	        	{
	        		$usuarios = usuarios_controledi::select( 
	        			array('codigo'=>$f->getcd_usuario_criacao()) 
        			);

	        		$remetente = "Remetente: " . $usuarios->items[0]->guerra . "  ";
	        	}

	        	$output = str_replace( "{remetente}", $remetente, $output );
	        	
	        	$tipo = "";
	        	if( $f->getcd_atendimento_protocolo_tipo()!="" )
	        	{
	        		$tipos = atendimento_protocolo_tipo::select( 
	        			array('cd_atendimento_protocolo_tipo'=>$f->getcd_atendimento_protocolo_tipo()) 
        			);
	        		$tipo = "Tipo: " . $tipos->items[0]->nome . "  ";
	        	}

	        	$output = str_replace( "{tipo}", $tipo, $output );

	        	$discriminacao = "";
	        	if( $f->getcd_atendimento_protocolo_discriminacao()!="" )
	        	{
	        		$discriminacoes = atendimento_protocolo_discriminacao::select( 
	        			array('cd_atendimento_protocolo_discriminacao'=>$f->getcd_atendimento_protocolo_discriminacao()) 
        			);
	        		$discriminacao = "Discriminação: " . $discriminacoes->items[0]->nome . "  ";
	        	}

	        	$output = str_replace( "{discriminacao}", $discriminacao, $output );
	        }

	        return $output;
        }
    }

// ----------------------

$esta = new eprev_atendimento_protocolo_lista_pdf($db, $D);

// ----------------------

$ob_pdf = new PDF();
$ob_pdf->AliasNbPages();

$ob_pdf->novaPagina();

$ob_pdf->SetY(15);
$ob_pdf->SetFont('Courier','B',22);
$ob_pdf->MultiCell($nr_largura, $nr_espaco, "Protocolo de Correspondências", 0, "C");

$ob_pdf->SetY( $ob_pdf->GetY()+7);
$ob_pdf->SetFont('Courier','B',12);
$ob_pdf->MultiCell($nr_largura, $nr_espaco, $esta->texto_filtro__get(1), 0, "C");

$ob_pdf->SetY( $ob_pdf->GetY()+5);
$ob_pdf->SetFont('Courier','B',12);
$ob_pdf->MultiCell($nr_largura, $nr_espaco, $esta->texto_filtro__get(2), 0, "C");

// Cabeçalho
$ob_pdf->SetXY( 15, $ob_pdf->GetY() + 5 );
$ob_pdf->SetLineWidth( 0 );
$ob_pdf->SetDrawColor( 0, 0, 0 );
$ob_pdf->SetWidths( array(25, 75, 15, 50, 30, 70) );
$ob_pdf->SetAligns( array('L','C','C','C','C','C','C', 'C','C') );
$ob_pdf->SetFont( 'Courier', 'B', 10 );
$ob_pdf->Row( array("Emp/RE/Seq", "Nome/destino", "Tipo", "Discriminação", "Atend/Enc", "Histórico") );		
$ob_pdf->SetFont( 'Courier', '', 10 );

$resultado = $esta->loadLista();
while ( $row = pg_fetch_array($resultado) )
{
	if( $row["cd_registro_empregado"]=="" )
	{
		$re = "";
	}
	else
	{
		$re = $row["cd_empresa"] . "/" . $row["cd_registro_empregado"] . "/" . $row["seq_dependencia"];
	}
	$nome = trim( $row["nome"] );
	if( $row["destino"]!="" )
	{
		if($nome!="")
		{
			$nome .= ' / '; 
		}
		$nome .= $row["destino"];
	}

	$discriminacao = $row["discriminacao_nome"];
	if( $row["identificacao"]!='' )
	{
		$discriminacao .= $row["identificacao"];
	}

	$historico = 'Envio: ' . $row["nome_gap"] . ' ' . $row["dt_criacao"] . '
	';
	if($row["nome_gad"]!=null)
	{
		$historico.= 'Recebimento: ' . $row["nome_gad"] . ' ' . $row["dt_recebimento"] . '
		';
	}
	if($row["dt_recebimento"]!=null)
	{
		$historico.= 'Cancelamento: ' . $row["dt_cancelamento"] . '';
	}
	if($row["cd_atendimento"]!="")
	{
		$atendimento_encaminhamento = $row["cd_atendimento"] . "/" . $row["cd_encaminhamento"];
	}

	$ob_pdf->SetX(15);
	$ob_pdf->Row( array( $re, $nome, $row["tipo_nome"], $discriminacao, $atendimento_encaminhamento, $historico ) );
}

$ob_pdf->Output();
?>