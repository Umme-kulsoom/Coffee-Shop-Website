<?php
session_start();
include "isConnected.php";

include_once './CnxDB.php';
include_once './UserRepository.php';

if (!isset($_GET['order_id'])) {
    header("Location: ./home.php");
    exit;
}

$order_id = $_GET['order_id'];
$db = CnxDB::getInstance();

// Get order details
$stmt = $db->prepare("SELECT c.commande_id, c.commande_date, c.price, u.users_uid, u.users_email FROM commande c JOIN users u ON c.users_id = u.users_id WHERE c.commande_id = ?");
$stmt->execute(array($order_id));
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header("Location: ./home.php");
    exit;
}

// Get order items
$stmt = $db->prepare("SELECT ci.product_name, ci.product_price FROM commande_item ci WHERE ci.commande_id = ?");
$stmt->execute(array($order_id));
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

include_once './CartRepository.php';
$cartrepo = new CartRepository();
if(isset($_COOKIE["user_id"])&&($cartrepo->findNProductsById(intval($_COOKIE["user_id"]))>0)){
    $nproducts = $cartrepo->findNProductsById(intval($_COOKIE["user_id"]));
} else {
    $nproducts=0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - Coffee Shop</title>
    <link rel="stylesheet" href="./styles/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Poppins', sans-serif;
        }
        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #8B4513;
            padding-bottom: 20px;
        }
        .receipt-header h1 {
            color: #8B4513;
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .order-info {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
        }
        .info-item label {
            font-weight: 600;
            color: #8B4513;
            margin-bottom: 5px;
        }
        .info-item span {
            color: #333;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table thead {
            background-color: #8B4513;
            color: white;
        }
        .items-table th {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .items-table td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total-section {
            text-align: right;
            border-top: 2px solid #8B4513;
            padding-top: 15px;
            margin-top: 15px;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #8B4513;
            margin-top: 10px;
        }
        .button-container {
            text-align: center;
            margin-top: 30px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        .btn {
            padding: 12px 30px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: 0.3s;
        }
        .btn-home {
            background-color: #8B4513;
            color: white;
        }
        .btn-home:hover {
            background-color: #704010;
        }
        .btn-print {
            background-color: #666;
            color: white;
        }
        .btn-print:hover {
            background-color: #555;
        }
        @media print {
            .button-container {
                display: none;
            }
            body {
                background-color: white;
            }
        }
    </style>
</head>
<body>

<header>
    <a href="./home.php" class="logo">
        <img src="images/logo.png" alt="">
    </a>
    <i class="fas fa-bars" id="menu-icon"></i>
    <ul class="navbar">
        <li id="home"><a href="./home.php">Home</a></li>
        <li><a href="./home.php #products">Products</a></li>
        <li id="reviews"><a href="./home.php #customers">Reviews</a></li>
        <li><a href="./kyufi game.php">PLAY</a></li>
        <li id="join"><a href="./joinus.php">JOIN US</a></li>
        <li id="abouticon"><a href="./home.php #about">About</a></li>
    </ul>
    <div class="header-icons">
        <button id="shopping"><i class="fas fa-shopping-cart" id="cart-btn"></i><span id="cart-count"> <?php echo $nproducts ?> </span></button>
        <button id="search-btn"><i class="fas fa-search"></i></button>
        <input id="search-input" onkeyup="search()" type="text" placeholder="Search drinks, stores...">
        <button id="lang"><i class="fas fa-globe"></i></button>
        <a href=<?php if(!(isset($_COOKIE['user_uid']))||!(isset($_COOKIE['user_id']))){echo "./login.php";} else{echo "account.php";} ?>><button class="header-btn"><i class="fa-solid fa-user"></i></button></a>
        <?php if((isset($_COOKIE['user_uid']))&&(isset($_COOKIE['user_id']))){?>
            <a href="./logout.inc.php"><button class="header-btn"><i class="fa fa-sign-out"></i></button></a>
        <?php }?>
    </div>
</header>

<div class="receipt-container">
    <div class="receipt-header">
        <h1>🎉 Order Confirmed!</h1>
        <p>Thank you for your order at KYUFI Coffee Shop</p>
    </div>

    <div class="order-info">
        <div class="info-item">
            <label>Order ID:</label>
            <span><?php echo $order['commande_id']; ?></span>
        </div>
        <div class="info-item">
            <label>Order Date:</label>
            <span><?php echo date('d/m/Y', strtotime($order['commande_date'])); ?></span>
        </div>
        <div class="info-item">
            <label>Customer:</label>
            <span><?php echo $order['users_uid']; ?></span>
        </div>
        <div class="info-item">
            <label>Email:</label>
            <span><?php echo $order['users_email']; ?></span>
        </div>
    </div>

    <h3 style="color: #8B4513; margin-top: 30px;">Order Items:</h3>
    
    <table class="items-table">
        <thead>
            <tr>
                <th>Product Name</th>
                <th style="text-align: right;">Price</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($items) > 0): ?>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td style="text-align: right;">$<?php echo number_format($item['product_price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="2" style="text-align: center; padding: 20px;">No items in this order</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="total-section">
        <p style="margin: 0; color: #666;">TOTAL AMOUNT:</p>
        <div class="total-amount">$<?php echo number_format($order['price'], 2); ?></div>
    </div>

    <div class="button-container">
        <button class="btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> Print Receipt</button>
        <a href="./home.php" class="btn btn-home">Back to Home</a>
    </div>
</div>

<script>
    const cartButton = document.querySelector('#shopping');
    cartButton.addEventListener('click', () => {
        window.location.href = 'commander.php';
    });

    let globe = document.getElementById('lang');
    globe.addEventListener('click', function(){
        window.location.href = "./traduction.php";
    });

    let searchBtn = document.getElementById('search-btn');
    let searchInput = document.getElementById('search-input');
    let menuIcon = document.getElementById('menu-icon');
    let navbar = document.querySelector('.navbar');

    searchBtn.addEventListener('click', function() {
        searchInput.style.display = (searchInput.style.display === 'none' || searchInput.style.display === '') ? 'block' : 'none';
    });

    menuIcon.addEventListener('click', function() {
        navbar.classList.toggle('active');
    });
</script>

</body>
</html>
