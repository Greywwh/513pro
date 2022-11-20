<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>



<?php
//session_start();
require_once("dbcontroller.php");
$db_handle = new DBController();
if(!empty($_GET["action"])) {
switch($_GET["action"]) {
	case "add":
		if(!empty($_POST["quantity"])) {
			$productByCode = $db_handle->runQuery("SELECT * FROM tblproduct WHERE code='" . $_GET["code"] . "'");
			$itemArray = array($productByCode[0]["code"]=>array('name'=>$productByCode[0]["name"], 'code'=>$productByCode[0]["code"], 'quantity'=>$_POST["quantity"], 'price'=>$productByCode[0]["price"], 'image'=>$productByCode[0]["image"]));
			
			if(!empty($_SESSION["cart_item"])) {
				if(in_array($productByCode[0]["code"],array_keys($_SESSION["cart_item"]))) {
					foreach($_SESSION["cart_item"] as $k => $v) {
							if($productByCode[0]["code"] == $k) {
								if(empty($_SESSION["cart_item"][$k]["quantity"])) {
									$_SESSION["cart_item"][$k]["quantity"] = 0;
								}
								$_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
							}
					}
				} else {
					$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
				}
			} else {
				$_SESSION["cart_item"] = $itemArray;
			}
		}
	break;
	case "remove":
		if(!empty($_SESSION["cart_item"])) {
			foreach($_SESSION["cart_item"] as $k => $v) {
					if($_GET["code"] == $k)
						unset($_SESSION["cart_item"][$k]);				
					if(empty($_SESSION["cart_item"]))
						unset($_SESSION["cart_item"]);
			}
		}
	break;
	case "empty":
		unset($_SESSION["cart_item"]);
	break;	
}
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Luca’s Loaves</title>
<link href="style1.css" rel="stylesheet" type="text/css" media="all" />
<link href="style.css" rel="stylesheet" type="text/css" media="all" />
<link rel="stylesheet"
    href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

</head>
<body>
<nav class="nav">
      <div class="container">
        <img src="logo11.png"  class="logo1" alt="logo" />
        <h1 class="logo"><a href="http://localhost/pro//index.html">Luca’s Loaves</a></h1>
        <ul>
          <li><a href="index.html" >Home</a></li>
          <li><a href="aboutus.html">About us</a></li>
          <li><a href="upload.html">Careers</a></li>
          <li><a href="orderonline" class="current">Orderonline</a></li>
          <li><a href="contactus.html">Contact us</a></li>
          <li><a href="register1.php">Register</a></li>
        </ul>
      </div>
    </nav>

    <div class="hero3">
      <div class="container">
        </br>
      </br>
    </br>
  </br>
        <h1>Choose your bread</h1>
      </div>
    </div>
<div id="shopping-cart">
<div class="txt-heading">Shopping Cart</div>

<a id="btnEmpty" href="orderonline.php?action=empty">Empty Cart</a>
<?php
if(isset($_SESSION["cart_item"])){
    $total_quantity = 0;
    $total_price = 0;
?>	
<table class="tbl-cart" cellpadding="10" cellspacing="1">
<tbody>
<tr>
<th style="text-align:left;">Name</th>
<th style="text-align:left;">Code</th>
<th style="text-align:right;" width="5%">Quantity</th>
<th style="text-align:right;" width="10%">Unit Price</th>
<th style="text-align:right;" width="10%">Price</th>
<th style="text-align:center;" width="5%">Remove</th>
</tr>	
<?php		
    foreach ($_SESSION["cart_item"] as $item){
        $item_price = $item["quantity"]*$item["price"];
		?>
				<tr>
				<td><img src="<?php echo $item["image"]; ?>" class="cart-item-image" /><?php echo $item["name"]; ?></td>
				<td><?php echo $item["code"]; ?></td>
				<td style="text-align:right;"><?php echo $item["quantity"]; ?></td>
				<td  style="text-align:right;"><?php echo "$ ".$item["price"]; ?></td>
				<td  style="text-align:right;"><?php echo "$ ". number_format($item_price,2); ?></td>
				<td style="text-align:center;"><a href="orderonline.php?action=remove&code=<?php echo $item["code"]; ?>" class="btnRemoveAction"><img src="icon-delete.png" alt="Remove Item" /></a></td>
				</tr>
				<?php
				$total_quantity += $item["quantity"];
				$total_price += ($item["price"]*$item["quantity"]);
		}
		?>

<tr>
<td colspan="2" align="right">Total:</td>
<td align="right"><?php echo $total_quantity; ?></td>
<td align="right" colspan="2"><strong><?php echo "$ ".number_format($total_price, 2); ?></strong></td>
<td></td>
</tr>
</tbody>
</table>		
  <?php
} else {
?>
<div class="no-records">Your Cart is Empty</div>
<?php 
}
?>
<?php
$query = $db_handle->runQuery("SELECT * FROM tblproduct ORDER BY id ASC");
if (! empty($query)) {
    foreach ($query as $key => $value) {
        ?>  
		<?php
    }
}
?>
</div>

<div id="product-grid">
	<div class="txt-heading">Products</div>
	<?php
	$product_array = $db_handle->runQuery("SELECT * FROM tblproduct ORDER BY id ASC");
	if (!empty($product_array)) { 
		foreach($product_array as $key=>$value){
	?>
		<div class="product-item">
			<form method="post" action="orderonline.php?action=add&code=<?php echo $product_array[$key]["code"]; ?>">
			<div class="product-image"><img src="<?php echo $product_array[$key]["image"]; ?>"></div>
			<div class="product-tile-footer">
			<div class="product-title"><?php echo $product_array[$key]["name"]; ?></div>
			<a href='get-product-info.php?id=<?php echo $query 
                [$key]["id"] ; ?>' target="_blank">  click</a>

			<div class="product-price"><?php echo "$".$product_array[$key]["price"]; ?></div>
			<div class="cart-action"><input type="text" class="product-quantity" name="quantity" value="1" size="2" /><input type="submit" value="Add to Cart" class="btnAddAction" /></div>
			</div>
			
			</form>
			<button class="quick_look" data-id="<?php echo $product_array[$key]["id"] ; ?>">Quick Look</button>
		</div>
	<?php
		}
	}
	?>
</div>
</div>
	

<div id="demo-modal"></div>







<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$(".quick_look").on("click", function() {
	var product_id = $(this).data("id");
		var options = {
				modal: true,
				height: 'auto',
				width:'70%'
			};
		$('#demo-modal').load('get-product-info.php?id='+product_id).dialog(options).dialog('open');
});

$(document).ready(function() {
		$(".image").hover(function() {
			$(this).children(".quick_look").show();
		},function() {
			   $(this).children(".quick_look").hide();
		});
});
</script>

<footer  class="footer2">   

              <div class="footer-left">

                <h3><span></span></h3>
            
                <p class="footer-links">
                  <a href="#" class="link-1">Home</a>
                  
                  <a href="#">Aboutus</a>
                
                  <a href="#">Careers</a>
                
                  <a href="#">Orderonline</a>
                  
                  <a href="#">Contactus</a>
                  
                  <a href="#">Register</a>
                </p>
            
                <p class="footer-company-name"> Grey © 2022</p>
              </div>
            
              <div class="footer-center">
            
                <div>
                  <i class="fa fa-map-marker"></i>
                  <p><span>Luca's Loaves</span> 36 Garden Ave, Mullumbimby NSW 2482</p>
                </div>
            
                <div>
                  <i class="fa fa-phone"></i>
                  <p></p>
                </div>
            
                <div>
                  <i class="fa fa-envelope"></i>
                  <p><a href="mailto:support@company.com"></a></p>
                </div>
            
              </div>
            
              <div class="footer-right">
            
                <p class="footer-company-about">
                  <span>Slogan</span>
                  <p></p></br>
                  We hoped that we could let people enjoy the best bread.
                </p>
            
                <div class="footer-icons">
            
                  <a href="#"><i class="fa fa-facebook"></i></a>
                  <a href="#"><i class="fa fa-twitter"></i></a>
                  <a href="#"><i class="fa fa-linkedin"></i></a>
                  <a href="#"><i class="fa fa-github"></i></a>
            
                </div>
            
              </div>
                20ITA1 Grey Wenhao Weng
                          </footer>

		  <script src="script.js"></script>
</body>
</html>
