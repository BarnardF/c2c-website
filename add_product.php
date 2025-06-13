<?php
require_once 'includes/db_connect.php';

include('includes/header.php');
include('includes/error_msg.php');



if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);



?>


<!-- add product form, ai helped with this,especially image preview(claude and chatgpt - 3 June) -->
<div class="form_container">
    <h2 class="form-title">List a Product</h2>
    <form action="add_prod_logic.php" method="POST" enctype="multipart/form-data">
        
        <!-- title -->
        <div class="form-group">
            <label for="title">Product Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Enter product title"
                   value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>" required>
        </div>

        <!-- category -->
        <div class="form-group">
            <label for="category">Category</label>
            <select class="form-control" name="category" id="category" required>
                <option value="">Select Category</option>
                <?php
                $stmt = $conn->prepare("SELECT category_id, category_name 
                                        FROM category 
                                        ORDER BY category_name");
                $stmt->execute();
                $result = $stmt->get_result();

                while ($category = $result->fetch_assoc()) {
                    $selected = (isset($form_data['category']) && $form_data['category'] == $category['category_id']) ? 'selected' : '';
                    echo "<option value='{$category['category_id']}' $selected>{$category['category_name']}</option>";
                }
                ?>
            </select>
        </div>
        
        <!-- produ description -->
        <div class="form-group">
            <label for="product_descr">Product Description</label>
            <textarea class="form-control" name="product_descr" id="product_descr" rows="4"
                      placeholder="Describe your product in detail" required><?php echo htmlspecialchars($form_data['product_descr'] ?? ''); ?></textarea>
        </div>

        <!-- price -->
        <div class="form-group">
            <label for="price">Price (ZAR)</label>
            <input type="number" class="form-control" name="price" id="price" placeholder="0.00" step="0.01" min="0.01"
                   value="<?php echo htmlspecialchars($form_data['price'] ?? ''); ?>" required>
        </div>

        <!-- product condition -->
        <div class="form-group">
            <label for="product_condition">Condition</label>
            <select class="form-control" name="product_condition" id="product_condition" required>
                <option value="">Select Condition</option>
                <option value="new" <?php echo (isset($form_data['product_condition']) && $form_data['product_condition'] == 'new') ? 'selected' : ''; ?>>New</option>
                <option value="like-new" <?php echo (isset($form_data['product_condition']) && $form_data['product_condition'] == 'like-new') ? 'selected' : ''; ?>>Like New</option>
                <option value="good" <?php echo (isset($form_data['product_condition']) && $form_data['product_condition'] == 'good') ? 'selected' : ''; ?>>Good</option>
                <option value="fair" <?php echo (isset($form_data['product_condition']) && $form_data['product_condition'] == 'fair') ? 'selected' : ''; ?>>Fair</option>
                <option value="poor" <?php echo (isset($form_data['product_condition']) && $form_data['product_condition'] == 'poor') ? 'selected' : ''; ?>>Poor</option>
            </select>
        </div>

        <!-- location -->
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" class="form-control" name="location" id="location" placeholder="e.g., Johannesburg, Cape Town"
                   value="<?php echo htmlspecialchars($form_data['location'] ?? ''); ?>" required>
        </div>

        <!-- shipping -->
        <div class="form-group">
            <label for="shipping_options">Shipping Options</label>
            <select class="form-control" name="shipping_options" id="shipping_options" required>
                <option value="">Select Shipping Option</option>
                <option value="pickup" <?php echo (isset($form_data['shipping']) && $form_data['shipping'] == 'pickup') ? 'selected' : ''; ?>>Local Pickup Only</option>
                <option value="delivery" <?php echo (isset($form_data['shipping']) && $form_data['shipping'] == 'delivery') ? 'selected' : ''; ?>>Delivery Available</option>
                <option value="both" <?php echo (isset($form_data['shipping']) && $form_data['shipping'] == 'both') ? 'selected' : ''; ?>>Both Pickup & Delivery</option>
            </select>
        </div>


        <!-- image -->
        <div class="form-group">
            <label for="product_image">Product Image</label>
            <input type="file" class="form-control" name="product_image" id="product_image" onchange="previewImage()" accept="image/jpeg,image/png" required>
            <img id="preview" src="#" alt="Preview" style="max-width: 200px; display: none;">
            <small>Upload a clear image of your product (JPG, PNG - Max 5MB)</small>
        </div>

        <button type="submit" class="button">List Product</button>

        <div class="redirect">
            <a href="index.php">Cancel</a>
        </div>

    </form>
</div>
<script src="assets/js/scripts.js"></script>

<?php include('includes/footer.php'); ?>