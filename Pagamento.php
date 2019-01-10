<?php
/**
 * Created by PhpStorm.
 * User: Haagsma
 * Date: 03/09/2018
 * Time: 10:35
 */

?>
<!-- Header -->
<?php
require_once ("layout/header.php");
$pacotes = $conn->query("select * from tbl_pacotes");
?>
<!-- End-Header -->


<!-- Content -->
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="content-page">
    <!-- Start content -->
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Comprar Crédito</h4>
                        <ol class="breadcrumb p-0 m-0">
                            <li>
                                <a href="./">Home</a>
                            </li>
                            <li class="active">
                                Comprar Crédito
                            </li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <!-- end row -->

            <div class="row">
                <div class="col-lg-9 center-page">
                    <div class="text-center">
                        <h3 class="m-b-30 m-t-20">Escolha o pacote de crédito que deseja.</h3>
                        <p>
                            Aqui você escolhe o pacote de crédito que deseja, mesmo que não utilize todos eles ficam
                            como saldo para um novo envio de carta.
                        </p>
                    </div>

                    <div class="row m-t-50">
                        <?php
                        while($col = $pacotes->fetch_assoc()):
                        ?>
                        <!--Pricing Column-->
                        <article class="pricing-column col-md-4">
                            <div class="inner-box card-box">
                                <div class="plan-header text-center">
                                    <h3 class="plan-title"><?= $col['nome'] ?></h3>
                                    <h2 class="plan-price">R$ <?= $col['valor'] ?></h2>
                                    <!-- div class="plan-duration">Per Month</div -->
                                </div>
                                <ul class="plan-stats list-unstyled text-center">
                                    <li><?= $col['descricao'] ?></li>
                                </ul>

                                <div class="text-center">
                                    <button type="button"
                                       class="btn btn-danger btn-md w-md btn-bordred btn-rounded waves-effect
                                           waves-light " data-toggle="modal" data-target="#modal<?= $col['id'] ?>" >Comprar Agora</button>
                                </div>
                            </div>
                        </article>
                            <!-- Modal -->
                            <div class="modal fade" id="modal<?= $col['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLongTitle">Escolha a opção de pagamento!</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="row modal-body" align="center">
                                            <div class="col-md-6">
                                                <a href="#" class="btnComprarPS"  itemClt="<?= $_SESSION['session_login_user_ne'] ?>" itemDesc="<?= $col['nome'] ?>" itemIdd="<?= $col['id'] ?>" itemVal="<?= $col['valor'] ?>"><img src="assets/images/pagseguro.png" width="100" height="100" ></a>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endwhile;
                        ?>

                        <!--Pricing Column
                        <article class="pricing-column col-md-4">
                            <div class="ribbon"><span>POPULAR</span></div>
                            <div class="inner-box card-box">
                                <div class="plan-header text-center">
                                    <h3 class="plan-title">Pacote Extra</h3>
                                    <h2 class="plan-price">R$19,90%</h2>
                                </div>
                                <ul class="plan-stats list-unstyled text-center">
                                    <li>%Aqui vai descrição do pacote</li>
                                </ul>

                                <div class="text-center">
                                    <a href="#"
                                       class="btn btn-danger btn-md w-md btn-bordred btn-rounded waves-effect waves-light">
                                        Comprar Agora</a>
                                </div>
                            </div>
                        </article>-->


                    </div>

                </div><!-- end col -->
            </div>
            <!-- end row -->

        </div> <!-- container -->

    </div> <!-- content -->


</div>


<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->
<!-- End-Content -->


<!-- Footer -->
<?php
require_once ("layout/footer.php");
?>
<!-- End-Footer -->
<script
        type="text/javascript"
        src="https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.lightbox.js" >
</script>
<script type="text/javascript">
    // Start PagSeguro
        function pagseguro(token){
            PagSeguroLightbox(token);
        }
        $('.btnComprarPS').on('click', function () {
            let valor = $(this).attr('itemVal').replace(',', '.');
            let dados = {
                id: $(this).attr('itemIdd'),
                desc: $(this).attr('itemDesc'),
                val: valor,
                clt: $(this).attr('itemClt')
            };
            $.post('../functions/pagseguro/pagseguro.php', dados, function (res) {
                if(res === 'error'){
                    alert('Houve um erro, atualize a página e tente novamente!');
                }else{
                    pagseguro(res);
                }
            })
        });
    // End PagSeguro

    // Start PayPal
    $('.btnComprarPP').on('click', function () {
        let valor = $(this).attr('itemVal').replace(',', '.');
        let dados = {
            id: $(this).attr('itemIdd'),
            desc: $(this).attr('itemDesc'),
            val: valor,
            clt: $(this).attr('itemClt')
        };
        $.post('../functions/paypal/paypal.php', dados, function (res) {
            if(res === 'error'){
                alert('Houve um erro, atualize a página e tente novamente!');
            }else{
                if(res == 'error'){
                    alert('Houve um problema, tente novamente!')
                }else if(res == 'error2'){
                    alert('Houve um problema, tente novamente')
                }else {
                    console.log(res);
                    window.open(res);
                }

            }
        })
    });
    // End PayPal
</script>