# EMS Email System Documentation

# 1. Current System Operational Flow Documentation

## Overview

EMS (Exhibition Management System) mein email sending ka process asynchronous (Queue-Based) architecture par kaam karta hai.

Iska matlab hai ke jab koi user ya admin koi action perform karta hai to email foran send nahi hoti. Pehle email database queue mein save hoti hai aur phir background cron job usay process karta hai.

Is approach se system fast rehta hai aur users ko email send hone ka wait nahi karna parta.

---

# Current Email Flow

## Step 1 – User Action

System mein koi event hota hai.

Examples:

* Order Approval
* Event Invitation
* MoU Scheduling
* MoU Rescheduling
* MoU Acceptance
* MoU Rejection
* Bulk Email Campaign

User action perform karta hai aur controller execute hota hai.

---

## Step 2 – Email Template Load Hota Hai

Controller database se relevant email template load karta hai.

Example:

```text
EVENT_INVITATION
MOU_SIGNING_ACCEPTED
CUSTOM_BULK_EMAILS
RESET_PASSWORD_LINK
```

Template mein placeholders mojood hote hain.

Example:

```text
Dear {NAME}

Your event invitation has been approved.

Login URL:
{LOGIN_URL}
```

---

## Step 3 – Dynamic Data Replace Hoti Hai

Controller placeholders ko actual values se replace karta hai.

Example:

```text
{NAME}
```

Replace ho jata hai:

```text
Ali Khan
```

Result:

```text
Dear Ali Khan

Your event invitation has been approved.
```

---

## Step 4 – Email Queue Mein Save Hoti Hai

Email direct send nahi hoti.

System email ko table:

```text
es_emails_cron
```

mein save kar deta hai.

Stored Information:

| Field   | Purpose             |
| ------- | ------------------- |
| type    | Email category      |
| email   | Recipient email     |
| subject | Email subject       |
| message | Final email content |
| is_sent | Sent status         |
| sent_on | Sent timestamp      |

Initial Status:

```text
is_sent = 0
```

Meaning:

```text
Email abhi send nahi hui.
```

---

## Step 5 – Cron Job Execute Hota Hai

Server par cron job har minute run hoti hai.

Cron URL:

```text
Cron_email/send_messages
```

Cron automatically database check karta hai.

---

## Step 6 – Pending Emails Fetch Ki Jati Hain

Cron table:

```text
es_emails_cron
```

se pending emails uthata hai.

Condition:

```text
is_sent = 0
```

Example:

```text
10 pending emails
```

Cron unko processing queue mein le leta hai.

---

## Step 7 – Email Layout Apply Hota Hai

Raw email content ko system HTML email template ke andar wrap karta hai.

Purpose:

* Better design
* Mobile friendly email
* Consistent branding

---

## Step 8 – PHPMailer Send Karta Hai

System PHPMailer library use karta hai.

Current SMTP Configuration:

```text
SMTP Host
SMTP Port
SMTP Username
SMTP Password
```

PHPMailer SMTP server se connect karta hai aur email recipient ko send karta hai.

---

## Step 9 – Status Update Hota Hai

Agar email successfully send ho jaye:

```text
is_sent = 1
```

Aur:

```text
sent_on = Current Timestamp
```

Database update ho jata hai.

---

## Step 10 – Business Logic Update

Kuch email types additional updates bhi karti hain.

Example:

EVENT_INVITATION

Email successful hone ke baad:

```text
invitation_sent = 1
```

update ho jata hai.

---

# Current System Components

## Email Queue Table

```text
es_emails_cron
```

Purpose:

Pending aur sent emails ko manage karna.

---

## Email Templates

```text
email_template
```

Purpose:

Dynamic subjects aur message bodies maintain karna.

---

## Cron Controller

```text
Cron_email.php
```

Purpose:

Background email processing.

---

## PHPMailer Helper

```text
phpmailer_helper.php
```

Purpose:

SMTP connection aur actual email delivery.

---

## Email Layout

```text
email.php
```

Purpose:

Email design aur branding.

---

# Current System Limitations

Current implementation mein:

* SMTP settings code mein hardcoded hain
* Sender email dynamically change nahi ki ja sakti
* SMTP host dynamically change nahi kiya ja sakta
* Admin panel se email settings manage nahi ki ja sakti

Isi wajah se Email Settings Module introduce karna recommended hai.

---

# 2. Enhanced Form Field Configuration Guide

## Purpose

Is module ka objective hai ke administrator bina code change kiye:

* Sender email change kar sake
* SMTP provider change kar sake
* Reply email change kar sake
* Email sending behavior control kar sake

---

# SMTP Configuration Section

## SMTP Provider

### Field Type

Dropdown

### Example Values

```text
Gmail
Outlook
Office365
Custom SMTP
```

### System Behavior

SMTP provider select karne par system us provider ke SMTP server se emails send karega.

---

## SMTP Host

### Field Type

Text

### Example

```text
smtp.gmail.com
```

### System Behavior

Ye server address define karta hai jahan PHPMailer connect karega.

---

## SMTP Port

### Field Type

Number

### Example

```text
465
587
```

### System Behavior

Connection kis port par establish hogi.

---

## Encryption Type

### Field Type

Dropdown

### Values

```text
SSL
TLS
STARTTLS
```

### System Behavior

Email connection secure banata hai.

---

## SMTP Username

### Field Type

Text

### Example

```text
noreply@company.com
```

### System Behavior

SMTP authentication ke liye use hota hai.

---

## SMTP Password

### Field Type

Password

### Example

```text
********
```

### System Behavior

SMTP authentication credential.

### Security Note

Password encrypted format mein store ki jaye.

---

# Sender Information Section

## From Name

### Example

```text
EMS Support Team
```

### Effect

Recipient ko sender naam isi format mein nazar aayega.

---

## From Email

### Example

```text
noreply@ems.com
```

### Effect

System ki tamam outgoing emails isi address se send hongi.

---

## Reply-To Email

### Example

```text
support@ems.com
```

### Effect

User jab reply karega to email yahan receive hogi.

---

# Queue Settings Section

## Email Queue Enabled

### Values

```text
Yes
No
```

### Effect

Yes:

```text
Emails queue ke through jayengi.
```

No:

```text
Emails direct send hongi.
```

---

## Emails Per Cron Run

### Example

```text
10
```

### Effect

Cron ek execution mein kitni emails process karega.

---

## Retry Attempts

### Example

```text
3
```

### Effect

Failed email kitni dafa dobara send karne ki koshish ki jayegi.

---

# Monitoring Section

## Enable Email Logging

### Values

```text
Yes
No
```

### Effect

Har email ka log database mein save hoga.

---

## SMTP Debug Mode

### Values

```text
Yes
No
```

### Effect

SMTP connection issues troubleshoot karne mein help karega.

---

# Testing Section

## Test Email Address

### Example

```text
admin@company.com
```

### Effect

Test email isi address par send hogi.

---

## Send Test Email Button

### Purpose

Current SMTP configuration verify karna.

### Result

Success:

```text
SMTP working correctly.
```

Failure:

```text
SMTP configuration issue detected.
```

---

# Expected Result After Implementation

Administrator future mein bina developer ki help ke:

* Sender email change kar sakega
* SMTP server change kar sakega
* SMTP credentials update kar sakega
* Email queue behavior control kar sakega
* Email delivery monitor kar sakega
* System ko production level par maintain kar sakega

Aur PHPMailer hardcoded values ki jagah database settings use karega.
