# MCYF — Data Backup Guide

## کیوں ضروری ہے
اگر سرور crash ہو جائے، hard disk خراب ہو جائے، یا غلطی سے کچھ delete ہو جائے —
اس backup system کے بغیر آپ کا سارا data (members، تصاویر، سب کچھ) ہمیشہ کے لیے ضائع ہو سکتا ہے۔

یہاں **دو طریقے** دیے گئے ہیں — دونوں ایک ساتھ استعمال کرنا بہترین ہے۔

---

## ✅ طریقہ 1: روزانہ خودکار Backup (Google Drive تک)

یہ سب سے اہم ہے — **بغیر کسی کے یاد رکھے، روزانہ خودکار** چلتا ہے۔

### Setup کے مراحل (صرف ایک بار):

**قدم 1 — Google Drive Desktop App انسٹال کریں**
اگر پہلے سے نہیں ہے تو یہاں سے download کریں: https://www.google.com/drive/download/
انسٹال کرنے کے بعد یہ آپ کے کمپیوٹر پر ایک folder بنا دے گا (عام طور پر `G:\My Drive` یا
`C:\Users\آپ کا نام\Google Drive`) — اس folder میں جو کچھ بھی رکھیں گے، خودکار cloud پر چلا جائے گا۔

**قدم 2 — `scripts/backup-daily.bat` کھولیں (Notepad سے edit کریں)**
اس میں 5 چیزیں اپنے حساب سے تبدیل کریں:

```bat
SET MYSQL_BIN=B:\xampp-8.2\mysql\bin
SET DB_NAME=mcyf_db
SET DB_USER=root
SET DB_PASS=
SET BACKUP_DIR=G:\My Drive\MCYF-Backups
SET UPLOADS_DIR=B:\xampp-8.2\htdocs\...\public\assets\uploads
SET KEEP_DAYS=30
```

- `BACKUP_DIR` وہ folder ہونا چاہیے جو Google Drive کے ساتھ sync ہو رہا ہو
- `DB_PASS` اگر خالی ہے تو ایسے ہی رہنے دیں (`SET DB_PASS=`)، اگر پاس ورڈ ہے تو `SET DB_PASS=-pYourPassword` لکھیں (بغیر space کے `-p` کے بعد)

**قدم 3 — ایک بار manually چلا کر test کریں**
`scripts/backup-daily.bat` پر double-click کریں۔ Black window کھلے گی، چند سیکنڈ میں بند ہو جائے گی۔
اپنے `BACKUP_DIR` والے folder میں جا کر دیکھیں — دو files نظر آنی چاہئیں:
- `mcyf_db_2026-08-09_1430.sql` (database)
- `uploads_2026-08-09_1430.zip` (تمام تصاویر)

**قدم 4 — Windows Task Scheduler میں روزانہ چلانے کے لیے سیٹ کریں**

1. Windows search میں "Task Scheduler" لکھ کر کھولیں
2. دائیں طرف **"Create Basic Task"** پر کلک کریں
3. Name: `MCYF Daily Backup` → Next
4. Trigger: **Daily** → Next
5. وقت منتخب کریں (مثلاً رات 2 بجے، جب کوئی app استعمال نہ کر رہا ہو) → Next
6. Action: **Start a program** → Next
7. Program/script میں `scripts\backup-daily.bat` کا **پورا path** ڈالیں
   (مثلاً: `B:\xampp-8.2\htdocs\Projects\Masood Youth Forum Bolt\MCYF-PHP-Application\scripts\backup-daily.bat`)
8. Finish پر کلک کریں

بس! اب ہر رات خودکار database اور تصاویر کا backup بن کر Google Drive پر چلا جائے گا،
اور 30 دن سے پرانی backups خود delete ہوتی رہیں گی (جگہ بچانے کے لیے)۔

### اگر کبھی data واپس چاہیے ہو (Restore)
1. Google Drive سے مطلوبہ `mcyf_db_....sql` file download کریں
2. phpMyAdmin کھولیں → اپنا database منتخب کریں → **Import** ٹیب → وہ file منتخب کریں → Go

---

## ✅ طریقہ 2: One-Click Manual Backup (Admin Panel سے)

کسی بڑی تبدیلی سے پہلے (مثلاً bulk data delete کرنے سے پہلے) فوری طور پر backup لینے کے لیے،
Admin Panel میں ایک بٹن شامل کیا گیا ہے — دیکھیں Admin → Backup۔
یہ آپ کے browser میں فوراً `.sql` file download کر دیتا ہے، کوئی server access نہیں چاہیے۔

---

## 💡 اضافی مشورہ (جب یہ app اصل سرور/hosting پر لائیو ہو)

- زیادہ تر ہوسٹنگ (cPanel) میں built-in "Backup Wizard" ہوتا ہے — وہ بھی چالو کر دیں
- اگر ممکن ہو تو ایک سے زیادہ جگہ backup رکھیں (صرف Google Drive نہ رکھیں) — یہ "3-2-1 rule"
  کہلاتا ہے: 3 کاپیاں، 2 مختلف جگہ، 1 گھر سے باہر (جیسے cloud)
