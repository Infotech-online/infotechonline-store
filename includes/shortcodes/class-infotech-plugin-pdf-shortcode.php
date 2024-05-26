<?php

class Infotech_Plugin_Pdf_Shortcode {
    public function __construct() {
        add_shortcode( 'get_pdf', array( $this, 'get_pdf_callback' ) );
    }

    public function get_pdf_callback( $atts ) {
        
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
}