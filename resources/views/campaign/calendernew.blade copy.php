@extends('layouts.master')
@section('title')
    Campaign Calendar | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection

@section('content')
<div class="right_col" role="main">
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Campaign Calendar</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load FullCalendar setelah semua library master dimuat -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

<style>
.fc-view-harness {
    background-color: #fff;
}
.fc-day-today {
    background-color: #f0f8ff !important;
}
.fc th {
    padding: 10px 0px;
    background-color: #f4f4f4;
    border-left: 1px solid #ddd !important;
    border-right: 1px solid #ddd !important;
}
.fc-scroller {
    overflow: hidden !important;
}
.fc-day-number {
    padding: 5px;
}
.fc-daygrid-day {
    height: 100px !important;
    border: 1px solid #ddd !important;
}

/* Custom styles for campaign info */
.campaign-info {
    font-size: 9px;
    line-height: 1.1;
    margin-top: 2px;
    padding: 1px;
    position: absolute;
    bottom: 2px;
    left: 2px;
    right: 2px;
}

.quota-info {
    background: #e3f2fd;
    border-radius: 2px;
    padding: 1px 2px;
    margin: 1px 0;
    display: block;
    font-weight: 500;
    color: #1565c0;
    text-align: center;
}

.sent-info {
    background: #e8f5e8;
    border-radius: 2px;
    padding: 1px 2px;
    margin: 1px 0;
    display: block;
    color: #2e7d32;
    text-align: center;
}

.reserved-info {
    background: #fff8e1;
    border-radius: 2px;
    padding: 1px 2px;
    margin: 1px 0;
    display: block;
    color: #f57c00;
    text-align: center;
}

.fc-daygrid-day-events {
    margin-top: 0 !important;
    min-height: 60px;
}

.fc-daygrid-day-top {
    flex-direction: row;
    justify-content: space-between;
}

.fc-daygrid-day-frame {
    position: relative;
}

/* Custom styles for campaign events */
.campaign-event {
    font-size: 10px !important;
    padding: 1px 3px !important;
    margin: 1px 0 !important;
    border-radius: 3px !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    transition: opacity 0.2s ease !important;
}

.campaign-event:hover {
    opacity: 0.8 !important;
}

.quota-event {
    background-color: #e3f2fd !important;
    color: #1565c0 !important;
    border-color: #1565c0 !important;
}

.sent-event {
    background-color: #e8f5e8 !important;
    color: #2e7d32 !important;
    border-color: #2e7d32 !important;
}

.reserved-event {
    background-color: #fff3e0 !important;
    color: #e65100 !important;
    border-color: #e65100 !important;
}

.fc-daygrid-day {
    height: 140px !important;
    border: 1px solid #ddd !important;
}

.fc-event-title {
    font-size: 9px !important;
    line-height: 1.2 !important;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Modal styles */
.event-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.event-modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    border-radius: 8px;
    width: 80%;
    max-width: 500px;
    position: relative;
}

.event-modal-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    position: absolute;
    right: 15px;
    top: 10px;
}

.event-modal-close:hover {
    color: black;
}

.event-detail {
    margin: 10px 0;
}

.event-detail strong {
    display: inline-block;
    width: 120px;
    color: #333;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendarData = {};
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        height: 650,
        contentHeight: 600,
        aspectRatio: 1.5,
        themeSystem: 'bootstrap',
        locale: 'en',
        buttonText: {
            today: 'Today'
        },
        dayMaxEvents: true,
        views: {
            dayGridMonth: {
                dayMaxEvents: 6,
                fixedWeekCount: false
            }
        },
        events: function(info, successCallback, failureCallback) {
            // Fix: Use info.start directly but get the correct month
            // FullCalendar's info.start is the first day shown in the view
            // For month view, this could be from previous month
            
            // Get the actual month being displayed by using the view's title or center date
            const viewStart = new Date(info.start);
            const viewEnd = new Date(info.end);
            
            // Calculate the middle date of the view to get the correct month
            const middleDate = new Date(viewStart.getTime() + (viewEnd.getTime() - viewStart.getTime()) / 2);
            
            const year = middleDate.getFullYear();
            const month = middleDate.getMonth() + 1; // JavaScript months are 0-indexed
            
            console.log(`Loading data for ${year}-${month}`);
            console.log('View start:', viewStart);
            console.log('View end:', viewEnd);
            console.log('Middle date:', middleDate);
            
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
                        successCallback(events);
                    } else {
                        console.error('Invalid API response structure:', data);
                        successCallback([]);
                    }
                })
                .catch(error => {
                    console.error('Error fetching calendar data:', error);
                    successCallback([]);
                });
        },
        eventDisplay: 'block',
        eventTextColor: '#000',
        eventBorderWidth: 0,
        eventClassNames: function(arg) {
            if (arg.extendedProps && arg.extendedProps.type) {
                if (arg.extendedProps.type === 'campaign') {
                    return ['campaign-event'];
                }
                return ['campaign-event', arg.extendedProps.type + '-event'];
            }
            return ['campaign-event'];
        },
        eventDidMount: function(info) {
            if (info.event.extendedProps && info.event.extendedProps.type) {
                info.el.classList.add(info.event.extendedProps.type + '-event');
            }
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
                backgroundColor: '#e3f2fd',
                borderColor: '#1565c0',
                textColor: '#1565c0',
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
                // For sent campaigns, show subject and sent info
                if (campaign.sent_at || campaign.actual_emails_sent) {
                    events.push({
                        title: `${campaign.subject} (Sent)`,
                        start: date,
                        allDay: true,
                        backgroundColor: '#e8f5e8',
                        borderColor: '#2e7d32',
                        textColor: '#2e7d32',
                        extendedProps: {
                            type: 'sent',
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
                } else {
                    // For reserved/scheduled campaigns
                    events.push({
                        title: `${campaign.subject} (${campaign.unit})`,
                        start: date,
                        allDay: true,
                        backgroundColor: '#fff3e0',
                        borderColor: '#e65100',
                        textColor: '#e65100',
                        extendedProps: {
                            type: 'reserved',
                            details: {
                                id: campaign.id,
                                subject: campaign.subject,
                                unit: campaign.unit,
                                email_count: campaign.email_count,
                                status: campaign.status,
                                type: campaign.type
                            }
                        }
                    });
                }
            });
        }
        
        return events;
    }
    
    calendar.render();
});
</script>
@endsection