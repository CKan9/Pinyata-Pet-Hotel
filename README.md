# Pinyata-Pet-Hotel
A Web-Based Booking Platform for Pet Owners and Hotel Administrators > Developed by: 79027 CHAI CHENG KANG (Universiti Malaysia Sarawak)

## 🧾 Project Overview
This system allows pet owners to book pet hotel rooms, manage bookings, make payments via PayPal, and interact with AI-powered customer support. Hotel staff can manage users, pets, rooms, and view reservation histories via the admin dashboard.

This project is for educational purposes only. No real transactions will occur and all data is for testing or demonstration.

## 🚦 Getting Started
### Deploy Website To Local Machine

<details>
  Run Locally with Wampserver64/XAMPP
  -Wampserver64
  1. Copy project files into wamp64/htdocs/
  2. Launch Wampserver64
  3. Import the .sql file into phpMyAdmin
  4. Access via http://localhost/index.html

  -XAMPP
  1. Copy project files into xampp/htdocs/
  2. Launch XAMPP → Start Apache and MySQL
  3. Import the .sql file into phpMyAdmin
  4. Access via http://localhost/index.html
</details>

### User Role
Pet Owners
| Features     | How To Access                       |          |
| -------------| ----------------------------------- | -------- |
| Registration | Click "Register here" on Log in page|          |
| Booking      | User Dashboard → "Book Now"         |          |
| Manage Pets  | User Dashboard → "My Pets"          |          |
| Chat Support | Click 🪶 icon on any page          |          |

Administrators
| Features         | How To Access                       |          |
| -----------------| ----------------------------------- | -------- |
| User Management  | Admin Dashboard → "Users"           |          |
| Booking Oversight| Admin Dashboard → "Bookings"        |          |
| Room Availability| Admin Dashboard → "Rooms"           |          |

## Key Feature Guides
### Chat Support (AI Bot)
<details>
Click the chatbot icon on the bottom corner

Ask queries relate to pet field like:

"Is Deluxe room available?"

"What pet this hotel was support?"

The bot provides automated responses 24/7
</details>

### Payment via PayPal
<details>
PayPal Sandbox credentials are required

Transaction processed securely using cURL API integration

Live payments are not activated — simulation only
1. Complete booking 

2. Use sandbox credentials:

Buyer Email: sb-abcdef@personal.example.com

Password: test_password

3. Confirm payment → Redirected to booking confirmation

Paypal Credentials
Ask from developer
</details>

### Managing Pets
<details>
To add a pet:

1. After login Go to "My Pets" → "Add New"

2. Upload photo (JPG/PNG under 2MB)

3. Enter details:

    - Name: Max
    - Type: Dog
    - Breed: Golden Retriever
    - Age: 3
    - Special Notes: Allergic to chicken
</details>

## Dummy Data

| Role  | Email                  | PWD         |
| ----- | ---------------------- | ----------- |
| ADMIN | admin@example.com      | admin123    |
| ----- | ---------------------- | ----------- |
| USER  | james@gmail.com        | james666    |
| USER  | Harry123@gmail.com     | 4r3e2w1q    |
| USER  | user1@gmail.com        | 0987654321  |

## Sample and demo

Video Link: https://youtu.be/3Z3DnVWsIMo


