/**
 * Javascript général
 */

jQuery(function() {
  jQuery(".kl-abonnement").click(function(){
    jQuery(".menu-item-4798").trigger('click');
  });
  /*
  //jQuery("body.xoo-el-popup-active").css('overflow-y', 'inherit');
  //jQuery("#xoo-el-container").hide();
  //jQuery("body").removeClass('xoo-el-popup-active');
  jQuery(".menu-item-4798, .kl-abonnement").click(function(){
    //jQuery("#xoo-el-container").show();
    popup=new Popup($('.xoo-el-container'));
    popup.toggle('show');
  });
  $("body").delegate(".kl-abonnement", "click", function() {
    //alert('testdelegate');
    jQuery("#xoo-el-container").show();
  });
  */

  jQuery('#login-submit').click(function (e) {
    e.preventDefault();
    var email = jQuery('#user_email').val();
    var password = jQuery('#user_password').val();
    var data_send = JSON.stringify({email: email, password: password});
    jQuery.ajax({
      url: url_login,
      type: 'POST',
      contentType: "application/json",
      dataType: 'json',
      data: data_send,
      success: function (data, status) {
        jQuery('#gn-loader').hide();
        if (data.user > 0)
        {
          window.location.reload();
        }
      },
      beforeSend: function(){
        jQuery('#gn-loader').show();
      },
      error: function(data, status, object) {
        jQuery("#content-msg-outer").show();
        jQuery('#content-msg').text('Identifiants invalides');
        jQuery('#gn-loader').hide();
      }
    });
  });
});
