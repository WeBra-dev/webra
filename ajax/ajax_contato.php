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
require_once("../vendor/autoload.php");

use Google\Cloud\RecaptchaEnterprise\V1\Client\RecaptchaEnterpriseServiceClient;
use Google\Cloud\RecaptchaEnterprise\V1\Event;
use Google\Cloud\RecaptchaEnterprise\V1\Assessment;
use Google\Cloud\RecaptchaEnterprise\V1\CreateAssessmentRequest;

$recaptcha=$_POST['recaptcha']??'';

if($recaptcha===''){
    http_response_code(400);

    echo json_encode([
        "status"=>false,
        "erro"=>"Falha na verificacao de seguranca"
    ]);

    exit;
}

$recaptchaKey='6LfkasQtAAAAAJEbHS_yFVNKgnK7gAa4POKpyyah';
$project='recaptcha-webra-1789835756038';

try{

    $client=new RecaptchaEnterpriseServiceClient();

    $projectName=$client->projectName($project);

    $event=(new Event())
        ->setSiteKey($recaptchaKey)
        ->setToken($recaptcha);

    $assessment=(new Assessment())
        ->setEvent($event);

    $request=(new CreateAssessmentRequest())
        ->setParent($projectName)
        ->setAssessment($assessment);

    $response=$client->createAssessment($request);

    if(!$response->getTokenProperties()->getValid()){

        http_response_code(403);

        echo json_encode([
            "status"=>false,
            "erro"=>"Falha na verificacao de seguranca"
        ]);

        $client->close();

        exit;
    }

    if($response->getTokenProperties()->getAction()!=='contato'){

        http_response_code(403);

        echo json_encode([
            "status"=>false,
            "erro"=>"Falha na verificacao de seguranca"
        ]);

        $client->close();

        exit;
    }

    $score=$response->getRiskAnalysis()->getScore();

    if($score<0.5){

        http_response_code(403);

        echo json_encode([
            "status"=>false,
            "erro"=>"Falha na verificacao de seguranca"
        ]);

        $client->close();

        exit;
    }

    $client->close();

}catch(Exception $e){

    http_response_code(500);

    echo json_encode([
        "status"=>false,
        "erro"=>"Nao foi possivel validar a seguranca"
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


    http_response_code(500);

    echo json_encode([
        "status"=>false,
        "erro"=>"Nao foi possivel enviar a mensagem"
    ]);

    exit;
}

$httpCode=curl_getinfo($ch,CURLINFO_HTTP_CODE);


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