<?php

/**
 * The Template for displaying all single products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}

// Get the current product
global $product;

// Check if product has the multipack category
$multipack_category = get_term_by('name', 'Pegpro Hooks Multipack', 'product_cat');
$is_multipack = false;

if ($multipack_category) {
  $product_categories = wp_get_post_terms(get_the_ID(), 'product_cat', array('fields' => 'ids'));
  $is_multipack = in_array($multipack_category->term_id, $product_categories);
}

// Remove the short description from its default position
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

// Add the short description after the title (priority 6, between title at 5 and price at 10)
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 6);

// Add product details link after the price
add_action('woocommerce_single_product_summary', 'custom_product_details_link', 11);

// Remove breadcrumbs
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

// Remove the default product data tabs (we'll show description separately)
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10);

// Remove related products from default position (priority 20)
remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);

// Remove cross-sells from default position
remove_action('woocommerce_after_single_product_summary', 'woocommerce_cross_sell_display', 10);

// Add custom product description section with priority 15 (after main content, before upsells)
add_action('woocommerce_after_single_product_summary', 'custom_product_description_section', 15);

// Display related products as a slider instead of default upsells
remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
add_action('woocommerce_after_single_product_summary', 'custom_related_products_slider', 15);


/**
 * Custom function to display product details link
 */
function custom_product_details_link()
{
  echo '<div class="product-details-link-container">';
  echo '<a href="#product-details" class="product-details-link">View Product Details ↓</a>';
  echo '</div>';
}

/**
 * Custom function to display product description
 */
function custom_product_description_section()
{
  global $product;

  $description = $product->get_description();

  if (!empty($description)) {
    echo '<div id="product-details" class="product-full-description-container">';
    echo '<div class="product-full-description">';
    echo '<h3>Product Details</h3>';
    echo '<div class="description-content">' . wp_kses_post($description) . '</div>';
    echo '</div>';
    echo '</div>';
  }
}


/**
 * Custom function to display related products as a slider on mobile.
 */
function custom_related_products_slider()
{
  global $product;

  $upsell_ids = $product->get_upsell_ids();

  if (empty($upsell_ids)) {
    return;
  }

  $args = array(
    'post_type'      => 'product',
    'post__in'       => $upsell_ids,
    'posts_per_page' => -1,
    'orderby'        => 'post__in',
  );

  $upsell_posts = get_posts($args);

  if ($upsell_posts) {
    echo '<section class="upsells products related-products-slider-container">';
    echo '<h2>' . esc_html__('Related Products', 'woocommerce') . '</h2>';

    echo '<div class="related-products-slider">';
    // Swiper container
    echo '<div class="swiper-container"><div class="swiper-wrapper">';

    foreach ($upsell_posts as $upsell_post) {
      setup_postdata($GLOBALS['post'] = &$upsell_post);
      echo '<div class="swiper-slide">';
      wc_get_template_part('content', 'product');
      echo '</div>';
    }

    wp_reset_postdata();

    echo '</div>'; // close swiper-wrapper

    // Add navigation buttons
    echo '<div class="swiper-button-next"></div>';
    echo '<div class="swiper-button-prev"></div>';

    echo '</div>'; // close swiper-container
    echo '</div>'; // close .related-products-slider
    echo '</section>';
  }
}

get_header(); ?>

<!-- Divi compatibility - ensure proper container structure -->
<div id="main-content">
  <div class="container">
    <div id="content-area" class="clearfix">
      <div id="left-area">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
        <style>
          footer .et_builder_inner_content,
          header .et_builder_inner_content {
            max-width: 100% !important;
            padding: 0 !important;

            .et_pb_section {
              width: 100% !important;
            }
          }

          #et-main-area {
            #main-content {
              justify-items: center !important;

              div#main-content {
                .container {
                  width: 100% !important;
                }
              }
            }
          }

          #main-content .container {
            max-width: 1200px !important;
            margin: 0 auto !important;
            padding: 0 20px !important;
            /* width: 100% !important; */
          }

          #left-area {
            width: 100% !important;
            float: none !important;
            padding: 0 !important;

            ul {
              @media screen and (max-width: 768px) {
                padding-left: 0 !important;
              }

              li {
                @media screen and (max-width: 768px) {
                  margin-left: 0 !important;
                }
              }
            }
          }

          #main-content {
            .container {
              padding: 0 !important;
              margin: 0 !important;
            }
          }

          div.product {
            padding: 0 !important;
          }

          /* Force override Divi's product styling */
          .woocommerce div.product,
          .woocommerce-page div.product {
            max-width: none !important;
            margin: 0 !important;
          }

          /* Clean, modern product page styling with pegboard color swatches */
          .woocommerce div.product {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            flex-wrap: wrap;
          }

          table.variations:hover {
            box-shadow: unset !important;
          }

          /* Product Gallery Styling */
          .woocommerce-product-gallery,
          .images {
            width: 48%;
            order: 1;
            margin-right: 4%;
          }

          .woocommerce-product-gallery__wrapper {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 40px;
            border: none !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
          }

          .woocommerce-product-gallery__wrapper:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12) !important;
          }

          .woocommerce-product-gallery img {
            border-radius: 12px;
          }

          .entry-summary .entry-title {
            padding: 0 !important;
          }

          /* Product Summary Section */
          .summary,
          .entry-summary {
            width: 48%;
            order: 2;
          }

          /* Product Details Link */
          .product-details-link-container {
            margin: 16px 0 24px 0;
          }

          .product-details-link {
            display: inline-block;
            color: #649aaa;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            padding: 8px 16px;
            border: 2px solid #649aaa;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: transparent;
          }

          .product-details-link:hover {
            background: #649aaa;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
          }

          /* Product Title */
          .product_title,
          h1.product_title {
            font-size: 2.5rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 16px;
            letter-spacing: -0.02em;
            line-height: 1.2;
          }

          /* Short Description */
          .woocommerce div.product .woocommerce-product-details__short-description {
            color: #6b7280;
            margin-bottom: 16px;
            border: none;
            padding: 0;
            font-size: 1.1rem;
            line-height: 1.6;
          }

          /* Price Styling */
          .woocommerce div.product p.price {
            font-size: 2rem;
            color: #1a1a1a;
            font-weight: 700;
            margin-bottom: 32px;
            margin-top: 0;
          }

          .woocommerce div.product p.price .amount {
            color: #059669;
          }

          /* STEP LABELS STYLING */
          .step-label {
            font-weight: 700;
            color: #1a1a1a;
            font-size: 1.1rem;
            margin-bottom: 16px;
            margin-top: 24px;
            display: block;
            position: relative;
          }

          .step-label:first-of-type {
            margin-top: 0;
          }

          /* PRODUCT ADD-ONS STYLING (from Pegboard template) */

          /* Addon Labels */
          .wc-pao-addon-name {
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
            display: block;
            font-size: 1rem;
          }

          /* Select Dropdown Styling */
          .wc-pao-addon-select,
          select.wc-pao-addon-field {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            background: #fff;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23649aaa' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            color: #1a1a1a;
            font-weight: 600;
          }

          .wc-pao-addon-select:focus,
          select.wc-pao-addon-field:focus {
            border-color: #649aaa;
            outline: none;
            box-shadow: 0 0 0 3px rgba(100, 154, 170, 0.25);
          }

          /* Image Swatches */
          .wc-pao-addon-image-swatch {
            display: inline-block;
            width: 80px;
            height: 80px;
            border: 2px solid #e5e7eb;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 12px;
            margin-bottom: 12px;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
          }

          .wc-pao-addon-image-swatch:hover {
            border-color: #649aaa;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(100, 154, 170, 0.25);
          }

          .wc-pao-addon-image-swatch.selected {
            border-color: #3c6875;
            box-shadow: 0 0 0 2px rgba(60, 104, 117, 0.3);
            transform: translateY(-2px);
          }

          .wc-pao-addon-image-swatch img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            display: block;
            max-width: 100%;
            max-height: 100%;
            border-radius: 50%;
          }

          /* Hide select dropdown for image swatches */
          .wc-pao-addon-image-swatch-select {
            display: none !important;
          }

          /* Selected swatch text */
          .wc-pao-addon-image-swatch-selected-swatch {
            color: #6b7280;
            font-size: 0.9rem;
            display: none;
            clear: both;
          }

          /* Product Summary/Totals */
          #product-addons-total {
            background: #f9fafb;
            border-radius: 16px;
            padding: 24px;
            margin: 30px 0;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
          }

          .product-addon-totals ul {
            list-style: none;
            margin: 0;
            padding: 0;
          }

          .product-addon-totals li {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
          }

          .product-addon-totals li:last-child,
          .wc-pao-subtotal-line {
            border-bottom: none;
            border-top: 2px solid #e5e7eb;
            padding-top: 16px;
            margin-top: 8px;
            font-weight: 700;
            color: #1a1a1a;
          }

          div.product-addon-totals {
            border: none !important;
          }

          .wc-pao-subtotal-line {
            .price {
              width: 100%;
              display: flex;
              justify-content: space-between;

              .amount {
                color: #059669;
                font-weight: 700;
              }
            }
          }

          .wc-pao-addons-container .wc-pao-addon-container:first-of-type {
            margin-bottom: 1.5em;
          }

          .selected-option-display {
            font-weight: 600;
            color: #3b82f6;
          }

          ul {
            li.variable-item {

              &.image-variable-item,
              &.color-variable-item {
                height: 80px !important;
                width: 80px !important;

              }
            }
          }

          /* VARIATIONS STYLING - Keeping the modern buttons */
          ul[role="radiogroup"] {
            padding: 0 0 .587em .857em !important;
          }

          ul[data-attribute_name="attribute_pa_material-type"] {
            border-radius: unset;

            li {
              border-radius: unset !important;
              background-color: unset !important;
              box-shadow: unset !important;
              width: auto !important;

              .variable-item-span {
                border-radius: unset !important;
              }

              &.variable-item.button-variable-item {
                font-family: inherit !important;
                font-size: 1.1rem !important;
                font-weight: 600 !important;
                border: none !important;
                padding: 16px 32px !important;
                color: #ffffff !important;
                background: #649aaa !important;
                border-radius: 12px !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 4px 14px rgba(100, 154, 170, 0.3) !important;
                cursor: pointer !important;
                width: 100%;
                margin-top: 8px;

                .variable-item-contents {
                  height: auto;
                }

                &:hover {
                  background: #3c6875 !important;
                  transform: translateY(-2px) !important;
                  box-shadow: 0 8px 25px rgba(60, 104, 117, 0.4) !important;
                }

                &:before,
                &:after {
                  content: unset !important;
                }

                &.selected {
                  background: #3c6875 !important;
                  transform: translateY(-2px) !important;
                  box-shadow: 0 8px 25px rgba(60, 104, 117, 0.4) !important;
                }
              }
            }


            /* When any item is selected, make unselected items slightly dimmed but still clickable */
            &:has(.selected) li.variable-item.button-variable-item:not(.selected) {
              background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%) !important;
              color: #ffffff !important;
              box-shadow: 0 2px 8px rgba(148, 163, 184, 0.2) !important;
              opacity: 0.75 !important;
              cursor: pointer !important;

              &:hover {
                background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important;
                color: #ffffff !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 4px 12px rgba(148, 163, 184, 0.3) !important;
              }
            }

            /* Alternative approach for broader browser compatibility */
            &.has-selection li.variable-item.button-variable-item:not(.selected) {
              background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%) !important;
              color: #ffffff !important;
              box-shadow: 0 2px 8px rgba(148, 163, 184, 0.2) !important;
              opacity: 0.75 !important;
              cursor: pointer !important;

              &:hover {
                background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important;
                color: #ffffff !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 4px 12px rgba(148, 163, 184, 0.3) !important;
              }
            }

            /* Additional class-based dimmed state */
            li.variation-dimmed {
              background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%) !important;
              color: #ffffff !important;
              box-shadow: 0 2px 8px rgba(148, 163, 184, 0.2) !important;
              opacity: 0.75 !important;
              cursor: pointer !important;

              &:hover {
                background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important;
                color: #ffffff !important;
                transform: translateY(-1px) !important;
                box-shadow: 0 4px 12px rgba(148, 163, 184, 0.3) !important;
              }
            }
          }

          table.variations {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;

            th.label {
              display: none !important;
            }
          }

          .variations tr {
            border: none !important;
          }

          .variations td {
            border: none !important;
            padding: 16px 0 !important;
            vertical-align: top !important;
          }

          .variations td:first-child {
            padding-right: 20px !important;
          }

          /* Variation Labels */
          .variations .label {
            font-weight: 600 !important;
            color: #374151 !important;
            font-size: 1rem !important;
            width: 120px !important;
            display: block !important;
          }

          .variations .label label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0;
            font-size: 1rem;
          }

          .variable-items-wrapper {
            li.disabled {
              display: none !important;
            }
          }

          /* Select Dropdown Styling for Variations */
          .variations select:focus {
            border-color: #3b82f6 !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
          }

          /* Variation Swatches Plugin Support */
          .variation-swatches,
          .tawcvs-swatches {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: flex-start;
            justify-content: flex-start;
            margin-top: 12px;
          }

          /* Required field styling */
          .required,
          em.required {
            color: #ef4444;
            font-style: normal;
            font-weight: 500;
          }

          /* Reset/Clear button styling - Modern style */
          .reset_variations {
            display: inline-block !important;
            font-size: 0.9rem !important;
            color: #ef4444 !important;
            text-decoration: none !important;
            margin-left: 16px !important;
            font-weight: 600 !important;
            padding: 6px 12px !important;
            border: 1.5px solid #ef4444 !important;
            border-radius: 6px !important;
            background: transparent !important;
            transition: all 0.2s ease !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            font-size: 0.8rem !important;
          }

          .reset_variations:hover {
            color: #ffffff !important;
            background: #ef4444 !important;
            text-decoration: none !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3) !important;
          }

          .reset_variations:active {
            transform: translateY(0) !important;
            box-shadow: 0 1px 4px rgba(239, 68, 68, 0.3) !important;
          }

          /* Quantity Input */
          .woocommerce-page div.product form.cart div.quantity,
          .woocommerce div.product form.cart div.quantity {
            display: flex;
            align-items: center;
            margin-bottom: 24px !important;
            gap: 12px;

            &::before {
              content: "Quantity:";
              font-weight: 600;
              color: #374151;
              font-size: 1rem;
            }

            input[type="number"] {
              width: 80px;
              padding: 14px;
              border: 2px solid #e5e7eb;
              border-radius: 10px;
              text-align: center;
              color: #1a1a1a !important;
              font-weight: 600;
              font-size: 1rem;
              transition: border-color 0.2s ease;

              &:focus {
                border-color: #3b82f6;
                outline: none;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
              }
            }
          }

          /* Add to Cart Button */
          .single_add_to_cart_button {
            font-family: inherit !important;
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            border: none !important;
            padding: 16px 32px !important;
            color: #ffffff !important;
            background-color: #649aaa !important;
            border-radius: 12px !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 14px rgba(100, 154, 170, 0.3) !important;
            cursor: pointer !important;
            width: 100%;
            margin-top: 8px;
          }

          .single_add_to_cart_button:hover {
            background-color: #3c6875 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(60, 104, 117, 0.4) !important;
            color: #ffffff !important;
          }

          .single_add_to_cart_button:active {
            transform: translateY(0) !important;
            box-shadow: 0 4px 14px rgba(60, 104, 117, 0.3) !important;
          }

          .single_add_to_cart_button:disabled {
            background: #d1d5db !important;
            color: #9ca3af !important;
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
          }

          /* Variation Buttons */
          ul[data-attribute_name] li.variable-item.button-variable-item {
            font-family: inherit !important;
            font-size: 1.1rem !important;
            font-weight: 600 !important;
            border: none !important;
            padding: 16px 32px !important;
            color: #ffffff !important;
            background-color: #649aaa !important;
            border-radius: 12px !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 14px rgba(100, 154, 170, 0.3) !important;
            cursor: pointer !important;
            width: 100%;
            margin-top: 8px;
          }

          ul[data-attribute_name] li.variable-item.button-variable-item:hover {
            background-color: #3c6875 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(60, 104, 117, 0.4) !important;
          }

          ul[data-attribute_name] li.variable-item.button-variable-item.selected {
            background-color: #3c6875 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(60, 104, 117, 0.4) !important;
          }

          /* Related Products Add to Cart */
          ul.products li.product .button {
            font-family: inherit !important;
            font-size: 1rem !important;
            font-weight: 600 !important;
            padding: 12px 24px !important;
            color: #ffffff !important;
            background-color: #649aaa !important;
            border-radius: 10px !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 3px 10px rgba(100, 154, 170, 0.3) !important;
            cursor: pointer !important;
            display: inline-block;
          }

          ul.products li.product .button:hover {
            background-color: #3c6875 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 18px rgba(60, 104, 117, 0.4) !important;
          }

          /* Cart Form Container */
          .cart {
            background: #f9fafb;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e5e7eb;
            margin-top: 24px;
          }

          /* Product Full Description Section */
          .product-full-description-container {
            clear: both;
            padding-top: 40px;
            margin-bottom: 40px;
          }

          .product-full-description {
            background: #ffffff;
            border-radius: 16px;
            padding: 32px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            max-height: max-content;
          }

          .product-full-description h3 {
            font-size: 2.25rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e5e7eb;
            letter-spacing: -0.01em;
          }

          .product-full-description .description-content {
            color: #374151;
            font-size: 1rem;
            line-height: 1.7;
          }

          .product-full-description .description-content h4 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 16px;
            margin-top: 0;
          }

          .product-full-description .description-content p {
            margin-bottom: 16px;
          }

          .product-full-description .description-content p:last-child {
            margin-bottom: 0;
          }

          .product-full-description .description-content ul,
          .product-full-description .description-content ol {
            margin-bottom: 16px;
            padding-left: 24px;
          }

          .product-full-description .description-content li {
            margin-bottom: 8px;
            color: #374151;
            line-height: 1.6;
          }

          .product-full-description .description-content strong {
            font-weight: 600;
            color: #1a1a1a;
          }

          /* Upsells (now "Related Products") Section Styling */
          .upsells.products,
          .up-sells.products {
            clear: both;
            padding-top: 40px;
            margin-bottom: 60px;
            width: 100%;
            order: 3;
          }

          .upsells.products>h2,
          .up-sells.products>h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 32px;
            text-align: center;
            letter-spacing: -0.02em;
          }

          .upsells.products ul.products,
          .up-sells.products ul.products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin: 0;
            padding: 0;
            list-style: none;
          }

          .upsells.products li.product,
          .up-sells.products li.product {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            margin: 0;
          }

          .upsells.products li.product:hover,
          .up-sells.products li.product:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
          }

          .upsells.products .woocommerce-loop-product__title,
          .up-sells.products .woocommerce-loop-product__title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin: 12px 0 8px;
          }

          .upsells.products .price,
          .up-sells.products .price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #059669;
          }

          .upsells.products .button,
          .up-sells.products .button {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            margin-top: 12px;
          }

          .upsells.products .button:hover,
          .up-sells.products .button:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            color: #ffffff;
          }

          /* Hide unwanted elements */
          .product_meta,
          .wcpay-express-checkout-wrapper,
          #judgeme_product_reviews {
            display: none !important;
            opacity: 0;
            visibility: hidden;
          }

          /* Page Layout Improvements */
          #main-content .container::before {
            content: "";
            display: none;
          }

          #left-area {
            width: 100% !important;
            padding-right: 0 !important;
          }

          /* Clearfix */
          .woocommerce div.product:after {
            content: "";
            display: table;
            clear: both;
          }

          @media (max-width: 768px) {
            .woocommerce div.product {
              padding: 0 16px;
              flex-direction: column;
            }

            .woocommerce-product-gallery,
            .images {
              order: 2;
              width: 100%;
              margin-right: 0;
            }

            .product_title,
            h1.product_title {
              font-size: 2rem;
            }

            .woocommerce-product-gallery__wrapper {
              padding: 24px;
            }

            .variations {
              padding: 20px;
            }

            .variations .label {
              width: auto !important;
              margin-bottom: 8px;
              display: block !important;
            }

            .variations td {
              display: block !important;
              padding: 12px 0 !important;
            }

            .variations td:first-child {
              padding-right: 0 !important;
            }

            .variations select {
              max-width: 100% !important;
            }

            .cart {
              padding: 20px;
            }

            .product-full-description-container {
              padding-top: 32px;
            }

            .product-full-description {
              padding: 24px;
            }

            .product-full-description h3 {
              font-size: 1.3rem;
            }

            .related.products {
              padding-top: 32px;
            }

            .related.products>h2 {
              font-size: 1.5rem;
              margin-bottom: 24px;
            }

            .upsells.products,
            .up-sells.products {
              padding-top: 32px;
            }

            .upsells.products>h2,
            .up-sells.products>h2 {
              font-size: 1.5rem;
              margin-bottom: 24px;
            }

            .upsells.products ul.products,
            .up-sells.products ul.products {
              grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
              gap: 16px;
            }

            /* Mobile responsive image swatches */
            .wc-pao-addon-image-swatch {
              width: 60px;
              height: 60px;
              margin-right: 8px;
              margin-bottom: 8px;
            }

            .step-label {
              font-size: 1rem;
            }
          }

          /* Micro-interactions and Polish */
          .variations,
          .cart,
          .quantity input,
          .variations select {
            transition: all 0.2s ease;
          }

          .variations:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
          }

          .cart:hover {
            border-color: #d1d5db;
          }

          /* Loading state for add to cart */
          .single_add_to_cart_button.loading {
            position: relative;
            color: transparent !important;
          }

          .single_add_to_cart_button.loading::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
          }

          @keyframes spin {
            0% {
              transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
              transform: translate(-50%, -50%) rotate(360deg);
            }
          }

          /* == Related Products Slider == */
          .related-products-slider-container {
            position: relative;
          }

          /* Default: Desktop Grid Layout */
          .related-products-slider .swiper-wrapper {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            list-style: none;
            padding: 0;
            margin: 0;
          }

          .related-products-slider .swiper-slide {
            width: 100%;
          }

          /* Mobile: Slider Layout */
          @media (max-width: 768px) {
            .related-products-slider .swiper-container {
              overflow: hidden;
              position: relative;
            }

            /* Swiper JS will set display: flex on this element */
            .related-products-slider .swiper-wrapper {
              display: flex;
              /* This overrides the grid layout on mobile */
              gap: 0;
            }

            .related-products-slider .swiper-slide {
              width: 100%;
              /* A single slide takes the full width */
              flex-shrink: 0;
              /* Prevent slide from shrinking */
            }

            .related-products-slider .swiper-button-next,
            .related-products-slider .swiper-button-prev {
              display: flex;
              align-items: center;
              justify-content: center;
              width: 40px;
              height: 40px;
              background-color: rgba(255, 255, 255, 0.9);
              border-radius: 50%;
              box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
              color: #333;
              position: absolute;
              top: 50%;
              transform: translateY(-50%);
              z-index: 10;
              cursor: pointer;
              transition: background-color 0.2s ease;

              svg {
                height: 1rem;
              }
            }

            .related-products-slider .swiper-button-next:hover,
            .related-products-slider .swiper-button-prev:hover {
              background-color: #fff;
            }

            .related-products-slider .swiper-button-prev {
              left: 10px;
            }

            .related-products-slider .swiper-button-next {
              right: 10px;
            }

            /* Hide default Swiper icon and use Font Awesome */
            .related-products-slider .swiper-button-next:after,
            .related-products-slider .swiper-button-prev:after {
              display: none;
            }
          }
        </style>
        <?php
        /**
         * woocommerce_before_main_content hook.
         *
         * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
         * @hooked woocommerce_breadcrumb - 20
         */
        do_action('woocommerce_before_main_content');
        ?>

        <?php while (have_posts()) : ?>
          <?php the_post(); ?>

          <?php wc_get_template_part('content', 'single-product'); ?>

        <?php endwhile; // end of the loop.
        ?>

        <?php
        /**
         * woocommerce_after_single_product_summary hook.
         *
         * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
         */
        do_action('woocommerce_after_main_content');
        ?>

      </div> <!-- #left-area -->
    </div> <!-- #content-area -->
  </div> <!-- .container -->
</div> <!-- #main-content -->

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
  // Simple script for step labels functionality
  document.addEventListener('DOMContentLoaded', function() {

    // Check if this is a multipack product (using the PHP variable)
    const isMultipack = <?php echo $is_multipack ? 'true' : 'false'; ?>;

    // Function to add step labels dynamically
    function addStepLabels() {
      // Remove any existing step labels first
      document.querySelectorAll('.step-label').forEach(label => label.remove());

      // Get all variation and addon containers
      const variationTable = document.querySelector('.variations');
      const addonContainers = document.querySelectorAll('.wc-pao-addon-container');

      // Collect all sections that need labeling
      const sections = [];

      // Check variations first
      if (variationTable) {
        const variationRows = variationTable.querySelectorAll('tr');

        variationRows.forEach(row => {
          const label = row.querySelector('.label');
          const valueCell = row.querySelector('td:last-child');

          if (label && valueCell) {
            // Get the attribute name from select or data attribute
            const select = valueCell.querySelector('select');
            const attributeName = select ? select.name.replace('attribute_', '') : '';

            // Check for variation swatches data attribute
            const swatchesContainer = valueCell.querySelector('[data-attribute_name]');
            const swatchAttributeName = swatchesContainer ?
              swatchesContainer.getAttribute('data-attribute_name').replace('attribute_', '') : '';

            const finalAttributeName = attributeName || swatchAttributeName;

            if (finalAttributeName) {
              sections.push({
                element: label.parentNode,
                insertBefore: label,
                attributeName: finalAttributeName,
                originalText: label.textContent.trim()
              });
            }
          }
        });
      }

      // Check addons
      addonContainers.forEach(container => {
        const addonName = container.querySelector('.wc-pao-addon-name');

        if (addonName) {
          const addonText = addonName.textContent.toLowerCase().trim();

          sections.push({
            element: container,
            insertBefore: addonName,
            attributeName: 'addon',
            originalText: addonText
          });
        }
      });

      // Now determine which sections get which step labels
      let stepCounter = 1;
      let hasHookShape = false;

      // First pass: check if we have hook shape selection
      sections.forEach(section => {
        const text = section.originalText.toLowerCase();
        const attr = section.attributeName.toLowerCase();

        // Check for hook shape indicators
        if (text.includes('multipack') && text.includes('hook') ||
          text.includes('straight') && text.includes('hook') && text.includes('size') ||
          text.includes('hook') && text.includes('shape')) {
          hasHookShape = true;
        }
      });

      // Second pass: assign step labels
      sections.forEach(section => {
        const text = section.originalText.toLowerCase();
        const attr = section.attributeName.toLowerCase();
        let stepLabel = '';
        let stepNum = 0;

        // Step 1: Material Type (always first when present)
        if (attr.includes('material-type') ||
          (text.includes('material') && text.includes('type'))) {
          stepLabel = 'Step 1: Select Pegboard Material Type';
          stepNum = 1;
        }
        // Step 2: Hook Shape - Different text for multipack vs standard
        else if (hasHookShape && (
            text.includes('multipack') && text.includes('hook') ||
            text.includes('straight') && text.includes('hook') && text.includes('size') ||
            text.includes('hook') && text.includes('shape'))) {
          if (isMultipack) {
            stepLabel = 'Step 2. Select Desired Multipack Shape Combination';
          } else {
            stepLabel = 'Step 2: Select Desired Hook Shape';
          }
          stepNum = 2;
        }
        // Step 3: Color (last step, number depends on whether hook shape exists)
        else if (text.includes('color')) {
          if (hasHookShape) {
            stepLabel = 'Step 3: Select Desired Color';
            stepNum = 3;
          } else {
            stepLabel = 'Step 2: Select Desired Color';
            stepNum = 2;
          }
        }

        // Add the step label if we determined one
        if (stepLabel) {
          const stepLabelElement = document.createElement('div');
          stepLabelElement.className = 'step-label';
          stepLabelElement.setAttribute('data-step', stepNum);
          stepLabelElement.textContent = stepLabel;

          // Insert before the original label/name
          section.element.insertBefore(stepLabelElement, section.insertBefore);
        }
      });
    }

    // Run on page load
    addStepLabels();

    // Original variation dimming functionality
    const materialTypeList = document.querySelector('ul[data-attribute_name="attribute_pa_material-type"]');
    if (materialTypeList) {

      function updateDimmedStates() {
        const selectedItem = materialTypeList.querySelector('.selected');
        const allItems = materialTypeList.querySelectorAll('li.variable-item.button-variable-item');
      }

      // One event listener for all clicks inside variation list
      materialTypeList.addEventListener('click', e => {
        if (e.target.closest('.variable-item')) {
          requestAnimationFrame(updateDimmedStates);
        }
      });

      // One listener for WooCommerce variation changes
      const variationForm = document.querySelector('.variations_form');
      if (variationForm) {
        variationForm.addEventListener('reset_data', updateDimmedStates);
        variationForm.addEventListener('woocommerce_variation_select_change', updateDimmedStates);
        variationForm.addEventListener('woocommerce_update_variation_values', addStepLabels);
      }

      // Initial run
      updateDimmedStates();
    }

    // Smooth scroll for product details link
    const productDetailsLink = document.querySelector('.product-details-link');
    if (productDetailsLink) {
      productDetailsLink.addEventListener('click', function(e) {
        e.preventDefault();
        const targetSection = document.querySelector('#product-details');
        if (targetSection) {
          targetSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    }

    // Related Products Slider Initialization
    const relatedSliderContainer = document.querySelector('.related-products-slider .swiper-container');
    if (relatedSliderContainer) {
      let relatedSlider;

      const initSlider = () => {
        // Activate slider only on screens smaller than 768px
        if (window.innerWidth < 768 && !relatedSlider) {
          relatedSlider = new Swiper(relatedSliderContainer, {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 16,
            grabCursor: true,
            navigation: {
              nextEl: '.related-products-slider .swiper-button-next',
              prevEl: '.related-products-slider .swiper-button-prev',
            },
          });
        } else if (window.innerWidth >= 768 && relatedSlider) {
          // Destroy slider on larger screens
          relatedSlider.destroy(true, true);
          relatedSlider = undefined;
        }
      };

      // Run on load
      initSlider();

      // Rerun on resize
      window.addEventListener('resize', initSlider);
    }
  });
</script>

<?php
get_footer();
?>