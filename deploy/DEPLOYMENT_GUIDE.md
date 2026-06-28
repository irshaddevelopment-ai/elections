# دليل النشر (Deployment) الكامل — elections-vote

دليل عملي مكتوب بناءً على الأعطال الحقيقية التي حصلت، ويشرح كيف تنشر بأمان وتتجنّب الأخطاء.

> **القاعدة الذهبية:** الكود يتدفّق في اتجاه واحد فقط:
> **محليًا (local) ← `git push` ← السيرفر `git pull` ← `systemctl restart elections-octane`**
> لا تعدّل الكود على السيرفر مباشرة، ولا تعمل `commit`/`push` من السيرفر أبدًا.

---

## 0. نظرة سريعة على البنية (architecture)

| المكوّن | القيمة |
|---|---|
| الدومين | `https://elections-vote.duckdns.org` (DuckDNS) |
| مزوّد الخدمة | DigitalOcean — droplet في FRA1 |
| مسار المشروع | `/var/www/elections` |
| مستخدم التشغيل | `www-data` |
| تطبيق الخادم | Laravel **Octane + RoadRunner** على المنفذ `:8000` |
| الـ systemd service | `elections-octane` |
| الـ reverse proxy | **nginx** (يستمع 80/443، يمرّر إلى 127.0.0.1:8000) |
| TLS | Let's Encrypt (certbot) |
| قاعدة البيانات | MySQL 8 (`election`) |
| الجلسات (sessions) | Redis |
| ملف البيئة الفعّال | `.env.production` (هو **symlink** إلى `.env`) |

السبب أن Laravel يقرأ `.env.production` وليس `.env`: لأن `APP_ENV=production` يُمرَّر إلى الـ workers، فيبحث Laravel عن `.env.{APP_ENV}`. لذلك جعلناه symlink لـ `.env` كي لا يختلفا أبدًا.

---

## 1. النشر الروتيني (الأكثر استخدامًا) — نشر تعديل على الكود

### الخطوة 1 — محليًا (على جهازك)
```bash
git add -A
git commit -m "وصف التعديل"
git push origin main
```

### الخطوة 2 — على السيرفر (عبر SSH)
```bash
cd /var/www/elections
git pull origin main
```

### الخطوة 3 — إن تغيّرت مكتبات composer فقط (وإلا تجاوزها)
```bash
sudo -u www-data composer install --no-dev --optimize-autoloader
```

### الخطوة 4 — مسح الكاش (نفّذها كـ www-data وليس root)
```bash
sudo -u www-data php artisan view:clear
sudo -u www-data php artisan config:clear
```

### الخطوة 5 — إعادة تشغيل الـ workers (إلزامي — وإلا لن يظهر التعديل)
```bash
sudo systemctl restart elections-octane
```
> ⚠️ Octane يحتفظ بالكود في الذاكرة (warm). تعديل الملفات على القرص لا يظهر حتى تعيد التشغيل.
> استعمل `restart` وليس `octane:reload` (الأخير يعطي خطأ بسبب ملف `.rr.yaml` الفارغ — وهذا طبيعي).

### الخطوة 6 — التحقّق
```bash
curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8000/        # متوقّع 200
curl -s -o /dev/null -w "%{http_code}\n" https://elections-vote.duckdns.org/   # متوقّع 200
```
ثم في المتصفّح اعمل **Ctrl+Shift+R** لتجاوز كاش الصفحة.

### الخطوة 7 — إن ظهرت أخطاء صلاحيات بعد التشغيل
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
```

---

## 2. قائمة الأخطاء الشائعة وكيف تتجنّبها (مهم جدًا)

هذه هي الأعطال التي أوقعتنا فعليًا — احفظها:

1. **التعديل لا يظهر بعد `git pull`** → نسيت إعادة تشغيل Octane. الحل: `sudo systemctl restart elections-octane`.

2. **الموقع يعطي 500 و `MissingAppKeyException`** → السبب أن `.env.production` غير مقروء من `www-data`. اليوم هو symlink لـ `.env`، لكن إن أُعيد إنشاؤه كملف مملوك لـ root:
   ```bash
   sudo chown -h www-data:www-data /var/www/elections/.env.production
   sudo -u www-data head -c1 /var/www/elections/.env.production && echo " READABLE"
   ```

3. **تشغيل artisan كـ root** يجعل ملفات `storage/` و `bootstrap/cache/` مملوكة لـ root فيعجز الـ worker عن الكتابة → 500 بلا أثر في اللوج. **دائمًا** نفّذ artisan كـ `www-data`. الإصلاح:
   ```bash
   sudo chown -R www-data:www-data storage bootstrap/cache
   ```

4. **`502 Bad Gateway`** = nginx لا يصل إلى Octane (الـ service متوقّف أو الـ workers ماتت). الحل عادةً:
   ```bash
   sudo systemctl restart elections-octane
   ```

5. **`RoadRunner can't communicate with the worker` / `Network: EOF`** = الـ worker يموت أثناء الإقلاع. ليست مشكلة شبكة — ابحث عن سبب انهيار الـ worker (مفتاح ناقص، صلاحيات `.env.production`، class ناقص). راجع لوج التشخيص في `/tmp/rr.log` بتشغيل rr يدويًا مع `--log-level=debug`.

6. **بعد `git reset --hard` أو نسخة جديدة** ملف `rr` يفقد صلاحية التنفيذ:
   ```bash
   sudo chmod +x /var/www/elections/rr && sudo systemctl restart elections-octane
   ```

7. **لا تعمل `commit`/`push` من السيرفر أبدًا** — `git status` هناك فوضوي عمدًا (`.env.production` أسرار، `vendor/` محذوف جزئيًا). أي commit يسرّب الأسرار أو يحذف أدوات.

8. **لا تشغّل `apt upgrade` ولا تعمل reboot أثناء استخدام فعلي** — تحديثات MySQL/nginx التلقائية هي ما أسقطت الموقع مرة. التحديثات التلقائية **معطّلة** الآن؛ حدّث يدويًا في نافذة صيانة (انظر القسم 6).

9. **لا تضغط `/resetdata` في الإنتاج أبدًا** — يمسح كل قاعدة البيانات.

---

## 3. استكشاف الأعطال السريع (Troubleshooting)

ابدأ دائمًا بهذا الفحص الشامل:
```bash
echo "Octane: $(systemctl is-active elections-octane) | MySQL: $(systemctl is-active mysql) | Redis: $(systemctl is-active redis-server)"
echo "Workers: $(pgrep -fc roadrunner-worker)"
curl -s -o /dev/null -w "LOCAL %{http_code}\n" http://127.0.0.1:8000/
free -h | head -2
tail -n 30 /var/www/elections/storage/logs/laravel.log
```

| العرَض | الفحص | الحل |
|---|---|---|
| 502 | `systemctl status elections-octane` | `sudo systemctl restart elections-octane` |
| 500 + MissingAppKey | صلاحيات `.env.production` | `chown -h www-data:www-data .env.production` |
| 500 عام | `tail laravel.log` | حسب الاستثناء الظاهر |
| الـ service متوقّف بعد تحديث | journal فيه "apt upgrade" | راجع أن `Wants=` و `Restart=always` موجودان في الـ unit |
| 000 / لا يردّ | `chmod +x rr` ثم restart | |

لرؤية الخطأ الحقيقي من داخل الـ worker (عندما يكون "can't communicate"):
```bash
sudo systemctl stop elections-octane
cd /var/www/elections
sudo -u www-data env APP_BASE_PATH=/var/www/elections APP_ENV=production LARAVEL_OCTANE=1 \
  /var/www/elections/rr serve -c /var/www/elections/.rr.yaml \
  -o version=3 -o http.address=127.0.0.1:8000 \
  -o server.command=/usr/bin/php8.2,/var/www/elections/vendor/bin/roadrunner-worker \
  -o http.pool.num_workers=1 -o rpc.listen=tcp://127.0.0.1:6001 \
  -o logs.level=debug -o logs.mode=development -o logs.output=stdout > /tmp/rr.log 2>&1 &
sleep 5; curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8000/; sleep 1; cat /tmp/rr.log; kill %1
# بعد التشخيص:
sudo systemctl start elections-octane
```

---

## 4. إعداد سيرفر جديد من snapshot (الاستعادة / تغيير الريجن)

عند إنشاء droplet جديد من snapshot يتغيّر الـ **IP**، فتحتاج لتحديث DNS و TLS.

1. **أنشئ droplet من الـ snapshot** (DigitalOcean → Snapshots → Create Droplet) واختر الريجن والحجم.
2. **حدّث DuckDNS**: ادخل duckdns.org وغيّر IP الدومين `elections-vote` إلى IP السيرفر الجديد.
3. **ادخل SSH** وتأكّد من الخدمات وأصلح ما يلزم:
   ```bash
   cd /var/www/elections
   sudo chmod +x rr
   sudo chown -R www-data:www-data storage bootstrap/cache
   sudo chown -h www-data:www-data .env.production
   sudo systemctl restart elections-octane mysql redis-server nginx
   ```
4. **تحقّق من HTTPS**: شهادة TLS موجودة داخل النسخة وصالحة للدومين، فتعمل فور توجيه DNS. للتجديد:
   ```bash
   sudo certbot renew --dry-run
   ```
5. **عطّل التحديثات التلقائية** (مهم على كل سيرفر جديد):
   ```bash
   sudo systemctl disable --now apt-daily-upgrade.timer apt-daily.timer
   ```
6. **تحقّق نهائي**:
   ```bash
   curl -s -o /dev/null -w "%{http_code}\n" https://elections-vote.duckdns.org/
   ```

---

## 5. التحجيم (Scale up / down) — قبل/بعد الحدث

> **مهم:** عند الـ resize اختر دائمًا **"CPU and RAM only"** (قابل للعكس). خيار "Disk, CPU and RAM" يكبّر القرص بشكل دائم ولا يمكن تصغيره.

### رفع الموارد قبل الحدث
1. خذ snapshot احتياطي.
2. أطفئ الـ droplet → Resize (CPU & RAM only) → الخطة الأكبر المتاحة في الريجن.
3. ارفع عدد الـ workers في الـ unit:
   ```bash
   sudo sed -i 's/--workers=12/--workers=16/' /etc/systemd/system/elections-octane.service
   sudo systemctl daemon-reload && sudo systemctl restart elections-octane
   ```
4. ارفع MySQL buffer (إن كان RAM ≥ 16GB) في `/etc/mysql/mysql.conf.d/zz-elections-tuning.cnf`:
   ```
   [mysqld]
   innodb_buffer_pool_size = 4G
   max_connections = 200
   ```
   ثم `sudo systemctl restart mysql`.

القيم الحالية (4 vCPU / 8 GB): `--workers=12`، buffer `2G`، max_connections `150`.

### إيقاف الدفع بعد الحدث (الأهم)
> **إطفاء الـ droplet لا يوقف الفوترة** — تبقى تدفع كامل السعر. لتوقّف الدفع فعليًا:
1. خذ snapshot نهائي (Power off → Take Snapshot).
2. **احذف الـ droplet** (Destroy). عندها تدفع فقط تخزين الـ snapshot (سنتات/شهر).
3. عند العودة: أنشئ droplet من الـ snapshot واتبع **القسم 4**.

---

## 6. الصيانة الدورية (لأن التحديث التلقائي معطّل)

حدّث يدويًا كل بضعة أسابيع في وقت هادئ (ليس أثناء استخدام):
```bash
sudo apt update && sudo apt upgrade -y
sudo systemctl restart elections-octane
curl -s -o /dev/null -w "%{http_code}\n" https://elections-vote.duckdns.org/
```

---

## 7. ملف الـ systemd unit الصحيح (المتين)

`/etc/systemd/system/elections-octane.service` يجب أن يكون هكذا (مع `Wants=` و `Restart=always`):

```ini
[Unit]
Description=Elections Octane Server (RoadRunner)
After=network.target mysql.service redis-server.service
Wants=mysql.service redis-server.service
StartLimitIntervalSec=0

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/var/www/elections
ExecStart=/usr/bin/php /var/www/elections/artisan octane:start --server=roadrunner --host=0.0.0.0 --port=8000 --workers=12 --max-requests=1000
ExecReload=/usr/bin/php /var/www/elections/artisan octane:reload
Restart=always
RestartSec=3
LimitNOFILE=65535

[Install]
WantedBy=multi-user.target
```

> `Wants=` (وليس `Requires=`): كي لا يتوقّف Octane عند إعادة تشغيل MySQL/Redis.
> `Restart=always`: كي يعود تلقائيًا بعد أي انهيار/قتل.
> بعد أي تعديل: `sudo systemctl daemon-reload && sudo systemctl restart elections-octane`.
> اختبار التعافي: `sudo systemctl kill -s SIGKILL elections-octane` ثم تأكّد أنه عاد (active + 200).

---

## ملخّص بطاقة سريعة (Cheat sheet)

```bash
# نشر تعديل
cd /var/www/elections && git pull origin main \
  && sudo -u www-data php artisan view:clear \
  && sudo -u www-data php artisan config:clear \
  && sudo systemctl restart elections-octane \
  && curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8000/

# الموقع واقع؟
sudo systemctl restart elections-octane

# فحص صحّة
systemctl is-active elections-octane mysql redis-server; pgrep -fc roadrunner-worker
```
