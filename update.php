<?php

// Making the connection with the database

$pdo = new PDO("mysql:host=localhost;port=3306;dbname=products_crud", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$id = $_GET["id"] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// Selecting the product and redering it from database

$statement = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$statement->bindValue(":id", $id);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);

$errors = [];
$title = $product["title"];
$price = $product["price"];
$description = $product["description"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $price = $_POST["price"];
    $description = $_POST["description"];

    if (!$title) {
        $errors[] = "Requires product title";
    }

    if (!$price) {
        $errors[] = "Requires product price";
    }

    if (!is_dir("images")) {
        mkdir("images");
    }

    if (empty($errors)) {
        $image = $_FILES["image"] ?? null;
        $imagepath = $product["image"];

        if ($image && $image["tmp_name"]) {

            // Deleting the previous image
            unlink($product["image"]);

            $imagepath = "images/" . randomString(8) . "/" . $image["name"];
            mkdir(dirname($imagepath));
            move_uploaded_file($image["tmp_name"], $imagepath);
        }

        $statement = $pdo->prepare("UPDATE products SET title = :title, image = :image, 
                    description = :description, price = :price WHERE id = :id");

        $statement->bindValue(":title", $title);
        $statement->bindValue(":image", $imagepath);
        $statement->bindValue(":description", $description);
        $statement->bindValue(":price", $price);
        $statement->bindValue(":id", $id);

        $statement->execute();

        // Redirecting to the index page after creating a product
        header("Location: index.php");
    }
}

function randomString($n)
{
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $string = "";

    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $string .= $characters[$index];
    }

    return $string;
}


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

    <p>
        <a href="./index.php" class="btn btn-secondary">Go Back to Products</a>
    </p>

    <h1>Update Product <?php echo $product["title"] ?></h1>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div><?php echo $error ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">

        <!-- Showing the current image of the product -->

        <?php if ($product["image"]) : ?>
            <img src="<?php echo $product["image"] ?>" class="product-image">
        <?php endif; ?>

        <div class="mb-3">
            <label class="form-label">Product Image</label>
            <br>
            <input type="file" name="image">
        </div>
        <div class="mb-3">
            <label class="form-label">Product Title</label>
            <input type="text" class="form-control" name="title" value="<?php echo $title ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Product Description</label>
            <textarea class="form-control" name="description"><?php echo $description ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Product Price</label>
            <input type="number" step="0.01" class="form-control" name="price" value="<?php echo $price ?>">
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</body>

</html>