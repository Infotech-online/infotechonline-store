<?php

// Mostrar checkbox en la página de pago
add_action('woocommerce_review_order_before_payment', 'mostrar_checkbox_monedero');

function mostrar_checkbox_monedero() {
    ?>
    <div id="monedero_descuento" style="display: flex;">
        <input type="checkbox" name="usar_monedero" id="usar_monedero">
        <label for="usar_monedero">Pagar con mi monedero</label>
    </div>
    <script type="text/javascript">
        jQuery(function($){
            $('#usar_monedero').change(function(){
                var usarMonedero = $('#usar_monedero').is(':checked') ? 'yes' : 'no';
                $.ajax({
                    type: 'POST',
                    url: wc_checkout_params.ajax_url, // URL de AJAX proporcionada por WooCommerce
                    data: {
                        action: 'guardar_estado_monedero',
                        usar_monedero: usarMonedero
                    },
                    success: function(response) {
                        $('body').trigger('update_checkout');
                    }
                });
            });
        });
    </script>
    <?php
}

add_action('wp_ajax_guardar_estado_monedero', 'guardar_estado_monedero');
add_action('wp_ajax_nopriv_guardar_estado_monedero', 'guardar_estado_monedero');

function guardar_estado_monedero() {
    if (isset($_POST['usar_monedero'])) {
        WC()->session->set('usar_monedero', wc_clean($_POST['usar_monedero']));
    }
    wp_die(); // Detiene la ejecución para devolver una respuesta a AJAX
}

// Aplicar el descuento al calcular las tarifas del carrito
add_action('woocommerce_cart_calculate_fees', 'aplicar_descuento_monedero');

function aplicar_descuento_monedero($cart) {
    if (WC()->session->get('usar_monedero') === 'yes') {
        // Monto del descuento que deseas aplicar
        $descuento = 655000; // Por ejemplo, 10 unidades monetarias de descuento

        // Calcular el total de impuestos del carrito
        $total_impuestos = $cart->get_taxes_total();
        
        // Calcular el descuento sin incluir impuestos
        $descuento_sin_impuestos = $descuento / (1 + ($total_impuestos / $cart->get_subtotal()));

        // Aplica el descuento sin impuestos
        $cart->add_fee('Descuento Monedero', -$descuento_sin_impuestos, false);
    }
}
