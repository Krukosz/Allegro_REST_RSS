<?php
   //header('Content-Type: image/jpeg');
   require('src/SimpleImage.php');
   function imageResize($imageAddress, $imageSize) {
       $image = new SimpleImage();
       $image->load($imageAddress);
       list($imageWidth, $imageHeight) = getimagesize($imageAddress);
       if ($imageWidth >= $imageHeight) {
           $image->resizeToWidth($imageSize);
       }
       else {
           $image->resizeToHeight($imageSize);
       }
       
       return $image->output();
   }
   if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['url'])) {
       imageResize($_GET['url'], 128);
   }
?>