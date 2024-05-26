<?php

// Add the custom payment method
add_action('woocommerce_payment_gateways', 'add_custom_payment_method');

function add_custom_payment_method($gateways) {
    $gateways[] = 'WC_Infotech_Credit_Payment';
    return $gateways;
}

class WC_Infotech_Credit_Payment extends WC_Payment_Gateway {

    // Constructor
    public function __construct() {
        $this->id = 'infotech_credit_payment'; // Identifier of the payment
        $this->method_title = 'Crédito directo con Infotech'; // Only visible for the user_
        $this->title = 'Crédito directo o Convenio'; // Only visible for the checkout
        $this->has_fields = true;
        $this->init_form_fields();
        $this->init_settings();
        
        // Details of the payment method
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');
        
        // Save the configurations of the method
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_checkout_process', 'validate_custom_fields');
        add_action('woocommerce_admin_order_data_after_shipping_address', 'mostrar_texto_descriptivo_shipping', 10, 1);
        add_action('woocommerce_thankyou', 'verify_payment_method');
    }

    // Configuration of the credit 
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'   => 'Habilitar/Deshabilitar',
                'type'    => 'checkbox',
                'label'   => 'Habilitar Tu Método de Pago',
                'default' => 'yes',
            ),
            'description' => array(
                'title'       => 'Descripción',
                'type'        => 'textarea',
                'description' => 'Descripción del método de pago que se mostrará al cliente.',
                'default'     => 'Paga con el crédito directo a Infotech (Opcion disponible solo para empresas)',
            ),
        );
    }

    public function payment_fields() {

        // Company name field
        echo '<div id="custom_payment_field" style="width: calc(100% - 40px);">';
        woocommerce_form_field('company_name', array(
            'type'        => 'text',
            'class'       => array('custom-field-class form-row-wide'),
            'label'       => __('Nombre de la compañía o fondo de empleados', 'text-domain'),
            'placeholder' => __('Ingresa aquí', 'text-domain'),
            'required'    => true,
        ), '');
        echo '</div>';

        // Company ID field
        echo '<div id="custom_payment_field" style="margin-top: 20px;width: calc(100% - 40px);">';
        woocommerce_form_field('company_identification', array(
            'type'        => 'text',
            'class'       => array('custom-field-class form-row-wide'),
            'label'       => __('Identificación de la empresa (NIT)', 'text-domain'),
            'placeholder' => __('Ingresa aquí', 'text-domain'),
            'required'    => true,
        ), '');
        echo '</div>';

        // Employee name field
        echo '<div id="custom_payment_field" style="margin-top: 20px;width: calc(100% - 40px);">';
        woocommerce_form_field('employee_name', array(
            'type'        => 'text',
            'class'       => array('custom-field-class form-row-wide'),
            'label'       => __('Nombre completo del empleado', 'text-domain'),
            'placeholder' => __('Ingresa aquí', 'text-domain'),
            'required'    => true,
        ), '');
        echo '</div>';

        // Employee position field
        echo '<div id="custom_payment_field" style="margin-top: 20px;width: calc(100% - 40px);">';
        woocommerce_form_field('employee_position', array(
            'type'        => 'text',
            'class'       => array('custom-field-class form-row-wide'),
            'label'       => __('Posición laboral', 'text-domain'),
            'placeholder' => __('Ingresa aquí', 'text-domain'),
            'required'    => true,
        ), '');
        echo '</div>';
        
        // Employee position field
        echo '<div id="custom_payment_field" style="margin-top: 20px;width: calc(100% - 40px);">';
        woocommerce_form_field('order_type', array(
            'type'        => 'select',
            'class'       => array('custom-field-class form-row-wide'),
            'label'       => __('Tipo de pedido', 'text-domain'),
            'placeholder' => __('Selecciona un tipo', 'text-domain'),
            'required'    => true,
            'options'     => array(
                'direct_credit' => __('Crédito directo ( Para créditos directos con Infotech )', 'text-domain'),
                'convenio' => __('Convenio ( Para los fondos de empleado )', 'text-domain')
            ),
        ), '');
        echo '</div>';

        echo '<p id="additional-paragraph">Al realizar un pedido mediante crédito directo, se verificará la disponibilidad de crédito y se procederá con el procesamiento del pedido si existe cupo disponible.</p>';
        ?>

        <!-- JavaScript para mostrar el párrafo adicional según la opción seleccionada -->
        <script>
        jQuery(function($) {
            // Detecta el cambio en el campo de selección
            $('#order_type').change(function() {
                var selectedOption = $(this).val(); // Obtiene el valor de la opción seleccionada

                // Muestra u oculta el párrafo adicional según la opción seleccionada
                if (selectedOption === 'direct_credit') {
                    $('#additional-paragraph').text('Al realizar un pedido mediante crédito directo, se verificará la disponibilidad de crédito y se procederá con el procesamiento del pedido si existe cupo disponible.').show();
                } else if (selectedOption === 'convenio') {
                    $('#additional-paragraph').text('Al realizar un pedido a través del convenio, se verificará la información con tu fondo de empleado y, una vez confirmada, se procederá con el procesamiento del pedido.').show();
                } else {
                    $('#additional-paragraph').hide();
                }
            });
        });

        // Select the target
        const companyName = document.getElementById('company_name_field');
        const companyIdentification = document.getElementById('company_identification_field');
        const employeeName = document.getElementById('employee_name_field');
        const employeePosition = document.getElementById('employee_position_field');
        const orderType = document.getElementById('order_type_field');

        function add_observer(element) {
            // MainObserver for the classes of the element
            const observer = new MutationObserver(function(mutationsList, observer) {
                // If the class "woocommerce-validated exists
                if (element.classList.contains('woocommerce-validated')) {
                    element.classList.remove('woocommerce-validated');
                }
            });

            // Add the observer to the target
            const config = { attributes: true, attributeFilter: ['class'] };
            observer.observe(element, config);
        }

        add_observer(companyName);
        add_observer(companyIdentification);
        add_observer(employeeName);
        add_observer(employeePosition);
        add_observer(orderType);

        </script>
        <?php
    }

    // The payment is process
    public function process_payment($order_id) {

        $order = wc_get_order($order_id);
        $order->update_status('processing');
        $order->save();

        // Add meta values to the table (wp_postmeta)
        add_post_meta($order_id, "_company_name", $_POST["company_name"]);
        add_post_meta($order_id, "_company_identification", $_POST["company_identification"]);
        add_post_meta($order_id, "_employee_name", $_POST["employee_name"]);
        add_post_meta($order_id, "_employee_position", $_POST["employee_position"]);
        add_post_meta($order_id, "_order_type", $_POST["order_type"]);

        // Redirect to the success page
        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url($order),
        );
    }

    // Validate the custom fields

    function validate_custom_fields() {
        if ($_POST['payment_method'] === 'infotech_credit_payment') {

            if (empty($_POST['company_name'])) {
                wc_add_notice(__('Por favor, completa el campo de nombre de la empresa o fondo de empleados.', 'text-domain'), 'error');
            }

            if (empty($_POST['company_identification'])) {
                wc_add_notice(__('Por favor, completa el campo de identificación de la empresa', 'text-domain'), 'error');
            }

            if (empty($_POST['employee_name'])) {
                wc_add_notice(__('Por favor, completa el campo de nombre del empleado.', 'text-domain'), 'error');
            }

            if (empty($_POST['employee_position'])) {
                wc_add_notice(__('Por favor, completa el campo de posición laboral.', 'text-domain'), 'error');
            }

            if (empty($_POST['order_type'])) {
                wc_add_notice(__('Por favor, completa el campo de tipo de pedido.', 'text-domain'), 'error');
            }
        }
    }

    // Add custom fields to the orders with the "infotech_payment_method"
    function mostrar_texto_descriptivo_shipping($order) {

        $order_id = $order->get_id();
        $company_name = get_post_meta($order_id, '_company_name', true);
        $company_identification = get_post_meta($order_id, '_company_identification', true);
        $employee_name = get_post_meta($order_id, '_employee_name', true);
        $employee_position = get_post_meta($order_id, '_employee_position', true);
        $order_type = get_post_meta($order_id, '_order_type', true);

        echo '<div class="form-field form-field-wide">';
        echo "<p><strong>Nombre de la compañía o fondo de empleados:</strong><br>$company_name</p>";
        echo "<p><strong>Identificación de la empresa (NIT):</strong><br>$company_identification</p>";
        echo "<p><strong>Nombre del empleado:</strong><br>$employee_name</p>";
        echo "<p><strong>Posición laboral:</strong><br>$employee_position</p>";
        echo "<p><strong>Tipo de pedido:</strong><br>$order_type</p>";
        echo '</div>';
    }
    function verify_payment_method($order_id) {
        // Obtiene el método de pago utilizado en la orden
        $order = wc_get_order($order_id);

        // Mostrar texto después de la dirección de envío en la página de edición de órdenes en el backend
        do_action('woocommerce_admin_order_data_after_shipping_address', $order);
    }

}