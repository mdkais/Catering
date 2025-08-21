<?php
session_start();

// Connect to the MySQL database (replace with your actual database credentials)
$host = "localhost";
$dbname = "kitchen";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Add products to the cart when the form is submitted
if (isset($_POST['add_to_cart'])) {
    if (isset($_POST['products']) && is_array($_POST['products'])) {
        foreach ($_POST['products'] as $product_id) {
            // Fetch product details from the database
            $stmt = $pdo->prepare("SELECT id, name, price FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                // Add product to the cart
                $_SESSION['cart'][] = $product;
            }
        }
    }
}

// Remove products from the cart
if (isset($_POST['remove_from_cart'])) {
    if (isset($_POST['products']) && is_array($_POST['products'])) {
        foreach ($_POST['products'] as $index) {
            if (isset($_SESSION['cart'][$index])) {
                unset($_SESSION['cart'][$index]);
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex the array
    }
}

// Handle order placement logic
$orderPlaced = false;
if (isset($_POST['place_order'])) {
    // Calculate the total and insert order into the database (you need to implement this)
    $total = calculateTotal($_SESSION['cart']); // Implement this function

    // Insert order details into the database
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $stmt->execute([1, $total]); // Replace 1 with the actual user ID

    $order_id = $pdo->lastInsertId(); // Get the ID of the newly inserted order

    foreach ($_SESSION['cart'] as $product) {
        // Insert order items into the database
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $product['id'], 1, $product['price']]); // Assuming quantity is 1 per product
    }

    // Clear the cart
    $_SESSION['cart'] = array();
    $orderPlaced = true; // Set the flag to display the order placed message
}

function calculateTotal($cart)
{
    $total = 0;
    foreach ($cart as $product) {
        $total += $product['price'];
    }
    return $total;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .logo {
            width: 150px;
            height: 150px;
            cursor: pointer;
            padding-left: 130px;
        }

        .container {
            display: flex;
            justify-content: space-between;
            margin: 20px;
            padding: 20px;
            font-weight: bolder;
        }

        .product-catalog {
            flex: 1;
            background-color: #333;
            padding: 20px;
        }

        .shopping-cart {
            flex: 1;
            background-color: #444;
            padding: 20px;
            height: 500px;
        }

        h1 {
            color: #f7c705;
            text-align: center;
            font-family: blacksword;
        }

        .product {
            margin-bottom: 10px;
        }

        .button {
            font-weight: bolder;
            background-color: black;
            border: 20px white;
            color: #f7c705;
            width: 350px;
            height: 50px;
            transition-duration: 0.4s;
            box-shadow: 3px 3px 3px 4px #5f5e5d;
        }

        .button:hover {
            background-color: #f7c705;
            color: black;
        }

        .order-placed {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
        }

        h2 {
            font-family: sans-serif;
            font-weight: bolder;
            color: #f7c705;
        }

        .container-a {
            width: 100%;
            min-height: 1%;
            background-color: #f7c705;
            justify-content: center;
            align-items: center;
        }

        .contentsec {
            display: flex;
            padding: 100px;
            padding-top: 0%;
        }

        .contentsec .card {
            box-shadow: 3px 4px 3px 2px #5f5e5d;
            margin: 75px;
            height: 400px;
            background-color: #000;
        }

        img {
            width: 300px;
            height: 300px;
        }

        p {
            font-family: sans-serif;
            font-weight: bolder;
            font-size: 25px;
            padding-top: 1%;
            color: #f7c705;
            text-align: center;
        }
    </style>
</head>

<body>
    <img src="Logo2.png" class="logo">
    <button type="button" onclick="document.location='index.html'" class="button" style="position: absolute;top: 90px;right: 40px;">Log out</button>
    <div class="container">
        <div class="product-catalog">
            <h1>Product Catalog</h1>
            <form method="post" action="">
                <?php
                // Display products from the database
                $stmt = $pdo->prepare("SELECT id, name, price FROM products");
                $stmt->execute();
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($products as $product) : ?>
                    <div class="product">
                        <label for="product<?php echo $product['id']; ?>">
                            <?php echo $product['name'] . ' - ₹ ' . $product['price']; ?>
                        </label>
                        <input type="checkbox" name="products[]" id="product<?php echo $product['id']; ?>" value="<?php echo $product['id']; ?>">
                    </div>
                <?php endforeach; ?>
                <input type="submit" class="button" name="add_to_cart" value="Add to Cart">
            </form>
        </div>

        <div class="shopping-cart">
            <h1>Shopping Cart</h1>
            <form method="post" action="">
                <ul>
                    <?php
                    // Display cart contents
                    foreach ($_SESSION['cart'] as $index => $product) : ?>
                        <li>
                            <input type="checkbox" name="products[]" value="<?php echo $index; ?>">
                            <?php echo $product['name'] . ' - ' . $product['price']; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <input type="submit" class="button" name="remove_from_cart" value="Remove from Cart" style="box-shadow: 3px 3px 3px 4px #333;"><br><br>
                <input type="submit" class="button" name="place_order" value="Place Order" style="box-shadow: 3px 3px 3px 4px #333;">
            </form><br><br><br>
        </div>
    </div>

    <!-- New form for placing the order -->
    <div class="order-placed">
        <?php
        if ($orderPlaced) {
            echo "<h2>Your order has been placed. Thank you!<h2>";
        }
        ?>
    </div>

    <div class="container-a">
        <p style="padding-top: 30px;color:#000;font-size:50px;font-family:blacksword;">Order now for a delicious treat!!</p><br>
        <div class="contentsec">
            <div class="card" style="box-shadow: 2px 2px 2px 4px #5f5e5d;">
                <img src="biryani.jpg" class="image">
                <p>
                    CHICKEN BIRYANI <br>
                    ₹ 800
                </p>
            </div>
            <div class="card" style="box-shadow: 2px 2px 2px 4px #5f5e5d;">
                <img src="gravy.png" class="image">
                <p>
                    MUTTON GRAVY <br>
                    ₹ 700
                </p>
            </div>
            <div class="card" style="box-shadow: 2px 2px 2px 4px #5f5e5d;">
                <img src="paneer.jpg" class="image">
                <p>
                    PANEER GRAVY<br>
                    ₹ 600
                </p>
            </div>
        </div>
    </div>
</body>

</html>