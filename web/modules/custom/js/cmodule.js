function applyWhenElementExists(selector, myFunction, intervalTime) {
  var interval = setInterval(function() {
    if (jQuery(selector).length > 0) {
      myFunction();
      clearInterval(interval);
    }
  }, intervalTime);
}
jQuery(document).ready(function($) {

  applyWhenElementExists('.view-display-id-block_2 .views-field-field-mmg-review', function () {
    if($('.view-display-id-block_2').length) {
      var viewmore = "<p class='d-inline'><span class='continue-btn btn btn-success act'>View more</span></p>";
      var showChar = 80;
      var ellipses = "<span class='ellip'>...</span>";
      $('.view-display-id-block_2 .views-row .views-field-field-mmg-review .field-content').each(function(){
        if($(this).text().length > showChar){
          var first_half  = $(this).text().slice(0,80);
          var second_half = $(this).text().slice(80,$(this).text().length);
          $(this).html("<div class='text-wrapper'></div>");
          $(this).find('.text-wrapper').append("<div class='first d-inline'>"+first_half+ellipses+"</div>");
          $(this).find('.text-wrapper').append("<div class='second d-none'>"+second_half+"</div>");
          $(this).find('.text-wrapper').append(viewmore);
        }
      })
      $('.act').each(function(){ 
        $(this).click(function(){
          // Toggle the second half on or off
          $(this).parent().parent().find('.ellip').toggle();
          // Change the button text
          if($(this).text() == "View more"){
            $(this).parent().parent().find('.second').removeClass('d-none');
            $(this).parent().parent().find('.second').addClass('d-inline');
            $(this).text("Show Less")
          }else{
            $(this).parent().parent().find('.second').addClass('d-none');
            $(this).parent().parent().find('.second').removeClass('d-inline');
            $(this).text("View more");
          }
        })
      });
    }
  }, 50);

  if($('.fill-value').length) {
    $('.fill-value').each(function(){
      $(this).css('width',$(this).data("width"));
    });
  }
  var urlParams = new URLSearchParams(window.location.search);
  if(urlParams.has('rate')) {
    $('html, body').animate({
      scrollTop: $(".view-id-customer_reviews").offset().top - 150
    }, 1000);
  }

  $('.paragraph.paragraph--type--title-text').addClass('d-none hide');
  $('.field--name-field-video-url').addClass('d-none hide');
  $('.paragraph--type--specification .field--name-field-title h2').on('click',function(){
    $('.paragraph.paragraph--type--title-text').toggleClass('d-none hide');
  })
  $('.paragraph--type--videos .field--name-field-title h2').on('click',function(){
    $('.field--name-field-video-url').toggleClass('d-none hide');
  })
});