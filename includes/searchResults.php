<html>
<head>
    <title>
        Book Shop
    </title>
    <style>
        th, td {
            padding: 7px 9px;
        }

        tfoot td{
            font-weight: bold;
        }

        input#search{
            width: 20%;
            padding: 10px;
        }

    </style>
</head>
<body>
<form method="get" action="">
    <label>Search from Sales:</label>
    <input name="search" type="text" id="search" value="<?= $_GET['search'] ?? '' ?>" placeholder="Search for customer, product, price" width="200px">
</form>
<table>
    <thead>
    <tr>
        <th>Customer Name</th>
        <th>Customer Email</th>
        <th>Sale Id</th>
        <th>Sale Date</th>
        <th >Product Name</th>
        <th>Product Price</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total = 0;
    foreach($results as $row) {
        $total += $row['product_price'];
        ?>

        <tr>
            <td><?= $row['customer_name'] ?></td>
            <td><?= $row['customer_email'] ?></td>
            <td><?= $row['sale_id'] ?></td>
            <td><?= $row['sale_date'] ?></td>
            <td><?= $row['product_name'] ?></td>
            <td><?= $row['product_price'] ?></td>
        </tr>
    <?php } ?>
    </tbody>
    <tfoot>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td style="float: right;">Total Price</td>
        <td><?= $total ?></td>
    </tr>
    </tfoot>
</table>

<div>
    <a href="?import">Import/Refresh Data</a>
</div>
</body>
</html>