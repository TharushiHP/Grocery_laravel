# Fresh Railway Environment Setup Guide

## Required Environment Variables for Railway

Add these variables in your Railway project dashboard:

### Application Settings
APP_NAME="Grocery Store Laravel"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-railway-app.railway.app

### Database Settings (CRITICAL - Add These!)
DB_CONNECTION=mysql
DB_HOST=[Copy from Railway MySQL service]
DB_PORT=[Copy from Railway MySQL service]  
DB_DATABASE=[Copy from Railway MySQL service]
DB_USERNAME=[Copy from Railway MySQL service]
DB_PASSWORD=[Copy from Railway MySQL service]

### Application Key (CRITICAL!)
APP_KEY=[Generate with: php artisan key:generate --show]

### Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database

### Logging
LOG_CHANNEL=stack
LOG_LEVEL=error

## Step-by-Step Setup:

1. **Create New Railway Project**
2. **Add MySQL Database Service** (New Service → Database → MySQL)
3. **Copy MySQL connection details** to the DB_ variables above
4. **Generate APP_KEY locally**: Run `php artisan key:generate --show` and copy the result
5. **Add all environment variables** in Railway Variables section
6. **Connect GitHub repository** to Railway
7. **Deploy!**

## Common Issues:
- Missing DB_CONNECTION=mysql (app won't connect to database)
- Missing APP_KEY (app won't start)  
- Wrong DATABASE variables (connection failures)