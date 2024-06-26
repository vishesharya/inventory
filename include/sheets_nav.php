<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Responsive Navbar with PHP</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
  <style>
    /* Custom styles for navbar */
    .navbar {
      background-color: #007bff; /* Blue background */
    }

    .navbar-brand,
    .navbar-nav .nav-link {
      color: #ffffff; /* White text */
    }

    .navbar-nav {
      margin: 0 auto; /* Center the navigation items */
    }

    .navbar-nav .dropdown-menu {
      background-color: #007bff; /* Blue background for dropdown */
      max-height: 400px; /* Set a fixed height */
      overflow-y: auto; /* Enable vertical scrolling */
      border-radius: 3px;
    }

    .navbar-nav .dropdown-menu .dropdown-item {
      color: #ffffff; /* White text for dropdown items */
    }

    .navbar-nav .dropdown-menu .dropdown-item:hover {
      background-color: #0056b3; /* Darker blue on hover */
    }

    .navbar-nav .nav-link:hover {
      color: #ffffff; /* Light text on hover */
    }

    /* Custom scrollbar styles */
    .navbar-nav .dropdown-menu::-webkit-scrollbar {
      width: 8px;
    }

    .navbar-nav .dropdown-menu::-webkit-scrollbar-track {
      background: #0056b3; /* Track color */
    }

    .navbar-nav .dropdown-menu::-webkit-scrollbar-thumb {
      background-color: #ffffff; /* Thumb color */
      border-radius: 10px;
      border: 2px solid #0056b3; /* Thumb border color */
    }

    .navbar-nav .dropdown-menu::-webkit-scrollbar-thumb:hover {
      background-color: #cccccc; /* Thumb hover color */
    }

    /* Responsive styles */
    @media (max-width: 991px) {
      .navbar-nav .nav-item {
        padding: 10px 0;
      }
    }

    .navbar-brand:hover {
      transform: scale(1.1);
      transition-duration: 400ms;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
  <div class="container">
    <a class="navbar-brand" href="sheets_inventory.php"><img width="25px" src="./assets/images/go_back.png" alt="icon"> Go Back</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav">
        <!-- PHP code for session and verification form -->
        <?php
        if(isset($_SESSION['username'])) {
          echo '
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Sheets Inventory
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="sheets_received.php">Receiving Form</a>
              <a class="dropdown-item" href="sheets_issue.php">Issue Form</a>
              <a class="dropdown-item" href="sheets_buyer.php">Selling Form</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="sheets_issue_all_slip.php">Issue Slip</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="seets_job_work.php">Job Work</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="sheets_received_data.php">Receiving Data</a>
              <a class="dropdown-item" href="sheets_issue_data.php">Issue Data</a>
              <a class="dropdown-item" href="sheets_selling_data.php">Selling Data</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="sheets_product_list_for_production.php">Production Inventory</a>
              <a class="dropdown-item" href="sheets_product_list.php">Packaging Inventory</a>
              <a class="dropdown-item" href="sheets_color_panel_production_inventory.php">Production Color Panel</a>
              <a class="dropdown-item" href="sheets_color_panel_inventory.php">Packaging Color Panel</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="all_small_stock-update.php">Update Color Panel</a>
              <a class="dropdown-item" href="sheets_panel_color.php">Add/Delete Color Panel</a>
              <a class="dropdown-item" href="add_labour.php">Add/Delete Labour</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Kits Inventory
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="kits_receive_form.php">Receiving Form</a>
              <a class="dropdown-item" href="kits_issue_form.php">Issue Form</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="kits_receive_slip_all_data.php">Receiving Slip</a>
              <a class="dropdown-item" href="kits_issue_all_data_slip.php">Issue Slip</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="kits_job_work.php">Job Work</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="kits_receive_data.php">Receiving Data</a>
              <a class="dropdown-item" href="kits_issue_data.php">Issue Data</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="kits_product_list.php">Kits Inventory</a>
              <a class="dropdown-item" href="thread_inventory.php">Thread Inventory</a>
              <a class="dropdown-item" href="#">Bladder Inventory</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="add_thread.php">Add Thread</a>
              <a class="dropdown-item" href="add_bladder.php">Add Bladder</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="delete_thread.php">Delete Thread</a>
              <a class="dropdown-item" href="delete_bladder.php">Delete Bladder</a>
            </div>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Football Inventory
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="football_received.php">Receiving Form</a>
              <a class="dropdown-item" href="football_issue.php">Issue Form</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="football_receive_all_slip.php">Receiving Slip</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="football_receiving_data.php">Receiving Data</a>
              <a class="dropdown-item" href="football_issue_data.php">Issue Data</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="football_product_list.php">Football Inventory</a>
            </div>
          </li>
          ';
        }
        ?>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
