<!-- TRANG ĐẶT LỊCH HẸN -->
<style>
#bk-app{background:#f0f8fd;min-height:80vh;padding:0 0 60px;font-family:'Roboto',sans-serif;color:#1e2d3d;}
#bk-app *,#bk-app *::before,#bk-app *::after{box-sizing:border-box;}
/* Progress */
#bk-app .bk-prog-wrap{background:#fff;border-bottom:1px solid #d4ecf8;padding:22px 20px 18px;margin-bottom:28px;}
#bk-app .bk-prog-row{display:flex;justify-content:space-between;align-items:flex-start;max-width:560px;margin:0 auto;position:relative;}
#bk-app .bk-prog-row::before{content:'';position:absolute;top:16px;left:20px;right:20px;height:3px;background:#b3dff5;border-radius:99px;z-index:0;}
#bk-app .bk-prog-fill{position:absolute;top:16px;left:20px;height:3px;background:#1a9bd7;border-radius:99px;z-index:1;transition:width .4s ease;width:0;}
#bk-app .bk-stp{position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;flex:1;}
#bk-app .bk-stp-c{width:34px;height:34px;border-radius:50%;background:#b3dff5;border:3px solid #b3dff5;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#6b8ea8;transition:all .3s;}
#bk-app .bk-stp-l{font-size:11px;margin-top:7px;color:#6b8ea8;font-weight:500;text-align:center;white-space:nowrap;display:block;}
#bk-app .bk-stp.on .bk-stp-c{background:#1a9bd7;border-color:#1a9bd7;color:#fff;box-shadow:0 0 0 4px rgba(26,155,215,.2);}
#bk-app .bk-stp.on .bk-stp-l{color:#1a9bd7;font-weight:700;}
#bk-app .bk-stp.dn .bk-stp-c{background:#16a34a;border-color:#16a34a;color:#fff;font-size:0;}
#bk-app .bk-stp.dn .bk-stp-c::after{content:'\f00c';font-family:'Font Awesome 5 Free';font-weight:900;font-size:13px;}
#bk-app .bk-stp.dn .bk-stp-l{color:#16a34a;}
/* Body & Card */
#bk-app .bk-body{max-width:860px;margin:0 auto;padding:0 20px;}
#bk-app .bk-card{background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(26,155,215,.12);border:1px solid #d4ecf8;overflow:hidden;}
#bk-app .bk-card-hd{display:flex;align-items:center;gap:16px;padding:20px 24px 18px;border-bottom:1px solid #d4ecf8;background:linear-gradient(135deg,#edf7fd,#fff);}
#bk-app .bk-card-ic{width:46px;height:46px;min-width:46px;border-radius:12px;background:#1a9bd7;color:#fff;display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 4px 10px rgba(26,155,215,.3);}
#bk-app .bk-card-ti{font-size:17px;font-weight:700;color:#1e2d3d;line-height:1.3;}
#bk-app .bk-card-sb{font-size:13px;color:#6b8ea8;margin-top:2px;}
/* Error */
#bk-app .bk-err{display:none;align-items:center;gap:8px;margin:14px 24px 0;padding:10px 14px;border-radius:8px;background:#fee2e2;color:#dc2626;font-size:13px;font-weight:500;border:1px solid #fca5a5;}
</style>
<style>
/* Accordion & Services */
#bk-app .bk-acc{padding:18px 24px 22px;display:flex;flex-direction:column;gap:10px;}
#bk-app .bk-ab{border:1.5px solid #d4ecf8;border-radius:8px;overflow:hidden;}
#bk-app .bk-ah{width:100%;display:flex;align-items:center;gap:10px;padding:12px 16px;background:#e8f6fd;border:none;cursor:pointer;font-size:14px;font-weight:700;color:#1278aa;text-align:left;outline:none;}
#bk-app .bk-ah:hover{background:#b3dff5;}
#bk-app .bk-ah-ti{display:flex;align-items:center;gap:7px;flex:1;font-size:14px;font-weight:700;color:#1278aa;}
#bk-app .bk-ah-ti i{color:#1a9bd7;font-size:12px;}
#bk-app .bk-ah-ct{font-size:12px;font-weight:400;color:#6b8ea8;}
#bk-app .bk-ah-ar{font-size:11px;color:#1a9bd7;transition:transform .25s;flex-shrink:0;}
#bk-app .bk-ah-ar.op{transform:rotate(180deg);}
#bk-app .bk-abody{display:none;}
#bk-app .bk-abody.op{display:block;}
#bk-app .bk-sr{display:flex;align-items:center;gap:12px;padding:14px 18px;border-bottom:1px solid #d4ecf8;cursor:pointer;background:#fff;}
#bk-app .bk-sr:last-child{border-bottom:none;}
#bk-app .bk-sr:hover{background:#e8f6fd;}
#bk-app .bk-sr.sel{background:#edf8ff;}
#bk-app .bk-tick{width:24px;height:24px;min-width:24px;border-radius:6px;border:2px solid #b3dff5;background:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;color:transparent;transition:all .15s;}
#bk-app .bk-sr.sel .bk-tick{background:#1a9bd7;border-color:#1a9bd7;color:#fff;}
#bk-app .bk-si{flex:1;min-width:0;}
#bk-app .bk-sn{display:block;font-size:14px;font-weight:600;color:#1e2d3d;}
#bk-app .bk-sd{display:flex;align-items:center;gap:4px;font-size:12px;color:#6b8ea8;margin-top:2px;}
#bk-app .bk-sp{font-size:14px;font-weight:700;color:#1a9bd7;white-space:nowrap;}
#bk-app input[type=checkbox].bk-hcb{display:none !important;}
/* Staff */
#bk-app .bk-sg{display:flex;flex-wrap:wrap;gap:14px;padding:22px 24px;}
#bk-app .bk-sc{border:2px solid #d4ecf8;border-radius:10px;cursor:pointer;display:flex;flex-direction:column;align-items:center;padding:20px 14px 16px;background:#fff;min-width:140px;flex:1;max-width:200px;transition:all .2s;}
#bk-app .bk-sc:hover{border-color:#b3dff5;box-shadow:0 4px 16px rgba(26,155,215,.15);}
#bk-app .bk-sc.sel{border-color:#1a9bd7;box-shadow:0 4px 18px rgba(26,155,215,.25);}
#bk-app .bk-av{width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,#1a9bd7,#1278aa);color:#fff;font-size:20px;font-weight:700;display:flex;align-items:center;justify-content:center;margin-bottom:10px;box-shadow:0 4px 12px rgba(26,155,215,.3);}
#bk-app .bk-stnm{font-size:14px;font-weight:700;color:#1e2d3d;text-align:center;line-height:1.3;}
#bk-app .bk-role{font-size:12px;color:#6b8ea8;margin-top:3px;}
#bk-app .bk-sbdg{display:none;align-items:center;gap:4px;font-size:11px;font-weight:600;color:#1a9bd7;margin-top:8px;}
#bk-app .bk-sc.sel .bk-sbdg{display:flex;}
#bk-app input[type=radio].bk-hr{display:none !important;}
</style>
<style>
/* Calendar */
#bk-app .bk-cw{padding:18px 24px 22px;overflow-x:auto;}
#bk-app .bk-cl{display:flex;flex-direction:column;align-items:center;gap:12px;padding:36px 0;color:#6b8ea8;font-size:14px;}
#bk-app .bk-spin{width:32px;height:32px;border:3px solid #b3dff5;border-top-color:#1a9bd7;border-radius:50%;animation:bksp .7s linear infinite;}
@keyframes bksp{to{transform:rotate(360deg);}}
#bk-app .bk-cal{min-width:460px;border:1.5px solid #d4ecf8;border-radius:8px;overflow:hidden;}
#bk-app .bk-cdays{display:flex;background:#1a9bd7;}
#bk-app .bk-cdcol{flex:1;text-align:center;padding:11px 4px;color:#fff;border-right:1px solid rgba(255,255,255,.15);}
#bk-app .bk-cdcol:last-child{border-right:none;}
#bk-app .bk-cdnm{display:block;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.4px;}
#bk-app .bk-cddt{display:block;font-size:11px;opacity:.85;margin-top:1px;}
#bk-app .bk-cslots{display:flex;background:#fff;}
#bk-app .bk-cscol{flex:1;border-right:1px solid #d4ecf8;padding:8px 3px;display:flex;flex-direction:column;align-items:center;gap:4px;}
#bk-app .bk-cscol:last-child{border-right:none;}
#bk-app .bk-cem{color:#6b8ea8;font-size:12px;padding:14px 0;text-align:center;}
#bk-app input[type=radio][name=desired_date_time]{display:none !important;}
#bk-app .bk-ts{display:block;width:100%;max-width:70px;text-align:center;font-size:12px;font-weight:500;color:#1278aa;background:#e8f6fd;border:1.5px solid #d4ecf8;border-radius:6px;padding:7px 2px;cursor:pointer;transition:all .15s;margin:0 auto;}
#bk-app .bk-ts:hover{background:#b3dff5;border-color:#1a9bd7;}
#bk-app input[type=radio][name=desired_date_time]:checked+.bk-ts{background:#1a9bd7;color:#fff;border-color:#1278aa;font-weight:700;box-shadow:0 2px 8px rgba(26,155,215,.3);}
/* Form thông tin */
#bk-app .bk-fg2{display:flex;flex-wrap:wrap;gap:16px;padding:22px 24px;}
#bk-app .bk-fgi{flex:1;min-width:220px;display:flex;flex-direction:column;gap:5px;}
#bk-app .bk-lb{font-size:13px;font-weight:600;color:#1e2d3d;}
#bk-app .bk-req{color:#dc2626;}
#bk-app .bk-in{padding:11px 14px;border:1.5px solid #d4ecf8;border-radius:8px;font-size:14px;color:#1e2d3d;background:#fff;outline:none;width:100%;transition:border-color .15s,box-shadow .15s;}
#bk-app .bk-in:focus{border-color:#1a9bd7;box-shadow:0 0 0 3px rgba(26,155,215,.15);}
#bk-app .bk-in.err{border-color:#dc2626;}
#bk-app .bk-fe{font-size:12px;color:#dc2626;display:none;margin-top:2px;}
#bk-app .bk-note{display:flex;align-items:center;gap:10px;margin:0 24px 22px;padding:11px 14px;background:#e8f6fd;border-radius:8px;font-size:12px;color:#6b8ea8;border:1px solid #d4ecf8;}
#bk-app .bk-note i{color:#1a9bd7;font-size:15px;flex-shrink:0;}
/* Nút điều hướng */
#bk-app .bk-nav{display:flex;justify-content:flex-end;align-items:center;gap:12px;margin-top:22px;}
#bk-app .bk-btn{display:inline-flex;align-items:center;gap:8px;padding:11px 26px;font-size:15px;font-weight:600;border:none;border-radius:8px;cursor:pointer;transition:all .15s;font-family:'Roboto',sans-serif;line-height:1;text-decoration:none !important;}
#bk-app .bk-back{background:#fff;color:#6b8ea8;border:1.5px solid #d4ecf8;}
#bk-app .bk-back:hover{background:#e8f6fd;color:#1278aa;}
#bk-app .bk-next{background:#1a9bd7;color:#fff !important;box-shadow:0 4px 14px rgba(26,155,215,.35);}
#bk-app .bk-next:hover{background:#1278aa;}
#bk-app .bk-next:disabled{background:#b3dff5;box-shadow:none;cursor:not-allowed;}
/* Ẩn dummy steps */
.bk-dummy-steps{display:none !important;}
</style>

<div id="bk-app">

<!-- Thanh tiến trình -->
<div class="bk-prog-wrap">
  <div class="bk-prog-row">
    <div class="bk-prog-fill" id="bkFill"></div>
    <?php foreach(['Dịch vụ','Nhân viên','Lịch hẹn','Xác nhận'] as $si=>$sl): ?>
    <div class="bk-stp" data-s="<?=$si?>">
      <div class="bk-stp-c"><span><?=$si+1?></span></div>
      <div class="bk-stp-l"><?=$sl?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="bk-body">
<form method="post" id="bk-form" action="<?=base_url('index.php?url=appointment')?>">

<?php if($message): ?>
<div style="padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;
  background:<?=$messageType==='success'?'#dcfce7':'#fee2e2'?>;
  color:<?=$messageType==='success'?'#16a34a':'#dc2626'?>">
  <?=htmlspecialchars($message)?>
</div>
<?php endif; ?>

<!-- BƯỚC 1 - Dịch vụ -->
<div class="bk-tab" id="tab_services">
<div class="bk-card">
  <div class="bk-card-hd">
    <div class="bk-card-ic"><i class="fas fa-cut"></i></div>
    <div><div class="bk-card-ti">Chọn dịch vụ</div><div class="bk-card-sb">Bạn có thể chọn nhiều dịch vụ cùng lúc</div></div>
  </div>
  <div class="bk-err" id="err1"><i class="fas fa-exclamation-circle"></i> Vui lòng chọn ít nhất một dịch vụ!</div>
  <div class="bk-acc">
    <?php $ci=0; foreach($servicesByCategory as $cn=>$cs): ?>
    <div class="bk-ab">
      <button type="button" class="bk-ah" onclick="bkCat(this)">
        <span class="bk-ah-ti"><i class="fas fa-tag"></i><?=htmlspecialchars($cn)?></span>
        <span class="bk-ah-ct"><?=count($cs)?> dịch vụ</span>
        <i class="fas fa-chevron-down bk-ah-ar<?=$ci===0?' op':''?>"></i>
      </button>
      <div class="bk-abody<?=$ci===0?' op':''?>">
        <?php foreach($cs as $r): ?>
        <div class="bk-sr" onclick="bkSvc(this)">
          <div class="bk-tick"><i class="fas fa-check"></i></div>
          <input type="checkbox" name="selected_services[]" value="<?=(int)$r['ma_dich_vu']?>" class="bk-hcb">
          <div class="bk-si">
            <span class="bk-sn"><?=htmlspecialchars($r['ten_dich_vu'])?></span>
            <span class="bk-sd"><i class="fas fa-clock"></i> <?=(int)$r['thoi_luong']?> phút</span>
          </div>
          <div class="bk-sp"><?=format_money($r['gia'])?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php $ci++; endforeach; ?>
  </div>
</div>
</div>

<!-- BƯỚC 2 - Nhân viên -->
<div class="bk-tab" id="tab_employees">
<div class="bk-card">
  <div class="bk-card-hd">
    <div class="bk-card-ic"><i class="fas fa-user-tie"></i></div>
    <div><div class="bk-card-ti">Chọn nhân viên</div><div class="bk-card-sb">Chọn người thợ bạn muốn phục vụ</div></div>
  </div>
  <div class="bk-err" id="err2"><i class="fas fa-exclamation-circle"></i> Vui lòng chọn nhân viên!</div>
  <div class="bk-sg">
    <?php foreach($employees as $e):
      $av=mb_strtoupper(mb_substr($e['ten'],0,1,'UTF-8').mb_substr($e['ho_dem'],0,1,'UTF-8'));
    ?>
    <div class="bk-sc" onclick="bkStaff(this)">
      <input type="radio" name="selected_employee" value="<?=(int)$e['ma_nhan_vien']?>" class="bk-hr">
      <div class="bk-av"><?=htmlspecialchars($av)?></div>
      <div class="bk-stnm"><?=htmlspecialchars($e['ten'].' '.$e['ho_dem'])?></div>
      <?php if(!empty($e['chuc_vu'])): ?><div class="bk-role"><?=htmlspecialchars($e['chuc_vu'])?></div><?php endif; ?>
      <div class="bk-sbdg"><i class="fas fa-check-circle"></i> Đã chọn</div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
</div>

<!-- BƯỚC 3 - Lịch hẹn -->
<div class="bk-tab" id="tab_calendar">
<div class="bk-card">
  <div class="bk-card-hd">
    <div class="bk-card-ic"><i class="fas fa-calendar-alt"></i></div>
    <div><div class="bk-card-ti">Chọn ngày &amp; giờ</div><div class="bk-card-sb">Chọn khung giờ phù hợp với bạn</div></div>
  </div>
  <div class="bk-err" id="err3"><i class="fas fa-exclamation-circle"></i> Vui lòng chọn thời gian!</div>
  <div class="bk-cw" id="bk-cal-wrap">
    <div id="bk-cal-loading" class="bk-cl"><div class="bk-spin"></div><span>Đang tải lịch...</span></div>
  </div>
</div>
</div>

<!-- BƯỚC 4 - Thông tin khách -->
<div class="bk-tab" id="tab_client">
<div class="bk-card">
  <div class="bk-card-hd">
    <div class="bk-card-ic"><i class="fas fa-user"></i></div>
    <div><div class="bk-card-ti">Thông tin của bạn</div><div class="bk-card-sb">Điền đầy đủ để hoàn tất đặt lịch</div></div>
  </div>
  <div class="bk-fg2">
    <div class="bk-fgi">
      <label class="bk-lb">Họ <span class="bk-req">*</span></label>
      <input type="text" name="client_first_name" id="inp_fname" class="bk-in" placeholder="Nguyễn">
      <span class="bk-fe" id="fe1">Vui lòng nhập họ</span>
    </div>
    <div class="bk-fgi">
      <label class="bk-lb">Tên <span class="bk-req">*</span></label>
      <input type="text" name="client_last_name" id="inp_lname" class="bk-in" placeholder="Văn A">
      <span class="bk-fe" id="fe2">Vui lòng nhập tên</span>
    </div>
    <div class="bk-fgi">
      <label class="bk-lb">Email <span class="bk-req">*</span></label>
      <input type="email" name="client_email" id="inp_email" class="bk-in" placeholder="example@email.com">
      <span class="bk-fe" id="fe3">Email không hợp lệ</span>
    </div>
    <div class="bk-fgi">
      <label class="bk-lb">Số điện thoại <span class="bk-req">*</span></label>
      <input type="text" name="client_phone_number" id="inp_phone" class="bk-in" placeholder="09xxxxxxxx">
      <span class="bk-fe" id="fe4">Số điện thoại không hợp lệ (10 chữ số)</span>
    </div>
  </div>
  <div class="bk-note"><i class="fas fa-shield-alt"></i> Thông tin của bạn được bảo mật và chỉ dùng để xác nhận lịch hẹn.</div>
</div>
</div>

<!-- Nút điều hướng -->
<input type="hidden" name="submit_book_appointment_form" value="1">
<div class="bk-nav">
  <button type="button" id="bk-prev" class="bk-btn bk-back" onclick="bkPrev()">
    <i class="fas fa-arrow-left"></i> Quay lại
  </button>
  <button type="button" id="bk-next" class="bk-btn bk-next" onclick="bkNext()">
    Tiếp theo <i class="fas fa-arrow-right"></i>
  </button>
</div>

</form>
</div><!-- /bk-body -->
</div><!-- /bk-app -->

<script>
(function(){
  'use strict';

  /* ── Trạng thái ── */
  var TABS   = ['tab_services','tab_employees','tab_calendar','tab_client'];
  var curTab = 0;
  var calLoaded = false; // đã load lịch chưa

  /* ── Khởi động ── */
  document.addEventListener('DOMContentLoaded', function(){
    showTab(0);
  });

  /* ── Hiển thị tab ── */
  function showTab(n){
    curTab = n;
    TABS.forEach(function(id,i){
      var el = document.getElementById(id);
      if(el) el.style.display = (i===n) ? 'block' : 'none';
    });

    var prev = document.getElementById('bk-prev');
    var next = document.getElementById('bk-next');
    if(prev) prev.style.visibility = (n===0) ? 'hidden' : 'visible';
    if(next){
      next.innerHTML = (n===TABS.length-1)
        ? '<i class="fas fa-check"></i> Xác nhận đặt lịch'
        : 'Tiếp theo <i class="fas fa-arrow-right"></i>';
    }

    /* Cập nhật thanh tiến trình */
    var steps = document.querySelectorAll('#bk-app .bk-stp');
    var fill  = document.getElementById('bkFill');
    var total = steps.length - 1;
    if(fill) fill.style.width = (total>0 ? Math.min(n/total*100,100) : 0) + '%';
    steps.forEach(function(el,i){
      el.classList.remove('on','dn');
      if(i < n) el.classList.add('dn');
      if(i === n) el.classList.add('on');
    });
  }

  /* ── Tiếp theo ── */
  window.bkNext = function(){
    if(!validateStep(curTab)) return;
    /* Bước 2 → 3: load lịch qua AJAX */
    if(curTab === 1){
      loadCalendar(function(){ showTab(curTab+1); });
    } else if(curTab === TABS.length-1){
      document.getElementById('bk-form').submit();
    } else {
      showTab(curTab+1);
    }
  };

  /* ── Quay lại ── */
  window.bkPrev = function(){
    if(curTab > 0) showTab(curTab-1);
  };

  /* ── Validate từng bước ── */
  function validateStep(step){
    hideAllErrors();
    if(step===0){
      var checked = document.querySelectorAll('#tab_services input[type=checkbox]:checked');
      if(checked.length===0){ showErr('err1'); return false; }
    }
    if(step===1){
      var emp = document.querySelector('#tab_employees input[type=radio]:checked');
      if(!emp){ showErr('err2'); return false; }
    }
    if(step===2){
      var slot = document.querySelector('#bk-cal-wrap input[type=radio]:checked');
      if(!slot){ showErr('err3'); return false; }
    }
    if(step===3){
      var ok = true;
      function chk(id,feid,test){
        var el=document.getElementById(id), fe=document.getElementById(feid);
        if(!el) return;
        if(!test(el.value.trim())){
          el.classList.add('err');
          if(fe) fe.style.display='block';
          ok=false;
        } else {
          el.classList.remove('err');
          if(fe) fe.style.display='none';
        }
      }
      chk('inp_fname','fe1',function(v){return v!=='';});
      chk('inp_lname','fe2',function(v){return v!=='';});
      chk('inp_email','fe3',function(v){return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);});
      chk('inp_phone','fe4',function(v){return /^\d{10}$/.test(v);});
      return ok;
    }
    return true;
  }

  function showErr(id){
    var el=document.getElementById(id);
    if(el){ el.style.display='flex'; el.scrollIntoView({behavior:'smooth',block:'center'}); }
  }
  function hideAllErrors(){
    ['err1','err2','err3'].forEach(function(id){
      var el=document.getElementById(id);
      if(el) el.style.display='none';
    });
  }

  /* ── Load lịch AJAX ── */
  function loadCalendar(cb){
    var svcs = [];
    document.querySelectorAll("input[name='selected_services[]']:checked").forEach(function(el){ svcs.push(el.value); });
    var emp  = document.querySelector("input[name='selected_employee']:checked");
    if(!emp || svcs.length===0){ showTab(curTab+1); if(cb) cb(); return; }

    var wrap = document.getElementById('bk-cal-wrap');
    var loading = document.getElementById('bk-cal-loading');
    if(loading) loading.style.display='flex';
    /* Xóa nội dung cũ trừ loading */
    var oldCal = wrap.querySelector('.bk-cal');
    if(oldCal) oldCal.remove();

    var xhr = new XMLHttpRequest();
    xhr.open('POST', '<?=base_url('index.php?url=calendar')?>', true);
    xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    xhr.onload = function(){
      if(loading) loading.style.display='none';
      if(xhr.status===200){
        var tmp = document.createElement('div');
        tmp.innerHTML = xhr.responseText;
        wrap.appendChild(tmp.firstElementChild || tmp);
      } else {
        wrap.innerHTML = '<p style="color:#dc2626;padding:16px">Không thể tải lịch. Vui lòng thử lại.</p>';
      }
      if(cb) cb();
    };
    xhr.onerror = function(){
      if(loading) loading.style.display='none';
      wrap.innerHTML = '<p style="color:#dc2626;padding:16px">Lỗi kết nối. Vui lòng thử lại.</p>';
      if(cb) cb();
    };

    var params = 'selected_employee='+encodeURIComponent(emp.value);
    svcs.forEach(function(s){ params += '&selected_services[]='+encodeURIComponent(s); });
    xhr.send(params);
  }

  /* ── Accordion danh mục ── */
  window.bkCat = function(btn){
    var b=btn.nextElementSibling, a=btn.querySelector('.bk-ah-ar'), o=b.classList.contains('op');
    b.classList.toggle('op',!o);
    a.classList.toggle('op',!o);
  };

  /* ── Chọn dịch vụ ── */
  window.bkSvc = function(row){
    var cb=row.querySelector('.bk-hcb');
    cb.checked=!cb.checked;
    row.classList.toggle('sel',cb.checked);
  };

  /* ── Chọn nhân viên ── */
  window.bkStaff = function(card){
    document.querySelectorAll('#bk-app .bk-sc').forEach(function(c){ c.classList.remove('sel'); });
    card.classList.add('sel');
    card.querySelector('.bk-hr').checked=true;
  };

})();
</script>
