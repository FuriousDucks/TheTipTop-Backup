$('.gifts').slick({
    infinite: true,
    speed: 300,
    autoplay: true,
    arrows: false,
    slidesToShow: 4,
    slidesToScroll: 2,
    responsive: [{
            breakpoint: 1366,
            settings: {
                slidesToShow: 3,
            }
        }, {
            breakpoint: 1024,
            settings: {
                slidesToShow: 2,
            }
        },
        {
            breakpoint: 768,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
    ]
});