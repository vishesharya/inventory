<?php
session_start();
include_once 'include/connection.php';
include_once 'include/admin-main.php';

// Default value for Labour Name
$labour_name = isset($_POST['labour_name']) ? $_POST['labour_name'] : "";


// Logic to fetch product names from the database
$product_query = "SELECT DISTINCT product_name FROM kits_product ORDER BY product_name ASC";
$product_result = mysqli_query($con, $product_query);

// Logic to fetch product bases and colors based on selected product
$selected_product = isset($_POST['product_name']) ? $_POST['product_name'] : null;
if ($selected_product) {
    $product_base_query = "SELECT DISTINCT product_base FROM kits_product WHERE product_name = '$selected_product' ORDER BY product_base ASC";
    $product_color_query = "SELECT DISTINCT product_color FROM kits_product WHERE product_name = '$selected_product' ORDER BY product_color ASC";
    $product_base_result = mysqli_query($con, $product_base_query);
    $product_color_result = mysqli_query($con, $product_color_query);
}

// Function to fetch current number from the database
function getCurrentNumber($con) {
    $result = mysqli_query($con, "SELECT kits_received_temp FROM challan_temp LIMIT 1");
    $row = mysqli_fetch_assoc($result);
    return $row['kits_received_temp'];
}

// Function to update the current number in the database
function updateCurrentNumber($con, $newNumber) {
    mysqli_query($con, "UPDATE challan_temp SET kits_received_temp = $newNumber");
}

// Function to generate the code prefix
function generateCodePrefix($number) {
    return "KSI-KR-" . $number;
}

// Function to generate the Challan number
function generateChallanNumber($con) {
    $currentNumber = getCurrentNumber($con);
    $codePrefix = generateCodePrefix($currentNumber);
    // Increment current number for the next time
    updateCurrentNumber($con, $currentNumber + 1);
    return $codePrefix;
}


function viewChallanNumber($con) {
    $currentNumber = getCurrentNumber($con);
    $codePrefix = generateCodePrefix($currentNumber);
    return $codePrefix;
}

$challan_no = viewChallanNumber($con); 

$errors = array();

if (isset($_POST['add_product'])) {
    // Validate input
    if (empty($_POST['product_name']) || empty($_POST['product_base']) || empty($_POST['product_color']) || empty($_POST['quantity'])) {
        $errors[] = "Please fill in all fields.";
    } else {
        // Sanitize input
        $labour_name = isset($_POST['labour_name']) ? mysqli_real_escape_string($con, $_POST['labour_name']) : "";
        $product_name = mysqli_real_escape_string($con, $_POST['product_name']);
        $product_base = mysqli_real_escape_string($con, $_POST['product_base']);
        $product_color = mysqli_real_escape_string($con, $_POST['product_color']);
        $quantity = mysqli_real_escape_string($con, $_POST['quantity']);

        // Fetch remaining_quantity from kits_product table
        $remaining_quantity_query = "SELECT remaining_quantity FROM kits_product WHERE product_name = '$product_name' AND product_base = '$product_base' AND product_color = '$product_color'";
        $remaining_quantity_result = mysqli_query($con, $remaining_quantity_query);
        $row = mysqli_fetch_assoc($remaining_quantity_result);
        $remaining_quantity = $row['remaining_quantity'];

        // Calculate total
        $total = $remaining_quantity + $quantity;

        // Update remaining_quantity in kits_product table
        $updated_remaining_quantity = $remaining_quantity + $quantity;
        $update_remaining_quantity_query = "UPDATE kits_product SET remaining_quantity = $updated_remaining_quantity WHERE product_name = '$product_name' AND product_base = '$product_base' AND product_color = '$product_color'";
        mysqli_query($con, $update_remaining_quantity_query);

        // Insert data into temporary session storage
        $temp_product = array(
            'challan_no' => $challan_no,
            'labour_name' => $labour_name,
            'product_name' => $product_name,
            'product_base' => $product_base,
            'product_color' => $product_color,
            'received_quantity' => $quantity,
            'total' => $total
        );
        $_SESSION['temp_products'][] = $temp_product;
    }
}

// Check if delete button is clicked
if (isset($_POST['delete_product'])) {
    // Get the index of the product to be deleted
    $delete_index = $_POST['delete_index'];

    // Get the product details to update remaining_quantity in kits_product table
    $deleted_product = $_SESSION['temp_products'][$delete_index];
    $product_name = mysqli_real_escape_string($con, $deleted_product['product_name']);
    $product_base = mysqli_real_escape_string($con, $deleted_product['product_base']);
    $product_color = mysqli_real_escape_string($con, $deleted_product['product_color']);
    $deleted_quantity = mysqli_real_escape_string($con, $deleted_product['received_quantity']);
    $deleted_total = mysqli_real_escape_string($con, $deleted_product['total']);

    // Fetch remaining_quantity from kits_product table
    $remaining_quantity_query = "SELECT remaining_quantity FROM kits_product WHERE product_name = '$product_name' AND product_base = '$product_base' AND product_color = '$product_color'";
    $remaining_quantity_result = mysqli_query($con, $remaining_quantity_query);
    $row = mysqli_fetch_assoc($remaining_quantity_result);
    $remaining_quantity = $row['remaining_quantity'];

    // Calculate updated remaining_quantity
    $updated_remaining_quantity = $remaining_quantity - $deleted_quantity;

    // Update remaining_quantity in kits_product table
    $update_remaining_quantity_query = "UPDATE kits_product SET remaining_quantity = $updated_remaining_quantity WHERE product_name = '$product_name' AND product_base = '$product_base' AND product_color = '$product_color'";
    mysqli_query($con, $update_remaining_quantity_query);

    // Update the total in the session data
    $_SESSION['temp_products'][$delete_index]['total'] -= $deleted_total;

    // Remove the product from the session
    unset($_SESSION['temp_products'][$delete_index]);

    // Reset array keys to maintain consecutive numbering
    $_SESSION['temp_products'] = array_values($_SESSION['temp_products']);
}

// Store added products in the database when "Submit" button is clicked
if (isset($_POST['submit_products'])) {
    $temp_products = isset($_SESSION['temp_products']) ? $_SESSION['temp_products'] : [];

    if (empty($temp_products)) {
        $errors[] = "Please add at least one product.";
    } else {
        foreach ($temp_products as $product) {
            $challan_no = mysqli_real_escape_string($con, $product['challan_no']);
            $labour_name = mysqli_real_escape_string($con, $product['labour_name']);
            $product_name = mysqli_real_escape_string($con, $product['product_name']);
            $product_base = mysqli_real_escape_string($con, $product['product_base']);
            $product_color = mysqli_real_escape_string($con, $product['product_color']);
            $quantity = mysqli_real_escape_string($con, $product['received_quantity']);
            $total = mysqli_real_escape_string($con, $product['total']);

            // Insert product into the database
            $insert_query = "INSERT INTO kits_received (challan_no, labour_name, product_name, product_base, product_color, received_quantity, total) 
                            VALUES ('$challan_no', '$labour_name', '$product_name', '$product_base', '$product_color', '$quantity', '$total')";
            $insert_result = mysqli_query($con, $insert_query);

            if (!$insert_result) {
                $errors[] = "Failed to store data in the database.";
            }
        }

        // If no errors, update the Challan Number and clear session storage
        if (empty($errors)) {
            // Update Challan Number
            $challan_no = generateChallanNumber($con);
            
            // Clear session storage after insertion
            unset($_SESSION['temp_products']);

            // Redirect to the same page to prevent form resubmission
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kits Received</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fc;
            font-family: Arial, sans-serif;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .btn-group {
            margin-top: 1.5rem;
            justify-content: center;
        }
        .table {
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="h4 text-center mb-4">Kits Received</h1>
                        <?php if (!empty($errors)) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?php foreach ($errors as $error) : ?>
                                    <?php echo $error; ?><br>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <form method="post" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="challan_no">Challan No:</label>
                                        <input type="text" class="form-control" id="challan_no" name="challan_no" value="<?php echo $challan_no; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                      <label for="labour_name">Labour Name:</label>
                                         <select class="form-select" id="labour_name" name="labour_name">
                                            <option value="" selected disabled>Select Labour</option>
                                            <?php while ($row = mysqli_fetch_assoc($labour_result)) : ?>
                                            <option value="<?php echo $row['labour_name']; ?>"><?php echo $row['labour_name']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_name">Select Product:</label>
                                        <select class="form-select" id="product_name" name="product_name" onchange="this.form.submit()">
                                            <option value="" selected disabled>Select Product</option>
                                            <?php while ($row = mysqli_fetch_assoc($product_result)) : ?>
                                                <option value="<?php echo $row['product_name']; ?>" <?php echo $selected_product == $row['product_name'] ? 'selected' : ''; ?>><?php echo $row['product_name']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_base">Product Base:</label>
                                        <select class="form-select" id="product_base" name="product_base">
                                            <option value="" selected disabled>Select Product Base</option>
                                            <?php if ($selected_product) : ?>
                                                <?php while ($row = mysqli_fetch_assoc($product_base_result)) : ?>
                                                    <option value="<?php echo $row['product_base']; ?>"><?php echo $row['product_base']; ?></option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="product_color">Product Color:</label>
                                        <select class="form-select" id="product_color" name="product_color">
                                            <option value="" selected disabled>Select Product Color</option>
                                            <?php if ($selected_product) : ?>
                                                <?php while ($row = mysqli_fetch_assoc($product_color_result)) : ?>
                                                    <option value="<?php echo $row['product_color']; ?>"><?php echo $row['product_color']; ?></option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="quantity">Quantity:</label>
                                        <input type="number" class="form-control" id="quantity" name="quantity" placeholder="Enter Quantity">
                                    </div>
                                </div>
                            </div>
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary me-2" name="add_product">Add</button>
                                <button type="submit" class="btn btn-success" name="submit_products">Submit</button>
                            </div>
                        </form>
                        <hr>
                        <div class="added-products">
                            <h2 class="text-center mb-3">Added Products:</h2>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Challan No</th>
                                            <th>Labour Name</th>
                                            <th>Product Name</th>
                                            <th>Product Base</th>
                                            <th>Product Color</th>
                                            <th>Quantity</th>
                                            
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($_SESSION['temp_products'])) : ?>
                                            <?php foreach ($_SESSION['temp_products'] as $key => $product) : ?>
                                                <tr>
                                                    <td><?php echo $product['challan_no']; ?></td>
                                                    <td><?php echo $product['labour_name']; ?></td>
                                                    <td><?php echo $product['product_name']; ?></td>
                                                    <td><?php echo $product['product_base']; ?></td>
                                                    <td><?php echo $product['product_color']; ?></td>
                                                    <td><?php echo $product['received_quantity']; ?></td>
                                                   
                                                    <td>
                                                        <form method="post" action="">
                                                            <input type="hidden" name="delete_index" value="<?php echo $key; ?>">
                                                            <button type="submit" class="btn btn-danger" name="delete_product">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    // Function to update product colors based on selected product name and base
    function updateProductColors() {
        var productName = document.getElementById('product_name').value;
        var productBase = document.getElementById('product_base').value;

        // Make an AJAX request to fetch product colors based on product name and base
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState === 4 && this.status === 200) {
                var colors = JSON.parse(this.responseText);
                var productColorSelect = document.getElementById('product_color');
                // Clear existing options
                productColorSelect.innerHTML = '<option value="" selected disabled>Select Product Color</option>';
                // Add fetched colors as options
                colors.forEach(function(color) {
                    var option = document.createElement('option');
                    option.value = color;
                    option.text = color;
                    productColorSelect.appendChild(option);
                });
            }
        };
        xhr.open('GET', 'kits_issue_product_color.php?product_name=' + productName + '&product_base=' + productBase, true);
        xhr.send();
    }

    // Event listeners for product name and product base change
    document.getElementById('product_name').addEventListener('change', updateProductColors);
    document.getElementById('product_base').addEventListener('change', updateProductColors);
</script>
</html>


