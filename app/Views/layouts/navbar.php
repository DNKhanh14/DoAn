
    
    <!-- BẮT ĐẦU THANH ĐIỀU HƯỚNG -->

    <header id="header" class="header-section">
        <div class="container">
            <nav class="navbar" style="display:flex;align-items:center;justify-content:space-between;width:100%;padding:0;">

                <!-- LOGO -->
                <a href="./" class="navbar-brand" style="flex-shrink:0;line-height:0;">
                    <img src="<?= base_url('barber-admin/Design/images/LA_logo.png') ?>" alt="Logo" style="height:54px;width:auto;object-fit:contain;">
                </a>

                <!-- MENU GIỮA -->
                <div class="mainmenu" id="mainmenu" style="flex:1;display:flex;justify-content:center;">
                    <ul class="nav" style="display:flex;align-items:center;margin:0;padding:0;">
                        <li><a href="./#home-section">Trang chủ</a></li>
                        <li><a href="./#about">Giới thiệu</a></li>
                        <li><a href="./#services">Dịch vụ</a></li>
                        <li><a href="./#gallery">Thư viện ảnh</a></li>
                        <li><a href="./#pricing">Bảng giá</a></li>
                        <li><a href="./#contact-us">Liên hệ</a></li>
                    </ul>
                </div>

                <!-- NÚT ĐẶT LỊCH -->
                <div class="header-btn" style="flex-shrink:0;">
                    <a href="<?= base_url('index.php?url=appointment') ?>" class="menu-btn">Đặt lịch ngay</a>
                </div>

                <!-- TOGGLE MOBILE -->
                <a class="mob-menu-toggle" style="flex-shrink:0;">
                    <i class="fa fa-bars"></i>
                </a>

            </nav>
        </div>
    </header>

    <div class="header-height" style="height:80px;"></div>

    <!-- KẾT THÚC THANH ĐIỀU HƯỚNG -->

    <!-- BẮT ĐẦU THANH ĐIỀU HƯỚNG MOBILE -->
    
    <div id="menu_mobile" class="menu-mobile-menu-container">
        <ul class="mob-menu-top">
            <li class="menu-header">
                <a href="#">TRÌNH ĐƠN</a>
            </li>
            <li style="display: inline-block;">
                <a class="mob-close-toggle" style="cursor: pointer;width: 75px;">
                    <i class="fas fa-times" style="color: white;"></i>
                </a>
            </li>
        </ul>
        <div class="menu-tab-div">
            <ul id="mobile-menu" class="menu">
                <li>
                    <a href="index.php#home-section" class="a-mob-menu">
                        Trang chủ
                    </a>
                </li>
                <li>
                    <a href="index.php#about" class="a-mob-menu">
                        Giới thiệu
                    </a>
                </li>
                <li>
                    <a href="index.php#services" class="a-mob-menu">
                        Dịch vụ
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('index.php?url=appointment') ?>" class="a-mob-menu">
                        Đặt lịch ngay
                    </a>
                </li>
                <li>
                    <a href="index.php#gallery" class="a-mob-menu">
                        Thư viện ảnh
                    </a>
                </li>
                <li>
                    <a href="index.php#pricing" class="a-mob-menu">
                        Bảng giá
                    </a>
                </li>
                <li>
                    <a href="index.php#contact-us" class="a-mob-menu">
                        Liên hệ
                    </a>
                </li>

            </ul>
        </div>
    </div>

    <!-- KẾT THÚC THANH ĐIỀU HƯỚNG MOBILE -->