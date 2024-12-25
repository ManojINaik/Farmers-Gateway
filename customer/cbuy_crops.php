<?php
include ('csession.php');
include ('../sql.php');
include ('cart_init.php');

ini_set('memory_limit', '-1');

if(!isset($_SESSION['customer_login_user'])){
    header("location: ../index.php");
}

// Initialize cart
initializeCart($conn);

$query4 = "SELECT * from custlogin where email=?";
$stmt = $conn->prepare($query4);
$stmt->bind_param("s", $user_check);
$stmt->execute();
$result = $stmt->get_result();
$row4 = $result->fetch_assoc();
$para1 = $row4['cust_id'];
$para2 = $row4['cust_name'];

// Handle status messages
$status_message = '';
$status_type = '';
if(isset($_GET['status'])) {
    switch($_GET['status']) {
        case 'success':
            $status_message = 'Item added to cart successfully!';
            $status_type = 'success';
            break;
        case 'exists':
            $status_message = 'This item is already in your cart.';
            $status_type = 'warning';
            break;
        case 'insufficient':
            $status_message = 'Sorry, this item is out of stock or has insufficient quantity.';
            $status_type = 'error';
            break;
        case 'removed':
            $status_message = 'Item removed from cart.';
            $status_type = 'success';
            break;
    }
}

// Initialize shopping cart session if not exists
if(!isset($_SESSION["shopping_cart"])) {
    $_SESSION["shopping_cart"] = array();
}

// Handle cart item removal
if(isset($_GET["action"]) && $_GET["action"] == "delete" && isset($_GET["id"])) {
    foreach($_SESSION["shopping_cart"] as $keys => $values) {
        if($values["item_id"] == $_GET["id"]) {
            // Return quantity to stock
            $update_stock = "UPDATE farmer_crops_trade 
                           SET Crop_quantity = Crop_quantity + ? 
                           WHERE farmer_fkid = ? 
                           AND LOWER(TRIM(Trade_crop)) = LOWER(TRIM(?))";
            $stmt = $conn->prepare($update_stock);
            $stmt->bind_param("dis", $values["item_quantity"], $values["farmer_id"], $values["item_name"]);
            
            if($stmt->execute()) {
                unset($_SESSION["shopping_cart"][$keys]);
                $_SESSION['success_message'] = "Item removed from cart successfully";
            } else {
                $_SESSION['error_message'] = "Failed to remove item from cart";
            }
            break;
        }
    }
    // Reindex the array
    $_SESSION["shopping_cart"] = array_values($_SESSION["shopping_cart"]);
    header("Location: cbuy_crops.php");
    exit();
}

// Handle adding items to cart
if(isset($_POST["add_to_cart"])) {
    $farmer_id = $_POST["farmer_id"];
    $crop_name = $_POST["crop_name"];
    $quantity = floatval($_POST["quantity"]);
    $price = floatval($_POST["price"]);
    
    try {
        // Check stock availability
        $stock_query = "SELECT Crop_quantity FROM farmer_crops_trade 
                       WHERE farmer_fkid = ? 
                       AND LOWER(TRIM(Trade_crop)) = LOWER(TRIM(?))
                       AND Crop_quantity >= ?
                       FOR UPDATE";
        $stmt = $conn->prepare($stock_query);
        $stmt->bind_param("isd", $farmer_id, $crop_name, $quantity);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows == 0) {
            throw new Exception("Not enough stock available");
        }
        
        $was_added = false;
        
        // Check if product exists in cart
        if(!empty($_SESSION["shopping_cart"])) {
            foreach($_SESSION["shopping_cart"] as $keys => $values) {
                if($values["farmer_id"] == $farmer_id && $values["item_name"] == $crop_name) {
                    $new_quantity = $values["item_quantity"] + $quantity;
                    
                    // Check if new quantity exceeds stock
                    $stock_check = "SELECT Crop_quantity FROM farmer_crops_trade 
                                  WHERE farmer_fkid = ? 
                                  AND LOWER(TRIM(Trade_crop)) = LOWER(TRIM(?))
                                  AND Crop_quantity >= ?";
                    $stmt = $conn->prepare($stock_check);
                    $stmt->bind_param("isd", $farmer_id, $crop_name, $new_quantity);
                    $stmt->execute();
                    
                    if($stmt->get_result()->num_rows == 0) {
                        throw new Exception("Total quantity exceeds available stock");
                    }
                    
                    $_SESSION["shopping_cart"][$keys]["item_quantity"] = $new_quantity;
                    $_SESSION["shopping_cart"][$keys]["total_price"] = $new_quantity * $price;
                    $was_added = true;
                    break;
                }
            }
        }
        
        if(!$was_added) {
            $item_array = array(
                'item_id' => uniqid(),
                'farmer_id' => $farmer_id,
                'item_name' => $crop_name,
                'item_price' => $price,
                'item_quantity' => $quantity,
                'total_price' => $quantity * $price
            );
            $_SESSION["shopping_cart"][] = $item_array;
        }
        
        // Update stock
        $update_stock = "UPDATE farmer_crops_trade 
                        SET Crop_quantity = Crop_quantity - ? 
                        WHERE farmer_fkid = ? 
                        AND LOWER(TRIM(Trade_crop)) = LOWER(TRIM(?))
                        AND Crop_quantity >= ?";
        $stmt = $conn->prepare($update_stock);
        $stmt->bind_param("disd", $quantity, $farmer_id, $crop_name, $quantity);
        
        if(!$stmt->execute()) {
            throw new Exception("Failed to update stock");
        }
        
        $_SESSION['success_message'] = "Item added to cart successfully";
        
    } catch(Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
    
    header("Location: cbuy_crops.php");
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Buy Crops - Agriculture Portal</title>

    <!-- jQuery first, then other libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.6.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <?php include ('cheader.php'); ?>
    
    <style>
        body {
            background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .hero-section {
            padding: 2rem 0;
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .search-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transform: perspective(1000px) rotateX(0deg);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .search-container:hover {
            transform: perspective(1000px) rotateX(2deg);
            box-shadow: 0 12px 40px rgba(31, 38, 135, 0.25);
        }

        .search-input {
            width: 100%;
            padding: 1.2rem 1.8rem;
            font-size: 1.1rem;
            border: none;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            background: #fff;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1), 
                       0 0 0 3px rgba(67, 206, 162, 0.2);
            transform: translateY(-2px);
        }

        .filter-container {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 1.8rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.8rem 1.8rem;
            border: none;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .filter-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .filter-btn.active {
            background: #fff;
            color: #185a9d;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .farmer-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            padding: 2rem;
            margin-top: 2rem;
            animation: cardsAppear 0.6s ease-out;
        }

        @keyframes cardsAppear {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .farmer-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.18);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        .farmer-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #43cea2, #185a9d);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .farmer-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 48px rgba(31, 38, 135, 0.2);
        }

        .farmer-card:hover::before {
            opacity: 1;
        }

        .farmer-info {
            display: flex;
            align-items: center;
            gap: 1.8rem;
            margin-bottom: 2rem;
        }

        .farmer-avatar {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #fff;
            text-transform: uppercase;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .farmer-card:hover .farmer-avatar {
            transform: scale(1.05);
        }

        .farmer-details h3 {
            margin: 0;
            color: #185a9d;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .farmer-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 1.1rem;
        }

        .crop-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .crop-item:hover {
            background: rgba(67, 206, 162, 0.05);
            padding-left: 1rem;
            padding-right: 1rem;
            margin: 0 -1rem;
        }

        .crop-item:last-child {
            border-bottom: none;
        }

        .crop-item span:first-child {
            color: #185a9d;
            font-weight: 500;
        }

        .crop-item span:last-child {
            color: #43cea2;
            font-weight: 600;
        }

        .view-crops-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            width: 100%;
            padding: 1.2rem;
            margin-top: 1.8rem;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #43cea2 0%, #185a9d 100%);
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(67, 206, 162, 0.2);
            position: relative;
            overflow: hidden;
        }

        .view-crops-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 50%);
            transform: translateX(-100%) rotate(45deg);
            transition: transform 0.6s ease;
        }

        .view-crops-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 206, 162, 0.3);
        }

        .view-crops-btn:hover::before {
            transform: translateX(100%) rotate(45deg);
        }

        .view-crops-btn i {
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }

        .view-crops-btn:hover i {
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .search-container {
                margin: 0 1rem;
                padding: 1.5rem;
            }

            .farmer-cards-container {
                grid-template-columns: 1fr;
                padding: 1rem;
            }

            .farmer-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
  
<?php include ('cnav.php');  ?>
 	
<div class="hero-section">
    <div class="search-container">
        <input type="text" id="searchInput" class="search-input" placeholder="Search by crop name, location, or farmer...">
        <div class="filter-container">
            <button class="filter-btn active" data-filter="all">ALL</button>
            <button class="filter-btn" data-filter="crop">CROP NAME</button>
            <button class="filter-btn" data-filter="location">LOCATION</button>
            <button class="filter-btn" data-filter="farmer">FARMER NAME</button>
        </div>
    </div>
</div>

<div class="farmer-cards-container" id="cropContainer">
    <?php
    $sql = "SELECT DISTINCT f.farmer_id, f.farmer_name, f.phone_no, f.F_Location,
            GROUP_CONCAT(DISTINCT fct.Trade_crop) as crops,
            GROUP_CONCAT(DISTINCT CONCAT(fct.Trade_crop, ':', fct.Crop_quantity)) as crop_details
            FROM farmerlogin f 
            INNER JOIN farmer_crops_trade fct ON f.farmer_id = fct.farmer_fkid 
            WHERE fct.Crop_quantity > 0
            GROUP BY f.farmer_id, f.farmer_name, f.phone_no, f.F_Location";
    
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $crop_details = array();
            foreach(explode(',', $row['crop_details']) as $detail) {
                list($crop, $quantity) = explode(':', $detail);
                $crop_details[$crop] = $quantity;
            }
            ?>
            <div class="farmer-card" 
                 data-farmer="<?php echo strtolower($row['farmer_name']); ?>"
                 data-location="<?php echo strtolower($row['F_Location']); ?>"
                 data-crops="<?php echo strtolower($row['crops']); ?>">
                <div class="farmer-info">
                    <div class="farmer-avatar">
                        <?php echo substr($row['farmer_name'], 0, 1); ?>
                    </div>
                    <div class="farmer-details">
                        <h3><?php echo htmlspecialchars($row['farmer_name']); ?></h3>
                        <div class="farmer-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($row['F_Location']); ?>
                        </div>
                    </div>
                </div>
                
                <?php
                foreach($crop_details as $crop => $quantity) {
                    if($quantity > 0) {
                        echo "<div class='crop-item'>
                                <span>" . htmlspecialchars($crop) . "</span>
                                <span>Available: " . htmlspecialchars($quantity) . " kg</span>
                              </div>";
                    }
                }
                ?>
                
                <a href="farmer_crops.php?farmer_id=<?php echo $row['farmer_id']; ?>" 
                   class="view-crops-btn">
                   <i class="fas fa-eye"></i>
                   View Available Crops
                </a>
            </div>
            <?php
        }
    }
    ?>
</div>

<div id="noResults" style="display: none;" class="text-center text-white mt-5">
    <i class="fas fa-search fa-3x mb-3"></i>
    <h4>No Results Found</h4>
    <p>Try adjusting your search criteria</p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const farmerCards = document.querySelectorAll('.farmer-card');
    const noResults = document.getElementById('noResults');
    const cropContainer = document.getElementById('cropContainer');
    let currentFilter = 'all';

    function filterCards() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        let hasVisibleCards = false;

        farmerCards.forEach(card => {
            const crops = card.getAttribute('data-crops');
            const location = card.getAttribute('data-location');
            const farmerName = card.getAttribute('data-farmer');
            let isVisible = false;

            if (searchTerm === '') {
                isVisible = true;
            } else {
                switch(currentFilter) {
                    case 'crop':
                        isVisible = crops && crops.split(',').some(crop => 
                            crop.trim().toLowerCase() === searchTerm
                        );
                        break;
                    case 'location':
                        isVisible = location && location.includes(searchTerm);
                        break;
                    case 'farmer':
                        isVisible = farmerName && farmerName.includes(searchTerm);
                        break;
                    default:
                        isVisible = (crops && crops.includes(searchTerm)) || 
                                  (location && location.includes(searchTerm)) || 
                                  (farmerName && farmerName.includes(searchTerm));
                }
            }

            card.style.display = isVisible ? '' : 'none';
            if (isVisible) hasVisibleCards = true;
        });

        noResults.style.display = hasVisibleCards ? 'none' : '';
        cropContainer.style.display = hasVisibleCards ? '' : 'none';
    }

    searchInput.addEventListener('input', filterCards);

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            currentFilter = button.getAttribute('data-filter');
            filterCards();
        });
    });

    filterCards();
});
</script>

    

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
    
    // Initialize DataTable with custom styling
    if ($('#cartTable tbody tr').length > 1) {
        $('#cartTable').DataTable({
            "order": [[0, "asc"]],
            "pageLength": 5,
            "lengthMenu": [[5, 10, 25, -1], [5, 10, 25, "All"]],
            "language": {
                "lengthMenu": "_MENU_ items per page",
                "zeroRecords": "Your cart is empty",
                "info": "Showing _START_ to _END_ of _TOTAL_ items",
                "infoEmpty": "No items available",
                "infoFiltered": "(filtered from _MAX_ total items)"
            },
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
        });
    }
    
    // Real-time price calculation with animation
    function updatePrice() {
        var selectedOption = $('#crops option:selected');
        var quantity = $('#quantity').val();
        var price = selectedOption.data('price');
        var total = quantity * price;
        
        if (!isNaN(total)) {
            $('#price').val('₹' + total.toFixed(2))
                      .addClass('highlight');
            setTimeout(function() {
                $('#price').removeClass('highlight');
            }, 300);
        } else {
            $('#price').val('');
        }
    }
    
    $('#crops, #quantity').on('change input', updatePrice);
    
    // Enhanced form submission with loading state
    $('#addToCartForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        
        // Validate inputs
        var selectedCrop = $('#crops').val();
        var quantity = parseFloat($('#quantity').val());
        var selectedOption = $('#crops option:selected');
        var price = selectedOption.data('price');
        var maxQuantity = parseFloat(selectedOption.data('max'));
        
        if (!selectedCrop) {
            Swal.fire({
                title: 'Error!',
                text: 'Please select a crop',
                icon: 'error'
            });
            return;
        }
        
        if (isNaN(quantity) || quantity <= 0) {
            Swal.fire({
                title: 'Error!',
                text: 'Please enter a valid quantity',
                icon: 'error'
            });
            return;
        }
        
        if (quantity > maxQuantity) {
            Swal.fire({
                title: 'Error!',
                text: 'Quantity exceeds available stock',
                icon: 'error'
            });
            return;
        }
        
        submitBtn.prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin mr-2"></i>Adding...');
        
        var formData = new FormData();
        formData.append('add_to_cart', '1');
        formData.append('crops', selectedCrop);
        formData.append('quantity', quantity);
        formData.append('price', price);
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    var result = JSON.parse(response);
                    if(result.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: result.message,
                            icon: 'success'
                        }).then(() => {
                            // Refresh the page to update cart
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: result.message,
                            icon: 'error'
                        });
                    }
                } catch(e) {
                    console.error('Parse error:', e);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An unexpected error occurred',
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to add item to cart. Please try again.',
                    icon: 'error'
                });
            },
            complete: function() {
                submitBtn.prop('disabled', false)
                        .html('<i class="fas fa-cart-plus mr-2"></i>Add to Cart');
            }
        });
    });

    // Handle delete item confirmation
    $('.delete-item').on('click', function(e) {
        e.preventDefault();
        var deleteLink = $(this).attr('href');
        
        Swal.fire({
            title: 'Remove Item?',
            text: 'Are you sure you want to remove this item from your cart?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = deleteLink;
            }
        });
    });
    
    // Add smooth scrolling
    $('a[href*="#"]').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: $($(this).attr('href')).offset().top - 100
        }, 500, 'linear');
    });
    
    $('#crops').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var price = selectedOption.data('price') || 0;
        var maxQuantity = selectedOption.data('max') || 0;
        
        $('#price').val(price.toFixed(2));
        $('#quantity').attr('max', maxQuantity);
        $('#max-qty-label').text('Max Available: ' + maxQuantity + ' kg');
    });
});
</script>

<?php include("../modern-footer.php"); ?>

<!-- Add Razorpay script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>