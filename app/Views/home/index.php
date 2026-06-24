<!-- KHU VỰC TRANG CHỦ -->

    <section class="home-section" id="home-section">
	    <div class="home-section-content">
		    <div id="home-section-carousel" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#home-section-carousel" data-slide-to="0" class="active"></li>
                    <li data-target="#home-section-carousel" data-slide-to="1"></li>
                    <li data-target="#home-section-carousel" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                        <!-- FIRST SLIDE -->
                        <div class="carousel-item active">
                            <img class="d-block w-100" src="<?= base_url('barber-admin/Design/images/khanh1.jpg') ?>" alt="First slide">
                            <div class="carousel-caption d-md-block">
                                <h3>Không chỉ là cắt tóc, đó là một trải nghiệm</h3>
                                <p>
                                    Tiệm cắt tóc của chúng tôi là không gian dành riêng cho những người đàn ông biết trân trọng
                                    <br>
                                    chất lượng cao cấp, thời gian và một vẻ ngoài hoàn hảo.
                                </p>
                            </div>
                        </div>
                        <!-- SECOND SLIDE -->
                        <div class="carousel-item">
                            <img class="d-block w-100" src="<?= base_url('barber-admin/Design/images/q1.jpg') ?>" alt="Second slide">
                            <div class="carousel-caption d-md-block">
                                <h3>Tay nghề chuyên nghiệp, tận tâm từng đường kéo</h3>
                                <p>
                                    Đội ngũ thợ lành nghề của chúng tôi luôn lắng nghe và tư vấn kiểu tóc
                                    <br>
                                    phù hợp nhất với khuôn mặt và phong cách của bạn.
                                </p>
                            </div>
                        </div>
                        <!-- THIRD SLIDE -->
                        <div class="carousel-item">
                            <img class="d-block w-100" src="<?= base_url('barber-admin/Design/images/q3.jpg') ?>" alt="Third slide">
                            <div class="carousel-caption d-md-block">
                                <h3>Đặt lịch dễ dàng — đến đúng giờ là được phục vụ ngay</h3>
                                <p>
                                    Chúng tôi trân trọng thời gian của bạn. Hệ thống đặt lịch online giúp bạn
                                    <br>
                                    chủ động lịch trình mà không cần chờ đợi.
                                </p>
                            </div>
                        </div>
                </div>
                <!-- PREVIOUS & NEXT -->
                <a class="carousel-control-prev" href="#home-section-carousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Quay lại</span>
                </a>
                <a class="carousel-control-next" href="#home-section-carousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Tiếp tục </span>
                </a>
            </div>
		</div>
	</section>

    <!-- KHU VỰC GIỚI THIỆU -->

    <section id="about" class="about_section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="about_content" style="text-align: center;">
                        <h3>Giới thiệu</h3>
                        <h2>Barber Shop <br>Từ năm 2026</h2>
                        <img src="<?= base_url('barber-admin/Design/images/about-logo.png') ?>" alt="logo">
                        <p style="color: #777">
                            Chúng tôi mang đến không gian chăm sóc tóc và râu chuyên nghiệp dành cho nam giới. Mỗi dịch vụ được thực hiện chỉn chu bởi đội ngũ thợ lành nghề, giúp bạn luôn tự tin với diện mạo gọn gàng và lịch lãm.
                        </p>
                        <a href="#" class="default_btn" style="opacity: 1;">Xem thêm về chúng tôi</a>
                    </div>
                </div>
                <div class="col-md-6  d-none d-md-block">
                    <div class="about_img" style = "overflow:hidden">
                        <img class="about_img_1" src="<?= base_url('barber-admin/Design/images/about-1.jpg') ?>" alt="about-1">
                        <img class="about_img_2" src="<?= base_url('barber-admin/Design/images/about-2.jpg') ?>" alt="about-2">
                        <img class="about_img_3" src="<?= base_url('barber-admin/Design/images/about-3.jpg') ?>" alt="about-3">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KHU VỰC DỊCH VỤ -->

    <section class="services_section" id="services">
        <div class="container">
            <div class="section_heading">
                
                <h2>Dịch vụ của chúng tôi</h2>
                <div class="heading-line"></div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-6 padd_col_res">
                    <div class="service_box">
                        <i class="bs bs-scissors-1"></i>
                        <h3>Cắt tóc tạo kiểu</h3>
                        <p>Tư vấn và tạo kiểu phù hợp khuôn mặt, phong cách và công việc.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 padd_col_res">
                    <div class="service_box">
                        <i class="bs bs-razor-2"></i>
                        <h3>Tỉa râu</h3>
                        <p>Tạo dáng râu gọn gàng, sắc nét và hài hòa với tổng thể khuôn mặt.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 padd_col_res" >
                    <div class="service_box">
                        <i class="bs bs-brush"></i>
                        <h3>Cạo râu thư giãn</h3>
                        <p>Quy trình cạo râu êm ái, sạch sẽ, mang lại cảm giác dễ chịu.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 padd_col_res">
                    <div class="service_box">
                        <i class="bs bs-hairbrush-1"></i>
                        <h3>Chăm sóc da mặt</h3>
                        <p>Làm sạch và dưỡng da cơ bản, giúp da khỏe và sáng hơn.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KHU VỰC ĐẶT LỊCH -->

    <section class="book_section" id="booking">
        <div class="book_bg"></div>
        <div class="map_pattern"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <form action="<?= base_url('index.php?url=appointment') ?>" method="post" id="appointment_form" class="form-horizontal appointment_form">
                        <div class="book_content">
                            <h2 style="color: white;">Đặt lịch hẹn</h2>
                            <p style="color: #999;">
                                Đặt lịch nhanh để được phục vụ đúng giờ và chọn thợ theo mong muốn.
                            </p>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6 padding-10">  
                                <input type="date" class="form-control">
                            </div>
                            <div class="col-md-6 padding-10">
                                <input type="time" class="form-control">
                            </div>
                        </div>

                        <!-- SUBMIT BUTTON -->

                        <button id="app_submit" class="default_btn" type="submit">
                            Đặt lịch ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- KHU VỰC THƯ VIỆN ẢNH -->

    <section class="gallery-section" id="gallery">
        <div class="section_heading">
          
            <h2>Thư viện ảnh</h2>
            <div class="heading-line"></div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 gallery-column">
                    <div style="height: 230px">
                        <div class="gallery-img" style="background-image: url('<?= base_url('barber-admin/Design/images/portfolio-1.jpg') ?>');">    </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 gallery-column">
                    <div style="height: 230px">
                        <div class="gallery-img" style="background-image: url('<?= base_url('barber-admin/Design/images/portfolio-2.jpg') ?>');"></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 gallery-column">
                    <div style="height: 230px">
                        <div class="gallery-img" style="background-image: url('<?= base_url('barber-admin/Design/images/portfolio-3.jpg') ?>');"></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 gallery-column">
                    <div style="height: 230px">
                        <div class="gallery-img" style="background-image: url('<?= base_url('barber-admin/Design/images/portfolio-4.jpg') ?>');"></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 gallery-column">
                    <div style="height: 230px">
                        <div class="gallery-img" style="background-image: url('<?= base_url('barber-admin/Design/images/portfolio-5.jpg') ?>');"></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 gallery-column">
                    <div style="height: 230px">
                        <div class="gallery-img" style="background-image: url('<?= base_url('barber-admin/Design/images/portfolio-6.jpg') ?>');"></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 gallery-column">
                    <div style="height: 230px">
                        <div class="gallery-img" style="background-image: url('<?= base_url('barber-admin/Design/images/portfolio-7.jpg') ?>');"></div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 gallery-column">
                    <div style="height: 230px">
                        <div class="gallery-img" style="background-image: url('<?= base_url('barber-admin/Design/images/portfolio-8.jpg') ?>');"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KHU VỰC ĐỘI NGŨ -->

    <section id="team" class="team_section">
        <div class="container">
            <div class="section_heading ">
               
                <h2>Đội ngũ thợ</h2>
                <div class="heading-line"></div>
            </div>
            <ul class="team_members row"> 
                <li class="col-lg-3 col-md-6 padd_col_res">
                    <div class="team_member">
                        <img src="<?= base_url('barber-admin/Design/images/anh2.jpg') ?>" alt="Team Member">
                    </div>
                </li>
                <li class="col-lg-3 col-md-6 padd_col_res">
                    <div class="team_member">
                        <img src="<?= base_url('barber-admin/Design/images/team-2.jpg') ?>" alt="Team Member">
                    </div>
                </li>
                <li class="col-lg-3 col-md-6 padd_col_res">
                    <div class="team_member">
                        <img src="<?= base_url('barber-admin/Design/images/team-3.jpg') ?>" alt="Team Member">
                    </div>
                </li>
                <li class="col-lg-3 col-md-6 padd_col_res">
                    <div class="team_member">
                        <img src="<?= base_url('barber-admin/Design/images/team-4.jpg') ?>" alt="Team Member">
                    </div>
                </li>
            </ul>
        </div>
    </section>

    <!-- KHU VỰC ĐÁNH GIÁ -->
<!-- KHU VỰC ĐÁNH GIÁ -->

    <section id="reviews" class="testimonial_section">
        <div class="container">
            <div id="reviews-carousel" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <li data-target="#reviews-carousel" data-slide-to="0" class="active"></li>
                    <li data-target="#reviews-carousel" data-slide-to="1"></li>
                    <li data-target="#reviews-carousel" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                    <!-- REVIEW 1 -->
                    <div class="carousel-item active">
                        <img class="d-block w-100" src="<?= base_url('barber-admin/Design/images/barbershop_image_1.jpg') ?>" alt="First slide" style="visibility: hidden;">
                        <div class="carousel-caption d-md-block">
                            <h3>"Không gian tuyệt vời, thợ cắt rất có tâm"</h3>
                            <p>
                                Tôi rất ấn tượng với phong cách làm việc chuyên nghiệp ở đây. 
                                <br>
                                Đường kéo sắc nét, tư vấn kiểu tóc kỹ càng và không phải chờ đợi lâu khi đặt lịch trước.
                                <br>
                                <strong style="color: #ffc107;">- Anh Hoàng Minh (Đống Đa) -</strong>
                            </p>
                        </div>
                    </div>
                    <!-- REVIEW 2 -->
                    <div class="carousel-item">
                        <img class="d-block w-100" src="<?= base_url('barber-admin/Design/images/barbershop_image_1.jpg') ?>" alt="Second slide" style="visibility: hidden;">
                        <div class="carousel-caption d-md-block">
                            <h3>"Trải nghiệm cạo râu thư giãn chuẩn barber"</h3>
                            <p>
                                Không chỉ cắt tóc đẹp mà combo cạo râu, chăm sóc da mặt ở đây cực kỳ dễ chịu. 
                                <br>
                                Tiệm thơm mùi tinh dầu, nhạc nhẹ nhàng, xứng đáng là nơi để đàn ông lui tới mỗi tuần.
                                <br>
                                <strong style="color: #ffc107;">- Anh Tiến Dũng (Hoàn Kiếm) -</strong>
                            </p>
                        </div>
                    </div>
                    <!-- REVIEW 3 -->
                    <div class="carousel-item">
                        <img class="d-block w-100" src="<?= base_url('barber-admin/Design/images/barbershop_image_1.jpg') ?>" alt="Third slide" style="visibility: hidden;">
                        <div class="carousel-caption d-md-block">
                            <h3>"Chất lượng dịch vụ hoàn hảo, đúng giờ"</h3>
                            <p>
                                Điểm cộng lớn nhất là tiệm làm việc rất tôn trọng thời gian của khách. 
                                <br>
                                Đặt lịch giờ nào là được vào làm ngay giờ đó. Nhân viên thân thiện, tay nghề đồng đều.
                                <br>
                                <strong style="color: #ffc107;">- Anh Quốc Khánh (Thanh Xuân) -</strong>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- PREVIOUS & NEXT -->
                <a class="carousel-control-prev" href="#reviews-carousel" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Quay lại</span>
                </a>
                <a class="carousel-control-next" href="#reviews-carousel" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Tiếp theo</span>
                </a>
            </div>
        </div>
    </section>
    <!-- KHU VỰC BẢNG GIÁ -->

    <section class="pricing_section" id="pricing">
        <div class="container">
            <div class="section_heading">
               
                <h2>Bảng giá dịch vụ</h2>
                <div class="heading-line"></div>
            </div>
            <div class="row">
                <?php foreach ($categories as $category): ?>
                    <div class="col-lg-4 col-md-6 sm-padding" style="margin-bottom:24px;">
                        <div class="price_wrap" style="background:#fff;border-radius:14px;border:1px solid #d4ecf8;padding:24px;box-shadow:0 4px 20px rgba(26,155,215,.1);height:100%;">
                            <h3><?= htmlspecialchars($category['ten_danh_muc']) ?></h3>
                            <ul class="price_list">
                                <?php foreach ($category['services'] as $service): ?>
                                    <li>
                                        <h4><?= htmlspecialchars($service['ten_dich_vu']) ?></h4>
                                        <p><?= htmlspecialchars($service['mo_ta']) ?></p>
                                        <span class="price"><?= format_money($service['gia']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- KHU VỰC LIÊN HỆ -->

    <section class="contact-section" id="contact-us">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 sm-padding">
                    <div class="contact-info">
                        <h2>
                            Kết nối với chúng tôi
                            <br>và gửi tin nhắn ngay hôm nay!
                        </h2>
                        <p>
                            Nếu bạn cần tư vấn kiểu tóc, báo giá hoặc hỗ trợ đặt lịch nhanh, vui lòng để lại thông tin. Đội ngũ của chúng tôi sẽ liên hệ sớm nhất.
                        </p>
                        <h3>
                            
                            <br>
                            
                        </h3>
                     
                    </div>
                </div>
                <div class="col-lg-6 sm-padding">
                    <div class="contact-form">
                        <div id="contact_ajax_form" class="contactForm">
                            <div class="form-group colum-row row">
                                <div class="col-sm-6">
                                    <input type="text" id="contact_name" name="name" class="form-control" placeholder="Họ tên" required>
                                    <div class="invalid-feedback">Vui lòng nhập họ tên.</div>
                                </div>
                                <div class="col-sm-6">
                                    <input type="email" id="contact_email" name="email" class="form-control" placeholder="Email" required>
                                    <div class="invalid-feedback">Email không hợp lệ.</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <input type="text" id="contact_subject" name="subject" class="form-control" placeholder="Chủ đề" required>
                                    <div class="invalid-feedback">Vui lòng nhập chủ đề.</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <textarea id="contact_message" name="message" cols="30" rows="5" class="form-control message" placeholder="Nội dung" required></textarea>
                                    <div class="invalid-feedback">Vui lòng nhập nội dung.</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-12">
                                    <button id="contact_send" class="default_btn">Gửi tin nhắn</button>
                                </div>
                            </div>
                            <img src="<?= base_url('barber-admin/Design/images/ajax_loader_gif.gif') ?>" id="contact_ajax_loader" style="display: none">
                            <div id="contact_status_message"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- KHU VỰC WIDGET / CHÂN TRANG -->

    <section class="widget_section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="footer_widget">
                        <img src="<?= base_url('barber-admin/Design/images/LA_logo.png') ?>" alt="Brand" style="height:52px;width:auto;object-fit:contain;margin-bottom:12px;display:block;">
                        <p>
                            Chúng tôi xây dựng không gian barber hiện đại cho khách hàng yêu thích chất lượng, đúng giờ và phong cách chỉn chu.
                        </p>
                        <ul class="widget_social">
                            <li><a href="#" data-toggle="tooltip" title="Facebook"><i class="fab fa-facebook-f fa-2x"></i></a></li>
                           
                            <li><a href="#" data-toggle="tooltip" title="Instagram"><i class="fab fa-instagram fa-2x"></i></a></li>
        
                            <li><a href="#" data-toggle="tooltip" title="Google+"><i class="fab fa-google-plus-g fa-2x"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                     <div class="footer_widget">
                        <h3>Địa chỉ tiệm</h3>
                        <p>
                            175 Tây Sơn , Đống Đa , Hà Nội
                        </p>
                        <p>
                            duongngockhanh56@gmail.com
                            <br>
                            0396348994    
                        </p>
                     </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer_widget">
                        <h3>
                            Giờ mở cửa
                        </h3>
                        <ul class="opening_time">
                            <li>Thứ 2 - Thứ 6: 8:00 - 20:00</li>
                           
                            <li>thứ 7 - Chủ nhật: 8:00 - 19:00</li>
                            <li>Lễ/Tết: theo thông báo</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer_widget">
                        
                        <div class="subscribe_form">
                            <form action="#" class="subscribe_form" novalidate="true">
                                <input type="email" name="EMAIL" id="subs-email" class="form_input" placeholder="Nhập địa chỉ email...">
                                <button type="submit" class="submit">ĐĂNG KÝ</button>
                                <div class="clearfix"></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CHÂN TRANG -->

    
