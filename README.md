# ZFImage

Es una librería para la manipulación de imágenes en Zend Framework 1.10+ 

## Cambios

Se agrega nuevo limite de memoria para la manipulación de imagenes
se extendio hasta 256Mb, ahora la imagen más grande que se puede manipular
es de 11Mb o 7200px x 7200px

## Uso

### Rezise

```php
$image = new ZFImage_Image("images/desert.jpg");
$image->addPlugin(new ZFImage_Fx_Resize(220));
$image->imageJpeg()
```

### Crop

```php
$image = new ZFImage_Image('images/desert.jpg');
$image->addPlugin(new ZFImage_Fx_Crop(100));
$image->imageJpeg();
```

### Watermark

```php
$image = new ZFImage_Image("images/desert.jpg");
$watermark = new ZFImage_Image("images/watermark.png");
$image->addPlugin(new ZFImage_Fx_Resize(400));
$image->addPlugin(new ZFImage_Draw_WaterMark($watermark));
$image->imageJpeg();
```

### Crop top

```php
$image = new ZFImage_Image('images/desert.jpg');
$image->addPlugin(new ZFImage_Fx_Crop(400, 300, "top"));
$image->imageJpeg();
```
