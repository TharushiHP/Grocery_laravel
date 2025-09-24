# 500 Server Error Troubleshooting Guide

## What a 500 Error Means
The Laravel application is starting but encountering a runtime error. This is progress - it's better than the service being unavailable!

## Debug Steps Added

### 1. Enhanced Health Check
- URL: `/health.php` - Shows environment variables and database connection status
- URL: `/debug` - Shows Laravel configuration and database status

### 2. Enhanced Startup Logging
The startup command now includes more diagnostic information.

## Common Causes of 500 Errors in Laravel

### 1. Missing APP_KEY
**Check:** Does your Railway `APP_KEY` variable exist and start with `base64:`?
**Solution:** Run locally: `php artisan key:generate --show` and copy to Railway

### 2. Database Connection Issues
**Check:** Visit `/health.php` to see database connection status
**Required Variables:**
- `DB_CONNECTION=mysql`
- `DB_HOST` (set)
- `DB_PORT` (usually 3306)
- `DB_DATABASE` (set)
- `DB_USERNAME` (set)
- `DB_PASSWORD` (set)

### 3. Missing or Failed Migrations
**Check:** Look at Railway deployment logs for migration errors
**Solution:** Ensure database is accessible and tables can be created

### 4. File Permissions
**Check:** Laravel needs write access to storage/ and bootstrap/cache/
**Solution:** This should be handled by deploy.sh

### 5. Composer Dependencies
**Check:** All required PHP extensions and packages installed
**Solution:** Verify composer.json requirements

## How to Debug

1. **Visit `/health.php`** - Shows basic environment info
2. **Visit `/debug`** - Shows Laravel-specific configuration
3. **Check Railway Logs** - Look for specific error messages
4. **Temporarily set `APP_DEBUG=true`** in Railway variables to see detailed errors

## Next Steps

After deployment, visit:
- `https://your-app.railway.app/health.php`
- `https://your-app.railway.app/debug`

These will show exactly what's wrong!