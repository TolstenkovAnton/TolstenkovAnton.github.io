function main()
{
    $(".multiple-items").slick(
    {
        dots: true,
        infinite: false,
        slidesToShow: 3,
        slidesToScroll: 3,
        responsive:
        [
        {
            breakpoint: 1200,
            settings:
            {
                slidesToShow: 2,
                slidesToScroll: 2
            }
        },
        {
            breakpoint: 900,
            settings:
            {
                slidesToShow: 1,
                slidesToScroll: 1
            }
        }
        ]
    }
    );
}
  
$(document).ready(main)