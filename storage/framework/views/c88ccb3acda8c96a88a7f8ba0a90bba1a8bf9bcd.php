<div id="help-guidelines" class="help_guidelines_container">
    <ul class="nav help-menu">
        <li><a href="https://crm.ramayanasuiteskuta.com/Flip%20Book/JALAK_CRM_GUIDELINES.html" target="_blank">
            <i class="fa fa-question-circle" title="Click to view Help & Guidelines"></i> <span class="help-text">Help & Guidelines</span>
        </a></li>
    </ul>
</div>

<style>
.help_guidelines_container {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 230px; /* Sesuaikan dengan lebar sidebar */
    background-color: #2A3F54; 
    padding: 15px 0;
    border-top: 1px solid rgba(255,255,255,0.05);
    z-index: 9999;
    display: block !important; /* Pastikan selalu terlihat */
    visibility: visible !important; /* Pastikan selalu terlihat */
}

.help_guidelines_container a {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    width: 100%;
}

.help-text {
    display: inline-block;
    max-width: 160px; 
    overflow: hidden;
    text-overflow: ellipsis;
    vertical-align: bottom;
}

.nav-sm .help_guidelines_container {
    width: 70px;
}
.help-menu {
    margin-bottom: 0;
    padding-left: 0;
    list-style: none;
}

.help-menu li {
    position: relative;
    display: block;
}

.help-menu li a {
    padding: 13px 15px 12px;
    color: #E7E7E7;
    display: block;
    font-weight: 500;
}

.help-menu li a:hover {
    background-color: rgba(255, 255, 255, 0.05);
    color: #F2F5F7;
}

.help-menu li a i {
    font-size: 17px;
    width: 20px;
    margin-right: 10px;
}

.nav-sm .help-menu li a span {
    display: none;
}

.nav-sm .help-menu li a {
    padding: 13px 15px;
    text-align: center;
}

.nav-sm .help-menu li a i {
    font-size: 18px;
}
</style><?php /**PATH /home/jalakdev/www/crm/resources/views/layouts/_help_guidelines.blade.php ENDPATH**/ ?>