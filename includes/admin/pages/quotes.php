<div style="margin: 20px; boder:1px solid gray;">

    <h2 style="margin-bottom: 30px">Cotizaciones</h2>

    <!-- Table Libraries -->
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/datatables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/datatables.min.js"></script>

    <!-- Ionicos -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <table id="credit_orders_table" class="table table-striped" style="width:100%;">
        <thead>
            <tr>
                <th>Id</th>
                <th>Company</th>
                <th>Email</th>
                <th>Fecha de creacion</th>
                <th>Validez</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php

                $quotes = get_quotes();
                $quotation_link = "http://localhost/projects/infotech/wordpress/cotizacion"; // You need to create the quotation page with the "cotizacion" url

                foreach ($quotes as $quote) {

                    echo "<tr>";
                    echo "<th>$quote->quote_id</th>";
                    echo "<td>$quote->user_nicename</td>";
                    echo "<td>$quote->user_email</td>";
                    echo "<td>$quote->creation_date</td>";
                    echo "<td>$quote->expiry_date</td>";
                    echo "
                    <td style='display: flex;'>
                        <div id='modal-button_$quote->quote_id' style='cursor:pointer;text-decoration:underline'><ion-icon name='eye-outline'></ion-icon></div>
                        <div style='cursor:pointer;text-decoration:underline; margin-left: 10px;'>
                            <form action='cotizacion' method='GET' target='_blank'>
                                <input type='hidden' name='quotation_id' value='$quote->quote_id'>
                                <button type='submit' style='background-color: transparent;'><ion-icon name='document-text-outline'></ion-icon></button>
                            </form>
                        </div>
                    </td>";
                    
                    $items = get_quote_items($quote->quote_id);

                    $quote_details = "";
                    foreach ($items as $item) {
                        $product_name = $item["product_name"];
                        $product_qty = $item["quantity"];
                        $unit_price = $item["unit_price"];
                        $total_price = $item["total_price"];
                        $product_sku = $item["product_sku"];
			
                        $unit_price = number_format($unit_price, 0, ',', '.');
                        $total_price = number_format($total_price, 0, ',', '.');

                        $quote_details .= "<tr class='details-row'><td>$product_sku</td><td>$product_name</td><td>$product_qty</td><td>$$unit_price</td><td>$$total_price</td></tr>";
                    }

                    echo "
                    <style>
                        .details-row td, .details-row th {
                            padding: 5px;
                            border: 1px solid gray;
                        }
                    </style>
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
                    ";

                    echo "</tr>";
                }

            ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Id</th>
                <th>Company</th>
                <th>Email</th>
                <th>Fecha de creacion</th>
                <th>Validez</th>
                <th>Acciones</th>
            </tr>
        </tfoot>
    </table>

    <style>
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

    <script type="text/javascript">
        new DataTable('#credit_orders_table');
    </script>
</div>

<!-- Modals -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>