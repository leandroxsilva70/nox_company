$(document).ready(function(){
    // Inicializa o carrossel com configurações melhoradas
    $('.features-carousel').slick({
        dots: false, // Remove os pontos de navegação
        arrows: false, // Remove as setas de navegação
        infinite: true,
        speed: 300,
        slidesToShow: 1,
        centerMode: true,
        variableWidth: true,
        autoplay: true,
        autoplaySpeed: 5000,
        draggable: true, // Permite arrastar com o mouse
        swipeToSlide: true,
        touchThreshold: 10,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1,
                    centerMode: false,
                    variableWidth: false
                }
            }
        ]
    });
    
    // Adiciona feedback visual ao arrastar
    let isDragging = false;
    
    $('.features-carousel').on('beforeChange', function(){
        isDragging = true;
    });
    
    $('.features-carousel').on('afterChange', function(){
        isDragging = false;
    });
    
    $('.features-carousel').on('swipe', function(){
        $('.feature-slide').css('transform', 'scale(0.95)');
    });
    
    $('.features-carousel').on('swiped', function(){
        $('.feature-slide').css('transform', '');
    });
    
    // Pausa autoplay quando o mouse está sobre o carrossel
    $('.features-carousel').hover(
        function() {
            $(this).slick('slickPause');
        },
        function() {
            $(this).slick('slickPlay');
        }
    );
});