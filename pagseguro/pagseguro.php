<?php
header('Access-Control-Allow-Origin: *');
include_once ("../conexao.php");

if(isset($_POST['id'])){
    $cliente = $_POST['clt'];
    $item = $_POST['id'];
    $montante = $_POST['val'];
    $descricao = $_POST['desc'];
}else{
    $json = file_get_contents('php://input');
    $obj = json_decode($json);
    $cliente = $obj->clt;
    $item = $obj->id;
    $montante = $obj->val;
    $descricao = $obj->desc;
}

$conn->query("insert into tbl_historico_compras (cliente, item, api) values ('$cliente', '$item', 'pagseguro')");

if($conn->insert_id) {


    require_once "../../vendor/autoload.php";

    \PagSeguro\Library::initialize();
    \PagSeguro\Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
    \PagSeguro\Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");


    $payment = new \PagSeguro\Domains\Requests\Payment();

    $payment->addItems()->withParameters(
        $item,
        $descricao,
        1,
        $montante
    );


    $payment->setCurrency("BRL");
    $payment->setReference($conn->insert_id);


// Set your customer information.
//$payment->setSender()->setName('João Comprador');
    $payment->setRedirectUrl("http://www.meusite.com.br");
    $payment->setNotificationUrl("http://www.meusite.com.br/functions/pagseguro/transactionListener.php");

    try {
        $onlyCheckoutCode = true;
        $result = $payment->register(
            \PagSeguro\Configuration\Configure::getAccountCredentials(),
            $onlyCheckoutCode
        );
        if(isset($_POST['id'])){
            echo $result->getCode();
        }else{
            echo json_encode($result->getCode());
        }


    } catch (Exception $e) {
        die($e->getMessage());
    }
}else{

    if(isset($_POST['id'])){
        echo 'error';
    }else{
        echo json_encode('error');
    }
}
