<?php
/**
 * WooCommerce Custom Thank You Page Template / Snippet
 * File: woocommerce/checkout/thankyou.php (Inside child theme)
 *
 * @package WooCommerce
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="custom-thankyou-wrapper">
  <style>
    :root {
      --primary-color: #10b981;
      --heading-blue: #1d4ed8;
      --text-main: #1f2937;
      --whatsapp-color: #25D366;
      --whatsapp-hover: #1eb956;
      --fb-page-color: #1877F2;
      --fb-group-color: #7c3aed;
      --danger-color: #dc2626;
      --radius: 14px;
    }
    .custom-thankyou-wrapper {
      max-width: 860px;
      margin: 20px auto;
      font-family: 'Hind Siliguri', sans-serif, -apple-system;
      color: var(--text-main);
    }
    .custom-thankyou-card {
      background: #ffffff;
      border-radius: var(--radius);
      box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.06);
      padding: 32px 20px;
      text-align: center;
      margin-bottom: 22px;
      border: 1px solid #e5e7eb;
    }
    .custom-success-icon {
      width: 70px;
      height: 70px;
      background-color: #ecfdf5;
      color: var(--primary-color);
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 14px;
      border: 3px solid #a7f3d0;
    }
    .custom-success-icon svg { width: 36px; height: 36px; }
    .custom-main-title { font-size: 26px; font-weight: 700; color: var(--heading-blue); margin-bottom: 4px; }
    .custom-sub-title { font-size: 18px; font-weight: 600; color: #059669; margin-bottom: 14px; }
    .custom-notice-box {
      background: #eff6ff;
      border: 1px solid #bfdbfe;
      border-left: 4px solid var(--heading-blue);
      border-radius: 8px;
      padding: 14px 18px;
      margin: 14px auto;
      max-width: 650px;
      font-size: 15px;
      color: #1e3a8a;
      font-weight: 500;
      text-align: center;
    }
    .custom-summary-box {
      background-color: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 16px;
      margin-top: 20px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
      gap: 14px;
      text-align: left;
    }
    .custom-summary-item { border-right: 1px dashed #d1d5db; padding-right: 8px; }
    .custom-summary-item:last-child { border-right: none; }
    .custom-summary-label { font-size: 12px; color: #6b7280; display: block; }
    .custom-summary-val { font-size: 15px; font-weight: 700; color: #111827; }

    /* WhatsApp Direct Contact */
    .custom-wa-card {
      background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
      border: 2px solid #86efac;
      border-radius: var(--radius);
      padding: 22px;
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
    }
    .custom-wa-text { display: flex; align-items: center; gap: 14px; flex: 1; min-width: 260px; }
    .custom-wa-icon {
      width: 50px;
      height: 50px;
      background: var(--whatsapp-color);
      color: #fff;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .custom-wa-icon svg { width: 28px; height: 28px; }
    .custom-wa-btn {
      background: var(--whatsapp-color);
      color: #fff !important;
      text-decoration: none !important;
      padding: 12px 22px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 15px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    /* 3 Products Grid */
    .custom-upsell-section {
      background: #ffffff;
      border-radius: var(--radius);
      padding: 26px 18px;
      border: 1px solid #e5e7eb;
      margin-bottom: 24px;
    }
    .custom-upsell-header { text-align: center; margin-bottom: 22px; }
    .custom-offer-pill {
      display: inline-block;
      background: linear-gradient(135deg, #f43f5e, #e11d48);
      color: #fff;
      font-size: 12px;
      font-weight: 700;
      padding: 3px 12px;
      border-radius: 50px;
      margin-bottom: 6px;
    }
    .custom-upsell-title { font-size: 22px; font-weight: 700; color: #111827; }
    .custom-upsell-title span { color: #dc2626; background: #fef2f2; padding: 2px 8px; border-radius: 6px; border: 1px dashed #f87171; }
    .custom-products-3 {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
    }
    @media (max-width: 768px) {
      .custom-products-3 { grid-template-columns: 1fr; }
      .custom-wa-card { flex-direction: column; text-align: center; }
      .custom-wa-text { flex-direction: column; }
    }
    .custom-prod-card {
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      position: relative;
      background: #fff;
    }
    .custom-disc-tag {
      position: absolute;
      top: 10px;
      right: 10px;
      background: var(--danger-color);
      color: #fff;
      font-size: 11px;
      font-weight: 700;
      padding: 3px 7px;
      border-radius: 4px;
      z-index: 2;
    }
    .custom-prod-img { height: 180px; background: #f8fafc; overflow: hidden; }
    .custom-prod-img img { width: 100%; height: 100%; object-fit: cover; }
    .custom-prod-body { padding: 14px; display: flex; flex-direction: column; flex-grow: 1; }
    .custom-prod-title { font-size: 15px; font-weight: 600; margin-bottom: 8px; min-height: 40px; }
    .custom-prod-price { display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px; }
    .price-off { font-size: 18px; font-weight: 700; color: #dc2626; }
    .price-reg { font-size: 13px; color: #94a3b8; text-decoration: line-through; }
    .btn-buy {
      margin-top: auto;
      background: linear-gradient(135deg, #10b981, #059669);
      color: #fff !important;
      text-decoration: none !important;
      padding: 10px 14px;
      border-radius: 6px;
      text-align: center;
      font-size: 14px;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    /* Free 2 PDF Books & Community */
    .custom-gift-section {
      background: linear-gradient(135deg, #ffffff 0%, #fbf8ff 100%);
      border: 2px solid #e9d5ff;
      border-radius: var(--radius);
      padding: 26px 18px;
      margin-bottom: 24px;
    }
    .gift-header { text-align: center; margin-bottom: 20px; }
    .gift-badge {
      background: linear-gradient(135deg, #8b5cf6, #6d28d9);
      color: #fff;
      font-size: 12px;
      font-weight: 700;
      padding: 3px 12px;
      border-radius: 50px;
      display: inline-block;
      margin-bottom: 6px;
    }
    .custom-pdf-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 14px;
      margin-bottom: 20px;
    }
    @media (max-width: 600px) {
      .custom-pdf-grid { grid-template-columns: 1fr; }
      .custom-comm-btns { grid-template-columns: 1fr; }
    }
    .custom-pdf-box {
      background: #fff;
      border: 1.5px dashed #c084fc;
      border-radius: 10px;
      padding: 14px;
      display: flex;
      align-items: center;
      gap: 12px;
    }
    .pdf-ico {
      width: 46px;
      height: 52px;
      background: #ef4444;
      color: #fff;
      border-radius: 6px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 10px;
      flex-shrink: 0;
    }
    .custom-comm-btns {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
    }
    .comm-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 12px 18px;
      border-radius: 8px;
      color: #fff !important;
      text-decoration: none !important;
      font-weight: 600;
      font-size: 14px;
    }
    .btn-group { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
    .btn-page { background: linear-gradient(135deg, #1877F2, #0d65d9); }
  </style>

  <?php if ( $order ) : ?>
    <!-- 1. Header & Dynamic Order Info -->
    <div class="custom-thankyou-card">
      <div class="custom-success-icon">
        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
        </svg>
      </div>

      <h1 class="custom-main-title">আপনার অর্ডার টি সফল হয়েছে</h1>
      <h2 class="custom-sub-title">অর্ডার করার জন্য ধন্যবাদ</h2>

      <div class="custom-notice-box">
        আমাদের এখান থেকে একজন কাস্টমার প্রতিনিধি আপনাকে ফোন দিয়ে, আপনার অর্ডারটি কনফার্ম করবে। দয়া করে আপনার ফোন নাম্বারটি সচল রাখবেন।
      </div>

      <p style="font-size: 13px; color: #6b7280; margin-top: 8px;">Thank you. Your order has been received.</p>

      <div class="custom-summary-box">
        <div class="custom-summary-item">
          <span class="custom-summary-label">Order number:</span>
          <span class="custom-summary-val"><?php echo esc_html( $order->get_order_number() ); ?></span>
        </div>
        <div class="custom-summary-item">
          <span class="custom-summary-label">Date:</span>
          <span class="custom-summary-val"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></span>
        </div>
        <div class="custom-summary-item">
          <span class="custom-summary-label">Phone / Email:</span>
          <span class="custom-summary-val"><?php echo esc_html( $order->get_billing_phone() ? $order->get_billing_phone() : $order->get_billing_email() ); ?></span>
        </div>
        <div class="custom-summary-item">
          <span class="custom-summary-label">Total:</span>
          <span class="custom-summary-val" style="color: #059669;"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
        </div>
        <div class="custom-summary-item">
          <span class="custom-summary-label">Payment method:</span>
          <span class="custom-summary-val"><?php echo esc_html( $order->get_payment_method_title() ); ?></span>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- 2. WhatsApp Direct Contact (Placed Right After Confirmation) -->
  <div class="custom-wa-card">
    <div class="custom-wa-text">
      <div class="custom-wa-icon">
        <svg fill="currentColor" viewBox="0 0 24 24">
          <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
        </svg>
      </div>
      <div>
        <h3 style="font-size: 17px; font-weight: 700; color: #065f46; margin-bottom: 2px;">কোনো পরামর্শ বা জানার থাকলে হোয়াটসঅ্যাপে যোগাযোগ করুন</h3>
        <p style="font-size: 13px; color: #047857;">অর্ডার সংক্রান্ত দ্রুত সহায়তা পেতে সরাসরি মেসেজ দিন</p>
      </div>
    </div>
    <!-- Replace phone number 8801700000000 -->
    <a href="https://wa.me/8801700000000?text=<?php echo rawurlencode( 'আসসালামু আলাইকুম, আমার অর্ডার নম্বর #' . ( $order ? $order->get_order_number() : '' ) . ' সম্পর্কে জানতে চাই।' ); ?>" target="_blank" rel="noopener" class="custom-wa-btn">
      💬 হোয়াটসঅ্যাপে SMS করুন
    </a>
  </div>

  <!-- 3. Upsell 20% Discount - 3 Products -->
  <div class="custom-upsell-section">
    <div class="custom-upsell-header">
      <span class="custom-offer-pill">🔥 বিশেষ ছাড়</span>
      <h2 class="custom-upsell-title">আপনার জন্য আমাদের বিশেষ অফার! <span>২০% ডিসকাউন্ট</span></h2>
      <p style="font-size: 14px; color: #64748b; margin-top: 4px;">অর্ডারকারী স্পেশাল অফার - নির্ধারিত ৩টি প্রোডাক্টে আজকেই পাচ্ছেন ফ্ল্যাট ২০% মূল্যছাড়!</p>
    </div>

    <div class="custom-products-3">
      <!-- Product 1 -->
      <div class="custom-prod-card">
        <span class="custom-disc-tag">২০% ছাড়</span>
        <div class="custom-prod-img">
          <img src="https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&w=600&q=80" alt="প্রোডাক্ট ১" loading="lazy">
        </div>
        <div class="custom-prod-body">
          <h4 class="custom-prod-title">অর্গানিক স্কিন গ্লো সিরাম (৩০ মি.লি)</h4>
          <div class="custom-prod-price">
            <span class="price-off">৳৮০০</span>
            <span class="price-reg">৳১,০০০</span>
          </div>
          <a href="<?php echo esc_url( home_url( '/checkout/?add-to-cart=101' ) ); ?>" class="btn-buy">
            🛒 Buy Now (কিনুন)
          </a>
        </div>
      </div>

      <!-- Product 2 -->
      <div class="custom-prod-card">
        <span class="custom-disc-tag">২০% ছাড়</span>
        <div class="custom-prod-img">
          <img src="https://images.unsplash.com/photo-1526947425960-945c6e72858f?auto=format&fit=crop&w=600&q=80" alt="প্রোডাক্ট ২" loading="lazy">
        </div>
        <div class="custom-prod-body">
          <h4 class="custom-prod-title">প্রাকৃতিক হেয়ার গ্রোথ অয়েল (১০০ মি.লি)</h4>
          <div class="custom-prod-price">
            <span class="price-off">৳৬৪০</span>
            <span class="price-reg">৳৮০০</span>
          </div>
          <a href="<?php echo esc_url( home_url( '/checkout/?add-to-cart=102' ) ); ?>" class="btn-buy">
            🛒 Buy Now (কিনুন)
          </a>
        </div>
      </div>

      <!-- Product 3 -->
      <div class="custom-prod-card">
        <span class="custom-disc-tag">২০% ছাড়</span>
        <div class="custom-prod-img">
          <img src="https://images.unsplash.com/photo-1556228720-195a672e8a03?auto=format&fit=crop&w=600&q=80" alt="প্রোডাক্ট ৩" loading="lazy">
        </div>
        <div class="custom-prod-body">
          <h4 class="custom-prod-title">ডিপ ক্লিনসিং ফেসওয়াশ (১০০ গ্রাম)</h4>
          <div class="custom-prod-price">
            <span class="price-off">৳৪৮০</span>
            <span class="price-reg">৳৬০০</span>
          </div>
          <a href="<?php echo esc_url( home_url( '/checkout/?add-to-cart=103' ) ); ?>" class="btn-buy">
            🛒 Buy Now (কিনুন)
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- 4. Free 2 PDF Books & Community Join -->
  <div class="custom-gift-section">
    <div class="gift-header">
      <span class="gift-badge">🎁 ফ্রি উপহার ও টিপস</span>
      <h2 style="font-size: 20px; font-weight: 700; color: #1e1b4b; margin-bottom: 4px;">ফ্রি ২টি স্পেশাল PDF বই সংগ্রহ করুন</h2>
      <p style="font-size: 14px; color: #4b5563;">আমাদের নিয়মিত টিপস পেতে এবং ফ্রি বই ২টি পেতে ফেসবুক পেজ এবং ফেসবুক গ্রুপে জয়েন করুন!</p>
    </div>

    <div class="custom-pdf-grid">
      <div class="custom-pdf-box">
        <div class="pdf-ico"><span>PDF</span></div>
        <div>
          <span style="font-size: 11px; font-weight: 700; color: #059669; background: #d1fae5; padding: 2px 6px; border-radius: 4px;">১০০% ফ্রি ই-বুক</span>
          <h4 style="font-size: 14px; font-weight: 700; color: #1f2937; margin: 3px 0;">প্রাকৃতিক রূপচর্চা ও ডেইলি স্কিন কেয়ার গাইড</h4>
          <p style="font-size: 12px; color: #6b7280;">ঘরে বসে প্রাকৃতিক যত্নের পূর্ণাঙ্গ বই।</p>
        </div>
      </div>

      <div class="custom-pdf-box">
        <div class="pdf-ico"><span>PDF</span></div>
        <div>
          <span style="font-size: 11px; font-weight: 700; color: #059669; background: #d1fae5; padding: 2px 6px; border-radius: 4px;">১০০% ফ্রি ই-বুক</span>
          <h4 style="font-size: 14px; font-weight: 700; color: #1f2937; margin: 3px 0;">স্বাস্থ্যকর জীবনযাপন ও ঘরোয়া হেলথ টিপস</h4>
          <p style="font-size: 12px; color: #6b7280;">সুস্থ ও সতেজ থাকার ঘরোয়া টিপস ও গাইড।</p>
        </div>
      </div>
    </div>

    <div class="custom-comm-btns">
      <a href="https://facebook.com/groups/yourfacebookgroup" target="_blank" rel="noopener" class="comm-btn btn-group">
        👥 ফেসবুক গ্রুপে জয়েন করুন (টিপস ও PDF)
      </a>
      <a href="https://m.me/yourfacebookpage" target="_blank" rel="noopener" class="comm-btn btn-page">
        📩 ফেসবুক পেজে মেসেজ করুন ও ফলো দিন
      </a>
    </div>
  </div>
</div>
