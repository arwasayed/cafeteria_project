document.addEventListener('DOMContentLoaded', function() {
    const orderItems = document.querySelectorAll('.order_items .item');
    const totalAmountDisplay = document.getElementById('totalAmount');
    const totalAmountHidden = document.getElementById('totalAmountHidden');
    let totalAmount = 0;
    orderItems.forEach(function(item) {
        const quantityInput = item.querySelector('.quantity');
        const priceSpan = item.querySelector('.price');
        const price = parseFloat(priceSpan.textContent.replace('EGP ', ''));
        quantityInput.addEventListener('input', function() {
            updateTotal();
        });
    });
    function updateTotal() {
        totalAmount = 0;
        orderItems.forEach(function(item) {
            const quantityInput = item.querySelector('.quantity');
            const priceSpan = item.querySelector('.price');
            const price = parseFloat(priceSpan.textContent.replace('EGP ', ''));
            totalAmount += parseInt(quantityInput.value) * price;
        });
        totalAmountDisplay.textContent = totalAmount.toFixed(2);
        totalAmountHidden.value = totalAmount.toFixed(2);
    }
});