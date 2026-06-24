/**
 * web-main.js — JavaScript cho trang khách hàng (frontend)
 * Barbershop Website
 * Phụ thuộc: jQuery, Bootstrap 4
 */

$(document).ready(function () {

    /* =============================================
       NAVBAR — Sticky khi cuộn
       ============================================= */
    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 80) {
            $('.header-section').addClass('sticky');
        } else {
            $('.header-section').removeClass('sticky');
        }
    });

    /* =============================================
       MOBILE MENU — Mở / Đóng
       ============================================= */
    $('.mob-menu-toggle').on('click', function () {
        $('#menu_mobile').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('.mob-close-toggle').on('click', function () {
        $('#menu_mobile').removeClass('open');
        $('body').css('overflow', '');
    });

    // Đóng menu khi bấm link
    $('#menu_mobile a.a-mob-menu').on('click', function () {
        $('#menu_mobile').removeClass('open');
        $('body').css('overflow', '');
    });

    /* =============================================
       SMOOTH SCROLL cho anchor links
       ============================================= */
    $('a[href^="#"]').on('click', function (e) {
        var target = $(this.hash);
        if (target.length) {
            e.preventDefault();
            var offset = target.offset().top - 75;
            $('html, body').animate({ scrollTop: offset }, 600);
        }
    });

    /* =============================================
       TOOLTIP Bootstrap
       ============================================= */
    if ($.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }

    /* =============================================
       CAROUSEL Bootstrap — khởi động
       ============================================= */
    if ($.fn.carousel) {
        $('#home-section-carousel').carousel({ interval: 5000 });
        $('#reviews-carousel').carousel({ interval: 6000 });
    }

    /* =============================================
       CONTACT FORM — Validate & Gửi qua AJAX
       ============================================= */
    $('#contact_send').on('click', function (e) {
        e.preventDefault();

        var name    = $.trim($('#contact_name').val());
        var email   = $.trim($('#contact_email').val());
        var subject = $.trim($('#contact_subject').val());
        var message = $.trim($('#contact_message').val());
        var valid   = true;

        // Reset trạng thái lỗi
        $('#contact_ajax_form .form-control').removeClass('is-invalid');
        $('#contact_ajax_form .invalid-feedback').hide();

        if (!name) {
            $('#contact_name').addClass('is-invalid');
            $('#contact_name').next('.invalid-feedback').show();
            valid = false;
        }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#contact_email').addClass('is-invalid');
            $('#contact_email').next('.invalid-feedback').show();
            valid = false;
        }
        if (!subject) {
            $('#contact_subject').addClass('is-invalid');
            $('#contact_subject').next('.invalid-feedback').show();
            valid = false;
        }
        if (!message) {
            $('#contact_message').addClass('is-invalid');
            $('#contact_message').next('.invalid-feedback').show();
            valid = false;
        }

        if (!valid) return;

        // Hiện loader
        $('#contact_ajax_loader').show();
        $('#contact_send').prop('disabled', true);

        $.ajax({
            url: 'index.php?url=contact',
            method: 'POST',
            data: { name: name, email: email, subject: subject, message: message },
            success: function (response) {
                $('#contact_status_message').html(
                    '<div style="padding:12px;background:#d4edda;color:#155724;border-radius:6px;font-size:14px;margin-top:10px;">' +
                    '<i class="fas fa-check-circle"></i> Tin nhắn đã được gửi thành công!' +
                    '</div>'
                );
                // Reset form
                $('#contact_ajax_form .form-control').val('');
            },
            error: function () {
                $('#contact_status_message').html(
                    '<div style="padding:12px;background:#f8d7da;color:#721c24;border-radius:6px;font-size:14px;margin-top:10px;">' +
                    '<i class="fas fa-exclamation-circle"></i> Có lỗi xảy ra. Vui lòng thử lại.' +
                    '</div>'
                );
            },
            complete: function () {
                $('#contact_ajax_loader').hide();
                $('#contact_send').prop('disabled', false);
            }
        });
    });

    /* =============================================
       STEP FORM ĐẶT LỊCH — được xử lý hoàn toàn
       trong appointment/index.php (self-contained)
       Không override bất cứ thứ gì ở đây
       ============================================= */

});
