<?php

header("Content-Type: application/json");

if($_SERVER['REQUEST_METHOD']!=='POST'){
    http_response_code(405);

    echo json_encode([
        "status"=>false,
        "erro"=>"Metodo nao permitido"
    ]);

    exit;
}

require_once("../config.php");

$recaptcha=$_POST['recaptcha']??'';

if($recaptcha===''){
    http_response_code(400);

    echo json_encode([
        "status"=>false,
        "erro"=>"Falha na verificacao de seguranca"
    ]);

    exit;
}

$recaptchaSiteKey='6LfkasQtAAAAAJEbHS_yFVNKgnK7gAa4POKpyyah';
$project='recaptcha-webra-1789835756038';

$url="https://recaptchaenterprise.googleapis.com/v1/projects/".$project."/assessments?key=".$recaptchaApiKey;

$dadosRecaptcha=[
    "event"=>[
        "token"=>$recaptcha,
        "siteKey"=>$recaptchaSiteKey,
        "userIpAddress"=>$_SERVER['REMOTE_ADDR']??'',
        "userAgent"=>$_SERVER['HTTP_USER_AGENT']??''
    ]
];

$ch=curl_init($url);

curl_setopt_array($ch,[
    CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>json_encode($dadosRecaptcha),
    CURLOPT_HTTPHEADER=>[
        "Content-Type: application/json"
    ],
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_TIMEOUT=>10
]);

$respostaRecaptcha=curl_exec($ch);

if($respostaRecaptcha===false){

    curl_close($ch);

    http_response_code(500);

    echo json_encode([
        "status"=>false,
        "erro"=>"Nao foi possivel validar a seguranca"
    ]);

    exit;
}

$httpCode=curl_getinfo($ch,CURLINFO_HTTP_CODE);

curl_close($ch);

$recaptchaData=json_decode($respostaRecaptcha,true);

if($httpCode<200 || $httpCode>=300){

    http_response_code(500);

    echo json_encode([
        "status"=>false,
        "erro"=>"Nao foi possivel validar a seguranca"
    ]);

    exit;
}

if(
    !isset($recaptchaData['tokenProperties']) ||
    !isset($recaptchaData['tokenProperties']['valid']) ||
    $recaptchaData['tokenProperties']['valid']!==true
){

    http_response_code(403);

    echo json_encode([
        "status"=>false,
        "erro"=>"Falha na verificacao de seguranca"
    ]);

    exit;
}

$action=$recaptchaData['tokenProperties']['action']??'';

if($action!=='contato'){

    http_response_code(403);

    echo json_encode([
        "status"=>false,
        "erro"=>"Falha na verificacao de seguranca"
    ]);

    exit;
}

$score=$recaptchaData['riskAnalysis']['score']??0;

if($score<0.5){

    http_response_code(403);

    echo json_encode([
        "status"=>false,
        "erro"=>"Falha na verificacao de seguranca"
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
        "erro"=>"E-mail invalido"
    ]);

    exit;
}

if(!isset($id) || !isset($token) || $id==='' || $token===''){

    http_response_code(500);

    echo json_encode([
        "status"=>false,
        "erro"=>"Webhook do Discord nao configurado"
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

    curl_close($ch);

    http_response_code(500);

    echo json_encode([
        "status"=>false,
        "erro"=>"Nao foi possivel enviar a mensagem"
    ]);

    exit;
}

$httpCode=curl_getinfo($ch,CURLINFO_HTTP_CODE);

curl_close($ch);

if($httpCode>=200 && $httpCode<300){

    echo json_encode([
        "status"=>true
    ]);

}else{

    http_response_code(500);

    echo json_encode([
        "status"=>false,
        "erro"=>"Nao foi possivel enviar a mensagem"
    ]);
}
?>