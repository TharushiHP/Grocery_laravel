# Railway MySQL Database Setup Instructions

## Step 1: Add MySQL Database Service

1. In your Railway project dashboard, click "New Service"
2. Select "Database" → "MySQL"
3. Railway will automatically create a MySQL instance

## Step 2: Update Environment Variables

Replace your current database variables in Railway with these:

DB_CONNECTION=mysql
DB_HOST=${{MYSQLHOST}}
DB_PORT=${{MYSQLPORT}}
DB_DATABASE=${{MYSQLDATABASE}}
DB_USERNAME=${{MYSQLUSER}}
DB_PASSWORD=${{MYSQLPASSWORD}}

## Step 3: Remove Old Variables

Delete these PostgreSQL variables if they exist:

-   DB_CONNECTION (if set to pgsql or sqlite)
-   PGHOST, PGPORT, PGDATABASE, PGUSER, PGPASSWORD

## Step 4: Keep Other Variables

Keep all other variables as they are:

-   APP_NAME=Grocery Store
-   APP_ENV=production
-   APP_KEY=base64:GyE7qfdBepGWluD8mpQS4tjn+9iH35GIPoaIMhP1oVk=
-   APP_DEBUG=false
-   APP_URL=https://web-production-149e2.up.railway.app

## What This Will Fix:

✅ Database connection errors
✅ Migration issues  
✅ Seeding problems
✅ Products will load properly
✅ All database operations will work

After making these changes, Railway will automatically redeploy with MySQL support.
