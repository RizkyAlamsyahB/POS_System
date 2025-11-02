/**
 * ============================================================================
 * POS SYSTEM - TAX & PROMOTION LOGIC
 * ============================================================================
 * 
 * SKENARIO PAJAK (Tax Scenarios):
 * 
 * 1. PRODUK TANPA PAJAK (tax_type = 'NONE')
 *    - Contoh: Sayuran segar, bahan mentah
 *    - Perhitungan: Total = Harga - Discount
 *    - Pajak: Rp 0
 * 
 * 2. PRODUK DENGAN PAJAK SUDAH TERMASUK (tax_included = true)
 *    - Contoh: Harga Rp 11.000 (sudah include PPN 11%)
 *    - Harga yang ditampilkan sudah final
 *    - Pajak TIDAK ditambahkan lagi
 *    - Perhitungan: Total = Harga - Discount
 *    - Display: Tidak perlu tampil di breakdown (sudah termasuk)
 * 
 * 3. PRODUK DENGAN PAJAK BELUM TERMASUK (tax_included = false)
 *    - Contoh: Harga Rp 10.000 + PPN 11% = Rp 11.100
 *    - Pajak dihitung dan ditambahkan
 *    - Pajak dihitung SETELAH discount
 *    - Perhitungan: 
 *      * Subtotal: Rp 10.000
 *      * Discount 20%: - Rp 2.000
 *      * Harga setelah discount: Rp 8.000
 *      * PPN 11%: Rp 8.000 × 11% = Rp 880
 *      * Total: Rp 8.880
 * 
 * PAYMENT SUMMARY BREAKDOWN:
 * ─────────────────────────────────────────
 * Sub Total:     Rp 100.000  ← Total semua item
 * Discount:      - Rp 20.000  ← Total promo
 * Tax (PPN):     Rp 8.800     ← Hanya dari item yg tax_included=false
 * ─────────────────────────────────────────
 * Total Payment: Rp 88.800    ← Yang harus dibayar
 */

// Global variables - will be initialized from HTML
let cart = [];
let selectedPaymentMethod = 'credit';
let selectedOrderType = 'dine_in';
let activePromotions = []; // Will be set from PHP
let checkoutUrl = ''; // Will be set from PHP
let outletData = {}; // Will be set from PHP
let currentUser = {}; // Will be set from PHP

// Initialize function - call this from HTML after setting global variables
function initializePOS(promotionsData, posCheckoutUrl, outlet, user) {
    activePromotions = promotionsData;
    checkoutUrl = posCheckoutUrl;
    outletData = outlet;
    currentUser = user;
    
    console.log('POS Initialized with:', {
        promotions: activePromotions,
        checkoutUrl: checkoutUrl,
        outlet: outletData,
        user: currentUser
    });
    
    updateInvoice();
}

// Format currency to Rupiah
function formatCurrency(amount) {
    return 'Rp ' + Math.round(amount).toLocaleString('id-ID');
}

// Order type selection handler
document.querySelectorAll('.order-type-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Update active state
        document.querySelectorAll('.order-type-btn').forEach(b => {
            b.style.background = '#e9ecef';
            b.style.color = '#495057';
            b.classList.remove('active');
        });
        this.style.background = '#3772F0';
        this.style.color = 'white';
        this.classList.add('active');
        
        selectedOrderType = this.dataset.type;
        
        // Show/hide appropriate fields
        const tableNumberField = document.getElementById('tableNumberField');
        const customerNameField = document.getElementById('customerNameField');
        
        if (selectedOrderType === 'dine_in') {
            tableNumberField.style.display = 'block';
            customerNameField.style.display = 'none';
            document.getElementById('customerName').value = '';
        } else {
            tableNumberField.style.display = 'none';
            customerNameField.style.display = 'block';
            document.getElementById('tableNumber').value = '';
        }
    });
});

// Show promotion details modal
function showPromoDetails(promo) {
    console.log('Show promo details:', promo);
    
    // Format days
    const daysMap = {
        'monday': 'Senin',
        'tuesday': 'Selasa', 
        'wednesday': 'Rabu',
        'thursday': 'Kamis',
        'friday': 'Jumat',
        'saturday': 'Sabtu',
        'sunday': 'Minggu'
    };
    
    let daysText = 'Setiap hari';
    if (promo.valid_days) {
        const validDays = promo.valid_days.split(',').map(day => daysMap[day.toLowerCase()] || day);
        daysText = validDays.join(', ');
    }
    
    // Format time
    let timeText = 'Sepanjang hari';
    if (promo.start_time && promo.end_time) {
        timeText = `${promo.start_time.substring(0, 5)} - ${promo.end_time.substring(0, 5)} WIB`;
    }
    
    // Format discount
    let discountText = '';
    if (promo.discount_type === 'percentage') {
        discountText = `Diskon ${promo.discount_value}%`;
        if (promo.max_discount && parseFloat(promo.max_discount) > 0) {
            discountText += ` (Maks. ${formatCurrency(promo.max_discount)})`;
        }
    } else {
        discountText = `Diskon ${formatCurrency(promo.discount_value)}`;
    }
    
    // Format minimum purchase
    let minPurchaseText = 'Tidak ada minimal pembelian';
    if (promo.min_purchase && parseFloat(promo.min_purchase) > 0) {
        minPurchaseText = formatCurrency(promo.min_purchase);
    }
    
    // Format date range
    const startDate = new Date(promo.start_date);
    const endDate = new Date(promo.end_date);
    const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };
    const dateRange = `${startDate.toLocaleDateString('id-ID', dateOptions)} - ${endDate.toLocaleDateString('id-ID', dateOptions)}`;
    
    // Build modal content
    const modalContent = `
        <div style="padding: 1rem 0;">
            <!-- Promo Name -->
            <div class="mb-4">
                <h4 class="text-danger mb-2">
                    <i class="bi bi-megaphone-fill me-2"></i>${promo.name}
                </h4>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                    <strong>Kode:</strong> <code class="bg-light px-2 py-1 rounded">${promo.code}</code>
                </p>
            </div>
            
            <!-- Discount Info -->
            <div class="alert alert-success mb-3" role="alert">
                <i class="bi bi-tag-fill me-2"></i>
                <strong>${discountText}</strong>
            </div>
            
            <!-- Description -->
            ${promo.description ? `
                <div class="mb-4">
                    <h6 class="text-secondary mb-2">
                        <i class="bi bi-card-text me-2"></i>Deskripsi
                    </h6>
                    <p class="mb-0">${promo.description}</p>
                </div>
            ` : ''}
            
            <!-- Terms & Conditions -->
            <div class="card border-0 bg-light">
                <div class="card-body">
                    <h6 class="text-secondary mb-3">
                        <i class="bi bi-info-circle-fill me-2"></i>Syarat & Ketentuan
                    </h6>
                    
                    <div class="row g-3">
                        <!-- Period -->
                        <div class="col-12">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-calendar-range text-primary me-2 mt-1"></i>
                                <div>
                                    <small class="text-muted d-block">Periode Promo</small>
                                    <strong>${dateRange}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Valid Days -->
                        <div class="col-12">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-calendar-week text-primary me-2 mt-1"></i>
                                <div>
                                    <small class="text-muted d-block">Berlaku Hari</small>
                                    <strong>${daysText}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Valid Time -->
                        <div class="col-12">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-clock text-primary me-2 mt-1"></i>
                                <div>
                                    <small class="text-muted d-block">Jam Operasional</small>
                                    <strong>${timeText}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Min Purchase -->
                        <div class="col-12">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-cash-coin text-primary me-2 mt-1"></i>
                                <div>
                                    <small class="text-muted d-block">Minimal Pembelian</small>
                                    <strong>${minPurchaseText}</strong>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Applicable Products -->
                        <div class="col-12">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-box-seam text-primary me-2 mt-1"></i>
                                <div>
                                    <small class="text-muted d-block">Produk Berlaku</small>
                                    <strong>${promo.product_ids && promo.product_ids.length > 0 ? 'Produk tertentu saja' : 'Semua produk'}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Inject content and show modal
    document.getElementById('promoModalBody').innerHTML = modalContent;
    const modal = new bootstrap.Modal(document.getElementById('promoModal'));
    modal.show();
}

// Calculate promotion discount for a product
function calculatePromoDiscount(product, quantity) {
    let discount = 0;
    let appliedPromo = null;
    
    // Find applicable promotion
    for (const promo of activePromotions) {
        // Check if product is eligible for this promotion
        // If product_ids is empty array, promo applies to all products
        const isEligible = promo.product_ids.length === 0 || 
                          promo.product_ids.includes(product.id.toString()) ||
                          promo.product_ids.includes(product.id);
        
        console.log('Checking promo:', promo.code, 'for product:', product.id, 'eligible:', isEligible);
        
        if (!isEligible) continue;
        
        const itemSubtotal = product.price * quantity;
        
        // Check minimum purchase requirement
        if (promo.min_purchase && itemSubtotal < parseFloat(promo.min_purchase)) {
            console.log('Min purchase not met:', itemSubtotal, '<', promo.min_purchase);
            continue;
        }
        
        // Calculate discount
        if (promo.discount_type === 'percentage') {
            discount = itemSubtotal * (parseFloat(promo.discount_value) / 100);
            
            // Apply max discount if specified
            if (promo.max_discount && discount > parseFloat(promo.max_discount)) {
                discount = parseFloat(promo.max_discount);
            }
        } else if (promo.discount_type === 'fixed_amount') {
            discount = parseFloat(promo.discount_value);
        }
        
        console.log('Discount calculated:', discount, 'from promo:', promo.code);
        
        appliedPromo = promo;
        break; // Use first applicable promo only
    }
    
    return {
        discount: discount,
        promo: appliedPromo
    };
}

// Toggle invoice panel (mobile/tablet)
function toggleInvoice() {
    const invoicePanel = document.getElementById('invoicePanel');
    const backdrop = document.getElementById('invoiceBackdrop');
    
    invoicePanel.classList.toggle('show');
    backdrop.classList.toggle('show');
    
    // Prevent body scroll when invoice is open
    if (invoicePanel.classList.contains('show')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
}

// Add to cart
function addToCart(product) {
    if (product.stock <= 0) {
        alert('Sorry, this product is out of stock!');
        return;
    }
    
    const existing = cart.find(item => item.id === product.id);
    
    if (existing) {
        if (existing.qty + 1 > product.stock) {
            alert(`Not enough stock! Available: ${product.stock}`);
            return;
        }
        existing.qty += 1;
    } else {
        cart.push({...product, qty: 1, note: 'Dont Add Vegetables'});
    }
    
    updateInvoice();
    
    // Auto open invoice on mobile/tablet when item added
    if (window.innerWidth <= 992) {
        const invoicePanel = document.getElementById('invoicePanel');
        const backdrop = document.getElementById('invoiceBackdrop');
        
        if (!invoicePanel.classList.contains('show')) {
            setTimeout(() => {
                invoicePanel.classList.add('show');
                backdrop.classList.add('show');
                document.body.style.overflow = 'hidden';
            }, 150);
        }
    }
}

// Update quantity
function updateQty(id, change) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    
    const newQty = item.qty + change;
    
    if (newQty > item.stock) {
        alert(`Not enough stock! Available: ${item.stock}`);
        return;
    }
    
    if (newQty <= 0) {
        removeItem(id);
        return;
    }
    
    item.qty = newQty;
    updateInvoice();
}

// Remove item
function removeItem(id) {
    cart = cart.filter(item => item.id !== id);
    updateInvoice();
}

// Update invoice display
function updateInvoice() {
    const container = document.getElementById('invoiceItems');
    
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="invoice-empty">
                <i class="bi bi-receipt"></i>
                <p>No items added yet</p>
            </div>
        `;
        document.getElementById('btnPlaceOrder').disabled = true;
        
        // Reset totals to 0
        document.getElementById('subTotal').textContent = 'Rp 0';
        document.getElementById('discountAmount').textContent = 'Rp 0';
        document.getElementById('taxAmount').textContent = 'Rp 0';
        document.getElementById('totalPayment').textContent = 'Rp 0';
        
        updateBadge();
        return;
    }
    
    document.getElementById('btnPlaceOrder').disabled = false;
    
    container.innerHTML = '';
    
    cart.forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'invoice-item';
        itemDiv.innerHTML = `
            <img src="${item.image}" alt="${item.name}" class="invoice-item-image">
            <div class="invoice-item-details">
                <div class="invoice-item-header">
                    <div>
                        <div class="invoice-item-name">${item.name}</div>
                        <div class="invoice-item-note">${item.note || ''}</div>
                    </div>
                    <button class="btn-remove-item" data-id="${item.id}">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="invoice-item-footer">
                    <div class="invoice-item-qty">
                        <button class="qty-invoice-btn qty-decrease" data-id="${item.id}">
                            <i class="bi bi-dash"></i>
                        </button>
                        <span class="qty-invoice-value">${item.qty}x</span>
                        <button class="qty-invoice-btn qty-increase" data-id="${item.id}">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                    <div class="invoice-item-price">${formatCurrency(item.price * item.qty)}</div>
                </div>
            </div>
        `;
        
        // Add event listeners
        const removeBtn = itemDiv.querySelector('.btn-remove-item');
        removeBtn.addEventListener('click', () => removeItem(item.id));
        
        const decreaseBtn = itemDiv.querySelector('.qty-decrease');
        decreaseBtn.addEventListener('click', () => updateQty(item.id, -1));
        
        const increaseBtn = itemDiv.querySelector('.qty-increase');
        increaseBtn.addEventListener('click', () => updateQty(item.id, 1));
        
        container.appendChild(itemDiv);
    });
    
    updateTotals();
    updateBadge();
}

// Update totals
function updateTotals() {
    let subtotal = 0;
    let totalDiscount = 0;
    let totalTax = 0; // Tax yang ditambahkan (tax_included = false)
    let totalIncludedTax = 0; // Tax yang sudah termasuk (tax_included = true) - untuk informasi saja
    
    cart.forEach(item => {
        const itemSubtotal = item.price * item.qty;
        subtotal += itemSubtotal;
        
        // Calculate promotion discount for this item
        const promoResult = calculatePromoDiscount(item, item.qty);
        totalDiscount += promoResult.discount;
        
        /**
         * LOGIC PAJAK (Sesuai Perpajakan Indonesia):
         * 
         * 1. tax_type = 'NONE': Produk tidak kena pajak (Rp 0)
         * 
         * 2. tax_included = true: Harga sudah TERMASUK pajak (tidak ditambahkan lagi)
         *    Contoh: Harga Rp 25.000 (sudah termasuk PPN 11%)
         *    Rumus: included_tax = (price / 1.11) * 0.11
         *    Info: Rp 25.000 = Rp 22.522 (DPP) + Rp 2.478 (PPN)
         *    
         * 3. tax_included = false: Harga BELUM termasuk pajak (pajak ditambahkan)
         *    Contoh: Harga Rp 10.000 + PPN 11% = Rp 11.100
         *    
         * 4. Pajak dihitung SETELAH discount:
         *    - Harga awal: Rp 100.000
         *    - Discount 20%: Rp 20.000
         *    - Harga setelah discount: Rp 80.000
         *    - PPN 11%: Rp 80.000 x 11% = Rp 8.800
         *    - Total bayar: Rp 88.800
         */
        if (item.tax_type !== 'NONE' && item.tax_rate > 0) {
            if (item.tax_included) {
                // Tax sudah termasuk dalam harga - hitung untuk informasi saja
                const priceAfterDiscount = itemSubtotal - promoResult.discount;
                const taxDivisor = 1 + (item.tax_rate / 100); // 1.11 untuk PPN 11%
                const includedTax = (priceAfterDiscount / taxDivisor) * (item.tax_rate / 100);
                totalIncludedTax += includedTax;
                
                console.log(`Tax INCLUDED for ${item.name}:`, {
                    subtotal: itemSubtotal,
                    discount: promoResult.discount,
                    priceAfterDiscount: priceAfterDiscount,
                    taxRate: item.tax_rate + '%',
                    includedTax: includedTax,
                    note: 'Tax sudah termasuk dalam harga (tidak menambah total)'
                });
            } else {
                // Tax belum termasuk - ditambahkan ke total
                const taxableAmount = itemSubtotal - promoResult.discount;
                const itemTax = taxableAmount * (item.tax_rate / 100);
                totalTax += itemTax;
                
                console.log(`Tax ADDED for ${item.name}:`, {
                    subtotal: itemSubtotal,
                    discount: promoResult.discount,
                    taxableAmount: taxableAmount,
                    taxRate: item.tax_rate + '%',
                    tax: itemTax,
                    note: 'Tax ditambahkan ke total'
                });
            }
        }
    });
    
    const total = subtotal - totalDiscount + totalTax;
    
    console.log('Payment Summary:', {
        subtotal: subtotal,
        discount: totalDiscount,
        taxAdded: totalTax,
        taxIncluded: totalIncludedTax,
        total: total
    });
    
    // Update display
    document.getElementById('subTotal').textContent = formatCurrency(subtotal);
    document.getElementById('discountAmount').textContent = '- ' + formatCurrency(totalDiscount);
    
    // Tax yang ditambahkan (tax_included = false)
    const taxAddedRow = document.getElementById('taxAddedRow');
    if (totalTax > 0) {
        taxAddedRow.style.display = 'flex';
        document.getElementById('taxAmount').textContent = formatCurrency(totalTax);
    } else {
        taxAddedRow.style.display = 'none';
    }
    
    // Tax yang sudah termasuk (tax_included = true) - informasi saja
    const taxIncludedRow = document.getElementById('taxIncludedRow');
    if (totalIncludedTax > 0) {
        taxIncludedRow.style.display = 'flex';
        document.getElementById('taxIncludedAmount').textContent = formatCurrency(totalIncludedTax);
    } else {
        taxIncludedRow.style.display = 'none';
    }
    
    document.getElementById('totalPayment').textContent = formatCurrency(total);
}

// Update badge
function updateBadge() {
    const badge = document.getElementById('cartBadge');
    const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
    
    if (totalItems > 0) {
        badge.textContent = totalItems;
        badge.style.display = 'flex';
    } else {
        badge.style.display = 'none';
    }
}

// Payment method selection
document.querySelectorAll('.payment-method').forEach(method => {
    method.addEventListener('click', function() {
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('active'));
        this.classList.add('active');
        selectedPaymentMethod = this.dataset.method;
    });
});

// Category filter
document.querySelectorAll('.category-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        const categoryId = this.dataset.categoryId;
        const allCards = document.querySelectorAll('.menu-card');
        
        if (categoryId === '0') {
            // Show all products
            allCards.forEach(card => {
                card.style.display = 'flex';
            });
        } else {
            // Filter by category
            allCards.forEach(card => {
                const cardCategoryId = card.dataset.categoryId;
                if (cardCategoryId === categoryId) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    });
});

// Place order - temp storage for checkout data
let pendingCheckoutData = null;

document.getElementById('btnPlaceOrder').addEventListener('click', function() {
    if (cart.length === 0) return;
    
    let subtotal = 0;
    let totalDiscount = 0;
    let totalTax = 0;
    
    // Calculate totals with promotions
    const itemsWithDiscount = cart.map(item => {
        const itemSubtotal = item.price * item.qty;
        subtotal += itemSubtotal;
        
        // Calculate promotion discount
        const promoResult = calculatePromoDiscount(item, item.qty);
        totalDiscount += promoResult.discount;
        
        // Calculate tax
        if (item.tax_type !== 'NONE' && item.tax_rate > 0 && !item.tax_included) {
            const taxableAmount = itemSubtotal - promoResult.discount;
            totalTax += taxableAmount * (item.tax_rate / 100);
        }
        
        return {
            product_id: item.id,
            qty: item.qty,
            discount: promoResult.discount,
            discount_note: promoResult.promo ? `Promo: ${promoResult.promo.name}` : null
        };
    });
    
    const totalAmount = subtotal - totalDiscount + totalTax;
    
    const paymentMethodMap = {
        'credit': 'credit',
        'e-wallet': 'ewallet',
        'cash': 'cash'
    };
    
    // Get order details
    const tableNumber = document.getElementById('tableNumber').value;
    const customerName = document.getElementById('customerName').value;
    
    // Validation based on order type
    if (selectedOrderType === 'dine_in' && !tableNumber) {
        alert('⚠️ Please enter table number for dine-in order!');
        return;
    }
    
    if ((selectedOrderType === 'take_away' || selectedOrderType === 'delivery') && !customerName) {
        alert('⚠️ Please enter customer name!');
        return;
    }
    
    // Store checkout data temporarily
    pendingCheckoutData = {
        items: itemsWithDiscount,
        order_type: selectedOrderType,
        table_number: selectedOrderType === 'dine_in' ? tableNumber : null,
        customer_name: (selectedOrderType === 'take_away' || selectedOrderType === 'delivery') ? customerName : null,
        payment_method: paymentMethodMap[selectedPaymentMethod] || 'cash',
        cash_amount: totalAmount,
        notes: null,
        totalAmount: totalAmount
    };
    
    // If cash payment, show cash modal
    if (selectedPaymentMethod === 'cash') {
        document.getElementById('cashModalTotal').textContent = formatCurrency(totalAmount);
        document.getElementById('cashInput').value = '';
        document.getElementById('changeDisplay').style.display = 'none';
        document.getElementById('insufficientAlert').style.display = 'none';
        document.getElementById('btnConfirmCash').disabled = true;
        
        const cashModal = new bootstrap.Modal(document.getElementById('cashModal'));
        cashModal.show();
        
        // Focus on input after modal shown
        setTimeout(() => document.getElementById('cashInput').focus(), 500);
    } else {
        // For non-cash, proceed directly
        processCheckout(pendingCheckoutData);
    }
});

// Quick amount buttons handler
document.querySelectorAll('.quick-amount-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const amount = parseFloat(this.dataset.amount);
        document.getElementById('cashInput').value = amount;
        document.getElementById('cashInput').dispatchEvent(new Event('input'));
    });
});

// Exact amount button handler
document.getElementById('btnExactAmount').addEventListener('click', function() {
    const total = pendingCheckoutData.totalAmount;
    document.getElementById('cashInput').value = total;
    document.getElementById('cashInput').dispatchEvent(new Event('input'));
});

// Cash input handler
document.getElementById('cashInput').addEventListener('input', function() {
    const cashValue = parseFloat(this.value) || 0;
    const total = pendingCheckoutData.totalAmount;
    const changeAmount = cashValue - total;
    
    if (cashValue >= total) {
        document.getElementById('changeDisplay').style.display = 'block';
        document.getElementById('changeAmount').textContent = formatCurrency(changeAmount);
        document.getElementById('insufficientAlert').style.display = 'none';
        document.getElementById('btnConfirmCash').disabled = false;
    } else {
        document.getElementById('changeDisplay').style.display = 'none';
        document.getElementById('insufficientAlert').style.display = 'block';
        document.getElementById('btnConfirmCash').disabled = true;
    }
});

// Cash input - allow Enter key
document.getElementById('cashInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && !document.getElementById('btnConfirmCash').disabled) {
        document.getElementById('btnConfirmCash').click();
    }
});

// Confirm cash payment
document.getElementById('btnConfirmCash').addEventListener('click', function() {
    const cashValue = parseFloat(document.getElementById('cashInput').value) || 0;
    pendingCheckoutData.cash_amount = cashValue;
    
    // Close modal
    bootstrap.Modal.getInstance(document.getElementById('cashModal')).hide();
    
    // Process checkout
    processCheckout(pendingCheckoutData);
});

// Process checkout function
function processCheckout(checkoutData) {
    const btn = document.getElementById('btnPlaceOrder');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
    
    fetch(checkoutUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(checkoutData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            const change = data.data.change_amount || 0;
            const total = data.data.grand_total;
            const paid = data.data.cash_amount;
            
            const orderTypeLabels = {
                'dine_in': '<i class="bi bi-shop"></i> Dine In',
                'take_away': '<i class="bi bi-bag"></i> Take Away',
                'delivery': '<i class="bi bi-truck"></i> Delivery'
            };
            
            let contentHTML = `
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #dee2e6;">
                    <span style="color: #6c757d; font-weight: 500;">Transaction Code:</span>
                    <strong style="color: #212529; font-family: monospace;">${data.data.transaction_code}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #dee2e6;">
                    <span style="color: #6c757d; font-weight: 500;">Order Type:</span>
                    <strong style="color: #212529;">${orderTypeLabels[selectedOrderType] || 'Order'}</strong>
                </div>
            `;
            
            if (selectedOrderType === 'dine_in' && checkoutData.table_number) {
                contentHTML += `
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #dee2e6;">
                        <span style="color: #6c757d; font-weight: 500;"><i class="bi bi-table"></i> Table:</span>
                        <strong style="color: #212529;">${checkoutData.table_number}</strong>
                    </div>
                `;
            } else if (checkoutData.customer_name) {
                contentHTML += `
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #dee2e6;">
                        <span style="color: #6c757d; font-weight: 500;"><i class="bi bi-person"></i> Customer:</span>
                        <strong style="color: #212529;">${checkoutData.customer_name}</strong>
                    </div>
                `;
            }
            
            contentHTML += `
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #dee2e6;">
                    <span style="color: #6c757d; font-weight: 500;">Total:</span>
                    <strong style="color: #3772F0; font-size: 1.125rem;">${formatCurrency(total)}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid #dee2e6;">
                    <span style="color: #6c757d; font-weight: 500;">Paid:</span>
                    <strong style="color: #212529;">${formatCurrency(paid)}</strong>
                </div>
            `;
            
            if (change > 0) {
                contentHTML += `
                    <div style="display: flex; justify-content: space-between; padding: 0.5rem 0;">
                        <span style="color: #6c757d; font-weight: 500;">Change:</span>
                        <strong style="color: #28a745; font-size: 1.125rem;">${formatCurrency(change)}</strong>
                    </div>
                `;
            }
            
            document.getElementById('successModalContent').innerHTML = contentHTML;
            
            // Show success modal
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
            
            // Generate and show receipt
            generateReceipt(data.data, checkoutData);
            
            // Clear cart and reset form
            cart = [];
            updateInvoice();
            document.getElementById('tableNumber').value = '';
            document.getElementById('customerName').value = '';
            
            if (window.innerWidth <= 992) {
                document.getElementById('invoicePanel').classList.remove('show');
                document.getElementById('invoiceBackdrop').classList.remove('show');
                document.body.style.overflow = '';
            }
        } else {
            alert('❌ Order failed!\n\n' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ An error occurred during checkout!');
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = originalText;
    });
}

// Generate receipt
function generateReceipt(transactionData, orderData) {
    const now = new Date();
    const dateStr = now.toLocaleDateString('id-ID', { 
        year: 'numeric', month: '2-digit', day: '2-digit' 
    });
    const timeStr = now.toLocaleTimeString('id-ID', { 
        hour: '2-digit', minute: '2-digit' 
    });
    
    const orderTypeLabels = {
        'dine_in': 'Dine In',
        'take_away': 'Take Away',
        'delivery': 'Delivery'
    };
    
    let receiptHTML = `
        <div class="receipt-header">
            <div class="receipt-title">${outletData.name || 'OUTLET'}</div>
            <div class="receipt-info">${outletData.address || ''}</div>
            <div class="receipt-info">Tel: ${outletData.phone || ''}</div>
        </div>
        
        <div class="receipt-divider"></div>
        
        <div class="receipt-info">
            <div class="receipt-row">
                <span>No:</span>
                <span>${transactionData.transaction_code}</span>
            </div>
            <div class="receipt-row">
                <span>Date:</span>
                <span>${dateStr} ${timeStr}</span>
            </div>
            <div class="receipt-row">
                <span>Type:</span>
                <span>${orderTypeLabels[orderData.order_type] || 'Order'}</span>
            </div>
    `;
    
    if (orderData.table_number) {
        receiptHTML += `<div class="receipt-row"><span>Table:</span><span>${orderData.table_number}</span></div>`;
    }
    
    if (orderData.customer_name) {
        receiptHTML += `<div class="receipt-row"><span>Customer:</span><span>${orderData.customer_name}</span></div>`;
    }
    
    receiptHTML += `
            <div class="receipt-row">
                <span>Cashier:</span>
                <span>${currentUser.username || 'System'}</span>
            </div>
        </div>
        
        <div class="receipt-divider"></div>
        
        <div class="receipt-items">
    `;
    
    // Add cart items
    cart.forEach(item => {
        const itemTotal = item.price * item.qty;
        receiptHTML += `
            <div class="receipt-item">
                <div style="font-weight: bold;">${item.name}</div>
                <div class="receipt-row">
                    <span>${item.qty} x ${formatCurrency(item.price)}</span>
                    <span>${formatCurrency(itemTotal)}</span>
                </div>
            </div>
        `;
    });
    
    receiptHTML += `
        </div>
        
        <div class="receipt-divider"></div>
        
        <div class="receipt-row">
            <span>Subtotal:</span>
            <span>${formatCurrency(transactionData.grand_total)}</span>
        </div>
        <div class="receipt-row receipt-total">
            <span>TOTAL:</span>
            <span>${formatCurrency(transactionData.grand_total)}</span>
        </div>
        <div class="receipt-row">
            <span>Paid:</span>
            <span>${formatCurrency(transactionData.cash_amount)}</span>
        </div>
        <div class="receipt-row">
            <span>Change:</span>
            <span>${formatCurrency(transactionData.change_amount)}</span>
        </div>
        
        <div class="receipt-footer">
            Thank You!<br>
            Please Come Again
        </div>
    `;
    
    document.getElementById('receiptContent').innerHTML = receiptHTML;
    
    // Show receipt modal
    const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
    receiptModal.show();
}

// Print receipt function
function printReceipt() {
    window.print();
}
