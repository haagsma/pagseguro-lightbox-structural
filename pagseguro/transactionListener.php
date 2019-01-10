<?php
header("access-control-allow-origin: *");
include_once ("../conexao.php");
require_once "../../vendor/autoload.php";

\PagSeguro\Library::initialize();
\PagSeguro\Library::cmsVersion()->setName("Nome")->setRelease("1.0.0");
\PagSeguro\Library::moduleVersion()->setName("Nome")->setRelease("1.0.0");

try {
    if (\PagSeguro\Helpers\Xhr::hasPost()) {
        $response = \PagSeguro\Services\Transactions\Notification::check(
            \PagSeguro\Configuration\Configure::getAccountCredentials()
        );
    } else {
        throw new \InvalidArgumentException($_POST);
    }
    if($response->getStatus() == 3){
        $Carga = $response->getGrossAmount();
        $referencia = $response->getReference();
        $valQuery = $conn->query("select u.saldo, c.cliente from tbl_cadastro_usuario as u left join tbl_historico_compras as c on u.id = c.cliente where c.id = $referencia")->fetch_assoc();
        $usuario = $valQuery['cliente'];
        $saldo = str_replace(',','.', $valQuery['saldo']);
        $totalCredito = str_replace('.',',', ($Carga+$saldo));
        $conn->query("update tbl_cadastro_usuario set saldo = '$totalCredito' where id = $usuario");
        $carga2 = str_replace('.',',', $Carga);
        $conn->query("insert into tbl_pagamentos (valor, cliente, tipo) values ('$carga2', '$usuario', 'PagSeguro')");
    }

} catch (Exception $e) {
    die($e->getMessage());
}
