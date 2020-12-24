<? // init.php
\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    "main",
    "OnAfterResizeImage",
    array(
        "Handlers",
        "checkBadPictures"
    )
);

class Handlers{
    public static function checkBadPictures(
        $file,
        $options,
        &$callbackData,
        &$cacheImageFile,
        &$cacheImageFileTmp,
        &$arImageSize
    ) {
        if (file_exists($cacheImageFileTmp)) {
            if (stripos($cacheImageFileTmp, 'png') !== false) {
                $maxColor = 251;
                $oldImg = imagecreatefrompng($cacheImageFileTmp);
                $sizes = getimagesize($cacheImageFileTmp);
                $newImg = imagecreatetruecolor($sizes[0], $sizes[1]);
                imagealphablending($newImg, false);
                imagesavealpha($newImg, true);
                imagecopyresampled($newImg, $oldImg, 0, 0, 0, 0, $sizes[0], $sizes[1], $sizes[0], $sizes[1]);
                //$colorWhite = imagecolorallocate($newImg, 255, 255, 255);
                $colorWhite = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
                for ($y = 0; $y < ($sizes[1]); ++$y) {
                    for ($x = 0; $x < ($sizes[0]); ++$x) {
                        $colorat = imagecolorat($newImg, $x, $y);
                        $colorInfo = imagecolorsforindex($newImg, $colorat);
                        $r = $colorInfo['red'];
                        $g = $colorInfo['green'];
                        $b = $colorInfo['blue'];
                        $alpha = $colorInfo['alpha'];
                        $isTransparent = false;
                        if ($r == 0 && $g == 0 && $b == 0 && $alpha == 125) {
                            $isTransparent = true;
                        }
                        if (($r >= $maxColor && $g >= $maxColor && $b >= $maxColor) || ($isTransparent)) {
                            imagesetpixel($newImg, $x, $y, $colorWhite);
                        }
                    }
                }
                imagepng($newImg, $cacheImageFileTmp, 0);
            }
        }
    }
}
