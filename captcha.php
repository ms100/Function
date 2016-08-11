<?php
/**
 * 生成验证码
 * @param $width 宽度
 * @param $height 高度
 * @param $char_num 字符数量
 * @return string
 */
function captcha($width, $height, $char_num){
    if(!file_exists($font = './arialbd.ttf')){
        exit;
    }

    $margin_x = $width / 10;
    $margin_y = $height / 10;

    $char_size = min(($width - $margin_x) / $char_num, $height - $margin_y);

    $chars = '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

    $img = imagecreatetruecolor($width, $height);
    imagefill($img, 0, 0, imagecolorallocate($img, 250, 250, 250));

    $darkColor = function () use ($img){
        return imagecolorallocatealpha($img, mt_rand(20, 80), mt_rand(20, 80), mt_rand(20, 80), 40);
    };
    $lightColor = function () use ($img){
        return imagecolorallocatealpha($img, mt_rand(130, 190), mt_rand(130, 190), mt_rand(130, 190), 60);
    };
    $backColor = function () use ($img){
        return imagecolorallocatealpha($img, mt_rand(160, 220), mt_rand(160, 220), mt_rand(160, 220), 60);
    };

    //干扰矩形和椭圆
    for($i = 0; $i < 3; $i++){
        $h = mt_rand(0, 1);
        if($h == 0){
            $lx = mt_rand($width / 3 * $i - 10, $width / 3 * ($i + 1) + 10);
            $ly = mt_rand($height / 3 * $i - 10, $height / 3 * ($i + 1) + 10);
            $rx = mt_rand($width / 2, $width);
            $ry = mt_rand($height / 2, $height);
            imagefilledrectangle($img, $lx, $ly, $lx + $rx, $ly + $ry, $backColor());
        }elseif($h == 1){
            $lx = $width / 2 * $i;
            $ly = $height / 2 * $i;
            $rx = $width / 2 * ($i + 1);
            $ry = $height / 2 * ($i + 1);
            imagefilledellipse($img, mt_rand($lx, $rx), mt_rand($ly, $ry), mt_rand($width / 2, $width), mt_rand($height / 2, $height), $backColor());
        }
    }

    //干扰线
    for($i = 0; $i <= 15; $i++){
        imagearc($img, rand(-$width, 2 * $width), rand(-$height, 2 * $height), rand($width, 3 * $width), rand($height, 3 * $height), mt_rand(0, 360), mt_rand(0, 360), $lightColor());
    }

    //$max_i = mt_rand(15,30);
    //干扰文字
    for($i = 0; $i < 15; $i++){
        //imagechar($img, mt_rand(4, 5), mt_rand(0,$width), mt_rand(0,$height)-5, $str{mt_rand(0, strlen($str) - 1)}, $lightColor($img));
        $tmp_size = mt_rand($char_size - 8, $char_size - 6);
        imagettftext($img, $tmp_size, mt_rand(-20, 20), mt_rand(5, $width - $tmp_size), mt_rand($tmp_size / 2, $height + $tmp_size / 2), $lightColor(), $font, $chars{mt_rand(0, strlen($chars) - 1)});
    }

    $w = ($width - $margin_x) / $char_num;

    $move = ($w - $char_size) / 2;

    $captcha = '';
    for($i = 0; $i < $char_num; $i++){
        $captcha .= $chars{mt_rand(0, strlen($chars) - 1)};
        $x = $i * $w + $move + $margin_x / 2;

        $size = mt_rand($char_size - 4, $char_size);
        $y = ($height + $size) / 2 - 2;

        imagettftext($img, $size, mt_rand(-20, 20), $x, $y, $darkColor(), $font, $captcha{$i});
    }

    header("content-type:image/png");
    imagepng($img);
    imagedestroy($img);

    return strtolower($captcha);
}