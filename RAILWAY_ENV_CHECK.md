# Railway Environment Variables Checklist

## Current Variables (✓ = Set, ? = Needs Verification)

✓ APP_DEBUG (should be false for production)
✓ APP_ENV (should be production)  
✓ APP_KEY (Laravel application key)
✓ APP_NAME (Grocery Store Laravel)
✓ APP_URL (your Railway app URL)
✓ CACHE_DRIVER
✓ DB_DATABASE (MySQL database name)
✓ DB_HOST (MySQL host)
✓ DB_PASSWORD (MySQL password)
✓ DB_PORT (MySQL port, usually 3306)
✓ DB_USERNAME (MySQL username)
✓ DOCUMENT_STORE_ENABLED
✓ DOCUMENT_STORE_PATH
✓ LOG_CHANNEL
✓ LOG_LEVEL
✓ QUEUE_CONNECTION
✓ SESSION_DRIVER
✓ SESSION_LIFETIME

## MISSING - Please Add This Variable:

**DB_CONNECTION=mysql**

This is critical! Without this, Laravel doesn't know to use MySQL.

## How to Add:

1. Go to your Railway project dashboard
2. Go to your web service settings
3. Click on "Variables" tab
4. Click "New Variable"
5. Name: `DB_CONNECTION`
6. Value: `mysql`
7. Save

After adding this variable, Railway will automatically redeploy and your MySQL database should work properly.

## Environment Values Should Be:

-   APP_ENV=production
-   APP_DEBUG=false
-   DB_CONNECTION=mysql
-   APP_URL=https://your-railway-app-url.railway.app
