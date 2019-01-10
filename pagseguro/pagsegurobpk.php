<?php
header('Access-Control-Allow-Origin: *');
include_once ("../conexao.php");

$cliente = $_POST['clt'];
$item = $_POST['id'];

$conn->query("insert into tbl_historico_compras (cliente, item, api) values ('$cliente', '$item', 'pagseguro')");

if($conn->insert_id) {


    require_once "../../vendor/autoload.php";

    \PagSeguro\Library::initialize();
    \PagSeguro\Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
    \PagSeguro\Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");


    $payment = new \PagSeguro\Domains\Requests\Payment();

    $payment->addItems()->withParameters(
        $_POST['id'],
        $_POST['desc'],
        1,
        $_POST['val']
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

        echo $result->getCode();
    } catch (Exception $e) {
        die($e->getMessage());
    }
}else{
    echo 'error';
}
