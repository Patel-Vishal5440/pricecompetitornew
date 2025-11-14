<?php

namespace App\Utility;

enum PermissionEnum: string {
    case SYSTEM_DEVELOPER_CRON = 'cron';
    case SYSTEM_DEVELOPER_PRODUCT_MANAGEMENT = 'product_management';
    case SYSTEM_DEVELOPER_COMPETITOR_MANAGEMENT = 'competitor_management';
    
    case PRICE_MANAGER_FIND_COMPETITOR_URLS = 'find_competitor_urls';
    case PRICE_MANAGER_MONITOR_PRICE = 'monitor_price';
    
    // Add more permissions as needed
}