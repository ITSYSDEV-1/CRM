document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendarData = {};
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: ''
        },
        aspectRatio: 1.35,
        themeSystem: 'bootstrap',
        locale: 'en',
        dayMaxEvents: false,
        views: {
            dayGridMonth: {
                fixedWeekCount: true
            }
        },
        events: function(info, successCallback, failureCallback) {
            // Add loading animation
            showLoadingAnimation();
            
            // Get the actual month being displayed by using the view's title or center date
            const viewStart = new Date(info.start);
            const viewEnd = new Date(info.end);
            
            // Calculate the middle date of the view to get the correct month
            const middleDate = new Date(viewStart.getTime() + (viewEnd.getTime() - viewStart.getTime()) / 2);
            
            const year = middleDate.getFullYear();
            const month = middleDate.getMonth() + 1; // JavaScript months are 0-indexed
            
            console.log(`Loading data for ${year}-${month}`);
            
            fetch(`/campaign-calendar/monthly?year=${year}&month=${month}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('API Response:', data);
                    if (data.success && data.data && data.data.daily_breakdown) {
                        const events = [];
                        
                        data.data.daily_breakdown.forEach(day => {
                            const dayEvents = createEventsForDay(day);
                            events.push(...dayEvents);
                        });
                        
                        console.log('Created events:', events);
                        hideLoadingAnimation();
                        successCallback(events);
                    } else {
                        console.error('Invalid API response structure:', data);
                        hideLoadingAnimation();
                        successCallback([]);
                    }
                })
                .catch(error => {
                    console.error('Error fetching calendar data:', error);
                    hideLoadingAnimation();
                    successCallback([]);
                });
        },
        eventDisplay: 'block',
        eventTextColor: '#000',
        eventBorderWidth: 0,
        eventClassNames: function(arg) {
            if (arg.extendedProps && arg.extendedProps.details && arg.extendedProps.details.status) {
                const status = arg.extendedProps.details.status.toLowerCase();
                if (status === 'cancelled') {
                    return ['campaign-event', 'cancelled-event'];
                }
            }
            
            if (arg.extendedProps && arg.extendedProps.type) {
                if (arg.extendedProps.type === 'campaign') {
                    return ['campaign-event'];
                }
                return ['campaign-event', arg.extendedProps.type + '-event'];
            }
            return ['campaign-event'];
        },
        eventDidMount: function(info) {
            // Check for cancelled status first
            if (info.event.extendedProps && info.event.extendedProps.details && info.event.extendedProps.details.status) {
                const status = info.event.extendedProps.details.status.toLowerCase();
                if (status === 'cancelled') {
                    info.el.classList.add('cancelled-event');
                    // Remove other event type classes if they exist
                    info.el.classList.remove('reserved-event', 'sent-event', 'quota-event');
                }
            }
            
            // Add original type class if not cancelled
            if (info.event.extendedProps && info.event.extendedProps.type && 
                (!info.event.extendedProps.details || info.event.extendedProps.details.status !== 'cancelled')) {
                info.el.classList.add(info.event.extendedProps.type + '-event');
            }
            
            // Add hover effects only for events
            info.el.addEventListener('mouseenter', function() {
                this.style.zIndex = '100';
            });
            
            info.el.addEventListener('mouseleave', function() {
                this.style.zIndex = '1';
            });
        },
        // Tambahkan event untuk menghapus hover effect pada tanggal kosong
        dayCellDidMount: function(info) {
            // Cek apakah tanggal ini memiliki events
            const hasEvents = calendar.getEvents().some(event => {
                const eventDate = new Date(event.start);
                const cellDate = new Date(info.date);
                return eventDate.toDateString() === cellDate.toDateString();
            });
            
            // Jika tidak ada events, hapus hover effect
            if (!hasEvents) {
                info.el.style.cursor = 'default';
                info.el.addEventListener('mouseenter', function(e) {
                    e.stopPropagation();
                });
            }
        },
        eventClick: function(info) {
            showEventModal(info.event);
        }
    });
    
    function createEventsForDay(dayData) {
        const events = [];
        const date = dayData.date;
        
        // Only show quota event if can_book is true and not historical
        if (dayData.can_book && !dayData.is_historical && dayData.quota_info && dayData.quota_info.daily_quota) {
            events.push({
                title: `Available: ${dayData.quota_info.available_quota || 0}`,
                start: date,
                allDay: true,
                backgroundColor: 'transparent',
                borderColor: 'transparent',
                textColor: '#1e40af',
                extendedProps: {
                    type: 'quota',
                    details: {
                        available_quota: dayData.quota_info.available_quota,
                        daily_quota: dayData.quota_info.daily_quota,
                        used_quota: dayData.quota_info.used_quota,
                        utilization_rate: dayData.quota_info.utilization_rate
                    }
                }
            });
        }
        
        // Create individual campaign events with details
        if (dayData.campaigns && dayData.campaigns.length > 0) {
            dayData.campaigns.forEach((campaign, index) => {
                // Determine event type and color based on status first
                let eventType = 'reserved';
                let textColor = '#92400e';
                let icon = '📅';
                
                // Check if cancelled first
                if (campaign.status && campaign.status.toLowerCase() === 'cancelled') {
                    eventType = 'cancelled';
                    textColor = '#dc2626';
                    icon = '❌';
                } else if (campaign.sent_at || campaign.actual_emails_sent) {
                    eventType = 'sent';
                    textColor = '#166534';
                    icon = '📧';
                }
                
                events.push({
                    title: `${icon} ${campaign.subject}`,
                    start: date,
                    allDay: true,
                    backgroundColor: 'transparent',
                    borderColor: 'transparent',
                    textColor: textColor,
                    extendedProps: {
                        type: eventType,
                        details: {
                            id: campaign.id,
                            subject: campaign.subject,
                            unit: campaign.unit,
                            email_count: campaign.email_count,
                            status: campaign.status,
                            type: campaign.type,
                            sent_at: campaign.sent_at,
                            actual_emails_sent: campaign.actual_emails_sent
                        }
                    }
                });
            });
        }
        
        return events;
    }
    
    // Loading animation functions
    function showLoadingAnimation() {
        const calendarEl = document.getElementById('calendar');
        if (!document.querySelector('.loading-overlay')) {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'loading-overlay';
            loadingOverlay.innerHTML = `
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Loading calendar data...</p>
                </div>
            `;
            loadingOverlay.style.cssText = `
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(248, 250, 252, 0.9);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1000;
                border-radius: 12px;
            `;
            
            const spinner = loadingOverlay.querySelector('.spinner');
            spinner.style.cssText = `
                width: 40px;
                height: 40px;
                border: 4px solid #e2e8f0;
                border-top: 4px solid #3b82f6;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin-bottom: 10px;
            `;
            
            // Add CSS animation
            if (!document.querySelector('#spinner-style')) {
                const style = document.createElement('style');
                style.id = 'spinner-style';
                style.textContent = `
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                `;
                document.head.appendChild(style);
            }
            
            calendarEl.style.position = 'relative';
            calendarEl.appendChild(loadingOverlay);
        }
    }
    
    function hideLoadingAnimation() {
        const loadingOverlay = document.querySelector('.loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }
    
    // Modal functions
    function showEventModal(event) {
        const modal = createModal();
        const modalContent = modal.querySelector('.event-modal-content');
        
        // Set modal title based on event type and details
        const title = getModalTitle(event.extendedProps.type, event.extendedProps.details);
        modalContent.querySelector('.event-modal-title').textContent = title;
        
        // Set modal body content
        const modalBody = modalContent.querySelector('.event-modal-body');
        modalBody.innerHTML = generateModalContent(event);
        
        // Show modal
        document.body.appendChild(modal);
        modal.style.display = 'block';
        
        // Close modal handlers
        const closeBtn = modal.querySelector('.event-modal-close');
        closeBtn.onclick = () => closeModal(modal);
        
        modal.onclick = (e) => {
            if (e.target === modal) {
                closeModal(modal);
            }
        };
        
        // ESC key handler
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                closeModal(modal);
                document.removeEventListener('keydown', escHandler);
            }
        });
    }
    
    function createModal() {
        const modal = document.createElement('div');
        modal.className = 'event-modal';
        modal.innerHTML = `
            <div class="event-modal-content">
                <div class="event-modal-header">
                    <h3 class="event-modal-title"></h3>
                    <button class="event-modal-close">&times;</button>
                </div>
                <div class="event-modal-body"></div>
            </div>
        `;
        return modal;
    }
    
    function getModalTitle(eventType, details) {
        if (eventType === 'quota') {
            return 'Quota Information';
        }
        
        // Determine title based on status
        if (details && details.status) {
            const status = details.status.toLowerCase();
            
            if (status === 'cancelled') {
                return 'Cancelled Campaign';
            } else if (status === 'approved') {
                return 'Approved Campaign';
            } else if (status.includes('sent') || status.includes('delivered')) {
                return 'Sent Campaign';
            } else if (status.includes('scheduled') || status.includes('pending')) {
                return 'Scheduled Campaign';
            } else if (status === 'draft') {
                return 'Draft Campaign';
            }
        }
        
        // Fallback based on event type
        switch(eventType) {
            case 'sent':
                return 'Sent Campaign';
            case 'reserved':
                return 'Scheduled Campaign';
            default:
                return 'Campaign Details';
        }
    }
    
    function generateModalContent(event) {
        const details = event.extendedProps.details;
        const eventType = event.extendedProps.type;
        
        if (eventType === 'quota') {
            return `
                <div class="event-detail">
                    <span class="event-detail-label">Date:</span>
                    <span class="event-detail-value">${formatDate(event.start)}</span>
                </div>
                <div class="event-detail">
                    <span class="event-detail-label">Daily Quota:</span>
                    <span class="event-detail-value">${details.daily_quota || 'N/A'}</span>
                </div>
                <div class="event-detail">
                    <span class="event-detail-label">Available:</span>
                    <span class="event-detail-value">${details.available_quota || 0}</span>
                </div>
                <div class="event-detail">
                    <span class="event-detail-label">Used:</span>
                    <span class="event-detail-value">${details.used_quota || 0}</span>
                </div>
                <div class="event-detail">
                    <span class="event-detail-label">Utilization:</span>
                    <span class="event-detail-value">${details.utilization_rate || 0}%</span>
                </div>
            `;
        } else {
            const statusClass = getStatusClass(details.status);
            return `
                <div class="event-detail">
                    <span class="event-detail-label">Subject:</span>
                    <span class="event-detail-value">${details.subject || 'N/A'}</span>
                </div>
                <div class="event-detail">
                    <span class="event-detail-label">Unit:</span>
                    <span class="event-detail-value">${details.unit || 'N/A'}</span>
                </div>
                <div class="event-detail">
                    <span class="event-detail-label">Email Count:</span>
                    <span class="event-detail-value">${details.email_count || 0}</span>
                </div>
                <div class="event-detail">
                    <span class="event-detail-label">Status:</span>
                    <span class="event-detail-value">
                        <span class="event-status-badge ${statusClass}">${details.status || 'Unknown'}</span>
                    </span>
                </div>
                <div class="event-detail">
                    <span class="event-detail-label">Type:</span>
                    <span class="event-detail-value">${details.type || 'N/A'}</span>
                </div>
                ${details.sent_at ? `
                    <div class="event-detail">
                        <span class="event-detail-label">Sent At:</span>
                        <span class="event-detail-value">${formatDateTime(details.sent_at)}</span>
                    </div>
                ` : ''}
                ${details.actual_emails_sent ? `
                    <div class="event-detail">
                        <span class="event-detail-label">Emails Sent:</span>
                        <span class="event-detail-value">${details.actual_emails_sent}</span>
                    </div>
                ` : ''}
            `;
        }
    }
    
    function getStatusClass(status) {
        if (!status) return 'status-draft';
        
        const statusLower = status.toLowerCase();
        
        if (statusLower === 'cancelled') {
            return 'status-cancelled';
        } else if (statusLower === 'approved') {
            return 'status-approved';
        } else if (statusLower.includes('sent') || statusLower.includes('delivered')) {
            return 'status-sent';
        } else if (statusLower.includes('scheduled') || statusLower.includes('pending')) {
            return 'status-scheduled';
        } else if (statusLower === 'draft') {
            return 'status-draft';
        }
        
        return 'status-draft';
    }
    
    function formatDate(date) {
        return new Date(date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
    
    function formatDateTime(dateTime) {
        return new Date(dateTime).toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    function closeModal(modal) {
        modal.style.display = 'none';
        document.body.removeChild(modal);
    }
    
    calendar.render();
});

// Function to go back to previous page
function goBack() {
    // Check if there's a previous page in history
    if (window.history.length > 1) {
        window.history.back();
    } else {
        // Fallback: redirect to campaign list or dashboard
        window.location.href = '/campaign'; // Adjust this URL according to your routes
    }
}

// Alternative function with confirmation (optional)
function goBackWithConfirmation() {
    if (confirm('Are you sure you want to leave this page?')) {
        goBack();
    }
}