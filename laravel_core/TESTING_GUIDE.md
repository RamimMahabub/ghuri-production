# Production Portal Testing Guide - Step by Step

## Prerequisites
- Open PowerShell in your project folder: `D:\projects\Travel agent`
- Make sure Laravel server is NOT running yet

---

## STEP 1: Prepare the Database

### 1.1 Fresh Database Setup
```bash
php artisan migrate:fresh
```
**What it does:** Deletes all old data and creates fresh tables  
**You should see:** "Migrated: 2026_04_27_120000_add_security_columns_to_users_table"

### 1.2 Seed Test Users
```bash
php artisan db:seed --class=PortalTestSeeder
```
**What it does:** Creates 6 test users (customer, admin, support, etc.)  
**You should see:** A table with test user email/password

---

## STEP 2: Verify Setup is Complete
```bash
php artisan portal:verify
```
**What it does:** Checks all routes, tables, models, views  
**You should see:** All ✓ checkmarks (some tables may say "not migrated yet" - that's OK)

---

## STEP 3: Run Tests (Optional but Recommended)
```bash
php artisan test
```
**What it does:** Runs all automated tests  
**Expected result:** "25 passed" ✓

---

## STEP 4: Start the Development Server
```bash
php artisan serve
```
**You should see:**
```
   INFO  Server running on [http://127.0.0.1:8000]
```
**Keep this terminal open!**

---

## STEP 5: Test Customer Portal

### 5.1 Go to Customer Registration
- Open browser: `http://localhost:8000/register`
- Fill in:
  - **Name:** Test Customer
  - **Email:** testcustomer@example.com
  - **Phone:** +1234567890
  - **Password:** password123
  - **Confirm Password:** password123
- Click **Register**

**Expected:** Redirects to "Verify Your Email" page

### 5.2 Verify Email (Skip Email Check)
- Since we're local, check the Laravel log for the verification link
- OR in terminal (new PowerShell tab), run:
  ```bash
  php artisan tinker
  ```
  Then type:
  ```php
  App\Models\User::where('email', 'testcustomer@example.com')->update(['email_verified_at' => now()])
  exit
  ```

### 5.3 Login as Customer
- Go to: `http://localhost:8000/login`
- Login with:
  - **Email or Phone:** testcustomer@example.com (or +1234567890)
  - **Password:** password123
- Click **Log in**

**Expected:** Goes to `/dashboard` showing "My Bookings"

### 5.4 Test Customer Restrictions
- Go to: `http://localhost:8000/admin`

**Expected:** Error 403 (Forbidden) - customers cannot access admin portal ✓

---

## STEP 6: Test Admin Portal

### 6.1 Logout from Customer (if logged in)
- Click logout link on dashboard

### 6.2 Go to Admin Login
- Go to: `http://localhost:8000/admin/login`
- Login with:
  - **Work Email:** admin@example.com
  - **Password:** password
- Click **Continue with OTP**

**Expected:** Redirects to OTP page with message "OTP sent to your email"

### 6.3 Get the OTP
In a new PowerShell tab:
```bash
php artisan tinker
```
Then type:
```php
// Check what OTP was sent (it's hashed, but we can bypass for testing)
// In production, check your email. For now, just verify it's cached:
Cache::get('admin_login_otp_2')
exit
```

**For testing locally:** The OTP sent to console/mail is random. To bypass:
```bash
php artisan tinker
App\Models\User::find(2)->update(['two_factor_enabled' => true])
exit
```

Then just enter any 6 digits like `123456` to test the flow.

### 6.4 Enter OTP
- On the OTP page, enter **123456** (any 6 digits for testing)
- Click **Verify OTP**

**Expected:** 
- If invalid: Error message "Invalid or expired OTP"
- If you want to succeed, in PowerShell:
  ```bash
  php artisan tinker
  Cache::put('admin_login_otp_2', Hash::make('123456'), now()->addMinutes(10))
  exit
  ```
  Then try again with `123456`

**After valid OTP:** Redirects to `/admin` dashboard showing stats ✓

### 6.5 Test Admin Access
- You should see the admin dashboard with:
  - Total Sales
  - Bookings
  - Profit
  - Operations/Finance/Security info

### 6.6 Test Admin Restrictions
- Go to: `http://localhost:8000/dashboard`

**Expected:** Error 403 - admins cannot access customer dashboard ✓

---

## STEP 7: Test All Portal Roles

Use these test credentials to login to `/admin/login`:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Support Agent | support@example.com | password |
| Manager | manager@example.com | password |
| Ticketing Officer | ticketing@example.com | password |
| Accounts Officer | accounts@example.com | password |

**All should:** 
- Login successfully ✓
- Be asked for OTP ✓
- Access `/admin` dashboard ✓
- Be blocked from `/dashboard` ✓

---

## STEP 8: Test Portal Separation

### Test 1: Customer Cannot Access Admin Portal
1. Login as customer at `/login`
2. Try to go to `/admin`
**Result:** 403 Forbidden ✓

### Test 2: Admin Cannot Access Customer Portal
1. Login as admin at `/admin/login`
2. Try to go to `/dashboard`
**Result:** 403 Forbidden ✓

### Test 3: Email Verification Required
1. Create new customer account
2. Try to access `/dashboard` without verifying email
**Result:** Redirects to email verification page ✓

### Test 4: Email/Phone Login Works
1. Login at `/login` with email: `customer@example.com`
**Result:** Works ✓

2. Logout, then login with phone: `+1234567890`
**Result:** Works ✓

---

## STEP 9: Database Inspection (Optional)

### View Created Users
```bash
php artisan tinker
```
```php
App\Models\User::all(['id', 'name', 'email', 'role', 'email_verified_at'])->toArray()
exit
```

### View Security Tables
```bash
php artisan tinker
```
```php
// Check if tables exist
Schema::getTables()
exit
```

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| "No routes found" | Run `php artisan route:list` |
| "Column not found" | Run `php artisan migrate` |
| "View not found" | Check files exist: `resources/views/auth/` and `resources/views/admin/` |
| "Tests failing" | Run `php artisan migrate:fresh` then `php artisan db:seed --class=PortalTestSeeder` |
| OTP not working | Manually hash it: `php artisan tinker` then `Cache::put('admin_login_otp_2', Hash::make('123456'), now()->addMinutes(10))` |

---

## Summary

✅ Customer Portal (`/login` → `/dashboard`)
- Sign up with email + phone
- Email verification required
- Login with email OR phone
- Cannot access admin routes

✅ Admin/Staff Portal (`/admin/login` → `/admin`)
- Email verification required
- Mandatory OTP challenge
- 6 different staff roles with same access
- Cannot access customer routes

✅ Role-Based Protection
- Middleware blocks unauthorized access
- 403 Forbidden errors when accessing wrong portal

---

**You're all set!** 🎉
