$('.gifts').slick({
    dots: false,
    infinite: true,
    arrows: false,
    speed: 300,
    centerMode: true,
    centerPadding: '60px',
    slidesToShow: 3,
    slidesToScroll: 3,
    responsive: [{
            breakpoint: 768,
            settings: {
                arrows: false,
                centerMode: true,
                centerPadding: '40px',
                slidesToShow: 3
            }
        },
        {
            breakpoint: 480,
            settings: {
                arrows: false,
                centerMode: true,
                centerPadding: '40px',
                slidesToShow: 1
            }
        }
    ]
});