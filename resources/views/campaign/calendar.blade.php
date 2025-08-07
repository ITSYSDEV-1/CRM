@extends('layouts.master')
@section('title')
    Campaign Calendar | {{ $configuration->hotel_name.' '.$configuration->app_title }}
@endsection

@section('content')
<div class="right_col" role="main">
    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                    <div class="x_panel tile">
                        <div class="x_title">
                            <h3>Campaign Calendar</h3>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <!-- Calendar Navigation -->
                            <div class="calendar-navigation">
                                <div>
                                    <button type="button" class="nav-button" onclick="previousMonth()">
                                        <i class="fa fa-chevron-left"></i> Previous
                                    </button>
                                    <button type="button" class="nav-button" onclick="nextMonth()">
                                        Next <i class="fa fa-chevron-right"></i>
                                    </button>
                                </div>
                                <div>
                                    <h4 id="currentMonthYear" class="calendar-title"></h4>
                                </div>
                                <div>
                                    <button type="button" class="today-button" onclick="goToToday()">
                                        <i class="fa fa-calendar"></i> Today
                                    </button>
                                </div>
                            </div>

                            <!-- Monthly Summary -->
                            <div id="monthlySummary" class="row" style="margin-bottom: 20px; display: none;">
                                <div class="col-md-2">
                                    <div class="panel panel-primary">
                                        <div class="panel-body text-center">
                                            <h4 id="totalCampaigns">0</h4>
                                            <p class="text-muted">Total Campaigns</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="panel panel-success">
                                        <div class="panel-body text-center">
                                            <h4 id="approvedCampaigns">0</h4>
                                            <p class="text-muted">Approved</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="panel panel-warning">
                                        <div class="panel-body text-center">
                                            <h4 id="pendingCampaigns">0</h4>
                                            <p class="text-muted">Pending</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="panel panel-info">
                                        <div class="panel-body text-center">
                                            <h4 id="sentCampaigns">0</h4>
                                            <p class="text-muted">Sent</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="panel panel-default">
                                        <div class="panel-body text-center">
                                            <h4 id="totalEmailsSent">0</h4>
                                            <p class="text-muted">Emails Sent</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="panel panel-default">
                                        <div class="panel-body text-center">
                                            <h4 id="quotaUtilization">0%</h4>
                                            <p class="text-muted">Quota Used</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Loading Indicator -->
                            <div id="loadingIndicator" class="text-center" style="display: none;">
                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                <p>Loading calendar data...</p>
                            </div>

                            <!-- Error Message -->
                            <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>

                            <!-- Calendar Grid -->
                            <div id="calendarGrid" class="calendar-container">
                                <div class="calendar-grid" id="calendarGridContent">
                                    <!-- Calendar will be rendered here -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<!-- Campaign Detail Modal -->
<div class="modal fade" id="campaignDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Campaign Details</h4>
            </div>
            <div class="modal-body" id="campaignDetailContent">
                <!-- Campaign details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<style>
.calendar-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
    margin-top: 20px;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background-color: #e0e0e0;
}

.calendar-header-day {
    background: #34495e;
    color: white;
    padding: 15px 10px;
    text-align: center;
    font-weight: bold;
    font-size: 14px;
}

.calendar-day {
    background: #fff;
    min-height: 100px;
    padding: 8px;
    position: relative;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid #ecf0f1;
}

.calendar-day:hover {
    background: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.calendar-day.other-month {
    background: #f8f9fa;
    color: #95a5a6;
}

.calendar-day.today {
    background: #e3f2fd;
    border: 2px solid #2196f3;
    font-weight: bold;
}

.calendar-day.has-campaigns {
    background: #fff8e1;
    border-left: 4px solid #ff9800;
}

.calendar-day.historical {
    background: #f5f5f5;
    color: #7f8c8d;
}

.day-number {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 5px;
    color: #2c3e50;
}

.day-info {
    font-size: 11px;
    line-height: 1.2;
}

.quota-info {
    background: #e8f5e8;
    padding: 2px 4px;
    border-radius: 3px;
    margin-bottom: 2px;
    font-size: 10px;
    text-align: center;
}

.quota-info.low {
    background: #ffebee;
    color: #c62828;
}

.quota-info.medium {
    background: #fff3e0;
    color: #ef6c00;
}

.quota-info.high {
    background: #e8f5e8;
    color: #2e7d32;
}

.campaign-indicator {
    background: #3498db;
    color: white;
    padding: 1px 4px;
    border-radius: 2px;
    font-size: 9px;
    margin-bottom: 1px;
    display: block;
    text-align: center;
}

.campaign-indicator.sent {
    background: #27ae60;
}

.campaign-indicator.pending {
    background: #f39c12;
}

.campaign-indicator.approved {
    background: #3498db;
}

.status-indicator {
    position: absolute;
    top: 5px;
    right: 5px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #27ae60;
}

.status-indicator.cannot-book {
    background: #e74c3c;
}

.status-indicator.historical {
    background: #95a5a6;
}

.calendar-title {
    margin: 0;
    font-weight: 600;
    color: #2c3e50;
    font-size: 24px;
}

.calendar-navigation {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding: 15px;
    background: #ecf0f1;
    border-radius: 8px;
}

.nav-button {
    background: #3498db;
    color: white;
    border: none;
    padding: 8px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s;
}

.nav-button:hover {
    background: #2980b9;
}

.today-button {
    background: #27ae60;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.2s;
}

.today-button:hover {
    background: #229954;
}

@media (max-width: 768px) {
    .calendar-day {
        min-height: 80px;
        padding: 4px;
    }
    
    .day-number {
        font-size: 14px;
    }
    
    .day-info {
        font-size: 9px;
    }
    
    .calendar-header-day {
        padding: 10px 5px;
        font-size: 12px;
    }
}
</style>
@endsection

@section('script')
<script>
let currentYear = {{ date('Y') }};
let currentMonth = {{ date('n') }};
const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
];
const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

$(document).ready(function() {
    loadMonthlyCalendar();
});

function loadMonthlyCalendar() {
    $('#loadingIndicator').show();
    $('#errorMessage').hide();
    $('#calendarGrid').hide();
    $('#monthlySummary').hide();
    
    // Update header
    $('#currentMonthYear').text(monthNames[currentMonth - 1] + ' ' + currentYear);
    
    $.ajax({
        url: '{{ route("calendar.monthly") }}',
        method: 'GET',
        data: { 
            year: currentYear,
            month: currentMonth
        },
        success: function(response) {
            $('#loadingIndicator').hide();
            
            if (response.success) {
                displayMonthlyCalendar(response.data);
                $('#calendarGrid').show();
                $('#monthlySummary').show();
            } else {
                showError(response.message || 'Failed to load calendar data');
            }
        },
        error: function(xhr) {
            $('#loadingIndicator').hide();
            const errorMsg = xhr.responseJSON?.message || 'Error loading calendar data';
            showError(errorMsg);
        }
    });
}

function displayMonthlyCalendar(data) {
    // Update monthly summary
    const summary = data.monthly_summary;
    $('#totalCampaigns').text(summary.total_campaigns);
    $('#approvedCampaigns').text(summary.approved_campaigns);
    $('#pendingCampaigns').text(summary.pending_campaigns);
    $('#sentCampaigns').text(summary.sent_campaigns);
    $('#totalEmailsSent').text(summary.total_emails_sent.toLocaleString());
    $('#quotaUtilization').text(summary.quota_utilization + '%');
    
    // Build calendar grid
    let calendarHtml = '';
    
    // Add day headers (Sun, Mon, Tue, etc.)
    dayNames.forEach(day => {
        calendarHtml += `<div class="calendar-header-day">${day}</div>`;
    });
    
    // Calculate calendar layout
    const firstDay = new Date(currentYear, currentMonth - 1, 1).getDay();
    const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();
    const daysInPrevMonth = new Date(currentYear, currentMonth - 1, 0).getDate();
    const today = new Date();
    const todayStr = today.getFullYear() + '-' + 
                    String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                    String(today.getDate()).padStart(2, '0');
    
    // Add empty cells for previous month days
    for (let i = firstDay - 1; i >= 0; i--) {
        const dayNum = daysInPrevMonth - i;
        calendarHtml += `
            <div class="calendar-day other-month">
                <div class="day-number">${dayNum}</div>
            </div>
        `;
    }
    
    // Add current month days
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = currentYear + '-' + 
                       String(currentMonth).padStart(2, '0') + '-' + 
                       String(day).padStart(2, '0');
        
        // Find data for this day
        const dayData = data.daily_breakdown.find(d => d.date === dateStr);
        
        const isToday = dateStr === todayStr;
        const hasCampaigns = dayData && dayData.campaigns.length > 0;
        const isHistorical = dayData && dayData.is_historical;
        const canBook = dayData && dayData.can_book;
        
        let dayClasses = 'calendar-day';
        if (isToday) dayClasses += ' today';
        if (hasCampaigns) dayClasses += ' has-campaigns';
        if (isHistorical) dayClasses += ' historical';
        
        let dayContent = `<div class="day-number">${day}</div>`;
        
        if (dayData) {
            // Quota info
            const quotaInfo = dayData.quota_info;
            let quotaClass = 'high';
            if (quotaInfo.utilization_rate > 80) quotaClass = 'low';
            else if (quotaInfo.utilization_rate > 50) quotaClass = 'medium';
            
            dayContent += `
                <div class="day-info">
                    <div class="quota-info ${quotaClass}">
                        ${quotaInfo.available_quota}/${quotaInfo.daily_quota}
                    </div>
            `;
            
            // Campaign indicators
            if (dayData.campaigns.length > 0) {
                dayData.campaigns.slice(0, 2).forEach(campaign => {
                    let campaignClass = 'campaign-indicator';
                    if (campaign.status === 'sent') campaignClass += ' sent';
                    else if (campaign.status === 'pending') campaignClass += ' pending';
                    else if (campaign.status === 'approved') campaignClass += ' approved';
                    
                    dayContent += `<span class="${campaignClass}">${campaign.id}</span>`;
                });
                
                if (dayData.campaigns.length > 2) {
                    dayContent += `<span class="campaign-indicator">+${dayData.campaigns.length - 2}</span>`;
                }
            }
            
            dayContent += '</div>';
            
            // Status indicator
            let statusClass = 'status-indicator';
            if (!canBook) statusClass += ' cannot-book';
            else if (isHistorical) statusClass += ' historical';
            
            dayContent += `<div class="${statusClass}"></div>`;
        }
        
        calendarHtml += `
            <div class="${dayClasses}" onclick="showDayDetail('${dateStr}')">
                ${dayContent}
            </div>
        `;
    }
    
    // Add next month days to complete the grid
    const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
    const remainingCells = totalCells - (firstDay + daysInMonth);
    
    for (let i = 1; i <= remainingCells; i++) {
        calendarHtml += `
            <div class="calendar-day other-month">
                <div class="day-number">${i}</div>
            </div>
        `;
    }
    
    $('#calendarGridContent').html(calendarHtml);
}

function previousMonth() {
    if (currentMonth === 1) {
        currentMonth = 12;
        currentYear--;
    } else {
        currentMonth--;
    }
    loadMonthlyCalendar();
}

function nextMonth() {
    if (currentMonth === 12) {
        currentMonth = 1;
        currentYear++;
    } else {
        currentMonth++;
    }
    loadMonthlyCalendar();
}

function goToToday() {
    const today = new Date();
    currentYear = today.getFullYear();
    currentMonth = today.getMonth() + 1;
    loadMonthlyCalendar();
}

function showDayDetail(date) {
    // Implement day detail modal here
    alert('Day detail for: ' + date);
}

function showError(message) {
    $('#errorMessage').text(message).show();
}
</script>
@endsection