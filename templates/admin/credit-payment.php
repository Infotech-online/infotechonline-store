<div style="margin: 20px; boder:1px solid gray;">

    <!-- Table Libraries -->
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/datatables.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/datatables.min.js"></script>

    <h2 style="margin-bottom: 30px">Pagos por Crédito</h2>

    <table id="credit_orders_table" class="table table-striped" style="width:100%;">
        <thead>
            <tr>
                <th>Id</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Company</th>
                <th>Cliente</th>
                <th>Email</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php

                $orders = get_credit_orders();

                foreach ($orders as $order) {
                    $total_amount = (int)$order->total_amount;
                    echo "<tr>";
                    echo "<th>$order->id</th>";
                    echo "<td>$order->status</td>";
                    echo "<td>$total_amount</td>";
                    echo "<td>$order->company</td>";
                    echo "<td>$order->user_nicename</td>";
                    echo "<td>$order->billing_email</td>";
                    echo "<td>$order->date_created_gmt</td>";
                    echo "</tr>";
                }

            ?>      
        </tbody>
        <tfoot>
            <tr>
                <th>Id</th>
                <th>Estado</th>
                <th>Total</th>
                <th>Company</th>
                <th>Cliente</th>
                <th>Email</th>
                <th>Fecha</th>
            </tr>
        </tfoot>
    </table>

    <script type="text/javascript">
        new DataTable('#credit_orders_table');
    </script>
</div>