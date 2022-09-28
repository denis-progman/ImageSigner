<?php
namespace DenisPm\ImageSigner;

use GdImage;
use DenisPm\ImageSigner\constants\Colors;

class ImageBlank extends Colors
{
    private const WORD_GAP = " ";
    private GdImage|false $blank;

    private int|false $xSize;
    private int|false $ySize;

    /**
     * @return bool|GdImage
     */
    public function getBlank(): GdImage|false
    {
        return $this->blank;
    }

    /**
     * @return false|int
     */
    public function getXSize(): false|int
    {
        return $this->xSize;
    }

    /**
     * @return false|int
     */
    public function getYSize(): false|int
    {
        return $this->ySize;
    }

    public function __construct(string $file) {
        $startBlank = imagecreatefromjpeg($file);
        $this->xSize = imagesx($startBlank);
        $this->ySize = imagesy($startBlank);
        $this->blank = imagecreatetruecolor($this->xSize, $this->ySize);
        imagecopy($this->blank, $startBlank, 0, 0, 0, 0, $this->xSize, $this->ySize);
        return $this;
    }

    public static function getHeightText(string $text, int $fontSize, string $font, int $angle): int {
        $textBox = imageftbbox($fontSize, $angle, $font, $text);
        return abs($textBox[3]) + abs($textBox[5]);
    }

    public function addString(
        string $string,
        int $fontSize,
        int $xPosition,
        int $yPosition,
        string $font,
        int $angle = 0,
        array $color = self::WHITE,
        int $opacity = 0,
        bool $horizontal = true,
        bool $vertical = true
    ): self
    {
        $color[3] = $opacity ? round(1.27 * $opacity) : self::DEFAULT_ALFA;
        $color = imagecolorallocatealpha($this->blank, ...$color);
        $imageBounding = imagettfbbox($fontSize, $angle, $font, $string);
        $centeredX = $centeredY = 0;
        if ($horizontal) {
            $centeredX = round($imageBounding[2] / 2);
        }
        if ($vertical) {
            $centeredY = -$imageBounding[3];
        }
        imagettftext($this->blank, $fontSize, $angle, $xPosition - $centeredX, $yPosition - $centeredY, $color, $font, $string);
        return $this;
    }

    public function addStringBlock(
        string|array $stringBlock,
        int $fontSize,
        int $xPosition,
        int $yPosition,
        string $font,
        int $width,
        bool $historical = true,
        int $angle = 0,
        int $opacity = 0,
        array $color = self::WHITE,
        float $gap = 1.5
    ): self
    {
        $allHeight = 0;
        $fulStrings = [];
        if (is_string($stringBlock)) {
            $stringBlock = explode("\n", $stringBlock);
        }
        foreach ($stringBlock as $oneString){
            $oneString = trim($oneString);
            if(imagettfbbox($fontSize, $angle, $font, $oneString)[2] > $width) {
                $words = explode(self::WORD_GAP, $oneString);
                $oneString = '';
                foreach ($words as $oneWorld) {
                    if (!$oneWorld = trim($oneWorld)) continue;
                    if(imagettfbbox($fontSize, $angle, $font, $oneString . $oneWorld)[2] > $width) {
                        $fulStrings[] = trim($oneString);
                        $oneString = $oneWorld;
                    } else {
                        $oneString .= self::WORD_GAP . $oneWorld;
                    }
                }
            }
            $fulStrings[]= trim($oneString);
            $allHeight += self::getHeightText($oneString, $fontSize, $font, $angle);
        }

        $stringCount = count($fulStrings);
        $yPosition = $yPosition - ($allHeight + ($stringCount - 1) * $allHeight / $stringCount * $gap) / 2;
        foreach ($fulStrings as $oneFulString){
            $this->addString($oneFulString, $fontSize, $xPosition, round($yPosition), $font, $angle, $color, $opacity, $historical);
            $thisStringHeight = self::getHeightText($oneFulString, $fontSize, $font, $angle);
            $yPosition += $thisStringHeight * $gap * 2;
        }
        return $this;
    }

    public function addColumnsStringBlock(
        string|array $stringBlock,
        int $columnAmount,
        int $xPosition,
        int $yPosition,
        string $font,
        int $columnWidth,
        int $fontSize,
        bool $historical,
        array $color,
        int $opacity,
        float $stringGap,
        int $columnGap = 1,
        string $preMarker = "• ",
    ): self {
        $columns = [];
        if (is_string($stringBlock)) {
            $stringBlock = explode("\n", $stringBlock);
        }
        $commonCounter = $columnCounter = 0;
        $stringCount = ceil(count($stringBlock) / $columnAmount);
        foreach ($stringBlock as $string) {
            if (++$commonCounter > $stringCount) {
                $commonCounter = 0;
                $columnCounter++;
            }
            $columns[$columnCounter][] = "{$preMarker}{$string}\n";
        }
        $xPosition = $xPosition - ceil(($columnAmount * $columnWidth + ($columnAmount - 1) * $columnGap) / 2);
        foreach ($columns as $column) {
            $this->addStringBlock(
                $column, $fontSize, $xPosition, $yPosition, $font, $columnWidth, $historical,0, $opacity, $color, $stringGap
            );
            $xPosition += $columnWidth + $columnGap;
        }
        return $this;
    }

    public function show(int $quality = 100, bool $header = true): void {
        if ($header) {
            header("Content-type: image/jpeg");
        }
        imagejpeg($this->blank, NULL, $quality);
        imagedestroy($this->blank);
    }

    public function getBase64(int $quality = 100): string {
        ob_start();
        $this->show($quality, false);
        return base64_encode(ob_get_clean());
    }
}
