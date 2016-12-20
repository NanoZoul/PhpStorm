<?php
// Routes
$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});


class Order extends Illuminate\Database\Eloquent\Model {

    protected $fillable = ['title'];
    public $timestamps = false;
}

class Pic extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
}

class Twin extends Illuminate\Database\Eloquent\Model {
    public $timestamps = false;
}

$app->get('/usuarios/pic',function($request, $response, $args){

    $random = mt_rand(1, 10);
    $pic = Pic::select()->where('id',$random)->first();
    //$pic = Pic::first();
    return $response->withJson($pic);
});


$app->post('/pic/enviar',function($request, $response, $args){
    //Decodificar imagen
    $imagen = $request->getParam('imagen');
    $img = base64_decode($imagen);
    //Crear archivo de imagen
    $archivo = $request->getParam('deviceId').$request->getParam('fecha');
    $path = 'imagenes/'.$archivo.".png";
    $file = fopen($path,'wb');
    fwrite($file, $img);
    fclose($file);

    //Ingreso de pic a la base de datos
    $pic = new Pic();
    $pic -> deviceId = $request->getParam('deviceId');
    $pic -> fecha = $request->getParam('fecha');
    $pic -> url = $path;
    $pic -> latitude = $request->getParam('latitude');
    $pic -> longitude = $request->getParam('longitude');
    $pic -> positive = $request->getParam('positive');
    $pic -> negative = $request->getParam('negative');
    $pic -> warning = $request->getParam('warning');
    $pic -> imagen = $request->getParam('imagen');
    $pic -> save();


    //Seleccionar un pic al azar
    $random = mt_rand(1, 10);
    $pic2 = Pic::first();
    //Armar el twin entre ambos pic
    $twin = new Twin();
    $twin->local = $pic;
    $twin->remote = $pic2;
    $twin->save();

    //Devolver el twin generado
    return $response->withJson($twin);

    //RECIBIMOS TODA LA INFORMACION DE LA APP
    //CAMBIAMOS DECODIFICAMOS LA FOTO Y LA GUARDAMOS EN EL DIRECTORIO

    /*
    $pic = new Pic();
    $pic -> deviceid = "12345";
    $pic -> url = "foto3.jpg";
    $pic -> save();

    $randomPic = Pic::inRandomOrder()
        ->first();


    $twin = new Twin();
    $twin -> local = $pic;
    $twin -> remote = $randomPic;
    $twin -> save();

    $data = array(
        'id'=>'116998',
        'deviceId'=>$response->deviceId,
        'date'=>'2',
        'latitude'=>'100',
        'longitude'=>'200'
    );
    return $response->withJson($twin);
    */
});
