function trackPackage(trackingNumber) {
    // Display the modal
    const modal = document.getElementById('trackingModal');
    document.getElementById('trackingNumDisplay').textContent = trackingNumber;
    modal.style.display = 'flex';
    
    // Here you would typically make an API call to get tracking updates
    // fetchTrackingUpdates(trackingNumber);
}

function closeModal() {
    document.getElementById('trackingModal').style.display = 'none';
}

// Close modal when clicking outside content
window.onclick = function(event) {
    const modal = document.getElementById('trackingModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Example API call function (would need backend implementation)
async function fetchTrackingUpdates(trackingNumber) {
    try {
        const response = await fetch(`/api/tracking/${trackingNumber}`);
        const data = await response.json();
        // Update the modal with real tracking data
        updateTrackingInfo(data);
    } catch (error) {
        console.error('Error fetching tracking info:', error);
    }
}