<?php

/*
// Agregar un botón debajo de "Proceed to Checkout"
add_action('woocommerce_proceed_to_checkout', 'create_quotation');

// Create quotation button

function create_quotation_test() {

    $current_user = wp_get_current_user();
    $user_roles = $current_user->roles; // Obtiene los roles asignados al usuario

    if (!empty($user_roles)) {

        $primary_role = reset($user_roles); // Obtener el primer rol asignado al usuario
        if ($primary_role == 'company_infotech' or $primary_role == 'administrator') {

            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                if (isset($_POST['quotation'])) {

                    $cart_products = get_cart_products();

                    if ($cart_products != false) {

                        $quotation_id = create_quote_log();

                        // Redirect to quotation page
                        wp_redirect("http://infotechonline.co/cotizacion?quotation_id=$quotation_id");
                        exit;
                    }
                }
            }

            $self_page = wc_get_cart_url();
            $quotations_page = "https://infotechonline.co/tus-cotizaciones/";

            $content = "

                <div class='content_quotation' style='width: 100%; heigth: 200px; justify-content: center; margin-left: auto !important; margin-right: auto !important; display: flex; justify-content: center; flex-direction: column; align-items: center;'>
                    <a class='download-button' href='https://infotechonline.co/tus-cotizaciones/'>Ver tus cotizaciones</a>
                    <form method='POST' action='$self_page' style='display: flex; height: min-content; margin-bottom: 20px'>
                        <input name='quotation' hidden value='new_quotation_registered'>
                        <button class='download-button'>Generar cotización</button>
                    </form>
                </div>

                <style>
                    .download-button {
                        width: 100%;
                        cursor: pointer;
                        border-radius: 10px;
                        padding: 15px;
                        font-weight: 300;
                        font-size: 20px;
                        background-color: #7ADC85;
                        color: #fff;
                        border: 0;
                        outline: none;
                    }

                    .download-button:hover {
                        background-color: #71D17C;
                        color: #fff !important;
                    }
                
                </style>
            ";

            echo $content;
        }

    }

}
*/
?>