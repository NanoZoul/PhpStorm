<?php
// Routes
$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

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

$app->post('/reaccion/like',function($request, $response, $args){
    $id = $request->params('id');
    $pic = Pic::find($id)->increment('positive');
    $pic->save();
});

$app->post('/reaccion/warn',function($request, $response, $args){
    $id = $request->params('id');
    $pic = Pic::find($id)->increment('warning');
    $pic->save();
});

$app->post('/reaccion/dislike',function($request, $response, $args){
    $id = $request->params('id');
    $pic = Pic::find($id)->increment('negative');
    $pic->save();
});

$app->post('/pic/enviar',function($request, $response, $args){
    //Decodificar imagen
    $imagen = $request->getParam('imagen');
    $img = base64_decode($imagen);
    //Crear archivo de imagen
    $date = new DateTime();
    $archivo = $request->getParam('deviceId').$date->getTimestamp();
    $path = 'imagenes/'.$archivo.".png";
    $file = fopen($path,'wb');
    fwrite($file, $img);
    fclose($file);
    //Ingreso de pic a la base de datos
    $pic = new Pic();
    $pic -> deviceId = $request->getParam('deviceId');
    $pic -> fecha = date('d-m-Y H:i:s');
    $pic -> url = $path;
    $pic -> latitude = $request->getParam('latitude');
    $pic -> longitude = $request->getParam('longitude');
    $pic -> positive = $request->getParam('positive');
    $pic -> negative = $request->getParam('negative');
    $pic -> warning = $request->getParam('warning');
    $pic -> imagen = $request->getParam('imagen');
    $pic -> save();

    //SELECCION DE ID ULTIMO PIC
    $ultimoPic = Pic::select("id")
        ->orderBy("id","desc")
        ->limit("1")->first();

    //Guardar nuevo id en el pic creado
    $pic -> idRemota = $ultimoPic->id;
    $pic -> save();
    //SELECCIONAR PIC SEGUN LOS MENOS ENVIADOS
    $pic2 = Pic::where('deviceId','!=',$pic->deviceId)
        ->inRandomOrder()
        ->first();

    $pic2-> idRemota = $pic2->id;

    //Armar el twin entre ambos pic
    $twin = new Twin();
    $twin->local = $pic;
    $twin->remote = $pic2;
    $twin->idUsuario = $ultimoPic->id;
    $twin->idPareja = $pic2->id;
    $twin->save();

    //Devolver el twin generado
    return $response->withJson($twin);
});
