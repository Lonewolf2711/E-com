(function($) {

  "use strict";

  var initPreloader = function() {
    $(document).ready(function($) {
      var Body = $('body');
      Body.addClass('preloader-site');
    });
    $(window).on('load', function() {
        $('.preloader-wrapper').fadeOut();
        $('body').removeClass('preloader-site');
    });
  }

  var initSwiper = function() {

    if (document.querySelector('.main-swiper')) {
      var swiper = new Swiper(".main-swiper", {
        speed: 500,
        pagination: {
          el: ".swiper-pagination",
          clickable: true,
        },
      });
    }

    if (document.querySelector('.category-carousel')) {
      var category_swiper = new Swiper(".category-carousel", {
        slidesPerView: 6,
        spaceBetween: 30,
        speed: 500,
        navigation: {
          nextEl: ".category-carousel-next",
          prevEl: ".category-carousel-prev",
        },
        breakpoints: {
          0: {
            slidesPerView: 2,
          },
          768: {
            slidesPerView: 3,
          },
          991: {
            slidesPerView: 4,
          },
          1500: {
            slidesPerView: 6,
          },
        }
      });
    }

    if (document.querySelector('.brand-carousel')) {
      var brand_swiper = new Swiper(".brand-carousel", {
        slidesPerView: 4,
        spaceBetween: 30,
        speed: 500,
        navigation: {
          nextEl: ".brand-carousel-next",
          prevEl: ".brand-carousel-prev",
        },
        breakpoints: {
          0: {
            slidesPerView: 2,
          },
          768: {
            slidesPerView: 2,
          },
          991: {
            slidesPerView: 3,
          },
          1500: {
            slidesPerView: 4,
          },
        }
      });
    }

    if (document.querySelector('.products-carousel')) {
      var products_swiper = new Swiper(".products-carousel", {
        slidesPerView: 5,
        spaceBetween: 30,
        speed: 500,
        navigation: {
          nextEl: ".products-carousel-next",
          prevEl: ".products-carousel-prev",
        },
        breakpoints: {
          0: {
            slidesPerView: 1,
          },
          768: {
            slidesPerView: 3,
          },
          991: {
            slidesPerView: 4,
          },
          1500: {
            slidesPerView: 6,
          },
        }
      });
    }
  }

  var initProductQty = function(){

    $('.product-qty').each(function(){

      var $el_product = $(this);
      var quantity = 0;

      $el_product.find('.quantity-right-plus').click(function(e){
          e.preventDefault();
          var quantity = parseInt($el_product.find('#quantity, #qty').val());
          $el_product.find('#quantity, #qty').val(quantity + 1);
      });

      $el_product.find('.quantity-left-minus').click(function(e){
          e.preventDefault();
          var quantity = parseInt($el_product.find('#quantity, #qty').val());
          if(quantity>1){
            $el_product.find('#quantity, #qty').val(quantity - 1);
          }
      });

    });

  }

  // document ready
  $(document).ready(function() {
    
    initPreloader();
    initSwiper();
    initProductQty();

    // Safe init for optional libraries
    if (typeof jarallax !== 'undefined') {
      jarallax(document.querySelectorAll(".jarallax"));
      jarallax(document.querySelectorAll(".jarallax-keep-img"), {
        keepImg: true,
      });
    }

    if (typeof Chocolat !== 'undefined' && document.querySelectorAll('.image-link').length > 0) {
      Chocolat(document.querySelectorAll('.image-link'), {
        imageSize: 'contain',
        loop: true,
      });
    }

  }); // End of a document

})(jQuery);