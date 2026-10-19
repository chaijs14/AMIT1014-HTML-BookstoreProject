$(document).ready(function() {
    
    // ========== 1. SEARCH + CATEGORY + SORT ALL TOGETHER ==========
    function filterAndSortProducts() {
        // Get search text
        var searchText = $('#searchInput').val().toLowerCase();
        
        // Get selected category
        var selectedCategory = 'all';
        $('.filter-link').each(function() {
            if ($(this).hasClass('active')) {
                selectedCategory = $(this).data('category');
            }
        });
        
        // Get sort option
        var sortOption = $('#priceSort').val();
        
        // First, hide all products
        $('.product-card').hide();
        
        // Then show only matching products
        var visibleProducts = [];
        $('.product-card').each(function() {
            var $product = $(this);
            var title = $product.data('title').toLowerCase();
            var category = $product.data('category');
            
            var matchesSearch = (searchText === '' || title.indexOf(searchText) > -1);
            var matchesCategory = (selectedCategory === 'all' || category === selectedCategory);
            
            if (matchesSearch && matchesCategory) {
                $product.show();
                visibleProducts.push($product);
            } else {
                $product.hide();
            }
        });
        
        // Sort products by price
        if (sortOption === 'low') {
            visibleProducts.sort(function(a, b) {
                return a.data('price') - b.data('price');
            });
        } else if (sortOption === 'high') {
            visibleProducts.sort(function(a, b) {
                return b.data('price') - a.data('price');
            });
        }
        
        // Reorder products in grid
        var $grid = $('#productGrid');
        $.each(visibleProducts, function(i, $product) {
            $grid.append($product);
        });
    }
    
    // ========== 2. SEARCH BOX - When user types ==========
    $('#searchInput').on('keyup', function() {
        filterAndSortProducts();
    });
    
    // ========== 3. CATEGORY FILTER - When user clicks category ==========
    $('.filter-link').on('click', function(e) {
        e.preventDefault();
        
        // Remove active class from all
        $('.filter-link').removeClass('active');
        // Add active class to clicked one
        $(this).addClass('active');
        
        filterAndSortProducts();
    });
    
    // ========== 4. PRICE SORT - When user changes sort option ==========
    $('#priceSort').on('change', function() {
        filterAndSortProducts();
    });
    
    // ========== 5. DELETE CONFIRMATION ==========
    $('.delete-btn').on('click', function() {
        var result = confirm('Are you sure you want to delete this?');
        if (result === false) {
            return false;
        }
        return true;
    });
    
    // ========== 6. AUTO HIDE ALERTS ==========
    setTimeout(function() {
        $('.alert').fadeOut(500);
    }, 3000);
    
    // ========== 7. FIX BROKEN IMAGES ==========
    $('img').on('error', function() {
        $(this).attr('src', 'https://via.placeholder.com/150x200?text=No+Image');
    });
    
    // ========== 8. FORM VALIDATION ==========
    $('form').on('submit', function() {
        var isValid = true;
        
        $(this).find('input[required], select[required]').each(function() {
            if ($(this).val() === '') {
                alert('Please fill in all required fields');
                $(this).focus();
                isValid = false;
                return false;
            }
        });
        
        // Check if passwords match
        var password = $('#password').val();
        var confirmPassword = $('#confirmPassword').val();
        if (confirmPassword !== undefined && password !== confirmPassword) {
            alert('Passwords do not match!');
            isValid = false;
        }
        
        // Check email format
        var email = $('#email').val();
        if (email && email.indexOf('@') === -1) {
            alert('Please enter a valid email address');
            isValid = false;
        }
        
        return isValid;
    });
    
    // ========== 9. ADD TO CART FEEDBACK ==========
    $('.add-to-cart, .buy-btn').on('click', function() {
        var $btn = $(this);
        var originalText = $btn.text();
        $btn.text('Added!');
        setTimeout(function() {
            $btn.text(originalText);
        }, 1000);
    });
    
    // ========== 10. CHECKOUT CONFIRMATION ==========
    $('.checkout-btn').on('click', function(e) {
        var confirm = window.confirm('Proceed to checkout?');
        if (confirm === false) {
            e.preventDefault();
        }
    });
    
    // ========== 11. PROFILE PHOTO PREVIEW ==========
    $('#profilePhoto').on('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#profilePreview').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // ========== 12. PASSWORD MATCH CHECK ==========
    $('#confirmPassword').on('keyup', function() {
        var password = $('#password').val();
        var confirm = $(this).val();
        
        if (password !== confirm) {
            $(this).css('border-color', 'red');
            $('#passwordMatchMsg').text('Passwords do not match').css('color', 'red');
        } else {
            $(this).css('border-color', 'green');
            $('#passwordMatchMsg').text('Passwords match').css('color', 'green');
        }
    });
    
    // ========== 13. QUANTITY BUTTONS ==========
    $('.qty-plus').on('click', function() {
        var $input = $(this).siblings('.qty-input');
        var val = parseInt($input.val());
        $input.val(val + 1);
    });
    
    $('.qty-minus').on('click', function() {
        var $input = $(this).siblings('.qty-input');
        var val = parseInt($input.val());
        if (val > 1) {
            $input.val(val - 1);
        }
    });
    
    // ========== 14. BACK TO TOP BUTTON ==========
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 200) {
            $('#backToTop').fadeIn();
        } else {
            $('#backToTop').fadeOut();
        }
    });
    
    $('#backToTop').on('click', function() {
        $('html, body').animate({scrollTop: 0}, 500);
    });
    
    // ========== 15. REMOVE ERROR STYLES ==========
    $('input, select').on('keyup change', function() {
        $(this).css('border-color', '#ddd');
    });
    
    // ========== 16. PASSWORD STRENGTH METER ==========
    $('#password').on('keyup', function() {
        var pwd = $(this).val();
        var strength = 0;
        
        if (pwd.length >= 6) strength++;
        if (pwd.match(/[a-z]/)) strength++;
        if (pwd.match(/[A-Z]/)) strength++;
        if (pwd.match(/[0-9]/)) strength++;
        
        var msg = '';
        var color = '';
        if (strength <= 1) { msg = 'Weak'; color = 'red'; }
        else if (strength <= 3) { msg = 'Medium'; color = 'orange'; }
        else { msg = 'Strong'; color = 'green'; }
        
        if (pwd.length > 0) {
            $('#passwordStrength').html('<span style="color:' + color + '">Strength: ' + msg + '</span>');
        } else {
            $('#passwordStrength').html('');
        }
    });
    
    console.log('All features loaded!');
});