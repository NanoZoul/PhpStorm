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


$app->post('/pic/enviar',function($picRecibido, $response){
    //$pic = new Pic();
    //$pic->deviceid = $request->deviceId;
    //$pic->longitude = "3";
    //$pic->latitude = $args->latitude;
    //$pic->save();

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
    $picLocal = new Pic();
    $picLocal-> deviceId = $picRecibido->deviceId;
    $picLocal-> url = "foto1.jpg";
    $picLocal-> save();

    $randomPic = Pic::inRandomOrder()
        ->first();

    $twin = new Twin();
    $twin-> local = $picLocal;
    $twin-> remote = $randomPic;
    $twin-> save();

    return $response->withJson($twin);
});
