<?php


namespace App\Services\PictureGenerator;


class RandomPicGenerator
{

    private $width = 300;
    private $height = 300;
    private $image;
    /**
     * @var false|resource
     */
    private $image_mini;

    public function generateRandomPic () {

        $this->image = imagecreate($this->width, $this->height);

       imagecolorallocate($this->image, rand(0, 255), rand(0, 255), rand(0, 255));

        $noir = imagecolorallocate($this->image, 57, 57, 57);
        imagesetthickness     (  $this->image    ,  rand(1, 20)    );

    // Couleur de remplissage de l'ellipse
        $col_ellipse = imagecolorallocate($this->image, 255, rand(200, 255), rand(200, 255));
        $eyesColor = imagecolorallocate($this->image, rand(0, 255), rand(0, 255), rand(0, 255));

    // Left eye
        imagefilledellipse($this->image, 100, 100, 80, rand(10, 80), $col_ellipse);
        imagefilledellipse($this->image, rand(80, 120), 100, 10, 10, $eyesColor);

    // Right eye
        imagefilledellipse($this->image, 200, 100, 80, rand(10, 80), $col_ellipse);
        imagefilledellipse($this->image, rand(180, 220), 100, 10, 10, $eyesColor);

    // Mouth
        imagefilledellipse($this->image, 150, 230, 130, rand(10, 80), imagecolorallocate($this->image, rand(0, 255), rand(0, 255), rand(0, 255)));

    // Nose
        imagefilledellipse($this->image, 150, 180, 50, rand(10, 70), imagecolorallocate($this->image, rand(0, 255), rand(0, 255), rand(0, 255)));

    // Left Eye Brows
        imageline ($this->image, 60, rand(20, 100), 130, 60, $noir);

    // Left Eye Brows
        imageline($this->image, 160, 59, 230, rand(20, 100), $noir);

    // Render image

        return $this;
    }

    public function savePic ($path){

        if (!empty($this->image)) {
            imagepng($this->image, $path);
        }

        if (!empty($this->image_mini)) {
            imagepng($this->image_mini, str_replace('.png', '-mini.png',$path));
        }
    }

    public function makeMiniature ($newWidth, $newHeight) {

        if (!empty($this->image)) {
            // Resample
            $this->image_mini = imagecreatetruecolor($newWidth, $newHeight);
            $width = imagesx($this->image);
            $height = imagesy($this->image);
            imagecopyresampled($this->image_mini, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        }

        return $this;

    }

    public function makeRounded () {

    }

}