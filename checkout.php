<?php
// 🔹 مقداردهی اولیه به متغیرها برای جلوگیری از Warning
$name = $email = $province = $city = $address = $postcode = $phone = "";
$submitted = false;
session_start();


/*
|--------------------------------------------------------------------------
| SESSION EXPIRATION FOR SPECIFIC DATA (form_data)
|--------------------------------------------------------------------------
| این بخش بررسی می‌کند که اگر از زمان ذخیره شدن اطلاعات فرم
| بیشتر از 24 ساعت گذشته باشد، فقط داده‌های مربوط به form_data حذف شوند.
| بقیه متغیرهای سشن مثل cart یا user حفظ می‌شوند.
|--------------------------------------------------------------------------
*/

// اگر زمان ذخیره form_data ثبت شده و بیشتر از 1 روز گذشته بود → حذفش کن
if (isset($_SESSION['form_data_created']) && (time() - $_SESSION['form_data_created'] > 86400)) {
    unset($_SESSION['form_data']);
    unset($_SESSION['form_data_created']);
}

// اگر form_data جدیدی وجود ندارد → ثبت زمان ایجاد جدید
if (!isset($_SESSION['form_data_created']) && isset($_SESSION['form_data'])) {
    $_SESSION['form_data_created'] = time();
}
$error=[];
// important functions
   function isPersian($string){
         return preg_match('/^[\x{0600}-\x{06FF}\x{FB8A}\x{067E}\x{0686}\x{0698}\x{06AF}\s‌]+$/u', $string);
      }
    $provinces = [
  "آذربایجان شرقی" => ["تبریز", "مراغه", "مرند", "اهر", "بناب", "میانه", "شبستر", "سراب", "ملکان", "جلفا"],
  "آذربایجان غربی" => ["ارومیه", "خوی", "بوکان", "مهاباد", "میاندوآب", "سلماس", "پیرانشهر"],
  "اردبیل" => ["اردبیل", "مشگین‌شهر", "پارس‌آباد", "خلخال", "نمین", "گرمی"],
  "اصفهان" => ["اصفهان", "کاشان", "خمینی‌شهر", "نجف‌آباد", "شاهین‌شهر", "فلاورجان", "نطنز"],
  "البرز" => ["کرج", "فردیس", "نظرآباد", "اشتهارد", "ماهدشت"],
  "ایلام" => ["ایلام", "دهلران", "مهران", "دره‌شهر", "آبدانان"],
  "بوشهر" => ["بوشهر", "برازجان", "گناوه", "جم", "دیر", "کنگان"],
  "تهران" => ["تهران", "اسلام‌شهر", "ری", "شهریار", "قدس", "ورامین", "دماوند", "پردیس", "رباط‌کریم"],
  "چهارمحال و بختیاری" => ["شهرکرد", "بروجن", "فارسان", "لردگان"],
  "خراسان جنوبی" => ["بیرجند", "قائن", "نهبندان", "فردوس"],
  "خراسان رضوی" => ["مشهد", "نیشابور", "سبزوار", "تربت‌حیدریه", "کاشمر", "قوچان"],
  "خراسان شمالی" => ["بجنورد", "شیروان", "اسفراین", "جاجرم"],
  "خوزستان" => ["اهواز", "آبادان", "دزفول", "خرمشهر", "بهبهان", "اندیمشک", "ماهشهر", "ایذه", "شادگان"],
  "زنجان" => ["زنجان", "ابهر", "خرمدره", "طارم"],
  "سمنان" => ["سمنان", "شاهرود", "دامغان", "گرمسار"],
  "سیستان و بلوچستان" => ["زاهدان", "چابهار", "ایرانشهر", "زابل", "سراوان"],
  "فارس" => ["شیراز", "مرودشت", "فسا", "لار", "جهرم", "کازرون", "نی‌ریز"],
  "قزوین" => ["قزوین", "الوند", "تاکستان", "بوئین‌زهرا"],
  "قم" => ["قم"],
  "کردستان" => ["سنندج", "سقز", "بانه", "مریوان", "قروه"],
  "کرمان" => ["کرمان", "رفسنجان", "جیرفت", "زرند", "سیرجان", "بافت"],
  "کرمانشاه" => ["کرمانشاه", "اسلام‌آباد غرب", "سنقر", "هرسین", "صحنه"],
  "کهگیلویه و بویراحمد" => ["یاسوج", "دهدشت", "گچساران"],
  "گلستان" => ["گرگان", "گنبد کاووس", "علی‌آباد کتول", "آزادشهر"],
  "گیلان" => ["رشت", "انزلی", "لاهیجان", "لنگرود", "رودسر", "تالش", "آستارا"],
  "لرستان" => ["خرم‌آباد", "بروجرد", "الیگودرز", "دورود", "پلدختر"],
  "مازندران" => ["ساری", "بابل", "آمل", "قائم‌شهر", "نوشهر", "چالوس", "بابلسر"],
  "مرکزی" => ["اراک", "ساوه", "خمین", "محلات", "شازند"],
  "هرمزگان" => ["بندرعباس", "قشم", "بندر لنگه", "میناب", "جاسک"],
  "همدان" => ["همدان", "ملایر", "نهاوند", "تویسرکان"],
  "یزد" => ["یزد", "میبد", "اردکان", "مهریز"]
];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Sanitize and validate form inputs
$name = trim($_POST["name"])?? " ";
$email = trim($_POST["email"])?? " ";
$province = trim($_POST["province"]) ?? " ";
$city = trim($_POST["city"])?? " ";
$address = trim($_POST["address"]) ?? " ";
$postcode = trim($_POST["postcode"]) ?? " ";
$phone = trim($_POST["phone"]) ?? " ";
$submitted = true; // mark as submitted

// Store all submitted values in session
$_SESSION["form_data"] = [
  "name" => $name,
  "email" => $email,
  "province" => $province,
  "city" => $city,
  "address" => $address,
  "postcode" => $postcode,
  "phone" => $phone
];

  // بررسی پر بودن همه فیلدها
if(empty($name)||empty($province)||empty($city)||empty($address)||empty($postcode)||empty($phone)||empty($email)){
      $error[]="لطفاً تمام فیلدها را پر کنید.";
    }
   // بررسی فارسی بودن فیلدهای مشخص
if(!isPersian($name)||!isPersian($province)||!isPersian($city)||!isPersian($address)){
     $error[] = "نام، استان، شهر و آدرس باید فارسی باشند.";
}
  // بررسی طول نام
if(mb_strlen($name, 'UTF-8') < 4){
  $error[] = "نام باید بیشتر از 4 کاراکتر باشد.";
}
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
  $error[] = "ایمیل نامعتبر است.";
}
if(!array_key_exists($province,$provinces)){
  $error[] = "استان نامعتبر است.";
}
if(!in_array($city,$provinces[$province])){
  $error[] = "شهر نامعتبر است.";
}
if(!preg_match('/^\d{10}$/',$postcode)){
  $error[] = "کد پستی نامعتبر است.";
}
if(!preg_match('/^09\d{9}$/', $phone)){
  $error[] = "شماره تلفن نامعتبر است.";
}

// show errors 
if(!empty($error)){
  echo "<div style='background:#ffe8e8;border:1px solid #ccc;padding:15px;margin-bottom:20px;border-radius:10px;color:red'>";
  echo "<strong>❌ خطاهای ثبت‌نام:</strong><br>";
  foreach($error as $err){
    echo "- $err<br>";
  }
  echo "</div>";
}
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style/reset.css">
    <link rel="stylesheet" href="style/css.css">
    <link rel="stylesheet" href="style/eightball.css">
      <style>
    body {
      font-family: sans-serif;
      background: #f7f7f7;
      margin: 0;
      direction: rtl;
    }
    .container {
      display: flex;
      justify-content: space-between;
      padding: 40px;
      gap: 30px;
    }
    .checkout-form, .cart-summary {
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .cart-summary {
  max-height: 600px;
  overflow-y: auto;
  scrollbar-width: thin; /* makes scrollbar slimmer on Firefox */
}

.cart-summary::-webkit-scrollbar {
  width: 6px;
}
.cart-summary::-webkit-scrollbar-thumb {
  background-color: #aaa;
  border-radius: 10px;
}
    .checkout-form {
      flex: 2;
    }
    .cart-summary {
      flex: 1;
      max-height: 600px;
      overflow-y: auto;
    }

    h2 {
      margin-top: 0;
    }

    .cart-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 1px solid #eee;
      padding: 10px 0;
    }
    .cart-item img {
      width: 60px;
      height: 60px;
      border-radius: 8px;
      object-fit: cover;
      margin-left: 10px;
    }
    .cart-item-info {
      flex: 1;
    }
    .cart-item-info p {
      margin: 3px 0;
      font-size: 14px;
    }

    .total {
      font-size: 18px;
      font-weight: bold;
      margin-top: 20px;
      text-align: left;
    }

    label {
      display: block;
      margin-top: 15px;
      font-size: 14px;
    }
    input, select {
      width: 100%;
      padding: 8px;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-top: 5px;
      font-size: 14px;
    }
    button {
      margin-top: 20px;
      padding: 10px 20px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }
    .quantity-controls {
  display: flex;

  align-items: center;
  gap: 8px;
  margin-top: 8px;
}

.quantity-controls button {
  width: 26px;
  height: 26px;
  background: #4f5940;
  color: #fff;
  border: none;
  border-radius: 50%;
  font-size: 16px;
  font-weight: bold;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.quantity-controls span {
  min-width: 20px;
  text-align: center;
}

.remove-btn {
  margin-top: 10px;
  background: red;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 6px;
  cursor: pointer;
  font-size: 13px;
}
footer{
        margin-top: 400px;
}

  </style>
</head>
<body>
    <header>


    <div class="links">
      <div class="sidebar-toggle">
        <svg class="burger-menu" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
          <path d="M96 152C96 138.7 106.7 128 120 128L520 128C533.3 128 544 138.7 544 152C544 165.3 533.3 176 520 176L120 176C106.7 176 96 165.3 96 152zM96 320C96 306.7 106.7 296 120 296L520 296C533.3 296 544 306.7 544 320C544 333.3 533.3 344 520 344L120 344C106.7 344 96 333.3 96 320zM544 488C544 501.3 533.3 512 520 512L120 512C106.7 512 96 501.3 96 488C96 474.7 106.7 464 120 464L520 464C533.3 464 544 474.7 544 488z"/>
        </svg>
        <a href="#" class="menu-text">دسته بندی کالاها</a>
      </div>
      <a href="index.html">خانه</a>
      <a href="#">بیلیارد</a>
      <a href="games.html">بازی‌ها</a>
      <a href="BIO.html">بیوگرافی</a>
    </div>

        <div class="logo">
      <img src="images/logo.jpg" alt="Logo">
    </div>
  </header>

  <!-- ===== Overlay ===== -->
  <div class="overlay"></div>

  <!-- ===== Sidebar ===== -->
  <aside>
    <span class="close-btn">&times;</span>
    <ul>
      <li class="has-submenu">
        <a href="#">اسنوکر</a>
        <span>+</span>
        <ul class="submenu">
          <li><a href="snookers.html">میز اسنوکر</a></li>
          <li><a href="snooker.html">توپ اسنوکر</a></li>
        </ul>
      </li>
      <li class="has-submenu">
        <a href="#">ایت بال</a>
        <span>+</span>
        <ul class="submenu">
          <li><a href="eighttable.html">میز ایت بال</a></li>
          <li><a href="ball8.html">توپ ایت بال</a></li>
        </ul>
      </li>
      <li><a href="cues.html">چوب</a></li>
      <li><a href="janebis.html">لوازم جانبی</a></li>
      <li><a href="clths.html">ماهوت</a></li>
      <li><a href="games.html">بازی‌ها</a></li>
    </ul>
  </aside>
  <!-- content -->
   <div class="content">



  <div class="container">
  
  <!-- فرم اطلاعات -->
  <div class="checkout-form">
   <form id="addressForm" onsubmit="return validateForm()" method="post" action="checkout.php">
  <label>نام و نام خانوادگی</label>
  <input type="text" id="name" name="name" placeholder="مثلاً علی احمدی"  value="<?php echo isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : ''; ?>" required>
  <div class="error" id="nameError" style="display:none;color:red;font-size:12px;">لطفاً نام و نام خانوادگی خود را وارد کنید</div>

  <label>ایمیل</label>
  <input type="email" id="email" name="email" placeholder="example@email.com" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>" required>
  <div class="error" id="emailError" style="display:none;color:red;font-size:12px;">ایمیل معتبر وارد کنید</div>

  <label for="province">استان:</label>
  <select id="province" name="province" onchange="updateCities()" required>
    <option value="">انتخاب کنید...</option>
    <?php foreach ($provinces as $prov=>$cities){
      $selected =(isset($_SESSION["form_data"]["province"]) && $_SESSION["form_data"]["province"] === $prov) ? "selected" : "";
       echo "<option value=\"$prov\" $selected>$prov</option>";
    }?>
  </select>
  <div class="error" id="provinceError" style="display:none;color:red;font-size:12px;">لطفاً استان را انتخاب کنید</div>

  <label for="city">شهر:</label>
  <select id="city" name="city" required>
    <option value="">ابتدا استان را انتخاب کنید</option>
      <?php
    if (isset($_SESSION['form_data']['province']) && array_key_exists($_SESSION['form_data']['province'], $provinces)) {
        foreach ($provinces[$_SESSION['form_data']['province']] as $ct) {
            $selected = (isset($_SESSION['form_data']['city']) && $_SESSION['form_data']['city'] == $ct) ? 'selected' : '';
            echo "<option value=\"$ct\" $selected>$ct</option>";
        }
    }
    ?>
  </select>
  <div class="error" id="cityError" style="display:none;color:red;font-size:12px;">لطفاً شهر را انتخاب کنید</div>

  <label>آدرس</label>
  <input type="text" id="address" name="address" placeholder="مثلاً خیابان ولیعصر، نبش پارک ملت" value="<?php echo isset($_SESSION['form_data']['address']) ? htmlspecialchars($_SESSION['form_data']['address']) : ''; ?>" required>
  <div class="error" id="addressError" style="display:none;color:red;font-size:12px;">لطفاً آدرس را وارد کنید</div>

  <label for="postcode">کد پستی:</label>
  <input type="text" id="postcode" name="postcode" placeholder="مثلاً ۴۱۳۶۳۵۵۸۷۴" value="<?php echo isset($_SESSION['form_data']['postcode']) ? htmlspecialchars($_SESSION['form_data']['postcode']) : ''; ?>" required>
  <div class="error" id="postcodeError" style="display:none;color:red;font-size:12px;">کد پستی باید ۱۰ رقم باشد</div>

  <label>تلفن همراه</label>
  <input type="tel" id="phone" name="phone" placeholder="مثلاً ۰۹۱۲۳۴۵۶۷۸۹" value="<?php echo isset($_SESSION['form_data']['phone']) ? htmlspecialchars($_SESSION['form_data']['phone']) : ''; ?>" required>
  <div class="error" id="phoneError" style="display:none;color:red;font-size:12px;">شماره تلفن معتبر وارد کنید</div>

  <button type="submit"name="submit">نمایش اطلاعات</button>
  <?php
  if(empty($error)&& $submitted == true){
   echo "<div   style='background:#e8ffe8;border:1px solid #ccc;padding:15px;margin-bottom:20px;margin-top:20px;border-radius:10px;color:green;'>";
        echo "<strong>✅ اطلاعات ثبت‌شده:</strong><br>";
        echo "نام: " . htmlspecialchars($name) . "<br>";
        echo "ایمیل: " . htmlspecialchars($email) . "<br>";
        echo "استان: " . htmlspecialchars($province) . "<br>";
        echo "شهر: " . htmlspecialchars($city) . "<br>";
        echo "آدرس: " . htmlspecialchars($address) . "<br>";
        echo "کد پستی: " . htmlspecialchars($postcode) . "<br>";
        echo "تلفن: " . htmlspecialchars($phone) . "<br>";
        echo "</div>";
}?>
</form>

     
  </div>

  <!-- سبد خرید -->
  <div class="cart-summary">
    <h2>سبد خرید شما</h2>
    <div id="cart-items"></div>
    <div class="total">
      جمع کل: <span id="total-price">0</span> تومان
    </div>
    <button onclick="completeOrder()">ثبت سفارش</button>
  </div>
</div>
  </div>
   <footer>
      <div class="footer-container">
    <!-- Logo and Contact Info -->
    <div class="footer-logo-section">
      <img src="images/logo.jpg" alt="Brunswick Logo" class="footer-logo">

      <a href="tel:8003368771" class="footer-phone">800-336-8771</a>
      <div class="social-icons">
        <a href="#"><i class="fab fa-facebook"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-youtube"></i></a>
      </div>
    </div>

    <!-- Customer Care Section -->
    <div class="footer-links">
    <h3>خدمات مشتریان</h3>
      <ul>
        
        <li><a href="#">تماس با ما</a></li>
        <li><a href="#">حمل و بازگشت کالا</a></li>
        <li><a href="#">گارانتی</a></li>
        <li><a href="#">سوالات متداول</a></li>
        <li><a href="#">مراقبت از میز</a></li>
      </ul>
    </div>

    <!-- Company Section -->
    <div class="footer-links">
      <h3>شرکت</h3>
      <ul>
        <li><a href="#"> چرا ما؟</a></li>
        <li><a href="#">تاریخچه ما</a></li>
        <li><a href="#">یادگیری</a></li>
      </ul>
    </div>

    <!-- Shop Section -->
    <div class="footer-links">
      <h3>فروشگاه</h3>
      <ul>
        <li><a href="#">بیلیارد</a></li>
        <li><a href="#">بازی‌ها</a></li>
      </ul>
    </div>

    <!-- Newsletter Section -->
    <div class="footer-newsletter">
      <h3>در تماس باشید</h3>
      <p>برای اطلاع از آخرین محصولات، اخبار و رویدادهای ویژه ثبت‌نام کنید</p>
      <div class="newsletter-form">
        <button>➤</button>
        <input type="email" placeholder="آدرس ایمیل">
        
      </div>
    </div>
  </div>

  <!-- Bottom Bar -->
  <div class="footer-bottom">
    <ul>
      <li><a href="#">سیاست حفظ حریم خصوصی</a></li>
      <li><a href="#">شرایط استفاده</a></li>
      <li><a href="#">فروش عمده</a></li>
    </ul>
  </div>
  </footer>
  
    <!-- ===== Script ===== -->
  <script>
    const burger = document.querySelector('.burger-menu');
    const sidebar = document.querySelector('aside');
    const overlay = document.querySelector('.overlay');
    const closeBtn = document.querySelector('.close-btn');
    const submenuToggles = document.querySelectorAll('.has-submenu > span');

    // باز کردن منو
    function openMenu() {
      if (window.innerWidth <= 600) {
        sidebar.style.right = '0';
        overlay.style.display = 'block';
      } else {
        sidebar.style.right = '0';
        document.querySelector('.content').style.marginRight = '250px';
      }
    }

    // بستن منو
    function closeMenu() {
      if (window.innerWidth <= 600) {
        sidebar.style.right = '-100%';
        overlay.style.display = 'none';
      } else {
        sidebar.style.right = '-250px';
        document.querySelector('.content').style.marginRight = '0';
      }
    }

    burger.addEventListener('click', openMenu);
    closeBtn.addEventListener('click', closeMenu);
    overlay.addEventListener('click', closeMenu);

    // باز و بسته کردن زیرمنوها
    submenuToggles.forEach(toggle => {
      toggle.addEventListener('click', () => {
        const submenu = toggle.nextElementSibling;
        const isOpen = submenu.style.display === 'block';
        submenu.style.display = isOpen ? 'none' : 'block';
        toggle.textContent = isOpen ? '+' : '-';
      });
    });
    // document.querySelector(".table-img").addEventListener("click", function() {
    //     console.log("hi");
    //   this.src = "images/eightbal/batch1-allenton-7-foot-pool-table-with-ball-and-claw-leg__espresso_2_533x.jpg";
    // });

let cart = JSON.parse(localStorage.getItem("cart")) || [];

function renderCart() {
  const cartItemsDiv = document.getElementById("cart-items");
  const totalPriceSpan = document.getElementById("total-price");
  cartItemsDiv.innerHTML = "";
  let total = 0;

  cart.forEach((item, index) => {
    total += item.price * item.qty;

    cartItemsDiv.innerHTML += `
      <div class="cart-item">
        <img src="${item.img}" alt="${item.name}">
        <div class="cart-item-info">
          <p><strong>${item.name}</strong></p>
          <p>${item.price.toLocaleString()} تومان</p>

          <div class="quantity-controls">
            <button onclick="changeQty(${index}, -1)">−</button>
            <span>${item.qty}</span>
            <button onclick="changeQty(${index}, 1)">+</button>
          </div>

          <button class="remove-btn" onclick="removeItem(${index})">
            حذف
          </button>
        </div>
      </div>
    `;
  });

  totalPriceSpan.textContent = total.toLocaleString();
  localStorage.setItem("cart", JSON.stringify(cart));
}

function changeQty(index, change) {
  cart[index].qty += change;

  if (cart[index].qty < 1) {
    cart[index].qty = 1; // prevent zero or negative quantity
  }

  localStorage.setItem("cart", JSON.stringify(cart));
  renderCart();
}

function removeItem(index) {
  if (confirm("آیا از حذف این محصول مطمئن هستید؟")) {
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    renderCart();
  }
}

function completeOrder() {
  alert("سفارش شما با موفقیت ثبت شد! ✅");
  localStorage.removeItem("cart");
  window.location.href = "index.html";
}

renderCart()





// نمایش اطلاعات
const provinces = {
  "آذربایجان شرقی": ["تبریز", "مراغه", "مرند", "اهر", "بناب", "میانه", "شبستر", "سراب", "ملکان", "جلفا"],
  "آذربایجان غربی": ["ارومیه", "خوی", "بوکان", "مهاباد", "میاندوآب", "سلماس", "پیرانشهر"],
  "اردبیل": ["اردبیل", "مشگین‌شهر", "پارس‌آباد", "خلخال", "نمین", "گرمی"],
  "اصفهان": ["اصفهان", "کاشان", "خمینی‌شهر", "نجف‌آباد", "شاهین‌شهر", "فلاورجان", "نطنز"],
  "البرز": ["کرج", "فردیس", "نظرآباد", "اشتهارد", "ماهدشت"],
  "ایلام": ["ایلام", "دهلران", "مهران", "دره‌شهر", "آبدانان"],
  "بوشهر": ["بوشهر", "برازجان", "گناوه", "جم", "دیر", "کنگان"],
  "تهران": ["تهران", "اسلام‌شهر", "ری", "شهریار", "قدس", "ورامین", "دماوند", "پردیس", "رباط‌کریم"],
  "چهارمحال و بختیاری": ["شهرکرد", "بروجن", "فارسان", "لردگان"],
  "خراسان جنوبی": ["بیرجند", "قائن", "نهبندان", "فردوس"],
  "خراسان رضوی": ["مشهد", "نیشابور", "سبزوار", "تربت‌حیدریه", "کاشمر", "قوچان"],
  "خراسان شمالی": ["بجنورد", "شیروان", "اسفراین", "جاجرم"],
  "خوزستان": ["اهواز", "آبادان", "دزفول", "خرمشهر", "بهبهان", "اندیمشک", "ماهشهر", "ایذه", "شادگان"],
  "زنجان": ["زنجان", "ابهر", "خرمدره", "طارم"],
  "سمنان": ["سمنان", "شاهرود", "دامغان", "گرمسار"],
  "سیستان و بلوچستان": ["زاهدان", "چابهار", "ایرانشهر", "زابل", "سراوان"],
  "فارس": ["شیراز", "مرودشت", "فسا", "لار", "جهرم", "کازرون", "نی‌ریز"],
  "قزوین": ["قزوین", "الوند", "تاکستان", "بوئین‌زهرا"],
  "قم": ["قم"],
  "کردستان": ["سنندج", "سقز", "بانه", "مریوان", "قروه"],
  "کرمان": ["کرمان", "رفسنجان", "جیرفت", "زرند", "سیرجان", "بافت"],
  "کرمانشاه": ["کرمانشاه", "اسلام‌آباد غرب", "سنقر", "هرسین", "صحنه"],
  "کهگیلویه و بویراحمد": ["یاسوج", "دهدشت", "گچساران"],
  "گلستان": ["گرگان", "گنبد کاووس", "علی‌آباد کتول", "آزادشهر"],
  "گیلان": ["رشت", "انزلی", "لاهیجان", "لنگرود", "رودسر", "تالش", "آستارا"],
  "لرستان": ["خرم‌آباد", "بروجرد", "الیگودرز", "دورود", "پلدختر"],
  "مازندران": ["ساری", "بابل", "آمل", "قائم‌شهر", "نوشهر", "چالوس", "بابلسر"],
  "مرکزی": ["اراک", "ساوه", "خمین", "محلات", "شازند"],
  "هرمزگان": ["بندرعباس", "قشم", "بندر لنگه", "میناب", "جاسک"],
  "همدان": ["همدان", "ملایر", "نهاوند", "تویسرکان"],
  "یزد": ["یزد", "میبد", "اردکان", "مهریز"]
};



// ====== پرکردن استان‌ها ======
window.onload = function () {
  const provinceSelect = document.getElementById("province");
  for (const province in provinces) {
    const option = document.createElement("option");
    option.value = province;
    option.textContent = province;
    provinceSelect.appendChild(option);
  }
};
function updateCities() {
  const province = document.getElementById("province").value;
  const citySelect = document.getElementById("city");
  citySelect.innerHTML = '<option value="">انتخاب کنید...</option>';

  if (province && provinces[province]) {
    provinces[province].forEach(city => {
      const option = document.createElement("option");
      option.value = city;
      option.textContent = city;
      citySelect.appendChild(option);
    });
  }
}

function showResult() {
  const province = document.getElementById("province").value;
  const city = document.getElementById("city").value;
  const postcode = document.getElementById("postcode").value;
  const name = document.getElementById("name").value;
  const address = document.getElementById("address").value;
  const email = document.getElementById("email").value;
  const phone = document.getElementById("phone").value;

  if (!province || !city || !postcode || !name || !address || !email || !phone) {
    alert("لطفاً تمام فیلدها را پر کنید");
    return;
  }

  document.getElementById("result").innerHTML = `
    <strong>استان:</strong> ${province}<br>
    <strong>شهر:</strong> ${city}<br>
    <strong>کد پستی:</strong> ${postcode}
    <strong>نام:</strong> ${name}<br>
    <strong>تلفن همراه:</strong> ${phone}<br>
    <strong>ایمیل:</strong> ${email}
  `;
}
 function validateForm() {
         let valid = true;

    const province = document.getElementById("province").value.trim();
    const city = document.getElementById("city").value.trim();
    const postcode = document.getElementById("postcode").value.trim();
    const name = document.getElementById("name").value.trim();
    const address = document.getElementById("address").value.trim();
    const email = document.getElementById("email").value.trim();
    const phone = document.getElementById("phone").value.trim();

    if (!province || !city || !postcode || !name || !address || !email || !phone) {
        alert("لطفاً تمام فیلدها را پر کنید");
        valid = false;
    }
    // پنهان کردن خطاها
    document.querySelectorAll('.error').forEach(e => e.style.display = 'none');

    // اعتبارسنجی نام
     const persianRegex = /^[\u0600-\u06FF\s]+$/;
    if (!name || !persianRegex.test(name)) {
        document.getElementById("nameError").style.display = 'block';
        valid = false;
    }

    // اعتبارسنجی ایمیل
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        document.getElementById("emailError").style.display = 'block';
        valid = false;
    }

    // اعتبارسنجی استان
    if (!province) {
        document.getElementById("provinceError").style.display = 'block';
        valid = false;
    }

    // اعتبارسنجی شهر
    if (!city) {
        document.getElementById("cityError").style.display = 'block';
        valid = false;
    }

    // اعتبارسنجی آدرس
      const addressRegex = /^[\u0600-\u06FF0-9۰-۹\s،]+$/;
    if (!address || !addressRegex.test(address)) {
        document.getElementById("addressError").style.display = 'block';
        valid = false;
    }

    // اعتبارسنجی کد پستی (۱۰ رقم)
    const postcodeRegex = /^[0-9]{10}$/;
    if (!postcodeRegex.test(postcode)) {
        document.getElementById("postcodeError").style.display = 'block';
        valid = false;
    }

    // اعتبارسنجی شماره تلفن (ایران)
    const phoneRegex = /^09\d{9}$/;
    if (!phoneRegex.test(phone)) {
        document.getElementById("phoneError").style.display = 'block';
        valid = false;
    }

    if (valid) {
        // document.getElementById("result").innerHTML = `
        //     ✅ اطلاعات شما با موفقیت ثبت شد.<br>
        //     نام: ${name}<br>
        //     ایمیل: ${email}<br>
        //     استان: ${province}<br>
        //     شهر: ${city}<br>
        //     آدرس: ${address}<br>
        //     کد پستی: ${postcode}<br>
        //     تلفن: ${phone}
        // `;
    }

    return valid; // جلوگیری از رفرش صفحه
}
// console.log(validateForm())
  </script>

</body>
</html>
