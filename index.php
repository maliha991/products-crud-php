<?php

// Making the connection with the database

$pdo = new PDO("mysql:host=localhost;port=3306;dbname=products_crud", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$search = $_GET["search"] ?? null;

if ($search) {
    $statement = $pdo->prepare("SELECT * FROM products WHERE title LIKE :title ORDER BY create_date DESC");
    $statement->bindValue(":title", "%$search%");  // % requires for like to search in middle of the title
} else {
    $statement = $pdo->prepare("SELECT * FROM products ORDER BY create_date DESC");
}

// Putting the query statement inside the database
$statement->execute();

// Fetching the data from the database "product" table and storing in an associative array
$products = $statement->fetchAll(PDO::FETCH_ASSOC);

// Printing the each product as an associative array
// echo "<pre>";
// var_dump($products["image"]);
// echo "</pre>";

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="./style.css">
    <title>Products CRUD</title>
</head>

<body>
    <h1>Products CRUD</h1>

    <p>
        <a href="./create.php" class="btn btn-success">Create Product</a>
    </p>

    <form>
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Search for products" name="search" value="<?php echo $search ?>">
            <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Search</button>
        </div>
    </form>

    <table class="table">
        <thead>
            <tr>
                <th scope="col">Index</th>
                <th scope="col">Image</th>
                <th scope="col">Title</th>
                <th scope="col">Price</th>
                <th scope="col">Create Date</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $i => $product) : ?>
                <tr>
                    <th scope="row"> <?php echo $i + 1 ?> </th>
                    <td><img src="<?php echo $product["image"] ?>" class="product-image"></td>
                    <td> <?php echo $product["title"] ?> </td>
                    <td> <?php echo $product["price"] ?> </td>
                    <td> <?php echo $product["create_date"] ?> </td>
                    <td>
                        <a href="./update.php?id=<?php echo $product["id"] ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                        <form action="./delete.php" method="post" style="display: inline-block;">
                            <input type="hidden" name="id" value="<?php echo $product["id"] ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>