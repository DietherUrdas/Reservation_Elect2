// Populate day dropdowns
function populateDays(monthSelect, daySelect, selectedDay) {
    const daysInMonth = {
        'January': 31, 'February': 28, 'March': 31, 'April': 30,
        'May': 31, 'June': 30, 'July': 31, 'August': 31,
        'September': 30, 'October': 31, 'November': 30, 'December': 31
    };

    daySelect.innerHTML = '<option value="">Day</option>';
    const month = monthSelect.value;
    const days = daysInMonth[month] || 31;

    for (let i = 1; i <= days; i++) {
        const option = document.createElement('option');
        option.value = i;
        option.textContent = i;
        if (selectedDay && i == selectedDay) {
            option.selected = true;
        }
        daySelect.appendChild(option);
    }
}

// Populate year dropdowns
function populateYears(yearSelect, startYear, endYear, selectedYear) {
    yearSelect.innerHTML = '<option value="">Year</option>';
    for (let year = startYear; year <= endYear; year++) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        if (selectedYear && year == selectedYear) {
            option.selected = true;
        }
        yearSelect.appendChild(option);
    }
}

// Initialize date dropdowns
document.addEventListener('DOMContentLoaded', function() {
    const fromMonth = document.getElementById('fromMonth');
    const fromDay = document.getElementById('fromDay');
    const fromYear = document.getElementById('fromYear');
    const toMonth = document.getElementById('toMonth');
    const toDay = document.getElementById('toDay');
    const toYear = document.getElementById('toYear');

    // Populate years (2000-2010 for the 2006 context)
    populateYears(fromYear, 2000, 2010, 2006);
    populateYears(toYear, 2000, 2010, 2006);

    // Populate days for initial month selection
    populateDays(fromMonth, fromDay, 27);
    populateDays(toMonth, toDay, 30);

    // Update days when month changes
    fromMonth.addEventListener('change', function() {
        populateDays(fromMonth, fromDay);
    });

    toMonth.addEventListener('change', function() {
        populateDays(toMonth, toDay);
    });
});

// Clear form function
function clearForm() {
    document.getElementById('reservationForm').reset();
    
    // Reset date dropdowns
    const fromMonth = document.getElementById('fromMonth');
    const fromDay = document.getElementById('fromDay');
    const toMonth = document.getElementById('toMonth');
    const toDay = document.getElementById('toDay');
    
    populateDays(fromMonth, fromDay);
    populateDays(toMonth, toDay);
    
    // Reset radio buttons to default
    document.querySelector('input[name="roomType"][value="Suite"]').checked = true;
    document.querySelector('input[name="roomCapacity"][value="Family"]').checked = true;
    document.querySelector('input[name="paymentType"][value="Cash"]').checked = true;
}

// Calculate total bill based on room type, capacity, payment type, and number of nights
function calculateTotalBill(roomType, roomCapacity, paymentType, fromMonth, fromDay, fromYear, toMonth, toDay, toYear) {
    // Room pricing per night (Rate/day)
    const roomPrices = {
        'Single': {
            'Regular': 100.00,
            'De Luxe': 300.00,
            'Suite': 500.00
        },
        'Double': {
            'Regular': 200.00,
            'De Luxe': 500.00,
            'Suite': 800.00
        },
        'Family': {
            'Regular': 500.00,
            'De Luxe': 750.00,
            'Suite': 1000.00
        }
    };
    
    // Calculate number of nights
    const checkInDate = new Date(`${fromMonth} ${fromDay}, ${fromYear}`);
    const checkOutDate = new Date(`${toMonth} ${toDay}, ${toYear}`);
    const timeDiff = checkOutDate - checkInDate;
    const nights = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
    const totalNights = nights > 0 ? nights : 1;
    
    // Get price per night
    const pricePerNight = roomPrices[roomCapacity] && roomPrices[roomCapacity][roomType] 
        ? roomPrices[roomCapacity][roomType] 
        : 0;
    
    // Calculate base total bill (before discounts/charges)
    let baseTotalBill = pricePerNight * totalNights;
    
    // Apply payment type charges or cash discounts
    let additionalCharge = 0;
    let discount = 0;
    let discountPercent = 0;
    
    if (paymentType === 'Cash') {
        // Cash discounts based on number of nights
        if (totalNights >= 6) {
            discountPercent = 15; // 15% discount for 6 days and above
        } else if (totalNights >= 3) {
            discountPercent = 10; // 10% discount for 3-5 days
        }
        discount = baseTotalBill * (discountPercent / 100);
    } else if (paymentType === 'Cheque') {
        additionalCharge = baseTotalBill * 0.05; // +5% for Check
    } else if (paymentType === 'Credit Card') {
        additionalCharge = baseTotalBill * 0.10; // +10% for Credit Card
    }
    
    // Calculate final total bill
    const totalBill = baseTotalBill - discount + additionalCharge;
    
    return {
        totalNights: totalNights,
        pricePerNight: pricePerNight,
        baseTotalBill: baseTotalBill,
        discount: discount,
        discountPercent: discountPercent,
        additionalCharge: additionalCharge,
        totalBill: totalBill,
        paymentType: paymentType
    };
}

// Validate form fields
function validateForm() {
    const customerName = document.getElementById('customerName').value.trim();
    const contactNumber = document.getElementById('contactNumber').value.trim();
    const fromMonth = document.getElementById('fromMonth').value;
    const fromDay = document.getElementById('fromDay').value;
    const fromYear = document.getElementById('fromYear').value;
    const toMonth = document.getElementById('toMonth').value;
    const toDay = document.getElementById('toDay').value;
    const toYear = document.getElementById('toYear').value;
    const roomType = document.querySelector('input[name="roomType"]:checked');
    const roomCapacity = document.querySelector('input[name="roomCapacity"]:checked');
    const paymentType = document.querySelector('input[name="paymentType"]:checked');
    
    // Check for incomplete information
    const missingFields = [];
    
    if (!customerName) missingFields.push('Customer Name');
    if (!contactNumber) missingFields.push('Contact Number');
    if (!fromMonth || !fromDay || !fromYear) missingFields.push('Check-in Date');
    if (!toMonth || !toDay || !toYear) missingFields.push('Check-out Date');
    
    if (missingFields.length > 0) {
        alert('Please supply all necessary information.\n\nMissing fields:\n- ' + missingFields.join('\n- '));
        return false;
    }
    
    // Validate that checkout date is after check-in date
    if (fromMonth && fromDay && fromYear && toMonth && toDay && toYear) {
        const checkInDate = new Date(`${fromMonth} ${fromDay}, ${fromYear}`);
        const checkOutDate = new Date(`${toMonth} ${toDay}, ${toYear}`);
        
        if (checkOutDate <= checkInDate) {
            alert('Check-out date must be after check-in date.');
            return false;
        }
    }
    
    // Check room capacity selection
    if (!roomCapacity) {
        alert('No selected room capacity');
        return false;
    }
    
    // Check room type selection
    if (!roomType) {
        alert('No selected room type');
        return false;
    }
    
    // Check payment type selection
    if (!paymentType) {
        alert('No selected type of payment');
        return false;
    }
    
    return true;
}

// Form submission handler - validate before PHP submission
document.getElementById('reservationForm').addEventListener('submit', function(e) {
    // Validate all fields and selections
    if (!validateForm()) {
        e.preventDefault();
        return false;
    }
    
    // Show loading overlay
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.classList.add('active');
    }
    
    // Form will submit to PHP, no need to prevent default or redirect
    return true;
});
