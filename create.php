<?php

// Making the connection with the database

$pdo = new PDO("mysql:host=localhost;port=3306;dbname=products_crud", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Super Globals -> $_POST, $_GET, $_SERVER, $_FILES 

$errors = [];
$title = "";
$price = "";
$description = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $price = $_POST["price"];
    $description = $_POST["description"];
    $date = date("Y-m-d H:i:s");

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
        $imagepath = "";

        if ($image && $image["tmp_name"]) {

            $imagepath = "images/" . randomString(8) . "/" . $image["name"];
            mkdir(dirname($imagepath));
            move_uploaded_file($image["tmp_name"], $imagepath);
        }

        $statement = $pdo->prepare("INSERT INTO products (title, image, description, price, create_date)
                            VALUES(:title, :image, :description, :price, :date)");

        $statement->bindValue(":title", $title);
        $statement->bindValue(":image", $imagepath);
        $statement->bindValue(":description", $description);
        $statement->bindValue(":price", $price);
        $statement->bindValue(":date", $date);

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
    <h1>Create new Product</h1>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div><?php echo $error ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
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