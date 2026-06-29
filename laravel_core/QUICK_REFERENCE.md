# QUICK REFERENCE - Portal Testing Cheat Sheet

## 🚀 One Command Setup
```bash
php artisan quickstart
```
This does everything automatically:
1. ✓ Fresh database
2. ✓ Creates test users
3. ✓ Verifies setup

---

## 🌐 Portal URLs

| Portal | URL | Login With |
|--------|-----|-----------|
| **Customer** | http://localhost:8000/login | Email OR Phone |
| **Admin** | http://localhost:8000/admin/login | Email + OTP |
| **Customer Dashboard** | http://localhost:8000/dashboard | (after login) |
| **Admin Dashboard** | http://localhost:8000/admin | (after OTP) |

---

## 👥 Test Accounts (All password: `password`)

### Customer
```
Email: customer@example.com
Phone: +1234567890
```

### Admin & Staff
```
admin@example.com         (admin)
support@example.com       (support agent)
manager@example.com       (manager)
ticketing@example.com     (ticketing officer)
accounts@example.com      (accounts officer)
```

---

## ✅ What to Test

### 1️⃣ Customer Signup
- [ ] Go to `/register`
- [ ] Fill form with name, email, phone, password
- [ ] See "Verify your email" page
- [ ] Click `/login` and login

### 2️⃣ Customer Login (Email)
- [ ] Go to `/login`
- [ ] Enter `customer@example.com`
- [ ] See dashboard

### 3️⃣ Customer Login (Phone)
- [ ] Go to `/login`
- [ ] Enter `+1234567890`
- [ ] See dashboard

### 4️⃣ Admin Login with OTP
- [ ] Go to `/admin/login`
- [ ] Enter `admin@example.com` + password
- [ ] See OTP page
- [ ] Enter any 6 digits (e.g., `123456`)
- [ ] See admin dashboard

### 5️⃣ Portal Separation
- [ ] Login as customer, try `/admin` → 403 ✓
- [ ] Login as admin, try `/dashboard` → 403 ✓

---

## 🔧 Useful Commands

```bash
# View all routes
php artisan route:list

# Run tests
php artisan test

# Access database
php artisan tinker

# Verify setup
php artisan portal:verify

# Reset everything
php artisan migrate:fresh
php artisan db:seed --class=PortalTestSeeder
```

---

## 🆘 Troubleshooting

**"Database locked"**
- Kill any running servers: `Ctrl+C`
- Delete `database.sqlite` (if using SQLite)
- Run `php artisan migrate:fresh`

**"OTP not working"**
- In production, check email for OTP code
- For testing locally, any 6 digits work
- Or manually hash it:
  ```bash
  php artisan tinker
  Cache::put('admin_login_otp_2', Hash::make('123456'), now()->addMinutes(10))
  exit
  ```

**"Tables not found"**
- Run: `php artisan migrate`

**"Tests failing"**
- Run: `php artisan quickstart`

---

## 📖 Full Documentation
See `TESTING_GUIDE.md` for detailed step-by-step instructions
