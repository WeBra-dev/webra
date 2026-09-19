<?php

header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD']!=='POST'){
    http_response_code(405);

    echo json_encode([
        "status"=>false,
        "erro"=>"Método não permitido"
    ]);

    exit;
}

require_once("../config.php");

if($id==='' || $token===''){
    echo json_encode([
        "status"=>false,
        "erro"=>"Webhook do Discord não configurado"
    ]);

    exit;
}

$recaptcha=$_POST['recaptcha']??'';

if($recaptcha===''){
    http_response_code(400);

    echo json_encode([
        "status"=>false,
        "erro"=>"Falha na verificação de segurança"
    ]);

    exit;
}

$ch=curl_init("https://www.google.com/recaptcha/api/siteverify");

curl_setopt_array($ch,[
    CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>http_build_query([
        "secret"=>$recaptchaKey,
        "response"=>$recaptcha,
        "remoteip"=>$_SERVER['REMOTE_ADDR']??''
    ]),
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_TIMEOUT=>10
]);

$respostaRecaptcha=curl_exec($ch);

if($respostaRecaptcha===false){

    http_response_code(500);

    echo json_encode([
        "status"=>false,
        "erro"=>"Não foi possível validar a segurança"
    ]);

    exit;
}


$recaptchaData=json_decode($respostaRecaptcha,true);

if(
    !isset($recaptchaData['success']) ||
    $recaptchaData['success']!==true ||
    !isset($recaptchaData['score']) ||
    $recaptchaData['score']<0.5 ||
    !isset($recaptchaData['action']) ||
    $recaptchaData['action']!=='contato'
){
    http_response_code(403);

    echo json_encode([
        "status"=>false,
        "erro"=>"Falha na verificação de segurança"
    ]);

    exit;
}

$nome=trim($_POST['nome']??'');
$email=trim($_POST['email']??'');
$mensagem=trim($_POST['mensagem']??'');

if($nome==='' || $email==='' || $mensagem===''){
    http_response_code(400);

    echo json_encode([
        "status"=>false,
        "erro"=>"Preencha todos os campos"
    ]);

    exit;
}

if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    http_response_code(400);

    echo json_encode([
        "status"=>false,
        "erro"=>"E-mail inválido"
    ]);

    exit;
}

$webhook="https://discord.com/api/webhooks/".$id."/".$token;

$dados=[
    "embeds"=>[
        [
            "title"=>"Novo contato pelo site",
            "color"=>2067271,
            "fields"=>[
                [
                    "name"=>"Nome",
                    "value"=>$nome,
                    "inline"=>false
                ],
                [
                    "name"=>"E-mail",
                    "value"=>$email,
                    "inline"=>false
                ],
                [
                    "name"=>"Mensagem",
                    "value"=>$mensagem,
                    "inline"=>false
                ]
            ]
        ]
    ]
];

$ch=curl_init($webhook);

curl_setopt_array($ch,[
    CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>json_encode($dados),
    CURLOPT_HTTPHEADER=>[
        "Content-Type: application/json"
    ],
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_TIMEOUT=>10
]);

$resposta=curl_exec($ch);

if($resposta===false){

    echo json_encode([
        "status"=>false,
        "erro"=>"Não foi possível enviar a mensagem"
    ]);

    exit;
}

$httpCode=curl_getinfo($ch,CURLINFO_HTTP_CODE);


if($httpCode>=200 && $httpCode<300){
    echo json_encode([
        "status"=>true
    ]);
}else{
    echo json_encode([
        "status"=>false,
        "erro"=>"Não foi possível enviar a mensagem"
    ]);
}