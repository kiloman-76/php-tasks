<style>
    body{
        background-color: gray;
    }
</style>
<?php
class DiagrammBuilder
{
    private $input = '';
    private $output = '';
    private $proportion = '';
    private $img = '';
    private $cveta = array();
    private $darkcveta = array();
    private $namesArr = array();

    private $width = 300;
    private $height = 500;




    public function __construct($input='input.txt', $output = 'putput.png')
    {
        $this->input = $input;
        $this->output = $output;
        $this->prepareData();
        $this->prepareCanvas();
        $this->drawDiagramm();
        $this->drawLegend();

        imagepng($this->img, 'pirog.png');
        print '<h4>Круговая диаграмма</h4><img style="margin: 0px 0px" src="pirog.png" />';
    }

    /**
     * @return int
     */

    public function prepareData(){
        $handle = fopen($this->input, 'r');
        if($handle) {
            $namesArr = array();
            $proportion = array();

            while (($buffer = fgets($handle, 4096)) !== false) {
                $string = explode(';',$buffer);
                $namesArr[] = $string[0];
                $proportion[] = (int)$string[1];
            }
        }


        $sum = array_sum($proportion);
        foreach ($proportion as $key=>$value){
            $proportion[$key] = round($value/$sum *100);
        }
        $this->proportion = $proportion;
        $this->namesArr = $namesArr;

    }

    public function prepareCanvas(){
        $w = $this->width;
        $h = $this->height;
        $img = imagecreatetruecolor($w, $h);


        
        $this->cveta[0] = imagecolorallocate($img, 255, 203, 3);
        $this->cveta[1] = imagecolorallocate($img, 220, 101, 29);
        $this->cveta[2] = imagecolorallocate($img, 189, 24, 51);
        $this->cveta[3] = imagecolorallocate($img, 214, 0, 127);
        $this->cveta[4] = imagecolorallocate($img, 98, 1, 96);
        $this->cveta[5] = imagecolorallocate($img, 0, 62, 136);
        $this->cveta[6] = imagecolorallocate($img, 0, 102, 179);
        $this->cveta[7] = imagecolorallocate($img, 0, 145, 195);
        $this->cveta[8] = imagecolorallocate($img, 0, 115, 106);
        $this->cveta[9] = imagecolorallocate($img, 178, 210, 52);
        $this->cveta[10] = imagecolorallocate($img, 137, 91, 74);
        $this->cveta[11] = imagecolorallocate($img, 82, 56, 47);

        $this->darkcveta[0] = imagecolorallocate($img, 205, 153, 0);
        $this->darkcveta[1] = imagecolorallocate($img, 170, 51, 0);
        $this->darkcveta[2] = imagecolorallocate($img, 139, 0, 1);
        $this->darkcveta[3] = imagecolorallocate($img, 164, 0, 77);
        $this->darkcveta[4] = imagecolorallocate($img, 48, 0, 46);
        $this->darkcveta[5] = imagecolorallocate($img, 0, 12, 86);
        $this->darkcveta[6] = imagecolorallocate($img, 0, 52, 129);
        $this->darkcveta[7] = imagecolorallocate($img, 0, 95, 145);
        $this->darkcveta[8] = imagecolorallocate($img, 0, 65, 56);
        $this->darkcveta[9] = imagecolorallocate($img, 128, 160, 2);
        $this->darkcveta[10] = imagecolorallocate($img, 87, 41, 24);
        $this->darkcveta[11] = imagecolorallocate($img, 32, 6, 0);

        imagesavealpha($img, true);    // альфа-канал для прозрачности
        imagefill($img, 0, 0, IMG_COLOR_TRANSPARENT);
        $this->img = $img;

    }

    /**
     * @return int
     */
    public function drawDiagramm()
    {
        $w = $this->width;
        $h = $this->height;
        var_dump($this->cveta);
        for ($i = 0; $i < 50; $i++){
            $j=0;
            foreach ($this->proportion as $v) {
                imagefilledarc($this->img, $w/2, $h/2+50-$i, $w-10, $h-$h/2, $de, $de += round($v/100 * 360), $this->darkcveta[$j], IMG_ARC_PIE);
                $ds = $de;
                $j++;
            }
        }
        $k=0;

        foreach ($this->proportion as $i) {
            imagefilledarc($this->img, $w/2, $h/2, $w-10, $h-$h/2, $de, $de += round($i/100 * 360), $this->cveta[$k], IMG_ARC_PIE);
            $ds = $de;
            $k++;
        }
    }

    public function drawLegend(){
        $black=ImageColorAllocate($this->img,0,0,0);

        $w = $this->width;
        $h = $this->height;
        $legend_count=count($this->namesArr);
        $max_length=0;
        foreach($this->namesArr as $v){
            if ($max_length<strlen($v)) $max_length=strlen($v);
        };
        $FONT=2;
        $font_w=ImageFontWidth($FONT);
        $font_h=ImageFontHeight($FONT);
        $l_width=($font_w*$max_length)+$font_h+10+5+10;
        $l_height=$font_h*$legend_count+10+10;
        $l_x1=$w-70-$l_width;
        $l_y1=($h-$l_height);
        ImageRectangle($this->img, $l_x1, $l_y1, $l_x1+$l_width, $l_y1+$l_height, $black);
        $text_x=$l_x1+10+5+$font_h;
        $square_x=$l_x1+10;
        $y=$l_y1+10;

        $i=0;
        foreach($this->namesArr as $v) {
            $dy=$y+($i*$font_h);
            ImageString($this->img, $FONT, $text_x, $dy, $v, $black);
            ImageFilledRectangle($this->img,
                $square_x+1,$dy+1,$square_x+$font_h-1,$dy+$font_h-1,
               $this->cveta[$i]);
            ImageRectangle($this->img,
                $square_x+1,$dy+1,$square_x+$font_h-1,$dy+$font_h-1,
                $black);
            $i++;
        }
    }
}

new DiagrammBuilder()

?>