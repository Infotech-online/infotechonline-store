<?php
use MailPoetVendor\Symfony\Component\Validator\Constraints\Length;


function get_pdf() {

    $current_user = wp_get_current_user();
    $user_roles = $current_user->roles; // Obtiene los roles asignados al usuario

    if (!empty($user_roles)) {

        $primary_role = reset($user_roles); // Obtener el primer rol asignado al usuario
        if ($primary_role == 'company_infotech' or $primary_role == 'administrator' && isset($_GET['quotation_id'])) {

            $quotation_id = $_GET['quotation_id'];

            // Get quotation info
            $quotation = get_single_quotation($quotation_id);

            if (count($quotation) > 0) {

                $taxes = $quotation[0]->taxes;
                $shipping = $quotation[0]->shipping;
                $referred = $quotation[0]->referred;
                if ($referred == '') {
                    $referred = 'Ninguno';
                }

                $quotation_items = get_quote_items($quotation_id);

                $products = "";
                $subtotal = 0;
                $total = 0;
                $impuest = 0;
                $discount = "--";

                foreach ($quotation_items as $item) {

                    $product_name = $item["product_name"];
                    $product_qty = $item["quantity"];
                    $unit_price = $item["unit_price"];
                    $total_price = $item["total_price"];
                    $product_sku = $item["product_sku"];
                    $product_iva = ($item["unit_price"] * $taxes) - $item["unit_price"];

                    // Number format of prices
                    $unit_price_formated = number_format($unit_price, 0, ',', '.');
                    $total_price_formated = number_format($total_price, 0, ',', '.');
                    $product_iva = number_format($product_iva, 0, ',', '.');

                    $products .= "<tr class='details-row'><td id='item-title'>$product_name</td><td>$product_sku</td><td>$product_qty</td><td>$$unit_price_formated</td><td>$$product_iva</td><td>$$total_price_formated</td></tr>";

                    $total += intval($total_price) * $taxes;
                    $impuest += (intval($total_price) * $taxes) - intval($total_price);
                    $subtotal += intval($total_price);

                    if ($shipping_class == "b-fee") {
                        $shipping = $quotation[0]->shipping * $taxes;
                    }

                }

                $total = $total + $shipping;
                $subtotal = number_format($subtotal, 0, ',', '.');
                $total = number_format($total, 0, ',', '.');
                $impuest = number_format($impuest, 0, ',', '.');
                $shipping = number_format($shipping, 0, ',', '.');

                // Creation Date
                $creation_date = $quotation[0]->creation_date;
                $creation_date = new DateTime($creation_date);
                $creation_date = $creation_date->format('Y-m-d');

                // Expiry date
                $expiry_date = $quotation[0]->expiry_date;
                $expiry_date = new DateTime($expiry_date);
                $expiry_date = $expiry_date->format('Y-m-d');

                // Get user info
                $user_information = get_quote_user_information($quotation_id);
                $employee_name = $user_information[0]->employee_name;
                $company = $user_information[0]->company;
                $company_address = $user_information[0]->company_address;
                $company_city = $user_information[0]->city;
                $company_email = $user_information[0]->email;
                $company_phone = $user_information[0]->phone;

                if ($employee_name == '') {
                    $employee_name = "Ninguno";
                }

                $cart_url = wc_get_cart_url();

                // Get the base URL of the plugin
                $base_url = plugins_url('/', __FILE__);
                // Specific path to the image within the plugin's folder
                $image_path = $base_url . 'imgs/infotech.jpg';
                $infotech_logo_image = '<img src="' . $image_path . '" width="100px" height="50px">';

                $content = "
                    <h1 class='has-text-align-center wp-block-post-title' style='margin-bottom: 70px'>Cotización #$quotation_id</h1>
                    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>

                    <div class='content'>
                        <div class='table_container'>

                            <div class='quotation-buttons' style='margin-bottom: 20px;'>

                                <a href='$cart_url' class='back-button'>Volver al carrito</a>
                                <a href='https://infotechonline.co/tus-cotizaciones/' class='back-button'  style='margin-left: 10px;'>Ver tus cotizaciones</a>
                                <button id='download-button' style='margin-left: 10px; cursor: pointer; outline: none;' class='download-button'>Descargar PDF</button>
                            
                            </div>

                            <div id='quote-page_1' class='quotation_page'>

                            <div class='quotation-header'>
                                <div class='address-info'>
                                    <h6>Infotech de Colombia S.A.S</h6>
                                    <p>Cra. 100 #11 - 90, Holguines Trade Center</p>
                                    <p>Valle del Cauca - Cali</p>
                                    <a href='infotechonline.co' style='font-size: 13px;'>infotechonline.co</a>

                                    <h6 style='margin-top: 20px;'>Cotización para:</h6>
                                    <p>Compañia: $company</p>
                                    <p>Dirección: $company_address</p>
                                    <p>Ciudad: $company_city</p>
                                    <p>Correo: $company_email</p>
                                    <p>Telefono: $company_phone</p>
                                    <p>Empleado: $employee_name</p>
                                </div>
                                <div class='quotation-info'>
                                    $infotech_logo_image
                                    <h6 style='margin-top: 20px;'>Cotización</h6>
                                    <p>Fecha: $creation_date</p>
                                    <p>Numero: $quotation_id</p>

                                    <h6 style='margin-top: 20px;'>Validez</h6>
                                    <p>Hasta $expiry_date</p>

                                    <h6 style='margin-top: 20px;'>Comercial referido</h6>
                                    <p>$referred</p>
                                </div>
                            </div>

                            <table class='quotation-details_table table'>
                                <thead class='thead-dark'>
                                    <tr style='font-size: 13px;'>
                                        <th>Item</th>
                                        <th>Sku</th>
                                        <th>Cant.</th>
                                        <th>P.unitario</th>
                                        <th>IVA</th>
                                        <th>P.total sin IVA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    $products
                                    <tr>
                                        <td colspan='5' style='text-align: right; font-weight: bold;'>Subtotal</td>
                                        <td>$$subtotal</td>
                                    </tr>
                                    <tr>
                                        <td colspan='5' style='text-align: right; font-weight: bold;'>IVA 19% </td>
                                        <td>$$impuest</td>
                                    </tr>
                                    <tr>
                                        <td colspan='5' style='text-align: right; font-weight: bold;'>Envio </td>
                                        <td>$$shipping</td>
                                    </tr>
                                    <tr>
                                        <td colspan='5' style='text-align: right; font-weight: bold;'>Descuento </td>
                                        <td>$ $discount</td>
                                    </tr>
                                    <tr>
                                        <td colspan='5' style='text-align: right; font-weight: bold;'>Total </td>
                                        <td>$$total</td>
                                    </tr>
                                </tbody>
                            </table>

                            <p style='font-size: 14px; font-weight: 500; text-align: center;'>La cotización de precios está sujeta a modificaciones basadas en la <b>TRM</b> y la <b>rotación de inventario</b>, por lo cual esta cotización tiene vigencia de 1 día.</p>
                            </div>

                        </div>
                        
                    </div>

                    <style>
                        @media (max-width: 782px) {
                            .table_container {
                                max-width: 300px;
                                overflow: auto;
                            }
                        }  

                        .quotation-header {
                            margin-top: 20px;
                            margin-bottom: 40px;
                            display: flex;
                            justify-content: space-between;
                        }

                        .quotation-header p {
                            display: flex;
                            justify-content: space-between;
                            margin: 0;
                            font-size: 13px;
                        }

                        .address-info {
                            display: flex;
                            flex-direction: column;
                        }

                        .quotation_page {
                            width: calc(827px - 70px);
                            /* max-height: calc(1054px - 20px); */
                            background-color: rgb(255, 255, 255);
                            padding: 10px 35px;
                            margin-bottom: 20px;
                        }

                        .content {
                            font-size: 16px;
                            display: flex;
                            flex-direction: column;
                            justify-content: center;
                            align-items: center;
                            padding: 0 0 20px 0;
                        }

                        .quotation-details_table {
                            font-family: arial, sans-serif;
                            border-collapse: collapse;
                            width: 100%;
                            max-width: calc(827px - 70px);
                            padding: 0;
                            margin: 0 0 20px 0;
                            font-size: 13px;
                        }

                        .quotation-details_table td#item-title {
                            text-align: left;
                        }

                        .quotation-details_table td {
                            padding: 3px;
                            text-align: justify;
                            border: 1px solid gray;
                            text-align: center;
                        }

                        #quote-page_1 {
                            border: 2px solid #DCDCDC;
                            border-radius: 10px;
                        }

                        .quotation-buttons {
                            width: 700px;
                        }

                        .back-button {
                            border-radius: 10px;
                            padding: 10px;
                            background-color: #7ADC85;
                            color: #fff;
                            text-decoration: none;
                        }

                        .back-button:hover {
                            color: #fff;
                            text-decoration: none;
                            background-color: #71D17C;
                        }

                        .download-button {
                            border-radius: 10px;
                            padding: 5px;
                            background-color: #7ADC85;
                            color: #fff;
                            border: 0;
                            outline: none;
                        }

                        .download-button:hover {
                            background-color: #71D17C;
                        }

                    </style>

                    <!-- html2canvas library -->
                    <script src='https://jgallego.pythonanywhere.com/cdn/js/html2canvas.min.js'></script>
                    <!-- jsPDF library -->
                    <script src='https://jgallego.pythonanywhere.com/cdn/js/jsPDF/dist/jspdf.umd.js'></script>

                    <script>
                        
                        let download_document_button = document.getElementById('download-button');
                        let quotation_page = document.getElementById('quote-page_1');

                        download_document_button.addEventListener('click', function(){
                            quotation_page.style.border = '2px solid transparent';
                            download_pdf();
                        });

                        function download_pdf() {

                            window.jsPDF = window.jspdf.jsPDF;

                            var doc = new jsPDF();

                            doc.html(page1, {
                                callback: function (doc) {

                                    doc.save('cotizacion-'+make_id(5)+'-ID$quotation_id'+'.pdf');

                                    // Convertir el PDF a un Blob para enviarlo al servidor
                                    var blobPDF = doc.output('datauristring');
                                    console.log(blobPDF, 'cotizacion-'+make_id(5)+'-ID$quotation_id'+'.pdf');
                                    let pdfName = 'cotizacion-'+make_id(5)+'-ID$quotation_id'+'.pdf';
                                    send_to_server(String(blobPDF), pdfName, '$quotation_id', '$company_email');

                                    quotation_page.style.border = '2px solid #DCDCDC';

                                },
                                x: 0,
                                y: 0,
                                width: 210, //target width in the PDF document
                                windowWidth: 757, //window width in CSS pixels
                                height: 279
                            });
                        }

                        // ID Generator
                        function make_id(length) {

                            let result = '';
                            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                            const charactersLength = characters.length;
                            let counter = 0;

                            while (counter < length) {
                                result += characters.charAt(Math.floor(Math.random() * charactersLength));
                                counter += 1;
                            }
                            return result;
                        }

                        // Source HTMLElement or a string containing HTML.
                        var page1 = document.querySelector('#quote-page_1');

                        function send_to_server(pdfFile, pdfName, pdfNumber, email) {

                            data = {
                                pdf_file: pdfFile,
                                pdf_name: pdfName,
                                pdf_number: pdfNumber,
                                email_recipent: email
                            }

                            let url = `https://jgallego.pythonanywhere.com/save_pdf`;

                            fetch(url, {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(data)})	    

                            .then(response => {
                                console.log('Archivo PDF enviado correctamente');
                            })

                            .catch(error => {
                                console.error('Error al enviar el archivo PDF', error);
                            });
                        }

                    </script>";

                    return $content;

            } else {
                $quotation_id = $_GET['quotation_id'];
                return "<h1>El documento #$quotation_id no existe</h1>";
            }
        } else {
            wp_redirect(home_url('/'));
            exit;
        }
    } else {
        wp_redirect(home_url('/'));
        exit;
    }
}

add_shortcode('pdf-shortcode', 'get_pdf');

// User quotes table

function show_user_quotations() {

    $current_user = wp_get_current_user();

    if ($current_user->ID !== 0) {

        $user_id = $current_user->ID;

        $quotations = get_quotes($user_id);
        $quotations_info = "";
        
        foreach ($quotations as $quote) {

            $items = get_quote_items($quote->quote_id);

            $quote_details = '';
            $total_product_quantity = 0;
            $quotation_total_price = 0;

            foreach ($items as $item) {
                $product_name = $item['product_name'];

                $product_qty = $item['quantity'];
                // All products on the quotation
                $total_product_quantity += $product_qty;

                $unit_price = $item["unit_price"];
                $total_price = $item["total_price"];

                // The price of all products in the quotation with IVA
                $quotation_total_price += intval($quotation_total_price + $total_price * $quote->taxes);

		        $unit_price = number_format($unit_price, 0, ',', '.');
		        $total_price = number_format($total_price, 0, ',', '.');

                $product_sku = $item["product_sku"];
                $quote_details .= "<tr class='details-row'><td>$product_sku</td><td>$product_name</td><td>$product_qty</td><td>$$unit_price</td><td>$$total_price</td></tr>";
            }

	        $quotation_total_price = $quotation_total_price + $quote->shipping;
            $quotation_total_price = number_format($quotation_total_price, 0, ',', '.');

            $quotation_shipping = number_format($quote->shipping, 0, ',', '.');
            
            $quotations_info .= "
            <tr>
                <th>$quote->quote_id</th>
                <td>$total_product_quantity</td>
                <td>$quote->discount</td>
                <td>$ $quotation_shipping</td>
                <td>$ $quotation_total_price</td>
                <td>$quote->creation_date</td>
                <td>$quote->expiry_date</td>
                <td style='display: flex;'>
                    <div id='modal-button_$quote->quote_id' style='cursor:pointer;text-decoration:underline'><ion-icon name='eye-outline'></ion-icon></div>
                    <div style='cursor:pointer;text-decoration:underline; margin-left: 10px;'>
                        <form action='cotizacion' method='GET' target='_blank'>
                            <input type='hidden' name='quotation_id' value='$quote->quote_id'>
                            <button type='submit' style='background-color: transparent;'><ion-icon name='document-text-outline'></ion-icon></button>
                        </form>
                    </div>
                </td>";

            $quotations_info .= "
            <script type='text/javascript'>

                const html_to_quote_$quote->quote_id = `
                    <div style='display:flex; flex-direction: column; text-align: left;'>
                        <table>
                            <thead>
                                <tr class='details-row'>
                                    <th>SKU</th>
                                    <th>Nombre</th>
                                    <th>Cant.</th>
                                    <th>Precio unitario</th>
                                    <th>Precio total</th>
                                </tr>
                            </thead>
                            <tbody>
                                $quote_details
                            </tbody>
                        </table>
                    </div>
                `;

                document.getElementById('modal-button_$quote->quote_id').addEventListener('click', function() {
                    Swal.fire({
                        title: 'Detalles cotización #$quote->quote_id',
                        html: html_to_quote_$quote->quote_id,
                        width: 1000,
                        confirmButtonText: 'Cerrar'
                    })
                })
                
            </script>
            </tr>";
        }

        $content = "
            <h1 class='has-text-align-center wp-block-post-title'>Tus cotizaciones</h1>   
            <div class='quotations-table-container'>

                <!-- Table Libraries -->
                <link href='https://cdn.datatables.net/v/bs5/dt-1.13.8/datatables.min.css' rel='stylesheet'>
                <link href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css' rel='stylesheet'>
                <link href='https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css' rel='stylesheet'>
                <script src='https://cdn.datatables.net/v/bs5/dt-1.13.8/datatables.min.js'></script>

                <!-- Ionicos -->
                <script type='module' src='https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js'></script>
                <script nomodule src='https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js'></script>

                <div class='table_container'>
                    <table id='credit_orders_table' class='table table-striped' style='width: 1000px;'>
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Productos</th>
                                <th>Descuento</th>
                                <th>Envio</th>
                                <th>Total</th>
                                <th>Fecha de creacion</th>
                                <th>Fecha de validez</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            $quotations_info
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Id</th>
                                <th>Productos</th>
                                <th>Descuento</th>
                                <th>Envio</th>
                                <th>Total</th>
                                <th>Fecha de creacion</th>
                                <th>Fecha de validez</th>
                                <th>Acciones</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <script type='text/javascript'>
                    new DataTable('#credit_orders_table');
                </script>
            </div>

        <style>

            @media (max-width: 782px) {
                .table_container {
                    max-width: 300px;
                    overflow: auto;
                }
            }  
            .quotations-table-container {
                boder:1px solid #DCDCDC; 
                width: 100%; 
                display: flex; 
                justify-content: center;
            }

            .quotations-table-container table {
                background-color: #DCDCDC;
                border-radius: 10px;
                padding: 7px;
            }

            .details-row td, .details-row th {
                padding: 5px;
                border: 1px solid gray;
            }

            ion-icon {
                color: #424242;
                font-size: 25px;
                background-color: #DADADA;
                padding: 5px;
                border-radius: 10px;
                transition-duration: 0.3s;
            }

            ion-icon:hover {
                color: #0D6EFD;
            }

            button {
                outline: none;
                border: none;
            }
        </style>

        <!-- Modals -->

        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        ";

        return $content;

    } else {
        // User isn't logged in 
        $login_url = wp_login_url();

        // Redirect to login
        wp_redirect($login_url);
        exit;
    }
}

add_shortcode('user-quotations-shortcode', 'show_user_quotations');


// Create quotation button

function create_quotation_test() {

    $current_user = wp_get_current_user();
    $user_roles = $current_user->roles; // Obtiene los roles asignados al usuario

    if (!empty($user_roles)) {

        $primary_role = reset($user_roles); // Obtener el primer rol asignado al usuario
        if ($primary_role == 'company_infotech' or $primary_role == 'administrator') {

            if ($_SERVER["REQUEST_METHOD"] === "POST") {

                if (isset($_POST['quotation'])) {

                    $employee_name = $_POST['employee_name'];
                    $company = $_POST['company'];
                    $address = $_POST['company_address'];
                    $city = $_POST['city'];
                    $email = $_POST['email'];
                    $phone = $_POST['phone'];
                    $referred = $_POST['referred'];

                    $cart_products = get_cart_products();

                    if ($cart_products != false) {
                        
                        $quotation_id = create_quote_log($employee_name, $company, $address, $city, $email, $phone, $referred);

                        // Redirect to quotation page
                        wp_redirect("http://infotechonline.co/cotizacion?quotation_id=$quotation_id");
                        exit;
                    }
                }
            }

            $self_page = wc_get_cart_url();
            $quotations_page = "https://infotechonline.co/tus-cotizaciones/";

            // Get user information

            $user_id = wp_get_current_user()->ID;
            $user_email = $current_user->user_email;
            $user_company = get_user_meta($user_id, 'billing_company', true);
            $user_city = get_user_meta($user_id, 'billing_city', true);
            $user_address = get_user_meta($user_id, 'billing_address_1', true);
            $user_phone = get_user_meta($user_id, 'billing_phone', true);
            
            $content = "
            <div class='content-container_quotation alignwide'>
                <div class='content_quotation'>
                    <h4>Realiza tu cotización</h4>
                    <div class='button_container'>
                        <button class='quotation-button generate-quotation_button' id='generate-quotation_button'>Generar cotización</button><a class='quotation-button your-quotations_button' href='$quotations_page' target='_blank'>Ver tus cotizaciones</a><button class='info_button' id='info_button'><ion-icon name='information-circle-outline'></ion-icon></button>
                    </div>
                </div>
            </div><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script type='text/javascript'>
                const html_form = `
                <p>Las cotizaciones están sujetas a cambios en la Tasa Representativa del Mercado (TRM) y a la rotación del inventario.</p>
                <form action='$self_page' method='POST' id='create-quotation_post'>
                    <input name='quotation' hidden value='new_quotation_registered'>
                    <div style='display: flex; flex-direction: column; margin-top: 15px;'>
                        <label for='company-input' style='text-align: left; margin: 0 5px 0 5px;'>Compañia</label>
                        <input name='company' class='swal2-input' id='company_input' style='margin: 0 5px 0 5px;' value='$user_company' required>
                    </div>
                    <div style='display: flex; flex-direction: column; justify-content: left; margin-top: 15px;'>
                        <label style='text-align: left; margin: 0 5px 0 5px;'>Direccion</label>
                        <input name='company_address' class='swal2-input' id='address_input' style='margin: 0 5px 0 5px;' value='$user_address' required>
                    </div>
                    <div style='display: flex; flex-direction: column; margin-top: 15px;'>
                        <label style='text-align: left; margin: 0 5px 0 5px;'>Ciudad</label>
                        <input name='city' class='swal2-input' id='city_input' style='margin: 0 5px 0 5px;' value='$user_city' required>
                    </div>
                    <div style='display: flex; flex-direction: column; margin-top: 15px;'>
                        <label style='text-align: left; margin: 0 5px 0 5px;'>Correo</label>
                        <input name='email' class='swal2-input' id='email_input' style='margin: 0 5px 0 5px;' value='$user_email' required>
                    </div>
                    <div style='display: flex; flex-direction: column; margin-top: 15px;'>
                        <label style='text-align: left; margin: 0 5px 0 5px;'>Telefono</label>
                        <input name='phone' class='swal2-input' id='phone_input' style='margin: 0 5px 0 5px;' value='$user_phone' required>
                    </div>
                    <div style='display: flex; flex-direction: column; margin-top: 15px;'>
                        <label for='employee-input' style='text-align: left; margin: 0 5px 0 5px;'>Empleado (Opcional)</label>
                        <input name='employee_name' class='swal2-input' id='employee_input' style='margin: 0 5px 0 5px;'>
                    </div>
                    <div style='display: flex; flex-direction: column; margin-top: 15px;'>
                        <label style='text-align: left; margin: 0 5px 0 5px;'>Referido (opcional)</label>
                        <input name='referred' class='swal2-input' id='referred' style='margin: 0 5px 0 5px;'>
                    </div>
                    <button type='submit' class='quotation-button' style='margin-top: 20px; border: none !important; outline: none !important;'>Generar</button>
                </form>`;
                const info_container = `
                    <div class='info_content_container'>
                        <span style='padding-top: 10x;'><b>1.</b> Explora el catálogo de productos y selecciona los artículos deseados agregandolos al carrito.</span>
                        <span><b>2.</b> Navega hacia el carrito donde se encuentran los productos seleccionados.</span>
                        <span><b>3.</b> Busca el boton de Generar cotización y rellena el formulario con los detalles requeridos para la cotización.</span>
                        <span><b>4.</b> Confirma los detalles proporcionados en el formulario.</span>
                        <span><b>5.</b> Después de haber generado la cotización, busca la opción para descargar el documento en formato PDF.</span>
                        <p style='margin-top: 20px; font-weight: 500; text-align: left; margin-bottom: 0;'>Una vez que hayas descargado el documento en formato PDF, el sistema enviará automáticamente la cotización al correo electrónico asociado con la solicitud desde la dirección de correo (cotizaciones.infotech@gmail.com).</p>
                    </div>`;
                document.getElementById('generate-quotation_button').addEventListener('click', function() {
                    Swal.fire({
                        title: 'Informacion de la cotización',
                        html: html_form,
                        width: 600,
                        showConfirmButton: false
                    })
                });
                document.getElementById('info_button').addEventListener('click', function() {
                    Swal.fire({
                        title: 'Como generar una cotización?',
                        html: info_container,
                        width: 600,
                        showConfirmButton: true
                    })
                })
            </script>
            <style type='text/css'>
            .content-container_quotation {
                width: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                padding-top: 50px;
                max-width: 1400px !important;
                box-sizing: border-box;
            } 
            .content_quotation {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                align-items: center;
                box-shadow: 0px 6px 15px 0px rgba(64, 79, 104, 0.05);
                border: 1px solid var(--wp--preset--color--gray-100);
                width: 100%;
                padding: 20px;
                border-radius: 8px;
            }
            .button_container {
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .generate-quotation_form {
                display: flex;
                min-height: 50px;
            } 
            .quotation-button {
                cursor: pointer;
                border-radius: 10px;
                padding: 5px 10px;
                font-weight: 300;
                font-size: 20px;
                background-color: #4e8e3a;
                color: #fff;
                border: 0;
                outline: none;
                min-height: 46px;
                box-sizing: border-box;
            } 
            .quotation-button:hover {
                background-color: #60af48;
                color: #fff !important;
            }
            .generate-quotation_button {
                margin-right: 20px;
                padding: 10px;
            }
            .info_button {
                background-color: #3F79A7;
                color: #fff;
                font-size: 25px;
                min-height: 46px;
                width: 46px;
                cursor: pointer;
                border-radius: 10px;
                outline: none;
                border: none;
                margin-left: 20px;
                text-align: center;
                box-sizing: border-box;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .info_button:hover {
                background-color: #5299D2;
                color: #fff !important;
            }
            .wp-block-woocommerce-cart {
                padding-top: 0;
                margin-top: -60px !important;
            }
            .swal2-input {
                font-size: 15px;
            }
            .info_content_container {
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: start;
            }
            .info_content_container span {
                text-align: left;
                margin-top: 10px;
            }
            .info_content_container span b {
                font-size: 20px;
                font-wheight: bold;
            }</style>
            
            <script type='module' src='https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js'></script>
            <script nomodule src='https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.j'></script>";

            return trim($content);
	    }
    }
}

add_shortcode('generate-pdf-button', 'create_quotation_test');